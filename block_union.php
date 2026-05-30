<?php
include("scripts/settings.php");
error_reporting(E_ALL);
ini_set("display_errors", 1);

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
    mysqli_query($db, $del_sql);
}

$edit_prefill = [];
if ($edit_id > 0 && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $q = "SELECT * FROM block_union WHERE sno = " . intval($edit_id) . " AND (is_deleted IS NULL OR is_deleted = 0) LIMIT 1";
    $r = mysqli_query($db, $q);
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $edit_prefill = [
            'mandal_name' => $row['mandal_name'] ?? '',
            'janpad_name' => $row['janpad_name'] ?? '',
            'total_union' => $row['total_union'] ?? '',
            'active_union' => $row['active_union'] ?? '',
            'inactive_union' => $row['inactive_union'] ?? '',
            'liquidation_union' => $row['liquidation_union'] ?? '',
            'latitude' => $row['latitude'] ?? '',
            'longitude' => $row['longitude'] ?? ''
        ];

        $edit_prefill['active'] = [
            'active_status' => [$row['row_status'] ?? ''],
            'ncd_id' => [$row['ncd_id'] ?? ''],
            'samiti_naam' => [$row['samiti_naam'] ?? ''],
            'land_area' => [$row['land_area'] ?? ''],
            'land_sthiti' => [$row['land_sthiti'] ?? ''],
            'society_land' => [$row['society_land'] ?? ''],
            'godown_suitable' => [$row['godown_suitable'] ?? ''],
            'rack_distance' => [$row['rack_distance'] ?? ''],
            'arrived_land_type' => [$row['arrived_land_type'] ?? ''],
            'liquidator_name' => [$row['liquidator_name'] ?? ''],
            'liquidator_from_date' => [$row['liquidator_from_date'] ?? ''],
            // new per-row fields
            'kabja_vivad' => [$row['kabja_vivad'] ?? ''],
            'kabja_prayas' => [$row['kabja_prayas'] ?? ''],
            'rajsv_abhilekh_darj' => [$row['rajsv_abhilekh_darj'] ?? ''],
            'rajsv_na_darj_karan' => [$row['rajsv_na_darj_karan'] ?? ''],
            'rajsv_prayas' => [$row['rajsv_prayas'] ?? ''],
            // per-row lat/long if present
            'latitude' => [$row['latitude'] ?? ''],
            'longitude' => [$row['longitude'] ?? '']
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
    $posted_edit_id = intval($_POST['edit_id'] ?? 0);

    if ($mandal_name === '' || $janpad_name === '') {
        echo "<script>alert('कृपया मण्डल और जनपद चयन करें।');</script>";
    } else {
        $active = $_POST['active'] ?? [];
        $rows = max(
            count($active['samiti_naam'] ?? []),
            count($active['ncd_id'] ?? []),
            count($active['active_status'] ?? []),
            count($active['land_area'] ?? []),
            count($active['land_sthiti'] ?? []),
            count($active['society_land'] ?? []),
            count($active['godown_suitable'] ?? []),
            count($active['rack_distance'] ?? []),
            count($active['arrived_land_type'] ?? []),
            count($active['liquidator_name'] ?? []),
            count($active['liquidator_from_date'] ?? []),
            count($active['kabja_vivad'] ?? []),
            count($active['kabja_prayas'] ?? []),
            count($active['rajsv_abhilekh_darj'] ?? []),
            count($active['rajsv_na_darj_karan'] ?? []),
            count($active['rajsv_prayas'] ?? []),
            count($active['latitude'] ?? []),
            count($active['longitude'] ?? [])
        );

        if ($posted_edit_id > 0) {
            $del_sql = "DELETE FROM block_union WHERE sno = " . intval($posted_edit_id) . " LIMIT 1";
            mysqli_query($db, $del_sql);
        }

        $rowsInserted = 0;
        for ($i = 0; $i < $rows; $i++) {
            $samiti_naam_i = trim($active['samiti_naam'][$i] ?? '');
            if ($samiti_naam_i === '')
                continue; // skip empty rows

            $ncd_id_i = trim($active['ncd_id'][$i] ?? '');
            $active_status_i = trim($active['active_status'][$i] ?? 'सक्रिय');
            $land_area_i = trim($active['land_area'][$i] ?? '');
            $land_sthiti_i = trim($active['land_sthiti'][$i] ?? '');
            $society_land_i = trim($active['society_land'][$i] ?? '');
            $godown_suitable_i = trim($active['godown_suitable'][$i] ?? '');
            $rack_distance_i = trim($active['rack_distance'][$i] ?? '');
            $arrived_land_type_i = trim($active['arrived_land_type'][$i] ?? '');
            $liquidator_name_i = trim($active['liquidator_name'][$i] ?? '');
            $liquidator_date_i = trim($active['liquidator_from_date'][$i] ?? '');

            $kabja_vivad_i = trim($active['kabja_vivad'][$i] ?? '');
            $kabja_prayas_i = trim($active['kabja_prayas'][$i] ?? '');
            $rajsv_abhilekh_darj_i = trim($active['rajsv_abhilekh_darj'][$i] ?? '');
            $rajsv_na_darj_karan_i = trim($active['rajsv_na_darj_karan'][$i] ?? '');
            $rajsv_prayas_i = trim($active['rajsv_prayas'][$i] ?? '');

            $latitude_i = trim($active['latitude'][$i] ?? ($latitude ?? ''));
            $longitude_i = trim($active['longitude'][$i] ?? ($longitude ?? ''));

            $base_cols = [
                "mandal_name",
                "janpad_name",
                "total_union",
                "active_union",
                "inactive_union",
                "liquidation_union",
                "samiti_naam",
                "ncd_id",
                "row_status",
                "land_area",
                "land_sthiti",
                "society_land",
                "godown_suitable",
                "rack_distance",
                "arrived_land_type",
                "liquidator_name",
                "liquidator_from_date",
                "latitude",
                "longitude",
                "kabja_vivad",
                "kabja_prayas",
                "rajsv_abhilekh_darj",
                "rajsv_na_darj_karan",
                "rajsv_prayas"
            ];

            if ($posted_edit_id > 0) {
                $meta_cols = ["edited_by", "edition_time"];
                $meta_vals = [
                    (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : "NULL"),
                    "NOW()"
                ];
            } else {
                $meta_cols = ["created_by", "creation_time"];
                $meta_vals = [
                    (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : "NULL"),
                    "NOW()"
                ];
            }

            $cols = array_merge($base_cols, $meta_cols);

            $vals = [
                "'" . esc($db, $mandal_name) . "'",
                "'" . esc($db, $janpad_name) . "'",
                ($total_union !== '' ? "'" . esc($db, $total_union) . "'" : "NULL"),
                ($active_union !== '' ? "'" . esc($db, $active_union) . "'" : "NULL"),
                ($inactive_union !== '' ? "'" . esc($db, $inactive_union) . "'" : "NULL"),
                ($liquidation_union !== '' ? "'" . esc($db, $liquidation_union) . "'" : "NULL"),
                "'" . esc($db, $samiti_naam_i) . "'",
                ($ncd_id_i !== '' ? "'" . esc($db, $ncd_id_i) . "'" : "NULL"),
                "'" . esc($db, $active_status_i) . "'",
                ($land_area_i !== '' ? "'" . esc($db, $land_area_i) . "'" : "NULL"),
                ($land_sthiti_i !== '' ? "'" . esc($db, $land_sthiti_i) . "'" : "NULL"),
                ($society_land_i !== '' ? "'" . esc($db, $society_land_i) . "'" : "NULL"),
                ($godown_suitable_i !== '' ? "'" . esc($db, $godown_suitable_i) . "'" : "NULL"),
                ($rack_distance_i !== '' ? "'" . esc($db, $rack_distance_i) . "'" : "NULL"),
                ($arrived_land_type_i !== '' ? "'" . esc($db, $arrived_land_type_i) . "'" : "NULL"),
                ($active_status_i === 'परिसमापनाधीन' && $liquidator_name_i !== '' ? "'" . esc($db, $liquidator_name_i) . "'" : "NULL"),
                ($active_status_i === 'परिसमापनाधीन' && $liquidator_date_i !== '' ? "'" . esc($db, $liquidator_date_i) . "'" : "NULL"),
                ($latitude_i !== '' ? "'" . esc($db, $latitude_i) . "'" : "NULL"),
                ($longitude_i !== '' ? "'" . esc($db, $longitude_i) . "'" : "NULL"),
                ($kabja_vivad_i !== '' ? "'" . esc($db, $kabja_vivad_i) . "'" : "NULL"),
                ($kabja_prayas_i !== '' ? "'" . esc($db, $kabja_prayas_i) . "'" : "NULL"),
                ($rajsv_abhilekh_darj_i !== '' ? "'" . esc($db, $rajsv_abhilekh_darj_i) . "'" : "NULL"),
                ($rajsv_na_darj_karan_i !== '' ? "'" . esc($db, $rajsv_na_darj_karan_i) . "'" : "NULL"),
                ($rajsv_prayas_i !== '' ? "'" . esc($db, $rajsv_prayas_i) . "'" : "NULL")
            ];

            $vals = array_merge($vals, $meta_vals);

            $sql = "INSERT INTO block_union (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
            if (mysqli_query($db, $sql)) {
                $rowsInserted++;
            } else {
                echo "<div style='padding:12px;color:#b91c1c;'>Insert failed (row " . ($i + 1) . "): " . h(mysqli_error($db)) . "</div>";
            }
        }

        if ($rowsInserted > 0) {
            echo "<script>alert('Inserted successfully ($rowsInserted rows)');location.href=window.location.pathname;</script>";
        } else {
            echo "<script>alert('कोई वैध पंक्ति नहीं मिली।');</script>";
        }
    }
}
$lat_pref = $_POST['latitude'] ?? ($edit_prefill['latitude'] ?? '');
$long_pref = $_POST['longitude'] ?? ($edit_prefill['longitude'] ?? '');
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
        height: 40px;
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
        padding: 7px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
    }

    .btn-inf-add-row:hover {
        background: #059669;
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

    .active-row .form-control {
        width: 100%;
        box-sizing: border-box;
    }

    .active-row .small-note {
        margin-bottom: 6px;
    }

    @media (max-width:768px) {

        .active-row .col-sm-2,
        .active-row .col-sm-3,
        .active-row .col-sm-4 {
            max-width: 100%;
            flex-basis: 100%;
        }
    }

    /* small inline map style (per row) */
    .row-map {
        width: 100%;
        height: 180px;
        border: 1px solid #d0d7e9;
        margin-top: 8px;
        border-radius: 6px;
    }

    .coord-btn {
        font-size: 13px;
        padding: 6px 8px;
        border-radius: 6px;
        background: #10b981;
        color: #fff;
        border: none;
        cursor: pointer;
        height: 36px;
    }

    .coord-btn.danger {
        background: #ef4444;
    }
    .gps-missing {
        display: inline-block;
        background: #b00020;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        animation: blk 1s linear infinite;
    }

    @keyframes blk {

        0%,
        100% {
            opacity: 1
        }

        50% {
            opacity: 0
        }
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<?php

$DIV_ID = $_SESSION['division_id'] ?? null;
if (is_array($DIV_ID))
    $DIV_ID = $DIV_ID[0] ?? null;

$DIS_ID = $_SESSION['district_id'] ?? null;
if (is_array($DIS_ID))
    $DIS_ID = $DIS_ID[0] ?? null;

if ($DIV_ID) {

    $where = ["s.is_deleted IS NULL OR s.is_deleted = 0"];

    if ($DIS_ID) {
        $where[] = "s.janpad_name = '" . esc($db, $DIS_ID) . "'";
    }

    $sql = "SELECT s.*, dt.district_name, dv.division_name
            FROM block_union s
            LEFT JOIN master_district dt ON s.janpad_name = dt.sno
            LEFT JOIN master_division dv ON dt.division_id = dv.sno
            WHERE " . implode(' AND ', $where) . "
            ORDER BY s.sno DESC";

    $result = mysqli_query($db, $sql);
    ?>

    <div class="card" style="margin-top:30px;">
        <h3 class="section-heading" style="text-align:center;">📋 रिपोर्ट</h3>

        <div class="table-responsive">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>क्रम</th>
                        <th>मण्डल</th>
                        <th>जनपद</th>
                        <th>NCD ID</th>
                        <th>समिति का नाम</th>
                        <th>भूमि क्षेत्रफल</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td style="display:flex;gap:6px;">
                                    <a href="?edit=<?= (int) $row['sno'] ?>"
                                        style="background:#1565c0;color:#fff;padding:4px 6px;border-radius:4px;text-decoration:none;">✏️
                                        Edit</a>

                                    <a href="?delete=<?= (int) $row['sno'] ?>" onclick="return confirm('Are You Sure ?');"
                                        style="background:#b00020;color:#fff;padding:4px 6px;border-radius:4px;text-decoration:none;">🗑️
                                        Delete</a>
                                </td>

                                <td><?= $i++ ?></td>
                                <td><?= h($row['division_name'] ?? '') ?></td>
                                <td><?= h($row['district_name'] ?? '') ?></td>
                                <td><?= h($row['ncd_id'] ?? '') ?></td>
                                <td><?= h($row['samiti_naam'] ?? '') ?></td>
                                <td><?= h($row['land_area'] ?? '') ?></td>
                                <td>
                                    <?= h($row['latitude'] ?? '') ?>
                                    <?php if (empty($row['latitude'])): ?>
                                        <span class="gps-missing">📍 खाली</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= h($row['longitude'] ?? '') ?>
                                    <?php if (empty($row['longitude'])): ?>
                                        <span class="gps-missing">📍 खाली</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <button onclick="toggleRow(<?= $row['sno'] ?>)"
                                        style="padding:4px 8px;background:#0a7f3f;color:#fff;border:none;border-radius:4px;cursor:pointer;">
                                        View
                                    </button>
                                </td>
                            </tr>

                            <!-- Hidden Detail Row -->
                            <tr id="detail_<?= $row['sno'] ?>" style="display:none;background:#f4f8ff;">
                                <td colspan="8" style="padding:15px;">

                                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                                        <tr style="background:#e9f1ff;">
                                            <th style="padding:6px;border:1px solid #ccc;">Field</th>
                                            <th style="padding:6px;border:1px solid #ccc;">Details</th>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">समिति सक्रिय?</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['row_status'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">भूमि स्थिति</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['land_sthiti'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">स्थान</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['society_land'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">गोदाम उपयुक्त</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['godown_suitable'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">रैक दूरी (किमी)</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['rack_distance'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">पहुंच मार्ग</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['arrived_land_type'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">कब्जा / विवादित?</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['kabja_vivad'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">राजस्व अभिलेख में दर्ज?</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['rajsv_abhilekh_darj'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">परिसमापक का नाम</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['liquidator_name'] ?? '') ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:6px;border:1px solid #ccc;">कब से परिसमापक</td>
                                            <td style="padding:6px;border:1px solid #ccc;">
                                                <?= h($row['liquidator_from_date'] ?? '') ?>
                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>

                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
}
?>

<form method="post" action="">
    <h2 class="module-title">
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
                        $selected = '';
                        if (isset($_POST['mandal_name']) && $_POST['mandal_name'] == $row_division['sno'])
                            $selected = 'selected';
                        if (isset($edit_prefill['mandal_name']) && $edit_prefill['mandal_name'] == $row_division['sno'])
                            $selected = 'selected';
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
                        $selected = '';
                        if (isset($_POST['janpad_name']) && $_POST['janpad_name'] == $row_district['sno'])
                            $selected = 'selected';
                        if (isset($edit_prefill['janpad_name']) && $edit_prefill['janpad_name'] == $row_district['sno'])
                            $selected = 'selected';
                        echo '<option value="' . (int) $row_district['sno'] . '" ' . $selected . '>' . h($row_district['district_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="card" id="bhumi-details">
        <h3 class="section-heading">🏡 खाली भूमि का विवरण</h3>

        <div id="active_rows_container">
            <?php
            $active_rows = [];
            if (!empty($_POST['active']) && is_array($_POST['active'])) {
                $postActive = $_POST['active'];
                $count = max(
                    count($postActive['active_status'] ?? []),
                    count($postActive['ncd_id'] ?? []),
                    count($postActive['samiti_naam'] ?? []),
                    count($postActive['land_area'] ?? []),
                    count($postActive['land_sthiti'] ?? []),
                    count($postActive['society_land'] ?? []),
                    count($postActive['godown_suitable'] ?? []),
                    count($postActive['rack_distance'] ?? []),
                    count($postActive['arrived_land_type'] ?? []),
                    count($postActive['liquidator_name'] ?? []),
                    count($postActive['liquidator_from_date'] ?? []),
                    count($postActive['kabja_vivad'] ?? []),
                    count($postActive['kabja_prayas'] ?? []),
                    count($postActive['rajsv_abhilekh_darj'] ?? []),
                    count($postActive['rajsv_na_darj_karan'] ?? []),
                    count($postActive['rajsv_prayas'] ?? []),
                    count($postActive['latitude'] ?? []),
                    count($postActive['longitude'] ?? [])
                );
                for ($i = 0; $i < $count; $i++) {
                    $active_rows[] = [
                        'active_status' => $postActive['active_status'][$i] ?? '',
                        'ncd_id' => $postActive['ncd_id'][$i] ?? '',
                        'samiti_naam' => $postActive['samiti_naam'][$i] ?? '',
                        'land_area' => $postActive['land_area'][$i] ?? '',
                        'land_sthiti' => $postActive['land_sthiti'][$i] ?? '',
                        'society_land' => $postActive['society_land'][$i] ?? '',
                        'godown_suitable' => $postActive['godown_suitable'][$i] ?? '',
                        'rack_distance' => $postActive['rack_distance'][$i] ?? '',
                        'arrived_land_type' => $postActive['arrived_land_type'][$i] ?? '',
                        'liquidator_name' => $postActive['liquidator_name'][$i] ?? '',
                        'liquidator_from_date' => $postActive['liquidator_from_date'][$i] ?? '',
                        'kabja_vivad' => $postActive['kabja_vivad'][$i] ?? '',
                        'kabja_prayas' => $postActive['kabja_prayas'][$i] ?? '',
                        'rajsv_abhilekh_darj' => $postActive['rajsv_abhilekh_darj'][$i] ?? '',
                        'rajsv_na_darj_karan' => $postActive['rajsv_na_darj_karan'][$i] ?? '',
                        'rajsv_prayas' => $postActive['rajsv_prayas'][$i] ?? '',
                        'latitude' => $postActive['latitude'][$i] ?? '',
                        'longitude' => $postActive['longitude'][$i] ?? ''
                    ];
                }
            } elseif (!empty($edit_prefill['active']) && is_array($edit_prefill['active'])) {
                $ap = $edit_prefill['active'];
                $len = max(
                    count($ap['samiti_naam'] ?? []),
                    count($ap['ncd_id'] ?? []),
                    count($ap['active_status'] ?? [])
                );
                for ($i = 0; $i < $len; $i++) {
                    $active_rows[] = [
                        'active_status' => $ap['active_status'][$i] ?? '',
                        'ncd_id' => $ap['ncd_id'][$i] ?? '',
                        'samiti_naam' => $ap['samiti_naam'][$i] ?? '',
                        'land_area' => $ap['land_area'][$i] ?? '',
                        'land_sthiti' => $ap['land_sthiti'][$i] ?? '',
                        'society_land' => $ap['society_land'][$i] ?? '',
                        'godown_suitable' => $ap['godown_suitable'][$i] ?? '',
                        'rack_distance' => $ap['rack_distance'][$i] ?? '',
                        'arrived_land_type' => $ap['arrived_land_type'][$i] ?? '',
                        'liquidator_name' => $ap['liquidator_name'][$i] ?? '',
                        'liquidator_from_date' => $ap['liquidator_from_date'][$i] ?? '',
                        'kabja_vivad' => $ap['kabja_vivad'][$i] ?? '',
                        'kabja_prayas' => $ap['kabja_prayas'][$i] ?? '',
                        'rajsv_abhilekh_darj' => $ap['rajsv_abhilekh_darj'][$i] ?? '',
                        'rajsv_na_darj_karan' => $ap['rajsv_na_darj_karan'][$i] ?? '',
                        'rajsv_prayas' => $ap['rajsv_prayas'][$i] ?? '',
                        'latitude' => $ap['latitude'][$i] ?? '',
                        'longitude' => $ap['longitude'][$i] ?? ''
                    ];
                }
            } else {
                $active_rows[] = [
                    'active_status' => '',
                    'ncd_id' => '',
                    'samiti_naam' => '',
                    'land_area' => '',
                    'land_sthiti' => '',
                    'society_land' => '',
                    'godown_suitable' => '',
                    'rack_distance' => '',
                    'arrived_land_type' => '',
                    'liquidator_name' => '',
                    'liquidator_from_date' => '',
                    'kabja_vivad' => '',
                    'kabja_prayas' => '',
                    'rajsv_abhilekh_darj' => '',
                    'rajsv_na_darj_karan' => '',
                    'rajsv_prayas' => '',
                    'latitude' => '',
                    'longitude' => ''
                ];
            }

            $count = count($active_rows);
            foreach ($active_rows as $idx => $ar) {
                $i = $idx + 1;
                ?>
                <div class="row active-row" id="active_row_<?php echo $i; ?>"
                    style="border:1px solid #e6eefc;padding:12px;margin-bottom:10px;border-radius:6px;">

                    <div class="col-sm-3 form-group">
                        <label class="form-label">Latitude / Longitude</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="text" name="active[latitude][]" id="active_latitude_<?php echo $i; ?>"
                                placeholder="Lat" class="form-control" style="flex:1;"
                                value="<?php echo h($ar['latitude'] ?? ''); ?>">
                            <input type="text" name="active[longitude][]" id="active_longitude_<?php echo $i; ?>"
                                placeholder="Long" class="form-control" style="flex:1;"
                                value="<?php echo h($ar['longitude'] ?? ''); ?>">
                            <button type="button" class="coord-btn" onclick="getLocationForRow(<?php echo $i; ?>)"
                                title="Use device location">लोकेशन रिफ्रेश करें</button>
                        </div>
                        <div id="active_map_<?php echo $i; ?>" class="row-map"
                            style="display:<?php echo (!empty($ar['latitude']) && !empty($ar['longitude'])) ? 'block' : 'none'; ?>;">
                        </div>
                    </div>

                    <div class="col-sm-2 form-group">
                        <label>क्या समिति सक्रिय है?</label>
                        <select name="active[active_status][]" id="active_status_<?php echo $i; ?>" class="form-control"
                            onchange="active_toggleColumns(<?php echo $i; ?>)">
                            <option value="">--चुनें--</option>
                            <option value="सक्रिय" <?php echo ($ar['active_status'] == 'सक्रिय') ? 'selected' : ''; ?>>सक्रिय
                            </option>
                            <option value="निष्क्रिय" <?php echo ($ar['active_status'] == 'निष्क्रिय') ? 'selected' : ''; ?>>
                                निष्क्रिय</option>
                            <option value="परिसमापनाधीन" <?php echo ($ar['active_status'] == 'परिसमापनाधीन') ? 'selected' : ''; ?>>परिसमापनाधीन</option>
                        </select>
                    </div>

                    <div class="col-sm-2 form-group">
                        <label class="form-label">NCD ID</label>
                        <input type="text" name="active[ncd_id][]" id="active_ncd_id_<?php echo $i; ?>" class="form-control"
                            value="<?php echo h($ar['ncd_id']); ?>">
                    </div>

                    <div class="col-sm-3 form-group">
                        <label class="form-label">समिति का नाम</label>
                        <input type="text" name="active[samiti_naam][]" id="active_samiti_naam_<?php echo $i; ?>"
                            class="form-control" value="<?php echo h($ar['samiti_naam']); ?>">
                    </div>

                    <div class="col-sm-2 form-group land-col" id="land_cols_<?php echo $i; ?>">
                        <label class="form-label">भूमि क्षेत्रफल (हे.)</label>
                        <input type="number" name="active[land_area][]" id="active_land_area_<?php echo $i; ?>"
                            step="0.0001" min="0" class="form-control" value="<?php echo h($ar['land_area']); ?>">
                    </div>

                    <div class="col-sm-2 form-group land-col">
                        <label>भूमि की स्थिति</label>
                        <select name="active[land_sthiti][]" id="active_land_sthiti_<?php echo $i; ?>" class="form-control">
                            <option value="">--चुनें--</option>
                            <option value="उपजाऊ" <?php echo ($ar['land_sthiti'] == 'उपजाऊ') ? 'selected' : ''; ?>>उपजाऊ
                            </option>
                            <option value="बंजर" <?php echo ($ar['land_sthiti'] == 'बंजर') ? 'selected' : ''; ?>>बंजर</option>
                            <option value="rent" <?php echo ($ar['land_sthiti'] == 'rent') ? 'selected' : ''; ?>>भूमि किराये
                                पर है।</option>
                            <option value="भूमि उपलब्ध नही है" <?php echo ($ar['land_sthiti'] == 'भूमि उपलब्ध नही है') ? 'selected' : ''; ?>>भूमि उपलब्ध नही है।</option>
                        </select>
                    </div>

                    <div class="col-sm-2 form-group land-col">
                        <label>स्थान</label>
                        <select name="active[society_land][]" id="active_society_land_<?php echo $i; ?>"
                            class="form-control">
                            <option value="">--चुनें--</option>
                            <option value="समिति प्रांगण" <?php echo ($ar['society_land'] == 'समिति प्रांगण') ? 'selected' : ''; ?>>समिति प्रांगण</option>
                            <option value="अन्य स्थान" <?php echo ($ar['society_land'] == 'अन्य स्थान') ? 'selected' : ''; ?>>
                                अन्य स्थान</option>
                            <option value="भूमि नही है" <?php echo ($ar['society_land'] == 'भूमि नही है') ? 'selected' : ''; ?>>भूमि नही है।</option>
                        </select>
                    </div>

                    <div class="col-sm-2 form-group land-col">
                        <label>गोदाम उपयुक्त?</label>
                        <select name="active[godown_suitable][]" id="active_godown_suitable_<?php echo $i; ?>"
                            class="form-control">
                            <option value="">--चुनें--</option>
                            <option value="हाँ" <?php echo ($ar['godown_suitable'] == 'हाँ') ? 'selected' : ''; ?>>हाँ
                            </option>
                            <option value="नहीं" <?php echo ($ar['godown_suitable'] == 'नहीं') ? 'selected' : ''; ?>>नहीं
                            </option>
                            <option value="गोदाम उपलब्ध नही है।" <?php echo ($ar['godown_suitable'] == 'गोदाम उपलब्ध नही है।') ? 'selected' : ''; ?>>गोदाम उपलब्ध नही है।</option>
                        </select>
                    </div>

                    <div class="col-sm-2 form-group land-col">
                        <label>रैक दूरी (किमी.)</label>
                        <input type="number" name="active[rack_distance][]" id="active_rack_distance_<?php echo $i; ?>"
                            step="0.01" min="0" class="form-control" value="<?php echo h($ar['rack_distance']); ?>">
                    </div>

                    <div class="col-sm-2 form-group land-col">
                        <label>पहुंच मार्ग</label>
                        <select name="active[arrived_land_type][]" id="active_arrived_land_type_<?php echo $i; ?>"
                            class="form-control">
                            <option value="">--चुनें--</option>
                            <option value="कच्ची सड़क" <?php echo ($ar['arrived_land_type'] == 'कच्ची सड़क') ? 'selected' : ''; ?>>कच्ची सड़क</option>
                            <option value="नेशनल हाईवे" <?php echo ($ar['arrived_land_type'] == 'नेशनल हाईवे') ? 'selected' : ''; ?>>नेशनल हाईवे</option>
                            <option value="स्टेट HIGHवे" <?php echo ($ar['arrived_land_type'] == 'स्टेट HIGHवे') ? 'selected' : ''; ?>>स्टेट HIGHवे</option>
                            <option value="एम.डी.आर." <?php echo ($ar['arrived_land_type'] == 'एम.डी.आर.') ? 'selected' : ''; ?>>एम.डी.आर.</option>
                            <option value="ओ.डी.आर." <?php echo ($ar['arrived_land_type'] == 'ओ.डी.आर.') ? 'selected' : ''; ?>>ओ.डी.आर.</option>
                            <option value="ग्रामीण सड़क" <?php echo ($ar['arrived_land_type'] == 'ग्रामीण सड़क') ? 'selected' : ''; ?>>ग्रामीण सड़क</option>
                            <option value="अन्य" <?php echo ($ar['arrived_land_type'] == 'अन्य') ? 'selected' : ''; ?>>अन्य
                            </option>
                        </select>
                    </div>

                    <div class="col-sm-3 form-group liq-col" id="liq_cols_<?php echo $i; ?>"
                        style="display:<?php echo ($ar['active_status'] === 'परिसमापनाधीन') ? 'block' : 'none'; ?>;">
                        <label>परिसमापक का नाम</label>
                        <input type="text" name="active[liquidator_name][]" id="active_liquidator_name_<?php echo $i; ?>"
                            class="form-control" value="<?php echo h($ar['liquidator_name']); ?>">
                    </div>

                    <div class="col-sm-2 form-group liq-col"
                        style="display:<?php echo ($ar['active_status'] === 'परिसमापनाधीन') ? 'block' : 'none'; ?>;">
                        <label>कब से परिसमापक</label>
                        <input type="date" name="active[liquidator_from_date][]"
                            id="active_liquidator_from_date_<?php echo $i; ?>" class="form-control"
                            value="<?php echo h($ar['liquidator_from_date']); ?>">
                    </div>

                    <div class="col-sm-2 form-group">
                        <label>कब्जा / विवादित?</label>
                        <select name="active[kabja_vivad][]" id="active_kabja_vivad_<?php echo $i; ?>" class="form-control"
                            onchange="toggleKabjaPrayasRow(<?php echo $i; ?>)">
                            <option value="">--चुनें--</option>
                            <option value="yes" <?php echo ($ar['kabja_vivad'] === 'yes') ? 'selected' : ''; ?>>हाँ</option>
                            <option value="no" <?php echo ($ar['kabja_vivad'] === 'no') ? 'selected' : ''; ?>>नहीं</option>
                        </select>
                    </div>


                    <div class="col-sm-4 form-group" id="active_kabja_prayas_wrap_<?php echo $i; ?>"
                        style="display:<?php echo ($ar['kabja_vivad'] === 'yes') ? 'block' : 'none'; ?>;">
                        <label class="small-note" style="display:block;margin-bottom:6px;">किये गए प्रयास दर्ज करें</label>
                        <textarea name="active[kabja_prayas][]" id="active_kabja_prayas_<?php echo $i; ?>"
                            class="form-control" rows="2"><?php echo h($ar['kabja_prayas']); ?></textarea>
                    </div>

                    <div class="col-sm-2 form-group">
                        <label>राजस्व अभिलेख में दर्ज होने की स्थिति</label>
                        <select name="active[rajsv_abhilekh_darj][]" id="active_rajsv_abhilekh_darj_<?php echo $i; ?>"
                            class="form-control" onchange="toggleRajsvRow(<?php echo $i; ?>)">
                            <option value="">--चुनें--</option>
                            <option value="yes" <?php echo ($ar['rajsv_abhilekh_darj'] === 'yes') ? 'selected' : ''; ?>>हाँ
                            </option>
                            <option value="no" <?php echo ($ar['rajsv_abhilekh_darj'] === 'no') ? 'selected' : ''; ?>>नहीं
                            </option>
                        </select>
                    </div>

                    <div class="col-sm-6 form-group" id="active_rajsv_wrap_<?php echo $i; ?>"
                        style="display:flex;gap:8px;align-items:flex-start;">
                        <div id="active_rajsv_na_wrap_<?php echo $i; ?>"
                            style="flex:1; display:<?php echo ($ar['rajsv_abhilekh_darj'] === 'no') ? 'block' : 'none'; ?>;">
                            <label class="small-note" style="display:block;margin-bottom:6px;">दर्ज ना होने का कारण</label>
                            <textarea name="active[rajsv_na_darj_karan][]" id="active_rajsv_na_darj_karan_<?php echo $i; ?>"
                                class="form-control" rows="2"><?php echo h($ar['rajsv_na_darj_karan']); ?></textarea>
                        </div>

                        <div id="active_rajsv_prayas_wrap_<?php echo $i; ?>"
                            style="flex:1; display:<?php echo ($ar['rajsv_abhilekh_darj'] === 'no') ? 'block' : 'none'; ?>;">
                            <label class="small-note" style="display:block;margin-bottom:6px;">यदि नहीं है तो किये जाने वाले
                                प्रयास का विवरण / अन्य विवरण</label>
                            <textarea name="active[rajsv_prayas][]" id="active_rajsv_prayas_<?php echo $i; ?>"
                                class="form-control" rows="2"><?php echo h($ar['rajsv_prayas']); ?></textarea>
                        </div>
                    </div>

                    <div class="col-sm-1 form-group" style="display:flex;align-items:flex-end;gap:6px;">
                        <button type="button" class="btn-inf-add-row" onclick="active_add_row(<?php echo $i; ?>)">नई पंक्ति
                            जोड़े [+]</button>
                        <?php if ($i > 1): ?>
                            <button type="button" class="btn btn-info"
                                style="background:#ef4444;border:none;padding:6px 8px;border-radius:6px;color:#fff;"
                                onclick="active_remove_row(<?php echo $i; ?>)">−</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php } // end foreach ?>

            <input type="hidden" name="active_count" id="active_count" value="<?php echo $count; ?>">
        </div>
        <div style="text-align:center;margin-top:12px;">
            <input type="hidden" name="edit_id" id="edit_id" value="<?php echo (int) $edit_id; ?>">
            <button type="submit" class="btn-primary"><?php echo ($edit_id ? 'Update' : 'Submit'); ?></button>
            <?php if ($edit_id): ?>
                <a href="<?php echo h($_SERVER['PHP_SELF']); ?>" style="margin-left:10px;" class="btn-info">Cancel Edit</a>
            <?php endif; ?>
        </div>
    </div>


</form>

<script>
    window.active_add_row = function (callerIdx) {
        var total = parseInt(document.getElementById('active_count').value) || 0;
        for (var i = 1; i <= total; i++) {
            var sam = document.getElementById('active_samiti_naam_' + i);
            var ncd = document.getElementById('active_ncd_id_' + i);
            var status = document.getElementById('active_status_' + i);
            if (!sam || !ncd || !status) continue;
            if (sam.value.trim() === '' && ncd.value.trim() === '' && status.value.trim() === '') {
                alert("पंक्ति संख्या " + i + " खाली है — कृपया पहले इसे पूरा करें या हटाएँ।");
                if (sam) sam.focus();
                return;
            }
        }

        total = total + 1;
        document.getElementById('active_count').value = total;

        var container = document.getElementById('active_rows_container');

        var div = document.createElement('div');
        div.className = 'row active-row';
        div.id = 'active_row_' + total;
        div.style = 'border:1px solid #e6eefc;padding:12px;margin-bottom:10px;border-radius:6px;';

        var html = '';

        html += '<div class="col-sm-3 form-group">';
        html += '<label class="form-label">Latitude / Longitude</label>';
        html += '<div style="display:flex;gap:8px;align-items:center;">';
        html += '<input type="text" name="active[latitude][]" id="active_latitude_' + total + '" placeholder="Lat" class="form-control" style="flex:1;">';
        html += '<input type="text" name="active[longitude][]" id="active_longitude_' + total + '" placeholder="Long" class="form-control" style="flex:1;">';
        html += '<button type="button" class="coord-btn" onclick="getLocationForRow(' + total + ')">लोकेशन रिफ्रेश करें</button>';
        html += '</div>';
        html += '<div id="active_map_' + total + '" class="row-map" style="display:none;"></div>';
        html += '</div>';

        html += '<div class="col-sm-2 form-group">';
        html += '<label>क्या समिति सक्रिय है?</label>';
        html += '<select name="active[active_status][]" id="active_status_' + total + '" class="form-control" onchange="active_toggleColumns(' + total + ')">';
        html += '<option value="">--चुनें--</option>';
        html += '<option value="सक्रिय">सक्रिय</option>';
        html += '<option value="निष्क्रिय">निष्क्रिय</option>';
        html += '<option value="परिसमापनाधीन">परिसमापनाधीन</option>';
        html += '</select></div>';

        html += '<div class="col-sm-2 form-group"><label class="form-label">NCD ID</label><input type="text" name="active[ncd_id][]" id="active_ncd_id_' + total + '" class="form-control"></div>';
        html += '<div class="col-sm-3 form-group"><label class="form-label">समिति का नाम</label><input type="text" name="active[samiti_naam][]" id="active_samiti_naam_' + total + '" class="form-control"></div>';

        html += '<div class="col-sm-2 form-group land-col"><label class="form-label">भूमि क्षेत्रफल (हे.)</label><input type="number" step="0.0001" min="0" name="active[land_area][]" id="active_land_area_' + total + '" class="form-control"></div>';

        html += '<div class="col-sm-2 form-group land-col"><label>भूमि की स्थिति</label><select name="active[land_sthiti][]" id="active_land_sthiti_' + total + '" class="form-control"><option value="">--चुनें--</option><option value="उपजाऊ">उपजाऊ</option><option value="बंजर">बंजर</option><option value="rent">भूमि किराये पर है।</option><option value="भूमि उपलब्ध नही है">भूमि उपलब्ध नही है।</option></select></div>';

        html += '<div class="col-sm-2 form-group land-col"><label>स्थान</label><select name="active[society_land][]" id="active_society_land_' + total + '" class="form-control"><option value="">--चुनें--</option><option value="समिति प्रांगण">समिति प्रांगण</option><option value="अन्य स्थान">अन्य स्थान</option><option value="भूमि नही है">भूमि नही है।</option></select></div>';

        html += '<div class="col-sm-2 form-group land-col"><label>गोदाम उपयुक्त?</label><select name="active[godown_suitable][]" id="active_godown_suitable_' + total + '" class="form-control"><option value="">--चुनें--</option><option value="हाँ">हाँ</option><option value="नहीं">नहीं</option><option value="गोदाम उपलब्ध नही है।">गोदाम उपलब्ध नही है।</option></select></div>';

        html += '<div class="col-sm-2 form-group land-col"><label>रैक दूरी (किमी.)</label><input type="number" step="0.01" min="0" name="active[rack_distance][]" id="active_rack_distance_' + total + '" class="form-control"></div>';

        html += '<div class="col-sm-2 form-group land-col"><label>पहुंच मार्ग</label><select name="active[arrived_land_type][]" id="active_arrived_land_type_' + total + '" class="form-control"><option value="">--चुनें--</option><option value="कच्ची सड़क">कच्ची सड़क</option><option value="नेशनल हाईवे">नेशनल हाईवे</option><option value="स्टेट HIGHवे">स्टेट HIGHवे</option><option value="एम.डी.आर.">एम.डी.आर.</option><option value="ओ.डी.आर.">ओ.डी.आर.</option><option value="ग्रामीण सड़क">ग्रामीण सड़क</option><option value="अन्य">अन्य</option></select></div>';

        html += '<div class="col-sm-3 form-group liq-col" id="liq_cols_' + total + '" style="display:none;"><label>परिसमापक का नाम</label><input type="text" name="active[liquidator_name][]" id="active_liquidator_name_' + total + '" class="form-control"></div>';

        html += '<div class="col-sm-2 form-group liq-col" style="display:none;"><label>कब से परिसमापक</label><input type="date" name="active[liquidator_from_date][]" id="active_liquidator_from_date_' + total + '" class="form-control"></div>';

        html += '<div class="col-sm-2 form-group">';
        html += '<label>कब्जा / विवादित?</label>';
        html += '<select name="active[kabja_vivad][]" id="active_kabja_vivad_' + total + '" class="form-control" onchange="toggleKabjaPrayasRow(' + total + ')">';
        html += '<option value="">--चुनें--</option><option value="yes">हाँ</option><option value="no">नहीं</option>';
        html += '</select></div>';

        html += '<div class="col-sm-3 form-group" id="active_kabja_prayas_wrap_' + total + '" style="display:none;">';
        html += '<label class="small-note" style="display:block;margin-bottom:6px;">किये गए प्रयास दर्ज करें</label>';
        html += '<textarea name="active[kabja_prayas][]" id="active_kabja_prayas_' + total + '" class="form-control" rows="2"></textarea>';
        html += '</div>';

        html += '<div class="col-sm-2 form-group">';
        html += '<label>राजस्व अभिलेख में दर्ज होने की स्थिति</label>';
        html += '<select name="active[rajsv_abhilekh_darj][]" id="active_rajsv_abhilekh_darj_' + total + '" class="form-control" onchange="toggleRajsvRow(' + total + ')">';
        html += '<option value="">--चुनें--</option><option value="yes">हाँ</option><option value="no">नहीं</option>';
        html += '</select></div>';

        html += '<div class="col-sm-6 form-group" id="active_rajsv_wrap_' + total + '" style="display:flex;gap:8px;align-items:flex-start;">';
        html += '<div id="active_rajsv_na_wrap_' + total + '" style="flex:1;display:none;">';
        html += '<label class="small-note" style="display:block;margin-bottom:6px;">दर्ज ना होने का कारण</label>';
        html += '<textarea name="active[rajsv_na_darj_karan][]" id="active_rajsv_na_darj_karan_' + total + '" class="form-control" rows="2"></textarea>';
        html += '</div>';
        html += '<div id="active_rajsv_prayas_wrap_' + total + '" style="flex:1;display:none;">';
        html += '<label class="small-note" style="display:block;margin-bottom:6px;">यदि नहीं है तो किये जाने वाले प्रयास का विवरण / अन्य विवरण</label>';
        html += '<textarea name="active[rajsv_prayas][]" id="active_rajsv_prayas_' + total + '" class="form-control" rows="2"></textarea>';
        html += '</div>';
        html += '</div>';

        html += '<div class="col-sm-1 form-group" style="display:flex;align-items:flex-end;gap:6px;">';
        html += '<button type="button" class="btn-inf-add-row" onclick="active_add_row(' + total + ')">+</button>';
        html += '<button type="button" class="btn" style="background:#ef4444;border:none;padding:6px 8px;border-radius:6px;color:#fff;" onclick="active_remove_row(' + total + ')">−</button>';
        html += '</div>';

        div.innerHTML = html;
        container.appendChild(div);

        try {
            active_toggleColumns(total);
            toggleKabjaPrayasRow(total);
            toggleRajsvRow(total);
        } catch (e) {
        }

        var focusEl = document.getElementById('active_samiti_naam_' + total);
        if (focusEl) focusEl.focus();
    };

    window.active_remove_row = function (idx) {
        var el = document.getElementById('active_row_' + idx);
        if (!el) return;
        el.parentNode.removeChild(el);

        var container = document.getElementById('active_rows_container');
        var rows = Array.prototype.slice.call(container.querySelectorAll('.active-row'));
        for (var i = 0; i < rows.length; i++) {
            var r = rows[i];
            var newIndex = i + 1;
            r.id = 'active_row_' + newIndex;

            var els = r.querySelectorAll('[id]');
            els.forEach(function (node) {
                node.id = node.id.replace(/_\d+$/, '_' + newIndex);
            });

            var liq = r.querySelector('[id^="liq_cols_"]');
            if (liq) liq.id = 'liq_cols_' + newIndex;
            var kabjaWrap = r.querySelector('[id^="active_kabja_prayas_wrap_"]');
            if (kabjaWrap) kabjaWrap.id = 'active_kabja_prayas_wrap_' + newIndex;
            var kabjaPrayas = r.querySelector('[id^="active_kabja_prayas_"]');
            if (kabjaPrayas) kabjaPrayas.id = 'active_kabja_prayas_' + newIndex;
            var rajsvWrap = r.querySelector('[id^="active_rajsv_wrap_"]');
            if (rajsvWrap) rajsvWrap.id = 'active_rajsv_wrap_' + newIndex;
            var rajsvNa = r.querySelector('[id^="active_rajsv_na_wrap_"]');
            if (rajsvNa) rajsvNa.id = 'active_rajsv_na_wrap_' + newIndex;
            var rajsvPr = r.querySelector('[id^="active_rajsv_prayas_wrap_"]');
            if (rajsvPr) rajsvPr.id = 'active_rajsv_prayas_wrap_' + newIndex;

            var mapWrap = r.querySelector('[id^="active_map_"]');
            if (mapWrap) mapWrap.id = 'active_map_' + newIndex;
            var latInp = r.querySelector('[id^="active_latitude_"]');
            if (latInp) latInp.id = 'active_latitude_' + newIndex;
            var lonInp = r.querySelector('[id^="active_longitude_"]');
            if (lonInp) lonInp.id = 'active_longitude_' + newIndex;
            var coordBtn = r.querySelector('[onclick^="getLocationForRow("]');
            if (coordBtn) {
                coordBtn.setAttribute('onclick', 'getLocationForRow(' + newIndex + ')');
            }
        }

        document.getElementById('active_count').value = rows.length;

        var total = rows.length;
        for (var j = 1; j <= total; j++) {
            try {
                active_toggleColumns(j);
                toggleKabjaPrayasRow(j);
                toggleRajsvRow(j);
            } catch (e) { }
        }
    };

    window.active_toggleColumns = function (idx) {
        try {
            var statusEl = document.getElementById('active_status_' + idx);
            if (!statusEl) return;
            var status = statusEl.value || '';

            var row = document.getElementById('active_row_' + idx);
            if (!row) return;

            var landCols = row.querySelectorAll('.land-col');
            var liqCols = row.querySelectorAll('.liq-col');
            var kabjaSelect = document.getElementById('active_kabja_vivad_' + idx);
            var kabjaWrap = document.getElementById('active_kabja_prayas_wrap_' + idx);
            var rajsvSelect = document.getElementById('active_rajsv_abhilekh_darj_' + idx);
            var rajsvWrap = document.getElementById('active_rajsv_wrap_' + idx);

            if (status === 'परिसमापनाधीन') {
                landCols.forEach(function (el) { el.style.display = 'none'; });
                liqCols.forEach(function (el) { el.style.display = 'block'; });
                if (kabjaSelect) kabjaSelect.parentNode.style.display = 'none';
                if (kabjaWrap) kabjaWrap.style.display = 'none';
                if (rajsvSelect) rajsvSelect.parentNode.style.display = 'none';
                if (rajsvWrap) rajsvWrap.style.display = 'none';
            } else {
                landCols.forEach(function (el) { el.style.display = 'block'; });
                liqCols.forEach(function (el) { el.style.display = 'none'; });
                if (kabjaSelect) kabjaSelect.parentNode.style.display = 'block';
                if (kabjaWrap) {
                    toggleKabjaPrayasRow(idx);
                }
                if (rajsvSelect) rajsvSelect.parentNode.style.display = 'block';
                if (rajsvWrap) {
                    toggleRajsvRow(idx);
                }
            }
        } catch (e) {
            console && console.error && console.error(e);
        }
    };

    window.toggleKabjaPrayasRow = function (idx) {
        try {
            var sel = document.getElementById('active_kabja_vivad_' + idx);
            var wrap = document.getElementById('active_kabja_prayas_wrap_' + idx);
            if (!sel || !wrap) return;
            if (sel.value === 'yes') {
                wrap.style.display = 'block';
            } else {
                wrap.style.display = 'none';
                var ta = document.getElementById('active_kabja_prayas_' + idx);
                if (ta) ta.value = '';
            }
        } catch (e) {
            console && console.error && console.error(e);
        }
    };

    window.toggleRajsvRow = function (idx) {
        try {
            var sel = document.getElementById('active_rajsv_abhilekh_darj_' + idx);
            var naWrap = document.getElementById('active_rajsv_na_wrap_' + idx);
            var prWrap = document.getElementById('active_rajsv_prayas_wrap_' + idx);
            if (!sel) return;

            if (sel.value === 'no') {
                if (naWrap) naWrap.style.display = 'block';
                if (prWrap) prWrap.style.display = 'block';
            } else {
                if (naWrap) {
                    naWrap.style.display = 'none';
                    var naTa = document.getElementById('active_rajsv_na_darj_karan_' + idx);
                    if (naTa) naTa.value = '';
                }
                if (prWrap) {
                    prWrap.style.display = 'none';
                    var prTa = document.getElementById('active_rajsv_prayas_' + idx);
                    if (prTa) prTa.value = '';
                }
            }
        } catch (e) { }
    };

    window._init_active_rows = function () {
        try {
            var total = parseInt(document.getElementById('active_count').value) || 0;
            for (var j = 1; j <= total; j++) {
                if (!document.getElementById('active_row_' + j)) continue;
                (function (idx) {
                    var st = document.getElementById('active_status_' + idx);
                    if (st) st.onchange = function () { active_toggleColumns(idx); };
                    var kab = document.getElementById('active_kabja_vivad_' + idx);
                    if (kab) kab.onchange = function () { toggleKabjaPrayasRow(idx); };
                    var raj = document.getElementById('active_rajsv_abhilekh_darj_' + idx);
                    if (raj) raj.onchange = function () { toggleRajsvRow(idx); };

                    active_toggleColumns(idx);
                    toggleKabjaPrayasRow(idx);
                    toggleRajsvRow(idx);

                    var latVal = document.getElementById('active_latitude_' + idx);
                    var lonVal = document.getElementById('active_longitude_' + idx);
                    if (latVal && lonVal && latVal.value && lonVal.value) {
                        (function (ii, la, lo) {
                            setTimeout(function () { showMapForRow(ii, parseFloat(la), parseFloat(lo)); }, 100);
                        })(idx, latVal.value, lonVal.value);
                        var mapWrap = document.getElementById('active_map_' + idx);
                        if (mapWrap) mapWrap.style.display = 'block';
                    }
                })(j);
            }
        } catch (e) {
            console && console.error && console.error(e);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _init_active_rows);
    } else {
        _init_active_rows();
    }

    var _orig_add = window.active_add_row;
    if (_orig_add) {
        window.active_add_row = function (callerIdx) {
            _orig_add(callerIdx);
            setTimeout(_init_active_rows, 50);
        };
    }
    var _orig_remove = window.active_remove_row;
    if (_orig_remove) {
        window.active_remove_row = function (idx) {
            _orig_remove(idx);
            setTimeout(_init_active_rows, 50);
        };
    }

    window.getLocationForRow = function (idx) {
        var latEl = document.getElementById('active_latitude_' + idx);
        var lonEl = document.getElementById('active_longitude_' + idx);
        var mapWrap = document.getElementById('active_map_' + idx);

        if (latEl && lonEl && latEl.value && lonEl.value) {
            if (mapWrap) mapWrap.style.display = 'block';
            showMapForRow(idx, parseFloat(latEl.value), parseFloat(lonEl.value));
            return;
        }

        if (!navigator.geolocation) {
            alert('ब्राउज़र में Geolocation समर्थित नहीं है।');
            return;
        }

        var btn = event && event.target ? event.target : null;
        var prevHtml = btn ? btn.innerHTML : null;
        if (btn) btn.innerHTML = '⏳';

        navigator.geolocation.getCurrentPosition(function (position) {
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;
            if (latEl) latEl.value = lat.toFixed(6);
            if (lonEl) lonEl.value = lon.toFixed(6);
            if (mapWrap) {
                mapWrap.style.display = 'block';
                showMapForRow(idx, lat, lon);
            }
            if (btn) btn.innerHTML = prevHtml || 'लोकेशन रिफ्रेश करें';
        }, function (err) {
            alert('Location error: ' + (err.message || err.code));
            if (btn) btn.innerHTML = prevHtml || 'लोकेशन रिफ्रेश करें';
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    };

    window._rowMaps = window._rowMaps || {};

    window.showMapForRow = function (idx, lat, lon) {
        var id = 'active_map_' + idx;
        var el = document.getElementById(id);
        if (!el) return;

        if (window._rowMaps[idx]) {
            try {
                var m = window._rowMaps[idx].map;
                var mk = window._rowMaps[idx].marker;
                m.setView([lat, lon], 15);
                mk.setLatLng([lat, lon]);
                return;
            } catch (e) {
            }
        }

        el.innerHTML = '';
        var map = L.map(id, { scrollWheelZoom: false }).setView([lat, lon], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([lat, lon], { draggable: true }).addTo(map);

        marker.on('dragend', function () {
            var p = marker.getLatLng();
            var latEl = document.getElementById('active_latitude_' + idx);
            var lonEl = document.getElementById('active_longitude_' + idx);
            if (latEl) latEl.value = p.lat.toFixed(6);
            if (lonEl) lonEl.value = p.lng.toFixed(6);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            var latEl = document.getElementById('active_latitude_' + idx);
            var lonEl = document.getElementById('active_longitude_' + idx);
            if (latEl) latEl.value = e.latlng.lat.toFixed(6);
            if (lonEl) lonEl.value = e.latlng.lng.toFixed(6);
        });

        window._rowMaps[idx] = { map: map, marker: marker };
    };
</script>
<script>
    function toggleRow(id) {
        var row = document.getElementById("detail_" + id);
        if (row.style.display === "none") {
            row.style.display = "table-row";
        } else {
            row.style.display = "none";
        }
    }
</script>
<?php
page_footer_start();
page_footer_end();
?>