<?php
include("scripts/settings.php");
$msg='';
$tab=1;
if(isset($_POST['aadhaar'])) {
	if($_POST['aadhaar']==''){
		$msg .= '<h6 class="alert alert-danger">Enter Aadhaar Number</h6>';
	}
	if($_POST['mobile']==''){
		$msg .= '<h6 class="alert alert-danger">Enter Mobile Number</h6>';
	}
	if($_POST['full_name']==''){
		$msg .= '<h6 class="alert alert-danger">Enter Full Name</h6>';
	}
	if($_POST['father_name']==''){
		$msg .= '<h6 class="alert alert-danger">Enter Father Name</h6>';
	}
	if($_POST['address']==''){
		$msg .= '<h6 class="alert alert-danger">Enter Complete Address</h6>';
	}
	if($msg==''){
		if($_POST['edit_sno']!=''){
			$sql = 'delete from plv_users_villages where user_id='.$_POST['edit_sno'];
			execute_query($sql);
			$insert_id = $_POST['edit_sno'];
			if($_POST['job_role']==2){

				$vil = array();
				foreach($_POST['villages'] as $k=>$v){
					$vil[] = '("'.$insert_id.'", "'.$v.'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
				}
				$sql = 'insert into plv_users_villages (user_id, village_id, created_by, creation_time) values '.implode(", ", $vil);
				execute_query($sql);
				if(mysqli_error($db)){
					$msg .= '<h6 class="alert alert-danger">Error 4 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
					$msg .= '<h6 class="alert alert-danger">Error 4.</h6>';
				}

			}
			if($msg==''){
				$msg .= '<h6 class="alert alert-success">Updated.</h6>';
				$_POST['aadhaar']='';
				$_POST['mobile']='';
				$_POST['email']='';
				$_POST['full_name']='';
				$_POST['father_name']='';
				$_POST['address']='';

			}
		}
		else{			
			$sql = 'select * from plv_users where aadhaar="'.$_POST['aadhaar'].'" and 1!=1';
			$result = execute_query($sql);
			if(mysqli_num_rows($result)==0){
				$sql = 'insert into users (userid, pwd, user_name, type, father_name, address, mobile, created_by, creation_time) values ("", "", "'.$_POST['full_name'].'", "'.$_POST['job_role'].'", "'.$_POST['father_name'].'", "'.$_POST['address'].'", "'.$_POST['mobile'].'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
				execute_query($sql);
				if(mysqli_error($db)){
					$msg .= '<h6 class="alert alert-danger">Error 1 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
					$msg .= '<h6 class="alert alert-danger">Error 1.</h6>';
				}
				else{
					$insert_id = mysqli_insert_id($db);

				}

				$id = $insert_id;

				$otp = randomnumber();
				$user_pwd = randomnumber();
				if($_POST['job_role']==1){
					$user_id = 'th'.$id;
				}
				else{
					$user_id = 'pl'.$id;
				}
				$sql = 'update users set userid="'.$user_id.'", pwd="'.$user_pwd.'" where sno='.$id;
				execute_query($sql);
				if(mysqli_error($db)){
					$msg .= '<h6 class="alert alert-danger">Error 2 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
					$msg .= '<h6 class="alert alert-danger">Error 2.</h6>';
				}


				$sql = 'insert into plv_users (user_id, full_name, father_name, mobile, mobile_verify, aadhaar, address, job_role, district, tehsil, email, photo_id, expiry_date, status, created_by, creation_time) values("'.$id.'", "'.$_POST['full_name'].'", "'.$_POST['father_name'].'", "'.$_POST['mobile'].'", "'.$otp.'", "'.$_POST['aadhaar'].'", "'.$_POST['address'].'", "'.$_POST['job_role'].'", "1", "'.$_POST['tehsil'].'", "'.$_POST['email'].'", "", "'.$_POST['expiry_date'].'", "0", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
				execute_query($sql);
				if(mysqli_error($db)){
					$msg .= '<h6 class="alert alert-danger">Error 3 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
					$msg .= '<h6 class="alert alert-danger">Error 3.</h6>';
				}
				else{
					$insert_id = mysqli_insert_id($db);
				}

				if($_POST['job_role']==2){

					$vil = array();
					foreach($_POST['villages'] as $k=>$v){
						$vil[] = '("'.$insert_id.'", "'.$v.'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
					}
					$sql = 'insert into plv_users_villages (user_id, village_id, created_by, creation_time) values '.implode(", ", $vil);
					execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<h6 class="alert alert-danger">Error 4 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
						$msg .= '<h6 class="alert alert-danger">Error 4.</h6>';
					}

				}
				if($_POST['legal_aid_clinic']!=''){
					$sql = 'INSERT INTO `plv_legal_aid_clinic_allotment` (`plv_id`, `clinic_id`, `clinic_validity_from`, `clinic_validity_to`, `created_by`, `creation_time`) VALUES ("'.$insert_id.'", "'.$_POST['legal_aid_clinic'].'", "'.$_POST['validity_date_from'].'", "'.$_POST['validity_date_to'].'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
					execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<h6 class="alert alert-danger">Error 5 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
						$msg .= '<h6 class="alert alert-danger">Error 5.</h6>';
					}
				}
				if($msg==''){
					$msg .= '<h6 class="alert alert-success">Success. User Name: '.$user_id.' Password: '.$user_pwd.'</h6>';
					$_POST['aadhaar']='';
					$_POST['mobile']='';
					$_POST['email']='';
					$_POST['full_name']='';
					$_POST['father_name']='';
					$_POST['address']='';

				}
			}
			else{
				$msg .= '<h6 class="alert alert-danger">Duplicate Entry!</h6>';
			}
		}
	}
}
elseif(isset($_GET['id'])){
	$sql = 'select * from plv_users where sno='.$_GET['id'];
	$plv_user = mysqli_fetch_assoc(execute_query($sql));
	
	$_POST['aadhaar']=$plv_user['aadhaar'];
	$_POST['mobile']=$plv_user['mobile'];
	$_POST['email']=$plv_user['email'];
	$_POST['full_name']=$plv_user['full_name'];
	$_POST['father_name']=$plv_user['father_name'];
	$_POST['tehsil']=$plv_user['tehsil'];
	$_POST['address']=$plv_user['address'];
	$_POST['job_role']=$plv_user['job_role'];
	$_POST['expiry_date'] = $plv_user['expiry_date'];
	
}
else{
	$_POST['aadhaar']='';
	$_POST['mobile']='';
	$_POST['email']='';
	$_POST['full_name']='';
	$_POST['father_name']='';
	$_POST['tehsil']='';
	$_POST['job_role']='';
	$_POST['address']='';
	$_POST['expiry_date'] = date("Y-m-d");
	$_POST['validity_date_from'] = date("Y-m-d");
	$_POST['validity_date_to'] = date("Y-m-d");
}
?>

<?php
page_header_start();
page_header_end();
page_sidebar();

?>
   				<div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">User Profile</h4>
                                    <?php echo $msg; ?>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
										<div class="row">
											<div class="col-md-4">
												<label>Expiry Date</label>
												<script type="text/javascript" language="javascript">
												document.writeln(DateInput('expiry_date', 'user_form', true, 'YYYY-MM-DD', '<?php echo $_POST['expiry_date']; ?>', <?php echo $tab++; $tab=$tab+3; ?>));
												 </script>
											</div>
										</div>
                                       	<div class="row">
                                            <div class="col-md-4 pr-1">
                                                <div class="form-group">
                                                    <label>Legal Aid Clinic</label>
                                                    <select name="legal_aid_clinic" id="legal_aid_clinic" tabindex="<?php echo $tab++; ?>" class="form-control" onChange="legal_aid(this.value);">
                                                    	<option value="">N/A</option>
                                                    	<?php
														$sql='select * from plv_legal_aid_clinic';
														$result_clinic = execute_query($sql);
														while($row_clinic=mysqli_fetch_assoc($result_clinic)){
															echo '<option value="'.$row_clinic['sno'].'" ';
															echo ($_POST['legal_aid_clinic']==$row_clinic['sno']?"selected":"");
															
															echo ' >'.$row_clinic['clinic_name'].'</option>';
														}
														
														?>
													</select>
                                                </div>
                                            </div>
                                            <div class="col-md-4" id="exp_date_from_display" style="display: none;">
												<label>Validity Date From</label>
												<script type="text/javascript" language="javascript">
												document.writeln(DateInput('validity_date_from', 'user_form', true, 'YYYY-MM-DD', '<?php echo $_POST['validity_date_from']; ?>', <?php echo $tab++; $tab=$tab+3; ?>));
												 </script>
											</div>
											<div class="col-md-4"id="exp_date_to_display" style="display: none;">
												<label>Validity Date To</label>
												<script type="text/javascript" language="javascript">
												document.writeln(DateInput('validity_date_to', 'user_form', true, 'YYYY-MM-DD', '<?php echo $_POST['validity_date_to']; ?>', <?php echo $tab++; $tab=$tab+3; ?>));
												 </script>
											</div>
										</div>
                                        <div class="row">
                                            <div class="col-md-4 pr-1">
                                                <div class="form-group">
                                                    <label>Job Role</label>
                                                    <select name="job_role" id="job_role" tabindex="<?php echo $tab++; ?>" class="form-control" onChange="update_locations();">
                                                    	<option value="1" <?php echo ($_POST['job_role']==1?"selected":"");?>>Tehsildar</option>
                                                    	<option value="2" <?php echo ($_POST['job_role']==2?"selected":"");?>>PLV</option>
													</select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 pr-1">
                                                <div class="form-group">
                                                    <label>Tehsil</label>
                                                    <select name="tehsil" id="tehsil" tabindex="<?php echo $tab++; ?>" class="form-control" onChange="update_locations();">
                                                    	<?php
														$sql = 'select * from location_tehsil';
														$result_tehsil = execute_query($sql);
														while($row_tehsil = mysqli_fetch_assoc($result_tehsil)){
															echo '<option value="'.$row_tehsil['sno'].'" ';
															echo ($_POST['tehsil']==$row_tehsil['sno']?"selected":"");
															echo ' >'.$row_tehsil['location_name'].'</option>';
														}
														
														?>
													</select>
                                                </div>
                                            </div>
                                            <div class="col-md-5 pr-1">
                                                <div class="form-group" id="villages_group" style="display: none;">
                                                    <label>Villages</label>
                                                    <select name="villages[]" id="villages" multiple tabindex="<?php echo $tab++; ?>" class="form-control">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 pr-1">
                                                <div class="form-group">
                                                    <label>Aadhaar</label>
                                                    <input type="text" name="aadhaar" id="aadhaar" tabindex="<?php echo $tab++; ?>" class="form-control" placeholder="Aadhaar Number" value="<?php echo $_POST['aadhaar']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3 pr-1">
                                                <div class="form-group">
                                                    <label>Mobile</label>
                                                    <input type="text" name="mobile" id="mobile" tabindex="<?php echo $tab++; ?>" class="form-control" placeholder="Mobile Number" value="<?php echo $_POST['mobile']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-5 pr-1">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="text" name="email" id="email" tabindex="<?php echo $tab++; ?>" class="form-control" placeholder="Email Address" value="<?php echo $_POST['email']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 pr-1">
                                                <div class="form-group">
                                                    <label>Full Name</label>
                                                    <input type="text" name="full_name" id="full_name" tabindex="<?php echo $tab++; ?>" class="form-control" placeholder="Full Name" value="<?php echo $_POST['full_name']; ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 pl-1">
                                                <div class="form-group">
                                                    <label>Father Name</label>
                                                    <input type="text" name="father_name" id="father_name" tabindex="<?php echo $tab++; ?>" class="form-control" placeholder="Father Name" value="<?php echo $_POST['father_name']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <input type="text" name="address" id="address" tabindex="<?php echo $tab++; ?>" class="form-control" placeholder="Complete Address" value="<?php echo $_POST['address']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info btn-fill pull-right">Update Profile</button>
                                        <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];} ?>">
                                        <div class="clearfix"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
				
				
				<div class="row">
                        <div class="col-md-12">
                            <div class="card strpied-tabled-with-hover">
                                <div class="card-header ">
                                    <h4 class="card-title">Location</h4>
                                    <p class="card-category">Space for Pagination</p>
                                </div>
                                <div class="card-body table-full-width table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                            <th>S.No.</th>
                                            <th>Full Name</th>
                                            <th>Father Name</th>
                                            <th>Mobile</th>
                                            <th>Job Role</th>
                                            <th>Tehsil</th>
                                            <th></th>
                                            <th></th>
                                        </tr></thead>
                                        <tbody>
                                           	<?php
											$i=1;
											$sql = 'select plv_users.sno as sno, plv_users.full_name as full_name, plv_users.father_name as father_name, plv_users.mobile as mobile, job_role, tehsil, location_name, location_name_english from plv_users left join users on plv_users.user_id = users.sno left join location_tehsil on location_tehsil.sno = tehsil where users.sno!=1';
											$result = execute_query($sql);
											while($row = mysqli_fetch_assoc($result)){
												$legal_aid = '';
												$sql = 'SELECT clinic_name, clinic_validity_from, clinic_validity_to FROM `plv_legal_aid_clinic_allotment` left join plv_legal_aid_clinic on plv_legal_aid_clinic.sno = plv_legal_aid_clinic_allotment.clinic_id where plv_id="'.$row['sno'].'"';
												$result_clinic_allotment = execute_query($sql);
												while($row_clinic_allotment = mysqli_fetch_assoc($result_clinic_allotment)){
													$legal_aid .= '<div class="small text-warning">Clinic : '.$row_clinic_allotment['clinic_name'].'. From :'.$row_clinic_allotment['clinic_validity_from'].'. To :'.$row_clinic_allotment['clinic_validity_to'].'</div>';
												}
											?>
											<tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $row['full_name'].$legal_aid; ?></td>
                                                <td><?php echo $row['father_name']; ?></td>
                                                <td><?php echo $row['mobile']; ?></td>
                                                <td><?php echo ($row['job_role']==1?'Tehsildar':'PLV'); ?></td>
                                                <td><?php echo $row['location_name'].' ('.$row['location_name_english'].')'; ?></td>
                                                <!--<td><a href="scripts/printing_sale.php?inv='.$row['sno'].'" target="_blank"><span class="fa fa-eye" aria-hidden="true" data-toggle="tooltip" title="View Invoice"></span></a></td>-->
												<td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?id='.$row['sno'];?>" alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit" aria-hidden="true"></span></td>
												<!--<td><a href="sale_new.php?copy='.$row['sno'].'" target="_blank" alt="Copy Invoice" data-toggle="tooltip" title="Copy Invoice"><span class="pe-7s-copy-file" aria-hidden="true"></span></a></td>-->
												<td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?del='.$row['sno'].'" onclick="return confirm(\'Are you sure?\');" style="color:#f00" alt="Delete"><span class="far fa-trash-alt" aria-hidden="true" data-toggle="tooltip" title="Delete"></span></a></td>
                                            </tr>
											
											
											<?php
											}
											
											?>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
<?php
page_footer_start();
?>


    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>

    <script>
		function update_locations(plv_id=''){
			var job_role = $("#job_role").val();
			if(job_role=='2'){
				var tehsil = $("#tehsil").val();
				if(plv_id!=''){
					var url = "scripts/ajax.php?id=villages_selected&term="+tehsil+"&plv_id="+plv_id;
				}
				else{
					var url = "scripts/ajax.php?id=villages&term="+tehsil;
				}
				$.ajax({
					url: "scripts/ajax.php?id=villages&term="+tehsil,
					dataType:"json"
				})
				.done(function( data ) {
					var selected = [];
					if(plv_id!=''){
						$.ajax({
							url: "scripts/ajax.php?id=villages_selected&term="+tehsil+"&plv_id="+plv_id,
							dataType:"json"
						})
						.done(function( data ) {
							$.each(data, function(k, value){
								selected[value.id] = value.location_name;
							});
						});
					}
					var txt = '<label>Villages</label><select name="villages[]" id="villages" multiple class="form-control">';
					$.each(selected, function(k,v){
						console.log('@@'+k);	
					});
					console.log(selected[879]);
					$.each(data, function(k, value){
						//console.log(value.id);
						txt += '<option value="'+value.id+'" ';
						if (typeof selected[value.id] !== 'undefined') {
							console.log('ok');
							txt += ' selected ';
						}
						txt += '>'+value.location_name+'</option>';
					});
					txt += '</select>';
					$("#villages_group").html(txt);
					$("#villages").multiselect({
						selectAll: true,
						search: true
					});
					$("#villages_group").show();
				});
			}
			else{
				$("#villages_group").hide();
			}
		}
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
		function legal_aid(id){
			if(id!=''){
				$("#exp_date_from_display").css("display", "block");
				$("#exp_date_to_display").css("display", "block");
			}
			else{
				$("#exp_date_from_display").css("display", "none");
				$("#exp_date_to_display").css("display", "none");
			}
		}
		<?php
		if(isset($_GET['id'])){
		?>
		$(document).ready(function() {
			update_locations(<?php echo $_GET['id']; ?>);
		});
		<?php } ?>
		
    </script>
	<!--  Charts Plugin -->
	<script src="js/chartist.min.js"></script>

<?php		
page_footer_end();
?>
