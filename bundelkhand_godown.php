<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_set_charset($db, "utf8mb4");
mysqli_set_charset($db_upcod, "utf8mb4");

echo '<meta charset="UTF-8">';

echo '<style>
body{font-family:Arial;}
table{border-collapse:collapse;width:100%;margin-bottom:20px;}
th,td{border:1px solid #000;padding:6px;font-size:16px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h3{text-align:center;margin:15px 0;}
.total{font-weight:bold;background:#fafafa;}
.divtotal{font-weight:bold;background:#e6f2ff;}
.divhead{background:#e6f2ff;font-weight:bold;font-size:17px;}
</style>';

$report_date = date('d-m-Y');

echo "<h3>Societies having More Than (1000 sq. meter) Godown Land (Bundelkhand)</h3>
<h3>District Wise Summary</h3>
<div style='display:flex;justify-content:space-between;font-weight:bold;margin-bottom:15px;'>
<div>Date : $report_date</div>
<div>[1000 sq. meter = 0.1 hectare]</div>
</div>";

$range_min = 0.10;
$range_max = 10000;

$capacity = ['100 MT' => [0.0158, 0.0285], '250 MT' => [0.02886, 0.136], '500 MT' => [0.137, 0.197], '1000 MT' => [0.198, 0.237], '1500 MT' => [0.2371, 0.2832]];

$districts = [];
$res = mysqli_query($db, "SELECT d.sno AS district_id,d.district_name,v.division_name FROM master_district d JOIN master_division v ON v.sno=d.division_id WHERE d.division_id IN (7,11) ORDER BY v.division_name,d.district_name");

while ($d = mysqli_fetch_assoc($res)) {
    $districts[$d['district_id']] = ['district_name' => $d['district_name'], 'division_name' => $d['division_name']];
}

$matched = [];
$summary = [];

foreach ($districts as $did => $info) {

    $summary[$did] = ['PACS' => 0, 'Block Union' => 0, 'Marketing' => 0, 'Jila Sahkari' => 0, 'Consumer' => 0];

    $societies = [
        ['type' => "PACS", 'db' => $db_upcod, 'sql' => "SELECT t.col4 AS name,t.col31 AS secretary,t.col32 AS mobile,s.total_area AS area FROM survey_invoice si JOIN test2 t ON t.sno=si.society_id JOIN survey_invoice_sec_3_5 s ON s.survey_id=si.sno WHERE si.approval_status=6 AND s.suitable_godown='yes' AND t.col2='$did'"],
        ['type' => "Block Union", 'db' => $db, 'sql' => "SELECT samiti_naam AS name,sachiv_name AS secretary,land_area AS area FROM block_union WHERE is_deleted!=1 AND godown_suitable='हाँ' AND janpad_name='$did'"],
        ['type' => "Marketing", 'db' => $db, 'sql' => "SELECT society_name AS name,secretary_name AS secretary,secretary_mob AS mobile,land_area AS area FROM marketing WHERE is_deleted!=1 AND godown_suitable='हाँ' AND district_id='$did'"],
        ['type' => "Jila Sahkari", 'db' => $db, 'sql' => "SELECT society_name AS name,sachiv_name AS secretary,sachiv_no AS mobile,bhumi_area AS area FROM jila_sehkari WHERE is_deleted!=1 AND godown_suitable='हाँ' AND janpad_name='$did'"],
        ['type' => "Consumer", 'db' => $db, 'sql' => "SELECT society_name AS name,sachiv_name AS secretary,sachiv_no AS mobile,bhumi_area AS area FROM upss WHERE is_deleted!=1 AND godown_suitable='हाँ' AND janpad_name='$did'"]
    ];

    foreach ($societies as $cfg) {

        $q = mysqli_query($cfg['db'], $cfg['sql']);

        while ($r = mysqli_fetch_assoc($q)) {

            $area = (float) $r['area'];

            if ($area >= $range_min && $area <= $range_max) {

                $cap_label = "Not Matched";
                foreach ($capacity as $mt => $rng) {
                    if ($area >= $rng[0] && $area <= $rng[1]) {
                        $cap_label = $mt;
                        break;
                    }
                }

                $summary[$did][$cfg['type']]++;

                $matched[] = ['division' => $info['division_name'], 'district' => strtoupper($info['district_name']), 'type' => $cfg['type'], 'name' => $r['name'], 'area' => $area, 'secretary' => $r['secretary'] ?? '', 'mobile' => $r['mobile'] ?? ''];
            }
        }
    }
}

echo "<table><thead><tr>
<th>S.No</th>
<th>District</th>
<th>PACS</th>
<th>Block Union</th>
<th>Marketing</th>
<th>Jila Sahkari</th>
<th>Consumer</th>
<th>Total</th>
</tr></thead><tbody>";

$i = 1;
$current_div = "";
$div_totals = ['PACS' => 0, 'Block Union' => 0, 'Marketing' => 0, 'Jila Sahkari' => 0, 'Consumer' => 0];
$grand_totals = ['PACS' => 0, 'Block Union' => 0, 'Marketing' => 0, 'Jila Sahkari' => 0, 'Consumer' => 0];

foreach ($districts as $did => $info) {

    if ($current_div != "" && $current_div != $info['division_name']) {
        echo "<tr class='divtotal'><td colspan='2' style='text-align:left;padding-left:10px;'>$current_div Division</td>
<td>{$div_totals['PACS']}</td>
<td>{$div_totals['Block Union']}</td>
<td>{$div_totals['Marketing']}</td>
<td>{$div_totals['Jila Sahkari']}</td>
<td>{$div_totals['Consumer']}</td>
<td><b>" . array_sum($div_totals) . "</b></td></tr>";
        foreach ($div_totals as $k => $v) {
            $div_totals[$k] = 0;
        }
    }

    $current_div = $info['division_name'];
    $row = $summary[$did];
    $row_total = array_sum($row);

    echo "<tr>
<td>$i</td>
<td>" . strtoupper($info['district_name']) . "</td>
<td>{$row['PACS']}</td>
<td>{$row['Block Union']}</td>
<td>{$row['Marketing']}</td>
<td>{$row['Jila Sahkari']}</td>
<td>{$row['Consumer']}</td>
<td><b>$row_total</b></td>
</tr>";

    $i++;

    foreach ($row as $k => $v) {
        $div_totals[$k] += $v;
        $grand_totals[$k] += $v;
    }
}

echo "<tr class='divtotal'>
<td colspan='2' style='text-align:left;padding-left:10px;'>$current_div Division</td>
<td>{$div_totals['PACS']}</td>
<td>{$div_totals['Block Union']}</td>
<td>{$div_totals['Marketing']}</td>
<td>{$div_totals['Jila Sahkari']}</td>
<td>{$div_totals['Consumer']}</td>
<td><b>" . array_sum($div_totals) . "</b></td>
</tr>";

echo "<tr class='total'>
<td colspan='2'>GRAND TOTAL</td>
<td>{$grand_totals['PACS']}</td>
<td>{$grand_totals['Block Union']}</td>
<td>{$grand_totals['Marketing']}</td>
<td>{$grand_totals['Jila Sahkari']}</td>
<td>{$grand_totals['Consumer']}</td>
<td><b>" . array_sum($grand_totals) . "</b></td>
</tr>";

echo "</tbody></table>";

echo "<h3>Report (1000 sq. meter)</h3>";

echo "<table>
<thead>
<tr>
<th>S.No</th>
<th>District</th>
<th>Society Type</th>
<th>Society Name</th>
<th>Land Area</th>
<th>Secretary Name</th>
<th>Secretary Mobile No.</th>
</tr>
</thead>
<tbody>";

$i = 1;
$current_div = "";

foreach ($matched as $m) {

    if ($current_div != $m['division']) {
        $current_div = $m['division'];
        echo "<tr class='divhead'><td colspan='7' style='text-align:left;padding-left:10px;'>DIVISION : " . strtoupper($current_div) . "</td></tr>";
    }

    echo "<tr>
<td>$i</td>
<td>{$m['district']}</td>
<td>{$m['type']}</td>
<td>{$m['name']}</td>
<td>{$m['area']}</td>
<td>{$m['secretary']}</td>
<td>" . strtoupper($m['mobile']) . "</td>
</tr>";

    $i++;
}

echo "</tbody></table>";
?>