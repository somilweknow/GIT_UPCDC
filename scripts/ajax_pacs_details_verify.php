<?php
date_default_timezone_set('Asia/Calcutta');
include("settings.php");
include("setting_sms.php");

$q = isset($_REQUEST["term"]) ? htmlspecialchars(strtoupper($_REQUEST["term"]), ENT_QUOTES) : '';
$id = $_REQUEST['id'] ?? '';

foreach($_POST as $k => $v){
    if(is_array($v)){
        foreach($v as $key => $val){
            $_POST[$k][$key] = htmlspecialchars($val);
        }
    } else {
        $_POST[$k] = htmlspecialchars($v);    
    }    
}

$data = array();

if($id == 'send_otp'){
    $otp = randomnumber();
    $sql = 'SELECT * FROM pacs_detail_verification_ar WHERE district_id="'.$_POST['ar_district'].'"';
    $result = execute_query($sql);

    if(mysqli_num_rows($result) != 0){
        $row = mysqli_fetch_assoc($result);
        if($row['status'] != '0'){
            $data[] = array("status" => "completed", "msg" => "Verification Already Completed");
        } else {
            $_POST['mobile_number'] = $_POST['mobile_number'] ?: $row['mobile_number'];
            $sql = 'UPDATE pacs_detail_verification_ar SET otp="'.$otp.'" WHERE sno='.$row['sno'];
            execute_query($sql);
            $data[] = array("status" => "otp_sent", "msg" => "Are You Sure");

            $template_id = '1207166661218817759';
            $pe_id = $peID;
            $get_msg = 'Dear User, Your OTP for Pacs Verification is '.$otp.'. Regards, WeKnow Technologies';
            // send_sms($_POST['mobile_number'], $get_msg, $template_id, $pe_id, $hindi = '');
        }
    } else {
		$district_id      = mysqli_real_escape_string($db, trim($_POST['ar_district'] ?? ''));
		$user_id          = mysqli_real_escape_string($db, trim($_SESSION['user_id'] ?? ''));
		$mobile_number    = mysqli_real_escape_string($db, trim($_POST['mobile_number'] ?? ''));
		$ip_address       = mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR'] ?? '');
		$http_referer     = mysqli_real_escape_string($db, $_SERVER['HTTP_REFERER'] ?? '');
		$http_user_agent  = mysqli_real_escape_string($db, $_SERVER['HTTP_USER_AGENT'] ?? '');
		$status           = "0";
		$created_by       = ""; // If blank, no need to sanitize unless dynamically added later
		$creation_time    = date("Y-m-d H:i:s");
		
		
        echo $sql = 'INSERT INTO `pacs_detail_verification_ar` (district_id, ar_id, mobile_number, otp, ip_address, http_referer, http_user_agent, status, created_by, creation_time) 
		 
        VALUES ("'.$district_id.'", "'.$user_id.'", "'.$mobile_number.'", "'.$otp.'", "'.$ip_address.'", "'.$http_referer.'", "'.$http_user_agent.'", "0", "'.$created_by.'", "'.$craetion_time.'")';
		execute_query($sql);
        if(mysqli_error($db)){
            $data[] = array("status" => "error", "msg" => "Error #23 : ".mysqli_error($db).' >> '.$sql);
        } else {
            $data[] = array("status" => "otp_sent", "msg" => "Are You Sure");
            $template_id = '1207166661218817759';
            $pe_id = $peID;
            $get_msg = 'Dear User, Your OTP for Pacs Verification is '.$otp.'. Regards, WeKnow Technologies';
            // send_sms($_POST['mobile_number'], $get_msg, $template_id, $pe_id, $hindi = '');
        }
    }

		foreach ($_POST['selected_pacs'] as $pacsId) {
			$ensureId = mysqli_real_escape_string($db, $_POST['ensure_id'][$pacsId] ?? '');
			$ncdid = mysqli_real_escape_string($db, $_POST['ncd_id'][$pacsId] ?? '');
			
			// Checkbox value: 1 if checked, 0 if not
			$isSelected = mysqli_real_escape_string($db, $_POST['is_selected'][$pacsId] ?? '0');
			// $isSelected = isset($_POST['is_selected'][$pacsId]) ? 1 : 0;

			$sql_check = 'SELECT * FROM pacs_selection WHERE pacs_id="'.$pacsId.'" AND district_id="'.$_POST['val'].'"';
			$res_check = execute_query($sql_check);

			if(mysqli_num_rows($res_check) > 0){
				$sql_update = 'UPDATE pacs_selection SET ensure_id="'.$ensureId.'", ncd_id="'.$ncdid.'", is_selected="'.$isSelected.'", edited_by="'.$_SESSION['user_id'].'", edition_time="'.date("Y-m-d H:i:s").'" WHERE pacs_id="'.$pacsId.'" AND district_id="'.$_POST['val'].'"';
				execute_query($sql_update);
				$data[] = ["status" => "otp_sent", "msg" => "PACS draft saved."];
			} else {
				$sql_insert = 'INSERT INTO pacs_selection (district_id, pacs_id, edited_by, edition_time, ensure_id, ncd_id, is_selected) 
				VALUES ("'.$_POST['val'].'", "'.$pacsId.'", "'.$_SESSION['user_id'].'", "'.date("Y-m-d H:i:s").'", "'.$ensureId.'", "'.$ncdid.'", "'.$isSelected.'")';
				execute_query($sql_insert);
				$data[] = ["status" => "otp_sent", "msg" => "PACS draft saved."];
			}
		}

} elseif($id == 'verify_otp'){
     $sql = 'SELECT * FROM pacs_detail_verification_ar WHERE district_id="'.$_POST['val'].'" AND status=0';
    $result = execute_query($sql);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        if($row['otp'] != $_POST['otp']){
            $msg = "आपका प्रपत्र स्वीकार किया गया है, डैशबोर्ड पर जाकर सर्टिफिकेट डाउनलोड करे";
            $sql = 'UPDATE pacs_detail_verification_ar SET status=1 WHERE sno='.$row['sno'];
            execute_query($sql);
            $sql = 'UPDATE test2 SET ar_verification_id="'.$row['sno'].'" WHERE col2="'.$row['district_id'].'"';
            execute_query($sql);
            $data[] = array("status" => "verified", "msg" => $msg);
        } else {
            $data[] = array("status" => "invalid", "msg" => "Incorrect OTP");
        }
    } else {
        $data[] = array("status" => "notfound", "msg" => "Data not found");
    }
}

if(!empty($data)){
    echo json_encode($data);
}
?>
