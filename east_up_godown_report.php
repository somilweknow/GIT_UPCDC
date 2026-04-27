<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ==== UTF8 Fix for Hindi ==== */
mysqli_set_charset($db, "utf8mb4");
mysqli_set_charset($db_upcod, "utf8mb4");

echo '<meta charset="UTF-8">';

echo '
<style>
body{font-family:Arial;}
table{border-collapse:collapse;width:100%;margin-bottom:20px;}
th,td{border:1px solid #000;padding:6px;font-size:16px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h3{text-align:center;margin:15px 0;}
.total{font-weight:bold;background:#fafafa;}
</style>
';

echo "<h3>Storage Capacity 500 MT to 1500 MT – Societies Report</h3>";

/* ===== CAPACITY RANGE 500 TO 1500 MT ===== */
/* (Using same hectare logic as your master table) */
$capacity = [
    '500 MT'  => [0.137, 1000],
    '1000 MT' => [0.198, 1000],
    '1500 MT' => [0.2371,1000]
];

/* ===== FETCH DISTRICTS OF REQUIRED DIVISIONS ===== */
$districts = [];
$res = mysqli_query($db,"
    SELECT sno, district_name 
    FROM master_district 
    WHERE division_id IN (4,10,15,18)
");

while($d=mysqli_fetch_assoc($res)){
    $districts[$d['sno']] = $d['district_name'];
}

/* ===== INIT SUMMARY ===== */
$final_total = [];
foreach(array_keys($capacity) as $mt){
    $final_total[$mt] = [1=>0,2=>0,3=>0,4=>0,5=>0];
}

/* ===== STORE DETAILED SOCIETIES ===== */
$matched = [];

foreach($districts as $did=>$dname){

    $societies = [

        /* === PACS === */
        ['idx'=>1,'type'=>"PACS",'db'=>$db_upcod,'sql'=>"
            SELECT t.col4 AS name, s.total_area AS area
            FROM survey_invoice si
            JOIN test2 t ON t.sno=si.society_id
            JOIN survey_invoice_sec_3_5 s ON s.survey_id=si.sno
            WHERE si.approval_status=6
            AND s.suitable_godown='yes'
            AND t.col2='$did'
        "],

        /* === BLOCK UNION === */
        ['idx'=>2,'type'=>"Block Union",'db'=>$db,'sql'=>"
            SELECT samiti_naam AS name, land_area AS area
            FROM block_union
            WHERE is_deleted!=1 
            AND godown_suitable='हाँ'
            AND janpad_name='$did'
        "],

        /* === MARKETING === */
        ['idx'=>3,'type'=>"Marketing",'db'=>$db,'sql'=>"
            SELECT society_name AS name, land_area AS area
            FROM marketing
            WHERE is_deleted!=1 
            AND godown_suitable='हाँ'
            AND district_id='$did'
        "],

        /* === JILA SAHKARI === */
        ['idx'=>4,'type'=>"Jila Sahkari",'db'=>$db,'sql'=>"
            SELECT society_name AS name, bhumi_area AS area
            FROM jila_sehkari
            WHERE is_deleted!=1 
            AND godown_suitable='हाँ'
            AND janpad_name='$did'
        "],

        /* === CONSUMER === */
        ['idx'=>5,'type'=>"Consumer",'db'=>$db,'sql'=>"
            SELECT society_name AS name, bhumi_area AS area
            FROM upss
            WHERE is_deleted!=1 
            AND godown_suitable='हाँ'
            AND janpad_name='$did'
        "]
    ];

    foreach($societies as $cfg){

        $q = mysqli_query($cfg['db'],$cfg['sql']);

        while($r=mysqli_fetch_assoc($q)){

            $area = (float)$r['area'];

            foreach($capacity as $mt=>$range){

                if($area >= $range[0] && $area <= $range[1]){

                    /* === SUMMARY COUNT === */
                    $final_total[$mt][$cfg['idx']]++;

                    /* === DETAILED LIST === */
                    $matched[] = [
                        'district'=>$dname,
                        'type'=>$cfg['type'],
                        'name'=>$r['name'],
                        'area'=>$area,
                        'capacity'=>$mt
                    ];
                }
            }
        }
    }
}

/* ===== SUMMARY TABLE ===== */

/* ===== SUMMARY TABLE ===== */

echo "<h3>Summary</h3>";

echo "<table>
<thead>
<tr>
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

/* Column totals */
$col_total = [1=>0,2=>0,3=>0,4=>0,5=>0];
$grand_total = 0;

foreach($capacity as $mt=>$r){

    $row_total =
        $final_total[$mt][1] +
        $final_total[$mt][2] +
        $final_total[$mt][3] +
        $final_total[$mt][4] +
        $final_total[$mt][5];

    echo "<tr>
        <td>$mt</td>
        <td>{$final_total[$mt][1]}</td>
        <td>{$final_total[$mt][2]}</td>
        <td>{$final_total[$mt][3]}</td>
        <td>{$final_total[$mt][4]}</td>
        <td>{$final_total[$mt][5]}</td>
        <td><b>$row_total</b></td>
    </tr>";

    /* accumulate column totals */
    $col_total[1] += $final_total[$mt][1];
    $col_total[2] += $final_total[$mt][2];
    $col_total[3] += $final_total[$mt][3];
    $col_total[4] += $final_total[$mt][4];
    $col_total[5] += $final_total[$mt][5];

    $grand_total += $row_total;
}

/* === FINAL GRAND TOTAL ROW === */
echo "<tr class='total'>
    <td><b>Grand Total</b></td>
    <td>{$col_total[1]}</td>
    <td>{$col_total[2]}</td>
    <td>{$col_total[3]}</td>
    <td>{$col_total[4]}</td>
    <td>{$col_total[5]}</td>
    <td><b>$grand_total</b></td>
</tr>";

echo "</tbody></table>";


/* ===== DETAILED LIST ===== */

echo "<h3>All Societies (500 MT to 1500 MT)</h3>";

echo "<table>
<thead>
<tr>
<th>S.No</th>
<th>District</th>
<th>Society Type</th>
<th>Society Name</th>
<th>Land Area (Hectare)</th>
<th>Capacity Group</th>
</tr>
</thead>
<tbody>";

$i=1;
foreach($matched as $m){

    echo "<tr>
        <td>$i</td>
        <td>".strtoupper($m['district'])."</td>
        <td>".strtoupper($m['type'])."</td>
        <td>".strtoupper($m['name'])."</td>
        <td>{$m['area']}</td>
        <td>{$m['capacity']}</td>
    </tr>";

    $i++;
}

echo "</tbody></table>";

?>
