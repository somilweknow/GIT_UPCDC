<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
// print_r($_SESSION['usertype']);
// die();
function h($s)
{
  return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}
function e($db, $s)
{
  return mysqli_real_escape_string($db, trim((string) ($s ?? '')));
}

page_header_start();
page_header_end();

$save_msg = '';
$save_class = '';
$form = $_POST;
$edit_row = null;

// EDIT load
if (isset($_GET['edit_id']) && ctype_digit($_GET['edit_id'])) {
  $id = (int) $_GET['edit_id'];
  $res = mysqli_query($db, "SELECT * FROM marketing WHERE sno=" . $id);
  if ($res && mysqli_num_rows($res)) {
    $edit_row = mysqli_fetch_assoc($res);
    $form = $edit_row;
  }
}



// SAVE (INSERT/UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sno = isset($_POST['sno']) && ctype_digit($_POST['sno']) ? (int) $_POST['sno'] : 0;

  // read all fields (simple)
  $division_id = $_POST['division_id'] ?? ($_SESSION['division_id'][0] ?? $_SESSION['division_id'] ?? '');
  $district_id = $_POST['district_id'] ?? ($_SESSION['district_id'][0] ?? $_SESSION['district_id'] ?? '');
  $samiti_status = $_POST['samiti_status'] ?? '';

  // Committee details (show based on status)
  $ncd_id = ($_POST['ncd_id'] ?? '');
  $society_name = $_POST['society_name'] ?? '';
  $ado_name = $_POST['ado_name'] ?? '';
  $adhyaksh_name = $_POST['adhyaksh_name'] ?? ''; // will be saved to chairmain_name
  $parisampak_name = ($samiti_status === 'परिसमापनाधीन') ? ($_POST['parisampak_name'] ?? '') : '';
  $parisampak_from = ($samiti_status === 'परिसमापनाधीन') ? ($_POST['parisampak_from_date'] ?? '') : '';

  // Inactive पर समाज का नाम नहीं चाहिए (you had that rule)
  if ($samiti_status === 'सक्रिय' && $society_name === '') {
    // allowed — you previously enforced society_name required for active status in other code
  }
  if ($samiti_status === 'निष्क्रिय') {
    // for inactive, optionally keep society_name empty if you want — original code set society_name = '' for inactive; keep logic if needed
  }

  // Land/Business/Other (as-is)
  $land_area = $_POST['land_area'] ?? '';
  $kabza_status = $_POST['kabja_vivadit'] ?? ''; // -> possession_status
  $rajswa_abhi_status = $_POST['rajswa_abhilekh'] ?? ''; // -> revenue_records_status
  $bhumi_ki_sthiti = $_POST['bhumi_ki_sthiti'] ?? ''; // -> land_status
  $sthan_samiti_prangan = $_POST['sthan_samiti_prangan'] ?? ''; // -> society_land
  $godam_upyukt = $_POST['godam_upyukt'] ?? '';
  $janpad_rack_duri = $_POST['janpad_rack_duri'] ?? ''; // -> raik_distance_km
  $pahuch_marg_prakar = $_POST['pahuch_marg_prakar'] ?? ''; // -> arrived_land_type
  $business_type = $_POST['business_type'] ?? '';
  $business_status = $_POST['business_status'] ?? '';
  $balance_year = $_POST['balance_year'] ?? '';
  $last_audit_date = $_POST['last_audit_date'] ?? '';
  $property_type = $_POST['property_type'] ?? '';
  $other_property = $_POST['other_property'] ?? '';
  $latitude = $_POST['latitude'] ?? '';
  $longitude = $_POST['longitude'] ?? '';

  if ($samiti_status === 'सक्रिय' && $society_name === '') {
    $save_msg = 'समिति का नाम आवश्यक है (स्थिति: सक्रिय).';
    $save_class = 'color:#b00020;';
  } elseif ($samiti_status === 'परिसमापनाधीन' && ($society_name === '' || $parisampak_name === '' || $parisampak_from === '')) {
    $save_msg = 'परिसमापनाधीन के लिए: समिति का नाम, परिसमापक का नाम, और तारीख आवश्यक है.';
    $save_class = 'color:#b00020;';
  } else {

    if ($sno > 0) {
      $sql = "
                UPDATE marketing SET
                  division_id='" . e($db, $division_id) . "',
                  district_id='" . e($db, $district_id) . "',
                  latitude='" . e($db, $latitude) . "',
                  longitude='" . e($db, $longitude) . "',
                  ncd_id='" . e($db, $ncd_id) . "',
                  society_name='" . e($db, $society_name) . "',
                  ado_name='" . e($db, $ado_name) . "',
                  chairmain_name='" . e($db, $adhyaksh_name) . "', /* UI: adhyaksh_name -> DB: chairmain_name */
                  liquidator_name='" . e($db, $parisampak_name) . "',
                  liquidato_from_date='" . e($db, $parisampak_from) . "', /* DB column name as provided */
                  land_area='" . e($db, $land_area) . "',
                  possession_status='" . e($db, $kabza_status) . "', /* kabja_vivadit -> possession_status */
                  revenue_records_status='" . e($db, $rajswa_abhi_status) . "', /* rajswa_abhilekh -> revenue_records_status */
                  land_status='" . e($db, $bhumi_ki_sthiti) . "', /* bhumi_ki_sthiti -> land_status */
                  society_land='" . e($db, $sthan_samiti_prangan) . "', /* stahn -> society_land */
                  godown_suitable='" . e($db, $godam_upyukt) . "',
                  raik_distance_km='" . e($db, $janpad_rack_duri) . "',
                  arrived_land_type='" . e($db, $pahuch_marg_prakar) . "',
                  business_type='" . e($db, $business_type) . "',
                  business_status='" . e($db, $business_status) . "',
                  balance_year='" . e($db, $balance_year) . "',
                  last_audit_date=" . ($last_audit_date ? "'" . e($db, $last_audit_date) . "'" : "NULL") . ",
                  property_type='" . e($db, $property_type) . "',
                  other_property='" . e($db, $other_property) . "',
                  samiti_status='" . e($db, $samiti_status) . "'
                WHERE sno=" . $sno;
    } else {
      $sql = "
                INSERT INTO marketing
                (division_id,district_id,society_name,ado_name,chairmain_name,ncd_id,liquidator_name,liquidato_from_date,
                 land_area,possession_status,revenue_records_status,land_status,society_land,godown_suitable,
                 raik_distance_km,arrived_land_type,business_type,business_status,balance_year,last_audit_date,
                 property_type,other_property,latitude,longitude,samiti_status)
                VALUES (
                 '" . e($db, $division_id) . "','" . e($db, $district_id) . "','" . e($db, $society_name) . "',
                 '" . e($db, $ado_name) . "','" . e($db, $adhyaksh_name) . "','" . e($db, $ncd_id) . "','" . e($db, $parisampak_name) . "',
                 '" . e($db, $parisampak_from) . "','" . e($db, $land_area) . "','" . e($db, $kabza_status) . "',
                 '" . e($db, $rajswa_abhi_status) . "','" . e($db, $bhumi_ki_sthiti) . "','" . e($db, $sthan_samiti_prangan) . "','" . e($db, $godam_upyukt) . "',
                 '" . e($db, $janpad_rack_duri) . "','" . e($db, $pahuch_marg_prakar) . "','" . e($db, $business_type) . "','" . e($db, $business_status) . "','" . e($db, $balance_year) . "',
                 " . ($last_audit_date ? "'" . e($db, $last_audit_date) . "'" : "NULL") . ",
                 '" . e($db, $property_type) . "','" . e($db, $other_property) . "','" . e($db, $latitude) . "','" . e($db, $longitude) . "','" . e($db, $samiti_status) . "'
                )";
    }

    if (mysqli_query($db, $sql)) {
      $save_msg = ($sno > 0 ? 'Updated successfully!' : 'Inserted successfully!');
      $save_class = 'color:#1b5e20;';
      if ($sno === 0) {
        $form = [];
      }
    } else {
      $save_msg = 'DB Error: ' . h(mysqli_error($db));
      $save_class = 'color:#b00020;';
    }
  }
}

// default form seeds
if (empty($form)) {
  $form['division_id'] = is_array($_SESSION['division_id'] ?? null) ? ($_SESSION['division_id'][0] ?? '') : ($_SESSION['division_id'] ?? '');
  $form['district_id'] = is_array($_SESSION['district_id'] ?? null) ? ($_SESSION['district_id'][0] ?? '') : ($_SESSION['district_id'] ?? '');
}

page_sidebar();
?>

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
    background: #4a90e2;
    color: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-weight: 700;
    font-size: 1.1em
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 15px 20px
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

  .msg {
    margin: 10px 0 20px;
    font-weight: 600
  }

  @media (max-width:768px) {
    .form-grid {
      grid-template-columns: 1fr
    }
  }
</style>

<form method="post" action="">
  <h2
    style="text-align:center;font-size:28px;color:#357ab8;font-weight:600;padding:10px;border-radius:5px;margin-bottom:10px;">
    क्रय-विक्रय सहकारी समितियों से संबंधित सूचना
  </h2>

  <?php if ($save_msg): ?>
    <div class="msg" style="<?= $save_class ?>"><?= $save_msg ?></div>
  <?php endif; ?>

  <input type="hidden" name="sno" value="<?= h($form['sno'] ?? '') ?>">

  <div class="card">
    <h3 class="section-heading">📍 लोकेशन</h3>
    <div class="form-grid" style="grid-template-columns: 1fr 3fr;">
      <div>
        <label>Latitude</label>
        <input type="text" id="lat_show" class="form-control" value="<?= h($form['latitude'] ?? '') ?>" readonly>
        <label style="margin-top:10px;">Longitude</label>
        <input type="text" id="long_show" class="form-control" value="<?= h($form['longitude'] ?? '') ?>" readonly>

        <!-- ये hidden inputs ही DB में जाएँगे -->
        <input type="hidden" name="latitude" id="lat" value="<?= h($form['latitude'] ?? '') ?>">
        <input type="hidden" name="longitude" id="long" value="<?= h($form['longitude'] ?? '') ?>">

        <button type="button" class="btn btn-info" style="margin-top:10px;" onClick="getLocation();">
          लोकेशन रिफ्रेश करें
        </button>
        <div class="blinking-text">(लोकेशन मोबाईल से भरे)*</div>
      </div>
      <div id="map_container" style="height:280px;">
        <iframe id="googlemap"
          src="https://maps.google.com/maps?q=<?= h($form['latitude'] ?? '0') . ',' . h($form['longitude'] ?? '0') ?>&hl=hi&z=13&output=embed"
          width="100%" height="100%" style="border:1px solid; border-radius:10px;" allowfullscreen="" loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>
  </div>

  <div class="card">
    <h3 class="section-heading">📑 मूलभूत जानकारी</h3>
    <div class="form-grid">
      <div>
        <label class="form-label">मण्डल का नाम</label>
        <select name="division_id" id="division_id" class="form-control" style="height:35px;">
          <?php
          $session_division_ids = $_SESSION['division_id'] ?? [];
          if (!is_array($session_division_ids))
            $session_division_ids = [$session_division_ids];
          $sql = (!empty($session_division_ids[0]) && ($_SESSION['user_type'] ?? '') === 'ar')
            ? 'SELECT * FROM master_division WHERE sno IN (' . implode(',', array_map('intval', $session_division_ids)) . ')'
            : 'SELECT * FROM master_division';
          $resDiv = mysqli_query($db, $sql);
          while ($row = mysqli_fetch_assoc($resDiv)) {
            $sel = ((string) ($form['division_id'] ?? '') === (string) $row['sno']) ? 'selected' : '';
            echo '<option value="' . h($row['sno']) . '" ' . $sel . '>' . h($row['division_name']) . '</option>';
          }
          ?>
        </select>
      </div>
      <div>
        <label class="form-label">जनपद का नाम</label>
        <select name="district_id" id="district_id" class="form-control" style="height:35px;">
          <?php
          $session_district_ids = $_SESSION['district_id'] ?? [];
          if (!is_array($session_district_ids))
            $session_district_ids = [$session_district_ids];
          $sql = (!empty($session_district_ids[0]) && ($_SESSION['user_type'] ?? '') === 'ar')
            ? 'SELECT * FROM master_district WHERE sno IN (' . implode(',', array_map('intval', $session_district_ids)) . ')'
            : 'SELECT * FROM master_district';
          $resDis = mysqli_query($db, $sql);
          while ($row = mysqli_fetch_assoc($resDis)) {
            $sel = ((string) ($form['district_id'] ?? '') === (string) $row['sno']) ? 'selected' : '';
            echo '<option value="' . h($row['sno']) . '" ' . $sel . '>' . h($row['district_name']) . '</option>';
          }
          ?>
        </select>
      </div>
      <div>
        <label class="form-label">समिति की स्थिति</label>
        <select name="samiti_status" class="form-select" onchange="toggleSamiti(this.value)">
          <option value="">--चुनें--</option>
          <option value="सक्रिय" <?= (isset($form['samiti_status']) && $form['samiti_status'] === 'सक्रिय') ? 'selected' : ''; ?>>सक्रिय</option>
          <option value="निष्क्रिय" <?= (isset($form['samiti_status']) && ($form['samiti_status'] === 'निष्क्रिय' || $form['samiti_status'] === 'נिष्क्रिय')) ? 'selected' : ''; ?>>निष्क्रिय</option>
          <option value="परिसमापनाधीन" <?= (isset($form['samiti_status']) && $form['samiti_status'] === 'परिसमापनाधीन') ? 'selected' : ''; ?>>परिसमापनाधीन</option>
        </select>
      </div>
    </div>
  </div>

  <!-- समिति विवरण (डायनामिक) -->
  <div class="card" id="samiti-details" style="display:none;">
    <h3 class="section-heading">🏢 समिति विवरण</h3>
    <div class="form-grid" id="samiti-fields"><!-- JS will render right set --></div>
  </div>

  <!-- खाली भूमि -->
  <div class="card">
    <h3 class="section-heading">🏡 खाली भूमि का विवरण</h3>
    <div class="form-grid">
      <div><label class="form-label">भूमि का क्षेत्रफल (वर्ग मीटर)</label>
        <input type="text" name="land_area" class="form-control" value="<?= h($form['land_area'] ?? '') ?>">
      </div>
      <div><label class="form-label">कब्जा / विवादित</label>
        <select name="kabja_vivadit" class="form-select"
          onchange="hide_show(this.value, '#is_kabja_vivadit_is','yes');">
          <option value="">-- चुनें --</option>
          <option value="yes" <?= ((($form['kabza_status'] ?? $form['possession_status'] ?? $form['kabja_vivadit'] ?? '') === 'yes')) ? 'selected' : ''; ?>>हाँ</option>
          <option value="no" <?= ((($form['kabza_status'] ?? $form['possession_status'] ?? $form['kabja_vivadit'] ?? '') === 'no')) ? 'selected' : ''; ?>>नहीं</option>
        </select>
      </div>
      <div id="is_kabja_vivadit_is" style="display: none;">
        <label class="form-label">किये गए प्रयास दर्ज करें</label>
        <textarea name="is_kabja_vivadit" class="form-control" rows="2"><?= h($form['is_kabja_vivadit']) ?></textarea>
      </div>
      <div><label class="form-label">राजस्व अभिलेखों में दर्ज स्थिति</label>
        <select name="rajswa_abhilekh" class="form-select">
          <option value="">-- चुनें --</option>
          <option value="हाँ" <?= ((($form['rajswa_abhi_status'] ?? $form['revenue_records_status'] ?? $form['rajswa_abhilekh'] ?? '') === 'हाँ')) ? 'selected' : ''; ?>>हाँ</option>
          <option value="नहीं" <?= ((($form['rajswa_abhi_status'] ?? $form['revenue_records_status'] ?? $form['rajswa_abhilekh'] ?? '') === 'नहीं')) ? 'selected' : ''; ?>>नहीं</option>
        </select>
      </div>
      <div><label class="form-label">भूमि की स्थिति</label>
        <select name="bhumi_ki_sthiti" class="form-select">
          <option value="">--चुनें--</option>
          <option value="उपजाऊ" <?= ((($form['bhumi_ki_sthiti'] ?? $form['land_status'] ?? '') === 'उपजाऊ')) ? 'selected' : ''; ?>>
            उपजाऊ</option>
          <option value="बंजर" <?= ((($form['bhumi_ki_sthiti'] ?? $form['land_status'] ?? '') === 'बंजर')) ? 'selected' : ''; ?>>
            बंजर</option>
        </select>
      </div>
      <div><label class="form-label">स्थान (समिति प्रांगण या अन्य)</label>
        <select name="sthan_samiti_prangan" class="form-select">
          <option value="">--चुनें--</option>
          <option value="समिति प्रांगण" <?= ((($form['sthan_samiti_prangan'] ?? $form['society_land'] ?? '') === 'समिति प्रांगण')) ? 'selected' : ''; ?>>समिति प्रांगण</option>
          <option value="अन्य स्थान" <?= ((($form['sthan_samiti_prangan'] ?? $form['society_land'] ?? '') === 'अन्य स्थान')) ? 'selected' : ''; ?>>अन्य स्थान</option>
        </select>
      </div>
      <div><label class="form-label">गोदाम के लिए उपयुक्त?</label>
        <select name="godam_upyukt" class="form-select">
          <option value="">--चुनें--</option>
          <option value="हाँ" <?= ((($form['godam_upyukt'] ?? $form['godown_suitable'] ?? '') === 'हाँ')) ? 'selected' : ''; ?>>हाँ
          </option>
          <option value="नहीं" <?= ((($form['godam_upyukt'] ?? $form['godown_suitable'] ?? '') === 'नहीं')) ? 'selected' : ''; ?>>
            नहीं</option>
        </select>
      </div>
      <div><label class="form-label">जनपद के रैक पॉइंट से दूरी (किमी.)</label>
        <input type="text" name="janpad_rack_duri" class="form-control"
          value="<?= h($form['janpad_rack_duri'] ?? $form['raik_distance_km'] ?? '') ?>">
      </div>
      <div><label class="form-label">पहुंच मार्ग का प्रकार</label>
        <select name="pahuch_marg_prakar" class="form-select">
          <option value="">--चुनें--</option>
          <option value="ordinary" <?= ($form['pahuch_marg_prakar'] === 'ordinary') ? 'selected' : ''; ?>>कच्ची सड़क
          </option>
          <option value="nh" <?= ($form['pahuch_marg_prakar'] === 'nh') ? 'selected' : ''; ?>>नेशनल हाईवे</option>
          <option value="sh" <?= ($form['pahuch_marg_prakar'] === 'sh') ? 'selected' : ''; ?>>स्टेट हाईवे</option>
          <option value="mdr" <?= ($form['pahuch_marg_prakar'] === 'mdr') ? 'selected' : ''; ?>>एम.डी.आर.</option>
          <option value="odr" <?= ($form['pahuch_marg_prakar'] === 'odr') ? 'selected' : ''; ?>>ओ.डी.आर.</option>
          <option value="rural" <?= ($form['pahuch_marg_prakar'] === 'rural') ? 'selected' : ''; ?>>ग्रामीण सड़क</option>
          <option value="other" <?= ($form['pahuch_marg_prakar'] === 'other') ? 'selected' : ''; ?>>अन्य</option>
        </select>

      </div>
    </div>
  </div>

  <!-- व्यवसाय -->
  <div class="card">
    <h3 class="section-heading">📊 व्यवसाय की जानकारी</h3>
    <div class="form-grid">
      <div><label class="form-label">व्यवसाय का प्रकार</label>
        <input type="text" name="business_type" class="form-control" value="<?= h($form['business_type'] ?? '') ?>">
      </div>
      <div><label class="form-label">व्यवसाय की स्थिति (लाभ/हानि लाख ₹)</label>
        <input type="text" name="business_status" class="form-control" value="<?= h($form['business_status'] ?? '') ?>">
      </div>
      <div><label class="form-label">संतुलन पत्र किस वर्ष तक</label>
        <input type="text" name="balance_year" class="form-control" value="<?= h($form['balance_year'] ?? '') ?>">
      </div>
      <div id="business-last-audit"><label class="form-label">अन्तिम आडिट कब तक</label>
        <input type="date" name="last_audit_date" class="form-control" value="<?= h($form['last_audit_date'] ?? '') ?>">
      </div>
      <div><label class="form-label">परिसम्पत्ति</label>
        <select name="property_type" class="form-select">
          <option value="">--चुनें--</option>
          <option value="स्वयं" <?= (($form['property_type'] ?? '') === 'स्वयं') ? 'selected' : ''; ?>>स्वयं</option>
          <option value="किराये का" <?= (($form['property_type'] ?? '') === 'किराये का') ? 'selected' : ''; ?>>किराये का
          </option>
        </select>
      </div>
    </div>
  </div>

  <!-- अन्य -->
  <div class="card">
    <h3 class="section-heading">📦 अन्य जानकारी</h3>
    <div>
      <label class="form-label">अन्य सम्पत्ति का विवरण (चल/अचल)</label>
      <textarea name="other_property" class="form-control" rows="4"><?= h($form['other_property'] ?? '') ?></textarea>
    </div>
  </div>

  <div style="text-align:center;margin-top:20px;">
    <button type="submit" class="btn-primary"><?= $edit_row ? 'Update' : 'Submit' ?></button>
  </div>
</form>

<?php
$userType = $_SESSION['user_type'] ?? null;

$DIV_ID = $_SESSION['division_id'] ?? null;
if (is_array($DIV_ID)) $DIV_ID = $DIV_ID[0] ?? null;
$DIS_ID = $_SESSION['district_id'] ?? null;
if (is_array($DIS_ID)) $DIS_ID = $DIS_ID[0] ?? null;

// ✅ Build WHERE condition
$where = [];
if ($_SESSION['usertype'] === 'sadmin') {
  // Super Admin → No restriction
  $sql = "SELECT m.*, md.district_name
          FROM marketing m
          LEFT JOIN master_district md ON m.district_id = md.sno
          ORDER BY m.sno DESC";
} elseif ($DIV_ID) {
  // Normal user → filter by division/district
  $where[] = "m.division_id = '" . e($db, $DIV_ID) . "'";
  if ($DIS_ID) {
    $where[] = "(m.district_id IS NULL OR m.district_id = '" . e($db, $DIS_ID) . "')";
  }
  $sql = "SELECT m.*, md.district_name
          FROM marketing m
          LEFT JOIN master_district md ON m.district_id = md.sno
          WHERE " . implode(' AND ', $where) . "
          ORDER BY m.sno DESC";
} else {
  $sql = null;
}

if ($sql) {
  $res = mysqli_query($db, $sql);
  ?>
  <div class="card">
    <h3 class="section-heading">📋 रिपोर्ट</h3>
    <div class="table-wrap">
      <table class="report-table">
        <thead>
          <tr>
            <th>Action</th>
            <th>Sno</th>
            <th>NCD ID</th>
            <th>समिति का नाम</th>
            <th>सचिव का प्रकार <br> ADO / ADCO</th>
            <th>समिति के अध्यक्ष का नाम</th>
            <th>परिसमापक का नाम</th>
            <th>कब से परिसमापक है</th>
            <th>भूमि का क्षेत्रफल</th>
            <th>कब्जा / विवादित</th>
            <th>राजस्व अभिलेखों</th>
            <th>भूमि की स्थिति</th>
            <th>स्थान</th>
            <th>गोदाम उपयुक्त</th>
            <th>जनपद रैक दूरी</th>
            <th>पहुंच मार्ग</th>
            <th>व्यवसाय का प्रकार</th>
            <th>व्यवसाय की स्थिति</th>
            <th>संतुलन पत्र</th>
            <th>अन्तिम आडिट</th>
            <th>परिसम्पत्ति</th>
            <th>अन्य सम्पत्ति</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $i = 1;
          $last = null;
          if ($res) {
            while ($r = mysqli_fetch_assoc($res)) {
              $dist = $r['district_name'] ?? 'N/A';
              if ($dist !== $last) {
                echo '<tr><th colspan="23" style="background:#e9f3ff;color:#0a3d8f;font-weight:700;">जनपद: ' . h($dist) . '</th></tr>';
                $last = $dist;
              } ?>
              <tr>
                <td><a href="?edit_id=<?= (int) $r['sno'] ?>">Edit</a></td>
                <td><?= $i++ ?></td>
                <td><?= h($r['ncd_id'] ?? '') ?></td>
                <td><?= h($r['society_name'] ?? '') ?></td>
                <td><?= h($r['ado_name'] ?? '') ?></td>
                <td><?= h($r['chairmain_name'] ?? $r['adhyaksh_name'] ?? '') ?></td>
                <td><?= h($r['liquidator_name'] ?? '') ?></td>
                <td><?= h($r['liquidato_from_date'] ?? $r['parisampak_from_date'] ?? '') ?></td>
                <td><?= h($r['land_area'] ?? '') ?></td>
                <td><?= h($r['possession_status'] ?? $r['kabja_vivadit'] ?? '') ?></td>
                <td><?= h($r['revenue_records_status'] ?? $r['rajswa_abhilekh'] ?? '') ?></td>
                <td><?= h($r['land_status'] ?? $r['bhumi_ki_sthiti'] ?? '') ?></td>
                <td><?= h($r['society_land'] ?? $r['sthan_samiti_prangan'] ?? '') ?></td>
                <td><?= h($r['godown_suitable'] ?? '') ?></td>
                <td><?= h($r['raik_distance_km'] ?? $r['janpad_rack_duri'] ?? '') ?></td>
                <td><?= h($r['arrived_land_type'] ?? $r['pahuch_marg_prakar'] ?? '') ?></td>
                <td><?= h($r['business_type'] ?? '') ?></td>
                <td><?= h($r['business_status'] ?? '') ?></td>
                <td><?= h($r['balance_year'] ?? '') ?></td>
                <td><?= h($r['last_audit_date'] ?? '') ?></td>
                <td><?= h($r['property_type'] ?? '') ?></td>
                <td><?= h($r['other_property'] ?? '') ?></td>
              </tr>
          <?php } } ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php
} else {
  echo '<div class="msg" style="color:#b00020;">Division scope नहीं मिला, रिपोर्ट नहीं दिखाई जा सकती।</div>';
}
?>

<script>
  const INIT = {
    status: '<?= h($form['samiti_status'] ?? '') ?>',
    ncd_id: '<?= h($form['ncd_id'] ?? '') ?>',
    society_name: '<?= h($form['society_name'] ?? '') ?>',
    ado_name: '<?= h($form['ado_name'] ?? '') ?>',
    adhyaksh_name: '<?= h($form['adhyaksh_name'] ?? '') ?>',
    parisampak_name: '<?= h($form['parisampak_name'] ?? '') ?>',
    parisampak_from_date: '<?= h($form['parisampak_from_date'] ?? '') ?>'
  };
</script>
<script>
  // एक ही फ़ंक्शन: status के अनुसार fields render करना (defaults के साथ)
  function renderSamitiFields(status, defaults) {
    const el = document.getElementById('samiti-fields');
    const showCard = document.getElementById('samiti-details');
    const isActive = (status === 'सक्रिय');
    const isInactive = (status === 'निष्क्रिय' || status === 'निष्क्रिय');
    const isLiq = (status === 'परिसमापनाधीन');

    if (!status) { showCard.style.display = 'none'; el.innerHTML = ''; return; }
    showCard.style.display = 'block';

    function val(name) {
      const i = document.querySelector("[name='" + name + "']");
      const fromDom = i ? i.value : '';
      if (fromDom && fromDom.trim() !== '') return fromDom;
      if (defaults && defaults[name]) return defaults[name];
      return '';
    }

    let html = '';
    // Always render NCD ID when any status is selected
    html += `
      <div><label class="form-label">NCD ID</label>
        <input type="text" name="ncd_id" class="form-control" value="${val('ncd_id')}" placeholder="NCD ID दर्ज करें">
      </div>`;
    if (isActive) {
      html += `
        <div><label class="form-label">समिति का नाम</label>
          <input type="text" name="society_name" class="form-control" value="${val('society_name')}">
        </div>
        <div><label class="form-label">सचिव का प्रकार / ADO / ADCO</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}" placeholder="कैडर/नॉन-कैडर/ADO/ADCO">
        </div>
        <div><label class="form-label">सचिव का नाम</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}" >
        </div>
        <div><label class="form-label">सचिव का मो० न०</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}">
        </div>
        <div><label class="form-label">सचिव का मेल-आईडी</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}">
        </div>
        <div><label class="form-label">समिति के अध्यक्ष का नाम</label>
          <input type="text" name="adhyaksh_name" class="form-control" value="${val('adhyaksh_name')}">
        </div>`;
    } else if (isInactive) {
      html += `
        <div><label class="form-label">समिति का नाम</label>
          <input type="text" name="society_name" class="form-control" value="${val('society_name')}">
        </div>
        <div><label class="form-label">सचिव का प्रकार / ADO / ADCO</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}" placeholder="कैडर/नॉन-कैडर/ADO/ADCO">
        </div>
        <div><label class="form-label">सचिव का नाम</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}" placeholder="कैडर/नॉन-कैडर/ADO/ADCO">
        </div>
        <div><label class="form-label">सचिव का मो० न०</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}" placeholder="कैडर/नॉन-कैडर/ADO/ADCO">
        </div>
        <div><label class="form-label">सचिव का मेल-आईडी</label>
          <input type="text" name="ado_name" class="form-control" value="${val('ado_name')}" placeholder="कैडर/नॉन-कैडर/ADO/ADCO">
        </div>
        <div><label class="form-label">समिति के अध्यक्ष का नाम</label>
         ... <!-- continuation of renderSamitiFields -->

          <input type="text" name="adhyaksh_name" class="form-control" value="${val('adhyaksh_name')}">
        </div>`;
    } else if (isLiq) {
      html += `
        <div><label class="form-label">समिति का नाम</label>
          <input type="text" name="society_name" class="form-control" value="${val('society_name')}">
        </div>
        <div><label class="form-label">परिसमापक का नाम</label>
          <input type="text" name="parisampak_name" class="form-control" value="${val('parisampak_name')}">
        </div>
        <div><label class="form-label">परिसमापक कब से</label>
          <input type="date" name="parisampak_from_date" class="form-control" value="${val('parisampak_from_date')}">
        </div>`;
    }
    // Inactive can have minimal fields
    el.innerHTML = html;
  }

  function toggleSamiti(status) {
    renderSamitiFields(status, INIT);
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', () => {
    toggleSamiti(INIT.status);
  });

  // Geolocation
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition, showError, { enableHighAccuracy: true });
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  function showPosition(position) {
    document.getElementById('lat').value = position.coords.latitude;
    document.getElementById('long').value = position.coords.longitude;
    document.getElementById('lat_show').value = position.coords.latitude;
    document.getElementById('long_show').value = position.coords.longitude;
    document.getElementById('googlemap').src = `https://maps.google.com/maps?q=${position.coords.latitude},${position.coords.longitude}&hl=hi&z=13&output=embed`;
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

  // Hide/show extra fields for disputed land
  function hide_show(val, selector, showIf) {
    const el = document.querySelector(selector);
    if (!el) return;
    if (val === showIf) {
      el.style.display = 'block';
    } else {
      el.style.display = 'none';
    }
  }

  // Initialize disputed land field visibility
  document.addEventListener('DOMContentLoaded', () => {
    const kabjaVal = document.querySelector("[name='kabja_vivadit']").value;
    hide_show(kabjaVal, '#is_kabja_vivadit_is', 'yes');
  });
</script>
<?php
page_footer_start();
page_footer_end();
?>