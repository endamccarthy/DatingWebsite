<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: login.php");
  exit;
}

// Include config file
require_once "../scripts/config.php";

$matches = "";
$userID = $_SESSION["userID"];

// Prepare a select statement
$sql = "SELECT DISTINCT firstName, lastName, countyName FROM user JOIN profile JOIN countyList ON 
user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID IN (
  SELECT matchesUserTwo FROM matches WHERE matchesUserOne = $userID
  UNION ALL
  SELECT matchesUserOne FROM matches WHERE matchesUserTwo = $userID
);";

if($stmt = mysqli_prepare($link, $sql)) {
  // Attempt to execute the prepared statement
  if(mysqli_stmt_execute($stmt)) {
    // Store result
    mysqli_stmt_store_result($stmt);
    // Check if matches are found
    if(mysqli_stmt_num_rows($stmt) >= 1) {
      mysqli_stmt_bind_result($stmt, $firstName, $lastName, $countyName);
      while (mysqli_stmt_fetch($stmt)) {
        $matches .= '<p><a href="#">'.$firstName.' '.$lastName.'</a><br>'.$countyName.'</p>';
      } 
    }
    else {
      $matches = "<p>Sorry, no matches yet!</p>";
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
 
<?php $title = 'Matches'; include("templates/top.html");?>
  <div style="text-align: center">
    <h2>Matches - To Do...</h2>
    <div>
      <?php echo $matches; ?>
    </div>
  </div>
<?php include("templates/bottom.html");?>