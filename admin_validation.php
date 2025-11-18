<?php
include("scripts/settings.php");
$msg='';
$tab=1;
if(isset($_POST['update_profile'])) {
	$sql='delete from user_access where user_id="'.$_POST['job_role_hidden'].'"';
	execute_query($sql);
	if(mysqli_error($db)){
		$msg .= '<li>Error # 1 : '.mysqli_error($db).' >> '.$sql;
	}
	$sql = 'select * from navigation';
	$result=execute_query($sql);
	while($nav=mysqli_fetch_array($result)){
		$check1='check_'.$nav['sno'];
		if(isset($_POST[$check1])){
			$sql='INSERT INTO `user_access`(`user_id`, `file_name`, `created_by`, `creation_time`) 
			VALUES("'.$_POST['job_role_hidden'].'", "'.$nav['sno'].'", "'.$_SESSION['username'].'","'.date("Y-m-d H:i:s").'")';
			execute_query($sql);
		}
	}
}
else{
	$_POST['plv_tasks']='';
	$_POST['expiry_date'] = date("Y-m-d");
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
								<h4 class="card-title">Validation</h4>
								<?php echo $msg; ?>
							</div>
							<div class="card-body">
								<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
									<div class="row">
										<div class="col-md-4 pr-1">
											<div class="form-group">
												<label>Job Role</label>
												<select name="job_role" id="job_role" tabindex="<?php echo $tab++; ?>" class="form-control">
													<option value="1">Tehsildar</option>
													<option value="2">PLV Users</option>
												</select>
											</div>
										</div>
										<div class="col-md-4 pr-1">
											<button type="submit" class="btn btn-info btn-fill pull-right">Proceed</button>
										</div>
									</div>
								</form>
								<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
									<?php
									if(isset($_POST['job_role'])){
									?>
									<div class="row">
										<div class="col-md-12">
										<input type="hidden" name="job_role_hidden" value="<?php echo $_POST['job_role']; ?>">
											<table width="100%" class="table table-hover table-striped">
												<thead>
													<tr>
														<th>Module Name</th>
														<th></th>
													</tr>
												</thead>
											<?php
											$sql='select * from navigation where parent in ("") or parent is null order by link_description';
											$new = execute_query($sql);
											$i=1;
											while($row = mysqli_fetch_array($new)){
												$sql = 'select * from user_access where user_id="'.$_POST['job_role'].'" and file_name="'.$row['sno'].'"';
												$result_access = execute_query($sql);
												if(mysqli_num_rows($result_access)==1){
													$selected = 'checked="checked"';

												}
												else{
													$selected = '';
												}
												echo '<tr>
												<td>'.$row['link_description'].'</td>
												<td><input type="checkbox"  name="check_'.$row['sno'].'" value="" tabindex="'.$tab++.'" '.$selected.'><input type="hidden" name="id" id="id" value="'.$i++.'"></td>
												</tr>';
											}

											$sql='select * from navigation where parent in ("P") order by link_description';
											$new = execute_query($sql);
											$i=1;
											while($row = mysqli_fetch_array($new)){
												echo '<tr><th colspan="2">'.$row['link_description'].'</th></tr>';
												$sql = 'select * from navigation where parent in ('.$row['sno'].') order by link_description';
												$res_sub_menu = execute_query($sql);
												while($row_sub_menu = mysqli_fetch_array($res_sub_menu)){
													$sql = 'select * from user_access where user_id="'.$_POST['job_role'].'" and file_name="'.$row_sub_menu['sno'].'"';
													$result_access = execute_query($sql);
													//echo $sql.'<br>';
													if(mysqli_num_rows($result_access)==1){
														$selected = 'checked="checked"';

													}
													else{
														$selected = '';
													}
													echo '<tr>
													<td>'.$row_sub_menu['link_description'].'</td>
													<td><input type="checkbox"  name="check_'.$row_sub_menu['sno'].'" value="" tabindex="'.$tab++.'" '.$selected.'><input type="hidden" name="id" id="id" value="'.$i++.'">
													</tr>';

												}
											}

											?>
											</table>
											<button type="submit" name="update_profile" class="btn btn-info btn-fill pull-right">Update Profile</button>
										</div>
									</div>
									
									<?php
									}
									?>
									
									<div class="clearfix"></div>
								</form>
							</div>
						</div>
					</div>
				</div>				
				
<?php
page_footer_start();
?>


    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<?php		
page_footer_end();
?>
