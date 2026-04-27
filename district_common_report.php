<?php
echo '
<style>
body{font-family:Arial;}
table{
    border-collapse:collapse;
    width:100%;
}
th,td{
    border:1px solid #000;
    padding:6px;
    font-size:18px;
    text-align:center;
}
th{
    background:#f2f2f2;
    font-weight:bold;
}
h3{
    text-align:center;
    margin:15px 0;
}
.total{
    font-weight:bold;
    background:#fafafa;
}

/* PRINT FIX */
@media print {
    thead {
        display: table-header-group;
    }
    tr {
        page-break-inside: avoid;
    }
}
</style>
';

echo "<h3>$title</h3>";

$totals = ['pacs'=>0,'block'=>0,'marketing'=>0,'jila'=>0,'consumer'=>0];
$rows = [];

/* Fetch divisions */
$res_div = mysqli_query($db,"
    SELECT sno, division_name
    FROM master_division
    ORDER BY division_name ASC
");

while($div = mysqli_fetch_assoc($res_div)){

    $div_id = $div['sno'];

    /* PACS */
    $pacs = mysqli_num_rows(mysqli_query($db_upcod,"
        SELECT s.survey_id
        FROM survey_invoice si
        JOIN test2 t ON t.sno = si.society_id
        JOIN survey_invoice_sec_3_5 s ON s.survey_id = si.sno
        JOIN master_district md ON md.sno = t.col2
        WHERE si.approval_status = 6
          AND s.suitable_godown = 'yes'
          AND md.division_id = '$div_id'
          AND s.total_area BETWEEN {$range[0]} AND {$range[1]}
    "));

    /* Block Union */
    $block = mysqli_num_rows(mysqli_query($db,"
        SELECT bu.land_area
        FROM block_union bu
        JOIN master_district md ON md.sno = bu.janpad_name
        WHERE bu.is_deleted!=1
          AND bu.godown_suitable='हाँ'
          AND md.division_id='$div_id'
          AND bu.land_area BETWEEN {$range[0]} AND {$range[1]}
    "));

    /* Marketing */
    $marketing = mysqli_num_rows(mysqli_query($db,"
        SELECT m.land_area
        FROM marketing m
        JOIN master_district md ON md.sno = m.district_id
        WHERE m.is_deleted!=1
          AND m.godown_suitable='हाँ'
          AND md.division_id='$div_id'
          AND m.land_area BETWEEN {$range[0]} AND {$range[1]}
    "));

    /* Jila Sahkari */
    $jila = mysqli_num_rows(mysqli_query($db,"
        SELECT j.bhumi_area
        FROM jila_sehkari j
        JOIN master_district md ON md.sno = j.janpad_name
        WHERE j.is_deleted!=1
          AND j.godown_suitable='हाँ'
          AND md.division_id='$div_id'
          AND j.bhumi_area BETWEEN {$range[0]} AND {$range[1]}
    "));

    /* Consumer */
    $consumer = mysqli_num_rows(mysqli_query($db,"
        SELECT u.bhumi_area
        FROM upss u
        JOIN master_district md ON md.sno = u.janpad_name
        WHERE u.is_deleted!=1
          AND u.godown_suitable='हाँ'
          AND md.division_id='$div_id'
          AND u.bhumi_area BETWEEN {$range[0]} AND {$range[1]}
    "));

    /* Store row */
    $rows[] = [
        'division'=>$div['division_name'],
        'pacs'=>$pacs,
        'block'=>$block,
        'marketing'=>$marketing,
        'jila'=>$jila,
        'consumer'=>$consumer
    ];

    /* Grand totals */
    $totals['pacs'] += $pacs;
    $totals['block'] += $block;
    $totals['marketing'] += $marketing;
    $totals['jila'] += $jila;
    $totals['consumer'] += $consumer;
}

/* TABLE OUTPUT */
echo "
<table>
<thead>
<tr>
    <th>S.No</th>
    <th>Division</th>
    <th>PACS</th>
    <th>Block Union</th>
    <th>Marketing</th>
    <th>Jila Sahkari</th>
    <th>Consumer</th>
</tr>
</thead>
<tbody>

<!-- TOTAL AT TOP -->
<tr class='total'>
    <td colspan='2'>TOTAL</td>
    <td>{$totals['pacs']}</td>
    <td>{$totals['block']}</td>
    <td>{$totals['marketing']}</td>
    <td>{$totals['jila']}</td>
    <td>{$totals['consumer']}</td>
</tr>
";

$i = 1;
foreach($rows as $r){
    echo "
    <tr>
        <td>$i</td>
        <td>".strtoupper($r['division'])."</td>
        <td>{$r['pacs']}</td>
        <td>{$r['block']}</td>
        <td>{$r['marketing']}</td>
        <td>{$r['jila']}</td>
        <td>{$r['consumer']}</td>
    </tr>
    ";
    $i++;
}

echo "
<tr class='total'>
    <td colspan='2'>TOTAL</td>
    <td>{$totals['pacs']}</td>
    <td>{$totals['block']}</td>
    <td>{$totals['marketing']}</td>
    <td>{$totals['jila']}</td>
    <td>{$totals['consumer']}</td>
</tr>

</tbody>
</table>
";
?>
