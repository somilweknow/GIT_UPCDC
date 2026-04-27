<?php
include("scripts/settings.php");
// error_reporting(E_ALL);

page_header_start();
page_header_end();
page_sidebar();

/* Helpers */
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

/* Build division/district filters (apply to md - master_district) */
$filter_parts = [];
if (!empty($_SESSION['division_id'])) {
  $divs = array_map('intval', (array)$_SESSION['division_id']);
  if (!empty($divs)) $filter_parts[] = "md.division_id IN (" . implode(',', $divs) . ")";
}
if (!empty($_SESSION['district_id'])) {
  $dis = array_map('intval', (array)$_SESSION['district_id']);
  if (!empty($dis)) $filter_parts[] = "md.sno IN (" . implode(',', $dis) . ")";
}
$filter_sql = '';
if (!empty($filter_parts)) {
  $filter_sql = ' AND ' . implode(' AND ', $filter_parts);
}

/* Fetch divisions (only those that have at least one filled society according to our criteria) */
$sql_divs = "
  SELECT
    dv.sno AS division_sno,
    COALESCE(NULLIF(TRIM(dv.division_name), ''), 'N/A') AS division_name
  FROM master_division dv
  JOIN master_district md ON md.division_id = dv.sno
  JOIN upss u ON u.janpad_name = md.sno AND u.is_deleted = 0 AND u.created_at IS NOT NULL
  WHERE 1=1
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
  .summary-table th, .summary-table td { border:1px solid #e6edf7; padding:8px 10px; font-size:13px; text-align:left; vertical-align:middle; }
  .summary-table thead th { background:#e8f5ff; font-weight:800; color:#08386b; padding:12px; text-align:center; }
  .division-row { background:#f3f8ff; font-weight:800; color:#003366; }
  .district-row { background:#ffffff; }
  .division-total-row { background:#edf3ff; font-weight:bold; border-top:2px solid #9bbbe6; }
  .total-row { background:#dbe8ff; font-weight:bold; border-top:3px solid #7ea6e0; }
  .table-wrap { overflow:auto; }
  td.center { text-align:center; }
</style>

<div class="card">
  <div class="section-heading">Consumer Report Summary</div>

  <div class="table-wrap" style="margin-top:12px;">
    <table id="upss_summary_table" class="summary-table">
      <thead>
        <tr>
          <th>S.No.</th>
          <th>District / Division</th>
          <th>Filled (forms)</th>
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
            $div_sno = intval($div['division_sno']);
            $div_name = $div['division_name'];

            // Output division header row
            echo '<tr class="division-row"><td colspan="7">Division: ' . h($div_name) . '</td></tr>';

            // Reset division totals
            $div_filled = $div_active = $div_inactive = $div_liquidation = $div_all = 0;

            // Per-district aggregation - only include districts that have at least one filled record
            $sql_dists = "
              SELECT
                md.sno AS district_sno,
                md.district_name,
                SUM(CASE WHEN u.created_at IS NOT NULL AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS filled_count,
                SUM(CASE WHEN ( (u.society_status IS NOT NULL AND UPPER(u.society_status) = 'ACTIVE') OR u.society_status IN ('Active','1') ) AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS active_count,
                SUM(CASE WHEN ( (u.society_status IS NOT NULL AND UPPER(u.society_status) = 'INACTIVE') OR u.society_status IN ('Inactive','0') ) AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS inactive_count,
                SUM(CASE WHEN (u.liquidation_from_date IS NOT NULL OR (u.society_status IS NOT NULL AND UPPER(u.society_status) = 'LIQUIDATION') OR u.society_status = 'Liquidation') AND u.is_deleted = 0 THEN 1 ELSE 0 END) AS liquidation_count,
                COUNT(CASE WHEN u.is_deleted = 0 THEN 1 END) AS all_count
              FROM upss u
              JOIN master_district md ON u.janpad_name = md.sno
              WHERE md.division_id = {$div_sno}
                {$filter_sql}
              GROUP BY md.sno, md.district_name
              HAVING SUM(CASE WHEN u.created_at IS NOT NULL AND u.is_deleted = 0 THEN 1 ELSE 0 END) > 0
              ORDER BY md.district_name
            ";
            $res_dists = mysqli_query($db, $sql_dists);

            if ($res_dists && mysqli_num_rows($res_dists) > 0) {
              while ($dist = mysqli_fetch_assoc($res_dists)) {
                $filled = (int)$dist['filled_count'];
                $active = (int)$dist['active_count'];
                $inactive = (int)$dist['inactive_count'];
                $liquidation = (int)$dist['liquidation_count'];
                $all = (int)$dist['all_count'];

                // Accumulate division totals
                $div_filled += $filled;
                $div_active += $active;
                $div_inactive += $inactive;
                $div_liquidation += $liquidation;
                $div_all += $all;

                // Accumulate grand totals
                $grand_filled += $filled;
                $grand_active += $active;
                $grand_inactive += $inactive;
                $grand_liquidation += $liquidation;
                $grand_all += $all;

                echo '<tr class="district-row">';
                echo '<td class="center">' . $row_no++ . '</td>';
                echo '<td>' . h($dist['district_name']) . '</td>';
                echo '<td class="center">' . $filled . '</td>';
                echo '<td class="center">' . $active . '</td>';
                echo '<td class="center">' . $inactive . '</td>';
                echo '<td class="center">' . $liquidation . '</td>';
                echo '<td class="center" style="font-weight:bold;">' . $all . '</td>';
                echo '</tr>';
              }

              // Division total row
              echo '<tr class="division-total-row">';
              echo '<td colspan="2" style="text-align:right;"><b>Division Total (' . h($div_name) . ')</b></td>';
              echo '<td class="center"><b>' . $div_filled . '</b></td>';
              echo '<td class="center"><b>' . $div_active . '</b></td>';
              echo '<td class="center"><b>' . $div_inactive . '</b></td>';
              echo '<td class="center"><b>' . $div_liquidation . '</b></td>';
              echo '<td class="center"><b>' . $div_all . '</b></td>';
              echo '</tr>';
            } else {
              // If no districts with filled data under this division (rare because div selection is based on join), skip or show message row.
              echo '<tr><td colspan="7" style="text-align:center;">कोई जिला उपलब्ध नहीं</td></tr>';
            }
          }

          // Grand total row
          echo '<tr class="total-row">';
          echo '<td colspan="2" style="text-align:right;"><b>Grand Total</b></td>';
          echo '<td class="center"><b>' . $grand_filled . '</b></td>';
          echo '<td class="center"><b>' . $grand_active . '</b></td>';
          echo '<td class="center"><b>' . $grand_inactive . '</b></td>';
          echo '<td class="center"><b>' . $grand_liquidation . '</b></td>';
          echo '<td class="center"><b>' . $grand_all . '</b></td>';
          echo '</tr>';
        } else {
          echo '<tr><td colspan="7" style="text-align:center;">No Details</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
$(document).ready(function() {
  var t = $('#upss_summary_table').DataTable({
    paging: true,
    pageLength: 25,
    lengthMenu: [10, 25, 50, 100],
    searching: true,
    info: true,
    deferRender: true,
    processing: true,
    scrollX: true,
    order: [[1, 'asc']],
    columnDefs: [{ searchable: false, orderable: false, targets: 0 }],
    dom: 'lfrtip'
  });

  // Re-number S.No for only district rows (keep division/total rows unchanged)
  t.on('draw', function() {
    var idx = 1;
    $('#upss_summary_table tbody tr.district-row').each(function() {
      $(this).find('td:first').text(idx++);
    });
  }).draw();
});
</script>

<?php
page_footer_start();
page_footer_end();
?>
