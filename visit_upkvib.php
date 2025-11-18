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
        $row_invoice['prakhand_name'] = $row_invoice['prakhand_name'];
        $row_invoice['members_no'] = $row_invoice['members_no'];
        $row_invoice['active_members_no'] = $row_invoice['active_members_no'];
        $row_invoice['inactive_members_no'] = $row_invoice['inactive_members_no'];
        $row_invoice['new_members'] = $row_invoice['new_members'];
        $row_invoice['share_capital'] = $row_invoice['share_capital'];
        $row_invoice['inactive_to_active_no'] = $row_invoice['inactive_to_active_no'];
        $row_invoice['total_members'] = $row_invoice['total_members'];
    }

    $sql = 'select * from survey_invoice_new_sec_6_2 where survey_id="' . $row_invoice['sno'] . '"';
    $res_6_2 = execute_query($sql);
    if (mysqli_num_rows($res_6_2) != 0) {
        $row_sec_6_2 = mysqli_fetch_assoc($res_6_2);
        $row_sec_6_2['sec_6_2_mgt_committee_is_elected'] = $row_sec_6_2['mgt_committee_is_elected'];
        $row_sec_6_2['sec_6_2_election_year'] = $row_sec_6_2['election_year'];
        $row_sec_6_2['sec_6_2_end_year'] = $row_sec_6_2['end_year'];
        $row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = $row_sec_6_2['mgt_committee_resolution_no'];
    } else {
        $row_sec_6_2['sec_6_2_mgt_committee_is_elected'] = '';
        $row_sec_6_2['sec_6_2_election_year'] = '';
        $row_sec_6_2['sec_6_2_end_year'] = '';
        $row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = '';
    }


    $sql = 'select * from survey_invoice_new_sec_6_2_1 where survey_id="' . $row_invoice['sno'] . '"';
    // echo $sql;
    $result_sec_6_2_1 = execute_query($sql);
    $row_6_2 = array();
    $d = 1;
    if (mysqli_num_rows($result_sec_6_2_1) > 0) {
        $row_6_2['count'] = mysqli_num_rows($result_sec_6_2_1);
        while ($row_section_6_2_1 = mysqli_fetch_assoc($result_sec_6_2_1)) {

            $row_6_2['sec_6_2_designation_' . $d] = $row_section_6_2_1['designation'];
            $row_6_2['sec_6_2_name_' . $d] = $row_section_6_2_1['full_name'];
            $row_6_2['sec_6_2_father_name_' . $d] = $row_section_6_2_1['father_name'];
            $row_6_2['sec_6_2__mob_no_' . $d] = $row_section_6_2_1['mobile_no'];

            $d++;
        }
    } else {
        $row_6_2['count'] = 1;
        $row_6_2['sec_6_2_designation_' . $d] = "";
        $row_6_2['sec_6_2_name_' . $d] = '';
        $row_6_2['sec_6_2_father_name_' . $d] = '';
        $row_6_2['sec_6_2__mob_no_' . $d] = '';
    }

    $sql = 'select * from survey_invoice_sec_3_new_1 where survey_id = "' . $row_invoice['sno'] . '"';
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

    $sql = 'select * from survey_invoice_sec_2_1_2 where survey_id="' . $row_invoice['sno'] . '"';
    $res_2_1_2 = execute_query($sql);
    $i = 1;
    $a = 1;
    $other_msc = array();
    if (mysqli_num_rows($res_2_1_2) != 0) {
        $row_2_1_2['count'] = mysqli_num_rows($res_2_1_2);
        while ($row_temp = mysqli_fetch_assoc($res_2_1_2)) {
            if ($row_temp['other_description'] == 'msc') {
                $other_msc[$a] = $row_temp['other_amount'];
                $a++;
            } else {
                $row_2_1_2['sec_2_1_2_business_description_' . $i] = $row_temp['other_description'];
                $row_2_1_2['sec_2_1_2_value_' . $i] = $row_temp['other_amount'];
                $i++;
            }
        }
        $_POST['sec_1_1_2_msc_service'] = $other_msc;
        $row_2_1_2['count'] = $i - 1;
    } else {
        $row_2_1_2['count'] = 1;
        $row_2_1_2['sec_2_1_2_business_description_' . $i] = '';
        $row_2_1_2['sec_2_1_2_value_' . $i] = '';
    }

    $human_rows_1 = [];
    $sql_human = 'SELECT * FROM survey_invoice_16_1 WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $result_human = mysqli_query($db, $sql_human);

    if ($result_human && mysqli_num_rows($result_human) > 0) {
        while ($row_h = mysqli_fetch_assoc($result_human)) {
            $human_rows_1[] = [
                'post_id'        => $row_h['post_id'],
                'name'           => $row_h['name'],
                'father_name'    => $row_h['father_name'],
                'address'        => $row_h['address'],
                'dob'            => $row_h['dob'],
                'education'      => $row_h['education'],
                'computer_exp'   => $row_h['computer_exp']
            ];
        }
    }   

    $sql = 'select * from survey_invoice_plot_details where survey_id = "' . $row_invoice['sno'] . '"';
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
    } else {

        $row_new_plot['sec_new_plot_area'] = '';
        $row_new_plot['sec_new_plot_revenue_status'] = '';
        $row_new_plot['sec_new_plot_reason_for_not_record'] = '';
        $row_new_plot['sec_new_plot_practices_if_not'] = '';
        $row_new_plot['sec_new_plot_gata_no'] = '';
        $row_new_plot['sec_3_building_area'] = '';
        $row_new_plot['sec_3_building_rent'] = '';
        $row_new_plot['sec_3_remark'] = '';
        $row_new_plot['sec_new_remarks'] = '';
    }

    $sql = 'SELECT * FROM survey_invoice_sec_3_1 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_3_1 = execute_query($sql);

    if (mysqli_num_rows($res_3_1) > 0) {
        $row_3_1 = mysqli_fetch_assoc($res_3_1);
    } else {
        $row_3_1 = [
            'east_side' => '',
            'west_side' => '',
            'north_side' => '',
            'south_side' => '',
            'on_road_land' => '',
            'front_side' => '',
            'remarks' => '',
        ];
    }

    $sql = 'select * from survey_invoice_sec_3_4 where survey_id="' . $row_invoice['sno'] . '"';
    //echo $sql;
    $res_3_4 = execute_query($sql);
    if (mysqli_num_rows($res_3_4) != 0) {
        $i = 1;
        while ($row_3_4_temp = mysqli_fetch_assoc($res_3_4)) {
            $row_3_4['sec_3_b_storage_capacity_' . $i] = $row_3_4_temp['storage_capacity'];
            $row_3_4['sec_3_b_godown_year_' . $i] = $row_3_4_temp['godown_year'];
            $row_3_4['sec_3_b_wdra_certified_' . $i] = $row_3_4_temp['wdra_certified'];
            $row_3_4['sec_3_b_godown_type_of_fund_' . $i] = $row_3_4_temp['type_of_fund'];
            $row_3_4['sec_3_b_godown_status_' . $i] = $row_3_4_temp['construction_status'];
            $row_3_4['sec_3_b_godown_comment_' . $i] = $row_3_4_temp['remarks'];
            $i++;
        }
        $row_3_4['count'] = $i - 1;
    } else {
        $i = 1;
        $row_3_4['count'] = 1;
        $row_3_4['sec_3_b_storage_capacity_' . $i] = '';
        $row_3_4['sec_3_b_godown_year_' . $i] = '';
        $row_3_4['sec_3_b_wdra_certified_' . $i] = '';
        $row_3_4['sec_3_b_godown_type_of_fund_' . $i] = '';
        $row_3_4['sec_3_b_godown_status_' . $i] = '';
        $row_3_4['sec_3_b_godown_comment_' . $i] = '';
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
            $row_3_5['sec_3_c_suitable_godown_' . $i] = $row_3_5_side['suitable_godown'];
            $row_3_5['sec_3_c_rak_distance_' . $i] = $row_3_5_side['rak_distance'];
            $row_3_5['sec_3_c_approach_road_' . $i] = $row_3_5_side['approach_road'];
            $row_3_5['sec_3_c_paved_road_' . $i] = $row_3_5_side['approach_road'];
            $i++;
        }
        $row_3_5['sec_3_c_id'] = $i - 1;
    } else {
        $i = 1;
        $row_3_5['sec_3_c_id'] = 1;
        $row_3_5['sec_3_c_length_1'] = "";
        $row_3_5['sec_3_c_vacant_land_status_1'] = "";
        $row_3_5['sec_3_c_land_location_1'] = "";
        $row_3_5['sec_3_c_suitable_godown_1'] = "";
        $row_3_5['sec_3_c_rak_distance_1'] = "";
        $row_3_5['sec_3_c_approach_road_1'] = "";
        $row_3_5['sec_3_c_paved_road_1'] = "";
        $row_3_5['food_scheme' . $i] = "";
    }
    $sql = 'select * from survey_invoice_sec_2_1 where survey_id="' . $row_invoice['sno'] . '"';
    // $sql = 'select *, concat("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", approach_road_photo) as new_approach_road_photo  from survey_invoice_sec_2_1 where survey_id="' . $row_invoice['sno'] . '"';
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
    } else {
        $row_2_1['investment'] = '';
        $row_2_1['loan'] = '';
        $row_2_1['msp'] = '';
        $row_2_1['msp_comm'] = '';
        $row_2_1['subscribers'] = '';
        $row_2_1['pds'] = '';
        $row_2_1['total_business'] = '';
        $row_2_1['last_year_profit_loss'] = '';
        $row_2_1['last_year_pl_amount'] = '';
        $row_2_1['seq_year_profit_loss'] = '';
        $row_2_1['seq_year_pl_amount'] = '';
        $row_2_1['financial_audit_year'] = '';
        $row_2_1['approach_road_photo'] = '';

        $row_2_1['construction_status'] = '';
        $row_2_1['approach_road'] = '';
        $row_2_1['distance_from_approach_road'] = '';
        $row_2_1['electric_connection'] = '';
        $row_2_1['electric_connection_proposal'] = '';
        $row_2_1['internet_connectivity'] = '';
        $row_2_1['sec_6_access_road'] = '';
        $row_2_1['sec_6_2_truck_not_reach'] = '';
        $row_2_1['sec_7_electrical_connection'] = '';
        $row_2_1['sec_7_electrical_connection_working'] = '';
        $row_2_1['sec_7_if_yes'] = '';
        $row_2_1['sec_8_internet_connection'] = '';
        $row_2_1['sec_8_plot_frontage'] = '';
        $row_2_1['sec_6_paved_road'] = '';
    }
    $sql = 'select * from survey_invoice_new_sec_8 where survey_id="' . $row_invoice['sno'] . '"';
    // $sql = 'SELECT *, 
    // 	 CONCAT("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/" , toilet_available_image) AS toilet_available_image_new, 
    // 	 CONCAT("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", toilet_available_women_image) AS toilet_available_women_image_new FROM survey_invoice_new_sec_8 WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $res_8 = execute_query($sql);
    if (mysqli_num_rows($res_8) != 0) {
        $row_8 = mysqli_fetch_assoc($res_8);

        $row_8['sec_8_electrical_connection'] = $row_8['electrical_connection'];
        $row_8['sec_8_electrical_connection_working'] = $row_8['electrical_connection_working'];
        $row_8['sec_8_bill_paid_yes_no'] = $row_8['bill_paid_yes_no'];
        $row_8['sec_8_electricity_not_available_reason'] = $row_8['electricity_not_available_reason'];
        $row_8['sec_8_electricity_not_available_remark'] = $row_8['electricity_not_available_remark'];
        $row_8['sec_8_bill_not_paid_month'] = $row_8['bill_not_paid_month'];
        $row_8['sec_8_outstanding_amount'] = $row_8['outstanding_amount'];

        $row_8['sec_8_solar_connection'] = $row_8['solar_connection'];
        $row_8['sec_8_solar_work_status'] = $row_8['solar_work_status'];
        $row_8['sec_8_solar_bill_paid'] = $row_8['solar_bill_paid'];
        $row_8['sec_8_solar_rooftop'] = $row_8['roof_top'];
        $row_8['sec_8_solar_remark'] = $row_8['solar_remark'];
        $row_8['sec_8_solar_date'] = $row_8['solar_date'];
        $row_8['sec_8_solar_outstanding_amount'] = $row_8['solar_outstanding_amount'];

        $row_8['sec_8_internet_connection'] = $row_8['internet_connection'];
        $row_8['sec_8_internet_service_provider'] = $row_8['internet_service_provider'];
        $row_8['sec_8_internet_bill_paid'] = $row_8['internet_bill_paid'];
        $row_8['sec_8_select_internet_operator'] = $row_8['select_internet_operator'];
        // $row_8['internet_not_bill_paid_month'] = $row_8['internet_not_bill_paid_month'];
        $row_8['sec_8_internet_outstanding_amount'] = $row_8['internet_outstanding_amount'];

        $row_8['sec_8_narrow_tubes'] = $row_8['narrow_tubes'];
        $row_8['sec_8_water_tank'] = $row_8['water_tank'];
        $row_8['sec_8_samarsabel'] = $row_8['samarsabel'];
        $row_8['sec_8_handpump'] = $row_8['handpump'];
    } else {
        $row_8['sec_8_electrical_connection'] = '';
        $row_8['sec_8_electrical_connection_working'] = '';
        $row_8['sec_8_bill_paid_yes_no'] = '';
        $row_8['sec_8_electricity_not_available_reason'] = '';
        $row_8['sec_8_electricity_not_available_remark'] = '';
        $row_8['sec_8_bill_not_paid_month'] = '';
        $row_8['sec_8_outstanding_amount'] = '';

        $row_8['sec_8_solar_connection'] = '';
        $row_8['sec_8_solar_work_status'] = '';
        $row_8['sec_8_solar_bill_paid'] = '';
        $row_8['sec_8_solar_rooftop'] = '';
        $row_8['sec_8_solar_remark'] = '';
        $row_8['sec_8_solar_date'] = '';

        $row_8['sec_8_internet_connection'] = '';
        $row_8['sec_8_internet_service_provider'] = '';
        $row_8['sec_8_internet_bill_paid'] = '';
        $row_8['sec_8_select_internet_operator'] = '';
        $row_8['internet_not_bill_paid_month'] = '';

        $row_8['sec_8_narrow_tubes'] = '';
        $row_8['sec_8_water_tank'] = '';
        $row_8['sec_8_samarsabel'] = '';
        $row_8['sec_8_handpump'] = '';
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
                        <form action="scripts/ajax_ukvib.php" method="post" enctype="multipart/form-data" id="user_form"
                            name="user_form">
                            <div id="steps-container">
                                <!-------------------1st start--------------------------------------------------------------------->
                                <div class="step">
                                    <?php echo $msg; ?>

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
                                                            value="उत्तर प्रदेश राज्य निर्माण सहकारी संघ लि०, उ०प्र०"
                                                            class="form-control">
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
                                                            value="<?php echo ($row_invoice['division_name']); ?>"
                                                            placeholder="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>जिला :</label>
                                                        <input type="text" class="form-control" id="district_name"
                                                            name="district_name"
                                                            value="<?php echo ($row_invoice['district_name']); ?>"
                                                            placeholder="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>तहसील :</label>
                                                        <input type="text" class="form-control" id="tehseel_name"
                                                            name="tehseel_name"
                                                            value="<?php echo ($row_invoice['tehseel_name']); ?>"
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
                                                            value="<?php echo ($row_invoice['mobile_number']); ?>"
                                                            placeholder="">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>नगर पालिका / नगर पंचायत / नगर निगम</label>
                                                        <select class="form-control" id="nagar_nigam" name="nagar_nigam"
                                                            tabindex="<?php echo $tab++; ?>">
                                                            <option value="">--Select--</option>
                                                            <option value="1" <?php echo ($row_invoice['nagar_nigam'] == '1') ? 'selected' : ''; ?>
                                                                >नगर पालिका</option>
                                                            <option value="2" <?php echo ($row_invoice['nagar_nigam'] == '2') ? 'selected' : ''; ?>
                                                                >नगर पंचायत</option>
                                                            <option value="3" <?php echo ($row_invoice['nagar_nigam'] == '3') ? 'selected' : ''; ?>
                                                                >नगर निगम</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
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

                                            <div class="col-sm-3 form-group">
                                                <label>ई-मेल आई.डी.</label>
                                                <input type="text" name="email_id" id="email_id"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['email_id']); ?>">
                                            </div>

                                            <?php if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) { ?>
                                            <div class="col-sm-2 form-group">
                                                <label>मुख्यालय की फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="photo_id" id="photo_id" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control">
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <img src="<?php echo htmlspecialchars($row_invoice['photo_id']); ?>"
                                                    class="img-fluid img-thumbnail" style="height:50px;"
                                                    id="photo_id_uploaded">
                                                <label><a
                                                        href="<?php echo htmlspecialchars($row_invoice['photo_id']); ?>"
                                                        target="_blank">संलग्न फोटो देखें</a></label>
                                            </div>
                                            <?php } else { ?>
                                            <div class="col-sm-3 form-group">
                                                <label>मुख्यालय की फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="photo_id" id="photo_id" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control">
                                            </div>
                                            <?php } ?>

                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                <label>समिति पंजीकरण संख्या</label>
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_no']); ?>">
                                            </div>
                                            <br>
                                            <br>
                                            <div class="col-sm-2 form-group">
                                                <label>समिति पंजीकरण दिनांक</label>
                                                <label><small>नहीं पता होने की स्थिति में आज का ही दिनांक
                                                        दर्शायें</small></label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_date']); ?>">
                                            </div>
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
                                        <h5>
                                            <img src="images/logo/2.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;">
                                            1.1 सदस्यों का विवरण
                                        </h5>
                                        <br>

                                        <div class="col-sm-12">
                                            <small><b>(I) सदस्यों का विवरण</b></small><br>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>पंजीकरण के समय सदस्यों की संख्या</label>
                                                    <input type="text" name="members_no" id="members_no"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['members_no']); ?>">
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    <label>वर्तमान में सदस्यों की संख्या</label>
                                                    <input type="text" name="active_members_no" id="active_members_no"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo ($row_invoice['active_members_no']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <small><b>(II) बनाए गए नए सदस्यों की संख्या :</b></small>
                                            <div class="row">
                                                <div class="col-sm-4 form-group">
                                                    <label>01-अप्रैल-2024 से बनाए गए नए सदस्यों की संख्या :</label>
                                                    <input type="text" name="new_members" id="new_members"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['new_members']); ?>">
                                                </div>
                                                <div class="col-sm-4 form-group">
                                                    <label>01-अप्रैल-2024 से प्राप्त अंशधन</label>
                                                    <input type="text" name="share_capital" id="share_capital"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['share_capital']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <small><b>(IV) सदस्यों की संख्या :</b></small>
                                            <div class="row">
                                                <div class="col-sm-2 form-group">
                                                    <input type="text" name="total_members" id="total_members"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['total_members']); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!----------------3 start-------------------------------------------------------->

                                <div class="step">
                                    <h4><img src="images/logo/3.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 3. वित्तीय सूचना</h4>
                                    <div class="col-sm-12">
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
                                            <div class="col-sm-3 form-group">
                                                <label>संचित लाभ/हानि की स्थिति</label>
                                                <select name="sec_3_accumulated_1" id="sec_3_accumulated_1"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="profit" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                    <option value="loss" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>(धनराशि लाख मे)</label>
                                                <input type="text" name="sec_3_accumulated_amount_1"
                                                    id="sec_3_accumulated_amount_1" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control chk_decimal"
                                                    data-type="7.I वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                                    value="<?php echo isset($row_3_new_1['accumulated_amount_1']) ? $row_3_new_1['accumulated_amount_1'] : ''; ?>">
                                            </div>
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
                                            <div class="col-sm-3 form-group">
                                                <label>संचित लाभ/हानि की स्थिति</label>
                                                <select name="sec_3_accumulated_2" id="sec_3_accumulated_2"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="profit" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                    <option value="loss" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>(धनराशि लाख मे)</label>
                                                <input type="text" name="sec_3_accumulated_amount_2"
                                                    id="sec_3_accumulated_amount_2" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control chk_decimal"
                                                    data-type="7.II वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                                    value="<?php echo isset($row_3_new_1['accumulated_amount_2']) ? $row_3_new_1['accumulated_amount_2'] : ''; ?>">
                                            </div>
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
                                            <div class="col-sm-3 form-group">
                                                <label>संचित लाभ/हानि की स्थिति</label>
                                                <select name="sec_3_accumulated_3" id="sec_3_accumulated_3"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="profit" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ</option>
                                                    <option value="loss" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>(धनराशि लाख मे)</label>
                                                <input type="text" name="sec_3_accumulated_amount_3"
                                                    id="sec_3_accumulated_amount_3" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control chk_decimal"
                                                    data-type="7.III लाभांश को धनराशि रु० लाख मे भरे"
                                                    value="<?php echo isset($row_3_new_1['accumulated_amount_3']) ? $row_3_new_1['accumulated_amount_3'] : ''; ?>">
                                            </div>
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
                                                    <option value="A" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'A' ? 'selected="selected"' : '' ?>>A</option>
                                                    <option value="B" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'B' ? 'selected="selected"' : '' ?>>B</option>
                                                    <option value="C" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'C' ? 'selected="selected"' : '' ?>>C</option>
                                                    <option value="D" <?php echo isset($row_3_new_1['audit_grading']) && $row_3_new_1['audit_grading'] == 'D' ? 'selected="selected"' : '' ?>>D</option>
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
                                    <h4><img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 4. अन्य कार्य व व्यवसाय</h4>
                                    <div id="other_business">
                                        <?php
                                        for ($i = 1; $i <= $row_2_1_2['count']; $i++) {
                                            ?>
                                            <div class="row" id="business_row_<?php echo $i; ?>">
                                                <div class="col-sm-4 form-group">
                                                    <label>व्यवसाय का विवरण </label>
                                                    <select name="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                        tabindex="<?php echo $tab++; ?>"
                                                        id="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                        class="form-control">
                                                        <option value="">--select--</option>
                                                        <option value="cattle_feed" <?php echo ($row_2_1_2['sec_2_1_2_business_description_' . $i] == 'cattle_feed') ? 'selected="selected"' : ''; ?>>कैटल फीड
                                                        </option>
                                                        <option value="any_other" <?php echo ($row_2_1_2['sec_2_1_2_business_description_' . $i] == 'any_other') ? 'selected="selected"' : ''; ?>>अन्य</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-4 form-group">
                                                    <label>वार्षिक टर्नोवर</label>
                                                    <input type="text" name="sec_2_1_2_value_<?php echo $i; ?>"
                                                        tabindex="<?php echo $tab++; ?>"
                                                        id="sec_2_1_2_value_<?php echo $i; ?>"
                                                        class="form-control chk_decimal"
                                                        data-type="7.3.I वार्षिक टर्नोवर को धनराशि रु० लाख मे भरे"
                                                        value="<?php echo $row_2_1_2['sec_2_1_2_value_' . $i]; ?>">
                                                </div>
                                                <?php if ($i == $row_2_1_2['count']) { ?>
                                                    <div class="col-sm-2 form-group my-auto"
                                                        id="add_business_row_<?php echo $i; ?>"
                                                        tabindex="<?php echo $tab++; ?>">
                                                        <button type="button" class="btn btn-info"
                                                            onclick="add_more_business();">नईं पंक्ति जोड़े [+]</button>
                                                        <input type="hidden" name="other_business_id" id="other_business_id"
                                                            value="<?php echo $row_2_1_2['count']; ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!----------------2.1 start-------------------------------------------------------->

                                <div class="step">
                                    <h5>
                                        <img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 5. मानव सम्पदा
                                    </h5>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>पद</th>
                                                        <th>नाम</th>
                                                        <th>पिता का नाम</th>
                                                        <th>पता</th>
                                                        <th>जन्मतिथि</th>
                                                        <th>शैक्षिक योग्यता</th>
                                                        <th>कंप्यूटर अनुभव</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Define static posts
                                                    $posts = [
                                                        ['id' => 1, 'post_name' => 'सभापति'],
                                                        ['id' => 2, 'post_name' => 'सचिव']
                                                    ];

                                                    $i = 1;

                                                    foreach ($posts as $row) {
                                                        // find if this post_id exists in fetched $human_rows_1 (if applicable)
                                                        $existing = array_filter($human_rows_1 ?? [], function ($hr) use ($row) {
                                                            return $hr['post_id'] == $row['id'];
                                                        });

                                                        $existing = reset($existing);

                                                        $name = $existing['name'] ?? '';
                                                        $name = $existing['name'] ?? '';
                                                        $father_name = $existing['father_name'] ?? '';
                                                        $address = $existing['address'] ?? '';
                                                        $dob = $existing['dob'] ?? '';
                                                        $education = $existing['education'] ?? '';
                                                        $computer_exp = $existing['computer_exp'] ?? '';
                                                        ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($row['post_name']); ?></td>
                                                            <td><input type="text" class="form-control"
                                                                    name="name[<?php echo $row['id']; ?>]"
                                                                    value="<?php echo htmlspecialchars($name); ?>"></td>
                                                            <td><input type="text" class="form-control"
                                                                    name="father_name[<?php echo $row['id']; ?>]"
                                                                    value="<?php echo htmlspecialchars($father_name); ?>"></td>
                                                            <td><input type="text" class="form-control"
                                                                    name="address[<?php echo $row['id']; ?>]"
                                                                    value="<?php echo htmlspecialchars($address); ?>"></td>
                                                            <td><input type="date" class="form-control"
                                                                    name="dob[<?php echo $row['id']; ?>]"
                                                                    value="<?php echo htmlspecialchars($dob); ?>"></td>
                                                            <td><input type="text" class="form-control"
                                                                    name="education[<?php echo $row['id']; ?>]"
                                                                    value="<?php echo htmlspecialchars($education); ?>"></td>
                                                            <td><input type="text" class="form-control"
                                                                    name="computer_exp[<?php echo $row['id']; ?>]"
                                                                    value="<?php echo htmlspecialchars($computer_exp); ?>"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>

                                            <h5><img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                                    style="height:50px; width:50px;"> (II) समिति की प्रबंध कमेटी</h5>
                                            <div class="row">
                                                <div class="col-md-3"><label for="">प्रबंध कमेटी निर्वाचित है?</label>
                                                    <select name="sec_6_2_mgt_committee_is_elected"
                                                        id="sec_6_2_mgt_committee_is_elected" class="form-control"
                                                        onChange="hide_show(this.value, '#sec_6_2_election_year', 'yes'); hide_show(this.value, '#sec_6_2_end_year', 'yes'); hide_show(this.value, '#guard_count2', 'yes')">
                                                        <option value="">--Select--</option>
                                                        <option value="yes" <?php if ($row_sec_6_2['sec_6_2_mgt_committee_is_elected'] == 'yes') {
                                                            echo 'selected="selected"';
                                                        } ?>>निर्वाचित है</option>

                                                        <option value="no" <?php if ($row_sec_6_2['sec_6_2_mgt_committee_is_elected'] == 'no') {
                                                            echo 'selected="selected"';
                                                        } ?>>प्रशासनिक कमेटी</option>

                                                    </select>
                                                </div>

                                                <div class="col-sm-3 form-group" id="sec_6_2_election_year"
                                                    style="display:none;">
                                                    <label>निर्वाचन का वर्ष</label>
                                                    <select name="sec_6_2_election_year" id="sec_6_2_election_year"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                        <option value="">--Select--</option>
                                                        <?php
                                                        for ($i = 2024; $i >= 1975; $i--) {
                                                            echo '<option value="' . $i . '" ';
                                                            if ($i == $row_sec_6_2['sec_6_2_election_year']) {
                                                                echo ' selected="selected" ';
                                                            }
                                                            echo ' >' . $i . '</option>';
                                                        }
                                                        ?>

                                                    </select>
                                                </div>
                                                <div class="col-sm-3 form-group" id="sec_6_2_end_year"
                                                    style="display:none;">
                                                    <label>निर्वाचित कमेटी की कार्यावधि पूर्ण होने का वर्ष</label>
                                                    <select name="sec_6_2_end_year" id="sec_6_2_end_year"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                        <option value="">--Select--</option>
                                                        <?php
                                                        for ($i = 2024; $i <= 2030; $i++) {
                                                            echo '<option value="' . $i . '" ';
                                                            if ($i == $row_sec_6_2['sec_6_2_end_year']) {
                                                                echo ' selected="selected" ';
                                                            }
                                                            echo ' >' . $i . '</option>';
                                                        }
                                                        ?>

                                                    </select>
                                                </div>
                                            </div>

                                            <div id="sec_2_b">
                                                <?php
                                                // $row_6_2['count'] = 1;
                                                for ($i = 1; $i <= $row_6_2['count']; $i++) {
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-sm-2 form-group">
                                                            <label>पदनाम</label>
                                                            <select class="form-control"
                                                                id="sec_6_2_designation_<?php echo $i; ?>"
                                                                name="sec_6_2_designation_<?php echo $i; ?>"
                                                                tabindex="<?php echo $tab++; ?>">
                                                                <option value="">--Select--</option>
                                                                <option value="अध्यक्ष" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'अध्यक्ष' ? 'selected="selected"' : ''; ?>>अध्यक्ष</option>
                                                                <option value="उपाध्यक्ष" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'उपाध्यक्ष' ? 'selected="selected"' : ''; ?>>उपाध्यक्ष
                                                                </option>
                                                                <option value="संचालक" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'संचालक' ? 'selected="selected"' : ''; ?>>संचालक</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <label>नाम</label>
                                                            <input type="text" name="sec_6_2_name_<?php echo $i; ?>"
                                                                id="sec_6_2_name_<?php echo $i; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_text"
                                                                data-type="4.II नाम शब्दों में भरे"
                                                                value="<?php echo $row_6_2['sec_6_2_name_' . $i]; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <label>पिता / पति का नाम</label>
                                                            <input type="text" name="sec_6_2_father_name_<?php echo $i; ?>"
                                                                id="sec_6_2_father_name_<?php echo $i; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_text"
                                                                data-type="4.II पिता का नाम शब्दों में भरे"
                                                                value="<?php echo $row_6_2['sec_6_2_father_name_' . $i]; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <label>मोबाईल नंबर</label>
                                                            <input type="text" name="sec_6_2__mob_no_<?php echo $i; ?>"
                                                                id="sec_6_2__mob_no_<?php echo $i; ?>"
                                                                tabindex="<?php echo $tab++; ?>"
                                                                class="form-control chk_mobile" data-minlength="10"
                                                                data-maxlength="10" data-type="4.II 10 अंकों मे भरे"
                                                                value="<?php echo $row_6_2['sec_6_2__mob_no_' . $i]; ?>">
                                                        </div>

                                                        <?php
                                                        if ($i == $row_6_2['count']) {
                                                            ?>
                                                            <div class="col-sm-2 form-group my-auto" id="sec_2_b_rows">
                                                                <button type="button" class="btn btn-info"
                                                                    onClick="sec_6_2_add_rows();">नई पंक्ति जोड़े [+]</button>

                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <input type="hidden" name="sec_6_2_id" id="sec_6_2_id" value="<?php echo $row_6_2['count']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <!--------------------------------------------------------------->
                                <div class="step">
                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 7. समिति भवन/सम्पत्ति का विवरण</h2>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h5>(I) समिति भवन का स्वामित्व </h5>
                                                    <div class="col-sm-3 form-group">
                                                        <label>(I)समिति भवन का स्वामित्व </label>
                                                        <select name="sec_3_ownership" id="sec_3_ownership"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onChange="hide_show(this.value, '#sec_3_rented', 'rent'); hide_show(this.value, '#sec_3_other', 'other');">
                                                            <option value="">--Select--</option>
                                                            <option value="own" <?php $sec_3_rented_display = 'none';
                                                            $sec_3_other_display = 'none';
                                                            $sec_3_display = 'flex';
                                                            if ($row_new_plot['sec_3_ownership'] == 'own') {
                                                                echo ' selected="selected" ';
                                                                $sec_3_display = 'flex';
                                                            } ?>>समिति
                                                                के
                                                                स्वामित्व में है</option>
                                                            <option value="rent" <?php if ($row_new_plot['sec_3_ownership'] == 'rent') {
                                                                echo ' selected="selected" ';
                                                                $sec_3_rented_display = 'flex';
                                                            } ?>>
                                                                किराये पर है</option>
                                                            <option value="other" <?php if ($row_new_plot['sec_3_ownership'] != 'rent' && $row_new_plot['sec_3_ownership'] != 'own' && $row_new_plot['sec_3_ownership'] != '') {
                                                                echo ' selected="selected" ';
                                                                $sec_3_other_display = 'flex';
                                                            } ?>
                                                                >
                                                                अन्य स्थिती</option>
                                                        </select>
                                                    </div>
                                                    <div id="sec_3_rented"
                                                        style="display: <?php echo $sec_3_rented_display; ?>">
                                                        <div class="col-sm-3 form-group">
                                                            <label>समिति भवन का मासिक किराया </label>
                                                            <input name="sec_3_building_rent" id="sec_3_building_rent"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_new_plot['sec_3_building_rent']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>समिति भवन का क्षेत्रफल (स्क्वायर मीटर में)</label>
                                                            <input name="sec_3_building_area" id="sec_3_building_area"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_new_plot['sec_3_building_area']; ?>">
                                                        </div>

                                                    </div>
                                                    <div id="sec_3_other"
                                                        style="display: <?php echo $sec_3_other_display; ?>">
                                                        <div class="col-sm-3 form-group">
                                                            <label>कृपया विवरण दर्ज करें</label>
                                                            <input name="sec_3_remark" id="sec_3_remark"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_new_plot['society_building_remark']; ?>">
                                                        </div>
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

                                                        <div class="col-sm-3 form-group">
                                                            <label>टिप्पणी</label>
                                                            <input type="text" name="sec_new_remarks"
                                                                id="sec_new_remarks" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_new_plot['sec_new_remarks']; ?>">
                                                        </div>

                                                    </div>
                                                    <h5> (III) भूखंड की चौहद्दी का विवरण </h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की पूर्व दिशा का विवरण</label>
                                                            <input type="text" name="sec_3_a_land_chauhaddi_east"
                                                                id="sec_3_a_land_chauhaddi_east"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['east_side']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की पश्चिम दिशा का विवरण</label><input
                                                                type="text" name="sec_3_a_land_chauhaddi_west"
                                                                id="sec_3_a_land_chauhaddi_west"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['west_side']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की उत्तर दिशा का विवरण</label><input
                                                                type="text" name="sec_3_a_land_chauhaddi_north"
                                                                id="sec_3_a_land_chauhaddi_north"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['north_side']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की दक्षिण दिशा का विवरण</label><input
                                                                type="text" name="sec_3_a_land_chauhaddi_south"
                                                                id="sec_3_a_land_chauhaddi_south"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['south_side']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>सड़क पर भूमि कि लम्बाई (आन रोड जमीन) मीटर में</label>
                                                            <input type="text" name="sec_3_a_land_on_road"
                                                                id="sec_3_a_land_on_road"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['on_road_land']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>प्रमुख द्वार कि दिशा (फ्र्न्ट साईड)</label>
                                                            <select name="sec_3_a_land_frontage"
                                                                id="sec_3_a_land_frontage"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control">
                                                                <option>--Select--</option>
                                                                <option value="east" <?php if ($row_3_1['front_side'] == 'east') {
                                                                    echo 'selected="selected"';
                                                                } ?>>पूर्व</option>
                                                                <option value="west" <?php if ($row_3_1['front_side'] == 'west') {
                                                                    echo 'selected="selected"';
                                                                } ?>>पश्चिम</option>
                                                                <option value="north" <?php if ($row_3_1['front_side'] == 'north') {
                                                                    echo 'selected="selected"';
                                                                } ?>>उत्तर</option>
                                                                <option value="south" <?php if ($row_3_1['front_side'] == 'south') {
                                                                    echo 'selected="selected"';
                                                                } ?>>दक्षिण</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <h5> (IV) निर्मित भवन का विवरण </h5>
                                                    <div id="sec_2_nirmit_godown">
                                                        <?php
                                                        // $row_3_4['count'] = 1;
                                                        for ($i = 1; $i <= $row_3_4['count']; $i++) { ?>
                                                        <div class="row">
                                                            <div class="col-sm-2 form-group">
                                                                <label>क्षमता (मेट्रिक टन में)</label>
                                                                <input type="text"
                                                                    name="sec_3_b_storage_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_b_storage_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control chk_number"
                                                                    data-type="2. VI.क्षमता मेट्रिक टन में भरे"
                                                                    value="<?php echo $row_3_4['sec_3_b_storage_capacity_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>निर्माण का वर्ष</label>
                                                                <select name="sec_3_b_godown_year_<?php echo $i; ?>"
                                                                    id="sec_3_b_godown_year_<?php echo $i; ?>"
                                                                    class="form-control"
                                                                    tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--Select--</option>
                                                                    <option value="1999" <?php echo ($row_3_4['sec_3_b_godown_year_' . $i] == "1999") ? 'selected' : ''; ?>>2000 से पूर्व</option>
                                                                    <?php for ($a = 2000; $a <= 2024; $a++) { ?>
                                                                    <option value="<?php echo $a; ?>" <?php echo ($row_3_4['sec_3_b_godown_year_' . $i] == $a) ? 'selected' : ''; ?>>
                                                                        <?php echo $a; ?>
                                                                    </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>WDRA से प्रमाणित है?</label>
                                                                <select name="sec_3_b_wdra_certified_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    id="sec_3_b_wdra_certified_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select--</option>
                                                                    <option value="yes" <?php echo ($row_3_4['sec_3_b_wdra_certified_' . $i] == 'yes') ? ' selected="selected"' : ''; ?>
                                                                        >हाँ</option>
                                                                    <option value="no" <?php echo ($row_3_4['sec_3_b_wdra_certified_' . $i] == 'no') ? ' selected="selected"' : ''; ?>>नहीं</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>किस योजना के तहत बना है</label>
                                                                <input type="text"
                                                                    name="sec_3_b_godown_type_of_fund_<?php echo $i; ?>"
                                                                    id="sec_3_b_godown_type_of_fund_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_4['sec_3_b_godown_type_of_fund_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>गोदाम की भौतिक स्थिति</label>
                                                                <select name="sec_3_b_godown_status_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    id="sec_3_b_godown_status_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select--</option>
                                                                    <option value="good" <?php echo ($row_3_4['sec_3_b_godown_status_' . $i] == 'good') ? ' selected="selected"' : ''; ?>
                                                                        >अच्छा</option>
                                                                    <option value="repairable" <?php echo ($row_3_4['sec_3_b_godown_status_' . $i] == 'repairable') ? ' selected="selected"' : ''; ?>>खराब/मरम्मत योग्य</option>
                                                                    <option value="discarded" <?php echo ($row_3_4['sec_3_b_godown_status_' . $i] == 'discarded') ? ' selected="selected"' : ''; ?>>जर्जर/निष्प्रयोज्य्य</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-1 form-group">
                                                                <label>टिप्पणी</label>
                                                                <input type="text"
                                                                    name="sec_3_b_godown_comment_<?php echo $i; ?>"
                                                                    id="sec_3_b_godown_comment_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_4['sec_3_b_godown_comment_' . $i]; ?>">
                                                            </div>
                                                            <?php if ($i == $row_3_4['count']) { ?>
                                                            <div class="col-sm-1 form-group my-auto"
                                                                id="sec_2_nirmit_godown_rows">
                                                                <button type="button" class="btn btn-info"
                                                                    onClick="sec_2_nirmit_godown_add_rows()">नई पंक्ति
                                                                    जोड़े [+]</button>
                                                                <input type="hidden" name="sec_2_nirmit_godown_id"
                                                                    id="sec_2_nirmit_godown_id"
                                                                    value="<?php echo $row_3_4['count']; ?>">
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <h5> (V) खाली पड़ी भूमि का विवरण </h5>
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
                                                            <div class="col-sm-2 form-group">
                                                                <label>गोदाम के लिए उपयुक्त है या नहीं ?</label>
                                                                <select name="sec_3_c_suitable_godown_<?php echo $i; ?>"
                                                                    id="sec_3_c_suitable_godown_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes" <?php
                                                                    if ($row_3_5['sec_3_c_suitable_godown_' . $i] == 'yes') {
                                                                        echo ' selected="selected"';
                                                                    } ?>
                                                                        >हाँ
                                                                    </option>
                                                                    <option value="no" <?php if ($row_3_5['sec_3_c_suitable_godown_' . $i] == 'no') {
                                                                        echo ' selected="selected"';
                                                                    } ?>
                                                                        >नहीं
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>जनपद के रैक पाइण्ट से दूरी</label>
                                                                <input type="text"
                                                                    name="sec_3_c_rak_distance_<?php echo $i; ?>"
                                                                    id="sec_3_c_rak_distance_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_5['sec_3_c_rak_distance_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-2 form-group"
                                                                id="land_access_road_<?php echo $i; ?>">
                                                                <label>पहुच मार्ग का प्रकार</label>
                                                                <select name="sec_3_c_paved_road_<?php echo $i; ?>"
                                                                    id="sec_3_c_paved_road_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
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
                                                                    } ?>>ग्रामीण सड़क
                                                                    </option>
                                                                    <option value="other" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'other') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>अन्य
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <?php
                                                            if (!empty($row_3_5['food_scheme' . $i]) && file_exists($row_3_5['food_scheme' . $i])) {
                                                                ?>
                                                            <div class="col-sm-1 form-group" id="">
                                                                <img src="<?php echo $row_3_5['food_scheme' . $i]; ?>"
                                                                    class="img-fluid img-thumbnail" style="height:50px;"
                                                                    id="sec_3_c_image_uploaded_2">
                                                                <label><a
                                                                        href="<?php echo $row_3_5['food_scheme' . $i]; ?>"
                                                                        target="_blank">संलग्न फोटो
                                                                        देखें</a></label>

                                                            </div>
                                                            <?php
                                                            }
                                                            ?>
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

                                                    <h5>(VI) पहुंच मार्ग का विवरण</h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>पहुंच मार्ग</label>
                                                            <select name="sec_6_access_road" id="sec_6_access_road"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#access_road', 'proper'); hide_show(this.value, '#access_road_truck', 'ordinary');">
                                                                <option value="">--Select--</option>
                                                                <option value="proper" <?php echo $row_2_1['sec_6_access_road'] == 'proper' ? 'selected="selected"' : ''; ?>>पक्की सडक</option>
                                                                <option value="ordinary" <?php echo $row_2_1['sec_6_access_road'] == 'ordinary' ? 'selected="selected"' : ''; ?>>कच्ची सडक</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group" id="access_road"
                                                            style="display: none">
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
                                                        <div class="col-sm-3 form-group" id="access_road_truck"
                                                            style="display:none">
                                                            <label>यदि समिति भवन तक ट्रक नही पहुंचता है तो पक्के
                                                                मार्ग से समिति भवन की दूरी (मी. में)</label>
                                                            <input type="text" name="sec_6_2_truck_not_reach"
                                                                id="sec_6_2_truck_not_reach"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1['sec_6_2_truck_not_reach']; ?>">
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <label>भूखण्ड का फ्र्ण्टेज्‌ (आन रोड जमीन) मीटर
                                                                में</label>
                                                            <input type="text" name="sec_8_plot_frontage"
                                                                id="sec_8_plot_frontage"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1['sec_8_plot_frontage']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <!---------------7th Start---------------------------------------------------------------->
                                <div class="step">
                                    <h4><img src="images/logo/8.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 8. सुविधाएं </h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <h5>(I) विद्युत कनेक्शन</h5>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-4 form-group">
                                                        <label>विद्युत कनेक्शन है या नहीं ?</label>
                                                        <select name="sec_8_electrical_connection"
                                                            id="sec_8_electrical_connection"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onChange="hide_show(this.value, '#electricity_not_available', 'no'); hide_show(this.value, '#electricity_available', 'yes');  handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--select-- </option>
                                                            <option value="yes" <?php echo $row_8['sec_8_electrical_connection'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>

                                                            <option value="no" <?php echo $row_8['sec_8_electrical_connection'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>

                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="electricity_available"
                                                        style="display: none">
                                                        <label>यदि है तो चालू है या नहीं ?</label>
                                                        <select name="sec_8_electrical_connection_working"
                                                            id="sec_8_electrical_connection_working"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onChange="hide_show(this.value, '#electricity_available_not_working', 'no');hide_show(this.value, '#sec_8_bill_paid1', 'yes'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--select-- </option>
                                                            <option value="yes" <?php echo $row_8['sec_8_electrical_connection_working'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>

                                                            <option value="no" <?php echo $row_8['sec_8_electrical_connection_working'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>


                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="sec_8_bill_paid1"
                                                        style="display:none;">
                                                        <label>बिल नियमित भुगतान हो रहा है या नहीं ?</label>
                                                        <select name="sec_8_bill_paid_yes_no"
                                                            id="sec_8_bill_paid_yes_no" class="form-control"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            onchange="hide_show(this.value, '#sec_7_bill_status', 'no'); hide_show(this.value, '#sec_8_bill_paid2', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--select-- </option>
                                                            <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_bill_paid_yes_no'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>

                                                            <option style="background:#f00" value="no" <?php echo $row_8['sec_8_bill_paid_yes_no'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>

                                                        </select>
                                                    </div>

                                                    <div class="col-sm-3 form-group"
                                                        id="electricity_available_not_working" style="display: none">
                                                        <label>यदि चालू नहीं है तो कारण</label>
                                                        <input type="text" name="sec_8_electricity_not_available_reason"
                                                            id="sec_8_electricity_not_available_reason"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_8['sec_8_electricity_not_available_reason']; ?>">
                                                    </div>

                                                    <div class="col-sm-3 form-group" id="electricity_not_available"
                                                        style="display: none">
                                                        <label>यदि नहीं है तो प्रस्ताव</label>
                                                        <textarea name="sec_8_electricity_not_available_remark"
                                                            id="sec_8_electricity_not_available_remark"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            class="form-control"><?php echo $row_8['sec_8_electricity_not_available_remark']; ?></textarea>
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="sec_8_bill_paid2"
                                                        style="display:none;">
                                                        <label>बिल पेड कितने माह से नहीं है ?</label>
                                                        <input type="text" name="sec_8_bill_not_paid_month"
                                                            id="sec_8_bill_not_paid_month chk_number"
                                                            data-type="3.I बिल पेड माह को अंकों मे लिखे "
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_8['sec_8_bill_not_paid_month']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="sec_7_bill_status"
                                                        style="display:none;">
                                                        <label>अगर बकाया है तो धनराशि लिखे</label>
                                                        <input type="text" name="sec_8_outstanding_amount"
                                                            id="sec_8_outstanding_amount chk_decimal"
                                                            data-type="3.I बकाया धनराशि रु मे लिखे"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_8['sec_8_outstanding_amount']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <h5>(II) सोलर कनेक्शन</h5>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-4 form-group">
                                                        <label>सोलर की उपलब्धता है या नहीं ?</label>
                                                        <select class="form-control" value=""
                                                            id="sec_8_solar_connection" name="sec_8_solar_connection"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            onChange="hide_show(this.value, '#sec_8_solar_work', 'yes');hide_show(this.value, '#sec_8_solar_remark', 'no');hide_show(this.value, '#sec_8_solar_rooftop', 'no');handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--Select--</option>
                                                            <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_solar_connection'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>

                                                            <option style="background:#f00" value="no" <?php echo $row_8['sec_8_solar_connection'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>

                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="sec_8_solar_work"
                                                        style="display:none">
                                                        <label>यदि है तो चालू है या नहीं ?</label>
                                                        <select name="sec_8_solar_work_status"
                                                            id="sec_8_solar_work_status" class="form-control"
                                                            tabindex="<?php echo $tab++; ?>"
                                                            onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546'); hide_show(this.value, '#sec_8_solar_bill', 'yes');">
                                                            <option value="">--select--</option>
                                                            <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_solar_work_status'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>

                                                            <option style="background:#f00" value="no" <?php echo $row_8['sec_8_solar_work_status'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>


                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="sec_8_solar_bill"
                                                        style="display:none">
                                                        <label>बैट्री की स्थिति</label>
                                                        <select name="sec_8_solar_bill_paid" id="sec_8_solar_bill_paid"
                                                            class="form-control"
                                                            onchange="hide_show(this.value, '#sec_8_solar_bill_status', 'poor');">
                                                            <option value="">--select--</option>
                                                            <option style="background:#0f0" value="good" <?php echo $row_8['sec_8_solar_bill_paid'] == 'good' ? 'selected="selected"' : ''; ?>>अच्छी</option>

                                                            <option style="background:#f00" value="poor" <?php echo $row_8['sec_8_solar_bill_paid'] == 'poor' ? 'selected="selected"' : ''; ?>>खराब</option>

                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5>(III) इण्टरनेट कनेक्शन</h5>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>क्या इण्टरनेट कनेक्शन है।</label>
                                                        <select name="sec_8_internet_connection"
                                                            id="sec_8_internet_connection"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onChange="hide_show(this.value, '#net_con_available', 'yes'); hide_show(this.value, '#sec_8_internet_active', 'yes'); hide_show(this.value, '#net_con_not', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546'); ">
                                                            <option value="">--select-- </option>
                                                            <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_internet_connection'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>
                                                            <option style="background:#f00" value="no" <?php echo $row_8['sec_8_internet_connection'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <div class="col-sm-12" id="net_con_available" style="display: none">
                                                        <div class="row">
                                                            <div class="col-sm-4 form-group">
                                                                <label>यदि है तो सर्विस प्रोवाइडर का नाम</label>
                                                                <select name="sec_8_internet_service_provider"
                                                                    id="sec_8_internet_service_provider"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
                                                                    <option value="">--Select--</option>

                                                                    <option value="bsnl" <?php if ($row_8['sec_8_internet_service_provider'] == 'bsnl') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        BSNL
                                                                    </option>
                                                                    <option value="jio" <?php if ($row_8['sec_8_internet_service_provider'] == 'jio') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        JIO
                                                                    </option>
                                                                    <option value="vodafone" <?php if ($row_8['sec_8_internet_service_provider'] == 'vodafone') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        Vodafone
                                                                    </option>
                                                                    <option value="airtel" <?php if ($row_8['sec_8_internet_service_provider'] == 'airtel') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        Airtel
                                                                    </option>
                                                                    <option value="sdwan" <?php if ($row_8['sec_8_internet_service_provider'] == 'sdwan') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        SDWAN
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                <label>बिल नियमित भुगतान हो रहा है या नहीं
                                                                    ?</label>
                                                                <select name="sec_8_internet_bill_paid"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    id="sec_8_internet_bill_paid" class="form-control">
                                                                    <option value="">--select-- </option>
                                                                    <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_internet_bill_paid'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ
                                                                    </option>

                                                                    <option style="background:#f00" value="no" <?php echo $row_8['sec_8_internet_bill_paid'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं
                                                                    </option>


                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4 form-group">
                                                                <label>कनेक्शन एक्टिव है या नहीं ?</label>
                                                                <select name="sec_8_internet_active"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    id="sec_8_internet_active" class="form-control">
                                                                    <option value="">--select-- </option>
                                                                    <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_internet_bill_paid'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ
                                                                    </option>

                                                                    <option style="background:#f00" value="no" <?php echo $row_8['sec_8_internet_bill_paid'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं
                                                                    </option>


                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-sm-4 form-group" id="net_con_not"
                                                        style="display:none">
                                                        <label>क्षेत्र में उपलब्ध ईण्टरनेट सर्विस प्रोवाइडर
                                                            के
                                                            नाम (सभी उपलब्ध आपरेटर का चयन करें)</label>
                                                        <select name="sec_8_select_internet_operator[]"
                                                            id="sec_8_select_operator" tabindex="<?php echo $tab++; ?>"
                                                            multiple="multiple" class="form-control">
                                                            <?php
                                                            $internet_provider = explode(", ", $row_8['sec_8_select_internet_operator']);
                                                            ?>
                                                            <option value="bsnl" <?php if (in_array('bsnl', $internet_provider)) {
                                                                echo ' selected="selected"';
                                                            } ?>>BSNL</option>
                                                            <option value="jio" <?php if (in_array('jio', $internet_provider)) {
                                                                echo ' selected="selected"';
                                                            } ?>>JIO</option>
                                                            <option value="vodafone" <?php if (in_array('vodafone', $internet_provider)) {
                                                                echo ' selected="selected"';
                                                            } ?>>Vodafone
                                                            </option>
                                                            <option value="airtel" <?php if (in_array('airtel', $internet_provider)) {
                                                                echo ' selected="selected"';
                                                            } ?>>Airtel
                                                            </option>
                                                            <option value="sdwan" <?php if (in_array('sdwan', $internet_provider)) {
                                                                echo ' selected="selected"';
                                                            } ?>>SDWAN
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <h5>(IV) पेयजल की उपलब्धता</h5>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>सरकारी नलके का पानी</label>
                                                        <select name="sec_8_narrow_tubes" id="sec_8_narrow_tubes"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--select-- </option>
                                                            <option value="yes" <?php echo $row_8['sec_8_narrow_tubes'] == 'yes' ? 'selected="selected"' : ''; ?> style="background:#0f0">
                                                                हाँ </option>
                                                            <option value="no" <?php echo $row_8['sec_8_narrow_tubes'] == 'no' ? 'selected="selected"' : ''; ?>>
                                                                नहीं</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>पानी कि टंकी</label>
                                                        <select name="sec_8_water_tank" id="sec_8_water_tank"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--select-- </option>
                                                            <option value="yes" <?php echo $row_8['sec_8_water_tank'] == 'yes' ? 'selected="selected"' : ''; ?> style="background:#0f0">
                                                                हाँ </option>
                                                            <option value="no" <?php echo $row_8['sec_8_water_tank'] == 'no' ? 'selected="selected"' : ''; ?>>
                                                                नहीं</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label> सबमर्सिबल </label>
                                                        <select name="sec_8_samarsabel" id="sec_8_samarsabel"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--select-- </option>
                                                            <option value="yes" <?php echo $row_8['sec_8_samarsabel'] == 'yes' ? 'selected="selected"' : ''; ?> style="background:#0f0">
                                                                हाँ </option>
                                                            <option value="no" <?php echo $row_8['sec_8_samarsabel'] == 'no' ? 'selected="selected"' : ''; ?>>
                                                                नहीं</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label> हैंड पंप </label>
                                                        <select name="sec_8_handpump" id="sec_8_handpump"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--select-- </option>

                                                            <option value="yes" <?php echo $row_8['sec_8_handpump'] == 'yes' ? 'selected="selected"' : ''; ?> style="background:#0f0">

                                                                हाँ </option>
                                                            <option value="no" <?php echo $row_8['sec_8_handpump'] == 'no' ? 'selected="selected"' : ''; ?>>
                                                                नहीं</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
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
                            <button type="button" name="send_otp_btn" id="send_otp_btn" tabindex="<?php echo $tab++; ?>"
                                class="btn btn-info" onClick="send_otp($('#survey_id').val(), '');">ओ.टी.पी.
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

                <div id="q-box__buttons">
                    <button id="prev-btn" class="btn btn-info" type="button" onClick="save_draft()">Previous</button>
                    <button id="next-btn" class="btn btn-success" type="button" onClick="save_draft()">Next</button>
                    <button id="submit-btn" class="btn btn-danger" type="submit" onClick="save_draft()">Submit</button>
                </div>
                <button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i>
                    Save
                    Draft</button>
                <input type="hidden" id="term" name="term" value="a">
                <input type="hidden" id="latitude" name="latitude" value="<?php echo $row_invoice['latitude']; ?>">
                <input type="hidden" id="longitude" name="longitude" value="<?php echo $row_invoice['longitude']; ?>">
                <input type="hidden" id="id" name="id" value="submit_form_ukvib">
                <input type="hidden" id="current_step_count" name="current_step_count" value="">
                <input type="hidden" id="survey_id" name="survey_id" value="<?php echo $row_invoice['sno']; ?>">
                </form>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="otp_form"
                name="otp_form"></form>
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
    
    function add_more_business() {
        var id = parseFloat($("#other_business_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 1; i <= id; i++) {
            if ($("#sec_2_1_2_business_description_" + i).val() == '' || $("#sec_2_1_2_value_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_2_1_2_business_description_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $(".add_business_row").hide();
        var txt = '<div class="row" id="business_row_' + id + '">' +
            '<div class="col-sm-4 form-group">' +
            '<label>व्यवसाय का विवरण </label>' +
            '<select name="sec_2_1_2_business_description_' + id + '" id="sec_2_1_2_business_description_' + id + '" class="form-control">' +
            '<option value="">--select--</option>' +
            '<option value="cattle_feed">कैटल फीड</option>' +
            '<option value="any_other">अन्य</option>' +
            '</select>' +
            '</div>' +
            '<div class="col-sm-4 form-group">' +
            '<label>वार्षिक टर्नोवर</label>' +
            '<input type="text" name="sec_2_1_2_value_' + id + '" id="sec_2_1_2_value_' + id + '" class="form-control chk_decimal" data-type=" 7.3.I वार्षिक टर्नोवर को धनराशि रु० लाख मे भरे">' +
            '</div>' +
            '<div class="col-sm-2 form-group my-auto add_business_row" id="add_business_row_' + id + '">' +
            '<button type="button" class="btn btn-info" onclick="add_more_business();">नईं पंक्ति जोड़े [+]</button>' +
            '<input type="hidden" name="other_business_id" id="other_business_id" value="' + id + '">' +
            '</div>' +
            '</div>';
        $("#other_business").append(txt);
        $("#other_business_id").val(id);
        $("#add_business_row_" + id).show();
    }

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

        var txt = '<div class="row"><div class="col-sm-2 form-group"><label>क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="sec_3_c_length_' + id + '" id="sec_3_c_length_' + id + '" class="form-control chk_number" data-type="क्षेत्रफल हेक्टेयर में मे लिखे"></div><div class="col-sm-2 form-group"><label>भूमि की स्थिति (उपजाऊ /बंजर)</label><select name="sec_3_c_vacant_land_status_' + id + '" id="sec_3_c_vacant_land_status_' + id + '" class="form-control"><option value="">--select-- </option><option value="fertile">उपजाऊ </option><option value="barren">बंजर </option></select></div><div class="col-sm-2 form-group"><label>स्थान (समिति प्रांगण या अन्य स्थान)</label><select name="sec_3_c_land_location_' + id + '" id="sec_3_c_land_location_' + id + '" class="form-control"><option value="">--select-- </option><option value="inpremise">समिति प्रांगण </option><option value="other">अन्य स्थान </option></select></div><div class="col-sm-2 form-group"><label>गोदाम के लिए उपयुक्त है या नहीं ?</label><select class="form-control" type="checkbox" value="yes" id="sec_2_accountant" name="sec_3_c_suitable_godown_' + id + '" id="sec_3_c_suitable_godown_' + id + '"><option value="">--Select--</option><option value="yes">है</option><option value="no" style="background:#f00">नहीं</option></select></div><div class="col-sm-2 form-group"><label>जनपद से रैक पाइण्ट की दूरी</label><input type="text" name="sec_3_c_rak_distance_' + id + '" id="sec_3_c_rak_distance_' + id + '" class="form-control"></div><div class="col-sm-2 form-group" id="land_access_road_<?php echo $i; ?>"><label>पहुच मार्ग का प्रकार</label><select name="sec_3_c_paved_road_' + id + '" id="sec_3_c_paved_road_' + id + '" class="form-control"><option value="">--select--</option><option value="ordinary">कच्ची सड़क</option><option value="nh">नेशनल हाईवे</option><option value="sh">स्टेट हाईवे</option><option value="mdr">एम.डी.आर.</option><option value="odr">ओ.डी.आर.</option><option value="rural_road">ग्रामीण सड़क</option><option value="other">अन्य</option></select></div><div class="col-sm-2 form-group my-auto" id="sec_3_c_rows"><button type="button" class="btn btn-info" onClick="sec_3_c_add_rows();">नई पंक्ति जोड़े</button><input type="hidden" name="sec_3_c_id" id="sec_3_c_id" value="' + id + '"></div></div>';

        $("#sec_3_c").append(txt);
    }

    $('select[multiple]').multiselect({
        columns: 1,
        placeholder: 'Select options'
    });

    function sec_6_2_add_rows() {
        var id = parseFloat($("#sec_6_2_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 1; i <= id; i++) {
            if ($("#sec_2_b_name_" + i).val() == '' || $("#sec_6_2_father_name_" + i).val() == '' || $("#sec_6_2__mob_no_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_2_b_name_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#sec_2_b_rows").remove();
        var txt = '<div class="row"><div class="col-sm-2 form-group"><label>पदनाम</label><select class="form-control" id="sec_6_2_designation_' + id + '" name="sec_6_2_designation_' + id + '"><option value="">--Select--</option><option value="अध्यक्ष">अध्यक्ष</option><option value="उपाध्यक्ष">उपाध्यक्ष</option><option value="संचालक">संचालक</option></select></div><div class="col-sm-2 form-group"><label>नाम</label><input type="text" name="sec_6_2_name_' + id + '" id="sec_6_2_name_' + id + '" class="form-control chk_text" data-type="नाम शब्दों में भरे"></div><div class="col-sm-2 form-group"><label>पिता / पति का नाम</label><input type="text" name="sec_6_2_father_name_' + id + '" id="sec_6_2_father_name_' + id + '" class="form-control chk_text" data-type="पिता का नाम शब्दों में भरे"></div><div class="col-sm-2 form-group"><label>मोबाईल नंबर</label><input type="text" name="sec_6_2__mob_no_' + id + '" id="sec_6_2__mob_no_' + id + '" class="form-control chk_mobile" data-minlength="10" data-maxlength="10" data-type="10 अंकों मे भरे"></div><div class="col-sm-2 form-group my-auto" id="sec_2_b_rows"><button type="button" class="btn btn-info" onclick="sec_6_2_add_rows();">नई पंक्ति जोड़े [+]</button></div></div>';
        $("#sec_2_b").append(txt);
        $("#sec_6_2_id").val(id);
    }

    function sec_2_nirmit_godown_add_rows() {
        var id = parseFloat($("#sec_2_nirmit_godown_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 1; i <= id; i++) {
            if ($("#sec_3_b_storage_capacity_" + i).val() == '' || $("#sec_3_b_godown_year_" + i).val() == '' || $("#sec_3_b_wdra_certified_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_3_b_storage_capacity_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#sec_2_nirmit_godown_rows").remove();
        var txt = '<div class="row">';
        txt += '<div class="col-sm-2 form-group"><label>क्षमता (मेट्रिक टन में)</label><input type="text" name="sec_3_b_storage_capacity_' + id + '" id="sec_3_b_storage_capacity_' + id + '" class="form-control chk_number" data-type="क्षमता मेट्रिक टन में भरे"></div>';
        txt += '<div class="col-sm-2 form-group"><label>निर्माण का वर्ष</label><select name="sec_3_b_godown_year_' + id + '" id="sec_3_b_godown_year_' + id + '" class="form-control"><option value="">--Select--</option><option value="1999">2000 से पूर्व</option>';
        for (var a = 2000; a <= 2024; a++) {
            txt += '<option value="' + a + '">' + a + '</option>';
        }
        txt += '</select></div>';

        txt += '<div class="col-sm-2 form-group"><label>WDRA से प्रमाणित है?</label><select name="sec_3_b_wdra_certified_' + id + '" id="sec_3_b_wdra_certified_' + id + '" class="form-control"><option value="">--select--</option><option value="yes">हाँ</option><option value="no">नहीं</option></select></div>';
        txt += '<div class="col-sm-2 form-group"><label>किस योजना के तहत बना है</label><input type="text" name="sec_3_b_godown_type_of_fund_' + id + '" id="sec_3_b_godown_type_of_fund_' + id + '" class="form-control chk_text" data-type="योजना का नाम शब्दों में भरे"></div>';
        txt += '<div class="col-sm-2 form-group"><label>गोदाम की भौतिक स्थिति</label><select name="sec_3_b_godown_status_' + id + '" id="sec_3_b_godown_status_' + id + '" class="form-control"><option value="">--select--</option><option value="good">अच्छा</option><option value="repairable">खराब/मरम्मत योग्य</option><option value="discarded">जर्जर/निष्प्रयोज्य्य</option></select></div>';
        txt += '<div class="col-sm-1 form-group"><label>टिप्पणी</label><input type="text" name="sec_3_b_godown_comment_' + id + '" id="sec_3_b_godown_comment_' + id + '" class="form-control"></div>';
        txt += '<div class="col-sm-1 form-group my-auto" id="sec_2_nirmit_godown_rows"><button type="button" class="btn btn-info" onClick="sec_2_nirmit_godown_add_rows()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_2_nirmit_godown_id" id="sec_2_nirmit_godown_id" value="' + id + '"></div>';
        txt += '</div>';
        $("#sec_2_nirmit_godown").append(txt);
    }
</script>


<script type="text/javascript" src="js/multistepform_uprnss.js?v=1">
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    < script src="js/light-bootstrap-dashboard.js?v=1.4.0">
</script>

<?php
page_footer_start();
?>