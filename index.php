<?php
/*
---------------------------------------------------------------------------------------------------------------------
|-------------------------------------------------------------------------------------------------------------------|
|      swift_db v0.01                                                                                               |
|-------------------------------------------------------------------------------------------------------------------|
|            * Created by K Tony (Twitter: @tawn33y) . 2016                                                         |
|            * Distributed under the Open Source License                                                            |
|            * For instructions on how to use this library, open the 'readme.txt' file in the root directory        |
|-------------------------------------------------------------------------------------------------------------------|
---------------------------------------------------------------------------------------------------------------------
*/



/* DO NOT DELETE THE FOLLOWING LINE OF CODE!!
This line connects to the database and loads the functions for performing the database operations */
require_once "./core/swift_sql.php";


/* SELECT column_names FROM table_name */
$query = select("hello_world", ['id', 'name', 'random'], []);
print_results($query);

/* SELECT column_names FROM table_name; Using a for loop */
// $query = select("hello_world", ['id', 'name', 'random'], []);
// for($i = 0; $i < count($query); $i++) { print_results($query[$i]); }
// for($i = 0; $i < count($query); $i++) { echo $query[$i]['name'] . "<br />"; }

/* SELECT column_name FROM table_name */
// $query = select("hello_world", ['name'], [])[0]['name'];
// echo $query;

/* SELECT column_name(s) FROM table_name WHERE column_name = some_value */
// $query = select("hello_world", ['id', 'name', 'random'], ['id' => '1']);
// print_results($query);

/* SELECT column_name(s) FROM table_name WHERE column_name = some_value ORDER BY ASC */
// $query = select("hello_world", ['id', 'name', 'random'], [], 'name', 'ASC');
// print_results($query);

/* SELECT column_name(s) FROM table_name WHERE column_name LIKE %some_value% && column_name == even number ORDER BY `column_name` DESC */
// $query = select2("hello_world", ['id', 'name', 'random'], "`name` LIKE '%o%' && `id` %2 = 0 ORDER BY `id` DESC");
// print_results($query);

/* DELETE column_name(s) FROM table_name WHERE some_column = some_value */
// delete("hello_world", "id", "3");

/* UPDATE table_name SET column1 = value WHERE some_column = some_value */
// update("hello_world", ['name' => 'Hello'], ["id" => "1"]);

/* UPDATE table_name SET column1 = value, column2 = value2,... WHERE some_column = some_value */
// update("hello_world", ['name' => 'Hello', 'random' => 'uirdfx'], ["id" => "1"]);

/* INSERT column_name(s) INTO table_name WHERE column_name = xx; */
// insert("hello_world", ["name" => "Olla"]);

/* INSERT column_names INTO table_name WHERE column_name = xx; Return id of the last inserted row */
// echo insert("hello_world", ["name" => "Olla", "random" => "dsxuifjk"]);

?>
