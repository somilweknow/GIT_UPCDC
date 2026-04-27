<?php
include("scripts/settings.php");
error_reporting(E_ALL);
$msg = '';
$tab = 1;
?>

<?php
page_header_start();
page_header_end();
page_sidebar();

if (isset($_POST['submit'])) {
    // store posted filter values to session
    foreach ($_POST as $k => $v) {  //k=key,v=value
        $_SESSION['sale_' . $k] = $v;
    }
}

// Activation / Deactivation
if (isset($_GET['dis'])) {
    $id = intval($_GET['dis']);
    $sql = 'UPDATE cooperatives SET status=1 WHERE sno=' . $id;
    execute_query($sql);
    $msg .= '<p class="text-alert">Society Disabled</p>';
}

if (isset($_GET['act'])) {
    $id = intval($_GET['act']);
    $sql = 'UPDATE cooperatives SET status=0 WHERE sno=' . $id;
    execute_query($sql);
    $msg .= '<p class="text-success">Society Activated</p>';
}
// echo $sql;
?>
<style>
    #general_stat_table {
        width: 100%;
        border-collapse: collapse;
    }

    #general_stat_table thead th {
        position: sticky;
        top: 0;
        background-color: #fd7e14;
        color: #333;
        z-index: 1;
    }

    #general_stat_table th,
    #general_stat_table td {
        padding: 8px;
        border: 1px solid #333;
    }
</style>
<script src="js/survey_validate.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <?php echo $msg; ?>
            </div>
            <div class="card-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" novalidate method="post" autocomplete="off"
                      enctype="multipart/form-data" id="user_form" name="user_form">
                    <div class="row">
                        <div class="col-md-3 pr-1">
                            <div class="form-group">
                                <label>District Name</label>

                                <select name="district_name" id="district_name" tabindex="<?php echo $tab++; ?>"
                                        class="form-control">
                                    <option value="ALL">ALL</option>
                                    <?php
                                    $sql = 'SELECT * FROM master_district';
                                    $result_district = execute_query($sql);
                                    while ($row_district = mysqli_fetch_assoc($result_district)) {
                                        $val = isset($row_district['dist_lgd_code']) ? $row_district['dist_lgd_code'] : $row_district['sno'];
                                        echo '<option value="' . $val . '" ';
                                        if (isset($_SESSION['sale_district_name'])) {
                                            if ($_SESSION['sale_district_name'] == $val) {
                                                echo ' selected="selected" ';
                                            }
                                        }
                                        echo '>' . $row_district['district_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
						<!-- Add Society Type filter -->
						<div class="col-md-3 pr-1">
							<div class="form-group">
								<label>Society Type</label>
								<select name="society_type" id="society_type" tabindex="<?php echo $tab++; ?>" class="form-control">
									<option value="ALL">ALL</option>
									<?php
									$q = 'SELECT * FROM master_society_type ORDER BY society_type_id';
									$res = execute_query($q);
									while ($r = mysqli_fetch_assoc($res)) {
										$val = $r['society_type_id'];
										echo '<option value="' . htmlspecialchars($val) . '"';
										if (isset($_SESSION['sale_society_type']) && $_SESSION['sale_society_type'] == $val) echo ' selected';
										echo '>' . htmlspecialchars($r['society_type_name']) . '</option>';
									}
									?>
								</select>
							</div>
						</div>

						<!-- Add Functional Status filter -->
						<div class="col-md-3 pr-1">
							<div class="form-group">
								<label>Functional Status</label>
								<select name="functional_status" id="functional_status" tabindex="<?php echo $tab++; ?>" class="form-control">
									<option value="ALL">ALL</option>
									<option value="1" <?php if (isset($_SESSION['sale_functional_status']) && $_SESSION['sale_functional_status'] === '1') echo ' selected'; ?>>Functional</option>
									<option value="2" <?php if (isset($_SESSION['sale_functional_status']) && $_SESSION['sale_functional_status'] === '2') echo ' selected'; ?>>Liquidation</option>
									<option value="3" <?php if (isset($_SESSION['sale_functional_status']) && $_SESSION['sale_functional_status'] === '3') echo ' selected'; ?>>Non-functional</option>
								</select>
							</div>
						</div>

                    </div>
                    <input type="submit" class="btn btn-info btn-fill pull-right" name="submit" value="Submit">
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Build base SQL and apply filters from session (if set)
$unint = 0;
$int = 0;

// Base query
$sql = 'SELECT * FROM cooperatives WHERE district_code != "" ';

if (isset($_SESSION['sale_district_name']) && $_SESSION['sale_district_name'] != '' && $_SESSION['sale_district_name'] != 'ALL') {
    $district_filter = addslashes($_SESSION['sale_district_name']);
    $sql .= " AND district_code = '" . $district_filter . "' ";
}
if (isset($_SESSION['sale_society_type']) && $_SESSION['sale_society_type'] != '' && $_SESSION['sale_society_type'] != 'ALL') {
    $stype = addslashes($_SESSION['sale_society_type']);
    $sql .= " AND sector_of_operation = '" . $stype . "' ";
}

if (isset($_SESSION['sale_functional_status']) && $_SESSION['sale_functional_status'] != '' && $_SESSION['sale_functional_status'] != 'ALL') {
    $fstatus = intval($_SESSION['sale_functional_status']);
    $sql .= " AND functional_status = " . $fstatus . " ";
}

$sql .= ' ORDER BY district_code, sector_of_operation, id ';
// echo $sql;
$result_data = execute_query($sql);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card strpied-tabled-with-hover">
            <div id="general_stat">
                <div class="card-body table-full-width table-responsive">
                    <table id="general_stat_table" class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th colspan="6">
                                <?php
                                include('pagination/paginate.php'); //include of paginat page
                                $total_results = mysqli_num_rows($result_data);
                                $total_pages = ceil($total_results / $per_page);
                                $tpages = $total_pages;
                                if (isset($_GET['page'])) {
                                    $show_page = $_GET['page'];
                                    if ($show_page > 0 && $show_page <= $total_pages) {
                                        $start = ($show_page - 1) * $per_page;
                                        $end = $start + $per_page;
                                    } else {
                                        $start = 0;
                                        $end = $per_page;
                                    }
                                } else {
                                    $_GET['page'] = 1;
                                    $show_page = 1;
                                    $start = 0;
                                    $end = $per_page;
                                }
                                $page = intval($_GET['page']);
                                if ($page <= 0) $page = 1;

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

                    <?php
                    $mandal_text = '';
                    if (isset($_SESSION['sale_district_name']) && $_SESSION['sale_district_name'] != '' && $_SESSION['sale_district_name'] != 'ALL') {
                        $q = mysqli_fetch_assoc(execute_query("SELECT district_name FROM master_district WHERE dist_lgd_code = '" . addslashes($_SESSION['sale_district_name']) . "' LIMIT 1"));
                        if ($q) $mandal_text .= 'District - ' . $q['district_name'] . ', ';
                    }
                    if (isset($_SESSION['sale_tehseel_name']) && $_SESSION['sale_tehseel_name'] != '' && $_SESSION['sale_tehseel_name'] != 'ALL') {
                        $q = mysqli_fetch_assoc(execute_query("SELECT tehseel_name FROM master_tehseel WHERE tehseel_lgd_code = '" . addslashes($_SESSION['sale_tehseel_name']) . "' LIMIT 1"));
                        if ($q) $mandal_text .= 'Tehseel - ' . $q['tehseel_name'] . ', ';
                    }
                    $mandal_text = rtrim($mandal_text, ', ');
                    ?>

                    <?php if ($mandal_text != ''): ?>
                        <h5 style="margin: 10px 0; font-weight: bold; text-align: center;"><?php echo $mandal_text; ?></h5>
                    <?php endif; ?>

                    <table class="table table-hover table-striped">
                        <thead>
                        <tr class="no-print">
                            <td colspan="14" float="left" class="no-print">
                                <a href="visit_report_export.php"><input type="button"
                                                                         style="margin-top:20px; color:#ffffff;" name="student_ledger"
                                                                         class="form-control btn btn-danger"
                                                                         value="Download In Excel"></a></span>
                            </td>
                        </tr>
                        <tr>
                            <th>S.No.</th>
                            <th>Cooperatives_ID</th>
                            <th>Society Type</th>
                            <th>Society Name</th>
                            <th>District Name</th>
                            <th>Functional Status</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        for ($pgid = $start; $pgid < $end; $pgid++) {
                            if ($pgid == $total_results) {
                                break;
                            }
                            mysqli_data_seek($result_data, $pgid);
                            $row = mysqli_fetch_array($result_data);

                            $i = $pgid + 1;

                            // fetch district by LGD code stored in cooperatives.district_code
                            $sql_district = 'SELECT * FROM master_district WHERE dist_lgd_code = "' . addslashes($row['district_code']) . '" LIMIT 1';
                            $result_district = mysqli_fetch_array(execute_query($sql_district));
                            if (!isset($result_district['district_name'])) {
                                $result_district['district_name'] = '';
                                $result_district['sno'] = '';
                            }

                            $sql_type = 'SELECT * FROM master_society_type WHERE society_type_id = "' . addslashes($row['sector_of_operation']) . '" LIMIT 1';
                            $result_type = mysqli_fetch_array(execute_query($sql_type));

                            // functional status mapping
                            switch ($row['functional_status']) {
                                case 1:
                                    $functional_text = "Functional";
                                    break;
                                case 2:
                                    $functional_text = "Liquidation";
                                    break;
                                case 3:
                                    $functional_text = "Non-functional";
                                    break;
                                default:
                                    $functional_text = "—";
                            }
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['cooperative_id']); ?></td>
                                <td><?php echo htmlspecialchars($result_type['society_type_name']) . ' <small>(' . htmlspecialchars($result_type['society_type_id']) . ')</small>'; ?></td>
                                <td><?php echo htmlspecialchars($row['cooperative_society_name']) . ' <small>(' . htmlspecialchars($row['id']) . ')</small>'; ?></td>
                                <td><?php echo htmlspecialchars($result_district['district_name']) . ' <small>(' . htmlspecialchars($result_district['dist_lgd_code']) . ')</small>'; ?></td>
                                <td><?php echo $functional_text; ?></td>
                            </tr>
                            <?php
                        }
                        echo 'Initiated:' . $int . ' >> Unintiated:' . $unint;
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
page_footer_start();
?>

<script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<?php
page_footer_end();
?>
