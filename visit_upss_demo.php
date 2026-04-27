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
    'pan_no' => '',
    'tan_no' => '',
    'gst_no' => '',
    'mobile_number' => '',
    'website' => '',
    'society_registration_no' => '',
    'society_registration_date' => '',
    'indivisual_members' => '',
    'committee_members' => '',
    'central_soc_members' => '',
    'primary_soc_members' => '',
];
if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.`sno` as sno, apex_si_1_1.apex_id as `apex_id`, `longitude`, `latitude`, `committee_status`, `email_id`, `photo_id`, `pan_no`, `tan_no`, `gst_no`, `mobile_number`, `website`, `society_registration_no`, `society_registration_date`, `indivisual_members`, `committee_members`, `central_soc_members`, `primary_soc_members` FROM `apex_si_1_1` LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '"';
    $result_invoice = execute_query($sql);
    if ($result_invoice && mysqli_num_rows($result_invoice) >= 1) {
        $row_invoice = mysqli_fetch_assoc($result_invoice);

        $_SESSION['survey_id'] = $row_invoice['sno'];
        $row_invoice['latitude'] = $row_invoice['latitude'];
        $row_invoice['longitude'] = $row_invoice['longitude'];
        $row_invoice['committee_status'] = $row_invoice['committee_status'];
        $row_invoice['email_id'] = $row_invoice['email_id'];
        $row_invoice['photo_id'] = $row_invoice['photo_id'];
        $row_invoice['pan_no'] = $row_invoice['pan_no'];
        $row_invoice['tan_no'] = $row_invoice['tan_no'];
        $row_invoice['gst_no'] = $row_invoice['gst_no'];
        $row_invoice['mobile_number'] = $row_invoice['mobile_number'];
        $row_invoice['website'] = $row_invoice['website'];
        $row_invoice['society_registration_no'] = $row_invoice['society_registration_no'];
        $row_invoice['society_registration_date'] = $row_invoice['society_registration_date'];
        $row_invoice['indivisual_members'] = $row_invoice['indivisual_members'];
        $row_invoice['committee_members'] = $row_invoice['committee_members'];
        $row_invoice['central_soc_members'] = $row_invoice['central_soc_members'];
        $row_invoice['primary_soc_members'] = $row_invoice['primary_soc_members'];
    }

    $zone_data = [];
    $sql = 'SELECT * FROM apex_zone_details WHERE survey_id="' . $row_invoice['sno'] . '"';
    $result = execute_query($sql);
    if (!$result) {
        $data[] = array("id" => "error", "error" => "Zone: Unable to fetch data.");
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $zone_data[] = $row;
        }
    }
    $prakhand_data = [];
    $sql = 'SELECT * FROM apex_prakhand_details WHERE survey_id="' . $row_invoice['sno'] . '"';
    $result = execute_query($sql);
    if (!$result) {
        $data[] = array("id" => "error", "error" => "Prakhand: Unable to fetch data.");
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $prakhand_data[] = $row;
        }
    }

    $gas_service_data = [];
    $sql = 'SELECT * FROM apex_gas_service_details WHERE survey_id="' . $row_invoice['sno'] . '"';
    $result = execute_query($sql);
    if (!$result) {
        $data[] = array("id" => "error", "error" => "Gas Service: Unable to fetch data.");
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $gas_service_data[] = $row;
        }
    }

    $unit_data = [];
    $sql = 'SELECT * FROM apex_unit_details WHERE survey_id="' . $row_invoice['sno'] . '"';
    $result = execute_query($sql);
    if (!$result) {
        $data[] = array("id" => "error", "error" => "unit: Unable to fetch data.");
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $unit_data[] = $row;
        }
    }
    $apex = null;

    $sql = 'SELECT apex.* FROM apex_si_1_1 LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.sno="' . $row_invoice['sno'] . '"';

    $res_apex = execute_query($sql);
    if (mysqli_num_rows($res_apex) > 0) {
        $apex = mysqli_fetch_assoc($res_apex);
    }


    $sql = "SELECT c.*, GROUP_CONCAT(d.district_id ORDER BY d.district_id) AS district_ids FROM apex_si_1_4_contacts c LEFT JOIN apex_si_1_4_contact_districts d  ON d.contact_id = c.sno WHERE c.survey_id = '" . $row_invoice['sno'] . "' GROUP BY c.sno ";

    $res = execute_query($sql);

    $contact_rows = [];

    if (mysqli_num_rows($res) > 0) {
        while ($r = mysqli_fetch_assoc($res)) {
            $contact_rows[] = $r;
        }
    } else {
        // At least one blank row
        $contact_rows[] = [
            'division_id' => '',
            'district_ids' => '',
            'address' => '',
            'mobile' => '',
            'email' => '',
            'latitude' => '',
            'longitude' => ''
        ];
    }

    $sql = 'SELECT * FROM survey_major_activities WHERE survey_id="' . $row_invoice['sno'] . '" ORDER BY sno ASC';
    $result_sec_2_2 = execute_query($sql);
    $row_2_2 = array();
    $d = 1;
    if (mysqli_num_rows($result_sec_2_2) > 0) {
        $row_2_2['count'] = mysqli_num_rows($result_sec_2_2);
        while ($row_section_2_2 = mysqli_fetch_assoc($result_sec_2_2)) {
            $row_2_2['sec_2_2_year_' . $d] = $row_section_2_2['year'];
            $row_2_2['sec_2_2_amount_' . $d] = $row_section_2_2['amount'];
            $row_2_2['sec_2_2_dept_supply_' . $d] = $row_section_2_2['dept_supply'];
            $row_2_2['sec_2_2_wheat_purchase_' . $d] = $row_section_2_2['wheat_purchase'];
            $row_2_2['sec_2_2_paddy_purchase_' . $d] = $row_section_2_2['paddy_purchase'];
            $row_2_2['sec_2_2_fert_sales_' . $d] = $row_section_2_2['fert_sales'];
            $row_2_2['sec_2_2_fert_transport_' . $d] = $row_section_2_2['fert_transport'];
            $row_2_2['sec_2_2_lpg_dist_' . $d] = $row_section_2_2['lpg_dist'];
            $row_2_2['sec_2_2_trifed_simfed_' . $d] = $row_section_2_2['trifed_simfed'];
            $row_2_2['sec_2_2_cppl_anpl_' . $d] = $row_section_2_2['cppl_anpl'];
            $d++;
        }
    } else {
        $row_2_2['count'] = 1;
        $row_2_2['sec_2_2_year_1'] = '';
        $row_2_2['sec_2_2_amount_1'] = '';
        $row_2_2['sec_2_2_dept_supply_1'] = '';
        $row_2_2['sec_2_2_wheat_purchase_1'] = '';
        $row_2_2['sec_2_2_paddy_purchase_1'] = '';
        $row_2_2['sec_2_2_fert_sales_1'] = '';
        $row_2_2['sec_2_2_fert_transport_1'] = '';
        $row_2_2['sec_2_2_lpg_dist_1'] = '';
        $row_2_2['sec_2_2_trifed_simfed_1'] = '';
        $row_2_2['sec_2_2_cppl_anpl_1'] = '';
    }

    $sql = 'SELECT * FROM survey_consumer_business WHERE survey_id="' . $row_invoice['sno'] . '" ORDER BY sno ASC';
    $result_sec_2_3 = execute_query($sql);
    $row_2_3 = array();
    $d = 1;
    if (mysqli_num_rows($result_sec_2_3) > 0) {
        $row_2_3['count'] = mysqli_num_rows($result_sec_2_3);
        while ($row_section_2_3 = mysqli_fetch_assoc($result_sec_2_3)) {
            $row_2_3['sec_2_3_year_' . $d] = $row_section_2_3['year'];
            $row_2_3['sec_2_3_lpg_target_' . $d] = $row_section_2_3['lpg_target'];
            $row_2_3['sec_2_3_lpg_business_' . $d] = $row_section_2_3['lpg_business'];
            $row_2_3['sec_2_3_fert_target_' . $d] = $row_section_2_3['fert_target'];
            $row_2_3['sec_2_3_fert_business_' . $d] = $row_section_2_3['fert_business'];
            $row_2_3['sec_2_3_dept_target_' . $d] = $row_section_2_3['dept_target'];
            $row_2_3['sec_2_3_dept_business_' . $d] = $row_section_2_3['dept_business'];
            $row_2_3['sec_2_3_total_target_' . $d] = $row_section_2_3['total_target'];
            $row_2_3['sec_2_3_total_business_' . $d] = $row_section_2_3['total_business'];
            $d++;
        }
    } else {
        $row_2_3['count'] = 1;
        $row_2_3['sec_2_3_year_1'] = '';
        $row_2_3['sec_2_3_lpg_target_1'] = '';
        $row_2_3['sec_2_3_lpg_business_1'] = '';
        $row_2_3['sec_2_3_fert_target_1'] = '';
        $row_2_3['sec_2_3_fert_business_1'] = '';
        $row_2_3['sec_2_3_dept_target_1'] = '';
        $row_2_3['sec_2_3_dept_business_1'] = '';
        $row_2_3['sec_2_3_total_target_1'] = '';
        $row_2_3['sec_2_3_total_business_1'] = '';
    }

    $sql = 'select * from survey_invoice_sec_2_4 where survey_id="' . $row_invoice['sno'] . '"';
    $res_2_4 = execute_query($sql);
    if (mysqli_num_rows($res_2_4) != 0) {
        $i = 1;
        while ($row_2_4_temp = mysqli_fetch_assoc($res_2_4)) {
            $row_2_4['sec_2_4_year_' . $i] = $row_2_4_temp['year'];
            $row_2_4['sec_2_4_wheat_target_' . $i] = $row_2_4_temp['wheat_target'];
            $row_2_4['sec_2_4_wheat_business_' . $i] = $row_2_4_temp['wheat_business'];
            $row_2_4['sec_2_4_paddy_target_' . $i] = $row_2_4_temp['paddy_target'];
            $row_2_4['sec_2_4_paddy_business_' . $i] = $row_2_4_temp['paddy_business'];
            $row_2_4['sec_2_4_total_target_' . $i] = $row_2_4_temp['total_target'];
            $row_2_4['sec_2_4_total_business_' . $i] = $row_2_4_temp['total_business'];
            $i++;
        }
        $row_2_4['count'] = $i - 1;
    } else {
        $i = 1;
        $row_2_4['count'] = 1;
        $row_2_4['sec_2_4_year_' . $i] = '';
        $row_2_4['sec_2_4_wheat_target_' . $i] = '';
        $row_2_4['sec_2_4_wheat_business_' . $i] = '';
        $row_2_4['sec_2_4_paddy_target_' . $i] = '';
        $row_2_4['sec_2_4_paddy_business_' . $i] = '';
        $row_2_4['sec_2_4_total_target_' . $i] = '';
        $row_2_4['sec_2_4_total_business_' . $i] = '';
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
        $row_3_new_1['sec_3_gratuity_retired'] = $row_3_new_1['gratuity_retired'];
        $row_3_new_1['sec_3_encashment_retired'] = $row_3_new_1['encashment_retired'];
        $row_3_new_1['sec_3_proposed_work_plans'] = $row_3_new_1['proposed_work_plans'];
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

    // $sql = 'select * from survey_invoice_sec_2_1_2 where survey_id="' . $row_invoice['sno'] . '"';
    // $res_2_1_2 = execute_query($sql);
    // $i = 1;
    // $a = 1;
    // $other_msc = array();
    // if (mysqli_num_rows($res_2_1_2) != 0) {
    //     $row_2_1_2['count'] = mysqli_num_rows($res_2_1_2);
    //     while ($row_temp = mysqli_fetch_assoc($res_2_1_2)) {
    //         if ($row_temp['other_description'] == 'msc') {
    //             $other_msc[$a] = $row_temp['other_amount'];
    //             $a++;
    //         } else {
    //             $row_2_1_2['sec_2_1_2_business_description_' . $i] = $row_temp['other_description'];
    //             $row_2_1_2['sec_2_1_2_value_' . $i] = $row_temp['other_amount'];
    //             $i++;
    //         }
    //     }
    //     $_POST['sec_1_1_2_msc_service'] = $other_msc;
    //     $row_2_1_2['count'] = $i - 1;
    // } else {
    //     $row_2_1_2['count'] = 1;
    //     $row_2_1_2['sec_2_1_2_business_description_' . $i] = '';
    //     $row_2_1_2['sec_2_1_2_value_' . $i] = '';
    // }

    $sql_human = "SELECT * FROM apex_si_6_3 WHERE survey_id = '" . $row_invoice['sno'] . "'";
    $result_human = execute_query($sql_human);

    $human_rows = [];
    if ($result_human && mysqli_num_rows($result_human) > 0) {
        while ($row_h = mysqli_fetch_assoc($result_human)) {
            $human_rows[] = [
                'post_id' => $row_h['post_id'],
                'sanctioned_post' => $row_h['sanctioned_post'],
                'vacant_post' => $row_h['vacant_post'],
                'working_name' => $row_h['working_name'],
                'working_period' => $row_h['working_period'],
                'contract_no' => $row_h['contract_no'],
                'contract_name' => $row_h['contract_name']
            ];
        }
    } else {
        $human_rows[] = [
            'post_id' => '',
            'sanctioned_post' => '',
            'vacant_post' => '',
            'working_name' => '',
            'working_period' => '',
            'contract_no' => '',
            'contract_name' => ''
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

<!--Human Resource Data for Prefilled-->
<?php
$existing_hr = [];
$sql_hr = "SELECT * FROM apex_human_resource_info WHERE survey_id='" . $row_invoice['sno'] . "'";
$res_hr = execute_query($sql_hr);

if ($res_hr && mysqli_num_rows($res_hr) > 0) {
    while ($row = mysqli_fetch_assoc($res_hr)) {
        $existing_hr[] = $row;
    }
}

$prefillData = [];

$sql = "SELECT * FROM apex_human_resource_info 
        WHERE survey_id='" . $row_invoice['sno'] . "'";
$res = execute_query($sql);

if ($res && mysqli_num_rows($res) > 0) {

    while ($row = mysqli_fetch_assoc($res)) {

        $post_id = $row['hr_post_id'];

        if (!isset($prefillData[$post_id])) {
            $prefillData[$post_id] = [
                'staff_type' => $row['staff_type'],
                'hr_post_id' => $row['hr_post_id'],
                'sanctioned_post' => $row['sanctioned_post'],
                'vacant_post' => $row['vacant_post'],
                'staff_members' => []
            ];
        }

        $prefillData[$post_id]['staff_members'][] = $row;
    }
}

//Apex business data
$business_data = [];

$sql = "SELECT * FROM apex_work_profession_info 
        WHERE survey_id='" . $row_invoice['sno'] . "'";

$res = execute_query($sql);

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $business_data[] = $row;
    }
}
?>

<?php
$financial_data = [];

$sql_financial = "SELECT * FROM apex_financial_info 
                  WHERE survey_id='" . $row_invoice['sno'] . "' 
                  ORDER BY financial_year ASC";

$res_financial = execute_query($sql_financial);

if ($res_financial && mysqli_num_rows($res_financial) > 0) {
    while ($row = mysqli_fetch_assoc($res_financial)) {
        $financial_data[] = $row;
    }
}

// Structure Properly According To Your Real Columns
$structured_financial = [];

foreach ($financial_data as $row) {

    $year = $row['financial_year'];

    $structured_financial[$year] = [
        'annual' => [
            'status' => $row['annual_status'] ?? '',
            'gross_amount' => $row['annual_gross'] ?? '',
            'net_amount' => $row['annual_net'] ?? ''
        ],
        'accumulated' => [
            'status' => $row['accumulated_status'] ?? '',
            'gross_amount' => $row['accumulated_gross'] ?? '',
            'net_amount' => $row['accumulated_net'] ?? ''
        ]
    ];
}


/* ===============================
   EMPTY LAND DATA
================================ */

$empty_land_data = [];

$sql = "SELECT * 
        FROM apex_empty_land_info
        WHERE survey_id = '" . $row_invoice['sno'] . "'
        ORDER BY id ASC";

$res = execute_query($sql);

if ($res && mysqli_num_rows($res) > 0) {

    while ($row = mysqli_fetch_assoc($res)) {

        $empty_land_data[] = $row;

    }

}

//echo '<pre>';
//
//print_r($empty_land_data); exit;


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
        color: #1565c0;
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
        color: #1565c0;
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

    .highlight-text {
        padding: 8px 10px;
        background: #d7eee3;
        border: 1px solid #eee8d5;
        border-radius: 4px;
        font-weight: bold;
        color: #2c3e50;
    }
</style>
<style>
    .small-input {
        width: 600px;
        height: 30px;
        padding: 3px 6px;
        font-size: 13px;
    }
</style>
<style>
    .common-head {
        background-color: #f1f5ff;
        /* वही color जो प्रमुख कार्यकलाप का है */
        font-weight: 600;
        vertical-align: middle;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px 4px;
        vertical-align: middle;
    }

    thead th {
        text-align: center;
        white-space: normal;
    }

    .major-heading {
        font-weight: bold;
        text-align: center;
    }

    .major-heading {
        font-size: 16px;
        border-bottom: 2px solid #000;
        color: #1565c0;
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

                        <form action="scripts/ajax_upss.php" method="post" enctype="multipart/form-data" id="user_form"
                            name="user_form">
                            <div id="steps-container">
                                <!-------------------1st start--------------------------------------------------------------------->
                                <div class="step">
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
                                                            उ०प्र० उपभोक्ता सहकारी संघ, उ०प्र०
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="hidden" id="apex_code" name="apex_code"
                                                    value="<?php echo $row_invoice['apex_id']; ?>">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>Latitude</label>
                                                        <input type="text" id="lat" disabled="disabled"
                                                            value="<?php echo $row_invoice['latitude']; ?>"
                                                            class="form-control">
                                                        <label>Longitude</label>
                                                        <input type="text" id="long" disabled="disabled"
                                                            value="<?php echo $row_invoice['longitude']; ?>"
                                                            class="form-control">
                                                        <button type="button" class="btn btn-info btn-sm mt-2"
                                                            onclick="getLocation();"> लोकेशन रिफ्रेश करें</button>
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
                                        <hr />
                                        <div class="row">
                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                <label> संस्था का पंजीकरण संख्या</label>
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo ($row_invoice['society_registration_no']); ?>">
                                            </div>


                                            <div class="col-sm-3 form-group">
                                                <label> संस्था का पंजीकरण दिनांक</label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo ($row_invoice['society_registration_date']); ?>">
                                            </div>

                                            <div class="col-sm-3 form-group">
                                                <label>पैन न०</label>
                                                <input type="text" name="pan_no" id="pan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo ($row_invoice['pan_no'] ?? ''); ?>">
                                            </div>

                                            <div class="col-sm-3 form-group">
                                                <label>टैन न०</label>
                                                <input type="text" name="tan_no" id="tan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo ($row_invoice['tan_no'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>जी०एस०टी०एन० न०</label>
                                                <input type="text" name="gst_no" id="gst_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo ($row_invoice['gst_no'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>ई-मेल आई.डी.</label>
                                                <input type="text" name="email_id" id="email_id"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo ($row_invoice['email_id'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>दूरभाष न०</label>
                                                <input type="text" name="mobile_number" id="mobile_number"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo ($row_invoice['mobile_number'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>वेबसाइट</label>
                                                <input type="text" name="website" id="website"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo ($row_invoice['website'] ?? ''); ?>">
                                            </div>
                                            <?php if (!empty($row_invoice['photo_id'])) { ?>
                                                <div class="col-sm-2 form-group">
                                                    <label>मुख्यालय की फोटो संलग्न करें</label>
                                                    <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                        name="society_photo" id="society_photo"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                </div>
                                                <div class="col-sm-2 form-group">
                                                    <img src="<?php echo htmlspecialchars($row_invoice['photo_id']); ?>"
                                                        class="img-fluid img-thumbnail" style="height:50px;"
                                                        id="society_photo_uploaded">
                                                    <label><a
                                                            href="<?php echo htmlspecialchars($row_invoice['photo_id']); ?>"
                                                            target="_blank">संलग्न फोटो देखें</a></label>
                                                </div>
                                            <?php } else { ?>
                                                <div class="col-sm-3 form-group">
                                                    <label>मुख्यालय की फोटो संलग्न करें</label>
                                                    <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                        name="society_photo" id="society_photo"
                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                </div>
                                            <?php } ?>


                                        </div>
                                        <h5
                                            style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                            1.1 शीर्ष संस्था के कार्यालय </h5>
                                        <br>

                                        <div class="card shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3 form-group">
                                                        <label>1. क्षेत्रीय कार्यालय</label>
                                                        <input type="text" name="no_of_zones" id="no_of_zones"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            value="<?php echo !empty($zone_data) ? count($zone_data) : ''; ?>"
                                                            oninput="updateOfficeRows(this.value)">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label>2. जिला कार्यालय</label>
                                                        <input type="text" name="global_prakhand_count"
                                                            id="global_prakhand_count" class="form-control"
                                                            value="<?php echo !empty($prakhand_data) ? count($prakhand_data) : ''; ?>"
                                                            oninput="updateSeparatePrakhandRows(this.value)">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label>3. सहकारी गैस सर्विस</label>
                                                        <input type="text" name="gas_service_count"
                                                            id="gas_service_count" class="form-control"
                                                            value="<?php echo !empty($gas_service_data) ? count($gas_service_data) : ''; ?>"
                                                            oninput="updateGasServiceRows(this.value)">
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label>4. यूनिट</label>
                                                        <input type="text" name="unit_count" id="unit_count"
                                                            class="form-control"
                                                            value="<?php echo !empty($unit_data) ? count($unit_data) : ''; ?>"
                                                            oninput="updateUnitRows(this.value)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive" id="zoneTableWrapper">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>नाम</th>
                                                        <th>दूरभाष न०</th>
                                                        <th>ई-मेल आई.डी.</th>
                                                        <th>पता</th>
                                                        <th>क्षेत्रीय कार्यालय की फोटो GPS टैग के संलग्न करें</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="zoneTbody">
                                                    <?php
                                                    if (!empty($zone_data)) {
                                                        foreach ($zone_data as $z) {

                                                            $img_path = '';
                                                            if (!empty($z['zone_image'])) {
                                                                $img_path = "user_data/" . $apex['sno'] . "/" . $z['zone_image'];
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><input type="text" name="zone_name[]"
                                                                        value="<?php echo $z['zone_name']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="zone_mobile[]"
                                                                        value="<?php echo $z['zone_mobile']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="zone_email[]"
                                                                        value="<?php echo $z['zone_email']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="zone_address[]"
                                                                        value="<?php echo $z['zone_address']; ?>"
                                                                        class="form-control"></td>

                                                                <td>
                                                                    <input type="file" name="zone_image[]" class="form-control">

                                                                    <?php if (!empty($z['zone_image'])) { ?>
                                                                        <br>
                                                                        <img src="<?php echo $img_path; ?>" width="80"
                                                                            style="margin-top:5px;">
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="table-responsive" id="prakhandTableWrapper">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>नाम</th>
                                                        <th>दूरभाष न०</th>
                                                        <th>ई-मेल आई.डी.</th>
                                                        <th>पता</th>
                                                        <th>जिला कार्यालय की फोटो GPS टैग के संलग्न करें</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="prakhandTbody">
                                                    <?php
                                                    if (!empty($prakhand_data)) {
                                                        foreach ($prakhand_data as $p) {

                                                            $img_path = '';
                                                            if (!empty($p['prakhand_image'])) {
                                                                $img_path = "user_data/" . $apex['sno'] . "/" . $p['prakhand_image'];
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><input type="text" name="prakhand_name[]"
                                                                        value="<?php echo $p['prakhand_name']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="prakhand_mobile[]"
                                                                        value="<?php echo $p['prakhand_mobile']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="prakhand_email[]"
                                                                        value="<?php echo $p['prakhand_email']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="prakhand_address[]"
                                                                        value="<?php echo $p['prakhand_address']; ?>"
                                                                        class="form-control"></td>

                                                                <td>
                                                                    <input type="file" name="prakhand_image[]"
                                                                        class="form-control">

                                                                    <?php if (!empty($p['prakhand_image']) && file_exists($img_path)) { ?>
                                                                        <br>
                                                                        <img src="<?php echo $img_path; ?>" width="80"
                                                                            style="margin-top:5px;">
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="table-responsive" id="gasServiceTableWrapper">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>नाम</th>
                                                        <th>दूरभाष न०</th>
                                                        <th>ई-मेल आई.डी.</th>
                                                        <th>पता</th>
                                                        <th>सहकारी गैस सर्विस की फोटो GPS टैग के संलग्न करें</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="gasServiceTbody">
                                                    <?php
                                                    if (!empty($gas_service_data)) {
                                                        foreach ($gas_service_data as $p) {

                                                            $img_path = '';
                                                            if (!empty($p['gas_service_image'])) {
                                                                $img_path = "user_data/" . $apex['sno'] . "/" . $p['gas_service_image'];
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><input type="text" name="gas_service_name[]"
                                                                        value="<?php echo $p['gas_service_name']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="gas_service_mobile[]"
                                                                        value="<?php echo $p['gas_service_mobile']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="gas_service_email[]"
                                                                        value="<?php echo $p['gas_service_email']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="gas_service_address[]"
                                                                        value="<?php echo $p['gas_service_address']; ?>"
                                                                        class="form-control"></td>

                                                                <td>
                                                                    <input type="file" name="gas_service_image[]"
                                                                        class="form-control">

                                                                    <?php if (!empty($p['gas_service_image']) && file_exists($img_path)) { ?>
                                                                        <br>
                                                                        <img src="<?php echo $img_path; ?>" width="80"
                                                                            style="margin-top:5px;">
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="table-responsive" id="unitTableWrapper">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>नाम</th>
                                                        <th>दूरभाष न०</th>
                                                        <th>ई-मेल आई.डी.</th>
                                                        <th>पता</th>
                                                        <th>यूनिट की फोटो GPS टैग के संलग्न करें</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="unitTbody">
                                                    <?php
                                                    if (!empty($unit_data)) {
                                                        foreach ($unit_data as $p) {

                                                            $img_path = '';
                                                            if (!empty($p['unit_image'])) {
                                                                $img_path = "user_data/" . $apex['sno'] . "/" . $p['unit_image'];
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><input type="text" name="unit_name[]"
                                                                        value="<?php echo $p['unit_name']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="unit_mobile[]"
                                                                        value="<?php echo $p['unit_mobile']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="unit_email[]"
                                                                        value="<?php echo $p['unit_email']; ?>"
                                                                        class="form-control"></td>

                                                                <td><input type="text" name="unit_address[]"
                                                                        value="<?php echo $p['unit_address']; ?>"
                                                                        class="form-control">
                                                                </td>

                                                                <td>
                                                                    <input type="file" name="unit_image[]" class="form-control">

                                                                    <?php if (!empty($p['unit_image']) && file_exists($img_path)) { ?>
                                                                        <br>
                                                                        <img src="<?php echo $img_path; ?>" width="80"
                                                                            style="margin-top:5px;">
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                        <?php }
                                                    } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!----------------2nd start-------------------------------------------------------->
                                <div class="step">
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
                                            // $row_6_2['count'] = 1;
                                            for ($i = 1; $i <= $row_6_2['count']; $i++) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-3 form-group">
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
                                                    <div class="col-sm-3 form-group">
                                                        <label>नाम</label>
                                                        <input type="text" name="sec_6_2_name_<?php echo $i; ?>"
                                                            id="sec_6_2_name_<?php echo $i; ?>"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control chk_text"
                                                            data-type="4.II नाम शब्दों में भरे"
                                                            value="<?php echo $row_6_2['sec_6_2_name_' . $i]; ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
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
                                    <h5
                                        style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                        2.1.मानव सम्पदा </h5>



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

                                    $postOptionsHTML = '';
                                    foreach ($posts as $p) {
                                        $postOptionsHTML .= '<option value="' . $p['sno'] . '">' . ($p['post_name']) . '</option>';
                                    }
                                    ?>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="human_resource_table">
                                            <thead class="table-light" style="background-color: #b8daff;">
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
                                                                    <?php echo ($p['post_name']); ?>
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
                                                            onclick="addHumanResourceRow();">नई
                                                            पंक्ति जोड़े [+]</button>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="staff_section" style="display:none;" class="mt-3">
                                        <h5
                                            style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                            कर्मचारी विवरण</h5>
                                        <div id="staff_rows"></div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-primary btn-sm me-3"
                                                onclick="uploadDocument()" style="margin-right:1rem;">Upload
                                                Document</button>
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="downloadExcel(<?php echo $row_invoice['sno']; ?>)"
                                                style="height: 40px;">Download Excel</button>
                                        </div>
                                    </div>

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
                                                                <?php echo ($p['post_name']); ?>
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
                                                    <select name="staff_qualification[]" class="form-control">
                                                        <option value="">--Select--</option>
                                                        <option value="Intermediate">इंटरमीडिएट</option>
                                                        <option value="Graduate">स्नातक</option>
                                                        <option value="PostGraduate">परास्नातक</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 form-group">
                                                    <label>Upload Image</label>

                                                    <div style="display:flex; align-items:center; gap:10px;">

                                                        <!-- Image Preview -->

                                                        <a href="#" target="_blank" class="image-link"
                                                            style="display:none;">
                                                            <img class="img-preview" src="" style="width:85px; height:85px; object-fit:cover;
                                                                     border:1px solid #ddd; padding:3px;">
                                                        </a>

                                                        <div style="flex:1;">
                                                            <input type="file" name="staff_image[]"
                                                                class="form-control staff-image-input" accept="image/*">

                                                            <input type="hidden" name="existing_staff_image[]"
                                                                class="existing-staff-image">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="step">
                                    <h4 class="mt-4">
                                        <img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                            style="height:40px; width:40px;">
                                        3. प्रमुख कार्यकलाप
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center common-head">वर्ष</th>
                                                    <th class="text-center common-head">धनराशि (लाख में)</th>
                                                    <th class="text-center common-head">विभागीय आपूर्ति</th>
                                                    <th class="text-center common-head">गेहूँ खरीद</th>
                                                    <th class="text-center common-head">धान खरीद</th>
                                                    <th class="text-center common-head">उर्वरक बिक्री</th>
                                                    <th class="text-center common-head">उर्वरक परिवहन</th>
                                                    <th class="text-center common-head">एलपीजी वितरण</th>
                                                    <th class="text-center common-head">ट्राईफेड/सिमफेड</th>
                                                    <th class="text-center common-head">सीपीपीएल/एएनपीएल</th>
                                                    <th class="text-center common-head">कार्यवाही</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sec_2_2_tbody">
                                                <?php for ($i = 1; $i <= $row_2_2['count']; $i++) { ?>
                                                    <tr id="sec_2_2_row_<?php echo $i; ?>">
                                                        <td>
                                                            <select name="sec_2_2_year_<?php echo $i; ?>"
                                                                id="sec_2_2_year_<?php echo $i; ?>" class="form-control">
                                                                <?php
                                                                $start = 2020;
                                                                $current = date('Y');
                                                                for ($y = $start; $y <= $current + 1; $y++) {
                                                                    $fy = $y . '-' . substr($y + 1, -2);
                                                                    $selected = ($row_2_2['sec_2_2_year_' . $i] == $fy) ? 'selected' : '';
                                                                    echo "<option value='$fy' $selected>$fy</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_2_2_amount_<?php echo $i; ?>"
                                                                id="sec_2_2_amount_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="धनराशि अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_amount_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_2_2_dept_supply_<?php echo $i; ?>"
                                                                id="sec_2_2_dept_supply_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="विभागीय आपूर्ति अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_dept_supply_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="sec_2_2_wheat_purchase_<?php echo $i; ?>"
                                                                id="sec_2_2_wheat_purchase_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="गेहूँ खरीद अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_wheat_purchase_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="sec_2_2_paddy_purchase_<?php echo $i; ?>"
                                                                id="sec_2_2_paddy_purchase_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="धान खरीद अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_paddy_purchase_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_2_2_fert_sales_<?php echo $i; ?>"
                                                                id="sec_2_2_fert_sales_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="उर्वरक बिक्री अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_fert_sales_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="sec_2_2_fert_transport_<?php echo $i; ?>"
                                                                id="sec_2_2_fert_transport_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="उर्वरक परिवहन अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_fert_transport_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_2_2_lpg_dist_<?php echo $i; ?>"
                                                                id="sec_2_2_lpg_dist_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="एलपीजी वितरण अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_lpg_dist_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                name="sec_2_2_trifed_simfed_<?php echo $i; ?>"
                                                                id="sec_2_2_trifed_simfed_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="ट्राईफेड/सिमफेड अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_trifed_simfed_' . $i]; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_2_2_cppl_anpl_<?php echo $i; ?>"
                                                                id="sec_2_2_cppl_anpl_<?php echo $i; ?>"
                                                                class="form-control chk_number"
                                                                data-type="सीपीपीएल/एएनपीएल अंकों में भरे"
                                                                value="<?php echo $row_2_2['sec_2_2_cppl_anpl_' . $i]; ?>">
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <?php if ($i == $row_2_2['count']) { ?>
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    id="sec_2_2_add_btn" onclick="sec_2_2_add_rows();">
                                                                    नई पंक्ति जोड़े [+]
                                                                </button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <input type="hidden" name="sec_2_2_id" id="sec_2_2_id"
                                        value="<?php echo $row_2_2['count']; ?>">
                                    <h5
                                        style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                        3.1. वर्षवार उपभोक्ता व्यवसाय </h5>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered table-striped" id="consumerBusinessTable">
                                            <thead class="table-light" style="background-color: #e9ecef;">
                                                <tr>
                                                    <th style="color: #000; width: 100px;">वर्ष</th>
                                                    <th style="color: #000; min-width: 150px;">मद</th>
                                                    <th style="color: #000; min-width: 100px;">लक्ष्य</th>
                                                    <th style="color: #000; min-width: 100px;">व्यवसाय</th>
                                                    <th style="color: #000; width: 80px;">कार्यवाही</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sec_2_3_tbody">
                                                <?php for ($i = 1; $i <= $row_2_3['count']; $i++): ?>

                                                    <tr id="sec_2_3_row_lpg_<?= $i ?>">
                                                        <?php if ($i == 1): ?>
                                                            <td rowspan="4" class="align-middle text-center fw-bold"
                                                                id="sec_2_3_year_cell_<?= $i ?>">
                                                                <select name="sec_2_3_year_<?= $i ?>"
                                                                    id="sec_2_3_year_<?= $i ?>"
                                                                    class="form-control text-center">
                                                                    <?php
                                                                    $start = 2020;
                                                                    $current = date('Y');
                                                                    for ($y = $start; $y <= $current + 1; $y++) {
                                                                        $fy = $y . '-' . substr($y + 1, -2);
                                                                        $selected = ($row_2_3['sec_2_3_year_' . $i] == $fy) ? 'selected' : '';
                                                                        echo "<option value='$fy' $selected>$fy</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </td>
                                                        <?php else: ?>
                                                            <td rowspan="4" class="align-middle text-center fw-bold"
                                                                id="sec_2_3_year_cell_<?= $i ?>">
                                                                <input type="text" name="sec_2_3_year_<?= $i ?>"
                                                                    id="sec_2_3_year_<?= $i ?>" class="form-control text-center"
                                                                    placeholder="वर्ष जैसे 2022-23"
                                                                    value="<?= $row_2_3['sec_2_3_year_' . $i] ?>">
                                                            </td>
                                                        <?php endif; ?>
                                                        <td>एल०पी०जी०</td>
                                                        <td><input type="text" name="sec_2_3_lpg_target_<?= $i ?>"
                                                                id="sec_2_3_lpg_target_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="लक्ष्य अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_lpg_target_' . $i] ?>"></td>
                                                        <td><input type="text" name="sec_2_3_lpg_business_<?= $i ?>"
                                                                id="sec_2_3_lpg_business_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="व्यवसाय अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_lpg_business_' . $i] ?>"></td>
                                                        <td rowspan="4" class="align-middle text-center"
                                                            id="sec_2_3_btn_cell_<?= $i ?>">
                                                            <?php if ($i == $row_2_3['count']): ?>
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    id="sec_2_3_add_btn" onclick="sec_2_3_add_rows();">
                                                                    नई पंक्ति जोड़े [+]
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>

                                                    <tr id="sec_2_3_row_fert_<?= $i ?>">
                                                        <td>उर्वरक</td>
                                                        <td><input type="text" name="sec_2_3_fert_target_<?= $i ?>"
                                                                id="sec_2_3_fert_target_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="लक्ष्य अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_fert_target_' . $i] ?>"></td>
                                                        <td><input type="text" name="sec_2_3_fert_business_<?= $i ?>"
                                                                id="sec_2_3_fert_business_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="व्यवसाय अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_fert_business_' . $i] ?>"></td>
                                                    </tr>

                                                    <tr id="sec_2_3_row_dept_<?= $i ?>">
                                                        <td>विभागीय आपूर्ति</td>
                                                        <td><input type="text" name="sec_2_3_dept_target_<?= $i ?>"
                                                                id="sec_2_3_dept_target_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="लक्ष्य अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_dept_target_' . $i] ?>"></td>
                                                        <td><input type="text" name="sec_2_3_dept_business_<?= $i ?>"
                                                                id="sec_2_3_dept_business_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="व्यवसाय अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_dept_business_' . $i] ?>"></td>
                                                    </tr>

                                                    <tr id="sec_2_3_row_total_<?= $i ?>">
                                                        <td><strong>योग</strong></td>
                                                        <td><input type="text" name="sec_2_3_total_target_<?= $i ?>"
                                                                id="sec_2_3_total_target_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="लक्ष्य अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_total_target_' . $i] ?>"></td>
                                                        <td><input type="text" name="sec_2_3_total_business_<?= $i ?>"
                                                                id="sec_2_3_total_business_<?= $i ?>"
                                                                class="form-control chk_number"
                                                                data-type="व्यवसाय अंकों में भरे"
                                                                value="<?= $row_2_3['sec_2_3_total_business_' . $i] ?>">
                                                        </td>
                                                    </tr>

                                                <?php endfor; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <input type="hidden" name="sec_2_3_id" id="sec_2_3_id"
                                        value="<?= $row_2_3['count'] ?>">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h5
                                                style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                                3.2. आडिट</h5>
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
                                        <div class="col-sm-3 form-group" id="sec_2_dividend_per" style="display:none">
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
                                    <h5
                                        style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                        3.3 व्यवसाय वृद्धि हेतु प्रस्तावित कार्यकलाप</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>प्रस्तावित कार्य योजनायें</label>
                                            <textarea name="sec_3_proposed_work_plans" class="form-control" rows="2"
                                                placeholder="प्रस्तावित कार्य योजनायें"><?php echo $row_3_new_1['sec_3_proposed_work_plans']; ?></textarea>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>ग्रेच्युटी (सेवानिवृत्त कर्मचारियों की)</label>
                                            <input type="text" name="sec_3_gratuity_retired" class="form-control"
                                                value="<?php echo $row_3_new_1['sec_3_gratuity_retired']; ?>">
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>नकदीकरण (सेवानिवृत्त कर्मचारियों की)</label>
                                            <input type="text" name="sec_3_encashment_retired" class="form-control"
                                                value="<?php echo $row_3_new_1['sec_3_encashment_retired']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="step">
                                    <h4 class="mt-4">
                                        <img src="images/logo/6.png" class="img-fluid stat-icon"
                                            style="height:40px; width:40px;">
                                        4. मूल्य समर्थन योजना
                                    </h4>
                                    <div class="table-responsive mt-3">
                                        <div class="table-responsive mt-3">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width:120px;">वर्ष</th>
                                                        <th>मद</th>
                                                        <th>लक्ष्य</th>
                                                        <th>व्यवसाय</th>
                                                        <th style="width:100px;">कार्यवाही</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="sec_2_4_price_support">

                                                    <?php for ($i = 1; $i <= $row_2_4['count']; $i++) { ?>

                                                        <tr>
                                                            <td rowspan="3">
                                                                <input type="text" name="sec_2_4_year_<?php echo $i; ?>"
                                                                    id="sec_2_4_year_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $row_2_4['sec_2_4_year_' . $i]; ?>">
                                                            </td>

                                                            <td>गेहूं खरीद</td>

                                                            <td>
                                                                <input type="text"
                                                                    name="sec_2_4_wheat_target_<?php echo $i; ?>"
                                                                    id="sec_2_4_wheat_target_<?php echo $i; ?>"
                                                                    class="form-control chk_number"
                                                                    value="<?php echo $row_2_4['sec_2_4_wheat_target_' . $i]; ?>">
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="sec_2_4_wheat_business_<?php echo $i; ?>"
                                                                    id="sec_2_4_wheat_business_<?php echo $i; ?>"
                                                                    class="form-control chk_number"
                                                                    value="<?php echo $row_2_4['sec_2_4_wheat_business_' . $i]; ?>">
                                                            </td>

                                                            <?php if ($i == $row_2_4['count']) { ?>

                                                                <td rowspan="3" class="text-center my-auto" id="sec_2_4_rows">

                                                                    <button type="button" class="btn btn-info"
                                                                        onclick="sec_2_4_add_rows()">
                                                                        नई पंक्ति जोड़ें [+]
                                                                    </button>

                                                                    <input type="hidden" name="sec_2_4_id" id="sec_2_4_id"
                                                                        value="<?php echo $row_2_4['count']; ?>">

                                                                </td>

                                                            <?php } ?>

                                                        </tr>

                                                        <tr>

                                                            <td>धान खरीद</td>

                                                            <td>
                                                                <input type="text"
                                                                    name="sec_2_4_paddy_target_<?php echo $i; ?>"
                                                                    id="sec_2_4_paddy_target_<?php echo $i; ?>"
                                                                    class="form-control chk_number"
                                                                    value="<?php echo $row_2_4['sec_2_4_paddy_target_' . $i]; ?>">
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="sec_2_4_paddy_business_<?php echo $i; ?>"
                                                                    id="sec_2_4_paddy_business_<?php echo $i; ?>"
                                                                    class="form-control chk_number"
                                                                    value="<?php echo $row_2_4['sec_2_4_paddy_business_' . $i]; ?>">
                                                            </td>

                                                        </tr>

                                                        <tr>

                                                            <td><strong>योग</strong></td>

                                                            <td>
                                                                <input type="text"
                                                                    name="sec_2_4_total_target_<?php echo $i; ?>"
                                                                    id="sec_2_4_total_target_<?php echo $i; ?>"
                                                                    class="form-control chk_number"
                                                                    value="<?php echo $row_2_4['sec_2_4_total_target_' . $i]; ?>">
                                                            </td>

                                                            <td>
                                                                <input type="text"
                                                                    name="sec_2_4_total_business_<?php echo $i; ?>"
                                                                    id="sec_2_4_total_business_<?php echo $i; ?>"
                                                                    class="form-control chk_number"
                                                                    value="<?php echo $row_2_4['sec_2_4_total_business_' . $i]; ?>">
                                                            </td>

                                                        </tr>

                                                    <?php } ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <h4>
                                        <img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        4.1 कार्य व व्यवसाय
                                    </h4>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover" id="business_matrix">
                                                <thead class="table-light" style="background-color: #b8daff;">
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
                                </div>

                                <div class="step">
                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 5. संस्था भवन/सम्पत्ति का विवरण</h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h5
                                                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                                    (I) संस्था भवन का स्वामित्व
                                                </h5>
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
                                                <h5
                                                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                                    (II) संस्था के भूखंड का विवरण
                                                </h5>
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
                                                <h5
                                                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                                    (III) भूखंड की चौहद्दी का विवरण
                                                </h5>
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
                                                <?php
                                                // Fetch Districts
                                                $districts = [];
                                                $sql_dist = "SELECT * FROM master_district ORDER BY district_name ASC";
                                                $res_dist = execute_query($sql_dist);
                                                if ($res_dist && mysqli_num_rows($res_dist) > 0) {
                                                    while ($row_dist = mysqli_fetch_assoc($res_dist)) {
                                                        $districts[] = $row_dist;
                                                    }
                                                }
                                                $district_options = '<option value="">--Select--</option>';
                                                foreach ($districts as $d) {
                                                    $district_options .= '<option value="' . $d['sno'] . '">' . $d['district_name'] . '</option>';
                                                }
                                                ?>

                                                <h5
                                                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 12px 20px; border-radius: 8px; font-weight: bold; color: #1565c0; margin: 20px 0 15px 0; border-left: 4px solid #1976d2;">
                                                    (IV) खाली पड़ी भूमि का विवरण
                                                </h5>
                                                <div id="sec_3_c">
                                                    <?php for ($i = 1; $i <= $row_3_5['sec_3_c_id']; $i++) { ?>
                                                        <div class="row mb-2 sec3c_row">
                                                            <!-- District -->
                                                            <div class="col-sm-2 form-group">
                                                                <label>जनपद</label>
                                                                <select name="sec_3_c_district_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <?php echo $district_options; ?>
                                                                </select>
                                                            </div>

                                                            <!-- Area -->
                                                            <div class="col-sm-2 form-group">
                                                                <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                                <input type="text" name="sec_3_c_area_<?php echo $i; ?>"
                                                                    class="form-control">
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>पहुच मार्ग का प्रकार</label>
                                                                <select name="sec_3_c_paved_road_<?php echo $i; ?>"
                                                                    id="sec_3_c_paved_road_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select--</option>
                                                                    <option value="ordinary">कच्ची सड़क</option>
                                                                    <option value="nh">नेशनल हाईवे</option>
                                                                    <option value="sh">स्टेट हाईवे</option>
                                                                    <option value="mdr">एम.डी.आर.</option>
                                                                    <option value="odr">ओ.डी.आर.</option>
                                                                    <option value="rural_road">ग्रामीण सड़क</option>
                                                                    <option value="other">अन्य</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>स्थान (समिति प्रांगण <br> या अन्य स्थान)</label>
                                                                <select name="sec_3_c_land_location_<?php echo $i; ?>"
                                                                    id="sec_3_c_land_location_<?php echo $i; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select-- </option>
                                                                    <option value="inpremise">समिति प्रांगण </option>
                                                                    <option value="other">अन्य स्थान </option>
                                                                </select>
                                                            </div>
                                                            <!-- Image Upload -->
                                                            <div class="col-sm-2 form-group">
                                                                <label>संस्था का फोटो GPS <br> टैग के साथ संलग्न करे</label>

                                                                <input type="file" name="sec_3_c_image_<?php echo $i; ?>"
                                                                    class="form-control" accept="image/*"
                                                                    onchange="emptylanddetailspreviewimage(this)">
                                                                <input type="hidden"
                                                                    name="sec_3_c_existing_image_<?php echo $i; ?>"
                                                                    value="<?php echo $row_3_5['sec_3_c_image_' . $i] ?? ''; ?>">

                                                                <img class="img-preview mt-2"
                                                                    style="max-width:120px;display:none;border:1px solid #ccc;padding:3px;">

                                                            </div>
                                                            <!-- Add / Remove Button -->
                                                            <div class="col-sm-1 form-group my-auto">
                                                                <?php if ($i == $row_3_5['sec_3_c_id']) { ?>
                                                                    <button type="button" class="btn btn-info"
                                                                        onclick="sec_3_c_add_rows();">नई पंक्ति जोड़ें
                                                                        [+]</button>
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
                            </div>
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
                                        मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी
                                        सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
                                    <button type="button" class="btn btn-danger" id="verification_button"
                                        disabled="disabled">सत्यापन के लिये आगे प्रेषित
                                        करें
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
                </div>
                <input type="hidden" id="latitude" name="latitude" value="<?php echo $row_invoice['latitude']; ?>">
                <input type="hidden" id="longitude" name="longitude" value="<?php echo $row_invoice['longitude']; ?>">
                <div id="q-box__buttons" class="text-center">
                    <button id="prev-btn" class="btn btn-info" type="button" onClick="save_draft()">Previous</button>
                    <button id="next-btn" class="btn btn-success" type="button" onClick="save_draft()">Next</button>
                    <button id="submit-btn" class="btn btn-danger" type="submit"
                        onClick="validate_input(); save_draft();">Submit</button>
                </div>

                <div class="text-left mt-3 mb-4">
                    <button class="btn btn-warning" type="button" onClick="save_draft()">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                </div>
                <input type="hidden" id="term" name="term" value="a">
                <input type="hidden" id="id" name="id" value="submit_form_upss">
                <input type="hidden" id="current_step_count" name="current_step_count" value="">
                <input type="hidden" id="survey_id" name="survey_id" value="<?php echo $row_invoice['sno']; ?>">
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


    function sec_6_2_add_rows() {
        var id = parseFloat($("#sec_6_2_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 1; i <= id; i++) {
            if ($("#sec_2_b_name_" + i).val() == '' || $("#sec_6_2__mob_no_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_2_b_name_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#sec_2_b_rows").remove();
        var txt = '<div class="row"><div class="col-sm-3 form-group"><label>पदनाम</label><select class="form-control" id="sec_6_2_designation_' + id + '" name="sec_6_2_designation_' + id + '"><option value="">--Select--</option><option value="अध्यक्ष">अध्यक्ष</option><option value="उपाध्यक्ष">उपाध्यक्ष</option><option value="संचालक">संचालक</option></select></div><div class="col-sm-3 form-group"><label>नाम</label><input type="text" name="sec_6_2_name_' + id + '" id="sec_6_2_name_' + id + '" class="form-control chk_text" data-type="नाम शब्दों में भरे"></div><div class="col-sm-3 form-group"><label>मोबाईल नंबर</label><input type="text" name="sec_6_2__mob_no_' + id + '" id="sec_6_2__mob_no_' + id + '" class="form-control chk_mobile" data-minlength="10" data-maxlength="10" data-type="10 अंकों मे भरे"></div><div class="col-sm-2 form-group my-auto" id="sec_2_b_rows"><button type="button" class="btn btn-info" onclick="sec_6_2_add_rows();">नई पंक्ति जोड़े [+]</button></div></div>';
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
            url: "scripts/ajax_upss.php",
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

        let html = `
    <div class="row mb-2 sec3c_row">

        <div class="col-sm-2 form-group">
            <label>जनपद</label>
            <select name="sec_3_c_district_${id}" class="form-control">
                <option value="">--Select--</option>
                <?php foreach ($districts as $d) { ?>
                    <option value="<?php echo $d['sno']; ?>">
                        <?php echo $d['district_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-sm-2 form-group">
            <label>क्षेत्रफल (हेक्टेयर में)</label>
            <input type="text" name="sec_3_c_area_${id}" class="form-control">
        </div>

        <div class="col-sm-2 form-group">
            <label>पहुच मार्ग का प्रकार</label>
            <select name="sec_3_c_paved_road_${id}" class="form-control">
                <option value="">--select--</option>
                <option value="ordinary">कच्ची सड़क</option>
                <option value="nh">नेशनल हाईवे</option>
                <option value="sh">स्टेट हाईवे</option>
                <option value="mdr">एम.डी.आर.</option>
                <option value="odr">ओ.डी.आर.</option>
                <option value="rural_road">ग्रामीण सड़क</option>
                <option value="other">अन्य</option>
            </select>
        </div>

        <div class="col-sm-2 form-group">
            <label>स्थान</label>
            <select name="sec_3_c_land_location_${id}" class="form-control">
                <option value="">--select--</option>
                <option value="inpremise">समिति प्रांगण</option>
                <option value="other">अन्य स्थान</option>
            </select>
        </div>

       <div class="col-sm-2 form-group">
<label>संस्था का फोटो GPS टैग के साथ</label>

<input type="file"
name="sec_3_c_image_${id}"
class="form-control"
accept="image/*"
onchange="emptylanddetailspreviewimage(this)">

<img class="img-preview mt-2"
style="max-width:120px;display:none;border:1px solid #ccc;padding:3px;">
</div>

        <div class="col-sm-1 form-group my-auto">
            <button type="button" class="btn btn-danger"
            onclick="this.closest('.sec3c_row').remove()">-</button>
        </div>

    </div>
    `;

        container.insertAdjacentHTML('beforeend', html);
    }

    function emptylanddetailspreviewimage(input) {

        var preview = input.parentElement.querySelector('.img-preview');

        if (input.files && input.files[0]) {

            var reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            }

            reader.readAsDataURL(input.files[0]);
        }

    }
</script>
<script>
    function updateOfficeRows(val) {
        var count = parseInt(val, 10);
        var wrapper = document.getElementById('zoneTableWrapper');
        var table = document.getElementById('officeContainer');
        if (!wrapper || !table) return;
        if (isNaN(count) || count < 1) {
            wrapper.style.display = 'none';
            return;
        } else {
            wrapper.style.display = 'block';
        }
        var currentRows = table.querySelectorAll('tbody.office-block');
        var diff = count - currentRows.length;
        if (diff > 0) {
            var template = currentRows[0];
            for (var i = 0; i < diff; i++) {
                var clone = template.cloneNode(true);
                var newIndex = currentRows.length + i + 1;
                clone.setAttribute('data-zone-index', newIndex);
                var inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(function (inp) {
                    if (inp.type !== 'file') {
                        inp.value = '';
                    } else {
                        inp.value = '';
                    }
                });
                table.appendChild(clone);
            }
        } else if (diff < 0) {
            while (currentRows.length > count) {
                table.removeChild(currentRows[currentRows.length - 1]);
                currentRows = table.querySelectorAll('tbody.office-block');
            }
        }
    }
    // If page loads with existing zone count, render blocks accordingly
    document.addEventListener('DOMContentLoaded', function () {
        var z = document.getElementById('no_of_zones');
        if (z && z.value) updateOfficeRows(z.value);
    });
    /* ================= ZONE ROW HANDLER ================= */
    function updateOfficeRows(count) {

        count = parseInt(count) || 0;

        var wrapper = document.getElementById("zoneTableWrapper");
        var tbody = document.getElementById("zoneTbody");

        if (!wrapper || !tbody) return;

        var current = tbody.rows.length;

        if (count <= 0) {
            wrapper.style.display = "none";
            return;
        }

        wrapper.style.display = "block";

        // ADD ONLY NEW ROWS
        if (count > current) {
            for (var i = current; i < count; i++) {
                var row = tbody.insertRow();
                row.innerHTML = `
                <td><input type="text" name="zone_name[]" class="form-control" placeholder="क्षेत्रीय कार्यालय का नाम"></td>
                <td><input type="text" name="zone_mobile[]" class="form-control" placeholder="क्षेत्रीय कार्यालय का दूरभाष न०"></td>
                <td><input type="text" name="zone_email[]" class="form-control" placeholder="क्षेत्रीय कार्यालय का ई-मेल आई.डी."></td>
                <td><input type="text" name="zone_address[]" class="form-control" placeholder="क्षेत्रीय कार्यालय का पता"></td>
                <td><input type="file" name="zone_image[]" class="form-control"></td>
            `;
            }
        }

        // REMOVE ONLY EXTRA ROWS
        if (count < current) {
            for (var i = current; i > count; i--) {
                tbody.deleteRow(i - 1);
            }
        }
    }

    /* ================= PRAKHAND ROW HANDLER ================= */
    function updateSeparatePrakhandRows(count) {

        count = parseInt(count) || 0;

        var wrapper = document.getElementById("prakhandTableWrapper");
        var tbody = document.getElementById("prakhandTbody");

        if (!wrapper || !tbody) return;

        var current = tbody.rows.length;

        /* ===== Hide Table If 0 ===== */
        if (count <= 0) {
            wrapper.style.display = "none";
            return;   // do NOT clear tbody
        }

        wrapper.style.display = "block";

        /* ===== ADD ONLY NEW ROWS ===== */
        if (count > current) {
            for (var i = current; i < count; i++) {

                var row = tbody.insertRow();

                row.innerHTML = `
                <td><input type="text" name="prakhand_name[]" class="form-control" placeholder="जिला कार्यालय का नाम"></td>
                <td><input type="text" name="prakhand_mobile[]" class="form-control" placeholder="जिला कार्यालय का दूरभाष"></td>
                <td><input type="text" name="prakhand_email[]" class="form-control" placeholder="जिला कार्यालय का ईमेल"></td>
                <td><input type="text" name="prakhand_address[]" class="form-control" placeholder="जिला कार्यालय का पता"></td>
                <td><input type="file" name="prakhand_image[]" class="form-control"></td>
            `;
            }
        }

        /* ===== REMOVE ONLY EXTRA ROWS ===== */
        if (count < current) {
            for (var i = current; i > count; i--) {
                tbody.deleteRow(i - 1);
            }
        }
    }
    function updateGasServiceRows(count) {

        count = parseInt(count) || 0;

        var wrapper = document.getElementById("gasServiceTableWrapper");
        var tbody = document.getElementById("gasServiceTbody");

        if (!wrapper || !tbody) return;

        var current = tbody.rows.length;

        /* ===== Hide Table If 0 ===== */
        if (count <= 0) {
            wrapper.style.display = "none";
            return;   // do NOT clear tbody
        }

        wrapper.style.display = "block";

        /* ===== ADD ONLY NEW ROWS ===== */
        if (count > current) {
            for (var i = current; i < count; i++) {

                var row = tbody.insertRow();

                row.innerHTML = `
                <td><input type="text" name="gas_service_name[]" class="form-control" placeholder="सहकारी गैस सर्विस का नाम"></td>
                <td><input type="text" name="gas_service_mobile[]" class="form-control" placeholder="सहकारी गैस सर्विस का दूरभाष"></td>
                <td><input type="text" name="gas_service_email[]" class="form-control" placeholder="सहकारी गैस सर्विस का ईमेल"></td>
                <td><input type="text" name="gas_service_address[]" class="form-control" placeholder="सहकारी गैस सर्विस का पता"></td>
                <td><input type="file" name="gas_service_image[]" class="form-control"></td>
            `;
            }
        }

        /* ===== REMOVE ONLY EXTRA ROWS ===== */
        if (count < current) {
            for (var i = current; i > count; i--) {
                tbody.deleteRow(i - 1);
            }
        }
    }
    function updateUnitRows(count) {

        count = parseInt(count) || 0;

        var wrapper = document.getElementById("unitTableWrapper");
        var tbody = document.getElementById("unitTbody");

        if (!wrapper || !tbody) return;

        var current = tbody.rows.length;

        /* ===== Hide Table If 0 ===== */
        if (count <= 0) {
            wrapper.style.display = "none";
            return;   // do NOT clear tbody
        }

        wrapper.style.display = "block";

        /* ===== ADD ONLY NEW ROWS ===== */
        if (count > current) {
            for (var i = current; i < count; i++) {

                var row = tbody.insertRow();

                row.innerHTML = `
                <td><input type="text" name="unit_name[]" class="form-control" placeholder="यूनिट का नाम"></td>
                <td><input type="text" name="unit_mobile[]" class="form-control" placeholder="यूनिट का दूरभाष"></td>
                <td><input type="text" name="unit_email[]" class="form-control" placeholder="यूनिट का ईमेल"></td>
                <td><input type="text" name="unit_address[]" class="form-control" placeholder="यूनिट का पता"></td>
                <td><input type="file" name="unit_image[]" class="form-control"></td>
            `;
            }
        }

        /* ===== REMOVE ONLY EXTRA ROWS ===== */
        if (count < current) {
            for (var i = current; i > count; i--) {
                tbody.deleteRow(i - 1);
            }
        }
    }


    /* ================= SAFE PAGE LOAD ================= */
    document.addEventListener("DOMContentLoaded", function () {

        var zoneInput = document.getElementById("no_of_zones");
        var prakhandInput = document.getElementById("global_prakhand_count");
        var gasServiceInput = document.getElementById("gas_service_count");
        var unitInput = document.getElementById("unit_count");

        var zoneWrapper = document.getElementById("zoneTableWrapper");
        var prakhandWrapper = document.getElementById("prakhandTableWrapper");
        var gasServiceWrapper = document.getElementById("gasServiceTableWrapper");
        var unitWrapper = document.getElementById("unitTableWrapper");

        if (zoneInput && zoneInput.value !== "") {
            zoneWrapper.style.display = "block";
        }

        if (prakhandInput && prakhandInput.value !== "") {
            prakhandWrapper.style.display = "block";
        }

        if (gasServiceInput && gasServiceInput.value !== "") {
            gasServiceWrapper.style.display = "block";
        }

        if (unitInput && unitInput.value !== "") {
            unitWrapper.style.display = "block";
        }

    });

</script>

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
    function uploadDocument() {
        alert('Upload Document functionality can be implemented here!');
    }

    function addHumanResourceRow() {
        let tbody = document.getElementBy'Id('human_resource_rows');
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

<script type="text/javascript" src="js/multistepform_upss.js?v=1"></script>
<!-- <script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script> -->

<?php
page_footer_start();
page_footer_end();
?>

<script>
    function removeHumanResourceRow(btn) {
        let row = btn.closest('tr');
        let rowIndex = Array.from(row.parentNode.children).indexOf(row);

        // Remove corresponding staff block
        let staffBlock = document.querySelector('.staff_block[data-row="' + rowIndex + '"]');
        if (staffBlock) staffBlock.remove();

        row.remove();
    }
    function updateStaffSection(elem) {

        let row = elem.closest('.human_row');
        let tbody = document.getElementById('human_resource_rows');
        let rowIndex = Array.from(tbody.children).indexOf(row);

        let staffTypeSelect = row.querySelector('select[name="staff_type[]"]');
        let postSelect = row.querySelector('select[name="post_id[]"]');
        let sanctionedInput = row.querySelector('input[name="sanctioned_post[]"]');

        let staffTypeText = staffTypeSelect.options[staffTypeSelect.selectedIndex]?.text || '';
        let postText = postSelect.options[postSelect.selectedIndex]?.text || '';
        let postValue = postSelect.value;
        let sanctioned = parseInt(sanctionedInput.value) || 0;

        let mainContainer = document.getElementById('staff_rows');
        document.getElementById('staff_section').style.display = 'block';

        // Remove old block of this row only
        let oldBlock = document.querySelector('.staff_block[data-row="' + rowIndex + '"]');
        if (oldBlock) oldBlock.remove();

        if (postValue && sanctioned > 0) {

            let blockWrapper = document.createElement('div');
            blockWrapper.classList.add('staff_block');
            blockWrapper.setAttribute('data-row', rowIndex);
            blockWrapper.style.marginBottom = "20px";

            // 🔷 Add Heading
            let heading = document.createElement('div');
            heading.style.background = "#f1f8ff";
            heading.style.padding = "10px 15px";
            heading.style.borderLeft = "4px solid #0d6efd";
            heading.style.borderRadius = "6px";
            heading.style.marginBottom = "15px";
            heading.style.fontWeight = "600";
            heading.innerHTML = `
            ${staffTypeText} | ${postText} | स्वीकृत पद: ${sanctioned}
        `;

            blockWrapper.appendChild(heading);

            // Generate Staff Rows
            for (let i = 0; i < sanctioned; i++) {
                let template = document.getElementById('staff_row_template').cloneNode(true);
                template.style.display = 'block';
                template.removeAttribute('id');

                template.querySelector('.staff_post_name').value = postValue;

                blockWrapper.appendChild(template);
            }

            mainContainer.appendChild(blockWrapper);
        }

        // Hide section if empty
        if (mainContainer.children.length === 0) {
            document.getElementById('staff_section').style.display = 'none';
        }
    }

    function uploadDocument() {
        alert('Upload Document functionality can be implemented here!');
    }

    function downloadExcel() {
        var survey_id = "<?php echo $row_invoice['sno']; ?>";
        window.location.href = "download_hr_excel.php?survey_id=" + survey_id;
        // alert('Download Excel functionality can be implemented here!22222');
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        let rows = document.querySelectorAll('#human_resource_rows .human_row');
        rows.forEach(function (row) {

            console.log(rows);

            let postSelect = row.querySelector('select[name="post_id[]"]');
            let sanctionedInput = row.querySelector('input[name="sanctioned_post[]"]');

            if (postSelect.value && sanctionedInput.value > 0) {
                updateStaffSection(postSelect);
            }

        });

    });
</script>

<script>
    var existingHRData = <?php echo json_encode(array_values($prefillData)); ?>;
    document.addEventListener("DOMContentLoaded", function () {

        if (typeof existingHRData !== 'undefined' && existingHRData.length > 0) {

            let tbody = document.getElementById('human_resource_rows');
            tbody.innerHTML = ''; // clear default empty row

            existingHRData.forEach(function (hr, index) {

                // ---- CREATE MAIN ROW ----
                let row = document.createElement('tr');
                row.classList.add('human_row');

                row.innerHTML = `
                <td>
                    <select name="staff_type[]" class="form-control"
                        onchange="updateStaffSection(this)">
                        <option value="">--Select--</option>
                        <option value="tech" ${hr.staff_type == 'tech' ? 'selected' : ''}>Technical</option>
                        <option value="nontech" ${hr.staff_type == 'nontech' ? 'selected' : ''}>Non-Technical</option>
                    </select>
                </td>

                <td>
                    <select name="post_id[]" class="form-control post-select"
                        onchange="updateStaffSection(this)">
                        <option value="">--Select--</option>
                        <?php echo $postOptionsHTML; ?>
                    </select>
                </td>

                <td>
                    <input type="number" name="sanctioned_post[]"
                        value="${hr.sanctioned_post}"
                        class="form-control"
                        onchange="updateStaffSection(this)">
                </td>

                <td>
                    <input type="number" name="vacant_post[]"
                        value="${hr.vacant_post}"
                        class="form-control">
                </td>

                <td class="text-center">
                    ${index == 0 ?
                        `<button type="button" class="btn btn-info btn-sm"
                            onclick="addHumanResourceRow();">
                            नई पंक्ति जोड़े [+]
                        </button>`
                        :
                        `<button type="button" class="btn btn-danger btn-sm"
                            onclick="removeHumanResourceRow(this)">-</button>`
                    }
                </td>
            `;

                tbody.appendChild(row);

                // set selected post after adding
                row.querySelector('select[name="post_id[]"]').value = hr.hr_post_id;

            });

            // ---- NOW GENERATE STAFF BLOCKS ----
            setTimeout(function () {

                let rows = document.querySelectorAll('#human_resource_rows .human_row');

                existingHRData.forEach(function (hr, index) {

                    let row = rows[index];
                    let postSelect = row.querySelector('select[name="post_id[]"]');

                    updateStaffSection(postSelect);

                    // Fill staff details
                    let staffBlocks = document.querySelectorAll('.staff_block')[index];
                    let staffRows = staffBlocks.querySelectorAll('.staff_row');

                    hr.staff_members.forEach(function (staff, i) {

                        let current = staffRows[i];

                        current.querySelector('select[name="staff_post_name[]"]').value = staff.staff_post_id;
                        current.querySelector('input[name="staff_name[]"]').value = staff.staff_name;
                        current.querySelector('input[name="staff_sthiti[]"]').value = staff.staff_sthiti;
                        current.querySelector('input[name="staff_father[]"]').value = staff.staff_father;
                        current.querySelector('input[name="staff_dob[]"]').value = staff.staff_dob;
                        current.querySelector('input[name="staff_mobile[]"]').value = staff.staff_mobile;
                        current.querySelector('select[name="staff_qualification[]"]').value = staff.staff_qualification;

                        if (staff.staff_image && staff.staff_image !== "") {

                            let preview = current.querySelector(".img-preview");
                            let imageLink = current.querySelector(".image-link");
                            let hiddenInput = current.querySelector(".existing-staff-image");

                            var imagePath = "user_data/staff_" + staff.survey_id + "/" + staff.staff_image;

                            preview.src = imagePath;
                            imageLink.href = imagePath;

                            preview.style.display = "block";
                            imageLink.style.display = "inline-block";

                            hiddenInput.value = staff.staff_image;
                        }

                    });

                });

            }, 200);

        }
    });
</script>

<script>
    document.addEventListener("change", function (e) {

        if (e.target.classList.contains("staff-image-input")) {

            let input = e.target;
            let file = input.files[0];

            let container = input.closest(".form-group");
            let preview = container.querySelector(".img-preview");

            if (file) {
                let reader = new FileReader();

                reader.onload = function (event) {
                    preview.src = event.target.result;
                    preview.style.display = "block";
                };

                reader.readAsDataURL(file);
            }
        }
    });
</script>

<script>
    let businessData = <?php echo json_encode($business_data ?? []); ?>;
    document.addEventListener("DOMContentLoaded", function () {

        if (typeof businessData !== "undefined" && businessData.length > 0) {

            let tbody = document.querySelector("#business_matrix tbody");
            tbody.innerHTML = ""; // remove default row

            let total = businessData.length;

            businessData.forEach(function (item, index) {

                let rowNumber = index + 1;
                let isLast = (rowNumber === total);

                let yearOptions = "";

                for (let y = 2020; y <= 2030; y++) {
                    let fy = y + "-" + (y + 1);
                    let selected = (fy === item.business_year) ? "selected" : "";
                    yearOptions += `<option value="${fy}" ${selected}>${fy}</option>`;
                }

                let rowHTML = `
            <tr class="business_matrix_row">
                <td>
                    <select name="business_year_${rowNumber}" class="form-control">
                        <option value="">---वित्तीय वर्ष---</option>
                        ${yearOptions}
                    </select>
                </td>

                <td>
                    <input type="text"
                           name="business_description_${rowNumber}"
                           class="form-control"
                           value="${item.business_description || ''}">
                </td>

                <td>
                    <input type="text"
                           name="business_turnover_${rowNumber}"
                           class="form-control"
                           value="${item.business_turnover || ''}">
                </td>

                <td>
                    <input type="text"
                           name="business_target_${rowNumber}"
                           class="form-control"
                           value="${item.business_target || ''}">
                </td>

                <td>
                    <input type="text"
                           name="business_achievement_${rowNumber}"
                           class="form-control"
                           value="${item.business_achievement || ''}">
                </td>

                <td class="text-center">
                    ${isLast ? `
                        <button type="button"
                                class="btn btn-info btn-sm"
                                onclick="business_matrix_add_row();">
                            नई पंक्ति जोड़ें [+]
                        </button>
                        <input type="hidden"
                               name="other_business_id"
                               id="other_business_id"
                               value="${rowNumber}">
                    ` : `
                        <button type="button"
                                class="btn btn-danger btn-sm"
                                onclick="$(this).closest('tr').remove();">
                            -
                        </button>
                    `}
                </td>
            </tr>`;

                tbody.insertAdjacentHTML("beforeend", rowHTML);
            });

        }
    });
</script>

<script>

    let financialData = <?php echo json_encode($structured_financial); ?>;

    let yearCount = 3;
    let lastYear = 2024; // last static year

    document.addEventListener("DOMContentLoaded", function () {

        let years = Object.keys(financialData);
        let rowIndex = 1;

        years.forEach(function (year) {

            let startYear = parseInt(year.split('-')[0]);

            if (startYear > lastYear) {
                lastYear = startYear;
            }

            // create row if not present
            if (!document.querySelector('[name="financial_year_label_' + rowIndex + '"]')) {
                addFinancialRow(year);
            }

            let data = financialData[year];

            document.querySelector('[name="financial_year_label_' + rowIndex + '"]').value = year;

            document.querySelector('[name="sec_3_profit_loss_' + rowIndex + '"]').value =
                data.annual.status;

            document.querySelector('[name="sec_3_gross_amount_' + rowIndex + '"]').value =
                data.annual.gross_amount;

            document.querySelector('[name="sec_3_net_amount_' + rowIndex + '"]').value =
                data.annual.net_amount;

            document.querySelector('[name="sec_3_accumulated_' + rowIndex + '"]').value =
                data.accumulated.status;

            document.querySelector('[name="sec_3_acc_gross_amount_' + rowIndex + '"]').value =
                data.accumulated.gross_amount;

            document.querySelector('[name="sec_3_acc_net_amount_' + rowIndex + '"]').value =
                data.accumulated.net_amount;

            rowIndex++;

        });

        yearCount = years.length;

    });

    function addFinancialRow(prefillYear = null) {

        yearCount++;

        let yearLabel;

        if (prefillYear) {
            yearLabel = prefillYear;
            lastYear = parseInt(prefillYear.split('-')[0]);
        } else {

            lastYear++;
            let endYear = (lastYear + 1).toString().slice(-2);
            yearLabel = lastYear + "-" + endYear;
        }
        let tbody = document.querySelector("#financialMatrixTable tbody");
        let groupId = "year_group_" + yearCount;
        let html = `
<tr class="${groupId}">
    <td rowspan="2">
        ${yearLabel}
        <input type="hidden" name="financial_year_label_${yearCount}" value="${yearLabel}">
    </td>

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

    <td rowspan="2" class="text-center">
        <button type="button" class="btn btn-danger btn-sm"
        onclick="removeFinancialRow('${groupId}')">-</button>
    </td>
</tr>

<tr class="${groupId}">

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

    }

    document.getElementById("addYearRowBtn").addEventListener("click", function () {
        addFinancialRow();
    });

    function removeFinancialRow(groupId) {

        let allDynamicRows = document.querySelectorAll('[class^="year_group_"]');
        let lastGroup = allDynamicRows[allDynamicRows.length - 1].classList[0];

        if (groupId !== lastGroup) {
            alert("कृपया पहले अंतिम वर्ष हटाएँ!");
            return;
        }

        let rows = document.querySelectorAll("." + groupId);

        rows.forEach(function (row) {
            row.remove();
        });

    }
</script>

<script>
    let emptyLandData = <?php echo json_encode($empty_land_data ?? []); ?>;
    let surveyId = "<?php echo $row_invoice['sno']; ?>";

    let rowCount = 1;

    /* ===============================
       CREATE ROW
    ================================ */

    function createEmptyLandRow(data = {}) {

        rowCount++;
        document.getElementById("sec_3_c_id").value = rowCount;
        let district = data.sec_3_c_district ?? "";
        let area = data.sec_3_c_area ?? "";
        let road = data.sec_3_c_paved_road ?? "";
        let location = data.sec_3_c_land_location ?? "";
        let image = data.sec_3_c_image ?? "";

        let imagePath = image
            ? "user_data/empty_land_" + surveyId + "/" + image
            : "";

        let html = `
            <div class="row mb-2 sec3c_row">
            <div class="col-sm-2 form-group">
            <label>जनपद</label>
            <select name="sec_3_c_district_${rowCount}" class="form-control">
            <option value="">--Select--</option>
            <?php foreach ($districts as $d) { ?>
            <option value="<?php echo $d['sno']; ?>">
            <?php echo $d['district_name']; ?>
            </option>
            <?php } ?>
            </select>
            </div>
            <div class="col-sm-2 form-group">
            <label>क्षेत्रफल (हेक्टेयर में)</label>
            <input type="text"
            name="sec_3_c_area_${rowCount}"
            value="${area}"
            class="form-control">
            </div>
            <div class="col-sm-2 form-group">
            <label>पहुच मार्ग का प्रकार</label>
            <select name="sec_3_c_paved_road_${rowCount}" class="form-control">
            <option value="">--select--</option>
            <option value="ordinary">कच्ची सड़क</option>
            <option value="nh">नेशनल हाईवे</option>
            <option value="sh">स्टेट हाईवे</option>
            <option value="mdr">एम.डी.आर.</option>
            <option value="odr">ओ.डी.आर.</option>
            <option value="rural_road">ग्रामीण सड़क</option>
            <option value="other">अन्य</option>
            </select>
            </div>
            <div class="col-sm-2 form-group">
            <label>स्थान</label>
            <select name="sec_3_c_land_location_${rowCount}" class="form-control">
            <option value="">--select--</option>
            <option value="inpremise">समिति प्रांगण</option>
            <option value="other">अन्य स्थान</option>
            </select>
            </div>
            <div class="col-sm-2 form-group">
            <label>संस्था का फोटो GPS टैग के साथ संलग्न करे</label>
            <input type="file"
            name="sec_3_c_image_${rowCount}"
            class="form-control"
            accept="image/*"
            onchange="emptylanddetailspreviewimage(this)">
            <img class="img-preview mt-2"
            src="${imagePath}"
            style="max-width:120px; ${image ? 'display:block' : 'display:none'}; border:1px solid #ccc;padding:3px;">
            <input type="hidden"
            name="sec_3_c_existing_image_${rowCount}"
            value="${image}">
            </div>
            <div class="col-sm-1 form-group my-auto">
            <button type="button"
            class="btn btn-danger"
            onclick="removeSec3cRow(this)">
            -
            </button>
            </div>
            </div>`;
        document.getElementById("sec_3_c").insertAdjacentHTML("beforeend", html);

        /* SET SELECT VALUES */
        let row = document.querySelectorAll(".sec3c_row")[rowCount - 1];
        if (row) {
            row.querySelector(`select[name="sec_3_c_district_${rowCount}"]`).value = district;
            row.querySelector(`select[name="sec_3_c_paved_road_${rowCount}"]`).value = road;
            row.querySelector(`select[name="sec_3_c_land_location_${rowCount}"]`).value = location;
        }
    }

    /* ===============================
       ADD NEW ROW
    ================================ */

    function sec_3_c_add_rows() {
        createEmptyLandRow();
    }

    /* ===============================
       REMOVE ROW
    ================================ */

    function removeSec3cRow(btn) {

        btn.closest('.sec3c_row').remove();

    }

    /* ===============================
       IMAGE PREVIEW
    ================================ */

    function emptylanddetailspreviewimage(input) {

        let preview = input.parentElement.querySelector('.img-preview');

        if (input.files && input.files[0]) {

            let reader = new FileReader();

            reader.onload = function (e) {

                preview.src = e.target.result;
                preview.style.display = "block";

            }

            reader.readAsDataURL(input.files[0]);

        }

    }

    /* ===============================
       PREFILL DATA FOR EMPTY LAND
    ================================ */

    window.onload = function () {

        if (emptyLandData.length > 0) {

            emptyLandData.forEach(function (row) {

                createEmptyLandRow(row);

            });

        }

    }

    function sec_2_2_add_rows() {
        var id = parseFloat($("#sec_2_2_id").val());
        if (!id) { id = 0; }

        // Validate all existing rows before adding new one
        for (var i = 1; i <= id; i++) {
            if ($("#sec_2_2_year_" + i).val() == '' || $("#sec_2_2_amount_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " में वर्ष या धनराशि खाली है");
                $("#sec_2_2_year_" + i).focus();
                return;
            }
        }

        id = id + 1;

        // Remove add button from previous last row
        $("#sec_2_2_add_btn").remove();

        // Append only a new <tr> row into the existing tbody
        var tr = '<tr id="sec_2_2_row_' + id + '">'
            + '<td><select name="sec_2_2_year_' + id + '" id="sec_2_2_year_' + id + '" class="form-control">'
            + '<option value="">वर्ष चुनें</option>'
            + '<option value="2020-21">2020-21</option>'
            + '<option value="2021-22">2021-22</option>'
            + '<option value="2022-23">2022-23</option>'
            + '<option value="2023-24">2023-24</option>'
            + '<option value="2024-25">2024-25</option>'
            + '<option value="2025-26">2025-26</option>'
            + '<option value="2026-27">2026-27</option>'
            + '</select></td>'
            + '<td><input type="text" name="sec_2_2_amount_' + id + '" id="sec_2_2_amount_' + id + '" class="form-control chk_number" data-type="धनराशि अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_dept_supply_' + id + '" id="sec_2_2_dept_supply_' + id + '" class="form-control chk_number" data-type="विभागीय आपूर्ति अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_wheat_purchase_' + id + '" id="sec_2_2_wheat_purchase_' + id + '" class="form-control chk_number" data-type="गेहूँ खरीद अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_paddy_purchase_' + id + '" id="sec_2_2_paddy_purchase_' + id + '" class="form-control chk_number" data-type="धान खरीद अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_fert_sales_' + id + '" id="sec_2_2_fert_sales_' + id + '" class="form-control chk_number" data-type="उर्वरक बिक्री अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_fert_transport_' + id + '" id="sec_2_2_fert_transport_' + id + '" class="form-control chk_number" data-type="उर्वरक परिवहन अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_lpg_dist_' + id + '" id="sec_2_2_lpg_dist_' + id + '" class="form-control chk_number" data-type="एलपीजी वितरण अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_trifed_simfed_' + id + '" id="sec_2_2_trifed_simfed_' + id + '" class="form-control chk_number" data-type="ट्राईफेड/सिमफेड अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_2_cppl_anpl_' + id + '" id="sec_2_2_cppl_anpl_' + id + '" class="form-control chk_number" data-type="सीपीपीएल/एएनपीएल अंकों में भरे"></td>'
            + '<td class="text-center align-middle"><button type="button" class="btn btn-info btn-sm" id="sec_2_2_add_btn" onclick="sec_2_2_add_rows();">नई पंक्ति जोड़े [+]</button></td>'
            + '</tr>';

        $("#sec_2_2_tbody").append(tr);
        $("#sec_2_2_id").val(id);
    }
    function sec_2_3_add_rows() {
        var id = parseFloat($("#sec_2_3_id").val());
        if (!id) { id = 0; }

        // Validate last row before adding new one
        if ($("#sec_2_3_year_" + id).val() == '') {
            alert("पंक्ति संख्या " + id + " में वर्ष खाली है");
            $("#sec_2_3_year_" + id).focus();
            return;
        }
        if ($("#sec_2_3_lpg_target_" + id).val() == '' && $("#sec_2_3_lpg_business_" + id).val() == '') {
            alert("पंक्ति संख्या " + id + " में एल०पी०जी० लक्ष्य या व्यवसाय खाली है");
            $("#sec_2_3_lpg_target_" + id).focus();
            return;
        }

        id = id + 1;

        // Remove the add button from previous last row
        $("#sec_2_3_add_btn").remove();

        var tr = ''
            // LPG row — with year cell (rowspan=4) and action button (rowspan=4)
            + '<tr id="sec_2_3_row_lpg_' + id + '">'
            + '<td rowspan="4" class="align-middle text-center" id="sec_2_3_year_cell_' + id + '">'
            + '<select name="sec_2_3_year_' + id + '" id="sec_2_3_year_' + id + '" class="form-control text-center">'
            + '<option value="">वर्ष चुनें</option>'
            + '<option value="2020-21">2020-21</option>'
            + '<option value="2021-22">2021-22</option>'
            + '<option value="2022-23">2022-23</option>'
            + '<option value="2023-24">2023-24</option>'
            + '<option value="2024-25">2024-25</option>'
            + '<option value="2025-26">2025-26</option>'
            + '<option value="2026-27">2026-27</option>'
            + '</select>'
            + '<td>एल०पी०जी०</td>'
            + '<td><input type="text" name="sec_2_3_lpg_target_' + id + '" id="sec_2_3_lpg_target_' + id + '" class="form-control chk_number" data-type="लक्ष्य अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_3_lpg_business_' + id + '" id="sec_2_3_lpg_business_' + id + '" class="form-control chk_number" data-type="व्यवसाय अंकों में भरे"></td>'
            + '<td rowspan="4" class="align-middle text-center" id="sec_2_3_btn_cell_' + id + '">'
            + '<button type="button" class="btn btn-info btn-sm" id="sec_2_3_add_btn" onclick="sec_2_3_add_rows();">नई पंक्ति जोड़े [+]</button>'
            + '</td>'
            + '</tr>'
            // Fertilizer row
            + '<tr id="sec_2_3_row_fert_' + id + '">'
            + '<td>उर्वरक</td>'
            + '<td><input type="text" name="sec_2_3_fert_target_' + id + '" id="sec_2_3_fert_target_' + id + '" class="form-control chk_number" data-type="लक्ष्य अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_3_fert_business_' + id + '" id="sec_2_3_fert_business_' + id + '" class="form-control chk_number" data-type="व्यवसाय अंकों में भरे"></td>'
            + '</tr>'
            // Dept Supply row
            + '<tr id="sec_2_3_row_dept_' + id + '">'
            + '<td>विभागीय आपूर्ति</td>'
            + '<td><input type="text" name="sec_2_3_dept_target_' + id + '" id="sec_2_3_dept_target_' + id + '" class="form-control chk_number" data-type="लक्ष्य अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_3_dept_business_' + id + '" id="sec_2_3_dept_business_' + id + '" class="form-control chk_number" data-type="व्यवसाय अंकों में भरे"></td>'
            + '</tr>'
            // Total row
            + '<tr id="sec_2_3_row_total_' + id + '">'
            + '<td><strong>योग</strong></td>'
            + '<td><input type="text" name="sec_2_3_total_target_' + id + '" id="sec_2_3_total_target_' + id + '" class="form-control chk_number" data-type="लक्ष्य अंकों में भरे"></td>'
            + '<td><input type="text" name="sec_2_3_total_business_' + id + '" id="sec_2_3_total_business_' + id + '" class="form-control chk_number" data-type="व्यवसाय अंकों में भरे"></td>'
            + '</tr>';

        $("#sec_2_3_tbody").append(tr);
        $("#sec_2_3_id").val(id);
    }

    function sec_2_4_add_rows() {
        var id = $("#sec_2_4_id").val();
        if (!id) { id = 0; }
        id++;
        $("#sec_2_4_rows").remove();
        var txt = '';
        txt += '<tr>';
        txt += '<td rowspan="3"><input type="text" name="sec_2_4_year_' + id + '" id="sec_2_4_year_' + id + '" class="form-control"></td>';
        txt += '<td>गेहूं खरीद</td>';
        txt += '<td><input type="text" name="sec_2_4_wheat_target_' + id + '" class="form-control chk_number"></td>';
        txt += '<td><input type="text" name="sec_2_4_wheat_business_' + id + '" class="form-control chk_number"></td>';
        txt += '<td rowspan="3" id="sec_2_4_rows" class="text-center"><button type="button" class="btn btn-info" onclick="sec_2_4_add_rows()">नई पंक्ति जोड़ें [+]</button><input type="hidden" name="sec_2_4_id" id="sec_2_4_id" value="' + id + '"></td>';
        txt += '</tr>';
        txt += '<tr>';
        txt += '<td>धान खरीद</td>';
        txt += '<td><input type="text" name="sec_2_4_paddy_target_' + id + '" class="form-control chk_number"></td>';
        txt += '<td><input type="text" name="sec_2_4_paddy_business_' + id + '" class="form-control chk_number"></td>';
        txt += '</tr>';
        txt += '<tr>';
        txt += '<td><strong>योग</strong></td>';
        txt += '<td><input type="text" name="sec_2_4_total_target_' + id + '" class="form-control chk_number"></td>';
        txt += '<td><input type="text" name="sec_2_4_total_business_' + id + '" class="form-control chk_number"></td>';
        txt += '</tr>';
        $("#sec_2_4_price_support").append(txt);
        $("#sec_2_4_id").val(id);
    }
</script>