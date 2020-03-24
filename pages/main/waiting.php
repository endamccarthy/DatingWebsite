<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: ../login/login.php");
  exit;
}

// Include config file
require_once "../../scripts/config.php";

$pending = "";
$userID = $_SESSION["userID"];

// Prepare a select statement
$sql = "SELECT DISTINCT firstName, lastName, countyName FROM user JOIN profile JOIN countyList ON 
user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID IN (
  SELECT pendingUserOne FROM pending WHERE pendingUserTwo = $userID
);";

if($stmt = mysqli_prepare($link, $sql)) {
  // Attempt to execute the prepared statement
  if(mysqli_stmt_execute($stmt)) {
    // Store result
    mysqli_stmt_store_result($stmt);
    // Check if pending are found
    if(mysqli_stmt_num_rows($stmt) >= 1) {
      mysqli_stmt_bind_result($stmt, $firstName, $lastName, $countyName);
      while (mysqli_stmt_fetch($stmt)) {
        $pending .= '<p><a href="#">'.$firstName.' '.$lastName.'</a><br>'.$countyName.'</p>';
      } 
    }
    else {
      $pending = "<p>Sorry, no matches yet!</p>";
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
?>
 
<?php $title = 'Waiting For You'; include("../templates/top.html");?>
  <div style="text-align: center">
    <div class="wrapper">
      <h2>Waiting For You - To Do...</h2>
      <div>
        <?php echo $pending; ?>
      </div>
    </div>
  </div>
<?php include("../templates/bottom.html");?>