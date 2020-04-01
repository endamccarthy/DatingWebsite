<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Check existence of interestID parameter before processing further
if(isset($_GET["countyID"]) && !empty(trim($_GET["countyID"]))){
    
    // Prepare a select statement
    $sql = "SELECT * FROM countylist WHERE countyID = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_countyID);
        
        // Set parameters
        $param_countyID = trim($_GET["countyID"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $countyID = $row["countyID"];
                $countyName = $row["countyName"];
            } else{
                // URL doesn't contain valid interestID parameter. Redirect to error page
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
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: admin-error.php");
    exit();
}
?>

<?php $title = 'Admin | County | View'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>View County</h2>
                    </div>
                    <div class="form-group">
                        <label>countyID</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["countyID"] . '</b>' ; ?></p>
                    </div>
                    <div class="form-group">
                        <label>County Name</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["countyName"] . '</b>'; ?></p>
                    </div>
                    <p><a href="countyList-home.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>