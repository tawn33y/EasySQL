## Demo
**NOTE:** *An online demo is [available on the project site](https://tawn33y.github.io/EasySQL#demo).*

### Getting Started
These instructions will get you a copy of the project demo folder up and running on your local machine for development and testing purposes.
```shell
git clone http://github.com/tawn33y/easysql/demo
cp demo /your/path
```

### Prerequisites
You need the following to install and use the project:
- **Web server** - tool for running the PHP scripts, for example, [Apache Web Server](https://httpd.apache.org/download.cgi)
- **Database server** - RDBMS database program, [MySQL](https://dev.mysql.com/downloads/installer/)
- **Web browser** - tool to preview the result, for example, [Google Chrome](https://support.google.com/chrome/answer/95346) or [Mozilla Firefox](https://www.mozilla.org/en-US/firefox/new/)
- **Text Editor** - tool for editing your code, for example, [Atom Text Editor](https://atom.io/), [Sublime Text Editor](https://www.sublimetext.com/3) or [Notepad++](https://notepad-plus-plus.org/download/v7.4.1.html)

**NOTE:** *This document does not contain steps on how to setup or configure the above tools; it is assumed that you're already familiar with this as well as the process of creating a dummy site and deploying it on a local (or online) server.*

### Installing
1. **Import the contacts table**

	- ***Using the SQL CLI***

		Open your database server and create a new database called 'contacts'.
		```sql
		mysql --user=username --password=user_password
		CREATE DATABASE contacts;
		use contacts;
		```
		Next import the contacts table
		```sql
		source /your/path/demo/assets/demo.sql
		```

	- ***Using phpMyAdmin***

		- Open your web browser
		- Go to 'http://localhost/phpmyadmin'
		- On the top left corner above the list of databases, click on the 'New' link.
		- Enter the database name as 'contacts' and click 'Create'.
		- On the top menu bar, click on the 'Import' link.
		- Click the 'Choose File' link, and select the file at 'your/path/demo/assets/demo.sql'.
		- Click 'Go'. You're all set.

2. **Update the database credentials**

	To provide valid database credentials, update the credentials in the [credentials file](assets/credentials.json) as follows:
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

3. **Test the database connection**

	Open [index.php](index,php) on your web browser by going to http://localhost/your/path/demo/index.php.

	If there is an error message, validate your database credentials and try again.

### Running the tests
Appendix I contains a list of tests which you can run.

Copy at least one statement from the tests. Open [index.php](index,php) with your text editor and update it to look as follows (from lines 18-23):
```php
<?php
try {
	// paste the statement you copied here
	// [...]

	$conn->pretty_print($result);
} catch (Exception $e) {
	$conn->pretty_print($e->getMessage());
}
?>
```
A sample updated script would look as follows:
```php
<?php
try {
	$result = $conn->set_database_type("mysql");
	$conn->pretty_print($result);
} catch (Exception $e) {
	$conn->pretty_print($e->getMessage());
}
?>
```
To view the output, open [index.php](index,php) on your web browser by going to http://localhost/your/path/demo/index.php.

Repeat the procedure for any test you want to run.

### APPENDIX I: Test Runs
```php
<?php

$conn->set_database_type("mysql");
$conn->get_database_type();
$conn->set_host_name("127.0.0.1");
$conn->get_host_name();
$conn->set_host_username("username");
$conn->get_host_username();
$conn->set_host_password("user_password");
$conn->get_host_password();
$conn->set_database("sample_db");
$conn->get_database();
$conn->set_credentials("mysql", "localhost", "username", "user_password", "sample_db");
$conn->get_credentials();
$conn->set_credentials_via_json_file("assets/credentials.json");

$conn->set_logs_file_path("assets/logs.json");
$conn->get_logs_file_path();
$conn->set_logs_enable(false);
$conn->get_logs_enable();
$conn->set_backtrace_enable(false);
$conn->get_backtrace_enable();
$conn->set_logs_minify(false);
$conn->get_logs_minify();
$conn->clear_logs();
$conn->get_logs();

$conn->open_connection();
$conn->close_connection();

$conn->select("contacts", ["first_name", "last_name"]);
$conn->select("contacts", ["COUNT(`first_name`)"], ["id" => "2"]);
$conn->select("contacts", ["phone_number"], ["first_name" => "lorem"]);
$conn->select("contacts", ["phone_number"], null, ["id" => "DESC"]);

$conn->select2("contacts", ["first_name"]);
$conn->select2("contacts", ["first_name", "last_name"], "`first_name` LIKE 'lorem' && `id` %2 = 0 ORDER BY `id` DESC");
$conn->select2("contacts", ["COUNT(`first_name`)"], "`id` %2 = 0");

$conn->insert("contacts", ["first_name" => "testing"]);
$conn->insert("contacts", ["first_name" => "testing", "last_name" => "testing", "phone_number" => "0"]);
$conn->update("contacts", ["first_name" => "lorem"], ["id" => 3]);
$conn->delete("contacts", ["id" => 3]);
$conn->alter("contacts", "ADD", ["email_address" => "VARCHAR(32) NOT NULL DEFAULT '0'"]);
$conn->alter("contacts", "DROP", "email_address");

$result = $conn->process_output($result);
?>
```
