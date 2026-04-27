<?php
include("scripts/settings.php");

page_header_start();
page_header_end();
page_sidebar();
?>

<div class="container" style="background:#fff;padding:20px;margin-top:20px;">

<h3 style="text-align:center;margin-bottom:20px;">
कंप्यूटर ऑपरेटर (सहकार सारथी) रिपोर्ट
</h3>

<table border="1" width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;text-align:center;">
    <thead style="background:#f2f2f2;">
        <tr>
            <th>S.No</th>
            <th>जिला</th>
            <th>कुल समितियाँ</th>
            <th>कंप्यूटर ऑपरेटर (सहकार सारथी)</th>
        </tr>
    </thead>
    <tbody>

<?php

$i = 1;

$sql = "
SELECT 
    md.district_name,

    IFNULL(soc.total_societies,0) as total_societies,
    IFNULL(bp.operator,0) as operator

FROM master_district md

LEFT JOIN (
    SELECT col2 as district_id, COUNT(*) as total_societies
    FROM test2
    GROUP BY col2
) soc ON soc.district_id = md.sno

LEFT JOIN (
    SELECT bp1.*
    FROM bpacs_progress bp1
    INNER JOIN (
        SELECT district_id, MAX(id) as max_id
        FROM bpacs_progress
        WHERE is_deleted = 0
        GROUP BY district_id
    ) latest ON latest.max_id = bp1.id
) bp ON bp.district_id = md.sno

WHERE md.sno != 28
ORDER BY md.district_name
";

$res = execute_query($sql);

while($row = mysqli_fetch_assoc($res)){
    echo "<tr>
        <td>".$i++."</td>
        <td>".htmlspecialchars($row['district_name'])."</td>
        <td>".htmlspecialchars($row['total_societies'])."</td>
        <td>".htmlspecialchars($row['operator'])."</td>
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