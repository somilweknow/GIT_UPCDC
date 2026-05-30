<?php
include("settings.php");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
$soc_map = [1 => 'block_union', 2 => 'marketing', 3 => 'upss', 4 => 'jila_sehkari', 5 => 'consumer'];
function safe($val) { global $db; return mysqli_real_escape_string($db, trim($val ?? '')); }
function safe_decimal($val) { $val = trim($val ?? ''); return is_numeric($val) ? (float) $val : null; }
function safe_int($val) { $val = trim($val ?? ''); return (!is_numeric($val) || $val === '') ? null : intval($val); }
function upload_image($file_key, $society_type, $society_id, $name_prefix) {

    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0 && $_FILES[$file_key]['size'] > 0) {

        $allowed = ['jpg','jpeg','png','gif','bmp'];
        $ext = strtolower(pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            return null;
        }

        $upload_dir = dirname(__DIR__) . "/user_data/maintainance/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = $society_type . '_' . $society_id . '_' . $name_prefix . '.' . $ext;

        if (file_exists($upload_dir . $filename)) {
            unlink($upload_dir . $filename);
        }

        if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $upload_dir . $filename)) {
            return $filename;
        }
    }

    return null;
}
function to_sql_val($col, $val, $str_cols) { global $db; if (is_null($val) || $val === '') return 'NULL'; if (in_array($col, $str_cols)) return "'" . mysqli_real_escape_string($db, $val) . "'"; return $val; }

$society_id = safe_int($_POST['society_code'] ?? '');
$soc_type_sno = safe_int($_POST['society_type_sno'] ?? '');

if (!$society_id || !$soc_type_sno) {
    echo json_encode([['id' => 'error', 'error' => 'Invalid society ID or type']]);
    exit;
}

$society_type = $soc_map[$soc_type_sno] ?? 'block_union';
$division_id = safe_int($_POST['division_id'] ?? '');
$district_id = safe_int($_POST['district_id'] ?? '');
$tehseel_id = safe_int($_POST['tehseel_id'] ?? '');
$block_id = safe_int($_POST['block_id'] ?? '');

$block_union_id = ($society_type === 'block_union') ? $society_id : null;
$marketing_id = ($society_type === 'marketing') ? $society_id : null;
$upss_id = ($society_type === 'upss') ? $society_id : null;
$jila_sehkari_id = ($society_type === 'jila_sehkari') ? $society_id : null;

$img_map = [
    'sec_6_a_img_1' => 'floor_img_1', 'sec_6_a_img_2' => 'floor_img_2', 'sec_6_a_img_3' => 'floor_img_3', 'sec_6_a_img_4' => 'floor_img_4',
    'sec_6_b_img_1' => 'wall_img_1', 'sec_6_b_img_2' => 'wall_img_2', 'sec_6_b_img_3' => 'wall_img_3', 'sec_6_b_img_4' => 'wall_img_4',
    'sec_6_c_img_1' => 'paint_img_1', 'sec_6_c_img_2' => 'paint_img_2', 'sec_6_c_img_3' => 'paint_img_3', 'sec_6_c_img_4' => 'paint_img_4',
    'sec_6_d_img_1' => 'roof_img_1', 'sec_6_d_img_2' => 'roof_img_2', 'sec_6_d_img_3' => 'roof_img_3', 'sec_6_d_img_4' => 'roof_img_4',
    'sec_6_e_img_1' => 'wr_floor_img_1', 'sec_6_e_img_2' => 'wr_floor_img_2',
    'sec_6_f_img_1' => 'wr_plaster_img_1', 'sec_6_f_img_2' => 'wr_plaster_img_2',
    'sec_6_g_img_1' => 'wr_roof_img_1', 'sec_6_g_img_2' => 'wr_roof_img_2',
    'sec_6_h_img_1' => 'wr_seat_img_1', 'sec_6_h_img_2' => 'wr_seat_img_2',
    'sec_6_i_img_1' => 'wr_plumbing_img_1', 'sec_6_i_img_2' => 'wr_plumbing_img_2',
    'sec_6_j_img_1' => 'boundary_wall_img_1', 'sec_6_j_img_2' => 'boundary_wall_img_2'
];

$uploaded = [];
foreach ($img_map as $form_field => $db_col) {
    $result = upload_image($form_field, $society_type, $society_id, $db_col);
    if ($result !== null) {
        $uploaded[$db_col] = $result;
    }
}

$str_cols = ['society_type', 'washroom_floor', 'washroom_plaster', 'washroom_roof', 'washroom_seat', 'washroom_plumbing', 'others', 'latitude', 'longitude', 'society_name', 'society_registration_no', 'society_registration_date', 'gst_no', 'pan_no', 'secretary_name', 'secretary_mobile', 'secretary_aadhar', 'secretary_email', 'built_building_status', 'built_building_details', 'boundary_wall_status', 'floor_img_1', 'floor_img_2', 'floor_img_3', 'floor_img_4', 'wall_img_1', 'wall_img_2', 'wall_img_3', 'wall_img_4', 'paint_img_1', 'paint_img_2', 'paint_img_3', 'paint_img_4', 'roof_img_1', 'roof_img_2', 'roof_img_3', 'roof_img_4', 'wr_floor_img_1', 'wr_floor_img_2', 'wr_plaster_img_1', 'wr_plaster_img_2', 'wr_roof_img_1', 'wr_roof_img_2', 'wr_seat_img_1', 'wr_seat_img_2', 'wr_plumbing_img_1', 'wr_plumbing_img_2', 'boundary_wall_img_1', 'boundary_wall_img_2'];

$data = [
    'society_id' => $society_id,
    'society_type' => $society_type,
    'block_union_id' => $block_union_id,
    'marketing_id' => $marketing_id,
    'upss_id' => $upss_id,
    'jila_sehkari_id' => $jila_sehkari_id,
    'division_id' => $division_id,
    'district_id' => $district_id,
    'floor_length' => safe_decimal($_POST['sec_6_a_length'] ?? ''),
    'floor_width' => safe_decimal($_POST['sec_6_a_width'] ?? ''),
    'floor_cost' => safe_decimal($_POST['sec_6_a_floor_cost'] ?? ''),
    'wall_length' => safe_decimal($_POST['sec_6_b_length'] ?? ''),
    'wall_width' => safe_decimal($_POST['sec_6_b_width'] ?? ''),
    'wall_cost' => safe_decimal($_POST['sec_6_a_wall_cost'] ?? ''),
    'paint_length' => safe_decimal($_POST['sec_6_c_length'] ?? ''),
    'paint_width' => safe_decimal($_POST['sec_6_c_width'] ?? ''),
    'paint_cost' => safe_decimal($_POST['sec_6_a_paint_cost'] ?? ''),
    'roof_length' => safe_decimal($_POST['sec_6_d_length'] ?? ''),
    'roof_width' => safe_decimal($_POST['sec_6_d_width'] ?? ''),
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
    'status' => 1
];
$data = array_merge($data, $uploaded);

$survey_id = safe_int($_POST['survey_id'] ?? '');
$is_update = false;

if ($survey_id) {
    $chk = execute_query("SELECT sno FROM maintenance WHERE sno = '$survey_id' LIMIT 1");
    if (mysqli_num_rows($chk) > 0) $is_update = true;
}

if (!$is_update && $society_id) {
    $chk = execute_query("SELECT sno FROM maintenance WHERE society_id = '$society_id' AND society_type = '$society_type' LIMIT 1");
    if (mysqli_num_rows($chk) > 0) {
        $row = mysqli_fetch_assoc($chk);
        $survey_id = $row['sno'];
        $is_update = true;
    }
}

if ($is_update) {
    $set = [];
    $img_db_cols = array_values($img_map);
    foreach ($data as $col => $val) {
        if ($col === 'society_id') continue;
        if (in_array($col, $img_db_cols) && !isset($uploaded[$col])) continue;
        $set[] = "`$col` = " . to_sql_val($col, $val, $str_cols);
    }
    $sql = "UPDATE `maintenance` SET " . implode(', ', $set) . " WHERE `sno` = '$survey_id'";
    execute_query($sql);
    $visit_id = $survey_id;
} else {
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
    exit;
}

if ($visit_id) {
    execute_query("DELETE FROM maintenance_financial_info WHERE maintenance_sno='$visit_id'");

    if (!empty($_POST['financial_year'])) {

        foreach ($_POST['financial_year'] as $k => $fy) {

            $financial_year = safe($fy);

            $fy_profit_loss      = safe($_POST['fy_profit_loss'][$k] ?? '');
            $fy_profit_loss_amt  = safe_decimal($_POST['fy_profit_loss_amt'][$k] ?? '');

            $comm_profit_loss     = safe($_POST['comm_profit_loss'][$k] ?? '');
            $comm_profit_loss_amt = safe_decimal($_POST['comm_profit_loss_amt'][$k] ?? '');

            if (
                $financial_year != '' ||
                $fy_profit_loss != '' ||
                $comm_profit_loss != ''
            ) {

                $sql = "INSERT INTO maintenance_financial_info
                SET
                maintenance_sno='$visit_id',
                society_id='$society_id',
                society_type='$society_type',
                financial_year='$financial_year',
                fy_profit_loss='$fy_profit_loss',
                fy_profit_loss_amt=" . ($fy_profit_loss_amt === null ? "NULL" : $fy_profit_loss_amt) . ",
                comm_profit_loss='$comm_profit_loss',
                comm_profit_loss_amt=" . ($comm_profit_loss_amt === null ? "NULL" : $comm_profit_loss_amt);

                execute_query($sql);
            }
        }
    }

    execute_query("DELETE FROM maintenance_committee_info WHERE maintenance_sno = '$visit_id'");
    $m_elected = safe($_POST['sec_6_2_mgt_committee_is_elected'] ?? '');
    $m_e_year = safe_int($_POST['sec_6_2_election_year'] ?? '');
    $m_end_year = safe_int($_POST['sec_6_2_end_year'] ?? '');
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

echo json_encode([['id' => 'success', 'survey_id' => $visit_id, 'visit_id' => $visit_id, 'msg' => 'Data saved successfully']]);
?>
