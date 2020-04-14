<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$countyName = "";
$countyName_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Check if countyName is empty
    if((trim($_POST["countyName"]))) {
         $countyName = "Please enter a countyName.";
	}		
		
	// Check countyList table for countyName
	$sql = "SELECT countyName FROM countylist WHERE countyName = '$countyName';";
	
	//echo $sql;
		
	if($stmt = mysqli_prepare($link, $sql)) {
		if(mysqli_stmt_execute($stmt)) {
			mysqli_stmt_store_result($stmt);
			if(mysqli_stmt_num_rows($stmt) == 1) {
				$countyName_err = "This countyName already exists";
				echo $countyName_err;
			} 
			else {
				$countyName = trim($_POST["countyName"]);
			}
		} 
		else {
			echo "Oops! Something went wrong. Please try again later.";
		}
		// Close statement
		mysqli_stmt_close($stmt);
	}
	
	// Check input errors before inserting in database
	if(empty($countyName_err)) {
        $sql = "INSERT INTO countylist (countyName) VALUES(?);";
		
        if($stmt = mysqli_prepare($link, $sql)) {
			mysqli_stmt_bind_param($stmt, "s", $paramCountyName);
			$paramCountyName = $countyName;
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records created successfully. Redirect to landing page
				header("location: countyList-home.php");
				exit();
			} else{
				// countyName already exists
				// URL doesn't contain userID parameter. Redirect to error page
				header("location: county-error.php");
				exit();
				echo "Something went wrong. Please try again later.<br>";
				echo "Possible Duplicate countyName: $countyName <br>";
			}
		}
		// Close statement
		//mysqli_stmt_close($stmt);
    }
	// Close connection
	mysqli_close($link);
}	
?>
 
<?php $title = 'Admin | County | Create'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Create County</h2>
                    </div>
                    <p>Please fill this form and submit to add a County record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
					              <div class="form-group <?php echo (!empty($countyName_err)) ? 'has-error' : ''; ?>">
                            <label>County Name</label>
                            <input type="text" name="countyName" class="form-control" value="<?php echo $countyName; ?>" required>
                            <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="countyList-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>
