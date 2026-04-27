<?php
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    'prakhand_name' => '',
    'members_no' => '',
    'inactive_members_no' => '',
    'active_members_no' => '',
    'new_members' => '',
    'share_capital' => '',
    'inactive_to_active_no' => '',
    'total_members' => ''
];
// echo '-------------------------------------', $_GET['exdid'];
if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.`sno` as sno, apex_si_1_1.apex_id as `apex_id`, `longitude`, `latitude`, `committee_status`, `email_id`, concat("/user_data/", apex_si_1_1.apex_id, "/", photo_id) as photo_id, `society_registration_no`, `society_registration_date`, prakhand_name, `members_no`, `active_members_no`, `inactive_members_no`, `new_members`, `share_capital`, `inactive_to_active_no`, `total_members` FROM `apex_si_1_1` LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '" ';
    $result_invoice = execute_query($sql);
    if ($result_invoice && mysqli_num_rows($result_invoice) >= 1) {
        $row_invoice = mysqli_fetch_assoc($result_invoice);

        $_SESSION['survey_id'] = $row_invoice['sno'];
        $row_invoice['latitude'] = $row_invoice['latitude'];
        $row_invoice['longitude'] = $row_invoice['longitude'];
        $row_invoice['committee_status'] = $row_invoice['committee_status'];
        $row_invoice['email_id'] = $row_invoice['email_id'];
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
        $row_invoice['photo_id'] = $row_invoice['photo_id'];
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

    $sql_human = "SELECT * FROM apex_si_1_3 WHERE survey_id = '" . $row_invoice['sno'] . "'";
    $result_human = execute_query($sql_human);

    $human_rows = [];
    if ($result_human && mysqli_num_rows($result_human) > 0) {
        while ($row_h = mysqli_fetch_assoc($result_human)) {
            $human_rows[] = [
                'post_id' => $row_h['post_id'],
                'sanctioned_post' => $row_h['sanctioned_post'],
                'vacant_post' => $row_h['vacant_post']
            ];
        }
    } else {
        $human_rows[] = [
            'post_id' => '',
            'sanctioned_post' => '',
            'vacant_post' => ''
        ];
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

    // Initialize row_3_3 to avoid undefined variable warnings in later loop
    $row_3_3 = array();
    $i = 1;
    $row_3_3['count'] = 1;
    $row_3_3['sec_3_b_length_' . $i] = '';
    $row_3_3['sec_3_b_width_' . $i] = '';
    $row_3_3['sec_3_b_type_of_construction_' . $i] = '';
    $row_3_3['sec_3_b_type_of_fund_' . $i] = '';
    $row_3_3['sec_3_b_comment_' . $i] = '';

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
    $row_3_5 = array(); // Initialize to avoid undefined variable warning
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
            $row_3_5['sec_3_status_of_warehouse_' . $i] = ''; // Initialize undefined key
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
        $row_3_5['sec_3_status_of_warehouse_1'] = ""; // Initialize undefined key
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

    $sql = 'SELECT * FROM survey_invoice_other_land WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_other = execute_query($sql);
    if (mysqli_num_rows($res_other) != 0) {
        $i = 1;
        while ($rtemp = mysqli_fetch_assoc($res_other)) {
            $row_other_land['other_land_district_' . $i] = $rtemp['district'];
            $row_other_land['other_land_tehsil_' . $i] = $rtemp['tehsil'];
            $row_other_land['other_land_area_type_' . $i] = $rtemp['area_type'];
            $row_other_land['other_land_land_area_' . $i] = $rtemp['land_area'];
            $row_other_land['other_land_ownership_' . $i] = $rtemp['ownership'];
            $row_other_land['other_land_other_owner_' . $i] = $rtemp['other_owner'];
            $row_other_land['other_land_land_status_' . $i] = $rtemp['land_status'];
            $row_other_land['other_land_construction_' . $i] = $rtemp['construction'];
            $row_other_land['other_land_other_construct_' . $i] = $rtemp['other_construction'];
            $row_other_land['other_land_address_' . $i] = $rtemp['address'];
            $row_other_land['other_land_latitude_' . $i] = $rtemp['latitude'];
            $row_other_land['other_land_longitude_' . $i] = $rtemp['longitude'];
            $row_other_land['other_land_location_mode_' . $i] = $rtemp['location_mode'];
            $i++;
        }
        $row_other_land['count'] = $i - 1;
    } else {
        $i = 1;
        $row_other_land['count'] = 1;
        $row_other_land['other_land_district_1'] = '';
        $row_other_land['other_land_tehsil_1'] = '';
        $row_other_land['other_land_area_type_1'] = '';
        $row_other_land['other_land_land_area_1'] = '';
        $row_other_land['other_land_ownership_1'] = '';
        $row_other_land['other_land_other_owner_1'] = '';
        $row_other_land['other_land_land_status_1'] = '';
        $row_other_land['other_land_construction_1'] = '';
        $row_other_land['other_land_other_construct_1'] = '';
        $row_other_land['other_land_address_1'] = '';
        $row_other_land['other_land_latitude_1'] = '';
        $row_other_land['other_land_longitude_1'] = '';
        $row_other_land['other_land_location_mode_1'] = '';
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
$district_options = '<option value="">--Select--</option>';
if ($_SESSION['user_type'] == 'ar') {
    $sql = 'SELECT * FROM master_district WHERE sno IN (' . implode(",", $_SESSION['district_id']) . ')';
} else {
    $sql = 'SELECT * FROM master_district';
}
$res_d = execute_query($sql);
while ($d = mysqli_fetch_assoc($res_d)) {
    $district_options .= '<option value="' . $d['sno'] . '">' . $d['district_name'] . '</option>';
}

// Make it available for JS
$district_options_js = addslashes($district_options);
?>


<?php
page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="js/survey_validate.js?v=1.4.0"></script>


<style>
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

    .select-default {
        background-color: white;
    }

    .card label {
        font-size: 0.80rem;
    }
</style>

<?php
page_header_end();
page_sidebar();
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

                        <form action="scripts/ajax_uprnss.php" method="post" enctype="multipart/form-data"
                            id="user_form" name="user_form">
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
                                                        <label>समिति का नाम</label>
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
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>Latitude</label>
                                                        <input type="text" id="lat" disabled="disabled"
                                                            value="<?php echo $row_invoice['latitude']; ?>"
                                                            class="form-control">
                                                        <label>Longitude</label>
                                                        <input type="text" id="long" disabled="disabled"
                                                            value="<?php echo $row_invoice['longitude']; ?>"
                                                            class="form-control">
                                                        <button type="button" class="btn btn-info"
                                                            onClick="getLocation(); this.innerHTML='Refreshing...'; setTimeout(()=>this.innerHTML='लोकेशन रिफ्रेश करें',1500);">
                                                            लोकेशन रिफ्रेश करें</button>
                                                        <!-- Place the text here right after the button -->
                                                        <div class="blinking-text">(लोकेशन मोबाईल से भरे)*</div>
                                                    </div>
                                                    <div class="col-md-10" id="map_container">
                                                        <iframe id="googlemap"
                                                            src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                            width="100%" height="100%"
                                                            style="border:1px solid; border-radius:10px;"
                                                            allowfullscreen="" loading="lazy"
                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr/>

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
                                            
                                            <div class="col-sm-2 form-group">
                                                <label>समिति की फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="society_photo" id="society_photo"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">

                                            </div>
                                            <?php
                                            if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) {
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

                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                <label>पंजीकरण संख्या</label>
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_no']); ?>">
                                            </div>

                                            <div class="col-sm-2 form-group">
                                                <label>समिति पंजीकरण दिनांक</label>
                                                <label><small>नहीं पता होने की स्थिति में आज का ही दिनांक
                                                        दर्शायें</small></label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_date']); ?>">
                                            </div>
                                        </div>

                                        <br>
                                        <h5><img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> शीर्ष समिति के कार्यालय</h5>
                                        <br>
                                        <div class="col-md-6">
                                            <label>प्रखण्ड</label>
                                            <select name="prakhand_name" id="prakhand_name"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                style="width: 40%; display: inline-block;">
                                                <option value="">--Select--</option>
                                                <?php
                                                $sql_prakhand = "SELECT id, prakhand_name FROM apex_1_prakhand ORDER BY prakhand_name ASC";
                                                $result_prakhand = execute_query($sql_prakhand);
                                                $selected_prakhand_id = '';
                                                if (!empty($_POST['prakhand_name'])) {
                                                    $selected_prakhand_id = $_POST['prakhand_name'];
                                                } elseif (!empty($row_invoice['prakhand_name'])) {
                                                    $selected_prakhand_id = $row_invoice['prakhand_name'];
                                                }
                                                if ($result_prakhand && mysqli_num_rows($result_prakhand) > 0) {
                                                    while ($row_prakhand = mysqli_fetch_assoc($result_prakhand)) {
                                                        echo '<option value="' . htmlspecialchars($row_prakhand['id']) . '"';
                                                        if ($selected_prakhand_id == $row_prakhand['id']) {
                                                            echo ' selected="selected"';
                                                        }
                                                        echo '>' . htmlspecialchars($row_prakhand['prakhand_name']) . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <br>
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
                                                    <label>सदस्य समितियों की संख्या</label>
                                                    <input type="text" name="members_no" id="members_no"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['members_no']); ?>">
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    <label>सक्रिय समितियों / सदस्यों की संख्या</label>
                                                    <input type="text" name="active_members_no" id="active_members_no"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo ($row_invoice['active_members_no']); ?>">
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    <label>निष्क्रिय समितियों / सदस्यों की संख्या</label>
                                                    <input type="text" name="inactive_members_no"
                                                        id="inactive_members_no" tabindex="<?php echo $tab++; ?>"
                                                        class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['inactive_members_no']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <small><b>(III) बनाए गए नए सदस्यों की संख्या :</b></small>
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
                                                <div class="col-sm-4 form-group">
                                                    <label>01-अप्रैल-2024 से निष्क्रिय से सक्रिय किए गए सदस्यों की
                                                        संख्या</label>
                                                    <input type="text" name="inactive_to_active_no"
                                                        id="inactive_to_active_no" tabindex="<?php echo $tab++; ?>"
                                                        class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['inactive_to_active_no']); ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <small><b>(IV) कुल सदस्य :</b></small>
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
                                <!----------------2.1 start-------------------------------------------------------->
                                <div class="step">
                                    <h4><img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 2. समिति की प्रबंध कमेटी</h4>
                                    <div class="col-sm-12">
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
                                            $row_6_2['count'] = 1;
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
                                                            <option value="उपाध्यक्ष" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'उपाध्यक्ष' ? 'selected="selected"' : ''; ?>>उपाध्यक्ष</option>
                                                            <option value="संचालक" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'संचालक' ? 'selected="selected"' : ''; ?>>संचालक</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2 form-group">
                                                        <label>नाम</label>
                                                        <input type="text" name="sec_6_2_name_<?php echo $i; ?>"
                                                            id="sec_6_2_name_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control chk_text"
                                                            data-type="4.II नाम शब्दों में भरे"
                                                            value="<?php echo $row_6_2['sec_6_2_name_' . $i]; ?>">
                                                    </div>
                                                    <!-- <div class="col-sm-2 form-group">
                                                        <label>पिता / पति का नाम</label>
                                                        <input type="text" name="sec_6_2_father_name_<?php echo $i; ?>"
                                                            id="sec_6_2_father_name_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control chk_text"
                                                            data-type="4.II पिता का नाम शब्दों में भरे"
                                                            value="<?php echo $row_6_2['sec_6_2_father_name_' . $i]; ?>">
                                                    </div> -->
                                                    <div class="col-sm-2 form-group">
                                                        <label>मोबाईल नंबर</label>
                                                        <input type="text" name="sec_6_2__mob_no_<?php echo $i; ?>"
                                                            id="sec_6_2__mob_no_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control chk_mobile"
                                                            data-minlength="10" data-maxlength="10"
                                                            data-type="4.II 10 अंकों मे भरे"
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
                                        <input type="hidden" name="sec_6_2_id" id="sec_6_2_id"
                                            value="<?php echo $row_6_2['count']; ?>">
                                    </div>
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
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label> अंतिम ए० जी० एम० किस वित्तीय वर्ष तक सम्पन्न हुई</label>
                                                <select name="sec_3_agm_year" class="form-control"
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
                                                    $session_start = date('Y') - 7;
                                                    for ($i = $session_start; $i <= $session_start + 7; $i++) {
                                                        $end_session = $i + 1;
                                                        ?>
                                                        <option value="<?php echo $i . '-' . $end_session; ?>" <?php
                                                               if (isset($row_3_new_1['sec_3_agm_year']) && $row_3_new_1['sec_3_agm_year'] == $i . '-' . $end_session) {
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
                                                <label>लाभांश किस वर्ष का दिया गया</label>
                                                <select name="sec_3_dividend_year" id="sec_3_dividend_year"
                                                    class="form-control" tabindex="<?php echo $tab++; ?>"
                                                    onchange="hide_show(this.value, '#sec_2_dividend_per', ['2018', '2019', '2020', '2021', '2022','2023', '2024']); hide_show(this.value, '#sec_2_dividend', ['2018', '2019', '2020', '2021', '2022','2023', '2024']);">
                                                    <option value="">--Select--</option>
                                                    <option value="2018" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2018' ? 'selected="selected"' : '' ?>>2017-2018</option>
                                                    <option value="2019" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2019' ? 'selected="selected"' : '' ?>>2018-2019</option>
                                                    <option value="2020" <?php echo isset($row_3_new_1['sec_3_dividend_yearsec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2020' ? 'selected="selected"' : '' ?>>2019-2020</option>
                                                    <option value="2021" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2021' ? 'selected="selected"' : '' ?>>2020-2021</option>
                                                    <option value="2022" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2022' ? 'selected="selected"' : '' ?>>2021-2022</option>
                                                    <option value="2023" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2023' ? 'selected="selected"' : '' ?>>2022-2023</option>
                                                    <option value="2024" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2024' ? 'selected="selected"' : '' ?>>2023-2024</option>
                                                    <option value="no" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == 'no' ? 'selected="selected"' : '' ?>>नहीं दिया गया</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2 form-group" id="sec_2_dividend_per"
                                                style="display:none">
                                                <label>लाभांश का प्रतिशत (0-20 तक)</label>
                                                <input type="text" name="sec_3_dividend_per" id="sec_3_dividend_per"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                                    data-type="7.III लाभांश को प्रतिशत मे भरे"
                                                    value="<?php echo $row_3_new_1['sec_3_dividend_per']; ?>">
                                            </div>
                                            <div class="col-sm-2 form-group" id="sec_2_dividend" style="display:none">
                                                <label>लाभांश की धनराशि (लाख मे)</label>
                                                <input type="text" name="sec_3_dividend_amt" id="sec_3_dividend_amt"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                                    data-type="7.III लाभांश को धनराशि रु० लाख मे भरे"
                                                    value="<?php echo $row_3_new_1['sec_3_dividend_amt']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <h4><img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 4. अन्य कार्य व व्यवसाय</h4>
                                    <div class="col-sm-12">
                                        <?php
                                        $count = !empty($row_2_1_2['count']) ? (int) $row_2_1_2['count'] : 1;
                                        for ($i = 1; $i <= $count; $i++) {
                                            ?>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>व्यवसाय का विवरण </label>
                                                    <input type="text"
                                                        name="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                        id="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo $row_2_1_2['sec_2_1_2_business_description_' . $i]; ?>">
                                                </div>

                                                <div class="col-sm-3 form-group">
                                                    <label>वार्षिक टर्नोवर</label>
                                                    <input type="text" name="sec_2_1_2_value_<?php echo $i; ?>"
                                                        id="sec_2_1_2_value_<?php echo $i; ?>"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo $row_2_1_2['sec_2_1_2_value_' . $i]; ?>">
                                                </div>
                                                <?php
                                                if ($i == $row_2_1_2['count']) {
                                                    ?>
                                                    <div class="col-sm-2 form-group my-auto" id="add_business_row">
                                                        <button type="button" class="btn btn-info"
                                                            onClick="add_more_business();">नईं पंक्ति
                                                            जोड़े [+]</button>
                                                        <input type="hidden" name="other_business_id" id="other_business_id"
                                                            value="<?php echo $row_2_1_2['count']; ?>">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!----------------5th start-------------------------------------------------------->
                                <div class="step">
                                    <h4>
                                        <img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        5. मानव सम्पदा
                                    </h4>
                                    <?php
                                    $posts = [];
                                    $sql_posts = "SELECT `sno`, `post_name` FROM `master_posts_apex_1` ORDER BY `post_name` ASC";
                                    $result_posts = execute_query($sql_posts);
                                    if ($result_posts && mysqli_num_rows($result_posts) > 0) {
                                        while ($r = mysqli_fetch_assoc($result_posts)) {
                                            $posts[] = $r;
                                        }
                                    }
                                    ?>
                                    <div id="human_resource_rows">
                                        <?php
                                        $rowIndex = 1;
                                        if (!empty($human_rows)) {
                                            foreach ($human_rows as $h) {
                                                ?>
                                                <div class="row human_row mb-2" id="human_row_<?php echo $rowIndex; ?>">
                                                    <div class="col-md-3 form-group">
                                                        <label>पद</label>
                                                        <select name="post_id[]" id="post_id_<?php echo $rowIndex; ?>"
                                                            class="form-control">
                                                            <option value="">--Select--</option>
                                                            <?php
                                                            if (!empty($posts) && is_array($posts)) {
                                                                foreach ($posts as $p) {
                                                                    $id = htmlspecialchars((string) $p['sno']);
                                                                    $name = htmlspecialchars($p['post_name']);
                                                                    $selected = ($h['post_id'] == $id) ? 'selected' : '';
                                                                    echo "<option value=\"{$id}\" {$selected}>{$name}</option>\n";
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3 form-group">
                                                        <label>स्वीकृत पद</label>
                                                        <input type="number" name="sanctioned_post[]"
                                                            id="sanctioned_post_<?php echo $rowIndex; ?>" class="form-control"
                                                            value="<?php echo htmlspecialchars($h['sanctioned_post']); ?>">
                                                    </div>

                                                    <div class="col-md-3 form-group">
                                                        <label>रिक्त पद</label>
                                                        <input type="number" name="vacant_post[]"
                                                            id="vacant_post_<?php echo $rowIndex; ?>" class="form-control"
                                                            value="<?php echo htmlspecialchars($h['vacant_post']); ?>">
                                                    </div>

                                                    <div class="col-md-2 form-group my-auto add_human_row"
                                                        id="add_human_row_<?php echo $rowIndex; ?>">
                                                        <?php if ($rowIndex == 1) { ?>
                                                            <button type="button" class="btn btn-info" onclick="addHumanRow()">नई
                                                                पंक्ति जोड़े [+]</button>
                                                            <input type="hidden" name="human_resource_id" id="human_resource_id"
                                                                value="<?php echo count($human_rows); ?>">
                                                        <?php } else { ?>
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="$(this).closest('.human_row').remove()">हटाएं [-]</button>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $rowIndex++;
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <!--------------------------------------------------------------->

                                <div class="step">
                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 6. समिति भवन/सम्पत्ति का विवरण</h2>
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
                                                        <div class="col-sm-2 form-group">
                                                            <label>समिति भूखण्ड फोटो संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="society_photo_1" id="society_photo_1"
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
                                                   <div id="sec_3_b">
                                                        <?php
                                                        $count_3b = !empty($row_3_3['count']) ? (int)$row_3_3['count'] : 1;
                                                        for ($i = 1; $i <= $count_3b; $i++) {
                                                            ?>
                                                            <div class="row">
                                                                <div class="col-sm-2 form-group">
                                                                    <label>लंबाई (मीटर में)</label>
                                                                    <input type="text"
                                                                        name="sec_3_b_length_<?php echo $i; ?>"
                                                                        id="sec_3_b_length_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_3_3['sec_3_b_length_' . $i]; ?>">
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>चौड़ाई (मीटर में)</label>
                                                                    <input type="text"
                                                                        name="sec_3_b_width_<?php echo $i; ?>"
                                                                        id="sec_3_b_width_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_3_3['sec_3_b_width_' . $i]; ?>">
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>भवन का प्रकार</label>
                                                                    <select
                                                                        name="sec_3_b_type_of_construction_<?php echo $i; ?>"
                                                                        id="sec_3_b_type_of_construction_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control">
                                                                        <option value="">--Select--</option>
                                                                        <?php
                                                                        $sql = 'select * from master_type_of_construction';
                                                                        $result_const = execute_query($sql);
                                                                        while ($row_const = mysqli_fetch_assoc($result_const)) {
                                                                            echo '<option value="' . $row_const['sno'] . '" ';
                                                                            if ($row_const['sno'] == $row_3_3['sec_3_b_type_of_construction_' . $i]) {
                                                                                echo ' selected="selected" ';
                                                                            }
                                                                            echo '>' . $row_const['type_of_construction'] . '</option>';
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>किस फण्ड से बना है</label>
                                                                    <select name="sec_3_b_type_of_fund_<?php echo $i; ?>"
                                                                        id="sec_3_b_type_of_fund_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control">
                                                                        <option value="">--Select--</option>
                                                                        <?php
                                                                        $sql = 'select * from master_type_of_fund';
                                                                        $result_const = execute_query($sql);
                                                                        while ($row_const = mysqli_fetch_assoc($result_const)) {
                                                                            echo '<option value="' . $row_const['sno'] . '" ';
                                                                            if ($row_const['sno'] == $row_3_3['sec_3_b_type_of_fund_' . $i]) {
                                                                                echo ' selected="selected" ';
                                                                            }
                                                                            echo '>' . $row_const['type_of_fund'] . '</option>';
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>टिप्पणी</label>
                                                                    <input type="text"
                                                                        name="sec_3_b_comment_<?php echo $i; ?>"
                                                                        id="sec_3_b_comment_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_3_3['sec_3_b_comment_' . $i]; ?>">
                                                                </div>
                                                                <?php
                                                                if ($i == $row_3_3['count']) {
                                                                    ?>

                                                                    <div class="col-sm-2 form-group my-auto" id="sec_3_b_rows">
                                                                        <button type="button" class="btn btn-info"
                                                                            onClick="sec_3_b_add_rows()">नई पंक्ति जोड़े
                                                                            [+]</button>
                                                                        <input type="hidden" name="sec_3_b_id" id="sec_3_b_id"
                                                                            value="<?php echo $row_3_3['count']; ?>">
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                        <!-- </div>v id="sec_3_b"> -->
                                                        <?php
                                                        $count_3b = !empty($row_3_3['count']) ? (int)$row_3_3['count'] : 1;
                                                        for ($i = 1; $i <= $count_3b; $i++) {
                                                            ?>
                                                            <div class="row">
                                                                <div class="col-sm-2 form-group">
                                                                    <label>लंबाई (मीटर में)</label>
                                                                    <input type="text"
                                                                        name="sec_3_b_length_<?php echo $i; ?>"
                                                                        id="sec_3_b_length_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_3_3['sec_3_b_length_' . $i]; ?>">
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>चौड़ाई (मीटर में)</label>
                                                                    <input type="text"
                                                                        name="sec_3_b_width_<?php echo $i; ?>"
                                                                        id="sec_3_b_width_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_3_3['sec_3_b_width_' . $i]; ?>">
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>भवन का प्रकार</label>
                                                                    <select
                                                                        name="sec_3_b_type_of_construction_<?php echo $i; ?>"
                                                                        id="sec_3_b_type_of_construction_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control">
                                                                        <option value="">--Select--</option>
                                                                        <?php
                                                                        $sql = 'select * from master_type_of_construction';
                                                                        $result_const = execute_query($sql);
                                                                        while ($row_const = mysqli_fetch_assoc($result_const)) {
                                                                            echo '<option value="' . $row_const['sno'] . '" ';
                                                                            if ($row_const['sno'] == $row_3_3['sec_3_b_type_of_construction_' . $i]) {
                                                                                echo ' selected="selected" ';
                                                                            }
                                                                            echo '>' . $row_const['type_of_construction'] . '</option>';
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>किस फण्ड से बना है</label>
                                                                    <select name="sec_3_b_type_of_fund_<?php echo $i; ?>"
                                                                        id="sec_3_b_type_of_fund_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control">
                                                                        <option value="">--Select--</option>
                                                                        <?php
                                                                        $sql = 'select * from master_type_of_fund';
                                                                        $result_const = execute_query($sql);
                                                                        while ($row_const = mysqli_fetch_assoc($result_const)) {
                                                                            echo '<option value="' . $row_const['sno'] . '" ';
                                                                            if ($row_const['sno'] == $row_3_3['sec_3_b_type_of_fund_' . $i]) {
                                                                                echo ' selected="selected" ';
                                                                            }
                                                                            echo '>' . $row_const['type_of_fund'] . '</option>';
                                                                        }

                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>टिप्पणी</label>
                                                                    <input type="text"
                                                                        name="sec_3_b_comment_<?php echo $i; ?>"
                                                                        id="sec_3_b_comment_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_3_3['sec_3_b_comment_' . $i]; ?>">
                                                                </div>
                                                                <?php
                                                                if ($i == $row_3_3['count']) {
                                                                    ?>

                                                                    <div class="col-sm-2 form-group my-auto" id="sec_3_b_rows">
                                                                        <button type="button" class="btn btn-info"
                                                                            onClick="sec_3_b_add_rows()">नई पंक्ति जोड़े
                                                                            [+]</button>
                                                                        <input type="hidden" name="sec_3_b_id" id="sec_3_b_id"
                                                                            value="<?php echo $row_3_3['count']; ?>">
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
                                                             <div class="col-sm-2 form-group">
                                                                <label>गोदाम के निर्माण की स्थिति</label>
                                                                <input type="text"
                                                                    name="sec_3_status_of_warehouse_<?php echo $i; ?>"
                                                                    id="sec_3_status_of_warehouse_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_5['sec_3_status_of_warehouse_' . $i]; ?>">
                                                            </div>
                                                            <!-- <div class="col-sm-2 form-group"
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
                                                            </div> -->

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
                                                    <!-- <h5>(VII)अन्य भूमि का विवरण</h5> -->
                                                    
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <!---------------7th Start---------------------------------------------------------------->
                                <div class="step">
                                    <h4><img src="images/logo/8.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 7. सुविधाएं </h4>
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
                    <!----------9th start-------------------------------------------------------->

                    <div id="success">
                        <div class="mt-5 text-center">
                            <h4>प्रपत्र सफलता पूर्वक भरा गया</h4>
                            <p>आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
                                सत्यापन हेतु भेजा जाना है । कृप्या सत्यापन पर भेजने से पहले नीचे
                                दर्शायें
                                लिंक से फार्म खोल कर पुनः जाच कर लें । सब कुछ सही होने कि दशा में नीचे
                                दिये
                                बटन के माध्यम से सत्यापन के लिये आगे प्रेषित करें ।</p>
                            <button class="btn btn-info"
                                onclick="window.open('preview.php?exdid=<?php echo $_GET['exdid']; ?>', '_blank');">प्रपत्र
                                पुनः निरीक्षण के लिये देखे</button>
                        </div>
                        <div class="col-md-12 text-center">
                            <p><input type="checkbox" style="height: 20px; border:1px solid;" id="review_ack"
                                    onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
                                मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी सूचनायें मेरी
                                जानकारी अनुसार सत्य एवम सही है । </p>
                            <button type="button" class="btn btn-danger" id="verification_button"
                                onClick="form_validate()" disabled="disabled">सत्यापन के लिये आगे प्रेषित करें
                            </button>
                        </div>

                        <div class="col-sm-12 form-group my-auto" id="send_otp_button2" style="display: none">
                            <button type="button" name="verify_otp_btn" id="verify_otp_btn"
                                tabindex="<?php echo $tab++; ?>" class="btn btn-info"
                                onClick="verify_otp($('#survey_id').val());">आगे प्रेषित करे
                            </button>
                        </div>
                    </div>
                </div>

                <div id="q-box__buttons">
                    <button id="prev-btn" class="btn btn-info" type="button" onClick="save_draft()">Previous</button>
                    <button id="next-btn" class="btn btn-success" type="button" onClick="save_draft()">Next</button>
                    <button id="submit-btn" class="btn btn-danger" type="submit"
                        onClick="validate_input(); save_draft();">Submit</button>
                </div>
                <button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i>
                    Save
                    Draft</button>
                <input type="hidden" id="term" name="term" value="a">
                <input type="hidden" id="latitude" name="latitude" value="<?php echo $row_invoice['latitude']; ?>">
                <input type="hidden" id="longitude" name="longitude" value="<?php echo $row_invoice['longitude']; ?>">
                <input type="hidden" id="id" name="id" value="submit_form_uprnss">
                <input type="text" id="current_step_count" name="current_step_count" value="">
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
                    } else if (value.id != 'Update' && value.id != 'update') {
                        $("#survey_id").val(value.id);
                        console.log(value.id);
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
        var txt = '<div class="row"><div class="col-sm-2 form-group"><label>पदनाम</label><select class="form-control" id="sec_6_2_designation_' + id + '" name="sec_6_2_designation_' + id + '"><option value="">--Select--</option><option value="अध्यक्ष">अध्यक्ष</option><option value="उपाध्यक्ष">उपाध्यक्ष</option><option value="संचालक">संचालक</option></select></div><div class="col-sm-2 form-group"><label>नाम</label><input type="text" name="sec_6_2_name_' + id + '" id="sec_6_2_name_' + id + '" class="form-control chk_text" data-type="नाम शब्दों में भरे"></div><div class="col-sm-2 form-group"><label>मोबाईल नंबर</label><input type="text" name="sec_6_2__mob_no_' + id + '" id="sec_6_2__mob_no_' + id + '" class="form-control chk_mobile" data-minlength="10" data-maxlength="10" data-type="10 अंकों मे भरे"></div><div class="col-sm-2 form-group my-auto" id="sec_2_b_rows"><button type="button" class="btn btn-info" onclick="sec_6_2_add_rows();">नई पंक्ति जोड़े [+]</button></div></div>';
        $("#sec_2_b").append(txt);
        $("#sec_6_2_id").val(id);
    }

    function addHumanRow() {
        let id = parseInt($("#human_resource_id").val());
        if (isNaN(id)) id = 0;

        // Validate existing rows before adding new one
        for (let i = 1; i <= id; i++) {
            let post = $("#post_id_" + i).val();
            let sanc = $("#sanctioned_post_" + i).val();
            let vac = $("#vacant_post_" + i).val();

            if (!post || !sanc || !vac) {
                alert("पंक्ति संख्या " + i + " खाली है। कृपया सभी फ़ील्ड भरें।");
                $("#post_id_" + i).focus();
                return;
            }
        }

        id += 1;
        $("#human_resource_id").val(id);

        // Generate HTML dynamically — no PHP echo inside JS string!
        let postOptions = $("#post_id_1").html(); // clone options from first dropdown

        let rowHTML = `
            <div class="row human_row mb-2" id="human_row_${id}">
                <div class="col-md-3 form-group">
                    <label>पद</label>
                    <select name="post_id[]" id="post_id_${id}" class="form-control">
                        ${postOptions}
                    </select>
                </div>

                <div class="col-md-3 form-group">
                    <label>स्वीकृत पद</label>
                    <input type="number" name="sanctioned_post[]" id="sanctioned_post_${id}" class="form-control">
                </div>

                <div class="col-md-3 form-group">
                    <label>रिक्त पद</label>
                    <input type="number" name="vacant_post[]" id="vacant_post_${id}" class="form-control">
                </div>

                <div class="col-md-2 form-group my-auto add_human_row" id="add_human_row_${id}">
                    <button type="button" class="btn btn-info" onclick="addHumanRow()">नई पंक्ति जोड़े [+]</button>
                </div>
            </div>
        `;

        $("#human_resource_rows").append(rowHTML);
    }
    function handleDropdownColorChange(selectElement, yesValue, yesColor, noValue, noColor) {
        if (selectElement.value === yesValue) {
            selectElement.style.backgroundColor = yesColor;
        } else if (selectElement.value === noValue) {
            selectElement.style.backgroundColor = noColor;
        } else {
            selectElement.style.backgroundColor = 'white'; // Default background color
        }
    }
</script>
<script>
    function hide_show(value, containerId, showValue) {
        var testServicesContainer = document.querySelector(containerId);
        if (!testServicesContainer) {
            console.warn('Element with ID "' + containerId + '" not found.');
            return; // Exit early if the element is not found
        }
        if (Array.isArray(showValue)) {
            if (showValue.includes(value)) {
                testServicesContainer.style.display = 'block';
            } else {
                testServicesContainer.style.display = 'none';
            }
        } else {
            if (value === showValue) {
                testServicesContainer.style.display = 'block';
            } else {
                testServicesContainer.style.display = 'none';
            }
        }
    }
</script>
<script language="javascript" type="text/javascript">
    function validate_input() {
        // var regexp_text = /^[\p{Letter}\u0900-\u097F ]+$/u;
        var regexp_text = /^[A-Za-z\u0900-\u097F,.\s]+$/;
        // var regexp_spltext = /^[\p{Letter}\u0900-\u097F -,./]+$/u;
        var regexp_spltext = /^[\p{Letter}\u0900-\u097F ,.\-!?]+$/u;
        var regexp_number = /^\d+$/;
        var regexp_decimal = /^-?\d+(\.\d+)?$/;
        // var regexp_email = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
        var regexp_email = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var error_status = 0;
        var error_msg = '';

        // Validate Text Inputs (e.g., names, descriptions)
        $(".chk_text").each(function () {
            var value_text = $(this).val();
            if (value_text != "") {
                if (!regexp_text.test(value_text)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + "\n"; // Error message for invalid input
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });

        // Validate Special Text Inputs (e.g., addresses, descriptions with punctuation)
        $(".chk_spltext").each(function () {
            var value_text = $(this).val();
            if (value_text != "") {
                if (!regexp_spltext.test(value_text)) {
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

        $(".chk_special_text").each(function () {
            var value_text = $(this).val();
            // Regex allows letters (Latin and Devanagari), comma, and full stop only
            var regexp_spltext = /^[A-Za-z\u0900-\u097F,.\s]+$/; // Allows letters, comma, full stop, and space

            if (value_text != "") {
                if (!regexp_spltext.test(value_text)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + " (अवैध वर्ण)\n"; // Error message for invalid characters
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });



        // Validate Number Inputs (with max 5 digits)
        $(".chk_number").each(function () {
            var value_number = $(this).val();
            var minlength = $(this).data("minlength"); // Minimum length if defined
            var maxlength = $(this).data("maxlength"); // Maximum length (set as data-maxlength="5")

            // Ensure the value is not empty and validate length
            if (value_number != "") {
                // Check for minimum length
                if (value_number.length < minlength) {
                    error_msg += $(this).data("type") + ". न्यूनतम " + minlength + " अंक भरें। \n";
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_status = 1;
                }
                // Check if the value exceeds the maximum limit (e.g., 5 digits max)
                else if (value_number.length > maxlength) {
                    error_msg += $(this).data("type") + " 5 अंकों से अधिक नहीं हो सकता। \n"; // Max 5 digits
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_status = 1;
                }
                // Check if the value is numeric (only digits allowed)
                else if (!regexp_number.test(value_number)) {
                    error_msg += $(this).data("type") + " केवल अंक भरें। \n"; // Only numbers allowed
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

        $(".chk_mobile").each(function () {
            $(this).on("blur", function () { // Trigger on blur or keyup, based on your preference
                var value_mobile = $(this).val().trim(); // Strip extra spaces from the input

                // Define the minimum and maximum length for mobile number (10 digits)
                var minlength = 10;
                var maxlength = 10;

                // Ensure the value is not empty and validate length
                if (value_mobile != "") {
                    // Check if the value is not exactly 10 digits
                    if (value_mobile.length < minlength || value_mobile.length > maxlength) {
                        error_msg += $(this).data("type") + " केवल 10 अंक भरें। \n"; // Should be exactly 10 digits
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_status = 1;
                    }
                    // Check if the value is numeric (only digits allowed)
                    else if (!/^\d{10}$/.test(value_mobile)) {
                        error_msg += $(this).data("type") + " केवल अंक भरें। \n"; // Only numbers allowed
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
        });

        // Validate Decimal Inputs (for numbers with decimals, negative numbers allowed)
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

        // Validate Email Inputs
        $(".chk_email").each(function () {
            var value_email = $(this).val();
            if (value_email != "") {
                if (!regexp_email.test(value_email)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + ". \n";
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });

        // Final check: Set the error status
        $("#error_status").val(error_status);

        // Show error messages if there were validation issues
        if (error_msg != "") {
            alert(error_msg);
        }

        return error_status === 0; // Return false to prevent form submission if there are errors
    }
</script>

<script>
    function sec_3_b_add_rows() {
                    var id = parseFloat($("#sec_3_b_id").val());
                    if (!id) {
                        id = 0;
                    }
                    for (var i = 0; i <= id; i++) {
                        if ($("#sec_3_b_length_" + i).val() == '' || $("#sec_3_b_width_" + i).val() == '') {
                            alert("पंक्ति संख्या " + i + " खाली है");
                            $("#sec_3_b_length_" + i).focus();
                            return;
                        }
                    }
                    id = id + 1;
                    var const_options = $("#sec_3_b_type_of_construction_1").html();
                    var fund_options = $("#sec_3_b_type_of_fund_1").html();
                    $("#sec_3_b_rows").remove();

                    var txt = '<div class="row" id="sec_3_b"><div class="col-sm-2 form-group"><label>लंबाई (मीटर में)</label><input type="text" name="sec_3_b_length_' + id + '" id="sec_3_b_length_' + id + '" class="form-control"></div><div class="col-sm-2 form-group"><label>चौड़ाई (मीटर में)</label><input type="text" name="sec_3_b_width_' + id + '" id="sec_3_b_width_' + id + '" class="form-control"></div><div class="col-sm-2 form-group"><label>भवन का प्रकार</label><select name="sec_3_b_type_of_construction_' + id + '" id="sec_3_b_type_of_construction_' + id + '" class="form-control">' + const_options + '</select></div><div class="col-sm-2 form-group"><label>किस फण्ड से बना है</label><select name="sec_3_b_type_of_fund_' + id + '" id="sec_3_b_type_of_fund_' + id + '" class="form-control">' + fund_options + '</select></div><div class="col-sm-2 form-group"><label>टिप्पणी</label><input type="text" name="sec_3_b_comment_' + id + '" id="sec_3_b_comment_' + id + '" class="form-control"></div><div class="col-sm-2 form-group my-auto" id="sec_3_b_rows"><button type="button" class="btn btn-info" onClick="sec_3_b_add_rows()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_3_b_id" id="sec_3_b_id" value="' + id + '"></div></div>';
                    $("#sec_3_b").append(txt);
                }
    // function sec_2_nirmit_godown_add_rows() {
    //     var id = parseFloat($("#sec_2_nirmit_godown_id").val());
    //     if (!id) {
    //         id = 0;
    //     }
    //     for (var i = 1; i <= id; i++) {
    //         if ($("#sec_3_b_storage_capacity_" + i).val() == '' || $("#sec_3_b_godown_year_" + i).val() == '' || $("#sec_3_b_wdra_certified_" + i).val() == '') {
    //             alert("पंक्ति संख्या " + i + " खाली है");
    //             $("#sec_3_b_storage_capacity_" + i).focus();
    //             return;
    //         }
    //     }
    //     id = id + 1;
    //     $("#sec_2_nirmit_godown_rows").remove();
    //     var txt = '<div class="row">';
    //     txt += '<div class="col-sm-2 form-group"><label>लम्बाई (मीटर में )</label><input type="text" name="sec_3_b_storage_capacity_' + id + '" id="sec_3_b_storage_capacity_' + id + '" class="form-control chk_number" data-type="लम्बाई (मीटर में )"></div>';
    //     txt += '<div class="col-sm-2 form-group"><label>चौड़ाई (मीटर में )</label><select name="sec_3_b_godown_year_' + id + '" id="sec_3_b_godown_year_' + id + '" class="form-control"><option value="">--Select--</option><option value="1999">2000 से पूर्व</option>';
    //     for (var a = 2000; a <= 2024; a++) {
    //         txt += '<option value="' + a + '">' + a + '</option>';
    //     }
    //     txt += '</select></div>';

    //     txt += '<div class="col-sm-2 form-group"><label>भवन का प्रकार </label><select name="sec_3_b_wdra_certified_' + id + '" id="sec_3_b_wdra_certified_' + id + '" class="form-control"><input type="text" name="sec_3_b_wdra_certified_' + id + '" id="sec_3_b_type_of_Building_' + id + '" class="form-control chk_text" data-type="भवन का प्रकार भरे"></select></div>';
    //     txt += '<div class="col-sm-2 form-group"><label>किस फंड से बना हैं</label><input type="text" name="sec_3_b_godown_type_of_fund_' + id + '" id="sec_3_b_godown_type_of_fund_' + id + '" class="form-control chk_text" data-type="योजना का नाम शब्दों में भरे"></div>';
    //     // txt += '<div class="col-sm-2 form-group"><label>टिप्पणी</label><select name="sec_3_b_godown_status_' + id + '" id="sec_3_b_godown_status_' + id + '" class="form-control"><option value="">--select--</option><option value="good">अच्छा</option><option value="repairable">खराब/मरम्मत योग्य</option><option value="discarded">जर्जर/निष्प्रयोज्य्य</option></select></div>';
    //     txt += '<div class="col-sm-1 form-group"><label>टिप्पणी</label><input type="text" name="sec_3_b_godown_comment_' + id + '" id="sec_3_b_godown_comment_' + id + '" class="form-control"></div>';
    //     txt += '<div class="col-sm-1 form-group my-auto" id="sec_2_nirmit_godown_rows"><button type="button" class="btn btn-info" onClick="sec_2_nirmit_godown_add_rows()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_2_nirmit_godown_id" id="sec_2_nirmit_godown_id" value="' + id + '"></div>';
    //     txt += '</div>';
    //     $("#sec_2_nirmit_godown").append(txt);
    // }
</script>

<script>
    function other_land_add_row() {
        var id = parseInt($("#other_land_count").val());
        if (!id) id = 0;

        // validate current rows
        for (var i = 1; i <= id; i++) {
            var district = $("#other_land_district_" + i).val();
            var tehsil = $("#other_land_tehsil_" + i).val();
            if (district == '' || tehsil == '') {
                alert("पंक्ति संख्या " + i + " में जिला/तहसील खाली है");
                $("#other_land_district_" + i).focus();
                return;
            }
        }

        id = id + 1;
        $("#other_land_add_row_btn_area").remove();

        var txt = '';
        txt += '<div class="other-land-row" id="other_land_row_' + id + '" style="border:1px solid #ccc; padding:10px; margin-bottom:12px;">';
        txt += '<div class="col-sm-3 form-group"><label>1. जिला</label>';
        txt += '<select name="other_land_district_' + id + '" id="other_land_district_' + id + '" class="form-control" onchange="fill_other_tehsil(this.value,' + id + ')">';
        txt += '<?php echo $district_options_js; ?>'; // PHP district options for JS
        txt += '</select></div>';

        txt += '<div class="col-sm-3 form-group"><label>2. तहसील</label>';
        txt += '<select name="other_land_tehsil_' + id + '" id="other_land_tehsil_' + id + '" class="form-control">';
        txt += '<option value="">--Select--</option>';
        txt += '</select></div>';

        txt += '<div class="col-sm-3 form-group"><label>3. शहरी / ग्रामीण</label><select name="other_land_area_type_' + id + '" id="other_land_area_type_' + id + '" class="form-control"><option value="">-- चयन --</option><option>शहरी</option><option>ग्रामीण</option></select></div>';

        txt += '<div class="col-sm-3 form-group"><label>4. भूमि क्षेत्रफल (हे.)</label><input type="text" name="other_land_land_area_' + id + '" id="other_land_land_area_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-3 form-group"><label>5. स्वामित्व</label><select name="other_land_ownership_' + id + '" id="other_land_ownership_' + id + '" class="form-control other_owner_select" data-row="' + id + '"><option value="">-- चयन --</option><option>संस्था स्वामित्व</option><option>पट्टा (लीज)</option><option>अन्य</option></select></div>';

        txt += '<div class="col-sm-3 form-group" id="other_owner_div_' + id + '" style="display:none;"><label>6. किसके स्वामित्व में है?</label><input type="text" name="other_land_other_owner_' + id + '" id="other_land_other_owner_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-3 form-group"><label>7. भूमि की स्थिति</label><select name="other_land_land_status_' + id + '" id="other_land_land_status_' + id + '" class="form-control land_status_select" data-row="' + id + '"><option value="">-- चयन --</option><option>खली पड़ी है</option><option>निर्माण</option><option>विवादित है</option></select></div>';

        txt += '<div class="col-sm-3 form-group" id="construct_div_' + id + '" style="display:none;"><label>8. निर्माण के प्रकार</label><select name="other_land_construction_' + id + '" id="other_land_construction_' + id + '" class="form-control"><option value="">-- चयन --</option><option>ऑफिस स्पेस</option><option>किराये पर</option><option>जर्जर निर्माण</option><option>अन्य</option></select></div>';

        txt += '</div>'; // inner row

        txt += '<div class="row">';
        txt += '<div class="col-sm-12 form-group"><label>पता</label><textarea name="other_land_address_' + id + '" id="other_land_address_' + id + '" rows="2" class="form-control"></textarea></div>';

        txt += '<div class="col-sm-6 form-group">';
        txt += '<label>लोकेशन मोड</label>';
        txt += '<select name="other_land_location_mode_' + id + '" id="other_land_location_mode_' + id + '" class="form-control location_mode_select" data-row="' + id + '">';
        txt += '<option value="">-- चयन --</option>';
        txt += '<option>Manual</option>';
        txt += '<option>GPS</option>';
        txt += '</select>';
        txt += '<button type="button" id="other_land_gps_btn_' + id + '" class="btn btn-sm btn-success" style="margin-top:5px;display:none;" onclick="other_land_fetchGPS(\'' + id + '\')">लोकेशन रिफ्रेश करें</button>';
        txt += '</div>';

        txt += '</div>'; // row

        txt += '<div id="latlon_container_' + id + '" style="display:none;">';
        txt += '<div class="row">';
        txt += '<div class="col-sm-6 form-group"><label>Latitude</label><input type="text" name="other_land_latitude_' + id + '" id="other_land_latitude_' + id + '" class="form-control other_lat"></div>';
        txt += '<div class="col-sm-6 form-group"><label>Longitude</label><input type="text" name="other_land_longitude_' + id + '" id="other_land_longitude_' + id + '" class="form-control other_lon"></div>';
        txt += '</div>';
        txt += '</div>';

        txt += '</div>'; // col-sm-8

        txt += '<div class="col-sm-4"><label style="font-weight:bold;">Location Map</label><div id="other_land_map_' + id + '" style="width:100%; height:280px; border:1px solid #aaa; background:#f8f8f8;"></div></div>';

        txt += '</div>'; // row

        txt += '<div class="col-sm-12 form-group" id="other_land_add_row_btn_area">';
        txt += '<button type="button" class="btn btn-info" onclick="other_land_add_row()">नई पंक्ति जोड़े [+]</button>';
        txt += '<input type="hidden" name="other_land_count" id="other_land_count" value="' + id + '">';
        txt += '</div>';

        txt += '</div>';

        $("#other_land_rows_container").append(txt);

        if (typeof loadOtherLandMap === "function") {
            loadOtherLandMap(id);
        }
    }

    // show/hide "other owner" text input
    $(document).on('change', '.other_owner_select', function () {
        var row = $(this).data('row');
        if ($(this).val() == 'अन्य') {
            $("#other_owner_div_" + row).show();
        } else {
            $("#other_owner_div_" + row).hide();
        }
    });

    // show/hide construction type when status is निर्माण
    $(document).on('change', '.land_status_select', function () {
        var row = $(this).data('row');
        if ($(this).val() == 'निर्माण') {
            $("#construct_div_" + row).show();
        } else {
            $("#construct_div_" + row).hide();
        }
    });

    // initialize map for a row (Google Maps)
    function initMapForRow(id) {
        var lat = parseFloat($("#other_land_latitude_" + id).val()) || 23.2599;
        var lon = parseFloat($("#other_land_longitude_" + id).val()) || 77.4126;
        var el = document.getElementById("other_land_map_" + id);
        if (!el) return;
        var map = new google.maps.Map(el, { center: { lat: lat, lng: lon }, zoom: 14 });
        new google.maps.Marker({ position: { lat: lat, lng: lon }, map: map });
    }

    // handle location mode changes
    $(document).on('change', '.location_mode_select', function () {
        var row = $(this).data('row');
        var mode = $(this).val();
        if (mode === "") {
            $("#latlon_container_" + row).hide();
            return;
        }
        $("#latlon_container_" + row).show();
        if (mode === "GPS") {
            $("#other_land_latitude_" + row).prop("readonly", true);
            $("#other_land_longitude_" + row).prop("readonly", true);
            $("#other_land_gps_btn_" + row).show();
        } else {
            $("#other_land_latitude_" + row).prop("readonly", false);
            $("#other_land_longitude_" + row).prop("readonly", false);
            $("#other_land_gps_btn_" + row).hide();
        }
        initMapForRow(row);
    });

    // fetch GPS coordinates
    function other_land_fetchGPS(id) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                $("#other_land_latitude_" + id).val(pos.coords.latitude);
                $("#other_land_longitude_" + id).val(pos.coords.longitude);
                initMapForRow(id);
            }, function (err) {
                alert("GPS Error: " + err.message);
            });
        } else {
            alert("GPS not supported!");
        }
    }

    $(document).ready(function () {
        // initialize maps for any rows that have a location mode set
        $(".location_mode_select").each(function () {
            var row = $(this).data("row");
            if ($(this).val() !== "") {
                initMapForRow(row);
            }
        });

        // also init on change
        $(".location_mode_select").on("change", function () {
            var row = $(this).data("row");
            if ($(this).val() !== "") {
                initMapForRow(row);
            }
        });
    });

    function fill_other_tehsil(district_id, row) {
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: { term: "b", id: "tehseel", val: district_id },
            success: function (res) {
                let data = JSON.parse(res);
                let txt = '<option value="">--Select--</option>';

                $.each(data, function (index, item) {
                    txt += '<option value="' + item.id + '">' + item.tehseel_name + '</option>';
                });

                $("#other_land_tehsil_" + row).html(txt);
            }
        });
    }

</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Select all inputs, textareas and selects
    const fields = document.querySelectorAll('input, textarea, select');

    fields.forEach(function(field) {

        // Skip hidden, button, submit, reset, file inputs
        if (field.type === "hidden" || 
            field.type === "submit" || 
            field.type === "button" || 
            field.type === "reset" || 
            field.type === "file") {
            return;
        }

        // If already filled -> lock
        if (field.value && field.value.trim() !== "") {
            if (field.tagName.toLowerCase() === "select") {
                field.disabled = true;   // readonly doesn't work for select
            } else {
                field.readOnly = true;
            }
        }
    });
});
</script>

<script type="text/javascript" src="js/multistepform_uprnss.js?v=1"></script>
<!-- <script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script> -->


<?php
page_footer_start();
page_footer_end();
?>