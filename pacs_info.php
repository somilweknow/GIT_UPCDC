<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// print_r($_SESSION);
$msg = '';
$edit_data = null;
$edit_mode = false;

// Handle Delete (Soft Delete)
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    execute_query("UPDATE bpacs_progress SET is_deleted = 1 WHERE id = '{$delete_id}'");
    $msg = '<div class="alert alert-success">रिकॉर्ड सफलतापूर्वक हटाया गया</div>';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit - Load data
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_result = execute_query("SELECT * FROM bpacs_progress WHERE id = '{$edit_id}' AND is_deleted = 0");
    if ($edit_result && mysqli_num_rows($edit_result) > 0) {
        $edit_data = mysqli_fetch_assoc($edit_result);
        $edit_mode = true;
    }
}

// Handle Update
if (isset($_POST['update']) && isset($_POST['record_id'])) {
    $record_id = intval($_POST['record_id']);
    execute_query("UPDATE bpacs_progress SET 
        mandal_id='{$_POST['mandal_id']}',
        district_id='{$_POST['district_id']}',
        total_bpacs='{$_POST['total_bpacs']}',
        new_bpacs='{$_POST['new_bpacs']}',
        cc_limit='{$_POST['cc_limit']}',
        fertilizer='{$_POST['fertilizer']}',
        fd='{$_POST['fd']}',
        repair='{$_POST['repair']}',
        rkvp='{$_POST['rkvp']}',
        upgrade_amt='{$_POST['upgrade']}',
        review_status='{$_POST['review_status']}',
        pending_review='{$_POST['pending_review']}',
        grain_society_name='{$_POST['grain_society_name']}',
        loan_distributed='{$_POST['loan_distributed']}',
        loan_recovery='{$_POST['loan_recovery']}',
        solar='{$_POST['solar']}',
        solar_26='{$_POST['solar_26']}',
        operator='{$_POST['operator']}',
        grading='{$_POST['grading']}',
        wheat_center='{$_POST['wheat_center']}',
        pulse_center='{$_POST['pulse_center']}'
        WHERE id = '{$record_id}'");
    $msg = '<div class="alert alert-success">रिकॉर्ड सफलतापूर्वक अपडेट किया गया</div>';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle Insert
if (isset($_POST['save'])) {
    execute_query("INSERT INTO bpacs_progress(mandal_id,district_id,total_bpacs,new_bpacs,cc_limit,fertilizer,fd,repair,rkvp,upgrade_amt,review_status,pending_review,grain_society_name,loan_distributed,loan_recovery,solar, solar_26,operator,grading,wheat_center,pulse_center,is_deleted) VALUES('{$_POST['mandal_id']}','{$_POST['district_id']}','{$_POST['total_bpacs']}','{$_POST['new_bpacs']}','{$_POST['cc_limit']}','{$_POST['fertilizer']}','{$_POST['fd']}','{$_POST['repair']}','{$_POST['rkvp']}','{$_POST['upgrade']}','{$_POST['review_status']}','{$_POST['pending_review']}','{$_POST['grain_society_name']}','{$_POST['loan_distributed']}','{$_POST['loan_recovery']}','{$_POST['solar']}', '{$_POST['solar_26']}','{$_POST['operator']}','{$_POST['grading']}','{$_POST['wheat_center']}','{$_POST['pulse_center']}',0)");
    $msg = '<div class="alert alert-success">रिकॉर्ड सफलतापूर्वक सहेजा गया</div>';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$div = execute_query("SELECT sno,division_name FROM master_division ORDER BY division_name");
$dist = execute_query("SELECT sno,district_name FROM master_district ORDER BY district_name");

// Get AR user's division and district from session
$selected_division = '';
$selected_district = '';
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'ar') {
    if (isset($_SESSION['division_id']) && is_array($_SESSION['division_id']) && count($_SESSION['division_id']) > 0) {
        $selected_division = $_SESSION['division_id'][0];
    }
    if (isset($_SESSION['district_id']) && is_array($_SESSION['district_id']) && count($_SESSION['district_id']) > 0) {
        $selected_district = $_SESSION['district_id'][0];
    }
}

// Fetch existing records based on user permissions
$where_clause = 'WHERE bp.is_deleted = 0';
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'ar') {
    if ($selected_district != '') {
        $where_clause .= " AND bp.district_id = '{$selected_district}'";
    }
}

$records_query = "SELECT bp.*, 
                  md.division_name, 
                  mdt.district_name,
                  bp.id as record_id,
                  DATE_FORMAT(bp.created_at, '%d-%m-%Y') as created_date
                  FROM bpacs_progress bp
                  LEFT JOIN master_division md ON bp.mandal_id = md.sno
                  LEFT JOIN master_district mdt ON bp.district_id = mdt.sno
                  {$where_clause}
                  ORDER BY bp.mandal_id, bp.district_id DESC";
$records = execute_query($records_query);

// Reset query pointers for form dropdowns
$div = execute_query("SELECT sno,division_name FROM master_division ORDER BY division_name");
$dist = execute_query("SELECT sno,district_name FROM master_district ORDER BY district_name");

page_header_start();
page_header_end();
page_sidebar();
?>

<style>
    * {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 15px 10px;
    }

    .main-box {
        background: #ffffff;
        padding: 45px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        max-width: 1600px;
        margin: 0 auto 30px;
    }

    .title {
        font-size: 26px;
        font-weight: 800;
        text-align: center;
        margin-bottom: 35px;
        color: #2d3748;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 4px solid #667eea;
        padding-bottom: 20px;
    }

    .section-title {
        font-size: 22px;
        font-weight: 700;
        margin: 35px 0 20px;
        color: #2d3748;
        background: linear-gradient(to right, #667eea, #764ba2);
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 10px;
        display: block;
        color: #4a5568;
    }

    .form-control {
        height: 52px;
        font-size: 17px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        padding: 12px 18px;
        transition: all 0.3s ease;
        background-color: #f7fafc;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background-color: #ffffff;
        outline: none;
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234a5568' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }

    textarea.form-control {
        height: 120px;
        resize: vertical;
        font-family: inherit;
    }

    .row-gap {
        margin-bottom: 0;
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 20px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.5);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .btn-cancel {
        background: #718096;
        color: white;
        border: none;
        padding: 16px 40px;
        font-size: 18px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-right: 15px;
    }

    .btn-cancel:hover {
        background: #4a5568;
        color: white;
        text-decoration: none;
    }

    .alert {
        font-size: 18px;
        padding: 18px 25px;
        border-radius: 8px;
        margin-bottom: 25px;
        font-weight: 600;
    }

    .alert-success {
        background-color: #c6f6d5;
        border: 2px solid #48bb78;
        color: #22543d;
    }

    .required::after {
        content: " *";
        color: #e53e3e;
        font-weight: 700;
    }

    /* Report Table Styles */
    .report-table-container {
        overflow-x: auto;
        margin-top: 25px;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .report-table thead {
        background: linear-gradient(to right, #667eea, #764ba2);
        color: white;
    }

    .report-table thead th {
        padding: 15px 12px;
        text-align: left;
        font-weight: 700;
        font-size: 15px;
        border-bottom: 3px solid #5568d3;
        white-space: nowrap;
    }

    .report-table tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .report-table tbody tr:hover {
        background-color: #f7fafc;
        transform: scale(1.01);
    }

    .report-table tbody td {
        padding: 14px 12px;
        color: #2d3748;
        font-size: 15px;
    }

    .report-table tbody tr:last-child {
        border-bottom: none;
    }

    .action-btn {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
        margin-right: 8px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn-edit {
        background: #4299e1;
        color: white;
    }

    .btn-edit:hover {
        background: #3182ce;
        color: white;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: #fc8181;
        color: white;
    }

    .btn-delete:hover {
        background: #f56565;
        color: white;
        transform: translateY(-2px);
    }

    .no-records {
        text-align: center;
        padding: 40px;
        font-size: 18px;
        color: #718096;
        font-weight: 600;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-yes {
        background: #c6f6d5;
        color: #22543d;
    }

    .badge-no {
        background: #fed7d7;
        color: #742a2a;
    }

    @media (max-width: 768px) {
        .main-box {
            padding: 25px;
        }

        .title {
            font-size: 26px;
        }

        .section-title {
            font-size: 18px;
        }

        label {
            font-size: 15px;
        }

        .form-control {
            height: 46px;
            font-size: 15px;
        }

        .btn-submit {
            padding: 14px 40px;
            font-size: 17px;
        }

        .report-table {
            font-size: 13px;
        }

        .report-table thead th {
            padding: 10px 8px;
            font-size: 13px;
        }

        .report-table tbody td {
            padding: 10px 8px;
            font-size: 13px;
        }

        .action-btn {
            padding: 6px 12px;
            font-size: 12px;
            margin-bottom: 5px;
        }
    }
</style>

<div class="main-container">
    <div class="row">
        <div class="col-md-12">
            <div class="main-box">

                <div class="title"><?= $edit_mode ? 'जिले कि प्रगति सुचना' : 'जिले कि प्रगति सुचना' ?></div>
                <!-- <button type="button" class="btn btn-primary" onclick="window.location.href='b-pacs_rep.php'">
                    <i class="fas fa-save"></i> B-PACS Report
                </button> -->

                <?= $msg ?>

                <form method="post">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="record_id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="section-title">मूल विवरण</div>

                    <div class="row row-gap">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">मण्डल</label>
                                <select name="mandal_id" class="form-control" required <?= ($selected_division != '' && !$edit_mode) ? 'readonly style="pointer-events: none; background-color: #e2e8f0;"' : '' ?>>
                                    <option value="">-- चयन करें --</option>
                                    <?php while ($r = mysqli_fetch_assoc($div)) { ?>
                                        <option value="<?= $r['sno'] ?>" 
                                            <?= ($edit_mode && $edit_data['mandal_id'] == $r['sno']) ? 'selected' : '' ?>
                                            <?= (!$edit_mode && $selected_division == $r['sno']) ? 'selected' : '' ?>>
                                            <?= $r['division_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="required">जिला</label>
                                <select name="district_id" class="form-control" required <?= ($selected_district != '' && !$edit_mode) ? 'readonly style="pointer-events: none; background-color: #e2e8f0;"' : '' ?>>
                                    <option value="">-- चयन करें --</option>
                                    <?php while ($r = mysqli_fetch_assoc($dist)) { ?>
                                        <option value="<?= $r['sno'] ?>" 
                                            <?= ($edit_mode && $edit_data['district_id'] == $r['sno']) ? 'selected' : '' ?>
                                            <?= (!$edit_mode && $selected_district == $r['sno']) ? 'selected' : '' ?>>
                                            <?= $r['district_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>कुल बी-पैक्स</label>
                                <input type="text" name="total_bpacs" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['total_bpacs']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row row-gap">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>नवगठित बी-पैक्स</label>
                                <input type="text" name="new_bpacs" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['new_bpacs']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>सीसी लिमिट (10 लाख)</label>
                                <input type="text" name="cc_limit" class="form-control" placeholder="राशि दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['cc_limit']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>उर्वरक पैक्स</label>
                                <input type="text" name="fertilizer" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['fertilizer']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row row-gap">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>10 % मार्जिन मनी जमा करने वाली पैक्स</label>
                                <input type="text" name="fd" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['fd']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>मरम्मत/सुदृढ़ीकरण</label>
                                <input type="text" name="repair" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['repair']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>RKVP/SADP</label>
                                <input type="text" name="rkvp" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['rkvp']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row row-gap">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>उन्नयन धनराशि</label>
                                <input type="text" name="upgrade" class="form-control" placeholder="राशि दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['upgrade_amt']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>समीक्षा</label>
                                <select name="review_status" class="form-control">
                                    <option value="">-- चयन करें --</option>
                                    <option value="yes" <?= ($edit_mode && $edit_data['review_status'] == 'yes') ? 'selected' : '' ?>>हाँ</option>
                                    <option value="no" <?= ($edit_mode && $edit_data['review_status'] == 'no') ? 'selected' : '' ?>>नहीं</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>लंबित समीक्षा</label>
                                <input type="text" name="pending_review" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['pending_review']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="section-title">फसली ऋण</div>

                    <div class="row row-gap">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>वितरित धनराशि</label>
                                <input type="text" name="loan_distributed" class="form-control" placeholder="राशि दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['loan_distributed']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>वसूली</label>
                                <input type="text" name="loan_recovery" class="form-control" placeholder="राशि दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['loan_recovery']) : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="section-title">सोलर रूफटॉप (बी-पैक्स स०)</div>

                    <div class="row row-gap">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>पूर्व में हो चुका है</label>
                                <input type="text" name="solar" class="form-control" placeholder="राशि दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['solar']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>मार्च 2026 में होना है</label>
                                <input type="text" name="solar_26" class="form-control" placeholder="राशि दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['solar_26']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="section-title">अन्य विवरण</div>

                    <div class="row row-gap">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>कंप्यूटर ऑपरेटर सहकर सारथी कि संख्या</label>
                                <input type="text" name="operator" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['operator']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ग्रेडिंग (A, B, C, D)</label>
                                <input type="text" name="grading" class="form-control" placeholder="ग्रेड दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['grading']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row row-gap">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>गेहूं केन्द्र</label>
                                <input type="text" name="wheat_center" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['wheat_center']) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>दलहन/तिलहन केन्द्र</label>
                                <input type="text" name="pulse_center" class="form-control" placeholder="संख्या दर्ज करें" value="<?= $edit_mode ? htmlspecialchars($edit_data['pulse_center']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="section-title">चयनित समितियाँ</div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>चयनित समितियों का नाम</label>
                                <textarea name="grain_society_name" class="form-control" placeholder="चयनित समितियों के नाम यहां दर्ज करें..."><?= $edit_mode ? htmlspecialchars($edit_data['grain_society_name']) : '' ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <?php if ($edit_mode): ?>
                            <!-- <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn-cancel">reset</a> -->
                            <button type="submit" name="update" class="btn-submit">Update</button>
                        <?php else: ?>
                            <button type="submit" name="save" class="btn-submit">Submit</button>
                        <?php endif; ?>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<div class="row">
        <div class="col-md-12">
            <div class="main-box">
                <div class="title">प्रगति रिपोर्ट</div>
                
        <!-- <button type="button" class="btn btn-primary" onclick="window.location.href='pacs_info.php'">
                    <i class="fas fa-save"></i> B-PACS Form
                </button> -->
                <?php if ($records && mysqli_num_rows($records) > 0): ?>
                <div class="report-table-container">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>क्र.सं.</th>
                                <th>मण्डल</th>
                                <th>जिला</th>
                                <th>कुल बी-पैक्स</th>
                                <th>नवगठित</th>
                                <th>सीसी लिमिट</th>
                                <th>उर्वरक</th>
                                <th>FD</th>
                                <th>मरम्मत</th>
                                <th>RKVP</th>
                                <th>उन्नयन राशि</th>
                                <th>समीक्षा</th>
                                <th>लंबित</th>
                                <th>ऋण वितरण</th>
                                <th>वसूली</th>
                                <th>सोलर - पूर्व में हो चुका है</th>
                                <th>सोलर - मार्च 2026 में होना है</th>
                                <th>ऑपरेटर</th>
                                <th>ग्रेडिंग</th>
                                <th>गेहूं केंद्र</th>
                                <th>दलहन केंद्र</th>
                                <th>दिनांक</th>
                                <th>कार्यवाही</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $serial = 1;
                            while ($row = mysqli_fetch_assoc($records)): 
                            ?>
                            <tr>
                                <td><?= $serial++ ?></td>
                                <td><?= htmlspecialchars($row['division_name']) ?></td>
                                <td><?= htmlspecialchars($row['district_name']) ?></td>
                                <td><?= htmlspecialchars($row['total_bpacs']) ?></td>
                                <td><?= htmlspecialchars($row['new_bpacs']) ?></td>
                                <td><?= htmlspecialchars($row['cc_limit']) ?></td>
                                <td><?= htmlspecialchars($row['fertilizer']) ?></td>
                                <td><?= htmlspecialchars($row['fd']) ?></td>
                                <td><?= htmlspecialchars($row['repair']) ?></td>
                                <td><?= htmlspecialchars($row['rkvp']) ?></td>
                                <td><?= htmlspecialchars($row['upgrade_amt']) ?></td>
                                <td>
                                    <?php if ($row['review_status'] == 'yes'): ?>
                                        <span class="badge badge-yes">हाँ</span>
                                    <?php elseif ($row['review_status'] == 'no'): ?>
                                        <span class="badge badge-no">नहीं</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['pending_review']) ?></td>
                                <td><?= htmlspecialchars($row['loan_distributed']) ?></td>
                                <td><?= htmlspecialchars($row['loan_recovery']) ?></td>
                                <td><?= htmlspecialchars($row['solar']) ?></td>
                                <td><?= htmlspecialchars($row['solar_26']) ?></td>
                                <td><?= htmlspecialchars($row['operator']) ?></td>
                                <td><?= htmlspecialchars($row['grading']) ?></td>
                                <td><?= htmlspecialchars($row['wheat_center']) ?></td>
                                <td><?= htmlspecialchars($row['pulse_center']) ?></td>
                                <td><?= ($row['created_date']) ?></td>
                                <td style="white-space: nowrap;">
                                    <a href="?edit=<?= $row['record_id'] ?>" class="action-btn btn-edit">✏️ Edit</a>
                                    <a href="?delete=<?= $row['record_id'] ?>" class="action-btn btn-delete" onclick="return confirm('क्या आप इस रिकॉर्ड को हटाना चाहते हैं?')">🗑️ Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <!-- <div class="no-records">
                    📋 कोई रिकॉर्ड उपलब्ध नहीं है
                </div> -->
                <?php endif; ?>
            </div>
        </div>
    </div>
<script>
// Confirm before delete
document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        if (!confirm('क्या आप वाकई इस रिकॉर्ड को हटाना चाहते हैं?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php
page_footer_start();
page_footer_end();
?>