<?php
include("scripts/settings.php");
$tab = 1;
$msg = '';
// echo $sql = 'select gm_details.sno as sno, gm_name, mobile_number, gm_id, gm_details.district_id as district_id, division_id, master_district.district_name as district_name, master_division.division_name as division_name from gm_details left join master_district on master_district.sno = gm_details.district_id left join master_division on master_division.sno = master_district.division_id left join gm on gm.sno = gm_id where gm_details.gm_id='.$_SESSION['user_id'];
// $result_gm_district = execute_query($sql);
// $district_array = array();
// $_SESSION['district_id'] = array();
// while($row_gm_district = mysqli_fetch_assoc($result_gm_district)){
	// $district_array[] = $row_gm_district['district_id'];
	// $_SESSION['district_id'][] = $row_gm_district['district_id'];
	// $division = $row_gm_district['division_id'];
// }
//print_r($district_array);

if (isset($_POST['submit'])) {
	
	$sql = '(SELECT sno, pwd, username, ar_name as full_name, mobile_number,"2" as navuser_type, "ar_dr"  as info FROM `ar_dr` where username="'.$_POST['username'].'") union all
	
	(SELECT sno, pwd, username, ar_name as full_name, mobile_number, "3" as navuser_type, "ar" as info FROM `ar` where username="'.$_POST['username'].'") 
		union all 
	(SELECT sno,pwd, username, ado_name as full_name, mobile_number, "4" as navuser_type, "ado" as info FROM `ado` where username="'.$_POST['username'].'") 
		union all 
	(SELECT sno,pwd, username, adco_name as full_name, mobile_number, "5" as navuser_type, "adco" as info FROM `adco` where username="'.$_POST['username'].'")
	union all
		(SELECT sno,pwd, username, gm_name as full_name, mobile_number, "6" as navuser_type, "gm" as info FROM `gm` where username="'.$_POST['username'].'")
		union all
		(SELECT sno,pwd, username, bm_name as full_name, mobile_number, "7" as navuser_type, "bm" as info FROM `bm` where username="'.$_POST['username'].'")';
	$result = execute_query($sql);
	
	$num_rows= mysqli_num_rows($result);
	$result=mysqli_query($db,$sql);
	$num_rows= mysqli_num_rows($result);
	
    if (empty($_POST['bm_name'])) {
        $msg .= '<li>Please Enter BM Name.</li>';
    }

    $username = $_POST['username'];
    $edit_sno = isset($_POST['edit_sno']) ? $_POST['edit_sno'] : '';
    $sql = 'SELECT * FROM bm WHERE username = "' . $username . '"';
    
    if (!empty($edit_sno)) {
        $sql .= ' AND sno != ' . $edit_sno;
    }
    
    $result = execute_query($sql);
    if (mysqli_num_rows($result) > 0) {
        $msg .= '<li>Username already exists.</li>';
    }

    if (empty($msg)) {
        if (!empty($edit_sno)) {
			if($num_rows<=1){
            
				$sql = 'UPDATE bm SET 
					bm_name="' . $_POST['bm_name'] . '",
					mobile_number="' . $_POST['mobile_number'] . '",
					username="' . $username . '",
					pwd="' . $_POST['pwd'] . '",
					division_name="' . $_POST['division_name'] . '"
					WHERE sno=' . $edit_sno;
					$res = execute_query($sql);
					if ($res) {
						$msg .= '<li>Update successful.</li>';
						
						$sql = 'DELETE FROM bm_details WHERE bm_id="' . $edit_sno . '"';
						execute_query($sql);

						foreach ($_POST['district_name'] as $district_id) {
							$sql = 'INSERT INTO bm_details (bm_id, district_id) VALUES ("' . $edit_sno . '", "' . $district_id . '")';
							execute_query($sql);

							if (mysqli_error($db)) {
								$msg .= '<li>Error # 2: ' . mysqli_error($db) . ' >> ' . $sql . '</li>';
							}
						}
						$sql = 'DELETE FROM bm_society WHERE bm_society_id="' . $edit_sno . '"';
						execute_query($sql);
						
						foreach ($_POST['society_name'] as $society_id) {
							$sql = 'INSERT INTO bm_society (bm_society_id, society_id) VALUES ("' . $edit_sno . '", "' . $society_id . '")';
							execute_query($sql);
						}
					}
			}else{
					$msg .= '<li>Username already taken Enter Unique Username.</li>';
				}
        } else {
			if($num_rows==0){
            
				$sql = 'INSERT INTO bm (bm_name, mobile_number, division_name, pwd, username, created_by, creation_time) VALUES ("' . $_POST['bm_name'] . '", "' . $_POST['mobile_number'] . '", "' . $_POST['division_name'] . '", "' . $_POST['pwd'] . '", "' . $username . '", "' . $_SESSION['username'] . '", "' . date("Y-m-d H:i:s") . '")';
				execute_query($sql);

				if (mysqli_error($db)) {
					$msg .= '<li>Error # 1: ' . mysqli_error($db) . ' >> ' . $sql . '</li>';
					$inv = 0;
				} else {
					$inv = mysqli_insert_id($db);
				}

				if ($inv != 0) {
					foreach ($_POST['district_name'] as $district_id) {
						$sql = 'INSERT INTO bm_details (bm_id, district_id) VALUES ("' . $inv . '", "' . $district_id . '")';
						execute_query($sql);

						if (mysqli_error($db)) {
							$msg .= '<li>Error # 2: ' . mysqli_error($db) . ' >> ' . $sql . '</li>';
						}
					}
					foreach ($_POST['society_name'] as $society_id) {
						$sql = 'INSERT INTO bm_society (bm_society_id, society_id) VALUES ("' . $inv . '", "' . $society_id . '")';
						execute_query($sql);
					}
				}
            $msg .= '<li>Username has been generated</li>';
			}else{
					$msg .= '<li>Username already taken Enter Unique Username.</li>';
				}
        }
    }
}

if (isset($_GET['id'])) {
    $sql = 'SELECT * FROM bm WHERE sno=' . $_GET['id'];
    $result = execute_query($sql);
    $row_edit = mysqli_fetch_array($result);
	
}

if (isset($_GET['del'])) {
    $sql = 'DELETE FROM bm WHERE sno=' . $_GET['del'];
    execute_query($sql);

    $sql = 'DELETE FROM bm_details WHERE bm_id=' . $_GET['del'];
    execute_query($sql);
	
	$sql = 'DELETE FROM bm_society WHERE bm_society_id=' . $_GET['del'];
    execute_query($sql);
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
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
						
						
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label>BM Name</label>
                                        <input type="text" name="bm_name" id="bm_name" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo isset($row_edit['bm_name']) ? $row_edit['bm_name'] : ''; ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Mobile Number</label>
                                        <input type="text" name="mobile_number" id="mobile_number" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo isset($row_edit['mobile_number']) ? $row_edit['mobile_number'] : ''; ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" id="username" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo isset($row_edit['username']) ? $row_edit['username'] : ''; ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Password</label>
                                        <input type="text" name="pwd" id="pwd" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo isset($row_edit['pwd']) ? $row_edit['pwd'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label>मण्डल</label>
                                        <select name="division_name" id="division_name" tabindex="<?php echo $tab++; ?>" class="form-control" onChange="fill_district(this.value);">
                                            <?php
                                            if($_SESSION['user_type']=='gm'){
												$sql = 'select * from master_division where  sno in (' . implode(",", $_SESSION['division_id']) . ')';
											}else{
												$sql = 'select * from master_division';
											}
                                            $result_division = execute_query($sql);
                                            while ($row_division = mysqli_fetch_assoc($result_division)) {
                                                echo '<option value="' . $row_division['sno'] . '" ' . (isset($row_edit['division_name']) && $row_edit['division_name'] == $row_division['sno'] ? 'selected' : '') . '>' . $row_division['division_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>जनपद</label>
                                        <select name="district_name[]" id="district_name" tabindex="<?php echo $tab++; ?>" class="form-control" onChange="fill_society_dis(this.value);">
                                            <option value="">--Select--</option>
                                            <?php
                                            if($_SESSION['user_type']=='gm'){
												$sql = 'select * from master_district where  sno in (' . implode(",", $_SESSION['district_id']) . ')';
											}else{
												$sql = 'select * from master_district';
											}
                                            $result_district = execute_query($sql);
                                            while ($row_district = mysqli_fetch_assoc($result_district)) {
                                                echo '<option value="' . $row_district['sno'] . '" ' . (isset($row_edit['district_name']) && $row_edit['district_name'] == $row_district['sno'] ? 'selected' : '') . '>' . $row_district['district_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 form-group">
                                        <label>समितियाँ</label>
                                        <select name="society_name[]" id="society_name_dis" tabindex="<?php echo $tab++; ?>" class="form-control" multiple="multiple">
                                            <?php
                                            if (isset($_GET['id'])) {
                                                $sql = 'SELECT * FROM bm_details WHERE bm_id="' . $_GET['id'] . '"';
                                                $result_detail = execute_query($sql);
                                                $array = [];
                                                while ($row_detail = mysqli_fetch_assoc($result_detail)) {
                                                    $array[] = $row_detail['society_id'];
                                                }
                                                $sql = 'SELECT * FROM test2';
                                                $result_society = execute_query($sql);
                                                // $i =1;
                                                while ($row_society = mysqli_fetch_assoc($result_society)) {
                                                    echo '<option value="' . $row_society['sno'] . '" ' . (in_array($row_society['sno'], $array) ? 'selected' : '') . '>' . $row_society['society_name'] . '</option>';
                                                    // $i++;
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="edit_sno" value="<?php echo isset($row_edit['sno']) ? $row_edit['sno'] : ''; ?>">
                                    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                                </div>
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
                            <th>BM Name</th>
                            <th>Mobile Number</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Division Name</th>
                            <th>District Name</th>
                            <th>Alloted Society</th>
                            <th>Society List</th>
                            <!-- <th>Edit</th> -->
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i=1;
						if($_SESSION['user_type']=='gm'){
                        $sql = 'select bm.sno as sno, bm.bm_name as bm_name, bm.mobile_number as mobile_number,username, pwd, bm.division_name as division_name, bm.tehseel_name as tehseel_name, bm_details.bm_id as bm_id, bm_details.district_id as district_id from bm left join bm_details on bm.sno = bm_details.bm_id where bm_details.district_id in ('.implode(",", $_SESSION['district_id']).')';
						}else{
							$sql = 'select bm.sno as sno, bm.bm_name as bm_name, bm.mobile_number as mobile_number,username, pwd, bm.division_name as division_name, bm.tehseel_name as tehseel_name, bm_details.bm_id as bm_id, bm_details.district_id as district_id from bm left join bm_details on bm.sno = bm_details.bm_id';
						}
                        //echo $sql;
                        $result = execute_query($sql);
                        
                        while($row=mysqli_fetch_array($result)){
                        
                            $sql_division = 'select * from master_division where sno = "'.$row['division_name'].'"';
                            $result_division = mysqli_fetch_array(execute_query($sql_division));
                            
                            $sql_district = 'select * from master_district where sno = "'.$row['district_id'].'"';
                            //echo $sql_district.'</br>';
                            $result_district = mysqli_fetch_array(execute_query($sql_district));
                            
                            $sql = 'select count(*) as count from bm_society where bm_society_id = "'. $row['sno'] .'"';
                            $total = mysqli_fetch_assoc(execute_query($sql));
                            
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $row['bm_name']; ?></td>
                                <td><?php echo $row['mobile_number']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['pwd']; ?></td>
                                <td><?php echo $result_division['division_name']; ?></td>
                                <td><?php echo $result_district['district_name']; ?></td>
                                <td><?php echo $total['count']; ?></td>
                                <td class="text-center"><a href="bm_society_details.php?id=<?php echo $row['sno'];?>" target="_blank"alt="View" data-toggle="tooltip" title="View"><span class="far fa-eye" aria-hidden="true"></span></td>
                                <!-- <td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?id='.$row['sno'];?>" alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit" aria-hidden="true"></span></td> -->
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
<script>
<?php
	if(isset($_GET['id'])){
?>
	$(document).ready(function() {
		fill_district(<?php echo $row_edit['division_name']; ?>, <?php echo $row_edit['district_name']; ?>);
		
		
		
	}); 
<?php
	}
?>
</script>
<?php
page_footer_start();
?>
<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<?php
page_footer_end();
?>
