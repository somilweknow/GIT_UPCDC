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

    $session_districts = array_map('intval', array_column($all_districts, 'sno'));

} else {
    $user_district_id = !empty($session_districts) ? $session_districts[0] : 0;
    if ($user_district_id <= 0) {
        die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
    }
}

$res_dn             = execute_query("SELECT district_name FROM master_district WHERE sno = $user_district_id");
$user_district_name = (mysqli_num_rows($res_dn) > 0) ? mysqli_fetch_assoc($res_dn)['district_name'] : '';

$today   = date('Y-m-d');
$msg     = '';
$success = '';

if ($is_sadmin && isset($_GET['ajax_district_data'])) {
    $sel_dist = intval($_GET['selected_district'] ?? 0);
    $result   = ['static' => [], 'daily' => []];
    if ($sel_dist > 0) {
        $rs = execute_query("SELECT * FROM sangrah_sahkari_deyo_static WHERE district_id = $sel_dist");
        if (mysqli_num_rows($rs) > 0) $result['static'] = mysqli_fetch_assoc($rs);

        $rd = execute_query("SELECT * FROM sangrah_sahkari_deyo_daily_collection
                             WHERE district_id = $sel_dist AND entry_date = '$today'");
        if (mysqli_num_rows($rd) > 0) $result['daily'] = mysqli_fetch_assoc($rd);
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['id']) && $_POST['id'] === 'submit_deyo') {

    $district_id = $is_sadmin ? intval($_POST['district_id'] ?? $user_district_id) : $user_district_id;
    $entry_date  = $today;

    $total_bakaya                  = floatval($_POST['total_bakaya']                  ?? 0);
    $bakaya_95k                    = floatval($_POST['bakaya_95k']                    ?? 0);
    $recovery_95k_amount           = floatval($_POST['recovery_95k_amount']           ?? 0);
    $total_collection_fee          = floatval($_POST['total_collection_fee']          ?? 0);
    $big_defaulter_count           = intval($_POST['big_defaulter_count']             ?? 0);
    $big_defaulter_amount          = floatval($_POST['big_defaulter_amount']          ?? 0);
    $big_defaulter_recovery_count  = intval($_POST['big_defaulter_recovery_count']    ?? 0);
    $big_defaulter_recovery_amount = floatval($_POST['big_defaulter_recovery_amount'] ?? 0);

    $percent_95k          = ($total_bakaya > 0)        ? round($bakaya_95k * 100 / $total_bakaya, 2) : 0;
    $recovery_percent     = ($bakaya_95k > 0)          ? round($recovery_95k_amount * 100 / $bakaya_95k, 2) : 0;

    $daily_recovery_95k   = floatval($_POST['daily_recovery_95k']   ?? 0);
    $daily_collection_fee = floatval($_POST['daily_collection_fee'] ?? 0);
    $daily_payment        = floatval($_POST['daily_payment']        ?? 0);
    $balance              = $daily_collection_fee - $daily_payment;

    $r1 = execute_query("INSERT INTO sangrah_sahkari_deyo_static
        (district_id, total_bakaya, bakaya_95k, percent_95k,
         recovery_95k_amount, recovery_percent, total_collection_fee,
         big_defaulter_count, big_defaulter_amount,
         big_defaulter_recovery_count, big_defaulter_recovery_amount,
         updated_at, updated_by)
        VALUES ($district_id, $total_bakaya, $bakaya_95k, $percent_95k,
                $recovery_95k_amount, $recovery_percent, $total_collection_fee,
                $big_defaulter_count, $big_defaulter_amount,
                $big_defaulter_recovery_count, $big_defaulter_recovery_amount,
                NOW(), $current_user_sno)
        ON DUPLICATE KEY UPDATE
            total_bakaya                  = VALUES(total_bakaya),
            bakaya_95k                    = VALUES(bakaya_95k),
            percent_95k                   = VALUES(percent_95k),
            recovery_95k_amount           = VALUES(recovery_95k_amount),
            recovery_percent              = VALUES(recovery_percent),
            total_collection_fee          = VALUES(total_collection_fee),
            big_defaulter_count           = VALUES(big_defaulter_count),
            big_defaulter_amount          = VALUES(big_defaulter_amount),
            big_defaulter_recovery_count  = VALUES(big_defaulter_recovery_count),
            big_defaulter_recovery_amount = VALUES(big_defaulter_recovery_amount),
            updated_at                    = NOW(),
            updated_by                    = $current_user_sno");

    $r2 = execute_query("INSERT INTO sangrah_sahkari_deyo_daily_collection
        (district_id, entry_date, daily_recovery_95k, daily_collection_fee, daily_payment, balance)
        VALUES ($district_id, '$entry_date', $daily_recovery_95k, $daily_collection_fee, $daily_payment, $balance)
        ON DUPLICATE KEY UPDATE
            daily_recovery_95k   = VALUES(daily_recovery_95k),
            daily_collection_fee = VALUES(daily_collection_fee),
            daily_payment        = VALUES(daily_payment),
            balance              = VALUES(balance)");

    if ($r1 && $r2) {
        $success = '<div class="alert alert-success">Data Saved Successfully.</div>';
    } else {
        $msg = '<div class="alert alert-danger">Error. Please try again.</div>';
    }

    $user_district_id = $district_id;
}

$edit_static = [];
$edit_daily  = [];

$res_s = execute_query("SELECT * FROM sangrah_sahkari_deyo_static WHERE district_id = $user_district_id");
if (mysqli_num_rows($res_s) > 0) $edit_static = mysqli_fetch_assoc($res_s);

$res_d = execute_query("SELECT * FROM sangrah_sahkari_deyo_daily_collection
                        WHERE district_id = $user_district_id AND entry_date = '$today'");
if (mysqli_num_rows($res_d) > 0) $edit_daily = mysqli_fetch_assoc($res_d);

$audit_badge = '';
if (isset($_GET['ajax_preview'])) {
    header('Content-Type: application/json; charset=utf-8');
    $p_date = mysqli_real_escape_string($db, $_GET['p_date'] ?? '');
    $rows   = [];
    if (!empty($p_date)) {
        if ($is_sadmin) {
            $sel_dist   = intval($_GET['selected_district'] ?? $user_district_id);
            $where_dist = "c.district_id = $sel_dist";
        } else {
            $where_dist = !empty($session_districts)
                ? "c.district_id IN (" . implode(',', $session_districts) . ")"
                : "1=0";
        }
        $res_p = execute_query("SELECT d.district_name,
                s.total_bakaya, s.bakaya_95k,
                s.recovery_95k_amount, s.total_collection_fee,
                s.big_defaulter_count, s.big_defaulter_amount,
                s.big_defaulter_recovery_count, s.big_defaulter_recovery_amount,
                s.updated_at, s.updated_by,
                c.daily_recovery_95k, c.daily_collection_fee, c.daily_payment, c.balance
            FROM sangrah_sahkari_deyo_daily_collection c
            JOIN master_district d ON d.sno = c.district_id
            LEFT JOIN sangrah_sahkari_deyo_static s ON s.district_id = c.district_id
            WHERE c.entry_date = '$p_date'
              AND $where_dist
            ORDER BY d.district_name");
        while ($rp = mysqli_fetch_assoc($res_p)) $rows[] = $rp;
    }
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    exit;
}

function v_s($key, $arr, $default = '') {
    return isset($arr[$key]) ? htmlspecialchars($arr[$key]) : $default;
}

page_header_start();
?>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .step h4 { color:#fff; background:#FF8E00; border-radius:15px; padding:10px 10px 6px 20px; }
        .step h5 { color:#000; background:#FFDB44; border-radius:15px; padding:10px 10px 6px 20px; }
        .required-star { color:red; }
        .info-box { background:#fff8e1; border-left:4px solid #FF8E00; padding:10px 15px; border-radius:5px; font-size:13px; color:#555; }
        .readonly-field { background:#f0f0f0 !important; font-weight:bold; cursor:not-allowed; }
        .calc-field { background:#e8f4e8 !important; font-weight:bold; color:#155724; cursor:not-allowed; }
        .balance-display { font-size:1.05em; font-weight:bold; color:#155724; background:#d4edda; border-radius:8px; padding:6px 12px; display:inline-block; }
        .balance-display.negative { color:#721c24; background:#f8d7da; }
        .district-badge { background:#FF8E00; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
        .sadmin-badge { background:#6f42c1; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
        .preview-table th { background:#FF8E00; color:#fff; text-align:center; vertical-align:middle; }
        .preview-table td { text-align:center; vertical-align:middle; }
        .past-thead th { background:#FF8E00 !important; color:#fff; text-align:center; vertical-align:middle; }
        #past_section { }
        #district_loading { display:none; color:#FF8E00; font-size:13px; margin-top:4px; }
        #printArea {
    background: white;
    padding: 10px;
}
 @media print {
            #no-print {
                display: none !important;
            }
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
                            <h4><u></u></h4>
                        </div>
                         <div class="text-center mb-3">
                            <strong>प्रारूप-4</strong><br>
                            <h4><u>सहकारी देयो (पैक्स, एलडीबी, विविधीकरण) की बकाया वसूली</u></h4>
                            <small>दिनांक 01.07.2025 से &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; तक &nbsp;&nbsp;&nbsp;&nbsp; (धनराशि लाख रु0 में)</small>
                        </div>
                        
                        <?php echo $msg; echo $success; ?>
                        <form action="" method="post" id="deyo_form" accept-charset="UTF-8">
                            <input type="hidden" name="id"          value="submit_deyo">
                            <input type="hidden" name="district_id" id="form_district_id" value="<?php echo $user_district_id; ?>">

                            <div class="step">
                                <marquee style="font-size:16px; color:red;">
                                    नोट: सहकारी देयो (पैक्स, एलडीबी, विविधीकरण) बकाया वसूली का विवरण प्रतिदिन सही-सही भरें। धनराशि लाख रुपये में भरें।
                                </marquee><br>
                                <h4>1. जनपद एवं दिनांक</h4>
                                <div class="col-sm-12">
                                    <div class="row align-items-center">

                                        <div class="col-sm-4 form-group">
                                            <label>जनपद</label>
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
                                                <small class="text-muted">जनपद बदलें — फॉर्म का डेटा स्वतः अपडेट होगा।</small>
                                                <div id="district_loading">⏳ Loading...</div>
                                            <?php else: ?>
                                                <div class="form-control readonly-field">
                                                    <?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?>
                                                </div>
                                                <small class="text-muted">जनपद आपके खाते से स्वतः निर्धारित है।</small>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-sm-3 form-group">
                                            <label>दिनांक (आज)</label>
                                            <input type="text" class="form-control readonly-field"
                                                   value="<?php echo date('d-m-Y'); ?>" readonly>
                                            <input type="hidden" name="entry_date" value="<?php echo $today; ?>">
                                        </div>

                                        <div class="col-sm-5 form-group">
                                            <div class="info-box">
                                                दिनांक एवं जनपद स्वतः निर्धारित हैं।
                                                यदि आज का डेटा पहले से सेव है तो अपडेट होगा।<br>
                                                <?php echo $audit_badge; ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="step">
                                <h4>2. बकाया एवं वसूली का स्थायी विवरण
                                    <small style="font-size:13px; font-weight:normal;"><?php echo $audit_badge; ?></small>
                                </h4>
                                <div class="col-sm-12">

                                    <h5>(I) कुल बकाया एवं 95 "क" से आच्छादित बकाया</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>कुल बकाया (लाख रु.)</label>
                                            <input type="number" step="0.01" name="total_bakaya" id="total_bakaya"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcAll()"
                                                   value="<?php echo v_s('total_bakaya', $edit_static, isset($_POST['total_bakaya']) ? $_POST['total_bakaya'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>95 "क" से आच्छादित बकाया (लाख रु.)</label>
                                            <input type="number" step="0.01" name="bakaya_95k" id="bakaya_95k"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcAll()"
                                                   value="<?php echo v_s('bakaya_95k', $edit_static, isset($_POST['bakaya_95k']) ? $_POST['bakaya_95k'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>95 "क" से आच्छादन का प्रतिशत</label>
                                            <input type="text" id="pct_aachhadit" class="form-control calc-field" readonly
                                                   value="<?php
                                                   $tb = floatval($edit_static['total_bakaya'] ?? 0);
                                                   $bk = floatval($edit_static['bakaya_95k']   ?? 0);
                                                   echo ($tb > 0 ? round($bk * 100 / $tb, 2) : 0) . ' %';
                                                   ?>">
                                        </div>
                                    </div>

                                    <h5>(II) 95 "क" के आच्छादन से वसूली एवं संग्रह शुल्क</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>95 "क" के आच्छादन से वसूली (लाख रु.)</label>
                                            <input type="number" step="0.01" name="recovery_95k_amount" id="recovery_95k_amount"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcAll()"
                                                   value="<?php echo v_s('recovery_95k_amount', $edit_static, isset($_POST['recovery_95k_amount']) ? $_POST['recovery_95k_amount'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>वसूली प्रतिशत</label>
                                            <input type="text" id="pct_vasuli" class="form-control calc-field" readonly
                                                   value="<?php
                                                   $bk  = floatval($edit_static['bakaya_95k']         ?? 0);
                                                   $rec = floatval($edit_static['recovery_95k_amount'] ?? 0);
                                                   echo ($bk > 0 ? round($rec * 100 / $bk, 2) : 0) . ' %';
                                                   ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>प्राप्त संग्रह शुल्क (लाख रु.)</label>
                                            <input type="number" step="0.01" name="total_collection_fee" id="total_collection_fee"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcAll()"
                                                   value="<?php echo v_s('total_collection_fee', $edit_static, isset($_POST['total_collection_fee']) ? $_POST['total_collection_fee'] : ''); ?>">
                                        </div>
                                    </div>

                                    <h5>(III) 95 "क" से आच्छादित एक लाख से बड़े बकायेदार</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>संख्या</label>
                                            <input type="number" step="1" min="0" name="big_defaulter_count" id="big_defaulter_count"
                                                   class="form-control" placeholder="0"
                                                   value="<?php echo v_s('big_defaulter_count', $edit_static, isset($_POST['big_defaulter_count']) ? $_POST['big_defaulter_count'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>धनराशि (लाख रु.)</label>
                                            <input type="number" step="0.01" name="big_defaulter_amount" id="big_defaulter_amount"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcAll()"
                                                   value="<?php echo v_s('big_defaulter_amount', $edit_static, isset($_POST['big_defaulter_amount']) ? $_POST['big_defaulter_amount'] : ''); ?>">
                                        </div>
                                    </div>

                                    <h5>(IV) 95 "क" से आच्छादित एक लाख से बड़े बकायेदारों से वसूली</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>वसूली संख्या</label>
                                            <input type="number" step="1" min="0" name="big_defaulter_recovery_count" id="big_defaulter_recovery_count"
                                                   class="form-control" placeholder="0"
                                                   value="<?php echo v_s('big_defaulter_recovery_count', $edit_static, isset($_POST['big_defaulter_recovery_count']) ? $_POST['big_defaulter_recovery_count'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>वसूली धनराशि (लाख रु.)</label>
                                            <input type="number" step="0.01" name="big_defaulter_recovery_amount" id="big_defaulter_recovery_amount"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcAll()"
                                                   value="<?php echo v_s('big_defaulter_recovery_amount', $edit_static, isset($_POST['big_defaulter_recovery_amount']) ? $_POST['big_defaulter_recovery_amount'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>एक लाख से बड़े बकायेदारों से वसूली प्रतिशत</label>
                                            <input type="text" id="pct_big" class="form-control calc-field" readonly
                                                   value="<?php
                                                   $bda  = floatval($edit_static['big_defaulter_amount']          ?? 0);
                                                   $bdra = floatval($edit_static['big_defaulter_recovery_amount'] ?? 0);
                                                   echo ($bda > 0 ? round($bdra * 100 / $bda, 2) : 0) . ' %';
                                                   ?>">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="step">
                                <h4>3. दैनिक वसूली एवं भुगतान का विवरण
                                    (दिनांक: <?php echo date('d-m-Y'); ?>)
                                </h4>
                                <div class="col-sm-12">

                                    <h5>(I) 95 "क" से आच्छादन की दैनिक वसूली</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>दैनिक वसूली - 95 "क" (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="daily_recovery_95k" id="daily_recovery_95k"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="updatePreview()"
                                                   value="<?php echo v_s('daily_recovery_95k', $edit_daily, isset($_POST['daily_recovery_95k']) ? $_POST['daily_recovery_95k'] : ''); ?>">
                                        </div>
                                    </div>

                                    <h5>(II) दैनिक संग्रह शुल्क एवं भुगतान</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>प्राप्त दैनिक संग्रह शुल्क (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="daily_collection_fee" id="daily_collection_fee"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcBalance()"
                                                   value="<?php echo v_s('daily_collection_fee', $edit_daily, isset($_POST['daily_collection_fee']) ? $_POST['daily_collection_fee'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>दैनिक भुगतान राशि (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="daily_payment" id="daily_payment"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcBalance()"
                                                   value="<?php echo v_s('daily_payment', $edit_daily, isset($_POST['daily_payment']) ? $_POST['daily_payment'] : ''); ?>">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                            <label>अवशेष (संग्रह शुल्क - भुगतान) - स्वतः गणना</label>
                                            <input type="number" step="0.01" name="balance" id="balance"
                                                   class="form-control readonly-field" readonly
                                                   value="<?php echo v_s('balance', $edit_daily, isset($_POST['daily_collection_fee']) ? floatval($_POST['daily_collection_fee']) - floatval($_POST['daily_payment']) : ''); ?>">
                                            <div id="balance_display" style="margin-top:5px;"></div>
                                        </div>
                                    </div>
                                    <div class="row mt-2 mb-3">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-danger btn-lg mr-2">
                                                Submit
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
                     <div class="step">
                     <!-- ✅ EXPORT BUTTON -->
                    <div>
                        <button class="btn btn-danger" onclick="exportToPDF()">
                            📄 Export PDF
                        </button>
                    </div>
                     <div class="text-center mb-3">
                        <strong>प्रारूप-4</strong><br>
                        <h4><u>सहकारी देयो (पैक्स, एलडीबी, विविधीकरण) की बकाया वसूली</u></h4>
                        <small>दिनांक 01.07.2025 से &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; तक &nbsp;&nbsp;&nbsp;&nbsp; (धनराशि लाख रु0 में)</small>
                    </div>
          
                    <div class="col-md-12">
                                    <h5>(IV) पूर्व दिनांक का डेटा देखें (केवल चयनित जनपद का डेटा दिखेगा)</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>दिनांक चुनें <span class="required-star">*</span></label>
                                            <input type="date" id="past_date" class="form-control"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                            <small class="text-muted">आज या कोई भी पूर्व दिनांक।</small>
                                        </div>
                                        <div class="col-sm-3 form-group" style="padding-top:32px;">
                                            <button type="button" class="btn btn-warning btn-lg" onclick="loadPastData();">
                                               Search
                                            </button>
                                        </div>
                                    </div>

                                    <div id="past_section">
                                        <h6 id="past_title" style="margin:8px 0; font-weight:bold; color:#333;"></h6>
                                        <div class="table-responsive"  id="printArea">
                                            <table class="table table-bordered" style="font-size:12px; text-align:center;">
                                                <thead class="past-thead">
                                                <tr>
                                                    <th rowspan="2">जनपद</th>
                                                    <th rowspan="2">कुल बकाया (लाख)</th>
                                                    <th rowspan="2">95 'क' बकाया (लाख)</th>
                                                    <th rowspan="2">आच्छादन %</th>
                                                    <th rowspan="2">95 'क' वसूली (लाख)</th>
                                                    <th rowspan="2">वसूली %</th>
                                                    <th rowspan="2">संग्रह शुल्क (लाख)</th>
                                                    <th colspan="2">1 लाख+ बकायेदार</th>
                                                    <th colspan="2">1 लाख+ वसूली</th>
                                                    <th rowspan="2">वसूली %</th>
                                                    <th rowspan="2">दैनिक वसूली (लाख)</th>
                                                    <th rowspan="2">दैनिक संग्रह शुल्क (लाख)</th>
                                                    <th rowspan="2">दैनिक भुगतान (लाख)</th>
                                                    <th rowspan="2">अवशेष (लाख)</th>
                                                    <th rowspan="2" style="display: table-cell !important;">अंतिम अपडेट</th>
                                                </tr>
                                                <tr>
                                                    <th>संख्या</th><th>धनराशि</th>
                                                    <th>संख्या</th><th>धनराशि</th>
                                                </tr>
                                                </thead>
                                                <tbody id="past_body"></tbody>
                                            </table>
                                        </div>
                                        <p id="past_nodata" class="text-danger font-weight-bold" style="display:none;">
                                            चुने गए दिनांक के लिए कोई डेटा उपलब्ध नहीं है।
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
   

    <script>
        var isSadmin          = <?php echo $is_sadmin ? 'true' : 'false'; ?>;
        var fixedDistrictName = "<?php echo addslashes(htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8')); ?>";
        var currentDistrictId = <?php echo $user_district_id; ?>;
        function sadminDistrictChange(districtId) {
            if (!isSadmin || !districtId) return;
            currentDistrictId = parseInt(districtId);
            document.getElementById('form_district_id').value = districtId;
            document.getElementById('district_loading').style.display = 'block';

            var xhr = new XMLHttpRequest();
            xhr.open('GET', '?ajax_district_data=1&selected_district=' + districtId, true);
            xhr.onload = function () {
                document.getElementById('district_loading').style.display = 'none';
                if (xhr.status !== 200) return;
                try {
                    var data = JSON.parse(xhr.responseText);
                    var s = data.static || {};
                    var d = data.daily  || {};
                    var sel = document.getElementById('sadmin_district_select');
                    var selectedText = sel.options[sel.selectedIndex].text;
                    fixedDistrictName = selectedText;
                    setField('total_bakaya',                  s.total_bakaya                  || '');
                    setField('bakaya_95k',                    s.bakaya_95k                    || '');
                    setField('recovery_95k_amount',           s.recovery_95k_amount           || '');
                    setField('total_collection_fee',          s.total_collection_fee          || '');
                    setField('big_defaulter_count',           s.big_defaulter_count           || '');
                    setField('big_defaulter_amount',          s.big_defaulter_amount          || '');
                    setField('big_defaulter_recovery_count',  s.big_defaulter_recovery_count  || '');
                    setField('big_defaulter_recovery_amount', s.big_defaulter_recovery_amount || '');
                    setField('daily_recovery_95k',   d.daily_recovery_95k   || '');
                    setField('daily_collection_fee', d.daily_collection_fee || '');
                    setField('daily_payment',        d.daily_payment        || '');
                    setField('balance',              d.balance              || '');

                    calcAll();
                    calcBalance();

                    document.getElementById('past_body').innerHTML = '';
                    loadPastData();
                } catch (e) { console.error(e); }
            };
            xhr.send();
        }

        function setField(id, val) {
            var el = document.getElementById(id);
            if (el) el.value = val;
        }

        function pct(num, den) {
            num = parseFloat(num) || 0;
            den = parseFloat(den) || 0;
            return den > 0 ? (num * 100 / den).toFixed(2) : '0.00';
        }

        function gv(id) {
            var el = document.getElementById(id);
            return el ? (el.value || '0') : '0';
        }

        function calcAll() {
            var tb    = gv('total_bakaya');
            var b95   = gv('bakaya_95k');
            var r95   = gv('recovery_95k_amount');
            var bdAmt = gv('big_defaulter_amount');
            var brAmt = gv('big_defaulter_recovery_amount');

            var p5  = pct(b95,  tb);
            var p7  = pct(r95,  b95);
            var p13 = pct(brAmt, bdAmt);

            document.getElementById('pct_aachhadit').value = p5  + ' %';
            document.getElementById('pct_vasuli').value    = p7  + ' %';
            document.getElementById('pct_big').value       = p13 + ' %';

            updatePreview(p5, p7, p13);
        }

        function calcBalance() {
            var fee = parseFloat(gv('daily_collection_fee')) || 0;
            var pay = parseFloat(gv('daily_payment'))        || 0;
            var bal = (fee - pay).toFixed(2);
            document.getElementById('balance').value = bal;
            var disp = document.getElementById('balance_display');
            if (parseFloat(bal) >= 0) {
                disp.innerHTML = '<span class="balance-display">अवशेष: ' + bal + ' लाख</span>';
            } else {
                disp.innerHTML = '<span class="balance-display negative">अवशेष: ' + bal + ' लाख (ऋणात्मक)</span>';
            }
            updatePreview();
        }

        function updatePreview() {}

        function loadPastData() {
            var pdate = document.getElementById('past_date').value;
            if (!pdate) { alert('कृपया दिनांक चुनें।'); return; }

            var url = '?ajax_preview=1&p_date=' + pdate;
            if (isSadmin) url += '&selected_district=' + currentDistrictId;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.onload = function () {
                if (xhr.status !== 200) return;
                try {
                    var rows   = JSON.parse(xhr.responseText);
                    var tbody  = document.getElementById('past_body');
                    var title  = document.getElementById('past_title');
                    var nodata = document.getElementById('past_nodata');
                    var sec    = document.getElementById('past_section');
                    tbody.innerHTML   = '';
                    sec.style.display = 'block';
                    var p = pdate.split('-');
                    title.textContent = fixedDistrictName + ' - दिनांक: ' + p[2] + '-' + p[1] + '-' + p[0];
                    if (!rows || rows.length === 0) { nodata.style.display = 'block'; return; }
                    nodata.style.display = 'none';
                    rows.forEach(function (r) {
                        var tb   = parseFloat(r.total_bakaya || 0);
                        var bk   = parseFloat(r.bakaya_95k || 0);
                        var rec  = parseFloat(r.recovery_95k_amount || 0);
                        var bda  = parseFloat(r.big_defaulter_amount || 0);
                        var bdra = parseFloat(r.big_defaulter_recovery_amount || 0);
                        var p5  = tb  > 0 ? (bk * 100 / tb).toFixed(2) : '0.00';
                        var p7  = bk  > 0 ? (rec * 100 / bk).toFixed(2) : '0.00';
                        var p13 = bda > 0 ? (bdra * 100 / bda).toFixed(2) : '0.00';
                        tbody.innerHTML +=
                            '<tr>' +
                            '<td>' + (r.district_name || '-') + '</td>' +
                            '<td>' + (r.total_bakaya || '-') + '</td>' +
                            '<td>' + (r.bakaya_95k || '-') + '</td>' +
                            '<td>' + p5 + ' %</td>' +
                            '<td>' + (r.recovery_95k_amount || '-') + '</td>' +
                            '<td>' + p7 + ' %</td>' +
                            '<td>' + (r.total_collection_fee || '-') + '</td>' +
                            '<td>' + (r.big_defaulter_count || '-') + '</td>' +
                            '<td>' + (r.big_defaulter_amount || '-') + '</td>' +
                            '<td>' + (r.big_defaulter_recovery_count || '-') + '</td>' +
                            '<td>' + (r.big_defaulter_recovery_amount || '-') + '</td>' +
                            '<td>' + p13 + ' %</td>' +
                            '<td>' + (r.daily_recovery_95k || '-') + '</td>' +
                            '<td>' + (r.daily_collection_fee || '-') + '</td>' +
                            '<td>' + (r.daily_payment || '-') + '</td>' +
                            '<td>' + (r.balance || '-') + '</td>' +
                            '<td>' + (r.updated_at || '-') + '</td>' +
                            '</tr>';
                    });
                } catch (e) { console.error(e); }
            };
            xhr.send();
        }

        document.addEventListener('DOMContentLoaded', function () {
            calcAll();
            calcBalance();
            loadPastData();
        });
     function exportToPDF() {

    var element = document.getElementById("printArea");

    if (!element) {
        alert("Print area not found!");
        return;
    }

    // ✅ SAFE CHECK
    var loader = document.getElementById("prog_loading");
    if (loader) {
        loader.style.display = "none";
    }

    html2canvas(element, {
        scale: 2,
        useCORS: true
    }).then(function(canvas) {

        var imgData = canvas.toDataURL("image/png");

        const { jsPDF } = window.jspdf;
        var pdf = new jsPDF('l', 'mm', 'a4');

        var imgWidth = 297;
        var pageHeight = 210;
        var imgHeight = canvas.height * imgWidth / canvas.width;

        var heightLeft = imgHeight;
        var position = 0;

        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save("deyo_report.pdf");
    });
}
    </script>

<?php
page_footer_start();
page_footer_end();
?>