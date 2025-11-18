<?php
include("scripts/settings.php");
$tab = 1;
$msg = '';


if (isset($_POST['submit'])) {
		// Data receive karo from form (sanitize karna zaroori hai, yeh basic example hai)
		$form_type_id = $_POST['form_type_id'];
		$form_sub_type_id = $_POST['form_sub_type_id'];
		$input_label = $_POST['input_label'];  // spelling same as table (input_lable)
		$input_type = $_POST['input_type'];
		$options = $_POST['options'];
		$form_group = $_POST['form_group'];
		$validation = $_POST['validation'];
		$status = 0;
		$created_by = $_SESSION['usersno']; // user id from session
		$creation_time = date('Y-m-d H:i:s');

		// DB connection assume $db hai (mysqli connection object)

		$sql = "INSERT INTO form_input 
			(form_type_id, form_sub_type_id, input_lable, input_type, options, form_group, validation, status, created_by, creation_time) 
			VALUES 
			('$form_type_id', '$form_sub_type_id', '$input_label', '$input_type', '$options', '$form_group', '$validation', '$status', '$created_by', '$creation_time')";

		if (mysqli_query($db, $sql)) {
			echo "New form input inserted successfully";
		} else {
			echo "Error: " . mysqli_error($db);
		}
}else{
	
	$_POST['input_type']="";
	$_POST['validation']="";
}



if (isset($_GET['id'])) {
    $sql = 'SELECT * FROM form_input WHERE id=' . $_GET['id'];
    $result = execute_query($sql);
    $row_edit = mysqli_fetch_array($result);
	$_POST['input_type']=$row_edit['input_type'];
	$_POST['validation']=$row_edit['validation'];
	
}

if (isset($_GET['del'])) {
    $sql = 'DELETE FROM form_input WHERE id=' . $_GET['del'];
    if (mysqli_query($db, $sql)) {
		echo "Row Deleted successfully";
	} else {
		echo "Error: " . mysqli_error($db);
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
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
						
						
                            <div class="col-sm-12">
								<div class="row">
                                    <div class="col-sm-3 form-group">
                                        <label>Form type </label>
                                        <select name="form_type_id" id="form_type_id" tabindex="<?php echo $tab++; ?>" class="form-control" onChange="fill_sub_type(this.value);">
											<option value="">--Select--</option>
                                            <?php
                                            $sql = 'select * from form_type';
                                            $result_division = execute_query($sql);
                                            while ($row_division = mysqli_fetch_assoc($result_division)) {
                                                echo '<option value="' . $row_division['id'] . '" ' . (isset($row_edit['id']) && $row_edit['division_name'] == $row_division['id'] ? 'selected' : '') . '>' . $row_division['form_name'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Form Sub-Type</label>
                                        <select name="form_sub_type_id" id="form_sub_type_id" tabindex="<?php echo $tab++; ?>" class="form-control">
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 form-group">
                                        <label>Input label</label>
                                        <input type="text" name="input_label" id="input_label" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo isset($row_edit['input_label']) ? $row_edit['input_label'] : ''; ?>">
                                    </div>
                                    <div class="col-sm-2 form-group">
                                        <label>Input Type</label>
                                        <select name="input_type" id="input_type" class="form-control">
											<option value="text" <?php echo ($_POST['input_type'] == "text" ? 'selected' : ''); ?>>Text</option>
											<option value="email" <?php echo ($_POST['input_type'] == "email" ? 'selected' : ''); ?>>Email</option>
											<option value="number" <?php echo ($_POST['input_type'] == "number" ? 'selected' : ''); ?>>Number</option>
											<option value="date" <?php echo ($_POST['input_type'] == "date" ? 'selected' : ''); ?>>Date</option>
											<option value="file" <?php echo ($_POST['input_type'] == "file" ? 'selected' : ''); ?>>File Upload</option>
											<option value="select" <?php echo ($_POST['input_type'] == "select" ? 'selected' : ''); ?>>Dropdown (Select)</option>
											<option value="textarea" <?php echo ($_POST['input_type'] == "textarea" ? 'selected' : ''); ?>>Textarea</option>
											<option value="checkbox" <?php echo ($_POST['input_type'] == "checkbox" ? 'selected' : ''); ?>>Checkbox</option>
											<option value="radio" <?php echo ($_POST['input_type'] == "radio" ? 'selected' : ''); ?>>Radio</option>
										</select>

                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Validation</label>
                                        <select name="validation" id="validation" class="form-control">
											<option value="">-- Select Validation --</option>
											<option value="chk_text" <?php echo ($_POST['validation'] == "chk_text" ? 'selected' : ''); ?>>Text Only</option>
											<option value="chk_number" <?php echo ($_POST['validation'] == "chk_number" ? 'selected' : ''); ?>>Number Only</option>
											<option value="chk_decimal" <?php echo ($_POST['validation'] == "chk_decimal" ? 'selected' : ''); ?>>Decimal Number</option>
											<option value="chk_email" <?php echo ($_POST['validation'] == "chk_email" ? 'selected' : ''); ?>>Email Format</option>
											<option value="chk_mobile" <?php echo ($_POST['validation'] == "chk_mobile" ? 'selected' : ''); ?>>Mobile Number</option>
										</select>

                                    </div>
                                    <div class="col-sm-3 form-group">
                                        <label>Form Group Id</label>
                                        <input type="text" name="form_group" id="form_group" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo isset($row_edit['pwd']) ? $row_edit['pwd'] : ''; ?>">
                                    </div>
									<div class="col-sm-12 form-group">
                                        <label>Options {If you can choes Input type selct (Ex: option1, option2)}</label>
                                        <input type="text" name="options" id="options" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo isset($row_edit['options']) ? $row_edit['options'] : ''; ?>">
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
							<th>Form Type ID</th>
							<th>Sub Type ID</th>
							<th>Label</th>
							<th>Input Type</th>
							<th>Options</th>
							<th>Group</th>
							<th>Validation</th>
							<th>Status</th>
							<th>Edit</th>
							<th>Delete</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql = "SELECT * FROM form_input ORDER BY id DESC";
						$res = mysqli_query($db, $sql);
						$i = 1;
						while ($row = mysqli_fetch_assoc($res)) {
							echo "<tr>";
							echo "<td>" . $i++ . "</td>";
							echo "<td>" . $row['form_type_id'] . "</td>";
							echo "<td>" . $row['form_sub_type_id'] . "</td>";
							echo "<td>" . htmlspecialchars($row['input_lable']) . "</td>";
							echo "<td>" . $row['input_type'] . "</td>";
							echo "<td>" . $row['options'] . "</td>";
							echo "<td>" . ($row['form_group'] == 0 ? 'N/A' : $row['form_group']) . "</td>";
							echo "<td>" . $row['validation'] . "</td>";
							echo "<td>" . ($row['status'] == 0 ? 'Active' : 'Inactive') . "</td>";
							echo "<td><a href='manage_form_input.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning'>Edit</a></td>";
							echo "<td><a href='manage_form_input.php?del=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
var actionUrl = 'scripts/ajax.php';

function fill_sub_type(val){
	$("#form_sub_type_id").html('<option value="">--Select--</option>');
	var data = {"term":"b", "id":"form_type", "val":val};
	$.ajax({
        type: "POST",
        url: actionUrl,
        data: data, // serializes the form's elements.
        success: function(data){
			var txt = '<option value="">--Select--</option>';
			data = JSON.parse(data);
			$.each(data, function(key, value){
				txt += '<option value="'+value.id+'">'+value.sub_form_name+'</option>';
				
			});
          	$("#form_sub_type_id").html(txt);
        }
    });
}




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
