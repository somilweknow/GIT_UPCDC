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

    $session_districts = array_map('intval', array_column($all_districts, 'sno'));

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
        $session_districts = array_map('intval', array_column($all_districts, 'sno'));
    } else {
        $user_district_id = !empty($session_districts) ? $session_districts[0] : 0;
        if ($user_district_id <= 0) {
            die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
        }
    }
}

$res_dn = execute_query("SELECT district_name FROM master_district WHERE sno = $user_district_id");
$user_district_name = (mysqli_num_rows($res_dn) > 0) ? mysqli_fetch_assoc($res_dn)['district_name'] : '';

$today   = date('Y-m-d');
$msg     = '';
$success = '';

if (isset($_POST['id']) && $_POST['id'] === 'dr_review_finance') {
    header('Content-Type: application/json; charset=utf-8');
    if (!$is_dr) {
        echo json_encode(['ok' => false, 'msg' => 'केवल DR अनुमोदन कर सकते हैं।'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $rev_district  = intval($_POST['district_id'] ?? 0);
    $rev_date_from = mysqli_real_escape_string($db, $_POST['date_from'] ?? '');
    $rev_date_to   = mysqli_real_escape_string($db, $_POST['date_to']   ?? $rev_date_from);
    $rev_status    = in_array($_POST['status'], ['approved', 'rejected']) ? $_POST['status'] : '';
    $rev_remark    = mysqli_real_escape_string($db, trim($_POST['dr_remark'] ?? ''));

    if (!$rev_district || !$rev_date_from || !$rev_status) {
        echo json_encode(['ok' => false, 'msg' => 'अपूर्ण डेटा।'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($rev_status === 'rejected' && $rev_remark === '') {
        echo json_encode(['ok' => false, 'msg' => 'अस्वीकृत करने पर टिप्पणी अनिवार्य है।'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $r = execute_query("UPDATE sangrah_finance_daily_collection
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

if ($is_sadmin && isset($_GET['ajax_district_data'])) {
    $sel_dist = intval($_GET['selected_district'] ?? 0);
    $result   = ['static' => [], 'daily' => []];
    if ($sel_dist > 0) {
        $rs = execute_query("SELECT * FROM sangrah_finance_static WHERE district_id = $sel_dist");
        if (mysqli_num_rows($rs) > 0) $result['static'] = mysqli_fetch_assoc($rs);

        $rd = execute_query("SELECT * FROM sangrah_finance_daily_collection
                             WHERE district_id = $sel_dist AND entry_date = '$today'");
        if (mysqli_num_rows($rd) > 0) $result['daily'] = mysqli_fetch_assoc($rd);
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_GET['ajax_preview'])) {
    header('Content-Type: application/json; charset=utf-8');
    $date_from = mysqli_real_escape_string($db, $_GET['date_from'] ?? '');
    $date_to   = mysqli_real_escape_string($db, $_GET['date_to']   ?? $date_from);
    $rows   = [];
    if (!empty($date_from)) {
        if ($is_sadmin) {
            $sel_dist   = intval($_GET['selected_district'] ?? $user_district_id);
            $where_dist = "c.district_id = $sel_dist";
        } else {
            $where_dist = !empty($session_districts)
                ? "c.district_id IN (" . implode(',', $session_districts) . ")"
                : "1=0";
        }
        $res_p = execute_query("SELECT d.district_name,
                         s.bakaya_95k, s.amin_vetanik, s.amin_commission, s.amin_total,
                         s.total_recovery, s.total_collection_fee,
                         c.entry_date, c.daily_recovery_95k, c.daily_collection_fee, c.daily_payment, c.balance,
                         c.status, c.dr_remark,
                         s.updated_at, s.updated_by
                  FROM sangrah_finance_daily_collection c
                  JOIN master_district d ON d.sno = c.district_id
                  LEFT JOIN sangrah_finance_static s ON s.district_id = c.district_id
                  WHERE c.entry_date BETWEEN '$date_from' AND '$date_to'
                    AND $where_dist
                  ORDER BY c.entry_date, d.district_name");
        while ($rp = mysqli_fetch_assoc($res_p)) $rows[] = $rp;
    }
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['id']) && $_POST['id'] === 'submit_sangrah') {
    $district_id          = $is_sadmin ? intval($_POST['district_id'] ?? $user_district_id) : $user_district_id;
    $entry_date           = $today;
    $bakaya_95k           = floatval($_POST['bakaya_95k']           ?? 0);
    $amin_vetanik         = intval($_POST['amin_vetanik']           ?? 0);
    $amin_commission      = intval($_POST['amin_commission']        ?? 0);
    $amin_total           = $amin_vetanik + $amin_commission;
    $total_recovery       = floatval($_POST['total_recovery']       ?? 0);
    $total_collection_fee = floatval($_POST['total_collection_fee'] ?? 0);
    $daily_recovery_95k   = floatval($_POST['daily_recovery_95k']   ?? 0);
    $daily_collection_fee = floatval($_POST['daily_collection_fee'] ?? 0);
    $daily_payment        = floatval($_POST['daily_payment']        ?? 0);
    $balance              = $daily_collection_fee - $daily_payment;

    $user_district_id = $district_id;

    $r1 = execute_query("INSERT INTO sangrah_finance_static
        (district_id, bakaya_95k, amin_vetanik, amin_commission, amin_total,
         total_recovery, total_collection_fee, updated_at, updated_by)
        VALUES ($district_id, $bakaya_95k, $amin_vetanik, $amin_commission, $amin_total,
                $total_recovery, $total_collection_fee, NOW(), $current_user_sno)
        ON DUPLICATE KEY UPDATE
            bakaya_95k           = VALUES(bakaya_95k),
            amin_vetanik         = VALUES(amin_vetanik),
            amin_commission      = VALUES(amin_commission),
            amin_total           = VALUES(amin_total),
            total_recovery       = VALUES(total_recovery),
            total_collection_fee = VALUES(total_collection_fee),
            updated_at           = NOW(),
            updated_by           = $current_user_sno");

    $r2 = execute_query("INSERT INTO sangrah_finance_daily_collection
        (district_id, entry_date, daily_recovery_95k, daily_collection_fee, daily_payment, balance)
        VALUES ($district_id, '$entry_date', $daily_recovery_95k, $daily_collection_fee, $daily_payment, $balance)
        ON DUPLICATE KEY UPDATE
            daily_recovery_95k   = VALUES(daily_recovery_95k),
            daily_collection_fee = VALUES(daily_collection_fee),
            daily_payment        = VALUES(daily_payment),
            balance              = VALUES(balance)");

    if ($r1 && $r2) {
        $success = '<div class="alert alert-success">डेटा सफलतापूर्वक सहेजा गया।</div>';
    } else {
        $msg = '<div class="alert alert-danger">डेटा सहेजने में त्रुटि हुई। कृपया पुनः प्रयास करें।</div>';
    }
}

$edit_static = [];
$edit_daily  = [];

$res_s = execute_query("SELECT * FROM sangrah_finance_static WHERE district_id = $user_district_id");
if (mysqli_num_rows($res_s) > 0) $edit_static = mysqli_fetch_assoc($res_s);

$res_d = execute_query("SELECT * FROM sangrah_finance_daily_collection
                        WHERE district_id = $user_district_id AND entry_date = '$today'");
if (mysqli_num_rows($res_d) > 0) $edit_daily = mysqli_fetch_assoc($res_d);

$audit_badge = '';
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
        .balance-display { font-size:1.05em; font-weight:bold; color:#155724; background:#d4edda; border-radius:8px; padding:6px 12px; display:inline-block; }
        .balance-display.negative { color:#721c24; background:#f8d7da; }
        .readonly-field { background:#f0f0f0 !important; font-weight:bold; cursor:not-allowed; }
        .district-badge { background:#FF8E00; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
        .sadmin-badge { background:#6f42c1; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
        .past-thead th { background:#FF8E00 !important; color:#fff; }
        #past_section { display:none; }
        #district_loading { display:none; color:#FF8E00; font-size:13px; margin-top:4px; }
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
                        <?php echo $msg; echo $success; ?>
                        <div class="text-center mb-3">
                            <h4><u>जनपदवार बकाया वसूली एवं प्राप्त संग्रह शुल्क तथा किये गये भुगतान की दैनिक सूचना</u></h4>
                        </div>
                        <form action="" method="post" id="sangrah_form" accept-charset="UTF-8">
                            <input type="hidden" name="id"          value="submit_sangrah">
                            <input type="hidden" name="district_id" id="form_district_id"
                                   value="<?php echo $user_district_id; ?>">
                            <div class="step">
                                <marquee style="font-size:16px; color:red;">
                                    नोट: दैनिक संग्रह शुल्क एवं वसूली का विवरण प्रतिदिन सही-सही भरें। धनराशि लाख रुपये में भरें।
                                </marquee><br>
                                <h4>1. जनपद एवं दिनांक</h4>
                                <div class="col-sm-12">
                                    <div class="row align-items-center">
                                        <div class="col-sm-4 form-group">
                                            <label>जनपद
                                            </label>
                                            <?php if ($is_sadmin || $is_dr): ?>
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
                                                <div id="district_loading">⏳ डेटा लोड हो रहा है...</div>
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
                                                यदि आज का डेटा पहले से सहेजा है तो पुनः सहेजने पर अपडेट होगा।<br>
                                                <?php echo $audit_badge; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step">
                                <h4>2. बकाया एवं संचयी वसूली का विवरण
                                    <small style="font-size:13px; font-weight:normal;"><?php echo $audit_badge; ?></small>
                                </h4>
                                <div class="col-sm-12">
                                    <h5>(I) 95 "क" से आच्छादित बकाया</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>95 "क" से आच्छादित बकाया (धनराशि लाख रु. में)</label>
                                            <input type="number" step="0.01" name="bakaya_95k" id="bakaya_95k"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="updatePreview()"
                                                   value="<?php echo v_s('bakaya_95k', $edit_static, ''); ?>">
                                        </div>
                                    </div>
                                    <h5>(II) कार्यरत अमीनों की संख्या</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>वैतनिक अमीन</label>
                                            <input type="number" min="0" name="amin_vetanik" id="amin_vetanik"
                                                   class="form-control" placeholder="0"
                                                   oninput="calcAminTotal()"
                                                   value="<?php echo v_s('amin_vetanik', $edit_static, ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>कमीशन अमीन</label>
                                            <input type="number" min="0" name="amin_commission" id="amin_commission"
                                                   class="form-control" placeholder="0"
                                                   oninput="calcAminTotal()"
                                                   value="<?php echo v_s('amin_commission', $edit_static, ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>योग (स्वतः गणना)</label>
                                            <input type="number" name="amin_total" id="amin_total"
                                                   class="form-control readonly-field" readonly
                                                   value="<?php echo v_s('amin_total', $edit_static, ''); ?>">
                                        </div>
                                    </div>
                                    <h5>(III) कुल वसूली एवं संग्रह शुल्क (01-07-25 से 28-02-26)</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>कुल वसूली - 95 "क" (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="total_recovery" id="total_recovery"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="updatePreview()"
                                                   value="<?php echo v_s('total_recovery', $edit_static, ''); ?>">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                            <label>प्राप्त कुल संग्रह शुल्क (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="total_collection_fee" id="total_collection_fee"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="updatePreview()"
                                                   value="<?php echo v_s('total_collection_fee', $edit_static, ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="step">
                                <h4>3. दैनिक वसूली एवं भुगतान का विवरण</h4>
                                <div class="col-sm-12">
                                    <h5>(I) 95 "क" से आच्छादन की दैनिक वसूली</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>दैनिक वसूली - 95 "क" (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="daily_recovery_95k" id="daily_recovery_95k"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="updatePreview()"
                                                   value="<?php echo v_s('daily_recovery_95k', $edit_daily, ''); ?>">
                                        </div>
                                    </div>
                                    <h5>(II) दैनिक संग्रह शुल्क एवं भुगतान</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>प्राप्त दैनिक संग्रह शुल्क (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="daily_collection_fee" id="daily_collection_fee"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcBalance()"
                                                   value="<?php echo v_s('daily_collection_fee', $edit_daily, ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>दैनिक भुगतान राशि (लाख रु. में)</label>
                                            <input type="number" step="0.01" name="daily_payment" id="daily_payment"
                                                   class="form-control" placeholder="0.00"
                                                   oninput="calcBalance()"
                                                   value="<?php echo v_s('daily_payment', $edit_daily, ''); ?>">
                                        </div>
                                        <div class="col-sm-4 form-group">
                                            <label>अवशेष (संग्रह शुल्क - भुगतान) - स्वतः गणना</label>
                                            <input type="number" step="0.01" name="balance" id="balance"
                                                   class="form-control readonly-field" readonly
                                                   value="<?php echo v_s('balance', $edit_daily, ''); ?>">
                                            <div id="balance_display" style="margin-top:5px;"></div>
                                        </div>
                                    </div>
                                    <div class="row mt-2 mb-4">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-danger btn-lg">
                                                डेटा सहेजें
                                            </button>
                                        </div>
                                    </div>
                                    <h5>(III) दिनांकवार डेटा देखें
                                        (<span id="preview_district_name">
                                        <?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>)
                                    </h5>
                                    <div class="row align-items-end">
                                        <div class="col-sm-3 form-group">
                                            <label>प्रारम्भ दिनांक</label>
                                            <input type="date" id="date_from" class="form-control"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                            <small class="text-muted">प्रारम्भ दिनांक</small>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>समाप्ति दिनांक</label>
                                            <input type="date" id="date_to" class="form-control"
                                                   value="<?php echo date('Y-m-d'); ?>">
                                            <small class="text-muted">समाप्ति दिनांक</small>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <button type="button" class="btn btn-warning btn-lg"
                                                    onclick="loadPastData();">
                                                डेटा देखें
                                            </button>
                                        </div>
                                    </div>
                                    <div id="past_section">
                                        <h6 id="past_title" style="margin:8px 0; font-weight:bold; color:#333;"></h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover">
                                                <thead class="past-thead">
                                                <tr>
                                                    <th>दिनांक</th>
                                                    <th>जनपद</th>
                                                    <th>95 "क" बकाया (लाख)</th>
                                                    <th>वैतनिक</th>
                                                    <th>कमीशन</th>
                                                    <th>योग</th>
                                                    <th>कुल वसूली (लाख)</th>
                                                    <th>कुल संग्रह शुल्क (लाख)</th>
                                                    <th>दैनिक वसूली (लाख)</th>
                                                    <th>दैनिक संग्रह शुल्क (लाख)</th>
                                                    <th>दैनिक भुगतान (लाख)</th>
                                                    <th>अवशेष (लाख)</th>
                                                    <th>अंतिम अपडेट</th>
                                                    <th>स्थिति</th>
                                                    <th>DR टिप्पणी</th>
                                                    <th id="action_col_head" style="display:none;">कार्यवाही</th>
                                                </tr>
                                                </thead>
                                                <tbody id="past_body"></tbody>
                                            </table>
                                        </div>
                                        <p id="past_nodata" class="text-danger font-weight-bold"
                                           style="display:none;">
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
    </div>

    <script>
        var isSadmin          = <?php echo ($is_sadmin || $is_dr) ? 'true' : 'false'; ?>;
        var isDr              = <?php echo $is_dr ? 'true' : 'false'; ?>;
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
                    fixedDistrictName = sel.options[sel.selectedIndex].text;
                    document.getElementById('preview_district_name').textContent = fixedDistrictName;
                    setField('bakaya_95k',          s.bakaya_95k           || '');
                    setField('amin_vetanik',         s.amin_vetanik         || '');
                    setField('amin_commission',      s.amin_commission      || '');
                    setField('amin_total',           s.amin_total           || '');
                    setField('total_recovery',       s.total_recovery       || '');
                    setField('total_collection_fee', s.total_collection_fee || '');
                    setField('daily_recovery_95k',   d.daily_recovery_95k   || '');
                    setField('daily_collection_fee', d.daily_collection_fee || '');
                    setField('daily_payment',        d.daily_payment        || '');
                    setField('balance',              d.balance              || '');
                    calcBalance();
                    document.getElementById('past_section').style.display = 'none';
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

        function calcAminTotal() {
            var v = parseInt(document.getElementById('amin_vetanik').value)    || 0;
            var c = parseInt(document.getElementById('amin_commission').value) || 0;
            document.getElementById('amin_total').value = v + c;
        }

        function calcBalance() {
            var fee = parseFloat(document.getElementById('daily_collection_fee').value) || 0;
            var pay = parseFloat(document.getElementById('daily_payment').value)        || 0;
            var bal = (fee - pay).toFixed(2);
            document.getElementById('balance').value = bal;
            var disp = document.getElementById('balance_display');
            if (parseFloat(bal) >= 0) {
                disp.innerHTML = '<span class="balance-display">अवशेष: ' + bal + ' लाख</span>';
            } else {
                disp.innerHTML = '<span class="balance-display negative">अवशेष: ' + bal + ' लाख (ऋणात्मक)</span>';
            }
        }

        function updatePreview() {}

        function loadPastData() {
            var dateFrom = document.getElementById('date_from').value;
            var dateTo   = document.getElementById('date_to').value;
            if (!dateFrom || !dateTo) { alert('कृपया प्रारम्भ और समाप्ति दिनांक चुनें।'); return; }
            if (dateFrom > dateTo) { alert('प्रारम्भ दिनांक, समाप्ति दिनांक से पहले होनी चाहिए।'); return; }
            var url = '?ajax_preview=1&date_from=' + dateFrom + '&date_to=' + dateTo;
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
                    tbody.innerHTML      = '';
                    sec.style.display    = 'block';
                    nodata.style.display = 'none';
                    var f = dateFrom.split('-'), t = dateTo.split('-');
                    title.textContent = fixedDistrictName + ' — दिनांक: ' + f[2]+'-'+f[1]+'-'+f[0] + ' से ' + t[2]+'-'+t[1]+'-'+t[0];
                    var actionHead = document.getElementById('action_col_head');
                    if (actionHead) actionHead.style.display = isDr ? 'table-cell' : 'none';

                    if (!rows || rows.length === 0) {
                        nodata.style.display = 'block';
                        return;
                    }
                    rows.forEach(function (r, idx) {
                        var d = (r.entry_date || '').split('-');
                        var displayDate = d.length === 3 ? d[2]+'-'+d[1]+'-'+d[0] : (r.entry_date || '-');

                        var st = r.status || 'pending';
                        var stBadge = st === 'approved'
                            ? '<span class="badge badge-success">स्वीकृत</span>'
                            : (st === 'rejected'
                                ? '<span class="badge badge-danger">अस्वीकृत</span>'
                                : '<span class="badge badge-warning">लंबित</span>');

                        var remarkDisp = r.dr_remark ? r.dr_remark : '-';

                        var actionCell = '';
                        if (isDr) {
                            actionCell =
                                '<td>' +
                                '<textarea id="finance_remark_' + idx + '" class="form-control form-control-sm mb-1" rows="2" placeholder="टिप्पणी (वैकल्पिक)" style="min-width:150px;">' + (r.dr_remark || '') + '</textarea>' +
                                '<button class="btn btn-success btn-sm mr-1 mb-1" onclick="submitFinanceReview(\'' + dateFrom + '\',\'' + dateTo + '\',\'approved\',' + idx + ')">✔ स्वीकृत</button>' +
                                '<button class="btn btn-danger btn-sm mb-1" onclick="submitFinanceReview(\'' + dateFrom + '\',\'' + dateTo + '\',\'rejected\',' + idx + ')">✘ अस्वीकृत</button>' +
                                '</td>';
                        }

                        tbody.innerHTML +=
                            '<tr>' +
                            '<td>' + displayDate                                    + '</td>' +
                            '<td>' + (r.district_name        || '-')     + '</td>' +
                            '<td>' + (r.bakaya_95k            || '0.00') + '</td>' +
                            '<td>' + (r.amin_vetanik          || '0')    + '</td>' +
                            '<td>' + (r.amin_commission       || '0')    + '</td>' +
                            '<td>' + (r.amin_total            || '0')    + '</td>' +
                            '<td>' + (r.total_recovery        || '0.00') + '</td>' +
                            '<td>' + (r.total_collection_fee  || '0.00') + '</td>' +
                            '<td>' + (r.daily_recovery_95k    || '0.00') + '</td>' +
                            '<td>' + (r.daily_collection_fee  || '0.00') + '</td>' +
                            '<td>' + (r.daily_payment         || '0.00') + '</td>' +
                            '<td>' + (r.balance               || '0.00') + '</td>' +
                            '<td>' + (r.updated_at            || '-')    + '</td>' +
                            '<td>' + stBadge                             + '</td>' +
                            '<td>' + remarkDisp                          + '</td>' +
                            (isDr ? actionCell : '') +
                            '</tr>';
                    });
                } catch (e) { console.error(e); }
            };
            xhr.send();
        }
        function submitFinanceReview(dateFrom, dateTo, status, idx) {
            var remark = '';
            var el = document.getElementById('finance_remark_' + idx);
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
                    if (res.ok) loadPastData();
                } catch (e) { alert('त्रुटि हुई।'); }
            };
            var body = 'id=dr_review_finance'
                + '&district_id=' + currentDistrictId
                + '&date_from='   + encodeURIComponent(dateFrom)
                + '&date_to='     + encodeURIComponent(dateTo)
                + '&status='      + status
                + '&dr_remark='   + encodeURIComponent(remark);
            xhr.send(body);
        }

        document.addEventListener('DOMContentLoaded', function () {
            calcBalance();
            loadPastData();
        });
    </script>
<?php
page_footer_start();
page_footer_end();
?>