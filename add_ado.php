<?php
error_reporting(E_ALL);
include("scripts/settings.php");
$tab=1;
$msg='';
//print_r($_POST);
if(isset($_POST['submit'])) {
	
	$sql = '(SELECT sno, pwd, username, ar_name as full_name, mobile_number,"2" as navuser_type, "ar_dr"  as info FROM `ar_dr` where username="'.$_POST['username'].'") union all
	(SELECT sno, pwd, username, ar_name as full_name, mobile_number, "3" as navuser_type, "ar" as info FROM `ar` where username="'.$_POST['username'].'") 
		union all 
	(SELECT sno,pwd, username, ado_name as full_name, mobile_number, "4" as navuser_type, "ado" as info FROM `ado` where username="'.$_POST['username'].'") 
		union all 
	(SELECT sno,pwd, username, adco_name as full_name, mobile_number, "5" as navuser_type, "adco" as info FROM `adco` where username="'.$_POST['username'].'")';
	$result = execute_query($sql);
	
	$num_rows= mysqli_num_rows($result);
	$result=mysqli_query($db,$sql);
	$num_rows= mysqli_num_rows($result);				
			
		if($_POST['ado_name']==''){
			$msg .= '<li>Please Enter ADO Name.</li>';
		}
		if($msg==''){
			if($_POST['edit_sno']!=''){
				if($num_rows<=1){	
					
					$sql = 'update ado set ado_name="'.$_POST['ado_name'].'",
					mobile_number="'.$_POST['mobile_number'].'",
					username="'.$_POST['username'].'",
					pwd="'.$_POST['pwd'].'",
					division_name="'.$_POST['division_name'].'",
					district_name="'.$_POST['district_name'].'",
					tehseel_name="'.$_POST['tehseel_name'].'"
					where sno='.$_POST['edit_sno'];
					//echo $sql;
					$res = execute_query($sql);
					if ($res) {
						$msg .= '<li>Update sucessful.</li>';

						$sql = 'delete from ado_details where ado_id= "'.$_POST['edit_sno'].'" ';
						execute_query($sql);
						$sql2 = 'select * from ado where sno='.$_POST['edit_sno'];
						$result2 = mysqli_fetch_array(execute_query($sql2));
						$inv2 = $result2['sno'];
						if($inv2!=''){
							foreach($_POST['block_name'] as $k=>$v){
								
								$sql = 'insert into ado_details (ado_id, block_id) values 
								("'.$inv2.'","'.$v.'")';
								//echo $sql;
								execute_query($sql);
								if(mysqli_error($db)){
									$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
								}
							}
						}
					
					}
					
				}else{
					$msg .= '<li>Username already taken Enter Unique Username.</li>';
				}	
			}
			else{
				if($num_rows==0){
					
					$sql = 'insert into `ado` (ado_name, pwd, username, mobile_number, division_name, district_name, tehseel_name, created_by, creation_time) values ("'.$_POST['ado_name'].'","'.$_POST['pwd'].'","'.$_POST['username'].'", "'.$_POST['mobile_number'].'", "'.$_POST['division_name'].'", "'.$_POST['district_name'].'", "'.$_POST['tehseel_name'].'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
					//echo $sql;
					execute_query($sql);
					
					if(mysqli_error($db)){
						$msg .= '<li>Error # 1 : '.mysqli_error($db).' >> '.$sql.'</li>';
						$inv=0;
					}
					else{
						$inv = mysqli_insert_id($db);
					}		
					if($inv!=0){
						foreach($_POST['block_name'] as $k=>$v){
							
							$sql = 'insert into ado_details (ado_id, block_id) values 
							("'.$inv.'","'.$v.'")';
							//echo $sql;
							execute_query($sql);
							if(mysqli_error($db)){
								$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
							}
						}
					}
				}else{
					$msg .= '<li>Username already taken Enter Unique Username.</li>';
				}
			}
		}
}

if(isset($_GET['id'])){
	$sql = 'select * from ado where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row_edit = mysqli_fetch_array($result);
		
}
else{
	$row_edit['sno'] = '';
	$row_edit['division_name'] = '';
	$row_edit['district_name'] = '';
	$row_edit['tehseel_name'] = '';
}

if(isset($_GET['del'])){
	$sql = 'delete from ado where sno='.$_GET['del'];
	$result_ado = execute_query($sql);

	$sql = 'delete from ado_details where ado_id= '.$_GET['del'];
	$result_ado_details = execute_query($sql);
	
}

page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/survey_validation.js"></script>
<?php
page_header_end();
page_sidebar();


?>
				<div class="row">					
					<div class="col-md-12">
						<div class="card">
							<div class="card-body">
								<div class="row d-flex my-auto">
									<div class="col-md-12">
										<?php echo $msg; ?>
										<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
											
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-3 form-group">
														<label>ADO Name</label>
														<input type="text" name="ado_name" id="ado_name" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($_GET['id'])){echo $row_edit['ado_name'];} ?>">
													</div>
													<div class="col-sm-3 form-group">
														<label>Mobile Number</label>
														<input type="text" name="mobile_number" id="mobile_number" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($_GET['id'])){echo $row_edit['mobile_number'];} ?>">
													</div>
													<div class="col-sm-3 form-group">
														<label>Username</label>
														<input type="text" name="username" id="username" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($_GET['id'])){echo $row_edit['username'];} ?>">
													</div>
													<div class="col-sm-3 form-group">
														<label>Password</label>
														<input type="text" name="pwd" id="pwd" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($_GET['id'])){echo $row_edit['pwd'];} ?>">
													</div>
												</div>
													<?php //echo print_r($_SESSION['district_id']);?>
												<div class="row">
													<div class="col-sm-3 form-group">
														<label>मण्डल</label>
														<select name="division_name" id="division_name" tabindex="<?php echo $tab++; ?>"  class="form-control" onChange="fill_district(this.value);">
															<option value="">--Select--</option>
															<?php
															if($_SESSION['user_type']=='ar'){
																$sql = 'select * from master_division where  sno in (' . implode(",", $_SESSION['division_id']) . ')';
															}else{
																$sql = 'select * from master_division';
															}
															$result_division = execute_query($sql);
															while($row_division = mysqli_fetch_assoc($result_division)){
																echo '<option value="'.$row_division['sno'].'" ';
																if(isset($_GET['id'])){
																	if($row_edit['division_name']==$row_division['sno']){
																		 echo ' selected="selected" ';
																	}
																}
																echo '>'.$row_division['division_name'].'</option>';
															}
															?>
														</select>
													</div>
													<div class="col-sm-3 form-group">
														<label>जनपद</label>
														<select name="district_name" id="district_name" tabindex="<?php echo $tab++; ?>"  class="form-control" onChange="fill_tehseel(this.value);">
															<option value="<?php echo $row_edit['district_name']; ?>">
																<?php
																if($_SESSION['user_type']=='ar'){
																	$sql = 'select * from master_district where  sno in (' . implode(",", $_SESSION['district_id']) . ')';
																}else{
																	$sql = 'select * from master_district';
																}
																	$result_district = execute_query($sql);
																	while($row_district = mysqli_fetch_assoc($result_district)){
																		echo '<option value="'.$row_district['sno'].'" ';
																		if(isset($_GET['id'])){
																			if($row_edit['district_name']==$row_district['sno']){
																				 echo ' selected="selected" ';
																			}
																		}
																		echo '>'.$row_district['district_name'].'</option>';
																	}
																?>
															
															</option>
														</select>
													</div>
													<div class="col-sm-3 form-group">
														<label>तहसील</label>
														<select name="tehseel_name" id="tehseel_name" tabindex="<?php echo $tab++; ?>"  class="form-control" onChange="fill_block(this.value);">
															<option value="<?php echo $row_edit['tehseel_name']; ?>">
																<?php
																	$sql = 'select * from master_tehseel';
																	$result_tehseel = execute_query($sql);
																	while($row_tehseel = mysqli_fetch_assoc($result_tehseel)){
																		echo '<option value="'.$row_tehseel['sno'].'" ';
																		if(isset($_GET['id'])){
																			if($row_edit['tehseel_name']==$row_tehseel['sno']){
																				 echo ' selected="selected" ';
																			}
																		}
																		echo '>'.$row_tehseel['tehseel_name'].'</option>';
																	}
																?>
															
															
															</option>
														</select>
													</div>

													<div class="col-sm-3 form-group">
														<label>विकासखंड</label>
														<select name="block_name[]" id="block_name" tabindex="<?php echo $tab++; ?>"  class="form-control" multiple="multiple">
															<?php
																		if(isset($_GET['id'])){
																			$sql = 'select * from ado_details where ado_id="'.$_GET['id'].'"';
																			$result_detail = execute_query($sql);
																			$array = array();
																			$a=0;
																			while($row_detail = mysqli_fetch_assoc($result_detail)){
																				$array[] = $row_detail['block_id'];
																			}
																			$sql = 'select * from master_block';
																			$result_district = execute_query($sql);
																			while($row_district = mysqli_fetch_assoc($result_district)){
																				
																			
																			if(in_array($row_district['sno'], $array)){
																				echo '<option value="'.$row_district['sno'].'" ';
																				 echo ' selected="selected" ';
																				 echo '>'.$row_district['block_name'].'</option>';
																			}
																		}
																	}
																?>
														</select>
													</div>
												</div>
												<input type="submit" class="btn btn-info btn-fill pull-right" value="ADD ADO" name="submit" id="submit" />
												<input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo $_GET['id'];}?>" />
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
                        <div class="col-md-12">
                            <div class="card strpied-tabled-with-hover">
                               
                                <div class="card-body table-full-width table-responsive">
								<table class="table table-hover table-striped text-center">
                                        <thead>
                                            <tr>
                                            <th>S.No.</th>
                                            <th>ADO Name</th>
                                            <th>Mobile Number</th>
											<th>Username</th>
											<th>Password</th>
											<th>Division Name</th>
											<th>District Name</th>
											<th>Tehseel Name</th>
											<th>Block Name</th>
											<th>ID</th>
											<th>Edit</th>
											<th>Delete</th>
                                            
                                        </tr></thead>
                                        <tbody>
                                           	<?php
											$i=1;
											
											if($_SESSION['user_type']=='ar'){
												$sql = 'select ado.sno as sno,username, pwd, ado.ado_name as ado_name, ado.mobile_number as mobile_number, ado.district_name as district_name, ado.division_name as division_name, ado.tehseel_name as tehseel_name, ado_details.ado_id as ado_id, ado_details.block_id as block_id from ado left join ado_details on ado.sno = ado_details.ado_id where district_name in ('.implode(",", $_SESSION['district_id']).')';
											}else{
												$sql = 'select ado.sno as sno,username, pwd, ado.ado_name as ado_name, ado.mobile_number as mobile_number, ado.district_name as district_name, ado.division_name as division_name, ado.tehseel_name as tehseel_name, ado_details.ado_id as ado_id, ado_details.block_id as block_id from ado left join ado_details on ado.sno = ado_details.ado_id';
											}
											
											//echo $sql;
											$result = execute_query($sql);
											
											while($row=mysqli_fetch_array($result)){
												
											$sql_district = 'select * from master_district where sno = "'.$row['district_name'].'"';
											//echo $sql_district.'</br>';
											$result_district = mysqli_fetch_array(execute_query($sql_district));
											
											
											
											$sql_division = 'select * from master_division where sno = "'.$row['division_name'].'"';
											$result_division = mysqli_fetch_array(execute_query($sql_division));
											
											$sql_tehseel = 'select * from master_tehseel where sno = "'.$row['tehseel_name'].'"';
											$result_tehseel = mysqli_fetch_array(execute_query($sql_tehseel));
											
											$sql_block = 'select * from master_block where sno = "'.$row['block_id'].'"';
											$result_block = mysqli_fetch_array(execute_query($sql_block));
											
											?>
											<tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $row['ado_name']; ?></td>
												<td><?php echo $row['mobile_number']; ?></td>
												<td><?php echo $row['username']; ?></td>
												<td><?php echo $row['pwd']; ?></td>
												<td><?php echo $result_division['division_name']; ?></td>
												<td><?php echo $result_district['district_name']; ?></td>
												<td><?php echo $result_tehseel['tehseel_name']; ?></td>
												<td><?php echo $result_block['block_name']; ?></td>
												<td><?php echo $row['sno']; ?></td>
												<td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?id='.$row['sno'];?>" alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit" aria-hidden="true"></span></td>
												<td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?del='.$row['sno'];?>" onclick="return confirm('Are you sure?');" style="color:#f00" alt="Delete"><span class="far fa-trash-alt" aria-hidden="true" data-toggle="tooltip" title="Delete"></span></a></td>
                                                
                                            </tr>
											
											
											<?php
											}
											
											?>
                                            
                                        </tbody>
                                    </table>
				


				
																						


				
<script>

</script>	
  								
				
<?php
page_footer_start();
?>
<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<?php		
page_footer_end();
?>
