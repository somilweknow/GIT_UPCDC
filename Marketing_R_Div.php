<?php
include("scripts/settings.php");
error_reporting(E_ALL);

page_header_start();
page_header_end();
page_sidebar();

/* Helper */
function h($v) {
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

/* Apply division/district filters */
$where = " WHERE 1=1 ";
if (!empty($_SESSION['division_id'])) {
  $divs = array_map('intval', (array)$_SESSION['division_id']);
  if ($divs) $where .= " AND j.division_id IN (" . implode(',', $divs) . ")";
}
if (!empty($_SESSION['district_id'])) {
  $dis = array_map('intval', (array)$_SESSION['district_id']);
  if ($dis) $where .= " AND j.district_id IN (" . implode(',', $dis) . ")";
}

/* Fetch divisions */
$sql_divs = "
  SELECT dv.sno AS division_sno,
         COALESCE(NULLIF(TRIM(dv.division_name), ''), 'N/A') AS division_name
  FROM master_division dv
  ORDER BY dv.division_name
";
$res_divs = mysqli_query($db, $sql_divs);
?>

<style>
.card { background:#fff; border:1px solid #e6eefc; padding:18px; border-radius:8px; margin:20px 0; }
.section-heading { background:#2b6fb3; color:#fff; padding:12px; border-radius:6px; font-weight:800; font-size:1.4em; text-align:center; }
.summary-table { width:100%; border-collapse:collapse; margin-top:12px; min-width:1000px; }
.summary-table th, .summary-table td { border:1px solid #e6edf7; padding:8px 10px; font-size:13px; text-align:center; }
.summary-table thead th { background:#e8f5ff; font-weight:800; color:#08386b; padding:12px; }
.division-row { background:#edf3ff; font-weight:bold; }
.total-row { background:#dbe8ff; font-weight:bold; border-top:3px solid #7ea6e0; }
.table-wrap { overflow:auto; }
</style>

<div class="card">
  <div class="section-heading">Marketing Report – Division Wise Summary</div>

  <div class="table-wrap">
    <table id="marketing_summary_table" class="summary-table">
      <thead>
        <tr>
          <th>S.No.</th>
          <th>Division</th>
          <th>Pre-Feeded</th>
          <th>New Feeded</th>
          <th>Total</th>
          <th>Pre-Feeded Verified</th>
          <th>Pre-Feeded Not Verified</th>
        </tr>
      </thead>
      <tbody>

<?php
$row_no = 1;

$grand_prefeed = $grand_newfeed = $grand_total = 0;
$grand_prefeed_verified = $grand_prefeed_not_verified = 0;

if ($res_divs && mysqli_num_rows($res_divs) > 0) {

  while ($div = mysqli_fetch_assoc($res_divs)) {

    $div_sno  = (int)$div['division_sno'];
    $div_name = $div['division_name'];

    /* Division totals */
    $div_prefeed = $div_newfeed = $div_total = 0;
    $div_prefeed_verified = $div_prefeed_not_verified = 0;

    /* Aggregate division data */
    $sql_division_agg = "
      SELECT
        SUM(CASE WHEN j.created_at IS NULL THEN 1 ELSE 0 END) AS prefeed_count,
        SUM(CASE WHEN j.created_at IS NOT NULL THEN 1 ELSE 0 END) AS newfeed_count,
        SUM(CASE WHEN j.updated_at IS NOT NULL AND j.created_at IS NULL THEN 1 ELSE 0 END) AS prefeed_verified_count,
        SUM(CASE WHEN j.created_at IS NULL AND j.updated_at IS NULL THEN 1 ELSE 0 END) AS prefeed_not_verified_count,
        COUNT(*) AS total_count
      FROM marketing j
      LEFT JOIN master_district md ON j.district_id = md.sno
      WHERE md.division_id = {$div_sno}
    ";
    $res_agg = mysqli_query($db, $sql_division_agg);
    $agg = $res_agg ? mysqli_fetch_assoc($res_agg) : [];

    $div_prefeed = (int)($agg['prefeed_count'] ?? 0);
    $div_newfeed = (int)($agg['newfeed_count'] ?? 0);
    $div_total   = (int)($agg['total_count'] ?? 0);
    $div_prefeed_verified = (int)($agg['prefeed_verified_count'] ?? 0);
    $div_prefeed_not_verified = (int)($agg['prefeed_not_verified_count'] ?? 0);

    /* Grand totals */
    $grand_prefeed += $div_prefeed;
    $grand_newfeed += $div_newfeed;
    $grand_total += $div_total;
    $grand_prefeed_verified += $div_prefeed_verified;
    $grand_prefeed_not_verified += $div_prefeed_not_verified;

    /* Division row */
    echo '<tr class="division-row">';
    echo '<td>' . $row_no++ . '</td>';
    echo '<td>' . h($div_name) . '</td>';
    echo '<td>' . $div_prefeed . '</td>';
    echo '<td>' . $div_newfeed . '</td>';
    echo '<td><b>' . $div_total . '</b></td>';
    echo '<td>' . $div_prefeed_verified . '</td>';
    echo '<td>' . $div_prefeed_not_verified . '</td>';
    echo '</tr>';
  }

  /* Grand total row */
  echo '<tr class="total-row">';
  echo '<td colspan="2"><b>Grand Total</b></td>';
  echo '<td><b>' . $grand_prefeed . '</b></td>';
  echo '<td><b>' . $grand_newfeed . '</b></td>';
  echo '<td><b>' . $grand_total . '</b></td>';
  echo '<td><b>' . $grand_prefeed_verified . '</b></td>';
  echo '<td><b>' . $grand_prefeed_not_verified . '</b></td>';
  echo '</tr>';

} else {
  echo '<tr><td colspan="7">कोई रिकॉर्ड नहीं मिला</td></tr>';
}
?>

      </tbody>
    </table>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#marketing_summary_table').DataTable({
    paging: true,
    pageLength: 25,
    lengthMenu: [10, 25, 50, 100],
    searching: true,
    info: true,
    scrollX: true,
    ordering: true,
    columnDefs: [
      { orderable: false, searchable: false, targets: 0 }
    ]
  });
});
</script>

<?php
page_footer_start();
page_footer_end();
?>
