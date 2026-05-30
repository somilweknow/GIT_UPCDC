<?php
include("../../scripts/settings.php");

$block_code = $_GET['block_code'] ?? '';

$res = execute_query("
    SELECT DISTINCT gram_panchayat_code, gram_panchayat_name 
    FROM ncd_state_district_block_gp_village
    WHERE block_code = '$block_code'
");

$data = [];

while($r = mysqli_fetch_assoc($res)){
    $data[] = $r;
}

echo json_encode($data);