<?php
include("../scripts/settings.php");

header('Content-Type: application/json');

// GET survey_id
$survey_id = isset($_POST['survey_id']) ? intval($_POST['survey_id']) : 0;

if ($survey_id <= 0) {
    echo json_encode(["status" => false, "message" => "Invalid survey_id"]);
    exit;
}

// FETCH apex_id
$get_apex = "SELECT apex_id FROM apex_si_1_1 WHERE sno = '$survey_id'";
$res_apex = execute_query($get_apex);

if (!$res_apex || mysqli_num_rows($res_apex) == 0) {
    echo json_encode(["status" => false, "message" => "Survey not found"]);
    exit;
}

$apex_id = mysqli_fetch_assoc($res_apex)['apex_id'];

// FILE CHECK
if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
    echo json_encode(["status" => false, "message" => "File upload failed"]);
    exit;
}

$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

if ($ext !== 'csv') {
    echo json_encode(["status" => false, "message" => "Only CSV allowed"]);
    exit;
}

// READ CSV
$file = $_FILES['file']['tmp_name'];

$rows = [];
if (($handle = fopen($file, 'r')) !== false) {
    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        $rows[] = $data;
    }
    fclose($handle);
}

if (empty($rows)) {
    echo json_encode(["status" => false, "message" => "CSV empty"]);
    exit;
}

// DATE FORMAT FIX
function formatDate($dateStr) {
    if (empty($dateStr)) return "NULL";

    $formats = ['d-m-Y', 'd-m-y', 'Y-m-d', 'd/m/Y', 'd/m/y'];

    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $dateStr);
        if ($d && $d->format($format) === $dateStr) {
            return "'" . $d->format('Y-m-d') . "'";
        }
    }

    return "NULL";
}

// GROUP CSV (LIKE PREFILL)
$grouped = [];

foreach ($rows as $index => $d) {

    if ($index == 0) continue; // skip header

    if (empty(array_filter($d))) continue;

    if (count($d) < 14) continue;

    $staff_type = strtolower(trim($d[0]));
    $hr_post_id = intval(trim($d[1]));

    if ($staff_type == '' || $hr_post_id <= 0) continue;

    if (!isset($grouped[$hr_post_id])) {
        $grouped[$hr_post_id] = [
            'staff_type' => $staff_type,
            'hr_post_id' => $hr_post_id,
            'sanctioned_post' => $d[2],
            'karyarat_post' => $d[3],
            'vacant_post' => $d[7],
            'direct_bharti' => $d[4],
            'promotion_bharti' => $d[5],
            'compassionate_bharti' => $d[6],
            'staff_members' => []
        ];
    }

    $grouped[$hr_post_id]['staff_members'][] = $d;
}

// INIT
$inserted = 0;
$skipped  = 0;
$errors   = [];

// PROCESS GROUPS
foreach ($grouped as $post_id => $group) {

    //DELETE OLD DATA FOR THIS POST (like frontend)
    execute_query("
        DELETE FROM apex_human_resource_info 
        WHERE survey_id='$survey_id'
        AND hr_post_id='$post_id'
    ");

    foreach ($group['staff_members'] as $d) {

        $staff_name   = mysqli_real_escape_string($db, trim($d[8]));
        $staff_sthiti = mysqli_real_escape_string($db, trim($d[9]));
        $staff_father = mysqli_real_escape_string($db, trim($d[10]));

        $staff_dob = formatDate($d[11] ?? '');

        $staff_mobile = $d[12] ?? '';
        $qualification = mysqli_real_escape_string($db, trim($d[13]));

        $sql = "
            INSERT INTO apex_human_resource_info (
                survey_id, apex_code, staff_type, hr_post_id,
                sanctioned_post, karyarat_pad,
                seedhi_bharti, paddonati_bharti, pratipurti_bharti,
                vacant_post,
                staff_post_id,
                staff_name, staff_sthiti, staff_father, staff_dob,
                staff_mobile, staff_qualification,
                created_at
            ) VALUES (
                '$survey_id', '$apex_id', '{$group['staff_type']}', '{$group['hr_post_id']}',
                '{$group['sanctioned_post']}', '{$group['karyarat_post']}',
                '{$group['direct_bharti']}', '{$group['promotion_bharti']}', '{$group['compassionate_bharti']}',
                '{$group['vacant_post']}',
                '{$group['hr_post_id']}',
                '$staff_name', '$staff_sthiti', '$staff_father', $staff_dob,
                '$staff_mobile', '$qualification',
                '".date("Y-m-d H:i:s")."'
            )
        ";

        execute_query($sql);

        if (mysqli_error($db)) {
            $skipped++;
            $errors[] = "Insert failed for post $post_id";
        } else {
            $inserted++;
        }
    }
}

// FINAL RESPONSE
echo json_encode([
    "status"   => true,
    "message"  => "CSV Import Completed",
    "inserted" => $inserted,
    "updated"  => $updated,
    "skipped"  => $skipped,
    "errors"   => $errors
]);

exit;
?>