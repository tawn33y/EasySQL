<?php
/**
 * Created by PhpStorm.
 * User: dnyandika
 * Date: 9/12/2016
 * Time: 5:25 PM
 */

//Include and evaluate the necessary files for program execution.
require_once 'core/init.php';

$swiftSql = new SwiftSql();

////Select all records from table;
//$resultArray = $swiftSql->select('tbUser');

////Select all columns from table with where clause
//$bind[':user_name'] = 'admin';
//$resultArray = $swiftSql->select('tbUser', 'user_name=:user_name', $bind);

////Select specific columns from table
//$resultArray = $swiftSql->select('tbUser', '', '', "id,user_name,password,first_name,last_name,pin");

////Select specific columns from table with where clause
//$bind[':first_name'] = "Ng'ang'a";
//$bind[':user_name'] = 'admin';
//$resultArray = $swiftSql->select('tbUser', 'first_name=:first_name and user_name=:user_name', $bind, "id,password,pin");

////Define your own select query
//$sql = 'Select first_name, last_name, id, user_name, password from tbUser where pin = 1';
//$resultArray = $swiftSql->selfDefinedSelect($sql);

////Update table by passing the columns to update and the parameter to use in the where clause
//$columns = array();
//$columns['phone_number'] = '0712345678';
//$columns['id_number'] = '012345678';
//$bindUpdate = array();
//$bindUpdate[':user_name'] = 'admin';
//$result = $swiftSql->update('tbUser', $columns, 'user_name = :user_name', $bindUpdate);

////Insert into table by providing columns and values
//$columns['id'] = 'userID1';
//$columns['user_name'] = 'Admin2';
//$columns['password'] = 'password';
//$columns['pin'] = '1234';
//$columns['encryption_method'] = '1';
//$columns['first_name'] = 'Admin2';
//$columns['last_name'] = 'Admin2';
//$columns['phone_number'] = '0712345678';
//$columns['id_number'] = '012345678';
//$columns['created_on'] = date('Y-m-d H:i:s');
//$columns['active'] = 1;
//$result = $swiftSql->insert('tbUser',$columns);

////Delete a row from the table
//$delcol['id'] = 'userID1';
//$result = $swiftSql->delete('tbUser', 'id = :id', $delcol);

////Execute a stored procedure with no input or output parameters
//$result = $swiftSql->storedProcedure("justArandomSP");

////Execute a stored procedure that requires an input parameter without an output parameter
//$result = $swiftSql->storedProcedure("randomSPwithParam", "0712000025");

//Execute a stored procedure that requires an input parameter and returns an output paramater
//$result = $swiftSql->storedProcedureWithOutput("generate_serials_output", "SERIAL");

print $result;
print_r("This is a test page");