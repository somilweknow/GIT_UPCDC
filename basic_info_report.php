<?php
include("scripts/settings.php");
page_header_start();
error_reporting(E_ALL);
?>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<title>All Registered Societies</title>
<?php
page_header_end();
page_sidebar();
?>

<style>
  .page-container {
    background-color: #f8f9fa;
    width: 100%;
    overflow-x: hidden;
    padding-bottom: 0;
    margin-bottom: 0;
  }

  .container,
  .containers {
    max-width: 100%;
    width: 100%;
    padding: 0 15px;
    box-sizing: border-box;
  }

  .filter-card {
    background: #fff;
    border-radius: 12px 12px 0 0;
    padding: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    margin-bottom: 0;
    border: 1px solid #e9ecef;
    border-bottom: none;
    overflow: hidden;
  }

  .filter-header {
    background: linear-gradient(135deg, #9368e9 0%, #943bea 100%);
    color: #fff;
    padding: 14px 20px;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .filter-form {
    padding: 12px 16px 4px;
  }

  .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 2px;
    font-size: 12px;
    display: block;
  }

  .form-control,
  .form-select {
    border: 1.5px solid #e9ecef;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 13px;
    transition: border-color .2s;
    width: 100%;
    box-sizing: border-box;
    height: 32px;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #9368e9;
    box-shadow: 0 0 0 3px rgba(147, 104, 233, .18);
    outline: none;
  }

  .col-md-6,
  .col-lg-4 {
    padding: 0 6px;
    margin-bottom: 8px;
    box-sizing: border-box;
  }

  .row {
    margin: 0 -6px;
    display: flex;
    flex-wrap: wrap;
  }

  .filter-btn {
    min-width: 110px;
    padding: 6px 18px;
    font-weight: 600;
    border-radius: 6px;
    font-size: 13px;
    letter-spacing: .3px;
    transition: all .2s;
  }

  .btn-primary {
    background: linear-gradient(135deg, #1D62F0 0%, #23CCEF 100%);
    border: none;
    color: #fff;
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, #1557d1 0%, #1fb3d6 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(29, 98, 240, .35);
  }

  .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    color: #fff;
  }

  .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #3d4449 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(108, 117, 125, .35);
  }

  .table-wrapper {
    background: #fff;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
    border: 1px solid #e9ecef;
    border-top: 2px solid #e0e0e0;
    overflow: hidden;
    margin-top: 0;
  }

  .table-container {
    padding: 12px 16px 16px;
  }

  .table {
    margin-bottom: 0;
    font-size: 12px;
  }

  .table thead th {
    background-color: #f19e03;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 7px 6px;
    border: none;
    text-transform: uppercase;
    letter-spacing: .3px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }

  .table thead th:not(:last-child) {
    border-right: 1px solid rgba(255, 255, 255, .4);
  }

  .table {
    table-layout: fixed;
  }

  .table tbody td {
    padding: 3px 5px;
  }

  .table tbody td {
    border-color: #e9ecef;
    vertical-align: middle;
    text-align: center;
    color: #495057;
    font-size: 12px;
    white-space: nowrap;
  }

  .table tbody tr:hover {
    background-color: #f1f3f5;
  }

  .table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .table tbody tr:nth-child(even):hover {
    background-color: #e9ecef;
  }

  .table th:nth-child(3),
  .table td:nth-child(3) {
    min-width: 200px;
    white-space: normal;
    word-break: break-word;
    max-width: 220px;
  }

  .table-responsive {
    overflow-x: auto;
    border-radius: 4px;
  }

  .pagination-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    margin-top: 14px;
    flex-wrap: wrap;
  }

  .pagination {
    margin: 0;
    gap: 3px;
  }

  .pagination .page-link {
    color: #555;
    border: 1px solid #dde1e7;
    padding: 5px 11px;
    margin: 0;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    line-height: 1.4;
    transition: all .15s;
    background: #fff;
    min-width: 34px;
    text-align: center;
  }

  .pagination .page-link:hover {
    background-color: #f0ebfd;
    border-color: #9368e9;
    color: #9368e9;
  }

  .pagination .page-item.active .page-link {
    background: #9368e9;
    border-color: #9368e9;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(147, 104, 233, .35);
  }

  .pagination .page-item.disabled .page-link {
    color: #bbb;
    background: #f8f9fa;
    border-color: #e9ecef;
    cursor: not-allowed;
  }

  .no-records {
    padding: 30px 20px;
    font-size: 15px;
    font-weight: 600;
    color: #842029;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    margin: 16px 0;
    text-align: center;
  }

  .dataTables_info,
  .dataTables_paginate,
  .dataTables_length {
    display: none !important;
  }

  @media (min-width: 768px) {
    .col-md-6 {
      width: 50%;
      float: left;
    }
  }

  @media (min-width: 992px) {
    .col-lg-4 {
      width: 33.333%;
      float: left;
    }
  }

  @media (max-width: 767px) {

    .col-md-6,
    .col-lg-4 {
      width: 100%;
      float: none;
    }

    .filter-header {
      font-size: 16px;
      padding: 10px 14px;
    }

    .filter-form {
      padding: 10px 12px 4px;
    }

    .filter-btn {
      min-width: 100px;
      padding: 5px 14px;
      font-size: 12px;
    }

    .table-container {
      padding: 10px 10px 14px;
    }
  }
</style>

<div class="page-container" style="padding-bottom:0; margin-bottom:0;">
  <div class="container" style="padding-bottom:0; margin-bottom:0;">
    <div class="filter-card">
      <div class="filter-header">&#128202; All Registered Societies</div>
      <div class="filter-form">
        <form method="GET" action="">
          <div class="row">

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Society Name</label>
              <input type="text" name="society_name" class="form-control" placeholder="Enter society name"
                value="<?= htmlspecialchars($_GET['society_name'] ?? '') ?>">
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Registration Number</label>
              <input type="text" name="registration_number" class="form-control" placeholder="Enter registration number"
                value="<?= htmlspecialchars($_GET['registration_number'] ?? '') ?>">
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Sector of Operation</label>
              <select name="sector" class="form-select">
                <option value="">-- Select --</option>
                <?php
                $sectors = ['Credit', 'Non-Credit'];
                foreach ($sectors as $s) {
                  $sel = (($_GET['sector'] ?? '') === $s) ? 'selected' : '';
                  echo "<option $sel>" . htmlspecialchars($s) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Primary Activity</label>
              <select name="society_type" id="society_type" class="form-select">
                <option value="ALL">ALL</option>
                <?php
                $res = execute_query('SELECT * FROM master_society_type ORDER BY society_type_id');
                while ($r = mysqli_fetch_assoc($res)) {
                  $sel = (($_GET['society_type'] ?? 'ALL') == $r['society_type_id']) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($r['society_type_id']) . '" ' . $sel . '>' . htmlspecialchars($r['society_type_name']) . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Location</label>
              <select name="location" class="form-select">
                <option value="">-- Select --</option>
                <?php
                foreach (['Urban', 'Rural'] as $loc) {
                  $sel = (($_GET['location'] ?? '') === $loc) ? 'selected' : '';
                  echo "<option $sel>" . htmlspecialchars($loc) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">District</label>
              <select name="district_name" id="district_name" class="form-select">
                <option value="ALL">ALL</option>
                <?php
                $result_district = execute_query('SELECT * FROM master_district ORDER BY district_name');
                while ($row_district = mysqli_fetch_assoc($result_district)) {
                  $sel = (($_GET['district_name'] ?? 'ALL') == $row_district['sno']) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($row_district['sno']) . '" ' . $sel . '>' . htmlspecialchars($row_district['district_name']) . '</option>';
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Functional Status</label>
              <select name="functional_status" class="form-select">
                <option value="">-- Select --</option>
                <?php
                foreach (['Functional', 'Non Functional/Dormant', 'Under Liquidation'] as $fs) {
                  $sel = (($_GET['functional_status'] ?? '') === $fs) ? 'selected' : '';
                  echo "<option $sel>" . htmlspecialchars($fs) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">-- All --</option>
                <?php
                foreach (['Approved', 'Pending'] as $st) {
                  $sel = (($_GET['status'] ?? '') === $st) ? 'selected' : '';
                  echo "<option $sel>" . htmlspecialchars($st) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Audit Status</label>
              <select name="audit_status" class="form-select">
                <option value="">-- Select --</option>
                <?php
                foreach (['Yes', 'No'] as $as) {
                  $sel = (($_GET['audit_status'] ?? '') === $as) ? 'selected' : '';
                  echo "<option $sel>" . htmlspecialchars($as) . "</option>";
                }
                ?>
              </select>
            </div>

          </div>

          <div class="mt-2 mb-1 text-center" style="clear:both;">
            <button type="submit" class="btn btn-primary filter-btn">&#128269; Search</button>
            <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-secondary filter-btn" style="margin-left:8px;">&#8635;
              Reset</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php

$query = "SELECT c.*, ml.location_type, d.district_name, b.block_name, s.society_type_name,
    functional_type, audit_type,
    CASE WHEN c.is_profit_making = 1 THEN c.annual_turnover
         WHEN c.is_profit_making = 0 THEN c.annual_loss
         ELSE 'N/A' END AS financial_value
  FROM cooperatives c
  LEFT JOIN master_location ml ON c.location_of_head_quarter = ml.sno
  LEFT JOIN master_society_type s ON c.sector_of_operation = s.society_type_id
  LEFT JOIN master_district d ON d.dist_lgd_code = c.district_code
  LEFT JOIN master_block b ON b.block_lgd_code = c.block_code
  LEFT JOIN master_functional mf ON c.functional_status = mf.sno
  LEFT JOIN master_audit ma ON c.financial_audit = ma.audit_id
  WHERE 1=1";

if (!empty($_GET['society_name'])) {
  $query .= " AND c.cooperative_society_name LIKE '%" . mysqli_real_escape_string($db, $_GET['society_name']) . "%'";
}

if (!empty($_GET['registration_number'])) {
  $query .= " AND c.registration_number LIKE '%" . mysqli_real_escape_string($db, $_GET['registration_number']) . "%'";
}

if (!empty($_GET['sector'])) {
  $query .= " AND c.sector_of_operation = '" . mysqli_real_escape_string($db, $_GET['sector']) . "'";
}

if (!empty($_GET['society_type']) && $_GET['society_type'] !== 'ALL') {
  $query .= " AND c.sector_of_operation = '" . mysqli_real_escape_string($db, $_GET['society_type']) . "'";
}

if (!empty($_GET['location'])) {
  $query .= " AND ml.location_type = '" . mysqli_real_escape_string($db, $_GET['location']) . "'";
}

if (!empty($_GET['district_name']) && $_GET['district_name'] !== 'ALL') {
  $query .= " AND d.sno = '" . mysqli_real_escape_string($db, $_GET['district_name']) . "'";
}

if (!empty($_GET['functional_status'])) {
  $query .= " AND mf.functional_type = '" . mysqli_real_escape_string($db, $_GET['functional_status']) . "'";
}

if (!empty($_GET['status'])) {
  $query .= " AND c.status = '" . mysqli_real_escape_string($db, $_GET['status']) . "'";
}

if (!empty($_GET['audit_status'])) {
  $query .= " AND ma.audit_type = '" . mysqli_real_escape_string($db, $_GET['audit_status']) . "'";
}

$query .= " ORDER BY c.id DESC";

$result = mysqli_query($db, $query);
if (!$result) {
  die("Query Failed: " . mysqli_error($db));
}

$total_results = mysqli_num_rows($result);

include('pagination/paginate.php');

$total_pages = ceil($total_results / $per_page);
$tpages = $total_pages;

$show_page = max(1, (int) ($_GET['page'] ?? 1));
$start = ($show_page - 1) * $per_page;
$end = $start + $per_page;

$params = $_GET;
unset($params['page'], $params['tpages']);
$base_url = $_SERVER['PHP_SELF'] . '?' . http_build_query($params) . '&tpages=' . $tpages;
?>

<div class="containers" style="margin:0; padding:0 15px 20px;">
  <div class="table-wrapper">
    <div class="table-container">

      <?php if ($total_results > 0): ?>
        <p style="font-size:12px; color:#6c757d; margin:0 0 8px;">
          Showing <strong><?= $start + 1 ?></strong>–<strong><?= min($end, $total_results) ?></strong> of
          <strong><?= $total_results ?></strong> societies
        </p>
      <?php endif; ?>

      <div class="table-responsive">
        <table id="societiesTable" class="table table-bordered table-hover text-wrap">
          <thead>
            <tr>
              <th style="width:50px; white-space:normal;">S.No.</th>
              <th style="width:90px; white-space:normal;">NCD ID</th>
              <th style="width:250px; white-space:normal; word-break:break-word;">Society Name</th>
              <th style="width:90px; white-space:normal;">Location</th>
              <th style="width:120px; white-space:normal;">District</th>
              <th style="width:130px; white-space:normal;">Block / ULB</th>
              <th style="width:150px; white-space:normal; word-break:break-word;">Sector</th>
              <th style="width:120px; white-space:normal; word-break:break-word;">Reg. Number</th>
              <th style="width:100px; white-space:normal;">Phone</th>
              <th style="width:180px; white-space:normal; word-break:break-word;">Email</th>
              <th style="width:110px; white-space:normal;">Reg. Date</th>
              <th style="width:140px; white-space:normal;">Functional Status</th>
              <th style="width:90px; white-space:normal;">Members</th>
              <th style="width:110px; white-space:normal;">Audit Status</th>
              <th style="width:130px; white-space:normal;">Last Audit Year</th>
              <th style="width:130px; white-space:normal;">Annual Turnover</th>
              <th style="width:120px; white-space:normal;">Annual Loss</th>
              <th style="width:90px; white-space:normal;">Status</th>
              <th style="width:90px; white-space:normal;">Pin Code</th>
            </tr>
            <tr>
              <?php for ($i = 1; $i <= 19; $i++): ?>
                <th><?= $i ?></th>
              <?php endfor; ?>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($total_results > 0) {
              for ($pgid = $start; $pgid < $end; $pgid++) {
                if ($pgid >= $total_results)
                  break;
                mysqli_data_seek($result, $pgid);
                $row = mysqli_fetch_assoc($result);
                $turnover = !empty($row['annual_turnover']) ? htmlspecialchars($row['annual_turnover']) : 'N/A';
                $loss = !empty($row['annual_loss']) ? htmlspecialchars($row['annual_loss']) : 'N/A';
                ?>
                <tr>
                  <td><?= ($pgid - $start) + 1 ?></td>
                  <td><?= htmlspecialchars($row['cooperative_id']) ?></td>
                  <td><?= htmlspecialchars($row['cooperative_society_name']) ?></td>
                  <td><?= htmlspecialchars($row['location_type']) ?></td>
                  <td><?= ($row['district_name']) ?></td>
                  <td><?= ($row['block_name']) ?></td>
                  <td><?= htmlspecialchars($row['society_type_name']) ?></td>
                  <td><?= ($row['registration_number']) ?></td>
                  <td><?= ($row['mobile']) ?></td>
                  <td><?= ($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['date_registration']) ?></td>
                  <td><?= ($row['functional_type']) ?></td>
                  <td><?= htmlspecialchars($row['members_of_society']) ?></td>
                  <td><?= htmlspecialchars($row['audit_type']) ?></td>
                  <td><?= ($row['audit_complete_year']) ?></td>
                  <td><?= $turnover ?></td>
                  <td><?= $loss ?></td>
                  <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['pincode']) ?></td>
                </tr>
                <?php
              }
            } else {
              echo '<tr><td colspan="19"><div class="no-records">No records found matching your search criteria.</div></td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>

      <?php if ($total_pages > 1): ?>
        <div class="pagination-wrap">
          <ul class="pagination">
            <?php echo paginate($base_url, $show_page, $total_pages); ?>
          </ul>
          <span style="font-size:12px; color:#888; margin-left:10px;">
            Page <?= $show_page ?> of <?= $total_pages ?>
          </span>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="js/bootstrap.bundle.min.js"></script>

<script>
  $(function () {
    $('#societiesTable').DataTable({
      paging: false,
      info: false,
      lengthChange: false,
      searching: false,
      ordering: true,
      order: [],
      columnDefs: [
        { targets: 2, width: 180 },
        { targets: '_all', className: 'text-center' }
      ]
    });

    $('form').on('submit', function () {
      var btn = $(this).find('.btn-primary');
      btn.html('&#9203; Searching&hellip;').prop('disabled', true);
    });

    $('.form-control, .form-select').on('blur', function () {
      $(this).css('border-color', $(this).val().trim() ? '#28a745' : '#e9ecef');
    });

    $('input[name="society_name"]').trigger('focus');

    if (window.location.search.length > 1) {
      setTimeout(function () {
        var tbl = document.querySelector('.table-wrapper');
        if (tbl) tbl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 120);
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.ctrlKey && e.key === 'Enter') {
      document.querySelector('form .btn-primary').click();
    }
  });
</script>

<?php
page_footer_start();
page_footer_end();
?>