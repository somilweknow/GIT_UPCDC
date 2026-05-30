<?php
session_start();
include(__DIR__ . "/../../scripts/settings.php");

$name       = trim($_POST['name'] ?? '');
$username   = trim($_POST['username'] ?? '');
$password   = trim($_POST['password'] ?? '');
$division   = trim($_POST['division'] ?? '');
$type_id    = trim($_POST['type_id'] ?? '');
$usertype   = $_POST['usertype'] ?? '';
$district_id   =  isset($_POST['district_id']) ? trim($_POST['district_id'] ?? null) : '';
$district_name = isset($_POST['district_id']) ? trim($_POST['district_name'] ?? null): '';

$creator_admin_id = null;


if ($usertype === 'ncd_admin') {
    $creator_admin_id = $_SESSION['usersno'];
    $authority_id = $_POST['authority_id'] ?? null;
}

$creator_checker_id = null;
if ($usertype === 'ncd_checker') {
    $creator_checker_id = $_SESSION['usersno'];
    $authority_id = $_POST['authority_id_hidden'] ;
}

if ($name === '' || $username === '' || $password === '' || $type_id === '') {
    header("Location: ../add_ncd_user.php?msg=All fields are required&type=error&usertype=$usertype");
    exit;
}

$check = execute_query("
    SELECT id 
    FROM ncd_users 
    WHERE u_name = '$username' 
      AND BINARY u_pass = '$password'
    LIMIT 1
");

if ($check && mysqli_num_rows($check) > 0) {
    header("Location: ../add_ncd_user.php?msg=User already exists with same username and password&type=error&usertype=$usertype");
    exit;
}

if (mysqli_num_rows($check) > 0) {
    header("Location: ../add_ncd_user.php?msg=User already exists with same username and password&type=error&usertype=$usertype");
    exit;
}

$divRes = execute_query("SELECT division_name FROM master_division WHERE sno = '$division'");
$divRow = mysqli_fetch_assoc($divRes);
$division_name = $divRow['division_name'] ?? '';

$sql = "INSERT INTO ncd_users 
(name, u_name, u_pass, type_id, division_id, division_name, district_id, district_name, 
 department_authority_id, creator_admin_id, creator_checker_id, is_active, created_at)
VALUES 
('$name','$username','$password','$type_id','$division','$division_name','$district_id','$district_name',
 '$authority_id', '$creator_admin_id', '$creator_checker_id', 1, NOW())";

if (execute_query($sql)) {

    $msg = ($type_id == 2)
        ? "Checker created successfully"
        : "Maker created successfully";

    header("Location: ../add_ncd_user.php?msg=" . urlencode($msg) . "&type=success&usertype=$usertype");
    exit;

} else {

    header("Location: ../add_ncd_user.php?msg=Error creating user&type=error&usertype=$usertype");
    exit;
}