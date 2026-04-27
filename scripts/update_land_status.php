<?php
date_default_timezone_set('Asia/Calcutta');
$time = mktime(true);
include("settings.php");
include("setting_sms.php");
error_reporting(E_ALL);
ini_set("display_errors", 1);

if(isset($_POST['id'], $_POST['value'], $_POST['table'])){

    $id = (int)$_POST['id'];
    $value = (int)$_POST['value'];
    $table = $_POST['table'];

    // ✅ Allow only specific tables (Security)
    $allowed_tables = ['marketing','upss','block_union','jila_sehkari'];

    if(!in_array($table, $allowed_tables)){
        echo "Invalid Table";
        exit;
    }

    $sql = "UPDATE `$table` SET land_conf='$value' WHERE sno='$id'";

    if(mysqli_query($db,$sql)){
        echo "success";
    }else{
        echo "error";
    }

}else{
    echo "Invalid Request";
}
?>