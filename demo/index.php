<?php

// require EasySQL
require_once("assets/easysql.min.php");

// create a new instance
$conn = new easysql();

// set database credentials
try {
	$conn->set_credentials_via_json_file("assets/credentials.json");
} catch (Exception $e) {
	$conn->pretty_print($e->getMessage());
}


// test & debug
try {
	// your code goes here
	// [...]
} catch (Exception $e) {
	$conn->pretty_print($e->getMessage());
}

// destroy the instance
$conn->__destruct();
unset($conn);

?>
