<?php
include("scripts/settings.php");
page_header_start();
error_reporting(E_ALL);
?>
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="css/bootstrap.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<title>All Registered Societies</title>
<?php
page_header_end();
page_sidebar();
?>

<style>
  .page-container {
    background-color: #f8f9fa;
    min-height: 80vh;
    /* prevent large empty gap below the filters */
    padding: 20px 0;
    width: 100%;
    overflow-x: hidden;
  }

  .container {
    max-width: 100%;
    width: 100%;
    padding: 0 15px;
    box-sizing: border-box;
  }

  .filter-card {
    background: #fff;
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
    overflow: hidden;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
  }

  .filter-header {
    background: linear-gradient(135deg, #9368e9 0%, #943bea 100%);
    color: #fff;
    padding: 18px 25px;
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    border-bottom: none;
    display: flex;
    align-items: center;
  }

  .filter-header::before {
    content: "📊";
    margin-right: 12px;
    font-size: 24px;
  }

  .filter-form {
    padding: 25px;
  }

  /* .page-container {
  margin-bottom: 0 !important;
  padding-bottom: 0 !important;
}

.container {
  margin-bottom: 0 !important;
  padding-bottom: 0 !important;
} */

  .filter-card {
    margin-bottom: 10px !important;
  }

  .containers {
    max-width: 100%;
    width: 100%;
    padding: 0 15px;
    box-sizing: border-box;
    margin-top: 0 !important;
    padding-top: 0 !important;
  }

  /* Form styling */
  .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
    display: block;
    width: 100%;
  }

  .form-control,
  .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: #fff;
    width: 100%;
    box-sizing: border-box;
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 20px;
    width: 100%;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #9368e9;
    box-shadow: 0 0 0 0.2rem rgba(147, 104, 233, 0.25);
    outline: none;
  }

  .form-control::placeholder {
    color: #6c757d;
    font-style: italic;
  }

  /* Button styling */
  .filter-btn {
    min-width: 140px;
    padding: 12px 25px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #1D62F0 0%, #23CCEF 100%);
    border: none;
    color: #fff;
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, #1557d1 0%, #1fb3d6 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(29, 98, 240, 0.4);
  }

  .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    color: #fff;
  }

  .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #3d4449 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
  }

  /* Table wrapper styling */
  .table-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
    overflow: hidden;
    /* margin-top: 50px !important; */
  }

  .table-header {
    background: #43739d;
    color: #fff;
    padding: 18px 25px;
    font-size: 25px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    /* center the Report heading */
  }

  .table-header::before {
    content: "📋";
    margin-right: 12px;
    font-size: 45px;
  }

  .table-container {
    padding: 25px;
  }


  .table {
    margin-bottom: 0;
    font-size: 14px;
  }

  .table thead th {
    background-color: #f19e03ff;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 15px 12px;
    border: none;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }

  /* Subtle separators between header columns */
  .table thead th:not(:last-child) {
    border-right: 1px solid rgba(255, 255, 255, 0.5);
  }

  .table tbody td {
    padding: 15px 12px;
    border-color: #e9ecef;
    vertical-align: middle;
    text-align: center;
    color: #495057;
    font-size: 13px;
    white-space: nowrap;
  }

  .table tbody tr {
    transition: all 0.3s ease;
  }

  .table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
  }

  .table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
  }

  .table tbody tr:nth-child(even):hover {
    background-color: #e9ecef;
  }


  .btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border: none;
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-info:hover {
    background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4);
  }


  .table th:nth-child(3) {
    min-width: 420px;
    white-space: nowrap;
  }

  .table td:nth-child(3) {
    min-width: 420px;
    white-space: normal;
    /* allow wrapping for the name */
    word-break: break-word;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }

  .table td:nth-child(6),
  .table th:nth-child(6) {
    min-width: 180px;
    white-space: nowrap;
  }

  .table td:nth-child(9),
  .table th:nth-child(9),
  .table td:nth-child(10),
  .table th:nth-child(10) {
    min-width: 200px;
    white-space: nowrap;
  }

  .table-responsive {
    overflow-x: auto;
    border-radius: 8px;
  }

  .pagination {
    justify-content: center;
    margin-top: 25px;
    padding: 0;
  }

  .pagination .page-link {
    color: #9368e9;
    border: 2px solid #e9ecef;
    padding: 10px 15px;
    margin: 0 2px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .pagination .page-link:hover {
    background-color: #9368e9;
    border-color: #9368e9;
    color: #fff;
    transform: translateY(-2px);
  }

  .pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #9368e9 0%, #943bea 100%);
    border-color: #9368e9;
    color: #fff;
  }

  .text-center.text-danger {
    padding: 40px 20px;
    font-size: 16px;
    font-weight: 600;
    color: #dc3545 !important;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    margin: 20px 0;
  }

  /* Results count styling */
  /* .results-info {
    background: linear-gradient(135deg, #FFA534 0%, #ff8c00 100%);
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
    font-size: 16px;
} */

  /* Loading animation */
  .loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #9368e9;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }


  .row {
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    box-sizing: border-box;
  }

  .col-md-6,
  .col-lg-4 {
    padding: 0 15px;
    margin-bottom: 0;
    width: 100%;
    box-sizing: border-box;
    float: none;
    display: block;
  }

  /* Clear floats */
  .row::after {
    content: "";
    display: table;
    clear: both;
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


  .form-field-container {
    margin-bottom: 20px;
    width: 100%;
    display: flex;
    flex-direction: column;
  }


  @media (max-width: 768px) {

    .filter-header,
    .table-header {
      font-size: 18px;
      padding: 15px 20px;
    }

    .filter-form,
    .table-container {
      padding: 20px;
    }

    .filter-btn {
      min-width: 120px;
      padding: 10px 20px;
      font-size: 14px;
    }

    .table thead th,
    .table tbody td {
      padding: 10px 8px;
      font-size: 12px;
    }

    .btn-info {
      padding: 6px 12px;
      font-size: 11px;
    }

    .col-md-6,
    .col-lg-4 {
      width: 100% !important;
      padding: 0 10px;
      margin-bottom: 15px;
      float: none;
    }

    .form-control,
    .form-select {
      margin-bottom: 15px;
      padding: 10px 12px;
    }

    .form-field-container {
      margin-bottom: 15px;
    }
  }

  @media (max-width: 576px) {
    .page-container {
      padding: 10px 0;
    }

    .filter-card,
    .table-wrapper {
      margin: 0 10px 20px 10px;
    }

    .filter-form {
      padding: 15px;
    }

    .col-md-6,
    .col-lg-4 {
      padding: 0 5px;
      margin-bottom: 10px;
      float: none;
      width: 100% !important;
    }

    .form-control,
    .form-select {
      margin-bottom: 12px;
      padding: 8px 10px;
      font-size: 13px;
    }

    .form-label {
      font-size: 13px;
      margin-bottom: 5px;
    }

    .form-field-container {
      margin-bottom: 12px;
    }
  }

  .dataTables_info,
  .dataTables_paginate,
  .dataTables_length {
    display: none !important;
  }

  /* ensure our server-side pagination (your .pagination ul) shows below and centered */
  .table-container>.d-flex.justify-content-center,
  .table-container .pagination {
    margin-top: 18px;
  }
</style>

<div class="page-container">
  <div class="container">
    <div class="filter-card">
      <div class="filter-header">All Registered Societies</div>
      <div class="filter-form">
        <form method="GET" action="">
          <div class="row g-3">

            <div class="col-md-6 col-lg-4">
              <label class="form-label">Society Name</label>
              <input type="text" name="society_name" class="form-control" placeholder="Enter Society Name">
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">Registration Number</label>
              <input type="text" name="registration_number" class="form-control"
                placeholder="Enter Registration Number">
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">Sector of Operation</label>
              <select name="sector" class="form-select">
                <option>--Select--</option>
                <option>Credit</option>
                <option>Non-Credit</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">Primary Activity</label>
              <select name="society_type" id="society_type" class="form-control">
                <option value="ALL">ALL</option>
                <?php
                $q = 'SELECT * FROM master_society_type ORDER BY society_type_id';
                $res = execute_query($q);
                while ($r = mysqli_fetch_assoc($res)) {
                  $val = $r['society_type_id'];
                  echo '<option value="' . htmlspecialchars($val) . '">' . htmlspecialchars($r['society_type_name']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">Location</label>
              <select name="location" class="form-select">
                <option>--Select--</option>
                <option>Urban</option>
                <option>Rural</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">District</label>
              <select name="district_name" id="district_name" class="form-control">
                <option value="ALL">ALL</option>
                <?php
                $sql = 'SELECT * FROM master_district';
                $result_district = execute_query($sql);
                while ($row_district = mysqli_fetch_assoc($result_district)) {
                  echo '<option value="' . $row_district['sno'] . '">' . $row_district['district_name'] . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">Functional Status</label>
              <select name="functional_status" class="form-select">
                <option>--Select--</option>
                <option>Functional</option>
                <option>Non Functional/Dormant</option>
                <option>Under Liquidation</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option>--All--</option>
                <option>Approved</option>
                <option>Pending</option>
              </select>
            </div>
            <div class="col-md-6 col-lg-4">
              <label class="form-label">Audit Status</label>
              <select name="audit_status" class="form-select">
                <option>--Select--</option>
                <option>Yes</option>
                <option>No</option>
              </select>
            </div>
          </div>

          <div class="mt-4 text-center">
            <button type="submit" class="btn btn-primary filter-btn">
              <i class="fas fa-search"></i> Search
            </button>
            <button type="button" class="btn btn-secondary filter-btn" onclick="resetForm()">
              <i class="fas fa-redo"></i> Reset
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php

$query = "SELECT c.*, ml.location_type, d.district_name, b.block_name, s.society_type_name, functional_type,audit_type,
  CASE WHEN c.is_profit_making = 1 THEN c.annual_turnover WHEN c.is_profit_making = 0 THEN c.annual_loss ELSE 'N/A' END AS financial_value FROM cooperatives c LEFT JOIN master_location ml ON c.location_of_head_quarter = ml.sno LEFT JOIN master_society_type s ON c.sector_of_operation = s.society_type_id LEFT JOIN master_district d on d.dist_lgd_code = c.district_code LEFT JOIN master_block b on b.block_lgd_code = c.block_code LEFT JOIN master_functional mf ON c.functional_status = mf.sno LEFT JOIN master_audit ma ON c.financial_audit = ma.audit_id WHERE 1=1";

if (!empty($_GET['society_name'])) {
  $query .= " AND c.cooperative_society_name LIKE '%" . mysqli_real_escape_string($db, $_GET['society_name']) . "%'";
}

if (!empty($_GET['registration_number'])) {
  $query .= " AND c.registration_number LIKE '%" . mysqli_real_escape_string($db, $_GET['registration_number']) . "%'";
}

if (!empty($_GET['sector']) && $_GET['sector'] != '--Select--') {
  $query .= " AND c.sector_of_operation = '" . mysqli_real_escape_string($db, $_GET['sector']) . "'";
}

if (!empty($_GET['society_type']) && $_GET['society_type'] != 'ALL') {
  $query .= " AND c.sector_of_operation = '" . mysqli_real_escape_string($db, $_GET['society_type']) . "'";
}

if (!empty($_GET['location']) && $_GET['location'] != '--Select--') {
  $query .= " AND ml.location_type = '" . mysqli_real_escape_string($db, $_GET['location']) . "'";
}

if (!empty($_GET['district_name']) && $_GET['district_name'] != 'ALL') {
  $query .= " AND d.sno = '" . mysqli_real_escape_string($db, $_GET['district_name']) . "'";
}

if (!empty($_GET['functional_status']) && $_GET['functional_status'] != '--Select--') {
  $query .= " AND mf.functional_type = '" . mysqli_real_escape_string($db, $_GET['functional_status']) . "'";
}

if (!empty($_GET['status']) && $_GET['status'] != '--All--') {
  $query .= " AND c.status = '" . mysqli_real_escape_string($db, $_GET['status']) . "'";
}

if (!empty($_GET['audit_status']) && $_GET['audit_status'] != '--Select--') {
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

$show_page = isset($_GET['page']) ? $_GET['page'] : 1;
if ($show_page <= 0)
  $show_page = 1;
$start = ($show_page - 1) * $per_page;
$end = $start + $per_page;
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages;
?>


<div class="containers">
  <div class="table-wrapper">
    <div class="table-header">Report</div>
    <div class="table-container">
      <?php if ($total_results > 0): ?>
        <!-- <div class="results-info">
          <i class="fas fa-chart-bar"></i> Total Results: <?php echo $total_results; ?> Societies Found
        </div> -->
      <?php endif; ?>
      <div class="table-responsive">
        <table id="societiesTable" class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>S.No.</th>
              <th>NCD ID</th>
              <th>Cooperative Society Name</th>
              <th>Location</th>
              <th>District</th>
              <th>Block/ULB</th>
              <th>Sector</th>
              <th>Registration Number</th>
              <th>Phone Number</th>
              <th>E-mail</th>
              <th>Registration Date</th>
              <th>Functional Status</th>
              <th>Number Of Members</th>
              <th>Audit Status</th>
              <th>Last Audit Year</th>
              <th>Profit</th>
              <th>Loss</th>
              <th>Status</th>
              <th>Pin Code</th>
              <!-- <th>Action</th> -->
            </tr>
          </thead>
          <tbody>
            <?php
            if ($total_results > 0) {
              for ($pgid = $start; $pgid < $end; $pgid++) {
                if ($pgid == $total_results)
                  break;
                mysqli_data_seek($result, $pgid);
                $row = mysqli_fetch_assoc($result);

                $turnover = !empty($row['annual_turnover']) ? $row['annual_turnover'] : 'N/A';
                $loss = !empty($row['annual_loss']) ? $row['annual_loss'] : 'N/A';

                echo "<tr>
            <td>" . (($pgid - $start) + 1) . "</td>
            <td>{$row['cooperative_id']}</td>
            <td>{$row['cooperative_society_name']}</td>
            <td>{$row['location_type']}</td>
            <td>{$row['district_name']}</td>
            <td>{$row['block_name']}</td>
            <td>{$row['society_type_name']}</td>
            <td>{$row['registration_number']}</td>
            <td>{$row['mobile']}</td>
            <td>{$row['email']}</td>
            <td>{$row['date_registration']}</td>
            <td>{$row['functional_type']}</td>
            <td>{$row['members_of_society']}</td>
            <td>{$row['audit_type']}</td>
            <td>{$row['audit_complete_year']}</td>
            <td>{$turnover}</td>
            <td>{$loss}</td>
            <td></td>
            <td>{$row['pincode']}</td>
            
        </tr>";
              }
            } else {
              echo "<tr><td colspan='20' class='text-center text-danger'>No records found!</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

      <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center">
          <ul class="pagination">
            <?php echo paginate($reload, $show_page, $total_pages); ?>
          </ul>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<script>

  document.addEventListener('DOMContentLoaded', function () {

    const searchForm = document.querySelector('form');
    const searchBtn = document.querySelector('.btn-primary');

    if (searchForm && searchBtn) {
      searchForm.addEventListener('submit', function () {
        searchBtn.innerHTML = '<span class="loading"></span> Searching...';
        searchBtn.disabled = true;
      });
    }

    const formControls = document.querySelectorAll('.form-control, .form-select');
    formControls.forEach(function (control) {
      control.addEventListener('blur', function () {
        if (this.value.trim() !== '') {
          this.style.borderColor = '#28a745';
        } else {
          this.style.borderColor = '#e9ecef';
        }
      });
    });


    const firstInput = document.querySelector('input[name="society_name"]');
    if (firstInput) {
      firstInput.focus();
    }


    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function (tooltip) {
      new bootstrap.Tooltip(tooltip);
    });


    if (window.location.search.includes('society_name') ||
      window.location.search.includes('registration_number') ||
      window.location.search.includes('district_name')) {
      setTimeout(function () {
        document.querySelector('.table-wrapper').scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }, 100);
    }
  });


  function resetForm() {
    const form = document.querySelector('form');
    if (form) {
      form.reset();

      const formControls = document.querySelectorAll('.form-control, .form-select');
      formControls.forEach(function (control) {
        control.style.borderColor = '#e9ecef';
      });

      const firstInput = document.querySelector('input[name="society_name"]');
      if (firstInput) {
        firstInput.focus();
      }
    }
  }

  document.addEventListener('keydown', function (e) {

    if (e.ctrlKey && e.key === 'Enter') {
      const searchBtn = document.querySelector('.btn-primary');
      if (searchBtn) {
        searchBtn.click();
      }
    }

    if (e.key === 'Escape') {
      resetForm();
    }
  });

</script>
<!-- jQuery and DataTables -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(function () {
    $('#societiesTable').DataTable({
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      ordering: true,
      order: [],
      searching: true,
      columnDefs: [
        { targets: 2, width: 420 },
        { targets: '_all', className: 'align-left text-left' }
      ]
    });
  });
</script>
<script>
  $(function () {
    if (!$.fn.DataTable.isDataTable('#societiesTable')) {
      $('#societiesTable').DataTable({
        paging: false,
        info: false,
        lengthChange: false,
        searching: false,
        ordering: true,
        columnDefs: [
          { targets: 2, width: 420 },
          { targets: '_all', className: 'align-left text-left' }
        ]
      });
    }
  });
</script>
<script src="js/bootstrap.bundle.min.js"></script>

<?php
page_footer_start();
page_footer_end();
?>