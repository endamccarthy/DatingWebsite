<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: login.php");
  exit;
}
 
// Include config file
require_once "../scripts/config.php";
 
// Define variables and initialize with empty values
$newPassword = $confirmPassword = "";
$newPasswordErr = $confirmPasswordErr = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
  // Validate new password
  if(empty(trim($_POST["newPassword"]))) {
    $newPasswordErr = "Please enter the new password.";     
  } 
  elseif(strlen(trim($_POST["newPassword"])) < 6) {
    $newPasswordErr = "Password must have at least 6 characters.";
  } 
  else {
    $newPassword = trim($_POST["newPassword"]);
  }
  
  // Validate confirm password
  if(empty(trim($_POST["confirmPassword"]))) {
    $confirmPasswordErr = "Please confirm the password.";
  } 
  else {
    $confirmPassword = trim($_POST["confirmPassword"]);
    if(empty($newPasswordErr) && ($newPassword != $confirmPassword)) {
      $confirmPasswordErr = "Password did not match.";
    }
  }

  // Check input errors before updating the database
  if(empty($newPasswordErr) && empty($confirmPasswordErr)) {
    // Prepare an update statement
    $sql = "UPDATE user SET password = ? WHERE userID = ?";
    if($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "si", $paramPassword, $paramUserID);
      // Set parameters
      $paramPassword = password_hash($newPassword, PASSWORD_DEFAULT);
      $paramUserID = $_SESSION["userID"];
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)) {
        // Password updated successfully. Destroy the session, and redirect to login page
        session_destroy();
        header("location: login.php");
        exit();
      } 
      else {
        echo "Oops! Something went wrong. Please try again later.";
      }
      // Close statement
      mysqli_stmt_close($stmt);
    }
  }
  // Close connection
  mysqli_close($link);
}
?>
 
<?php $title = 'Reset Password'; include("templates/top.html");?>
  <div class="wrapper">
    <h2>Reset Password</h2>
    <p>Please fill out this form to reset your password.</p>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="newPassword" class="form-control <?php echo (!empty($newPasswordErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $newPassword; ?>">
        <span class="invalid-feedback"><?php echo $newPasswordErr; ?></span>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirmPassword" class="form-control <?php echo (!empty($confirmPasswordErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmPassword; ?>">
        <span class="invalid-feedback"><?php echo $confirmPasswordErr; ?></span>
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <a class="btn btn-link" href="welcome.php">Cancel</a>
      </div>
    </form>

  </div>    
<?php include("templates/bottom.html");?>