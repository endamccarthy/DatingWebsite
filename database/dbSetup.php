<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'dating_website');

// Attempt to connect to MySQL database 
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

// Add DB_NAME to link
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
		mysqli_query($link,$query) or die('<div class="error-response sql-import-response">Problem in executing the SQL query <b>' . $query. '</b></div>');
		$query= '';		
	}
}
echo '<div class="success-response sql-import-response">SQL file imported successfully</div>';

// Close connection
mysqli_close($link);
?>


<?php $title = 'Welcome'; include("../pages/templates/top.html");?>
	<p>
			<a href="../pages/register.php" class="btn btn-warning">Go to Register Page</a>
	</p>
<?php include("../pages/templates/bottom.html");?>