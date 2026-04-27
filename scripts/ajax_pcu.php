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
	$result = execute_query($sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$data[] = array("id" => $row['sno'], "society_name" => "B-PACS " . $row['col4']);
	}
} elseif ($id == 'verify_otp_pcu') {
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
} elseif ($id == 'submit_form_pcu') {
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

		// $sql = 'select * from apex_si_1_1 where sno="' . $id . '"';
        // $survey_invoice = mysqli_fetch_assoc(execute_query($sql));

        // $sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
        // $apex = mysqli_fetch_assoc(execute_query($sql));
        // if (isset($_FILES['society_photo']) && !empty($_FILES['society_photo']['name'])) {
        //     $society_image = upload_img($_FILES['society_photo'], $apex, "society_name_" . $id);
        //     //print_r($society_image);
        //     if ($society_image['error'] == 1) {
        //         $sql = 'update apex_si_1_1 set 
		// 		photo_id="' . $society_image['file_name'] . '"
		// 		where sno="' . $id . '"';
        //         execute_query($sql);
        //         $data[] = array("id" => "Update", "msg" => $society_image['msg']);
        //     } else {
        //         $data[] = array("id" => "error", "error" => $society_image['msg']);
        //     }
        // }
        //  Upload Society Image
        uploadSocietyImage();
    }
	else{
		switch($_POST['current_step_count']){
			case 0:{
				$sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$apex_si_1_1 = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'UPDATE apex_si_1_1 SET
                    edited_by = "",
                    edition_time = "' . date("Y-m-d H:i:s") . '",
                    apex_id = "' . $_POST['apex_code'] . '",
                    latitude = "' . $_POST['latitude'] . '",
                    longitude = "' . $_POST['longitude'] . '",
                	email_id = "' . $_POST['email_id'] . '",
                    society_registration_no = "' . $_POST['society_registration_no'] . '",
                    prakhand_name = "' . $_POST['prakhand_name'] . '",
                    society_registration_date = "' . $_POST['society_registration_date'] . '",
                    pan_no = "' . $_POST['pan_no'] . '",
                    tan_no = "' . $_POST['tan_no'] . '",
                    gst_no = "' . $_POST['gst_no'] . '",
                    mobile_number = "' . $_POST['mobile_number'] . '",
                    website = "' . $_POST['website'] . '",
                    hq_ownership = "' . $_POST['hq_ownership'] . '"
                    WHERE sno=' . $_POST['survey_id'];

				execute_query($sql);

				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "Data Saved");
				}

            //  Upload Society Image
                uploadSocietyImage();

				/* ==============================
				   SAVE ZONE / TRAINING / OTHER OFFICE
				   TABLE: apex_zone_details
				================================ */

				if (isset($_POST['survey_id']) && !empty($_POST['survey_id'])) {

					$survey_id = $_POST['survey_id'];

					/* delete old rows */
					$sql = 'DELETE FROM apex_zone_details WHERE survey_id="' . $survey_id . '"';
					execute_query($sql);

					/* ensure upload folder exists */
					$upload_dir = '../user_data/zones/';
					if (!is_dir($upload_dir)) {
						mkdir($upload_dir, 0777, true);
					}

					/* ==============================
                       1. BRANCH OFFICES (Zones)
                       office_type = 1
                    ================================ */

					if (isset($_POST['zone_name'])) {

						foreach ($_POST['zone_name'] as $i => $name) {

							if (trim($name) == '') continue;

							$mobile = $_POST['zone_mobile'][$i] ?? '';
							$email = $_POST['zone_email'][$i] ?? '';
							$address = $_POST['zone_address'][$i] ?? '';

							$image = $_POST['zone_image_old'][$i] ?? '';

							if (!empty($_FILES['zone_image']['name'][$i])) {

								$ext = pathinfo($_FILES['zone_image']['name'][$i], PATHINFO_EXTENSION);
								$image = time() . '_' . $survey_id . '_zone_' . $i . '.' . $ext;

								move_uploaded_file(
									$_FILES['zone_image']['tmp_name'][$i],
									$upload_dir . $image
								);
							}

							$sql = 'INSERT INTO apex_zone_details
									(
										survey_id,
										office_type,
										zone_name,
										zone_mobile,
										zone_email,
										zone_address,
										zone_image,
										created_at
									)
									VALUES
									(
										"' . $survey_id . '",
										"1",
										"' . $name . '",
										"' . $mobile . '",
										"' . $email . '",
										"' . $address . '",
										"' . $image . '",
										NOW()
									)';

							execute_query($sql);
						}
					}


					/* ==============================
                       2. TRAINING CENTERS
                       office_type = 2
                    ================================ */

					if (isset($_POST['prakhand_name'])) {

						foreach ($_POST['prakhand_name'] as $i => $name) {

							if (trim($name) == '') continue;

							$mobile = $_POST['prakhand_mobile'][$i] ?? '';
							$email = $_POST['prakhand_email'][$i] ?? '';
							$address = $_POST['prakhand_address'][$i] ?? '';

							$image = $_POST['prakhand_image_old'][$i] ?? '';

							if (!empty($_FILES['prakhand_image']['name'][$i])) {

								$ext = pathinfo($_FILES['prakhand_image']['name'][$i], PATHINFO_EXTENSION);
								$image = time() . '_' . $survey_id . '_prakhand_' . $i . '.' . $ext;

								move_uploaded_file(
									$_FILES['prakhand_image']['tmp_name'][$i],
									$upload_dir . $image
								);
							}

							$sql = 'INSERT INTO apex_zone_details
									(
										survey_id,
										office_type,
										zone_name,
										zone_mobile,
										zone_email,
										zone_address,
										zone_image,
										created_at
									)
									VALUES
									(
										"' . $survey_id . '",
										"2",
										"' . $name . '",
										"' . $mobile . '",
										"' . $email . '",
										"' . $address . '",
										"' . $image . '",
										NOW()
									)';

							execute_query($sql);
						}
					}

					/* ===================================================
                       5. OTHER OFFICES (office_type = 3)
                    =================================================== */

					/* ==============================
                       3. OTHER OFFICES
                       office_type = 3
                    ================================ */

					if (isset($_POST['other_office_name'])) {

						foreach ($_POST['other_office_name'] as $i => $name) {

							if (trim($name) == '') continue;

							$mobile = $_POST['other_office_mobile'][$i] ?? '';
							$email = $_POST['other_office_email'][$i] ?? '';
							$address = $_POST['other_office_address'][$i] ?? '';

							$image = $_POST['other_office_image_old'][$i] ?? '';

							if (!empty($_FILES['other_office_image']['name'][$i])) {

								$ext = pathinfo($_FILES['other_office_image']['name'][$i], PATHINFO_EXTENSION);
								$image = time() . '_' . $survey_id . '_other_' . $i . '.' . $ext;

								move_uploaded_file(
									$_FILES['other_office_image']['tmp_name'][$i],
									$upload_dir . $image
								);
							}

							$sql = 'INSERT INTO apex_zone_details
									(
										survey_id,
										office_type,
										zone_name,
										zone_mobile,
										zone_email,
										zone_address,
										zone_image,
										created_at
									)
									VALUES
									(
										"' . $survey_id . '",
										"3",
										"' . $name . '",
										"' . $mobile . '",
										"' . $email . '",
										"' . $address . '",
										"' . $image . '",
										NOW()
									)';

							execute_query($sql);
						}
					}
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

				saveApexFinancialInfo($_POST);

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
				profit_loss_amount_1 = "' . ($_POST['sec_3_profit_loss_amount_1'] ?? '') . '",
				accumulated_1 = "' . $_POST['sec_3_accumulated_1'] . '",
				accumulated_amount_1 = "' . ($_POST['sec_3_accumulated_amount_1'] ?? '') . '",
				profit_loss_2 = "' . $_POST['sec_3_profit_loss_2'] . '",
				profit_loss_amount_2 = "' . ($_POST['sec_3_profit_loss_amount_2'] ?? '') . '",
				accumulated_2 = "' . $_POST['sec_3_accumulated_2'] . '",
				accumulated_amount_2 = "' . ($_POST['sec_3_accumulated_amount_2'] ?? '') . '",
				profit_loss_3 = "' . $_POST['sec_3_profit_loss_3'] . '",
				profit_loss_amount_3 = "' . ($_POST['sec_3_profit_loss_amount_3'] ?? '') . '",
				accumulated_3 = "' . $_POST['sec_3_accumulated_3'] . '",
				accumulated_amount_3 = "' . ($_POST['sec_3_accumulated_amount_3'] ?? '') . '",
				financial_audit_year = "' . $_POST['sec_3_financial_audit_year'] . '",
				audit_grading = "' . $_POST['sec_3_audit_grading'] . '",
				compliance_status = "' . $_POST['sec_3_compliance_status'] . '",
				agm_year = "' . $_POST['sec_3_agm_year'] . '",
				dividend_year = "' . $_POST['sec_3_dividend_year'] . '",
				dividend_per = "' . $_POST['sec_3_dividend_per'] . '",
				dividend_amt = "' . $_POST['sec_3_dividend_amt'] . '",
				santulan_patra = "' . ($_POST['sec_3_santulan_patra'] ?? '') . '",
				edition_time = "' . date("Y-m-d H:i:s") . '"
				where sno="' . $row_3_new_1['sno'] . '"';
				execute_query($sql);
				if (mysqli_error($db)) {
					//echo mysqli_error($db).$sql;
					$data[] = array("id" => "error", "error" => "3.1.Unable to save data.");
				} else {
					$data[] = array("id" => "Update", "msg" => "sec-3.1. Data Saved");
				}

				/* =========================
				INSERT NEW ROWS
				========================== */
				if ($_POST['survey_id']) {
					/* -----------------------------
						 DELETE OLD RECORDS
					  ----------------------------- */
					$survey_id = $_POST['survey_id'];
					$sql = "DELETE FROM training_centers WHERE survey_id = '".mysqli_real_escape_string($db,$survey_id)."'";
					execute_query($sql);

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
				}
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

				saveApexManagementCommittee($_POST);

                saveApexHumanResource($_POST['survey_id']);

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
function saveApexFinancialInfo($post)
{
	$survey_id = intval($post['survey_id'] ?? 0);

	if (!$survey_id) {
		return false;
	}

	// Delete old records
	execute_query("DELETE FROM apex_financial_info WHERE survey_id='{$survey_id}'");

	$index = 1;

	while (isset($post['sec_3_profit_loss_' . $index])) {

		$yearLabel = $post['financial_year_label_' . $index] ?? '';

		if ($yearLabel == '') {
			$index++;
			continue;
		}

		$annual_status      = $post['sec_3_profit_loss_' . $index] ?? '';
		$annual_gross       = $post['sec_3_gross_amount_' . $index] ?? '0';
		$annual_net         = $post['sec_3_net_amount_' . $index] ?? '0';

		$accumulated_status = $post['sec_3_accumulated_' . $index] ?? '';
		$accumulated_gross  = $post['sec_3_acc_gross_amount_' . $index] ?? '0';
		$accumulated_net    = $post['sec_3_acc_net_amount_' . $index] ?? '0';

		$sql = "
        INSERT INTO apex_financial_info
        (
            survey_id,
            financial_year,
            annual_status,
            annual_gross,
            annual_net,
            accumulated_status,
            accumulated_gross,
            accumulated_net,
            created_at
        )
        VALUES
        (
            '{$survey_id}',
            '{$yearLabel}',
            '{$annual_status}',
            '{$annual_gross}',
            '{$annual_net}',
            '{$accumulated_status}',
            '{$accumulated_gross}',
            '{$accumulated_net}',
            '" . date('Y-m-d H:i:s') . "'
        )";

		execute_query($sql);

		$index++;
	}

	return true;
}
function saveApexManagementCommittee($post)
{
	$survey_id = intval($post['survey_id'] ?? 0);

	if (!$survey_id) {
		return false;
	}

	// Delete old records
	execute_query("DELETE FROM survey_management_committee WHERE survey_id='{$survey_id}'");

	$index = 1;

	while (isset($post['sec_6_2_name_' . $index])) {

		$category     = $post['sec_6_2_category_' . $index] ?? '';
		$designation  = $post['sec_6_2_designation_' . $index] ?? '';
		$name         = $post['sec_6_2_name_' . $index] ?? '';
		$father_name  = $post['sec_6_2_father_name_' . $index] ?? '';
		$mobile       = $post['sec_6_2__mob_no_' . $index] ?? '';
		$post_name    = $post['sec_6_2_post_name_' . $index] ?? '';
		$election_year= $post['sec_6_2_election_year_' . $index] ?? '';

		// Skip completely empty rows
		if ($name == '' && $mobile == '') {
			$index++;
			continue;
		}

		// Fix for empty election year
		if ($election_year == '') {
			$election_year_sql = "NULL";
		} else {
			$election_year_sql = "'" . intval($election_year) . "'";
		}

		$sql = "
		INSERT INTO survey_management_committee
		(
			survey_id,
			category,
			designation,
			member_name,
			father_name,
			mobile_no,
			post_name,
			election_year,
			created_at
		)
		VALUES
		(
			'{$survey_id}',
			'{$category}',
			'{$designation}',
			'{$name}',
			'{$father_name}',
			'{$mobile}',
			'{$post_name}',
			{$election_year_sql},
			'" . date('Y-m-d H:i:s') . "'
		)";

		execute_query($sql);

		$index++;
	}

	return true;
}
function saveApexHumanResource($survey_id)
{

	if (!$survey_id) {
		return false;
	}

    if (!isset($_POST['staff_name'])) return;

    $apex_code = intval($_POST['apex_code']);

    /* ===============================
       Delete Old Records
    ================================ */

    execute_query('DELETE FROM apex_human_resource_info WHERE survey_id="'.$survey_id.'"');

    /* ===============================
       Collect POST Arrays
    ================================ */

    $staff_type      = $_POST['staff_type'] ?? [];
    $hr_post_id      = $_POST['post_id'] ?? [];
    $sanctioned_post = $_POST['sanctioned_post'] ?? [];
    $vacant_post     = $_POST['vacant_post'] ?? [];

    $staff_post_id   = $_POST['staff_post_name'] ?? [];
    $staff_name      = $_POST['staff_name'] ?? [];
    $staff_sthiti    = $_POST['staff_sthiti'] ?? [];
    $staff_father    = $_POST['staff_father'] ?? [];
    $staff_dob       = $_POST['staff_dob'] ?? [];
    $staff_mobile    = $_POST['staff_mobile'] ?? [];
    $staff_qualification = $_POST['staff_qualification'] ?? [];

    $existing_images = $_POST['existing_staff_image'] ?? [];

    $staff_images = $_FILES['staff_image'] ?? [];

    /* ===============================
       Image Upload Path
    ================================ */

    $upload_dir = dirname(__DIR__)."/user_data/staff_".$survey_id."/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir,0777,true);
    }

    $totalStaff = count($staff_name);

    $hrIndex = 0;
    $staffCounter = 0;

    /* ===============================
       Insert Loop
    ================================ */

    for ($i=0; $i<$totalStaff; $i++) {

        if (empty($staff_name[$i])) continue;

        if ($staffCounter >= ($sanctioned_post[$hrIndex] ?? 0)) {
            $staffCounter = 0;
            $hrIndex++;
        }

        $imageName = $existing_images[$i] ?? "";

        /* ===============================
           Upload New Image
        ================================ */

        if (!empty($staff_images['name'][$i]) && $staff_images['error'][$i] == 0) {

            $allowed = ['jpg','jpeg','png'];
            $ext = strtolower(pathinfo($staff_images['name'][$i], PATHINFO_EXTENSION));

            if (in_array($ext,$allowed)) {

                $tmpPath = $staff_images['tmp_name'][$i];

                $newName = time().'_'.rand(1000,9999).'.jpg';
                $targetPath = $upload_dir.$newName;

                if ($ext == 'png') {
                    $image = imagecreatefrompng($tmpPath);
                } else {
                    $image = imagecreatefromjpeg($tmpPath);
                }

                $width  = imagesx($image);
                $height = imagesy($image);

                $newWidth  = 400;
                $newHeight = ($height/$width)*$newWidth;

                $newImage = imagecreatetruecolor($newWidth,$newHeight);

                imagecopyresampled(
                    $newImage,
                    $image,
                    0,0,0,0,
                    $newWidth,$newHeight,
                    $width,$height
                );

                $quality = 85;

                do {
                    imagejpeg($newImage,$targetPath,$quality);
                    $fileSize = filesize($targetPath);
                    $quality -= 5;
                } while ($fileSize > (120*1024) && $quality > 40);

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
            "'.$survey_id.'",
            "'.$apex_code.'",
            "'.($staff_type[$hrIndex] ?? '').'",
            "'.($hr_post_id[$hrIndex] ?? '').'",
            "'.($sanctioned_post[$hrIndex] ?? '').'",
            "'.($vacant_post[$hrIndex] ?? '').'",
            "'.($staff_post_id[$i] ?? '').'",
            "'.($staff_name[$i] ?? '').'",
            "'.($staff_sthiti[$i] ?? '').'",
            "'.($staff_father[$i] ?? '').'",
            "'.($staff_dob[$i] ?? '').'",
            "'.($staff_mobile[$i] ?? '').'",
            "'.($staff_qualification[$i] ?? '').'",
            "'.$imageName.'"
        )';

        execute_query($sql);

        $staffCounter++;
    }


}
function saveApexEmptyLandInfo($survey_id)
{
    if (!$survey_id){return;}
	if (!isset($_POST['sec_3_c_id'])) return;

	execute_query('DELETE FROM apex_empty_land_info WHERE survey_id="'.$survey_id.'"');

	$totalRows = intval($_POST['sec_3_c_id']);

	$upload_dir = dirname(__DIR__)."/user_data/empty_land_".$survey_id."/";

	if (!is_dir($upload_dir))
	{
		mkdir($upload_dir,0777,true);
	}

	for ($i=1; $i<=$totalRows; $i++)
	{
		$district = $_POST['sec_3_c_district_'.$i] ?? '';
		$area     = $_POST['sec_3_c_area_'.$i] ?? '';
		$road     = $_POST['sec_3_c_paved_road_'.$i] ?? '';
		$location = $_POST['sec_3_c_land_location_'.$i] ?? '';

		if ($district=='' && $area=='' && $road=='' && $location=='') continue;

		$existingImage = $_POST['sec_3_c_existing_image_'.$i] ?? '';
		$imageName = $existingImage;

		if (isset($_FILES['sec_3_c_image_'.$i]) && $_FILES['sec_3_c_image_'.$i]['error']==0)
		{
			$allowed = ['jpg','jpeg','png'];
			$ext = strtolower(pathinfo($_FILES['sec_3_c_image_'.$i]['name'],PATHINFO_EXTENSION));

			if (in_array($ext,$allowed))
			{
				$tmpPath = $_FILES['sec_3_c_image_'.$i]['tmp_name'];

				$newName = time().'_'.rand(1000,9999).'.jpg';
				$targetPath = $upload_dir.$newName;

				if ($ext == "png")
					$image = imagecreatefrompng($tmpPath);
				else
					$image = imagecreatefromjpeg($tmpPath);

				$width = imagesx($image);
				$height = imagesy($image);

				$newWidth = 400;
				$newHeight = ($height/$width)*$newWidth;

				$newImage = imagecreatetruecolor($newWidth,$newHeight);

				imagecopyresampled(
					$newImage,
					$image,
					0,0,0,0,
					$newWidth,$newHeight,
					$width,$height
				);

				imagejpeg($newImage,$targetPath,85);

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
            "'.$survey_id.'",
            "'.$district.'",
            "'.$area.'",
            "'.$road.'",
            "'.$location.'",
            "'.$imageName.'"
        )';

		execute_query($sql);
	}
}
function uploadSocietyImage() {
    if (!empty($_FILES['society_photo']['name'])) {

        $filename = $_FILES['society_photo']['name'];
        $ext  = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);

        $apex_id = $_REQUEST['apex_code'] ?? '';

        $new_filename = $name . '_' . $apex_id . '.' . $ext;

        $upload_dir = dirname(__DIR__) . "/user_data/society_img/";

        /* create folder if not exists */
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $target_file = $upload_dir . $new_filename;

        /* upload file */
        if (move_uploaded_file($_FILES['society_photo']['tmp_name'], $target_file)) {

            $sql = 'UPDATE apex_si_1_1 
                SET photo_id="' . $new_filename . '" 
                WHERE sno="' . $_POST['survey_id'] . '"';

            execute_query($sql);

        } else {
            echo "File upload failed";
        }
    }

}
?>


