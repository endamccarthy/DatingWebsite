<?php
// Include config file
require_once "../scripts/config.php";
 
// Define variables and initialize with empty values
$firstName = $lastName = $email = $password = $confirmPassword = $tAndC = "";
$emailErr = $passwordErr = $confirmPasswordErr = $tAndCErr = $over18Err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
  // Validate first name
  if(trim($_POST["firstName"])) {
    $firstName = trim($_POST["firstName"]);
  }

  // Validate last name
  if(trim($_POST["lastName"])) {
    $lastName = trim($_POST["lastName"]);
  }

  // Validate email
  if(empty(trim($_POST["email"]))) {
    $emailErr = "Please enter a email.";
  }
  else {
    // Prepare a select statement
    $sql = "SELECT userID FROM user WHERE email = '$email'";
    if($stmt = mysqli_prepare($link, $sql)) {
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)) {
        /* store result */
        mysqli_stmt_store_result($stmt);
        if(mysqli_stmt_num_rows($stmt) == 1) {
          $emailErr = "This email is already taken.";
        } 
        else {
          $email = trim($_POST["email"]);
        }
      } 
      else {
        echo "Oops! Something went wrong. Please try again later.";
      }
      // Close statement
      mysqli_stmt_close($stmt);
    }
  }
  
  // Validate password
  if(empty(trim($_POST["password"]))) {
    $passwordErr = "Please enter a password.";     
  } 
  elseif(strlen(trim($_POST["password"])) < 6) {
    $passwordErr = "Password must have at least 6 characters.";
  } 
  else {
    $password = trim($_POST["password"]);
  }
  
  // Validate confirm password
  if(empty(trim($_POST["confirmPassword"]))) {
    $confirmPasswordErr = "Please confirm password.";     
  } 
  else {
    $confirmPassword = trim($_POST["confirmPassword"]);
    if(empty($passwordErr) && ($password != $confirmPassword)) {
      $confirmPasswordErr = "Password did not match.";
    }
  }
  
  // Validate terms and conditions 
  if(!isset($_POST["tAndC"])) {
    $tAndCErr = "You must agree before submitting.";
  }

  // Validate over 18 
  if(!isset($_POST["over18"])) {
    $over18Err = "You must be over 18 to register";
  }

  // Check input errors before inserting in database
  if(empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr) && empty($tAndCErr) && empty($over18Err)) {
    // Prepare an insert statement
    $sql = "INSERT INTO user (firstName, lastName, email, password) VALUES ('$firstName', '$lastName', '$email', ?);";
    if($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $paramPassword);
      // Set parameters
      $paramPassword = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)) {
        // Redirect to login page
        header("location: login.php");
      } 
      else {
        echo "Something went wrong. Please try again later.";
      }
      // Close statement
      mysqli_stmt_close($stmt);
    }
  }
  // Close connection
  mysqli_close($link);
}
?>


<?php $title = 'Register'; include("templates/top.html");?>
  <div class="wrapper">
    <h2>Register</h2>
    <p>Please fill in this form to create an account.</p>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-row">
        <div class="col-md-6 mb-3">
          <label>First name</label>
          <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Last name</label>
          <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>" required>
        </div>
      </div>
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
        <label>Confirm Password</label>
        <input type="password" name="confirmPassword" class="form-control <?php echo (!empty($confirmPasswordErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmPassword; ?>">
        <span class="invalid-feedback"><?php echo $confirmPasswordErr; ?></span>
      </div>
      <div class="form-group">
        <div class="form-check">
          <input type="checkbox" name="tAndC" class="form-check-input <?php echo (!empty($tAndCErr)) ? 'is-invalid' : ''; ?>">
          <label class="form-check-label">Agree to terms and conditions</label>
          <span class="invalid-feedback"><?php echo $tAndCErr; ?></span>
        </div>
      </div>
      <div class="form-group">  
        <div class="form-check">
          <input type="checkbox" name="over18" class="form-check-input <?php echo (!empty($over18Err)) ? 'is-invalid' : ''; ?>">
          <label class="form-check-label">I am over 18</label>
          <span class="invalid-feedback"><?php echo $over18Err; ?></span>
        </div>
      </div>
      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <input type="reset" class="btn btn-default" value="Reset">
      </div>
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>

  </div>    
<?php include("templates/bottom.html");?>