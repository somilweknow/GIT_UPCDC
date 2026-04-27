<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '
<style>
body{font-family:Arial, sans-serif;}
table{border-collapse:collapse;width:100%;}
th,td{border:1px solid #000;padding:6px;font-size:13px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h3{text-align:center;margin:15px 0;}
</style>
';

/* MT Capacity Slabs (Hectare Range) */
$capacity = [
    '500 MT'   => [0.137, 0.197],
    '1000 MT'  => [0.198, 0.236],
    '1500 MT'  => [0.237, 0.282],
    '2000 MT'  => [0.283, 0.403],
    '2500 MT'  => [0.404, 0.808],
    '5000 MT'  => [0.809, 1.213],
    '7500 MT'  => [1.214, 1.617],
    '10000 MT' => [1.618, 999]
];

echo "<h3>District Wise Godown Feasibility Report (MT Capacity)</h3>";

echo "<table>
<tr>
    <th>S.No</th>
    <th>District</th>
    <th>Society Type</th>";

foreach($capacity as $mt=>$r){
    echo "<th>$mt</th>";
}
echo "</tr>";

$i = 1;

/* Fetch Districts */
$res = mysqli_query($db,"SELECT * FROM master_district WHERE sno!=28");

while($d = mysqli_fetch_assoc($res)){

    $did = $d['sno'];
    $district = $d['district_name'];

    /* Society Queries */
    $societies = [

        'PACS' => [
            'db'=>$db_upcod,
            'sql'=>"SELECT s.total_area area
                    FROM survey_invoice si
                    JOIN test2 t ON t.sno=si.society_id
                    JOIN survey_invoice_sec_3_5 s ON s.survey_id=si.sno
                    WHERE si.approval_status=6
                    AND s.suitable_godown='yes'
                    AND t.col2='$did'"
        ],

        'Block Union' => [
            'db'=>$db,
            'sql'=>"SELECT land_area area
                    FROM block_union
                    WHERE is_deleted!=1
                    AND godown_suitable='हाँ'
                    AND janpad_name='$did'"
        ],

        'Marketing' => [
            'db'=>$db,
            'sql'=>"SELECT land_area area
                    FROM marketing
                    WHERE is_deleted!=1
                    AND godown_suitable='हाँ'
                    AND district_id='$did'"
        ],

        'Jila Sahkari' => [
            'db'=>$db,
            'sql'=>"SELECT bhumi_area area
                    FROM jila_sehkari
                    WHERE is_deleted!=1
                    AND godown_suitable='हाँ'
                    AND janpad_name='$did'"
        ],

        'Consumer' => [
            'db'=>$db,
            'sql'=>"SELECT bhumi_area area
                    FROM upss
                    WHERE is_deleted!=1
                    AND godown_suitable='हाँ'
                    AND janpad_name='$did'"
        ]
    ];

    $printed = false;

    foreach($societies as $name=>$cfg){

        echo "<tr>";

        /* FIXED ROWSPAN LOGIC */
        if(!$printed){
            echo "<td rowspan='5'>$i</td>";
            echo "<td rowspan='5'>$district</td>";
            $printed = true;
        }

        echo "<td>$name</td>";

        /* Fetch Areas */
        $areas = [];
        $q = mysqli_query($cfg['db'],$cfg['sql']);
        while($r = mysqli_fetch_assoc($q)){
            if($r['area'] > 0){
                $areas[] = $r['area'];
            }
        }

        /* Count MT-wise feasibility */
        foreach($capacity as $range){
            $cnt = 0;
            foreach($areas as $a){
                if($a >= $range[0] && $a <= $range[1]){
                    $cnt++;
                }
            }
            echo "<td>$cnt</td>";
        }

        echo "</tr>";
    }

    $i++;
}

echo "</table>";
?>