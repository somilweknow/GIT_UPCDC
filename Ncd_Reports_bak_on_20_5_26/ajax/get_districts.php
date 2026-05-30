<?php
include("../../scripts/settings.php");

$state_code = $_GET['state_code'] ?? '';

$res = execute_query("
    SELECT district_code, district_name 
    FROM ncd_districts 
    WHERE state_code = '$state_code'
");

$data = [];

while($r = mysqli_fetch_assoc($res)){
    $data[] = $r;
}

echo json_encode($data);