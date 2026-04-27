<?php
include("scripts/settings.php");
// error_reporting(E_ALL);

/**
 * Compresses and resizes an image to keep it under KB size.
 */
function compressImage($source, $destination, $quality = 60) {
    if (!extension_loaded('gd')) {
        return move_uploaded_file($source, $destination);
    }
    
    $info = getimagesize($source);
    if (!$info) return move_uploaded_file($source, $destination);
    
    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg': $image = @imagecreatefromjpeg($source); break;
        case 'image/gif':  $image = @imagecreatefromgif($source);  break;
        case 'image/png':  $image = @imagecreatefrompng($source);  break;
        default: return move_uploaded_file($source, $destination);
    }

    if (!$image) return move_uploaded_file($source, $destination);

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

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Debug: Log POST data
    error_log("POST data received: " . print_r($_POST, true));
    
    // Security: Only AR or Sadmin can submit
    if(!isset($_SESSION['username']) || !($_SESSION['user_type'] == 'ar' || $_SESSION['usertype'] == '3' || $_SESSION['usertype'] == 'sadmin')) {
        echo "<script>alert('Access Denied.'); window.location.href='index_1.php';</script>";
        exit;
    }

    // 1. Create table if not exists with all fields
    $create_sql = "CREATE TABLE IF NOT EXISTS cold_storage_entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        district_name VARCHAR(100),
        tehseel_name VARCHAR(100),
        block_name VARCHAR(100),
        cs_name VARCHAR(255),
        capacity DECIMAL(12,2) DEFAULT 0,
        closure_reason VARCHAR(255),
        closure_year VARCHAR(50),
        road_access VARCHAR(255),
        land_area VARCHAR(100),
        land_value DECIMAL(15,2) DEFAULT 0,
        other_assets TEXT,
        rack_distance DECIMAL(10,2) DEFAULT 0,
        approach_road_type VARCHAR(100),
        elec_bill DECIMAL(12,2) DEFAULT 0,
        ncdc_loan DECIMAL(15,2) DEFAULT 0,
        bank_loan DECIMAL(15,2) DEFAULT 0,
        upcb_loan DECIMAL(15,2) DEFAULT 0,
        dcb_loan DECIMAL(15,2) DEFAULT 0,
        court_case TEXT,
        building_cond VARCHAR(100),
        employees TEXT,
        action_plan TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        society_name VARCHAR(255),
        is_active ENUM('active', 'inactive', 'under_repair') DEFAULT 'active',
        inactivity_date DATE DEFAULT NULL,
        inactivity_reason VARCHAR(100),
        inactivity_description TEXT,
        land_area_total DECIMAL(12,4) DEFAULT 0,
        vacant_land_area DECIMAL(12,4) DEFAULT 0,
        vacant_land_status VARCHAR(100),
        vacant_land_location VARCHAR(100),
        building_repair_floor TINYINT(1) DEFAULT 0,
        building_repair_wall TINYINT(1) DEFAULT 0,
        building_repair_paint TINYINT(1) DEFAULT 0,
        building_repair_ceiling TINYINT(1) DEFAULT 0,
        building_repair_plaster TINYINT(1) DEFAULT 0,
        building_repair_other TINYINT(1) DEFAULT 0,
        building_photo TEXT,
        building_desc TEXT,
        ownership_type VARCHAR(50),
        ownership_photo TEXT,
        monthly_rent DECIMAL(12,2) DEFAULT 0,
        building_area_sqft DECIMAL(12,2) DEFAULT 0,
        ownership_desc TEXT,
        has_basic_facilities ENUM('yes', 'no') DEFAULT 'no',
        elec_conn ENUM('yes', 'no') DEFAULT 'no',
        elec_working ENUM('yes', 'no') DEFAULT 'no',
        elec_bill_regular ENUM('yes', 'no') DEFAULT 'no',
        elec_not_working_reason TEXT,
        elec_proposal TEXT,
        elec_months_due INT DEFAULT 0,
        elec_outstanding DECIMAL(12,2) DEFAULT 0,
        solar_conn ENUM('yes', 'no') DEFAULT 'no',
        solar_working ENUM('yes', 'no') DEFAULT 'no',
        solar_battery_status ENUM('good', 'poor') DEFAULT 'good',
        internet_conn ENUM('yes', 'no') DEFAULT 'no',
        internet_provider VARCHAR(100),
        internet_bill_paid ENUM('yes', 'no') DEFAULT 'no',
        internet_active ENUM('yes', 'no') DEFAULT 'no',
        water_availability ENUM('yes', 'no') DEFAULT 'no',
        water_status ENUM('operational', 'non_operational') DEFAULT 'non_operational',
        has_loan ENUM('yes', 'no') DEFAULT 'no',
        other_loan_desc TEXT,
        other_loan_amount DECIMAL(15,2) DEFAULT 0,
        employees_data TEXT,
        building_status VARCHAR(50),
        is_approved TINYINT(1) DEFAULT 0,
        is_litigation ENUM('yes', 'no') DEFAULT 'no',
        litigation_desc TEXT,
        approach_road_desc TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    execute_query($create_sql);

    // Schema updates to ensure compatibility
    $cols = [
        'district_name' => 'VARCHAR(100)',
        'tehseel_name' => 'VARCHAR(100)',
        'block_name' => 'VARCHAR(100)',
        'cs_name' => 'VARCHAR(255)',
        'capacity' => 'DECIMAL(12,2) DEFAULT 0',
        'closure_reason' => 'VARCHAR(255)',
        'closure_year' => 'VARCHAR(50)',
        'road_access' => 'VARCHAR(255)',
        'land_area' => 'VARCHAR(100)',
        'land_value' => 'DECIMAL(15,2) DEFAULT 0',
        'other_assets' => 'TEXT',
        'rack_distance' => 'DECIMAL(10,2) DEFAULT 0',
        'approach_road_type' => 'VARCHAR(100)',
        'elec_bill' => 'DECIMAL(12,2) DEFAULT 0',
        'ncdc_loan' => 'DECIMAL(15,2) DEFAULT 0',
        'bank_loan' => 'DECIMAL(15,2) DEFAULT 0',
        'upcb_loan' => 'DECIMAL(15,2) DEFAULT 0',
        'dcb_loan' => 'DECIMAL(15,2) DEFAULT 0',
        'court_case' => 'TEXT',
        'building_cond' => 'VARCHAR(100)',
        'employees' => 'TEXT',
        'action_plan' => 'TEXT',
        'society_name' => 'VARCHAR(255)',
        'is_active' => "ENUM('active', 'inactive', 'under_repair') DEFAULT 'active'",
        'inactivity_date' => 'DATE DEFAULT NULL',
        'inactivity_reason' => 'VARCHAR(100)',
        'inactivity_description' => 'TEXT',
        'land_area_total' => 'DECIMAL(12,4) DEFAULT 0',
        'vacant_land_area' => 'DECIMAL(12,4) DEFAULT 0',
        'vacant_land_status' => 'VARCHAR(100)',
        'vacant_land_location' => 'VARCHAR(100)',
        'building_repair_floor' => 'TINYINT(1) DEFAULT 0',
        'building_repair_wall' => 'TINYINT(1) DEFAULT 0',
        'building_repair_paint' => 'TINYINT(1) DEFAULT 0',
        'building_repair_ceiling' => 'TINYINT(1) DEFAULT 0',
        'building_repair_plaster' => 'TINYINT(1) DEFAULT 0',
        'building_repair_other' => 'TINYINT(1) DEFAULT 0',
        'building_photo' => 'TEXT',
        'building_desc' => 'TEXT',
        'ownership_type' => 'VARCHAR(50)',
        'ownership_photo' => 'TEXT',
        'monthly_rent' => 'DECIMAL(12,2) DEFAULT 0',
        'building_area_sqft' => 'DECIMAL(12,2) DEFAULT 0',
        'ownership_desc' => 'TEXT',
        'has_basic_facilities' => "ENUM('yes', 'no') DEFAULT 'no'",
        'elec_conn' => "ENUM('yes', 'no') DEFAULT 'no'",
        'elec_working' => "ENUM('yes', 'no') DEFAULT 'no'",
        'elec_bill_regular' => "ENUM('yes', 'no') DEFAULT 'no'",
        'elec_not_working_reason' => 'TEXT',
        'elec_proposal' => 'TEXT',
        'elec_months_due' => 'INT DEFAULT 0',
        'elec_outstanding' => 'DECIMAL(12,2) DEFAULT 0',
        'solar_conn' => "ENUM('yes', 'no') DEFAULT 'no'",
        'solar_working' => "ENUM('yes', 'no') DEFAULT 'no'",
        'solar_battery_status' => "ENUM('good', 'poor') DEFAULT 'good'",
        'internet_conn' => "ENUM('yes', 'no') DEFAULT 'no'",
        'internet_provider' => 'VARCHAR(100)',
        'internet_bill_paid' => "ENUM('yes', 'no') DEFAULT 'no'",
        'internet_active' => "ENUM('yes', 'no') DEFAULT 'no'",
        'water_availability' => "ENUM('yes', 'no') DEFAULT 'no'",
        'water_status' => "ENUM('operational', 'non_operational') DEFAULT 'non_operational'",
        'has_loan' => "ENUM('yes', 'no') DEFAULT 'no'",
        'other_loan_desc' => 'TEXT',
        'other_loan_amount' => 'DECIMAL(15,2) DEFAULT 0',
        'employees_data' => 'TEXT',
        'building_status' => 'VARCHAR(50)',
        'is_approved' => 'TINYINT(1) DEFAULT 0',
        'is_litigation' => "ENUM('yes', 'no') DEFAULT 'no'",
        'litigation_desc' => 'TEXT',
        'approach_road_desc' => 'TEXT'
    ];
    foreach($cols as $col => $type) {
        $check_col = execute_query("SHOW COLUMNS FROM cold_storage_entries LIKE '$col'");
        if($check_col && mysqli_num_rows($check_col) == 0) {
            @execute_query("ALTER TABLE cold_storage_entries ADD COLUMN $col $type");
        } else {
            // Ensure photo columns are TEXT for large data
            if($col == 'building_photo' || $col == 'ownership_photo' || $col == 'employees_data' || $col == 'employees') {
                @execute_query("ALTER TABLE cold_storage_entries MODIFY COLUMN $col TEXT");
            }
        }
    }

    // Prepare Data
    $district_id = isset($_POST['district_name']) ? $_POST['district_name'] : '';
    $district_name = '';
    if($district_id) {
        $dist_res = execute_query("SELECT district_name FROM master_district WHERE sno = '$district_id'");
        if($dist_res && $dist_row = mysqli_fetch_assoc($dist_res)) $district_name = $dist_row['district_name'];
    }

    $tehseel_id = isset($_POST['tehseel_name']) ? $_POST['tehseel_name'] : '';
    $tehseel_name = '';
    if($tehseel_id) {
        $teh_res = execute_query("SELECT tehseel_name FROM master_tehseel WHERE sno = '$tehseel_id'");
        if($teh_res && $teh_row = mysqli_fetch_assoc($teh_res)) $tehseel_name = $teh_row['tehseel_name'];
    }

    $block_id = isset($_POST['block_name']) ? $_POST['block_name'] : '';
    $block_name = '';
    if($block_id) {
        $blk_res = execute_query("SELECT block_name FROM master_block WHERE sno = '$block_id'");
        if($blk_res && $blk_row = mysqli_fetch_assoc($blk_res)) $block_name = $blk_row['block_name'];
    }

    $society_id = isset($_POST['society_name']) ? $_POST['society_name'] : '';
    $society_name = '';
    if($society_id) {
        $soc_res = execute_query("SELECT col4 FROM test2 WHERE sno = '$society_id'");
        if($soc_res && $soc_row = mysqli_fetch_assoc($soc_res)) $society_name = $soc_row['col4'];
    }

    $capacity = (float)(isset($_POST['capacity']) ? $_POST['capacity'] : 0);
    $is_active = isset($_POST['is_active']) ? $_POST['is_active'] : 'active';
    $inactivity_date = ($is_active == 'inactive' && !empty($_POST['inactivity_date'])) ? $_POST['inactivity_date'] : 'NULL';
    $inactivity_reason = isset($_POST['inactivity_reason']) ? $_POST['inactivity_reason'] : '';
    $inactivity_description = isset($_POST['inactivity_description']) ? $_POST['inactivity_description'] : '';
    
    $land_area_total = (float)(isset($_POST['land_area_total']) ? $_POST['land_area_total'] : 0);
    $vacant_land_area = (float)(isset($_POST['vacant_land_area']) ? $_POST['vacant_land_area'] : 0);
    $vacant_land_status = isset($_POST['vacant_land_status']) ? $_POST['vacant_land_status'] : '';
    $vacant_land_location = isset($_POST['vacant_land_location']) ? $_POST['vacant_land_location'] : '';
    $rack_distance = (float)(isset($_POST['rack_distance']) ? $_POST['rack_distance'] : 0);
    $is_litigation = isset($_POST['is_litigation']) ? $_POST['is_litigation'] : 'no';
    $litigation_desc = mysqli_real_escape_string($db, $_POST['litigation_desc'] ?? '');
    
    $building_status = isset($_POST['building_status']) ? $_POST['building_status'] : '';
    $repair_floor = isset($_POST['repair_floor']) ? 1 : 0;
    $repair_wall = isset($_POST['repair_wall']) ? 1 : 0;
    $repair_paint = isset($_POST['repair_paint']) ? 1 : 0;
    $repair_ceiling = isset($_POST['repair_ceiling']) ? 1 : 0;
    $repair_plaster = isset($_POST['repair_plaster']) ? 1 : 0;
    $repair_other = isset($_POST['repair_other']) ? 1 : 0;
    $building_desc = isset($_POST['building_desc']) ? $_POST['building_desc'] : '';

    $ownership_type = isset($_POST['ownership_type']) ? $_POST['ownership_type'] : '';
    $monthly_rent = (float)(isset($_POST['monthly_rent']) ? $_POST['monthly_rent'] : 0);
    $building_area_sqft = (float)(isset($_POST['building_area_sqft']) ? $_POST['building_area_sqft'] : 0);
    $ownership_desc = mysqli_real_escape_string($db, $_POST['ownership_desc'] ?? '');

    $approach_road_type = mysqli_real_escape_string($db, $_POST['approach_road_type'] ?? '');
    $approach_road_desc = mysqli_real_escape_string($db, $_POST['approach_road_desc'] ?? '');
    $has_basic_facilities = isset($_POST['has_basic_facilities']) ? $_POST['has_basic_facilities'] : 'no';
    $elec_conn = isset($_POST['elec_conn']) ? $_POST['elec_conn'] : 'no';
    $elec_bill = (float)(isset($_POST['elec_bill']) ? $_POST['elec_bill'] : 0);
    $solar_conn = isset($_POST['solar_conn']) ? $_POST['solar_conn'] : 'no';
    $solar_status = isset($_POST['solar_status']) ? $_POST['solar_status'] : 'non_operational';
    $internet_conn = isset($_POST['internet_conn']) ? $_POST['internet_conn'] : 'no';
    $internet_status = isset($_POST['internet_status']) ? $_POST['internet_status'] : 'non_operational';
    $water_availability = isset($_POST['water_availability']) ? $_POST['water_availability'] : 'no';
    $water_status = isset($_POST['water_status']) ? $_POST['water_status'] : 'non_operational';

    $has_loan = isset($_POST['has_loan']) ? $_POST['has_loan'] : 'no';
    $ncdc_loan = (float)(isset($_POST['ncdc_loan']) ? $_POST['ncdc_loan'] : 0);
    $bank_loan = (float)(isset($_POST['bank_loan']) ? $_POST['bank_loan'] : 0);
    $upcb_loan = (float)(isset($_POST['upcb_loan']) ? $_POST['upcb_loan'] : 0);
    $dcb_loan = (float)(isset($_POST['dcb_loan']) ? $_POST['dcb_loan'] : 0);
    $other_loan_desc = mysqli_real_escape_string($db, $_POST['other_loan_desc'] ?? '');
    $other_loan_amount = (float)(isset($_POST['other_loan_amount']) ? $_POST['other_loan_amount'] : 0);
    $employees_data = isset($_POST['emp']) ? json_encode($_POST['emp']) : '[]';
    if ($employees_data === false) {
        $employees_data = '[]'; // Fallback if JSON encoding fails
    }
    $action_plan = mysqli_real_escape_string($db, $_POST['action_plan']);
    $is_approved = isset($_POST['is_approved']) ? $_POST['is_approved'] : 'pending';

    // Handle File Uploads
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            // Directory creation failed, but continue without file uploads
            error_log("Failed to create upload directory: $target_dir");
        }
    }

    $building_photo = isset($_POST['old_building_photo']) ? $_POST['old_building_photo'] : '';
    if (!empty($_FILES['building_photo']['name']) && !empty($_FILES['building_photo']['name'][0])) {
        $uploaded_photos = [];
        $files = $_FILES['building_photo'];
        $file_count = count($files['name']);
        for ($i = 0; $i < min($file_count, 4); $i++) {
            if ($files['error'][$i] === 0) {
                $ext = "jpg"; // Forcing jpg after compression
                $new_filename = time() . "_bld_" . $i . "." . $ext;
                $path = $target_dir . $new_filename;
                if (compressImage($files['tmp_name'][$i], $path, 60)) {
                    $uploaded_photos[] = $path;
                }
            }
        }
        if (!empty($uploaded_photos)) {
            $building_photo = json_encode($uploaded_photos);
        }
    }

    $ownership_photo = isset($_POST['old_ownership_photo']) ? $_POST['old_ownership_photo'] : '';
    if (!empty($_FILES['ownership_photo']['name'])) {
        $path = $target_dir . time() . "_own_.jpg";
        if (compressImage($_FILES['ownership_photo']['tmp_name'], $path, 60)) $ownership_photo = $path;
    }

    global $db;
    $is_edit = !empty($_GET['edit_id']);
    $edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';

    if($is_edit) {
        $sql = "UPDATE cold_storage_entries SET 
            district_name='$district_name', tehseel_name='$tehseel_name', block_name='$block_name', 
            society_name='$society_name', cs_name='$society_name', capacity='$capacity', 
            is_active='$is_active', inactivity_date=" . ($inactivity_date=='NULL' ? "NULL" : "'$inactivity_date'") . ", 
            closure_year=" . ($inactivity_date=='NULL' ? "NULL" : "'$inactivity_date'") . ",
            inactivity_reason='$inactivity_reason', closure_reason='$inactivity_reason', inactivity_description='$inactivity_description',
            land_area_total='$land_area_total', land_area='$land_area_total', vacant_land_area='$vacant_land_area', 
            vacant_land_status='$vacant_land_status', vacant_land_location='$vacant_land_location', 
            rack_distance='$rack_distance', is_litigation='$is_litigation', litigation_desc='$litigation_desc',
            court_case='$litigation_desc',
            building_status='$building_status', building_cond='$building_status',
            building_repair_floor='$repair_floor', building_repair_wall='$repair_wall', 
            building_repair_paint='$repair_paint', building_repair_ceiling='$repair_ceiling', building_repair_plaster='$repair_plaster', 
            building_repair_other='$repair_other', 
            building_photo='".mysqli_real_escape_string($db, $building_photo)."', 
            building_desc='$building_desc', 
            ownership_type='$ownership_type', 
            ownership_photo='".mysqli_real_escape_string($db, $ownership_photo)."', 
            monthly_rent='$monthly_rent', 
            building_area_sqft='$building_area_sqft', ownership_desc='$ownership_desc', 
            approach_road_type='$approach_road_type', road_access='$approach_road_type', approach_road_desc='$approach_road_desc', 
            has_basic_facilities='$has_basic_facilities',
            elec_conn='$elec_conn', elec_working='$elec_working', elec_bill_regular='$elec_bill_regular',
            elec_not_working_reason='$elec_not_working_reason', elec_proposal='$elec_proposal',
            elec_months_due='$elec_months_due', elec_outstanding='$elec_outstanding', elec_bill='$elec_bill',
            solar_conn='$solar_conn', solar_working='$solar_working', solar_battery_status='$solar_battery_status',
            internet_conn='$internet_conn', internet_provider='$internet_provider', internet_bill_paid='$internet_bill_paid', 
            internet_active='$internet_active', water_availability='$water_availability', water_status='$water_status',
            has_loan='$has_loan', ncdc_loan='$ncdc_loan', bank_loan='$bank_loan', upcb_loan='$upcb_loan', dcb_loan='$dcb_loan',
            other_loan_desc='$other_loan_desc', other_loan_amount='$other_loan_amount',
            employees_data='".mysqli_real_escape_string($db, $employees_data)."', employees='".mysqli_real_escape_string($db, $employees_data)."', 
            action_plan='$action_plan', is_approved='$is_approved'
            WHERE id='$edit_id'";
    } else {
        $sql = "INSERT INTO cold_storage_entries (
            district_name, tehseel_name, block_name, society_name, cs_name, capacity, is_active, inactivity_date, closure_year,
            inactivity_reason, closure_reason, inactivity_description,
            land_area_total, land_area, vacant_land_area, vacant_land_status, vacant_land_location, rack_distance, is_litigation, litigation_desc,
            court_case, building_status, building_cond, building_repair_floor, building_repair_wall, building_repair_paint, building_repair_ceiling,
            building_repair_plaster, building_repair_other, building_photo, building_desc, ownership_type,
            ownership_photo, monthly_rent, building_area_sqft, ownership_desc, approach_road_type, road_access, approach_road_desc,
            has_basic_facilities, elec_conn, elec_working, elec_bill_regular, elec_not_working_reason, 
            elec_proposal, elec_months_due, elec_outstanding, elec_bill,
            solar_conn, solar_working, solar_battery_status,
            internet_conn, internet_provider, internet_bill_paid, internet_active,
            water_availability, water_status,
            has_loan, ncdc_loan, bank_loan, upcb_loan, dcb_loan, other_loan_desc, other_loan_amount, employees_data, employees, action_plan, is_approved
        ) VALUES (
            '$district_name', '$tehseel_name', '$block_name', '$society_name', '$society_name', '$capacity', '$is_active', " . ($inactivity_date=='NULL' ? "NULL" : "'$inactivity_date'") . ", " . ($inactivity_date=='NULL' ? "NULL" : "'$inactivity_date'") . ",
            '$inactivity_reason', '$inactivity_reason', '$inactivity_description',
            '$land_area_total', '$land_area_total', '$vacant_land_area', '$vacant_land_status', '$vacant_land_location', '$rack_distance', '$is_litigation', '$litigation_desc',
            '$litigation_desc', '$building_status', '$building_status', '$repair_floor', '$repair_wall', '$repair_paint', '$repair_ceiling',
            '$repair_plaster', '$repair_other', 
            '".mysqli_real_escape_string($db, $building_photo)."', 
            '$building_desc', '$ownership_type',
            '".mysqli_real_escape_string($db, $ownership_photo)."', 
            '$monthly_rent', '$building_area_sqft', '$ownership_desc', '$approach_road_type', '$approach_road_type', '$approach_road_desc',
            '$has_basic_facilities', '$elec_conn', '$elec_working', '$elec_bill_regular', '$elec_not_working_reason',
            '$elec_proposal', '$elec_months_due', '$elec_outstanding', '$elec_bill',
            '$solar_conn', '$solar_working', '$solar_battery_status',
            '$internet_conn', '$internet_provider', '$internet_bill_paid', '$internet_active',
            '$water_availability', '$water_status',
            '$has_loan', '$ncdc_loan', '$bank_loan', '$upcb_loan', '$dcb_loan', '$other_loan_desc', '$other_loan_amount', '".mysqli_real_escape_string($db, $employees_data)."', '".mysqli_real_escape_string($db, $employees_data)."', '$action_plan', '$is_approved'
        )";
    }

    $res = execute_query($sql);
    if($res) {
        $msg = $is_edit ? 'Updated successfully!' : 'Saved successfully!';
        // Show success page instead of redirect
        $show_success = true;
    } else {
        $err = mysqli_error($db);
        $sql_error = "SQL Error: $err\nQuery: $sql";
        echo "<script>alert('Database Error: $err');</script>";
        // Log the error for debugging
        error_log($sql_error);
        // Also log the POST data for debugging
        error_log("Failed POST data: " . print_r($_POST, true));
    }
}

// Check for Edit ID
$edit_data = [];
$edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';
$is_edit = !empty($edit_id);

if($is_edit) {
    $sql = "SELECT * FROM cold_storage_entries WHERE id = '$edit_id'";
    $res = execute_query($sql);
    if($row = mysqli_fetch_assoc($res)) {
        $edit_data = $row;
        // Extract reason for संचालित
        if(strpos($edit_data['closure_reason'], 'संचालित - ') === 0) {
            $edit_data['asanchalit_reason'] = substr($edit_data['closure_reason'], strlen('संचालित - '));
            $edit_data['closure_reason'] = 'संचालित';
        }
        // Extract for land_area विवादित or अन्य
        if(strpos($edit_data['land_area'], 'विवादित - ') === 0 || strpos($edit_data['land_area'], 'अन्य - ') === 0) {
            $prefix = strpos($edit_data['land_area'], 'विवादित - ') === 0 ? 'विवादित - ' : 'अन्य - ';
            $edit_data['land_ownership_desc'] = substr($edit_data['land_area'], strlen($prefix));
            $edit_data['land_area'] = str_replace(' - ' . $edit_data['land_ownership_desc'], '', $edit_data['land_area']);
        }
        // Extract for other_liabilities
        if(isset($edit_data['other_liabilities'])) {
            if(strpos($edit_data['other_liabilities'], 'नहीं - ') === 0) {
                $edit_data['other_liabilities_yn'] = 'नहीं';
                $edit_data['other_liabilities_desc'] = substr($edit_data['other_liabilities'], strlen('नहीं - '));
            } else {
                $edit_data['other_liabilities_yn'] = $edit_data['other_liabilities'];
                $edit_data['other_liabilities_desc'] = '';
            }
        } else {
            $edit_data['other_liabilities_yn'] = '';
            $edit_data['other_liabilities_desc'] = '';
        }
    }
}

// Helper function to safe echo
function val($key) {
    global $edit_data;
    return isset($edit_data[$key]) ? $edit_data[$key] : '';
}

page_header_start("Cold Storage Management");
?>

<style>
    /* Theme Colors */
    :root {
        --theme-primary: #FF8E00;
        --theme-secondary: #ffcd85; /* Slightly darker than FFE5B4 */
        --theme-hover: #e07d00;
    }

    .card {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        border: none;
    }

    .card-header {
        background-color: var(--theme-primary);
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .btn-primary {
        background-color: var(--theme-primary);
        border-color: var(--theme-primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--theme-hover);
        border-color: var(--theme-hover);
    }
    
    .btn-info {
        background-color: #1DC7EA;
        color: white;
        border-color: #1DC7EA;
    }

    /* Table Styling */
    .table-bordered thead th {
        background-color: var(--theme-secondary) !important; 
        color: #000 !important;
        border: 1px solid #000 !important;
        text-align: center;
        vertical-align: middle !important;
        font-weight: 500;
        font-size: 20px; 
        background-image: none !important;
        padding: 12px 6px !important;
    }

    .table-bordered tbody td {
        vertical-align: middle !important;
        font-size: 16px;
        padding: 10px 6px;
        color: #000;
        border: 1px solid #000 !important;
    }

    /* Column Numbering Row */
    .col-number td {
        background-color: #e9ecef;
        font-weight: 500;
        text-align: center;
        color: #333;
        border: 1px solid #000 !important;
        font-size: 18px; 
        padding: 8px 6px;
    }
    
    .w-sno { width: 50px; }

    .card .table tbody td:last-child, 
    .card .table thead th:last-child {
        display: table-cell !important;
        padding-right: 8px !important;
    }
    
    /* Spacing between fields */
    .form-group {
        margin-bottom: 25px !important;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        font-size: 15px !important;
        margin-bottom: 8px !important;
        color: #333;
        line-height: 1.4;
    }

    #formCard .card-body {
        padding: 10px !important;
    }

    /* --- Interactive UI Enhancements --- */
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #formCard {
        display: none;
        animation: fadeInUp 0.5s ease
        -out forwards;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        padding: 20px 25px !important;
    }

    .card-title {
        color: #2c3e50;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        margin: 0 !important;
    }

    .section-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #2e7d32; /* Changed to green for a fresh look */
        margin-top: 35px !important;
        margin-bottom: 25px !important;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8f5e9;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .section-title::before {
        content: "";
        display: inline-block;
        width: 14px;
        height: 14px;
        background: #4caf50;
        border-radius: 4px; /* Square with rounded corners */
        box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
    }

    .form-group {
        margin-bottom: 25px !important; /* Increased spacing to reduce congestion */
        transition: all 0.2s ease;
    }

    .form-group label {
        font-weight: 600 !important;
        color: #455a64 !important;
        margin-bottom: 8px !important;
        font-size: 14px !important;
    }

    .form-control {
        height: 45px !important; /* Premium height */
        border: 1.5px solid #e0e0e0 !important;
        border-radius: 10px !important;
        padding: 10px 16px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        background-color: #ffffff !important;
        font-size: 14px !important;
    }

    .form-control:focus {
        border-color: #4caf50 !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1) !important;
        outline: none !important;
        transform: translateY(-1px);
    }

    .btn-primary.btn-fill {
        background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%) !important;
        border: none !important;
        padding: 12px 28px !important;
        font-weight: 600 !important;
        border-radius: 10px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3) !important;
    }

    .btn-primary.btn-fill:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4) !important;
        filter: brightness(1.1);
    }

    .btn-secondary {
        border-radius: 10px !important;
        padding: 12px 28px !important;
        font-weight: 600 !important;
        background: #ffffff !important;
        border: 1.5px solid #e0e0e0 !important;
        color: #546e7a !important;
        transition: all 0.3s ease !important;
    }

    .btn-secondary:hover {
        background: #f5f7f9 !important;
        border-color: #cfd8dc !important;
        color: #263238 !important;
    }

    .card {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05) !important;
        overflow: hidden;
    }

    #historyCard:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
    }

    .truncate-text {
        max-width: 200px; /* Increased width */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
    }

    .table {
        min-width: 100%; /* Changed to full width as per user image style */
    }
    
    @media (min-width: 1200px) {
        .table {
            width: 100%;
        }
    }

    .table th {
        background-color: #f8f9fa !important;
        color: #37474f !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        font-size: 13px !important;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6 !important;
    }

    .table td {
        padding: 15px 12px !important;
        vertical-align: middle !important;
        color: #455a64 !important;
        font-size: 14px !important;
    }

    .custom-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 15px;
    }

    .custom-checkbox input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    /* Success Page Styles */
    #success {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        padding: 40px 30px;
        margin-top: 30px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
    }

    #success h4 {
        color: #28a745;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 28px;
    }

    #success p {
        font-size: 16px;
        line-height: 1.6;
        color: #555;
        margin-bottom: 30px;
    }

    #success .btn {
        margin: 0 10px 10px 0;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    #success .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
</style>

<?php
page_header_end();
page_sidebar();
?>
<script src="js/survey_validation.js"></script>

<!-- Modal for Viewing Details -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: var(--theme-primary); color: white;">
        <h5 class="modal-title" id="viewModalLabel">Detailed Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" style="color: white;">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="modalTable">
                <tbody>
                    <!-- Content will be populated by JS -->
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="row">
    <div class="col-md-12">
        <!-- HISTORY / REPORT CARD -->
        <div id="historyCard" class="card" style="<?php echo (isset($show_success) && $show_success) ? 'display:none;' : 'display:block;'; ?>">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                       <h4 class="card-title"><strong>निष्क्रिय शीतगृहों की सूचना एवं कार्ययोजना</strong></h4>
<p class="card-category"><strong>Information regarding Inactive Cold Storages</strong></p>
 </div>
                    <div class="col-md-4 text-right">
                         <a href="cold_storage_export.php" class="btn btn-warning btn-fill">
                             <i class="fa fa-file-excel-o"></i> Export to Excel
                         </a>
                         <?php if(isset($_SESSION['username']) && ($_SESSION['user_type'] == 'ar' || $_SESSION['usertype'] == '3' || $_SESSION['usertype'] == 'sadmin')) { ?>
                         <button id="addNewBtn" class="btn btn-info btn-fill">
                             <i class="fa fa-plus"></i> Add New Entry
                         </button>
                         <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="border-bottom p-3 bg-light">
                <form method="GET" action="cold_storage.php">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0 small">मंडल (Mandal)</label>
                                <select name="f_division" id="f_division" class="form-control form-control-sm" onchange="filter_fill_district(this.value);" style="height: 32px !important; font-size: 13px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php
                                    $sql_div = 'select * from master_division';
                                    $res_div = execute_query($sql_div);
                                    while($row_div = mysqli_fetch_assoc($res_div)){
                                        $selected = (isset($_GET['f_division']) && $_GET['f_division'] == $row_div['sno']) ? 'selected' : '';
                                        echo '<option value="'.$row_div['sno'].'" '.$selected.'>'.$row_div['division_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0 small">जनपद (District)</label>
                                <select name="f_district" id="f_district" class="form-control form-control-sm" onchange="filter_fill_tehseel(this.value);" style="height: 32px  !important; font-size: 13px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php 
                                    if(isset($_GET['f_division']) && $_GET['f_division'] != '') {
                                        $dist_sql = "select * from master_district where division_id = '".$_GET['f_division']."'";
                                        $dist_res = execute_query($dist_sql);
                                        while($d_row = mysqli_fetch_assoc($dist_res)){
                                            $selected = (isset($_GET['f_district']) && $_GET['f_district'] == $d_row['sno']) ? 'selected' : '';
                                            echo '<option value="'.$d_row['sno'].'" '.$selected.'>'.$d_row['district_name'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0 small">तहसील (Tehsil)</label>
                                <select name="f_tehseel" id="f_tehseel" class="form-control form-control-sm" onchange="filter_fill_block(this.value);" style="height: 32px !important; font-size: 13px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php 
                                    if(isset($_GET['f_district']) && $_GET['f_district'] != '') {
                                        $teh_sql = "select * from master_tehseel where district_id = '".$_GET['f_district']."'";
                                        $teh_res = execute_query($teh_sql);
                                        while($t_row = mysqli_fetch_assoc($teh_res)){
                                            $selected = (isset($_GET['f_tehseel']) && $_GET['f_tehseel'] == $t_row['sno']) ? 'selected' : '';
                                            echo '<option value="'.$t_row['sno'].'" '.$selected.'>'.$t_row['tehseel_name'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0 small">विकासखंड (Block)</label>
                                <select name="f_block" id="f_block" class="form-control form-control-sm" style="height: 32px !important; font-size: 13px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php 
                                    if(isset($_GET['f_tehseel']) && $_GET['f_tehseel'] != '') {
                                        $blk_sql = "select * from master_block where tehseel_id = '".$_GET['f_tehseel']."'";
                                        $blk_res = execute_query($blk_sql);
                                        while($b_row = mysqli_fetch_assoc($blk_res)){
                                            $selected = (isset($_GET['f_block']) && $_GET['f_block'] == $b_row['sno']) ? 'selected' : '';
                                            echo '<option value="'.$b_row['sno'].'" '.$selected.'>'.$b_row['block_name'].'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                         <div class="col-md-2">
                             <div class="form-group mb-2">
                                 <label class="mb-0 small">शीतगृह की स्थिति</label>
                                 <select name="f_building_status" class="form-control form-control-sm" style="height: 32px !important; font-size: 13px !important;">
                                     <option value="">All</option>
                                     <option value="good" <?php echo (isset($_GET['f_building_status']) && $_GET['f_building_status'] == 'good') ? 'selected' : ''; ?>>संचालित</option>
                                     <option value="repairable" <?php echo (isset($_GET['f_building_status']) && $_GET['f_building_status'] == 'repairable') ? 'selected' : ''; ?>>असंचालित</option>
                                     <option value="bad" <?php echo (isset($_GET['f_building_status']) && $_GET['f_building_status'] == 'bad') ? 'selected' : ''; ?>>अन्य</option>
                                 </select>
                             </div>
                         </div>
                         <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-0 small">क्षमता </label>
                                <div class="input-group input-group-sm">
                                    <select name="f_cap_op" class="form-control p-1" style="max-width: 45px !important; height: 32px !important;">
                                        <option value="=" <?php echo (isset($_GET['f_cap_op']) && $_GET['f_cap_op'] == '=') ? 'selected' : ''; ?>>=</option>
                                        <option value=">" <?php echo (isset($_GET['f_cap_op']) && $_GET['f_cap_op'] == '>') ? 'selected' : ''; ?>>></option>
                                        <option value="<" <?php echo (isset($_GET['f_cap_op']) && $_GET['f_cap_op'] == '<') ? 'selected' : ''; ?>><</option>
                                    </select>
                                    <input type="number" step="0.01" name="f_capacity" class="form-control" value="<?php echo $_GET['f_capacity'] ?? ''; ?>" style="height: 32px !important;">
                                </div>
                            </div>
                        </div>
                         <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-0 small">भूमि क्षेत्रफल</label>
                                <div class="input-group input-group-sm">
                                    <select name="f_land_op" class="form-control p-1" style="max-width: 45px !important; height: 32px !important;">
                                        <option value="=" <?php echo (isset($_GET['f_land_op']) && $_GET['f_land_op'] == '=') ? 'selected' : ''; ?>>=</option>
                                        <option value=">" <?php echo (isset($_GET['f_land_op']) && $_GET['f_land_op'] == '>') ? 'selected' : ''; ?>>></option>
                                        <option value="<" <?php echo (isset($_GET['f_land_op']) && $_GET['f_land_op'] == '<') ? 'selected' : ''; ?>><</option>
                                    </select>
                                    <input type="number" step="0.0001" name="f_land_area" class="form-control" value="<?php echo $_GET['f_land_area'] ?? ''; ?>" style="height: 32px !important;">
                                </div>
                            </div>
                        </div>
                         <div class="col-md-3 d-flex align-items-end mb-2" style="gap: 5px;">
                             <button type="submit" class="btn btn-primary btn-sm btn-fill"><i class="fa fa-search"></i> Search</button>
                             <a href="cold_storage.php" class="btn btn-secondary btn-sm btn-fill"><i class="fa fa-refresh"></i> Reset</a>
                         </div>
                    </div>
                </form>
            </div>
          
            <div class="card-body">
                <div style="overflow-x: auto; width: 100%;">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr class="bg-light">
                                <th class="text-center">क्र.सं.</th>
                                <th>जनपद / तहसील / ब्लॉक</th>
                                <th>समिति का नाम</th>
                                <th>क्षमता</th>
                                <th>स्थिति (Active)</th>
                                <th>भूमि का क्षेत्रफल</th>
                                <th>भवन की स्थिति</th>
                                <th>स्वामित्व</th>
                                <th>बिजली बिल (₹)</th>
                                <th>कुल ऋण (₹)</th>
                                <th>कर्मचारी</th>
                                <th>एक्शन</th>
                            </tr>
                        </thead>
                        

                        <tbody>
                            <?php
                            $check_table = execute_query("SHOW TABLES LIKE 'cold_storage_entries'");
                            if(mysqli_num_rows($check_table) > 0) {
                                // Filter Logic
                                $where = [];
                                
                                // Location Filtering
                                if(!empty($_GET['f_block'])){
                                    $blk_res = execute_query("select block_name from master_block where sno='".$_GET['f_block']."'");
                                    $blk_row = mysqli_fetch_assoc($blk_res);
                                    if($blk_row) $where[] = "block_name = '".$blk_row['block_name']."'";
                                }
                                elseif(!empty($_GET['f_tehseel'])){
                                    $teh_res = execute_query("select tehseel_name from master_tehseel where sno='".$_GET['f_tehseel']."'");
                                    $teh_row = mysqli_fetch_assoc($teh_res);
                                    if($teh_row) $where[] = "tehseel_name = '".$teh_row['tehseel_name']."'";
                                }
                                elseif(!empty($_GET['f_district'])){
                                    $dist_res = execute_query("select district_name from master_district where sno='".$_GET['f_district']."'");
                                    $dist_row = mysqli_fetch_assoc($dist_res);
                                    if($dist_row) $where[] = "district_name = '".$dist_row['district_name']."'";
                                }
                                elseif(!empty($_GET['f_division'])){
                                    $dist_res = execute_query("select district_name from master_district where division_id='".$_GET['f_division']."'");
                                    $dist_names = [];
                                    while($row = mysqli_fetch_assoc($dist_res)){
                                        $dist_names[] = "'".$row['district_name']."'";
                                    }
                                    if(!empty($dist_names)){
                                        $where[] = "district_name IN (".implode(',', $dist_names).")";
                                    } else {
                                        $where[] = "1=0"; // No districts found
                                    }
                                }
                                

                                
                                // Capacity
                                if(!empty($_GET['f_capacity'])){
                                    $op = $_GET['f_cap_op'] ?? '=';
                                    if(in_array($op, ['<','>','='])) {
                                        $where[] = "capacity $op '".mysqli_real_escape_string($db, $_GET['f_capacity'])."'";
                                    }
                                }



                                // Building Status
                                if(!empty($_GET['f_building_status'])){
                                    $where[] = "building_status = '".mysqli_real_escape_string($db, $_GET['f_building_status'])."'";
                                }

                                // Land Area
                                if(!empty($_GET['f_land_area'])){
                                    $op = $_GET['f_land_op'] ?? '=';
                                    if(in_array($op, ['<','>','='])) {
                                        $where[] = "land_area_total $op '".mysqli_real_escape_string($db, $_GET['f_land_area'])."'";
                                    }
                                }
                                
                                $where_sql = count($where) ? "WHERE ".implode(' AND ', $where) : "";
                                $entries = execute_query("SELECT * FROM cold_storage_entries $where_sql ORDER BY created_at DESC");
                                $i = 1;
                                while($row = mysqli_fetch_assoc($entries)) {
                                    $emp_data = json_decode(isset($row['employees_data']) ? $row['employees_data'] : '[]', true) ?: [];
                                    $emp_count = count($emp_data);
                                    $total_loan = (float)(isset($row['ncdc_loan']) ? $row['ncdc_loan'] : 0) + (float)(isset($row['bank_loan']) ? $row['bank_loan'] : 0) + (float)(isset($row['upcb_loan']) ? $row['upcb_loan'] : 0) + (float)(isset($row['dcb_loan']) ? $row['dcb_loan'] : 0);
                                    
                                    echo "<tr>";
                                    echo "<td class='text-center'>{$i}</td>";
                                    echo "<td>
                                            <div style='font-weight:700; color:#2c3e50;'>{$row['district_name']}</div>
                                            <small class='text-muted'>{$row['tehseel_name']} / {$row['block_name']}</small>
                                          </td>";
                                    echo "<td><span class='truncate-text' title='".(isset($row['society_name']) ? $row['society_name'] : '')."'>".(isset($row['society_name']) ? $row['society_name'] : '')."</span></td>";
                                    echo "<td><strong>{$row['capacity']}</strong> <small>मै.टन</small></td>";
                                    echo "<td>";
                                    if(isset($row['is_active']) && $row['is_active'] == 'active') {
                                        echo "<span class='badge badge-success' style='background:#4caf50;'>सक्रिय (Active)</span>";
                                    } elseif(isset($row['is_active']) && $row['is_active'] == 'inactive') {
                                        echo "<span class='badge badge-danger' style='background:#f44336;'>असंचालित</span><br>";
                                        echo "<small class='text-danger' style='font-weight:600;'>".(isset($row['inactivity_reason']) ? $row['inactivity_reason'] : '')."</small><br>";
                                        echo "<small class='text-muted'>".(isset($row['inactivity_description']) ? $row['inactivity_description'] : '')."</small>";
                                    } else {
                                        echo "<span class='badge badge-warning'>अन्य</span>";
                                    }
                                    echo "</td>";
                                    echo "<td>".(isset($row['land_area_total']) ? $row['land_area_total'] : '0')." हे.</td>";
                                    echo "<td>" . strtoupper(isset($row['building_status']) ? $row['building_status'] : 'N/A') . "</td>";
                                    echo "<td>" . strtoupper(isset($row['ownership_type']) ? $row['ownership_type'] : 'N/A') . "</td>";
                                    echo "<td>₹" . number_format(isset($row['elec_bill']) ? $row['elec_bill'] : 0, 2) . "</td>";
                                    echo "<td>₹" . number_format($total_loan, 2) . "</td>";
                                    echo "<td class='text-center'><span class='badge badge-info' style='background:#2196f3;'>$emp_count</span></td>";
                                    echo "<td style='white-space:nowrap;'>
                                            <div class='btn-group' style='display:flex; gap:5px;'>
                                                <a href='?edit_id={$row['id']}' class='btn btn-warning btn-sm' title='Edit'><i class='fa fa-edit'></i></a>
                                                <button type='button' class='btn btn-info btn-sm' onclick='viewDetails(".htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8').")' title='View'><i class='fa fa-eye'></i></button>
                                                <a href='?delete_id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")' title='Delete'><i class='fa fa-trash'></i></a>
                                            </div>
                                          </td>";
                                    echo "</tr>";
                                    $i++;
                                }
                            } else { ?>
                                <tr>
                                    <td colspan="12" class="text-center">कोई डेटा उपलब्ध नहीं है (No Data Available)</td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <!-- FORM CARD (ONLY FOR AR OR ADMIN) -->
        <?php if(isset($_SESSION['username']) && ($_SESSION['user_type'] == 'ar' || $_SESSION['usertype'] == '3' || $_SESSION['usertype'] == 'sadmin')) { ?>
        <div id="formCard" class="card" style="display:none;">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title">शीतगृह विवरण (Cold Storage Details)</h4>
                        <p class="card-category">कृपया सभी विवरण ध्यानपूर्वक भरें</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <button id="backToHistoryBtn" class="btn btn-secondary btn-fill">
                            <i class="fa fa-arrow-left"></i> वापस (Back)
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="old_building_photo" value="<?php echo val('building_photo'); ?>">
                    <input type="hidden" name="old_ownership_photo" value="<?php echo val('ownership_photo'); ?>">
                    
                    <h5 class="section-title">1. सामान्य जानकारी (Basic Info)</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>मंडल (Mandal)*</label>
                                <select name="division_name" id="division_name" class="form-control" required onChange="fill_district(this.value);">
                                    <option value="">-- मंडल चुनें --</option>
                                    <?php
                                    $res_div = execute_query('select * from master_division');
                                    while($row_div = mysqli_fetch_assoc($res_div)){
                                        echo '<option value="'.$row_div['sno'].'">'.$row_div['division_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>जनपद (District) *</label>
                                <select name="district_name" id="district_name" class="form-control" required onChange="fill_tehseel(this.value);">
                                    <option value="">-- जनपद चुनें --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>तहसील (Tehsil)</label>
                                <select name="tehseel_name" id="tehseel_name" class="form-control" onChange="fill_block(this.value);">
                                    <option value="">-- तहसील चुनें --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>विकासखंड (Block)</label>
                                <select name="block_name" id="block_name" class="form-control" onChange="fill_society(this.value);">
                                    <option value="">-- विकासखंड चुनें --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>समिति (Society)</label>
                                <select name="society_name" id="society_name" class="form-control">
                                    <option value="">-- समिति चुनें --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>शीतगृह की भण्डारण क्षमता (मै.टन)</label>
                                <input type="number" step="0.01" name="capacity" class="form-control" value="<?php echo val('capacity'); ?>">
                            </div>
                        </div>
                       <div class="col-md-3">
    <div class="form-group">
        <label>शीतगृह की स्थिति</label>
        <select name="is_active" class="form-control" onchange="toggleInactivity(this.value)">
            <option value="">-- चयन करें --</option>
            <option value="active" <?php if(val('is_active')=='active') echo 'selected'; ?>>
                संचालित
            </option>
            <option value="inactive" <?php if(val('is_active')=='inactive') echo 'selected'; ?>>
                असंचालित
            </option>
            <option value="under_repair" <?php if(val('is_active')=='under_repair') echo 'selected'; ?>>
               अन्य
            </option>
            
        </select>
    </div>
</div>

                        <div class="col-md-12" id="inactivity_div" style="<?php echo (val('is_active') == 'inactive') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="row">
                               
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>असंचालित होने का कारण</label>
                                        <select name="inactivity_reason" class="form-control">
                                            <option value="">-- चयन करें --</option>
                                            <option value="निष्प्रयोग" <?php if(val('inactivity_reason')=='निष्प्रयोग') echo 'selected'; ?>>निष्प्रयोज्य</option>
                                            <option value="मरम्मत योग्य" <?php if(val('inactivity_reason')=='मरम्मत योग्य') echo 'selected'; ?>>मरम्मत योग्य</option>
                                            <option value="विवादित" <?php if(val('inactivity_reason')=='विवादित') echo 'selected'; ?>>विवादित</option>
                                        </select>
                                    </div>
                                </div>
                                 <div class="col-md-4">
                                    <div class="form-group">
                                        <label>असंचालित होने की वर्ष</label>
                                       <input type="text" name="inactivity_date" class="form-control"
       value="<?php echo val('inactivity_date'); ?>"
       placeholder="वर्ष दर्ज करें">

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>विवरण</label>
                                        <textarea name="inactivity_description" class="form-control" rows="2"><?php echo val('inactivity_description'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-3">
                            <div class="form-group">
                                <label>शीतगृह भवन की फोटो (अधिकतम 4 फोटो)</label>
                                <input type="file" name="building_photo[]" class="form-control" multiple onchange="checkFileLimit(this, 4); previewImages(this, 'building_preview')">
                                <small class="text-muted">आप एक साथ 4 फोटो तक चुन सकते हैं।</small>
                                <div id="building_preview" class="mt-2 d-flex flex-wrap" style="gap: 5px;"></div>
                                <?php 
                                $b_photo = val('building_photo');
                                $photos = json_decode($b_photo, true);
                                if(!$photos && !empty($b_photo)) $photos = [$b_photo];
                                if($photos): ?>
                                    <div class="mt-2 d-flex flex-wrap" style="gap: 5px;">
                                        <small class="w-100 text-muted">मौजूदा फोटो:</small>
                                        <?php foreach($photos as $p): ?>
                                            <img src="<?php echo $p; ?>" style="height: 60px; border-radius: 4px; border: 1px solid #ddd;">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                                </div>
                    </div>

                    <h5 class="section-title">2. परिसम्पत्ति विवरण (Assets)</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>कुल भूमि क्षेत्रफल (हेक्टेयर)</label>
                                <input type="number" step="0.0001" name="land_area_total" class="form-control" value="<?php echo val('land_area_total'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>खाली पड़ी भूमि (हेक्टेयर)</label>
                                <input type="number" step="0.0001" name="vacant_land_area" class="form-control" value="<?php echo val('vacant_land_area'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>जनपद के रैक पाइण्ट से दूरी (कि.मी.)</label>
                                <input type="number" step="0.01" name="rack_distance" class="form-control" value="<?php echo val('rack_distance'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>भूमि से जुड़ा कोई विवाद है/नहीं</label>
                                <select name="is_litigation" class="form-control" onchange="toggleLitigation(this.value)">
                                    <option value="">--Select--</option>
                                    <option value="yes" <?php if(val('is_litigation')=='yes') echo 'selected'; ?>>हाँ</option>
                                    <option value="no" <?php if(val('is_litigation')=='no') echo 'selected'; ?>>नहीं</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 litigation_div" style="<?php echo (val('is_litigation') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="form-group">
                                <label>विवाद दर्ज करें</label>
                                <textarea name="litigation_desc" class="form-control" rows="2" placeholder="यहाँ विवरण लिखें..."><?php echo val('litigation_desc'); ?></textarea>
                            </div>
                        </div>
                    </div>
                    

                    
                    <h5 class="section-title">3. शीतगृह भूमि का स्वामित्व (Ownership)</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>शीतगृह की स्थिति</label>
                                <select name="ownership_type" id="ownership_type" class="form-control" onchange="toggleOwnership(this.value)">
                                    <option value="">-- चयन करें --</option>
                                    <option value="owned" <?php if(val('ownership_type')=='owned') echo 'selected'; ?>>समिति के स्वामित्व में</option>
                                    <option value="rented" <?php if(val('ownership_type')=='rented') echo 'selected'; ?>>किराये पर</option>
                                    <option value="other" <?php if(val('ownership_type')=='other') echo 'selected'; ?>>अन्य स्थिति</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 rented_only" style="<?php echo (val('ownership_type') == 'rented') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="form-group">
                                <label>मासिक किराया (रुपये)</label>
                                <input type="number" step="0.01" name="monthly_rent" class="form-control" value="<?php echo val('monthly_rent'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3 rented_only" style="<?php echo (val('ownership_type') == 'rented') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="form-group">
                                <label>शीतगृह का क्षेत्रफल (Sq Ft)</label>
                                <input type="number" step="0.01" name="building_area_sqft" class="form-control" value="<?php echo val('building_area_sqft'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>स्वामित्व संबंधी फोटो/दस्तावेज</label>
                                <input type="file" name="ownership_photo" class="form-control" onchange="previewImages(this, 'ownership_preview')">
                                <div id="ownership_preview" class="mt-2 d-flex flex-wrap" style="gap: 5px;"></div>
                                <?php if(val('ownership_photo')): ?>
                                    <div class="mt-1">
                                        <small class="w-100 text-muted d-block">मौजूदा फोटो:</small>
                                        <img src="<?php echo val('ownership_photo'); ?>" style="height: 50px; border-radius: 4px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3" id="ownership_other_div" style="<?php echo (val('ownership_type') == 'other') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="form-group">
                                <label>स्वामित्व विवरण/अन्य</label>
                                <textarea name="ownership_desc" class="form-control" rows="1"><?php echo val('ownership_desc'); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>पहुंच मार्ग का प्रकार</label>
                                <select name="approach_road_type" class="form-control" onchange="toggleApproachRoad(this.value)">
                                    <option value="">-- चयन करें --</option>
                                    <option value="nh" <?php if(val('approach_road_type')=='nh') echo 'selected'; ?>>नेशनल हाईवे</option>
                                    <option value="expressway" <?php if(val('approach_road_type')=='expressway') echo 'selected'; ?>>एक्सप्रेसवे</option>
                                    <option value="sh" <?php if(val('approach_road_type')=='sh') echo 'selected'; ?>>स्टेट हाईवे</option>
                                    <option value="mdr" <?php if(val('approach_road_type')=='mdr') echo 'selected'; ?>>एम.डी.आर.</option>
                                    <option value="odr" <?php if(val('approach_road_type')=='odr') echo 'selected'; ?>>ओ.डी.आर.</option>
                                    <option value="rural_road" <?php if(val('approach_road_type')=='rural_road') echo 'selected'; ?>>ग्रामीण सड़क</option>
                                    <option value="ordinary" <?php if(val('approach_road_type')=='ordinary') echo 'selected'; ?>>कच्ची सड़क</option>
                                    <option value="other" <?php if(val('approach_road_type')=='other') echo 'selected'; ?>>अन्य</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="approach_other_div" style="<?php echo (val('approach_road_type') == 'other') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="form-group">
                                <label>पहुंच मार्ग का विवरण/अन्य</label>
                                <textarea name="approach_road_desc" class="form-control" rows="1"><?php echo val('approach_road_desc'); ?></textarea>
                            </div>
                        </div>
                    </div>

                   

                    <h5 class="section-title">4. आधारभूत सुविधाएं</h5>
                    
                    <!-- (I) विद्युत कनेक्शन -->
                    <div style="margin-bottom: 20px;">
                        <h6 style="font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">(I) विद्युत कनेक्शन</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>विद्युत कनेक्शन है या नहीं ?</label>
                                    <select name="elec_conn" id="elec_conn" class="form-control" onchange="toggleElecDetail(this.value)">
                                        <option value="">-- चुनें --</option>
                                        <option value="yes" <?php if(val('elec_conn')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('elec_conn')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 elec_yes_row" style="<?php echo (val('elec_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>यदि है तो चालू है या नहीं ?</label>
                                    <select name="elec_working" class="form-control" onchange="toggleElecWorking(this.value)">
                                        <option value="">-- चुनें --</option>
                                        <option value="yes" <?php if(val('elec_working')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('elec_working')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 elec_no_row" style="<?php echo (val('elec_conn') == 'no') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>यदि नहीं है तो प्रस्ताव</label>
                                    <textarea name="elec_proposal" class="form-control" rows="1"><?php echo val('elec_proposal'); ?></textarea>
                                </div>
                            </div>
                            <!-- Show if working NO -->
                            <div class="col-md-3 elec_working_no" style="<?php echo (val('elec_working') == 'no') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>यदि चालू नहीं है तो कारण</label>
                                    <input type="text" name="elec_not_working_reason" class="form-control" value="<?php echo val('elec_not_working_reason'); ?>">
                                </div>
                            </div>
                            <!-- Show if working YES -->
                            <div class="col-md-3 elec_working_yes" style="<?php echo (val('elec_working') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>बिल नियमित भुगतान हो रहा है?</label>
                                    <select name="elec_bill_regular" class="form-control" onchange="toggleElecBill(this.value)">
                                        <option value="">-- चुनें --</option>
                                        <option value="yes" <?php if(val('elec_bill_regular')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('elec_bill_regular')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row elec_bill_no" style="<?php echo (val('elec_bill_regular') == 'no' && val('elec_working') == 'yes') ? 'display:flex;' : 'display:none;'; ?>">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>कितने माह से भुगतान नहीं है?</label>
                                    <input type="number" name="elec_months_due" class="form-control" value="<?php echo val('elec_months_due'); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>बकाया धनराशि (रुपये)</label>
                                    <input type="number" step="0.01" name="elec_outstanding" class="form-control" value="<?php echo val('elec_outstanding'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- (II) सोलर कनेक्शन -->
                    <div style="margin-bottom: 20px;">
                        <h6 style="font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">(II) सोलर कनेक्शन</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>सोलर की उपलब्धता है या नहीं ?</label>
                                    <select name="solar_conn" class="form-control" onchange="toggleSolarDetail(this.value)">
                                        <option value="">-- चुनें --</option>
                                        <option value="yes" <?php if(val('solar_conn')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('solar_conn')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 solar_yes_row" style="<?php echo (val('solar_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>यदि है तो चालू है या नहीं ?</label>
                                    <select name="solar_working" class="form-control" onchange="toggleSolarWorking(this.value)">
                                        <option value="">-- चुनें --</option>
                                        <option value="yes" <?php if(val('solar_working')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('solar_working')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 solar_working_yes" style="<?php echo (val('solar_working') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>बैट्री की स्थिति</label>
                                    <select name="solar_battery_status" class="form-control">
                                        <option value="good" <?php if(val('solar_battery_status')=='good') echo 'selected'; ?>>अच्छी (Good)</option>
                                        <option value="poor" <?php if(val('solar_battery_status')=='poor') echo 'selected'; ?>>खराब (Poor)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- (III) इण्टरनेट कनेक्शन -->
                    <div style="margin-bottom: 20px;">
                        <h6 style="font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">(III) इण्टरनेट कनेक्शन</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>क्या इण्टरनेट कनेक्शन है ?</label>
                                    <select name="internet_conn" class="form-control" onchange="toggleInternetDetail(this.value)">
                                        <option value="">-- चुनें --</option>
                                        <option value="yes" <?php if(val('internet_conn')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('internet_conn')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 internet_yes_row" style="<?php echo (val('internet_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>सर्विस प्रोवाइडर का नाम</label>
                                    <select name="internet_provider" class="form-control">
                                        <option value="">-- चुनें --</option>
                                        <option value="bsnl" <?php if(val('internet_provider')=='bsnl') echo 'selected'; ?>>BSNL</option>
                                        <option value="jio" <?php if(val('internet_provider')=='jio') echo 'selected'; ?>>JIO</option>
                                        <option value="vodafone" <?php if(val('internet_provider')=='vodafone') echo 'selected'; ?>>Vodafone</option>
                                        <option value="airtel" <?php if(val('internet_provider')=='airtel') echo 'selected'; ?>>Airtel</option>
                                        <option value="sdwan" <?php if(val('internet_provider')=='sdwan') echo 'selected'; ?>>SDWAN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 internet_yes_row" style="<?php echo (val('internet_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>बिल नियमित भुगतान?</label>
                                    <select name="internet_bill_paid" class="form-control">
                                        <option value="yes" <?php if(val('internet_bill_paid')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('internet_bill_paid')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 internet_yes_row" style="<?php echo (val('internet_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                <div class="form-group">
                                    <label>कनेक्शन एक्टिव है?</label>
                                    <select name="internet_active" class="form-control">
                                        <option value="yes" <?php if(val('internet_active')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('internet_active')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- (IV) पेयजल की उपलब्धता -->
                    <div style="margin-bottom: 20px;">
                        <h6 style="font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">(IV) पेयजल की उपलब्धता</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>पेयजल की उपलब्धता है?</label>
                                    <select name="water_availability" class="form-control">
                                        <option value="yes" <?php if(val('water_availability')=='yes') echo 'selected'; ?>>हाँ</option>
                                        <option value="no" <?php if(val('water_availability')=='no') echo 'selected'; ?>>नहीं</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>आपूर्ति संचालित है?</label>
                                    <select name="water_status" class="form-control">
                                        <option value="operational" <?php if(val('water_status')=='operational') echo 'selected'; ?>>हाँ (संचालित)</option>
                                        <option value="non_operational" <?php if(val('water_status')=='non_operational') echo 'selected'; ?>>नहीं (असंचालित)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                    function toggleElecDetail(val) {
                        if(val == 'yes') {
                            $('.elec_yes_row').show();
                            $('.elec_no_row').hide();
                        } else if(val == 'no') {
                            $('.elec_yes_row').hide();
                            $('.elec_no_row').show();
                            $('.elec_working_yes, .elec_working_no, .elec_bill_no').hide();
                        } else {
                            $('.elec_yes_row, .elec_no_row, .elec_working_yes, .elec_working_no, .elec_bill_no').hide();
                        }
                    }
                    function toggleOwnership(val) {
                        if(val == 'rented') {
                            $('.rented_only').show();
                            $('#ownership_other_div').hide();
                        } else if(val == 'other') {
                            $('.rented_only').hide();
                            $('#ownership_other_div').show();
                        } else {
                            $('.rented_only').hide();
                            $('#ownership_other_div').hide();
                        }
                    }
                    function toggleApproachRoad(val) {
                        if(val == 'other') $('#approach_other_div').show();
                        else $('#approach_other_div').hide();
                    }
                    function toggleLitigation(val) {
                        if(val == 'yes') $('.litigation_div').show();
                        else $('.litigation_div').hide();
                    }
                    function toggleElecWorking(val) {
                        if(val == 'yes') {
                            $('.elec_working_yes').show();
                            $('.elec_working_no').hide();
                        } else if(val == 'no') {
                            $('.elec_working_yes').hide();
                            $('.elec_working_no').show();
                            $('.elec_bill_no').hide();
                        } else {
                            $('.elec_working_yes, .elec_working_no, .elec_bill_no').hide();
                        }
                    }
                    function toggleElecBill(val) {
                        if(val == 'no') $('.elec_bill_no').show();
                        else $('.elec_bill_no').hide();
                    }
                    function toggleSolarDetail(val) {
                        if(val == 'yes') $('.solar_yes_row').show();
                        else {
                            $('.solar_yes_row, .solar_working_yes').hide();
                        }
                    }
                    function toggleSolarWorking(val) {
                        if(val == 'yes') $('.solar_working_yes').show();
                        else $('.solar_working_yes').hide();
                    }
                    function toggleInternetDetail(val) {
                        if(val == 'yes') $('.internet_yes_row').show();
                        else $('.internet_yes_row').hide();
                    }
                    </script>

                    <h5 class="section-title">5.क्या शीतगृह मैं कोई ऋण हैं?</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>क्या ऋण हैं?</label>
                                <select name="has_loan" class="form-control" onchange="toggleLoan(this.value)">
                                    <option value="">--Select--</option>                                   
                                    <option value="yes" <?php if(val('has_loan')=='yes') echo 'selected'; ?>>हाँ</option>
                                     <option value="no" <?php if(val('has_loan')=='no') echo 'selected'; ?>>नहीं</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row loan_sub" style="<?php echo (val('has_loan') == 'yes') ? 'display:flex;' : 'display:none;'; ?>">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>एन.सी.डी.सी. (रुपये)</label>
                                <input type="number" step="0.01" name="ncdc_loan" class="form-control" value="<?php echo val('ncdc_loan'); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>व्यावसायिक बैंक (रुपये)</label>
                                <input type="number" step="0.01" name="bank_loan" class="form-control" value="<?php echo val('bank_loan'); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>यू.पी.सी.बी. (रुपये)</label>
                                <input type="number" step="0.01" name="upcb_loan" class="form-control" value="<?php echo val('upcb_loan'); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>डी.सी.बी. (रुपये)</label>
                                <input type="number" step="0.01" name="dcb_loan" class="form-control" value="<?php echo val('dcb_loan'); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>अन्य ऋण (Other Loan)</label>
                                <input type="text" name="other_loan_desc" class="form-control" value="<?php echo val('other_loan_desc'); ?>" placeholder="विवरण लिखें">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>अन्य ऋण की धनराशि</label>
                                <input type="number" step="0.01" name="other_loan_amount" class="form-control" value="<?php echo val('other_loan_amount'); ?>" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <h5 class="section-title">6. अन्य जानकारी (Other Info)</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <label>कार्यरत कर्मचारी:</label>
                            <table class="table table-bordered" id="employee_table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>पदनाम</th>
                                        <th>नाम</th>
                                        <th>पिता/पति का नाम</th>
                                        <th>मोबाइल नंबर</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $emps = json_decode(val('employees_data'), true) ?: [[]];
                                    foreach($emps as $e): ?>
                                    <tr>
                                        <td><input type="text" name="emp[designation][]" class="form-control" value="<?php echo isset($e['designation']) ? $e['designation'] : ''; ?>"></td>
                                        <td><input type="text" name="emp[name][]" class="form-control" value="<?php echo isset($e['name']) ? $e['name'] : ''; ?>"></td>
                                        <td><input type="text" name="emp[parent_name][]" class="form-control" value="<?php echo isset($e['parent_name']) ? $e['parent_name'] : ''; ?>"></td>
                                        <td><input type="text" name="emp[mobile][]" class="form-control" value="<?php echo isset($e['mobile']) ? $e['mobile'] : ''; ?>"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove()"><i class="fa fa-times"></i></button></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-info btn-sm mb-3" onclick="addEmployeeRow()"><i class="fa fa-plus"></i> कर्मचारी जोड़ें</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>कार्ययोजना (Future Plan)</label>
                                <textarea name="action_plan" class="form-control" rows="2"><?php echo val('action_plan'); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-right">
                            <button type="button" id="cancelBtn" class="btn btn-secondary mr-2">रद्द करें</button>
                            <button type="submit" class="btn btn-primary btn-fill"><i class="fa fa-save"></i> सुरक्षित करें</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <!-- SUCCESS CARD (Shown after successful submission) -->
        <?php if(isset($show_success) && $show_success): ?>
        <div id="successCard" class="card">
            <div class="card-header">
                <h4 class="card-title">✓ सफलता (Success)</h4>
            </div>
            <div class="card-body">
                <div id="success">
                    <div class="text-center">
                        <h4>प्रपत्र सफलता पूर्वक भरा गया</h4>
                        <p>आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
                            सत्यापन हेतु भेजा जाना है । कृप्या सत्यापन पर भेजने से पहले नीचे
                            दर्शायें
                            लिंक से फार्म खोल कर पुनः जाच कर लें । सब कुछ सही होने कि दशा में नीचे
                            दिये
                            बटन के माध्यम से सत्यापन के लिये आगे प्रेषित करें ।</p>
                        <button class="btn btn-info" onclick="window.open('cold_storage_report.php', '_blank');">प्रपत्र पुनः निरीक्षण के लिये देखे</button>
                        <button class="btn btn-secondary" onclick="window.location.href='cold_storage.php'">वापस जाएं</button>
                    </div>
                    <div class="col-md-12 text-center mt-4">
                        <p><input type="checkbox" style="height: 20px; border:1px solid;"
                                id="review_ack"
                                onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
                            मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी
                            सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
                        <button type="button" class="btn btn-danger"
                            id="verification_button" disabled="disabled">सत्यापन के लिये आगे प्रेषित
                            करें
                        </button>
                    </div>

                    <div class="col-sm-12 form-group my-auto text-center mt-3" id="send_otp_button2"
                        style="display: none">
                        <button type="button" name="verify_otp_btn" id="verify_otp_btn"
                            class="btn btn-info"
                            onClick="verify_otp()">आगे प्रेषित करे
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php } ?>
    </div>
</div>

<script>
    function fill_district(val) {
        $.ajax({
            type: "POST",
            url: "scripts/ajax.php?id=dist&term=a",
            data: { val: val },
            success: function(data) {
                data = JSON.parse(data);
                var options = '<option value="">-- जनपद चुनें --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.district_name}</option>`;
                });
                $('#district_name').html(options);
                $('#tehseel_name').html('<option value="">-- तहसील चुनें --</option>');
                $('#block_name').html('<option value="">-- विकासखंड चुनें --</option>');
                $('#society_name').html('<option value="">-- समिति चुनें --</option>');
            }
        });
    }

    function fill_tehseel(val) {
        $.ajax({
            type: "POST",
            url: "scripts/ajax.php?id=tehseel&term=a",
            data: { val: val },
            success: function(data) {
                data = JSON.parse(data);
                var options = '<option value="">-- तहसील चुनें --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.tehseel_name}</option>`;
                });
                $('#tehseel_name').html(options);
                $('#block_name').html('<option value="">-- विकासखंड चुनें --</option>');
                $('#society_name').html('<option value="">-- समिति चुनें --</option>');
            }
        });
    }

    function fill_block(val) {
        $.ajax({
            type: "POST",
            url: "scripts/ajax.php?id=block&term=a",
            data: { val: val },
            success: function(data) {
                data = JSON.parse(data);
                var options = '<option value="">-- विकासखंड चुनें --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.block_name}</option>`;
                });
                $('#block_name').html(options);
                $('#society_name').html('<option value="">-- समिति चुनें --</option>');
            }
        });
    }

    function fill_society(val) {
        var division = $('#division_name').val();
        var district = $('#district_name').val();
        var tehseel = $('#tehseel_name').val();
        
        $.ajax({
            type: "POST",
            url: "scripts/ajax.php?id=society&term=a",
            data: { 
                division: division,
                district: district,
                tehseel: tehseel,
                block: val,
                val: '' 
            },
            success: function(data) {
                data = JSON.parse(data);
                var options = '<option value="">-- समिति चुनें --</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}">${item.society_name}</option>`;
                });
                $('#society_name').html(options);
            }
        });
    }

    function toggleInactivity(val) {
        $('#inactivity_div').toggle(val === 'inactive');
    }

    function toggleBuildingDetails(val) {
        $('#repairable_options').toggle(val === 'repairable');
    }



    function toggleFacilities(val) {
        $('.facilities_sub').toggle(val === 'yes');
    }

    function toggleLoan(val) {
        $('.loan_sub').toggle(val === 'yes');
    }

    function addEmployeeRow() {
        var row = `<tr>
            <td><input type="text" name="emp[designation][]" class="form-control"></td>
            <td><input type="text" name="emp[name][]" class="form-control"></td>
            <td><input type="text" name="emp[parent_name][]" class="form-control"></td>
            <td><input type="text" name="emp[mobile][]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove()"><i class="fa fa-times"></i></button></td>
        </tr>`;
        $('#employee_table tbody').append(row);
    }

    function viewDetails(data) {
        var $tbody = $('#modalTable tbody');
        $tbody.empty();
        
        const labels = {
            'district_name': 'जनपद (District)',
            'tehseel_name': 'तहसील (Tehsil)',
            'block_name': 'विकासखंड (Block)',
            'society_name': 'समiti का नाम (Society)',
            'cs_name': 'शीतगृह का नाम (CS Name)',
            'capacity': 'क्षमता (Capacity)',
            'is_active': 'स्थिति (Status)',
            'inactivity_date': 'बंद होने की तिथि',
            'inactivity_reason': 'बंद होने का कारण',
            'land_area_total': 'कुल भूमि क्षेत्रफल',
            'vacant_land_area': 'रिक्त भूमि क्षेत्रफल',
            'is_litigation': 'विवादित?',
            'litigation_desc': 'विवाद का विवरण',
            'building_status': 'भवन की स्थिति',
            'ownership_type': 'स्वामित्व',
            'ownership_desc': 'स्वामित्व विवरण',
            'approach_road_type': 'पहुँच मार्ग',
            'approach_road_desc': 'पहुँच मार्ग विवरण',
            'elec_bill': 'बिजली बिल (₹)',
            'ncdc_loan': 'NCDC ऋण (₹)',
            'bank_loan': 'Bank ऋण (₹)',
            'upcb_loan': 'UPCB ऋण (₹)',
            'dcb_loan': 'DCB ऋण (₹)',
            'other_loan_amount': 'अन्य ऋण (₹)',
            'action_plan': 'कार्ययोजना'
        };

        Object.keys(labels).forEach(key => {
            var val = data[key] || '-';
            if(key === 'is_active') {
                if(val === 'active') val = 'सक्रिय (Active)';
                else if(val === 'inactive') val = 'असंचालित (Inactive)';
                else if(val === 'under_repair') val = 'अन्य (Other)';
            }
            if(key.includes('loan') || key === 'elec_bill') val = '₹' + parseFloat(val || 0).toLocaleString('en-IN');
            
            $tbody.append(`<tr>
                <th style="width:40%; background:#f8f9fa;">${labels[key]}</th>
                <td>${val}</td>
            </tr>`);
        });

        // Photos handling
        let buildingPhotosHtml = '-';
        if (data.building_photo) {
            try {
                let photos = JSON.parse(data.building_photo);
                if (Array.isArray(photos)) {
                    buildingPhotosHtml = photos.map(p => `<img src="${p}" style="height: 100px; margin: 5px; border-radius: 4px; border: 1px solid #ddd;">`).join('');
                } else {
                    buildingPhotosHtml = `<img src="${data.building_photo}" style="height: 100px; margin: 5px; border-radius: 4px; border: 1px solid #ddd;">`;
                }
            } catch (e) {
                buildingPhotosHtml = `<img src="${data.building_photo}" style="height: 100px; margin: 5px; border-radius: 4px; border: 1px solid #ddd;">`;
            }
        }
        $tbody.append(`<tr><th style="background:#f8f9fa;">शीतगृह भवन की फोटो</th><td>${buildingPhotosHtml}</td></tr>`);

        let ownershipPhotoHtml = '-';
        if (data.ownership_photo) {
            ownershipPhotoHtml = `<img src="${data.ownership_photo}" style="height: 100px; margin: 5px; border-radius: 4px; border: 1px solid #ddd;">`;
        }
        $tbody.append(`<tr><th style="background:#f8f9fa;">स्वामित्व संबंधी फोटो</th><td>${ownershipPhotoHtml}</td></tr>`);

        $('#viewModal').modal('show');
    }

    $(document).ready(function() {
        // Initialize DataTable - Commented out as table doesn't have ID yet
        /*
        if($.fn.DataTable.isDataTable('#historyTable')) {
            $('#historyTable').DataTable().destroy();
        }
        $('#historyTable').DataTable({
            "order": [[ 0, "desc" ]],
            "pageLength": 25,
            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                   "<'row'<'col-sm-12'tr>>" +
                   "<'row'<'col-sm-5'i><'col-sm-7'p>>"
        });
        */

        // Show/Hide Form Logic
        $('#addNewBtn').click(function() {
            $('#historyCard').fadeOut(300, function() {
                $('#formCard').fadeIn(300);
            });
        });

        $('#backToHistoryBtn, #cancelBtn').click(function() {
            $('#formCard').fadeOut(300, function() {
                $('#historyCard').fadeIn(300);
                window.location.href = 'cold_storage.php'; 
            });
        });

        <?php if($is_edit): ?>
            $('#historyCard').hide();
            $('#formCard').show();
            
            // Populate dropdowns in edit mode
            <?php 
            // Get IDs from names for edit mode
            $edit_division_id = '';
            $edit_district_id = '';
            $edit_tehseel_id = '';
            $edit_block_id = '';
            $edit_society_id = '';
            
            if(!empty($edit_data['district_name'])) {
                $dist_res = execute_query("SELECT sno, division_id FROM master_district WHERE district_name = '".mysqli_real_escape_string($db, $edit_data['district_name'])."'");
                if($dist_row = mysqli_fetch_assoc($dist_res)) {
                    $edit_district_id = $dist_row['sno'];
                    $edit_division_id = $dist_row['division_id'];
                }
            }
            
            if(!empty($edit_data['tehseel_name'])) {
                $teh_res = execute_query("SELECT sno FROM master_tehseel WHERE tehseel_name = '".mysqli_real_escape_string($db, $edit_data['tehseel_name'])."'");
                if($teh_row = mysqli_fetch_assoc($teh_res)) {
                    $edit_tehseel_id = $teh_row['sno'];
                }
            }
            
            if(!empty($edit_data['block_name'])) {
                $blk_res = execute_query("SELECT sno FROM master_block WHERE block_name = '".mysqli_real_escape_string($db, $edit_data['block_name'])."'");
                if($blk_row = mysqli_fetch_assoc($blk_res)) {
                    $edit_block_id = $blk_row['sno'];
                }
            }
            
            if(!empty($edit_data['society_name'])) {
                $soc_res = execute_query("SELECT sno FROM test2 WHERE col4 = '".mysqli_real_escape_string($db, $edit_data['society_name'])."'");
                if($soc_row = mysqli_fetch_assoc($soc_res)) {
                    $edit_society_id = $soc_row['sno'];
                }
            }
            ?>
            
            // Set division first
            <?php if($edit_division_id): ?>
            $('#division_name').val('<?php echo $edit_division_id; ?>');
            
            // Load districts for this division
            fill_district('<?php echo $edit_division_id; ?>');
            
            // Wait for districts to load, then set district
            setTimeout(function() {
                <?php if($edit_district_id): ?>
                $('#district_name').val('<?php echo $edit_district_id; ?>');
                
                // Load tehseels for this district
                fill_tehseel('<?php echo $edit_district_id; ?>');
                
                // Wait for tehseels to load, then set tehseel
                setTimeout(function() {
                    <?php if($edit_tehseel_id): ?>
                    $('#tehseel_name').val('<?php echo $edit_tehseel_id; ?>');
                    
                    // Load blocks for this tehseel
                    fill_block('<?php echo $edit_tehseel_id; ?>');
                    
                    // Wait for blocks to load, then set block
                    setTimeout(function() {
                        <?php if($edit_block_id): ?>
                        $('#block_name').val('<?php echo $edit_block_id; ?>');
                        
                        // Load societies for this block
                        fill_society('<?php echo $edit_block_id; ?>');
                        
                        // Wait for societies to load, then set society
                        setTimeout(function() {
                            <?php if($edit_society_id): ?>
                            $('#society_name').val('<?php echo $edit_society_id; ?>');
                            <?php endif; ?>
                        }, 500);
                        <?php endif; ?>
                    }, 500);
                    <?php endif; ?>
                }, 500);
                <?php endif; ?>
            }, 500);
            <?php endif; ?>
        <?php endif; ?>

        <?php if(isset($show_success) && $show_success): ?>
            $('#historyCard').hide();
            $('#formCard').hide();
        <?php endif; ?>
    });

    function checkFileLimit(input, limit) {
        if (input.files.length > limit) {
            alert("आप अधिकतम " + limit + " फोटो ही अपलोड कर सकते हैं।");
            input.value = "";
            $('#' + input.getAttribute('onchange').match(/'([^']+)'/)[1]).empty();
        }
    }

    function previewImages(input, previewId) {
        var preview = $('#' + previewId);
        preview.empty();
        if (input.files) {
            Array.from(input.files).forEach(file => {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.append('<img src="' + e.target.result + '" style="height: 60px; border-radius: 4px; border: 1px solid #ddd; object-fit: cover;">');
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // Filter dynamic scripts
    function filter_fill_district(val){
        var data = {"term":"b", "id":"dist", "val":val};
        $.ajax({
            type: "POST",
            url: 'scripts/survey_form_ajax.php',
            data: data, 
            success: function(data){
                var txt = '<option value="">All</option>';
                data = JSON.parse(data);
                $.each(data, function(key, value){
                    txt += '<option value="'+value.id+'">'+value.district_name+'</option>';
                });
                $("#f_district").html(txt);
                $("#f_tehseel").html('<option value="">All</option>'); 
                $("#f_block").html('<option value="">All</option>'); 
            }
        });
    }

    function filter_fill_tehseel(val){
        var data = {term:"b", id:"tehseel", val:val};
        $.ajax({
            type: "POST",
            url: 'scripts/survey_form_ajax.php',
            data: data, 
            success: function(data){
                var txt = '<option value="">All</option>';
                data = JSON.parse(data);
                $.each(data, function(key, value){
                    txt += '<option value="'+value.id+'">'+value.tehseel_name+'</option>';
                });
                $("#f_tehseel").html(txt);
                $("#f_block").html('<option value="">All</option>'); 
            }
        });
    }

    function filter_fill_block(val){
        var data = {term:"b", id:"block", val:val};
        $.ajax({
            type: "POST",
            url: 'scripts/survey_form_ajax.php',
            data: data, 
            success: function(data){
                var txt = '<option value="">All</option>';
                data = JSON.parse(data);
                $.each(data, function(key, value){
                    txt += '<option value="'+value.id+'">'+value.block_name+'</option>';
                });
                $("#f_block").html(txt);
            }
        });
    }
</script>

<?php
page_footer_start();
page_footer_end();
?>
