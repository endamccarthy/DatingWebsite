<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

if(isset($_POST["countyID"]) && !empty($_POST["countyID"])){
    
    // Prepare a delete statement
    $sql = "DELETE FROM countylist WHERE countyID = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_countyID);
        
        // Set parameters
        $param_countyID = trim($_POST["countyID"]);
        
        // Attempt to execute the prepared statement

        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: countylist-home.php");
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
    if(empty(trim($_GET["countyID"]))){
        // URL doesn't contain userID parameter. Redirect to error page
        header("location: admin-error.php");
        exit();
    }
}
?>

<?php $title = 'Admin | County | Delete'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-wide">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Delete countyList</h2>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger" role="alert">
                            <input type="hidden" name="countyID" value="<?php echo trim($_GET["countyID"]); ?>"/>
							<h6><b>Are you sure you want to delete this County?</b></h6><br>
							<input type="submit" value="Warning" class="btn btn-danger">
							<p><b>CASCADE DELETE has been setup on the Database Tables<br><br>
							Proceeding with Yes (below) will physically delete this county from ALL tables in the Database<br>
							Tables affected are:  countylist, profile, preferences and events <br><br>
							ALL county history and activity/interations will disappear from the Database<br><br>
							A Database Restore will be required to retreive a county record and any associated user history and activity</b></p><br><p>
							<p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="countyList-home.php" class="btn btn-warning">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>