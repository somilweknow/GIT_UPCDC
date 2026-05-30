<?php
// session_start();
include("../scripts/settings.php");
echo '<base href="../">';
page_header_start();
page_header_end();
page_sidebar();

$authority_id = isset($_GET['authority_id']) ? intval($_GET['authority_id']) : 0;
$user_type = $_SESSION['user_type'] ?? '';
$totalAll = 0;

if ($user_type === 'ncd_maker') {

    $district_id = (int)($_SESSION['district_id'] ?? 0);

    $query = "
    SELECT 
        c.cooperative_society_type_id,
        COALESCE(mst.name, 'Other') AS society_type_name,
        COUNT(*) as total
    FROM ncd_cooperative_registrations c
    LEFT JOIN ncd_cooperative_society_types mst 
        ON c.cooperative_society_type_id = mst.id
    WHERE c.district_code = $district_id
    " . ($authority_id > 0 ? " AND c.registration_authoritie_id = $authority_id " : "") . "
    GROUP BY c.cooperative_society_type_id, mst.name
    ORDER BY total DESC
    ";

    $res = execute_query($query);

    $totalRes = execute_query("
        SELECT COUNT(*) as total 
        FROM ncd_cooperative_registrations
        WHERE district_code = $district_id
        " . ($authority_id > 0 ? " AND registration_authoritie_id = $authority_id " : "") . "
    ");
    $totalAll = mysqli_fetch_assoc($totalRes)['total'];
}

// ✅ CHECKER → multiple districts
elseif ($user_type === 'ncd_checker') {

    $division_id = $_SESSION['division_id'] ?? '';

    $districts = [];

    if (!empty($division_id)) {

        $sql = "SELECT nd.district_code
                FROM master_district md
                LEFT JOIN ncd_districts nd 
                ON LOWER(md.district_name) = LOWER(nd.district_name)
                WHERE md.division_id = '$division_id'";

        $resDist = execute_query($sql);

        while ($row = mysqli_fetch_assoc($resDist)) {
            if (!empty($row['district_code'])) {
                $districts[] = (int)$row['district_code'];
            }
        }
    }

    $district_list = !empty($districts) ? implode(",", $districts) : '0';

    $query = "
    SELECT 
        c.cooperative_society_type_id,
        COALESCE(mst.name, 'Other') AS society_type_name,
        COUNT(*) as total
    FROM ncd_cooperative_registrations c
    LEFT JOIN ncd_cooperative_society_types mst 
        ON c.cooperative_society_type_id = mst.id
    WHERE c.district_code IN ($district_list)
    " . ($authority_id > 0 ? " AND c.registration_authoritie_id = $authority_id " : "") . "
    GROUP BY c.cooperative_society_type_id, mst.name
    ORDER BY total DESC
    ";

    $res = execute_query($query);

    $totalRes = execute_query("
        SELECT COUNT(*) as total 
        FROM ncd_cooperative_registrations
        WHERE district_code IN ($district_list)
        " . ($authority_id > 0 ? " AND registration_authoritie_id = $authority_id " : "") . "
    ");
    $totalAll = mysqli_fetch_assoc($totalRes)['total'];
}

// ✅ ADMIN / OTHERS → no district filter
else {

    $query = "
    SELECT 
        c.cooperative_society_type_id,
        COALESCE(mst.name, 'Other') AS society_type_name,
        COUNT(*) as total
    FROM ncd_cooperative_registrations c
    LEFT JOIN ncd_cooperative_society_types mst 
        ON c.cooperative_society_type_id = mst.id
    WHERE 1=1
    " . ($authority_id > 0 ? " AND c.registration_authoritie_id = $authority_id " : "") . "
    GROUP BY c.cooperative_society_type_id, mst.name
    ORDER BY total DESC
    ";

    $res = execute_query($query);

    $totalRes = execute_query("
        SELECT COUNT(*) as total 
        FROM ncd_cooperative_registrations
        WHERE 1=1
        " . ($authority_id > 0 ? " AND registration_authoritie_id = $authority_id " : "") . "
    ");
    $totalAll = mysqli_fetch_assoc($totalRes)['total'];
}

function getIconAndColor($name, $index) {
    $name = strtolower($name ?? '');

    $icons = [
        'credit' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1e8449" stroke-width="1.6"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>',
            'bg' => '#d1fae5', 'badge' => '#1e8449'
        ],
        'housing' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="1.6"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>',
            'bg' => '#e5e7eb', 'badge' => '#6e2f0a'
        ],
        'agriculture' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.6"><path d="M12 2v7m0 0l3 3m-3-3l-3 3"/><path d="M5 12l7-7 7 7"/><path d="M12 22v-7"/></svg>',
            'bg' => '#dcfce7', 'badge' => '#52b545'
        ],
        'milk' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1a5276" stroke-width="1.6"><path d="M8 2v4m8-4v4M2 12h20M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/></svg>',
            'bg' => '#dbeafe', 'badge' => '#1a5276'
        ],
        'fisheries' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="1.6"><path d="M16 8s2-2 2-4-2-4-2-4-2 2-2 4 2 4 2 4zM8 16s-2 2-2 4 2 4 2 4 2-2 2-4-2-4-2-4z"/><path d="M16 8l-4 4-4-4m0 8l4-4 4 4"/></svg>',
            'bg' => '#ccfbf1', 'badge' => '#148f77'
        ],
        'handloom' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#92680a" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 3v18M3 15h18"/></svg>',
            'bg' => '#fef9c3', 'badge' => '#9a7d0a'
        ],
        'consumer' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e67e22" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/><path d="M12 18a4 4 0 0 0 4-4"/></svg>',
            'bg' => '#fff3e0', 'badge' => '#e67e22'
        ],
        'marketing' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="1.6"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M8 12l3 3 5-5"/></svg>',
            'bg' => '#d1fae5', 'badge' => '#27ae60'
        ],
    ];

    foreach ($icons as $keyword => $data) {
        if (strpos($name, $keyword) !== false) {
            return $data;
        }
    }

    // Default fallback colors cycling
    $defaults = [
        ['icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>', 'bg' => '#ede9fe', 'badge' => '#7c3aed'],
        ['icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0369a1" stroke-width="1.6"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>', 'bg' => '#e0f2fe', 'badge' => '#0369a1'],
        ['icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b45309" stroke-width="1.6"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>', 'bg' => '#fef3c7', 'badge' => '#b45309'],
        ['icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="1.6"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', 'bg' => '#ccfbf1', 'badge' => '#0f766e'],
        ['icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>', 'bg' => '#fee2e2', 'badge' => '#b91c1c'],
    ];

    return $defaults[$index % count($defaults)];
}

?>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #eaf0f6;
        }

        .top-bar {
            background: linear-gradient(90deg, #e05a00, #f47b20);
            color: white;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-bar {
            background: #ffffff;
            border-bottom: 2px solid #1a5276;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .brand-logos {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .logo-circle {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: 2px solid #1a5276;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 500;
            color: #1a5276;
            text-align: center;
            line-height: 1.3;
        }

        .brand-title {
            flex: 1;
            text-align: center;
        }

        .brand-title .hindi {
            font-size: 20px;
            font-weight: bold;
            color: #c0392b;
        }

        .brand-title .english {
            font-size: 17px;
            font-weight: bold;
            color: #1a5276;
        }

        .dashboard {
            padding: 24px 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(175px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 18px 14px 14px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 8px;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 6px 20px rgba(0,0,0,0.10);
        }

        .icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2px;
        }

        .card-label {
            font-size: 13px;
            font-weight: 500;
            color: #555;
            line-height: 1.4;
            min-height: 2.8em;
        }

        .badge {
            padding: 5px 16px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            min-width: 72px;
            display: inline-block;
            text-align: center;
        }

        .back-btn {
            margin-bottom: 15px;
            display: inline-block;
            padding: 6px 12px;
            background: #6c757d;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }

        .back-btn:hover {
            background: #545b62;
        }
    </style>
</head>

<body>

<!-- Dashboard -->
<div class="dashboard">

    <!-- Back to main dashboard -->
    <a href="Ncd_Reports/dashboard_cooperatives.php" class="back-btn">&#9668; Back to Dashboard</a>

    <div class="section-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/>
            <rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Cooperative Societies Types
    </div>

    <div class="grid">

        <!-- ALL TYPES -->
        <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'sadmin'): ?>
        <div class="card" onclick="goToData('all')">
            <div class="icon-wrap" style="background:#fee2e2;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="1.6">
                    <circle cx="9" cy="8" r="2.5"/>
                    <circle cx="15" cy="8" r="2.5"/>
                    <path d="M3 20c0-3.31 2.69-6 6-6h6c3.31 0 6 2.69 6 6"/>
                </svg>
            </div>
            <div class="card-label">All  Of Societies</div>
            <div class="badge" style="background:#e74c3c;"><?= $totalAll ?></div>
        </div>
        <?php endif; ?>

        <!-- Dynamic Types -->
        <?php
        $index = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $typeName = $row['society_type_name'] ?? 'Other';
            $card = getIconAndColor($typeName, $index);
        ?>
        <div class="card" onclick="goToData(<?= (int)$row['cooperative_society_type_id'] ?>)">
            <div class="icon-wrap" style="background:<?= $card['bg'] ?>;">
                <?= $card['icon'] ?>
            </div>
            <div class="card-label"><?= htmlspecialchars($typeName) ?></div>
            <div class="badge" style="background:<?= $card['badge'] ?>;"><?= $row['total'] ?></div>
        </div>
        <?php
        $index++;
        } ?>

    </div>
</div>

<script>
    function goToData(typeId) {
        let url = "Ncd_Reports/ncd_cooperatives_info.php";
        let params = new URLSearchParams();

        // always pass authority_id
        params.append('authority_id', "<?= $authority_id ?>");

        // pass type_id if not ALL
        if (typeId !== 'all') {
            params.append('type_id', typeId);
        }

        window.open(url + '?' + params.toString(), '_blank');
    }
</script>
<?php
page_footer_start();
page_footer_end();
?>