<?php
include("../scripts/settings.php");
echo '<base href="../">';
page_header_start();
page_header_end();
page_sidebar();

$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

$userSno = isset($_SESSION['usersno']) ? $_SESSION['usersno'] : '';
$assignedDeptId = null;
$res = execute_query("
    SELECT department_authority_id 
    FROM ncd_users 
    WHERE id = '$userSno'
    LIMIT 1
");
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $assignedDeptId = $row['department_authority_id'];
}

$district_id = '';
$totalAll = 0;

// MAKER single district
if ($user_type === 'ncd_maker') {

    $district_id = (int)($_SESSION['district_id'] ?? 0);

    $query = "
    SELECT 
        c.registration_authoritie_id,
        COALESCE(MAX(ra.authority_name), 'Other Authorities') AS authority_name,
        COUNT(*) AS total
    FROM ncd_cooperative_registrations c
    LEFT JOIN ncd_registration_authorities ra 
        ON c.registration_authoritie_id = ra.id
    WHERE c.district_code = $district_id
    GROUP BY c.registration_authoritie_id
    ORDER BY total DESC
    ";

    $res = execute_query($query);

    $totalRes = execute_query("
        SELECT COUNT(*) as total 
        FROM ncd_cooperative_registrations 
        WHERE district_code = $district_id
    ");
    $totalAll = mysqli_fetch_assoc($totalRes)['total'];
}


//CHECKER multiple districts (division based)
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
        c.registration_authoritie_id,
        COALESCE(MAX(ra.authority_name), 'Other Authorities') AS authority_name,
        COUNT(*) AS total
    FROM ncd_cooperative_registrations c
    LEFT JOIN ncd_registration_authorities ra 
        ON c.registration_authoritie_id = ra.id
    WHERE c.district_code IN ($district_list)
    GROUP BY c.registration_authoritie_id
    ORDER BY total DESC
    ";

    $res = execute_query($query);

    $totalRes = execute_query("
        SELECT COUNT(*) as total 
        FROM ncd_cooperative_registrations 
        WHERE district_code IN ($district_list)
    ");
    $totalAll = mysqli_fetch_assoc($totalRes)['total'];
}
//ADMIN / OTHERS → no district filter
else {

    $query = "
    SELECT 
        c.registration_authoritie_id,
        COALESCE(MAX(ra.authority_name), 'Other Authorities') AS authority_name,
        COUNT(*) AS total
    FROM ncd_cooperative_registrations c
    LEFT JOIN ncd_registration_authorities ra 
        ON c.registration_authoritie_id = ra.id
    GROUP BY c.registration_authoritie_id
    ORDER BY total DESC
    ";

    $res = execute_query($query);

    $totalRes = execute_query("
        SELECT COUNT(*) as total 
        FROM ncd_cooperative_registrations
    ");
    $totalAll = mysqli_fetch_assoc($totalRes)['total'];
}



// Icon map by authority_name keywords (customize as needed)
function getIconAndColor($name, $index) {
    $name = strtolower($name ?? '');

    $icons = [
        'state federation' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1e8449" stroke-width="1.6"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/><path d="M12 6l2 4h4l-3 3 1 4-4-2-4 2 1-4-3-3h4z"/></svg>',
            'bg' => '#d1fae5', 'badge' => '#1e8449'
        ],
        'district federation' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="1.6"><circle cx="12" cy="12" r="3"/><path d="M12 1v6m0 6v6m4.22-13.22l4.24 4.24M18.46 18.46l4.24 4.24M1.54 1.54l4.24 4.24M18.46 18.46l4.24 4.24"/></svg>',
            'bg' => '#e5e7eb', 'badge' => '#6e2f0a'
        ],
        'block' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="2"/><rect x="7" y="7" width="10" height="10" rx="1"/><path d="M3 9h18M9 3v18"/></svg>',
            'bg' => '#dcfce7', 'badge' => '#52b545'
        ],
        'regional' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#1a5276" stroke-width="1.6"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
            'bg' => '#dbeafe', 'badge' => '#1a5276'
        ],
        'state bank' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="1.6"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8M8 10h8M8 14h5"/><circle cx="18" cy="6" r="3"/></svg>',
            'bg' => '#ccfbf1', 'badge' => '#148f77'
        ],
        'district bank' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#92680a" stroke-width="1.6"><path d="M3 6h18M7 12h10M11 16h2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
            'bg' => '#fef9c3', 'badge' => '#9a7d0a'
        ],
        'primary' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e67e22" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="M16 14a4 4 0 0 1-8 0"/><path d="M8 10h.01M12 10h.01M16 10h.01"/></svg>',
            'bg' => '#fff3e0', 'badge' => '#e67e22'
        ],
        'functional' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="1.6"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
            'bg' => '#d1fae5', 'badge' => '#27ae60'
        ],
        'credit' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.6"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/><path d="M7 15h.01M11 15h2"/></svg>',
            'bg' => '#d1fae5', 'badge' => '#059669'
        ],
        'housing' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.6"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
            'bg' => '#ede9fe', 'badge' => '#7c3aed'
        ],
        'agriculture' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.6"><path d="M12 2v7m0 0l3 3m-3-3l-3 3"/><path d="M5 12l7-7 7 7M12 22v-7"/><path d="M8 6h.01M16 6h.01"/></svg>',
            'bg' => '#dcfce7', 'badge' => '#16a34a'
        ],
        'milk' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="1.6"><path d="M8 2v4m8-4v4M2 12h20M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/><path d="M8 12h8"/></svg>',
            'bg' => '#e0f2fe', 'badge' => '#0891b2'
        ],
        'fisheries' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0d9488" stroke-width="1.6"><path d="M16 8s2-2 2-4-2-4-2-4-2 2-2 4 2 4 2 4zM8 16s-2 2-2 4 2 4 2 4 2-2 2-4-2-4-2-4z"/><path d="M16 8l-4 4-4-4m0 8l4-4 4 4"/></svg>',
            'bg' => '#ccfbf1', 'badge' => '#0d9488'
        ],
        'handloom' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#92680a" stroke-width="1.6"><path d="M4 4h16v16H4z"/><path d="M4 8h16M8 4v16M12 4v16M16 4v16"/><path d="M8 12h8"/></svg>',
            'bg' => '#fef9c3', 'badge' => '#92680a'
        ],
        'consumer' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.6"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
            'bg' => '#fee2e2', 'badge' => '#dc2626'
        ],
        'marketing' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.6"><path d="M3 3v18h18"/><path d="M18 17V9M13 17V5M8 17v-3"/></svg>',
            'bg' => '#ede9fe', 'badge' => '#7c3aed'
        ],
        'weavers' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#92680a" stroke-width="1.6"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><path d="M12 11V4M8 11h8l-4-8z"/><path d="M6 11h12l-2 7h-8z"/></svg>',
            'bg' => '#fef9c3', 'badge' => '#92680a'
        ],
        'industrial' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6"><path d="M2 20h20M5 20V8l3-3 3 3v12M10 20V8l3-3 3 3v12"/><rect x="3" y="13" width="3" height="7"/><rect x="8" y="13" width="3" height="7"/><rect x="13" y="13" width="3" height="7"/><rect x="18" y="13" width="3" height="7"/></svg>',
            'bg' => '#f1f5f9', 'badge' => '#475569'
        ],
        'urban' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.6"><path d="M3 21h18M5 21V7l8-4v18M13 21v-9l7-4v13"/><rect x="7" y="10" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>',
            'bg' => '#f9fafb', 'badge' => '#6b7280'
        ],
        'rural' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.6"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/><path d="M8 6h.01M16 6h.01"/></svg>',
            'bg' => '#dcfce7', 'badge' => '#16a34a'
        ],
        'labor' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="1.6"><circle cx="12" cy="8" r="3"/><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
            'bg' => '#fff3e0', 'badge' => '#ea580c'
        ],
        'transport' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="1.6"><path d="M5 17H3v-4h2m14 4h2v-4h-2M6 8h12l2 5v4H4v-4l2-5z"/><circle cx="7.5" cy="17.5" r="2.5"/><circle cx="16.5" cy="17.5" r="2.5"/></svg>',
            'bg' => '#e0f2fe', 'badge' => '#0891b2'
        ],
        'women' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="1.6"><circle cx="12" cy="8" r="3"/><path d="M12 11v6M9 14h6"/><path d="M16 21v-2a4 4 0 0 0-4-4h-4a4 4 0 0 0-4 4v2"/></svg>',
            'bg' => '#fce7f3', 'badge' => '#ec4899'
        ],
        'youth' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="1.6"><circle cx="12" cy="8" r="3"/><path d="M12 11v6"/><path d="M8 15h8M12 3v2"/></svg>',
            'bg' => '#dbeafe', 'badge' => '#3b82f6'
        ],
        'ex-servicemen' => [
            'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.6"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/><path d="M12 8v4l3 3"/></svg>',
            'bg' => '#fee2e2', 'badge' => '#dc2626'
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
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between; /* KEY */
            align-items: center;
        }

        .title-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn {
            background: #1a5276;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: #154360;
        }
    </style>
</head>
<body>

<!-- Dashboard -->
<div class="dashboard">

    <div class="section-title">
        <div class="title-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Cooperative Societies
        </div>

        <a href="index_1.php" class="back-btn">
            ← Back to Main Dashboard
        </a>
    </div>

    <div class="grid">

        <!--All Types Of Societies-->
        <?php if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'sadmin'): ?>
            <div class="card" onclick="goToData('all')">
                <div class="icon-wrap" style="background:#fee2e2;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="1.6">
                        <circle cx="9" cy="8" r="2.5"/>
                        <circle cx="15" cy="8" r="2.5"/>
                        <path d="M3 20c0-3.31 2.69-6 6-6h6c3.31 0 6 2.69 6 6"/>
                    </svg>
                </div>
                <div class="card-label">All Cooperatives Societies</div>
                <div class="badge" style="background:#e74c3c;"><?= $totalAll ?></div>
            </div>
        <?php endif; ?>


        <!-- Dynamic Cards from DB -->
        <?php
        $index = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $authorityName = $row['authority_name'] ?? 'Other Authorities';
            $authorityId = $row['registration_authoritie_id'] ?? null;
            $card = getIconAndColor($authorityName, $index);

            //  Only assigned departments can be seen
            if ($_SESSION['usertype'] != 'sadmin' && $authorityId != $assignedDeptId) {continue;}

            ?>
        <div class="card" onclick="goToData(<?= (int)$row['registration_authoritie_id'] ?>)">
            <div class="icon-wrap" style="background:<?= $card['bg'] ?>;">
                <?= $card['icon'] ?>
            </div>
            <div class="card-label"><?= htmlspecialchars($authorityName) ?></div>
            <div class="badge" style="background:<?= $card['badge'] ?>;"><?= $row['total'] ?></div>
        </div>
        <?php 
        $index++;
        } ?>

    </div>
</div>

<script>
    function goToData(id) {
    let url = "Ncd_Reports/dashboard_cooperative_types.php";
        if (id !== 'all') {
            url += "?authority_id=" + id;
        }
        window.open(url, '_blank');
    }
</script>
<?php
page_footer_start();
page_footer_end();
?>