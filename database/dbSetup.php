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

// Attempt to connect to MySQL database - General access to hive server (or localhost)
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
 
// Check connection
if($link === false){
	die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Attempt create database query execution
$sql = sprintf("CREATE DATABASE %s", DB_NAME);
if(mysqli_query($link, $sql)) {
	echo "Database created successfully";
} 
else {
	echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

// Add DB_NAME to link - Specific access to dbgroup17 DB on hive server (or localhost)
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$query = '';
$sqlScript = file('schema.sql');
foreach ($sqlScript as $line) {	
	$startWith = substr(trim($line), 0 ,2);
	$endWith = substr(trim($line), -1 ,1);
	if (empty($line) || $startWith == '/*' || $startWith == '*') {
		continue;
	}	
	$query = $query . $line;
	if ($endWith == ';') {
		mysqli_query($link,$query) or die('<div class="error-response sql-import-response">Problem in executing the SQL query <b>'.$query.'</b></div>');
		$query= '';		
	}
}
echo '<div class="success-response sql-import-response">SQL file imported successfully</div>';

// Close connection
mysqli_close($link);
?>


<?php $title = 'Welcome'; include("../pages/templates/top.html");?>
	<p>
		<a href="../pages/main/register.php" class="btn btn-warning">Go to Register Page</a>
	</p>
<?php include("../pages/templates/bottom.html");?>