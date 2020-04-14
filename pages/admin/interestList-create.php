<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$interestName = "";
$interestName_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Check if countyName is empty
    if((trim($_POST["interestName"]))) {
         $interestName = "Please enter an interestName.";
	}		
		
	// Check interestlist table for interestName
	$sql = "SELECT interestName FROM interestlist WHERE interestName = '$interestName';";
	
	if($stmt = mysqli_prepare($link, $sql)) {
		if(mysqli_stmt_execute($stmt)) {
			mysqli_stmt_store_result($stmt);
			if(mysqli_stmt_num_rows($stmt) == 1) {
				$interestName_err = "This interestName already exists";
				echo $interestName_err;
			} 
			else {
				$interestName = trim($_POST["interestName"]);
			}
		} 
		else {
			echo "Oops! Something went wrong. Please try again later.";
		}
		// Close statement
		mysqli_stmt_close($stmt);
	}
	
	// Check input errors before inserting in database

	if(empty($interestName_err)) {
        $sql = "INSERT INTO interestlist (interestName) VALUES(?);";
		
		echo $sql;
		
        if($stmt = mysqli_prepare($link, $sql)) {
			mysqli_stmt_bind_param($stmt, "s", $paramInterestName);
			$paramInterestName = $interestName;
			
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records created successfully. Redirect to landing page
				header("location: interestList-home.php");
				exit();
			} else{
			    // interestName already exists
				// URL doesn't contain userID parameter. Redirect to error page
				header("location: interest-error.php");
				exit();
				echo "Something went wrong. Please try again later.<br>";
				echo "Possible Duplicate interestName: $interestName <br>";
			}
		}
		// Close statement
		//mysqli_stmt_close($stmt);
    }
	// Close connection
	mysqli_close($link);
}

?>

<?php $title = 'Admin | interestList | Create'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                      <h2>Create Interest</h2>
                    </div>
                    <p>Please enter a new Interest to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
					    <div class="form-group <?php echo (!empty($interestName_err)) ? 'has-error' : ''; ?>">
                            <label>Interest Name</label>
                            <input type="text" name="interestName" class="form-control" value="<?php echo $interestName; ?>" required>
                            <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="interestList-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>