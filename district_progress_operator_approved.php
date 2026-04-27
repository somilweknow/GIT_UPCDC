<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
page_header_start();
page_header_end();
page_sidebar();
?>

<div class="container" style="background:#fff;padding:20px;margin-top:20px;">

<h3 style="text-align:center;margin-bottom:20px;">
Approved Districts (DR Approval)
</h3>

<table border="1" width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;text-align:center;">
    <thead style="background:#f2f2f2;">
        <tr>
            <th>S.No</th>
            <th>मण्डल</th>
            <th>जिला</th>
            <th>कुल समितियाँ</th>
            <th>कंप्यूटर ऑपरेटर (सहकार सारथी)</th>
        </tr>
    </thead>
    <tbody>

<?php

$i = 1;

$total_soc = 0;
$total_op = 0;

$sql = "
SELECT 
    mdv.division_name,
    mdt.district_name,

    IFNULL(soc.total_societies,0) as total_societies,
    IFNULL(bp.operator,0) as operator

FROM master_district mdt

INNER JOIN (
    SELECT bp1.*
    FROM bpacs_progress bp1
    INNER JOIN (
        SELECT district_id, MAX(id) as max_id
        FROM bpacs_progress
        WHERE is_deleted = 0
        GROUP BY district_id
    ) latest ON latest.max_id = bp1.id
    WHERE bp1.approval_status = 1
) bp ON bp.district_id = mdt.sno

LEFT JOIN master_division mdv ON mdv.sno = mdt.division_id

LEFT JOIN (
    SELECT col2 as district_id, COUNT(*) as total_societies
    FROM test2
    GROUP BY col2
) soc ON soc.district_id = mdt.sno

WHERE mdt.sno != 28
ORDER BY mdv.division_name, mdt.district_name
";

$res = execute_query($sql);

while($row = mysqli_fetch_assoc($res)){

    $soc_val = (int)($row['total_societies'] ?? 0);
    $op_val  = (int)($row['operator'] ?? 0);

    $total_soc += $soc_val;
    $total_op  += $op_val;

    echo "<tr>
        <td>".$i++."</td>
        <td>".strtoupper(htmlspecialchars($row['division_name']))."</td>
        <td>".strtoupper(htmlspecialchars($row['district_name']))."</td>
        <td>".$soc_val."</td>
        <td>".$op_val."</td>
    </tr>";
}

?>

<tr style="font-weight:bold;background:#f2f2f2;">
    <td colspan="3">TOTAL</td>
    <td><?php echo $total_soc; ?></td>
    <td><?php echo $total_op; ?></td>
</tr>

    </tbody>
</table>

</div>

<?php
page_footer_start();
page_footer_end();
?>