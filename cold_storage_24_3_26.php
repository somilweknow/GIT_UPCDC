<?php
include("scripts/settings.php");
/*error_reporting(E_ALL);/*

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

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Debug: Log POST data
    error_log("POST data received: " . print_r($_POST, true));

    // Security: Only AR or Sadmin can submit
    if (!isset($_SESSION['username']) || !($_SESSION['user_type'] == 'ar' || $_SESSION['usertype'] == '3' || $_SESSION['usertype'] == 'sadmin')) {
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
        'litigation_start_year' => 'VARCHAR(20)',
        'litigation_current_status' => 'TEXT',
        'approach_road_desc' => 'TEXT',
        'road_width' => 'DECIMAL(10,2) DEFAULT 0',
        'assets_loan_data' => 'TEXT',
        'other_dues_tax' => 'DECIMAL(15,2) DEFAULT 0',
        'other_dues_salary' => 'DECIMAL(15,2) DEFAULT 0',
        'other_dues_other' => 'DECIMAL(15,2) DEFAULT 0',
        'other_dues_other_status' => "ENUM('yes', 'no') DEFAULT 'no'",
        'elec_bill_status' => "ENUM('yes', 'no') DEFAULT 'no'",
        'other_dues_tax_status' => "ENUM('yes', 'no') DEFAULT 'no'",
        'other_dues_salary_status' => "ENUM('yes', 'no') DEFAULT 'no'"
    ];
    foreach ($cols as $col => $type) {
        $check_col = execute_query("SHOW COLUMNS FROM cold_storage_entries LIKE '$col'");
        if ($check_col && mysqli_num_rows($check_col) == 0) {
            @execute_query("ALTER TABLE cold_storage_entries ADD COLUMN $col $type");
        } else {
            // Ensure photo columns are TEXT for large data
            if ($col == 'building_photo' || $col == 'ownership_photo' || $col == 'employees_data' || $col == 'employees') {
                @execute_query("ALTER TABLE cold_storage_entries MODIFY COLUMN $col TEXT");
            }
        }
    }

    // Prepare Data
    $district_id = isset($_POST['district_name']) ? $_POST['district_name'] : '';
    $district_name = '';
    if ($district_id) {
        $dist_res = execute_query("SELECT district_name FROM master_district WHERE sno = '$district_id'");
        if ($dist_res && $dist_row = mysqli_fetch_assoc($dist_res))
            $district_name = $dist_row['district_name'];
    }

    $tehseel_id = isset($_POST['tehseel_name']) ? $_POST['tehseel_name'] : '';
    $tehseel_name = '';
    if ($tehseel_id) {
        $teh_res = execute_query("SELECT tehseel_name FROM master_tehseel WHERE sno = '$tehseel_id'");
        if ($teh_res && $teh_row = mysqli_fetch_assoc($teh_res))
            $tehseel_name = $teh_row['tehseel_name'];
    }

    $block_id = isset($_POST['block_name']) ? $_POST['block_name'] : '';
    $block_name = '';
    if ($block_id) {
        $blk_res = execute_query("SELECT block_name FROM master_block WHERE sno = '$block_id'");
        if ($blk_res && $blk_row = mysqli_fetch_assoc($blk_res))
            $block_name = $blk_row['block_name'];
    }

    $society_id = isset($_POST['society_name']) ? $_POST['society_name'] : '';
    $society_name = '';
    if ($society_id) {
        $soc_res = execute_query("SELECT col4 FROM test2 WHERE sno = '$society_id'");
        if ($soc_res && $soc_row = mysqli_fetch_assoc($soc_res))
            $society_name = $soc_row['col4'];
    }

    $capacity = (float) (isset($_POST['capacity']) ? $_POST['capacity'] : 0);
    $is_active = isset($_POST['is_active']) ? $_POST['is_active'] : 'active';
    $inactivity_date = ($is_active == 'inactive' && !empty($_POST['inactivity_date'])) ? $_POST['inactivity_date'] : 'NULL';
    $inactivity_reason = isset($_POST['inactivity_reason']) ? $_POST['inactivity_reason'] : '';
    $inactivity_description = isset($_POST['inactivity_description']) ? $_POST['inactivity_description'] : '';

    $land_area_total = (float) (isset($_POST['land_area_total']) ? $_POST['land_area_total'] : 0);
    $vacant_land_area = (float) (isset($_POST['vacant_land_area']) ? $_POST['vacant_land_area'] : 0);
    $vacant_land_status = isset($_POST['vacant_land_status']) ? $_POST['vacant_land_status'] : '';
    $vacant_land_location = isset($_POST['vacant_land_location']) ? $_POST['vacant_land_location'] : '';
    $rack_distance = (float) (isset($_POST['rack_distance']) ? $_POST['rack_distance'] : 0);
    $road_width = (float) (isset($_POST['road_width']) ? $_POST['road_width'] : 0);
    $is_litigation = isset($_POST['is_litigation']) ? $_POST['is_litigation'] : 'no';
    $litigation_desc = mysqli_real_escape_string($db, $_POST['litigation_desc'] ?? '');
    $court_case = mysqli_real_escape_string($db, $_POST['court_case'] ?? '');
    $litigation_start_year = mysqli_real_escape_string($db, $_POST['litigation_start_year'] ?? '');
    $litigation_current_status = mysqli_real_escape_string($db, $_POST['litigation_current_status'] ?? '');

    $building_status = isset($_POST['building_status']) ? $_POST['building_status'] : '';
    $repair_floor = isset($_POST['repair_floor']) ? 1 : 0;
    $repair_wall = isset($_POST['repair_wall']) ? 1 : 0;
    $repair_paint = isset($_POST['repair_paint']) ? 1 : 0;
    $repair_ceiling = isset($_POST['repair_ceiling']) ? 1 : 0;
    $repair_plaster = isset($_POST['repair_plaster']) ? 1 : 0;
    $repair_other = isset($_POST['repair_other']) ? 1 : 0;
    $building_desc = isset($_POST['building_desc']) ? $_POST['building_desc'] : '';

    $ownership_type = isset($_POST['ownership_type']) ? $_POST['ownership_type'] : '';
    $monthly_rent = (float) (isset($_POST['monthly_rent']) ? $_POST['monthly_rent'] : 0);
    $building_area_sqft = (float) (isset($_POST['building_area_sqft']) ? $_POST['building_area_sqft'] : 0);
    $ownership_desc = mysqli_real_escape_string($db, $_POST['ownership_desc'] ?? '');

    $approach_road_type = mysqli_real_escape_string($db, $_POST['approach_road_type'] ?? '');
    $approach_road_desc = mysqli_real_escape_string($db, $_POST['approach_road_desc'] ?? '');
    $has_basic_facilities = isset($_POST['has_basic_facilities']) ? $_POST['has_basic_facilities'] : 'no';
    $elec_conn = isset($_POST['elec_conn']) ? $_POST['elec_conn'] : 'no';
    $elec_bill = (float) (isset($_POST['elec_bill']) ? $_POST['elec_bill'] : 0);
    $solar_conn = isset($_POST['solar_conn']) ? $_POST['solar_conn'] : 'no';
    $solar_status = isset($_POST['solar_status']) ? $_POST['solar_status'] : 'non_operational';
    $internet_conn = isset($_POST['internet_conn']) ? $_POST['internet_conn'] : 'no';
    $internet_status = isset($_POST['internet_status']) ? $_POST['internet_status'] : 'non_operational';
    $water_availability = isset($_POST['water_availability']) ? $_POST['water_availability'] : 'no';
    $water_status = isset($_POST['water_status']) ? $_POST['water_status'] : 'non_operational';

    $has_loan = isset($_POST['has_loan']) ? $_POST['has_loan'] : 'no';
    $ncdc_loan = (float) (isset($_POST['ncdc_loan']) ? $_POST['ncdc_loan'] : 0);
    $bank_loan = (float) (isset($_POST['bank_loan']) ? $_POST['bank_loan'] : 0);
    $upcb_loan = (float) (isset($_POST['upcb_loan']) ? $_POST['upcb_loan'] : 0);
    $dcb_loan = (float) (isset($_POST['dcb_loan']) ? $_POST['dcb_loan'] : 0);
    $other_loan_desc = mysqli_real_escape_string($db, $_POST['other_loan_desc'] ?? '');
    $other_loan_desc = mysqli_real_escape_string($db, $_POST['other_loan_desc'] ?? '');
    $other_loan_amount = (float) (isset($_POST['other_loan_amount']) ? $_POST['other_loan_amount'] : 0);

    $other_dues_tax = (float) (isset($_POST['other_dues_tax']) ? $_POST['other_dues_tax'] : 0);
    $other_dues_salary = (float) (isset($_POST['other_dues_salary']) ? $_POST['other_dues_salary'] : 0);
    $other_dues_other = (float) (isset($_POST['other_dues_other']) ? $_POST['other_dues_other'] : 0);

    $elec_bill_status = isset($_POST['elec_bill_status']) ? $_POST['elec_bill_status'] : 'no';
    $other_dues_tax_status = isset($_POST['other_dues_tax_status']) ? $_POST['other_dues_tax_status'] : 'no';
    $other_dues_salary_status = isset($_POST['other_dues_salary_status']) ? $_POST['other_dues_salary_status'] : 'no';
    $other_dues_other_status = isset($_POST['other_dues_other_status']) ? $_POST['other_dues_other_status'] : 'no';

    $action_plan = mysqli_real_escape_string($db, $_POST['action_plan']);
    $is_approved = isset($_POST['is_approved']) ? $_POST['is_approved'] : 'pending';

    // Transform Employees to Row-Major
    $normalized_emps = [];
    if (!empty($_POST['emp']['name'])) {
        foreach ($_POST['emp']['name'] as $idx => $name) {
            if (empty($name) && empty($_POST['emp']['designation'][$idx]))
                continue;
            $normalized_emps[] = [
                'name' => $_POST['emp']['name'][$idx] ?? '',
                'designation' => $_POST['emp']['designation'][$idx] ?? '',
                'appt_date' => $_POST['emp']['appt_date'][$idx] ?? '',
                'working_status' => $_POST['emp']['working_status'][$idx] ?? '',
                'mobile' => $_POST['emp']['mobile'][$idx] ?? '',
                'email' => $_POST['emp']['email'][$idx] ?? '',
                'appt_source' => $_POST['emp']['appt_source'][$idx] ?? '',
                'appt_source_other' => $_POST['emp']['appt_source_other'][$idx] ?? '',
                'approval_level' => $_POST['emp']['approval_level'][$idx] ?? '',
                'appt_type' => $_POST['emp']['appt_type'][$idx] ?? ''
            ];
        }
    }
    $employees_data = json_encode($normalized_emps);

    // Transform Assets/Loan to Row-Major
    $normalized_assets = [];
    if (!empty($_POST['assets_loan']['loan_date'])) {
        foreach ($_POST['assets_loan']['loan_date'] as $idx => $ldate) {
            if (empty($ldate) && empty($_POST['assets_loan']['principal_amt'][$idx]))
                continue;
            $normalized_assets[] = [
                'loan_date' => $_POST['assets_loan']['loan_date'][$idx] ?? '',
                'principal_amt' => $_POST['assets_loan']['principal_amt'][$idx] ?? 0,
                'principal_pending' => $_POST['assets_loan']['principal_pending'][$idx] ?? 0,
                'interest_amt' => $_POST['assets_loan']['interest_amt'][$idx] ?? 0,
                'interest_pending' => $_POST['assets_loan']['interest_pending'][$idx] ?? 0
            ];
        }
    }
    $assets_loan_data = json_encode($normalized_assets);

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
        if (compressImage($_FILES['ownership_photo']['tmp_name'], $path, 60))
            $ownership_photo = $path;
    }

    global $db;
    $is_edit = !empty($_GET['edit_id']);
    $edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';

    if ($is_edit) {
        $sql = "UPDATE cold_storage_entries SET 
            district_name='$district_name', tehseel_name='$tehseel_name', block_name='$block_name', 
            society_name='$society_name', cs_name='$society_name', capacity='$capacity', 
            is_active='$is_active', inactivity_date=" . ($inactivity_date == 'NULL' ? "NULL" : "'$inactivity_date'") . ", 
            closure_year=" . ($inactivity_date == 'NULL' ? "NULL" : "'$inactivity_date'") . ",
            inactivity_reason='$inactivity_reason', closure_reason='$inactivity_reason', inactivity_description='$inactivity_description',
            land_area_total='$land_area_total', land_area='$land_area_total', vacant_land_area='$vacant_land_area', 
            rack_distance='$rack_distance', road_width='$road_width', is_litigation='$is_litigation', litigation_desc='$litigation_desc',
            court_case='$court_case', litigation_start_year='$litigation_start_year', litigation_current_status='$litigation_current_status',
            building_status='$building_status', building_cond='$building_status',
            building_repair_floor='$repair_floor', building_repair_wall='$repair_wall', 
            building_repair_paint='$repair_paint', building_repair_ceiling='$repair_ceiling', building_repair_plaster='$repair_plaster', 
            building_repair_other='$repair_other', 
            building_photo='" . mysqli_real_escape_string($db, $building_photo) . "', 
            building_desc='$building_desc', 
            ownership_type='$ownership_type', 
            ownership_photo='" . mysqli_real_escape_string($db, $ownership_photo) . "', 
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
            other_dues_tax='$other_dues_tax', other_dues_salary='$other_dues_salary', other_dues_other='$other_dues_other', 
            other_dues_other_status='$other_dues_other_status', elec_bill_status='$elec_bill_status', other_dues_tax_status='$other_dues_tax_status', other_dues_salary_status='$other_dues_salary_status',
            employees_data='" . mysqli_real_escape_string($db, $employees_data) . "', employees='" . mysqli_real_escape_string($db, $employees_data) . "', 
            assets_loan_data='" . mysqli_real_escape_string($db, $assets_loan_data) . "',
            action_plan='$action_plan', is_approved='$is_approved'
            WHERE id='$edit_id'";
    } else {
        $sql = "INSERT INTO cold_storage_entries (
            district_name, tehseel_name, block_name, society_name, cs_name, capacity, is_active, inactivity_date, closure_year,
            inactivity_reason, closure_reason, inactivity_description,
            land_area_total, land_area, vacant_land_area, vacant_land_status, vacant_land_location, rack_distance, road_width, is_litigation, litigation_desc,
            court_case, litigation_start_year, litigation_current_status, building_status, building_cond, building_repair_floor, building_repair_wall, building_repair_paint, building_repair_ceiling,
            building_repair_plaster, building_repair_other, building_photo, building_desc, ownership_type,
            ownership_photo, monthly_rent, building_area_sqft, ownership_desc, approach_road_type, road_access, approach_road_desc,
            has_basic_facilities, elec_conn, elec_working, elec_bill_regular, elec_not_working_reason, 
            elec_proposal, elec_months_due, elec_outstanding, elec_bill,
            solar_conn, solar_working, solar_battery_status,
            internet_conn, internet_provider, internet_bill_paid, internet_active,
            water_availability, water_status,
            has_loan, ncdc_loan, bank_loan, upcb_loan, dcb_loan, other_loan_desc, other_loan_amount, 
            other_dues_tax, other_dues_salary, other_dues_other, other_dues_other_status, elec_bill_status, other_dues_tax_status, other_dues_salary_status,
            employees_data, employees, assets_loan_data, action_plan, is_approved
        ) VALUES (
            '$district_name', '$tehseel_name', '$block_name', '$society_name', '$society_name', '$capacity', '$is_active', " . ($inactivity_date == 'NULL' ? "NULL" : "'$inactivity_date'") . ", " . ($inactivity_date == 'NULL' ? "NULL" : "'$inactivity_date'") . ",
            '$inactivity_reason', '$inactivity_reason', '$inactivity_description',
            '$land_area_total', '$land_area_total', '$vacant_land_area', '$vacant_land_status', '$vacant_land_location', '$rack_distance', '$road_width', '$is_litigation', '$litigation_desc',
            '$court_case', '$litigation_start_year', '$litigation_current_status', '$building_status', '$building_status', '$repair_floor', '$repair_wall', '$repair_paint', '$repair_ceiling',
            '$repair_plaster', '$repair_other', 
            '" . mysqli_real_escape_string($db, $building_photo) . "', 
            '$building_desc', '$ownership_type',
            '" . mysqli_real_escape_string($db, $ownership_photo) . "', 
            '$monthly_rent', '$building_area_sqft', '$ownership_desc', '$approach_road_type', '$approach_road_type', '$approach_road_desc',
            '$has_basic_facilities', '$elec_conn', '$elec_working', '$elec_bill_regular', '$elec_not_working_reason',
            '$elec_proposal', '$elec_months_due', '$elec_outstanding', '$elec_bill',
            '$solar_conn', '$solar_working', '$solar_battery_status',
            '$internet_conn', '$internet_provider', '$internet_bill_paid', '$internet_active',
            '$water_availability', '$water_status',
            '$has_loan', '$ncdc_loan', '$bank_loan', '$upcb_loan', '$dcb_loan', '$other_loan_desc', '$other_loan_amount', '" . mysqli_real_escape_string($db, $employees_data) . "', '" . mysqli_real_escape_string($db, $employees_data) . "', '" . mysqli_real_escape_string($db, $assets_loan_data) . "', '$action_plan', '$is_approved'
        )";
    }

    $res = execute_query($sql);
    if ($res) {
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

if ($is_edit) {
    $sql = "SELECT * FROM cold_storage_entries WHERE id = '$edit_id'";
    $res = execute_query($sql);
    if ($row = mysqli_fetch_assoc($res)) {
        $edit_data = $row;
        // Extract reason for संचालित
        if (strpos($edit_data['closure_reason'], 'संचालित - ') === 0) {
            $edit_data['asanchalit_reason'] = substr($edit_data['closure_reason'], strlen('संचालित - '));
            $edit_data['closure_reason'] = 'संचालित';
        }
        // Extract for land_area विवादित or अन्य
        if (strpos($edit_data['land_area'], 'विवादित - ') === 0 || strpos($edit_data['land_area'], 'अन्य - ') === 0) {
            $prefix = strpos($edit_data['land_area'], 'विवादित - ') === 0 ? 'विवादित - ' : 'अन्य - ';
            $edit_data['land_ownership_desc'] = substr($edit_data['land_area'], strlen($prefix));
            $edit_data['land_area'] = str_replace(' - ' . $edit_data['land_ownership_desc'], '', $edit_data['land_area']);
        }
        // Extract for other_liabilities
        if (isset($edit_data['other_liabilities'])) {
            if (strpos($edit_data['other_liabilities'], 'नहीं - ') === 0) {
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
function val($key)
{
    global $edit_data;
    return isset($edit_data[$key]) ? $edit_data[$key] : '';
}

// Helper Class to prevent "Undefined Array Key" warnings
class SafeMockArray implements ArrayAccess
{
    private $container = [];
    public function __construct(array $initial = [])
    {
        $this->container = $initial;
    }
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : '';
    }
}

// Initialize undefined variables used in the form
$tab = 1;
$row_invoice = new SafeMockArray();
$row_3_1 = new SafeMockArray();
$row_new_plot = new SafeMockArray();
$sides_data = [];
$row_2_1 = new SafeMockArray();
$row_3_5_1 = new SafeMockArray();
$row_new_scheme = new SafeMockArray();
$row_3_4 = new SafeMockArray(['count' => 1]);
$row_7_6 = new SafeMockArray(['count' => 1]);
$row_3_5 = new SafeMockArray(['sec_3_c_id' => 1]);
$row_2_6 = new SafeMockArray(['count' => 1]);
$row_5 = new SafeMockArray();


page_header_start("Cold Storage Management");
?>

<style>
    /* Blue Theme - Based on Block Union Reference */
    :root {
        --primary-blue: #2980b9;
        /* Strong Blue */
        --light-blue: #3498db;
        /* Lighter Blue */
        --header-bg: #ecf0f1;
        /* Very light grey for backgrounds */
        --border-color: #bdc3c7;
        --input-active: #3498db;
        --text-dark: #2c3e50;

    }

    /* Force White/Light Background - Remove Orange/Peach Tint */
    body,
    .wrapper,
    .main-panel,
    .content,
    .container-fluid {
        background-color: #f4f7f6 !important;
        background: #f4f7f6 !important;
    }

    .card {
        background-color: #ffffff !important;
        border: 1px solid #e1e4e8;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        overflow: hidden;
    }

    .card-body {
        padding: 20px !important;
        /* Fixed padding */
    }

    .card-header {
        background-color: #fff !important;
        border-bottom: 1px solid #eee;
        padding: 15px 25px;
    }

    .card-title {
        color: var(--primary-blue);
        font-weight: 700;
        font-size: 22px;
        margin: 0;
        text-align: center;
        /* Center title like reference */
        text-transform: uppercase;
    }

    /* Section Headers (Blue Bars) */
    .section-title {
        background-color: var(--light-blue);
        color: #fff;
        padding: 12px 20px;
        font-size: 20px;
        font-weight: 600;
        border-radius: 4px;
        margin: 30px 0 25px 0;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h5 {
        margin-top: 25px;
        margin-bottom: 25px;
        font-weight: 600;
        color: #2c3e50;
    }

    .card label {
        font-size: 18px;
        /* margin-top: 25px; */
        /* margin-bottom: 25px; */
        font-weight: 600;
        color: #2c3e50;
    }

    /* Form Fields */
    .form-group {
        margin-bottom: 25px;
        /* Increased spacing between fields */
    }

    /* 
    .section-title::before {
        content: '\f0f6'; /* File icon */
    font-family: 'FontAwesome';
    margin-right: 10px;
    }

    */

    /* Form Fields */
    .form-group label {
        font-weight: 600;
        color: #555;
        margin-bottom: 8px;
        display: block;
        font-size: 15px;
        /* Increased from 14px */
    }

    .form-control {
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 10px 12px;
        height: 45px;
        /* Slightly taller */
        font-size: 15px;
        /* Increased from 14px */
        color: #333;
        background-color: #fff;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        border-color: var(--input-active);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    /* Tables */
    .table thead th,
    .table-bordered thead th {
        background-color: #3498db !important;
        /* Blue background */
        color: #fff !important;
        /* White text */
        font-weight: 600;
        border-bottom: 2px solid #2980b9 !important;
        text-align: center;
        vertical-align: middle;
        font-size: 17px;
        /* Increased */
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table thead th:first-child {
        border-radius: 8px 0 0 0;
    }

    .table thead th:last-child {
        border-radius: 0 8px 0 0;
    }

    .table-bordered td {
        vertical-align: middle;
        background-color: #fff;
        /* Ensure cells are white */
        font-size: 16px;
        /* Increased font size */
    }

    /* Striped rows */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, .02) !important;
    }

    /* Column Numbering Row specific style */
    .col-number td {
        background-color: #ecf0f1 !important;
        color: #2c3e50 !important;
        font-weight: 600;
    }

    /* Buttons */
    .btn {
        border-radius: 4px;
        padding: 10px 20px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background-color: #4a90e2;
        color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-primary:hover {
        background-color: #357ab8;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .btn-secondary {
        background-color: #95a5a6;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #7f8c8d;
        color: white;
    }

    .btn-info {
        background-color: #0ea5e9;
        color: white;
    }

    .btn-info:hover {
        background-color: #0284c7;
        color: white;
    }

    .btn-success {
        background-color: #10b981;
        color: white;
        border: none;
    }

    .btn-success:hover {
        background-color: #059669;
        color: white;
    }

    .btn-danger {
        background-color: #b00020;
        color: white;
    }

    .btn-danger:hover {
        background-color: #be123c;
        /* Slightly lighter/different shade for hover effect */
        color: white;
    }

    /* Submit Area */
    .form-actions {
        text-align: center;
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    /* Specific overrides for layout consistency */
    .row {
        margin-left: -10px;
        margin-right: -10px;
    }

    .col-md-3,
    .col-md-4,
    .col-md-6,
    .col-md-12 {
        padding-left: 10px;
        padding-right: 10px;
    }

    /* Input Group for Lat/Long */
    .input-group-append .btn {
        padding: 10px 15px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
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
        <div id="historyCard" class="card"
            style="<?php echo (isset($show_success) && $show_success) ? 'display:none;' : 'display:block;'; ?>">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title text-center" style="font-weight: bold;">शीतगृहों की सूचना </h4>

                    </div>
                    <div class="col-md-4 text-right">
                        <a href="cold_storage_export.php" class="btn"
                            style="background-color: #10b981; border-color: #10b981; color: white; background-image: none !important; font-size: 14px; padding: 8px 10px; width: 180px; margin-bottom: 5px;">
                            <i class="fa fa-file-excel-o"></i> Export to Excel
                        </a>
                        <?php if (isset($_SESSION['username']) && ($_SESSION['user_type'] == 'ar' || $_SESSION['usertype'] == '3' || $_SESSION['usertype'] == 'sadmin')) { ?>
                            <button id="addNewBtn" class="btn"
                                style="background-color: #007bff; border-color: #007bff; color: white; background-image: none !important; font-size: 14px; padding: 8px 10px; width: 180px;">
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
                                <label class="mb-0" style="font-size: 15px; font-weight: bold; color: #333;">मंडल
                                    (Mandal)</label>
                                <select name="f_division" id="f_division" class="form-control"
                                    onchange="filter_fill_district(this.value);"
                                    style="height: 40px !important; font-size: 15px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php
                                    $sql_div = 'select * from master_division';
                                    $res_div = execute_query($sql_div);
                                    while ($row_div = mysqli_fetch_assoc($res_div)) {
                                        $selected = (isset($_GET['f_division']) && $_GET['f_division'] == $row_div['sno']) ? 'selected' : '';
                                        echo '<option value="' . $row_div['sno'] . '" ' . $selected . '>' . $row_div['division_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size: 15px; font-weight: bold; color: #333;">जनपद
                                    (District)</label>
                                <select name="f_district" id="f_district" class="form-control"
                                    onchange="filter_fill_tehseel(this.value);"
                                    style="height: 40px  !important; font-size: 15px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php
                                    if (isset($_GET['f_division']) && $_GET['f_division'] != '') {
                                        $dist_sql = "select * from master_district where division_id = '" . $_GET['f_division'] . "'";
                                        $dist_res = execute_query($dist_sql);
                                        while ($d_row = mysqli_fetch_assoc($dist_res)) {
                                            $selected = (isset($_GET['f_district']) && $_GET['f_district'] == $d_row['sno']) ? 'selected' : '';
                                            echo '<option value="' . $d_row['sno'] . '" ' . $selected . '>' . $d_row['district_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size: 15px; font-weight: bold; color: #333;">तहसील
                                    (Tehsil)</label>
                                <select name="f_tehseel" id="f_tehseel" class="form-control"
                                    onchange="filter_fill_block(this.value);"
                                    style="height: 40px !important; font-size: 15px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php
                                    if (isset($_GET['f_district']) && $_GET['f_district'] != '') {
                                        $teh_sql = "select * from master_tehseel where district_id = '" . $_GET['f_district'] . "'";
                                        $teh_res = execute_query($teh_sql);
                                        while ($t_row = mysqli_fetch_assoc($teh_res)) {
                                            $selected = (isset($_GET['f_tehseel']) && $_GET['f_tehseel'] == $t_row['sno']) ? 'selected' : '';
                                            echo '<option value="' . $t_row['sno'] . '" ' . $selected . '>' . $t_row['tehseel_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size: 15px; font-weight: bold; color: #333;">विकासखंड
                                    (Block)</label>
                                <select name="f_block" id="f_block" class="form-control"
                                    style="height: 40px !important; font-size: 15px !important; max-width: 100% !important;">
                                    <option value="">All</option>
                                    <?php
                                    if (isset($_GET['f_tehseel']) && $_GET['f_tehseel'] != '') {
                                        $blk_sql = "select * from master_block where tehseel_id = '" . $_GET['f_tehseel'] . "'";
                                        $blk_res = execute_query($blk_sql);
                                        while ($b_row = mysqli_fetch_assoc($blk_res)) {
                                            $selected = (isset($_GET['f_block']) && $_GET['f_block'] == $b_row['sno']) ? 'selected' : '';
                                            echo '<option value="' . $b_row['sno'] . '" ' . $selected . '>' . $b_row['block_name'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size: 15px; font-weight: bold; color: #333;">शीतगृह की
                                    स्थिति</label>
                                <select name="f_building_status" class="form-control"
                                    style="height: 40px !important; font-size: 15px !important;">
                                    <option value="">All</option>
                                    <option value="good" <?php echo (isset($_GET['f_building_status']) && $_GET['f_building_status'] == 'good') ? 'selected' : ''; ?>>संचालित</option>
                                    <option value="repairable" <?php echo (isset($_GET['f_building_status']) && $_GET['f_building_status'] == 'repairable') ? 'selected' : ''; ?>>असंचालित
                                    </option>
                                    <option value="bad" <?php echo (isset($_GET['f_building_status']) && $_GET['f_building_status'] == 'bad') ? 'selected' : ''; ?>>अन्य</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size: 15px; font-weight: bold; color: #333;">क्षमता
                                </label>
                                <div class="input-group">
                                    <select name="f_cap_op" class="form-control p-1"
                                        style="max-width: 45px !important; height: 40px !important; font-size: 15px !important;">
                                        <option value="=" <?php echo (isset($_GET['f_cap_op']) && $_GET['f_cap_op'] == '=') ? 'selected' : ''; ?>>=</option>
                                        <option value=">" <?php echo (isset($_GET['f_cap_op']) && $_GET['f_cap_op'] == '>') ? 'selected' : ''; ?>>></option>
                                        <option value="<" <?php echo (isset($_GET['f_cap_op']) && $_GET['f_cap_op'] == '<') ? 'selected' : ''; ?>>
                                            << /option>
                                    </select>
                                    <input type="number" step="0.01" name="f_capacity" class="form-control"
                                        value="<?php echo $_GET['f_capacity'] ?? ''; ?>"
                                        style="height: 40px !important; font-size: 15px !important;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="mb-0" style="font-size: 15px; font-weight: bold; color: #333;">भूमि
                                    क्षेत्रफल</label>
                                <div class="input-group">
                                    <select name="f_land_op" class="form-control p-1"
                                        style="max-width: 45px !important; height: 40px !important; font-size: 15px !important;">
                                        <option value="=" <?php echo (isset($_GET['f_land_op']) && $_GET['f_land_op'] == '=') ? 'selected' : ''; ?>>=</option>
                                        <option value=">" <?php echo (isset($_GET['f_land_op']) && $_GET['f_land_op'] == '>') ? 'selected' : ''; ?>>></option>
                                        <option value="<" <?php echo (isset($_GET['f_land_op']) && $_GET['f_land_op'] == '<') ? 'selected' : ''; ?>>
                                            << /option>
                                    </select>
                                    <input type="number" step="0.0001" name="f_land_area" class="form-control"
                                        value="<?php echo $_GET['f_land_area'] ?? ''; ?>"
                                        style="height: 40px !important; font-size: 15px !important;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end mb-2" style="gap: 5px;">
                            <button type="submit" class="btn btn-primary btn-sm btn-fill"><i class="fa fa-search"></i>
                                Search</button>
                            <a href="cold_storage.php" class="btn btn-secondary btn-sm btn-fill"><i
                                    class="fa fa-refresh"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div style="overflow-x: auto; width: 100%;">
                    <table class="table table-striped">
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
                                <th style="min-width: 150px;">एक्शन</th>
                                <th></th>
                            </tr>
                        </thead>


                        <tbody>
                            <?php
                            $check_table = execute_query("SHOW TABLES LIKE 'cold_storage_entries'");
                            if (mysqli_num_rows($check_table) > 0) {
                                // Filter Logic
                                $where = [];

                                // Location Filtering
                                if (!empty($_GET['f_block'])) {
                                    $blk_res = execute_query("select block_name from master_block where sno='" . $_GET['f_block'] . "'");
                                    $blk_row = mysqli_fetch_assoc($blk_res);
                                    if ($blk_row)
                                        $where[] = "block_name = '" . $blk_row['block_name'] . "'";
                                } elseif (!empty($_GET['f_tehseel'])) {
                                    $teh_res = execute_query("select tehseel_name from master_tehseel where sno='" . $_GET['f_tehseel'] . "'");
                                    $teh_row = mysqli_fetch_assoc($teh_res);
                                    if ($teh_row)
                                        $where[] = "tehseel_name = '" . $teh_row['tehseel_name'] . "'";
                                } elseif (!empty($_GET['f_district'])) {
                                    $dist_res = execute_query("select district_name from master_district where sno='" . $_GET['f_district'] . "'");
                                    $dist_row = mysqli_fetch_assoc($dist_res);
                                    if ($dist_row)
                                        $where[] = "district_name = '" . $dist_row['district_name'] . "'";
                                } elseif (!empty($_GET['f_division'])) {
                                    $dist_res = execute_query("select district_name from master_district where division_id='" . $_GET['f_division'] . "'");
                                    $dist_names = [];
                                    while ($row = mysqli_fetch_assoc($dist_res)) {
                                        $dist_names[] = "'" . $row['district_name'] . "'";
                                    }
                                    if (!empty($dist_names)) {
                                        $where[] = "district_name IN (" . implode(',', $dist_names) . ")";
                                    } else {
                                        $where[] = "1=0"; // No districts found
                                    }
                                }



                                // Capacity
                                if (!empty($_GET['f_capacity'])) {
                                    $op = $_GET['f_cap_op'] ?? '=';
                                    if (in_array($op, ['<', '>', '='])) {
                                        $where[] = "capacity $op '" . mysqli_real_escape_string($db, $_GET['f_capacity']) . "'";
                                    }
                                }



                                // Building Status
                                if (!empty($_GET['f_building_status'])) {
                                    $where[] = "building_status = '" . mysqli_real_escape_string($db, $_GET['f_building_status']) . "'";
                                }

                                // Land Area
                                if (!empty($_GET['f_land_area'])) {
                                    $op = $_GET['f_land_op'] ?? '=';
                                    if (in_array($op, ['<', '>', '='])) {
                                        $where[] = "land_area_total $op '" . mysqli_real_escape_string($db, $_GET['f_land_area']) . "'";
                                    }
                                }

                                $where_sql = count($where) ? "WHERE " . implode(' AND ', $where) : "";
                                $entries = execute_query("SELECT * FROM cold_storage_entries $where_sql ORDER BY created_at DESC");
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($entries)) {
                                    $emp_data = json_decode(isset($row['employees_data']) ? $row['employees_data'] : '[]', true) ?: [];
                                    $emp_count = count($emp_data);
                                    $total_loan = (float) (isset($row['ncdc_loan']) ? $row['ncdc_loan'] : 0) + (float) (isset($row['bank_loan']) ? $row['bank_loan'] : 0) + (float) (isset($row['upcb_loan']) ? $row['upcb_loan'] : 0) + (float) (isset($row['dcb_loan']) ? $row['dcb_loan'] : 0);

                                    echo "<tr>";
                                    echo "<td class='text-center' style='vertical-align: middle;'>{$i}</td>";
                                    echo "<td>
                                            <div style='font-weight:700; color:#2c3e50;'>{$row['district_name']}</div>
                                            <small class='text-muted'>{$row['tehseel_name']} / {$row['block_name']}</small>
                                          </td>";
                                    echo "<td><span class='truncate-text' title='" . (isset($row['society_name']) ? $row['society_name'] : '') . "'>" . (isset($row['society_name']) ? $row['society_name'] : '') . "</span></td>";
                                    echo "<td><strong>{$row['capacity']}</strong> <small>मै.टन</small></td>";
                                    echo "<td>";
                                    if (isset($row['is_active']) && $row['is_active'] == 'active') {
                                        echo "<span class='badge badge-success' style='background:#4caf50;'>सक्रिय (Active)</span>";
                                    } elseif (isset($row['is_active']) && $row['is_active'] == 'inactive') {
                                        echo "<span class='badge badge-danger' style='background:#f44336;'>असंचालित</span><br>";
                                        echo "<small class='text-danger' style='font-weight:600;'>" . (isset($row['inactivity_reason']) ? $row['inactivity_reason'] : '') . "</small><br>";
                                        echo "<small class='text-muted'>" . (isset($row['inactivity_description']) ? $row['inactivity_description'] : '') . "</small>";
                                    } else {
                                        echo "<span class='badge badge-warning'>अन्य</span>";
                                    }
                                    echo "</td>";
                                    echo "<td>" . (isset($row['land_area_total']) ? $row['land_area_total'] : '0') . " हे.</td>";
                                    echo "<td>" . strtoupper(isset($row['building_status']) ? $row['building_status'] : 'N/A') . "</td>";
                                    echo "<td>" . strtoupper(isset($row['ownership_type']) ? $row['ownership_type'] : 'N/A') . "</td>";
                                    echo "<td>₹" . number_format(isset($row['elec_bill']) ? $row['elec_bill'] : 0, 2) . "</td>";
                                    echo "<td>₹" . number_format($total_loan, 2) . "</td>";
                                    echo "<td class='text-center' style='vertical-align: middle;'><span class='badge badge-info' style='background:#2196f3;'>$emp_count</span></td>";
                                    echo "<td style='white-space:nowrap; vertical-align: middle;'>
                                            <div class='btn-group' style='display:flex; gap:5px; align-items: center;'>
                                                <a href='?edit_id={$row['id']}' class='btn' style='background:#007bff; color:#fff; padding:4px 8px; border-radius:4px; font-size:11px;' title='Edit'><i class='fa fa-edit'></i> Edit</a>
                                                <button type='button' class='btn' style='background:#10b981; color:#fff; padding:4px 8px; border-radius:4px; font-size:11px;' onclick='viewDetails(" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . ")' title='View'><i class='fa fa-eye'></i> View</button>
                                                <a href='?delete_id={$row['id']}' class='btn' style='background:#b00020; color:#fff; padding:4px 8px; border-radius:4px; font-size:11px;' onclick='return confirm(\"Are you sure?\")' title='Delete'><i class='fa fa-trash'></i> Delete</a>
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
        <?php if (isset($_SESSION['username']) && ($_SESSION['user_type'] == 'ar' || $_SESSION['usertype'] == '3' || $_SESSION['usertype'] == 'sadmin')) { ?>
            <div id="formCard" class="card" style="display:none;">
                <div class="card-body">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <h4 class="card-title card-title-center" style="font-weight: bold;">
                                    शीतगृह विवरण (Cold Storage Details)
                                </h4>
                            </div>
                        </div>
                    </div>
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($is_edit): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="old_building_photo" value="<?php echo val('building_photo'); ?>">
                        <input type="hidden" name="old_ownership_photo" value="<?php echo val('ownership_photo'); ?>">

                        <h5 class="section-title"><i class="fa fa-file-text-o"></i> 1. सामान्य जानकारी (Basic Info)</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label style="text-transform: uppercase;">Latitude / Longitude</label>
                                    <div class="mb-2">
                                        <input type="text" name="lat" class="form-control" placeholder="Latitude"
                                            value="<?php echo isset($row_invoice['latitude']) ? $row_invoice['latitude'] : ''; ?>">
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="long" class="form-control" placeholder="Longitude"
                                            value="<?php echo isset($row_invoice['longitude']) ? $row_invoice['longitude'] : ''; ?>">
                                    </div>
                                    <div>
                                        <button class="btn btn-success" type="button" style="width: 100%;"
                                            onClick="getLocation();">लोकेशन रिफ्रेश करे</button>
                                    </div>
                                    <!-- <div class="blinking-text mt-2">(लोकेशन मोबाईल से भरे)*</div> -->
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">

                                    <iframe id="googlemap"
                                        src="https://maps.google.com/maps?q=<?php echo (isset($row_invoice['latitude']) ? $row_invoice['latitude'] : '') . ',' . (isset($row_invoice['longitude']) ? $row_invoice['longitude'] : ''); ?>&hl=en&z=13&amp;output=embed"
                                        width="100%" height="100%" style="border:1px solid; border-radius:10px;"
                                        allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>मंडल (Mandal)*</label>
                                    <select name="division_name" id="division_name" class="form-control" required
                                        onChange="fill_district(this.value);">
                                        <option value="">-- मंडल चुनें --</option>
                                        <?php
                                        $res_div = execute_query('select * from master_division');
                                        while ($row_div = mysqli_fetch_assoc($res_div)) {
                                            echo '<option value="' . $row_div['sno'] . '">' . $row_div['division_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>जनपद (District) *</label>
                                    <select name="district_name" id="district_name" class="form-control" required
                                        onChange="fill_tehseel(this.value);">
                                        <option value="">-- जनपद चुनें --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>तहसील (Tehsil)</label>
                                    <select name="tehseel_name" id="tehseel_name" class="form-control"
                                        onChange="fill_block(this.value);">
                                        <option value="">-- तहसील चुनें --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>विकासखंड (Block)</label>
                                    <select name="block_name" id="block_name" class="form-control"
                                        onChange="fill_society(this.value);">
                                        <option value="">-- विकासखंड चुनें --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>शीतगृह</label>
                                    <select name="society_name" id="society_name" class="form-control">
                                        <option value="">-- शीतगृह चुनें --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>भण्डारण क्षमता (मै.टन)</label>
                                    <input type="number" step="0.01" name="capacity" class="form-control"
                                        value="<?php echo val('capacity'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label> भवन की फोटो (अधिकतम 4 फोटो)</label><br>
                                <small class="text-muted">आप एक साथ 4 फोटो तक चुन सकते हैं।</small>
                                <input type="file" name="building_photo[]" class="form-control" multiple
                                    onchange="checkFileLimit(this, 4); previewImages(this, 'building_preview')">
                                <div id="building_preview" class="mt-2 d-flex flex-wrap" style="gap: 5px;"></div>
                                <?php
                                $b_photo = val('building_photo');
                                $photos = json_decode($b_photo, true);
                                if (!$photos && !empty($b_photo))
                                    $photos = [$b_photo];
                                if ($photos): ?>
                                    <div class="mt-2 d-flex flex-wrap" style="gap: 5px;">
                                        <small class="w-100 text-muted">मौजूदा फोटो:</small>
                                        <?php foreach ($photos as $p): ?>
                                            <img src="<?php echo $p; ?>"
                                                style="height: 60px; border-radius: 4px; border: 1px solid #ddd;">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-12" id="inactivity_div"
                            style="<?php echo (val('is_active') == 'inactive') ? 'display:block;' : 'display:none;'; ?>">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>असंचालित होने का कारण</label>
                                        <select name="inactivity_reason" class="form-control">
                                            <option value="">-- चयन करें --</option>
                                            <option value="निष्प्रयोग" <?php if (val('inactivity_reason') == 'निष्प्रयोग')
                                                echo 'selected'; ?>>निष्प्रयोज्य</option>
                                            <option value="मरम्मत योग्य" <?php if (val('inactivity_reason') == 'मरम्मत योग्य')
                                                echo 'selected'; ?>>मरम्मत योग्य</option>
                                            <option value="विवादित" <?php if (val('inactivity_reason') == 'विवादित')
                                                echo 'selected'; ?>>विवादित</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>असंचालित होने की वर्ष</label>
                                        <input type="text" name="inactivity_date" class="form-control"
                                            value="<?php echo val('inactivity_date'); ?>" placeholder="वर्ष दर्ज करें">

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>विवरण</label>
                                        <textarea name="inactivity_description" class="form-control"
                                            rows="2"><?php echo val('inactivity_description'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!---------------------------- 2. समिति भवन/सम्पत्ति का विवरण --------------------------------->
                    <h5 class="section-title"><i class="fa fa-file-text-o"></i>2. शीतगृह का विवरण</h5>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                <h5>(I) शीतगृह का स्वामित्व </h5>
                                <div class="col-sm-3 form-group">
                                    <!-- <label>शीतगृह के भवन का स्वामित्व </label> -->
                                        <select name="sec_3_ownership" id="sec_3_ownership" tabindex="<?php echo $tab++; ?>"
                                            class="form-control"
                                            onChange="hide_show(this.value, '#sec_3_rented', 'rent'); hide_show(this.value, '#sec_3_other', 'other'); hide_show(this.value, '#sec_3_image', ['own', 'rent', 'other']);">
                                            <option value="">--Select--</option>
                                            <option value="own" <?php $sec_3_rented_display = 'none';
                                            $sec_3_other_display = 'none';
                                            $sec_3_display = 'flex';
                                            if ($row_3_1['sec_3_ownership'] == 'own') {
                                                echo ' selected="selected" ';
                                                $sec_3_display = 'flex';
                                            } ?>> स्वामित्व में है</option>
                                            <option value="rent" <?php if ($row_3_1['sec_3_ownership'] == 'rent') {
                                                echo ' selected="selected" ';
                                                $sec_3_rented_display = 'flex';
                                            } ?>>किराये पर है
                                            </option>
                                            <option value="other" <?php if ($row_3_1['sec_3_ownership'] != 'rent' && $row_3_1['sec_3_ownership'] != 'own' && $row_3_1['sec_3_ownership'] != '') {
                                                echo ' selected="selected" ';
                                                $sec_3_other_display = 'flex';
                                            } ?>>अन्य स्थिति
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group" id="sec_3_image" style="display:none;">
                                        <label> भवन का फोटो संलग्न करें</label>
                                        <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                            name="sec_3_ownership_image" id="sec_3_ownership_image"
                                            tabindex="<?php echo $tab++; ?>" class="form-control">
                                    </div>
                                    <?php
                                    if (!empty($row_3_1['sec_3_ownership_image']) && file_exists($row_3_1['sec_3_ownership_image'])) {
                                        ?>
                                        <div class="col-sm-3 form-group">
                                            <img src="<?php echo $row_3_1['sec_3_ownership_image']; ?>"
                                                class="img-fluid img-thumbnail" style="height:50px;"
                                                id="sec_3_a_image_uploaded">
                                            <label><a href="<?php echo $row_3_1['sec_3_ownership_image']; ?>"
                                                    target="_blank">संलग्न
                                                    फोटो देखें</a></label>

                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div id="sec_3_rented" style="display: <?php echo $sec_3_rented_display; ?>">
                                        <div class="col-sm-3 form-group">
                                            <label> भवन का मासिक किराया</label>
                                            <input name="sec_3_building_rent" id="sec_3_building_rent"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo $row_invoice['society_building_rent_amount']; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>शीतगृह का क्षेत्रफल (स्क्वायर फिट में)</label>
                                            <input name="sec_3_building_area" id="sec_3_building_area"
                                                tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                                data-type="2.I समिति भवन का क्षेत्रफल (स्क्वायर फिट में भरे)"
                                                value="<?php echo $row_invoice['society_building_area']; ?>">
                                        </div>

                                    </div>
                                    <div id="sec_3_other" style="display: <?php echo $sec_3_other_display; ?>">
                                        <div class="col-sm-3 form-group">
                                            <label>कृपया विवरण दर्ज करें</label>
                                            <input name="sec_3_building_rent1" id="sec_3_building_rent1"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo $row_invoice['society_building_rent_amount']; ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>क्या शीतगृह विवाद मे हैं (Litigation)</label>
                                            <select name="is_litigation" id="is_litigation" tabindex="<?php echo $tab++; ?>"
                                                class="form-control"
                                                onchange="hide_show(this.value, '#litigation_desc_div', 'yes'); hide_show(this.value, '#court_case_div', 'yes'); hide_show(this.value, '#litigation_start_year_div', 'yes'); hide_show(this.value, '#litigation_current_status_div', 'yes'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="yes" <?php echo (val('is_litigation') == 'yes') ? ' selected="selected"' : ''; ?> style="background:#0f0">हाँ</option>
                                                <option value="no" <?php echo (val('is_litigation') == 'no') ? ' selected="selected"' : ''; ?> style="background:#f00">नहीं</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-3 form-group" id="court_case_div"
                                            style="display: <?php echo (val('is_litigation') == 'yes') ? 'block' : 'none'; ?>">
                                            <label>किस न्यायालय में विचाराधीन है?</label>
                                            <select name="court_case" id="court_case" class="form-control"
                                                tabindex="<?php echo $tab++; ?>">
                                                <option value="">-- चुनें --</option>
                                                <option value="Revenue Court" <?php echo (val('court_case') == 'Revenue Court') ? ' selected="selected"' : ''; ?>>राजस्व न्यायालय (Revenue
                                                    Court)</option>
                                                <option value="Civil Court" <?php echo (val('court_case') == 'Civil Court') ? ' selected="selected"' : ''; ?>>सिविल न्यायालय (Civil Court)</option>
                                                <option value="High Court" <?php echo (val('court_case') == 'High Court') ? ' selected="selected"' : ''; ?>>हाई कोर्ट (High Court)</option>
                                                <option value="Supreme Court" <?php echo (val('court_case') == 'Supreme Court') ? ' selected="selected"' : ''; ?>>उच्चतम न्यायालय (Supreme
                                                    Court)</option>
                                                <option value="Other" <?php echo (val('court_case') == 'Other') ? ' selected="selected"' : ''; ?>>अन्य </option>
                                            </select>
                                        </div>

                                        <div class="col-sm-3 form-group" id="litigation_start_year_div"
                                            style="display: <?php echo (val('is_litigation') == 'yes') ? 'block' : 'none'; ?>">
                                            <label>विवाद कब से चल रहा है?</label>
                                            <select name="litigation_start_year" id="litigation_start_year"
                                                class="form-control" tabindex="<?php echo $tab++; ?>">
                                                <option value="">-- चुनें --</option>
                                                <?php
                                                $currentYear = date('Y');
                                                for ($y = 1900; $y <= $currentYear; $y++) {
                                                    $selected = (val('litigation_start_year') == $y) ? 'selected="selected"' : '';
                                                    echo "<option value='$y' $selected>$y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-3 form-group" id="litigation_current_status_div"
                                            style="display: <?php echo (val('is_litigation') == 'yes') ? 'block' : 'none'; ?>">
                                            <label>विवाद की वर्तमान स्थिति</label>
                                            <input type="text" name="litigation_current_status"
                                                id="litigation_current_status" class="form-control"
                                                tabindex="<?php echo $tab++; ?>"
                                                value="<?php echo val('litigation_current_status'); ?>">
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row" id="sec_3" style="display: <?php echo $sec_3_display; ?>;">
                                <div class="col-sm-12">
                                    <h5> (II) शीतगृह के भूखंड का विवरण </h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>क्षेत्रफल (हेक्टेयर में)</label>
                                            <label><small>&nbsp;</small></label>
                                            <input type="text" name="sec_new_plot_area" id="sec_new_plot_area"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                data-type="2.II. क्षेत्रफल (को हेक्टेयर में भरे)"
                                                value="<?php echo $row_new_plot['sec_new_plot_area']; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>भूमि का मूल्य (लाख रुपये मे)</label>
                                            <label><small>वर्तमान सर्किल रेट के अनुसार</small></label>
                                            <input type="text" name="sec_new_plot_area" id="sec_new_plot_area"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                data-type="2.II. क्षेत्रफल (को हेक्टेयर में भरे)"
                                                value="<?php echo $row_new_plot['sec_new_plot_area']; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>राजस्व अभिलेख में दर्ज होने की
                                                स्थिति(हाँ/नहीं)</label>
                                            <label><small>&nbsp;</small></label>
                                            <select name="sec_new_plot_revenue_status" id="sec_new_plot_revenue_status"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                onChange="hide_show(this.value, '#sec_new_plot_reason', 'no'); hide_show(this.value, '#sec_new_plot_if_not', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="yes" <?php echo ($row_new_plot['sec_new_plot_revenue_status'] == 'yes') ? ' selected="selected"' : ''; ?> style="background:#0f0">हाँ</option>
                                                <option value="no" <?php echo ($row_new_plot['sec_new_plot_revenue_status'] == 'no') ? ' selected="selected"' : ''; ?> style="background:#f00">नहीं</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group" id="sec_new_plot_reason" style="display:none">
                                            <label>दर्ज ना होने का कारण?</label>
                                            <label><small>&nbsp;</small></label>
                                            <input type="text" name="sec_new_plot_reason_for_not_record"
                                                id="sec_new_plot_reason_for_not_record" tabindex="<?php echo $tab++; ?>"
                                                class="form-control"
                                                value="<?php echo $row_new_plot['sec_new_plot_reason_for_not_record']; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group" id="sec_new_plot_if_not" style="display:none">
                                            <label>यदि नहीं है तो किये जाने वाले प्रयास का विवरण</label>
                                            <label><small>&nbsp;</small></label>
                                            <input type="text" name="sec_new_plot_practices_if_not"
                                                id="sec_new_plot_practices_if_not" tabindex="<?php echo $tab++; ?>"
                                                class="form-control"
                                                value="<?php echo $row_new_plot['sec_new_plot_practices_if_not']; ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>गाटा/खसरा संख्या (खतौनी संलग्न करें)</label>
                                            <input type="text" name="sec_new_plot_gata_no" id="sec_new_plot_gata_no"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo $row_new_plot['sec_new_plot_gata_no']; ?>">
                                        </div>

                                        <div class="col-sm-3 form-group">
                                            <label>टिप्पणी (अधिकतम 100 शब्दो मे)</label>
                                            <input type="text" name="sec_new_remarks" id="sec_new_remarks"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo $row_new_plot['sec_new_remarks']; ?>">
                                        </div>

                                    </div>
                                    <!-- <?php
                                    if (empty($sides_data)) {
                                        ?>
                                                            <div id="sides_display" style="display: none;" class="row">

                                                            </div>
                                                        <?php
                                    } else {
                                        $col = ceil(12 / sizeof($sides_data));
                                        ?>
                                                            <div id="sides_display" class="row">
                                                                <?php
                                                                $i = 1;
                                                                foreach ($sides_data as $k => $v) {
                                                                    ?>
                                                                    <div class="col-sm-<?php echo $col; ?> form-group">
                                                                        <label>भुजा
                                                                            <?php echo $i; ?> की लम्बाई (मी० मे)
                                                                        </label>
                                                                        <input type="text" name="sec_3_a_side_<?php echo $i; ?>"
                                                                            id="sec_3_a_<?php echo $i; ?>"
                                                                            class="form-control chk_decimal"
                                                                            data-type="भुजा <?php echo $i; ?> की लम्बाई"
                                                                            value="<?php echo $v; ?>">
                                                                    </div>

                                                                <?php
                                                                $i++;
                                                                }
                                                                ?>
                                                            </div>
                                                        <?php
                                    }
                                    ?> -->

                                    <h5>(III) पहुंच मार्ग का विवरण</h2>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>पहुंच मार्ग</label>
                                                <select name="sec_6_access_road" id="sec_6_access_road"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onChange="hide_show(this.value, '#access_road', 'proper'); hide_show(this.value, '#access_road_truck', 'ordinary');">
                                                    <option value="">--Select--</option>
                                                    <option value="proper" <?php echo $row_2_1['sec_6_access_road'] == 'proper' ? 'selected="selected"' : ''; ?>>पक्की सडक
                                                    </option>
                                                    <option value="ordinary" <?php echo $row_2_1['sec_6_access_road'] == 'ordinary' ? 'selected="selected"' : ''; ?>>कच्ची सडक
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group" id="access_road" style="display: none">
                                                <label>पक्की सड़क का प्रकार</label>
                                                <select name="sec_6_paved_road" id="sec_6_paved_road"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                                    <option value="">--select-- </option>
                                                    <option value="nh" <?php if ($row_2_1['sec_6_paved_road'] == 'nh') {
                                                        echo 'selected="selected"';
                                                    } ?>>नेशनल हाईवे</option>
                                                    <option value="sh" <?php if ($row_2_1['sec_6_paved_road'] == 'sh') {
                                                        echo 'selected="selected"';
                                                    } ?>>स्टेट हाईवे</option>
                                                    <option value="mdr" <?php if ($row_2_1['sec_6_paved_road'] == 'mdr') {
                                                        echo 'selected="selected"';
                                                    } ?>>एम.डी.आर. (मेजर
                                                        डिस्ट्रिक्ट रोड)</option>
                                                    <option value="odr" <?php if ($row_2_1['sec_6_paved_road'] == 'odr') {
                                                        echo 'selected="selected"';
                                                    } ?>>ओ.डी.आर.
                                                        (ऑर्डिनरी डिस्ट्रिक्ट रोड)</option>
                                                    <option value="rural_road" <?php if ($row_2_1['sec_6_paved_road'] == 'rural_road') {
                                                        echo 'selected="selected"';
                                                    } ?>>ग्रामीण सड़क
                                                    </option>
                                                    <option value="other" <?php if ($row_2_1['sec_6_paved_road'] == 'other') {
                                                        echo 'selected="selected"';
                                                    } ?>>अन्य</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>भूखण्ड का फ्र्ण्टेज्‌ (आन रोड जमीन) मीटर
                                                    में</label>
                                                <input type="text" name="sec_8_plot_frontage" id="sec_8_plot_frontage"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_2_1['sec_8_plot_frontage']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>पहुंच मार्ग का फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="sec_3_approach_image" id="sec_3_approach_image"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                            </div>
                                            <?php
                                            if (!empty($row_2_1['approach_road_photo']) && file_exists($row_2_1['approach_road_photo'])) {
                                                ?>
                                                <div class="col-sm-3 form-group">
                                                    <img src="<?php echo $row_2_1['approach_road_photo']; ?>"
                                                        class="img-fluid img-thumbnail" style="height:50px;" id="">
                                                    <label><a href="<?php echo $row_2_1['approach_road_photo']; ?>"
                                                            target="_blank">संलग्न फोटो देखें</a></label>

                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <h5> (IV) अवैध कब्जा </h5>
                                        <div class="row">
                                            <div class="col-sm-3 form-group" id="others2">
                                                <label>क्या भवन पर अवैध कब्जा है
                                                    ?</label>
                                                <select name="sec_6_illegal_possession" id="sec_6_illegal_possession"
                                                    class="form-control"
                                                    onchange="hide_show(this.value, '#others3', 'yes'); hide_show(this.value, '#others4', 'yes'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');"
                                                    tabindex="<?php echo $tab++; ?>">
                                                    <option value="">--Select-- </option>
                                                    <option value="yes" <?php echo $row_3_5_1['sec_6_illegal_possession'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>
                                                    <option value="no" <?php echo $row_3_5_1['sec_6_illegal_possession'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>
                                                </select>
                                            </div>


                                            <div class="col-sm-3 form-group" id="others3" style="display: none;">
                                                <label>अगर कब्जा है, तो विवरण दीजिए</label>
                                                <label><small>कृपया अधिकतम 200 शब्दों मे अपनी बात
                                                        रखे</small></label>
                                                <input type="text" name="sec_6_if_yes_6" id="sec_6_if_yes_6"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_3_5_1['sec_6_if_yes_6']; ?>">
                                            </div>
                                        </div>
                                        <h5> (V) शीतगृह भंडारण क्षमता </h5>

                                        <div id="sec_2_nirmit_godown">
                                            <?php
                                            // $row_3_4['count'] = 1;
                                            for ($i = 1; $i <= $row_3_4['count']; $i++) { ?>
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>क्षमता (मेट्रिक टन में)</label>
                                                        <input type="text" name="sec_3_b_storage_capacity_<?php echo $i; ?>"
                                                            id="sec_3_b_storage_capacity_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control chk_number"
                                                            data-type="2. VI.क्षमता मेट्रिक टन में भरे"
                                                            value="<?php echo $row_3_4['sec_3_b_storage_capacity_' . $i]; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>निर्माण का वर्ष</label>
                                                        <select name="sec_3_b_godown_year_<?php echo $i; ?>"
                                                            id="sec_3_b_godown_year_<?php echo $i; ?>" class="form-control"
                                                            tabindex="<?php echo $tab++; ?>">
                                                            <option value="">--Select--</option>
                                                            <option value="1999" <?php echo ($row_3_4['sec_3_b_godown_year_' . $i] == "1999") ? 'selected' : ''; ?>>2000 से
                                                                पूर्व</option>
                                                            <?php for ($a = 2000; $a <= 2024; $a++) { ?>
                                                                <option value="<?php echo $a; ?>" <?php echo ($row_3_4['sec_3_b_godown_year_' . $i] == $a) ? 'selected' : ''; ?>>
                                                                    <?php echo $a; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>WDRA से प्रमाणित है?</label>
                                                        <select name="sec_3_b_wdra_certified_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            id="sec_3_b_wdra_certified_<?php echo $i; ?>" class="form-control">
                                                            <option value="">--select--</option>
                                                            <option value="yes" <?php echo ($row_3_4['sec_3_b_wdra_certified_' . $i] == 'yes') ? ' selected="selected"' : ''; ?>>हाँ</option>
                                                            <option value="no" <?php echo ($row_3_4['sec_3_b_wdra_certified_' . $i] == 'no') ? ' selected="selected"' : ''; ?>>नहीं</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>किस योजना के तहत बना है</label>
                                                        <input type="text" name="sec_3_b_godown_type_of_fund_<?php echo $i; ?>"
                                                            id="sec_3_b_godown_type_of_fund_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_3_4['sec_3_b_godown_type_of_fund_' . $i]; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>गोदाम की भौतिक स्थिति</label>
                                                        <select name="sec_3_b_godown_status_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            id="sec_3_b_godown_status_<?php echo $i; ?>" class="form-control">
                                                            <option value="">--select--</option>
                                                            <option value="good" <?php echo ($row_3_4['sec_3_b_godown_status_' . $i] == 'good') ? ' selected="selected"' : ''; ?>>अच्छा</option>
                                                            <option value="repairable" <?php echo ($row_3_4['sec_3_b_godown_status_' . $i] == 'repairable') ? ' selected="selected"' : ''; ?>>खराब/मरम्मत
                                                                योग्य</option>
                                                            <option value="discarded" <?php echo ($row_3_4['sec_3_b_godown_status_' . $i] == 'discarded') ? ' selected="selected"' : ''; ?>>
                                                                जर्जर/निष्प्रयोज्य्य</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-sm-3 form-group">
                                                        <label>टिप्पणी</label>
                                                        <input type="text" name="sec_3_b_godown_comment_<?php echo $i; ?>"
                                                            id="sec_3_b_godown_comment_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_3_4['sec_3_b_godown_comment_' . $i]; ?>">
                                                    </div>
                                                    <?php if ($i == $row_3_4['count']) { ?>
                                                        <div class="col-sm-2 form-group my-auto" id="sec_2_nirmit_godown_rows">
                                                            <button type="button" class="btn btn-info"
                                                                onClick="sec_2_nirmit_godown_add_rows()">नई
                                                                पंक्ति जोड़े [+]</button>
                                                            <input type="hidden" name="sec_2_nirmit_godown_id"
                                                                id="sec_2_nirmit_godown_id"
                                                                value="<?php echo $row_3_4['count']; ?>">
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <h5> (VI) खाली पड़ी भूमि का विवरण </h5>
                                        <div id="sec_3_c">
                                            <?php
                                            // $row_3_5['sec_3_c_id'] = 1;
                                            for ($i = 1; $i <= $row_3_5['sec_3_c_id']; $i++) {

                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                        <input type="text" name="sec_3_c_length_<?php echo $i; ?>"
                                                            id="sec_3_c_length_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                                            data-type="2. VII. क्षेत्रफल हेक्टेयर में मे लिखे"
                                                            value="<?php echo $row_3_5['sec_3_c_length_' . $i]; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>रैक पाइण्ट से दूरी</label>
                                                        <input type="text" name="sec_3_c_rak_distance_<?php echo $i; ?>"
                                                            id="sec_3_c_rak_distance_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_3_5['sec_3_c_rak_distance_' . $i]; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="land_access_road_<?php echo $i; ?>">
                                                        <label>पहुच मार्ग का प्रकार</label>
                                                        <select name="sec_3_c_paved_road_<?php echo $i; ?>"
                                                            id="sec_3_c_paved_road_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control">
                                                            <option value="">--select-- </option>
                                                            <option value="ordinary" <?php
                                                            if ($row_3_5['sec_3_c_paved_road_' . $i] == 'ordinary') {
                                                                echo ' selected="selected"';
                                                            } ?>>कच्ची सड़क
                                                            </option>
                                                            <option value="nh" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'nh') {
                                                                echo ' selected="selected"';
                                                            } ?>>नेशनल
                                                                हाईवे</option>
                                                            <option value="sh" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'sh') {
                                                                echo ' selected="selected"';
                                                            } ?>>स्टेट
                                                                हाईवे</option>
                                                            <option value="mdr" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'mdr') {
                                                                echo ' selected="selected"';
                                                            } ?>>
                                                                एम.डी.आर.</option>
                                                            <option value="odr" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'odr') {
                                                                echo ' selected="selected"';
                                                            } ?>>
                                                                ओ.डी.आर.</option>
                                                            <option value="rural_road" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'rural_road') {
                                                                echo ' selected="selected"';
                                                            } ?>>ग्रामीण
                                                                सड़क
                                                            </option>
                                                            <option value="other" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'other') {
                                                                echo ' selected="selected"';
                                                            } ?>>अन्य
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>खाली पड़ी भूमि का किस उपयोग में लाया जा सकता है?</label>
                                                        <input type="text" name="sec_3_c_rak_distance_<?php echo $i; ?>"
                                                            id="sec_3_c_rak_distance_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_3_5['sec_3_c_rak_distance_' . $i]; ?>">
                                                    </div>

                                                    <div class="col-sm-3 form-group">
                                                        <label>भूमि का फोटो संलग्न करें</label>
                                                        <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                            class="form-control"
                                                            name="sec_3_c_food_scheme_image_<?php echo $i; ?>"
                                                            id="sec_3_c_food_scheme_image_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" <?php echo $i; ?>"
                                                            value="<?php echo $row_3_5['food_scheme' . $i]; ?>">
                                                    </div>
                                                    <?php
                                                    if (!empty($row_3_5['food_scheme' . $i]) && file_exists($row_3_5['food_scheme' . $i])) {
                                                        ?>
                                                        <div class="col-sm-1 form-group" id="">
                                                            <img src="<?php echo $row_3_5['food_scheme' . $i]; ?>"
                                                                class="img-fluid img-thumbnail" style="height:50px;"
                                                                id="sec_3_c_image_uploaded_2">
                                                            <label><a href="<?php echo $row_3_5['food_scheme' . $i]; ?>"
                                                                    target="_blank">संलग्न फोटो
                                                                    देखें</a></label>

                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($i == $row_3_5['sec_3_c_id']) {
                                                        ?>
                                                        <div class="col-sm-2 form-group my-auto" id="sec_3_c_rows">
                                                            <button type="button" class="btn btn-info"
                                                                onClick="sec_3_c_add_rows();">नई
                                                                पंक्ति
                                                                जोड़े</button>
                                                            <input type="hidden" name="sec_3_c_id" id="sec_3_c_id"
                                                                value="<?php echo $row_3_5['sec_3_c_id']; ?>">
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <h5> (VII) चारदिवारी (बाऊण्डरी वाल) </h5>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>चारदिवारी (बाऊण्डरी वाल)</label>
                                                <select name="sec_3_d_boundry" id="sec_3_d_boundry"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="hide_show(this.value, '#sec_3_d_boundry_height_1', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo $row_3_1['sec_3_d_boundry'] == 'yes' ? ' selected="selected" ' : ''; ?>>हैं</option>
                                                    <option value="no" <?php echo $row_3_1['sec_3_d_boundry'] == 'no' ? ' selected="selected" ' : ''; ?> style="background:#f00">नहीं</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group" id="sec_3_d_boundry_height_1"
                                                style="display:none">
                                                <label> बाऊण्डरी वाल की अनुमानित लंबाई (मी०)</label>
                                                <input type="number" name="sec_3_d_boundry_height"
                                                    id="sec_3_d_boundry_height" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control" value="">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>प्रमुख द्वार (मेन गेट)</label>
                                                <select name="sec_3_d_main_gate" id="sec_3_d_main_gate"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo $row_3_1['sec_3_d_main_gate'] == 'yes' ? ' selected="selected" ' : ''; ?>>हैं</option>
                                                    <option value="no" <?php echo $row_3_1['sec_3_d_main_gate'] == 'no' ? ' selected="selected" ' : ''; ?> style="background:#f00">नहीं</option>
                                                </select>
                                            </div>
                                        </div>


                                </div>
                            </div>
                        </div>
                        <h5 class="section-title"></i> 4. आधारभूत सुविधाएं</h5>

                        <!-- (I) विद्युत कनेक्शन -->
                        <div style="margin-bottom: 20px; margin-left: 10px; margin-right: 10px;">
                            <h6
                                style="font-size: 18px; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">
                                (I) विद्युत कनेक्शन</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>विद्युत कनेक्शन है या नहीं ?</label>
                                        <select name="elec_conn" id="elec_conn" class="form-control"
                                            onchange="toggleElecDetail(this.value)">
                                            <option value="">-- चुनें --</option>
                                            <option value="yes" <?php if (val('elec_conn') == 'yes')
                                                echo 'selected'; ?>>हाँ
                                            </option>
                                            <option value="no" <?php if (val('elec_conn') == 'no')
                                                echo 'selected'; ?>>नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 elec_yes_row"
                                    style="<?php echo (val('elec_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>यदि है तो चालू है या नहीं ?</label>
                                        <select name="elec_working" class="form-control"
                                            onchange="toggleElecWorking(this.value)">
                                            <option value="">-- चुनें --</option>
                                            <option value="yes" <?php if (val('elec_working') == 'yes')
                                                echo 'selected'; ?>>
                                                हाँ
                                            </option>
                                            <option value="no" <?php if (val('elec_working') == 'no')
                                                echo 'selected'; ?>>नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 elec_no_row"
                                    style="<?php echo (val('elec_conn') == 'no') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>यदि नहीं है तो प्रस्ताव</label>
                                        <textarea name="elec_proposal" class="form-control"
                                            rows="1"><?php echo val('elec_proposal'); ?></textarea>
                                    </div>
                                </div>
                                <!-- Show if working NO -->
                                <div class="col-md-4 elec_working_no"
                                    style="<?php echo (val('elec_working') == 'no') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>यदि चालू नहीं है तो कारण</label>
                                        <input type="text" name="elec_not_working_reason" class="form-control"
                                            value="<?php echo val('elec_not_working_reason'); ?>">
                                    </div>
                                </div>
                                <!-- Show if working YES -->
                                <div class="col-md-4 elec_working_yes"
                                    style="<?php echo (val('elec_working') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>बिल नियमित भुगतान हो रहा है?</label>
                                        <select name="elec_bill_regular" class="form-control"
                                            onchange="toggleElecBill(this.value)">
                                            <option value="">-- चुनें --</option>
                                            <option value="yes" <?php if (val('elec_bill_regular') == 'yes')
                                                echo 'selected'; ?>>हाँ
                                            </option>
                                            <option value="no" <?php if (val('elec_bill_regular') == 'no')
                                                echo 'selected'; ?>>नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row elec_bill_no"
                                style="<?php echo (val('elec_bill_regular') == 'no' && val('elec_working') == 'yes') ? 'display:flex;' : 'display:none;'; ?>">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>कितने माह से भुगतान नहीं है?</label>
                                        <input type="number" name="elec_months_due" class="form-control"
                                            value="<?php echo val('elec_months_due'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>बकाया धनराशि (रुपये)</label>
                                        <input type="number" step="0.01" name="elec_outstanding" class="form-control"
                                            value="<?php echo val('elec_outstanding'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- (II) सोलर कनेक्शन -->
                        <div style="margin-bottom: 20px; margin-left: 10px; margin-right: 10px;">
                            <h6
                                style="font-size: 18px; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">
                                (II) सोलर कनेक्शन</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>सोलर की उपलब्धता है या नहीं ?</label>
                                        <select name="solar_conn" class="form-control"
                                            onchange="toggleSolarDetail(this.value)">
                                            <option value="">-- चुनें --</option>
                                            <option value="yes" <?php if (val('solar_conn') == 'yes')
                                                echo 'selected'; ?>>हाँ
                                            </option>
                                            <option value="no" <?php if (val('solar_conn') == 'no')
                                                echo 'selected'; ?>>नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 solar_yes_row"
                                    style="<?php echo (val('solar_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>यदि है तो चालू है या नहीं ?</label>
                                        <select name="solar_working" class="form-control"
                                            onchange="toggleSolarWorking(this.value)">
                                            <option value="">-- चुनें --</option>
                                            <option value="yes" <?php if (val('solar_working') == 'yes')
                                                echo 'selected'; ?>>
                                                हाँ
                                            </option>
                                            <option value="no" <?php if (val('solar_working') == 'no')
                                                echo 'selected'; ?>>
                                                नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 solar_working_yes"
                                    style="<?php echo (val('solar_working') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>बैट्री की स्थिति</label>
                                        <select name="solar_battery_status" class="form-control">
                                            <option value="good" <?php if (val('solar_battery_status') == 'good')
                                                echo 'selected'; ?>>
                                                अच्छी (Good)</option>
                                            <option value="poor" <?php if (val('solar_battery_status') == 'poor')
                                                echo 'selected'; ?>>
                                                खराब (Poor)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- (III) इण्टरनेट कनेक्शन -->
                        <div style="margin-bottom: 20px; margin-left: 10px; margin-right: 10px;">
                            <h6
                                style="font-size: 18px; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">
                                (III) इंटरनेट कनेक्शन</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>क्या इंटरनेट कनेक्शन है ?</label>
                                        <select name="internet_conn" class="form-control"
                                            onchange="toggleInternetDetail(this.value)">
                                            <option value="">-- चुनें --</option>
                                            <option value="yes" <?php if (val('internet_conn') == 'yes')
                                                echo 'selected'; ?>>
                                                हाँ
                                            </option>
                                            <option value="no" <?php if (val('internet_conn') == 'no')
                                                echo 'selected'; ?>>
                                                नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 internet_yes_row"
                                    style="<?php echo (val('internet_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>सर्विस प्रोवाइडर का नाम</label>
                                        <select name="internet_provider" class="form-control">
                                            <option value="">-- चुनें --</option>
                                            <option value="bsnl" <?php if (val('internet_provider') == 'bsnl')
                                                echo 'selected'; ?>>
                                                BSNL</option>
                                            <option value="jio" <?php if (val('internet_provider') == 'jio')
                                                echo 'selected'; ?>>JIO
                                            </option>
                                            <option value="vodafone" <?php if (val('internet_provider') == 'vodafone')
                                                echo 'selected'; ?>>Vodafone</option>
                                            <option value="airtel" <?php if (val('internet_provider') == 'airtel')
                                                echo 'selected'; ?>>Airtel</option>
                                            <option value="sdwan" <?php if (val('internet_provider') == 'sdwan')
                                                echo 'selected'; ?>>
                                                SDWAN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 internet_yes_row"
                                    style="<?php echo (val('internet_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>बिल नियमित भुगतान?</label>
                                        <select name="internet_bill_paid" class="form-control">
                                            <option value="yes" <?php if (val('internet_bill_paid') == 'yes')
                                                echo 'selected'; ?>>हाँ
                                            </option>
                                            <option value="no" <?php if (val('internet_bill_paid') == 'no')
                                                echo 'selected'; ?>>नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 internet_yes_row"
                                    style="<?php echo (val('internet_conn') == 'yes') ? 'display:block;' : 'display:none;'; ?>">
                                    <div class="form-group">
                                        <label>कनेक्शन एक्टिव है?</label>
                                        <select name="internet_active" class="form-control">
                                            <option value="yes" <?php if (val('internet_active') == 'yes')
                                                echo 'selected'; ?>>हाँ
                                            </option>
                                            <option value="no" <?php if (val('internet_active') == 'no')
                                                echo 'selected'; ?>>
                                                नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- (IV) पेयजल की उपलब्धता -->
                        <div style="margin-bottom: 20px; margin-left: 10px; margin-right: 10px;">
                            <h6
                                style="font-size: 18px; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; color: #333;">
                                (IV) पेयजल की उपलब्धता</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>पेयजल की उपलब्धता है?</label>
                                        <select name="water_availability" class="form-control">
                                            <option value="">-- चुनें --</option>
                                            <option value="yes" <?php if (val('water_availability') == 'yes')
                                                echo 'selected'; ?>>हाँ
                                            </option>
                                            <option value="no" <?php if (val('water_availability') == 'no')
                                                echo 'selected'; ?>>नहीं
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>आपूर्ति संचालित है?</label>
                                        <select name="water_status" class="form-control">
                                            <option value="operational" <?php if (val('water_status') == 'operational')
                                                echo 'selected'; ?>>हाँ (संचालित)</option>
                                            <option value="non_operational" <?php if (val('water_status') == 'non_operational')
                                                echo 'selected'; ?>>नहीं (असंचालित)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="section-title"></i> 5. क्या शीतगृह पर कोई ऋण हैं?</h5>
                        <div class="row" stle="margin-left: 10px;  margin-right: 10px;">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>क्या ऋण हैं?</label>
                                    <select name="has_loan" class="form-control" onchange="toggleLoan(this.value)">
                                        <option value="">-- चुनें --</option>
                                        <option value="yes" <?php if (val('has_loan') == 'yes')
                                            echo 'selected'; ?>>हाँ
                                        </option>
                                        <option value="no" <?php if (val('has_loan') == 'no')
                                            echo 'selected'; ?>>नहीं
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row loan_sub"
                            style="<?php echo (val('has_loan') == 'yes') ? 'display:flex;' : 'display:none;'; ?>">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>एन.सी.डी.सी. (लाख रुपये मे)</label>
                                    <input type="number" step="0.01" name="ncdc_loan" class="form-control"
                                        value="<?php echo val('ncdc_loan'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>व्यावसायिक बैंक (लाख रुपये मे)</label>
                                    <input type="number" step="0.01" name="bank_loan" class="form-control"
                                        value="<?php echo val('bank_loan'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>यू.पी.सी.बी. (लाख रुपये मे)</label>
                                    <input type="number" step="0.01" name="upcb_loan" class="form-control"
                                        value="<?php echo val('upcb_loan'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>डी.सी.बी. (लाख रुपये मे)</label>
                                    <input type="number" step="0.01" name="dcb_loan" class="form-control"
                                        value="<?php echo val('dcb_loan'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>अन्य ऋण (Other Loan)</label>
                                    <input type="text" name="other_loan_desc" class="form-control"
                                        value="<?php echo val('other_loan_desc'); ?>" placeholder="विवरण लिखें">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>अन्य ऋण की धनराशि</label>
                                    <input type="number" step="0.01" name="other_loan_amount" class="form-control"
                                        value="<?php echo val('other_loan_amount'); ?>" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                                <label style="font-weight: bold; font-size: 15px; color: #333;">ऋण का विवरण</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="assets_loan_table">
                                        <thead class="bg-light">
                                            <tr style="background:#fef3e2">
                                                <th style="font-weight: bold; font-size: 16px;">ऋण की तिथि</th>
                                                <th style="font-weight: bold; font-size: 16px;">मूलधन धनराशि (₹)</th>
                                                <th style="font-weight: bold; font-size: 16px;">मूलधन अवशेष (₹)</th>
                                                <th style="font-weight: bold; font-size: 16px;">ब्याज धनराशि (₹)</th>
                                                <th style="font-weight: bold; font-size: 16px;">ब्याज अवशेष (₹)</th>

                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $assets_data = json_decode(val('assets_loan_data'), true) ?: [[]];
                                            foreach ($assets_data as $ad): ?>
                                                <tr>
                                                    <td><input type="date" step="0.01" name="assets_loan[loan_date][]"
                                                            class="form-control"
                                                            value="<?php echo isset($ad['loan_date']) ? $ad['loan_date'] : ''; ?>">
                                                    </td>
                                                    <td><input type="number" step="0.01" name="assets_loan[principal_amt][]"
                                                            class="form-control"
                                                            value="<?php echo isset($ad['principal_amt']) ? $ad['principal_amt'] : ''; ?>"
                                                            placeholder="0.00"></td>
                                                    <td><input type="number" step="0.01" name="assets_loan[principal_pending][]"
                                                            class="form-control"
                                                            value="<?php echo isset($ad['principal_pending']) ? $ad['principal_pending'] : ''; ?>"
                                                            placeholder="0.00"></td>
                                                    <td><input type="number" step="0.01" name="assets_loan[interest_amt][]"
                                                            class="form-control"
                                                            value="<?php echo isset($ad['interest_amt']) ? $ad['interest_amt'] : ''; ?>"
                                                            placeholder="0.00"></td>
                                                    <td><input type="number" step="0.01" name="assets_loan[interest_pending][]"
                                                            class="form-control"
                                                            value="<?php echo isset($ad['interest_pending']) ? $ad['interest_pending'] : ''; ?>"
                                                            placeholder="0.00"></td>

                                                    <td><button type="button" class="btn btn-danger btn-sm"
                                                            onclick="$(this).closest('tr').remove()"><i
                                                                class="fa fa-times"></i></button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success btn-sm mb-3" onclick="addAssetsLoanRow()"><i
                                        class="fa fa-plus"></i> नई लाइन जोड़ें (Add Asset/Loan)</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5
                                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                    अन्य ऋण</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3 form-group">
                                <label>(I) विद्युत बिल</label>
                                <select name="elec_bill_status" class="form-control"
                                    onchange="hide_show(this.value, '#elec_bill_amount_div', 'yes')">
                                    <option value="">-- चुनें --</option>
                                    <option value="yes" <?php echo (val('elec_bill_status') == 'yes') ? 'selected="selected"' : ''; ?>>हाँ</option>
                                    <option value="no" <?php echo (val('elec_bill_status') == 'no') ? 'selected="selected"' : ''; ?>>
                                        नहीं</option>
                                </select>
                                <div id="elec_bill_amount_div"
                                    style="display: <?php echo (val('elec_bill_status') == 'yes') ? 'block' : 'none'; ?>; margin-top: 10px;">
                                    <input type="number" step="0.01" name="elec_bill" class="form-control"
                                        placeholder="धनराशि भरें" value="<?php echo val('elec_bill'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>(II) कर</label>
                                <select name="other_dues_tax_status" class="form-control"
                                    onchange="hide_show(this.value, '#other_dues_tax_amount_div', 'yes')">
                                    <option value="">-- चुनें --</option>
                                    <option value="yes" <?php echo (val('other_dues_tax_status') == 'yes') ? 'selected="selected"' : ''; ?>>हाँ</option>
                                    <option value="no" <?php echo (val('other_dues_tax_status') == 'no') ? 'selected="selected"' : ''; ?>>नहीं</option>
                                </select>
                                <div id="other_dues_tax_amount_div"
                                    style="display: <?php echo (val('other_dues_tax_status') == 'yes') ? 'block' : 'none'; ?>; margin-top: 10px;">
                                    <input type="number" step="0.01" name="other_dues_tax" class="form-control"
                                        placeholder="धनराशि भरें" value="<?php echo val('other_dues_tax'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>(III) कर्मचारियों का वेतन</label>
                                <select name="other_dues_salary_status" class="form-control"
                                    onchange="hide_show(this.value, '#other_dues_salary_amount_div', 'yes')">
                                    <option value="">-- चुनें --</option>
                                    <option value="yes" <?php echo (val('other_dues_salary_status') == 'yes') ? 'selected="selected"' : ''; ?>>हाँ</option>
                                    <option value="no" <?php echo (val('other_dues_salary_status') == 'no') ? 'selected="selected"' : ''; ?>>नहीं</option>
                                </select>
                                <div id="other_dues_salary_amount_div"
                                    style="display: <?php echo (val('other_dues_salary_status') == 'yes') ? 'block' : 'none'; ?>; margin-top: 10px;">
                                    <input type="number" step="0.01" name="other_dues_salary" class="form-control"
                                        placeholder="धनराशि भरें" value="<?php echo val('other_dues_salary'); ?>">
                                </div>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>(IV) अन्य</label>
                                <select name="other_dues_other_status" class="form-control"
                                    onchange="hide_show(this.value, '#other_dues_amount_div', 'yes')">
                                    <option value="">-- चुनें --</option>
                                    <option value="yes" <?php echo (val('other_dues_other_status') == 'yes') ? 'selected="selected"' : ''; ?>>हाँ</option>
                                    <option value="no" <?php echo (val('other_dues_other_status') == 'no') ? 'selected="selected"' : ''; ?>>नहीं</option>
                                </select>
                                <div id="other_dues_amount_div"
                                    style="display: <?php echo (val('other_dues_other_status') == 'yes') ? 'block' : 'none'; ?>; margin-top: 10px;">
                                    <input type="number" step="0.01" name="other_dues_other" class="form-control"
                                        placeholder="धनराशि भरें" value="<?php echo val('other_dues_other'); ?>">
                                </div>
                            </div>
                        </div>

                        <h5 class="section-title"> 6. मानव सम्पदा (Manav Sampada)</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="employee_table" style="min-width: 1200px;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="font-weight: bold; font-size: 16px;">नाम</th>
                                                <th style="font-weight: bold; font-size: 16px;">पदनाम</th>
                                                <th style="font-weight: bold; font-size: 16px;">नियुक्ति की तिथि</th>
                                                <th style="font-weight: bold; font-size: 16px;">कार्यरत की स्थिति</th>
                                                <th style="font-weight: bold; font-size: 16px;">मोबाइल न०</th>
                                                <th style="font-weight: bold; font-size: 16px;">ई-मेल आईडी</th>
                                                <th style="font-weight: bold; font-size: 16px;">नियुक्ति का स्रोत</th>
                                                <th style="font-weight: bold; font-size: 16px;">अनुमोदन का स्तर</th>
                                                <th style="font-weight: bold; font-size: 16px;">नियुक्ति का प्रकार</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $emps = json_decode(val('employees_data'), true) ?: [[]];
                                            foreach ($emps as $e): ?>
                                                <tr>
                                                    <td><input type="text" name="emp[name][]" class="form-control"
                                                            value="<?php echo isset($e['name']) ? $e['name'] : ''; ?>"></td>
                                                    <td><input type="text" name="emp[designation][]" class="form-control"
                                                            value="<?php echo isset($e['designation']) ? $e['designation'] : ''; ?>">
                                                    </td>
                                                    <td><input type="date" name="emp[appt_date][]" class="form-control"
                                                            value="<?php echo isset($e['appt_date']) ? $e['appt_date'] : ''; ?>">
                                                    </td>
                                                    <td>
                                                        <select name="emp[working_status][]" class="form-control">
                                                            <option value="">--चुनें--</option>
                                                            <option value="working" <?php echo (isset($e['working_status']) && $e['working_status'] == 'working') ? 'selected' : ''; ?>>कार्यरत
                                                            </option>
                                                            <option value="retired" <?php echo (isset($e['working_status']) && $e['working_status'] == 'retired') ? 'selected' : ''; ?>>सेवा
                                                                निवृत
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="emp[mobile][]" class="form-control"
                                                            value="<?php echo isset($e['mobile']) ? $e['mobile'] : ''; ?>"></td>
                                                    <td><input type="email" name="emp[email][]" class="form-control"
                                                            value="<?php echo isset($e['email']) ? $e['email'] : ''; ?>"></td>
                                                    <td>
                                                        <select name="emp[appt_source][]" class="form-control"
                                                            onchange="if(this.value=='other'){ $(this).next().show(); } else { $(this).next().hide(); }">
                                                            <option value="">--चुनें--</option>
                                                            <option value="society" <?php echo (isset($e['appt_source']) && $e['appt_source'] == 'society') ? 'selected' : ''; ?>>समिति द्वारा
                                                            </option>
                                                            <option value="other" <?php echo (isset($e['appt_source']) && $e['appt_source'] == 'other') ? 'selected' : ''; ?>>अन्य</option>
                                                        </select>
                                                        <input type="text" name="emp[appt_source_other][]"
                                                            class="form-control mt-1" placeholder="किसके द्वारा"
                                                            style="display: <?php echo (isset($e['appt_source']) && $e['appt_source'] == 'other') ? 'block' : 'none'; ?>;"
                                                            value="<?php echo isset($e['appt_source_other']) ? $e['appt_source_other'] : ''; ?>">
                                                    </td>
                                                    <td>
                                                        <select name="emp[approval_level][]" class="form-control">
                                                            <option value="">--चुनें--</option>
                                                            <!-- <option value="not_approved" <?php echo (isset($e['approval_level']) && $e['approval_level'] == 'not_approved') ? 'selected' : ''; ?>>Not Approved</option> -->
                                                            <option value="DR" <?php echo (isset($e['approval_level']) && $e['approval_level'] == 'DR') ? 'selected' : ''; ?>>DR</option>
                                                            <option value="AR" <?php echo (isset($e['approval_level']) && $e['approval_level'] == 'AR') ? 'selected' : ''; ?>>AR</option>
                                                            <option value="ADCO" <?php echo (isset($e['approval_level']) && $e['approval_level'] == 'ADCO') ? 'selected' : ''; ?>>ADCO
                                                            </option>
                                                            <option value="ADO" <?php echo (isset($e['approval_level']) && $e['approval_level'] == 'ADO') ? 'selected' : ''; ?>>ADO</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="emp[appt_type][]" class="form-control"
                                                            value="<?php echo isset($e['appt_type']) ? $e['appt_type'] : ''; ?>">
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger btn-sm"
                                                            onclick="$(this).closest('tr').remove()"><i
                                                                class="fa fa-times"></i></button></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success btn-sm mb-3" onclick="addEmployeeRow()"><i
                                        class="fa fa-plus"></i> कर्मचारी जोड़ें</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>कार्ययोजना (Future Plan)</label>
                                    <textarea name="action_plan" class="form-control"
                                        rows="2"><?php echo val('action_plan'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <!-- <button type="button" id="cancelBtn" class="btn btn-secondary">रद्द करें</button> -->
                            <button type="submit" class="btn btn-primary btn-fill"><i class="fa fa-save"></i>Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <!-- SUCCESS CARD (Shown after successful submission) -->
    <?php if (isset($show_success) && $show_success): ?>
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
                        <button class="btn btn-info" onclick="window.open('cold_storage_report.php', '_blank');">प्रपत्र पुनः
                            निरीक्षण के लिये देखे</button>
                        <!-- <button class="btn btn-secondary" onclick="window.location.href='cold_storage.php'">वापस जाएं</button> -->
                    </div>
                    <div class="col-md-12 text-center mt-4">
                        <p><input type="checkbox" style="height: 20px; border:1px solid;" id="review_ack"
                                onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
                            मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी
                            सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
                        <button type="button" class="btn btn-danger" id="verification_button" disabled="disabled">सत्यापन के
                            लिये आगे प्रेषित
                            करें
                        </button>
                    </div>

                    <div class="col-sm-12 form-group my-auto text-center mt-3" id="send_otp_button2" style="display: none">
                        <button type="button" name="verify_otp_btn" id="verify_otp_btn" class="btn btn-info"
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
            data: {
                val: val
            },
            success: function (data) {
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
            data: {
                val: val
            },
            success: function (data) {
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
            data: {
                val: val
            },
            success: function (data) {
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
            success: function (data) {
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
            <td><input type="text" name="emp[name][]" class="form-control"></td>
            <td><input type="text" name="emp[designation][]" class="form-control"></td>
            <td><input type="date" name="emp[appt_date][]" class="form-control"></td>
            <td>
                <select name="emp[working_status][]" class="form-control">
                    <option value="">--चुनें--</option>
                    <option value="working">कार्यरत</option>
                    <option value="retired">सेवा निवृत (Pension)</option>
                </select>
            </td>
            <td><input type="text" name="emp[mobile][]" class="form-control"></td>
            <td><input type="email" name="emp[email][]" class="form-control"></td>
            <td>
                <select name="emp[appt_source][]" class="form-control" onchange="if(this.value=='other'){ $(this).next().show(); } else { $(this).next().hide(); }">
                    <option value="">--चुनें--</option>
                    <option value="society">समिति द्वारा</option>
                    <option value="other">अन्य</option>
                </select>
                <input type="text" name="emp[appt_source_other][]" class="form-control mt-1" placeholder="किसके द्वारा" style="display: none;">
            </td>
            <td>
                <select name="emp[approval_level][]" class="form-control">
                    <option value="">--चुनें--</option>
                    <option value="not_approved">Not Approved</option>
                    <option value="DR">DR</option>
                    <option value="AR">AR</option>
                    <option value="ADCO">ADCO</option>
                    <option value="ADO">ADO</option>
                </select>
            </td>
            <td><input type="text" name="emp[appt_type][]" class="form-control" placeholder="PD/MP"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove()"><i class="fa fa-times"></i></button></td>
        </tr>`;
        $('#employee_table tbody').append(row);
    }

    function addAssetsLoanRow() {
        var row = `<tr>
            <td><input type="number" step="0.01" name="assets_loan[principal_amt][]" class="form-control" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="assets_loan[principal_pending][]" class="form-control" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="assets_loan[interest_amt][]" class="form-control" placeholder="0.00"></td>
            <td><input type="number" step="0.01" name="assets_loan[interest_pending][]" class="form-control" placeholder="0.00"></td>

            <td><button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('tr').remove()"><i class="fa fa-times"></i></button></td>
        </tr>`;
        $('#assets_loan_table tbody').append(row);
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
            if (key === 'is_active') {
                if (val === 'active') val = 'सक्रिय (Active)';
                else if (val === 'inactive') val = 'असंचालित (Inactive)';
                else if (val === 'under_repair') val = 'अन्य (Other)';
            }
            if (key.includes('loan') || key === 'elec_bill') val = '₹' + parseFloat(val || 0).toLocaleString('en-IN');

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

    $(document).ready(function () {
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
        $('#addNewBtn').click(function () {
            $('#historyCard').fadeOut(300, function () {
                $('#formCard').fadeIn(300);
            });
        });

        $('#backToHistoryBtn, #cancelBtn').click(function () {
            $('#formCard').fadeOut(300, function () {
                $('#historyCard').fadeIn(300);
                window.location.href = 'cold_storage.php';
            });
        });

        <?php if ($is_edit): ?>
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

            if (!empty($edit_data['district_name'])) {
                $dist_res = execute_query("SELECT sno, division_id FROM master_district WHERE district_name = '" . mysqli_real_escape_string($db, $edit_data['district_name']) . "'");
                if ($dist_row = mysqli_fetch_assoc($dist_res)) {
                    $edit_district_id = $dist_row['sno'];
                    $edit_division_id = $dist_row['division_id'];
                }
            }

            if (!empty($edit_data['tehseel_name'])) {
                $teh_res = execute_query("SELECT sno FROM master_tehseel WHERE tehseel_name = '" . mysqli_real_escape_string($db, $edit_data['tehseel_name']) . "'");
                if ($teh_row = mysqli_fetch_assoc($teh_res)) {
                    $edit_tehseel_id = $teh_row['sno'];
                }
            }

            if (!empty($edit_data['block_name'])) {
                $blk_res = execute_query("SELECT sno FROM master_block WHERE block_name = '" . mysqli_real_escape_string($db, $edit_data['block_name']) . "'");
                if ($blk_row = mysqli_fetch_assoc($blk_res)) {
                    $edit_block_id = $blk_row['sno'];
                }
            }

            if (!empty($edit_data['society_name'])) {
                $soc_res = execute_query("SELECT sno FROM test2 WHERE col4 = '" . mysqli_real_escape_string($db, $edit_data['society_name']) . "'");
                if ($soc_row = mysqli_fetch_assoc($soc_res)) {
                    $edit_society_id = $soc_row['sno'];
                }
            }
            ?>

            // Set division first
            <?php if ($edit_division_id): ?>
                $('#division_name').val('<?php echo $edit_division_id; ?>');

                // Load districts for this division
                fill_district('<?php echo $edit_division_id; ?>');

                // Wait for districts to load, then set district
                setTimeout(function () {
                    <?php if ($edit_district_id): ?>
                        $('#district_name').val('<?php echo $edit_district_id; ?>');

                        // Load tehseels for this district
                        fill_tehseel('<?php echo $edit_district_id; ?>');

                        // Wait for tehseels to load, then set tehseel
                        setTimeout(function () {
                            <?php if ($edit_tehseel_id): ?>
                                $('#tehseel_name').val('<?php echo $edit_tehseel_id; ?>');

                                // Load blocks for this tehseel
                                fill_block('<?php echo $edit_tehseel_id; ?>');

                                // Wait for blocks to load, then set block
                                setTimeout(function () {
                                    <?php if ($edit_block_id): ?>
                                        $('#block_name').val('<?php echo $edit_block_id; ?>');

                                        // Load societies for this block
                                        fill_society('<?php echo $edit_block_id; ?>');

                                        // Wait for societies to load, then set society
                                        setTimeout(function () {
                                            <?php if ($edit_society_id): ?>
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

        <?php if (isset($show_success) && $show_success): ?>
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
                reader.onload = function (e) {
                    preview.append('<img src="' + e.target.result + '" style="height: 60px; border-radius: 4px; border: 1px solid #ddd; object-fit: cover;">');
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // Filter dynamic scripts
    function filter_fill_district(val) {
        var data = {
            "term": "b",
            "id": "dist",
            "val": val
        };
        $.ajax({
            type: "POST",
            url: 'scripts/survey_form_ajax.php',
            data: data,
            success: function (data) {
                var txt = '<option value="">All</option>';
                data = JSON.parse(data);
                $.each(data, function (key, value) {
                    txt += '<option value="' + value.id + '">' + value.district_name + '</option>';
                });
                $("#f_district").html(txt);
                $("#f_tehseel").html('<option value="">All</option>');
                $("#f_block").html('<option value="">All</option>');
            }
        });
    }

    function filter_fill_tehseel(val) {
        var data = {
            term: "b",
            id: "tehseel",
            val: val
        };
        $.ajax({
            type: "POST",
            url: 'scripts/survey_form_ajax.php',
            data: data,
            success: function (data) {
                var txt = '<option value="">All</option>';
                data = JSON.parse(data);
                $.each(data, function (key, value) {
                    txt += '<option value="' + value.id + '">' + value.tehseel_name + '</option>';
                });
                $("#f_tehseel").html(txt);
                $("#f_block").html('<option value="">All</option>');
            }
        });
    }

    function filter_fill_block(val) {
        var data = {
            term: "b",
            id: "block",
            val: val
        };
        $.ajax({
            type: "POST",
            url: 'scripts/survey_form_ajax.php',
            data: data,
            success: function (data) {
                var txt = '<option value="">All</option>';
                data = JSON.parse(data);
                $.each(data, function (key, value) {
                    txt += '<option value="' + value.id + '">' + value.block_name + '</option>';
                });
                $("#f_block").html(txt);
            }
        });
    }
</script>

<script>
    function toggleElecDetail(val) {
        if (val == 'yes') {
            $('.elec_yes_row').show();
            $('.elec_no_row').hide();
        } else if (val == 'no') {
            $('.elec_yes_row').hide();
            $('.elec_no_row').show();
            $('.elec_working_yes, .elec_working_no, .elec_bill_no').hide();
        } else {
            $('.elec_yes_row, .elec_no_row, .elec_working_yes, .elec_working_no, .elec_bill_no').hide();
        }
    }

    function toggleOwnership(val) {
        if (val == 'rented') {
            $('.rented_only').show();
            $('#ownership_other_div').hide();
        } else if (val == 'other') {
            $('.rented_only').hide();
            $('#ownership_other_div').show();
        } else {
            $('.rented_only').hide();
            $('#ownership_other_div').hide();
        }
    }

    function toggleApproachRoad(val) {
        if (val == 'other') $('#approach_other_div').show();
        else $('#approach_other_div').hide();
    }

    function toggleLitigation(val) {
        if (val == 'yes') $('.litigation_div').show();
        else $('.litigation_div').hide();
    }

    function toggleElecWorking(val) {
        if (val == 'yes') {
            $('.elec_working_yes').show();
            $('.elec_working_no').hide();
        } else if (val == 'no') {
            $('.elec_working_yes').hide();
            $('.elec_working_no').show();
            $('.elec_bill_no').hide();
        } else {
            $('.elec_working_yes, .elec_working_no, .elec_bill_no').hide();
        }
    }

    function toggleElecBill(val) {
        if (val == 'no') $('.elec_bill_no').show();
        else $('.elec_bill_no').hide();
    }

    function toggleSolarDetail(val) {
        if (val == 'yes') $('.solar_yes_row').show();
        else {
            $('.solar_yes_row, .solar_working_yes').hide();
        }
    }

    function toggleSolarWorking(val) {
        if (val == 'yes') $('.solar_working_yes').show();
        else $('.solar_working_yes').hide();
    }

    function toggleInternetDetail(val) {
        if (val == 'yes') $('.internet_yes_row').show();
        else $('.internet_yes_row').hide();
    }
</script>

<?php
page_footer_start();
page_footer_end();
?>