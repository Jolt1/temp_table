<?php
function db_open(){
	
 	define("DB_SERVER","localhost");
	define("DB_USER","username_here");
	define("DB_PASS","pass_here");
	define("DB_NAME","dbname_here"); 
	
 	global $mysqlilink;
	$mysqlilink = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	}

//*************************************************
// Confirms MySQL querry
//*************************************************
function confirm_query($result_set){
	global $mysqlilink;
	if(!$result_set){
	die(mysqli_error($mysqlilink));	
	} }
//*************************************************
// preps entry for DB
//*************************************************
function mp( $value ) {
	global $mysqlilink;
	$value = mysqli_real_escape_string( $mysqlilink, $value );
	return $value;
	}

//************************************
// quick qu	
//************************************
function qu($query){
	global $mysqlilink;
	$result =  mysqli_query($mysqlilink, $query);
	confirm_query($result);
	return $result;
	}

//************************************
//quick fetch
//************************************
function fa($value){
	$result = mysqli_fetch_array($value, MYSQLI_BOTH);
	return $result;
	}
 
?>