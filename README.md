# EasySQL [![Shippable](https://img.shields.io/shippable/5444c5ecb904a4b21567b0ff.svg?style=flat-square)]() [![Github file size](https://img.shields.io/github/size/webcaetano/craft/build/craft.min.js.svg?style=flat-square)]() [![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)]()

![alt text](docs/assets/images/Logo-64.jpg "EasySQL")

**Lightweight PHP library to perform SQL operations easily and securely**

## Features
| Feature              | Description                                                                             |
| :------------------- | :-------------------------------------------------------------------------------------- |
| Negligible size      | EasySQL is less than 1KB (minified and gzipped).                                        |
| Fast and convenient  | Write only one line of code to perform simple or complex CRUD database operations.      |
| Avoid SQL Injection  | EasySQL uses prepared statements and escapes user input.                                |
| Error logging        | Track script errors easily.                                                             |
| Extra functionality  | Error minification, custom error codes, enable/disable backtrace                        |
| Error Debugging      | Easily debug your project by viewing the backtrace output in error exception object.    |
| Error Handling       | Handle errors gracefully in your project by taking advantage of the exception class.    |
| Plug & Play          | EasySQL is pretty simple to use; just include it in your script and you're good to go!  |
| Wide Support         | EasySQL supports all browsers and servers which can process PHP.                        |

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.
```shell
git clone http://github.com/tawn33y/easysql
```

## Prerequisites
You need the following to install and use the project:
- **Web server** - tool for running the PHP scripts, for example, [Apache Web Server](https://httpd.apache.org/download.cgi)
- **Database server** - RDBMS database program, [MySQL](https://dev.mysql.com/downloads/installer/)
- **Web browser** - tool to preview the result, for example, [Google Chrome](https://support.google.com/chrome/answer/95346) or [Mozilla Firefox](https://www.mozilla.org/en-US/firefox/new/)
- **Text Editor** - tool for editing your code, for example, [Atom Text Editor](https://atom.io/), [Sublime Text Editor](https://www.sublimetext.com/3) or [Notepad++](https://notepad-plus-plus.org/download/v7.4.1.html)

**NOTE:** *This document does not contain steps on how to setup or configure the above tools; it is assumed that you're already familiar with this as well as the process of creating a dummy site and deploying it on a local (or online) server.*

## Installing
To use EasySQL, add the following at the beginning of your PHP code in your working script:
```php
<?php
require_once("easysql.min.php");
?>
```

## Running the tests
To run tests, [visit the online demo](http://tawn33y.github.io/EasySQL#demo) or see the [DEMO/README.md](./demo/README.md) file for notes on how to test locally.

## Deployment
To deploy this project on a live system, copy the distribution folder to your working environment:
```shell
git clone http://github.com/tawn33y/easysql/dist
cp dist /your/path
```
Next, provide valid database credentials in the [credentials file](dist/credentials.json) as follows:
```json
{
  "database_type"   : "",
  "host_name"       : "",
  "host_username"   : "",
  "host_password"   : "",
  "database"        : ""
}
```
Finally, include EasySQL in your working scripts, and configure it:
```php
<?php
require_once("dist/easysql.min.php");

// create a new instance
$conn = new easysql();

// provide credentials
$conn->set_credentials_via_json_file("dist/credentials.json");

// (optional) enable error logging
$conn->set_logs_file_path("dist/logs.json");
$conn->set_logs_minify(true);

// your code follows here
// [...]

// destroy instance
$conn->__destruct();
unset($conn);
?>
```

## Usage
For guidelines on how to use EasySQL, see the [USAGE.md](./USAGE.md) file or [visit the project site](https://tawn33y.github.io/EasySQL/usage.html).

## Built With
- [PHP 7.0](https://) - The web framework used
- [PDO](https://) - The framework linking PHP to the Database

## Documentation
EasySQL uses an object-oriented approach highly facilitated by mixed inheritance, a combination of single and multilevel inheritance. For the classes' UML and relationship diagrams, see the [UML.pdf](./UML.pdf) file for more details. A full list of the classes is available in the [UML.md](./UML.md) file, and the [unminified source code](./dist/easysql.php) contains a description of the entire codebase.

## Contributing
EasySQL is an open source software project and we encourage developers to contribute patches and code for us to include in the main package of EasySQL. All contributions will be fully **credited** - see the [Contributing.md](./CONTRIBUTING.md) file for details on our code of conduct, and the process for submitting pull requests.

## Versioning
This project uses [semver](https:://semver) for versioning. Current version: v1.0.0

## Authors & Contributors
This version is mantained by [K Tony](https://tawn33y.github.io). Many thanks to [Smodav](https://github.com/smodav) for introducing the concept of refactoring EasySQL via an Object Oriented approach in the previous version.

## License
This project is licensed under the MIT License - see the [LICENSE.md](./LICENSE.md) file for details.
