<?php
include("scripts/settings.php");
$msg='';
$tab=1;
if(isset($_POST['aadhaar'])) {
	if($_POST['tehsil']==''){
		$msg .= '<h6 class="alert alert-danger">Select Tehsil</h6>';
	}
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
		$sql = 'select * from enquiry_customer where adhar_no="'.$_POST['aadhaar'].'"';
		$result = execute_query($sql);
		if(mysqli_num_rows($result)==0){
			$sql = 'insert into enquiry_customer (cus_name, fname, address, mobile, adhar_no, add_2, city, created_by, creation_time) values ("'.$_POST['full_name'].'", "'.$_POST['father_name'].'", "'.$_POST['address'].'", "'.$_POST['mobile'].'", "'.$_POST['aadhaar'].'", "'.$_POST['tehsil'].'", "'.$_POST['villages'].'",  "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'")';
			execute_query($sql);
			if(mysqli_error($db)){
				$msg .= '<h6 class="alert alert-danger">Error 1 # : '.mysqli_error($db).' >> '.$sql.'</h6>';
				$msg .= '<h6 class="alert alert-danger">Error 1.</h6>';
			}
			else{
				$insert_id = mysqli_insert_id($db);
				
			}
			
			$id = $insert_id;
			if($msg==''){
				$msg .= '<h6 class="alert alert-success">Success.</h6>';
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
else{
	$_POST['aadhaar']='';
	$_POST['mobile']='';
	$_POST['email']='';
	$_POST['full_name']='';
	$_POST['father_name']='';
	$_POST['address']='';
	$_POST['expiry_date'] = date("Y-m-d");
}
?>

<?php
page_header_start();
page_header_end();
page_sidebar();

?>
   				<div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Beneficiary</h4>
                                    <?php echo $msg; ?>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
										<div class="row">
                                            <div class="col-md-3 pr-1">
                                                <div class="form-group">
                                                    <label>Tehsil</label>
                                                    <select name="tehsil" id="tehsil" tabindex="<?php echo $tab++; ?>" class="form-control" onChange="update_locations();">
                                                    	<option value=""></option>
                                                    	<?php
														$sql = 'select * from location_tehsil';
														$result_tehsil = execute_query($sql);
														while($row_tehsil = mysqli_fetch_assoc($result_tehsil)){
															echo '<option value="'.$row_tehsil['sno'].'">'.$row_tehsil['location_name'].'</option>';
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
                                        <div class="clearfix"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-user">
                                <div class="card-image">
                                    <img src="https://ununsplash.imgix.net/photo-1431578500526-4d9613015464?fit=crop&amp;fm=jpg&amp;h=300&amp;q=75&amp;w=400" alt="...">
                                </div>
                                <div class="card-body">
                                    <div class="author">
                                        <a href="#">
                                            <img class="avatar border-gray" src="../assets/img/faces/face-3.jpg" alt="...">
                                            <h5 class="title">Mike Andrew</h5>
                                        </a>
                                        <p class="description">
                                            michael24
                                        </p>
                                    </div>
                                    <p class="description text-center">
                                        "Lamborghini Mercy
                                        <br> Your chick she so thirsty
                                        <br> I'm in that two seat Lambo"
                                    </p>
                                </div>
                                <hr>
                                <div class="button-container mr-auto ml-auto">
                                    <button href="#" class="btn btn-simple btn-link btn-icon">
                                        <i class="fa fa-facebook-square"></i>
                                    </button>
                                    <button href="#" class="btn btn-simple btn-link btn-icon">
                                        <i class="fa fa-twitter"></i>
                                    </button>
                                    <button href="#" class="btn btn-simple btn-link btn-icon">
                                        <i class="fa fa-google-plus-square"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
				
				
				<div class="row">
                        <div class="col-md-12">
                            <div class="card strpied-tabled-with-hover">
                                <div class="card-header ">
                                    <h4 class="card-title">Beneficiary</h4>
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
                                            <th>Tehsil</th>
                                            <th>Village</th>
                                            <th></th>
                                            <th></th>
                                        </tr></thead>
                                        <tbody>
                                           	<?php
											$i=1;
											$sql = 'select enquiry_customer.sno as sno, cus_name, fname, mobile, location_tehsil.location_name as tehsil, location_village.location_name as village from enquiry_customer left join location_tehsil on location_tehsil.sno = add_2 left join location_village on location_village.sno = city';
											$result = execute_query($sql);
											while($row = mysqli_fetch_assoc($result)){
											?>
											<tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $row['cus_name']; ?></td>
                                                <td><?php echo $row['fname']; ?></td>
                                                <td><?php echo $row['mobile']; ?></td>
												<td><?php echo $row['tehsil']; ?></td>
												<td><?php echo $row['village']; ?></td>
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
		function update_locations(){
			var tehsil = $("#tehsil").val();
			$.ajax({
				url: "scripts/ajax.php?id=villages&term="+tehsil,
				dataType:"json"
			})
			.done(function( data ) {

				var txt = '<label>Villages</label><select name="villages" id="villages" class="form-control">';
				$.each(data, function(k, value){
					txt += '<option value="'+value.id+'">'+value.location_name+'</option>';
				});
				txt += '</select>';
				console.log(txt);
				$("#villages_group").html(txt);
				$("#villages_group").show();
			});
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
		
    </script>
	<!--  Charts Plugin -->
	<script src="js/chartist.min.js"></script>

<?php		
page_footer_end();
?>
