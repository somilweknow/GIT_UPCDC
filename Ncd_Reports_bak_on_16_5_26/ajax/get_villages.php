<?php
include("../../scripts/settings.php");

$gp_code = $_GET['gp_code'] ?? '';

$res = execute_query("
    SELECT village_code, village_name 
    FROM ncd_state_district_block_gp_village
    WHERE gram_panchayat_code = '$gp_code'
");

$data = [];

while($r = mysqli_fetch_assoc($res)){
    $data[] = $r;
}

echo json_encode($data);