<?php
include("scripts/settings.php");

$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : 0;

/* ══════════════════════════════════════════════════════════
   Get division_id — 3 fallback levels:
   1. $_SESSION['division_id']          (works on local)
   2. master_division.username match    (live fix — username stored there)
   3. master_division.sno via usersno   (last resort)
══════════════════════════════════════════════════════════ */
$session_division_id = 0;

// Level 1: session
if (!empty($_SESSION['division_id'])) {
    $session_division_id = is_array($_SESSION['division_id'])
        ? intval($_SESSION['division_id'][0])
        : intval($_SESSION['division_id']);
}

// Level 2: match by username in master_division
if ($session_division_id == 0 && !empty($_SESSION['username'])) {
    $uname = mysqli_real_escape_string($GLOBALS['con'] ?? $GLOBALS['conn'] ?? $GLOBALS['db'] ?? null,
             $_SESSION['username']);
    // Try email/username fields in master_division
    $r = mysqli_fetch_assoc(execute_query(
        "SELECT sno FROM master_division 
         WHERE username = '{$_SESSION['username']}' 
            OR username = '{$_SESSION['user_id']}' 
         LIMIT 1"
    ));
    if ($r) $session_division_id = intval($r['sno']);
}

// Level 3: usersno directly as division sno
if ($session_division_id == 0 && !empty($_SESSION['usersno'])) {
    $session_division_id = intval($_SESSION['usersno']);
}

/* ── Approve / Reject ── */
$row     = null;
$prog_id = 0;
$msg     = '';

if ($district_id > 0) {
    $res = execute_query("
        SELECT bp.*, md.division_name, mdt.district_name
        FROM bpacs_progress bp
        LEFT JOIN master_division  md  ON md.sno  = bp.mandal_id
        LEFT JOIN master_district  mdt ON mdt.sno = bp.district_id
        WHERE bp.district_id = '$district_id'
          AND bp.is_deleted  = 0
        ORDER BY bp.id DESC
        LIMIT 1
    ");
    $row     = mysqli_fetch_assoc($res);
    $prog_id = $row ? intval($row['id']) : 0;

    if ($prog_id > 0) {
        if (isset($_POST['approve'])) {
            execute_query("UPDATE bpacs_progress SET approval_status=1, approved_by='{$_SESSION['username']}', approved_at=NOW() WHERE id='$prog_id'");
            $msg = "<div class='alert alert-success'>✔ Approved Successfully</div>";
            $res = execute_query("SELECT bp.*,md.division_name,mdt.district_name FROM bpacs_progress bp LEFT JOIN master_division md ON md.sno=bp.mandal_id LEFT JOIN master_district mdt ON mdt.sno=bp.district_id WHERE bp.id='$prog_id'");
            $row = mysqli_fetch_assoc($res);
        }
        if (isset($_POST['reject'])) {
            execute_query("UPDATE bpacs_progress SET approval_status=2, approved_by='{$_SESSION['username']}', approved_at=NOW() WHERE id='$prog_id'");
            $msg = "<div class='alert alert-danger'>✘ Rejected</div>";
            $res = execute_query("SELECT bp.*,md.division_name,mdt.district_name FROM bpacs_progress bp LEFT JOIN master_division md ON md.sno=bp.mandal_id LEFT JOIN master_district mdt ON mdt.sno=bp.district_id WHERE bp.id='$prog_id'");
            $row = mysqli_fetch_assoc($res);
        }
    }
}

/* ── Always fetch ALL divisions with their districts ── */
$divisions_res = execute_query("SELECT sno, division_name FROM master_division ORDER BY division_name");
$all_divisions = [];
while ($dv = mysqli_fetch_assoc($divisions_res)) {
    $all_divisions[$dv['sno']] = [
        'division_name' => $dv['division_name'],
        'districts'     => []
    ];
}

$all_dist_res = execute_query("SELECT sno, district_name, division_id FROM master_district ORDER BY district_name");
while ($dd = mysqli_fetch_assoc($all_dist_res)) {
    $dvid = $dd['division_id'];
    if (isset($all_divisions[$dvid])) {
        $all_divisions[$dvid]['districts'][] = ['sno' => $dd['sno'], 'district_name' => $dd['district_name']];
    }
}

/* ── Pre-fetch approval status for all districts in one query ── */
$status_map = [];
$st_res = execute_query("
    SELECT bp.district_id, bp.approval_status
    FROM bpacs_progress bp
    INNER JOIN (
        SELECT district_id, MAX(id) AS max_id
        FROM bpacs_progress
        WHERE is_deleted = 0
        GROUP BY district_id
    ) latest ON latest.max_id = bp.id
");
while ($sr = mysqli_fetch_assoc($st_res)) {
    $status_map[$sr['district_id']] = $sr['approval_status'];
}

page_header_start();
page_header_end();
page_sidebar();
?>

<style>
    * { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

    .main-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 15px 10px;
    }

    .main-box {
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        max-width: 1400px;
        margin: 0 auto 30px;
    }

    .dashboard-page-title {
        font-size: 24px;
        font-weight: 800;
        text-align: center;
        color: #2d3748;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 4px solid #667eea;
        padding-bottom: 16px;
        margin-bottom: 30px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        margin: 28px 0 15px;
        background: linear-gradient(to right, #667eea, #764ba2);
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dist-table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(0,0,0,0.07);
        margin-bottom: 8px;
    }

    .dist-table thead tr {
        background: linear-gradient(to right, #667eea, #764ba2);
        color: white;
    }

    .dist-table thead th {
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 700;
        text-align: left;
    }

    .dist-table tbody tr { border-bottom: 1px solid #e2e8f0; transition: background 0.2s; }
    .dist-table tbody tr:last-child { border-bottom: none; }
    .dist-table tbody tr:hover { background: #eef2ff; }

    .dist-table tbody td {
        padding: 11px 16px;
        font-size: 14px;
        color: #2d3748;
        vertical-align: middle;
    }

    .status-badge { display:inline-block; padding:4px 11px; border-radius:20px; font-size:12px; font-weight:700; }
    .sb-approved  { background:#c6f6d5; color:#22543d; }
    .sb-rejected  { background:#fed7d7; color:#742a2a; }
    .sb-pending   { background:#fefcbf; color:#744210; }
    .sb-none      { background:#e2e8f0; color:#718096; }

    .btn-open {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white; border: none; padding: 7px 18px; font-size: 13px;
        font-weight: 700; border-radius: 7px; cursor: pointer; text-decoration: none;
        display: inline-block; transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(102,126,234,0.3);
    }
    .btn-open:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(102,126,234,0.5); color:white; text-decoration:none; }

    /* Detail view */
    .form-group { margin-bottom: 20px; }
    label { font-size:15px; font-weight:600; margin-bottom:7px; display:block; color:#4a5568; }

    .ro-field {
        height: 50px; font-size: 16px; border-radius: 8px;
        border: 2px solid #e2e8f0; padding: 10px 16px;
        background-color: #e2e8f0; color: #2d3748; width: 100%; box-sizing: border-box;
    }
    textarea.ro-field { height: 110px; resize: none; }
    .row-gap { margin-bottom: 0; }

    .alert { font-size:16px; padding:14px 20px; border-radius:8px; margin-bottom:20px; font-weight:600; }
    .alert-success { background:#c6f6d5; border:2px solid #48bb78; color:#22543d; }
    .alert-danger  { background:#fed7d7; border:2px solid #fc8181; color:#742a2a; }

    .approve-reject-box {
        background:#f7fafc; border:2px solid #e2e8f0; border-radius:12px;
        padding:26px; margin-top:26px; text-align:center;
    }
    .approve-reject-box h4 { font-size:18px; font-weight:700; color:#2d3748; margin-bottom:16px; }

    .btn-approve {
        background: linear-gradient(135deg, #38a169, #2f855a);
        color:white; border:none; padding:12px 34px; font-size:16px; font-weight:700;
        border-radius:10px; cursor:pointer; transition:all 0.3s; margin-right:12px;
        box-shadow:0 4px 14px rgba(56,161,105,0.4);
    }
    .btn-approve:hover { transform:translateY(-2px); box-shadow:0 7px 22px rgba(56,161,105,0.5); }

    .btn-reject {
        background: linear-gradient(135deg, #e53e3e, #c53030);
        color:white; border:none; padding:12px 34px; font-size:16px; font-weight:700;
        border-radius:10px; cursor:pointer; transition:all 0.3s;
        box-shadow:0 4px 14px rgba(229,62,62,0.4);
    }
    .btn-reject:hover { transform:translateY(-2px); box-shadow:0 7px 22px rgba(229,62,62,0.5); }

    .btn-back {
        background:#718096; color:white; border:none; padding:10px 24px;
        font-size:14px; font-weight:700; border-radius:8px; text-decoration:none;
        display:inline-block; margin-bottom:18px; transition:all 0.2s;
    }
    .btn-back:hover { background:#4a5568; color:white; text-decoration:none; }

    .no-data-box {
        text-align:center; padding:30px; color:#718096; font-size:15px; font-weight:600;
        background:#f7fafc; border-radius:10px; border:2px dashed #e2e8f0;
    }

    .division-block { margin-bottom: 35px; }

    @media (max-width:768px) {
        .main-box { padding: 16px; }
        .dashboard-page-title { font-size: 17px; }
        .section-title { font-size: 13px; }
        .ro-field { height: 44px; font-size: 14px; }
        .btn-approve, .btn-reject { padding:10px 16px; font-size:14px; margin-bottom:8px; }
        .dist-table thead th, .dist-table tbody td { padding:8px 9px; font-size:12px; }
    }
</style>

<div class="main-container">
    <div class="row">
        <div class="col-md-12">
            <div class="main-box">

                <div class="dashboard-page-title">जिले कि प्रगति सुचना</div>

<?php if ($district_id == 0): ?>

    <!-- ══════════════════════════════════════════════════
         STEP 1 : All divisions → districts list
    ══════════════════════════════════════════════════ -->

    <?php foreach ($all_divisions as $div_sno => $division):
        if (empty($division['districts'])) continue;
    ?>
        <div class="division-block">
            <div class="section-title">
                मण्डल: <?= htmlspecialchars($division['division_name']) ?>
            </div>
            <div class="table-responsive">
            <table class="dist-table">
                <thead>
                    <tr>
                        <th>क्र.सं.</th>
                        <th>जिले का नाम</th>
                        <th>स्थिति</th>
                        <th>विवरण</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($division['districts'] as $i => $dist):
                        $st = $status_map[$dist['sno']] ?? null;
                        if ($st === null)  $badge = '<span class="status-badge sb-none">डेटा नहीं</span>';
                        elseif ($st == 1)  $badge = '<span class="status-badge sb-approved">✔ Approved</span>';
                        elseif ($st == 2)  $badge = '<span class="status-badge sb-rejected">✘ Rejected</span>';
                        else               $badge = '<span class="status-badge sb-pending">⏳ Pending</span>';
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><b><?= htmlspecialchars($dist['district_name']) ?></b></td>
                            <td><?= $badge ?></td>
                            <td>
                                <a href="pacs_info_dr.php?district_id=<?= $dist['sno'] ?>" class="btn-open">
                                    📋 विवरण देखें
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    <?php endforeach; ?>

<?php else: ?>

    <!-- ══════════════════════════════════════════════════
         STEP 2 : Full detail + Approve / Reject
    ══════════════════════════════════════════════════ -->

    <a href="pacs_info_dr.php" class="btn-back">← वापस जाएं</a>

    <?= $msg ?>

    <?php if (!$row): ?>
        <div class="no-data-box">इस जिले के लिए कोई B-PACS प्रगति डेटा दर्ज नहीं है।</div>
    <?php else: ?>

        <div class="section-title">मूल विवरण</div>
        <div class="row row-gap">
            <div class="col-md-4"><div class="form-group"><label>मण्डल</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['division_name'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>जिला</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['district_name'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>कुल बी-पैक्स</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['total_bpacs'] ?? '') ?>" readonly></div></div>
        </div>
        <div class="row row-gap">
            <div class="col-md-4"><div class="form-group"><label>नवगठित बी-पैक्स</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['new_bpacs'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>सीसी लिमिट (10 लाख)</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['cc_limit'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>उर्वरक पैक्स</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['fertilizer'] ?? '') ?>" readonly></div></div>
        </div>
        <div class="row row-gap">
            <div class="col-md-4"><div class="form-group"><label>10% मार्जिन मनी जमा पैक्स</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['fd'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>मरम्मत/सुदृढ़ीकरण</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['repair'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>RKVP/SADP</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['rkvp'] ?? '') ?>" readonly></div></div>
        </div>
        <div class="row row-gap">
            <div class="col-md-4"><div class="form-group"><label>उन्नयन धनराशि</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['upgrade_amt'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>समीक्षा</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['review_status'] ?? '') ?>" readonly></div></div>
            <div class="col-md-4"><div class="form-group"><label>लंबित समीक्षा</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['pending_review'] ?? '') ?>" readonly></div></div>
        </div>

        <div class="section-title">फसली ऋण</div>
        <div class="row row-gap">
            <div class="col-md-6"><div class="form-group"><label>वितरित धनराशि</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['loan_distributed'] ?? '') ?>" readonly></div></div>
            <div class="col-md-6"><div class="form-group"><label>वसूली</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['loan_recovery'] ?? '') ?>" readonly></div></div>
        </div>

        <div class="section-title">सोलर रूफटॉप (बी-पैक्स स०)</div>
        <div class="row row-gap">
            <div class="col-md-6"><div class="form-group"><label>पूर्व में हो चुका है</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['solar'] ?? '') ?>" readonly></div></div>
            <div class="col-md-6"><div class="form-group"><label>मार्च 2026 में होना है</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['solar_26'] ?? '') ?>" readonly></div></div>
        </div>

        <div class="section-title">अन्य विवरण</div>
        <div class="row row-gap">
            <div class="col-md-6"><div class="form-group"><label>कंप्यूटर ऑपरेटर सहकर सारथी कि संख्या</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['operator'] ?? '') ?>" readonly></div></div>
            <div class="col-md-6"><div class="form-group"><label>ग्रेडिंग (A, B, C, D)</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['grading'] ?? '') ?>" readonly></div></div>
        </div>
        <div class="row row-gap">
            <div class="col-md-6"><div class="form-group"><label>गेहूं केन्द्र</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['wheat_center'] ?? '') ?>" readonly></div></div>
            <div class="col-md-6"><div class="form-group"><label>दलहन/तिलहन केन्द्र</label>
                <input class="ro-field" type="text" value="<?= htmlspecialchars($row['pulse_center'] ?? '') ?>" readonly></div></div>
        </div>

        <div class="section-title">चयनित समितियाँ</div>
        <div class="row">
            <div class="col-md-12"><div class="form-group"><label>चयनित समितियों का नाम</label>
                <textarea class="ro-field" readonly><?= htmlspecialchars($row['grain_society_name'] ?? '') ?></textarea>
            </div></div>
        </div>

        <div class="approve-reject-box">
            <h4>अनुमोदन / अस्वीकृति</h4>
            <form method="post" action="pacs_info_dr.php?district_id=<?= $district_id ?>">
                <button type="submit" name="approve" class="btn-approve"
                    onclick="return confirm('क्या आप इस रिकॉर्ड को Approve करना चाहते हैं?')">
                    ✔ Approve
                </button>
                <button type="submit" name="reject" class="btn-reject"
                    onclick="return confirm('क्या आप इस रिकॉर्ड को Reject करना चाहते हैं?')">
                    ✘ Reject
                </button>
            </form>
        </div>

    <?php endif; ?>

<?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
page_footer_start();
page_footer_end();
?>
