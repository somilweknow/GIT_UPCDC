<?php
include("scripts/settings.php");
$msg = '';
if (!isset($_GET['id'])) {
    die("Invalid");
}
$id = intval($_GET['id']);
if (isset($_POST['approve'])) {
    execute_query("UPDATE bpacs_progress SET approval_status=1,approved_by='{$_SESSION['username']}',approved_at=NOW() WHERE id='$id'");
    $msg = "<div class='alert alert-success'>Approved Successfully</div>";
}
if (isset($_POST['reject'])) {
    execute_query("UPDATE bpacs_progress SET approval_status=2,approved_by='{$_SESSION['username']}',approved_at=NOW() WHERE id='$id'");
    $msg = "<div class='alert alert-danger'>Rejected</div>";
}
$res = execute_query("SELECT bp.*,md.division_name,mdt.district_name FROM bpacs_progress bp LEFT JOIN master_division md ON md.sno=bp.mandal_id LEFT JOIN master_district mdt ON mdt.sno=bp.district_id WHERE bp.id='$id'");
$row = mysqli_fetch_assoc($res);

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

    .alert-danger {
        background-color: #fed7d7;
        border: 2px solid #fc8181;
        color: #742a2a;
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

    /* Approve/Reject section */
    .approve-reject-box {
        background: #f7fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 30px;
        margin-top: 30px;
        text-align: center;
    }

    .approve-reject-box h4 {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
    }

    .btn-approve {
        background: linear-gradient(135deg, #38a169, #2f855a);
        color: white;
        border: none;
        padding: 14px 40px;
        font-size: 18px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 15px;
        box-shadow: 0 4px 15px rgba(56, 161, 105, 0.4);
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(56, 161, 105, 0.5);
    }

    .btn-reject {
        background: linear-gradient(135deg, #e53e3e, #c53030);
        color: white;
        border: none;
        padding: 14px 40px;
        font-size: 18px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(229, 62, 62, 0.4);
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(229, 62, 62, 0.5);
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

        .btn-approve,
        .btn-reject {
            padding: 12px 25px;
            font-size: 16px;
            margin-bottom: 10px;
        }
    }
    /* Filter Box */
    .filter-box {
        background: #eef2ff;
        border: 2px solid #c7d2fe;
        border-radius: 12px;
        padding: 25px 30px;
        margin-bottom: 25px;
    }

    .btn-go {
        background: linear-gradient(135deg, #f6ad55, #ed8936);
        color: white;
        border: none;
        padding: 13px 28px;
        font-size: 17px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        box-shadow: 0 4px 15px rgba(237, 137, 54, 0.4);
        display: block;
        margin-top: 32px;
    }

    .btn-go:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(237, 137, 54, 0.5);
    }

    @media (max-width: 768px) {
        .btn-go {
            margin-top: 15px;
        }
        .filter-box {
            padding: 18px;
        }
    }
</style>

<script>
// When division changes, fetch matching districts via AJAX
document.getElementById('filter_division').addEventListener('change', function () {
    var divisionId = this.value;
    var districtSelect = document.getElementById('filter_district');

    districtSelect.innerHTML = '<option value="">-- लोड हो रहा है... --</option>';

    if (!divisionId) {
        districtSelect.innerHTML = '<option value="">-- पहले मण्डल चुनें --</option>';
        return;
    }

    fetch('get_districts.php?division_id=' + divisionId)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            districtSelect.innerHTML = '<option value="">-- जिला चयन करें --</option>';
            if (data.length === 0) {
                districtSelect.innerHTML = '<option value="">-- कोई जिला नहीं मिला --</option>';
                return;
            }
            data.forEach(function(d) {
                districtSelect.innerHTML += '<option value="' + d.sno + '">' + d.district_name + '</option>';
            });
        })
        .catch(function() {
            districtSelect.innerHTML = '<option value="">-- त्रुटि हुई, पुनः प्रयास करें --</option>';
        });
});

// Redirect to pacs_info_dr.php with selected district id
function goToForm() {
    var districtId = document.getElementById('filter_district').value;
    if (!districtId) {
        alert('कृपया पहले जिला चुनें।');
        return;
    }
    window.location.href = 'pacs_info_dr.php?id=' + districtId;
}
</script>

<div class="main-container">
    <div class="row">
        <div class="col-md-12">
            <div class="main-box">

                <div class="title">जिले कि प्रगति सुचना</div>

                <?= $msg ?>

                <!-- ===================== VIEW FORM (Read-Only Record Details) ===================== -->
                <div class="section-title">मूल विवरण</div>

                <!-- Division → District Filter + Redirect -->
                <div class="filter-box">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>मण्डल चुनें</label>
                                <select id="filter_division" class="form-control">
                                    <option value="">-- मण्डल चयन करें --</option>
                                    <?php
                                    $div_list = execute_query("SELECT sno, division_name FROM master_division ORDER BY division_name");
                                    while ($dv = mysqli_fetch_assoc($div_list)) {
                                        $sel = ($dv['sno'] == $row['mandal_id']) ? 'selected' : '';
                                        echo "<option value='{$dv['sno']}' {$sel}>" . htmlspecialchars($dv['division_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>जिला चुनें</label>
                                <select id="filter_district" class="form-control">
                                    <option value="">-- पहले मण्डल चुनें --</option>
                                    <?php
                                    // Pre-load districts of current record's division so page loads correctly
                                    if (!empty($row['mandal_id'])) {
                                        $pre_dist = execute_query("SELECT sno, district_name FROM master_district WHERE division_id='{$row['mandal_id']}' ORDER BY district_name");
                                        while ($pd = mysqli_fetch_assoc($pre_dist)) {
                                            $sel = ($pd['sno'] == $row['district_id']) ? 'selected' : '';
                                            echo "<option value='{$pd['sno']}' {$sel}>" . htmlspecialchars($pd['district_name']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>&nbsp;</label>
                                <button type="button" class="btn-go" id="btn_go_form" onclick="goToForm()">
                                    📋 जिला फॉर्म खोलें
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row row-gap" style="margin-top:20px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>मण्डल</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['division_name'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>जिला</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['district_name'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>कुल बी-पैक्स</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['total_bpacs'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="row row-gap">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>नवगठित बी-पैक्स</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['new_bpacs'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>सीसी लिमिट (10 लाख)</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['cc_limit'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>उर्वरक पैक्स</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['fertilizer'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="row row-gap">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>10 % मार्जिन मनी जमा करने वाली पैक्स</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['fd'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>मरम्मत/सुदृढ़ीकरण</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['repair'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>RKVP/SADP</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['rkvp'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="row row-gap">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>उन्नयन धनराशि</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['upgrade_amt'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>समीक्षा</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['review_status'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>लंबित समीक्षा</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['pending_review'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="section-title">फसली ऋण</div>

                <div class="row row-gap">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>वितरित धनराशि</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['loan_distributed'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>वसूली</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['loan_recovery'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="section-title">सोलर रूफटॉप (बी-पैक्स स०)</div>

                <div class="row row-gap">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>पूर्व में हो चुका है</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['solar'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>मार्च 2026 में होना है</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['solar_26'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="section-title">अन्य विवरण</div>

                <div class="row row-gap">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>कंप्यूटर ऑपरेटर सहकर सारथी कि संख्या</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['operator'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ग्रेडिंग (A, B, C, D)</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['grading'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="row row-gap">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>गेहूं केन्द्र</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['wheat_center'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>दलहन/तिलहन केन्द्र</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($row['pulse_center'] ?? '') ?>" readonly style="background-color:#e2e8f0;">
                        </div>
                    </div>
                </div>

                <div class="section-title">चयनित समितियाँ</div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>चयनित समितियों का नाम</label>
                            <textarea class="form-control" readonly style="background-color:#e2e8f0;"><?= htmlspecialchars($row['grain_society_name'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- ===================== APPROVE / REJECT FORM ===================== -->
                <div class="approve-reject-box">
                    <h4>अनुमोदन / अस्वीकृति</h4>
                    <form method="post" action="?id=<?= $id ?>">
                        <button type="submit" name="approve" class="btn-approve"
                            onclick="return confirm('क्या आप इस रिकॉर्ड को Approve करना चाहते हैं?')">
                            ✔ Approve
                        </button>
                        <button type="submit" name="reject" class="btn-reject"
                            onclick="return confirm('क्या आप इस रिकॉर्ड को Reject करना चाहते हैं?')">
                            ✘ Reject
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
