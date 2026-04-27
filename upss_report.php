<?php
include("scripts/settings.php");
// print_r($_SESSION);
error_reporting(E_ALL);
page_header_start();
page_header_end();
page_sidebar();

/* Helpers */
function h($s){ return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
function esc($db,$s){ return mysqli_real_escape_string($db, (string)$s); }
function status_code($v){
    $v = trim((string)$v);
    if ($v === '') return '';
    $active_syn = ['सक्रिय', 'सक्रिय ', 'सक्रिया', 'active'];
    $non_syn = ['निष्क्रिय', 'non-active', 'non active', 'non_active'];
    $closed_syn = ['परिसमापनाधीन', 'परिसमापनाधीन ', 'परिसमापन', 'closed'];
    $na_syn = ['स्थापित नही है', 'स्थापित नहीं है', 'स्थापित नही', 'not_applicable'];
    if (in_array($v, $active_syn, true)) return 'active';
    if (in_array($v, $non_syn, true)) return 'non-active';
    if (in_array($v, $closed_syn, true)) return 'closed';
    if (in_array($v, $na_syn, true)) return 'not_applicable';
    $vl = mb_strtolower($v);
    if ($vl === 'active' || $vl === 'सक्रिय') return 'active';
    if ($vl === 'non-active' || $vl === 'non active' || $vl === 'निष्क्रिय') return 'non-active';
    if ($vl === 'closed' || $vl === 'परिसमापनाधीन') return 'closed';
    if ($vl === 'not_applicable' || $vl === 'na' || strpos($vl, 'स्थापित नहीं है') === 0) return 'not_applicable';
    return $v;
}
function status_label($v){
    $code = status_code($v);
    if ($code === 'active') return 'सक्रिय';
    if ($code === 'non-active') return 'निष्क्रिय';
    if ($code === 'closed') return 'परिसमापनाधीन';
    if ($code === 'not_applicable') return 'स्थापित नही है';
    return h($v);
}

/* Filter inputs */
$bhumi_from = isset($_GET['bhumi_area_from']) ? trim($_GET['bhumi_area_from']) : '';
$bhumi_to   = isset($_GET['bhumi_area_to'])   ? trim($_GET['bhumi_area_to'])   : '';
$bhumi_from_val = ($bhumi_from === '') ? null : floatval(str_replace(',', '.', $bhumi_from));
$bhumi_to_val   = ($bhumi_to === '')   ? null : floatval(str_replace(',', '.', $bhumi_to));

$godown_filter   = isset($_GET['godown_suitable']) ? trim($_GET['godown_suitable']) : '';
$status_filter   = isset($_GET['society_status']) ? trim($_GET['society_status']) : '';
$division_filter = isset($_GET['division_id']) ? (int)$_GET['division_id'] : 0;
$district_filter = isset($_GET['district_id']) ? (int)$_GET['district_id'] : 0;
$kabja_filter    = isset($_GET['kabja_vivadit']) ? trim($_GET['kabja_vivadit']) : '';

/* Fetch filter options */
$division_options = [];
$resDiv = mysqli_query($db, "SELECT sno, division_name FROM master_division ORDER BY division_name");
if ($resDiv) {
    while ($row = mysqli_fetch_assoc($resDiv)) {
        $division_options[] = $row;
    }
    mysqli_free_result($resDiv);
}

$district_options = [];
$resDis = mysqli_query($db, "SELECT sno, district_name FROM master_district ORDER BY district_name");
if ($resDis) {
    while ($row = mysqli_fetch_assoc($resDis)) {
        $district_options[] = $row;
    }
    mysqli_free_result($resDis);
}

/* Build query */
$sql = "SELECT u.*, md.district_name, dv.division_name
        FROM upss u
        LEFT JOIN master_district md ON u.janpad_name = md.sno
        LEFT JOIN master_division dv ON u.mandal_name = dv.sno
        WHERE (u.is_deleted IS NULL OR u.is_deleted = 0)";

$user_type = $_SESSION['user_type'] ?? '';

                    if ($user_type === 'ar_dr') {
                        // Fetch division for this DR user from ar_dr table
                        $uid = (int)$_SESSION['user_id'];
                        $dr_res = mysqli_query($db, "SELECT master_division.sno as division_sno 
                                                    FROM ar_dr 
                                                    LEFT JOIN master_division ON ar_dr.division_name = master_division.sno 
                                                    WHERE ar_dr.sno = $uid");
                        $dr_div_ids = [];
                        if ($dr_res) {
                            while ($dr_row = mysqli_fetch_assoc($dr_res)) {
                                if (!empty($dr_row['division_sno'])) {
                                    $dr_div_ids[] = (int)$dr_row['division_sno'];
                                }
                            }
                        }
                        if (!empty($dr_div_ids)) {
                            $sql .= " AND u.mandal_name IN (" . implode(',', $dr_div_ids) . ")";
                        } else {
                            $sql .= " AND 1=0"; // no division found, show nothing
                        }
                    } else {
                        // Admin or other roles — apply session filters if set
                        if (!empty($_SESSION['division_id'])) {
                            $div_ids = array_map('intval', (array) $_SESSION['division_id']);
                            if ($div_ids) $sql .= " AND s.mandal_name IN (" . implode(',', $div_ids) . ")";
                        }
                        if (!empty($_SESSION['district_id'])) {
                            $dis_ids = array_map('intval', (array) $_SESSION['district_id']);
                            if ($dis_ids) $sql .= " AND s.janpad_name IN (" . implode(',', $dis_ids) . ")";
                        }
                    }
if ($division_filter > 0) {
    $sql .= " AND u.mandal_name = " . $division_filter;
}
if ($district_filter > 0) {
    $sql .= " AND u.janpad_name = " . $district_filter;
}
if ($status_filter !== '') {
    $status_code_filter = status_code($status_filter);
    $sql .= " AND u.society_status = '" . esc($db, $status_code_filter) . "'";
}
if ($godown_filter !== '') {
    $sql .= " AND u.godown_suitable = '" . esc($db, $godown_filter) . "'";
}
if ($kabja_filter !== '') {
    $sql .= " AND u.is_kabja_vivadit = '" . esc($db, $kabja_filter) . "'";
}
if ($bhumi_from_val !== null && $bhumi_to_val !== null) {
    $sql .= " AND (u.bhumi_area+0) BETWEEN " . (float)$bhumi_from_val . " AND " . (float)$bhumi_to_val;
} elseif ($bhumi_from_val !== null) {
    $sql .= " AND (u.bhumi_area+0) >= " . (float)$bhumi_from_val;
} elseif ($bhumi_to_val !== null) {
    $sql .= " AND (u.bhumi_area+0) <= " . (float)$bhumi_to_val;
}

$sql .= " ORDER BY dv.division_name, md.district_name, u.society_name";
$result = mysqli_query($db, $sql);
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<style>
    .card {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08)
    }
    .section-heading {
        background: #2b6fb3;
        color: #fff;
        padding: 18px 20px;
        border-radius: 6px;
        margin-bottom: 18px;
        font-weight: 800;
        font-size: 2em;
        line-height: 1.05;
        text-transform: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
    }
    .filters {
        margin: 12px 0 18px 0;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .filters label {
        font-weight: 700;
    }
    .filters .form-control {
        width: 140px;
    }
    .filters .wide {
        min-width: 200px;
    }
    .btn-primary {
        background: #4a90e2;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-primary:hover { background:#357ab8; }
    .table-container {
        background: linear-gradient(180deg, #ffffff, #fbfdff);
        border: 1px solid #e6eefc;
        padding: 14px;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(10, 45, 85, 0.03);
    }
    .table-wrap { overflow:auto; margin-top:12px; }
    .report-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px;
    }
    .report-table th, .report-table td {
        border: 1px solid #e6edf7;
        padding: 10px 12px;
        font-size: 13px;
        white-space: nowrap;
        vertical-align: middle;
    }
    .report-table thead th {
        background: #e8f5ff;
        text-align: left;
        font-size: 1.05em;
        font-weight: 800;
        padding: 14px 12px;
        color: #08386b;
        letter-spacing: 0.15px;
    }
    @media (max-width: 900px) {
        .section-heading { font-size: 1.6em; padding: 14px 16px; }
        .report-table thead th { font-size: 1em; padding: 12px 8px; }
        .report-table { min-width: 900px; }
    }
</style>
<style>
.blink-red{
    animation: blinkAnim 3s infinite;
    color:#c62828;
    font-weight:700;
}
@keyframes blinkAnim{
    0%{opacity:1;}
    50%{opacity:0;}
    100%{opacity:1;}
}
</style>
<div class="card" style="margin-top:40px;">
    <h3 class="section-heading" style="text-align:center;">उपभोक्ता संघ रिपोर्ट</h3>
    <form method="get" class="filters">
        <label>भूमि क्षेत्र (From)</label>
        <input name="bhumi_area_from" class="form-control" value="<?php echo h($bhumi_from); ?>">
        <label>(To)</label>
        <input name="bhumi_area_to" class="form-control" value="<?php echo h($bhumi_to); ?>">

        <label>गोदाम उपयुक्त</label>
        <select name="godown_suitable" class="form-control">
            <option value="">Select</option>
            <option value="हाँ" <?php if($godown_filter==='हाँ') echo 'selected'; ?>>हाँ</option>
            <option value="नहीं" <?php if($godown_filter==='नहीं') echo 'selected'; ?>>नहीं</option>
        </select>

        <label>समिति स्थिति</label>
        <select name="society_status" class="form-control">
            <?php
            $status_options = [
                '' => 'Select',
                'active' => 'सक्रिय',
                'non-active' => 'निष्क्रिय',
                'closed' => 'परिसमापनाधीन',
                'not_applicable' => 'स्थापित नही है'
            ];
            foreach ($status_options as $val => $label) {
                $sel = ($status_filter !== '' && status_code($status_filter) === $val) ? 'selected' : '';
                if ($val === '' && $status_filter === '') $sel = 'selected';
                echo "<option value=\"".h($val)."\" {$sel}>".h($label)."</option>";
            }
            ?>
        </select>

        <label>मण्डल</label>
        <select name="division_id" class="form-control wide">
            <option value="0">Select</option>
            <?php foreach ($division_options as $div): ?>
                <option value="<?php echo (int)$div['sno']; ?>" <?php if($division_filter== (int)$div['sno']) echo 'selected'; ?>>
                    <?php echo h($div['division_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>जनपद</label>
        <select name="district_id" class="form-control wide">
            <option value="0">Select</option>
            <?php foreach ($district_options as $dist): ?>
                <option value="<?php echo (int)$dist['sno']; ?>" <?php if($district_filter== (int)$dist['sno']) echo 'selected'; ?>>
                    <?php echo h($dist['district_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>कब्जा विवादित?</label>
        <select name="kabja_vivadit" class="form-control">
            <option value="">Select</option>
            <option value="हाँ" <?php if($kabja_filter==='हाँ') echo 'selected'; ?>>हाँ</option>
            <option value="नहीं" <?php if($kabja_filter==='नहीं') echo 'selected'; ?>>नहीं</option>
        </select>

        <button class="btn-primary" type="submit">Filter</button>
        <a href="upss_report.php" style="margin-left:8px; padding:8px 10px; display:inline-block; background:#eee; border-radius:5px; text-decoration:none;">Reset</a>
    </form>

    <div class="table-container">
        <div class="table-wrap">
            <table id="general_stat_table" class="report-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>मण्डल</th>
                        <th>जनपद</th>
                        <th>समिति का नाम</th>
                        <th>NCD ID</th>
                        <th>समिति स्थिति</th>
                        <th>सचिव प्रकार</th>
                        <th>सचिव नाम</th>
                        <th>सचिव मोबाइल</th>
                        <th>सचिव ईमेल</th>
                        <th>समिति अध्यक्ष</th>
                        <th>अध्यक्ष मोबाइल</th>
                        <th>भूमि क्षेत्रफल</th>
                        <th>भूमि स्थिति</th>
                        <th>भूमि प्रकार</th>
                        <th>स्थान / पहुँच मार्ग</th>
                        <th>गोदाम उपयुक्त</th>
                        <th>रैक दूरी (किमी)</th>
                        <th>कब्जा विवरण</th>
                        <th>क्या कब्जा विवादित?</th>
                        <th>राजस्व अभिलेख</th>
                        <th>परिसमापक का नाम</th>
                        <th>परिसमापक का मोबाइल</th>
                        <th>कब से परिसमापक</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $bhumi = isset($row['bhumi_area']) ? floatval($row['bhumi_area']) : 0;
                            $is_confirmed = (int) ($row['land_conf'] ?? 0);
                            $should_blink = ($bhumi > 0.5 && $is_confirmed == 0);
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td>" . h($row['division_name'] ?? '') . "</td>";
                            echo "<td>" . h($row['district_name'] ?? '') . "</td>";
                            echo "<td>" . h($row['society_name'] ?? '') . "</td>";
                            echo "<td>" . h($row['ncd_id'] ?? '') . "</td>";
                            echo "<td>" . h(status_label($row['society_status'] ?? '')) . "</td>";
                            echo "<td>" . h($row['sachiv_type'] ?? '') . "</td>";
                            echo "<td>" . h($row['sachiv_name'] ?? '') . "</td>";
                            echo "<td>" . h($row['sachiv_no'] ?? '') . "</td>";
                            echo "<td>" . h($row['sachiv_mail'] ?? '') . "</td>";
                            echo "<td>" . h($row['society_chairamin_name'] ?? '') . "</td>";
                            echo "<td>" . h($row['society_chairamin_no'] ?? '') . "</td>";
                            echo "<td id='land_cell_{$row['sno']}' style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>";

                            if ($should_blink) {

                                echo "<span class='blink-red'>" . h($row['bhumi_area']) . "</span>";
                                echo "<br>";
                                echo "<button onclick='confirmLand({$row['sno']},1)' 
                                        style='margin-top:4px;padding:3px 6px;font-size:11px;background:#e6208d;color:#fff;border:none;border-radius:4px;'>
                                        Correct Land Area
                                    </button>";

                            } else {

                                echo h($row['bhumi_area']);

                                if ($is_confirmed == 1) {
                                    echo "<br>
                                        <span style='background:#c8e6c9;color:#1b5e20;padding:2px 6px;border-radius:4px;font-size:11px;font-weight:600;'>
                                            ✔
                                        </span>";
                                }
                            }

                            echo "</td>";
                            echo "<td>" . h($row['land_status'] ?? '') . "</td>";
                            echo "<td>" . h($row['land_type'] ?? '') . "</td>";
                            echo "<td>" . h($row['arrived_road'] ?? '') . "</td>";
                            echo "<td>" . h($row['godown_suitable'] ?? '') . "</td>";
                            echo "<td>" . h($row['raik_distance_km'] ?? '') . "</td>";
                            echo "<td>" . h($row['kabja_vivadit'] ?? '') . "</td>";
                            echo "<td>" . h($row['is_kabja_vivadit'] ?? '') . "</td>";
                            echo "<td>" . h($row['rajswa_abhilekh'] ?? '') . "</td>";
                            echo "<td>" . h($row['liquidator_name'] ?? '') . "</td>";
                            echo "<td>" . h($row['liquidator_no'] ?? '') . "</td>";
                            echo "<td>" . h($row['liquidation_from_date'] ?? '') . "</td>";
                            echo "<td>" . h($row['latitude'] ?? '') . "</td>";
                            echo "<td>" . h($row['longitude'] ?? '') . "</td>";
                            echo "</tr>";
                        }
                        mysqli_free_result($result);
                    } else {
                        echo "<tr><td colspan='26' style='text-align:center;'>कोई रिकॉर्ड नहीं मिला</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    var t = $('#general_stat_table').DataTable({
        paging: true,
        pageLength: 25,
        lengthMenu: [25, 50, 100, 200],
        searching: true,
        info: true,
        processing: true,
        responsive: false,
        scrollX: true,
        deferRender: false,
        order: [],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            }
        ]
    });
});
</script>
<script>
  function confirmLand(id, value){

    if(!confirm("Confirm land data?")) return;

    var xhr = new XMLHttpRequest();
    xhr.open("POST","scripts/update_land_status.php",true);
    xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");

    xhr.onload = function(){
        if(this.status == 200 && this.responseText.trim() == "success"){

            var cell = document.getElementById("land_cell_"+id);

            var span = cell.querySelector(".blink-red");
            if(span){ span.classList.remove("blink-red"); }

            var btn = cell.querySelector("button");
            if(btn){ btn.remove(); }

            cell.innerHTML += 
                "<br><span style='background:#c8e6c9;color:#1b5e20;padding:2px 6px;border-radius:4px;font-size:11px;font-weight:600;'>✔</span>";
        }
    };

    xhr.send("id="+id+"&value="+value+"&table=upss");
}
</script>
<?php
page_footer_start();
page_footer_end();
?>