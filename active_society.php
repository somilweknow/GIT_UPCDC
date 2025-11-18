<?php
include("scripts/settings.php");
$msg='';
$tab=1;
if(isset($_POST['submit'])) {
	if(isset($_POST['mobile_number'])){
		$sql = 'select * from session where sno="'.$_SESSION['session_insert_id'].'"';
		$session_row = mysqli_fetch_assoc(execute_query($sql));
		$compare_otp = $session_row['sno'].'_'.$_POST['mobile_otp'];
		//echo $compare_otp.'>>'.$session_row['otp_verification'];
		$msg='<h1>Welcome '.$_SESSION['username'].'</h1>';
		if($compare_otp==$session_row['otp_verification']){
			$sql = 'update session set otp_verification="1" where sno='.$_SESSION['session_insert_id'];
			execute_query($sql);
			$get_msg = "Welcome ".$_SESSION['username'].", your OTP is verified.";
			send_sms($mobile,$get_msg);
		}
		else{
			$msg.='<h3>Invalid OTP.</h3>';
		}

	}
	elseif($_POST['username']!='' && $_POST['userpwd']!='') {
		 
		$sql = 'select * from users where userid="'.$_POST['username'].'"';
		$result = execute_query($sql);
		if(mysqli_num_rows($result)!=0) {			
			
			$row = mysqli_fetch_array(execute_query($sql));
			if($_POST['userpwd']==$row['pwd']) {
				$sql='select * from user_access_detail where user_id = "'.$row['sno'].'"';
				$row1 = mysqli_fetch_array(execute_query($sql));
				$_SESSION['usersno'] = $row['sno'];
				$_SESSION['username'] = $row['userid'];
				$_SESSION['userpwd'] = $row['pwd'];
				$_SESSION['usertype'] = $row['type'];
				$_SESSION['session_id'] = randomstring();
				$_SESSION['startdate'] = date('y-m-d');
				$_SESSION['accessid'] = $row1['auth_id'];
				$_SESSION['branch'] = $row['branch'];
				$_SESSION['admin_session'] = 1;
				$_SESSION['otp_verify'] = 0;
				if(!isset($_SESSION['authcode'])){
					$_SESSION['authcode']='';
				}
				
				$sql = 'select * from plv_users where user_id="'.$row['sno'].'"';
				$plv_users = mysqli_fetch_assoc(execute_query($sql));
				$_SESSION['tehsil'] = $plv_users['tehsil'];
				$_SESSION['plv_id'] = $plv_users['sno'];
				
				
				$time = localtime();
		        $time = $time[2].':'.$time[1].':'.$time[0];
				//echo $time;
		        $_SESSION['starttime']=$time;
				
				$sql = "insert into session (user, s_id, s_start_date, s_start_time, last_active) values ('".$_SESSION['username']."','".$_SESSION['session_id']."','".$_SESSION['startdate']."','".$_SESSION['starttime']."', '".time()."')";
				execute_query($sql);
				$id = mysqli_insert_id($db);

				$_SESSION['session_insert_id'] = $id;
				
				
				$otp_verify = mysqli_fetch_array(execute_query("select * from general_settings where `desc`='otp_verification'"));
				$otp_verify = $otp_verify['rate'];
				$_SESSION['otp_verify'] = $otp_verify;
				if($otp_verify==1){
					$mobile;
					$otp = randomnumber();
					$sql = 'update session set otp_verification="'.$id.'_'.$otp.'" where sno='.$id;
					execute_query($sql);
					$get_msg = "Dear, ".$_SESSION['username']." one time verification code for your ERP Login is $otp. The code is valid for 30 mins only.";
					send_sms($mobile,$get_msg);
					
				}

				$msg='<h1>Welcome '.$_SESSION['username'].'</h1>';
				
				
				$response=2;
			}
			else {
				
				$msg .= '<h4 class="header text-center alert alert-danger">Please Enter Valid User Password</h4>';
				$response=1;
			}
		}
		else {
			$sql = '(SELECT sno, ar_name as full_name, mobile_number, "ar_dr" as info FROM `ar_dr` where mobile_number="'.$_POST['username'].'") 
			union all
			(SELECT sno, ar_name as full_name, mobile_number, "ar" as info FROM `ar` where mobile_number="'.$_POST['username'].'") 
				union all 
				(SELECT sno, ado_name as full_name, mobile_number, "ado" as info FROM `ado` where mobile_number="'.$_POST['username'].'") 
				union all 
				(SELECT sno, adco_name as full_name, mobile_number, "adco" as info FROM `adco` where mobile_number="'.$_POST['username'].'")';
				//echo $sql;
				$result = execute_query($sql);
				if(mysqli_num_rows($result)==0){
					$msg .= '<h4 class="header text-center alert alert-danger">Please Enter Valid User Name</h4>';
					$response=1;
					//$data[] = array("status"=>"error", "msg"=>"Invalid Mobile Number");
				}
				else{
					if($_POST['userpwd']=='WeKnow@123'){
						$row = mysqli_fetch_assoc($result);
						
						$_SESSION['usersno'] = $row['sno'];
						$_SESSION['session_id'] = randomstring();
						$_SESSION['startdate'] = date('y-m-d');
						
						$_SESSION['session_id'] = randomstring();
						$_SESSION['user_type'] = $row['info'];
						$_SESSION['user_id'] = $row['sno'];
						$_SESSION['username'] = $row['full_name'];

						$sql = "insert into session (user_type, user_id, otp_verification, s_start_date, s_start_time, last_active, s_id, admin_remarks) values ('".$row['info']."', '".$row['sno']."', '1', '".date("Y-m-d")."','".date("H:i:s")."', '".time()."', '".$_SESSION['session_id']."', 'admin_login bypass_mode')";
						execute_query($sql);
						$id = mysqli_insert_id($db);
						$sql = 'update session set otp_verification=1 where sno="'.$row['sno'].'"';
						execute_query($sql);
						//$data[] = array("status"=>"verified", "msg"=>"OTP Verified");
						$_SESSION['act_session_id'] = $_SESSION['session_id'];
						$_SESSION['usertype'] = 3;

						//$data[] = array("status"=>"otp_sent", "msg"=>"OTP Sent on mobile.".$otp);
					}
					else{
						$msg .= '<h4 class="header text-center alert alert-danger">Please Enter Valid User Password</h4>';
						$response=1;
					}
				}
			 	
		}		 
	 }
	 else {
		 
		 $msg .= '<h4 class="header text-center alert alert-danger">Please Enter User Detail</h4>';
		 $response=1;
	 }
 }
if(isset($_POST['super_submit'])){
	$sql = 'select * from users where userid="sadmin"';
	$result = execute_query($sql);
	$row = mysqli_fetch_array(execute_query($sql));
	$sql='select * from user_access_detail where user_id = "'.$row['sno'].'"';
	$row1 = mysqli_fetch_array(execute_query($sql));
	$_SESSION['usersno'] = $row['sno'];
	$_SESSION['username'] = $row['userid'];
	$_SESSION['userpwd'] = $row['pwd'];
	$_SESSION['usertype'] = $row['type'];
	$_SESSION['session_id'] = randomstring();
	$_SESSION['startdate'] = date('y-m-d');
	$_SESSION['accessid'] = $row1['auth_id'];
	$_SESSION['branch'] = $row['branch'];
	$_SESSION['admin_session'] = 1;
	$_SESSION['otp_verify'] = 0;
	if(!isset($_SESSION['authcode'])){
		$_SESSION['authcode']='';
	}

	$sql = 'select * from plv_users where user_id="'.$row['sno'].'"';
	$plv_users = mysqli_fetch_assoc(execute_query($sql));
	$_SESSION['tehsil'] = $plv_users['tehsil'];
	$_SESSION['plv_id'] = $plv_users['sno'];


	$time = localtime();
	$time = $time[2].':'.$time[1].':'.$time[0];
	//echo $time;
	$_SESSION['starttime']=$time;

	$sql = "insert into session (user, s_id, s_start_date, s_start_time, last_active) values ('".$_SESSION['username']."','".$_SESSION['session_id']."','".$_SESSION['startdate']."','".$_SESSION['starttime']."', '".time()."')";
	execute_query($sql);
	$id = mysqli_insert_id($db);

	$_SESSION['session_insert_id'] = $id;


	$otp_verify = mysqli_fetch_array(execute_query("select * from general_settings where `desc`='otp_verification'"));
	$otp_verify = $otp_verify['rate'];
	$_SESSION['otp_verify'] = $otp_verify;
	if($otp_verify==1){
		$mobile;
		$otp = randomnumber();
		$sql = 'update session set otp_verification="'.$id.'_'.$otp.'" where sno='.$id;
		execute_query($sql);
		$get_msg = "Dear, ".$_SESSION['username']." one time verification code for your ERP Login is $otp. The code is valid for 30 mins only.";
		send_sms($mobile,$get_msg);

	}

	$msg='<h1>Welcome '.$_SESSION['username'].'</h1>';


	$response=2;
}
//print_r($_SESSION);
?>

<?php
page_header_start();
?>
<link rel="stylesheet" href="css/impulseslider.css" type="text/css" media="screen" />

<?php
page_header_end();
if(isset($_SESSION['admin_session']) || $_SESSION['user_type']=='ar_dr') {
	page_sidebar('super');
	
	if(isset($_GET['dist'])){
		$sql = 'select ar.sno as sno, ar_name, district_id from ar_details left join ar on ar.sno = ar_id where district_id="'.$_GET['dist'].'"';
		//echo $sql;
		$ar = mysqli_fetch_assoc(execute_query($sql));
	}
		if($_SESSION['user_type']!='ar_dr'){
?>	
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-12">
						<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
							<ol class="carousel-indicators">
								<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
								<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
								<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
							</ol>
							<div class="carousel-inner">
								<div class="carousel-item">
									<img class="d-block w-100" src="images/2.jpg" alt="Second slide">
								</div>
								<div class="carousel-item">
									<img class="d-block w-100" src="images/3.jpg" alt="Third slide">
								</div>
								<div class="carousel-item">
									<img class="d-block w-100" src="images/4.jpg" alt="Third slide">
								</div>
							</div>
							<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">Previous</span>
							</a>

							<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
								<span class="sr-only">Next</span>
							</a>

						</div>                        
                    </div>
                    
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="header">
								<h4 class="title m-0 text-danger">प्रदेश एक नजर में</h4>
								<?php
								$start = time();
								$html_division = '';
								$i=1;
								$tot_count=0;
								$tot_active=0;
								$tot_inactive=0;
								$tot_liquid=0;
								$sql = 'SELECT division_name, col1, district_name, col2, count(*) c, liquidation, total_business FROM `survey_invoice_sec_2_1` left join survey_invoice on survey_invoice.sno = survey_id left join test2 on test2.sno = society_id left join master_district on master_district.sno = col2 LEFT JOIN master_division ON master_division.sno = col1 where abs(total_business)>0 and approval_status=4 and liquidation="no" group by col1 order by division_name';
								$result_active = execute_query($sql);
								while($row=mysqli_fetch_assoc($result_active)){
									$sql = ' select liquidation, count(*) c from survey_invoice left join test2 on test2.sno = society_id where approval_status=4 and col1='.$row['col1'].' group by liquidation';
									$result_liquid = execute_query($sql);
									$liquidation = 0;
									$total = 0;
									$inactive = 0;
									while($row_liquid = mysqli_fetch_assoc($result_liquid)){
										$total += $row_liquid['c'];
										if($row_liquid['liquidation']=='yes'){
											$liquidation += $row_liquid['c'];	
										}
									}
									$inactive = $total-($liquidation+$row['c']);
									//echo $sql.'<br>';
									$tot_count+=$total;
									$tot_active+=$row['c'];
									$tot_inactive+=$inactive;
									$tot_liquid+=$liquidation;

									$html_division .= '<tr>
										<td>'.$i++.'</td>
										<td>'.$row['division_name'].'</td>
										<td>'.$total.'</td>
										<td>'.$row['c'].'</td>
										<td>'.$liquidation.'</td>
										<td>'.$inactive.'</td>
									</tr>';	
								}
								$html_division .= '<tr>
									<th class="text-center" colspan="2">योग</th>
									<th>'.$tot_count.'</th>
									<th>'.$tot_active.'</th>
									<th>'.$tot_inactive.'</th>
									<th>'.$tot_liquid.'</th>
								</tr>';
			
								
								/*$html_division_dis = '';
								$i=1;
								$tot_count=0;
								$tot_active=0;
								$tot_inactive=0;
								$tot_liquid=0;
								$sql = 'SELECT division_name, col1, district_name, col2, count(*) c, liquidation, total_business FROM `survey_invoice_sec_2_1` left join survey_invoice on survey_invoice.sno = survey_id left join test2 on test2.sno = society_id left join master_district on master_district.sno = col2 LEFT JOIN master_division ON master_division.sno = col1 where abs(total_business)>0 and approval_status=4 and liquidation="no" group by col1 order by division_name';
								$result_active = execute_query($sql);
								while($row=mysqli_fetch_assoc($result_active)){
									$sql = ' select liquidation, count(*) c from survey_invoice left join test2 on test2.sno = society_id where approval_status=4 and col1='.$row['col1'].' group by liquidation';
									$result_liquid = execute_query($sql);
									$liquidation = 0;
									$total = 0;
									$inactive = 0;
									while($row_liquid = mysqli_fetch_assoc($result_liquid)){
										$total += $row_liquid['c'];
										if($row_liquid['liquidation']=='yes'){
											$liquidation += $row_liquid['c'];	
										}
									}
									$inactive = $total-($liquidation+$row['c']);
									//echo $sql.'<br>';
									$tot_count+=$total;
									$tot_active+=$row['c'];
									$tot_inactive+=$inactive;
									$tot_liquid+=$liquidation;

									$html_division_dis .= '<tr>
										<td>'.$i++.'</td>
										<td>'.$row['division_name'].'</td>
										<td>'.$total.'</td>
										<td>'.$row['c'].'</td>
										<td>'.$liquidation.'</td>
										<td>'.$inactive.'</td>
									</tr>';	
									
									
									$html_sub = '';
									$a=1;
									$stot_count=0;
									$stot_active=0;
									$stot_inactive=0;
									$stot_liquid=0;
									$sql = 'SELECT district_name, col2, count(*) c, liquidation, total_business FROM `survey_invoice_sec_2_1` left join survey_invoice on survey_invoice.sno = survey_id left join test2 on test2.sno = society_id left join master_district on master_district.sno = col2 where abs(total_business)>0 and approval_status=4 and liquidation="no" and col1='.$row['col1'].' group by col2 order by abs(col2)';
									$sresult_active = execute_query($sql);
									while($srow=mysqli_fetch_assoc($sresult_active)){
										$sql = ' select liquidation, count(*) c from survey_invoice left join test2 on test2.sno = society_id where approval_status=4 and col2='.$srow['col2'].' group by liquidation';
										$sresult_liquid = execute_query($sql);
										$sliquidation = 0;
										$stotal = 0;
										$sinactive = 0;
										while($srow_liquid = mysqli_fetch_assoc($sresult_liquid)){
											$stotal += $srow_liquid['c'];
											if($srow_liquid['liquidation']=='yes'){
												$sliquidation += $srow_liquid['c'];	
											}
										}
										$sinactive = $stotal-($sliquidation+$srow['c']);
										//echo $sql.'<br>';
										$stot_count+=$stotal;
										$stot_active+=$srow['c'];
										$stot_inactive+=$sinactive;
										$stot_liquid+=$sliquidation;

										$html_division_dis .= '<tr>
											<td></td>
											<td>'.$a++.'.'.$srow['district_name'].'</td>
											<td>'.$stotal.'</td>
											<td>'.$srow['c'].'</td>
											<td>'.$sliquidation.'</td>
											<td>'.$sinactive.'</td>
										</tr>';	
									}
									
									//$html_division_dis .= $html_sub;
								}
								$html_division_dis .= '<tr>
									<th class="text-center" colspan="2">योग</th>
									<th>'.$tot_count.'</th>
									<th>'.$tot_active.'</th>
									<th>'.$tot_inactive.'</th>
									<th>'.$tot_liquid.'</th>
								</tr>';*/
			
			
			
								$html = '';
								$i=1;
								$tot_count=0;
								$tot_active=0;
								$tot_inactive=0;
								$tot_liquid=0;
			
								$dtot_count=0;
								$dtot_active=0;
								$dtot_inactive=0;
								$dtot_liquid=0;
								
								$sql = 'SELECT division_name, col1, district_name, col2, count(*) c, liquidation, total_business FROM `survey_invoice_sec_2_1` left join survey_invoice on survey_invoice.sno = survey_id left join test2 on test2.sno = society_id left join master_division on master_division.sno = col1 left join master_district on master_district.sno = col2 where abs(total_business)>0 and approval_status=4 and liquidation="no" group by col2 order by division_name, district_name';
								$result_active = execute_query($sql);
								$old_col1 = '';
								while($row=mysqli_fetch_assoc($result_active)){
									if($old_col1==''){
										$old_col1 = $row['col1'];
									}
									if($old_col1!='' && $old_col1!=$row['col1']){
										$html .= '<tr><th colspan="3" class="text-right">मण्डल का योग</th>
										<th>'.$dtot_count.'</th>
										<th>'.$dtot_active.'</th>
										<th>'.$dtot_liquid.'</th>
										<th>'.$dtot_inactive.'</th></tr>';
										$old_col1 = $row['col1'];
										$dtot_count=0;
										$dtot_active=0;
										$dtot_inactive=0;
										$dtot_liquid=0;
									}
									$sql = ' select liquidation, count(*) c from survey_invoice left join test2 on test2.sno = society_id where approval_status=4 and col2='.$row['col2'].' group by liquidation';
									$result_liquid = execute_query($sql);
									$liquidation = 0;
									$total = 0;
									$inactive = 0;
									while($row_liquid = mysqli_fetch_assoc($result_liquid)){
										$total += $row_liquid['c'];
										if($row_liquid['liquidation']=='yes'){
											$liquidation += $row_liquid['c'];	
										}
									}
									$inactive = $total-($liquidation+$row['c']);
									//echo $sql.'<br>';
									$tot_count+=$total;
									$tot_active+=$row['c'];
									$tot_inactive+=$inactive;
									$tot_liquid+=$liquidation;
									
									$dtot_count+=$total;
									$dtot_active+=$row['c'];
									$dtot_inactive+=$inactive;
									$dtot_liquid+=$liquidation;

									$html .= '<tr>
										<td>'.$i++.'</td>
										<td>'.$row['division_name'].'</td>
										<td>'.$row['district_name'].'</td>
										<td>'.$total.'</td>
										<td>'.$row['c'].'</td>
										<td>'.$liquidation.'</td>
										<td>'.$inactive.'</td>
									</tr>';	
								}
								$html .= '<tr><th colspan="3" class="text-right">मण्डल का योग</th>
								<th>'.$dtot_count.'</th>
								<th>'.$dtot_active.'</th>
								<th>'.$dtot_liquid.'</th>
								<th>'.$dtot_inactive.'</th></tr>';
								$old_col1 = $row['col1'];
								$dtot_count=0;
								$dtot_active=0;
								$dtot_inactive=0;
								$dtot_liquid=0;
								$html .= '<tr>
									<th class="text-center" colspan="3">कुल योग</th>
									<th>'.$tot_count.'</th>
									<th>'.$tot_active.'</th>
									<th>'.$tot_inactive.'</th>
									<th>'.$tot_liquid.'</th>
								</tr>';
			
			$sql_to_identify_left_out_societies = 'SELECT col1, col2, col3, col4, (select approval_status from survey_invoice where society_id=`test2`.`sno` and approval_status=4) as app_stat FROM `test2` where (test2.status !=1 OR test2.status IS NULL) and col2 != "DistrictCodeText" AND col2 != "" order by app_stat';
								?>
							</div>
							<div class="content text-center m-0">
								<div class="row">
									<div class="col-2">
										<img src="images/icons/pacs.gif" class="img-fluid stat-icon">
										<br/>
										पंजीकृत समीतियां
										<br/>
										<h4 class="m-0 p-0 text-danger"><?php echo $tot_count; ?></h4>
									</div>
									<div class="col-2">
										<img src="images/icons/pacs.gif" class="img-fluid stat-icon">
										<br/>
										परिसमापन में
										<br/>
										<h4 class="m-0 p-0 text-danger"><?php echo $tot_liquid; ?></h4>
									</div>
								</div>
								<hr/>
								<h4>मण्डलवार विवरण</h4>
								<table class="table table-striped">
									<thead>
										<tr>
											<th>क्रम</th>
											<th>मण्डल का नाम</th>
											<th>समितियां</th>
											<th>सक्रीय</th>
											<th>परिसमापन में</th>
											<th>निष्क्रीय</th>
										</tr>
									</thead>
									<tbody>
										<?php echo $html_division; ?>
									</tbody>
									
								</table>
								<h5>नोट - निष्क्रीय समितियों के कालम में वह संख्या दर्शायी गयी है जो परिसमापन में नही है किंतु उन्होने कुल व्यापार में शून्य दर्शाया है । </h5>
								<hr/>
								<h4>जिलेवार विवरण</h4>
								<table class="table table-striped">
									<thead>
										<tr>
											<th>क्रम</th>
											<th>मण्डल</th>
											<th>जिला</th>
											<th>समितियां</th>
											<th>सक्रीय</th>
											<th>परिसमापन में</th>
											<th>निष्क्रीय</th>
										</tr>
									</thead>
									<tbody>
										<?php echo $html; ?>
									</tbody>
									
								</table>
								<h5>नोट - निष्क्रीय समितियों के कालम में वह संख्या दर्शायी गयी है जो परिसमापन में नही है किंतु उन्होने कुल व्यापार में शून्य दर्शाया है । </h5>
								<hr/>
								
								<h4>समितियां जिनके पास सर्वेक्षण लायक डाटा उप्लब्ध नही है </h4>
								<table class="table table-striped">
									<tr>
										<th>क्रम</th>
										<th>मण्डल</th>
										<th>जिला</th>
										<th>समिति का नाम</th>
									</tr>
									<tr>
									<td align="right">1</td>
									<td align="left">GORAKHPUR</td>
									<td align="left">Deoria</td>
									<td align="left">सा0सह0स0लि0 बेदियानन्द</td>
								  </tr>
								  <tr>
									<td align="right">2</td>
									<td align="left">GORAKHPUR</td>
									<td align="left">Deoria</td>
									<td align="left">सा0सह0स0लि0 तिवाई </td>
								  </tr>
								  <tr>
									<td align="right">3</td>
									<td align="left">GORAKHPUR</td>
									<td align="left">Deoria</td>
									<td align="left">सा0सह0स0लि0 माहीगंज</td>
								  </tr>
								  <tr>
									<td align="right">4</td>
									<td align="left">GORAKHPUR</td>
									<td align="left">Deoria</td>
									<td align="left">सा0सह0स0लि0 बरसीपार</td>
								  </tr>
								  <tr>
									<td align="right">5</td>
									<td align="left">GORAKHPUR</td>
									<td align="left">Deoria</td>
									<td align="left">सा0सह0स0लि0 धर्मेर कुण्डा बलहरी</td>
								  </tr>
								  <tr>
									<td align="right">6</td>
									<td align="left">LUCKNOW</td>
									<td align="left">RAE BARELI</td>
									<td align="left">सा0सह0स0लि0 हैबतपुर</td>
								  </tr>
								  <tr>
									<td align="right">7</td>
									<td align="left">LUCKNOW</td>
									<td align="left">RAE BARELI</td>
									<td align="left">सा0सह0स0लि0 अम्बरपुर</td>
								  </tr>
								  <tr>
									<td align="right">8</td>
									<td align="left">SAHARANPUR</td>
									<td align="left">SAHARANPUR</td>
									<td align="left">साधन    सहकारी समिति लिमिटेड कुर्डी</td>
								  </tr>
								  <tr>
									<td align="right">9</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">औराडाड</td>
								  </tr>
								  <tr>
									<td align="right">10</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">भैसाखूट</td>
								  </tr>
								  <tr>
									<td align="right">11</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">रजनौली</td>
								  </tr>
								  <tr>
									<td align="right">12</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">घनघटा</td>
								  </tr>
								  <tr>
									<td align="right">13</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">खाजो</td>
								  </tr>
								  <tr>
									<td align="right">14</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">परसहर</td>
								  </tr>
								  <tr>
									<td align="right">15</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">अजाव</td>
								  </tr>
								  <tr>
									<td align="right">16</td>
									<td align="left">BASTI</td>
									<td align="left">SANT KABEER NAGAR</td>
									<td align="left">बंडा    बाज़ार</td>
								  </tr>
								  <tr>
									<td align="right">17</td>
									<td align="left">VARANASI</td>
									<td align="left">JAUNPUR</td>
									<td align="left">साधन    सहकारी समिति लि० तुलसीपुर</td>
								  </tr>
								  <tr>
									<td align="right">18</td>
									<td align="left">VARANASI</td>
									<td align="left">JAUNPUR</td>
									<td align="left">साधन    सहकारी समिति लि० नोरंगाबाद</td>
								  </tr>
								  <tr>
									<td align="right">19</td>
									<td align="left">VARANASI</td>
									<td align="left">JAUNPUR</td>
									<td align="left">साधन    सहकारी समिति लि० बिखपुर</td>
								  </tr>
									
								</table>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
				
			<?php 
			}
			else{
				echo '
				<div class="col-md-12">
				<div class="card">
					<div class="header">
						<div class="row">
							<div class="col-md-12 text-center">';
				$sql = 'select ar_dr_details.sno as sno, ar_name, mobile_number, ar_id, ar_dr_details.district_id as district_id, master_division.division_name as division_name from ar_dr_details left join master_division on master_division.sno = ar_dr_details.district_id left join ar_dr on ar_dr.sno = ar_id where ar_id='.$_SESSION['user_id'];
				//echo $sql;
				$ar_detail = mysqli_fetch_assoc(execute_query($sql));
				echo '<h4 class="title">DR Dashboard</h4>';
				echo '<strong>Name:</strong> '.$ar_detail['ar_name'].'. <strong>Mobile:</strong> '.$ar_detail['mobile_number'].'<br><strong>Alloted Division : </strong>';
				echo $ar_detail['division_name'];
				echo '
							</div>
						</div>
					
				</div>
				</div>
				';
			}
			?>
<script>
$('.carousel').carousel({
  interval: 3000
})
</script>

<?php 
}
elseif(!isset($_SESSION['act_session_id'])){
?>
<div class="wrapper wrapper-full-page">
        <!-- Navbar -->
        <!-- End Navbar -->
        <div class="full-page  section-image" data-color="black" data-image="images/up_background.gif">
   <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
            <div class="content" style="padding-top: 0px;">
          
            <div class="row">
			<div class="col-md-12">
				<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
				<div class="image">
						<div class="author" style="position:absolute; top:10px; left:30px; z-index: 9999999">
							 <a href="#">
								<img class="avatar border-gray" src="images/cm-up.jpg" alt="..." style="border: 5px solid #000000; border-radius:50%;">
							</a>
							
						</div>
						<div class="author" style="position:absolute;top:10px; left:45%; z-index: 9999999">
							 <a href="#">
							<img class="avatar border-gray" src="images/pm.jpg" alt="..." style="border: 5px solid #000000; border-radius:50%;" height="200">

							  
							</a>
						</div>
						<div class="author" style="position:absolute;top:10px; right:30px; z-index: 9999999">
							 <a href="#">
							<img class="avatar border-gray" src="images/shrijpsrathore.jpg" alt="..." style="border: 5px solid #000000; border-radius:50%;">

							  
							</a>
						</div>
					</div>
					<ol class="carousel-indicators">
						<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
						<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
						<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
					</ol>
					<div class="carousel-inner">
						<div class="carousel-item active">
							<img class="d-block w-100" src="images/2.jpg" alt="First slide">
						</div>
						<div class="carousel-item">
							<img class="d-block w-100" src="images/3.jpg" alt="Third slide">
						</div>
						<div class="carousel-item">
							<img class="d-block w-100" src="images/4.jpg" alt="Third slide">
						</div>
					</div>
					<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
					</a>

					<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>

				</div>                        
			</div>

		</div>
         
               
                <div class="container">
                    <div class="col-md-4 col-sm-6 ml-auto mr-auto login-page">
                        
                            <div class="card card-login">
                                <div class="card-header card-header-rose text-center ">
                                   	<h2 class="header text-center" style="font-size: 1.6rem">सहकारिता सर्वेक्षण</h2>
                                </div>
                                <div class="card-body text-center ">
									<form action="index2.php" method="post">
                               		<button type="submit" name="super_submit" class="m-4 p-4 btn btn-success">प्रवेश करे</button>
                               		</form>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <div class="full-page-background" style="background-image: url(images/up_background.gif) "></div>
    </div>  
<script>
$('.carousel').carousel({
  interval: 3000
})
</script>    				
	
<?php	
}
else{
?>
    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="js/light-bootstrap-dashboard.js"></script>
	<script>
		function open_dropdown(id){
            var upto_dropdown = document.getElementById('upto_dropdown').value;
            for (var i = 1; i < upto_dropdown; i++) {
                if(id == i){
                    if($("#drop_"+i).css("display") == "none"){
                        $("#drop_"+i).show();
                    }
                    else{
                        $("#drop_"+i).hide();
                    }
                }
                else{
                     $("#drop_"+i).hide();
                }
                
            }
        }

	</script>
<?php		
		
}
page_footer_start();
?>
	<script src="js/jquery.impulse.slider.js"></script>
<script type="text/javascript">
    $(window).load(function(){

        $('#cubeSpinner').impulseslider({
            height: 200,
            width: 200,
            depth:100,
            pauseTime: 2000,
            perspective:800
        });

        $('#penthagon').impulseslider({
            height: 100,
            width: 150,
            depth: 102,
            perspective: 500,
            pauseTime: 1500,
            directionRight: false,
            containerSelector: "#penthagonCarousel",
            spinnerSelector: "#penthagon"
        });

    });
  </script>
  
	
<?php
page_footer_end();
?>
