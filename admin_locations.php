<?php
include("scripts/settings.php");
$msg='';
if(isset($_POST['submit'])) {
	if($_POST['village']!=''){
		$sql = 'select * from location_village where location_name="'.$_POST['village'].'" and parent="'.$_POST['tehsil'].'"';
		$result = execute_query($sql);
		if(mysqli_num_rows($result)==0){
			$sql = 'insert into location_village (location_name, parent, created_by, creation_time) values("'.$_POST['village'].'", "'.$_POST['tehsil'].'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
			execute_query($sql);
			if(mysqli_error($db)){
				$msg .= '<h4 class="alert alert-danger">Failed! '.mysqli_error($db).' >> '.$sql.'</h4>';
			}
			else{
				$msg .= '<h4 class="alert alert-success">Success</h4>';
			}
		}
		else{
			$msg .= '<h4 class="alert alert-danger">Duplicate Entry!</h4>';
		}
	}
	else{
		$msg .= '<h4 class="alert alert-danger">Blank Entry!</h4>';
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
								<h4 class="card-title">Locations</h4>
								<?php echo $msg; ?>
							</div>
							<div class="card-body">
								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
									<div class="row">
										<div class="col-md-3 pr-1">
											<div class="form-group">
												<label>Tehsil</label>
												<select name="tehsil" id="tehsil" class="form-control">
													<?php
													$sql = 'select * from location_tehsil';
													$result = execute_query($sql);
													while($row = mysqli_fetch_assoc($result)){
														echo '<option value="'.$row['sno'].'">'.$row['location_name'].' ('.$row['location_name_english'].')</option>';
													}
													?>
													</select>
											</div>
										</div>
										<div class="col-md-3 pr-1">
											<div class="form-group">
												<label for="exampleInputEmail1">ग्राम का नाम (हिंदी में)</label>
												<input type="text" name="village" id="village" class="form-control" placeholder="ग्राम का नाम (हिंदी में)">
											</div>
										</div>
										<div class="col-md-3 pr-1">
											<div class="form-group">
												<label for="exampleInputEmail1">Village Name (In English)</label>
												<input type="text" name="village_english" id="village_english" class="form-control" placeholder="Village Name (In English)">
											</div>
										</div>
										<div class="col-md-3 pr-1">
											<div class="form-group">
												<button type="submit" name="submit" class="btn btn-info btn-fill pull-right">Create Location</button>
											</div>
										</div>
									</div>
									
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
                                            <th>Tehsil Name</th>
                                            <th>ग्राम का नाम</th>
                                            <th>Village Name</th>                                            
                                            <th></th>
                                            <th></th>
                                        </tr></thead>
                                        <tbody>
                                           	<?php
											$i=1;
											$sql = 'select location_village.sno as sno, location_tehsil.location_name as tehsil_name, location_tehsil.location_name_english as tehsil_name_english, location_village.location_name, location_village.location_name_english as location_name_english from location_village left join location_tehsil on location_tehsil.sno = location_village.parent';
											$result = execute_query($sql);
											while($row = mysqli_fetch_assoc($result)){
											?>
											<tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $row['tehsil_name'].' ('.$row['tehsil_name_english'].')'; ?></td>
                                                <td><?php echo $row['location_name']; ?></td>
                                                <td><?php echo $row['location_name_english']; ?></td>
                                                <!--<td><a href="scripts/printing_sale.php?inv='.$row['sno'].'" target="_blank"><span class="fa fa-eye" aria-hidden="true" data-toggle="tooltip" title="View Invoice"></span></a></td>-->
												<td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?id='.$row['sno'].'" target="_blank" alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit" aria-hidden="true"></span></td>
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
	<!--  Charts Plugin -->
	<script src="js/chartist.min.js"></script>

<?php		
page_footer_end();
?>
