<?php
session_start();
include("../scripts/settings.php");
echo '<base href="../">';
page_header_start();
page_header_end();
page_sidebar();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) die("Invalid ID");

$sessionUserType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

$res = execute_query("SELECT * FROM ncd_cooperative_registrations WHERE id = $id");

if (mysqli_num_rows($res) == 0) die("Record not found");

$row = mysqli_fetch_assoc($res);

function e($value) {
    return $value;
}

unset($row['created_at'], $row['updated_at']);

$fieldConfig = [

    'cooperative_id' => [
        'type' => 'text',
        'readonly' => true
    ],
    'registration_number' => [
        'type' => 'text',
        'readonly' => true
    ],
    'full_address' => [
        'type' => 'textarea',
        'rows' => 3
    ],
    'registration_authoritie_id' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_registration_authorities',
        'value' => 'id',
        'label' => 'authority_name'
    ],
    'cooperative_society_type_id' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_cooperative_society_types',
        'value' => 'id',
        'label' => 'name'
    ],
    'area_of_operation_id' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'area_of_operations_master',
        'value' => 'id',
        'label' => 'name'
    ],
    'is_approved' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [
            1 => 'Yes',
            0 => 'No'
        ]
    ],
    'water_body_type_id' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_water_body_types',
        'value' => 'id',
        'label' => 'name'
    ],
    'sector_of_operation_type' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [
            1 => 'Rural',
            2 => 'Urban'
        ]
    ],
    'sector_of_operation' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_sectors',
        'value' => 'id',
        'label' => 'name'
    ],
    'functional_status' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [
            1 => 'Functional',
            2 => 'Non-Functional',
            3 => 'Liquidation'
        ]
    ],
    'location_of_head_quarter' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [
            1 => 'Urban',
            2 => 'Rural'
        ]
    ],
    'designation' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_designations',
        'value' => 'id',
        'label' => 'name'
    ],
    'category_audit' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_audit_categories',
        'value' => 'id',
        'label' => 'name'
    ],
    'cooperative_society_bank_id' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_cooperative_society_banks',
        'value' => 'id',
        'label' => 'bank_name'
    ],
    'state_code' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_states',
        'value' => 'state_code',
        'label' => 'name'
    ],
    'district_code' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_districts',
        'value' => 'district_code',
        'label' => 'district_name'
    ],
    'locality_ward_code' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_urban_local_body_wards',
        'value' => 'ward_code',
        'label' => 'ward_name'
    ],
    'block_code' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_blocks',
        'value' => 'block_code',
        'label' => 'name'
    ],
    'urban_local_body_type_code' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_urban_local_bodies',
        'value' => 'localbody_type_code',
        'label' => 'localbody_type_name'
    ],
    'urban_local_body_code' => [
        'type' => 'select',
        'source' => 'db',
        'table' => 'ncd_urban_local_bodies',
        'value' => 'localbody_code',
        'label' => 'local_body_name'
    ],
    'bank_type' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [
            1 => 'Rural',
            2 => 'Urban'
        ]
    ],
    'is_approved' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [1 => 'Approved', 0 => 'Non Approved']
    ],
    'is_coastal' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [1 => 'Yes', 0 => 'No']
    ],
    'is_affiliated_union_federation' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [1 => 'Yes', 0 => 'No']
    ],
    'financial_audit' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [1 => 'Yes', 0 => 'No']
    ],
    'is_profit_making' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [1 => 'Yes', 0 => 'No']
    ],
    'is_dividend_paid' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [1 => 'Yes', 0 => 'No']
    ],
    'full_time_secretary' => [
        'type' => 'select',
        'source' => 'static',
        'options' => [1 => 'Yes', 0 => 'No']
    ],
    'gram_panchayat_code' => [
        'type' => 'select',
        'source' => 'dynamic'
    ],
    'village_code' => [
        'type' => 'select',
        'source' => 'dynamic'
    ],
];

function getLabel($col) {

    $map = [

        // 🔹 BASIC
        'cooperative_society_name' => 'Society Name',
        'local_langauge_society_name' => 'Society Name (Local Language)',
        'cooperative_id' => 'Cooperative ID',
        'registration_authoritie_id' => 'Registration Authority',
        'reference_year' => 'Reference Year',
        'date_registration' => 'Date of Registration',
        'registration_number' => 'Registration Number',

        // 🔹 TYPE & AREA
        'cooperative_society_type_id' => 'Cooperative Society Type',
        'area_of_operation_id' => 'Area of Operation',
        'water_body_type_id' => 'Water Body Type',
        'sector_of_operation_type' => 'Sector of Operation Type',
        'sector_of_operation' => 'Sector of Operation',
        'functional_status' => 'Functional Status',

        // 🔹 LOCATION
        'location_of_head_quarter' => 'Headquarter Location',
        'state_code' => 'State',
        'district_code' => 'District',
        'block_code' => 'Block',
        'gram_panchayat_code' => 'Gram Panchayat',
        'village_code' => 'Village',
        'urban_local_body_type_code' => 'Urban Local Body Type',
        'urban_local_body_code' => 'Urban Local Body',
        'locality_ward_code' => 'Ward / Locality',
        'operation_area_location' => 'Operation Area Location',

        // 🔹 ADDRESS
        'pincode' => 'Pincode',
        'full_address' => 'Full Address',
        'address_line' => 'Address Line',

        // 🔹 CONTACT
        'contact_person' => 'Contact Person',
        'designation' => 'Designation',
        'mobile' => 'Mobile Number',
        'landline' => 'Landline Number',
        'email' => 'Email Address',

        // 🔹 SECRETARY / PACS
        'full_time_secretary' => 'Full Time Secretary',
        'mobile_number_of_secretary' => 'Secretary Mobile Number',
        'alternate_contact_no_for_pacs' => 'Alternate Contact Number',
        'pacs_id' => 'PACS ID',

        // 🔹 STATUS FLAGS
        'is_approved' => 'Approved Status',
        'is_coastal' => 'Coastal Area',
        'is_affiliated_union_federation' => 'Affiliated to Union/Federation',
        'financial_audit' => 'Financial Audit Completed',
        'is_profit_making' => 'Profit Making',
        'is_dividend_paid' => 'Dividend Paid',

        // 🔹 MEMBERS & AUDIT
        'members_of_society' => 'Number of Members',
        'audit_complete_year' => 'Audit Completed Year',
        'category_audit' => 'Audit Category',

        // 🔹 FINANCIALS
        'annual_turnover' => 'Annual Turnover',
        'annual_profit' => 'Annual Profit',
        'annual_loss' => 'Annual Loss',
        'dividend_rate' => 'Dividend Rate',

        // 🔹 BANKING
        'bank_type' => 'Bank Type',
        'cooperative_society_bank_id' => 'Bank Name',
        'other_bank' => 'Other Bank',

        // 🔹 TAX
        'pan_no' => 'PAN Number',
        'gst_no' => 'GST Number',

        // 🔹 INFRA
        'how_many_branches' => 'Number of Branches',
    ];

    return $map[$col] ?? ucwords(str_replace('_', ' ', $col));
}

function getOptionsFromDB($table, $valueCol, $labelCol) {
    $options = [];
    $res = execute_query("SELECT $valueCol, $labelCol FROM $table");

    while ($r = mysqli_fetch_assoc($res)) {
        $key = trim($r[$valueCol]);
        $label = trim($r[$labelCol]);

        $options[$key] = $label;
    }

    return $options;
}

$basicFields = [
    'cooperative_id',
    'registration_authoritie_id',
    'cooperative_society_type_id',
    'area_of_operation_id',
    'registration_number',
    'full_address'
];

$societyName = $row['cooperative_society_name'] ?? '';



function getValidationHistory($cooperative_id)
{
    $sql = "
        SELECT *
        FROM ncd_cooperatives_validation
        WHERE ncd_cooperative_id = '$cooperative_id'
        ORDER BY request_id DESC
        LIMIT 5
    ";

    $res = execute_query($sql);

    $history = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $history[] = $row;
    }

    return $history;
}

$validationHistory = getValidationHistory($id);

$current = $validationHistory[0] ?? null;

// Find last rejected row
$lastRejected = null;
foreach ($validationHistory as $h) {
    if ($h['final_status'] == 'rejected' || $h['checker_status'] == 2 || $h['admin_status'] == 2) {
        $lastRejected = $h;
        break;
    }
}

// Extract remarks safely
$checkerRemark = $lastRejected['checker_remark'] ?? null;
$adminRemark   = $lastRejected['admin_remark'] ?? null;

$isReadOnlyUser = in_array($sessionUserType, ['ncd_checker', 'ncd_admin']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cooperative Details</title>
    <meta charset="UTF-8">

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial;
            background: #eaf0f6;
            margin: 0;
        }

        .top-bar {
            background: linear-gradient(90deg, #e05a00, #f47b20);
            color: #fff;
            padding: 10px 20px;
            font-size: 13px;
        }

        .brand-bar {
            background: #fff;
            border-bottom: 2px solid #1a5276;
            padding: 15px;
            text-align: center;
        }

        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
        }

        .container { padding: 20px; }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
        }

        .back-btn {
            background: #6c757d;
            color: #fff;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
        }

        .society-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 20px;
            padding: 12px;
            background: #f1f5f9;
            border-left: 5px solid #1a5276;
        }

        .section { margin-bottom: 25px; }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            padding: 8px 12px;
            background: linear-gradient(135deg, #1a5276, #2c3e50);
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .form-group {
            background: #f9fafb;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
        }

        textarea {
            resize: vertical;
        }

        input[readonly] {
            background: #f3f4f6;
        }

        .actions {
            margin-top: 25px;
            text-align: center;
        }

        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #1a5276, #2c3e50);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }
        .top-bar {
            background: linear-gradient(90deg, #e05a00, #f47b20);
            color: white;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-bar {
            background: #ffffff;
            border-bottom: 2px solid #1a5276;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logos {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .logo-circle {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: 2px solid #1a5276;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 500;
            color: #1a5276;
            text-align: center;
            line-height: 1.3;
        }

        .brand-title {
            flex: 1;
            text-align: center;
        }

        .brand-title .hindi {
            font-size: 20px;
            font-weight: bold;
            color: #c0392b;
        }

        .brand-title .english {
            font-size: 17px;
            font-weight: bold;
            color: #1a5276;
        }


        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .save-reject_btn{
            text-align: center;
        }
    </style>
</head>

<body>
<div id="pageLoader" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(255,255,255,0.7);
    z-index:99999;
    align-items:center;
    justify-content:center;
">
    <div style="
        width:50px;
        height:50px;
        border:5px solid #ccc;
        border-top:5px solid #1a5276;
        border-radius:50%;
        animation:spin 1s linear infinite;
    "></div>
</div>
<div id="msgBox" style="
    display:none;
    position:fixed;
    top:10px;
    left:50%;
    transform:translateX(-50%);
    padding:12px 20px;
    border-radius:6px;
    font-weight:bold;
    z-index:9999;
    color:#fff;
">
</div>

<div class="brand-bar">
    <div class="brand-title">Cooperative Details</div>
</div>

<div class="container">
    <?php if ($sessionUserType === 'ncd_maker' || $sessionUserType === 'ncd_checker') { ?>
    <div class="card" style="margin-bottom: 10px">

            <?php if (!empty($checkerRemark) || !empty($adminRemark)) { ?>

                <div style="
            background:#fff3cd;
            border-left:5px solid #f39c12;
            padding:12px;
            margin-bottom:15px;
            border-radius:6px;
        ">
                    <strong>⚠ Review Remarks:</strong><br><br>

                    <?php if ($sessionUserType === 'ncd_maker' && !empty($checkerRemark)) { ?>
                        <div style="margin-bottom:6px;">
                            <strong>Checker:</strong> <?= htmlspecialchars($checkerRemark) ?>
                        </div>
                    <?php } ?>

                    <?php if (!empty($adminRemark)) { ?>
                        <div>
                            <strong>Admin:</strong> <?= htmlspecialchars($adminRemark) ?>
                        </div>
                    <?php } ?>

                </div>

            <?php } ?>
    </div>
    <?php } ?>

    <div class="card">
        <div class="header">
            <div class="title">Cooperative Information</div>
            <a href="#" class="back-btn" onclick="goBack()">← Back</a>

            <script>
                function goBack() {
                    if (document.referrer !== "") {
                        window.history.back();
                    } else {
                        window.location.href = "list_page.php"; // 👈 change to your listing page
                    }
                }
            </script>        </div>

        <form id="coopForm">
            <input type="hidden" name="id" value="<?= $id ?>">
            <?php $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : ''; ?>

            <!-- SOCIETY NAME -->
            <div class="society-title">
                Society Name: <?= e($societyName) ?>
            </div>

            <!--  BASIC -->
            <div class="section">
                <div class="section-title">Basic Information</div>
                <div class="grid">

                    <?php foreach($basicFields as $col):
                        if(!isset($row[$col])) continue;
                        $val = $row[$col];
                        $config = $fieldConfig[$col] ?? null;
                        ?>

                        <div class="form-group">
                            <label><?= getLabel($col) ?></label>

                            <?php
                            if ($config && $config['type'] === 'textarea') {
                                echo "<textarea name='$col'>".e($val)."</textarea>";
                            }
                            elseif ($config && $config['type'] === 'select') {

                                //Skip options for dynamic dropdowns
                                if ($config['source'] === 'dynamic') {
                                    echo "<select name='$col' id='$col'>";
                                    echo "<option value=''>-- Select --</option>";
                                    echo "</select>";
                                }

                                elseif ($config['source'] === 'db') {
                                    $options = getOptionsFromDB($config['table'], $config['value'], $config['label']);

                                    echo "<select name='$col' id='$col'>";
                                    echo "<option value=''>-- Select --</option>";

                                    foreach($options as $k => $v){
                                        $sel = ((string)$k === (string)$val) ? "selected" : "";
                                        echo "<option value='".e(trim($k))."' $sel>".e(trim($v))."</option>";
                                    }

                                    echo "</select>";
                                }

                                else {
                                    $options = $config['options'];

                                    echo "<select name='$col' id='$col'>";
                                    echo "<option value=''>-- Select --</option>";

                                    foreach($options as $k => $v){
                                        $sel = ((string)$k === (string)$val) ? "selected" : "";
                                        echo "<option value='".e(trim($k))."' $sel>".e(trim($v))."</option>";
                                    }

                                    echo "</select>";
                                }
                            }
                            elseif ($config && !empty($config['readonly'])) {
                                echo "<input type='text' value='".e($val)."' readonly>";
                            }

                            else {
                                echo "<input type='text' name='$col' value='".e($val)."'>";
                            }
                            ?>

                        </div>

                    <?php endforeach; ?>

                </div>
            </div>

            <!-- ADDITIONAL -->
            <div class="section">
                <div class="section-title">Other Details</div>
                <div class="grid">

                    <?php foreach($row as $col => $val):

                        if($col == 'id' || $col == 'cooperative_society_name') continue;
                        if(in_array($col, $basicFields)) continue;

                        $config = $fieldConfig[$col] ?? null;
                        ?>

                        <div class="form-group">
                            <label><?= getLabel($col) ?></label>

                            <?php
                            if ($config && $config['type'] === 'textarea') {
                                echo "<textarea name='$col'>".e($val)."</textarea>";
                            }
                            elseif ($config && $config['type'] === 'select') {

                                //  Skip options for dynamic dropdowns
                                if ($config['source'] === 'dynamic') {
                                    echo "<select name='$col' id='$col'>";
                                    echo "<option value=''>-- Select --</option>";
                                    echo "</select>";
                                }

                                elseif ($config['source'] === 'db') {
                                    $options = getOptionsFromDB($config['table'], $config['value'], $config['label']);

                                    echo "<select name='$col' id='$col'>";
                                    echo "<option value=''>-- Select --</option>";

                                    foreach($options as $k => $v){
                                        $sel = ((string)$k === (string)$val) ? "selected" : "";
                                        echo "<option value='".e(trim($k))."' $sel>".e(trim($v))."</option>";
                                    }

                                    echo "</select>";
                                }

                                else {
                                    $options = $config['options'];

                                    echo "<select name='$col' id='$col'>";
                                    echo "<option value=''>-- Select --</option>";

                                    foreach($options as $k => $v){
                                        $sel = ((string)$k === (string)$val) ? "selected" : "";
                                        echo "<option value='".e(trim($k))."' $sel>".e(trim($v))."</option>";
                                    }

                                    echo "</select>";
                                }
                            }
                            else {
                                echo "<input type='text' name='$col' value='".e($val)."'>";
                            }
                            ?>

                        </div>

                    <?php endforeach; ?>

                </div>
            </div>

            <?php if ($sessionUserType === 'ncd_maker') { ?>
                <div class="actions">

                    <!-- SAVE (Draft) -->
                    <button type="submit" name="action" value="save" class="btn">
                        Save Draft
                    </button>

                    <!-- FINAL SUBMIT -->
                    <button type="submit" name="action" value="submit" class="btn" style="background:green">
                        Submit to Checker
                    </button>

                </div>
            <?php } ?>

            <div>
                <?php if ($sessionUserType === 'ncd_checker') { ?>
                    <div class="save-reject_btn">
                        <button type="submit" name="action" value="verify" class="btn" style="background:green">
                            Verify
                        </button>

                        <button type="button" class="btn" style="background:red" onclick="toggleCheckerReject()">
                            Reject
                        </button>
                    </div>
                    <div >
                        <div id="checkerRejectBox" style="display:none; margin:10px;padding: 10px;">
                            <textarea name="checker_remark" placeholder="Enter rejection reason" required></textarea>
                            <div class="save-reject_btn " style="margin: 10px">
                                <button type="submit" name="action" value="checker_reject" class="btn ">
                                    Final Reject
                                </button>
                            </div>

                        </div>
                    </div>

                <?php } ?>
            </div>
            <div>
                <?php if ($sessionUserType === 'ncd_admin') { ?>
                <div class="save-reject_btn">
                    <button type="submit" name="action" value="approve" class="btn" style="background:green">
                        Approve
                    </button>

                    <button type="button" class="btn" style="background:red" onclick="toggleAdminReject()">
                        Reject
                    </button>

                    <div id="adminRejectBox" style="display:none; margin-top:10px;">
                        <textarea name="admin_remark" placeholder="Enter rejection reason" required></textarea>
                        <button type="submit" name="action" value="admin_reject" class="btn">
                            Final Reject
                        </button>
                    </div>
                </div>

                <?php } ?>
            </div>

        </form>

    </div>
</div>

</body>
</html>

<script>

    //  PRESELECT DATA FROM PHP
    const preselected = {
        state: "<?= $row['state_code'] ?? '' ?>",
        district: "<?= $row['district_code'] ?? '' ?>",
        block: "<?= $row['block_code'] ?? '' ?>",
        gp: "<?= $row['gram_panchayat_code'] ?? '' ?>",
        village: "<?= $row['village_code'] ?? '' ?>"
    };


    //  ELEMENT REFERENCES
    const state    = document.querySelector("[name='state_code']");
    const district = document.querySelector("[name='district_code']");
    const block    = document.querySelector("[name='block_code']");
    const gp       = document.querySelector("[name='gram_panchayat_code']");
    const village  = document.querySelector("[name='village_code']");

    //  GENERIC DROPDOWN LOADER
    function loadDropdown(url, target, valueKey, labelKey, selectedValue = '') {

        // Reset target before loading
        target.innerHTML = "<option value=''>Loading...</option>";

        return fetch(url)
            .then(res => res.json())
    .then(data => {

            let html = "<option value=''>-- Select --</option>";

        if (!data || data.length === 0) {
            target.innerHTML = "<option value=''>No Data Found</option>";
            return;
        }

        data.forEach(item => {
            let selected = (String(item[valueKey]) === String(selectedValue)) ? "selected" : "";
        html += `<option value="${item[valueKey]}" ${selected}>${item[labelKey]}</option>`;
    });

        target.innerHTML = html;
    })
    .catch(err => {
            console.error("Dropdown Load Error:", err);
        target.innerHTML = "<option value=''>Error loading</option>";
    });
    }

    //  RESET HELPERS
    function resetDropdown(el) {
        el.innerHTML = "<option value=''>-- Select --</option>";
    }

    function resetBelow(level) {
        if (level === 'state') {
            resetDropdown(district);
            resetDropdown(block);
            resetDropdown(gp);
            resetDropdown(village);
        }
        if (level === 'district') {
            resetDropdown(block);
            resetDropdown(gp);
            resetDropdown(village);
        }
        if (level === 'block') {
            resetDropdown(gp);
            resetDropdown(village);
        }
        if (level === 'gp') {
            resetDropdown(village);
        }
    }

    //  CHANGE EVENTS
    state.addEventListener("change", async function () {
        if (!this.value) return resetBelow('state');

        await loadDropdown(
            "Ncd_Reports/ajax/get_districts.php?state_code=" + this.value,
            district,
            "district_code",
            "district_name"
        );

        resetBelow('district');
    });

    district.addEventListener("change", async function () {
        if (!this.value) return resetBelow('district');

        await loadDropdown(
            "Ncd_Reports/ajax/get_blocks.php?district_code=" + this.value,
            block,
            "block_code",
            "name"
        );

        resetBelow('block');
    });

    block.addEventListener("change", async function () {
        if (!this.value) return resetBelow('block');

        await loadDropdown(
            "Ncd_Reports/ajax/get_gp.php?block_code=" + this.value,
            gp,
            "gram_panchayat_code",
            "gram_panchayat_name"
        );

        resetBelow('gp');
    });

    gp.addEventListener("change", async function () {
        if (!this.value) return resetBelow('gp');

        await loadDropdown(
            "Ncd_Reports/ajax/get_villages.php?gp_code=" + this.value,
            village,
            "village_code",
            "village_name"
        );
    });

    // PRESELECT FLOW
    window.addEventListener("load", async function () {

        if (!preselected.state) return;

        // STEP 1: District
        await loadDropdown(
            "Ncd_Reports/ajax/get_districts.php?state_code=" + preselected.state,
            district,
            "district_code",
            "district_name",
            preselected.district
        );

        // STEP 2: Block
        if (preselected.district) {
            await loadDropdown(
                "Ncd_Reports/ajax/get_blocks.php?district_code=" + preselected.district,
                block,
                "block_code",
                "name",
                preselected.block
            );
        }

        // STEP 3: GP
        if (preselected.block) {
            await loadDropdown(
                "Ncd_Reports/ajax/get_gp.php?block_code=" + preselected.block,
                gp,
                "gram_panchayat_code",
                "gram_panchayat_name",
                preselected.gp
            );
        }

        // STEP 4: Village
        if (preselected.gp) {
            await loadDropdown(
                "Ncd_Reports/ajax/get_villages.php?gp_code=" + preselected.gp,
                village,
                "village_code",
                "village_name",
                preselected.village
            );
        }
    });

</script>

<script>
    const form = document.getElementById("coopForm");
    const msgBox = document.getElementById("msgBox");
    const loader = document.getElementById("pageLoader");

    let isSubmitting = false;
    let clickedButton = null;

    // detect which submit button was clicked
    document.querySelectorAll("#coopForm button[type=submit]").forEach(btn => {
        btn.addEventListener("click", function () {
            clickedButton = this;
        });
    });

    // MESSAGE FUNCTION
    function showMessage(message, type = "success") {

        msgBox.innerText = message;
        msgBox.style.background = (type === "success") ? "#28a745" : "#dc3545";

        msgBox.style.display = "block";
        msgBox.style.opacity = "1";

        setTimeout(() => {
            msgBox.style.opacity = "0";
        setTimeout(() => {
            msgBox.style.display = "none";
    }, 300);
    }, 3000);
    }

    //FORM SUBMIT
    form.addEventListener("submit", function(e) {
        e.preventDefault();

        if (isSubmitting) return;
        isSubmitting = true;

        const formData = new FormData(this);

        // IMPORTANT: send correct action (save / submit / verify etc.)
        if (clickedButton) {
            formData.set("action", clickedButton.value);
        }

        const btn = clickedButton || this.querySelector("button");

        // UI START
        loader.style.display = "flex";
        btn.disabled = true;
        btn.innerText = "Saving...";

        fetch("Ncd_Reports/ajax/save_cooperative.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.text())
    .then(text => {

            let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error("Invalid JSON:", text);
            throw new Error("Invalid response from server");
        }

        loader.style.display = "none";

        setTimeout(() => {
            if (data.status === "success") {
            showMessage(data.message || "Saved successfully", "success");
        } else {
            showMessage(data.message || "Update failed", "error");
        }
    }, 50);

    })
    .catch(err => {
            console.error(err);

        loader.style.display = "none";

        setTimeout(() => {
            showMessage("Server error occurred", "error");
    }, 50);
    })
    .finally(() => {
            isSubmitting = false;
        btn.disabled = false;
        btn.innerText = "Save";
        clickedButton = null;
    });

    });
</script>

<script>
    function toggleCheckerReject() {
        const box = document.getElementById("checkerRejectBox");
        if (box) {
            box.style.display = "block";

            const textarea = box.querySelector("textarea[name='checker_remark']");
            if (textarea) {
                textarea.setAttribute("required", "required");
            }
        }
    }

    function toggleAdminReject() {
        const box = document.getElementById("adminRejectBox");
        if (box) {
            box.style.display = "block";

            const textarea = box.querySelector("textarea[name='admin_remark']");
            if (textarea) {
                textarea.setAttribute("required", "required");
            }
        }
    }

    document.querySelector("button[value='verify']")?.addEventListener("click", function () {
        const textarea = document.querySelector("textarea[name='checker_remark']");
        if (textarea) {
            textarea.removeAttribute("required");
        }
    });

    document.querySelector("button[value='approve']")?.addEventListener("click", function () {
        const textarea = document.querySelector("textarea[name='admin_remark']");
        if (textarea) {
            textarea.removeAttribute("required");
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // 1. USER TYPE FROM PHP
        const userType = "<?= $sessionUserType ?>";
        const isReadOnlyUser = (userType === 'ncd_checker' || userType === 'ncd_admin' || userType ==='superadmin');

        if (!isReadOnlyUser) return;

        // 2. LOCK ALL FIELDS EXCEPT REMARKS & BUTTONS
        document.querySelectorAll("#coopForm input, #coopForm textarea, #coopForm select")
            .forEach(el => {

            // Allow remarks fields
            if (el.name === "checker_remark" || el.name === "admin_remark") return;

        // Allow buttons
        if (el.type === "submit" || el.type === "button") return;

        // Handle SELECT
        if (el.tagName === "SELECT") {
            el.disabled = true;

            // create hidden input to preserve value
            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = el.name;
            hidden.value = el.value;

            el.parentNode.appendChild(hidden);
        }

        // Handle INPUT & TEXTAREA
        else {
            if (el.type !== "hidden") {
                el.readOnly = true;
            }
        }

        // Add visual style
        el.style.background = "#f1f5f9";
        el.style.cursor = "not-allowed";
    });

    });
</script>

<script>
    function goBack() {
        if (document.referrer !== "") {
            history.back();
        } else {
            window.location.href = "list_page.php"; // 👈 change to your listing page
        }
    }
</script>
<?php
page_footer_start();
page_footer_end();
?>