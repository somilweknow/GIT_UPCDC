<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '
<style>
    body{font-family:Arial, sans-serif;}
    table{border-collapse:collapse;width:100%;text-align:center;margin-bottom:40px;}
    th,td{border:1px solid #000;padding:6px;font-size:14px;}
    th{background:#f2f2f2;font-weight:bold;}
    h3{text-align:center;margin:20px 0;}
    .total-row{font-weight:bold;background:#fafafa;}
</style>';

district_report();
division_report();

function district_report(){
    global $db, $db_upcod;

    $i = 1;
    $tot = array_fill(0, 5, 0);

    $res = mysqli_query($db,"SELECT * FROM master_district WHERE sno != 28");

    echo '<h3>District Wise Godown Report</h3>
    <table>
    <thead>
        <th>S.No.</th>
        <th>District</th>
        <th>PACS</th>
        <th>Block Union</th>
        <th>Marketing</th>
        <th>Jila Sahkari</th>
        <th>Consumer</th>
    </thead>';

    while($row = mysqli_fetch_assoc($res)){
        $did = $row['sno'];

        /* UPCOD */
        // $up_total = count_rows($db_upcod,"
        //     SELECT DISTINCT society_id
        //     FROM survey_invoice si
        //     left JOIN test2 t ON t.sno = si.society_id
        //     WHERE si.approval_status = 6 AND t.col2='$did'
        // ");
        $up_godown = count_rows($db_upcod,"
            SELECT DISTINCT society_id
            FROM survey_invoice si
            JOIN test2 t ON t.sno = si.society_id
            left JOIN survey_invoice_sec_3_5 s ON s.survey_id = si.sno
            WHERE si.approval_status = 6 AND t.col2='$did' and s.suitable_godown = 'yes'
        ");

        // $b  = count_rows($db,"SELECT sno FROM block_union WHERE is_deleted!=1 AND janpad_name='$did'");
        $bg = count_rows($db,"SELECT sno FROM block_union WHERE is_deleted!=1 AND janpad_name='$did' AND godown_suitable='हाँ'");
        // $m  = count_rows($db,"SELECT sno FROM marketing WHERE is_deleted!=1 AND district_id='$did'");
        $mg = count_rows($db,"SELECT sno FROM marketing WHERE is_deleted!=1 AND district_id='$did' AND godown_suitable='हाँ'");
        // $j  = count_rows($db,"SELECT sno FROM jila_sehkari WHERE is_deleted!=1 AND janpad_name='$did'");
        $jg = count_rows($db,"SELECT sno FROM jila_sehkari WHERE is_deleted!=1 AND janpad_name='$did' AND godown_suitable='हाँ'");
        // $u  = count_rows($db,"SELECT sno FROM upss WHERE is_deleted!=1 AND janpad_name='$did'");
        $ug = count_rows($db,"SELECT sno FROM upss WHERE is_deleted!=1 AND janpad_name='$did' AND godown_suitable='हाँ'");

        $vals = [$up_godown,$bg,$mg,$jg,$ug];
        foreach($vals as $k=>$v){ $tot[$k]+=$v; }

        echo "<tr>
            <td>$i</td>
            <td>{$row['district_name']}</td>
            <td>$up_godown</td>
            <td>$bg</td>
            <td>$mg</td>
            <td>$jg</td>
            <td>$ug</td>
        </tr>";
        $i++;
    }

    echo "<tr class='total-row'>
        <td colspan='2'>TOTAL</td>";
    foreach($tot as $t){ echo "<td>$t</td>"; }
    echo "</tr></table>";
}

function division_report(){
    global $db, $db_upcod;

    $i = 1;
    $tot = array_fill(0, 5, 0);

    $res = mysqli_query($db,"SELECT * FROM master_division");

    echo '<h3>Division Wise Godown Report</h3>
    <table>
    <thead>
        <th>S.No.</th>
        <th>District</th>
        <th>PACS</th>
        <th>Block Union</th>
        <th>Marketing</th>
        <th>Jila Sahkari</th>
        <th>Consumer</th>
    </thead>';

    while($row = mysqli_fetch_assoc($res)){
        $vid = $row['sno'];

        /* UPCOD */
        // $up_total = count_rows($db_upcod,"
        //     SELECT DISTINCT society_id
        //     FROM survey_invoice si
        //     JOIN test2 t ON t.sno = si.society_id
        //     WHERE si.approval_status = 6 AND t.col1='$vid'
        // ");
        $up_godown = count_rows($db_upcod,"
            SELECT DISTINCT society_id
            FROM survey_invoice si
            JOIN test2 t ON t.sno = si.society_id
            left JOIN survey_invoice_sec_3_5 s ON s.survey_id = si.sno
            WHERE si.approval_status = 6 AND t.col1='$vid'
            and s.suitable_godown = 'yes'
        ");

        /* UPCDC */
        $b  = count_rows($db,"SELECT sno FROM block_union WHERE is_deleted!=1 AND mandal_name='$vid'");
        $bg = count_rows($db,"SELECT sno FROM block_union WHERE is_deleted!=1 AND mandal_name='$vid' AND godown_suitable='हाँ'");
        $m  = count_rows($db,"SELECT sno FROM marketing WHERE is_deleted!=1 AND division_id='$vid'");
        $mg = count_rows($db,"SELECT sno FROM marketing WHERE is_deleted!=1 AND division_id='$vid' AND godown_suitable='हाँ'");
        $j  = count_rows($db,"SELECT sno FROM jila_sehkari WHERE is_deleted!=1 AND mandal_name='$vid'");
        $jg = count_rows($db,"SELECT sno FROM jila_sehkari WHERE is_deleted!=1 AND mandal_name='$vid' AND godown_suitable='हाँ'");
        $u  = count_rows($db,"SELECT sno FROM upss WHERE is_deleted!=1 AND mandal_name='$vid'");
        $ug = count_rows($db,"SELECT sno FROM upss WHERE is_deleted!=1 AND mandal_name='$vid' AND godown_suitable='हाँ'");

        $vals = [$up_godown,$bg,$mg,$jg,$ug];
        foreach($vals as $k=>$v){ $tot[$k]+=$v; }

        echo "<tr>
            <td>$i</td>
            <td>{$row['division_name']}</td>
            <td>$up_godown</td>
            <td>$bg</td>
            <td>$mg</td>
            <td>$jg</td>
            <td>$ug</td>
        </tr>";
        $i++;
    }

    echo "<tr class='total-row'>
        <td colspan='2'>TOTAL</td>";
    foreach($tot as $t){ echo "<td>$t</td>"; }
    echo "</tr></table>";
}

function count_rows($db,$sql){
    return mysqli_num_rows(mysqli_query($db,$sql));
}
?>