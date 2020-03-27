<?php

// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect to welcome page
if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
  header("location: ../main/welcome.php");
  exit;
}

// Include config file
require_once "../../scripts/config.php";
 
// Define variables
$firstName = $lastName = $email = $password = $confirmPassword = "";
$emailErr = $passwordErr = $confirmPasswordErr = $tAndCErr = $over18Err = "";
$userID; 

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
    $emailErr = "Please enter an email address.";
  }
  else {
    // Check user table for email address
    $sql = "SELECT userID FROM user WHERE email = ?;";
    if($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $paramEmail);
      $paramEmail = trim($_POST["email"]);
      if(mysqli_stmt_execute($stmt)) {
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
    $sql = "INSERT INTO user (firstName, lastName, email, password) VALUES ('$firstName', '$lastName', '$email', ?);";
    if($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $paramPassword);
      // Create a password hash
      $paramPassword = password_hash($password, PASSWORD_DEFAULT);
      if(!mysqli_stmt_execute($stmt)) {
        echo "Oops! Something went wrong. Please try again later.";
      }
      // Close statement
      mysqli_stmt_close($stmt);
    }
    // Retrieve the new users ID
    $sql = "SELECT userID FROM user WHERE email = '$email';";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if(mysqli_stmt_num_rows($stmt) == 1) {
          mysqli_stmt_bind_result($stmt, $userIDTemp);
          while (mysqli_stmt_fetch($stmt)) {
            // Store data in session variables
            $_SESSION["loggedIn"] = true;
            $_SESSION["userID"] = $userIDTemp;
            $_SESSION["email"] = $email;
            $_SESSION["profileComplete"] = false;
            header("location: ../main/edit-profile.php");
          }
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


<?php $title = 'Register'; include("../templates/top.html");?>
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
        <input type="password" name="password" class="form-control <?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" required>
        <span class="invalid-feedback"><?php echo $passwordErr; ?></span>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirmPassword" class="form-control <?php echo (!empty($confirmPasswordErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmPassword; ?>" required>
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
<?php include("../templates/bottom.html");?>
