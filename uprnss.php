<?php
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;
// error_reporting(E_ALL);
// ini_set('display_errors', 1);


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
    'pan_no' => '',
    'tan_no' => '',
    'gst_no' => '',
    'mobile_number' => '',
    'website' => '',
    'prakhand_name' => '',
    'members_no' => '',
    'inactive_members_no' => '',
    'active_members_no' => '',
    'new_members' => '',
    'share_capital' => '',
    'inactive_to_active_no' => '',
    'total_members' => '',
    'address' => '',
    'no_of_zones' => '',
    'hq_ownership' => ''
];

$row_sec_6_2 = [
    'sec_6_2_mgt_committee_is_elected' => '',
    'sec_6_2_election_year' => '',
    'sec_6_2_end_year' => '',
    'sec_6_2_mgt_committee_resolution_no' => ''
];
$row_6_2 = ['count' => 1, 'sec_6_2_designation_1' => '', 'sec_6_2_name_1' => '', 'sec_6_2_father_name_1' => '', 'sec_6_2__mob_no_1' => ''];
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
$row_2_1_2 = ['count' => 1, 'sec_2_1_2_business_description_1' => '', 'sec_2_1_2_value_1' => ''];
$human_rows = [['post_id' => '', 'sanctioned_post' => '', 'vacant_post' => '']];
$row_new_plot = [
    'sec_3_ownership' => '',
    'sec_new_plot_area' => '',
    'sec_new_plot_revenue_status' => '',
    'sec_new_plot_reason_for_not_record' => '',
    'sec_new_plot_practices_if_not' => '',
    'sec_new_plot_gata_no' => '',
    'sec_3_building_area' => '',
    'sec_3_building_rent' => '',
    'sec_3_remark' => '',
    'sec_new_remarks' => ''
];
$row_3_1 = ['east_side' => '', 'west_side' => '', 'north_side' => '', 'south_side' => '', 'on_road_land' => '', 'front_side' => '', 'remarks' => ''];
$row_3_3 = ['count' => 1, 'sec_3_b_length_1' => '', 'sec_3_b_width_1' => '', 'sec_3_b_type_of_construction_1' => '', 'sec_3_b_type_of_fund_1' => '', 'sec_3_b_comment_1' => ''];
$row_3_4 = ['count' => 1, 'sec_3_b_storage_capacity_1' => '', 'sec_3_b_godown_year_1' => '', 'sec_3_b_wdra_certified_1' => '', 'sec_3_b_godown_type_of_fund_1' => '', 'sec_3_b_godown_status_1' => '', 'sec_3_b_godown_comment_1' => ''];
$row_3_5 = ['sec_3_c_id' => 1, 'sec_3_c_length_1' => '', 'sec_3_c_vacant_land_status_1' => '', 'sec_3_c_land_location_1' => '', 'sec_3_c_suitable_godown_1' => '', 'sec_3_c_rak_distance_1' => '', 'sec_3_c_approach_road_1' => '', 'sec_3_c_paved_road_1' => '', 'sec_3_status_of_warehouse_1' => ''];
$row_2_1 = [
    'sec_6_access_road' => '',
    'sec_6_paved_road' => '',
    'sec_6_2_truck_not_reach' => '',
    'sec_8_plot_frontage' => '',
    'investment' => '',
    'loan' => '',
    'msp' => '',
    'msp_comm' => '',
    'subscribers' => '',
    'pds' => '',
    'total_business' => '',
    'last_year_profit_loss' => '',
    'last_year_pl_amount' => '',
    'seq_year_profit_loss' => '',
    'seq_year_pl_amount' => '',
    'financial_audit_year' => '',
    'approach_road_photo' => '',
    'construction_status' => '',
    'approach_road' => '',
    'distance_from_approach_road' => '',
    'electric_connection' => '',
    'electric_connection_proposal' => '',
    'internet_connectivity' => '',
    'sec_7_electrical_connection' => '',
    'sec_7_electrical_connection_working' => '',
    'sec_7_if_yes' => '',
    'sec_8_internet_connection' => ''
];
$row_other_land = [
    'count' => 1,
    'other_land_district_1' => '',
    'other_land_tehsil_1' => '',
    'other_land_area_type_1' => '',
    'other_land_land_area_1' => '',
    'other_land_ownership_1' => '',
    'other_land_other_owner_1' => '',
    'other_land_land_status_1' => '',
    'other_land_construction_1' => '',
    'other_land_other_construct_1' => '',
    'other_land_address_1' => '',
    'other_land_latitude_1' => '',
    'other_land_longitude_1' => '',
    'other_land_location_mode_1' => ''
];
$row_8 = [
    'sec_8_electrical_connection' => '',
    'sec_8_electrical_connection_working' => '',
    'sec_8_bill_paid_yes_no' => '',
    'sec_8_electricity_not_available_reason' => '',
    'sec_8_electricity_not_available_remark' => '',
    'sec_8_bill_not_paid_month' => '',
    'sec_8_outstanding_amount' => '',
    'sec_8_solar_connection' => '',
    'sec_8_solar_work_status' => '',
    'sec_8_solar_bill_paid' => '',
    'sec_8_solar_rooftop' => '',
    'sec_8_solar_remark' => '',
    'sec_8_solar_date' => '',
    'sec_8_internet_connection' => '',
    'sec_8_internet_service_provider' => '',
    'sec_8_internet_bill_paid' => '',
    'sec_8_select_internet_operator' => '',
    'internet_not_bill_paid_month' => '',
    'sec_8_internet_outstanding_amount' => '',
    'sec_8_narrow_tubes' => '',
    'sec_8_water_tank' => '',
    'sec_8_samarsabel' => '',
    'sec_8_handpump' => '',
    'sec_8_internet_active' => ''
];

if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.*, concat("/user_data/", apex_si_1_1.apex_id, "/", photo_id) as photo_id FROM `apex_si_1_1` LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '" ';
    $result_invoice = execute_query($sql);
    if ($result_invoice && mysqli_num_rows($result_invoice) >= 1) {
        $row_invoice = array_merge($row_invoice, mysqli_fetch_assoc($result_invoice));

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
$districts = [];
$sql_d = "SELECT sno AS district_id, district_name FROM master_district ORDER BY district_name ASC";
$res_d = execute_query($sql_d);
if ($res_d) {
    while ($d = mysqli_fetch_assoc($res_d)) {
        $districts[] = $d;
    }
}
?>

<?php
page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="js/survey_validate.js?v=1.4.0"></script>


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
                                <!-- <div class="step"> -->
                                    <?php echo $msg; ?>

                                    <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                            style="height:45px; width:45px;"> 1. संस्था का विवरण </h4>
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
                                                            उत्तर प्रदेश राज्य निर्माण सहकारी संघ लि०, उ०प्र०
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

                                        <hr/>

                                        <div class="row">
                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                <label> संस्था का पंजीकरण संख्या</label>
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_no']); ?>">
                                            </div>


                                            <div class="col-sm-3 form-group">
                                                <label> संस्था का पंजीकरण दिनांक</label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_date']); ?>">
                                            </div>

                                            <div class="col-sm-3 form-group">
                                                <label>पैन न०</label>
                                                <input type="text" name="pan_no" id="pan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['pan_no'] ?? ''); ?>">
                                            </div>

                                            <div class="col-sm-3 form-group">
                                                <label>टैन न०</label>
                                                <input type="text" name="tan_no" id="tan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['tan_no'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>जी०एस०टी०एन० न०</label>
                                                <input type="text" name="gst_no" id="gst_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['gst_no'] ?? ''); ?>">
                                            </div>
                                            <!-- <div class="col-sm-3 form-group">
                                                <label>क्या समिति सक्रिय है ?</label>
                                                <select class="form-control" id="committee_status"
                                                    name="committee_status" tabindex="<?php echo $tab++; ?>">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php if (($row_invoice['committee_status'] ?? '') == 'yes')
                                                        echo 'selected'; ?>>
                                                        हाँ</option>
                                                    <option value="no" <?php if (($row_invoice['committee_status'] ?? '') == 'no')
                                                        echo 'selected'; ?>>नहीं</option>
                                                </select>
                                            </div> -->

                                            <div class="col-sm-3 form-group">
                                                <label>ई-मेल आई.डी.</label>
                                                <input type="text" name="email_id" id="email_id"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['email_id'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>दूरभाष न०</label>
                                                <input type="text" name="mobile_number" id="mobile_number"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['mobile_number'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>वेबसाइट</label>
                                                <input type="text" name="website" id="website"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['website'] ?? ''); ?>">
                                            </div>
                                            <!-- <div class="col-sm-3 form-group">
                                                <label>पता (Address)</label>
                                                <input type="text" name="address" id="address"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['address'] ?? ''); ?>">
                                            </div> -->

                                            <div class="col-sm-3 form-group">
                                                <label>मुख्यालय का स्वामित्व</label>
                                                <select name="hq_ownership" id="hq_ownership" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>">
                                                    <option value="">--Select--</option>
                                                    <option value="sway_ka" <?php if (($row_invoice['hq_ownership'] ?? '') == 'sway_ka')
                                                        echo 'selected'; ?>>स्वयं का</option>
                                                    <option value="kiraye_pe" <?php if (($row_invoice['hq_ownership'] ?? '') == 'kiraye_pe')
                                                        echo 'selected'; ?>>किराये का</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>मुख्यालय की फोटो संलग्न करें</label>
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


                                        </div>
                                        <h5>
                                            <img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;">1.1 शीर्ष संस्था के कार्यालय
                                        </h5>
                                        <br>

                                        <div class="card shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 form-group">
                                                        <label>जोन की संख्या</label>
                                                        <input type="text" name="no_of_zones" id="no_of_zones"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo htmlspecialchars($row_invoice['no_of_zones'] ?? ''); ?>"
                                                            oninput="updateOfficeRows(this.value)">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label>प्रखण्ड की संख्या</label>
                                                        <input type="text" id="global_prakhand_count"
                                                            class="form-control" tabindex="<?php echo $tab++; ?>"
                                                            oninput="updateSeparatePrakhandRows(this.value)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive" id="zoneTableWrapper" style="display:none;">
                                            <table class="table table-bordered" id="officeContainer"
                                                style="width: 100%; table-layout: fixed;">
                                                <thead>
                                                    <tr class="office-block-header bg-light">
                                                        <th width="15%" style="color: black; font-weight: bold;">जोन का
                                                            नाम</th>
                                                        <th width="15%" style="color: black; font-weight: bold;">जोन का
                                                            दूरभाष न०</th>
                                                        <th width="20%" style="color: black; font-weight: bold;">जोन का
                                                            ई-मेल आई.डी.</th>
                                                        <th width="20%" style="color: black; font-weight: bold;">जोन का
                                                            पता</th>
                                                        <th width="100%" style="color: black; font-weight: bold;">जोन की
                                                            फोटो संलग्न करें</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="office-block" data-zone-index="1"
                                                    style="border-top: 2px solid #dee2e6;">
                                                    <tr>
                                                        <td style="padding: 5px;">
                                                            <input type="text" name="zone_name[]"
                                                                class="form-control zone-name" placeholder="जोन का नाम">
                                                        </td>
                                                        <td style="padding: 5px;">
                                                            <input type="text" name="zone_mobile[]"
                                                                class="form-control zone-mobile"
                                                                placeholder="जोन का दूरभाष">
                                                        </td>
                                                        <td style="padding: 5px;">
                                                            <input type="text" name="zone_email[]"
                                                                class="form-control zone-email"
                                                                placeholder="जोन का ई-मेल">
                                                        </td>
                                                        <td style="padding: 5px;">
                                                            <input type="text" name="zone_address[]"
                                                                class="form-control zone-address"
                                                                placeholder="जोन का पता">
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
                                            <h5 class="text-primary mb-2">प्रखण्ड का विवरण</h5>
                                            <table class="table table-bordered" id="prakhandContainer"
                                                style="width: 100%; table-layout: fixed;">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th width="15%" style="color: black; font-weight: bold;">प्रखण्ड
                                                            का नाम</th>
                                                        <th width="15%" style="color: black; font-weight: bold;">प्रखण्ड
                                                            का दूरभाष न०</th>
                                                        <th width="20%" style="color: black; font-weight: bold;">प्रखण्ड
                                                            का ई-मेल आई.डी.</th>
                                                        <th width="20%" style="color: black; font-weight: bold;">प्रखण्ड
                                                            का पता</th>
                                                        <th width="" style="color: black; font-weight: bold;">प्रखण्ड की
                                                            फोटो</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="prakhand-main-tbody">
                                                    <tr class="prakhand-row-template">
                                                        <td><input type="text" name="prakhand_name[]"
                                                                class="form-control" placeholder="प्रखण्ड का नाम"></td>
                                                        <td><input type="text" name="prakhand_mobile[]"
                                                                class="form-control" placeholder="प्रखण्ड का दूरभाष">
                                                        </td>
                                                        <td><input type="text" name="prakhand_email[]"
                                                                class="form-control" placeholder="प्रखण्ड का ई-मेल">
                                                        </td>
                                                        <td><input type="text" name="prakhand_address[]"
                                                                class="form-control" placeholder="प्रखण्ड का पता"></td>
                                                        <td><input type="file" name="prakhand_image[]"
                                                                class="form-control"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>


                                        <br>
                                        <h5>
                                            <img src="images/logo/2.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;">
                                            1.2 संस्था के सदस्यों का विवरण
                                        </h5>
                                        <br>

                                        <div class="col-sm-12">
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
                                        </div>
                                    </div>
                                <!-- </div> -->
                                <!----------------2nd start-------------------------------------------------------->
                                <!-- <div class="step"> -->
                                    <h4>
                                        <img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        2. संस्था की प्रबंध कमेटी
                                    </h4>

                                    <div class="col-sm-12">
                                        <div class="row">

                                            <!-- Committee elected -->
                                            <div class="col-md-3">
                                                <label>प्रबंध कमेटी निर्वाचित है?</label>
                                                <select name="sec_6_2_mgt_committee_is_elected"
                                                    id="sec_6_2_mgt_committee_is_elected" class="form-control"
                                                    onchange="hide_show(this.value, '#election_year_block', 'yes'); hide_show(this.value, '#end_year_block', 'yes');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo ($row_sec_6_2['sec_6_2_mgt_committee_is_elected'] == 'yes') ? 'selected' : ''; ?>>
                                                        निर्वाचित है
                                                    </option>
                                                    <option value="no" <?php echo ($row_sec_6_2['sec_6_2_mgt_committee_is_elected'] == 'no') ? 'selected' : ''; ?>>
                                                        प्रशासनिक कमेटी
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Election Year -->
                                            <div class="col-sm-3 form-group" id="election_year_block"
                                                style="display:none;">
                                                <label>निर्वाचन का वर्ष</label>
                                                <select name="sec_6_2_election_year" id="sec_6_2_election_year"
                                                    class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    for ($i = 2024; $i >= 1975; $i--) {
                                                        echo '<option value="' . $i . '" ' . ($i == $row_sec_6_2['sec_6_2_election_year'] ? 'selected' : '') . '>' . $i . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- End Year -->
                                            <div class="col-sm-3 form-group" id="end_year_block" style="display:none;">
                                                <label>निर्वाचित कमेटी की कार्यावधि पूर्ण होने का वर्ष</label>
                                                <select name="sec_6_2_end_year" id="sec_6_2_end_year"
                                                    class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    for ($i = 2024; $i <= 2030; $i++) {
                                                        echo '<option value="' . $i . '" ' . ($i == $row_sec_6_2['sec_6_2_end_year'] ? 'selected' : '') . '>' . $i . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>

                                        <!-- Committee Members -->
                                        <div id="sec_2_b">
                                            <?php
                                            $row_6_2['count'] = 1;
                                            for ($i = 1; $i <= $row_6_2['count']; $i++) {
                                                ?>
                                                <div class="row">

                                                    <!-- Designation -->
                                                    <div class="col-sm-4 form-group">
                                                        <label>पदनाम</label>
                                                        <select class="form-control"
                                                            name="sec_6_2_designation_<?php echo $i; ?>">
                                                            <option value="">--Select--</option>
                                                            <option value="अध्यक्ष" <?php echo ($row_6_2['sec_6_2_designation_' . $i] == 'अध्यक्ष') ? 'selected' : ''; ?>>अध्यक्ष</option>
                                                            <option value="उपाध्यक्ष" <?php echo ($row_6_2['sec_6_2_designation_' . $i] == 'उपाध्यक्ष') ? 'selected' : ''; ?>>उपाध्यक्ष</option>
                                                            <option value="संचालक" <?php echo ($row_6_2['sec_6_2_designation_' . $i] == 'संचालक') ? 'selected' : ''; ?>>संचालक</option>
                                                        </select>
                                                    </div>

                                                    <!-- Name -->
                                                    <div class="col-sm-4 form-group">
                                                        <label>नाम</label>
                                                        <input type="text" class="form-control"
                                                            name="sec_6_2_name_<?php echo $i; ?>"
                                                            value="<?php echo $row_6_2['sec_6_2_name_' . $i]; ?>">
                                                    </div>

                                                    <!-- Mobile -->
                                                    <div class="col-sm-4 form-group">
                                                        <label>मोबाईल नंबर</label>
                                                        <input type="text" class="form-control"
                                                            name="sec_6_2__mob_no_<?php echo $i; ?>"
                                                            value="<?php echo $row_6_2['sec_6_2__mob_no_' . $i]; ?>">
                                                    </div>

                                                    <?php if ($i == $row_6_2['count']) { ?>
                                                        <div class="col-sm-12 text-right">
                                                            <button type="button" class="btn btn-info"
                                                                onclick="sec_6_2_add_rows();">
                                                                नई पंक्ति जोड़े [+]
                                                            </button>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            <?php } ?>
                                        </div>

                                        <input type="hidden" name="sec_6_2_id" value="<?php echo $row_6_2['count']; ?>">
                                    </div>
                                <!-- </div> -->

                                <!-- ------------------------------- 3rd start ---------------------------------------- -->
                                <!-- <div class="step"> -->
                                    <h4>
                                        <img src="images/logo/3.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        3. संस्था की वित्तीय सूचना
                                    </h4>

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
                                                    ?>
                                                    <tr>
                                                        <td rowspan="2"><?= $yearLabel ?></td>
                                                        <td>वार्षिक लाभ/हानि</td>
                                                        <td>
                                                            <select name="sec_3_profit_loss_<?= $i + 1 ?>"
                                                                class="form-control"
                                                                onchange="handleDropdownColorChange(this,'profit','#42ecf5','loss','#f28546');">
                                                                <option value="">--Select--</option>
                                                                <option value="profit">लाभ</option>
                                                                <option value="loss">हानि</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="sec_3_gross_amount_<?= $i + 1 ?>"
                                                                class="form-control chk_decimal"></td>
                                                        <td><input type="text" name="sec_3_net_amount_<?= $i + 1 ?>"
                                                                class="form-control chk_decimal"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>संचित लाभ/हानि</td>
                                                        <td>
                                                            <select name="sec_3_accumulated_<?= $i + 1 ?>"
                                                                class="form-control"
                                                                onchange="handleDropdownColorChange(this,'profit','#42ecf5','loss','#f28546');">
                                                                <option value="">--Select--</option>
                                                                <option value="profit">लाभ</option>
                                                                <option value="loss">हानि</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="sec_3_acc_gross_amount_<?= $i + 1 ?>"
                                                                class="form-control chk_decimal"></td>
                                                        <td><input type="text" name="sec_3_acc_net_amount_<?= $i + 1 ?>"
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
                                    </div>
                                <!-- </div> -->

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


                                <!-- ------------------------------------------------4th start --------------------------------- -->
                                <!-- <div class="step"> -->
                                    <h4>
                                        <img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        4. कार्य व व्यवसाय
                                    </h4>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover" id="business_matrix">
                                                <thead class="table-light" style="background-color: #e9ecef;">
                                                    <tr>
                                                        <th style="width: 15%; color: #000;">वर्ष</th>
                                                        <th style="width: 30%; color: #000;">व्यवसाय का विवरण</th>
                                                        <th style="width: 18%; color: #000;">वार्षिक टर्नओवर</th>
                                                        <th style="width: 15%; color: #000;">लक्ष्य</th>
                                                        <th style="width: 15%; color: #000;">उपलब्धि</th>
                                                        <th style="width: 7%; color: #000;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $count = 1;
                                                    for ($i = 1; $i <= $count; $i++) {
                                                        ?>
                                                        <tr class="business_matrix_row">
                                                            <td>
                                                                <select name="business_year_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <option value="">---वित्तीय वर्ष---</option>
                                                                    <?php
                                                                    for ($y = 2020; $y <= 2030; $y++) {
                                                                        $fy = $y . "-" . ($y + 1);
                                                                        $selected = (!empty($row_2_1_2['business_year_' . $i]) &&
                                                                            $row_2_1_2['business_year_' . $i] == $fy)
                                                                            ? 'selected' : '';
                                                                        echo "<option value='$fy' $selected>$fy</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    name="business_description_<?php echo $i; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1_2['sec_2_1_2_business_description_' . $i] ?? ''; ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    name="business_turnover_<?php echo $i; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1_2['sec_2_1_2_value_' . $i] ?? ''; ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="business_target_<?php echo $i; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1_2['business_target_' . $i] ?? ''; ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    name="business_achievement_<?php echo $i; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1_2['business_achievement_' . $i] ?? ''; ?>">
                                                            </td>
                                                            <td class="text-center">
                                                                <?php if ($i == $count) { ?>
                                                                    <button type="button" class="btn btn-info btn-sm"
                                                                        onclick="business_matrix_add_row();">नई पंक्ति जोड़ें
                                                                        [+]</button>
                                                                    <input type="hidden" name="other_business_id"
                                                                        id="other_business_id" value="<?php echo $count; ?>">
                                                                <?php } else { ?>
                                                                    <button type="button" class="btn btn-danger btn-sm"
                                                                        onclick="$(this).closest('tr').remove();">-</button>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <!-- </div> -->

                                <!----------------5th start-------------------------------------------------------->

                                <!-- <div class="step">
                                    <h4>
                                        <img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        5. मानव सम्पदा
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

                                    // Update the JS variable for dynamic rows
                                    $postOptionsHTML = '';
                                    foreach ($posts as $p) {
                                        $postOptionsHTML .= '<option value="' . $p['sno'] . '">' . htmlspecialchars($p['post_name']) . '</option>';
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
                                                        <select name="staff_type[]" class="form-control"
                                                            onchange="updateStaffSection(this)">
                                                            <option value="">--Select--</option>
                                                            <option value="tech">तकनीकी कर्मचारी</option>
                                                            <option value="nontech">प्रशासनिक कर्मचारी</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="post_id[]" class="form-control post-select"
                                                            onchange="updateStaffSection(this)">
                                                            <option value="">--Select--</option>
                                                            <?php foreach ($posts as $p) { ?>
                                                                <option value="<?php echo $p['sno']; ?>"
                                                                    data-technical="<?php echo ($p['technical'] == 'T') ? 'T' : ''; ?>">
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
                                                            onclick="addHumanResourceRow();">नई पंक्ति जोड़ें
                                                            [+]</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="staff_section" style="display:none;" class="mt-3">
                                        <h6>कर्मचारी विवरण</h6>
                                        <div id="staff_rows"></div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-primary btn-sm me-3"
                                                onclick="uploadDocument()" style="margin-right:1rem;">Upload
                                                Document</button>
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="downloadExcel()" style="height: 40px;">Download Excel</button>
                                        </div>
                                    </div>
                                    <div id="staff_row_template" style="display:none;">
                                        <div class="staff_row border p-3 mb-3 rounded">
                                            <div class="row">
                                                <div class="col-md-3 form-group">
                                                    <label>पद</label>
                                                    <input type="text" name="staff_post_name[]"
                                                        class="form-control staff_post_name" readonly>
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
                                </div> -->

                                <!-- <div class="step"> -->
                                    <h4>
                                        <img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        5. मानव सम्पदा
                                    </h4>

                                    <!-- <h5 class="mt-3">स्टाफ (Staff)</h5> -->

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

                                    // Update the JS variable for dynamic rows
                                    $postOptionsHTML = '';
                                    foreach ($posts as $p) {
                                        $postOptionsHTML .= '<option value="' . $p['sno'] . '">' . htmlspecialchars($p['post_name']) . '</option>';
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
                                                        <select name="staff_type[]" class="form-control"
                                                            onchange="updateStaffSection(this)">
                                                            <option value="">--Select--</option>
                                                            <option value="tech">Technical</option>
                                                            <option value="nontech">Non-Technical</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="post_id[]" class="form-control post-select"
                                                            onchange="updateStaffSection(this)">
                                                            <option value="">--Select--</option>
                                                            <?php foreach ($posts as $p) { ?>
                                                                <option value="<?php echo $p['sno']; ?>">
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

                                    <script>
                                        function addHumanResourceRow() {
                                            let tbody = document.getElementById('human_resource_rows');
                                            let firstRow = tbody.querySelector('tr.human_row');
                                            let newRow = firstRow.cloneNode(true);

                                            // Clear all input values in the new row
                                            newRow.querySelectorAll('input').forEach(input => input.value = '');
                                            newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

                                            // Change the + button to - button
                                            let actionCell = newRow.querySelector('td:last-child');
                                            actionCell.innerHTML = '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'tr\').remove();">-</button>';

                                            tbody.appendChild(newRow);
                                        }
                                    </script>

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

                                        function downloadExcel() {
                                            alert('Download Excel functionality can be implemented here!');
                                        }
                                    </script>

                                <!-- </div> -->

                                <!-- <div class="step"> -->
                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 6. संस्था भवन/सम्पत्ति का विवरण</h4>
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

                                                <!-- <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Latitude</label>
                                                        <input type="text" id="lat"
                                                            value="<?php echo $row_invoice['latitude']; ?>"
                                                            class="form-control">
                                                        <label>Longitude</label>
                                                        <input type="text" id="long"
                                                            value="<?php echo $row_invoice['longitude']; ?>"
                                                            class="form-control">
                                                        <button type="button" class="btn btn-info btn-block mt-2"
                                                            onClick="getLocation();">लोकेशन रिफ्रेश करें</button>
                                                        <div class="blinking-text">(संस्था का लोकेशन मोबाईल से भरे)*
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8" id="map_container">
                                                        <iframe id="googlemap"
                                                            src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                            width="100%" height="300px"
                                                            style="border:1px solid #ddd; border-radius:10px;"
                                                            allowfullscreen="" loading="lazy"
                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="latitude" name="latitude"
                                value="<?php echo $row_invoice['latitude']; ?>">
                            <input type="hidden" id="longitude" name="longitude"
                                value="<?php echo $row_invoice['longitude']; ?>">
                            <div id="q-box__buttons" class="text-center">
                                <button id="prev-btn" class="btn btn-info" type="button"
                                    onClick="save_draft()">Previous</button>
                                <button id="next-btn" class="btn btn-success" type="button"
                                    onClick="save_draft()">Next</button>
                                <button id="submit-btn" class="btn btn-danger" type="submit"
                                    onClick="validate_input(); save_draft();">Submit</button>
                            </div>

                            <div class="text-left mt-3 mb-4">
                                <button class="btn btn-warning" type="button" onClick="save_draft()">
                                    <i class="fas fa-save"></i> Save Draft
                                </button>
                            </div>
                            <input type="hidden" id="term" name="term" value="a">
                            <input type="hidden" id="id" name="id" value="submit_form_uprnss">
                            <input type="hidden" id="current_step_count" name="current_step_count" value="">
                            <input type="hidden" id="survey_id" name="survey_id"
                                value="<?php echo $row_invoice['sno']; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="otp_form"
        name="otp_form"></form>
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

    // function sec_3_c_add_rows() {
    //     var id = parseFloat($("#sec_3_c_id").val());
    //     if (!id) {
    //         id = 0;
    //     }
    //     for (var i = 0; i <= id; i++) {
    //         if ($("#sec_3_c_length_" + i).val() == '') {
    //             alert("पंक्ति संख्या " + i + " खाली है");
    //             $("#sec_3_c_length_" + i).focus();
    //             return;
    //         }
    //     }
    //     id = id + 1;
    //     $("#sec_3_c_rows").remove();

    //     var txt = '<div class="row"><div class="col-sm-2 form-group"><label>क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="sec_3_c_length_' + id + '" id="sec_3_c_length_' + id + '" class="form-control chk_number" data-type="क्षेत्रफल हेक्टेयर में मे लिखे"></div><div class="col-sm-2 form-group"><label>भूमि की स्थिति (उपजाऊ /बंजर)</label><select name="sec_3_c_vacant_land_status_' + id + '" id="sec_3_c_vacant_land_status_' + id + '" class="form-control"><option value="">--select-- </option><option value="fertile">उपजाऊ </option><option value="barren">बंजर </option></select></div><div class="col-sm-2 form-group"><label>स्थान (समिति प्रांगण या अन्य स्थान)</label><select name="sec_3_c_land_location_' + id + '" id="sec_3_c_land_location_' + id + '" class="form-control"><option value="">--select-- </option><option value="inpremise">समिति प्रांगण </option><option value="other">अन्य स्थान </option></select></div><div class="col-sm-2 form-group"><label>गोदाम के लिए उपयुक्त है या नहीं ?</label><select class="form-control" type="checkbox" value="yes" id="sec_2_accountant" name="sec_3_c_suitable_godown_' + id + '" id="sec_3_c_suitable_godown_' + id + '"><option value="">--Select--</option><option value="yes">है</option><option value="no" style="background:#f00">नहीं</option></select></div><div class="col-sm-2 form-group"><label>जनपद से रैक पाइण्ट की दूरी</label><input type="text" name="sec_3_c_rak_distance_' + id + '" id="sec_3_c_rak_distance_' + id + '" class="form-control"></div><div class="col-sm-2 form-group" id="land_access_road_<?php echo $i; ?>"><label>पहुच मार्ग का प्रकार</label><select name="sec_3_c_paved_road_' + id + '" id="sec_3_c_paved_road_' + id + '" class="form-control"><option value="">--select--</option><option value="ordinary">कच्ची सड़क</option><option value="nh">नेशनल हाईवे</option><option value="sh">स्टेट हाईवे</option><option value="mdr">एम.डी.आर.</option><option value="odr">ओ.डी.आर.</option><option value="rural_road">ग्रामीण सड़क</option><option value="other">अन्य</option></select></div><div class="col-sm-2 form-group my-auto" id="sec_3_c_rows"><button type="button" class="btn btn-info" onClick="sec_3_c_add_rows();">नई पंक्ति जोड़े</button><input type="hidden" name="sec_3_c_id" id="sec_3_c_id" value="' + id + '"></div></div>';

    //     $("#sec_3_c").append(txt);
    // }

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
        // if (mode === "GPS") {
        //     $("#other_land_latitude_" + row).prop("readonly", true);
        //     $("#other_land_longitude_" + row).prop("readonly", true);
        //     $("#other_land_gps_btn_" + row).show();
        // } else {
        //     $("#other_land_latitude_" + row).prop("readonly", false);
        //     $("#other_land_longitude_" + row).prop("readonly", false);
        //     $("#other_land_gps_btn_" + row).hide();
        // }
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

        // Fetch prakhand details if already selected
        let initialPrakhand = $("#prakhand_name").val();
        if (initialPrakhand) {
            fetchPrakhandDetails(initialPrakhand);
        }
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

    function fetchPrakhandDetails(prakhand_id) {
        if (!prakhand_id) {
            $("#prakhand_details_area").hide();
            $("#prakhand_mobile").val('');
            $("#prakhand_email").val('');
            $("#prakhand_address").val('');
            $("#prakhand_lat").val('');
            $("#prakhand_long").val('');
            return;
        }
        $.ajax({
            type: "POST",
            url: "scripts/ajax_uprnss.php",
            data: { term: "b", id: "prakhand_details", val: prakhand_id },
            success: function (res) {
                let data = JSON.parse(res);
                if (data.length > 0) {
                    let lat = data[0].latitude;
                    let lon = data[0].longitude;
                    let mobile = data[0].mobile_no;
                    let email = data[0].email_id;
                    let address = data[0].address;

                    // Update UI fields
                    $("#prakhand_mobile").val(mobile || '');
                    $("#prakhand_email").val(email || '');
                    $("#prakhand_address").val(address || '');
                    $("#prakhand_lat").val(lat || '');
                    $("#prakhand_long").val(lon || '');

                    $("#prakhand_details_area").fadeIn();

                    if (lat && lon) {
                        $("#lat").val(lat);
                        $("#long").val(lon);
                        $("#googlemap").attr("src", "https://maps.google.com/maps?q=" + lat + "," + lon + "&hl=en&z=13&amp;output=embed");
                    }
                }
            }
        });
    }
</script>


<script>
    let officeIndex = 1;

    function fetchPrakhandDetailsDynamic(selectEl) {
        const block = selectEl.closest('.office-block');

        if (selectEl.value === "") {
            block.querySelectorAll('input').forEach(inp => inp.value = "");
            return;
        }

        // Show demo data (replace with AJAX later)
        block.querySelector('.prakhand-mobile').value = "9876543210";
        block.querySelector('.prakhand-email').value = "office@example.com";
        block.querySelector('.prakhand-address').value = "Block Office Address";
        block.querySelector('.prakhand-lat').value = "25.123456";
        block.querySelector('.prakhand-long').value = "85.123456";
    }

    function getCurrentLocationDynamic(btn) {
        const block = btn.closest('.office-block');

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (pos) {
                block.querySelector('.current-lat').value = pos.coords.latitude;
                block.querySelector('.current-long').value = pos.coords.longitude;
            }, function () {
                alert("Location access denied.");
            });
        } else {
            alert("Geolocation not supported.");
        }
    }

    function addOfficeBlock() {
        officeIndex++;
        const container = document.getElementById('officeContainer');
        const firstBlock = container.querySelector('.office-block');
        const newBlock = firstBlock.cloneNode(true);

        // Reset values
        newBlock.querySelectorAll('input').forEach(inp => inp.value = "");
        newBlock.querySelectorAll('select').forEach(sel => sel.value = "");

        container.appendChild(newBlock);
    }
</script>

<script>
    function save_draft() {
        var form = $('#user_form')[0];
        var data = new FormData(form);

        // Determine current step
        var activeStepIndex = $(".step").index($(".step.d-block"));
        data.append('current_step_count', activeStepIndex);

        data.append('id', 'submit_form_uprnss');
        data.append('term', 'b');

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "scripts/ajax_uprnss.php",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            success: function (data) {
                // console.log("Response:", data);
                try {
                    var res = JSON.parse(data);
                    if (res && res.length > 0) {
                        res.forEach(function (item) {
                            if (item.id == 'error') {
                                console.log(item.error);
                            } else if (item.id && item.id != 'Update') {
                                if ($('#survey_id').val() == '') {
                                    $('#survey_id').val(item.id);
                                }
                            }
                        });
                    }
                } catch (e) {
                    console.log("Error parsing response", e);
                }
            },
            error: function (e) {
                console.log("Error:", e);
            }
        });
    }
</script>
<script>
    function business_matrix_add_row() {
        let id = parseInt(document.getElementById('other_business_id').value) + 1;
        document.getElementById('other_business_id').value = id;

        // Build Financial Year options 2020–2030
        let fyOptions = `<option value="">---वित्तीय वर्ष---</option>`;
        for (let y = 2020; y <= 2030; y++) {
            let fy = y + "-" + (y + 1);
            fyOptions += `<option value="${fy}">${fy}</option>`;
        }

        let html = `
            <tr class="business_matrix_row">
                <td>
                    <select name="business_year_${id}" class="form-control">
                        ${fyOptions}
                    </select>
                </td>
                <td>
                    <input type="text" name="business_description_${id}" class="form-control">
                </td>
                <td>
                    <input type="text" name="business_turnover_${id}" class="form-control">
                </td>
                <td>
                    <input type="text" name="business_target_${id}" class="form-control">
                </td>
                <td>
                    <input type="text" name="business_achievement_${id}" class="form-control">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick="$(this).closest('tr').remove();">
                        हटाएं [-]
                    </button>
                </td>
            </tr>
        `;

        document.querySelector('#business_matrix tbody')
            .insertAdjacentHTML('beforeend', html);
    }

    function sec_3_c_add_rows() {
        let container = document.getElementById('sec_3_c');
        let id = parseInt(document.getElementById('sec_3_c_id').value) + 1;
        document.getElementById('sec_3_c_id').value = id;

        // Remove existing + button from last row
        let lastAddBtn = container.querySelector('.sec3c_row .btn-info');
        if (lastAddBtn) lastAddBtn.remove();

        let html = `
        <div class="row mb-2 sec3c_row">
            <!-- District -->
            <div class="col-sm-2 form-group">
                <select name="sec_3_c_district_${id}" class="form-control">
                    <option value="">--Select--</option>
                    <?php foreach ($districts as $d) { ?>
                        <option value="<?php echo $d['district_id']; ?>"><?php echo htmlspecialchars($d['district_name']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Area -->
            <div class="col-sm-2 form-group">
                <input type="text" name="sec_3_c_area_${id}" class="form-control">
            </div>

            <!-- Image Upload -->
            <div class="col-sm-3 form-group">
                <label>संस्था का फोटो GPS टैग <br>के साथ संलग्न करे</label>
                <input type="file" name="sec_3_c_image_${id}" class="form-control">
            </div>

            <!-- Add / Remove Buttons -->
            <div class="col-sm-1 form-group my-auto">
                <button type="button" class="btn btn-danger" onclick="$(this).closest('.sec3c_row').remove();">-</button>
                <button type="button" class="btn btn-info" onclick="sec_3_c_add_rows();">+</button>
            </div>
        </div>
        `;

        container.insertAdjacentHTML('beforeend', html);
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

    // If page loads with existing zone count, render blocks accordingly
    document.addEventListener('DOMContentLoaded', function () {
        var z = document.getElementById('no_of_zones');
        if (z && z.value) updateOfficeRows(z.value);

        // Also sync global prakhand if needed, but usually empty on load unless saved. 
        // If saved, we might need value from hidden input of first row? 
        // For now, leave as is.
    });
</script>

<script type="text/javascript" src="js/multistepform_uprnss.js?v=1"></script>
<!-- <script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script> -->


<?php
page_footer_start();
page_footer_end();
?>