<h1>Swift SQL</h1>
**A lightweight PHP library for performing swift SQL operations**

*Created by K Tony (Twitter: @tawn33y)*

<h3>What is Swift SQL?</h3>
Swift SQL is a library created with the objective of reducing the amount of code one has to write in order to connect to and perform operations on a database e.g. to select data from a database in a specific format or update the data in a database table.

<h3>Main Features</h3>
  - The library uses the PDO approach and an object-oriented approach in the PHP Language.
  - Security is a major concern, and thus major steps are taken to achieve this;
      e.g. use of Prepared Statements, and escaping data before each database operation is executed.
  - Note that in functions which return data from the database, the data is returned in a 2D array. Do a loop to get individual data (examples are given).
      e.g. in 'select()' && 'select2()' functions.

<h3>Getting Started - Setting up a sample environment</h3>
   - To use this library, you need a sample database. An example has been provided in the root directory (sample_db.sql)
   - Simply create a new database in your server, and import the file 'sample_db.sql'. Note that for my environment, am using PHP Version: 7.0.8
   - The 'core' folder contains a database connection file, and a file with functions for executing swift database functions.
   - Update the credentials in the 'db_connect.php' to match the ones in your local server so as to allow a successful database connection.
   - The 'core' folder contains two versions of the main library, 'swift_sql.php' - use the minimized version for production and the latter for development purposes. 

<h3>How To</h3>
   - In the root folder, the file 'index.php' contains sample code that illustrate the using of this library.
   - To illustrate a function's usage, simply uncomment the lines of code which follow the line commented out as /* .. */
       - For example, simply uncoment the second & third lines below (the lines commented out with a '//')

      	 -  /* SELECT column_names FROM table_name */
      	 -  // $query = select("hello_world", ['id', 'name', 'random'], []);
     	 -  // print_results($query);

   - To reuse the code, all you need is a copy of the main library 'swift_sql.php' and the connection file.

<h3>Help | Contact</h3>
  - If you have any questions regarding this library or how to use it, get in touch with me via Twitter @tawn33y.
