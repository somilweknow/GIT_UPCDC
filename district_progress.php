<?php
include("scripts/settings.php");

page_header_start();
page_header_end();
page_sidebar();
?>

<div style="padding:20px;">

<h3 style="text-align:center;">B-PACS Approval Status Report</h3>

<?php

$summary_sql = "
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN approval_status = 1 THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN approval_status = 2 THEN 1 ELSE 0 END) as rejected,
    SUM(CASE WHEN approval_status = 0 THEN 1 ELSE 0 END) as pending
FROM (
    SELECT bp.*
    FROM bpacs_progress bp
    INNER JOIN (
        SELECT district_id, MAX(id) as max_id
        FROM bpacs_progress
        WHERE is_deleted = 0
        GROUP BY district_id
    ) latest ON latest.max_id = bp.id
) x
";

$summary = mysqli_fetch_assoc(execute_query($summary_sql));

$total = $summary['total'] ?? 0;
$approved = $summary['approved'] ?? 0;
$rejected = $summary['rejected'] ?? 0;
$pending = $summary['pending'] ?? 0;
?>

<div style="margin-bottom:20px;display:flex;gap:15px;flex-wrap:wrap;">
    <div style="background:#edf2ff;padding:15px 20px;border-radius:8px;font-weight:bold;">
        Total Submitted: <?= $total ?>
    </div>
    <div style="background:#c6f6d5;padding:15px 20px;border-radius:8px;font-weight:bold;">
        Approved: <?= $approved ?>
    </div>
    <div style="background:#fed7d7;padding:15px 20px;border-radius:8px;font-weight:bold;">
        Rejected: <?= $rejected ?>
    </div>
    <div style="background:#fefcbf;padding:15px 20px;border-radius:8px;font-weight:bold;">
        Pending: <?= $pending ?>
    </div>
</div>

<table border="1" cellspacing="0" cellpadding="8" width="100%" style="border-collapse:collapse;text-align:center;">
    <thead style="background:#f2f2f2;font-weight:bold;">
        <tr>
            <th>S.No</th>
            <th>Division</th>
            <th>District</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

<?php
$i = 1;

$sql = "
SELECT 
    dv.division_name,
    md.district_name,
    bp.approval_status,
    bp.approved_by,
    bp.approved_at
FROM master_district md
LEFT JOIN master_division dv ON dv.sno = md.division_id
LEFT JOIN (
    SELECT *
    FROM bpacs_progress
    WHERE is_deleted = 0
    ORDER BY id DESC
) bp ON bp.district_id = md.sno
WHERE md.sno != 28
GROUP BY md.sno
ORDER BY dv.division_name, md.district_name
";

$res = execute_query($sql);

while($row = mysqli_fetch_assoc($res)){

    $status = "No Data";
    if ($row['approval_status'] == 1) $status = "Approved";
    elseif ($row['approval_status'] == 2) $status = "Rejected";
    elseif ($row['approval_status'] == 0) $status = "Pending";

    echo "<tr>
        <td>".$i++."</td>
        <td>".htmlspecialchars($row['division_name'])."</td>
        <td>".htmlspecialchars($row['district_name'])."</td>
        <td>".$status."</td>
    </tr>";
}
?>

    </tbody>
</table>

</div>

<?php
page_footer_start();
page_footer_end();
?>