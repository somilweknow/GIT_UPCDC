<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_set_charset($db, "utf8mb4");
mysqli_set_charset($db_upcod, "utf8mb4");

echo '<meta charset="UTF-8">';

echo '
<style>
body{font-family:Arial;}
table{border-collapse:collapse;width:100%;margin-bottom:20px;}
th,td{border:1px solid #000;padding:6px;font-size:18px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h2{text-align:center;margin:15px 0;}
</style>
';

echo "<h2>Division & District Wise – 2500 MT Godown Feasibility (All Societies)</h2>";

/* 2500 MT RANGE */
$min_area = 0.4045;
$max_area = 0.8093;

/* District List */
$districts = [];

// $dq = mysqli_query($db,"
// SELECT 
// d.sno AS district_id,
// d.district_name,
// v.division_name
// FROM master_district d
// JOIN master_division v 
// ON v.sno = d.division_id
// ORDER BY v.division_name,d.district_name
// ");

$district_filter = "1,47,35,22,39,40,55,49,10,11,32,46,20,27,3,67,4,70,71,21,41,14,61,9,29,38,16,58,63,62,69,51,66,19";

$dq = mysqli_query($db,"
SELECT 
d.sno AS district_id,
d.district_name,
v.division_name
FROM master_district d
JOIN master_division v 
ON v.sno = d.division_id
WHERE d.sno IN ($district_filter)
ORDER BY v.division_name,d.district_name
");

while($d = mysqli_fetch_assoc($dq)){
    $districts[$d['district_id']] = [
        'district' => $d['district_name'],
        'division' => $d['division_name']
    ];
}

/* Table */

echo "<table>
<thead>
<tr>
<th>S.No</th>
<th>Division</th>
<th>District</th>
<th>Society Type</th>
<th>Society Name</th>
<th>Land Area (Hectare)</th>
</tr>
</thead>
<tbody>";

$sr = 1;

foreach($districts as $did => $info){

$division = $info['division'];
$district = $info['district'];

/* ---------- PACS ---------- */

$sql1 = "
SELECT 
t.col4 AS name,
s.total_area AS area
FROM survey_invoice si
JOIN test2 t ON t.sno = si.society_id
JOIN survey_invoice_sec_3_5 s 
ON s.survey_id = si.sno
WHERE si.approval_status = 6
AND s.suitable_godown='yes'
AND t.col2='$did'
AND s.total_area >= $min_area
AND s.total_area < $max_area
";

$q1 = mysqli_query($db_upcod,$sql1);

while($r=mysqli_fetch_assoc($q1)){

echo "<tr>
<td>$sr</td>
<td>$division</td>
<td>".strtoupper($district)."</td>
<td>PACS</td>
<td>".strtoupper($r['name'])."</td>
<td>".number_format($r['area'],4)."</td>
</tr>";

$sr++;
}


/* ---------- BLOCK UNION ---------- */

$sql2 = "
SELECT 
samiti_naam AS name,
land_area AS area
FROM block_union
WHERE is_deleted!=1
AND godown_suitable='हाँ'
AND janpad_name='$did'
AND land_area >= $min_area
AND land_area < $max_area
";

$q2=mysqli_query($db,$sql2);

while($r=mysqli_fetch_assoc($q2)){

echo "<tr>
<td>$sr</td>
<td>$division</td>
<td>".strtoupper($district)."</td>
<td>Block Union</td>
<td>".strtoupper($r['name'])."</td>
<td>".number_format($r['area'],4)."</td>
</tr>";

$sr++;
}


/* ---------- MARKETING ---------- */

$sql3="
SELECT 
society_name AS name,
land_area AS area
FROM marketing
WHERE is_deleted!=1
AND godown_suitable='हाँ'
AND district_id='$did'
AND land_area >= $min_area
AND land_area < $max_area
";

$q3=mysqli_query($db,$sql3);

while($r=mysqli_fetch_assoc($q3)){

echo "<tr>
<td>$sr</td>
<td>$division</td>
<td>".strtoupper($district)."</td>
<td>Marketing</td>
<td>".strtoupper($r['name'])."</td>
<td>".($r['area'])."</td>
</tr>";

$sr++;
}


/* ---------- JILA SAHKARI ---------- */

$sql4="
SELECT 
society_name AS name,
bhumi_area AS area
FROM jila_sehkari
WHERE is_deleted!=1
AND godown_suitable='हाँ'
AND janpad_name='$did'
AND bhumi_area >= $min_area
AND bhumi_area < $max_area
";

$q4=mysqli_query($db,$sql4);

while($r=mysqli_fetch_assoc($q4)){

echo "<tr>
<td>$sr</td>
<td>$division</td>
<td>".strtoupper($district)."</td>
<td>Jila Sahkari</td>
<td>".strtoupper($r['name'])."</td>
<td>".($r['area'])."</td>
</tr>";

$sr++;
}


/* ---------- CONSUMER ---------- */

$sql5="
SELECT 
society_name AS name,
bhumi_area AS area
FROM upss
WHERE is_deleted!=1
AND godown_suitable='हाँ'
AND janpad_name='$did'
AND bhumi_area >= $min_area
AND bhumi_area < $max_area
";

$q5=mysqli_query($db,$sql5);

while($r=mysqli_fetch_assoc($q5)){

echo "<tr>
<td>$sr</td>
<td>$division</td>
<td>".strtoupper($district)."</td>
<td>Consumer</td>
<td>".strtoupper($r['name'])."</td>
<td>".($r['area'])."</td>
</tr>";

$sr++;
}

}

/* No data */

if($sr==1){

echo "<tr>
<td colspan='6'>
No societies found under 2500 MT capacity.
</td>
</tr>";

}

echo "</tbody></table>";

?>