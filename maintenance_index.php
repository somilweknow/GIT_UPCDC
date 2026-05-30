<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
page_header_start();
page_header_end();
page_sidebar();

$society_filter = "";
$current_role = $_SESSION['user_type'] ?? '';

// District Filter
if (!empty($_SESSION['district_id'])) {
    if (is_array($_SESSION['district_id'])) {
        $district_ids = array_map('intval', $_SESSION['district_id']);
        $district_list = implode(',', $district_ids);
        $society_filter = " AND all_soc.district_id IN ($district_list)";
    } else {
        $district_list = intval($_SESSION['district_id']);
        $society_filter = " AND all_soc.district_id = $district_list";
    }
} else {
    if (isset($_GET['dist']) && is_numeric($_GET['dist'])) {
        $district_id = intval($_GET['dist']);
        $society_filter = " AND all_soc.district_id = $district_id";
    }
}

$sql_list = "SELECT all_soc.society_id, all_soc.soc_name, all_soc.soc_type, all_soc.type_val, COALESCE(dist.district_name, all_soc.district_id) AS district_name, COALESCE(divsn.division_name, all_soc.division_id) AS division_name, NULL AS society_registration_no, all_soc.latitude, all_soc.longitude, all_soc.soc_sec_name AS secretary_name, all_soc.soc_sec_mob AS secretary_mobile, NULL AS president_name, NULL AS is_filled, NULL AS filling_date, NULL AS built_building_status FROM (SELECT sno AS society_id, samiti_naam AS soc_name, 'Block Union' AS soc_type, 'block_union' AS type_val, block_union.janpad_name AS district_id, mandal_name AS division_id, sachiv_name AS soc_sec_name, NULL AS soc_sec_mob, latitude, longitude FROM block_union WHERE is_deleted = 0
UNION ALL 
SELECT sno, society_name, 'Marketing', 'marketing', marketing.district_id AS district_id, division_id AS division_id, NULL AS soc_sec_name, secretary_mob AS soc_sec_mob, latitude, longitude FROM marketing WHERE is_deleted = 0 
UNION ALL 
SELECT sno, society_name,'UPSS' AS soc_type, 'upss' AS type_val, upss.janpad_name AS district_id, mandal_name AS division_id, sachiv_name AS soc_sec_name, sachiv_no AS soc_sec_mob, latitude, longitude FROM upss WHERE is_deleted = 0 
UNION ALL 
SELECT sno, society_name, 'Jila Sehkari', 'jila_sehkari', jila_sehkari.janpad_name AS district_id, mandal_name AS division_id, sachiv_name AS soc_sec_name, sachiv_no AS soc_sec_mob, latitude, longitude FROM jila_sehkari WHERE is_deleted = 0) AS all_soc LEFT JOIN master_district dist ON dist.sno = all_soc.district_id LEFT JOIN master_division divsn ON divsn.sno = all_soc.division_id LEFT JOIN maintenance m ON m.society_id = all_soc.society_id AND m.society_type = all_soc.type_val WHERE (m.sno IS NULL OR m.status = 0) $society_filter ORDER BY all_soc.type_val ASC, all_soc.soc_name ASC";

$result_list = execute_query($sql_list);

$categorized_data = [
    'block_union' => ['name' => 'ब्लॉक यूनियन (सहकारी संघ)', 'items' => []],
    'jila_sehkari' => ['name' => 'जिला सहकारी विकास संघ', 'items' => []],
    'marketing' => ['name' => 'सहकारी क्रय-विक्रय', 'items' => []],

    'upss' => ['name' => 'केंद्रिय उपभोक्ता सहकारी संघ', 'items' => []],
];

if ($result_list && mysqli_num_rows($result_list) > 0) {
    while ($row = mysqli_fetch_assoc($result_list)) {
        if (isset($categorized_data[$row['type_val']])) {
            $categorized_data[$row['type_val']]['items'][] = $row;
        }
    }
}

// Logic: Sorting categories based on society count (fewest first - as per "min societies")
uasort($categorized_data, function ($a, $b) {
    return count($a['items']) - count($b['items']);
});

?>
<?php

$division_id = $_SESSION['division_id'][0] ?? 0;
$district_id = $_SESSION['district_id'][0] ?? 0;

$division_name = '';
$district_name = '';

$res_div = execute_query("SELECT division_name FROM master_division WHERE sno='$division_id'");
if($row_div = mysqli_fetch_assoc($res_div)){
    $division_name = $row_div['division_name'];
}

$res_dist = execute_query("SELECT district_name FROM master_district WHERE sno='$district_id'");
if($row_dist = mysqli_fetch_assoc($res_dist)){
    $district_name = $row_dist['district_name'];
}
?>
<style>
    :root {
        --primary-blue: #3498db;
        --dark-blue: #2980b9;
        --light-bg: #f4f7f6;
    }

    .report-header {
        background: var(--primary-blue);
        color: white;
        padding: 15px;
        text-align: center;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .report-note {
        color: #e74c3c;
        font-weight: 500;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .table-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .custom-report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .custom-report-table th {
        font-size: 18px;
        background-color: #f8faff;
        color: #2c3e50;
        font-weight: 700;
        text-align: center;
        padding: 12px 8px;
        border: 1px solid #e1e8ed;
        text-transform: uppercase;
        vertical-align: middle;
    }

    .custom-report-table td {
        font-size: 16px;
        padding: 10px 8px;
        border: 1px solid #e1e8ed;
        vertical-align: middle;
        color: #444;
        font-weight: 600;
    }

    .custom-report-table tr:hover {
        background-color: #fcfdfe;
    }

    .category-row td {
        background-color: #f1f7ff !important;
        font-weight: 800;
        color: #1e3a8a;
        font-size: 19px;
        padding: 12px 15px !important;
        border-left: 5px solid #2980b9 !important;
    }

    .btn-action {
        padding: 4px 10px;
        font-size: 11px;
        font-weight: bold;
        border-radius: 4px;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 2px;
    }

    .btn-edit {
        background: #3498db;
        color: white !important;
    }

    .btn-delete {
        background: #e74c3c;
        color: white !important;
    }

    .badge-status {
        font-size: 10px;
        padding: 3px 6px;
        border-radius: 10px;
        font-weight: bold;
    }

    .badge-filled {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .badge-pending {
        font-size: 12px;
        background: #fff3e0;
        color: #ef6c00;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="report-header">
            लंबित समितियों की सूची
        </div>

        <div class="d-flex justify-content-center mb-4 gap-3">
            <a href="maintenance_index.php" class="btn btn-primary"
                style="font-weight: bold; border-radius: 20px; padding: 10px 25px; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
                <i class="fa fa-clock"></i> Pening Societies
            </a>
            <a href="maintenance_complete_index.php" class="btn btn-outline-success"
                style="font-weight: bold; border-radius: 20px; padding: 10px 25px;">
                <i class="fa fa-check-circle"></i> Filled Societies
            </a>
        </div>
        <h5 style="margin:10px 0;font-weight:bold;text-align:center;">
            Division: <?php echo $division_name; ?>
            |
            District: <?php echo $district_name; ?>
        </h5>



        <?php
        $total_records = 0;
        foreach ($categorized_data as $type_key => $data) {
            if (count($data['items']) == 0)
                continue;

            $category_sno = 1;
            ?>
            <div class="table-container table-responsive mb-4" style="border-top: 4px solid var(--primary-blue);">
                <h3
                    style="color: #1e3a8a; font-weight: 800; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                    <?php echo $data['name']; ?>
                </h3>
                <table class="custom-report-table table-bordered">
                    <thead>
                        <tr>
                            <th width="10%">Action</th>
                            <th width="4%">क्रम</th>


                            <th width="15%">समिति का नाम</th>
                            <th width="10%">भवन की स्थिति</th>
                            <th width="8%">Latitude</th>
                            <th width="8%">Longitude</th>
                            <th width="15%">सचिव का नाम</th>
                            <th width="10%">सचिव का मोबाइल नंबर</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data['items'] as $row) {
                            $soc_name = trim($row['soc_name'] ?? '');
                            if (empty($soc_name)) {
                                $soc_name = $row['district_name'] ?? 'N/A';
                            }
                            $edit_url = "visit_maintenance.php?exdid=" . intval($row['society_id']) . "&type=" . urlencode($row['type_val']);

                            echo '<a href="' . $edit_url . '" target="_blank"></a>';
                            echo '<tr>';
                            echo '<td class="text-center">
                                    <a href="' . $edit_url . '" target="_blank" class="btn-action btn-edit">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>';

                            // Delete button restricted to superadmin (sadmin/superadmin)
                            if (in_array($current_role, ['sadmin', 'superadmin'])) {
                                echo '<a href="javascript:void(0)" onclick="confirmDelete(' . $row['society_id'] . ', \'' . $row['type_val'] . '\')" class="btn-action btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>';
                            }

                            echo '</td>';
                            echo '<td class="text-center">' . $category_sno++ . '</td>';
                            $total_records++;


                            echo '<td>';
                            echo '<strong>' . htmlspecialchars($soc_name) . '</strong>';
                            echo '<br><span class="badge-status badge-pending">अनारंभ</span>';
                            echo '</td>';
                            echo '<td>';
                            $status_map = [
                                'good' => '<span class="badge badge-success">अच्छा</span>',
                                'repairable' => '<span class="badge badge-warning">मरम्मत योग्य</span>',
                                'discarded' => '<span class="badge badge-danger">जर्जर</span>',
                                'not_available' => '<span class="badge badge-secondary">उपलब्ध नहीं</span>'
                            ];
                            $building_status = $row['built_building_status'] ?? null;
                            echo $status_map[$building_status] ?? '-';
                            echo '</td>';
                            // Output latitude, longitude, secretary info
                            echo '<td>' . htmlspecialchars($row['latitude'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($row['longitude'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($row['secretary_name'] ?? '') . '</td>';
                            echo '<td>' . htmlspecialchars($row['secretary_mobile'] ?? '') . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }

        if ($total_records == 0) {
            echo '<div class="table-container text-center"><h5>कोई रिकॉर्ड नहीं मिला</h5></div>';
        }
        ?>
    </div>
</div>

<script>
    function confirmDelete(id, type) {
        if (confirm('क्या आप वाकई इस रिकॉर्ड को हटाना चाहते हैं?')) {
            // Implementation for delete can be added here
            alert('Delete functionality to be implemented for Society ID: ' + id + ' (' + type + ')');
        }
    }
</script>

<?php
page_footer_start();
page_footer_end();
?>