<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables and initialize with empty values
$firstName = $lastName = "";
$firstName_err = $lastName_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["userID"]) && !empty($_POST["userID"])){
    // Get hidden input value
    $userID = $_POST["userID"];

    $sql = "UPDATE user SET status = 'suspended' WHERE userID = $userID";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
    }
    $sql = "DELETE FROM reported WHERE reportedUserTwo = $userID;";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
      mysqli_stmt_close($stmt);
    }
    header("location: admin-home.php");
    exit;

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
  } else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: admin-error.php");
    exit();
  }

}
?>
 
 <?php $title = 'Admin | User | Update'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Suspend User</h2>
                    </div>
                    <h4><?php echo $firstName . " " . $lastName ?></h4><br>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						          <input type="hidden" name="userID" value="<?php echo $userID; ?>"/>
                      <input type="submit" class="btn btn-primary" value="Suspend">
                      <a href="admin-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>