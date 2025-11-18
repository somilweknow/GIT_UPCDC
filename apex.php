<?php
include("scripts/settings.php");
error_reporting(E_ALL);

$apex_id = 0;
if (!empty($_SESSION['apex_id'])) {
  $apex_id = intval($_SESSION['apex_id']);
} elseif (!empty($_GET['exdid'])) {
  $apex_id = intval($_GET['exdid']);
}

if (!function_exists('h')) {
  function h($v)
  {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}
function esc($db, $v)
{
  return mysqli_real_escape_string($db, trim((string) ($v ?? '')));
}

$msg = '';
$msg_class = 'success';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// helper to build district options with optional selected value
function buildDistrictOptions($db, $selected = null)
{
  $opts = '';
  if (!empty($_SESSION['district_id'])) {
    $ids = array_map('intval', (array) $_SESSION['district_id']);
    $sql = 'SELECT * FROM master_district WHERE sno IN (' . implode(',', $ids) . ') ORDER BY district_name';
  } else {
    $sql = 'SELECT * FROM master_district ORDER BY district_name';
  }
  $rd = mysqli_query($db, $sql);
  while ($rr = mysqli_fetch_assoc($rd)) {
    $sel = ((string) $selected === (string) $rr['sno']) ? ' selected' : '';
    $opts .= '<option value="' . (int) $rr['sno'] . '"' . $sel . '>' . h($rr['district_name']) . '</option>';
  }
  return $opts;
}

// load existing main + other rows if editing (for View/Edit)
$main_fields = [
  'janpad' => '',
  'tehsil' => '',
  'urban_rural' => '',
  'land_area' => '',
  'latitude' => '',
  'longitude' => '',
  'address' => '',
  'tenure' => '',
  'tenure_other' => '',
  'vacant_occupied' => '',
  'construction_type' => '',
  'construction_other' => '',
  'dispute_details' => '',
  'building_type' => '',
  'building_usage' => '',
  'building_usage_other' => '',
  'occupied_by' => ''
];
$other_rows = []; // array of associative arrays for each other land

if ($id > 0 && $_SERVER['REQUEST_METHOD'] !== 'POST') {
  $q = "SELECT * FROM apex_main_land WHERE sno = " . intval($id) . " LIMIT 1";
  $res = mysqli_query($db, $q);
  if ($res && $row = mysqli_fetch_assoc($res)) {
    foreach ($main_fields as $k => $_v) {
      if (array_key_exists($k, $row))
        $main_fields[$k] = (string) $row[$k];
    }
    // load other rows
    $q2 = "SELECT * FROM apex_other_land WHERE main_id = " . intval($id) . " ORDER BY sno";
    $res2 = mysqli_query($db, $q2);
    while ($r2 = mysqli_fetch_assoc($res2)) {
      $other_rows[] = [
        'janpad' => $r2['janpad'] ?? '',
        'tehsil' => $r2['tehsil'] ?? '',
        'urban_rural' => $r2['urban_rural'] ?? '',
        'land_area' => $r2['land_area'] ?? '',
        'latitude' => $r2['latitude'] ?? '',
        'longitude' => $r2['longitude'] ?? '',
        'address' => $r2['address'] ?? '',
        'tenure' => $r2['tenure'] ?? '',
        'tenure_other' => $r2['tenure_other'] ?? '',
        'vacant_occupied' => $r2['vacant_occupied'] ?? '',
        'construction_type' => $r2['construction_type'] ?? '',
        'construction_other' => $r2['construction_other'] ?? '',
        'dispute_details' => $r2['dispute_details'] ?? '',
        'building_type' => $r2['building_type'] ?? '',
        'building_usage' => $r2['building_usage'] ?? '',
        'building_usage_other' => $r2['building_usage_other'] ?? '',
        'occupied_by' => $r2['occupied_by'] ?? ''
      ];
    }
  } else {
    $msg = 'रिकॉर्ड नहीं मिला।';
    $msg_class = 'error';
    $id = 0;
  }
}

// Determine if main location should be locked (freeze) — if both lat & lng present in DB
$location_locked = (!empty($main_fields['latitude']) && !empty($main_fields['longitude']));

// Handle POST (insert or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // get id hidden (for update)
  $post_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  // Top location inputs that map to main.latitude/longitude
  // NOTE: server expects latitude_top/longitude_top (kept consistent)
  $lat = $_POST['latitude_top'] ?? '';
  $lng = $_POST['longitude_top'] ?? '';

  // main fields (without lat/long in main section)
  $main = [];
  $main['janpad'] = $_POST['janpad'] ?? '';
  $main['tehsil'] = $_POST['tehsil'] ?? '';
  $main['urban_rural'] = $_POST['urban_rural'] ?? '';
  $main['land_area'] = $_POST['land_area'] ?? '';
  $main['latitude'] = $lat;
  $main['longitude'] = $lng;
  $main['address'] = $_POST['address'] ?? '';
  $main['tenure'] = $_POST['tenure'] ?? '';
  $main['tenure_other'] = $_POST['tenure_other'] ?? '';
  $main['vacant_occupied'] = $_POST['vacant_occupied'] ?? '';
  $main['construction_type'] = $_POST['construction_type'] ?? '';
  $main['construction_other'] = $_POST['construction_other'] ?? '';
  $main['dispute_details'] = $_POST['dispute_details'] ?? '';
  $main['building_type'] = $_POST['building_type'] ?? '';
  $main['building_usage'] = $_POST['building_usage'] ?? '';
  $main['building_usage_other'] = $_POST['building_usage_other'] ?? '';
  $main['occupied_by'] = $_POST['occupied_by'] ?? '';

  // basic validation
  if (trim($main['janpad']) === '' || trim($main['tehsil']) === '') {
    $msg = 'कृपया जिला और तहसील भरें (मुख्यालय की भूमि)।';
    $msg_class = 'error';
    // repopulate form values for display
    foreach ($main_fields as $k => $_v) {
      if (isset($main[$k]))
        $main_fields[$k] = $main[$k];
    }
    $other_rows = $_POST['other'] ?? [];
  } else {
    // escape main
    $janpad = esc($db, $main['janpad']);
    $tehsil = esc($db, $main['tehsil']);
    $urban_rural = esc($db, $main['urban_rural']);
    $land_area = esc($db, $main['land_area']);
    $latitude = esc($db, $main['latitude']);
    $longitude = esc($db, $main['longitude']);
    $address = esc($db, $main['address']);
    $tenure = esc($db, $main['tenure']);
    $tenure_other = esc($db, $main['tenure_other']);
    $vacant_occupied = esc($db, $main['vacant_occupied']);
    $construction_type = esc($db, $main['construction_type']);
    $construction_other = esc($db, $main['construction_other']);
    $dispute_details = esc($db, $main['dispute_details']);
    $building_type = esc($db, $main['building_type']);
    $building_usage = esc($db, $main['building_usage']);
    $building_usage_other = esc($db, $main['building_usage_other']);
    $occupied_by = esc($db, $main['occupied_by']);

    if ($post_id > 0) {
      // if location was previously filled in DB and locked, don't overwrite lat/lng
      if ($location_locked) {
        $latitude = esc($db, $main_fields['latitude']);
        $longitude = esc($db, $main_fields['longitude']);
      }

      // UPDATE main (include apex_id)
      $sql_u = "UPDATE apex_main_land SET
        janpad='{$janpad}', tehsil='{$tehsil}', urban_rural='{$urban_rural}', land_area='{$land_area}',
        latitude='{$latitude}', longitude='{$longitude}', address='{$address}', tenure='{$tenure}', tenure_other='{$tenure_other}',
        vacant_occupied='{$vacant_occupied}', construction_type='{$construction_type}', construction_other='{$construction_other}',
        dispute_details='{$dispute_details}', building_type='{$building_type}', building_usage='{$building_usage}',
        building_usage_other='{$building_usage_other}', occupied_by='{$occupied_by}',
        apex_id='" . intval($apex_id) . "', edited_at=NOW()
        WHERE sno=" . intval($post_id) . " LIMIT 1";

      if (!mysqli_query($db, $sql_u)) {
        $msg = 'Update error: ' . h(mysqli_error($db));
        $msg_class = 'error';
      } else {
        // delete existing other rows for this main and re-insert
        mysqli_query($db, "DELETE FROM apex_other_land WHERE main_id=" . intval($post_id));
        $others = $_POST['other'] ?? [];
        if (is_array($others) && count($others) > 0) {
          // prepare counts
          $count = 0;
          // compute maximum length among all arrays inside $others
          foreach ($others as $k => $arr) {
            if (is_array($arr)) $count = max($count, count($arr));
          }
          for ($i = 0; $i < $count; $i++) {
            $ojan = esc($db, $others['janpad'][$i] ?? '');
            $oteh = esc($db, $others['tehsil'][$i] ?? '');
            $our = esc($db, $others['urban_rural'][$i] ?? '');
            $oarea = esc($db, $others['land_area'][$i] ?? '');
            $olat = esc($db, $others['latitude'][$i] ?? '');
            $olng = esc($db, $others['longitude'][$i] ?? '');
            $oaddr = esc($db, $others['address'][$i] ?? '');
            $oten = esc($db, $others['tenure'][$i] ?? '');
            $oten_other = esc($db, $others['tenure_other'][$i] ?? '');
            $ovac = esc($db, $others['vacant_occupied'][$i] ?? '');
            $ocon_type = esc($db, $others['construction_type'][$i] ?? '');
            $ocon_other = esc($db, $others['construction_other'][$i] ?? '');
            $odispute = esc($db, $others['dispute_details'][$i] ?? '');
            $obtype = esc($db, $others['building_type'][$i] ?? '');
            $obus = esc($db, $others['building_usage'][$i] ?? '');
            $obus_other = esc($db, $others['building_usage_other'][$i] ?? '');
            $ooccby = esc($db, $others['occupied_by'][$i] ?? '');

            // skip empty rows
            if (trim($ojan) === '' && trim($oteh) === '' && trim($oarea) === '')
              continue;

            $sql_o = "INSERT INTO apex_other_land (main_id, janpad, tehsil, urban_rural, land_area, latitude, longitude, address, tenure, tenure_other, vacant_occupied, construction_type, construction_other, dispute_details, building_type, building_usage, building_usage_other, occupied_by, apex_id, created_at, edited_at)
                          VALUES ('" . intval($post_id) . "','" . $ojan . "','" . $oteh . "','" . $our . "','" . $oarea . "','" . $olat . "','" . $olng . "','" . $oaddr . "','" . $oten . "','" . $oten_other . "','" . $ovac . "','" . $ocon_type . "','" . $ocon_other . "','" . $odispute . "','" . $obtype . "','" . $obus . "','" . $obus_other . "','" . $ooccby . "','" . intval($apex_id) . "', NOW(), NOW())";

            mysqli_query($db, $sql_o);
          }
        }
        header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?saved=1&id=' . intval($post_id));
        exit;
      }
    } else {
      // INSERT new main
      $sql = "INSERT INTO apex_main_land (janpad, tehsil, urban_rural, land_area, latitude, longitude, address, tenure, tenure_other, vacant_occupied, construction_type, construction_other, dispute_details, building_type, building_usage, building_usage_other, occupied_by, apex_id, created_at, edited_at)
        VALUES ('{$janpad}','{$tehsil}','{$urban_rural}','{$land_area}','{$latitude}','{$longitude}','{$address}','{$tenure}','{$tenure_other}','{$vacant_occupied}','{$construction_type}','{$construction_other}','{$dispute_details}','{$building_type}','{$building_usage}','{$building_usage_other}','{$occupied_by}','" . intval($apex_id) . "', NOW(), NOW())";
      if (mysqli_query($db, $sql)) {
        $main_id = mysqli_insert_id($db);
        $others = $_POST['other'] ?? [];
        if (is_array($others) && count($others) > 0) {
          $count = 0;
          foreach ($others as $k => $arr) {
            if (is_array($arr)) $count = max($count, count($arr));
          }
          for ($i = 0; $i < $count; $i++) {
            $ojan = esc($db, $others['janpad'][$i] ?? '');
            $oteh = esc($db, $others['tehsil'][$i] ?? '');
            $our = esc($db, $others['urban_rural'][$i] ?? '');
            $oarea = esc($db, $others['land_area'][$i] ?? '');
            $olat = esc($db, $others['latitude'][$i] ?? '');
            $olng = esc($db, $others['longitude'][$i] ?? '');
            $oaddr = esc($db, $others['address'][$i] ?? '');
            $oten = esc($db, $others['tenure'][$i] ?? '');
            $oten_other = esc($db, $others['tenure_other'][$i] ?? '');
            $ovac = esc($db, $others['vacant_occupied'][$i] ?? '');
            $ocon_type = esc($db, $others['construction_type'][$i] ?? '');
            $ocon_other = esc($db, $others['construction_other'][$i] ?? '');
            $odispute = esc($db, $others['dispute_details'][$i] ?? '');
            $obtype = esc($db, $others['building_type'][$i] ?? '');
            $obus = esc($db, $others['building_usage'][$i] ?? '');
            $obus_other = esc($db, $others['building_usage_other'][$i] ?? '');
            $ooccby = esc($db, $others['occupied_by'][$i] ?? '');

            if (trim($ojan) === '' && trim($oteh) === '' && trim($oarea) === '')
              continue;

            $sql_o = "INSERT INTO apex_other_land (main_id, janpad, tehsil, urban_rural, land_area, latitude, longitude, address, tenure, tenure_other, vacant_occupied, construction_type, construction_other, dispute_details, building_type, building_usage, building_usage_other, occupied_by, apex_id, created_at, edited_at)
                            VALUES ('" . intval($main_id) . "','" . $ojan . "','" . $oteh . "','" . $our . "','" . $oarea . "','" . $olat . "','" . $olng . "','" . $oaddr . "','" . $oten . "','" . $oten_other . "','" . $ovac . "','" . $ocon_type . "','" . $ocon_other . "','" . $odispute . "','" . $obtype . "','" . $obus . "','" . $obus_other . "','" . $ooccby . "','" . intval($apex_id) . "', NOW(), NOW())";

            mysqli_query($db, $sql_o); // ✅ execute
          }
        }
        header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?') . '?saved=1&id=' . intval($main_id));
        exit;
      } else {
        $msg = 'Insert error: ' . h(mysqli_error($db));
        $msg_class = 'error';
      }
    }
  }
}

// after redirect
if (isset($_GET['saved']) && $_GET['saved'] == '1') {
  $msg = '✅ This Form has been Updated Successfully.';
  $msg_class = 'success';
}

page_header_start();
page_header_end();
page_sidebar();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --brand: #0f6ad9;
    --accent: #06b6d4;
    --card-bg: #ffffff;
    --muted: #6b7280;
    --border: #e6edf6;
    --soft: #f3f7fb;
    --danger: #ef4444;
  }

  body {
    font-family: "Noto Sans", Arial, sans-serif;
    background: linear-gradient(180deg, #f6f9ff 0%, #f6f8fb 100%);
    margin: 0;
    color: #1f2937
  }

  .container {
    max-width: 1300px;
    margin: 22px auto;
    padding: 0 16px;
  }

  h1.page-title {
    color: var(--brand);
    text-align: center;
    font-size: 28px;
    margin: 12px 0 18px;
    font-weight: 800;
    letter-spacing: -0.2px;
  }

  .card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(13, 60, 122, 0.04);
    margin-bottom: 18px;
  }

  .section-heading {
    background: linear-gradient(90deg, var(--brand), var(--accent));
    color: #fff;
    padding: 10px 14px;
    border-radius: 8px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
    font-size: 15px;
  }

  /* 3-column responsive grid - all controls share consistent width */
  .form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 12px;
  }

  .three-col-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 12px;
  }

  /* uniform control style */
  label {
    display: block;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 6px;
    font-weight: 700;
    white-space: normal;
    line-height: 1.15;
  }

  input[type=text],
  input[type=number],
  select,
  textarea {
    width: 100%;
    padding: 9px 10px;
    border: 1px solid #e2efff;
    background: #fbfdff;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: box-shadow .12s ease, border-color .12s ease;
    box-sizing: border-box;
  }

  input[type=text]:focus,
  select:focus,
  textarea:focus {
    box-shadow: 0 6px 18px rgba(15, 106, 217, 0.06);
    border-color: rgba(15, 106, 217, 0.9);
  }

  textarea {
    resize: vertical;
    min-height: 48px;
  }

  .btn {
    background: var(--brand);
    color: #fff;
    padding: 9px 12px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn.secondary {
    background: #fff;
    color: var(--brand);
    border: 1px solid var(--border);
  }

  .btn.small {
    padding: 6px 10px;
    font-size: 13px;
  }

  #map_small {
    width: 100%;
    height: 100%;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--border);
  }

  .report-controls {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    align-items: center;
    margin-bottom: 10px;
  }

  .report-table {
    border-collapse: collapse;
    width: 100%;
    font-size: 14px;
    border-radius: 8px;
    overflow: hidden;
  }

  .report-table thead th {
    background: #f1f5f9;
    padding: 10px;
    position: sticky;
    top: 0;
    text-align: left;
    border-bottom: 1px solid #e6edf6;
  }

  .report-table tbody td {
    padding: 8px 10px;
    border-bottom: 1px solid #f1f5f9;
  }

  .report-table tbody tr:nth-child(odd) {
    background: #fff;
  }

  .report-table tbody tr:nth-child(even) {
    background: #fbfdff;
  }

  .search-input {
    padding: 8px 10px;
    border-radius: 8px;
    border: 1px solid #d7e3f7;
    width: 100%;
    max-width: 360px;
  }

  @media (max-width:900px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .three-col-grid {
      grid-template-columns: 1fr;
    }

    #map_small {
      width: 100%;
      height: 200px;
    }

    .report-controls {
      flex-direction: column;
      align-items: stretch;
    }
  }

  .span-3 {
    grid-column: 1 / -1;
  }

  .inline-pair {
    display: flex;
    gap: 8px;
  }

  .inline-pair>* {
    flex: 1 1 0;
  }

  .muted-note {
    color: #6b7280;
    font-size: 13px;
    margin-top: 6px;
  }

  .small-map {
    width: 100%;
    height: 250px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--border);
    margin-top: 8px;
  }

  .row-legend {
    font-weight: 700;
    margin-bottom: 8px;
  }

  .locked {
    opacity: 0.6;
    pointer-events: none;
  }

  .inline-controls {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .inline-controls .select-small {
    min-width: 170px;
  }

  .location-inline-area {
    display: inline-flex;
    gap: 8px;
    align-items: center;
    margin-left: 6px;
  }

  .location-inline-area input[type=text] {
    width: 120px;
    font-size: 13px;
    padding: 7px 8px;
  }

  .alert.success {
    background: #ecfdf5;
    border-left: 4px solid #10b981;
    padding: 10px;
    margin-bottom: 12px;
    color: #064e3b;
    border-radius: 6px;
  }

  .alert.error {
    background: #fff1f2;
    border-left: 4px solid var(--danger);
    padding: 10px;
    margin-bottom: 12px;
    color: #7f1d1d;
    border-radius: 6px;
  }

  .other-row .section-tools {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    align-items: center;
  }

  .other-placeholder {
    min-height: 8px;
  }

  /* new per-row grid: left controls + right small map */
  .other-row-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 12px;
    align-items: start;
  }

  .other-left {
    /* left column holds inputs & buttons */
  }

  .other-right {
    /* right column holds small map preview */
  }
</style>

<?php
// Report listing (existing main records)
echo '<div class="card"><div class="section-heading"><i class="fa-solid fa-table"></i> एपेक्स भूमि सर्वेक्षण रिपोर्ट</div>';

$apex_clause = '';
if (!empty($_SESSION['apex_id'])) {
  $apex_clause = " WHERE a.apex_id = " . intval($_SESSION['apex_id']);
}
$sql = "SELECT a.sno, d.district_name, a.tehsil, a.land_area, a.urban_rural 
          FROM apex_main_land a 
          LEFT JOIN master_district d ON d.sno=a.janpad
          {$apex_clause}
          ORDER BY d.district_name, a.sno DESC";
$res = mysqli_query($db, $sql);
echo '<div style="margin-top:12px;">';

if ($res && mysqli_num_rows($res) > 0) {
  echo '<div style="overflow:auto"><table class="report-table" id="reportTable"><thead><tr><th style="width:60px">क्रम</th><th>जनपद</th><th>तहसील</th><th>भूमि क्षेत्रफल (हेक्टेयर में)</th><th>शहरी/ग्रामीण</th><th style="width:180px">Action</th></tr></thead><tbody>';
  $i = 1;
  while ($r = mysqli_fetch_assoc($res)) {
    $printUrl = 'apex_land_print.php?id=' . intval($r['sno']) . '&print=1';
    echo '<tr><td>' . $i++ . '</td><td>' . h($r['district_name']) . '</td><td>' . h($r['tehsil']) . '</td><td>' . h($r['land_area']) . '</td><td>' . h($r['urban_rural']) . '</td><td><a class="btn small" href="?id=' . intval($r['sno']) . '">View / Edit</a></td></tr>';
  }
  echo '</tbody></table></div>';
} else {
  echo '<div style="color:#777;margin-top:8px">No Data Filled Yet.</div>';
}
echo '</div></div>';
?>

<div class="container">
  <div class="card">
    <h3 class="section-heading">📍 मुख्यालय का लोकेशन</h3>
    <div class="form-grid" style="grid-template-columns: 1fr 3fr;">
      <div>
        <label>Latitude</label>
        <!-- visible main lat/long shown here (these are not posted directly,
             hidden fields inside form will be posted). We keep them editable and copy on submit. -->
        <input type="text" id="lat_show" class="form-control" readonly value="<?= h($main_fields['latitude'] ?? '') ?>">
        <label style="margin-top:10px;">Longitude</label>
        <input type="text" id="long_show" class="form-control" readonly value="<?= h($main_fields['longitude'] ?? '') ?>">

        <button type="button" class="btn" style="margin-top:10px;" onclick="getLocationMain();">
          <!-- लोकेशन पाये (Get Location) -->
           लोकेशन रिफ्रेश करे 
        </button>
        <!-- <button type="button" class="btn secondary" style="margin-top:10px;margin-left:8px;" onclick="resetLocationTop();">
          Reset
        </button> -->
        <div class="muted-note">(लोकेशन मोबाईल से भरे)</div>
      </div>
      <div id="map_container" style="height:280px;">
        <iframe id="googlemap"
          src="https://maps.google.com/maps?q=<?= h($main_fields['latitude'] ?: '0') . ',' . h($main_fields['longitude'] ?: '0') ?>&hl=hi&z=13&output=embed"
          width="100%" height="100%" style="border:1px solid; border-radius:10px;" allowfullscreen="" loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>
  </div>


  <h1 class="page-title">भूमि सम्बंधित सुचना (मुख्यालय और अन्य भूमि)</h1>

  <?php if ($msg): ?>
    <div class="alert <?= ($msg_class === 'success') ? 'success' : 'error' ?>"><?= h($msg) ?></div>
  <?php endif; ?>

  <form method="post" id="landForm" onsubmit="prepareAndSubmit(); return false;">
    <input type="hidden" name="id" value="<?= (int) $id ?>">
    <!-- hidden fields that actually go to server -->
    <input type="hidden" name="latitude_top" id="latitude_top" value="<?= h($main_fields['latitude'] ?? '') ?>">
    <input type="hidden" name="longitude_top" id="longitude_top" value="<?= h($main_fields['longitude'] ?? '') ?>">

    <div class="card">
      <div class="section-heading"><i class="fa-solid fa-building"></i> मुख्यालय की भूमि (मुख्य जानकारी)</div>

      <div class="form-grid">
        <div>
          <label>1. जिला</label>
          <select name="janpad" id="janpad" required <?= $location_locked ? 'disabled' : '' ?>>
            <option value="">-- चुनें --</option>
            <?php
            echo buildDistrictOptions($db, $main_fields['janpad']);
            ?>
          </select>
        </div>

        <div>
          <label>2. तहसील</label>
          <input type="text" name="tehsil" id="tehsil" value="<?= h($main_fields['tehsil']) ?>" required>
        </div>

        <div>
          <label>3. शहरी / ग्रामीण</label>
          <select name="urban_rural" id="urban_rural">
            <option value="">-- चुनें --</option>
            <option value="Urban" <?= ($main_fields['urban_rural'] === 'Urban') ? 'selected' : '' ?>>शहरी</option>
            <option value="Rural" <?= ($main_fields['urban_rural'] === 'Rural') ? 'selected' : '' ?>>ग्रामीण</option>
          </select>
        </div>

        <!-- row 2: land_area, tenure, tenure_other -->
        <div>
          <label>4. भूमि क्षेत्रफल (हेक्टेयर में)</label>
          <input type="text" name="land_area" id="land_area" value="<?= h($main_fields['land_area']) ?>"
            placeholder="हेक्टेयर में">
        </div>

        <div>
          <label>किसके स्वामित्व में है</label>
          <select name="tenure" id="tenure" onchange="onTenureChangeMain(this.value)">
            <option value="">-- चुनें --</option>
            <option value="Freehold" <?= ($main_fields['tenure'] === 'Freehold') ? 'selected' : '' ?>>संस्था स्वामित्व
            </option>
            <option value="Lease" <?= ($main_fields['tenure'] === 'Lease') ? 'selected' : '' ?>>पट्टा (लीज)</option>
            <option value="Other" <?= ($main_fields['tenure'] === 'Other') ? 'selected' : '' ?>>अन्य</option>
          </select>
        </div>

        <div class="col-sm-3" id="tenure_other_wrap_main"
          style="display: <?= ($main_fields['tenure'] === 'Other') ? 'block' : 'none' ?>;">
          <label>किसके स्वामित्व में है?</label>
          <input type="text" name="tenure_other" id="tenure_other" value="<?= h($main_fields['tenure_other']) ?>">
        </div>

        <!-- row 3: vacant_occupied options now 3 -->
        <div>
          <label>8. भूमि कि स्थिति</label>
          <select name="vacant_occupied" id="vacant_occupied" onchange="onVacantChangeMain(this.value)">
            <option value="">-- चुनें --</option>
            <option value="खली पड़ी है" <?= ($main_fields['vacant_occupied'] === 'खली पड़ी है') ? 'selected' : '' ?>>खली
              पढ़ी है</option>
            <option value="निर्माण है" <?= ($main_fields['vacant_occupied'] === 'निर्माण है') ? 'selected' : '' ?>>निर्माण
              है</option>
            <option value="विवादित है" <?= ($main_fields['vacant_occupied'] === 'विवादित है') ? 'selected' : '' ?>>विवादित
              है</option>
          </select>
        </div>

        <div id="construction_wrap_main"
          style="display: <?= ($main_fields['vacant_occupied'] === 'निर्माण है') ? 'block' : 'none' ?>;">
          <label>निर्माण के प्रकार</label>
          <select name="construction_type" id="construction_type" onchange="onConstructionOtherMain(this.value)">
            <option value="">-- चुनें --</option>
            <option value="office" <?= ($main_fields['construction_type'] === 'office') ? 'selected' : '' ?>>ऑफिस स्पेस है</option>
            <option value="rent" <?= ($main_fields['construction_type'] === 'rent') ? 'selected' : '' ?>>किराये पे है</option>
            <option value="not_good" <?= ($main_fields['construction_type'] === 'not_good') ? 'selected' : '' ?>>जर्जर निर्माण है</option>
            <option value="Other" <?= ($main_fields['construction_type'] === 'Other') ? 'selected' : '' ?>>अन्य</option>
          </select>
          <div id="construction_other_wrap_main"
            style="display: <?= ($main_fields['construction_type'] === 'Other') ? 'block' : 'none' ?>; margin-top:8px;">
            <input type="text" name="construction_other" id="construction_other"
              value="<?= h($main_fields['construction_other']) ?>" placeholder="">
          </div>
        </div>

        <div id="dispute_wrap_main"
          style="display: <?= ($main_fields['vacant_occupied'] === 'विवादित है') ? 'block' : 'none' ?>;">
          <label>विवाद का विवरण दर्ज करें</label>
          <textarea name="dispute_details" id="dispute_details"><?= h($main_fields['dispute_details']) ?></textarea>
        </div>

        <!-- building_type & usage -->
        <div id="building_type_wrap_main"
          style="display: <?= ($main_fields['vacant_occupied'] === 'खली पड़ी है' || $main_fields['building_type']) ? 'block' : 'none' ?>;">
          <label>9. भवन का प्रकार </label>
          <input type="text" name="building_type" id="building_type" value="<?= h($main_fields['building_type']) ?>">
        </div>

        <div id="building_usage_wrap_main"
          style="display: <?= ($main_fields['building_usage'] || $main_fields['occupied_by'] === 'Self' || $main_fields['occupied_by'] === 'Rent' || $main_fields['occupied_by'] === 'Other') ? 'block' : 'none' ?>;">
          <label>भवन किसके उपयोग में है?</label>
          <select name="building_usage" id="building_usage" onchange="onBuildingUsageMain(this.value)">
            <option value="">-- चुनें --</option>
            <option value="Self" <?= ($main_fields['building_usage'] === 'Self') ? 'selected' : '' ?>>स्वयं</option>
            <option value="Rent" <?= ($main_fields['building_usage'] === 'Rent') ? 'selected' : '' ?>>किराया पर है</option>
            <option value="Other" <?= ($main_fields['building_usage'] === 'Other') ? 'selected' : '' ?>>अन्य</option>
          </select>
          <div id="building_usage_other_wrap_main"
            style="display: <?= ($main_fields['building_usage'] === 'Other') ? 'block' : 'none' ?>; margin-top:8px;">
            <input type="text" name="building_usage_other" id="building_usage_other"
              value="<?= h($main_fields['building_usage_other']) ?>" placeholder="अन्य कारण लिखें">
          </div>
        </div>

        <div id="occupied_by_wrap_main"
          style="display: <?= ($main_fields['vacant_occupied'] === 'Occupied' || $main_fields['occupied_by']) ? 'block' : 'none' ?>;">
          <label>10. किसके द्वारा (स्वयं / अन्य)</label>
          <select name="occupied_by" id="occupied_by">
            <option value="">-- चुनें --</option>
            <option value="Self" <?= ($main_fields['occupied_by'] === 'Self') ? 'selected' : '' ?>>स्वयं</option>
            <option value="Other" <?= ($main_fields['occupied_by'] === 'Other') ? 'selected' : '' ?>>अन्य</option>
          </select>
        </div>

        <!-- dummy placeholder to keep 3 columns even when the two above are hidden -->
        <div id="placeholder_main"></div>

        <!-- address spans all 3 columns -->
        <div class="span-3">
          <label>6. पता</label>
          <textarea name="address" id="address" rows="2"><?= h($main_fields['address']) ?></textarea>
        </div>

      </div>
    </div>

    <!-- Other lands: multiple rows WITH left controls & right map -->
    <div class="card">
      <div class="section-heading"><i class="fa-solid fa-layer-plus"></i> अन्य भूमि का विवरण</div>

      <div id="other_container" style="margin-top:12px;">
        <?php
        // prepare district options without selected (for JS/template)
        $district_options_plain = '';
        if (!empty($_SESSION['district_id'])) {
          $ids = array_map('intval', (array) $_SESSION['district_id']);
          $sql = 'SELECT * FROM master_district WHERE sno IN (' . implode(',', $ids) . ') ORDER BY district_name';
        } else {
          $sql = 'SELECT * FROM master_district ORDER BY district_name';
        }
        $rd2 = mysqli_query($db, $sql);
        while ($rr2 = mysqli_fetch_assoc($rd2)) {
          $district_options_plain .= '<option value="' . (int) $rr2['sno'] . '">' . h($rr2['district_name']) . '</option>';
        }

        // Render existing other rows (each uses left controls + right small map)
        if (!empty($other_rows)) {
          foreach ($other_rows as $idx => $or) {
            echo '<div class="card other-row" style="padding:12px;margin-top:10px;">';
            echo '<div style="display:flex;justify-content:space-between;align-items:center"><div style="font-weight:700">अन्य भूमि विवरण</div><div class="section-tools"><button type="button" class="btn secondary small" onclick="this.closest(\\\'div.other-row\\\').remove()">Remove</button></div></div>';

            echo '<div class="other-row-grid" style="margin-top:10px;">';
            // left controls
            echo '<div class="other-left">';
            echo '<div class="three-col-grid">';

            // row1: janpad, tehsil, urban_rural
            echo '<div><label>1. जिला</label><select name="other[janpad][]" class="other-janpad"><option value="">-- चुनें --</option>';
            // reuse buildDistrictOptions but ensure selected matches this row
            echo buildDistrictOptions($db, $or['janpad']);
            echo '</select></div>';

            echo '<div><label>2. तहसील</label><input type="text" name="other[tehsil][]" value="' . h($or['tehsil']) . '"></div>';

            echo '<div><label>3. शहरी / ग्रामीण</label><select name="other[urban_rural][]"><option value="">--</option><option value="Urban" ' . (($or['urban_rural'] === 'Urban') ? 'selected' : '') . '>शहरी</option><option value="Rural" ' . (($or['urban_rural'] === 'Rural') ? 'selected' : '') . '>ग्रामीण</option></select></div>';

            // row2: land_area, tenure, tenure_other
            echo '<div><label>4. भूमि क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="other[land_area][]" value="' . h($or['land_area']) . '"></div>';

            echo '<div><label>किसके स्वामित्व में है</label><select name="other[tenure][]" onchange="onTenureChangeOtherRowInline(this)"><option value="">--</option><option value="Freehold" ' . (($or['tenure'] === 'Freehold') ? 'selected' : '') . '>संस्था स्वामित्व</option><option value="Lease" ' . (($or['tenure'] === 'Lease') ? 'selected' : '') . '>पट्टा (लीज)</option><option value="Other" ' . (($or['tenure'] === 'Other') ? 'selected' : '') . '>अन्य</option></select></div>';
            $tenure_other_display = ($or['tenure'] === 'Other') ? '' : 'style="display:none;"';
            echo '<div style="grid-column: 2 / 4;"><input type="text" name="other[tenure_other][]" class="other-tenure-other" value="' . h($or['tenure_other']) . '" placeholder="किसके स्वामित्व में है?" ' . $tenure_other_display . '></div>';

            // row3: vacant_occupied, construction/dispute/building usage will follow
            echo '<div><label>8. भूमि कि स्थिति</label><select name="other[vacant_occupied][]" onchange="onVacantChangeOtherRow(this)"><option value="">-- चयन --</option><option value="खली पड़ी है" ' . (($or['vacant_occupied'] === 'खली पड़ी है') ? 'selected' : '') . '>खली पड़ी है</option><option value="निर्माण है" ' . (($or['vacant_occupied'] === 'निर्माण है') ? 'selected' : '') . '>निर्माण है</option><option value="विवादित है" ' . (($or['vacant_occupied'] === 'विवादित है') ? 'selected' : '') . '>विवादित है</option></select></div>';

            $showConstruction = ($or['vacant_occupied'] === 'निर्माण है') ? 'block' : 'none';
            $showDispute = ($or['vacant_occupied'] === 'विवादित है') ? 'block' : 'none';
            echo '<div class="construction_wrap" style="display:' . $showConstruction . '"><label>निर्माण के प्रकार</label><select name="other[construction_type][]" onchange="onConstructionOtherOtherRow(this)"><option value="">--</option><option value="office" ' . (($or['construction_type'] === 'office') ? 'selected' : '') . '>ऑफिस स्पेस है</option><option value="rent" ' . (($or['construction_type'] === 'rent') ? 'selected' : '') . '>किराये पे है</option><option value="not_good" ' . (($or['construction_type'] === 'not_good') ? 'selected' : '') . '>जर्जर निर्माण है</option><option value="Other" ' . (($or['construction_type'] === 'Other') ? 'selected' : '') . '>अन्य</option></select><input type="text" name="other[construction_other][]" class="construction_other" value="' . h($or['construction_other']) . '" placeholder="" ' . (($or['construction_type'] === 'Other') ? '' : 'style="display:none;margin-top:8px;"') . '></div>';

            echo '<div class="dispute_wrap" style="display:' . $showDispute . '"><label>विवाद का विवरण</label><textarea name="other[dispute_details][]" class="dispute_details">' . h($or['dispute_details']) . '</textarea></div>';

            echo '<div class="building_wrap" style="display:' . (($or['vacant_occupied'] === 'खली पड़ी है' || $or['building_type']) ? 'block' : 'none') . '"><label>9. भवन का प्रकार </label><input type="text" name="other[building_type][]" value="' . h($or['building_type']) . '"></div>';

            echo '<div class="occupied_wrap" style="display:' . (($or['building_usage'] || $or['occupied_by']) ? 'block' : 'none') . '"><label>भवन किसके उपयोग में है?</label><select name="other[building_usage][]" onchange="onBuildingUsageOtherRow(this)"><option value="">--</option><option value="Self" ' . (($or['building_usage'] === 'Self') ? 'selected' : '') . '>स्वयं</option><option value="Rent" ' . (($or['building_usage'] === 'Rent') ? 'selected' : '') . '>किराया पर है</option><option value="Other" ' . (($or['building_usage'] === 'Other') ? 'selected' : '') . '>अन्य</option></select><input type="text" name="other[building_usage_other][]" class="building_usage_other" value="' . h($or['building_usage_other']) . '" placeholder="अन्य कारण लिखें" ' . (($or['building_usage'] === 'Other') ? '' : 'style="display:none;margin-top:8px;"') . '></div>';

            // location selection controls for row (left column)
            $lat_val = h($or['latitude']);
            $lng_val = h($or['longitude']);
            echo '<div style="grid-column:1 / 4; margin-top:8px;">';
            echo '<label style="font-weight:700;">लोकेशन चयन</label>';
            echo '<div style="display:flex;gap:8px;align-items:center;margin-top:6px;">';
            echo '<select onchange="onOtherLocationModeChange(this)" class="other-location-mode"><option value="">---Select---</option><option value="manual">Manual</option><option value="gps">GPS</option></select>';
            // manual inputs
            $manual_style = ($lat_val || $lng_val) ? '' : 'display:none;';
            echo '<input type="text" class="other-lat-show" name="other_lat_show[]" placeholder="Latitude" value="' . $lat_val . '" style="' . $manual_style . '">';
            echo '<input type="text" class="other-lng-show" name="other_lng_show[]" placeholder="Longitude" value="' . $lng_val . '" style="' . $manual_style . '">';
            echo '<button type="button" class="btn small other-gps-btn" onclick="getLocationForRow(this)">Get GPS</button>';
            echo '<button type="button" class="btn secondary small" onclick="clearOtherRowLocation(this)">रीसेट</button>';
            echo '</div>';
            echo '</div>';

            // hidden lat/lng for server
            echo '<input type="hidden" name="other[latitude][]" class="other-lat" value="' . $lat_val . '">';
            echo '<input type="hidden" name="other[longitude][]" class="other-lng" value="' . $lng_val . '">';

            // address field spanning full width of left column
            echo '<div class="span-3" style="grid-column:1 / -1;margin-top:8px;"><label>6. पता</label><textarea name="other[address][]" rows="2">' . h($or['address']) . '</textarea></div>';

            echo '</div>'; // end three-col-grid in left
            echo '</div>'; // end other-left

            // right: small map
            echo '<div class="other-right">';
            if ($lat_val || $lng_val) {
              echo '<iframe class="small-map" src="https://maps.google.com/maps?q=' . $lat_val . ',' . $lng_val . '&hl=hi&z=13&output=embed"></iframe>';
            } else {
              echo '<div class="small-map" style="display:flex;align-items:center;justify-content:center;color:#666">No map</div>';
            }
            echo '</div>'; // end other-right

            echo '</div>'; // end other-row-grid
            echo '</div>'; // end card other-row
          }
        } else {
          // if no existing other rows, show one default empty row
          echo '<script>var needInitialOtherRow = true;</script>';
        }
        ?>
      </div>

      <div style="margin-top:10px; display:flex; gap:8px; align-items:center;">
        <button type="button" class="btn" onclick="addOtherRow()">Add row</button>
        <button type="button" class="btn secondary" onclick="clearOtherRows()">Clear rows</button>
      </div>
    </div>

    <div style="text-align:center; margin:14px 0;">
      <button class="btn" type="submit">Submit</button>
    </div>
  </form>

</div>

<!-- template for other row (left controls + right map) -->
<template id="other_template">
  <div class="card other-row" style="padding:12px;margin-top:10px;">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <div style="font-weight:700">अन्य भूमि विवरण</div>
      <div class="section-tools"><button type="button" class="btn secondary small"
          onclick="this.closest('.other-row').remove()">Remove</button></div>
    </div>

    <div class="other-row-grid" style="margin-top:10px;">
      <div class="other-left">
        <div class="three-col-grid">
          <!-- row1 -->
          <div>
            <label>1. जिला</label>
            <select name="other[janpad][]" class="other-janpad">
              <option value="">-- चुनें --</option>
            </select>
          </div>
          <div>
            <label>2. तहसील</label>
            <input type="text" name="other[tehsil][]" value="">
          </div>
          <div>
            <label>3. शहरी / ग्रामीण</label>
            <select name="other[urban_rural][]">
              <option value="">--</option>
              <option value="Urban">शहरी</option>
              <option value="Rural">ग्रामीण</option>
            </select>
          </div>

          <!-- row2 -->
          <div>
            <label>4. भूमि क्षेत्रफल (हेक्टेयर में)</label>
            <input type="text" name="other[land_area][]" value="">
          </div>
          <div>
            <label>किसके स्वामित्व में है</label>
            <select name="other[tenure][]" onchange="onTenureChangeOtherRowInline(this)">
              <option value="">--</option>
              <option value="Freehold">संस्था स्वामित्व</option>
              <option value="Lease">पट्टा (लीज)</option>
              <option value="Other">अन्य</option>
            </select>
          </div>
          <div style="grid-column: 2 / 4;">
            <input type="text" name="other[tenure_other][]" class="other-tenure-other" placeholder="किसके स्वामित्व में है?"
              style="display:none;">
          </div>

          <!-- row3 -->
          <div>
            <label>8. भूमि कि स्थिति</label>
            <select name="other[vacant_occupied][]" onchange="onVacantChangeOtherRow(this)">
              <option value="">-- चयन --</option>
              <option value="खली पड़ी है">खली पड़ी है</option>
              <option value="निर्माण है">निर्माण है</option>
              <option value="विवादित है">विवादित है</option>
            </select>
          </div>

          <div class="construction_wrap" style="display:none">
            <label>निर्माण के प्रकार</label>
            <select name="other[construction_type][]" onchange="onConstructionOtherOtherRow(this)">
              <option value="">--</option>
              <option value="office">ऑफिस स्पेस है</option>
              <option value="rent">किराये पे है</option>
              <option value="not_good">जर्जर निर्माण है</option>
              <option value="Other">अन्य</option>
            </select>
            <label>अन्य है तो, विवरण दर्ज करें</label>
            <input type="text" name="other[construction_other][]" class="construction_other" placeholder=""
              style="display:none;margin-top:8px;">
          </div>

          <div class="dispute_wrap" style="display:none">
            <label>विवाद का विवरण</label>
            <textarea name="other[dispute_details][]" class="dispute_details"></textarea>
          </div>

          <div class="building_wrap" style="display:none">
            <label>9. भवन का प्रकार (यदि खाली)</label>
            <input type="text" name="other[building_type][]" value="">
          </div>
          <div class="occupied_wrap" style="display:none">
            <label>10. किसके द्वारा (स्वयं / अन्य)</label>
            <select name="other[building_usage][]" onchange="onBuildingUsageOtherRow(this)">
              <option value="">--</option>
              <option value="Self">स्वयं</option>
              <option value="Rent">किराया पर है</option>
              <option value="Other">अन्य</option>
            </select>
            <input type="text" name="other[building_usage_other][]" class="building_usage_other"
              placeholder="अन्य कारण लिखें" style="display:none;margin-top:8px;">
          </div>

          <!-- location area inside left column -->
          <div style="grid-column:1 / 4; margin-top:8px;">
            <label style="font-weight:700;">लोकेशन चयन</label>
            <div style="display:flex;gap:8px;align-items:center;margin-top:6px;">
              <select onchange="onOtherLocationModeChange(this)" class="other-location-mode">
                <option value="">---Select---</option>
                <option value="manual">Manually स्वयं से भरे</option>
                <option value="gps">GPS से भरे</option>
              </select>

              <input type="text" class="other-lat-show" name="other_lat_show[]" placeholder="Latitude"
                style="display:none;">
              <input type="text" class="other-lng-show" name="other_lng_show[]" placeholder="Longitude"
                style="display:none;">
              <button type="button" class="btn small other-gps-btn" onclick="getLocationForRow(this)">Get GPS</button>
              <button type="button" class="btn secondary small" onclick="clearOtherRowLocation(this)">रीसेट</button>
            </div>
          </div>

          <input type="hidden" name="other[latitude][]" class="other-lat">
          <input type="hidden" name="other[longitude][]" class="other-lng">

          <div class="span-3">
            <label>6. पता</label>
            <textarea name="other[address][]" rows="2"></textarea>
          </div>

        </div>
      </div>

      <div class="other-right">
        <div class="small-map" style="display:flex;align-items:center;justify-content:center;color:#666">No map</div>
      </div>
    </div>
  </div>
</template>

<script>
  // district options for template/JS
  var districtOptionsPlain = <?= json_encode($district_options_plain) ?>;
  var location_locked = <?= $location_locked ? 'true' : 'false' ?>;

  // initialize visible and hidden fields (after DOM ready)
  (function initMainLocation() {
    var latShow = document.getElementById('lat_show');
    var longShow = document.getElementById('long_show');
    var latHidden = document.getElementById('latitude_top');
    var lngHidden = document.getElementById('longitude_top');

    // populate visible from hidden if visible empty (use DB value)
    if (latHidden && latHidden.value.trim() !== '' && latShow && latShow.value.trim() === '') latShow.value = latHidden.value.trim();
    if (lngHidden && lngHidden.value.trim() !== '' && longShow && longShow.value.trim() === '') longShow.value = lngHidden.value.trim();

    // when user edits visible fields manually, copy to hidden
    if (latShow) latShow.oninput = function () {
      if (latHidden) latHidden.value = this.value;
      updateMapFromVisible();
    };
    if (longShow) longShow.oninput = function () {
      if (lngHidden) lngHidden.value = this.value;
      updateMapFromVisible();
    };

    // initial fill of hidden fields if they are empty
    if (latHidden && latHidden.value.trim() === '' && latShow && latShow.value.trim() !== '') latHidden.value = latShow.value.trim();
    if (lngHidden && lngHidden.value.trim() === '' && longShow && longShow.value.trim() !== '') lngHidden.value = longShow.value.trim();

    // add initial other row if required
    if (typeof needInitialOtherRow !== 'undefined' && needInitialOtherRow) {
      addOtherRow();
    }
  })();

  function updateMapFromVisible() {
    var lat = document.getElementById('lat_show').value.trim();
    var lng = document.getElementById('long_show').value.trim();
    if (lat !== '' && lng !== '') {
      document.getElementById('googlemap').src = 'https://maps.google.com/maps?q=' + encodeURIComponent(lat) + ',' + encodeURIComponent(lng) + '&hl=hi&z=13&output=embed';
    }
  }

  // Get current position for main location (writes hidden + visible)
  function getLocationMain() {
    if (location_locked) {
      alert('लोकेशन लॉक्ड है — पहले से भरी हुई।');
      return;
    }
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        // visible
        var latShow = document.getElementById('lat_show');
        var longShow = document.getElementById('long_show');
        if (latShow) { latShow.value = lat; }
        if (longShow) { longShow.value = lng; }
        // hidden (named for POST)
        var latHidden = document.getElementById('latitude_top');
        var lngHidden = document.getElementById('longitude_top');
        if (latHidden) latHidden.value = lat;
        if (lngHidden) lngHidden.value = lng;
        // update iframe
        document.getElementById('googlemap').src = 'https://maps.google.com/maps?q=' + lat + ',' + lng + '&hl=hi&z=13&output=embed';
      }, function (err) {
        alert('GPS error: ' + err.message);
      }, { enableHighAccuracy: true });
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function resetLocationTop() {
    if (location_locked) {
      alert('लोकेशन लॉक्ड है — रीसेट नहीं कर सकते।');
      return;
    }
    var latShow = document.getElementById('lat_show');
    var longShow = document.getElementById('long_show');
    var latHidden = document.getElementById('latitude_top');
    var lngHidden = document.getElementById('longitude_top');
    if (latShow) latShow.value = '';
    if (longShow) longShow.value = '';
    if (latHidden) latHidden.value = '';
    if (lngHidden) lngHidden.value = '';
    var iframe = document.getElementById('googlemap');
    if (iframe) iframe.src = 'https://maps.google.com/maps?q=0,0&hl=hi&z=5&output=embed';
  }

  // MAIN Tenure change
  function onTenureChangeMain(val) {
    var wrap = document.getElementById('tenure_other_wrap_main');
    if (wrap) wrap.style.display = (val === 'Other') ? 'block' : 'none';
  }

  // MAIN Vacant change: show building type when khali, construction when निर्माण है; show dispute when विवादित
  function onVacantChangeMain(val) {
    var bwrap = document.getElementById('building_type_wrap_main');
    var owrap = document.getElementById('occupied_by_wrap_main');
    var placeholder = document.getElementById('placeholder_main');
    var construction_wrap = document.getElementById('construction_wrap_main');
    var dispute_wrap = document.getElementById('dispute_wrap_main');

    if (val === 'खली पड़ी है') {
      if (bwrap) bwrap.style.display = 'inline-block';
      if (owrap) owrap.style.display = 'none';
      if (construction_wrap) construction_wrap.style.display = 'none';
      if (dispute_wrap) dispute_wrap.style.display = 'none';
    } else if (val === 'निर्माण है') {
      if (construction_wrap) construction_wrap.style.display = 'inline-block';
      if (bwrap) bwrap.style.display = 'none';
      if (owrap) owrap.style.display = 'none';
      if (dispute_wrap) dispute_wrap.style.display = 'none';
    } else if (val === 'विवादित है') {
      if (dispute_wrap) dispute_wrap.style.display = 'inline-block';
      if (bwrap) bwrap.style.display = 'none';
      if (construction_wrap) construction_wrap.style.display = 'none';
      if (owrap) owrap.style.display = 'none';
    } else {
      if (bwrap) bwrap.style.display = 'none';
      if (owrap) owrap.style.display = 'none';
      if (construction_wrap) construction_wrap.style.display = 'none';
      if (dispute_wrap) dispute_wrap.style.display = 'none';
    }

    // placeholder visible when neither shown to keep grid
    if (placeholder) placeholder.style.display = (val === '' || (val !== 'खली पड़ी है' && val !== 'निर्माण है' && val !== 'विवादित है')) ? 'block' : 'none';
  }

  function onConstructionOtherMain(val) {
    var wrap = document.getElementById('construction_other_wrap_main');
    if (wrap) wrap.style.display = (val === 'Other') ? 'block' : 'none';
  }

  function onBuildingUsageMain(val) {
    var wrap = document.getElementById('building_usage_other_wrap_main');
    if (wrap) wrap.style.display = (val === 'Other') ? 'block' : 'none';
  }

  // Other-row handlers
  function onVacantChangeOtherRow(selElem) {
    var selector = selElem;
    if (!(selector instanceof HTMLElement)) return;
    var rowGrid = selector.closest('.three-col-grid');
    if (!rowGrid) {
      rowGrid = selector.closest('.other-row') ? selector.closest('.other-row').querySelector('.three-col-grid') : null;
    }
    if (!rowGrid) return;
    var val = selector.value;
    var bwrap = rowGrid.querySelector('.building_wrap');
    var owrap = rowGrid.querySelector('.occupied_wrap');
    var ph = rowGrid.querySelector('.other-placeholder');
    var construction = rowGrid.querySelector('.construction_wrap');
    var dispute = rowGrid.querySelector('.dispute_wrap');

    if (val === 'खली पड़ी है') {
      if (bwrap) bwrap.style.display = 'inline-block';
      if (owrap) owrap.style.display = 'none';
      if (construction) construction.style.display = 'none';
      if (dispute) dispute.style.display = 'none';
      if (ph) ph.style.display = 'none';
    } else if (val === 'निर्माण है') {
      if (construction) construction.style.display = 'block';
      if (bwrap) bwrap.style.display = 'none';
      if (owrap) owrap.style.display = 'none';
      if (dispute) dispute.style.display = 'none';
      if (ph) ph.style.display = 'none';
    } else if (val === 'विवादित है') {
      if (dispute) dispute.style.display = 'block';
      if (bwrap) bwrap.style.display = 'none';
      if (construction) construction.style.display = 'none';
      if (owrap) owrap.style.display = 'none';
      if (ph) ph.style.display = 'none';
    } else {
      if (bwrap) bwrap.style.display = 'none';
      if (owrap) owrap.style.display = 'none';
      if (construction) construction.style.display = 'none';
      if (dispute) dispute.style.display = 'none';
      if (ph) ph.style.display = 'block';
    }
  }

  function onTenureChangeOtherRowInline(sel) {
    // sel is the tenure select in a row
    var row = sel.closest('.three-col-grid');
    if (!row) row = sel.closest('.other-row').querySelector('.three-col-grid');
    if (!row) return;
    var otherInput = row.querySelector('.other-tenure-other');
    if (!otherInput) return;
    if (sel.value === 'Other') {
      otherInput.style.display = 'block';
    } else {
      otherInput.style.display = 'none';
      otherInput.value = '';
    }
  }

  function onConstructionOtherOtherRow(sel) {
    var row = sel.closest('.three-col-grid');
    if (!row) return;
    var other = row.querySelector('.construction_other');
    if (other) other.style.display = (sel.value === 'Other') ? 'block' : 'none';
  }

  function onBuildingUsageOtherRow(sel) {
    var row = sel.closest('.three-col-grid');
    if (!row) return;
    var other = row.querySelector('.building_usage_other');
    if (other) other.style.display = (sel.value === 'Other') ? 'block' : 'none';
  }

  function onOtherLocationModeChange(sel) {
    // sel is the select element in the row
    var row = sel.closest('.three-col-grid');
    if (!row) row = sel.closest('.other-row') ? sel.closest('.other-row').querySelector('.three-col-grid') : null;
    if (!row) return;
    var mode = sel.value;
    var latShow = row.querySelector('.other-lat-show');
    var lngShow = row.querySelector('.other-lng-show');
    var latHidden = row.querySelector('.other-lat');
    var lngHidden = row.querySelector('.other-lng');
    var gpsBtn = row.querySelector('.other-gps-btn');
    var rightMap = sel.closest('.other-row') ? sel.closest('.other-row').querySelector('.other-right') : null;

    if (mode === 'manual') {
      if (latShow) { latShow.style.display = ''; latShow.readOnly = false; }
      if (lngShow) { lngShow.style.display = ''; lngShow.readOnly = false; }
      if (gpsBtn) gpsBtn.style.display = 'inline-flex';
      // when manual inputs change, update hidden inputs and update map (if both filled)
      if (latShow) latShow.oninput = function () {
        if (latHidden) latHidden.value = this.value;
        updateOtherRowMap(sel.closest('.other-row'));
      };
      if (lngShow) lngShow.oninput = function () {
        if (lngHidden) lngHidden.value = this.value;
        updateOtherRowMap(sel.closest('.other-row'));
      };
    } else if (mode === 'gps') {
      if (latShow) { latShow.style.display = ''; latShow.readOnly = true; }
      if (lngShow) { lngShow.style.display = ''; lngShow.readOnly = true; }
      if (gpsBtn) gpsBtn.style.display = 'inline-flex';
    } else {
      // none selected - hide manual inputs
      if (latShow) { latShow.style.display = 'none'; latShow.value = ''; }
      if (lngShow) { lngShow.style.display = 'none'; lngShow.value = ''; }
      if (latHidden) latHidden.value = '';
      if (lngHidden) lngHidden.value = '';
      if (rightMap) {
        var mapDiv = rightMap.querySelector('.small-map');
        if (mapDiv) mapDiv.remove();
        // show placeholder
        var placeholder = document.createElement('div');
        placeholder.className = 'small-map';
        placeholder.style.display = 'flex';
        placeholder.style.alignItems = 'center';
        placeholder.style.justifyContent = 'center';
        placeholder.style.color = '#666';
        placeholder.innerText = 'No map';
        rightMap.appendChild(placeholder);
      }
    }
  }

  function updateOtherRowMap(otherRowElem) {
    if (!otherRowElem) return;
    var latHidden = otherRowElem.querySelector('.other-lat');
    var lngHidden = otherRowElem.querySelector('.other-lng');
    var latShow = otherRowElem.querySelector('.other-lat-show');
    var lngShow = otherRowElem.querySelector('.other-lng-show');
    var right = otherRowElem.querySelector('.other-right');
    var lat = (latShow && latShow.value) ? latShow.value : (latHidden ? latHidden.value : '');
    var lng = (lngShow && lngShow.value) ? lngShow.value : (lngHidden ? lngHidden.value : '');
    if (!lat || !lng) return;
    // remove placeholder and add iframe
    if (right) {
      var existing = right.querySelector('iframe.small-map');
      if (!existing) {
        right.innerHTML = '';
        var map = document.createElement('iframe');
        map.className = 'small-map';
        map.src = 'https://maps.google.com/maps?q=' + encodeURIComponent(lat) + ',' + encodeURIComponent(lng) + '&hl=hi&z=13&output=embed';
        right.appendChild(map);
      } else {
        existing.src = 'https://maps.google.com/maps?q=' + encodeURIComponent(lat) + ',' + encodeURIComponent(lng) + '&hl=hi&z=13&output=embed';
      }
    }
  }

  // get location for a specific row (button passes itself)
  function getLocationForRow(btn) {
    var row = btn.closest('.three-col-grid');
    if (!row) row = btn.closest('.other-row') ? btn.closest('.other-row').querySelector('.three-col-grid') : null;
    if (!row) return;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        var latShow = row.querySelector('.other-lat-show');
        var lngShow = row.querySelector('.other-lng-show');
        var latHidden = row.querySelector('.other-lat');
        var lngHidden = row.querySelector('.other-lng');

        if (latShow) { latShow.value = lat; latShow.readOnly = true; latShow.style.display = ''; }
        if (lngShow) { lngShow.value = lng; lngShow.readOnly = true; lngShow.style.display = ''; }
        if (latHidden) latHidden.value = lat;
        if (lngHidden) lngHidden.value = lng;

        // update right map container
        var rowElem = row.closest('.other-row');
        updateOtherRowMap(rowElem);

        // also set the other-location-mode to gps visually
        var modeSel = row.querySelector('.other-location-mode');
        if (modeSel) modeSel.value = 'gps';
      }, function (err) {
        alert('GPS error: ' + err.message);
      }, { enableHighAccuracy: true });
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function clearOtherRowLocation(btn) {
    var row = btn.closest('.three-col-grid');
    if (!row) row = btn.closest('.other-row') ? btn.closest('.other-row').querySelector('.three-col-grid') : null;
    if (!row) return;
    var latShow = row.querySelector('.other-lat-show');
    var lngShow = row.querySelector('.other-lng-show');
    var latHidden = row.querySelector('.other-lat');
    var lngHidden = row.querySelector('.other-lng');
    if (latShow) latShow.value = '';
    if (lngShow) lngShow.value = '';
    if (latHidden) latHidden.value = '';
    if (lngHidden) lngHidden.value = '';
    var right = row.closest('.other-row') ? row.closest('.other-row').querySelector('.other-right') : null;
    if (right) {
      right.innerHTML = '<div class="small-map" style="display:flex;align-items:center;justify-content:center;color:#666">No map</div>';
    }
  }

  // Add other row: clone template and copy district options
  function addOtherRow() {
    var tpl = document.getElementById('other_template');
    var clone = tpl.content.cloneNode(true);
    // copy district options
    var target = clone.querySelector('.other-janpad');
    if (target) target.innerHTML = '<option value="">-- चुनें --</option>' + districtOptionsPlain;
    // append
    document.getElementById('other_container').appendChild(clone);
    // ensure the new vacant select has the onchange properly bound (inline attribute already calls function)
    var newRow = document.getElementById('other_container').lastElementChild;
    if (newRow) {
      // initialize other-location-mode default
      var locSel = newRow.querySelector('.other-location-mode');
      if (locSel) {
        locSel.value = '';
        onOtherLocationModeChange(locSel);
      }
    }
    // scroll to new
    var cont = document.getElementById('other_container');
    if (cont.lastElementChild) cont.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function clearOtherRows() {
    document.getElementById('other_container').innerHTML = '';
    // ensure at least one row remains visible
    addOtherRow();
  }

  // prepare hidden lat/long fields and submit normally
  function prepareAndSubmit() {
    // protect locked location: if locked, copy DB values into hidden fields to avoid overwriting
    var latHidden = document.getElementById('latitude_top');
    var lngHidden = document.getElementById('longitude_top');
    var latVisible = document.getElementById('lat_show') ? document.getElementById('lat_show').value.trim() : '';
    var lngVisible = document.getElementById('long_show') ? document.getElementById('long_show').value.trim() : '';

    if (location_locked) {
      // preserve server-side DB values (they were set already in page render)
    } else {
      if (latHidden) latHidden.value = latVisible;
      if (lngHidden) lngHidden.value = lngVisible;
    }

    // for each other-row, ensure hidden lat/lng inputs are set (copy from visible fields if needed)
    document.querySelectorAll('.three-col-grid').forEach(function (grid) {
      var latShow = grid.querySelector('.other-lat-show');
      var lngShow = grid.querySelector('.other-lng-show');
      var latHidden = grid.querySelector('.other-lat');
      var lngHidden = grid.querySelector('.other-lng');
      if (latHidden && latShow && latShow.value) latHidden.value = latShow.value;
      if (lngHidden && lngShow && lngShow.value) lngHidden.value = lngShow.value;
    });

    // finally submit by turning off our handler and using native submit
    document.getElementById('landForm').onsubmit = null;
    document.getElementById('landForm').submit();
  }

  // Simple client-side search for report table (if present)
  (function () {
    var search = document.getElementById('reportSearch');
    var table = document.getElementById('reportTable');
    if (!search || !table) return;
    search.addEventListener('input', function () {
      var q = this.value.trim().toLowerCase();
      var rows = table.tBodies[0].rows;
      for (var i = 0; i < rows.length; i++) {
        var txt = rows[i].innerText.toLowerCase();
        rows[i].style.display = (q === '' || txt.indexOf(q) !== -1) ? '' : 'none';
      }
    });
  })();
</script>

<?php
page_footer_start();
page_footer_end();
?>
