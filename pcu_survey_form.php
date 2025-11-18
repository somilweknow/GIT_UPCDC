<?php
$db = mysqli_connect("p:localhost", "root", "mysql", "pcu_db");
if (!$db) {
    die('System error. ' . mysqli_connect_error());
}

$error = "";
$msg = "";
$society_id = 3;
$form_type_id = 1;
$created_by = 'admin';
$timestamp = date('Y-m-d H:i:s');

// Save form answers
if (isset($_POST['submit'])) {
    unset($_POST['submit']);
	
	if(isset($_POST['edit_id']) && $_POST['edit_id']!==""){
		$edit_society_id = (int)$_POST['edit_id'];
			
		$delet_sql='UPDATE `form_answer` SET `Status` = 1 WHERE `row_index` IS NOT NULL AND Society_id = "'.$edit_society_id.'" AND Form_type_id = "'.$form_type_id.'"';
		mysqli_query($db, $delet_sql);
		
		foreach ($_POST as $key => $value) {
			$input_id = (int)$key;
		
			if (is_array($value)) {
				foreach ($value as $rowIndex => $val) {
					$val = mysqli_real_escape_string($db, $val);
					$sqlInsert = "INSERT INTO form_answer (Society_id, Form_type_id, Form_Input_id, Form_input_value, Status, Created_by, Creation_time, row_index)
								  VALUES ('$edit_society_id', '$form_type_id', '$input_id', '$val', '0', '$created_by', '$timestamp', '$rowIndex')";
					$res = mysqli_query($db, $sqlInsert);
					if ($res) {
						$msg .= "<div class='alert alert-success'>Form Updated successfully.</div>";
					} else {
						$error .= "<div class='alert alert-danger'>Error: " . mysqli_error($db) . "</div>";
					}
				}
			} else {
				
				$val = mysqli_real_escape_string($db, $value);
					$checkExisting = mysqli_query($db, "SELECT id FROM form_answer WHERE Society_id = '$edit_society_id' AND Form_type_id = '$form_type_id' AND Form_Input_id = '$input_id' AND row_index IS NULL");

				if (mysqli_num_rows($checkExisting) > 0) {
					$sqlUpdate = "UPDATE form_answer SET Form_input_value = '$val', Creation_time = '$timestamp' 
								  WHERE Society_id = '$edit_society_id' AND Form_type_id = '$form_type_id' AND Form_Input_id = '$input_id' AND row_index IS NULL";
					$res = mysqli_query($db, $sqlUpdate);
				}
			}
		}
	}else{
		
		foreach ($_POST as $key => $value) {
			$input_id = (int)$key;

			if (is_array($value)) {
				foreach ($value as $rowIndex => $val) {
					$val = mysqli_real_escape_string($db, $val);
					$sqlInsert = "INSERT INTO form_answer (Society_id, Form_type_id, Form_Input_id, Form_input_value, Status, Created_by, Creation_time, row_index)
								  VALUES ('$society_id', '$form_type_id', '$input_id', '$val', '0', '$created_by', '$timestamp', '$rowIndex')";
					$res = mysqli_query($db, $sqlInsert);
					if ($res) {
						$msg .= "<div class='alert alert-success'>Form submitted successfully.</div>";
					} else {
						$error .= "<div class='alert alert-danger'>Error: " . mysqli_error($db) . "</div>";
					}
				}
			} else {
				$val = mysqli_real_escape_string($db, $value);
				$sqlInsert = "INSERT INTO form_answer (Society_id, Form_type_id, Form_Input_id, Form_input_value, Status, Created_by, Creation_time)
							  VALUES ('$society_id', '$form_type_id', '$input_id', '$val', '0', '$created_by', '$timestamp')";
				$res = mysqli_query($db, $sqlInsert);
				if ($res) {
					$msg .= "<div class='alert alert-success'>Form submitted successfully.</div>";
				} else {
					$error .= "<div class='alert alert-danger'>Error: " . mysqli_error($db) . "</div>";
				}
			}
		}
		if ($error == "") {
			$msg .= "<div class='alert alert-success'>All entries saved successfully.</div>";
		}
	}
}


// Fetch inputs
$sql = "SELECT * FROM form_input WHERE form_type_id = 1 ORDER BY form_group, id ASC";
$result = mysqli_query($db, $sql);

$groupedFields = [];
$nonGroupedFields = [];

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['form_group'] == 0) {
        $nonGroupedFields[] = $row;
    } else {
        $groupedFields[$row['form_group']][] = $row;
    }
}

if (isset($_GET['id'])){
// Fetch previously saved answers
	$existingAnswers = [];
	echo $ansQuery = "SELECT Form_Input_id, Form_input_value, form_group, row_index FROM form_answer
				left join form_input on form_answer.Form_Input_id=form_input.id
				 WHERE Society_id = {$_GET['id']} AND form_answer.status!=1 AND form_answer.Form_type_id = $form_type_id";
	$ansResult = mysqli_query($db, $ansQuery);
	while ($ansRow = mysqli_fetch_assoc($ansResult)) {
		$inputId = $ansRow['Form_Input_id'];
		$groupId = $ansRow['form_group'];
		$val = $ansRow['Form_input_value'];

		if (!isset($existingAnswers[$inputId])) {
			$existingAnswers[$inputId] = [];
		}
		$existingAnswers[$inputId][] = $val;
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dynamic Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php echo $msg . $error; ?>
<div class="container mt-4">
    <form method="POST" action="pcu_survey_form.php" enctype="multipart/form-data">
        <!-- Non-grouped fields -->
        <div class="row">
            <?php foreach ($nonGroupedFields as $field): ?>
                <?php
                    $inputId = $field['id'];
                    $val = isset($existingAnswers[$inputId][0]) ? $existingAnswers[$inputId][0] : '';
                ?>
                <div class="col-md-4 mb-3">
                    <label class="form-label"><?= $field['Input_lable'] ?></label>
                    <input 
                        type="<?= $field['Input_type'] ?>" 
                        name="<?= $inputId ?>" 
                        class="form-control <?= $field['validation'] ?>" 
                        placeholder="<?= $field['Input_lable'] ?>"
                        value="<?= htmlspecialchars($val) ?>"
                    >
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Grouped fields -->
        <?php foreach ($groupedFields as $groupId => $fields): ?>
            <?php
                echo $firstFieldId = $fields[0]['id'];
				$maxRows = 1;

				if (isset($existingAnswers[$firstFieldId])) {
					// Count only non-empty values
					$nonEmptyValues = array_filter($existingAnswers[$firstFieldId], function($val) {
						return trim($val) !== '';
					});
					$maxRows = count($nonEmptyValues);
					if ($maxRows == 0) $maxRows = 1;
				}

            ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Group <?= $groupId ?></span>
                    <button type="button" class="btn btn-light btn-sm" onclick="addGroupRow(<?= $groupId ?>)">➕ Add Row</button>
                </div>
                <div class="card-body group-wrapper" id="group-wrapper-<?= $groupId ?>">
                    <?php for ($i = 0; $i < $maxRows; $i++): ?>
                        <div class="row group-row">
                            <?php foreach ($fields as $field): ?>
                                <?php
                                    $inputId = $field['id'];
                                    $value = isset($existingAnswers[$inputId][$i]) ? $existingAnswers[$inputId][$i] : '';
                                ?>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><?= $field['Input_lable'] ?></label>
                                    <?php if ($field['Input_type'] === 'select' && $field['options']): ?>
                                        <select class="form-control" name="<?= $inputId ?>[]">
                                            <option value="">-- Select --</option>
                                            <?php foreach (explode(',', $field['options']) as $opt): ?>
                                                <option value="<?= trim($opt) ?>" <?= trim($opt) == $value ? 'selected' : '' ?>>
                                                    <?= trim($opt) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <input 
                                            type="<?= $field['Input_type'] ?>" 
                                            name="<?= $inputId ?>[]" 
                                            class="form-control <?= $field['validation'] ?>"
                                            value="<?= htmlspecialchars($value) ?>"
                                        >
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <button type="submit" name="submit" class="btn btn-success">Submit</button>
		<input type="text" name="society_id" value="<?php echo $society_id; ?>">
		<input type="text" name="edit_id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">
    </form>
</div>

<!-- JS to add group row dynamically -->
<script>
function addGroupRow(groupId) {
    const wrapper = document.getElementById('group-wrapper-' + groupId);
    const existingRow = wrapper.querySelector('.group-row');
    if (!existingRow) return;

    const cloned = existingRow.cloneNode(true);

    cloned.querySelectorAll('input, select, textarea').forEach(input => {
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else {
            input.value = '';
        }
    });

    wrapper.appendChild(cloned);
}
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script language="javascript" type="text/javascript">

	function validate_input() {
		// var regexp_text = /^[\p{Letter}\u0900-\u097F ]+$/u;
		var regexp_text = /^[A-Za-z\u0900-\u097F,.\s]+$/;
		// var regexp_spltext = /^[\p{Letter}\u0900-\u097F -,./]+$/u;
		var regexp_spltext = /^[\p{Letter}\u0900-\u097F ,.\-!?]+$/u;
		var regexp_number = /^\d+$/;
		var regexp_decimal = /^-?\d+(\.\d+)?$/;
		// var regexp_email = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
		var regexp_email = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
		var error_status = 0;
		var error_msg = '';

		// Validate Text Inputs (e.g., names, descriptions)
		$(".chk_text").each(function () {
			var value_text = $(this).val();
			if (value_text != "") {
				if (!regexp_text.test(value_text)) {
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_msg += $(this).data("type") + "\n"; // Error message for invalid input
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			} else {
				$(this).removeClass("danger success");
			}
		});

		// Validate Special Text Inputs (e.g., addresses, descriptions with punctuation)
		$(".chk_spltext").each(function () {
			var value_text = $(this).val();
			if (value_text != "") {
				if (!regexp_spltext.test(value_text)) {
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_msg += $(this).data("type") + "\n";
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			} else {
				$(this).removeClass("danger success");
			}
		});

		$(".chk_special_text").each(function () {
			var value_text = $(this).val();
			// Regex allows letters (Latin and Devanagari), comma, and full stop only
			var regexp_spltext = /^[A-Za-z\u0900-\u097F,.\s]+$/; // Allows letters, comma, full stop, and space

			if (value_text != "") {
				if (!regexp_spltext.test(value_text)) {
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_msg += $(this).data("type") + " (अवैध वर्ण)\n";  // Error message for invalid characters
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			} else {
				$(this).removeClass("danger success");
			}
		});

		// Validate Number Inputs (with max 5 digits)
		$(".chk_number").each(function () {
			var value_number = $(this).val();
			var minlength = $(this).data("minlength");  // Minimum length if defined
			var maxlength = $(this).data("maxlength"); // Maximum length (set as data-maxlength="5")
			
			// Ensure the value is not empty and validate length
			if (value_number != "") {
				// Check for minimum length
				if (value_number.length < minlength) {
					error_msg += $(this).data("type") + ". न्यूनतम " + minlength + " अंक भरें। \n";
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_status = 1;
				}
				// Check if the value exceeds the maximum limit (e.g., 5 digits max)
				else if (value_number.length > maxlength) {
					error_msg += $(this).data("type") + " 5 अंकों से अधिक नहीं हो सकता। \n"; // Max 5 digits
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_status = 1;
				}
				// Check if the value is numeric (only digits allowed)
				else if (!regexp_number.test(value_number)) {
					error_msg += $(this).data("type") + " केवल अंक भरें। \n"; // Only numbers allowed
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			} else {
				$(this).removeClass("danger success");
			}
		});

		$(".chk_mobile").each(function () {
			 // Trigger on blur or keyup, based on your preference
				var value_mobile = $(this).val().trim();  // Strip extra spaces from the input
				
				// Define the minimum and maximum length for mobile number (10 digits)
				var minlength = 10;
				var maxlength = 10;
				
				// Ensure the value is not empty and validate length
				if (value_mobile != "") {
					// Check if the value is not exactly 10 digits
					if (value_mobile.length < minlength || value_mobile.length > maxlength) {
						error_msg += $(this).data("type") + " केवल 10 अंक भरें। \n"; // Should be exactly 10 digits
						$(this).addClass("danger");
						$(this).removeClass("success");
						error_status = 1;
					}
					// Check if the value is numeric (only digits allowed)
					else if (!/^\d{10}$/.test(value_mobile)) {
						error_msg += $(this).data("type") + " केवल अंक भरें। \n"; // Only numbers allowed
						$(this).addClass("danger");
						$(this).removeClass("success");
						error_status = 1;
					} else {
						$(this).addClass("success");
						$(this).removeClass("danger");
					}
				} else {
					$(this).removeClass("danger success");
				}
		});

		// Validate Decimal Inputs (for numbers with decimals, negative numbers allowed)
		$(".chk_decimal").each(function () {
			var value_decimal = $(this).val().trim();
			if (value_decimal != "") {
				if (!regexp_decimal.test(value_decimal)) {
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_msg += $(this).data("type") + "\n";
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			} else {
				$(this).removeClass("danger success");
			}
		});

		// Validate Email Inputs
		$(".chk_email").each(function () {
			var value_email = $(this).val();
			if (value_email != "") {
				if (!regexp_email.test(value_email)) {
					$(this).addClass("danger");
					$(this).removeClass("success");
					error_msg += $(this).data("type") + ". \n";
					error_status = 1;
				} else {
					$(this).addClass("success");
					$(this).removeClass("danger");
				}
			} else {
				$(this).removeClass("danger success");
			}
		});

		// Final check: Set the error status
		$("#error_status").val(error_status);
		
		// Show error messages if there were validation issues
		if (error_msg != "") {
			alert(error_msg);
		}

		return error_status === 0;  // Return false to prevent form submission if there are errors
	}
</script>
<script>
	$(document).ready(function () {
		// alert('suraj');
			$(".chk_text, .chk_number, .chk_decimal, .chk_email, .chk_mobile").blur(function () {
				validate_input();
			});
	});
</script>
</body>
</html>
