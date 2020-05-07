<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$userID = $_GET["userID"];

$user = getUser($link, $userID);
$firstName = $user['firstName'];
$lastName = $user['lastName'];
$email = $user['email'];
$status = $user['status'];
$accessLevel = $user['accessLevel'];

$emailChange_err =  "";
 
// Processing form data when form is submitted

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "";
    // Check if firstName was changed
    if($_POST["firstName"] != $firstName) {
      $firstName = $_POST["firstName"];
      $sql .= "UPDATE user SET firstName = '$firstName' WHERE userID = $userID;";
    }
    
    // Check if lastName was changed
    if($_POST["lastName"] != $lastName) {
        $lastName = $_POST["lastName"];
        $sql .= "UPDATE user SET lastName = '$lastName' WHERE userID = $userID;";
    }

    // Check if email was changed.  Also check user Table to prevent a duplicate being added
    if(trim($_POST["email"]) != $email) {
        // Check user table if the updated email already exists on user table
        $sqlTemp = "SELECT userID FROM user WHERE email = ?;";
        if($stmt = mysqli_prepare($link, $sqlTemp)) {
            mysqli_stmt_bind_param($stmt, "s", $paramEmail);
            $paramEmail = trim($_POST["email"]);
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    $emailChange_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                    $sql .= "UPDATE user SET email = '$email' WHERE userID = $userID;";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Check if status was changed
    if($_POST["status"] != $status) {
        if($_POST["status"] != "") {
            $status = $_POST["status"];
            $sql .= "UPDATE user SET status = '$status' WHERE userID = $userID;";
        }
    }

    // Check if accessLevel was changed
    if($_POST["accessLevel"] != $accessLevel) {
        if($_POST["accessLevel"] != "") {
            $accessLevel = $_POST["accessLevel"];
            $sql .= "UPDATE user SET accessLevel = '$accessLevel' WHERE userID = $userID;";
        }
    }

  // Execute multi query sql statement
  if($emailChange_err == "") {
    if(!mysqli_multi_query($link, $sql)) {
        echo "Something went wrong. Please try again later.";
    }
  }
  header("location: ../admin/user-home.php");
}
// Close connection
mysqli_close($link);
?>
 
 <?php $title = 'Admin | User | Update'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Update User</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group">
                            <label><b>First Name</b></label>
                            <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>" required>
                        </div>
                        
						<div class="form-group">
                            <label><b>Last Name</b></label>
                            <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>" required>
                        </div>
 						<div class="form-group">
							<label><b>Email</b></label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($emailChange_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
							<span class="invalid-feedback"><?php echo $emailChange_err; ?></span>
                        </div>

                        <div class="mb-4 form-row">
                            <div class="col-md-3">
                                <label class="control-label"><b>Status: </b></label>
                            </div>
                        <div class="col-md-2 custom-control custom-radio custom-control-inline">
                             <input type="radio" name="status" class="form-control custom-control-input" id="statusActive" value="active" <?php echo ($status == 'active') ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="statusActive">Active</label>
                        </div>
                        <div class="col-md-2 custom-control custom-radio custom-control-inline">
                            <input type="radio" name="status" class="form-control custom-control-input" id="statusBanned" value="banned" <?php echo ($status == 'banned') ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="statusBanned">Banned</label>
                        </div>
                        <div class="col-md-2 custom-control custom-radio custom-control-inline">
                            <input type="radio" name="status" class="form-control custom-control-input" id="statusSuspended" value="suspended" <?php echo ($status == 'suspended') ? 'checked' : ''; ?>>
                             <label class="custom-control-label" for="statusSuspended">Suspended</label>
                        </div>
						</div>

						<div class="mb-4 form-row">
						<div class="col-md-3">
							<label class="control-label"><b>Access </b></label><br>
                            <label class="control-label"><b>Level: </b></label>
						</div>
						<div class="col-md-2 custom-control custom-radio custom-control-inline">
							<input type="radio" name="accessLevel" class="form-control custom-control-input" id="accessLevelRegular" value="regular" <?php echo ($accessLevel == 'regular') ? 'checked' : ''; ?>>
							<label class="custom-control-label" for="accessLevelRegular">Regular</label>
						</div>
						<div class="col-md-2 custom-control custom-radio custom-control-inline">
							<input type="radio" name="accessLevel" class="form-control custom-control-input" id="accessLevelPremium" value="premium" <?php echo ($accessLevel == 'premium') ? 'checked' : ''; ?>>
							<label class="custom-control-label" for="accessLevelPremium">Premium</label>
						</div>
						<div class="col-md-2 custom-control custom-radio custom-control-inline">
							<input type="radio" name="accessLevel" class="form-control custom-control-input" id="accessLevelAdmin" value="admin" <?php echo ($accessLevel == 'admin') ? 'checked' : ''; ?>>
								<label class="custom-control-label" for="accessLevelAdmin">Admin</label>
							</div>
						</div>

						<input type="hidden" name="userID" value="<?php echo $userID; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="user-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>