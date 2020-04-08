<?php

// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables
$firstName = $lastName = $emailLogin = $passwordLogin = $emailRegister = $passwordRegister = $confirmPassword = "";
$emailErrLogin = $passwordErrLogin = $emailErrRegister = $passwordErrRegister = $confirmPasswordErr = $tAndCErr = $over18Err = "";
$userID; 

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
  switch ($_POST["action"]) {
    // Login Form
    case "login":
      // Check if email is empty
      if(empty(trim($_POST["emailLogin"]))) {
        $emailErrLogin = "Please enter email.";
      } 
      else {
        $emailLogin = trim($_POST["emailLogin"]);
      }

      // Check if password is empty
      if(empty(trim($_POST["passwordLogin"]))) {
        $passwordErrLogin = "Please enter your password.";
      } 
      else {
        $passwordLogin = trim($_POST["passwordLogin"]);
      }
      
      // Validate credentials
      if(empty($emailErrLogin) && empty($passwordErrLogin)) {
        // Prepare a select statement
        $sql = "SELECT userID, email, password, status FROM user WHERE email = '$emailLogin';";
        if($stmt = mysqli_prepare($link, $sql)) {
          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            // Check if email exists, if yes then verify password
            if(mysqli_stmt_num_rows($stmt) == 1) {
              mysqli_stmt_bind_result($stmt, $userIDTemp, $emailTemp, $hashedPasswordTemp, $statusTemp);
              if(mysqli_stmt_fetch($stmt)) {
                if($statusTemp == "suspended") {
                  $passwordErrLogin = "Sorry this account is temporarily suspended.";
                }
                else if($statusTemp == "banned") {
                  $passwordErrLogin = "This account has been banned.";
                }
                else {
                  if(password_verify($passwordLogin, $hashedPasswordTemp)) {
                    // Password is correct, so start a new session
                    session_start();
                    // Store data in session variables
                    $_SESSION["loggedIn"] = true;
                    $_SESSION["userID"] = $userIDTemp;
                    $_SESSION["email"] = $emailTemp;
                  } 
                  else {
                    $passwordErrLogin = "The password you entered was not valid.";
                  }
                }
              }
            } 
            else {
              $emailErrLogin = "No account found with that email.";
            }
          } 
          else {
            echo "Oops! Something went wrong. Please try again later.";
          }
          // Close statement
          mysqli_stmt_close($stmt);
        }

        // check if user has completed their profile, redirect them to edit profile page if not
        if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
          $userID = $_SESSION["userID"];
          // check if user has entry in profile table
          $sql = "SELECT userID FROM profile WHERE userID = $userID;";
          if($stmt = mysqli_prepare($link, $sql)) {
            if(mysqli_stmt_execute($stmt)) {
              mysqli_stmt_store_result($stmt);
              if(mysqli_stmt_num_rows($stmt) !== 1) {
                $_SESSION["profileComplete"] = false;
                header("location: ../main/edit-profile.php");
              } 
              else {
                $_SESSION["profileComplete"] = true;
                header("location: ../main/suggestions.php");
              }
            } 
            else {
              echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
          }
        }
      }
    break;
    // Register Form
    case "register":
      // Validate first name
      if(trim($_POST["firstName"])) {
        $firstName = trim($_POST["firstName"]);
      }

      // Validate last name
      if(trim($_POST["lastName"])) {
        $lastName = trim($_POST["lastName"]);
      }

      // Validate email
      if(empty(trim($_POST["emailRegister"]))) {
        $emailErrRegister = "Please enter an email address.";
      }
      else {
        // Check user table for email address
        $sql = "SELECT userID FROM user WHERE email = ?;";
        if($stmt = mysqli_prepare($link, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $paramEmail);
          $paramEmail = trim($_POST["emailRegister"]);
          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1) {
              $emailErrRegister = "This email is already taken.";
            } 
            else {
              $emailRegister = trim($_POST["emailRegister"]);
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
      if(empty(trim($_POST["passwordRegister"]))) {
        $passwordErrRegister = "Please enter a password.";     
      } 
      elseif(strlen(trim($_POST["passwordRegister"])) < 6) {
        $passwordErrRegister = "Password must have at least 6 characters.";
      } 
      else {
        $passwordRegister = trim($_POST["passwordRegister"]);
      }
      
      // Validate confirm password
      if(empty(trim($_POST["confirmPassword"]))) {
        $confirmPasswordErr = "Please confirm password.";     
      } 
      else {
        $confirmPassword = trim($_POST["confirmPassword"]);
        if(empty($passwordErrRegister) && ($passwordRegister != $confirmPassword)) {
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
      if(empty($emailErrRegister) && empty($passwordErrRegister) && empty($confirmPasswordErr) && empty($tAndCErr) && empty($over18Err)) {
        $sql = "INSERT INTO user (firstName, lastName, email, password) VALUES ('$firstName', '$lastName', '$emailRegister', ?);";
        if($stmt = mysqli_prepare($link, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $paramPassword);
          // Create a password hash
          $paramPassword = password_hash($passwordRegister, PASSWORD_DEFAULT);
          if(!mysqli_stmt_execute($stmt)) {
            echo "Oops! Something went wrong. Please try again later.";
          }
          // Close statement
          mysqli_stmt_close($stmt);
        }
        // Retrieve the new users ID
        $sql = "SELECT userID FROM user WHERE email = '$emailRegister';";
        if($stmt = mysqli_prepare($link, $sql)) {
          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1) {
              mysqli_stmt_bind_result($stmt, $userIDTemp);
              while (mysqli_stmt_fetch($stmt)) {
                // Store data in session variables
                $_SESSION["loggedIn"] = true;
                $_SESSION["userID"] = $userIDTemp;
                $_SESSION["email"] = $emailRegister;
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
    break;
    default:
      echo "Oops! Something went wrong. Please try again later.";
  }
}
// Close connection
mysqli_close($link);
?>


<?php $title = 'Foxy Farmers'; include("../templates/top.html");?>
<div class="container">
  <div class="container-item container-item-center-text">
    <h2><i>Find Your Foxy Farmer!</i></h2>
  </div>
</div>
<div class="container">
  <div class="container-item">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form name="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="emailLogin" class="form-control <?php echo (!empty($emailErrLogin)) ? 'is-invalid' : ''; ?>" value="<?php echo $emailLogin; ?>" required>
        <span class="invalid-feedback"><?php echo $emailErrLogin; ?></span>
      </div>   
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="passwordLogin" class="form-control <?php echo (!empty($passwordErrLogin)) ? 'is-invalid' : ''; ?>" value="<?php echo $passwordLogin; ?>">
        <span class="invalid-feedback"><?php echo $passwordErrLogin; ?></span>
      </div>
      <div class="form-group">
        <input type="hidden" name="action" value="login">
        <input type="submit" class="btn btn-primary" value="Login">
      </div>
    </form>
    <div class="p-3 mt-4 border-top" style="text-align: center;">
      <p>“Sometimes I think I’d be less lonely living in an enclosed convent than in small town Ireland.”</p>
      <p><i>The Irish Times, Aine Ryan, 25 June 2019</i></p><br>
      <h5><b>Foxy Farmers</b> is a dating site for people living or wanting to live in a rural farming community in Ireland.</h5>
    </div>
  </div> 

  <div class="container-item">
    <h2>Register</h2>
    <p>Please fill in this form to create an account.</p>
    <form name="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
        <input type="email" name="emailRegister" class="form-control <?php echo (!empty($emailErrRegister)) ? 'is-invalid' : ''; ?>" value="<?php echo $emailRegister; ?>" required>
        <span class="invalid-feedback"><?php echo $emailErrRegister; ?></span>
      </div>    
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="passwordRegister" class="form-control <?php echo (!empty($passwordErrRegister)) ? 'is-invalid' : ''; ?>" value="<?php echo $passwordRegister; ?>" required>
        <span class="invalid-feedback"><?php echo $passwordErrRegister; ?></span>
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
        <input type="hidden" name="action" value="register">
        <input type="submit" class="btn btn-primary" value="Submit">
      </div>
    </form>
  </div>
</div>

<?php include("../templates/bottom.html");?>
