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
    'total_members' => '',
    'society_remark' => '',
    'society_objective' => '',
    'website' => '',
    'regional_office' => '',
    'district_branch_office' => '',
    'branch_office' => '',
    'education_center' => ''
];
// echo '-------------------------------------', $_GET['exdid'];
if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.`sno` as sno, apex_si_1_1.apex_id as `apex_id`, `longitude`, `latitude`, `committee_status`, `email_id`, concat("/user_data/", apex_si_1_1.apex_id, "/", photo_id) as photo_id, `society_registration_no`, `society_registration_date`, prakhand_name, `members_no`, `active_members_no`, `inactive_members_no`, `new_members`, `share_capital`, `inactive_to_active_no`, `total_members`, `society_remark`, `society_objective`, `website`, `regional_office`, `district_branch_office`, `branch_office`, `education_center` FROM `apex_si_1_1` LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '" ';
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
        $row_invoice['society_remark'] = $row_invoice['society_remark'];
        $row_invoice['society_objective'] = $row_invoice['society_objective'];
        $row_invoice['website'] = $row_invoice['website'];
        $row_invoice['regional_office'] = $row_invoice['regional_office'];
        $row_invoice['district_branch_office'] = $row_invoice['district_branch_office'];
        $row_invoice['branch_office'] = $row_invoice['branch_office'];
        $row_invoice['education_center'] = $row_invoice['education_center'];
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
                        <form action="scripts/ajax.php" method="post" enctype="multipart/form-data" id="user_form"
                            name="user_form">
                            <div id="steps-container">
                                <!-------------------1st start--------------------------------------------------------------------->
                                <div class="step">
                                    <div>
                                        <?php echo $msg; ?>
                                    </div>
                                    <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                            style="height:45px; width:45px;"> 1. समिति का विवरण </h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-sm-8 form-group">
                                                        <label>समिति का प्रकार</label>
                                                        <input type="text" id="" name=""
                                                            tabindex="<?php echo $tab++; ?>" readonly
                                                            value="शीर्ष सहकारी संस्था (APEX)">
                                                    </div>
                                                    <div class="col-sm-8 form-group" style="margin: 9px;"
                                                        id="sec_1_institute_name_container">
                                                        <label>समिति का नाम</label>
                                                        <input type="text" id="" name=""
                                                            tabindex="<?php echo $tab++; ?>" readonly
                                                            value="उ०प्र० सहकारी ग्राम विकास बैक">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>Latitude</label>
                                                        <input type="text" id="lat"
                                                            value="<?php echo $row_invoice['latitude']; ?>"
                                                            class="form-control">
                                                        <label>Longitude</label>
                                                        <input type="text" id="long"
                                                            value="<?php echo $row_invoice['longitude']; ?>"
                                                            class="form-control">
                                                        <button type="button" class="btn btn-info"
                                                            onClick="getLocation();">मुख्यालय की
                                                            जियो-लोकेशन</button>
                                                    </div>
                                                    <div class="col-md-10" id="map_container">
                                                        <iframe id="googlemap"
                                                            src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                            width="100%" height="100%"
                                                            style="border: 1px solid; border-radius: 10px;"
                                                            allowfullscreen="" loading="lazy"
                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label>क्या समिति सक्रिय है ?</label>
                                        <select class="form-control" id="committee_status" name="committee_status"
                                            tabindex="<?php echo $tab++; ?>">
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
                                    <div class="col-sm-3 form-group">
                                        <label>आधिकारिक वेब साईट</label>
                                        <input type="text" name="website" id="website"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                            value="<?php echo $row_invoice['website']; ?>">
                                    </div>

                                    <div class="col-sm-2 form-group">
                                        <label>समिति की फोटो संलग्न करें</label>
                                        <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp" name="society_photo"
                                            id="society_photo" tabindex="<?php echo $tab++; ?>" class="form-control">

                                    </div>
                                    <?php
                                    if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) {
                                        ?>
                                    <div class="col-sm-2 form-group">
                                        <img src="<?php echo $row_invoice['photo_id']; ?>"
                                            class="img-fluid img-thumbnail" style="height:50px;"
                                            id="society_photo_uploaded">
                                        <label><a href="<?php echo $row_invoice['photo_id']; ?>" target="_blank">संलग्न
                                                फोटो देखें</a></label>

                                    </div>
                                    <?php
                                    }
                                    ?>

                                    <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                        <label>समिति पंजीकरण संख्या</label>
                                        <input type="text" name="society_registration_no" id="society_registration_no"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
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
                                    <div class="col-sm-3">
                                        <label>समिति का संक्षिप्त विवरण</label>
                                        <textarea name="society_remark" id="society_remark" rows="3"
                                            tabindex="<?php echo $tab++; ?>"
                                            class="form-control"><?php echo $row_invoice['society_remark']; ?></textarea>
                                    </div>
                                    <div class="col-sm-3">
                                        <label>समिति का उद्देश्य</label>
                                        <textarea name="society_objective" id="society_objective" rows="3"
                                            tabindex="<?php echo $tab++; ?>"
                                            class="form-control"><?php echo $row_invoice['society_objective']; ?></textarea>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>कार्यालय भवन </label>
                                        <select class="form-control" id="committee_status"
                                            name="committee_status" tabindex="<?php echo $tab++; ?>"
                                            onChange="hide_show(this.value, '#committee_date_section', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                            <option value="">--Select--</option>
                                            <option value="yes">स्वयं का</option>
                                            <option value="no">किराये का</option>
                                        </select>
                                    </div>
                                </div>
                                <br>

                                <h5> <img src="#" class="img-fluid stat-icon" style="height:50px; width:50px;">शीर्ष समिति के नियंत्रणधीन अन्य कार्यालय</h5><br>
                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label><b>(I) क्षेत्रीय कार्यालय</b></label>
                                        <input type="text" name="regional_office" id="regional_office"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                            value="<?php echo $row_invoice['regional_office']; ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label><b>(II)जनपदीय शाखा कार्यालय</b></label>
                                        <input type="text" name="district_branch_office" id="district_branch_office"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                            value="<?php echo $row_invoice['district_branch_office']; ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label><b>(III)शाखा कार्यालय</b></label>
                                        <input type="text" name="branch_office" id="branch_office"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                            value="<?php echo $row_invoice['branch_office']; ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label><b>(IV)प्रशिक्षण केंद्र</b></label>
                                        <input type="text" name="education_center" id="education_center"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                            value="<?php echo $row_invoice['education_center']; ?>">
                                    </div>
                                </div>

                                <h5> <img src="images/logo/2.png" alt="text" class="img-fluid stat-icon"
                                        style="height:50px; width:50px;"> 1.1 कार्यालयों का विवरण </h5><br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="member_list_table">
                                                <thead>
                                                    <tr>
                                                        <th>क्र०स०</th>
                                                        <th>क्षेत्रीय कार्यालय </th>
                                                        <th>मण्डल के क्षेत्राधीन जनपद के नाम</th>
                                                        <th>जनपद के क्षेत्राधीन शाखाओ के नाम</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php for ($m = 1; $m <= $row_member_list['count']; $m++) { ?>
                                                    <tr id="member_row_<?php echo $m; ?>">
                                                        <td>
                                                            <?php echo $m; ?>
                                                        </td>
                                                        <td><input type="text" name="member_mandal_<?php echo $m; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_member_list['member_mandal_' . $m]; ?>">
                                                        </td>
                                                        <td><input type="text" name="member_district_<?php echo $m; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_member_list['member_district_' . $m]; ?>">
                                                        </td>
                                                        <td><input type="text" name="member_tehsil_<?php echo $m; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_member_list['member_tehsil_' . $m]; ?>">
                                                        </td>
                                                        <td><input type="text" name="member_block_<?php echo $m; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_member_list['member_block_' . $m]; ?>">
                                                        </td>
                                                        <td><input type="text" name="member_type_<?php echo $m; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_member_list['member_type_' . $m]; ?>">
                                                        </td>
                                                        <td><input type="text" name="member_name_<?php echo $m; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_member_list['member_name_' . $m]; ?>">
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                <button type="button" class="btn btn-info"
                                                    onclick="add_member_row();">नई पंक्ति जोड़े
                                                    [+]</button>
                                            </div>
                                            <input type="hidden" name="member_list_count" id="member_list_count"
                                                value="<?php echo $row_member_list['count']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <h5> <img src="#" class="img-fluid stat-icon" style="height:50px; width:50px;">सामान्य
                                    निकाय </h5><br>
                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label><b>(I) कुल सदस्यों की संख्या </b></label>
                                        <input type="text" name="nominal_member" id="nominal_member"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                            value="<?php echo $row_invoice['nominal_member']; ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label><b>(II)कुल सदस्य समितियों की संख्या </b></label>
                                        <input type="text" name="lifetime_member" id="lifetime_member"
                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                            value="<?php echo $row_invoice['lifetime_member']; ?>">
                                    </div>
                                </div>
                                <h5> <img src="#" class="img-fluid stat-icon" style="height:50px; width:50px;">प्रबंध
                                    सामिति </h5><br>
                                <div class="col-sm-3 form-group">
                                    <label>प्रबंध सामिति </label>
                                    <select name="liquidation" id="liquidation" tabindex="<?php echo $tab++; ?>"
                                        class="form-control"
                                        onchange="hide_show(this.value, '#liquidation_date_container', 'yes');hide_show(this.value, '#liquidation_status', 'yes');handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="yes" <?php echo ($row_invoice['liquidation'] == 'yes') ? ' selected="selected"' : ''; ?> style="background:#0f0"> हाँ
                                        </option>
                                        <option value="no" <?php echo ($row_invoice['liquidation'] == 'no') ? ' selected="selected"' : ''; ?> style="background:#f00"> नहीं
                                        </option>
                                    </select>
                                </div>

                                <h5> <img src="images/logo/2.png" alt="text" class="img-fluid stat-icon"
                                        style="height:50px; width:50px;"> प्रबंध समिति के अध्यक्ष / उपाध्यक्ष / सदस्यगण
                                    के नाम और मोबाइल नंबर </h5><br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- <div id="sec_3_b" style="overflow-x: auto; width: 100%"> -->
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>क्र०स०</th>
                                                    <th style="width: 200px;">प्रकार</th>
                                                    <th>नाम</th>
                                                    <th>मोबाईल न०</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td>
                                                            <select name="sec_2_stock_insurance" class="form-control">
                                                                <option value="">--select--</option>
                                                                <option value="yes">अध्यक्ष </option>
                                                                <option value="no">उपाध्यक्ष</option>
                                                                <option value="no">सदस्य </option>
                                                                <option value="no">नामित डायरेक्टर्स </option>
                                                                <option value="no">मुख्य महाप्रबंधक – नाबार्ड</option>
                                                                <option value="no">सदस्य / प्रबंध निदेशक
                                                                <option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="sec_1_2_name<?php echo $i; ?>"
                                                                class="form-control"></td>
                                                        <td><input type="text" name="sec_1_2_division<?php echo $i; ?>"
                                                                class="form-control"></td>

                                                    </tr>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>
                                        <!-- Button to add new row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-info" onClick="sec_3_b_add_rows()">
                                                    नई पंक्ति जोड़े [+]
                                                </button>
                                                <input type="hidden" name="sec_3_b_id" id="sec_3_b_id"
                                                    value="<?php echo htmlspecialchars($row_3_3['count']); ?>">
                                            </div>
                                        </div>
                                        <!-- </div> -->
                                    </div>
                                </div>
                            </div>
                    </div>
                    <!----------------2.1 start-------------------------------------------------------->
                    <div class="step">
                        <h4><img src="images/logo/3.png" class="img-fluid stat-icon" style="height:50px; width:50px;">
                            3(I) वित्तीय सूचना</h4>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>संतुलन पत्र किस वित्तीय वर्ष तक बना है</label>
                                    <select name="sec_3_santulan_patra" id="sec_3_santulan_patra" class="form-control"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_1" id="sec_3_profit_loss_amount_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.I वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_1']) ? $row_3_new_1['profit_loss_amount_1'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_1" id="sec_3_accumulated_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_1" id="sec_3_accumulated_amount_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_2" id="sec_3_profit_loss_amount_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.II वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_2']) ? $row_3_new_1['profit_loss_amount_2'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_2" id="sec_3_accumulated_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_2" id="sec_3_accumulated_amount_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_3" id="sec_3_profit_loss_amount_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.III वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_3']) ? $row_3_new_1['profit_loss_amount_3'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_3" id="sec_3_accumulated_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_3" id="sec_3_accumulated_amount_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label> अंतिम ए० जी० एम० किस वित्तीय वर्ष तक सम्पन्न हुई</label>
                                    <select name="sec_3_agm_year" class="form-control" tabindex="<?php echo $tab++; ?>">
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
                                    <select name="sec_3_dividend_year" id="sec_3_dividend_year" class="form-control"
                                        tabindex="<?php echo $tab++; ?>"
                                        onchange="hide_show(this.value, '#sec_2_dividend_per', ['2018', '2019', '2020', '2021', '2022','2023', '2024']); hide_show(this.value, '#sec_2_dividend', ['2018', '2019', '2020', '2021', '2022','2023', '2024']);">
                                        <option value="">--Select--</option>
                                        <option value="2018" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2018' ? 'selected="selected"' : '' ?>>
                                            2017-2018</option>
                                        <option value="2019" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2019' ? 'selected="selected"' : '' ?>>
                                            2018-2019</option>
                                        <option value="2020" <?php echo isset($row_3_new_1['sec_3_dividend_yearsec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2020' ? 'selected="selected"' : '' ?>>
                                            2019-2020</option>
                                        <option value="2021" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2021' ? 'selected="selected"' : '' ?>>
                                            2020-2021</option>
                                        <option value="2022" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2022' ? 'selected="selected"' : '' ?>>
                                            2021-2022</option>
                                        <option value="2023" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2023' ? 'selected="selected"' : '' ?>>
                                            2022-2023</option>
                                        <option value="2024" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == '2024' ? 'selected="selected"' : '' ?>>
                                            2023-2024</option>
                                        <option value="no" <?php echo isset($row_3_new_1['sec_3_dividend_year']) && $row_3_new_1['sec_3_dividend_year'] == 'no' ? 'selected="selected"' : '' ?>>
                                            नहीं दिया गया</option>
                                    </select>
                                </div>
                                <div class="col-sm-2 form-group" id="sec_2_dividend_per" style="display:none">
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
                    </div>

                    <div class="step">
                        <h4> <img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                style="height:50px; width:50px;"> समिति के गत पाँच वर्षों के व्यावसायिक किया कलापों का
                            विवरण-</h4>
                        <div class="col-sm-12">


                            <table class="table table-bordered table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th rowspan="2" style="text-align: center;">क्र०स०</th>
                                        <th rowspan="2" style="text-align: center;">वित्तीय वर्ष</th>
                                        <th rowspan="2" style="text-align: center;">ऋण वितरण (दिनांक 1 अप्रैल से 31
                                            मार्च)</th>
                                        <th rowspan="2" style="text-align: center;">वसूली (दिनांक 1 जुलाई से 30 जून)
                                        </th>
                                        <th rowspan="2" style="text-align: center;">सावधि जमा योजनान्तर्गत निक्षेपित
                                            (दिनांक 1 अप्रैल से 31 मार्च)</th>
                                        <th colspan="2" style="text-align: center;">NPA की स्थिति 31 मार्च को</th>
                                    </tr>
                                    <tr>
                                        <th style="text-align: center;">Gross NPA %</th>
                                        <th style="text-align: center;">Net NPA %</th>
                                    </tr>



                                    <tr>
                                        <td>1</td>
                                        <td>2019-20
                                        </td>
                                        <td><input type="text" name="desktop_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="desktop_value_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="desktop_4" class="form-control"></td>
                                        <td><input type="text" name="desktop_value_4_gross" class="form-control"></td>
                                        <td><input type="text" name="desktop_value_4_net" class="form-control"></td>

                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>2020-21</td>
                                        <td><input type="text" name="printer_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="printer_value_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="printer_4" class="form-control"></td>
                                        <td><input type="text" name="printer_value_4_gross" class="form-control"></td>
                                        <td><input type="text" name="printer_value_4_net" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>2021-22</td>
                                        <td><input type="text" name="printer_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="printer_value_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="printer_4" class="form-control"></td>
                                        <td><input type="text" name="printer_value_4_gross" class="form-control"></td>
                                        <td><input type="text" name="printer_value_4_net" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>2022-23</td>
                                        <td><input type="text" name="camera_2023" class="form-control"></td>
                                        <td><input type="text" name="camera_value_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="camera_4" class="form-control"></td>
                                        <td><input type="text" name="camera_value_4_gross" class="form-control"></td>
                                        <td><input type="text" name="camera_value_4_net" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>2023-24</td>
                                        <td><input type="text" name="ups_2023" class="form-control"></td>
                                        <td><input type="text" name="ups_value_2023" class="form-control">
                                        </td>
                                        <td><input type="text" name="ups_4" class="form-control"></td>
                                        <td><input type="text" name="ups_value_4_gross" class="form-control"></td>
                                        <td><input type="text" name="ups_value_4_net" class="form-control"></td>
                                    </tr>


                                </tbody>
                            </table>
                            <div class="text-right">
                                <button type="button" class="btn btn-info" onclick="add_member_row();">नई पंक्ति जोड़े
                                    [+]</button>
                            </div>
                            <input type="hidden" name="member_list_count" id="member_list_count"
                                value="<?php echo $row_member_list['count']; ?>">
                        </div>
                    </div>
                    <!------ Manav Sampada start ------->
                    <div class="step">
                        <h4><img src="images/logo/5.png" alt="text" class="img-fluid stat-icon"
                                style="height:50px; width:50px;"> मानव सम्पदा </h4>
                        <div class="col-sm-12">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>कम सं0</th>
                                        <th>पदों का नाम</th>
                                        <th>संवर्ग</th>
                                        <th>स्वीकृत पद</th>
                                        <th>कार्यरत</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- 1. Prabandh Nideshak -->
                                    <tr>
                                        <td>1</td>
                                        <td>प्रबन्ध निदेशक</td>
                                        <td>प्रतिनियुक्ति</td>
                                        <td><input type="text" name="ms_1_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_1_working" class="form-control"></td>
                                    </tr>
                                    <!-- 2. Mukhya Mahaprabandhak -->
                                    <tr>
                                        <td rowspan="2">2</td>
                                        <td rowspan="2">मुख्य महाप्रबन्धक</td>
                                        <td>सामान्य</td>
                                        <td><input type="text" name="ms_2_1_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_2_1_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>तकनीकी</td>
                                        <td><input type="text" name="ms_2_2_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_2_2_working" class="form-control"></td>
                                    </tr>
                                    <!-- 3. Mahaprabandhak -->
                                    <tr>
                                        <td rowspan="2">3</td>
                                        <td rowspan="2">महाप्रबन्धक</td>
                                        <td>सामान्य</td>
                                        <td><input type="text" name="ms_3_1_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_3_1_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>तकनीकी</td>
                                        <td><input type="text" name="ms_3_2_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_3_2_working" class="form-control"></td>
                                    </tr>
                                    <!-- 4. Varishtha Vitta Evam Lekhadhikari -->
                                    <tr>
                                        <td>4</td>
                                        <td>वरिष्ठ वित्त एवं लेखाधिकारी</td>
                                        <td>प्रतिनियुक्ति</td>
                                        <td><input type="text" name="ms_4_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_4_working" class="form-control"></td>
                                    </tr>
                                    <!-- 5. Up Mahaprabandhak -->
                                    <tr>
                                        <td rowspan="4">5</td>
                                        <td rowspan="4">उप महाप्रबन्धक</td>
                                        <td>सामान्य</td>
                                        <td><input type="text" name="ms_5_1_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_5_1_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>तकनीकी</td>
                                        <td><input type="text" name="ms_5_2_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_5_2_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>विधि</td>
                                        <td><input type="text" name="ms_5_3_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_5_3_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>नि:संवर्ग</td>
                                        <td><input type="text" name="ms_5_4_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_5_4_working" class="form-control"></td>
                                    </tr>
                                    <!-- 6. Sahayak Mahaprabandhak -->
                                    <tr>
                                        <td rowspan="4">6</td>
                                        <td rowspan="4">सहायक महाप्रबन्धक</td>
                                        <td>सामान्य</td>
                                        <td><input type="text" name="ms_6_1_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_6_1_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>तकनीकी</td>
                                        <td><input type="text" name="ms_6_2_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_6_2_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>विधि</td>
                                        <td><input type="text" name="ms_6_3_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_6_3_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>नि:संवर्ग</td>
                                        <td><input type="text" name="ms_6_4_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_6_4_working" class="form-control"></td>
                                    </tr>
                                    <!-- 7. Varishtha Prabandhak -->
                                    <tr>
                                        <td rowspan="4">7</td>
                                        <td rowspan="4">वरिष्ठ प्रबन्धक</td>
                                        <td>सामान्य</td>
                                        <td><input type="text" name="ms_7_1_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_7_1_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>तकनीकी</td>
                                        <td><input type="text" name="ms_7_2_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_7_2_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>विधि</td>
                                        <td><input type="text" name="ms_7_3_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_7_3_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>कम्प्यूटर</td>
                                        <td><input type="text" name="ms_7_4_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_7_4_working" class="form-control"></td>
                                    </tr>
                                    <!-- 8. Prabandhak -->
                                    <tr>
                                        <td rowspan="2">8</td>
                                        <td rowspan="2">प्रबन्धक</td>
                                        <td>सामान्य</td>
                                        <td><input type="text" name="ms_8_1_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_8_1_working" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td>विधि</td>
                                        <td><input type="text" name="ms_8_2_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_8_2_working" class="form-control"></td>
                                    </tr>
                                    <!-- 9. Niji Sachiv -->
                                    <tr>
                                        <td>9</td>
                                        <td>निजी सचिव</td>
                                        <td></td>
                                        <td><input type="text" name="ms_9_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_9_working" class="form-control"></td>
                                    </tr>
                                    <!-- 10. Vayaktik Sahayak -->
                                    <tr>
                                        <td>10</td>
                                        <td>वैयक्तिक सहायक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_10_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_10_working" class="form-control"></td>
                                    </tr>
                                    <!-- 11. Karyalay Adheekshak -->
                                    <tr>
                                        <td>11</td>
                                        <td>कार्यालय अधीक्षक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_11_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_11_working" class="form-control"></td>
                                    </tr>
                                    <!-- 12. Ashulipik -->
                                    <tr>
                                        <td>12</td>
                                        <td>आशुलिपिक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_12_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_12_working" class="form-control"></td>
                                    </tr>
                                    <!-- 13. Field Officer -->
                                    <tr>
                                        <td>13</td>
                                        <td>फील्ड आफिसर</td>
                                        <td></td>
                                        <td><input type="text" name="ms_13_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_13_working" class="form-control"></td>
                                    </tr>
                                    <!-- 14. Shakha Aankik / Lekha Lipik -->
                                    <tr>
                                        <td>14</td>
                                        <td>शाखा आंकिक / लेखा लिपिक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_14_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_14_working" class="form-control"></td>
                                    </tr>
                                    <!-- 15. Sahayak Field Officer -->
                                    <tr>
                                        <td>15</td>
                                        <td>सहायक फील्ड आफिसर</td>
                                        <td></td>
                                        <td><input type="text" name="ms_15_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_15_working" class="form-control"></td>
                                    </tr>
                                    <!-- 16. Sahayak Shakha Aankik / Avar Lipik -->
                                    <tr>
                                        <td>16</td>
                                        <td>सहायक शाखा आंकिक / अवर लिपिक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_16_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_16_working" class="form-control"></td>
                                    </tr>
                                    <!-- 17. Pravar Tankak -->
                                    <tr>
                                        <td>17</td>
                                        <td>प्रवर टंकक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_17_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_17_working" class="form-control"></td>
                                    </tr>
                                    <!-- 18. Avar Tankak -->
                                    <tr>
                                        <td>18</td>
                                        <td>अवर टंकक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_18_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_18_working" class="form-control"></td>
                                    </tr>
                                    <!-- 19. Data Entry Operator -->
                                    <tr>
                                        <td>19</td>
                                        <td>डाटा इन्ट्री आपरेटर</td>
                                        <td></td>
                                        <td><input type="text" name="ms_19_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_19_working" class="form-control"></td>
                                    </tr>
                                    <!-- 20. Vahan Chalak -->
                                    <tr>
                                        <td>20</td>
                                        <td>वाहन चालक</td>
                                        <td></td>
                                        <td><input type="text" name="ms_20_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_20_working" class="form-control"></td>
                                    </tr>
                                    <!-- 21. Chaprasi / Chaukidar -->
                                    <tr>
                                        <td>21</td>
                                        <td>चपरासी / चौकीदार</td>
                                        <td></td>
                                        <td><input type="text" name="ms_21_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_21_working" class="form-control"></td>
                                    </tr>
                                    <!-- 22. Sweeper -->
                                    <tr>
                                        <td>22</td>
                                        <td>स्वीपर</td>
                                        <td></td>
                                        <td><input type="text" name="ms_22_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_22_working" class="form-control"></td>
                                    </tr>
                                    <!-- 23. Contract/Outsourced -->
                                    <tr>
                                        <td>23</td>
                                        <td>संविदा / आउटसोर्स पर कार्यरत कार्मिक का नाम</td>
                                        <td></td>
                                        <td><input type="text" name="ms_23_sanctioned" class="form-control"></td>
                                        <td><input type="text" name="ms_23_working" class="form-control"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!------ 3td start ------->
                    <div class="step">

                        <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                style="height:50px; width:50px;"> 7. मुख्यालय पर कार्यरत अधिकारियों का विवरण</h4>

                        <div class="row">
                            <div class="col-sm-12">
                                <!-- <div id="sec_3_b" style="overflow-x: auto; width: 100%"> -->
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>क्र०स०</th>
                                            <th style="width: 200px;">पद नाम</th>
                                            <th>कार्यरत अधिकारी/कर्मचारी का नाम</th>
                                            <th>कार्यालय में योगदान तिथि</th>
                                            <th>संबंधित अनुभाग</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td>
                                                    <select name="sec_2_stock_insurance" class="form-control">
                                                        <option value="">--select--</option>
                                                        <option value="yes">प्रबन्ध निदेशक </option>
                                                        <option value="no">मुख्य महाप्रबन्धक</option>
                                                        <option value="no">महाप्रबन्धक </option>
                                                        <option value="no">उप महाप्रबन्धक </option>
                                                        <option value="no">सहायक महाप्रबन्धक</option>

                                                    </select>
                                                </td>
                                                <td><input type="text" name="sec_1_2_name<?php echo $i; ?>"
                                                        class="form-control"></td>
                                                <td><input type="text" name="sec_1_2_division<?php echo $i; ?>"
                                                        class="form-control"></td>
                                                <td><input type="text" name="sec_1_2_division<?php echo $i; ?>"
                                                        class="form-control"></td>

                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                                <!-- Button to add new row -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-info" onClick="sec_3_b_add_rows()">
                                            नई पंक्ति जोड़े [+]
                                        </button>
                                        <input type="hidden" name="sec_3_b_id" id="sec_3_b_id"
                                            value="<?php echo htmlspecialchars($row_3_3['count']); ?>">
                                    </div>
                                </div>
                                <!-- </div> -->
                            </div>


                            <!-- <div class="col-sm-12">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label>Latitude</label>
                                                                        <input type="text" id="lat" disabled="disabled"
                                                                            value="<?php echo htmlspecialchars($row_invoice['latitude']); ?>"
                                                                            class="form-control">
                                                                        <label>Longitude</label>
                                                                        <input type="text" id="long" disabled="disabled"
                                                                            value="<?php echo htmlspecialchars($row_invoice['longitude']); ?>"
                                                                            class="form-control">
                                                                        <button type="button" class="btn btn-info"
                                                                            onClick="getLocation();">
                                                                            मुख्यालय की जियो-लोकेशन
                                                                        </button>
                                                                    </div>
                                                                    <div class="col-md-8" id="map_container">
                                                                        <iframe id="googlemap"
                                                                            src="https://maps.google.com/maps?q=<?php echo htmlspecialchars($row_invoice['latitude'] . ',' . $row_invoice['longitude']); ?>&hl=en&z=13&amp;output=embed"
                                                                            width="100%" height="100%"
                                                                            style="border:1px solid; border-radius:10px;"
                                                                            allowfullscreen="" loading="lazy"
                                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                                    </div>
                                                                </div>
                                                            </div> -->
                        </div>
                        <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                style="height:50px; width:50px;"> 7. समिति भवन/सम्पत्ति का विवरण</h4>
                        <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(I) भूखंड
                            स्वामित्व का विवरण</h5>
                        <div class="row mb-3">
                            <div class="col-sm-4 form-group">
                                <label>(I) समिति भवन का स्वामित्व</label>
                                <select name="sec_7_committee_building_ownership" class="form-control">
                                    <option value="">--Select--</option>
                                    <option value="own">स्वयं का</option>
                                    <option value="rented">किराये का</option>
                                    <option value="other">अन्य</option>
                                </select>
                            </div>
                        </div>

                        <!-- (II) Plot Details -->
                        <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(II)
                            भूखंड का विवरण</h5>
                        <div class="row">

                            <div class="col-sm-3 form-group">
                                <label>क्षेत्रफल (हेक्टेयर में)</label>
                                <input type="text" name="sec_7_area_hectare" class="form-control chk_decimal">
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>राजस्व अभिलेख में दर्ज होने की स्थिति (हाँ/नहीं)</label>
                                <select name="sec_7_revenue_record_status" class="form-control">
                                    <option value="">--Select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>यदि नहीं है तो किये जाने वाले प्रयास का विवरण</label>
                                <input type="text" name="sec_7_revenue_record_efforts" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 form-group">
                                <label>गाटा/खसरा संख्या</label>
                                <input type="text" name="sec_7_gata_khasra_no" class="form-control">
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>समिति भूखंड फोटो संलग्न करें</label>
                                <input type="file" name="sec_7_plot_photo" class="form-control">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>टिप्पणी</label>
                                <input type="text" name="sec_7_remarks" class="form-control">
                            </div>
                        </div>

                        <!-- (III) Plot Boundary Details -->
                        <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(III)
                            भूखंड की चहारदीवारी का विवरण</h5>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>भूखंड की पूर्व दिशा का विवरण</label>
                                <input type="text" name="sec_7_east_desc" class="form-control">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>भूखंड की पश्चिम दिशा का विवरण</label>
                                <input type="text" name="sec_7_west_desc" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>भूखंड की उत्तर दिशा का विवरण</label>
                                <input type="text" name="sec_7_north_desc" class="form-control">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>भूखंड की दक्षिण दिशा का विवरण</label>
                                <input type="text" name="sec_7_south_desc" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>सड़क पर भूमि की लम्बाई (फ्रंट रोड जमीन) मीटर में</label>
                                <input type="text" name="sec_7_road_length_meters" class="form-control chk_decimal">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>प्रमुख द्वार की दिशा (फ्रंट साइड)</label>
                                <select name="sec_7_main_gate_direction" class="form-control">
                                    <option value="">--Select--</option>
                                    <option value="east">पूर्व</option>
                                    <option value="west">पश्चिम</option>
                                    <option value="north">उत्तर</option>
                                    <option value="south">दक्षिण</option>
                                </select>
                            </div>
                        </div>

                        <!-- (IV) Constructed Building Details -->
                        <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(IV)
                            निर्मित भवन का विवरण</h5>
                        <div id="sec_7_constructed_building_container">
                            <?php
                            // Logic to load existing rows if any
                            /* 
                             * Assuming we need to support dynamic rows similar to other sections.
                             * For now, we initialize with 1 row or load from DB if available.
                             * Using placeholder logic since DB schema for new fields is creating...
                             */
                            $sec_7_4_count = isset($row_7_4['count']) ? $row_7_4['count'] : 1;
                            ?>
                            <input type="hidden" name="sec_7_4_count" id="sec_7_4_count"
                                value="<?php echo $sec_7_4_count; ?>">

                            <?php for ($i = 1; $i <= $sec_7_4_count; $i++) { ?>
                                <div class="row" id="sec_7_4_row_<?php echo $i; ?>">
                                    <div class="col-sm-2 form-group">
                                        <label>लंबाई (मीटर में)</label>
                                        <input type="text" name="sec_7_4_length_<?php echo $i; ?>"
                                            class="form-control chk_decimal">
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>चौड़ाई (मीटर में)</label>
                                        <input type="text" name="sec_7_4_width_<?php echo $i; ?>"
                                            class="form-control chk_decimal">
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>भवन का प्रकार</label>
                                        <select name="sec_7_4_building_type_<?php echo $i; ?>" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="office">कार्यालय</option>
                                            <option value="godown">गोदाम</option>
                                            <option value="shop">दुकान</option>
                                            <option value="other">अन्य</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>किस फंड से बना है</label>
                                        <select name="sec_7_4_fund_source_<?php echo $i; ?>" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="society">समिति निधि</option>
                                            <option value="govt">शासकीय अनुदान</option>
                                            <option value="loan">ऋण</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>टिप्पणी</label>
                                        <input type="text" name="sec_7_4_remarks_<?php echo $i; ?>" class="form-control">
                                    </div>
                                    <?php if ($i == 1) { ?>
                                        <div class="col-sm-2 form-group my-auto">
                                            <button type="button" class="btn btn-info mt-4"
                                                onclick="add_constructed_building_row();">नई पंक्ति जोड़े [+]</button>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- (V) Vacant Land Details -->
                        <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(V) खाली
                            पड़ी भूमि का विवरण</h5>
                        <div id="sec_7_vacant_land_container">
                            <?php $sec_7_5_count = isset($row_7_5['count']) ? $row_7_5['count'] : 1; ?>
                            <input type="hidden" name="sec_7_5_count" id="sec_7_5_count"
                                value="<?php echo $sec_7_5_count; ?>">

                            <?php for ($i = 1; $i <= $sec_7_5_count; $i++) { ?>
                                <div class="row" id="sec_7_5_row_<?php echo $i; ?>">
                                    <div class="col-sm-2 form-group">
                                        <label>क्षेत्रफल (हेक्टेयर में)</label>
                                        <input type="text" name="sec_7_5_area_<?php echo $i; ?>"
                                            class="form-control chk_decimal">
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>भूमि की स्थिति (उपजाऊ/बंजर)</label>
                                        <select name="sec_7_5_status_<?php echo $i; ?>" class="form-control">
                                            <option value="">--select--</option>
                                            <option value="fertile">उपजाऊ</option>
                                            <option value="barren">बंजर</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>स्थान (समिति प्रांगण या अन्य स्थान)</label>
                                        <select name="sec_7_5_location_<?php echo $i; ?>" class="form-control">
                                            <option value="">--select--</option>
                                            <option value="campus">समिति प्रांगण</option>
                                            <option value="other">अन्य स्थान</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>गोदाम के लिए उपयुक्त है या नहीं ?</label>
                                        <select name="sec_7_5_godown_suitable_<?php echo $i; ?>" class="form-control">
                                            <option value="">--Select--</option>
                                            <option value="yes">हाँ</option>
                                            <option value="no">नहीं</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>जनपद से रैक की दूरी</label>
                                        <input type="text" name="sec_7_5_rack_distance_<?php echo $i; ?>"
                                            class="form-control">
                                    </div>
                                    <?php if ($i == 1) { ?>
                                        <div class="col-sm-2 form-group my-auto">
                                            <button type="button" class="btn btn-info mt-4" onclick="add_vacant_land_row();">नई
                                                पंक्ति जोड़े [+]</button>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- (VI) Access Road Details -->
                        <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(VI)
                            पहुंच मार्ग का विवरण</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>पहुंच मार्ग</label>
                                <select name="sec_7_access_road" class="form-control">
                                    <option value="">--select--</option>
                                    <option value="paved">पक्की सड़क</option>
                                    <option value="unpaved">कच्ची सड़क</option>
                                    <option value="none">मार्ग नहीं है</option>
                                </select>
                            </div>
                        </div>

                        <!-- (VII) Other -->
                        <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(VII)
                            अन्य</h5>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>खाली पड़ी भूमि में हरे पेड़ों की संख्या</label>
                                <input type="number" name="sec_7_green_trees_count" class="form-control">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>बाउंड्रीवाल का निर्माण</label>
                                <select name="sec_7_boundary_wall" class="form-control">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                    <option value="partial">आंशिक</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-------4th start------->

                    <div class="step">
                        <div?php echo $msg; ?>

                            <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                    style="height:45px; width:45px;"> क्षेत्रीय कार्यालय</h4>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="row">

                                            <div class="col-sm-8 form-group" style="margin: 9px;"
                                                id="sec_1_institute_name_container">
                                                <label>क्षेत्रीय कार्यालय का नाम</label>
                                                <input type="text" id="" name="" tabindex="<?php echo $tab++; ?>"
                                                    readonly value="उ०प्र० सहकारी ग्राम विकास बैक">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label>Latitude</label>
                                                <input type="text" id="lat"
                                                    value="<?php echo $row_invoice['latitude']; ?>"
                                                    class="form-control">
                                                <label>Longitude</label>
                                                <input type="text" id="long"
                                                    value="<?php echo $row_invoice['longitude']; ?>"
                                                    class="form-control">
                                                <button type="button" class="btn btn-info"
                                                    onClick="getLocation();">मुख्यालय की जियो-लोकेशन</button>
                                            </div>
                                            <div class="col-md-10" id="map_container">
                                                <iframe id="googlemap"
                                                    src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                    width="100%" height="100%"
                                                    style="border: 1px solid; border-radius: 10px;" allowfullscreen=""
                                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row" style="margin: 7px;">
                                            <div class="col-sm-3">
                                                <label>कार्यालय का पता</label>
                                                <input name="address" id="address" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control" value="<?php echo $row_invoice['address']; ?>">
                                            </div>

                                            <div class="col-sm-3">
                                                <label>ई-मेल आईडी</label>
                                                <input name="address" id="address" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control" value="<?php echo $row_invoice['address']; ?>">
                                            </div>


                                            <div class="col-sm-3">
                                                <label>संपर्क सूत्र</label>
                                                <input name="address" id="address" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control" value="<?php echo $row_invoice['address']; ?>">
                                            </div>
                                            <!-- <div class="col-md-3">
                                                        <?php echo $row_invoice['mobile_number']; ?>
                                                    </div> -->
                                            <div class="col-md-3 form-group">
                                                <label>कार्यालय भवन </label>
                                                <select class="form-control" id="committee_status"
                                                    name="committee_status" tabindex="<?php echo $tab++; ?>"
                                                    onChange="hide_show(this.value, '#committee_date_section', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes">स्वयं का</option>
                                                    <option value="no">किराये पर</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr />

                                <br>


                                <div class="row">
                                    <div class="col-sm-12">
                                        <!-- <div id="sec_3_b" style="overflow-x: auto; width: 100%"> -->
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>क्र०स०</th>
                                                    <th style="width: 200px;">पद नाम </th>
                                                    <th>कार्यरत अधिकारी /कर्मचारी का नाम</th>
                                                    <th> कार्यालय में योगदान तिथि</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td>
                                                            <select name="sec_2_stock_insurance" class="form-control">
                                                                <option value="">--select--</option>
                                                                <option value="yes">क्षेत्रीय प्रबन्धक </option>
                                                                <option value="no">आकिक</option>
                                                                <option value="no">सहायक आकिक </option>
                                                                <option value="no">अवर/प्रवर टकक </option>
                                                                <option value="no">सहयोगी</option>
                                                                <option value="no">अन्य कार्यरत संवर्ग</option>
                                                                <option value="no">संविदा/आउटसोर्स पर कार्यरत कार्मिक का नाम
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="sec_1_2_name<?php echo $i; ?>"
                                                                class="form-control"></td>
                                                        <td><input type="text" name="sec_1_2_division<?php echo $i; ?>"
                                                                class="form-control"></td>

                                                    </tr>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>
                                        <!-- Button to add new row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-info" onClick="sec_3_b_add_rows()">
                                                    नई पंक्ति जोड़े [+]
                                                </button>
                                                <input type="hidden" name="sec_3_b_id" id="sec_3_b_id"
                                                    value="<?php echo htmlspecialchars($row_3_3['count']); ?>">
                                            </div>
                                        </div>
                                        <!-- </div> -->
                                    </div>


                                    <!-- <div class="col-sm-12">
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <label>Latitude</label>
                                                                        <input type="text" id="lat" disabled="disabled"
                                                                            value="<?php echo htmlspecialchars($row_invoice['latitude']); ?>"
                                                                            class="form-control">
                                                                        <label>Longitude</label>
                                                                        <input type="text" id="long" disabled="disabled"
                                                                            value="<?php echo htmlspecialchars($row_invoice['longitude']); ?>"
                                                                            class="form-control">
                                                                        <button type="button" class="btn btn-info"
                                                                            onClick="getLocation();">
                                                                            मुख्यालय की जियो-लोकेशन
                                                                        </button>
                                                                    </div>
                                                                    <div class="col-md-8" id="map_container">
                                                                        <iframe id="googlemap"
                                                                            src="https://maps.google.com/maps?q=<?php echo htmlspecialchars($row_invoice['latitude'] . ',' . $row_invoice['longitude']); ?>&hl=en&z=13&amp;output=embed"
                                                                            width="100%" height="100%"
                                                                            style="border:1px solid; border-radius:10px;"
                                                                            allowfullscreen="" loading="lazy"
                                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                                    </div>
                                                                </div>
                                                            </div> -->
                                </div>
                            </div>
                    </div>
                    <div class="step">
                        <h4><img src="images/logo/3.png" class="img-fluid stat-icon" style="height:50px; width:50px;">
                            3(I) वित्तीय सूचना</h4>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>संतुलन पत्र किस वित्तीय वर्ष तक बना है</label>
                                    <select name="sec_3_santulan_patra" id="sec_3_santulan_patra" class="form-control"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_1" id="sec_3_profit_loss_amount_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.I वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_1']) ? $row_3_new_1['profit_loss_amount_1'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_1" id="sec_3_accumulated_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_1" id="sec_3_accumulated_amount_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_2" id="sec_3_profit_loss_amount_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.II वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_2']) ? $row_3_new_1['profit_loss_amount_2'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_2" id="sec_3_accumulated_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_2" id="sec_3_accumulated_amount_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_3" id="sec_3_profit_loss_amount_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.III वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_3']) ? $row_3_new_1['profit_loss_amount_3'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_3" id="sec_3_accumulated_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_3" id="sec_3_accumulated_amount_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                    </div>

                    <div class="step">
                        <!-- <h4><img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> 5. सुविधाएं </h4> -->
                        <h5>मण्डल के गत पाँच वर्षों के व्यावसायिक क्रिया कलापो का विवरण-</h5>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>क०सं०</th>
                                            <th>वित्तीय वर्ष</th>
                                            <th>ऋण वितरण<br>(दिनांक 1 अप्रैल से 31 मार्च)</th>
                                            <th>वसूली<br>(दिनांक 1 जुलाई से 30 जून)</th>
                                            <th>NPA की स्थिति<br>31 मार्च को NPA %</th>
                                        </tr>
                                    </thead>
                                    <tbody id="financial_table_body">
                                        <tr>
                                            <td>1</td>
                                            <td>2019-20</td>
                                            <td><input type="text" name="loan_dist_1" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_1" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_1" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>2020-21</td>
                                            <td><input type="text" name="loan_dist_2" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_2" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_2" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>2021-22</td>
                                            <td><input type="text" name="loan_dist_3" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_3" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_3" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>2022-23</td>
                                            <td><input type="text" name="loan_dist_4" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_4" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_4" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>2023-24</td>
                                            <td><input type="text" name="loan_dist_5" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_5" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_5" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-right">
                                    <button type="button" class="btn btn-primary" onclick="addFinancialRow()"><i
                                            class="fa fa-plus"></i> Add Row</button>
                                </div>
                            </div>
                        </div>
                        <h4><img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                style="height:50px; width:50px;"> 4. सुविधाएं </h4>
                        <h5>(i) बिजली कनेक्शन</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>बिजली की उपलब्धता</label>
                                <select name="sec_8_electricity_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>

                        <h5>(ii) सोलर कनेक्शन</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>सोलर कनेक्शन</label>
                                <select name="sec_8_solar_connection" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>

                        <h5>(iii) कम्प्यूटराइजेशन की प्रगति</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>कम्प्यूटर की उपलब्धता</label>
                                <select name="sec_8_computer_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-4 form-group">
                                <label>प्रिंटर की उपलब्धता</label>
                                <select name="sec_8_printer_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-4 form-group">
                                <label>अन्य</label>
                                <input type="text" name="sec_8_other_computer_equipment" class="form-control">
                            </div>
                        </div>

                        <h5>(iv) इंटरनेट कनेक्शन की उपलब्धता</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>इंटरनेट कनेक्शन की उपलब्धता</label>
                                <select name="sec_8_internet_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>

                        <h5>(v) पेयजल की उपलब्धता</h5>
                        <div class="row">
                            <div class="col-sm-3 form-group">
                                <label>सरकारी नल की सुविधा</label>
                                <select name="sec_8_govt_tap_facility" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>सबमर्सिबल पम्प</label>
                                <select name="sec_8_submersible_pump" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>पानी की टंकी</label>
                                <select name="sec_8_water_tank_facility" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>हैण्ड पम्प</label>
                                <select name="sec_8_hand_pump_facility" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="step">
                        <h4>4. शाखा / जनपदीय शाखा कार्यालय</h4>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>शाखा का नाम</label>
                                        <input type="text" name="sec_branch_name" class="form-control"
                                            value="उत्तर प्रदेश सहकारी ग्राम विकास बैंक लि0," readonly>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Latitude</label>
                                            <input type="text" id="lat" value="<?php echo $row_invoice['latitude']; ?>"
                                                class="form-control">
                                            <label>Longitude</label>
                                            <input type="text" id="long"
                                                value="<?php echo $row_invoice['longitude']; ?>" class="form-control">
                                            <button type="button" class="btn btn-info" onClick="getLocation();">मुख्यालय
                                                की जियो-लोकेशन</button>
                                        </div>
                                        <div class="col-md-10" id="map_container">
                                            <iframe id="googlemap"
                                                src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                width="100%" height="100%"
                                                style="border: 1px solid; border-radius: 10px;" allowfullscreen=""
                                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 15px;">
                                <div class="col-sm-4 form-group">
                                    <label>कार्यालय का पता</label>
                                    <input type="text" name="sec_branch_address" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>जनपद</label>
                                    <input type="text" name="sec_branch_district" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>मण्डल</label>
                                    <input type="text" name="sec_branch_mandal" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label>ई-मेल आई डी0</label>
                                    <input type="text" name="sec_branch_email" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>सम्पर्क सूत्र</label>
                                    <input type="text" name="sec_branch_contact" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>कार्यालय भवन</label>
                                    <select name="sec_branch_building_status" class="form-control">
                                        <option value="">--Select--</option>
                                        <option value="own">स्वयं का</option>
                                        <option value="rented">किराये पर</option>
                                    </select>
                                </div>
                            </div>

                            <h5 style="margin-top: 20px;">कार्मिकों का विवरण-</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>क्रम सं0</th>
                                            <th>पद नाम</th>
                                            <th>कार्यरत अधिकारी / कर्मचारी का नाम</th>
                                            <th>कार्यालय में योगदान तिथि</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>वरिष्ठ / शाखा प्रबन्धक</td>
                                            <td><input type="text" name="sec_branch_emp_name_1" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_1" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>फील्ड आफिसर</td>
                                            <td><input type="text" name="sec_branch_emp_name_2" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_2" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>शाखा आंकिक</td>
                                            <td><input type="text" name="sec_branch_emp_name_3" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_3" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>सहायक शाखा आंकिक</td>
                                            <td><input type="text" name="sec_branch_emp_name_4" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_4" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>सहायक फील्ड आफिसर</td>
                                            <td><input type="text" name="sec_branch_emp_name_5" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_5" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>अवर / प्रवर टंकक</td>
                                            <td><input type="text" name="sec_branch_emp_name_6" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_6" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td>सहयोगी</td>
                                            <td><input type="text" name="sec_branch_emp_name_7" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_7" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>8</td>
                                            <td>अन्य कार्यरत संवर्ग</td>
                                            <td><input type="text" name="sec_branch_emp_name_8" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_8" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>9</td>
                                            <td>संविदा / आउटसर्स पर कार्यरत कार्मिक का नाम</td>
                                            <td><input type="text" name="sec_branch_emp_name_9" class="form-control">
                                            </td>
                                            <td><input type="date" name="sec_branch_emp_date_9" class="form-control">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!----------------5th start-------------------------------------------------------->
                    <div class="step">
                        <!-- <h4><img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                                        style="height:50px; width:50px;"> 6 (I) संस्थागत ढांचा</h4> -->
                        <div class="col-sm-12">
                            <h5>(I) सदस्यों का विवरण—</h5>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label>कुल सदस्य सदस्यों की संख्या</label>
                                    <input type="text" name="sec_6_i_total_members" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>सक्रिय कुल सदस्यों की संख्या</label>
                                    <input type="text" name="sec_6_i_active_members" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>निष्क्रिय कुल सदस्यों की संख्या</label>
                                    <input type="text" name="sec_6_i_inactive_members" class="form-control">
                                </div>
                            </div>

                            <h5>(II) बनाये गये नए सदस्यों की संख्या—</h5>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label>1 अप्रैल, 2024 से बनाए गए नए सदस्यों की संख्या</label>
                                    <input type="text" name="sec_6_ii_new_members" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>1 अप्रैल, 2024 से प्राप्त अंशधन</label>
                                    <input type="text" name="sec_6_ii_share_capital" class="form-control">
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>1 अप्रैल, 2024 से निष्क्रिय से सक्रिय किये गये सदस्यों की संख्या</label>
                                    <input type="text" name="sec_6_ii_activated_members" class="form-control">
                                </div>
                            </div>

                            <h5>(III) कुल सदस्य—</h5>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <input type="text" name="sec_6_iii_total_members_summary" class="form-control">
                                </div>
                            </div>

                            <h5>(IV) कुल प्राप्त अंश धन—</h5>
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <input type="text" name="sec_6_iv_total_share_capital" class="form-control">
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td width="5%">(v)</td>
                                            <td width="45%">(a) शाखा प्रतिनिधि का निर्वाचन हुआ है या नहीं ?</td>
                                            <td width="50%">
                                                <select name="sec_6_v_election_held" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <option value="yes">हाँ</option>
                                                    <option value="no">नहीं</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>(b) निर्वाचन की तिथि</td>
                                            <td><input type="date" name="sec_6_v_election_date" class="form-control">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>(c) निर्वाचित शाखा प्रतिनिधि का नाम व पता—</td>
                                            <td><textarea name="sec_6_v_rep_details" class="form-control"
                                                    rows="2"></textarea></td>
                                        </tr>

                                        <tr>
                                            <td>(vi)</td>
                                            <td>(a) शाखा पर शाखा प्रबन्ध समिति का गठन हुआ है अथवा नहीं?</td>
                                            <td>
                                                <select name="sec_6_vi_committee_formed" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <option value="yes">हाँ</option>
                                                    <option value="no">नहीं</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>(b) शाखा प्रबन्ध समिति के मनोनीत सदस्यों के नाम व पता</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>(i)</td>
                                            <td><input type="text" name="sec_6_vi_member_1" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>(ii)</td>
                                            <td><input type="text" name="sec_6_vi_member_2" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>(iii)</td>
                                            <td><input type="text" name="sec_6_vi_member_3" class="form-control"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- <h3>Hierarchy Tree Output</h3>
                                                    <div id="output" class="tree-output"></div> -->

                        </div>
                    </div>
                    <!--------------------------------------------------------------->

                    <!--------------------------------------------------------------->
                    <div class="step">
                        <h4><img src="images/logo/3.png" class="img-fluid stat-icon" style="height:50px; width:50px;">
                            3(I) वित्तीय सूचना</h4>
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <label>संतुलन पत्र किस वित्तीय वर्ष तक बना है</label>
                                    <select name="sec_3_santulan_patra" id="sec_3_santulan_patra" class="form-control"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_1']) && $row_3_new_1['profit_loss_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_1" id="sec_3_profit_loss_amount_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.I वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_1']) ? $row_3_new_1['profit_loss_amount_1'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_1" id="sec_3_accumulated_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_1']) && $row_3_new_1['accumulated_1'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_1" id="sec_3_accumulated_amount_1"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_2']) && $row_3_new_1['profit_loss_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_2" id="sec_3_profit_loss_amount_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.II वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_2']) ? $row_3_new_1['profit_loss_amount_2'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_2" id="sec_3_accumulated_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_2']) && $row_3_new_1['accumulated_2'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_2" id="sec_3_accumulated_amount_2"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                                        <option value="profit" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['profit_loss_3']) && $row_3_new_1['profit_loss_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_profit_loss_amount_3" id="sec_3_profit_loss_amount_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
                                        data-type="7.III वित्तीय सूचना को धनराशि रु० लाख मे भरे"
                                        value="<?php echo isset($row_3_new_1['profit_loss_amount_3']) ? $row_3_new_1['profit_loss_amount_3'] : ''; ?>">
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>संचित लाभ/हानि की स्थिति</label>
                                    <select name="sec_3_accumulated_3" id="sec_3_accumulated_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                        onchange="handleDropdownColorChange(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                        <option value="">--Select--</option>
                                        <option value="profit" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'profit' ? 'selected="selected"' : '' ?>>लाभ
                                        </option>
                                        <option value="loss" <?php echo isset($row_3_new_1['accumulated_3']) && $row_3_new_1['accumulated_3'] == 'loss' ? 'selected="selected"' : '' ?>>हानि
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-3 form-group">
                                    <label>(धनराशि लाख मे)</label>
                                    <input type="text" name="sec_3_accumulated_amount_3" id="sec_3_accumulated_amount_3"
                                        tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
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
                    </div>



                    <div class="step">
                        <!-- <h4><img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> 5. सुविधाएं </h4> -->
                        <h5>मण्डल के गत पाँच वर्षों के व्यावसायिक क्रिया कलापो का विवरण-</h5>
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>क०सं०</th>
                                            <th>वित्तीय वर्ष</th>
                                            <th>ऋण वितरण<br>(दिनांक 1 अप्रैल से 31 मार्च)</th>
                                            <th>वसूली<br>(दिनांक 1 जुलाई से 30 जून)</th>
                                            <th>NPA की स्थिति<br>31 मार्च को NPA %</th>
                                        </tr>
                                    </thead>
                                    <tbody id="financial_table_body">
                                        <tr>
                                            <td>1</td>
                                            <td>2019-20</td>
                                            <td><input type="text" name="loan_dist_1" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_1" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_1" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>2020-21</td>
                                            <td><input type="text" name="loan_dist_2" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_2" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_2" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>2021-22</td>
                                            <td><input type="text" name="loan_dist_3" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_3" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_3" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>2022-23</td>
                                            <td><input type="text" name="loan_dist_4" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_4" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_4" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>2023-24</td>
                                            <td><input type="text" name="loan_dist_5" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="recovery_5" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                            <td><input type="text" name="npa_5" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="text-right">
                                    <button type="button" class="btn btn-primary" onclick="addFinancialRow()"><i
                                            class="fa fa-plus"></i> Add Row</button>
                                </div>
                            </div>
                        </div>
                        <h4><img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                style="height:50px; width:50px;"> 4. सुविधाएं </h4>
                        <h5>(i) बिजली कनेक्शन</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>बिजली की उपलब्धता</label>
                                <select name="sec_8_electricity_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>

                        <h5>(ii) सोलर कनेक्शन</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>सोलर कनेक्शन</label>
                                <select name="sec_8_solar_connection" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>

                        <h5>(iii) कम्प्यूटराइजेशन की प्रगति</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>कम्प्यूटर की उपलब्धता</label>
                                <select name="sec_8_computer_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-4 form-group">
                                <label>प्रिंटर की उपलब्धता</label>
                                <select name="sec_8_printer_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-4 form-group">
                                <label>अन्य</label>
                                <input type="text" name="sec_8_other_computer_equipment" class="form-control">
                            </div>
                        </div>

                        <h5>(iv) इंटरनेट कनेक्शन की उपलब्धता</h5>
                        <div class="row">
                            <div class="col-sm-4 form-group">
                                <label>इंटरनेट कनेक्शन की उपलब्धता</label>
                                <select name="sec_8_internet_availability" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>

                        <h5>(v) पेयजल की उपलब्धता</h5>
                        <div class="row">
                            <div class="col-sm-3 form-group">
                                <label>सरकारी नल की सुविधा</label>
                                <select name="sec_8_govt_tap_facility" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>सबमर्सिबल पम्प</label>
                                <select name="sec_8_submersible_pump" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>पानी की टंकी</label>
                                <select name="sec_8_water_tank_facility" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>हैण्ड पम्प</label>
                                <select name="sec_8_hand_pump_facility" class="form-control"
                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                    <option value="">--select--</option>
                                    <option value="yes">हाँ</option>
                                    <option value="no">नहीं</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!---------------7th Start---------------------------------------------------------------->

                    <div class="step">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Latitude</label>
                                    <input type="text" id="lat" value="<?php echo $row_invoice['latitude']; ?>"
                                        class="form-control">
                                    <label>Longitude</label>
                                    <input type="text" id="long" value="<?php echo $row_invoice['longitude']; ?>"
                                        class="form-control">
                                    <button type="button" class="btn btn-info" onClick="getLocation();">मुख्यालय की
                                        जियो-लोकेशन</button>
                                </div>
                                <div class="col-md-10" id="map_container">
                                    <iframe id="googlemap"
                                        src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                        width="100%" height="100%" style="border: 1px solid; border-radius: 10px;"
                                        allowfullscreen="" loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                                </div>
                            </div>
                        </div>

                        <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                style="height:45px; width:45px;"> 13. सहकारी प्रशिक्षण केंद्र का विवरण</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td width="20%"><b>नाम-सहकारी प्रशिक्षण केन्द्र</b></td>
                                                <td width="30%"><input type="text" name="sec_13_training_center_name"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td width="10%"><b>पता</b></td>
                                                <td width="40%"><input type="text" name="sec_13_address"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>प्रधानाचार्य नाम</b></td>
                                                <td><input type="text" name="sec_13_principal_name" class="form-control"
                                                        tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>मूलपद</b></td>
                                                <td><input type="text" name="sec_13_original_post" class="form-control"
                                                        tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>प्रधानाचार्य के पद पर योगदान की तिथि</b></td>
                                                <td><input type="date" name="sec_13_principal_joining_date"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td><b>प्रधानाचार्य कार्यालय तैनात कार्मिको की संख्या</b></td>
                                                <td><input type="number" name="sec_13_staff_count" class="form-control"
                                                        tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>संख्या</b></td>
                                                <td><input type="number" name="sec_13_count" class="form-control"
                                                        tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>कक्ष संख्या</b></td>
                                                <td><input type="number" name="sec_13_room_count" class="form-control"
                                                        tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>क्षमता</b></td>
                                                <td><input type="number" name="sec_13_room_capacity"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>हॉस्टल संख्या</b></td>
                                                <td><input type="number" name="sec_13_hostel_count" class="form-control"
                                                        tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>क्षमता</b></td>
                                                <td><input type="number" name="sec_13_hostel_capacity"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>पुस्तकालय संख्या</b></td>
                                                <td><input type="number" name="sec_13_library_count"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>क्षमता</b></td>
                                                <td><input type="number" name="sec_13_library_capacity"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>कम्प्यूटर लैब संख्या</b></td>
                                                <td><input type="number" name="sec_13_computer_lab_count"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>क्षमता</b></td>
                                                <td><input type="number" name="sec_13_computer_lab_capacity"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>संकाय सदस्यों की संख्या</b></td>
                                                <td><input type="number" name="sec_13_faculty_count"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td><b>प्रशिक्षण सत्रों की संख्या</b></td>
                                                <td><input type="number" name="sec_13_training_session_count"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>प्रशिक्षण विषय के नाम</b></td>
                                                <td><input type="text" name="sec_13_training_subjects"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>प्रशिक्षण सत्र अवधि</b></td>
                                                <td><input type="text" name="sec_13_training_duration"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td><b>प्रशिक्षार्थियों की संख्या</b></td>
                                                <td><input type="number" name="sec_13_trainees_count"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td><b>विभागीय प्रशिक्षार्थियों की संख्या</b></td>
                                                <td><input type="number" name="sec_13_dept_trainees_count"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>गैर विभागीय प्रशिक्षार्थियों की संख्या</b></td>
                                                <td><input type="number" name="sec_13_non_dept_trainees_count"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>विभागीय प्रशिक्षण शुल्क</b></td>
                                                <td><input type="number" name="sec_13_dept_training_fee"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>गैर विभागीय प्रशिक्षण शुल्क</b></td>
                                                <td><input type="number" name="sec_13_non_dept_training_fee"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>विभागीय हॉस्टल शुल्क</b></td>
                                                <td><input type="number" name="sec_13_dept_hostel_fee"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>गैर विभागीय हॉस्टल शुल्क</b></td>
                                                <td><input type="number" name="sec_13_non_dept_hostel_fee"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td><b>निर्माण वर्ष</b></td>
                                                <td><input type="number" name="sec_13_construction_year"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><b>संचालन वर्ष</b></td>
                                                <td><input type="number" name="sec_13_operation_year"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                <input type="hidden" id="id" name="id" value="submit_form_ldb">
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
    function add_more_business(val) {
        var id = parseFloat($("#other_business_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 0; i <= id; i++) {
            if ($("#sec_2_1_2_business_description_" + i).val() == '' || $("#sec_2_1_2_value_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_2_1_2_business_description_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#add_business_row").remove();
        var txt = '<div class="row"><div class="col-sm-3 form-group"><label>व्यवसाय का विवरण </label><input type="text" name="sec_2_1_2_business_description_' + id + '" id="sec_2_1_2_business_description_' + id + '" class="form-control"></div><div class="col-sm-3 form-group"><label>वार्षिक टर्नोवर </label><input type="text" name="sec_2_1_2_value_' + id + '" id="sec_2_1_2_value_' + id + '" class="form-control"></div><div class="col-sm-2 form-group my-auto" id="add_business_row"><button type="button" class="btn btn-info" onclick="add_more_business();">नईं पंक्ति जोड़े [+]</button><input type="hidden" name="other_business_id" id="other_business_id" value="' + id + '"></div></div>';
        $("#other_business").append(txt);
    }

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

    function sec_3_b_godown_add_rows() {
        var id = parseFloat($("#sec_3_b_godown_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 0; i <= id; i++) {
            if ($("#sec_3_b_godown_length_" + i).val() == '' || $("#sec_3_b_godown_width_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_3_b_godown_length_" + i).focus();
                return;
            }
        }
        id = id + 1;
        var fund_options = $("#sec_3_b_godown_type_of_fund_1").html();
        $("#sec_3_b_godown_rows").remove();

        var txt = '<div class="row"><div class="col-sm-2 form-group"><label>लंबाई (मीटर में)</label><input type="text" name="sec_3_b_godown_length_' + id + '" id="sec_3_b_godown_length_' + id + '" tabindex="" class="form-control" value=""></div>	<div class="col-sm-2 form-group"><label>चौड़ाई (मीटर में)</label><input type="text" name="sec_3_b_godown_width_' + id + '" id="sec_3_b_godown_width_' + id + '" tabindex="" class="form-control" value=""></div><div class="col-sm-2 form-group"><label>क्षमता (मेट्रिक टन में)</label><input type="text" name="sec_3_b_storage_capacity_' + id + '" id="sec_3_b_storage_capacity_' + id + '" tabindex="" class="form-control" value=""></div><div class="col-sm-2 form-group"><label>किस फण्ड से बना है</label><select name="sec_3_b_godown_type_of_fund_' + id + '" id="sec_3_b_godown_type_of_fund_' + id + '" class="form-control">' + fund_options + '</select></div><div class="col-sm-2 form-group"><label>गोदाम के निर्माण कि स्थिति</label><select name="sec_3_b_godown_status_' + id + '" id="sec_3_b_godown_status_' + id + '" tabindex="" class="form-control"><option value="">--select-- </option><option value="good">अच्छा</option><option value="repairable">खराब/मरम्मत योग्य</option><option value="discarded">जर्जर/निषप्रयोज्य</option></select></div><div class="col-sm-1 form-group"><label>टिप्पणी</label><input type="text" name="sec_3_b_godown_comment_' + id + '" id="sec_3_b_godown_comment_' + id + '" tabindex="" class="form-control" value=""></div><div class="col-sm-1 form-group my-auto" id="sec_3_b_godown_rows"><button type="button" class="btn btn-info" onclick="sec_3_b_godown_add_rows()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_3_b_godown_id" id="sec_3_b_godown_id" value="' + id + '"></div></div>';
        $("#sec_3_b_godown").append(txt);
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

        var txt = '<div class="row"><div class="col-sm-2 form-group"><label>क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="sec_3_c_length_' + id + '" id="sec_3_c_length_' + id + '" lass="form-control"></div>	<div class="col-sm-2 form-group"><label>भूमि की स्थिति (उपजाऊ /बंजर)</label><select name="sec_3_c_vacant_land_status_' + id + '" id="sec_3_c_vacant_land_status_' + id + '" class="form-control"><option value="">--select-- </option><option value="उपजाऊ">उपजाऊ </option><option value="बंजर">बंजर </option></select></div><div class="col-sm-2 form-group"><label>स्थान (समिति प्रांगण या अन्य स्थान)*</label><select name="sec_3_c_land_location_' + id + '" id="sec_3_c_land_location_' + id + '" class="form-control" onChange="hide_show(this.value, \'#land_connectivity' + id + '\', \'other\'); hide_show(this.value, \'#land_access_road' + id + '\', \'na\');"><option value="">--select-- </option><option value="inpremise">समिति प्रांगण </option><option value="other">अन्य स्थान </option></select></div><div class="col-sm-2 form-group" id="land_connectivity' + id + '" style="display: none;"><label>संपर्क मार्ग*</label><select name="sec_3_c_approach_road_' + id + '" id="sec_3_c_approach_road_' + id + '" class="form-control" onChange="hide_show(this.value, \'#land_access_road' + id + '\', \'proper\');"><option value="">--select-- </option><option value="ordinary">कच्ची सड़क </option><option value="proper">पक्की सड़क </option></select></div><div class="col-sm-2 form-group" id="land_access_road' + id + '" style="display: none;"><label>पक्की सड़क का प्रकार</label><select name="sec_3_c_paved_road_' + id + '" id="sec_3_c_paved_road_' + id + '" class="form-control"><option value="">--select-- </option><option value="nh">नेशनल हाईवे</option><option value="sh">स्टेट हाईवे</option><option value="mdr">एम.डी.आर.</option><option value="odr">ओ.डी.आर.</option><option value="rural_road">ग्रामीण सड़क</option><option value="other">अन्य</option></select></div><div class="col-sm-2 form-group my-auto" id="sec_3_c_rows"><button type="button" class="btn btn-info" onClick="sec_3_c_add_rows();">नई पंक्ति जोड़े</button><input type="hidden" name="sec_3_c_id" id="sec_3_c_id" value="' + id + '"></div></div>';

        $("#sec_3_c").append(txt);
    }

    function msc_services_other1() {
        var disp = 0;
        var msc_value = $("#sec_1_1_2_msc_service").val();
        //console.log(msc_value);
        $.each(msc_value, function (key, value) {
            if (value == 'other') {
                $("#msc_services_other").show();
                disp = 1;
            }
        });
        if (disp == 0) {
            $("#msc_services_other").hide();
        }
    }
    function msc_services_other() {
        var disp = 0;
        var msc_value = $("#sec_1_1_2_msc_service").val();
        //console.log(msc_value);
        $.each(msc_value, function (key, value) {
            if (value == 'other') {
                $("#msc_services_other").show();
                disp = 1;
            }
        });
        if (disp == 0) {
            $("#msc_services_other").hide();
        }
    }

    $('select[multiple]').multiselect({
        columns: 1,
        placeholder: 'Select options'
    });
</script>
<script>

    $(document).ready(function () {
        //getLocation();
    });
</script>

<script>
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
</script>

<script>
    function color_change(selectElement, yesValue, yesColor, noValue, noColor) {
        if (selectElement.value === yesValue) {
            selectElement.style.backgroundColor = yesColor;
        } else if (selectElement.value === noValue) {
            selectElement.style.backgroundColor = noColor;
        } else {
            selectElement.style.backgroundColor = 'white'; // Default background color
        }
    }
    function toggleFields(value) {
        const loanDetails = document.getElementById('loan_details');
        if (value === 'yes') {
            loanDetails.style.display = 'block';
        } else {
            loanDetails.style.display = 'none';
        }
    }
</script>

<script>
    function addRow() {
        var id = parseFloat($("#sec_3_row_count").val());
        if (!id) {
            id = 0;
        }
        for (var i = 0; i <= id; i++) {
            if ($("#sec_3_cpmt_" + i).val() == '' || $("#sec_3_post_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_3_cpmt_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#sec_3_add_rows").remove();

        var txt = '<div class="row" id="row_' + id + '"><div class="col-sm-4"><label>नाम:-सहकारी प्रबंध प्रशिक्षण केंद्र</label><input name="sec_3_cpmt_' + id + '" id="sec_3_cpmt_' + id + '" class="form-control"></div><div class="col-sm-4"><label>पता</label><input name="sec_3_address_' + id + '" id="sec_3_address_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>पदेन प्रधानाचार्य नाम</label><input name="sec_3_principal_name_' + id + '" id="sec_3_principal_name_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>मूलपद</label><input name="sec_3_post_' + id + '" id="sec_3_post_' + id + '" class="form-control" ></div></div>	<div class="row"><div class="col-sm-4"><label>प्रधानाचार्य आवास</label><select name="sec_3_principal_house_' + id + '" id="sec_3_principal_house_' + id + '" class="form-control"onChange="hide_show(this.value, '#sec_3_building_rent', 'yes');"><option value="">--select-- </option><option value="">हाँ </option><option value="">नहीं </option></select></div><div class="col-sm-4"><label>संख्या</label><input name="sec_3_principal_house_no_' + id + '" id="sec_3_principal_house_no_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>प्रधानाचार्य कार्यालय</label><select name="sec_3_principal_office_' + id + '" id="sec_3_principal_office_' + id + '"class="form-control"onChange="hide_show(this.value, '#sec_3_building_rent', 'yes');"><option value="">--select-- </option><option value="">हाँ </option><option value="">नहीं </option></select></div><div class="col-sm-4"><label>संख्या</label><input name="sec_3_principal_office_no_' + id + '" id="sec_3_principal_office_no_' + id + '" class="form-control" ></div></div>	<div class="row"><div class="col-sm-4"><label>कक्षा संख्या</label><input name="sec_3_class_no_' + id + '" id="sec_3_class_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_class_capacity_' + id + '" id="sec_3_class_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>हॉस्टल संख्या</label><input name="sec_3_hostel_no_' + id + '" id="sec_3_hostel_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_hostel_capacity_' + id + '" id="sec_3_hostel_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>पुस्तकालय संख्या</label><input name="sec_3_library_no_' + id + '" id="sec_3_library_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_library_capacity_' + id + '" id="sec_3_library_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>कंप्युटर लैब संख्या</label><input name="sec_3_computer_lab_no_' + id + '" id="sec_3_computer_lab_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_computer_lab_capacity_' + id + '" id="sec_3_computer_lab_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>अध्यापक / अतिथि प्रवक्ता संख्या</label><input name="sec_3_teacher_no_' + id + '" id="sec_3_teacher_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>कर्मचारी विवरण</label><textarea name="sec_3_employee_remarks_' + id + '" id="sec_3_employee_remarks_' + id + '" class="form-control"></textarea></div></div><div class="row"><div class="col-sm-4"><label>प्रशिक्षण सत्रों की संख्या</label><input name="sec_3_training_sessions_no_' + id + '" id="sec_3_training_sessions_no_' + id + '" class="form-control"></div><div class="col-sm-4"><label>प्रशिक्षण विषय के नाम </label><input name="sec_3_training_subject_name_' + id + '" id="sec_3_training_subject_name_' + id + '" class="form-control"></div><div class="col-sm-3"><label>प्रशिक्षण सत्र अवधि</label><input type="date" name="sec_3_training_sessions_duration_' + id + '" id="sec_3_training_sessions_duration_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-4"><label>विभागीय प्रशिक्षार्थियों की संख्या</label><input name="sec_3_departmental_trainees_no_' + id + '" id="sec_3_departmental_trainees_no_' + id + '" class="form-control"></div><div class="col-sm-4"><label>गैर-विभागीय प्रशिक्षार्थियों की संख्या</label><input name="sec_3_non_departmental_trainees_no_' + id + '" id="sec_3_non_departmental_trainees_no_' + id + '" class="form-control"></div><div class="col-sm-4"><label>प्रशिक्षार्थियों की संख्या</label><input name="sec_3_trainees_no_' + id + '" id="sec_3_trainees_no_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-4"><label>विभागीय प्रशिक्षण शुल्क</label><input name="sec_3_departmental_trainees_fee_' + id + '" id="sec_3_departmental_trainees_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>गैर-विभागीय प्रशिक्षण शुल्क</label><input name="sec_3_non_departmental_trainees_fee_' + id + '" id="sec_3_non_departmental_trainees_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>प्रशिक्षण शुल्क</label><input name="sec_3_trainees_fee_' + id + '" id="sec_3_trainees_fee_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-4"><label>विभागीय हॉस्टल शुल्क</label><input name="sec_3_departmental_hostel_fee_' + id + '" id="sec_3_departmental_hostel_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>गैर-विभागीय हॉस्टल शुल्क</label><input name="sec_3_non_departmental_hostel_fee_' + id + '" id="sec_3_non_departmental_hostel_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>हॉस्टल शुल्क</label><input name="sec_3_hostel_fee_' + id + '" id="sec_3_hostel_fee_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-3"><label>निर्माण वर्ष</label><select name="sec2_santulan_patra" class="form-control"><option value="">--Select--</option><option value="">2024</option><option value="">2023</option><option value="">2022</option><option value="">2021</option><option value="">2020</option><option value="">2019</option><option value="">2018</option></select></div><div class="col-sm-3"><label>संचालन वर्ष</label><select name="sec2_santulan_patra" class="form-control"><option value="">--Select--</option><option value="">2024</option><option value="">2023</option><option value="">2022</option><option value="">2021</option><option value="">2020</option><option value="">2019</option><option value="">2018</option></select></div><div class="col-sm-3"><label>प्रशिक्षण कोर्स लाभ</label><textarea name="sec_3_training_course_benefits_' + id + '" id="sec_3_training_course_benefits_' + id + '" class="form-control"></textarea></div><div class="col-sm-3"><label>भवन/हॉस्टल स्तिथि</label><textarea name="sec_3_building_hostel_status_' + id + '" id="sec_3_building_hostel_status_' + id + '" class="form-control"></textarea></div></div><div class="col-sm-2 form-group my-auto" id="sec_3_add_rows"><button type="button" class="btn btn-info" onClick="addRow()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_3_row_count" id="sec_3_row_count" value="' + id + '"></div></div>';
        $("#sec_3_b").append(txt);
    }
</script>
<script>
    function handleCountInput(input, type) {
        const count = parseInt(input.value);
        if (count > 11) {
            alert('You cannot add more than 11 entries.');
            input.value = '';
            return;
        }

        const tableBody = document.getElementById('postTableBody');
        const existingRows = document.querySelectorAll(`.${type}_dynamic`);

        // Remove existing rows for the post type
        existingRows.forEach(row => tableBody.removeChild(row));

        // Remove the existing header if it exists
        const existingHeader = document.getElementById(`${type}_header`);
        if (existingHeader) {
            tableBody.removeChild(existingHeader);
        }

        // Add the header for नाम, रिक्त पद, स्वीकृत पद
        const headerRow = document.createElement('tr');
        headerRow.id = `${type}_header`;
        headerRow.innerHTML = `
                                            <th style="text-align: center;">पद</th>
                                            <th style="text-align: center;">नाम</th>
                                            <th style="text-align: center;">स्वीकृत पद</th>
                                            <th style="text-align: center;">रिक्त पद</th>
                                        `;
        tableBody.insertBefore(headerRow, input.parentNode.parentNode.nextSibling);

        // Add new rows based on the count
        for (let i = 0; i < count; i++) {
            const row = document.createElement('tr');
            row.className = `${type}_dynamic`;
            row.innerHTML = `
                                                <td>${type.toUpperCase()} ${i + 1}</td>
                                                <td><input type="text" name="name_${type}[]"></td>
                                                <td><input type="text" name="vacant_${type}[]"></td>
                                                <td><input type="text" name="sanctioned_${type}[]"></td>
                                            `;
            tableBody.insertBefore(row, input.parentNode.parentNode.nextSibling);
        }
    }

    function showTree() {
        const nameMd = document.getElementById('name_md').value;
        const mobileMd = document.getElementById('mobile_md').value;
        const educationMd = document.getElementById('education_md').value;

        const nameAmd = document.getElementById('name_amd').value;
        const mobileAmd = document.getElementById('mobile_amd').value;
        const educationAmd = document.getElementById('education_amd').value;

        const nameCgm = document.getElementById('name_cgm').value;
        const mobileCgm = document.getElementById('mobile_cgm').value;
        const educationCgm = document.getElementById('education_cgm').value;

        const gmNames = document.getElementsByName('name_gm[]');
        const gmVacant = document.getElementsByName('vacant_gm[]');
        const gmSanctioned = document.getElementsByName('sanctioned_gm[]');

        const dgmNames = document.getElementsByName('name_dgm[]');
        const dgmVacant = document.getElementsByName('vacant_dgm[]');
        const dgmSanctioned = document.getElementsByName('sanctioned_dgm[]');

        const agmNames = document.getElementsByName('name_agm[]');
        const agmVacant = document.getElementsByName('vacant_agm[]');
        const agmSanctioned = document.getElementsByName('sanctioned_agm[]');

        let outputHtml = `
                                            <ul>
                                                <li>प्रबंध निदेशक- नाम:${nameMd}, Mobile: ${mobileMd}, Education: ${educationMd}</li>
                                                <li>उप-प्रबंध निदेशक- नाम:${nameAmd}, Mobile: ${mobileAmd}, Education: ${educationAmd}</li>
                                                <li>मुख्य महाप्रबंधक- नाम:${nameCgm}, Mobile: ${mobileCgm}, Education: ${educationCgm}</li>
                                                <li>महाप्रबंधक:
                                                    <ul>
                                        `;

        for (let i = 0; i < gmNames.length; i++) {
            outputHtml += `<li>नाम: ${gmNames[i].value}, रिक्त पद: ${gmVacant[i].value}, स्वीकृत पद: ${gmSanctioned[i].value}</li>`;
        }

        outputHtml += `
                                                    </ul>
                                                </li>
                                                <li>उप-महाप्रबंधक:
                                                    <ul>
                                        `;

        for (let i = 0; i < dgmNames.length; i++) {
            outputHtml += `<li>नाम: ${dgmNames[i].value}, रिक्त पद: ${dgmVacant[i].value}, स्वीकृत पद: ${dgmSanctioned[i].value}</li>`;
        }

        outputHtml += `
                                                    </ul>
                                                </li>
                                                <li>सहायक महाप्रबंधक:
                                                    <ul>
                                        `;

        for (let i = 0; i < agmNames.length; i++) {
            outputHtml += `<li>नाम: ${agmNames[i].value}, रिक्त पद: ${agmVacant[i].value}, स्वीकृत पद: ${agmSanctioned[i].value}</li>`;
        }

        outputHtml += `
                                                    </ul>
                                                </li>
                                            </ul>
                                        `;

        document.getElementById('output').innerHTML = outputHtml;
    }

</script>


<script type="text/javascript" src="js/multistepform_ldb.js?v=5">
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    < script src="js/light-bootstrap-dashboard.js?v=1.4.0">
</script>
<script>
    function addFinancialRow() {
                    var tableBody = document.getElementById("financial_table_body");
    var update_index = tableBody.rows.length + 1;

    // Get last row year
    var lastRow = tableBody.rows[tableBody.rows.length - 1];
    var lastYearText = lastRow.cells[1].innerText.trim();
    var lastYearParts = lastYearText.split('-');
    var startYear = parseInt(lastYearParts[0]);
    var endYear = parseInt(lastYearParts[1]);

    var nextStartYear = startYear + 1;
    var nextEndYear = endYear + 1;
    var nextYearStr = nextStartYear + '-' + nextEndYear;

    var newRow = `
    <tr>
        <td>${update_index}</td>
        <td>${nextYearStr}</td>
        <td><input type="text" name="loan_dist_${update_index}" class="form-control"></td>
        <td><input type="text" name="recovery_${update_index}" class="form-control"></td>
        <td><input type="text" name="npa_${update_index}" class="form-control"></td>
    </tr>
                    `;

                    tableBody.insertAdjacentHTML('beforeend', newRow);
                }
</script>
<?php
page_footer_start();
?>