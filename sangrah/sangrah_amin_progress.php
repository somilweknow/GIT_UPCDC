<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
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

function load_amin_rows($district_id, $entry_date) {
    $district_id = intval($district_id);
    $entry_date  = mysqli_real_escape_string($GLOBALS['db'], $entry_date);
    $rows = [];
    $res  = execute_query("
        SELECT s.*,
               d.monthly_recovery_count, d.monthly_recovery_amount,
               d.cumulative_recovery_count, d.cumulative_recovery_amount,
               d.collection_fee, d.deposited_amount, d.action_taken
        FROM sangrah_amin_target_static s
        LEFT JOIN sangrah_amin_daily_progress d
               ON d.district_id = s.district_id
              AND d.amin_name   = s.amin_name
              AND d.entry_date  = '$entry_date'
        WHERE s.district_id = $district_id
        ORDER BY s.amin_type, s.mandal_name, s.amin_name");
    while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
    return $rows;
}

if ($is_sadmin && isset($_GET['ajax_district_data'])) {
    header('Content-Type: application/json; charset=utf-8');
    $sel_dist = intval($_GET['selected_district'] ?? 0);
    echo json_encode($sel_dist > 0 ? load_amin_rows($sel_dist, $today) : [], JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_GET['ajax_date_rows'])) {
    header('Content-Type: application/json; charset=utf-8');
    $p_date   = mysqli_real_escape_string($db, $_GET['p_date'] ?? '');
    $sel_dist = $is_sadmin ? intval($_GET['selected_district'] ?? $user_district_id) : $user_district_id;
    echo json_encode((!empty($p_date) && $sel_dist > 0) ? load_amin_rows($sel_dist, $p_date) : [], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['id'] ?? '') === 'save_amin') {
    $district_id      = $is_sadmin ? intval($_POST['district_id'] ?? $user_district_id) : $user_district_id;
    $user_district_id = $district_id;
    $entry_date = $today;
    $mandal_esc    = mysqli_real_escape_string($db, trim($_POST['mandal_name']        ?? ''));
    $amin_esc      = mysqli_real_escape_string($db, trim($_POST['amin_name']          ?? ''));
    $amin_type     = in_array($_POST['amin_type'] ?? '', ['vetanik','commission']) ? $_POST['amin_type'] : 'vetanik';
    $monthly_tgt   = floatval($_POST['monthly_target']          ?? 0);
    $cumulative_tgt= floatval($_POST['cumulative_target']       ?? 0);
    $alloc_count   = intval($_POST['allocated_bakaya_count']    ?? 0);
    $alloc_amount  = floatval($_POST['allocated_bakaya_amount'] ?? 0);
    $mrc  = intval($_POST['monthly_recovery_count']       ?? 0);
    $mra  = floatval($_POST['monthly_recovery_amount']    ?? 0);
    $crc  = intval($_POST['cumulative_recovery_count']    ?? 0);
    $cra  = floatval($_POST['cumulative_recovery_amount'] ?? 0);
    $cf   = floatval($_POST['collection_fee']             ?? 0);
    $dep  = floatval($_POST['deposited_amount']           ?? 0);
    $act  = mysqli_real_escape_string($db, trim($_POST['action_taken'] ?? ''));
    if (empty($amin_esc)) {
        $msg = '<div class="alert alert-danger">अमीन का नाम आवश्यक है।</div>';
    } else {
        $r1 = execute_query("INSERT INTO sangrah_amin_target_static
            (district_id, mandal_name, amin_name, amin_type,
             monthly_target, cumulative_target,
             allocated_bakaya_count, allocated_bakaya_amount,
             updated_at, updated_by)
            VALUES ($district_id, '$mandal_esc', '$amin_esc', '$amin_type',
                    $monthly_tgt, $cumulative_tgt, $alloc_count, $alloc_amount,
                    NOW(), $current_user_sno)
            ON DUPLICATE KEY UPDATE
                mandal_name             = VALUES(mandal_name),
                amin_type               = VALUES(amin_type),
                monthly_target          = VALUES(monthly_target),
                cumulative_target       = VALUES(cumulative_target),
                allocated_bakaya_count  = VALUES(allocated_bakaya_count),
                allocated_bakaya_amount = VALUES(allocated_bakaya_amount),
                updated_at              = NOW(),
                updated_by              = $current_user_sno");
        $r2 = execute_query("INSERT INTO sangrah_amin_daily_progress
            (district_id, amin_name, entry_date,
             monthly_recovery_count, monthly_recovery_amount,
             cumulative_recovery_count, cumulative_recovery_amount,
             collection_fee, deposited_amount, action_taken)
            VALUES ($district_id, '$amin_esc', '$entry_date',
                    $mrc, $mra, $crc, $cra, $cf, $dep, '$act')
            ON DUPLICATE KEY UPDATE
                monthly_recovery_count     = VALUES(monthly_recovery_count),
                monthly_recovery_amount    = VALUES(monthly_recovery_amount),
                cumulative_recovery_count  = VALUES(cumulative_recovery_count),
                cumulative_recovery_amount = VALUES(cumulative_recovery_amount),
                collection_fee             = VALUES(collection_fee),
                deposited_amount           = VALUES(deposited_amount),
                action_taken               = VALUES(action_taken)");
        if ($r1 && $r2)
            $success = '<div class="alert alert-success">"' . htmlspecialchars(trim($_POST['amin_name']), ENT_QUOTES) . '" का डेटा ('
                . date('d-m-Y', strtotime($entry_date)) . ') Data Saved Successfully.</div>';
        else
            $msg = '<div class="alert alert-danger">Error. Please try again.</div>';
    }
}
$amin_rows = load_amin_rows($user_district_id, $today);

function v_s($key, $arr, $default = '') {
    return isset($arr[$key]) ? htmlspecialchars($arr[$key], ENT_QUOTES, 'UTF-8') : $default;
}

function render_amin_form($r, $district_id, $today, $is_new = false) {
    $v = function($key, $default = '') use ($r) {
        return isset($r[$key]) ? htmlspecialchars($r[$key], ENT_QUOTES, 'UTF-8') : $default;
    };
    $amin_label = $is_new ? 'नया अमीन' : htmlspecialchars($r['amin_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $type_v     = $v('amin_type', 'vetanik');
    ob_start(); ?>
    <div class="amin-section<?php echo $is_new ? ' new-amin' : ''; ?>">
        <form action="" method="post" accept-charset="UTF-8">
            <input type="hidden" name="id"          value="save_amin">
            <input type="hidden" name="district_id" value="<?php echo intval($district_id); ?>">
            <input type="hidden" name="entry_date"  value="<?php echo $today; ?>">
            <div class="amin-section-header">
                <span class="amin-title"><?php echo $amin_label; ?></span>
                <?php if ($is_new): ?>
                    <button type="button" class="btn btn-sm btn-outline-light ml-2"
                            onclick="this.closest('.amin-section').remove();">✕ रद्द</button>
                <?php endif; ?>
            </div>
            <div class="amin-section-body">
                <h5>(I) स्थायी विवरण</h5>
                <div class="row">
                    <div class="col-sm-3 form-group">
                        <label>नाम मण्डल</label>
                        <input type="text" name="mandal_name" class="form-control" placeholder="मण्डल" value="<?php echo $v('mandal_name'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>अमीन का नाम <span style="color:red;">*</span></label>
                        <input type="text" name="amin_name" class="form-control"
                               placeholder="अमीन नाम" required
                            <?php echo !$is_new ? 'readonly style="background:#f0f0f0;"' : ''; ?>
                               value="<?php echo $v('amin_name'); ?>"
                               oninput="updateSectionTitle(this)">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>प्रकार</label>
                        <select name="amin_type" class="form-control">
                            <option value="vetanik"    <?php echo $type_v==='vetanik'    ? 'selected':''; ?>>वैतनिक</option>
                            <option value="commission" <?php echo $type_v==='commission' ? 'selected':''; ?>>कमीशन</option>
                        </select>
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>वैतनिक/कमीशन राशि</label>
                        <input type="number" step="0.01" name="monthly_target" class="form-control" placeholder="0.00" value="<?php echo $v('monthly_target','0.00'); ?>">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>क्रमिक लक्ष्य</label>
                        <input type="number" step="0.01" name="cumulative_target" class="form-control" placeholder="0.00" value="<?php echo $v('cumulative_target','0.00'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 form-group">
                        <label>आवंटित बकाया — संख्या</label>
                        <input type="number" step="1" min="0" name="allocated_bakaya_count" class="form-control" placeholder="0" value="<?php echo $v('allocated_bakaya_count','0'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>आवंटित बकाया — धनराशि लाख रु.</label>
                        <input type="number" step="0.01" name="allocated_bakaya_amount" class="form-control" placeholder="0.00" value="<?php echo $v('allocated_bakaya_amount','0.00'); ?>">
                    </div>
                </div>
                <h5>(II) दैनिक वसूली प्रगति (<?php echo date('d-m-Y'); ?>)</h5>
                <div class="row">
                    <div class="col-sm-3 form-group">
                        <label>मासिक वसूली — संख्या</label>
                        <input type="number" step="1" min="0" name="monthly_recovery_count" class="form-control" placeholder="0" value="<?php echo $v('monthly_recovery_count','0'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>मासिक वसूली — धनराशि लाख रु.</label>
                        <input type="number" step="0.01" name="monthly_recovery_amount" class="form-control" placeholder="0.00" value="<?php echo $v('monthly_recovery_amount','0.00'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>क्रमिक वसूली — संख्या</label>
                        <input type="number" step="1" min="0" name="cumulative_recovery_count" class="form-control" placeholder="0" value="<?php echo $v('cumulative_recovery_count','0'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>क्रमिक वसूली — धनराशि लाख रु.</label>
                        <input type="number" step="0.01" name="cumulative_recovery_amount" class="form-control" placeholder="0.00" value="<?php echo $v('cumulative_recovery_amount','0.00'); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 form-group">
                        <label>क्रमिक वसूल संग्रह शुल्क लाख रु.</label>
                        <input type="number" step="0.01" name="collection_fee" class="form-control" placeholder="0.00" value="<?php echo $v('collection_fee','0.00'); ?>">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>संग्रह निधि में जमा लाख रु.</label>
                        <input type="number" step="0.01" name="deposited_amount" class="form-control" placeholder="0.00" value="<?php echo $v('deposited_amount','0.00'); ?>">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>लक्ष्य से कम वसूली वाले अमीन के विरुद्ध कृत कार्यवाही</label>
                        <textarea name="action_taken" class="form-control" rows="2" placeholder="कार्यवाही का विवरण"><?php echo $v('action_taken'); ?></textarea>
                    </div>
                </div>
                <div class="row mt-1 mb-2">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-danger">
                            💾 <?php echo $is_new ? 'Add Ameen and Submit' : '"'.$amin_label.'" का Submit'; ?>
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
        .step h4  { color:#fff; background:#FF8E00; border-radius:15px; padding:10px 10px 6px 20px; }
        .step h5  { color:#000; background:#FFDB44; border-radius:15px; padding:10px 10px 6px 20px; }
        .info-box { background:#fff8e1; border-left:4px solid #FF8E00; padding:10px 15px; border-radius:5px; font-size:13px; color:#555; }
        .readonly-field { background:#f0f0f0 !important; font-weight:bold; cursor:not-allowed; }
        .district-badge { background:#FF8E00; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
        .sadmin-badge   { background:#6f42c1; color:#fff; border-radius:20px; padding:3px 12px; font-size:13px; font-weight:bold; margin-left:8px; }
        .amin-section         { border:2px solid #FF8E00; border-radius:12px; margin-bottom:20px; overflow:hidden; }
        .amin-section-header  { background:#FF8E00; color:#fff; padding:10px 16px; font-size:15px; font-weight:bold; display:flex; align-items:center; justify-content:space-between; }
        .amin-section-body    { padding:15px 15px 5px; background:#fffaf5; }
        .amin-section.new-amin .amin-section-header { background:#28a745; }
        .amin-section.new-amin { border-color:#28a745; }
        .prog-table     { border:2px solid #FF8E00 !important; border-collapse:collapse; }
        .prog-table th  { background:#FF8E00 !important; color:#fff !important; text-align:center; vertical-align:middle; font-size:11px; white-space:nowrap; padding:6px 4px; border:1px solid #e07000 !important; }
        .prog-table td  { text-align:center; vertical-align:middle; font-size:12px; padding:5px 4px; border:1px solid #FF8E00 !important; }
        .prog-table tbody tr:hover td { background:#fff3e0; }
        .prog-table tbody td:last-child,
        .prog-table thead th:last-child { display:table-cell !important; }
        #district_loading { display:none; color:#FF8E00; font-size:13px; margin-top:4px; }
        #prog_loading     { display:none; color:#FF8E00; font-size:13px; }
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
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="text-center mb-3">
                             <u><strong>प्रारूप-5</strong></u><br>
                            <h4><u>वैतनिक/कमीशन अमीनवार बकाया की मासिक/क्रमिक वसूली प्रगति</u></h4>
                        </div>
                        <?php echo $msg; echo $success; ?>
                        <div class="step">
                            <marquee style="font-size:16px; color:red;">
                                नोट: प्रत्येक अमीन की दैनिक वसूली प्रगति प्रतिदिन सही-सही भरें। धनराशि लाख रुपये में भरें।
                            </marquee><br>
                            <h4>1. जनपद एवं दिनांक</h4>
                            <div class="col-sm-12">
                                <div class="row align-items-center">
                                    <div class="col-sm-4 form-group">
                                        <label>जनपद <?php if($is_sadmin): ?><span class="sadmin-badge">सुपर एडमिन</span><?php endif; ?></label>
                                        <?php if ($is_sadmin): ?>
                                            <select id="sadmin_district_select" class="form-control" onchange="sadminDistrictChange(this.value);">
                                                <?php foreach ($all_districts as $dist): ?>
                                                    <option value="<?php echo intval($dist['sno']); ?>" <?php echo (intval($dist['sno']) === $user_district_id) ? 'selected' : ''; ?>>
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
                                        <input type="text" class="form-control readonly-field" value="<?php echo date('d-m-Y'); ?>" readonly>
                                    </div>
                                    <div class="col-sm-5 form-group">
                                        <div class="info-box">
                                            प्रत्येक अमीन का फॉर्म अलग-अलग सहेजा जाता है।
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="step">
                            <h4>2. अमीनवार विवरण एवं आज की वसूली प्रगति
                                (<span id="entry_district_name"><?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?></span>
                                — <?php echo date('d-m-Y'); ?>)
                            </h4>
                            <div class="col-sm-12" id="amin_forms_wrap">
                                <?php if (empty($amin_rows)): ?>
                                    <?php echo render_amin_form([], $user_district_id, $today, true); ?>
                                <?php else: ?>
                                    <?php foreach ($amin_rows as $r): ?>
                                        <?php echo render_amin_form($r, $user_district_id, $today, false); ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <div id="new_amin_container"></div>
                                <div class="row mt-2 mb-3">
                                    <div class="col-sm-12">
                                        <button type="button" class="btn btn-success btn-lg" onclick="addNewAminForm();">
                                            + नया अमीन जोड़ें
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
     
 </div> <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                             <!-- ✅ EXPORT BUTTON -->
                            <div>
                                <button class="btn btn-danger" onclick="exportToPDF()">
                                    📄 Export PDF
                                </button>
                            </div>
                            <div class="text-center mb-3">
                             <u><strong>प्रारूप-5</strong></u><br>
                            <h4><u>वैतनिक/कमीशन अमीनवार बकाया की मासिक/क्रमिक वसूली प्रगति</u></h4>
                        </div>
                        <div class="step">
                            <h4>3. दिनांकवार अमीन वसूली प्रगति देखें
                                (<span id="view_district_name"><?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?></span>)
                            </h4>
                            <div class="col-sm-12">

                                <div class="row align-items-end mb-3">
                                    <div class="col-sm-3 form-group">
                                        <label>दिनांक चुनें</label>
                                        <input type="date" id="view_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                        <small class="text-muted">आज या कोई भी पूर्व दिनांक।</small>
                                    </div>
                                    <div class="col-sm-4 form-group">
                                        <button type="button" class="btn btn-warning btn-lg" onclick="loadProgTable();">
                                           Search
                                        </button>
                                        <span id="prog_loading" class="ml-2">⏳ Loading...</span>
                                    </div>
                                </div>

                                <div id="prog_section" style="display:none;">
                                    <h6 id="prog_title" style="font-weight:bold; color:#333; margin-bottom:8px;"></h6>
                                    <div class="table-responsive"  id="printArea">
                                        <table class="table table-bordered table-hover prog-table">
                                            <thead>
                                            <tr>
                                                <th rowspan="2">क0 स0</th>
                                                <th rowspan="2">नाम मण्डल</th>
                                                <th rowspan="2">नाम अमीन</th>
                                                <th rowspan="2">वैतनिक/<br>कमीशन</th>
                                                <th rowspan="2">क्रमिक<br>लक्ष्य</th>
                                                <th colspan="2">आवंटित बकाया</th>
                                                <th colspan="2">मासिक वसूली</th>
                                                <th colspan="2">क्रमिक वसूली</th>
                                                <th rowspan="2">संग्रह<br>शुल्क</th>
                                                <th rowspan="2">निधि<br>जमा</th>
                                                <th rowspan="2">कार्यवाही</th>
                                            </tr>
                                            <tr>
                                                <th>संख्या</th><th>धन0</th>
                                                <th>संख्या</th><th>धन0</th>
                                                <th>संख्या</th><th>धन0</th>
                                            </tr>
                                            </thead>
                                            <tbody id="prog_tbody"></tbody>
                                        </table>
                                    </div>
                                    <div id="prog_nodata" class="text-danger font-weight-bold" style="display:none;">
                                        इस जनपद में अभी कोई अमीन नहीं है।
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

        function updateSectionTitle(input) {
            var hdr = input.closest('.amin-section').querySelector('.amin-title');
            if (hdr) hdr.textContent = input.value || 'नया अमीन';
        }
        function addNewAminForm() {
            var distId  = currentDistrictId;
            var today   = "<?php echo $today; ?>";
            var todayFt = "<?php echo date('d-m-Y'); ?>";
            var html =
                '<div class="amin-section new-amin">' +
                '<div class="amin-section-header"><span class="amin-title">नया अमीन</span>' +
                '<button type="button" class="btn btn-sm btn-outline-light ml-2" onclick="this.closest(\'.amin-section\').remove();">✕ रद्द</button></div>' +
                '<form action="" method="post" accept-charset="UTF-8">' +
                '<input type="hidden" name="id" value="save_amin">' +
                '<input type="hidden" name="district_id" value="' + distId + '">' +
                '<input type="hidden" name="entry_date" value="' + today + '">' +
                '<div class="amin-section-body">' +
                '<h5>(I) स्थायी विवरण</h5>' +
                '<div class="row">' +
                fld('नाम मण्डल ', 'text', 'mandal_name', '', '3', 'मण्डल', false) +
                fld('अमीन का नाम  *', 'text', 'amin_name', '', '3', 'अमीन नाम', false, 'required oninput="updateSectionTitle(this)"') +
                '<div class="col-sm-2 form-group"><label>प्रकार</label>' +
                '<select name="amin_type" class="form-control"><option value="vetanik">वैतनिक</option><option value="commission">कमीशन</option></select></div>' +
                fld('वैतनिक/कमीशन राशि ', 'number', 'monthly_target', '0', '2', '0.00', false) +
                fld('क्रमिक लक्ष्य ', 'number', 'cumulative_target', '0', '2', '0.00', false) +
                '</div><div class="row">' +
                fld('आवंटित बकाया संख्या ', 'number', 'allocated_bakaya_count', '0', '3', '0', true) +
                fld('आवंटित बकाया धनराशि लाख रु. ', 'number', 'allocated_bakaya_amount', '0', '3', '0.00', false) +
                '</div>' +
                '<h5>(II) दैनिक वसूली प्रगति (' + todayFt + ')</h5>' +
                '<div class="row">' +
                fld('मासिक संख्या ', 'number', 'monthly_recovery_count', '0', '3', '0', true) +
                fld('मासिक धनराशि ', 'number', 'monthly_recovery_amount', '0', '3', '0.00', false) +
                fld('क्रमिक संख्या ', 'number', 'cumulative_recovery_count', '0', '3', '0', true) +
                fld('क्रमिक धनराशि ', 'number', 'cumulative_recovery_amount', '0', '3', '0.00', false) +
                '</div><div class="row">' +
                fld('संग्रह शुल्क लाख रु. ', 'number', 'collection_fee', '0', '3', '0.00', false) +
                fld('निधि जमा लाख रु. ', 'number', 'deposited_amount', '0', '3', '0.00', false) +
                '<div class="col-sm-6 form-group"><label>कृत कार्यवाही</label>' +
                '<textarea name="action_taken" class="form-control" rows="2" placeholder="कार्यवाही"></textarea></div>' +
                '</div>' +
                '<div class="row mt-1 mb-2"><div class="col-sm-12">' +
                '<button type="submit" class="btn btn-danger">💾 अमीन जोड़ें और सहेजें</button>' +
                '</div></div></div></form></div>';
            document.getElementById('new_amin_container').insertAdjacentHTML('beforeend', html);
            var forms = document.getElementById('new_amin_container').querySelectorAll('.amin-section');
            if (forms.length) forms[forms.length-1].scrollIntoView({behavior:'smooth', block:'start'});
        }

        function fld(lbl, type, name, val, col, ph, isInt, extra) {
            var step = isInt ? '1' : '0.01';
            var min  = (type === 'number') ? ' min="0"' : '';
            return '<div class="col-sm-' + col + ' form-group"><label>' + lbl + '</label>' +
                '<input type="' + type + '" step="' + step + '"' + min + ' name="' + name + '"' +
                ' class="form-control" value="' + val + '" placeholder="' + ph + '"' +
                (extra ? ' ' + extra : '') + '></div>';
        }

        function sadminDistrictChange(districtId) {
            if (!isSadmin || !districtId) return;
            currentDistrictId = parseInt(districtId);
            document.getElementById('district_loading').style.display = 'block';
            var sel = document.getElementById('sadmin_district_select');
            fixedDistrictName = sel.options[sel.selectedIndex].text;
            document.getElementById('entry_district_name').textContent = fixedDistrictName;
            document.getElementById('view_district_name').textContent  = fixedDistrictName;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '?ajax_district_data=1&selected_district=' + districtId, true);
            xhr.onload = function () {
                document.getElementById('district_loading').style.display = 'none';
                if (xhr.status !== 200) return;
                try {
                    var rows = JSON.parse(xhr.responseText);
                    var wrap = document.getElementById('amin_forms_wrap');
                    wrap.querySelectorAll('.amin-section').forEach(function(el){el.remove();});
                    document.getElementById('new_amin_container').innerHTML = '';
                    var container = document.getElementById('new_amin_container');
                    if (rows.length === 0) {
                        container.insertAdjacentHTML('beforebegin', buildExistingAminForm({}, districtId, true));
                    } else {
                        rows.forEach(function(r) {
                            container.insertAdjacentHTML('beforebegin', buildExistingAminForm(r, districtId, false));
                        });
                    }
                    document.getElementById('prog_section').style.display = 'none';
                    loadProgTable();
                } catch(e) { console.error(e); }
            };
            xhr.send();
        }

        function buildExistingAminForm(r, distId, isNew) {
            var today   = "<?php echo $today; ?>";
            var todayFt = "<?php echo date('d-m-Y'); ?>";
            var aminName = r.amin_name || '';
            var typeV    = (r.amin_type === 'commission') ? '' : ' selected';
            var typeC    = (r.amin_type === 'commission') ? ' selected' : '';
            var hdrStyle = isNew ? 'background:#28a745;' : '';
            var borderStyle = isNew ? 'border-color:#28a745;' : '';
            var readonlyAttr = isNew ? '' : 'readonly style="background:#f0f0f0;"';
            var oninput  = isNew ? 'oninput="updateSectionTitle(this)"' : '';
            var cancelBtn = isNew ? '<button type="button" class="btn btn-sm btn-outline-light ml-2" onclick="this.closest(\'.amin-section\').remove();">✕ रद्द</button>' : '';
            var hdrLabel = isNew ? 'नया अमीन' : aminName;

            return '<div class="amin-section" style="' + borderStyle + '">' +
                '<div class="amin-section-header" style="' + hdrStyle + '"><span class="amin-title">' + hdrLabel + '</span>' + cancelBtn + '</div>' +
                '<form action="" method="post" accept-charset="UTF-8">' +
                '<input type="hidden" name="id" value="save_amin">' +
                '<input type="hidden" name="district_id" value="' + distId + '">' +
                '<input type="hidden" name="entry_date" value="' + today + '">' +
                '<div class="amin-section-body">' +
                '<h5>(I) स्थायी विवरण</h5>' +
                '<div class="row">' +
                '<div class="col-sm-3 form-group"><label>नाम मण्डल </label><input type="text" name="mandal_name" class="form-control" value="' + (r.mandal_name||'') + '" placeholder="मण्डल"></div>' +
                '<div class="col-sm-3 form-group"><label>अमीन का नाम </label><input type="text" name="amin_name" class="form-control" value="' + aminName + '" placeholder="अमीन नाम" ' + readonlyAttr + ' ' + oninput + '></div>' +
                '<div class="col-sm-2 form-group"><label>प्रकार</label><select name="amin_type" class="form-control"><option value="vetanik"' + typeV + '>वैतनिक</option><option value="commission"' + typeC + '>कमीशन</option></select></div>' +
                '<div class="col-sm-2 form-group"><label>वैतनिक/कमीशन </label><input type="number" step="0.01" name="monthly_target" class="form-control" value="' + (r.monthly_target||'0') + '"></div>' +
                '<div class="col-sm-2 form-group"><label>क्रमिक लक्ष्य </label><input type="number" step="0.01" name="cumulative_target" class="form-control" value="' + (r.cumulative_target||'0') + '"></div>' +
                '</div><div class="row">' +
                '<div class="col-sm-3 form-group"><label>बकाया संख्या </label><input type="number" step="1" min="0" name="allocated_bakaya_count" class="form-control" value="' + (r.allocated_bakaya_count||'0') + '"></div>' +
                '<div class="col-sm-3 form-group"><label>बकाया धनराशि </label><input type="number" step="0.01" name="allocated_bakaya_amount" class="form-control" value="' + (r.allocated_bakaya_amount||'0') + '"></div>' +
                '</div>' +
                '<h5>(II) दैनिक वसूली प्रगति (' + todayFt + ')</h5>' +
                '<div class="row">' +
                '<div class="col-sm-3 form-group"><label>मासिक संख्या </label><input type="number" step="1" min="0" name="monthly_recovery_count" class="form-control" value="' + (r.monthly_recovery_count||'0') + '"></div>' +
                '<div class="col-sm-3 form-group"><label>मासिक धनराशि </label><input type="number" step="0.01" name="monthly_recovery_amount" class="form-control" value="' + (r.monthly_recovery_amount||'0') + '"></div>' +
                '<div class="col-sm-3 form-group"><label>क्रमिक संख्या </label><input type="number" step="1" min="0" name="cumulative_recovery_count" class="form-control" value="' + (r.cumulative_recovery_count||'0') + '"></div>' +
                '<div class="col-sm-3 form-group"><label>क्रमिक धनराशि </label><input type="number" step="0.01" name="cumulative_recovery_amount" class="form-control" value="' + (r.cumulative_recovery_amount||'0') + '"></div>' +
                '</div><div class="row">' +
                '<div class="col-sm-3 form-group"><label>संग्रह शुल्क </label><input type="number" step="0.01" name="collection_fee" class="form-control" value="' + (r.collection_fee||'0') + '"></div>' +
                '<div class="col-sm-3 form-group"><label>निधि जमा </label><input type="number" step="0.01" name="deposited_amount" class="form-control" value="' + (r.deposited_amount||'0') + '"></div>' +
                '<div class="col-sm-6 form-group"><label>कृत कार्यवाही </label><textarea name="action_taken" class="form-control" rows="2">' + (r.action_taken||'') + '</textarea></div>' +
                '</div>' +
                '<div class="row mt-1 mb-2"><div class="col-sm-12">' +
                '<button type="submit" class="btn btn-danger">💾 ' + (isNew ? 'अमीन जोड़ें और सहेजें' : '"' + aminName + '" का Submit') + '</button>' +
                '</div></div></div></form></div>';
        }

        function loadProgTable() {
            var vdate = document.getElementById('view_date').value;
            if (!vdate) return;
            document.getElementById('prog_loading').style.display = 'inline';
            var url = '?ajax_date_rows=1&p_date=' + vdate;
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
                    var sec    = document.getElementById('prog_section');
                    tbody.innerHTML      = '';
                    sec.style.display    = 'block';
                    nodata.style.display = 'none';
                    var p = vdate.split('-');
                    title.textContent = fixedDistrictName + ' — दिनांक: ' + p[2] + '-' + p[1] + '-' + p[0];
                    if (!rows || rows.length === 0) {
                        nodata.style.display = 'block';
                        return;
                    }

                    rows.forEach(function(r, i) {
                        var typeLabel = r.amin_type === 'vetanik' ? 'वैतनिक' : 'कमीशन';
                        var d = function(v) { return v !== null && v !== undefined && v !== '' ? v : '-'; };
                        tbody.innerHTML +=
                            '<tr>' +
                            '<td>' + (i+1) + '</td>' +
                            '<td>' + d(r.mandal_name) + '</td>' +
                            '<td><strong>' + d(r.amin_name) + '</strong></td>' +
                            '<td>' + typeLabel + '</td>' +
                            '<td>' + d(r.cumulative_target) + '</td>' +
                            '<td>' + d(r.allocated_bakaya_count) + '</td>' +
                            '<td>' + d(r.allocated_bakaya_amount) + '</td>' +
                            '<td>' + d(r.monthly_recovery_count) + '</td>' +
                            '<td>' + d(r.monthly_recovery_amount) + '</td>' +
                            '<td>' + d(r.cumulative_recovery_count) + '</td>' +
                            '<td>' + d(r.cumulative_recovery_amount) + '</td>' +
                            '<td>' + d(r.collection_fee) + '</td>' +
                            '<td>' + d(r.deposited_amount) + '</td>' +
                            '<td>' + d(r.action_taken) + '</td>' +
                            '</tr>';
                    });
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

        pdf.save("amin_progress_report.pdf");
    });
}
    </script>

<?php
page_footer_start();
page_footer_end();
?>