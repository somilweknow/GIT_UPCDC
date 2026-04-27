<?php
include("scripts/settings.php");
// error_reporting(E_ALL);

page_header_start();
page_header_end();
page_sidebar();

/* Helper */
function h($v) {
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

/* Build division/district filters (apply on master_district) */
$filter_parts = [];
if (!empty($_SESSION['division_id'])) {
  $divs = array_map('intval', (array)$_SESSION['division_id']);
  if ($divs) $filter_parts[] = "md.division_id IN (" . implode(',', $divs) . ")";
}
if (!empty($_SESSION['district_id'])) {
  $dis = array_map('intval', (array)$_SESSION['district_id']);
  if ($dis) $filter_parts[] = "md.sno IN (" . implode(',', $dis) . ")";
}
$filter_sql = '';
if ($filter_parts) {
  $filter_sql = ' AND ' . implode(' AND ', $filter_parts);
}

/* Fetch divisions that actually have filled data */
$sql_divs = "
  SELECT
    dv.sno AS division_sno,
    COALESCE(NULLIF(TRIM(dv.division_name), ''), 'N/A') AS division_name
  FROM master_division dv
  JOIN master_district md ON md.division_id = dv.sno
  JOIN upss u ON u.janpad_name = md.sno
    AND u.is_deleted = 0
    AND u.created_at IS NOT NULL
  {$filter_sql}
  GROUP BY dv.sno, dv.division_name
  ORDER BY dv.division_name
";
$res_divs = mysqli_query($db, $sql_divs);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
.card { background:#fff; border:1px solid #e6eefc; padding:18px; border-radius:8px; margin:20px 0; }
.section-heading { background:#2b6fb3; color:#fff; padding:12px; border-radius:6px; font-weight:800; font-size:1.4em; text-align:center; }
.summary-table { width:100%; border-collapse:collapse; margin-top:12px; min-width:900px; }
.summary-table th, .summary-table td { border:1px solid #e6edf7; padding:8px 10px; font-size:13px; text-align:center; }
.summary-table thead th { background:#e8f5ff; font-weight:800; color:#08386b; padding:12px; }
.division-row { background:#edf3ff; font-weight:bold; }
.total-row { background:#dbe8ff; font-weight:bold; border-top:3px solid #7ea6e0; }
.table-wrap { overflow:auto; }
</style>

<div class="card">
  <div class="section-heading">Consumer Report Summary – Division Wise</div>

  <div class="table-wrap">
    <table id="upss_summary_table" class="summary-table">
      <thead>
        <tr>
          <th>S.No.</th>
          <th>Division</th>
          <th>Filled (Forms)</th>
          <th>Active</th>
          <th>Inactive</th>
          <th>Liquidation</th>
          <th>Total Societies</th>
        </tr>
      </thead>
      <tbody>

<?php
$row_no = 1;

$grand_filled = $grand_active = $grand_inactive = $grand_liquidation = $grand_all = 0;

if ($res_divs && mysqli_num_rows($res_divs) > 0) {

  while ($div = mysqli_fetch_assoc($res_divs)) {

    $div_sno  = (int)$div['division_sno'];
    $div_name = $div['division_name'];

    /* Division aggregation */
    $sql_div_agg = "
      SELECT
        SUM(CASE WHEN u.created_at IS NOT NULL AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS filled_count,
        SUM(CASE WHEN (
              (u.society_status IS NOT NULL AND UPPER(u.society_status) = 'ACTIVE')
              OR u.society_status IN ('Active','1')
            ) AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS active_count,
        SUM(CASE WHEN (
              (u.society_status IS NOT NULL AND UPPER(u.society_status) = 'INACTIVE')
              OR u.society_status IN ('Inactive','0')
            ) AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS inactive_count,
        SUM(CASE WHEN (
              u.liquidation_from_date IS NOT NULL
              OR (u.society_status IS NOT NULL AND UPPER(u.society_status) = 'LIQUIDATION')
              OR u.society_status = 'Liquidation'
            ) AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS liquidation_count,
        COUNT(CASE WHEN u.is_deleted = 0 THEN 1 END) AS all_count
      FROM upss u
      JOIN master_district md ON u.janpad_name = md.sno
      WHERE md.division_id = {$div_sno}
        {$filter_sql}
    ";
    $res_agg = mysqli_query($db, $sql_div_agg);
    $agg = $res_agg ? mysqli_fetch_assoc($res_agg) : [];

    $filled      = (int)($agg['filled_count'] ?? 0);
    $active      = (int)($agg['active_count'] ?? 0);
    $inactive    = (int)($agg['inactive_count'] ?? 0);
    $liquidation = (int)($agg['liquidation_count'] ?? 0);
    $all         = (int)($agg['all_count'] ?? 0);

    /* Grand totals */
    $grand_filled += $filled;
    $grand_active += $active;
    $grand_inactive += $inactive;
    $grand_liquidation += $liquidation;
    $grand_all += $all;

    /* Division row */
    echo '<tr class="division-row">';
    echo '<td>' . $row_no++ . '</td>';
    echo '<td>' . h($div_name) . '</td>';
    echo '<td>' . $filled . '</td>';
    echo '<td>' . $active . '</td>';
    echo '<td>' . $inactive . '</td>';
    echo '<td>' . $liquidation . '</td>';
    echo '<td><b>' . $all . '</b></td>';
    echo '</tr>';
  }

  /* Grand Total row */
  echo '<tr class="total-row">';
  echo '<td colspan="2"><b>Grand Total</b></td>';
  echo '<td><b>' . $grand_filled . '</b></td>';
  echo '<td><b>' . $grand_active . '</b></td>';
  echo '<td><b>' . $grand_inactive . '</b></td>';
  echo '<td><b>' . $grand_liquidation . '</b></td>';
  echo '<td><b>' . $grand_all . '</b></td>';
  echo '</tr>';

} else {
  echo '<tr><td colspan="7">No Details</td></tr>';
}
?>

      </tbody>
    </table>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#upss_summary_table').DataTable({
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
