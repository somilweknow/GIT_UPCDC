<?php
include("scripts/settings.php");

$is_sadmin = (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'sadmin');

if (!isset($_SESSION['usersno']) || empty($_SESSION['usersno'])) {
    header('Location: index.php');
    exit;
}

$current_user_sno  = intval($_SESSION['usersno']);
$session_districts = isset($_SESSION['district_id']) && is_array($_SESSION['district_id'])
    ? array_map('intval', $_SESSION['district_id'])
    : [];

if ($is_sadmin) {
    $all_districts = [];
    $res_all = execute_query("SELECT sno, district_name FROM master_district ORDER BY district_name");
    while ($row = mysqli_fetch_assoc($res_all)) $all_districts[] = $row;
    if (isset($_GET['selected_district']) && intval($_GET['selected_district']) > 0)
        $user_district_id = intval($_GET['selected_district']);
    elseif (isset($_POST['district_id']) && intval($_POST['district_id']) > 0)
        $user_district_id = intval($_POST['district_id']);
    else
        $user_district_id = !empty($all_districts) ? intval($all_districts[0]['sno']) : 0;
} else {
    $user_district_id = !empty($session_districts) ? $session_districts[0] : 0;
    if ($user_district_id <= 0)
        die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
}

$res_dn             = execute_query("SELECT district_name FROM master_district WHERE sno = $user_district_id");
$user_district_name = (mysqli_num_rows($res_dn) > 0) ? mysqli_fetch_assoc($res_dn)['district_name'] : '';

$today = date('Y-m-d');
$msg   = '';
$success = '';

/*
==========================================================
  SQL TO RUN (run once on your database):
==========================================================

-- Static table: stores cumulative/total liability per employee
CREATE TABLE IF NOT EXISTS sangrah_payment_static (
    sno                         INT AUTO_INCREMENT PRIMARY KEY,
    district_id                 INT NOT NULL,
    mandal_name                 VARCHAR(100) DEFAULT '',
    employee_name               VARCHAR(150) NOT NULL,
    employee_designation        VARCHAR(150) DEFAULT '',
    total_salary                DECIMAL(15,2) DEFAULT 0.00,
    total_pension               DECIMAL(15,2) DEFAULT 0.00,
    total_retirement_dues       DECIMAL(15,2) DEFAULT 0.00,
    updated_at                  DATETIME,
    UNIQUE KEY uq_emp (district_id, employee_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daily table: stores monthly payment + cumulative payment per employee per date
CREATE TABLE IF NOT EXISTS sangrah_payment_daily (
    sno                         INT AUTO_INCREMENT PRIMARY KEY,
    district_id                 INT NOT NULL,
    employee_name               VARCHAR(150) NOT NULL,
    entry_date                  DATE NOT NULL,
    -- माह में भुगतान
    month_salary                DECIMAL(15,2) DEFAULT 0.00,
    month_pension               DECIMAL(15,2) DEFAULT 0.00,
    month_retirement_dues       DECIMAL(15,2) DEFAULT 0.00,
    -- क्रमिक भुगतान
    cum_salary                  DECIMAL(15,2) DEFAULT 0.00,
    cum_pension                 DECIMAL(15,2) DEFAULT 0.00,
    cum_retirement_dues         DECIMAL(15,2) DEFAULT 0.00,
    -- अवशेष देयता की अवधि (माह में)
    balance_period_months       VARCHAR(50)   DEFAULT '',
    UNIQUE KEY uq_emp_date (district_id, employee_name, entry_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- If table already exists and you need to add new columns:
ALTER TABLE sangrah_payment_daily
    ADD COLUMN IF NOT EXISTS month_salary          DECIMAL(15,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS month_pension         DECIMAL(15,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS month_retirement_dues DECIMAL(15,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS cum_salary            DECIMAL(15,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS cum_pension           DECIMAL(15,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS cum_retirement_dues   DECIMAL(15,2) DEFAULT 0.00,
    ADD COLUMN IF NOT EXISTS balance_period_months VARCHAR(50)   DEFAULT '';

ALTER TABLE sangrah_payment_static
    DROP COLUMN IF EXISTS total_retirement_pension;
==========================================================
*/

function load_employee_rows($district_id, $entry_date) {
    $district_id = intval($district_id);
    $entry_date  = mysqli_real_escape_string($GLOBALS['db'], $entry_date);
    $rows = [];
  $res  = execute_query("
    SELECT s.*,
           COALESCE(d.month_salary,          0)  AS month_salary,
           COALESCE(d.month_pension,         0)  AS month_pension,
           COALESCE(d.month_retirement_dues, 0)  AS month_retirement_dues,
           COALESCE(d.cum_salary,            0)  AS cum_salary,
           COALESCE(d.cum_pension,           0)  AS cum_pension,
           COALESCE(d.cum_retirement_dues,   0)  AS cum_retirement_dues,
           COALESCE(d.balance_period_months, '') AS balance_period_months
    FROM sangrah_payment_static s
    LEFT JOIN sangrah_payment_daily d
               ON d.district_id   = s.district_id
              AND d.employee_name = s.employee_name
              AND d.entry_date    = '$entry_date'
        WHERE s.district_id = $district_id
        ORDER BY s.mandal_name, s.employee_name");
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    return $rows;
}

if ($is_sadmin && isset($_GET['ajax_district_data'])) {
    header('Content-Type: application/json; charset=utf-8');
    $sel_dist = intval($_GET['selected_district'] ?? 0);
    echo json_encode($sel_dist > 0 ? load_employee_rows($sel_dist, $today) : [], JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_GET['ajax_date_rows'])) {
    header('Content-Type: application/json; charset=utf-8');
    $p_date   = mysqli_real_escape_string($db, $_GET['p_date'] ?? '');
    $sel_dist = $is_sadmin ? intval($_GET['selected_district'] ?? $user_district_id) : $user_district_id;
    $rows = [];
    if (!empty($p_date) && $sel_dist > 0) {
        $res = execute_query("
    SELECT s.*,
           COALESCE(d.month_salary,          0)  AS month_salary,
           COALESCE(d.month_pension,         0)  AS month_pension,
           COALESCE(d.month_retirement_dues, 0)  AS month_retirement_dues,
           COALESCE(d.cum_salary,            0)  AS cum_salary,
           COALESCE(d.cum_pension,           0)  AS cum_pension,
           COALESCE(d.cum_retirement_dues,   0)  AS cum_retirement_dues,
           COALESCE(d.balance_period_months, '') AS balance_period_months,
           d.entry_date as view_date
    FROM sangrah_payment_static s
    INNER JOIN sangrah_payment_daily d
                   ON d.district_id   = s.district_id
                  AND d.employee_name = s.employee_name
                  AND d.entry_date    = '$p_date'
            WHERE s.district_id = $sel_dist
            ORDER BY s.mandal_name, s.employee_name");
        while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    }
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['id'] ?? '') === 'save_employee') {
    $district_id      = $is_sadmin ? intval($_POST['district_id'] ?? $user_district_id) : $user_district_id;
    $user_district_id = $district_id;
    $entry_date       = $today;

    $mandal_esc  = mysqli_real_escape_string($db, trim($_POST['mandal_name']         ?? ''));
    $emp_esc     = mysqli_real_escape_string($db, trim($_POST['employee_name']        ?? ''));
    $desig_esc   = mysqli_real_escape_string($db, trim($_POST['employee_designation'] ?? ''));
    $period_from = mysqli_real_escape_string($db, trim($_POST['balance_period_from'] ?? ''));
    $period_to   = mysqli_real_escape_string($db, trim($_POST['balance_period_to']   ?? ''));
    $period_esc  = $period_from && $period_to ? $period_from . ' से ' . $period_to : '';

    // कुल अधतन देयता (static)
    $t_sal      = floatval($_POST['total_salary']          ?? 0);
    $t_pen      = floatval($_POST['total_pension']         ?? 0);
    $t_ret      = floatval($_POST['total_retirement_dues'] ?? 0);

    // माह में भुगतान (daily)
    $m_sal      = floatval($_POST['month_salary']          ?? 0);
    $m_pen      = floatval($_POST['month_pension']         ?? 0);
    $m_ret      = floatval($_POST['month_retirement_dues'] ?? 0);

    // क्रमिक भुगतान (daily)
    $c_sal      = floatval($_POST['cum_salary']            ?? 0);
    $c_pen      = floatval($_POST['cum_pension']           ?? 0);
    $c_ret      = floatval($_POST['cum_retirement_dues']   ?? 0);

    if (empty($emp_esc)) {
        $msg = '<div class="alert alert-danger">कर्मचारी का नाम आवश्यक है।</div>';
    } else {
       $r1 = execute_query("INSERT INTO sangrah_payment_static
    (district_id, mandal_name, employee_name, employee_designation,
     total_salary, total_pension, total_retirement_dues, updated_at)
            VALUES ($district_id, '$mandal_esc', '$emp_esc', '$desig_esc',
                    $t_sal, $t_pen, $t_ret, NOW())
            ON DUPLICATE KEY UPDATE
                mandal_name          = VALUES(mandal_name),
                employee_designation = VALUES(employee_designation),
                total_salary         = VALUES(total_salary),
                total_pension        = VALUES(total_pension),
                total_retirement_dues= VALUES(total_retirement_dues),
                updated_at           = NOW()");

        $r2 = execute_query("INSERT INTO sangrah_payment_daily
            (district_id, employee_name, entry_date,
             month_salary, month_pension, month_retirement_dues,
             cum_salary,   cum_pension,   cum_retirement_dues,
             balance_period_months)
            VALUES ($district_id, '$emp_esc', '$entry_date',
                    $m_sal, $m_pen, $m_ret,
                    $c_sal, $c_pen, $c_ret,
                    '$period_esc')
            ON DUPLICATE KEY UPDATE
                month_salary          = VALUES(month_salary),
                month_pension         = VALUES(month_pension),
                month_retirement_dues = VALUES(month_retirement_dues),
                cum_salary            = VALUES(cum_salary),
                cum_pension           = VALUES(cum_pension),
                cum_retirement_dues   = VALUES(cum_retirement_dues),
                balance_period_months = VALUES(balance_period_months)");

        if ($r1 && $r2)
            $success = '<div class="alert alert-success">"' . htmlspecialchars(trim($_POST['employee_name']), ENT_QUOTES)
                . '" Data (' . date('d-m-Y') . ') Saved Successfully.</div>';
        else
            $msg = '<div class="alert alert-danger">Error saving data.</div>';
    }
}

$emp_rows = load_employee_rows($user_district_id, $today);

function v_s($key, $arr, $default = '') {
    return isset($arr[$key]) ? htmlspecialchars($arr[$key], ENT_QUOTES, 'UTF-8') : $default;
}

function render_employee_form($r, $district_id, $today, $is_new = false) {
    $v = function($key, $default = '') use ($r) {
        return isset($r[$key]) ? htmlspecialchars($r[$key], ENT_QUOTES, 'UTF-8') : $default;
    };
    $emp_label = $is_new ? 'नया कर्मचारी' : htmlspecialchars($r['employee_name'] ?? '', ENT_QUOTES, 'UTF-8');
    ob_start(); ?>
    <div class="emp-section<?php echo $is_new ? ' new-emp' : ''; ?>">
        <form action="" method="post" accept-charset="UTF-8">
            <input type="hidden" name="id"          value="save_employee">
            <input type="hidden" name="district_id" value="<?php echo intval($district_id); ?>">
            <input type="hidden" name="entry_date"  value="<?php echo $today; ?>">
            <div class="emp-section-header">
                <span class="emp-title"><?php echo $emp_label; ?></span>
                <?php if ($is_new): ?>
                    <button type="button" class="btn btn-sm btn-outline-light ml-2"
                            onclick="this.closest('.emp-section').remove();">✕ रद्द</button>
                <?php endif; ?>
            </div>
            <div class="emp-section-body">

                <!-- Section I: Basic Info + Total Liability -->
                <h5>(I) मूल विवरण एवं कुल अधतन देयता</h5>
                <div class="row">
                    <div class="col-sm-2 form-group">
                        <label>नाम मण्डल</label>
                        <input type="text" name="mandal_name" class="form-control" placeholder="मण्डल"
                               value="<?php echo $v('mandal_name'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>कर्मचारी का नाम <span style="color:red;">*</span></label>
                        <input type="text" name="employee_name" class="form-control"
                               placeholder="कर्मचारी का नाम" required
                            <?php echo !$is_new ? 'readonly style="background:#f0f0f0;"' : ''; ?>
                               value="<?php echo $v('employee_name'); ?>"
                               oninput="updateEmpTitle(this)">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>कर्मचारी का नाम व पद नाम</label>
                        <input type="text" name="employee_designation" class="form-control"
                               placeholder="लिपिक / चालक / अमीन"
                               value="<?php echo $v('employee_designation'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 form-group">
                        <label>वेतन (₹)</label>
                        <input type="number" step="0.01" min="0" name="total_salary" class="form-control"
                               placeholder="0.00" value="<?php echo $v('total_salary', '0.00'); ?>">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>पेंशन (₹)</label>
                        <input type="number" step="0.01" min="0" name="total_pension" class="form-control"
                               placeholder="0.00" value="<?php echo $v('total_pension', '0.00'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>सेवानिवृत्तिक देयता<br><small>(ग्रेच्युटी,नकदी,करण आदि) (₹)</small></label>
                        <input type="number" step="0.01" min="0" name="total_retirement_dues" class="form-control"
                               placeholder="0.00" value="<?php echo $v('total_retirement_dues', '0.00'); ?>">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>योग (स्वतः)</label>
                        <input type="number" step="0.01" name="total_yog" id="total_yog_<?php echo md5($v('employee_name','new')); ?>"
                               class="form-control readonly-field" readonly
                               value="<?php echo number_format(
                                   floatval($v('total_salary',0)) + floatval($v('total_pension',0)) + floatval($v('total_retirement_dues',0)),
                               2); ?>">
                    </div>
                </div>

                <!-- Section II: माह में भुगतान -->
                <h5>(II) माह में भुगतान — <?php echo date('d-m-Y'); ?></h5>
                <div class="row">
                    <div class="col-sm-2 form-group">
                        <label>वेतन (₹)</label>
                        <input type="number" step="0.01" min="0" name="month_salary" class="form-control"
                               placeholder="0.00" value="<?php echo $v('month_salary', '0.00'); ?>"
                               oninput="calcMonthYog(this)">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>पेंशन (₹)</label>
                        <input type="number" step="0.01" min="0" name="month_pension" class="form-control"
                               placeholder="0.00" value="<?php echo $v('month_pension', '0.00'); ?>"
                               oninput="calcMonthYog(this)">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>सेवानिवृत्तिक देयता<br><small>(ग्रेच्युटी,नकदी,करण आदि) (₹)</small></label>
                        <input type="number" step="0.01" min="0" name="month_retirement_dues" class="form-control"
                               placeholder="0.00" value="<?php echo $v('month_retirement_dues', '0.00'); ?>"
                               oninput="calcMonthYog(this)">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>योग (स्वतः)</label>
                        <input type="number" step="0.01" name="month_yog" class="form-control readonly-field" readonly
                               value="<?php echo number_format(
                                   floatval($v('month_salary',0)) + floatval($v('month_pension',0)) + floatval($v('month_retirement_dues',0)),
                               2); ?>">
                    </div>
                </div>

                <!-- Section III: क्रमिक भुगतान -->
                <h5>(III) क्रमिक भुगतान</h5>
                <div class="row">
                    <div class="col-sm-2 form-group">
                        <label>वेतन (₹)</label>
                        <input type="number" step="0.01" min="0" name="cum_salary" class="form-control"
                               placeholder="0.00" value="<?php echo $v('cum_salary', '0.00'); ?>"
                               oninput="calcCumYog(this)">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>पेंशन (₹)</label>
                        <input type="number" step="0.01" min="0" name="cum_pension" class="form-control"
                               placeholder="0.00" value="<?php echo $v('cum_pension', '0.00'); ?>"
                               oninput="calcCumYog(this)">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>सेवानिवृत्तिक देयता<br><small>(ग्रेच्युटी,नकदी,करण आदि) (₹)</small></label>
                        <input type="number" step="0.01" min="0" name="cum_retirement_dues" class="form-control"
                               placeholder="0.00" value="<?php echo $v('cum_retirement_dues', '0.00'); ?>"
                               oninput="calcCumYog(this)">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>योग (स्वतः)</label>
                        <input type="number" step="0.01" name="cum_yog" class="form-control readonly-field" readonly
                               value="<?php echo number_format(
                                   floatval($v('cum_salary',0)) + floatval($v('cum_pension',0)) + floatval($v('cum_retirement_dues',0)),
                               2); ?>">
                    </div>
                </div>

                <!-- Section IV: माह के अन्त में अवशेष देयता (auto-calculated) + अवधि -->
                <h5>(IV) माह के अन्त में अवशेष देयता एवं अवधि</h5>
                <div class="row">
                    <div class="col-sm-2 form-group">
                        <label>वेतन अवशेष (₹)</label>
                        <input type="number" step="0.01" name="bal_salary" class="form-control readonly-field" readonly
                               placeholder="0.00">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>पेंशन अवशेष (₹)</label>
                        <input type="number" step="0.01" name="bal_pension" class="form-control readonly-field" readonly
                               placeholder="0.00">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>सेवानिवृत्तिक अवशेष (₹)</label>
                        <input type="number" step="0.01" name="bal_retirement" class="form-control readonly-field" readonly
                               placeholder="0.00">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>योग अवशेष (₹)</label>
                        <input type="number" step="0.01" name="bal_yog" class="form-control readonly-field" readonly
                               placeholder="0.00">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>अवशेष देयता की अवधि</label>
                        <input type="month" name="balance_period_from" class="form-control mb-1"
                            placeholder="कब से"
                            value="<?php echo $v('balance_period_from', ''); ?>">
                        <small class="text-muted d-block text-center">से</small>
                        <input type="month" name="balance_period_to" class="form-control mt-1"
                            placeholder="कब तक"
                            value="<?php echo $v('balance_period_to', ''); ?>">
                    </div>
                </div>

                <div class="row mt-1 mb-2">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-danger">
                            💾 <?php echo $is_new
                                ? 'कर्मचारी जोड़ें और सहेजें'
                                : '"' . $emp_label . '" Submit'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

page_header_start();
?>
    <style>
        .step h4 { color:#fff; background:#FF8E00; border-radius:15px; padding:10px 10px 6px 20px; }
        .step h5 { color:#000; background:#FFDB44; border-radius:15px; padding:10px 10px 6px 20px; }
        .info-box { background:#fff8e1; border-left:4px solid #FF8E00; padding:10px 15px; border-radius:5px; font-size:13px; color:#555; }
        .readonly-field { background:#f0f0f0 !important; font-weight:bold; cursor:not-allowed; }
        .emp-section         { border:2px solid #FF8E00; border-radius:12px; margin-bottom:20px; overflow:hidden; }
        .emp-section-header  { background:#FF8E00; color:#fff; padding:10px 16px; font-size:15px; font-weight:bold; display:flex; align-items:center; justify-content:space-between; }
        .emp-section-body    { padding:15px 15px 5px; background:#fffaf5; }
        .emp-section.new-emp .emp-section-header { background:#28a745; }
        .emp-section.new-emp { border-color:#28a745; }
        .prog-table                   { border:2px solid #FF8E00; border-collapse:collapse; width:100%; }
        .prog-table thead tr th       { background:#FF8E00 !important; color:#fff !important; text-align:center; vertical-align:middle; font-size:10px; white-space:nowrap; padding:5px 3px; border:1px solid #e07000; }
        .prog-table tbody tr td       { text-align:center; vertical-align:middle; font-size:11px; padding:4px 3px; border:1px solid #FF8E00; }
        .prog-table tbody tr:hover td { background:#fff3e0; }
        .prog-table tfoot tr td       { background:#fff3e0; font-weight:bold; font-size:11px; border:1px solid #FF8E00; text-align:center; }
        #district_loading { display:none; color:#FF8E00; font-size:13px; margin-top:4px; }
        #prog_loading     { display:none; color:#FF8E00; font-size:13px; }
          @media print {
            #no-print {
                display: none !important;
            }
        }
        #printArea {
    background: white;
    padding: 10px;
}
    </style>
<?php
page_header_end();
page_sidebar();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <div class="row" id="no-print">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="text-center mb-3">
                            <h4><u>प्रारूप–7</u></h4>
                            <p style="font-size:13px;">संग्रह योजना के अन्तर्गत प्रदेश में कार्यरत / सेवानिवृत्त लिपिक, चालक एवं सहयोगी तथा वैतनिक अमीन व अमीन सहयोगियों की मासिक/क्रमिक भुगतान की प्रगति<br>
                            <strong>दिनांक 01.07.2025 से ........................ तक</strong></p>
                        </div>
                        <?php echo $msg; echo $success; ?>

                        <!-- Step 1: District & Date -->
                        <div class="step">
                            <marquee style="font-size:16px; color:red;">
                                नोट: प्रत्येक कर्मचारी का दैनिक भुगतान विवरण प्रतिदिन सही-सही भरें। धनराशि रुपये में भरें।
                            </marquee><br>
                            <h4>1. जनपद एवं दिनांक</h4>
                            <div class="col-sm-12">
                                <div class="row align-items-center">
                                    <div class="col-sm-4 form-group">
                                        <?php if ($is_sadmin): ?>
                                            <select id="sadmin_district_select" class="form-control"
                                                    onchange="sadminDistrictChange(this.value);">
                                                <?php foreach ($all_districts as $dist): ?>
                                                    <option value="<?php echo intval($dist['sno']); ?>"
                                                        <?php echo (intval($dist['sno']) === $user_district_id) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($dist['district_name'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted">जनपद बदलें — फॉर्म स्वतः अपडेट होगा।</small>
                                            <div id="district_loading">⏳ Loading...</div>
                                        <?php else: ?>
                                            <div class="form-control readonly-field">
                                                <?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>दिनांक (आज)</label>
                                        <input type="text" class="form-control readonly-field"
                                               value="<?php echo date('d-m-Y'); ?>" readonly>
                                    </div>
                                    <div class="col-sm-5 form-group">
                                        <div class="info-box">
                                            प्रत्येक कर्मचारी का फॉर्म अलग-अलग सहेजा जाता है।<br>
                                            <strong>केवल आज का डेटा भरा/अपडेट किया जा सकता है।</strong>
                                            पूर्व दिनांक का डेटा Section 3 में देखें।
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Employee Forms -->
                        <div class="step">
                            <h4>2. कर्मचारीवार विवरण एवं आज का भुगतान
                                (<span id="entry_district_name"><?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                — <?php echo date('d-m-Y'); ?>)
                            </h4>
                            <div class="col-sm-12" id="emp_forms_wrap">
                                <?php if (empty($emp_rows)): ?>
                                    <?php echo render_employee_form([], $user_district_id, $today, true); ?>
                                <?php else: ?>
                                    <?php foreach ($emp_rows as $r): ?>
                                        <?php echo render_employee_form($r, $user_district_id, $today, false); ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <div id="new_emp_container"></div>
                                <div class="row mt-2 mb-3">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-success btn-lg" onclick="addNewEmpForm();">
                                            + नया कर्मचारी जोड़ें
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
 </div>
                </div>
            </div>
        </div>
    </div>
                                    </form>
 <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                         <div class="text-center mb-3">
                            <h4><u>प्रारूप–7</u></h4>
                            <p style="font-size:13px;">संग्रह योजना के अन्तर्गत प्रदेश में कार्यरत / सेवानिवृत्त लिपिक, चालक एवं सहयोगी तथा वैतनिक अमीन व अमीन सहयोगियों की मासिक/क्रमिक भुगतान की प्रगति<br>
                            <strong>दिनांक 01.07.2025 से ........................ तक</strong></p>
                        </div>
                        <!-- Step 3: View Progress Table (21 columns as per Proforma-7) -->
                        <div class="step">
                             <!-- ✅ EXPORT BUTTON -->
                            <div>
                                <button class="btn btn-danger" onclick="exportToPDF()">
                                    📄 Export PDF
                                </button>
                            </div>
                            <h4>3. दिनांकवार कर्मचारी भुगतान प्रगति देखें
                                (<span id="view_district_name"><?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?></span>)
                            </h4>
                            <div class="col-sm-12">
                                <div class="row align-items-end mb-3">
                                    <div class="col-sm-3 form-group">
                                        <label>दिनांक चुनें</label>
                                        <input type="date" id="view_date" class="form-control"
                                               value="<?php echo date('Y-m-d'); ?>">
                                        <small class="text-muted">आज या कोई भी पूर्व दिनांक।</small>
                                    </div>
                                    <div class="col-sm-4 form-group">
                                        <button type="button" class="btn btn-warning btn-lg" onclick="loadProgTable();">
                                           Search
                                        </button>
                                        <span id="prog_loading" class="ml-2">⏳ Loading...</span>
                                    </div>
                                </div>
                                <div id="prog_section">
                                    <h6 id="prog_title" style="font-weight:bold; color:#333; margin-bottom:8px;"></h6>
                                    <p style="font-size:12px; color:#555;">धनराशि रुपये में</p>
                                    <div class="table-responsive"  id="printArea">
                                        <table class="table-bordered prog-table">
                                            <thead>
                                            <tr>
                                                <th rowspan="2">क्र0<br>स0<br>(1)</th>
                                                <th rowspan="2">नाम<br>मण्डल<br>(2)</th>
                                                <th rowspan="2">नाम<br>जनपद<br>(3)</th>
                                                <th rowspan="2">कर्मचारी का नाम<br>व पद नाम<br>(4)</th>
                                                <!-- कुल अधतन देयता -->
                                                <th colspan="4" style="background:#cc6600 !important;">कुल अधतन देयता</th>
                                                <!-- माह में भुगतान -->
                                                <th colspan="4" style="background:#b35900 !important;">माह में भुगतान</th>
                                                <!-- क्रमिक भुगतान -->
                                                <th colspan="4" style="background:#994d00 !important;">क्रमिक भुगतान</th>
                                                <!-- माह के अन्त में अवशेष देयता -->
                                                <th colspan="4" style="background:#804000 !important;">माह के अन्त में अवशेष देयता</th>
                                                <!-- अवशेष देयता की अवधि -->
                                                <th rowspan="2">अवशेष देयता<br>की अवधि<br>(कब से कब तक)<br>(21)</th>
                                            </tr>
                                            <tr>
                                                <!-- कुल अधतन देयता sub-cols -->
                                                <th>वेतन<br>(₹)<br>(5)</th>
                                                <th>पेंशन<br>(₹)<br>(6)</th>
                                                <th>सेवानिवृत्तिक<br>देयता<br>(ग्रेच्युटी,नकदी<br>करण आदि)<br>(7)</th>
                                                <th>योग<br>(₹)<br>(8)</th>
                                                <!-- माह में भुगतान sub-cols -->
                                                <th>वेतन<br>(₹)<br>(9)</th>
                                                <th>पेंशन<br>(₹)<br>(10)</th>
                                                <th>सेवानिवृत्तिक<br>देयता<br>(ग्रेच्युटी,नकदी<br>करण आदि)<br>(11)</th>
                                                <th>योग<br>(₹)<br>(12)</th>
                                                <!-- क्रमिक भुगतान sub-cols -->
                                                <th>वेतन<br>(₹)<br>(13)</th>
                                                <th>पेंशन<br>(₹)<br>(14)</th>
                                                <th>सेवानिवृत्तिक<br>देयता<br>(ग्रेच्युटी,नकदी<br>करण आदि)<br>(15)</th>
                                                <th>योग<br>(₹)<br>(16)</th>
                                                <!-- अवशेष देयता sub-cols -->
                                                <th>वेतन<br>(₹)<br>(17)</th>
                                                <th>पेंशन<br>(₹)<br>(18)</th>
                                                <th>सेवानिवृत्तिक<br>देयता<br>(ग्रेच्युटी,नकदी<br>करण आदि)<br>(19)</th>
                                                <th>योग<br>(₹)<br>(20)</th>
                                            </tr>
                                            </thead>
                                            <tbody id="prog_tbody"></tbody>
                                            <tfoot id="prog_tfoot">
                                                <tr id="row_janpad_yog" style="display:none;">
                                                    <td colspan="4" style="text-align:right; font-weight:bold;">जनपदीय योग</td>
                                                    <td id="ft_ts"></td><td id="ft_tp"></td><td id="ft_tr"></td><td id="ft_tt"></td>
                                                    <td id="ft_ms"></td><td id="ft_mp"></td><td id="ft_mr"></td><td id="ft_mt"></td>
                                                    <td id="ft_cs"></td><td id="ft_cp"></td><td id="ft_cr"></td><td id="ft_ct"></td>
                                                    <td id="ft_bs"></td><td id="ft_bp"></td><td id="ft_br"></td><td id="ft_bt"></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div id="prog_nodata" class="text-danger font-weight-bold" style="display:none;">
                                        इस जनपद में अभी कोई कर्मचारी नहीं है।
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var isSadmin          = <?php echo $is_sadmin ? 'true' : 'false'; ?>;
        var fixedDistrictName = "<?php echo addslashes(htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8')); ?>";
        var currentDistrictId = <?php echo $user_district_id; ?>;

        /* ---- Auto-calc helpers inside each emp-section ---- */
        function updateEmpTitle(input) {
            var hdr = input.closest('.emp-section').querySelector('.emp-title');
            if (hdr) hdr.textContent = input.value || 'नया कर्मचारी';
        }

        function calcSectionYog(trigger, names, yogName) {
            var sec = trigger.closest('.emp-section-body');
            if (!sec) return;
            var total = 0;
            names.forEach(function(n) {
                var el = sec.querySelector('[name="' + n + '"]');
                if (el) total += parseFloat(el.value || 0);
            });
            var yog = sec.querySelector('[name="' + yogName + '"]');
            if (yog) yog.value = total.toFixed(2);
            // also recalc balance row
            calcBalanceRow(sec);
        }

        function calcMonthYog(trigger) {
            calcSectionYog(trigger,
                ['month_salary','month_pension','month_retirement_dues'],
                'month_yog');
        }
        function calcCumYog(trigger) {
            calcSectionYog(trigger,
                ['cum_salary','cum_pension','cum_retirement_dues'],
                'cum_yog');
        }

        function calcBalanceRow(sec) {
            var getV = function(n) {
                var el = sec.querySelector('[name="' + n + '"]');
                return el ? parseFloat(el.value || 0) : 0;
            };
            var setV = function(n, v) {
                var el = sec.querySelector('[name="' + n + '"]');
                if (el) el.value = v.toFixed(2);
            };
            var bs = getV('total_salary')         - getV('cum_salary');
            var bp = getV('total_pension')        - getV('cum_pension');
            var br = getV('total_retirement_dues')- getV('cum_retirement_dues');
            setV('bal_salary',     Math.max(0, bs));
            setV('bal_pension',    Math.max(0, bp));
            setV('bal_retirement', Math.max(0, br));
            setV('bal_yog',        Math.max(0, bs) + Math.max(0, bp) + Math.max(0, br));
        }

        /* Also trigger balance on total_* changes */
        document.addEventListener('input', function(e) {
            if (['total_salary','total_pension','total_retirement_dues'].indexOf(e.target.name) > -1) {
                var sec = e.target.closest('.emp-section-body');
                if (sec) calcBalanceRow(sec);
                // recalc total yog
                var names = ['total_salary','total_pension','total_retirement_dues'];
                var total = 0;
                names.forEach(function(n) {
                    var el = sec.querySelector('[name="' + n + '"]');
                    if (el) total += parseFloat(el.value || 0);
                });
                var yog = sec.querySelector('[name="total_yog"]');
                if (yog) yog.value = total.toFixed(2);
            }
        });

        /* ---- Add new employee form ---- */
        function addNewEmpForm() {
            var distId  = currentDistrictId;
            var today   = "<?php echo $today; ?>";
            var todayFt = "<?php echo date('d-m-Y'); ?>";

            var html = buildEmpFormHTML({}, distId, true, todayFt, today);
            document.getElementById('new_emp_container').insertAdjacentHTML('beforeend', html);
            var forms = document.getElementById('new_emp_container').querySelectorAll('.emp-section');
            if (forms.length) forms[forms.length-1].scrollIntoView({behavior:'smooth', block:'start'});
        }

        function buildEmpFormHTML(r, distId, isNew, todayFt, today) {
            var empName      = r.employee_name || '';
            var hdrStyle     = isNew ? 'style="background:#28a745;"' : '';
            var borderStyle  = isNew ? 'style="border-color:#28a745;"' : '';
            var readAttr     = isNew ? '' : 'readonly style="background:#f0f0f0;"';
            var oninputAttr  = isNew ? 'oninput="updateEmpTitle(this)"' : '';
            var cancelBtn    = isNew ? '<button type="button" class="btn btn-sm btn-outline-light ml-2" onclick="this.closest(\'.emp-section\').remove();">✕ रद्द</button>' : '';
            var hdrLabel     = isNew ? 'नया कर्मचारी' : empName;
            var submitLabel  = isNew ? 'Add Employee & Submit' : '💾 "' + empName + '" Submit';
            var periodParts = (r.balance_period_months || '').split(' से ');
            var pFrom = periodParts[0] || '';
            var pTo   = periodParts[1] || '';

            function numFld(lbl, name, val, col, sub) {
                sub = sub || '';
                return '<div class="col-sm-' + col + ' form-group"><label>' + lbl + (sub ? '<br><small>' + sub + '</small>' : '') + '</label>' +
                    '<input type="number" step="0.01" min="0" name="' + name + '" class="form-control" value="' + (parseFloat(val||0).toFixed(2)) + '" placeholder="0.00"></div>';
            }
            function roFld(lbl, name, col) {
                return '<div class="col-sm-' + col + ' form-group"><label>' + lbl + '</label>' +
                    '<input type="number" step="0.01" name="' + name + '" class="form-control readonly-field" readonly value="0.00"></div>';
            }

            return '<div class="emp-section" ' + borderStyle + '>' +
                '<div class="emp-section-header" ' + hdrStyle + '><span class="emp-title">' + hdrLabel + '</span>' + cancelBtn + '</div>' +
                '<form action="" method="post" accept-charset="UTF-8">' +
                '<input type="hidden" name="id" value="save_employee">' +
                '<input type="hidden" name="district_id" value="' + distId + '">' +
                '<input type="hidden" name="entry_date" value="' + today + '">' +
                '<div class="emp-section-body">' +
                '<h5>(I) मूल विवरण एवं कुल अधतन देयता</h5>' +
                '<div class="row">' +
                '<div class="col-sm-2 form-group"><label>नाम मण्डल</label><input type="text" name="mandal_name" class="form-control" value="' + (r.mandal_name||'') + '" placeholder="मण्डल"></div>' +
                '<div class="col-sm-3 form-group"><label>कर्मचारी का नाम <span style="color:red;">*</span></label><input type="text" name="employee_name" class="form-control" value="' + empName + '" ' + readAttr + ' ' + oninputAttr + ' placeholder="कर्मचारी का नाम" required></div>' +
                '<div class="col-sm-3 form-group"><label>कर्मचारी का नाम व पद नाम</label><input type="text" name="employee_designation" class="form-control" value="' + (r.employee_designation||'') + '" placeholder="लिपिक / चालक / अमीन"></div>' +
                '</div><div class="row">' +
                numFld('वेतन (₹)', 'total_salary', r.total_salary, '2') +
                numFld('पेंशन (₹)', 'total_pension', r.total_pension, '2') +
                numFld('सेवानिवृत्तिक देयता (₹)', 'total_retirement_dues', r.total_retirement_dues, '3', 'ग्रेच्युटी,नकदी,करण आदि') +
                roFld('योग (स्वतः)', 'total_yog', '2') +
                '</div>' +
                '<h5>(II) माह में भुगतान — ' + todayFt + '</h5>' +
                '<div class="row">' +
                numFld('वेतन (₹)', 'month_salary', r.month_salary, '2') +
                numFld('पेंशन (₹)', 'month_pension', r.month_pension, '2') +
                numFld('सेवानिवृत्तिक देयता (₹)', 'month_retirement_dues', r.month_retirement_dues, '3', 'ग्रेच्युटी,नकदी,करण आदि') +
                roFld('योग (स्वतः)', 'month_yog', '2') +
                '</div>' +
                '<h5>(III) क्रमिक भुगतान</h5>' +
                '<div class="row">' +
                numFld('वेतन (₹)', 'cum_salary', r.cum_salary, '2') +
                numFld('पेंशन (₹)', 'cum_pension', r.cum_pension, '2') +
                numFld('सेवानिवृत्तिक देयता (₹)', 'cum_retirement_dues', r.cum_retirement_dues, '3', 'ग्रेच्युटी,नकदी,करण आदि') +
                roFld('योग (स्वतः)', 'cum_yog', '2') +
                '</div>' +
                '<h5>(IV) माह के अन्त में अवशेष देयता एवं अवधि</h5>' +
                '<div class="row">' +
                roFld('वेतन अवशेष (₹)', 'bal_salary', '2') +
                roFld('पेंशन अवशेष (₹)', 'bal_pension', '2') +
                roFld('सेवानिवृत्तिक अवशेष (₹)', 'bal_retirement', '3') +
                roFld('योग अवशेष (₹)', 'bal_yog', '2') +
                '<div class="col-sm-2 form-group"><label>अवशेष देयता की अवधि</label>' +
                '<input type="month" name="balance_period_from" class="form-control mb-1" value="' + pFrom + '" placeholder="कब से">' +
                '<small class="text-muted d-block text-center">से</small>' +
                '<input type="month" name="balance_period_to" class="form-control mt-1" value="' + pTo + '" placeholder="कब तक">' +
                '</div>' +
                '</div>' +
                '<div class="row mt-1 mb-2"><div class="col-sm-12"><button type="submit" class="btn btn-danger">' + submitLabel + '</button></div></div>' +
                '</div></form></div>';
        }

        function sadminDistrictChange(districtId) {
            if (!isSadmin || !districtId) return;
            currentDistrictId = parseInt(districtId);
            document.getElementById('district_loading').style.display = 'block';
            var sel = document.getElementById('sadmin_district_select');
            fixedDistrictName = sel.options[sel.selectedIndex].text;
            var edn = document.getElementById('entry_district_name');
            if (edn) edn.textContent = fixedDistrictName;
            var vdn = document.getElementById('view_district_name');
            if (vdn) vdn.textContent = fixedDistrictName;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', '?ajax_district_data=1&selected_district=' + districtId + '&_=' + Date.now(), true);
            xhr.onload = function () {
                document.getElementById('district_loading').style.display = 'none';
                if (xhr.status !== 200) return;
                try {
                    var rows = JSON.parse(xhr.responseText);
                    var wrap = document.getElementById('emp_forms_wrap');
                    wrap.querySelectorAll('.emp-section').forEach(function(el){ el.remove(); });
                    document.getElementById('new_emp_container').innerHTML = '';
                    var container = document.getElementById('new_emp_container');
                    var todayFt = "<?php echo date('d-m-Y'); ?>";
                    var today   = "<?php echo $today; ?>";
                    if (rows.length === 0) {
                        container.insertAdjacentHTML('beforebegin', buildEmpFormHTML({}, districtId, true, todayFt, today));
                    } else {
                        rows.forEach(function(r) {
                            container.insertAdjacentHTML('beforebegin', buildEmpFormHTML(r, districtId, false, todayFt, today));
                        });
                    }
                    loadProgTable();
                } catch(e) { console.error(e); }
            };
            xhr.send();
        }

        function loadProgTable() {
            var vdate = document.getElementById('view_date').value;
            if (!vdate) return;
            document.getElementById('prog_loading').style.display = 'inline';
            var url = '?ajax_date_rows=1&p_date=' + vdate + '&_=' + Date.now();
            if (isSadmin) url += '&selected_district=' + currentDistrictId;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onload = function () {
                document.getElementById('prog_loading').style.display = 'none';
                if (xhr.status !== 200) return;
                try {
                    var rows   = JSON.parse(xhr.responseText);
                    var tbody  = document.getElementById('prog_tbody');
                    var title  = document.getElementById('prog_title');
                    var nodata = document.getElementById('prog_nodata');
                    var jyRow  = document.getElementById('row_janpad_yog');

                    tbody.innerHTML      = '';
                    nodata.style.display = 'none';
                    jyRow.style.display  = 'none';

                    var p = vdate.split('-');
                    title.textContent = fixedDistrictName + ' — दिनांक: ' + p[2] + '-' + p[1] + '-' + p[0];

                    if (!rows || rows.length === 0) {
                        nodata.style.display = 'block';
                        return;
                    }

                    // Totals for जनपदीय योग
                    var totals = {ts:0,tp:0,tr:0,tt:0, ms:0,mp:0,mr:0,mt:0, cs:0,cp:0,cr:0,ct:0, bs:0,bp:0,br:0,bt:0};

                    rows.forEach(function(r, i) {
                        var d = function(v) { return (v !== null && v !== undefined && v !== '') ? parseFloat(v).toFixed(2) : '0.00'; };

                        var ts = parseFloat(r.total_salary          || 0);
                        var tp = parseFloat(r.total_pension         || 0);
                        var tr = parseFloat(r.total_retirement_dues || 0);
                        var tt = ts + tp + tr;

                        var ms = parseFloat(r.month_salary          || 0);
                        var mp = parseFloat(r.month_pension         || 0);
                        var mr = parseFloat(r.month_retirement_dues || 0);
                        var mt = ms + mp + mr;

                        var cs = parseFloat(r.cum_salary            || 0);
                        var cp = parseFloat(r.cum_pension           || 0);
                        var cr = parseFloat(r.cum_retirement_dues   || 0);
                        var ct = cs + cp + cr;

                        // Balance = Total - Cumulative
                        var bs = Math.max(0, ts - cs);
                        var bp = Math.max(0, tp - cp);
                        var br = Math.max(0, tr - cr);
                        var bt = bs + bp + br;

                        totals.ts += ts; totals.tp += tp; totals.tr += tr; totals.tt += tt;
                        totals.ms += ms; totals.mp += mp; totals.mr += mr; totals.mt += mt;
                        totals.cs += cs; totals.cp += cp; totals.cr += cr; totals.ct += ct;
                        totals.bs += bs; totals.bp += bp; totals.br += br; totals.bt += bt;

                        tbody.innerHTML +=
                            '<tr>' +
                            '<td>' + (i+1) + '</td>' +
                            '<td>' + (r.mandal_name || '-') + '</td>' +
                            '<td>' + fixedDistrictName + '</td>' +
                            '<td><strong>' + (r.employee_name || '-') + '</strong><br><small>' + (r.employee_designation || '') + '</small></td>' +
                            '<td>' + ts.toFixed(2) + '</td>' +
                            '<td>' + tp.toFixed(2) + '</td>' +
                            '<td>' + tr.toFixed(2) + '</td>' +
                            '<td><strong>' + tt.toFixed(2) + '</strong></td>' +
                            '<td>' + ms.toFixed(2) + '</td>' +
                            '<td>' + mp.toFixed(2) + '</td>' +
                            '<td>' + mr.toFixed(2) + '</td>' +
                            '<td><strong>' + mt.toFixed(2) + '</strong></td>' +
                            '<td>' + cs.toFixed(2) + '</td>' +
                            '<td>' + cp.toFixed(2) + '</td>' +
                            '<td>' + cr.toFixed(2) + '</td>' +
                            '<td><strong>' + ct.toFixed(2) + '</strong></td>' +
                            '<td>' + bs.toFixed(2) + '</td>' +
                            '<td>' + bp.toFixed(2) + '</td>' +
                            '<td>' + br.toFixed(2) + '</td>' +
                            '<td><strong>' + bt.toFixed(2) + '</strong></td>' +
                            '<td>' + (r.balance_period_months || '-') + '</td>' +
                            '</tr>';
                    });

                    // Populate जनपदीय योग footer
                    document.getElementById('ft_ts').textContent = totals.ts.toFixed(2);
                    document.getElementById('ft_tp').textContent = totals.tp.toFixed(2);
                    document.getElementById('ft_tr').textContent = totals.tr.toFixed(2);
                    document.getElementById('ft_tt').textContent = totals.tt.toFixed(2);
                    document.getElementById('ft_ms').textContent = totals.ms.toFixed(2);
                    document.getElementById('ft_mp').textContent = totals.mp.toFixed(2);
                    document.getElementById('ft_mr').textContent = totals.mr.toFixed(2);
                    document.getElementById('ft_mt').textContent = totals.mt.toFixed(2);
                    document.getElementById('ft_cs').textContent = totals.cs.toFixed(2);
                    document.getElementById('ft_cp').textContent = totals.cp.toFixed(2);
                    document.getElementById('ft_cr').textContent = totals.cr.toFixed(2);
                    document.getElementById('ft_ct').textContent = totals.ct.toFixed(2);
                    document.getElementById('ft_bs').textContent = totals.bs.toFixed(2);
                    document.getElementById('ft_bp').textContent = totals.bp.toFixed(2);
                    document.getElementById('ft_br').textContent = totals.br.toFixed(2);
                    document.getElementById('ft_bt').textContent = totals.bt.toFixed(2);
                    jyRow.style.display = '';

                } catch(e) { console.error(e); }
            };
            xhr.send();
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadProgTable();
        });

        function exportToPDF() {

    var element = document.getElementById("printArea");

    if (!element) {
        alert("Print area not found!");
        return;
    }

    // Hide loading if visible
    document.getElementById("prog_loading").style.display = "none";

    html2canvas(element, {
        scale: 2,
        useCORS: true
    }).then(function(canvas) {

        var imgData = canvas.toDataURL("image/png");

        const { jsPDF } = window.jspdf;
        var pdf = new jsPDF('l', 'mm', 'a4'); // landscape

        var imgWidth = 297; // A4 width
        var pageHeight = 210;
        var imgHeight = canvas.height * imgWidth / canvas.width;

        var heightLeft = imgHeight;
        var position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Multiple pages support
        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save("pacs_report.pdf");

    });
}
    </script>

<?php
page_footer_start();
page_footer_end();
?>