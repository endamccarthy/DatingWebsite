<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$firstName = $lastName =$email = $password = "";
$firstName_err = $lastName_err = $email_err = $password_err = "";
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
    $email_err = "Please enter an email address.";
  }
  else {
    // Check user table for email address
    $sql = "SELECT userID FROM user WHERE email = '$email';";
    if($stmt = mysqli_prepare($link, $sql)) {
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)) {
        /* store result */
        mysqli_stmt_store_result($stmt);
        if(mysqli_stmt_num_rows($stmt) == 1) {
          $email_err = "This email is already taken.";
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
    $password_err = "Please enter a password.";     
  } 
  elseif(strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have at least 6 characters.";
  } 
  else {
    $password = trim($_POST["password"]);
  }
  
  // Check input errors before inserting in database
  if(empty($email_err) && empty($password_err)) {
    // Add entry to user table
    $sql = "INSERT INTO user (firstName, lastName, email, password) VALUES ('$firstName', '$lastName', '$email', ?);";
    if($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $paramPassword);
      // Set parameters
      $paramPassword = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      // Attempt to execute the prepared statement
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
      // Close statement
      mysqli_stmt_close($stmt);
    }
  }

  // Retrieve the new users ID
  $sql = "SELECT userID FROM user WHERE email = '$email';";
  if($stmt = mysqli_prepare($link, $sql)) {
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)) {
      /* store result */
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $userID);
        while (mysqli_stmt_fetch($stmt)) {
          // Store data in session variables
          $_SESSION["loggedIn"] = true;
          $_SESSION["userID"] = $userID;
          $_SESSION["email"] = $email;
// $_SESSION["profileComplete"] = false;
          header("location: ../admin/user-home.php");
        }
      }
    } 
    else {
      echo "Oops! Something went wrong. Please try again later.";
    }
    // Close statement
    mysqli_stmt_close($stmt);
  }

  // Close connection
  mysqli_close($link);

}
?>
 
<?php $title = 'Admin | User | Create'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Create User</h2>
                    </div>
                    <p>Please fill this form and submit to add a User record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
					              <div class="form-group <?php echo (!empty($firstName_err)) ? 'has-error' : ''; ?>">
                            <label>First Name</label>
                            <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>" required>
                        </div>
                        <div class="form-group <?php echo (!empty($lastName_err)) ? 'has-error' : ''; ?>">
                            <label>Last Name</label>
                            <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>" required>
                        </div>
                        <div class="form-group">
							              <label>Email</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
							              <span class="invalid-feedback"><?php echo $emailErr; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" required>
							              <span class="invalid-feedback"><?php echo $password; ?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="user-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>