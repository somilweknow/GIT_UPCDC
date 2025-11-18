<?php
date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");


$q = htmlspecialchars(urldecode(strtoupper($_REQUEST["term"])), ENT_QUOTES);
if (!$q) return;

if(isset($_REQUEST['id'])){
	$id = $_REQUEST['id'];
}
else {
	$id='';
}

foreach($_POST as $k=>$v){
	if(is_array($v)){
		foreach($v as $key=>$val){
			$_POST[$k][$key] = htmlspecialchars($val);
		}
	}
	else{
		$_POST[$k] = htmlspecialchars($v);	
	}	
}

$data = array();

if($id=='type'){
	$sql = 'select * from master_society_type';
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['sno'], "type_name"=>$row['type_name'], "status"=>$row['status']);
	}
}
elseif($id=='society'){
	$sql = 'select * from test2 where col1="'.$_POST['division'].'" and col2="'.$_POST['district'].'" and col5="'.$_POST['tehseel'].'" and col6="'.$_POST['block'].'" and col3="1" and (status!="1" or status is null)';
	//echo $sql;
	//echo $sql;
	$result = execute_query($sql);
	while($row = mysqli_fetch_assoc($result)){
		$data[] = array("id"=>$row['sno'], "society_name" => "B-PACS ". $row['col4']);
	}
}

elseif($id=='submit_form_upcldf'){
	// print_r($_POST);
	//print_r($_SERVER);
	// echo $_POST['apex_code'];
	if($_POST['survey_id']==''){
        $sql = 'INSERT INTO `apex_si_1_1` (`apex_id`,`longitude`,`latitude`,`committee_status`,`email_id`,`photo_id`,`society_registration_no`,`society_registration_date`, `members_no`,`inactive_members_no`,`new_members`,`share_capital`,`inactive_to_active_no`,`total_members`) VALUES ("' . $_POST['apex_code'] . '","'.$_POST['longitude'].'","'.$_POST['latitude'].'","'.$_POST['committee_status'].'","'.$_POST['email_id'].'","'.$_POST['photo_id'].'","'.$_POST['society_registration_no'].'","'.$_POST['society_registration_date'].'", "'.$_POST['members_no'].'","'.$_POST['inactive_members_no'].'","'.$_POST['new_members'].'","'.$_POST['share_capital'].'","'.$_POST['inactive_to_active_no'].'","'.$_POST['total_members'].'")';
		// echo $_POST['apex_id'];
		execute_query($sql);
		if(mysqli_error($db)){
			$data[] = array("id"=>"error", "error"=>"Error# ".mysqli_error($db).' >> '.$sql);
		}
		else{
			$id = mysqli_insert_id($db);
			$data[] = array("id"=>$id);
		}
		
		$sql = 'select * from apex_si_1_1 where sno="'.$id.'"';
		$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
		
		$sql = 'select * from apex where sno="'.$survey_invoice['apex_id'].'"';
		$society = mysqli_fetch_assoc(execute_query($sql));
		if($_FILES['society_photo']['name']!=''){
			$society_image = upload_img($_FILES['society_photo'], $society, "society_name_".$survey_invoice['sno']);
			//print_r($society_image);
			if($society_image['error']==1){
				$sql = 'update apex_si_1_1 set 
				photo_id="'.$society_image['file_name'].'"
				where sno="'.$id.'"';
				execute_query($sql);
				$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
			}
			else{
				$data[] = array("id"=>"error", "error"=>$society_image['msg']);
			}
		}
        var_dump($_POST); exit;

	}
	else{	
		// echo $_POST['apex_code'];
		switch($_POST['current_step_count']){
			case 0:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$apex_si_1_1 = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'select * from apex where sno="'.$apex_si_1_1['apex_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				if($_FILES['society_photo']['name']!=''){
					$society_image = upload_img($_FILES['society_photo'], $society, "society_name_".$apex_si_1_1['sno']);
					//print_r($society_image);
					if($society_image['error']==1){
						$sql = 'update apex_si_1_1 set 
						photo_id="'.$society_image['file_name'].'"
						where sno="'.$_POST['survey_id'].'"';
						execute_query($sql);
						$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$society_image['msg']);
					}
				}
				$sql = 'UPDATE apex_si_1_1 SET 
                    edited_by = "",
                    edition_time = "'.date("Y-m-d H:i:s").'",
                    apex_id = "'.$_POST['apex_code'].'",
                    latitude = "'.$_POST['latitude'].'",
                    longitude = "'.$_POST['longitude'].'",
                    committee_status = "'.$_POST['committee_status'].'",
                    email_id = "'.$_POST['email_id'].'",
                    photo_id = "'.$_POST['photo_id'].'",
                    society_registration_no = "'.$_POST['society_registration_no'].'",
                    society_registration_date = "'.$_POST['society_registration_date'].'",
                    members_no = "'.$_POST['members_no'].'",
                    inactive_members_no = "'.$_POST['inactive_members_no'].'",
                    active_members_no = "'.$_POST['active_members_no'].'",
                    new_members = "'.$_POST['new_members'].'",
                    share_capital = "'.$_POST['share_capital'].'",
                    inactive_to_active_no = "'.$_POST['inactive_to_active_no'].'",
                    total_members = "'.$_POST['total_members'].'"
                    WHERE sno = '.$_POST['survey_id'];
                execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"sec-1,1.1.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"1,1.1.Data Saved");	
				}
				
				break;
			}
			case 1:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from apex where sno="'.$survey_invoice['apex_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from survey_invoice_new_sec_6_2 where survey_id="'.$_POST['survey_id'].'"';
				$res_6_2 = execute_query($sql);
				if(mysqli_num_rows($res_6_2)==1){
					$row_6_2 = mysqli_fetch_assoc($res_6_2);
					
					$sql = 'update survey_invoice_new_sec_6_2 set 
					mgt_committee_is_elected="'.$_POST['sec_6_2_mgt_committee_is_elected'].'",
					election_year="'.$_POST['sec_6_2_election_year'].'",
					end_year="'.$_POST['sec_6_2_end_year'].'"
					
					where sno='.$row_6_2['sno'];
					execute_query($sql);
					
					if(mysqli_error($db)){
						$data[] = array("id"=>"error", "error"=>"6.2 Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"6.2Data Saved");
						
						$sql = 'delete from survey_invoice_new_sec_6_2_1 where survey_id="'.$_POST['survey_id'].'"';
						execute_query($sql);
						
						for($i=1;$i<=$_POST['sec_6_2_id'];$i++){
							$sql = 'insert into survey_invoice_new_sec_6_2_1 (`survey_id`, `sec_6_2_id`, `designation`, `full_name`, `father_name`, `mobile_no`) values("'.$_POST['survey_id'].'", "'.$row_6_2['sno'].'", "'.$_POST['sec_6_2_designation_'.$i].'", "'.$_POST['sec_6_2_name_'.$i].'", "'.$_POST['sec_6_2_father_name_'.$i].'", "'.$_POST['sec_6_2__mob_no_'.$i].'")';
							execute_query($sql);
							if(mysqli_error($db)){
								//echo mysqli_error($db);
								$data[] = array("id"=>"error", "error"=>"6.2.Unable to save data.");
							}
							else{
								$data[] = array("id"=>"Update", "msg"=>"6.2.Data Saved");	
							}
						}
						
					}
				}
				else{
					$sql = 'INSERT INTO `survey_invoice_new_sec_6_2`(`survey_id`, `mgt_committee_is_elected`, `election_year`, `mgt_committee_resolution_no`) VALUES ("'.$_POST['survey_id'].'","'.$_POST['sec_6_2_mgt_committee_is_elected'].'", "'.$_POST['sec_6_2_election_year'].'", "'.$_POST['sec_6_2_mgt_committee_resolution_no'].'")';
					execute_query($sql);
					$row_6_2['sno'] = mysqli_insert_id($db);
					if(mysqli_error($db)){
						$data[] = array("id"=>"error", "error"=>"6.2Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"6.2Data Saved");	
						
						for($i=1;$i<=$_POST['sec_6_2_id'];$i++){
							$sql = 'insert into survey_invoice_new_sec_6_2_1 (`survey_id`, `sec_6_2_id`, `designation`, `full_name`, `father_name`, `mobile_no`) values("'.$_POST['survey_id'].'", "'.$row_6_2['sno'].'", "'.$_POST['sec_6_2_designation_'.$i].'", "'.$_POST['sec_6_2_name_'.$i].'", "'.$_POST['sec_6_2_father_name_'.$i].'", "'.$_POST['sec_6_2__mob_no_'.$i].'")';
							execute_query($sql);
							if(mysqli_error($db)){
								//echo mysqli_error($db);
								$data[] = array("id"=>"error", "error"=>"6.2.Unable to save data.");
							}
							else{
								$data[] = array("id"=>"Update", "msg"=>"6.2.Data Saved");	
							}
						}
					}
				}
				$sql = 'select * from survey_invoice_sec_3_new_1 where survey_id="' . $_POST['survey_id'] . '"';
				$res_3_new_1 = execute_query($sql);
				if (mysqli_num_rows($res_3_new_1) == 1) {
					$row_3_new_1 = mysqli_fetch_assoc($res_3_new_1);
					// print_r($row_3_new_1);
				} else {
					$sql = 'insert into survey_invoice_sec_3_new_1 (survey_id) values("' . $_POST['survey_id'] . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						//echo mysqli_error($db);
						$data[] = array("id" => "error", "error" => "3.1.Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "sec-3.1.Data Saved");
					}
					$row_3_new_1['sno'] = mysqli_insert_id($db);
				}

				echo $sql = 'update survey_invoice_sec_3_new_1 set
				profit_loss_1 = "'.$_POST['sec_3_profit_loss_1'].'",
				profit_loss_amount_1 = "'.$_POST['sec_3_profit_loss_amount_1'].'",
				accumulated_1 = "'.$_POST['sec_3_accumulated_1'].'",
				accumulated_amount_1 = "'.$_POST['sec_3_accumulated_amount_1'].'",
				profit_loss_2 = "'.$_POST['sec_3_profit_loss_2'].'",
				profit_loss_amount_2 = "'.$_POST['sec_3_profit_loss_amount_2'].'",
				accumulated_2 = "'.$_POST['sec_3_accumulated_2'].'",
				accumulated_amount_2 = "'.$_POST['sec_3_accumulated_amount_2'].'",
				profit_loss_3 = "'.$_POST['sec_3_profit_loss_3'].'",
				profit_loss_amount_3 = "'.$_POST['sec_3_profit_loss_amount_3'].'",
				accumulated_3 = "'.$_POST['sec_3_accumulated_3'].'",
				accumulated_amount_3 = "'.$_POST['sec_3_accumulated_amount_3'].'",
				financial_audit_year = "'.$_POST['sec_3_financial_audit_year'].'",
				audit_grading = "'.$_POST['sec_3_audit_grading'].'",
				compliance_status = "'.$_POST['sec_3_compliance_status'].'",
				agm_year = "'.$_POST['sec_3_agm_year'].'",
				dividend_year = "'.$_POST['sec_3_dividend_year'].'",
				dividend_per = "'.$_POST['sec_3_dividend_per'].'",
				dividend_amt = "'.$_POST['sec_3_dividend_amt'].'",
				santulan_patra = "'.$_POST['sec_3_santulan_patra'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_1['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.1.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-3.1. Data Saved");	
				}

				$sql = 'delete from survey_invoice_sec_2_1_2 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"4.Unable to save data.");
				}
				for($i=1; $i<=$_POST['other_business_id']; $i++){
					$sql = 'insert into survey_invoice_sec_2_1_2 (survey_id, other_description, other_amount, edition_time) values ("'.$_POST['survey_id'].'", "'.$_POST['sec_2_1_2_business_description_'.$i].'", "'.$_POST['sec_2_1_2_value_'.$i].'", "'. date('Y-m-d H:i:s') .'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$data[] = array("id"=>"error", "error"=>"4.Unable to save data.");
					}else{
						$data[] = array("id"=>"update", "msg"=>"4. Data saved.");
					}

				}

				break;
			}
			case 2:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from apex where sno="'.$survey_invoice['apex_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				$sql_human_delete = 'DELETE FROM apex_si_6_3 WHERE survey_id = "' . mysqli_real_escape_string($db, $_POST['survey_id']) . '"';
				execute_query($sql_human_delete);

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Human Resource: Unable to delete existing rows."];
				} else {
					$data[] = ["id" => "update", "msg" => "Human Resource: Existing rows deleted."];
				}

				$sanctioned_posts = $_POST['sanctioned_post'] ?? [];
				$vacant_posts     = $_POST['vacant_post'] ?? [];
				$working_names    = $_POST['working_name'] ?? [];
				$working_periods  = $_POST['working_period'] ?? [];
				$contract_numbers = $_POST['contract_no'] ?? [];
				$contract_names   = $_POST['contract_name'] ?? [];

				foreach ($sanctioned_posts as $post_id => $sanctioned_post) {

					$sanctioned_post = mysqli_real_escape_string($db, $sanctioned_post ?? '');
					$vacant_post     = mysqli_real_escape_string($db, $vacant_posts[$post_id] ?? '');
					$working_name    = mysqli_real_escape_string($db, $working_names[$post_id] ?? '');
					$working_period  = mysqli_real_escape_string($db, $working_periods[$post_id] ?? '');
					$contract_no     = mysqli_real_escape_string($db, $contract_numbers[$post_id] ?? '');
					$contract_name   = mysqli_real_escape_string($db, $contract_names[$post_id] ?? '');

					if (
						$sanctioned_post === '' &&
						$vacant_post === '' &&
						$working_name === '' &&
						$working_period === '' &&
						$contract_no === '' &&
						$contract_name === ''
					) continue;

					$sql_insert_human = "
						INSERT INTO apex_si_6_3 (
							survey_id, post_id, sanctioned_post, vacant_post,
							working_name, working_period, contract_no, contract_name,
							created_by, created_at
						) VALUES (
							'" . mysqli_real_escape_string($db, $_POST['survey_id']) . "',
							'" . mysqli_real_escape_string($db, $post_id) . "',
							'{$sanctioned_post}', '{$vacant_post}',
							'{$working_name}', '{$working_period}',
							'{$contract_no}', '{$contract_name}',
							'" . mysqli_real_escape_string($db, $_SESSION['user_id']) . "',
							'" . date('Y-m-d H:i:s') . "'
						)
					";

					execute_query($sql_insert_human);

					if (mysqli_error($db)) {
						$data[] = ["id" => "error", "error" => "Human Resource: Unable to save post ID " . $post_id];
					} else {
						$data[] = ["id" => "update", "msg" => "Human Resource: Post ID " . $post_id . " saved."];
					}
				}
				
				break;
			}
			case 3:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['apex_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'update survey_invoice set 
				society_building_ownership="' . $_POST['sec_3_ownership'] . '", 
				society_building_rent_amount="' . $_POST['sec_3_building_rent'] . '", 
				society_building_area="' . $_POST['sec_3_building_area'] . '", 
				edition_time="' . date("Y-m-d H:i:s") . '" where sno="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				//echo $sql;
				if (mysqli_error($db)) {
					//echo mysqli_error($db);
					$data[] = array("id" => "error", "error" => "7.1.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "7.1.Data Saved");
				}

				$sql = 'select * from  survey_invoice_plot_details where survey_id="'.$_POST['survey_id'].'"';
				$res_new_plot = execute_query($sql);
				if(mysqli_num_rows($res_new_plot)==1){
					$row_new_plot = mysqli_fetch_assoc($res_new_plot);
				}
				else{
					$sql = 'insert into  survey_invoice_plot_details (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					$row_new_plot['sno'] = mysqli_insert_id($db);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.1.123.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"sec-3.1.123.Data Saved");	
					}
				}
				$sql = 'UPDATE survey_invoice_plot_details SET
					plot_area = "'.$_POST['sec_new_plot_area'].'",
					plot_revenue_status = "'.$_POST['sec_new_plot_revenue_status'].'",
					plot_reason_for_not_record = "'.$_POST['sec_new_plot_reason_for_not_record'].'",
					plot_practices_if_not = "'.$_POST['sec_new_plot_practices_if_not'].'",
					plot_gata_no = "'.$_POST['sec_new_plot_gata_no'].'",
					sec_3_ownership = "'.$_POST['sec_3_ownership'].'",
					society_building_area = "'.$_POST['sec_3_building_area'].'",
					society_building_rent_amount = "'.$_POST['sec_3_building_rent'].'",
					society_building_remark = "'.$_POST['sec_3_remark'].'",
					remarks = "'.$_POST['sec_new_remarks'].'"
				WHERE sno = "'.$row_new_plot['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.1.124.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-3.1.124. Data Saved");	
				}

				$sql_check = 'SELECT sno FROM survey_invoice_sec_3_1 WHERE survey_id="' . $_POST['survey_id'] . '"';
				$res_check = execute_query($sql_check);

				if (mysqli_num_rows($res_check) > 0) {
					// UPDATE
					$sql = 'UPDATE survey_invoice_sec_3_1 SET
						east_side = "' . $_POST['sec_3_a_land_chauhaddi_east'] . '",
						west_side = "' . $_POST['sec_3_a_land_chauhaddi_west'] . '",
						north_side = "' . $_POST['sec_3_a_land_chauhaddi_north'] . '",
						south_side = "' . $_POST['sec_3_a_land_chauhaddi_south'] . '",
						on_road_land = "' . $_POST['sec_3_a_land_on_road'] . '",
						front_side = "' . $_POST['sec_3_a_land_frontage'] . '",
						remarks = "' . $_POST['sec_3_a_comment'] . '",
						edition_time = "' . date('Y-m-d H:i:s') . '"
						WHERE survey_id="' . $_POST['survey_id'] . '"';
				} else {
					$sql = 'INSERT INTO survey_invoice_sec_3_1 (
						survey_id, east_side, west_side, north_side, south_side,
						on_road_land, front_side, remarks, edition_time
					) VALUES (
						"' . $_POST['survey_id'] . '",
						"' . $_POST['sec_3_a_land_chauhaddi_east'] . '",
						"' . $_POST['sec_3_a_land_chauhaddi_west'] . '",
						"' . $_POST['sec_3_a_land_chauhaddi_north'] . '",
						"' . $_POST['sec_3_a_land_chauhaddi_south'] . '",
						"' . $_POST['sec_3_a_land_on_road'] . '",
						"' . $_POST['sec_3_a_land_frontage'] . '",
						"' . $_POST['sec_3_a_comment'] . '",
						"' . date('Y-m-d H:i:s') . '"
					)';
				}

				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Unable to save data."];
				} else {
					$data[] = ["id" => "Update", "msg" => "Data saved successfully."];
				}

				$sql = 'DELETE FROM survey_invoice_sec_3_4 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);  // Function to execute the query

				for ($i = 1; $i <= $_POST['sec_2_nirmit_godown_id']; $i++) {
					$storage_capacity = isset($_POST['sec_3_b_storage_capacity_' . $i]) ? $_POST['sec_3_b_storage_capacity_' . $i] : '';
					$godown_year = isset($_POST['sec_3_b_godown_year_' . $i]) ? $_POST['sec_3_b_godown_year_' . $i] : '';
					$wdra_certified = isset($_POST['sec_3_b_wdra_certified_' . $i]) ? $_POST['sec_3_b_wdra_certified_' . $i] : '';
					$type_of_fund = isset($_POST['sec_3_b_godown_type_of_fund_' . $i]) ? $_POST['sec_3_b_godown_type_of_fund_' . $i] : '';
					$godown_status = isset($_POST['sec_3_b_godown_status_' . $i]) ? $_POST['sec_3_b_godown_status_' . $i] : '';  // Use isset to check if the key is defined
					$comment = isset($_POST['sec_3_b_godown_comment_' . $i]) ? $_POST['sec_3_b_godown_comment_' . $i] : '';

					// SQL query for insertion
					$sql = 'INSERT INTO survey_invoice_sec_3_4 (storage_capacity, godown_year, wdra_certified, type_of_fund, construction_status, remarks, survey_id)
								VALUES ("' . $storage_capacity . '", "' . $godown_year . '", "' . $wdra_certified . '", "' . $type_of_fund . '", "' . $godown_status . '", "' . $comment . '", "' . $_POST['survey_id'] . '")';
					execute_query($sql);
				}

				$sql = 'delete from survey_invoice_sec_3_5 where survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				for ($i = 1; $i <= $_POST['sec_3_c_id']; $i++) {
					if ($_POST['sec_3_c_length_1'] != "" && $_POST['sec_3_c_length_1'] != "0") {
						$sql = 'insert into survey_invoice_sec_3_5 (survey_id, land_type, location, total_area, approach_road,suitable_godown, rak_distance,  edition_time) values("' . $_POST['survey_id'] . '", "' . $_POST['sec_3_c_vacant_land_status_' . $i] . '", "' . $_POST['sec_3_c_land_location_' . $i] . '", "' . $_POST['sec_3_c_length_' . $i] . '", "' . $_POST['sec_3_c_paved_road_' . $i] . '", "' . $_POST['sec_3_c_suitable_godown_' . $i] . '", "' . $_POST['sec_3_c_rak_distance_' . $i] . '", "' . date("Y-m-d H:i:s") . '")';
						// echo $sql;
						execute_query($sql);
						if (mysqli_error($db)) {
							//echo mysqli_error($db);
							$data[] = array("id" => "error", "error" => "7.7.Unable to save data.");
						} else {
							$data[] = array("id" => "Update", "msg" => "7.7.Data Saved");
						}
					}
					$row_3_5['sno'] = mysqli_insert_id($db);
					if ($_FILES['sec_3_c_food_scheme_image_' . $i]['name'] != '') {
						$food_scheme = upload_img($_FILES['sec_3_c_food_scheme_image_' . $i], $society, "food_scheme_" . $row_3_5['sno']);
						if ($food_scheme['error'] == 1) {
							$sql = 'UPDATE survey_invoice_sec_3_5 SET 
									food_scheme = "' . $food_scheme['file_name'] . '"
									WHERE sno = "' . $row_3_5['sno'] . '"';
							execute_query($sql);
							if (mysqli_error($db)) {
								$data[] = array("id" => "error", "error" => "sec-2.7.Unable to save data.");
							} else {
								$data[] = array("id" => "Update", "msg" => "sec-2.7.Data Saved");
							}

							$data[] = array("id" => "Update", "msg" => $food_scheme['msg']);
						} else {
							$data[] = array("id" => "error", "error" => $food_scheme['msg']);
						}
					}
				}

				$sql = 'select * from survey_invoice_sec_2_1 where survey_id="' . $_POST['survey_id'] . '"';
				$res_2_1 = execute_query($sql);
				if (mysqli_num_rows($res_2_1) == 1) {
					$row_2_1 = mysqli_fetch_assoc($res_2_1);
				} else {
					$sql = 'insert into survey_invoice_sec_2_1 (survey_id) values("' . $_POST['survey_id'] . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "21.Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "21.Data Saved");
					}

					$row_2_1['sno'] = mysqli_insert_id($db);
				}
				//print_r($_POST);
				// approach_road="'.($_POST['sec_6_access_road']=='ordinary'?'ordinary':$_POST['sec_6_paved_road']).'",
				$sql = 'update survey_invoice_sec_2_1 set 
				sec_6_road="' . $_POST['sec_6_access_road'] . '",
				distance_from_approach_road="' . $_POST['sec_6_2_truck_not_reach'] . '",
				approach_road="' . $_POST['sec_6_paved_road'] . '",
				plot_frontage="' . $_POST['sec_8_plot_frontage'] . '"
				where sno=' . $row_2_1['sno'];
				// echo $sql;
				execute_query($sql);
				if (mysqli_error($db)) {
					//echo mysqli_error($db).$sql;
					$data[] = array("id" => "error", "error" => "7.11.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "7.11.Data Saved");
				}

				break;
			}
			case 4:{
				
				$sql = 'select * from survey_invoice_new_sec_8 where survey_id="'.$_POST['survey_id'].'"';
				$res_sec_8 = execute_query($sql);
				if(mysqli_num_rows($res_sec_8)==1){
					$row_8 = mysqli_fetch_assoc($res_sec_8);
				}
				else{
					$sql = 'insert into survey_invoice_new_sec_8 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$data[] = array("id"=>"error", "error"=>"21.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"21.Data Saved");	
					}

					$row_8['sno'] = mysqli_insert_id($db);
				}
				//print_r($_POST);
				if(!isset($_POST['sec_8_select_internet_operator'])){
						$_POST['sec_8_select_internet_operator'] = array();
					}
				$sql = 'UPDATE `survey_invoice_new_sec_8` SET 
				`electrical_connection`= "'.$_POST['sec_8_electrical_connection'].'",
				`electrical_connection_working`= "'.$_POST['sec_8_electrical_connection_working'].'",
				`bill_paid_yes_no`= "'.$_POST['sec_8_bill_paid_yes_no'].'",
				`electricity_not_available_reason`= "'.$_POST['sec_8_electricity_not_available_reason'].'",
				`electricity_not_available_remark`= "'.$_POST['sec_8_electricity_not_available_remark'].'",
				`bill_not_paid_month`= "'.$_POST['sec_8_bill_not_paid_month'].'",
				`outstanding_amount`= "'.$_POST['sec_8_outstanding_amount'].'",
				
				`solar_connection`= "'.$_POST['sec_8_solar_connection'].'",
				`solar_work_status`= "'.$_POST['sec_8_solar_work_status'].'",
				`solar_bill_paid`= "'.$_POST['sec_8_solar_bill_paid'].'",
				
				`internet_connection`= "'.$_POST['sec_8_internet_connection'].'",
				`internet_service_provider`= "'.$_POST['sec_8_internet_service_provider'].'",
				`internet_bill_paid`= "'.$_POST['sec_8_internet_bill_paid'].'",
				`select_internet_operator`= "'.($_POST['sec_8_internet_connection']=='yes'?$_POST['sec_8_internet_service_provider']:implode(", ", $_POST['sec_8_select_internet_operator'])).'",
				
				`narrow_tubes`= "'.$_POST['sec_8_narrow_tubes'].'",
				`water_tank`= "'.$_POST['sec_8_water_tank'].'",
				`samarsabel`= "'.$_POST['sec_8_samarsabel'].'",
				`handpump`= "'.$_POST['sec_8_handpump'].'"			
				where sno='.$row_8['sno'];
				// echo $sql;
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"8.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"8.Data Saved");	
				}
				
				break;
			}
		}
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
	if ($name["size"] > 50000000) {
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
