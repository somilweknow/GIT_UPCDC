<?php
include("scripts/settings.php");
page_header_start();
page_header_end();
// error_reporting(E_ALL);
page_sidebar();

/** helpers */
function h($v)
{
  return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}
function e($db, $v)
{
  return mysqli_real_escape_string($db, trim((string) ($v ?? '')));
}

/**
 * Normalize various possible stored values into canonical codes:
 * returns 'active' | 'non-active' | 'closed' | ''
 */
function status_code($v)
{
  $v = trim((string) $v);
  if ($v === '')
    return '';
  // match known English codes first
  $lc = $v;
  // Hindi synonyms
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
  // fallback: try case-insensitive english match
  $vl = mb_strtolower($v);
  if ($vl === 'active' || $vl === 'सक्रिय')
    return 'active';
  if ($vl === 'non-active' || $vl === 'non active' || $vl === 'निष्क्रिय')
    return 'non-active';
  if ($vl === 'closed' || $vl === 'परिसमापनाधीन')
    return 'closed';
  if ($vl === 'not_applicable' || $vl === 'na' || strpos($vl, 'स्थापित नहीं है') === 0)
    return 'not_applicable';


  return $v; // unknown, return as-is
}

/** Display label for status (Hindi friendly) */
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


if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
  $del_id = (int) $_GET['delete'];
  $cur_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';

  $del_sql = "UPDATE jila_sehkari SET is_deleted = 1, deleted_at = NOW(), deleted_by = " . $cur_user_id . " WHERE sno = " . $del_id . " LIMIT 1";

  if (mysqli_query($db, $del_sql)) {
  } else {
    $msg = 'Delete error: ' . h(mysqli_error($db));
    $msg_class = 'error';
  }
}

$msg = '';
$msg_class = 'success';

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
  'business_type' => '',
  'business_status' => '',
  'business_status_amt' => '',
  'balance_sheet_year' => '',
  'last_audit_date' => '',
  'liquidation_type' => '',
  'samiti_sthiti' => '',
  'any_sampatti_details' => '',
  'latitude' => '',
  'longitude' => ''
];

$edit_id = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$is_edit = $edit_id > 0;

if ($is_edit) {
  $sql = "SELECT j.*, mdv.division_name AS mandal_label, md.district_name AS janpad_label FROM jila_sehkari j LEFT JOIN master_division mdv ON mdv.sno=j.mandal_name LEFT JOIN master_district md ON md.sno=j.janpad_name WHERE j.sno=" . $edit_id . " AND (j.is_deleted IS NULL OR j.is_deleted = 0) LIMIT 1";
  if ($res = mysqli_query($db, $sql)) {
    if ($row = mysqli_fetch_assoc($res)) {
      foreach ($form as $k => $_) {
        if (array_key_exists($k, $row))
          $form[$k] = (string) $row[$k];
      }
      // normalize society_status so UI and logic use canonical codes
      $form['society_status'] = status_code($form['society_status']);
      $mandal_label = (string) ($row['mandal_label'] ?? '');
      $janpad_label = (string) ($row['janpad_label'] ?? '');
    } else {
      $msg = "Record not found for edit #" . $edit_id;
      $msg_class = 'error';
    }
    mysqli_free_result($res);
  } else {
    $msg = 'Edit load error: ' . h(mysqli_error($db));
    $msg_class = 'error';
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // populate form from POST
  foreach ($form as $k => $v) {
    $form[$k] = isset($_POST[$k]) ? trim((string) $_POST[$k]) : '';
  }
  if (isset($_POST['last_audit_date'])) {
    unset($_POST['last_audit_date']);
  }
  $form['last_audit_date'] = '';

  // Normalize society_status in case legacy Hindi value was submitted
  $form['society_status'] = status_code($form['society_status']);

  $is_update = isset($_POST['update_sno']) && ctype_digit($_POST['update_sno']);
  $update_id = $is_update ? (int) $_POST['update_sno'] : 0;

  // if not closed, clear liquidation-only fields
  if ($form['society_status'] !== 'closed') {
    $form['liquidator_name'] = '';
    $form['liquidation_from_date'] = '';
  }

  // last_audit_date must be YYYY-MM-DD or cleared
  if ($form['last_audit_date'] && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $form['last_audit_date'])) {
    $form['last_audit_date'] = '';
  }

  // validation for closed
  // if (
  //   $form['society_status'] === 'closed' &&
  //   ($form['society_name'] === '' || $form['liquidator_name'] === '' || $form['liquidation_from_date'] === '')
  // ) {
  //   $msg = 'परिसमापनाधीन के लिए समिति का नाम, परिसमापक का नाम और तारीख आवश्यक है.';
  //   $msg_class = 'error';
  // } else {

  if ($form['society_name'] === '') {
    $msg = 'कृपया समिति का नाम दर्ज करें।';
    $msg_class = 'error';
  } elseif (
    $form['society_status'] === 'closed' &&
    ($form['liquidator_name'] === '' || $form['liquidation_from_date'] === '')
  ) {
    $msg = 'परिसमापनाधीन के लिए परिसमापक का नाम और तारीख आवश्यक है.';
    $msg_class = 'error';
  } else {
    if ($is_update) {
      $cur_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';
      $sql = "UPDATE jila_sehkari SET
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
        liquidation_from_date=" . ($form['liquidation_from_date'] ? "'" . e($db, $form['liquidation_from_date']) . "'" : "NULL") . ",
        bhumi_area='" . e($db, $form['bhumi_area']) . "',
        arrived_road='" . e($db, $form['arrived_road']) . "',
        land_status='" . e($db, $form['land_status']) . "',
        land_type='" . e($db, $form['land_type']) . "',
        godown_suitable='" . e($db, $form['godown_suitable']) . "',
        raik_distance_km='" . e($db, $form['raik_distance_km']) . "',
        kabja_vivadit='" . e($db, $form['kabja_vivadit']) . "',
        is_kabja_vivadit='" . e($db, $form['is_kabja_vivadit']) . "',
        rajswa_abhilekh='" . e($db, $form['rajswa_abhilekh']) . "',
        business_type='" . e($db, $form['business_type']) . "',
        business_status='" . e($db, $form['business_status']) . "',
        business_status_amt='" . e($db, $form['business_status_amt']) . "',
        balance_sheet_year='" . e($db, $form['balance_sheet_year']) . "',
        liquidation_type='" . e($db, $form['liquidation_type']) . "',
        samiti_sthiti='" . e($db, $form['samiti_sthiti']) . "',
        any_sampatti_details='" . e($db, $form['any_sampatti_details']) . "',
        latitude='" . e($db, $form['latitude']) . "',
        longitude='" . e($db, $form['longitude']) . "',
        updated_at = NOW(),
        updated_by = " . $cur_user_id . "
      WHERE sno=" . $update_id;

      if (mysqli_query($db, $sql)) {
        $msg = "Record #" . $update_id . " updated successfully.";
        $msg_class = 'success';
      } else {
        $msg = 'Update error: ' . h(mysqli_error($db));
        $msg_class = 'error';
      }
    } else {
      // set created_by from session user_id if present, else NULL
      $create_user = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';

      $sql = "INSERT INTO jila_sehkari
    (mandal_name,janpad_name,society_status,sachiv_type,sachiv_name,sachiv_no,sachiv_mail,society_chairamin_name,society_chairamin_no,
    society_name,ncd_id,liquidator_name,liquidator_no,liquidation_from_date,
    bhumi_area,arrived_road,land_status,land_type,godown_suitable,
    raik_distance_km,kabja_vivadit,is_kabja_vivadit,rajswa_abhilekh,business_type,business_status,business_status_amt,
    balance_sheet_year,liquidation_type,samiti_sthiti,any_sampatti_details,latitude,longitude,
    created_at, created_by)
    VALUES(
    '" . e($db, $form['mandal_name']) . "','" . e($db, $form['janpad_name']) . "','" . e($db, $form['society_status']) . "',
    '" . e($db, $form['sachiv_type']) . "','" . e($db, $form['sachiv_name']) . "','" . e($db, $form['sachiv_no']) . "','" . e($db, $form['sachiv_mail']) . "','" . e($db, $form['society_chairamin_name']) . "','" . e($db, $form['society_chairamin_no']) . "',
    '" . e($db, $form['society_name']) . "','" . e($db, $form['ncd_id']) . "','" . e($db, $form['liquidator_name']) . "','" . e($db, $form['liquidator_no']) . "'," . ($form['liquidation_from_date'] ? "'" . e($db, $form['liquidation_from_date']) . "'" : "NULL") . ",
    '" . e($db, $form['bhumi_area']) . "','" . e($db, $form['arrived_road']) . "','" . e($db, $form['land_status']) . "',
    '" . e($db, $form['land_type']) . "','" . e($db, $form['godown_suitable']) . "',
    '" . e($db, $form['raik_distance_km']) . "','" . e($db, $form['kabja_vivadit']) . "','" . e($db, $form['is_kabja_vivadit']) . "','" . e($db, $form['rajswa_abhilekh']) . "',
    '" . e($db, $form['business_type']) . "','" . e($db, $form['business_status']) . "','" . e($db, $form['business_status_amt']) . "',
    '" . e($db, $form['balance_sheet_year']) . "',
    '" . e($db, $form['liquidation_type']) . "','" . e($db, $form['samiti_sthiti']) . "','" . e($db, $form['any_sampatti_details']) . "',
    '" . e($db, $form['latitude']) . "','" . e($db, $form['longitude']) . "',
    NOW(), " . $create_user . "
    )";

      if (mysqli_query($db, $sql)) {
        $msg = '✅ Inserted successfully!';
        $msg_class = 'success';
        $form = array_map(fn($x) => '', $form);
      } else {
        $msg = 'Insert error: ' . h(mysqli_error($db));
        $msg_class = 'error';
      }
    }
  }
}
?>

<style>
  /* same CSS as before (kept concise) */
  .card {
    background: #f9f9f9;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .08)
  }

  .section-heading {
    background: #4a90e2;
    color: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-weight: 700;
    font-size: 1.1em
  }

  .form-label {
    font-weight: 700;
    margin-bottom: 5px
  }

  .form-control,
  .form-select,
  textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: .95em
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px 20px
  }

  .btn-primary {
    background: #4a90e2;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer
  }

  .btn-primary:hover {
    background: #357ab8
  }

  @media (max-width:768px) {
    .form-grid {
      grid-template-columns: 1fr
    }
  }

  .alert {
    margin: 12px 0;
    padding: 10px 12px;
    border-radius: 8px;
    font-weight: 700
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

  .table-wrap {
    overflow: auto;
    margin-top: 18px
  }

  .report-table {
    width: 100%;
    border-collapse: collapse
  }

  .report-table th,
  .report-table td {
    border: 1px solid #e1e5ee;
    padding: 8px 10px;
    font-size: 13px;
    white-space: nowrap
  }

  .report-table th {
    background: #f1f5ff;
    text-align: left
  }

  #map_container {
    height: 280px
  }

  .blink {
    animation: blinker 5.0s linear infinite;
  }

  @keyframes blinker {
    50% {
      opacity: 0;
    }
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
<?php

$DIV_ID = $_SESSION['division_id'] ?? null;
$DIS_ID = $_SESSION['district_id'] ?? null;

if (is_array($DIV_ID))
  $DIV_ID = $DIV_ID[0] ?? null;
if (is_array($DIS_ID))
  $DIS_ID = $DIS_ID[0] ?? null;

if (!empty($DIV_ID) || !empty($DIS_ID)) {

  ?>
  <!-- Report (kept original layout) -->
  <div class="card" style="margin-top: 40px;">
    <h3 class="section-heading" style="text-align: center;"> रिपोर्ट</h3>
    <h3 class="blink" style="font-size: 18px; color: red;">
      <b>नोट: समस्त पहले से भरी हुयी समितियों को एक बार अवश्य एडिट कर सेव कर दे। ताकि रिपोर्ट में सही स्थिति प्रदर्शित हो
        सके।</b>
    </h3>

    <div class="table-wrap">
      <table class="report-table">
        <thead>
          <tr>
            <th>Action</th>
            <th>क्रम</th>
            <th>NCD ID</th>
            <th>समिति अध्यक्ष</th>
            <th>सचिव प्रकार</th>
            <th>समिति सक्रिय?</th>
            <th>भूमि क्षेत्रफल</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>

          <?php
          $sql = "SELECT j.*, dt.district_name 
        FROM jila_sehkari j 
        LEFT JOIN master_district dt ON j.janpad_name = dt.sno 
        WHERE (j.is_deleted IS NULL OR j.is_deleted = 0)";

          if (!empty($_SESSION['division_id'])) {
            $div_ids = array_map('intval', (array) $_SESSION['division_id']);
            if ($div_ids)
              $sql .= " AND j.mandal_name IN (" . implode(',', $div_ids) . ")";
          }

          if (!empty($_SESSION['district_id'])) {
            $dis_ids = array_map('intval', (array) $_SESSION['district_id']);
            if ($dis_ids)
              $sql .= " AND j.janpad_name IN (" . implode(',', $dis_ids) . ")";
          }

          $sql .= " ORDER BY dt.district_name, j.sno DESC";

          if ($res = mysqli_query($db, $sql)) {

            $i = 1;
            $last = null;

            while ($row = mysqli_fetch_assoc($res)) {

              $dist = $row['district_name'] ?? 'N/A';

              if ($dist !== $last) {
                echo '<tr>
              <th colspan="8" style="background:#e9f3ff;color:#0a3d8f;font-weight:700;">
                जनपद: ' . h($dist) . '
              </th>
            </tr>';
                $last = $dist;
              }
              ?>

              <tr>
                <td style="display:flex;gap:8px;">
                  <a href="?edit=<?= (int) $row['sno'] ?>"
                    style="background:#1565c0;color:#fff;padding:5px 6px;border-radius:4px;text-decoration:none;font-size:12px;">✏️
                    Edit</a>

                  <?php if (empty($row['is_deleted']) || $row['is_deleted'] == 0) { ?>
                    <a href="?delete=<?= (int) $row['sno'] ?>" onclick="return confirm('Are You Sure ?');"
                      style="background:#b00020;color:#fff;padding:5px 6px;border-radius:4px;text-decoration:none;font-size:12px;">🗑️
                      Delete</a>
                  <?php } ?>
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
                      <td>परिसमापक का नाम</td>
                      <td><?= h($row['liquidator_name']) ?></td>
                    </tr>
                    <tr>
                      <td>कब से परिसमापक</td>
                      <td><?= h($row['liquidation_from_date']) ?></td>
                    </tr>
                    <tr>
                      <td>पहुच मार्ग</td>
                      <td><?= h($row['arrived_road'] ?? $row['pahuch_marg_prakar'] ?? '') ?></td>
                    </tr>
                    <tr>
                      <td>भूमि स्थिति</td>
                      <td><?= h($row['land_status']) ?></td>
                    </tr>
                    <tr>
                      <td>स्थान</td>
                      <td><?= h($row['land_type']) ?></td>
                    </tr>
                    <tr>
                      <td>गोदाम उपयुक्त</td>
                      <td><?= h($row['godown_suitable']) ?></td>
                    </tr>
                    <tr>
                      <td>रेल दूरी (किमी)</td>
                      <td><?= h($row['raik_distance_km']) ?></td>
                    </tr>
                    <tr>
                      <td>कब्जा/विवादित</td>
                      <td><?= h($row['kabja_vivadit']) ?></td>
                    </tr>
                    <tr>
                      <td>व्यवसाय प्रकार</td>
                      <td><?= h($row['business_type']) ?></td>
                    </tr>
                    <tr>
                      <td>व्यवसाय ₹ (लाख)</td>
                      <td><?= h($row['business_status_amt'] ?? '') ?></td>
                    </tr>
                    <tr>
                      <td>संतुलन पत्र</td>
                      <td><?= h($row['balance_sheet_year'] ?? '') ?></td>
                    </tr>
                    <tr>
                      <td>अन्तिम आडिट</td>
                      <td><?= h($row['last_audit_date']) ?></td>
                    </tr>
                    <tr>
                      <td>परिसम्पत्ति</td>
                      <td><?= h($row['liquidation_type'] ?? '') ?></td>
                    </tr>
                    <tr>
                      <td>समिति की स्थिति</td>
                      <td><?= h($row['samiti_sthiti']) ?></td>
                    </tr>
                    <tr>
                      <td>अन्य सम्पत्ति</td>
                      <td><?= h($row['any_sampatti_details']) ?></td>
                    </tr>

                  </table>

                </td>
              </tr>

              <?php
            }
            mysqli_free_result($res);
          }
          ?>

        </tbody>
      </table>
    </div>
  </div>
<?php } ?>

<?php if ($msg): ?>
  <div class="alert <?= h($msg_class) ?>"><?= h($msg) ?></div>
<?php endif; ?>

<form method="post" action="">
  <?php if ($is_edit): ?><input type="hidden" name="update_sno" value="<?= (int) $edit_id ?>"><?php endif; ?>

  <h2
    style="text-align:center;font-size:28px;color:#357ab8;font-weight:600;padding:10px;border-radius:5px;margin-bottom:20px;">
    जिला सहकारी संघों से संबंधित सूचना
  </h2>

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
            value="<?= h(round((float) ($form['latitude'] ?? 0), 8)) ?>" readonly>
          <label style="margin-top:10px;">Longitude</label>
          <input type="text" id="long_show" class="form-control"
            value="<?= h(round((float) ($form['longitude'] ?? 0), 8)) ?>" readonly>
        </div>
        <div id="gps_section" style="display:none; margin-top:10px;">
          <button type="button" class="btn btn-info" onclick="getLocation();">
            📍 लोकेशन रिफ्रेश करें
          </button>
          <div class="blinking-text">(लोकेशन मोबाईल से भरे)*</div>
        </div>
        <input type="hidden" name="latitude" id="lat" value="<?= h(round((float) ($form['latitude']), 8)) ?>">
        <input type="hidden" name="longitude" id="long" value="<?= h(round((float) ($form['longitude']), 8)) ?>">
      </div>
      <div id="map_container" style="height:280px;">
        <iframe id="googlemap"
          src="https://maps.google.com/maps?q=<?= h($form['latitude'] ?? '') . ',' . h($form['longitude'] ?? '') ?>&hl=hi&z=13&output=embed"
          width="100%" height="100%" style="border:1px solid; border-radius:10px;" allowfullscreen loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
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
          <option value="">--चुनें--</option>
          <option value="active" <?= ($form['society_status'] === 'active') ? 'selected' : '' ?>>सक्रिय</option>
          <option value="non-active" <?= ($form['society_status'] === 'non-active') ? 'selected' : '' ?>>निष्क्रिय</option>
          <option value="closed" <?= ($form['society_status'] === 'closed') ? 'selected' : '' ?>>परिसमापनाधीन</option>
          <option value="not_applicable" <?= ($form['society_status'] === 'not_applicable') ? 'selected' : '' ?>>स्थापित
            नही है</option>

        </select>
      </div>
    </div>
  </div>
  <div id="samiti-details" class="card" style="display:none;">
    <h3 class="section-heading">🏢 समिति विवरण</h3>
    <div class="form-grid">
      <div id="group-ncd-id" style="display:none;">
        <label class="form-label">NCD ID</label>
        <input type="text" name="ncd_id" class="form-control" value="<?= h($form['ncd_id']) ?>"
          placeholder="NCD ID दर्ज करें">
      </div>

      <div id="group-samiti-naam" style="display:none;">
        <label class="form-label">समिति का नाम</label>
        <!-- corrected name to society_name -->
        <input type="text" name="society_name" class="form-control" value="<?= h($form['society_name']) ?>">
      </div>

      <div id="group-active-1" style="display:none;">
        <label class="form-label">सचिव का प्रकार / ADO / ADCO</label>
        <input type="text" name="sachiv_type" class="form-control" value="<?= h($form['sachiv_type']) ?>"
          placeholder="सचिव का प्रकार दर्ज करें">
      </div>

      <div id="group-active-2" style="display:none;">
        <label class="form-label">सचिव का नाम</label>
        <input type="text" name="sachiv_name" class="form-control" value="<?= h($form['sachiv_name']) ?>">
      </div>

      <div id="group-active-3" style="display:none;">
        <label class="form-label">सचिव का मो० न०</label>
        <input type="text" name="sachiv_no" class="form-control" value="<?= h($form['sachiv_no']) ?>">
      </div>

      <div id="group-active-4" style="display:none;">
        <label class="form-label">सचिव का मेल-आईडी</label>
        <input type="text" name="sachiv_mail" class="form-control" value="<?= h($form['sachiv_mail']) ?>">
      </div>

      <div id="group-active-5" style="display:none;">
        <label class="form-label">समिति के अध्यक्ष का नाम</label>
        <input type="text" name="society_chairamin_name" class="form-control"
          value="<?= h($form['society_chairamin_name']) ?>">
      </div>

      <div id="group-active-6" style="display:none;">
        <label class="form-label">अध्यक्ष का मो० न०</label>
        <input type="text" name="society_chairamin_no" class="form-control"
          value="<?= h($form['society_chairamin_no']) ?>">
      </div>
    </div>
  </div>

  <div id="society-liquidation" class="card" style="display:none;">
    <div class="form-grid">
      <div id="group-liq-1" style="display:none;">
        <label class="form-label">परिसमापक का नाम</label>
        <input type="text" name="liquidator_name" class="form-control" value="<?= h($form['liquidator_name']) ?>">
      </div>

      <div id="group-liq-2" style="display:none;">
        <label class="form-label">परिसमापक का मो० न०</label>
        <input type="text" name="liquidator_no" class="form-control" value="<?= h($form['liquidator_no']) ?>">
      </div>

      <div id="group-liq-3" style="display:none;">
        <label class="form-label">परिसमापक नियुक्त किये जाने की स्थिति</label>
        <input type="date" name="liquidation_from_date" class="form-control"
          value="<?= h($form['liquidation_from_date']) ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <h3 class="section-heading">🏡 खाली भूमि का विवरण</h3>
    <div class="form-grid">
      <div><label class="form-label">भूमि का क्षेत्रफल (हेक्टेयर में)</label>
        <input type="text" name="bhumi_area" class="form-control" value="<?= h($form['bhumi_area']) ?>">
      </div>

      <div><label class="form-label">भूमि की स्थिति</label>
        <select name="land_status" class="form-select">
          <option value="">--select--</option>
          <option value="उपजाऊ" <?= ($form['land_status'] === 'उपजाऊ') ? 'selected' : ''; ?>>उपजाऊ</option>
          <option value="बंजर" <?= ($form['land_status'] === 'बंजर') ? 'selected' : ''; ?>>बंजर</option>
        </select>
      </div>

      <div><label class="form-label">स्थान (समिति प्रांगण या अन्य)</label>
        <select name="land_type" class="form-select">
          <option value="">--select--</option>
          <option value="समिति प्रांगण" <?= ($form['land_type'] === 'समिति प्रांगण') ? 'selected' : ''; ?>>समिति प्रांगण
          </option>
          <option value="अन्य स्थान" <?= ($form['land_type'] === 'अन्य स्थान') ? 'selected' : ''; ?>>अन्य स्थान</option>
        </select>
      </div>

      <div><label class="form-label">गोदाम के लिए उपयुक्त?</label>
        <select name="godown_suitable" class="form-select">
          <option value="">--select--</option>
          <option value="हाँ" <?= ($form['godown_suitable'] === 'हाँ') ? 'selected' : ''; ?>>हाँ</option>
          <option value="नहीं" <?= ($form['godown_suitable'] === 'नहीं') ? 'selected' : ''; ?>>नहीं</option>
        </select>
      </div>

      <div><label class="form-label">जनपद के रैक प्वाइंट से दूरी (किमी)</label>
        <input type="text" name="raik_distance_km" class="form-control" value="<?= h($form['raik_distance_km']) ?>">
      </div>

      <div><label class="form-label">पहुच मार्ग का प्रकार</label>
        <select name="arrived_road" class="form-select">
          <option value="">--select--</option>
          <option value="ordinary" <?= ($form['arrived_road'] === 'ordinary') ? 'selected' : ''; ?>>कच्ची सड़क</option>
          <option value="nh" <?= ($form['arrived_road'] === 'nh') ? 'selected' : ''; ?>>नेशनल हाईवे</option>
          <option value="sh" <?= ($form['arrived_road'] === 'sh') ? 'selected' : ''; ?>>स्टेट हाईवे</option>
          <option value="mdr" <?= ($form['arrived_road'] === 'mdr') ? 'selected' : ''; ?>>एम.डी.आर.</option>
          <option value="odr" <?= ($form['arrived_road'] === 'odr') ? 'selected' : ''; ?>>ओ.डी.आर.</option>
          <option value="rural" <?= ($form['arrived_road'] === 'rural') ? 'selected' : ''; ?>>ग्रामीण सड़क</option>
          <option value="other" <?= ($form['arrived_road'] === 'other') ? 'selected' : ''; ?>>अन्य</option>
        </select>
      </div>

      <div><label class="form-label">कब्जा / विवादित</label>
        <select name="kabja_vivadit" class="form-select" onchange="onKabjaChange(this)">
          <option value="">-- चुनें --</option>
          <option value="हाँ" <?= ($form['kabja_vivadit'] === 'हाँ') ? 'selected' : ''; ?>>हाँ</option>
          <option value="नहीं" <?= ($form['kabja_vivadit'] === 'नहीं') ? 'selected' : ''; ?>>नहीं</option>
        </select>
      </div>

      <div id="is_kabja_vivadit_is" style="display: none;">
        <label class="form-label">किये गए प्रयास दर्ज करें</label>
        <textarea name="is_kabja_vivadit" class="form-control" rows="2"><?= h($form['is_kabja_vivadit']) ?></textarea>
      </div>

      <div><label class="form-label">राजस्व अभिलेखों में दर्ज स्थिति</label>
        <select name="rajswa_abhilekh" class="form-select">
          <option value="">-- चुनें --</option>
          <option value="हाँ" <?= ($form['rajswa_abhilekh'] === 'हाँ') ? 'selected' : ''; ?>>हाँ</option>
          <option value="नहीं" <?= ($form['rajswa_abhilekh'] === 'नहीं') ? 'selected' : ''; ?>>नहीं</option>
        </select>
      </div>
    </div>
  </div>

  <div class="card">
    <h3 class="section-heading">📊 व्यवसाय</h3>
    <div class="form-grid">
      <div><label class="form-label">व्यवसाय का प्रकार</label>
        <input type="text" name="business_type" class="form-control" value="<?= h($form['business_type']) ?>">
      </div>

      <div><label class="form-label">व्यवसाय ₹ (लाख)</label>
        <input type="text" name="business_status_amt" class="form-control"
          value="<?= h($form['business_status_amt']) ?>">
      </div>

      <div><label class="form-label">संतुलन पत्र किस वर्ष तक</label>
        <input type="text" name="balance_sheet_year" class="form-control" value="<?= h($form['balance_sheet_year']) ?>">
      </div>

      <div><label class="form-label">अन्तिम आडिट कब तक</label>
        <input type="text" name="last_audit_date" class="form-control" value="<?= h($form['last_audit_date']) ?>">
      </div>

      <div><label class="form-label">परिसम्पत्ति</label>
        <select name="liquidation_type" class="form-select">
          <option value="">--चुनें--</option>
          <option value="स्वयं" <?= ($form['liquidation_type'] === 'स्वयं') ? 'selected' : ''; ?>>स्वयं</option>
          <option value="किराये का" <?= ($form['liquidation_type'] === 'किराये का') ? 'selected' : ''; ?>>किराये का
          </option>
        </select>
      </div>

      <div><label class="form-label">समिति की स्थिति</label>
        <select name="samiti_sthiti" class="form-select">
          <option value="">--चुनें--</option>
          <option value="परिसमापनाधीन" <?= ($form['samiti_sthiti'] === 'परिसमापनाधीन') ? 'selected' : ''; ?>>परिसमापनाधीन
          </option>
          <option value="प्रबन्ध कमेटी गठित" <?= ($form['samiti_sthiti'] === 'प्रबन्ध कमेटी गठित') ? 'selected' : ''; ?>>
            प्रबन्ध कमेटी गठित</option>
          <option value="प्रबन्ध कमेटी गठित नही" <?= ($form['samiti_sthiti'] === 'प्रबन्ध कमेटी गठित नही') ? 'selected' : ''; ?>>प्रबन्ध कमेटी गठित नही</option>
        </select>
      </div>
    </div>
  </div>

  <div class="card">
    <h3 class="section-heading">📦 अन्य जानकारी</h3>
    <div>
      <label class="form-label">अन्य सम्पत्ति का विवरण (चल/अचल)</label>
      <textarea name="any_sampatti_details" class="form-control"
        rows="4"><?= h($form['any_sampatti_details']) ?></textarea>
    </div>
  </div>

  <div style="text-align:center;margin-top:20px;">
    <button type="submit" class="btn-primary"><?= $is_edit ? 'Update' : 'Submit' ?></button>
  </div>
</form>

<script>
  // JS normalization mirrors PHP: accept both codes and Hindi values defensively
  function normalizeStatusJS(s) {
    if (!s) return '';
    if (s === 'active' || s === 'सक्रिय' || s === 'सक्रिया') return 'active';
    if (s === 'non-active' || s === 'निष्क्रिय') return 'non-active';
    if (s === 'closed' || s === 'परिसमापनाधीन' || s === 'परिसमापन') return 'closed';
    if (s === 'not_applicable' || s === 'स्थापित नही है' || s === 'स्थापित नहीं है' || s === 'not applicable' || s === 'na') return 'not_applicable';

    return s;
  }

  function showHideBySocietyStatus(statusRaw) {
    var status = normalizeStatusJS(statusRaw);
    var samitiCard = document.getElementById('samiti-details');
    var liqCard = document.getElementById('society-liquidation');

    // hide everything
    if (samitiCard) samitiCard.style.display = 'none';
    if (liqCard) liqCard.style.display = 'none';
    document.querySelectorAll('#samiti-details .form-grid > div').forEach(d => d.style.display = 'none');
    document.querySelectorAll('#society-liquidation .form-grid > div').forEach(d => d.style.display = 'none');

    if (status === 'not_applicable') {
      return; // leave all hidden
    }

    // active / non-active => show samiti card + active groups + common fields
    if (status === 'active' || status === 'non-active') {
      if (samitiCard) samitiCard.style.display = 'block';
      ['group-active-1', 'group-active-2', 'group-active-3', 'group-active-4', 'group-active-5', 'group-active-6', 'group-ncd-id', 'group-samiti-naam'].forEach(id => {
        var el = document.getElementById(id); if (el) el.style.display = 'block';
      });
    }

    // closed => show samiti card + ONLY common fields + liquidation groups (do NOT show active-only)
    if (status === 'closed') {
      if (samitiCard) samitiCard.style.display = 'block';
      if (liqCard) liqCard.style.display = 'block';
      ['group-ncd-id', 'group-samiti-naam', 'group-liq-1', 'group-liq-2', 'group-liq-3'].forEach(id => {
        var el = document.getElementById(id); if (el) el.style.display = 'block';
      });
    }
  }

  function onSocietyStatusChange(el) {
    var v = (el && el.value) ? el.value : '';
    showHideBySocietyStatus(v);
  }

  function onKabjaChange(el) {
    var v = (el && el.value) ? el.value : '';
    var elc = document.querySelector('#is_kabja_vivadit_is');
    if (!elc) return;
    elc.style.display = (v === 'हाँ') ? 'block' : 'none';
  }

  // initial state
  document.addEventListener('DOMContentLoaded', function () {
    var sel = document.querySelector('select[name="society_status"]');
    if (sel) showHideBySocietyStatus(sel.value || sel.options[sel.selectedIndex].value);
    var kabja = document.querySelector('select[name="kabja_vivadit"]');
    if (kabja) onKabjaChange(kabja);
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