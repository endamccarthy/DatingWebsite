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
if(isset($_POST["interestID"]) && !empty($_POST["interestID"])){
    // Get hidden input value
    $interestID = $_POST["interestID"];
	$interestName = $_POST["interestName"];
    
	// Validate interestName 
	if(trim($_POST["interestName"])) {
		$interestName = trim($_POST["interestName"]);
	}
   
    
	// Check input errors before inserting in database
	if(empty($countyName_err)) {
		$sql = "UPDATE interestList SET interestName=? WHERE interestID=?";
		
        if($stmt = mysqli_prepare($link, $sql)) {
			mysqli_stmt_bind_param($stmt, "si", $paramInterestName, $param_interestID);
			$paramInterestName = $interestName;
			$param_interestID = $interestID;
			// Attempt to execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				// Records updated successfully. Redirect to landing page
				header("location: interestList-home.php");
				exit();
				echo "Something went wrong. Please try again later.<br>";
			}
		}
		// Close statement
		//mysqli_stmt_close($stmt);
    }
	// Close connection
	mysqli_close($link);
	
	
} else{
    // Check existence of interestID parameter before processing further
    if(isset($_GET["interestID"]) && !empty(trim($_GET["interestID"]))){
        // Get URL parameter
        $interestID =  trim($_GET["interestID"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM interestList WHERE interestID = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_interestID);
            
            // Set parameters
            $param_interestID = $interestID;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
					$interestName = $row["interestName"];
                } else{
                    // URL doesn't contain valid interestID. Redirect to error page
                    header("location: admin-error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain interestID parameter. Redirect to error page
        header("location: admin-error.php");
        exit();
    }
}


 
?>
 
 <?php $title = 'Admin | Interest | Update'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Update Interest</h2>
                    </div>
                    <p>Please edit the Interest and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($interestName_err)) ? 'has-error' : ''; ?>">
                            <label><b>Interest Name</b></label>
                            <input type="text" name="interestName" class="form-control" value="<?php echo $interestName; ?>" required>
                        </div>
                       
						<input type="hidden" name="interestID" value="<?php echo $interestID; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="interestList-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>