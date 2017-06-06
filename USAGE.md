## Usage
**NOTE:** *This document is also [available online on the project site](https://github.com/tawn33y/EasySQL/blob/master/USAGE.md).*

### Functions
1. **Initializing**

  To use EasySQL, you need to include it in your working scripts. This can be done by adding the following at the beginning of your PHP code in your working script:
  ```php
  <?php
  require_once("easysql.min.php");
  ?>
  ```
  Next, create a new instance:
  ```php
  <?php
  $conn = new easysql();
  ?>
  ```

2. **Providing database credentials**

  Next, you need to provide valid database credentials for access to your database server. There are two ways of doing this:
  - ***Through an external JSON file:***

    To provide valid database credentials, update the credentials in the [credentials file](dist/credentials.json) as follows:
    ```json
    {
      "database_type"   : "",
      "host_name"       : "",
      "host_username"   : "",
      "host_password"   : "",
      "database"        : ""
    }
    ```
    A sample updated credentials file would look as follows:
    ```json
    {
      "database_type"   : "mysql",
      "host_name"       : "127.0.0.1",
      "host_username"   : "root",
      "host_password"   : "",
      "database"        : "sample_db"
    }
    ```

  - ***In the working script:***

    You can provide all the credentials in one line as follows:
    ```php
    <?php
    $conn->set_credentials("mysql", "localhost", "root", "", "sample_db");
    ?>
    ```
    To set individual properties, do the following:
    ```php
    <?php
    // set the type of database
    $conn->set_database_type("mysql");

    // set the host name
    $conn->set_host_name("127.0.0.1");

    // set the username for a user
    $conn->set_host_username("root");

    // set the user's password
    $conn->set_host_password("root");

    // set the database
    $conn->set_database("sample_db");
    ?>
    ```
    To get the set credentials, do the following:
    ```php
    <?php
    // NOTE: Remember to echo or print each line to view the result

    // get all credentials (returns array)
    $conn->get_credentials();

    // get the type of database
    $conn->get_database_type();

    // get the host name
    $conn->get_host_name();

    // get the username for a user
    $conn->get_host_username();

    // get the user's password
    $conn->get_host_password();

    // get the database
    $conn->get_database();
    ?>
    ```

3. **Opening and closing a connection to the database**

  You may need to test if the credentials are working. To do this, you need to open a connection to the database.
  ```php
  <?php
  $conn->open_connection();
  ?>
  ```
  If there is no output, then the connection is working. However, if the above throws an error, you either set the wrong credentials or you have not configured your database server well. The error causing this will be specified in the output.

  To view the result / output in a neat manner, replace the above with the following:
  ```php
  <?php
  try {
    $result = $conn->open_connection();
    $conn->pretty_print($result);
  } catch (Exception $e) {
    $conn->pretty_print($e->getMessage());
  }
  ?>
  ```
  To close the connection to the database, do the following:
  ```php
  <?php
  $conn->close_connection();
  ?>
  ```
  **NOTE:** *When performing CRUD operations such as selecting data from a table, you don't need to open or close a connection to the database; all this is done for you in the background.*

4. **CRUD operations**

  - ***Select***

    SQL EQUIVALENT: SELECT column(s) FROM table
    ```php
    <?php
    // HACK: Use this to select all rows from a table
    $conn->select("sample_db", ["column_1", "column_2"]);
    ?>
    ```

    SQL EQUIVALENT: SELECT column(s) FROM table WHERE column = value
    ```php
    <?php
    $conn->select("sample_db", ["column_1", "column_2"], ["column_3" => "value"]);
    ?>
    ```

    SQL EQUIVALENT: SELECT column(s) FROM table WHERE column = value ORDER BY column_1 ASC
    ```php
    <?php
    // NOTE: Only one value for the 3rd array is required. Do not pass in multiple values in the 3rd array.
    // HACK: You can replace ASC with DESC to select data in a descending order
    $conn->select("sample_db", ["column_1", "column_2"], ["column_4" => "value"], ["column_1" => "ASC"]);
    ?>
    ```
  - ***Advanced Select***

    SQL EQUIVALENT: SELECT column(s) FROM table WHERE column LIKE %value% && column === even number ORDER BY column ASC
    ```php
    <?php
    // HACK: Use this for complex queries such as "Right Join", "LIKE", etc.
    // HACK: You can replace ASC with DESC to select data in a descending order
    $conn->select2("sample_db", ["column_1", "column_2"], "WHERE `column_1` LIKE '%value%' && `column_2` %2 = 0 ORDER BY `column_1` ASC");
    ?>
    ```
  - ***Insert***

    SQL EQUIVALENT: INSERT row INTO table
    ```php
    <?php
    // HACK: The data returned is the id of the inserted row
    $conn->insert("sample_db", ["column_1" => "value_1", "column_2" => "value_2"]);
    ?>
    ```
  - ***Update***

    SQL EQUIVALENT: UPDATE table SET column_1 = value_1 WHERE column_2 = value_2
    ```php
    <?php
    $conn->update("sample_db", ["column_1" => "value_1"], ["column_2" => "value_2"]);
    ?>
    ```
  - ***Delete***

    SQL EQUIVALENT: DELETE row FROM table WHERE WHERE column = value
    ```php
    <?php
    // NOTE: Only one value for the array is required. Do not pass in mulHACKle values in the array.
    $conn->delete("sample_db", ["column" => "value"]);
    ?>
    ```
  - ***Alter***

    SQL EQUIVALENT: ALTER TABLE ADD column type
    ```php
    <?php
    // HACK: Examples of type include "VARCHAR(32) NOT NULL DEFAULT '0'", "INT", etc.
    $conn->alter("sample_db", "ADD", ["column" => "type"]);
    ?>
    ```
    SQL EQUIVALENT: ALTER TABLE DROP column
    ```php
    <?php
    $conn->alter("sample_db", "DROP", "column");
    ?>
    ```

5. **Output**

  An EasySQL method call might be successful or it might fail. Whichever the case, EasySQL returns a standard JSON object which contains all the necessary information to help you deduce whether or not it was successful.

  For each object returned, the *code* key contains the HTTP exit status code, while the *status* key contains information on whether the call was successful or not.

  There are 3 possible outputs:

  - ***Success***

    When an EasySQL call is successful, the EasySQL object's *data* key contains an object with the returned data.

    For example:
    ```json
    {
        "code": 200,
        "status": "success",
        "data": [
            {
              "id": 1,
              "name": "hello"
            },
            {
              "id": 2,
              "name": "world"
            }
        ]
    }
    ```

  - ***Fail***

    When an EasySQL call is rejected due to invalid data or call conditions, the EasySQL object's *error* key contains an object explaining what went wrong.

    The error key contains the following information:
    - code: an alphanumeric code corresponding to the error, if applicable.
    - message: a meaningful end-user-readable (or at least log-worthy) message, explaining what went wrong.

    For example:
    ```json
    {
        "code": 200,
        "status": "fail",
        "error": {
            "code": "ES611",
            "message": "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'column_1' in 'field list'"
        }
    }
    ```

  - ***Error***

    When an EasySQL call fails due to an error on the server, the EasySQL object's *error* key contains an object explaining what went wrong.

    The error key contains the following information:
    - code: an alphanumeric code corresponding to the error, if applicable.
    - message: a meaningful end-user-readable (or at least log-worthy) message, explaining what went wrong.

    For example:
    ```json
    {
        "code": 200,
        "status": "error",
        "error": {
            "code": "ES611",
            "message": "SQLSTATE[HY000] [1049] Unknown database 'sample_db'"
        }
    }
    ```
  **NOTE:** *There are several possible errors. See Appendix I for a table listing the various possible error codes and an explanation for each.*

6. **Processing the output**

  When the standard EasySQL object is returned from a call, you might want to convert it from JSON into a PHP array.

  This can be done by doing the following:
  ```php
  <?php
  // Example: get data from database
  $result = $conn->select("sample_db", ["column_1"], ["column_2" => "value_2"]);

  // convert from JSON to string and get the information in the data key
  $result = $conn->process_output($result);

  // display the array
  $conn->pretty_print($result);
  ?>
  ```

7. **Error Handling**

  With EasySQL, it is easy to handle gracefully any errors which might occur, and display a user-friendly error message.

  This can be done by doing the following:
  ```php
  <?php
  // Example: try getting data from database
  try {
    $result = $conn->select("sample_db", ["column_1"], ["column_2" => "value_2"]);

    // your code follows here
    // [...]
  }
  catch(Exception $e) {
    // display a user-friendly error message
    die("Sorry. There was an error in getting the data.");
  }
  ?>
  ```

8. **Testing & Debugging**

  If you're in development and an error occurs during an EasySQL call, you can easily debug to view details of what went wrong.

  This can be done by doing the following:
  ```php
  <?php
  // Example: try getting data from database
  try {
    $result = $conn->select("sample_db", ["column_1"], ["column_2" => "value_2"]);

    // your code follows here
    // [...]
  } catch (Exception $e) {
    // see why the error happened
    $conn->pretty_print($e->getMessage());
  }
  ?>
  ```

  EasySQL also provides an option of viewing the backtrace (an object containing information as to where the error happened: the script, function, etc.). By default, error backtrace is set to true.
  To enable error backtrace, do the following:
  ```php
  <?php
  // HACK: to disable error backtrace, set it to false
  $conn->set_backtrace_enable(true);
  ?>
  ```
  To view whether or not error backtrace is enabled, do the following:
  ```php
  <?php
  // NOTE: the following will return a boolean value
  $conn->get_backtrace_enable();
  ?>
  ```

9. **Error Logging**

  During a production environment, it may be necessary to have a record of whatever errors happen when they do. To do this, EasySQL provides an error logging tool which can be used as follows:

  - ***Enabling error logging***

    ```php
    <?php
    // HACK: to disable error logging, set the following to false
    // NOTE: error logging is disabled by default
    $conn->set_logs_enable(true);
    ?>
    ```

  - ***Providing a file for error logging***

    ```php
    <?php
    $conn->set_logs_file_path("/your/path/logs.json");
    ?>
    ```

  - ***Minifying logs***

    ```php
    <?php
    // HACK: to disable error logs minification, set the following to false
    $conn->set_logs_minify(true);
    ?>
    ```

  - ***Getting the set values***

    ```php
    <?php
    // check if error logging is enabled
    // NOTE: the following will return a boolean value
    $conn->get_logs_enable();

    // get the logs file path
    // NOTE: the following will return a string value
    $conn->get_logs_file_path();

    // check if error logs minification is enabled
    // NOTE: the following will return a boolean value
    $conn->get_logs_minify();
    ?>
    ```

  - ***Reading logs from log file***

    ```php
    <?php
    $conn->get_logs();
    ?>
    ```

  - ***Clearing logs from log file***

    ```php
    <?php
    $conn->clear_logs();
    ?>
    ```

10. **Destroying an instance of EasySQL**

  ```php
  <?php
  $conn->__destruct();
  unset($conn);
  ?>
  ```

### APPENDIX I: Error Codes
The following table lists various possible error codes and an explanation for each:

**NOTE:** *For DB Errors, see the message key in the error object for the PDO error code*

| TYPE           | CODE           | EXPLANATION                            |
| :------------- | :------------- | :------------------------------------- |
| DB Errors      | ES601          | Failed to open database connection     |
|                | ES611          | Failed to read from table              |
|                | ES612          | Failed to insert a new row in table    |
|                | ES613          | Failed to update a row in table        |
|                | ES614          | Failed to delete a row from table      |
|                | ES615          | Failed to alter a column in table      |
|                |                |                                        |
| Syntax Errors  | ES701          | Missing database credentials           |
|                | ES711          | Missing argument/parameter in funciton |
|                | ES712          | Expects arg to be string               |
|                | ES713          | Expects arg to be boolean              |
|                | ES714          | Expects arg to be array                |
|                | ES715          | Expects [x] number of keys in array    |
|                | ES721          | Invalid JSON format                    |
|                |                |                                        |
| File Errors    | ES801          | Files does not exist                   |
|                | ES802          | Permission denied                      |
|                | ES803          | Empty file                             |
|                | ES804          | Log file path empty                    |
|                | ES805          | Unable to write to file                |
