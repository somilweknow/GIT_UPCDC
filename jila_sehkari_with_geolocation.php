<?php
include("scripts/settings.php");
// session_start();

page_header_start();
page_header_end();
page_sidebar();

/* Helpers */
function h($v)
{
  return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}
function e($db, $v)
{
  return mysqli_real_escape_string($db, trim((string) ($v ?? '')));
}

/* Status normalization + label */
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
  return [
    'active' => 'सक्रिय',
    'non-active' => 'निष्क्रिय',
    'closed' => 'परिसमापनाधीन',
    'not_applicable' => 'स्थापित नही है'
  ][$code] ?? h($v);
}

/* LAND AREA + GODOWN FILTERS */
$land_from = isset($_GET['land_area_from']) ? trim($_GET['land_area_from']) : '';
$land_to = isset($_GET['land_area_to']) ? trim($_GET['land_area_to']) : '';
$land_from_val = ($land_from === '') ? null : floatval(str_replace(',', '.', $land_from));
$land_to_val = ($land_to === '') ? null : floatval(str_replace(',', '.', $land_to));
$godown_filter = isset($_GET['godown_suitable']) ? trim($_GET['godown_suitable']) : '';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<style>
  /* base card + form styles (kept concise) */
  .card {
    background: #f9f9f9;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .08)
  }

  /* Section heading: made larger (approx 2x) and stronger BG */
  .section-heading {
    background: #2b6fb3;
    /* stronger blue */
    color: #fff;
    padding: 18px 20px;
    /* increased padding */
    border-radius: 6px;
    margin-bottom: 18px;
    font-weight: 800;
    font-size: 2.0em;
    /* ~2x size */
    line-height: 1.05;
    text-transform: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
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

  /* Table container wrapper (visual wrap) */
  .table-container {
    background: linear-gradient(180deg, #ffffff, #fbfdff);
    border: 1px solid #e6eefc;
    padding: 14px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(10, 45, 85, 0.03);
  }

  .table-wrap {
    overflow: auto;
    margin-top: 12px
  }

  /* Table visuals */
  .report-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1100px
  }

  .report-table th,
  .report-table td {
    border: 1px solid #e6edf7;
    padding: 10px 12px;
    font-size: 13px;
    white-space: nowrap;
    vertical-align: middle
  }

  /* Header row — bigger (approx 2x visual weight compared to normal cells) */
  .report-table thead th {
    background: #e8f5ff;
    /* soft bluish background */
    text-align: left;
    font-size: 1.15em;
    /* larger than body cells */
    font-weight: 800;
    padding: 16px 14px;
    /* taller header */
    color: #08386b;
    letter-spacing: 0.2px;
  }

  /* emphasize the top heading row inside the card (if you want the heading above table also larger) */
  .card .top-row-highlight {
    background: #f0f7ff;
    border-radius: 6px;
    padding: 12px 14px;
    margin-bottom: 10px;
    font-weight: 700;
  }

  /* smaller screens: keep heading readable and allow wrapping */
  @media (max-width:900px) {
    .section-heading {
      font-size: 1.6em;
      padding: 14px 16px;
    }

    .report-table thead th {
      font-size: 1em;
      padding: 12px 8px;
    }

    .report-table {
      min-width: 900px
    }
  }

  #map_container {
    height: 280px
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
    transition: 0.3s;
  }

  .download-buttons button:hover {
    opacity: 0.8;
  }
</style>

<!-- ✅ Styles skipped for brevity, keep your existing CSS as-is -->

<div class="card" style="margin-top: 40px;">
  <h3 class="section-heading" style="text-align: center;">जिला सहकारी रिपोर्ट</h3>

  <div style="margin:12px 0 18px 0; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
    <form method="get" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
      <label style="font-weight:700;">भूमि क्षेत्र (From)</label>
      <input name="land_area_from" class="form-control" style="width:110px;" value="<?= h($land_from) ?>">
      <label style="font-weight:700;">(To)</label>
      <input name="land_area_to" class="form-control" style="width:110px;" value="<?= h($land_to) ?>">
      <label style="font-weight:700;">गोदाम उपयुक्त</label>
      <select name="godown_suitable" class="form-control" style="width:150px;">
        <option value="">All</option>
        <option value="हाँ" <?= ($godown_filter === 'हाँ' ? 'selected' : '') ?>>हाँ</option>
        <option value="नहीं" <?= ($godown_filter === 'नहीं' ? 'selected' : '') ?>>नहीं</option>
      </select>
      <button class="btn-primary" type="submit">Filter</button>
      <a href="?"
        style="margin-left:8px; padding:8px 10px; display:inline-block; background:#eee; border-radius:5px;">Reset</a>
    </form>
  </div>

  <div class="download-buttons">
    <button onclick="downloadExcel()">Download Excel</button>
  </div>

  <div class="table-container">
    <div class="table-wrap">
      <table id="general_stat_table" class="report-table">
        <thead>
          <tr>
            <th>S.No.</th>
            <th>Status</th>
            <th>मण्डल</th>
            <th>जनपद</th>
            <th>NCD ID</th>
            <th>क्या समिति सक्रिय है</th>
          <th>Latitude</th>
          <th>Longitude</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $sql = "SELECT j.*, dt.district_name, dv.division_name 
        FROM jila_sehkari j 
        LEFT JOIN master_district dt ON j.janpad_name = dt.sno 
        LEFT JOIN master_division dv ON j.mandal_name = dv.sno 
        WHERE j.latitude IS NOT NULL 
          AND j.latitude != ''";


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

          if ($land_from_val !== null && $land_to_val !== null) {
            $sql .= " AND (j.bhumi_area+0) BETWEEN " . (float) $land_from_val . " AND " . (float) $land_to_val;
          } elseif ($land_from_val !== null) {
            $sql .= " AND (j.bhumi_area+0) >= " . (float) $land_from_val;
          } elseif ($land_to_val !== null) {
            $sql .= " AND (j.bhumi_area+0) <= " . (float) $land_to_val;
          }

          if ($godown_filter !== '') {
            $sql .= " AND TRIM(j.godown_suitable) = '" . e($db, $godown_filter) . "'";
          }

          $sql .= " ORDER BY dv.division_name, dt.district_name, j.sno DESC";
          if ($res = execute_query($sql)) {
            $i = 1;
            while ($row = mysqli_fetch_assoc($res)) {

              $creation = trim($r['created_at'] ?? '');
              $edition = trim($r['updated_at'] ?? '');
              $status_label = '';
              $status_color = '';

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
                <td><?= h($row['division_name'] ?? '') ?></td>
                <td><?= strtoupper($row['district_name'] ?? '') ?></td>
                <td><?= h($row['ncd_id']) ?></td>
              <td><?= h($row['latitude'] ?? '') ?></td>
              <td><?= h($row['longitude'] ?? '') ?></td>
              </tr>
              <?php
            }
            mysqli_free_result($res);
          } else {
            echo '<tr><td colspan="23" style="text-align:center;">कोई रिकॉर्ड नहीं मिला</td></tr>';
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
        {
          searchable: false,
          orderable: false,
          targets: 0 // serial number column
        }
      ],
      dom: 'lfrtip'
    });

    t.on('order.dt search.dt page.dt', function () {
      var info = t.page.info();
      var start = info.start;
      t.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
        cell.innerHTML = start + i + 1;
      });
    }).draw();
  });

  function getTableHeaders() {
    const headers = [];
    document.querySelectorAll('#general_stat_table thead th').forEach(th => {
      headers.push((th.innerText || th.textContent || '').trim());
    });
    return headers;
  }

  function getDataTableRows() {
    var dt = null;
    try {
      dt = $('#general_stat_table').DataTable();
    } catch (e) {
      dt = null;
    }

    if (dt) {
      const rows = dt.rows({ search: 'applied', order: 'applied' }).nodes().toArray();
      const rowData = dt.rows({ search: 'applied', order: 'applied' }).data().toArray();

      const out = [];
      for (let i = 0; i < rowData.length; i++) {
        const node = rows[i];
        const cells = [];
        if (node && node.querySelectorAll) {
          node.querySelectorAll('td').forEach(td => {
            cells.push((td.innerText || td.textContent || '').trim());
          });
        } else {
          if (Array.isArray(rowData[i])) {
            rowData[i].forEach(c => cells.push(String(c)));
          } else if (typeof rowData[i] === 'object' && rowData[i] !== null) {
            Object.values(rowData[i]).forEach(v => cells.push(String(v)));
          } else {
            cells.push(String(rowData[i]));
          }
        }
        out.push(cells);
      }
      return out;
    }

    const out = [];
    document.querySelectorAll('#general_stat_table tbody tr').forEach(tr => {
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
    const fname = 'report_' + new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-') + '.xlsx';
    XLSX.writeFile(wb, fname);
  }

  function downloadPDF() {
    const element = document.getElementById('general_stat_table');
    var opt = {
      margin: 0.2,
      filename: 'report_' + new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-') + '.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
    };
    html2pdf().set(opt).from(element).save();
  }
</script>

<?php
page_footer_start();
page_footer_end();
?>