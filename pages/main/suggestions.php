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

$suggestions = "";
$userID = $_SESSION["userID"];

// Prepare a select statement
$sql = "SELECT DISTINCT firstName, lastName, countyName FROM user JOIN profile JOIN countyList ON 
user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID IN (
  SELECT userID FROM profile WHERE userID != $userID
  AND
  userID NOT IN (
    SELECT pendingUserTwo FROM pending WHERE pendingUserOne = $userID
    UNION ALL
    SELECT matchesUserTwo FROM matches WHERE matchesUserOne = $userID
    UNION ALL
    SELECT matchesUserOne FROM matches WHERE matchesUserTwo = $userID
    UNION ALL
    SELECT rejectionsUserTwo FROM rejections WHERE rejectionsUserOne = $userID
    UNION ALL
    SELECT rejectionsUserOne FROM rejections WHERE rejectionsUserTwo = $userID
  )
  AND
  gender = (
    SELECT prefGender FROM preferences WHERE userID = $userID
  ) 
  AND 
  TIMESTAMPDIFF(YEAR, dateOfBirth, NOW()) BETWEEN 
    (SELECT prefAgeMin FROM preferences WHERE userID = $userID) AND 
    (SELECT prefAgeMax FROM preferences WHERE userID = $userID) 
  AND 
  countyID IN (
    SELECT prefCountyID FROM preferences WHERE userID = $userID 
    UNION ALL 
    SELECT countyID FROM countyList WHERE NOT EXISTS (
      SELECT prefCountyID FROM preferences WHERE userID = $userID AND prefCountyID IS NOT NULL
    )
  )
  AND
  userID IN (
    SELECT userID FROM interests WHERE interestID IN (
      SELECT prefInterestID FROM preferences WHERE userID = $userID
    )
    UNION ALL
    SELECT userID FROM user WHERE NOT EXISTS (
      SELECT prefInterestID FROM preferences WHERE userID = $userID AND prefInterestID IS NOT NULL
    )
  )
  AND
  smokes IN (
    SELECT prefSmokes FROM preferences WHERE userID = $userID
    UNION ALL
    SELECT smokes FROM profile WHERE NOT EXISTS (
      SELECT prefSmokes FROM preferences WHERE userID = $userID AND prefSmokes IS NOT NULL
    )
  )
  AND
  height BETWEEN
    (SELECT prefHeightMin FROM preferences WHERE userID = $userID) AND 
    (SELECT prefHeightMax FROM preferences WHERE userID = $userID)
);";

if($stmt = mysqli_prepare($link, $sql)) {
  // Attempt to execute the prepared statement
  if(mysqli_stmt_execute($stmt)) {
    // Store result
    mysqli_stmt_store_result($stmt);
    // Check if suggestions are found
    if(mysqli_stmt_num_rows($stmt) >= 1) {
      mysqli_stmt_bind_result($stmt, $firstName, $lastName, $countyName);
      while (mysqli_stmt_fetch($stmt)) {
        $suggestions .= '<p><a href="#">'.$firstName.' '.$lastName.'</a><br>'.$countyName.'</p>';
      } 
    }
    else {
      $suggestions = "<p>Sorry, no suggestions!</p>";
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
 
<?php $title = 'Suggestions'; include("../templates/top.html");?>
  <div style="text-align: center">
    <div class="wrapper">
      <h2>Suggestions</h2>
      <div>
        <?php echo $suggestions; ?>
      </div>
    </div>
  </div>
<?php include("../templates/bottom.html");?>