<?php
date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");
//print_r($_POST);
$q = htmlspecialchars(urldecode(strtoupper($_REQUEST["term"])), ENT_QUOTES);
if (!$q) return;

if(isset($_REQUEST['id'])){
	$id = $_REQUEST['id'];
}
else {
	$id='';
}

$data = array();


if($id=='form_type'){
	
	$sql = 'select * from form_sub_type where form_type_id="'.$_POST['val'].'"';
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['id'], "sub_form_name"=>$row['sub_form_name']);
	}
}
elseif($id=='dist'){
	
	if($_SESSION['user_type']=='ar'){
	$sql = 'select * from master_district where  sno in (' . implode(",", $_SESSION['district_id']) . ') and  division_id="'.$_POST['val'].'"';
	}else{
		$sql = 'select * from master_district where division_id="'.$_POST['val'].'"';
	}
	// $sql = 'select * from master_district where division_id="'.$_POST['val'].'"';
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['sno'], "district_name"=>$row['district_name']);
	}
}
elseif($id=='tehseel'){
	$sql = 'select * from master_tehseel where district_id="'.$_POST['val'].'"';
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['sno'], "tehseel_name"=>$row['tehseel_name']);
	}
}
elseif($id=='block'){
	$sql = 'select * from master_block where tehseel_id="'.$_POST['val'].'"';
	//echo $sql;
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['sno'], "block_name"=>$row['block_name']);
	}
}
elseif($id=='type'){
	$sql = 'select * from master_society_type';
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['sno'], "type_name"=>$row['type_name']);
	}
}
elseif($id=='society'){
	$sql = 'select * from test2 where col1="'.$_POST['division'].'" and col2="'.$_POST['district'].'" and col5="'.$_POST['tehseel'].'" and col6="'.$_POST['block'].'" and col3="'.$_POST['val'].'"';
	//echo $sql;
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['sno'], "society_name"=>$row['col4']);
	}
} elseif ($id == 'maintenance_society') {
	$type = (int) $_POST['type'];
	$dist = (int) $_POST['district'];
	$sql = '';

	if ($type == 1) {
		$sql = 'SELECT sno as id, samiti_naam as society_name FROM block_union WHERE janpad_name = "' . $dist . '" AND is_deleted = 0 ORDER BY samiti_naam';
	} elseif ($type == 2) {
		$sql = 'SELECT sno as id, society_name FROM marketing WHERE district_id = "' . $dist . '" AND is_deleted = 0 ORDER BY society_name';
	} elseif ($type == 3) {
		$sql = 'SELECT sno as id, society_name FROM upss WHERE janpad_name = "' . $dist . '" AND is_deleted = 0 ORDER BY society_name';
	} elseif ($type == 4) {
		$sql = 'SELECT sno as id, society_name FROM jila_sehkari WHERE janpad_name = "' . $dist . '" AND is_deleted = 0 ORDER BY society_name';
	} elseif ($type == 5) {
		$sql = 'SELECT sno as id, col4 as society_name FROM test2 WHERE col2 = "' . $dist . '" AND col3 = 80 ORDER BY col4';
	}

	if ($sql != '') {
		$result = execute_query($sql);
		while ($row = mysqli_fetch_assoc($result)) {
			$data[] = array("id" => $row['id'], "society_name" => $row['society_name']);
		}
	}
} elseif($id=='submit_form'){
	
	//print_r($_POST);
	//print_r($_SERVER);
	if($_POST['survey_id']==''){
		$sql = 'INSERT INTO `survey_invoice` (`society_id`, `latitude`, `longitude`, `mobile_number`, `otp_verify`, `ip_address`, `device_details`, `mac_address`, `operating_system`, `http_referer`, `http_user_agent`, `approval_status`, `status`, `credited_by`, `creation_time`) VALUES ("'.$_POST['society_name'].'", "'.$_POST['latitude'].'", "'.$_POST['longitude'].'", "'.$_POST['mobile_number'].'", "OTP", "'.$_SERVER['REMOTE_ADDR'].'", "", "", "", "'.$_SERVER['HTTP_REFERER'].'", "'.$_SERVER['HTTP_USER_AGENT'].'", "0", "", "", "'.date("Y-m-d H:i:s").'")';
		execute_query($sql);
		if(mysqli_error($db)){
			$data[] = array("id"=>"error", "error"=>"Error# ".mysqli_error($db).' >> '.$sql);
		}
		else{
			$id = mysqli_insert_id($db);
			$data[] = array("id"=>$id);
		}
		
	}
	else{
		$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
		$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
		
		$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
		$society = mysqli_fetch_assoc(execute_query($sql));
		
		switch($_POST['current_step_count']){
			case 0:{
				if($_FILES['society_photo']['name']!=''){
					$society_image = upload_img($_FILES['society_photo'], $society, "society_name_".$survey_invoice['sno']);
					//print_r($society_image);
					if($society_image['error']==1){
						$sql = 'update survey_invoice set 
						photo_id="'.$society_image['file_name'].'"
						where sno="'.$_POST['survey_id'].'"';
						execute_query($sql);
						$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$society_image['msg']);
					}
				}
				$sql_update = 'update survey_invoice set 
				latitude="'.$_POST['latitude'].'",
				longitude="'.$_POST['longitude'].'",
				liquidation = "'.$_POST['sec1_liquidation'].'",
				litigation = "'.$_POST['sec_1_litigation'].'",
				society_registration_no = "'.$_POST['society_registration_no'].'",
				society_registration_date = "'.$_POST['society_registration_date'].'",
				email_id = "'.$_POST['sec1_email'].'",
				respondent_name = "'.$_POST['person_name'].'",
				respondent_designation = "'.$_POST['person_designation'].'",
				respondent_aadhaar = "'.$_POST['person_aadhaar'].'",
				active_members = "'.$_POST['sec_1_members_active'].'",
				inactive_members = "'.$_POST['sec_1_members_non_active'].'",
				others = "'.$_POST['sec_1_others'].'"
				where sno='.$_POST['survey_id'];
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"1.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"1.Data Saved");	
				}	
				break;
			}
			case 1:{
				$sql = 'select * from survey_invoice_sec_2_1 where survey_id="'.$_POST['survey_id'].'"';
				$res_2_1 = execute_query($sql);
				if(mysqli_num_rows($res_2_1)==1){
					$row_2_1 = mysqli_fetch_assoc($res_2_1);
				}
				else{
					$sql = 'insert into survey_invoice_sec_2_1 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					$row_2_1['sno'] = mysqli_insert_id($db);
				}
				$sql_update = 'update survey_invoice_sec_2_1 set 
				investment="'.$_POST['sec_1_investment'].'",
				loan="'.$_POST['sec_1_loan'].'",
				msp="'.$_POST['sec_1_msp'].'",
				msp_comm="'.$_POST['sec_1_msp_comm'].'",
				subscribers="'.$_POST['sec_1_subscriber'].'",
				pds="'.$_POST['sec_1_pds'].'",
				total_business="'.$_POST['sec_1_total_business'].'",
				last_year_profit_loss="'.$_POST['sec_1_profit_loss'].'",
				last_year_pl_amount="'.$_POST['sec_1_profit_loss_amount'].'",
				seq_year_profit_loss="'.$_POST['sec_1_sequentially'].'",
				seq_year_pl_amount="'.$_POST['sec_1_sequentially_amount'].'",
				financial_audit_year="'.$_POST['sec2_financial_audit'].'",
				balance_sheet_year="'.$_POST['sec2_balance_sheet'].'"
				where sno='.$row_2_1['sno'];
				execute_query($sql_update);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"2.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"2.Data Saved");	
				}
				$sql = 'delete from survey_invoice_sec_2_1_2 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"2.1.Unable to save data.");
				}
				for($i=1; $i<=$_POST['other_business_id']; $i++){
					$sql = 'insert into survey_invoice_sec_2_1_2 (survey_id, other_description, other_amount) values ("'.$_POST['survey_id'].'", "'.$_POST['sec_2_1_2_business_description_'.$i].'", "'.$_POST['sec_2_1_2_value_'.$i].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$data[] = array("id"=>"error", "error"=>"2.2.Unable to save data.");
					}

				}
				if($_POST['sec_1_1_2_msc']=='yes'){
					foreach($_POST['sec_1_1_2_msc_service'] as $k=>$v){
						if($v!='other'){
							$sql = 'insert into survey_invoice_sec_2_1_2 (survey_id, other_description, other_amount) values ("'.$_POST['survey_id'].'", "msc", "'.$v.'")';
							execute_query($sql);
							if(mysqli_error($db)){
								$data[] = array("id"=>"error", "error"=>"2.3.Unable to save data.");
							}

						}						
					}
					if(isset($_POST['sec_1_1_2_msc_service_other'])){
						if($_POST['sec_1_1_2_msc_service_other']!=''){
							$sql = 'select * from master_msc_services where msc_service="'.$_POST['sec_1_1_2_msc_service_other'].'"';
							$res_tmp = execute_query($sql);
							if(mysqli_num_rows($res_tmp)!=0){
								$row_tmp = mysqli_fetch_assoc($res_tmp);
								$sql = 'insert into survey_invoice_sec_2_1_2 (survey_id, other_description, other_amount) values ("'.$_POST['survey_id'].'", "msc", "'.$row_tmp['sno'].'")';
								execute_query($sql);
								if(mysqli_error($db)){
									$data[] = array("id"=>"error", "error"=>"2.4.Unable to save data.");
								}

							}
							else{
								$sql = 'insert into master_msc_services (msc_service) values ("'.$_POST['sec_1_1_2_msc_service_other'].'")';
								execute_query($sql);
								if(mysqli_error($db)){
									$data[] = array("id"=>"error", "error"=>"2.5.Unable to save data.");
								}

								$other_id = mysqli_insert_id($db);
								$sql = 'insert into survey_invoice_sec_2_1_2 (survey_id, other_description, other_amount) values ("'.$_POST['survey_id'].'", "msc", "'.$other_id.'")';
								execute_query($sql);
								if(mysqli_error($db)){
									$data[] = array("id"=>"error", "error"=>"2.6.Unable to save data.");
								}

							}
							
						}
					}
				}
				break;
			}
			case 2:{
				$sql = 'select * from survey_invoice_sec_2_2 where survey_id="'.$_POST['survey_id'].'"';
				$res_2_2 = execute_query($sql);
				if(mysqli_num_rows($res_2_2)==1){
					$row_2_2 = mysqli_fetch_assoc($res_2_2);
				}
				else{
					$sql = 'insert into survey_invoice_sec_2_2 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.Data Saved");	
					}
					$row_2_2['sno'] = mysqli_insert_id($db);
				}
				$sql_update = 'update survey_invoice_sec_2_2 set
				secretary = "'.$_POST['sec_2_secretary'].'",
				secretary_status = "'.$_POST['sec_2_if_yes_status'].'",
				secretary_cader = "'.$_POST['sec_2_if_yes'].'",
				secretary_name = "'.$_POST['sec_2_secretary_name'].'",
				secretary_mobile = "'.$_POST['sec_2_secretary_mobile_no'].'",
				secretary_aadhaar = "'.$_POST['sec_2_secretary_aadhaar'].'",
				accountant = "'.($_POST['sec_2_accountant']=='yes'?$_POST['sec_2_accountant_count']:$_POST['sec_2_accountant']).'",
				assistant_accountant = "'.($_POST['sec_2_assistant_accountant']=='yes'?$_POST['sec_2_assistant_accountant_count']:$_POST['sec_2_assistant_accountant']).'",
				seller = "'.($_POST['sec_2_seller']=='yes'?$_POST['sec_2_seller_count']:$_POST['sec_2_seller']).'",
				support_staff = "'.($_POST['sec_2_support_staff']=='yes'?$_POST['sec_2_support_staff_count']:$_POST['sec_2_support_staff']).'",
				guard = "'.($_POST['sec_2_guard']=='yes'?$_POST['sec_2_guard_count']:$_POST['sec_2_guard']).'",
				computer_operator = "'.($_POST['sec_2_computer_operator']=='yes'?$_POST['sec_2_computer_operator_count']:$_POST['sec_2_computer_operator']).'",
				govt_program = "'.$_POST['sec_2_govt_program'].'",
				other_description = "'.$_POST['sec_2_other_description'].'"			
				where sno="'.$row_2_2['sno'].'"';
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"4.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"4.Data Saved");	
				}
				

				break;
			}
			case 3:{
				if($_POST['sec_3_ownership']=='rent'){
					$_POST['sec_3_building_rent'] = $_POST['sec_3_building_rent'];
				}
				elseif($_POST['sec_3_ownership']=='other'){
					$_POST['sec_3_building_rent'] = $_POST['sec_3_building_rent1'];
				}
				$sql_update = 'update survey_invoice set 
				society_building_ownership="'.$_POST['sec_3_ownership'].'", 
				society_building_rent_amount="'.$_POST['sec_3_building_rent'].'", 
				society_building_area="'.$_POST['sec_3_building_area'].'"
				where sno="'.$_POST['survey_id'].'"';
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"05.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"05.Data Saved");	
				}
				$sql = 'select * from survey_invoice_sec_3_1 where survey_id="'.$_POST['survey_id'].'"';
				$res_3_1 = execute_query($sql);
				if(mysqli_num_rows($res_3_1)==1){
					$row_3_1 = mysqli_fetch_assoc($res_3_1);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_1 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"5.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"5.Data Saved");	
					}
					$row_3_1['sno'] = mysqli_insert_id($db);
				}
			
				if($_FILES['sec_3_a_image']['name']!=''){
					$sec_3_1_photo = upload_img($_FILES['sec_3_a_image'], $society, "sec_3_a_image_".$survey_invoice['sno']);
					//print_r($society_image);
					if($sec_3_1_photo['error']==1){
						$sql = 'update survey_invoice_sec_3_1 set 
						photo_id="'.$sec_3_1_photo['file_name'].'"
						where sno="'.$row_3_1['sno'].'"';
						//echo $sql;
						execute_query($sql);
						//echo mysqli_error($db);
						$data[] = array("id"=>"Update", "msg"=>$sec_3_1_photo['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_3_1_photo['msg']);
					}
				}
				
				$sql_update = 'update survey_invoice_sec_3_1 set
				number_of_sides = "'.$_POST['sec_3_a_land_length'].'",
				total_area = "'.$_POST['sec_3_a_area'].'",
				govt_records = "'.($_POST['sec_3_a_govt_records']=='yes'?$_POST['sec_3_a_govt_records']:$_POST['sec_3_a_if_yes']).'",
				gata_no = "'.$_POST['sec_3_a_gata'].'",
				east_side = "'.$_POST['sec_3_a_land_chauhaddi_east'].'",
				west_side = "'.$_POST['sec_3_a_land_chauhaddi_west'].'",
				south_side = "'.$_POST['sec_3_a_land_chauhaddi_south'].'",
				north_side = "'.$_POST['sec_3_a_land_chauhaddi_north'].'",
				on_road_land = "'.$_POST['sec_3_a_land_on_road'].'",
				front_side = "'.$_POST['sec_3_a_land_frontage'].'",
				remarks = "'.$_POST['sec_3_a_comment'].'"
				where sno="'.$row_3_1['sno'].'"';
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"6.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"6.Data Saved");	
				}
				$sql = 'delete from survey_invoice_sec_3_1_sides where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1;$i<=$_POST['sec_3_a_land_length'];$i++){
					$sql = 'insert into survey_invoice_sec_3_1_sides (survey_id, length) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_a_side_'.$i].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"7.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"7.Data Saved");	
					}
				}
				
				$sql = 'delete from survey_invoice_sec_3_3 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1;$i<=$_POST['sec_3_b_id'];$i++){
					$sql = 'insert into survey_invoice_sec_3_3 (survey_id, type_of_construction, type_of_fund, length, width, total_area, remarks) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_b_type_of_construction_'.$i].'", "'.$_POST['sec_3_b_type_of_fund_'.$i].'", "'.$_POST['sec_3_b_length_'.$i].'", "'.$_POST['sec_3_b_width_'.$i].'", "'.($_POST['sec_3_b_length_'.$i]*$_POST['sec_3_b_width_'.$i]).'", "'.$_POST['sec_3_b_comment_'.$i].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"8.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"8.Data Saved");	
					}
				}
				
				$sql = 'delete from survey_invoice_sec_3_4 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1; $i<=$_POST['sec_3_b_godown_id']; $i++){
					$sql = 'insert into survey_invoice_sec_3_4 (survey_id, storage_capacity, length, width, remarks, type_of_fund, construction_status, creation_time) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_b_storage_capacity_'.$i].'", "'.$_POST['sec_3_b_godown_length_'.$i].'", "'.$_POST['sec_3_b_godown_width_'.$i].'", "'.$_POST['sec_3_b_godown_comment_'.$i].'", "'.$_POST['sec_3_b_godown_type_of_fund_'.$i].'", "'.$_POST['sec_3_b_godown_status_'.$i].'", "'.date("Y-m-d H:i:s").'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"9.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"9.Data Saved");	
					}
				}
			
				$sql = 'delete from survey_invoice_sec_3_5 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1;$i<=$_POST['sec_3_c_id'];$i++){
					$sql = 'insert into survey_invoice_sec_3_5 (survey_id, land_type, location, total_area, approach_road) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_c_vacant_land_status_'.$i].'", "'.$_POST['sec_3_c_land_location_'.$i].'", "'.$_POST['sec_3_c_length_'.$i].'", "'.($_POST['sec_3_c_approach_road_'.$i]=='ordinary'?$_POST['sec_3_c_approach_road_'.$i]:$_POST['sec_3_c_paved_road_'.$i]).'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"10.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"10.Data Saved");	
					}
				}
			
				$sql = 'delete from survey_invoice_sec_3_6 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				$sql = 'insert into survey_invoice_sec_3_6 (survey_id, type_of_construction, rent_amount, total_area, creation_time) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_6_type_of_construction'].'", "'.$_POST['sec_3_6_rent'].'", "'.$_POST['sec_3_6_area'].'", "'.date("Y-m-d H:i:s").'")';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"11.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"11.Data Saved");	
				}
			
				$sql_update = 'update survey_invoice_sec_3_1 set
				boundry_wall="'.$_POST['sec_3_d_boundry'].'",
				main_gate="'.$_POST['sec_3_d_main_gate'].'"
				where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"12.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"12.Data Saved");	
				}

				break;
			}
			case 4:{
				$sql = 'select * from survey_invoice_sec_4 where survey_id="'.$_POST['survey_id'].'"';
				$res_4 = execute_query($sql);
				if(mysqli_num_rows($res_4)==1){
					$row_4 = mysqli_fetch_assoc($res_4);
				}
				else{
					$sql = 'insert into survey_invoice_sec_4 (survey_id, creation_time) values("'.$_POST['survey_id'].'", "'.date("Y-m-d H:i:s").'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"13.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"13.Data Saved");	
					}
					$row_4['sno'] = mysqli_insert_id($db);
				}
				$sql_update = 'update survey_invoice_sec_4 set
				micro_atm = "'.$_POST['sec_4_micro_atm'].'",
				custom_hiring_center = "'.$_POST['sec_4_custom_hiring'].'",
				drone = "'.$_POST['sec_4_drone'].'",
				chalana = "'.$_POST['sec_4_chhanna'].'",
				power_duster = "'.$_POST['sec_4_power_duster'].'",
				tractor = "'.$_POST['sec_4_tractor'].'",
				office_chair = "'.$_POST['sec_4_chair'].'",
				office_table = "'.$_POST['sec_4_table'].'",
				office_almirah = "'.$_POST['sec_4_almari'].'",
				remarks = "'.$_POST['sec_4_remarks'].'"
				where sno="'.$row_4['sno'].'"';
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"14.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"14.Data Saved");	
				}
				if(isset($_POST['sec_4_custom_hiring_euip'])){
					$sql = 'delete from survey_invoice_sec_4_custom_hiring where survey_id="'.$_POST['survey_id'].'"';
					execute_query($sql);
					foreach($_POST['sec_4_custom_hiring_euip'] as $k=>$v){
						$sql = 'insert into survey_invoice_sec_4_custom_hiring (survey_id, custom_hiring_id, creation_time) values ("'.$_POST['survey_id'].'", "'.$v.'", "'.date("Y-m-d H:i:s").'")';
						execute_query($sql);
						if(mysqli_error($db)){
							//echo mysqli_error($db).$sql;
							$data[] = array("id"=>"error", "error"=>"14.1.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"14.1.Data Saved");	
						}

					}
				}
				break;
			}
			case 5:{
				$sql = 'select * from survey_invoice_sec_5 where survey_id="'.$_POST['survey_id'].'"';
				$res_5 = execute_query($sql);
				if(mysqli_num_rows($res_5)==1){
					$row_5 = mysqli_fetch_assoc($res_5);
				}
				else{
					$sql = 'insert into survey_invoice_sec_5 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"15.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"15.Data Saved");	
					}
					$row_5['sno'] = mysqli_insert_id($db);
				}
				$sql_update = '
				UPDATE `survey_invoice_sec_5` SET
				`building_status` = "'.$_POST['sec_5_built_building'].'",
				`building_status_remarks` = "'.$_POST['sec_5_detailed_information'].'",
				`floor_length` = "'.$_POST['sec_6_a_length'].'",
				`floor_width` = "'.$_POST['sec_6_a_width'].'",
				`wall_length` = "'.$_POST['sec_6_b_length'].'",
				`wall_width` = "'.$_POST['sec_6_b_width'].'",
				`paint_length` = "'.$_POST['sec_6_c_length'].'",
				`paint_width` = "'.$_POST['sec_6_c_width'].'",
				`roof_length` = "'.$_POST['sec_6_d_length'].'",
				`roof_width` = "'.$_POST['sec_6_d_width'].'",
				`washroom_floor` = "'.($_POST['sec_6_e_floor']=='repairable'?$_POST['sec_6_e_floor_cost']:$_POST['sec_6_e_floor']).'",
				`washroom_plaster` = "'.($_POST['sec_6_e_plaster']=='repairable'?$_POST['sec_6_e_plaster_cost']:$_POST['sec_6_e_plaster']).'",
				`washroom_roof` = "'.($_POST['sec_6_e_ceiling']=='repairable'?$_POST['sec_6_e_ceiling_cost']:$_POST['sec_6_e_ceiling']).'",
				`washroom_seat` = "'.($_POST['sec_6_e_seat']=='repairable'?$_POST['sec_6_e_seat_cost']:$_POST['sec_6_e_seat']).'",
				`washroom_plumbing` = "'.($_POST['sec_6_e_plumbing']=='repairable'?$_POST['sec_6_e_plumbing_cost']:$_POST['sec_6_e_plumbing']).'",
				`doors` = "'.$_POST['sec_6_f_number_of_door'].'",
				`windows` = "'.$_POST['sec_6_g_number_of_window'].'",
				`plaster_wall` = "'.$_POST['sec_6_h_length'].'",
				`plaster_roof` = "'.$_POST['sec_6_h_width'].'",
				`others` = "'.$_POST['sec_6_i_other'].'"
				where `sno` ="'.$row_5['sno'].'"';
				execute_query($sql_update);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"16.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"16.Data Saved");	
				}
			
				if($_FILES['sec_6_a_img']['name']!=''){
					$society_image = upload_img($_FILES['sec_6_a_img'], $society, "sec_6_a_img_".$survey_invoice['sno']);
					//print_r($society_image);
					if($society_image['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						floor_image="'.$society_image['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"17.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"17.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$society_image['msg']);
					}
				}
				if($_FILES['sec_6_b_img']['name']!=''){
					$society_image = upload_img($_FILES['sec_6_b_img'], $society, "sec_6_b_img_".$survey_invoice['sno']);
					//print_r($society_image);
					if($society_image['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						wall_image="'.$society_image['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"18.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"18.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$society_image['msg']);
					}
				}
				if($_FILES['sec_6_c_img']['name']!=''){
					$society_image = upload_img($_FILES['sec_6_c_img'], $society, "sec_6_c_img_".$survey_invoice['sno']);
					//print_r($society_image);
					if($society_image['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						paint_image="'.$society_image['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"19.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"19.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$society_image['msg']);
					}
				}
				if($_FILES['sec_6_d_img']['name']!=''){
					$society_image = upload_img($_FILES['sec_6_d_img'], $society, "sec_6_d_img_".$survey_invoice['sno']);
					//print_r($society_image);
					if($society_image['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						roof_image="'.$society_image['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"20.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"20.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$society_image['msg']);
					}
				}
				break;
			}
			case 6:{
				$sql = 'select * from survey_invoice_sec_2_1 where survey_id="'.$_POST['survey_id'].'"';
				$res_2_1 = execute_query($sql);
				if(mysqli_num_rows($res_2_1)==1){
					$row_2_1 = mysqli_fetch_assoc($res_2_1);
				}
				else{
					$sql = 'insert into survey_invoice_sec_2_1 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$data[] = array("id"=>"error", "error"=>"21.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"21.Data Saved");	
					}

					$row_2_1['sno'] = mysqli_insert_id($db);
				}
				//print_r($_POST);
				if(!isset($_POST['sec_6_select_operator'])){
					$_POST['sec_6_select_operator'] = array();
				}
				$sql_update = 'update survey_invoice_sec_2_1 set 
				approach_road="'.($_POST['sec_6_access_road']=='ordinary'?'ordinary':$_POST['sec_6_paved_road']).'",
				distance_from_approach_road="'.$_POST['sec_6_2_truck_not_reach'].'",
				electric_connection="'.$_POST['sec_7_electrical_connection'].'",
				electric_connection_working="'.($_POST['sec_7_electrical_connection_working']=='yes'?'yes':$_POST['sec_7_electrical_connection_notworking']).'",
				electric_connection_proposal="'.$_POST['sec_7_if_yes'].'",
				internet_connectivity="'.$_POST['sec_8_internet_connection'].'",
				internet_service_provider="'.($_POST['sec_8_internet_connection']=='yes'?$_POST['sec_8_if_yes']:implode(", ", $_POST['sec_6_select_operator'])).'",
				water_govt_tap="'.$_POST['sec_6_narrow_tubes'].'",
				water_tank="'.$_POST['sec_6_water_tank'].'",
				water_submersible="'.$_POST['sec_6_samarsabel'].'",
				water_hand_pump="'.$_POST['sec_6_handpump'].'"
				where sno='.$row_2_1['sno'];
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"22.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"22.Data Saved");	
				}
				break;
			}
			case 7:{
				$sql = 'select * from survey_invoice_sec_11 where survey_id="'.$_POST['survey_id'].'"';
				$res_9 = execute_query($sql);
				if(mysqli_num_rows($res_9)==1){
					$row_9 = mysqli_fetch_assoc($res_9);
				}
				else{
					$sql = 'insert into survey_invoice_sec_11 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					$row_9['sno'] = mysqli_insert_id($db);
				}
				
				$sql_update = 'update survey_invoice_sec_11 set 
				godown_length = "'.$_POST['sec_9_a_length'].'", 
				godown_width = "'.$_POST['sec_9_a_width'].'", 
				godown_capacity = "'.$_POST['sec_9_a_capacity_in_mt'].'", 
				bathroom_length = "'.$_POST['sec_9_b_length'].'", 
				bathroom_width = "'.$_POST['sec_9_b_width'].'",
				showroom_length = "'.$_POST['sec_9_c_length'].'",
				showroom_width = "'.$_POST['sec_9_c_width'].'",
				boundary_length = "'.$_POST['sec_9_d_boundary_wall_length'].'",
				boundary_width = "'.$_POST['sec_9_d_boundary_wall_width'].'",
				multipurpose_length = "'.$_POST['sec_9_e_multipurpose_hall_length'].'",
				multipurpose_width = "'.$_POST['sec_9_e_multipurpose_hall_width'].'"
				where sno='.$row_9['sno'];
				execute_query($sql_update);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"23.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"23.Data Saved");	
				}
				break;
			}
		}
		
		$post_dump = json_encode($_POST, true);
		$post_dump = str_replace("'", "", $post_dump);
		$post_dump = str_replace('"', "", $post_dump);
		
		$sql = 'insert into survey_invoice_verification_actions (survey_id, user_id, creation_time, step_count, query) values("'.$_POST['survey_id'].'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'", "'.$_POST['current_step_count'].'", "'.$post_dump.'")';
		//echo $sql;
		execute_query($sql);
	}

}

if(empty($data)!=true){
	echo json_encode($data);
}


function upload_img($name, $society, $new_name, $maxDim = 1500){
	
	$file_name = $name['tmp_name'];
	list($width, $height, $type, $attr) = getimagesize( $file_name );
	if ( $width > $maxDim || $height > $maxDim ) {
		$target_filename = $file_name;
		$ratio = $width/$height;
		if( $ratio > 1) {
			$new_width = $maxDim;
			$new_height = $maxDim/$ratio;
		} else {
			$new_width = $maxDim*$ratio;
			$new_height = $maxDim;
		}
		$src = imagecreatefromstring( file_get_contents( $file_name ) );
		$dst = imagecreatetruecolor( $new_width, $new_height );
		imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
		imagedestroy( $src );
		imagejpeg( $dst, $target_filename ); // adjust format as needed
		imagedestroy( $dst );
	}
	
	
	
	$msg='';
	$imageFileType = strtolower(pathinfo($name['name'],PATHINFO_EXTENSION));
	$target_dir = '../user_data/'.$society['col2'].'/'.$society['col6'].'/';
	$target_file = $target_dir . basename($new_name).'.'.$imageFileType;
	
	$uploadOk = 1;
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($name["tmp_name"]);
		if($check !== false) {
			$msg .=  "<div class='text-danger'>File is an image - " . $check["mime"] . ".</div>";
			$uploadOk = 1;
		} 
		else {
			$msg .= "<div class='text-danger'>File is not an image.</div>";
			$uploadOk = 0;
		}
	}

	// Check if file already exists
	/*if (file_exists($target_file)) {
		$msg .= "<div class='text-danger'>Sorry, file already exists.</div>";
		$uploadOk = 0;
	}*/

	// Check file size
	if ($name["size"] > 5000000) {
		$msg .= "<div class='text-danger'>Sorry, your file is too large.</div>";
		$uploadOk = 0;
	}

	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		$msg .= "<div class='text-danger'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>";
		$uploadOk = 0;
	}

	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		$msg .= "<div class='text-danger'>Sorry, your file was not uploaded.</div>";
		// if everything is ok, try to upload file
	} 
	else {
		if (!file_exists($target_dir)) {
			mkdir($target_dir, 0777, true);
		}

		if (move_uploaded_file($name["tmp_name"], $target_file)) {
			$msg .= "<div class='text-success'>The file ". htmlspecialchars( basename($name["name"])). " has been uploaded.</div>";
		} 
		else {
			$msg .= "<div class='text-danger'>Sorry, there was an error uploading your file.</div>";
		}
	}
	$result = array("error"=>$uploadOk, "msg"=>$msg, "file_name"=>basename($new_name).'.'.$imageFileType);
	return $result;
}
?>