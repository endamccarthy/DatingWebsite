<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$interestName = "";
$interestName_err = "";
$interestID; 

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {

// Validate interestName name
if(trim($_POST["interestName"])) {
  $interestName = trim($_POST["interestName"]);
}
else {
  // Check interestList table for interestList name
  $sql = "SELECT interestID FROM interestList WHERE interestName = '$interestName';";
  if($stmt = mysqli_prepare($link, $sql)) {
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)) {
      /* store result */
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $interestID);
        while (mysqli_stmt_fetch($stmt)) {
          // Store data in session variables
          $_SESSION["loggedIn"] = true;
          $_SESSION["userID"] = $userID;
          $_SESSION["interestID"] = $interestID;
          header("location: ../admin/interestList-home.php");
        }
      }
    } 
    else {
      echo "Oops! Something went wrong. Please try again later.";
    }
  }
  // Close statement
  mysqli_stmt_close($stmt);
}

// Check input errors before inserting in database
if(empty($interestName_err) ) {
  // Add entry to interestList table
  $sql = "INSERT INTO interestList (interestName, ?);";
  if($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $paramInterestName);
    // Set parameters
    $paramInterestName = $interestName;

    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
            // Records created successfully. Redirect to landing page
            header("location: interestList-home.php");
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
}
?>

<?php $title = 'Admin | interestList | Create'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                      <h2>Create Interest</h2>
                    </div>
                    <p>Please fill this form and submit to add a User record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
					              <div class="form-group <?php echo (!empty($interestList_err)) ? 'has-error' : ''; ?>">
                            <label>Interest Name</label>
                            <input type="text" name="interestName" class="form-control" value="<?php echo $interestName; ?>" required>
                            <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="user-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>