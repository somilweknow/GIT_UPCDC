<?php
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;
error_reporting(E_ALL);

$row_invoice = [
    'sno' => '',
    'apex_id' => '',
    'latitude' => '',
    'longitude' => '',
    'committee_status' => '',
    'email_id' => '',
    'photo_id' => '',
    'society_registration_no' => '',
    'society_registration_date' => '',
    'members_no' => '',
    'inactive_members_no' => '',
    'active_members_no' => '',
    'new_members' => '',
    'share_capital' => '',
    'inactive_to_active_no' => '',
    'total_members' => '',
    'division_name' => '',
    'district_name' => '',
    'tehseel_name' => '',
    'mobile_number' => '',
    'nagar_nigam' => '',
    'liquidation' => '',
    'liquidation_date' => '',
    'liquidation_status' => '',
    'litigation' => '',
    'litigation_remark' => ''
];
// echo '-------------------------------------', $_GET['exdid'];
if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.*, apex.* FROM `apex_si_1_1` LEFT JOIN `apex` ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id = "' . $_GET['exdid'] . '"';
    $result_invoice = execute_query($sql);
    if ($result_invoice && mysqli_num_rows($result_invoice) >= 1) {
        $row_invoice = mysqli_fetch_assoc($result_invoice);

        $_SESSION['survey_id'] = $row_invoice['sno'];
        $row_invoice['latitude'] = $row_invoice['latitude'];
        $row_invoice['longitude'] = $row_invoice['longitude'];
        $row_invoice['committee_status'] = $row_invoice['committee_status'];
        $row_invoice['email_id'] = $row_invoice['email_id'];
        $row_invoice['photo_id'] = $row_invoice['photo_id'];
        $row_invoice['society_registration_no'] = $row_invoice['society_registration_no'];
        $row_invoice['society_registration_date'] = $row_invoice['society_registration_date'];
        $row_invoice['members_no'] = $row_invoice['members_no'];
        $row_invoice['active_members_no'] = $row_invoice['active_members_no'];
        $row_invoice['inactive_members_no'] = $row_invoice['inactive_members_no'];
        $row_invoice['new_members'] = $row_invoice['new_members'];
        $row_invoice['share_capital'] = $row_invoice['share_capital'];
        $row_invoice['inactive_to_active_no'] = $row_invoice['inactive_to_active_no'];
        $row_invoice['total_members'] = $row_invoice['total_members'];
    }

    $sql_contacts = "SELECT * FROM apex_si_1_4_contacts WHERE survey_id = '" . $row_invoice['sno'] . "'";
    $res_contacts = execute_query($sql_contacts);
    $contact_rows = [];

    if ($res_contacts && mysqli_num_rows($res_contacts) > 0) {
        while ($r = mysqli_fetch_assoc($res_contacts)) {
            $contact_rows[] = $r;
        }
    } else {
        // One empty row by default
        $contact_rows[] = [
            'name' => '',
            'division_id' => '',
            'district_ids' => '',
            'address' => '',
            'mobile' => '',
            'email' => '',
            'pincode' => '',
            'latitude' => '',
            'longitude' => ''
        ];
    }

    $sql = 'SELECT * FROM survey_invoice_new_sec_3_9 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $result_sec_3_9 = execute_query($sql);
    $row_3_9 = array();
    $d = 1;

    if ($result_sec_3_9 && mysqli_num_rows($result_sec_3_9) > 0) {
        $row_3_9['count'] = mysqli_num_rows($result_sec_3_9);
        while ($row_section_3_9 = mysqli_fetch_assoc($result_sec_3_9)) {

            $row_3_9['sec_3_flat_area_' . $d] = $row_section_3_9['sec_3_flat_area'];
            $row_3_9['sec_3_flat_type_' . $d] = $row_section_3_9['sec_3_flat_type'];

            $d++;
        }
    } else {
        $row_3_9['count'] = 1;
        $row_3_9['sec_3_flat_area_' . $d] = '';
        $row_3_9['sec_3_flat_type_' . $d] = '';
    }

    $sql = 'select * from survey_invoice_sec_3_5 where survey_id="' . $row_invoice['sno'] . '"';
    // $sql = 'SELECT *, 
    // 	 CONCAT("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", food_scheme) AS new_food_scheme FROM survey_invoice_sec_3_5 WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $res_3_5_side = execute_query($sql);
    if (mysqli_num_rows($res_3_5_side) != 0) {
        $data_3_5 = array();
        $i = 1;
        while ($row_3_5_side = mysqli_fetch_assoc($res_3_5_side)) {
            $row_3_5['sec_3_c_length_' . $i] = $row_3_5_side['total_area'];
            $row_3_5['sec_3_c_vacant_land_status_' . $i] = $row_3_5_side['land_type'];
            $row_3_5['sec_3_c_land_location_' . $i] = $row_3_5_side['location'];
            $i++;
        }
        $row_3_5['sec_3_c_id'] = $i - 1;
    } else {
        $i = 1;
        $row_3_5['sec_3_c_id'] = 1;
        $row_3_5['sec_3_c_length_1'] = "";
        $row_3_5['sec_3_c_vacant_land_status_1'] = "";
        $row_3_5['sec_3_c_land_location_1'] = "";
    }

    $sql = 'SELECT * FROM survey_invoice_sec_3_new_1 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_3_new_1 = execute_query($sql);

    if (mysqli_num_rows($res_3_new_1) != 0) {
        $row_3_new_1 = mysqli_fetch_assoc($res_3_new_1);
        $row_3_new_1['sec_3_profit_loss_1'] = $row_3_new_1['profit_loss_1'];
        $row_3_new_1['sec_3_profit_loss_amount_1'] = $row_3_new_1['profit_loss_amount_1'];
        $row_3_new_1['sec_3_accumulated_1'] = $row_3_new_1['accumulated_1'];
        $row_3_new_1['sec_3_accumulated_amount_1'] = $row_3_new_1['accumulated_amount_1'];
        $row_3_new_1['sec_3_profit_loss_2'] = $row_3_new_1['profit_loss_2'];
        $row_3_new_1['sec_3_profit_loss_amount_2'] = $row_3_new_1['profit_loss_amount_2'];
        $row_3_new_1['sec_3_accumulated_2'] = $row_3_new_1['accumulated_2'];
        $row_3_new_1['sec_3_accumulated_amount_2'] = $row_3_new_1['accumulated_amount_2'];
        $row_3_new_1['sec_3_profit_loss_3'] = $row_3_new_1['profit_loss_3'];
        $row_3_new_1['sec_3_profit_loss_amount_3'] = $row_3_new_1['profit_loss_amount_3'];
        $row_3_new_1['sec_3_accumulated_3'] = $row_3_new_1['accumulated_3'];
        $row_3_new_1['sec_3_accumulated_amount_3'] = $row_3_new_1['accumulated_amount_3'];
        $row_3_new_1['sec_3_financial_audit_year'] = $row_3_new_1['financial_audit_year'];
        $row_3_new_1['sec_3_audit_grading'] = $row_3_new_1['audit_grading'];
        $row_3_new_1['sec_3_compliance_status'] = $row_3_new_1['compliance_status'];
        $row_3_new_1['sec_3_agm_year'] = $row_3_new_1['agm_year'];
        $row_3_new_1['sec_3_dividend_year'] = $row_3_new_1['dividend_year'];
        $row_3_new_1['sec_3_dividend_per'] = $row_3_new_1['dividend_per'];
        $row_3_new_1['sec_3_dividend_amt'] = $row_3_new_1['dividend_amt'];
        $row_3_new_1['sec_3_santulan_patra'] = $row_3_new_1['santulan_patra'];
    } else {
        $row_3_new_1 = [
            'sec_3_profit_loss_1' => '',
            'sec_3_profit_loss_amount_1' => '',
            'sec_3_accumulated_1' => '',
            'sec_3_accumulated_amount_1' => '',
            'sec_3_profit_loss_2' => '',
            'sec_3_profit_loss_amount_2' => '',
            'sec_3_accumulated_2' => '',
            'sec_3_accumulated_amount_2' => '',
            'sec_3_profit_loss_3' => '',
            'sec_3_profit_loss_amount_3' => '',
            'sec_3_accumulated_3' => '',
            'sec_3_accumulated_amount_3' => '',
            'sec_3_financial_audit_year' => '',
            'sec_3_audit_grading' => '',
            'sec_3_compliance_status' => '',
            'sec_3_agm_year' => '',
            'sec_3_dividend_year' => '',
            'sec_3_dividend_per' => '',
            'sec_3_dividend_amt' => '',
            'sec_3_santulan_patra' => ''
        ];
    }

    $sql = "SELECT * FROM survey_invoice_plot_details WHERE survey_id = '" . $row_invoice['sno'] . "'";
    $res_new_plot = execute_query($sql);

    if (mysqli_num_rows($res_new_plot) != 0) {
        $row_new_plot = mysqli_fetch_assoc($res_new_plot);
        $row_new_plot['sec_new_plot_area'] = $row_new_plot['plot_area'];
        $row_new_plot['sec_new_plot_revenue_status'] = $row_new_plot['plot_revenue_status'];
        $row_new_plot['sec_new_plot_reason_for_not_record'] = $row_new_plot['plot_reason_for_not_record'];
        $row_new_plot['sec_new_plot_practices_if_not'] = $row_new_plot['plot_practices_if_not'];
        $row_new_plot['sec_new_plot_gata_no'] = $row_new_plot['plot_gata_no'];
        $row_new_plot['sec_3_ownership'] = $row_new_plot['sec_3_ownership'];
        $row_new_plot['sec_3_building_area'] = $row_new_plot['society_building_area'];
        $row_new_plot['sec_3_building_rent'] = $row_new_plot['society_building_rent_amount'];
        $row_new_plot['sec_3_remark'] = $row_new_plot['society_building_remark'];
        $row_new_plot['sec_new_remarks'] = $row_new_plot['remarks'];
        $row_new_plot['sec_3_is_map'] = $row_new_plot['is_map'];
        $row_new_plot['sec_3_map_accept'] = $row_new_plot['map_accept'];
    } else {
        $row_new_plot = [
            'sec_new_plot_area' => '',
            'sec_new_plot_revenue_status' => '',
            'sec_new_plot_reason_for_not_record' => '',
            'sec_new_plot_practices_if_not' => '',
            'sec_new_plot_gata_no' => '',
            'sec_3_ownership' => '',
            'sec_3_building_area' => '',
            'sec_3_building_rent' => '',
            'sec_3_remark' => '',
            'sec_new_remarks' => '',
            'sec_3_is_map' => '',
            'sec_3_map_accept' => ''
        ];
    }

    $sql = 'SELECT * FROM survey_invoice_sec_2_1 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_2_1 = execute_query($sql);

    if (mysqli_num_rows($res_2_1) != 0) {
        $row_2_1 = mysqli_fetch_assoc($res_2_1);
        $row_2_1['sec_6_access_road'] = $row_2_1['sec_6_road'];
        $row_2_1['sec_6_paved_road'] = $row_2_1['approach_road'];
        $row_2_1['sec_6_2_truck_not_reach'] = $row_2_1['distance_from_approach_road'];
        $row_2_1['sec_7_electrical_connection'] = $row_2_1['electric_connection'];
        $row_2_1['sec_7_electrical_connection_working'] = $row_2_1['electric_connection_working'];
        $row_2_1['sec_7_if_yes'] = $row_2_1['electric_connection_proposal'];
        $row_2_1['sec_8_internet_connection'] = $row_2_1['internet_connectivity'];
        $row_2_1['sec_8_plot_frontage'] = $row_2_1['plot_frontage'];
        $row_2_1['sec_8_school_hosp_status'] = $row_2_1['school_hosp_status'];
    } else {
        $row_2_1 = [
            'sec_6_access_road' => '',
            'sec_6_paved_road' => '',
            'sec_6_2_truck_not_reach' => '',
            'sec_7_electrical_connection' => '',
            'sec_7_electrical_connection_working' => '',
            'sec_7_if_yes' => '',
            'sec_8_internet_connection' => '',
            'sec_8_plot_frontage' => '',
            'sec_8_school_hosp_status' => ''
        ];
    }

    $sql_land = 'SELECT * FROM survey_invoice_sec_3c WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_land = execute_query($sql_land);

    if (mysqli_num_rows($res_land) != 0) {
        $i = 1;
        while ($row_land_temp = mysqli_fetch_assoc($res_land)) {
            $row_3_c['sec_3_c_area_' . $i] = $row_land_temp['area'];
            $row_3_c['sec_3_c_land_status_' . $i] = $row_land_temp['land_status'];
            $row_3_c['sec_3_c_land_location_' . $i] = $row_land_temp['land_location'];
            $row_3_c['sec_3_c_land_remark_' . $i] = $row_land_temp['remark'];
            $i++;
        }
        $row_3_c['count'] = $i - 1;
    } else {
        $row_3_c['count'] = 1;
        $row_3_c['sec_3_c_area_1'] = '';
        $row_3_c['sec_3_c_land_status_1'] = '';
        $row_3_c['sec_3_c_land_location_1'] = '';
        $row_3_c['sec_3_c_land_remark_1'] = '';
    }
}

if (empty($row_invoice['apex_id']) && isset($_GET['exdid'])) {
    $row_invoice['apex_id'] = $_GET['exdid'];
}
?>

<?php
page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/survey_validate.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .date-field {
        display: none;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }
</style>
<style>
    .table-section {
        border-collapse: collapse;
    }

    .table-section th,
    .table-section td {
        border: 1px solid #000;
    }

    .table-section,
    .table-section th,
    .table-section td {
        border-color: transparent;
    }

    .table-section td:first-child {
        border-left-color: #000;
    }

    .table-section th:first-child {
        border-left-color: #000;
    }

    .table-section td:last-child {
        border-right-color: #000;
    }

    .table-section th:last-child {
        border-right-color: #000;
    }

    .table-section tr:first-child th {
        border-top-color: #000;
    }

    .table-section tr:last-child td {
        border-bottom-color: #000;
    }

    .form-section {
        margin-bottom: 0;
    }

    .form-section input {
        margin-bottom: 0;
    }

    .step h4 {
        color: #FFFFFF;
        background: #FF8E00;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
    }

    .step h5 {
        color: #000000;
        background: #FFDB44;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
    }
</style>
<style>
    .select-default {
        background-color: white;
    }

    .card label {
        font-size: 0.80rem;
    }
</style>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }

    th,
    td {
        padding: 8px 12px;
        border: 1px solid #ccc;
        text-align: left;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        box-sizing: border-box;
    }

    button {
        margin-top: 10px;
        padding: 6px 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    button:hover {
        background-color: #45a049;
    }

    .tree-output {
        margin-top: 30px;
        font-family: 'Courier New', Courier, monospace;
    }

    .tree-output ul {
        padding-left: 20px;
        list-style-type: none;
    }

    .tree-output ul li {
        margin: 10px 0;
        position: relative;
    }

    .tree-output ul li::before {
        content: "";
        position: absolute;
        left: -20px;
        top: 10px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #000;
    }

    .tree-output ul li ul {
        margin-top: 5px;
        padding-left: 20px;
    }

    .tree-output ul li ul li::before {
        left: -10px;
        top: 8px;
    }

    .tree-output ul li ul li {
        margin: 5px 0;
    }
</style>
<?php
page_header_end();
page_sidebar();

// $sql = 'select * from survey_invoice_validation where survey_id="' . $_SESSION['survey_id'] . '" and approval_status="reject" order by creation_time desc limit 1';
// $result_rejection = execute_query($sql);
// if (mysqli_num_rows($result_rejection) != 0) {
//     $row_rejection = mysqli_fetch_assoc($result_rejection);
//     $msg = '<p class="text-danger">आपका प्रपत्र निम्न कारणों से सत्यापन में वापस भेजा गया है : <br/>' . $row_rejection['remarks'] . '</p>';
// }
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex my-auto">
                    <div class="col-md-12">
                        <div class="progress">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50"
                                class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                role="progressbar" style="width: 0%">
                            </div>
                        </div>
                        <form action="scripts/ajax_upavp.php" method="post" enctype="multipart/form-data" id="user_form"
                            name="user_form">
                            <div id="steps-container">
                                <div class="step">
                                    <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                            style="height:45px; width:45px;"> 1. समिति का विवरण </h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-sm-8 form-group">
                                                        <label>संस्था का प्रकार</label>
                                                        <input type="text" name="apex_type" id="apex_type"
                                                            tabindex="<?php echo $tab++; ?>" readonly
                                                            value="शीर्ष सहकारी संस्था (APEX)" class="form-control">
                                                    </div>

                                                    <div class="col-sm-8 form-group" style="margin: 9px;"
                                                        id="sec_1_institute_name_container">
                                                        <label>संस्था का नाम</label>
                                                        <input type="text" name="apex_name" id="apex_name"
                                                            tabindex="<?php echo $tab++; ?>" readonly
                                                            value="आवासीय समितियां" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <input type="hidden" id="apex_code" name="apex_code"
                                                    value="<?php echo $row_invoice['apex_id']; ?>">
                                                <input type="hidden" id="mobile_number" name="mobile_number"
                                                    value="<?php echo $row_invoice['mobile_number']; ?>">

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>Latitude</label>
                                                        <input type="text" id="lat" disabled class="form-control"
                                                            value="<?php echo $row_invoice['latitude']; ?>">

                                                        <label>Longitude</label>
                                                        <input type="text" id="long" disabled class="form-control"
                                                            value="<?php echo $row_invoice['longitude']; ?>">

                                                        <button type="button" class="btn btn-info mt-2"
                                                            onClick="getLocation();">लोकेशन रिफ्रेश करें</button>
                                                        <div class="blinking-text small text-danger mt-1">(लोकेशन
                                                            मोबाईल से भरे)*</div>
                                                    </div>

                                                    <div class="col-md-9" id="map_container">
                                                        <iframe id="googlemap"
                                                            src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                            width="100%" height="100%"
                                                            style="border:1px solid #ccc; border-radius:10px;"
                                                            allowfullscreen loading="lazy"
                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row mb-2 align-items-end">
                                                    <div class="col-md-2">
                                                        <label>मण्डल :</label>
                                                        <input type="text" class="form-control" id="division_name"
                                                            name="division_name"
                                                            value="<?php echo htmlspecialchars($row_invoice['division_name']); ?>"
                                                            placeholder="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>जिला :</label>
                                                        <input type="text" class="form-control" id="district_name"
                                                            name="district_name"
                                                            value="<?php echo htmlspecialchars($row_invoice['district_name']); ?>"
                                                            placeholder="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>तहसील :</label>
                                                        <input type="text" class="form-control" id="tehseel_name"
                                                            name="tehseel_name"
                                                            value="<?php echo htmlspecialchars($row_invoice['tehseel_name']); ?>"
                                                            placeholder="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>समिति का प्रकार :</label>
                                                        <select class="form-control" id="committee_status"
                                                            name="committee_status" tabindex="<?php echo $tab++; ?>"
                                                            onChange="hide_show(this.value, '#committee_date_section', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--Select--</option>
                                                            <option value="yes" <?php echo ($row_invoice['committee_status'] == 'yes') ? 'selected' : ''; ?>>केन्द्रीय</option>
                                                            <option value="no" <?php echo ($row_invoice['committee_status'] == 'no') ? 'selected' : ''; ?>>प्राथमिक</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>सचिव का मोबाइल नंबर :</label>
                                                        <input type="text" class="form-control" id="mobile_number"
                                                            name="mobile_number"
                                                            value="<?php echo htmlspecialchars($row_invoice['mobile_number']); ?>"
                                                            placeholder="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>नगर पालिका / नगर पंचायत / नगर निगम</label>
                                                        <select class="form-control" id="nagar_nigam" name="nagar_nigam"
                                                            tabindex="<?php echo $tab++; ?>">
                                                            <option value="">--Select--</option>
                                                            <option value="1" <?php echo ($row_invoice['nagar_nigam'] == '1') ? 'selected' : ''; ?>>नगर पालिका</option>
                                                            <option value="2" <?php echo ($row_invoice['nagar_nigam'] == '2') ? 'selected' : ''; ?>>नगर पंचायत</option>
                                                            <option value="3" <?php echo ($row_invoice['nagar_nigam'] == '3') ? 'selected' : ''; ?>>नगर निगम</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr />
                                        <div class="row">
                                            <div class="col-sm-2 form-group">
                                                <label>क्या समिति सक्रिय है ?</label>
                                                <select class="form-control" id="committee_status"
                                                    name="committee_status" tabindex="<?php echo $tab++; ?>">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php if ($row_invoice['committee_status'] == 'yes')
                                                        echo 'selected'; ?>>
                                                        हाँ</option>
                                                    <option value="no" <?php if ($row_invoice['committee_status'] == 'no')
                                                        echo 'selected'; ?>>नहीं</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2 form-group" id="committee_date_section"
                                                style="display: none;">
                                                <label>समिति की तिथि</label><br>
                                                <label><small>नहीं पता होने की स्थिति में आज का ही दिनांक
                                                        दर्शायें</small></label>
                                                <input type="text" id="sec_1_committee_date" name="sec_1_committee_date"
                                                    class="form-control"
                                                    value="<?php echo isset($row_invoice['committee_date']) ? $row_invoice['committee_date'] : ''; ?>"
                                                    readonly>
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <label>समिति पंजीकरण संख्या</label>
                                                <br />
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_no']); ?>">
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <label>समिति पंजीकरण दिनांक</label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_date']); ?>">
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <label>ई-मेल आई.डी.</label>
                                                <input type="text" name="email_id" id="email_id"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['email_id']); ?>">
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <label>समिति की फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="society_photo" id="society_photo"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                            </div>
                                            <?php
                                            if (!empty($row_invoice['photo_id'])) {
                                                ?>
                                                <div class="col-sm-2 form-group">
                                                    <img src="<?php echo $row_invoice['photo_id']; ?>"
                                                        class="img-fluid img-thumbnail" style="height:50px;"
                                                        id="society_photo_uploaded">
                                                    <label><a href="<?php echo $row_invoice['photo_id']; ?>"
                                                            target="_blank">संलग्न फोटो देखें</a></label>

                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>क्या समिति परिसमापन (Liquidation) में है?</label>
                                                <select name="liquidation" id="liquidation"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="hide_show(this.value, '#liquidation_date_container', 'yes');hide_show(this.value, '#liquidation_status', 'yes');handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo ($row_invoice['liquidation'] == 'yes') ? ' selected="selected"' : ''; ?> style="background:#0f0"> हाँ
                                                    </option>
                                                    <option value="no" <?php echo ($row_invoice['liquidation'] == 'no') ? ' selected="selected"' : ''; ?> style="background:#f00"> नहीं
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-sm-2 form-group" id="liquidation_date_container"
                                                style="display: none;">
                                                <label>परिसमापक नियुक्त करने की तिथि</label>
                                                <input type="date" tabindex="<?php echo $tab++; ?>"
                                                    id="liquidation_date" name="liquidation_date" class="form-control"
                                                    placeholder="Choose Date"
                                                    value="<?php echo isset($row_invoice['liquidation_date']) ? $row_invoice['liquidation_date'] : ''; ?>">
                                            </div>

                                            <div class="col-sm-2 form-group" id="liquidation_status"
                                                style="display: none;">
                                                <label>परिसमापन की अद्यतन स्थिति</label>
                                                <input type="text" tabindex="<?php echo $tab++; ?>"
                                                    id="liquidation_status" name="liquidation_status"
                                                    class="form-control" placeholder=""
                                                    value="<?php echo isset($row_invoice['liquidation_status']) ? $row_invoice['liquidation_status'] : ''; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>क्या समिति पर स्वामित्व को लेकर कोई वाद (Litigation) सिविल
                                                    न्यायालय में विचाराधीन हैं?</label>
                                                <select name="litigation" id="litigation"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="hide_show(this.value, '#litigation_remark', 'yes'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo ($row_invoice['litigation'] == 'yes') ? ' selected="selected"' : ''; ?> style="background:#0f0">हाँ
                                                    </option>
                                                    <option value="no" <?php echo ($row_invoice['litigation'] == 'no') ? ' selected="selected"' : ''; ?> style="background:#f00">नहीं
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-sm-2 form-group" id="litigation_remark"
                                                style="display: none;">
                                                <label>विवाद का विवरण</label>
                                                <label><small>कृपया अधिकतम 200 शब्दों मे अपनी बात
                                                        रखे</small></label>
                                                <textarea name="litigation_remark" tabindex="<?php echo $tab++ ?>"
                                                    id="litigation_remark"
                                                    class="form-control"><?php echo $row_invoice['litigation_remark'] ?></textarea>
                                            </div>
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <!----------------2 start-------------------------------------------------------->
                                <div class="step">
                                    <h4><img src="images/logo/3.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 2. वित्तीय सूचना</h4>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>संतुलन पत्र किस वित्तीय वर्ष तक बना है</label>
                                            <select name="sec_3_santulan_patra" id="sec_3_santulan_patra"
                                                class="form-control" tabindex="<?php echo $tab++; ?>">
                                                <option value="">
                                                    <?php echo '--Select--'; ?>
                                                </option>
                                                <?php
                                                if (date('m') > 3) {
                                                    $select_start_session = date('Y');
                                                } else {
                                                    $select_start_session = date('Y') - 1;
                                                }
                                                $session_start = date('Y') - 7;
                                                for ($i = $session_start; $i <= $session_start + 7; $i++) {
                                                    $end_session = $i + 1;
                                                    ?>
                                                    <option value="<?php echo $i . '-' . $end_session; ?>" <?php
                                                           if (isset($row_3_new_1['sec_3_santulan_patra']) && $row_3_new_1['sec_3_santulan_patra'] == $i . '-' . $end_session) {
                                                               echo 'selected';
                                                           }
                                                           ?>>
                                                        <?php echo $i . '-' . $end_session; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <small><b>(I) वित्तीय वर्ष 2021-22</b></small>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>वार्षिक लाभ/हानि की स्थिति</label>
                                            <select name="sec_3_profit_loss_1" id="sec_3_profit_loss_1"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="profit" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                <option value="loss" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>(धनराशि लाख मे)</label>
                                            <input type="text" name="sec_3_profit_loss_amount_1"
                                                id="sec_3_profit_loss_amount_1" tabindex="<?php echo $tab++; ?>"
                                                class="form-control chk_decimal"
                                                data-type="7.I वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                                value="<?php echo isset($row_3_new_1['profit_loss_amount_1']) ? $row_3_new_1['profit_loss_amount_1'] : ''; ?>">
                                        </div>
                                        <!-- <div class="col-sm-3 form-group">
                                            <label>संचित लाभ/हानि की स्थिति</label>
                                            <select name="sec_3_accumulated_1" id="sec_3_accumulated_1"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="profit" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                <option value="loss" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                            </select>
                                        </div> -->
                                        <!-- <div class="col-sm-3 form-group">
                                            <label>(धनराशि लाख मे)</label>
                                            <input type="text" name="sec_3_accumulated_amount_1"
                                                id="sec_3_accumulated_amount_1" tabindex="<?php echo $tab++; ?>"
                                                class="form-control chk_decimal"
                                                data-type="7.I वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                                value="<?php echo isset($row_3_new_1['accumulated_amount_1']) ? $row_3_new_1['accumulated_amount_1'] : ''; ?>">
                                        </div> -->
                                    </div>
                                    <small><b>(II) वित्तीय वर्ष 2022-23</b></small>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>वार्षिक लाभ/हानि की स्थिति</label>
                                            <select name="sec_3_profit_loss_2" id="sec_3_profit_loss_2"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="profit" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                <option value="loss" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>(धनराशि लाख मे)</label>
                                            <input type="text" name="sec_3_profit_loss_amount_2"
                                                id="sec_3_profit_loss_amount_2" tabindex="<?php echo $tab++; ?>"
                                                class="form-control chk_decimal"
                                                data-type="7.II वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                                value="<?php echo isset($row_3_new_1['profit_loss_amount_2']) ? $row_3_new_1['profit_loss_amount_2'] : ''; ?>">
                                        </div>
                                        <!-- <div class="col-sm-3 form-group">
                                            <label>संचित लाभ/हानि की स्थिति</label>
                                            <select name="sec_3_accumulated_2" id="sec_3_accumulated_2"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="profit" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                <option value="loss" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                            </select>
                                        </div> -->
                                        <!-- <div class="col-sm-3 form-group">
                                            <label>(धनराशि लाख मे)</label>
                                            <input type="text" name="sec_3_accumulated_amount_2"
                                                id="sec_3_accumulated_amount_2" tabindex="<?php echo $tab++; ?>"
                                                class="form-control chk_decimal"
                                                data-type="7.II वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                                value="<?php echo isset($row_3_new_1['accumulated_amount_2']) ? $row_3_new_1['accumulated_amount_2'] : ''; ?>">
                                        </div> -->
                                    </div>
                                    <small><b>(III)वित्तीय वर्ष 2023-24</b></small>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>वार्षिक लाभ/हानि की स्थिति</label>
                                            <select name="sec_3_profit_loss_3" id="sec_3_profit_loss_3"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="profit" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                <option value="loss" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>(धनराशि लाख मे)</label>
                                            <input type="text" name="sec_3_profit_loss_amount_3"
                                                id="sec_3_profit_loss_amount_3" tabindex="<?php echo $tab++; ?>"
                                                class="form-control chk_decimal"
                                                data-type="7.III वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                                value="<?php echo isset($row_3_new_1['profit_loss_amount_3']) ? $row_3_new_1['profit_loss_amount_3'] : ''; ?>">
                                        </div>
                                        <!-- <div class="col-sm-3 form-group">
                                            <label>संचित लाभ/हानि की स्थिति</label>
                                            <select name="sec_3_accumulated_3" id="sec_3_accumulated_3"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="profit" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                <option value="loss" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                            </select>
                                        </div> -->
                                        <!-- <div class="col-sm-3 form-group">
                                            <label>(धनराशि लाख मे)</label>
                                            <input type="text" name="sec_3_accumulated_amount_3"
                                                id="sec_3_accumulated_amount_3" tabindex="<?php echo $tab++; ?>"
                                                class="form-control chk_decimal"
                                                data-type="7.III लाभांश को धनराशि रु० लाख मे भरे"
                                                value="<?php echo isset($row_3_new_1['accumulated_amount_3']) ? $row_3_new_1['accumulated_amount_3'] : ''; ?>">
                                        </div> -->
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>आडिट किस वित्तीय वर्ष तक हुआ है</label>
                                            <select name="sec_3_financial_audit_year" class="form-control"
                                                tabindex="<?php echo $tab++; ?>">
                                                <option value="">
                                                    <?php echo '--Select--'; ?>
                                                </option>
                                                <?php
                                                if (date('m') > 3) {
                                                    $select_start_session = date('Y');
                                                } else {
                                                    $select_start_session = date('Y') - 1;
                                                }
                                                $session_start = date('Y') - 17;
                                                for ($i = $session_start; $i <= $session_start + 16; $i++) {
                                                    $end_session = $i + 1;
                                                    ?>
                                                    <option value="<?php echo $i . '-' . $end_session; ?>" <?php
                                                           if (isset($row_3_new_1['sec_3_financial_audit_year']) && $row_3_new_1['sec_3_financial_audit_year'] == $i . '-' . $end_session) {
                                                               echo 'selected';
                                                           }
                                                           ?>>
                                                        <?php echo $i . '-' . $end_session; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 form-group">
                                            <label>ऑडिट वर्गीकरण</label>
                                            <select name="sec_3_audit_grading" class="form-control"
                                                tabindex="<?php echo $tab++; ?>">
                                                <option value="">--select--</option>
                                                <option value="A" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'A' ? 'selected="selected"' : '' ?>>A
                                                </option>
                                                <option value="B" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'B' ? 'selected="selected"' : '' ?>>B
                                                </option>
                                                <option value="C" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'C' ? 'selected="selected"' : '' ?>>C
                                                </option>
                                                <option value="D" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'D' ? 'selected="selected"' : '' ?>>D
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-sm-2 form-group">
                                            <label>अनुपालन की स्थिति</label>
                                            <select name="sec_3_compliance_status" class="form-control"
                                                tabindex="<?php echo $tab++; ?>"
                                                onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes" <?php echo $row_3_new_1['sec_3_compliance_status'] == 'yes' ? 'selected="selected"' : '' ?> style="background:#0f0">हाँ
                                                </option>
                                                <option value="no" <?php echo $row_3_new_1['sec_3_compliance_status'] == 'no' ? 'selected="selected"' : '' ?> style="background:#f00">नहीं
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!--------------------------------------------------------------->

                                <div class="step">
                                    <h4>
                                        <img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        3. समिति भवन/सम्पत्ति का विवरण
                                    </h4>
                                    <div class="col-sm-12">
                                        <h5>(I) समिति भवन का स्वामित्व</h5>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>(I) समिति भवन का स्वामित्व</label>
                                                <select name="sec_3_ownership" id="sec_3_ownership"
                                                    class="form-control"
                                                    onchange="hide_show(this.value, '#sec_3_rented', 'rent');hide_show(this.value, '#sec_3_other', 'other');">
                                                    <option value="">--Select--</option>
                                                    <option value="own" <?= $row_new_plot['sec_3_ownership']=='own'?'selected':'' ?>>
                                                        समिति के स्वामित्व में है
                                                    </option>
                                                    <option value="rent" <?= $row_new_plot['sec_3_ownership']=='rent'?'selected':'' ?>>
                                                        किराये पर है
                                                    </option>
                                                    <option value="other" <?= !in_array($row_new_plot['sec_3_ownership'],['own','rent',''])?'selected':'' ?>>
                                                        अन्य स्थिति
                                                    </option>
                                                </select>
                                            </div>
                                            <div id="sec_3_rented" style="display: <?= $sec_3_rented_display ?? 'none' ?>; width:100%;">
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>समिति भवन का मासिक किराया</label>
                                                        <input name="sec_3_building_rent" class="form-control"
                                                            value="<?= $row_new_plot['sec_3_building_rent'] ?>">
                                                    </div>

                                                    <div class="col-sm-3 form-group">
                                                        <label>समिति भवन का क्षेत्रफल (स्क्वायर मीटर)</label>
                                                        <input name="sec_3_building_area" class="form-control"
                                                            value="<?= $row_new_plot['sec_3_building_area'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="sec_3_other" style="display: <?= $sec_3_other_display ?? 'none' ?>; width:100%;">
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>कृपया विवरण दर्ज करें</label>
                                                        <input name="sec_3_remark" class="form-control"
                                                            value="<?= $row_new_plot['society_building_remark'] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>समिति का मानचित्र स्वीकृत हैं अथवा नहीं?</label>
                                                <select name="sec_3_is_map" id="sec_3_is_map" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"
                                                    onchange="hide_show(this.value, '#sec_3_map_accept_1', 'yes'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo ($row_new_plot['sec_3_is_map'] == 'yes') ? 'selected="selected"' : ''; ?>>हाँ</option>
                                                    <option value="no" <?php echo ($row_new_plot['sec_3_is_map'] == 'no') ? 'selected="selected"' : ''; ?>>नहीं</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group" id="sec_3_map_accept_1" style="display: none;">
                                                <label>यदि स्वीकृत हैं तो किस प्राधिकारी द्वारा स्वीकृत है?</label>
                                                <input type="text" id="sec_3_map_accept" name="sec_3_map_accept"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_new_plot['sec_3_map_accept']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="sec_3" style="display: <?php echo $sec_3_display; ?>;">
                                                <div class="col-sm-12">
                                                    <h5> (II) भूखंड का विवरण </h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                            <label><small>&nbsp;</small></label>
                                                            <input type="text" name="sec_new_plot_area"
                                                                id="sec_new_plot_area" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control"
                                                                data-type="2.II. क्षेत्रफल (को हेक्टेयर में भरे)"
                                                                value="<?php echo $row_new_plot['sec_new_plot_area']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>राजस्व अभिलेख में दर्ज होने की
                                                                स्थिति(हाँ/नहीं)</label>
                                                            <label><small>&nbsp;</small></label>
                                                            <select name="sec_new_plot_revenue_status"
                                                                id="sec_new_plot_revenue_status"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#sec_new_plot_reason', 'no'); hide_show(this.value, '#sec_new_plot_if_not', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--Select--</option>
                                                                <option value="yes" <?php echo ($row_new_plot['sec_new_plot_revenue_status'] == 'yes') ? ' selected="selected"' : ''; ?>
                                                                    style="background:#0f0">हाँ</option>
                                                                <option value="no" <?php echo ($row_new_plot['sec_new_plot_revenue_status'] == 'no') ? ' selected="selected"' : ''; ?>
                                                                    style="background:#f00">नहीं</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_new_plot_reason"
                                                            style="display:none">
                                                            <label>दर्ज ना होने का कारण?</label>
                                                            <label><small>&nbsp;</small></label>
                                                            <input type="text" name="sec_new_plot_reason_for_not_record"
                                                                id="sec_new_plot_reason_for_not_record"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_new_plot['sec_new_plot_reason_for_not_record']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_new_plot_if_not"
                                                            style="display:none">
                                                            <label>यदि नहीं है तो किये जाने वाले प्रयास का विवरण</label>
                                                            <label><small>&nbsp;</small></label>
                                                            <input type="text" name="sec_new_plot_practices_if_not"
                                                                id="sec_new_plot_practices_if_not"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_new_plot['sec_new_plot_practices_if_not']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>गाटा/खसरा संख्या</label>
                                                            <input type="text" name="sec_new_plot_gata_no"
                                                                id="sec_new_plot_gata_no"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_new_plot['sec_new_plot_gata_no']; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <label>समिति भूखण्ड फोटो संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="society_photo" id="society_photo"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control">
                                                        </div>
                                                        <?php
                                                        if (!empty($row_invoice['photo_id'])) {
                                                            ?>
                                                        <div class="col-sm-2 form-group">
                                                            <img src="<?php echo $row_invoice['photo_id']; ?>"
                                                                class="img-fluid img-thumbnail" style="height:50px;"
                                                                id="society_photo_uploaded">
                                                            <label><a href="<?php echo $row_invoice['photo_id']; ?>"
                                                                    target="_blank">संलग्न फोटो देखें</a></label>

                                                        </div>
                                                        <?php
                                                        }
                                                        ?>

                                                        <div class="col-sm-3 form-group">
                                                            <label>टिप्पणी</label>
                                                            <input type="text" name="sec_new_remarks"
                                                                id="sec_new_remarks" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_new_plot['sec_new_remarks']; ?>">
                                                        </div>

                                                    </div>

                                            <h5>(III) वर्तमान मे कुल निर्मित भवनों / फ्लैट्स का विवरण</h5>
                                            <div class="col-md-12">
                                                <div id="building_rows">
                                                    <?php for ($i = 1; $i <= $row_3_9['count']; $i++) { ?>
                                                    <div class="row mb-2 building-row" data-index="<?php echo $i; ?>">
                                                        <div class="col-sm-3 form-group">
                                                            <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                            <input type="text" name="sec_3_flat_area_<?php echo $i; ?>"
                                                                id="sec_3_flat_area_<?php echo $i; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_3_9['sec_3_flat_area_' . $i]; ?>">
                                                        </div>

                                                        <div class="col-sm-3 form-group">
                                                            <label>भवन का प्रकार</label>
                                                            <select name="sec_3_flat_type_<?php echo $i; ?>"
                                                                id="sec_3_flat_type_<?php echo $i; ?>"
                                                                class="form-control">
                                                                <option value="">--select--</option>
                                                                <option value="inpremise" <?php if ($row_3_9['sec_3_flat_type_' . $i] == 'inpremise')
                                                                    echo 'selected'; ?>>आवासीय</option>
                                                                <option value="other" <?php if ($row_3_9['sec_3_flat_type_' . $i] == 'other')
                                                                    echo 'selected'; ?>>व्यावसायिक</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-2 form-group d-flex align-items-end">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-row">हटाएं</button>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                                <button type="button" class="btn btn-success mt-2" id="add_row_btn">+
                                                    नया पंक्ति जोड़ें</button>
                                            </div>

                                            <h5>(IV) वर्तमान मे अवशेष भूमि का विवरण</h5>
                                            <div id="sec_3_c">
                                                        <?php
                                                        // $row_3_5['sec_3_c_id'] = 1;
                                                        for ($i = 1; $i <= $row_3_5['sec_3_c_id']; $i++) {

                                                            ?>
                                                        <div class="row">
                                                            <div class="col-sm-2 form-group">
                                                                <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                                <input type="text"
                                                                    name="sec_3_c_length_<?php echo $i; ?>"
                                                                    id="sec_3_c_length_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control chk_decimal"
                                                                    data-type="2. VII. क्षेत्रफल हेक्टेयर में मे लिखे"
                                                                    value="<?php echo $row_3_5['sec_3_c_length_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>भूमि की स्थिति (उपजाऊ /बंजर)</label>
                                                                <select
                                                                    name="sec_3_c_vacant_land_status_<?php echo $i; ?>"
                                                                    id="sec_3_c_vacant_land_status_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select-- </option>
                                                                    <option value="fertile" <?php if ($row_3_5['sec_3_c_vacant_land_status_' . $i] == 'fertile') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        उपजाऊ </option>
                                                                    <option value="barren" <?php if ($row_3_5['sec_3_c_vacant_land_status_' . $i] == 'barren') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        बंजर </option>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-2 form-group">
                                                                <label>स्थान (समिति प्रांगण या अन्य स्थान)</label>
                                                                <select name="sec_3_c_land_location_<?php echo $i; ?>"
                                                                    id="sec_3_c_land_location_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    onChange="hide_show(this.value, '#land_connectivity_<?php echo $i; ?>', 'other');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="inpremise" <?php $land_location_display = 'none';
                                                                    if ($row_3_5['sec_3_c_land_location_' . $i] == 'inpremise') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>समिति प्रांगण
                                                                    </option>
                                                                    <option value="other" <?php if ($row_3_5['sec_3_c_land_location_' . $i] == 'other') {
                                                                        echo ' selected="selected"';
                                                                        $land_location_display = 'block';
                                                                    } ?>>अन्य
                                                                        स्थान
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <?php
                                                            if ($i == $row_3_5['sec_3_c_id']) {
                                                                ?>
                                                            <div class="col-sm-1 form-group my-auto" id="sec_3_c_rows">
                                                                <button type="button" class="btn btn-info"
                                                                    onClick="sec_3_c_add_rows();">नई पंक्ति
                                                                    जोड़े</button>
                                                                <input type="hidden" name="sec_3_c_id" id="sec_3_c_id"
                                                                    value="<?php echo $row_3_5['sec_3_c_id']; ?>">
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                        <?php } ?>
                                                    </div>

                                            <h5>(V) समिति परिसर मे स्कूल/ अस्पताल आदि की स्तिथि</h5>
                                            <div class="col-sm-3 form-group">
                                                <label> स्थिति (उपजाऊ /बंजर)</label>
                                                <select name="sec_8_school_hosp_status" id="sec_8_school_hosp_status"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                        <option value="">--Select--</option>
                                                        <option value="good" <?php echo ($row_2_1['sec_8_school_hosp_status'] == 'good') ? 'selected="selected"' : ''; ?>>अच्छा</option>
                                                        <option value="bad" <?php echo ($row_2_1['sec_8_school_hosp_status'] == 'bad') ? 'selected="selected"' : ''; ?>>खराब</option>
                                                    </select>
                                            </div>

                                            <h5>(VI) पहुंच मार्ग का विवरण</h5>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>पहुंच मार्ग</label>
                                                    <select name="sec_6_access_road" id="sec_6_access_road"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        onChange="hide_show(this.value, '#access_road', 'proper'); hide_show(this.value, '#access_road_truck', 'ordinary');">
                                                        <option value="">--Select--</option>
                                                        <option value="proper" <?php echo ($row_2_1['sec_6_access_road'] == 'proper') ? 'selected="selected"' : ''; ?>>पक्की सडक</option>
                                                        <option value="ordinary" <?php echo ($row_2_1['sec_6_access_road'] == 'ordinary') ? 'selected="selected"' : ''; ?>>कच्ची सडक</option>
                                                    </select>
                                                </div>

                                                <?php
                                                $access_road_display = ($row_2_1['sec_6_access_road'] == 'proper') ? 'flex' : 'none';
                                                $access_road_truck_display = ($row_2_1['sec_6_access_road'] == 'ordinary') ? 'flex' : 'none';
                                                ?>
                                                <div class="col-sm-2 form-group" id="access_road"
                                                    style="display: <?php echo $access_road_display; ?>;">
                                                    <label>पक्की सड़क का प्रकार</label>
                                                    <select name="sec_6_paved_road" id="sec_6_paved_road"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                        <option value="">--select--</option>
                                                        <option value="nh" <?php if ($row_2_1['sec_6_paved_road'] == 'nh')
                                                            echo 'selected'; ?>
                                                            >नेशनल हाईवे</option>
                                                        <option value="sh" <?php if ($row_2_1['sec_6_paved_road'] == 'sh')
                                                            echo 'selected'; ?>
                                                            >स्टेट हाईवे</option>
                                                        <option value="mdr" <?php if ($row_2_1['sec_6_paved_road'] == 'mdr')
                                                            echo 'selected'; ?>
                                                            >एम.डी.आर. (मेजर डिस्ट्रिक्ट रोड)</option>
                                                        <option value="odr" <?php if ($row_2_1['sec_6_paved_road'] == 'odr')
                                                            echo 'selected'; ?>
                                                            >ओ.डी.आर. (ऑर्डिनरी डिस्ट्रिक्ट ROAD)</option>
                                                        <option value="rural_road" <?php if ($row_2_1['sec_6_paved_road'] == 'rural_road')
                                                            echo 'selected'; ?>>ग्रामीण सड़क</option>
                                                        <option value="other" <?php if ($row_2_1['sec_6_paved_road'] == 'other')
                                                            echo 'selected'; ?>
                                                            >अन्य</option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 form-group" id="access_road_truck"
                                                    style="display: <?php echo $access_road_truck_display; ?>;">
                                                    <label>यदि समिति भवन तक ट्रक नही पहुंचता है तो पक्के मार्ग से
                                                        समिति भवन की दूरी (मी. में)</label>
                                                    <input type="text" name="sec_6_2_truck_not_reach"
                                                        id="sec_6_2_truck_not_reach" tabindex="<?php echo $tab++; ?>"
                                                        class="form-control"
                                                        value="<?php echo $row_2_1['sec_6_2_truck_not_reach']; ?>">
                                                </div>

                                                <!-- <div class="col-sm-2 form-group">
                                                        <label>भूखण्ड का फ्रंटेज् (आन रोड जमीन) मीटर में</label>
                                                        <input type="text" name="sec_8_plot_frontage"
                                                            id="sec_8_plot_frontage" tabindex="<?php echo $tab++; ?>"
                                                            class="form-control"
                                                            value="<?php echo $row_2_1['sec_8_plot_frontage']; ?>">
                                                    </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="success">
                                <div class="mt-5 text-center">
                                    <h4>प्रपत्र सफलता पूर्वक भरा गया</h4>
                                    <p>आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
                                        सत्यापन हेतु भेजा जाना है । कृप्या सत्यापन पर भेजने से पहले नीचे दर्शायें
                                        लिंक से फार्म खोल कर पुनः जाच कर लें । सब कुछ सही होने कि दशा में नीचे दिये
                                        बटन के माध्यम से सत्यापन के लिये आगे प्रेषित करें ।</p>
                                    <button class="btn btn-info" onclick="window.open('preview.php','_blank');">प्रपत्र
                                        पुनः
                                        निरीक्षण के लिये
                                        देखे</button>
                                </div>
                                <div class="col-md-12 text-center">
                                    <p><input type="checkbox" style="height: 20px; border:1px solid;" id="review_ack"
                                            onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
                                        मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी
                                        सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
                                    <button type="button" class="btn btn-danger" onClick="form_validate();"
                                        id="verification_button" disabled="disabled">सत्यापन के लिये आगे प्रेषित
                                        करें</button>
                                </div>

                                <div class="col-sm-12 form-group my-auto" id="send_otp_button1" style="display: none">
                                    <button type="button" name="send_otp_btn" id="send_otp_btn"
                                        tabindex="<?php echo $tab++; ?>" class="btn btn-info"
                                        onClick="send_otp($('#survey_id').val(), '');">ओ.टी.पी.
                                        भेजे</button>
                                </div>
                                <div class="col-sm-12 form-group" id="otp_verify" style="display: none">
                                    <div class="row">
                                        <div class="col-sm-4 form-group my-auto">
                                            <label>ओ.टी.पी. कोड दर्ज करें</label>
                                            <input type="text" class="form-control" id="user_otp">
                                        </div>
                                        <div class="col-sm-8 form-group my-auto">
                                            <button type="button" name="verify_otp_btn" id="verify_otp_btn"
                                                tabindex="<?php echo $tab++; ?>" class="btn btn-info"
                                                onClick="verify_otp($('#survey_id').val(), '', $('#user_otp').val());">वेरिफाई
                                                करें</button>
                                            <button type="button" name="send_otp_btn" id="send_otp_btn"
                                                tabindex="<?php echo $tab++; ?>" class="btn btn-info"
                                                onClick="send_otp($('#survey_id').val(), '');">पुनः ओ.टी.पी.
                                                भेजे</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <div id="q-<div id=" q-box__buttons">
                        <button id="prev-btn" class="btn btn-info" type="button"
                            onClick="save_draft()">Previous</button>
                        <button id="next-btn" class="btn btn-success" type="button" onClick="save_draft()">Next</button>
                        <button id="submit-btn" class="btn btn-danger" type="submit"
                            onClick="validate_input(); save_draft();">Submit</button>
                    </div>
                    <button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i>
                        Save
                        Draft</button>
                    <input type="hidden" id="term" name="term" value="a">
                    <input type="hidden" id="latitude" name="latitude" value="<?php echo $row_invoice['latitude']; ?>">
                    <input type="hidden" id="longitude" name="longitude"
                        value="<?php echo $row_invoice['longitude']; ?>">
                    <input type="hidden" id="id" name="id" value="submit_form_upavp">
                    <input type="hidden" id="current_step_count" name="current_step_count" value="">
                    <input type="hidden" id="survey_id" name="survey_id" value="<?php echo $row_invoice['sno']; ?>">
                    </form>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data"
                    id="otp_form" name="otp_form"></form>
            </div>
        </div>
    </div>
</div>
</div>

<div id="preloader-wrapper">
    <div id="preloader"></div>
    <div class="preloader-section section-left"></div>
    <div class="preloader-section section-right"></div>
</div>

<script>
    function save_draft() {
        var form = $("#user_form");
        var actionUrl = form.attr('action');
        $("#current_step_count").val(current_step);
        //console.log(form[0]);
        var formData = new FormData(form[0]);
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                data = JSON.parse(data);
                //data = data[0];
                //console.log(data);
                var err = 0;
                $.each(data, function (key, value) {
                    //console.log(value);
                    if (value.id == 'error') {
                        err = 1;
                        //alert(value.error);
                        $.notify({
                            icon: 'pe-7s-gift',
                            message: value.error

                        }, {
                            type: 'danger',
                            timer: 2000
                        });
                    }
                });
                if (err == 0) {
                    $.notify({
                        icon: 'pe-7s-gift',
                        message: 'Data Saved'

                    }, {
                        type: 'success',
                        timer: 2000
                    });
                }
            }
        });
    }

    $('select[multiple]').multiselect({
        columns: 1,
        placeholder: 'Select options'
    });

    $(document).ready(function () {
        //getLocation();
    });

    function hide_show(value, containerId, showValue) {
        var testServicesContainer = document.querySelector(containerId);
        if (Array.isArray(showValue)) {
            if (showValue.includes(value)) {
                testServicesContainer.style.display = 'block';
            } else {
                testServicesContainer.style.display = 'none';
            }
        }
        else {
            if (value === showValue) {
                testServicesContainer.style.display = 'block';
            } else {
                testServicesContainer.style.display = 'none';
            }
        }
    }
    hide_show(document.getElementById('sec_1_1_2_test').value, '#test_services', 'yes');
    document.getElementById('sec_1_1_2_test').addEventListener('change', function () {
        hide_show(this.value, '#test_services', 'yes');
    });

    function color_change(selectElement, yesValue, yesColor, noValue, noColor) {
        if (selectElement.value === yesValue) {
            selectElement.style.backgroundColor = yesColor;
        } else if (selectElement.value === noValue) {
            selectElement.style.backgroundColor = noColor;
        } else {
            selectElement.style.backgroundColor = 'white'; // Default background color
        }
    }

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            e.target.closest('.building-row').remove();
        }
    });
    $(document).ready(function () {
        let landRowIndex = $("#land_rows .land-row").length;

        // Add new row
        $("#add_land_row_btn").click(function () {
            landRowIndex++;
            let newRow = `
        <div class="row mb-2 land-row" data-index="${landRowIndex}">
            <div class="col-sm-3 form-group">
                <label>क्षेत्रफल (हेक्टेयर में)</label>
                <input type="text" name="sec_3_c_length_${landRowIndex}" id="sec_3_c_length_${landRowIndex}" class="form-control">
            </div>
            <div class="col-sm-3 form-group">
                <label>भूमि की स्थिति (उपजाऊ / बंजर)</label>
                <select name="sec_3_c_vacant_land_status_${landRowIndex}" id="sec_3_c_vacant_land_status_${landRowIndex}" class="form-control">
                    <option value="">--select--</option>
                    <option value="fertile">उपजाऊ</option>
                    <option value="barren">बंजर</option>
                </select>
            </div>
            <div class="col-sm-3 form-group">
                <label>स्थान (समिति प्रांगण या अन्य स्थान)</label>
                <select name="sec_3_c_land_location_${landRowIndex}" id="sec_3_c_land_location_${landRowIndex}" class="form-control">
                    <option value="">--select--</option>
                    <option value="inpremise">समिति प्रांगण</option>
                    <option value="other">अन्य स्थान</option>
                </select>
            </div>
            <div class="col-sm-2 form-group d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-land-row">हटाएं</button>
            </div>
        </div>`;
            $("#land_rows").append(newRow);
        });

        // Remove row
        $(document).on("click", ".remove-land-row", function () {
            $(this).closest(".land-row").remove();
        });
    });

    function sec_3_c_land_add_rows() {
        let last_id = parseInt(document.getElementById('sec_3_c_land_id').value);
        let new_id = last_id + 1;

        let html = `
    <div class="row">
        <div class="col-sm-3 form-group">
            <label>क्षेत्रफल (हेक्टेयर में)</label>
            <input type="text" name="sec_3_c_area_${new_id}" id="sec_3_c_area_${new_id}" tabindex="1" class="form-control chk_number">
        </div>

        <div class="col-sm-3 form-group">
            <label>भूमि की स्थिति (उपजाऊ / बंजर)</label>
            <select name="sec_3_c_land_status_${new_id}" id="sec_3_c_land_status_${new_id}" tabindex="1" class="form-control">
                <option value="">--select--</option>
                <option value="fertile">उपजाऊ</option>
                <option value="barren">बंजर</option>
            </select>
        </div>

        <div class="col-sm-3 form-group">
            <label>स्थान (समिति प्रांगण या अन्य स्थान)</label>
            <select name="sec_3_c_land_location_${new_id}" id="sec_3_c_land_location_${new_id}" tabindex="1" class="form-control">
                <option value="">--select--</option>
                <option value="inpremise">समिति प्रांगण</option>
                <option value="other">अन्य स्थान</option>
            </select>
        </div>

        <div class="col-sm-1 form-group my-auto" id="sec_3_c_land_rows">
            <button type="button" class="btn btn-info" onclick="sec_3_c_land_add_rows()">नई पंक्ति जोड़े [+]</button>
            <input type="hidden" name="sec_3_c_land_id" id="sec_3_c_land_id" value="${new_id}">
        </div>
    </div>`;

        document.getElementById('sec_3_c_land_rows').remove(); // remove old button
        document.getElementById('sec_3_c_land_details').insertAdjacentHTML('beforeend', html);
    }
</script>
<script>
    let rowCount = 1;

    document.getElementById('add_row_btn').addEventListener('click', function () {
        rowCount++;

        const newRow = document.createElement('div');
        newRow.classList.add('row', 'mb-2', 'building-row');
        newRow.setAttribute('data-index', rowCount);

        newRow.innerHTML = `
        <div class="col-sm-3 form-group">
            <label>क्षेत्रफल (हेक्टेयर में)</label>
            <input type="text" name="sec_3_flat_area_${rowCount}" id="sec_3_flat_area_${rowCount}" class="form-control">
        </div>

        <div class="col-sm-3 form-group">
            <label>भवन का प्रकार</label>
            <select name="sec_3_flat_type_${rowCount}" id="sec_3_flat_type_${rowCount}" class="form-control">
                <option value="">--select--</option>
                <option value="inpremise">आवासीय</option>
                <option value="other">व्यावसायिक</option>
            </select>
        </div>

        <div class="col-sm-2 form-group d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-sm remove-row">हटाएं</button>
        </div>`;

        document.getElementById('building_rows').appendChild(newRow);
    });

    function sec_3_c_add_rows() {
        var id = parseFloat($("#sec_3_c_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 0; i <= id; i++) {
            if ($("#sec_3_c_length_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_3_c_length_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#sec_3_c_rows").remove();

        var txt = '<div class="row"><div class="col-sm-2 form-group"><label>क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="sec_3_c_length_' + id + '" id="sec_3_c_length_' + id + '" class="form-control chk_number" data-type="क्षेत्रफल हेक्टेयर में मे लिखे"></div><div class="col-sm-2 form-group"><label>भूमि की स्थिति (उपजाऊ /बंजर)</label><select name="sec_3_c_vacant_land_status_' + id + '" id="sec_3_c_vacant_land_status_' + id + '" class="form-control"><option value="">--select-- </option><option value="fertile">उपजाऊ </option><option value="barren">बंजर </option></select></div><div class="col-sm-2 form-group"><label>स्थान (समिति प्रांगण या अन्य स्थान)</label><select name="sec_3_c_land_location_' + id + '" id="sec_3_c_land_location_' + id + '" class="form-control"><option value="">--select-- </option><option value="inpremise">समिति प्रांगण </option><option value="other">अन्य स्थान </option></select></div><div class="col-sm-2 form-group my-auto" id="sec_3_c_rows"><button type="button" class="btn btn-info" onClick="sec_3_c_add_rows();">नई पंक्ति जोड़े</button><input type="hidden" name="sec_3_c_id" id="sec_3_c_id" value="' + id + '"></div></div>';

        $("#sec_3_c").append(txt);
    }
</script>


<script type="text/javascript" src="js/multistepform_upavp.js?v=1">
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    < script src="js/light-bootstrap-dashboard.js?v=1.4.0">
</script>

<?php
page_footer_start();
?>