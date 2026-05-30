<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("scripts/settings.php");

$society_type_map = [
    1 => 'block_union',
    2 => 'marketing',
    3 => 'upss',
    4 => 'jila_sehkari',
    5 => 'consumer',
];

// -------------------------------------------
// Helpers
// -------------------------------------------
function safe($val)
{
    global $db;
    return mysqli_real_escape_string($db, trim($val ?? ''));
}
function safe_decimal($val)
{
    $val = trim($val ?? '');
    return is_numeric($val) ? (float) $val : null;
}
function safe_int($val)
{
    $val = trim($val ?? '');
    return is_numeric($val) ? (int) $val : null;
}

function upload_image($file_key, $district_id, $block_id)
{
    if (
        isset($_FILES[$file_key]) &&
        $_FILES[$file_key]['error'] == 0 &&
        $_FILES[$file_key]['size'] > 0
    ) {
        $allowed = ['jpg', 'jpeg', 'gif', 'png', 'bmp'];
        $ext = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed))
            return null;

        $upload_dir = "user_data/{$district_id}/{$block_id}/";
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0755, true);

        $filename = time() . '_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $filename)) {
            return $filename;
        }
    }
    return null;
}

function to_sql_val($col, $val, $str_cols)
{
    global $db;
    if (is_null($val) || $val === '')
        return 'NULL';
    if (in_array($col, $str_cols))
        return "'" . mysqli_real_escape_string($db, $val) . "'";
    return $val;
}

// -------------------------------------------
// POST values
// -------------------------------------------
$society_id = safe_int($_POST['society_code'] ?? '');
$soc_type_sno = safe_int($_POST['society_type_sno'] ?? '');  // hidden field from form

if (!$society_id) {
    echo json_encode([['id' => 'error', 'error' => 'Invalid society ID']]);
    exit;
}

// -------------------------------------------
// survey_invoice se location info lena
// -------------------------------------------
// NOTE: Jab naya form fill ho rha ho, toh hum dropdowns se IDs bhejenge.
// Yahan hum try karenge query se info nikalne ki agar society_id survey_invoice (test2) me hai.
$society_type = $society_type_map[$soc_type_sno] ?? 'block_union';

if ($society_type === 'consumer') {
    $res_info = execute_query("SELECT col1, col2, col3, col5, col6 FROM test2 WHERE sno = '$society_id' LIMIT 1");
    if (mysqli_num_rows($res_info) > 0) {
        $info = mysqli_fetch_assoc($res_info);
        $division_id = (int) $info['col1'];
        $district_id = (int) $info['col2'];
        $soc_type_id = $soc_type_sno ?: (int) $info['col3'];
        $tehseel_id = (int) $info['col5'];
        $block_id = (int) $info['col6'];
    } else {
        $division_id = safe_int($_POST['division_id'] ?? '');
        $district_id = safe_int($_POST['district_id'] ?? '');
        $soc_type_id = $soc_type_sno;
        $tehseel_id = safe_int($_POST['tehseel_id'] ?? '');
        $block_id = safe_int($_POST['block_id'] ?? '');
    }
} else {
    $division_id = safe_int($_POST['division_id'] ?? '');
    $district_id = safe_int($_POST['district_id'] ?? '');
    $soc_type_id = $soc_type_sno;
    $tehseel_id = safe_int($_POST['tehseel_id'] ?? '');
    $block_id = safe_int($_POST['block_id'] ?? '');
}

// Already defined above

// -------------------------------------------
// 4 society tables se respective ID fetch karna
// -------------------------------------------
$block_union_id = null;
$marketing_id = null;
$upss_id = null;
$jila_sehkari_id = null;

switch ($society_type) {
    case 'block_union':
        $block_union_id = $society_id;
        break;
    case 'marketing':
        $marketing_id = $society_id;
        break;
    case 'upss':
        $upss_id = $society_id;
        break;
    case 'jila_sehkari':
        $jila_sehkari_id = $society_id;
        break;
}

// -------------------------------------------
// Form field -> DB column image mapping
// -------------------------------------------
$img_map = [
    'sec_6_a_img_1' => 'floor_img_1',
    'sec_6_a_img_2' => 'floor_img_2',
    'sec_6_a_img_3' => 'floor_img_3',
    'sec_6_a_img_4' => 'floor_img_4',
    'sec_6_b_img_1' => 'wall_img_1',
    'sec_6_b_img_2' => 'wall_img_2',
    'sec_6_b_img_3' => 'wall_img_3',
    'sec_6_b_img_4' => 'wall_img_4',
    'sec_6_c_img_1' => 'paint_img_1',
    'sec_6_c_img_2' => 'paint_img_2',
    'sec_6_c_img_3' => 'paint_img_3',
    'sec_6_c_img_4' => 'paint_img_4',
    'sec_6_d_img_1' => 'roof_img_1',
    'sec_6_d_img_2' => 'roof_img_2',
    'sec_6_d_img_3' => 'roof_img_3',
    'sec_6_d_img_4' => 'roof_img_4',
    'sec_6_e_img_1' => 'wr_floor_img_1',
    'sec_6_e_img_2' => 'wr_floor_img_2',
    'sec_6_f_img_1' => 'wr_plaster_img_1',
    'sec_6_f_img_2' => 'wr_plaster_img_2',
    'sec_6_g_img_1' => 'wr_roof_img_1',
    'sec_6_g_img_2' => 'wr_roof_img_2',
    'sec_6_h_img_1' => 'wr_seat_img_1',
    'sec_6_h_img_2' => 'wr_seat_img_2',
    'sec_6_i_img_1' => 'wr_plumbing_img_1',
    'sec_6_i_img_2' => 'wr_plumbing_img_2',
    'sec_6_j_img_1' => 'boundary_wall_img_1',
    'sec_6_j_img_2' => 'boundary_wall_img_2',
];

// Images upload karna
$uploaded = [];
foreach ($img_map as $form_field => $db_col) {
    $result = upload_image($form_field, $district_id, $block_id);
    if ($result !== null)
        $uploaded[$db_col] = $result;
}

// Combined string columns for proper SQL quoting
$str_cols = [
    'society_type',
    'washroom_floor',
    'washroom_plaster',
    'washroom_roof',
    'washroom_seat',
    'washroom_plumbing',
    'others',
    'latitude',
    'longitude',
    'society_name',
    'society_registration_no',
    'society_registration_date',
    'gst_no',
    'pan_no',
    'secretary_name',
    'secretary_mobile',
    'secretary_aadhar',
    'secretary_email',
    'built_building_status',
    'built_building_details',
    'boundary_wall_status',
    'floor_img_1',
    'floor_img_2',
    'floor_img_3',
    'floor_img_4',
    'wall_img_1',
    'wall_img_2',
    'wall_img_3',
    'wall_img_4',
    'paint_img_1',
    'paint_img_2',
    'paint_img_3',
    'paint_img_4',
    'roof_img_1',
    'roof_img_2',
    'roof_img_3',
    'roof_img_4',
    'wr_floor_img_1',
    'wr_floor_img_2',
    'wr_plaster_img_1',
    'wr_plaster_img_2',
    'wr_roof_img_1',
    'wr_roof_img_2',

    'wr_seat_img_1',
    'wr_seat_img_2',
    'wr_plumbing_img_1',
    'wr_plumbing_img_2',
    'boundary_wall_img_1',
    'boundary_wall_img_2'
];

// -------------------------------------------
// Data array banana
// -------------------------------------------
$data = [
    'society_id' => $society_id,
    'society_type' => $society_type,
    'block_union_id' => $block_union_id,
    'marketing_id' => $marketing_id,
    'upss_id' => $upss_id,
    'jila_sehkari_id' => $jila_sehkari_id,
    'division_id' => $division_id,
    'district_id' => $district_id,
    'tehseel_id' => $tehseel_id,
    'block_id' => $block_id,

    'floor_cost' => safe_decimal($_POST['sec_6_a_floor_cost'] ?? ''),
    'wall_cost' => safe_decimal($_POST['sec_6_a_wall_cost'] ?? ''),
    'paint_cost' => safe_decimal($_POST['sec_6_a_paint_cost'] ?? ''),
    'roof_cost' => safe_decimal($_POST['sec_6_a_roof_cost'] ?? ''),

    'washroom_floor' => safe($_POST['sec_6_e_floor'] ?? ''),
    'wr_floor_cost' => safe_decimal($_POST['sec_6_e_floor_cost'] ?? ''),

    'washroom_plaster' => safe($_POST['sec_6_e_plaster'] ?? ''),
    'wr_plaster_cost' => safe_decimal($_POST['sec_6_e_plaster_cost'] ?? ''),

    'washroom_roof' => safe($_POST['sec_6_e_ceiling'] ?? ''),
    'wr_roof_cost' => safe_decimal($_POST['sec_6_e_ceiling_cost'] ?? ''),

    'washroom_seat' => safe($_POST['sec_6_e_seat'] ?? ''),
    'wr_seat_cost' => safe_decimal($_POST['sec_6_e_seat_cost'] ?? ''),

    'washroom_plumbing' => safe($_POST['sec_6_e_plumbing'] ?? ''),
    'wr_plumbing_cost' => safe_decimal($_POST['sec_6_e_plumbing_cost'] ?? ''),

    'doors' => safe_int($_POST['sec_6_f_number_of_door'] ?? ''),
    'door_cost' => safe_decimal($_POST['sec_6_f_door_cost'] ?? ''),
    'windows' => safe_int($_POST['sec_6_g_number_of_window'] ?? ''),
    'window_cost' => safe_decimal($_POST['sec_6_f_window_cost'] ?? ''),

    'plaster_wall' => safe_decimal($_POST['sec_6_h_length'] ?? ''),
    'plaster_roof' => safe_decimal($_POST['sec_6_h_width'] ?? ''),
    'others' => safe($_POST['sec_6_i_other'] ?? ''),
    'latitude' => safe($_POST['sec_1_latitude'] ?? ''),
    'longitude' => safe($_POST['sec_1_longitude'] ?? ''),

    // New fields for maintenance table (if columns exist)
    'society_name' => safe($_POST['society_name'] ?? ''),
    'society_registration_no' => safe($_POST['society_registration_no'] ?? ''),
    'society_registration_date' => safe($_POST['society_registration_date'] ?? ''),
    'gst_no' => safe($_POST['gst_no'] ?? ''),
    'pan_no' => safe($_POST['pan_no'] ?? ''),
    'secretary_name' => safe($_POST['secretary_name'] ?? ''),
    'secretary_mobile' => safe($_POST['secretary_mobile'] ?? ''),
    'secretary_aadhar' => safe($_POST['secretary_aadhar'] ?? ''),
    'secretary_email' => safe($_POST['secretary_email'] ?? ''),
    'built_building_status' => safe($_POST['sec_5_built_building'] ?? ''),
    'built_building_details' => safe($_POST['sec_5_detailed_information'] ?? ''),
    'boundary_wall_status' => safe($_POST['sec_6_j_boundary_wall'] ?? ''),
    'boundary_wall_cost' => safe_decimal($_POST['sec_6_j_boundary_wall_cost'] ?? ''),
    'status' => 1,

];

// -------------------------------------------
// Respective Society table Update (DEACTIVATED: Saving ONLY to maintenance table now)
// -------------------------------------------
/*
$soc_table = "";
switch ($society_type) {
    case 'block_union':
        $soc_table = "block_union";
        break;
    case 'marketing':
        $soc_table = "marketing";
        break;
    case 'upss':
        $soc_table = "upss";
        break;
    case 'jila_sehkari':
        $soc_table = "jila_sehkari";
        break;
}

if ($soc_table != "") {
    $soc_reg_no = safe($_POST['society_registration_no'] ?? '');
    $soc_reg_date = safe($_POST['society_registration_date'] ?? '');
    $gst = safe($_POST['gst_no'] ?? '');
    $pan = safe($_POST['pan_no'] ?? '');
    $s_name = safe($_POST['secretary_name'] ?? '');
    $s_mobile = safe($_POST['secretary_mobile'] ?? '');
    $s_aadhar = safe($_POST['secretary_aadhar'] ?? '');
    $s_email = safe($_POST['secretary_email'] ?? '');

    $sql_soc_update = "UPDATE `$soc_table` SET 
                        `society_registration_no` = '$soc_reg_no',
                        `society_registration_date` = '$soc_reg_date',
                        `gst_no` = '$gst',
                        `pan_no` = '$pan',
                        `secretary_name` = '$s_name',
                        `secretary_mobile` = '$s_mobile',
                        `secretary_aadhar` = '$s_aadhar',
                        `secretary_email` = '$s_email'
                      WHERE `sno` = '$society_id'";
    execute_query($sql_soc_update);
}
*/

// Uploaded images merge karna
$data = array_merge($data, $uploaded);

// -------------------------------------------
// (Consolidated at the top)

// -------------------------------------------
// INSERT ya UPDATE check karna
// -------------------------------------------
$survey_id = safe_int($_POST['survey_id'] ?? '');

$is_update = false;
if ($survey_id) {
    $chk = execute_query("SELECT sno FROM maintenance WHERE sno = '$survey_id' LIMIT 1");
    if (mysqli_num_rows($chk) > 0) {
        $is_update = true;
    }
}

if (!$is_update && $society_id) {
    // Check if society already has a record (fallback)
    $chk = execute_query("SELECT sno FROM maintenance WHERE society_id = '$society_id' AND society_type = '$society_type' LIMIT 1");
    if (mysqli_num_rows($chk) > 0) {
        $row = mysqli_fetch_assoc($chk);
        $survey_id = $row['sno'];
        $is_update = true;
    }
}

if ($is_update) {
    // ---- UPDATE ----
    $set = [];
    $img_db_cols = array_values($img_map);

    foreach ($data as $col => $val) {
        if ($col === 'society_id')
            continue;
        // Purani image preserve karo agar naya upload nahi hua
        if (in_array($col, $img_db_cols) && !isset($uploaded[$col]))
            continue;

        $set[] = "`$col` = " . to_sql_val($col, $val, $str_cols);
    }

    $sql = "UPDATE `maintenance` SET " . implode(', ', $set) . " WHERE `sno` = '$survey_id'";
    execute_query($sql);
    $visit_id = $survey_id;
} else {
    // ---- INSERT ----
    $cols = [];
    $vals = [];
    foreach ($data as $col => $val) {
        $cols[] = "`$col`";
        $vals[] = to_sql_val($col, $val, $str_cols);
    }

    $sql = "INSERT INTO `maintenance` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
    execute_query($sql);
    $visit_id = mysqli_insert_id($db);
}

if (mysqli_error($db)) {
    echo json_encode([['id' => 'error', 'error' => 'Saving Error: ' . mysqli_error($db)]]);
} else {
    // -------------------------------------------
    // Save Financial & Committee info to new tables
    // -------------------------------------------
    if ($visit_id) {
        // 1. Financial Info
        $f_year = safe($_POST['sec_3_santulan_patra'] ?? '');
        $f_p1 = safe($_POST['profit_loss_1'] ?? '');
        $f_a1 = safe_decimal($_POST['profit_loss_amount_1'] ?? '');
        $f_ac1 = safe($_POST['accumulated_1'] ?? '');
        $f_aca1 = safe_decimal($_POST['accumulated_amount_1'] ?? '');
        $f_p2 = safe($_POST['profit_loss_2'] ?? '');
        $f_a2 = safe_decimal($_POST['profit_loss_amount_2'] ?? '');
        $f_ac2 = safe($_POST['accumulated_2'] ?? '');
        $f_aca2 = safe_decimal($_POST['accumulated_amount_2'] ?? '');

        $chk_f = execute_query("SELECT sno FROM maintenance_financial_info WHERE maintenance_sno = '$visit_id' LIMIT 1");
        if (mysqli_num_rows($chk_f) > 0) {
            $sql_f = "UPDATE maintenance_financial_info SET 
                        society_id = '$society_id',
                        society_type = '$society_type',
                        sec_3_santulan_patra = '$f_year',
                        profit_loss_1 = '$f_p1',
                        profit_loss_amount_1 = " . ($f_a1 === null ? "NULL" : "'$f_a1'") . ",
                        accumulated_1 = '$f_ac1',
                        accumulated_amount_1 = " . ($f_aca1 === null ? "NULL" : "'$f_aca1'") . ",
                        profit_loss_2 = '$f_p2',
                        profit_loss_amount_2 = " . ($f_a2 === null ? "NULL" : "'$f_a2'") . ",
                        accumulated_2 = '$f_ac2',
                        accumulated_amount_2 = " . ($f_aca2 === null ? "NULL" : "'$f_aca2'") . "
                      WHERE maintenance_sno = '$visit_id'";
        } else {
            $sql_f = "INSERT INTO maintenance_financial_info (maintenance_sno, society_id, society_type, sec_3_santulan_patra, profit_loss_1, profit_loss_amount_1, accumulated_1, accumulated_amount_1, profit_loss_2, profit_loss_amount_2, accumulated_2, accumulated_amount_2)
                      VALUES ('$visit_id', '$society_id', '$society_type', '$f_year', '$f_p1', " . ($f_a1 === null ? "NULL" : "'$f_a1'") . ", '$f_ac1', " . ($f_aca1 === null ? "NULL" : "'$f_aca1'") . ", '$f_p2', " . ($f_a2 === null ? "NULL" : "'$f_a2'") . ", '$f_ac2', " . ($f_aca2 === null ? "NULL" : "'$f_aca2'") . ")";
        }
        execute_query($sql_f);

        // 2. Committee Info
        $m_elected = safe($_POST['sec_6_2_mgt_committee_is_elected'] ?? '');
        $m_e_year = safe_int($_POST['sec_6_2_election_year'] ?? '');
        $m_end_year = safe_int($_POST['sec_6_2_end_year'] ?? '');
        $m_count = safe_int($_POST['sec_6_2_member_count'] ?? 1);

        execute_query("DELETE FROM maintenance_committee_info WHERE maintenance_sno = '$visit_id'");
        for ($i = 1; $i <= 10; $i++) {
            $desig = safe($_POST["sec_6_2_designation_$i"] ?? '');
            $name = safe($_POST["sec_6_2_name_$i"] ?? '');
            $fname = safe($_POST["sec_6_2_father_name_$i"] ?? '');
            $mob = safe($_POST["sec_6_2__mob_no_$i"] ?? '');
            if ($desig || $name || $mob) {
                $sql_c = "INSERT INTO maintenance_committee_info (maintenance_sno, society_id, society_type, mgt_committee_is_elected, election_year, end_year, designation, name, father_name, mobile_no)
                          VALUES ('$visit_id', '$society_id', '$society_type', '$m_elected', " . ($m_e_year ?: 'NULL') . ", " . ($m_end_year ?: 'NULL') . ", '$desig', '$name', '$fname', '$mob')";
                execute_query($sql_c);
            }
        }
    }
    echo json_encode([['id' => 'success', 'survey_id' => $visit_id, 'msg' => 'Data saved successfully']]);
}