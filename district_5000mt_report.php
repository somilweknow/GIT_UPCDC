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
echo "<h2>Division & District Wise PACS – 5000 MT & Above Godown Feasibility</h2>";
$min_area = 0.8093;
$districts = [];
// $dq = mysqli_query($db,"SELECT  d.sno AS district_id, d.district_name, v.division_name FROM master_district d JOIN master_division v ON v.sno = d.division_id ORDER BY v.division_name, d.district_name");
$district_filter = "1,47,35,22,39,40,55,49,10,11,32,46,20,27,3,67,4,70,71,21,41,14,61,9,29,38,16,58,63,62,69,51,66,19";

$dq = mysqli_query($db,"SELECT  d.sno AS district_id, d.district_name, v.division_name FROM master_district d JOIN master_division v ON v.sno = d.division_id WHERE d.sno IN ($district_filter)ORDER BY v.division_name, d.district_name");

while($d = mysqli_fetch_assoc($dq)){
    $districts[$d['district_id']] = [
        'district' => $d['district_name'],
        'division' => $d['division_name']
    ];
}

echo "<table>
<thead>
<tr>
<th>S.No</th>
<th>Division</th>
<th>District</th>
<th>PACS Name</th>
<th>Land Area (Hectare)</th>
</tr>
</thead>
<tbody>";

$sr = 1;

foreach($districts as $did => $info){

    $division = $info['division'];
    $district = $info['district'];

    $sql = "SELECT t.col4 AS pacs_name, s.total_area AS land_area FROM survey_invoice si JOIN test2 t ON t.sno = si.society_id JOIN survey_invoice_sec_3_5 s ON s.survey_id = si.sno WHERE si.approval_status = 6 AND s.suitable_godown = 'yes' AND t.col2 = '$did' AND s.total_area >= $min_area ORDER BY t.col4 ";

    $q = mysqli_query($db_upcod, $sql);

    while($r = mysqli_fetch_assoc($q)){
        echo "<tr>
            <td>{$sr}</td>
            <td>{$division}</td>
            <td>".strtoupper($district)."</td>
            <td>".strtoupper($r['pacs_name'])."</td>
            <td>".number_format($r['land_area'],4)."</td>
        </tr>";
        $sr++;
    }
}
if($sr == 1){
    echo "<tr>
        <td colspan='6'>No PACS found under 5000 MT & Above capacity.</td>
    </tr>";
}
echo "</tbody></table>";
?>