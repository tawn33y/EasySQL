<?php

/*
 *--------------------------------------------------------------------------------------------------------------------------
 * EasySQL v1.0.0 (https://tawn33y.github.io/EasySQL) | (c) 2016-2017 K Tony (https://tawn33y.github.io) | License: MIT
 *--------------------------------------------------------------------------------------------------------------------------
 */


/**
 * 'files' is a trait containing methods related to files, for example, reading from files
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
trait files {

  /**
   * Reads data from a file
   *
   * @param     $path                 string specifying the file path
   * @return    string                string specifying data read from the file
   * @throws    Exception             if the file does not exist
   * @throws    Exception             if the file cannot be opened due to system permissions
   * @throws    Exception             if the file is empty
   */
  protected function read_file($path) {
    if(!file_exists($path)) {
      $this->throw_error("ES801", "File error: File does not exist");
  	}

    if(!is_readable($path)) {
      $this->throw_error("ES802", "File error: Permission denied");
    }

		// read the content of the file
		$content = file_get_contents($path);

    if(empty($content)) {
      $this->throw_error("ES803", "File error: File is empty");
    }

    // return
    return $content;
  }

  /**
   * Writes data to a file
   *
   * @param     $path                 string specifying the file path
   * @param     $content              string|int|array|boolean specifying the data to be written to the file
   * @return    boolean               returns true if the data is successfully written to the file
   * @throws    Exception             if the file does not exist
   * @throws    Exception             if the file cannot be opened due to system permissions
   */
  protected function write_to_file($path, $content) {
    if(!file_exists($path)) {
      $this->throw_error("ES801", "File error: File does not exist.");
  	}

    if(!is_writable($path)) {
      $this->throw_error("ES802", "File error: Permission denied");
    }

    // empty
    $file = fopen($path, "w");
    fwrite($file, $content);
    fclose($file);
    unset($file);

    // return
    return $this->std_output();
  }

  /**
   * Appends JSON content to an existing JSON file
   *
   * @param     $path                 string specifying the file path
   * @param     $json_content         string|int|array|boolean specifying the JSON data to be appended to the file
   * @param     $auto_id              boolean specifying whether to add an auto increment ID for each JSON object
   * @param     $minify_json          boolean specifying whether or not to write the data in a minified format
   * @return    boolean               returns true if the JSON content is successfully appended to the file
   * @throws    Exception             if the file does not exist
   * @throws    Exception             if the file cannot be opened due to system permissions
   * @throws    Exception             if the JSON content cannot be written into the file
   */
  protected function append_to_json_file($path, $json_content, $auto_id=true, $minify_json) {

    if(!file_exists($path)) {
      $this->throw_error("ES801", "File error: File does not exist");
  	}

    if(!is_writable($path)) {
      $this->throw_error("ES802", "File error: Permission denied");
    }

    // read the content of the file, and convert it temporarily to an array
    $file_content_json   = file_get_contents($path);
  	$file_content_array  = json_decode($file_content_json, true);

    // auto create and prepend id to json_content
    if($auto_id) {
      if(empty($file_content_array)) {
        $new_id       = 0;
      } else {
        $array_keys   = array_keys($file_content_array);
        $new_id       = end($array_keys)+1;
      }

      $json_content   = json_decode($json_content, true);
      $json_content   = ["id" => $new_id] + $json_content;
    }

    // append new json_content to file_content array
  	$file_content_array[] = $json_content;

    // convert array back to JSON, with the option of minifying the JSON
  	$file_content_json = $minify_json ? json_encode($file_content_array) : json_encode($file_content_array, JSON_PRETTY_PRINT);

    // write the JSON data to file
    try {
      file_put_contents($path, $file_content_json, LOCK_EX);
    }
    catch (Exception $e) {
      $this->throw_error("ES805", "File error: Writing to file failed");
    }

    // return
    return true;
  }
}


/**
 * misc is a trait containing several miscellaneous methods which can be used & reused in different classes
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
trait misc {

  /**
   * Returns the standard EasySQL JSON output, after a successful or failed method call
   *
   * @param     $data_content         array containing data. It would be null after an unsuccesful method call
   * @param     $error_content        array or object containing error details. It would be null after a succesful
   *                                  method call
   * @param     $server_error         boolean specifying if the error resulted due to a server error. It would be
   *                                  true if the error resulted from a server error, and would be false if the error
   *                                  is caused by a non-server issue, for instance, syntax error in the method call
   * @return    string                returns a JSON string containing the standard EasySQL output
   */
  protected function std_output($data_content=null, $error_content=null, $server_error=false) {
    $backtrace = debug_backtrace();

  	$output = empty($error_content)
  							?
  								[
  									"code"		=> 	http_response_code(),
  									"status"	=> 	"success",
  									"data"		=> 	$data_content
  								]
  							:
  								[
  									"code"		=>	http_response_code(),
  									"status"	=> 	($server_error ? "error" : "fail"),
  									"error"		=> 	[
  																	"code" 		=> is_array($error_content)
                                                    ? $error_content["code"]
                                                    : $error_content->getCode(),
  																	"message"	=> is_array($error_content)
                                                    ? $error_content["message"]
                                                    : $error_content->getMessage()
  																]
  								]
                ;
    if($this->get_backtrace_enable() && !empty($error_content)) {
      $output["backtrace"] = end($backtrace);
    }

    // return
  	return json_encode($output, JSON_PRETTY_PRINT);
  }

  /**
   * Creates an error exception object using a custom error code & error message
   *
   * @param     $code                 string specifying a custom error code
   * @param     $error_message        string specifying a custom error message
   * @return    void
   * @throws    Exception
   */
  protected function throw_error($code, $error_message) {
    $error_content = [
                      "code" => $code,
                      "message" => $error_message
                    ];
    throw new Exception($this->std_output(null, $error_content));
  }

  /**
   * Creates an error exception object from an already existing error object.
   * It removes all extra unnecessary PHP Exception error object keys
   *
   * @param     $error_object         object containing a PHP Exception error
   * @return    void
   * @throws    Exception
   */
  protected function throw_error_from_error_object($error_object) {
    $error_array = json_decode($error_object->getMessage(), true);

    throw new Exception(json_encode($error_array, JSON_PRETTY_PRINT));
  }

  /**
   * Decodes a standard EasySQL JSON output & throws exception if the status value !== "success"
   * You can use this function to process ouput from CRUD operations & handle errors gracefully
   *
   * @param     $json_content         JSON string containing the standard EasySQL output
   * @return    array                 array containing the data returned from a EasySQL method call
   * @throws    Exception             if the status value from the EasySQL output !== "success"
   */
  public function process_output($json_content) {
  	$json_content = json_decode($json_content, true);

  	if($json_content["status"] !== "success") {
  		throw new Exception(json_encode($json_content, JSON_PRETTY_PRINT));
  	}

  	return $json_content["data"];
  }

  /**
   * Converts a JSON string to an array
   *
   * @param     $json                 JSON string
   * @return    array                 array containing the converted JSON string
   * @throws    Exception             if the JSON string does not contain valid JSON
   */
  protected function json_to_array($json_string) {
    $json_array = json_decode($json_string, true);

    if(empty($json_array) || !is_array($json_array)) {
      $this->throw_error("ES721", "Syntax error: The file or input does not contain valid JSON");
    }

    return $json_array;
  }

  /**
   * Displays an array in a neat readable way
   *
   * @param     $array                array to be displayed
   * @return    void
   */
  //
  public function pretty_print($array) {
  	echo "<pre>";
  	print_r($array);
  	echo "</pre>";
  }
}


/**
 * args_validations is a trait which checks for certain expectations in args and ensures that they meet the requirements
 * specified. Each method call throws a custom error if validation fails.
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
trait args_validations {

  /**
   * Validates that an argument is set (is not empty)
   *
   * @param     $arg                  int|string|array|boolean specifying the argument to be validated
   * @param     $arg_position         int specifying the position of the argument in the function containing it
   *                                  for example, in method($table, $data), $data has arg_position 2
   * @param     $function             string specifying the function in which the argument exists
   *                                  for example, in method($table, $data), $function would be "method()"
   * @return    boolean               returns true if the argument is set (is not empty)
   * @throws    Exception             if the argument is not set (is empty)
   */
  public function validate_arg_isset($arg, $arg_position, $function) {
    if(empty($arg)) {
      $this->throw_error("ES711", "Syntax error: Missing argument {$arg_position} for {$function}");
    }

    return true;
  }

  /**
   * Validates that an argument is a string
   *
   * @param     $arg                  int|string|array|boolean specifying the argument to be validated
   * @param     $arg_position         int specifying the position of the argument in the function containing it
   *                                  for example, in method($table, $data), $data has arg_position 2
   * @param     $function             string specifying the function in which the argument exists
   *                                  for example, in method($table, $data), $function would be "method()"
   * @return    boolean               returns true if the argument is a string
   * @throws    Exception             if the argument is not a string
   */
  public function validate_arg_is_string($arg, $arg_position, $function) {
    if(gettype($arg) !== "string") {
      $this->throw_error("ES712", "Syntax error: Expects argument {$arg_position} for {$function} to be a string");
    }

    return true;
  }

  /**
   * Validates that an argument is boolean
   *
   * @param     $arg                  int|string|array|boolean specifying the argument to be validated
   * @param     $arg_position         int specifying the position of the argument in the function containing it
   *                                  for example, in method($table, $data), $data has arg_position 2
   * @param     $function             string specifying the function in which the argument exists
   *                                  for example, in method($table, $data), $function would be "method()"
   * @return    boolean               returns true if the argument is boolean
   * @throws    Exception             if the argument is not boolean
   */
  public function validate_arg_is_boolean($arg, $arg_position, $function) {
    if(gettype($arg) !== "boolean") {
      $this->throw_error("ES713", "Syntax error: Expects argument {$arg_position} for {$function} to be boolean");
    }

    return true;
  }

  /**
   * Validates that an argument is an array
   *
   * @param     $arg                  int|string|array|boolean specifying the argument to be validated
   * @param     $arg_position         int specifying the position of the argument in the function containing it
   *                                  for example, in method($table, $data), $data has arg_position 2
   * @param     $function             string specifying the function in which the argument exists
   *                                  for example, in method($table, $data), $function would be "method()"
   * @return    boolean               returns true if the argument is an array
   * @throws    Exception             if the argument is not an array
   */
  public function validate_arg_is_array($arg, $arg_position, $function) {
    if(!is_array($arg)) {
      $this->throw_error("ES714", "Syntax error: Expects argument {$arg_position} for {$function} to be an array");
    }

    return true;
  }

  /**
   * Validates that an argument is a string ONLY IF it has been set (is not empty)
   *
   * @param     $arg                  int|string|array|boolean specifying the argument to be validated
   * @param     $arg_position         int specifying the position of the argument in the function containing it
   *                                  for example, in method($table, $data), $data has arg_position 2
   * @param     $function             string specifying the function in which the argument exists
   *                                  for example, in method($table, $data), $function would be "method()"
   * @return    boolean               returns true if the argument is a string
   * @throws    Exception             if the argument is not a string
   */
  public function validate_arg_is_string_iff_isset($arg, $arg_position, $function) {
    if(!empty($arg)) {
      try {
        $this->validate_arg_is_string($arg, $arg_position, $function);
      }
      catch (Exception $e) {
        $this->throw_error_from_error_object($e);
      }
    }

    return true;
  }

  /**
   * Validates that an argument is a string
   *
   * @param     $arg                  int|string|array|boolean specifying the argument to be validated
   * @param     $arg_position         int specifying the position of the argument in the function containing it
   *                                  for example, in method($table, $data), $data has arg_position 2
   * @param     $function             string specifying the function in which the argument exists
   *                                  for example, in method($table, $data), $function would be "method()"
   * @return    boolean               returns true if the argument is a string
   * @throws    Exception             if the argument is not a string
   */
  public function validate_arg_is_array_iff_isset($arg, $arg_position, $function) {
    if(!empty($arg)) {
      try {
        $this->validate_arg_is_array($arg, $arg_position, $function);
      }
      catch (Exception $e) {
        $this->throw_error_from_error_object($e);
      }
    }

    return true;
  }

  /**
   * Validates that an array has the required number of keys. This is helpful in cases where you want to validate that
   * a user does not pass in many keys in an array, while you expected a certain amount (e.g. just one)
   *
   * @param     $arg                  int|string|array|boolean specifying the argument to be validated
   * @param     $arg_position         int specifying the position of the argument in the function containing it
   *                                  for example, in method($table, $data), $data has arg_position 2
   * @param     $function             string specifying the function in which the argument exists
   *                                  for example, in method($table, $data), $function would be "method()"
   * @param     $expected_num_keys    int specifying the required number of keys
   * @return    boolean               returns true if the argument is a string
   * @throws    Exception             if the argument is not a string
   */
  public function validate_arg_array_has_required_number_of_keys($arg, $arg_position, $function, $expected_num_keys) {
    if(!empty($arg) && count($arg) > $expected_num_keys) {
      $error_msg = "Syntax error: More than {$expected_num_keys} value passed in argument {$arg_position} for {$function}";
      $this->throw_error("ES715", $error_msg);
  	}

    return true;
  }
}


/**
 * args_formatting is a trait containing methods related to converting variables into proper SQL syntax
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
trait args_formatting {

  /**
   * Converts an array into a string
   *
   * Converted string may be in either of two forms:
   * (1) `key` = 'string'
   * (2) `key` string
   *
   * @param     $array                array specifying the data to be converted into a string
   * @param     $imploder             string specifying the type of imploder to be used for separating array keys & data
   * @param     $use_quotes           boolean specifying whether or not to surround the array values with quotes
   *                                  If true, the conversion uses (1) described above, else it uses (2)
   * @return    string                returns $string on successful array-to-string conversion
   */
  protected function convert_array_to_string($array, $imploder, $use_quotes=true) {
    if(!empty($array)) {
    	$temp = [];
    	foreach($array as $key => $value) {
    		$temp[] = $use_quotes ? "`{$key}` = '{$value}'" : "`{$key}` {$value}";
    	}
    	$string = implode($imploder, $temp);
    } else {
      $string = null;
    }

    return $string;
  }
}


/**
 * init is the abstract base class for all easysql contexts. It instantiates user-specific options, and inherits all traits
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
abstract class init
{
  protected $backtrace_enable = true;

  use files;
  use misc;
  use args_validations;
  use args_formatting;

  /**
   * Destroys the init object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  protected function __destruct() {
    $this->backtrace_enable = null;

    return true;
  }

  /**
   * Enables or disables the inclusion of backtrace information in error objects
   *
   * @param     $new_backtrace_enable   boolean specifying whether or not backtrace information should be included
   *                                    in error objects.
   * @return    boolean                 returns true if the option for backtrace inclusion is successfully set.
   * @throws    Exception               if validation for the arg fails
   */
  public function set_backtrace_enable($new_backtrace_enable) {

    // validate $new_backtrace_enable is a boolean
    try {
      init::validate_arg_is_boolean($new_backtrace_enable, 1, "set_backtrace_enable()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // set
    $this->backtrace_enable = $new_backtrace_enable;

    // return
    return true;
  }

  /**
   * Gets the currently set backtrace inclusion option
   *
   * @return    boolean               returns true if backtrace inclusion is enabled, or false if it's disabled.
   */
  public function get_backtrace_enable() {
    return $this->backtrace_enable;
  }
}


/**
 * credentials is an abstract child class. It instantiates all necessary database connection credentials
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
abstract class credentials extends init
{
  protected $database_type;
  protected $host_name;
  protected $host_username;
  protected $host_password;
  protected $database;

  /**
   * Destroys the credentials object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  protected function __destruct() {
    $this->database_type    = null;
    $this->host_name        = null;
    $this->host_username    = null;
    $this->host_password    = null;
    $this->database         = null;

    return true;
  }

  /**
   * Sets the type of database
   *
   * @param     $new_database_type    string specifying the type of database to be used
   * @return    boolean               returns true if the database is successfully set
   */
  public function set_database_type($new_database_type) {
    $this->database_type = $new_database_type;

    return true;
  }

  /**
   * Gets the type of database
   *
   * @return    string                returns the type of database used
   */
  public function get_database_type() {
    return $this->database_type;
  }

  /**
   * Sets the host name
   *
   * @param     $new_host_name        string specifying the type of host to be used on the database server.
   * @return    boolean               returns true if the host name is successfully set
   */
  public function set_host_name($new_host_name) {
    $this->host_name = $new_host_name;

    return true;
  }

  /**
   * Gets the host name
   *
   * @return    string                returns the host name
   */
  public function get_host_name() {
    return $this->host_name;
  }

  /**
   * Sets the user's username
   *
   * @param     $new_host_username    string specifying the username of a given user on the database server
   * @return    boolean               returns true if the username is successfully set
   */
  public function set_host_username($new_host_username) {
    $this->host_username = $new_host_username;

    return true;
  }

  /**
   * Gets the user's username
   *
   * @return    string                returns the user's username
   */
  public function get_host_username() {
    return $this->host_username;
  }

  /**
   * Sets the user's password
   *
   * @param     $new_host_password    string specifying the password of a given user on the database server
   * @return    boolean               returns true if the password is successfully set
   */
  public function set_host_password($new_host_password) {
    $this->host_password = $new_host_password;

    return true;
  }

  /**
   * Gets the user's password
   *
   * @return    string                returns the user's password
   */
  public function get_host_password() {
    return $this->host_password;
  }

  /**
   * Sets the database
   *
   * @param     $new_database         string specifying the database to be selected
   * @return    boolean               returns true if the database is successfully set
   */
  public function set_database($new_database) {
    $this->database = $new_database;

    return true;
  }

  /**
   * Gets the database
   *
   * @return    string                returns the database
   */
  public function get_database() {
    return $this->database;
  }

  /**
   * Sets the necessary credentials for database connection.
   * NOTE: All parameters are instantiated to null as a tweak to pass them as args in validation.
   *
   * @param     $new_database_type    string specifying the type of database to be used
   * @param     $new_host_name        string specifying the type of host to be used on the database server
   * @param     $new_host_username    string specifying the username of a given user on the database server
   * @param     $new_host_password    string specifying the password of a given user on the database server
   * @param     $new_database         string specifying the database to be selected
   * @return    boolean               returns true if the credentials are successfully set
   * @throws    Exception             if credentials do not pass validation
   */
  public function set_credentials($new_database_type=null, $new_host_name=null, $new_host_username=null,
    $new_host_password=null, $new_database=null) {

    // validate that all arguments meet the expected syntax
    // NOTE: if args were not instantiated to null, and a user did not pass in any of the args, PHP would throw
    //       a warning in the method call below, stating that the arg is not set.
    try {
      credentials::validate_credentials_args($new_database_type, $new_host_name, $new_host_username, $new_database);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    credentials::set_database_type($new_database_type);
    credentials::set_host_name($new_host_name);
    credentials::set_host_username($new_host_username);
    credentials::set_host_password($new_host_password);
    credentials::set_database($new_database);

    return true;
  }

  /**
   * Sets the necessary credentials for database connection through a JSON file
   *
   * @param     $path                 string specifying the path to the JSON file
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if reading the file is unsuccesful
   * @throws    Exception             if JSON could not be converted to array (is not valid JSON)
   * @throws    Exception             if credentials do not pass validation
   */
  public function set_credentials_via_json_file($path) {

    // read from file
    try {
      $credentials = init::read_file($path);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // convert the JSON credentials to array
    try {
      $credentials = init::json_to_array($credentials);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // validate that all needed credentials are in place
    try {
      credentials::validate_isset_credentials($credentials);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // set credentials
    credentials::set_credentials(
                            $credentials["database_type"],
                            $credentials["host_name"],
                            $credentials["host_username"],
                            $credentials["host_password"],
                            $credentials["database"]
                          );

    // return
    return init::std_output();
  }

  /**
   * Gets the credentials
   *
   * @return    array                 returns an array containing the credentials
   */
  public function get_credentials() {
    return [
      "database_type"   => credentials::get_database_type(),
      "host_name"       => credentials::get_host_name(),
      "host_username"   => credentials::get_host_username(),
      "host_password"   => credentials::get_host_password(),
      "database"        => credentials::get_database()
    ];
  }

  /**
   * Validates that a credentials array or object contains all necessary values. A valid credentials array should
   * contain args $database_type, $host_name, $host_username, and $database_name. Arg $host_password
   * is optional, and a user can choose to set it or not.
   *
   * @param     $credentials          array containing the credentials
   * @return    boolean               returns true if the credentials pass the validation check
   * @throws    Exception             if credentials do not pass validation
   */
  protected function validate_isset_credentials($credentials=null) {
    $isset_credentials    = true;
    $expected_credentials = ["database_type", "host_name", "host_username", "database"];
    $missing_credentials  = [];

    foreach ($expected_credentials as $key => $value) {
      if(empty($credentials)) {
      // if it's null, then $credentials has not been passed in as an arg (array).
      // Therefore get values from credentials object
        $temp = "get_{$value}";
        if(empty(credentials::$temp() )) {
          $isset_credentials = false;
          $missing_credentials[] = $value;
          // break;
        }
      } else {
      // credentials array has been passed in as an arg

        if(!array_key_exists($value, $credentials)) {
        // if array keys have not be defined as expected
          $isset_credentials = false;
          $missing_credentials[] = $value;
          // break;
        } else {
        // if array key value is empty
          if(empty($credentials[$value]) && $value !== "host_password") {
            $isset_credentials = false;
            $missing_credentials[] = $value;
            // break;
          }
        }
      }
    }

    // validate the "host_password" key exists
    if(is_array($credentials) && !array_key_exists("host_password", $credentials)) {
      $isset_credentials      = false;
      $missing_credentials[]  = "host_password";
    }

    // throw error if validation fails
    if(!$isset_credentials) {
      init::throw_error("ES701", "Syntax error: Missing credentials for " . implode(', ', $missing_credentials));
    }

    // return
    return true;
  }

  /**
   * Validates args for set_credentials() method
   *
   * The following checks have been disabled, but you can include them if you wish:
   *
   * init::validate_arg_is_string($new_host_name, 2, "set_credentials()");
   * init::validate_arg_is_string($new_host_username, 3, "set_credentials()");
   * init::validate_arg_is_string($new_database, 5, "set_credentials()");
   *
   * @param     $new_database_type    string specifying the type of database to be used
   * @param     $new_host_name        string specifying the type of host to be used on the database server
   * @param     $new_host_username    string specifying the username of a given user on the database server
   * @param     $new_database         string specifying the database to be selected
   * @return    boolean               returns true if the args pass validation
   * @throws    Exception             if $new_database_type is not set (is empty)
   * @throws    Exception             if $new_database_type is not a string
   * @throws    Exception             if $new_host_name is not set (is empty)
   * @throws    Exception             if $new_host_username is not set (is empty)
   * @throws    Exception             if $new_database is not set (is empty)
   */
  protected function validate_credentials_args($new_database_type, $new_host_name, $new_host_username, $new_database) {

    // validate $new_database_type is set (is not empty)
    try {
      init::validate_arg_isset($new_database_type, 1, "set_credentials()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // validate $new_database_type is a string
    try {
      init::validate_arg_is_string($new_database_type, 1, "set_credentials()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // validate $new_host_name is set (is not empty)
    try {
      init::validate_arg_isset($new_host_name, 2, "set_credentials()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // validate $new_host_username is set (is not empty)
    try {
      init::validate_arg_isset($new_host_username, 3, "set_credentials()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // validate $new_database is set (is not empty)
    try {
      init::validate_arg_isset($new_database, 5, "set_credentials()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // return
    return true;
  }
}


/**
 * logs is an abstract child class. It contains all methods dealing with error logging
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
abstract class logs extends credentials
{
  protected $logs_enable = false;
  protected $logs_file_path;
  protected $logs_minify = false;

  /**
   * Destroys the logs object
   *
   * @return    boolean               returns true if all class objects are successfully set to null.
   */
  protected function __destruct() {
    $this->logs_enable      = null;
    $this->logs_file_path   = null;
    $this->log_minify       = null;

    return true;
  }

  /**
   * Enables or disables error logging
   *
   * @param     $new_logs_enable      boolean specifying whether or not error logging should be enabled
   * @return    boolean               returns true if the option for error logging is successfully set
   * @throws    Exception             if validation for the arg fails
   */
  public function set_logs_enable($new_logs_enable) {

    // validate $new_logs_enable is a boolean
    try {
      init::validate_arg_is_boolean($new_logs_enable, 1, "set_logs_enable()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // set
    $this->logs_enable = $new_logs_enable;

    // return
    return true;
  }

  /**
   * Gets the currently set error logging option
   *
   * @return    boolean               returns true if error logging is enabled, or false if it's disabled
   */
  public function get_logs_enable() {
    return $this->logs_enable;
  }

  /**
   * Sets the file path for logs
   *
   * @param     $new_logs_file_path   string specifying the path to the logging file, which will be used if error
   *                                  logging is enabled
   * @return    boolean               returns true if the file path is successfully set
   */
  public function set_logs_file_path($new_logs_file_path) {
    if(!empty($new_logs_file_path)) {
      logs::set_logs_enable(true);
      $this->logs_file_path = $new_logs_file_path;
    }

    return true;
  }

  /**
   * Gets the file path for logs
   *
   * @return    string                returns the path to the logging file
   */
  public function get_logs_file_path() {
    return $this->logs_file_path;
  }

  /**
   * Enables or disables minification in error logs
   *
   * @param     $new_logs_enable      boolean specifying whether or not minification in error logs should be enabled
   * @return    boolean               returns true if the option for logs minification is successfully set
   * @throws    Exception             if validation for the arg fails
   */
  public function set_logs_minify($new_logs_minify) {

    // validate $new_logs_minify is a boolean
    try {
      init::validate_arg_is_boolean($new_logs_minify, 1, "set_logs_minify()");
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // set
    $this->logs_minify = $new_logs_minify;

    // return
    return true;
  }

  /**
   * Gets the currently set error minification option
   *
   * @return    boolean               returns true if error minification is enabled, or false if it's disabled
   */
  public function get_logs_minify() {
    return $this->logs_minify;
  }

  /**
   * Creates an error log
   *
   * @param     $error_content        JSON string specifying the error details from a failed EasySQL method call
   * @return    boolean               returns true if the error log is successfully created
   * @throws    Exception             if the log file path has not been set
   * @throws    Exception             if writing to the log file fails
   */
  protected function create_error_log($error_content) {

    // get path
    $path = logs::get_logs_file_path();

    // throw error if log file path is not set (is empty)
    if(empty($path)) {
      init::throw_error("ES804", "Syntax error: Log file path not set.");
    }

    // get boolean TRUE | FALSE from credentials class to decide whether the log should be minified or not
    $logs_minify = logs::get_logs_minify();

    // create error log from $error_content
    $log_array =  [
                    "timestamp" => time(),
                    "log"       => json_decode($error_content, true),
                  ];

    $log_json = json_encode($log_array, JSON_PRETTY_PRINT);

    // append to file
    try {
      init::append_to_json_file($path, $log_json, true, $logs_minify);
    }
    catch (Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // return
    return true;
  }

  /**
   * Reads log data from file
   *
   * @return    string                returns a JSON string containing the standard EasySQL output
   * @throws    Exception             if the log file path has not been set
   * @throws    Exception             if the log file path cannot be read from
   * @throws    Exception             if the log file contains invalid JSON
   */
  public function get_logs() {
    // get path
    $path = logs::get_logs_file_path();

    // throw error if log file has not been set
    if(empty($path)) {
      init::throw_error("ES804", "Syntax error: Log file path not set.");
    }

    // read from file
    try {
    	$logs_json = init::read_file($path);
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // convert the logs to array
    try {
    	$logs_array = init::json_to_array($logs_json);
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // return
    return init::std_output($logs_array);
  }

  /**
   * Empties / Clears a log file
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if the log file path has not been set
   * @throws    Exception             if clearing the logs fails
   */
  public function clear_logs() {
    // get path
    $path = logs::get_logs_file_path();

    // throw error if log file has not been set
    if(empty($path)) {
      init::throw_error("ES804", "Syntax error: Log file path not set.");
    }

    // clear logs
    try {
      init::write_to_file($path, null);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // return
    return init::std_output();
  }
}


/**
 * connection is an abstract child class. It opens and closes connection to a database
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
abstract class connection extends logs
{
  protected $connection_object;

  /**
   * Destroys the PDO connection object
   *
   * @return    boolean               returns true if the PDO connection object is successfully set to null
   */
  protected function __destruct() {
    $this->connection_object = null;

    return true;
  }

  /**
   * Sets the PDO connection object
   *
   * @param     $new_connection_object  PDO connection object
   * @return    boolean                 returns true if the PDO connection object is successfully set
   */
  protected function set_connection_object($new_connection_object) {
    $this->connection_object = $new_connection_object;

    return true;
  }

  /**
   * Gets the PDO connection object
   *
   * @return    string                returns the PDO connection object
   */
  protected function get_connection_object() {
    return $this->connection_object;
  }

  /**
   * Opens a connection to the database
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if credentials do not pass validation
   * @throws    Exception             if opening a database connection is unsuccesful
   */
  public function open_connection() {

    // validate that all credentials are set
    try {
      credentials::validate_isset_credentials();
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // open connection
    try {
  		$conn = new PDO(
  											credentials::get_database_type() . ": host=" . credentials::get_host_name() . ";
                        dbname=" . credentials::get_database() . ";",
  											credentials::get_host_username(),
                        credentials::get_host_password()
  										);

  		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      connection::set_connection_object($conn);
  	}
  	catch(PDOException $e) {
      init::throw_error("ES601", $e->getMessage());
  	}

    // return
    return init::std_output();
  }

  /**
   * Closes a connection to the database
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   */
  public function close_connection() {
    $connection_object = connection::get_connection_object();
    connection::__destruct(); // $connection_object = null;
    unset($connection_object);

    // return
  	return init::std_output();
  }
}


/**
 * crud is an abstract child class. It contains methods for performing CRUD SQL operations via a PDO approach.
 *
 * Two types of data, data_1 and data_2, are passed in as arrays and used in the SQL operations. The first type, data_1,
 * contains primary data, while the second data, data_2, contains extra secondary data.
 *
 * To understand this better, you need to understand which parameters are passed in for all the CRUD methods:

 * | select()       | select2()      | delete()       | update()       | insert         | alter          |
 * | :------------- | :------------- | :------------- | :------------- | :------------- | :------------- |
 * | table          | table          | table          | table          | table          | table          |
 * | [data]         | [data]         | [data]         | [data]         | [data]         | operand        |
 * | [query]        | clause         |                | [query]        |                | data || [data] |
 * | [order]        |                |                |                |                |                |
 *
 * It can be noted from the table above that each method contains two arbitrary/similar parameters, that is, table (string),
 * and data (array).
 * However some methods contain extra parameters; for instance, select() also contains query (array), and order (array).
 * To maintain an object-oriented approach and consistency throughout the methods, the extra parameters are all passed in
 * the $data_2 variable.
 *
 * To illustrate this, consider how the crud object will be created for the select() method:
 *
 *  $table  => table
 *  $data_1 => [data]
 *  $data_2 => (
 *               [query]
 *               [order]
 *             )
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
abstract class crud extends connection
{

  protected $table;
  protected $data_1;
  protected $data_2;

  /**
   * Destroys the crud object
   *
   * @return    boolean               returns true if the crud object is successfully set to null
   */
  protected function __destruct() {
    $this->table  = null;
    $this->data_1 = null;
    $this->data_2 = null;

    return true;
  }

  /**
   * Sets the table
   *
   * @param     $new_table              string specifying the table to be used in a SQL query
   * @return    boolean                 returns true if the table is successfully set
   */
  protected function set_table($new_table) {
    $this->table = $new_table;

    return true;
  }

  /**
   * Gets the table
   *
   * @return    string                returns the table used in a SQL query
   */
  protected function get_table() {
    return $this->table;
  }

  /**
   * Sets the first type of data
   *
   * @param     $new_data_1             array containing the first type of data
   * @return    boolean                 returns true if the first type of data is successfully set
   */
  protected function set_data_1($new_data_1) {
    $this->data_1 = $new_data_1;

    return true;
  }

  /**
   * Gets the first type of data
   *
   * @return    array                 returns the first type of data
   */
  protected function get_data_1() {
    return $this->data_1;
  }

   /**
   * Sets the second type of data
   *
   * @param     $new_data_2             array containing the second type of data
   * @return    boolean                 returns true if the second type of data is successfully set
   */
  protected function set_data_2($new_data_2) {
    $this->data_2 = $new_data_2;

    return true;
  }

  /**
   * Gets the second type of data
   *
   * @return    array                 returns the second type of data
   */
  protected function get_data_2() {
    return $this->data_2;
  }

  /**
   * Sets the entire CRUD object
   *
   * @param     $new_table              string specifying the table to be used in a SQL query
   * @param     $new_data_1             array containing the first type of data
   * @param     $new_data_1             array containing the second type of data
   * @return    boolean                 returns true if the CRUD object is successfully set
   */
  protected function set_crud($table, $new_data_1, $new_data_2=null) {
    crud::set_table($table);
    crud::set_data_1($new_data_1);
    crud::set_data_2($new_data_2);

    return true;
  }

  /**
   * Gets the entire CRUD object
   *
   * @return    array                 returns the entire CRUD object
   */
  protected function get_crud() {
    return [
      "table"  => crud::get_table(),
      "data_1" => crud::get_data_1(),
      "data_2" => crud::get_data_2()
    ];
  }

  /**
   * Reads data from a table
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if opening a database connection is unsuccesful
   * @throws    Exception             if reading the data is unsuccesful
   * @throws    Exception             if comitting a log entry fails
   */
  protected function _read() {
    // initiliaze an array to hold the results of the entire query
    $return_data = [];

    // open connection
    try {
      connection::open_connection();
      $conn = connection::get_connection_object();
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // query
  	try {
  		$stmt = $conn->prepare("SELECT " . crud::get_data_1() . " FROM `" . crud::get_table() . "` ". crud::get_data_2());
  		$stmt->execute();

  		// set the resulting array to associative
  		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  		foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
  			$return_data[] = $v;
  		}

      // null return
      /*
      if(empty($return_data)) {
        $output = init::std_output(null, ["code" => "0", "message" => "Null return"]);
      }
      */

      // close connection
      connection::close_connection();
  	}
  	catch(PDOException $e) {

      // create log
      if(logs::get_logs_enable()) {
        try {
        	logs::create_error_log(init::std_output(null, $e));
        }
        catch(Exception $e) {
        	init::throw_error_from_error_object($e);
        }
      }

      // close connection
      connection::close_connection();

      // throw error
      init::throw_error("ES611", $e->getMessage());
  	}

    // return
    return init::std_output($return_data);
  }

  /**
   * Creates a new row of data in a table
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if opening a database connection is unsuccesful
   * @throws    Exception             if inserting the row is unsuccesful
   * @throws    Exception             if comitting a log entry fails
   */
  protected function _create() {
    // initiliaze an array to hold the results of the entire query
    $return_data = [];

    // open connection
    try {
      connection::open_connection();
      $conn = connection::get_connection_object();
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // insert
    try {
  		// prepare statement for execution
  		$stmt = $conn->prepare(
                              "INSERT INTO `" . crud::get_table() . "`
                              (" . crud::get_data_1()['keys_formatted'] . ")
                              VALUES (" . crud::get_data_1()['params'] . ")"
                            );
  		for($i = 0; $i < count(crud::get_data_1()["keys"]); $i++) {
  			$stmt -> bindParam(crud::get_data_1()["keys"][$i], crud::get_data_1()["values"][$i]);
  		}

  		// insert a row
  		$stmt->execute();

  		// store the id of the row inserted in return data
      $return_data = ["id" => $conn->lastInsertId()];

      // close connection
      connection::close_connection();
    }
    catch(PDOException $e) {

      // create log
      if(logs::get_logs_enable()) {
        try {
        	logs::create_error_log(init::std_output(null, $e));
        }
        catch(Exception $e) {
        	init::throw_error_from_error_object($e);
        }
      }

      // close connection
      connection::close_connection();

      // throw error
      init::throw_error("ES612", $e->getMessage());
    }

    // return
    return init::std_output($return_data);
  }

  /**
   * Updates a row of data in a table
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if opening a database connection is unsuccesful
   * @throws    Exception             if updating the row is unsuccesful
   * @throws    Exception             if comitting a log entry fails
   */
  protected function _update() {

    // open connection
    try {
      connection::open_connection();
      $conn = connection::get_connection_object();
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // update
    try {
  		$sql = "UPDATE `" . crud::get_table() . "` SET " . crud::get_data_1() . " " . crud::get_data_2();
  		$stmt = $conn->prepare($sql);
  		$stmt->execute();

      // close connection
      connection::close_connection();
  	}
  	catch(PDOException $e) {

      // create log
      if(logs::get_logs_enable()) {
        try {
        	logs::create_error_log(init::std_output(null, $e));
        }
        catch(Exception $e) {
        	init::throw_error_from_error_object($e);
        }
      }

      // close connection
      connection::close_connection();

      // throw error
      init::throw_error("ES613", $e->getMessage());
  	}

    // return
    return init::std_output();
  }

  /**
   * Deletes a row of data from a table
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if opening a database connection is unsuccesful
   * @throws    Exception             if deleting the row is unsuccesful
   * @throws    Exception             if comitting a log entry fails
   */
  protected function _delete() {

    // open connection
    try {
      connection::open_connection();
      $conn = connection::get_connection_object();
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // delete
    try {
  		$sql = "DELETE FROM `" . crud::get_table() . "` WHERE " . crud::get_data_1();
  		$conn->exec($sql); // use exec() because no results are returned

      // close connection
      connection::close_connection();
  	}
  	catch(PDOException $e) {

      // create log
      if(logs::get_logs_enable()) {
        try {
        	logs::create_error_log(init::std_output(null, $e));
        }
        catch(Exception $e) {
        	init::throw_error_from_error_object($e);
        }
      }

      // close connection
      init::close_connection();

      // throw error
      init::throw_error("ES614", $e->getMessage());
  	}

    // return
    return init::std_output();
  }

  /**
   * Alter an existing table and performs columns-specific-operations, for example, inserting a new column
   *
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if opening a database connection is unsuccesful
   * @throws    Exception             if altering the table is unsuccesful
   * @throws    Exception             if comitting a log entry fails
   */
  protected function _alter() {

    // open connection
    try {
      connection::open_connection();
      $conn = connection::get_connection_object();
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // alter
    try {
  		$stmt = $conn->prepare("ALTER TABLE `" . crud::get_table() . "` " . crud::get_data_1() . " " . crud::get_data_2());
  		$stmt->execute();

      // close connection
      connection::close_connection();
  	}
  	catch(PDOException $e) {

      // create log
      if(logs::get_logs_enable()) {
        try {
        	logs::create_error_log(init::std_output(null, $e));
        }
        catch(Exception $e) {
        	init::throw_error_from_error_object($e);
        }
      }

      // close connection
      connection::close_connection();

      // throw error
      init::throw_error("ES615", $e->getMessage());
  	}

    // return
    return init::std_output();
  }
}


/**
 * easysql is an abstract child class. It contains all EasySQL method calls for CRUD operations
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
class easysql extends crud
{

  /**
   * Destroys all classes' objects
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  public function __destruct() {
    init::__destruct();
    credentials::__destruct();
    logs::__destruct();
    connection::__destruct();
    crud::__destruct();

    return true;
  }

  /**
   * Selects one or multiple rows from a certain table matching a certain criteria
   *
   * NOTE: All parameters are instantiated to null as a tweak to pass them as args in the $prepare object functions
   *
   * @param     $table                string specifying the table from which the data will be selected
   * @param     $select_data          array specifying the columns to be selected
   * @param     $select_query         array specifying how the columns should be selected
   * @param     $select_order         array specifying the order in which columns should be selected
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if $table is not set (is empty)
   * @throws    Exception             if $table is not a string
   * @throws    Exception             if $select_data is not set (is empty)
   * @throws    Exception             if $select_data is not an array
   * @throws    Exception             if $select_query is not an array
   * @throws    Exception             if $select_order is not an array
   * @throws    Exception             if $select_order array contains more than one (1) key
   * @throws    Exception             if making the DB query fails
   */
  public function select($table, $select_data=null, $select_query=null, $select_order=null) {

    // Instantiate an object which prepares data by formatting the values (passed in as args) into proper SQL syntax
    $prepare = new prepare_select();
    $prepare->set_method_name("select()");
    $prepare->set_backtrace_enable(init::get_backtrace_enable());

    // sets the table to be used for the SQL operation
    // NOTE: if $table was not instantiated as null, and a user did not pass in a value for $table, PHP would throw
    //       a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_table($table);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the select_data to be used for the SQL operation
    // NOTE: if $select_data was not instantiated as null, and a user did not pass in a value for $select_data, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_select_data($select_data);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the select_query to be used for the SQL operation
    // NOTE: if $select_query was not instantiated as null, and a user did not pass in a value for $select_query, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_select_query($select_query);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the select_order to be used for the SQL operation
    // NOTE: if $select_order was not instantiated as null, and a user did not pass in a value for $select_order, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_select_order($select_order);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // get the formatted values
    $table        = $prepare->get_table();
    $select_data  = $prepare->get_select_data();
    $select_query = $prepare->get_select_query();
    $select_order = $prepare->get_select_order();

    // destroy the $prepare object
    $prepare->__destruct();
    unset($prepare);

    // store in crud object
    crud::set_crud($table, $select_data, $select_query . " " . $select_order);

    // DB query
    try {
    	$result = crud::_read();
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // return
    return $result;
  }

  /**
   * Advanced select one or multiple rows from a certain table matching a certain criteria
   *
   * NOTE: All parameters are instantiated to null as a tweak to pass them as args in the $prepare object functions
   *
   * @param     $table                string specifying the table from which the data will be selected
   * @param     $select_data          array specifying the columns to be selected
   * @param     $select_query         array specifying how the columns should be selected
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if $table is not set (is empty)
   * @throws    Exception             if $table is not a string
   * @throws    Exception             if $select_data is not set (is empty)
   * @throws    Exception             if $select_data is not an array
   * @throws    Exception             if $select_query is not a string
   * @throws    Exception             if making the DB query fails
   */
  public function select2($table, $select_data=null, $select_query=null) {

    // Instantiate an object which prepares data by formatting the values (passed in as args) into proper SQL syntax
    $prepare = new prepare_select2();
    $prepare->set_method_name("select2()");

    // sets the table to be used for the SQL operation
    // NOTE: if $table was not instantiated as null, and a user did not pass in a value for $table, PHP would throw
    //       a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_table($table);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the select_data to be used for the SQL operation
    // NOTE: if $select_data was not instantiated as null, and a user did not pass in a value for $select_data, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_select_data($select_data);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the select_query to be used for the SQL operation
    // NOTE: if $select_query was not instantiated as null, and a user did not pass in a value for $select_query, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_select_query($select_query);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    $table        = $prepare->get_table();
    $select_data  = $prepare->get_select_data();
    $select_query = $prepare->get_select_query();

    // destroy the $prepare object
    $prepare->__destruct();
    unset($prepare);

    // store in crud object
    crud::set_crud($table, $select_data, $select_query);

    // DB query
    try {
    	$result = crud::_read();
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // return
    return $result;
  }

  /**
   * Inserts data into a certain table
   *
   * NOTE: All parameters are instantiated to null as a tweak to pass them as args in the $prepare object functions
   *
   * @param     $table                string specifying the table to be used for inserting data
   * @param     $insert_data          array specifying the rows to be inserted
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if $table is not set (is empty)
   * @throws    Exception             if $table is not a string
   * @throws    Exception             if $insert_data is not set (is empty)
   * @throws    Exception             if $insert_data is not an array
   * @throws    Exception             if making the DB query fails
   */
  public function insert($table, $insert_data=null) {

    // Instantiate an object which prepares data by formatting the values (passed in as args) into proper SQL syntax
    $prepare = new prepare_insert();
    $prepare->set_method_name("insert()");

    // sets the table to be used for the SQL operation
    // NOTE: if $table was not instantiated as null, and a user did not pass in a value for $table, PHP would throw
    //       a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_table($table);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the insert_data to be used for the SQL operation
    // NOTE: if $insert_data was not instantiated as null, and a user did not pass in a value for $insert_data, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_insert_data($insert_data);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // get the formatted values
    $table        = $prepare->get_table();
    $insert_data  = $prepare->get_insert_data();

    // destroy the $prepare object
    $prepare->__destruct();
    unset($prepare);

    // store in crud object
    crud::set_crud($table, $insert_data);

    // DB create row
    try {
      $result = crud::_create();
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // return
    return $result;
  }

  /**
   * Updates a row of data in a certain table
   *
   * NOTE: All parameters are instantiated to null as a tweak to pass them as args in the $prepare object functions
   *
   * @param     $table                string specifying the table to be used for updating data
   * @param     $update_data          array specifying the rows to be updated
   * @param     $update_query         array specifying how the rows should be updated
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if $table is not set (is empty)
   * @throws    Exception             if $table is not a string
   * @throws    Exception             if $update_data is not set (is empty)
   * @throws    Exception             if $update_data is not an array
   * @throws    Exception             if $update_query is not set (is empty)
   * @throws    Exception             if $update_query is not an array
   * @throws    Exception             if making the DB query fails
   */
  public function update($table, $update_data=null, $update_query=null) {

    // Instantiate an object which prepares data by formatting the values (passed in as args) into proper SQL syntax
    $prepare = new prepare_update();
    $prepare->set_method_name("update()");

    // sets the table to be used for the SQL operation
    // NOTE: if $table was not instantiated as null, and a user did not pass in a value for $table, PHP would throw
    //       a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_table($table);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the update_data to be used for the SQL operation
    // NOTE: if $update_data was not instantiated as null, and a user did not pass in a value for $update_data, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_update_data($update_data);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the update_query to be used for the SQL operation
    // NOTE: if $update_query was not instantiated as null, and a user did not pass in a value for $update_query, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_update_query($update_query);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // get the formatted values
    $table          = $prepare->get_table();
    $update_data    = $prepare->get_update_data();
    $update_query   = $prepare->get_update_query();

    // destroy the $prepare object
    $prepare->__destruct();
    unset($prepare);

    // store in crud object
    crud::set_crud($table, $update_data, $update_query);

    // DB update row
    try {
      $result = crud::_update();
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // return
    return $result;
  }

  /**
   * Deletes a row of data in a certain table
   *
   * NOTE: All parameters are instantiated to null as a tweak to pass them as args in the $prepare object functions
   *
   * @param     $table                string specifying the table to be used for deleting data
   * @param     $delete_data          array specifying the rows to be deleted
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if $table is not set (is empty)
   * @throws    Exception             if $table is not a string
   * @throws    Exception             if $delete_data is not set (is empty)
   * @throws    Exception             if $delete_data is not an array
   * @throws    Exception             if $delete_data array contains more than one (1) key
   * @throws    Exception             if making the DB query fails
   */
  public function delete($table, $delete_data=null) {

    // Instantiate an object which prepares data by formatting the values (passed in as args) into proper SQL syntax
    $prepare = new prepare_delete();
    $prepare->set_method_name("delete()");

    // sets the table to be used for the SQL operation
    // NOTE: if $table was not instantiated as null, and a user did not pass in a value for $table, PHP would throw
    //       a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_table($table);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the delete_data to be used for the SQL operation
    // NOTE: if $delete_data was not instantiated as null, and a user did not pass in a value for $delete_data, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_delete_data($delete_data);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // get the formatted values
    $table          = $prepare->get_table();
    $delete_data    = $prepare->get_delete_data();

    // destroy the $prepare object
    $prepare->__destruct();
    unset($prepare);

    // store in crud object
    crud::set_crud($table, $delete_data);

    // DB delete row
    try {
      $result = crud::_delete();
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // return
    return $result;
  }

  /**
   * Alters an existing table and performs columns-specific-operations, for example, adding a new column
   *
   * NOTE: All parameters are instantiated to null as a tweak to pass them as args in the $prepare object functions
   *
   * @param     $table                string specifying the table to be used for adding columns
   * @param     $alter_data           array specifying the column to be inserted
   * @param     $alter_operand        string specifying which operation to use, for example, "ADD" or "DROP"
   * @return    string                returns the standard EasySQL JSON output containing a successful method call
   * @throws    Exception             if $table is not set (is empty)
   * @throws    Exception             if $table is not a string
   * @throws    Exception             if $alter_data is not set (is empty)
   * @throws    Exception             if $alter_data is not an array
   * @throws    Exception             if $alter_data array contains more than one (1) key
   * @throws    Exception             if making the DB query fails
   */
  public function alter($table, $alter_operand, $alter_data) {

    // Instantiate an object which prepares data by formatting the values (passed in as args) into proper SQL syntax
    $prepare = new prepare_alter();
    $prepare->set_method_name("alter()");

    // sets the table to be used for the SQL operation
    // NOTE: if $table was not instantiated as null, and a user did not pass in a value for $table, PHP would throw
    //       a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_table($table);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the alter_operand to be used for the SQL operation
    // NOTE: if alter_operand was not instantiated as null, and a user did not pass in a value for $alter_data, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_alter_operand($alter_operand);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // sets the alter_data to be used for the SQL operation
    // NOTE: if $alter_data was not instantiated as null, and a user did not pass in a value for $alter_data, PHP
    //       would throw a warning in the method call below, stating that the arg has not been set
    try {
      $prepare->set_alter_data($alter_data);
    }
    catch(Exception $e) {
      init::throw_error_from_error_object($e);
    }

    // get the formatted values
    $table         = $prepare->get_table();
    $alter_operand = $prepare->get_alter_operand();
    $alter_data    = $prepare->get_alter_data();

    // destroy the $prepare object
    $prepare->__destruct();
    unset($prepare);

    // store in crud object
    crud::set_crud($table, $alter_operand, $alter_data);

    // DB add column
    try {
      $result = crud::_alter();
    }
    catch(Exception $e) {
    	init::throw_error_from_error_object($e);
    }

    // return
    return $result;
  }
}


/**
 * prepare_all_methods is the abstract base class for all prepare contexts.
 * It instantiates options such as $table and $method_name
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
abstract class prepare_all_methods extends init
{
  protected $table;
  protected $method_name;

  /**
   * Destroys the prepare_all_methods object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  protected function __destruct() {
    init::__destruct();
    $this->table            = null;
    $this->method_name      = null;

    return true;
  }

  /**
   * Sets the method name
   *
   * @param     $new_fmethod_name     string specifying the method name
   * @return    boolean               returns true if the method name is successfully set
   * @throws    Exception             if $new_method_name is not set (is empty)
   * @throws    Exception             if $new_fmethod_name is not a string
   */
  public function set_method_name($new_method_name) {

    // validate $new_method_name is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_method_name, 1, "set_method_name()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_method_name is a string
    try {
      prepare_all_methods::validate_arg_is_string($new_method_name, 1, "set_method_name()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->method_name = $new_method_name;

    // return
    return true;
  }

  /**
   * Gets the method name
   *
   * @return    string                returns the method name
   */
  public function get_method_name() {
    return $this->method_name;
  }

  /**
   * Sets the table
   *
   * @param     $new_table            string specifying the table
   * @return    boolean               returns true if $new_table is successfully set
   * @throws    Exception             if $new_table is not set (is empty)
   * @throws    Exception             if $new_table is not a string
   */
  public function set_table($new_table) {

    // validate $new_table is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_table, 1, prepare_all_methods::get_method_name());
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_table is a string
    try {
      prepare_all_methods::validate_arg_is_string($new_table, 1, prepare_all_methods::get_method_name());
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->table = $new_table;

    // return
    return true;
  }

  /**
   * Gets the table
   *
   * @return    string                returns the table
   */
  public function get_table() {
    return $this->table;
  }
}


/**
 * prepare_select is a public child class.
 * It instantiates and formats all select() args into proper SQL syntax
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
class prepare_select extends prepare_all_methods
{
  protected $select_data;
  protected $select_query;
  protected $select_order;

  /**
   * Destroys the prepare_select object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  public function __destruct() {
    prepare_all_methods::__destruct();
    $this->select_data  = null;
    $this->select_query = null;
    $this->select_order = null;

    return true;
  }

  /**
   * Sets the select data
   *
   * @param     $new_select_data      array specifying the select data
   * @return    boolean               returns true if $new_select_data is successfully set
   * @throws    Exception             if $new_select_data is not set (is empty)
   * @throws    Exception             if $new_select_data is not an array
   */
  public function set_select_data($new_select_data) {

    // validate $new_select_data is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_select_data, 2, "select()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_select_data is an array
    try {
      prepare_all_methods::validate_arg_is_array($new_select_data, 2, "select()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->select_data = $new_select_data;

    // return
    return true;
  }

  /**
   * Gets the select data
   *
   * @return    string                returns select data
   */
  public function get_select_data() {
    $select_data = $this->select_data;

    if(strpos($select_data[0], "(")) { // if(strstr($select_data[0], "(")) {
      // useful for queries such as COUNT()
      $select_data = implode(", ", $select_data);
    } else {
      $select_data = "`" . implode("`, `", $select_data) . "`";
    }

    // return
    return $select_data;
  }

  /**
   * Sets the select query
   *
   * @param     $new_select_query     array specifying the select query
   * @return    boolean               returns true if $new_select_query is successfully set
   * @throws    Exception             if $new_select_query is not an array
   */
  public function set_select_query($new_select_query) {

    // validate $new_select_query is an array
    // NOTE: $new_select_query can be null (the user has the option not to set it)
    //       therefore, check is array ONLY IF user has set it (it's not null)
    try {
      prepare_all_methods::validate_arg_is_array_iff_isset($new_select_query, 3, "select()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->select_query = $new_select_query;

    // return
    return true;
  }

  /**
   * Gets the select query
   *
   * @return    string                returns the select query
   */
  public function get_select_query() {
    $select_query = $this->select_query;

    $select_query = empty($select_query) ? null : "WHERE ". prepare_all_methods::convert_array_to_string($select_query, " && ");

    // return
    return $select_query;
  }

  /**
   * Sets the select order
   *
   * @param     $new_select_order     array specifying the select order
   * @return    boolean               returns true if $new_select_order is successfully set
   * @throws    Exception             if $new_select_order is not an array
   * @throws    Exception             if $new_select_order does not have the expected number of keys
   */
  public function set_select_order($new_select_order) {

    // validate $new_select_order is an array
    // NOTE: $new_select_order can be null (the user has the option not to set it)
    //       therefore, check is array ONLY IF user has set it (it's not null)
    try {
      prepare_all_methods::validate_arg_is_array_iff_isset($new_select_order, 4, "select()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_select_order has required number of keys
    try {
      prepare_all_methods::validate_arg_array_has_required_number_of_keys($new_select_order, 4, "select()", 1);
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->select_order = $new_select_order;

    // return
    return true;
  }

  /**
   * Gets the select order
   *
   * @return    string                returns the select order
   */
  public function get_select_order() {
    $select_order = $this->select_order;
    $select_order = empty($select_order) ? null : "ORDER BY " . prepare_all_methods::convert_array_to_string($select_order, "", false);

    // return
    return $select_order;
  }
}


/**
 * prepare_select2 is a public child class.
 * It instantiates and formats all select2() args into proper SQL syntax
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
class prepare_select2 extends prepare_select
{

  /**
   * Sets the select query
   *
   * @param     $new_select_query     string specifying the select query
   * @return    boolean               returns true if $new_select_query is successfully set
   * @throws    Exception             if $new_select_query is not a string
   */
  public function set_select_query($new_select_query) {

    // validate $new_select_query is a string
    // NOTE: $new_select_query can be null (the user has the option not to set it)
    //       therefore, check is string ONLY IF user has set it (it's not null)
    try {
      prepare_all_methods::validate_arg_is_string_iff_isset($new_select_query, 3, "select2()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->select_query = $new_select_query;

    // return
    return true;
  }

  /**
   * Gets the select query
   *
   * @return    string                returns the select query
   */
  public function get_select_query() {
    $select_query = $this->select_query;
    $select_query = empty($select_query) ? null : $select_query;

    // return
    return $select_query;
  }
}


/**
 * prepare_insert is a public child class.
 * It instantiates and formats all insert() args into proper SQL syntax
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
class prepare_insert extends prepare_all_methods
{
  protected $insert_data;

  /**
   * Destroys the prepare_insert object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  public function __destruct() {
    prepare_all_methods::__destruct();
    $this->insert_data  = null;

    return true;
  }

  /**
   * Sets the insert data
   *
   * @param     $new_insert_data      array specifying the insert data
   * @return    boolean               returns true if $new_insert_data is successfully set
   * @throws    Exception             if $new_insert_data is not set (is empty)
   * @throws    Exception             if $new_insert_data is not an array
   */
  public function set_insert_data($new_insert_data) {

    // validate $new_insert_data is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_insert_data, 2, "insert()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_insert_data is an array
    try {
      prepare_all_methods::validate_arg_is_array($new_insert_data, 2, "insert()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->insert_data = $new_insert_data;

    // return
    return true;
  }

  /**
   * Gets the insert data
   *
   * @return    array                 returns the insert data
   */
  public function get_insert_data() {
    $insert_data = $this->insert_data;

    // initiliaze a temporary array which will hold the processed $insert_data
    $_insert_data = [];

    // assign processed values to $_insert_data
  	foreach($insert_data as $key => $value) {
      $_insert_data["keys"][]             = $key;
      $_insert_data["values"][]           = $value;
  		$_insert_data["keys_formatted"][] 	= "`{$key}`";
  		$_insert_data["params"][]           = ':' . $key;
  	}

    // convert some values in the array into strings
  	$_insert_data["keys_formatted"] 	    = implode(", ", $_insert_data["keys_formatted"]);
  	$_insert_data["params"]               = implode(", ", $_insert_data["params"]);

    // return
    return $_insert_data;
  }
}


/**
 * prepare_update is a public child class.
 * It instantiates and formats all update() args into proper SQL syntax
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
class prepare_update extends prepare_all_methods
{
  protected $update_data;
  protected $update_query;

  /**
   * Destroys the prepare_update object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  public function __destruct() {
    prepare_all_methods::__destruct();
    $this->update_data    = null;
    $this->update_query   = null;

    return true;
  }

  /**
   * Sets the update data
   *
   * @param     $new_update_data      array specifying the update data
   * @return    boolean               returns true if $new_update_data is successfully set
   * @throws    Exception             if $new_update_data is not set (is empty)
   * @throws    Exception             if $new_update_data is not an array
   */
  public function set_update_data($new_update_data) {

    // validate $new_update_data is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_update_data, 2, "update()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_update_data is an array
    try {
      prepare_all_methods::validate_arg_is_array($new_update_data, 2, "update()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->update_data = $new_update_data;

    // return
    return true;
  }

  /**
   * Gets the update data
   *
   * @return    string                returns the update data
   */
  public function get_update_data() {
    $update_data = $this->update_data;
    $update_data = $this->convert_array_to_string($update_data, ", ");

    return $update_data;
  }

  /**
   * Sets the update query
   *
   * @param     $new_update_query     array specifying the update query
   * @return    boolean               returns true if $new_update_data is successfully set
   * @throws    Exception             if $new_update_query is not set (is empty)
   * @throws    Exception             if $new_update_query is not an array
   */
  public function set_update_query($new_update_query) {

    // validate $new_update_query is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_update_query, 3, "update()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_update_query is an array
    try {
      prepare_all_methods::validate_arg_is_array($new_update_query, 3, "update()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->update_query = $new_update_query;

    // return
    return true;
  }

  /**
   * Gets the update query
   *
   * @return    string                returns the update query
   */
  public function get_update_query() {
    $update_query = $this->update_query;
    $update_query = "WHERE " . $this->convert_array_to_string($update_query, " && ");

    return $update_query;
  }
}


/**
 * prepare_delete is a public child class.
 * It instantiates and formats all delete() args into proper SQL syntax
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
class prepare_delete extends prepare_all_methods
{
  protected $delete_data;

  /**
   * Destroys the prepare_delete object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  public function __destruct() {
    prepare_all_methods::__destruct();
    $this->delete_data  = null;

    return true;
  }

  /**
   * Sets the delete data
   *
   * @param     $new_delete_data      array specifying the delete data
   * @return    boolean               returns true if $new_delete_data is successfully set
   * @throws    Exception             if $new_delete_data is not set (is empty)
   * @throws    Exception             if $new_delete_data is not an array
   * @throws    Exception             if $new_delete_data does not have the expected number of keys
   */
  public function set_delete_data($new_delete_data) {

    // validate $new_delete_data is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_delete_data, 2, "delete()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_delete_data is an array
    try {
      prepare_all_methods::validate_arg_is_array($new_delete_data, 2, "delete()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $new_delete_data has required number of keys
    try {
      prepare_all_methods::validate_arg_array_has_required_number_of_keys($new_delete_data, 2, "delete()", 1);
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->delete_data = $new_delete_data;

    // return
    return true;
  }

  /**
   * Gets the delete data
   *
   * @return    string                returns the delete data
   */
  public function get_delete_data() {
    $delete_data = $this->delete_data;
    $delete_data = $this->convert_array_to_string($delete_data, ", ");

    return $delete_data;
  }
}


/**
 * prepare_alter is a public child class.
 * It instantiates and formats all alter() args into proper SQL syntax
 *
 * @author: K Tony
 * @version 1.0.0
 * @since 2017-05-11
 */
class prepare_alter extends prepare_all_methods
{
  protected $alter_data;
  protected $alter_operand;

  /**
   * Destroys the prepare_alter object
   *
   * @return    boolean               returns true if all class objects are successfully set to null
   */
  public function __destruct() {
    prepare_all_methods::__destruct();
    $this->alter_data     = null;
    $this->alter_operand  = null;

    return true;
  }

  /**
   * Sets the alter operand
   *
   * @param     $new_alter_operand    string specifying the alter operand
   * @return    boolean               returns true if $new_alter_data is successfully set
   * @throws    Exception             if $new_alter_operand is not set (is empty)
   * @throws    Exception             if $new_alter_operand is not a string
   */
  public function set_alter_operand($new_alter_operand) {

    // validate $new_alter_operand is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_alter_operand, 2, "alter()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // validate $alter_operand is a string
    try {
      prepare_all_methods::validate_arg_is_string($new_alter_operand, 2, "alter()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    // set
    $this->alter_operand = $new_alter_operand;

    // return
    return true;
  }

  /**
   * Gets the alter operand
   *
   * @return    string                returns the alter operand
   */
  public function get_alter_operand() {
    $alter_operand = $this->alter_operand;

    return $alter_operand;
  }

  /**
   * Sets the alter data
   *
   * @param     $new_alter_data       array specifying the alter data
   * @return    boolean               returns true if $new_alter_data is successfully set
   * @throws    Exception             if $new_alter_data is not set (is empty)
   * @throws    Exception             if $new_alter_data is not an array
   * @throws    Exception             if $new_alter_data does not have the expected number of keys
   */
  public function set_alter_data($new_alter_data) {

    // validate $new_alter_data is set (is not empty)
    try {
      prepare_all_methods::validate_arg_isset($new_alter_data, 3, "alter()");
    }
    catch(Exception $e) {
      prepare_all_methods::throw_error_from_error_object($e);
    }

    if(is_array($new_alter_data)) {
      // validate $new_alter_data is an array
      try {
        prepare_all_methods::validate_arg_is_array($new_alter_data, 3, "alter()");
      }
      catch(Exception $e) {
        prepare_all_methods::throw_error_from_error_object($e);
      }

      // validate $new_alter_data has required number of keys
      try {
        prepare_all_methods::validate_arg_array_has_required_number_of_keys($new_alter_data, 3, "alter()", 1);
      }
      catch(Exception $e) {
        prepare_all_methods::throw_error_from_error_object($e);
      }
    } else {
      // validate $new_alter_data is a string
      try {
        prepare_all_methods::validate_arg_is_string($new_alter_data, 3, "alter()");
      }
      catch(Exception $e) {
        prepare_all_methods::throw_error_from_error_object($e);
      }
    }

    // set
    $this->alter_data = $new_alter_data;

    // return
    return true;
  }

  /**
   * Gets the alter data
   *
   * @return    string                returns the alter data
   */
  public function get_alter_data() {
    $alter_data = $this->alter_data;
    $alter_data = !is_array($alter_data) ? $alter_data : $this->convert_array_to_string($alter_data, "", false);

    return $alter_data;
  }
}

?>
