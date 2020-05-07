<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Check existence of interestID parameter before processing further
if(isset($_GET["interestID"]) && !empty(trim($_GET["interestID"]))){
    
    // Prepare a select statement
    $sql = "SELECT * FROM interestList WHERE interestID = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_interestID);
        
        // Set parameters
        $param_interestID = trim($_GET["interestID"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $interestID = $row["interestID"];
                $interestName = $row["interestName"];
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

<?php $title = 'Admin | User | View'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>View Interest</h2>
                    </div>
                    <div class="form-group">
                        <label>interestID</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["interestID"] . '</b>' ; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Interest Name</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["interestName"] . '</b>'; ?></p>
                    </div>
                    <p><a href="interestList-home.php" class="btn btn-info">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>