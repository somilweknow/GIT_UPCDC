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

elseif($id=='submit_form_upavp'){
	// print_r($_POST);
	//print_r($_SERVER);
	// echo $_POST['apex_code'];
	// echo $_POST['survey_id'];
	
	if ($_POST['survey_id'] == '') {
        echo $sql = 'INSERT INTO `apex_si_1_1` (`apex_id`,`longitude`,`latitude`,`committee_status`,`email_id`,`photo_id`,`society_registration_no`,`society_registration_date`,`division_name`,`district_name`,`tehseel_name`,`mobile_number`,`nagar_nigam`,`liquidation`,`liquidation_date`,`liquidation_status`,`litigation`,`litigation_remark`) VALUES ("' . $_POST['apex_code'] . '","' . $_POST['longitude'] . '","' . $_POST['latitude'] . '","' . $_POST['committee_status'] . '","' . $_POST['email_id'] . '","' . $_POST['photo_id'] . '","' . $_POST['society_registration_no'] . '","' . $_POST['society_registration_date'] . '","' . $_POST['division_name'] . '","' . $_POST['district_name'] . '","' . $_POST['tehseel_name'] . '","' . $_POST['mobile_number'] . '","' . $_POST['nagar_nigam'] . '","' . $_POST['liquidation'] . '","' . $_POST['liquidation_date'] . '","' . $_POST['liquidation_status'] . '","' . $_POST['litigation'] . '","' . $_POST['litigation_remark'] . '")';

        execute_query($sql);
        if (mysqli_error($db)) {
            $data[] = array("id" => "error", "error" => "Error# " . mysqli_error($db) . ' >> ' . $sql);
        } else {
            $id = mysqli_insert_id($db);
            $data[] = array("id" => $id);
        }

        $sql = 'SELECT * FROM apex_si_1_1 WHERE sno="' . $id . '"';
        $survey_invoice = mysqli_fetch_assoc(execute_query($sql));

        $sql = 'SELECT * FROM apex WHERE sno="' . $survey_invoice['apex_id'] . '"';
        $society = mysqli_fetch_assoc(execute_query($sql));

        if ($_FILES['society_photo']['name'] != '') {
            $society_image = upload_img($_FILES['society_photo'], $society, "society_name_" . $survey_invoice['sno']);
            if ($society_image['error'] == 1) {
                $sql = 'UPDATE apex_si_1_1 SET photo_id="' . $society_image['file_name'] . '" WHERE sno="' . $id . '"';
                execute_query($sql);
                $data[] = array("id" => "Update", "msg" => $society_image['msg']);
            } else {
                $data[] = array("id" => "error", "error" => $society_image['msg']);
            }
        }

        var_dump($_POST);
        exit;

    } else {

        switch ($_POST['current_step_count']) {
            case 0: {
                echo $sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
				$society = mysqli_fetch_assoc(execute_query($sql));

                if ($_FILES['society_photo']['name'] != '') {
                    $society_image = upload_img($_FILES['society_photo'], $society, "society_name_" . $apex_si_1_1['sno']);
                    if ($society_image['error'] == 1) {
                        $sql = 'UPDATE apex_si_1_1 SET photo_id="' . $society_image['file_name'] . '" WHERE sno="' . $_POST['survey_id'] . '"';
                        execute_query($sql);
                        $data[] = array("id" => "Update", "msg" => $society_image['msg']);
                    } else {
                        $data[] = array("id" => "error", "error" => $society_image['msg']);
                    }
                }

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
                    division_name = "' . $_POST['division_name'] . '",
                    district_name = "' . $_POST['district_name'] . '",
                    tehseel_name = "' . $_POST['tehseel_name'] . '",
                    mobile_number = "' . $_POST['mobile_number'] . '",
                    nagar_nigam = "' . $_POST['nagar_nigam'] . '",
                    liquidation = "' . $_POST['liquidation'] . '",
                    liquidation_date = "' . $_POST['liquidation_date'] . '",
                    liquidation_status = "' . $_POST['liquidation_status'] . '",
                    litigation = "' . $_POST['litigation'] . '",
                    litigation_remark = "' . $_POST['litigation_remark'] . '"
                    WHERE sno = ' . $_POST['survey_id'];

                execute_query($sql);

                if (mysqli_error($db)) {
                    $data[] = array("id" => "error", "error" => "sec-1,1.1.Unable to save data. " . mysqli_error($db));
                } else {
                    $data[] = array("id" => "Update", "msg" => "1,1.1.Data Saved");
                }

                break;
            }
			case 1:{
				echo $sql = 'select * from apex_si_1_1 where sno="'.$_POST['survey_id'].'"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				echo $sql = 'select * from apex where sno="'.$survey_invoice['apex_id'].'"';
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

				break;
			}
			case 2: {
				echo $sql = 'select * from apex_si_1_1 where sno="' . $_POST['survey_id'] . '"';
				$survey_invoice = mysqli_fetch_assoc(execute_query($sql));
				echo $sql = 'select * from apex where sno="' . $survey_invoice['apex_id'] . '"';
				$society = mysqli_fetch_assoc(execute_query($sql));

				$sql = 'select * from survey_invoice_plot_details where survey_id="'.$_POST['survey_id'].'"';
				$res_new_plot = execute_query($sql);
				if(mysqli_num_rows($res_new_plot)==1){
					$row_new_plot = mysqli_fetch_assoc($res_new_plot);
				}
				else{
					$sql = 'insert into survey_invoice_plot_details (survey_id) values("'.$_POST['survey_id'].'")';
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
				sec_3_building_area = "'.$_POST['sec_3_building_area'].'",
				sec_3_building_rent = "'.$_POST['sec_3_building_rent'].'",
				sec_3_remark = "'.$_POST['society_building_remark'].'",
				remarks = "'.$_POST['sec_new_remarks'].'",
				is_map = "'.$_POST['sec_3_is_map'].'",
				map_accept = "'.$_POST['sec_3_map_accept'].'"
				where sno="'.$row_new_plot['sno'].'"';
				execute_query($sql);
				if(mysqli_error($db)){
					//echo mysqli_error($db).$sql;
					$data[] = array("id"=>"error", "error"=>"3.1.124.Unable to save data.");
				}
				else{
					$data[] = array("id"=>"Update", "msg"=>"sec-3.1.124. Data Saved");	
				}

				$sql_delete = 'DELETE FROM survey_invoice_new_sec_3_9 WHERE survey_id = "'.$_POST['survey_id'].'"';
				execute_query($sql_delete);
				if (mysqli_error($db)) {
					$data[] = ["id" => "error", "error" => "Building Details: Unable to delete existing rows.", "sql" => $sql_delete, "db_error" => mysqli_error($db)];
				} else {
					$data[] = ["id" => "update", "msg" => "Building Details: Existing rows deleted."];
				}

				$flat_areas = [];
				$flat_types = [];
				foreach ($_POST as $key => $val) {
					if (strpos($key, 'sec_3_flat_area_') === 0) {
						$index = str_replace('sec_3_flat_area_', '', $key);
						$flat_areas[$index] = $val;
					}
					if (strpos($key, 'sec_3_flat_type_') === 0) {
						$index = str_replace('sec_3_flat_type_', '', $key);
						$flat_types[$index] = $val;
					}
				}
				foreach ($flat_areas as $i => $area_val) {
					$area_val_esc = mysqli_real_escape_string($db, trim($area_val ?? ''));
					$type_val_esc = mysqli_real_escape_string($db, trim($flat_types[$i] ?? ''));
					if ($area_val_esc === '' && $type_val_esc === '') continue;
					$sql_insert = "INSERT INTO survey_invoice_new_sec_3_9 (survey_id, sec_3_flat_area, sec_3_flat_type, created_by, created_at)
								VALUES ('".$_POST['survey_id']."', '{$area_val_esc}', '{$type_val_esc}', '" . mysqli_real_escape_string($db, $_SESSION['user_id'] ?? 'system') . "', '" . date('Y-m-d H:i:s') . "')";
					execute_query($sql_insert);
					if (mysqli_error($db)) {
						$data[] = ["id" => "error", "error" => "Building Details: Unable to save row {$i}", "sql" => $sql_insert, "db_error" => mysqli_error($db)];
					} else {
						$data[] = ["id" => "update", "msg" => "Building Details: Row {$i} saved."];
					}
				}

				$sql = 'delete from survey_invoice_sec_3_5 where survey_id="' . $_POST['survey_id'] . '"';
				execute_query($sql);
				for ($i = 1; $i <= $_POST['sec_3_c_id']; $i++) {
					if ($_POST['sec_3_c_length_1'] != "" && $_POST['sec_3_c_length_1'] != "0") {
						$sql = 'insert into survey_invoice_sec_3_5 (survey_id, land_type, location, total_area, edition_time) values("' . $_POST['survey_id'] . '", "' . $_POST['sec_3_c_vacant_land_status_' . $i] . '", "' . $_POST['sec_3_c_land_location_' . $i] . '", "' . $_POST['sec_3_c_length_' . $i] . '", "' . date("Y-m-d H:i:s") . '")';
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

				// $sql = 'UPDATE survey_invoice SET society_building_ownership="' . $sec_3_ownership . '", society_building_rent_amount="' . $society_building_rent_amount . '", society_building_area="' . $society_building_area . '", edition_time="' . date("Y-m-d H:i:s") . '" WHERE sno="'.$_POST['survey_id'].'"';
				// execute_query($sql);
				// if (mysqli_error($db)) {
				// 	$data[] = array("id" => "error", "error" => "7.1.Unable to save survey_invoice.", "sql"=>$sql, "db_error"=>mysqli_error($db));
				// } else {
				// 	$data[] = array("id" => "Update", "msg" => "7.1.Data Saved");
				// }

				$sql = 'DELETE FROM survey_invoice_sec_3c WHERE survey_id="'.$_POST['survey_id'].'"';
				execute_query($sql);
				if (mysqli_error($db)) {
					$data[] = ["id"=>"error", "error"=>"Unable to delete existing sec_3c rows", "sql"=>$sql, "db_error"=>mysqli_error($db)];
				}
				$sec_3_c_count = intval($_POST['sec_3_c_land_id'] ?? 0);
				if ($sec_3_c_count <= 0) $sec_3_c_count = 1;
				for ($i = 1; $i <= $sec_3_c_count; $i++) {
					$area = mysqli_real_escape_string($db, $_POST['sec_3_c_area_' . $i] ?? '');
					$land_status = mysqli_real_escape_string($db, $_POST['sec_3_c_land_status_' . $i] ?? '');
					$land_location = mysqli_real_escape_string($db, $_POST['sec_3_c_land_location_' . $i] ?? '');
					$remark = mysqli_real_escape_string($db, $_POST['sec_3_c_land_remark_' . $i] ?? '');
					if ($area == '' && $land_status == '' && $land_location == '' && $remark == '') continue;
					$sql = 'INSERT INTO survey_invoice_sec_3c (survey_id, area, land_status, land_location, remark, created_at)
							VALUES ("'.$_POST['survey_id'].'", "' . $area . '", "' . $land_status . '", "' . $land_location . '", "' . $remark . '", "' . date('Y-m-d H:i:s') . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						$data[] = ["id"=>"error", "error"=>"Unable to insert sec_3c row {$i}", "sql"=>$sql, "db_error"=>mysqli_error($db)];
					}
				}

				$sql = 'SELECT * FROM survey_invoice_sec_2_1 WHERE survey_id="'.$_POST['survey_id'].'"';
				$res_2_1 = execute_query($sql);
				if (mysqli_num_rows($res_2_1) == 1) {
					$row_2_1 = mysqli_fetch_assoc($res_2_1);
				} else {
					$sql = 'INSERT INTO survey_invoice_sec_2_1 (survey_id, created_at) VALUES("'.$_POST['survey_id'].'", "' . date('Y-m-d H:i:s') . '")';
					execute_query($sql);
					if (mysqli_error($db)) {
						$data[] = array("id" => "error", "error" => "21.Unable to save data.", "sql"=>$sql, "db_error"=>mysqli_error($db));
					} else {
						$row_2_1['sno'] = mysqli_insert_id($db);
						$data[] = array("id" => "Update", "msg" => "21.Data Saved");
					}
				}
				$sec_6_access_road = mysqli_real_escape_string($db, $_POST['sec_6_access_road'] ?? '');
				$sec_6_2_truck_not_reach = mysqli_real_escape_string($db, $_POST['sec_6_2_truck_not_reach'] ?? '');
				$sec_6_paved_road = mysqli_real_escape_string($db, $_POST['sec_6_paved_road'] ?? '');
				$sec_8_plot_frontage = mysqli_real_escape_string($db, $_POST['sec_8_plot_frontage'] ?? '');
				$sec_8_school_hosp_status = mysqli_real_escape_string($db, $_POST['sec_8_school_hosp_status'] ?? '');
				$sno_2_1 = $row_2_1['sno'];
				$sql = 'UPDATE survey_invoice_sec_2_1 SET 
							sec_6_road="' . $sec_6_access_road . '",
							distance_from_approach_road="' . $sec_6_2_truck_not_reach . '",
							approach_road="' . $sec_6_paved_road . '",
							plot_frontage="' . $sec_8_plot_frontage . '",
							school_hosp_status="' . $sec_8_school_hosp_status . '"
						WHERE sno=' . mysqli_real_escape_string($db, $sno_2_1);
				execute_query($sql);
				if (mysqli_error($db)) {
					$data[] = array("id" => "error", "error" => "7.11.Unable to save data.", "sql"=>$sql, "db_error"=>mysqli_error($db));
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
