<?php
include("scripts/settings.php");
$tab = 1;
$msg = '';
//print_r($_POST);
if (isset($_POST['submit'])) {
	if ($_POST['society_name'] == '') {
		$msg .= '<li>Please Enter Society Name.</li>';
	}
	if ($msg == '') {
		if ($_POST['edit_sno'] != '') {
			$sql = 'update test2 set col1="' . $_POST['division_name'] . '",
			col2="' . $_POST['district_name'] . '",
			col3="' . $_POST['society_type'] . '",
			col4="' . $_POST['society_name'] . '",
			col5="' . $_POST['tehseel_name'] . '",
			col6="' . $_POST['block_name'] . '"
			where sno=' . $_POST['edit_sno'];
			//echo $sql;
			execute_query($sql);
			if (mysqli_error($db)) {
				$msg .= '<li>Error # 2 : ' . $sql . '</li>';
			} else {
				$msg .= '<li>Update sucessful.</li>';
			}
		} else {
			$sql = 'insert into `test2` (col1, col2, col3, col4, col5, col6) values ("' . $_POST['division_name'] . '", "' . $_POST['district_name'] . '", "' . $_POST['society_type'] . '", "' . $_POST['society_name'] . '", "' . $_POST['tehseel_name'] . '", "' . $_POST['block_name'] . '")';
			//echo $sql;
			execute_query($sql);

			if (mysqli_error($db)) {
				$msg .= '<li>Error # 1 : ' . mysqli_error($db) . ' >> ' . $sql . '</li>';

			}
		}
	}
}

if (isset($_GET['id'])) {
	$sql = 'select * from test2 where sno=' . $_GET['id'];
	$result = execute_query($sql);
	$row_edit = mysqli_fetch_array($result);

} else {
	$row_edit['col1'] = '';
	$row_edit['col2'] = '';
	$row_edit['col3'] = '';
	$row_edit['col4'] = '';
	$row_edit['col5'] = '';
	$row_edit['col6'] = '';
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
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data"
							id="user_form" name="user_form">

							<div class="col-sm-12">

								<div class="row">
									<div class="col-sm-4 form-group">
										<label>मण्डल</label>
										<select name="division_name" id="division_name" tabindex="<?php echo $tab++; ?>"
											class="form-control" onChange="fill_district(this.value);">
											<option value="">--Select--</option>
											<?php
											$sql = 'select * from master_division';
											$result_division = execute_query($sql);
											while ($row_division = mysqli_fetch_assoc($result_division)) {
												echo '<option value="' . $row_division['sno'] . '" ';
												if (isset($_GET['id'])) {
													if ($row_edit['col1'] == $row_division['sno']) {
														echo ' selected="selected" ';
													}
												}
												echo '>' . $row_division['division_name'] . '</option>';
											}
											?>
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label>जनपद</label>
										<select name="district_name" id="district_name" tabindex="<?php echo $tab++; ?>"
											class="form-control" onChange="fill_tehseel(this.value);">
											<option value="<?php echo $row_edit['col2']; ?>">
												<?php
												$sql = 'select * from master_district where sno="' . $row_edit['col2'] . '"';
												$result_district = execute_query($sql);
												$row_district = mysqli_fetch_assoc($result_district);
												if (isset($row_district['district_name'])) {
													echo $row_district['district_name'];
												}
												?>


											</option>
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label>तहसील</label>
										<select name="tehseel_name" id="tehseel_name" tabindex="<?php echo $tab++; ?>"
											class="form-control" onChange="fill_block(this.value);">
											<option value="<?php echo $row_edit['col5']; ?>">
												<?php
												$sql = 'select * from master_tehseel where sno="' . $row_edit['col5'] . '"';
												$result_tehseel = execute_query($sql);
												$row_tehseel = mysqli_fetch_assoc($result_tehseel);

												if (isset($row_tehseel['tehseel_name'])) {
													echo $row_tehseel['tehseel_name'];
												}
												?>
											</option>
										</select>
									</div>


								</div>
								<div class="row">
									<div class="col-sm-4 form-group">
										<label>विकासखंड</label>
										<select name="block_name" id="block_name" tabindex="<?php echo $tab++; ?>"
											class="form-control" onChange="fill_type(this.value);">
											<option value="<?php echo $row_edit['col6']; ?>">
												<?php
												$sql = 'select * from master_block where sno="' . $row_edit['col6'] . '"';
												$result_block = execute_query($sql);
												$row_block = mysqli_fetch_assoc($result_block);
												if (isset($row_block['block_name'])) {
													echo $row_block['block_name'];
												}
												?>
											</option>
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label>समिति का प्रकार</label>
										<select name="society_type" id="society_type" tabindex="<?php echo $tab++; ?>"
											class="form-control" onChange="fill_society(this.value);"
											value="<?php if (isset($_GET['id'])) {
												echo $row_edit['col3'];
											} ?>">
											<option value="<?php echo $row_edit['col3']; ?>">
												<?php
												$sql = 'select * from master_society_type where sno="' . $row_edit['col3'] . '"';
												$result_type = execute_query($sql);
												$row_type = mysqli_fetch_assoc($result_type);

												if (isset($row_type['type_name'])) {
													echo $row_type['type_name'];
												}
												?>
											</option>
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label>समिति का नाम</label>
										<input type="text" name="society_name" id="society_name"
											tabindex="<?php echo $tab++; ?>" class="form-control"
											value="<?php if (isset($_GET['id'])) {
												echo $row_edit['col4'];
											} ?>"></select>
									</div>
								</div>

								<input type="submit" class="btn btn-info btn-fill pull-right" value="ADD SOCIETY"
									name="submit" id="submit" />
								<input type="hidden" name="edit_sno"
									value="<?php if (isset($_GET['id'])) {
										echo $_GET['id'];
									} ?>" />
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
				<table class="table table-hover table-striped">
					<thead>
						<?php
						$i = 1;
						$sql = 'select * from test2 where col4 !="SocietyName"';
						//echo $sql;											
						$result_data = execute_query($sql); ?>

						<tr>
							<th colspan="6">
								<?php
								include('pagination/paginate.php'); //include of paginat page
								$total_results = mysqli_num_rows($result_data);
								$total_pages = ceil($total_results / $per_page);//total pages we going to have
								$tpages = $total_pages;
								if (isset($_GET['page'])) {
									$show_page = $_GET['page'];             //it will telles the current page
									if ($show_page > 0 && $show_page <= $total_pages) {
										$start = ($show_page - 1) * $per_page;
										$end = $start + $per_page;
									} else {
										// error - show first set of results
										$start = 0;
										$end = $per_page;
									}
								} else {
									// if page isn't set, show first set of results
									$_GET['page'] = 1;
									$show_page = 1;
									$start = 0;
									$end = $per_page;
								}
								// display pagination
								$page = intval($_GET['page']);

								if ($page <= 0)
									$page = 1;


								$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . (isset($_GET['details']) ? '&details=1' : '');
								echo '<div class="pagination"><ul>';
								if ($total_pages > 1) {
									echo paginate($reload, $show_page, $total_pages);
								}
								echo "</ul></div>";
								?>
							</th>
						</tr>

					</thead>
				</table>
				<table class="table table-hover table-striped text-center">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Division Name</th>
							<th>District Name</th>
							<th>Tehseel Name</th>
							<th>Block Name</th>
							<th>Society Type</th>
							<th>Society Name</th>
							<th>Society ID</th>
							<th>Edit</th>

						</tr>
					</thead>
					<tbody>
						<?php

						for ($pgid = $start; $pgid < $end; $pgid++) {
							//print_r($row);
							if ($pgid == $total_results) {
								break;
							}
							mysqli_data_seek($result_data, $pgid);
							$row = mysqli_fetch_array($result_data);

							$i = $pgid + 1;


							$sql_division = 'select * from master_division where sno = "' . $row['col1'] . '"';
							$result_division = mysqli_fetch_array(execute_query($sql_division));


							$sql_district = 'select * from master_district where sno = "' . $row['col2'] . '"';
							$sql_district = 'select * from master_district where sno = "' . $row['col2'] . '"';
							$result_district = mysqli_fetch_array(execute_query($sql_district));
							if (!isset($result_district['district_name'])) {
								$result_district['district_name'] = '';
								$result_district['sno'] = '';
							}

							$sql_tehseel = 'select * from master_tehseel where sno = "' . $row['col5'] . '"';
							$result_tehseel = mysqli_fetch_array(execute_query($sql_tehseel));
							if (!isset($result_tehseel['tehseel_name'])) {
								$result_tehseel['tehseel_name'] = '';
								$result_tehseel['sno'] = '';
							}


							$sql_block = 'select * from master_block where sno = "' . $row['col6'] . '"';
							$result_block = mysqli_fetch_array(execute_query($sql_block));
							if (!isset($result_block['block_name'])) {
								$result_block['block_name'] = '';
								$result_block['sno'] = '';
							}

							$sql_type = 'select * from master_society_type where sno = "' . $row['col3'] . '"';
							$result_type = mysqli_fetch_array(execute_query($sql_type));
							if (!isset($result_type['block_name'])) {
								$result_type['type_name'] = '';
								$result_type['sno'] = '';
							}

							?>
							<tr>
								<td><?php echo $i++; ?></td>
								<td><?php echo $result_division['division_name']; ?></td>
								<td><?php echo $result_district['district_name']; ?></td>
								<td><?php echo $result_tehseel['tehseel_name']; ?></td>
								<td><?php echo $result_block['block_name']; ?></td>
								<td><?php echo $result_type['type_name']; ?></td>
								<td><?php echo $row['col4']; ?></td>
								<td><?php echo $row['sno']; ?></td>
								<td class="text-center"><a href="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $row['sno']; ?>"
										alt="Edit" data-toggle="tooltip" title="Edit"><span class="far fa-edit"
											aria-hidden="true"></span></td>
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

</script>


<?php
page_footer_start();
?>
<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<?php
page_footer_end();
?>