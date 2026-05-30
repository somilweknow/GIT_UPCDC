<?php
include("scripts/settings.php");
error_reporting(E_ALL);

page_header_start();
page_header_end();

// print_r($_SESSION);
/* Helpers (use same 'h' & 'e' as earlier) */
function h($s)
{
  return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}
function e($db, $s)
{
  return mysqli_real_escape_string($db, (string) ($s ?? ''));
}

/* --- LOAD / EDIT LOGIC --- */
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

// --- LAND AREA FILTER (GET)
$land_from = isset($_GET['land_area_from']) ? trim($_GET['land_area_from']) : '';
$land_to = isset($_GET['land_area_to']) ? trim($_GET['land_area_to']) : '';
$land_from_val = ($land_from === '') ? null : floatval(str_replace(',', '.', $land_from));
$land_to_val = ($land_to === '') ? null : floatval(str_replace(',', '.', $land_to));
$godown_filter = isset($_GET['godown_suitable']) ? trim($_GET['godown_suitable']) : '';

// --- SAVE (INSERT/UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sno = isset($_POST['sno']) && ctype_digit($_POST['sno']) ? (int) $_POST['sno'] : 0;

  // read fields (as provided)
  $division_id = $_POST['division_id'] ?? ($_SESSION['division_id'][0] ?? $_SESSION['division_id'] ?? '');
  $district_id = $_POST['district_id'] ?? ($_SESSION['district_id'][0] ?? $_SESSION['district_id'] ?? '');
  $samiti_status = $_POST['samiti_status'] ?? '';

  $ncd_id = ($_POST['ncd_id'] ?? '');
  $society_name = $_POST['society_name'] ?? '';
  $ado_name = $_POST['ado_name'] ?? '';
  $adhyaksh_name = $_POST['adhyaksh_name'] ?? '';
  $parisampak_name = ($samiti_status === 'परिसमापनाधीन') ? ($_POST['parisampak_name'] ?? '') : '';
  $parisampak_from = ($samiti_status === 'परिसमापनाधीन') ? ($_POST['parisampak_from_date'] ?? '') : '';

  $land_area = $_POST['land_area'] ?? '';
  $kabza_status = $_POST['kabja_vivadit'] ?? '';
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

  // basic validation
  if ($samiti_status === 'सक्रिय' && $society_name === '') {
    $save_msg = 'समिति का नाम आवश्यक है (स्थिति: सक्रिय).';
    $save_class = 'color:#b00020;';
  } elseif ($samiti_status === 'परिसमापनाधीन' && ($society_name === '' || $parisampak_name === '' || $parisampak_from === '')) {
    $save_msg = 'परिसमापनाधीन के लिए: समिति का नाम, परिसमापक का नाम, और तारीख आवश्यक है.';
    $save_class = 'color:#b00020;';
  } else {

    if ($sno > 0) {
      // UPDATE
      $sql = "
        UPDATE marketing SET
          division_id='" . e($db, $division_id) . "',
          district_id='" . e($db, $district_id) . "',
          latitude='" . e($db, $latitude) . "',
          longitude='" . e($db, $longitude) . "',
          ncd_id='" . e($db, $ncd_id) . "',
          society_name='" . e($db, $society_name) . "',
          ado_name='" . e($db, $ado_name) . "',
          chairmain_name='" . e($db, $adhyaksh_name) . "',
          liquidator_name='" . e($db, $parisampak_name) . "',
          liquidato_from_date='" . e($db, $parisampak_from) . "',
          land_area='" . e($db, $land_area) . "',
          possession_status='" . e($db, $kabza_status) . "',
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
          samiti_status='" . e($db, $samiti_status) . "'
        WHERE sno=" . $sno;
    } else {
      // INSERT
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

/* Render sidebar (only once) */
page_sidebar();
?>

<style>
  /* copy the same styling approach used in block_union report for visual parity */
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
    font-size: 2.0em;
    line-height: 1.05;
  }

  .form-control,
  .form-select,
  textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: .95em;
  }

  .btn-primary {
    background: #4a90e2;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
  }

  .btn-primary:hover {
    background: #357ab8
  }

  .report-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1100px;
  }

  .report-table th,
  .report-table td {
    border: 1px solid #e6edf7;
    padding: 10px 12px;
    font-size: 13px;
    white-space: nowrap;
    vertical-align: middle;
  }

  .report-table thead th {
    background: #e8f5ff;
    text-align: left;
    font-size: 1.0em;
    font-weight: 800;
    padding: 12px 10px;
    color: #08386b;
  }

  .report-table.concise th,
  .report-table.concise td {
    padding: 6px 8px;
    font-size: 12px;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .report-table.concise td:hover {
    white-space: normal;
    background: #fdfdfd;
  }

  .download-buttons {
    margin-bottom: 12px;
  }

  .download-buttons button {
    background: linear-gradient(135deg, #d4612f 0%, #fa7d50 50%, #f8ac53 100%);
    border: none;
    padding: 8px 16px;
    margin-left: 8px;
    border-radius: 8px;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
  }

  .download-buttons button:hover {
    opacity: 0.9;
  }

  @media (max-width:900px) {
    .section-heading {
      font-size: 1.6em;
      padding: 14px 16px;
    }

    .report-table {
      min-width: 900px;
    }
  }
</style>
<style>
  .blink-red {
    animation: blinkAnim 3s infinite;
    color: #c62828;
    font-weight: 700;
  }

  @keyframes blinkAnim {
    0% {
      opacity: 1;
    }

    50% {
      opacity: 0;
    }

    100% {
      opacity: 1;
    }
  }
</style>
<?php
$sql = "SELECT m.*, md.district_name, dv.division_name FROM marketing m LEFT JOIN master_district md ON m.district_id = md.sno LEFT JOIN master_division dv ON m.division_id = dv.sno WHERE 1=1 and m.is_deleted=0";

$user_type = $_SESSION['user_type'] ?? '';

if ($user_type === 'ar_dr') {
  // Fetch division for this DR user from ar_dr table
  $uid = (int) $_SESSION['user_id'];
  $dr_res = mysqli_query($db, "SELECT master_division.sno as division_sno 
                                  FROM ar_dr 
                                  LEFT JOIN master_division ON ar_dr.division_name = master_division.sno 
                                  WHERE ar_dr.sno = $uid");
  $dr_div_ids = [];
  if ($dr_res) {
    while ($dr_row = mysqli_fetch_assoc($dr_res)) {
      if (!empty($dr_row['division_sno'])) {
        $dr_div_ids[] = (int) $dr_row['division_sno'];
      }
    }
  }
  if (!empty($dr_div_ids)) {
    $sql .= " AND m.division_id IN (" . implode(',', $dr_div_ids) . ")";
  } else {
    $sql .= " AND 1=0"; // no division found, show nothing
  }
} else {
  // Admin or other roles — apply session filters if set
  if (!empty($_SESSION['division_id'])) {
    $div_ids = array_map('intval', (array) $_SESSION['division_id']);
    if ($div_ids)
      $sql .= " AND m.division_id IN (" . implode(',', $div_ids) . ")";
  }
  if (!empty($_SESSION['district_id'])) {
    $dis_ids = array_map('intval', (array) $_SESSION['district_id']);
    if ($dis_ids)
      $sql .= " AND m.district_id IN (" . implode(',', $dis_ids) . ")";
  }
}


if ($land_from_val !== null && $land_to_val !== null) {
  $sql .= " AND (land_area+0) BETWEEN " . (float) $land_from_val . " AND " . (float) $land_to_val;
} elseif ($land_from_val !== null) {
  $sql .= " AND (land_area+0) >= " . (float) $land_from_val;
} elseif ($land_to_val !== null) {
  $sql .= " AND (land_area+0) <= " . (float) $land_to_val;
}
if ($godown_filter !== '') {
  $sql .= " AND godown_suitable = '" . e($db, $godown_filter) . "'";
}

$sql .= " ORDER BY md.district_name, m.sno DESC";
// echo $sql;
$res = mysqli_query($db, $sql);
?>

<div class="card" style="margin-top: 24px;">
  <h3 class="section-heading" style="text-align:center;"> मार्केटिंग रिपोर्ट</h3>

  <!-- FILTERS: Land area range -->
  <div style="margin:12px 0 18px 0; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <form method="get" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
      <label style="font-weight:700;">भूमि क्षेत्र (From)</label>
      <input name="land_area_from" class="form-control" style="width:110px;" value="<?php echo h($land_from); ?>">
      <label style="font-weight:700;">(To)</label>
      <input name="land_area_to" class="form-control" style="width:110px;" value="<?php echo h($land_to); ?>">
      <label style="font-weight:700;">गोदाम उपयुक्त</label>
      <select name="godown_suitable" class="form-control" style="width:150px;">
        <option value="">All</option>
        <option value="हाँ" <?php if ($godown_filter === 'हाँ')
          echo 'selected'; ?>>हाँ</option>
        <option value="नहीं" <?php if ($godown_filter === 'नहीं')
          echo 'selected'; ?>>नहीं</option>
      </select>
      <button class="btn-primary" type="submit">Filter</button>
      <a href="?"
        style="margin-left:8px; padding:8px 10px; display:inline-block; background:#eee; border-radius:5px;">Reset</a>
      <!-- <div style="font-size:12px; color:#666; margin-left:16px;">नोट: दशमलव के लिए "." प्रयोग करें (0.5)</div> -->
    </form>
  </div>

  <div class="download-buttons">
    <button onclick="downloadExcel()">Download Excel</button>
    <!-- <button onclick="downloadPDF()">Download PDF</button> -->
  </div>

  <div class="table-wrap table-responsive">
    <table id="general_stat_table" class="report-table concise">
      <thead>
        <tr>
          <th style="max-width:70px;">S.No.</th>
          <th style="max-width:70px;">Status</th>
          <th>मण्डल</th>
          <th>जनपद</th>
          <th style="max-width:100px;">NCD ID</th>
          <th>समिति का नाम</th>
          <th>सचिव (ADO/ADCO)</th>
          <th>अध्यक्ष</th>
          <th>भूमि क्षेत्रफल</th>
          <th>कब्जा / विवादित</th>
          <th>राजस्व अभिलेख</th>
          <th>भूमि की स्थिति</th>
          <th>स्थान</th>
          <th>गोदाम उपयुक्त</th>
          <th>जनपद रैक दूरी</th>
          <th>पहुंच मार्ग</th>
          <th>व्यवसाय प्रकार</th>
          <th>व्यवसाय स्थिति</th>
          <th>संतुलन पत्र (Balance Year)</th>
          <th>अन्तिम आडिट</th>
          <th>सम्पत्ति प्रकार</th>
          <th>अन्य सम्पत्ति</th>
          <th>परिसमापक</th>
          <th>कब से परिसमापक</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        $last = null;
        if ($res) {
          while ($r = mysqli_fetch_assoc($res)) {
            $creation = trim($r['created_at'] ?? '');
            $edition = trim($r['updated_at'] ?? '');
            $status_label = '';
            $status_color = '';
            $bhumi = isset($r['land_area']) ? floatval($r['land_area']) : 0;
            $is_confirmed = (int) ($r['land_conf'] ?? 0);
            $should_blink = ($bhumi > 0.5 && $is_confirmed == 0);

            if ($creation && !$edition) {
              $status_label = 'New Feeded';
              $status_color = 'green';
            } elseif (!$creation && $edition) {
              $status_label = 'Data Verified';
              $status_color = 'orange';
            } elseif (!$creation && !$edition) {
              $status_label = 'Previous Feeded';
              $status_color = 'blue';
            } elseif ($creation && $edition) {
              $status_label = 'New Feeded';
              $status_color = 'gray';
            }
            ?>
            <tr>
              <td style="white-space:nowrap;"><?= $i++ ?></td>
              <td style="color:<?= $status_color ?>; font-weight:600;"><?= $status_label ?></td>
              <td><?= h($r['division_name'] ?? '') ?></td>
              <td><?= h($r['district_name'] ?? '') ?></td>
              <td><?= h($r['ncd_id'] ?? '') ?></td>
              <td><?= h($r['society_name'] ?? '') ?></td>
              <td><?= h($r['ado_name'] ?? '') ?></td>
              <td><?= h($r['chairmain_name'] ?? $r['adhyaksh_name'] ?? '') ?></td>
              <td id="land_cell_<?php echo $r['sno']; ?>">
                <?php if ($should_blink) { ?>
                  <span class="blink-red">
                    <?= h($r['land_area']) ?>
                  </span>
                  <br>
                  <button onclick="confirmLand(<?php echo $r['sno']; ?>,1)"
                    style="margin-top:4px;padding:3px 6px;font-size:11px;background:#e6208d;color:#fff;border:none;border-radius:4px;">
                    Correct Land Area
                  </button>
                <?php } else { ?>
                  <?= h($r['land_area']) ?>
                  <?php if ($is_confirmed == 1) { ?>
                    <br>
                    <span
                      style="background:#c8e6c9;color:#1b5e20;padding:2px 6px;border-radius:4px;font-size:11px;font-weight:600;">
                      ✔
                    </span>
                  <?php } ?>
                <?php } ?>
              </td>
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
              <td><?= h($r['liquidator_name'] ?? '') ?></td>
              <td><?= h($r['liquidato_from_date'] ?? $r['parisampak_from_date'] ?? '') ?></td>
            </tr>
            <?php
          }
          mysqli_free_result($res);
        } else {
          echo '<tr><td colspan="21" style="text-align:center;">कोई रिकॉर्ड नहीं मिला</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Excel / PDF helpers (same as you added) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<script>
  // Data export helpers (same behaviour as provided)
  function getTableHeaders() {
    const headers = [];
    document.querySelectorAll('#general_stat_table thead th').forEach(th => {
      headers.push((th.innerText || th.textContent || '').trim());
    });
    return headers;
  }

  function getDataTableRows() {
    const out = [];
    document.querySelectorAll('#general_stat_table tbody tr').forEach(tr => {
      // skip group rows
      if (tr.querySelectorAll('th').length) return;
      const r = [];
      tr.querySelectorAll('td').forEach(td => r.push((td.innerText || td.textContent || '').trim()));
      if (r.length) out.push(r);
    });
    return out;
  }

  function downloadExcel() {
    const headers = getTableHeaders();
    const rows = getDataTableRows();

    const aoa = [];
    aoa.push(headers);
    for (let r of rows) {
      const row = [];
      for (let i = 0; i < headers.length; i++) {
        row.push(r[i] !== undefined ? r[i] : '');
      }
      aoa.push(row);
    }
    const ws = XLSX.utils.aoa_to_sheet(aoa);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Report');
    const fname = 'marketing_report_' + new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-') + '.xlsx';
    XLSX.writeFile(wb, fname);
  }

  function downloadPDF() {
    const element = document.getElementById('general_stat_table');
    var opt = {
      margin: 0.2,
      filename: 'marketing_report_' + new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-') + '.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
    };
    html2pdf().set(opt).from(element).save();
  }
</script>

<!-- DataTables init (matching first report behaviour & serial number handling) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function () {
    var t = $('#general_stat_table').DataTable({
      paging: true,
      pageLength: 50,
      lengthMenu: [50, 100, 200, 500],
      searching: true,
      info: true,
      deferRender: true,
      processing: true,
      responsive: false,
      scrollX: true,
      order: [],
      columnDefs: [
        { searchable: false, orderable: false, targets: 0 } // serial number column
      ],
      dom: 'lfrtip'
    });

    t.on('order.dt search.dt page.dt', function () {
      var info = t.page.info();
      var start = info.page * info.length;
      t.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
        cell.innerHTML = start + i + 1;
      });
    }).draw();
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

    xhr.send("id="+id+"&value="+value+"&table=marketing");
}
</script>
<?php
page_footer_start();
page_footer_end();
?>