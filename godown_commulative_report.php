<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '
<style>
body{font-family:Arial;}
table{border-collapse:collapse;width:100%;}
th,td{border:1px solid #000;padding:6px;font-size:13px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h3{text-align:center;margin:15px 0;}
.total{font-weight:bold;background:#fafafa;}
.small{font-size:11px;}

@media print {
    thead {display: table-header-group;}
    tr {page-break-inside: avoid;}
}
</style>
';

echo "<h3>District Wise Godown Feasibility Summary (100 MT to 5000+ MT)</h3>";

/* ==== ALL MT RANGES ==== */
$capacity = [
    '100 MT'    => [0.015829, 0.02886],
    '250 MT'    => [0.028868, 0.136],
    '500 MT'    => [0.137, 0.197],
    '1000 MT'   => [0.198, 0.237],
    '1500 MT'   => [0.2371, 0.283],
    '2000 MT'   => [0.2832, 0.4045],
    '2500 MT'   => [0.4045, 0.809],
    '5000+ MT'  => [0.8093, 1000]
];

/* ==== TABLE HEADER ==== */
echo "<table>
<thead>
<tr>
    <th rowspan='2'>S.No</th>
    <th rowspan='2'>District</th>
    <th colspan='".count($capacity)."'>PACS</th>
    <th colspan='".count($capacity)."'>Block Union</th>
    <th colspan='".count($capacity)."'>Marketing</th>
    <th colspan='".count($capacity)."'>Jila Sahkari</th>
    <th colspan='".count($capacity)."'>Consumer</th>
</tr>
<tr>";

for($i=1;$i<=5;$i++){
    foreach($capacity as $mt=>$r){
        echo "<th class='small'>$mt</th>";
    }
}
echo "</tr>
</thead>
<tbody>";

/* ==== GRAND TOTAL INIT ==== */
$grand_total = [];
foreach(range(1,5) as $s){
    foreach(array_keys($capacity) as $mt){
        $grand_total[$s][$mt] = 0;
    }
}

/* ==== DISTRICT LOOP ==== */
$i = 1;
$res = mysqli_query($db,"SELECT * FROM master_district WHERE sno!=28 ORDER BY district_name ASC");

while($d = mysqli_fetch_assoc($res)){

    $did = $d['sno'];
    $district = $d['district_name'];

    $societies = [

        ['idx'=>1,'db'=>$db_upcod,'sql'=>"
            SELECT s.total_area area
            FROM survey_invoice si
            JOIN test2 t ON t.sno=si.society_id
            JOIN survey_invoice_sec_3_5 s ON s.survey_id=si.sno
            WHERE si.approval_status=6
            AND s.suitable_godown='yes'
            AND t.col2='$did'
        "],

        ['idx'=>2,'db'=>$db,'sql'=>"
            SELECT land_area area FROM block_union
            WHERE is_deleted!=1 AND godown_suitable='हाँ'
            AND janpad_name='$did'
        "],

        ['idx'=>3,'db'=>$db,'sql'=>"
            SELECT land_area area FROM marketing
            WHERE is_deleted!=1 AND godown_suitable='हाँ'
            AND district_id='$did'
        "],

        ['idx'=>4,'db'=>$db,'sql'=>"
            SELECT bhumi_area area FROM jila_sehkari
            WHERE is_deleted!=1 AND godown_suitable='हाँ'
            AND janpad_name='$did'
        "],

        ['idx'=>5,'db'=>$db,'sql'=>"
            SELECT bhumi_area area FROM upss
            WHERE is_deleted!=1 AND godown_suitable='हाँ'
            AND janpad_name='$did'
        "]
    ];

    echo "<tr>
            <td>$i</td>
            <td>$district</td>";

    foreach($societies as $cfg){

        $areas = [];
        $q = mysqli_query($cfg['db'],$cfg['sql']);
        while($r = mysqli_fetch_assoc($q)){
            if($r['area'] > 0){
                $areas[] = $r['area'];
            }
        }

        foreach($capacity as $mt=>$range){
            $cnt = 0;
            foreach($areas as $a){
                if($a >= $range[0] && $a <= $range[1]){
                    $cnt++;
                }
            }
            $grand_total[$cfg['idx']][$mt] += $cnt;
            echo "<td>$cnt</td>";
        }
    }

    echo "</tr>";
    $i++;
}

/* ==== TOTAL ROW ==== */
echo "<tr class='total'>
        <td colspan='2'>TOTAL</td>";

foreach($grand_total as $soc){
    foreach($capacity as $mt=>$r){
        echo "<td>{$soc[$mt]}</td>";
    }
}

echo "</tr>
</tbody>
</table>";
?>
