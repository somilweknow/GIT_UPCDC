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
    else
        $user_district_id = !empty($all_districts) ? intval($all_districts[0]['sno']) : 0;

    $session_districts = array_map('intval', array_column($all_districts, 'sno'));
} else {
    $user_district_id = !empty($session_districts) ? $session_districts[0] : 0;
    if ($user_district_id <= 0)
        die('<div class="alert alert-danger" style="margin:20px;">आपके खाते से कोई जनपद नहीं जुड़ा है।</div>');
}

$res_dn             = execute_query("SELECT district_name FROM master_district WHERE sno = $user_district_id");
$user_district_name = (mysqli_num_rows($res_dn) > 0) ? mysqli_fetch_assoc($res_dn)['district_name'] : '';

$today = date('Y-m-d');

function f_sum(...$vals) { $t = 0; foreach ($vals as $v) $t += floatval($v); return round($t, 2); }
function f_pct($num, $den) { $n = floatval($num); $d = floatval($den); return $d > 0 ? round($n * 100 / $d, 2) : 0; }
function f_disp($v) { return ($v != 0) ? $v : '-'; }

function load_today_sums($dist_id, $today) {
    $d   = intval($dist_id);
    $rps = execute_query("SELECT * FROM sangrah_pacs_bakaya_static WHERE district_id = $d");
    $ps  = mysqli_num_rows($rps) > 0 ? mysqli_fetch_assoc($rps) : [];
    $rss = execute_query("SELECT * FROM sangrah_sahkari_gram_vikas_static WHERE district_id = $d");
    $ss  = mysqli_num_rows($rss) > 0 ? mysqli_fetch_assoc($rss) : [];
    $rvs = execute_query("SELECT * FROM sangrah_vividhikaran_static WHERE district_id = $d");
    $vs  = mysqli_num_rows($rvs) > 0 ? mysqli_fetch_assoc($rvs) : [];
    $rpd = execute_query("SELECT * FROM sangrah_pacs_bakaya_daily_collection WHERE district_id = $d AND entry_date = '$today'");
    $pd  = mysqli_num_rows($rpd) > 0 ? mysqli_fetch_assoc($rpd) : [];
    $rsd = execute_query("SELECT * FROM sangrah_sahkari_gram_vikas_daily_collection WHERE district_id = $d AND entry_date = '$today'");
    $sd  = mysqli_num_rows($rsd) > 0 ? mysqli_fetch_assoc($rsd) : [];
    $rvd = execute_query("SELECT * FROM sangrah_vividhikaran_daily_collection WHERE district_id = $d AND entry_date = '$today'");
    $vd  = mysqli_num_rows($rvd) > 0 ? mysqli_fetch_assoc($rvd) : [];

    $total_bakaya   = f_sum($ps['total_bakaya'] ?? 0,             $ss['total_bakaya'] ?? 0,           $vs['total_bakaya'] ?? 0);
    $bakaya_95k     = f_sum($ps['bakaya_95k'] ?? 0,               $ss['bakaya_95k'] ?? 0,             $vs['bakaya_95k'] ?? 0);
    $recovery_95k   = f_sum($ps['recovery_95k_amount'] ?? 0,      $ss['recovery_95k'] ?? 0,           $vs['recovery_95k'] ?? 0);
    $coll_fee       = f_sum($ps['total_collection_fee'] ?? 0,     $ss['collection_fee'] ?? 0,         $vs['total_collection_fee'] ?? 0);
    $big_count      = f_sum($ps['big_defaulter_count'] ?? 0,      $ss['big_defaulters_count'] ?? 0,   $vs['big_defaulter_count'] ?? 0);
    $big_amount     = f_sum($ps['big_defaulter_amount'] ?? 0,     $ss['big_defaulters_amount'] ?? 0,  $vs['big_defaulter_amount'] ?? 0);
    $big_rec_count  = f_sum($ps['big_defaulter_recovery_count'] ?? 0,  $ss['big_defaulters_recovery_count'] ?? 0,  $vd['big_defaulter_recovery_count'] ?? 0);
    $big_rec_amount = f_sum($ps['big_defaulter_recovery_amount'] ?? 0, $ss['big_defaulters_recovery_amount'] ?? 0, $vd['big_defaulter_recovery_amount'] ?? 0);
    $daily_rec      = f_sum($pd['daily_recovery_95k'] ?? 0,   $sd['daily_recovery_95k'] ?? 0);
    $daily_fee      = f_sum($pd['daily_collection_fee'] ?? 0, $sd['daily_collection_fee'] ?? 0);

    return [
        'total_bakaya'   => $total_bakaya,
        'bakaya_95k'     => $bakaya_95k,
        'pct_aachhadit'  => f_pct($bakaya_95k, $total_bakaya),
        'recovery_95k'   => $recovery_95k,
        'pct_vasuli'     => f_pct($recovery_95k, $bakaya_95k),
        'coll_fee'       => $coll_fee,
        'big_count'      => $big_count,
        'big_amount'     => $big_amount,
        'big_rec_count'  => $big_rec_count,
        'big_rec_amount' => $big_rec_amount,
        'pct_big'        => f_pct($big_rec_amount, $big_amount),
        'daily_rec'      => $daily_rec,
        'daily_fee'      => $daily_fee,
    ];
}

if ($is_sadmin && isset($_GET['ajax_district_data'])) {
    header('Content-Type: application/json; charset=utf-8');
    $sel_dist = intval($_GET['selected_district'] ?? 0);
    echo json_encode($sel_dist > 0 ? load_today_sums($sel_dist, $today) : [], JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_GET['ajax_preview'])) {
    header('Content-Type: application/json; charset=utf-8');
    $p_date = mysqli_real_escape_string($db, $_GET['p_date'] ?? '');
    $rows   = [];

    if (!empty($p_date)) {
        if ($is_sadmin) {
            $dist_list = [intval($_GET['selected_district'] ?? $user_district_id)];
        } else {
            $dist_list = !empty($session_districts) ? $session_districts : [0];
        }
        $in   = implode(',', $dist_list);
        $data = [];

        $res_dn2 = execute_query("SELECT sno, district_name FROM master_district WHERE sno IN ($in)");
        while ($rd = mysqli_fetch_assoc($res_dn2)) {
            $data[intval($rd['sno'])] = [
                'district_name' => $rd['district_name'],
                'total_bakaya'  => 0, 'bakaya_95k'    => 0,
                'recovery_95k'  => 0, 'coll_fee'      => 0,
                'big_count'     => 0, 'big_amount'    => 0,
                'big_rec_count' => 0, 'big_rec_amount'=> 0,
                'daily_rec'     => 0, 'daily_fee'     => 0,
                'has_data'      => false,
            ];
        }

        $res_p = execute_query("
            SELECT p.district_id,
                ps.total_bakaya, ps.bakaya_95k, ps.recovery_95k_amount,
                ps.total_collection_fee, ps.big_defaulter_count, ps.big_defaulter_amount,
                ps.big_defaulter_recovery_count, ps.big_defaulter_recovery_amount,
                p.daily_recovery_95k, p.daily_collection_fee
            FROM sangrah_pacs_bakaya_daily_collection p
            LEFT JOIN sangrah_pacs_bakaya_static ps ON ps.district_id = p.district_id
            WHERE p.entry_date = '$p_date' AND p.district_id IN ($in)");
        while ($r = mysqli_fetch_assoc($res_p)) {
            $did = intval($r['district_id']);
            if (!isset($data[$did])) continue;
            $data[$did]['total_bakaya']  += floatval($r['total_bakaya']);
            $data[$did]['bakaya_95k']    += floatval($r['bakaya_95k']);
            $data[$did]['recovery_95k']  += floatval($r['recovery_95k_amount']);
            $data[$did]['coll_fee']      += floatval($r['total_collection_fee']);
            $data[$did]['big_count']     += floatval($r['big_defaulter_count']);
            $data[$did]['big_amount']    += floatval($r['big_defaulter_amount']);
            $data[$did]['big_rec_count'] += floatval($r['big_defaulter_recovery_count']);
            $data[$did]['big_rec_amount']+= floatval($r['big_defaulter_recovery_amount']);
            $data[$did]['daily_rec']     += floatval($r['daily_recovery_95k']);
            $data[$did]['daily_fee']     += floatval($r['daily_collection_fee']);
            $data[$did]['has_data']       = true;
        }

        $res_s = execute_query("
            SELECT s.district_id,
                ss.total_bakaya, ss.bakaya_95k, ss.recovery_95k,
                ss.collection_fee, ss.big_defaulters_count, ss.big_defaulters_amount,
                ss.big_defaulters_recovery_count, ss.big_defaulters_recovery_amount,
                s.daily_recovery_95k, s.daily_collection_fee
            FROM sangrah_sahkari_gram_vikas_daily_collection s
            LEFT JOIN sangrah_sahkari_gram_vikas_static ss ON ss.district_id = s.district_id
            WHERE s.entry_date = '$p_date' AND s.district_id IN ($in)");
        while ($r = mysqli_fetch_assoc($res_s)) {
            $did = intval($r['district_id']);
            if (!isset($data[$did])) continue;
            $data[$did]['total_bakaya']  += floatval($r['total_bakaya']);
            $data[$did]['bakaya_95k']    += floatval($r['bakaya_95k']);
            $data[$did]['recovery_95k']  += floatval($r['recovery_95k']);
            $data[$did]['coll_fee']      += floatval($r['collection_fee']);
            $data[$did]['big_count']     += floatval($r['big_defaulters_count']);
            $data[$did]['big_amount']    += floatval($r['big_defaulters_amount']);
            $data[$did]['big_rec_count'] += floatval($r['big_defaulters_recovery_count']);
            $data[$did]['big_rec_amount']+= floatval($r['big_defaulters_recovery_amount']);
            $data[$did]['daily_rec']     += floatval($r['daily_recovery_95k']);
            $data[$did]['daily_fee']     += floatval($r['daily_collection_fee']);
            $data[$did]['has_data']       = true;
        }

        $res_v = execute_query("
            SELECT v.district_id,
                vs.total_bakaya, vs.bakaya_95k, vs.recovery_95k,
                vs.total_collection_fee, vs.big_defaulter_count, vs.big_defaulter_amount,
                v.big_defaulter_recovery_count, v.big_defaulter_recovery_amount
            FROM sangrah_vividhikaran_daily_collection v
            LEFT JOIN sangrah_vividhikaran_static vs ON vs.district_id = v.district_id
            WHERE v.entry_date = '$p_date' AND v.district_id IN ($in)");
        while ($r = mysqli_fetch_assoc($res_v)) {
            $did = intval($r['district_id']);
            if (!isset($data[$did])) continue;
            $data[$did]['total_bakaya']  += floatval($r['total_bakaya']);
            $data[$did]['bakaya_95k']    += floatval($r['bakaya_95k']);
            $data[$did]['recovery_95k']  += floatval($r['recovery_95k']);
            $data[$did]['coll_fee']      += floatval($r['total_collection_fee']);
            $data[$did]['big_count']     += floatval($r['big_defaulter_count']);
            $data[$did]['big_amount']    += floatval($r['big_defaulter_amount']);
            $data[$did]['big_rec_count'] += floatval($r['big_defaulter_recovery_count']);
            $data[$did]['big_rec_amount']+= floatval($r['big_defaulter_recovery_amount']);
            $data[$did]['has_data']       = true;
        }

        foreach ($data as $did => $d) {
            if (!$d['has_data']) continue;
            $tb  = round($d['total_bakaya'],   2); $bk  = round($d['bakaya_95k'],     2);
            $rec = round($d['recovery_95k'],   2); $cf  = round($d['coll_fee'],        2);
            $bc  = round($d['big_count'],      2); $ba  = round($d['big_amount'],      2);
            $brc = round($d['big_rec_count'],  2); $bra = round($d['big_rec_amount'],  2);
            $dr  = round($d['daily_rec'],      2); $df  = round($d['daily_fee'],       2);
            $rows[] = [
                'district_name'  => $d['district_name'],
                'total_bakaya'   => $tb,  'bakaya_95k'    => $bk,
                'pct_aachhadit'  => f_pct($bk, $tb),
                'recovery_95k'   => $rec, 'pct_vasuli'    => f_pct($rec, $bk),
                'coll_fee'       => $cf,
                'big_count'      => $bc,  'big_amount'    => $ba,
                'big_rec_count'  => $brc, 'big_rec_amount'=> $bra,
                'pct_big'        => f_pct($bra, $ba),
                'daily_rec'      => $dr,  'daily_fee'     => $df,
            ];
        }
        usort($rows, fn($a, $b) => strcmp($a['district_name'], $b['district_name']));
    }
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    exit;
}

$f = load_today_sums($user_district_id, $today);

page_header_start();
?>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .step h4 { color:#fff; background:#FF8E00; border-radius:15px; padding:10px 10px 6px 20px; }
        .step h5 { color:#000; background:#FFDB44; border-radius:15px; padding:10px 10px 6px 20px; }
        .info-box { background:#fff8e1; border-left:4px solid #FF8E00; padding:10px 15px; border-radius:5px; font-size:13px; color:#555; }
        .readonly-field { background:#f0f0f0 !important; font-weight:bold; cursor:not-allowed; }
        .calc-field { background:#e8f4e8 !important; font-weight:bold; color:#155724; cursor:not-allowed; }
        .past-thead th { background:#FF8E00 !important; color:#fff; text-align:center; vertical-align:middle; }
        .past-thead th.sortable { cursor:pointer; user-select:none; white-space:nowrap; }
        .past-thead th.sortable:hover { background:#d97500 !important; }
        .sort-asc::after  { content:' ▲'; font-size:10px; }
        .sort-desc::after { content:' ▼'; font-size:10px; }
        .table td, .table th { vertical-align:middle !important; text-align:center; }
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
                    <div class="text-center mb-3">
                        <h4><u>सहकारी देयो (पैक्स, एलडीबी, विविधीकरण) की बकाया वसूली</u></h4>
                        <small class="text-muted">तीनों मॉड्यूल का संयुक्त योग — पैक्स बकाया + सहकारी ग्राम विकास + विविधीकरण</small>
                    </div>

                    <div class="step">
                        <marquee style="font-size:16px; color:red;">
                            नोट: यह पृष्ठ तीनों मॉड्यूल (पैक्स, सहकारी ग्राम विकास, विविधीकरण) का स्वतः गणना किया गया संयुक्त योग दर्शाता है। यहाँ कोई डेटा संपादन नहीं होता।
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
                                        <small class="text-muted">जनपद बदलें — योग स्वतः अपडेट होगा।</small>
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
                                </div>
                                <div class="col-sm-5 form-group">
                                    <div class="info-box">
                                        यहाँ दिखाई गई प्रत्येक धनराशि तीनों मॉड्यूल का <strong>योग</strong> है।<br>
                                        उदा. कुल बकाया = पैक्स बकाया + सहकारी ग्राम + विविधीकरण
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step">
                        <h4>2. बकाया एवं वसूली का स्थायी विवरण (तीनों मॉड्यूल का योग — आज)</h4>
                        <div class="col-sm-12">

                            <h5>(I) कुल बकाया एवं 95 "क" से आच्छादित बकाया</h5>
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>कुल बकाया (लाख रु.)</label>
                                    <input type="text" class="form-control readonly-field" id="f_total_bakaya"
                                           value="<?php echo f_disp($f['total_bakaya']); ?>" readonly>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>95 "क" से आच्छादित बकाया (लाख रु.)</label>
                                    <input type="text" class="form-control readonly-field" id="f_bakaya_95k"
                                           value="<?php echo f_disp($f['bakaya_95k']); ?>" readonly>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>95 "क" से आच्छादन का प्रतिशत</label>
                                    <input type="text" class="form-control calc-field" id="f_pct_aachhadit"
                                           value="<?php echo $f['pct_aachhadit']; ?> %" readonly>
                                </div>
                            </div>

                            <h5>(II) 95 "क" के आच्छादन से वसूली एवं संग्रह शुल्क</h5>
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>95 "क" के आच्छादन से वसूली (लाख रु.)</label>
                                    <input type="text" class="form-control readonly-field" id="f_recovery_95k"
                                           value="<?php echo f_disp($f['recovery_95k']); ?>" readonly>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>वसूली प्रतिशत</label>
                                    <input type="text" class="form-control calc-field" id="f_pct_vasuli"
                                           value="<?php echo $f['pct_vasuli']; ?> %" readonly>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>प्राप्त संग्रह शुल्क (लाख रु.)</label>
                                    <input type="text" class="form-control readonly-field" id="f_coll_fee"
                                           value="<?php echo f_disp($f['coll_fee']); ?>" readonly>
                                </div>
                            </div>

                            <h5>(III) 95 "क" से आच्छादित एक लाख से बड़े बकायेदार</h5>
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>संख्या</label>
                                    <input type="text" class="form-control readonly-field" id="f_big_count"
                                           value="<?php echo f_disp($f['big_count']); ?>" readonly>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>धनराशि (लाख रु.)</label>
                                    <input type="text" class="form-control readonly-field" id="f_big_amount"
                                           value="<?php echo f_disp($f['big_amount']); ?>" readonly>
                                </div>
                            </div>

                            <h5>(IV) 95 "क" से आच्छादित एक लाख से बड़े बकायेदारों से वसूली</h5>
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>वसूली संख्या</label>
                                    <input type="text" class="form-control readonly-field" id="f_big_rec_count"
                                           value="<?php echo f_disp($f['big_rec_count']); ?>" readonly>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>वसूली धनराशि (लाख रु.)</label>
                                    <input type="text" class="form-control readonly-field" id="f_big_rec_amount"
                                           value="<?php echo f_disp($f['big_rec_amount']); ?>" readonly>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>एक लाख से बड़े बकायेदारों से वसूली प्रतिशत</label>
                                    <input type="text" class="form-control calc-field" id="f_pct_big"
                                           value="<?php echo $f['pct_big']; ?> %" readonly>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="step">
                        <h4>3. दैनिक वसूली एवं संग्रह शुल्क का विवरण (दिनांक: <?php echo date('d-m-Y'); ?>)</h4>
                        <div class="col-sm-12">
                            <h5>(I) 95 "क" से आच्छादन की दैनिक वसूली एवं संग्रह शुल्क</h5>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label>दैनिक वसूली - 95 "क" (लाख रु. में)</label>
                                    <input type="text" class="form-control readonly-field" id="f_daily_rec"
                                           value="<?php echo f_disp($f['daily_rec']); ?>" readonly>
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>प्राप्त दैनिक संग्रह शुल्क (लाख रु. में)</label>
                                    <input type="text" class="form-control readonly-field" id="f_daily_fee"
                                           value="<?php echo f_disp($f['daily_fee']); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step">
                        <h4>(III) दिनांकवार डेटा देखें (<span id="preview_district_name"><?php echo htmlspecialchars($user_district_name, ENT_QUOTES, 'UTF-8'); ?></span>)</h4>
                        <div class="col-sm-12">
                            <div class="row align-items-end mb-2">
                                <div class="col-sm-3 form-group">
                                    <label>दिनांक चुनें</label>
                                    <input type="date" id="past_date" class="form-control"
                                           value="<?php echo date('Y-m-d'); ?>">
                                    <small class="text-muted">आज या कोई भी पूर्व दिनांक।</small>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <button type="button" class="btn btn-warning btn-lg" onclick="loadGridData();">
                                        डेटा देखें
                                    </button>
                                </div>
                            </div>
                            <h6 id="grid_title" style="font-weight:bold; color:#333; margin-bottom:8px;"></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size:12px; text-align:center;">
                                    <thead class="past-thead">
                                    <tr>
                                        <th rowspan="2" class="sortable" data-col="0">जनपद</th>
                                        <th rowspan="2" class="sortable" data-col="1">कुल बकाया (लाख)</th>
                                        <th rowspan="2" class="sortable" data-col="2">95 'क' बकाया (लाख)</th>
                                        <th rowspan="2" class="sortable" data-col="3">आच्छादन %</th>
                                        <th rowspan="2" class="sortable" data-col="4">95 'क' वसूली (लाख)</th>
                                        <th rowspan="2" class="sortable" data-col="5">वसूली %</th>
                                        <th rowspan="2" class="sortable" data-col="6">संग्रह शुल्क (लाख)</th>
                                        <th colspan="2">1 लाख+ बकायेदार</th>
                                        <th colspan="2">1 लाख+ वसूली</th>
                                        <th rowspan="2" class="sortable" data-col="11">वसूली %</th>
                                        <th rowspan="2" class="sortable" data-col="12">दैनिक वसूली (लाख)</th>
                                        <th rowspan="2" class="sortable" data-col="13">दैनिक संग्रह शुल्क (लाख)</th>
                                    </tr>
                                    <tr>
                                        <th class="sortable" data-col="7">संख्या</th>
                                        <th class="sortable" data-col="8">धनराशि</th>
                                        <th class="sortable" data-col="9">संख्या</th>
                                        <th class="sortable" data-col="10">धनराशि</th>
                                    </tr>
                                    </thead>
                                    <tbody id="grid_body"></tbody>
                                </table>
                            </div>
                            <p id="grid_nodata" class="text-danger font-weight-bold" style="display:none;">
                                चुने गए दिनांक के लिए कोई डेटा उपलब्ध नहीं है।
                            </p>
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
    var sortState         = { col: -1, asc: true };

    function fv(v) { return (v !== null && v !== undefined && parseFloat(v) != 0) ? v : '-'; }
    function fmtDate(d) { var p = d.split('-'); return p[2] + '-' + p[1] + '-' + p[0]; }
    function setF(id, val) {
        var el = document.getElementById(id);
        if (!el) return;
        el.value = (val !== '' && val !== null && parseFloat(val) != 0) ? val : '-';
    }

    function sadminDistrictChange(districtId) {
        if (!isSadmin || !districtId) return;
        currentDistrictId = parseInt(districtId);
        var sel = document.getElementById('sadmin_district_select');
        fixedDistrictName = sel.options[sel.selectedIndex].text;
        document.getElementById('preview_district_name').textContent = fixedDistrictName;
        document.getElementById('district_loading').style.display = 'block';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?ajax_district_data=1&selected_district=' + districtId, true);
        xhr.onload = function () {
            document.getElementById('district_loading').style.display = 'none';
            if (xhr.status !== 200) return;
            try {
                var d = JSON.parse(xhr.responseText);
                setF('f_total_bakaya',   d.total_bakaya);
                setF('f_bakaya_95k',     d.bakaya_95k);
                setF('f_recovery_95k',   d.recovery_95k);
                setF('f_coll_fee',       d.coll_fee);
                setF('f_big_count',      d.big_count);
                setF('f_big_amount',     d.big_amount);
                setF('f_big_rec_count',  d.big_rec_count);
                setF('f_big_rec_amount', d.big_rec_amount);
                setF('f_daily_rec',      d.daily_rec);
                setF('f_daily_fee',      d.daily_fee);
                document.getElementById('f_pct_aachhadit').value = (d.pct_aachhadit || 0) + ' %';
                document.getElementById('f_pct_vasuli').value    = (d.pct_vasuli    || 0) + ' %';
                document.getElementById('f_pct_big').value       = (d.pct_big       || 0) + ' %';
                loadGridData();
            } catch(e) { console.error(e); }
        };
        xhr.send();
    }

    function renderGrid(rows) {
        var tbody  = document.getElementById('grid_body');
        var nodata = document.getElementById('grid_nodata');
        tbody.innerHTML = '';
        if (!rows || rows.length === 0) { nodata.style.display = 'block'; return; }
        nodata.style.display = 'none';
        rows.forEach(function(r) {
            tbody.innerHTML +=
                '<tr>' +
                '<td data-val="'+(r.district_name||'') +'">'+(r.district_name||'-')        +'</td>'+
                '<td data-val="'+(r.total_bakaya||0)   +'">'+ fv(r.total_bakaya)            +'</td>'+
                '<td data-val="'+(r.bakaya_95k||0)     +'">'+ fv(r.bakaya_95k)              +'</td>'+
                '<td data-val="'+(r.pct_aachhadit||0)  +'">'+(r.pct_aachhadit||0)+' %'     +'</td>'+
                '<td data-val="'+(r.recovery_95k||0)   +'">'+ fv(r.recovery_95k)            +'</td>'+
                '<td data-val="'+(r.pct_vasuli||0)     +'">'+(r.pct_vasuli||0)+' %'         +'</td>'+
                '<td data-val="'+(r.coll_fee||0)       +'">'+ fv(r.coll_fee)                +'</td>'+
                '<td data-val="'+(r.big_count||0)      +'">'+ fv(r.big_count)               +'</td>'+
                '<td data-val="'+(r.big_amount||0)     +'">'+ fv(r.big_amount)              +'</td>'+
                '<td data-val="'+(r.big_rec_count||0)  +'">'+ fv(r.big_rec_count)           +'</td>'+
                '<td data-val="'+(r.big_rec_amount||0) +'">'+ fv(r.big_rec_amount)          +'</td>'+
                '<td data-val="'+(r.pct_big||0)        +'">'+(r.pct_big||0)+' %'            +'</td>'+
                '<td data-val="'+(r.daily_rec||0)      +'">'+ fv(r.daily_rec)               +'</td>'+
                '<td data-val="'+(r.daily_fee||0)      +'">'+ fv(r.daily_fee)               +'</td>'+
                '</tr>';
        });
    }

    document.addEventListener('click', function(e) {
        var th = e.target.closest('th.sortable');
        if (!th) return;
        var col = parseInt(th.getAttribute('data-col'));
        var asc = (sortState.col === col) ? !sortState.asc : true;
        sortState = { col: col, asc: asc };
        document.querySelectorAll('th.sortable').forEach(function(t) { t.classList.remove('sort-asc','sort-desc'); });
        document.querySelectorAll('th.sortable[data-col="'+col+'"]').forEach(function(t) { t.classList.add(asc ? 'sort-asc' : 'sort-desc'); });
        var tbody = document.getElementById('grid_body');
        var rows  = Array.from(tbody.getElementsByTagName('tr'));
        rows.sort(function(a, b) {
            var aV = a.getElementsByTagName('td')[col];
            var bV = b.getElementsByTagName('td')[col];
            if (!aV || !bV) return 0;
            var aT = aV.getAttribute('data-val') || aV.textContent.trim();
            var bT = bV.getAttribute('data-val') || bV.textContent.trim();
            var aN = parseFloat(aT), bN = parseFloat(bT);
            var cmp = (!isNaN(aN) && !isNaN(bN)) ? (aN - bN) : aT.localeCompare(bT, 'hi');
            return asc ? cmp : -cmp;
        });
        rows.forEach(function(r) { tbody.appendChild(r); });
    });

    function loadGridData() {
        var pdate = document.getElementById('past_date').value;
        if (!pdate) { alert('कृपया दिनांक चुनें।'); return; }
        document.getElementById('grid_title').textContent =
            fixedDistrictName + ' — दिनांक: ' + fmtDate(pdate) + ' (तीनों मॉड्यूल का संयुक्त योग)';
        var url = '?ajax_preview=1&p_date=' + pdate;
        if (isSadmin) url += '&selected_district=' + currentDistrictId;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.onload = function () {
            if (xhr.status !== 200) return;
            try {
                var rows = JSON.parse(xhr.responseText);
                sortState = { col: -1, asc: true };
                document.querySelectorAll('th.sortable').forEach(function(t) { t.classList.remove('sort-asc','sort-desc'); });
                renderGrid(rows);
            } catch(e) { console.error(e); }
        };
        xhr.send();
    }

    document.addEventListener('DOMContentLoaded', function () { loadGridData(); });
</script>

<?php
page_footer_start();
page_footer_end();
?>