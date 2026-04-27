<?php
include("scripts/settings.php");
error_reporting(E_ALL);

page_header_start();
page_header_end();
page_sidebar();

/* Helper */
function h($s) {
  return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
}

/* District filter (session based) */
$session_district_clause = '';
if (!empty($_SESSION['district_id'])) {
  $districts = array_map('intval', (array)$_SESSION['district_id']);
  if ($districts) {
    $session_district_clause = " AND md.sno IN (" . implode(',', $districts) . ")";
  }
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
  <div class="section-heading">Block Union Summary (Division-Wise)</div>

  <div class="table-wrap">
    <table id="summary_table" class="summary-table">
      <thead>
        <tr>
          <th>S.No.</th>
          <th>Division</th>
          <th>Initial Total</th>
          <th>Societies Filled</th>
          <th>Active</th>
          <th>Inactive</th>
          <th>Liquidation</th>
          <th>Rest Societies <br><small>[Only (-) values]</small></th>
        </tr>
      </thead>
      <tbody>

<?php
$row_no = 1;

$grand_initial = $grand_filled = 0;
$grand_active = $grand_inactive = $grand_liquidation = 0;
$grand_rest_minus = 0;

if ($res_divs && mysqli_num_rows($res_divs) > 0) {
  while ($div = mysqli_fetch_assoc($res_divs)) {

    $div_sno  = (int)$div['division_sno'];
    $div_name = $div['division_name'];

    /* Division totals */
    $div_initial = $div_filled = 0;
    $div_active = $div_inactive = $div_liquidation = 0;
    $div_rest_minus = 0;

    /* Fetch districts under division */
    $sql_districts = "
      SELECT md.sno, md.block_union
      FROM master_district md
      WHERE md.division_id = {$div_sno}
        AND md.sno != 28
        {$session_district_clause}
    ";
    $res_dists = mysqli_query($db, $sql_districts);

    if ($res_dists) {
      while ($dist = mysqli_fetch_assoc($res_dists)) {

        $district_sno = (int)$dist['sno'];
        $initial_total = (int)($dist['block_union'] ?? 0);

        $sql_counts = "
          SELECT
            COUNT(*) AS total_count,
            SUM(CASE WHEN LOWER(TRIM(row_status)) IN ('सक्रिय','active','सक्रिया') THEN 1 ELSE 0 END) AS active_count,
            SUM(CASE WHEN LOWER(TRIM(row_status)) IN ('निष्क्रिय','non-active','non active','non_active') THEN 1 ELSE 0 END) AS inactive_count,
            SUM(CASE WHEN LOWER(TRIM(row_status)) IN ('परिसमापनाधीन','परिसमापन','closed') THEN 1 ELSE 0 END) AS liquidation_count
          FROM block_union
          WHERE janpad_name = {$district_sno}
        ";
        $res_cnt = mysqli_query($db, $sql_counts);
        $cnt = $res_cnt ? mysqli_fetch_assoc($res_cnt) : [];

        $filled = (int)($cnt['total_count'] ?? 0);
        $active = (int)($cnt['active_count'] ?? 0);
        $inactive = (int)($cnt['inactive_count'] ?? 0);
        $liquidation = (int)($cnt['liquidation_count'] ?? 0);

        $rest = $initial_total - $filled;

        $div_initial += $initial_total;
        $div_filled += $filled;
        $div_active += $active;
        $div_inactive += $inactive;
        $div_liquidation += $liquidation;

        if ($rest > 0) {
          $div_rest_minus += $rest;
        }
      }
    }

    /* Accumulate grand totals */
    $grand_initial += $div_initial;
    $grand_filled += $div_filled;
    $grand_active += $div_active;
    $grand_inactive += $div_inactive;
    $grand_liquidation += $div_liquidation;
    $grand_rest_minus += $div_rest_minus;

    /* Division row */
    echo '<tr class="division-row">';
    echo '<td>' . $row_no++ . '</td>';
    echo '<td>' . h($div_name) . '</td>';
    echo '<td>' . $div_initial . '</td>';
    echo '<td>' . $div_filled . '</td>';
    echo '<td>' . $div_active . '</td>';
    echo '<td>' . $div_inactive . '</td>';
    echo '<td>' . $div_liquidation . '</td>';
    echo '<td>-' . abs($div_rest_minus) . '</td>';
    echo '</tr>';
  }

  /* Grand Total */
  echo '<tr class="total-row">';
  echo '<td colspan="2"><b>Grand Total</b></td>';
  echo '<td><b>' . $grand_initial . '</b></td>';
  echo '<td><b>' . $grand_filled . '</b></td>';
  echo '<td><b>' . $grand_active . '</b></td>';
  echo '<td><b>' . $grand_inactive . '</b></td>';
  echo '<td><b>' . $grand_liquidation . '</b></td>';
  echo '<td><b>-' . abs($grand_rest_minus) . '</b></td>';
  echo '</tr>';

} else {
  echo '<tr><td colspan="8">कोई रिकॉर्ड नहीं मिला</td></tr>';
}
?>

      </tbody>
    </table>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#summary_table').DataTable({
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
