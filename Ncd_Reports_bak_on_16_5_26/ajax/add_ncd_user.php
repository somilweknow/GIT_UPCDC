<?php
include("../scripts/settings.php");
echo '<base href="../">';
page_header_start();
page_header_end();
page_sidebar();

//echo '<pre>';
//print_r($_GET); exit;

/* =========================================================
   SAFE GET PARAMS
========================================================= */

function getParam($key, $default = '')
{
    return isset($_GET[$key]) && $_GET[$key] !== ''
        ? trim($_GET[$key])
        : $default;
}

$user_type     = getParam('usertype');
$user_name     = getParam('username');
$division_id   = getParam('division_id');
$division_name = getParam('division_name');
$user_no       = getParam('usersno');
$ncd_user      = getParam('ncd_user');

/* =========================================================
   FLASH MESSAGE
========================================================= */

$message     = getParam('msg');
$messageType = getParam('type');

/* =========================================================
   PRESERVE EXISTING URL PARAMS
========================================================= */

$preservedParams = $_GET;

unset($preservedParams['msg']);
unset($preservedParams['type']);

$preservedQuery = http_build_query($preservedParams);

if (!$user_type) {
    die("Unauthorized Access");
}

$userName = $user_name ? $user_name : 'Not Logged In';

$currentUserType = trim($user_type ?? '');

$createTypeName = '';
$createTypeId   = '';

//echo $currentUserType; exit;

if ($currentUserType === 'ncd_admin') {
    $createTypeName = 'Checker';
    $createTypeId   = 2;
} elseif ($currentUserType === 'ncd_checker') {
    $createTypeName = 'Maker';
    $createTypeId   = 3;
} else {
    die("Invalid Access");
}

$divisions = [];
$res = execute_query("SELECT sno, division_name FROM master_division ORDER BY division_name ASC");

while ($row = mysqli_fetch_assoc($res)) {
    $divisions[] = $row;
}

$roleFilter = "";

// ncd_checker should only see their own division users

$groupedUsers = [];
$users = [];

if ($currentUserType === 'ncd_admin') {

    // ✅ ADMIN → ONLY CHECKERS (NOT ADMIN, NOT MAKERS)
    $sql = "SELECT * FROM ncd_users 
            WHERE type_id = 2
            ORDER BY division_name, id DESC";

    $res = execute_query($sql);

    while ($row = mysqli_fetch_assoc($res)) {

        $division = $row['division_name'] ?? '-';
        $groupedUsers[$division][] = $row;
    }

} elseif ($currentUserType === 'ncd_checker') {

    // CHECKER → ONLY OWN DIVISION USERS (NO GROUP NEEDED)
    $divisionName = $division_name ?? '';

    $sql = "SELECT * FROM ncd_users 
        WHERE division_name = '$divisionName'
        AND type_id = 3
        ORDER BY id DESC";
    $res = execute_query($sql);

    while ($row = mysqli_fetch_assoc($res)) {
        $users[] = $row;
    }
}

//Department/Authrity names for drop down
$authorities = [];
$resAuth = execute_query("SELECT id, authority_name FROM ncd_registration_authorities ORDER BY authority_name ASC");

while ($row = mysqli_fetch_assoc($resAuth)) {
    $authorities[] = $row;
}

//Authrity name
$user_id = $ncd_user ? $ncd_user  : 0;

$department_name = '';
$res = execute_query("
        SELECT a.authority_name 
        FROM ncd_users u
        LEFT JOIN ncd_registration_authorities a 
        ON u.department_authority_id = a.id
        WHERE u.id = '$user_id'
        LIMIT 1
    ");

if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $department_name = $row['authority_name'] ?? '';
}

if ($user_no) {
    $user_id = $user_no ?? 0;

    $res = execute_query("
    SELECT department_authority_id 
    FROM ncd_users 
    WHERE id = '$user_id'
    LIMIT 1
");

    $dept_id = null;

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $dept_id = $row['department_authority_id'] ?? null;
    }
}
?>

    <style>
        body { font-family: Arial; background: #eaf0f6; margin: 0; }

        .top-bar {
            background: linear-gradient(90deg, #e05a00, #f47b20);
            color: #fff;
            padding: 10px 20px;
        }

        .brand-bar {
            background: #fff;
            border-bottom: 2px solid #1a5276;
            padding: 15px;
            text-align: center;
        }

        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
        }

        .container { padding: 20px; }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .form-group {
            background: #f9fafb;
            padding: 12px;
            border-radius: 8px;
        }

        label {
            font-size: 12px;
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 8px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
        }

        input[readonly] {
            background: #f3f4f6;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            background: #1a5276;
            color: #fff;
            cursor: pointer;
        }

        #msgBox {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 20px;
            border-radius: 6px;
            color: #fff;
            font-weight: bold;
            z-index: 9999;
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
        #create_title{
            font-size: 20px;
            font-weight: bold;
            color: white;
            background: #1a5276;
        }
        /************************************************
              BEAUTIFUL REPORT / LISTING CSS
      ************************************************/

        .user-listings{
            margin: 28px 20px;
        }

        /* Card */
        .user-listings .section-card{
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 28px;
            border: 1px solid #e6edf5;
            box-shadow:
                    0 10px 30px rgba(26,82,118,0.08),
                    0 2px 8px rgba(0,0,0,0.05);
            transition: 0.3s ease;
        }

        .user-listings .section-card:hover{
            transform: translateY(-3px);
            box-shadow:
                    0 16px 35px rgba(26,82,118,0.12),
                    0 4px 12px rgba(0,0,0,0.06);
        }

        /* Heading */
        .user-listings .section-title{
            background: linear-gradient(135deg,#0f4c75,#1a659e,#00a8cc);
            color: #fff;
            padding: 14px 22px;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.4px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .user-listings .section-title::before{
            content: "🏢";
            font-size: 20px;
        }

        /* Table */
        .user-listings table{
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        /* Table Header */
        .user-listings table thead tr{
            background: linear-gradient(to right,#f8fbff,#eef5fb);
        }

        .user-listings table thead th{
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 800;
            color: #0f4c75;
            border-bottom: 2px solid #dbe8f4;
            text-transform: uppercase;
            letter-spacing: 0.7px;
        }

        /* Table Body */
        .user-listings table tbody td{
            padding: 10px 14px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #edf2f7;
            transition: 0.2s ease;
        }

        .user-listings table tbody tr{
            transition: 0.25s ease;
        }

        .user-listings table tbody tr:hover{
            background: linear-gradient(to right,#f9fcff,#eef7ff);
            transform: scale(1.002);
        }

        /* Alternate Row */
        .user-listings table tbody tr:nth-child(even){
            background: #fcfdff;
        }

        /* Sno */
        .user-listings table tbody td:first-child{
            font-weight: bold;
            color: #1a5276;
        }

        /* Name */
        .user-listings table tbody td:nth-child(2){
            font-weight: 700;
            color: #0f172a;
        }

        /* Username */
        .user-listings table tbody td:nth-child(3){
            color: black;
            font-weight: 600;
        }

        /* Status */
        .user-listings .status-active{
            background: linear-gradient(135deg,#dcfce7,#bbf7d0);
            color: #15803d;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 8px rgba(34,197,94,0.15);
        }

        .user-listings .status-active::before{
            content: "🟢";
            font-size: 10px;
        }

        .user-listings .status-inactive{
            background: linear-gradient(135deg,#fee2e2,#fecaca);
            color: #dc2626;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 8px rgba(239,68,68,0.15);
        }

        .user-listings .status-inactive::before{
            content: "🔴";
            font-size: 10px;
        }

        /* Delete Button */
        .user-listings .delete-btn{
            background: linear-gradient(135deg,#ef4444,#dc2626);
            color: #fff;
            padding: 9px 14px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: 0.25s ease;
            box-shadow: 0 4px 10px rgba(239,68,68,0.25);
        }



        .user-listings .delete-btn:hover{
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 16px rgba(239,68,68,0.35);
        }

        /* Empty State */
        .user-listings .empty-report{
            background: #fff;
            padding: 30px;
            text-align: center;
            border-radius: 16px;
            color: #64748b;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Responsive */
        @media(max-width:768px){

            .user-listings{
                overflow-x: auto;
            }

            .user-listings table{
                min-width: 700px;
            }

            .user-listings .section-title{
                font-size: 15px;
                padding: 14px 16px;
            }
        }/* Division Heading - Orange Gradient */
        .user-listings .division-title{
            background: linear-gradient(135deg,#e65c00,#f47b20,#ffb347);
            color: #fff;
            padding: 14px 22px;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .user-listings .division-title::before{
            content: "🏛️";
            font-size: 20px;
        }
        .user-profile-badge{
            display: none;
        }
    </style>
    </head>

    <body>

<?php if (!empty($message)): ?>
    <div id="msgBox" style="background: <?= ($messageType == 'success') ? '#28a745' : '#dc3545' ?>;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

    <div class="brand-bar" id="create_title">
        <div class="brand-title" id="create_title">
            Create <?= $createTypeName ?>
        </div>
    </div>

    <div style="
    padding:10px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
">
        <button onclick="history.back()" style="
        background:#1a5276;
        color:#fff;
        border:none;
        padding:8px 14px;
        border-radius:6px;
        cursor:pointer;
        font-size:13px;
        box-shadow:0 2px 6px rgba(0,0,0,0.2);
        display:flex;
        align-items:center;
        gap:6px;
    ">
            ⬅ Back
        </button>

        <div class="user-profile-badge">
            <span class="user-icon">👨‍💼</span>
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($userName) ?></span>
            </div>
        </div>
    </div>

    <div style="
    margin:10px 20px;
    font-size:20px;
    font-weight:700;
    color:#1a5276;
    display:inline-block;
    border-bottom:3px solid;
    border-image: linear-gradient(to right, #e05a00, #27ae60) 1;
    padding-bottom:4px;
">
        <?= htmlspecialchars($department_name ?: 'No Department') ?>
    </div>

    <div class="card">

        <form method="POST" action="Ncd_Reports/ajax/save_ncd_user.php?<?= $preservedQuery ?>">

            <input type="hidden" name="type_id" value="<?= $createTypeId ?>">
            <input type="hidden" name="usertype" value="<?= $currentUserType ?>">
            <input type="hidden" name="division" id="checker_division_id" value="<?= $division_id ?? '' ?>">
            <input type="hidden" name="division_name" id="checker_division_name" value="<?= $division_name ?? '' ?>">
            <input type="hidden" name="authority_id_hidden" id="authority_hidden" value="<?= $dept_id ? $dept_id : '' ?>">

            <div class="grid">

                <div class="form-group">
                    <label>State</label>
                    <input type="text" value="Uttar Pradesh" readonly>
                </div>

                <div class="form-group">
                    <label>Division</label>
                    <select name="division" id="division" required>
                        <option value="">-- Select Division --</option>

                        <?php foreach($divisions as $d): ?>
                            <option value="<?= $d['sno'] ?>">
                                <?= htmlspecialchars($d['division_name']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <?php if ($currentUserType === 'ncd_checker'): ?>

                    <div class="form-group">
                        <label>District</label>

                        <select name="district_id" id="district_id" required>
                            <option value="">Loading districts...</option>
                        </select>

                        <input type="hidden" name="district_name" id="district_name">
                    </div>

                <?php endif; ?>

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" required>
                </div>

                <div class="form-group">
                    <label>Department</label>

                    <select name="authority_id" id="authority_id" required>

                        <option value="">-- Select Department --</option>

                        <?php foreach($authorities as $a): ?>
                            <option value="<?= $a['id'] ?>">
                                <?= htmlspecialchars($a['authority_name']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label>User Type</label>
                    <input type="text" value="<?= $createTypeName ?>" readonly>
                </div>

            </div>

            <button class="btn">Create User</button>

        </form>

    </div>

    <div class="user-listings">

        <?php if ($currentUserType === 'ncd_admin'): ?>

            <?php
            $adminId = intval($user_no ?? 0);

            $groupedCheckers = [];
            $checkerIds = [];

            $sql = "
            SELECT *
            FROM ncd_users 
            WHERE type_id = 2 
            AND creator_admin_id = '$adminId'
            ORDER BY division_name ASC, id DESC
        ";

            $res = execute_query($sql);

            while ($row = mysqli_fetch_assoc($res)) {
                $division = $row['division_name'] ?: 'Unknown Division';
                $groupedCheckers[$division][] = $row;
                $checkerIds[] = intval($row['id']);
            }
            ?>

            <?php if (!empty($groupedCheckers)): ?>

                <?php foreach ($groupedCheckers as $divisionName => $users): ?>

                    <div class="section-card">

                        <div class="section-title division-title">
                            Division : <?= htmlspecialchars($divisionName) ?>
                        </div>

                        <div class="table-wrap">

                            <table style="width:100%;border-collapse:collapse;font-size:14px;text-align:center;">

                                <thead>
                                <tr style="background:#f1f5f9;">
                                    <th>Sno</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>

                                <?php $i=1; foreach ($users as $u): ?>

                                    <tr>

                                        <td><?= $i++ ?></td>

                                        <td><?= htmlspecialchars($u['name']) ?></td>

                                        <td><?= htmlspecialchars($u['u_name']) ?></td>

                                        <td>
                                            <?php if($u['is_active']) : ?>
                                                <span class="status-active">Active</span>
                                            <?php else : ?>
                                                <span class="status-inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <a href="Ncd_Reports/ajax/delete_user.php?id=<?= $u['id'] ?>&role=checker&<?= $preservedQuery ?>"
                                               onclick="return confirm('Delete this checker?')"
                                               class="delete-btn">
                                                🗑 Delete
                                            </a>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        <?php endif; ?>

    </div>

    <script>
        setTimeout(() => {

            const box = document.getElementById("msgBox");

        if (box) {
            box.style.display = "none";
        }

        const currentUrl = new URL(window.location.href);

        currentUrl.searchParams.delete('msg');
        currentUrl.searchParams.delete('type');

        window.history.replaceState({}, document.title, currentUrl.toString());

        }, 3000);
    </script>

    <script>
        const userType = "<?= $user_type ?>";
        const sessionDivisionId = "<?= $division_id ?? '' ?>";

        document.addEventListener("DOMContentLoaded", function () {

            const divisionEl = document.getElementById("division");

            if (userType === "ncd_checker" && sessionDivisionId) {

                divisionEl.value = sessionDivisionId;
                divisionEl.disabled = true;
                divisionEl.style.background = "#f3f4f6";
            }

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const divisionId = document.getElementById("division").value;
            const districtEl = document.getElementById("district_id");
            const districtNameEl = document.getElementById("district_name");

            function loadDistricts(divId) {

                if (!divId) return;

                fetch("Ncd_Reports/ajax/get_districts_for_maker.php?division_id=" + divId)

                    .then(res => res.json())

            .then(data => {

                    districtEl.innerHTML = '<option value="">-- Select District --</option>';

                data.forEach(d => {

                    districtEl.innerHTML += `
                            <option value="${d.district_code}" data-name="${d.district_name}">
                                ${d.district_name} (${d.district_code || ''})
                            </option>
                        `;

            });

            });
            }

            if (divisionId) {
                loadDistricts(divisionId);
            }

            districtEl?.addEventListener("change", function () {

                const selected = this.options[this.selectedIndex];

                districtNameEl.value = selected.getAttribute("data-name") || "";

            });

        });
    </script>

<?php if ($currentUserType === 'ncd_checker' || $currentUserType === 'ncd_admin'): ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const deptId = "<?= $dept_id ?? '' ?>";
            const deptSelect = document.getElementById("authority_id");

            if (deptId && deptSelect) {

                deptSelect.value = deptId;

                deptSelect.disabled = true;

                deptSelect.style.background = "#f3f4f6";
                deptSelect.style.cursor = "not-allowed";

                deptSelect.removeAttribute("name");

                const hiddenInput = document.createElement("input");

                hiddenInput.type = "hidden";
                hiddenInput.name = "authority_id";
                hiddenInput.value = deptId;

                document.forms[0].appendChild(hiddenInput);
            }

        });
    </script>

<?php endif; ?>

<?php
page_footer_start();
page_footer_end();
?>