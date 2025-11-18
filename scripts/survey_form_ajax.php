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

if($id=='send_otp'){
	//print_r($_POST);
	$otp = randomnumber();
	if(!isset($_POST['number'])){
		$sql = 'select * from survey_invoice where sno="'.$_POST['val'].'"';
	}
	elseif($_POST['number']==''){
		$sql = 'select * from survey_invoice where sno="'.$_POST['val'].'"';
	}
	else{
		$sql = 'select * from survey_invoice where society_id="'.$_POST['val'].'" and mobile_number="'.$_POST['number'].'"';	
	}
	//echo $sql;
	$result = execute_query($sql);
	if(mysqli_num_rows($result)!=0){
		$row = mysqli_fetch_assoc($result);
		if($row['approval_status']!='0'){
			$data[] = array("status"=>"completed", "msg"=>"Survey Already Completed");
		}
		else{
			if(!isset($_POST['number'])){
				$_POST['number'] = $row['mobile_number'];
			}
			if($_POST['number']==''){
				$_POST['number'] = $row['mobile_number'];
			}
			$sql = 'update survey_invoice set otp_verify="'.$otp.'" where sno='.$row['sno'];
			execute_query($sql);	
			$data[] = array("status"=>"otp_sent", "msg"=>"OTP Sent on mobile.".$otp);
			$template_id = '1207166661218817759';
			$pe_id = $peID;
			$get_msg = 'Dear User,

Your OTP for for login is '.$otp.'.

Regards,
WeKnow Technologies';
			//send_sms($_POST['number'],$get_msg, $template_id, $pe_id, $hindi='');
		}
	}
	else{
		$sql = 'INSERT INTO `survey_invoice` (`society_id`, `mobile_number`, `otp_verify`, `ip_address`, `http_referer`, `http_user_agent`, `approval_status`, `status`, `credited_by`, `creation_time`) VALUES ("'.$_POST['val'].'", "'.$_POST['number'].'", "'.$otp.'", "'.$_SERVER['REMOTE_ADDR'].'", "'.$_SERVER['HTTP_REFERER'].'", "'.$_SERVER['HTTP_USER_AGENT'].'", "0", "0", "", "'.date("Y-m-d H:i:s").'")';
		execute_query($sql);
		if(mysqli_error($db)){
			$data[] = array("status"=>"error", "msg"=>"Error # 23 : ".mysqli_error($db).' >> '.$sql);
		}
		else{
			$data[] = array("status"=>"otp_sent", "msg"=>"OTP Sent on mobile.".$otp);
			$template_id = '1207166661218817759';
			$pe_id = $peID;
			$get_msg = 'Dear User,

Your OTP for for login is '.$otp.'.

Regards,
WeKnow Technologies';
			//send_sms($_POST['number'],$get_msg, $template_id, $pe_id, $hindi='');
		}
		//send_sms();
	}
}
elseif($id=='verify_otp'){
	//print_r($_POST);
	$otp = randomnumber();
	$sql_validation = 'select * from survey_invoice_validation where survey_id="'.$_POST['val'].'" and user_type = "secretary" and status!=7';	
	$result_validation  = execute_query($sql_validation);
	if(mysqli_num_rows($result_validation)!=0){
		$data_validation=mysqli_fetch_array($result_validation);
		if($data_validation['status']="7"){
			$data[] = array("status"=>"completed", "msg"=>"Survey Already Completed");
		}elseif($data_validation['status']>"2"){
			$data[] = array("status"=>"completed", "msg"=>"अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है ");
		}else{
			goto newinsert;
		}
			
	}	
	else{
		newinsert:
		$sql = 'select * from survey_invoice where sno="'.$_POST['val'].'"';	
		$result = execute_query($sql);
		if(mysqli_num_rows($result)!=0){
			$row = mysqli_fetch_assoc($result);
			
			$sql = 'insert into survey_invoice_validation (survey_id,request_id, user_id, user_type, mobile_number, otp_verify, `ip_address`, `http_referer`, `http_user_agent`, `approval_status`, status, creation_time) values ("'.$row['sno'].'","", "", "secretary", "'.$row['mobile_number'].'", "1", "'.$_SERVER['REMOTE_ADDR'].'", "'.$_SERVER['HTTP_REFERER'].'", "'.$_SERVER['HTTP_USER_AGENT'].'", "approve", 2,  "'.date("Y-m-d H:i:s").'")';
			execute_query($sql);
			// echo 'A: '.$sql;
			if(mysqli_error($db)){
				$data[] = array("status"=>"error", "msg"=>"AVF#01 : Some error occured");
			}else{
				$request_id = mysqli_insert_id($db);
				
				
				$sql = 'insert into survey_invoice_validation (survey_id, request_id, user_id, user_type, mobile_number, otp_verify, `ip_address`, `http_referer`, `http_user_agent`, `approval_status`, creation_time) values ("'.$row['sno'].'","'.$request_id.'", "'.$_SESSION['user_id'].'", "ado", "'.$row['mobile_number'].'", "", "'.$_SERVER['REMOTE_ADDR'].'", "'.$_SERVER['HTTP_REFERER'].'", "'.$_SERVER['HTTP_USER_AGENT'].'", "approve", "'.date("Y-m-d H:i:s").'")';
				execute_query($sql);
				// echo 'B: '.$sql;
				if(mysqli_error($db)){
					$data[] = array("status"=>"error", "msg"=>"AVF#01 : Some error occured");
				}else{
					$sql = 'update survey_invoice set otp_verify=1, approval_status=2 where sno='.$row['sno'];
				
					execute_query($sql);
					$msg = "आपका परिपत्र सफलता पूर्वक अग्रिम कार्यवाही हेतु प्रेषित कर दिया गया है ";
					$data[] = array("status"=>"verified", "msg"=>$msg);
				}
			}				
		}
		else{
			$data[] = array("status"=>"notfound", "msg"=>"Data not found");
		}
	}
}
elseif($id=='dist'){
	$sql = 'select * from master_district where division_id="'.$_POST['val'].'"';
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
elseif($id == 'dis_society') {
	$sql = 'SELECT `sno`, `bm_society_id`, `society_id` FROM `bm_society`';
	$res = execute_query($sql);
	$_SESSION['society_sno'] = array();
	if (mysqli_num_rows($res) != 0) {
		while ($row = mysqli_fetch_assoc($res)) {
			$_SESSION['society_sno'][] = $row['society_id'];
		}
	}

	if (mysqli_num_rows($res) != 0) {

		$sql = 'SELECT * FROM test2 WHERE col1="' . $_POST['division'] . '" AND col2="' . $_POST['district'] . '" AND (status != "1" OR status IS NULL) and  test2.sno not in (SELECT `society_id` FROM `bm_society`)';
//echo $sql;
		$result = execute_query($sql);
		$data = [];
		$i = 1;
		while ($row = mysqli_fetch_assoc($result)) {
			$data[] = array("id" => $row['sno'], "society_name" => $i . '. B-PACS ' . $row['col4']);
			$i++;
		}
	}else{

			$sql = 'SELECT * FROM test2 WHERE col1="' . $_POST['division'] . '" AND col2="' . $_POST['district'] . '" AND (status != "1" OR status IS NULL)';
			$result = execute_query($sql);
			$data = [];
			$i = 1;
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = array("id" => $row['sno'], "society_name" => $i . '. B-PACS ' . $row['col4']);
				$i++;
			}

	}	
}

elseif($id=='submit_form'){
	//print_r($_POST);
	//print_r($_SERVER);
	
	if($_POST['survey_id']==''){
		
		// echo $_POST['society_code'];
		$sql = 'INSERT INTO `survey_invoice` (`society_id`, `latitude`, `longitude`, `mobile_number`, `otp_verify`, `ip_address`, `device_details`, `mac_address`, `operating_system`, `http_referer`, `http_user_agent`, `approval_status`, `status`, `credited_by`, `creation_time`, 	`committee_status`, `committee_date`, `society_registration_no`,`society_registration_date`, `email_id`, `liquidation`, `liquidation_date`, `liquidation_status`, `gst`, `gst_no`, `gst_return`, `pan`, `pan_no`, `pan_itr_return`, `fertilizer`, `fertilizer_start_date`, `fertilizer_end_date`, `pesticide`, `pesticide_start_date`, `pesticide_end_date`) VALUES ("'.$_POST['society_code'].'", "'.$_POST['latitude'].'", "'.$_POST['longitude'].'", "'.$_POST['mobile_number'].'", "OTP", "'.$_SERVER['REMOTE_ADDR'].'", "", "", "", "'.$_SERVER['HTTP_REFERER'].'", "'.$_SERVER['HTTP_USER_AGENT'].'", "0", "", "", "'.date("Y-m-d H:i:s").'",  "'.$_POST['sec_1_committee_status'].'", "'.$_POST['sec_1_committee_date'].'", "'.$_POST['sec_1_society_registration_no'].'", "'.$_POST['sec_1_society_registration_date'].'", "'.$_POST['sec_1_email'].'", "'.$_POST['sec_1_liquidation'].'", "'.$_POST['sec_1_liquidation_date'].'", "'.$_POST['sec_1_liquidation_status'].'", "'.$_POST['sec_1_gst'].'", "'.$_POST['sec_1_gst_no'].'", "'.$_POST['sec_1_gst_return'].'", "'.$_POST['sec_1_pan'].'", "'.$_POST['sec_1_pan_no'].'", "'.$_POST['sec_1_pan_itr_return'].'", "'.$_POST['sec_1_fertilizer'].'", "'.$_POST['sec_1_fertilizer_start_date'].'", "'.$_POST['sec_1_fertilizer_end_date'].'", "'.$_POST['sec_1_pesticide'].'", "'.$_POST['sec_1_pesticide_start_date'].'", "'.$_POST['sec_1_pesticide_end_date'].'")';
		execute_query($sql);
		if(mysqli_error($db)){
			$data[] = array("id"=>"error", "error"=>"Error# ".mysqli_error($db).' >> '.$sql);
		}
		else{
			$id = mysqli_insert_id($db);
			$data[] = array("id"=>$id);
		}
		if (isset($_POST['gram_panchayat']) && is_array($_POST['gram_panchayat'])) {
			foreach($_POST['gram_panchayat'] as $k=>$v){
				if($v != 'other'){
					// Insert the Gram Panchayat data associated with the newly created survey_id ($id)
					$sql = 'INSERT INTO survey_invoice_sec_1_grampanchayat (survey_id, gram_panchayt_id) VALUES ("'.$id.'", "'.$v.'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$data[] = array("id"=>"error", "error"=>"sec-1.1.Unable to save Gram Panchayt data.");
					}else{
						$data[] = array("id"=>"Update", "msg"=>"sec-1.Gram Panchayt Data Saved");
					}
				}
			}
		}
		
		$sql = 'select * from survey_invoice where sno="'.$id.'"';
		$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
		
		$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
		$society = mysqli_fetch_assoc(execute_query($sql));
		if($_FILES['society_photo']['name']!=''){
			$society_image = upload_img($_FILES['society_photo'], $society, "society_name_".$survey_invoice['sno']);
			//print_r($society_image);
			if($society_image['error']==1){
				$sql = 'update survey_invoice set 
				photo_id="'.$society_image['file_name'].'"
				where sno="'.$id.'"';
				execute_query($sql);
				$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
			}
			else{
				$data[] = array("id"=>"error", "error"=>$society_image['msg']);
			}
		}
	}
	else{
		// $sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
		// $survey_invoice = mysqli_fetch_assoc(execute_query($sql));
		
		// $sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
		// $society = mysqli_fetch_assoc(execute_query($sql));
		// if($_FILES['society_photo']['name']!=''){
			// $society_image = upload_img($_FILES['society_photo'], $society, "society_name_".$survey_invoice['sno']);
			// //print_r($society_image);
			// if($society_image['error']==1){
				// $sql = 'update survey_invoice set 
				// photo_id="'.$society_image['file_name'].'"
				// where sno="'.$_POST['survey_id'].'"';
				// execute_query($sql);
				// $data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
			// }
			// else{
				// $data[] = array("id"=>"error", "error"=>$society_image['msg']);
			// }
		// }
		
		switch($_POST['current_step_count']){
			case 0:{
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
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
				$sql = 'update survey_invoice set 
				edited_by="",
				edition_time="'.date("Y-m-d H:i:s").'",
				
				latitude="'.$_POST['latitude'].'",
				longitude="'.$_POST['longitude'].'",			

				committee_status="'.$_POST['sec_1_committee_status'].'",
				committee_date="'.$_POST['sec_1_committee_date'].'",
				society_registration_no = "'.$_POST['sec_1_society_registration_no'].'",
				society_registration_date = "'.$_POST['sec_1_society_registration_date'].'",
				email_id = "'.$_POST['sec_1_email'].'",
				liquidation = "'.$_POST['sec_1_liquidation'].'",
				liquidation_date = "'.$_POST['sec_1_liquidation_date'].'",
				liquidation_status = "'.$_POST['sec_1_liquidation_status'].'",
				gst = "'.$_POST['sec_1_gst'].'",
				gst_no = "'.$_POST['sec_1_gst_no'].'",
				gst_return = "'.$_POST['sec_1_gst_return'].'",
				pan = "'.$_POST['sec_1_pan'].'",
				pan_no = "'.$_POST['sec_1_pan_no'].'",
				pan_itr_return = "'.$_POST['sec_1_pan_itr_return'].'",
				fertilizer = "'.$_POST['sec_1_fertilizer'].'",
				fertilizer_start_date = "'.$_POST['sec_1_fertilizer_start_date'].'",
				fertilizer_end_date = "'.$_POST['sec_1_fertilizer_end_date'].'",
				pesticide = "'.$_POST['sec_1_pesticide'].'",
				pesticide_start_date = "'.$_POST['sec_1_pesticide_start_date'].'",
				pesticide_end_date = "'.$_POST['sec_1_pesticide_end_date'].'"
				where sno='.$_POST['survey_id'];
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"sec-1,1.1.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"1,1.1.Data Saved");	
				}

				$sql = 'DELETE FROM survey_invoice_sec_1_grampanchayat WHERE survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>".Gram Panchayat Unable to delete existing data.");
				}
				
				if (isset($_POST['gram_panchayat']) && is_array($_POST['gram_panchayat'])) {
					foreach($_POST['gram_panchayat'] as $k=>$v){
						if($v != 'other'){
							$sql = 'INSERT INTO survey_invoice_sec_1_grampanchayat (survey_id, gram_panchayt_id) VALUES ("'.$_POST['survey_id'].'", "'.$v.'")';
							execute_query($sql);
							if(mysqli_error($db)){
								$data[] = array("id"=>"error", "error"=>"sec-1.1.Unable to save Gram Panchayat data.");
							}else{
								$data[] = array("id"=>"Update", "msg"=>"sec-1.Gram Panchayat Data Saved");
							}
						}
					}
				}
				
				break;
			}
			case 1:{
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				
				if($_POST['sec_3_ownership']=='rent'){
					$_POST['sec_3_building_rent'] = $_POST['sec_3_building_rent'];
				}
				elseif($_POST['sec_3_ownership']=='other'){
					$_POST['sec_3_building_rent'] = $_POST['sec_3_building_rent1'];
				}
				$sql = 'update survey_invoice set 
				society_building_ownership="'.$_POST['sec_3_ownership'].'", 
				society_building_rent_amount="'.$_POST['sec_3_building_rent'].'", 
				society_building_area="'.$_POST['sec_3_building_area'].'", 
				edition_time="'.date("Y-m-d H:i:s").'" where sno="'.$_POST['survey_id'].'"';
				execute_query($sql);
				//echo $sql;
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"7.1.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"7.1.Data Saved");	
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
						$data[] = array("id"=>"error", "error"=>"7.2.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"7.2.Data Saved");	
					}
					$row_3_1['sno'] = mysqli_insert_id($db);
				}
			
				if($_FILES['sec_3_ownership_image']['name']!=''){
					$sec_3_1_photo = upload_img($_FILES['sec_3_ownership_image'], $society, "sec_3_ownership_image_".$survey_invoice['sno']);
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
				
				$sql = 'select * from  survey_invoice_plot_details where survey_id="'.$_POST['survey_id'].'"';
				$res_new_plot = execute_query($sql);
				if(mysqli_num_rows($res_new_plot)==1){
					$row_new_plot = mysqli_fetch_assoc($res_new_plot);
				}
				else{
					$sql = 'insert into  survey_invoice_plot_details (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.1.123.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"sec-3.1.123.Data Saved");	
					}
					$row_new_plot['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update  survey_invoice_plot_details set
				plot_area = "'.$_POST['sec_new_plot_area'].'",
				plot_revenue_status = "'.$_POST['sec_new_plot_revenue_status'].'",
				plot_reason_for_not_record = "'.$_POST['sec_new_plot_reason_for_not_record'].'",
				plot_practices_if_not = "'.$_POST['sec_new_plot_practices_if_not'].'",
				plot_gata_no = "'.$_POST['sec_new_plot_gata_no'].'",
				remarks = "'.$_POST['sec_new_remarks'].'",
				new_litigation = "'.$_POST['sec_new_new_litigation'].'",
				new_dispute_details = "'.$_POST['sec_new_new_dispute_details'].'"
				where sno="'.$row_new_plot['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.1.124.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-3.1.124. Data Saved");	
				}

				$sql = 'select * from   survey_invoice_new_govt_scheme where survey_id="'.$_POST['survey_id'].'"';
				$res_new_scheme = execute_query($sql);
				if(mysqli_num_rows($res_new_scheme)==1){
					$row_new_scheme = mysqli_fetch_assoc($res_new_scheme);
				}
				else{
					$sql = 'insert into   survey_invoice_new_govt_scheme (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.1.1122.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"sec-3.1.1122.Data Saved");	
					}
					$row_new_scheme['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update   survey_invoice_new_govt_scheme set
				society_selected_in_scheme = "'.$_POST['sec_new_society_selected_in_scheme'].'",
				organisation_name = "'.$_POST['sec_new_organisation_name'].'",
				selected_year_in_scheme = "'.$_POST['sec_new_selected_year_in_scheme'].'",
				scheme_remarks = "'.$_POST['sec_new_scheme_remarks'].'",
				tin_shade = "'.$_POST['sec_new_tin_shade'].'",
				tin_shade_use = "'.$_POST['sec_new_tin_shade_use'].'"
				where sno="'.$row_new_scheme['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.1.1122.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-3.1.1122. Data Saved");	
				}

				if($_FILES['sec_3_scheme_photo_id']['name']!=''){
					$sec_3_scheme_photo_id = upload_img($_FILES['sec_3_scheme_photo_id'], $society, "sec_3_scheme_photo_id_".$survey_invoice['sno']);
					//print_r($sec_3_scheme_photo_id);
					if($sec_3_scheme_photo_id['error']==1){
						$sql = 'update survey_invoice_new_govt_scheme set 
						scheme_photo_id="'.$sec_3_scheme_photo_id['file_name'].'"
						where sno="'.$row_new_scheme['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"20.scheme.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"20.scheme.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$sec_3_scheme_photo_id['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_3_scheme_photo_id['msg']);
					}					
				}


				
				// $sql = 'update survey_invoice_sec_3_1 set
				// number_of_sides = "'.$_POST['sec_3_a_land_length'].'",
				// total_area = "'.$_POST['sec_3_a_area'].'",
				// govt_records = "'.($_POST['sec_3_a_govt_records']=='yes'?$_POST['sec_3_a_govt_records']:$_POST['sec_3_a_if_yes']).'",
				// gata_no = "'.$_POST['sec_3_a_gata'].'",
				// east_side = "'.$_POST['sec_3_a_land_chauhaddi_east'].'",
				// west_side = "'.$_POST['sec_3_a_land_chauhaddi_west'].'",
				// south_side = "'.$_POST['sec_3_a_land_chauhaddi_south'].'",
				// north_side = "'.$_POST['sec_3_a_land_chauhaddi_north'].'",
				// on_road_land = "'.$_POST['sec_3_a_land_on_road'].'",
				// front_side = "'.$_POST['sec_3_a_land_frontage'].'",
				// remarks = "'.$_POST['sec_3_a_comment'].'"
				// where sno="'.$row_3_1['sno'].'"';
				// execute_query($sql);
				// if(mysqli_error($db)){
				// 	//echo mysqli_error($db);
				// 	$data[] = array("id"=>"error", "error"=>"7.2,3.Unable to save data.");
				// }
				// else{
				// 	$data[] = array("id"=>"Update", "msg"=>"7.2,3.Data Saved");	
				// }
				
				$sql = 'delete from survey_invoice_sec_3_1_sides where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1;$i<=$_POST['sec_3_a_land_length'];$i++){
					$sql = 'insert into survey_invoice_sec_3_1_sides (survey_id, length) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_a_side_'.$i].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"7.2.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"7.2.Data Saved");	
					}
				}
				
				// $sql = 'delete from survey_invoice_sec_3_3 where survey_id="'.$_POST['survey_id'].'"';
				// execute_query($sql);
				// for($i=1;$i<=$_POST['sec_3_b_id'];$i++){
				// 	$sql = 'insert into survey_invoice_sec_3_3 (survey_id, type_of_construction, type_of_fund, length, width, total_area, remarks) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_b_type_of_construction_'.$i].'", "'.$_POST['sec_3_b_type_of_fund_'.$i].'", "'.$_POST['sec_3_b_length_'.$i].'", "'.$_POST['sec_3_b_width_'.$i].'", "'.((float)$_POST['sec_3_b_length_'.$i]*(float)$_POST['sec_3_b_width_'.$i]).'", "'.$_POST['sec_3_b_comment_'.$i].'")';
				// 	execute_query($sql);
				// 	if(mysqli_error($db)){
				// 		//echo mysqli_error($db);
				// 		$data[] = array("id"=>"error", "error"=>"7.4.Unable to save data.");
				// 	}
				// 	else{
				// 		$data[] = array("id"=>"Update", "msg"=>"7.4.Data Saved");	
				// 	}
				// }

				$sql = 'DELETE FROM survey_invoice_sec_3_4 WHERE survey_id="'.$_POST['survey_id'].'"';
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
				
				$sql = 'delete from survey_invoice_sec_7_6 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1; $i<=$_POST['sec_3_b_storage_scheme_id']; $i++){
					$sql = 'insert into survey_invoice_sec_7_6 (survey_id, storage_capacity, scheme_rent, org_name, remarks, construction_status, creation_time) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_b_storage_scheme_capacity_'.$i].'", "'.$_POST['sec_3_b_storage_scheme_rent_'.$i].'", "'.$_POST['sec_3_b_storage_scheme_org_name_'.$i].'", "'.$_POST['sec_3_b_storage_scheme_comment_'.$i].'", "'.$_POST['sec_3_b_storage_scheme_status_'.$i].'", "'.date("Y-m-d H:i:s").'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"7.6.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"7.6.Data Saved");	
					}
					$row_7_6['sno'] = mysqli_insert_id($db);
					if($_FILES['sec_3_b_storage_scheme_image_'.$i]['name'] != '') {
						$scheme_image = upload_img($_FILES['sec_3_b_storage_scheme_image_'.$i], $society, "scheme_image_" . $survey_invoice['sno']);
						if($scheme_image['error'] == 1) {
							$sql = 'UPDATE survey_invoice_sec_7_6 SET 
									scheme_image = "' . $scheme_image['file_name'] . '"
									WHERE sno = "' . $row_7_6['sno'] . '"';
							execute_query($sql);
							if(mysqli_error($db)){
								$data[] = array("id"=>"error", "error"=>"sec-2.7.Unable to save data.");
							}
							else{
								$data[] = array("id"=>"Update", "msg"=>"sec-2.7.Data Saved");	
							}
	
							$data[] = array("id"=>"Update", "msg"=>$scheme_image['msg']);
						}
						else{
							$data[] = array("id"=>"error", "error"=>$scheme_image['msg']);
						}
					}
				}
								
			
				$sql = 'delete from survey_invoice_sec_3_5 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1;$i<=$_POST['sec_3_c_id'];$i++){
					if($_POST['sec_3_c_length_1']!="" && $_POST['sec_3_c_length_1']!="0"){
						$sql = 'insert into survey_invoice_sec_3_5 (survey_id, land_type, location, total_area, approach_road,suitable_godown, rak_distance,  edition_time) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_c_vacant_land_status_'.$i].'", "'.$_POST['sec_3_c_land_location_'.$i].'", "'.$_POST['sec_3_c_length_'.$i].'", "'.$_POST['sec_3_c_paved_road_'.$i].'", "'.$_POST['sec_3_c_suitable_godown_'.$i].'", "'.$_POST['sec_3_c_rak_distance_'.$i].'", "'.date("Y-m-d H:i:s").'")';
						// echo $sql;
						execute_query($sql);
						if(mysqli_error($db)){
							//echo mysqli_error($db);
							$data[] = array("id"=>"error", "error"=>"7.7.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"7.7.Data Saved");	
						}
					}
					$row_3_5['sno'] = mysqli_insert_id($db);

					$row_3_5['sno'] = mysqli_insert_id($db);
					if($_FILES['sec_3_c_food_scheme_image_'.$i]['name'] != '') {
						$food_scheme = upload_img($_FILES['sec_3_c_food_scheme_image_'.$i], $society, "food_scheme_" . $survey_invoice['sno']);
						if($food_scheme['error'] == 1) {
							$sql = 'UPDATE survey_invoice_sec_3_5 SET 
									food_scheme = "' . $food_scheme['file_name'] . '"
									WHERE sno = "' . $row_3_5['sno'] . '"';
							execute_query($sql);
							if(mysqli_error($db)){
								$data[] = array("id"=>"error", "error"=>"sec-2.7.Unable to save data.");
							}
							else{
								$data[] = array("id"=>"Update", "msg"=>"sec-2.7.Data Saved");	
							}
	
							$data[] = array("id"=>"Update", "msg"=>$food_scheme['msg']);
						}
						else{
							$data[] = array("id"=>"error", "error"=>$food_scheme['msg']);
						}
					}
				}
			
				// $sql = 'delete from survey_invoice_sec_3_6 where survey_id="'.$_POST['survey_id'].'"';
				// execute_query($sql);
				// if(isset($_POST['sec_3_6_type_of_construction'])){
				// 	$sec_3_6_type_of_construction = $_POST['sec_3_6_type_of_construction'];
				// $sec_3_6_type_of_construction_res = implode(', ', $sec_3_6_type_of_construction);
				// }else{
					
				// 	$sec_3_6_type_of_construction_res = "";
				// }
				
				
				// $sql = 'insert into survey_invoice_sec_3_6 (survey_id, rent_amount, creation_time) values("'.$_POST['survey_id'].'", "'.$_POST['sec_3_6_rent'].'", "'.date("Y-m-d H:i:s").'")';
				// execute_query($sql);
				// if(mysqli_error($db)){
				// 	$data[] = array("id"=>"error", "error"=>"7.8.Unable to save data.");
				// }
				// else{
				// 	$data[] = array("id"=>"Update", "msg"=>"7.8.Data Saved");	
				// }
			
				$sql = 'update survey_invoice_sec_3_1 set
				boundry_wall="'.$_POST['sec_3_d_boundry'].'",
				main_gate="'.$_POST['sec_3_d_main_gate'].'",
				edition_time="'.date("Y-m-d H:i:s").'"
				where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"7.9.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"7.9.Data Saved");	
				}
				
				
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
						$data[] = array("id"=>"error", "error"=>"7.2.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"7.2.Data Saved");	
					}
					$row_5['sno'] = mysqli_insert_id($db);
				}
				$sql = '
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
				`others` = "'.$_POST['sec_6_i_other'].'",
				`creation_time` = "'.date("Y-m-d H:i:s").'"
				where `sno` ="'.$row_5['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"sec-7.2.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-7.2.Data Saved");	
				}
			
				if($_FILES['sec_6_a_img']['name']!=''){
					$sec_6_a_img = upload_img($_FILES['sec_6_a_img'], $society, "sec_6_a_img_".$survey_invoice['sno']);
					//print_r($sec_6_a_img);
					if($sec_6_a_img['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						floor_image="'.$sec_6_a_img['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"sec-7.2.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"sec-7.2.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$sec_6_a_img['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_6_a_img['msg']);
					}
				}
				if($_FILES['sec_6_b_img']['name']!=''){
					$sec_6_b_img = upload_img($_FILES['sec_6_b_img'], $society, "sec_6_b_img_".$survey_invoice['sno']);
					//print_r($sec_6_b_img);
					if($sec_6_b_img['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						wall_image="'.$sec_6_b_img['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"18.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"18.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$sec_6_b_img['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_6_b_img['msg']);
					}
				}
				if($_FILES['sec_6_c_img']['name']!=''){
					$sec_6_c_img = upload_img($_FILES['sec_6_c_img'], $society, "sec_6_c_img_".$survey_invoice['sno']);
					//print_r($sec_6_c_img);
					if($sec_6_c_img['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						paint_image="'.$sec_6_c_img['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"19.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"19.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$sec_6_c_img['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_6_c_img['msg']);
					}
				}
				if($_FILES['sec_6_d_img']['name']!=''){
					$sec_6_d_img = upload_img($_FILES['sec_6_d_img'], $society, "sec_6_d_img_".$survey_invoice['sno']);
					//print_r($sec_6_d_img);
					if($sec_6_d_img['error']==1){
						$sql = 'update survey_invoice_sec_5 set 
						roof_image="'.$sec_6_d_img['file_name'].'"
						where sno="'.$row_5['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"20.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"20.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$sec_6_d_img['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_6_d_img['msg']);
					}
				}
				
				$sql = 'select * from survey_invoice_sec_3_5_1 where survey_id="'.$_POST['survey_id'].'"';
				$res_3_5_1 = execute_query($sql);
				if(mysqli_num_rows($res_3_5_1)==1){
					$row_3_5_1 = mysqli_fetch_assoc($res_3_5_1);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_5_1 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"7.12.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"7.12.Data Saved");	
					}
					$row_3_5_1['sno'] = mysqli_insert_id($db);
				}

				 $sql = 'update survey_invoice_sec_3_5_1 set
				tree = "'.$_POST['sec_6_tree'].'",
				illegal_possession = "'.$_POST['sec_6_illegal_possession'].'",
				if_yes_6 = "'.$_POST['sec_6_if_yes_6'].'",
				other_remarks = "'.$_POST['sec_6_other_remarks'].'",
				edited_by = "'. $_SESSION['username'] .'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_5_1['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"7.12.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"7.12.Data Saved");	
				}

				if($_FILES['sec_6_image']['name']!=''){
					$sec_6_image = upload_img($_FILES['sec_6_image'], $society, "sec_6_image_".$survey_invoice['sno']);
					//print_r($sec_6_image);
					if($sec_6_image['error']==1){
						$sql = 'update survey_invoice_sec_3_5_1 set 
						sec_6_image="'.$sec_6_image['file_name'].'"
						where sno="'.$row_3_5_1['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"20.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"20.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$sec_6_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_6_image['msg']);
					}					
				}
				if($_FILES['tree_image']['name']!=''){
					$tree_image = upload_img($_FILES['tree_image'], $society, "tree_image_".$survey_invoice['sno']);
					//print_r($tree_image);
					if($tree_image['error']==1){
						$sql = 'update survey_invoice_sec_3_5_1 set 
						tree_image="'.$tree_image['file_name'].'"
						where sno="'.$row_3_5_1['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"20.1.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"20.1.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$tree_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$tree_image['msg']);
					}
				}

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
				// approach_road="'.($_POST['sec_6_access_road']=='ordinary'?'ordinary':$_POST['sec_6_paved_road']).'",
				$sql = 'update survey_invoice_sec_2_1 set 
				sec_6_road="'.$_POST['sec_6_access_road'].'",
				distance_from_approach_road="'.$_POST['sec_6_2_truck_not_reach'].'",
				approach_road="'.$_POST['sec_6_paved_road'].'",
				plot_frontage="'.$_POST['sec_8_plot_frontage'].'"
				where sno='.$row_2_1['sno'];
				// echo $sql;
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"7.11.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"7.11.Data Saved");	
				}

				if($_FILES['sec_3_approach_image']['name']!=''){
					$sec_3_approach_image = upload_img($_FILES['sec_3_approach_image'], $society, "sec_3_approach_image_".$survey_invoice['sno']);
					//print_r($sec_3_approach_image);
					if($sec_3_approach_image['error']==1){
						$sql = 'update survey_invoice_sec_2_1 set 
						approach_road_photo="'.$sec_3_approach_image['file_name'].'"
						where sno="'.$row_2_1['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"20.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"20.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$sec_3_approach_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$sec_3_approach_image['msg']);
					}
				}
				
				$sql = 'DELETE FROM survey_invoice_sec_2_6 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				for ($i = 1; $i <= $_POST['sec_2_mandi_parishad_id']; $i++) {
					$sql = 'INSERT INTO survey_invoice_sec_2_6 (survey_id, number_of_store, number_of_store_rent, monthly_rent, number_of_shop, auction_status) 
							VALUES ("' . $_POST['survey_id'] . '", "' . $_POST['sec_2_number_of_store_' . $i] . '", "' . $_POST['sec_2_number_of_store_rent_' . $i] . '", "' . $_POST['sec_2_monthly_rent_' . $i] . '", "' . $_POST['sec_2_number_of_shop_' . $i] . '", "' . $_POST['sec_2_auction_status_' . $i] . '")';
					execute_query($sql);
				}

				break;
			}
			case 2:{
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				
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
				`handpump`= "'.$_POST['sec_8_handpump'].'",
				`toilet_available`= "'.$_POST['sec_8_toilet_available'].'",
				`toilet_available_women`= "'.$_POST['sec_8_toilet_available_women'].'"			
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
				// plot_photo_id = "'.$_POST['sec_new_plot_photo_id'].'",

				if($_FILES['toilet_available_image']['name']!=''){
					$toilet_available_image = upload_img($_FILES['toilet_available_image'], $society, "toilet_available_image_".$survey_invoice['sno']);
					//print_r($toilet_available_image);
					if($toilet_available_image['error']==1){
						$sql = 'update survey_invoice_new_sec_8 set 
						toilet_available_image="'.$toilet_available_image['file_name'].'"
						where sno="'.$row_8['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"sec-3.1.5.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"sec-3.1.5.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$toilet_available_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$toilet_available_image['msg']);
					}
				}
				if($_FILES['toilet_available_women_image']['name']!=''){
					$toilet_available_women_image = upload_img($_FILES['toilet_available_women_image'], $society, "toilet_available_women_image_".$survey_invoice['sno']);
					//print_r($toilet_available_women_image);
					if($toilet_available_women_image['error']==1){
						$sql = 'update survey_invoice_new_sec_8 set 
						toilet_available_women_image="'.$toilet_available_women_image['file_name'].'"
						where sno="'.$row_8['sno'].'"';
						execute_query($sql);
						if(mysqli_error($db)){
							$data[] = array("id"=>"error", "error"=>"sec-3.1.6.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"sec-3.1.6.Data Saved");	
						}

						$data[] = array("id"=>"Update", "msg"=>$toilet_available_women_image['msg']);
					}
					else{
						$data[] = array("id"=>"error", "error"=>$toilet_available_women_image['msg']);
					}
				}
				
				break;
			}
			case 3:{
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'delete from survey_invoice_new_sec_6_1 where survey_id="'.$_POST['survey_id'].'"';
					execute_query($sql);
					
					$sql = 'select * from master_designation';
					$res_designation = execute_query($sql);
					$num_row= mysqli_num_rows($res_designation);
			
					for($i=1;$i<=$num_row;$i++){
						$sql = 'insert into survey_invoice_new_sec_6_1 (survey_id, `emp_designation_id`, `emp_condition`, `emp_name`, `emp_father_name`, `emp_address`, `emp_birth_date`, `emp_education_qualification`, `emp_computer_qualification`, `emp_approval_level`, `emp_appointment_date`, `emp_mgt_committee_resolution_number_date`, `emp_type`, `emp_source`) values("'.$_POST['survey_id'].'", "'.$_POST['sec_6_1_designation_'.$i].'", "'.$_POST['sec_6_1_condition_'.$i].'", "'.$_POST['sec_6_1_name_'.$i].'", "'.$_POST['sec_6_1_father_name_'.$i].'", "'.$_POST['sec_6_1_address_'.$i].'", "'.$_POST['sec_6_1_birth_date_'.$i].'","'.$_POST['sec_6_1_education_qualification_'.$i].'", "'.$_POST['sec_6_1_computer_qualification_'.$i].'", "'.$_POST['sec_6_1_approval_level_'.$i].'", "'.$_POST['sec_6_1_appointment_date_'.$i].'", "'.$_POST['sec_6_1_mgt_committee_resolution_number_date_'.$i].'", "'.$_POST['sec_6_1_employee_type_'.$i].'", "'.$_POST['sec_6_1_source_emp_'.$i].'")';
						execute_query($sql);
						if(mysqli_error($db)){
							//echo mysqli_error($db);
							$data[] = array("id"=>"error", "error"=>"6.1.Unable to save data.");
						}
						else{
							$data[] = array("id"=>"Update", "msg"=>"6.1.Data Saved");	
						}
					}
					
					
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
					
					
	
					break;
				}
			case 4:{
				
				// $sql = 'select * from survey_invoice_new_sec_2 where survey_id="'.$_POST['survey_id'].'"';
				// $res_2 = execute_query($sql);
				// if(mysqli_num_rows($res_2)==1){
				// 	$row_2 = mysqli_fetch_assoc($res_2);
				// }
				// else{
				// 	$sql = 'insert into survey_invoice_new_sec_2 (survey_id, stock_insurance_yes_no) values("'.$_POST['survey_id'].'", "'.$_POST['sec_2_stock_insurance'].'")';
				// 	execute_query($sql);
				// 	$row_2['sno'] = mysqli_insert_id($db);
				// }
				// $sql = 'update survey_invoice_new_sec_2 set 
				// stock_insurance_yes_no="'.$_POST['sec_2_stock_insurance'].'"
				// where sno='.$row_2['sno'];
				// execute_query($sql);
				// if(mysqli_error($db)){
				// 	$data[] = array("id"=>"error", "error"=>"2.Unable to save data.");
				// }
				// else{
				// 	$data[] = array("id"=>"Update", "msg"=>"2.Data Saved");	
				// }
					////// section 2 stock////////////////
			
				$sql = 'delete from survey_trans_new_sec_2_stock where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				
					$sql='SELECT `sno`, `type_name` FROM `stock_item_type`';
					$res_stock_item_type = execute_query($sql);
					$t=1;
					while ($row_stock_item_type = mysqli_fetch_assoc($res_stock_item_type)) {
						
						$id_type=$row_stock_item_type['sno'];
						
						$sql = 'SELECT `sno`, `stock_item_type_id`, `item_name` FROM `stock_item_des` WHERE stock_item_type_id="' . $row_stock_item_type['sno'] . '"';
						$res_stock_item_des = execute_query($sql);
						$d = 1;
						
						if (mysqli_num_rows($res_stock_item_des) > 0) {
							
							while ($row_stock_item_des = mysqli_fetch_assoc($res_stock_item_des)) {
								$id_des=$row_stock_item_des['sno'];
							
								$sql = 'INSERT INTO survey_trans_new_sec_2_stock(survey_id, invoice_id, stock_item_type_id, stock_item_des_id, closing_stock_1, book_value_1, closing_stock_2, book_value_2) 
								VALUES (
								"'.$_POST['survey_id'].'",
								"'.$row_2['sno'].'",
								"'.$id_type.'",
								"'.$id_des.'",
								"'.$_POST['closing_stock_1_'.$id_type.'_'.$id_des].'",
								"'.$_POST['book_value_1_'.$id_type.'_'.$id_des].'", 
								"'.$_POST['closing_stock_2_'.$id_type.'_'.$id_des].'",
								"'.$_POST['book_value_2_'.$id_type.'_'.$id_des].'")'; 
								execute_query($sql);
								if(mysqli_error($db)){
									//echo mysqli_error($db);
									$data[] = array("id"=>"error", "error"=>"sec-2.Unable to save data.");
								}
								else{
									$data[] = array("id"=>"Update", "msg"=>"sec-2.Data Saved");	
								}
			
							}
						} else {
							
								$sql = 'INSERT INTO survey_trans_new_sec_2_stock(survey_id, invoice_id, stock_item_type_id, closing_stock_1, book_value_1, closing_stock_2, book_value_2) VALUES ("'.$_POST['survey_id'].'","'.$row_2['sno'].'","'.$id_type.'","'.$_POST['closing_stock_1_'.$id_type].'","'.$_POST['book_value_1_'.$id_type].'","'.$_POST['closing_stock_2_'.$id_type].'","'.$_POST['book_value_2_'.$id_type].'")';
								execute_query($sql);
								if(mysqli_error($db)){
									//echo mysqli_error($db);
									$data[] = array("id"=>"error", "error"=>"sec2.Unable to save data.");
								}
								else{
									$data[] = array("id"=>"Update", "msg"=>"sec2.Data Saved");	
								}
			
						}
					}
				
				
	//////////////////2.1 SCRAPPED (निष्प्रयोज्य)///////////////////////////////////	
			$sql = 'delete from survey_invoice_new_sec_2_1 where survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				for($i=1;$i<=10;$i++){
					$sql = 'insert into survey_invoice_new_sec_2_1 (survey_id, invoice_id, item_name, item_description, book_value) values("'.$_POST['survey_id'].'","'.$row_2['sno'].'", "'.$_POST['scraped_item_name_'.$i].'", "'.$_POST['scraped_item_description_'.$i].'", "'.$_POST['book_value_'.$i].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"2.1.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"2.1.Data Saved");	
					}
				}

	
	/////////////////////////////////////////////////////			
	
		$sql = 'delete from survey_invoice_new_sec_2_2 where survey_id="'.$_POST['survey_id'].'"';
			execute_query($sql);
			for($i=1;$i<=10;$i++){
				$sql = 'insert into survey_invoice_new_sec_2_2 (survey_id,invoice_id, item_name, item_description, scheme_name, date, purchase_value, quantity) values("'.$_POST['survey_id'].'","'.$row_2['sno'].'", "'.$_POST['item_name_'.$i].'", "'.$_POST['item_description_'.$i].'", "'.$_POST['scheme_name_'.$i].'", "'.$_POST['date_'.$i].'", "'.$_POST['purchase_value_'.$i].'", "'.$_POST['quantity_'.$i].'")';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db);
					$data[] = array("id"=>"error", "error"=>"2.2.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"2.2.Data Saved");	
				}
			}

			$sql = 'select * from survey_invoice_new_sec_4 where survey_id="'.$_POST['survey_id'].'"';
				$res_4_1 = execute_query($sql);
				if(mysqli_num_rows($res_4_1)==1){
					$row_4_1 = mysqli_fetch_assoc($res_4_1);
				}
				else{
					$sql = 'insert into survey_invoice_new_sec_4 (survey_id, computer_insurance_yes_no) values("'.$_POST['survey_id'].'", "'.$_POST['sec_4_computer_insurance'].'")';
					execute_query($sql);
					$row_4_1['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_new_sec_4 set 
				computer_insurance_yes_no="'.$_POST['sec_4_computer_insurance'].'"
				where sno='.$row_4_1['sno'];
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"2.22.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"2.22.Data Saved");	
				}
				
				break;
			}
			
			
			case 5:{
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'select * from survey_invoice_new_members where survey_id="'.$_POST['survey_id'].'"';
				$res_new_members = execute_query($sql);
				if(mysqli_num_rows($res_new_members)==1){ 
					$row_new_members = mysqli_fetch_assoc($res_new_members);
					// print_r($row_new_members);
				}
				else{
					$sql = 'insert into survey_invoice_new_members (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.1.111.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"sec-3.1.111.Data Saved");	
					}
					$row_new_members['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_new_members set
				active_members = "'.$_POST['sec_new_active_members'].'",
				inactive_members = "'.$_POST['sec_new_inactive_members'].'",
				kcc_members = "'.$_POST['sec_new_kcc_members'].'",
				total_farmers_member = "'.$_POST['sec_new_total_farmers_member'].'",
				total_non_farmers_member = "'.$_POST['sec_new_total_non_farmers_member'].'",
				new_members = "'.$_POST['sec_new_new_members'].'",
				contribution_received_capital = "'.$_POST['sec_new_contribution_received_capital'].'",
				inactive_to_active_members = "'.$_POST['sec_new_inactive_to_active_members'].'",
				total_members = "'.$_POST['sec_new_total_members'].'",
				marginal_farmer = "'.$_POST['sec_new_marginal_farmer'].'",
				small_farmer = "'.$_POST['sec_new_small_farmer'].'",
				big_farmer = "'.$_POST['sec_new_big_farmer'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_new_members['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.1.111.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-3.1.111. Data Saved");	
				}
				
				break;	
			}
			
			case 6:{ 
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'select * from survey_invoice_new_sec_2 where survey_id="'.$_POST['survey_id'].'"';
				$res_2 = execute_query($sql);
				if(mysqli_num_rows($res_2)==1){
					$row_2 = mysqli_fetch_assoc($res_2);
				}
				else{
					$sql = 'insert into survey_invoice_new_sec_2 (survey_id, stock_insurance_yes_no) values("'.$_POST['survey_id'].'", "'.$_POST['sec_2_stock_insurance'].'")';
					execute_query($sql);
					$row_2['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_new_sec_2 set 
				stock_insurance_yes_no="'.$_POST['sec_2_stock_insurance'].'"
				where sno='.$row_2['sno'];
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"2.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"2.Data Saved");	
				}
				
				$sql = 'select * from survey_invoice_sec_3_new_1 where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_1 = execute_query($sql);
				if(mysqli_num_rows($res_3_new_1)==1){ 
					$row_3_new_1 = mysqli_fetch_assoc($res_3_new_1);
					// print_r($row_3_new_1);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_1 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.1.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"sec-3.1.Data Saved");	
					}
					$row_3_new_1['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_1 set
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
				mulya_samarthan = "'.$_POST['sec_3_mulya_samarthan'].'",
				msp_fin_year = "'.$_POST['sec_3_msp_fin_year'].'",
				commision_amt = "'.$_POST['sec_3_commision_amt'].'",
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
				
				$sql = 'select * from survey_invoice_sec_3_new_2_urea where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_urea = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_urea)==1){ 
					$row_3_new_2_urea = mysqli_fetch_assoc($res_3_new_2_urea);
				// print 'somil';	print_r($row_3_new_2_urea);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_urea (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 urea.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 urea.Data Saved");	
					}
					$row_3_new_2_urea['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_urea set
				urea_opening = "'.$_POST['sec_3_urea_opening'].'",
				urea_opening_oct = "'.$_POST['sec_3_urea_opening_oct'].'",
				urea_1 = "'.$_POST['sec_3_new_2_urea_1'].'",
				urea_2 = "'.$_POST['sec_3_new_2_urea_2'].'",
				urea_3 = "'.$_POST['sec_3_new_2_urea_3'].'",
				urea_4 = "'.$_POST['sec_3_new_2_urea_4'].'",
				urea_5 = "'.$_POST['sec_3_new_2_urea_5'].'",
				urea_6 = "'.$_POST['sec_3_new_2_urea_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_urea['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 urea.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 urea.Data Saved");	
				}
				
				$sql = 'select * from survey_invoice_sec_3_new_2_dap where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_dap = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_dap)==1){
					$row_3_new_2_dap = mysqli_fetch_assoc($res_3_new_2_dap);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_dap (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 dap.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 dap.Data Saved");	
					}
					$row_3_new_2_dap['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_dap set
				dap_opening = "'.$_POST['sec_3_dap_opening'].'",
				dap_opening_oct = "'.$_POST['sec_3_dap_opening_oct'].'",
				dap_1 = "'.$_POST['sec_3_new_2_dap_1'].'",
				dap_2 = "'.$_POST['sec_3_new_2_dap_2'].'",
				dap_3 = "'.$_POST['sec_3_new_2_dap_3'].'",
				dap_4 = "'.$_POST['sec_3_new_2_dap_4'].'",
				dap_5 = "'.$_POST['sec_3_new_2_dap_5'].'",
				dap_6 = "'.$_POST['sec_3_new_2_dap_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_dap['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 dap.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 dap.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_npk where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_npk = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_npk)==1){
					$row_3_new_2_npk = mysqli_fetch_assoc($res_3_new_2_npk);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_npk (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 npk.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 npk.Data Saved");	
					}
					$row_3_new_2_npk['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_npk set
				npk_opening = "'.$_POST['sec_3_npk_opening'].'",
				npk_opening_oct = "'.$_POST['sec_3_npk_opening_oct'].'",
				npk_1 = "'.$_POST['sec_3_new_2_npk_1'].'",
				npk_2 = "'.$_POST['sec_3_new_2_npk_2'].'",
				npk_3 = "'.$_POST['sec_3_new_2_npk_3'].'",
				npk_4 = "'.$_POST['sec_3_new_2_npk_4'].'",
				npk_5 = "'.$_POST['sec_3_new_2_npk_5'].'",
				npk_6 = "'.$_POST['sec_3_new_2_npk_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_npk['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 npk.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 npk.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_nano_urea where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_nano_urea = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_nano_urea)==1){
					$row_3_new_2_nano_urea = mysqli_fetch_assoc($res_3_new_2_nano_urea);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_nano_urea (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 nano.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 nano.Data Saved");	
					}
					$row_3_new_2_nano_urea['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_nano_urea set
				nano_urea_opening = "'.$_POST['sec_3_nano_urea_opening'].'",
				nano_urea_opening_oct = "'.$_POST['sec_3_nano_urea_opening_oct'].'",
				nano_urea_1 = "'.$_POST['sec_3_new_2_nano_urea_1'].'",
				nano_urea_2 = "'.$_POST['sec_3_new_2_nano_urea_2'].'",
				nano_urea_3 = "'.$_POST['sec_3_new_2_nano_urea_3'].'",
				nano_urea_4 = "'.$_POST['sec_3_new_2_nano_urea_4'].'",
				nano_urea_5 = "'.$_POST['sec_3_new_2_nano_urea_5'].'",
				nano_urea_6 = "'.$_POST['sec_3_new_2_nano_urea_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_nano_urea['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 nano.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 nano.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_nano_dap where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_nano_dap = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_nano_dap)==1){
					$row_3_new_2_nano_dap = mysqli_fetch_assoc($res_3_new_2_nano_dap);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_nano_dap (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 nano dap.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 nano dap.Data Saved");	
					}
					$row_3_new_2_nano_dap['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_nano_dap set
				nano_dap_opening = "'.$_POST['sec_3_nano_dap_opening'].'",
				nano_dap_opening_oct = "'.$_POST['sec_3_nano_dap_opening_oct'].'",
				nano_dap_1 = "'.$_POST['sec_3_new_2_nano_dap_1'].'",
				nano_dap_2 = "'.$_POST['sec_3_new_2_nano_dap_2'].'",
				nano_dap_3 = "'.$_POST['sec_3_new_2_nano_dap_3'].'",
				nano_dap_4 = "'.$_POST['sec_3_new_2_nano_dap_4'].'",
				nano_dap_5 = "'.$_POST['sec_3_new_2_nano_dap_5'].'",
				nano_dap_6 = "'.$_POST['sec_3_new_2_nano_dap_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_nano_dap['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 nano dap.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 nano dap.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_pesticide where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_pesticide = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_pesticide)==1){
					$row_3_new_2_pesticide = mysqli_fetch_assoc($res_3_new_2_pesticide);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_pesticide (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 pesticide.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 pesticide.Data Saved");	
					}
					$row_3_new_2_pesticide['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_pesticide set
				pesticide_opening = "'.$_POST['sec_3_pesticide_opening'].'",
				pesticide_opening_oct = "'.$_POST['sec_3_pesticide_opening_oct'].'",
				pesticide_1 = "'.$_POST['sec_3_new_2_pesticide_1'].'",
				pesticide_2 = "'.$_POST['sec_3_new_2_pesticide_2'].'",
				pesticide_3 = "'.$_POST['sec_3_new_2_pesticide_3'].'",
				pesticide_4 = "'.$_POST['sec_3_new_2_pesticide_4'].'",
				pesticide_5 = "'.$_POST['sec_3_new_2_pesticide_5'].'",
				pesticide_6 = "'.$_POST['sec_3_new_2_pesticide_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_pesticide['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 pesticide.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 pesticide.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_seeds where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_seeds = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_seeds)==1){
					$row_3_new_2_seeds = mysqli_fetch_assoc($res_3_new_2_seeds);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_seeds (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 seeds.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 seeds.Data Saved");	
					}
					$row_3_new_2_seeds['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_seeds set
				seeds_opening = "'.$_POST['sec_3_seeds_opening'].'",
				seeds_opening_oct = "'.$_POST['sec_3_seeds_opening_oct'].'",
				seeds_1 = "'.$_POST['sec_3_new_2_seeds_1'].'",
				seeds_2 = "'.$_POST['sec_3_new_2_seeds_2'].'",
				seeds_3 = "'.$_POST['sec_3_new_2_seeds_3'].'",
				seeds_4 = "'.$_POST['sec_3_new_2_seeds_4'].'",
				seeds_5 = "'.$_POST['sec_3_new_2_seeds_5'].'",
				seeds_6 = "'.$_POST['sec_3_new_2_seeds_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_seeds['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 seeds.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 seeds.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_iffco where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_iffco = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_iffco)==1){
					$row_3_new_2_iffco = mysqli_fetch_assoc($res_3_new_2_iffco);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_iffco (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 iffco.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 iffco.Data Saved");	
					}
					$row_3_new_2_iffco['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_iffco set
				iffco_opening = "'.$_POST['sec_3_iffco_opening'].'",
				iffco_opening_oct = "'.$_POST['sec_3_iffco_opening_oct'].'",
				iffco_1 = "'.$_POST['sec_3_new_2_iffco_1'].'",
				iffco_2 = "'.$_POST['sec_3_new_2_iffco_2'].'",
				iffco_3 = "'.$_POST['sec_3_new_2_iffco_3'].'",
				iffco_4 = "'.$_POST['sec_3_new_2_iffco_4'].'",
				iffco_5 = "'.$_POST['sec_3_new_2_iffco_5'].'",
				iffco_6 = "'.$_POST['sec_3_new_2_iffco_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_iffco['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 iffco.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 iffco.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_kribhko where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_kribhko = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_kribhko)==1){
					$row_3_new_2_kribhko = mysqli_fetch_assoc($res_3_new_2_kribhko);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_kribhko (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 kribhko.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 kribhko.Data Saved");	
					}
					$row_3_new_2_kribhko['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_kribhko set
				kribhko_opening = "'.$_POST['sec_3_kribhko_opening'].'",
				kribhko_opening_oct = "'.$_POST['sec_3_kribhko_opening_oct'].'",
				kribhko_1 = "'.$_POST['sec_3_new_2_kribhko_1'].'",
				kribhko_2 = "'.$_POST['sec_3_new_2_kribhko_2'].'",
				kribhko_3 = "'.$_POST['sec_3_new_2_kribhko_3'].'",
				kribhko_4 = "'.$_POST['sec_3_new_2_kribhko_4'].'",
				kribhko_5 = "'.$_POST['sec_3_new_2_kribhko_5'].'",
				kribhko_6 = "'.$_POST['sec_3_new_2_kribhko_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_kribhko['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 kribhko.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 kribhko.Data Saved");	
				}

				$sql = 'select * from survey_invoice_sec_3_new_2_other where survey_id="'.$_POST['survey_id'].'"';
				$res_3_new_2_other = execute_query($sql);
				if(mysqli_num_rows($res_3_new_2_other)==1){
					$row_3_new_2_other = mysqli_fetch_assoc($res_3_new_2_other);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_2_other (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 other.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 other.Data Saved");	
					}
					$row_3_new_2_other['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_2_other set
				other_opening = "'.$_POST['sec_3_other_opening'].'",
				other_opening_oct = "'.$_POST['sec_3_other_opening_oct'].'",
				other_1 = "'.$_POST['sec_3_new_2_other_1'].'",
				other_2 = "'.$_POST['sec_3_new_2_other_2'].'",
				other_3 = "'.$_POST['sec_3_new_2_other_3'].'",
				other_4 = "'.$_POST['sec_3_new_2_other_4'].'",
				other_5 = "'.$_POST['sec_3_new_2_other_5'].'",
				other_6 = "'.$_POST['sec_3_new_2_other_6'].'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_new_2_other['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 other.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 other.Data Saved");	
				}
				

				$sql = 'select * from survey_invoice_sec_3_new_3 where survey_id="'.$_POST['survey_id'].'" ';
				$res_5_new = execute_query($sql);
				if(mysqli_num_rows($res_5_new)==1){
					$row_5_new = mysqli_fetch_assoc($res_5_new);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_3 (survey_id) values ("'.$_POST['survey_id'].'")';
					execute_query($sql);
					$row_5_new['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_3 set
				stock_insurance = "'.$_POST['sec_new_3_stock_insurance'].'",
				ten_lakh_loan_1 = "'.$_POST['sec_new_3_ten_lakh_loan_1'].'",
				ten_lakh_loan_2 = "'.$_POST['sec_new_3_ten_lakh_loan_2'].'",
				csc = "'.$_POST['sec_new_3_csc'].'",
				csc_transactions = "'.$_POST['sec_new_3_csc_transactions'].'",
				csc_amt = "'.$_POST['sec_new_3_csc_amt'].'",
				csc_commission = "'.$_POST['sec_new_3_csc_commission'].'",
				pds_1 = "'.$_POST['sec_new_3_pds_1'].'",
				pds_tornover = "'.$_POST['sec_new_3_pds_tornover'].'",
				medical_center = "'.$_POST['sec_new_3_medical_center'].'",
				total_order = "'.$_POST['sec_new_3_total_order'].'",
				gained_commission = "'.$_POST['sec_new_3_gained_commission'].'",
				total_export = "'.$_POST['sec_new_3_total_export'].'",
				edited_by = "'.$_SESSION['username'].'",
				edition_time = "'.date('Y-m-d H:i:s').'"
				where sno = '.$row_5_new['sno'];
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"3.2 i. Unable to save data.");
				}
				else{
					$data[] = array("id"=>"update", "msg"=>"3.2 i. Data saved.");
				}

				$sql = 'select * from survey_invoice_sec_3_loan_distribution where survey_id="'.$_POST['survey_id'].'" ';
				$res_5_new = execute_query($sql);

				if (mysqli_num_rows($res_5_new) == 1) {
					$row_5_new = mysqli_fetch_assoc($res_5_new);
				} else {
					$sql = 'insert into survey_invoice_sec_3_loan_distribution (survey_id) values ("'.$_POST['survey_id'].'")';
					execute_query($sql);
					$row_5_new['sno'] = mysqli_insert_id($db);
				}

				$loanType = $_POST['sec_3_select_loan'];
				$loan_distribution = $_POST['sec_new_3_loan_distribution'] ?? '';
				$loan_amt_limit = $_POST['sec_new_3_loan_amt_limit'] ?? '';
				$loan_amt_target = $_POST['sec_new_3_loan_amt_target'] ?? '';
				$fy_loan_sanctioned_amt = $_POST['sec_new_3_fy_loan_sanctioned_amt'] ?? '';
				$loan_sanctioned_amt = $_POST['sec_new_3_loan_sanctioned_amt'] ?? '';
				$farmers_3_lakh = $_POST['sec_new_3_farmers_3_lakh'] ?? '';
				$beneficiaries = $_POST['sec_new_3_beneficiaries'] ?? '';
				$kcc_iss_beneficiaries = $_POST['sec_new_3_kcc_iss_beneficiaries'] ?? '';
				$loan_max = $_POST['sec_new_3_loan_max'] ?? '';

				$sql = 'update survey_invoice_sec_3_loan_distribution set
					locan_type = "'.$loanType.'",
					loan_distribution = "'.$loan_distribution.'",
					loan_amt_limit = "'.$loan_amt_limit.'",
					loan_amt_target = "'.$loan_amt_target.'",
					fy_loan_sanctioned_amt = "'.$fy_loan_sanctioned_amt.'",
					loan_sanctioned_amt = "'.$loan_sanctioned_amt.'",
					farmers_3_lakh = "'.$farmers_3_lakh.'",
					beneficiaries = "'.$beneficiaries.'",
					kcc_iss_beneficiaries = "'.$kcc_iss_beneficiaries.'",
					loan_max = "'.$loan_max.'",
					edited_by = "'.$_SESSION['username'].'",
					edition_time = "'.date('Y-m-d H:i:s').'"
					where sno = '.$row_5_new['sno'];

				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = array("id"=>"error", "error"=>"3.2 j. Unable to save data.");
				} else {
					$data[] = array("id"=>"update", "msg"=>"3.2 j. Data saved.");
				}

				
				// $sql = 'select * from survey_invoice_sec_3_dcb_distribution where survey_id="'.$_POST['survey_id'].'" ';
				// $res_5_new = execute_query($sql);

				// if (mysqli_num_rows($res_5_new) == 1) {
				// 	$row_5_dcb = mysqli_fetch_assoc($res_5_new);
				// } else { 
				// 	$sql = 'insert into survey_invoice_sec_3_dcb_distribution (survey_id) values ("'.$_POST['survey_id'].'")';
				// 	execute_query($sql);
				// 	$row_5_dcb['sno'] = mysqli_insert_id($db);
				// }
				
				// $sql = 'UPDATE survey_invoice_sec_3_dcb_distribution SET
				// 	dcb_loan_distribution = "'.$_POST['sec_new_3_dcb_loan_distribution'].'",
				// 	diversification_num = "'.$_POST['sec_new_3_diversification_num'].'",
				// 	diversification_target = "'.$_POST['sec_new_3_diversification_target'].'",
				// 	diversification_supply = "'.$_POST['sec_new_3_diversification_supply'].'",
				// 	edited_by = "'.$_SESSION['username'].'",
				// 	edition_time = "'.date('Y-m-d H:i:s').'"
				// 	WHERE sno = '.$row_5_dcb['sno'];
				// execute_query($sql);

				// if (mysqli_error($db)) {
				// 	$data[] = array("id" => "error", "error" => "3.2 k. Unable to save data.");
				// } else {
				// 	$data[] = array("id" => "update", "msg" => "3.2 k. Data saved.");
				// }

				// $sql = 'select * from survey_invoice_sec_3_ten_lakh_loan_limit where survey_id="'.$_POST['survey_id'].'"';
				// $res_3_loan_limit_10a = execute_query($sql);
				// if(mysqli_num_rows($res_3_loan_limit_10a)==1){
				// 	$row_3_loan_limit_10a = mysqli_fetch_assoc($res_3_loan_limit_10a);
				// 	//print($row_3_loan_limit);
				// }
				// else{
				// 	$sql = 'insert into survey_invoice_sec_3_ten_lakh_loan_limit (survey_id) values("'.$_POST['survey_id'].'")';
				// 	execute_query($sql);
				// 	if(mysqli_error($db)){
				// 		 // mysqli_error($db);
				// 		$data[] = array("id"=>"error", "error"=>"3.2 l.Unable to save data.");
				// 	}
				// 	else{
				// 		$row_3_loan_limit_10a['sno'] = mysqli_insert_id($db);	
				// 		$data[] = array("id"=>"Update", "msg"=>"3.2 l.Data Saved");
				// 	}
					
				// 	// print_r($row_3_loan_limit);
				// }
				// // echo $row_3_loan_limit_10a['sno'];
				// $sql = 'update survey_invoice_sec_3_ten_lakh_loan_limit set
				// ten_lakh_loan_limit = "'.$_POST['sec_new_3_ten_lakh_loan_limit'].'",
				// gl_code = "'.$_POST['sec_new_3_gl_code'].'",
				// acc_num = "'.$_POST['sec_new_3_acc_num'].'",
				// open_bal_1 = "'.$_POST['sec_new_3_open_bal_1'].'",
				// debit_amt_1 = "'.$_POST['sec_new_3_debit_amt_1'].'",
				// credit_amt_1 = "'.$_POST['sec_new_3_credit_amt_1'].'",
				// num_of_transactions_1 = "'.$_POST['sec_new_3_num_of_transactions_1'].'",
				// open_bal = "'.$_POST['sec_new_3_open_bal'].'",
				// debit_amt = "'.$_POST['sec_new_3_debit_amt'].'",
				// credit_amt = "'.$_POST['sec_new_3_credit_amt'].'",
				// num_of_transactions = "'.$_POST['sec_new_3_num_of_transactions'].'",
				
				// loan_other = "'.$_POST['sec_new_3_loan_other'].'",
				// gl_code_other_1 = "'.$_POST['sec_new_3_gl_code_other_1'].'",
				// acc_num_other_1 = "'.$_POST['sec_new_3_acc_num_other_1'].'",
				// open_bal_other_1 = "'.$_POST['sec_new_3_open_bal_other_1'].'",
				// debit_amt_other_1 = "'.$_POST['sec_new_3_debit_amt_other_1'].'",
				// credit_amt_other_1 = "'.$_POST['sec_new_3_credit_amt_other_1'].'",
				// transaction_other_1 = "'.$_POST['sec_new_3_transaction_other_1'].'",
				// open_bal_other = "'.$_POST['sec_new_3_open_bal_other'].'",
				// debit_amt_cur = "'.$_POST['sec_new_3_debit_amt_cur'].'",
				// credit_amt_cur = "'.$_POST['sec_new_3_credit_amt_cur'].'",
				// num_of_transaction_cur = "'.$_POST['sec_new_3_num_of_transaction_cur'].'",
				// edition_time = "'.date("Y-m-d H:i:s").'"
				// where sno="'.$row_3_loan_limit_10a['sno'].'"';
				// execute_query($sql);
				// if(mysqli_error($db)){
				// 	//echo mysqli_error($db).$sql;
				// 	$data[] = array("id"=>"error", "error"=>"3.2 l.Unable to save data.");
				// }
				// else{
				// 	$data[] = array("id"=>"Update", "msg"=>"3.2 l.Data Saved");	
				// }

				$sql = 'select * from survey_invoice_sec_3_new_msp where survey_id="'.$_POST['survey_id'].'"';
				$res_3_loan_limit = execute_query($sql);
				if(mysqli_num_rows($res_3_loan_limit)==1){
					$row_3_loan_limit = mysqli_fetch_assoc($res_3_loan_limit);
				}
				else{
					$sql = 'insert into survey_invoice_sec_3_new_msp (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"3.2 m.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"3.2 m.Data Saved");	
					}
					$row_3_loan_limit['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_3_new_msp set
				msp = "'.$_POST['sec_new_3_msp'].'",
				agency_name_kharif = "'.$_POST['sec_new_3_agency_name_kharif'].'",
				kharif_crops = "'.$_POST['sec_new_3_kharif_crops'].'",
				crop_name_kharif = "'.$_POST['sec_new_3_crop_name_kharif'].'",
				quantity_kharif = "'.$_POST['sec_new_3_quantity_kharif'].'",
				amt_kharif = "'.$_POST['sec_new_3_amt_kharif'].'",
				commission_pay_kharif = "'.$_POST['sec_new_3_commission_pay_kharif'].'",
				commission_rec_kharif = "'.$_POST['sec_new_3_commission_rec_kharif'].'",

				agency_name_rabi = "'.$_POST['sec_new_3_agency_name_rabi'].'",
				rabi_crops = "'.$_POST['sec_new_3_rabi_crops'].'",
				crop_name_rabi = "'.$_POST['sec_new_3_crop_name_rabi'].'",
				quantity_rabi = "'.$_POST['sec_new_3_quantity_rabi'].'",
				amt_rabi = "'.$_POST['sec_new_3_amt_rabi'].'",
				commission_pay_rabi = "'.$_POST['sec_new_3_commission_pay_rabi'].'",
				commission_rec_rabi = "'.$_POST['sec_new_3_commission_rec_rabi'].'",
				edited_by = "'. $_SESSION['username'] .'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_3_loan_limit['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.2 m.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"3.2 m.Data Saved");	
				}
				
				if($_SESSION['user_type']=='bm'){
					$update= 'update survey_invoice set
						bm_status = "1"
						where sno="'.$_POST['survey_id'].'"';
					execute_query($update);
					if(mysqli_error($db)){
						//echo mysqli_error($db).$sql;
						$data[] = array("id"=>"error", "error"=>"BM Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"BM Data Saved");	
					}
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
			case 7:{
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from survey_invoice_sec_5_new where survey_id="'.$_POST['survey_id'].'" ';
				$res_5_new = execute_query($sql);
				if(mysqli_num_rows($res_5_new)==1){
					$row_5_new = mysqli_fetch_assoc($res_5_new);
				}
				else{
					$sql = 'insert into survey_invoice_sec_5_new (survey_id) values ("'.$_POST['survey_id'].'")';
					execute_query($sql);
					$row_5_new['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_5_new set
				samiti_vs_sadasya = "'.$_POST['sec_5_new_samiti_vs_sadasya'].'",
				recovery_amt = "'.$_POST['sec_5_new_recovery_amt'].'",
				monthly_recovery = "'.$_POST['sec_5_new_monthly_recovery'].'",
				arrears = "'.$_POST['sec_5_new_arrears'].'",
				remain_amt = "'.$_POST['sec_5_new_remain_amt'].'",
				month_cur = "'.$_POST['sec_5_new_month_cur'].'",
				gradual_recovery = "'.$_POST['sec_5_new_gradual_recovery'].'",
				deposit_center = "'.$_POST['sec_5_new_deposit_center'].'",
				deposit_amt = "'.$_POST['sec_5_new_deposit_amt'].'",
				reconciled_dcb = "'.$_POST['sec_5_new_reconciled_dcb'].'",
				edition_time="'.date("Y-m-d H:i:s").'"
				
				where sno = '.$row_5_new['sno'];
				execute_query($sql);
				if(mysqli_error($db)){
					$data[] = array("id"=>"error", "error"=>"55. Unable to save data.");
				}
				else{
					$data[] = array("id"=>"update", "msg"=>"55. Data saved.");
				}
				if (isset($_POST['debtor_count'])) {

					$sql = 'DELETE FROM survey_invoice_sec_8_1 WHERE survey_id="' . $_POST['survey_id'] . '"';
					execute_query($sql);

					for ($i = 1; $i <= $_POST['debtor_count']; $i++) {
						$sql = 'INSERT INTO survey_invoice_sec_8_1 (survey_id, debtor_name, father_name, mobile_number, loan_given_date, loan_outstanding_date, outstanding_amount, action_taken, details, reason, creation_time) VALUES ("' . $_POST['survey_id'] . '", "' . $_POST['debtor_name_' . $i] . '", "' . $_POST['father_name_' . $i] . '", "' . $_POST['mobile_number_' . $i] . '", "' . $_POST['loan_given_date_' . $i] . '", "' . $_POST['loan_outstanding_date_' . $i] . '", "' . $_POST['outstanding_amount_' . $i] . '", "' . $_POST['action_taken_' . $i] . '", "' . $_POST['details_' . $i] . '", "' . $_POST['reason_' . $i] . '", "' . date("Y-m-d H:i:s") . '")';

						execute_query($sql);
						if (mysqli_error($db)) {
							$data[] = array("id" => "error", "error" => "1 lakh not saved.Unable to save data.");
						} else {
							$data[] = array("id" => "update", "msg" => "1 lakh saved. Data saved.");
						}
					}
				} else {
					$data[] = array("id" => "error", "error" => "Debtor count not provided.");
				}
				
				break;
			}
			case 8:{
				$sql = 'select * from survey_invoice where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['society_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));
				
				$sql = 'select * from survey_invoice_sec_new_8 where survey_id="'.$_POST['survey_id'].'"';
				$res_3_loan_limit = execute_query($sql);
				if(mysqli_num_rows($res_3_loan_limit)==1){
					$row_8_loan_limit = mysqli_fetch_assoc($res_3_loan_limit);
				}
				else{
					$sql = 'insert into survey_invoice_sec_new_8 (survey_id) values("'.$_POST['survey_id'].'")';
					execute_query($sql);
					if(mysqli_error($db)){
						//echo mysqli_error($db);
						$data[] = array("id"=>"error", "error"=>"9,10.Unable to save data.");
					}
					else{
						$data[] = array("id"=>"Update", "msg"=>"9,10.Data Saved");	
					}
					$row_8_loan_limit['sno'] = mysqli_insert_id($db);
				}
				$sql = 'update survey_invoice_sec_new_8 set
				govt_program = "'.$_POST['sec_new_8_govt_program'].'",
				other_description = "'.$_POST['sec_new_8_other_description'].'",
				hw_secure = "'.$_POST['sec_new_8_hw_secure'].'",
				go_live = "'.$_POST['sec_new_8_go_live'].'",
				balance_sheet = "'.$_POST['sec_new_8_balance_sheet'].'",
				day_end = "'.$_POST['sec_new_8_day_end'].'",
				last_day_end_date = "'.$_POST['sec_new_8_last_day_end_date'].'",
				fin_year = "'.$_POST['sec_new_8_fin_year'].'",
				inspection_officer = "'.$_POST['sec_new_8_inspection_officer'].'",
				inspection_date = "'.$_POST['sec_new_8_inspection_date'].'",
				last_inspection_date = "'.$_POST['sec_new_8_last_inspection_date'].'",
				remarks = "'.$_POST['sec_new_8_remarks'].'",
				compliance = "'.$_POST['sec_new_8_compliance'].'",
				edited_by = "'. $_SESSION['username'] .'",
				edition_time = "'.date("Y-m-d H:i:s").'"
				where sno="'.$row_8_loan_limit['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"sec-9,10.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-9,10.Data Saved");	
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
