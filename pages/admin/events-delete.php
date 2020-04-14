<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

if(isset($_POST["eventID"]) && !empty($_POST["eventID"])){
    
    // Prepare a delete statement
    $sql = "DELETE FROM events WHERE eventID = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_eventID);
        
        // Set parameters
        $param_eventID = trim($_POST["eventID"]);
        
        // Attempt to execute the prepared statement

        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: events-home.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter
    if(empty(trim($_GET["eventID"]))){
        // URL doesn't contain userID parameter. Redirect to error page
        header("location: admin-error.php");
        exit();
    }
}
?>

<?php $title = 'Admin | Event | Delete'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-wide">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Delete Event</h2>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger" role="alert">
                            <input type="hidden" name="eventID" value="<?php echo trim($_GET["eventID"]); ?>"/>
                            <p>Are you sure you want to delete this Event?</p><br>
							<input type="submit" value="Warning" class="btn btn-danger">
							<p><b>CASCADE DELETE has been setup on the Database Tables<br><br>
							Proceeding with Yes (below) will physically delete this event from ALL tables in the Database<br>
							Table(s) affected are:  events<br><br>
							ALL event history and activity/interations will disappear from the Database<br><br>
							A Database Restore will be required to retreive an event record and any associated history and activity</b></p><br>
							<p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="events-home.php" class="btn btn-warning">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>