<?php
date_default_timezone_set("Asia/Kolkata");

// $db_upcod = mysqli_connect("localhost", "root", "mysql", "upcod");
$db_upcod = mysqli_connect("localhost", "root", "mysql", "upcod_10_2_26");
if(!$db_upcod){
    die("UPCOD DB Error : Contact Administrator.");
}

mysqli_set_charset($db_upcod, "utf8");

function execute_upcod_query($query){
    global $db_upcod;

    $result = mysqli_query($db_upcod, $query);

    if(mysqli_error($db_upcod)){
        global $db;
        if(isset($db)){
            $log = "
                INSERT INTO mysql_dump
                (mysql_dump, mysql_error, creation_time)
                VALUES
                ('".mysqli_real_escape_string($db, $query)."',
                 '".mysqli_real_escape_string($db, mysqli_error($db_upcod))."',
                 '".date('Y-m-d H:i:s')."')
            ";
            mysqli_query($db, $log);
        }
    }
    return $result;
}

function upcod_insert_id(){
    global $db_upcod;
    return mysqli_insert_id($db_upcod);
}
?>
