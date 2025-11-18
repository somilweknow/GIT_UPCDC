<?php
include("scripts/settings.php");
$tab=1;
$msg='';
//print_r($_POST);
if(isset($_POST['submit'])) {
	if($_POST['gm_name']==''){
		$msg .= '<li>Please Enter AR Name.</li>';
	}
	if($msg==''){
		if($_POST['edit_sno']!=''){
			$sql = 'update gm set gm_name="'.$_POST['gm_name'].'",
			mobile_number="'.$_POST['mobile_number'].'",
			username="'.$_POST['username'].'",
			pwd="'.$_POST['pwd'].'",
			division_name="'.$_POST['division_name'].'"
			where sno='.$_POST['edit_sno'];
			//echo $sql;
			$res = execute_query($sql);
			if ($res) {
				$msg .= '<li>Update sucessful.</li>';

				$sql = 'delete from gm_details where gm_id= "'.$_POST['edit_sno'].'" ';
				execute_query($sql);
				$sql2 = 'select * from gm where sno='.$_POST['edit_sno'];
				$result2 = mysqli_fetch_array(execute_query($sql2));
				$inv2 = $result2['sno'];
				if($inv2!=''){
					foreach($_POST['district_name'] as $k=>$v){
					$sql = 'insert into gm_details (gm_id, district_id) values 
					("'.$inv2.'","'.$v.'")';
					//echo $sql;
					execute_query($sql);
						if(mysqli_error($db)){
							$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
						}
					}
				}
			
			}
		}
		else{
		$sql = 'insert into `gm` (gm_name, mobile_number, division_name, pwd, username, created_by, creation_time) values ("'.$_POST['gm_name'].'", "'.$_POST['mobile_number'].'", "'.$_POST['division_name'].'","'.$_POST['pwd'].'","'.$_POST['username'].'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
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
				foreach($_POST['district_name'] as $k=>$v){
					$sql = 'insert into gm_details (gm_id, district_id) values 
					("'.$inv.'","'.$v.'")';
					//echo $sql;
					execute_query($sql);
					if(mysqli_error($db)){
						$msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
					}
				}
			}
		}
	}
}

if(isset($_GET['id'])){
	$sql = 'select * from gm where sno='.$_GET['id'];
	$result = execute_query($sql);
	$row_edit = mysqli_fetch_array($result);
		
}

if(isset($_GET['del'])){
	$sql = 'delete from gm where sno='.$_GET['del'];
	$result_ado = execute_query($sql);

	$sql = 'delete from gm_details where gm_id= '.$_GET['del'];
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
														<label>GM Name</label>
														<input type="text" name="gm_name" id="gm_name" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($_GET['id'])){echo $row_edit['gm_name'];} ?>">
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
											
												<div class="row">
													<div class="col-sm-3 form-group">
														<label>मण्डल</label>
														<select name="division_name" id="division_name" tabindex="<?php echo $tab++; ?>"  class="form-control" onChange="fill_district(this.value);">
															<option value="">--Select--</option>
															<?php
															$sql = 'select * from master_division';
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
														<select name="district_name[]" id="district_name" tabindex="<?php echo $tab++; ?>"  class="form-control" multiple="multiple">
															<?php
																		if(isset($_GET['id'])){
																			$sql = 'select * from gm_details where gm_id="'.$_GET['id'].'"';
																			$result_detail = execute_query($sql);
																			$array = array();
																			$a=0;
																			while($row_detail = mysqli_fetch_assoc($result_detail)){
																				$array[] = $row_detail['district_id'];
																			}
																			$sql = 'select * from master_district';
																			$result_district = execute_query($sql);
																			while($row_district = mysqli_fetch_assoc($result_district)){
																				
																			
																			if(in_array($row_district['sno'], $array)){
																				echo '<option value="'.$row_district['sno'].'" ';
																				 echo ' selected="selected" ';
																				 echo '>'.$row_district['district_name'].'</option>';
																			}
																		}
																	}
																?>
														</select>
													</div>
													
												</div>
												<input type="submit" class="btn btn-info btn-fill pull-right" value="ADD GM" name="submit" id="submit" />
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
                                            <th>AR Name</th>
                                            <th>Mobile Number</th>
											<th>Username</th>
											<th>Password</th>
											<th>Division Name</th>
											<th>District Name</th>
											<th>ID</th>
											<th>Edit</th>
											<th>Delete</th>
                                            
                                        </tr></thead>
                                        <tbody>
                                           	<?php
											$i=1;
											$sql = 'select gm.sno as sno, gm.gm_name as gm_name, gm.mobile_number as mobile_number,username, pwd,  gm.division_name as division_name, gm.tehseel_name as tehseel_name, gm_details.gm_id as gm_id, gm_details.district_id as district_id from gm left join gm_details on gm.sno = gm_details.gm_id';
											//echo $sql;
											$result = execute_query($sql);
											
											while($row=mysqli_fetch_array($result)){
											
											$sql_division = 'select * from master_division where sno = "'.$row['division_name'].'"';
											$result_division = mysqli_fetch_array(execute_query($sql_division));
											
											$sql_district = 'select * from master_district where sno = "'.$row['district_id'].'"';
											//echo $sql_district.'</br>';
											$result_district = mysqli_fetch_array(execute_query($sql_district));
											
											
											?>
											<tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $row['gm_name']; ?></td>
												<td><?php echo $row['mobile_number']; ?></td>
												<td><?php echo $row['username']; ?></td>
												<td><?php echo $row['pwd']; ?></td>
												<td><?php echo $result_division['division_name']; ?></td>
												<td><?php echo $result_district['district_name']; ?></td>
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

<script>
	
	// $('select[multiple]').multiselect({

		// search: true,

		// selectAll: true

	// });
</script>

<?php		
page_footer_end();
?>
