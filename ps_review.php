<?php
include("scripts/settings.php");
$msg = '';
$tab = 1;
$response = 1;

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
<style>
	.table-container {
		width: 100%;
		/* Ensures the container takes full width */
		overflow-x: auto;
		/* Enables horizontal scrolling */
		-webkit-overflow-scrolling: touch;
		/* Smooth scrolling on mobile */
	}

	.table-6-1 {
		width: 100%;
		/* Take up the full width of the parent container */
		table-layout: auto;
		/* Automatically adjusts columns based on content */
	}

	.table-6-1 th,
	.table-6-1 td {
		padding: 15px;
		/* Add padding for better readability */
		text-align: center;
		/* Center-align text */
		white-space: nowrap;
		/* Prevent text from breaking into multiple lines */
	}

	/* Optional: Adjustments for individual columns */
	.table-6-1 th:nth-child(1),
	.table-6-1 td:nth-child(1) {
		min-width: 120px;
		/* 'पद' column */
	}

	.table-6-1 th:nth-child(2),
	.table-6-1 td:nth-child(2) {
		min-width: 120px;
		/* 'स्थिति' column */
	}

	.table-6-1 th:nth-child(3),
	.table-6-1 td:nth-child(3) {
		min-width: 200px;
		/* 'नाम' column */
	}

	.table-6-1 th:nth-child(4),
	.table-6-1 td:nth-child(4) {
		min-width: 200px;
		/* 'पिता का नाम' column */
	}

	.table-6-1 th:nth-child(5),
	.table-6-1 td:nth-child(5) {
		min-width: 200px;
		/* 'पता' column */
	}

	.table-6-1 th:nth-child(6),
	.table-6-1 td:nth-child(6) {
		min-width: 150px;
		/* 'जन्म तिथि' column */
	}

	.table-6-1 th:nth-child(7),
	.table-6-1 td:nth-child(7) {
		min-width: 150px;
		/* 'शैक्षिक योग्यता' column */
	}

	.table-6-1 th:nth-child(8),
	.table-6-1 td:nth-child(8) {
		min-width: 150px;
		/* 'कंप्युटर का अनुभव' column */
	}

	.table-6-1 th:nth-child(9),
	.table-6-1 td:nth-child(9) {
		min-width: 150px;
		/* 'अनुमोदन स्तर' column */
	}

	.table-6-1 th:nth-child(10),
	.table-6-1 td:nth-child(10) {
		min-width: 120px;
		/* 'नियुक्ति वर्ष' column */
	}

	.table-6-1 th:nth-child(11),
	.table-6-1 td:nth-child(11) {
		min-width: 200px;
		/* 'प्रबंध समिति का प्रस्ताव संख्या' column */
	}

	.table-6-1 th:nth-child(12),
	.table-6-1 td:nth-child(12) {
		min-width: 150px;
		/* 'कार्मिक प्रकार' column */
	}

	.table-6-1 th:nth-child(13),
	.table-6-1 td:nth-child(13) {
		min-width: 120px;
		/* 'यदि अस्थाई हैं, तो आउटसोर्स/दैनिक/संविदा' column */
	}

	.blinking-text {
	animation: blink 1s step-start 0s infinite;
	color: red;
	}

	@keyframes blink {
	50% {
		opacity: 0;
	}
</style>

<?php
page_header_end();
page_sidebar();

?>

<?php
if ($response == 1) {
?>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex my-auto">
						<div class="col-md-12">
							<form action="scripts/survey_form_ajax.php" method="post" enctype="multipart/form-data" id="user_form" name="user_form">
								<div class="col-sm-12">
									<h4><img src="images/logo/5.png" alt="text" class="img-fluid stat-icon"
											style="height:50px; width:50px;">प्रमुख सचिव महोदय, सहकारिता के प्रतिदिन 02 बजे अपरान्ह पर समीक्षा बिन्दु</h5>
									<!-- Requires Bootstrap 5 CSS -->
										<div class="col-sm-12">
											<div class="row">
												<div class="col-md-4"><label for="">जनपद में सहकारिता क्षेत्र के कुल  उर्वरक सहकारी  बिक्री केन्द्रों की संख्या</label><input type="text" class="form-control"></div>
												<div class="col-md-4"><label for="">उर्वरक प्रेषण हेतु ट्रकों की आवश्यक्ता <br/></label><input type="text" class="form-control"></div>
												<div class="col-md-4"><label for="">7.5 MT से कम फास्फेटिक उर्वरक सहकारी  बिक्री केन्द्रों  की संख्या  <br/></label><input type="text" class="form-control"></div>
												
											</div>
											<div class="row">
												<div class="col-md-3">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0">Urea Sale</legend>
														<div class="row">
															<div class="col-md-5"><label for="">मात्रा MT में </label><input type="text" class="form-control"></div>
															<div class="col-md-5"><label for="">धनराशि लाख रु० में</label><input type="text" class="form-control"></div>
														</div>
													</fieldset>
												</div>
												<div class="col-md-3">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0">DAP Sale</legend>
														<div class="row">
															<div class="col-md-5"><label for="">मात्रा MT में </label><input type="text" class="form-control"></div>
															<div class="col-md-5"><label for="">धनराशि लाख रु० में</label><input type="text" class="form-control"></div>
														</div>
													</fieldset>
												</div>
												<div class="col-md-3">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0">NPK Sale</legend>
														<div class="row">
															<div class="col-md-5"><label for="">मात्रा MT में </label><input type="text" class="form-control"></div>
															<div class="col-md-5"><label for="">धनराशि लाख रु० में</label><input type="text" class="form-control"></div>
														</div>
													</fieldset>
												</div>
												<div class="col-md-3">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0">Total</legend>
														<div class="row">
															<div class="col-md-5"><label for="">मात्रा MT में </label><input type="text" class="form-control" disabled></div>
															<div class="col-md-5"><label for="">धनराशि लाख रु० में</label><input type="text" class="form-control" disabled></div>
														</div>
													</fieldset>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0"> बिक्री केन्द्रों द्वारा बैंक में जमा धनराशि लाख में </legend>
														<div class="row">
															<div class="col-md-12"><input type="text" class="form-control"></div>
														</div>
													</fieldset>
												</div>
												<div class="col-md-8">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0"> बैंक द्वारा प्रदायकर्ताओं को RTGS की गयी  धनराशि लाख में </legend>
														<div class="row">
															<div class="col-md-3"><label for="">IFFCO</label><input type="text" class="form-control"></div>
															<div class="col-md-3"><label for="">KRIBHCO</label><input type="text" class="form-control"></div>
															<div class="col-md-3"><label for="">PCF</label><input type="text" class="form-control"></div>
															<div class="col-md-3"><label for="">योग</label><input type="text" class="form-control" disabled></div>
														</div>
													</fieldset>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0">जिलाधिकारी द्वारा फास्फेटिक उर्वरक आवंटित उर्वरक  बिक्री केन्द्रों  की संख्या </legend>
														<div class="row">
															<div class="col-md-12"><input type="text" class="form-control"></div>
														</div>
													</fieldset>
												</div>
												<div class="col-md-4">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0"> RTGS के सापेक्ष PCF द्वारा प्रेषण </legend>
														<div class="row">
															<div class="col-md-6"><label for="">मात्रा MT में </label><input type="text" class="form-control"></div>
															<div class="col-md-6"><label for="">धनराशि लाख रु० में</label><input type="text" class="form-control"></div>
														</div>
													</fieldset>
												</div>
												<div class="col-md-4">
													<fieldset class="border rounded-3 p-3 mb-3">
														<legend class="float-none w-auto px-3 mb-0">PCF स्तर पर Pending 	</legend>
														<div class="row">
															<div class="col-md-6"><label for="">मात्रा MT में </label><input type="text" class="form-control"></div>
															<div class="col-md-6"><label for="">धनराशि लाख रु० में</label><input type="text" class="form-control"></div>
														</div>
													</fieldset>
												</div>
											</div>
										</div>
								</div>
								<button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i>Save</button>
								</br>
								<?php
								// echo 'somillllllllllllllllllllllllllllllll';
								if (!empty($latest_remarks)) {
								?>
									<label><b>Remarks (If REJECTED)*</b></label>
									<textarea disabled class="form-control"><?php echo htmlspecialchars($latest_remarks); ?></textarea><br>
								<?php
								}
								?>
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

	<script src="js/light-bootstrap-dashboard.js?v=1.4.0">
	</script>

<?php
} else {
	echo $msg;
}
page_footer_start();
page_footer_end();
?>>>>>