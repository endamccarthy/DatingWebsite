<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
  header("location: welcome.php");
  exit;
}
 
// Include config file
require_once "../scripts/config.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$emailErr = $passwordErr = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
  // Check if email is empty
  if(empty(trim($_POST["email"]))) {
    $emailErr = "Please enter email.";
  } 
  else {
    $email = trim($_POST["email"]);
  }

  // Check if password is empty
  if(empty(trim($_POST["password"]))) {
    $passwordErr = "Please enter your password.";
  } 
  else {
    $password = trim($_POST["password"]);
  }
  
  // Validate credentials
  if(empty($emailErr) && empty($passwordErr)) {
    // Prepare a select statement
    $sql = "SELECT userID, email, password FROM user WHERE email = '$email'";
    if($stmt = mysqli_prepare($link, $sql)) {
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);
        // Check if email exists, if yes then verify password
        if(mysqli_stmt_num_rows($stmt) == 1) {                    
          // Bind result variables
          mysqli_stmt_bind_result($stmt, $userID, $email, $hashedPassword);
          if(mysqli_stmt_fetch($stmt)) {
            if(password_verify($password, $hashedPassword)) {
              // Password is correct, so start a new session
              session_start();
              // Store data in session variables
              $_SESSION["loggedIn"] = true;
              $_SESSION["userID"] = $userID;
              $_SESSION["email"] = $email;                            
              // Redirect user to welcome page
              header("location: welcome.php");
            } 
            else {
              // Display an error message if password is not valid
              $passwordErr = "The password you entered was not valid.";
            }
          }
        } 
        else {
          // Display an error message if email doesn't exist
          $emailErr = "No account found with that email.";
        }
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
 
<?php $title = 'Login'; include("templates/top.html");?>
  <div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control <?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
        <span class="invalid-feedback"><?php echo $emailErr; ?></span>
      </div>   
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
        <span class="invalid-feedback"><?php echo $passwordErr; ?></span>
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Login">
      </div>
      <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
    </form>

  </div>    
<?php include("templates/bottom.html");?>