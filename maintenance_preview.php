<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// echo '22222222222222';
// echo $_SESSION['survey_id'];
$msg = '';
$tab = 1;
$row_invoice = [];
$row_5 = [];
$visit_id = 0;
$soc_type_map = ['block_union' => 'block_union_id', 'marketing' => 'marketing_id', 'upss' => 'upss_id', 'jila_sehkari' => 'jila_sehkari_id', 'consumer' => 'consumer_id'];
$society_type = strtolower(trim($_GET['type'] ?? $_SESSION['society_type'] ?? 'block_union'));
$society_type = str_replace(' ', '_', $society_type);
$section_1_labels = ['block_union' => 'ब्लॉक यूनियन (सहकारी संघ) का विवरण', 'jila_sehkari' => 'जिला सहकारी विकास संघ का विवरण', 'marketing' => 'सहकारी क्रय-विक्रय का विवरण', 'upss' => 'केंद्रिय उपभोक्ता संघ का विवरण', 'consumer' => 'उपभोक्ता आयोग के समिति का विवरण'];
$display_section_1_label = $section_1_labels[$society_type] ?? 'समिति का विवरण';

if (isset($_GET['exdid'])) {
    $society_id = (int) $_GET['exdid'];
    $society_info_sql = "";
    switch ($society_type) {
        case 'marketing':
            $society_info_sql = "SELECT s.*, s.society_name as soc_name, md.division_name, mdist.district_name, NULL as tehseel_name, NULL as block_name FROM marketing s LEFT JOIN master_division md ON md.sno = s.division_id LEFT JOIN master_district mdist ON mdist.sno = s.district_id WHERE s.is_deleted = 0 AND s.sno = '$society_id'";
            $type_label = "Marketing";
            $soc_type_sno_val = 2;
            break;
        case 'block_union':
            $society_info_sql = "SELECT s.*, s.samiti_naam as soc_name, md.division_name, mdist.district_name, NULL as tehseel_name, NULL as block_name FROM block_union s LEFT JOIN master_division md ON md.sno = s.mandal_name LEFT JOIN master_district mdist ON mdist.sno = s.janpad_name WHERE s.is_deleted = 0 AND s.sno = '$society_id'";
            $type_label = "Block Union";
            $soc_type_sno_val = 1;
            break;
        case 'upss':
            $society_info_sql = "SELECT s.*, s.society_name as soc_name, md.division_name, mdist.district_name, NULL as tehseel_name, NULL as block_name FROM upss s LEFT JOIN master_division md ON md.sno = s.mandal_name LEFT JOIN master_district mdist ON mdist.sno = s.janpad_name WHERE s.is_deleted = 0 AND s.sno = '$society_id'";
            $type_label = "Consumer";
            $soc_type_sno_val = 3;
            break;
        case 'jila_sehkari':
            $society_info_sql = "SELECT s.*, s.society_name as soc_name, md.division_name, mdist.district_name, NULL as tehseel_name, NULL as block_name FROM jila_sehkari s LEFT JOIN master_division md ON md.sno = s.mandal_name LEFT JOIN master_district mdist ON mdist.sno = s.janpad_name WHERE s.is_deleted = 0 AND s.sno = '$society_id'";
            $type_label = "Jila Sehkari";
            $soc_type_sno_val = 4;
            break;
    }

    if ($society_info_sql != "") {
        $res_soc = execute_query($society_info_sql);
        if ($row_soc = mysqli_fetch_assoc($res_soc)) {
            $row_invoice = $row_soc;
            $row_invoice['society_name'] = $row_soc['soc_name'];
            $row_invoice['type_name'] = $type_label;
            $row_invoice['society_type'] = $type_label;
            $row_invoice['society_id'] = $society_id;

            if ($society_type == 'marketing') {
                $row_invoice['col1'] = $row_soc['division_id'];
                $row_invoice['col2'] = $row_soc['district_id'];
                $row_invoice['col3'] = $soc_type_sno_val;
                $row_invoice['col5'] = '';
                $row_invoice['col6'] = '';
            } elseif ($society_type == 'consumer') {
                $row_invoice['col1'] = $row_soc['col1'] ?? '';
                $row_invoice['col2'] = $row_soc['col2'] ?? '';
                $row_invoice['col3'] = $soc_type_sno_val;
                $row_invoice['col5'] = '';
                $row_invoice['col6'] = $row_soc['col6'] ?? '';
            } else {
                $row_invoice['col1'] = $row_soc['mandal_name'] ?? '';
                $row_invoice['col2'] = $row_soc['janpad_name'] ?? '';
                $row_invoice['col3'] = $soc_type_sno_val;
                $row_invoice['col5'] = '';
                $row_invoice['col6'] = '';
            }

            $res_m = execute_query("SELECT * FROM maintenance WHERE society_id='$society_id' ORDER BY sno DESC LIMIT 1");
            if ($res_m && mysqli_num_rows($res_m) > 0) {
                $row_m = mysqli_fetch_assoc($res_m);
                $row_invoice = array_merge($row_invoice, $row_m);
                $visit_id = $row_m['sno'];
                $_SESSION['survey_id'] = $visit_id;
                $row_invoice['visit_id'] = $visit_id;

                $res_f = execute_query("SELECT * FROM maintenance_financial_info WHERE maintenance_sno='$visit_id' ORDER BY sno ASC");

                $fi = 1;

                while ($row_f = mysqli_fetch_assoc($res_f)) {

                    if ($fi == 1) {
                        $row_invoice['sec_3_santulan_patra'] = $row_f['financial_year'];
                    }

                    $row_invoice["profit_loss_$fi"] = $row_f['fy_profit_loss'];
                    $row_invoice["profit_loss_amount_$fi"] = $row_f['fy_profit_loss_amt'];

                    $row_invoice["accumulated_$fi"] = $row_f['comm_profit_loss'];
                    $row_invoice["accumulated_amount_$fi"] = $row_f['comm_profit_loss_amt'];

                    $row_invoice["financial_year_$fi"] = $row_f['financial_year'];

                    $fi++;
                }

                $row_invoice['financial_year_count'] = max(2, $fi - 1);
                $row_invoice['visit_id'] = $visit_id;

                $res_c = execute_query("SELECT * FROM maintenance_committee_info WHERE maintenance_sno = '$visit_id' ORDER BY sno ASC");
                $ci = 1;
                while ($row_c = mysqli_fetch_assoc($res_c)) {
                    $row_invoice['sec_6_2_mgt_committee_is_elected'] = $row_c['mgt_committee_is_elected'];
                    $row_invoice['sec_6_2_election_year'] = $row_c['election_year'];
                    $row_invoice['sec_6_2_end_year'] = $row_c['end_year'];
                    $row_invoice["sec_6_2_designation_$ci"] = $row_c['designation'];
                    $row_invoice["sec_6_2_name_$ci"] = $row_c['name'];
                    $row_invoice["sec_6_2_father_name_$ci"] = $row_c['father_name'];
                    $row_invoice["sec_6_2__mob_no_$ci"] = $row_c['mobile_no'];
                    $ci++;
                }
                if ($ci > 1)
                    $row_invoice['sec_6_2_member_count'] = $ci - 1;
            } else {
                $_SESSION['survey_id'] = '';
                $visit_id = 0;
            }

            if (($row_invoice['society_registration_date'] ?? '') == '')
                $row_invoice['society_registration_date'] = date("Y-m-d");
            if (($row_invoice['committee_date'] ?? '') == '')
                $row_invoice['committee_date'] = date("Y-m-d");

            $soc_type_sno_map = ['block_union' => 1, 'marketing' => 2, 'upss' => 3, 'jila_sehkari' => 4, 'consumer' => 5];
            $soc_type_sno_val = $soc_type_sno_map[$society_type] ?? 1;

            $row_5 = $row_invoice;
            $row_5['sec_5_built_building'] = $row_invoice['built_building_status'] ?? '';
            $row_5['sec_5_detailed_information'] = $row_invoice['built_building_details'] ?? '';
            $row_5['boundary_wall_status'] = $row_invoice['boundary_wall_status'] ?? '';
            $row_5['boundary_wall_cost'] = $row_invoice['boundary_wall_cost'] ?? '';
            $row_5['sec_6_a_length'] = $row_invoice['floor_length'] ?? '';
            $row_5['sec_6_a_width'] = $row_invoice['floor_width'] ?? '';
            $row_5['sec_6_b_length'] = $row_invoice['wall_length'] ?? '';
            $row_5['sec_6_b_width'] = $row_invoice['wall_width'] ?? '';
            $row_5['sec_6_c_length'] = $row_invoice['paint_length'] ?? '';
            $row_5['sec_6_c_width'] = $row_invoice['paint_width'] ?? '';
            $row_5['sec_6_d_length'] = $row_invoice['roof_length'] ?? '';
            $row_5['sec_6_d_width'] = $row_invoice['roof_width'] ?? '';
            $row_5['sec_6_e_floor'] = $row_invoice['washroom_floor'] ?? '';
            $row_5['sec_6_e_plaster'] = $row_invoice['washroom_plaster'] ?? '';
            $row_5['sec_6_e_ceiling'] = $row_invoice['washroom_roof'] ?? '';
            $row_5['sec_6_e_seat'] = $row_invoice['washroom_seat'] ?? '';
            $row_5['sec_6_e_plumbing'] = $row_invoice['washroom_plumbing'] ?? '';
            $row_5['sec_6_f_number_of_door'] = $row_invoice['doors'] ?? '';
            $row_5['sec_6_g_number_of_window'] = $row_invoice['windows'] ?? '';
            $row_5['sec_6_h_length'] = $row_invoice['plaster_wall'] ?? '';
            $row_5['sec_6_h_width'] = $row_invoice['plaster_roof'] ?? '';
            $row_5['sec_6_i_other'] = $row_invoice['others'] ?? '';
            $row_5['sec_6_f_door_cost'] = $row_invoice['door_cost'] ?? '';
            $row_5['sec_6_f_window_cost'] = $row_invoice['window_cost'] ?? '';
            $row_5['sec_6_a_floor_cost'] = $row_invoice['floor_cost'] ?? '';
            $row_5['sec_6_a_wall_cost'] = $row_invoice['wall_cost'] ?? '';
            $row_5['sec_6_a_paint_cost'] = $row_invoice['paint_cost'] ?? '';
            $row_5['sec_6_a_roof_cost'] = $row_invoice['roof_cost'] ?? '';
            $row_5['sec_6_e_floor_cost'] = $row_invoice['wr_floor_cost'] ?? '';
            $row_5['sec_6_e_plaster_cost'] = $row_invoice['wr_plaster_cost'] ?? '';
            $row_5['sec_6_e_ceiling_cost'] = $row_invoice['wr_roof_cost'] ?? '';
            $row_5['sec_6_e_seat_cost'] = $row_invoice['wr_seat_cost'] ?? '';
            $row_5['sec_6_e_plumbing_cost'] = $row_invoice['wr_plumbing_cost'] ?? '';

            $d_id = $row_invoice['district_id'] ?? $row_invoice['col2'] ?? '';
            $b_id = $row_invoice['block_id'] ?? $row_invoice['col6'] ?? '';

            $img_fields = ['floor_img_1' => 'sec_6_a_img_1', 'floor_img_2' => 'sec_6_a_img_2', 'floor_img_3' => 'sec_6_a_img_3', 'floor_img_4' => 'sec_6_a_img_4', 'wall_img_1' => 'sec_6_b_img_1', 'wall_img_2' => 'sec_6_b_img_2', 'wall_img_3' => 'sec_6_b_img_3', 'wall_img_4' => 'sec_6_b_img_4', 'paint_img_1' => 'sec_6_c_img_1', 'paint_img_2' => 'sec_6_c_img_2', 'paint_img_3' => 'sec_6_c_img_3', 'paint_img_4' => 'sec_6_c_img_4', 'roof_img_1' => 'sec_6_d_img_1', 'roof_img_2' => 'sec_6_d_img_2', 'roof_img_3' => 'sec_6_d_img_3', 'roof_img_4' => 'sec_6_d_img_4', 'wr_floor_img_1' => 'sec_6_e_img_1', 'wr_floor_img_2' => 'sec_6_e_img_2', 'wr_plaster_img_1' => 'sec_6_f_img_1', 'wr_plaster_img_2' => 'sec_6_f_img_2', 'wr_roof_img_1' => 'sec_6_g_img_1', 'wr_roof_img_2' => 'sec_6_g_img_2', 'wr_seat_img_1' => 'sec_6_h_img_1', 'wr_seat_img_2' => 'sec_6_h_img_2', 'wr_plumbing_img_1' => 'sec_6_i_img_1', 'wr_plumbing_img_2' => 'sec_6_i_img_2', 'boundary_wall_img_1' => 'boundary_wall_img_1', 'boundary_wall_img_2' => 'boundary_wall_img_2'];
            foreach ($img_fields as $db_f => $form_f) {
                if (($row_invoice[$db_f] ?? '') != '') {
                    $row_5[$form_f] = 'user_data/maintainance/' . $row_invoice[$db_f];
                } else {
                    $row_5[$form_f] = '';
                }
            }
        }
    }
} else {
    $row_5 = [];
    $fields = ['sec_5_built_building', 'sec_5_detailed_information', 'boundary_wall_status', 'boundary_wall_cost', 'sec_6_a_length', 'sec_6_a_width', 'sec_6_b_length', 'sec_6_b_width', 'sec_6_c_length', 'sec_6_c_width', 'sec_6_d_width', 'sec_6_d_length', 'sec_6_e_floor', 'sec_6_e_plaster', 'sec_6_e_ceiling', 'sec_6_e_seat', 'sec_6_e_plumbing', 'sec_6_f_number_of_door', 'sec_6_g_number_of_window', 'sec_6_h_length', 'sec_6_h_width', 'sec_6_i_other', 'sec_6_a_floor_cost', 'sec_6_a_wall_cost', 'sec_6_a_paint_cost', 'sec_6_a_roof_cost', 'sec_6_e_floor_cost', 'sec_6_e_plaster_cost', 'sec_6_e_ceiling_cost', 'sec_6_e_seat_cost', 'sec_6_e_plumbing_cost', 'sec_6_f_door_cost', 'sec_6_f_window_cost'];
    foreach ($fields as $f)
        $row_5[$f] = '';
    $img_fields_form = ['sec_6_a_img', 'sec_6_a_img_1', 'sec_6_a_img_2', 'sec_6_a_img_3', 'sec_6_a_img_4', 'sec_6_b_img', 'sec_6_b_img_1', 'sec_6_b_img_2', 'sec_6_b_img_3', 'sec_6_b_img_4', 'sec_6_c_img', 'sec_6_c_img_1', 'sec_6_c_img_2', 'sec_6_c_img_3', 'sec_6_c_img_4', 'sec_6_d_img', 'sec_6_d_img_1', 'sec_6_d_img_2', 'sec_6_d_img_3', 'sec_6_d_img_4', 'sec_6_e_img_1', 'sec_6_e_img_2', 'sec_6_f_img_1', 'sec_6_f_img_2', 'sec_6_g_img_1', 'sec_6_g_img_2', 'sec_6_h_img_1', 'sec_6_h_img_2', 'sec_6_i_img_1', 'sec_6_i_img_2', 'boundary_wall_img_1', 'boundary_wall_img_2'];
    foreach ($img_fields_form as $f)
        $row_5[$f] = '';
}

$res_div = execute_query("SELECT sno, division_name FROM master_division ORDER BY division_name");
$divisions = [];
while ($row = mysqli_fetch_assoc($res_div))
    $divisions[] = $row;
page_header_start();
page_sidebar();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/survey_validate.js?v=1.4.0"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .blinking-text {
        animation: blinker 1.5s linear infinite;
        font-weight: bold;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }

    .date-field {
        display: none;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

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

    .table-section td:first-child,
    .table-section th:first-child {
        border-left-color: #000;
    }

    .table-section td:last-child,
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

    .select-default {
        background-color: white;
    }

    .success {
        border: 3px solid #0f0;
    }

    .card-body {
        background: white;
        border-radius: 0 0 10px 10px;
    }

    .step-fixed {
        background: white;
        padding: 20px;
    }

    .section-header {
        background: #3498db;
        color: white;
        padding: 12px 25px;
        font-weight: 600;
        margin-top: 25px;
        margin-bottom: 25px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        font-size: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .section-header .icon-box {
        background: white;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }

    .section-header .icon-box img {
        height: 28px;
    }

    .form-group label {
        font-weight: 700;
        color: #333;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .form-control-sm {
        border-radius: 6px;
        border: 1px solid #ced4da;
        height: 38px;
        background: white !important;
    }

    .info-label {
        font-weight: 700;
        color: #333;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .info-value-box {
        background: #e8f4f1;
        padding: 10px 15px;
        border-radius: 6px;
        font-weight: 600;
        color: #2c3e50;
        min-height: 40px;
        display: flex;
        align-items: center;
        border: 1px solid #d1e7e4;
    }

    #action-buttons {
        margin-top: 30px;
        padding: 20px 0;
        background: transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        width: 100%;
        clear: both;
    }

    .btn-save-draft,
    .btn-next {
        width: 220px;
        padding: 12px 20px;
        font-weight: 700;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.1s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .btn-save-draft {
        background: #f39c12;
        color: white;
        box-shadow: 0 4px 0 #d35400;
    }

    .btn-save-draft:active {
        transform: translateY(2px);
        box-shadow: 0 2px 0 #d35400;
    }

    .btn-next {
        background: #27ae60;
        color: white;
        box-shadow: 0 4px 0 #219150;
        display: none;
    }

    .btn-next:active {
        transform: translateY(2px);
        box-shadow: 0 2px 0 #219150;
    }

    .step-fixed h5 {
        color: #2c3e50;
        border-left: 5px solid #3498db;
        padding: 8px 15px;
        background: #f0f7fe;
        margin-top: 25px;
        margin-bottom: 15px;
        font-weight: 700;
        border-radius: 4px;
    }

    body,
    .wrapper,
    .main-panel {
        background-color: #f4f7f6 !important;
    }

    .table-6-1 {
        width: 100%;
        table-layout: auto;
    }

    .table-6-1 th,
    .table-6-1 td {
        padding: 15px;
        text-align: center;
        white-space: nowrap;
    }

    .table-6-1 th:nth-child(1),
    .table-6-1 td:nth-child(1) {
        min-width: 120px;
    }

    .table-6-1 th:nth-child(2),
    .table-6-1 td:nth-child(2) {
        min-width: 120px;
    }

    .table-6-1 th:nth-child(3),
    .table-6-1 td:nth-child(3) {
        min-width: 200px;
    }

    .table-6-1 th:nth-child(4),
    .table-6-1 td:nth-child(4) {
        min-width: 200px;
    }

    .table-6-1 th:nth-child(5),
    .table-6-1 td:nth-child(5) {
        min-width: 200px;
    }

    .table-6-1 th:nth-child(6),
    .table-6-1 td:nth-child(6) {
        min-width: 150px;
    }

    .blinking-text {
        animation: blink 1s step-start 0s infinite;
        color: red;
    }

    @keyframes blink {
        50% {
            opacity: 0;
        }
    }
</style>
<?php page_header_end(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex my-auto">
                    <div class="col-md-12">
                        <div class="progress">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50"
                                class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                role="progressbar" style="width: 0%"></div>
                        </div>
                        <form action="scripts/ajax_maintenance.php" method="post" enctype="multipart/form-data"
                            id="user_form" name="user_form">
                            <input type="hidden" name="survey_id" id="survey_id"
                                value="<?php echo htmlspecialchars($visit_id); ?>">
                            <div id="steps-container">
                                <div class="step-fixed">
                                    <marquee
                                        style="font-size: 16px; color: #3498db; font-weight: 500; background: #fff8f8; padding: 5px 0; border-bottom: 1px solid #f00;">
                                        नोट: जिन इनपुट फ़ील्ड्स पर लाल रंग <span style="color:red;">(*)</span> का चिन्ह
                                        लगा है, उन्हें भरना अनिवार्य है। यदि ये जानकारी नहीं भरी जाएगी, तो फ़ॉर्म सबमिट
                                        नहीं होगा।
                                    </marquee>
                                    <?php echo $msg; ?>
                                    <div class="section-header mt-4"
                                        style="background: #cfe6fc; color:#2c3e50; border-left:10px solid #3498db; border-radius:20px 15px 15px 0;">
                                        <img src="images/logo/3.png" alt="text" class="img-fluid mr-2"
                                            style="height:30px; width:30px;">
                                        1.<?php echo $display_section_1_label ?? 'समिति का विवरण'; ?>
                                    </div>
                                    <div class="row no-gutters align-items-center d-flex flex-nowrap">
                                        <div class="col-5 pr-3">
                                            <div class="row mb-2 align-items-center">
                                                <div class="col-5 info-label">मण्डल का नाम</div>
                                                <div class="col-7 info-value-box">
                                                    <?php echo $row_invoice['division_name'] ?? ''; ?></div>
                                            </div>
                                            <div class="row mb-2 align-items-center">
                                                <div class="col-5 info-label">जनपद का नाम</div>
                                                <div class="col-7 info-value-box">
                                                    <?php echo $row_invoice['district_name'] ?? ''; ?></div>
                                            </div>
                                            <div class="row mb-2 align-items-center">
                                                <div class="col-5 info-label">LATITUDE<span style="color:red;">*</span>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" id="lat" readonly required
                                                        value="<?php echo $row_invoice['latitude'] ?? ''; ?>"
                                                        class="form-control form-control-sm bg-light-input">
                                                </div>
                                            </div>
                                            <div class="row mb-2 align-items-center">
                                                <div class="col-5 info-label">LONGITUDE<span style="color:red;">*</span>
                                                </div>
                                                <div class="col-7">
                                                    <input type="text" id="long" readonly required
                                                        value="<?php echo $row_invoice['longitude'] ?? ''; ?>"
                                                        class="form-control form-control-sm bg-light-input">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-5"></div>
                                                <div class="col-7">
                                                    <button type="button" class="btn btn-info btn-sm btn-block"
                                                        onClick="getLocation();"
                                                        style="border-radius:4px; font-weight:bold;">लोकेशन रिफ्रेश
                                                        करें</button>
                                                    <div class="status-alert"
                                                        style="font-size:11px; color:#e74c3c; margin-top:5px; font-weight:500;">
                                                        (लोकेशन मोबाईल से भरे)*</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7" id="map_container">
                                            <iframe id="googlemap"
                                                src="https://maps.google.com/maps?output=embed&z=13&q=<?php echo ($row_invoice['latitude'] ?? '26.8467') . ',' . ($row_invoice['longitude'] ?? '80.9462'); ?>"
                                                width="100%" height="250"
                                                style="border:6px solid white; border-radius:15px; box-shadow: 0 8px 20px rgba(0,0,0,0.12); background:#f9f9f9;"
                                                allowfullscreen="" loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-4">
                                            <div class="info-label">समिति का नाम</div>
                                            <div class="info-value-box">
                                                <input type="text" name="society_name" id="society_name"
                                                    style="width: 100%;"
                                                    value="<?php echo $row_invoice['society_name'] ?? ''; ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                            <label>निबंधन संख्या<span style="color:red;">*</span></label>
                                            <input type="text" name="society_registration_no"
                                                id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                class="form-control" required data-type="निबंधन संख्या"
                                                value="<?php echo ($row_invoice['society_registration_no'] ?? ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>निबंधन दिनांक<span style="color:red;">*</span></label>
                                            <input type="date" name="society_registration_date"
                                                id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                class="form-control" required data-type="निबंधन दिनांक"
                                                value="<?php echo ($row_invoice['society_registration_date'] ?? ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>जी०एस०टी०एन० न०</label>
                                            <input type="text" name="gst_no" id="gst_no" maxlength="15"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo ($row_invoice['gst_no'] ?? ''); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>पैन न०</label>
                                            <input type="text" name="pan_no" id="pan_no" maxlength="10"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo ($row_invoice['pan_no'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="section-header mt-4"
                                        style="background: #cfe6fc; color:#2c3e50; border-left:10px solid #3498db; border-radius:20px 15px 15px 0;">
                                        <img src="images/logo/3.png" alt="text" class="img-fluid mr-2"
                                            style="height:30px; width:30px;">
                                        2. सचिव का विवरण
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>नाम<span style="color:red;">*</span></label>
                                            <input type="text" name="secretary_name" id="secretary_name"
                                                tabindex="<?php echo $tab++; ?>" class="form-control chk_text" required
                                                data-type="सचिव का नाम"
                                                value="<?php echo $row_invoice['secretary_name'] ?? ''; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>मोबाइल नंबर<span style="color:red;">*</span></label>
                                            <input type="text" name="secretary_mobile" id="secretary_mobile"
                                                tabindex="<?php echo $tab++; ?>" class="form-control chk_mobile"
                                                maxlength="10" required data-type="सचिव का मोबाइल नंबर"
                                                value="<?php echo $row_invoice['secretary_mobile'] ?? ''; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>आधार नंबर<span style="color:red;">*</span></label>
                                            <input type="text" name="secretary_aadhar" id="secretary_aadhar"
                                                tabindex="<?php echo $tab++; ?>" class="form-control" maxlength="12"
                                                required data-type="आधार नंबर"
                                                value="<?php echo $row_invoice['secretary_aadhar'] ?? ''; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>ईमेल आईडी</label>
                                            <input type="email" name="secretary_email" id="secretary_email"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo $row_invoice['secretary_email'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="section-header mt-4"
                                        style="background: #cfe6fc; color:#2c3e50; border-left:10px solid #3498db; border-radius:20px 15px 15px 0;">
                                        <img src="images/logo/3.png" alt="text" class="img-fluid mr-2"
                                            style="height:30px; width:30px;">
                                        3. वित्तीय सूचना
                                    </div>
                                    <div id="financial_years_container">
                                        <?php
                                        $res_f = execute_query("SELECT * FROM maintenance_financial_info WHERE maintenance_sno='$visit_id' ORDER BY sno ASC");

                                        $rows = [];
                                        while ($r = mysqli_fetch_assoc($res_f)) {
                                            $rows[] = $r;
                                        }

                                        if (count($rows) == 0) {
                                            $rows[] = [
                                                'financial_year' => '2024-25',
                                                'fy_profit_loss' => '',
                                                'fy_profit_loss_amt' => '',
                                                'comm_profit_loss' => '',
                                                'comm_profit_loss_amt' => ''
                                            ];

                                            $rows[] = [
                                                'financial_year' => '2025-26',
                                                'fy_profit_loss' => '',
                                                'fy_profit_loss_amt' => '',
                                                'comm_profit_loss' => '',
                                                'comm_profit_loss_amt' => ''
                                            ];
                                        }

                                        $romans = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X'];

                                        foreach ($rows as $k => $row_f) {

                                            $i = $k + 1;
                                            ?>
                                            <div class="row mt-2 financial-year-row" id="year_row_<?php echo $i; ?>">

                                                <div class="col-sm-12 mb-2">
                                                    <b>(<?php echo $romans[$i] ?? $i; ?>) वित्तीय वर्ष</b>

                                                    <input type="text" name="financial_year[]"
                                                        value="<?php echo $row_f['financial_year']; ?>"
                                                        class="form-control d-inline-block ml-2" style="width:150px;"
                                                        readonly>
                                                </div>

                                                <div class="col-sm-3 form-group">
                                                    <label>वार्षिक लाभ/हानि की स्थिति</label>
                                                    <select name="fy_profit_loss[]" class="form-control">
                                                        <option value="">--Select--</option>
                                                        <option value="profit" <?php echo ($row_f['fy_profit_loss'] == 'profit') ? 'selected' : ''; ?>>लाभ
                                                        </option>
                                                        <option value="loss" <?php echo ($row_f['fy_profit_loss'] == 'loss') ? 'selected' : ''; ?>>हानि
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 form-group">
                                                    <label>(धनराशि लाख रु मे)</label>
                                                    <input type="text" name="fy_profit_loss_amt[]"
                                                        class="form-control chk_decimal"
                                                        value="<?php echo $row_f['fy_profit_loss_amt']; ?>">
                                                </div>

                                                <div class="col-sm-3 form-group">
                                                    <label>संचित लाभ/हानि की स्थिति</label>
                                                    <select name="comm_profit_loss[]" class="form-control">
                                                        <option value="">--Select--</option>
                                                        <option value="profit" <?php echo ($row_f['comm_profit_loss'] == 'profit') ? 'selected' : ''; ?>>लाभ
                                                        </option>
                                                        <option value="loss" <?php echo ($row_f['comm_profit_loss'] == 'loss') ? 'selected' : ''; ?>>हानि
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 form-group">
                                                    <label>(धनराशि लाख रु मे)</label>

                                                    <div class="input-group">

                                                        <input type="text" name="comm_profit_loss_amt[]"
                                                            class="form-control chk_decimal"
                                                            value="<?php echo $row_f['comm_profit_loss_amt']; ?>">

                                                        <?php if ($i > 2) { ?>
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="remove_financial_year(<?php echo $i; ?>)">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        <?php } ?>

                                                    </div>

                                                </div>

                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-sm-12 text-right">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="add_financial_year();">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="section-header mt-4"
                                        style="background: #cfe6fc; color:#2c3e50; border-left:10px solid #3498db; border-radius:20px 15px 15px 0;">
                                        <img src="images/logo/7.png" alt="text" class="img-fluid mr-2"
                                            style="height:30px; width:30px;">
                                        4. प्रबंध कमेटी
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>प्रबंध कमेटी निर्वाचित है?</label>
                                                <select name="sec_6_2_mgt_committee_is_elected"
                                                    id="sec_6_2_mgt_committee_is_elected" class="form-control"
                                                    onChange="hide_show(this.value, '#sec_6_2_election_year_div', 'yes'); hide_show(this.value, '#sec_6_2_end_year_div', 'yes');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo ($row_invoice['sec_6_2_mgt_committee_is_elected'] ?? '') == 'yes' ? 'selected' : '' ?>>निर्वाचित है</option>
                                                    <option value="no" <?php echo ($row_invoice['sec_6_2_mgt_committee_is_elected'] ?? '') == 'no' ? 'selected' : '' ?>>प्रशासनिक कमेटी</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group" id="sec_6_2_election_year_div"
                                                style="<?php echo ($row_invoice['sec_6_2_mgt_committee_is_elected'] ?? '') == 'yes' ? '' : 'display:none;'; ?>">
                                                <label>निर्वाचन का वर्ष</label>
                                                <select name="sec_6_2_election_year" id="sec_6_2_election_year"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php for ($i = 2024; $i >= 1975; $i--) {
                                                        $selected = ($row_invoice['sec_6_2_election_year'] ?? '') == $i ? 'selected' : '';
                                                        echo "<option value='$i' $selected>$i</option>";
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group" id="sec_6_2_end_year_div"
                                                style="<?php echo ($row_invoice['sec_6_2_mgt_committee_is_elected'] ?? '') == 'yes' ? '' : 'display:none;'; ?>">
                                                <label>कार्यावधि पूर्ण होने का वर्ष</label>
                                                <select name="sec_6_2_end_year" id="sec_6_2_end_year"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php for ($i = 2024; $i <= 2030; $i++) {
                                                        $selected = ($row_invoice['sec_6_2_end_year'] ?? '') == $i ? 'selected' : '';
                                                        echo "<option value='$i' $selected>$i</option>";
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="sec_2_b">
                                            <?php
                                            $m_count = $row_invoice['sec_6_2_member_count'] ?? 1;
                                            for ($i = 1; $i <= $m_count; $i++) { ?>
                                                <div class="row member-row" id="row_<?php echo $i; ?>">
                                                    <div class="col-sm-3 form-group">
                                                        <label>पदनाम<span style="color:red;">*</span></label>
                                                        <select class="form-control chk_designation"
                                                            id="sec_6_2_designation_<?php echo $i; ?>"
                                                            name="sec_6_2_designation_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" required data-type="पदनाम">
                                                            <option value="">--Select--</option>
                                                            <option value="अध्यक्ष" <?php echo ($row_invoice['sec_6_2_designation_' . $i] ?? '') == 'अध्यक्ष' ? 'selected' : ''; ?>>अध्यक्ष</option>
                                                            <option value="उपाध्यक्ष" <?php echo ($row_invoice['sec_6_2_designation_' . $i] ?? '') == 'उपाध्यक्ष' ? 'selected' : ''; ?>>उपाध्यक्ष</option>
                                                            <option value="संचालक" <?php echo ($row_invoice['sec_6_2_designation_' . $i] ?? '') == 'संचालक' ? 'selected' : ''; ?>>संचालक</option>
                                                            <option value="सदस्य" <?php echo ($row_invoice['sec_6_2_designation_' . $i] ?? '') == 'सदस्य' ? 'selected' : ''; ?>>सदस्य</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>प्रबंध कमेटी का नाम<span style="color:red;">*</span></label>
                                                        <input type="text" name="sec_6_2_name_<?php echo $i; ?>"
                                                            id="sec_6_2_name_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control chk_text"
                                                            required data-type="प्रबंध कमेटी का नाम"
                                                            value="<?php echo $row_invoice['sec_6_2_name_' . $i] ?? ''; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>पिता / पति का नाम</label>
                                                        <input type="text" name="sec_6_2_father_name_<?php echo $i; ?>"
                                                            id="sec_6_2_father_name_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_invoice['sec_6_2_father_name_' . $i] ?? ''; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>मोबाईल नंबर</label>
                                                        <div class="input-group">
                                                            <input type="text" name="sec_6_2__mob_no_<?php echo $i; ?>"
                                                                id="sec_6_2__mob_no_<?php echo $i; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_mobile" maxlength="10"
                                                                value="<?php echo $row_invoice['sec_6_2__mob_no_' . $i] ?? ''; ?>">
                                                            <?php if ($i == $m_count) { ?>
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-info btn-sm"
                                                                        onClick="sec_6_2_add_rows();" id="btn_add_row"><i
                                                                            class="fas fa-plus"></i></button>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <input type="hidden" name="sec_6_2_member_count" id="sec_6_2_member_count"
                                            value="<?php echo $m_count; ?>">
                                    </div>
                                    <input type="hidden" id="society_code" name="society_code"
                                        value="<?php echo $row_invoice['society_id'] ?? ''; ?>">
                                    <input type="hidden" id="mobile_number" name="mobile_number"
                                        value="<?php echo $row_invoice['mobile_number'] ?? ''; ?>">
                                    <input type="hidden" id="society_type_sno" name="society_type_sno"
                                        value="<?php echo $soc_type_sno_val ?? 1; ?>">
                                    <input type="hidden" name="sec_1_latitude" id="lat_hidden"
                                        value="<?php echo $row_invoice['latitude'] ?? ''; ?>">
                                    <input type="hidden" name="sec_1_longitude" id="long_hidden"
                                        value="<?php echo $row_invoice['longitude'] ?? ''; ?>">
                                    <div class="section-header mt-5"
                                        style="background:#cfe6fc; color:#2c3e50; border-left:10px solid #3498db; border-radius:20px 15px 15px 0;">
                                        <img src="images/logo/3.png" alt="text" class="img-fluid mr-2"
                                            style="height:30px; width:30px;">
                                        5. भवन की स्थिति
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-4 form-group">
                                                    <label>निर्मित भवन की स्थिति</label>
                                                    <select name="sec_5_built_building" id="sec_5_built_building"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        onChange="hide_show(this.value, '#jarjar_remarks', ['discarded', 'not_available']); hide_show(this.value, '#repairable', 'repairable')">
                                                        <option value="">--select-- </option>
                                                        <option value="good" <?php $repairable_display = 'none';
                                                        $jarjar_display = 'none';
                                                        if (($row_5['sec_5_built_building'] ?? '') == 'good') {
                                                            echo ' selected="selected" ';
                                                        } ?>>अच्छा
                                                        </option>
                                                        <option value="repairable" <?php if (($row_5['sec_5_built_building'] ?? '') == 'repairable') {
                                                            echo ' selected="selected" ';
                                                            $repairable_display = 'block';
                                                        } ?>>खराब/मरम्मत योग्य</option>
                                                        <option value="discarded" <?php if (($row_5['sec_5_built_building'] ?? '') == 'discarded') {
                                                            echo ' selected="selected" ';
                                                            $jarjar_display = 'block';
                                                        } ?>>
                                                            जर्जर/निष्प्रयोज्य्य</option>
                                                        <option value="not_available" <?php if (($row_5['sec_5_built_building'] ?? '') == 'not_available') {
                                                            echo ' selected="selected" ';
                                                            $jarjar_display = 'block';
                                                        } ?>>
                                                            भवन उपलब्ध नही है</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4 form-group" id="jarjar_remarks"
                                                    style="display: <?php echo $jarjar_display; ?>;">
                                                    <label>कृप्या विस्तृत जानकारी दर्ज करें</label>
                                                    <input type="text" name="sec_5_detailed_information"
                                                        id="sec_5_detailed_information" tabindex="<?php echo $tab++; ?>"
                                                        class="form-control"
                                                        value="<?php echo $row_5['sec_5_detailed_information'] ?? ''; ?>">
                                                </div>
                                            </div>
                                            <div id="repairable" style="display: <?php echo $repairable_display; ?>;">
                                                <div class="col-sm-12">
                                                    <h5 class="mb-3">(I) फर्श</h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_a_floor_cost"
                                                                id="sec_6_a_floor_cost" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_decimal"
                                                                data-type="2.XI.1 लंबाई (को मीटर में भरे)"
                                                                value="<?php echo $row_5['sec_6_a_floor_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="row col-sm-9" id="sec_6_a_floor_photos_container"
                                                            style="display: flex;">
                                                            <?php for ($pi = 1; $pi <= 4; $pi++) {
                                                                $img_field = "sec_6_a_img_$pi"; ?>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>फोटो <?php echo $pi; ?> संलग्न करें</label>
                                                                    <input type="file"
                                                                        accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                        name="<?php echo $img_field; ?>"
                                                                        id="<?php echo $img_field; ?>"
                                                                        data-uploaded="<?php echo (isset($row_5[$img_field]) && file_exists($row_5[$img_field])) ? '1' : '0'; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control sec_6_a_img_group">
                                                                </div>
                                                                <?php if (!empty($row_5[$img_field])) { ?>
                                                                    <div class="col-sm-2 form-group">
                                                                        <img src="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                            class="img-fluid img-thumbnail"
                                                                            style="height:50px;">
                                                                        <label><a href="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                                target="_blank">संलग्न फोटो देखें</a></label>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <h5>(II) दीवार</h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_a_wall_cost"
                                                                id="sec_6_a_wall_cost" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_decimal"
                                                                data-type="2.XI.1 लंबाई (को मीटर में भरे)"
                                                                value="<?php echo $row_5['sec_6_a_wall_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="row col-sm-9" id="sec_6_a_wall_photos_container"
                                                            style="display: flex;">
                                                            <?php for ($pi = 1; $pi <= 4; $pi++) {
                                                                $img_field = "sec_6_b_img_$pi"; ?>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>फोटो <?php echo $pi; ?> संलग्न करें</label>
                                                                    <input type="file"
                                                                        accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                        name="<?php echo $img_field; ?>"
                                                                        id="<?php echo $img_field; ?>"
                                                                        data-uploaded="<?php echo (isset($row_5[$img_field]) && file_exists($row_5[$img_field])) ? '1' : '0'; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control sec_6_b_img_group">
                                                                </div>
                                                                <?php if (!empty($row_5[$img_field])) { ?>
                                                                    <div class="col-sm-2 form-group">
                                                                        <img src="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                            class="img-fluid img-thumbnail"
                                                                            style="height:50px;">
                                                                        <label><a href="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                                target="_blank">संलग्न फोटो देखें</a></label>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <h5>(III) पुताई</h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_a_paint_cost"
                                                                id="sec_6_a_paint_cost" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_decimal"
                                                                data-type="2.XI.1 लंबाई (को मीटर में भरे)"
                                                                value="<?php echo $row_5['sec_6_a_paint_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="row col-sm-9" id="sec_6_a_paint_photos_container"
                                                            style="display: flex;">
                                                            <?php for ($pi = 1; $pi <= 4; $pi++) {
                                                                $img_field = "sec_6_c_img_$pi"; ?>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>फोटो <?php echo $pi; ?> संलग्न करें</label>
                                                                    <input type="file"
                                                                        accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                        name="<?php echo $img_field; ?>"
                                                                        id="<?php echo $img_field; ?>"
                                                                        data-uploaded="<?php echo (isset($row_5[$img_field]) && file_exists($row_5[$img_field])) ? '1' : '0'; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control sec_6_c_img_group">
                                                                </div>
                                                                <?php if (!empty($row_5[$img_field])) { ?>
                                                                    <div class="col-sm-2 form-group">
                                                                        <img src="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                            class="img-fluid img-thumbnail"
                                                                            style="height:50px;">
                                                                        <label><a href="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                                target="_blank">संलग्न फोटो देखें</a></label>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <h5>(IV) छत</h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_a_roof_cost"
                                                                id="sec_6_a_roof_cost" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_decimal"
                                                                data-type="2.XI.1 लंबाई (को मीटर में भरे)"
                                                                value="<?php echo $row_5['sec_6_a_roof_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="row col-sm-9" id="sec_6_a_roof_photos_container"
                                                            style="display: flex;">
                                                            <?php for ($pi = 1; $pi <= 4; $pi++) {
                                                                $img_field = "sec_6_d_img_$pi"; ?>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>फोटो <?php echo $pi; ?> संलग्न करें</label>
                                                                    <input type="file"
                                                                        accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                        name="<?php echo $img_field; ?>"
                                                                        id="<?php echo $img_field; ?>"
                                                                        data-uploaded="<?php echo (isset($row_5[$img_field]) && file_exists($row_5[$img_field])) ? '1' : '0'; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control sec_6_d_img_group">
                                                                </div>
                                                                <?php if (!empty($row_5[$img_field])) { ?>
                                                                    <div class="col-sm-2 form-group">
                                                                        <img src="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                            class="img-fluid img-thumbnail"
                                                                            style="height:50px;">
                                                                        <label><a href="<?php echo $row_5[$img_field] ?? ''; ?>"
                                                                                target="_blank">संलग्न फोटो देखें</a></label>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h5>(V) शौचालय</h5>
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-2 form-group">
                                                            <label>फर्श</label>
                                                            <select name="sec_6_e_floor" id="sec_6_e_floor"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#bathroom_floor', 'repairable');hide_show(this.value, '#img_611', 'repairable');hide_show(this.value, '#img_612', 'repairable');">
                                                                <option value="" <?php echo empty($row_5['sec_6_e_floor'] ?? '') ? 'selected' : ''; ?>>--Select--</option>
                                                                <option value="good" <?php echo (($row_5['sec_6_e_floor'] ?? '') == 'good') ? 'selected' : ''; ?>>सही है</option>
                                                                <option value="repairable" <?php echo (($row_5['sec_6_e_floor'] ?? '') == 'repairable') ? 'selected' : ''; ?>>मरम्म्त योग्य</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_floor'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="bathroom_floor">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_e_floor_cost"
                                                                id="sec_6_e_floor_cost" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_5['sec_6_e_floor_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_floor'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_611">
                                                            <label>फोटो 1 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_e_img_1" id="sec_6_e_img_1"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_e_img_1']) && file_exists($row_5['sec_6_e_img_1'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_e_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_e_img_1'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_e_img_1'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_e_img_1'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_floor'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_612">
                                                            <label>फोटो 2 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_e_img_2" id="sec_6_e_img_2"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_e_img_2']) && file_exists($row_5['sec_6_e_img_2'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_e_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_e_img_2'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_e_img_2'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_e_img_2'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2 form-group">
                                                            <label>प्लासटर</label>
                                                            <select name="sec_6_e_plaster" id="sec_6_e_plaster"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#bathroom_plaster', 'repairable');hide_show(this.value, '#img_621', 'repairable');hide_show(this.value, '#img_622', 'repairable');">
                                                                <option value="" <?php echo empty($row_5['sec_6_e_plaster'] ?? '') ? 'selected' : ''; ?>>--Select--</option>
                                                                <option value="good" <?php echo (($row_5['sec_6_e_plaster'] ?? '') == 'good') ? 'selected' : ''; ?>>सही है</option>
                                                                <option value="repairable" <?php echo (($row_5['sec_6_e_plaster'] ?? '') == 'repairable') ? 'selected' : ''; ?>>मरम्म्त योग्य</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_plaster'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="bathroom_plaster">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_e_plaster_cost"
                                                                id="sec_6_e_plaster_cost"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_5['sec_6_e_plaster_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_plaster'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_621">
                                                            <label>फोटो 1 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_f_img_1" id="sec_6_f_img_1"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_f_img_1']) && file_exists($row_5['sec_6_f_img_1'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_f_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_f_img_1'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_f_img_1'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_f_img_1'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_plaster'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_622">
                                                            <label>फोटो 2 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_f_img_2" id="sec_6_f_img_2"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_f_img_2']) && file_exists($row_5['sec_6_f_img_2'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_f_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_f_img_2'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_f_img_2'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_f_img_2'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2 form-group">
                                                            <label>छत</label>
                                                            <select name="sec_6_e_ceiling" id="sec_6_e_ceiling"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#bathroom_roof', 'repairable');hide_show(this.value, '#img_631', 'repairable');hide_show(this.value, '#img_632', 'repairable');">
                                                                <option value="" <?php echo empty($row_5['sec_6_e_ceiling'] ?? '') ? 'selected' : ''; ?>>--Select--</option>
                                                                <option value="good" <?php echo (($row_5['sec_6_e_ceiling'] ?? '') == 'good') ? 'selected' : ''; ?>>सही है</option>
                                                                <option value="repairable" <?php echo (($row_5['sec_6_e_ceiling'] ?? '') == 'repairable') ? 'selected' : ''; ?>>मरम्म्त योग्य</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_ceiling'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="bathroom_roof">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_e_ceiling_cost"
                                                                id="sec_6_e_ceiling_cost"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_5['sec_6_e_ceiling_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_ceiling'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_631">
                                                            <label>फोटो 1 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_g_img_1" id="sec_6_g_img_1"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_g_img_1']) && file_exists($row_5['sec_6_g_img_1'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_g_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_g_img_1'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_g_img_1'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_g_img_1'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_ceiling'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_632">
                                                            <label>फोटो 2 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_g_img_2" id="sec_6_g_img_2"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_g_img_2']) && file_exists($row_5['sec_6_g_img_2'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_g_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_g_img_2'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_g_img_2'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_g_img_2'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2 form-group">
                                                            <label>सीट</label>
                                                            <select name="sec_6_e_seat" id="sec_6_e_seat"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#bathroom_seat', 'repairable');hide_show(this.value, '#img_641', 'repairable');hide_show(this.value, '#img_642', 'repairable');">
                                                                <option value="" <?php echo empty($row_5['sec_6_e_seat'] ?? '') ? 'selected' : ''; ?>>--Select--</option>
                                                                <option value="good" <?php echo (($row_5['sec_6_e_seat'] ?? '') == 'good') ? 'selected' : ''; ?>>सही है
                                                                </option>
                                                                <option value="repairable" <?php echo (($row_5['sec_6_e_seat'] ?? '') == 'repairable') ? 'selected' : ''; ?>>मरम्म्त योग्य</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_seat'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="bathroom_seat">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_e_seat_cost"
                                                                id="sec_6_e_seat_cost" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_5['sec_6_e_seat_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_seat'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_641">
                                                            <label>फोटो 1 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_h_img_1" id="sec_6_h_img_1"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_h_img_1']) && file_exists($row_5['sec_6_h_img_1'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_h_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_h_img_1'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_h_img_1'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_h_img_1'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_seat'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_642">
                                                            <label>फोटो 2 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_h_img_2" id="sec_6_h_img_2"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_h_img_2']) && file_exists($row_5['sec_6_h_img_2'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_h_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_h_img_2'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_h_img_2'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_h_img_2'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2 form-group">
                                                            <label>प्लम्बिंग</label>
                                                            <select name="sec_6_e_plumbing" id="sec_6_e_plumbing"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#bathroom_plumbing', 'repairable');hide_show(this.value, '#img_651', 'repairable');hide_show(this.value, '#img_652', 'repairable');">
                                                                <option value="" <?php echo empty($row_5['sec_6_e_plumbing'] ?? '') ? 'selected' : ''; ?>>--Select--</option>
                                                                <option value="good" <?php echo (($row_5['sec_6_e_plumbing'] ?? '') == 'good') ? 'selected' : ''; ?>>सही है</option>
                                                                <option value="repairable" <?php echo (($row_5['sec_6_e_plumbing'] ?? '') == 'repairable') ? 'selected' : ''; ?>>मरम्म्त योग्य</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_plumbing'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="bathroom_plumbing">
                                                            <label>अनुमानित लागत (रुपये में)</label>
                                                            <input type="text" name="sec_6_e_plumbing_cost"
                                                                id="sec_6_e_plumbing_cost"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_5['sec_6_e_plumbing_cost'] ?? ''; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_plumbing'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_651">
                                                            <label>फोटो 1 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_i_img_1" id="sec_6_i_img_1"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_i_img_1']) && file_exists($row_5['sec_6_i_img_1'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_i_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_i_img_1'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_i_img_1'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_i_img_1'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="col-sm-2 form-group"
                                                            style="display: <?php echo (($row_5['sec_6_e_plumbing'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                            id="img_652">
                                                            <label>फोटो 2 संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_6_i_img_2" id="sec_6_i_img_2"
                                                                data-uploaded="<?php echo (isset($row_5['sec_6_i_img_2']) && file_exists($row_5['sec_6_i_img_2'])) ? '1' : '0'; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control sec_6_i_img_v_group">
                                                        </div>
                                                        <?php if (file_exists($row_5['sec_6_i_img_2'] ?? '')) { ?>
                                                            <div class="col-sm-2 form-group">
                                                                <img src="<?php echo $row_5['sec_6_i_img_2'] ?? ''; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;">
                                                                <label><a
                                                                        href="<?php echo $row_5['sec_6_i_img_2'] ?? ''; ?>"
                                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <h5>(VI) चारदिवारी (बाऊण्डरी वाल) </h5>
                                                <div class="row">
                                                    <div class="col-sm-2 form-group">
                                                        <label>चारदिवारी</label>
                                                        <select name="sec_6_j_boundary_wall" id="sec_6_j_boundary_wall"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onChange="hide_show(this.value, '#boundary_wall_info', 'repairable');hide_show(this.value, '#img_661', 'repairable');hide_show(this.value, '#img_662', 'repairable');">
                                                            <option value="" <?php echo empty($row_5['boundary_wall_status'] ?? '') ? 'selected' : ''; ?>>--Select--</option>
                                                            <option value="good" <?php echo (($row_5['boundary_wall_status'] ?? '') == 'good') ? 'selected' : ''; ?>>सही है</option>
                                                            <option value="repairable" <?php echo (($row_5['boundary_wall_status'] ?? '') == 'repairable') ? 'selected' : ''; ?>>मरम्म्त योग्य</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2 form-group"
                                                        style="display: <?php echo (($row_5['boundary_wall_status'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                        id="boundary_wall_info">
                                                        <label>अनुमानित लागत (रुपये में)</label>
                                                        <input type="text" name="sec_6_j_boundary_wall_cost"
                                                            id="sec_6_j_boundary_wall_cost"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_5['boundary_wall_cost'] ?? ''; ?>">
                                                    </div>
                                                    <div class="col-sm-2 form-group"
                                                        style="display: <?php echo (($row_5['boundary_wall_status'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                        id="img_661">
                                                        <label>फोटो 1 संलग्न करें</label>
                                                        <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                            name="sec_6_j_img_1" id="sec_6_j_img_1"
                                                            data-uploaded="<?php echo (isset($row_5['boundary_wall_img_1']) && file_exists($row_5['boundary_wall_img_1'])) ? '1' : '0'; ?>"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            class="form-control sec_6_j_img_group">
                                                    </div>
                                                    <?php if (file_exists($row_5['boundary_wall_img_1'] ?? '')) { ?>
                                                        <div class="col-sm-2 form-group">
                                                            <img src="<?php echo $row_5['boundary_wall_img_1'] ?? ''; ?>"
                                                                class="img-fluid img-thumbnail" style="height:50px;">
                                                            <label><a
                                                                    href="<?php echo $row_5['boundary_wall_img_1'] ?? ''; ?>"
                                                                    target="_blank">संलग्न फोटो देखें</a></label>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-sm-2 form-group"
                                                        style="display: <?php echo (($row_5['boundary_wall_status'] ?? '') == 'repairable') ? 'block' : 'none'; ?>;"
                                                        id="img_662">
                                                        <label>फोटो 2 संलग्न करें</label>
                                                        <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                            name="sec_6_j_img_2" id="sec_6_j_img_2"
                                                            data-uploaded="<?php echo (isset($row_5['boundary_wall_img_2']) && file_exists($row_5['boundary_wall_img_2'])) ? '1' : '0'; ?>"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            class="form-control sec_6_j_img_group">
                                                    </div>
                                                    <?php if (file_exists($row_5['boundary_wall_img_2'] ?? '')) { ?>
                                                        <div class="col-sm-2 form-group">
                                                            <img src="<?php echo $row_5['boundary_wall_img_2'] ?? ''; ?>"
                                                                class="img-fluid img-thumbnail" style="height:50px;">
                                                            <label><a
                                                                    href="<?php echo $row_5['boundary_wall_img_2'] ?? ''; ?>"
                                                                    target="_blank">संलग्न फोटो देखें</a></label>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <h5>(VII) दरवाजा </h5>
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>(VII) दरवाजा (संख्या)</label>
                                                        <input type="text" name="sec_6_f_number_of_door"
                                                            id="sec_6_f_number_of_door" tabindex="<?php echo $tab++; ?>"
                                                            class="form-control chk_number" data-maxlength="5"
                                                            data-minlength="0"
                                                            data-type="2.XI.6 दरवाजा (संख्या को अंक मे भरे)"
                                                            value="<?php echo $row_5['sec_6_f_number_of_door'] ?? ''; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>अनुमानित लागत (रुपये में)</label>
                                                        <input type="text" name="sec_6_f_door_cost"
                                                            id="sec_6_f_door_cost" tabindex="<?php echo $tab++; ?>"
                                                            class="form-control chk_decimal"
                                                            data-type="2.XI.1 लंबाई (को मीटर में भरे)"
                                                            value="<?php echo $row_5['sec_6_f_door_cost'] ?? ''; ?>">
                                                    </div>
                                                </div>
                                                <h5>(VIII) खिडकी </h5>
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>(VIII) खिडकी (संख्या)</label>
                                                        <input type="text" name="sec_6_g_number_of_window"
                                                            id="sec_6_g_number_of_window"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            class="form-control chk_number" data-maxlength="5"
                                                            data-minlength="0"
                                                            data-type="2.XI.7 खिडकी (संख्या को अंक मे भरे)"
                                                            value="<?php echo $row_5['sec_6_g_number_of_window'] ?? ''; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>अनुमानित लागत (रुपये में)</label>
                                                        <input type="text" name="sec_6_f_window_cost"
                                                            id="sec_6_f_window_cost" tabindex="<?php echo $tab++; ?>"
                                                            class="form-control chk_decimal"
                                                            data-type="2.XI.1 लंबाई (को मीटर में भरे)"
                                                            value="<?php echo $row_5['sec_6_f_window_cost'] ?? ''; ?>">
                                                    </div>
                                                </div>
                                                <h5>(IX) प्लास्टर </h5>
                                                <div class="row">
                                                    <div class="col-sm-4 form-group">
                                                        <label>दीवार (आवश्यकता अनुसार क्षेत्रफल स्क्वायर मीटर में
                                                            लिखें)</label>
                                                        <input type="text" name="sec_6_h_length" id="sec_6_h_length"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_5['sec_6_h_length'] ?? ''; ?>">
                                                    </div>
                                                    <div class="col-sm-4 form-group">
                                                        <label>छत (आवश्यकता अनुसार क्षेत्रफल स्क्वायर मीटर में
                                                            लिखें)</label>
                                                        <input type="text" name="sec_6_h_width" id="sec_6_h_width"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_5['sec_6_h_width'] ?? ''; ?>">
                                                    </div>
                                                </div>
                                                <h5>(IX) अन्य </h5>
                                                <div class="row">
                                                    <div class="col-sm-6 form-group">
                                                        <label>यदि उपरोक्त के अतिरिक्त किसी प्रकार कि मरम्म्त कि
                                                            आवश्यक्ता हो तो उल्लेख करें</label>
                                                        <input type="text" name="sec_6_i_other" id="sec_6_i_other"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_5['sec_6_i_other'] ?? ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="action-buttons" class="text-center mt-4">
                                <button class="btn-save-draft" type="button" onClick="save_draft()"><i
                                        class="fa fa-save"></i> Save Draft</button>
                                <button id="submit-btn" class="btn-next" type="button"
                                    onClick="if(validate_input()){ save_draft(true); }"> Submit</button>
                            </div>
                        </form>
                        <div id="success" style="display:none;">
                            <div class="mt-5 text-center">
                                <h4 style="color: #0000ff9e;"><i class="fas fa-check-circle"></i> प्रपत्र सफलता पूर्वक
                                    भरा गया</h4>
                                <p class="mt-3">आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
                                    सत्यापन हेतु भेजा जाना है ।<br>कृप्या सत्यापन पर भेजने से पहले नीचे दर्शायें लिंक से
                                    फार्म खोल कर पुनः जाच कर लें ।</p>
                                <button type="button" class="btn btn-info btn-lg mt-2"
                                    onclick="window.open('maintenance_preview.php?exdid=<?php echo $_GET['exdid'] ?? ''; ?>&type=<?php echo $society_type; ?>', '_blank');"><i
                                        class="fas fa-eye"></i> प्रपत्र पुनः निरीक्षण के लिये देखे</button>
                            </div>
                            <div class="col-md-12 text-center mt-4">
                                <p><input type="checkbox" style="height: 20px; width: 20px; vertical-align: middle;"
                                        id="review_ack"
                                        onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}"
                                        <label for="review_ack"
                                        style="cursor: pointer; font-weight: bold; margin-left: 10px;">मै एतत्द्वारा
                                    घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी सूचनायें मेरी जानकारी अनुसार
                                    सत्य एवम सही है ।</label>
                                </p>
                                <button type="button" class="btn btn-danger btn-lg px-5" onClick="form_validate()"
                                    id="verification_button" disabled="disabled"
                                    style="font-weight: bold; border-radius: 5px;">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="term" name="term" value="a">
                <input type="hidden" id="latitude" name="latitude"
                    value="<?php echo $row_invoice['latitude'] ?? ''; ?>">
                <input type="hidden" id="longitude" name="longitude"
                    value="<?php echo $row_invoice['longitude'] ?? ''; ?>">
                <input type="hidden" id="id" name="id" value="submit_form_comp">
                <input type="hidden" id="current_step_count" name="current_step_count" value="5">
                </form>
            </div>
        </div>
    </div>
    <script>
        var current_step = 5;
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
        function showPosition(position) {
            $('#lat').val(position.coords.latitude);
            $('#long').val(position.coords.longitude);
            $('#lat_hidden').val(position.coords.latitude);
            $('#long_hidden').val(position.coords.longitude);
            $('#googlemap').attr('src', 'https://maps.google.com/maps?output=embed&z=13&q=' + position.coords.latitude + ',' + position.coords.longitude);
        }
        $(document).ready(function () {
            $(document).on('input', '.chk_number, .chk_decimal', function () {
                var val = $(this).val();
                var errMsg = $(this).next('.error-msg');
                if (errMsg.length === 0) { $(this).after('<div class="error-msg text-danger" style="font-size: 11px; margin-top: 2px;"></div>'); errMsg = $(this).next('.error-msg'); }
                if (val !== '' && !/^-?\d*(\.\d*)?$/.test(val)) { errMsg.text("कृपया संख्या भरें"); } else { errMsg.text(""); }
            });
            $(document).on('input', '#secretary_email, .chk_email', function () {
                var val = $(this).val();
                var errMsg = $(this).next('.error-msg');
                if (errMsg.length === 0) { $(this).after('<div class="error-msg text-danger" style="font-size: 11px; margin-top: 2px;"></div>'); errMsg = $(this).next('.error-msg'); }
                var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (val !== '' && !emailRegex.test(val)) { errMsg.text("कृपया सही ईमेल भरें"); } else { errMsg.text(""); }
            });
            $(document).on('input', '#gst_no', function () {
                var val = $(this).val();
                var errMsg = $(this).next('.error-msg');
                if (errMsg.length === 0) { $(this).after('<div class="error-msg text-danger" style="font-size: 11px; margin-top: 2px;"></div>'); errMsg = $(this).next('.error-msg'); }
                if (val !== '' && val.length !== 15) { errMsg.text("15 अंक का जीएसटी (GST) नंबर भरें"); } else { errMsg.text(""); }
            });
            $(document).on('input', '#pan_no', function () {
                var val = $(this).val();
                var errMsg = $(this).next('.error-msg');
                if (errMsg.length === 0) { $(this).after('<div class="error-msg text-danger" style="font-size: 11px; margin-top: 2px;"></div>'); errMsg = $(this).next('.error-msg'); }
                if (val !== '' && val.length !== 10) { errMsg.text("10 अंक का पैन (PAN) नंबर भरें"); } else { errMsg.text(""); }
            });
            $(document).on('input', '#secretary_aadhar', function () {
                var val = $(this).val();
                var errMsg = $(this).next('.error-msg');
                if (errMsg.length === 0) { $(this).after('<div class="error-msg text-danger" style="font-size: 11px; margin-top: 2px;"></div>'); errMsg = $(this).next('.error-msg'); }
                if (val !== '' && (!/^\d+$/.test(val) || val.length !== 12)) { errMsg.text("12 अंक का आधार (Aadhar) नंबर भरें"); } else { errMsg.text(""); }
            });
            $(document).on('input', '#secretary_mobile, .chk_mobile', function () {
                var val = $(this).val();
                var container = $(this).closest('.input-group');
                var targetElement = container.length ? container : $(this);
                var errMsg = targetElement.next('.error-msg');
                if (errMsg.length === 0) { targetElement.after('<div class="error-msg text-danger" style="font-size: 11px; margin-top: 2px;"></div>'); errMsg = targetElement.next('.error-msg'); }
                if (val !== '' && (!/^\d+$/.test(val) || val.length !== 10)) { errMsg.text("10 अंक का मोबाइल नंबर भरें"); } else { errMsg.text(""); }
            });
            $('#verification_button').on('click', function () {
                alert('Form saved Successfully!');
                window.location.href = 'maintenance_index.php';
            });
        });
        function save_draft(isSubmit = false) {
            $("#current_step_count").val(current_step);
            $.ajax({
                type: "POST",
                url: $("#user_form").attr("action"),
                data: new FormData($("#user_form")[0]),
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    if (response[0] && response[0].id === 'error') {
                        $.notify({ message: response[0].error }, { type: 'danger', timer: 2000 });
                        return;
                    }
                    var surveyId = response[0]?.survey_id || response[0]?.visit_id;
                    if (surveyId) {
                        $("#survey_id").val(surveyId);
                    }
                    if (isSubmit) {
                        $('#steps-container').hide();
                        $('#action-buttons').hide();
                        $('#success').show();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        $('#submit-btn').show();
                        $.notify({ message: 'Data Saved Successfully' }, { type: 'success', timer: 2000 });
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    $.notify({ message: 'Something went wrong. Please try again.' }, { type: 'danger', timer: 2000 });
                }
            });
        }
        function hide_show(value, containerId, showValue) {
            var testServicesContainer = document.querySelector(containerId);
            if (!testServicesContainer) return;
            if (Array.isArray(showValue)) {
                if (showValue.includes(value)) { testServicesContainer.style.display = 'block'; } else { testServicesContainer.style.display = 'none'; }
            } else {
                if (value === showValue) { testServicesContainer.style.display = 'block'; } else { testServicesContainer.style.display = 'none'; }
            }
        }
        function validate_input() {
            var regexp_text = /^[A-Za-z\u0900-\u097F,.\s]+$/;
            var regexp_spltext = /^[\p{Letter}\u0900-\u097F ,.\-!?]+$/u;
            var regexp_number = /^\d+$/;
            var regexp_decimal = /^-?\d+(\.\d+)?$/;
            var regexp_email = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            var error_status = 0;
            var error_msg = '';
            var requiredFields = [
                { id: 'lat', label: 'Location (Latitude)' },
                { id: 'long', label: 'Location (Longitude)' },
                { id: 'society_name', label: 'समिति का नाम' },
                { id: 'society_registration_no', label: 'निबंधन संख्या' },
                { id: 'society_registration_date', label: 'निबंधन दिनांक' },
                { id: 'secretary_name', label: 'सचिव का नाम' },
                { id: 'secretary_mobile', label: 'सचिव का मोबाइल नंबर' },
                { id: 'secretary_aadhar', label: 'आधार नंबर' }
            ];
            var imgGroupValidations = [
                { className: 'sec_6_a_img_group', label: '(I) फर्श फोटो', inputId: 'sec_6_a_floor_cost' },
                { className: 'sec_6_b_img_group', label: '(II) दीवार फोटो', inputId: 'sec_6_a_wall_cost' },
                { className: 'sec_6_c_img_group', label: '(III) पुताई फोटो', inputId: 'sec_6_a_paint_cost' },
                { className: 'sec_6_d_img_group', label: '(IV) छत फोटो', inputId: 'sec_6_a_roof_cost' }
            ];
            $.each(imgGroupValidations, function (index, group) {
                var costValue = $('#' + group.inputId).val();
                if (costValue && costValue.trim() !== '') {
                    var isFilled = false;
                    $('.' + group.className).each(function () {
                        if ($(this).val() !== '' || $(this).attr('data-uploaded') === '1') { isFilled = true; return false; }
                    });
                    if (!isFilled) { error_msg += group.label + ' कम से कम एक फोटो संलग्न करना अनिवार्य है।\n'; error_status = 1; }
                }
            });
            var toiletImgGroups = [
                { dropdownId: 'sec_6_e_floor', costId: 'sec_6_e_floor_cost', className: 'sec_6_e_img_v_group', label: '(V) शौचालय - फर्श फोटो' },
                { dropdownId: 'sec_6_e_plaster', costId: 'sec_6_e_plaster_cost', className: 'sec_6_f_img_v_group', label: '(V) शौचालय - प्लास्टर फोटो' },
                { dropdownId: 'sec_6_e_ceiling', costId: 'sec_6_e_ceiling_cost', className: 'sec_6_g_img_v_group', label: '(V) शौचालय - छत फोटो' },
                { dropdownId: 'sec_6_e_seat', costId: 'sec_6_e_seat_cost', className: 'sec_6_h_img_v_group', label: '(V) शौचालय - सीट फोटो' },
                { dropdownId: 'sec_6_e_plumbing', costId: 'sec_6_e_plumbing_cost', className: 'sec_6_i_img_v_group', label: '(V) शौचालय - प्लम्बिंग फोटो' },
                { dropdownId: 'sec_6_j_boundary_wall', costId: 'sec_6_j_boundary_wall_cost', className: 'sec_6_j_img_group', label: '(VI) चारदिवारी फोटो' }
            ];
            $.each(toiletImgGroups, function (index, group) {
                var costValue = $('#' + group.costId).val();
                if ($('#' + group.dropdownId).val() === 'repairable' && costValue && costValue.trim() !== '') {
                    var isFilled = false;
                    $('.' + group.className).each(function () {
                        if ($(this).val() !== '' || $(this).attr('data-uploaded') === '1') { isFilled = true; return false; }
                    });
                    if (!isFilled) { error_msg += group.label + ' कम से कम एक फोटो संलग्न करना अनिवार्य है।\n'; error_status = 1; }
                }
            });
            $.each(requiredFields, function (index, field) {
                var value = $('#' + field.id).val();
                if (!value || value.trim() === '') {
                    error_msg += field.label + ' आवश्यक है। कृपया भरें।\n';
                    $('#' + field.id).addClass('danger');
                    error_status = 1;
                } else {
                    $('#' + field.id).removeClass('danger');
                }
            });
            $(".chk_designation").each(function () {
                var value_designation = $(this).val();
                if (!value_designation || value_designation.trim() === '') {
                    error_msg += $(this).data("type") + ' आवश्यक है। कृपया भरें।\n';
                    $(this).addClass('danger');
                    error_status = 1;
                } else {
                    $(this).removeClass('danger');
                }
            });
            $(".chk_text").each(function () {
                var value_text = $(this).val();
                if (value_text != "") {
                    if (!regexp_text.test(value_text)) {
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_msg += $(this).data("type") + "\n";
                        error_status = 1;
                    } else {
                        $(this).addClass("success");
                        $(this).removeClass("danger");
                    }
                } else {
                    $(this).removeClass("danger success");
                }
            });
            $(".chk_number").each(function () {
                var value_number = $(this).val();
                var minlength = $(this).data("minlength");
                var maxlength = $(this).data("maxlength");
                if (value_number != "") {
                    if (value_number.length < minlength) {
                        error_msg += $(this).data("type") + ". न्यूनतम " + minlength + " अंक भरें। \n";
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_status = 1;
                    } else if (value_number.length > maxlength) {
                        error_msg += $(this).data("type") + " 5 अंकों से अधिक नहीं हो सकता। \n";
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_status = 1;
                    } else if (!regexp_number.test(value_number)) {
                        error_msg += $(this).data("type") + " केवल अंक भरें। \n";
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_status = 1;
                    } else {
                        $(this).addClass("success");
                        $(this).removeClass("danger");
                    }
                } else {
                    $(this).removeClass("danger success");
                }
            });
            $(".chk_decimal").each(function () {
                var value_decimal = $(this).val().trim();
                if (value_decimal != "") {
                    if (!regexp_decimal.test(value_decimal)) {
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_msg += $(this).data("type") + "\n";
                        error_status = 1;
                    } else {
                        $(this).addClass("success");
                        $(this).removeClass("danger");
                    }
                } else {
                    $(this).removeClass("danger success");
                }
            });
            if (error_msg != "") { alert(error_msg); }
            return error_status === 0;
        }
        function add_financial_year() {

    var rowCount = $('.financial-year-row').length;
    var nextNo = rowCount + 1;

    var startYear = 2024 + rowCount;
    var endYear = (startYear + 1).toString().substr(2,2);
    var fy = startYear + '-' + endYear;

    var romans = ["","I","II","III","IV","V","VI","VII","VIII","IX","X"];
    var romanLabel = romans[nextNo] || nextNo;

    var html = '';

    html += '<div class="row mt-2 financial-year-row" id="year_row_'+nextNo+'">';

    html += '<div class="col-sm-12 mb-2">';
    html += '<b>(' + romanLabel + ') वित्तीय वर्ष</b>';
    html += '<input type="text" name="financial_year[]" value="'+fy+'" class="form-control d-inline-block ml-2" style="width:150px;" readonly>';
    html += '</div>';

    html += '<div class="col-sm-3 form-group">';
    html += '<label>वार्षिक लाभ/हानि की स्थिति</label>';
    html += '<select name="fy_profit_loss[]" class="form-control">';
    html += '<option value="">--Select--</option>';
    html += '<option value="profit">लाभ</option>';
    html += '<option value="loss">हानि</option>';
    html += '</select>';
    html += '</div>';

    html += '<div class="col-sm-3 form-group">';
    html += '<label>(धनराशि लाख रु मे)</label>';
    html += '<input type="text" name="fy_profit_loss_amt[]" class="form-control chk_decimal">';
    html += '</div>';

    html += '<div class="col-sm-3 form-group">';
    html += '<label>संचित लाभ/हानि की स्थिति</label>';
    html += '<select name="comm_profit_loss[]" class="form-control">';
    html += '<option value="">--Select--</option>';
    html += '<option value="profit">लाभ</option>';
    html += '<option value="loss">हानि</option>';
    html += '</select>';
    html += '</div>';

    html += '<div class="col-sm-3 form-group">';
    html += '<label>(धनराशि लाख रु मे)</label>';
    html += '<div class="input-group">';
    html += '<input type="text" name="comm_profit_loss_amt[]" class="form-control chk_decimal">';
    html += '<div class="input-group-append">';
    html += '<button type="button" class="btn btn-danger btn-sm" onclick="remove_financial_year('+nextNo+')">';
    html += '<i class="fas fa-trash"></i>';
    html += '</button>';
    html += '</div>';
    html += '</div>';
    html += '</div>';

    html += '</div>';

    $('#financial_years_container').append(html);
}

function remove_financial_year(id){
    $('#year_row_'+id).remove();
}
        function remove_financial_year(rowNum) {
            if (confirm('क्या आप इस वर्ष को हटाना चाहते हैं?')) { $('#year_row_' + rowNum).remove(); }
        }
        function sec_6_2_add_rows() {
            var count = parseInt($('#sec_6_2_member_count').val());
            if (count >= 10) { alert("अधिकतम 10 सदस्य जोड़े जा सकते हैं।"); return; }
            $('#btn_add_row').remove();
            count++;
            $('#sec_6_2_member_count').val(count);
            var html = '<div class="row member-row" id="row_' + count + '">' +
                '<div class="col-sm-3 form-group"><label>पदनाम<span style="color:red;">*</span></label><select class="form-control chk_designation" required name="sec_6_2_designation_' + count + '" data-type="पदनाम"><option value="">--Select--</option><option value="अध्यक्ष">अध्यक्ष</option><option value="उपाध्यक्ष">उपाध्यक्ष</option><option value="संचालक">संचालक</option><option value="सदस्य">सदस्य</option></select></div>' +
                '<div class="col-sm-3 form-group"><label>प्रबंध कमेटी का नाम<span style="color:red;">*</span></label><input type="text" name="sec_6_2_name_' + count + '" class="form-control chk_text" required data-type="प्रबंध कमेटी का नाम"></div>' +
                '<div class="col-sm-3 form-group"><label>पिता / पति का नाम</label><input type="text" name="sec_6_2_father_name_' + count + '" class="form-control"></div>' +
                '<div class="col-sm-3 form-group"><label>मोबाईल नंबर</label><div class="input-group"><input type="text" name="sec_6_2__mob_no_' + count + '" class="form-control chk_mobile" maxlength="10"><div class="input-group-append"><button type="button" class="btn btn-info btn-sm" onClick="sec_6_2_add_rows();" id="btn_add_row"><i class="fas fa-plus"></i></button></div></div></div></div>';
            $('#sec_2_b').append(html);
        }
        function form_validate() {
            alert('Form saved Successfully!');
            window.location.href = 'maintenance_index.php';
        }



        $(document).ready(function () {

    // Disable all form controls
    $('#user_form')
        .find('input, select, textarea, button')
        .prop('disabled', true);

    // Hidden fields enabled rakho (optional)
    $('#user_form input[type=hidden]').prop('disabled', false);

    // Images and links visible rahenge
    $('img').css({
        'pointer-events': 'auto',
        'opacity': '1'
    });

    $('a').css({
        'pointer-events': 'auto'
    });

    // File inputs completely hide
    $('input[type="file"]').closest('.form-group').hide();

    // Save/Submit buttons hide
    $('.btn-save-draft').hide();
    $('.btn-next').hide();
    $('#action-buttons').hide();

});
    </script>
    <script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>
    <?php page_footer_start();
    page_footer_end(); ?>