<?php
include("scripts/settings.php");
$tab=1;
$msg='';
// echo $_SESSION['usertype'];
// echo 'somilllllllllllllllll';
if(isset($_POST['submit'])) {

    // sanitize inputs
    $adco_name = mysqli_real_escape_string($db, $_POST['adco_name']);
    $mobile_number = mysqli_real_escape_string($db, $_POST['mobile_number']);
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $pwd = mysqli_real_escape_string($db, $_POST['pwd']);
    $division_name = mysqli_real_escape_string($db, $_POST['division_name']);
    $district_name = mysqli_real_escape_string($db, $_POST['district_name']);
    $edit_sno = isset($_POST['edit_sno']) ? (int) $_POST['edit_sno'] : 0;

    $sql = '(SELECT sno, pwd, username, ar_name as full_name, mobile_number,"2" as navuser_type, "ar_dr"  as info FROM `ar_dr` where username="'.$username.'") union all
    (SELECT sno, pwd, username, ar_name as full_name, mobile_number, "3" as navuser_type, "ar" as info FROM `ar` where username="'.$username.'") 
        union all 
    (SELECT sno,pwd, username, ado_name as full_name, mobile_number, "4" as navuser_type, "ado" as info FROM `ado` where username="'.$username.'") 
    union all 
    (SELECT sno,pwd, username, adco_name as full_name, mobile_number, "5" as navuser_type, "adco" as info FROM `adco` where username="'.$username.'")';
    $result = execute_query($sql);

    $num_rows= mysqli_num_rows($result);

    if($adco_name==''){
        $msg .= '<li>Please Enter ADCO Name.</li>';
    }
    if($msg==''){
        if($edit_sno!=''){

            if($num_rows<=1){

                $sql = 'update adco set adco_name="'.$adco_name.'",
                mobile_number="'.$mobile_number.'",
                username="'.$username.'",
                pwd="'.$pwd.'",
                division_name="'.$division_name.'",
                district_name="'.$district_name.'"
                where sno='.$edit_sno.' and status=0';
                //echo $sql;
                $res = execute_query($sql);
                if ($res) {
                    $msg .= '<li>Update successful.</li>';

                    // remove existing details and re-insert
                    $sql = 'delete from adco_details where adco_id= "'.$edit_sno.'" ';
                    execute_query($sql);

                    $sql2 = 'select * from adco where sno='.$edit_sno;
                    $result2 = mysqli_fetch_array(execute_query($sql2));
                    $inv2 = $result2['sno'];
                    if($inv2!=''){
                        if(isset($_POST['tehseel_name']) && is_array($_POST['tehseel_name'])){
                            foreach($_POST['tehseel_name'] as $k=>$v){
                                $teh = (int)$v;
                                $sql = 'insert into adco_details (adco_id, tehseel_id) values 
                                ("'.$inv2.'","'.$teh.'")';
                                //echo $sql;
                                execute_query($sql);
                                if(mysqli_error($db)){
                                    $msg .= '<li>Error # 2 : '.mysqli_error($db).' >> '.$sql.'</li>';
                                }
                            }
                        }
                    }

                } else {
                    $msg .= '<li>Update failed.</li>';
                }
            }else{
                $msg .= '<li>Username already taken Enter Unique Username.</li>';
            }
        }
        else{
            if($num_rows==0){

                // insert with status = 0 (active)
                $sql = 'insert into `adco` (adco_name,pwd, username, mobile_number, division_name, district_name, created_by, creation_time, status) values ("'.$adco_name.'", "'.$pwd.'","'.$username.'", "'.$mobile_number.'", "'.$division_name.'", "'.$district_name.'", "'.$_SESSION['username'].'", "'.date("Y-m-d H:i:s").'", 0)';
                execute_query($sql);

                if(mysqli_error($db)){
                    $msg .= '<li>Error # 1 : '.mysqli_error($db).' >> '.$sql.'</li>';
                    $inv=0;
                }
                else{
                    $inv = mysqli_insert_id($db);
                }
                if($inv!=0){
                    if(isset($_POST['tehseel_name']) && is_array($_POST['tehseel_name'])){
                        foreach($_POST['tehseel_name'] as $k=>$v){
                            $teh = (int)$v;
                            $sql = 'insert into adco_details (adco_id, tehseel_id) values 
                            ("'.$inv.'","'.$teh.'")';
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

    }

}

if(isset($_GET['id'])){
    $id = (int) $_GET['id'];
    // fetch only if active (status = 0)
    $sql = 'select * from adco where sno='.$id.' and status=0';
    $result = execute_query($sql);
    $row_edit = mysqli_fetch_array($result);
}

// Soft-delete route — only S-Admin (user_type == 1) can soft delete
if (isset($_GET['del'])) {
    $del_id = (int) $_GET['del']; // sanitize as integer

    if ($_SESSION['usertype'] !== 'sadmin') {
        $msg .= '<li>You do not have permission to delete records.</li>';
    } else {
        // Check record exists and is currently active
        $check = execute_query("SELECT sno FROM adco WHERE sno = $del_id AND status = 0");
        if (mysqli_num_rows($check) > 0) {
            // Soft delete: set status = 1
            $sql = "UPDATE adco SET status = 1 WHERE sno = $del_id";
            execute_query($sql);

            if (mysqli_error($db)) {
                $msg .= '<li>Error while deleting: ' . mysqli_error($db) . '</li>';
            } else {
                $msg .= '<li>ADCO record soft-deleted successfully.</li>';
            }
        } else {
            $msg .= '<li>Invalid record or already deleted — nothing changed.</li>';
        }
    }
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
                                                        <label>ADCO Name</label>
                                                        <input type="text" name="adco_name" id="adco_name" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($row_edit['adco_name'])){echo htmlspecialchars($row_edit['adco_name']);} ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>Mobile Number</label>
                                                        <input type="text" name="mobile_number" id="mobile_number" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($row_edit['mobile_number'])){echo htmlspecialchars($row_edit['mobile_number']);} ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>Username</label>
                                                        <input type="text" name="username" id="username" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($row_edit['username'])){echo htmlspecialchars($row_edit['username']);} ?>">
                                                    </div>
                                                    <div class="col-sm-3 form-group">
                                                        <label>Password</label>
                                                        <input type="text" name="pwd" id="pwd" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if(isset($row_edit['pwd'])){echo htmlspecialchars($row_edit['pwd']);} ?>">
                                                    </div>
                                                </div>
                                            
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
                                                                if(isset($row_edit['division_name'])){
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
                                                        <option value="<?php  if(isset($row_edit['district_name'])){echo $row_edit['district_name'];} ?>">
                                                                <?php
                                                                    if($_SESSION['user_type']=='ar'){
                                                                    $sql = 'select * from master_district where  sno in (' . implode(",", $_SESSION['district_id']) . ')';
                                                                    }else{
                                                                        $sql = 'select * from master_district';
                                                                    }
                                                                    $result_district = execute_query($sql);
                                                                    while($row_district = mysqli_fetch_assoc($result_district)){
                                                                        echo '<option value="'.$row_district['sno'].'" ';
                                                                        if(isset($row_edit['district_name'])){
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
                                                        <select name="tehseel_name[]" id="tehseel_name" tabindex="<?php echo $tab++; ?>"  class="form-control" multiple="multiple">
														<?php
															if(isset($_GET['id'])){
																$sql = 'select * from adco_details where adco_id="'.(int)$_GET['id'].'"';
																$result_detail = execute_query($sql);
																$array = array();
																while($row_detail = mysqli_fetch_assoc($result_detail)){
																	$array[] = $row_detail['tehseel_id'];
																}
																$sql = 'select * from master_tehseel';
																$result_district = execute_query($sql);
																while($row_district = mysqli_fetch_assoc($result_district)){
																	if(in_array($row_district['sno'], $array)){
																		echo '<option value="'.$row_district['sno'].'" selected="selected">'.$row_district['tehseel_name'].'</option>';
																	} else {
																		echo '<option value="'.$row_district['sno'].'">'.$row_district['tehseel_name'].'</option>';
																	}
																}
															} else {
																// not editing: show all tehseels as options
																$sql = 'select * from master_tehseel';
																$result_district = execute_query($sql);
																while($row_district = mysqli_fetch_assoc($result_district)){
																	echo '<option value="'.$row_district['sno'].'">'.$row_district['tehseel_name'].'</option>';
																}
															}
														?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <input type="submit" class="btn btn-info btn-fill pull-right" value="ADD ADCO" name="submit" id="submit" />
                                                <input type="hidden" name="edit_sno" value="<?php if(isset($_GET['id'])){echo (int)$_GET['id'];}?>" />
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
                                            <th>ADCO Name</th>
                                            <th>Mobile Number</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Division Name</th>
                                            <th>District Name</th>
                                            <th>Tehseel Name</th>
                                            <th>ID</th>
                                            <th>Edit</th>
                                            <?php if ($_SESSION['usertype'] === 'sadmin') { ?>
                                            <th>Delete</th>
                                            <?php } ?>
                                        </tr></thead>
                                        <tbody>
                                            <?php
                                            $i=1;

                                            if($_SESSION['user_type']=='ar'){
                                                // only active records
                                                $sql = 'select adco.sno as sno, username, pwd, adco.adco_name as adco_name, adco.mobile_number as mobile_number, adco.district_name as district_name, adco.division_name as division_name,  adco_details.adco_id as adco_id, adco_details.tehseel_id as tehseel_id from adco left join adco_details on adco.sno = adco_details.adco_id where district_name in ('.implode(",", $_SESSION['district_id']).') and adco.status = 0';
                                            }else{
                                                $sql = 'select adco.sno as sno, username, pwd, adco.adco_name as adco_name, adco.mobile_number as mobile_number, adco.district_name as district_name, adco.division_name as division_name,  adco_details.adco_id as adco_id, adco_details.tehseel_id as tehseel_id from adco left join adco_details on adco.sno = adco_details.adco_id where adco.status = 0';
                                            }

                                            // echo $sql;
                                            $result = execute_query($sql);

                                            while($row=mysqli_fetch_array($result)){

                                            $sql_district = 'select * from master_district where sno = "'.$row['district_name'].'"';
                                            //echo $sql_district.'</br>';
                                            $result_district = mysqli_fetch_array(execute_query($sql_district));

                                            $sql_division = 'select * from master_division where sno = "'.$row['division_name'].'"';
                                            $result_division = mysqli_fetch_array(execute_query($sql_division));

                                            $sql_tehseel = 'select * from master_tehseel where sno = "'.$row['tehseel_id'].'"';
                                            $result_tehseel = mysqli_fetch_array(execute_query($sql_tehseel));

                                            ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo htmlspecialchars($row['adco_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['pwd']); ?></td>
                                                <td><?php echo htmlspecialchars($result_division['division_name']); ?></td>
                                                <td><?php echo htmlspecialchars($result_district['district_name']); ?></td>
                                                <td><?php echo htmlspecialchars($result_tehseel['tehseel_name']); ?></td>
                                                <td><?php echo $row['sno']; ?></td>
                                                <td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?id='.$row['sno'];?>" alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit" aria-hidden="true"></span></a></td>
                                                <?php if (isset($_SESSION['user_type']) && $_SESSION['usertype'] === 'sadmin') { ?>
                                                <td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'].'?del='.$row['sno'];?>" onclick="return confirm('Are you sure you want to delete (soft-delete) this record?');" style="color:#f00" alt="Delete"><span class="far fa-trash-alt" aria-hidden="true" data-toggle="tooltip" title="Delete"></span></a></td>
                                                <?php } ?>
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
