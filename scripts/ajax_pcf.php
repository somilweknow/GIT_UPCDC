<?php
error_reporting(E_ALL);
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

$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
$survey_invoice = mysqli_fetch_assoc(execute_query($sql));

if ($id == 'type') {
	$sql = 'select * from master_society_type';
	$result = execute_query($sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = array("id" => $row['sno'], "type_name" => $row['type_name'], "status" => $row['status']);
	}
} elseif ($id == 'society') {
	$sql = 'select * from test2 where col1="' . $_POST['division'] . '" and col2="' . $_POST['district'] . '" and col5="' . $_POST['tehseel'] . '" and col6="' . $_POST['block'] . '" and col3="1" and (status!="1" or status is null)';
	$result = execute_query($sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = array("id" => $row['sno'], "society_name" => "B-PACS " . $row['col4']);
	}
} elseif ($id == 'verify_otp_pcf') {
	$otp = randomnumber();
	$sql_validation = 'select * from apex_validation where survey_id="' . $_POST['val'] . '" and user_type = "apex" and status!=7';
	$result_validation = execute_query($sql_validation);
	if (mysqli_num_rows($result_validation) != 0) {
		$data_validation = mysqli_fetch_array($result_validation);
		if ($data_validation['status'] == "7") {
			$data[] = array("status" => "completed", "msg" => "Survey Already Completed");
		} elseif ($data_validation['status'] > "2") {
			$data[] = array("status" => "completed", "msg" => "अग्रिम कार्यवाही हेतु आपका परिपत्र प्रेषित किया जा चुका है ");
		} else {
			goto newinsert;
		}
	} else {
		newinsert:
		$sql = 'select * from apex_si_1_1 where sno="' . $_POST['val'] . '"';
		$result = execute_query($sql);
		if (mysqli_num_rows($result) != 0) {
			$row = mysqli_fetch_assoc($result);
			$sql = 'insert into apex_validation (survey_id, request_id, user_id, user_type, mobile_number, `ip_address`, `http_referer`, `http_user_agent`, `approval_status`, status, creation_time) 
                    values ("' . $row['sno'] . '", "", "' . $_SESSION['user_id'] . '", "apex.sno", "' . $row['mobile_number'] . '", "' . $_SERVER['REMOTE_ADDR'] . '", "' . $_SERVER['HTTP_REFERER'] . '", "' . $_SERVER['HTTP_USER_AGENT'] . '", "approve", 1, "' . date("Y-m-d H:i:s") . '")';
			execute_query($sql);
			if (mysqli_error($db)) {
				$data[] = array("status" => "error", "msg" => "AVF#01 : Some error occured");
			} else {
				$sql = 'update apex_si_1_1 set approval_status=1 where sno=' . $row['sno'];
				execute_query($sql);
				$msg = "आपका परिपत्र सफलता पूर्वक अग्रिम कार्यवाही हेतु प्रेषित कर दिया गया है ";
				$data[] = array("status" => "verified", "msg" => $msg);
			}
		} else {
			$data[] = array("status" => "notfound", "msg" => "Data not found");
		}
	}
} elseif ($id == 'submit_form_pcf') {

	if ($_POST['survey_id'] == '') {

		$sql = 'INSERT INTO apex_si_1_1 
        (apex_id, longitude, latitude, email_id, society_registration_no, society_registration_date, pan_no, tan_no, gst_no, mobile_number, website) 
        VALUES ("' . $_POST['apex_code'] . '","' . $_POST['longitude'] . '","' . $_POST['latitude'] . '","' . $_POST['email_id'] . '","' . $_POST['society_registration_no'] . '","' . $_POST['society_registration_date'] . '","' . $_POST['pan_no'] . '","' . $_POST['tan_no'] . '","' . $_POST['gst_no'] . '","' . $_POST['mobile_number'] . '","' . $_POST['website'] . '")';
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

                    execute_query('INSERT INTO apex_zone_details (survey_id,zone_name,zone_mobile,zone_email,zone_address,zone_image)
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
	} else {
		switch ($_POST['current_step_count']) {
			case 0: {
				$sql = 'SELECT * FROM apex_si_1_1 WHERE sno="' . $_POST['survey_id'] . '"';
				$apex_si_1_1 = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'SELECT * FROM apex WHERE sno="' . $apex_si_1_1['apex_id'] . '"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				//                if ($_FILES['society_photo']['name'] != '') {
//                    $society_image = upload_img($_FILES['society_photo'], $society, "society_name_".$apex_si_1_1['sno']);
//                    if ($society_image['error'] == 1) {
//                        $sql = 'UPDATE apex_si_1_1 SET photo_id="'.$society_image['file_name'].'" WHERE sno="'.$_POST['survey_id'].'"';
//                        execute_query($sql);
//                        $data[] = array("id" => "Update", "msg" => $society_image['msg']);
//                    } else {
//                        $data[] = array("id" => "error", "error" => $society_image['msg']);
//                    }
//                }

				$uppcfImageName = $_FILES['society_photo']['name'];
				savepcfsocietyimage($_POST, $_FILES);


				$sql = 'UPDATE apex_si_1_1 SET
                    edited_by = "",
                    edition_time = "' . date("Y-m-d H:i:s") . '",
                    apex_id = "' . $_POST['apex_code'] . '",
                    latitude = "' . $_POST['latitude'] . '",
                    longitude = "' . $_POST['longitude'] . '",
                	email_id = "' . $_POST['email_id'] . '",
                    photo_id = "' . $uppcfImageName . '",
                    society_registration_no = "' . $_POST['society_registration_no'] . '",
                    prakhand_name = "' . (isset($_POST['prakhand_name']) ? $_POST['prakhand_name'] : '') . '",
                    society_registration_date = "' . $_POST['society_registration_date'] . '",
                    pan_no = "' . $_POST['pan_no'] . '",
                    tan_no = "' . $_POST['tan_no'] . '",
                    gst_no = "' . $_POST['gst_no'] . '",
                    mobile_number = "' . $_POST['mobile_number'] . '",
                    website = "' . $_POST['website'] . '",
                    sec_4_total_personnel = "' . $_POST['sec_4_total_personnel'] . '",
                    hq_ownership = "' . $_POST['hq_ownership'] . '"
                    WHERE sno=' . $_POST['survey_id'];

				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "Data Saved");
				}

				save_zone_details($_POST['survey_id']);
				save_prakhand_details($_POST['survey_id']);
				break;
			}
			case 1: {
				$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				$sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'DELETE FROM survey_regional_offices WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				if (isset($_POST['regional_office_count'])) {
					for ($i = 1; $i <= $_POST['regional_office_count']; $i++) {

						$office_name = $_POST['office_name_' . $i] ?? '';
						$district = $_POST['district_' . $i] ?? '';
						$division = $_POST['division_' . $i] ?? '';
						$tehsil = $_POST['tehsil_' . $i] ?? '';
						$address = $_POST['address_' . $i] ?? '';
						$phone = $_POST['phone_' . $i] ?? '';
						$pincode = $_POST['pincode_' . $i] ?? '';
						$email = $_POST['email_' . $i] ?? '';

						$sql = 'INSERT INTO survey_regional_offices (survey_id, office_name, district, division, tehsil, address, phone, pincode, email) VALUES  ("' . $_POST['survey_id'] . '",  "' . $office_name . '",  "' . $district . '",  "' . $division . '",  "' . $tehsil . '",  "' . $address . '",  "' . $phone . '",  "' . $pincode . '",  "' . $email . '")';

						execute_query($sql);
					}
				}

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
							$sql = 'insert into survey_invoice_new_sec_6_2_1 (`survey_id`, `sec_6_2_id`, `designation`, `full_name`, `father_name`, `mobile_no`) values("' . $_POST['survey_id'] . '", "' . $row_6_2['sno'] . '", "' . $_POST['sec_6_2_designation_' . $i] . '", "' . $_POST['sec_6_2_name_' . $i] . '", "' . $_POST['sec_6_2_father_name_' . $i] . '", "' . $_POST['sec_6_2__mob_no_' . $i] . '")';
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
							$sql = 'insert into survey_invoice_new_sec_6_2_1 (`survey_id`, `sec_6_2_id`, `designation`, `full_name`, `father_name`, `mobile_no`) values("' . $_POST['survey_id'] . '", "' . $row_6_2['sno'] . '", "' . $_POST['sec_6_2_designation_' . $i] . '", "' . $_POST['sec_6_2_name_' . $i] . '", "' . $_POST['sec_6_2_father_name_' . $i] . '", "' . $_POST['sec_6_2__mob_no_' . $i] . '")';
							execute_query($sql);
							if (mysqli_error($db)) {
								$data[] = array("id" => "error", "error" => "6.2.Unable to save data.");
							} else {
								$data[] = array("id" => "Update", "msg" => "6.2.Data Saved");
							}
						}
					}
				}

				$sec_4_total_personnel = $_POST['sec_4_total_personnel'] ?? '';
				$sql = 'UPDATE apex_si_1_1 SET sec_4_total_personnel="' . $sec_4_total_personnel . '" WHERE sno="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				$sql = 'DELETE FROM apex_si_7_2 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				// Insert new rows
				for ($i = 1; $i <= $_POST['employees_row_count']; $i++) {

					$post = $_POST['employee_post_' . $i] ?? '';
					$name = $_POST['employee_name_' . $i] ?? '';
					$father_name = $_POST['employee_father_name_' . $i] ?? '';
					$phone = $_POST['employee_phone_' . $i] ?? '';

					// Skip totally empty rows (optional)
					if ($post == '' && $name == '' && $father_name == '' && $phone == '') {
						continue;
					}

					$sql = 'INSERT INTO apex_si_7_2 (row_no, post, name, father_name, phone, survey_id)
							VALUES ("' . $i . '", "' . $post . '", "' . $name . '", "' . $father_name . '", "' . $phone . '", "' . $_POST['survey_id'] . '")';

					execute_query($sql);
				}


				$sql = 'DELETE FROM apex_si_district_positions WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				if (isset($_POST['district_address_row_count'])) {
					for ($i = 1; $i <= $_POST['district_address_row_count']; $i++) {

						$post = $_POST['district_post_' . $i] ?? '';
						$number = $_POST['district_number_' . $i] ?? '';
						$vacant = $_POST['district_vacant_' . $i] ?? '';
						$approved = $_POST['district_approved_' . $i] ?? '';

						if ($post === '' && $number === '' && $vacant === '' && $approved === '') {
							continue;
						}

						$sql = 'INSERT INTO apex_si_district_positions (survey_id, row_no, post_name, number, vacant, approved) VALUES ("' . $_POST['survey_id'] . '", "' . $i . '", "' . $post . '", "' . $number . '", "' . $vacant . '", "' . $approved . '")';
						execute_query($sql);

						if (mysqli_error($db)) {
							echo "<pre>Error: " . mysqli_error($db) . "</pre>";
						}
					}
				}
				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Unable to save district position details."];
				} else {
					$data[] = ["id" => "update", "msg" => "District position details saved successfully."];
				}

				$sql = 'DELETE FROM apex_si_7_3 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				if (isset($_POST['purchase_sale_row_count'])) {
					for ($i = 1; $i <= $_POST['purchase_sale_row_count']; $i++) {
						$wheat = $_POST['wheat_purchase_' . $i] ?? '';
						$rice = $_POST['rice_purchase_' . $i] ?? '';
						$seed = $_POST['seed_' . $i] ?? '';
						$fertilizer = $_POST['fertilizer_' . $i] ?? '';
						$rent = $_POST['godown_rent_' . $i] ?? '';
						$nefed = $_POST['nefed_' . $i] ?? '';
						$fsc = $_POST['farmer_service_center_' . $i] ?? '';
						$other = $_POST['other_business_' . $i] ?? '';

						$sql = 'INSERT INTO apex_si_7_3 (wheat_purchase, rice_purchase, seed, fertilizer, godown_rent, nefed,  farmer_service_center, other_business, survey_id) VALUES ("' . $wheat . '","' . $rice . '","' . $seed . '","' . $fertilizer . '", "' . $rent . '","' . $nefed . '","' . $fsc . '","' . $other . '", "' . $_POST['survey_id'] . '")';
						execute_query($sql);
					}
				}

				$sql = "DELETE FROM apex_si_7_4 WHERE survey_id='" . $_POST['survey_id'] . "'";
				execute_query($sql);

				// INSERT new rows
				$total = intval($_POST['sec_7_row_count']);

				for ($i = 1; $i <= $total; $i++) {

					$business = $_POST["sec_7_business_name_$i"];
					$target = $_POST["sec_7_annual_target_$i"];
					$achv = $_POST["sec_7_achievement_$i"];

					if ($business == '' && $target == '' && $achv == '') {
						continue;
					}

					$sql = "INSERT INTO apex_si_7_4 (survey_id, business_name, annual_target, achievement)
							VALUES ('" . $_POST['survey_id'] . "', '$business', '$target', '$achv')";
					execute_query($sql);
				}

				$sql = 'select * from survey_invoice_sec_3_new_1 where survey_id="' . $_POST['survey_id'] . '"';
				$res_3_new_1 = execute_query($sql);
				if (mysqli_num_rows($res_3_new_1) == 1) {
					$row_3_new_1 = mysqli_fetch_assoc($res_3_new_1);
				} else {
					$sql = 'insert into survey_invoice_sec_3_new_1 (survey_id) values("' . $_POST['survey_id'] . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "3.1.Unable to save data.");
					} else {
						$data[] = array("id" => "Update", "msg" => "sec-3.1.Data Saved");
					}
					$row_3_new_1['sno'] = mysqli_insert_id($db);
				}

				saveApexHumanResource($_POST['survey_id']);

				break;
			}
			case 2: {
				$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$apex_si_1_1 = mysqli_fetch_assoc(execute_query($sql));


				$sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'DELETE FROM apex_si_7_5 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);

				if (isset($_POST['stock_row_count'])) {
					for ($i = 1; $i <= $_POST['stock_row_count']; $i++) {

						$item = $_POST['stock_item_' . $i] ?? '';
						$close = $_POST['stock_closing_' . $i] ?? '';
						$book = $_POST['stock_book_' . $i] ?? '';

						if ($item === '' && $close === '' && $book === '')
							continue;

						$sql = 'INSERT INTO apex_si_7_5 (survey_id, row_no, item_name, closing_stock, book_value) 
							VALUES ("' . $_POST['survey_id'] . '","' . $i . '","' . $item . '","' . $close . '","' . $book . '")';
						execute_query($sql);
					}
				}

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Unable to save yearly apex_si_7_5."];
				} else {
					$data[] = ["id" => "update", "msg" => "Yearly apex_si_7_5 saved successfully."];
				}

				$demand_count = isset($_POST['useless_row_count']) ? intval($_POST['useless_row_count']) : 0;
				$sql = 'DELETE FROM apex_si_7_6 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				for ($i = 1; $i <= $demand_count; $i++) {

					$item = $_POST["useless_item_$i"] ?? '';
					$closing = $_POST["useless_closing_$i"] ?? '';
					$book = $_POST["useless_book_$i"] ?? '';

					if ($item != "" || $closing != "" || $book != "") {
						$sql = 'INSERT INTO apex_si_7_6 (survey_id, row_no, item_name, closing_stock, book_value)
								VALUES ("' . $_POST['survey_id'] . '","' . $i . '","' . $item . '","' . $closing . '","' . $book . '")';
						execute_query($sql);
					}
				}

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Unable to save apex_si_7_6."];
				} else {
					$data[] = ["id" => "update", "msg" => "apex_si_7_6 saved successfully."];
				}

				$sql = 'DELETE FROM apex_si_7_7 WHERE survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				if (isset($_POST['purchase_row_count'])) {
					for ($i = 1; $i <= $_POST['purchase_row_count']; $i++) {

						$item_no = $_POST['purchase_item_no_' . $i] ?? '';
						$item_desc = $_POST['purchase_item_desc_' . $i] ?? '';
						$date = $_POST['purchase_date_' . $i] ?? '';
						$value = $_POST['purchase_value_' . $i] ?? '';
						$qty = $_POST['purchase_qty_' . $i] ?? '';

						$sql = 'INSERT INTO apex_si_7_7 (item_no, item_desc, purchase_date, value, qty, survey_id)VALUES ("' . $item_no . '","' . $item_desc . '","' . $date . '","' . $value . '","' . $qty . '","' . $_POST['survey_id'] . '")';
						execute_query($sql);
					}
				}

				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Human Resource: Unable to save post ID "];
				} else {
					$data[] = ["id" => "update", "msg" => "Human Resource: Post ID saved."];
				}


				$sql = 'SELECT * FROM survey_invoice_sec_3_new_1 WHERE survey_id="' . $_POST['survey_id'] . '"';
				$res_3_new_1 = execute_query($sql);

				if (mysqli_num_rows($res_3_new_1) == 1) {
					$row_3_new_1 = mysqli_fetch_assoc($res_3_new_1);
				} else {
					$sql = 'INSERT INTO survey_invoice_sec_3_new_1 (survey_id) VALUES("' . $_POST['survey_id'] . '")';
					execute_query($sql);
					$row_3_new_1['sno'] = mysqli_insert_id($db);
				}

				$sql = 'UPDATE survey_invoice_sec_3_new_1 SET
                        financial_audit_year = "' . $_POST['sec_3_financial_audit_year'] . '",
                        audit_grading = "' . $_POST['sec_3_audit_grading'] . '",
                        compliance_status = "' . $_POST['sec_3_compliance_status'] . '",
                        agm_year = "' . $_POST['sec_3_agm_year'] . '",
                        dividend_year = "' . $_POST['sec_3_dividend_year'] . '",
                        dividend_per = "' . $_POST['sec_3_dividend_per'] . '",
                        dividend_amt = "' . $_POST['sec_3_dividend_amt'] . '",
                        edition_time = "' . date("Y-m-d H:i:s") . '"
                        WHERE sno="' . $row_3_new_1['sno'] . '"';
				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "Audit data: Unable to save.");
				} else {
					$data[] = array("id" => "Update", "msg" => "Audit data saved.");
				}

				break;
			}
			case 3: {
				$sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$apex_si_1_1 = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from apex where sno="' . $apex_si_1_1['apex_id'] . '"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'update survey_invoice set 
				society_building_ownership="' . $_POST['sec_3_ownership'] . '", 
				society_building_rent_amount="' . $_POST['sec_3_building_rent'] . '", 
				society_building_area="' . $_POST['sec_3_building_area'] . '", 
				edition_time="' . date("Y-m-d H:i:s") . '" where sno="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				if (mysqli_error($db)) {
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
						execute_query($sql);
						if (mysqli_error($db)) {
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

				$sql = 'update survey_invoice_sec_2_1 set 
				sec_6_road="' . $_POST['sec_6_access_road'] . '",
				distance_from_approach_road="' . $_POST['sec_6_2_truck_not_reach'] . '",
				approach_road="' . $_POST['sec_6_paved_road'] . '",
				plot_frontage="' . $_POST['sec_8_plot_frontage'] . '"
				where sno=' . $row_2_1['sno'];
				execute_query($sql);
				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "7.11.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "7.11.Data Saved");
				}

				saveApexEmptyLandInfo($_POST['survey_id']);

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
				execute_query($sql);
				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "8.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "8.Data Saved");
				}

				break;
			}
			case 5: {
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

				break;
			}
		}
	}

	save_apex_agro_work_professions($_POST['survey_id']);
}


if (empty($data) != true) {
	echo json_encode($data);
}

function savepcfsocietyimage($post, $files)
{
	$survey_id = intval($post['survey_id'] ?? 0);

	if (!$survey_id) {
		return "";
	}
	/* Absolute Path */
	$target_dir = dirname(__DIR__) . "/user_data/society_img/";

	if (!is_dir($target_dir)) {
		mkdir($target_dir, 0755, true);
	}

	if (!isset($files['society_photo']) || $files['society_photo']['error'] != 0) {
		return "";
	}

	$file_tmp = $files['society_photo']['tmp_name'];
	$file_name = $files['society_photo']['name'];

	$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

	$new_file_name = $file_name;

	$target_file = $target_dir . $new_file_name;

	if (move_uploaded_file($file_tmp, $target_file)) {
		return $new_file_name;
	}

	return "";
}

function upload_img($name, $society, $new_name, $maxDim = 1500)
{

	return true;
	$file_name = $name['tmp_name'];
	list($width, $height, $type, $attr) = getimagesize($file_name);
	if ($width > $maxDim || $height > $maxDim) {
		$target_filename = $file_name;
		$ratio = $width / $height;
		if ($ratio > 1) {
			$new_width = $maxDim;
			$new_height = $maxDim / $ratio;
		} else {
			$new_width = $maxDim * $ratio;
			$new_height = $maxDim;
		}
		$src = imagecreatefromstring(file_get_contents($file_name));
		$dst = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		imagedestroy($src);
		imagejpeg($dst, $target_filename); // adjust format as needed
		imagedestroy($dst);
	}

	$msg = '';
	$imageFileType = strtolower(pathinfo($name['name'], PATHINFO_EXTENSION));
	$target_dir = '../user_data/' . ($society['col2'] ?? '6') . '/' . ($society['col6'] ?? '') . '/';
	$target_file = $target_dir . basename($new_name) . '.' . $imageFileType;

	$uploadOk = 1;
	// Check if image file is a actual image or fake image
	if (isset($_POST["submit"])) {
		$check = getimagesize($name["tmp_name"]);
		if ($check !== false) {
			$msg .= "<div class='text-danger'>File is an image - " . $check["mime"] . ".</div>";
			$uploadOk = 1;
		} else {
			$msg .= "<div class='text-danger'>File is not an image.</div>";
			$uploadOk = 0;
		}
	}
	// Check file size
	if ($name["size"] > 50000000) {
		$msg .= "<div class='text-danger'>Sorry, your file is too large.</div>";
		$uploadOk = 0;
	}

	// Allow certain file formats
	if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
		$msg .= "<div class='text-danger'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>";
		$uploadOk = 0;
	}

	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		$msg .= "<div class='text-danger'>Sorry, your file was not uploaded.</div>";
		// if everything is ok, try to upload file
	} else {
		if (!file_exists($target_dir)) {
			mkdir($target_dir, 0777, true);
		}

		if (move_uploaded_file($name["tmp_name"], $target_file)) {
			$msg .= "<div class='text-success'>The file " . htmlspecialchars(basename($name["name"])) . " has been uploaded.</div>";
		} else {
			$msg .= "<div class='text-danger'>Sorry, there was an error uploading your file.</div>";
		}
	}
	$result = array("error" => $uploadOk, "msg" => $msg, "file_name" => basename($new_name) . '.' . $imageFileType);
	return $result;
}

function save_zone_details($survey_id)
{
	if (!$survey_id) {
		return false;
	}
	if (!isset($_POST['zone_name']))
		return;

	$names = $_POST['zone_name'];
	$districts = $_POST['zone_district'] ?? [];
	$mobiles = $_POST['zone_mobile'] ?? [];
	$emails = $_POST['zone_email'] ?? [];
	$addresses = $_POST['zone_address'] ?? [];
	$existing_images = $_POST['existing_zone_image'] ?? [];

	$row_count = count($names);

	execute_query('DELETE FROM apex_zone_details WHERE survey_id="' . $survey_id . '"');

	$upload_dir = dirname(__DIR__) . "/user_data/society_img/";

	if (!is_dir($upload_dir)) {
		mkdir($upload_dir, 0777, true);
	}

	for ($i = 0; $i < $row_count; $i++) {

		$zone_name = trim($names[$i] ?? '');
		$zone_district = trim($districts[$i] ?? '');
		$zone_mobile = trim($mobiles[$i] ?? '');
		$zone_email = trim($emails[$i] ?? '');
		$zone_address = trim($addresses[$i] ?? '');

		if ($zone_name == '' && $zone_district == '' && $zone_mobile == '' && $zone_email == '' && $zone_address == '') {
			continue;
		}

		$zone_image = $existing_images[$i] ?? '';

		if (!empty($_FILES['zone_image']['name'][$i])) {

			$original = $_FILES['zone_image']['name'][$i];
			$tmp = $_FILES['zone_image']['tmp_name'][$i];

			$ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));

			if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {

				$file_name = time() . '_' . $survey_id . '_zone_' . $i . '.' . $ext;

				if (move_uploaded_file($tmp, $upload_dir . $file_name)) {
					$zone_image = $file_name;
				}
			}
		}

		$sql = 'INSERT INTO apex_zone_details
        (survey_id,zone_name,zone_district,zone_mobile,zone_email,zone_address,zone_image,created_at)
        VALUES
        ("' . $survey_id . '",
        "' . $zone_name . '",
        "' . $zone_district . '",
        "' . $zone_mobile . '",
        "' . $zone_email . '",
        "' . $zone_address . '",
        "' . $zone_image . '",
        "' . date("Y-m-d H:i:s") . '")';

		execute_query($sql);
	}
}
function save_prakhand_details($survey_id)
{
	if (!$survey_id) {
		return false;
	}
	if (!isset($_POST['other_office_name']))
		return;

	$names = $_POST['other_office_name'];
	$districts = $_POST['other_office_district'] ?? [];
	$mobiles = $_POST['other_office_mobile'] ?? [];
	$emails = $_POST['other_office_email'] ?? [];
	$addresses = $_POST['other_office_address'] ?? [];
	$existing_images = $_POST['existing_other_image'] ?? [];

	$row_count = count($names);

	execute_query('DELETE FROM apex_prakhand_details WHERE survey_id="' . $survey_id . '"');

	$upload_dir = dirname(__DIR__) . "/user_data/society_img/";

	if (!is_dir($upload_dir)) {
		mkdir($upload_dir, 0777, true);
	}

	for ($i = 0; $i < $row_count; $i++) {

		$prakhand_name = trim($names[$i] ?? '');
		$prakhand_district = trim($districts[$i] ?? '');
		$prakhand_mobile = trim($mobiles[$i] ?? '');
		$prakhand_email = trim($emails[$i] ?? '');
		$prakhand_address = trim($addresses[$i] ?? '');

		if ($prakhand_name == '' && $prakhand_district == '' && $prakhand_mobile == '' && $prakhand_email == '' && $prakhand_address == '') {
			continue;
		}

		$prakhand_image = $existing_images[$i] ?? '';

		if (!empty($_FILES['other_office_image']['name'][$i])) {

			$original = $_FILES['other_office_image']['name'][$i];
			$tmp = $_FILES['other_office_image']['tmp_name'][$i];

			$ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));

			if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {

				$file_name = time() . '_' . $survey_id . '_office_' . $i . '.' . $ext;

				if (move_uploaded_file($tmp, $upload_dir . $file_name)) {
					$prakhand_image = $file_name;
				}
			}
		}

		$sql = 'INSERT INTO apex_prakhand_details
        (survey_id,prakhand_name,prakhand_district,prakhand_mobile,prakhand_email,prakhand_address,prakhand_image,created_at)
        VALUES
        ("' . $survey_id . '",
        "' . $prakhand_name . '",
        "' . $prakhand_district . '",
        "' . $prakhand_mobile . '",
        "' . $prakhand_email . '",
        "' . $prakhand_address . '",
        "' . $prakhand_image . '",
        "' . date("Y-m-d H:i:s") . '")';

		execute_query($sql);
	}
}
function saveApexHumanResource($survey_id)
{
	if (!$survey_id) {
		return false;
	}
	if (!isset($_POST['staff_name']))
		return;

	$apex_code = intval($_POST['apex_code']);

	/* ===============================
	   Delete Old Records
	================================ */

	execute_query('DELETE FROM apex_human_resource_info WHERE survey_id="' . $survey_id . '"');

	/* ===============================
	   Collect POST Arrays
	================================ */

	$staff_type = $_POST['staff_type'] ?? [];
	$hr_post_id = $_POST['post_id'] ?? [];
	$sanctioned_post = $_POST['sanctioned_post'] ?? [];
	$vacant_post = $_POST['vacant_post'] ?? [];

	$staff_post_id = $_POST['staff_post_name'] ?? [];
	$staff_name = $_POST['staff_name'] ?? [];
	$staff_sthiti = $_POST['staff_sthiti'] ?? [];
	$staff_father = $_POST['staff_father'] ?? [];
	$staff_dob = $_POST['staff_dob'] ?? [];
	$staff_mobile = $_POST['staff_mobile'] ?? [];
	$staff_qualification = $_POST['staff_qualification'] ?? [];

	$existing_images = $_POST['existing_staff_image'] ?? [];

	$staff_images = $_FILES['staff_image'] ?? [];

	/* ===============================
	   Image Upload Path
	================================ */

	$upload_dir = dirname(__DIR__) . "/user_data/staff_" . $survey_id . "/";

	if (!is_dir($upload_dir)) {
		mkdir($upload_dir, 0777, true);
	}

	$totalStaff = count($staff_name);

	$hrIndex = 0;
	$staffCounter = 0;

	/* ===============================
	   Insert Loop
	================================ */

	for ($i = 0; $i < $totalStaff; $i++) {

		if (empty($staff_name[$i]))
			continue;

		if ($staffCounter >= ($sanctioned_post[$hrIndex] ?? 0)) {
			$staffCounter = 0;
			$hrIndex++;
		}

		$imageName = $existing_images[$i] ?? "";

		/* ===============================
		   Upload New Image
		================================ */

		if (!empty($staff_images['name'][$i]) && $staff_images['error'][$i] == 0) {

			$allowed = ['jpg', 'jpeg', 'png'];
			$ext = strtolower(pathinfo($staff_images['name'][$i], PATHINFO_EXTENSION));

			if (in_array($ext, $allowed)) {

				$tmpPath = $staff_images['tmp_name'][$i];

				$newName = time() . '_' . rand(1000, 9999) . '.jpg';
				$targetPath = $upload_dir . $newName;

				if ($ext == 'png') {
					$image = imagecreatefrompng($tmpPath);
				} else {
					$image = imagecreatefromjpeg($tmpPath);
				}

				$width = imagesx($image);
				$height = imagesy($image);

				$newWidth = 400;
				$newHeight = ($height / $width) * $newWidth;

				$newImage = imagecreatetruecolor($newWidth, $newHeight);

				imagecopyresampled(
					$newImage,
					$image,
					0,
					0,
					0,
					0,
					$newWidth,
					$newHeight,
					$width,
					$height
				);

				$quality = 85;

				do {
					imagejpeg($newImage, $targetPath, $quality);
					$fileSize = filesize($targetPath);
					$quality -= 5;
				} while ($fileSize > (120 * 1024) && $quality > 40);

				imagedestroy($image);
				imagedestroy($newImage);

				$imageName = $newName;
			}
		}

		/* ===============================
		   Insert Record
		================================ */

		$sql = 'INSERT INTO apex_human_resource_info
        (
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
        )
        VALUES
        (
            "' . $survey_id . '",
            "' . $apex_code . '",
            "' . ($staff_type[$hrIndex] ?? '') . '",
            "' . ($hr_post_id[$hrIndex] ?? '') . '",
            "' . ($sanctioned_post[$hrIndex] ?? '') . '",
            "' . ($vacant_post[$hrIndex] ?? '') . '",
            "' . ($staff_post_id[$i] ?? '') . '",
            "' . ($staff_name[$i] ?? '') . '",
            "' . ($staff_sthiti[$i] ?? '') . '",
            "' . ($staff_father[$i] ?? '') . '",
            "' . ($staff_dob[$i] ?? '') . '",
            "' . ($staff_mobile[$i] ?? '') . '",
            "' . ($staff_qualification[$i] ?? '') . '",
            "' . $imageName . '"
        )';

		execute_query($sql);

		$staffCounter++;
	}


}
function save_apex_agro_work_professions($survey_id)
{
	if (!$survey_id) {
		return false;
	}
	$row_count = 0;

	foreach ($_POST as $key => $value) {
		if (strpos($key, 'wheat_purchase_') === 0) {
			$row_count++;
		}
	}

	if ($row_count == 0)
		return;

	execute_query('DELETE FROM apex_agro_work_professions_details 
                   WHERE survey_id="' . $survey_id . '"');

	for ($i = 1; $i <= $row_count; $i++) {

		$wheat_purchase = $_POST['wheat_purchase_' . $i] ?? '';
		$rice_purchase = $_POST['rice_purchase_' . $i] ?? '';
		$seed = $_POST['seed_' . $i] ?? '';
		$fertilizer = $_POST['fertilizer_' . $i] ?? '';
		$godown_rent = $_POST['godown_rent_' . $i] ?? '';
		$nefed = $_POST['nefed_' . $i] ?? '';
		$farmer_service_center = $_POST['farmer_service_center_' . $i] ?? '';
		$other_business = $_POST['other_business_' . $i] ?? '';

		if (
			$wheat_purchase == '' &&
			$rice_purchase == '' &&
			$seed == '' &&
			$fertilizer == '' &&
			$godown_rent == '' &&
			$nefed == '' &&
			$farmer_service_center == '' &&
			$other_business == ''
		)
			continue;

		$sql = 'INSERT INTO apex_agro_work_professions_details
        (
            survey_id,
            row_no,
            wheat_purchase,
            rice_purchase,
            seed,
            fertilizer,
            godown_rent,
            nefed,
            farmer_service_center,
            other_business,
            created_at
        )
        VALUES
        (
            "' . $survey_id . '",
            "' . $i . '",
            "' . $wheat_purchase . '",
            "' . $rice_purchase . '",
            "' . $seed . '",
            "' . $fertilizer . '",
            "' . $godown_rent . '",
            "' . $nefed . '",
            "' . $farmer_service_center . '",
            "' . $other_business . '",
            "' . date("Y-m-d H:i:s") . '"
        )';

		execute_query($sql);
	}
}

function saveApexEmptyLandInfo($survey_id)
{
	if (!isset($_POST['sec_3_c_id']))
		return;

	execute_query('DELETE FROM apex_empty_land_info WHERE survey_id="' . $survey_id . '"');

	$totalRows = intval($_POST['sec_3_c_id']);

	$upload_dir = dirname(__DIR__) . "/user_data/empty_land_" . $survey_id . "/";

	if (!is_dir($upload_dir)) {
		mkdir($upload_dir, 0777, true);
	}

	for ($i = 1; $i <= $totalRows; $i++) {
		$district = $_POST['sec_3_c_district_' . $i] ?? '';
		$area = $_POST['sec_3_c_area_' . $i] ?? '';
		$road = $_POST['sec_3_c_paved_road_' . $i] ?? '';
		$location = $_POST['sec_3_c_land_location_' . $i] ?? '';

		if ($district == '' && $area == '' && $road == '' && $location == '')
			continue;

		$existingImage = $_POST['sec_3_c_existing_image_' . $i] ?? '';
		$imageName = $existingImage;

		if (isset($_FILES['sec_3_c_image_' . $i]) && $_FILES['sec_3_c_image_' . $i]['error'] == 0) {
			$allowed = ['jpg', 'jpeg', 'png'];
			$ext = strtolower(pathinfo($_FILES['sec_3_c_image_' . $i]['name'], PATHINFO_EXTENSION));

			if (in_array($ext, $allowed)) {
				$tmpPath = $_FILES['sec_3_c_image_' . $i]['tmp_name'];

				$newName = time() . '_' . rand(1000, 9999) . '.jpg';
				$targetPath = $upload_dir . $newName;

				if ($ext == "png")
					$image = imagecreatefrompng($tmpPath);
				else
					$image = imagecreatefromjpeg($tmpPath);

				$width = imagesx($image);
				$height = imagesy($image);

				$newWidth = 400;
				$newHeight = ($height / $width) * $newWidth;

				$newImage = imagecreatetruecolor($newWidth, $newHeight);

				imagecopyresampled(
					$newImage,
					$image,
					0,
					0,
					0,
					0,
					$newWidth,
					$newHeight,
					$width,
					$height
				);

				imagejpeg($newImage, $targetPath, 85);

				imagedestroy($image);
				imagedestroy($newImage);

				$imageName = $newName;
			}
		}

		$sql = 'INSERT INTO apex_empty_land_info
        (
            survey_id,
            sec_3_c_district,
            sec_3_c_area,
            sec_3_c_paved_road,
            sec_3_c_land_location,
            sec_3_c_image
        )
        VALUES
        (
            "' . $survey_id . '",
            "' . $district . '",
            "' . $area . '",
            "' . $road . '",
            "' . $location . '",
            "' . $imageName . '"
        )';

		execute_query($sql);
	}
}