<?php
include("../scripts/settings.php");
include("helpers/value_mapper.php");
include("helpers/filter_builder.php");

header('Content-Type: application/json');

$request = $_REQUEST;

// columns
$columns = [];
$resCols = execute_query("SHOW COLUMNS FROM cooperatives");

while ($c = mysqli_fetch_assoc($resCols)) {
    if (in_array($c['Field'], ['created_at', 'updated_at'])) continue;
    $columns[] = $c['Field'];
}

// ✅ BUILD FILTERS (uses filter_builder.php)
$where = buildCooperativeFilters($request);

// 🔍 SEARCH
if (!empty($request['search']['value'])) {

    $search = addslashes(trim($request['search']['value']));
    $searchParts = [];

    foreach ($columns as $col) {
        $searchParts[] = "c.$col LIKE '%$search%'";
    }

    $where .= " AND (" . implode(" OR ", $searchParts) . ")";
}

// totals
$total = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) t FROM cooperatives c"))['t'];
$totalFiltered = mysqli_fetch_assoc(execute_query("SELECT COUNT(*) t FROM cooperatives c $where"))['t'];

// order
$orderCol = $columns[intval($request['order'][0]['column'] ?? 0)] ?? 'id';
$orderDir = $request['order'][0]['dir'] ?? 'desc';

// limit
$start = intval($request['start'] ?? 0);
$length = intval($request['length'] ?? 25);

// query
$res = execute_query("
    SELECT c.*
    FROM cooperatives c
    $where
    ORDER BY c.$orderCol $orderDir
    LIMIT $start, $length
");

$data = [];

while ($row = mysqli_fetch_assoc($res)) {

    foreach ($row as $col => $val) {
        $row[$col] = mapDisplayValue($col, $val);
    }

    $data[] = $row;
}

echo json_encode([
    "draw" => intval($request['draw'] ?? 0),
    "recordsTotal" => $total,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
]);

