<?php
/*
---------------------------------------------------------------------------------------------------------------------
|-------------------------------------------------------------------------------------------------------------------|
|      swift_sql v0.01                                                                                               |
|-------------------------------------------------------------------------------------------------------------------|
|            * Created by K Tony (Twitter: @tawn33y) . 2016                                                         |
|            * Distributed under the Open Source License                                                            |
|            * For instructions on how to use this library, open the 'readme.txt' file in the root directory        |
|-------------------------------------------------------------------------------------------------------------------|
---------------------------------------------------------------------------------------------------------------------
*/


function alter($table_name, $column_name, $type) {
	/* -------------------------------------------------------------------------------------------------------------
	FUNCTION ONE: alter table and add columns
		ABOUT:
		- This function alters the existing table and adds a new column
		KEY:
		- $table_name = name of the table to alter the data into
		- $column_name = name of the new column to be inserted
		- $type = type of the column to be inserted e.g INT
	------------------------------------------------------------------------------------------------------------- */

	// require the connection file
	require './core/db_connect.php';

	try {
		// use a prepared statement to add column to table
		$stmt = $conn->prepare("ALTER TABLE `{$table_name}` ADD `{$column_name}` {$type}");
		$stmt->execute();
	}
	catch(PDOException $e) {
		// return error if operation failed
		echo "Error: " . $e->getMessage();
	}
}


function select($table_name, $select_data, $select_queries, $order_column = "", $order_query = "") {
	/* -------------------------------------------------------------------------------------------------------------
	FUNCTION TWO: select data from a table
		ABOUT:
		- This function selects one or multiple rows from a certain table matching a certain criteria
		KEY:
		- $table_name = name of the table to select the data from
		- $select_data = array containing the data to be selected from the table
		- $select_queries = array containing the criteria with which to select data from the table. Can be empty e.g when selecting an entire table
		- $order_column and $order_query = used if the data should be returned in a specific order e.g "ORDER by column_name ($order_column) ascending($order_query)". Both are initially set to null;
		Note:
		- Use this function only when doing simple selects like "SELECT column_name WHERE id = 1"
		- For advanced selections like "Right Join" or "LIKE", use the function after this i.e select2
	------------------------------------------------------------------------------------------------------------- */

	// require the connection file
	require './core/db_connect.php';

	// initiliaze a 2D array with the name of 'data' which will hold the results of the query
	$data = [];

	// escape data to ensure it is safe/clean
	foreach ($select_data as $key => $value) { addslashes($value); }
	foreach ($select_queries as $key => $value) { addslashes($value); }

	// convert $select_data to a simple string for execution
	if(strstr($select_data[0], "(")) {
		$select_data = implode(", ", $select_data);
	} else {
		$select_data = "`" . implode("`, `", $select_data) . "`";
	}

	// convert $select_query to a simple string for execution
	if(!empty($select_queries)) {
		foreach($select_queries as $fields => $dataX) { $select_queries2[] = '`' . $fields . '` = \'' . $dataX . '\''; }
		$select_queries3 = implode(" && ", $select_queries2);
		$select_query = "WHERE {$select_queries3}";
	} else { $select_query = ""; }

	// prints string if you need the data to be returned in a specific order e.g by column_name ascending
	if(!empty($order_column)) { $order = "ORDER BY `{$order_column}` {$order_query}"; } else { $order = ""; }

	try {
		// use a prepared statement to select the data from the table
		$stmt = $conn->prepare("SELECT {$select_data} FROM `{$table_name}` {$select_query} {$order}");
		$stmt->execute();

		// set the resulting array to associative
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
			$data[] = $v;
		}
		// return the data
		return $data;
	}
	catch(PDOException $e) {
		// return error message if operation failed
		echo "Error: " . $e->getMessage();
	}
}


function select2($table_name, $select_data, $clause="") {
	/* -------------------------------------------------------------------------------------------------------------
	FUNCTION THREE: advanced select data from a table
		ABOUT:
		- This function selects one or multiple rows from a certain table matching a certain criteria which is left to the user's descretion
		KEY:
		- $table_name = name of the table to select the data from
		- $select_data = array containing the data to be selected from the table
		- $select_queries = array containing the criteria with which to select data from the table. Can be empty e.g when selecting an entire table
		- $clause = criteria to be met when selecting data. Set to null; the user can use any clause e.g "WHERE column_name LIKE %el% ORDER BY column_name ASC";
	------------------------------------------------------------------------------------------------------------- */

	// require the connection file
	require './core/db_connect.php';

	// initiliaze a 2D array with the name of 'data' which will hold the results of the query
	$data = [];

	// escape data to ensure it is safe/clean
	foreach ($select_data as $key => $value) { addslashes($value); }

	// Convert $select_data to a simple string for execution
	if(strstr($select_data[0], "(")) {
		$select_data = implode(", ", $select_data);
	} else {
		$select_data = "`" . implode("`, `", $select_data) . "`";
	}

	// Create the statement to be used for matching the criteria during execution. If no clause is set by the user, it is left null
	$clause = !empty($clause) ? "WHERE ".$clause : "";

	try {
		// use a prepared statement to select the data from the table
		$stmt = $conn->prepare("SELECT {$select_data} FROM `{$table_name}` {$clause}");
		$stmt->execute();

		// set the resulting array to associative
		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
			$data[] = $v;
		}
		// return the data
		return $data;
	}
	catch(PDOException $e) {
		// return error message if operation failed
		echo "Error: " . $e->getMessage();
	}
}


function delete($table_name, $column_name, $id_name) {
	/* -------------------------------------------------------------------------------------------------------------
	FUNCTION FOUR: delete data from a table
		ABOUT:
		- This function deletes a row of data from a certain table
		KEY:
		- $table_name = name of the table to delete the data from
		- $column_name = name of the column to delete the data from
		- $id_name = row id to delete the data from
	------------------------------------------------------------------------------------------------------------- */

	// require the connection file
	require './core/db_connect.php';

	// make sure the ID is in integer
	$id_name = (int)$id_name;

	try {
		// delete the data from the table
		$sql = "DELETE FROM `{$table_name}` WHERE `{$column_name}` = '{$id_name}'";
		$conn->exec($sql); // use exec() because no results are returned
		// echo "Record deleted successfully";
	}
	catch(PDOException $e) {
		// return error message if operation failed
		echo "Error: " . $e->getMessage();
	}
}


function update($table_name, $update_data, $update_queries) {
	/* -------------------------------------------------------------------------------------------------------------
	FUNCTION FIVE: update data records in a table
		ABOUT:
		- This function updates a row of data in a certain table
		KEY:
		- $table_name = name of the table to update the data
		- $update_data = array containing the data to be updated in the table
		- $update_queries = array containing the rows to which to apply the update changes
	------------------------------------------------------------------------------------------------------------- */

	// require the connection file
	require './core/db_connect.php';

	// escape data to ensure it is safe/clean
	foreach ($update_data as $key => $value) { addslashes($value); }
	foreach ($update_queries as $key => $value) { addslashes($value); }

	// Convert $update_data to a simple string for execution
	foreach($update_data as $fields => $data) { $update_data2[] = '`' . $fields . '` = \'' . $data . '\''; }
	$update_data3 = implode(", ", $update_data2);

	// Convert $update_queries to a simple string for execution
	foreach($update_queries as $fields => $dataX) { $update_queries2[] = '`' . $fields . '` = \'' . $dataX . '\''; }
	$update_queries3 = implode(" && ", $update_queries2);
	$update_query = "WHERE {$update_queries3}";

	try {
		// use a prepared statement to select the data from the table
		$sql = "UPDATE `{$table_name}` SET {$update_data3} {$update_query}";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		// echo $stmt->rowCount() . " records UPDATED successfully";
	}
	catch(PDOException $e) {
		// return error message if operation failed
		echo "Error: " . $e->getMessage();
	}
}


function insert($table_name, $insert_data) {
	/* -------------------------------------------------------------------------------------------------------------
	FUNCTION SIX: insert data records into a table
		ABOUT:
		- This function inserts data into a certain table
		KEY:
		- $table_name = name of the table to inserted into the data
		- $insert_data = array containing the data to be inserted into the table
		Note:
		- It is not imperative to include the value for the primary column, you can just exclude it or leave it empty/null e.g id = ''
	------------------------------------------------------------------------------------------------------------- */

	// require the connection file
	require './core/db_connect.php';

	// escape data to ensure it is safe/clean
	foreach ($insert_data as $key => $value) { addslashes($value); }

	// convert the array into a single line for execution
	foreach($insert_data as $fields => $data) {
		$insert_keys[] 	 = '`' . $fields . '`';
		$insert_values[] = '\'' . $data . '\'';
		$insert_params[] = ':' . $fields;
		$insert_query1[] = $fields;
		$insert_query2[] = $data;
	}

	$insert_keys 	 = implode(", ", $insert_keys);
	$insert_values = implode(", ", $insert_values);
	$insert_params = implode(", ", $insert_params);

	try {
		// prepare statement for execution
		$stmt = $conn->prepare("INSERT INTO `{$table_name}` ({$insert_keys}) VALUES ({$insert_params})");
		for($i = 0; $i < count($insert_query1); $i++) {
			$stmt -> bindParam($insert_query1[$i], $insert_query2[$i]);
		}

		// insert a row
		$stmt->execute();

		// return the id of the row inserted
		return $conn->lastInsertId();
	}
	catch(PDOException $e) {
		// return error message if operation failed
		echo "Error: " . $e->getMessage();
	}
}


function print_results($data) {
	/* -------------------------------------------------------------------------------------------------------------
	BONUS FUNCTION: print results from an execution
		ABOUT:
		- This function prints the data received from an execution in a neat way
		- Delete this function; It's not required to do the database operations
	------------------------------------------------------------------------------------------------------------- */

	echo "<pre>";
	print_r($data);
	echo "</pre>";
}
?>
