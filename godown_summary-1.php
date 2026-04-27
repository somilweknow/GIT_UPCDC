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
</style>
';

echo "<h3>Storage Capacity Wise Godown Summary</h3>";

/* ==== MT RANGES ==== */
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

/* ==== INIT STORAGE ==== */
$final_total = [];
foreach(array_keys($capacity) as $mt){
    $final_total[$mt] = [1=>0,2=>0,3=>0,4=>0,5=>0];
}

/* ==== DISTRICT LOOP ==== */
$res = mysqli_query($db,"SELECT sno FROM master_district WHERE sno!=28");

while($d = mysqli_fetch_assoc($res)){

    $did = $d['sno'];

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
            $final_total[$mt][$cfg['idx']] += $cnt;
        }
    }
}

/* ==== OUTPUT TABLE ==== */
echo "<table>
<thead>
<tr>
    <th>S.No</th>
    <th>Storage Capacity</th>
    <th>PACS</th>
    <th>Block Union</th>
    <th>Marketing</th>
    <th>Jila Sahkari</th>
    <th>Consumer</th>
    <th>Total</th>
</tr>
</thead>
<tbody>";

$i=1;

/* column totals init */
$col_total = [1=>0,2=>0,3=>0,4=>0,5=>0];
$grand_total_all = 0;

foreach($capacity as $mt=>$r){

    $row_total =
        $final_total[$mt][1] +
        $final_total[$mt][2] +
        $final_total[$mt][3] +
        $final_total[$mt][4] +
        $final_total[$mt][5];

    echo "<tr>
        <td>$i</td>
        <td>$mt</td>
        <td>".$final_total[$mt][1]."</td>
        <td>".$final_total[$mt][2]."</td>
        <td>".$final_total[$mt][3]."</td>
        <td>".$final_total[$mt][4]."</td>
        <td>".$final_total[$mt][5]."</td>
        <td><b>$row_total</b></td>
    </tr>";

    /* column totals */
    $col_total[1] += $final_total[$mt][1];
    $col_total[2] += $final_total[$mt][2];
    $col_total[3] += $final_total[$mt][3];
    $col_total[4] += $final_total[$mt][4];
    $col_total[5] += $final_total[$mt][5];

    $grand_total_all += $row_total;

    $i++;
}

/* ==== FINAL TOTAL ROW ==== */
echo "<tr class='total'>
        <td colspan='2'>TOTAL</td>
        <td>{$col_total[1]}</td>
        <td>{$col_total[2]}</td>
        <td>{$col_total[3]}</td>
        <td>{$col_total[4]}</td>
        <td>{$col_total[5]}</td>
        <td>$grand_total_all</td>
      </tr>";

echo "</tbody></table>";
?>
