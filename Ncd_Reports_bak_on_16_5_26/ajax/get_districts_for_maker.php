<?php
include(__DIR__ . "/../../scripts/settings.php");

$division_id = $_GET['division_id'] ?? '';

if (!$division_id) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT md.sno, md.district_name, nd.district_code
        FROM master_district md
        LEFT JOIN ncd_districts nd 
        ON LOWER(md.district_name) = LOWER(nd.district_name)
        WHERE md.division_id = '$division_id'
        ORDER BY md.district_name ASC";

$res = execute_query($sql);

$data = [];

while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode($data);