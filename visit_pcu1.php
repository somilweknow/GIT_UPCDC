<?php
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// var_dump($_POST);

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
    'address' => '',
    'pincode' => '',
    'pan_no' => '',
    'tan_no' => '',
    'mobile_no' => '',
    'website' => '',
    'membership_fee' => '',
    'nominal_member' => '',
    'lifetime_member' => ''
];
// echo '-------------------------------------', $_GET['exdid'];
if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.*, apex.* FROM `apex_si_1_1` LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '" ';
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
        $row_invoice['address'] = $row_invoice['address'];
        $row_invoice['pincode'] = $row_invoice['pincode'];
        $row_invoice['pan_no'] = $row_invoice['pan_no'];
        $row_invoice['tan_no'] = $row_invoice['tan_no'];
        $row_invoice['mobile_no'] = $row_invoice['mobile_no'];
        $row_invoice['website'] = $row_invoice['website'];
        $row_invoice['lifetime_member'] = $row_invoice['lifetime_member'];
        $row_invoice['nominal_member'] = $row_invoice['nominal_member'];
        $row_invoice['membership_fee'] = $row_invoice['membership_fee'];
    }
    // print_r($row_invoice);
    // print_r($row_invoice['sno']);

    $sql = 'SELECT * FROM apex_si_2_2 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $result_sec_2_b = execute_query($sql);
    $row_2_b = array();
    $d = 1;

    if (mysqli_num_rows($result_sec_2_b) > 0) {
        $row_2_b['count'] = mysqli_num_rows($result_sec_2_b);
        while ($r = mysqli_fetch_assoc($result_sec_2_b)) {
            $row_2_b['sec_2_b_type_' . $d] = $r['office_type'];
            $row_2_b['sec_2_b_name_' . $d] = $r['name'];
            $row_2_b['sec_2_b_division_' . $d] = $r['division'];
            $row_2_b['sec_2_b_district_' . $d] = $r['district'];
            $row_2_b['sec_2_b_tehsil_' . $d] = $r['tehsil'];
            $row_2_b['sec_2_b_address_' . $d] = $r['address'];
            $row_2_b['sec_2_b_mobile_' . $d] = $r['mobile'];
            $row_2_b['sec_2_b_email_' . $d] = $r['email'];
            $row_2_b['sec_2_b_pincode_' . $d] = $r['pincode'];
            $row_2_b['sec_2_b_latitude_' . $d] = $r['latitude'];
            $row_2_b['sec_2_b_longitude_' . $d] = $r['longitude'];
            $d++;
        }
    } else {

        $row_2_b['count'] = 3;
        // Row 1: Branches
        $row_2_b['sec_2_b_type_1'] = 'शाखा';
        $row_2_b['sec_2_b_name_1'] = '';
        $row_2_b['sec_2_b_division_1'] = '';
        $row_2_b['sec_2_b_district_1'] = '';
        $row_2_b['sec_2_b_tehsil_1'] = '';
        $row_2_b['sec_2_b_address_1'] = '';
        $row_2_b['sec_2_b_mobile_1'] = '';
        $row_2_b['sec_2_b_email_1'] = '';
        $row_2_b['sec_2_b_pincode_1'] = '';
        $row_2_b['sec_2_b_latitude_1'] = '';
        $row_2_b['sec_2_b_longitude_1'] = '';

        // Row 2: Training Center
        $row_2_b['sec_2_b_type_2'] = 'ट्रैनिंग सेंटर';
        $row_2_b['sec_2_b_name_2'] = '';
        $row_2_b['sec_2_b_division_2'] = '';
        $row_2_b['sec_2_b_district_2'] = '';
        $row_2_b['sec_2_b_tehsil_2'] = '';
        $row_2_b['sec_2_b_address_2'] = '';
        $row_2_b['sec_2_b_mobile_2'] = '';
        $row_2_b['sec_2_b_email_2'] = '';
        $row_2_b['sec_2_b_pincode_2'] = '';
        $row_2_b['sec_2_b_latitude_2'] = '';
        $row_2_b['sec_2_b_longitude_2'] = '';

        // Row 3: Other Offices
        $row_2_b['sec_2_b_type_3'] = 'अन्य कार्यालय';
        $row_2_b['sec_2_b_name_3'] = '';
        $row_2_b['sec_2_b_division_3'] = '';
        $row_2_b['sec_2_b_district_3'] = '';
        $row_2_b['sec_2_b_tehsil_3'] = '';
        $row_2_b['sec_2_b_address_3'] = '';
        $row_2_b['sec_2_b_mobile_3'] = '';
        $row_2_b['sec_2_b_email_3'] = '';
        $row_2_b['sec_2_b_pincode_3'] = '';
        $row_2_b['sec_2_b_latitude_3'] = '';
        $row_2_b['sec_2_b_longitude_3'] = '';
    }

    // Initialize Member List Data (Dynamic 1 row default)
    $row_member_list = [];
    $row_member_list['count'] = 1;
    $row_member_list['member_mandal_1'] = '';
    $row_member_list['member_district_1'] = '';
    $row_member_list['member_tehsil_1'] = '';
    $row_member_list['member_block_1'] = '';
    $row_member_list['member_type_1'] = '';
    $row_member_list['member_name_1'] = '';

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

    $sql = 'select * from survey_trans_new_sec_2_stock where survey_id="' . $row_invoice['sno'] . '"';
    // echo $sql;
    $result_sec_2 = execute_query($sql);
    $row_sec_2 = array();
    $j = 1;
    if (mysqli_num_rows($result_sec_2) > 0) {
        while ($row_data_section_2 = mysqli_fetch_assoc($result_sec_2)) {
            // Check if 'stock_item_des_id' is empty or not
            if (empty($row_data_section_2['stock_item_des_id'])) {
                // Use stock_item_type_id as the key
                $type_id = $row_data_section_2['stock_item_type_id'];
                $row_sec_2[$type_id] = array(
                    'closing_stock_1' => $row_data_section_2['closing_stock_1'],
                    'closing_stock_2' => $row_data_section_2['closing_stock_2'],
                    'book_value_1' => $row_data_section_2['book_value_1'],
                    'book_value_2' => $row_data_section_2['book_value_2']
                );
            } else {

                $type_id = $row_data_section_2['stock_item_type_id'];
                $des_id = $row_data_section_2['stock_item_des_id'];
                if (!isset($row_sec_2[$type_id])) {
                    $row_sec_2[$type_id] = array();
                }
                $row_sec_2[$type_id][$des_id] = array(
                    'closing_stock_1' => $row_data_section_2['closing_stock_1'],
                    'closing_stock_2' => $row_data_section_2['closing_stock_2'],
                    'book_value_1' => $row_data_section_2['book_value_1'],
                    'book_value_2' => $row_data_section_2['book_value_2']
                );
            }
        }
    } else {

        $row_sec_2['closing_stock_1_1'] = "";
        $row_sec_2['book_value_1_1'] = "";
        $row_sec_2['closing_stock_2_1'] = "";
        $row_sec_2['book_value_2_1'] = "";
    }

    $sql = 'SELECT * FROM msy_data WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $res_msy = execute_query($sql);

    if (mysqli_num_rows($res_msy) != 0) {
        $row_msy = mysqli_fetch_assoc($res_msy);

        $row_msy['msy_1_target_1'] = isset($row_msy['msy_1_target_1']) ? $row_msy['msy_1_target_1'] : '';
        $row_msy['msy_1_supply_1'] = isset($row_msy['msy_1_supply_1']) ? $row_msy['msy_1_supply_1'] : '';
        $row_msy['msy_1_member_no_1'] = isset($row_msy['msy_1_member_no_1']) ? $row_msy['msy_1_member_no_1'] : '';
        $row_msy['msy_1_payment_to_farmer_1'] = isset($row_msy['msy_1_payment_to_farmer_1']) ? $row_msy['msy_1_payment_to_farmer_1'] : '';

        $row_msy['msy_1_target_2'] = isset($row_msy['msy_1_target_2']) ? $row_msy['msy_1_target_2'] : '';
        $row_msy['msy_1_supply_2'] = isset($row_msy['msy_1_supply_2']) ? $row_msy['msy_1_supply_2'] : '';
        $row_msy['msy_1_member_no_2'] = isset($row_msy['msy_1_member_no_2']) ? $row_msy['msy_1_member_no_2'] : '';
        $row_msy['msy_1_payment_to_farmer_2'] = isset($row_msy['msy_1_payment_to_farmer_2']) ? $row_msy['msy_1_payment_to_farmer_2'] : '';

        for ($i = 1; $i <= 4; $i++) {
            $row_msy["msy_2_target_{$i}"] = isset($row_msy["msy_2_target_{$i}"]) ? $row_msy["msy_2_target_{$i}"] : '';
            $row_msy["msy_2_supply_{$i}"] = isset($row_msy["msy_2_supply_{$i}"]) ? $row_msy["msy_2_supply_{$i}"] : '';
            $row_msy["msy_2_member_no_{$i}"] = isset($row_msy["msy_2_member_no_{$i}"]) ? $row_msy["msy_2_member_no_{$i}"] : '';
            $row_msy["msy_2_payment_to_farmer_{$i}"] = isset($row_msy["msy_2_payment_to_farmer_{$i}"]) ? $row_msy["msy_2_payment_to_farmer_{$i}"] : '';
        }
    } else {
        $row_msy = [
            'msy_1_target_1' => '',
            'msy_1_supply_1' => '',
            'msy_1_member_no_1' => '',
            'msy_1_payment_to_farmer_1' => '',
            'msy_1_target_2' => '',
            'msy_1_supply_2' => '',
            'msy_1_member_no_2' => '',
            'msy_1_payment_to_farmer_2' => '',
        ];
        for ($i = 1; $i <= 4; $i++) {
            $row_msy["msy_2_target_{$i}"] = '';
            $row_msy["msy_2_supply_{$i}"] = '';
            $row_msy["msy_2_member_no_{$i}"] = '';
            $row_msy["msy_2_payment_to_farmer_{$i}"] = '';
        }
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
                $row_2_1_2['sec_2_1_2_profit_loss_' . $i] = $row_temp['profit_loss'] ?? '';
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

    $sql = 'SELECT * FROM training_centers WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $res_centers = execute_query($sql);

    if ($res_centers && mysqli_num_rows($res_centers) > 0) {

        $tr = mysqli_fetch_assoc($res_centers);

    } else {
        $tr = [
            'cpmt_name' => '',
            'address' => '',
            'principal_name' => '',
            'post' => '',
            'principal_house' => '',
            'principal_house_no' => '',
            'principal_office' => '',
            'principal_office_no' => '',
            'class_no' => '',
            'class_capacity' => '',
            'hostel_no' => '',
            'hostel_capacity' => '',
            'library_no' => '',
            'library_capacity' => '',
            'computer_lab_no' => '',
            'computer_lab_capacity' => '',
            'teacher_no' => '',
            'employee_remarks' => '',
            'training_sessions_no' => '',
            'training_subject_name' => '',
            'training_sessions_duration' => '',
            'departmental_trainees_no' => '',
            'non_departmental_trainees_no' => '',
            'trainees_no' => '',
            'departmental_trainees_fee' => '',
            'non_departmental_trainees_fee' => '',
            'trainees_fee' => '',
            'departmental_hostel_fee' => '',
            'non_departmental_hostel_fee' => '',
            'hostel_fee' => '',
            'construction_year' => '',
            'operational_year' => '',
            'center_ref_id' => '',
            'staff_count' => '',
            'training_course_benefits' => '',
            'building_hostel_status' => ''
        ];
    }

    // $r = $training_rows[0];

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

    $sql_pcu = "SELECT * FROM apex_si_1_4 WHERE survey_id = '" . $row_invoice['sno'] . "'";
    $result_pcu = execute_query($sql_pcu);

    $pcu_rows = [];
    if ($result_pcu && mysqli_num_rows($result_pcu) > 0) {
        while ($row_p = mysqli_fetch_assoc($result_pcu)) {
            $pcu_rows[] = [
                'pcu_post_id'     => $row_p['pcu_post_id'],
                'pcu_sanctioned'  => $row_p['pcu_sanctioned'],
                'pcu_working'     => $row_p['pcu_working'],
                'pcu_vacant'      => $row_p['pcu_vacant'],
                'auth_sanctioned' => $row_p['auth_sanctioned'],
                'auth_working'    => $row_p['auth_working'],
                'auth_vacant'     => $row_p['auth_vacant'],
                'pcu_other_detail'=> $row_p['pcu_other_detail']
            ];
        }
    } else {
        // Default empty row for first load
        $pcu_rows[] = [
            'pcu_post_id'     => '',
            'pcu_sanctioned'  => '',
            'pcu_working'     => '',
            'pcu_vacant'      => '',
            'auth_sanctioned' => '',
            'auth_working'    => '',
            'auth_vacant'     => '',
            'pcu_other_detail'=> ''
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

    $sql = 'SELECT * FROM other_land_details WHERE survey_id="' . $row_invoice['sno'] . '"';
$result = execute_query($sql);

$other_land_rows = [];
$count = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $count++;
    $other_land_rows[$count] = $row;
}

// If no rows found, show 1 empty row
if ($count == 0) {
    $count = 1;
    $other_land_rows[1] = [
        "district" => "",
        "tehsil" => "",
        "area_type" => "",
        "land_area" => "",
        "ownership" => "",
        "other_owner" => "",
        "land_status" => "",
        "construction" => "",
        "other_construction" => "",
        "address" => "",
        "latitude" => "",
        "longitude" => "",
        "location_mode" => ""
    ];
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
                        <form action="scripts/ajax_pcu.php" method="post" enctype="multipart/form-data" id="user_form"
                            name="user_form">
                            <div id="steps-container">
                                <!-------------------1st start--------------------------------------------------------------------->
                                <div class="step">
                                    <?php echo $msg; ?>

                                    <h4><img src="images/logo/1.png" class="img-fluid stat-icon"
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
                                                            value="यू०पी० कोआपरेटिव यूनियन लि०" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="hidden" id="apex_code" name="apex_code"
                                                    value="<?php echo $row_invoice['apex_id']; ?>">
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
                                                        <button type="button" class="btn btn-info" style=""
                                                            onClick="getLocation();">मुख्यालय की
                                                            जियो-लोकेशन</button>
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
                                        <hr />
                                        <div class="row">
                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                <label>समिति पंजीकरण संख्या</label>
                                                <br />
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo $row_invoice['society_registration_no']; ?>">
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <label>समिति पंजीकरण दिनांक</label>
                                                <label><small>नहीं पता होने कि स्थिति में आज का ही दिनांक
                                                        दर्शायें</small></label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo $row_invoice['society_registration_date']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>क्या समिति सक्रिय है ?</label>
                                                <select class="form-control" id="committee_status"
                                                    name="committee_status" tabindex="<?php echo $tab++; ?>"
                                                    onChange="hide_show(this.value, '#committee_date_section', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes">हाँ</option>
                                                    <option value="no">नहीं</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group" id="committee_date_section"
                                                style="display: none;">
                                                <label>समिति की तिथि</label><br>
                                                <label><small>नहीं पता होने की स्थिति में आज का ही दिनांक
                                                        दर्शायें</small></label>
                                                <input type="date" name="committee_date" id="committee_date"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo !empty($row_invoice['committee_date']) ? $row_invoice['committee_date'] : date('Y-m-d'); ?>">
                                            </div>
                                            <?php
                                            if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) {
                                                ?>
                                            <div class="col-sm-2 form-group">
                                                <label>मुख्यालय की फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="society_photo" id="society_photo"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <img src="<?php echo $row_invoice['photo_id']; ?>"
                                                    class="img-fluid img-thumbnail" style="height:50px;"
                                                    id="society_photo_uploaded">
                                                <label><a href="<?php echo $row_invoice['photo_id']; ?>"
                                                        target="_blank">संलग्न फोटो देखें</a></label>

                                            </div>
                                            <?php
                                            } else {
                                                ?>
                                            <div class="col-sm-3 form-group">
                                                <label>मुख्यालय की फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="society_photo" id="society_photo"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">

                                            </div>

                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label>पता</label>
                                                <input name="address" id="address" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control" value="<?php echo $row_invoice['address']; ?>">
                                            </div>
                                            <div class="col-sm-3">
                                                <label>पिनकोड</label>
                                                <input name="pincode" id="pincode" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control" value="<?php echo $row_invoice['pincode']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>पैन न०</label>
                                                <input type="text" name="pan_no" id="pan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['pan_no']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>टैन न०</label>
                                                <input type="text" name="tan_no" id="tan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['tan_no']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>जी० एस० टी० न०</label>
                                                <input type="text" name="tan_no" id="tan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['tan_no']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>ई-मेल आई.डी.</label>
                                                <input type="text" name="sec_1_email" id="sec_1_email"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['email_id']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>दूरभाष न०</label>
                                                <input type="text" name="mobile_no" id="mobile_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['mobile_no']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>वेबसाईट</label>
                                                <input type="text" name="website" id="website"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['website']; ?>">
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
                                        </div>
                                        <br>

                                        <h5> <img src="#" class="img-fluid stat-icon" style="height:50px; width:50px;"> 1.1 सदस्यों का प्रकार </h5><br>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label><b>(I) सदस्य समितियों की संख्या </b></label>
                                                <input type="text" name="lifetime_member" id="lifetime_member"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['lifetime_member']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label><b>(II) सामान्य सदस्य की संख्या </b></label>
                                                <input type="text" name="nominal_member" id="nominal_member"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['nominal_member']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label><b>(III) कृषक सदस्य की संख्या </b></label>
                                                <input type="text" name="membership_fee" id="membership_fee"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['membership_fee']; ?>">
                                            </div>

                                        </div>
                                        <small><b> (IV) कुल सदस्य :</b></small>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <input type="text" name="total_members" id="total_members"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control" disabled
                                                    value="<?php echo $row_invoice['total_members']; ?>">
                                            </div>
                                        </div>
                                        <br>
                                        <label><b>(V) सदस्य समितियों की सूची</b></label>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped"
                                                        id="member_list_table">
                                                        <thead>
                                                            <tr>
                                                                <th>क्र०</th>
                                                                <th>मंडल</th>
                                                                <th>जिला</th>
                                                                <th>तहसील</th>
                                                                <th>ब्लॉक</th>
                                                                <th>समिति का प्रकार</th>
                                                                <th>समिति का नाम</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php for ($m = 1; $m <= $row_member_list['count']; $m++) { ?>
                                                            <tr id="member_row_<?php echo $m; ?>">
                                                                <td><?php echo $m; ?></td>
                                                                <td><input type="text"
                                                                        name="member_mandal_<?php echo $m; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_member_list['member_mandal_' . $m]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="member_district_<?php echo $m; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_member_list['member_district_' . $m]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="member_tehsil_<?php echo $m; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_member_list['member_tehsil_' . $m]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="member_block_<?php echo $m; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_member_list['member_block_' . $m]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="member_type_<?php echo $m; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_member_list['member_type_' . $m]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="member_name_<?php echo $m; ?>"
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
                                                    <input type="hidden" name="member_list_count" id="member_list_count" value="<?php echo $row_member_list['count']; ?>">
                                                </div>
                                            </div>
                                        </div>

                                        <div style="background-color: #ffd966; padding: 8px; border-radius: 10px; margin-bottom: 15px;">
                                            <h5 style="margin: 0; color: #000;"> <img src="images/logo/2.png"
                                                    class="img-fluid stat-icon" style="height:40px; width:40px; margin-right: 10px;"> 1.2
                                                शाखाओं / ट्रेनिंग सेंटर / अन्य
                                                कार्यालयों का विवरण </h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div id="sec_2_b" style="overflow-x:auto; width:100%">
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <thead style="background-color: #fd7e14; color: white;">
                                                            <tr>
                                                                <th>स०</th>
                                                                <th style="width:200px;">प्रकार</th>
                                                                <th>नाम</th>
                                                                <th>मण्डल</th>
                                                                <th>जनपद</th>
                                                                <th>तहसील</th>
                                                                <th>पता</th>
                                                                <th>दूरभाष न०</th>
                                                                <th>ई-मेल आई.डी.</th>
                                                                <th>पिनकोड</th>
                                                                <th>अक्षांश</th>
                                                                <th>देशान्तर</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php for ($i = 1; $i <= $row_2_b['count']; $i++) { ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $i; ?>
                                                                </td>
                                                                <td>
                                                                    <select name="sec_2_b_type_<?php echo $i; ?>"
                                                                        id="sec_2_b_type_<?php echo $i; ?>"
                                                                        class="form-control">
                                                                        <option value="">--select--</option>
                                                                        <option value="शाखा" <?php echo $row_2_b['sec_2_b_type_' . $i] == 'शाखा' ? 'selected' : ''; ?>>शाखा</option>
                                                                        <option value="ट्रैनिंग सेंटर" <?php echo $row_2_b['sec_2_b_type_' . $i] == 'ट्रैनिंग सेंटर' ? 'selected' : ''; ?>>ट्रैनिंग सेंटर
                                                                        </option>
                                                                        <option value="अन्य कार्यालय" <?php echo $row_2_b['sec_2_b_type_' . $i] == 'अन्य कार्यालय' ? 'selected' : ''; ?>>अन्य
                                                                            कार्यालय</option>
                                                                    </select>
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_name_<?php echo $i; ?>"
                                                                        id="sec_2_b_name_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_name_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_division_<?php echo $i; ?>"
                                                                        id="sec_2_b_division_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_division_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_district_<?php echo $i; ?>"
                                                                        id="sec_2_b_district_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_district_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_tehsil_<?php echo $i; ?>"
                                                                        id="sec_2_b_tehsil_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_tehsil_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_address_<?php echo $i; ?>"
                                                                        id="sec_2_b_address_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_address_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_mobile_<?php echo $i; ?>"
                                                                        id="sec_2_b_mobile_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_mobile_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_email_<?php echo $i; ?>"
                                                                        id="sec_2_b_email_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_email_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_pincode_<?php echo $i; ?>"
                                                                        id="sec_2_b_pincode_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_pincode_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_latitude_<?php echo $i; ?>"
                                                                        id="sec_2_b_latitude_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_latitude_' . $i]; ?>">
                                                                </td>
                                                                <td><input type="text"
                                                                        name="sec_2_b_longitude_<?php echo $i; ?>"
                                                                        id="sec_2_b_longitude_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        value="<?php echo $row_2_b['sec_2_b_longitude_' . $i]; ?>">
                                                                </td>

                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="text-left mt-2" id="sec_2_b_rows">
                                                    <button type="button" class="btn btn-info"
                                                        onclick="sec_2_b_add_rows();">नई पंक्ति जोड़े
                                                        [+]</button>
                                                </div>
                                                <input type="hidden" name="sec_2_b_id" id="sec_2_b_id"
                                                    value="<?php echo !empty($row_2_b['count']) ? $row_2_b['count'] : 0; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!----------------2.1 start-------------------------------------------------------->
                                <div class="step">
                                    <h4> <img src="images/logo/4.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 2. E-Stock Register(ई-स्टॉक रजिस्टर)
                                    </h4>
                                    <div class="col-sm-12">
                                        <div class="alert alert-primary">B-PACS E-Stock Register (बी-पैक्स ई-स्टॉक
                                            रजिस्टर)</div>
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">S.No.</th>
                                                    <th style="text-align: center;">Stock Item(स्टॉक आइटम)</th>
                                                    <th style="text-align: center;">Closing Stock as on 31st March,
                                                        2023(31 मार्च, 2023 को समापन स्टॉक)</th>
                                                    <th style="text-align: center;">Book Value(बुक वैल्यू)</th>
                                                    <th style="text-align: center;">Closing Stock as on 31st March,
                                                        2024(31 मार्च को अंतिम स्टॉक,2024)</th>
                                                    <th style="text-align: center;">Book Value(बुक वैल्यू)</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php
                                                $sql = 'SELECT `sno`, `type_name` FROM `stock_item_type`';
                                                $res_stock_item_type = execute_query($sql);
                                                $t = 1;
                                                $p = 1;
                                                while ($row_stock_item_type = mysqli_fetch_assoc($res_stock_item_type)) {

                                                    $sql = 'SELECT `sno`, `stock_item_type_id`, `item_name` FROM `stock_item_des` WHERE stock_item_type_id="' . $row_stock_item_type['sno'] . '"';
                                                    // echo $sql.'<br>';;
                                                    $res_stock_item_des = execute_query($sql);
                                                    $d = 1;

                                                    if (mysqli_num_rows($res_stock_item_des) > 0) {

                                                        echo '<tr>
                                                                    <th>' . $t++ . '</th>
                                                                    <th> ' . $row_stock_item_type['type_name'] . '</th>
                                                                    </tr>';

                                                        while ($row_stock_item_des = mysqli_fetch_assoc($res_stock_item_des)) {

                                                            echo '<tr>
                                                        
                                                                <td></td>
                                                                <td>' . $d++ . '.)' . $row_stock_item_des['item_name'] . '</td>
                                                                <td><input type="text" name="closing_stock_1_' . $row_stock_item_type['sno'] . '_' . $row_stock_item_des['sno'] . '" 
                                                                
                                                                value="' . $row_sec_2[$row_stock_item_type['sno']][$row_stock_item_des['sno']]['closing_stock_1'] . '" 
                                                                class="form-control chk_number" data-type="5.1 31 मार्च, 2023 को समापन स्टॉक को अंक मे भरे-' . $d . '"></td>
                                                                
                                                                <td><input type="text" name="book_value_1_' . $row_stock_item_type['sno'] . '_' . $row_stock_item_des['sno'] . '" 
                                                                    
                                                                value="' . $row_sec_2[$row_stock_item_type['sno']][$row_stock_item_des['sno']]['book_value_1'] . '"
                                                                
                                                                class="form-control chk_decimal" data-type="5.1 बुक वैल्यू को धनराशि रु० मे भरे-' . $d . '"></td>
                                                                <td><input type="text" name="closing_stock_2_' . $row_stock_item_type['sno'] . '_' . $row_stock_item_des['sno'] . '" 
                                                                
                                                                value="' . $row_sec_2[$row_stock_item_type['sno']][$row_stock_item_des['sno']]['closing_stock_2'] . '"
                                                                
                                                                class="form-control chk_number" data-type="5.1 31 मार्च को अंतिम स्टॉक,2024 को अंक मे भरे-' . $d . '"></td>
                                                                <td><input type="text" name="book_value_2_' . $row_stock_item_type['sno'] . '_' . $row_stock_item_des['sno'] . '" 
                                                                
                                                                value="' . $row_sec_2[$row_stock_item_type['sno']][$row_stock_item_des['sno']]['book_value_2'] . '"
                                                                
                                                                class="form-control chk_decimal" data-type="5.1 बुक वैल्यू को धनराशि रु० मे भरे-' . $d . '"></td>
                                                                </tr>';
                                                            $p++;
                                                        }
                                                    } else {

                                                        echo '<tr>
                                                                <th>' . $t++ . '</th>
                                                                <th> ' . $row_stock_item_type['type_name'] . '</th>
                                                    
                                                                    <td><input type="text" name="closing_stock_1_' . $row_stock_item_type['sno'] . '" 
                                                                    
                                                                    value="' . $row_sec_2[$row_stock_item_type['sno']]['closing_stock_1'] . '" 
                                                                    
                                                                    class="form-control chk_number" data-type="5.1 (31 मार्च, 2023 को समापन स्टॉक को अंक मे भरे)-' . $t . '"></td>
                                                                    
                                                                    <td><input type="text" name="book_value_1_' . $row_stock_item_type['sno'] . '" 
                                                                    
                                                                    value="' . $row_sec_2[$row_stock_item_type['sno']]['book_value_1'] . '"
                                                                    
                                                                    class="form-control chk_decimal" data-type="5.1 बुक वैल्यू को धनराशि रु० मे भरे-' . $t . '"></td>
                                                                    
                                                                    <td><input type="text" name="closing_stock_2_' . $row_stock_item_type['sno'] . '" 
                                                                    
                                                                    value="' . $row_sec_2[$row_stock_item_type['sno']]['closing_stock_2'] . '"
                                                                    
                                                                    
                                                                    class="form-control chk_number" data-type="5.1 31 मार्च को अंतिम स्टॉक,2024 को अंक मे भरे-' . $t . '" ></td>
                                                                    
                                                                    <td><input type="text" name="book_value_2_' . $row_stock_item_type['sno'] . '" 
                                                                    
                                                                    value="' . $row_sec_2[$row_stock_item_type['sno']]['book_value_2'] . '"
                                                                    
                                                                    class="form-control chk_decimal" data-type="5.1 बुक वैल्यू को धनराशि रु० मे भरे-' . $t . '"></td>
                                                                        
                                                            </tr>';
                                                        $p++;
                                                    }
                                                }
                                                ?>

                                            </tbody>
                                        </table>
                                        
                                        <br>
                                        <div class="alert alert-primary">SCRAPPED (निष्प्रयोज्य)</div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>S.No.</th>
                                                        <th>ITEM NAME (वस्तु का नाम)</th>
                                                        <th>CLOSING STOCK AS ON 31st MARCH 2024 (31 मार्च 2024 को समापन स्टॉक)</th>
                                                        <th>BOOK VALUE (बुक वैल्यू)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php for ($k = 1; $k <= 10; $k++) { ?>
                                                    <tr>
                                                        <td><?php echo $k; ?></td>
                                                        <td><input type="text" name="scrapped_item_name_<?php echo $k; ?>" class="form-control"></td>
                                                        <td><input type="text" name="scrapped_closing_stock_<?php echo $k; ?>" class="form-control"></td>
                                                        <td><input type="text" name="scrapped_book_value_<?php echo $k; ?>" class="form-control"></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <br>
                                        <div class="alert alert-primary">FY 24-25 Purchase</div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>S.No.</th>
                                                        <th>ITEM NAME</th>
                                                        <th>ITEM DESCRIPTION WITH BRAND NAME</th>
                                                        <th>SCHEME NAME</th>
                                                        <th>DATE</th>
                                                        <th>PURCHASE VALUE</th>
                                                        <th>QUANTITY</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php for ($l = 1; $l <= 10; $l++) { ?>
                                                    <tr>
                                                        <td><?php echo $l; ?></td>
                                                        <td><input type="text" name="purchase_item_name_<?php echo $l; ?>" class="form-control"></td>
                                                        <td><input type="text" name="purchase_desc_<?php echo $l; ?>" class="form-control"></td>
                                                        <td><input type="text" name="purchase_scheme_<?php echo $l; ?>" class="form-control"></td>
                                                        <td><input type="date" name="purchase_date_<?php echo $l; ?>" class="form-control"></td>
                                                        <td><input type="text" name="purchase_value_<?php echo $l; ?>" class="form-control"></td>
                                                        <td><input type="text" name="purchase_qty_<?php echo $l; ?>" class="form-control"></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- </div> -->
                                <!------ 3td start ------->
                                <div class="step">
                                    <h4><img src="images/logo/3.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 3(I) वित्तीय सूचना</h4>
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
                                </div>
                                <!-------4th start------->
                                <div class="step">
                                    <h4><img src="images/logo/4.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 4. अन्य कार्य व व्यवसाय</h4>
                                    <div id="other_business">
                                        <?php
                                        $sec_4_count = !empty($row_2_1_2['count']) ? $row_2_1_2['count'] : 1;
                                        ?>
                                        <input type="hidden" name="other_business_id" id="other_business_id" value="<?php echo $sec_4_count; ?>">
                                        
                                        <?php for ($i = 1; $i <= $sec_4_count; $i++) { ?>
                                        <div class="row" id="business_row_<?php echo $i; ?>">
                                            <div class="col-sm-5 form-group">
                                                <label>व्यवसाय का विवरण</label>
                                                <input type="text" name="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                    id="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                    class="form-control"
                                                    value="<?php echo isset($row_2_1_2['sec_2_1_2_business_description_' . $i]) ? $row_2_1_2['sec_2_1_2_business_description_' . $i] : ''; ?>">
                                            </div>

                                            <div class="col-sm-5 form-group">
                                                <label>वार्षिक टर्नओवर</label>
                                                <input type="text" name="sec_2_1_2_value_<?php echo $i; ?>"
                                                    id="sec_2_1_2_value_<?php echo $i; ?>"
                                                    class="form-control chk_decimal"
                                                    data-type="वार्षिक टर्नोवर को धनराशि रु० लाख मे भरे"
                                                    value="<?php echo isset($row_2_1_2['sec_2_1_2_value_' . $i]) ? $row_2_1_2['sec_2_1_2_value_' . $i] : ''; ?>">
                                            </div>

                                            <?php if ($i == 1) { ?>
                                            <div class="col-sm-2 form-group my-auto">
                                                <button type="button" class="btn btn-info mt-4" onclick="add_more_business();">नई पंक्ति जोड़े [+]</button>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-12">
                                            <h5>
                                                <img src="#"
                                                    class="img-fluid stat-icon" style="height:60px; width:60px;">
                                                5. सहकारी प्रबंध प्रशिक्षण केंद्र
                                            </h5>
                                            <div id="dynamic-rows">
                                                <?php
                                                $count = !empty($row_3_3['count']) ? $row_3_3['count'] : 1;
                                                for ($i = 1; $i <= $count; $i++) {
                                                    ?>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>नाम :- सहकारी प्रबंध प्रशिक्षण केंद्र</label>
                                                                <input name="sec_3_cpmt_<?php echo $i; ?>"
                                                                    id="sec_3_cpmt_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_cpmt_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>पता</label>
                                                                <input name="sec_3_address_<?php echo $i; ?>"
                                                                    id="sec_3_address_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_address_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>पदेन प्रधानाचार्य नाम</label>
                                                                <input name="sec_3_principal_name_<?php echo $i; ?>"
                                                                    id="sec_3_principal_name_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_principal_name_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>मूलपद</label>
                                                                <input name="sec_3_post_<?php echo $i; ?>"
                                                                    id="sec_3_post_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_post_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>प्रधानाचार्य आवास</label>
                                                                <select name="sec_3_principal_house_<?php echo $i; ?>"
                                                                    id="sec_3_principal_house"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    onChange="hide_show(this.value, '#sec_3_principal_house_no', 'yes');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes">हाँ </option>
                                                                    <option value="no">नहीं </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4" id="sec_3_principal_house_no"
                                                                style="display:none">
                                                                <label>संख्या</label>
                                                                <input name="sec_3_principal_house_no_<?php echo $i; ?>"
                                                                    id="sec_3_principal_house_no"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_principal_house_no_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>प्रधानाचार्य कार्यालय</label>
                                                                <select name="sec_3_principal_office_<?php echo $i; ?>"
                                                                    id="sec_3_principal_office_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    onChange="hide_show(this.value, '#sec_3_principal_office_no', 'yes');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes">हाँ </option>
                                                                    <option value="no">नहीं </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4" id="sec_3_principal_office_no"
                                                                style="display:none">
                                                                <label>संख्या</label>
                                                                <input
                                                                    name="sec_3_principal_office_no_<?php echo $i; ?>"
                                                                    id="sec_3_principal_office_no"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_principal_office_no_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>क्लासरूम संख्या</label>
                                                                <input name="sec_3_class_no_<?php echo $i; ?>"
                                                                    id="sec_3_class_no" tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_class_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_class_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_class_capacity"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_class_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>हॉस्टल संख्या</label>
                                                                <input name="sec_3_hostel_no_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_no"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_hostel_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_hostel_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_capacity"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_hostel_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>पुस्तकालय संख्या</label>
                                                                <input name="sec_3_library_no_<?php echo $i; ?>"
                                                                    id="sec_3_library_no"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_library_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_library_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_library_capacity"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_library_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>कंप्युटर लैब संख्या</label>
                                                                <input name="sec_3_computer_lab_no_<?php echo $i; ?>"
                                                                    id="sec_3_computer_lab_no"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_computer_lab_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input
                                                                    name="sec_3_computer_lab_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_computer_lab_capacity"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_computer_lab_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>अध्यापक / अतिथि प्रवक्ता संख्या</label>
                                                                <input name="sec_3_teacher_no_<?php echo $i; ?>"
                                                                    id="sec_3_teacher_no"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_teacher_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>कर्मचारी विवरण</label>
                                                                <textarea
                                                                    name="sec_3_employee_remarks_<?php echo $i; ?>"
                                                                    id="sec_3_employee_remarks"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"><?php echo $row_invoice['sec_3_employee_remarks_' . $i]; ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षण सत्रों की संख्या</label>
                                                                <input
                                                                    name="sec_3_training_sessions_no_<?php echo $i; ?>"
                                                                    id="sec_3_training_sessions_no"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_training_sessions_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षण विषय के नाम</label>
                                                                <input
                                                                    name="sec_3_training_subject_name_<?php echo $i; ?>"
                                                                    id="sec_3_training_subject_name"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_training_subject_name_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>प्रशिक्षण सत्र अवधि</label>
                                                                <input type="date"
                                                                    name="sec_3_training_sessions_duration_<?php echo $i; ?>"
                                                                    id="sec_3_training_sessions_duration_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_training_sessions_duration_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>विभागीय प्रशिक्षार्थियों की संख्या</label>
                                                                <input
                                                                    name="sec_3_departmental_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_departmental_trainees_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>गैर-विभागीय प्रशिक्षार्थियों की संख्या</label>
                                                                <input
                                                                    name="sec_3_non_departmental_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_non_departmental_trainees_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षार्थियों की संख्या</label>
                                                                <input name="sec_3_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control" readonly
                                                                    value="<?php echo $row_invoice['sec_3_trainees_no_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input
                                                                    name="sec_3_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_departmental_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>गैर-विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input
                                                                    name="sec_3_non_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_non_departmental_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input name="sec_3_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control" readonly
                                                                    value="<?php echo $row_invoice['sec_3_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>विभागीय हॉस्टल शुल्क</label>
                                                                <input
                                                                    name="sec_3_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_departmental_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>गैर-विभागीय हॉस्टल शुल्क</label>
                                                                <input
                                                                    name="sec_3_non_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['sec_3_non_departmental_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>हॉस्टल शुल्क</label>
                                                                <input name="sec_3_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control" readonly
                                                                    value="<?php echo $row_invoice['sec_3_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <label>निर्माण वर्ष</label>
                                                                <select name="sec2_santulan_patra" class="form-control">
                                                                    <option value="">--Select--</option>
                                                                    <option value="">2024</option>
                                                                    <option value="">2023</option>
                                                                    <option value="">2022</option>
                                                                    <option value="">2021</option>
                                                                    <option value="">2020</option>
                                                                    <option value="">2019</option>
                                                                    <option value="">2018</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>संचालन वर्ष</label>
                                                                <select name="sec2_santulan_patra" class="form-control">
                                                                    <option value="">--Select--</option>
                                                                    <option value="">2024</option>
                                                                    <option value="">2023</option>
                                                                    <option value="">2022</option>
                                                                    <option value="">2021</option>
                                                                    <option value="">2020</option>
                                                                    <option value="">2019</option>
                                                                    <option value="">2018</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3"><label for="">सहकारी प्रबंध प्रशिक्षण
                                                                    केंद्र</label>
                                                                <select name="" id="" class="form-control">
                                                                    <option value="">--Select--</option>
                                                                    <option value="0">मेरठ </option>
                                                                    <option value="1">वाराणसी</option>
                                                                    <option value="1">महोबा</option>
                                                                    <option value="1">हेवरा (ईटवा)</option>
                                                                    <option value="1">अयोध्या (फैजाबाद)</option>
                                                                    <option value="1">बिलारी (मोरादाबाद)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>कार्मिक की संख्या</label>
                                                                <select name="sec_2_stock_insurance"
                                                                    class="form-control"
                                                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes">उ० प्र० कोआपरेटिव यूनियन
                                                                    </option>
                                                                    <option value="no">सहकारी संघ प्राधिकारी</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>प्रशिक्षण कोर्स लाभ</label>
                                                                <textarea
                                                                    name="sec_3_training_course_benefits_<?php echo $i; ?>"
                                                                    id="sec_3_training_course_benefits_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"><?php echo $row_invoice['sec_3_training_course_benefits_' . $i]; ?></textarea>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>भवन/हॉस्टल स्तिथि</label>
                                                                <textarea
                                                                    name="sec_3_building_hostel_status_<?php echo $i; ?>"
                                                                    id="sec_3_building_hostel_status_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"><?php echo $row_invoice['sec_3_building_hostel_status_' . $i]; ?></textarea>
                                                            </div>
                                                        </div>
                                                        <?php if ($i == $row_3_3['count']) { ?>
                                                        <div class="col-sm-2 form-group my-auto" id="sec_3_add_rows">
                                                            <button type="button" class="btn btn-info"
                                                                onClick="addRow()">नई
                                                                पंक्ति जोड़े [+]</button>
                                                            <input type="hidden" name="sec_3_row_count"
                                                                id="sec_3_row_count"
                                                                value="<?php echo $row_3_3['count']; ?>">
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                </div>
                                <!----------------5th start-------------------------------------------------------->
                                <div class="step">
                                    <h4><img src="images/logo/6.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 6 (I) संस्थागत ढांचा</h4>
                                    <div class="col-sm-12">
                                        <form id="postForm">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <!-- <th colspan="3" style="text-align: center;"></th> -->
                                                        <th colspan="" style="text-align: center;">पद</th>
                                                        <th colspan="" style="text-align: center;">नाम</th>
                                                        <th colspan="" style="text-align: center;">पिता का नाम</th>
                                                        <th colspan="" style="text-align: center;">दूरभाष न०</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="postTableBody">
                                                    <!-- MD Row -->
                                                    <tr>
                                                        <td>प्रबंध निदेशक</td>
                                                        <td><input type="text" id="name_md"></td>
                                                        <td><input type="text" id="mobile_md"></td>
                                                        <td><input type="text" id="education_md"></td>
                                                        <!-- <td colspan="3"></td> -->
                                                    </tr>
                                                    <!-- AMD Row -->
                                                    <tr>
                                                        <td>उप-प्रबंध निदेशक</td>
                                                        <td><input type="text" id="name_amd"></td>
                                                        <td><input type="text" id="mobile_amd"></td>
                                                        <td><input type="text" id="education_amd"></td>
                                                        <!-- <td colspan="3"></td> -->
                                                    </tr>
                                                    <!-- CGM Row -->
                                                    <tr>
                                                        <td>मुख्य महाप्रबंधक</td>
                                                        <td><input type="text" id="name_cgm"></td>
                                                        <td><input type="text" id="mobile_cgm"></td>
                                                        <td><input type="text" id="education_cgm"></td>
                                                        <!-- <td colspan="3"></td> -->
                                                    </tr>
                                                    <!-- GM, DGM, AGM Rows -->
                                                    <tr>
                                                        <!-- <th colspan="3" style="text-align: center;"></th> -->
                                                        <th colspan="" style="text-align: center;">पद</th>
                                                        <th colspan="" style="text-align: center;">संख्या</th>
                                                        <th colspan="" style="text-align: center;">स्वीकृत पद</th>
                                                        <th colspan="" style="text-align: center;">रिक्त पद</th>
                                                    </tr>
                                                    <tr id="gmRow">
                                                        <td>महाप्रबंधक</td>
                                                        <!-- <td colspan="2"></td> -->
                                                        <td><input type="number" class="count_gm"></td>
                                                        <td><input type="text" class="vacant_gm"></td>
                                                        <td><input type="text" class="sanctioned_gm"></td>
                                                    </tr>
                                                    <tr id="dgmRow">
                                                        <td>उप-महाप्रबंधक</td>
                                                        <!-- <td colspan="2"></td> -->
                                                        <td><input type="number" class="count_dgm"></td>
                                                        <td><input type="text" class="vacant_dgm"></td>
                                                        <td><input type="text" class="sanctioned_dgm"></td>
                                                    </tr>
                                                    <tr id="agmRow">
                                                        <td>सहायक महाप्रबंधक</td>
                                                        <!-- <td colspan="2"></td> -->
                                                        <td><input type="number" class="count_agm"></td>
                                                        <td><input type="text" class="vacant_agm"></td>
                                                        <td><input type="text" class="sanctioned_agm"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <!-- <button type="button" onclick="showTree()">Submit and Show Tree</button> -->
                                        </form>

                                        <!-- <h3>Hierarchy Tree Output</h3>
                                                    <div id="output" class="tree-output"></div> -->


                                        <h5><img src="images/logo/7.png" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> (III) प्रबंध कमेटी</h5>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>प्रबंध कमेटी निर्वाचित है?</label>
                                                <select name="sec_2_stock_insurance"
                                                    class="form-control"
                                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--select-- </option>
                                                    <option value="yes">हाँ </option>
                                                    <option value="no">नहीं </option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2 form-group" id="post_name" style="display:none;">
                                                <label>पद का नाम</label>
                                                <input type="text" name="post_name" id="post_name"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>" value="">
                                            </div>
                                            <div class="col-sm-2 form-group" id="election_year" style="display:none;">
                                                <label>निर्वाचन का वर्ष</label>
                                                <select name="sec2_balance_sheet" id="sec2_balance_sheet"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    for ($i = 2022; $i >= 1975; $i--) {
                                                        echo '<option value="' . $i . '" ';
                                                        if ($i == $row_2_1['balance_sheet_year']) {
                                                            echo ' selected="selected" ';
                                                        }
                                                        echo ' >' . $i . '</option>';
                                                    }
                                                    ?>
                                                    <!-- <option value="old" <?php echo $row_2_1['balance_sheet_year'] == 'old' ? ' selected="selected"' : '' ?>>2015 से पूर्व में</option> -->
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>पदनाम</label>
                                                <select name="sec_2_stock_insurance"
                                                    class="form-control"
                                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--select-- </option>
                                                    <option value="yes">अध्यक्ष</option>
                                                    <option value="yes">उपाध्यक्ष</option>
                                                    <option value="no">संचालक</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <label>नाम</label>
                                                <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>" value="">
                                            </div>
                                            <!-- <div class="col-sm-2 form-group">
                                                                <label>कार्यकाल</label>
                                                                <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    tabindex="<?php echo $tab++; ?>" value="">
                                                            </div> -->
                                            <div class="col-sm-2 form-group" id="guard_count">
                                                <label>पिता का नाम</label>
                                                <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>" value="">
                                            </div>
                                            <div class="col-sm-2 form-group" id="guard_count">
                                                <label>मोबाईल नंबर</label>
                                                <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    tabindex="<?php echo $tab++; ?>" value="">
                                            </div>

                                            <div class="col-sm-2 form-group">
                                                <button type="button" class="btn btn-info"
                                                    onclick="()">नई
                                                    पंक्ति जोड़े [+]</button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="sec_6_2_id" id="sec_6_2_id" value="<?php echo $row_6_2['count']; ?>">
                                        <br>


                                    </div>
                                </div>
                                <!--------------------------------------------------------------->

                                <div class="step">
                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon" style="height:50px; width:50px;"> 7. समिति भवन/सम्पत्ति का विवरण</h4>
                                    
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

                                    <!-- (I) Plot Ownership Details -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(I) भूखंड स्वामित्व का विवरण</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>भूखंड स्वामित्व की स्थिति</label>
                                            <select name="sec_7_plot_ownership_status" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="registered">पंजीकृत</option>
                                                <option value="unregistered">अपंजीकृत</option>
                                                <option value="disputed">विवादित</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- (II) Plot Details -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(II) भूखंड का विवरण</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>भूखंड की भुजा की संख्या</label>
                                            <select name="sec_7_plot_sides" class="form-control">
                                                <option value="">--Select--</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="more">4 से अधिक</option>
                                            </select>
                                        </div>
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
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(III) भूखंड की चहारदीवारी का विवरण</h5>
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
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(IV) निर्मित भवन का विवरण</h5>
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
                                        <input type="hidden" name="sec_7_4_count" id="sec_7_4_count" value="<?php echo $sec_7_4_count; ?>">
                                        
                                        <?php for($i=1; $i<=$sec_7_4_count; $i++) { ?>
                                        <div class="row" id="sec_7_4_row_<?php echo $i; ?>">
                                            <div class="col-sm-2 form-group">
                                                <label>लंबाई (मीटर में)</label>
                                                <input type="text" name="sec_7_4_length_<?php echo $i; ?>" class="form-control chk_decimal">
                                            </div>
                                            <div class="col-sm-2 form-group">
                                                <label>चौड़ाई (मीटर में)</label>
                                                <input type="text" name="sec_7_4_width_<?php echo $i; ?>" class="form-control chk_decimal">
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
                                            <?php if($i == 1) { ?>
                                            <div class="col-sm-2 form-group my-auto">
                                                <button type="button" class="btn btn-info mt-4" onclick="add_constructed_building_row();">नई पंक्ति जोड़े [+]</button>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>

                                    <!-- (V) Vacant Land Details -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(V) खाली पड़ी भूमि का विवरण</h5>
                                    <div id="sec_7_vacant_land_container">
                                        <?php $sec_7_5_count = isset($row_7_5['count']) ? $row_7_5['count'] : 1; ?>
                                        <input type="hidden" name="sec_7_5_count" id="sec_7_5_count" value="<?php echo $sec_7_5_count; ?>">

                                        <?php for($i=1; $i<=$sec_7_5_count; $i++) { ?>
                                        <div class="row" id="sec_7_5_row_<?php echo $i; ?>">
                                            <div class="col-sm-2 form-group">
                                                <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                <input type="text" name="sec_7_5_area_<?php echo $i; ?>" class="form-control chk_decimal">
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
                                                <input type="text" name="sec_7_5_rack_distance_<?php echo $i; ?>" class="form-control">
                                            </div>
                                            <?php if($i == 1) { ?>
                                            <div class="col-sm-2 form-group my-auto">
                                                <button type="button" class="btn btn-info mt-4" onclick="add_vacant_land_row();">नई पंक्ति जोड़े [+]</button>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php } ?>
                                    </div>

                                    <!-- (VI) Access Road Details -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(VI) पहुंच मार्ग का विवरण</h5>
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
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(VII) अन्य</h5>
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
                                <br>
                                <hr>
                                <div class="step">
                                    <h4><img src="images/logo/8.png" class="img-fluid stat-icon" style="height:50px; width:50px;"> 8. सुविधाएं </h4>
                                    
                                    <!-- (I) Facilities -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(I) सुविधाएं</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>सुविधाएं</label>
                                            <select name="sec_8_facilities_status" class="form-control">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- (II) Electricity -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(II) विद्युत</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>विद्युत कनेक्शन</label>
                                            <select name="sec_8_electrical_connection" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- (III) Solar -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(III) सोलर की उपलब्धता</h5>
                                    <div class="row">
                                        <div class="col-sm-6 form-group">
                                            <label>गोदाम के लिए उपयुक्त है या नहीं ?</label>
                                            <select name="sec_8_solar_connection" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--Select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- (IV) Internet -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(IV) इन्टरनेट कनेक्शन</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>इन्टरनेट कनेक्शन</label>
                                            <select name="sec_8_internet_connection" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- (V) Drinking Water -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(V) पेयजल की उपलब्धता</h5>
                                    <div class="row">
                                        <div class="col-sm-3 form-group">
                                            <label>सरकारी नलके का पानी</label>
                                            <select name="sec_8_govt_tap_water" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>पानी की टंकी</label>
                                            <select name="sec_8_water_tank" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>सबमर्सिबल</label>
                                            <select name="sec_8_samarsabel" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 form-group">
                                            <label>हैंड पंप</label>
                                            <select name="sec_8_handpump" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- (VI) Rain Water Harvesting -->
                                    <h5 style="background-color: #f7c94b; color: #333; padding: 10px; border-radius: 5px;">(VI) रेन वाटर हार्वेस्टिंग की उपलब्धता</h5>
                                    <div class="row">
                                        <div class="col-sm-4 form-group">
                                            <label>रेन वाटर हार्वेस्टिंग</label>
                                            <select name="sec_8_rain_water_harvesting" class="form-control" onchange="handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                <option value="">--select--</option>
                                                <option value="yes">हाँ</option>
                                                <option value="no">नहीं</option>
                                            </select>
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
                <input type="hidden" id="id" name="id" value="submit_form_pcu">
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
        if (!id) id = 0;
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
            '<div class="col-sm-3 form-group">' +
            '<label>व्यवसाय का विवरण</label>' +
            '<select name="sec_2_1_2_business_description_' + id + '" id="sec_2_1_2_business_description_' + id + '" class="form-control">' +
            '<option value="">--select--</option>' +
            '<option value="cattle_feed">कैटल फीड</option>' +
            '<option value="any_other">अन्य</option>' +
            '</select>' +
            '</div>' +
            '<div class="col-sm-3 form-group">' +
            '<label>वार्षिक टर्नओवर</label>' +
            '<input type="text" name="sec_2_1_2_value_' + id + '" id="sec_2_1_2_value_' + id + '" class="form-control chk_decimal" data-type="7.3.I वार्षिक टर्नोवर को धनराशि रु० लाख मे भरे">' +
            '</div>' +
            '<div class="col-sm-3 form-group">' +
            '<label>लाभ / हानि</label>' +
            '<select name="sec_2_1_2_profit_loss_' + id + '" id="sec_2_1_2_profit_loss_' + id + '" class="form-control">' +
            '<option value="">--select--</option>' +
            '<option value="लाभ">लाभ</option>' +
            '<option value="हानि">हानि</option>' +
            '</select>' +
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

    $('select[multiple]').multiselect({
        columns: 1,
        placeholder: 'Select options'
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

    function sec_2_b_add_rows() {
        var id = parseInt($("#sec_2_b_id").val()) || 0;

        // Validate existing rows
        for (var i = 1; i <= id; i++) {
            if ($("#sec_2_b_name_" + i).val() == '') {
                // Optional validation: you can remove or adjust as needed
                // alert("कृपया पंक्ति संख्या " + i + " का नाम भरें।");
                // $("#sec_2_b_name_" + i).focus();
                // return;
            }
        }

        id++;

        var row = `
            <tr>
                <td>${id}</td>
                <td>
                    <select name="sec_2_b_type_${id}" id="sec_2_b_type_${id}" class="form-control">
                        <option value="">--select--</option>
                        <option value="शाखा">शाखा</option>
                        <option value="ट्रैनिंग सेंटर">ट्रैनिंग सेंटर</option>
                        <option value="जनपदीय कार्यालय">जनपदीय कार्यालय</option>
                        <option value="क्षेत्रीय कार्यालय">क्षेत्रीय कार्यालय</option>
                        <option value="अन्य कार्यालय">अन्य कार्यालय</option>
                    </select>
                </td>
                <td><input type="text" name="sec_2_b_name_${id}" id="sec_2_b_name_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_division_${id}" id="sec_2_b_division_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_district_${id}" id="sec_2_b_district_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_tehsil_${id}" id="sec_2_b_tehsil_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_address_${id}" id="sec_2_b_address_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_mobile_${id}" id="sec_2_b_mobile_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_email_${id}" id="sec_2_b_email_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_pincode_${id}" id="sec_2_b_pincode_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_latitude_${id}" id="sec_2_b_latitude_${id}" class="form-control"></td>
                <td><input type="text" name="sec_2_b_longitude_${id}" id="sec_2_b_longitude_${id}" class="form-control"></td>
            </tr>
        `;

        $("#sec_2_b tbody").append(row);
        $("#sec_2_b_id").val(id);
    }

    function sec_3_c_add_rows() {
        // Get current count
        var count = parseInt($("#sec_3_b_count").val()) || 0;
        count++; // next row number

        // Create new row HTML
        var rowHtml = `
        <div class="row sec_3_b_row mb-2" id="sec_3_b_row_${count}">

            <!-- श्रेणी -->
            <div class="col-sm-2 form-group">
                <label>श्रेणी</label>
                <select class="form-control" id="sec_3_b_category_${count}" name="category_${count}"
                    onchange="hide_show(this.value, '#sec_3_b_postname_${count}', 'yes'); hide_show(this.value, '#sec_3_b_year_${count}', 'no')">
                    <option value="">--Select--</option>
                    <option value="yes">हाँ </option>
                    <option value="no">नहीं</option>
                </select>
            </div>

            <!-- पद का नाम -->
            <div class="col-sm-2 form-group" id="sec_3_b_postname_${count}" style="display:none;">
                <label>पद का नाम</label>
                <input type="text" class="form-control" name="post_name_${count}" id="post_name_${count}">
            </div>

            <!-- निर्वाचन वर्ष -->
            <div class="col-sm-2 form-group" id="sec_3_b_year_${count}" style="display:none;">
                <label>निर्वाचन का वर्ष</label>
                <select class="form-control" name="year_${count}" id="year_${count}">
                    <option value="">--Select--</option>
                    ${[...Array(2025-1975+1)].map((_, idx) => {
                        var y = 2025 - idx;
                        return `<option value="${y}">${y}</option>`;
                    }).join('')}
                </select>
            </div>

            <!-- पदनाम -->
            <div class="col-sm-2 form-group">
                <label>पदनाम</label>
                <select class="form-control" name="designation_${count}" id="designation_${count}">
                    <option value="">--Select--</option>
                    <option value="अध्यक्ष">अध्यक्ष</option>
                    <option value="उपाध्यक्ष">उपाध्यक्ष</option>
                    <option value="संचालक">संचालक</option>
                </select>
            </div>

            <!-- नाम -->
            <div class="col-sm-2 form-group">
                <label>नाम</label>
                <input type="text" class="form-control" name="name_${count}" id="name_${count}">
            </div>

            <!-- पिता का नाम -->
            <div class="col-sm-2 form-group">
                <label>पिता / पति का नाम</label>
                <input type="text" class="form-control" name="father_${count}" id="father_${count}">
            </div>

            <!-- मोबाइल -->
            <div class="col-sm-2 form-group">
                <label>मोबाईल नंबर</label>
                <input type="text" class="form-control chk_mobile" name="mobile_${count}" id="mobile_${count}" maxlength="10">
            </div>

            <!-- Add Button -->
            <div class="col-sm-2 form-group my-auto">
                <button type="button" class="btn btn-info" onclick="sec_3_c_add_rows()">नई पंक्ति जोड़े [+]</button>
            </div>
        </div>
        `;

        // Append new row
        $("#sec_3_b").append(rowHtml);

        // Update count
        $("#sec_3_b_count").val(count);
    }
    </script>
    <script>
    function addPcuRow() {

        let id = parseInt($("#pcu_resource_id").val());
        if (isNaN(id)) id = 0;

        // Validate existing rows
        for (let i = 1; i <= id; i++) {
            if (!$("#pcu_post_id_" + i).val()) {
                alert("पंक्ति " + i + " में ‘नाम संवर्ग’ खाली है।");
                return;
            }
        }

        id++;
        $("#pcu_resource_id").val(id);

        // Clone options from first dropdown
        let pcuOptions = $("#pcu_post_id_1").html();

        let rowHTML = `
        <div class="row pcu_row mb-2" id="pcu_row_${id}">

            <div class="col-md-2 form-group">
                <label>नाम संवर्ग</label>
                <select name="pcu_post_id[]" id="pcu_post_id_${id}" class="form-control">
                    ${pcuOptions}
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label>स्वीकृत</label>
                <input type="number" name="pcu_sanctioned[]" id="pcu_sanctioned_${id}" class="form-control">
            </div>

            <div class="col-md-2 form-group">
                <label>कार्यरत</label>
                <input type="number" name="pcu_working[]" id="pcu_working_${id}" class="form-control">
            </div>

            <div class="col-md-2 form-group">
                <label>रिक्त</label>
                <input type="number" name="pcu_vacant[]" id="pcu_vacant_${id}" class="form-control">
            </div>

            <div class="col-md-2 form-group">
                <label>प्रा० स्वीकृत</label>
                <input type="number" name="auth_sanctioned[]" id="auth_sanctioned_${id}" class="form-control">
            </div>

            <div class="col-md-2 form-group">
                <label>प्रा० कार्यरत</label>
                <input type="number" name="auth_working[]" id="auth_working_${id}" class="form-control">
            </div>

            <div class="col-md-2 form-group">
                <label>प्रा० रिक्त</label>
                <input type="number" name="auth_vacant[]" id="auth_vacant_${id}" class="form-control">
            </div>

            <div class="col-md-2 form-group">
                <label>अन्य विवरण</label>
                <input type="text" name="pcu_other_detail[]" id="pcu_other_detail_${id}" class="form-control">
            </div>

            <div class="col-md-1 form-group my-auto">
                <button type="button" class="btn btn-info" onclick="addPcuRow()">नई पंक्ति [+]</button>
            </div>

        </div>
        `;

        $("#pcu_resource_rows").append(rowHTML);
    }

</script>
<script>
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
/* Row counter */
let otherLandRowCounter = 0;

/* Utility hide_show() as per your example signature:
   hide_show(this.value, '#electricity_not_available', 'no'); */
function hide_show(val, targetSelector, matchVal) {
  const el = document.querySelector(targetSelector);
  if (!el) return;
  if (val === matchVal) el.style.display = 'block';
  else el.style.display = 'none';
}

/* Render single row. data is optional object with keys matching form names. */
function renderOtherLandRow(data = {}, rowId = null) {
  if (!rowId) {
    otherLandRowCounter++;
    rowId = otherLandRowCounter;
  } else {
    otherLandRowCounter = Math.max(otherLandRowCounter, rowId);
  }

  // sanitize values to display - fallback to empty string
  const v = (k) => (data && data[k] !== undefined && data[k] !== null ? data[k] : '');

  const html = `
  <div class="row other_land_row_box" id="other_land_row_${rowId}" style="padding:15px; border:1px solid #ccc; margin-top:12px;">
    <div class="col-sm-4 form-group">
      <label>1. जिला</label>
      <select class="form-control" name="district_${rowId}">
        <option value="">-- चुनें --</option>
        <option ${v('district')==='जिला 1' ? 'selected':''}>जिला 1</option>
        <option ${v('district')==='जिला 2' ? 'selected':''}>जिला 2</option>
        <option ${v('district')==='जिला 3' ? 'selected':''}>जिला 3</option>
      </select>
    </div>

    <div class="col-sm-4 form-group">
      <label>2. तहसील</label>
      <input type="text" class="form-control" name="tehsil_${rowId}" value="${escapeHtml(v('tehsil'))}">
    </div>

    <div class="col-sm-4 form-group">
      <label>3. शहरी / ग्रामीण</label>
      <select class="form-control" name="area_type_${rowId}">
        <option value="">-- चयन --</option>
        <option ${v('area_type')==='शहरी' ? 'selected':''}>शहरी</option>
        <option ${v('area_type')==='ग्रामीण' ? 'selected':''}>ग्रामीण</option>
      </select>
    </div>

    <div class="col-sm-4 form-group">
      <label>4. भूमि क्षेत्रफल (हे.)</label>
      <input type="text" class="form-control" name="area_hectare_${rowId}" value="${escapeHtml(v('area_hectare'))}" placeholder="हेक्टेयर में">
    </div>

    <div class="col-sm-4 form-group">
      <label>5. स्वामित्व</label>
      <select class="form-control" name="ownership_${rowId}" onchange="hide_show(this.value, '#other_ownership_${rowId}', 'अन्य')">
        <option value="">-- चयन --</option>
        <option ${v('ownership')==='संस्था स्वामित्व' ? 'selected':''}>संस्था स्वामित्व</option>
        <option ${v('ownership')==='पट्टा (लीज)' ? 'selected':''}>पट्टा (लीज)</option>
        <option ${v('ownership')==='अन्य' ? 'selected':''}>अन्य</option>
      </select>
    </div>

    <div class="col-sm-4 form-group" id="other_ownership_${rowId}" style="display:${v('ownership')==='अन्य' ? 'block' : 'none'};">
      <label>6. अन्य स्वामित्व विवरण</label>
      <input type="text" class="form-control" name="other_ownership_detail_${rowId}" value="${escapeHtml(v('other_ownership_detail'))}">
    </div>

    <div class="col-sm-4 form-group">
      <label>7. भूमि की स्थिति</label>
      <select class="form-control" name="land_status_${rowId}">
        <option value="">-- चयन --</option>
        <option ${v('land_status')==='खली पड़ी है' ? 'selected':''}>खली पड़ी है</option>
        <option ${v('land_status')==='निर्माण है' ? 'selected':''}>निर्माण है</option>
        <option ${v('land_status')==='विवादित है' ? 'selected':''}>विवादित है</option>
      </select>
    </div>

    <div class="col-sm-4 form-group">
      <label>8. निर्माण के प्रकार</label>
      <select class="form-control" name="construction_type_${rowId}" onchange="hide_show(this.value, '#other_construction_${rowId}', 'अन्य')">
        <option value="">-- चयन --</option>
        <option ${v('construction_type')==='ऑफिस स्पेस' ? 'selected':''}>ऑफिस स्पेस</option>
        <option ${v('construction_type')==='किराये पर' ? 'selected':''}>किराये पर</option>
        <option ${v('construction_type')==='जर्जर निर्माण' ? 'selected':''}>जर्जर निर्माण</option>
        <option ${v('construction_type')==='अन्य' ? 'selected':''}>अन्य</option>
      </select>
    </div>

    <div class="col-sm-4 form-group" id="other_construction_${rowId}" style="display:${v('construction_type')==='अन्य' ? 'block' : 'none'};">
      <label>9. अन्य निर्माण विवरण</label>
      <input type="text" class="form-control" name="other_construction_detail_${rowId}" value="${escapeHtml(v('other_construction_detail'))}">
    </div>

    <div class="col-sm-12 form-group">
      <label>10. पता</label>
      <textarea class="form-control" rows="2" name="address_${rowId}">${escapeHtml(v('address'))}</textarea>
    </div>

    <div class="col-sm-4 form-group">
      <label>Latitude</label>
      <input type="text" class="form-control" id="lat_${rowId}" name="latitude_${rowId}" value="${escapeHtml(v('latitude'))}" readonly>
    </div>

    <div class="col-sm-4 form-group">
      <label>Longitude</label>
      <input type="text" class="form-control" id="lng_${rowId}" name="longitude_${rowId}" value="${escapeHtml(v('longitude'))}" readonly>
    </div>

    <div class="col-sm-4 form-group">
      <label>लोकेशन मोड</label>
      <select class="form-control" name="location_mode_${rowId}" onchange="toggleLocationMode(${rowId}, this.value)">
        <option value="">-- चयन --</option>
        <option value="manual" ${v('location_mode')==='manual' ? 'selected' : ''}>Manual</option>
        <option value="gps" ${v('location_mode')==='gps' ? 'selected' : ''}>GPS</option>
      </select>
    </div>

    <div class="col-sm-12 form-group" id="gps_btn_${rowId}" style="display:${v('location_mode')==='gps' ? 'block' : 'none'};">
      <button type="button" class="btn btn-success" onclick="getGPSForRow(${rowId})">Click Here to Get GPS</button>
    </div>

    <div class="col-sm-12 form-group text-right">
      <button type="button" class="btn btn-sm btn-danger" onclick="removeOtherLandRow(${rowId})">Remove Row</button>
    </div>

  </div>
  `;

  document.getElementById('other_land_rows_container').insertAdjacentHTML('beforeend', html);

  // If location_mode is manual make lat/lng editable, else readonly
  toggleLocationMode(rowId, v('location_mode') || '');

  // update hidden total
  document.getElementById('other_land_total_rows').value = otherLandRowCounter;
}

/* add empty row */
function addOtherLandRow() {
  renderOtherLandRow({}, null);
}

/* remove given row */
function removeOtherLandRow(rowId) {
  const el = document.getElementById('other_land_row_' + rowId);
  if (el) el.remove();
  // optionally we don't decrement counter; we'll rely on total rows on submit (we'll recalc)
  recalcTotalRows();
}

/* clear all rows */
function clearOtherLandRows() {
  document.getElementById('other_land_rows_container').innerHTML = '';
  otherLandRowCounter = 0;
  document.getElementById('other_land_total_rows').value = 0;
}

/* toggle location mode for a specific row */
function toggleLocationMode(rowId, mode) {
  const lat = document.getElementById('lat_' + rowId);
  const lng = document.getElementById('lng_' + rowId);
  const gpsBtn = document.getElementById('gps_btn_' + rowId);

  if (mode === 'manual') {
    if (lat) lat.readOnly = false;
    if (lng) lng.readOnly = false;
    if (gpsBtn) gpsBtn.style.display = 'none';
  } else if (mode === 'gps') {
    if (lat) lat.readOnly = true;
    if (lng) lng.readOnly = true;
    if (gpsBtn) gpsBtn.style.display = 'block';
  } else {
    // default
    if (lat) lat.readOnly = true;
    if (lng) lng.readOnly = true;
    if (gpsBtn) gpsBtn.style.display = 'none';
  }
}

/* fetch GPS using browser geolocation and fill row inputs */
function getGPSForRow(rowId) {
  if (!navigator.geolocation) {
    alert('Geolocation not supported by this browser.');
    return;
  }
  navigator.geolocation.getCurrentPosition(function(position) {
    const lat = document.getElementById('lat_' + rowId);
    const lng = document.getElementById('lng_' + rowId);
    if (lat) lat.value = position.coords.latitude;
    if (lng) lng.value = position.coords.longitude;
  }, function(err) {
    alert('Unable to fetch GPS. Please allow location access or try again.');
  }, { enableHighAccuracy: true, timeout: 10000 });
}

/* recalc visible rows to set total */
function recalcTotalRows() {
  const rows = document.querySelectorAll('.other_land_row_box');
  // reset counter to length of present highest numeric id or just count
  document.getElementById('other_land_total_rows').value = rows.length;
}

/* escape html in values to safely insert into input values */
function escapeHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

/* Before the form is submitted, recalculate how many rows and set hidden field */
function prepareSubmit() {
  recalcTotalRows();
  // Optionally you can gather indices of rows to send as a CSV to server.
  return true; // allow submit
}

/* Optional: on page load, if no rows exist, you can auto-add one */
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('other_land_rows_container');
  if (container && container.children.length === 0) {
    // keep empty by default or add one row:
    // addOtherLandRow();
  }
});
</script>

<script type="text/javascript" src="js/multistepform_pcu.js?v=1">
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    <script src="js/light-bootstrap-dashboard.js?v=1.4.0">
</script>

<script>
function sec_2_b_add_rows() {
    var countInput = document.getElementById('sec_2_b_id');
    var count = parseInt(countInput.value);
    if (isNaN(count)) {
        count = 0;
    }
    count++;
    countInput.value = count;

    var html = '<tr>';
    html += '<td>' + count + '</td>';
    html += '<td>';
    html += '<select name="sec_2_b_type_' + count + '" id="sec_2_b_type_' + count + '" class="form-control">';
    html += '<option value="">--select--</option>';
    html += '<option value="शाखा">शाखा</option>';
    html += '<option value="ट्रैनिंग सेंटर">ट्रैनिंग सेंटर</option>';
    html += '<option value="अन्य कार्यालय">अन्य कार्यालय</option>';
    html += '</select>';
    html += '</td>';
    html += '<td><input type="text" name="sec_2_b_name_' + count + '" id="sec_2_b_name_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_division_' + count + '" id="sec_2_b_division_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_district_' + count + '" id="sec_2_b_district_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_tehsil_' + count + '" id="sec_2_b_tehsil_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_address_' + count + '" id="sec_2_b_address_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_mobile_' + count + '" id="sec_2_b_mobile_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_email_' + count + '" id="sec_2_b_email_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_pincode_' + count + '" id="sec_2_b_pincode_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_latitude_' + count + '" id="sec_2_b_latitude_' + count + '" class="form-control"></td>';
    html += '<td><input type="text" name="sec_2_b_longitude_' + count + '" id="sec_2_b_longitude_' + count + '" class="form-control"></td>';
    html += '</tr>';

    var tbody = document.querySelector('#sec_2_b table tbody');
    if(tbody) {
        tbody.insertAdjacentHTML('beforeend', html);
    }
}

function add_more_business() {
    var countInput = document.getElementById('other_business_id');
    var count = parseInt(countInput.value);
    if(isNaN(count)) count = 0;
    count++;
    countInput.value = count;

    var html = '<div class="row" id="business_row_' + count + '">';
    html += '<div class="col-sm-5 form-group">';
    html += '<input type="text" name="sec_2_1_2_business_description_' + count + '" id="sec_2_1_2_business_description_' + count + '" class="form-control">';
    html += '</div>';
    
    html += '<div class="col-sm-5 form-group">';
    html += '<input type="text" name="sec_2_1_2_value_' + count + '" id="sec_2_1_2_value_' + count + '" class="form-control chk_decimal" data-type="वार्षिक टर्नोवर को धनराशि रु० लाख मे भरे">';
    html += '</div>';
    
    html += '</div>';

    var container = document.getElementById('other_business');
    if (container) {
        container.insertAdjacentHTML('beforeend', html);
    }
}

function add_constructed_building_row() {
    var countInput = document.getElementById('sec_7_4_count');
    var count = parseInt(countInput.value);
    if(isNaN(count)) count = 0;
    count++;
    countInput.value = count;

    var html = '<div class="row" id="sec_7_4_row_' + count + '">';
    html += '<div class="col-sm-2 form-group"><label>लंबाई (मीटर में)</label><input type="text" name="sec_7_4_length_' + count + '" class="form-control chk_decimal"></div>';
    html += '<div class="col-sm-2 form-group"><label>चौड़ाई (मीटर में)</label><input type="text" name="sec_7_4_width_' + count + '" class="form-control chk_decimal"></div>';
    
    html += '<div class="col-sm-2 form-group"><label>भवन का प्रकार</label><select name="sec_7_4_building_type_' + count + '" class="form-control">';
    html += '<option value="">--Select--</option><option value="office">कार्यालय</option><option value="godown">गोदाम</option><option value="shop">दुकान</option><option value="other">अन्य</option></select></div>';
    
    html += '<div class="col-sm-2 form-group"><label>किस फंड से बना है</label><select name="sec_7_4_fund_source_' + count + '" class="form-control">';
    html += '<option value="">--Select--</option><option value="society">समिति निधि</option><option value="govt">शासकीय अनुदान</option><option value="loan">ऋण</option></select></div>';
    
    html += '<div class="col-sm-2 form-group"><label>टिप्पणी</label><input type="text" name="sec_7_4_remarks_' + count + '" class="form-control"></div>';
    
    html += '</div>';

    var container = document.getElementById('sec_7_constructed_building_container');
    if(container) container.insertAdjacentHTML('beforeend', html);
}

function add_vacant_land_row() {
    var countInput = document.getElementById('sec_7_5_count');
    var count = parseInt(countInput.value);
    if(isNaN(count)) count = 0;
    count++;
    countInput.value = count;

    var html = '<div class="row" id="sec_7_5_row_' + count + '">';
    html += '<div class="col-sm-2 form-group"><label>क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="sec_7_5_area_' + count + '" class="form-control chk_decimal"></div>';
    
    html += '<div class="col-sm-2 form-group"><label>भूमि की स्थिति (उपजाऊ/बंजर)</label><select name="sec_7_5_status_' + count + '" class="form-control">';
    html += '<option value="">--select--</option><option value="fertile">उपजाऊ</option><option value="barren">बंजर</option></select></div>';
    
    html += '<div class="col-sm-2 form-group"><label>स्थान (समिति प्रांगण या अन्य स्थान)</label><select name="sec_7_5_location_' + count + '" class="form-control">';
    html += '<option value="">--select--</option><option value="campus">समिति प्रांगण</option><option value="other">अन्य स्थान</option></select></div>';
    
    html += '<div class="col-sm-2 form-group"><label>गोदाम के लिए उपयुक्त है या नहीं ?</label><select name="sec_7_5_godown_suitable_' + count + '" class="form-control">';
    html += '<option value="">--Select--</option><option value="yes">हाँ</option><option value="no">नहीं</option></select></div>';
    
    html += '<div class="col-sm-2 form-group"><label>जनपद से रैक की दूरी</label><input type="text" name="sec_7_5_rack_distance_' + count + '" class="form-control"></div>';
    
    html += '</div>';

    var container = document.getElementById('sec_7_vacant_land_container');
    if(container) container.insertAdjacentHTML('beforeend', html);
</script>


<?php
page_footer_start();
?>
