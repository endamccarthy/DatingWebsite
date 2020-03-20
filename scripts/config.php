<?php

/********************************************************************/
/*
/* OPTION 1
/* CONNECTION TO HIVE SERVER DB
/*
/********************************************************************/
/*
define('DB_SERVER', 'hive.csis.ul.ie');
define('DB_USERNAME', 'group17');
define('DB_PASSWORD', 'Ut5QsH4v@={uwa3d');
define('DB_NAME', 'dbgroup17');
*/

/********************************************************************/
/*
/* OPTION 2
/* CONNECTION TO LOCAL SERVER DB (FOR TESTING DURING DEVELOPMENT)
/* (values for username and password may need to be changed or set to empty)
/*
/********************************************************************/
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'dbgroup17');

// Attempt to connect to MySQL database 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
// Check connection
if($link === false) {
  die("ERROR: Could not connect. " . mysqli_connect_error());
}

?>