<?php
include("scripts/settings.php");
session_start();

page_header_start();
page_header_end();
page_sidebar();

/* Helpers */
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }

/* Filters */
$where = " WHERE 1=1 ";
if (!empty($_SESSION['division_id'])) {
    $divs = array_map('intval', (array)$_SESSION['division_id']);
    if (!empty($divs)) $where .= " AND j.mandal_name IN (" . implode(',', $divs) . ")";
}
if (!empty($_SESSION['district_id'])) {
    $dis = array_map('intval', (array)$_SESSION['district_id']);
    if (!empty($dis)) $where .= " AND j.janpad_name IN (" . implode(',', $dis) . ")";
}

/*
Logic (same as marketing):
------------------------------------------------
- Pre-Feeded Verified     → updated_at IS NOT NULL AND created_at IS NULL
- Pre-Feeded Not Verified → created_at IS NULL AND updated_at IS NULL
- New Feeded              → created_at IS NOT NULL
- New Feeded Verified     → created_at IS NOT NULL AND updated_at IS NOT NULL
- Total                   → all records
------------------------------------------------
*/

$sql = "
SELECT
  COALESCE(dv.sno, 0) AS mandal_sno,
  COALESCE(NULLIF(TRIM(dv.division_name), ''), NULLIF(TRIM(j.mandal_name), ''), 'Unknown') AS mandal_name,

  /* Pre-Feeded Verified */
  SUM(CASE WHEN j.updated_at IS NOT NULL AND j.created_at IS NULL THEN 1 ELSE 0 END) AS prefeed_verified_count,

  /* Pre-Feeded Not Verified */
  SUM(CASE WHEN j.created_at IS NULL AND j.updated_at IS NULL THEN 1 ELSE 0 END) AS prefeed_not_verified_count,

  /* New Feeded */
  SUM(CASE WHEN j.created_at IS NOT NULL THEN 1 ELSE 0 END) AS new_feed_count,

  /* New Feeded Verified */
  SUM(CASE WHEN j.created_at IS NOT NULL AND j.updated_at IS NOT NULL THEN 1 ELSE 0 END) AS newfeed_verified_count,

  /* Total */
  COUNT(*) AS total_count

FROM jila_sehkari j
LEFT JOIN master_division dv ON j.mandal_name = dv.sno
{$where}
GROUP BY dv.sno, mandal_name
ORDER BY mandal_name
";

$res = execute_query($sql);
?>

<style>
.card{ background:#fff; border:1px solid #e6eefc; padding:16px; border-radius:8px; margin:18px 0; }
.section-heading{ background:#2b6fb3; color:#fff; padding:12px; border-radius:6px; font-weight:800; font-size:1.4em; text-align:center; }
.summary-table{ width:100%; border-collapse:collapse; margin-top:12px; min-width:900px; }
.summary-table th, .summary-table td{ border:1px solid #e6edf7; padding:10px; font-size:14px; text-align:left; }
.summary-table thead th{ background:#e8f5ff; font-weight:800; color:#08386b; padding:12px; }
tfoot td{ font-weight:bold; background:#f0f7ff; }
.kpi{ font-weight:800; }
.table-wrap{ overflow:auto; }
</style>

<div class="card">
  <div class="section-heading">Jila Sehkari Report - Summary</div>

  <div class="table-wrap" style="margin-top:12px;">
    <table id="mandal_summary" class="summary-table">
      <thead>
        <tr>
          <th>S.No.</th>
          <th>Division</th>
          <th>Pre-Feeded</th>
          <th>New feeded <br>& Verified</th>
          <th>Total</th>
          <th>Pre-feeded <br> Verified</th>
          <th>Pre-feeded <br> Not Verified</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        $sum_prefeed = $sum_prefeed_verified = $sum_prefeed_not_verified = $sum_newfeed = $sum_total = 0;

        if ($res && mysqli_num_rows($res) > 0) {
            while ($r = mysqli_fetch_assoc($res)) {
                $prefeed_verified = (int)$r['prefeed_verified_count'];
                $prefeed_not_verified = (int)$r['prefeed_not_verified_count'];
                $prefeed = $prefeed_verified + $prefeed_not_verified;
                $newfeed = (int)$r['new_feed_count'];
                $total = (int)$r['total_count'];

                $sum_prefeed += $prefeed;
                $sum_prefeed_verified += $prefeed_verified;
                $sum_prefeed_not_verified += $prefeed_not_verified;
                $sum_newfeed += $newfeed;
                $sum_total += $total;

                echo '<tr>';
                echo '<td>' . $i++ . '</td>';
                echo '<td>' . h($r['mandal_name']) . '</td>';
                echo '<td>' . h($prefeed) . '</td>';
                echo '<td>' . h($newfeed) . '</td>';
                echo '<td class="kpi">' . h($total) . '</td>';
                echo '<td>' . h($prefeed_verified) . '</td>';
                echo '<td>' . h($prefeed_not_verified) . '</td>';
                echo '</tr>';
            }
            mysqli_free_result($res);
        } else {
            echo '<tr><td colspan="7" style="text-align:center;">कोई रिकॉर्ड नहीं मिला</td></tr>';
        }
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2">Total</td>
          <td><?= h($sum_prefeed) ?></td>
          <td><?= h($sum_newfeed) ?></td>
          <td><?= h($sum_total) ?></td>
          <td><?= h($sum_prefeed_verified) ?></td>
          <td><?= h($sum_prefeed_not_verified) ?></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<?php
page_footer_start();
page_footer_end();
?>
