<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;

// Session-based restriction logic
$user_id = $_SESSION['user_id'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';

$filter_division = [];
$filter_district = [];
$filter_tehseel  = [];
$filter_block    = [];

// Example restrictions based on roles
if ($user_type == 'ar') {
    // AR has multiple districts often
    $res = execute_query("SELECT district_id FROM ar_details WHERE ar_id = '$user_id'");
    while($r = mysqli_fetch_assoc($res)) $filter_district[] = $r['district_id'];
    
    if(!empty($filter_district)){
        $dist_list = implode(',', $filter_district);
        $res_div = execute_query("SELECT DISTINCT division_id FROM master_district WHERE sno IN ($dist_list)");
        while($r = mysqli_fetch_assoc($res_div)) $filter_division[] = $r['division_id'];
    }
} elseif ($user_type == 'adco') {
    $res = execute_query("SELECT tehseel_id FROM adco_details WHERE adco_id = '$user_id'");
    while($r = mysqli_fetch_assoc($res)) $filter_tehseel[] = $r['tehseel_id'];
    
    if(!empty($filter_tehseel)){
        $teh_list = implode(',', $filter_tehseel);
        $res_dist = execute_query("SELECT DISTINCT district_id FROM master_tehseel WHERE sno IN ($teh_list)");
        while($r = mysqli_fetch_assoc($res_dist)) $filter_district[] = $r['district_id'];
        
        if(!empty($filter_district)){
            $dist_list = implode(',', $filter_district);
            $res_div = execute_query("SELECT DISTINCT division_id FROM master_district WHERE sno IN ($dist_list)");
            while($r = mysqli_fetch_assoc($res_div)) $filter_division[] = $r['division_id'];
        }
    }
} elseif ($user_type == 'ado') {
    $res = execute_query("SELECT block_id FROM ado_details WHERE ado_id = '$user_id'");
    while($r = mysqli_fetch_assoc($res)) $filter_block[] = $r['block_id'];
    
    if(!empty($filter_block)){
        $blk_list = implode(',', $filter_block);
        $res_teh = execute_query("SELECT DISTINCT tehseel_id FROM master_block WHERE sno IN ($blk_list)");
        while($r = mysqli_fetch_assoc($res_teh)) $filter_tehseel[] = $r['tehseel_id'];
        
        if(!empty($filter_tehseel)){
            $teh_list = implode(',', $filter_tehseel);
            $res_dist = execute_query("SELECT DISTINCT district_id FROM master_tehseel WHERE sno IN ($teh_list)");
            while($r = mysqli_fetch_assoc($res_dist)) $filter_district[] = $r['district_id'];
            
            if(!empty($filter_district)){
                $dist_list = implode(',', $filter_district);
                $res_div = execute_query("SELECT DISTINCT division_id FROM master_district WHERE sno IN ($dist_list)");
                while($r = mysqli_fetch_assoc($res_div)) $filter_division[] = $r['division_id'];
            }
        }
    }
}

page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
	body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
	.form-card { border: none; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 30px; background: linear-gradient(145deg, #fffbf2 0%, #fff0d9 100%); border: 1px solid #ffcc80; }
	.step h4 { color: #fff; background: linear-gradient(90deg, #e35d05, #ff8e00); border-radius: 8px; padding: 15px 25px; margin-bottom: 25px; font-weight: 800; display: flex; align-items: center; border-bottom: 4px solid #b34a04; letter-spacing: 0.5px; }
    .step h4 i { margin-right: 12px; font-size: 1.5rem; }
	.section-box { border: 1px solid #d1d9e6; border-radius: 12px; margin-bottom: 30px; padding: 25px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
	.alert-primary { background-color: #e3f2fd; border: none; border-left: 6px solid #1976d2; color: #0d47a1; font-weight: 800; padding: 12px 20px; margin-bottom: 20px; font-size: 1.1rem; }
	.table-section { width: 100%; border-collapse: collapse; background: #fff; }
	.table-section th { background-color: #e9ecef; color: #212529; font-weight: 800; border: 1px solid #adb5bd; padding: 12px; text-align: center; text-transform: uppercase; font-size: 0.9rem; }
	.table-section td { border: 1px solid #dee2e6; padding: 10px; }
	.form-control { border-radius: 6px; border: 1px solid #999; font-weight: 600; font-size: 0.95rem; }
	.form-control:focus { border-color: #e35d05; box-shadow: 0 0 0 0.2rem rgba(227, 93, 5, 0.2); }
    #googlemap { border: 2px solid #555; border-radius: 10px; width: 100%; height: 260px; background: #fff; }
    .blinking-text { animation: blink 1.5s step-start infinite; color: #d32f2f; font-size: 0.85rem; font-weight: 800; }
	@keyframes blink { 50% { opacity: 0; } }
    .label-col { display: flex; align-items: center; font-weight: 800; color: #333; font-size: 1rem; }
    button { font-weight: 800 !important; }
    .pax-row { display: flex; flex-wrap: wrap; margin-right: -10px; margin-left: -10px; }
    .pax-left-col { flex: 0 0 40%; max-width: 40%; padding: 0 10px; }
    .pax-gps-col { flex: 0 0 15%; max-width: 15%; padding: 0 10px; }
    .pax-map-col { flex: 0 0 45%; max-width: 45%; padding: 0 10px; }
</style>

<?php
page_header_end();
page_sidebar();
?>

<div class="row">
	<div class="col-md-12">
		<div class="card form-card">
			<div class="card-body p-4">
				<form id="user_form" method="post">
					<div id="steps-container">
						
						<!-- Step 1 Start -->
						<div class="step" style="display: block;">
							
							<h4><i class="fas fa-building"></i>नेशनल को-ऑपरेटिव ऑर्गेनिक्स लिमिटेड समिति का विवरण </h4>
							
							<div class="col-sm-12">
								<div class="pax-row">
									
                                    <!-- 40% Left Column -->
									<div class="pax-left-col">
										
										<div class="form-group row mb-3">
											<label class="col-sm-4 label-col">मण्डल :</label>
											<div class="col-sm-8 px-1">
												<select name="division_disabled" id="division_name" class="form-control" tabindex="<?php echo $tab++; ?>" onchange="fill_district(this.value)" disabled style="background-color: #e9ecef;">
													<option value="">--Select--</option>
													<?php
                                                    $sql = 'SELECT sno, division_name FROM master_division';
                                                    if(!empty($filter_division)) $sql .= ' WHERE sno IN ('.implode(',',$filter_division).')';
													$divisions = execute_query($sql);
													if ($divisions && mysqli_num_rows($divisions) > 0) {
														while ($row = mysqli_fetch_assoc($divisions)) {
                                                            $sel = (count($filter_division) == 1 && in_array($row['sno'], $filter_division)) ? 'selected' : '';
															echo '<option value="' . $row['sno'] . '" '.$sel.'>' . $row['division_name'] . '</option>';
                                                            if($sel) echo '<input type="hidden" name="division" value="'.$row['sno'].'">';
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="form-group row mb-3">
											<label class="col-sm-4 label-col">जिला :</label>
											<div class="col-sm-8 px-1">
												<select name="district_disabled" id="district_name" class="form-control" tabindex="<?php echo $tab++; ?>" onchange="fill_tehseel(this.value)" disabled style="background-color: #e9ecef;">
													<option value="">--Select--</option>
													<?php
                                                    $sql = 'SELECT sno, district_name FROM master_district';
                                                    if(!empty($filter_district)) $sql .= ' WHERE sno IN ('.implode(',',$filter_district).')';
                                                    elseif (!empty($filter_division) && count($filter_division) == 1) $sql .= ' WHERE division_id = '.$filter_division[0];
                                                    else $sql .= ' WHERE 1=0';
													$districts = execute_query($sql);
													if ($districts && mysqli_num_rows($districts) > 0) {
														while ($row = mysqli_fetch_assoc($districts)) {
                                                            $sel = (count($filter_district) == 1 && in_array($row['sno'], $filter_district)) ? 'selected' : '';
															echo '<option value="' . $row['sno'] . '" '.$sel.'>' . $row['district_name'] . '</option>';
                                                            if($sel) echo '<input type="hidden" name="district" value="'.$row['sno'].'">';
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="form-group row mb-3">
											<label class="col-sm-4 label-col">तहसील :</label>
											<div class="col-sm-8 px-1">
												<select name="tehseel" id="tehseel_name" class="form-control" tabindex="<?php echo $tab++; ?>" onchange="fill_block(this.value)">
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    $sql = 'SELECT sno, tehseel_name FROM master_tehseel';
                                                    if(!empty($filter_tehseel)) $sql .= ' WHERE sno IN ('.implode(',',$filter_tehseel).')';
                                                    elseif (!empty($filter_district) && count($filter_district) == 1) $sql .= ' WHERE district_id = '.$filter_district[0];
                                                    else $sql .= ' WHERE 1=0';
                                                    $teh = execute_query($sql);
                                                    if($teh && mysqli_num_rows($teh) > 0){
                                                        while($row = mysqli_fetch_assoc($teh)) {
                                                            $sel = (count($filter_tehseel) == 1 && in_array($row['sno'], $filter_tehseel)) ? 'selected' : '';
                                                            echo '<option value="'.$row['sno'].'" '.$sel.'>'.$row['tehseel_name'].'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
											</div>
										</div>
										<div class="form-group row mb-3">
											<label class="col-sm-4 label-col">ब्लाक :</label>
											<div class="col-sm-8 px-1">
												<select name="block" id="block_name" class="form-control" tabindex="<?php echo $tab++; ?>">
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    $sql = 'SELECT sno, block_name FROM master_block';
                                                    if(!empty($filter_block)) $sql .= ' WHERE sno IN ('.implode(',',$filter_block).')';
                                                    elseif (!empty($filter_tehseel) && count($filter_tehseel) == 1) $sql .= ' WHERE tehseel_id = '.$filter_tehseel[0];
                                                    else $sql .= ' WHERE 1=0';
                                                    $blk = execute_query($sql);
                                                    if($blk && mysqli_num_rows($blk) > 0){
                                                        while($row = mysqli_fetch_assoc($blk)) {
                                                            $sel = (count($filter_block) == 1 && in_array($row['sno'], $filter_block)) ? 'selected' : '';
                                                            echo '<option value="'.$row['sno'].'" '.$sel.'>'.$row['block_name'].'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
											</div>
										</div>
                                        <div class="form-group row mb-3">
											<label class="col-sm-4 label-col">समिति का नाम :</label>
											<div class="col-sm-8 px-1">
												<input type="text" name="society_name" id="society_name_input" class="form-control" placeholder="Enter Name" tabindex="<?php echo $tab++; ?>">
											</div>
										</div>
									</div>

                                    <!-- 15% GPS Column -->
									<div class="pax-gps-col">
                                        <label class="small font-weight-bold mb-1" style="font-weight: 800 !important;">LATITUDE</label>
                                        <input type="text" id="lat" name="latitude" disabled="disabled" class="form-control mb-2" style="background-color: #eee; font-weight: 800;">
                                        <label class="small font-weight-bold mb-1" style="font-weight: 800 !important;">LONGITUDE</label>
                                        <input type="text" id="long" name="longitude" disabled="disabled" class="form-control mb-3" style="background-color: #eee; font-weight: 800;">
                                        <button type="button" class="btn btn-info p-1" onClick="getLocation();" style="background-color: #31b0d5; border-color: #269abc; width: 100%; height: 42px; font-weight: 800; font-size: 0.85rem;">लोकेशन रिफ्रेश करें</button>
                                        <div class="blinking-text mt-2 text-center">(लोकेशन मोबाईल से भरे)*</div>
									</div>

                                    <!-- 45% Map Column -->
                                    <div class="pax-map-col">
                                        <iframe id="googlemap" 
                                            src="https://maps.google.com/maps?q=26.8467,80.9462&hl=en&z=13&output=embed"
                                            width="100%" height="260px"
                                            style="border:1px solid #000; border-radius:10px;"
                                            allowfullscreen="" loading="lazy"></iframe>
                                    </div>

								</div>
							</div>

							<hr class="my-4"/>

							<!-- Registration Details Section -->
							<div class="section-box shadow-sm">
								<div class="alert alert-primary"><i class="fas fa-id-card"></i> 1. पंजीकरण विवरण (Registration Details) </div>
								<div class="table-responsive">
                                    <table class="table-section" id="vivranTable">
                                        <thead>
                                            <tr>
                                                <th>पंजीकरण नंबर (Reg No)</th>
                                                <th>पंजीकरण तिथि (Date)</th>
                                                <th>कुल जैविक किसान (Total Organic Farmers)</th>
                                                <th width="60px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="reg_no[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><input type="date" name="reg_date[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><input type="number" name="total_farmers[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
								<div class="text-right mt-3">
									<button type="button" class="btn btn-info btn-sm" onclick="addVivranRow()">
										<i class="fas fa-plus"></i> नयी पंक्ति ऐड करें
									</button>
								</div>
							</div>

							<!-- Farmer Details Section -->
							<div class="section-box shadow-sm">
								<div class="alert alert-primary"><i class="fas fa-users"></i> 2. किसान का विवरण (Farmer Details) </div>
								<div class="table-responsive">
                                    <table class="table-section" id="farmerTable">
                                        <thead>
                                            <tr>
                                                <th>किसान का नाम (Farmer Name)</th>
                                                <th>मोबाइल नंबर</th>
                                                <!-- <th>जन्म तिथि (DOB)</th> -->
                                                <th>कुल भूमि (हेक्टेयर)</th>
                                                <th>अतिरिक्त (Remark)</th>
                                                <th width="60px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="farmer_name[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><input type="text" name="mobile[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <!-- <td><input type="date" name="dob[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td> -->
                                                <td><input type="text" name="total_land[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td><input type="text" name="extra[]" class="form-control" tabindex="<?php echo $tab++; ?>"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
								<div class="text-right mt-3">
									<button type="button" class="btn btn-info btn-sm" onclick="addFarmerRow()">
										<i class="fas fa-plus"></i> नयी पंक्ति ऐड करें
									</button>
								</div>
							</div>

							<div class="text-center pb-5">
								<button type="submit" class="btn btn-success btn-lg px-5 shadow">
                                    <i class="fas fa-save"></i> Submit
                                </button>
							</div>

						</div>
						<!-- Step 1 End -->
						
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
    const actionUrl = 'scripts/survey_form_ajax.php';

    function fill_district(val){
        var data = {"id":"dist", "val":val};
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: data,
            success: function(response){
                var txt = '<option value="">--Select--</option>';
                var dataArr = JSON.parse(response);
                $.each(dataArr, function(key, value){
                    txt += '<option value="'+value.id+'">'+value.district_name+'</option>';
                });
                $("#district_name").html(txt);
                $("#tehseel_name").html('<option value="">--Select--</option>');
                $("#block_name").html('<option value="">--Select--</option>');
            }
        });
    }

    function fill_tehseel(val){
        var data = {id:"tehseel", val:val};
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: data,
            success: function(response){
                var txt = '<option value="">--Select--</option>';
                var dataArr = JSON.parse(response);
                $.each(dataArr, function(key, value){
                    txt += '<option value="'+value.id+'">'+value.tehseel_name+'</option>';
                });
                $("#tehseel_name").html(txt);
                $("#block_name").html('<option value="">--Select--</option>');
            }
        });
    }

    function fill_block(val){
        var data = {id:"block", val:val};
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: data,
            success: function(response){
                var txt = '<option value="">--Select--</option>';
                var dataArr = JSON.parse(response);
                $.each(dataArr, function(key, value){
                    txt += '<option value="'+value.id+'">'+value.block_name+'</option>';
                });
                $("#block_name").html(txt);
            }
        });
    }

	function addVivranRow() {
		const table = document.getElementById('vivranTable').getElementsByTagName('tbody')[0];
		const newRow = table.insertRow();
		newRow.innerHTML = `
			<td><input type="text" name="reg_no[]" class="form-control"></td>
			<td><input type="date" name="reg_date[]" class="form-control"></td>
			<td><input type="number" name="total_farmers[]" class="form-control"></td>
			<td class="text-center">
				<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
			</td>
		`;
	}

	function addFarmerRow() {
		const table = document.getElementById('farmerTable').getElementsByTagName('tbody')[0];
		const newRow = table.insertRow();
		newRow.innerHTML = `
			<td><input type="text" name="farmer_name[]" class="form-control"></td>
			<td><input type="text" name="mobile[]" class="form-control"></td>
			<td><input type="date" name="dob[]" class="form-control"></td>
			<td><input type="text" name="total_land[]" class="form-control"></td>
			<td><input type="text" name="extra[]" class="form-control"></td>
			<td class="text-center">
				<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(this)"><i class="fas fa-times"></i></button>
			</td>
		`;
	}

	function removeRow(btn) {
		const row = btn.parentNode.parentNode;
		const tbody = row.parentNode;
		if (tbody.rows.length > 1) {
			tbody.removeChild(row);
		} else {
			alert("कम से कम एक पंक्ति आवश्यक है।");
		}
	}

	function getLocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		} else {
			alert("Geolocation is not supported by this browser.");
		}
	}

	function showPosition(position) {
		document.getElementById("lat").value = position.coords.latitude;
		document.getElementById("long").value = position.coords.longitude;
        const latLong = position.coords.latitude + "," + position.coords.longitude;
        document.getElementById("googlemap").src = "https://maps.google.com/maps?q=" + latLong + "&hl=en&z=13&output=embed";
	}
</script>

<?php
page_footer_end();
?>
