<?php
include("scripts/settings.php");

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['role'];
$user_district = $_SESSION['district'] ?? '';
// District IDs allotted to the user (from session set at login)
$user_district_ids = isset($_SESSION['districts']) ? array_map('intval', (array)$_SESSION['districts']) : [];
$user_district_ids = array_values(array_filter($user_district_ids, function($d){ return $d > 0; }));

$allowed_roles = ['sadmin', 'admin', 'report', 'dr'];
if (!in_array($user_role, $allowed_roles)) {
    die('<div style="text-align:center; padding:50px; color:red;"><h3>Access Denied</h3><p>यह रिपोर्ट केवल व्यवस्थापक के लिए उपलब्ध है</p></div>');
}

$filter_date = $_GET['date'] ?? date('Y-m-d');
$filter_district = $_GET['district'] ?? '';

$districts = [];
if (in_array($user_role, ['sadmin', 'admin', 'report'])) {
    $sql_districts = "SELECT district_id, district_name FROM district ORDER BY district_name";
    $result_districts = mysqli_query($conn, $sql_districts);
    if ($result_districts) {
        while ($row = mysqli_fetch_assoc($result_districts)) {
            $districts[] = $row;
        }
    }
} elseif ($user_role === 'dr') {
    if (!empty($user_district_ids)) {
        $in_ids = implode(',', array_map('intval', $user_district_ids));
        $sql_districts = "SELECT district_id, district_name FROM district WHERE district_id IN ($in_ids) ORDER BY district_name";
        $result_districts = mysqli_query($conn, $sql_districts);
        if ($result_districts) {
            while ($row = mysqli_fetch_assoc($result_districts)) {
                $districts[] = $row;
            }
        }
    }
}

$params = [];
$types = [];
$where_conditions = [];

if (!empty($filter_date)) {
    $where_conditions[] = "dfe.entry_date = ?";
    $params[] = $filter_date;
    $types[] = 's';
}

if (!empty($filter_district)) {
    $where_conditions[] = "dfe.district_id = ?";
    $params[] = $filter_district;
    $types[] = 's';
}

// Enforce district-level access for DR users (only their allotted districts)
if ($user_role === 'dr' && !empty($user_district_ids)) {
    $placeholders = implode(',', array_fill(0, count($user_district_ids), '?'));
    $where_conditions[] = "dfe.district_id IN ($placeholders)";
    foreach ($user_district_ids as $did) { $params[] = $did; $types[] = 'i'; }
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$sql = "SELECT 
    dfe.*,
    d.district_name,
    DATE_FORMAT(dfe.entry_date, '%d.%m.%Y') as formatted_date,
    DATE_FORMAT(dfe.created_at, '%d.%m.%Y %H:%i') as created_at_formatted,
    DATE_FORMAT(dfe.updated_at, '%d.%m.%Y %H:%i') as updated_at_formatted
FROM daily_fertilizer_entry dfe
LEFT JOIN district d ON dfe.district_id = d.district_id
$where_clause
ORDER BY dfe.entry_date DESC, d.district_name ASC";

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, implode('', $types), ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$report_data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $report_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>प्रतिदिन समीक्षा रिपोर्ट</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f0f3fb 100%);
            background-size: 400% 400%;
            overflow-x: hidden;
            animation: gradientBG 15s ease infinite;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: radial-gradient(ellipse at top left, rgba(102, 126, 234, 0.2) 0%, transparent 50%),
                        radial-gradient(ellipse at bottom right, rgba(118, 75, 162, 0.2) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0,0,0,0.1);
            margin: 20px auto;
            padding: 30px;
        }
        
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white;
            padding: 20px 15px;
            margin-bottom: 30px;
            text-align: center;
            border-radius: 10px; 
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.3); 
            position: relative;
        }
        
        .report-header h2 {
            margin-bottom: 5px;
        }
        .report-header p {
            margin-bottom: 0;
            font-size: 1.1rem;
        }

        .filter-section {
            background: #f8f9fa; 
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
        }
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .table-responsive {
            font-size: 13px;
            border-radius: 10px; 
            overflow: hidden; 
        }

        .table thead th {
            background: #667eea; 
            color: white; 
            padding: 12px 8px;
            font-weight: 600;
            text-align: center; 
            vertical-align: middle; 
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            border-bottom: 2px solid #5a6fcf; 
        }
        .table thead th:last-child {
            border-right: none;
        }
        
        .table thead tr:nth-child(2) th {
            background: #ffffff;
            color: #333;
            font-weight: bold;
            font-size: 13px;
            padding: 10px 8px;
            border-top: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }
        
        .table thead tr.column-numbers th {
            background: #f1f3f5;
            color: #555;
            font-weight: bold;
            font-size: 12px;
            padding: 8px;
            border-top: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }
        
        .table thead th:last-child,
        .table thead tr:nth-child(2) th:last-child,
        .table thead tr.column-numbers th:last-child {
             border-right: none;
        }
        
        .table tbody td {
            padding: 12px 8px;
            border: 1px solid #e9ecef;
            text-align: center;
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
            border-radius: 5px;
        }
        .no-data {
            text-align: center;
            padding: 60px;
            color: #6c757d;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
        }
        .role-info {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .date-info {
            background: linear-gradient(135deg, #17a2b8, #6f42c1);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .form-control, .btn {
            border-radius: 8px;
        }
        .btn-primary {
            background: #667eea; 
            border: none;
        }
        .btn-primary:hover {
            background: #5a6fcf; 
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border: none;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #545b62);
            border: none;
        }
        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            border: none;
            color: white !important;
        }

        .btn-back-dashboard {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
            padding: 8px 15px;
            background-color: #f8f9fa; 
            color: #333; 
            border: 1px solid #ddd;
        }
        .btn-back-dashboard:hover {
            background-color: #e9ecef;
            color: #000;
        }
        
        @media (max-width: 576px) {
            .btn-back-dashboard {
                position: relative;
                top: auto;
                left: auto;
                display: block;
                width: 100%;
                margin-bottom: 15px;
                text-align: center;
            }
            .report-header h2 {
                font-size: 20px; 
            }
        }
        
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 14px;
            padding-top: 0.5em;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px !important;
            padding: 8px 12px; 
            font-size: 14px; 
            height: auto;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            padding: 0.5em 1em; 
            margin: 0 2px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #667eea; 
            color: white !important;
            border: 1px solid #667eea;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: #f8f9fa;
            color: #6c757d;
            border: 1px solid #ddd;
        }
        
        #print-header { display: none; }

        @media print {
            body {
                background: #fff;
                font-family: Arial, sans-serif;
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }
            .main-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                border-radius: 0;
            }
            #print-header {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }
            #print-header h1 {
                font-size: 16pt;
                font-weight: bold;
                margin: 0;
                color: #667eea;
            }
            #print-header p {
                font-size: 12pt;
                margin: 0;
            }
            .report-header,
            .filter-section,
            .summary-card,
            .role-info,
            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate,
            .btn-back-dashboard,
            .dt-buttons,
            .btn-info, 
            .btn-success {
                display: none;
            }
            .table-responsive {
                border: none;
                overflow: visible;
            }
            .table {
                width: 100%;
                font-size: 9pt;
            }
            .table thead th {
                background: #667eea !important; 
                color: #ffffff !important; 
                border: 1px solid #5a6fcf !important;
            }
            .table tbody td, .table thead th {
                border: 1px solid #999;
                padding: 4px;
            }
            .table thead tr:nth-child(2) th,
            .table thead tr.column-numbers th {
                background: #f1f3f5 !important;
                color: #000 !important;
            }
            @page {
                size: A4 landscape;
                margin: 0.25in;
            }
        }
    </style>
</head>
<body>

    <div id="print-header">
        <h1>Principle Secretary Daily Report</h1>
        <p>Date: <?php echo date('d.m.Y'); ?></p>
    </div>

    <div class="container-fluid">
        <div class="main-container">
            <div class="report-header">
                
                <a href="dashboard.php" class="btn btn-light btn-back-dashboard">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                
                <h2><i class="fas fa-chart-line"></i> प्रमुख सचिव महोदय - प्रतिदिन समीक्षा रिपोर्ट</h2>
                <p class="mb-0">सहकारिता क्षेत्र के उर्वरक बिक्री केन्द्रों की दैनिक रिपोर्ट</p>
            </div>
            
            <div class="text-center mb-3">
                <span class="role-info">
                    <i class="fas fa-user"></i> भूमिका: <?php echo strtoupper($user_role); ?>
                </span>
            </div>
            
            <div class="filter-section">
                <h5><i class="fas fa-filter"></i> फिल्टर विकल्प</h5>
                <form method="GET" action="ps_review_report_hindi.php">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="date"><strong>दिनांक:</strong></label>
                            <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
                        </div>
                        <?php if (in_array($user_role, ['sadmin', 'admin', 'report', 'dr'])): ?>
                        <div class="col-md-4">
                            <label for="district"><strong>जनपद:</strong></label>
                            <select id="district" name="district" class="form-control">
                                <option value="">सभी जनपद</option>
                                <?php foreach ($districts as $district): ?>
                                    <option value="<?php echo $district['district_id']; ?>" <?php echo ($filter_district == $district['district_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($district['district_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <div class="col-md-4">
                            <label><strong>जनपद:</strong></label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_district); ?>" readonly>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="ps_review_report_hindi.php" class="btn btn-secondary">
                                <i class="fas fa-refresh"></i> Clear Filter
                            </a>
                        </div>
                    </div>
                </form>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                         <button type="button" onclick="window.print()" class="btn btn-info">
                            <i class="fas fa-print"></i> Print Report
                        </button>
                        <a href="ps_excel_export.php?date=<?php echo htmlspecialchars($filter_date); ?>&district=<?php echo htmlspecialchars($filter_district); ?>" 
                           class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>
                
            </div>
            
            <?php if (!empty($report_data)): ?>
            <div class="summary-card">
                <h5><i class="fas fa-info-circle"></i> सारांश (Summary)</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="summary-item">
                            <span>कुल प्रविष्टियां (Entries):</span>
                            <span><?php echo count($report_data); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-item">
                            <span>कुल UREA बिक्री (MT):</span>
                            <span><?php echo number_format(array_sum(array_column($report_data, 'urea_sale_quantity_mt')), 2); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-item">
                            <span>कुल DAP बिक्री (MT):</span>
                            <span><?php echo number_format(array_sum(array_column($report_data, 'dap_sale_quantity_mt')), 2); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="summary-item">
                            <span>कुल NPK बिक्री (MT):</span>
                            <span><?php echo number_format(array_sum(array_column($report_data, 'npk_sale_quantity_mt')), 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <?php if (!empty($report_data)): ?>
                    <table id="reportTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th rowspan="2">S.No.</th>
                                <th rowspan="2">दिनांक</th>
                                <th rowspan="2">जनपद</th>
                                <th rowspan="2">कुल केन्द्र</th>
                                <th rowspan="2">ट्रक आवश्यक</th>
                                <th colspan="2">UREA</th>
                                <th colspan="2">DAP</th>
                                <th colspan="2">NPK</th>
                                <th colspan="2">कुल बिक्री</th>
                                <th rowspan="2">बैंक जमा (लाख)</th>
                                <th rowspan="2">RTGS कुल (लाख)</th>
                                <th rowspan="2">DM आवंटित केन्द्र</th>
                                <th colspan="2">PCF</th>
                                <th rowspan="2">निर्मित (Created)</th>
                                <th rowspan="2">अपडेट (Updated)</th>
                            </tr>
                            <tr>
                                <th>बिक्री (MT)</th>
                                <th>राशि (लाख)</th>
                                <th>बिक्री (MT)</th>
                                <th>राशि (लाख)</th>
                                <th>बिक्री (MT)</th>
                                <th>राशि (लाख)</th>
                                <th>बिक्री (MT)</th>
                                <th>राशि (लाख)</th>
                                <th>प्रेषण (MT)</th>
                                <th>लंबित (MT)</th>
                            </tr>
                            <tr class="column-numbers">
                                <th>(1)</th>
                                <th>(2)</th>
                                <th>(3)</th>
                                <th>(4)</th>
                                <th>(5)</th>
                                <th>(6)</th>
                                <th>(7)</th>
                                <th>(8)</th>
                                <th>(9)</th>
                                <th>(10)</th>
                                <th>(11)</th>
                                <th>(12)</th>
                                <th>(13)</th>
                                <th>(14)</th>
                                <th>(15)</th>
                                <th>(16)</th>
                                <th>(17)</th>
                                <th>(18)</th>
                                <th>(19)</th>
                                <th>(20)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $i => $row): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['formatted_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                                <td><?php echo number_format($row['total_coop_centers_district']); ?></td>
                                <td><?php echo number_format($row['trucks_required_dispatch']); ?></td>
                                <td><?php echo number_format($row['urea_sale_quantity_mt'], 2); ?></td>
                                <td><?php echo number_format($row['urea_sale_amount_lakh_rs'], 2); ?></td>
                                <td><?php echo number_format($row['dap_sale_quantity_mt'], 2); ?></td>
                                <td><?php echo number_format($row['dap_sale_amount_lakh_rs'], 2); ?></td>
                                <td><?php echo number_format($row['npk_sale_quantity_mt'], 2); ?></td>
                                <td><?php echo number_format($row['npk_sale_amount_lakh_rs'], 2); ?></td>
                                <td><?php echo number_format($row['total_sale_quantity_mt'], 2); ?></td>
                                <td><?php echo number_format($row['total_sale_amount_lakh_rs'], 2); ?></td>
                                <td><?php echo number_format($row['bank_deposit_amount_lakh_rs'], 2); ?></td>
                                <td><?php echo number_format($row['rtgs_total_amount_lakh_rs'], 2); ?></td>
                                <td><?php echo number_format($row['dm_allocated_phosphatic_centers']); ?></td>
                                <td><?php echo number_format($row['pcf_dispatch_rtgs_quantity_mt'], 2); ?></td>
                                <td><?php echo number_format($row['pcf_pending_quantity_mt'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at_formatted']); ?></td>
                                <td><?php echo htmlspecialchars($row['updated_at_formatted']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <h5>कोई डेटा नहीं मिला</h5>
                        <p>चयनित मानदंड के अनुसार कोई प्रविष्टि नहीं मिली।</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#reportTable').DataTable({
                "scrollX": true,
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries found",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "order": [[ 1, "desc" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]
            });
        });
    </script>
</body>
</html>