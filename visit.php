<?php
include("scripts/settings.php");
$msg = '';
$tab = 1;
$response = 1;

?>

<?php
page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/survey_validate.js?v=1.4.0"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
	.date-field {
		display: none;
	}

	.table-container {
		width: 100%;
		overflow-x: auto;
	}
</style>
<style>
	.table-section {
		border-collapse: collapse;
	}

	.table-section th,
	.table-section td {
		border: 1px solid #000;
	}

	.table-section,
	.table-section th,
	.table-section td {
		border-color: transparent;
	}

	.table-section td:first-child {
		border-left-color: #000;
	}

	.table-section th:first-child {
		border-left-color: #000;
	}

	.table-section td:last-child {
		border-right-color: #000;
	}

	.table-section th:last-child {
		border-right-color: #000;
	}

	.table-section tr:first-child th {
		border-top-color: #000;
	}

	.table-section tr:last-child td {
		border-bottom-color: #000;
	}

	.form-section {
		margin-bottom: 0;
	}

	.form-section input {
		margin-bottom: 0;
	}

	.step h4 {
		color: #FFFFFF;
		background: #FF8E00;
		border-radius: 15px;
		padding: 10px 10px 6px 20px;
	}

	.step h5 {
		color: #000000;
		background: #FFDB44;
		border-radius: 15px;
		padding: 10px 10px 6px 20px;
	}
</style>
<style>
	.select-default {
		background-color: white;
	}
</style>

<style>
	.danger {
		border: 2px solid #f00;
		background-color: #f00;
		text-color: white;
	}

	.success {
		border: 3px solid #0f0;
	}
</style>

<?php
page_header_end();
page_sidebar();
?>

<?php
if (isset($_GET['sid'])) {
    $society_type_id = $_GET['stid'];
    $society_id = $_GET['sid'];
    $query = "SELECT `sno`, `division_id`, `district_id`, `tehseel_id`, `block_id`, `society_type_id`, `society_name`, `society_address`, `sachive_name`, `latitude`, `longitude`, `status`, mobile_number FROM master_society_details WHERE sno = '$society_id' AND society_type_id = '$society_type_id' ";

    $result = mysqli_query($db, $query);
	$row_info = mysqli_fetch_assoc($result);
}

?>

<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row d-flex my-auto">
					<div class="col-md-12">
						<div class="progress">
							<div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50"
								class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
								role="progressbar" style="width: 0%">
							</div>
						</div>
						<form action="scripts/survey_form_ajax.php" method="post" enctype="multipart/form-data"
							id="user_form" name="user_form">
							<div id="steps-container">
								<marquee style="font-size: 18px; color: red;">
									नोट: समस्त विवरण ADO अथवा (ADO विकास खंड के तैनात न होने पर ADCO द्वारा)
									प्रत्येक माह की पांच (5) तारीख तक भरना अनिवार्य है, जिसके उपरांत AR अथवा CEO
									द्वारा परीक्षण कर 10 तारीख तक अनुमोदन करना आवश्यक है।
								</marquee><br><br>
								<?php echo $msg; ?>
									
								<div class="step">
									<h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
											style="height:45px; width:45px;"> 1. समिति का विवरण </h4>
									<div class="col-sm-12">
										<div class="row">
											<div class="col-md-4">
												<div class="row">
													<div class="col-md-4">
														<h6>संस्था का प्रकार : </h6>
													</div>
													<div class="col-md-8">
														शीर्ष सहकारी संस्था (APEX)
													</div>
												</div>
												<div class="row">
													<div class="col-sm-4"><h6>समिति का नाम : </h6></div>
													<div class="col-sm-8"><?php echo $row_info['society_name']; ?></div>
												</div>
											</div>
											<div class="col-md-8">
												<input type="hidden" id="society_code" name="society_code"
													value="<?php echo $row_info['sno']; ?>">
												<input type="hidden" id="mobile_number" name="mobile_number"
													value="<?php echo $row_info['mobile_number']; ?>">
												<div class="row">
													<div class="col-md-2">
														<label>Latitude</label>
														<input type="text" id="lat" disabled="disabled"
															value="<?php echo $row_invoice['latitude']; ?>"
															class="form-control">
														<label>Longitude</label>
														<input type="text" id="long" disabled="disabled"
															value="<?php echo $row_invoice['longitude']; ?>"
															class="form-control">
														<button type="button" class="btn btn-info"
															onClick="getLocation();">लोकेशन रिफ्रेश करें</button>
													</div>
													<div class="col-md-10" id="map_container">
														<iframe id="googlemap"
															src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
															width="100%" height="100%"
															style="border:1px solid; border-radius:10px;"
															allowfullscreen="" loading="lazy"
															referrerpolicy="no-referrer-when-downgrade"></iframe>
													</div>
												</div>
											</div>
										</div>

										<br>
										
										
										<div class="row">
											<div class="col-sm-4 form-group">
												<label>क्या समिति सक्रिय है ?</label>
												<select class="form-control" id="sec_1_committee_status"
													name="sec_1_committee_status" tabindex="<?php echo $tab++; ?>"
													onChange="hide_show(this.value, '#committee_date_section', 'no'); handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['committee_status'] == 'yes') ? 'selected = "selected"' : ''; ?> style="background:#0f0"> हाँ
													</option>
													<option value="no" <?php echo ($row_invoice['committee_status'] == 'no') ? 'selected = "selected"' : ''; ?> style="background:#f00"> नहीं</option>
												</select>
											</div>
											<div class="col-sm-4 form-group" id="committee_date_section"
												style="display: none;">
												<label>समिति की तिथि</label><br>
												<label><small>नहीं पता होने की स्थिति में आज का ही दिनांक
														दर्शायें</small></label>
												<input type="text" id="sec_1_committee_date" name="sec_1_committee_date"
													class="form-control"
													value="<?php echo isset($row_invoice['committee_date']) ? $row_invoice['committee_date'] : ''; ?>"
													readonly>
											</div>
											<!-- <div class="col-sm-3 form-group" id="msc_services"
												style="<?php echo $displayStyle_sec_1_1_2_msc; ?>">
												<label>ग्रामपंचायत </label>
												<select name="gram_panchayat[]" id="gram_panchayat" tabindex="2"
													class="form-control" multiple="multiple">

													<?php
													echo$query = 'select * from gram_panchayt where block_sno="' . $row_info['block_id'] . '"';
													$run = mysqli_query($db, $query);
													while ($data = mysqli_fetch_array($run)) {
														echo '<option value="' . $data['sno'] . '" ';
														if (isset($_POST['gram_panchayat'])) {
															foreach ($_POST['gram_panchayat'] as $k => $v) {
																if ($v == $data['sno']) {

																	echo ' selected="selected"';
																}
															}
														}
														echo '>' . $data['gram_panchayt_id'] . '</option>';
													}

													?>

												</select>
											</div> -->

										</div>

										<div class="row">
											<div class="col-sm-2 form-group">
												<label>समिति पंजीकरण संख्या</label>
												<br />
												<input type="text" name="sec_1_society_registration_no"
													id="sec_1_society_registration_no" tabindex="<?php echo $tab++; ?>"
													class="form-control"
													value="<?php echo $row_invoice['society_registration_no']; ?>">
											</div>
											<div class="col-sm-2 form-group">
												<label>समिति पंजीकरण दिनांक</label>
												<input type="text" name="sec_1_society_registration_date"
													id="sec_1_society_registration_date"
													tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo $row_invoice['sec_1_society_registration_date']; ?>" readonly>
											</div>
											<div class="col-sm-2 form-group">
												<label>ई-मेल आई.डी.</label>
												<input type="text" name="sec_1_email" id="sec_1_email"
													data-type="1.1 सही ई-मेल आई.डी. भरे" tabindex="<?php echo $tab++; ?>"
													class="form-control chk_email"
													value="<?php echo $row_invoice['email_id'], ""; ?>">
											</div>
											<div class="col-sm-2 form-group">
												<label>समिति की फोटो संलग्न करें</label>
												<input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
													name="society_photo" id="society_photo"
													tabindex="<?php echo $tab++; ?>" class="form-control">

											</div>
											<?php
											if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) {
											?>
												<div class="col-sm-2 form-group">
													<img src="<?php echo $row_invoice['photo_id']; ?>" class="img-fluid img-thumbnail" style="height:50px;"
														id="society_photo_uploaded">
													<label><a href="<?php echo $row_invoice['photo_id']; ?>"
															target="_blank">संलग्न फोटो देखें</a></label>

												</div>
											<?php
											}
											?>
										</div>

										<hr>
										<br>
										<br>


										<div class="row">
											<div class="col-sm-3 form-group">
												<label for="">क्या समिति जी0एस0टी0 में पंजीकृत है?</label>
												<select class="form-control" name="sec_1_gst" id="sec_1_gst" tabindex="<?php echo $tab++; ?>"
													onChange="hide_show(this.value, '#sec_1_gst_no', 'yes');hide_show(this.value, '#sec_1_gst_return', 'yes');">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['sec_1_gst'] == 'yes') ? 'selected = "selected"' : ''; ?> style="background:#0f0"> हाँ </option>
													<option value="no" <?php echo ($row_invoice['sec_1_gst'] == 'no') ? 'selected = "selected"' : ''; ?> style="background:#f00"> नहीं</option>
												</select>
											</div>
											<div class="col-sm-3 form-group" id="sec_1_gst_no" style="display: none;">
												<label for="">जी0एस0टी0 पंजीकरण संख्या दर्ज करें</label>
												<input type="text" name="sec_1_gst_no" id="sec_1_gst_no" tabindex="<?php echo $tab++; ?>"
													class="form-control"
													value="<?php echo $row_invoice['sec_1_gst_no']; ?>">
											</div>
											<div class="col-sm-3 form-group" name="sec_1_gst_return" id="sec_1_gst_return" style="display: none;">
												<label for="">क्या नियमित जी0एस0टी0 रिटर्न दाखिल हो रहा है?</label>
												<select class="form-control" name="sec_1_gst_return" id="sec_1_gst_return" tabindex="<?php echo $tab++; ?>">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['sec_1_gst_return'] == 'yes') ? 'selected = "selected"' : ''; ?> style="background:#0f0"> हाँ </option>
													<option value="no" <?php echo ($row_invoice['sec_1_gst_return'] == 'no') ? 'selected = "selected"' : ''; ?> style="background:#f00"> नहीं</option>
												</select>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-3 form-group">
												<label for="">क्या समिति पैन पंजीकृत है?</label>
												<select class="form-control" name="sec_1_pan" id="sec_1_pan" tabindex="<?php echo $tab++; ?>"
													onChange="hide_show(this.value, '#sec_1_pan_no', 'yes');hide_show(this.value, '#sec_1_pan_itr_return', 'yes');">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['sec_1_pan'] == 'yes') ? 'selected = "selected"' : ''; ?> style="background:#0f0"> हाँ </option>
													<option value="no" <?php echo ($row_invoice['sec_1_pan'] == 'no') ? 'selected = "selected"' : ''; ?> style="background:#f00"> नहीं</option>
												</select>
											</div>
											<div class="col-sm-3 form-group" id="sec_1_pan_no" style="display: none;">
												<label for="">पैन पंजीकरण संख्या दर्ज करें</label>
												<input type="text" name="sec_1_pan_no" id="sec_1_pan_no" tabindex="<?php echo $tab++; ?>"
													class="form-control"
													value="<?php echo $row_invoice['sec_1_pan_no']; ?>">
											</div>
											<div class="col-sm-3 form-group" id="sec_1_pan_itr_return" style="display: none;">
												<label for="">क्या नीयमित आई०टी०आर० रिटर्न दाखिल हो रहा है?</label>
												<select class="form-control" name="sec_1_pan_itr_return" tabindex="<?php echo $tab++; ?>">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['sec_1_pan_itr_return'] == 'yes') ? 'selected = "selected"' : ''; ?> style="background:#0f0"> हाँ </option>
													<option value="no" <?php echo ($row_invoice['sec_1_pan_itr_return'] == 'no') ? 'selected = "selected"' : ''; ?> style="background:#f00"> नहीं</option>
												</select>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-3 form-group">
												<label for="">क्या उर्वरक लाइसेंसड है?</label>
												<select class="form-control" name="sec_1_fertilizer" id="sec_1_fertilizer"
													onChange="hide_show(this.value, '#sec_1_fertilizer_start_date', 'yes');hide_show(this.value, '#sec_1_fertilizer_end_date', 'yes');" tabindex="<?php echo $tab++; ?>">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['sec_1_fertilizer'] == 'yes') ? 'selected = "selected"' : ''; ?> style="background:#0f0"> हाँ </option>
													<option value="no" <?php echo ($row_invoice['sec_1_fertilizer'] == 'no') ? 'selected = "selected"' : ''; ?> style="background:#f00"> नहीं</option>
												</select>
											</div>
											<div class="col-sm-3 form-group" id="sec_1_fertilizer_start_date" style="display: none;">
												<label for="">प्रारंभ तिथि</label>
												<input type="text" placeholder="Choose Date" name="sec_1_fertilizer_start_date" id="sec_1_fertilizer_start_date_val" tabindex="<?php echo $tab++; ?>" class="form-control" value="<?php echo $row_invoice['sec_1_fertilizer_start_date']; ?>" readonly>
											</div>
											<div class="col-sm-3 form-group" id="sec_1_fertilizer_end_date" style="display: none;">
												<label for="">समाप्ति तिथि</label>
												<input type="text" placeholder="Choose Date" tabindex="<?php echo $tab++; ?>" id="sec_1_fertilizer_end_date_val" name="sec_1_fertilizer_end_date" class="form-control" value="<?php echo $row_invoice['sec_1_fertilizer_end_date']; ?>" readonly>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-3 form-group">
												<label for="">क्या पेस्टिसाइड लाइसेंसड है?</label>
												<select class="form-control" name="sec_1_pesticide" id="sec_1_pesticide"
													onChange="hide_show(this.value, '#sec_1_pesticide_start_date', 'yes');hide_show(this.value, '#sec_1_pesticide_end_date', 'yes');" tabindex="<?php echo $tab++; ?>">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['sec_1_pesticide'] == 'yes') ? 'selected = "selected"' : ''; ?> style="background:#0f0"> हाँ </option>
													<option value="no" <?php echo ($row_invoice['sec_1_pesticide'] == 'no') ? 'selected = "selected"' : ''; ?> style="background:#f00"> नहीं</option>
												</select>
											</div>
											<div class="col-sm-3 form-group" id="sec_1_pesticide_start_date" style="display: none;">
												<label for="">प्रारंभ तिथि</label>
												<input type="text" tabindex="<?php echo $tab++; ?>" id="sec_1_pesticide_start_date_val" name="sec_1_pesticide_start_date" class="form-control" placeholder="Choose Date" value="<?php echo $row_invoice['sec_1_pesticide_start_date']; ?>" readonly>
											</div>
											<div class="col-sm-3 form-group" tabindex="<?php echo $tab++; ?>" id="sec_1_pesticide_end_date" style="display: none;">
												<label for="">समाप्ति तिथि</label>
												<input type="text" id="sec_1_pesticide_end_date_val" name="sec_1_pesticide_end_date" class="form-control" placeholder="Choose Date" value="<?php echo $row_invoice['sec_1_pesticide_end_date']; ?>" readonly>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-3 form-group">
												<label>क्या समिति परिसमापन (Liquidation) में है?</label>
												<select name="sec_1_liquidation" id="sec_1_liquidation"
													tabindex="<?php echo $tab++; ?>" class="form-control"
													onchange="hide_show(this.value, '#liquidation_date_container', 'yes');hide_show(this.value, '#liquidation_status', 'yes');handleDropdownColorChange(this, 'yes', '#42ecf5', 'no', '#f28546');">
													<option value="">--Select--</option>
													<option value="yes" <?php echo ($row_invoice['liquidation'] == 'yes') ? ' selected="selected"' : ''; ?> style="background:#0f0"> हाँ </option>
													<option value="no" <?php echo ($row_invoice['liquidation'] == 'no') ? ' selected="selected"' : ''; ?> style="background:#f00"> नहीं </option>
												</select>
											</div>

											<div class="col-sm-3 form-group" id="liquidation_date_container"
												style="display: none;">
												<label>परिसमापक नियुक्त करने की तिथि</label>
												<input type="text" tabindex="<?php echo $tab++; ?>" id="sec_1_liquidation_date" name="sec_1_liquidation_date" class="form-control" placeholder="Choose Date" value="<?php echo isset($row_invoice['liquidation_date']) ? $row_invoice['liquidation_date'] : ''; ?>" readonly>
											</div>

											<div class="col-sm-3 form-group" id="liquidation_status"
												style="display: none;">
												<label>परिसमापन की अद्यतन स्थिति</label>
												<input type="text" tabindex="<?php echo $tab++; ?>" id="sec_1_liquidation_status" name="sec_1_liquidation_status" class="form-control" placeholder="" value="<?php echo isset($row_invoice['liquidation_status']) ? $row_invoice['liquidation_status'] : ''; ?>">
											</div>
										</div>

									</div>
								</div>
								
								
								



<!----								
	<div class="step">
		<h4><img src="images/logo/8.png" alt="text" class="img-fluid stat-icon" style="height:50px; width:50px;"> </h4>
		<div class="col-sm-12">
			<div class="row">
				<h5></h5>
				<div class="col-sm-12">
									
				</div>
			</div>
		</div>
	</div>
------------>

<!-------------------------------------- Form append dynmicly here --------------------------------->
<?php
$society_type_id = $_GET['stid'];
$output = "";

// STEP 1: Get all applicable form types
$formTypes = mysqli_query($db, "SELECT * FROM form_type WHERE FIND_IN_SET('$society_type_id', applicable_society_id)");

while ($formType = mysqli_fetch_assoc($formTypes)) {
    // echo $form_type_id = $formType['id'];
    $form_type_id = $formType['id'];
    $form_type_name = htmlspecialchars($formType['form_name']);

    $output .= '<div class="step">
        <h4><img src="images/logo/8.png" alt="text" class="img-fluid stat-icon" style="height:50px; width:50px;"> ' . $form_type_id . '. &nbsp;' . $form_type_name . '</h4>
        <div class="col-sm-12">';

    // STEP A: Show inputs without sub_type_id
    $inputSql = "SELECT * FROM form_input 
                WHERE form_type_id = '$form_type_id' 
                AND (form_sub_type_id IS NULL OR form_sub_type_id = 0)
                ORDER BY form_group, id ASC";
    $inputResult = mysqli_query($db, $inputSql);

    $groupedFields = [];
    $nonGroupedFields = [];

    while ($field = mysqli_fetch_assoc($inputResult)) {
        if ($field['form_group'] == 0) {
            $nonGroupedFields[] = $field;
        } else {
            $groupedFields[$field['form_group']][] = $field;
        }
    }

    // Non-grouped fields (no sub_type_id)
    if (!empty($nonGroupedFields)) {
        $output .= '<div class="row">';
        foreach ($nonGroupedFields as $field) {
            $inputId = $field['id'];
            $output .= '<div class="col-md-4 mb-3">
                <label class="form-label">' . $field['input_lable'] . '</label>';

            if ($field['input_type'] == 'select' && $field['options']) {
                $output .= '<select class="form-control" name="' . $inputId . '">
                    <option value="">-- Select --</option>';
                foreach (explode(',', $field['options']) as $opt) {
                    $opt = trim($opt);
                    $output .= '<option value="' . $opt . '">' . $opt . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<input 
                    type="' . $field['input_type'] . '" 
                    name="' . $inputId . '" 
                    class="form-control ' . $field['validation'] . '" 
                    placeholder="' . $field['input_lable'] . '">';
            }

            $output .= '</div>';
        }
        $output .= '</div>';
    }

    // Grouped fields (no sub_type_id)
    foreach ($groupedFields as $groupId => $fields) {
        $output .= '<div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Group ' . $groupId . '</span>
                <button type="button" class="btn btn-light btn-sm" onclick="addGroupRow(' . $groupId . ')">➕ Add Row</button>
            </div>
            <div class="card-body group-wrapper" id="group-wrapper-' . $groupId . '">
                <div class="row group-row">';

        foreach ($fields as $field) {
            $inputId = $field['id'];
            $output .= '<div class="col-md-4 mb-3">
                <label class="form-label">' . $field['input_lable'] . '</label>';
            if ($field['input_type'] == 'select' && $field['options']) {
                $output .= '<select class="form-control" name="' . $inputId . '[]">
                    <option value="">-- Select --</option>';
                foreach (explode(',', $field['options']) as $opt) {
                    $opt = trim($opt);
                    $output .= '<option value="' . $opt . '">' . $opt . '</option>';
                }
                $output .= '</select>';
            } else {
                $output .= '<input 
                    type="' . $field['input_type'] . '" 
                    name="' . $inputId . '[]" 
                    class="form-control ' . $field['validation'] . '">';
            }
            $output .= '</div>';
        }

        $output .= '</div></div></div>';
    }

    // STEP 2: Show sub-form types and their inputs
    $formSubTypes = mysqli_query($db, "SELECT * FROM form_sub_type WHERE form_type_id = '$form_type_id' AND FIND_IN_SET('$society_type_id', applicable_society_id)");

    while ($subType = mysqli_fetch_assoc($formSubTypes)) {
        $sub_type_id = $subType['id'];
        $sub_type_name = htmlspecialchars($subType['sub_form_name']);

        $output .= '<div class="row">
            <div class="col-sm-12">
            <h5 class="fw-bold py-2 border-bottom">' . $sub_type_name . '</h5>';

        // STEP 3: Get inputs for this sub_type
        $inputSql = "SELECT * FROM form_input 
                    WHERE form_type_id = '$form_type_id' 
                    AND form_sub_type_id = '$sub_type_id'
                    ORDER BY form_group, id ASC";
        $inputResult = mysqli_query($db, $inputSql);

        $groupedFields = [];
        $nonGroupedFields = [];

        while ($field = mysqli_fetch_assoc($inputResult)) {
            if ($field['form_group'] == 0) {
                $nonGroupedFields[] = $field;
            } else {
                $groupedFields[$field['form_group']][] = $field;
            }
        }

        // Non-grouped fields
        if (!empty($nonGroupedFields)) {
            $output .= '<div class="row">';
            foreach ($nonGroupedFields as $field) {
                $inputId = $field['id'];
                $output .= '<div class="col-md-4 mb-3">
                    <label class="form-label">' . $field['input_lable'] . '</label>';

                if ($field['input_type'] == 'select' && $field['options']) {
                    $output .= '<select class="form-control" name="' . $inputId . '">
                        <option value="">-- Select --</option>';
                    foreach (explode(',', $field['options']) as $opt) {
                        $opt = trim($opt);
                        $output .= '<option value="' . $opt . '">' . $opt . '</option>';
                    }
                    $output .= '</select>';
                } else {
                    $output .= '<input 
                        type="' . $field['input_type'] . '" 
                        name="' . $inputId . '" 
                        class="form-control ' . $field['validation'] . '" 
                        placeholder="' . $field['input_lable'] . '">';
                }

                $output .= '</div>';
            }
            $output .= '</div>';
        }

        // Grouped fields
        foreach ($groupedFields as $groupId => $fields) {
            $output .= '<div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Group ' . $groupId . '</span>
                    <button type="button" class="btn btn-light btn-sm" onclick="addGroupRow(' . $groupId . ')">➕ Add Row</button>
                </div>
                <div class="card-body group-wrapper" id="group-wrapper-' . $groupId . '">
                    <div class="row group-row">';

            foreach ($fields as $field) {
                $inputId = $field['id'];
                $output .= '<div class="col-md-4 mb-3">
                    <label class="form-label">' . $field['input_lable'] . '</label>';
                if ($field['input_type'] == 'select' && $field['options']) {
                    $output .= '<select class="form-control" name="' . $inputId . '[]">
                        <option value="">-- Select --</option>';
                    foreach (explode(',', $field['options']) as $opt) {
                        $opt = trim($opt);
                        $output .= '<option value="' . $opt . '">' . $opt . '</option>';
                    }
                    $output .= '</select>';
                } else {
                    $output .= '<input 
                        type="' . $field['input_type'] . '" 
                        name="' . $inputId . '[]" 
                        class="form-control ' . $field['validation'] . '">';
                }
                $output .= '</div>';
            }

            $output .= '</div></div></div>';
        }

        $output .= '</div></div>'; // end subform block
    }

    $output .= '</div></div>'; // end form type block
}

echo $output;
?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const visibilityRules = [];

    <?php
    $rules = mysqli_query($db, "SELECT * FROM form_input_visibility_rules");
    while ($rule = mysqli_fetch_assoc($rules)) {
        echo "visibilityRules.push({
            controller_id: '{$rule['controller_input_id']}',
            value: '{$rule['controller_value']}',
            dependent_id: '{$rule['dependent_input_id']}',
            visibility: '{$rule['visibility']}'
        });\n";
    }
    ?>

    // Group rules by controller_id
    const controllerMap = {};
    // Group rules by dependent_id
    const dependentMap = {};

    visibilityRules.forEach(rule => {
        if (!controllerMap[rule.controller_id]) {
            controllerMap[rule.controller_id] = [];
        }
        controllerMap[rule.controller_id].push(rule);

        if (!dependentMap[rule.dependent_id]) {
            dependentMap[rule.dependent_id] = [];
        }
        dependentMap[rule.dependent_id].push(rule);
    });

    function getControllerValue(controllerId) {
        const controller = document.querySelector(`[name='${controllerId}']`);
        return controller ? controller.value : null;
    }

    function evaluateDependentVisibility(dependentId) {
        const rules = dependentMap[dependentId];
        const dependent = document.querySelector(`[name='${dependentId}'], [name='${dependentId}[]']`);
        if (!dependent) return;

        const dependentWrapper = dependent.closest('.col-md-4');
        if (!dependentWrapper) return;

        let show = false;

        for (let rule of rules) {
            const controllerValue = getControllerValue(rule.controller_id);
            const isMatch = (controllerValue === rule.value);
            if ((isMatch && rule.visibility === 'show') || (!isMatch && rule.visibility === 'hide')) {
                show = true;
                break;
            }
        }

        dependentWrapper.style.display = show ? 'block' : 'none';
    }

    function applyRulesOnControllerChange(controllerId) {
        Object.keys(dependentMap).forEach(dependentId => {
            const rules = dependentMap[dependentId];
            const related = rules.some(rule => rule.controller_id === controllerId);
            if (related) {
                evaluateDependentVisibility(dependentId);
            }
        });
    }

    // Add event listeners
    Object.keys(controllerMap).forEach(controllerId => {
        const controller = document.querySelector(`[name='${controllerId}']`);
        if (!controller) return;

        const eventType = controller.tagName === 'SELECT' ? 'change' : 'input';
        controller.addEventListener(eventType, () => applyRulesOnControllerChange(controllerId));

        // Run initially
        applyRulesOnControllerChange(controllerId);
    });
});
</script>


















								<div id="success">
									<div class="mt-5 text-center">
										<h4>प्रपत्र सफलता पूर्वक भरा गया</h4>
										<p>आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
											सत्यापन हेतु भेजा जाना है । कृप्या सत्यापन पर भेजने से पहले नीचे
											दर्शायें
											लिंक से फार्म खोल कर पुनः जाच कर लें । सब कुछ सही होने कि दशा में नीचे
											दिये
											बटन के माध्यम से सत्यापन के लिये आगे प्रेषित करें ।</p>
										<button class="btn btn-info" onclick="window.open('preview.php?exdid=<?php echo $_GET['exdid']; ?>', '_blank');">प्रपत्र पुनः निरीक्षण के लिये देखे</button>
									</div>
									<div class="col-md-12 text-center">
										<p><input type="checkbox" style="height: 20px; border:1px solid;"
												id="review_ack"
												onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
											मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी
											सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
										<button type="button" class="btn btn-danger" onClick="form_validate()"
											id="verification_button" disabled="disabled">सत्यापन के लिये आगे प्रेषित
											करें
										</button>


									</div>

									<div class="col-sm-12 form-group my-auto" id="send_otp_button2"
										style="display: none">
										<button type="button" name="verify_otp_btn" id="verify_otp_btn"
											tabindex="<?php echo $tab++; ?>" class="btn btn-info"
											onClick="verify_otp($('#survey_id').val());">आगे प्रेषित करे
										</button>
									</div>
								</div>

							</div>

							<div id="q-box__buttons">
								<button id="prev-btn" class="btn btn-info" type="button"
									onClick="save_draft()">Previous</button>
								<button id="next-btn" class="btn btn-success" type="button"
									onClick="save_draft()">Next</button>
								<button id="submit-btn" class="btn btn-danger" type="submit"
									onClick="validate_input(); save_draft();">Submit</button>
							</div>

							<button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i> Save Draft</button>
							<!-- <button class="btn btn-warning" type="button" onClick="chk_duplicate_id()"><i class="fas fa-save"></i> Find Duplicate IDs</button> -->
							<input type="hidden" id="term" name="term" value="a">
							<input type="hidden" id="latitude" name="latitude"
								value="<?php echo $row_invoice['latitude']; ?>">
							<input type="hidden" id="longitude" name="longitude"
								value="<?php echo $row_invoice['longitude']; ?>">
							<input type="hidden" id="id" name="id" value="submit_form">
							<input type="hidden" id="current_step_count" name="current_step_count" value="">
							<input type="hidden" id="survey_id" name="survey_id"
								value="<?php echo $row_invoice['sno']; ?>">
							<input type="hidden" id="error_status" name="error_status" value="">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="preloader-wrapper">
	<div id="preloader"></div>
	<div class="preloader-section section-left"></div>
	<div class="preloader-section section-right"></div>
</div>




<script type="text/javascript" src="js/multistepform.js?v=1">
	<!-- Light Bootstrap Table Core javascript and methods for Demo purpose  -->
</script>
<script src="js/light-bootstrap-dashboard.js?v=1.4.0">
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
<?php
page_footer_start();
page_footer_end();
?>