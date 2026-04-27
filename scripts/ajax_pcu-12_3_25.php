<?php
date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$q = isset($_REQUEST["term"]) ? htmlspecialchars(urldecode(strtoupper($_REQUEST["term"])), ENT_QUOTES) : '';
if (!$q)
	return;
if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
} else {
	$id = '';
}

foreach ($_POST as $k => $v) {
	if (is_array($v)) {
		foreach ($v as $key => $val) {
			$_POST[$k][$key] = htmlspecialchars($val);
		}
	} else {
		$_POST[$k] = htmlspecialchars($v);
	}
}

$data = array();

if ($id == 'type') {
	$sql = 'select * from master_society_type';
	$result = execute_query($sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = array("id" => $row['sno'], "type_name" => $row['type_name'], "status" => $row['status']);
	}
} elseif ($id == 'society') {
	$sql = 'select * from test2 where col1="' . $_POST['division'] . '" and col2="' . $_POST['district'] . '" and col5="' . $_POST['tehseel'] . '" and col6="' . $_POST['block'] . '" and col3="1" and (status!="1" or status is null)';
	//echo $sql;
	//echo $sql;
	$result = execute_query($sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = array("id" => $row['sno'], "society_name" => "B-PACS " . $row['col4']);
	}
} elseif($id=='verify_otp'){
	// print_r($_POST);
	$survey_id = intval($_POST['val']);
	$res = execute_query('SELECT 1 FROM survey_invoice_validation WHERE survey_id="'.$survey_id.'" AND status=2 LIMIT 1');
	if (mysqli_num_rows($res) > 0) {
		$data[] = array("status"=>"completed","msg"=>"अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है");
	} else {
		$res = execute_query('SELECT sno,mobile_number FROM survey_invoice WHERE sno="'.$survey_id.'"');
		if (mysqli_num_rows($res) == 0) {
			$data[] = array("status"=>"notfound","msg"=>"Data not found");
		} else {
			$row = mysqli_fetch_assoc($res);
			execute_query('INSERT INTO survey_invoice_validation (survey_id,user_type,mobile_number,otp_verify,ip_address,http_referer,http_user_agent,approval_status,status,creation_time) VALUES ("'.$row['sno'].'","secretary","'.$row['mobile_number'].'",1,"'.$_SERVER['REMOTE_ADDR'].'","'.($_SERVER['HTTP_REFERER'] ?? '').'","'.($_SERVER['HTTP_USER_AGENT'] ?? '').'","approve",2,"'.date("Y-m-d H:i:s").'")');
			$request_id = mysqli_insert_id($db);
			execute_query('INSERT INTO survey_invoice_validation (survey_id,request_id,user_id,user_type,mobile_number,ip_address,http_referer,http_user_agent,approval_status,creation_time) VALUES ("'.$row['sno'].'","'.$request_id.'","'.$_SESSION['user_id'].'","ado","'.$row['mobile_number'].'","'.$_SERVER['REMOTE_ADDR'].'","'.($_SERVER['HTTP_REFERER'] ?? '').'","'.($_SERVER['HTTP_USER_AGENT'] ?? '').'","approve","'.date("Y-m-d H:i:s").'")');
			execute_query('UPDATE survey_invoice SET approval_status=2,otp_verify=1 WHERE sno="'.$row['sno'].'"');
			$data[] = array("status"=>"verified","msg"=>"आपका परिपत्र सफलता पूर्वक अग्रिम कार्यवाही हेतु प्रेषित कर दिया गया है");
		}
	}
} elseif ($id == 'submit_form_pcu') {
	//print_r($_POST);
	//print_r($_SERVER);
	// echo $_POST['apex_code'];
	// echo $_POST['survey_id'];
	// echo $id;
	// echo '----------------------';
	
	if ($_POST['survey_id'] == '') {

        $sql = 'INSERT INTO `apex_si_1_1` (`apex_id`,`longitude`,`latitude`,`committee_status`,`email_id`,`society_registration_no`,`society_registration_date`,`address`,`pincode`,`pan_no`,`tan_no`,`mobile_number`,`website`,`membership_fee`,`nominal_member`,`lifetime_member`, total_members, liquidation, liquidation_date, liquidation_status) VALUES ("' . $_POST['apex_code'] . '","' . $_POST['longitude'] . '","' . $_POST['latitude'] . '","' . $_POST['committee_status'] . '","' . $_POST['email_id'] . '","' . $_POST['society_registration_no'] . '","' . $_POST['society_registration_date'] . '","' . $_POST['address'] . '","' . $_POST['pincode'] . '","' . $_POST['pan_no'] . '","' . $_POST['tan_no'] . '","' . $_POST['mobile_number'] . '","' . $_POST['website'] . '","' . $_POST['membership_fee'] . '","' . $_POST['nominal_member'] . '","' . $_POST['lifetime_member'] .'","' . $_POST['total_members'] .'","'. $_POST['liquidation'].'", "'.($_POST['liquidation_date']).'", "'.($_POST['liquidation_status']).'" )';
		execute_query($sql);
		if (mysqli_error($db)) {
			$data[] = array("id" => "error", "error" => "Error# " . mysqli_error($db) . ' >> ' . $sql);
		} else {
			$id = mysqli_insert_id($db);
			$data[] = array("id" => $id);
		}
    }
	else{	
		// echo $_POST['apex_code'];
		switch($_POST['current_step_count']){
			case 0:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$apex_si_1_1 = mysqli_fetch_assoc(execute_query($sql));
				
				// $sql = 'select * from apex where sno="'.$apex_si_1_1['apex_id'].'"';
				// $society = mysqli_fetch_assoc(execute_query($sql));
				// if($_FILES['photo_id']['name']!=''){
				// 	$society_image = upload_img($_FILES['photo_id'], $society, "society_name_".$apex_si_1_1['sno']);
				// 	//print_r($society_image);
				// 	if($society_image['error']==1){
				// 		$sql = 'update apex_si_1_1 set 
				// 		photo_id="'.$society_image['file_name'].'"
				// 		where sno="'.$_POST['survey_id'].'"';
				// 		execute_query($sql);
				// 		$data[] = array("id"=>"Update", "msg"=>$society_image['msg']);
				// 	}
				// 	else{
				// 		$data[] = array("id"=>"error", "error"=>$society_image['msg']);
				// 	}
				// }
				$sql = 'UPDATE apex_si_1_1 SET 
                    edited_by = "",
                    edition_time = "'.date("Y-m-d H:i:s").'",
                    apex_id = "'.$_POST['apex_code'].'",
                    latitude = "'.$_POST['latitude'].'",
                    longitude = "'.$_POST['longitude'].'",
                    committee_status = "'.$_POST['committee_status'].'",
                    email_id = "'.$_POST['email_id'].'",
                    society_registration_no = "'.$_POST['society_registration_no'].'",
                    society_registration_date = "'.$_POST['society_registration_date'].'",
                    address = "'.$_POST['address'].'",
                    pincode = "'.$_POST['pincode'].'",
                    pan_no = "'.$_POST['pan_no'].'",
                    tan_no = "'.$_POST['tan_no'].'",
                    website = "'.$_POST['website'].'",
                    membership_fee = "'.$_POST['membership_fee'].'",
                    nominal_member = "'.$_POST['nominal_member'].'",
                    lifetime_member = "'.$_POST['lifetime_member'].'",
                    liquidation = "'.$_POST['liquidation'].'",
                    liquidation_date = "'.$_POST['liquidation_date'].'",
                    liquidation_status = "'.$_POST['liquidation_status'].'",
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

				$sql = 'delete from survey_trans_new_sec_2_stock where survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				$sql = 'SELECT `sno`, `type_name` FROM `stock_item_type`';
				$res_stock_item_type = execute_query($sql);
				$t = 1;
				while ($row_stock_item_type = mysqli_fetch_assoc($res_stock_item_type)) {

					$id_type = $row_stock_item_type['sno'];

					$sql = 'SELECT `sno`, `stock_item_type_id`, `item_name` FROM `stock_item_des` WHERE stock_item_type_id="' . $row_stock_item_type['sno'] . '"';
					$res_stock_item_des = execute_query($sql);
					$d = 1;

					if (mysqli_num_rows($res_stock_item_des) > 0) {

						while ($row_stock_item_des = mysqli_fetch_assoc($res_stock_item_des)) {
							$id_des = $row_stock_item_des['sno'];

							$sql = 'INSERT INTO survey_trans_new_sec_2_stock(survey_id, invoice_id, stock_item_type_id, stock_item_des_id, closing_stock_1, book_value_1, closing_stock_2, book_value_2) 
								VALUES (
								"' . $_POST['survey_id'] . '",
								"' . $row_2['sno'] . '",
								"' . $id_type . '",
								"' . $id_des . '",
								"' . $_POST['closing_stock_1_' . $id_type . '_' . $id_des] . '",
								"' . $_POST['book_value_1_' . $id_type . '_' . $id_des] . '", 
								"' . $_POST['closing_stock_2_' . $id_type . '_' . $id_des] . '",
								"' . $_POST['book_value_2_' . $id_type . '_' . $id_des] . '")';
							execute_query($sql);
							if (mysqli_error($db)) {
								//echo mysqli_error($db);
								$data[] = array("id" => "error", "error" => "sec-2.Unable to save data.");
							} else {
								$data[] = array("id" => "Update", "msg" => "sec-2.Data Saved");
							}

						}
					} else {

						$sql = 'INSERT INTO survey_trans_new_sec_2_stock(survey_id, invoice_id, stock_item_type_id, closing_stock_1, book_value_1, closing_stock_2, book_value_2) VALUES ("' . $_POST['survey_id'] . '","' . $row_2['sno'] . '","' . $id_type . '","' . $_POST['closing_stock_1_' . $id_type] . '","' . $_POST['book_value_1_' . $id_type] . '","' . $_POST['closing_stock_2_' . $id_type] . '","' . $_POST['book_value_2_' . $id_type] . '")';
						execute_query($sql);
						if (mysqli_error($db)) {
							//echo mysqli_error($db);
							$data[] = array("id" => "error", "error" => "sec2.Unable to save data.");
						} else {
							$data[] = array("id" => "Update", "msg" => "sec2.Data Saved");
						}

					}
				}
				
				$sql = 'delete from survey_invoice_new_sec_2_1 where survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				for ($i = 1; $i <= 10; $i++) {
					$sql = 'insert into survey_invoice_new_sec_2_1 (survey_id, invoice_id, item_name, item_description, book_value) values("' . $_POST['survey_id'] . '","' . $row_2['sno'] . '", "' . $_POST['scraped_item_name_' . $i] . '", "' . $_POST['scraped_item_description_' . $i] . '", "' . $_POST['book_value_' . $i] . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						//echo mysqli_error($db);
						$data[] = array("id" => "error", "error" => "2.1.Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "2.1.Data Saved");
					}
				}		

				$sql = 'delete from survey_invoice_new_sec_2_2 where survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				for ($i = 1; $i <= 10; $i++) {
					$sql = 'insert into survey_invoice_new_sec_2_2 (survey_id,invoice_id, item_name, item_description, scheme_name, date, purchase_value, quantity) values("' . $_POST['survey_id'] . '","' . $row_2['sno'] . '", "' . $_POST['item_name_' . $i] . '", "' . $_POST['item_description_' . $i] . '", "' . $_POST['scheme_name_' . $i] . '", "' . $_POST['date_' . $i] . '", "' . $_POST['purchase_value_' . $i] . '", "' . $_POST['quantity_' . $i] . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						//echo mysqli_error($db);
						$data[] = array("id" => "error", "error" => "2.2.Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "2.2.Data Saved");
					}
				}

				break;
			}
			case 2:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from apex where sno="'.$survey_invoice['apex_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));

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
				$sql = 'update survey_invoice_sec_3_new_1 set
				profit_loss_1 = "' . $_POST['sec_3_profit_loss_1'] . '",
				profit_loss_amount_1 = "' . $_POST['sec_3_profit_loss_amount_1'] . '",
				accumulated_1 = "' . $_POST['sec_3_accumulated_1'] . '",
				accumulated_amount_1 = "' . $_POST['sec_3_accumulated_amount_1'] . '",
				profit_loss_2 = "' . $_POST['sec_3_profit_loss_2'] . '",
				profit_loss_amount_2 = "' . $_POST['sec_3_profit_loss_amount_2'] . '",
				accumulated_2 = "' . $_POST['sec_3_accumulated_2'] . '",
				accumulated_amount_2 = "' . $_POST['sec_3_accumulated_amount_2'] . '",
				profit_loss_3 = "' . $_POST['sec_3_profit_loss_3'] . '",
				profit_loss_amount_3 = "' . $_POST['sec_3_profit_loss_amount_3'] . '",
				accumulated_3 = "' . $_POST['sec_3_accumulated_3'] . '",
				accumulated_amount_3 = "' . $_POST['sec_3_accumulated_amount_3'] . '",
				financial_audit_year = "' . $_POST['sec_3_financial_audit_year'] . '",
				audit_grading = "' . $_POST['sec_3_audit_grading'] . '",
				compliance_status = "' . $_POST['sec_3_compliance_status'] . '",
				agm_year = "' . $_POST['sec_3_agm_year'] . '",
				dividend_year = "' . $_POST['sec_3_dividend_year'] . '",
				dividend_per = "' . $_POST['sec_3_dividend_per'] . '",
				dividend_amt = "' . $_POST['sec_3_dividend_amt'] . '",
				santulan_patra = "' . $_POST['sec_3_santulan_patra'] . '",
				edition_time = "' . date("Y-m-d H:i:s") . '"
				where sno="' . $row_3_new_1['sno'] . '"';
				execute_query($sql);
				if (mysqli_error($db)) {
					//echo mysqli_error($db).$sql;
					$data[] = array("id" => "error", "error" => "3.1.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "sec-3.1. Data Saved");
				}


                // $survey_id = isset($_POST['survey_id']) ? mysqli_real_escape_string($db, $_POST['survey_id']) : '';
                // $sql = 'SELECT * FROM msy_data WHERE survey_id = "' . $survey_id . '"';
                // $res_msy = execute_query($sql);

                // if (mysqli_num_rows($res_msy) == 1) {
                //     $row_msy = mysqli_fetch_assoc($res_msy);
                //     $sno = $row_msy['sno'];
                // } else {
                //     // insert a new row
                //     $sql = 'INSERT INTO msy_data (survey_id, edition_time) VALUES("' . $survey_id . '", "' . date("Y-m-d H:i:s") . '")';
                //     execute_query($sql);
                //     if (mysqli_error($db)) {
                //         $data[] = array("id" => "error", "error" => "MSY. Unable to create record.");
                //         // stop further processing (optional)
                //     } else {
                //         $data[] = array("id" => "Update", "msg" => "MSY record created");
                //     }
                //     // get inserted id
                //     $sno = mysqli_insert_id($db);
                //     // if insert failed to return id, try to fetch the row
                //     if (empty($sno)) {
                //         $sql = 'SELECT * FROM msy_data WHERE survey_id = "' . $survey_id . '"';
                //         $res_msy = execute_query($sql);
                //         if (mysqli_num_rows($res_msy) == 1) {
                //             $row_msy = mysqli_fetch_assoc($res_msy);
                //             $sno = $row_msy['sno'];
                //         } else {
                //             // still no row — bail out
                //             $data[] = array("id" => "error", "error" => "MSY. Record not found after insert.");
                //         }
                //     }
                // }
                // function gpost($key, $db) {
                //     return isset($_POST[$key]) ? mysqli_real_escape_string($db, $_POST[$key]) : '';
                // }
                // $assign = [];

                // $assign[] = 'msy_1_target_1 = "' . gpost('msy_1_target_1', $db) . '"';
                // $assign[] = 'msy_1_supply_1 = "' . gpost('msy_1_supply_1', $db) . '"';
                // $assign[] = 'msy_1_member_no_1 = "' . gpost('msy_1_member_no_1', $db) . '"';
                // $assign[] = 'msy_1_payment_to_farmer_1 = "' . gpost('msy_1_payment_to_farmer_1', $db) . '"';
                // $assign[] = 'msy_1_target_2 = "' . gpost('msy_1_target_2', $db) . '"';
                // $assign[] = 'msy_1_supply_2 = "' . gpost('msy_1_supply_2', $db) . '"';
                // $assign[] = 'msy_1_member_no_2 = "' . gpost('msy_1_member_no_2', $db) . '"';
                // $assign[] = 'msy_1_payment_to_farmer_2 = "' . gpost('msy_1_payment_to_farmer_2', $db) . '"';

                // for ($i = 1; $i <= 4; $i++) {
                //     $assign[] = 'msy_2_target_' . $i . ' = "' . gpost('msy_2_target_' . $i, $db) . '"';
                //     $assign[] = 'msy_2_supply_' . $i . ' = "' . gpost('msy_2_supply_' . $i, $db) . '"';
                //     $assign[] = 'msy_2_member_no_' . $i . ' = "' . gpost('msy_2_member_no_' . $i, $db) . '"';
                //     $assign[] = 'msy_2_payment_to_farmer_' . $i . ' = "' . gpost('msy_2_payment_to_farmer_' . $i, $db) . '"';
                // }

				
				break;
			}
			case 3:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="'.$survey_invoice['apex_id'].'"';
				$society = mysqli_fetch_assoc(execute_query($sql));

                $sql = 'DELETE FROM survey_invoice_sec_2_1_2 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "4.Unable to save data."];
				}

				for ($i = 1; $i <= $_POST['other_business_id']; $i++) {
					$desc  = $_POST['sec_2_1_2_business_description_' . $i] ?? '';
					$value = $_POST['sec_2_1_2_value_' . $i] ?? '';
					$profit_loss = $_POST['sec_2_1_2_profit_loss_' . $i] ?? '';
					if ($desc == '' && $value == '') {
						continue;
					}

					$sql = 'INSERT INTO survey_invoice_sec_2_1_2 
						(survey_id, other_description, other_amount, profit_loss, edition_time)
						VALUES (
							"' . $_POST['survey_id'] . '",
							"' . $desc . '",
							"' . $value . '",
							"' . $profit_loss . '",
							"' . date('Y-m-d H:i:s') . '"
						)';

					execute_query($sql);

					if (mysqli_error($db)) {
						$data[] = ["id" => "error", "error" => "4.Unable to save data."];
					}
				}
				$sql = 'SELECT * FROM apex_si_2_2 WHERE survey_id="' . $_POST['survey_id'] . '"';
				$res_3 = execute_query($sql);

				if (mysqli_num_rows($res_3) == 1) {

					$row_3 = mysqli_fetch_assoc($res_3);

					// Update parent
					$sql = 'UPDATE apex_si_2_2 SET updated_at = NOW() WHERE sno=' . $row_3['sno'];
					execute_query($sql);

					if (mysqli_error($db)) {

						$data[] = ["id" => "error", "error" => "3.B Unable to update section parent data."];

					} else {

						$data[] = ["id" => "Update", "msg" => "3.B Parent updated."];

						// Delete existing children
						$sql = 'DELETE FROM apex_si_2_2_b WHERE survey_id="' . $_POST['survey_id'] . '"';
						execute_query($sql);

						// Insert new children
						for ($i = 1; $i <= $_POST['sec_3_b_id']; $i++) {

							// Skip blank rows
							if (trim($_POST['sec_3_b_name_' . $i]) == '' &&
								trim($_POST['sec_3_b_type_' . $i]) == '') {
								continue;
							}

							$sql = 'INSERT INTO apex_si_2_2_b 
									(`survey_id`, `sec_3_b_id`, `office_type`, `name`, `division`,
									`district`, `tehsil`, `address`, `mobile`, `email`,
									`pincode`, `latitude`, `longitude`)
									VALUES (
										"' . $_POST['survey_id'] . '",
										"' . $row_3['sno'] . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_type_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_name_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_division_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_district_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_tehsil_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_address_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_mobile_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_email_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_pincode_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_latitude_' . $i]) . '",
										"' . mysqli_real_escape_string($db, $_POST['sec_2_b_longitude_' . $i]) . '"
									)';

							execute_query($sql);

							if (mysqli_error($db)) {
								$data[] = ["id" => "error", "error" => "3.B Unable to insert row " . $i];
							} else {
								$data[] = ["id" => "Update", "msg" => "3.B Row " . $i . " saved successfully"];
							}
						}
					}
				}
				
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

				$sql = 'DELETE FROM training_centers WHERE survey_id="' . $survey_id . '"';
				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "5.2 Unable to delete old data."];
					break;
				}

				/* =========================
				INSERT NEW ROWS
				========================== */
				$row_count = (int)($_POST['sec_3_row_count'] ?? 0);

				for ($i = 1; $i <= $row_count; $i++) {

					// Skip empty rows
					if (
						trim($_POST['sec_3_cpmt_' . $i] ?? '') == '' &&
						trim($_POST['sec_3_post_' . $i] ?? '') == ''
					) {
						continue;
					}

					$sql = 'INSERT INTO training_centers SET
						survey_id                     = "' . $survey_id . '",
						row_no                        = "' . $i . '",

						cpmt_name                     = "' . mysqli_real_escape_string($db, $_POST['sec_3_cpmt_' . $i]) . '",
						address                       = "' . mysqli_real_escape_string($db, $_POST['sec_3_address_' . $i]) . '",
						principal_name                = "' . mysqli_real_escape_string($db, $_POST['sec_3_principal_name_' . $i]) . '",
						post                           = "' . mysqli_real_escape_string($db, $_POST['sec_3_post_' . $i]) . '",

						principal_house               = "' . ($_POST['sec_3_principal_house_' . $i] ?? '') . '",
						principal_house_no            = "' . ($_POST['sec_3_principal_house_no_' . $i] ?? '') . '",

						principal_office              = "' . ($_POST['sec_3_principal_office_' . $i] ?? '') . '",
						principal_office_no           = "' . ($_POST['sec_3_principal_office_no_' . $i] ?? '') . '",

						class_no                      = "' . ($_POST['sec_3_class_no_' . $i] ?? '') . '",
						class_capacity                = "' . ($_POST['sec_3_class_capacity_' . $i] ?? '') . '",

						hostel_no                     = "' . ($_POST['sec_3_hostel_no_' . $i] ?? '') . '",
						hostel_capacity               = "' . ($_POST['sec_3_hostel_capacity_' . $i] ?? '') . '",

						library_no                    = "' . ($_POST['sec_3_library_no_' . $i] ?? '') . '",
						library_capacity              = "' . ($_POST['sec_3_library_capacity_' . $i] ?? '') . '",

						computer_lab_no               = "' . ($_POST['sec_3_computer_lab_no_' . $i] ?? '') . '",
						computer_lab_capacity         = "' . ($_POST['sec_3_computer_lab_capacity_' . $i] ?? '') . '",

						teacher_no                    = "' . ($_POST['sec_3_teacher_no_' . $i] ?? '') . '",
						employee_remarks              = "' . mysqli_real_escape_string($db, $_POST['sec_3_employee_remarks_' . $i]) . '",

						training_sessions_no          = "' . ($_POST['sec_3_training_sessions_no_' . $i] ?? '') . '",
						training_subject_name         = "' . mysqli_real_escape_string($db, $_POST['sec_3_training_subject_name_' . $i]) . '",
						training_sessions_duration    = "' . ($_POST['sec_3_training_sessions_duration_' . $i] ?? '') . '",

						departmental_trainees_no      = "' . ($_POST['sec_3_departmental_trainees_no_' . $i] ?? 0) . '",
						non_departmental_trainees_no  = "' . ($_POST['sec_3_non_departmental_trainees_no_' . $i] ?? 0) . '",
						trainees_no                   = "' . ($_POST['sec_3_trainees_no_' . $i] ?? 0) . '",

						departmental_trainees_fee     = "' . ($_POST['sec_3_departmental_trainees_fee_' . $i] ?? 0) . '",
						non_departmental_trainees_fee = "' . ($_POST['sec_3_non_departmental_trainees_fee_' . $i] ?? 0) . '",
						trainees_fee                  = "' . ($_POST['sec_3_trainees_fee_' . $i] ?? 0) . '",

						departmental_hostel_fee       = "' . ($_POST['sec_3_departmental_hostel_fee_' . $i] ?? 0) . '",
						non_departmental_hostel_fee   = "' . ($_POST['sec_3_non_departmental_hostel_fee_' . $i] ?? 0) . '",
						hostel_fee                    = "' . ($_POST['sec_3_hostel_fee_' . $i] ?? 0) . '",

						construction_year             = "' . ($_POST['sec_3_build_year_' . $i] ?? '') . '",
						operational_year              = "' . ($_POST['sec_3_operation_year_' . $i] ?? '') . '",

						center_ref_id                 = "' . ($_POST['sec_3_training_center_' . $i] ?? '') . '",
						staff_count                   = "' . ($_POST['sec_3_staff_type_' . $i] ?? '') . '",

						training_course_benefits      = "' . mysqli_real_escape_string($db, $_POST['sec_3_training_course_benefits_' . $i]) . '",
						building_hostel_status        = "' . mysqli_real_escape_string($db, $_POST['sec_3_building_hostel_status_' . $i]) . '",

						edition_time                  = "' . date('Y-m-d H:i:s') . '"
					';

					execute_query($sql);

					if (mysqli_error($db)) {
						$data[] = ["id" => "error", "error" => "5.2 Row $i save failed"];
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
