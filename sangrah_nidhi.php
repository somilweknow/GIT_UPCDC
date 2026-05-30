<?php
include("scripts/settings.php");

$is_sadmin = (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'sadmin');
$is_dr = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'ar_dr');

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
    if ($is_dr) {
        $all_districts = [];
        $res_all = execute_query("SELECT md.sno, md.district_name FROM master_district md 
            WHERE md.division_id = (SELECT division_name FROM ar_dr WHERE sno = " . intval($_SESSION['usersno']) . ") 
            ORDER BY md.district_name");
        while ($row = mysqli_fetch_assoc($res_all)) $all_districts[] = $row;
        if (isset($_GET['selected_district']) && intval($_GET['selected_district']) > 0)
            $user_district_id = intval($_GET['selected_district']);
        elseif (isset($_POST['district_id']) && intval($_POST['district_id']) > 0)
            $user_district_id = intval($_POST['district_id']);
        else
            $user_district_id = !empty($all_districts) ? intval($all_districts[0]['sno']) : 0;
        if ($user_district_id <= 0)
            die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
        $session_districts = array_map('intval', array_column($all_districts, 'sno'));
    } else {
        $user_district_id = !empty($session_districts) ? $session_districts[0] : 0;
        if ($user_district_id <= 0)
            die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
    }
}

$res_dn             = execute_query("SELECT district_name FROM master_district WHERE sno = $user_district_id");
$user_district_name = (mysqli_num_rows($res_dn) > 0) ? mysqli_fetch_assoc($res_dn)['district_name'] : '';

$today   = date('Y-m-d');
$msg     = '';
$success = '';

// ─── Load static + daily data for a district+date ────────────────────────────
function load_nidhi_data($district_id, $entry_date) {
    $district_id = intval($district_id);
    $entry_date  = mysqli_real_escape_string($GLOBALS['db'], $entry_date);
    $res = execute_query("
        SELECT s.*,
               d.month_salary_pension, d.month_commission,
               d.month_vehicle_expense, d.month_other_expense,
               d.cumul_salary_pension, d.cumul_commission,
               d.cumul_vehicle_expense, d.cumul_other_expense,
               d.balance_in_account,
               d.entry_date as view_date
        FROM sangrah_nidhi_static s
        LEFT JOIN sangrah_nidhi_daily d
               ON d.district_id = s.district_id
              AND d.entry_date  = '$entry_date'
        WHERE s.district_id = $district_id");
    return mysqli_fetch_assoc($res) ?: [];
}

function load_nidhi_rows_range($district_id, $date_from, $date_to) {
    $district_id = intval($district_id);
    $date_from   = mysqli_real_escape_string($GLOBALS['db'], $date_from);
    $date_to     = mysqli_real_escape_string($GLOBALS['db'], $date_to);
    $rows = [];
    $res = execute_query("
        SELECT d.district_name,
               s.mandal_name, s.opening_balance, s.income_received, s.total_income,
               s.fd_amount, s.fd_maturity_period,
               dy.district_id AS c_district_id,
               dy.entry_date,
               dy.month_salary_pension, dy.month_commission,
               dy.month_vehicle_expense, dy.month_other_expense,
               dy.cumul_salary_pension, dy.cumul_commission,
               dy.cumul_vehicle_expense, dy.cumul_other_expense,
               dy.balance_in_account,
               dy.status, dy.dr_remark, dy.reviewed_at,
               s.updated_at
        FROM sangrah_nidhi_daily dy
        JOIN master_district d        ON d.sno          = dy.district_id
        LEFT JOIN sangrah_nidhi_static s ON s.district_id = dy.district_id
        WHERE dy.entry_date BETWEEN '$date_from' AND '$date_to'
          AND dy.district_id = $district_id
        ORDER BY dy.entry_date, d.district_name");
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    return $rows;
}

if (isset($_POST['id']) && $_POST['id'] === 'dr_review') {
    header('Content-Type: application/json; charset=utf-8');
    $rev_district  = intval($_POST['district_id'] ?? 0);
    $rev_date_from = mysqli_real_escape_string($db, $_POST['date_from'] ?? '');
    $rev_date_to   = mysqli_real_escape_string($db, $_POST['date_to']   ?? $rev_date_from);
    $rev_status    = in_array($_POST['status'], ['approved','rejected']) ? $_POST['status'] : '';
    $rev_remark    = mysqli_real_escape_string($db, trim($_POST['dr_remark'] ?? ''));

    if (!$is_dr) {
        echo json_encode(['ok' => false, 'msg' => 'केवल AR/DR अनुमोदन कर सकते हैं।'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!$rev_district || !$rev_date_from || !$rev_status) {
        echo json_encode(['ok' => false, 'msg' => 'अपूर्ण डेटा।'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($rev_status === 'rejected' && $rev_remark === '') {
        echo json_encode(['ok' => false, 'msg' => 'अस्वीकृत करने पर टिप्पणी अनिवार्य है।'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $r = execute_query("UPDATE sangrah_nidhi_daily
        SET status = '$rev_status', dr_remark = '$rev_remark',
            reviewed_by = $current_user_sno, reviewed_at = NOW()
        WHERE district_id = $rev_district
          AND entry_date BETWEEN '$rev_date_from' AND '$rev_date_to'");
    if ($r)
        echo json_encode(['ok' => true, 'msg' => ($rev_status === 'approved' ? 'स्वीकृत' : 'अस्वीकृत') . ' किया गया।'], JSON_UNESCAPED_UNICODE);
    else
        echo json_encode(['ok' => false, 'msg' => 'अपडेट विफल।'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── AJAX: sadmin district switch ────────────────────────────────────────────
if ($is_sadmin && isset($_GET['ajax_district_data'])) {
    header('Content-Type: application/json; charset=utf-8');
    $sel_dist = intval($_GET['selected_district'] ?? 0);
    echo json_encode($sel_dist > 0 ? load_nidhi_data($sel_dist, $today) : [], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── AJAX: date-wise read-only view (INNER JOIN — only dates with daily entry) ─
if (isset($_GET['ajax_date_rows'])) {
    header('Content-Type: application/json; charset=utf-8');
    $date_from = mysqli_real_escape_string($db, $_GET['date_from'] ?? '');
    $date_to   = mysqli_real_escape_string($db, $_GET['date_to']   ?? $date_from);
    $sel_dist  = $is_sadmin ? intval($_GET['selected_district'] ?? $user_district_id) : $user_district_id;
    echo json_encode((!empty($date_from) && $sel_dist > 0)
        ? load_nidhi_rows_range($sel_dist, $date_from, $date_to)
        : [], JSON_UNESCAPED_UNICODE);
    exit;
}

// ─── POST: Save static + today's daily ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['id'] ?? '') === 'save_nidhi') {
    $district_id      = $is_sadmin ? intval($_POST['district_id'] ?? $user_district_id) : $user_district_id;
    $user_district_id = $district_id;
    $entry_date       = $today; // always today — past is view-only

    $mandal_esc       = mysqli_real_escape_string($db, trim($_POST['mandal_name']        ?? ''));
    $opening_bal      = floatval($_POST['opening_balance']     ?? 0);
    $income_recv      = floatval($_POST['income_received']     ?? 0);
    $total_income     = $opening_bal + $income_recv; // col 6 auto
    $fd_amount        = floatval($_POST['fd_amount']           ?? 0);
    $fd_maturity      = mysqli_real_escape_string($db, trim($_POST['fd_maturity_period'] ?? ''));

    $m_sal  = floatval($_POST['month_salary_pension']   ?? 0);
    $m_com  = floatval($_POST['month_commission']       ?? 0);
    $m_veh  = floatval($_POST['month_vehicle_expense']  ?? 0);
    $m_oth  = floatval($_POST['month_other_expense']    ?? 0);
    $c_sal  = floatval($_POST['cumul_salary_pension']   ?? 0);
    $c_com  = floatval($_POST['cumul_commission']       ?? 0);
    $c_veh  = floatval($_POST['cumul_vehicle_expense']  ?? 0);
    $c_oth  = floatval($_POST['cumul_other_expense']    ?? 0);
    $bal_ac = floatval($_POST['balance_in_account']     ?? 0);

    $r1 = execute_query("INSERT INTO sangrah_nidhi_static
        (district_id, mandal_name, opening_balance, income_received, total_income,
         fd_amount, fd_maturity_period, updated_at, updated_by)
        VALUES ($district_id, '$mandal_esc', $opening_bal, $income_recv, $total_income,
                $fd_amount, '$fd_maturity', NOW(), $current_user_sno)
        ON DUPLICATE KEY UPDATE
            mandal_name        = VALUES(mandal_name),
            opening_balance    = VALUES(opening_balance),
            income_received    = VALUES(income_received),
            total_income       = VALUES(total_income),
            fd_amount          = VALUES(fd_amount),
            fd_maturity_period = VALUES(fd_maturity_period),
            updated_at         = NOW(),
            updated_by         = $current_user_sno");

    $r2 = execute_query("INSERT INTO sangrah_nidhi_daily
        (district_id, entry_date,
         month_salary_pension, month_commission, month_vehicle_expense, month_other_expense,
         cumul_salary_pension, cumul_commission, cumul_vehicle_expense, cumul_other_expense,
         balance_in_account)
        VALUES ($district_id, '$entry_date',
                $m_sal, $m_com, $m_veh, $m_oth,
                $c_sal, $c_com, $c_veh, $c_oth, $bal_ac)
        ON DUPLICATE KEY UPDATE
            month_salary_pension  = VALUES(month_salary_pension),
            month_commission      = VALUES(month_commission),
            month_vehicle_expense = VALUES(month_vehicle_expense),
            month_other_expense   = VALUES(month_other_expense),
            cumul_salary_pension  = VALUES(cumul_salary_pension),
            cumul_commission      = VALUES(cumul_commission),
            cumul_vehicle_expense = VALUES(cumul_vehicle_expense),
            cumul_other_expense   = VALUES(cumul_other_expense),
            balance_in_account    = VALUES(balance_in_account)");

    if ($r1 && $r2)
        $success = '<div class="alert alert-success">डेटा (' . date('d-m-Y') . ') सफलतापूर्वक सहेजा गया।</div>';
    else
        $msg = '<div class="alert alert-danger">डेटा सहेजने में त्रुटि हुई।</div>';
}

// Load current data for form pre-fill
$edit = load_nidhi_data($user_district_id, $today);

$audit_badge = '';
if (!empty($edit['updated_at'])) {
    $audit_badge = '<span class="badge badge-secondary ml-2">अंतिम अपडेट: '
        . date('d-m-Y H:i', strtotime($edit['updated_at']))
        . ' | यूजर ID: ' . intval($edit['updated_by'] ?? 0) . '</span>';
}

function v($key, $arr, $default = '') {
    return isset($arr[$key]) ? htmlspecialchars($arr[$key], ENT_QUOTES, 'UTF-8') : $default;
}
function vn($key, $arr, $default = '0.00') {
    return isset($arr[$key]) ? htmlspecialchars($arr[$key], ENT_QUOTES, 'UTF-8') : $default;
}

page_header_start();
?>
<style>
    .step h4  { color:#fff; background:#FF8E00; border-radius:15px; padding:10px 10px 6px 20px; }
    .step h5  { color:#000; background:#FFDB44; border-radius:15px; padding:10px 10px 6px 20px; }
    .info-box { background:#fff8e1; border-left:4px solid #FF8E00; padding:10px 15px; border-radius:5px; font-size:13px; color:#555; }
    .readonly-field { background:#f0f0f0 !important; font-weight:bold; cursor:not-allowed; }
    .calc-field     { background:#e8f4e8 !important; font-weight:bold; color:#155724; cursor:not-allowed; }
    .district-badge { background:#FF8E00; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
    .sadmin-badge   { background:#6f42c1; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
    /* View table */
    .prog-table                    { border:2px solid #FF8E00; border-collapse:collapse; width:100%; }
    .prog-table thead tr th        { background:#FF8E00 !important; color:#fff !important; text-align:center; vertical-align:middle; font-size:11px; white-space:nowrap; padding:6px 4px; border:1px solid #e07000; display:table-cell !important; }
    .prog-table thead tr th.grp-green  { background:#c8e6c9 !important; color:#1b5e20 !important; }
    .prog-table thead tr th.grp-blue   { background:#bbdefb !important; color:#0d47a1 !important; }
    .prog-table thead tr th.grp-pink   { background:#fce4ec !important; color:#880e4f !important; }
    .prog-table tbody tr td        { text-align:center; vertical-align:middle; font-size:12px; padding:5px 4px; border:1px solid #FF8E00; display:table-cell !important; }
    .prog-table tbody tr:hover td  { background:#fff3e0; }
    #district_loading { display:none; color:#FF8E00; font-size:13px; margin-top:4px; }
    #prog_loading     { display:none; color:#FF8E00; font-size:13px; }
</style>
<?php
page_header_end();
page_sidebar();
?>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="col-md-12">

          <div class="text-center mb-3">
            <strong>प्रारूप-6</strong><br>
            <h4><u>प्रदेश के जनपदों के सहकारी संग्रह निधि जिला लेखा खाते की आय एवं व्यय की सूचना</u></h4>
            <small>दिनांक 01.07.2025 से &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; तक &nbsp;&nbsp;&nbsp;&nbsp; (धनराशि लाख रु0 में)</small>
          </div>
          <?php echo $msg; echo $success; ?>

          <form action="" method="post" id="nidhi_form" accept-charset="UTF-8">
            <input type="hidden" name="id"          value="save_nidhi">
            <input type="hidden" name="district_id" id="form_district_id" value="<?php echo $user_district_id; ?>">

            <!-- SECTION 1: जनपद -->
            <div class="step">
              <marquee style="font-size:16px; color:red;">
                नोट: सहकारी संग्रह निधि जिला लेखा खाते का विवरण प्रतिदिन सही-सही भरें। धनराशि लाख रुपये में भरें।
              </marquee><br>
              <h4>1. जनपद एवं दिनांक</h4>
              <div class="col-sm-12">
                <div class="row align-items-center">
                  <div class="col-sm-4 form-group">
                    <label>जनपद <?php if($is_sadmin): ?><span class="sadmin-badge">सुपर एडमिन</span><?php endif; ?></label>
                    <?php if ($is_sadmin || $is_dr): ?>
                      <select id="sadmin_district_select" class="form-control" onchange="sadminDistrictChange(this.value);">
                        <?php foreach ($all_districts as $dist): ?>
                          <option value="<?php echo intval($dist['sno']); ?>"
                            <?php echo (intval($dist['sno']) === $user_district_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dist['district_name'], ENT_QUOTES, 'UTF-8'); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <small class="text-muted">जनपद बदलें — फॉर्म स्वतः अपडेट होगा।</small>
                      <div id="district_loading">⏳ डेटा लोड हो रहा है...</div>
                    <?php else: ?>
                      <div class="form-control readonly-field">
                        <?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?>
                      </div>
                    <?php endif; ?>
                  </div>
                  <div class="col-sm-3 form-group">
                    <label>दिनांक (आज)</label>
                    <input type="text" class="form-control readonly-field" value="<?php echo date('d-m-Y'); ?>" readonly>
                  </div>
                  <div class="col-sm-5 form-group">
                    <div class="info-box">
                      केवल आज का डेटा भरा/अपडेट किया जा सकता है।
                      पूर्व दिनांक का डेटा Section 3 में देखें।<br>
                      <?php echo $audit_badge; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SECTION 2: Static + Daily entry form -->
            <div class="step">
              <h4>2. आय एवं व्यय का विवरण
                (<span id="entry_district_name"><?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?></span>
                — <?php echo date('d-m-Y'); ?>)
              </h4>
              <div class="col-sm-12">

                <!-- Static: opening balance & income -->
                <h5>(I) स्थायी विवरण — प्रारंभिक अवशेष एवं आय (कॉ. 4, 5, 6)</h5>
                <div class="row">
                  <div class="col-sm-2 form-group">
                    <label>नाम मण्डल (कॉ.2)</label>
                    <input type="text" name="mandal_name" class="form-control" placeholder="मण्डल"
                           value="<?php echo v('mandal_name', $edit); ?>">
                  </div>
                  <div class="col-sm-3 form-group">
                    <label>दिनांक 01.07.2025 को प्रारंभिक अवशेष धनराशि (कॉ.4)</label>
                    <input type="number" step="0.01" min="0" name="opening_balance" id="opening_balance"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotalIncome()"
                           value="<?php echo vn('opening_balance', $edit); ?>">
                  </div>
                  <div class="col-sm-3 form-group">
                    <label>01.07.2025 से प्राप्त आय (कॉ.5)</label>
                    <input type="number" step="0.01" min="0" name="income_received" id="income_received"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotalIncome()"
                           value="<?php echo vn('income_received', $edit); ?>">
                  </div>
                  <div class="col-sm-3 form-group">
                    <label>कुल आय (कॉ.6) — स्वतः गणना (4+5)</label>
                    <input type="text" id="total_income_display" class="form-control calc-field" readonly
                           value="<?php
                             $oi = floatval($edit['opening_balance'] ?? 0);
                             $ir = floatval($edit['income_received'] ?? 0);
                             echo number_format($oi + $ir, 2);
                           ?>">
                  </div>
                </div>

                <!-- Daily: monthly expenditure -->
                <h5>(II) मासिक व्यय का विवरण — <?php echo date('d-m-Y'); ?> (कॉ. 7–11)</h5>
                <div class="row">
                  <div class="col-sm-2 form-group">
                    <label>वेतन/पेंशन पर व्यय (कॉ.7)</label>
                    <input type="number" step="0.01" min="0" name="month_salary_pension" id="m_sal"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('month_salary_pension', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>कमीशन भुगतान (कॉ.8)</label>
                    <input type="number" step="0.01" min="0" name="month_commission" id="m_com"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('month_commission', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>वाहन पर व्यय (कॉ.9)</label>
                    <input type="number" step="0.01" min="0" name="month_vehicle_expense" id="m_veh"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('month_vehicle_expense', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>अन्य व्यय मदवार (कॉ.10)</label>
                    <input type="number" step="0.01" min="0" name="month_other_expense" id="m_oth"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('month_other_expense', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>मासिक योग (कॉ.11) — स्वतः</label>
                    <input type="text" id="month_total_display" class="form-control calc-field" readonly
                           value="<?php
                             echo number_format(
                               floatval($edit['month_salary_pension'] ?? 0) +
                               floatval($edit['month_commission'] ?? 0) +
                               floatval($edit['month_vehicle_expense'] ?? 0) +
                               floatval($edit['month_other_expense'] ?? 0), 2);
                           ?>">
                  </div>
                </div>

                <!-- Daily: cumulative expenditure -->
                <h5>(III) क्रमिक व्यय का विवरण (कॉ. 12–16)</h5>
                <div class="row">
                  <div class="col-sm-2 form-group">
                    <label>वेतन/पेंशन पर व्यय (कॉ.12)</label>
                    <input type="number" step="0.01" min="0" name="cumul_salary_pension" id="c_sal"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('cumul_salary_pension', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>कमीशन भुगतान (कॉ.13)</label>
                    <input type="number" step="0.01" min="0" name="cumul_commission" id="c_com"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('cumul_commission', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>वाहन पर व्यय (कॉ.14)</label>
                    <input type="number" step="0.01" min="0" name="cumul_vehicle_expense" id="c_veh"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('cumul_vehicle_expense', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>अन्य व्यय मदवार (कॉ.15)</label>
                    <input type="number" step="0.01" min="0" name="cumul_other_expense" id="c_oth"
                           class="form-control" placeholder="0.00"
                           oninput="calcTotals()"
                           value="<?php echo vn('cumul_other_expense', $edit); ?>">
                  </div>
                  <div class="col-sm-2 form-group">
                    <label>क्रमिक योग (कॉ.16) — स्वतः</label>
                    <input type="text" id="cumul_total_display" class="form-control calc-field" readonly
                           value="<?php
                             echo number_format(
                               floatval($edit['cumul_salary_pension'] ?? 0) +
                               floatval($edit['cumul_commission'] ?? 0) +
                               floatval($edit['cumul_vehicle_expense'] ?? 0) +
                               floatval($edit['cumul_other_expense'] ?? 0), 2);
                           ?>">
                  </div>
                </div>

                <!-- Balance + FD -->
                <h5>(IV) अवशेष एवं एफ0डी0 विवरण (कॉ. 17–19)</h5>
                <div class="row">
                  <div class="col-sm-3 form-group">
                    <label>खाते में जमा क्रमिक अवशेष धनराशि (कॉ.17)</label>
                    <input type="number" step="0.01" min="0" name="balance_in_account"
                           class="form-control" placeholder="0.00"
                           value="<?php echo vn('balance_in_account', $edit); ?>">
                  </div>
                  <div class="col-sm-3 form-group">
                    <label>एफ0डी0 की धनराशि (कॉ.18)</label>
                    <input type="number" step="0.01" min="0" name="fd_amount"
                           class="form-control" placeholder="0.00"
                           value="<?php echo vn('fd_amount', $edit); ?>">
                  </div>
                  <div class="col-sm-3 form-group">
                    <label>परिपक्वता अवधि (कॉ.19)</label>
                    <input type="text" name="fd_maturity_period"
                           class="form-control" placeholder="जैसे: 01-07-2025 से 30-06-2026"
                           value="<?php echo v('fd_maturity_period', $edit); ?>">
                  </div>
                </div>

                <div class="row mt-2 mb-3">
                  <div class="col-sm-12">
                    <button type="submit" class="btn btn-danger btn-lg">💾 डेटा सहेजें</button>
                  </div>
                </div>
              </div>
            </div>

          </form>

          <!-- SECTION 3: Date-wise read-only view -->
          <div class="step">
            <h4>3. दिनांकवार डेटा देखें
              (<span id="view_district_name"><?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?></span>)
            </h4>
            <div class="col-sm-12">
              <div class="row align-items-end mb-3">
                <div class="col-sm-3 form-group">
                  <label>प्रारम्भ दिनांक</label>
                  <input type="date" id="date_from" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                  <small class="text-muted">प्रारम्भ दिनांक</small>
                </div>
                <div class="col-sm-3 form-group">
                  <label>समाप्ति दिनांक</label>
                  <input type="date" id="date_to" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                  <small class="text-muted">समाप्ति दिनांक</small>
                </div>
                <div class="col-sm-4 form-group">
                  <button type="button" class="btn btn-warning btn-lg" onclick="loadProgTable();">
                    डेटा देखें
                  </button>
                  <span id="prog_loading" class="ml-2">⏳ लोड हो रहा है...</span>
                </div>
              </div>

              <div id="prog_section">
                <h6 id="prog_title" style="font-weight:bold; color:#333; margin-bottom:8px;"></h6>
                <div class="table-responsive">
                  <table class="table-bordered prog-table">
                    <thead>
                      <tr>
                        <th rowspan="2">क्र0</th>
                        <th rowspan="2">दिनांक</th>
                        <th rowspan="2">नाम मण्डल</th>
                        <th rowspan="2">नाम जनपद</th>
                        <th rowspan="2">प्रारंभिक अवशेष<br>(कॉ.4)</th>
                        <th rowspan="2">प्राप्त आय<br>(कॉ.5)</th>
                        <th rowspan="2">कुल आय<br>(कॉ.6)</th>
                        <th colspan="5" class="grp-green">सहकारी संग्रह निधि लेखा खाते का मासिक व्यय</th>
                        <th colspan="5" class="grp-blue">सहकारी संग्रह निधि लेखा खाते का क्रमिक व्यय</th>
                        <th rowspan="2">खाते में जमा<br>क्रमिक अवशेष<br>(कॉ.17)</th>
                        <th colspan="2">एफ0डी0<br>धनराशि</th>
                        <th rowspan="2">अंतिम<br>अपडेट</th>
                        <th rowspan="2">स्थिति</th>
                        <th rowspan="2">DR टिप्पणी</th>
                        <th rowspan="2" id="nidhi_action_col_head" style="display:none;">कार्यवाही</th>
                      </tr>
                      <tr>
                        <th class="grp-green">वेतन/पेंशन<br>(कॉ.7)</th>
                        <th class="grp-green">कमीशन<br>(कॉ.8)</th>
                        <th class="grp-green">वाहन व्यय<br>(कॉ.9)</th>
                        <th class="grp-green">अन्य व्यय<br>(कॉ.10)</th>
                        <th class="grp-green">योग<br>(कॉ.11)</th>
                        <th class="grp-blue">वेतन/पेंशन<br>(कॉ.12)</th>
                        <th class="grp-blue">कमीशन<br>(कॉ.13)</th>
                        <th class="grp-blue">वाहन व्यय<br>(कॉ.14)</th>
                        <th class="grp-blue">अन्य व्यय<br>(कॉ.15)</th>
                        <th class="grp-blue">योग<br>(कॉ.16)</th>
                        <th class="test">धनराशि<br>(कॉ.18)</th>
                        <th class="test">परिपक्वता<br>अवधि<br>(कॉ.19)</th>
                      </tr>
                    </thead>
                    <tbody id="prog_tbody"></tbody>
                  </table>
                </div>
                <div id="prog_nodata" class="text-danger font-weight-bold" style="display:none;">
                  चुने गए दिनांक के लिए कोई डेटा उपलब्ध नहीं है।
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
    var isSadmin          = <?php echo ($is_sadmin || $is_dr) ? 'true' : 'false'; ?>;
    var isDr              = <?php echo $is_dr ? 'true' : 'false'; ?>;
    var fixedDistrictName = "<?php echo addslashes(htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8')); ?>";
    var currentDistrictId = <?php echo $user_district_id; ?>;

    // ── Auto-calculate totals ─────────────────────────────────────────────────
    function gv(id) { var el=document.getElementById(id); return el ? (parseFloat(el.value)||0) : 0; }

    function calcTotalIncome() {
        var t = gv('opening_balance') + gv('income_received');
        document.getElementById('total_income_display').value = t.toFixed(2);
    }

    function calcTotals() {
        var mt = gv('m_sal') + gv('m_com') + gv('m_veh') + gv('m_oth');
        var ct = gv('c_sal') + gv('c_com') + gv('c_veh') + gv('c_oth');
        document.getElementById('month_total_display').value = mt.toFixed(2);
        document.getElementById('cumul_total_display').value = ct.toFixed(2);
    }

    // ── sadmin district change ────────────────────────────────────────────────
    function sadminDistrictChange(districtId) {
        if (!isSadmin || !districtId) return;
        currentDistrictId = parseInt(districtId);
        document.getElementById('form_district_id').value = districtId;
        document.getElementById('district_loading').style.display = 'block';

        var sel = document.getElementById('sadmin_district_select');
        fixedDistrictName = sel.options[sel.selectedIndex].text;
        var edn = document.getElementById('entry_district_name');
        if (edn) edn.textContent = fixedDistrictName;
        var vdn = document.getElementById('view_district_name');
        if (vdn) vdn.textContent = fixedDistrictName;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?ajax_district_data=1&selected_district=' + districtId, true);
        xhr.onload = function () {
            document.getElementById('district_loading').style.display = 'none';
            if (xhr.status !== 200) return;
            try {
                var d = JSON.parse(xhr.responseText);
                function sf(id, val) { var el=document.getElementById(id); if(el) el.value = val||''; }

                sf('opening_balance',        d.opening_balance);
                sf('income_received',        d.income_received);
                sf('m_sal',  d.month_salary_pension);
                sf('m_com',  d.month_commission);
                sf('m_veh',  d.month_vehicle_expense);
                sf('m_oth',  d.month_other_expense);
                sf('c_sal',  d.cumul_salary_pension);
                sf('c_com',  d.cumul_commission);
                sf('c_veh',  d.cumul_vehicle_expense);
                sf('c_oth',  d.cumul_other_expense);

                var bal = document.querySelector('[name="balance_in_account"]');
                if (bal) bal.value = d.balance_in_account || '';
                var fd  = document.querySelector('[name="fd_amount"]');
                if (fd)  fd.value  = d.fd_amount || '';
                var fdm = document.querySelector('[name="fd_maturity_period"]');
                if (fdm) fdm.value = d.fd_maturity_period || '';
                var mn  = document.querySelector('[name="mandal_name"]');
                if (mn)  mn.value  = d.mandal_name || '';

                calcTotalIncome();
                calcTotals();
                document.getElementById('prog_tbody').innerHTML = '';
                document.getElementById('prog_nodata').style.display = 'none';
            } catch(e) { console.error(e); }
        };
        xhr.send();
    }

    // ── Section 3: read-only date view ───────────────────────────────────────
   function loadProgTable() {
        var dateFrom = document.getElementById('date_from').value;
        var dateTo   = document.getElementById('date_to').value;
        if (!dateFrom || !dateTo) { alert('कृपया प्रारम्भ और समाप्ति दिनांक चुनें।'); return; }
        if (dateFrom > dateTo) { alert('प्रारम्भ दिनांक, समाप्ति दिनांक से पहले होनी चाहिए।'); return; }
        document.getElementById('prog_loading').style.display = 'inline';

        var url = '?ajax_date_rows=1&date_from=' + dateFrom + '&date_to=' + dateTo;
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
                var actionHead = document.getElementById('nidhi_action_col_head');

                tbody.innerHTML      = '';
                nodata.style.display = 'none';

                if (actionHead) actionHead.style.display = isDr ? 'table-cell' : 'none';

                var f = dateFrom.split('-'), t = dateTo.split('-');
                title.textContent = fixedDistrictName + ' — दिनांक: ' + f[2]+'-'+f[1]+'-'+f[0] + ' से ' + t[2]+'-'+t[1]+'-'+t[0];

                if (!rows || rows.length === 0) {
                    nodata.style.display = 'block';
                    return;
                }

                rows.forEach(function(r, i) {
                    var d  = function(v) { return (v !== null && v !== undefined && v !== '') ? v : '-'; };
                    var f  = function(v) { return parseFloat(v||0).toFixed(2); };
                    var mt = (parseFloat(r.month_salary_pension||0) + parseFloat(r.month_commission||0) +
                              parseFloat(r.month_vehicle_expense||0) + parseFloat(r.month_other_expense||0)).toFixed(2);
                    var ct = (parseFloat(r.cumul_salary_pension||0) + parseFloat(r.cumul_commission||0) +
                              parseFloat(r.cumul_vehicle_expense||0) + parseFloat(r.cumul_other_expense||0)).toFixed(2);
                    var ti = (parseFloat(r.opening_balance||0) + parseFloat(r.income_received||0)).toFixed(2);

                    var st = r.status || 'pending';
                    var stBadge = st === 'approved'
                        ? '<span class="badge badge-success">स्वीकृत</span>'
                        : (st === 'rejected'
                            ? '<span class="badge badge-danger">अस्वीकृत</span>'
                            : '<span class="badge badge-warning">लंबित</span>');

                    var remarkDisp = r.dr_remark ? r.dr_remark : '-';
                    var distId     = r.c_district_id || currentDistrictId;

                    var actionCell = '';
                    if (isDr) {
                        actionCell =
                            '<td>' +
                            '<textarea id="nidhi_remark_' + distId + '" class="form-control form-control-sm mb-1" rows="1" placeholder="टिप्पणी (वैकल्पिक)" style="min-width:140px;">' + (r.dr_remark || '') + '</textarea>' +
                            '<button class="btn btn-success btn-sm mr-1" onclick="submitNidhiReview(' + distId + ',\'' + dateFrom + '\',\'' + dateTo + '\',\'approved\')">✔ स्वीकृत</button>' +
                            '<button class="btn btn-danger btn-sm" onclick="submitNidhiReview(' + distId + ',\'' + dateFrom + '\',\'' + dateTo + '\',\'rejected\')">✘ अस्वीकृत</button>' +
                            '</td>';
                    }

                    var ed = (r.entry_date || '').split('-');
                    var displayDate = ed.length === 3 ? ed[2]+'-'+ed[1]+'-'+ed[0] : (r.entry_date || '-');
                    tbody.innerHTML +=
                        '<tr>' +
                        '<td>' + (i+1) + '</td>' +
                        '<td>' + displayDate + '</td>' +
                        '<td>' + d(r.mandal_name) + '</td>' +
                        '<td>' + d(r.district_name) + '</td>' +
                        '<td>' + f(r.opening_balance) + '</td>' +
                        '<td>' + f(r.income_received) + '</td>' +
                        '<td><strong>' + ti + '</strong></td>' +
                        '<td>' + f(r.month_salary_pension)  + '</td>' +
                        '<td>' + f(r.month_commission)      + '</td>' +
                        '<td>' + f(r.month_vehicle_expense) + '</td>' +
                        '<td>' + f(r.month_other_expense)   + '</td>' +
                        '<td><strong>' + mt + '</strong></td>' +
                        '<td>' + f(r.cumul_salary_pension)  + '</td>' +
                        '<td>' + f(r.cumul_commission)      + '</td>' +
                        '<td>' + f(r.cumul_vehicle_expense) + '</td>' +
                        '<td>' + f(r.cumul_other_expense)   + '</td>' +
                        '<td><strong>' + ct + '</strong></td>' +
                        '<td>' + f(r.balance_in_account) + '</td>' +
                        '<td>' + f(r.fd_amount) + '</td>' +
                        '<td>' + d(r.fd_maturity_period) + '</td>' +
                        '<td>' + d(r.updated_at) + '</td>' +
                        '<td>' + stBadge + '</td>' +
                        '<td>' + remarkDisp + '</td>' +
                        (isDr ? actionCell : '') +
                        '</tr>';
                });
            } catch(e) { console.error(e); }
        };
        xhr.send();
    }

    function submitNidhiReview(districtId, dateFrom, dateTo, status) {
        var remark = '';
        var el = document.getElementById('nidhi_remark_' + districtId);
        if (el) remark = el.value.trim();

        if (status === 'rejected' && !remark) {
            alert('अस्वीकृत करने पर टिप्पणी अनिवार्य है।');
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            try {
                var res = JSON.parse(xhr.responseText);
                alert(res.msg);
                if (res.ok) loadProgTable();
            } catch (e) { alert('त्रुटि हुई।'); }
        };
        var body = 'id=dr_review'
            + '&district_id=' + districtId
            + '&date_from='   + encodeURIComponent(dateFrom)
            + '&date_to='     + encodeURIComponent(dateTo)
            + '&status='      + status
            + '&dr_remark='   + encodeURIComponent(remark);
        xhr.send(body);
    }

    document.addEventListener('DOMContentLoaded', function () {
        calcTotalIncome();
        calcTotals();
    });
</script>

<?php
page_footer_start();
page_footer_end();
?>
