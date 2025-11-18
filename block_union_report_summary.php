<?php
include("scripts/settings.php");
error_reporting(E_ALL);

page_header_start();
page_header_end();
page_sidebar();

/* Helpers */
function h($s) { return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }

/* District filter */
$session_district_clause = '';
$session_districts = [];
if (!empty($_SESSION['district_id'])) {
  $session_districts = array_map('intval', (array)$_SESSION['district_id']);
  if (!empty($session_districts)) {
    $session_district_clause = " AND md.sno IN (" . implode(',', $session_districts) . ")";
  }
}

/* Fetch divisions */
$sql_divs = "SELECT dv.sno AS division_sno, 
                    COALESCE(NULLIF(TRIM(dv.division_name), ''), 'N/A') AS division_name
             FROM master_division dv 
             ORDER BY dv.division_name";
$res_divs = mysqli_query($db, $sql_divs);
?>

<style>
  .card { background:#fff; border:1px solid #e6eefc; padding:18px; border-radius:8px; margin:20px 0; }
  .section-heading { background:#2b6fb3; color:#fff; padding:12px; border-radius:6px; font-weight:800; font-size:1.4em; text-align:center; }
  .summary-table { width:100%; border-collapse:collapse; margin-top:12px; min-width:1000px; }
  .summary-table th, .summary-table td { border:1px solid #e6edf7; padding:8px 10px; font-size:13px; text-align:left; vertical-align:middle; }
  .summary-table thead th { background:#e8f5ff; font-weight:800; color:#08386b; padding:12px; text-align:center; }
  .division-row { background:#f3f8ff; font-weight:800; color:#003366; }
  .district-row { background:#ffffff; }
  .division-total-row { background:#edf3ff; font-weight:bold; border-top:2px solid #9bbbe6; }
  .total-row { background:#dbe8ff; font-weight:bold; border-top:3px solid #7ea6e0; }
  .table-wrap { overflow:auto; }
</style>

<div class="card">
  <div class="section-heading">Block Union Summary</div>

  <div class="table-wrap" style="margin-top:12px;">
    <table id="summary_table" class="summary-table">
      <thead>
        <tr>
          <th>S.No.</th>
          <th>District / Division</th>
          <th>Initial Total</th>
          <th>Societies Filled</th>
          <th>Active</th>
          <th>Inactive</th>
          <th>Liquidation</th>
          <th>Rest Societies <br><small>[Only added (-) values]</small></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $row_no = 1;
        $grand_initial = $grand_filled = $grand_active = $grand_inactive = $grand_liquidation = $grand_rest_minus = 0;

        if ($res_divs && mysqli_num_rows($res_divs) > 0) {
          while ($div = mysqli_fetch_assoc($res_divs)) {
            $div_sno = intval($div['division_sno']);
            $div_name = $div['division_name'];

            echo '<tr class="division-row"><td colspan="8">Division: ' . h($div_name) . '</td></tr>';

            // Division totals initialization
            $div_initial = $div_filled = $div_active = $div_inactive = $div_liquidation = $div_rest_minus = 0;

            // Districts under this division
            $sql_districts = "SELECT sno AS district_sno, district_name, block_union
                              FROM master_district
                              WHERE division_id = {$div_sno} AND sno != 28 {$session_district_clause}
                              ORDER BY district_name";
            $res_dists = mysqli_query($db, $sql_districts);

            if ($res_dists && mysqli_num_rows($res_dists) > 0) {
              while ($dist = mysqli_fetch_assoc($res_dists)) {
                $dist_sno = intval($dist['district_sno']);
                $dist_name = $dist['district_name'];
                $initial_total = (int)($dist['block_union'] ?? 0);

                // Societies filled & status count
                $sql_dist_agg = "
                  SELECT
                    COUNT(*) AS total_count,
                    SUM(CASE WHEN LOWER(TRIM(row_status)) IN ('सक्रिय','active','सक्रिया') THEN 1 ELSE 0 END) AS active_count,
                    SUM(CASE WHEN LOWER(TRIM(row_status)) IN ('निष्क्रिय','non-active','non active','non_active') THEN 1 ELSE 0 END) AS inactive_count,
                    SUM(CASE WHEN LOWER(TRIM(row_status)) IN ('परिसमापनाधीन','परिसमापन','closed') THEN 1 ELSE 0 END) AS liquidation_count
                  FROM block_union WHERE janpad_name = {$dist_sno}";
                $res_dist_agg = mysqli_query($db, $sql_dist_agg);
                $dist_counts = $res_dist_agg ? mysqli_fetch_assoc($res_dist_agg) : null;

                $filled_total = (int)($dist_counts['total_count'] ?? 0);
                $active_total = (int)($dist_counts['active_count'] ?? 0);
                $inactive_total = (int)($dist_counts['inactive_count'] ?? 0);
                $liquidation_total = (int)($dist_counts['liquidation_count'] ?? 0);
                $rest_societies = $initial_total - $filled_total;
                $sign = ($rest_societies >= 0) ? '-' : '+';

                // Accumulate division totals
                $div_initial += $initial_total;
                $div_filled += $filled_total;
                $div_active += $active_total;
                $div_inactive += $inactive_total;
                $div_liquidation += $liquidation_total;

                // count only negative ("-") rest values
                if ($rest_societies > 0) {
                  $div_rest_minus += $rest_societies;
                  $grand_rest_minus += $rest_societies;
                }

                // Accumulate grand totals
                $grand_initial += $initial_total;
                $grand_filled += $filled_total;
                $grand_active += $active_total;
                $grand_inactive += $inactive_total;
                $grand_liquidation += $liquidation_total;

                // District Row
                echo '<tr class="district-row">';
                echo '<td style="text-align:center;">' . $row_no++ . '</td>';
                echo '<td>' . h($dist_name) . '</td>';
                echo '<td style="text-align:center; font-weight:bold;">' . $initial_total . '</td>';
                echo '<td style="text-align:center; font-weight:bold;">' . $filled_total . '</td>';
                echo '<td style="text-align:center;">' . $active_total . '</td>';
                echo '<td style="text-align:center;">' . $inactive_total . '</td>';
                echo '<td style="text-align:center;">' . $liquidation_total . '</td>';
                echo '<td style="text-align:center; font-weight:bold;">' . $sign . abs($rest_societies) . '</td>';
                echo '</tr>';
              }

              // Division Total Row (Rest = only sum of negative parts)
              echo '<tr class="division-total-row">';
              echo '<td colspan="2" style="text-align:right;"><b>Division Total (' . h($div_name) . ')</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_initial . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_filled . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_active . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_inactive . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_liquidation . '</b></td>';
              echo '<td style="text-align:center;"><b>-' . abs($div_rest_minus) . '</b></td>';
              echo '</tr>';
            } else {
              echo '<tr><td colspan="8" style="text-align:center;">कोई जिला उपलब्ध नहीं</td></tr>';
            }
          }

          // ---- Grand Total Row (Rest = only sum of negative parts) ----
          echo '<tr class="total-row">';
          echo '<td colspan="2" style="text-align:right;"><b>Grand Total</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_initial . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_filled . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_active . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_inactive . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_liquidation . '</b></td>';
          echo '<td style="text-align:center;"><b>-' . abs($grand_rest_minus) . '</b></td>';
          echo '</tr>';
        } else {
          echo '<tr><td colspan="8" style="text-align:center;">कोई रिकॉर्ड नहीं मिला</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
$(document).ready(function() {
  var t = $('#summary_table').DataTable({
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

  t.on('draw', function() {
    var idx = 1;
    $('#summary_table tbody tr.district-row').each(function() {
      $(this).find('td:first').text(idx++);
    });
  }).draw();
});
</script>

<?php
page_footer_start();
page_footer_end();
?>
