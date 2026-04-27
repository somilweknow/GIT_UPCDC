<?php
include("../scripts/settings.php");
echo '<base href="../">';

page_header_start();
page_header_end();
page_sidebar();

// ✅ GET authority_id from previous dashboard
$authority_id = isset($_GET['authority_id']) ? intval($_GET['authority_id']) : 0;

// 🔥 Fetch cooperative types count (WITH authority filter)
$query = "
SELECT 
    c.cooperative_society_type_id,
    mst.cooperative_society_types AS society_type_name,
    COUNT(*) as total

FROM cooperatives c

LEFT JOIN ncd_cooperative_society_type mst 
    ON c.cooperative_society_type_id = mst.sno
";

// ✅ Apply authority filter
if ($authority_id > 0) {
    $query .= " WHERE c.registration_authoritie_id = $authority_id ";
}

$query .= "
GROUP BY c.cooperative_society_type_id
ORDER BY total DESC
";


$res = execute_query($query);

// 🔥 Total count (respect authority filter)
if ($authority_id > 0) {
    $totalRes = execute_query("
        SELECT COUNT(*) as total 
        FROM cooperatives 
        WHERE registration_authoritie_id = $authority_id
    ");
} else {
    $totalRes = execute_query("SELECT COUNT(*) as total FROM cooperatives");
}

$totalAll = mysqli_fetch_assoc($totalRes)['total'];

// Icon map by authority_name keywords (customize as needed)
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

<!DOCTYPE html>
<html>
<head>
    <title>Cooperative Types Dashboard</title>
    <meta charset="UTF-8">
    <style>
       

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
        Cooperative Types Dashboard
    </div>

    <div class="grid">

        <!-- ALL TYPES -->
        <div class="card" onclick="goToData('all')">
            <div class="icon-wrap" style="background:#fee2e2;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="1.6">
                    <circle cx="9" cy="8" r="2.5"/>
                    <circle cx="15" cy="8" r="2.5"/>
                    <path d="M3 20c0-3.31 2.69-6 6-6h6c3.31 0 6 2.69 6 6"/>
                </svg>
            </div>
            <div class="card-label">All Types Of Societies</div>
            <div class="badge" style="background:#e74c3c;"><?= $totalAll ?></div>
        </div>

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

</body>
</html>