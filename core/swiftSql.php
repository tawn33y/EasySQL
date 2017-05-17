<?php

/**
 * Created by PhpStorm.
 * User: dnyandika
 * Date: 9/12/2016
 * Time: 6:37 PM
 */
//This class contains the code logic to format the passed statements.
class SwiftSql
{
    public $logDir;
    public $logsFile;

    function SwiftSql()
    {
        $config = parse_ini_file('db_config.ini', true);
        $this->logDir = $config['logDir'];
        $this->logsFile = $config['logsFile'];
        $this->database_type = strtolower($config['database_type']);
        $this->host = $config['host'];
        $this->database = $config['database'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->port = $config['port'];

        // Initialize a connection to the database
        $this->mySwiftDB = new db_connection($this->database_type, $this->host, $this->database, $this->user, $this->password, $this->port);

        return true;
    }

    // This function allows writing log error messages to a text file.
    // The log is written to the path directed in the config.ini file
    public function writeToLog($type, $string)
    {
        $date = date("Y-m-d H:i:s");
        if ($fo = fopen($this->logsFile, 'ab')) {
            fwrite($fo, "$date - [ $type ] " . $_SERVER['PHP_SELF'] . " | $string \n");
            fclose($fo);
        } else {
            //Print to screen and exit if log cannot be written to life
            var_dump("$date - [ $type ] " . $_SERVER['PHP_SELF'] . " | $string \n");
            exit();
        }
    }

    // This is a simple sql statement
    // Returns an array if there are values found, or false if none was retrieved
    public function select($table, $where = "", $bind = "", $fields = "*")
    {
        $sql = "SELECT " . $fields;
        if (!empty($table)) {
            $sql .= " FROM " . $table;
        }
        if (!empty($where)) {
            $sql .= " WHERE " . $where;
        }
        $sql .= ";";
        return $this->mySwiftDB->runSqlStatement($sql, $bind);
    }

    //This allows the user to define their own select query and run it.
    //Sometimes a users select query may be complex and is best defined by themselves
    public function selfDefinedSelect($sql)
    {
        return $this->mySwiftDB->runSelfSelect($sql);
    }

    //This function conveys vaues received to the db_onnection class for execution
    //A result of true/false is returned depending on the success of execution.
    public function update($table, $info, $where, $bind = "")
    {
        return $this->mySwiftDB->update($table, $info, $where, $bind);
    }

    //This function passes received values to the db_connection class for execution
    public function insert($table, $columns)
    {
        return $this->mySwiftDB->insert($table, $columns);
    }

    //Delete a specified row from the table
    public function delete($table, $where, $bind = "")
    {
        $sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
        return $this->mySwiftDB->runSqlStatement($sql, $bind);
    }

    //Call a stored procedure that expects input parameter(s) without output parameter
    function storedProcedure($spName, $spParams = null)
    {
        //Remove leading and trailing spaces
        $spName = trim($spName);
        $spParams = trim($spParams);

        //Check if the [$spParams] variable is an array or NOT
        if (is_array($spParams)) {
            //If it is an array, implode the array elements so as to coma separate them
            $spParams = implode(',', $spParams);
            $sql = 'EXEC ' . $spName . " '" . $spParams . "' ";
        } else {
            //Check if the variable is set and is NOT NULL
            if (isset($spParams) && $spParams != '') {
                //This means that variable is a single variable and NOT an array. Hence, return it AS IS.
                $sql = 'EXEC ' . $spName . " '" . $spParams . "' ";
            } else {
                //Means the variable $spParams is NULL, meaning there are NO params for the SP
                $sql = 'EXEC ' . $spName;
            }
        }
        return $this->mySwiftDB->storedProcedure($sql);
    }

    //Called a stored procedure that expects input parameter(s) and returns an output parameter
    function storedProcedureWithOutput($spName, $spParams = null)
    {
        //Remove leading and trailing spaces
        $spName = trim($spName);
        $spParams = trim($spParams);

        //Check if the [$spParams] variable is an array or NOT
        if (is_array($spParams)) {
            //If it is an array, implode the array elements so as to coma separate them
            $spParams = implode(',', $spParams);
            $sql = 'declare @csv as varchar(50) EXEC ' . $spName . " '" . $spParams . "', @csv output " . ' 
	                select @csv';
        } else {
            //Check if the variable is set and is NOT NULL
            if (isset($spParams) && $spParams != '') {
                //This means that variable is a single variable and NOT an array. Hence, return it AS IS.
                $sql = 'declare @csv as varchar(50) EXEC ' . $spName . " '" . $spParams . "', @csv output  " . ' 
	                select @csv';
            } else {
                //Means the variable $spParams is NULL, meaning there are NO params for the SP
                $sql = 'declare @csv as varchar(50) EXEC ' . $spName . ' select @csv';
            }
        }
        return $this->mySwiftDB->storedProcedure($sql);
    }
}