<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");
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
} elseif ($id == 'verify_otp_ldb') {
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
} elseif ($id == 'submit_form_ldb') {

    if ($_POST['survey_id'] == '') {
        try {

            $sql = "INSERT INTO apex_si_1_1 
                    (`apex_id`,`longitude`,`latitude`,`committee_status`,`email_id`,
                    `society_registration_no`,`society_registration_date`,`prakhand_name`,
                    `members_no`,`active_members_no`,`inactive_members_no`,`new_members`,
                    `share_capital`,`inactive_to_active_no`,`total_members`,
                    `society_remark`,`society_objective`,`website`,
                    `regional_office`,`district_branch_office`,`branch_office`,`pan_no`, `tan_no`, `gst_no`,`mobile_no`, `education_center`, `photo_id`) 
                    VALUES (
                    '{$_POST['apex_code']}',
                    '{$_POST['longitude']}',
                    '{$_POST['latitude']}',
                    '{$_POST['committee_status']}',
                    '{$_POST['email_id']}',
                    '{$_POST['society_registration_no']}',
                    '{$_POST['society_registration_date']}',
                    '{$_POST['prakhand_name']}',
                    '{$_POST['members_no']}',
                    '{$_POST['active_members_no']}',
                    '{$_POST['inactive_members_no']}',
                    '{$_POST['new_members']}',
                    '{$_POST['share_capital']}',
                    '{$_POST['inactive_to_active_no']}',
                    '{$_POST['total_members']}',
                    '{$_POST['society_remark']}',
                    '{$_POST['society_objective']}',
                    '{$_POST['website']}',
                    '{$_POST['regional_office']}',
                    '{$_POST['district_branch_office']}',
                    '{$_POST['branch_office']}',
                    '{$_POST['pan_no']}',
                    '{$_POST['tan_no']}',
                    '{$_POST['gstin_no']}',
                    '{$_POST['phone_no']}',
                    '{$_POST['photo_id']}',
                    '{$_FILES['society_photo']['name']}";

            execute_query($sql);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();

        }
        if (mysqli_error($db)) {
            $data[] = array("id" => "error", "error" => "Error# " . mysqli_error($db) . ' >> ' . $sql);
        } else {
            $id = mysqli_insert_id($db);
            $data[] = array("id" => $id);
        }
        $sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
        $survey_invoice = mysqli_fetch_assoc(execute_query($sql));

        $sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
        $society = mysqli_fetch_assoc(execute_query($sql));

        // Upload Society Image
        uploadSocietyImage();
    }
    else {
        switch ($_POST['current_step_count']) {
            case 0: {
                $sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
                $survey_invoice = mysqli_fetch_assoc(execute_query($sql));

                $sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
                $society = mysqli_fetch_assoc(execute_query($sql));

                $sql = 'UPDATE apex_si_1_1 SET 
                    edited_by = "",
                    edition_time = "' . date("Y-m-d H:i:s") . '",
                    apex_id = "' . $_POST['apex_code'] . '",
                    latitude = "' . $_POST['latitude'] . '",
                    longitude = "' . $_POST['longitude'] . '",
                    committee_status = "' . $_POST['committee_status'] . '",
                    email_id = "' . $_POST['email_id'] . '",
                    photo_id = "' . $_POST['photo_id'] . '",
                    society_registration_no = "' . $_POST['society_registration_no'] . '",
                    society_registration_date = "' . $_POST['society_registration_date'] . '",
                    members_no = "' . $_POST['members_no'] . '",
                    inactive_members_no = "' . $_POST['inactive_members_no'] . '",
                    active_members_no = "' . $_POST['active_members_no'] . '",
                    new_members = "' . $_POST['new_members'] . '",
                    share_capital = "' . $_POST['share_capital'] . '",
                    inactive_to_active_no = "' . $_POST['inactive_to_active_no'] . '",
                    total_members = "' . $_POST['total_members'] . '",
                    society_remark = "' . $_POST['society_remark'] . '",
                    society_objective = "' . $_POST['society_objective'] . '",
                    website = "' . $_POST['website'] . '",
                    regional_office = "' . $_POST['regional_office'] . '",
                    district_branch_office = "' . $_POST['district_branch_office'] . '",
                    branch_office = "' . $_POST['branch_office'] . '",
                    education_center = "' . $_POST['education_center'] . '",
                    pan_no = "' . $_POST['pan_no'] . '",
                    tan_no = "' . $_POST['tan_no'] . '",
                    gst_no = "' . $_POST['gstin_no'] . '",
                    mobile_no = "' . $_POST['phone_no'] . '",
                    photo_id = "' . $_POST['society_photo'] . '",
                    education_center = "' . $_POST['education_center'] . '",
                    photo_id = "' .$_FILES['society_photo']['name']. '"
                    WHERE sno = ' . $_POST['survey_id'];
                try {
                    execute_query($sql);
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
                if (mysqli_error($db)) {
                    $data[] = array("id" => "error", "error" => "sec-1,1.1.Unable to save data.");
                } else {
                    $data[] = array("id" => "Update", "msg" => "1,1.1.Data Saved");
                }

                /* ==============================
               SAVE ZONE / TRAINING / OTHER OFFICE
               TABLE: apex_zone_details
            ================================ */

                if (isset($_POST['survey_id']) && !empty($_POST['survey_id'])) {

                    $survey_id = $_POST['survey_id'];

                    /* delete old rows */
                    $sql = 'DELETE FROM apex_zone_details WHERE survey_id="'.$survey_id.'"';
                    execute_query($sql);

                    /* upload folder */
                    $upload_dir = '../user_data/zones/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir,0777,true);
                    }

                    /* =====================================================
                       1. ZONE OFFICES (क्षेत्रीय कार्यालय)
                       office_type = 1
                    ===================================================== */

                    if(isset($_POST['zone_name'])){

                        foreach($_POST['zone_name'] as $i=>$name){

                            if(trim($name)=='') continue;

                            $mobile  = $_POST['zone_mobile'][$i] ?? '';
                            $email   = $_POST['zone_email'][$i] ?? '';
                            $address = $_POST['zone_address'][$i] ?? '';

                            $image = $_POST['existing_zone_image'][$i] ?? '';

                            if(!empty($_FILES['zone_image']['name'][$i])){

                                $ext = pathinfo($_FILES['zone_image']['name'][$i],PATHINFO_EXTENSION);
                                $image = time().'_'.$survey_id.'_zone_'.$i.'.'.$ext;

                                move_uploaded_file(
                                    $_FILES['zone_image']['tmp_name'][$i],
                                    $upload_dir.$image
                                );
                            }

                            $sql='INSERT INTO apex_zone_details
                (survey_id,office_type,zone_name,zone_mobile,zone_email,zone_address,zone_image,created_at)
                VALUES
                ("'.$survey_id.'","1","'.$name.'","'.$mobile.'","'.$email.'","'.$address.'","'.$image.'",NOW())';

                            execute_query($sql);
                        }
                    }


                    /* =====================================================
                       2. PRAKHAND OFFICES (जनपदीय शाखा)
                       office_type = 2
                    ===================================================== */

                    if(isset($_POST['prakhand_name'])){

                        foreach($_POST['prakhand_name'] as $i=>$name){

                            if(trim($name)=='') continue;

                            $mobile  = $_POST['prakhand_mobile'][$i] ?? '';
                            $email   = $_POST['prakhand_email'][$i] ?? '';
                            $address = $_POST['prakhand_address'][$i] ?? '';

                            $image = $_POST['existing_prakhand_image'][$i] ?? '';

                            if(!empty($_FILES['prakhand_image']['name'][$i])){

                                $ext = pathinfo($_FILES['prakhand_image']['name'][$i],PATHINFO_EXTENSION);
                                $image = time().'_'.$survey_id.'_prakhand_'.$i.'.'.$ext;

                                move_uploaded_file(
                                    $_FILES['prakhand_image']['tmp_name'][$i],
                                    $upload_dir.$image
                                );
                            }

                            $sql='INSERT INTO apex_zone_details
                (survey_id,office_type,zone_name,zone_mobile,zone_email,zone_address,zone_image,created_at)
                VALUES
                ("'.$survey_id.'","2","'.$name.'","'.$mobile.'","'.$email.'","'.$address.'","'.$image.'",NOW())';

                            execute_query($sql);
                        }
                    }


                    /* =====================================================
                       3. BRANCH OFFICES (शाखा कार्यालय)
                       office_type = 3
                    ===================================================== */

                    if(isset($_POST['branch_office_name'])){

                        foreach($_POST['branch_office_name'] as $i=>$name){

                            if(trim($name)=='') continue;

                            $mobile  = $_POST['branch_office_mobile'][$i] ?? '';
                            $email   = $_POST['branch_office_email'][$i] ?? '';
                            $address = $_POST['branch_office_address'][$i] ?? '';

                            $image = $_POST['existing_branch_image'][$i] ?? '';

                            if(!empty($_FILES['branch_office_image']['name'][$i])){

                                $ext = pathinfo($_FILES['branch_office_image']['name'][$i],PATHINFO_EXTENSION);
                                $image = time().'_'.$survey_id.'_branch_'.$i.'.'.$ext;

                                move_uploaded_file(
                                    $_FILES['branch_office_image']['tmp_name'][$i],
                                    $upload_dir.$image
                                );
                            }

                            $sql='INSERT INTO apex_zone_details
                (survey_id,office_type,zone_name,zone_mobile,zone_email,zone_address,zone_image,created_at)
                VALUES
                ("'.$survey_id.'","3","'.$name.'","'.$mobile.'","'.$email.'","'.$address.'","'.$image.'",NOW())';

                            execute_query($sql);
                        }
                    }


                    /* =====================================================
                       4. TRAINING CENTERS (प्रशिक्षण केंद्र)
                       office_type = 4
                    ===================================================== */

                    if(isset($_POST['training_center_name'])){

                        foreach($_POST['training_center_name'] as $i=>$name){

                            if(trim($name)=='') continue;

                            $mobile  = $_POST['training_center_mobile'][$i] ?? '';
                            $email   = $_POST['training_center_email'][$i] ?? '';
                            $address = $_POST['training_center_address'][$i] ?? '';

                            $image = $_POST['existing_training_image'][$i] ?? '';
                            
                            if(!empty($_FILES['training_center_image']['name'][$i])){

                                $ext = pathinfo($_FILES['training_center_image']['name'][$i],PATHINFO_EXTENSION);
                                $image = time().'_'.$survey_id.'_training_'.$i.'.'.$ext;

                                move_uploaded_file(
                                    $_FILES['training_center_image']['tmp_name'][$i],
                                    $upload_dir.$image
                                );
                            }

                            $sql='INSERT INTO apex_zone_details
                                (survey_id,office_type,zone_name,zone_mobile,zone_email,zone_address,zone_image,created_at)
                                VALUES
                                ("'.$survey_id.'","4","'.$name.'","'.$mobile.'","'.$email.'","'.$address.'","'.$image.'",NOW())';
                            execute_query($sql);
                        }
                    }

                }
                // Upload Society Image
                uploadSocietyImage();
                break;
            }
            case 1: {
                $sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
                $survey_invoice = mysqli_fetch_assoc(execute_query($sql));
                $sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
                $society = mysqli_fetch_assoc(execute_query($sql));

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
                                //echo mysqli_error($db);
                                $data[] = array("id" => "error", "error" => "6.2.Unable to save data.");
                            } else {
                                $data[] = array("id" => "Update", "msg" => "6.2.Data Saved");
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

               if (isset($_POST['survey_id'])) {
                saveApexFinancialInfo($_POST);
                save_apex_five_year_business_activity($_POST['survey_id']);
               }

                break;
            }

            case 2: {
                $sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
                $survey_invoice = mysqli_fetch_assoc(execute_query($sql));
                $sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
                $society = mysqli_fetch_assoc(execute_query($sql));

                $sql_human_delete = 'DELETE FROM apex_si_1_3 WHERE survey_id = "' . $_POST['survey_id'] . '"';
                execute_query($sql_human_delete);
                if (mysqli_error($db)) {
                    $data[] = array("id" => "error", "error" => "Human rows: Unable to delete existing rows.");
                } else {
                    $data[] = array("id" => "update", "msg" => "Human rows: Existing rows deleted.");
                }

                $posts = $_POST['post_id'] ?? [];
                $sanctioned = $_POST['sanctioned_post'] ?? [];
                $vacant = $_POST['vacant_post'] ?? [];

                for ($i = 0; $i < count($posts); $i++) {
                    $post_id = mysqli_real_escape_string($db, $posts[$i]);
                    $sanctioned_post = mysqli_real_escape_string($db, $sanctioned[$i]);
                    $vacant_post = mysqli_real_escape_string($db, $vacant[$i]);

                    if ($post_id === '' && $sanctioned_post === '' && $vacant_post === '')
                        continue;

                    $sql_insert_h = "INSERT INTO apex_si_1_3 
						(survey_id, post_id, sanctioned_post, vacant_post, created_by, created_at)
						VALUES (
							'{$_POST['survey_id']}',
							'{$post_id}',
							'{$sanctioned_post}',
							'{$vacant_post}',
							'{$_SESSION['user_id']}',
							'" . date('Y-m-d H:i:s') . "'
						)";

                    execute_query($sql_insert_h);

                    if (mysqli_error($db)) {
                        $data[] = ["id" => "error", "error" => "Human rows: Unable to save row #" . ($i + 1)];
                    } else {
                        $data[] = ["id" => "update", "msg" => "Human rows: Row #" . ($i + 1) . " saved."];
                    }
                }

                if (isset($_POST['survey_id'])) {
                    saveApexHumanResource($_POST['survey_id']);
                    save_apex_officers_info($_POST['survey_id']);
                }

                break;
            }
            case 3: {
                try {
                    save_training_centers($_POST['survey_id']);
                } catch (Exception $ex) {
                    $ex->getMessage();
                }

                $sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
                $survey_invoice = mysqli_fetch_assoc(execute_query($sql));
                $sql = 'select * from test2 where sno="' . $survey_invoice['apex_id'] . '"';
                $society = mysqli_fetch_assoc(execute_query($sql));

                $sql = 'update apex_si_1_1 set
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

                $sql = 'DELETE FROM survey_invoice_other_land WHERE survey_id="' . $_POST['survey_id'] . '"';
                execute_query($sql);

                $max = isset($_POST['other_land_count']) ? intval($_POST['other_land_count']) : 0;
                if ($max < 1) $max = 1; // at least 1

                for ($i = 1; $i <= $max; $i++) {
                    $district = isset($_POST['other_land_district_' . $i]) ? $_POST['other_land_district_' . $i] : '';
                    $tehsil = isset($_POST['other_land_tehsil_' . $i]) ? $_POST['other_land_tehsil_' . $i] : '';
                    $area_type = isset($_POST['other_land_area_type_' . $i]) ? $_POST['other_land_area_type_' . $i] : '';
                    $land_area = isset($_POST['other_land_land_area_' . $i]) ? $_POST['other_land_land_area_' . $i] : '';
                    $ownership = isset($_POST['other_land_ownership_' . $i]) ? $_POST['other_land_ownership_' . $i] : '';
                    $other_owner = isset($_POST['other_land_other_owner_' . $i]) ? $_POST['other_land_other_owner_' . $i] : '';
                    $land_status = isset($_POST['other_land_land_status_' . $i]) ? $_POST['other_land_land_status_' . $i] : '';
                    $construction = isset($_POST['other_land_construction_' . $i]) ? $_POST['other_land_construction_' . $i] : '';
                    $other_construct = isset($_POST['other_land_other_construct_' . $i]) ? $_POST['other_land_other_construct_' . $i] : '';
                    $address = isset($_POST['other_land_address_' . $i]) ? $_POST['other_land_address_' . $i] : '';
                    $latitude = isset($_POST['other_land_latitude_' . $i]) ? $_POST['other_land_latitude_' . $i] : '';
                    $longitude = isset($_POST['other_land_longitude_' . $i]) ? $_POST['other_land_longitude_' . $i] : '';
                    $location_mode = isset($_POST['other_land_location_mode_' . $i]) ? $_POST['other_land_location_mode_' . $i] : '';

                    $sql = 'INSERT INTO survey_invoice_other_land (survey_id, district, tehsil, area_type, land_area, ownership, other_owner, land_status, construction, other_construction, address, latitude, longitude, location_mode)
							VALUES ("' . $_POST['survey_id'] . '", "' . $district . '", "' . $tehsil . '", "' . $area_type . '", "' . $land_area . '", "' . $ownership . '", "' . $other_owner . '", "' . $land_status . '", "' . $construction . '", "' . $other_construct . '", "' . $address . '", "' . $latitude . '", "' . $longitude . '", "' . $location_mode . '")';
                    execute_query($sql);
                }
                if (mysqli_error($db)) {
                    $data[] = array("id" => "error", "error" => "7.12.Unable to save data.");
                } else {
                    $data[] = array("id" => "Update", "msg" => "7.12.Data Saved");
                }

                break;
            }
            case 4: {
                try {
                    saveApexEmptyLandInfo($_POST['survey_id']);
                    saveLandBoundaryDetails($_POST['survey_id']);
                    savePlotDetails($_POST['survey_id']);
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                }

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
        }
    }
}

if (empty($data) != true) {
    echo json_encode($data);
}

function upload_img($name, $society, $new_name, $maxDim = 1500)
{

    $file_name = $name['tmp_name'];
    list($width, $height, $type, $attr) = getimagesize($file_name);
    if ($width > $maxDim || $height > $maxDim) {
        $target_filename = $file_name;
        $ratio = $width / $height;
        if ($ratio > 1) {
            $new_width = (int) round($maxDim);
            $new_height = (int) round($maxDim / $ratio);
        } else {
            $new_width = (int) round($maxDim * $ratio);
            $new_height = (int) round($maxDim);
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
    $target_dir = '../user_data/';
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

    // Check if file already exists

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

        try {
        execute_query($sql);

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $index++;
    }

    return true;
}
function save_apex_five_year_business_activity($survey_id)
{
    try {

        if (!isset($_POST['financial_year'])) {
            return;
        }

        $years = $_POST['financial_year'];
        $loan_distribution = $_POST['loan_distribution'];
        $recovery_amount = $_POST['recovery_amount'];
        $term_deposit = $_POST['term_deposit'];
        $gross_npa = $_POST['gross_npa'];
        $net_npa = $_POST['net_npa'];

        for ($i = 0; $i < count($years); $i++) {

            $year = $years[$i];

            $loan = $loan_distribution[$i];
            $recovery = $recovery_amount[$i];
            $deposit = $term_deposit[$i];
            $gross = $gross_npa[$i];
            $net = $net_npa[$i];

            $sql = "INSERT INTO apex_five_year_business_activity
                    (survey_id, financial_year, loan_distribution, recovery_amount, term_deposit, gross_npa, net_npa)
                    VALUES
                    ('$survey_id','$year','$loan','$recovery','$deposit','$gross','$net')

                    ON DUPLICATE KEY UPDATE
                    loan_distribution='$loan',
                    recovery_amount='$recovery',
                    term_deposit='$deposit',
                    gross_npa='$gross',
                    net_npa='$net'";

            execute_query($sql);
        }

    } catch (Exception $e) {

        $exception_message = $e->getMessage();
        exit;

    }
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

function save_apex_officers_info($survey_id)
{
    try {

        if(!isset($_POST['officer_count'])) return;

        $count = $_POST['officer_count'];

        // remove previous rows
        $delete_sql = "DELETE FROM apex_officers_info WHERE survey_id='".$survey_id."'";
        execute_query($delete_sql);

        for($i=1; $i <= $count; $i++){

            $designation = $_POST['officer_designation_'.$i] ?? '';
            $name = $_POST['officer_name_'.$i] ?? '';
            $joining = $_POST['officer_joining_date_'.$i] ?? '';
            $section = $_POST['officer_section_'.$i] ?? '';

            if($designation=='' && $name=='') continue;

            $sql = "INSERT INTO apex_officers_info
                    (survey_id,row_no,designation,officer_name,joining_date,officer_section)
                    VALUES
                    ('$survey_id','$i','$designation','$name','$joining','$section')";

            execute_query($sql);

        }

    } catch (Exception $e) {

     echo $e->getMessage();

    }
}
function save_training_centers($survey_id)
{
    if (!$survey_id) {
        return ["id" => "error", "msg" => "Survey ID missing"];
    }

    $data = [];

    /* -----------------------------
       DELETE OLD RECORDS
    ----------------------------- */
    $sql = "DELETE FROM training_centers WHERE survey_id = '$survey_id'";
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
            survey_id = "' . $survey_id . '",
            row_no = "' . $i . '",

            cpmt_name = "' . ($_POST['sec_3_cpmt_' . $i] ?? '') . '",
            address = "' . ($_POST['sec_3_address_' . $i] ?? '') . '",
            principal_name = "' . ($_POST['sec_3_principal_name_' . $i] ?? '') . '",
            post = "' . ($_POST['sec_3_post_' . $i] ?? '') . '",

            principal_house = "' . ($_POST['sec_3_principal_house_' . $i] ?? '') . '",
            principal_house_no = "' . ($_POST['sec_3_principal_house_no_' . $i] ?? '') . '",

            principal_office = "' . ($_POST['sec_3_principal_office_' . $i] ?? '') . '",
            principal_office_no = "' . ($_POST['sec_3_principal_office_no_' . $i] ?? '') . '",

            class_no = "' . ($_POST['sec_3_class_no_' . $i] ?? '') . '",
            class_capacity = "' . ($_POST['sec_3_class_capacity_' . $i] ?? '') . '",

            hostel_no = "' . ($_POST['sec_3_hostel_no_' . $i] ?? '') . '",
            hostel_capacity = "' . ($_POST['sec_3_hostel_capacity_' . $i] ?? '') . '",

            library_no = "' . ($_POST['sec_3_library_no_' . $i] ?? '') . '",
            library_capacity = "' . ($_POST['sec_3_library_capacity_' . $i] ?? '') . '",

            computer_lab_no = "' . ($_POST['sec_3_computer_lab_no_' . $i] ?? '') . '",
            computer_lab_capacity = "' . ($_POST['sec_3_computer_lab_capacity_' . $i] ?? '') . '",

            teacher_no = "' . ($_POST['sec_3_teacher_no_' . $i] ?? '') . '",
            employee_remarks = "' . ($_POST['sec_3_employee_remarks_' . $i] ?? '') . '",

            training_sessions_no = "' . ($_POST['sec_3_training_sessions_no_' . $i] ?? '') . '",
            training_subject_name = "' . ($_POST['sec_3_training_subject_name_' . $i] ?? '') . '",
            training_sessions_duration = "' . ($_POST['sec_3_training_sessions_duration_' . $i] ?? '') . '",

            departmental_trainees_no = "' . ($_POST['sec_3_departmental_trainees_no_' . $i] ?? 0) . '",
            non_departmental_trainees_no = "' . ($_POST['sec_3_non_departmental_trainees_no_' . $i] ?? 0) . '",
            trainees_no = "' . ($_POST['sec_3_trainees_no_' . $i] ?? 0) . '",

            departmental_trainees_fee = "' . ($_POST['sec_3_departmental_trainees_fee_' . $i] ?? 0) . '",
            non_departmental_trainees_fee = "' . ($_POST['sec_3_non_departmental_trainees_fee_' . $i] ?? 0) . '",
            trainees_fee = "' . ($_POST['sec_3_trainees_fee_' . $i] ?? 0) . '",

            departmental_hostel_fee = "' . ($_POST['sec_3_departmental_hostel_fee_' . $i] ?? 0) . '",
            non_departmental_hostel_fee = "' . ($_POST['sec_3_non_departmental_hostel_fee_' . $i] ?? 0) . '",
            hostel_fee = "' . ($_POST['sec_3_hostel_fee_' . $i] ?? 0) . '",

            construction_year = "' . ($_POST['sec_3_build_year_' . $i] ?? '') . '",
            operational_year = "' . ($_POST['sec_3_operation_year_' . $i] ?? '') . '",

            center_ref_id = "' . ($_POST['sec_3_training_center_' . $i] ?? '') . '",
            staff_count = "' . ($_POST['sec_3_staff_type_' . $i] ?? '') . '",

            training_course_benefits = "' . ($_POST['sec_3_training_course_benefits_' . $i] ?? '') . '",
            building_hostel_status = "' . ($_POST['sec_3_building_hostel_status_' . $i] ?? '') . '",

            edition_time = "' . date('Y-m-d H:i:s') . '"
        ';

        execute_query($sql);
    }

    return ["id" => "success", "msg" => "Training centers saved"];
}
function saveApexEmptyLandInfo($survey_id)
{
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
function saveLandBoundaryDetails($survey_id)
{
    if (!$survey_id) {
        return false;
    }

    /* -----------------------------
       DELETE OLD RECORD
    ----------------------------- */
    $sql = 'DELETE FROM survey_invoice_sec_3_1 
            WHERE survey_id="' . $survey_id . '"';
    execute_query($sql);


    /* -----------------------------
       INSERT NEW RECORD
    ----------------------------- */
    $sql = 'INSERT INTO survey_invoice_sec_3_1 SET
        survey_id = "' . $survey_id . '",

        east_side = "' . ($_POST['sec_3_a_land_chauhaddi_east'] ?? '') . '",
        west_side = "' . ($_POST['sec_3_a_land_chauhaddi_west'] ?? '') . '",
        north_side = "' . ($_POST['sec_3_a_land_chauhaddi_north'] ?? '') . '",
        south_side = "' . ($_POST['sec_3_a_land_chauhaddi_south'] ?? '') . '",

        on_road_land = "' . ($_POST['sec_3_a_land_on_road'] ?? '') . '",
        front_side = "' . ($_POST['sec_3_a_land_frontage'] ?? '') . '",
        plot_access_road  = "' . ($_POST['sec_6_access_road'] ?? '') . '",
        plot_frontage   = "' . ($_POST['sec_8_plot_frontage'] ?? '') . '",

        remarks = "' . ($_POST['sec_3_a_comment'] ?? '') . '",

        edition_time = "' . date('Y-m-d H:i:s') . '"
    ';

    execute_query($sql);

    return true;
}
function savePlotDetails($survey_id)
{
    if (!$survey_id) {
        return false;
    }

    /* -----------------------------
       DELETE EXISTING RECORD
    ----------------------------- */
    $sql = 'DELETE FROM survey_invoice_plot_details 
            WHERE survey_id="' . $survey_id . '"';
    execute_query($sql);

    /* -----------------------------
       INSERT NEW RECORD
    ----------------------------- */
    $sql = 'INSERT INTO survey_invoice_plot_details SET

        survey_id = "' . $survey_id . '",

        plot_area = "' . ($_POST['sec_new_plot_area'] ?? '') . '",
        plot_revenue_status = "' . ($_POST['sec_new_plot_revenue_status'] ?? '') . '",
        plot_reason_for_not_record = "' . ($_POST['sec_new_plot_reason_for_not_record'] ?? '') . '",
        plot_practices_if_not = "' . ($_POST['sec_new_plot_practices_if_not'] ?? '') . '",
        plot_gata_no = "' . ($_POST['sec_new_plot_gata_no'] ?? '') . '",

        sec_3_ownership = "' . ($_POST['sec_3_ownership'] ?? '') . '",

        society_building_area = "' . ($_POST['sec_3_building_area'] ?? '') . '",
        society_building_rent_amount = "' . ($_POST['sec_3_building_rent'] ?? '') . '",
        society_building_remark = "' . ($_POST['sec_3_remark'] ?? '') . '",

        remarks = "' . ($_POST['sec_new_remarks'] ?? '') . '",

        edition_time = "' . date('Y-m-d H:i:s') . '"
    ';

    execute_query($sql);

    return true;
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