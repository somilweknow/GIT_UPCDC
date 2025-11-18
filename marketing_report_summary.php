<?php
include("scripts/settings.php");
error_reporting(E_ALL);

page_header_start();
page_header_end();
page_sidebar();

/* Helpers */
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

/* Apply division/district filters */
$where = " WHERE 1=1 ";
if (!empty($_SESSION['division_id'])) {
  $divs = array_map('intval', (array)$_SESSION['division_id']);
  if (!empty($divs)) $where .= " AND j.division_id IN (" . implode(',', $divs) . ")";
}
if (!empty($_SESSION['district_id'])) {
  $dis = array_map('intval', (array)$_SESSION['district_id']);
  if (!empty($dis)) $where .= " AND j.district_id IN (" . implode(',', $dis) . ")";
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
  <div class="section-heading">Marketing Report - Division & District Summary</div>

  <div class="table-wrap" style="margin-top:12px;">
    <table id="marketing_summary_table" class="summary-table">
      <thead>
        <tr>
          <th>S.No.</th>
          <th>District / Division</th>
          <th>Pre-Feeded</th>
          <th>New Feeded</th>
          <th>Total</th>
          <th>Pre-Feeded Verified</th>
          <th>Pre-Feeded Not Verified</th>
          <!-- <th>New Feeded Verified</th> -->
        </tr>
      </thead>
      <tbody>
        <?php
        $row_no = 1;
        $grand_prefeed = $grand_newfeed = $grand_total = $grand_prefeed_verified = $grand_prefeed_not_verified = $grand_newfeed_verified = 0;

        if ($res_divs && mysqli_num_rows($res_divs) > 0) {
          while ($div = mysqli_fetch_assoc($res_divs)) {
            $div_sno = intval($div['division_sno']);
            $div_name = $div['division_name'];

            echo '<tr class="division-row"><td colspan="8">Division: ' . h($div_name) . '</td></tr>';

            $div_prefeed = $div_newfeed = $div_total = $div_prefeed_verified = $div_prefeed_not_verified = $div_newfeed_verified = 0;

            // Districts under division
            $sql_dists = "
              SELECT 
                md.sno AS district_sno, 
                md.district_name,
                SUM(CASE WHEN j.created_at IS NULL THEN 1 ELSE 0 END) AS prefeed_count,
                SUM(CASE WHEN j.created_at IS NOT NULL THEN 1 ELSE 0 END) AS newfeed_count,
                SUM(CASE WHEN j.updated_at IS NOT NULL AND j.created_at IS NULL THEN 1 ELSE 0 END) AS prefeed_verified_count,
                SUM(CASE WHEN j.created_at IS NULL AND j.updated_at IS NULL THEN 1 ELSE 0 END) AS prefeed_not_verified_count,
                SUM(CASE WHEN j.updated_at IS NOT NULL AND j.created_at IS NOT NULL THEN 1 ELSE 0 END) AS newfeed_verified_count,
                COUNT(*) AS total_count
              FROM marketing j
              LEFT JOIN master_district md ON j.district_id = md.sno
              WHERE md.division_id = {$div_sno}
              GROUP BY md.sno, md.district_name
              ORDER BY md.district_name
            ";
            $res_dists = mysqli_query($db, $sql_dists);

            if ($res_dists && mysqli_num_rows($res_dists) > 0) {
              while ($dist = mysqli_fetch_assoc($res_dists)) {
                $prefeed = (int)$dist['prefeed_count'];
                $newfeed = (int)$dist['newfeed_count'];
                $prefeed_verified = (int)$dist['prefeed_verified_count'];
                $prefeed_not_verified = (int)$dist['prefeed_not_verified_count'];
                $newfeed_verified = (int)$dist['newfeed_verified_count'];
                $total = (int)$dist['total_count'];

                $div_prefeed += $prefeed;
                $div_newfeed += $newfeed;
                $div_total += $total;
                $div_prefeed_verified += $prefeed_verified;
                $div_prefeed_not_verified += $prefeed_not_verified;
                $div_newfeed_verified += $newfeed_verified;

                $grand_prefeed += $prefeed;
                $grand_newfeed += $newfeed;
                $grand_total += $total;
                $grand_prefeed_verified += $prefeed_verified;
                $grand_prefeed_not_verified += $prefeed_not_verified;
                $grand_newfeed_verified += $newfeed_verified;

                echo '<tr class="district-row">';
                echo '<td style="text-align:center;">' . $row_no++ . '</td>';
                echo '<td>' . h($dist['district_name']) . '</td>';
                echo '<td style="text-align:center;">' . $prefeed . '</td>';
                echo '<td style="text-align:center;">' . $newfeed . '</td>';
                echo '<td style="text-align:center; font-weight:bold;">' . $total . '</td>';
                echo '<td style="text-align:center;">' . $prefeed_verified . '</td>';
                echo '<td style="text-align:center;">' . $prefeed_not_verified . '</td>';
                echo '</tr>';
              }

              // Division total row
              echo '<tr class="division-total-row">';
              echo '<td colspan="2" style="text-align:right;"><b>Division Total (' . h($div_name) . ')</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_prefeed . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_newfeed . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_total . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_prefeed_verified . '</b></td>';
              echo '<td style="text-align:center;"><b>' . $div_prefeed_not_verified . '</b></td>';
              echo '</tr>';
            } else {
              echo '<tr><td colspan="8" style="text-align:center;">कोई जिला उपलब्ध नहीं</td></tr>';
            }
          }

          // Grand total row
          echo '<tr class="total-row">';
          echo '<td colspan="2" style="text-align:right;"><b>Total</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_prefeed . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_newfeed . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_total . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_prefeed_verified . '</b></td>';
          echo '<td style="text-align:center;"><b>' . $grand_prefeed_not_verified . '</b></td>';
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
  var t = $('#marketing_summary_table').DataTable({
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
    $('#marketing_summary_table tbody tr.district-row').each(function() {
      $(this).find('td:first').text(idx++);
    });
  }).draw();
});
</script>

<?php
page_footer_start();
page_footer_end();
?>
