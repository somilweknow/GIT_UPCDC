<?php
// upss_form.php
include("scripts/settings.php");
page_header_start();
page_header_end();
page_sidebar();
error_reporting(E_ALL);

function h($v)
{
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}
function e($db, $v)
{
    return mysqli_real_escape_string($db, trim((string) ($v ?? '')));
}

/* Status normalization copied from previous file */
function status_code($v)
{
    $v = trim((string) $v);
    if ($v === '')
        return '';
    $active_syn = ['सक्रिय', 'सक्रिय ', 'सक्रिया', 'active'];
    $non_syn = ['निष्क्रिय', 'non-active', 'non active', 'non_active'];
    $closed_syn = ['परिसमापनाधीन', 'परिसमापनाधीन ', 'परिसमापन', 'closed'];
    $na_syn = ['स्थापित नही है', 'स्थापित नहीं है', 'स्थापित नही', 'not_applicable'];
    if (in_array($v, $active_syn, true))
        return 'active';
    if (in_array($v, $non_syn, true))
        return 'non-active';
    if (in_array($v, $closed_syn, true))
        return 'closed';
    if (in_array($v, $na_syn, true))
        return 'not_applicable';
    $vl = mb_strtolower($v);
    if ($vl === 'active' || $vl === 'सक्रिय')
        return 'active';
    if ($vl === 'non-active' || $vl === 'non active' || $vl === 'निष्क्रिय')
        return 'non-active';
    if ($vl === 'closed' || $vl === 'परिसमापनाधीन')
        return 'closed';
    if ($vl === 'not_applicable' || $vl === 'na' || strpos($vl, 'स्थापित नहीं है') === 0)
        return 'not_applicable';
    return $v;
}
function status_label($v)
{
    $code = status_code($v);
    if ($code === 'active')
        return 'सक्रिय';
    if ($code === 'non-active')
        return 'निष्क्रिय';
    if ($code === 'closed')
        return 'परिसमापनाधीन';
    if ($code === 'not_applicable')
        return 'स्थापित नही है';
    return h($v);
}

/* initial form state */
$form = [
    'mandal_name' => '',
    'janpad_name' => '',
    'society_status' => '',
    'sachiv_type' => '',
    'sachiv_name' => '',
    'sachiv_no' => '',
    'sachiv_mail' => '',
    'society_chairamin_name' => '',
    'society_chairamin_no' => '',
    'society_name' => '',
    'ncd_id' => '',
    'liquidator_name' => '',
    'liquidator_no' => '',
    'liquidation_from_date' => '',
    'bhumi_area' => '',
    'arrived_road' => '',
    'land_status' => '',
    'land_type' => '',
    'godown_suitable' => '',
    'raik_distance_km' => '',
    'kabja_vivadit' => '',
    'is_kabja_vivadit' => '',
    'rajswa_abhilekh' => '',
    'latitude' => '',
    'longitude' => ''
];

$msg = '';
$msg_class = 'success';
$edit_id = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int) $_GET['edit'] : 0;
$is_edit = $edit_id > 0;

/* load for edit */
if ($is_edit) {
    $sql = "SELECT * FROM upss WHERE sno=? AND (is_deleted IS NULL OR is_deleted=0) LIMIT 1";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $edit_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            foreach ($form as $k => $_) {
                if (array_key_exists($k, $row))
                    $form[$k] = (string) $row[$k];
            }
            $form['society_status'] = status_code($form['society_status']);
        } else {
            $msg = "Record not found for edit #$edit_id";
            $msg_class = 'error';
            $is_edit = false;
            $edit_id = 0;
        }
        mysqli_stmt_close($stmt);
    } else {
        $msg = 'Edit load error: ' . h(mysqli_error($db));
        $msg_class = 'error';
    }
}

/* handle delete (soft) */
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $del_id = (int) $_GET['delete'];
    $cur_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    $sql = "UPDATE upss SET is_deleted=1, deleted_at=NOW(), deleted_by=? WHERE sno=? LIMIT 1";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, 'ii', $cur_user_id, $del_id);
        if (mysqli_stmt_execute($stmt)) {
            $msg = "Record #$del_id deleted.";
            $msg_class = 'success';
        } else {
            $msg = 'Delete error: ' . h(mysqli_error($db));
            $msg_class = 'error';
        }
        mysqli_stmt_close($stmt);
    } else {
        $msg = 'Delete prepare error: ' . h(mysqli_error($db));
        $msg_class = 'error';
    }
}

/* handle form submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // collect inputs
    foreach ($form as $k => $v) {
        $form[$k] = isset($_POST[$k]) ? trim((string) $_POST[$k]) : '';
    }
    // normalize
    $form['society_status'] = status_code($form['society_status']);

    $is_update = isset($_POST['update_sno']) && ctype_digit($_POST['update_sno']);
    $update_id = $is_update ? (int) $_POST['update_sno'] : 0;

    // validation
    if ($form['society_name'] === '') {
        $msg = 'कृपया समिति का नाम दर्ज करें।';
        $msg_class = 'error';
    } elseif ($form['society_status'] === 'closed' && ($form['liquidator_name'] === '' || $form['liquidation_from_date'] === '')) {
        $msg = 'परिसमापनाधीन के लिए परिसमापक का नाम और तारीख आवश्यक है.';
        $msg_class = 'error';
    } else {
        if ($is_update) {
            // UPDATE (Final + Working)
            $cur_user = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';
            $latitude = round((float) ($_POST['latitude']), 8);
            $longitude = round((float) ($_POST['longitude']), 8);
            $sqlu = "UPDATE upss SET
    mandal_name='" . e($db, $form['mandal_name']) . "',
    janpad_name='" . e($db, $form['janpad_name']) . "',
    society_status='" . e($db, $form['society_status']) . "',
    sachiv_type='" . e($db, $form['sachiv_type']) . "',
    sachiv_name='" . e($db, $form['sachiv_name']) . "',
    sachiv_no='" . e($db, $form['sachiv_no']) . "',
    sachiv_mail='" . e($db, $form['sachiv_mail']) . "',
    society_chairamin_name='" . e($db, $form['society_chairamin_name']) . "',
    society_chairamin_no='" . e($db, $form['society_chairamin_no']) . "',
    society_name='" . e($db, $form['society_name']) . "',
    ncd_id='" . e($db, $form['ncd_id']) . "',
    liquidator_name='" . e($db, $form['liquidator_name']) . "',
    liquidator_no='" . e($db, $form['liquidator_no']) . "',
    liquidation_from_date=" . ($form['liquidation_from_date'] ? ("'" . e($db, $form['liquidation_from_date']) . "'") : "NULL") . ",
    bhumi_area='" . e($db, $form['bhumi_area']) . "',
    arrived_road='" . e($db, $form['arrived_road']) . "',
    land_status='" . e($db, $form['land_status']) . "',
    land_type='" . e($db, $form['land_type']) . "',
    godown_suitable='" . e($db, $form['godown_suitable']) . "',
    raik_distance_km='" . e($db, $form['raik_distance_km']) . "',
    kabja_vivadit='" . e($db, $form['kabja_vivadit']) . "',
    is_kabja_vivadit='" . e($db, $form['is_kabja_vivadit']) . "',
    rajswa_abhilekh='" . e($db, $form['rajswa_abhilekh']) . "',
    latitude='" . e($db, $latitude) . "',
    longitude='" . e($db, $longitude) . "',
    updated_at=NOW(),
    updated_by=" . $cur_user . "
WHERE sno=" . (int) $update_id;

            if (mysqli_query($db, $sqlu)) {
                $msg = "Record #$update_id updated.";
                $msg_class = 'success';
            } else {
                $msg = 'Update error: ' . h(mysqli_error($db));
                $msg_class = 'error';
            }

            // Simpler update using escaped values (keeps code shorter & matches your app pattern)
            $cur_user = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';
            $sqlu = "UPDATE upss SET
        mandal_name='" . e($db, $form['mandal_name']) . "',
        janpad_name='" . e($db, $form['janpad_name']) . "',
        society_status='" . e($db, $form['society_status']) . "',
        sachiv_type='" . e($db, $form['sachiv_type']) . "',
        sachiv_name='" . e($db, $form['sachiv_name']) . "',
        sachiv_no='" . e($db, $form['sachiv_no']) . "',
        sachiv_mail='" . e($db, $form['sachiv_mail']) . "',
        society_chairamin_name='" . e($db, $form['society_chairamin_name']) . "',
        society_chairamin_no='" . e($db, $form['society_chairamin_no']) . "',
        society_name='" . e($db, $form['society_name']) . "',
        ncd_id='" . e($db, $form['ncd_id']) . "',
        liquidator_name='" . e($db, $form['liquidator_name']) . "',
        liquidator_no='" . e($db, $form['liquidator_no']) . "',
        liquidation_from_date=" . ($form['liquidation_from_date'] ? ("'" . e($db, $form['liquidation_from_date']) . "'") : "NULL") . ",
        bhumi_area='" . e($db, $form['bhumi_area']) . "',
        arrived_road='" . e($db, $form['arrived_road']) . "',
        land_status='" . e($db, $form['land_status']) . "',
        land_type='" . e($db, $form['land_type']) . "',
        godown_suitable='" . e($db, $form['godown_suitable']) . "',
        raik_distance_km='" . e($db, $form['raik_distance_km']) . "',
        kabja_vivadit='" . e($db, $form['kabja_vivadit']) . "',
        is_kabja_vivadit='" . e($db, $form['is_kabja_vivadit']) . "',
        rajswa_abhilekh='" . e($db, $form['rajswa_abhilekh']) . "',
        latitude='" . e($db, $latitude) . "',
        longitude='" . e($db, $longitude) . "',
        updated_at=NOW(),
        updated_by=" . $cur_user . "
      WHERE sno=" . (int) $update_id;
            if (mysqli_query($db, $sqlu)) {
                $msg = "Record #$update_id updated.";
                $msg_class = 'success';
            } else {
                $msg = 'Update error: ' . h(mysqli_error($db));
                $msg_class = 'error';
            }
        } else {
            // Insert
            $create_user = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';
            $sql = "INSERT INTO upss (mandal_name, janpad_name, society_status, sachiv_type, sachiv_name, sachiv_no, sachiv_mail, society_chairamin_name, society_chairamin_no, society_name, ncd_id, liquidator_name, liquidator_no, liquidation_from_date, bhumi_area, arrived_road, land_status, land_type, godown_suitable, raik_distance_km, kabja_vivadit, is_kabja_vivadit, rajswa_abhilekh, latitude, longitude, created_at, created_by) VALUES (
        '" . e($db, $form['mandal_name']) . "',
        '" . e($db, $form['janpad_name']) . "',
        '" . e($db, $form['society_status']) . "',
        '" . e($db, $form['sachiv_type']) . "',
        '" . e($db, $form['sachiv_name']) . "',
        '" . e($db, $form['sachiv_no']) . "',
        '" . e($db, $form['sachiv_mail']) . "',
        '" . e($db, $form['society_chairamin_name']) . "',
        '" . e($db, $form['society_chairamin_no']) . "',
        '" . e($db, $form['society_name']) . "',
        '" . e($db, $form['ncd_id']) . "',
        '" . e($db, $form['liquidator_name']) . "',
        '" . e($db, $form['liquidator_no']) . "',
        " . ($form['liquidation_from_date'] ? ("'" . e($db, $form['liquidation_from_date']) . "'") : "NULL") . ",
        '" . e($db, $form['bhumi_area']) . "',
        '" . e($db, $form['arrived_road']) . "',
        '" . e($db, $form['land_status']) . "',
        '" . e($db, $form['land_type']) . "',
        '" . e($db, $form['godown_suitable']) . "',
        '" . e($db, $form['raik_distance_km']) . "',
        '" . e($db, $form['kabja_vivadit']) . "',
        '" . e($db, $form['is_kabja_vivadit']) . "',
        '" . e($db, $form['rajswa_abhilekh']) . "',
        '" . e($db, $latitude) . "',
        '" . e($db, $longitude) . "',
        NOW(), " . $create_user . "
      )";
            if (mysqli_query($db, $sql)) {
                $msg = "✅ Inserted successfully!";
                $msg_class = 'success';
                // reset form
                foreach ($form as $k => $_)
                    $form[$k] = '';
            } else {
                $msg = 'Insert error: ' . h(mysqli_error($db));
                $msg_class = 'error';
            }
        }
    }
}
?>
<?php

$DIV_ID = $_SESSION['division_id'] ?? null;
$DIS_ID = $_SESSION['district_id'] ?? null;
$USER_TYPE = $_SESSION['usertype'] ?? null;

if (is_array($DIV_ID))
    $DIV_ID = $DIV_ID[0] ?? null;
if (is_array($DIS_ID))
    $DIS_ID = $DIS_ID[0] ?? null;

// if (!empty($DIV_ID) || !empty($DIS_ID)) {
if ($USER_TYPE == 2 || !empty($DIV_ID) || !empty($DIS_ID)) {

    ?>
    <div class="card" style="margin-top:30px">
        <h3 class="section-heading" style="text-align:center">उपभोक्ता संघ रिपोर्ट</h3>
        <div class="table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>क्रम स०</th>
                        <th>NCD ID</th>
                        <th>समिति अध्यक्ष</th>
                        <th>सचिव का प्रकार</th>
                        <th>क्या समिति सक्रिय है</th>
                        <th>भूमि क्षेत्रफल</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>पहुच मार्ग</th>
                        <th>भूमि स्थिति</th>
                        <th>स्थान</th>
                        <th>गोदाम उपयुक्त</th>
                        <th>रैक दूरी</th>
                        <th>कब्जा</th>
                        <th>परिसमापक का नाम</th>
                        <th>कब से परिसमापक</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT u.*, d.district_name FROM upss u LEFT JOIN master_district d ON u.janpad_name = d.sno WHERE (u.is_deleted IS NULL OR u.is_deleted=0)";

                    if ($USER_TYPE != 2) {
                        if (!empty($_SESSION['division_id'])) {
                            $div_ids = array_map('intval', (array) $_SESSION['division_id']);
                            if ($div_ids) {
                                $sql .= " AND u.mandal_name IN (" . implode(',', $div_ids) . ")";
                            }
                        }

                        if (!empty($_SESSION['district_id'])) {
                            $dis_ids = array_map('intval', (array) $_SESSION['district_id']);
                            if ($dis_ids) {
                                $sql .= " AND u.janpad_name IN (" . implode(',', $dis_ids) . ")";
                            }
                        }
                    }

                    $sql .= " ORDER BY d.district_name, u.sno DESC";

                    if ($res = mysqli_query($db, $sql)) {
                        $i = 1;
                        $last = null;
                        while ($row = mysqli_fetch_assoc($res)) {
                            $dist = $row['district_name'] ?? 'N/A';
                            if ($dist !== $last) {
                                echo '<tr><th colspan="16" style="background:#e9f3ff;color:#0a3d8f;text-align:left">जनपद: ' . h($dist) . '</th></tr>';
                                $last = $dist;
                            }
                            ?>
                            <tr>
                                <td style="display:flex;gap:6px;align-items:center">
                                    <a href="?edit=<?= (int) $row['sno'] ?>"
                                        style="background:#1565c0;color:#fff;padding:6px 8px;border-radius:6px;text-decoration:none;font-weight:700">✏️
                                        Edit</a>
                                    <a href="?delete=<?= (int) $row['sno'] ?>" onclick="return confirm('Are You Sure ?');"
                                        style="background:#b00020;color:#fff;padding:6px 8px;border-radius:6px;text-decoration:none;font-weight:700">🗑️
                                        Delete</a>
                                </td>
                                <td><?= $i++ ?></td>
                                <td><?= h($row['ncd_id']) ?></td>
                                <td><?= h($row['society_chairamin_name'] ?? $row['samiti_adhyaksh_name'] ?? '') ?></td>
                                <td><?= h($row['sachiv_type']) ?></td>
                                <td><?= h(status_label($row['society_status'] ?? '')) ?></td>
                                <td><?= h($row['bhumi_area']) ?></td>
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
                                <td><?= h($row['arrived_road']) ?></td>
                                <td><?= h($row['land_status']) ?></td>
                                <td><?= h($row['land_type']) ?></td>
                                <td><?= h($row['godown_suitable']) ?></td>
                                <td><?= h($row['raik_distance_km']) ?></td>
                                <td><?= h($row['kabja_vivadit']) ?></td>
                                <td><?= h($row['liquidator_name']) ?></td>
                                <td><?= h($row['liquidation_from_date']) ?></td>
                            </tr>
                            <?php
                        }
                        mysqli_free_result($res);
                    } else {
                        echo '<tr><td colspan="16">List load error: ' . h(mysqli_error($db)) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>

<style>
    .card {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 18px;
        border-radius: 8px;
        margin-bottom: 20px
    }

    .section-heading {
        background: #4a90e2;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        font-weight: 700
    }

    .form-control,
    .form-select,
    textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px 16px
    }

    @media (max-width:768px) {
        .form-grid {
            grid-template-columns: 1fr
        }
    }

    .btn-primary {
        background: #4a90e2;
        color: #fff;
        padding: 10px 18px;
        border-radius: 6px;
        border: none;
        cursor: pointer
    }

    .alert {
        margin: 10px 0;
        padding: 10px;
        border-radius: 8px
    }

    .alert.success {
        background: #e8f7ee;
        color: #11634a;
        border: 1px solid #b7ebc6
    }

    .alert.error {
        background: #fdecea;
        color: #8a1c22;
        border: 1px solid #f5c2c7
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px
    }

    .report-table th,
    .report-table td {
        border: 1px solid #e1e5ee;
        padding: 8px;
        font-size: 13px
    }

    .report-table th {
        background: #f1f5ff
    }

    #map_container {
        height: 280px
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

<?php if ($msg): ?>
    <div class="alert <?= h($msg_class) ?>"><?= h($msg) ?></div>
<?php endif; ?>

<!-- Form -->
<form method="post" action="">
    <?php if ($is_edit): ?><input type="hidden" name="update_sno" value="<?= (int) $edit_id ?>"><?php endif; ?>

    <div class="card">
        <h3 class="section-heading">📍 लोकेशन</h3>
        <div class="form-grid" style="grid-template-columns: 1fr 3fr;">
            <div>
                <label class="form-label">लोकेशन भरने का तरीका</label>
                <select id="geo_type" class="form-select" onchange="toggleGeoType()">
                    <option value="">-- चुनें --</option>
                    <option value="button">मोबाईल से (GPS)</option>
                    <option value="self">स्वयं से भरें</option>
                </select>
                <div style="margin-top:10px;">
                    <label>Latitude</label>
                    <input type="text" id="lat_show" class="form-control"
                        value="<?= h(round((float) ($form['latitude'] ?? ''), 8)) ?>" readonly>
                    <label style="margin-top:10px;">Longitude</label>
                    <input type="text" id="long_show" class="form-control"
                        value="<?= h(round((float) ($form['longitude'] ?? ''), 8)) ?>" readonly>
                </div>
                <div id="gps_section" style="display:none; margin-top:10px;">
                    <button type="button" class="btn btn-info" onclick="getLocation();">
                        📍 लोकेशन रिफ्रेश करें
                    </button>
                    <div class="blinking-text">(लोकेशन मोबाईल से भरे)*</div>
                </div>
                <input type="hidden" name="latitude" id="lat"
                    value="<?= h(round((float) ($form['latitude'] ?? ''), 8)) ?>">
                <input type="hidden" name="longitude" id="long"
                    value="<?= h(round((float) ($form['longitude'] ?? ''), 8)) ?>">
            </div>
            <div id="map_container" style="height:280px;">
                <iframe id="googlemap"
                    src="https://maps.google.com/maps?q=<?= h($form['latitude'] ?? '') . ',' . h($form['longitude'] ?? '') ?>&hl=hi&z=13&output=embed"
                    width="100%" height="100%" style="border:1px solid; border-radius:10px;" allowfullscreen
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="section-heading">📑 मूलभूत जानकारी</h3>
        <div class="form-grid">
            <?php if ($is_edit): ?>
                <div>
                    <label class="form-label">मण्डल</label>
                    <input type="text" class="form-control" value="<?= h($mandal_label ?? '') ?>" readonly>
                    <input type="hidden" name="mandal_name" value="<?= h($form['mandal_name']) ?>">
                </div>
                <div>
                    <label class="form-label">जनपद</label>
                    <input type="text" class="form-control" value="<?= h($janpad_label ?? '') ?>" readonly>
                    <input type="hidden" name="janpad_name" value="<?= h($form['janpad_name']) ?>">
                </div>
            <?php else: ?>
                <div>
                    <label class="form-label">मण्डल</label>
                    <select name="mandal_name" id="mandal_name" class="form-control" style="height:35px;">
                        <?php
                        if ($_SESSION['user_type'] == 'ar' && !empty($_SESSION['division_id'])) {
                            $ids = array_map('intval', (array) $_SESSION['division_id']);
                            $sql = 'SELECT * FROM master_division WHERE sno IN (' . implode(',', $ids) . ') ORDER BY division_name';
                        } else {
                            $sql = 'SELECT * FROM master_division ORDER BY division_name';
                        }
                        $resDiv = mysqli_query($db, $sql);
                        while ($r = mysqli_fetch_assoc($resDiv)) {
                            $sel = ((string) $form['mandal_name'] === (string) $r['sno']) ? 'selected' : '';
                            echo '<option value="' . h($r['sno']) . '" ' . $sel . '>' . h($r['division_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="form-label">जनपद</label>
                    <select name="janpad_name" id="janpad_name" class="form-control" style="height:35px;">
                        <?php
                        if ($_SESSION['user_type'] == 'ar' && !empty($_SESSION['district_id'])) {
                            $ids = array_map('intval', (array) $_SESSION['district_id']);
                            $sql = 'SELECT * FROM master_district WHERE sno IN (' . implode(',', $ids) . ') ORDER BY district_name';
                        } else {
                            $sql = 'SELECT * FROM master_district ORDER BY district_name';
                        }
                        $resDis = mysqli_query($db, $sql);
                        while ($r = mysqli_fetch_assoc($resDis)) {
                            $sel = ((string) $form['janpad_name'] === (string) $r['sno']) ? 'selected' : '';
                            echo '<option value="' . h($r['sno']) . '" ' . $sel . '>' . h($r['district_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <label class="form-label">क्या समिति सक्रिय है</label>
                <select name="society_status" class="form-select" onchange="onSocietyStatusChange(this)">
                    <option value="">-- चुनें --</option>
                    <option value="active" <?= ($form['society_status'] === 'active') ? 'selected' : '' ?>>सक्रिय</option>
                    <option value="non-active" <?= ($form['society_status'] === 'non-active') ? 'selected' : '' ?>>
                        निष्क्रिय
                    </option>
                    <option value="closed" <?= ($form['society_status'] === 'closed') ? 'selected' : '' ?>>परिसमापनाधीन
                    </option>
                    <option value="not_applicable" <?= ($form['society_status'] === 'not_applicable') ? 'selected' : '' ?>>
                        स्थापित नही है</option>
                </select>
            </div>
        </div>
    </div>

    <div id="samiti-details" class="card" style="display:none">
        <h3 class="section-heading">🏢 समिति विवरण</h3>
        <div class="form-grid">
            <div id="group-ncd-id" style="display:none">
                <label>NCD ID</label>
                <input type="text" name="ncd_id" class="form-control" value="<?= h($form['ncd_id']) ?>">
            </div>

            <div id="group-samiti-naam" style="display:none">
                <label>समिति का नाम</label>
                <input type="text" name="society_name" class="form-control" value="<?= h($form['society_name']) ?>">
            </div>

            <div id="group-active-1" style="display:none">
                <label>सचिव का प्रकार / ADO / ADCO</label>
                <input type="text" name="sachiv_type" class="form-control" value="<?= h($form['sachiv_type']) ?>">
            </div>

            <div id="group-active-2" style="display:none">
                <label>सचिव का नाम</label>
                <input type="text" name="sachiv_name" class="form-control" value="<?= h($form['sachiv_name']) ?>">
            </div>

            <div id="group-active-3" style="display:none">
                <label>सचिव का मो० न०</label>
                <input type="text" name="sachiv_no" class="form-control" value="<?= h($form['sachiv_no']) ?>">
            </div>

            <div id="group-active-4" style="display:none">
                <label>सचिव का मेल-आईडी</label>
                <input type="text" name="sachiv_mail" class="form-control" value="<?= h($form['sachiv_mail']) ?>">
            </div>

            <div id="group-active-5" style="display:none">
                <label>समिति के अध्यक्ष का नाम</label>
                <input type="text" name="society_chairamin_name" class="form-control"
                    value="<?= h($form['society_chairamin_name']) ?>">
            </div>

            <div id="group-active-6" style="display:none">
                <label>अध्यक्ष का मो० न०</label>
                <input type="text" name="society_chairamin_no" class="form-control"
                    value="<?= h($form['society_chairamin_no']) ?>">
            </div>
        </div>
    </div>

    <div id="society-liquidation" class="card" style="display:none">
        <div class="form-grid">
            <div id="group-liq-1" style="display:none">
                <label>परिसमापक का नाम</label>
                <input type="text" name="liquidator_name" class="form-control"
                    value="<?= h($form['liquidator_name']) ?>">
            </div>

            <div id="group-liq-2" style="display:none">
                <label>परिसमापक का मो० न०</label>
                <input type="text" name="liquidator_no" class="form-control" value="<?= h($form['liquidator_no']) ?>">
            </div>

            <div id="group-liq-3" style="display:none">
                <label>परिसमापक नियुक्त किये जाने की तारीख</label>
                <input type="date" name="liquidation_from_date" class="form-control"
                    value="<?= h($form['liquidation_from_date']) ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="section-heading">🏡 खाली भूमि का विवरण</h3>
        <div class="form-grid">
            <div>
                <label>भूमि का क्षेत्रफल (हेक्टेयर में)</label>
                <input type="text" name="bhumi_area" class="form-control" value="<?= h($form['bhumi_area']) ?>">
            </div>
            <div>
                <label>भूमि की स्थिति</label>
                <select name="land_status" class="form-select">
                    <option value="">--select--</option>
                    <option value="उपजाऊ" <?= ($form['land_status'] === 'उपजाऊ') ? 'selected' : '' ?>>उपजाऊ</option>
                    <option value="बंजर" <?= ($form['land_status'] === 'बंजर') ? 'selected' : '' ?>>बंजर</option>
                </select>
            </div>
            <div>
                <label>स्थान (समिति प्रांगण या अन्य)</label>
                <select name="land_type" class="form-select">
                    <option value="">--select--</option>
                    <option value="समिति प्रांगण" <?= ($form['land_type'] === 'समिति प्रांगण') ? 'selected' : '' ?>>समिति
                        प्रांगण</option>
                    <option value="अन्य स्थान" <?= ($form['land_type'] === 'अन्य स्थान') ? 'selected' : '' ?>>अन्य स्थान
                    </option>
                </select>
            </div>
            <div>
                <label>गोदाम के लिए उपयुक्त?</label>
                <select name="godown_suitable" class="form-select">
                    <option value="">--select--</option>
                    <option value="हाँ" <?= ($form['godown_suitable'] === 'हाँ') ? 'selected' : '' ?>>हाँ</option>
                    <option value="नहीं" <?= ($form['godown_suitable'] === 'नहीं') ? 'selected' : '' ?>>नहीं</option>
                </select>
            </div>
            <div>
                <label>जनपद के रैक प्वाइंट से दूरी (किमी)</label>
                <input type="text" name="raik_distance_km" class="form-control"
                    value="<?= h($form['raik_distance_km']) ?>">
            </div>
            <div>
                <label>पहुच मार्ग का प्रकार</label>
                <select name="arrived_road" class="form-select">
                    <option value="">--select--</option>
                    <option value="ordinary" <?= ($form['arrived_road'] === 'ordinary') ? 'selected' : '' ?>>कच्ची सड़क
                    </option>
                    <option value="nh" <?= ($form['arrived_road'] === 'nh') ? 'selected' : '' ?>>नेशनल हाईवे</option>
                    <option value="sh" <?= ($form['arrived_road'] === 'sh') ? 'selected' : '' ?>>स्टेट हाईवे</option>
                    <option value="mdr" <?= ($form['arrived_road'] === 'mdr') ? 'selected' : '' ?>>एम.डी.आर.</option>
                    <option value="odr" <?= ($form['arrived_road'] === 'odr') ? 'selected' : '' ?>>ओ.डी.आर.</option>
                    <option value="rural" <?= ($form['arrived_road'] === 'rural') ? 'selected' : '' ?>>ग्रामीण सड़क
                    </option>
                    <option value="other" <?= ($form['arrived_road'] === 'other') ? 'selected' : '' ?>>अन्य</option>
                </select>
            </div>
            <div>
                <label>कब्जा / विवादित</label>
                <select name="kabja_vivadit" class="form-select" onchange="onKabjaChange(this)">
                    <option value="">-- चुनें --</option>
                    <option value="हाँ" <?= ($form['kabja_vivadit'] === 'हाँ') ? 'selected' : '' ?>>हाँ</option>
                    <option value="नहीं" <?= ($form['kabja_vivadit'] === 'नहीं') ? 'selected' : '' ?>>नहीं</option>
                </select>
            </div>

            <div id="is_kabja_vivadit_is" style="display:none">
                <label>किये गए प्रयास दर्ज करें</label>
                <textarea name="is_kabja_vivadit" rows="2"
                    class="form-control"><?= h($form['is_kabja_vivadit']) ?></textarea>
            </div>

            <div>
                <label>राजस्व अभिलेखों में दर्ज स्थिति</label>
                <select name="rajswa_abhilekh" class="form-select">
                    <option value="">-- चुनें --</option>
                    <option value="हाँ" <?= ($form['rajswa_abhilekh'] === 'हाँ') ? 'selected' : '' ?>>हाँ</option>
                    <option value="नहीं" <?= ($form['rajswa_abhilekh'] === 'नहीं') ? 'selected' : '' ?>>नहीं</option>
                </select>
            </div>
        </div>
    </div>

    <div style="text-align:center;margin-top:14px">
        <button type="submit" class="btn-primary"><?= $is_edit ? 'Update' : 'Submit' ?></button>
    </div>
</form>

<script>
    /**
     * Normalize status (same logic as PHP)
     */
    function normalizeStatusJS(s) {
        if (!s) return '';
        s = String(s).trim();
        if (s === 'active' || s === 'सक्रिय' || s === 'सक्रिया') return 'active';
        if (s === 'non-active' || s === 'निष्क्रिय') return 'non-active';
        if (s === 'closed' || s === 'परिसमापनाधीन' || s === 'परिसमापन') return 'closed';
        if (s === 'not_applicable' || s === 'स्थापित नही है' || s === 'स्थापित नहीं है' || s === 'na') return 'not_applicable';
        return s;
    }

    /**
     * Show/hide samiti & liquidation groups based on society status
     */
    function showHideBySocietyStatus(statusRaw) {
        var status = normalizeStatusJS(statusRaw);
        var samitiCard = document.getElementById('samiti-details');
        var liqCard = document.getElementById('society-liquidation');

        // hide container DIVs
        if (samitiCard) samitiCard.style.display = 'none';
        if (liqCard) liqCard.style.display = 'none';

        // hide immediate child groups (safe generic hide)
        document.querySelectorAll('#samiti-details .form-grid > div').forEach(function (d) {
            d.style.display = 'none';
        });
        document.querySelectorAll('#society-liquidation .form-grid > div').forEach(function (d) {
            d.style.display = 'none';
        });

        // not applicable -> nothing to show
        if (status === 'not_applicable') return;

        // Active / Non-active -> show samiti card + active groups
        if (status === 'active' || status === 'non-active') {
            if (samitiCard) samitiCard.style.display = 'block';
            [
                'group-ncd-id', 'group-samiti-naam',
                'group-active-1', 'group-active-2', 'group-active-3',
                'group-active-4', 'group-active-5', 'group-active-6'
            ].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) el.style.display = 'block';
            });
        }

        // Closed -> show samiti card + liquidation groups (do NOT show active-only groups)
        if (status === 'closed') {
            if (samitiCard) samitiCard.style.display = 'block';
            if (liqCard) liqCard.style.display = 'block';
            ['group-ncd-id', 'group-samiti-naam', 'group-liq-1', 'group-liq-2', 'group-liq-3'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) el.style.display = 'block';
            });
        }
    }

    /**
     * Called from the <select onchange> for कब्जा/विवादित
     * Shows the "किये गए प्रयास दर्ज करें" textarea when value === 'हाँ'
     */
    function onKabjaChange(sel) {
        var v = (sel && sel.value) ? String(sel.value).trim() : '';
        var target = document.getElementById('is_kabja_vivadit_is');
        if (!target) return;
        if (v === 'हाँ' || v === 'हाँ ') {
            target.style.display = 'block';
        } else {
            target.style.display = 'none';
        }
    }

    /**
     * On DOM ready: wire up initial visibility and event handlers
     */
    document.addEventListener('DOMContentLoaded', function () {
        // initial society status display
        var sel = document.querySelector('select[name="society_status"]');
        if (sel) {
            showHideBySocietyStatus(sel.value || (sel.options[sel.selectedIndex] && sel.options[sel.selectedIndex].value));
            // ensure onchange also works if user changes it
            sel.addEventListener('change', function () { showHideBySocietyStatus(this.value); });
        }

        // kabja field
        var kabja = document.querySelector('select[name="kabja_vivadit"]');
        if (kabja) {
            // run once to set initial state
            onKabjaChange(kabja);
            // attach listener
            kabja.addEventListener('change', function () { onKabjaChange(this); });
        }

        // optional: allow clicking the map area to refresh geolocation if desired
        var mapContainer = document.getElementById('map_container');
        if (mapContainer) {
            mapContainer.addEventListener('click', function (e) {
                // if user clicked the map area, attempt to re-fetch location
                // (already have a dedicated button; this is just convenience)
                // Avoid accidental clicks on iframe — only clicks on container background trigger
                if (e.target && e.target.id !== 'googlemap') getLocation();
            });
        }
    });

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

    function toggleGeoType() {
        var type = document.getElementById("geo_type").value;

        if (type === "button") {
            document.getElementById("gps_section").style.display = "block";
            document.getElementById("lat_show").setAttribute("readonly", true);
            document.getElementById("long_show").setAttribute("readonly", true);
        }
        else if (type === "self") {
            document.getElementById("gps_section").style.display = "none";
            document.getElementById("lat_show").removeAttribute("readonly");
            document.getElementById("long_show").removeAttribute("readonly");
        }
        else {
            document.getElementById("gps_section").style.display = "none";
            document.getElementById("lat_show").setAttribute("readonly", true);
            document.getElementById("long_show").setAttribute("readonly", true);
        }
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError, { enableHighAccuracy: true });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var long = position.coords.longitude;

        document.getElementById('lat_show').value = lat;
        document.getElementById('long_show').value = long;

        document.getElementById('lat').value = lat;
        document.getElementById('long').value = long;

        updateMap(lat, long);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var latInput = document.getElementById("lat_show");
        var longInput = document.getElementById("long_show");

        if (latInput) latInput.addEventListener("input", manualUpdate);
        if (longInput) longInput.addEventListener("input", manualUpdate);
    });

    function manualUpdate() {
        var type = document.getElementById("geo_type").value;
        if (type !== "self") return;

        var lat = document.getElementById("lat_show").value.trim();
        var long = document.getElementById("long_show").value.trim();

        if (lat !== "" && long !== "") {
            document.getElementById('lat').value = lat;
            document.getElementById('long').value = long;
            updateMap(lat, long);
        }
    }

    function updateMap(lat, long) {
        document.getElementById('googlemap').src =
            "https://maps.google.com/maps?q=" + lat + "," + long +
            "&hl=hi&z=13&output=embed";
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.");
                break;
        }
    }

    window.onload = function () {
        var lat = document.getElementById('lat').value;
        var long = document.getElementById('long').value;

        if (lat && long) {
            document.getElementById('lat_show').value = lat;
            document.getElementById('long_show').value = long;
            document.getElementById('lat_show').setAttribute("readonly", true);
            document.getElementById('long_show').setAttribute("readonly", true);
        }
    };
</script>

<?php
page_footer_start();
page_footer_end();
?>