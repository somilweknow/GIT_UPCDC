<?php
include("scripts/settings.php");

// --- START AUTHENTICATION AND DATA PREP ---

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    if ((isset($_POST['action']) || isset($_GET['action']))) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
    header('Location: index.php');
    exit;
}

$user_role = strtolower(trim($_SESSION['role'] ?? ''));
$full_access_roles = array('sadmin', 'admin', 'report'); 
$user_id = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? '');
$user_districts = [];
if (!empty($user_id)) {
    $sql_ud = "SELECT district_id FROM user_district WHERE user_id = ? AND status = 1";
    $stmt_ud = mysqli_prepare($conn, $sql_ud);    
    if ($stmt_ud) {
        mysqli_stmt_bind_param($stmt_ud, "s", $user_id);
        mysqli_stmt_execute($stmt_ud);
        $res_ud = mysqli_stmt_get_result($stmt_ud);
        if ($res_ud) {
            while ($r = mysqli_fetch_assoc($res_ud)) {
                $user_districts[] = $r['district_id'];
            }
        }
    }
}
if (empty($user_districts) && !empty($_SESSION['district'])) {
    $user_districts[] = $_SESSION['district'];
}

$district_names_map = array();
if (!empty($user_districts)) {
    $placeholders = str_repeat('?,', count($user_districts) - 1) . '?';
    $sql_dist = "SELECT district_id, district_name FROM district WHERE district_id IN ($placeholders)";
    $stmt_dist = mysqli_prepare($conn, $sql_dist);
    if ($stmt_dist) {
        $types = str_repeat('s', count($user_districts));
        $stmt_dist->bind_param($types, ...$user_districts);
        mysqli_stmt_execute($stmt_dist);
        $result_dist = mysqli_stmt_get_result($stmt_dist);
        while ($row_dist = mysqli_fetch_assoc($result_dist)) {
            $district_names_map[$row_dist['district_id']] = $row_dist['district_name'];
        }
    }
}

// --- END AUTHENTICATION AND DATA PREP ---

// --- START ACTION HANDLER (AJAX) ---

if (isset($_POST['action']) || isset($_GET['action'])) {
    $response = array('success' => false, 'message' => '', 'debug' => '');
    
    // ACTION: check_entry
    if (isset($_GET['action']) && $_GET['action'] === 'check_entry') {
        $date = mysqli_real_escape_string($conn, $_GET['date']);
        $district_id = mysqli_real_escape_string($conn, $_GET['district_id']);
        
        $sql = "SELECT COUNT(*) as count FROM daily_fertilizer_entry 
                WHERE entry_date = ? AND district_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $date, $district_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        $response['exists'] = $row['count'] > 0;
        $response['success'] = true;
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // ACTION: get_entry
    if (isset($_GET['action']) && $_GET['action'] === 'get_entry') {
        $date = mysqli_real_escape_string($conn, $_GET['date']);
        $district_id = mysqli_real_escape_string($conn, $_GET['district_id']);
        $sql = "SELECT * FROM daily_fertilizer_entry WHERE entry_date = ? AND district_id = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $date, $district_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        if ($row) {
            $response['success'] = true;
            $response['entry'] = $row;
        } else {
            $response['message'] = 'Entry not found';
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // ACTION: delete_entry
    if (isset($_POST['action']) && $_POST['action'] === 'delete_entry') {
        try {
            $date = mysqli_real_escape_string($conn, $_POST['entry_date']);
            $district_id = mysqli_real_escape_string($conn, $_POST['district_id']);
            if (!in_array($user_role, ['sadmin', 'admin'])) {
                if (empty($user_districts) || !in_array($district_id, $user_districts)) {
                    throw new Exception('You do not have permission to delete this district entry');
                }
            }
            $sql = "DELETE FROM daily_fertilizer_entry WHERE entry_date = ? AND district_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $date, $district_id);
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = 'Entry deleted';
            } else {
                throw new Exception(mysqli_error($conn));
            }
        } catch (Exception $e) {
            $response['message'] = 'Error deleting entry';
            $response['debug'] = $e->getMessage();
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    // ACTION: save_entry
    if ($_POST['action'] === 'save_entry') {
        try {
            $entry_date = mysqli_real_escape_string($conn, $_POST['entry_date']);
            $district_id = mysqli_real_escape_string($conn, $_POST['district_id']);
            if (!in_array($user_role, $full_access_roles)) {
                if (empty($user_districts) || !in_array($district_id, $user_districts)) {
                    throw new Exception('You do not have permission to add/edit entries for the selected district');
                }
            }
            
            $sql = "INSERT INTO daily_fertilizer_entry (
                entry_date, district_id, total_coop_centers_district, trucks_required_dispatch,
                phosphatic_centers_less_7_5mt, urea_sale_quantity_mt, urea_sale_amount_lakh_rs,
                dap_sale_quantity_mt, dap_sale_amount_lakh_rs, npk_sale_quantity_mt,
                npk_sale_amount_lakh_rs, total_sale_quantity_mt, total_sale_amount_lakh_rs,
                bank_deposit_amount_lakh_rs, rtgs_iffco_amount_lakh_rs, rtgs_kribhco_amount_lakh_rs,
                rtgs_pcf_amount_lakh_rs, rtgs_total_amount_lakh_rs, dm_allocated_phosphatic_centers,
                pcf_dispatch_rtgs_quantity_mt, pcf_dispatch_rtgs_amount_lakh_rs,
                pcf_pending_quantity_mt, pcf_pending_amount_lakh_rs
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                total_coop_centers_district = VALUES(total_coop_centers_district),
                trucks_required_dispatch = VALUES(trucks_required_dispatch),
                phosphatic_centers_less_7_5mt = VALUES(phosphatic_centers_less_7_5mt),
                urea_sale_quantity_mt = VALUES(urea_sale_quantity_mt),
                urea_sale_amount_lakh_rs = VALUES(urea_sale_amount_lakh_rs),
                dap_sale_quantity_mt = VALUES(dap_sale_quantity_mt),
                dap_sale_amount_lakh_rs = VALUES(dap_sale_amount_lakh_rs),
                npk_sale_quantity_mt = VALUES(npk_sale_quantity_mt),
                npk_sale_amount_lakh_rs = VALUES(npk_sale_amount_lakh_rs),
                total_sale_quantity_mt = VALUES(total_sale_quantity_mt),
                total_sale_amount_lakh_rs = VALUES(total_sale_amount_lakh_rs),
                bank_deposit_amount_lakh_rs = VALUES(bank_deposit_amount_lakh_rs),
                rtgs_iffco_amount_lakh_rs = VALUES(rtgs_iffco_amount_lakh_rs),
                rtgs_kribhco_amount_lakh_rs = VALUES(rtgs_kribhco_amount_lakh_rs),
                rtgs_pcf_amount_lakh_rs = VALUES(rtgs_pcf_amount_lakh_rs),
                rtgs_total_amount_lakh_rs = VALUES(rtgs_total_amount_lakh_rs),
                dm_allocated_phosphatic_centers = VALUES(dm_allocated_phosphatic_centers),
                pcf_dispatch_rtgs_quantity_mt = VALUES(pcf_dispatch_rtgs_quantity_mt),
                pcf_dispatch_rtgs_amount_lakh_rs = VALUES(pcf_dispatch_rtgs_amount_lakh_rs),
                pcf_pending_quantity_mt = VALUES(pcf_pending_quantity_mt),
                pcf_pending_amount_lakh_rs = VALUES(pcf_pending_amount_lakh_rs)";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssiiiddddddddddddddiddd",
                $entry_date, $district_id, 
                $_POST['total_coop_centers_district'],
                $_POST['trucks_required_dispatch'],
                $_POST['phosphatic_centers_less_7_5mt'],
                $_POST['urea_sale_quantity_mt'],
                $_POST['urea_sale_amount_lakh_rs'],
                $_POST['dap_sale_quantity_mt'],
                $_POST['dap_sale_amount_lakh_rs'],
                $_POST['npk_sale_quantity_mt'],
                $_POST['npk_sale_amount_lakh_rs'],
                $_POST['total_sale_quantity_mt'],
                $_POST['total_sale_amount_lakh_rs'],
                $_POST['bank_deposit_amount_lakh_rs'],
                $_POST['rtgs_iffco_amount_lakh_rs'],
                $_POST['rtgs_kribhco_amount_lakh_rs'],
                $_POST['rtgs_pcf_amount_lakh_rs'],
                $_POST['rtgs_total_amount_lakh_rs'],
                $_POST['dm_allocated_phosphatic_centers'],
                $_POST['pcf_dispatch_rtgs_quantity_mt'],
                $_POST['pcf_dispatch_rtgs_amount_lakh_rs'],
                $_POST['pcf_pending_quantity_mt'],
                $_POST['pcf_pending_amount_lakh_rs']
            );
            
            if (mysqli_stmt_execute($stmt)) {
                $response['success'] = true;
                $response['message'] = 'Entry saved successfully';
            } else {
                throw new Exception(mysqli_error($conn));
            }
        } catch (Exception $e) {
            $response['message'] = 'Error saving entry';
            $response['debug'] = $e->getMessage();
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// --- END ACTION HANDLER (AJAX) ---

// --- START PAGE-SPECIFIC DATA PREP ---

// Re-check authentication in case session expired before AJAX call
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

// Re-populating variables just in case (this section is redundant from the top but ensures page load works)
$user_role = strtolower(trim($_SESSION['role']));
$user_id = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? '');
$user_districts = array();
if (!empty($user_id)) {
    $sql_ud = "SELECT district_id FROM user_district WHERE user_id = ? AND status = 1";
    $stmt_ud = mysqli_prepare($conn, $sql_ud);
    if ($stmt_ud) {
        mysqli_stmt_bind_param($stmt_ud, "s", $user_id);
        mysqli_stmt_execute($stmt_ud);
        $res_ud = mysqli_stmt_get_result($stmt_ud);
        if ($res_ud) {
            while ($r = mysqli_fetch_assoc($res_ud)) {
                $user_districts[] = $r['district_id'];
            }
        }
    }
}
if (empty($user_districts) && !empty($_SESSION['district'])) {
    $user_districts[] = $_SESSION['district'];
}
$user_district = count($user_districts) === 1 ? $user_districts[0] : '';

// Role check for page access
$allowed_roles = array('sadmin', 'admin', 'report', 'AR', 'ar');

$role_found_strict = in_array($user_role, $allowed_roles, true);
$role_found_loose = in_array($user_role, $allowed_roles, false);

if (!$role_found_loose) {
    die('<div style="text-align:center; padding:50px; color:red;">
            <h3>Access Denied</h3>
            <p>आपके पास इस पेज तक पहुंचने की अनुमति नहीं है</p>
            <div style="margin:20px; text-align:left; background:#f8f8f8; padding:15px; border-radius:5px;">
                <small style="color: #666;">
                    Debug Info:<br>
                    Role: [' . htmlspecialchars($user_role) . ']<br>
                    Role Type: ' . gettype($user_role) . '<br>
                    Role Length: ' . strlen($user_role) . '<br>
                    Session ID: ' . session_id() . '<br>
                    Strict Check: ' . ($role_found_strict ? 'Pass' : 'Fail') . '<br>
                    Loose Check: ' . ($role_found_loose ? 'Pass' : 'Fail') . '<br>
                    Allowed Roles: ' . htmlspecialchars(implode(", ", $allowed_roles)) . '
                </small>
            </div>
            </div>');
}

$full_access_roles = array('sadmin', 'admin', 'report');

// Get all districts for dropdown
$districts = [];
$sql_districts = "SELECT district_id, district_name FROM district ORDER BY district_name";
$result_districts = mysqli_query($conn, $sql_districts);

if ($result_districts) {
    while ($row = mysqli_fetch_assoc($result_districts)) {
        $districts[] = $row;
    }
}

// Check if main table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'daily_fertilizer_entry'");
$table_exists = mysqli_num_rows($table_check) > 0;

// --- END PAGE-SPECIFIC DATA PREP ---
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PS Review Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        min-height: 100vh;
        color: #333;
        /* UPDATED BACKGROUND */
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        background-size: 400% 400%;
        overflow-x: hidden;
        animation: gradientBG 15s ease infinite;
    }
    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    body::before {
        content: '';
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        /* UPDATED RADIAL GRADIENT COLORS */
        background: radial-gradient(ellipse at top left, rgba(102, 126, 234, 0.2) 0%, transparent 50%),
                    radial-gradient(ellipse at bottom right, rgba(118, 75, 162, 0.2) 0%, transparent 50%);
        pointer-events: none;
        z-index: -1;
    }
    
    /* SIDEBAR REMOVED */

    .main-container {
        /* UPDATED MARGINS (NO SIDEBAR) */
        margin: 20px; 
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 30px;
        /* REMOVED TRANSITION */
    }
    /* REMOVED SIDEBAR:HOVER RULE */

    .form-section {
        margin-bottom: 25px;
        padding: 20px;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        background-color: #fff;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }
    .fieldset {
        border: 2px solid #FF8C00; /* <-- YEH ORANGE RAHEGA */
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        background-color: white;
        transition: all 0.3s ease;
        height: 100%; 
        text-align: center; 
    }
    .fieldset:hover {
        box-shadow: 0 5px 15px rgba(255,140,0,0.1); /* <-- YEH BHI ORANGE GLOW HAI */
    }
    .legend {
        font-weight: bold;
        color: #764ba2; /* <--- YEH BADAL DIYA (THEME PURPLE) */
        padding: 0 15px;
        background: white;
        font-size: 16px;
        width: auto; 
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #FF8C00; /* <-- YEH ORANGE RAHEGA */
        box-shadow: 0 0 0 0.2rem rgba(255,140,0,0.25); /* <-- YEH BHI ORANGE GLOW HAI */
    }
    
    .fieldset .row {
        text-align: left;
    }
    
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    
    /* --- START: BUTTON THEME UPDATE --- */
    .btn-theme-save {
        background-color: #667eea; /* <--- YEH BADAL DIYA (THEME BLUE) */
        border-color: #667eea; /* <--- YEH BADAL DIYA (THEME BLUE) */
        color: #ffffff;
        font-weight: 600;
    }
    .btn-theme-save:hover {
        background-color: #5a6fcf; /* <--- YEH BADAL DIYA (DARKER BLUE) */
        border-color: #5a6fcf; /* <--- YEH BADAL DIYA (DARKER BLUE) */
        color: #ffffff;
    }
    /* --- END: BUTTON THEME UPDATE --- */
    
    .header-title {
        background: linear-gradient(135deg, #667eea 0%, #764ba2); 
        color: white;
        padding: 25px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3); /* <-- Glow ko blue kar diya */
        position: relative;
        overflow: hidden;
    }
    .header-title::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .header-title h2 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 15px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        position: relative;
    }
    
    /* --- START: RESPONSIVE BUTTON FIX --- */
    .btn-back-dashboard {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 10;
        padding: 8px 15px;
    }
    @media (max-width: 576px) {
        .btn-back-dashboard {
            position: relative;
            top: auto;
            left: auto;
            display: block;
            width: 100%;
            margin-bottom: 15px;
            text-align: center;
        }
        .header-title h2 {
            font-size: 20px; /* Choti screen par title chota kar diya */
        }
    }
    /* --- END: RESPONSIVE BUTTON FIX --- */
    
    .debug-info {
        background: #e9ecef;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 12px;
    }
    .table-responsive {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-top: 20px;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead th {
        background: #667eea; /* <--- YEH BADAL DIYA (THEME BLUE) */
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
        text-align: center;
        font-size: 15px;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
    }
    .table tbody td {
        font-size: 14px;
        padding: 12px;
        vertical-align: middle;
    }
    .btn-action {
        padding: 8px 15px;
        font-size: 14px;
        border-radius: 6px;
        font-weight: 500;
    }
    .badge {
        letter-spacing: 0.5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table td {
        vertical-align: middle;
        border-color: #e9ecef;
        padding: 12px;
    }
    .btn-action {
        padding: 5px 10px;
        font-size: 0.875rem;
    }
    .badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 500;
    }
</style>
</head>
<body>
    
    <div class="container-fluid">
        <div class="main-container">
            <div class="header-title">
                
                <a href="dashboard.php" class="btn btn-light btn-back-dashboard">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                
                <h2><i class="fas fa-chart-line"></i> प्रमुख सचिव महोदय, सहकारिता के प्रतिदिन 02 बजे अपरान्ह पर समीक्षा बिन्दु</h2>
                
                <div class="text-center mt-3">
                    <?php if ($user_role === 'ar'): ?>
                        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 15px;">
                            <span class="badge badge-info" style="font-size: 16px; padding: 10px 20px; font-weight: 600;">
                                <i class="fas fa-map-marker-alt"></i> District: <?php 
                                $district_names = [];
                                if (!empty($user_districts)) {
                                    $placeholders = str_repeat('?,', count($user_districts) - 1) . '?';
                                    $sql_dist = "SELECT district_name FROM district WHERE district_id IN ($placeholders)";
                                    $stmt_dist = mysqli_prepare($conn, $sql_dist);
                                    if ($stmt_dist) {
                                        $types = str_repeat('s', count($user_districts));
                                        $stmt_dist->bind_param($types, ...$user_districts);
                                        mysqli_stmt_execute($stmt_dist);
                                        $result_dist = mysqli_stmt_get_result($stmt_dist);
                                        while ($row_dist = mysqli_fetch_assoc($result_dist)) {
                                            $district_names[] = $row_dist['district_name'];
                                        }
                                    }
                                }
                                echo htmlspecialchars(implode(', ', $district_names) ?: 'No District Assigned'); 
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
            
            <?php if (!$table_exists): ?>
                <div class="alert alert-warning">
                    <strong>⚠️ Warning:</strong> The daily_fertilizer_entry table doesn't exist yet. 
                    Please run: <a href="create_table.php">create_table.php</a>
                </div>
            <?php endif; ?>
            
            <form id="ps_review_form">
                <div class="form-section">
                    <h5><i class="fas fa-calendar"></i> Date & District Selection</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="entry_date"><strong>दिनांक (Date)</strong></label>
                            <input type="date" id="entry_date" name="entry_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="district_id"><strong>जनपद (District)</strong></label>
                            <?php if (in_array($user_role, $full_access_roles)): ?>
                                <select id="district_id" name="district_id" class="form-control">
                                    <option value="">जनपद चुनें</option>
                                    <?php foreach ($districts as $district): ?>
                                        <option value="<?php echo htmlspecialchars($district['district_id']); ?>">
                                            <?php echo htmlspecialchars($district['district_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <?php if (count($user_districts) === 1): ?>
                                    <?php 
                                    $display_district = isset($district_names_map[$user_districts[0]]) ? 
                                        $district_names_map[$user_districts[0]] : 
                                        'Unknown District';
                                    ?>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($display_district); ?>" readonly>
                                    <input type="hidden" id="district_id" name="district_id" value="<?php echo htmlspecialchars($user_districts[0]); ?>">
                                <?php else: ?>
                                    <select id="district_id" name="district_id" class="form-control">
                                        <option value="">जनपद चुनें</option>
                                        <?php foreach ($districts as $district): ?>
                                            <?php if (in_array($district['district_id'], $user_districts)): ?>
                                                <option value="<?php echo htmlspecialchars($district['district_id']); ?>">
                                                    <?php echo htmlspecialchars($district['district_name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h5><i class="fas fa-info-circle"></i> General Information</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="total_coop_centers_district">जनपद में सहकारिता क्षेत्र के कुल उर्वरक सहकारी बिक्री केन्द्रों की संख्या</label>
                            <input type="number" id="total_coop_centers_district" name="total_coop_centers_district" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="trucks_required_dispatch">उर्वरक प्रेषण हेतु ट्रकों की आवश्यक्ता</label>
                            <input type="number" id="trucks_required_dispatch" name="trucks_required_dispatch" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="phosphatic_centers_less_7_5mt">7.5 MT से कम फास्फेटिक उर्वरक सहकारी बिक्री केन्द्रों की संख्या</label>
                            <input type="number" id="phosphatic_centers_less_7_5mt" name="phosphatic_centers_less_7_5mt" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="fieldset">
                            <legend class="legend">Urea Sale</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="urea_sale_quantity_mt">मात्रा MT में</label>
                                    <input type="number" id="urea_sale_quantity_mt" name="urea_sale_quantity_mt" class="form-control" step="0.01" onchange="calculateTotals()">
                                </div>
                                <div class="col-md-6">
                                    <label for="urea_sale_amount_lakh_rs">धनराशि लाख रु० में</label>
                                    <input type="number" id="urea_sale_amount_lakh_rs" name="urea_sale_amount_lakh_rs" class="form-control" step="0.01" onchange="calculateTotals()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fieldset">
                            <legend class="legend">DAP Sale</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="dap_sale_quantity_mt">मात्रा MT में</label>
                                    <input type="number" id="dap_sale_quantity_mt" name="dap_sale_quantity_mt" class="form-control" step="0.01" onchange="calculateTotals()">
                                </div>
                                <div class="col-md-6">
                                    <label for="dap_sale_amount_lakh_rs">धनराशि लाख रु० में</label>
                                    <input type="number" id="dap_sale_amount_lakh_rs" name="dap_sale_amount_lakh_rs" class="form-control" step="0.01" onchange="calculateTotals()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fieldset">
                            <legend class="legend">NPK Sale</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="npk_sale_quantity_mt">मात्रा MT में</label>
                                    <input type="number" id="npk_sale_quantity_mt" name="npk_sale_quantity_mt" class="form-control" step="0.01" onchange="calculateTotals()">
                                </div>
                                <div class="col-md-6">
                                    <label for="npk_sale_amount_lakh_rs">धनराशि लाख रु० में</label>
                                    <input type="number" id="npk_sale_amount_lakh_rs" name="npk_sale_amount_lakh_rs" class="form-control" step="0.01" onchange="calculateTotals()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fieldset">
                            <legend class="legend">Total</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="total_sale_quantity_mt">मात्रा MT में</label>
                                    <input type="number" id="total_sale_quantity_mt" name="total_sale_quantity_mt" class="form-control" step="0.01" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="total_sale_amount_lakh_rs">धनराशि लाख रु० में</label>
                                    <input type="number" id="total_sale_amount_lakh_rs" name="total_sale_amount_lakh_rs" class="form-control" step="0.01" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="fieldset">
                                <legend class="legend">बिक्री केन्द्रों द्वारा बैंक में जमा धनराशि लाख में</legend>
                                <input type="number" id="bank_deposit_amount_lakh_rs" name="bank_deposit_amount_lakh_rs" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="fieldset">
                                <legend class="legend">बैंक द्वारा प्रदायकर्ताओं को RTGS की गयी धनराशि लाख में</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="rtgs_iffco_amount_lakh_rs">IFFCO</label>
                                        <input type="number" id="rtgs_iffco_amount_lakh_rs" name="rtgs_iffco_amount_lakh_rs" class="form-control" step="0.01" onchange="calculateRTGSTotal()">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="rtgs_kribhco_amount_lakh_rs">KRIBHCO</label>
                                        <input type="number" id="rtgs_kribhco_amount_lakh_rs" name="rtgs_kribhco_amount_lakh_rs" class="form-control" step="0.01" onchange="calculateRTGSTotal()">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="rtgs_pcf_amount_lakh_rs">PCF</label>
                                        <input type="number" id="rtgs_pcf_amount_lakh_rs" name="rtgs_pcf_amount_lakh_rs" class="form-control" step="0.01" onchange="calculateRTGSTotal()">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="rtgs_total_amount_lakh_rs">योग</label>
                                        <input type="number" id="rtgs_total_amount_lakh_rs" name="rtgs_total_amount_lakh_rs" class="form-control" step="0.01" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="fieldset">
                                <legend class="legend">जिलाधिकारी द्वारा फास्फेटिक उर्वरक आवंटित उर्वरक बिक्री केन्द्रों की संख्या</legend>
                                <input type="number" id="dm_allocated_phosphatic_centers" name="dm_allocated_phosphatic_centers" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fieldset">
                                <legend class="legend">RTGS के सापेक्ष PCF द्वारा प्रेषण</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="pcf_dispatch_rtgs_quantity_mt">मात्रा MT में</label>
                                        <input type="number" id="pcf_dispatch_rtgs_quantity_mt" name="pcf_dispatch_rtgs_quantity_mt" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pcf_dispatch_rtgs_amount_lakh_rs">धनराशि लाख रु० में</label>
                                        <input type="number" id="pcf_dispatch_rtgs_amount_lakh_rs" name="pcf_dispatch_rtgs_amount_lakh_rs" class="form-control" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fieldset">
                                <legend class="legend">PCF स्तर पर Pending</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="pcf_pending_quantity_mt">मात्रा MT में</label>
                                        <input type="number" id="pcf_pending_quantity_mt" name="pcf_pending_quantity_mt" class="form-control" step="0.01">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pcf_pending_amount_lakh_rs">धनराशि लाख रु० में</label>
                                        <input type="number" id="pcf_pending_amount_lakh_rs" name="pcf_pending_amount_lakh_rs" class="form-control" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-section text-center">
                    <button type="button" class="btn btn-theme-save btn-lg" onclick="saveEntry()">
                        <i class="fas fa-save"></i> Save Entry
                    </button>
                    <?php if (in_array($user_role, $full_access_roles)): ?>
                    <a href="ps_review_report_hindi.php" class="btn btn-info btn-lg ml-3">
                        <i class="fas fa-chart-line"></i> रिपोर्ट देखें
                    </a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="form-section mt-4">
                <h5 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 20px;"><i class="fas fa-table"></i> Current Entries</h5>
                <div id="district_entries_table" class="table-responsive">
                    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css"/>
                    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css"/>
                    <?php
                    $current_date = date('Y-m-d');
                    $sql = "SELECT d.*, dd.district_name 
                            FROM daily_fertilizer_entry d 
                            JOIN district dd ON d.district_id = dd.district_id 
                            WHERE entry_date = ?";

                    $bind_params = array();
                    $bind_types = '';
                    $bind_params[] = $current_date;
                    $bind_types .= 's';

                    if (!in_array($user_role, ['sadmin', 'admin']) && !empty($user_districts)) {
                        if (count($user_districts) === 1) {
                            $sql .= " AND d.district_id = ?";
                            $bind_params[] = $user_districts[0];
                            $bind_types .= 's';
                        } else {
                            $placeholders = implode(',', array_fill(0, count($user_districts), '?'));
                            $sql .= " AND d.district_id IN ($placeholders)";
                            foreach ($user_districts as $ud) {
                                $bind_params[] = $ud;
                                $bind_types .= 's';
                            }
                        }
                    }

                    $stmt = mysqli_prepare($conn, $sql);
                    if ($stmt) {
                        if (!empty($bind_params)) {
                            $refs = array();
                            $refs[] = & $bind_types;
                            for ($i = 0; $i < count($bind_params); $i++) {
                                $refs[] = & $bind_params[$i];
                            }
                            call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $refs));
                        }
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                    } else {
                        $result = false;
                    }
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        echo '<table id="entriesTable" class="table table-striped table-bordered display responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>दिनांक</th>
                                    <th>जनपद</th>
                                    <th>कुल केन्द्र</th>
                                    <th>ट्रक आवश्यक</th>
                                    <th>Urea बिक्री (MT)</th>
                                    <th>Urea राशि (लाख)</th>
                                    <th>DAP बिक्री (MT)</th>
                                    <th>DAP राशि (लाख)</th>
                                    <th>NPK बिक्री (MT)</th>
                                    <th>NPK राशि (लाख)</th>
                                    <th>कुल बिक्री (MT)</th>
                                    <th>कुल राशि (लाख)</th>
                                    <th>बैंक जमा (लाख)</th>
                                    <th>RTGS कुल (लाख)</th>
                                    <th>DM आवंटित केन्द्र</th>
                                    <th>PCF प्रेषण (MT)</th>
                                    <th>PCF लंबित (MT)</th>
                                    <th>कार्य</th>
                                </tr>
                            </thead>
                            <tbody>';
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            $today = date('Y-m-d');
                            $isEditable = ($row['entry_date'] == $today);
                            
                            echo '<tr>
                                <td>' . date('d-m-Y', strtotime($row['entry_date'])) . '</td>
                                <td>' . htmlspecialchars($row['district_name']) . '</td>
                                <td>' . number_format($row['total_coop_centers_district']) . '</td>
                                <td>' . number_format($row['trucks_required_dispatch']) . '</td>
                                <td>' . number_format($row['urea_sale_quantity_mt'], 2) . '</td>
                                <td>' . number_format($row['urea_sale_amount_lakh_rs'], 2) . '</td>
                                <td>' . number_format($row['dap_sale_quantity_mt'], 2) . '</td>
                                <td>' . number_format($row['dap_sale_amount_lakh_rs'], 2) . '</td>
                                <td>' . number_format($row['npk_sale_quantity_mt'], 2) . '</td>
                                <td>' . number_format($row['npk_sale_amount_lakh_rs'], 2) . '</td>
                                <td>' . number_format($row['total_sale_quantity_mt'], 2) . '</td>
                                <td>' . number_format($row['total_sale_amount_lakh_rs'], 2) . '</td>
                                <td>' . number_format($row['bank_deposit_amount_lakh_rs'], 2) . '</td>
                                <td>' . number_format($row['rtgs_total_amount_lakh_rs'], 2) . '</td>
                                <td>' . number_format($row['dm_allocated_phosphatic_centers']) . '</td>
                                <td>' . number_format($row['pcf_dispatch_rtgs_quantity_mt'], 2) . '</td>
                                <td>' . number_format($row['pcf_pending_quantity_mt'], 2) . '</td>
                                <td>' .
                                    ($isEditable ? '
                                    <button type="button" class="btn btn-primary" style="font-size: 14px; padding: 8px 15px;" onclick="editEntry(\'' . $row['entry_date'] . '\', \'' . $row['district_id'] . '\')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger ml-2" style="font-size: 14px; padding: 8px 15px;" onclick="deleteEntry(\'' . $row['entry_date'] . '\', \'' . $row['district_id'] . '\')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>' : '<span class="badge badge-secondary" style="font-size: 14px; padding: 8px 15px;">Not Editable</span>') . '
                                </td>
                            </tr>';
                        }
                        
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="alert alert-info">आज की कोई Entry नहीं मिली</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script type_text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    <script>
        function calculateTotals() {
            const ureaQty = parseFloat(document.getElementById('urea_sale_quantity_mt').value) || 0;
            const ureaAmt = parseFloat(document.getElementById('urea_sale_amount_lakh_rs').value) || 0;
            const dapQty = parseFloat(document.getElementById('dap_sale_quantity_mt').value) || 0;
            const dapAmt = parseFloat(document.getElementById('dap_sale_amount_lakh_rs').value) || 0;
            const npkQty = parseFloat(document.getElementById('npk_sale_quantity_mt').value) || 0;
            const npkAmt = parseFloat(document.getElementById('npk_sale_amount_lakh_rs').value) || 0;
            
            const totalQty = ureaQty + dapQty + npkQty;
            const totalAmt = ureaAmt + dapAmt + npkAmt;
            
            document.getElementById('total_sale_quantity_mt').value = totalQty.toFixed(2);
            document.getElementById('total_sale_amount_lakh_rs').value = totalAmt.toFixed(2);
        }
        
        function calculateRTGSTotal() {
            const iffco = parseFloat(document.getElementById('rtgs_iffco_amount_lakh_rs').value) || 0;
            const kribhco = parseFloat(document.getElementById('rtgs_kribhco_amount_lakh_rs').value) || 0;
            const pcf = parseFloat(document.getElementById('rtgs_pcf_amount_lakh_rs').value) || 0;
            
            const total = iffco + kribhco + pcf;
            document.getElementById('rtgs_total_amount_lakh_rs').value = total.toFixed(2);
        }
        
        function checkEntry() {
            const entryDate = document.getElementById('entry_date').value;
            const districtId = document.getElementById('district_id').value;
            
            if (!entryDate || !districtId) {
                alert('Please select both date and district');
                return;
            }
                fetch(`ps_review_standalone.php?action=check_entry&date=${entryDate}&district_id=${districtId}`)
                    .then(r=>r.json())
                    .then(d=>{
                        if (d.success && d.exists) alert('An entry for this date and district already exists.');
                        else if (d.success) alert('No entry found for selected date/district.');
                        else alert('Error checking entry');
                    })
                    .catch(e=>{ console.error(e); alert('Error checking entry'); });
        }
        
        function saveEntry() {
            const entryDate = document.getElementById('entry_date').value;
            const districtId = document.getElementById('district_id').value;
            
            if (!entryDate || !districtId) {
                alert('Please select both date and district');
                return;
            }
            
            const form = document.getElementById('ps_review_form');
            const formData = new FormData(form);
            formData.append('action', 'save_entry');
            
            const saveBtn = document.querySelector('button[onclick="saveEntry()"]');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;
            
            fetch('ps_review_standalone.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Entry saved successfully!');
                    form.reset();
                    location.reload(); 
                } else {
                    let errorMsg = 'Error: ' + data.message;
                    if (data.debug) {
                        errorMsg += '\nDebug: ' + data.debug;
                    }
                    alert(errorMsg);
                    console.error('Save Error:', data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the entry');
            })
            .finally(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            });
        }
        
        function editEntry(date, districtId) {
            document.getElementById('entry_date').value = date;
            document.getElementById('district_id').value = districtId;
            
            fetch(`ps_review_standalone.php?action=get_entry&date=${date}&district_id=${districtId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        for (const [key, value] of Object.entries(data.entry)) {
                            const input = document.getElementById(key);
                            if (input) input.value = value;
                        }
                        calculateTotals();
                        calculateRTGSTotal();
                        window.scrollTo(0, 0); // Scroll to top to see the form
                    } else {
                        alert('Error fetching entry data: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching the entry data');
                });
        }
        
        function deleteEntry(date, districtId) {
            if (!confirm('क्या आप वाकई इस Entry को हटाना चाहते हैं?')) return;
            
            const formData = new FormData();
            formData.append('action', 'delete_entry');
            formData.append('entry_date', date);
            formData.append('district_id', districtId);
            
            fetch('ps_review_standalone.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Entry सफलतापूर्वक हटा दी गई');
                    location.reload();
                } else {
                    alert('Error deleting entry: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the entry');
            });
        }

        function checkExistingEntry(districtId) {
            const today = new Date().toISOString().split('T')[0];
            
            fetch(`ps_review_standalone.php?action=check_entry&date=${today}&district_id=${districtId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        alert('आज की Entry पहले से मौजूद है। आप इसे संपादित कर सकते हैं।');
                        return false;
                    }
                    return true;
                })
                .catch(error => {
                    console.error('Error:', error);
                    return false;
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('PS Review Form loaded successfully');
            calculateTotals();
            calculateRTGSTotal();

            $('#entriesTable').DataTable({
                responsive: true,
                language: {
                    "search": "खोजें:",
                    "lengthMenu": "_MENU_ Entries",
                    "info": "_TOTAL_ में से _START_ से _END_ Entries",
                    "infoEmpty": "कोई Entry नहीं मिली",
                    "paginate": {
                        "first": "प्रथम",
                        "last": "अंतिम",
                        "next": "अगला",
                        "previous": "पिछला"
                    }
                },
                order: [[0, 'desc']],
                pageLength: 25
            });

            // Automatically load entry if user is not admin and has one district
            const districtSelect = document.getElementById('district_id');
            if (districtSelect.type === 'hidden' && districtSelect.value) {
                const today = document.getElementById('entry_date').value;
                editEntry(today, districtSelect.value);
            }

            // Add change listener to district select for admins
            $('#district_id').on('change', function() {
                const districtId = $(this).val();
                if (districtId) {
                    const today = document.getElementById('entry_date').value;
                    editEntry(today, districtId);
                }
            });
        });
    </script>
</body>
</html>