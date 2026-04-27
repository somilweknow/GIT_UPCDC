<?php
include("scripts/settings.php");
error_reporting(E_ALL);
ini_set("display_errors",1);
$tab = 1;
$msg = '';
if (isset($_POST['submit'])) {
   $maker_name = mysqli_real_escape_string($db, $_POST['maker_name']);
   $mobile_number = mysqli_real_escape_string($db, $_POST['mobile_number']);
   $username = mysqli_real_escape_string($db, $_POST['username']);
   $pwd = mysqli_real_escape_string($db, $_POST['pwd']);
   $edit_sno = (int) $_POST['edit_sno'];
   $designation = (int) $_POST['designation'];
   $sql='(SELECT apex_id AS username FROM apex WHERE apex_id="'.$username.'") UNION ALL (SELECT username AS username FROM maker WHERE username="'.$username.'" AND sno!='.$edit_sno.')';
   $result = execute_query($sql);
   $num_rows = mysqli_num_rows($result);
   if ($maker_name == '') {
      $msg .= '<li>Please Enter Maker Name</li>';
   }
   if ($msg == '') {
      if ($edit_sno > 0) {
         if ($num_rows == 0) {
            $sql = 'update maker set maker_name="' . $maker_name . '",mobile_number="' . $mobile_number . '",username="' . $username . '",pwd="' . $pwd . '",designation="' . $designation . '" where sno=' . $edit_sno . ' and status=0';
            execute_query($sql);
            $msg .= '<li>maker Updated Successfully</li>';
         } else {
            $msg .= '<li>Username already exists</li>';
         }
      } else {
         if ($num_rows == 0) {
            $sql = 'insert into maker(maker_name,mobile_number,username,pwd,designation,apex_id,created_by,creation_time,status) values("' . $maker_name . '","' . $mobile_number . '","' . $username . '","' . $pwd . '","' . $designation . '","' . $_SESSION['apex_id'] . '","' . $_SESSION['username'] . '","' . date("Y-m-d H:i:s") . '",0)';
            execute_query($sql);
            $msg .= '<li>maker Created Successfully</li>';
         } else {
            $msg .= '<li>Username already exists</li>';
         }
      }
   }
}
if (isset($_GET['id'])) {
   $id = (int) $_GET['id'];
   $sql = 'select * from maker where sno=' . $id . ' and status=0';
   $row_edit = mysqli_fetch_assoc(execute_query($sql));
}
if (isset($_GET['del'])) {
   $del_id = (int) $_GET['del'];
   $sql = 'update maker set status=1 where sno=' . $del_id;
   execute_query($sql);
   $msg .= '<li>maker Deleted Successfully</li>';
}
page_header_start();
page_header_end();
page_sidebar();
?>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-body"><?php echo $msg; ?>
            <form method="post">
               <div class="row">
                  <div class="col-md-3 form-group"><label>Maker Name</label><input type="text" name="maker_name"
                        class="form-control" value="<?php if (isset($row_edit['maker_name']))
                           echo $row_edit['maker_name']; ?>"></div>
                  <div class="col-md-3 form-group"><label>Mobile Number</label><input type="text" name="mobile_number"
                        class="form-control" value="<?php if (isset($row_edit['mobile_number']))
                           echo $row_edit['mobile_number']; ?>"></div>
                  <div class="col-md-3 form-group"><label>Username</label><input type="text" name="username"
                        class="form-control" value="<?php if (isset($row_edit['username']))
                           echo $row_edit['username']; ?>">
                  </div>
                  <div class="col-md-3 form-group"><label>Password</label><input type="text" name="pwd"
                        class="form-control" value="<?php if (isset($row_edit['pwd']))
                           echo $row_edit['pwd']; ?>"></div>
                  <div class="col-md-3 form-group">
                     <label>Designation</label>
                     <select name="designation" class="form-control">
                        <option value="">Select</option>
                        <?php
                        $sql = "select * from master_post_upcldf order by id";
                        $res = execute_query($sql);
                        while ($r = mysqli_fetch_assoc($res)) {
                           ?>
                           <option value="<?php echo $r['id']; ?>" <?php if (isset($row_edit['designation']) && $row_edit['designation'] == $r['id'])
                                 echo 'selected'; ?>>
                              <?php echo $r['post_name']; ?>
                           </option>
                        <?php } ?>
                     </select>
                  </div>
               </div>
               <input type="hidden" name="edit_sno" value="<?php if (isset($_GET['id']))
                  echo $_GET['id']; ?>">
               <input type="submit" class="btn btn-info" value="Save" name="submit">
            </form>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-body table-responsive">
            <table class="table table-hover table-striped text-center">
               <thead>
                  <tr>
                     <th>S.No.</th>
                     <th>Maker Name</th>
                     <th>Mobile Number</th>
                     <th>Username</th>
                     <th>Password</th>
                     <th>Designation</th>
                     <th>ID</th>
                     <th>Edit</th>
                     <th>Delete</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  $i = 1;
                  if ($_SESSION['usertype'] == 'sadmin') {
                     $sql = "select maker.*,master_post_upcldf.post_name from maker left join master_post_upcldf on master_post_upcldf.id=maker.designation where maker.status=0";
                  } else {
                     $sql = "select maker.*,master_post_upcldf.post_name from maker left join master_post_upcldf on master_post_upcldf.id=maker.designation where maker.status=0 and maker.apex_id=" . (int) $_SESSION['apex_id'];
                  }
                  $result = execute_query($sql);
                  while ($row = mysqli_fetch_assoc($result)) { ?>
                     <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['maker_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['pwd']); ?></td>
                        <td><?php echo ($row['post_name']); ?></td>
                        <td><?php echo $row['sno']; ?></td>
                        <td><a href="?id=<?php echo $row['sno']; ?>"><span class="far fa-edit"></span></a></td>
                        <td><a href="?del=<?php echo $row['sno']; ?>" onclick="return confirm('Delete this record?')"
                              style="color:red"><span class="far fa-trash-alt"></span></a></td>
                     </tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<?php
page_footer_start();
page_footer_end();
?>