<?php
include ("scripts/settings.php");
$msg = '';
$tab = 1;
$response= 1;
// print_r($_SESSION);
// error_reporting(0);
 if ($_SESSION['user_type'] == 'bm') echo '@<style>.step { display: none; }</style>'; 

if(isset($_GET['exdid'])){
	
	/////////////////////invoice and1 , 1.1/////////////////////////	

	$sql = 'SELECT survey_invoice.sno as sno, survey_invoice.society_id as society_id, test2.col4 as society_name, committee_status,committee_date,  master_block.sno as block_id,liquidation_date,dispute_details,kcc_members,total_farmers_member,total_non_farmers_member,new_members,contribution_received_capital,inactive_to_active_members,total_members, master_society_type.type_name as type_name, division_name, district_name, tehseel_name, block_name,  survey_invoice.latitude as latitude, survey_invoice.longitude as longitude, survey_invoice.mobile_number as mobile_number, liquidation, litigation,  concat("user_data/", col2, "/", col6, "/", photo_id) as photo_id, society_building_ownership, society_building_rent_amount, society_building_area, society_registration_no, society_registration_date, email_id, respondent_name, respondent_designation, respondent_aadhaar, active_members, inactive_members, others, col1, col2, col3, col5, col6 FROM `survey_invoice` left join test2 on test2.sno = society_id left join master_block on master_block.sno = col6 left join master_tehseel on master_tehseel.sno = col5 left join master_district on master_district.sno = col2 left join master_division on master_division.sno = col1 left join master_society_type on master_society_type.sno = col3  where society_id="' . $_GET['exdid'] . '"';
	// echo $sql;
	$result_invoice = execute_query($sql);
	if (mysqli_num_rows($result_invoice) >= 1) {
		// echo '@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@survey insert:1111';
		$row_invoice = mysqli_fetch_assoc($result_invoice);
		if ($row_invoice['society_registration_date'] == '') {
			$row_invoice['society_registration_date'] = date("Y-m-d");
		}
		$_SESSION['survey_id'] = $row_invoice['sno'];
		$row_invoice['latitude'] = $row_invoice['latitude'];
		$row_invoice['longitude'] = $row_invoice['longitude'];		
		$row_invoice['committee_status'] = $row_invoice['committee_status'];
		$row_invoice['committee_date'] = $row_invoice['committee_date'];
		$row_invoice['society_registration_no'] = $row_invoice['society_registration_no'];
		$row_invoice['sec_1_society_registration_date'] = $row_invoice['society_registration_date'];
		$row_invoice['email_id'] = $row_invoice['email_id'];
		$row_invoice['liquidation'] = $row_invoice['liquidation'];
		$row_invoice['liquidation_date'] =$row_invoice['liquidation_date'];
		$row_invoice['litigation'] = $row_invoice['litigation'];
		$row_invoice['dispute_details'] = $row_invoice['dispute_details'];
		
					
		$row_invoice['active_members'] =$row_invoice['active_members'];
		$row_invoice['inactive_members'] =$row_invoice['inactive_members'];
		$row_invoice['kcc_members'] =$row_invoice['kcc_members'];
		$row_invoice['total_farmers_member'] =$row_invoice['total_farmers_member'];
		$row_invoice['total_non_farmers_member'] =$row_invoice['total_non_farmers_member'];
					
		$row_invoice['new_members'] = $row_invoice['new_members'];
		$row_invoice['contribution_received_capital'] =$row_invoice['contribution_received_capital'];
		$row_invoice['inactive_to_active_members'] = $row_invoice['inactive_to_active_members'];
		$row_invoice['total_members'] = $row_invoice['total_members'];
		$row_invoice['mobile_number'] = $row_invoice['mobile_number'];
		
/////////////////2 and 2.1 and 2.2/////////////////suraj////////////////////////////	

		$sql = 'select * from survey_invoice_new_sec_2 where survey_id="' . $row_invoice['sno'] . '"';
		// echo $sql;
		$res_2 = execute_query($sql);
		if (mysqli_num_rows($res_2) != 0) {
			$row_2 = mysqli_fetch_assoc($res_2);
			
			$row_2['sec_2_stock_insurance'] = $row_2['stock_insurance_yes_no'];
		}else{
			$row_2['sec_2_stock_insurance']="";
		}
	

	$sql = 'select * from survey_trans_new_sec_2_stock where survey_id="'.$row_invoice['sno'].'"';
			// echo $sql;
		$result_sec_2 = execute_query($sql);
		$row_sec_2 = array();
		$j=1;
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
		}
		else{
			
			$row_sec_2['closing_stock_1_1'] = "";
			$row_sec_2['book_value_1_1'] ="";
			$row_sec_2['closing_stock_2_1'] = "";
			$row_sec_2['book_value_2_1'] = "";	
		}
	
	
		$sql = 'select * from survey_invoice_new_sec_2_1 where survey_id="'.$row_invoice['sno'].'"';
			// echo $sql;
		$result_sec_2_1 = execute_query($sql);
		$row_sec_2_1 = array();
		$a=1;
		if(mysqli_num_rows($result_sec_2_1)>0){
			while($row_section_2_1 = mysqli_fetch_assoc($result_sec_2_1)){
				$row_sec_2_1['scraped_item_name_'.$a] = $row_section_2_1['item_name'];
				$row_sec_2_1['scraped_item_description_'.$a] = $row_section_2_1['item_description'];
				$row_sec_2_1['book_value_'.$a] = $row_section_2_1['book_value'];
				$a++;
			}
		}else{
				$row_sec_2_1['scraped_item_name_1'] = "";
				$row_sec_2_1['scraped_item_description_1'] = "";
				$row_sec_2_1['book_value_1'] = "";
		}
	
		
		$sql = 'select * from survey_invoice_new_sec_2_2 where survey_id="'.$row_invoice['sno'].'"';
			// echo $sql;
		$result_sec_2_2 = execute_query($sql);
		$row_sec_2_2 = array();
		$b=1;
		if(mysqli_num_rows($result_sec_2_2)>0){
			while($row_section_2_2 = mysqli_fetch_assoc($result_sec_2_2)){
				$row_sec_2_2['item_name_'.$b] = $row_section_2_2['item_name'];
				$row_sec_2_2['item_description_'.$b] = $row_section_2_2['item_description'];
				$row_sec_2_2['scheme_name_'.$b] = $row_section_2_2['scheme_name'];
				$row_sec_2_2['date_'.$b] = $row_section_2_2['date'];
				$row_sec_2_2['purchase_value_'.$b] = $row_section_2_2['purchase_value'];
				$row_sec_2_2['quantity_'.$b] = $row_section_2_2['quantity'];
				$b++;
			}
		}
		
		
/////////SEction 6.1 manav sampdaa///////////// suraj/////////////////////////
	
	$sql = 'select * from survey_invoice_new_sec_6_1 where survey_id="'.$row_invoice['sno'].'"';
			// echo $sql;
		$result_sec_6_1 = execute_query($sql);
		$row_sec_6_1 = array();
		$c=1;
		if(mysqli_num_rows($result_sec_6_1)>0){
			while($row_section_6_1 = mysqli_fetch_assoc($result_sec_6_1)){
				
				$row_sec_6_1['sec_6_1_condition_'.$c] = $row_section_6_1['emp_condition'];
				$row_sec_6_1['sec_6_1_name_'.$c] = $row_section_6_1['emp_name'];
				$row_sec_6_1['sec_6_1_father_name_'.$c] = $row_section_6_1['emp_father_name'];
				$row_sec_6_1['sec_6_1_address_'.$c] = $row_section_6_1['emp_address'];
				$row_sec_6_1['sec_6_1_birth_date_'.$c] = $row_section_6_1['emp_birth_date'];
				$row_sec_6_1['sec_6_1_education_qualification_'.$c] = $row_section_6_1['emp_education_qualification'];
				
				$row_sec_6_1['sec_6_1_computer_qualification_'.$c] = $row_section_6_1['emp_computer_qualification'];
				$row_sec_6_1['sec_6_1_approval_level_'.$c] = $row_section_6_1['emp_approval_level'];
				
				$row_sec_6_1['sec_6_1_appointment_date_'.$c] = $row_section_6_1['emp_appointment_date'];
				$row_sec_6_1['sec_6_1_mgt_committee_resolution_number_date_'.$c] = $row_section_6_1['emp_mgt_committee_resolution_number_date'];
				$row_sec_6_1['sec_6_1_employee_type_'.$c] = $row_section_6_1['emp_type'];
				$row_sec_6_1['sec_6_1_source_emp_'.$c] = $row_section_6_1['emp_source'];
				
				$c++;
			}
		}
////////////////row 6.2 and 6.2.1//////// suraj///////	
	
	$sql = 'select * from survey_invoice_new_sec_6_2 where survey_id="' . $row_invoice['sno'] . '"';
		$res_62 = execute_query($sql);
		if (mysqli_num_rows($res_62) != 0) {
			$row_sec_6_2 = mysqli_fetch_assoc($res_62);
			$row_sec_6_2['sec_6_2_mgt_committee_is_elected'] = $row_sec_6_2['mgt_committee_is_elected'];
			$row_sec_6_2['sec_6_2_election_year'] = $row_sec_6_2['election_year'];
			$row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = $row_sec_6_2['mgt_committee_resolution_no'];
			
		} else {
			$row_sec_6_2['sec_6_2_mgt_committee_is_elected'] = '';
			$row_sec_6_2['sec_6_2_election_year'] = '';
			$row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = '';
		}
	
	
		$sql = 'select * from survey_invoice_new_sec_6_2_1 where survey_id="'.$row_invoice['sno'].'"';
			// echo $sql;
		$result_sec_6_2_1 = execute_query($sql);
		$row_6_2 = array();
		$d=1;
		if(mysqli_num_rows($result_sec_6_2_1)>0){
			$row_6_2['count']= mysqli_num_rows($result_sec_6_2_1);
			while($row_section_6_2_1 = mysqli_fetch_assoc($result_sec_6_2_1)){
				
				$row_6_2['sec_6_2_designation_'.$d] = $row_section_6_2_1['designation'];
				$row_6_2['sec_6_2_name_'.$d] = $row_section_6_2_1['full_name'];
				$row_6_2['sec_6_2_father_name_'.$d] = $row_section_6_2_1['father_name'];
				$row_6_2['sec_6_2__mob_no_'.$d] = $row_section_6_2_1['mobile_no'];
				
				
				
				$d++;
			}
		}else{
			$row_6_2['count']=1;
			$row_6_2['sec_6_2_designation_'.$d] = "";
			$row_6_2['sec_6_2_name_'.$d] = '';
			$row_6_2['sec_6_2_father_name_'.$d] = '';
			$row_6_2['sec_6_2__mob_no_'.$d] = '';
		}
		
/////////////////////////section 8////////suraj/////////////////////////////////

		$sql = 'select * from survey_invoice_new_sec_8 where survey_id="' . $row_invoice['sno'] . '"';
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
			$row_8['internet_not_bill_paid_month'] = $row_8['internet_not_bill_paid_month'];
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






/////////////////////////8 somill/////////////////////////////////////////


$sql = 'select * from  survey_invoice_sec_new_8 where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_new_8 = execute_query($sql);
		if(mysqli_num_rows($res_new_8) != 0){
			$row_new_8 = mysqli_fetch_assoc($res_new_8);
			$row_new_8['sec_new_8_govt_program'] = $row_new_8['govt_program'] ?? '';
			$row_new_8['sec_new_8_other_description'] = $row_new_8['other_description'] ?? '';
			$row_new_8['sec_new_8_hw_secure'] = $row_new_8['hw_secure'] ?? '';
			$row_new_8['sec_new_8_go_live'] = $row_new_8['go_live'] ?? '';
			$row_new_8['sec_new_8_balance_sheet'] = $row_new_8['balance_sheet'] ?? '';
			$row_new_8['sec_new_8_day_end'] = $row_new_8['day_end'] ?? '';
			$row_new_8['sec_new_8_fin_year'] = $row_new_8['fin_year'] ?? '';
			$row_new_8['sec_new_8_inspection_officer'] = $row_new_8['inspection_officer'] ?? '';
			$row_new_8['sec_new_8_inspection_date'] = $row_new_8['inspection_date'] ?? '';
			$row_new_8['sec_new_8_last_inspection_date'] = $row_new_8['last_inspection_date'] ?? '';
			$row_new_8['sec_new_8_remarks'] = $row_new_8['remarks'] ?? '';
			$row_new_8['sec_new_8_compliance'] = $row_new_8['compliance'] ?? '';

		} else {
			
			$row_new_8['sec_new_8_govt_program'] = '';
			$row_new_8['sec_new_8_other_description'] = '';
			$row_new_8['sec_new_8_hw_secure'] = '';
			$row_new_8['sec_new_8_go_live'] = '';
			$row_new_8['sec_new_8_balance_sheet'] = '';
			$row_new_8['sec_new_8_day_end'] = '';
			$row_new_8['sec_new_8_fin_year'] = '';
			$row_new_8['sec_new_8_inspection_officer'] = '';
			$row_new_8['sec_new_8_inspection_date'] = '';
			$row_new_8['sec_new_8_last_inspection_date'] = '';
			$row_new_8['sec_new_8_remarks'] = '';
			$row_new_8['sec_new_8_compliance'] = '';
		}


		$sql = 'select * from survey_invoice_sec_2_1_2 where survey_id="' . $row_invoice['sno'] . '" and other_description="msc"';
		$res_sec_1_2_msc = execute_query($sql);
		if (mysqli_num_rows($res_sec_1_2_msc) != 0) {
			
			$row_sec_1_2_msc['sec_1_1_2_msc'] = "yes";
		
		} else {
			$row_sec_1_2_msc['sec_1_1_2_msc'] = "no";
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
		
		$sql = 'select * from survey_invoice_sec_1_grampanchayat where survey_id="' . $row_invoice['sno'] . '"';
		$res_1_grampanchayat = execute_query($sql);
		$n = 1;
		
		$grampanchayat = array();
		if (mysqli_num_rows($res_1_grampanchayat) != 0) {
			
			while ($row_temp_grampanchayat = mysqli_fetch_assoc($res_1_grampanchayat)) {
				
				$grampanchayat[] = $row_temp_grampanchayat['gram_panchayt_id'];
					
			}
			$_POST['gram_panchayat'] = $grampanchayat;
			
		}
		
		
		
		$sql = 'select * from survey_invoice_sec_2_1 where survey_id="' . $row_invoice['sno'] . '"';
		$res_2_1 = execute_query($sql);
		if (mysqli_num_rows($res_2_1) != 0) {
			$row_2_1 = mysqli_fetch_assoc($res_2_1);
			$row_2_1['sec_6_access_road'] = $row_2_1['approach_road'];
			$row_2_1['sec_6_paved_road_road'] = $row_2_1['approach_road'];
			$row_2_1['sec_6_2_truck_not_reach'] = $row_2_1['distance_from_approach_road'];
			$row_2_1['sec_7_electrical_connection'] = $row_2_1['electric_connection'];
			$row_2_1['sec_7_electrical_connection_working'] = $row_2_1['electric_connection_working'];
			$row_2_1['sec_7_if_yes'] = $row_2_1['electric_connection_proposal'];
			$row_2_1['sec_8_internet_connection'] = $row_2_1['internet_connectivity'];
		
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
			
			
			

		}
		
		
	   $sql = 'select * from survey_invoice_sec_3_5_1 where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_5_1 = execute_query($sql);
		if(mysqli_num_rows($res_3_5_1) != 0){
			$row_3_5_1 = mysqli_fetch_assoc($res_3_5_1);
			$row_3_5_1['sec_6_godown_insurance'] = $row_3_5_1['godown_insurance'];
			$row_3_5_1['sec_6_tree'] = $row_3_5_1['tree'];
			$row_3_5_1['sec_6_illegal_possession'] = $row_3_5_1['illegal_possession'];
			$row_3_5_1['sec_6_if_yes_6'] = $row_3_5_1['if_yes_6'];
			$row_3_5_1['sec_6_other_remarks'] = $row_3_5_1['other_remarks'];
		} else {
			$row_3_5_1['sec_6_godown_insurance'] = '';
			$row_3_5_1['sec_6_tree'] = '';
			$row_3_5_1['sec_6_illegal_possession'] = '';
			$row_3_5_1['sec_6_if_yes_6'] = '';
			$row_3_5_1['sec_6_other_remarks'] = '';

		}
		

/////////////////////////END END END END END END END/////////////////////////////////////////






		$sql = 'select * from survey_invoice_sec_2_2 where survey_id="' . $row_invoice['sno'] . '"';
		$res_2_2 = execute_query($sql);
		if (mysqli_num_rows($res_2_2) != 0) {
			$row_2_2 = mysqli_fetch_assoc($res_2_2);
		} else {
			$row_2_2['secretary'] = '';
			$row_2_2['secretary_status'] = '';
			$row_2_2['secretary_cader'] = '';
			$row_2_2['secretary_name'] = '';
			$row_2_2['secretary_mobile'] = '';
			$row_2_2['secretary_aadhaar'] = '';
			$row_2_2['accountant'] = '';
			$row_2_2['assistant_accountant'] = '';
			$row_2_2['seller'] = '';
			$row_2_2['support_staff'] = '';
			$row_2_2['guard'] = '';
			$row_2_2['computer_operator'] = '';
			$row_2_2['govt_program'] = '';
			$row_2_2['other_description'] = '';

		}

		$sql = 'select *, concat("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", photo_id) as new_photo_id from survey_invoice_sec_3_1 where survey_id="' . $row_invoice['sno'] . '"';
		$res_3_1 = execute_query($sql);
		if (mysqli_num_rows($res_3_1) != 0) {
			$row_3_1 = mysqli_fetch_assoc($res_3_1);
			$row_3_1['sec_3_ownership'] = $row_invoice['society_building_ownership'];
			$row_3_1['sec_3_d_boundry'] = $row_3_1['boundry_wall'];
			$row_3_1['sec_3_d_main_gate'] = $row_3_1['main_gate'];
		} else {
			$row_3_1['number_of_sides'] = '';
			$row_3_1['total_area'] = '';
			$row_3_1['govt_records'] = '';
			$row_3_1['gata_no'] = '';
			$row_3_1['new_photo_id'] = '';
			$row_3_1['east_side'] = '';
			$row_3_1['west_side'] = '';
			$row_3_1['south_side'] = '';
			$row_3_1['north_side'] = '';
			$row_3_1['on_road_land'] = '';
			$row_3_1['front_side'] = '';
			$row_3_1['remarks'] = '';
			$row_3_1['sec_3_ownership'] = '';
			$row_3_1['sec_3_d_boundry'] = '';
			$row_3_1['sec_3_d_main_gate'] = '';
		}

		$sql = 'select * from survey_invoice_sec_3_3 where survey_id="' . $row_invoice['sno'] . '"';
		$res_3_3 = execute_query($sql);
		$i = 1;
		$a = 1;
		if (mysqli_num_rows($res_3_3) != 0) {
			$row_3_3['count'] = mysqli_num_rows($res_3_3);
			while ($row_temp = mysqli_fetch_assoc($res_3_3)) {
				$row_3_3['sec_3_b_type_of_construction_' . $i] = $row_temp['type_of_construction'];
				$row_3_3['sec_3_b_type_of_fund_' . $i] = $row_temp['type_of_fund'];
				$row_3_3['sec_3_b_length_' . $i] = $row_temp['length'];
				$row_3_3['sec_3_b_width_' . $i] = $row_temp['width'];
				$row_3_3['sec_3_b_comment_' . $i] = $row_temp['remarks'];
				$i++;
			}
			$row_3_3['count'] = $i - 1;
		} else {
			$row_3_3['count'] = 1;
			$row_3_3['sec_3_b_type_of_construction_' . $i] = '';
			$row_3_3['sec_3_b_type_of_fund_' . $i] = '';
			$row_3_3['sec_3_b_length_' . $i] = '';
			$row_3_3['sec_3_b_width_' . $i] = '';
			$row_3_3['sec_3_b_comment_' . $i] = '';
		}

		$sql = 'select * from survey_invoice_sec_3_1_sides where survey_id="' . $row_invoice['sno'] . '"';
		$res_3_3_side = execute_query($sql);
		if (mysqli_num_rows($res_3_3_side) != 0) {
			$sides_data = array();
			$i = 1;
			while ($row_3_3_side = mysqli_fetch_assoc($res_3_3_side)) {
				$sides_data['sec_3_a_' . $i] = $row_3_3_side['length'];
				$i++;
			}
		}

		$sql = 'select * from survey_invoice_sec_3_4 where survey_id="' . $row_invoice['sno'] . '"';
		//echo $sql;
		$res_3_4 = execute_query($sql);
		if (mysqli_num_rows($res_3_4) != 0) {
			$i = 1;
			while ($row_3_4_temp = mysqli_fetch_assoc($res_3_4)) {
				$row_3_4['sec_3_b_godown_length_' . $i] = $row_3_4_temp['length'];
				$row_3_4['sec_3_b_godown_width_' . $i] = $row_3_4_temp['width'];
				$row_3_4['sec_3_b_storage_capacity_' . $i] = $row_3_4_temp['storage_capacity'];
				$row_3_4['sec_3_b_godown_status_' . $i] = $row_3_4_temp['construction_status'];
				$row_3_4['sec_3_b_godown_type_of_fund_' . $i] = $row_3_4_temp['type_of_fund'];
				$row_3_4['sec_3_b_comment_' . $i] = $row_3_4_temp['remarks'];
				$i++;
			}
			$row_3_4['count'] = $i - 1;
		} else {
			$i = 1;
			$row_3_4['count'] = 1;
			$row_3_4['sec_3_b_godown_length_' . $i] = '';
			$row_3_4['sec_3_b_godown_width_' . $i] = '';
			$row_3_4['sec_3_b_storage_capacity_' . $i] = '';
			$row_3_4['sec_3_b_godown_status_' . $i] = '';
			$row_3_4['sec_3_b_godown_type_of_fund_' . $i] = '';
			$row_3_4['sec_3_b_comment_' . $i] = '';

		}
		
		$sql = 'select * from survey_invoice_sec_7_6 where survey_id="' . $row_invoice['sno'] . '"';
		//echo $sql;
		$res_7_6 = execute_query($sql);
		if (mysqli_num_rows($res_7_6) != 0) {
			$i = 1;
			while ($row_7_6_temp = mysqli_fetch_assoc($res_7_6)) {
				$row_7_6['sec_3_b_storage_scheme_length_' . $i] = $row_7_6_temp['length'];
				$row_7_6['sec_3_b_storage_scheme_width_' . $i] = $row_7_6_temp['width'];
				$row_7_6['sec_3_b_storage_scheme_capacity_' . $i] = $row_7_6_temp['storage_capacity'];
				$row_7_6['sec_3_b_storage_scheme_status_' . $i] = $row_7_6_temp['construction_status'];
				$row_7_6['sec_3_b_storage_scheme_comment_' . $i] = $row_7_6_temp['remarks'];
				$i++;
			}
			$row_7_6['count'] = $i - 1;
		} else {
			$i = 1;
			$row_7_6['count'] = 1;
			$row_7_6['sec_3_b_storage_scheme_length_' . $i] = '';
			$row_7_6['sec_3_b_storage_scheme_width_' . $i] = '';
			$row_7_6['sec_3_b_storage_scheme_capacity_' . $i] = '';
			$row_7_6['sec_3_b_storage_scheme_status_' . $i] = '';
			$row_7_6['sec_3_b_storage_scheme_comment_' . $i] = '';

		}
		
		
		$sql = 'select * from survey_invoice_sec_3_5 where survey_id="' . $row_invoice['sno'] . '"';
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
		}

		$sql = 'select * from survey_invoice_sec_3_6 where survey_id="' . $row_invoice['sno'] . '"';
		$res_3_6_side = execute_query($sql);
		if (mysqli_num_rows($res_3_6_side) != 0) {
			$data_3_6 = array();
			$i = 1;
			while ($row_3_6_side = mysqli_fetch_assoc($res_3_6_side)) {
				$row_3_6['sec_3_6_type_of_construction'] = $row_3_6_side['type_of_construction'];
				$row_3_6['sec_3_6_rent'] = $row_3_6_side['rent_amount'];
				$row_3_6['sec_3_6_area'] = $row_3_6_side['total_area'];
			}
		} else {
			
			$row_3_6['sec_3_6_type_of_construction'] = '';
			$row_3_6['sec_3_6_rent'] = '';
			$row_3_6['sec_3_6_area'] = '';
		}

		$sql = 'select * from survey_invoice_sec_4 where survey_id="' . $row_invoice['sno'] . '"';
		$res_4 = execute_query($sql);
		$custom_hiring_other = array();
		if (mysqli_num_rows($res_4) != 0) {
			$row_4 = mysqli_fetch_assoc($res_4);
			$row_4['sec_4_micro_atm'] = $row_4['micro_atm'];
			$row_4['sec_4_custom_hiring'] = $row_4['custom_hiring_center'];
			$row_4['sec_4_drone'] = $row_4['drone'];
			$row_4['sec_4_chhanna'] = $row_4['chalana'];
			$row_4['sec_4_power_duster'] = $row_4['power_duster'];
			$row_4['sec_4_tractor'] = $row_4['tractor'];
			$row_4['sec_4_chair'] = $row_4['office_chair'];
			$row_4['sec_4_table'] = $row_4['office_table'];
			$row_4['sec_4_almari'] = $row_4['office_almirah'];
			$row_4['sec_4_remarks'] = $row_4['remarks'];
		} else {
			$row_4['sec_4_micro_atm'] = '';
			$row_4['sec_4_custom_hiring'] = '';
			$row_4['sec_4_drone'] = '';
			$row_4['sec_4_chhanna'] = '';
			$row_4['sec_4_power_duster'] = '';
			$row_4['sec_4_tractor'] = '';
			$row_4['sec_4_chair'] = '';
			$row_4['sec_4_table'] = '';
			$row_4['sec_4_almari'] = '';
			$row_4['sec_4_remarks'] = '';

		}

		$sql = 'select * from survey_invoice_sec_5 where survey_id="' . $row_invoice['sno'] . '"';
		$res_5 = execute_query($sql);
		if (mysqli_num_rows($res_5) != 0) {
			$row_5 = mysqli_fetch_assoc($res_5);
			$row_5['sec_5_built_building'] = $row_5['building_status'];
			$row_5['sec_5_detailed_information'] = $row_5['building_status_remarks'];
			$row_5['sec_6_a_length'] = $row_5['floor_length'];
			$row_5['sec_6_a_width'] = $row_5['floor_width'];
			$row_5['sec_6_b_length'] = $row_5['wall_length'];
			$row_5['sec_6_b_width'] = $row_5['wall_width'];
			$row_5['sec_6_c_length'] = $row_5['paint_length'];
			$row_5['sec_6_c_width'] = $row_5['paint_width'];
			$row_5['sec_6_d_length'] = $row_5['roof_length'];
			$row_5['sec_6_d_width'] = $row_5['roof_width'];

			$row_5['sec_6_e_floor'] = $row_5['washroom_floor'];
			$row_5['sec_6_e_plaster'] = $row_5['washroom_plaster'];
			$row_5['sec_6_e_ceiling'] = $row_5['washroom_roof'];
			$row_5['sec_6_e_seat'] = $row_5['washroom_seat'];
			$row_5['sec_6_e_plumbing'] = $row_5['washroom_plumbing'];
			$row_5['sec_6_f_number_of_door'] = $row_5['doors'];
			$row_5['sec_6_g_number_of_window'] = $row_5['windows'];
			$row_5['sec_6_h_length'] = $row_5['plaster_wall'];
			$row_5['sec_6_h_width'] = $row_5['plaster_roof'];
			$row_5['sec_6_i_other'] = $row_5['others'];

			if ($row_5['floor_image'] != '') {
				$row_5['sec_6_a_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['floor_image'];
			} else {
				$row_5['sec_6_a_img'] = '';
			}
			if ($row_5['wall_image'] != '') {
				$row_5['sec_6_b_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['wall_image'];
			} else {
				$row_5['sec_6_b_img'] = '';
			}
			if ($row_5['paint_image'] != '') {
				$row_5['sec_6_c_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['paint_image'];
			} else {
				$row_5['sec_6_c_img'] = '';
			}
			if ($row_5['roof_image'] != '') {
				$row_5['sec_6_d_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['roof_image'];
			} else {
				$row_5['sec_6_d_img'] = '';
			}
		} else {
			$row_5['sec_5_built_building'] = "";
			$row_5['sec_5_detailed_information'] = "";
			$row_5['sec_6_a_length'] = "";
			$row_5['sec_6_a_width'] = "";
			$row_5['sec_6_b_length'] = "";
			$row_5['sec_6_b_width'] = "";
			$row_5['sec_6_c_length'] = "";
			$row_5['sec_6_c_width'] = "";
			$row_5['sec_6_d_width'] = "";
			$row_5['sec_6_d_length'] = "";
			$row_5['sec_6_d_width'] = "";
			$row_5['sec_6_e_floor'] = "";
			$row_5['sec_6_e_plaster'] = "";
			$row_5['sec_6_e_ceiling'] = "";
			$row_5['sec_6_e_seat'] = "";
			$row_5['sec_6_e_plumbing'] = "";
			$row_5['sec_6_f_number_of_door'] = "";
			$row_5['sec_6_g_number_of_window'] = "";
			$row_5['sec_6_h_length'] = "";
			$row_5['sec_6_h_width'] = "";
			$row_5['sec_6_i_other'] = "";
			$row_5['sec_6_a_img'] = '';
			$row_5['sec_6_b_img'] = '';
			$row_5['sec_6_c_img'] = '';
			$row_5['sec_6_d_img'] = '';
		}


		
		$sql = 'select * from survey_invoice_sec_5_new where survey_id = "'. $row_invoice['sno'] .'"';	
		$res_5_new = execute_query($sql);
		if(mysqli_num_rows($res_5_new) != 0){
			$row_5_new = mysqli_fetch_assoc($res_5_new);
			$row_5_new['sec_5_new_samiti_vs_sadasya'] = $row_5_new['samiti_vs_sadasya'];
			$row_5_new['sec_5_new_recovery_amt'] = $row_5_new['recovery_amt'];
			$row_5_new['sec_5_new_monthly_recovery'] = $row_5_new['monthly_recovery'];
			$row_5_new['sec_5_new_arrears'] = $row_5_new['arrears'];
			$row_5_new['sec_5_new_remain_amt'] = $row_5_new['remain_amt'];
			$row_5_new['sec_5_new_month_cur'] = $row_5_new['month_cur'];
			$row_5_new['sec_5_new_gradual_recovery'] = $row_5_new['gradual_recovery'];
			$row_5_new['sec_5_new_deposit_center'] = $row_5_new['deposit_center'];
			$row_5_new['sec_5_new_deposit_amt'] = $row_5_new['deposit_amt'];
			$row_5_new['sec_5_new_reconciled_dcb'] = $row_5_new['reconciled_dcb'];
		} else {
			$row_5_new['sec_5_new_samiti_vs_sadasya'] = '';
			$row_5_new['sec_5_new_recovery_amt'] = '';
			$row_5_new['sec_5_new_monthly_recovery'] = '';
			$row_5_new['sec_5_new_arrears'] = '';
			$row_5_new['sec_5_new_remain_amt'] = '';
			$row_5_new['sec_5_new_month_cur'] = '';
			$row_5_new['sec_5_new_gradual_recovery'] = '';
			$row_5_new['sec_5_new_deposit_center'] = '';
			$row_5_new['sec_5_new_deposit_amt'] = '';
			$row_5_new['sec_5_new_reconciled_dcb'] = '';
		}
		
		
		$sql = 'select * from survey_invoice_sec_3_new_1 where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_1 = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_1) != 0){
			$row_3_new_1 = mysqli_fetch_assoc($res_3_new_1);
			$row_3_new_1['sec_3_profit_loss_1'] = $row_3_new_1['profit_loss_1'] ;
			$row_3_new_1['sec_3_profit_loss_amount_1'] = $row_3_new_1['profit_loss_amount_1'] ;
			$row_3_new_1['sec_3_accumulated_1'] = $row_3_new_1['accumulated_1'] ;
			$row_3_new_1['sec_3_accumulated_amount_1'] = $row_3_new_1['accumulated_amount_1'] ;
			$row_3_new_1['sec_3_profit_loss_2'] = $row_3_new_1['profit_loss_2'] ;
			$row_3_new_1['sec_3_profit_loss_amount_2'] = $row_3_new_1['profit_loss_amount_2'] ;
			$row_3_new_1['sec_3_accumulated_2'] = $row_3_new_1['accumulated_2'] ;
			$row_3_new_1['sec_3_accumulated_amount_2'] = $row_3_new_1['accumulated_amount_2'] ;
			$row_3_new_1['sec_3_profit_loss_3'] = $row_3_new_1['profit_loss_3'] ;
			$row_3_new_1['sec_3_profit_loss_amount_3'] = $row_3_new_1['profit_loss_amount_3'] ;
			$row_3_new_1['sec_3_accumulated_3'] = $row_3_new_1['accumulated_3'] ;
			$row_3_new_1['sec_3_accumulated_amount_3'] = $row_3_new_1['accumulated_amount_3'] ;
			$row_3_new_1['sec_3_financial_audit_year'] = $row_3_new_1['financial_audit_year'] ;
			$row_3_new_1['sec_3_audit_grading'] = $row_3_new_1['audit_grading'] ;
			$row_3_new_1['sec_3_compliance_status'] = $row_3_new_1['compliance_status'] ;
			$row_3_new_1['sec_3_agm_year'] = $row_3_new_1['agm_year'] ;
			$row_3_new_1['sec_3_dividend_year'] = $row_3_new_1['dividend_year'] ;
			$row_3_new_1['sec_3_dividend_per'] = $row_3_new_1['dividend_per'] ;
			$row_3_new_1['sec_3_dividend_amt'] = $row_3_new_1['dividend_amt'] ;
			$row_3_new_1['sec_3_santulan_patra'] = $row_3_new_1['santulan_patra'] ;
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

		$sql = 'select * from survey_invoice_sec_3_new_2_urea where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_urea = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_urea) != 0){
			$row_3_new_2_urea = mysqli_fetch_assoc($res_3_new_2_urea);
			$row_3_new_2_urea['sec_3_urea_opening'] = $row_3_new_2_urea['urea_opening'] ;
			$row_3_new_2_urea['sec_3_new_2_urea_1'] = $row_3_new_2_urea['urea_1'] ;
			$row_3_new_2_urea['sec_3_new_2_urea_2'] = $row_3_new_2_urea['urea_2'] ;
			$row_3_new_2_urea['sec_3_new_2_urea_3'] = $row_3_new_2_urea['urea_3'] ;
			$row_3_new_2_urea['sec_3_new_2_urea_4'] = $row_3_new_2_urea['urea_4'] ;
			$row_3_new_2_urea['sec_3_new_2_urea_5'] = $row_3_new_2_urea['urea_5'] ;
			$row_3_new_2_urea['sec_3_new_2_urea_6'] = $row_3_new_2_urea['urea_6'] ;
		} else {
			$row_3_new_2_urea = [
				'sec_3_urea_opening' => '',
				'sec_3_new_2_urea_1' => '',
				'sec_3_new_2_urea_2' => '',
				'sec_3_new_2_urea_3' => '',
				'sec_3_new_2_urea_4' => '',
				'sec_3_new_2_urea_5' => '',
				'sec_3_new_2_urea_6' => ''
			];
		}
		
		$sql = 'select * from survey_invoice_sec_3_new_2_dap where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_dap = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_dap) != 0){
			$row_3_new_2_dap = mysqli_fetch_assoc($res_3_new_2_dap);
			$row_3_new_2_dap['sec_3_dap_opening'] = $row_3_new_2_dap['dap_opening'] ;
			$row_3_new_2_dap['sec_3_new_2_dap_1'] = $row_3_new_2_dap['dap_1'] ;
			$row_3_new_2_dap['sec_3_new_2_dap_2'] = $row_3_new_2_dap['dap_2'] ;
			$row_3_new_2_dap['sec_3_new_2_dap_3'] = $row_3_new_2_dap['dap_3'] ;
			$row_3_new_2_dap['sec_3_new_2_dap_4'] = $row_3_new_2_dap['dap_4'] ;
			$row_3_new_2_dap['sec_3_new_2_dap_5'] = $row_3_new_2_dap['dap_5'] ;
			$row_3_new_2_dap['sec_3_new_2_dap_6'] = $row_3_new_2_dap['dap_6'] ;
		} else {
			$row_3_new_2_dap = [
				'sec_3_dap_opening' => '',
				'sec_3_new_2_dap_1' => '',
				'sec_3_new_2_dap_2' => '',
				'sec_3_new_2_dap_3' => '',
				'sec_3_new_2_dap_4' => '',
				'sec_3_new_2_dap_5' => '',
				'sec_3_new_2_dap_6' => ''
			];
		}
		$sql = 'select * from survey_invoice_sec_3_new_2_npk where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_npk = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_npk) != 0){
			$row_3_new_2_npk = mysqli_fetch_assoc($res_3_new_2_npk);
			$row_3_new_2_npk['sec_3_npk_opening'] = $row_3_new_2_npk['npk_opening'] ;
			$row_3_new_2_npk['sec_3_new_2_npk_1'] = $row_3_new_2_npk['npk_1'] ;
			$row_3_new_2_npk['sec_3_new_2_npk_2'] = $row_3_new_2_npk['npk_2'] ;
			$row_3_new_2_npk['sec_3_new_2_npk_3'] = $row_3_new_2_npk['npk_3'] ;
			$row_3_new_2_npk['sec_3_new_2_npk_4'] = $row_3_new_2_npk['npk_4'] ;
			$row_3_new_2_npk['sec_3_new_2_npk_5'] = $row_3_new_2_npk['npk_5'] ;
			$row_3_new_2_npk['sec_3_new_2_npk_6'] = $row_3_new_2_npk['npk_6'] ;
		} else {
			$row_3_new_2_npk = [
				'sec_3_npk_opening' => '',
				'sec_3_new_2_npk_1' => '',
				'sec_3_new_2_npk_2' => '',
				'sec_3_new_2_npk_3' => '',
				'sec_3_new_2_npk_4' => '',
				'sec_3_new_2_npk_5' => '',
				'sec_3_new_2_npk_6' => ''
			];
		}

		$sql = 'select * from survey_invoice_sec_3_new_2_nano_urea where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_nano_urea = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_nano_urea) != 0){
			$row_3_new_2_nano_urea = mysqli_fetch_assoc($res_3_new_2_nano_urea);
			$row_3_new_2_nano_urea['sec_3_nano_urea_opening'] = $row_3_new_2_nano_urea['nano_urea_opening'] ;
			$row_3_new_2_nano_urea['sec_3_new_2_nano_urea_1'] = $row_3_new_2_nano_urea['nano_urea_1'] ;
			$row_3_new_2_nano_urea['sec_3_new_2_nano_urea_2'] = $row_3_new_2_nano_urea['nano_urea_2'] ;
			$row_3_new_2_nano_urea['sec_3_new_2_nano_urea_3'] = $row_3_new_2_nano_urea['nano_urea_3'] ;
			$row_3_new_2_nano_urea['sec_3_new_2_nano_urea_4'] = $row_3_new_2_nano_urea['nano_urea_4'] ;
			$row_3_new_2_nano_urea['sec_3_new_2_nano_urea_5'] = $row_3_new_2_nano_urea['nano_urea_5'] ;
			$row_3_new_2_nano_urea['sec_3_new_2_nano_urea_6'] = $row_3_new_2_nano_urea['nano_urea_6'] ;
		} else {
			$row_3_new_2_nano_urea = [
				'sec_3_nano_urea_opening' => '',
				'sec_3_new_2_nano_urea_1' => '',
				'sec_3_new_2_nano_urea_2' => '',
				'sec_3_new_2_nano_urea_3' => '',
				'sec_3_new_2_nano_urea_4' => '',
				'sec_3_new_2_nano_urea_5' => '',
				'sec_3_new_2_nano_urea_6' => ''
			];
		}

		$sql = 'select * from survey_invoice_sec_3_new_2_nano_dap where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_nano_dap = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_nano_dap) != 0){
			$row_3_new_2_nano_dap = mysqli_fetch_assoc($res_3_new_2_nano_dap);
			$row_3_new_2_nano_dap['sec_3_nano_dap_opening'] = $row_3_new_2_nano_dap['nano_dap_opening'] ;
			$row_3_new_2_nano_dap['sec_3_new_2_nano_dap_1'] = $row_3_new_2_nano_dap['nano_dap_1'] ;
			$row_3_new_2_nano_dap['sec_3_new_2_nano_dap_2'] = $row_3_new_2_nano_dap['nano_dap_2'] ;
			$row_3_new_2_nano_dap['sec_3_new_2_nano_dap_3'] = $row_3_new_2_nano_dap['nano_dap_3'] ;
			$row_3_new_2_nano_dap['sec_3_new_2_nano_dap_4'] = $row_3_new_2_nano_dap['nano_dap_4'] ;
			$row_3_new_2_nano_dap['sec_3_new_2_nano_dap_5'] = $row_3_new_2_nano_dap['nano_dap_5'] ;
			$row_3_new_2_nano_dap['sec_3_new_2_nano_dap_6'] = $row_3_new_2_nano_dap['nano_dap_6'] ;
		} else {
			$row_3_new_2_nano_dap = [
				'sec_3_nano_dap_opening' => '',
				'sec_3_new_2_nano_dap_1' => '',
				'sec_3_new_2_nano_dap_2' => '',
				'sec_3_new_2_nano_dap_3' => '',
				'sec_3_new_2_nano_dap_4' => '',
				'sec_3_new_2_nano_dap_5' => '',
				'sec_3_new_2_nano_dap_6' => ''
			];
		}

		$sql = 'select * from survey_invoice_sec_3_new_2_pesticide where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_pesticide = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_pesticide) != 0){
			$row_3_new_2_pesticide = mysqli_fetch_assoc($res_3_new_2_pesticide);
			$row_3_new_2_pesticide['sec_3_pesticide_opening'] = $row_3_new_2_pesticide['pesticide_opening'] ;
			$row_3_new_2_pesticide['sec_3_new_2_pesticide_1'] = $row_3_new_2_pesticide['pesticide_1'] ;
			$row_3_new_2_pesticide['sec_3_new_2_pesticide_2'] = $row_3_new_2_pesticide['pesticide_2'] ;
			$row_3_new_2_pesticide['sec_3_new_2_pesticide_3'] = $row_3_new_2_pesticide['pesticide_3'] ;
			$row_3_new_2_pesticide['sec_3_new_2_pesticide_4'] = $row_3_new_2_pesticide['pesticide_4'] ;
			$row_3_new_2_pesticide['sec_3_new_2_pesticide_5'] = $row_3_new_2_pesticide['pesticide_5'] ;
			$row_3_new_2_pesticide['sec_3_new_2_pesticide_6'] = $row_3_new_2_pesticide['pesticide_6'] ;
		} else {
			$row_3_new_2_pesticide = [
				'sec_3_pesticide_opening' => '',
				'sec_3_new_2_pesticide_1' => '',
				'sec_3_new_2_pesticide_2' => '',
				'sec_3_new_2_pesticide_3' => '',
				'sec_3_new_2_pesticide_4' => '',
				'sec_3_new_2_pesticide_5' => '',
				'sec_3_new_2_pesticide_6' => ''
			];
		}

		$sql = 'select * from survey_invoice_sec_3_new_2_seeds where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_seeds = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_seeds) != 0){
			$row_3_new_2_seeds = mysqli_fetch_assoc($res_3_new_2_seeds);
			$row_3_new_2_seeds['sec_3_seeds_opening'] = $row_3_new_2_seeds['seeds_opening'] ;
			$row_3_new_2_seeds['sec_3_new_2_seeds_1'] = $row_3_new_2_seeds['seeds_1'] ;
			$row_3_new_2_seeds['sec_3_new_2_seeds_2'] = $row_3_new_2_seeds['seeds_2'] ;
			$row_3_new_2_seeds['sec_3_new_2_seeds_3'] = $row_3_new_2_seeds['seeds_3'] ;
			$row_3_new_2_seeds['sec_3_new_2_seeds_4'] = $row_3_new_2_seeds['seeds_4'] ;
			$row_3_new_2_seeds['sec_3_new_2_seeds_5'] = $row_3_new_2_seeds['seeds_5'] ;
			$row_3_new_2_seeds['sec_3_new_2_seeds_6'] = $row_3_new_2_seeds['seeds_6'] ;
		} else {
			$row_3_new_2_seeds = [
				'sec_3_seeds_opening' => '',
				'sec_3_new_2_seeds_1' => '',
				'sec_3_new_2_seeds_2' => '',
				'sec_3_new_2_seeds_3' => '',
				'sec_3_new_2_seeds_4' => '',
				'sec_3_new_2_seeds_5' => '',
				'sec_3_new_2_seeds_6' => ''
			];
		}
		$sql = 'select * from survey_invoice_sec_3_new_2_iffco where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_iffco = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_iffco) != 0){
			$row_3_new_2_iffco = mysqli_fetch_assoc($res_3_new_2_iffco);
			$row_3_new_2_iffco['sec_3_new_2_iffco_opening'] = $row_3_new_2_iffco['iffco_opening'] ;
			$row_3_new_2_iffco['sec_3_new_2_iffco_1'] = $row_3_new_2_iffco['iffco_1'] ;
			$row_3_new_2_iffco['sec_3_new_2_iffco_2'] = $row_3_new_2_iffco['iffco_2'] ;
			$row_3_new_2_iffco['sec_3_new_2_iffco_3'] = $row_3_new_2_iffco['iffco_3'] ;
			$row_3_new_2_iffco['sec_3_new_2_iffco_4'] = $row_3_new_2_iffco['iffco_4'] ;
			$row_3_new_2_iffco['sec_3_new_2_iffco_5'] = $row_3_new_2_iffco['iffco_5'] ;
			$row_3_new_2_iffco['sec_3_new_2_iffco_6'] = $row_3_new_2_iffco['iffco_6'] ;
		} else {
			$row_3_new_2_iffco = [
				'sec_3_new_2_iffco_opening' => '',
				'sec_3_new_2_iffco_1' => '',
				'sec_3_new_2_iffco_2' => '',
				'sec_3_new_2_iffco_3' => '',
				'sec_3_new_2_iffco_4' => '',
				'sec_3_new_2_iffco_5' => '',
				'sec_3_new_2_iffco_6' => ''
			];
		}

		$sql = 'select * from survey_invoice_sec_3_new_2_kribhko where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_kribhko = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_kribhko) != 0){
			$row_3_new_2_kribhko = mysqli_fetch_assoc($res_3_new_2_kribhko);
			$row_3_new_2_kribhko['sec_3_new_2_kribhko_opening'] = $row_3_new_2_kribhko['kribhko_opening'] ;
			$row_3_new_2_kribhko['sec_3_new_2_kribhko_1'] = $row_3_new_2_kribhko['kribhko_1'] ;
			$row_3_new_2_kribhko['sec_3_new_2_kribhko_2'] = $row_3_new_2_kribhko['kribhko_2'] ;
			$row_3_new_2_kribhko['sec_3_new_2_kribhko_3'] = $row_3_new_2_kribhko['kribhko_3'] ;
			$row_3_new_2_kribhko['sec_3_new_2_kribhko_4'] = $row_3_new_2_kribhko['kribhko_4'] ;
			$row_3_new_2_kribhko['sec_3_new_2_kribhko_5'] = $row_3_new_2_kribhko['kribhko_5'] ;
			$row_3_new_2_kribhko['sec_3_new_2_kribhko_6'] = $row_3_new_2_kribhko['kribhko_6'] ;
		} else {
			$row_3_new_2_kribhko = [
				'sec_3_new_2_kribhko_opening' => '',
				'sec_3_new_2_kribhko_1' => '',
				'sec_3_new_2_kribhko_2' => '',
				'sec_3_new_2_kribhko_3' => '',
				'sec_3_new_2_kribhko_4' => '',
				'sec_3_new_2_kribhko_5' => '',
				'sec_3_new_2_kribhko_6' => ''
			];
		}

		$sql = 'select * from survey_invoice_sec_3_new_2_other where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_new_2_other = execute_query($sql);
		
		if(mysqli_num_rows($res_3_new_2_other) != 0){
			$row_3_new_2_other = mysqli_fetch_assoc($res_3_new_2_other);
			$row_3_new_2_other['sec_3_new_2_other_opening'] = $row_3_new_2_other['other_opening'] ;
			$row_3_new_2_other['sec_3_new_2_other_1'] = $row_3_new_2_other['other_1'] ;
			$row_3_new_2_other['sec_3_new_2_other_2'] = $row_3_new_2_other['other_2'] ;
			$row_3_new_2_other['sec_3_new_2_other_3'] = $row_3_new_2_other['other_3'] ;
			$row_3_new_2_other['sec_3_new_2_other_4'] = $row_3_new_2_other['other_4'] ;
			$row_3_new_2_other['sec_3_new_2_other_5'] = $row_3_new_2_other['other_5'] ;
			$row_3_new_2_other['sec_3_new_2_other_6'] = $row_3_new_2_other['other_6'] ;
		} else {
			$row_3_new_2_other = [
				'sec_3_new_2_other_opening' => '',
				'sec_3_new_2_other_1' => '',
				'sec_3_new_2_other_2' => '',
				'sec_3_new_2_other_3' => '',
				'sec_3_new_2_other_4' => '',
				'sec_3_new_2_other_5' => '',
				'sec_3_new_2_other_6' => ''
			];
		}

		$sql = 'select * from survey_invoice_sec_3_new_3 where survey_id = "'. $row_invoice['sno'] .'"';
		$res_new_3 = execute_query($sql);

		if(mysqli_num_rows($res_new_3) != 0){
			$row_new_3 = mysqli_fetch_assoc($res_new_3);
			$row_new_3['sec_new_3_stock_insurance'] = $row_new_3['stock_insurance'] ;
			$row_new_3['sec_new_3_ten_lakh_loan_1'] = $row_new_3['ten_lakh_loan_1'] ;
			$row_new_3['sec_new_3_ten_lakh_loan_2'] = $row_new_3['ten_lakh_loan_2'] ;
			$row_new_3['sec_new_3_csc'] = $row_new_3['csc'] ;
			$row_new_3['sec_new_3_csc_transactions'] = $row_new_3['csc_transactions'] ;
			$row_new_3['sec_new_3_csc_amt'] = $row_new_3['csc_amt'] ;
			$row_new_3['sec_new_3_csc_commission'] = $row_new_3['csc_commission'] ;
			$row_new_3['sec_new_3_pds_1'] = $row_new_3['pds_1'] ;
			$row_new_3['sec_new_3_pds_tornover'] = $row_new_3['pds_tornover'] ;
		} else {
			$row_new_3 = [
				'sec_new_3_stock_insurance' => '',
				'sec_new_3_ten_lakh_loan_1' => '',
				'sec_new_3_ten_lakh_loan_2' => '',
				'sec_new_3_csc' => '',
				'sec_new_3_csc_transactions' => '',
				'sec_new_3_csc_amt' => '',
				'sec_new_3_csc_commission' => '',
				'sec_new_3_pds_1' => '',
				'sec_new_3_pds_tornover' => '',
			];
		}

			$sql = 'select * from survey_invoice_sec_3_loan_distribution where survey_id = "'. $row_invoice['sno'] .'"';
			$res_new_3 = execute_query($sql);

			if(mysqli_num_rows($res_new_3) != 0){
				$row_new_3_loan = mysqli_fetch_assoc($res_new_3);
				
				$row_new_3_loan['sec_3_select_loan'] = $row_new_3_loan['locan_type'] ;
				$row_new_3_loan['sec_new_3_loan_distribution'] = $row_new_3_loan['loan_distribution'] ;
				$row_new_3_loan['sec_new_3_loan_amt_target'] = $row_new_3_loan['loan_amt_target'] ;
				$row_new_3_loan['sec_new_3_fy_loan_sanctioned_amt'] = $row_new_3_loan['fy_loan_sanctioned_amt'] ;
				$row_new_3_loan['sec_new_3_loan_sanctioned_amt'] = $row_new_3_loan['loan_sanctioned_amt'] ;
				$row_new_3_loan['sec_new_3_farmers_3_lakh'] = $row_new_3_loan['farmers_3_lakh'] ;
				$row_new_3_loan['sec_new_3_beneficiaries'] = $row_new_3_loan['beneficiaries'] ;
				$row_new_3_loan['sec_new_3_kcc_iss_beneficiaries'] = $row_new_3_loan['kcc_iss_beneficiaries'] ;
				$row_new_3_loan['sec_new_3_loan_amt_limit'] = $row_new_3_loan['loan_amt_limit'] ;
				
			} else {
				$row_new_3_loan = [
					'sec_3_select_loan' => '',
					'sec_new_3_loan_distribution' => '',
					'sec_new_3_loan_amt_target' => '',
					'sec_new_3_fy_loan_sanctioned_amt' => '',
					'sec_new_3_loan_sanctioned_amt' => '',
					'sec_new_3_farmers_3_lakh' => '',
					'sec_new_3_beneficiaries' => '',
					'sec_new_3_kcc_iss_beneficiaries' => '',
				];

				// if ($_POST['sec_3_select_loan'] == 'crop_loan') {
					// $row_new_3_loan['sec_new_3_loan_amt_limit'] = '';
				// }
			}
			
			$sql = 'select * from survey_invoice_sec_3_dcb_distribution where survey_id = "'. $row_invoice['sno'] .'"';
			$res_new_3_dis = execute_query($sql);

			if(mysqli_num_rows($res_new_3_dis) != 0){
				$row_new_3_loan_distribution = mysqli_fetch_assoc($res_new_3_dis);
				
			echo 	$row_new_3_loan_distribution['sec_new_3_dcb_loan_distribution'] = $row_new_3_loan_distribution['dcb_loan_distribution'] ;
				$row_new_3_loan_distribution['sec_new_3_diversification_num'] = $row_new_3_loan_distribution['diversification_num'] ;
				$row_new_3_loan_distribution['sec_new_3_diversification_target'] = $row_new_3_loan_distribution['diversification_target'] ;
				$row_new_3_loan_distribution['sec_new_3_diversification_supply'] = $row_new_3_loan_distribution['diversification_supply'] ;
			} else {
				$row_new_3_loan_distribution = [
					
					'sec_new_3_dcb_loan_distribution' => '',
					'sec_new_3_diversification_num' => '',
					'sec_new_3_diversification_target' => '',
					'sec_new_3_diversification_supply' => '',
				];
			}
// print_r($row_new_3_loan_distribution);			
		
		$sql = 'select * from survey_invoice_sec_3_ten_lakh_loan_limit where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_loan_limit = execute_query($sql);
		if(mysqli_num_rows($res_3_loan_limit) != 0){
			$row_3_loan_limit = mysqli_fetch_assoc($res_3_loan_limit);
			
			$row_3_loan_limit['sec_new_3_ten_lakh_loan_limit'] = $row_3_loan_limit['ten_lakh_loan_limit'] ;
			$row_3_loan_limit['sec_new_3_gl_code'] = $row_3_loan_limit['gl_code'] ;
			$row_3_loan_limit['sec_new_3_acc_num'] = $row_3_loan_limit['acc_num'] ;
			$row_3_loan_limit['sec_new_3_open_bal_1'] = $row_3_loan_limit['open_bal_1'] ;
			$row_3_loan_limit['sec_new_3_debit_amt_1'] = $row_3_loan_limit['debit_amt_1'] ;
			$row_3_loan_limit['sec_new_3_credit_amt_1'] = $row_3_loan_limit['credit_amt_1'] ;
			$row_3_loan_limit['sec_new_3_num_of_transactions_1'] = $row_3_loan_limit['num_of_transactions_1'] ;
			$row_3_loan_limit['sec_new_3_open_bal'] = $row_3_loan_limit['open_bal'] ;
			$row_3_loan_limit['sec_new_3_debit_amt'] = $row_3_loan_limit['debit_amt'] ;
			$row_3_loan_limit['sec_new_3_credit_amt'] = $row_3_loan_limit['credit_amt'] ;
			$row_3_loan_limit['sec_new_3_num_of_transactions'] = $row_3_loan_limit['num_of_transactions'] ;

			$row_3_loan_limit['sec_new_3_loan_other'] = $row_3_loan_limit['loan_other'] ;
			$row_3_loan_limit['sec_new_3_gl_code_other_1'] = $row_3_loan_limit['gl_code_other_1'] ;
			$row_3_loan_limit['sec_new_3_acc_num_other_1'] = $row_3_loan_limit['acc_num_other_1'] ;
			$row_3_loan_limit['sec_new_3_open_bal_other_1'] = $row_3_loan_limit['open_bal_other_1'] ;
			$row_3_loan_limit['sec_new_3_debit_amt_other_1'] = $row_3_loan_limit['debit_amt_other_1'] ;
			$row_3_loan_limit['sec_new_3_credit_amt_other_1'] = $row_3_loan_limit['credit_amt_other_1'] ;
			$row_3_loan_limit['sec_new_3_transaction_other_1'] = $row_3_loan_limit['transaction_other_1'] ;
			$row_3_loan_limit['sec_new_3_open_bal_other'] = $row_3_loan_limit['open_bal_other'] ;
			$row_3_loan_limit['sec_new_3_debit_amt_cur'] = $row_3_loan_limit['debit_amt_cur'] ;
			$row_3_loan_limit['sec_new_3_credit_amt_cur'] = $row_3_loan_limit['credit_amt_cur'] ;
			$row_3_loan_limit['sec_new_3_num_of_transaction_cur'] = $row_3_loan_limit['num_of_transaction_cur'] ;
		} else {
			
			$row_3_loan_limit['sec_new_3_ten_lakh_loan_limit'] = '';
			$row_3_loan_limit['sec_new_3_gl_code'] = '';
			$row_3_loan_limit['sec_new_3_gl_code'] = '';
			$row_3_loan_limit['sec_new_3_acc_num'] = '';
			$row_3_loan_limit['sec_new_3_open_bal_1'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt_1'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt_1'] = '';
			$row_3_loan_limit['sec_new_3_num_of_transactions_1'] = '';
			$row_3_loan_limit['sec_new_3_open_bal'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt'] = '';
			$row_3_loan_limit['sec_new_3_num_of_transactions'] = '';

			$row_3_loan_limit['sec_new_3_loan_other'] = '';
			$row_3_loan_limit['sec_new_3_gl_code_other_1'] = '';
			$row_3_loan_limit['sec_new_3_acc_num_other_1'] = '';
			$row_3_loan_limit['sec_new_3_open_bal_other_1'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt_other_1'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt_other_1'] = '';
			$row_3_loan_limit['sec_new_3_transaction_other_1'] = '';
			$row_3_loan_limit['sec_new_3_open_bal_other'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt_cur'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt_cur'] = '';
			$row_3_loan_limit['sec_new_3_num_of_transaction_cur'] = '';
		}


		$sql = 'select * from survey_invoice_sec_3_new_msp where survey_id = "' . $row_invoice['sno'] . '"';	
		$res_3_msp = execute_query($sql);
		if(mysqli_num_rows($res_3_msp) != 0){
			$row_3_msp = mysqli_fetch_assoc($res_3_msp);
			$row_3_msp['sec_new_3_msp'] = $row_3_msp['msp'] ;
			$row_3_msp['sec_new_3_agency_name_kharif'] = $row_3_msp['agency_name_kharif'] ;
			$row_3_msp['sec_new_3_kharif_crops'] = $row_3_msp['kharif_crops'] ;
			$row_3_msp['sec_new_3_crop_name_kharif'] = $row_3_msp['crop_name_kharif'] ;
			$row_3_msp['sec_new_3_quantity_kharif'] = $row_3_msp['quantity_kharif'] ;
			$row_3_msp['sec_new_3_amt_kharif'] = $row_3_msp['amt_kharif'] ;
			$row_3_msp['sec_new_3_commission_pay_kharif'] = $row_3_msp['commission_pay_kharif'] ;
			$row_3_msp['sec_new_3_commission_rec_kharif'] = $row_3_msp['commission_rec_kharif'] ;

			$row_3_msp['sec_new_3_agency_name_rabi'] = $row_3_msp['agency_name_rabi'] ;
			$row_3_msp['sec_new_3_rabi_crops'] = $row_3_msp['rabi_crops'] ;
			$row_3_msp['sec_new_3_crop_name_rabi'] = $row_3_msp['crop_name_rabi'] ;
			$row_3_msp['sec_new_3_quantity_rabi'] = $row_3_msp['quantity_rabi'] ;
			$row_3_msp['sec_new_3_amt_rabi'] = $row_3_msp['amt_rabi'] ;
			$row_3_msp['sec_new_3_commission_pay_rabi'] = $row_3_msp['commission_pay_rabi'] ;
			$row_3_msp['sec_new_3_commission_rec_rabi'] = $row_3_msp['commission_rec_rabi'] ;
		} else {
			
			$row_3_msp['sec_new_3_msp'] = '';
			$row_3_msp['sec_new_3_agency_name_kharif'] = '';
			$row_3_msp['sec_new_3_kharif_crops'] = '';
			$row_3_msp['sec_new_3_crop_name_kharif'] = '';
			$row_3_msp['sec_new_3_quantity_kharif'] = '';
			$row_3_msp['sec_new_3_amt_kharif'] = '';
			$row_3_msp['sec_new_3_commission_pay_kharif'] = '';
			$row_3_msp['sec_new_3_commission_rec_kharif'] = '';

			$row_3_msp['sec_new_3_agency_name_rabi'] = '';
			$row_3_msp['sec_new_3_rabi_crops'] = '';
			$row_3_msp['sec_new_3_crop_name_rabi'] = '';
			$row_3_msp['sec_new_3_quantity_rabi'] = '';
			$row_3_msp['sec_new_3_amt_rabi'] = '';
			$row_3_msp['sec_new_3_commission_pay_rabi'] = '';
			$row_3_msp['sec_new_3_commission_rec_rabi'] = '';
		}



	}else{
		
		// echo '@@@@@@@@@@@@@@@@@@survey Not insert:22222';
		
		$sql='SELECT test2.`sno` as society_id, `col1`, `col2`, `col3`, test2.col4 as society_name, master_society_type.type_name as type_name, division_name, district_name, master_block.sno as block_id, tehseel_name, block_name, `col5`, `col6` , `col7`, `col8`, `col9`, `col10`, `col11`, `col12`, `col13`, `col14`, `col15`, `col16` as mobile_number, `col17`, `col18`, `col19`, `col20`, `col21`, `col22`, `col23`, `col24`, `col25`, `col26`, `col27`, `col28`, `col29`, `col30`, `col31`, `col32`, `col33`, `col34`, `col35`, `col36`, `col37`, test2.`status` FROM test2  left join master_block on master_block.sno = col6 left join master_tehseel on master_tehseel.sno = col5 left join master_district on master_district.sno = col2 left join master_division on master_division.sno = col1 left join master_society_type on master_society_type.sno = col3  where test2.sno="' . $_GET['exdid'] . '"';
		$result_society = execute_query($sql);
		$row_invoice = mysqli_fetch_assoc($result_society);
		
		$row_invoice['latitude'] ="";
		$row_invoice['longitude'] ="";		
		$row_invoice['committee_status'] ="";
		$row_invoice['committee_date'] ="";
		$row_invoice['society_registration_no'] ="";
		$row_invoice['sec_1_society_registration_date'] = date('Y-m-d');
		$row_invoice['email_id'] ="";
		$row_invoice['liquidation'] ="";
		$row_invoice['liquidation_date'] ="";
		$row_invoice['litigation'] ="";
		$row_invoice['dispute_details'] ="";
					
		$row_invoice['active_members'] ="";
		$row_invoice['inactive_members'] ="";
		$row_invoice['kcc_members'] ="";
		$row_invoice['total_farmers_member'] ="";
		$row_invoice['total_non_farmers_member'] ="";
					
		$row_invoice['new_members'] ="";
		$row_invoice['contribution_received_capital'] ="";
		$row_invoice['inactive_to_active_members'] ="";
		$row_invoice['total_members'] ="";
		$row_invoice['mobile_number'] ="";
		$row_invoice['sno']="";
		
		$row_invoice['society_building_rent_amount']='';
		$row_invoice['society_building_area']='';
		
		$i=1;
		$row_2_1_2['count'] = 1;
		$row_2_1_2['sec_2_1_2_business_description_' . $i] = '';
		$row_2_1_2['sec_2_1_2_value_' . $i] = '';
		$row_6_2['count']=1;
		$row_3_3['count']=1;
		$row_3_4['count']=1;
		$row_3_5['count']=1;
		$row_7_6['count']=1;

		$row_6_2['count']=1;
		$row_6_2['sec_6_2_designation_1'] ="";
		$row_6_2['sec_6_2_name_1'] ="";
		$row_6_2['sec_6_2_father_name_1'] ="";
		$row_6_2['sec_6_2__mob_no_1'] ="";

		$row_3_5['sec_3_c_id'] = 1;
		$row_3_5['sec_3_c_length_1'] ="";
		$row_3_5['sec_3_c_vacant_land_status_1'] ="";
		$row_3_5['sec_3_c_land_location_1'] ="";
		$row_3_5['sec_3_c_suitable_godown_1'] ="";
		$row_3_5['sec_3_c_rak_distance_1'] ="";
		$row_3_5['sec_3_c_approach_road_1'] ="";
		$row_3_5['sec_3_c_paved_road_1'] ="";
		
		$row_2['sec_2_stock_insurance']="";
		$row_sec_2['closing_stock_1_1'] = '';
		$row_sec_2['book_value_1_1'] = '';
		$row_sec_2['closing_stock_2_1'] = '';
		$row_sec_2['book_value_2_1'] = '';
		
		$row_sec_2_1['scraped_item_name_1'] = "";
		$row_sec_2_1['scraped_item_description_1'] = "";
		$row_sec_2_1['book_value_1'] = "";

		$row_sec_2_1['scraped_item_name_2'] = "";
		$row_sec_2_1['scraped_item_description_2'] = "";
		$row_sec_2_1['book_value_2'] = "";

		$row_sec_2_1['scraped_item_name_3'] = "";
		$row_sec_2_1['scraped_item_description_3'] = "";
		$row_sec_2_1['book_value_3'] = "";

		$row_sec_2_1['scraped_item_name_4'] = "";
		$row_sec_2_1['scraped_item_description_4'] = "";
		$row_sec_2_1['book_value_4'] = "";

		$row_sec_2_1['scraped_item_name_5'] = "";
		$row_sec_2_1['scraped_item_description_5'] = "";
		$row_sec_2_1['book_value_5'] = "";

		$row_sec_2_1['scraped_item_name_6'] = "";
		$row_sec_2_1['scraped_item_description_6'] = "";
		$row_sec_2_1['book_value_6'] = "";

		$row_sec_2_1['scraped_item_name_7'] = "";
		$row_sec_2_1['scraped_item_description_7'] = "";
		$row_sec_2_1['book_value_7'] = "";

		$row_sec_2_1['scraped_item_name_8'] = "";
		$row_sec_2_1['scraped_item_description_8'] = "";
		$row_sec_2_1['book_value_8'] = "";

		$row_sec_2_1['scraped_item_name_9'] = "";
		$row_sec_2_1['scraped_item_description_9'] = "";
		$row_sec_2_1['book_value_9'] = "";

		$row_sec_2_1['scraped_item_name_10'] = "";
		$row_sec_2_1['scraped_item_description_10'] = "";
		$row_sec_2_1['book_value_10'] = "";
		
		$row_val=10;
		$b=1;
		while ($b<=$row_val) {
			$row_sec_2_2['item_name_'.$b] = '';
			$row_sec_2_2['item_description_'.$b] = '';
			$row_sec_2_2['scheme_name_'.$b] = '';
			$row_sec_2_2['date_'.$b] = '';
			$row_sec_2_2['purchase_value_'.$b] = '';
			$row_sec_2_2['quantity_'.$b] = '';
			$b++;
		}
		
		
		$row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = '';
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
		$row_3_new_2_urea = [
				'sec_3_urea_opening' => '',
				'sec_3_new_2_urea_1' => '',
				'sec_3_new_2_urea_2' => '',
				'sec_3_new_2_urea_3' => '',
				'sec_3_new_2_urea_4' => '',
				'sec_3_new_2_urea_5' => '',
				'sec_3_new_2_urea_6' => ''
			];
			
			
		$row_new_3_loan_distribution = [
			
			'sec_new_3_dcb_loan_distribution' => '',
			'sec_new_3_diversification_num' => '',
			'sec_new_3_diversification_target' => '',
			'sec_new_3_diversification_supply' => '',
		];
		$row_new_3_loan = [
			'sec_3_select_loan' => '',
			'sec_new_3_loan_distribution' => '',
			'sec_new_3_loan_amt_target' => '',
			'sec_new_3_fy_loan_sanctioned_amt' => '',
			'sec_new_3_loan_sanctioned_amt' => '',
			'sec_new_3_farmers_3_lakh' => '',
			'sec_new_3_beneficiaries' => '',
			'sec_new_3_kcc_iss_beneficiaries' => '',
		];
		$row_new_3 = [
			'sec_new_3_stock_insurance' => '',
			'sec_new_3_ten_lakh_loan_1' => '',
			'sec_new_3_ten_lakh_loan_2' => '',
			'sec_new_3_csc' => '',
			'sec_new_3_csc_transactions' => '',
			'sec_new_3_csc_amt' => '',
			'sec_new_3_csc_commission' => '',
			'sec_new_3_pds_1' => '',
			'sec_new_3_pds_tornover' => '',
		];
		$row_3_new_2_kribhko = [
			'sec_3_new_2_kribhko_opening' => '',
			'sec_3_new_2_kribhko_1' => '',
			'sec_3_new_2_kribhko_2' => '',
			'sec_3_new_2_kribhko_3' => '',
			'sec_3_new_2_kribhko_4' => '',
			'sec_3_new_2_kribhko_5' => '',
			'sec_3_new_2_kribhko_6' => ''
		];
		$row_3_new_2_iffco = [
			'sec_3_new_2_iffco_opening' => '',
			'sec_3_new_2_iffco_1' => '',
			'sec_3_new_2_iffco_2' => '',
			'sec_3_new_2_iffco_3' => '',
			'sec_3_new_2_iffco_4' => '',
			'sec_3_new_2_iffco_5' => '',
			'sec_3_new_2_iffco_6' => ''
		];
		$row_3_new_2_seeds = [
			'sec_3_seeds_opening' => '',
			'sec_3_new_2_seeds_1' => '',
			'sec_3_new_2_seeds_2' => '',
			'sec_3_new_2_seeds_3' => '',
			'sec_3_new_2_seeds_4' => '',
			'sec_3_new_2_seeds_5' => '',
			'sec_3_new_2_seeds_6' => ''
		];
		$row_3_new_2_nano_dap = [
			'sec_3_nano_dap_opening' => '',
			'sec_3_new_2_nano_dap_1' => '',
			'sec_3_new_2_nano_dap_2' => '',
			'sec_3_new_2_nano_dap_3' => '',
			'sec_3_new_2_nano_dap_4' => '',
			'sec_3_new_2_nano_dap_5' => '',
			'sec_3_new_2_nano_dap_6' => ''
		];
		$row_3_new_2_nano_dap = [
			'sec_3_nano_dap_opening' => '',
			'sec_3_new_2_nano_dap_1' => '',
			'sec_3_new_2_nano_dap_2' => '',
			'sec_3_new_2_nano_dap_3' => '',
			'sec_3_new_2_nano_dap_4' => '',
			'sec_3_new_2_nano_dap_5' => '',
			'sec_3_new_2_nano_dap_6' => ''
		];
		$row_3_new_2_nano_urea = [
			'sec_3_nano_urea_opening' => '',
			'sec_3_new_2_nano_urea_1' => '',
			'sec_3_new_2_nano_urea_2' => '',
			'sec_3_new_2_nano_urea_3' => '',
			'sec_3_new_2_nano_urea_4' => '',
			'sec_3_new_2_nano_urea_5' => '',
			'sec_3_new_2_nano_urea_6' => ''
		];
		$row_3_5_1['sec_6_illegal_possession']= "";
		$row_sec_1_2_msc['sec_1_1_2_msc']= "";
		$row_5_new['sec_5_new_samiti_vs_sadasya'] = '';
			$row_5_new['sec_5_new_recovery_amt'] = '';
			$row_5_new['sec_5_new_monthly_recovery'] = '';
			$row_5_new['sec_5_new_arrears'] = '';
			$row_5_new['sec_5_new_remain_amt'] = '';
			$row_5_new['sec_5_new_month_cur'] = '';
			$row_5_new['sec_5_new_gradual_recovery'] = '';
			$row_5_new['sec_5_new_deposit_center'] = '';
			$row_5_new['sec_5_new_deposit_amt'] = '';
			$row_5_new['sec_5_new_reconciled_dcb'] = '';
		$row_8['sec_8_internet_connection']= "";
		$row_8['sec_8_internet_connection']= "";
		$row_8['sec_8_internet_bill_paid']= "";
		$row_new_8['sec_new_8_govt_program'] = '';
		$row_new_8['sec_new_8_other_description'] = '';
		$row_new_8['sec_new_8_hw_secure'] = '';
		$row_new_8['sec_new_8_go_live'] = '';
		$row_new_8['sec_new_8_balance_sheet'] = '';
		$row_new_8['sec_new_8_day_end'] = '';
		$row_new_8['sec_new_8_fin_year'] = '';
		$row_new_8['sec_new_8_inspection_officer'] = '';
		$row_new_8['sec_new_8_inspection_date'] = '';
		$row_new_8['sec_new_8_last_inspection_date'] = '';
		$row_new_8['sec_new_8_remarks'] = '';
		$row_new_8['sec_new_8_compliance'] = '';
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
		
		$row_8['sec_8_internet_outstanding_amount'] = '';
		$row_8['sec_8_solar_outstanding_amount'] = '';
		
		$row_sec_6_2['sec_6_2_mgt_committee_is_elected'] = '';
		$row_sec_6_2['sec_6_2_election_year'] = '';
		$row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = '';
		
		$row_3_5_1['sec_6_godown_insurance'] = '';
		$row_3_5_1['sec_6_tree'] = '';
		$row_3_5_1['sec_6_illegal_possession'] = '';
		$row_3_5_1['sec_6_if_yes_6'] = '';
		$row_3_5_1['sec_6_other_remarks'] = '';
		
		
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
		$row_2_1['sec_6_paved_road_road'] = '';
		
		
		$row_5['sec_5_built_building'] = "";
		$row_5['sec_5_detailed_information'] = "";
		$row_5['sec_6_a_length'] = "";
		$row_5['sec_6_a_width'] = "";
		$row_5['sec_6_b_length'] = "";
		$row_5['sec_6_b_width'] = "";
		$row_5['sec_6_c_length'] = "";
		$row_5['sec_6_c_width'] = "";
		$row_5['sec_6_d_width'] = "";
		$row_5['sec_6_d_length'] = "";
		$row_5['sec_6_d_width'] = "";
		$row_5['sec_6_e_floor'] = "";
		$row_5['sec_6_e_plaster'] = "";
		$row_5['sec_6_e_ceiling'] = "";
		$row_5['sec_6_e_seat'] = "";
		$row_5['sec_6_e_plumbing'] = "";
		$row_5['sec_6_f_number_of_door'] = "";
		$row_5['sec_6_g_number_of_window'] = "";
		$row_5['sec_6_h_length'] = "";
		$row_5['sec_6_h_width'] = "";
		$row_5['sec_6_i_other'] = "";
		$row_5['sec_6_a_img'] = '';
		$row_5['sec_6_b_img'] = '';
		$row_5['sec_6_c_img'] = '';
		$row_5['sec_6_d_img'] = '';
		
		
		$row_3_1['number_of_sides'] = '';
		$row_3_1['total_area'] = '';
		$row_3_1['govt_records'] = '';
		$row_3_1['gata_no'] = '';
		$row_3_1['new_photo_id'] = '';
		$row_3_1['east_side'] = '';
		$row_3_1['west_side'] = '';
		$row_3_1['south_side'] = '';
		$row_3_1['north_side'] = '';
		$row_3_1['on_road_land'] = '';
		$row_3_1['front_side'] = '';
		$row_3_1['remarks'] = '';
		$row_3_1['sec_3_ownership'] = '';
		$row_3_1['sec_3_d_boundry'] = '';
		$row_3_1['sec_3_d_main_gate'] = '';
		
		$row_3_6['sec_3_6_type_of_construction'] = '';
		$row_3_6['sec_3_6_rent'] = '';
		$row_3_6['sec_3_6_area'] = '';
		
		$i = 1;
		$row_7_6['count'] = 1;
		$row_7_6['sec_3_b_storage_scheme_length_' . $i] = '';
		$row_7_6['sec_3_b_storage_scheme_width_' . $i] = '';
		$row_7_6['sec_3_b_storage_scheme_capacity_' . $i] = '';
		$row_7_6['sec_3_b_storage_scheme_status_' . $i] = '';
		$row_7_6['sec_3_b_storage_scheme_comment_' . $i] = '';
		$i = 1;
			$row_3_4['count'] = 1;
			$row_3_4['sec_3_b_godown_length_' . $i] = '';
			$row_3_4['sec_3_b_godown_width_' . $i] = '';
			$row_3_4['sec_3_b_storage_capacity_' . $i] = '';
			$row_3_4['sec_3_b_godown_status_' . $i] = '';
			$row_3_4['sec_3_b_godown_type_of_fund_' . $i] = '';
			$row_3_4['sec_3_b_comment_' . $i] = '';
		
		
		$row_3_3['count'] = 1;
			$row_3_3['sec_3_b_type_of_construction_' . $i] = '';
			$row_3_3['sec_3_b_type_of_fund_' . $i] = '';
			$row_3_3['sec_3_b_length_' . $i] = '';
			$row_3_3['sec_3_b_width_' . $i] = '';
			$row_3_3['sec_3_b_comment_' . $i] = '';
		
		$row_val=10;
		$c=1;
		while ($c<=$row_val) {
			$row_sec_6_1['sec_6_1_condition_'.$c]  = '';
			$row_sec_6_1['sec_6_1_name_'.$c]  = '';
			$row_sec_6_1['sec_6_1_father_name_'.$c]  = '';
			$row_sec_6_1['sec_6_1_address_'.$c]  = '';
			$row_sec_6_1['sec_6_1_birth_date_'.$c]  = '';
			$row_sec_6_1['sec_6_1_education_qualification_'.$c]  = '';
			$row_sec_6_1['sec_6_1_computer_qualification_'.$c]  = '';
			$row_sec_6_1['sec_6_1_approval_level_'.$c]  = '';
			$row_sec_6_1['sec_6_1_appointment_date_'.$c]  = '';
			$row_sec_6_1['sec_6_1_mgt_committee_resolution_number_date_'.$c]  = '';
			$row_sec_6_1['sec_6_1_employee_type_'.$c]  = '';
			$row_sec_6_1['sec_6_1_source_emp_'.$c]  = '';
			$c++;
		}
		
		$row_3_new_2_dap = [
				'sec_3_dap_opening' => '',
				'sec_3_new_2_dap_1' => '',
				'sec_3_new_2_dap_2' => '',
				'sec_3_new_2_dap_3' => '',
				'sec_3_new_2_dap_4' => '',
				'sec_3_new_2_dap_5' => '',
				'sec_3_new_2_dap_6' => ''
			];
		$row_3_msp['sec_new_3_msp'] = '';
			$row_3_msp['sec_new_3_agency_name_kharif'] = '';
			$row_3_msp['sec_new_3_kharif_crops'] = '';
			$row_3_msp['sec_new_3_crop_name_kharif'] = '';
			$row_3_msp['sec_new_3_quantity_kharif'] = '';
			$row_3_msp['sec_new_3_amt_kharif'] = '';
			$row_3_msp['sec_new_3_commission_pay_kharif'] = '';
			$row_3_msp['sec_new_3_commission_rec_kharif'] = '';

			$row_3_msp['sec_new_3_agency_name_rabi'] = '';
			$row_3_msp['sec_new_3_rabi_crops'] = '';
			$row_3_msp['sec_new_3_crop_name_rabi'] = '';
			$row_3_msp['sec_new_3_quantity_rabi'] = '';
			$row_3_msp['sec_new_3_amt_rabi'] = '';
			$row_3_msp['sec_new_3_commission_pay_rabi'] = '';
			$row_3_msp['sec_new_3_commission_rec_rabi'] = '';
		
		$row_3_loan_limit['sec_new_3_ten_lakh_loan_limit'] = '';
			$row_3_loan_limit['sec_new_3_gl_code'] = '';
			$row_3_loan_limit['sec_new_3_gl_code'] = '';
			$row_3_loan_limit['sec_new_3_acc_num'] = '';
			$row_3_loan_limit['sec_new_3_open_bal_1'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt_1'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt_1'] = '';
			$row_3_loan_limit['sec_new_3_num_of_transactions_1'] = '';
			$row_3_loan_limit['sec_new_3_open_bal'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt'] = '';
			$row_3_loan_limit['sec_new_3_num_of_transactions'] = '';

			$row_3_loan_limit['sec_new_3_loan_other'] = '';
			$row_3_loan_limit['sec_new_3_gl_code_other_1'] = '';
			$row_3_loan_limit['sec_new_3_acc_num_other_1'] = '';
			$row_3_loan_limit['sec_new_3_open_bal_other_1'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt_other_1'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt_other_1'] = '';
			$row_3_loan_limit['sec_new_3_transaction_other_1'] = '';
			$row_3_loan_limit['sec_new_3_open_bal_other'] = '';
			$row_3_loan_limit['sec_new_3_debit_amt_cur'] = '';
			$row_3_loan_limit['sec_new_3_credit_amt_cur'] = '';
			$row_3_loan_limit['sec_new_3_num_of_transaction_cur'] = '';
		
		$row_3_new_2_other = [
				'sec_3_new_2_other_opening' => '',
				'sec_3_new_2_other_1' => '',
				'sec_3_new_2_other_2' => '',
				'sec_3_new_2_other_3' => '',
				'sec_3_new_2_other_4' => '',
				'sec_3_new_2_other_5' => '',
				'sec_3_new_2_other_6' => ''
			];
		
		$row_3_new_2_pesticide = [
				'sec_3_pesticide_opening' => '',
				'sec_3_new_2_pesticide_1' => '',
				'sec_3_new_2_pesticide_2' => '',
				'sec_3_new_2_pesticide_3' => '',
				'sec_3_new_2_pesticide_4' => '',
				'sec_3_new_2_pesticide_5' => '',
				'sec_3_new_2_pesticide_6' => ''
			];
		
		$row_3_new_2_npk = [
				'sec_3_npk_opening' => '',
				'sec_3_new_2_npk_1' => '',
				'sec_3_new_2_npk_2' => '',
				'sec_3_new_2_npk_3' => '',
				'sec_3_new_2_npk_4' => '',
				'sec_3_new_2_npk_5' => '',
				'sec_3_new_2_npk_6' => ''
			];
	}
	
	
	
}else{
	
	
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
	.step h4{
		color: #FFFFFF;
		background: #FF8E00;
		border-radius: 15px;
		padding: 10px 10px 6px 20px;
	}
	
	.step h5{
		color: #000000;
		background:#FFDB44;
		border-radius: 15px;
		padding: 10px 10px 6px 20px;
	}
</style>
<style>
	.select-default {
		background-color: white;
	}
</style>

<style>
.danger{border:2px solid #f00; background-color:#f00; text-color:white;}
.success{border:3px solid #0f0;}

</style>

<?php
page_header_end();
page_sidebar();

?>

<?php
if($response==1){
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
					<form action="scripts/bm_form_ajax.php" method="post" enctype="multipart/form-data" id="user_form"
										name="user_form">
										<div id="steps-container">
											<!-------------------1st start--------------------------------------------------------------------->
								<!-- <div class="step"> -->
									<marquee style="font-size: 18px; color: red;">
										नोट: समस्त विवरण ADO अथवा (ADO विकास खंड के तैनात न होने पर ADCO द्वारा) प्रत्येक माह की पांच (5) तारीख तक भरना अनिवार्य है, जिसके उपरांत AR अथवा CEO द्वारा परीक्षण कर 10 तारीख तक अनुमोदन करना आवश्यक है।
									</marquee><br><br>
									<?php echo $msg; ?>
										
									<h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon" style="height:45px; width:45px;"> 1. समिति का विवरण </h4>
									<div class="col-sm-12">
										<div class="row">
											<div class="col-md-4">
												<div class="row">
													<div class="col-sm-4">
														<h6>समिति का नाम : </h6>
													</div>
													<div class="col-sm-8">
														बी-पैक्स
														<?php echo $row_invoice['society_name']; ?>
														
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<h6>मण्डल : </h6>
													</div>
													<div class="col-md-8">
														<?php echo $row_invoice['division_name']; ?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<h6>जिला : </h6>
													</div>
													<div class="col-md-8">
														<?php echo $row_invoice['district_name']; ?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<h6>तहसील : </h6>
													</div>
													<div class="col-md-8">
														<?php echo $row_invoice['tehseel_name']; ?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<h6>ब्लाक : </h6>
													</div>
													<div class="col-md-8">
														<?php echo $row_invoice['block_name']; ?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<h6>समिति का प्रकार : </h6>
													</div>
													<div class="col-md-8">
														<?php echo $row_invoice['type_name']; ?>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<h6>मोबाइल नंबर : </h6>
													</div>
													<div class="col-md-8">
														<?php echo $row_invoice['mobile_number']; ?>
													</div>
												</div>
											</div>
											<div class="col-md-8">
												<input type="hidden" id="society_code" name="society_code"value="<?php echo $row_invoice['society_id']; ?>">
												<input type="hidden" id="mobile_number" name="mobile_number"value="<?php echo $row_invoice['mobile_number']; ?>">
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
															onClick="getLocation();">लोकेशन रिफ्रेश करें</button>
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
										
								<!-- </div> -->

											<!------ 3td start ------->
								<!-- <div class="step"> -->
									<h4><img src="images/logo/3.png" alt="text" class="img-fluid stat-icon" style="height:50px; width:50px;"> 3. वित्तीय सूचना</h4>
									<div class="col-sm-12">
										

										<!-- <h4> <img src="images/logo/5.png" alt="text" class="img-fluid stat-icon" style="height:50px; width:50px;"> 3.2. कार्य व व्यवसाय</h4>										 -->
										
										
										
										<!-- <h6> 10 लाख तक की ब्याज मुक्त ऋण सीमा का विवरण</h6> -->
										<div class="row">
											<div class="col-sm-3 form-group">
												<label><b>(V)क्या 10 लाख तक की ब्याज मुक्त ऋण सीमा स्वीकृत है ?</b></label>
												<select disabled class="form-control" id="ten_lakh_loan_limit_approved"
													name="sec_new_3_ten_lakh_loan_limit" tabindex="<?php echo $tab++; ?>"
													onChange="hide_show(this.value, '#gl_code_section', 'yes'); hide_show(this.value, '#acc_num_section', 'yes');hide_show(this.value, '#sec_1_acc_num', 'yes');hide_show(this.value, '#sec_1_open_bal', 'yes');hide_show(this.value, '#sec_2_open_bal', 'yes');hide_show(this.value, '#sec_3_open_bal', 'yes');hide_show(this.value, '#sec_4_open_bal', 'yes');hide_show(this.value, '#sec_5_open_bal', 'yes');hide_show(this.value, '#sec_6_open_bal', 'yes'); hide_show(this.value, '#sec_7_open_bal', 'yes'); hide_show(this.value, '#sec_8_open_bal', 'yes');  handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
													<option value="">--Select--</option>
													<option value="yes" <?php echo isset($row_3_loan_limit['sec_new_3_ten_lakh_loan_limit']) && $row_3_loan_limit['sec_new_3_ten_lakh_loan_limit'] == 'yes' ? 'selected="selected"' : '' ?> style="background:#0f0">हाँ</option>
													<option value="no" <?php echo isset($row_3_loan_limit['sec_new_3_ten_lakh_loan_limit']) && $row_3_loan_limit['sec_new_3_ten_lakh_loan_limit'] == 'no' ? 'selected="selected"' : '' ?> style="background:#f00">नहीं</option>
												</select>
											</div>
											<?php
											 $displayStyle_sec_new_3_ten_lakh_loan_limit = $row_3_loan_limit['sec_new_3_ten_lakh_loan_limit'] == "yes" ? "display: block;" : "display: none;";
											 
											?>
											
											<div class="col-sm-3 form-group" id="gl_code_section"
												style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
												<label>जी० एल० कोड अंकित करे</label>
												<input disabled type="text" name="sec_new_3_gl_code" id="sec_1_gl_code"
													tabindex="<?php echo $tab++; ?>" class="form-control"
													value="<?php echo $row_3_loan_limit['sec_new_3_gl_code'] ; ?>">
											</div>
											<div class="col-sm-3 form-group" id="acc_num_section"
												style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
												<label>खाता संख्या अंकित करे</label>
												<input disabled type="text" name="sec_new_3_acc_num" id="sec_1_acc_num"
													tabindex="<?php echo $tab++; ?>" 
													class="form-control chk_number" data-type="खाता संख्या अंकित करे को अंक मे भरे " 
													value="<?php echo $row_3_loan_limit['sec_new_3_acc_num']; ?>">
											</div>
										</div>
										
										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-3 form-group" id="sec_1_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> माह</label>
													<input disabled type="text" name="sec_new_3_open_bal_1" id="sec_1_open_bal"
														tabindex="<?php echo $tab++; ?>" readonly class="form-control"
														value="01-अप्रैल-2024 से 30-अक्टूबर-2024">
												</div>
												<div class="col-sm-3 form-group" id="sec_2_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> डेबिट धनराशि (लाख मे)</label>
													<input disabled type="text" name="sec_new_3_debit_amt_1" id="sec_1_debit_money"
														tabindex="<?php echo $tab++; ?>" 
														class="form-control chk_decimal" data-type="डेबिट धनराशि (को लाख मे भरे)" 
														value="<?php echo $row_3_loan_limit['sec_new_3_debit_amt_1']; ?>">
												</div>
												<div class="col-sm-3 form-group" id="sec_3_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> क्रेडिट धनराशि (लाख मे)</label>
													<input disabled type="text" name="sec_new_3_credit_amt_1" id="sec_1_credit_money"
														tabindex="<?php echo $tab++; ?>" 
														class="form-control chk_decimal" data-type="क्रेडिट धनराशि (को लाख मे भरे)" 
														value="<?php echo $row_3_loan_limit['sec_new_3_credit_amt_1'] ?? '' ?>">
												</div>
												<div class="col-sm-3 form-group" id="sec_7_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> ट्रांजैक्शन की संख्या</label>
													<input disabled type="text" name="sec_new_3_num_of_transactions_1" id="sec_1_transaction_no"
														tabindex="<?php echo $tab++; ?>" 
														class="form-control chk_number" data-type="ट्रांजैक्शन की संख्या को अंक मे भरे" 
														value="<?php echo $row_3_loan_limit['sec_new_3_num_of_transactions_1'] ?? '' ?>">
												</div>
											</div>
										</div>
										<div class="col-sm-12">
											<div class="row">
												<div class="col-sm-3 form-group" id="sec_4_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> माह</label>
													<input disabled type="text" name="sec_new_3_open_bal" id="sec_4_open_bal"
														tabindex="<?php echo $tab++; ?>" readonly class="form-control"
														value="नवम्बर-2024">
												</div>
												<div class="col-sm-3 form-group" id="sec_5_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> डेबिट धनराशि (लाख मे)</label>
													<input disabled type="text" name="sec_new_3_debit_amt" id="sec_5_open_bal"
														tabindex="<?php echo $tab++; ?>" 
														class="form-control chk_decimal" data-type="डेबिट धनराशि (को लाख मे भरे)" 
														value="<?php echo $row_3_loan_limit['sec_new_3_debit_amt'] ?? '' ?>">
												</div>
												<div class="col-sm-3 form-group" id="sec_6_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> क्रेडिट धनराशि (लाख मे)</label>
													<input disabled type="text" name="sec_new_3_credit_amt" id="sec_6_open_bal"
														tabindex="<?php echo $tab++; ?>" 
														class="form-control chk_decimal" data-type="क्रेडिट धनराशि (को लाख मे भरे)" 
														value="<?php echo $row_3_loan_limit['sec_new_3_credit_amt']; ?>">
												</div>
												<div class="col-sm-3 form-group" id="sec_8_open_bal"
													style="<?php echo $displayStyle_sec_new_3_ten_lakh_loan_limit; ?>">
													<label> ट्रांजैक्शन की संख्या</label>
													<input disabled type="text" name="sec_new_3_num_of_transactions" id="sec_1_transaction_no_3"
														tabindex="<?php echo $tab++; ?>" 
														class="form-control chk_number" data-type="ट्रांजैक्शन की संख्या को अंक मे भरे" 
														value="<?php echo $row_3_loan_limit['sec_new_3_num_of_transactions'];?>">
												</div>
											</div>
										</div>
										<div class="row">
												<div class="col-sm-3 form-group">
													<label><b>(V) क्या 10 लाख तक की ब्याज मुक्त कैश एण्ड कैरी उर्वरक ऋण सीमा
															के अतिरिक्त ऋण सीमा स्वीकृत है ?</b></label>
													<select disabled class="form-control" id="loan_limit_approved_other"
														name="sec_new_3_loan_other" tabindex="<?php echo $tab++; ?>"
														onchange="hide_show(this.value, '#sec_1_gl_code_other', 'yes');hide_show(this.value, '#sec_1_acc_num_other', 'yes');hide_show(this.value, '#sec_1_1', 'yes');hide_show(this.value, '#sec_1_2', 'yes');hide_show(this.value, '#sec_1_3', 'yes');hide_show(this.value, '#sec_1_4', 'yes');hide_show(this.value, '#sec_1_5', 'yes');hide_show(this.value, '#sec_1_6', 'yes');hide_show(this.value, '#sec_1_7', 'yes');hide_show(this.value, '#sec_1_8', 'yes'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
														<option value="">--Select--</option>

														<option value="yes" <?php echo isset($row_3_loan_limit['sec_new_3_loan_other']) && $row_3_loan_limit['sec_new_3_loan_other'] == 'yes' ? 'selected="selected"' : '' ?> style="background:#0f0">हाँ
														</option>
														<option value="no" <?php echo isset($row_3_loan_limit['sec_new_3_loan_other']) && $row_3_loan_limit['sec_new_3_loan_other'] == 'no' ? 'selected="selected"' : '' ?> style="background:#f00">नहीं
														</option>

													</select>
												</div>

												<?php
												$displayStyle_sec_new_3_loan_other = $row_3_loan_limit['sec_new_3_loan_other'] == "yes" ? "display: block;" : "display: none;";

												?>
												<div class="col-sm-3 form-group" id="sec_1_gl_code_other"
													style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
													<label>जी० एल० कोड अंकित करे</label>
													<input disabled type="text" name="sec_new_3_gl_code_other_1"
														id="sec_1_gl_code_other" tabindex="<?php echo $tab++; ?>"
														class="form-control"
														value="<?php echo $row_3_loan_limit['sec_new_3_gl_code_other_1']; ?>">
												</div>
												<div class="col-sm-3 form-group" id="sec_1_acc_num_other"
													style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
													<label>खाता संख्या अंकित करे</label>
													<input disabled type="text" name="sec_new_3_acc_num_other_1"
														id="sec_1_acc_num_other" tabindex="<?php echo $tab++; ?>"
														class="form-control chk_number"
														data-type="खाता संख्या को अंक मे भरे"
														value="<?php echo $row_3_loan_limit['sec_new_3_acc_num_other_1']; ?>">
												</div>
											</div>
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-3 form-group" id="sec_1_1"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> माह</label>
														<input disabled type="text" name="sec_new_3_open_bal_other_1"
															id="sec_1_open_bal" tabindex="<?php echo $tab++; ?>" readonly
															class="form-control" value="01-अप्रैल-2024 से 30-अक्टूबर-2024">
													</div>
													<div class="col-sm-3 form-group" id="sec_1_2"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> डेबिट धनराशि (रु० लाख मे)</label>
														<input disabled type="text" name="sec_new_3_debit_amt_other_1"
															id="sec_1_debit_money" tabindex="<?php echo $tab++; ?>"
															class="form-control chk_decimal"
															data-type="डेबिट धनराशि (को रु० लाख मे भरे)"
															value="<?php echo $row_3_loan_limit['sec_new_3_debit_amt_other_1']; ?>">
													</div>
													<div class="col-sm-3 form-group" id="sec_1_3"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> क्रेडिट धनराशि (रु० लाख मे)</label>
														<input disabled type="text" name="sec_new_3_credit_amt_other_1"
															id="sec_1_credit_money" tabindex="<?php echo $tab++; ?>"
															class="form-control chk_decimal"
															data-type="क्रेडिट धनराशि (को रु० लाख मे भरे)"
															value="<?php echo $row_3_loan_limit['sec_new_3_credit_amt_other_1']; ?>">
													</div>
													<div class="col-sm-3 form-group" id="sec_1_7"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> ट्रांजैक्शन की संख्या</label>
														<input disabled type="text" name="sec_new_3_transaction_other_1"
															id="sec_1_transaction_no" tabindex="<?php echo $tab++; ?>"
															class="form-control chk_number"
															data-type="ट्रांजैक्शन की संख्या को अंक मे भरे"
															value="<?php echo $row_3_loan_limit['sec_new_3_transaction_other_1']; ?>">
													</div>
												</div>
											</div>
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-3 form-group" id="sec_1_4"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> माह</label>
														<input disabled type="text" name="sec_new_3_open_bal_other"
															id="sec_4_open_bal" tabindex="<?php echo $tab++; ?>" readonly
															class="form-control" value="नवम्बर-2024">
													</div>
													<div class="col-sm-3 form-group" id="sec_1_5"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> डेबिट धनराशि (रु० लाख मे)</label>
														<input disabled type="text" name="sec_new_3_debit_amt_cur"
															id="sec_5_open_bal" tabindex="<?php echo $tab++; ?>"
															class="form-control chk_decimal"
															data-type="Eडेबिट धनराशि (को रु० लाख मे भरे)"
															value="<?php echo $row_3_loan_limit['sec_new_3_debit_amt_cur']; ?>">
													</div>
													<div class="col-sm-3 form-group" id="sec_1_6"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> क्रेडिट धनराशि (रु० लाख मे)</label>
														<input disabled type="text" name="sec_new_3_credit_amt_cur"
															id="sec_6_open_bal" tabindex="<?php echo $tab++; ?>"
															class="form-control chk_decimal"
															data-type="क्रेडिट धनराशि (को रु० लाख मे भरे)"
															value="<?php echo $row_3_loan_limit['sec_new_3_credit_amt_cur']; ?>">
													</div>
													<div class="col-sm-3 form-group" id="sec_1_8"
														style="<?php echo $displayStyle_sec_new_3_loan_other; ?>">
														<label> ट्रांजैक्शन की संख्या</label>
														<input disabled type="text" name="sec_new_3_num_of_transaction_cur"
															id="sec_1_transaction_no1" tabindex="<?php echo $tab++; ?>"
															class="form-control chk_number"
															data-type="ट्रांजैक्शन की संख्या को अंक मे भरे"
															value="<?php echo $row_3_loan_limit['sec_new_3_num_of_transaction_cur']; ?>">
													</div>
												</div>
											</div>
										</div>
								<!-- </div> -->
								<!-------4th start------->
								
								
								<div id="success">
									<div class="mt-5 text-center">
										<h4>प्रपत्र सफलता पूर्वक भरा गया</h4>
										<p>आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
											सत्यापन हेतु भेजा जाना है । कृप्या सत्यापन पर भेजने से पहले नीचे दर्शायें
											लिंक से फार्म खोल कर पुनः जाच कर लें । सब कुछ सही होने कि दशा में नीचे दिये
											बटन के माध्यम से सत्यापन के लिये आगे प्रेषित करें ।</p>
										<button class="btn btn-info"
											onclick="window.open('preview.php','_blank');">प्रपत्र पुनः निरीक्षण के लिये
											देखे</button>
									</div>
									<div class="col-md-12 text-center">
										<p><input disabled type="checkbox" style="height: 20px; border:1px solid;"
												id="review_ack"
												onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
											मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
										<button type="button" class="btn btn-danger" onClick="form_validate()"
											id="verification_button" disabled="disabled">सत्यापन के लिये आगे प्रेषित करें
										</button>
										
										
									</div>

									<div class="col-sm-12 form-group my-auto" id="send_otp_button2" style="display: none">
										<button type="button" name="verify_otp_btn" id="verify_otp_btn"
											tabindex="<?php echo $tab++; ?>" class="btn btn-info"
											onClick="bm_verify_otp($('#survey_id').val());">आगे प्रेषित करे 
										</button>
									</div>
								</div>
							</div>

							<!-- <div id="q-box__buttons">
								
									<button id="submit-btn" class="btn btn-danger" type="submit"
									onClick="validate_input(); save_draft();">Submit</button>
								
							</div> -->
							
							<!-- <button class="btn btn-warning" type="button" onClick="save_draft()"><i
									class="fas fa-save"></i> Save Draft</button> -->
							<input type="hidden" id="term" name="term" value="a">
							<input type="hidden" id="latitude" name="latitude"
								value="<?php echo $row_invoice['latitude']; ?>">
							<input type="hidden" id="longitude" name="longitude"
								value="<?php echo $row_invoice['longitude']; ?>">
							<input type="hidden" id="id" name="id"  value="submit_form">
							<input type="hidden" id="tt" name="current_step_count" value="6">
							<input type="hidden" id="survey_id" name="survey_id"
								value="<?php echo $row_invoice['sno']; ?>">
							<input type="hidden" id="error_status" name="error_status" value="">
						</form>
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
    document.getElementById('submit-btn').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default form submission behavior

        // Hide the submit button and show the preloader
        document.getElementById('q-box__buttons').style.display = 'none';
        document.getElementById('preloader-wrapper').style.display = 'block';

        // Simulate a delay to show the preloader (e.g., 2 seconds)
        const timer = ms => new Promise(res => setTimeout(res, ms));
        timer(2000).then(() => {
            // Hide the preloader and show the success div
            document.getElementById('preloader-wrapper').style.display = 'none';
            document.getElementById('success').style.display = 'block';
        });
    });
</script>
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
					}else if(value.id != 'Update' &&  value.id != 'update' ){
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


	$('select[multiple]').multiselect({
		columns: 1,
		placeholder: 'Select options'
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
    } else {
        if (value === showValue) {
            testServicesContainer.style.display = 'block';
        } else {
            testServicesContainer.style.display = 'none';
        }
    }
}
	window.onload = function() {
		var dropdown = document.getElementById('sec_3_select_loan');
		var selectedValue = dropdown ? dropdown.value : '';
		loanSelection(selectedValue);
		if (dropdown) {
			dropdown.addEventListener('change', function() {
				loanSelection(this.value);
			});
		}
	};

</script>
<script language="javascript" type="text/javascript">

	function validate_input(){
		//var regexp_text = /^[A-Za-z ]+$/u;
		var regexp_text = /^[\p{Letter}\u0900-\u097F ]+$/u;
		var regexp_spltext = /^[\p{Letter}\u0900-\u097F -,./]+$/u;
		var regexp_number = /^\d+$/;
		var regexp_decimal = /^-?\d+(\.\d+)?$/;
		var regexp_email = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/; 
		var error_status=0;
		var error_msg = '';
		
		$(".chk_text").each(function() {
			var value_text = $(this).val();
	/*console.log(this.value + '>>' + $(this).data("type") + '>>' + regexp_text.test(this.value));*/
			if (value_text != "") {
				if (!regexp_text.test(this.value)) {
					$(this).addClass("danger"); 
					$(this).removeClass("success"); 
					error_msg += $(this).data("type")+"\n"; 
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger"); 
				}
			}
			else{
				$(this).removeClass("danger"); 
				$(this).removeClass("success"); 
			}
		});
		
		$(".chk_spltext").each(function() {
			var value_text = $(this).val();
	/*console.log(this.value + '>>' + $(this).data("type") + '>>' + regexp_text.test(this.value));*/
			if (value_text != "") {
				if (!regexp_spltext.test(this.value)) {
					$(this).addClass("danger"); 
					$(this).removeClass("success"); 
					error_msg += $(this).data("type")+"\n"; 
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger"); 
				}
			}
			else{
				$(this).removeClass("danger"); 
				$(this).removeClass("success"); 
			}
		});
		
		
		$(".chk_number").each(function() {
			
			
			var value_number = $(this).val();
	/*console.log(this.value+\'>>\'+$(this).data("type")+\'>>\'+regexp_number.test(this.value));*/
			if (value_number != "") {
				var minlength = $(this).data("minlength");
				if($(this).val().length<minlength){
					error_msg += $(this).data("type")+". न्यूनतम "+minlength+" शब्द भरें । \n"; 
					$(this).addClass("danger");
					$(this).removeClass("success");

					error_status = 1;
				}
				else{

					if(!regexp_number.test(this.value)){
						$(this).addClass("danger");
						$(this).removeClass("success");
						error_msg += $(this).data("type")+"\n"; 
						error_status = 1;
					}
					else{

						$(this).addClass("success");
						$(this).removeClass("danger");
					}
				}
			}
			else{
				$(this).removeClass("danger"); 
				$(this).removeClass("success"); 
			}
		});
		$(".chk_decimal").each(function() {
			var value_decimal = $(this).val().trim();
	/*console.log(this.value + '>>' + $(this).data("type") + '>>' + regexp_decimal.test(this.value));*/
			if (value_decimal != "") {
				if(!regexp_decimal.test(this.value)){
					
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_msg += $(this).data("type")+"\n"; 
					error_status = 1;
				}
				else{
					
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			}
			else{
				$(this).removeClass("danger"); 
				$(this).removeClass("success"); 
			}
		});
		$(".chk_email").each(function() {
			var value_email = $(this).val();
	/*console.log(this.value + '>>' + $(this).data("type") + '>>' + regexp_email.test(this.value))*/

			if (value_email != "") {
				if(!regexp_email.test(this.value)){
					
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_msg += $(this).data("type")+"\n"; 
					error_status = 1;
				}
				else{
					
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			}
			else{
				$(this).removeClass("danger"); 
				$(this).removeClass("success"); 
			}
		});
		$("#error_status").val(error_status);
		/*console.log("error_status");*/
		if(error_msg!=""){
			alert(error_msg);
			// exit(error_msg);
		}
	}
	</script>


<script type="text/javascript" src="js/multistepform.js?v=1">
	<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	< script src="js/light-bootstrap-dashboard.js?v=1.4.0">
</script>

<?php
}else{
echo $msg;
}
?>


<?php
page_footer_start();
page_footer_end();
?>