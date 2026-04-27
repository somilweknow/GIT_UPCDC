<?php
include("scripts/settings.php");

header('Content-Type: application/json');

if (!isset($_GET['division_id']) || !is_numeric($_GET['division_id'])) {
    echo json_encode([]);
    exit;
}

$division_id = intval($_GET['division_id']);

$res = execute_query("SELECT sno, district_name FROM master_district WHERE division_id='$division_id' ORDER BY district_name");

$districts = [];
while ($row = mysqli_fetch_assoc($res)) {
    $districts[] = $row;
}

echo json_encode($districts);
exit;
?>
