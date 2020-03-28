<?php
// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$matches = "";
$userID = $_SESSION["userID"];

// Prepare a select statement
$sql = "SELECT DISTINCT firstName, lastName, countyName FROM user JOIN profile JOIN countyList ON 
user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID IN (
  SELECT matchesUserTwo FROM matches WHERE matchesUserOne = $userID
  UNION ALL
  SELECT matchesUserOne FROM matches WHERE matchesUserTwo = $userID
);";

// Execute sql statement and save results to a string
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) >= 1) {
      mysqli_stmt_bind_result($stmt, $firstNameTemp, $lastNameTemp, $countyNameTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $matches .= '<p><a href="#">'.$firstNameTemp.' '.$lastNameTemp.'</a><br>'.$countyNameTemp.'</p>';
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
 
<?php $title = 'Matches'; include("../templates/top.html");?>
  <div style="text-align: center">
    <a href="../main/suggestions.php" class="btn btn-info">Suggestions</a>
    <a href="../main/matches.php" class="btn btn-info">Matches</a>
    <a href="../main/waiting.php" class="btn btn-info">Waiting For You</a>
    <div class="wrapper">
      <h2>Matches - To Do...</h2>
      <div>
        <?php echo $matches; ?>
      </div>
    </div>
  </div>
<?php include("../templates/bottom.html");?>