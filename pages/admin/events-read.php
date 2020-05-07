<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Check existence of interestID parameter before processing further
if(isset($_GET["eventID"]) && !empty(trim($_GET["eventID"]))){
    
    // Prepare a select statement
    $sql = "SELECT * FROM events WHERE eventID = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_eventID);
        
        // Set parameters
        $param_eventID = trim($_GET["eventID"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $eventID = $row["eventID"];
                $eventCountyID = $row["eventCountyID"];
                $eventName = $row["eventName"];
                $eventDate = $row["eventDate"];
                $eventWebsite = $row["eventWebsite"];
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

<?php $title = 'Admin | Event | View'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>View Event</h2>
                    </div>
                    <div class="form-group">
                        <label>eventID</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["eventID"] . '</b>' ; ?></p>
                    </div>
                    <div class="form-group">
                        <label>eventCountyID</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["eventCountyID"] . '</b>' ; ?></p>
                    </div>
                    <div class="form-group">
                        <label>eventName</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["eventName"] . '</b>' ; ?></p>
                    </div>
                    <div class="form-group">
                        <label>eventDate</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["eventDate"] . '</b>' ; ?></p>
                    </div>
                    <div class="form-group">
                        <label>eventWebsite</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["eventWebsite"] . '</b>' ; ?></p>
                    </div>

                    <p><a href="events-home.php" class="btn btn-info">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>