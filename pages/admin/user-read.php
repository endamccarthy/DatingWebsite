<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Check existence of id parameter before processing further
if(isset($_GET["userID"]) && !empty(trim($_GET["userID"]))){
    
    // Prepare a select statement
    $sql = "SELECT * FROM user WHERE userID = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_userID);
        
        // Set parameters
        $param_userID = trim($_GET["userID"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $userID = $row["userID"];
                $email = $row["email"];
                //$password = $row["password"];
				$firstName = $row["firstName"];
                $lastName = $row["lastName"];
				$dateJoined = $row["dateJoined"];
                $accessLevel = $row["accessLevel"];
				$status = $row["status"];
                $notificiations = $row["notifications"];
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
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
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>View User</h2>
                    </div>
                    <div class="form-group">
                        <label>UserID</label>
                        <p class="form-control-static"><?php echo '<b>' . $row["userID"] . '</b>' ; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["email"] . '</b>'; ?></p>
                    </div>
                    <!-- <div class="form-group">
                        <label>Password</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["password"] . '</b>'; ?></p>
                     </div> -->
					<div class="form-group">
                        <label>First Name</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["firstName"] . '</b>'; ?></p>
                    </div>
					<div class="form-group">
                        <label>Last Name</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["lastName"] . '</b>'; ?></p>
                    </div>
					<div class="form-group">
                        <label>Date Joined</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["dateJoined"] . '</b>'; ?></p>
                    </div>
					<div class="form-group">
                        <label>Level</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["accessLevel"] . '</b>'; ?></p>
                    </div>
					<div class="form-group">
                        <label>Status</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["status"] . '</b>'; ?></p>
                    </div>
					<div class="form-group">
                        <label>Notifications</label>
                        <p class="form-control-static"><?php echo '<b>' .  $row["notifications"] . '</b>'; ?></p>
                    </div>
                    <p><a href="user-home.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>