<?php
date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");
error_reporting(E_ALL);
ini_set("display_errors", 1);
// echo '<pre>';
// print_r($_FILES);
// echo '</pre>';
$q = htmlspecialchars(urldecode(strtoupper($_REQUEST["term"])), ENT_QUOTES);
if (!$q)
	return;

if (isset($_REQUEST['id'])) {
	$id = $_REQUEST['id'];
} else {
	$id = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	try {
		saveApexHumanResource($_POST, $_FILES);
	} catch (\Exception $exception) {
		echo $exception->getMessage();
	}

	saveApexWorkProfession($_POST);
	saveApexFinancialInfo($_POST);
	saveApexEmptyLandInfo($_POST, $_FILES);
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
		$sql = 'INSERT INTO `apex_si_1_1` (`apex_id`,`longitude`,`latitude`,`email_id`,`society_registration_no`,`society_registration_date`, `pan_no`, `tan_no`, `gst_no`, `mobile_number`, `website`) VALUES ("' . $_POST['apex_code'] . '","' . $_POST['longitude'] . '","' . $_POST['latitude'] . '","' . $_POST['email_id'] . '","' . $_POST['society_registration_no'] . '","' . $_POST['society_registration_date'] . '", "' . $_POST['pan_no'] . '", "' . $_POST['tan_no'] . '", "' . $_POST['gst_no'] . '", "' . $_POST['mobile_number'] . '","' . $_POST['website'] . '")';
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
				if (isset($_POST['survey_id']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

					$survey_id = $_POST['survey_id'];

					/* ===== DELETE OLD DATA ===== */
					execute_query('DELETE FROM apex_zone_details WHERE survey_id="' . $survey_id . '"');
					execute_query('DELETE FROM apex_prakhand_details WHERE survey_id="' . $survey_id . '"');

					/* ===== FETCH APEX FOR IMAGE PATH ===== */
					$sql = 'SELECT apex.* 
							FROM apex_si_1_1 
							LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id 
							WHERE apex_si_1_1.sno="' . $survey_id . '"';

					$res = execute_query($sql);
					$apex = mysqli_fetch_assoc($res);

					/* ================= ZONE INSERT ================= */
					if (isset($_POST['zone_name']) && is_array($_POST['zone_name'])) {

						foreach ($_POST['zone_name'] as $k => $v) {

							$zone_name = trim($_POST['zone_name'][$k]);
							$zone_mobile = trim($_POST['zone_mobile'][$k]);
							$zone_email = trim($_POST['zone_email'][$k]);
							$zone_address = trim($_POST['zone_address'][$k]);

							// Skip fully empty row
							if ($zone_name == '' && $zone_mobile == '' && $zone_email == '' && $zone_address == '') {
								continue;
							}

							$zone_image_name = '';

							if (isset($_FILES['zone_image']['name'][$k]) && $_FILES['zone_image']['name'][$k] != '') {

								$file_array = [
									'name' => $_FILES['zone_image']['name'][$k],
									'type' => $_FILES['zone_image']['type'][$k],
									'tmp_name' => $_FILES['zone_image']['tmp_name'][$k],
									'error' => $_FILES['zone_image']['error'][$k],
									'size' => $_FILES['zone_image']['size'][$k],
								];

								$zone_image = upload_img($file_array, $apex, "zone_" . $survey_id . "_" . $k);

								if ($zone_image['error'] == 1) {
									$zone_image_name = $zone_image['file_name'];
								}
							}

							execute_query('INSERT INTO apex_zone_details
								(survey_id,zone_name,zone_mobile,zone_email,zone_address,zone_image)
								VALUES
								("' . $survey_id . '",
								"' . $zone_name . '",
								"' . $zone_mobile . '",
								"' . $zone_email . '",
								"' . $zone_address . '",
								"' . $zone_image_name . '")');
						}
					}

					/* ================= PRAKHAND INSERT ================= */
					if (isset($_POST['prakhand_name']) && is_array($_POST['prakhand_name'])) {

						foreach ($_POST['prakhand_name'] as $k => $v) {

							$name = trim($_POST['prakhand_name'][$k]);
							$mobile = trim($_POST['prakhand_mobile'][$k]);
							$email = trim($_POST['prakhand_email'][$k]);
							$address = trim($_POST['prakhand_address'][$k]);

							if ($name == '' && $mobile == '' && $email == '' && $address == '') {
								continue;
							}

							$prakhand_image_name = '';

							if (isset($_FILES['prakhand_image']['name'][$k]) && $_FILES['prakhand_image']['name'][$k] != '') {

								$file_array = [
									'name' => $_FILES['prakhand_image']['name'][$k],
									'type' => $_FILES['prakhand_image']['type'][$k],
									'tmp_name' => $_FILES['prakhand_image']['tmp_name'][$k],
									'error' => $_FILES['prakhand_image']['error'][$k],
									'size' => $_FILES['prakhand_image']['size'][$k],
								];

								$prakhand_image = upload_img($file_array, $apex, "prakhand_" . $survey_id . "_" . $k);

								if ($prakhand_image['error'] == 1) {
									$prakhand_image_name = $prakhand_image['file_name'];
								}
							}

							execute_query('INSERT INTO apex_prakhand_details
								(survey_id,prakhand_name,prakhand_mobile,prakhand_email,prakhand_address,prakhand_image)
								VALUES
								("' . $survey_id . '",
								"' . $name . '",
								"' . $mobile . '",
								"' . $email . '",
								"' . $address . '",
								"' . $prakhand_image_name . '")');
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
                    website = "' . $_POST['website'] . '"
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
					$sql = 'INSERT INTO `survey_invoice_new_sec_6_2`(`survey_id`, `mgt_committee_is_elected`, `election_year`) VALUES ("' . $_POST['survey_id'] . '","' . $_POST['sec_6_2_mgt_committee_is_elected'] . '", "' . $_POST['sec_6_2_election_year'] . '")';
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
				accumulated_1 = "' . $_POST['sec_3_accumulated_1'] . '",
				profit_loss_2 = "' . $_POST['sec_3_profit_loss_2'] . '",
				accumulated_2 = "' . $_POST['sec_3_accumulated_2'] . '",
				profit_loss_3 = "' . $_POST['sec_3_profit_loss_3'] . '",
				accumulated_3 = "' . $_POST['sec_3_accumulated_3'] . '",
				financial_audit_year = "' . $_POST['sec_3_financial_audit_year'] . '",
				audit_grading = "' . $_POST['sec_3_audit_grading'] . '",
				compliance_status = "' . $_POST['sec_3_compliance_status'] . '",
				agm_year = "' . $_POST['sec_3_agm_year'] . '",
				dividend_year = "' . $_POST['sec_3_dividend_year'] . '",
				dividend_per = "' . $_POST['sec_3_dividend_per'] . '",
				dividend_amt = "' . $_POST['sec_3_dividend_amt'] . '",
				edition_time = "' . date("Y-m-d H:i:s") . '"
				where sno="' . $row_3_new_1['sno'] . '"';
				execute_query($sql);
				if (mysqli_error($db)) {
					//echo mysqli_error($db).$sql;
					$data[] = array("id" => "error", "error" => "3.1.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "sec-3.1. Data Saved");
				}

				// $sql = 'delete from survey_invoice_sec_2_1_2 where survey_id="' . $_POST['survey_id'] . '"';
				// execute_query($sql);
				// if (mysqli_error($db)) {
				// 	$data[] = array("id" => "error", "error" => "4.Unable to save data.");
				// }
				// for ($i = 1; $i <= $_POST['other_business_id']; $i++) {
				// 	$sql = 'insert into survey_invoice_sec_2_1_2 (survey_id, other_description, other_amount, edition_time) values ("' . $_POST['survey_id'] . '", "' . $_POST['sec_2_1_2_business_description_' . $i] . '", "' . $_POST['sec_2_1_2_value_' . $i] . '", "' . date('Y-m-d H:i:s') . '")';
				// 	execute_query($sql);
				// 	if (mysqli_error($db)) {
				// 		$data[] = array("id" => "error", "error" => "4.Unable to save data.");
				// 	} else {
				// 		$data[] = array("id" => "update", "msg" => "4. Data saved.");
				// 	}

				// }

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
							working_name, working_period, contract_no, contract_name, created_at
						) VALUES (
							'" . mysqli_real_escape_string($db, $_POST['survey_id']) . "',
							'" . mysqli_real_escape_string($db, $post_id) . "',
							'{$sanctioned_post}', '{$vacant_post}',
							'{$working_name}', '{$working_period}',
							'{$contract_no}', '{$contract_name}',
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

				$sql = 'DELETE FROM apex_work_profession_info WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "4.1.Unable to delete old data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "4.1.Old Data Cleared");
				}

				$totalRows = intval($_POST['other_business_id'] ?? 0);
				for ($i = 1; $i <= $totalRows; $i++) {
					$year = $_POST["business_year_$i"] ?? '';
					$desc = $_POST["business_description_$i"] ?? '';
					$turnover = $_POST["business_turnover_$i"] ?? '';
					$target = $_POST["business_target_$i"] ?? '';
					$achievement = $_POST["business_achievement_$i"] ?? '';

					// Skip empty row
					if (empty($year) && empty($desc)) {
						continue;
					}

					$sql = 'INSERT INTO apex_work_profession_info (survey_id,apex_code,business_year,business_description,business_turnover,business_target,business_achievement) VALUES ("' . $_POST['survey_id'] . '","' . $_POST['apex_code'] . '","' . $year . '","' . $desc . '","' . $turnover . '","' . $target . '","' . $achievement . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "4.2.Unable to save row $i data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "4.2.Row $i Data Saved");
					}
				}
				break;
			}
			case 4: {
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
						"' . date('Y-m-d H:i:s') . '"
					)';
				}

				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Unable to save data."];
				} else {
					$data[] = ["id" => "Update", "msg" => "Data saved successfully."];
				}

				// $sql = 'DELETE FROM survey_invoice_sec_3_4 WHERE survey_id="' . $_POST['survey_id'] . '"';
				// execute_query($sql);  // Function to execute the query

				// for ($i = 1; $i <= $_POST['sec_2_nirmit_godown_id']; $i++) {
				// 	$storage_capacity = isset($_POST['sec_3_b_storage_capacity_' . $i]) ? $_POST['sec_3_b_storage_capacity_' . $i] : '';
				// 	$godown_year = isset($_POST['sec_3_b_godown_year_' . $i]) ? $_POST['sec_3_b_godown_year_' . $i] : '';
				// 	$wdra_certified = isset($_POST['sec_3_b_wdra_certified_' . $i]) ? $_POST['sec_3_b_wdra_certified_' . $i] : '';
				// 	$type_of_fund = isset($_POST['sec_3_b_godown_type_of_fund_' . $i]) ? $_POST['sec_3_b_godown_type_of_fund_' . $i] : '';
				// 	$godown_status = isset($_POST['sec_3_b_godown_status_' . $i]) ? $_POST['sec_3_b_godown_status_' . $i] : '';  // Use isset to check if the key is defined
				// 	$comment = isset($_POST['sec_3_b_godown_comment_' . $i]) ? $_POST['sec_3_b_godown_comment_' . $i] : '';

				// 	// SQL query for insertion
				// 	$sql = 'INSERT INTO survey_invoice_sec_3_4 (storage_capacity, godown_year, wdra_certified, type_of_fund, construction_status, remarks, survey_id)
				// 				VALUES ("' . $storage_capacity . '", "' . $godown_year . '", "' . $wdra_certified . '", "' . $type_of_fund . '", "' . $godown_status . '", "' . $comment . '", "' . $_POST['survey_id'] . '")';
				// 	execute_query($sql);
				// }

				// $sql = 'delete from survey_invoice_sec_3_5 where survey_id="' . $_POST['survey_id'] . '"';
				// execute_query($sql);
				// for ($i = 1; $i <= $_POST['sec_3_c_id']; $i++) {
				// 	$sql = 'insert into survey_invoice_sec_3_5 (survey_id, land_type, location, total_area, approach_road,suitable_godown, rak_distance,  edition_time) values("' . $_POST['survey_id'] . '", "' . $_POST['sec_3_c_land_location_' . $i] . '",  "' . $_POST['sec_3_c_paved_road_' . $i] . '", "' . date("Y-m-d H:i:s") . '")';
				// 	// echo $sql;
				// 	execute_query($sql);
				// 	if (mysqli_error($db)) {
				// 		//echo mysqli_error($db);
				// 		$data[] = array("id" => "error", "error" => "7.7.Unable to save data.");
				// 	} else {
				// 		$data[] = array("id" => "Update", "msg" => "7.7.Data Saved");
				// 	}
				// 	$row_3_5['sno'] = mysqli_insert_id($db);
				// }

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
		}
	}
}

if (empty($data) != true) {
	echo json_encode($data);
}


function upload_img($name, $society, $new_name, $maxDim = 1500)
{
    // Validate first
    if (!isset($name) || $name['error'] !== UPLOAD_ERR_OK) {
        return ["error" => 0, "msg" => "File upload error: " . $name['error'], "file_name" => ""];
    }

    if (!file_exists($name['tmp_name']) || !is_uploaded_file($name['tmp_name'])) {
        return ["error" => 0, "msg" => "Temporary file missing.", "file_name" => ""];
    }

    $imageFileType = strtolower(pathinfo($name['name'], PATHINFO_EXTENSION));

    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return ["error" => 0, "msg" => "Only JPG, JPEG, PNG & GIF files are allowed.", "file_name" => ""];
    }

    if ($name['size'] > 50000000) {
        return ["error" => 0, "msg" => "File is too large.", "file_name" => ""];
    }

    $imgInfo = getimagesize($name['tmp_name']);
    if ($imgInfo === false) {
        return ["error" => 0, "msg" => "Invalid image file.", "file_name" => ""];
    }

    $file_name = $name['tmp_name'];
    $width     = $imgInfo[0];
    $height    = $imgInfo[1];

    // Resize if needed
    if ($width > $maxDim || $height > $maxDim) {
        $ratio      = $width / $height;
        $new_width  = $ratio > 1 ? (int) round($maxDim) : (int) round($maxDim * $ratio);
        $new_height = $ratio > 1 ? (int) round($maxDim / $ratio) : (int) round($maxDim);

        $src = imagecreatefromstring(file_get_contents($file_name));
        $dst = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($src);
        imagejpeg($dst, $file_name);
        imagedestroy($dst);
    }

    $target_dir  = __DIR__ . '/../user_data/' . $society['sno'] . '/';
    $target_file = $target_dir . basename($new_name) . '.' . $imageFileType;

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($name['tmp_name'], $target_file)) {
        return ["error" => 1, "msg" => "Uploaded Successfully.", "file_name" => basename($new_name) . '.' . $imageFileType];
    } else {
        return ["error" => 0, "msg" => "Error uploading file.", "file_name" => ""];
    }
}

function saveApexHumanResource($post, $files)
{
	$conn = new mysqli("localhost", "root", "mysql", "upcdc_2025");

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	/* ===============================
	   STEP 1: Get survey_id
	================================ */

	$apex_code = intval($post['apex_code']);

	if (!$apex_code) {
		return false;
	}

	$res = $conn->query("SELECT sno FROM apex_si_1_1 WHERE apex_id = $apex_code LIMIT 1");

	if (!$res || $res->num_rows == 0) {
		return false;
	}

	$row = $res->fetch_assoc();
	$survey_id = $row['sno'];

	/* ===============================
	   STEP 2: Delete old records
	================================ */

	$conn->query("DELETE FROM apex_human_resource_info WHERE survey_id = $survey_id");

	/* ===============================
	   STEP 3: Collect POST arrays
	================================ */

	$staff_type = $post['staff_type'] ?? [];
	$hr_post_id = $post['post_id'] ?? [];
	$sanctioned_post = $post['sanctioned_post'] ?? [];
	$vacant_post = $post['vacant_post'] ?? [];

	$staff_post_id = $post['staff_post_name'] ?? [];
	$staff_name = $post['staff_name'] ?? [];
	$staff_sthiti = $post['staff_sthiti'] ?? [];
	$staff_father = $post['staff_father'] ?? [];
	$staff_dob = $post['staff_dob'] ?? [];
	$staff_mobile = $post['staff_mobile'] ?? [];
	$staff_qualification = $post['staff_qualification'] ?? [];

	/* ===============================
	STEP 4: Upload Image
	 ================================ */

	// Base path (your required folder)
	$basePath = "D:/htdocs/Upcdc/upcdc.in/user_data/";

	// Create staff folder inside it (example: staff_1)
	$uploadDir = $basePath . "staff_" . $survey_id . "/";

	// Create folder if it does not exist
	if (!is_dir($uploadDir)) {
		mkdir($uploadDir, 0755, true);
	}

	if (!is_dir($uploadDir)) {
		mkdir($uploadDir, 0755, true);
	}

	$staff_images = $files['staff_image'] ?? [];

	$totalStaff = count($staff_name);

	$hrIndex = 0;
	$staffCounter = 0;

	/* ===============================
	   STEP 5: Insert Loop
	================================ */

	$existing_images = $post['existing_staff_image'] ?? [];

	for ($i = 0; $i < $totalStaff; $i++) {

		if (empty($staff_name[$i])) {
			continue;
		}

		if ($staffCounter >= ($sanctioned_post[$hrIndex] ?? 0)) {
			$staffCounter = 0;
			$hrIndex++;
		}

		$imageName = "";

		// Case 1: New image uploaded

		$imageName = "";

		// Case 1: New image uploaded
		if (!empty($staff_images['name'][$i]) && $staff_images['error'][$i] == 0) {

			$allowedTypes = ['jpg', 'jpeg', 'png', 'png'];
			$ext = strtolower(pathinfo($staff_images['name'][$i], PATHINFO_EXTENSION));

			if (!in_array($ext, $allowedTypes)) {
				continue; // skip invalid file
			}

			$tmpPath = $staff_images['tmp_name'][$i];
			$newName = time() . "_" . rand(1000, 9999) . ".jpg";
			$targetPath = $uploadDir . $newName;

			// Create image resource
			if ($ext == 'png') {
				$image = imagecreatefrompng($tmpPath);
			} else {
				$image = imagecreatefromjpeg($tmpPath);
			}

			$width = imagesx($image);
			$height = imagesy($image);

			// Resize (max width 400px)
			$newWidth = 400;
			$newHeight = ($height / $width) * $newWidth;

			$newImage = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

			// Compress dynamically to max 120KB
			$quality = 85;
			do {
				imagejpeg($newImage, $targetPath, $quality);
				$fileSize = filesize($targetPath);
				$quality -= 5;
			} while ($fileSize > (120 * 1024) && $quality > 40);

			imagedestroy($image);
			imagedestroy($newImage);

			$imageName = $newName;
		} else {
			// Keep old image
			$imageName = $existing_images[$i] ?? "";
		}

		/* ==== Insert ==== */

		$stmt = $conn->prepare("
            INSERT INTO apex_human_resource_info (
                survey_id,
                apex_code,
                staff_type,
                hr_post_id,
                sanctioned_post,
                vacant_post,
                staff_post_id,
                staff_name,
                staff_sthiti,
                staff_father,
                staff_dob,
                staff_mobile,
                staff_qualification,
                staff_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

		$stmt->bind_param(
			"iisiiiisssssss",
			$survey_id,
			$apex_code,
			$staff_type[$hrIndex],
			$hr_post_id[$hrIndex],
			$sanctioned_post[$hrIndex],
			$vacant_post[$hrIndex],
			$staff_post_id[$i],
			$staff_name[$i],
			$staff_sthiti[$i],
			$staff_father[$i],
			$staff_dob[$i],
			$staff_mobile[$i],
			$staff_qualification[$i],
			$imageName
		);

		$stmt->execute();
		$stmt->close();

		$staffCounter++;
	}

	$conn->close();

	return true;
}
function saveApexWorkProfession($post)
{
    $conn = new mysqli("localhost", "root", "mysql", "upcdc_2025");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $apex_code = intval($post['apex_code']);
    $survey_id = intval($post['survey_id']);

    if (!$apex_code || !$survey_id) {
        return false;
    }

    // 🔥 Delete old records (important for edit case)
    $conn->query("DELETE FROM apex_work_profession_info WHERE survey_id = $survey_id");

    $totalRows = intval($post['other_business_id'] ?? 0);

    for ($i = 1; $i <= $totalRows; $i++) {

        $year = $post["business_year_$i"] ?? '';
        $desc = $post["business_description_$i"] ?? '';
        $turnover = $post["business_turnover_$i"] ?? '';
        $target = $post["business_target_$i"] ?? '';
        $achievement = $post["business_achievement_$i"] ?? '';

        // Skip empty row
        if (empty($year) && empty($desc)) {
            continue;
        }

        $stmt = $conn->prepare("
            INSERT INTO apex_work_profession_info (
                survey_id,
                apex_code,
                business_year,
                business_description,
                business_turnover,
                business_target,
                business_achievement
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iisssss",
            $survey_id,
            $apex_code,
            $year,
            $desc,
            $turnover,
            $target,
            $achievement
        );

        $stmt->execute();
        $stmt->close();
    }

    $conn->close();

    return true;
}

function saveApexFinancialInfo($post)
{
    global $db;

    $survey_id = intval($post['survey_id'] ?? 0);

    if (!$survey_id) return;

    $sql = 'DELETE FROM apex_financial_info WHERE survey_id="' . $survey_id . '"';
    execute_query($sql);

    $index = 1;
    while (isset($post['sec_3_profit_loss_' . $index])) {

        $yearLabel = $post['financial_year_label_' . $index] ?? '';

        if (empty($yearLabel)) {
            $index++;
            continue;
        }

        $sql = 'INSERT INTO apex_financial_info (survey_id,financial_year,annual_status,annual_gross,annual_net,accumulated_status,accumulated_gross,accumulated_net
                ) VALUES ("' . $survey_id . '","' . $yearLabel . '","' . ($post['sec_3_profit_loss_'      . $index] ?? '') . '","' . ($post['sec_3_gross_amount_'     . $index] ?? '') . '","' . ($post['sec_3_net_amount_'       . $index] ?? '') . '","' . ($post['sec_3_accumulated_'      . $index] ?? '') . '","' . ($post['sec_3_acc_gross_amount_' . $index] ?? '') . '", "' . ($post['sec_3_acc_net_amount_'   . $index] ?? '') . '")';
        execute_query($sql);

        $index++;
    }

    return true;
}


function saveApexEmptyLandInfo($post, $files)
{
    global $db;

    $apex_code = intval($post['apex_code'] ?? 0);

    if (!$apex_code) return false;

    $sql = 'SELECT sno FROM apex_si_1_1 WHERE apex_id="' . $apex_code . '" LIMIT 1';
    $res = execute_query($sql);
    $row = mysqli_fetch_assoc($res);
    $survey_id = intval($row['sno']);

    $sql = 'DELETE FROM apex_empty_land_info WHERE survey_id="' . $survey_id . '"';
    execute_query($sql);

    $totalRows = intval($post['sec_3_c_id'] ?? 0);

    $basePath  = "D:/htdocs/Upcdc/upcdc.in/user_data/";
    $uploadDir = $basePath . "empty_land_" . $survey_id . "/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    for ($i = 1; $i <= $totalRows; $i++) {

        $district = $post['sec_3_c_district_'      . $i] ?? '';
        $area     = $post['sec_3_c_area_'          . $i] ?? '';
        $road     = $post['sec_3_c_paved_road_'    . $i] ?? '';
        $location = $post['sec_3_c_land_location_' . $i] ?? '';

        if ($district == '' && $area == '' && $road == '' && $location == '') {
            continue;
        }

        $imageName = $post['sec_3_c_existing_image_' . $i] ?? '';

        if (isset($files['sec_3_c_image_' . $i]) && $files['sec_3_c_image_' . $i]['error'] == 0) {

            $allowedTypes = ['jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($files['sec_3_c_image_' . $i]['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowedTypes)) {

                $tmpPath    = $files['sec_3_c_image_' . $i]['tmp_name'];
                $newName    = time() . "_" . rand(1000, 9999) . ".jpg";
                $targetPath = $uploadDir . $newName;

                $image = ($ext == "png") ? imagecreatefrompng($tmpPath) : imagecreatefromjpeg($tmpPath);

                if ($image) {
                    $width     = imagesx($image);
                    $height    = imagesy($image);
                    $newWidth  = 400;
                    $newHeight = (int)(($height / $width) * $newWidth);

                    $newImage = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagejpeg($newImage, $targetPath, 85);
                    imagedestroy($image);
                    imagedestroy($newImage);

                    $imageName = $newName;
                }
            }
        }

        $sql = 'INSERT INTO apex_empty_land_info (survey_id,sec_3_c_district,sec_3_c_area,sec_3_c_paved_road,sec_3_c_land_location,sec_3_c_image
                ) VALUES ("' . $survey_id . '","' . $district  . '","' . $area      . '","' . $road      . '","' . $location  . '","' . $imageName . '")';
        execute_query($sql);
    }

    return true;
}