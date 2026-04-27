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
    'mobile_number' => '',
    'website' => '',
    'membership_fee' => '',
    'nominal_member' => '',
    'lifetime_member' => '',
    'liquidation' => '',
    'liquidation_date' => '',
    'liquidation_status' => ''
];

if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.sno AS sno, apex_si_1_1.apex_id AS apex_id, longitude, latitude, committee_status, email_id, CONCAT("/user_data/", apex_si_1_1.apex_id, "/", photo_id) AS photo_id, society_registration_no, society_registration_date, prakhand_name, members_no, active_members_no, inactive_members_no, new_members, share_capital, inactive_to_active_no, total_members, address, pincode, pan_no, tan_no, mobile_number, website, membership_fee, nominal_member, lifetime_member, liquidation, liquidation_date, liquidation_status FROM apex_si_1_1 LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '"';

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
        $row_invoice['address'] = $row_invoice['address'];
        $row_invoice['pincode'] = $row_invoice['pincode'];
        $row_invoice['pan_no'] = $row_invoice['pan_no'];
        $row_invoice['tan_no'] = $row_invoice['tan_no'];
        $row_invoice['mobile_number'] = $row_invoice['mobile_number'];
        $row_invoice['website'] = $row_invoice['website'];
        $row_invoice['lifetime_member'] = $row_invoice['lifetime_member'];
        $row_invoice['nominal_member'] = $row_invoice['nominal_member'];
        $row_invoice['membership_fee'] = $row_invoice['membership_fee'];
        $row_invoice['liquidation'] = $row_invoice['liquidation'];
        $row_invoice['liquidation_date'] = $row_invoice['liquidation_date'];
        $row_invoice['liquidation_status'] = $row_invoice['liquidation_status'];
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
        $row_2_b['count'] = 1;
        $row_2_b['sec_2_b_type_1'] = '';
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

		$sql = 'select * from survey_invoice_new_sec_2_1 where survey_id="' . $row_invoice['sno'] . '"';
		// echo $sql;
		$result_sec_2_1 = execute_query($sql);
		$row_sec_2_1 = array();
		$a = 1;
		if (mysqli_num_rows($result_sec_2_1) > 0) {
			while ($row_section_2_1 = mysqli_fetch_assoc($result_sec_2_1)) {
				$row_sec_2_1['scraped_item_name_' . $a] = $row_section_2_1['item_name'];
				$row_sec_2_1['scraped_item_description_' . $a] = $row_section_2_1['item_description'];
				$row_sec_2_1['book_value_' . $a] = $row_section_2_1['book_value'];
				$a++;
			}
		} else {
			$row_sec_2_1['scraped_item_name_1'] = "";
			$row_sec_2_1['scraped_item_description_1'] = "";
			$row_sec_2_1['book_value_1'] = "";
		}


		$sql = 'select * from survey_invoice_new_sec_2_2 where survey_id="' . $row_invoice['sno'] . '"';
		// echo $sql;
		$result_sec_2_2 = execute_query($sql);
		$row_sec_2_2 = array();
		$b = 1;
		if (mysqli_num_rows($result_sec_2_2) > 0) {
			while ($row_section_2_2 = mysqli_fetch_assoc($result_sec_2_2)) {
				$row_sec_2_2['item_name_' . $b] = $row_section_2_2['item_name'];
				$row_sec_2_2['item_description_' . $b] = $row_section_2_2['item_description'];
				$row_sec_2_2['scheme_name_' . $b] = $row_section_2_2['scheme_name'];
				$row_sec_2_2['date_' . $b] = $row_section_2_2['date'];
				$row_sec_2_2['purchase_value_' . $b] = $row_section_2_2['purchase_value'];
				$row_sec_2_2['quantity_' . $b] = $row_section_2_2['quantity'];
				$b++;
			}
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
            $row_6_2['sec_6_2__mob_no_' . $d] = $row_section_6_2_1['mobile_number'];

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

    $row_3_3 = [];

    $sql = 'SELECT * FROM training_centers WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $res_3_3 = execute_query($sql);

    if (mysqli_num_rows($res_3_3) != 0) {
        $i = 1;
        while ($row_3_3_temp = mysqli_fetch_assoc($res_3_3)) {

            $row_3_3['sec_3_cpmt_' . $i]                               = $row_3_3_temp['cpmt_name'];              // नाम
            $row_3_3['sec_3_address_' . $i]                            = $row_3_3_temp['address'];                // पता
            $row_3_3['sec_3_principal_name_' . $i]                     = $row_3_3_temp['principal_name'];         // प्रधानाचार्य नाम
            $row_3_3['sec_3_post_' . $i]                               = $row_3_3_temp['post_name'];              // मूलपद

            $row_3_3['sec_3_principal_house_' . $i]                    = $row_3_3_temp['principal_house'];        // yes/no
            $row_3_3['sec_3_principal_house_no_' . $i]                 = $row_3_3_temp['principal_house_no'];     // संख्या

            $row_3_3['sec_3_principal_office_' . $i]                   = $row_3_3_temp['principal_office'];       // yes/no
            $row_3_3['sec_3_principal_office_no_' . $i]                = $row_3_3_temp['principal_office_no'];    // संख्या

            $row_3_3['sec_3_class_no_' . $i]                           = $row_3_3_temp['classroom_no'];
            $row_3_3['sec_3_class_capacity_' . $i]                     = $row_3_3_temp['classroom_capacity'];

            $row_3_3['sec_3_hostel_no_' . $i]                          = $row_3_3_temp['hostel_no'];
            $row_3_3['sec_3_hostel_capacity_' . $i]                    = $row_3_3_temp['hostel_capacity'];

            $row_3_3['sec_3_library_no_' . $i]                         = $row_3_3_temp['library_no'];
            $row_3_3['sec_3_library_capacity_' . $i]                   = $row_3_3_temp['library_capacity'];

            $row_3_3['sec_3_computer_lab_no_' . $i]                    = $row_3_3_temp['computer_lab_no'];
            $row_3_3['sec_3_computer_lab_capacity_' . $i]              = $row_3_3_temp['computer_lab_capacity'];

            $row_3_3['sec_3_teacher_no_' . $i]                         = $row_3_3_temp['teacher_no'];
            $row_3_3['sec_3_employee_remarks_' . $i]                   = $row_3_3_temp['employee_remarks'];

            $row_3_3['sec_3_training_sessions_no_' . $i]               = $row_3_3_temp['training_sessions_no'];
            $row_3_3['sec_3_training_subject_name_' . $i]              = $row_3_3_temp['training_subject_name'];
            $row_3_3['sec_3_training_sessions_duration_' . $i]         = $row_3_3_temp['training_sessions_duration'];

            $row_3_3['sec_3_departmental_trainees_no_' . $i]           = $row_3_3_temp['departmental_trainees_no'];
            $row_3_3['sec_3_non_departmental_trainees_no_' . $i]       = $row_3_3_temp['non_departmental_trainees_no'];
            $row_3_3['sec_3_trainees_no_' . $i]                        = $row_3_3_temp['trainees_no'];

            $row_3_3['sec_3_departmental_trainees_fee_' . $i]          = $row_3_3_temp['departmental_trainees_fee'];
            $row_3_3['sec_3_non_departmental_trainees_fee_' . $i]      = $row_3_3_temp['non_departmental_trainees_fee'];
            $row_3_3['sec_3_trainees_fee_' . $i]                       = $row_3_3_temp['trainees_fee'];

            $row_3_3['sec_3_departmental_hostel_fee_' . $i]            = $row_3_3_temp['departmental_hostel_fee'];
            $row_3_3['sec_3_non_departmental_hostel_fee_' . $i]        = $row_3_3_temp['non_departmental_hostel_fee'];
            $row_3_3['sec_3_hostel_fee_' . $i]                         = $row_3_3_temp['hostel_fee'];

            $row_3_3['sec_3_build_year_' . $i]                         = $row_3_3_temp['build_year'];             // निर्माण वर्ष
            $row_3_3['sec_3_operation_year_' . $i]                     = $row_3_3_temp['operation_year'];         // संचालन वर्ष

            $row_3_3['sec_3_training_center_' . $i]                    = $row_3_3_temp['training_center'];        // मेरठ/वाराणसी...
            $row_3_3['sec_3_staff_type_' . $i]                         = $row_3_3_temp['staff_type'];             // उ० प्र० कोआपरेटिव यूनियन / सहकारी संघ...

            $row_3_3['sec_3_training_course_benefits_' . $i]           = $row_3_3_temp['training_course_benefits'];
            $row_3_3['sec_3_building_hostel_status_' . $i]             = $row_3_3_temp['building_hostel_status'];

            $i++;
        }
        $row_3_3['count'] = $i - 1;
    } else {
        // default empty 1 row
        $i = 1;
        $row_3_3['count'] = 1;

        $fields = [
            'sec_3_cpmt_', 'sec_3_address_', 'sec_3_principal_name_', 'sec_3_post_',
            'sec_3_principal_house_', 'sec_3_principal_house_no_',
            'sec_3_principal_office_', 'sec_3_principal_office_no_',
            'sec_3_class_no_', 'sec_3_class_capacity_',
            'sec_3_hostel_no_', 'sec_3_hostel_capacity_',
            'sec_3_library_no_', 'sec_3_library_capacity_',
            'sec_3_computer_lab_no_', 'sec_3_computer_lab_capacity_',
            'sec_3_teacher_no_', 'sec_3_employee_remarks_',
            'sec_3_training_sessions_no_', 'sec_3_training_subject_name_', 'sec_3_training_sessions_duration_',
            'sec_3_departmental_trainees_no_', 'sec_3_non_departmental_trainees_no_', 'sec_3_trainees_no_',
            'sec_3_departmental_trainees_fee_', 'sec_3_non_departmental_trainees_fee_', 'sec_3_trainees_fee_',
            'sec_3_departmental_hostel_fee_', 'sec_3_non_departmental_hostel_fee_', 'sec_3_hostel_fee_',
            'sec_3_build_year_', 'sec_3_operation_year_',
            'sec_3_training_center_', 'sec_3_staff_type_',
            'sec_3_training_course_benefits_', 'sec_3_building_hostel_status_',
        ];

        foreach ($fields as $f) {
            $row_3_3[$f . $i] = '';
        }
    }

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
                'pcu_post_id' => $row_p['pcu_post_id'],
                'pcu_sanctioned' => $row_p['pcu_sanctioned'],
                'pcu_working' => $row_p['pcu_working'],
                'pcu_vacant' => $row_p['pcu_vacant'],
                'auth_sanctioned' => $row_p['auth_sanctioned'],
                'auth_working' => $row_p['auth_working'],
                'auth_vacant' => $row_p['auth_vacant'],
                'pcu_other_detail' => $row_p['pcu_other_detail']
            ];
        }
    } else {
        $pcu_rows[] = [
            'pcu_post_id' => '',
            'pcu_sanctioned' => '',
            'pcu_working' => '',
            'pcu_vacant' => '',
            'auth_sanctioned' => '',
            'auth_working' => '',
            'auth_vacant' => '',
            'pcu_other_detail' => ''
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
                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                <label>समिति पंजीकरण संख्या</label>
                                                <br />
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo $row_invoice['society_registration_no']; ?>">
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
                                                <input type="text" name="mobile_number" id="mobile_number"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['mobile_number']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>वेबसाईट</label>
                                                <input type="text" name="website" id="website"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['website']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>समिति पंजीकरण दिनांक</label>
                                                <label><small>नहीं पता होने कि स्थिति में आज का ही दिनांक
                                                        दर्शायें</small></label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo $row_invoice['society_registration_date']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>क्या समिति परिसमापन (Liquidation) में है?</label>
                                                <select name="sec_1_liquidation" id="sec_1_liquidation"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    onchange="hide_show(this.value, '#liquidation_date_container', 'yes');hide_show(this.value, '#liquidation_status', 'yes');handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php echo ($row_invoice['liquidation'] == 'yes') ? ' selected="selected"' : ''; ?> style="background:#0f0"> हाँ
                                                    </option>
                                                    <option value="no" <?php echo ($row_invoice['liquidation'] == 'no') ? ' selected="selected"' : ''; ?> style="background:#f00"> नहीं
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-sm-3 form-group" id="liquidation_date_container"
                                                style="display: none;">
                                                <label>परिसमापक नियुक्त करने की तिथि</label>
                                                <input type="date" tabindex="<?php echo $tab++; ?>"
                                                    id="sec_1_liquidation_date" name="sec_1_liquidation_date"
                                                    class="form-control" placeholder="Choose Date"
                                                    value="<?php echo isset($row_invoice['liquidation_date']) ? $row_invoice['liquidation_date'] : ''; ?>">
                                            </div>

                                            <div class="col-sm-3 form-group" id="liquidation_status"
                                                style="display: none;">
                                                <label>परिसमापन की अद्यतन स्थिति</label>
                                                <input type="text" tabindex="<?php echo $tab++; ?>"
                                                    id="sec_1_liquidation_status" name="sec_1_liquidation_status"
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

                                        <h5> <img src="#" class="img-fluid stat-icon" style="height:50px; width:50px;">
                                            1.1 सदस्यों का प्रकार </h5><br>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label><b>(I) सदस्य समितियों की संख्या</b></label>
                                                <input type="text" name="lifetime_member" id="lifetime_member"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['lifetime_member']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label><b>(II) सामान्य सदस्य की संख्या</b></label>
                                                <input type="text" name="nominal_member" id="nominal_member"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo $row_invoice['nominal_member']; ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label><b>(III) कृषक सदस्य की संख्या</b></label>
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
                                    </div>
                                </div>
                                <!----------------2.1 start-------------------------------------------------------->
                                <div class="step">
										<h4> <img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
												style="height:50px; width:50px;"> 2.1 e-Dead Stock Register(ई-डेड स्टॉक
											रजिस्टर)</h4>
										<div class="row">
											<div class="col-md-3"><label for="">क्या कंप्यूटर इंफ्रास्ट्रक्चर का बीमा हुआ
													है?</label>
												<select name="sec_4_computer_insurance" id="sec_4_computer_insurance"
													class="form-control">
													<option value="">--select--</option>
													<option value="yes" <?php echo $row_4_1['sec_4_computer_insurance'] == 'yes' ? 'selected="selected"' : '' ?> style="background:#0f0">हाँ
													</option>
													<option value="no" <?php echo $row_4_1['sec_4_computer_insurance'] == 'no' ? 'selected="selected"' : '' ?> style="background:#f00">नहीं
													</option>
												</select>
											</div>
										</div>

										<div class="col-sm-12">

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


											<br><br>
											<h5>2.2 निष्प्रयोज्य डेड स्टाक</h5>
											<br>

											<table class="table table-bordered table-striped table-hover">
												<thead>
													<tr>
														<th>S.No.</th>
														<th>Item Name(वस्तु का नाम)</th>
														<th>Closing Stock as on 31st March, 2023(31 मार्च, 2023 को समापन
															स्टॉक)</th>
														<th>Book Value(बुक वैल्यू)</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$row_value = 10;
													$r = 1;
													while ($row_value > 0) {
														// echo @@@$row_sec_2_1['scraped_item_name_' .$r];
														echo '<tr>
															<td>' . $r . '</td>
															
															<td><input type="text" name="scraped_item_name_' . $r . '" value="' . $row_sec_2_1['scraped_item_name_' . $r] . '" class="form-control"></td> 
															<td><input type="text" name="scraped_item_description_' . $r . '" value="' . $row_sec_2_1['scraped_item_description_' . $r] . '" class="form-control" data-type="5.2 Closing Stock as on 31st March, 2023(31 मार्च, 2023 को समापन स्टॉक)' . $r . ' को को अंक मे भरे"></td>
															<td><input type="text" name="book_value_' . $r . '" value="' . $row_sec_2_1['book_value_' . $r] . '" class="form-control chk_decimal" data-type="5.2 बुक वैल्यू ' . $r . ' को धनराशि रु० मे भरे"></td>
														  </tr>';
														$r++;
														$row_value--;
													}
													?>
												</tbody>


											</table>
											<br><br>
											<h5>2.3 वित्तीय वर्ष 2024-25 में नये खरीदे गये डेड स्टाक का विवरण</h5><br>
											<table class="table table-bordered table-striped table-hover">
												<tbody>
													<thead>
														<tr>
															<th>क्र०स०</th>
															<th>वस्तु का नाम</th>
															<th>ब्रांड नाम के साथ आइटम विवरण</th>
															<th>योजना का नाम</th>
															<th>दिनांक</th>
															<th>खरीद मूल्य</th>
															<th>मात्रा</th>
														</tr>
													</thead>

												<tbody>
													<?php
													$row_val = 10;
													$s = 1;
													while ($row_val > 0) {
														echo '<tr>
															<td>' . $s . '</td>
															<td><input type="text" name="item_name_' . $s . '" value="' . $row_sec_2_2['item_name_' . $s] . '" class="form-control"></td>
															<td><input type="text" name="item_description_' . $s . '" value="' . $row_sec_2_2['item_description_' . $s] . '" class="form-control"></td>
															<td><input type="text" name="scheme_name_' . $s . '" value="' . $row_sec_2_2['scheme_name_' . $s] . '" class="form-control"></td>
															<td><input type="text" id="Purchase_date_' . $s . '" name="date_' . $s . '" value="' . $row_sec_2_2['date_' . $s] . '" class="form-control"></td>
															<td><input type="text" id="purchase_value_' . $s . '" name="purchase_value_' . $s . '" value="' . $row_sec_2_2['purchase_value_' . $s] . '" class="form-control chk_decimal" data-type="5.3 वि० वर्ष० -24-25 Purchase Value-' . $s . '"></td>
															<td><input type="text" name="quantity_' . $s . '" value="' . $row_sec_2_2['quantity_' . $s] . '" class="form-control chk_number" data-type="5.3 वि० वर्ष० मात्रा-' . $s . '"></td>
																
														  </tr>';
														$s++;
														$row_val--;
													}


													?>
												</tbody>

											</table>
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
                                        <h5>3(II) मूल्य समर्थन योजना</h5><br>
                                        <div class="table-responsive">
                                            <label style="font-size:15px;margin:7px;"><b>(I) धान एवं गेहू क्रय की
                                                    प्रगति</b></label>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>वर्ष</th>
                                                        <th>विवरण</th>
                                                        <th>लक्ष्य (मै० टन)</th>
                                                        <th>पूर्ति (मै० टन)</th>
                                                        <th>लाभान्वित कृषकों की संख्या</th>
                                                        <th>कृषकों को भुगतान (लाख ₹ में)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td rowspan="2">2023-24</td>
                                                        <td>धान</td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_target_1" id="msy_1_target_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_target_1']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_supply_1" id="msy_1_supply_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_supply_1']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_member_no_1" id="msy_1_member_no_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_member_no_1']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_payment_to_farmer_1"
                                                                id="msy_1_payment_to_farmer_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_payment_to_farmer_1']) ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>गेहू</td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_target_2" id="msy_1_target_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_target_2']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_supply_2" id="msy_1_supply_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_supply_2']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_member_no_2" id="msy_1_member_no_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_member_no_2']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_1_payment_to_farmer_2"
                                                                id="msy_1_payment_to_farmer_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_1_payment_to_farmer_2']) ?>">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <label style="font-size:15px;margin:7px;"><b>(II) दलहन तिलहन खरीद की
                                                प्रगति</b></label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>वर्ष</th>
                                                        <th>विवरण</th>
                                                        <th>लक्ष्य (मै० टन)</th>
                                                        <th>पूर्ति (मै० टन)</th>
                                                        <th>लाभान्वित कृषकों की संख्या</th>
                                                        <th>कृषकों को भुगतान (करोड़ ₹ में)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td rowspan="4">2023-24</td>
                                                        <td>सरसों</td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_target_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_target_1']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_supply_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_supply_1']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_member_no_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_member_no_1']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_payment_to_farmer_1"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_payment_to_farmer_1']) ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>मूंगफली</td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_target_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_target_2']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_supply_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_supply_2']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_member_no_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_member_no_2']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_payment_to_farmer_2"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_payment_to_farmer_2']) ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>चना</td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_target_3"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_target_3']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_supply_3"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_supply_3']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_member_no_3"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_member_no_3']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_payment_to_farmer_3"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_payment_to_farmer_3']) ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>मसूर</td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_target_4"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_target_4']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_supply_4"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_supply_4']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_member_no_4"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_member_no_4']) ?>">
                                                        </td>
                                                        <td><input type="text" class="form-control"
                                                                name="msy_2_payment_to_farmer_4"
                                                                value="<?= htmlspecialchars($row_msy['msy_2_payment_to_farmer_4']) ?>">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-------4th start------->
                                <div class="step">
                                    <h4><img src="images/logo/4.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 4. अन्य कार्य व व्यवसाय</h4>
                                    <div id="other_business">
                                        <?php
                                        for ($i = 1; $i <= $row_2_1_2['count']; $i++) {
                                            ?>
                                        <div class="row" id="business_row_<?php echo $i; ?>">
                                            <div class="col-sm-3 form-group">
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

                                            <div class="col-sm-3 form-group">
                                                <label>वार्षिक टर्नओवर</label>
                                                <input type="text" name="sec_2_1_2_value_<?php echo $i; ?>"
                                                    tabindex="<?php echo $tab++; ?>"
                                                    id="sec_2_1_2_value_<?php echo $i; ?>"
                                                    class="form-control chk_decimal"
                                                    data-type="7.3.I वार्षिक टर्नोवर को धनराशि रु० लाख मे भरे"
                                                    value="<?php echo $row_2_1_2['sec_2_1_2_value_' . $i]; ?>">
                                            </div>

                                            <div class="col-sm-3 form-group">
                                                <label>लाभ / हानि</label>
                                                <select name="sec_2_1_2_profit_loss_<?php echo $i; ?>"
                                                    id="sec_2_1_2_profit_loss_<?php echo $i; ?>" class="form-control">
                                                    <option value="">--select--</option>
                                                    <option value="लाभ" <?php echo ($row_2_1_2['sec_2_1_2_profit_loss_' . $i] == 'लाभ') ? 'selected' : ''; ?>>लाभ</option>
                                                    <option value="हानि" <?php echo ($row_2_1_2['sec_2_1_2_profit_loss_' . $i] == 'हानि') ? 'selected' : ''; ?>>हानि</option>
                                                </select>
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
                                    <h5> <img src="images/logo/2.png" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 5.1 शाखाओं / ट्रेनिंग सेंटर / अन्य
                                        कार्यालयों का विवरण </h5><br>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div id="sec_2_b" style="overflow-x:auto; width:100%">
                                                <table class="table table-bordered table-striped table-hover">
                                                    <thead>
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
                                                                    <option value="जनपदीय कार्यालय" <?php echo $row_2_b['sec_2_b_type_' . $i] == 'जनपदीय कार्यालय' ? 'selected' : ''; ?>>जनपदीय
                                                                        कार्यालय</option>
                                                                    <option value="क्षेत्रीय कार्यालय" <?php echo $row_2_b['sec_2_b_type_' . $i] == 'क्षेत्रीय कार्यालय' ? 'selected' : ''; ?>>क्षेत्रीय
                                                                        कार्यालय</option>
                                                                    <option value="अन्य कार्यालय" <?php echo $row_2_b['sec_2_b_type_' . $i] == 'अन्य कार्यालय' ? 'selected' : ''; ?>>अन्य
                                                                        कार्यालय</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="text" name="sec_2_b_name_<?php echo $i; ?>"
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

                                                            <?php if ($i == $row_2_b['count']) { ?>
                                                            <td id="sec_2_b_rows">
                                                                <button type="button" class="btn btn-info"
                                                                    onclick="sec_2_b_add_rows();">नई पंक्ति जोड़े
                                                                    [+]</button>
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <input type="hidden" name="sec_2_b_id" id="sec_2_b_id"
                                                value="<?php echo $row_2_b['count']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <h5>
                                            <img src="#" class="img-fluid stat-icon" style="height:60px; width:60px;">
                                            5.2. सहकारी प्रबंध प्रशिक्षण केंद्र
                                        </h5>
                                        <div id="sec_3_training_center">
                                            <?php
                                            $count = !empty($row_3_3['count']) ? $row_3_3['count'] : 1;
                                            for ($i = 1; $i <= $count; $i++) {
                                            ?>
                                                <div class="row sec-3-row" id="sec_3_row_<?php echo $i; ?>">
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>नाम :- सहकारी प्रबंध प्रशिक्षण केंद्र</label>
                                                                <input name="sec_3_cpmt_<?php echo $i; ?>"
                                                                    id="sec_3_cpmt_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_cpmt_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>पता</label>
                                                                <input name="sec_3_address_<?php echo $i; ?>"
                                                                    id="sec_3_address_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_address_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>पदेन प्रधानाचार्य नाम</label>
                                                                <input name="sec_3_principal_name_<?php echo $i; ?>"
                                                                    id="sec_3_principal_name_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_principal_name_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>मूलपद</label>
                                                                <input name="sec_3_post_<?php echo $i; ?>"
                                                                    id="sec_3_post_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_post_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>प्रधानाचार्य आवास</label>
                                                                <select name="sec_3_principal_house_<?php echo $i; ?>"
                                                                        id="sec_3_principal_house_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        onchange="hide_show(this.value, '#sec_3_principal_house_no_box_<?php echo $i; ?>', 'yes');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes" <?php echo ($row_3_3['sec_3_principal_house_' . $i] == 'yes') ? 'selected' : ''; ?>>हाँ</option>
                                                                    <option value="no"  <?php echo ($row_3_3['sec_3_principal_house_' . $i] == 'no')  ? 'selected' : ''; ?>>नहीं</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4"
                                                                id="sec_3_principal_house_no_box_<?php echo $i; ?>"
                                                                style="<?php echo ($row_3_3['sec_3_principal_house_' . $i] == 'yes') ? '' : 'display:none'; ?>">
                                                                <label>संख्या</label>
                                                                <input name="sec_3_principal_house_no_<?php echo $i; ?>"
                                                                    id="sec_3_principal_house_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_principal_house_no_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>प्रधानाचार्य कार्यालय</label>
                                                                <select name="sec_3_principal_office_<?php echo $i; ?>"
                                                                        id="sec_3_principal_office_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"
                                                                        onchange="hide_show(this.value, '#sec_3_principal_office_no_box_<?php echo $i; ?>', 'yes');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="yes" <?php echo ($row_3_3['sec_3_principal_office_' . $i] == 'yes') ? 'selected' : ''; ?>>हाँ</option>
                                                                    <option value="no"  <?php echo ($row_3_3['sec_3_principal_office_' . $i] == 'no')  ? 'selected' : ''; ?>>नहीं</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4"
                                                                id="sec_3_principal_office_no_box_<?php echo $i; ?>"
                                                                style="<?php echo ($row_3_3['sec_3_principal_office_' . $i] == 'yes') ? '' : 'display:none'; ?>">
                                                                <label>संख्या</label>
                                                                <input name="sec_3_principal_office_no_<?php echo $i; ?>"
                                                                    id="sec_3_principal_office_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_principal_office_no_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>क्लासरूम संख्या</label>
                                                                <input name="sec_3_class_no_<?php echo $i; ?>"
                                                                    id="sec_3_class_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_class_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_class_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_class_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_class_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>हॉस्टल संख्या</label>
                                                                <input name="sec_3_hostel_no_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_hostel_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_hostel_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_hostel_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>पुस्तकालय संख्या</label>
                                                                <input name="sec_3_library_no_<?php echo $i; ?>"
                                                                    id="sec_3_library_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_library_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_library_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_library_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_library_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>कंप्युटर लैब संख्या</label>
                                                                <input name="sec_3_computer_lab_no_<?php echo $i; ?>"
                                                                    id="sec_3_computer_lab_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_computer_lab_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>क्षमता</label>
                                                                <input name="sec_3_computer_lab_capacity_<?php echo $i; ?>"
                                                                    id="sec_3_computer_lab_capacity_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_computer_lab_capacity_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>अध्यापक / अतिथि प्रवक्ता संख्या</label>
                                                                <input name="sec_3_teacher_no_<?php echo $i; ?>"
                                                                    id="sec_3_teacher_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_teacher_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>कर्मचारी विवरण</label>
                                                                <textarea name="sec_3_employee_remarks_<?php echo $i; ?>"
                                                                        id="sec_3_employee_remarks_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"><?php echo $row_3_3['sec_3_employee_remarks_' . $i]; ?></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षण सत्रों की संख्या</label>
                                                                <input name="sec_3_training_sessions_no_<?php echo $i; ?>"
                                                                    id="sec_3_training_sessions_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_training_sessions_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षण विषय के नाम</label>
                                                                <input name="sec_3_training_subject_name_<?php echo $i; ?>"
                                                                    id="sec_3_training_subject_name_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_training_subject_name_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>प्रशिक्षण सत्र अवधि</label>
                                                                <input type="date"
                                                                    name="sec_3_training_sessions_duration_<?php echo $i; ?>"
                                                                    id="sec_3_training_sessions_duration_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_training_sessions_duration_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>विभागीय प्रशिक्षार्थियों की संख्या</label>
                                                                <input name="sec_3_departmental_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_departmental_trainees_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>गैर-विभागीय प्रशिक्षार्थियों की संख्या</label>
                                                                <input name="sec_3_non_departmental_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_non_departmental_trainees_no_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षार्थियों की संख्या</label>
                                                                <input name="sec_3_trainees_no_<?php echo $i; ?>"
                                                                    id="sec_3_trainees_no_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    readonly
                                                                    value="<?php echo $row_3_3['sec_3_trainees_no_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input name="sec_3_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_departmental_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>गैर-विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input name="sec_3_non_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_non_departmental_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>प्रशिक्षार्थी प्रशिक्षण शुल्क</label>
                                                                <input name="sec_3_trainees_fee_<?php echo $i; ?>"
                                                                    id="sec_3_trainees_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    readonly
                                                                    value="<?php echo $row_3_3['sec_3_trainees_fee_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label>विभागीय हॉस्टल शुल्क</label>
                                                                <input name="sec_3_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_departmental_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>गैर-विभागीय हॉस्टल शुल्क</label>
                                                                <input name="sec_3_non_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_non_departmental_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_non_departmental_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>हॉस्टल शुल्क</label>
                                                                <input name="sec_3_hostel_fee_<?php echo $i; ?>"
                                                                    id="sec_3_hostel_fee_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    readonly
                                                                    value="<?php echo $row_3_3['sec_3_hostel_fee_' . $i]; ?>">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-sm-3">
                                                                <label>निर्माण वर्ष</label>
                                                                <select name="sec_3_build_year_<?php echo $i; ?>"
                                                                        id="sec_3_build_year_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--Select--</option>
                                                                    <option value="1999" <?php echo ($row_3_3['sec_3_build_year_' . $i] == '1999') ? 'selected' : ''; ?>>2000 से पूर्व</option>
                                                                    <?php for ($y = 2000; $y <= 2024; $y++) { ?>
                                                                        <option value="<?php echo $y; ?>" <?php echo ($row_3_3['sec_3_build_year_' . $i] == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>संचालन वर्ष</label>
                                                                <select name="sec_3_operation_year_<?php echo $i; ?>"
                                                                        id="sec_3_operation_year_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--Select--</option>
                                                                    <option value="1999" <?php echo ($row_3_3['sec_3_operation_year_' . $i] == '1999') ? 'selected' : ''; ?>>2000 से पूर्व</option>
                                                                    <?php for ($y = 2000; $y <= 2024; $y++) { ?>
                                                                        <option value="<?php echo $y; ?>" <?php echo ($row_3_3['sec_3_operation_year_' . $i] == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label>सहकारी प्रबंध प्रशिक्षण केंद्र</label>
                                                                <select name="sec_3_training_center_<?php echo $i; ?>"
                                                                        id="sec_3_training_center_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--Select--</option>
                                                                    <option value="meerut"   <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'meerut')   ? 'selected' : ''; ?>>मेरठ</option>
                                                                    <option value="varanasi" <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'varanasi') ? 'selected' : ''; ?>>वाराणसी</option>
                                                                    <option value="mahoba"   <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'mahoba')   ? 'selected' : ''; ?>>महोबा</option>
                                                                    <option value="hewra"    <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'hewra')    ? 'selected' : ''; ?>>हेवरा (ईटवा)</option>
                                                                    <option value="ayodhya"  <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'ayodhya')  ? 'selected' : ''; ?>>अयोध्या (फैजाबाद)</option>
                                                                    <option value="bilari"   <?php echo ($row_3_3['sec_3_training_center_' . $i] == 'bilari')   ? 'selected' : ''; ?>>बिलारी (मोरादाबाद)</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-3 form-group">
                                                                <label>कार्मिक की संख्या</label>
                                                                <select name="sec_3_staff_type_<?php echo $i; ?>"
                                                                        id="sec_3_staff_type_<?php echo $i; ?>"
                                                                        class="form-control"
                                                                        tabindex="<?php echo $tab++; ?>">
                                                                    <option value="">--select-- </option>
                                                                    <option value="union"   <?php echo ($row_3_3['sec_3_staff_type_' . $i] == 'union')   ? 'selected' : ''; ?>>उ० प्र० कोआपरेटिव यूनियन</option>
                                                                    <option value="authority" <?php echo ($row_3_3['sec_3_staff_type_' . $i] == 'authority') ? 'selected' : ''; ?>>सहकारी संघ प्राधिकारी</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-3">
                                                                <label>प्रशिक्षण कोर्स लाभ</label>
                                                                <textarea name="sec_3_training_course_benefits_<?php echo $i; ?>"
                                                                        id="sec_3_training_course_benefits_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"><?php echo $row_3_3['sec_3_training_course_benefits_' . $i]; ?></textarea>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>भवन/हॉस्टल स्तिथि</label>
                                                                <textarea name="sec_3_building_hostel_status_<?php echo $i; ?>"
                                                                        id="sec_3_building_hostel_status_<?php echo $i; ?>"
                                                                        tabindex="<?php echo $tab++; ?>"
                                                                        class="form-control"><?php echo $row_3_3['sec_3_building_hostel_status_' . $i]; ?></textarea>
                                                            </div>
                                                        </div>

                                                        <?php if ($i == $count) { ?>
                                                            <div class="col-sm-2 form-group my-auto" id="sec_3_add_rows_wrapper">
                                                                <button type="button" class="btn btn-info" onclick="sec_3_add_rows()">नई पंक्ति जोड़े [+]</button>
                                                                <input type="hidden" name="sec_3_row_count" id="sec_3_row_count"
                                                                    value="<?php echo $count; ?>">
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

                                        <!-- <h3>Hierarchy Tree Output</h3>
                                                    <div id="output" class="tree-output"></div> -->

                                        <h5><img src="images/logo/6.png" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> (II) मानव सम्पदा</h5>
                                        <?php
                                        $pcu_posts = [];
                                        $sql_pcu = "SELECT `sno`, `post_name` FROM `master_posts_pcu` ORDER BY `post_name` ASC";
                                        $result_pcu = execute_query($sql_pcu);
                                        if ($result_pcu && mysqli_num_rows($result_pcu) > 0) {
                                            while ($r = mysqli_fetch_assoc($result_pcu)) {
                                                $pcu_posts[] = $r;
                                            }
                                        }
                                        ?>
                                        <div id="pcu_resource_rows">
                                            <?php
                                            $pcuIndex = 1;
                                            if (!empty($pcu_rows)) {
                                                foreach ($pcu_rows as $p) {
                                                    ?>
                                                    <div class="row pcu_row mb-2" id="pcu_row_<?php echo $pcuIndex; ?>">

                                                        <div class="col-md-2 form-group">
                                                            <label>नाम संवर्ग</label>
                                                            <select name="pcu_post_id[]"
                                                                id="pcu_post_id_<?php echo $pcuIndex; ?>" class="form-control">
                                                                <option value="">--Select--</option>
                                                                <?php
                                                                foreach ($pcu_posts as $pp) {
                                                                    $pid = htmlspecialchars($pp['sno']);
                                                                    $pname = htmlspecialchars($pp['post_name']);
                                                                    $selected = ($p['pcu_post_id'] == $pid) ? "selected" : "";
                                                                    echo "<option value='{$pid}' {$selected}>{$pname}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-2 form-group">
                                                            <label>स्वीकृत</label>
                                                            <input type="number" name="pcu_sanctioned[]" class="form-control"
                                                                id="pcu_sanctioned_<?php echo $pcuIndex; ?>"
                                                                value="<?php echo htmlspecialchars($p['pcu_sanctioned']); ?>">
                                                        </div>

                                                        <div class="col-md-2 form-group">
                                                            <label>कार्यरत</label>
                                                            <input type="number" name="pcu_working[]" class="form-control"
                                                                id="pcu_working_<?php echo $pcuIndex; ?>"
                                                                value="<?php echo htmlspecialchars($p['pcu_working']); ?>">
                                                        </div>

                                                        <div class="col-md-2 form-group">
                                                            <label>रिक्त</label>
                                                            <input type="number" name="pcu_vacant[]" class="form-control"
                                                                id="pcu_vacant_<?php echo $pcuIndex; ?>"
                                                                value="<?php echo htmlspecialchars($p['pcu_vacant']); ?>">
                                                        </div>

                                                        <div class="col-md-2 form-group">
                                                            <label>प्रा० स्वीकृत</label>
                                                            <input type="number" name="auth_sanctioned[]" class="form-control"
                                                                id="auth_sanctioned_<?php echo $pcuIndex; ?>"
                                                                value="<?php echo htmlspecialchars($p['auth_sanctioned']); ?>">
                                                        </div>

                                                        <div class="col-md-2 form-group">
                                                            <label>प्रा० कार्यरत</label>
                                                            <input type="number" name="auth_working[]" class="form-control"
                                                                id="auth_working_<?php echo $pcuIndex; ?>"
                                                                value="<?php echo htmlspecialchars($p['auth_working']); ?>">
                                                        </div>

                                                        <div class="col-md-2 form-group">
                                                            <label>प्रा० रिक्त</label>
                                                            <input type="number" name="auth_vacant[]" class="form-control"
                                                                id="auth_vacant_<?php echo $pcuIndex; ?>"
                                                                value="<?php echo htmlspecialchars($p['auth_vacant']); ?>">
                                                        </div>

                                                        <div class="col-md-2 form-group">
                                                            <label>अन्य विवरण</label>
                                                            <input type="text" name="pcu_other_detail[]" class="form-control"
                                                                id="pcu_other_detail_<?php echo $pcuIndex; ?>"
                                                                value="<?php echo htmlspecialchars($p['pcu_other_detail']); ?>">
                                                        </div>

                                                        <div class="col-md-1 form-group my-auto">
                                                            <?php if ($pcuIndex == 1) { ?>
                                                                <button type="button" class="btn btn-info" onclick="addPcuRow()">नई
                                                                    पंक्ति [+]</button>
                                                                <input type="hidden" id="pcu_resource_id" name="pcu_resource_id"
                                                                    value="<?php echo count($pcu_rows); ?>">
                                                            <?php } else { ?>
                                                                <button type="button" class="btn btn-danger"
                                                                    onclick="$(this).closest('.pcu_row').remove()">हटाएं
                                                                    [-]</button>
                                                            <?php } ?>
                                                        </div>

                                                    </div>
                                                    <?php
                                                    $pcuIndex++;
                                                }
                                            }
                                            ?>
                                        </div>

                                        <br>
                                        <h5><img src="images/logo/7.png" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> (III) प्रबंध कमेटी</h5>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>श्रेणी</label>
                                                <select name="sec_2_stock_insurance" class="form-control"
                                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--select-- </option>
                                                    <option value="yes">पदेन</option>
                                                    <option value="yes">निर्वाचित</option>
                                                    <option value="no">नामित</option>
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
                                                <select name="sec_2_stock_insurance" class="form-control"
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
                                                <button type="button" class="btn btn-info" onclick="()">नई
                                                    पंक्ति जोड़े [+]</button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="sec_6_2_id" id="sec_6_2_id"
                                            value="<?php echo $row_6_2['count']; ?>">
                                        <br>
                                        <h5><img src="images/logo/7.png" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> (IV) प्राधिकारी कमेटी</h5>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>श्रेणी</label>
                                                <select name="sec_2_stock_insurance" class="form-control"
                                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--select-- </option>
                                                    <option value="yes">पदेन</option>
                                                    <option value="yes">निर्वाचित</option>
                                                    <option value="no">नामित</option>
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
                                                <select name="sec_2_stock_insurance" class="form-control"
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
                                                <button type="button" class="btn btn-info" onclick="()">नई
                                                    पंक्ति जोड़े [+]</button>
                                            </div>
                                        </div>
                                        <h5><img src="images/logo/7.png" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> (V) प्रशासनिक कमेटी</h5>
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>श्रेणी</label>
                                                <select name="sec_2_stock_insurance" class="form-control"
                                                    onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                    <option value="">--select-- </option>
                                                    <option value="yes">पदेन</option>
                                                    <option value="yes">निर्वाचित</option>
                                                    <option value="no">नामित</option>
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
                                                <select name="sec_2_stock_insurance" class="form-control"
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
                                                <button type="button" class="btn btn-info" onclick="()">नई
                                                    पंक्ति जोड़े [+]</button>
                                            </div>
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
                                            <h5>(VII)अन्य भूमि का विवरण</h5>
                                            <div id="other_land_rows_container">
                                                <?php for ($i = 1; $i <= $row_other_land['count']; $i++) { ?>
                                                <div class="other-land-row" id="other_land_row_<?php echo $i; ?>"
                                                    style="border:1px solid #ccc; padding:10px; margin-bottom:12px;">

                                                    <div class="row">
                                                        <!-- LEFT SIDE -->
                                                            <div class="col-sm-8">

                                                                <div class="row">
                                                                    <div class="col-sm-3 form-group">
                                                                        <label>1. जिला</label>
                                                                        <?php
                                                                        $district_options = '<option value="">--Select--</option>';
                                                                        $res_d = execute_query($sql);
                                                                        while ($d = mysqli_fetch_assoc($res_d)) {
                                                                            $selected = ($row_other_land['other_land_district_' . $i] == $d['sno']) ? ' selected="selected"' : '';
                                                                            $district_options .= '<option value="' . $d['sno'] . '"' . $selected . '>' . $d['district_name'] . '</option>';
                                                                        }
                                                                        ?>
                                                                        <select name="other_land_district_<?php echo $i; ?>"
                                                                            id="other_land_district_<?php echo $i; ?>"
                                                                            class="form-control"
                                                                            onchange="fill_other_tehsil(this.value, <?php echo $i; ?>)">
                                                                            <?php echo $district_options; ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-sm-3 form-group">
                                                                        <label>2. तहसील</label>
                                                                        <select name="other_land_tehsil_<?php echo $i; ?>"
                                                                            id="other_land_tehsil_<?php echo $i; ?>"
                                                                            class="form-control">
                                                                            <option value="">--Select--</option>
                                                                            <?php
                                                                            if (!empty($row_other_land['other_land_tehsil_' . $i])) {
                                                                                echo '<option selected value="' . $row_other_land['other_land_tehsil_' . $i] . '">' . $row_other_land['other_land_tehsil_' . $i] . '</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-sm-3 form-group">
                                                                        <label>3. शहरी / ग्रामीण</label>
                                                                        <select
                                                                            name="other_land_area_type_<?php echo $i; ?>"
                                                                            id="other_land_area_type_<?php echo $i; ?>"
                                                                            class="form-control">
                                                                            <option value="">-- चयन --</option>
                                                                            <option <?php echo ($row_other_land['other_land_area_type_' . $i] == "शहरी") ? 'selected' : ''; ?>>शहरी
                                                                            </option>
                                                                            <option <?php echo ($row_other_land['other_land_area_type_' . $i] == "ग्रामीण") ? 'selected' : ''; ?>>
                                                                                ग्रामीण</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-sm-3 form-group">
                                                                        <label>4. भूमि क्षेत्रफल (हे.)</label>
                                                                        <input type="text"
                                                                            name="other_land_land_area_<?php echo $i; ?>"
                                                                            id="other_land_land_area_<?php echo $i; ?>"
                                                                            class="form-control"
                                                                            value="<?php echo $row_other_land['other_land_land_area_' . $i]; ?>">
                                                                    </div>

                                                                    <div class="col-sm-3 form-group">
                                                                        <label>5. स्वामित्व</label>
                                                                        <select
                                                                            name="other_land_ownership_<?php echo $i; ?>"
                                                                            id="other_land_ownership_<?php echo $i; ?>"
                                                                            class="form-control other_owner_select"
                                                                            data-row="<?php echo $i; ?>">
                                                                            <option value="">-- चयन --</option>
                                                                            <option <?php echo ($row_other_land['other_land_ownership_' . $i] == "संस्था स्वामित्व") ? 'selected' : ''; ?>>संस्था स्वामित्व
                                                                            </option>
                                                                            <option <?php echo ($row_other_land['other_land_ownership_' . $i] == "पट्टा (लीज)") ? 'selected' : ''; ?>>
                                                                                पट्टा (लीज)</option>
                                                                            <option <?php echo ($row_other_land['other_land_ownership_' . $i] == "अन्य") ? 'selected' : ''; ?>>अन्य
                                                                            </option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-sm-3 form-group"
                                                                        id="other_owner_div_<?php echo $i; ?>"
                                                                        style="<?php echo ($row_other_land['other_land_ownership_' . $i] == "अन्य" ? '' : 'display:none;'); ?>">
                                                                        <label>6. किसके स्वामित्व में है?</label>
                                                                        <input type="text"
                                                                            name="other_land_other_owner_<?php echo $i; ?>"
                                                                            id="other_land_other_owner_<?php echo $i; ?>"
                                                                            class="form-control"
                                                                            value="<?php echo $row_other_land['other_land_other_owner_' . $i]; ?>">
                                                                    </div>

                                                                    <div class="col-sm-3 form-group">
                                                                        <label>7. भूमि की स्थिति</label>
                                                                        <select
                                                                            name="other_land_land_status_<?php echo $i; ?>"
                                                                            id="other_land_land_status_<?php echo $i; ?>"
                                                                            class="form-control land_status_select"
                                                                            data-row="<?php echo $i; ?>">
                                                                            <option value="">-- चयन --</option>
                                                                            <option <?php echo ($row_other_land['other_land_land_status_' . $i] == "खली पड़ी है") ? 'selected' : ''; ?>>
                                                                                खली पड़ी है</option>
                                                                            <option <?php echo ($row_other_land['other_land_land_status_' . $i] == "निर्माण") ? 'selected' : ''; ?>>
                                                                                निर्माण</option>
                                                                            <option <?php echo ($row_other_land['other_land_land_status_' . $i] == "विवादित है") ? 'selected' : ''; ?>>
                                                                                विवादित है</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-sm-3 form-group"
                                                                        id="construct_div_<?php echo $i; ?>"
                                                                        style="<?php echo ($row_other_land['other_land_land_status_' . $i] == "निर्माण" ? '' : 'display:none;'); ?>">
                                                                        <label>8. निर्माण के प्रकार</label>
                                                                        <select
                                                                            name="other_land_construction_<?php echo $i; ?>"
                                                                            id="other_land_construction_<?php echo $i; ?>"
                                                                            class="form-control">
                                                                            <option value="">-- चयन --</option>
                                                                            <option <?php echo ($row_other_land['other_land_construction_' . $i] == "ऑफिस स्पेस") ? 'selected' : ''; ?>>
                                                                                ऑफिस स्पेस</option>
                                                                            <option <?php echo ($row_other_land['other_land_construction_' . $i] == "किराये पर") ? 'selected' : ''; ?>>
                                                                                किराये पर</option>
                                                                            <option <?php echo ($row_other_land['other_land_construction_' . $i] == "जर्जर निर्माण") ? 'selected' : ''; ?>>जर्जर निर्माण</option>
                                                                            <option <?php echo ($row_other_land['other_land_construction_' . $i] == "अन्य") ? 'selected' : ''; ?>>अन्य
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-sm-12 form-group">
                                                                        <label>पता</label>
                                                                        <textarea
                                                                            name="other_land_address_<?php echo $i; ?>"
                                                                            id="other_land_address_<?php echo $i; ?>"
                                                                            rows="2"
                                                                            class="form-control"><?php echo $row_other_land['other_land_address_' . $i]; ?></textarea>
                                                                    </div>
                                                                    <div class="col-sm-6 form-group">
                                                                        <label>लोकेशन मोड</label>
                                                                        <select
                                                                            name="other_land_location_mode_<?php echo $i; ?>"
                                                                            id="other_land_location_mode_<?php echo $i; ?>"
                                                                            class="form-control location_mode_select"
                                                                            data-row="<?php echo $i; ?>">
                                                                            <option value="">-- चयन --</option>
                                                                            <option <?php echo ($row_other_land['other_land_location_mode_' . $i] == "Manual") ? 'selected' : ''; ?>>
                                                                                Manual</option>
                                                                            <option <?php echo ($row_other_land['other_land_location_mode_' . $i] == "GPS") ? 'selected' : ''; ?>>
                                                                                GPS</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div id="latlon_container_<?php echo $i; ?>"
                                                                    style="<?php echo ($row_other_land['other_land_location_mode_' . $i] == "" ? 'display:none;' : ''); ?>">

                                                                    <div class="row">
                                                                        <div class="col-sm-6 form-group">
                                                                            <label>Latitude</label>
                                                                            <input type="text"
                                                                                name="other_land_latitude_<?php echo $i; ?>"
                                                                                id="other_land_latitude_<?php echo $i; ?>"
                                                                                value="<?php echo $row_other_land['other_land_latitude_' . $i]; ?>"
                                                                                class="form-control" <?php echo ($row_other_land['other_land_location_mode_' . $i] == "GPS") ? 'readonly' : ''; ?>>
                                                                        </div>

                                                                        <div class="col-sm-6 form-group">
                                                                            <label>Longitude</label>
                                                                            <input type="text"
                                                                                name="other_land_longitude_<?php echo $i; ?>"
                                                                                id="other_land_longitude_<?php echo $i; ?>"
                                                                                value="<?php echo $row_other_land['other_land_longitude_' . $i]; ?>"
                                                                                class="form-control" <?php echo ($row_other_land['other_land_location_mode_' . $i] == "GPS") ? 'readonly' : ''; ?>>
                                                                        </div>
                                                                    </div>

                                                                    <button type="button"
                                                                        id="other_land_gps_btn_<?php echo $i; ?>"
                                                                        class="btn btn-sm btn-success"
                                                                        onclick="other_land_fetchGPS('<?php echo $i; ?>')"
                                                                        style="<?php echo ($row_other_land['other_land_location_mode_' . $i] == "GPS") ? 'margin-top:5px;' : ''; ?>; 
                                                                        <?php echo ($row_other_land['other_land_location_mode_' . $i] == "GPS") ? '' : 'display:none;'; ?>">
                                                                        लोकेशन रिफ्रेश करें
                                                                    </button>
                                                                </div>

                                                            </div>

                                                            <div class="col-sm-4">
                                                                <label>Location Map</label>
                                                                <div id="other_land_map_'+id+'"
                                                                    style="width:100%; height:280px; border:1px solid #aaa;background:#f8f8f8;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php if ($i == $row_other_land['count']) { ?>
                                                            <div class="col-sm-12 form-group" id="other_land_add_row_btn_area">
                                                                <button type="button" class="btn btn-info"
                                                                    onClick="other_land_add_row()">नई पंक्ति जोड़े
                                                                    [+]</button>
                                                                <input type="hidden" name="other_land_count" id="other_land_count"
                                                                    value="<?php echo $row_other_land['count']; ?>">
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <!---------------7th Start---------------------------------------------------------------->
                            <div class="step">
                                <h4><img src="images/logo/8.png" class="img-fluid stat-icon"
                                        style="height:50px; width:50px;"> 8. सुविधाएं </h4>
                                <h5>(I) सुविधाएं</h5>
                                <!-- <div class="col-sm-12"> -->
                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label>सभागार</label>
                                        <select name="sec_2_stock_insurance" class="form-control"
                                            onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                            <option value="">--select-- </option>
                                            <option value="yes">हाँ</option>
                                            <option value="no">नहीं</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>जन औषधि केंद्र</label>
                                        <select name="sec_2_stock_insurance" class="form-control"
                                            onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                            <option value="">--select-- </option>
                                            <option value="yes">हाँ</option>
                                            <option value="no">नहीं</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>जन सुविधा केंद्र</label>
                                        <select name="sec_2_stock_insurance" class="form-control"
                                            onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                            <option value="">--select-- </option>
                                            <option value="yes">हाँ</option>
                                            <option value="no">नहीं</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>प्रिंटिंग प्रेस</label>
                                        <select name="sec_2_stock_insurance" class="form-control"
                                            onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                            <option value="">--select-- </option>
                                            <option value="yes">हाँ</option>
                                            <option value="no">नहीं</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 form-group">
                                    <label>दिव्यांग जन कैंपस</label>
                                    <select name="sec_2_stock_insurance" class="form-control"
                                        onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                        <option value="">--select-- </option>
                                        <option value="yes">हाँ</option>
                                        <option value="no">नहीं</option>
                                    </select>
                                </div>
                                <!-- <h5>(II)विद्युत कनेक्शन</h5> -->
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
                                                    <select name="sec_8_bill_paid_yes_no" id="sec_8_bill_paid_yes_no"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"
                                                        onchange="hide_show(this.value, '#sec_7_bill_status', 'no'); hide_show(this.value, '#sec_8_bill_paid2', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                        <option value="">--select-- </option>
                                                        <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_bill_paid_yes_no'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>

                                                        <option style="background:#f00" value="no" <?php echo $row_8['sec_8_bill_paid_yes_no'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>

                                                    </select>
                                                </div>

                                                <div class="col-sm-3 form-group" id="electricity_available_not_working"
                                                    style="display: none">
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
                                                    <select class="form-control" value="" id="sec_8_solar_connection"
                                                        name="sec_8_solar_connection" tabindex="<?php echo $tab++; ?>"
                                                        onChange="hide_show(this.value, '#sec_8_solar_work', 'yes');hide_show(this.value, '#sec_8_solar_remark', 'no');hide_show(this.value, '#sec_8_solar_rooftop', 'no');handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                        <option value="">--Select--</option>
                                                        <option style="background:#0f0" value="yes" <?php echo $row_8['sec_8_solar_connection'] == 'yes' ? 'selected="selected"' : ''; ?>>हाँ</option>

                                                        <option style="background:#f00" value="no" <?php echo $row_8['sec_8_solar_connection'] == 'no' ? 'selected="selected"' : ''; ?>>नहीं</option>

                                                    </select>
                                                </div>
                                                <div class="col-sm-3 form-group" id="sec_8_solar_work"
                                                    style="display:none">
                                                    <label>यदि है तो चालू है या नहीं ?</label>
                                                    <select name="sec_8_solar_work_status" id="sec_8_solar_work_status"
                                                        class="form-control" tabindex="<?php echo $tab++; ?>"
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
                                                        id="sec_8_internet_connection" tabindex="<?php echo $tab++; ?>"
                                                        class="form-control"
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
                                                                tabindex="<?php echo $tab++; ?>" class="form-control">
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


                                                <div class="col-sm-4 form-group" id="net_con_not" style="display:none">
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
                                                        } ?>>
                                                            BSNL</option>
                                                        <option value="jio" <?php if (in_array('jio', $internet_provider)) {
                                                            echo ' selected="selected"';
                                                        } ?>>JIO
                                                        </option>
                                                        <option value="vodafone" <?php if (in_array('vodafone', $internet_provider)) {
                                                            echo ' selected="selected"';
                                                        } ?>>
                                                            Vodafone
                                                        </option>
                                                        <option value="airtel" <?php if (in_array('airtel', $internet_provider)) {
                                                            echo ' selected="selected"';
                                                        } ?>>
                                                            Airtel
                                                        </option>
                                                        <option value="sdwan" <?php if (in_array('sdwan', $internet_provider)) {
                                                            echo ' selected="selected"';
                                                        } ?>>
                                                            SDWAN
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
                                                        <option value="yes" <?php echo ($row_8['sec_8_narrow_tubes'] ?? '') == 'yes' ? 'selected' : ''; ?> style="background:#0f0">
                                                            हाँ </option>
                                                        <option value="no" <?php echo ($row_8['sec_8_narrow_tubes'] ?? '') == 'no' ? 'selected' : ''; ?>>
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
                        <button type="button" name="verify_otp_btn" id="verify_otp_btn" tabindex="<?php echo $tab++; ?>"
                            class="btn btn-info" onClick="verify_otp($('#survey_id').val());">आगे प्रेषित करे
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
    function sec_3_add_rows() {
        var id = parseInt($("#sec_3_row_count").val(), 10);
        if (!id) {
            id = 0;
        }

        for (var i = 1; i <= id; i++) {
            if ($("#sec_3_cpmt_" + i).val() === '' || $("#sec_3_post_" + i).val() === '') {
                alert("पंक्ति संख्या " + i + " अधूरी है (नाम / मूलपद खाली है)");
                $("#sec_3_cpmt_" + i).focus();
                return;
            }
        }

        id = id + 1;
        $("#sec_3_add_rows_wrapper").remove();

        var txt = '';
        txt += '<div class="row sec-3-row" id="sec_3_row_' + id + '">';
        txt += '  <div class="col-sm-12">';

        // नाम + पता
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4">';
        txt += '        <label>नाम :- सहकारी प्रबंध प्रशिक्षण केंद्र</label>';
        txt += '        <input name="sec_3_cpmt_' + id + '" id="sec_3_cpmt_' + id + '" class="form-control">';
        txt += '      </div>';
        txt += '      <div class="col-sm-4">';
        txt += '        <label>पता</label>';
        txt += '        <input name="sec_3_address_' + id + '" id="sec_3_address_' + id + '" class="form-control">';
        txt += '      </div>';
        txt += '    </div>';

        // प्रधानाचार्य नाम + मूलपद
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4">';
        txt += '        <label>पदेन प्रधानाचार्य नाम</label>';
        txt += '        <input name="sec_3_principal_name_' + id + '" id="sec_3_principal_name_' + id + '" class="form-control">';
        txt += '      </div>';
        txt += '      <div class="col-sm-4">';
        txt += '        <label>मूलपद</label>';
        txt += '        <input name="sec_3_post_' + id + '" id="sec_3_post_' + id + '" class="form-control">';
        txt += '      </div>';
        txt += '    </div>';

        // प्रधानाचार्य आवास
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4">';
        txt += '        <label>प्रधानाचार्य आवास</label>';
        txt += '        <select name="sec_3_principal_house_' + id + '" id="sec_3_principal_house_' + id + '" class="form-control" onchange="hide_show(this.value, \'#sec_3_principal_house_no_box_' + id + '\', \'yes\');">';
        txt += '          <option value="">--select--</option>';
        txt += '          <option value="yes">हाँ</option>';
        txt += '          <option value="no">नहीं</option>';
        txt += '        </select>';
        txt += '      </div>';
        txt += '      <div class="col-sm-4" id="sec_3_principal_house_no_box_' + id + '" style="display:none">';
        txt += '        <label>संख्या</label>';
        txt += '        <input name="sec_3_principal_house_no_' + id + '" id="sec_3_principal_house_no_' + id + '" class="form-control">';
        txt += '      </div>';
        txt += '    </div>';

        // प्रधानाचार्य कार्यालय
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4">';
        txt += '        <label>प्रधानाचार्य कार्यालय</label>';
        txt += '        <select name="sec_3_principal_office_' + id + '" id="sec_3_principal_office_' + id + '" class="form-control" onchange="hide_show(this.value, \'#sec_3_principal_office_no_box_' + id + '\', \'yes\');">';
        txt += '          <option value="">--select--</option>';
        txt += '          <option value="yes">हाँ</option>';
        txt += '          <option value="no">नहीं</option>';
        txt += '        </select>';
        txt += '      </div>';
        txt += '      <div class="col-sm-4" id="sec_3_principal_office_no_box_' + id + '" style="display:none">';
        txt += '        <label>संख्या</label>';
        txt += '        <input name="sec_3_principal_office_no_' + id + '" id="sec_3_principal_office_no_' + id + '" class="form-control">';
        txt += '      </div>';
        txt += '    </div>';

        // class, hostel, library, computer lab, teacher, remarks ... (same structure जैसा ऊपर PHP में है)
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>क्लासरूम संख्या</label><input name="sec_3_class_no_' + id + '" id="sec_3_class_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>क्षमता</label><input name="sec_3_class_capacity_' + id + '" id="sec_3_class_capacity_' + id + '" class="form-control"></div>';
        txt += '    </div>';

        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>हॉस्टल संख्या</label><input name="sec_3_hostel_no_' + id + '" id="sec_3_hostel_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>क्षमता</label><input name="sec_3_hostel_capacity_' + id + '" id="sec_3_hostel_capacity_' + id + '" class="form-control"></div>';
        txt += '    </div>';

        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>पुस्तकालय संख्या</label><input name="sec_3_library_no_' + id + '" id="sec_3_library_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>क्षमता</label><input name="sec_3_library_capacity_' + id + '" id="sec_3_library_capacity_' + id + '" class="form-control"></div>';
        txt += '    </div>';

        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>कंप्युटर लैब संख्या</label><input name="sec_3_computer_lab_no_' + id + '" id="sec_3_computer_lab_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>क्षमता</label><input name="sec_3_computer_lab_capacity_' + id + '" id="sec_3_computer_lab_capacity_' + id + '" class="form-control"></div>';
        txt += '    </div>';

        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>अध्यापक / अतिथि प्रवक्ता संख्या</label><input name="sec_3_teacher_no_' + id + '" id="sec_3_teacher_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>कर्मचारी विवरण</label><textarea name="sec_3_employee_remarks_' + id + '" id="sec_3_employee_remarks_' + id + '" class="form-control"></textarea></div>';
        txt += '    </div>';

        // training sessions
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>प्रशिक्षण सत्रों की संख्या</label><input name="sec_3_training_sessions_no_' + id + '" id="sec_3_training_sessions_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>प्रशिक्षण विषय के नाम</label><input name="sec_3_training_subject_name_' + id + '" id="sec_3_training_subject_name_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-3"><label>प्रशिक्षण सत्र अवधि</label><input type="date" name="sec_3_training_sessions_duration_' + id + '" id="sec_3_training_sessions_duration_' + id + '" class="form-control"></div>';
        txt += '    </div>';

        // trainees
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>विभागीय प्रशिक्षार्थियों की संख्या</label><input name="sec_3_departmental_trainees_no_' + id + '" id="sec_3_departmental_trainees_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>गैर-विभागीय प्रशिक्षार्थियों की संख्या</label><input name="sec_3_non_departmental_trainees_no_' + id + '" id="sec_3_non_departmental_trainees_no_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>प्रशिक्षार्थियों की संख्या</label><input name="sec_3_trainees_no_' + id + '" id="sec_3_trainees_no_' + id + '" class="form-control" readonly></div>';
        txt += '    </div>';

        // fees
        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label><input name="sec_3_departmental_trainees_fee_' + id + '" id="sec_3_departmental_trainees_fee_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>गैर-विभागीय प्रशिक्षार्थी प्रशिक्षण शुल्क</label><input name="sec_3_non_departmental_trainees_fee_' + id + '" id="sec_3_non_departmental_trainees_fee_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>प्रशिक्षार्थी प्रशिक्षण शुल्क</label><input name="sec_3_trainees_fee_' + id + '" id="sec_3_trainees_fee_' + id + '" class="form-control" readonly></div>';
        txt += '    </div>';

        txt += '    <div class="row">';
        txt += '      <div class="col-sm-4"><label>विभागीय हॉस्टल शुल्क</label><input name="sec_3_departmental_hostel_fee_' + id + '" id="sec_3_departmental_hostel_fee_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>गैर-विभागीय हॉस्टल शुल्क</label><input name="sec_3_non_departmental_hostel_fee_' + id + '" id="sec_3_non_departmental_hostel_fee_' + id + '" class="form-control"></div>';
        txt += '      <div class="col-sm-4"><label>हॉस्टल शुल्क</label><input name="sec_3_hostel_fee_' + id + '" id="sec_3_hostel_fee_' + id + '" class="form-control" readonly></div>';
        txt += '    </div>';

        // निर्माण वर्ष, संचालन वर्ष, केंद्र, स्टाफ टाइप, लाभ, स्थिति
        txt += '    <div class="row">';

        txt += '      <div class="col-sm-3">';
        txt += '        <label>निर्माण वर्ष</label>';
        txt += '        <select name="sec_3_build_year_' + id + '" id="sec_3_build_year_' + id + '" class="form-control">';
        txt += '          <option value="">--Select--</option>';
        txt += '          <option value="1999">2000 से पूर्व</option>';
        for (var y = 2000; y <= 2024; y++) {
            txt += '          <option value="' + y + '">' + y + '</option>';
        }
        txt += '        </select>';
        txt += '      </div>';

        txt += '      <div class="col-sm-3">';
        txt += '        <label>संचालन वर्ष</label>';
        txt += '        <select name="sec_3_operation_year_' + id + '" id="sec_3_operation_year_' + id + '" class="form-control">';
        txt += '          <option value="">--Select--</option>';
        txt += '          <option value="1999">2000 से पूर्व</option>';
        for (var y2 = 2000; y2 <= 2024; y2++) {
            txt += '          <option value="' + y2 + '">' + y2 + '</option>';
        }
        txt += '        </select>';
        txt += '      </div>';

        txt += '      <div class="col-sm-3">';
        txt += '        <label>सहकारी प्रबंध प्रशिक्षण केंद्र</label>';
        txt += '        <select name="sec_3_training_center_' + id + '" id="sec_3_training_center_' + id + '" class="form-control">';
        txt += '          <option value="">--Select--</option>';
        txt += '          <option value="meerut">मेरठ</option>';
        txt += '          <option value="varanasi">वाराणसी</option>';
        txt += '          <option value="mahoba">महोबा</option>';
        txt += '          <option value="hewra">हेवरा (ईटवा)</option>';
        txt += '          <option value="ayodhya">अयोध्या (फैजाबाद)</option>';
        txt += '          <option value="bilari">बिलारी (मोरादाबाद)</option>';
        txt += '        </select>';
        txt += '      </div>';

        txt += '      <div class="col-sm-3">';
        txt += '        <label>कार्मिक की संख्या</label>';
        txt += '        <select name="sec_3_staff_type_' + id + '" id="sec_3_staff_type_' + id + '" class="form-control">';
        txt += '          <option value="">--select--</option>';
        txt += '          <option value="union">उ० प्र० कोआपरेटिव यूनियन</option>';
        txt += '          <option value="authority">सहकारी संघ प्राधिकारी</option>';
        txt += '        </select>';
        txt += '      </div>';

        txt += '      <div class="col-sm-3">';
        txt += '        <label>प्रशिक्षण कोर्स लाभ</label>';
        txt += '        <textarea name="sec_3_training_course_benefits_' + id + '" id="sec_3_training_course_benefits_' + id + '" class="form-control"></textarea>';
        txt += '      </div>';

        txt += '      <div class="col-sm-3">';
        txt += '        <label>भवन/हॉस्टल स्तिथि</label>';
        txt += '        <textarea name="sec_3_building_hostel_status_' + id + '" id="sec_3_building_hostel_status_' + id + '" class="form-control"></textarea>';
        txt += '      </div>';

        txt += '    </div>'; // row

        // add-row button (नई row पर)
        txt += '    <div class="col-sm-2 form-group my-auto" id="sec_3_add_rows_wrapper">';
        txt += '      <button type="button" class="btn btn-info" onclick="sec_3_add_rows()">नई पंक्ति जोड़े [+]</button>';
        txt += '      <input type="hidden" name="sec_3_row_count" id="sec_3_row_count" value="' + id + '">';
        txt += '    </div>';

        txt += '  </div>'; // col-sm-12
        txt += '</div>';   // row

        $("#sec_3_training_center").append(txt);
    }

    function sec_2_b_add_rows() {
        var id = parseInt($("#sec_2_b_id").val()) || 0;

        // Validate existing rows
        for (var i = 1; i <= id; i++) {
            if ($("#sec_2_b_name_" + i).val() == '') {
                alert("कृपया पंक्ति संख्या " + i + " का नाम भरें।");
                $("#sec_2_b_name_" + i).focus();
                return;
            }
        }

        id++;

        // Remove existing 'add row' button cell
        $(".sec_2_b_rows").remove();

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
                <td class="sec_2_b_rows">
                    <button type="button" class="btn btn-info" onclick="sec_2_b_add_rows();">नई पंक्ति जोड़े [+]</button>
                </td>
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
                    <option value="yes">पदेन</option>
                    <option value="no">निर्वाचित</option>
                    <option value="1">नामित</option>
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
                    ${[...Array(2025 - 1975 + 1)].map((_, idx) => {
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
    /* Row counter */
    let otherLandRowCounter = 0;

    /* Utility hide_show() as per your example signature:
       hide_show(this.value, '#electricity_not_available', 'no'); */
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

    function handleDropdownColorChange(selectElement, yesValue, yesColor, noValue, noColor) {
        if (selectElement.value === yesValue) {
            selectElement.style.backgroundColor = yesColor;
        } else if (selectElement.value === noValue) {
            selectElement.style.backgroundColor = noColor;
        } else {
            selectElement.style.backgroundColor = 'white'; // Default background color
        }
    }

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

<script type="text/javascript" src="js/multistepform_pcu.js?v=1">
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    < script src="js/light-bootstrap-dashboard.js?v=1.4.0">
</script>


<?php
page_footer_start();
?>