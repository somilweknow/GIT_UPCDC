<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
function h($s)
{
  return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}
function e($db, $s)
{
  return mysqli_real_escape_string($db, trim((string) ($s ?? '')));
}
function arrived_label($v)
{
  if ($v === 'ordinary')
    return 'कच्ची सड़क';
  if ($v === 'nh')
    return 'नेशनल हाईवे';
  if ($v === 'sh')
    return 'स्टेट हाईवे';
  if ($v === 'mdr')
    return 'एम.डी.आर.';
  if ($v === 'odr')
    return 'ओ.डी.आर.';
  if ($v === 'rural')
    return 'ग्रामीण सड़क';
  if ($v === 'other')
    return 'अन्य';
  return $v ?: '';
}
function map_status_code_to_label($v)
{
  if ($v === 'active')
    return 'सक्रिय';
  if ($v === 'not_active')
    return 'निष्क्रिय';
  if ($v === 'liquidation')
    return 'परिसमापनाधीन';
  return $v ?: '';
}
function map_status_label_to_code($v)
{
  if ($v === 'सक्रिय')
    return 'active';
  if ($v === 'निष्क्रिय')
    return 'not_active';
  if ($v === 'परिसमापनाधीन')
    return 'liquidation';
  return in_array($v, ['active', 'not_active', 'liquidation'], true) ? $v : '';
}

page_header_start();
page_header_end();

$save_msg = '';
$save_class = '';
$form = $_POST;
$edit_row = null;

if (isset($_GET['delete_id']) && ctype_digit($_GET['delete_id'])) {
  $del_id = (int) $_GET['delete_id'];
  $cur_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';

  $del_sql = "UPDATE marketing SET is_deleted = 1, deleted_at = NOW(), deleted_by = " . $cur_user_id . " WHERE sno = " . $del_id . " LIMIT 1";
  if (mysqli_query($db, $del_sql)) {
    $save_msg = '<div style="background:#ffebee;color:#b00020;padding:10px;border-radius:5px;font-weight:bold;text-align:center;">✔️ रिकॉर्ड सफलतापूर्वक हटाया गया।</div>';
    $save_class = '';
  } else {
    $save_msg = '<div style="background:#ffebee;color:#b00020;padding:10px;border-radius:5px;font-weight:bold;text-align:center;">DB Error: ' . h(mysqli_error($db)) . '</div>';
    $save_class = '';
  }
}

if (isset($_GET['edit_id']) && ctype_digit($_GET['edit_id'])) {
  $id = (int) $_GET['edit_id'];
    $res = mysqli_query($db, "SELECT * FROM marketing WHERE sno=" . $id . " AND (is_deleted IS NULL OR is_deleted = 0)");
  if ($res && mysqli_num_rows($res)) {
    $edit_row = mysqli_fetch_assoc($res);
    $form = $edit_row;
    $form['pahuch_marg_prakar'] = $edit_row['arrived_land_type'] ?? ($edit_row['pahuch_marg_prakar'] ?? '');
    $form['is_kabja_vivadit'] = $edit_row['is_kabja_vivadit'] ?? $edit_row['possession_status'] ?? ($form['is_kabja_vivadit'] ?? '');
    $form['kabja_vivadit'] = $edit_row['possession_status'] ?? $edit_row['kabja_vivadit'] ?? ($form['kabja_vivadit'] ?? '');
    $form['parisampak_name'] = $edit_row['liquidator_name'] ?? ($form['parisampak_name'] ?? '');
    $form['parisampak_from_date'] = $edit_row['liquidato_from_date'] ?? ($form['parisampak_from_date'] ?? '');
    if (empty($form['is_active']) && !empty($form['samiti_status'])) {
      $form['is_active'] = map_status_label_to_code($form['samiti_status']);
    }
    if (empty($form['is_active'])) {
      $form['is_active'] = 'active';
    }
  }
}
// print_r($_SESSION['division_id']);
// print_r($_SESSION['district_id']);
// echo 'somillllllllllllllllllllllllllll';
// echo $_SESSION['district_id'];


// SAVE (INSERT/UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sno = isset($_POST['sno']) && ctype_digit($_POST['sno']) ? (int) $_POST['sno'] : 0;

  $division_id = $_POST['division_id'] ?? ($_SESSION['division_id'][0] ?? $_SESSION['division_id'] ?? '');
  $district_id = $_POST['district_id'] ?? ($_SESSION['district_id'][0] ?? $_SESSION['district_id'] ?? '');
  $is_active = $_POST['is_active'] ?? '';
  $isActiveCode = in_array($is_active, ['active', 'सक्रिय'], true);
  $isNotActiveCode = in_array($is_active, ['not_active', 'निष्क्रिय'], true);
  $isLiqCode = in_array($is_active, ['liquidation', 'परिसमापनाधीन'], true);
  $samiti_status = $_POST['samiti_status'] ?? '';
  // On update, if samiti_status not provided, keep existing value (don't auto-derive from is_active)
  if ($sno > 0 && $samiti_status === '') {
    $prev = mysqli_query($db, "SELECT samiti_status FROM marketing WHERE sno=" . (int) $sno);
    if ($prev && mysqli_num_rows($prev)) {
      $samiti_status_row = mysqli_fetch_assoc($prev);
      $samiti_status = $samiti_status_row['samiti_status'] ?? '';
    }
  }
  $ncd_id = ($_POST['ncd_id'] ?? '');
  $society_name = $_POST['society_name'] ?? '';
  $ado_name = $_POST['ado_name'] ?? '';
  $secretary_name = $_POST['secretary_name'] ?? '';
  $secretary_mob = $_POST['secretary_mob'] ?? '';
  $secretary_email = $_POST['secretary_email'] ?? '';
  $chairmain_name = $_POST['chairmain_name'] ?? '';
  $parisampak_name = $isLiqCode ? ($_POST['parisampak_name'] ?? '') : '';
  $parisampak_from = $isLiqCode ? ($_POST['parisampak_from_date'] ?? '') : '';

  if ($isActiveCode && $society_name === '') {
  }
  if ($isNotActiveCode) {
  }

  $land_area = $_POST['land_area'] ?? '';
  $kabza_status = $_POST['kabja_vivadit'] ?? '';
  $is_kabja_vivadit = $_POST['kabja_vivadit'] ?? '';
  $rajswa_abhi_status = $_POST['rajswa_abhilekh'] ?? '';
  $bhumi_ki_sthiti = $_POST['bhumi_ki_sthiti'] ?? '';
  $sthan_samiti_prangan = $_POST['sthan_samiti_prangan'] ?? '';
  $godam_upyukt = $_POST['godam_upyukt'] ?? '';
  $janpad_rack_duri = $_POST['janpad_rack_duri'] ?? '';
  $pahuch_marg_prakar = $_POST['pahuch_marg_prakar'] ?? '';
  $business_type = $_POST['business_type'] ?? '';
  $business_status = $_POST['business_status'] ?? '';
  $balance_year = $_POST['balance_year'] ?? '';
  $last_audit_date = $_POST['last_audit_date'] ?? '';
  $property_type = $_POST['property_type'] ?? '';
  $other_property = $_POST['other_property'] ?? '';
  $latitude = $_POST['latitude'] ?? '';
  $longitude = $_POST['longitude'] ?? '';

  // if ($isLiqCode && ($society_name === '' || $parisampak_name === '' || $parisampak_from === '')) {
  //   $save_msg = 'परिसमापनाधीन के लिए: समिति का नाम, परिसमापक का नाम, और तारीख आवश्यक है.';
  //   $save_class = 'color:#b00020;';
  // } else {

  if ($society_name === '') {
    $save_msg = '<div style="background:#ffebee;color:#b00020;padding:10px;border-radius:5px;font-weight:bold;text-align:center;">
      ⚠️ कृपया समिति का नाम दर्ज करें।
    </div>';
    $save_class = '';
  } elseif ($isLiqCode && ($parisampak_name === '' || $parisampak_from === '')) {
    $save_msg = '<div style="background:#ffebee;color:#b00020;padding:10px;border-radius:5px;font-weight:bold;text-align:center;">
      ⚠️ परिसमापनाधीन के लिए: परिसमापक का नाम और तारीख आवश्यक है।
    </div>';
    $save_class = '';
  } else {

    $cur_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';
    $create_user = $cur_user_id;

    // determine user ids (use your session key; change if you store user id elsewhere)
    $cur_user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 'NULL';
    $create_user = $cur_user_id;

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
        secretary_name='" . e($db, $secretary_name) . "',
        secretary_mob='" . e($db, $secretary_mob) . "',
        secretary_email='" . e($db, $secretary_email) . "',
        chairmain_name='" . e($db, $chairmain_name) . "',
        liquidator_name='" . e($db, $parisampak_name) . "',
        liquidato_from_date=" . ($parisampak_from ? "'" . e($db, $parisampak_from) . "'" : "NULL") . ",
        land_area='" . e($db, $land_area) . "',
        possession_status='" . e($db, $kabza_status) . "',
        is_kabja_vivadit='" . e($db, $is_kabja_vivadit) . "',
        revenue_records_status='" . e($db, $rajswa_abhi_status) . "',
        land_status='" . e($db, $bhumi_ki_sthiti) . "',
        society_land='" . e($db, $sthan_samiti_prangan) . "',
        godown_suitable='" . e($db, $godam_upyukt) . "',
        raik_distance_km='" . e($db, $janpad_rack_duri) . "',
        arrived_land_type='" . e($db, $pahuch_marg_prakar) . "',
        business_type='" . e($db, $business_type) . "',
        business_status='" . e($db, $business_status) . "',
        balance_year='" . e($db, $balance_year) . "',
        last_audit_date=" . ($last_audit_date ? "'" . e($db, $last_audit_date) . "'" : "NULL") . ",
        property_type='" . e($db, $property_type) . "',
        other_property='" . e($db, $other_property) . "',
        samiti_status='" . e($db, $samiti_status) . "',
        is_active='" . e($db, $is_active) . "',
        updated_at = NOW(),
        updated_by = " . $cur_user_id . "
      WHERE sno=" . $sno;
    } else {
      // INSERT: set created_at and created_by
      $sql = "
      INSERT INTO marketing
      (division_id,district_id,society_name,ado_name,secretary_name,secretary_mob,secretary_email,chairmain_name,ncd_id,liquidator_name,liquidato_from_date,
       land_area,possession_status,is_kabja_vivadit,revenue_records_status,land_status,society_land,godown_suitable,
       raik_distance_km,arrived_land_type,business_type,business_status,balance_year,last_audit_date,
       property_type,other_property,latitude,longitude,is_active,samiti_status,
       created_at, created_by)
      VALUES (
       '" . e($db, $division_id) . "','" . e($db, $district_id) . "','" . e($db, $society_name) . "',
       '" . e($db, $ado_name) . "','" . e($db, $secretary_name) . "','" . e($db, $secretary_mob) . "','" . e($db, $secretary_email) . "','" . e($db, $chairmain_name) . "','" . e($db, $ncd_id) . "','" . e($db, $parisampak_name) . "',
       " . ($parisampak_from ? "'" . e($db, $parisampak_from) . "'" : "NULL") . ",'" . e($db, $land_area) . "','" . e($db, $kabza_status) . "','" . e($db, $is_kabja_vivadit) . "',
       '" . e($db, $rajswa_abhi_status) . "','" . e($db, $bhumi_ki_sthiti) . "','" . e($db, $sthan_samiti_prangan) . "','" . e($db, $godam_upyukt) . "',
       '" . e($db, $janpad_rack_duri) . "','" . e($db, $pahuch_marg_prakar) . "','" . e($db, $business_type) . "','" . e($db, $business_status) . "','" . e($db, $balance_year) . "',
       " . ($last_audit_date ? "'" . e($db, $last_audit_date) . "'" : "NULL") . ",
       '" . e($db, $property_type) . "','" . e($db, $other_property) . "','" . e($db, $latitude) . "','" . e($db, $longitude) . "','" . e($db, $is_active) . "','" . e($db, $samiti_status) . "',
       NOW(), " . $create_user . "
      )";
    }


    if (mysqli_query($db, $sql)) {
      $save_msg = ($sno > 0 ? 'Updated successfully!' : 'Inserted successfully!');
      $save_class = 'color:#1b5e20;';
      if ($sno > 0) {
        // Reload fresh row from DB so form shows updated values
        $res2 = mysqli_query($db, "SELECT * FROM marketing WHERE sno=" . (int) $sno);
        if ($res2 && mysqli_num_rows($res2)) {
          $edit_row = mysqli_fetch_assoc($res2);
          $form = $edit_row;

          // keep the same mapping/compatibility you used on initial EDIT load:
          $form['pahuch_marg_prakar'] = $edit_row['arrived_land_type'] ?? ($edit_row['pahuch_marg_prakar'] ?? '');
          $form['is_kabja_vivadit'] = $edit_row['is_kabja_vivadit'] ?? $edit_row['possession_status'] ?? ($form['is_kabja_vivadit'] ?? '');
          $form['kabja_vivadit'] = $edit_row['possession_status'] ?? $edit_row['kabja_vivadit'] ?? ($form['kabja_vivadit'] ?? '');
          $form['parisampak_name'] = $edit_row['liquidator_name'] ?? ($form['parisampak_name'] ?? '');
          $form['parisampak_from_date'] = $edit_row['liquidato_from_date'] ?? ($form['parisampak_from_date'] ?? '');

          // ensure is_active defaulting behaviour as before
          if (empty($form['is_active']) && !empty($form['samiti_status'])) {
            $form['is_active'] = map_status_label_to_code($form['samiti_status']);
          }
          if (empty($form['is_active'])) {
            $form['is_active'] = 'active';
          }
        }
        if (isset($res2) && $res2)
          mysqli_free_result($res2);
      } else {
        // inserted new row — keep behaviour you want (you already clear $form = []; earlier)
        if ($sno === 0) {
          $form = [];
        }
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
<?php

$DIV_ID = $_SESSION['division_id'] ?? null;
if (is_array($DIV_ID))
  $DIV_ID = $DIV_ID[0] ?? null;
$DIS_ID = $_SESSION['district_id'] ?? null;
if (is_array($DIS_ID))
  $DIS_ID = $DIS_ID[0] ?? null;

if ($DIV_ID) {
  $where = ["m.division_id = '" . e($db, $DIV_ID) . "'"];
  if ($DIS_ID) {
    $where[] = "(m.district_id IS NULL OR m.district_id = '" . e($db, $DIS_ID) . "')";
  }

  // SELECT uses new DB column names (as per your provided list)
  $sql = "SELECT m.*, md.district_name, dv.division_name FROM marketing m LEFT JOIN master_division dv ON m.division_id = dv.sno LEFT JOIN master_district md ON m.district_id = md.sno WHERE is_deleted = 0 and " . implode(' AND ', $where) . " ORDER BY m.sno DESC";


} else {
 // echo '<div class="msg" style="color:#b00020;">Division scope नहीं मिला, रिपोर्ट नहीं दिखाई जा सकती।</div>';

    $sql = "SELECT m.*, md.district_name, dv.division_name FROM marketing m LEFT JOIN master_division dv ON m.division_id = dv.sno LEFT JOIN master_district md ON m.district_id = md.sno WHERE is_deleted !=1  ORDER BY m.sno DESC";

}
 
  
  
  $res = mysqli_query($db, $sql);
  ?>
  <?php $isEdit = (bool) $edit_row; ?>
  <div class="card">
    <h3 class="section-heading" style="text-align: center;"> रिपोर्ट</h3>
    <h3 class="blink" style="font-size: 18px; color: red;">
      <b>नोट: समस्त पहले से भरी हुयी समितियों को एक बार अवश्य एडिट कर सेव कर दे। ताकि रिपोर्ट में सही स्थिति प्रदर्शित हो
        सके।</b>
    </h3>
    <div class="table-wrap">
      <table class="report-table">
        <thead>
          <tr>
            <th>क्रम</th>
            <th>Action</th>
            <th>मण्डल</th>
            <th>जनपद</th>
            <th>NCD ID</th>
            <th>समिति का नाम</th>
            <th>ADO / सचिव का प्रकार</th>
            <th>समिति के अध्यक्ष का नाम</th>
            <th>सचिव का नाम</th>
            <th>सचिव का मो० न०</th>
            <th>सचिव का मेल-आईडी</th>
            <th>परिसमापक का नाम</th>
            <th>कब से परिसमापक है</th>
            <th>भूमि का क्षेत्रफल (हेक्टेयर में)</th>
            <th>कब्जा / विवादित</th>
            <th>समिति की स्थिति</th>
            <th>क्या समिति सक्रिय है?</th>
            
            <th>राजस्व अभिलेखों की स्थिति</th>
            <th>भूमि की स्थिति</th>
            <th>स्थान (समिति प्रांगण)</th>
            <th>गोदाम उपयुक्त</th>
            <th>जनपद रैक दूरी (कि०मी० में)</th>
            <th>पहुंच मार्ग</th>
            <th>व्यवसाय का प्रकार</th>
            <th>व्यवसाय की स्थिति</th>
            <th>संतुलन वर्ष</th>
            <th>अन्तिम आडिट</th>
            <th>परिसम्पत्ति का प्रकार</th>
            <th>अन्य सम्पत्ति</th>
            <th>समिति के स्वामित्व में है</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
          $last = null;

          if ($res) {
            while ($r = mysqli_fetch_assoc($res)) {
              // Optional: join district_name if available, otherwise use 'N/A'
              $dist = $r['district_name'] ?? 'N/A';
              // if ($dist !== $last) {
              //   echo '<tr><th colspan="27" style="background:#e9f3ff;color:#0a3d8f;font-weight:700;">जनपद: ' . h($dist) . '</th></tr>';
              //   $last = $dist;
              // }
              ?>
              <tr>
                <!-- <td><a href="?edit_id=<?= (int) $r['sno'] ?>">Edit</a></td> -->
                 <td style="display:flex;gap:6px;">
  
  <!-- Edit
<a href="?edit_id=<?= (int) $r['sno'] ?>
     style="background:#1565c0;color:#fff;padding:4px 5px;border-radius:4px;
            text-decoration:none;font-weight:600;font-size:12px;">
     ✏️ Edit
  </a>

   Delete

   
  <?php if (empty($r['is_deleted']) || $r['is_deleted'] == 0) { ?>
    <a href="?delete_id=<?= (int) $r['sno'] ?>"
       onclick="return confirm('Are You Sure ?');"
       style="background:#b00020;color:#fff;padding:4px 5px;border-radius:4px;
              text-decoration:none;font-weight:600;font-size:12px;">
       🗑️ Delete
    </a>
  <?php } else { ?>
    <span style="background:#ccc;color:#555;padding:4px 5px;border-radius:4px;
                 font-weight:600;font-size:12px;">
      (Deleted)
    </span>
  <?php } ?>  -->

  <!-- Verify -->
   
  

                  <tr>
                      <td><?= $i++ ?></td>
                      <td>
                          <a href="satyapan_marketing_form.php?verify_id=<?= (int) $r['sno'] ?>"
                            style="background:#1E90FF;color:#fff;padding:4px 5px;border-radius:4px;
                                    text-decoration:none;font-weight:600;font-size:12px;">
                            ✔ Verify
                          </a>
                      </td>
                      <td><?= h($r['division_name'] ?? '') ?></td>
                      <td><?= h($r['district_name'] ?? '') ?></td>
                      <td><?= h($r['ncd_id'] ?? '') ?></td>
                      <td><?= h($r['society_name'] ?? '') ?></td>
                      <td><?= h($r['ado_name'] ?? '') ?></td>
                      <td><?= h($r['chairmain_name'] ?? '') ?></td>
                      <td><?= h($r['secretary_name'] ?? '') ?></td>
                      <td><?= h($r['secretary_mob'] ?? '') ?></td>
                      <td><?= h($r['secretary_email'] ?? '') ?></td>
                      <td><?= h($r['liquidator_name'] ?? '') ?></td>
                      <td><?= h($r['liquidato_from_date'] ?? '') ?></td>
                      <td><?= h($r['land_area'] ?? '') ?></td>
                      <td><?= h($r['is_kabja_vivadit'] ?? $r['possession_status'] ?? '') ?></td>
                      <td><?= h($r['samiti_status'] ?? '') ?></td>
                      <td><?= h(map_status_code_to_label($r['is_active'] ?? '')) ?></td>
                      <td><?= h($r['revenue_records_status'] ?? '') ?></td>
                      <td><?= h($r['land_status'] ?? '') ?></td>
                      <td><?= h($r['society_land'] ?? '') ?></td>
                      <td><?= h($r['godown_suitable'] ?? '') ?></td>
                      <td><?= h($r['raik_distance_km'] ?? '') ?></td>
                      <td><?= h(arrived_label($r['arrived_land_type'] ?? '')) ?></td>
                      <td><?= h($r['business_type'] ?? '') ?></td>
                      <td><?= h($r['business_status'] ?? '') ?></td>
                      <td><?= h($r['balance_year'] ?? '') ?></td>
                      <td><?= h($r['last_audit_date'] ?? '') ?></td>
                      <td><?= h($r['property_type'] ?? '') ?></td>
                      <td><?= h($r['other_property'] ?? '') ?></td>
                  </tr>

            <?php
            }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

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
    font-size: 1.3em
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
    padding: 1px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 0.9em;
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

  .pdf-input {
  height: 5px;        /* default ~38px hoti hai */
  
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


  .blink {
    animation: blinker 5.0s linear infinite;
  }

  @keyframes blinker {
    50% { opacity: 0; }
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
        <label class="form-label">क्या सामिति सक्रिया हैं ?</label>
        <select name="is_active" class="form-select" onchange="toggleSamiti(this.value)">
          <option value="">--चुनें--</option>
          <option value="active" <?= (($form['is_active'] ?? '') === 'active' || ($form['is_active'] ?? '') === 'सक्रिय' || ($isEdit && empty($form['is_active']))) ? 'selected' : ''; ?>>सक्रिय</option>
          <option value="not_active" <?= (($form['is_active'] ?? '') === 'not_active' || ($form['is_active'] ?? '') === 'निष्क्रिय') ? 'selected' : ''; ?>>निष्क्रिय</option>
          <option value="liquidation" <?= (($form['is_active'] ?? '') === 'liquidation' || ($form['is_active'] ?? '') === 'परिसमापनाधीन') ? 'selected' : ''; ?>>परिसमापनाधीन</option>
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
      <div><label class="form-label">भूमि का क्षेत्रफल (हेक्टेयर में) </label>
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
      <div><label class="form-label">समिति भवन का स्वामित्व</label>
        <select name="kabja_vivadit" class="form-select" onchange="onKabjaChange(this)">
          <option value="">-- चुनें --</option>
          <option value="समिति के स्वामित्व में है" <?= ($form['kabja_vivadit'] === 'समिति के स्वामित्व में है') ? 'selected' : ''; ?>>समिति के स्वामित्व में है</option>
          <option value="किराये पर है" <?= ($form['kabja_vivadit'] === 'किराये पर है') ? 'selected' : ''; ?>>किराये पर है</option>
          <option value="अन्य स्थिति" <?= ($form['kabja_vivadit'] === 'अन्य स्थिति') ? 'selected' : ''; ?>>अन्य स्थिति</option>

        </select></div>

      <div id="is_kabja_vivadit_is" style="display: none;">
        <label class="form-label">किये गए प्रयास दर्ज करें</label>
        <textarea name="is_kabja_vivadit" class="form-control"
          rows="2"><?= h($form['is_kabja_vivadit'] ?? '') ?></textarea>
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
      <div><label class="form-label">जनपद के रैक पॉइंट से दूरी (कि०मी० में.)</label>
        <input type="text" name="janpad_rack_duri" class="form-control"
          value="<?= h($form['janpad_rack_duri'] ?? $form['raik_distance_km'] ?? '') ?>">
      </div>
      <div><label class="form-label">पहुंच मार्ग का प्रकार</label>
        <?php $pmp = $form['pahuch_marg_prakar'] ?? ($form['arrived_land_type'] ?? ''); ?>
        <select name="pahuch_marg_prakar" class="form-select">
          <option value="">--चुनें--</option>
          <option value="ordinary" <?= ($pmp === 'ordinary') ? 'selected' : ''; ?>>कच्ची सड़क</option>
          <option value="nh" <?= ($pmp === 'nh') ? 'selected' : ''; ?>>नेशनल हाईवे</option>
          <option value="sh" <?= ($pmp === 'sh') ? 'selected' : ''; ?>>स्टेट हाईवे</option>
          <option value="mdr" <?= ($pmp === 'mdr') ? 'selected' : ''; ?>>एम.डी.आर.</option>
          <option value="odr" <?= ($pmp === 'odr') ? 'selected' : ''; ?>>ओ.डी.आर.</option>
          <option value="rural" <?= ($pmp === 'rural') ? 'selected' : ''; ?>>ग्रामीण सड़क</option>
          <option value="other" <?= ($pmp === 'other') ? 'selected' : ''; ?>>अन्य</option>
        </select>

      </div>
    </div>
  </div>
  <div class="card">
    <h3 class="section-heading">📊 सत्यापन </h3>
    <div class="form-grid">
      <div><label class="form-label">सत्यापन कर रहे व्यक्ति का नाम </label>
        <input type="text" name="business_type" class="form-control" value="<?= h($form['business_type'] ?? '') ?>">
      </div>
      <div><label class="form-label">मोबाइल नंबर </label>
        <input type="text" name="business_status" class="form-control" value="<?= h($form['business_status'] ?? '') ?>">
      </div>
      <div><label class="form-label">पदनाम </label>
        <input type="text" name="balance_year" class="form-control" value="<?= h($form['balance_year'] ?? '') ?>">
      </div>
      <div id="business-last-audit"><label class="form-label">संस्था का नाम </label>
        <input type="text" name="last_audit_date" class="form-control" value="<?= h($form['last_audit_date'] ?? '') ?>">
      </div>
      <div class="col-md-8 " >
       <label class="form-label">फिजिबिलिटी रिपोर्ट (PDF अपलोड करें)</label>
        <input  type="file"  name="property_pdf"  class="form-control" accept="application/pdf" required>
      </div>
      <div class="col-md-8">
        <label class="form-label"> लेआउट प्लान (PDF अपलोड करें)</label>
        <input  type="file"  name="property_pdf"  class="form-control" accept="application/pdf" required>
      </div>  
      </div>
  </div>

  <!-- व्यवसाय 
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
        <input type="text" name="last_audit_date" class="form-control" value="<?= h($form['last_audit_date'] ?? '') ?>">
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
  </div>-->

   
  <div class="card">
    <h3 class="section-heading">📦 अन्य जानकारी</h3>
    <div>
      <label class="form-label">भूमि के आधार पर व्यवसायक सुझाव </label>
      <textarea name="other_property" class="form-control" rows="4"><?= h($form['other_property'] ?? '') ?></textarea>
    </div>
  </div>

  <div style="text-align:center;margin-top:20px;">
    <button type="submit" class="btn-primary"><?= $edit_row ? 'Update' : 'Submit' ?></button>
  </div>
</form> 


<script>
  const INIT = {
    status: '<?= h($form['is_active'] ?? '') ?>',
    ncd_id: '<?= h($form['ncd_id'] ?? '') ?>',
    society_name: '<?= h($form['society_name'] ?? '') ?>',
    ado_name: '<?= h($form['ado_name'] ?? '') ?>',
    chairmain_name: '<?= h($form['chairmain_name'] ?? '') ?>',
    secretary_name: '<?= h($form['secretary_name'] ?? '') ?>',
    secretary_mob: '<?= h($form['secretary_mob'] ?? '') ?>',
    secretary_email: '<?= h($form['secretary_email'] ?? '') ?>',
    parisampak_name: '<?= h($form['parisampak_name'] ?? '') ?>',
    parisampak_from_date: '<?= h($form['parisampak_from_date'] ?? '') ?>',
    samiti_status: '<?= h($form['samiti_status'] ?? '') ?>'
  };
  const IS_EDIT = <?= isset($edit_row) && $edit_row ? 'true' : 'false' ?>;
</script>
<script>
function toggleSamiti(status) {
  renderSamitiFields(status, INIT);
}

function renderSamitiFields(status, defaults = {}) {
  const el = document.getElementById('samiti-fields');
  const card = document.getElementById('samiti-details');
  el.innerHTML = '';
  card.style.display = 'none';

  if (!status) return;

  const isActive = (status === 'active' || status === 'सक्रिय');
  const isInactive = (status === 'not_active' || status === 'निष्क्रिय');
  const isLiq = (status === 'liquidation' || status === 'परिसमापनाधीन');

  card.style.display = 'block';

  let html = `
    <div>
      <label class="form-label">समिति का नाम</label>
      <input type="text" name="society_name" class="form-control"
        value="${defaults.society_name || ''}">
    </div>
  `;

  if (isLiq) {
    html += `
      <div>
        <label class="form-label">परिसमापक का नाम</label>
        <input type="text" name="parisampak_name" class="form-control"
          value="${defaults.parisampak_name || ''}">
      </div>

      <div>
        <label class="form-label">परिसमापक नियुक्ति तिथि</label>
        <input type="date" name="parisampak_from_date" class="form-control"
          value="${defaults.parisampak_from_date || ''}">
      </div>
    `;
  }

  el.innerHTML = html;
}

// page load
document.addEventListener("DOMContentLoaded", function () {
  if (INIT.status) {
    toggleSamiti(INIT.status);
  }
});
</script>
<?php
page_footer_start();
page_footer_end();
?>