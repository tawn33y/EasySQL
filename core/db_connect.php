<?php
/*
---------------------------------------------------------------------------------------------------------------------
|-------------------------------------------------------------------------------------------------------------------|
|      Swift SQL                                                                                                    |
|-------------------------------------------------------------------------------------------------------------------|
|            * Created by K Tony (Twitter: @tawn33y)                                                                |
|            * Distributed under the Open Source License                                                            |
|            * For instructions on how to use this library, open the 'readme.md' file in the root directory         |
|-------------------------------------------------------------------------------------------------------------------|
---------------------------------------------------------------------------------------------------------------------
*/



/*-------------------------------------------------------------------------------------------------------------
	ABOUT
	- This file sets up the database connection

	Note:
	- In production, it is recommended to put all credentials in a single separate file
	- Also remember to exclude this file from source code managers like Git and SVN.
-------------------------------------------------------------------------------------------------------------*/

$db_server 		= "localhost";	// database server
$db_user 			= "root"; 			// database user
$db_password 	= ""; 					// database password
$db_name		 	= "sample_db"; 	// database name

try {
	// connect to database
	$conn = new PDO("mysql:host=". $db_server . ";dbname=" . $db_name, $db_user, $db_password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// echo "Connected successfully";
}
catch(PDOException $e) {
	// return error message if connection failed
	echo "Sorry we're experiencing connection problems: " . $e->getMessage();
}

// uncomment the line below to close the connection
// $conn = null;

?>
