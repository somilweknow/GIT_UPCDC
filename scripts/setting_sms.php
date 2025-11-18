<?php

$sql = 'select * from general_settings where `desc` = "sms_user"';
$sms_user = mysqli_fetch_array(execute_query($sql));
$sms_user = $sms_user['rate'];

$sql = 'select * from general_settings where `desc` = "sms_pwd"';
$sms_pwd = mysqli_fetch_array(execute_query($sql));
$sms_pwd = $sms_pwd['rate'];

$sql = 'select * from general_settings where `desc` = "sms_senderid"';
$senderid = mysqli_fetch_array(execute_query($sql));
$senderid = $senderid['rate'];

$sql = 'select * from general_settings where `desc` = "sms_peid"';
$peID = mysqli_fetch_array(execute_query($sql));
$peID = $peID['rate'];

function send_sms($number,$get_msg, $template_id, $pe_id, $hindi=''){
	$get_msg = urlencode($get_msg);
	$ch = curl_init();
	global $sms_user;
	global $sms_pwd;
	global $senderid;
	$no=trim($number);
	$route = 22;
	$url = "http://sms.weknowtech.in/pushsms.php?username=$sms_user&api_password=$sms_pwd&sender=$senderid&to=$no&message=$get_msg&priority=11&jsonapi=1&e_id=$pe_id&t_id=$template_id";
	//$param = "user=$sms_user&password=$sms_pwd&senderid=$senderID&channel=Trans&DCS=0&flashsms=0&number=$no&text=$get_msg&route=$route$hindi";
	//$url = $url.$param;
	//echo $url;
	curl_setopt($ch, CURLOPT_URL,  $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$buffer = curl_exec($ch);
	if(empty($buffer)){
	   return $buffer;
	}
	else{
		$sql = 'insert into sms_buffer_dump (SenderId, message, sendondate, api_url, pe_id, template_message, template_id) values("'.$senderid.'", "'.addslashes($buffer).'", "'.date("Y-m-d H:i:s").'", "'.addslashes($url).'", "'.$pe_id.'", "'.$get_msg.'", "'.$template_id.'")';
		//echo $sql;
		execute_query($sql);
		$res = json_decode($buffer, true);
		//print_r($res);
		$row = array();
		$comma=0;
		$i=1;
		$tot_credit = 0;
		$res = $res['description'];
		$dlr_seq = $res['trackid'];
		$sql = 'insert into sms_report (msg_id, SenderId, billcredit, message, sendondate, originalnumber, textMessage, dlr_seq) value ';
		foreach($res['msg'] as $k=>$v){
			//$tot_credit += $res['billcredit'];
			if($comma==0){
				$comma=1;
				$sql .= '("'.$v['msgid'].'", "'.$senderid.'", "", "'.$get_msg.'", "'.date("Y-m-d H:i:s").'", "'.$v['receiver'].'", "'.$get_msg.'", "'.$dlr_seq.'")';
			}
			else{
				$sql .= ', ("'.$v['msgid'].'", "'.$senderid.'", "", "'.$get_msg.'", "'.date("Y-m-d H:i:s").'", "'.$v['receiver'].'", "'.$get_msg.'", "'.$dlr_seq.'")';

			}
			if($i%100==0){
				execute_query($sql);
				//echo 'Count : '.$i.' >> '.$sql.'<br><br>';
				$sql = 'insert into sms_report (msg_id, SenderId, billcredit, message, sendondate, originalnumber, textMessage, dlr_seq) value ';
				$i++;
				$comma=0;
			}
			else{
				$i++;
			}
		}
		execute_query($sql);
	   	return $buffer;
	}
}

function sms_delivery($msgid){
	global $sms_user;
	global $sms_pwd;
	$ch = curl_init();
	$url = "http://smsw.co.in/API/WebSMS/Http/v1.0a/index.php?method=show_dlr&username=$sms_user&password=$sms_pwd&msg_id=$msgid&format=json";
	//echo $url.'<br><br>';
	curl_setopt($ch, CURLOPT_URL,  $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$buffer = curl_exec($ch);
	if(empty($buffer)){
	   return $buffer;
	}
	else{
	   return $buffer;
	}
}

?>
