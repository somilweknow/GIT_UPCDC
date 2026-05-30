<?php
include("scripts/settings.php");

$is_sadmin = (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'sadmin');
$is_dr = (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'ar_dr');

if (!isset($_SESSION['usersno']) || empty($_SESSION['usersno'])) {
    header('Location: index.php');
    exit;
}

$current_user_sno = intval($_SESSION['usersno']);
$session_districts = isset($_SESSION['district_id']) && is_array($_SESSION['district_id'])
    ? array_map('intval', $_SESSION['district_id'])
    : [];

if ($is_sadmin) {
    $all_districts = [];
    $res_all = execute_query("SELECT sno, district_name FROM master_district ORDER BY district_name");
    while ($row = mysqli_fetch_assoc($res_all)) {
        $all_districts[] = $row;
    }
    if (isset($_GET['selected_district']) && intval($_GET['selected_district']) > 0) {
        $user_district_id = intval($_GET['selected_district']);
    } elseif (isset($_POST['district_id']) && intval($_POST['district_id']) > 0) {
        $user_district_id = intval($_POST['district_id']);
    } else {
        $user_district_id = !empty($all_districts) ? intval($all_districts[0]['sno']) : 0;
    }
} else {
    if ($is_dr) {
        $all_districts = [];
        $res_all = execute_query("SELECT md.sno, md.district_name FROM master_district md 
            WHERE md.division_id = (SELECT division_name FROM ar_dr WHERE sno = " . intval($_SESSION['usersno']) . ") 
            ORDER BY md.district_name");
        while ($row = mysqli_fetch_assoc($res_all)) {
            $all_districts[] = $row;
        }
        if (isset($_GET['selected_district']) && intval($_GET['selected_district']) > 0) {
            $user_district_id = intval($_GET['selected_district']);
        } elseif (isset($_POST['district_id']) && intval($_POST['district_id']) > 0) {
            $user_district_id = intval($_POST['district_id']);
        } else {
            $user_district_id = !empty($all_districts) ? intval($all_districts[0]['sno']) : 0;
        }
        if ($user_district_id <= 0) {
            die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
        }
    } else {
        $user_district_id = !empty($session_districts) ? $session_districts[0] : 0;
        if ($user_district_id <= 0) {
            die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
        }
    }
}

$res_dn = execute_query("SELECT district_name FROM master_district WHERE sno = $user_district_id");
$user_district_name = (mysqli_num_rows($res_dn) > 0) ? mysqli_fetch_assoc($res_dn)['district_name'] : '';
$today = date('Y-m-d');
$report_date = isset($_REQUEST['report_date']) && $_REQUEST['report_date'] !== '' ? $_REQUEST['report_date'] : $today;
$msg = '';
$success = '';

function h($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

function load_praroop6_rows($district_id, $date_from, $date_to = null)
{
    $district_id = intval($district_id);
    $date_from = mysqli_real_escape_string($GLOBALS['db'], $date_from);
    if (!$date_to)
        $date_to = $date_from;
    $date_to = mysqli_real_escape_string($GLOBALS['db'], $date_to);
    $rows = [];
    $res = execute_query("
        SELECT *, MAX(status) as row_status, MAX(dr_remark) as row_dr_remark,
               MAX(reviewed_at) as row_reviewed_at
        FROM sangrah_praroop6
        WHERE district_id = $district_id
          AND report_date BETWEEN '$date_from' AND '$date_to'
        GROUP BY mandal_name, report_date, sno
        ORDER BY report_date, mandal_name, sno
    ");
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }
    return $rows;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['id'] ?? '') === 'save_praroop6') {
    $district_id = $is_sadmin ? intval($_POST['district_id'] ?? $user_district_id) : $user_district_id;
    $user_district_id = $district_id;

    $report_date = mysqli_real_escape_string($db, $_POST['report_date'] ?? $today);
    $period_start = mysqli_real_escape_string($db, $_POST['period_start'] ?? '2025-07-01');
    $period_end_i = trim($_POST['period_end'] ?? '');
    $period_end = ($period_end_i !== '') ? "'" . mysqli_real_escape_string($db, $period_end_i) . "'" : "NULL";
    $mandal_name = mysqli_real_escape_string($db, trim($_POST['mandal_name'] ?? ''));

    $opening_balance = floatval($_POST['opening_balance'] ?? 0);
    $income_received = floatval($_POST['income_received'] ?? 0);
    $total_income = floatval($_POST['total_income'] ?? ($opening_balance + $income_received));
    $me_salary_pension = floatval($_POST['me_salary_pension'] ?? 0);
    $me_commission = floatval($_POST['me_commission'] ?? 0);
    $me_vehicle = floatval($_POST['me_vehicle'] ?? 0);
    $me_other = floatval($_POST['me_other'] ?? 0);
    $me_monthly_total = floatval($_POST['me_monthly_total'] ?? ($me_salary_pension + $me_commission + $me_vehicle + $me_other));
    $ce_salary_pension = floatval($_POST['ce_salary_pension'] ?? 0);
    $ce_commission = floatval($_POST['ce_commission'] ?? 0);
    $ce_vehicle = floatval($_POST['ce_vehicle'] ?? 0);
    $ce_other = floatval($_POST['ce_other'] ?? 0);
    $ce_total = floatval($_POST['ce_total'] ?? ($ce_salary_pension + $ce_commission + $ce_vehicle + $ce_other));
    $cumulative_expense_ytd = floatval($_POST['cumulative_expense_ytd'] ?? $ce_total);
    $closing_balance = floatval($_POST['closing_balance'] ?? ($total_income - $cumulative_expense_ytd));
    $fd_amount = floatval($_POST['fd_amount'] ?? 0);
    $fd_maturity = mysqli_real_escape_string($db, trim($_POST['fd_maturity'] ?? ''));

    if ($mandal_name === '') {
        $msg = '<div class="alert alert-danger">नाम मण्डल आवश्यक है।</div>';
    } else {
        $sql = "
            INSERT INTO sangrah_praroop6
            (district_id, mandal_name, period_start, period_end, report_date,
             opening_balance, income_received, total_income,
             me_salary_pension, me_commission, me_vehicle, me_other, me_monthly_total,
             ce_salary_pension, ce_commission, ce_vehicle, ce_other, ce_total,
             cumulative_expense_ytd, closing_balance, fd_amount, fd_maturity, updated_at, updated_by)
            VALUES
            ($district_id, '$mandal_name', '$period_start', $period_end, '$report_date',
             $opening_balance, $income_received, $total_income,
             $me_salary_pension, $me_commission, $me_vehicle, $me_other, $me_monthly_total,
             $ce_salary_pension, $ce_commission, $ce_vehicle, $ce_other, $ce_total,
             $cumulative_expense_ytd, $closing_balance, $fd_amount, '$fd_maturity', NOW(), $current_user_sno)
            ON DUPLICATE KEY UPDATE
             period_start = VALUES(period_start),
             period_end = VALUES(period_end),
             opening_balance = VALUES(opening_balance),
             income_received = VALUES(income_received),
             total_income = VALUES(total_income),
             me_salary_pension = VALUES(me_salary_pension),
             me_commission = VALUES(me_commission),
             me_vehicle = VALUES(me_vehicle),
             me_other = VALUES(me_other),
             me_monthly_total = VALUES(me_monthly_total),
             ce_salary_pension = VALUES(ce_salary_pension),
             ce_commission = VALUES(ce_commission),
             ce_vehicle = VALUES(ce_vehicle),
             ce_other = VALUES(ce_other),
             ce_total = VALUES(ce_total),
             cumulative_expense_ytd = VALUES(cumulative_expense_ytd),
             closing_balance = VALUES(closing_balance),
             fd_amount = VALUES(fd_amount),
             fd_maturity = VALUES(fd_maturity),
             updated_at = NOW(),
             updated_by = $current_user_sno
        ";
        if (execute_query($sql)) {
            $success = '<div class="alert alert-success">प्रारूप-6 डेटा सफलतापूर्वक सहेजा गया।</div>';
        } else {
            $msg = '<div class="alert alert-danger">डेटा सहेजने में त्रुटि हुई।</div>';
        }
    }
}

if (isset($_POST['id']) && $_POST['id'] === 'dr_review') {
    header('Content-Type: application/json; charset=utf-8');
    $rev_district = intval($_POST['district_id'] ?? 0);
    $rev_date_from = mysqli_real_escape_string($db, $_POST['date_from'] ?? '');
    $rev_date_to = mysqli_real_escape_string($db, $_POST['date_to'] ?? $rev_date_from);
    $rev_status = in_array($_POST['status'], ['approved', 'rejected']) ? $_POST['status'] : '';
    $rev_remark = mysqli_real_escape_string($db, trim($_POST['dr_remark'] ?? ''));
    if (!$is_dr) {
        echo json_encode(['ok' => false, 'msg' => 'केवल DR अनुमोदन कर सकते हैं।'], JSON_UNESCAPED_UNICODE);
        exit;
    }

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
    $r = execute_query("UPDATE sangrah_praroop6
        SET status = '$rev_status', dr_remark = '$rev_remark',
            reviewed_by = $current_user_sno, reviewed_at = NOW()
        WHERE district_id = $rev_district AND report_date BETWEEN '$rev_date_from' AND '$rev_date_to'");
    if ($r)
        echo json_encode(['ok' => true, 'msg' => ($rev_status === 'approved' ? 'स्वीकृत' : 'अस्वीकृत') . ' किया गया।'], JSON_UNESCAPED_UNICODE);
    else
        echo json_encode(['ok' => false, 'msg' => 'अपडेट विफल।'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($is_sadmin && isset($_GET['ajax_district_data'])) {
    header('Content-Type: application/json; charset=utf-8');
    $sel_dist = intval($_GET['selected_district'] ?? 0);
    $date_from = $_GET['date_from'] ?? $today;
    $date_to = $_GET['date_to'] ?? $date_from;
    echo json_encode($sel_dist > 0 ? load_praroop6_rows($sel_dist, $date_from, $date_to) : [], JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_GET['ajax_date_rows'])) {
    header('Content-Type: application/json; charset=utf-8');
    $date_from = mysqli_real_escape_string($db, $_GET['date_from'] ?? $today);
    $date_to = mysqli_real_escape_string($db, $_GET['date_to'] ?? $date_from);
    $sel_dist = $is_sadmin ? intval($_GET['selected_district'] ?? $user_district_id) : $user_district_id;
    echo json_encode(($sel_dist > 0) ? load_praroop6_rows($sel_dist, $date_from, $date_to) : [], JSON_UNESCAPED_UNICODE);
    exit;
}

$date_from = isset($_REQUEST['date_from']) && $_REQUEST['date_from'] !== '' ? $_REQUEST['date_from'] : $today;
$date_to = isset($_REQUEST['date_to']) && $_REQUEST['date_to'] !== '' ? $_REQUEST['date_to'] : $today;
$rows = load_praroop6_rows($user_district_id, $date_from, $date_to);
page_header_start();
?>
<style>
    .readonly-field {
        background: #f0f0f0 !important;
        font-weight: bold;
        cursor: not-allowed;
    }

    .sadmin-badge {
        background: #6f42c1;
        color: #fff;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 13px;
        font-weight: bold;
        margin-left: 8px;
    }

    .step h4 {
        color: #fff;
        background: #FF8E00;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
    }

    .praroop6-wrap {
        font-family: 'Arial Unicode MS', sans-serif;
    }

    .praroop6-title {
        text-align: center;
        line-height: 1.6;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .praroop6-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    .praroop6-table th,
    .praroop6-table td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
        font-size: 12px;
        vertical-align: middle;
    }

    .praroop6-table .header-main {
        font-weight: bold;
        background-color: #f2f2f2;
    }

    .praroop6-col-no td {
        background: #eee;
        font-weight: bold;
    }

    .praroop6-footer {
        margin-top: 15px;
        font-size: 11px;
        text-align: left;
    }

    #district_loading,
    #table_loading {
        display: none;
        color: #FF8E00;
        font-size: 13px;
    }
</style>
<?php
page_header_end();
page_sidebar();
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body praroop6-wrap">
                <?php echo $msg;
                echo $success; ?>

                <div class="step">
                    <h4>1. जनपद एवं दिनांक चुनें</h4>
                    <form method="get" action="">
                        <div class="row align-items-end">
                            <div class="col-sm-4 form-group">
                                <label>जनपद <?php if ($is_sadmin): ?><span class="sadmin-badge">सुपर
                                            एडमिन</span><?php endif; ?></label>
                                <?php if ($is_sadmin || $is_dr): ?>
                                    <select id="sadmin_district_select" name="selected_district" class="form-control">
                                        <?php foreach ($all_districts as $dist): ?>
                                            <option value="<?php echo intval($dist['sno']); ?>" <?php echo (intval($dist['sno']) === $user_district_id) ? 'selected' : ''; ?>>
                                                <?php echo h($dist['district_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div id="district_loading">⏳ डेटा लोड हो रहा है...</div>
                                <?php else: ?>
                                    <div class="form-control readonly-field"><?php echo h($user_district_name); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>रिपोर्ट दिनांक</label>
                                <input type="date" id="report_date_filter" name="report_date" class="form-control"
                                    value="<?php echo h($report_date); ?>">
                            </div>
                            <div class="col-sm-2 form-group">
                                <button type="submit" class="btn btn-warning">देखें</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="step">
                    <h4>2. प्रारूप-6 डेटा प्रविष्टि</h4>
                    <form method="post" action="" id="praroop6_form">
                        <input type="hidden" name="id" value="save_praroop6">
                        <input type="hidden" name="district_id" value="<?php echo intval($user_district_id); ?>">
                        <div class="row">
                            <div class="col-sm-3 form-group">
                                <label>नाम मण्डल <span style="color:red">*</span></label>
                                <input type="text" name="mandal_name" class="form-control" required>
                            </div>
                            <div class="col-sm-2 form-group">
                                <label>रिपोर्ट दिनांक</label>
                                <input type="date" name="report_date" class="form-control"
                                    value="<?php echo h($report_date); ?>" required>
                            </div>
                            <div class="col-sm-2 form-group">
                                <label>प्रारम्भ तिथि</label>
                                <input type="date" name="period_start" class="form-control" value="2025-07-01" required>
                            </div>
                            <div class="col-sm-2 form-group">
                                <label>समाप्ति तिथि</label>
                                <input type="date" name="period_end" class="form-control"
                                    value="<?php echo h($report_date); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 form-group"><label>प्रारम्भिक अवशेष (4)</label><input type="number"
                                    step="0.01" name="opening_balance" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>प्राप्त आय (5)</label><input type="number"
                                    step="0.01" name="income_received" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>कुल आय (6)</label><input type="number" step="0.01"
                                    name="total_income" class="form-control" value="0"></div>
                            <div class="col-sm-2 form-group"><label>मासिक वेतन/पेंशन (7)</label><input type="number"
                                    step="0.01" name="me_salary_pension" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>मासिक कमीशन (8)</label><input type="number"
                                    step="0.01" name="me_commission" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>मासिक वाहन (9)</label><input type="number"
                                    step="0.01" name="me_vehicle" class="form-control calc" value="0"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 form-group"><label>मासिक अन्य (10)</label><input type="number"
                                    step="0.01" name="me_other" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>मासिक योग (11)</label><input type="number"
                                    step="0.01" name="me_monthly_total" class="form-control" value="0"></div>
                            <div class="col-sm-2 form-group"><label>क्रमिक वेतन/पेंशन (12)</label><input type="number"
                                    step="0.01" name="ce_salary_pension" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>क्रमिक कमीशन (13)</label><input type="number"
                                    step="0.01" name="ce_commission" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>क्रमिक वाहन (14)</label><input type="number"
                                    step="0.01" name="ce_vehicle" class="form-control calc" value="0"></div>
                            <div class="col-sm-2 form-group"><label>क्रमिक अन्य (15)</label><input type="number"
                                    step="0.01" name="ce_other" class="form-control calc" value="0"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 form-group"><label>क्रमिक कुल योग (16)</label><input type="number"
                                    step="0.01" name="ce_total" class="form-control" value="0"></div>
                            <div class="col-sm-2 form-group"><label>कुल व्यय क्रमिक योग (17)</label><input type="number"
                                    step="0.01" name="cumulative_expense_ytd" class="form-control" value="0"></div>
                            <div class="col-sm-2 form-group"><label>अवशेष धनराशि (18)</label><input type="number"
                                    step="0.01" name="closing_balance" class="form-control" value="0"></div>
                            <div class="col-sm-2 form-group"><label>एफ.डी. धनराशि (19)</label><input type="number"
                                    step="0.01" name="fd_amount" class="form-control" value="0"></div>
                            <div class="col-sm-4 form-group"><label>परिपक्वता अवधि</label><input type="text"
                                    name="fd_maturity" class="form-control" placeholder="उदा. 12 माह / 31-03-2027">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger">💾 सहेजें</button>
                    </form>
                </div>

                <div class="step">
                    <h4>3. दिनांकवार डेटा देखें (<span id="view_district_name">
                            <?php echo h($user_district_name); ?>
                        </span>)</h4>
                    <div class="row align-items-end mb-2">
                        <div class="col-sm-3 form-group">
                            <label>दिनांक चुनें</label>
                            <input type="date" id="date_from" class="form-control"
                                value="<?php echo h($report_date); ?>">
                            <small class="text-muted">प्रारम्भ दिनांक</small>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label>समाप्ति दिनांक</label>
                            <input type="date" id="date_to" class="form-control" value="<?php echo h($report_date); ?>">
                            <small class="text-muted">समाप्ति दिनांक</small>
                        </div>
                        <div class="col-sm-3 form-group" style="padding-top:32px;">
                            <button type="button" class="btn btn-warning btn-lg" onclick="loadPastSection();">डेटा
                                देखें</button>
                        </div>
                    </div>
                    <h6 id="past_section_title" style="margin:8px 0; font-weight:bold; color:#333;"></h6>
                    <div class="table-responsive">
                        <table class="praroop6-table">
                            <thead>
                                <tr class="header-main">
                                    <th rowspan="3">क्र० सं०</th>
                                    <th rowspan="3">नाम मण्डल</th>
                                    <th rowspan="3">नाम जनपद</th>
                                    <th rowspan="3">प्रारम्भिक अवशेष</th>
                                    <th rowspan="3">प्राप्त आय</th>
                                    <th rowspan="3">कुल आय</th>
                                    <th colspan="5">मासिक व्यय</th>
                                    <th colspan="5">क्रमिक व्यय</th>
                                    <th rowspan="3">कुल व्यय क्रमिक योग</th>
                                    <th rowspan="3">अवशेष धनराशि</th>
                                    <th colspan="2">एफ०डी०</th>
                                    <th rowspan="3">स्थिति</th>
                                    <th rowspan="3">DR टिप्पणी</th>
                                    <th rowspan="3" id="past_action_col_head" style="display:none;">कार्यवाही</th>
                                </tr>
                                <tr class="header-main">
                                    <th rowspan="2">वेतन/पेंशन</th>
                                    <th rowspan="2">कमीशन</th>
                                    <th rowspan="2">वाहन</th>
                                    <th rowspan="2">अन्य</th>
                                    <th rowspan="2">मासिक योग</th>
                                    <th rowspan="2">वेतन/पेंशन</th>
                                    <th rowspan="2">कमीशन</th>
                                    <th rowspan="2">वाहन</th>
                                    <th rowspan="2">अन्य</th>
                                    <th rowspan="2">कुल योग</th>
                                    <th rowspan="2">धनराशि</th>
                                    <th rowspan="2">परिपक्वता</th>
                                </tr>
                                <tr></tr>
                            </thead>
                            <tbody id="past_section_tbody"></tbody>
                        </table>
                    </div>
                    <p id="past_section_nodata" class="text-danger font-weight-bold" style="display:none;">
                        चुने गए दिनांक के लिए कोई डेटा उपलब्ध नहीं है।
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var isSadmin = <?php echo ($is_sadmin || $is_dr) ? 'true' : 'false'; ?>;
    var isDr = <?php echo $is_dr ? 'true' : 'false'; ?>;
    var currentDistrictId = <?php echo intval($user_district_id); ?>;
    var districtName = "<?php echo addslashes(h($user_district_name)); ?>";
    var pdate_today = "<?php echo $today; ?>";

    function toN(v) {
        var n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    function recalcFormTotals() {
        var f = document.getElementById('praroop6_form');
        if (!f) return;
        var opening = toN(f.opening_balance.value);
        var income = toN(f.income_received.value);
        f.total_income.value = (opening + income).toFixed(2);

        var msum = toN(f.me_salary_pension.value) + toN(f.me_commission.value) + toN(f.me_vehicle.value) + toN(f.me_other.value);
        f.me_monthly_total.value = msum.toFixed(2);

        var csum = toN(f.ce_salary_pension.value) + toN(f.ce_commission.value) + toN(f.ce_vehicle.value) + toN(f.ce_other.value);
        f.ce_total.value = csum.toFixed(2);
        f.cumulative_expense_ytd.value = csum.toFixed(2);
        f.closing_balance.value = (toN(f.total_income.value) - csum).toFixed(2);
    }

    function escapeHtml(str) {
        return (str == null ? '' : String(str))
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function loadPastSection() {
        var dateFrom = document.getElementById('date_from').value;
        var dateTo = document.getElementById('date_to').value;
        if (!dateFrom || !dateTo) { alert('कृपया प्रारम्भ और समाप्ति दिनांक चुनें।'); return; }
        if (dateFrom > dateTo) { alert('प्रारम्भ दिनांक, समाप्ति दिनांक से पहले होनी चाहिए।'); return; }

        var url = '?ajax_date_rows=1&date_from=' + encodeURIComponent(dateFrom) + '&date_to=' + encodeURIComponent(dateTo);
        if (isSadmin) url += '&selected_district=' + encodeURIComponent(currentDistrictId);

        var tbody = document.getElementById('past_section_tbody');
        var nodata = document.getElementById('past_section_nodata');
        var title = document.getElementById('past_section_title');
        var actionHead = document.getElementById('past_action_col_head');

        tbody.innerHTML = '';
        nodata.style.display = 'none';

        var f = dateFrom.split('-'), t = dateTo.split('-');
        title.textContent = districtName + ' — दिनांक: ' + f[2] + '-' + f[1] + '-' + f[0] + ' से ' + t[2] + '-' + t[1] + '-' + t[0];

        if (actionHead) actionHead.style.display = isDr ? 'table-cell' : 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function () {
            if (xhr.status !== 200) return;
            try {
                var rows = JSON.parse(xhr.responseText);
                if (!rows || rows.length === 0) {
                    nodata.style.display = 'block';
                    return;
                }

                var firstRow = rows[0];
                var st = firstRow.status || 'pending';
                var stBadge = st === 'approved'
                    ? '<span class="badge badge-success">स्वीकृत</span>'
                    : (st === 'rejected'
                        ? '<span class="badge badge-danger">अस्वीकृत</span>'
                        : '<span class="badge badge-warning">लंबित</span>');
                var remarkVal = firstRow.dr_remark || '';
                var remarkDisp = remarkVal ? escapeHtml(remarkVal) : '-';

                var actionCellFirst = '';
                if (isDr) {
                    actionCellFirst =
                        '<td rowspan="' + rows.length + '">' +
                        '<textarea id="past_remark_praroop6" class="form-control form-control-sm mb-1" rows="2" placeholder="टिप्पणी (वैकल्पिक)" style="min-width:150px;">' + escapeHtml(remarkVal) + '</textarea>' +
                        '<button class="btn btn-success btn-sm mr-1 mb-1" onclick="submitPastReview(\'' + dateFrom + '\',\'' + dateTo + '\',\'approved\')">✔ स्वीकृत</button>' +
                        '<button class="btn btn-danger btn-sm mb-1" onclick="submitPastReview(\'' + dateFrom + '\',\'' + dateTo + '\',\'rejected\')">✘ अस्वीकृत</button>' +
                        '</td>';
                }

                rows.forEach(function (r, idx) {
                    var tr = document.createElement('tr');
                    var statusCell = idx === 0 ? '<td rowspan="' + rows.length + '">' + stBadge + '</td>' : '';
                    var remarkCell = idx === 0 ? '<td rowspan="' + rows.length + '">' + remarkDisp + '</td>' : '';
                    var actionCell = idx === 0 ? actionCellFirst : '';

                    tr.innerHTML =
                        '<td>' + (idx + 1) + '</td>' +
                        '<td>' + escapeHtml(r.mandal_name || '') + '</td>' +
                        '<td>' + escapeHtml(districtName) + '</td>' +
                        '<td>' + escapeHtml(r.opening_balance || '0') + '</td>' +
                        '<td>' + escapeHtml(r.income_received || '0') + '</td>' +
                        '<td>' + escapeHtml(r.total_income || '0') + '</td>' +
                        '<td>' + escapeHtml(r.me_salary_pension || '0') + '</td>' +
                        '<td>' + escapeHtml(r.me_commission || '0') + '</td>' +
                        '<td>' + escapeHtml(r.me_vehicle || '0') + '</td>' +
                        '<td>' + escapeHtml(r.me_other || '0') + '</td>' +
                        '<td>' + escapeHtml(r.me_monthly_total || '0') + '</td>' +
                        '<td>' + escapeHtml(r.ce_salary_pension || '0') + '</td>' +
                        '<td>' + escapeHtml(r.ce_commission || '0') + '</td>' +
                        '<td>' + escapeHtml(r.ce_vehicle || '0') + '</td>' +
                        '<td>' + escapeHtml(r.ce_other || '0') + '</td>' +
                        '<td>' + escapeHtml(r.ce_total || '0') + '</td>' +
                        '<td>' + escapeHtml(r.cumulative_expense_ytd || '0') + '</td>' +
                        '<td>' + escapeHtml(r.closing_balance || '0') + '</td>' +
                        '<td>' + escapeHtml(r.fd_amount || '0') + '</td>' +
                        '<td>' + escapeHtml(r.fd_maturity || '') + '</td>' +
                        statusCell + remarkCell + actionCell;
                    tbody.appendChild(tr);
                });
            } catch (e) { console.error(e); }
        };
        xhr.send();
    }

    function submitPastReview(dateFrom, dateTo, status) {
        var remark = '';
        var el = document.getElementById('past_remark_praroop6');
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
                if (res.ok) loadPastSection();
            } catch (e) { alert('त्रुटि हुई।'); }
        };
        var body = 'id=dr_review'
            + '&district_id=' + currentDistrictId
            + '&date_from=' + encodeURIComponent(dateFrom)
            + '&date_to=' + encodeURIComponent(dateTo)
            + '&status=' + status
            + '&dr_remark=' + encodeURIComponent(remark);
        xhr.send(body);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('praroop6_form');
        if (form) {
            form.querySelectorAll('.calc').forEach(function (el) {
                el.addEventListener('input', recalcFormTotals);
            });
            recalcFormTotals();
        }

        if (isSadmin) {
            var districtSelect = document.getElementById('sadmin_district_select');
            districtSelect.addEventListener('change', function () {
                currentDistrictId = parseInt(this.value, 10) || 0;
                districtName = this.options[this.selectedIndex].text;
                var vdn = document.getElementById('view_district_name');
                if (vdn) vdn.textContent = districtName;
            });
        }
    });
</script>

<?php
page_footer_start();
page_footer_end();
?>