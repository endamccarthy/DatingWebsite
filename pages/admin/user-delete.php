<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

if(isset($_POST["userID"]) && !empty($_POST["userID"])){
    
    // Prepare a delete statement
    $sql = "DELETE FROM user WHERE userID = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_userID);
        
        // Set parameters
        $param_userID = trim($_POST["userID"]);
        
        // Attempt to execute the prepared statement

        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: user-home.php");
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
    if(empty(trim($_GET["userID"]))){
        // URL doesn't contain userID parameter. Redirect to error page
        header("location: admin-error.php");
        exit();
    }
}
?>

<?php $title = 'Admin | User | Delete'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-wide">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Delete User</h2>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger" role="alert">
                            <input type="hidden" name="userID" value="<?php echo trim($_GET["userID"]); ?>"/>
                            <p>Are you sure you want to delete this User?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="user-home.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>