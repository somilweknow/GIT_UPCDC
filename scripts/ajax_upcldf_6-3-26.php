<?php
date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");
error_reporting(E_ALL);
ini_set("display_errors", 1);

$q = htmlspecialchars(urldecode(strtoupper($_REQUEST["term"])), ENT_QUOTES);
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
} elseif ($id == 'submit_form_upcldf') {
	// print_r($_POST);
	//print_r($_SERVER);
	// echo $_POST['apex_code'];
	// echo $_POST['survey_id'];
	if ($_POST['survey_id'] == '') {
		$sql = 'INSERT INTO `apex_si_1_1` (`apex_id`,`longitude`,`latitude`,`email_id`,`society_registration_no`,`society_registration_date`, `pan_no`, `tan_no`, `gst_no`, `mobile_number`, `website`, `indivisual_members`, `committee_members`, `central_soc_members`, `primary_soc_members`) VALUES ("' . $_POST['apex_code'] . '","' . $_POST['longitude'] . '","' . $_POST['latitude'] . '","' . $_POST['email_id'] . '","' . $_POST['society_registration_no'] . '","' . $_POST['society_registration_date'] . '", "' . $_POST['pan_no'] . '", "' . $_POST['tan_no'] . '", "' . $_POST['gst_no'] . '", "' . $_POST['mobile_number'] . '","' . $_POST['website'] . '","' . $_POST['indivisual_members'] . '","' . $_POST['committee_members'] . '","' . $_POST['central_soc_members'] . '","' . $_POST['primary_soc_members'] . '")';
		// echo $_POST['apex_id'];
		execute_query($sql);
		if (mysqli_error($db)) {
			$data[] = array("id" => "error", "error" => "Error# " . mysqli_error($db) . ' >> ' . $sql);
		} else {
			$id = mysqli_insert_id($db);
			$data[] = array("id" => $id);
		}

		$sql = 'select * from apex_si_1_1 where sno="' . $id . '"';
		$survey_invoice = mysqli_fetch_assoc(execute_query($sql));

		$sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
		$apex = mysqli_fetch_assoc(execute_query($sql));
		if (isset($_FILES['society_photo']) && !empty($_FILES['society_photo']['name'])) {
			$society_image = upload_img($_FILES['society_photo'], $apex, "society_name_" . $id);
			//print_r($society_image);
			if ($society_image['error'] == 1) {
				$sql = 'update apex_si_1_1 set 
				photo_id="' . $society_image['file_name'] . '"
				where sno="' . $id . '"';
				execute_query($sql);
				$data[] = array("id" => "Update", "msg" => $society_image['msg']);
			} else {
				$data[] = array("id" => "error", "error" => $society_image['msg']);
			}
		}
	} else {
		// echo $_POST['apex_code'];
		switch ($_POST['current_step_count']) {
			case 0: {
				$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
				$apex = mysqli_fetch_assoc(execute_query($sql));
				if (isset($_FILES['society_photo']) && !empty($_FILES['society_photo']['name'])) {
					$society_image = upload_img($_FILES['society_photo'], $apex, "society_name_" . $_POST['survey_id']);
					//print_r($society_image);
					$society_image = upload_img($_FILES['society_photo'], $apex, "society_name_" . $_POST['survey_id']);
					if ($society_image['error'] == 1) {
						$db_path = 'user_data/' . $apex['sno'] . '/' . $society_image['file_name'];
						$sql = 'update apex_si_1_1 set photo_id="' . $db_path . '" where sno="' . $_POST['survey_id'] . '"';
						execute_query($sql);
					} else {
						$data[] = array("id" => "error", "error" => $society_image['msg']);
					}
				}
				if(isset($_POST['survey_id']) && $_SERVER['REQUEST_METHOD'] == 'POST'){

					$survey_id = $_POST['survey_id'];

					/* ===== DELETE OLD DATA ===== */
					execute_query('DELETE FROM apex_zone_details WHERE survey_id="'.$survey_id.'"');
					execute_query('DELETE FROM apex_prakhand_details WHERE survey_id="'.$survey_id.'"');

					/* ===== FETCH APEX FOR IMAGE PATH ===== */
					$sql = 'SELECT apex.* 
							FROM apex_si_1_1 
							LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id 
							WHERE apex_si_1_1.sno="'.$survey_id.'"';

					$res = execute_query($sql);
					$apex = mysqli_fetch_assoc($res);

					/* ================= ZONE INSERT ================= */
					if(isset($_POST['zone_name']) && is_array($_POST['zone_name'])){

						foreach($_POST['zone_name'] as $k=>$v){

							$zone_name   = trim($_POST['zone_name'][$k]);
							$zone_mobile = trim($_POST['zone_mobile'][$k]);
							$zone_email  = trim($_POST['zone_email'][$k]);
							$zone_address= trim($_POST['zone_address'][$k]);

							// Skip fully empty row
							if($zone_name=='' && $zone_mobile=='' && $zone_email=='' && $zone_address==''){
								continue;
							}

							$zone_image_name = '';

							if(isset($_FILES['zone_image']['name'][$k]) && $_FILES['zone_image']['name'][$k] != ''){

								$file_array = [
									'name'     => $_FILES['zone_image']['name'][$k],
									'type'     => $_FILES['zone_image']['type'][$k],
									'tmp_name' => $_FILES['zone_image']['tmp_name'][$k],
									'error'    => $_FILES['zone_image']['error'][$k],
									'size'     => $_FILES['zone_image']['size'][$k],
								];

								$zone_image = upload_img($file_array, $apex, "zone_".$survey_id."_".$k);

								if($zone_image['error'] == 1){
									$zone_image_name = $zone_image['file_name'];
								}
							}

							execute_query('INSERT INTO apex_zone_details
								(survey_id,zone_name,zone_mobile,zone_email,zone_address,zone_image)
								VALUES
								("'.$survey_id.'",
								"'.$zone_name.'",
								"'.$zone_mobile.'",
								"'.$zone_email.'",
								"'.$zone_address.'",
								"'.$zone_image_name.'")');
						}
					}

					/* ================= PRAKHAND INSERT ================= */
					if(isset($_POST['prakhand_name']) && is_array($_POST['prakhand_name'])){

						foreach($_POST['prakhand_name'] as $k=>$v){

							$name    = trim($_POST['prakhand_name'][$k]);
							$mobile  = trim($_POST['prakhand_mobile'][$k]);
							$email   = trim($_POST['prakhand_email'][$k]);
							$address = trim($_POST['prakhand_address'][$k]);

							if($name=='' && $mobile=='' && $email=='' && $address==''){
								continue;
							}

							$prakhand_image_name = '';

							if(isset($_FILES['prakhand_image']['name'][$k]) && $_FILES['prakhand_image']['name'][$k] != ''){

								$file_array = [
									'name'     => $_FILES['prakhand_image']['name'][$k],
									'type'     => $_FILES['prakhand_image']['type'][$k],
									'tmp_name' => $_FILES['prakhand_image']['tmp_name'][$k],
									'error'    => $_FILES['prakhand_image']['error'][$k],
									'size'     => $_FILES['prakhand_image']['size'][$k],
								];

								$prakhand_image = upload_img($file_array, $apex, "prakhand_".$survey_id."_".$k);

								if($prakhand_image['error'] == 1){
									$prakhand_image_name = $prakhand_image['file_name'];
								}
							}

							execute_query('INSERT INTO apex_prakhand_details
								(survey_id,prakhand_name,prakhand_mobile,prakhand_email,prakhand_address,prakhand_image)
								VALUES
								("'.$survey_id.'",
								"'.$name.'",
								"'.$mobile.'",
								"'.$email.'",
								"'.$address.'",
								"'.$prakhand_image_name.'")');
						}
					}
				}
				$sql = 'UPDATE apex_si_1_1 SET 
                    edited_by = "",
                    edition_time = "' . date("Y-m-d H:i:s") . '",
                    apex_id = "' . $_POST['apex_code'] . '",
                    latitude = "' . $_POST['latitude'] . '",
                    longitude = "' . $_POST['longitude'] . '",
                    email_id = "' . $_POST['email_id'] . '",
                    society_registration_no = "' . $_POST['society_registration_no'] . '",
                    society_registration_date = "' . $_POST['society_registration_date'] . '",
                    pan_no = "' . $_POST['pan_no'] . '",
                    tan_no = "' . $_POST['tan_no'] . '",
                    gst_no = "' . $_POST['gst_no'] . '",
                    mobile_number = "' . $_POST['mobile_number'] . '",
                    website = "' . $_POST['website'] . '",
                    indivisual_members = "' . $_POST['indivisual_members'] . '",
                    committee_members = "' . $_POST['committee_members'] . '",
                    central_soc_members = "' . $_POST['central_soc_members'] . '",
                    primary_soc_members = "' . $_POST['primary_soc_members'] . '"
                    WHERE sno = ' . $_POST['survey_id'];
				execute_query($sql);
				if (mysqli_error($db)) {
					//echo mysqli_error($db);
					$data[] = array("id" => "error", "error" => "sec-1,1.1.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "1,1.1.Data Saved");
				}

				break;
			}
			case 1: {
				$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				execute_query('DELETE d FROM apex_si_1_4_contact_districts d INNER JOIN apex_si_1_4_contacts c ON c.sno = d.contact_id WHERE c.survey_id = "' . mysqli_real_escape_string($db, $_POST['survey_id']) . '" ');
				execute_query('DELETE FROM apex_si_1_4_contacts WHERE survey_id = "' . mysqli_real_escape_string($db, $_POST['survey_id']) . '" ');

				$data[] = ["id" => "update", "msg" => "Contact: Old rows deleted"];
				$division_ids = $_POST['division_id'] ?? [];
				$addresses = $_POST['address'] ?? [];
				$mobiles = $_POST['mobile'] ?? [];
				$emails = $_POST['email'] ?? [];
				$latitudes = $_POST['latitude'] ?? [];
				$longitudes = $_POST['longitude'] ?? [];
				$districtMap = $_POST['district_name'] ?? [];


				$total_rows = count($division_ids);

				for ($i = 0; $i < $total_rows; $i++) {
					$sql_contact = "INSERT INTO apex_si_1_4_contacts SET
					survey_id   = '" . mysqli_real_escape_string($db, $_POST['survey_id']) . "',
					division_id = '" . intval($division_ids[$i]) . "',
					address     = '" . mysqli_real_escape_string($db, $addresses[$i] ?? '') . "',
					mobile      = '" . mysqli_real_escape_string($db, $mobiles[$i] ?? '') . "',
					email       = '" . mysqli_real_escape_string($db, $emails[$i] ?? '') . "',
					latitude    = '" . mysqli_real_escape_string($db, $latitudes[$i] ?? '') . "',
					longitude   = '" . mysqli_real_escape_string($db, $longitudes[$i] ?? '') . "'";
					execute_query($sql_contact);

					if (mysqli_error($db)) {
						$data[] = ["id" => "error", "error" => "Contact row save failed"];
						continue;
					}

					$contact_id = mysqli_insert_id($db);
					$row_no = $i + 1;
					if (!empty($districtMap[$row_no]) && is_array($districtMap[$row_no])) {

						foreach ($districtMap[$row_no] as $dist_id) {

							if (empty($dist_id))
								continue;

							$sql_dist = "
                    INSERT INTO apex_si_1_4_contact_districts SET
                        contact_id = '" . $contact_id . "',
                        district_id = '" . intval($dist_id) . "'
                ";
							execute_query($sql_dist);
						}
					}
				}

				$data[] = ["id" => "Update", "msg" => "Contact & District data saved successfully"];

				$sql = 'select * from survey_invoice_new_sec_6_2 where survey_id="' . $_POST['survey_id'] . '"';
				$res_6_2 = execute_query($sql);
				if (mysqli_num_rows($res_6_2) == 1) {
					$row_6_2 = mysqli_fetch_assoc($res_6_2);

					$sql = 'update survey_invoice_new_sec_6_2 set 
					mgt_committee_is_elected="' . $_POST['sec_6_2_mgt_committee_is_elected'] . '",
					election_year="' . $_POST['sec_6_2_election_year'] . '",
					end_year="' . $_POST['sec_6_2_end_year'] . '"
					
					where sno=' . $row_6_2['sno'];
					execute_query($sql);

					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "6.2 Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "6.2Data Saved");

						$sql = 'delete from survey_invoice_new_sec_6_2_1 where survey_id="' . $_POST['survey_id'] . '"';
						execute_query($sql);

						for ($i = 1; $i <= $_POST['sec_6_2_id']; $i++) {
							$sql = 'insert into survey_invoice_new_sec_6_2_1 (`survey_id`, `sec_6_2_id`, `designation`, `full_name`, `mobile_no`) values("' . $_POST['survey_id'] . '", "' . $row_6_2['sno'] . '", "' . $_POST['sec_6_2_designation_' . $i] . '", "' . $_POST['sec_6_2_name_' . $i] . '","' . $_POST['sec_6_2__mob_no_' . $i] . '")';
							execute_query($sql);
							if (mysqli_error($db)) {
								//echo mysqli_error($db);
								$data[] = array("id" => "error", "error" => "6.2.Unable to save data.");
							} else {
								$data[] = array("id" => "Update", "msg" => "6.2.Data Saved");
							}
						}

					}
				} else {
					$sql = 'INSERT INTO `survey_invoice_new_sec_6_2`(`survey_id`, `mgt_committee_is_elected`, `election_year`, `mgt_committee_resolution_no`) VALUES ("' . $_POST['survey_id'] . '","' . $_POST['sec_6_2_mgt_committee_is_elected'] . '", "' . $_POST['sec_6_2_election_year'] . '", "' . $_POST['sec_6_2_mgt_committee_resolution_no'] . '")';
					execute_query($sql);
					$row_6_2['sno'] = mysqli_insert_id($db);
					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "6.2Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "6.2Data Saved");

						for ($i = 1; $i <= $_POST['sec_6_2_id']; $i++) {
							$sql = 'insert into survey_invoice_new_sec_6_2_1 (`survey_id`, `sec_6_2_id`, `designation`, `full_name`, `mobile_no`) values("' . $_POST['survey_id'] . '", "' . $row_6_2['sno'] . '", "' . $_POST['sec_6_2_designation_' . $i] . '", "' . $_POST['sec_6_2_name_' . $i] . '", "' . $_POST['sec_6_2__mob_no_' . $i] . '")';
							execute_query($sql);
							if (mysqli_error($db)) {
								//echo mysqli_error($db);
								$data[] = array("id" => "error", "error" => "6.2.Unable to save data.");
							} else {
								$data[] = array("id" => "Update", "msg" => "6.2.Data Saved");
							}
						}
					}
				}
				break;
			}
			case 2: {
				$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
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

				$sql = 'delete from survey_invoice_sec_2_1_2 where survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "4.Unable to save data.");
				}
				for ($i = 1; $i <= $_POST['other_business_id']; $i++) {
					$sql = 'insert into survey_invoice_sec_2_1_2 (survey_id, other_description, other_amount, edition_time) values ("' . $_POST['survey_id'] . '", "' . $_POST['sec_2_1_2_business_description_' . $i] . '", "' . $_POST['sec_2_1_2_value_' . $i] . '", "' . date('Y-m-d H:i:s') . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "4.Unable to save data.");
					} else {
						$data[] = array("id" => "update", "msg" => "4. Data saved.");
					}

				}

				$sql_human_delete = 'DELETE FROM apex_si_6_3 WHERE survey_id = "' . mysqli_real_escape_string($db, $_POST['survey_id']) . '"';
				execute_query($sql_human_delete);

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Human Resource: Unable to delete existing rows."];
				} else {
					$data[] = ["id" => "update", "msg" => "Human Resource: Existing rows deleted."];
				}

				$sanctioned_posts = $_POST['sanctioned_post'] ?? [];
				$vacant_posts = $_POST['vacant_post'] ?? [];
				$working_names = $_POST['working_name'] ?? [];
				$working_periods = $_POST['working_period'] ?? [];
				$contract_numbers = $_POST['contract_no'] ?? [];
				$contract_names = $_POST['contract_name'] ?? [];

				foreach ($sanctioned_posts as $post_id => $sanctioned_post) {

					$sanctioned_post = mysqli_real_escape_string($db, $sanctioned_post ?? '');
					$vacant_post = mysqli_real_escape_string($db, $vacant_posts[$post_id] ?? '');
					$working_name = mysqli_real_escape_string($db, $working_names[$post_id] ?? '');
					$working_period = mysqli_real_escape_string($db, $working_periods[$post_id] ?? '');
					$contract_no = mysqli_real_escape_string($db, $contract_numbers[$post_id] ?? '');
					$contract_name = mysqli_real_escape_string($db, $contract_names[$post_id] ?? '');

					if (
						$sanctioned_post === '' &&
						$vacant_post === '' &&
						$working_name === '' &&
						$working_period === '' &&
						$contract_no === '' &&
						$contract_name === ''
					)
						continue;

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
			case 3: {
				$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from test2 where sno="' . $survey_invoice['apex_id'] . '"';
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

				$sql = 'select * from  survey_invoice_plot_details where survey_id="' . $_POST['survey_id'] . '"';
				$res_new_plot = execute_query($sql);
				if (mysqli_num_rows($res_new_plot) == 1) {
					$row_new_plot = mysqli_fetch_assoc($res_new_plot);
				} else {
					$sql = 'insert into  survey_invoice_plot_details (survey_id) values("' . $_POST['survey_id'] . '")';
					execute_query($sql);
					$row_new_plot['sno'] = mysqli_insert_id($db);
					if (mysqli_error($db)) {
						//echo mysqli_error($db);
						$data[] = array("id" => "error", "error" => "3.1.123.Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "sec-3.1.123.Data Saved");
					}
				}
				$sql = 'UPDATE survey_invoice_plot_details SET
					plot_area = "' . $_POST['sec_new_plot_area'] . '",
					plot_revenue_status = "' . $_POST['sec_new_plot_revenue_status'] . '",
					plot_reason_for_not_record = "' . $_POST['sec_new_plot_reason_for_not_record'] . '",
					plot_practices_if_not = "' . $_POST['sec_new_plot_practices_if_not'] . '",
					plot_gata_no = "' . $_POST['sec_new_plot_gata_no'] . '",
					sec_3_ownership = "' . $_POST['sec_3_ownership'] . '",
					society_building_area = "' . $_POST['sec_3_building_area'] . '",
					society_building_rent_amount = "' . $_POST['sec_3_building_rent'] . '",
					society_building_remark = "' . $_POST['sec_3_remark'] . '",
					remarks = "' . $_POST['sec_new_remarks'] . '"
				WHERE sno = "' . $row_new_plot['sno'] . '"';
				execute_query($sql);
				if (mysqli_error($db)) {
					//echo mysqli_error($db).$sql;
					$data[] = array("id" => "error", "error" => "3.1.124.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "sec-3.1.124. Data Saved");
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
			case 4: {

				$sql = 'select * from survey_invoice_new_sec_8 where survey_id="' . $_POST['survey_id'] . '"';
				$res_sec_8 = execute_query($sql);
				if (mysqli_num_rows($res_sec_8) == 1) {
					$row_8 = mysqli_fetch_assoc($res_sec_8);
				} else {
					$sql = 'insert into survey_invoice_new_sec_8 (survey_id) values("' . $_POST['survey_id'] . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "21.Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "21.Data Saved");
					}

					$row_8['sno'] = mysqli_insert_id($db);
				}
				//print_r($_POST);
				if (!isset($_POST['sec_8_select_internet_operator'])) {
					$_POST['sec_8_select_internet_operator'] = array();
				}
				$sql = 'UPDATE `survey_invoice_new_sec_8` SET 
				`electrical_connection`= "' . $_POST['sec_8_electrical_connection'] . '",
				`electrical_connection_working`= "' . $_POST['sec_8_electrical_connection_working'] . '",
				`bill_paid_yes_no`= "' . $_POST['sec_8_bill_paid_yes_no'] . '",
				`electricity_not_available_reason`= "' . $_POST['sec_8_electricity_not_available_reason'] . '",
				`electricity_not_available_remark`= "' . $_POST['sec_8_electricity_not_available_remark'] . '",
				`bill_not_paid_month`= "' . $_POST['sec_8_bill_not_paid_month'] . '",
				`outstanding_amount`= "' . $_POST['sec_8_outstanding_amount'] . '",
				
				`solar_connection`= "' . $_POST['sec_8_solar_connection'] . '",
				`solar_work_status`= "' . $_POST['sec_8_solar_work_status'] . '",
				`solar_bill_paid`= "' . $_POST['sec_8_solar_bill_paid'] . '",
				
				`internet_connection`= "' . $_POST['sec_8_internet_connection'] . '",
				`internet_service_provider`= "' . $_POST['sec_8_internet_service_provider'] . '",
				`internet_bill_paid`= "' . $_POST['sec_8_internet_bill_paid'] . '",
				`select_internet_operator`= "' . ($_POST['sec_8_internet_connection'] == 'yes' ? $_POST['sec_8_internet_service_provider'] : implode(", ", $_POST['sec_8_select_internet_operator'])) . '",
				
				`narrow_tubes`= "' . $_POST['sec_8_narrow_tubes'] . '",
				`water_tank`= "' . $_POST['sec_8_water_tank'] . '",
				`samarsabel`= "' . $_POST['sec_8_samarsabel'] . '",
				`handpump`= "' . $_POST['sec_8_handpump'] . '"			
				where sno=' . $row_8['sno'];
				// echo $sql;
				execute_query($sql);
				if (mysqli_error($db)) {
					//echo mysqli_error($db).$sql;
					$data[] = array("id" => "error", "error" => "8.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "8.Data Saved");
				}

				break;
			}
		}
	}
}

if (empty($data) != true) {
	echo json_encode($data);
}


function upload_img($file, $society, $new_name, $maxDim = 1500)
{
    // Check upload error
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            "error" => 0,
            "msg" => "File upload error: " . $file['error'],
            "file_name" => ""
        ];
    }

    // Check tmp file exists
    if (!file_exists($file['tmp_name'])) {
        return [
            "error" => 0,
            "msg" => "Temporary file missing.",
            "file_name" => ""
        ];
    }

    $allowed = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return [
            "error" => 0,
            "msg" => "Only JPG, JPEG, PNG & GIF allowed",
            "file_name" => ""
        ];
    }

    $imgInfo = getimagesize($file['tmp_name']);

    if ($imgInfo === false) {
        return [
            "error" => 0,
            "msg" => "Invalid image file.",
            "file_name" => ""
        ];
    }

    $width = $imgInfo[0];
    $height = $imgInfo[1];

    if ($height == 0) {
        return [
            "error" => 0,
            "msg" => "Invalid image dimensions.",
            "file_name" => ""
        ];
    }

    $target_dir = __DIR__ . '/../user_data/' . $society['sno'] . '/';

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . $new_name . '.' . $ext;

    $ratio = $width / $height;

    if ($width > $maxDim || $height > $maxDim) {

        if ($ratio > 1) {
            $new_width = (int) round($maxDim);
            $new_height = (int) round($maxDim / $ratio);
        } else {
            $new_height = (int) round($maxDim);
            $new_width = (int) round($maxDim * $ratio);
        }

        $src = imagecreatefromstring(file_get_contents($file['tmp_name']));
        $dst = imagecreatetruecolor($new_width, $new_height);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        if ($ext == 'png') {
            imagepng($dst, $target_file);
        } elseif ($ext == 'gif') {
            imagegif($dst, $target_file);
        } else {
            imagejpeg($dst, $target_file, 90);
        }

        imagedestroy($src);
        imagedestroy($dst);

    } else {
        move_uploaded_file($file['tmp_name'], $target_file);
    }

    return [
        "error" => 1,
        "msg" => "Uploaded Successfully",
        "file_name" => $new_name . '.' . $ext
    ];
}
?>