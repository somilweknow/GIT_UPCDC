<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");
error_reporting(E_ALL);
ini_set('display_errors',1);

mysqli_set_charset($db,"utf8mb4");
mysqli_set_charset($db_upcod,"utf8mb4");

echo '<meta charset="UTF-8"><style>
body{font-family:Arial;}
table{border-collapse:collapse;width:100%;margin-bottom:20px;}
th,td{border:1px solid #000;padding:6px;font-size:18px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h2{text-align:center;margin:15px 0;}
</style>';

/* ================= BLOCK UNION ================= */

// echo "<h2>Block Union – All Land Area (Descending)</h2>
// <table><thead><tr>
// <th>S.No</th><th>Division</th><th>District</th><th>Society Type</th><th>Society Name</th><th>Land Area (Hectare)</th>
// </tr></thead><tbody>";

// $sr=1;

// $sql="SELECT v.division_name,d.district_name,b.samiti_naam AS name,b.land_area AS area
// FROM block_union b
// LEFT JOIN master_district d ON d.sno=b.janpad_name
// LEFT JOIN master_division v ON v.sno=d.division_id
// WHERE b.is_deleted!=1
// AND b.land_area IS NOT NULL
// AND b.land_area>0
// ORDER BY b.land_area DESC";

// $q=mysqli_query($db,$sql);

// while($r=mysqli_fetch_assoc($q)){
// echo "<tr>
// <td>$sr</td>
// <td>{$r['division_name']}</td>
// <td>".strtoupper($r['district_name'])."</td>
// <td>Block Union</td>
// <td>".strtoupper($r['name'])."</td>
// <td>".number_format($r['area'],4)."</td>
// </tr>";
// $sr++;
// }

if($sr==1) echo "<tr><td colspan='6'>No Block Union data found.</td></tr>";

echo "</tbody></table>";

/* ================= JILA SAHKARI SANGH ================= */

echo "<h2>Jila Sahkari Sangh – All Land Area (Descending)</h2>
<table><thead><tr>
<th>S.No</th><th>Division</th><th>District</th><th>Society Type</th><th>Society Name</th><th>Land Area (Hectare)</th>
</tr></thead><tbody>";

$sr=1;

$sql="SELECT v.division_name,d.district_name,j.society_name AS name,j.bhumi_area AS area
FROM jila_sehkari j
LEFT JOIN master_district d ON d.sno=j.janpad_name
LEFT JOIN master_division v ON v.sno=d.division_id
WHERE j.is_deleted!=1
AND j.bhumi_area IS NOT NULL
AND j.bhumi_area>0
ORDER BY j.bhumi_area DESC";

$q=mysqli_query($db,$sql);

while($r=mysqli_fetch_assoc($q)){
echo "<tr>
<td>$sr</td>
<td>{$r['division_name']}</td>
<td>".strtoupper($r['district_name'])."</td>
<td>Jila Sahkari Sangh</td>
<td>".($r['name'])."</td>
<td>".number_format((float)preg_replace('/[^0-9.]/','',$r['area']),4)."</td>
</tr>";
$sr++;
}

if($sr==1) echo "<tr><td colspan='6'>No Jila Sahkari Sangh data found.</td></tr>";

echo "</tbody></table>";
?>