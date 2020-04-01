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
$countyID; 
 
// Processing form data when form is submitted
if(isset($_POST["countyID"]) && !empty($_POST["countyID"])){
    // Get hidden input value
    $countyID = $_POST["countyID"];
    
	// Validate interestName 
	if(trim($_POST["countyName"])) {
		$countyName = trim($_POST["countyName"]);
	}
   
    // Check input errors before updating database
    if(empty($countyName_err)){
        // Prepare an update statement
        $sql = "UPDATE countylist SET countyName=? WHERE countyID=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_countyName, $param_countyID);

            // Set parameters
            $param_countyName = $countyName;
            $param_countyID = $countyID;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: countyList-home.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of interestID parameter before processing further
    if(isset($_GET["countyID"]) && !empty(trim($_GET["countyID"]))){
        // Get URL parameter
        $countyID =  trim($_GET["countyID"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM countylist WHERE countyID = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_countyID);
            
            // Set parameters
            $param_countyID = $countyID;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
					$countyName = $row["countyName"];
                } else{
                    // URL doesn't contain valid countyID. Redirect to error page
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
        // URL doesn't contain countyID parameter. Redirect to error page
        header("location: admin-error.php");
        exit();
    }
}
?>
 
 <?php $title = 'Admin | Interest | Update'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Update County</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($countyName_err)) ? 'has-error' : ''; ?>">
                            <label>County</label>
                            <input type="text" name="countyName" class="form-control" value="<?php echo $countyName; ?>" required>
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