<?php
header('Content-Type: application/json');

include("settings.php");

error_reporting(E_ALL);
ini_set('display_errors',0);

$district_id=intval($_POST['district_id']??0);

if($district_id==0){

    echo json_encode([
        'status'=>'error',
        'msg'=>'Invalid district'
    ]);

    exit;
}

$user_type=$_SESSION['user_type']??'';

$register_1=$_POST['register_1']??[];
$register_2=$_POST['register_2']??[];

$pacs_ids=array_unique(array_merge(
    array_keys($register_1),
    array_keys($register_2)
));

foreach($pacs_ids as $pacs_id){

    $pacs_id=intval($pacs_id);

    if($pacs_id==0)
        continue;

    $old=mysqli_fetch_assoc(execute_query("SELECT register_1,register_2 FROM pacs_register WHERE district_id='$district_id' AND pacs_id='$pacs_id'"));

    $b1=mysqli_real_escape_string($db,trim($register_1[$pacs_id]??($old['register_1']??'')));

    $b2=mysqli_real_escape_string($db,trim($register_2[$pacs_id]??($old['register_2']??'')));

    $chk=mysqli_fetch_assoc(execute_query("SELECT COUNT(*) cnt FROM pacs_register WHERE district_id='$district_id' AND pacs_id='$pacs_id'"));

    if($chk['cnt']==0){

        execute_query("INSERT INTO pacs_register(district_id,pacs_id,register_1,register_2,updated_on) VALUES('$district_id','$pacs_id','$b1','$b2',NOW())");

    }else{

        if($user_type=='ado'){

            execute_query("UPDATE pacs_register SET register_2='$b2',updated_on=NOW() WHERE district_id='$district_id' AND pacs_id='$pacs_id'");

        }else{

            execute_query("UPDATE pacs_register SET register_1='$b1',register_2='$b2',updated_on=NOW() WHERE district_id='$district_id' AND pacs_id='$pacs_id'");
        }
    }
}

echo json_encode([
    'status'=>'success',
    'msg'=>'Register data saved successfully'
]);

exit;