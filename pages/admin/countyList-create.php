<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$countyName = "";
$countyName_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
  // Validate interestName name
  if(trim($_POST["countyName"])) {
    $countyName = trim($_POST["countyName"]);
  }

  
  // Check input errors before inserting in database
  if(empty($countyName_err) ) {
    // Add entry to interestList table
    $sql = "INSERT INTO countylist (countyName, ?);";
    if($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $paramCountyName);
      // Set parameters
      $paramCountyName = $countyName;
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)){
              // Records created successfully. Redirect to landing page
              header("location: countyList-home.php");
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
 
<?php $title = 'Admin | County | Create'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Create County</h2>
                    </div>
                    <p>Please fill this form and submit to add a County record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
					              <div class="form-group <?php echo (!empty($countyList_err)) ? 'has-error' : ''; ?>">
                            <label>County Name</label>
                            <input type="text" name="countyName" class="form-control" value="<?php echo $countyName; ?>" required>
                            <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="user-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>
