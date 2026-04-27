<?php
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;
// error_reporting(E_ALL);
// ini_set('display_errors', 0);

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

    $row_member_list = [
        'count' => 1,
        'member_mandal_1' => '',
        'member_district_1' => '',
        'member_tehsil_1' => '',
        'member_block_1' => '',
        'member_type_1' => '',
        'member_name_1' => ''
    ];

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

// Fetch Districts
$districts = [];
$sql_dist = "SELECT * FROM master_district ORDER BY district_name ASC";
$res_dist = execute_query($sql_dist);
if ($res_dist && mysqli_num_rows($res_dist) > 0) {
    while ($row_dist = mysqli_fetch_assoc($res_dist)) {
        $districts[] = $row_dist;
    }
}
?>

<?php
page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<style>
    /* Override the min-height from multistepform.css to remove gap */
    #steps-container {
        min-height: 0 !important;
    }
</style>
<script src="js/survey_validate.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .table>thead>tr>th {
        border-bottom-width: 1px;
        font-size: 0.9rem;
        /* Increased from 0.75rem */
        text-transform: uppercase;
        color: blue;
        font-weight: 700;
        /* Bold */
        padding-bottom: 5px;
        padding-top: 5px;
        text-align: center;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
        margin-bottom: 0.3rem;
    }

    .gap-bottom {
        margin-bottom: 12px;
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
        color: white;
        background: #4a90e2;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
        font-weight: 700;
        /* Bold */
        font-size: 1.5rem;
        /* Increased size */
    }

    .step h5 {
        color: blue !important;
        background: #a4cbf8ff;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
        font-weight: 700;
        /* Bold */
        font-size: 1.25rem;
        /* Increased size */
    }

    .select-default {
        background-color: white;
    }

    .card label,
    .form-group label {
        font-size: 1.1rem;
        /* Increased size */
        font-weight: 700;
        /* Bold */
        color: #333;
    }

    .form-control {
        font-size: 1rem;
        /* Increased size */
        font-weight: 500;
        height: auto;
        /* Allow height to adjust */
        min-height: 40px;
    }

    #q-box__buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
        margin-bottom: 20px;
    }


    /* Matrix table input alignment */
    #business_matrix_table td {
        vertical-align: middle;
    }

    #business_matrix_table .form-control {
        height: 40px;
        /* Increased height */
        font-size: 1rem;
        /* Increased size */
    }

    /* Work Done compact layout */
    .work-done-wrap {
        display: flex;
        gap: 8px;
    }

    .work-done-col {
        flex: 1;
    }

    .work-done-col label {
        font-size: 0.9rem;
        /* Increased size */
        font-weight: 700;
        /* Bold */
        margin-bottom: 2px;
        display: block;
        color: #444;
    }

    #business_matrix_table td.text-center {
        padding: 25px;
        /* Remove extra padding */
    }

    #business_matrix_table td.text-center button {
        height: 100%;
        /* Full height of the td */
        width: 100%;
        /* Optional: button fills the cell width */
        border-radius: 0.5rem;
        /* Rounded edges to match inputs */
    }

    .table-striped thead {
        background-color: #b8daff;
        color: #ffffff;
    }

    .img-fluid {
        max-width: 100%;
        height: auto;
        margin-right: 0.5rem;
    }
</style>

<style>
    .highlight-text {
        padding: 8px 10px;
        background: #d7eee3;
        border: 1px solid #eee8d5;
        border-radius: 4px;
        font-weight: bold;
        color: #2c3e50;
    }

    .blinking-text {
        color: red;
        font-size: 13px;
        font-weight: bold;
        animation: blink 1s infinite;
    }

    @keyframes blink {
        50% {
            opacity: 0;
        }
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
                                <!-- <div class="step"> -->
                                    <div>
                                        <?php echo $msg; ?>
                                    </div>
                                    <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                            style="height:45px; width:45px;"> 1. समिति का विवरण </h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="row">

                                                    <div class="col-sm-12 form-group">
                                                        <label>संस्था का प्रकार</label>
                                                        <div class="highlight-text" style="font-size: 18px;">
                                                            शीर्ष सहकारी संस्था (APEX)
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-12 form-group" style="padding: 15px;">
                                                        <label>समिति का नाम</label>
                                                        <div class="highlight-text" style="font-size: 18px;">
                                                            उ०प्र० सहकारी ग्राम विकास बैंक लि०
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Right Section : Location + Map -->
                                            <div class="col-md-8">
                                                <input type="hidden" id="apex_code" name="apex_code"
                                                    value="<?php echo $row_invoice['apex_id']; ?>">
                                                <input type="hidden" id="survey_id" name="survey_id"
                                                    value="<?php echo $row_invoice['sno']; ?>">

                                                <div class="row">
                                                    <!-- Lat Long + Button -->
                                                    <div class="col-md-3">
                                                        <label>Latitude</label>
                                                        <input type="text" id="lat" disabled
                                                            value="<?php echo $row_invoice['latitude']; ?>"
                                                            class="form-control">

                                                        <label>Longitude</label>
                                                        <input type="text" id="long" disabled
                                                            value="<?php echo $row_invoice['longitude']; ?>"
                                                            class="form-control">

                                                        <button type="button" class="btn btn-info btn-sm mt-2"
                                                            onclick="getLocation();">
                                                            लोकेशन रिफ्रेश करें
                                                        </button>

                                                        <div class="blinking-text mt-1">
                                                            (मुख्यालय का लोकेशन मोबाईल से भरे)*
                                                        </div>
                                                    </div>

                                                    <!-- Map -->
                                                    <div class="col-md-9" id="map_container">
                                                        <iframe id="googlemap"
                                                            src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&output=embed"
                                                            width="100%" height="230"
                                                            style="border:1px solid; border-radius:10px;"
                                                            loading="lazy">
                                                        </iframe>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>संस्था का पंजीकरण संख्या</label>
                                            <input type="text" name="society_registration_no"
                                                id="society_registration_no" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['society_registration_no'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>संस्था का पंजीकरण दिनांक</label>
                                            <input type="date" name="society_registration_date"
                                                id="society_registration_date" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['society_registration_date'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>पैन न०</label>
                                            <input type="text" name="pan_no" id="pan_no" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['pan_no'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>टैन न०</label>
                                            <input type="text" name="tan_no" id="tan_no" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['tan_no'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>जी०एस०टी०एन० न०</label>
                                            <input type="text" name="gstin_no" id="gstin_no" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['gstin_no'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>ई-मेल आई.डी.</label>
                                            <input type="text" name="email_id" id="email_id" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['email_id'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>दूरभाष न०</label>
                                            <input type="text" name="phone_no" id="phone_no" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['phone_no'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>वेबसाइट</label>
                                            <input type="text" name="website" id="website" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['website'] ?? ''); ?>"
                                                tabindex="<?php echo $tab++; ?>">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>मुख्यालय का स्वामित्व</label>
                                            <select name="hq_ownership" id="hq_ownership" class="form-control"
                                                tabindex="<?php echo $tab++; ?>">
                                                <option value="">--Select--</option>
                                                <option value="Own" <?php if (($row_invoice['hq_ownership'] ?? '') == 'Own')
                                                    echo 'selected'; ?>>स्वयं का (Own)</option>
                                                <option value="Rented" <?php if (($row_invoice['hq_ownership'] ?? '') == 'Rented')
                                                    echo 'selected'; ?>>किराये का (Rented)</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>मुख्यालय की फोटो संलग्न करें</label>
                                            <input type="file" name="society_photo" id="society_photo"
                                                class="form-control" tabindex="<?php echo $tab++; ?>">
                                            <?php if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) { ?>
                                                <div class="mt-2">
                                                    <a href="<?php echo $row_invoice['photo_id']; ?>" target="_blank"
                                                        class="text-primary">View Uploaded Photo</a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <h5>
                                        <img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">1.1शीर्ष संस्था के कार्यालय
                                    </h5>
                                    <br>

                                    <div class="card shadow-sm mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <label>क्षेत्रीय कार्यालय की संख्या</label>
                                                    <input type="text" name="no_of_zones" id="no_of_zones"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        value="<?php echo htmlspecialchars($row_invoice['no_of_zones'] ?? ''); ?>"
                                                        oninput="updateOfficeRows(this.value)">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>जनपदीय शाखा की संख्या</label>
                                                    <input type="text" id="global_prakhand_count" class="form-control"
                                                        oninput="updateSeparatePrakhandRows(this.value)">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label> शाखा कार्यालय की संख्या</label>
                                                    <input type="text" id="global_branch_office_count"
                                                        class="form-control"
                                                        oninput="updateBranchOfficeRows(this.value)">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label> प्रशिक्षण केंद्र की संख्या</label>
                                                    <input type="text" id="global_training_center_count"
                                                        class="form-control"
                                                        oninput="updateTrainingCenterRows(this.value)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive" id="zoneTableWrapper" style="display:none;">
                                        <h6 class="text-primary mb-2">क्षेत्रीय कार्यालय का विवरण</h6>
                                        <table class="table table-bordered" id="officeContainer"
                                            style="width: 100%; table-layout: fixed;">
                                            <thead>
                                                <tr class="office-block-header bg-light">
                                                    <th width="15%" style="color: black; font-weight: bold;">क्षेत्रीय
                                                        का नाम</th>
                                                    <th width="15%" style="color: black; font-weight: bold;">क्षेत्रीय
                                                        का दूरभाष न०</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">क्षेत्रीय
                                                        का ई-मेल आई.डी.</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">क्षेत्रीय
                                                        का पता</th>
                                                    <th width="100%" style="color: black; font-weight: bold;">क्षेत्रीय
                                                        कार्यालय की फोटो GPS टैग के साथ संलग्न करे</th>
                                                </tr>
                                            </thead>
                                            <tbody class="office-block" data-zone-index="1"
                                                style="border-top: 2px solid #dee2e6;">
                                                <tr>
                                                    <td style="padding: 5px;">
                                                        <input type="text" name="zone_name[]"
                                                            class="form-control zone-name"
                                                            placeholder="क्षेत्रीय का नाम">
                                                    </td>
                                                    <td style="padding: 5px;">
                                                        <input type="text" name="zone_mobile[]"
                                                            class="form-control zone-mobile"
                                                            placeholder="क्षेत्रीय का दूरभाष">
                                                    </td>
                                                    <td style="padding: 5px;">
                                                        <input type="text" name="zone_email[]"
                                                            class="form-control zone-email"
                                                            placeholder="क्षेत्रीय का ई-मेल">
                                                    </td>
                                                    <td style="padding: 5px;">
                                                        <input type="text" name="zone_address[]"
                                                            class="form-control zone-address"
                                                            placeholder="क्षेत्रीय का पता">
                                                    </td>
                                                    <td style="padding: 5px;">
                                                        <input type="file" name="zone_image[]" class="form-control">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-responsive" id="prakhandTableWrapper"
                                        style="display:none; margin-top: 15px;">
                                        <h6 class="text-primary mb-2">जनपदीय शाखा का विवरण</h6>
                                        <table class="table table-bordered" id="prakhandContainer"
                                            style="width: 100%; table-layout: fixed;">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th width="15%" style="color: black; font-weight: bold;">जनपदीय शाखा
                                                        का नाम</th>
                                                    <th width="15%" style="color: black; font-weight: bold;">जनपदीय शाखा
                                                        का दूरभाष न०</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">जनपदीय शाखा
                                                        का ई-मेल आई.डी.</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">जनपदीय शाखा
                                                        का पता</th>
                                                    <th width="100%" style="color: black; font-weight: bold;">जनपदीय
                                                        शाखा की फोटो GPS टैग के साथ संलग्न करे</th>
                                                </tr>
                                            </thead>
                                            <tbody id="prakhand-main-tbody">
                                                <tr class="prakhand-row-template">
                                                    <td><input type="text" name="prakhand_name[]" class="form-control"
                                                            placeholder="जनपदीय शाखा का नाम"></td>
                                                    <td><input type="text" name="prakhand_mobile[]" class="form-control"
                                                            placeholder="जनपदीय शाखा का दूरभाष"></td>
                                                    <td><input type="text" name="prakhand_email[]" class="form-control"
                                                            placeholder="जनपदीय शाखा का ई-मेल"></td>
                                                    <td><input type="text" name="prakhand_address[]"
                                                            class="form-control" placeholder="जनपदीय शाखा का पता"></td>
                                                    <td><input type="file" name="prakhand_image[]" class="form-control">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive" id="branchOfficeTableWrapper"
                                        style="display:none; margin-top: 15px;">
                                        <h6 class="text-primary mb-2">शाखा कार्यालय का विवरण</h6>
                                        <table class="table table-bordered" id="branchOfficeContainer"
                                            style="width: 100%; table-layout: fixed;">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th width="15%" style="color: black; font-weight: bold;">शाखा
                                                        कार्यालय का नाम</th>
                                                    <th width="15%" style="color: black; font-weight: bold;">शाखा
                                                        कार्यालय का दूरभाष न०</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">शाखा
                                                        कार्यालय का ई-मेल आई.डी.</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">शाखा
                                                        कार्यालय का पता</th>
                                                    <th width="100%" style="color: black; font-weight: bold;">शाखा
                                                        कार्यालय की फोटो GPS टैग के साथ संलग्न करे</th>
                                                </tr>
                                            </thead>
                                            <tbody id="branch-office-main-tbody">
                                                <tr class="branch-office-row-template">
                                                    <td><input type="text" name="branch_office_name[]"
                                                            class="form-control" placeholder="शाखा कार्यालय का नाम">
                                                    </td>
                                                    <td><input type="text" name="branch_office_mobile[]"
                                                            class="form-control" placeholder="शाखा कार्यालय का दूरभाष">
                                                    </td>
                                                    <td><input type="text" name="branch_office_email[]"
                                                            class="form-control" placeholder="शाखा कार्यालय का ई-मेल">
                                                    </td>
                                                    <td><input type="text" name="branch_office_address[]"
                                                            class="form-control" placeholder="शाखा कार्यालय का पता">
                                                    </td>
                                                    <td><input type="file" name="branch_office_image[]"
                                                            class="form-control"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive" id="trainingCenterTableWrapper"
                                        style="display:none; margin-top: 15px;">
                                        <h6 class="text-primary mb-2">प्रशिक्षण केंद्र का विवरण</h6>
                                        <table class="table table-bordered" id="trainingCenterContainer"
                                            style="width: 100%; table-layout: fixed;">
                                            <thead>
                                                <tr class="bg-light">
                                                    <th width="15%" style="color: black; font-weight: bold;">प्रशिक्षण
                                                        केंद्र का नाम</th>
                                                    <th width="15%" style="color: black; font-weight: bold;">प्रशिक्षण
                                                        केंद्र का दूरभाष न०</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">प्रशिक्षण
                                                        केंद्र का ई-मेल आई.डी.</th>
                                                    <th width="20%" style="color: black; font-weight: bold;">प्रशिक्षण
                                                        केंद्र का पता</th>
                                                    <th width="100%" style="color: black; font-weight: bold;">प्रशिक्षण
                                                        केंद्र की फोटो GPS टैग के साथ संलग्न करे</th>
                                                </tr>
                                            </thead>
                                            <tbody id="training-center-main-tbody">
                                                <tr class="training-center-row-template">
                                                    <td><input type="text" name="training_center_name[]"
                                                            class="form-control" placeholder="प्रशिक्षण केंद्र का नाम">
                                                    </td>
                                                    <td><input type="text" name="training_center_mobile[]"
                                                            class="form-control"
                                                            placeholder="प्रशिक्षण केंद्र का दूरभाष"></td>
                                                    <td><input type="text" name="training_center_email[]"
                                                            class="form-control"
                                                            placeholder="प्रशिक्षण केंद्र का ई-मेल"></td>
                                                    <td><input type="text" name="training_center_address[]"
                                                            class="form-control" placeholder="प्रशिक्षण केंद्र का पता">
                                                    </td>
                                                    <td><input type="file" name="training_center_image[]"
                                                            class="form-control"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <h5> <img src="images/logo/2.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">1.2. सामान्य
                                        निकाय </h5><br>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>(I) व्यक्तिगत सदस्यों की संख्या </label>
                                            <input type="text" name="members_no" id="members_no"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['members_no']); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>(II) सदस्य समितियो कि संख्या</label>
                                            <input type="text" name="members_no" id="members_no"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['members_no']); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>(III) केंद्रीय समितियो की संख्या</label>
                                            <input type="text" name="members_no" id="members_no"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['members_no']); ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>(IV) प्राथमिक समितियो की संख्या</label>
                                            <input type="text" name="members_no" id="members_no"
                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                value="<?php echo htmlspecialchars($row_invoice['members_no']); ?>">
                                        </div>
                                    </div>
                                <!-- </div> -->
                                <!----------------2.1 start-------------------------------------------------------->
                                <!-- <div class="step"> -->
                                    <h4><img src="images/logo/3.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        2. वित्तीय सूचना</h4>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="financialMatrixTable">
                                                <thead>
                                                    <tr>
                                                        <th style="color: #000;">वर्ष</th>
                                                        <th style="color: #000;">प्रकार</th>
                                                        <th style="color: #000;">स्थिति</th>
                                                        <th style="color: #000;">सकल लाभ/हानि धनराशि<br>(लाख में)</th>
                                                        <th style="color: #000;">शुद्ध लाभ/हानि धनराशि<br>(लाख में)</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <!-- Starting 3 rows -->
                                                    <?php
                                                    $startYear = 2022;
                                                    for ($i = 0; $i < 3; $i++) {
                                                        $yearLabel = $startYear + $i . '-' . substr(($startYear + $i + 1), -2);
                                                        // Determine data keys based on index
                                                        // Existing logic uses suffixes 1, 2, 3
                                                        $suffix = $i + 1;
                                                        ?>
                                                        <tr>
                                                            <td rowspan="2"><?= $yearLabel ?></td>
                                                            <td>वार्षिक लाभ/हानि</td>
                                                            <td>
                                                                <select name="sec_3_profit_loss_<?= $suffix ?>"
                                                                    class="form-control"
                                                                    onchange="handleDropdownColorChange(this,'profit','#42ecf5','loss','#f28546');">
                                                                    <option value="">--Select--</option>
                                                                    <option value="profit" <?php echo isset($row_3_new_1['sec_3_profit_loss_' . $suffix]) && $row_3_new_1['sec_3_profit_loss_' . $suffix] == 'profit' ? 'selected' : ''; ?>>लाभ
                                                                    </option>
                                                                    <option value="loss" <?php echo isset($row_3_new_1['sec_3_profit_loss_' . $suffix]) && $row_3_new_1['sec_3_profit_loss_' . $suffix] == 'loss' ? 'selected' : ''; ?>>हानि</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" name="sec_3_gross_amount_<?= $suffix ?>"
                                                                    value="<?php echo isset($row_3_new_1['sec_3_profit_loss_amount_' . $suffix]) ? $row_3_new_1['sec_3_profit_loss_amount_' . $suffix] : ''; ?>"
                                                                    class="form-control chk_decimal"></td>
                                                            <td><input type="text" name="sec_3_net_amount_<?= $suffix ?>"
                                                                    value="<?php echo isset($row_3_new_1['sec_3_net_amount_' . $suffix]) ? $row_3_new_1['sec_3_net_amount_' . $suffix] : ''; ?>"
                                                                    class="form-control chk_decimal"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>संचित लाभ/हानि</td>
                                                            <td>
                                                                <select name="sec_3_accumulated_<?= $suffix ?>"
                                                                    class="form-control"
                                                                    onchange="handleDropdownColorChange(this,'profit','#42ecf5','loss','#f28546');">
                                                                    <option value="">--Select--</option>
                                                                    <option value="profit" <?php echo isset($row_3_new_1['sec_3_accumulated_' . $suffix]) && $row_3_new_1['sec_3_accumulated_' . $suffix] == 'profit' ? 'selected' : ''; ?>>लाभ
                                                                    </option>
                                                                    <option value="loss" <?php echo isset($row_3_new_1['sec_3_accumulated_' . $suffix]) && $row_3_new_1['sec_3_accumulated_' . $suffix] == 'loss' ? 'selected' : ''; ?>>हानि</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text"
                                                                    name="sec_3_acc_gross_amount_<?= $suffix ?>"
                                                                    value="<?php echo isset($row_3_new_1['sec_3_accumulated_amount_' . $suffix]) ? $row_3_new_1['sec_3_accumulated_amount_' . $suffix] : ''; ?>"
                                                                    class="form-control chk_decimal"></td>
                                                            <td><input type="text"
                                                                    name="sec_3_acc_net_amount_<?= $suffix ?>"
                                                                    value="<?php echo isset($row_3_new_1['sec_3_acc_net_amount_' . $suffix]) ? $row_3_new_1['sec_3_acc_net_amount_' . $suffix] : ''; ?>"
                                                                    class="form-control chk_decimal"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mb-2 text-right">
                                            <button type="button" class="btn btn-info" id="addYearRowBtn">
                                                नई पंक्ति जोड़ें [+]
                                            </button>
                                        </div>

                                        

                                    </div>
                                    <h4> <img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">2.1. समिति के गत पाँच वर्षों के व्यावसायिक
                                        किया कलापों का
                                        विवरण-</h4>
                                    <div class="col-sm-12">


                                        <table class="table table-bordered table-striped table-hover">
                                            <tbody>
                                                <tr>
                                                    <th rowspan="2" style="text-align: center;">क्र०स०</th>
                                                    <th rowspan="2" style="text-align: center;">वित्तीय वर्ष</th>
                                                    <th rowspan="2" style="text-align: center;">ऋण वितरण (दिनांक 1
                                                        अप्रैल से 31
                                                        मार्च)</th>
                                                    <th rowspan="2" style="text-align: center;">वसूली (दिनांक 1 जुलाई से
                                                        30 जून)
                                                    </th>
                                                    <th rowspan="2" style="text-align: center;">सावधि जमा योजनान्तर्गत
                                                        निक्षेपित
                                                        (दिनांक 1 अप्रैल से 31 मार्च)</th>
                                                    <th colspan="2" style="text-align: center;">NPA की स्थिति 31 मार्च
                                                        को</th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: center;">Gross NPA %</th>
                                                    <th style="text-align: center;">Net NPA %</th>
                                                </tr>



                                                <tr>
                                                    <td>1</td>
                                                    <td>2020-21
                                                    </td>
                                                    <td><input type="text" name="desktop_2023" class="form-control">
                                                    </td>
                                                    <td><input type="text" name="desktop_value_2023"
                                                            class="form-control">
                                                    </td>
                                                    <td><input type="text" name="desktop_4" class="form-control"></td>
                                                    <td><input type="text" name="desktop_value_4_gross"
                                                            class="form-control"></td>
                                                    <td><input type="text" name="desktop_value_4_net"
                                                            class="form-control"></td>

                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>2021-22</td>
                                                    <td><input type="text" name="printer_2023" class="form-control">
                                                    </td>
                                                    <td><input type="text" name="printer_value_2023"
                                                            class="form-control">
                                                    </td>
                                                    <td><input type="text" name="printer_4" class="form-control"></td>
                                                    <td><input type="text" name="printer_value_4_gross"
                                                            class="form-control"></td>
                                                    <td><input type="text" name="printer_value_4_net"
                                                            class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>3</td>
                                                    <td>2022-23</td>
                                                    <td><input type="text" name="printer_2023" class="form-control">
                                                    </td>
                                                    <td><input type="text" name="printer_value_2023"
                                                            class="form-control">
                                                    </td>
                                                    <td><input type="text" name="printer_4" class="form-control"></td>
                                                    <td><input type="text" name="printer_value_4_gross"
                                                            class="form-control"></td>
                                                    <td><input type="text" name="printer_value_4_net"
                                                            class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>4</td>
                                                    <td>2023-24</td>
                                                    <td><input type="text" name="camera_2023" class="form-control"></td>
                                                    <td><input type="text" name="camera_value_2023"
                                                            class="form-control">
                                                    </td>
                                                    <td><input type="text" name="camera_4" class="form-control"></td>
                                                    <td><input type="text" name="camera_value_4_gross"
                                                            class="form-control"></td>
                                                    <td><input type="text" name="camera_value_4_net"
                                                            class="form-control"></td>
                                                </tr>
                                                <tr>
                                                    <td>5</td>
                                                    <td>2024-25</td>
                                                    <td><input type="text" name="ups_2023" class="form-control"></td>
                                                    <td><input type="text" name="ups_value_2023" class="form-control">
                                                    </td>
                                                    <td><input type="text" name="ups_4" class="form-control"></td>
                                                    <td><input type="text" name="ups_value_4_gross"
                                                            class="form-control"></td>
                                                    <td><input type="text" name="ups_value_4_net" class="form-control">
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                                                        <div class="row">
                                        <div class="col-sm-12">
                                            <h3
                                                style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                                आडिट</h3>
                                        </div>
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
											<div class="col-sm-3 form-group">
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

											<div class="col-sm-3 form-group">
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
											<div class="col-sm-3 form-group">
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
											<div class="col-sm-3 form-group" id="sec_2_dividend_per"
												style="display:none">
												<label>लाभांश का प्रतिशत (0-20 तक)</label>
												<input type="text" name="sec_3_dividend_per" id="sec_3_dividend_per"
													tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
													data-type="7.III लाभांश को प्रतिशत मे भरे"
													value="<?php echo $row_3_new_1['sec_3_dividend_per']; ?>">
											</div>
											<div class="col-sm-3 form-group" id="sec_2_dividend" style="display:none">
												<label>लाभांश की धनराशि (लाख मे)</label>
												<input type="text" name="sec_3_dividend_amt" id="sec_3_dividend_amt"
													tabindex="<?php echo $tab++; ?>" class="form-control chk_decimal"
													data-type="7.III लाभांश को धनराशि रु० लाख मे भरे"
													value="<?php echo $row_3_new_1['sec_3_dividend_amt']; ?>">
											</div>
                                            <div class="col-sm-3 form-group">
												<label>विधानसभा के पटल पर ऑडिट रिपोर्ट प्रस्तुत किये जाने का वर्ष:</label>
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
										</div>
                                    <!-- </div> -->
                                <!------ Manav Sampada start ------->
                                <!-- <div class="step"> -->
                                    <h4>
                                        <img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        3. मानव सम्पदा
                                    </h4>

                                    <h5 class="mt-3">स्टाफ (Staff)</h5>

                                    <?php
                                    // Fetch posts for the dropdown
                                    $posts = [];
                                    $sql_posts = "SELECT * FROM master_posts_apex_1 ORDER BY post_name ASC";
                                    $result_posts = execute_query($sql_posts);
                                    if ($result_posts && mysqli_num_rows($result_posts) > 0) {
                                        while ($row_p = mysqli_fetch_assoc($result_posts)) {
                                            $posts[] = $row_p;
                                        }
                                    }

                                    // Update the JS variable for dynamic rows with post type
                                    $postOptionsHTML = '';
                                    foreach ($posts as $p) {
                                        // Check 'technical' column: 'T' = tech, NULL/empty = nontech
                                        $post_type = (isset($p['technical']) && $p['technical'] === 'T') ? 'tech' : 'nontech';
                                        $postOptionsHTML .= '<option value="' . $p['sno'] . '" data-type="' . $post_type . '">' . htmlspecialchars($p['post_name']) . '</option>';
                                    }
                                    ?>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="human_resource_table">
                                            <thead class="table-light" style="background-color: #e9ecef;">
                                                <tr>
                                                    <th style="width: 25%; color: #000;">कर्मचारी प्रकार</th>
                                                    <th style="width: 25%; color: #000;">पद</th>
                                                    <th style="width: 20%; color: #000;">स्वीकृत पद</th>
                                                    <th style="width: 20%; color: #000;">रिक्त पद</th>
                                                    <th style="width: 10%; color: #000; white-space: nowrap;">Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="human_resource_rows">
                                                <tr class="human_row">
                                                    <td>
                                                        <select name="staff_type[]"
                                                            class="form-control staff-type-select"
                                                            onchange="filterPostsByType(this)">
                                                            <option value="">--Select--</option>
                                                            <option value="tech">Technical</option>
                                                            <option value="nontech">Non-Technical</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="post_id[]" class="form-control post-select"
                                                            onchange="updateStaffSection(this)">
                                                            <option value="">--Select--</option>
                                                            <?php foreach ($posts as $p) {
                                                                // Check 'technical' column: 'T' = tech, NULL/empty = nontech
                                                                $post_type = (isset($p['technical']) && $p['technical'] === 'T') ? 'tech' : 'nontech';
                                                                ?>
                                                            <option value="<?php echo $p['sno']; ?>"
                                                                data-type="<?php echo $post_type; ?>">
                                                                <?php echo htmlspecialchars($p['post_name']); ?>
                                                            </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="sanctioned_post[]"
                                                            class="form-control" onchange="updateStaffSection(this)">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="vacant_post[]" class="form-control">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-info btn-sm"
                                                            onclick="addHumanResourceRow();">+</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- Staff Section (Dynamic) -->
                                    <div id="staff_section" style="display:none;" class="mt-3">
                                        <h5>कर्मचारी विवरण</h5>
                                        <div id="staff_rows"></div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-primary btn-sm me-3"
                                                onclick="uploadDocument()" style="margin-right:1rem;">Upload
                                                Document</button>
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="downloadExcel()" style="height: 40px;">Download Excel</button>
                                        </div>
                                    </div>

                                    <script>
                                        function uploadDocument() {
                                            alert('Upload Document functionality can be implemented here!');
                                        }

                                        function downloadExcel() {
                                            alert('Download Excel functionality can be implemented here!');
                                        }
                                    </script>


                                    <!-- ===== STAFF ROW TEMPLATE ===== -->
                                    <div id="staff_row_template" style="display:none;">
                                        <div class="staff_row border p-3 mb-3 rounded">
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <label>पद</label>
                                                    <select name="staff_post_name[]"
                                                        class="form-control staff_post_name">
                                                        <option value="">--Select--</option>
                                                        <?php foreach ($posts as $p) { ?>
                                                            <option value="<?php echo $p['sno']; ?>">
                                                                <?php echo htmlspecialchars($p['post_name']); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>नाम</label>
                                                    <input type="text" name="staff_name[]" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>स्थिति</label>
                                                    <input type="text" name="staff_sthiti[]" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>पिता का नाम</label>
                                                    <input type="text" name="staff_father[]" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <label>जन्म तिथि</label>
                                                    <input type="date" name="staff_dob[]" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>मोबाइल नंबर</label>
                                                    <input type="text" name="staff_mobile[]" class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>शैक्षिक योग्यता</label>
                                                    <input type="text" name="staff_qualification[]"
                                                        class="form-control">
                                                </div>
                                                <div class="col-md-3 form-group">
                                                    <label>Upload Image</label>
                                                    <input type="file" name="staff_image[]" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function updateStaffSection(elem) {
                                            let row = elem.closest('.human_row');
                                            let postSelect = row.querySelector('select[name="post_id[]"]');
                                            let sanctionedInput = row.querySelector('input[name="sanctioned_post[]"]');

                                            let postName = postSelect.options[postSelect.selectedIndex]?.text || '';
                                            let sanctioned = parseInt(sanctionedInput.value) || 0;

                                            let container = document.getElementById('staff_rows');
                                            container.innerHTML = '';

                                            if (postName && sanctioned > 0) {
                                                document.getElementById('staff_section').style.display = 'block';
                                                for (let i = 0; i < sanctioned; i++) {
                                                    let template = document.getElementById('staff_row_template').cloneNode(true);
                                                    template.style.display = 'block';
                                                    template.removeAttribute('id');
                                                    template.querySelector('.staff_post_name').value = postName;
                                                    container.appendChild(template);
                                                }
                                            } else {
                                                document.getElementById('staff_section').style.display = 'none';
                                            }
                                        }
                                    </script>

                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 3.1. मुख्यालय पर कार्यरत अधिकारियों का
                                        विवरण
                                    </h4>

                                    <div class="row">
                                        <div class="col-sm-12">
                                            <!-- <div id="sec_3_b" style="overflow-x: auto; width: 100%"> -->
                                            <table class="table table-bordered table-striped table-hover"
                                                id="officer_table">
                                                <thead>
                                                    <tr>
                                                        <th>क्र०स०</th>
                                                        <th style="width: 200px;">पद नाम</th>
                                                        <th>कार्यरत अधिकारी/कर्मचारी का नाम</th>
                                                        <th>कार्यालय में योगदान तिथि</th>
                                                        <th>संबंधित अनुभाग</th>

                                                    </tr>
                                                </thead>
                                                <tbody id="officer_table_body">
                                                    <?php
                                                    $officer_count = 5;
                                                    for ($i = 1; $i <= $officer_count; $i++): ?>
                                                        <tr id="officer_row_<?php echo $i; ?>">
                                                            <td><?php echo $i; ?></td>
                                                            <td>
                                                                <select name="officer_designation_<?php echo $i; ?>"
                                                                    id="officer_designation_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select--</option>
                                                                    <option value="MD">प्रबन्ध निदेशक </option>
                                                                    <option value="CGM">मुख्य महाप्रबन्धक</option>
                                                                    <option value="GM">महाप्रबन्धक </option>
                                                                    <option value="DGM">उप महाप्रबन्धक </option>
                                                                    <option value="AGM">सहायक महाप्रबन्धक</option>

                                                                </select>
                                                            </td>
                                                            <td><input type="text" name="officer_name_<?php echo $i; ?>"
                                                                    id="officer_name_<?php echo $i; ?>"
                                                                    class="form-control"></td>
                                                            <td><input type="date"
                                                                    name="officer_joining_date_<?php echo $i; ?>"
                                                                    id="officer_joining_date_<?php echo $i; ?>"
                                                                    class="form-control"></td>
                                                            <td><input type="text" name="officer_section_<?php echo $i; ?>"
                                                                    id="officer_section_<?php echo $i; ?>"
                                                                    class="form-control"></td>

                                                        </tr>
                                                    <?php endfor; ?>
                                                </tbody>
                                            </table>
                                            <!-- Button to add new row -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-info"
                                                        onClick="add_officer_row()">
                                                        नई पंक्ति जोड़े [+]
                                                    </button>
                                                    <input type="hidden" name="officer_count" id="officer_count"
                                                        value="<?php echo htmlspecialchars($officer_count); ?>">
                                                </div>
                                            </div>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                <!-- </div> -->
                                <!-- <div class="step"> -->
                                    <div class="col-sm-12">
                                        <h5>
                                            <img src="images/logo/3.png" class="img-fluid stat-icon"
                                                style="height:60px; width:60px;">
                                            4.. सहकारी प्रबंध प्रशिक्षण केंद्र
                                        </h5>
                                        <div id="sec_3_training_center">
                                            <?php
                                            $count = !empty($row_3_3['count']) ? $row_3_3['count'] : 1;
                                            for ($i = 1; $i <= $count; $i++) {
                                                ?>
                                                <div class="row sec-3-row" id="sec_3_row_<?php echo $i; ?>">
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>नाम :- सहकारी प्रबंध प्रशिक्षण केंद्र</label>
                                                                <input name="sec_3_cpmt_<?php echo $i; ?>"
                                                                    id="sec_3_cpmt_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_cpmt_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>पता</label>
                                                                <input name="sec_3_address_<?php echo $i; ?>"
                                                                    id="sec_3_address_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_address_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>पदेन प्रधानाचार्य नाम</label>
                                                                <input name="sec_3_principal_name_<?php echo $i; ?>"
                                                                    id="sec_3_principal_name_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_principal_name_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>मूलपद</label>
                                                                <input name="sec_3_post_<?php echo $i; ?>"
                                                                    id="sec_3_post_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_post_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रधानाचार्य आवास </label>
                                                                <select name="sec_3_principal_house_<?php echo $i; ?>"
                                                                    id="sec_3_principal_house_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    onchange="hide_show(this.value, '#sec_3_principal_house_no_box_<?php echo $i; ?>', 'yes');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes" <?php echo ($row_3_3['sec_3_principal_house_' . $i] == 'yes') ? 'selected' : ''; ?>>हाँ</option>
                                                                    <option value="no" <?php echo ($row_3_3['sec_3_principal_house_' . $i] == 'no') ? 'selected' : ''; ?>>नहीं</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3 form-group"
                                                                id="sec_3_principal_house_no_box_<?php echo $i; ?>"
                                                                style="<?php echo ($row_3_3['sec_3_principal_house_' . $i] == 'yes') ? '' : 'display:none'; ?>">
                                                                <label>संख्या</label>
                                                                <input name="sec_3_principal_house_no_<?php echo $i; ?>"
                                                                    id="sec_3_principal_house_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_principal_house_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रधानाचार्य कार्यालय </label>
                                                                <select name="sec_3_principal_office_<?php echo $i; ?>"
                                                                    id="sec_3_principal_office_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    onchange="hide_show(this.value, '#sec_3_principal_office_no_box_<?php echo $i; ?>', 'yes');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes" <?php echo ($row_3_3['sec_3_principal_office_' . $i] == 'yes') ? 'selected' : ''; ?>>हाँ</option>
                                                                    <option value="no" <?php echo ($row_3_3['sec_3_principal_office_' . $i] == 'no') ? 'selected' : ''; ?>>नहीं</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3 form-group"
                                                                id="sec_3_principal_office_no_box_<?php echo $i; ?>"
                                                                style="<?php echo ($row_3_3['sec_3_principal_office_' . $i] == 'yes') ? '' : 'display:none'; ?>">
                                                                <label>संख्या</label>
                                                                <input name="sec_3_principal_office_no_<?php echo $i; ?>"
                                                                    id="sec_3_principal_office_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_principal_office_no_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>क्लासरूम संख्या</label>
                                                                <input name="sec_3_class_no_<?php echo $i; ?>"
                                                                    id="sec_3_class_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_class_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_class_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_class_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_class_capacity_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>हॉस्टल संख्या</label>
                                                                <input name="sec_3_hostel_no_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_hostel_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_hostel_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_hostel_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>पुस्तकालय संख्या</label>
                                                                <input name="sec_3_library_no_<?php echo $i; ?>"
                                                                    id="sec_3_library_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_library_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_library_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_library_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_library_capacity_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>कंप्युटर लैब संख्या</label>
                                                                <input name="sec_3_computer_lab_no_<?php echo $i; ?>"
                                                                    id="sec_3_computer_lab_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_computer_lab_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_computer_lab_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_computer_lab_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_computer_lab_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>अध्यापक / अतिथि प्रवक्ता संख्या</label>
                                                                <input name="sec_3_teacher_no_<?php echo $i; ?>"
                                                                    id="sec_3_teacher_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_teacher_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रशिक्षण सत्रों की संख्या</label>
                                                                <input name="sec_3_training_sessions_no_<?php echo $i; ?>"
                                                                    id="sec_3_training_sessions_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_training_sessions_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रशिक्षण विषय के नाम</label>
                                                                <input name="sec_3_training_subject_name_<?php echo $i; ?>"
                                                                    id="sec_3_training_subject_name_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_training_subject_name_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रशिक्षण सत्र अवधि</label>
                                                                <input type="date"
                                                                    name="sec_3_training_sessions_duration_<?php echo $i; ?>"
                                                                    id="sec_3_training_sessions_duration_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_training_sessions_duration_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>कर्मचारी विवरण</label>
                                                                <textarea name="sec_3_employee_remarks_<?php echo $i; ?>"
                                                                    id="sec_3_employee_remarks_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    rows="1"><?php echo $row_3_3['sec_3_employee_remarks_' . $i]; ?></textarea>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>विभागीय प्रशिक्षार्थियों की संख्या</label>
                                                                <input
                                                                    name="sec_3_departmental_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_departmental_trainees_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>गैर-विभागीय प्रशिक्षार्थियों की संख्या</label>
                                                                <input
                                                                    name="sec_3_non_departmental_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_non_departmental_trainees_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रशिक्षार्थियों की संख्या </label>
                                                                <input name="sec_3_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_trainees_no_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input
                                                                    name="sec_3_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_departmental_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>गैर-विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input
                                                                    name="sec_3_non_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_non_departmental_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रशिक्षार्थी प्रशिक्षण शुल्क </label>
                                                                <input name="sec_3_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>विभागीय हॉस्टल शुल्क</label>
                                                                <input
                                                                    name="sec_3_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_departmental_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>गैर-विभागीय हॉस्टल शुल्क</label>
                                                                <input
                                                                    name="sec_3_non_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_non_departmental_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>हॉस्टल शुल्क </label>
                                                                <input name="sec_3_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>निर्माण वर्ष</label>
                                                                <select name="sec_3_build_year_<?php echo $i; ?>"
                                                                    id="sec_3_build_year_<?php echo $i; ?>"
                                                                    class="form-control" tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--Select--</option>
                                                                    <option value="1999" <?php echo ($row_3_3['sec_3_build_year_' . $i] == '1999') ? 'selected' : ''; ?>>2000 से पूर्व</option>
                                                                    <?php for ($y = 2000; $y <= 2024; $y++) { ?>
                                                                        <option value="<?php echo $y; ?>" <?php echo ($row_3_3['sec_3_build_year_' . $i] == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>संचालन वर्ष</label>
                                                                <select name="sec_3_operation_year_<?php echo $i; ?>"
                                                                    id="sec_3_operation_year_<?php echo $i; ?>"
                                                                    class="form-control" tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--Select--</option>
                                                                    <option value="1999" <?php echo ($row_3_3['sec_3_operation_year_' . $i] == '1999') ? 'selected' : ''; ?>>2000 से पूर्व</option>
                                                                    <?php for ($y = 2000; $y <= 2024; $y++) { ?>
                                                                        <option value="<?php echo $y; ?>" <?php echo ($row_3_3['sec_3_operation_year_' . $i] == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-3 form-group">
                                                                <label>सहकारी प्रबंध प्रशिक्षण केंद्र</label>
                                                                <select name="sec_3_training_center_<?php echo $i; ?>"
                                                                    id="sec_3_training_center_<?php echo $i; ?>"
                                                                    class="form-control" tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--Select--</option>
                                                                    <option value="meerut" <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'meerut') ? 'selected' : ''; ?>>मेरठ</option>
                                                                    <option value="varanasi" <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'varanasi') ? 'selected' : ''; ?>>वाराणसी
                                                                    </option>
                                                                    <option value="mahoba" <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'mahoba') ? 'selected' : ''; ?>>महोबा</option>
                                                                    <option value="hewra" <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'hewra') ? 'selected' : ''; ?>>हेवरा (ईटवा)</option>
                                                                    <option value="ayodhya" <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'ayodhya') ? 'selected' : ''; ?>>अयोध्या (फैजाबाद)</option>
                                                                    <option value="bilari" <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'bilari') ? 'selected' : ''; ?>>बिलारी (मोरादाबाद)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>कार्मिक की संख्या</label>
                                                                <select name="sec_3_staff_type_<?php echo $i; ?>"
                                                                    id="sec_3_staff_type_<?php echo $i; ?>"
                                                                    class="form-control" tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--select-- </option>
                                                                    <option value="union" <?php echo ($row_3_3['sec_3_staff_type_' . $i] == 'union') ? 'selected' : ''; ?>>उ० प्र० कोआपरेटिव यूनियन
                                                                    </option>
                                                                    <option value="authority" <?php echo ($row_3_3['sec_3_staff_type_' . $i] == 'authority') ? 'selected' : ''; ?>>सहकारी संघ प्राधिकारी</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>प्रशिक्षण कोर्स लाभ</label>
                                                                <textarea
                                                                    name="sec_3_training_course_benefits_<?php echo $i; ?>"
                                                                    id="sec_3_training_course_benefits_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    rows="1"><?php echo $row_3_3['sec_3_training_course_benefits_' . $i]; ?></textarea>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>भवन/हॉस्टल स्तिथि</label>
                                                                <textarea
                                                                    name="sec_3_building_hostel_status_<?php echo $i; ?>"
                                                                    id="sec_3_building_hostel_status_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    rows="1"><?php echo $row_3_3['sec_3_building_hostel_status_' . $i]; ?></textarea>
                                                            </div>
                                                        </div>

                                                        <?php if ($i == $count) { ?>
                                                            <div class="col-sm-2 form-group my-auto"
                                                                id="sec_3_add_rows_wrapper">
                                                                <button type="button" class="btn btn-info"
                                                                    onclick="sec_3_add_rows()">नई पंक्ति जोड़े [+]</button>
                                                                <input type="hidden" name="sec_3_row_count" id="sec_3_row_count"
                                                                    value="<?php echo $count; ?>">
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <!-- </div> -->
                                <!-- <div class="step"> -->
                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 5. संस्था भवन/सम्पत्ति का विवरण</h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h5>(I) संस्था भवन का स्वामित्व </h5>
                                                <div class="col-sm-3 form-group">
                                                    <label>(I)संस्था भवन का स्वामित्व </label>
                                                    <?php
                                                    $sec_3_display = 'none';
                                                    $sec_3_rented_display = 'none';
                                                    $sec_3_other_display = 'none';

                                                    if ($row_new_plot['sec_3_ownership'] === 'own') {
                                                        $sec_3_display = 'flex';
                                                    } elseif ($row_new_plot['sec_3_ownership'] === 'rent') {
                                                        $sec_3_rented_display = 'flex';
                                                    } elseif (!empty($row_new_plot['sec_3_ownership'])) {
                                                        $sec_3_other_display = 'flex';
                                                    }
                                                    ?>
                                                    <select name="sec_3_ownership" id="sec_3_ownership"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control"
                                                        onChange="hide_show(this.value, '#sec_3_rented', 'rent'); hide_show(this.value, '#sec_3_other', 'other'); hide_show(this.value, '#sec_3', 'own');">
                                                        <option value="">--Select--</option>
                                                        <option value="own" <?php
                                                        if ($row_new_plot['sec_3_ownership'] == 'own') {
                                                            echo ' selected="selected" ';
                                                        } ?>>स्वयं </option>
                                                        <option value="rent" <?php
                                                        if ($row_new_plot['sec_3_ownership'] == 'rent') {
                                                            echo ' selected="selected" ';
                                                        } ?>>
                                                            किराये पर </option>
                                                        <option value="other" <?php
                                                        if ($row_new_plot['sec_3_ownership'] != 'rent' && $row_new_plot['sec_3_ownership'] != 'own' && $row_new_plot['sec_3_ownership'] != '') {
                                                            echo ' selected="selected" ';
                                                        } ?>>
                                                            अन्य स्थिती</option>
                                                    </select>
                                                </div>
                                                <div id="sec_3_rented"
                                                    style="display: <?php echo $sec_3_rented_display; ?>">
                                                    <div class="col-sm-3 form-group">
                                                        <label>संस्था भवन का मासिक किराया </label>
                                                        <input name="sec_3_building_rent" id="sec_3_building_rent"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_new_plot['sec_3_building_rent']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>संस्था भवन का क्षेत्रफल (स्क्वायर मीटर में)</label>
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
                                                <h5> (II) संस्था के भूखंड का विवरण </h5>
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                        <input type="text" name="sec_new_plot_area"
                                                            id="sec_new_plot_area" tabindex="<?php echo $tab++; ?>"
                                                            class="form-control"
                                                            value="<?php echo $row_new_plot['sec_new_plot_area']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>राजस्व अभिलेख में दर्ज होने की स्थिति</label>
                                                        <select name="sec_new_plot_revenue_status"
                                                            id="sec_new_plot_revenue_status"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onChange="hide_show(this.value, '#sec_new_plot_reason', 'no'); hide_show(this.value, '#sec_new_plot_if_not', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                            <option value="">--Select--</option>
                                                            <option value="yes" <?php echo ($row_new_plot['sec_new_plot_revenue_status'] == 'yes') ? ' selected="selected"' : ''; ?>>हाँ</option>
                                                            <option value="no" <?php echo ($row_new_plot['sec_new_plot_revenue_status'] == 'no') ? ' selected="selected"' : ''; ?>>नहीं</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="sec_new_plot_reason"
                                                        style="display:none">
                                                        <label>दर्ज ना होने का कारण?</label>
                                                        <input type="text" name="sec_new_plot_reason_for_not_record"
                                                            id="sec_new_plot_reason_for_not_record"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_new_plot['sec_new_plot_reason_for_not_record']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group" id="sec_new_plot_if_not"
                                                        style="display:none">
                                                        <label>यदि नहीं है तो किये जाने वाले प्रयास</label>
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
                                                            id="sec_new_plot_gata_no" tabindex="<?php echo $tab++; ?>"
                                                            class="form-control"
                                                            value="<?php echo $row_new_plot['sec_new_plot_gata_no']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>टिप्पणी</label>
                                                        <input type="text" name="sec_new_remarks" id="sec_new_remarks"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
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
                                                        <label>भूखण्ड की पश्चिम दिशा का विवरण</label>
                                                        <input type="text" name="sec_3_a_land_chauhaddi_west"
                                                            id="sec_3_a_land_chauhaddi_west"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_3_1['west_side']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>भूखण्ड की उत्तर दिशा का विवरण</label>
                                                        <input type="text" name="sec_3_a_land_chauhaddi_north"
                                                            id="sec_3_a_land_chauhaddi_north"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_3_1['north_side']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>भूखण्ड की दक्षिण दिशा का विवरण</label>
                                                        <input type="text" name="sec_3_a_land_chauhaddi_south"
                                                            id="sec_3_a_land_chauhaddi_south"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo $row_3_1['south_side']; ?>">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
                                                        <label>सड़क पर भूमि कि लम्बाई (मीटर में)</label>
                                                        <input type="text" name="sec_3_a_land_on_road"
                                                            id="sec_3_a_land_on_road" tabindex="<?php echo $tab++; ?>"
                                                            class="form-control"
                                                            value="<?php echo $row_3_1['on_road_land']; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>प्रमुख द्वार कि दिशा (फ्र्न्ट साईड)</label>
                                                        <select name="sec_3_a_land_frontage" id="sec_3_a_land_frontage"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control">
                                                            <option>--Select--</option>
                                                            <option value="east" <?php echo ($row_3_1['front_side'] == 'east') ? 'selected' : ''; ?>>
                                                                पूर्व</option>
                                                            <option value="west" <?php echo ($row_3_1['front_side'] == 'west') ? 'selected' : ''; ?>>
                                                                पश्चिम</option>
                                                            <option value="north" <?php echo ($row_3_1['front_side'] == 'north') ? 'selected' : ''; ?>>
                                                                उत्तर</option>
                                                            <option value="south" <?php echo ($row_3_1['front_side'] == 'south') ? 'selected' : ''; ?>>
                                                                दक्षिण</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-sm-3 form-group">
                                                        <label>पहुंच मार्ग</label>
                                                        <select name="sec_6_access_road" id="sec_6_access_road"
                                                            class="form-control">
                                                            <option value="">--Select--</option>
                                                            <option value="proper" <?php echo ($row_2_1['sec_6_access_road'] == 'proper') ? 'selected' : ''; ?>>पक्की सडक</option>
                                                            <option value="ordinary" <?php echo ($row_2_1['sec_6_access_road'] == 'ordinary') ? 'selected' : ''; ?>>कच्ची सडक</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2 form-group">
                                                        <label>फ्र्ण्टेज्‌ मीटर में</label>
                                                        <input type="text" name="sec_8_plot_frontage"
                                                            id="sec_8_plot_frontage" class="form-control"
                                                            value="<?php echo $row_2_1['sec_8_plot_frontage']; ?>">
                                                    </div>
                                                </div>
                                                <h5>(V) खाली पड़ी भूमि का विवरण</h5>
                                                <div id="sec_3_c">
                                                    <?php for ($i = 1; $i <= $row_3_5['sec_3_c_id']; $i++) { ?>
                                                        <div class="row mb-2 sec3c_row">
                                                            <!-- District -->
                                                            <div class="col-sm-2 form-group">
                                                                <label>जनपद</label>
                                                                <select name="sec_3_c_district_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <option value="">--Select--</option>
                                                                    <?php foreach ($districts as $d) { ?>
                                                                        <option value="<?php echo $d['district_id']; ?>">
                                                                            <?php echo htmlspecialchars($d['district_name']); ?>
                                                                        </option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <!-- Area -->
                                                            <div class="col-sm-2 form-group">
                                                                <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                                <input type="text" name="sec_3_c_area_<?php echo $i; ?>"
                                                                    class="form-control">
                                                            </div>

                                                            <!-- Image Upload -->
                                                            <div class="col-sm-3 form-group">
                                                                <label>संस्था का फोटो GPS टैग के साथ संलग्न करे</label>
                                                                <input type="file" name="sec_3_c_image_<?php echo $i; ?>"
                                                                    class="form-control">
                                                            </div>

                                                            <!-- Add / Remove Button -->
                                                            <div class="col-sm-1 form-group my-auto">
                                                                <?php if ($i == $row_3_5['sec_3_c_id']) { ?>
                                                                    <button type="button" class="btn btn-info"
                                                                        onclick="sec_3_c_add_rows();">+</button>
                                                                <?php } else { ?>
                                                                    <button type="button" class="btn btn-danger"
                                                                        onclick="$(this).closest('.sec3c_row').remove();">-</button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <input type="hidden" name="sec_3_c_id" id="sec_3_c_id"
                                                        value="<?php echo $row_3_5['sec_3_c_id']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3 mb-3" id="q-box__buttons">
            <div class="col-md-12 text-center">
                <button id="prev-btn" class="btn btn-info" type="button" onClick="save_draft()">Previous</button>
                <button id="next-btn" class="btn btn-success" type="button" onClick="save_draft()">Next</button>
                <button id="submit-btn" class="btn btn-danger" type="submit" onClick="save_draft()">Submit</button>
            </div>
            <div class="col-md-12 text-center mt-3">
                <button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i> Save
                    Draft</button>
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
            <button type="button" class="btn btn-danger" onClick="form_validate();" id="verification_button"
                disabled="disabled">सत्यापन के लिये आगे प्रेषित
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
                    <button type="button" name="verify_otp_btn" id="verify_otp_btn" tabindex="<?php echo $tab++; ?>"
                        class="btn btn-info"
                        onClick="verify_otp($('#survey_id').val(), '', $('#user_otp').val());">वेरिफाई
                        करें</button>
                    <button type="button" name="send_otp_btn" id="send_otp_btn" tabindex="<?php echo $tab++; ?>"
                        class="btn btn-info" onClick="send_otp($('#survey_id').val(), '');">पुनः ओ.टी.पी.
                        भेजे</button>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="term" name="term" value="a">
<input type="hidden" id="latitude" name="latitude" value="<?php echo $row_invoice['latitude']; ?>">
<input type="hidden" id="longitude" name="longitude" value="<?php echo $row_invoice['longitude']; ?>">
<input type="hidden" id="id" name="id" value="submit_form_ldb">
<input type="hidden" id="current_step_count" name="current_step_count" value="">
<input type="hidden" id="survey_id" name="survey_id" value="<?php echo $row_invoice['sno']; ?>">
</form>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="otp_form"
    name="otp_form"></form>

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


    function add_member_row() {
        var countVal = $("#member_list_count").val();
        var count = parseInt(countVal);

        // Fallback if count is NaN (e.g. empty value)
        if (isNaN(count)) {
            count = $("#member_list_table tbody tr").length;
        }

        var newCount = count + 1;

        // Create the new row HTML
        var newRow = '<tr id="member_row_' + newCount + '">' +
            '<td>' + newCount + '</td>' +
            '<td><input type="text" name="member_mandal_' + newCount + '" class="form-control"></td>' +
            '<td><input type="text" name="member_district_' + newCount + '" class="form-control"></td>' +
            '<td><input type="text" name="member_tehsil_' + newCount + '" class="form-control"></td>' +
            '<td><input type="text" name="member_block_' + newCount + '" class="form-control"></td>' +
            '<td><input type="text" name="member_type_' + newCount + '" class="form-control"></td>' +
            '<td><input type="text" name="member_name_' + newCount + '" class="form-control"></td>' +
            '</tr>';

        // Append the new row to the table body
        $("#member_list_table tbody").append(newRow);

        // Update the count hidden field
        $("#member_list_count").val(newCount);
    }

</script>

<script>
    function updateOfficeRows(val) {
        var count = parseInt(val, 10);
        var container = document.getElementById('officeContainer');
        var wrapper = document.getElementById('zoneTableWrapper');
        if (!container || !wrapper) return;

        // Toggle visibility based on count
        if (isNaN(count) || count < 1) {
            wrapper.style.display = 'none';
            return;
        } else {
            wrapper.style.display = 'block';
        }

        var tbody = container.getElementsByClassName('office-block')[0];
        // Ensure we operate on rows inside the tbody
        var currentRows = tbody.getElementsByTagName('tr');
        if (currentRows.length === 0) return;

        var diff = count - currentRows.length;

        if (diff > 0) {
            var template = currentRows[0];
            for (var i = 0; i < diff; i++) {
                var clone = template.cloneNode(true);
                var inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(function (inp) {
                    if (inp.type === 'file') {
                        inp.value = '';
                    } else {
                        inp.value = '';
                    }
                    if (inp.tagName === 'SELECT') inp.selectedIndex = 0;
                });
                tbody.appendChild(clone);
            }
        } else if (diff < 0) {
            while (currentRows.length > count) {
                tbody.removeChild(currentRows[currentRows.length - 1]);
            }
        }
    }

    function updateSeparatePrakhandRows(val) {
        var count = parseInt(val, 10);
        var wrapper = document.getElementById('prakhandTableWrapper');
        var tbody = document.getElementById('prakhand-main-tbody');

        if (!wrapper || !tbody) return;

        if (isNaN(count) || count < 1) {
            wrapper.style.display = 'none';
            return;
        } else {
            wrapper.style.display = 'block';
        }

        var currentRows = tbody.getElementsByClassName('prakhand-row-template');
        var diff = count - currentRows.length;

        if (diff > 0) {
            var template = currentRows[0];
            for (var i = 0; i < diff; i++) {
                var clone = template.cloneNode(true);
                var inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(function (inp) {
                    if (inp.type === 'file') {
                        inp.value = '';
                    } else {
                        inp.value = '';
                    }
                });
                tbody.appendChild(clone);
            }
        } else if (diff < 0) {
            while (currentRows.length > count) {
                tbody.removeChild(currentRows[currentRows.length - 1]);
            }
        }
    }

    function updateBranchOfficeRows(val) {
        var count = parseInt(val, 10);
        var wrapper = document.getElementById('branchOfficeTableWrapper');
        var tbody = document.getElementById('branch-office-main-tbody');

        if (!wrapper || !tbody) return;

        if (isNaN(count) || count < 1) {
            wrapper.style.display = 'none';
            return;
        } else {
            wrapper.style.display = 'block';
        }

        var currentRows = tbody.getElementsByClassName('branch-office-row-template');
        var diff = count - currentRows.length;

        if (diff > 0) {
            var template = currentRows[0];
            for (var i = 0; i < diff; i++) {
                var clone = template.cloneNode(true);
                var inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(function (inp) {
                    if (inp.type === 'file') {
                        inp.value = '';
                    } else {
                        inp.value = '';
                    }
                });
                tbody.appendChild(clone);
            }
        } else if (diff < 0) {
            while (currentRows.length > count) {
                tbody.removeChild(currentRows[currentRows.length - 1]);
            }
        }
    }

    function updateTrainingCenterRows(val) {
        var count = parseInt(val, 10);
        var wrapper = document.getElementById('trainingCenterTableWrapper');
        var tbody = document.getElementById('training-center-main-tbody');

        if (!wrapper || !tbody) return;

        if (isNaN(count) || count < 1) {
            wrapper.style.display = 'none';
            return;
        } else {
            wrapper.style.display = 'block';
        }

        var currentRows = tbody.getElementsByClassName('training-center-row-template');
        var diff = count - currentRows.length;

        if (diff > 0) {
            var template = currentRows[0];
            for (var i = 0; i < diff; i++) {
                var clone = template.cloneNode(true);
                var inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(function (inp) {
                    if (inp.type === 'file') {
                        inp.value = '';
                    } else {
                        inp.value = '';
                    }
                });
                tbody.appendChild(clone);
            }
        } else if (diff < 0) {
            while (currentRows.length > count) {
                tbody.removeChild(currentRows[currentRows.length - 1]);
            }
        }
    }

    // If page loads with existing zone count, render blocks accordingly
    document.addEventListener('DOMContentLoaded', function () {
        var z = document.getElementById('no_of_zones');
        if (z && z.value) updateOfficeRows(z.value);


    });
</script>



<script type="text/javascript" src="js/multistepform_ldb.js?v=5">
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    < script src="js/light-bootstrap-dashboard.js?v=1.4.0">
</script>
<script>
    function add_officer_row() {
        var countField = document.getElementById('officer_count');
    var count = parseInt(countField.value);
    if (isNaN(count)) count = 0;

        // Validate last row before adding new one
        if(count > 0) {
            var lastDesignation = document.getElementById('officer_designation_' + count);
    var lastName = document.getElementById('officer_name_' + count);

    if (lastDesignation && lastDesignation.value === '') {

    }
        }

    var newId = count + 1;
    var tableBody = document.getElementById('officer_table_body');

    var designationOptions = `
    <option value="">--select--</option>
    <option value="MD">प्रबन्ध निदेशक </option>
    <option value="CGM">मुख्य महाप्रबन्धक</option>
    <option value="GM">महाप्रबन्धक </option>
    <option value="DGM">उप महाप्रबन्धक </option>
    <option value="AGM">सहायक महाप्रबन्धक</option>
    `;

    var rowHtml = `
    <tr id="officer_row_${newId}">
        <td>${newId}</td>
        <td>
            <select name="officer_designation_${newId}" id="officer_designation_${newId}" class="form-control">
                ${designationOptions}
            </select>
        </td>
        <td><input type="text" name="officer_name_${newId}" id="officer_name_${newId}" class="form-control"></td>
        <td><input type="date" name="officer_joining_date_${newId}" id="officer_joining_date_${newId}" class="form-control"></td>
        <td><input type="text" name="officer_section_${newId}" id="officer_section_${newId}" class="form-control"></td>
    </tr>
        `;

        // Append new row
        tableBody.insertAdjacentHTML('beforeend', rowHtml);

        // Update count
        countField.value = newId;
    }
</script>

<script>
function filterPostsByType(selectElement) {
    const row = selectElement.closest('tr');
    const postSelect = row.querySelector('.post-select');
    const selectedType = selectElement.value;

    if (!postSelect) return;

    // Get all options in the post select
    const allOptions = postSelect.querySelectorAll('option');

    // Show/hide options based on selected type
    allOptions.forEach(option => {
        if (option.value === '') {
            // Always show the "--Select--" option
            option.style.display = '';
            option.disabled = false;
        } else {
            const optionType = option.getAttribute('data-type');

            if (selectedType === '') {
                // If no type selected, show all
                option.style.display = '';
                option.disabled = false;
            } else if (selectedType === 'tech' && optionType === 'tech') {
                // Show technical posts
                option.style.display = '';
                option.disabled = false;
            } else if (selectedType === 'nontech' && optionType === 'nontech') {
                // Show non-technical posts
                option.style.display = '';
                option.disabled = false;
            } else {
                // Hide non-matching posts
                option.style.display = 'none';
                option.disabled = true;
            }
        }
    });

    // Reset post selection
    postSelect.selectedIndex = 0;

    // Also call updateStaffSection if it exists
    if (typeof updateStaffSection === 'function') {
        updateStaffSection(selectElement);
    }
}

function addHumanResourceRow() {
    let tbody = document.getElementById('human_resource_rows');
    let firstRow = tbody.querySelector('tr.human_row');
    let newRow = firstRow.cloneNode(true);

    // Clear all input values in the new row
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

    // Update onchange handler for staff type in new row
    const staffTypeSelect = newRow.querySelector('.staff-type-select');
    if (staffTypeSelect) {
        staffTypeSelect.onchange = function() { filterPostsByType(this); };
    }

    // Change the + button to - button
    let actionCell = newRow.querySelector('td:last-child');
    actionCell.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'tr\').remove();">-</button>';

    tbody.appendChild(newRow);
}
</script>


<script>
    let yearCount = 3; // Already 3 rows present

    document.getElementById("addYearRowBtn").addEventListener("click", function () {
        yearCount++;

    let startYear = 2022 + (yearCount - 1);
    let endYear = startYear + 1;
    let yearLabel = startYear + "-" + endYear.toString().slice(-2);

    let tbody = document.querySelector("#financialMatrixTable tbody");

    let html = `
    <tr>
        <td rowspan="2">${yearLabel}</td>
        <td>वार्षिक लाभ/हानि</td>
        <td>
            <select name="sec_3_profit_loss_${yearCount}" class="form-control"
                onchange="handleDropdownColorChange(this,'profit','#42ecf5','loss','#f28546');">
                <option value="">--Select--</option>
                <option value="profit">लाभ</option>
                <option value="loss">हानि</option>
            </select>
        </td>
        <td>
            <input type="text" name="sec_3_gross_amount_${yearCount}" class="form-control chk_decimal">
        </td>
        <td>
            <input type="text" name="sec_3_net_amount_${yearCount}" class="form-control chk_decimal">
        </td>
    </tr>

    <tr>
        <td>संचित लाभ/हानि</td>
        <td>
            <select name="sec_3_accumulated_${yearCount}" class="form-control"
                onchange="handleDropdownColorChange(this,'profit','#42ecf5','loss','#f28546');">
                <option value="">--Select--</option>
                <option value="profit">लाभ</option>
                <option value="loss">हानि</option>
            </select>
        </td>
        <td>
            <input type="text" name="sec_3_acc_gross_amount_${yearCount}" class="form-control chk_decimal">
        </td>
        <td>
            <input type="text" name="sec_3_acc_net_amount_${yearCount}" class="form-control chk_decimal">
        </td>
    </tr>
`;

        tbody.insertAdjacentHTML("beforeend", html);
    });
</script>
<?php
page_footer_start();
?>