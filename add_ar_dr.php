<?php
include("scripts/settings.php");
$tab = 1;
$msg = '';

if (isset($_POST['submit'])) {
    if ($_POST['ar_name'] == '') {
        $msg .= '<li>Please Enter AR Name.</li>';
    }
    if ($msg == '') {
        if ($_POST['edit_sno'] != '') {
            $sql = 'UPDATE ar_dr SET ar_name="' . $_POST['ar_name'] . '",
            mobile_number="' . $_POST['mobile_number'] . '",
            username="' . $_POST['username'] . '",
            pwd="' . $_POST['pwd'] . '",
            division_name="' . $_POST['division_name'] . '"
            WHERE sno=' . $_POST['edit_sno'];
            $res = execute_query($sql);
            if ($res) {
                $msg .= '<li>Update successful.</li>';

                $sql = 'DELETE FROM ar_dr_details WHERE ar_id= "' . $_POST['edit_sno'] . '" ';
                execute_query($sql);
                $sql2 = 'SELECT * FROM ar_dr WHERE sno=' . $_POST['edit_sno'];
                $result2 = mysqli_fetch_array(execute_query($sql2));
                $inv2 = $result2['sno'];
                if ($inv2 != '' && isset($_POST['district_name']) && is_array($_POST['district_name'])) {
                    foreach ($_POST['district_name'] as $k => $v) {
                        $sql = 'INSERT INTO ar_dr_details (ar_id, district_id) VALUES 
                        ("' . $inv2 . '","' . $v . '")';
                        execute_query($sql);
                        if (mysqli_error($db)) {
                            $msg .= '<li>Error # 2 : ' . mysqli_error($db) . ' >> ' . $sql . '</li>';
                        }
                    }
                }
            }
        } else {
            $sql = 'INSERT INTO ar_dr (ar_name, mobile_number, division_name, pwd, username, created_by, creation_time) VALUES 
            ("' . $_POST['ar_name'] . '", "' . $_POST['mobile_number'] . '", "' . $_POST['division_name'] . '", "' . $_POST['pwd'] . '","' . $_POST['username'] . '", "' . $_SESSION['username'] . '", "' . date("Y-m-d H:i:s") . '")';
            execute_query($sql);
            if (mysqli_error($db)) {
                $msg .= '<li>Error # 1 : ' . mysqli_error($db) . ' >> ' . $sql . '</li>';
                $inv = 0;
            } else {
                $inv = mysqli_insert_id($db);
            }
            if ($inv != 0 && isset($_POST['district_name']) && is_array($_POST['district_name'])) {
                foreach ($_POST['district_name'] as $k => $v) {
                    $sql = 'INSERT INTO ar_dr_details (ar_id, district_id) VALUES ("' . $inv . '","' . $v . '")';
                    execute_query($sql);
                    if (mysqli_error($db)) {
                        $msg .= '<li>Error # 2 : ' . mysqli_error($db) . ' >> ' . $sql . '</li>';
                    }
                }
            }
        }
    }
}

if (isset($_GET['id'])) {
    $sql = 'SELECT * FROM ar_dr WHERE sno=' . $_GET['id'];
    $result = execute_query($sql);
    $row_edit = mysqli_fetch_array($result);
}

if (isset($_GET['del'])) {
    $sql = 'DELETE FROM ar_dr WHERE sno=' . $_GET['del'];
    execute_query($sql);

    $sql = 'DELETE FROM ar_dr_details WHERE ar_id= ' . $_GET['del'];
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
                                        <label>AC &amp; DR Name</label>
                                        <input type="text" name="ar_name" id="ar_name" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php if (isset($_GET['id'])) { echo $row_edit['ar_name']; } ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Mobile Number</label>
                                        <input type="text" name="mobile_number" id="mobile_number" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php if (isset($_GET['id'])) { echo $row_edit['mobile_number']; } ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" id="username" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if (isset($_GET['id'])) { echo $row_edit['username']; } ?>">
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Password</label>
                                        <input type="text" name="pwd" id="pwd" tabindex="<?php echo $tab++; ?>"  class="form-control" value="<?php if (isset($_GET['id'])) { echo $row_edit['pwd']; } ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label>मण्डल</label>
                                        <select name="division_name" id="division_name" tabindex="<?php echo $tab++; ?>" class="form-control">
                                            <option value="">--Select--</option>
                                            <?php
                                            $sql = 'SELECT * FROM master_division';
                                            $result_division = execute_query($sql);
                                            while ($row_division = mysqli_fetch_assoc($result_division)) {
                                                echo '<option value="' . $row_division['sno'] . '" ';
                                                if (isset($_GET['id']) && $row_edit['division_name'] == $row_division['sno']) {
                                                    echo ' selected="selected" ';
                                                }
                                                echo '>' . $row_division['division_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-info btn-fill pull-right" value="ADD DR" name="submit" id="submit" />
                                <input type="hidden" name="edit_sno" value="<?php if (isset($_GET['id'])) { echo $_GET['id']; } ?>" />
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
                            <th>DR Name</th>
                            <th>Mobile Number</th>
                            <th>Division Name</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>ID</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $sql = 'SELECT ar_dr.sno AS sno, ar_dr.ar_name AS ar_name, ar_dr.mobile_number AS mobile_number, ar_dr.division_name AS division_name, ar_dr.username AS username, ar_dr.pwd AS pwd FROM ar_dr';
                        $result = execute_query($sql);
                        while ($row = mysqli_fetch_array($result)) {
                            $sql_division = 'SELECT division_name FROM master_division WHERE sno = "' . $row['division_name'] . '"';
                            $result_division = mysqli_fetch_array(execute_query($sql_division));
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $row['ar_name']; ?></td>
                                <td><?php echo $row['mobile_number']; ?></td>
                                <td><?php echo $result_division['division_name']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['pwd']; ?></td>
                                <td><?php echo $row['sno']; ?></td>
                                <td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $row['sno']; ?>" alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit" aria-hidden="true"></span></a></td>
                                <td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'] . '?del=' . $row['sno']; ?>" onclick="return confirm('Are you sure?');" style="color:#f00" alt="Delete"><span class="far fa-trash-alt" aria-hidden="true" data-toggle="tooltip" title="Delete"></span></a></td>
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
