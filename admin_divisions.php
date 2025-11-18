<?php
include("scripts/settings.php");
$msg='';
$tab=1;
if(isset($_POST['division_name'])) {
	if($_POST['division_name']==''){
		$msg .= '<h6 class="alert alert-danger">Blank Entry!</h6>';
	}
	if($msg==''){
		if($_POST['edit_sno']!=''){
			$sql = 'update master_division set 
			division_name="'.$_POST['division_name'].'",
			edited_by="'.$_SESSION['username'].'",
			edition_time="'.date("Y-m-d H:i:s").'"
			where sno='.$_POST['edit_sno'];
		}
		else{
			$sql = 'insert into master_division (division_name, created_by, creation_time) values ("'.$_POST['division_name'].'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';	
		}
		
		execute_query($sql);
		if(mysqli_error($db)){
			$msg .= '<h6 class="alert alert-danger">Error 1 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
			$msg .= '<h6 class="alert alert-danger">Error 1.</h6>';
		}
		if($msg==''){
			$msg .= '<h6 class="alert alert-success">Success.</h6>';
			$_POST['division_name']='';
			$_POST['edit_sno']='';
		}
	}
}
else{
	$_POST['division_name']='';
	$_POST['edit_sno']='';
}
if(isset($_GET['id'])){
	$sql = 'select * from master_division where sno='.$_GET['id'];
	$data = mysqli_fetch_assoc(execute_query($sql));
	$_POST['division_name']=$data['division_name'];
	$_POST['edit_sno']=$data['sno'];
	
}
if(isset($_GET['del'])){
	$sql = 'select * from master_district where division_id='.$_GET['del'];
	$data = execute_query($sql);
	if(mysqli_num_rows($data)!=0){
		$msg .= '<h6 class="alert alert-danger">Can not deleted. Underlying district exists</h6>';
	}
	else{
		$sql = 'delete from master_division where sno='.$_GET['del'];
		$data = execute_query($sql);
		$msg .= '<h6 class="alert alert-danger">Data deleted.</h6>';
	}
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
								<h4 class="card-title">Divisions</h4>
								<?php echo $msg; ?>
							</div>
							<div class="card-body">
								<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
									<div class="row">
										<div class="col-md-4 pr-1">
											<div class="form-group">
												<label>Division Name</label>
												<input type="text" name="division_name" id="division_name" tabindex="<?php echo $tab++; ?>" class="form-control" placeholder="Division Name" value="<?php echo $_POST['division_name']; ?>">
											</div>
										</div>										
									</div>
									<button type="submit" class="btn btn-info btn-fill pull-right">Create/Update Profile</button>
									<input type="hidden" name="edit_sno" id="edit_sno" value="<?php echo $_POST['edit_sno']; ?>">
									<div class="clearfix"></div>
								</form>
							</div>
						</div>
					</div>
				</div>				
				
				<div class="row">
                        <div class="col-md-12">
                            <div class="card strpied-tabled-with-hover">
                               
                                <div class="card-body table-full-width table-responsive">
								<table class="table table-hover table-striped">
									<thead>
										<?php
											$i=1;
											$sql = 'select * from master_division where 1=1';
											if(isset($_POST['submit'])){
												//print_r($_POST);
												
												if($_POST['div_name']!='ALL'){
													$sql .= ' and `sno` ="'.$_POST['div_name'].'"';
												}	
											}
											//echo $sql;
											$result_data = execute_query($sql);
											
											?>
										<tr>
											<th colspan="6">
											<?php
												include ('pagination/paginate.php'); //include of paginat page
												$total_results = mysqli_num_rows($result_data);
												$total_pages = ceil($total_results / $per_page);//total pages we going to have
												$tpages=$total_pages;
												if (isset($_GET['page'])) {
													$show_page = $_GET['page'];             //it will telles the current page
													if ($show_page > 0 && $show_page <= $total_pages) {
														$start = ($show_page - 1) * $per_page;
														$end = $start + $per_page;
													} else {
														// error - show first set of results
														$start = 0;              
														$end = $per_page;
													}
												} else {
													// if page isn't set, show first set of results
													$_GET['page'] = 1;
													$show_page = 1;
													$start = 0;
													$end = $per_page;
												}
												// display pagination
												$page = intval($_GET['page']);

												if ($page <= 0)
													$page = 1;


												$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . (isset($_GET['details'])?'&details=1':'');
												echo '<div class="pagination"><ul>';
												if ($total_pages > 1) {
													echo paginate($reload, $show_page, $total_pages);
												}
												echo "</ul></div>";
											?>
											</th>
											
												 
											
										</tr>
										
									</thead>
								</table>
								
								<div class="card-body">
									<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
										<div class="row">
											
											
											<div class="col-md-4 pr-1">
												<div class="form-group">
													<label>Division Name</label>
													<select name="div_name" id="div_name" tabindex="<?php echo $tab++; ?>" class="form-control">
													<option value="ALL">ALL</option>
														<?php
														$sql = 'select * from master_division';
														$result_divison = execute_query($sql);
														while($row_division = mysqli_fetch_assoc($result_divison)){
															echo '<option value="'.$row_division['sno'].'" ';														
															echo '>'.$row_division['division_name'].'</option>';
														}
														
														?>
													</select>
												</div>
											</div>
											<div class="col-md-4 pr-1">
												<div class="form-group">
												</div>
												<div class="form-group">
													<input type="submit" class="btn btn-info btn-fill pull-right" name="submit" value="Submit" >
												</div>
											</div>
											
											
										</div>
										
										
										<!--<button type="submit" class="btn btn-info btn-fill pull-right">Search</button>-->
										
										<div class="clearfix"></div>
									</form>
								</div>
								
								
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                            <th>S.No.</th>
                                            <th>Division Name</th>
                                            <th></th>
                                            <th></th>
                                        </tr></thead>
                                        <tbody>
                                           	<?php
											$i=1;
											$sql = 'select * from master_division';
											$result = execute_query($sql);
											for ($pgid = $start; $pgid < $end; $pgid++) {
											//print_r($row);
											if ($pgid == $total_results) {
												break;
											}
											mysqli_data_seek($result_data, $pgid);
											$row = mysqli_fetch_array($result_data);
											$i = $pgid+1;
											?>
											<tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $row['division_name']; ?></td>
                                                <td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?id='.$row['sno'];?>" alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit" aria-hidden="true"></span></td>
												<td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?del='.$row['sno'];?>" onclick="return confirm('Are you sure?');" style="color:#f00" alt="Delete"><span class="far fa-trash-alt" aria-hidden="true" data-toggle="tooltip" title="Delete"></span></a></td>
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

<?php		
page_footer_end();
?>
