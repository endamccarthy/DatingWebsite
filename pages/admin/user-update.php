<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$firstName = $lastName = $email = $password = "";
$firstName_err = $lastName_err = $email_err = $password_err = "";
//$userID = $_SESSION["userID"];
 
// Processing form data when form is submitted
if(isset($_POST["userID"]) && !empty($_POST["userID"])){
    // Get hidden input value
    $userID = $_POST["userID"];
    
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
			} else {
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
    
    
    // Check input errors before updating database
    if(empty($name_err) && empty($address_err) && empty($salary_err)){
        // Prepare an update statement
        $sql = "UPDATE user SET firstName=?, lastName=?, email=?, password=? WHERE userID=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $param_firstName, $param_lastName, $param_email, $param_password, $param_userID);
            
            // Set parameters
            $param_firstName = $firstName;
            $param_lastName = $lastName;
            $param_email = $email;
			$param_password = $password;
            $param_userID = $userID;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: user-home.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["userID"]) && !empty(trim($_GET["userID"]))){
        // Get URL parameter
        $userID =  trim($_GET["userID"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM user WHERE userID = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_userID);
            
            // Set parameters
            $param_userID = $userID;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
					$firstName = $row["firstName"];
					$lastName = $row["lastName"];
					$email = $row["email"];
					$password = $row["password"];	
                } else{
                    // URL doesn't contain valid id. Redirect to error page
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
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: admin-error.php");
        exit();
    }
}
?>
 
 <?php $title = 'Admin | User | Update'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Update User</h2>
                    </div>
                    <p>Please edit the input values and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
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
                        
						<input type="hidden" name="userID" value="<?php echo $userID; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="admin-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>