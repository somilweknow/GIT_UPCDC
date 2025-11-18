<?php
	date_default_timezone_set("Asia/Kolkata");
	// 	$db = mysqli_connect("localhost", "root", "mysql", "upcdc_2025");
	// $db = mysqli_connect("localhost", "upcdc", "GGGxWcMjlZ2X65AR", "upcdc_2025");
	// $db = mysqli_connect("localhost", "root", "mysql", "cloudsdj_upcds");
	// $db = mysqli_connect("localhost", "root", "mysql", "upcdc");
	$db = mysqli_connect("localhost", "root", "mysql", "upcdc_2025");
	if(!$db){
		die("Error 1 : Contact Administrator.");
	}
	mysqli_query($db, 'SET character_set_results=utf8'); 
	// mysqli_query($db, 'SET names=utf8'); 
	mysqli_query($db, 'SET character_set_client=utf8'); 
	mysqli_query($db, 'SET character_set_connection=utf8'); 
	mysqli_query($db, 'SET character_set_results=utf8'); 
	mysqli_query($db, 'SET collation_connection=utf8_general_ci'); 

function execute_query($query){
	global $db;
	$result = mysqli_query($db, $query);
	if(mysqli_error($db)){
		$sql = 'insert into mysql_dump (mysql_dump, mysql_error, creation_time) values ("'.$query.'", "'.mysqli_error($db).'", "'.date("Y-m-d H:i:s").'")';
		mysqli_query($db, $sql);
	}
	return $result;
}

function insert_id($db=''){
	global $db;
	return mysqli_insert_id($db);
}

function select_data($table, $fields, $where='', $join='', $join_on='', $union='', $union_cols=''){
	
}

function update_data($table, $fields, $values, $where){
	
}

function delete_data($table, $fields, $values, $where){
	
}

?>