<?php
date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

$q = htmlspecialchars(urldecode(strtoupper($_REQUEST["term"])), ENT_QUOTES);
if (!$q)
    return;

if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
} else {
    $id = '';
}
if ($id == 'verify_dist_form') {
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
}