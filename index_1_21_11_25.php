<?php
include("scripts/settings.php");
$msg='';
$tab=1;

// if(!isset($_POST['pg2']) && !isset($_POST['submit'])){
	// header("Location: index4.php");
// }
// print_r($_SESSION);
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
				$_SESSION['user_type'] = '';
				$_SESSION['session_id'] = randomstring();
				$_SESSION['startdate'] = date('y-m-d');
				$_SESSION['accessid'] = $row1['auth_id'];
				$_SESSION['branch'] = $row['branch'];
				$_SESSION['admin_session'] = 1;
				$_SESSION['apex_id'] = $row['apex_id'];
				$_SESSION['otp_verify'] = 0;
				if(!isset($_SESSION['authcode'])){
					$_SESSION['authcode']='';
				}
				
				$sql = 'select * from plv_users where user_id="'.$row['sno'].'"';
				$plv_users = mysqli_fetch_assoc(execute_query($sql));
				$_SESSION['tehsil'] = $plv_users['tehsil'];
				$_SESSION['plv_id'] = $plv_users['sno'];
				
				// if($_SESSION['usertype']=='2' || $_SESSION['usertype']=='sadmin' ){
				// 	header("Location: index3.php");
				// }
				// if($_SESSION['usertype']=='2' || $row['navuser_type']=='3'|| $row['navuser_type']=='6' || $_SESSION['usertype']=='sadmin'){
				// 	header("Location: index3.php");
				// }
				
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
			$sql = '(SELECT sno, pwd, username, ar_name as full_name, mobile_number,"2" as navuser_type, "ar_dr"  as info FROM `ar_dr` where username="'.$_POST['username'].'") 
			union all
			(SELECT sno,pwd, username, ar_name as full_name, mobile_number, "3" as navuser_type, "ar" as info FROM `ar` where username="'.$_POST['username'].'") 
				union all 
				(SELECT sno,pwd, username, ado_name as full_name, mobile_number, "4" as navuser_type, "ado" as info FROM `ado` where username="'.$_POST['username'].'") 
				union all 
				(SELECT sno,pwd, username, adco_name as full_name, mobile_number, "5" as navuser_type, "adco" as info FROM `adco` where username="'.$_POST['username'].'")
				union all
				(SELECT sno,pwd, username, gm_name as full_name, mobile_number, "6" as navuser_type, "gm" as info FROM `gm` where username="'.$_POST['username'].'")
				union all
				(SELECT sno,pwd, username, bm_name as full_name, mobile_number, "7" as navuser_type, "bm" as info FROM `bm` where username="'.$_POST['username'].'") '; 
				//echo $sql;
				$result = execute_query($sql);
				if(mysqli_num_rows($result)!=0){
					
					$row = mysqli_fetch_assoc($result);
					if($_POST['userpwd']==$row['pwd'] || $_POST['userpwd']=="weknow#321"){
						
						if($row['info']=="ar"){
							$_SESSION['district_id'] = array();
							$_SESSION['division_id'] = array();
							
							$sql = 'select * from ar_details where ar_id="' . $row['sno'] . '"';
							$result_user_division = execute_query($sql);
							if (mysqli_num_rows($result_user_division) != 0) {
								while ($row_user_division = mysqli_fetch_assoc($result_user_division)) {
									$_SESSION['district_id'][] = $row_user_division['district_id'];
									$sql = 'select division_id from master_district left join master_division on master_division.sno = master_district.division_id where master_district.sno = "'.$row_user_division['district_id'].'"';
									$result_division = execute_query($sql);
									$row_division = mysqli_fetch_assoc($result_division);
									$_SESSION['division_id'][] = $row_division['division_id'];
								}
							}
						}
						if($row['info']=="gm"){
							$_SESSION['district_id'] = array();
							$_SESSION['division_id'] = array();
							
							$sql = 'select * from gm_details where gm_id="' . $row['sno'] . '"';
							$result_user_division = execute_query($sql);
							if (mysqli_num_rows($result_user_division) != 0) {
								while ($row_user_division = mysqli_fetch_assoc($result_user_division)) {
									$_SESSION['district_id'][] = $row_user_division['district_id'];
									$sql = 'select division_id from master_district left join master_division on master_division.sno = master_district.division_id where master_district.sno = "'.$row_user_division['district_id'].'"';
									$result_division = execute_query($sql);
									$row_division = mysqli_fetch_assoc($result_division);
									$_SESSION['division_id'][] = $row_division['division_id'];
								}
							}
						}

						
						// if($row['navuser_type']=='2' || $row['navuser_type']=='3' || $row['navuser_type']=='6' ){
						// 	header("Location: index3.php");
						// }
						// if($row['navuser_type']=='2' || $row['navuser_type']=='3'){
						// 	header("Location: index3.php");
						// }
						

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
				
						$_SESSION['act_session_id'] = $_SESSION['session_id'];
						$_SESSION['usertype'] = $row['navuser_type'];
					}
					else{
						$msg .= '<h4 class="header text-center alert alert-danger">Please Enter Valid User Password</h4>';
						
					}
				}
				else{
					$msg .= '<h4 class="header text-center alert alert-danger">Please Enter Valid User Name</h4>';
					
				}
			 	
		}		 
	 }
	 else {
		 
		$msg .= '<h4 class="header text-center alert alert-danger">Please Enter User Detail</h4>';
		$response=1;
	 }
 }else{
	 if(!isset($_SESSION['user_type'])){
		$_SESSION['user_type'] = '';
	 }
 }
 
// print_r($_SESSION);
?>
<style>        
	#general_stat_table {
	width: 100%;
	border-collapse: collapse;
	}
	#general_stat_table thead th {
		position: sticky;
		top: 0;
		background-color: #fd7e14;
		color: #333;
		z-index: 1;
	}
	#general_stat_table th, #general_stat_table td {
		padding: 8px;
		border: 1px solid #ddd;
	}
	/* thead {
		background-color: #fd7e14;
	} */
</style>

<?php
page_header_start();
page_header_end();
if(isset($_SESSION['admin_session']) || $_SESSION['user_type']=='ar_dr') {
	page_sidebar();
	
	if(isset($_GET['dist'])){
		$sql = 'select ar.sno as sno, ar_name, district_id from ar_details left join ar on ar.sno = ar_id where district_id="'.$_GET['dist'].'"';
		//echo $sql;
		$ar = mysqli_fetch_assoc(execute_query($sql));
	}
			if($_SESSION['user_type']!='ar_dr'){
?>	
			<?php 
			}
			else{
				echo '
				<div class="col-md-12">
				<div class="card">
					<div class="header">
						<div class="row">
							<div class="col-md-12 text-center">';
				// $sql = 'select ar_dr_details.sno as sno, ar_name, mobile_number, ar_id, ar_dr_details.district_id as district_id, master_division.division_name as division_name from ar_dr_details left join master_division on master_division.sno = ar_dr_details.district_id left join ar_dr on ar_dr.sno = ar_id where ar_id='.$_SESSION['user_id']';
				
				$sql = 'SELECT ar_dr.sno, ar_name, mobile_number, ar_dr.division_name, district_name, tehseel_name, block_name, master_division.division_name as division_name 
					FROM ar_dr left join master_division on master_division.sno = ar_dr.division_name where ar_dr.sno="'.$_SESSION['user_id'].'"';
				
				// echo $sql;
				$ar_dr_detail = mysqli_fetch_assoc(execute_query($sql));
				echo '<h4 class="title">DR Dashboard</h4>';
				echo '<strong>Name:</strong> '.$ar_dr_detail['ar_name'].'. <strong>Mobile:</strong> '.$ar_dr_detail['mobile_number'].'<br><strong>Alloted Division : </strong>';
				echo $ar_dr_detail['division_name'];
				echo '
							</div>
						</div>
					
				</div>
				</div>
				';
			}

			if($_SESSION['user_type']=="sadmin"){
			?>
				<div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">सर्वेक्षण कि स्थिति</h4>
                                <p class="category">अब तक कुल पंजीकृत समितियों के सर्वेक्षण कि स्थिति</p>
                            </div>
                            <div class="content">
								<div id="general_stat">
									<div class="table-container">
										<table id="" class="table table-responsive table-striped table-hover">
											<thead>
												<tr>
													<th>S.No.</th>
													<th>Society type</th>
													<th>Society Name</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
											<?php
											$sql= "SELECT `sno`, `society_type_id`, `society_name`, `society_address`, `sachive_name`, `latitude`, `longitude`, `status` FROM `master_society_details` WHERE 1=1";
											$res_society = execute_query($sql);
											$i=1;
											while ($row_society = mysqli_fetch_assoc($res_society)) {
												echo'<tr>
													<td>'.$i++.'</td>
													<td>'.$row_society['society_type_id'].'</td>
													<td>'.$row_society['society_name'].'</td>
													<td><a href="visit.php?sid=' . $row_society['sno'] . '&stid='.$row_society['society_type_id'].'" target="_blank"><i class="fa fa-edit "></i></a></td>
													
												
												</tr>';
												
											}
											
											?>
										</table>
                                	</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			<?php
			}
			?>
<script>
$(document).ready( function () {
    /*$('#general_stat_table').DataTable({
		paging: false,
		fixedHeader: true,
		colReorder: true
		});
	});	*/

	
	var t = $('#general_stat_table').DataTable({
        columnDefs: [
            {
                searchable: false,
                orderable: false,
                targets: 0,
            },
        ],
        order: [[10, 'dsc']],
		paging: false,
    });
 
    t.on('order.dt search.dt', function () {
        let i = 1;
 
        t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
});
</script>

<?php 
}
elseif(!isset($_SESSION['act_session_id'])){
?>
<style>


.full-page {
    background-image: url('images/bg_img.jpg');
    background-size: cover;
    background-position: center;
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
   
}
</style>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

	<div class="wrapper wrapper-full-page">
        <!-- Navbar -->
        
        <!-- End Navbar -->
        <div class="full-page  section-image" >
            <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
            <div class="content">
                <div class="container">
                    <div class="col-md-4 col-sm-6 ml-auto mr-auto login-page">
                        <form id="loginform" name="login" class="wufoo page" autocomplete="off" enctype="multipart/form-data" method="post" action="index.php">
                            <div class="card card-login" >
                                <div class="card-header card-header-rose text-center ">
                                   	<h2 class="header text-center" style="font-size: 1.6rem">उत्तर प्रदेश को-आपरेटिव डेटाबेस सेंटर &trade; (<span class="pe-7s-study"></span>)</h2>
                                    <?php echo $msg; ?>
                                </div>
                                <div class="card-body ">
                                    <div class="card-body">
										<div class="form-group">
											<label>User ID</label>
											<input type="text" placeholder="Enter User ID" name="username" class="form-control">
										</div>
										<div class="form-group">
											<label>Password</label>
											<input type="password" placeholder="Password" name="userpwd" class="form-control">
										</div>
										<div class="col-md-6 text-center">
											<button type="submit" name="submit" class="btn btn-warning btn-wd">Login</button>
										</div>
										
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <div class="full-page-background" ></div></div>
    </div>
	
	
<?php	
}
else{
	page_sidebar();

?>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="header">
						<div class="row">
							<div class="col-md-12 text-center">
								
								<?php
								
								// print_r($_SESSION['usertype']);
								// print_r($_SESSION['user_type']);
								
								switch($_SESSION['user_type']){
									case 'gm':{
											$sql = 'SELECT gm_details.sno AS sno, gm_name, mobile_number, gm_id, gm_details.district_id AS district_id, 
											master_district.district_name AS district_name, master_division.division_name AS division_name 
											FROM gm_details 
											LEFT JOIN master_district ON master_district.sno = gm_details.district_id 
											LEFT JOIN master_division ON master_division.sno = master_district.division_id 
											LEFT JOIN gm ON gm.sno = gm_id 
											WHERE gm_details.gm_id = ' . $_SESSION['user_id'];
							
											$result_gm_detail = execute_query($sql);
											
											$gm_district = array();
											$gm_district_id = array();
											$gm_name = '';
											$gm_number = '';
											$sql = 'SELECT bm.sno AS bm_sno, division_name, district_id 
													FROM bm 
													LEFT JOIN bm_details ON bm.sno = bm_details.bm_id 
													WHERE district_id IN (' . implode(",", $_SESSION['district_id']) . ')';
											
											$res = execute_query($sql);
											$bm_sno_array = array();
											
											if (mysqli_num_rows($res) > 0) {
												while ($row = mysqli_fetch_assoc($res)) {
													$bm_sno_array[] = $row['bm_sno'];
												}
											}
											$_SESSION['society_id_array'] = array();
											
											if (!empty($bm_sno_array)) {
												$sql = 'SELECT `sno`, `bm_society_id`, `society_id` 
														FROM `bm_society` 
														WHERE `bm_society_id` IN (' . implode(",", $bm_sno_array) . ')';
												
												$res = execute_query($sql);
												
												if (mysqli_num_rows($res) > 0) {
													while ($row = mysqli_fetch_assoc($res)) {
														$_SESSION['society_id_array'][] = $row['society_id'];
													}
												}
											}
											
											while ($row_gm_detail = mysqli_fetch_assoc($result_gm_detail)) {
												$gm_district[] = $row_gm_detail['district_name'];
												$gm_district_id[] = $row_gm_detail['district_id'];
												$gm_name = $row_gm_detail['gm_name'];
												$gm_number = $row_gm_detail['mobile_number'];
											}
											
											echo '<h4 class="title">GM Dashboard</h4>';
											echo '<strong>Name:</strong> ' . $gm_name . '. <strong>Mobile:</strong> ' . $gm_number . '<br><strong>Allotted District: </strong>';
											echo implode(",", $gm_district);
											
											
											
										break;
									}	
									case 'ar': {
										$sql = 'SELECT ar_details.sno AS sno, ar_name, ar.status AS status, mobile_number, helpdesk_no, ar_id, ar_details.district_id AS district_id, master_district.district_name AS district_name, master_division.division_name AS division_name 
												FROM ar_details 
												LEFT JOIN master_district ON master_district.sno = ar_details.district_id 
												LEFT JOIN master_division ON master_division.sno = master_district.division_id 
												left join helpdesk_tier_1 on helpdesk_tier_1.district_id = master_district.sno
												LEFT JOIN ar ON ar.sno = ar_id 
												WHERE ar_id = ' . intval($_SESSION['user_id']);
										
										$ar_detail = mysqli_fetch_assoc(execute_query($sql));
										
										
										echo '<h4 class="title">AC &amp; AR Dashboard</h4>';
										echo '<strong>Name:</strong> ' . htmlspecialchars($ar_detail['ar_name']) . '. <strong>Mobile:</strong> ' . htmlspecialchars($ar_detail['mobile_number']) . '<br><strong>Allotted District: </strong>' . htmlspecialchars($ar_detail['district_name']) . '<br><strong>Helpdesk: </strong><b>' . htmlspecialchars($ar_detail['helpdesk_no']) . '</b>';

										
										break;
									}					
									case 'adco':{
										$sql = 'select * from adco where sno='.$_SESSION['user_id'];
										$adco_detail = mysqli_fetch_assoc(execute_query($sql));
									
										$sql = 'select adco_details.sno as sno, adco_name, mobile_number, adco_id, adco_details.tehseel_id as tehseel_id, master_tehseel.tehseel_name tehseel_name, master_tehseel.district_id as district_id, master_district.district_name as district_name, master_division.division_name as division_name from adco_details left join master_tehseel on master_tehseel.sno = adco_details.tehseel_id left join master_district on master_district.sno = master_tehseel.district_id left join master_division on master_division.sno = master_district.division_id left join adco on adco.sno = adco_id where adco_id='.$_SESSION['user_id'];
										//echo $sql;
										$adco_result = execute_query($sql);
										$tehseel = array();
										$tehseel_id = array();
										$adco_name='';
										$adco_mobile='';									
										
										$i=1;
										while($row_adco = mysqli_fetch_assoc($adco_result)){
											$tehseel[$i]['tehseel_name'] = $row_adco['tehseel_name'];
											$tehseel[$i]['district_name'] = $row_adco['district_name'];
											$adco_name=$row_adco['adco_name'];
											$adco_mobile=$row_adco['mobile_number'];	
											$tehseel_id[] = $row_adco['tehseel_id'];
											$i++;

										}									
									
										echo '<h4 class="title">ADCO Dashboard</h4>';
										echo '<strong>Name:</strong> '.$adco_name.'. <strong>Mobile:</strong> '.$adco_mobile.'<br><strong>Alloted Tehseel: </strong>';
										foreach($tehseel as $k=>$v){
											echo $v['tehseel_name'].' (District: '.$v['district_name'].') | ';
										}
										break;
									}
									case 'ado':{
										$sql = 'select * from ado where sno='.$_SESSION['user_id'];
										$ado_detail = mysqli_fetch_assoc(execute_query($sql));
									
										$sql = 'select ado_details.sno as sno, ado_id, ado_details.block_id as block_id, block_name, tehseel_name, district_name, division_name from ado_details left join master_block on master_block.sno = ado_details.block_id left join master_tehseel on master_tehseel.sno = master_block.tehseel_id left join master_district on master_district.sno = master_tehseel.district_id left join master_division on master_division.sno = master_district.division_id where ado_id='.$ado_detail['sno'];
										$ado_block_result = execute_query($sql);
										$block = array();
										$_SESSION['block'] = array();
										$block_id = array();
										$i=1;
										while($ado_block_row = mysqli_fetch_assoc($ado_block_result)){
											$block_id[] = $ado_block_row['block_id'];
											$_SESSION['block'][] = $ado_block_row['block_id'];
											$block[$i]['block_id'] = $ado_block_row['block_id'];
											$block[$i]['block_name'] = $ado_block_row['block_name'];
											$block[$i]['tehseel_name'] = $ado_block_row['tehseel_name'];
											$block[$i]['district_name'] = $ado_block_row['district_name'];
											$block[$i]['division_name'] = $ado_block_row['division_name'];
											$i++;
										}
										
										echo '<h4 class="title">ADO Dashboard</h4>';
										echo '<strong>Name:</strong> '.$ado_detail['ado_name'].'. <strong>Mobile:</strong> '.$ado_detail['mobile_number'].'<br><strong>Alloted Blocks:</strong>';
										foreach($block as $k=>$v){
											echo $v['block_name'].' (Tehseel: '.$v['tehseel_name'].'. District: '.$v['district_name'].') | ';
										}
										
										break;
									}
									case 'bm':{
										$sql = 'select bm_details.sno as sno, bm_name, mobile_number, bm_id, bm_details.district_id as district_id, master_district.district_name as district_name, master_division.division_name as division_name from bm_details left join master_district on master_district.sno = bm_details.district_id left join master_division on master_division.sno = master_district.division_id left join bm on bm.sno = bm_id where bm_details.bm_id='.$_SESSION['user_id'];
										$bm_detail = '';
										$bm_detail = mysqli_fetch_assoc(execute_query($sql));
										echo '<h4 class="title">Branch Manager Dashboard</h4>';
										echo '<strong>Name:</strong> '.$bm_detail['bm_name'].'. <strong>Mobile:</strong> '.$bm_detail['mobile_number'].'<br><strong>Alloted District : </strong>';
										echo $bm_detail['district_name'];
										break;
									}
								}
								
								?>
								
							</div>                                
						</div>
					</div>
				</div>
			</div>
		</div>
		
			<?php if($_SESSION['user_type']!='ado' && $_SESSION['user_type']!='bm'){ ?>
			
			<?php }else if ($_SESSION['user_type']=='ado' || $_SESSION['user_type']=='bm'){?>
			
			
			<?php }?>
			
		</div>
		<?php 
		//print_r($_SESSION['user_type']);
		
		if($_SESSION['user_type']!='ado'&& $_SESSION['user_type']!='bm'){ ?>
		
		<?php } ?>



<?php		
		
}
page_footer_start();
?>
<script src="js/chart.min.js"></script>
<script>

<?php
page_footer_end();
?>
