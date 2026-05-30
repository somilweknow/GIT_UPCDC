<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '-1');

while (ob_get_level()) ob_end_clean();

include("../scripts/settings.php");
include("helpers/filter_builder.php");

$where = buildCooperativeFilters($_GET);

if (!empty($_GET['district_ids'])) {
    $ids = array_filter(explode(',', $_GET['district_ids']));
    if (!empty($ids)) {
        $safeIds = array_map('intval', $ids);

        if (stripos($where, 'WHERE') !== false) {
            $where .= " AND c.district_code IN (" . implode(',', $safeIds) . ")";
        } else {
            $where .= " WHERE c.district_code IN (" . implode(',', $safeIds) . ")";
        }
    }
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="cooperatives_' . date('Ymd_His') . '.csv"');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

function formatColumnName($col)
{
    return ucwords(str_replace('_', ' ', $col));
}

$query = "
SELECT 

c.cooperative_id,
c.cooperative_society_name,

ra.authority_name AS registration_authority,
st.name AS cooperative_society_type,
aom.name AS area_of_operation,

CASE WHEN c.is_approved = 1 THEN 'Yes' ELSE 'No' END AS is_approved,
CASE WHEN c.functional_status = 1 THEN 'Functional' ELSE 'Non Functional' END AS functional_status,
CASE WHEN c.full_time_secretary = 1 THEN 'Yes' ELSE 'No' END AS full_time_secretary,
CASE WHEN c.is_coastal = 1 THEN 'Yes' ELSE 'No' END AS is_coastal,
CASE WHEN c.is_affiliated_union_federation = 1 THEN 'Yes' ELSE 'No' END AS is_affiliated_union_federation,
CASE WHEN c.financial_audit = 1 THEN 'Yes' ELSE 'No' END AS financial_audit,
CASE WHEN c.is_profit_making = 1 THEN 'Yes' ELSE 'No' END AS is_profit_making,
CASE WHEN c.is_dividend_paid = 1 THEN 'Yes' ELSE 'No' END AS is_dividend_paid,

CASE 
    WHEN c.location_of_head_quarter = 1 THEN 'Urban'
    WHEN c.location_of_head_quarter = 2 THEN 'Rural'
    ELSE 'N/A'
END AS location_of_head_quarter,

wbt.name AS water_body_type,
sm.name AS sector_of_operation,
oal.name AS operation_area_location,

st2.name AS state,
d.district_name AS district,
b.name AS block,
gp.gram_panchayat_name AS gram_panchayat,

c.reference_year,
c.pincode,
c.full_address,
c.mobile,
c.email

FROM ncd_cooperative_registrations c

LEFT JOIN ncd_registration_authorities ra 
    ON c.registration_authoritie_id = ra.id

LEFT JOIN ncd_cooperative_society_types st 
    ON c.cooperative_society_type_id = st.id

LEFT JOIN ncd_area_of_operations aom 
    ON c.area_of_operation_id = aom.id

LEFT JOIN ncd_water_body_types wbt
    ON c.water_body_type_id = wbt.id

LEFT JOIN ncd_sectors sm
    ON c.sector_of_operation = sm.id

LEFT JOIN ncd_area_of_operations oal
    ON c.operation_area_location = oal.id

LEFT JOIN ncd_states st2
    ON c.state_code = st2.state_code

LEFT JOIN ncd_districts d 
    ON c.district_code = d.district_code

LEFT JOIN ncd_blocks b 
    ON c.block_code = b.block_code

LEFT JOIN ncd_state_district_block_gp_village gp 
    ON c.gram_panchayat_code = gp.gram_panchayat_code

$where

ORDER BY c.id DESC
";

$res = execute_query($query);

if (!$res) {
    die("SQL Error: " . mysqli_error($GLOBALS['db']));
}

$first = mysqli_fetch_assoc($res);

if (!$first) {
    fputcsv($output, ["No data found with selected filters"]);
    fclose($output);
    exit;
}


$headers = array_keys($first);
array_unshift($headers, 'Sr No');
fputcsv($output, array_map('formatColumnName', $headers));

$sr = 1;
array_unshift($first, $sr);
fputcsv($output, $first);

$sr++;

while ($row = mysqli_fetch_assoc($res)) {

    foreach ($row as $k => $v) {
        if ($v === null || $v === '') {
            $row[$k] = 'N/A';
        }
    }

    array_unshift($row, $sr);
    fputcsv($output, $row);
    $sr++;
}

fclose($output);
exit;