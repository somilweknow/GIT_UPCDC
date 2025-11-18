<?php
include("scripts/settings.php");
error_reporting(E_ALL);
page_header_start();
page_header_end();

page_sidebar();

// Small helpers
function h($s)
{
    return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}
function esc($db, $s)
{
    return mysqli_real_escape_string($db, (string) $s);
}
$edit_id = 0;
if (!empty($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
} elseif (!empty($_POST['edit_id'])) {
    $edit_id = intval($_POST['edit_id']);
}


if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    $cur_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';

    $del_sql = "UPDATE block_union SET is_deleted = 1, deleted_at = NOW(), deleted_by = " . $cur_user_id . " WHERE sno = " . $del_id . " LIMIT 1";
    if (mysqli_query($db, $del_sql)) {
    } else {
        echo "<div style='padding:12px;color:#b91c1c;'>Delete failed: " . h(mysqli_error($db)) . "</div>";
    }
}

$edit_prefill = [];
if ($edit_id > 0 && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $q = "SELECT * FROM block_union WHERE sno = " . $edit_id . " AND (is_deleted IS NULL OR is_deleted = 0) LIMIT 1";
    $r = mysqli_query($db, $q);
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $edit_prefill['mandal_name'] = $row['mandal_name'] ?? '';
        $edit_prefill['janpad_name'] = $row['janpad_name'] ?? '';
        $edit_prefill['total_union'] = $row['total_union'] ?? '';
        $edit_prefill['active_union'] = $row['active_union'] ?? '';
        $edit_prefill['inactive_union'] = $row['inactive_union'] ?? '';
        $edit_prefill['liquidation_union'] = $row['liquidation_union'] ?? '';
        $edit_prefill['latitude'] = $row['latitude'] ?? '';
        $edit_prefill['longitude'] = $row['longitude'] ?? '';

        $edit_prefill['active'] = [
            'active_status' => $row['row_status'] ?? '',
            'ncd_id' => $row['ncd_id'] ?? '',
            'samiti_naam' => $row['samiti_naam'] ?? '',
            'land_area' => $row['land_area'] ?? '',
            'land_sthiti' => $row['land_sthiti'] ?? '',
            'society_land' => $row['society_land'] ?? '',
            'godown_suitable' => $row['godown_suitable'] ?? '',
            'rack_distance' => $row['rack_distance'] ?? '',
            'arrived_land_type' => $row['arrived_land_type'] ?? '',
            'liquidator_name' => $row['liquidator_name'] ?? '',
            'liquidator_from_date' => $row['liquidator_from_date'] ?? ''
        ];
    }
    if ($r)
        mysqli_free_result($r);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mandal_name = $_POST['mandal_name'] ?? '';
    $janpad_name = $_POST['janpad_name'] ?? '';
    $total_union = $_POST['total_union'] ?? '';
    $active_union = $_POST['active_union'] ?? '';
    $inactive_union = $_POST['inactive_union'] ?? '';
    $liquidation_union = $_POST['liquidation_union'] ?? '';

    $latitude = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';

    $status_mode = $_POST['status_mode'] ?? '';

    $posted_edit_id = intval($_POST['edit_id'] ?? 0);

    if ($mandal_name === '' || $janpad_name === '') {
        echo "<script>alert('कृपया मण्डल और जनपद चयन करें।');</script>";
    } else {
        $has = [
            'liquidator_name' => false,
            'liquidator_from_date' => false,
            'samiti_naam' => false,
            'ncd_id' => false,
            'row_status' => false,
            'latitude' => false,
            'longitude' => false,
        ];

        if ($posted_edit_id > 0) {
            $sets = [];
            $sets[] = "mandal_name = '" . esc($db, $mandal_name) . "'";
            $sets[] = "janpad_name = '" . esc($db, $janpad_name) . "'";
            $sets[] = "total_union = '" . esc($db, $total_union) . "'";
            $sets[] = "active_union = '" . esc($db, $active_union) . "'";
            $sets[] = "inactive_union = '" . esc($db, $inactive_union) . "'";
            $sets[] = "liquidation_union = '" . esc($db, $liquidation_union) . "'";

            $a = $_POST['active'] ?? [];
            $samiti_naam_i = trim(($a['samiti_naam'][0] ?? ''));
            if ($samiti_naam_i === '') {
                echo "<script>alert('कृपया समिति का नाम (समिति_नाम) भरें। अपडेट नहीं किया जा सकता।');
                location.href=window.location.pathname;</script>";
                exit;
            }
            $ncd_id_i = trim(($a['ncd_id'][0] ?? ''));
            $active_status_i = trim(($a['active_status'][0] ?? ''));
            $land_area_i = trim(($a['land_area'][0] ?? ''));
            $land_sthiti_i = trim(($a['land_sthiti'][0] ?? ''));
            $society_land_i = trim(($a['society_land'][0] ?? ''));
            $godown_suitable_i = trim(($a['godown_suitable'][0] ?? ''));
            $rack_distance_i = trim(($a['rack_distance'][0] ?? ''));
            $arrived_land_type_i = trim(($a['arrived_land_type'][0] ?? ''));
            $liquidator_name_i = trim(($a['liquidator_name'][0] ?? ''));
            $liquidator_date_i = trim(($a['liquidator_from_date'][0] ?? ''));

            if ($has['row_status']) {
                $sets[] = "row_status = '" . esc($db, ($active_status_i !== '' ? $active_status_i : 'सक्रिय')) . "'";
            }
            if ($has['samiti_naam']) {
                $sets[] = "samiti_naam = '" . esc($db, $samiti_naam_i) . "'";
            }
            if ($has['ncd_id']) {
                $sets[] = "ncd_id = '" . esc($db, $ncd_id_i) . "'";
            }
            if ($has['liquidator_name']) {
                $val = ($active_status_i === 'परिसमापनाधीन' && $liquidator_name_i !== '') ? $liquidator_name_i : '';
                $sets[] = "liquidator_name = '" . esc($db, $val) . "'";
            }
            if ($has['liquidator_from_date']) {
                $val = ($active_status_i === 'परिसमापनाधीन' && $liquidator_date_i !== '') ? ("'" . esc($db, $liquidator_date_i) . "'") : "NULL";
                $sets[] = "liquidator_from_date = " . ($val === "NULL" ? "NULL" : ("'" . esc($db, $liquidator_date_i) . "'"));
            }
            $sets[] = "land_area = '" . esc($db, $land_area_i) . "'";
            $sets[] = "land_sthiti = '" . esc($db, $land_sthiti_i) . "'";
            $sets[] = "society_land = '" . esc($db, $society_land_i) . "'";
            $sets[] = "godown_suitable = '" . esc($db, $godown_suitable_i) . "'";
            $sets[] = "rack_distance = '" . esc($db, $rack_distance_i) . "'";
            $sets[] = "arrived_land_type = '" . esc($db, $arrived_land_type_i) . "'";

            if ($has['latitude']) {
                $sets[] = "latitude = " . ($latitude !== '' ? ("'" . esc($db, $latitude) . "'") : "NULL");
            }
            if ($has['longitude']) {
                $sets[] = "longitude = " . ($longitude !== '' ? ("'" . esc($db, $longitude) . "'") : "NULL");
            }

            $sql = "UPDATE block_union SET " . implode(",", $sets) . " WHERE sno = " . $posted_edit_id . " LIMIT 1";
            if (!mysqli_query($db, $sql)) {
                echo "<div style='padding:12px;color:#b91c1c;'>Update failed: " . h(mysqli_error($db)) . "</div>";
            } else {
                echo "<script>alert('Record updated successfully');location.href = window.location.pathname;</script>";
                exit;
            }

        } else {
            $fixedCols = ["mandal_name", "janpad_name", "total_union", "active_union", "inactive_union", "liquidation_union", "sachiv_name", "land_area", "land_sthiti", "society_land", "godown_suitable", "rack_distance", "arrived_land_type"];

            $rowsInserted = 0;

            $a_samiti = $_POST['active']['samiti_naam'] ?? [];
            $a_ncd = $_POST['active']['ncd_id'] ?? [];
            $a_status = $_POST['active']['active_status'] ?? [];
            $a_area = $_POST['active']['land_area'] ?? [];
            $a_sthiti = $_POST['active']['land_sthiti'] ?? [];
            $a_place = $_POST['active']['society_land'] ?? [];
            $a_godam = $_POST['active']['godown_suitable'] ?? [];
            $a_rack = $_POST['active']['rack_distance'] ?? [];
            $a_road = $_POST['active']['arrived_land_type'] ?? [];
            $a_liq_name = $_POST['active']['liquidator_name'] ?? [];
            $a_liq_date = $_POST['active']['liquidator_from_date'] ?? [];

            $n = max(
                count($a_samiti),
                count($a_ncd),
                count($a_status),
                count($a_area),
                count($a_sthiti),
                count($a_place),
                count($a_godam),
                count($a_rack),
                count($a_road),
                count($a_liq_name),
                count($a_liq_date)
            );
            for ($i = 0; $i < $n; $i++) {
                $samiti_naam_i = trim($a_samiti[$i] ?? '');
                $ncd_id_i = trim($a_ncd[$i] ?? '');
                $active_status_i = trim($a_status[$i] ?? '');
                $land_area_i = trim($a_area[$i] ?? '');
                $land_sthiti_i = trim($a_sthiti[$i] ?? '');
                $society_land_i = trim($a_place[$i] ?? '');
                $godown_suitable_i = trim($a_godam[$i] ?? '');
                $rack_distance_i = trim($a_rack[$i] ?? '');
                $arrived_land_type_i = trim($a_road[$i] ?? '');
                $liquidator_name_i = trim($a_liq_name[$i] ?? '');
                $liquidator_date_i = trim($a_liq_date[$i] ?? '');

                if ($samiti_naam_i === '') {
                    echo "<div style='padding:12px;color:#b91c1c;'>पंक्ति " . ($i+1) . " के लिए समिति का नाम आवश्यक है।</div>";
                    continue;
                }

                $cols = $fixedCols;
                $vals = [
                    "'" . esc($db, $mandal_name) . "'",
                    "'" . esc($db, $janpad_name) . "'",
                    "'" . esc($db, $total_union) . "'",
                    "'" . esc($db, $active_union) . "'",
                    "'" . esc($db, $inactive_union) . "'",
                    "'" . esc($db, $liquidation_union) . "'",
                    "''",
                    "'" . esc($db, $land_area_i) . "'",
                    "'" . esc($db, $land_sthiti_i) . "'",
                    "'" . esc($db, $society_land_i) . "'",
                    "'" . esc($db, $godown_suitable_i) . "'",
                    "'" . esc($db, $rack_distance_i) . "'",
                    "'" . esc($db, $arrived_land_type_i) . "'"
                ];

                if ($has['row_status']) {
                    $cols[] = "row_status";
                    $finalStatus = ($active_status_i !== '') ? $active_status_i : 'सक्रिय';
                    $vals[] = "'" . esc($db, $finalStatus) . "'";
                }
                if ($has['samiti_naam']) {
                    $cols[] = "samiti_naam";
                    $vals[] = "'" . esc($db, $samiti_naam_i) . "'";
                }
                if ($has['ncd_id']) {
                    $cols[] = "ncd_id";
                    $vals[] = ($ncd_id_i !== '') ? "'" . esc($db, $ncd_id_i) . "'" : "''";
                }
                if ($has['liquidator_name']) {
                    $cols[] = "liquidator_name";
                    $vals[] = ($active_status_i === 'परिसमापनाधीन' && $liquidator_name_i !== '')
                        ? "'" . esc($db, $liquidator_name_i) . "'"
                        : "''";
                }
                if ($has['liquidator_from_date']) {
                    $cols[] = "liquidator_from_date";
                    $vals[] = ($active_status_i === 'परिसमापनाधीन' && $liquidator_date_i !== '')
                        ? "'" . esc($db, $liquidator_date_i) . "'"
                        : "NULL";
                }
                if ($has['latitude']) {
                    $cols[] = "latitude";
                    $vals[] = ($latitude !== '') ? "'" . esc($db, $latitude) . "'" : "NULL";
                }
                if ($has['longitude']) {
                    $cols[] = "longitude";
                    $vals[] = ($longitude !== '') ? "'" . esc($db, $longitude) . "'" : "NULL";
                }

                $sql = "INSERT INTO block_union (" . implode(",", $cols) . ") VALUES (" . implode(",", $vals) . ")";
                if (!mysqli_query($db, $sql)) {
                    echo "<div style='padding:12px;color:#b91c1c;'>Insert failed (row " . ($i + 1) . "): " . h(mysqli_error($db)) . "</div>";
                } else {
                    $rowsInserted++;
                }
            }

            if ($rowsInserted > 0) {
                echo "<script>alert('Inserted successfully (" . $rowsInserted . " rows)');</script>";
                $_POST = [];
            } else {
                echo "<script>alert('कोई वैध पंक्ति नहीं मिली।');</script>";
            }
        }
    }
}

$lat_pref = $_POST['latitude'] ?? '';
$long_pref = $_POST['longitude'] ?? '';
?>
<style>
    .card {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .section-heading {
        background: #4a90e2;
        color: #fff;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        font-weight: bold;
        font-size: 1.1em;
    }

    .form-label {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .form-control,
    .form-select,
    textarea {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: 0.95em;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px 20px;
    }

    .btn-primary {
        background-color: #4a90e2;
        color: #fff;
        border: none;
        padding: 10px 20px;
        font-size: 1em;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #357ab8;
    }

    .btn-info {
        background: #0ea5e9;
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-info:hover {
        background: #0284c7;
    }

    .btn-inf-add-row {
        background: #10b981;
        color: #fff;
        border: none;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: bold;
    }

    .btn-inf-add-row:hover {
        background: #059669;
    }

    .common-column {
        display: table-cell !important;
    }

    .land-column {
        display: table-cell;
    }

    .liq-column {
        display: table-cell;
    }

    .cell-hidden>* {
        display: none !important;
    }

    @media (max-width:768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .table-wrap {
        overflow: auto;
        margin-top: 18px;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #e1e5ee;
        padding: 8px 10px;
        font-size: 13px;
        white-space: nowrap;
    }

    .report-table th {
        background: #f1f5ff;
        text-align: left;
    }

    .row-flex {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 10px;
    }

    .col {
        flex: 1 1 220px;
        min-width: 220px;
    }

    .blinking-text {
        color: #2563eb;
        font-weight: 700;
        animation: blink 1.2s linear infinite;
        margin-top: 6px
    }

    @keyframes blink {
        0% {
            opacity: 1
        }

        50% {
            opacity: .2
        }

        100% {
            opacity: 1
        }
    }

    table.mode-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    table.mode-table thead th {
        background: #f1f5ff;
        border: 1px solid #e1e5ee;
        padding: 10px;
        text-align: left;
        font-size: 13px;
    }

    table.mode-table tbody td {
        border: 1px solid #e1e5ee;
        padding: 8px;
    }

    .hidden {
        display: none;
    }
</style>
<div class="card" style="margin-top: 30px;">
    <h3 class="section-heading">📋 रिपोर्ट</h3>

    <div class="table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>S.No.</th>
                    <th>NCD ID</th>
                    <th>समिति सक्रिय?</th>
                    <th>समिति का नाम</th>
                    <th>भूमि क्षेत्रफल</th>
                    <th>भूमि स्थिति</th>
                    <th>स्थान</th>
                    <th>गोदाम उपयुक्त</th>
                    <th>रैक दूरी (किमी)</th>
                    <th>पहुंच मार्ग</th>
                    <th>परिसमापक का नाम</th>
                    <th>कब से परिसमापक</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT s.*, dt.district_name 
                        FROM block_union s 
                        LEFT JOIN master_district dt ON s.janpad_name = dt.sno 
                        WHERE (s.is_deleted IS NULL OR s.is_deleted = 0)";

                if (!empty($_SESSION['district_id'])) {
                    $dis_ids = array_map('intval', (array) $_SESSION['district_id']);
                    if (!empty($dis_ids)) {
                        $sql .= " AND s.janpad_name IN (" . implode(',', $dis_ids) . ")";
                    }
                }

                $sql .= " ORDER BY dt.district_name, s.sno DESC";
                $result = mysqli_query($db, $sql);

                $i = 1;
                $last = null;

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $dist = $row['district_name'] ?? 'N/A';

                        if ($dist !== $last) {
                            echo '<tr><th colspan="22" style="background:#e9f3ff;color:#0a3d8f;font-weight:700;">जनपद: ' . h($dist) . '</th></tr>';
                            $last = $dist;
                        }

                        echo "<tr>";
                        // Action buttons
                        echo "<td style='display:flex;gap:6px;align-items:center;'>";
                        echo '<a href="?edit=' . (int)$row['sno'] . '" style="background:#007bff;color:#fff;padding:6px 8px;border-radius:4px;text-decoration:none;">✏️ Edit</a>';
                        echo '<a href="?delete=' . (int)$row['sno'] . '" onclick="return confirm(\'Are You Sure\');" style="background:#b00020;color:#fff;padding:6px 8px;border-radius:4px;text-decoration:none;">🗑️ Delete</a>';
                        echo "</td>";

                        echo "<td>" . $i++ . "</td>";
                        echo "<td>" . h($row['ncd_id'] ?? '') . "</td>";
                        echo "<td>" . h($row['row_status'] ?? '') . "</td>";
                        echo "<td>" . h($row['samiti_naam'] ?? '') . "</td>";
                        echo "<td>" . h($row['land_area'] ?? '') . "</td>";
                        echo "<td>" . h($row['land_sthiti'] ?? '') . "</td>";
                        echo "<td>" . h($row['society_land'] ?? '') . "</td>";
                        echo "<td>" . h($row['godown_suitable'] ?? '') . "</td>";
                        echo "<td>" . h($row['rack_distance'] ?? '') . "</td>";
                        echo "<td>" . h($row['arrived_land_type'] ?? '') . "</td>";
                        echo "<td>" . h($row['liquidator_name'] ?? '') . "</td>";
                        echo "<td>" . h($row['liquidator_from_date'] ?? '') . "</td>";
                        echo "</tr>";
                    }
                    mysqli_free_result($result);
                } else {
                    echo "<tr><td colspan='21'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<form method="post" action="">
    <h2
        style="text-align:center;font-size:28px;color:#357ab8;font-weight:600;padding:10px;border-radius:5px;margin-bottom:20px;">
        सहकारी संघों (ब्लॉक यूनियन) का जनपदवार विवरण
    </h2>

    <div class="card">
        <div class="form-grid">
            <div>
                <label class="form-label" style="font-size:15px;">मण्डल</label>
                <select name="mandal_name" id="mandal_name" class="form-control" onchange="fill_district(this.value);"
                    style="height:35px;">
                    <?php
                    if ($_SESSION['user_type'] == 'ar' && !empty($_SESSION['division_id'])) {
                        $ids = array_map('intval', (array) $_SESSION['division_id']);
                        $sql = 'SELECT * FROM master_division WHERE sno IN (' . implode(',', $ids) . ') ORDER BY division_name';
                    } else {
                        $sql = 'SELECT * FROM master_division ORDER BY division_name';
                    }
                    $result_division = mysqli_query($db, $sql);
                    while ($row_division = mysqli_fetch_assoc($result_division)) {
                        $selected = (isset($_POST['mandal_name']) && $_POST['mandal_name'] == $row_division['sno']) ? 'selected' : '';
                        echo '<option value="' . (int) $row_division['sno'] . '" ' . $selected . '>' . h($row_division['division_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size:15px;">जनपद</label>
                <select name="janpad_name" id="janpad_name" class="form-control" style="height:35px;">
                    <?php
                    if ($_SESSION['user_type'] == 'ar' && !empty($_SESSION['district_id'])) {
                        $ids = array_map('intval', (array) $_SESSION['district_id']);
                        $sql = 'SELECT * FROM master_district WHERE sno IN (' . implode(',', $ids) . ') ORDER BY district_name';
                    } else {
                        $sql = 'SELECT * FROM master_district ORDER BY district_name';
                    }
                    $result_district = mysqli_query($db, $sql);
                    while ($row_district = mysqli_fetch_assoc($result_district)) {
                        $selected = (isset($_POST['janpad_name']) && $_POST['janpad_name'] == $row_district['sno']) ? 'selected' : '';
                        echo '<option value="' . (int) $row_district['sno'] . '" ' . $selected . '>' . h($row_district['district_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="card" id="bhumi-details">
        <h3 class="section-heading">🏡 खाली भूमि का विवरण</h3>
        <div id="tableActiveWrap">
            <div class="table-wrap">
                <table class="mode-table" id="tableActive">
                    <thead>
                        <tr>
                            <th>क्या समिति सक्रिय है?</th>
                            <th class="common-column">NCD ID</th>
                            <th class="common-column">समिति का नाम</th>
                            <th class="land-column">भूमि का क्षेत्रफल (हे.)</th>
                            <th class="land-column">भूमि की स्थिति</th>
                            <th class="land-column">स्थान</th>
                            <th class="land-column">गोदाम उपयुक्त?</th>
                            <th class="land-column">रैक पॉइंट दूरी (किमी.)</th>
                            <th class="land-column">पहुंच मार्ग प्रकार</th>
                            <th class="liq-column">परिसमापक का नाम</th>
                            <th class="liq-column">कब से परिसमापक</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div style="text-align:center;margin-top:12px;">
        <input type="hidden" name="edit_id" id="edit_id" value="<?php echo (int) $edit_id; ?>">
        <button type="submit" class="btn-primary"><?php echo ($edit_id ? 'Update' : 'Submit'); ?></button>
        <?php if ($edit_id): ?>
            <a href="<?php echo h($_SERVER['PHP_SELF']); ?>" style="margin-left:10px;" class="btn-info">Cancel Edit</a>
        <?php endif; ?>
    </div>
</form>

<script>
    function getLocation() {
        if (!navigator.geolocation) { alert('Geolocation unsupported'); return; }
        navigator.geolocation.getCurrentPosition(function (pos) {
            var lat = pos.coords.latitude.toFixed(6);
            var lng = pos.coords.longitude.toFixed(6);
            var latShow = document.getElementById('lat_show');
            var lngShow = document.getElementById('long_show');
            if (latShow) latShow.value = lat;
            if (lngShow) lngShow.value = lng;
            var latH = document.getElementById('lat');
            var lngH = document.getElementById('long');
            if (latH) latH.value = lat;
            if (lngH) lngH.value = lng;
            var iframe = document.getElementById('googlemap');
            if (iframe) iframe.src = 'https://maps.google.com/maps?q=' + lat + ',' + lng + '&hl=hi&z=13&output=embed';
        }, function (err) {
            alert('लोकेशन नहीं मिल सकी: ' + (err.message || err.code));
        }, { enableHighAccuracy: true, timeout: 10000 });
    }

    (function () {
        const statusSelect = document.getElementById('status_mode');
        const tableActive = document.getElementById('tableActive');
        const tableActiveWrap = document.getElementById('tableActiveWrap');
        const tBodyActive = tableActive.querySelector('tbody');
        const addRowActive = document.getElementById('addRowActive');
        window.newActiveRow = function () {
            const tr = document.createElement('tr');
            tr.innerHTML = `
      <td>
        <select name="active[active_status][]" class="form-select" onchange="toggleColumns(this)">
            <option value="">--चुनें--</option>
            <option value="सक्रिय">सक्रिय</option>
            <option value="निष्क्रिय">निष्क्रिय</option>
            <option value="परिसमापनाधीन">परिसमापनाधीन</option>
        </select>
      </td>
      <td class="common-column"><input type="text"   name="active[ncd_id][]"             class="form-control" placeholder="NCD ID"></td>
      <td class="common-column"><input type="text"   name="active[samiti_naam][]"        class="form-control" placeholder="समिति का नाम"></td>
      <td class="land-column"><input type="number" name="active[land_area][]"           class="form-control" step="0.0001" min="0" placeholder="0.0000"></td>
      <td class="land-column">
        <select name="active[land_sthiti][]" class="form-select">
        <option value="">--चुनें--</option>
        <option value="उपजाऊ">उपजाऊ</option>
        <option value="बंजर">बंजर</option>
        <option value="rent">भूमि किराये पर है।</option>
        <option value="भूमि उपलब्ध नही है">भूमि उपलब्ध नही है।</option>
      </select>
      </td>
      <td class="land-column">
        <select name="active[society_land][]" class="form-select">
        <option value="">--चुनें--</option>
        <option value="समिति प्रांगण">समिति प्रांगण</option>
        <option value="अन्य स्थान">अन्य स्थान</option>
        <option value="भूमि नही है">भूमि नही है।</option>
      </select>
      </td>
      <td class="land-column">
        <select name="active[godown_suitable][]" class="form-select">
        <option value="">--चुनें--</option>
        <option value="हाँ">हाँ</option>
        <option value="नहीं">नहीं</option>
        <option value="गोदाम उपलब्ध नही है।">गोदाम उपलब्ध नही है।</option>
      </select>
      </td>
      <td class="land-column"><input type="number" name="active[rack_distance][]"   class="form-control" step="0.01" min="0" placeholder="0.00"></td>
      <td class="land-column">
        <select name="active[arrived_land_type][]" class="form-select">
        <option value="">--चुनें--</option>
        <option value="कच्ची सड़क">कच्ची सड़क</option>
        <option value="नेशनल हाईवे">नेशनल हाईवे</option>
        <option value="स्टेट हाईवे">स्टेट हाईवे</option>
        <option value="एम.डी.आर.">एम.डी.आर.</option>
        <option value="ओ.डी.आर.">ओ.डी.आर.</option>
        <option value="ग्रामीण सड़क">ग्रामीण सड़क</option>
        <option value="अन्य">अन्य</option>
      </select>
      </td>
      <td class="liq-column"><input type="text" name="active[liquidator_name][]" class="form-control" placeholder="परिसमापक का नाम"></td>
      <td class="liq-column"><input type="date" name="active[liquidator_from_date][]" class="form-control"></td>
      <td><button type="button" class="btn-inf-add-row" onclick="addNewActiveRow(this)">+</button></td>`;
            return tr;
        }
        window.toggleColumns = function (selectElement) {
            const row = selectElement.closest('tr');
            const isLiquidation = selectElement.value === 'परिसमापनाधीन';
            row.querySelectorAll('.land-column').forEach(col => {
                col.classList.toggle('cell-hidden', isLiquidation);
            });
            row.querySelectorAll('.liq-column').forEach(col => {
                col.classList.toggle('cell-hidden', !isLiquidation);
            });
        }
        window.addNewActiveRow = function (buttonElement) {
            console.log('Add row button clicked');
            const table = document.getElementById('tableActive');
            if (!table) {
                console.error('Table not found');
                return;
            }
            const tbody = table.querySelector('tbody');
            if (!tbody) {
                console.error('Tbody not found');
                return;
            }
            const newRow = window.newActiveRow();
            tbody.appendChild(newRow);
            console.log('New row added successfully');
        }

        function ensureOneRow() {
            if (tBodyActive.children.length === 0) {
                tBodyActive.appendChild(window.newActiveRow());
            }
        }

        (function initFromPost() {
            ensureOneRow();

            <?php if (!empty($_POST['active'])): ?>
                    (<?php echo json_encode($_POST['active'], JSON_UNESCAPED_UNICODE); ?>)['samiti_naam']?.forEach(() => { tBodyActive.appendChild(window.newActiveRow()); });
                const rows = document.querySelectorAll('#tableActive tbody tr');
                rows.forEach(r => {
                    const sel = r.querySelector('select[name="active[active_status][]"]');
                    if (sel) { window.toggleColumns(sel); }
                });
            <?php elseif (!empty($edit_prefill)): ?>
                tBodyActive.innerHTML = '';
                const data = <?php echo json_encode($edit_prefill['active'] ?? [], JSON_UNESCAPED_UNICODE); ?>;
                const tr = window.newActiveRow();
                tr.querySelector('select[name="active[active_status][]"]').value = data['active_status'] || '';
                tr.querySelector('input[name="active[ncd_id][]"]').value = data['ncd_id'] || '';
                tr.querySelector('input[name="active[samiti_naam][]"]').value = data['samiti_naam'] || '';
                tr.querySelector('input[name="active[land_area][]"]').value = data['land_area'] || '';
                const landStSelect = tr.querySelector('select[name="active[land_sthiti][]"]');
                if (landStSelect) landStSelect.value = data['land_sthiti'] || '';
                const placeSel = tr.querySelector('select[name="active[society_land][]"]');
                if (placeSel) placeSel.value = data['society_land'] || '';
                const godamSel = tr.querySelector('select[name="active[godown_suitable][]"]');
                if (godamSel) godamSel.value = data['godown_suitable'] || '';
                tr.querySelector('input[name="active[rack_distance][]"]').value = data['rack_distance'] || '';
                const arrivedSel = tr.querySelector('select[name="active[arrived_land_type][]"]');
                if (arrivedSel) arrivedSel.value = data['arrived_land_type'] || '';
                tr.querySelector('input[name="active[liquidator_name][]"]').value = data['liquidator_name'] || '';
                tr.querySelector('input[name="active[liquidator_from_date][]"]').value = data['liquidator_from_date'] || '';

                tBodyActive.appendChild(tr);
                const sel = tr.querySelector('select[name="active[active_status][]"]');
                if (sel) window.toggleColumns(sel);
                <?php if (!empty($edit_prefill)): ?>
                    document.getElementById('mandal_name').value = <?php echo json_encode($edit_prefill['mandal_name']); ?>;
                    document.getElementById('janpad_name').value = <?php echo json_encode($edit_prefill['janpad_name']); ?>;
                    // if you have inputs for total_union etc. add lines:
                    // document.querySelector('input[name="total_union"]').value = <?php echo json_encode($edit_prefill['total_union']); ?>;
                    // document.querySelector('input[name="active_union"]').value = <?php echo json_encode($edit_prefill['active_union']); ?>;
                    // ...
                    // lat/long
                    var latH = document.getElementById('lat');
                    var lngH = document.getElementById('long');
                    if (latH) latH.value = <?php echo json_encode($edit_prefill['latitude']); ?>;
                    if (lngH) lngH.value = <?php echo json_encode($edit_prefill['longitude']); ?>;
                <?php endif; ?>

            <?php endif; ?>
        })();

    })();
</script>

<?php
page_footer_start();
page_footer_end();
