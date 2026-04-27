<?php
date_default_timezone_set('Asia/Calcutta');
include("settings.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid Request']);
    exit;
}

$apex_id = $_POST['apex_id'] ?? '';

if (!$apex_id) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid Apex ID']);
    exit;
}

// sanitize
foreach ($_POST as $k => $v) {
    if (is_array($v)) {
        foreach ($v as $key => $val) {
            $_POST[$k][$key] = htmlspecialchars($val, ENT_QUOTES);
        }
    } else {
        $_POST[$k] = htmlspecialchars($v, ENT_QUOTES);
    }
}

// delete old
execute_query("DELETE FROM survey_invoice_apex_designation WHERE apex_id = '$apex_id'");

// ✅ SAVE SELECTED (USING post_id)
if (!empty($_POST['posts'])) {
    foreach ($_POST['posts'] as $post_id) {

        $post_id = (int)$post_id;

        // fetch name from master
        $res = execute_query("SELECT post_name FROM master_designation_apex_new WHERE sno = $post_id");
        $row = mysqli_fetch_assoc($res);

        if (!$row) continue;

        $post_name = mysqli_real_escape_string($db, $row['post_name']);

        execute_query("
            INSERT INTO survey_invoice_apex_designation (post_id, post_name, apex_id)
            VALUES ($post_id, '$post_name', '$apex_id')
        ");
    }
}

// ✅ SAVE NEW POSTS
if (!empty($_POST['new_post_name'])) {

    foreach ($_POST['new_post_name'] as $i => $name) {

        if (trim($name) == '') continue;

        $new_post = mysqli_real_escape_string($db, $name);
        $technical = ($_POST['technical'][$i] == 'T') ? 'T' : NULL;

        // insert into master
        execute_query("
            INSERT IGNORE INTO master_designation_apex_new (post_name, technical)
            VALUES ('$new_post', " . ($technical ? "'T'" : "NULL") . ")
        ");

        // get post_id
        $res = execute_query("
            SELECT sno FROM master_designation_apex_new 
            WHERE post_name = '$new_post' LIMIT 1
        ");
        $row = mysqli_fetch_assoc($res);

        if (!$row) continue;

        $post_id = $row['sno'];

        // insert mapping
        execute_query("
            INSERT INTO survey_invoice_apex_designation (post_id, post_name, apex_id, technical)
            VALUES ($post_id, '$new_post', '$apex_id', " . ($technical ? "'T'" : "NULL") . ")
        ");
    }
}

echo json_encode([
    'status' => 'success',
    'msg' => 'Saved Successfully'
]);