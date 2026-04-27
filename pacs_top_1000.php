<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");
error_reporting(E_ALL); ini_set('display_errors',1);
mysqli_set_charset($db,"utf8mb4"); mysqli_set_charset($db_upcod,"utf8mb4");

echo '<meta charset="UTF-8"><style>
body{font-family:Arial;}
table{border-collapse:collapse;width:100%;margin-bottom:20px;}
th,td{border:1px solid #000;padding:6px;font-size:18px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h2{text-align:center;margin:15px 0;}
</style>';

echo "<h2>Top 1000 PACS – Largest Land Area</h2>
<table><thead><tr>
<th>S.No</th><th>Division</th><th>District</th><th>Tehsil</th><th>Block</th><th>Society Type</th><th>Society Name</th><th>Land Area (Hectare)</th>
</tr></thead><tbody>";

$sr=1;

$sql="SELECT v.division_name,d.district_name,mt.tehseel_name,mb.block_name,t.col4 AS name,s.total_area AS area 
FROM survey_invoice si 
JOIN test2 t ON t.sno=si.society_id 
JOIN survey_invoice_sec_3_5 s ON s.survey_id=si.sno 
JOIN master_district d ON d.sno=t.col2 
JOIN master_division v ON v.sno=d.division_id
LEFT JOIN master_tehseel mt ON mt.sno=t.col5
LEFT JOIN master_block mb ON mb.sno=t.col6
WHERE si.approval_status=6 
AND s.suitable_godown='yes' 
AND s.total_area IS NOT NULL 
AND s.total_area>0 
ORDER BY s.total_area DESC 
LIMIT 1000";

$q=mysqli_query($db_upcod,$sql);

while($r=mysqli_fetch_assoc($q)){
echo "<tr>
<td>$sr</td>
<td>{$r['division_name']}</td>
<td>".strtoupper($r['district_name'])."</td>
<td>".strtoupper($r['tehseel_name'])."</td>
<td>".strtoupper($r['block_name'])."</td>
<td>PACS</td>
<td>".strtoupper($r['name'])."</td>
<td>".number_format($r['area'],4)."</td>
</tr>";
$sr++;
}

if($sr==1) echo "<tr><td colspan='8'>No PACS land area data found.</td></tr>";

echo "</tbody></table>";
?>