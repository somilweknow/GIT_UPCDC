<?php
error_reporting(0);
set_time_limit(0);
ini_set('memory_limit', '-1');

while (ob_get_level()) ob_end_clean();

include("../scripts/settings.php");
include("helpers/filter_builder.php");

// 🔥 BUILD FILTERS
$where = buildCooperativeFilters($_GET);

// 📥 Headers
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="cooperatives_' . date('Ymd_His') . '.xls"');

$output = fopen('php://output', 'w');

// BOM
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Column format
function formatColumnName($col)
{
    return ucwords(str_replace('_', ' ', $col));
}

// 🔥 MAIN QUERY (ALL COLUMNS + OVERRIDES)
$query = "
SELECT 

-- 🔥 ALL BASE COLUMNS
c.*,

-- 🔁 OVERRIDE BOOLEAN FIELDS
CASE WHEN c.is_approved = 1 THEN 'Yes' ELSE 'No' END AS is_approved,
CASE WHEN c.functional_status = 1 THEN 'Functional' ELSE 'Non Functional' END AS functional_status,
CASE WHEN c.is_coastal = 1 THEN 'Yes' ELSE 'No' END AS is_coastal,
CASE WHEN c.is_affiliated_union_federation = 1 THEN 'Yes' ELSE 'No' END AS is_affiliated_union_federation,
CASE WHEN c.financial_audit = 1 THEN 'Yes' ELSE 'No' END AS financial_audit,
CASE WHEN c.is_profit_making = 1 THEN 'Yes' ELSE 'No' END AS is_profit_making,
CASE WHEN c.is_dividend_paid = 1 THEN 'Yes' ELSE 'No' END AS is_dividend_paid,
CASE WHEN c.full_time_secretary = 1 THEN 'Yes' ELSE 'No' END AS full_time_secretary,

-- 🔁 LOCATION
CASE 
    WHEN c.location_of_head_quarter = 1 THEN 'Urban'
    WHEN c.location_of_head_quarter = 2 THEN 'Rural'
    ELSE 'N/A'
END AS location_of_head_quarter,

-- 🔁 JOINS (Reference data)
CONCAT(ra.authority_name, ' (', c.registration_authoritie_id, ')') AS registration_authoritie_id,
CONCAT(st.society_type_name, ' (', c.cooperative_society_type_id, ')') AS cooperative_society_type_id,
CONCAT(aom.name, ' (', c.area_of_operation_id, ')') AS area_of_operation_id,
CONCAT(wbt.name, ' (', c.water_body_type_id, ')') AS water_body_type_id,
CONCAT(sm.name, ' (', c.sector_of_operation, ')') AS sector_of_operation,
CONCAT(oal.name, ' (', c.operation_area_location, ')') AS operation_area_location,
CONCAT(d.district_name, ' (', c.district_code, ')') AS district_code,
CONCAT(b.block_name, ' (', c.block_code, ')') AS block_code,
CONCAT(gp.gram_panchayat_name, ' (', c.gram_panchayat_code, ')') AS gram_panchayat_code,
CONCAT(st2.name, ' (', c.state_code, ')') AS state_code

FROM cooperatives c

LEFT JOIN registration_authorities_master ra 
    ON c.registration_authoritie_id = ra.id

LEFT JOIN master_society_type st 
    ON c.cooperative_society_type_id = st.society_type_id

LEFT JOIN area_of_operations_master aom 
    ON c.area_of_operation_id = aom.id

LEFT JOIN water_body_types_master wbt
    ON c.water_body_type_id = wbt.id

LEFT JOIN sector_master sm
    ON c.sector_of_operation = sm.id

LEFT JOIN area_of_operations_master oal
    ON c.operation_area_location = oal.id

LEFT JOIN districts_master d 
    ON c.district_code = d.district_code

LEFT JOIN blocks_master b 
    ON c.block_code = b.block_code

LEFT JOIN gp_villages_master gp 
    ON c.gram_panchayat_code = gp.gram_panchayat_code

LEFT JOIN states_master st2
    ON c.state_code = st2.state_code

$where
ORDER BY c.id DESC
";

$res = execute_query($query);

if (!$res) {
    die("SQL Error: " . mysqli_error($GLOBALS['db']));
}

// 🔥 FIRST ROW
$first = mysqli_fetch_assoc($res);

if (!$first) {
    // No data message
    fputcsv($output, ["No data found with selected filters"]);
    fclose($output);
    exit;
}

// ❌ REMOVE unwanted columns
unset($first['created_at'], $first['updated_at']);

// HEADER
fputcsv($output, array_map('formatColumnName', array_keys($first)));

// FIRST ROW
fputcsv($output, $first);

// LOOP
$counter = 0;

while ($row = mysqli_fetch_assoc($res)) {

    unset($row['created_at'], $row['updated_at']);

    foreach ($row as $k => $v) {
        if ($v === null || $v === '') {
            $row[$k] = 'N/A';
        }
    }

    fputcsv($output, $row);

    if (++$counter % 2000 === 0) {
        fflush($output);
    }
}

fclose($output);
exit;
