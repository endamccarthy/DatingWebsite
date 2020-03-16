<?php
// Include config file
require_once "../scripts/config.php";
 
// Define variables and initialize with empty values
$first_name = $last_name = $email = $password = $confirm_password = $t_and_c = "";
$email_err = $password_err = $confirm_password_err = $t_and_c_err = $over_18_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Validate first name
    if(trim($_POST["first_name"])) {
        $first_name = trim($_POST["first_name"]);
    }

    // Validate last name
    if(trim($_POST["last_name"])) {
        $last_name = trim($_POST["last_name"]);
    }

    // Validate email
    if(empty(trim($_POST["email"]))) {
        $email_err = "Please enter a email.";
    }
    else {
        // Prepare a select statement
        $sql = "SELECT userID FROM user WHERE email = ?";
        if($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            // Set parameters
            $param_email = trim($_POST["email"]);
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
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } 
    else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Validate terms and conditions 
    if(!isset($_POST["t_and_c"])) {
        $t_and_c_err = "You must agree before submitting.";
    }

    // Validate over 18 
    if(!isset($_POST["over_18"])) {
        $over_18_err = "You must be over 18 to register";
    }

    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($t_and_c_err) && empty($over_18_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO user (firstName, lastName, email, password) VALUES (?, ?, ?, ?);";
        if($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_first_name, $param_last_name, $param_email, $param_password);
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
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
                    <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Last name</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" required>
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="t_and_c" class="form-check-input <?php echo (!empty($t_and_c_err)) ? 'is-invalid' : ''; ?>" value="">
                    <label class="form-check-label">
                        Agree to terms and conditions
                    </label>
                    <span class="invalid-feedback"><?php echo $t_and_c_err; ?></span>
                </div>
            </div>
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="over_18" class="form-check-input <?php echo (!empty($over_18_err)) ? 'is-invalid' : ''; ?>" value="">
                    <label class="form-check-label">
                        I am over 18
                    </label>
                    <span class="invalid-feedback"><?php echo $over_18_err; ?></span>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>

    </div>    
<?php include("templates/bottom.html");?>