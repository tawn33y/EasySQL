<?php

/**
 * Created by PhpStorm.
 * User: dnyandika
 * Date: 9/12/2016
 * Time: 5:45 PM
 */

// This class shall be used to make connections to the database server and executing statements
// By extending the PDO class, PHP shall manage the database connection automatically
class db_connection extends PDO
{
    public $error;
    private $sql;
    private $bind;
    private $errorMsgFormat;
    private $errorCallbackFunction;

    //The class constructor is used to initialize the sql connection depending on the database type
    public function __construct($database_type, $host, $database, $user, $password, $port)
    {
        try {
            if ($database_type == "mysql") {
                parent::__construct("mysql:host=" . $host . ";dbname=" . $database, $user, $password);
            } elseif ($database_type == "mssql") {
                parent::__construct("sqlsrv:server=" . $host . ";Database=" . $database, $user, $password);
            } else {
                //Add the default database type to use in case it is not among the earlier defined
            }

        } catch (PDOException $ex) {
            $this->error = $ex->getMessage();
        }
    }

    //This function is the main sql statement execution function
    //All possible sql execute statements can be run by this function, with a few tweaks as well
    public function runSqlStatement($sql, $bind = "")
    {
        $this->sql = trim($sql);
        $this->bind = $this->cleanup($bind);
        $this->error = "";

        try {
            $pdostmt = $this->prepare($this->sql);
            if ($pdostmt->execute($this->bind) !== false) {
                // If the statement being executed is a select statement, then we will expect a value returned back
                // from the query.
                // If its an insert, update or delete statement then the expected response is a rowcount of affected
                // columns or a true/false response based on success of execution
                if (preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->sql)) {
                    return $pdostmt->fetchAll(PDO::FETCH_ASSOC);
                } elseif (preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $this->sql)) {
                    return $pdostmt->rowCount();
                }
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->debug();
            return $this->error;
        }
        //If there is no result of any kind, then an error must have occured
        return $this->error;
    }

    //This function executes a prepared sql statement and returns an array
    public function runSelfSelect($sql, $bind = "")
    {
        $this->sql = trim($sql);
        $this->bind = $this->cleanup($bind);
        $this->error = "";

        try {
            $pdostmt = $this->prepare($sql);
            $pdostmt->execute();
            $resultQuery = $pdostmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultQuery;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->error;
            $this->debug();
            return false;
        }
    }

    //This function checks validity of entered params, sanitizes the code and passes resulting sql to the run statement.
    public function update($table, $info, $where, $bind = "")
    {
        $fields = $this->filter($table, $info);
        $fieldSize = sizeof($fields);

        $sql = "UPDATE " . $table . " SET ";
        for ($f = 0; $f < $fieldSize; ++$f) {
            if ($f > 0)
                $sql .= ", ";
            $sql .= $fields[$f] . " = :update_" . $fields[$f];
        }
        $sql .= " WHERE " . $where . ";";
        $bind = $this->cleanup($bind);
        foreach ($fields as $field) {
            $bind[":update_$field"] = $info[$field];
        }
        if ($this->runSqlStatement($sql, $bind)) {
            return true;
        } else {
            return false;
        }
    }

    //This function forms the insert statement and passes to run statement function for execution
    public function insert($table, $columns)
    {
        $fields = $this->filter($table, $columns);
        $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
        $bind = array();
        foreach ($fields as $field) {
            $bind[":$field"] = $columns[$field];
        }
        if ($this->runSqlStatement($sql, $bind)) {
            return true;
        } else {
            return false;
        }
    }
    //This function executes a stored Procedure
    public function storedProcedure($sql, $bind = "")
    {
        $this->sql = trim($sql);
        $this->bind = $this->cleanup($bind);
        $this->error = "";

        try {
            $pdostmt = $this->prepare($sql);
            $pdostmt->execute();
            $resultQuery = $pdostmt->fetch();
            $result = $resultQuery[0];
            return $result;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->error;
            $this->debug();
            return false;
        }
    }

    // Converts the bound variables into an array
    private function cleanup($bind)
    {
        if (!is_array($bind)) {
            if (!empty($bind))
                $bind = array($bind);
            else
                $bind = array();
        }
        return $bind;
    }

    //This function prepares the sql statement for an insert or update operation
    private function filter($table, $info)
    {
        $driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver == 'sqlite') {
            $sql = "PRAGMA table_info('" . $table . "');";
            $key = "name";
        } elseif ($driver == 'mysql') {
            $sql = "DESCRIBE " . $table . ";";
            $key = "Field";
        } else {
            $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
            $key = "column_name";
        }

        if (false !== ($list = $this->runSqlStatement($sql))) {
            $fields = array();
            foreach ($list as $record)
                $fields[] = $record[$key];
            return array_values(array_intersect($fields, array_keys($info)));
        }
        return array();
    }

    // Handle all exceptions that are bound to be thrown from the database end
    private function debug()
    {
        if (!empty($this->errorCallbackFunction)) {
            $error = array("Error" => $this->error);
            if (!empty($this->sql))
                $error["SQL Statement"] = $this->sql;
            if (!empty($this->bind))
                $error["Bind Parameters"] = trim(print_r($this->bind, true));

            $backtrace = debug_backtrace();
            if (!empty($backtrace)) {
                foreach ($backtrace as $info) {
                    if ($info["file"] != __FILE__)
                        $error["Backtrace"] = $info["file"] . " at line " . $info["line"];
                }
            }

            $msg = "";
            if ($this->errorMsgFormat == "html") {
                if (!empty($error["Bind Parameters"]))
                    $error["Bind Parameters"] = "<pre>" . $error["Bind Parameters"] . "</pre>";
                $css = trim(file_get_contents(dirname(__FILE__) . "/error.css"));
                $msg .= '<style type="text/css">' . "\n" . $css . "\n</style>";
                $msg .= "\n" . '<div class="db-error">' . "\n\t<h3>SQL Error</h3>";
                foreach ($error as $key => $val)
                    $msg .= "\n\t<label>" . $key . ":</label>" . $val;
                $msg .= "\n\t</div>\n</div>";
            } elseif ($this->errorMsgFormat == "text") {
                $msg .= "SQL Error\n" . str_repeat("-", 50);
                foreach ($error as $key => $val)
                    $msg .= "\n\n$key:\n$val";
            }

            $errorMsg = $this->errorCallbackFunction;
            $errorMsg($msg);
        }
    }
}