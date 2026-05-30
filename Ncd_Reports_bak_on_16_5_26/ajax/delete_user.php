<?php
session_start();
include(__DIR__ . "/../../scripts/settings.php");

$id        = intval($_GET['id'] ?? 0);
$userType  = $_SESSION['usertype'] ?? '';
$userId    = intval($_SESSION['usersno'] ?? 0);

if (!$id) {
    die("Invalid request");
}

/* =========================
   ADMIN DELETE LOGIC
========================= */
if ($userType === 'ncd_admin') {

    $sql = "
        DELETE FROM ncd_users
        WHERE id = '$id'
        AND (
            creator_admin_id = '$userId'
            OR creator_checker_id IN (
                SELECT id FROM (
                    SELECT id FROM ncd_users 
                    WHERE creator_admin_id = '$userId'
                ) AS tmp
            )
        )
    ";

}

/* =========================
   CHECKER DELETE LOGIC
========================= */
elseif ($userType === 'ncd_checker') {

    $sql = "
        DELETE FROM ncd_users
        WHERE id = '$id'
        AND creator_checker_id = '$userId'
    ";

} else {
    die("Unauthorized Access");
}

/* =========================
   EXECUTE
========================= */
execute_query($sql);

/* =========================
   REDIRECT BACK
========================= */
header("Location: ../add_ncd_user.php?usertype=$userType&msg=Deleted Successfully&type=success");
exit;
?>