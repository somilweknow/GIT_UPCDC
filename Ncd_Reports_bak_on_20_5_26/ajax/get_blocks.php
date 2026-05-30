<?php
include("../../scripts/settings.php");

$district_code = $_GET['district_code'] ?? '';

$res = execute_query("
    SELECT block_code, name 
    FROM ncd_blocks 
    WHERE district_code = '$district_code'
");

$data = [];

while($r = mysqli_fetch_assoc($res)){
    $data[] = $r;
}

echo json_encode($data);