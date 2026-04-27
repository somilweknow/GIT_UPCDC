<?php
include("../scripts/settings.php");
error_reporting(E_ALL);

/**
 * Compresses and resizes an image to keep it under KB size.
 */
function compressImage($source, $destination, $quality = 60)
{
    if (!extension_loaded('gd')) {
        return move_uploaded_file($source, $destination);
    }

    $info = getimagesize($source);
    if (!$info)
        return move_uploaded_file($source, $destination);

    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source);
            break;
        case 'image/gif':
            $image = @imagecreatefromgif($source);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source);
            break;
        default:
            return move_uploaded_file($source, $destination);
    }

    if (!$image)
        return move_uploaded_file($source, $destination);

    // Resize if too large (Max width 1200px)
    $max_dim = 1200;
    if ($width > $max_dim || $height > $max_dim) {
        if ($width > $height) {
            $new_width = $max_dim;
            $new_height = ($height / $width) * $max_dim;
        } else {
            $new_height = $max_dim;
            $new_width = ($width / $height) * $max_dim;
        }
        $new_image = imagecreatetruecolor($new_width, $new_height);

        // Preserve transparency for PNG/GIF
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);

        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($image);
        $image = $new_image;
    }

    // Convert everything to JPEG for consistent compression
    $result = imagejpeg($image, $destination, $quality);
    imagedestroy($image);
    return $result;
}

$response = array();

// Security: Only AR or Sadmin can submit (copied from cold_storage.php)
if (!isset($_SESSION['username']) || !($_SESSION['user_type'] == 'ar' || $_SESSION['usertype'] == '3' || $_SESSION['usertype'] == 'sadmin')) {
    $response = array("status" => "error", "msg" => "Access Denied.");
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $db;

    // Schema updates (ensure table and columns exist)
    $create_sql = "CREATE TABLE IF NOT EXISTS cold_storage_entries (id INT AUTO_INCREMENT PRIMARY KEY) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    execute_query($create_sql);

    $cols = [
        'latitude' => 'VARCHAR(50)', 'longitude' => 'VARCHAR(50)',
        'district_name' => 'VARCHAR(100)', 'tehseel_name' => 'VARCHAR(100)', 'block_name' => 'VARCHAR(100)', 'cs_name' => 'VARCHAR(255)',
        'capacity' => 'DECIMAL(12,2) DEFAULT 0', 'closure_reason' => 'VARCHAR(255)', 'closure_year' => 'VARCHAR(50)', 'road_access' => 'VARCHAR(255)',
        'land_area' => 'VARCHAR(100)', 'land_value' => 'DECIMAL(15,2) DEFAULT 0', 'other_assets' => 'TEXT', 'rack_distance' => 'DECIMAL(10,2) DEFAULT 0',
        'approach_road_type' => 'VARCHAR(100)', 'elec_bill' => 'DECIMAL(12,2) DEFAULT 0', 'ncdc_loan' => 'DECIMAL(15,2) DEFAULT 0',
        'bank_loan' => 'DECIMAL(15,2) DEFAULT 0', 'upcb_loan' => 'DECIMAL(15,2) DEFAULT 0', 'dcb_loan' => 'DECIMAL(15,2) DEFAULT 0',
        'court_case' => 'TEXT', 'building_cond' => 'VARCHAR(100)', 'employees' => 'TEXT', 'action_plan' => 'TEXT',
        'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP', 'society_name' => 'VARCHAR(255)',
        'is_active' => "VARCHAR(20) DEFAULT 'active'", 'inactivity_date' => 'DATE DEFAULT NULL',
        'inactivity_reason' => 'VARCHAR(100)', 'inactivity_description' => 'TEXT', 'land_area_total' => 'DECIMAL(12,4) DEFAULT 0',
        'vacant_land_area' => 'DECIMAL(12,4) DEFAULT 0', 'vacant_land_status' => 'VARCHAR(100)', 'vacant_land_location' => 'VARCHAR(100)',
        'building_repair_floor' => 'TINYINT(1) DEFAULT 0', 'building_repair_wall' => 'TINYINT(1) DEFAULT 0', 'building_repair_paint' => 'TINYINT(1) DEFAULT 0',
        'building_repair_ceiling' => 'TINYINT(1) DEFAULT 0', 'building_repair_plaster' => 'TINYINT(1) DEFAULT 0', 'building_repair_other' => 'TINYINT(1) DEFAULT 0',
        'building_photo' => 'TEXT', 'building_desc' => 'TEXT', 'ownership_type' => 'VARCHAR(50)', 'ownership_photo' => 'TEXT',
        'monthly_rent' => 'DECIMAL(12,2) DEFAULT 0', 'building_area_sqft' => 'DECIMAL(12,2) DEFAULT 0', 'ownership_desc' => 'TEXT',
        'has_basic_facilities' => "VARCHAR(10) DEFAULT 'no'", 'elec_conn' => "VARCHAR(10) DEFAULT 'no'",
        'elec_working' => "VARCHAR(10) DEFAULT 'no'", 'elec_bill_regular' => "VARCHAR(10) DEFAULT 'no'",
        'elec_not_working_reason' => 'TEXT', 'elec_proposal' => 'TEXT', 'elec_months_due' => 'INT DEFAULT 0', 'elec_outstanding' => 'DECIMAL(12,2) DEFAULT 0',
        'solar_conn' => "VARCHAR(10) DEFAULT 'no'", 'solar_working' => "VARCHAR(10) DEFAULT 'no'", 'solar_battery_status' => "VARCHAR(20) DEFAULT 'good'",
        'internet_conn' => "VARCHAR(10) DEFAULT 'no'", 'internet_provider' => 'VARCHAR(100)', 'internet_bill_paid' => "VARCHAR(10) DEFAULT 'no'",
        'internet_active' => "VARCHAR(10) DEFAULT 'no'", 'water_availability' => "VARCHAR(10) DEFAULT 'no'",
        'water_status' => "VARCHAR(20) DEFAULT 'non_operational'", 'has_loan' => "VARCHAR(10) DEFAULT 'no'",
        'other_loan_desc' => 'TEXT', 'other_loan_amount' => 'DECIMAL(15,2) DEFAULT 0', 'employees_data' => 'TEXT', 'building_status' => 'VARCHAR(50)',
        'is_approved' => 'TINYINT(1) DEFAULT 0', 'is_litigation' => "VARCHAR(10) DEFAULT 'no'", 'litigation_desc' => 'TEXT',
        'litigation_start_year' => 'VARCHAR(20)', 'litigation_current_status' => 'TEXT', 'approach_road_desc' => 'TEXT',
        'road_width' => 'DECIMAL(10,2) DEFAULT 0', 'assets_loan_data' => 'TEXT', 'other_dues_tax' => 'DECIMAL(15,2) DEFAULT 0',
        'other_dues_salary' => 'DECIMAL(15,2) DEFAULT 0', 'other_dues_other' => 'DECIMAL(15,2) DEFAULT 0', 'other_dues_other_status' => "VARCHAR(10) DEFAULT 'no'",
        'elec_bill_status' => "VARCHAR(10) DEFAULT 'no'", 'other_dues_tax_status' => "VARCHAR(10) DEFAULT 'no'", 'other_dues_salary_status' => "VARCHAR(10) DEFAULT 'no'",
        // New Sections II -> VII fields mapping
        'sec_new_plot_area' => 'VARCHAR(50)', 'sec_new_plot_value' => 'VARCHAR(50)',
        'sec_new_plot_revenue_status' => 'VARCHAR(20)', 'sec_new_plot_reason_for_not_record' => 'TEXT', 'sec_new_plot_practices_if_not' => 'TEXT',
        'sec_new_plot_gata_no' => 'VARCHAR(255)', 'sec_new_remarks' => 'TEXT', 'sec_6_access_road' => 'VARCHAR(50)', 'sec_6_paved_road' => 'VARCHAR(50)',
        'sec_8_plot_frontage' => 'VARCHAR(50)', 'sec_3_approach_image' => 'TEXT', 'sec_6_illegal_possession' => 'VARCHAR(20)', 'sec_6_if_yes_6' => 'TEXT',
        'storage_capacity_data' => 'LONGTEXT', 'vacant_land_data' => 'LONGTEXT', 'sec_3_d_boundry' => 'VARCHAR(20)',
        'sec_3_d_boundry_height' => 'VARCHAR(50)', 'sec_3_d_main_gate' => 'VARCHAR(50)'
    ];
    foreach ($cols as $col => $type) {
        $check_col = execute_query("SHOW COLUMNS FROM cold_storage_entries LIKE '$col'");
        if ($check_col && mysqli_num_rows($check_col) == 0) {
            @execute_query("ALTER TABLE cold_storage_entries ADD COLUMN $col $type");
        } else {
            // Modify ENUM to VARCHAR if it already exists to avoid truncation
            $row = mysqli_fetch_assoc($check_col);
            if (strpos($row['Type'], 'enum') !== false) {
                @execute_query("ALTER TABLE cold_storage_entries MODIFY COLUMN $col $type");
            }
        }
    }

    // Process Dropdowns - Improved to handle both IDs and names, and prevent accidental clearing
    $district_name = $_POST['old_district_name'] ?? ''; 
    if (!empty($_POST['district_name'])) {
        $dist_val = mysqli_real_escape_string($db, $_POST['district_name']);
        $row = mysqli_fetch_assoc(execute_query("SELECT district_name FROM master_district WHERE sno = '$dist_val' OR district_name = '$dist_val'"));
        if ($row) $district_name = $row['district_name'];
    }

    $tehseel_name = $_POST['old_tehseel_name'] ?? '';
    if (!empty($_POST['tehseel_name'])) {
        $teh_val = mysqli_real_escape_string($db, $_POST['tehseel_name']);
        $row = mysqli_fetch_assoc(execute_query("SELECT tehseel_name FROM master_tehseel WHERE sno = '$teh_val' OR tehseel_name = '$teh_val'"));
        if ($row) $tehseel_name = $row['tehseel_name'];
    }

    $block_name = $_POST['old_block_name'] ?? '';
    if (!empty($_POST['block_name'])) {
        $blk_val = mysqli_real_escape_string($db, $_POST['block_name']);
        $row = mysqli_fetch_assoc(execute_query("SELECT block_name FROM master_block WHERE sno = '$blk_val' OR block_name = '$blk_val'"));
        if ($row) $block_name = $row['block_name'];
    }

    $society_name = $_POST['old_society_name'] ?? '';
    if (!empty($_POST['society_name'])) {
        $soc_val = mysqli_real_escape_string($db, $_POST['society_name']);
        $row = mysqli_fetch_assoc(execute_query("SELECT col4 FROM test2 WHERE sno = '$soc_val' OR col4 = '$soc_val'"));
        if ($row) $society_name = $row['col4'];
    }

    // Normalized Data Fields
    $is_active = $_POST['is_active'] ?? 'active';
    $inactivity_date = ($is_active == 'inactive' && !empty($_POST['inactivity_date'])) ? $_POST['inactivity_date'] : null;

    // File Uploads
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) @mkdir($target_dir, 0777, true);

    $building_photo = $_POST['old_building_photo'] ?? '';
    if (!empty($_FILES['building_photo']['name']) && !empty($_FILES['building_photo']['name'][0])) {
        $uploaded_photos = [];
        $files = $_FILES['building_photo'];
        for ($i = 0; $i < min(count($files['name']), 4); $i++) {
            if ($files['error'][$i] === 0) {
                $path = $target_dir . time() . "_bld_" . $i . ".jpg";
                if (compressImage($files['tmp_name'][$i], $path, 60)) $uploaded_photos[] = $path;
            }
        }
        if (!empty($uploaded_photos)) $building_photo = json_encode($uploaded_photos);
    }

    $ownership_photo = $_POST['old_ownership_photo'] ?? '';
    if (!empty($_FILES['ownership_photo']['name'])) {
        $path = $target_dir . time() . "_own_.jpg";
        if (compressImage($_FILES['ownership_photo']['tmp_name'], $path, 60)) $ownership_photo = $path;
    }

    $sec_3_approach_image = $_POST['old_sec_3_approach_image'] ?? '';
    if (!empty($_FILES['sec_3_approach_image']['name'])) {
        $path = $target_dir . time() . "_appr_.jpg";
        if (compressImage($_FILES['sec_3_approach_image']['tmp_name'], $path, 60)) $sec_3_approach_image = $path;
    }

    // JSON Data
    $normalized_emps = [];
    if (!empty($_POST['emp']['name'])) {
        foreach ($_POST['emp']['name'] as $idx => $name) {
            if (empty($name) && empty($_POST['emp']['designation'][$idx])) continue;
            $normalized_emps[] = array_map(function($v) use ($db) { return $v ?? ''; }, [
                'name' => $_POST['emp']['name'][$idx], 'designation' => $_POST['emp']['designation'][$idx],
                'appt_date' => $_POST['emp']['appt_date'][$idx], 'working_status' => $_POST['emp']['working_status'][$idx],
                'mobile' => $_POST['emp']['mobile'][$idx], 'email' => $_POST['emp']['email'][$idx],
                'appt_source' => $_POST['emp']['appt_source'][$idx], 'appt_source_other' => $_POST['emp']['appt_source_other'][$idx],
                'approval_level' => $_POST['emp']['approval_level'][$idx], 'appt_type' => $_POST['emp']['appt_type'][$idx]
            ]);
        }
    }
    $employees_data = json_encode($normalized_emps);

    $normalized_assets = [];
    if (!empty($_POST['assets_loan']['loan_date'])) {
        foreach ($_POST['assets_loan']['loan_date'] as $idx => $ldate) {
            if (empty($ldate) && empty($_POST['assets_loan']['principal_amt'][$idx])) continue;
            $normalized_assets[] = [
                'loan_date' => $_POST['assets_loan']['loan_date'][$idx],
                'principal_amt' => $_POST['assets_loan']['principal_amt'][$idx] ?? 0,
                'principal_pending' => $_POST['assets_loan']['principal_pending'][$idx] ?? 0,
                'interest_amt' => $_POST['assets_loan']['interest_amt'][$idx] ?? 0,
                'interest_pending' => $_POST['assets_loan']['interest_pending'][$idx] ?? 0
            ];
        }
    }
    $assets_loan_data = json_encode($normalized_assets);

    $storage_capacity_data = [];
    $count_capacity = intval($_POST['sec_2_nirmit_godown_id'] ?? 1);
    for ($i = 1; $i <= $count_capacity; $i++) {
        if (!empty($_POST["sec_3_b_storage_capacity_$i"]) || !empty($_POST["sec_3_b_godown_year_$i"])) {
            $storage_capacity_data["sec_3_b_storage_capacity_$i"] = $_POST["sec_3_b_storage_capacity_$i"] ?? '';
            $storage_capacity_data["sec_3_b_godown_year_$i"] = $_POST["sec_3_b_godown_year_$i"] ?? '';
            $storage_capacity_data["sec_3_b_wdra_certified_$i"] = $_POST["sec_3_b_wdra_certified_$i"] ?? '';
            $storage_capacity_data["sec_3_b_godown_type_of_fund_$i"] = $_POST["sec_3_b_godown_type_of_fund_$i"] ?? '';
            $storage_capacity_data["sec_3_b_godown_status_$i"] = $_POST["sec_3_b_godown_status_$i"] ?? '';
            $storage_capacity_data["sec_3_b_godown_comment_$i"] = $_POST["sec_3_b_godown_comment_$i"] ?? '';
        }
    }
    if (!empty($storage_capacity_data)) {
        $storage_capacity_data['count'] = $count_capacity;
    }
    $storage_cap_json = json_encode($storage_capacity_data);

    $vacant_land_data = [];
    $count_vacant = intval($_POST['sec_3_c_id'] ?? 1);
    for ($i = 1; $i <= $count_vacant; $i++) {
        if (!empty($_POST["sec_3_c_length_$i"]) || !empty($_POST["sec_3_c_rak_distance_$i"])) {
            $vacant_land_data["sec_3_c_length_$i"] = $_POST["sec_3_c_length_$i"] ?? '';
            $vacant_land_data["sec_3_c_rak_distance_$i"] = $_POST["sec_3_c_rak_distance_$i"] ?? '';
            $vacant_land_data["sec_3_c_paved_road_$i"] = $_POST["sec_3_c_paved_road_$i"] ?? '';
            
            // Handle image uploads for vacant land if any
            if (!empty($_FILES["sec_3_c_food_scheme_image_$i"]['name'])) {
                $path = $target_dir . time() . "_vacant_$i.jpg";
                if (compressImage($_FILES["sec_3_c_food_scheme_image_$i"]['tmp_name'], $path, 60)) {
                    $vacant_land_data["food_scheme$i"] = $path;
                }
            } else {
                $vacant_land_data["food_scheme$i"] = $_POST["old_food_scheme$i"] ?? ''; // We didn't setup hidden for this but harmless
            }
        }
    }
    if (!empty($vacant_land_data)) {
        $vacant_land_data['sec_3_c_id'] = $count_vacant;
    }
    $vacant_land_json = json_encode($vacant_land_data);

    // Build Data Array for Query
    $data = [
        'latitude' => $_POST['latitude'] ?? '', 'longitude' => $_POST['longitude'] ?? '',
        'district_name' => $district_name, 'tehseel_name' => $tehseel_name, 'block_name' => $block_name,
        'society_name' => $society_name, 'cs_name' => $society_name, 'capacity' => (float) ($_POST['capacity'] ?? 0),
        'is_active' => $is_active, 'inactivity_date' => $inactivity_date, 'closure_year' => $inactivity_date,
        'inactivity_reason' => $_POST['inactivity_reason'] ?? '', 'closure_reason' => $_POST['inactivity_reason'] ?? '',
        'inactivity_description' => $_POST['inactivity_description'] ?? '', 'land_area_total' => (float) ($_POST['land_area_total'] ?? 0),
        'land_area' => (float) ($_POST['land_area_total'] ?? 0), 'vacant_land_area' => (float) ($_POST['vacant_land_area'] ?? 0),
        'vacant_land_status' => $_POST['vacant_land_status'] ?? '', 'vacant_land_location' => $_POST['vacant_land_location'] ?? '',
        'rack_distance' => (float) ($_POST['rack_distance'] ?? 0), 'road_width' => (float) ($_POST['road_width'] ?? 0),
        'is_litigation' => $_POST['is_litigation'] ?? 'no', 'litigation_desc' => $_POST['litigation_desc'] ?? '',
        'court_case' => $_POST['court_case'] ?? '', 'litigation_start_year' => $_POST['litigation_start_year'] ?? '',
        'litigation_current_status' => $_POST['litigation_current_status'] ?? '', 'building_status' => $_POST['building_status'] ?? '',
        'building_cond' => $_POST['building_status'] ?? '', 'building_repair_floor' => isset($_POST['repair_floor']) ? 1 : 0,
        'building_repair_wall' => isset($_POST['repair_wall']) ? 1 : 0, 'building_repair_paint' => isset($_POST['repair_paint']) ? 1 : 0,
        'building_repair_ceiling' => isset($_POST['repair_ceiling']) ? 1 : 0, 'building_repair_plaster' => isset($_POST['repair_plaster']) ? 1 : 0,
        'building_repair_other' => isset($_POST['repair_other']) ? 1 : 0, 'building_photo' => $building_photo,
        'building_desc' => $_POST['building_desc'] ?? '', 'ownership_type' => $_POST['ownership_type'] ?? '',
        'ownership_photo' => $ownership_photo, 'monthly_rent' => (float) ($_POST['monthly_rent'] ?? 0),
        'building_area_sqft' => (float) ($_POST['building_area_sqft'] ?? 0), 'ownership_desc' => $_POST['ownership_desc'] ?? '',
        'approach_road_type' => $_POST['approach_road_type'] ?? '', 'road_access' => $_POST['approach_road_type'] ?? '',
        'approach_road_desc' => $_POST['approach_road_desc'] ?? '', 'has_basic_facilities' => $_POST['has_basic_facilities'] ?? 'no',
        'elec_conn' => $_POST['elec_conn'] ?? 'no', 'elec_bill' => (float) ($_POST['elec_bill'] ?? 0),
        'elec_working' => $_POST['elec_working'] ?? 'no', 'elec_bill_regular' => $_POST['elec_bill_regular'] ?? 'no',
        'elec_not_working_reason' => $_POST['elec_not_working_reason'] ?? '', 'elec_proposal' => $_POST['elec_proposal'] ?? '',
        'elec_months_due' => (int) ($_POST['elec_months_due'] ?? 0), 'elec_outstanding' => (float) ($_POST['elec_outstanding'] ?? 0),
        'solar_conn' => $_POST['solar_conn'] ?? 'no', 'solar_working' => $_POST['solar_working'] ?? 'no',
        'solar_battery_status' => $_POST['solar_battery_status'] ?? 'good', 'internet_conn' => $_POST['internet_conn'] ?? 'no',
        'internet_provider' => $_POST['internet_provider'] ?? '', 'internet_bill_paid' => $_POST['internet_bill_paid'] ?? 'no',
        'internet_active' => $_POST['internet_active'] ?? 'no', 'water_availability' => $_POST['water_availability'] ?? 'no',
        'water_status' => $_POST['water_status'] ?? 'non_operational', 'has_loan' => $_POST['has_loan'] ?? 'no',
        'ncdc_loan' => (float) ($_POST['ncdc_loan'] ?? 0), 'bank_loan' => (float) ($_POST['bank_loan'] ?? 0), 'upcb_loan' => (float) ($_POST['upcb_loan'] ?? 0),
        'dcb_loan' => (float) ($_POST['dcb_loan'] ?? 0), 'other_loan_desc' => $_POST['other_loan_desc'] ?? '',
        'other_loan_amount' => (float) ($_POST['other_loan_amount'] ?? 0), 'other_dues_tax' => (float) ($_POST['other_dues_tax'] ?? 0),
        'other_dues_salary' => (float) ($_POST['other_dues_salary'] ?? 0), 'other_dues_other' => (float) ($_POST['other_dues_other'] ?? 0),
        'other_dues_other_status' => $_POST['other_dues_other_status'] ?? 'no', 'elec_bill_status' => $_POST['elec_bill_status'] ?? 'no',
        'other_dues_tax_status' => $_POST['other_dues_tax_status'] ?? 'no', 'other_dues_salary_status' => $_POST['other_dues_salary_status'] ?? 'no',
        'employees_data' => $employees_data, 'employees' => $employees_data, 'assets_loan_data' => $assets_loan_data,
        'action_plan' => $_POST['action_plan'] ?? '', 'is_approved' => (int) ($_POST['is_approved'] ?? 0),
        'sec_new_plot_area' => $_POST['sec_new_plot_area'] ?? '', 'sec_new_plot_value' => $_POST['sec_new_plot_value'] ?? '',
        'sec_new_plot_revenue_status' => $_POST['sec_new_plot_revenue_status'] ?? '', 'sec_new_plot_reason_for_not_record' => $_POST['sec_new_plot_reason_for_not_record'] ?? '',
        'sec_new_plot_practices_if_not' => $_POST['sec_new_plot_practices_if_not'] ?? '', 'sec_new_plot_gata_no' => $_POST['sec_new_plot_gata_no'] ?? '',
        'sec_new_remarks' => $_POST['sec_new_remarks'] ?? '', 'sec_6_access_road' => $_POST['sec_6_access_road'] ?? '',
        'sec_6_paved_road' => $_POST['sec_6_paved_road'] ?? '', 'sec_8_plot_frontage' => $_POST['sec_8_plot_frontage'] ?? '',
        'sec_3_approach_image' => $sec_3_approach_image, 'sec_6_illegal_possession' => $_POST['sec_6_illegal_possession'] ?? '',
        'sec_6_if_yes_6' => $_POST['sec_6_if_yes_6'] ?? '', 'storage_capacity_data' => $storage_cap_json,
        'vacant_land_data' => $vacant_land_json, 'sec_3_d_boundry' => $_POST['sec_3_d_boundry'] ?? '',
        'sec_3_d_boundry_height' => $_POST['sec_3_d_boundry_height'] ?? '', 'sec_3_d_main_gate' => $_POST['sec_3_d_main_gate'] ?? ''
    ];

    $edit_id = $_POST['edit_id'] ?? '';
    if (!empty($edit_id)) {
        $sets = [];
        foreach ($data as $col => $val) {
            $safe_val = ($val === null) ? "NULL" : "'" . mysqli_real_escape_string($db, $val) . "'";
            $sets[] = "$col = $safe_val";
        }
        $sql = "UPDATE cold_storage_entries SET " . implode(", ", $sets) . " WHERE id = '" . mysqli_real_escape_string($db, $edit_id) . "'";
    } else {
        $cols_str = implode(", ", array_keys($data));
        $vals_str = implode(", ", array_map(function($v) use ($db) {
            return ($v === null) ? "NULL" : "'" . mysqli_real_escape_string($db, $v) . "'";
        }, array_values($data)));
        $sql = "INSERT INTO cold_storage_entries ($cols_str) VALUES ($vals_str)";
    }

    $res = execute_query($sql);
    if ($res) {
        $record_id = !empty($edit_id) ? $edit_id : mysqli_insert_id($db);
        $response = array("status" => "success", "msg" => !empty($edit_id) ? 'Updated successfully!' : 'Saved successfully!', "record_id" => $record_id);
    } else {
        // Capture error directly in case execute_query clears it
        $err = mysqli_error($db);
        if (empty($err)) {
            // Check mysql_dump table for the error logged by execute_query
            $dump_res = mysqli_query($db, "SELECT mysql_error FROM mysql_dump ORDER BY sno DESC LIMIT 1");
            $dump_row = mysqli_fetch_assoc($dump_res);
            $err = $dump_row['mysql_error'] ?? 'Unknown Error';
        }
        $response = array("status" => "error", "msg" => "Database Error: $err");
    }
} else {
    $response = array("status" => "error", "msg" => "Invalid Request Method.");
}

echo json_encode($response);
?>
