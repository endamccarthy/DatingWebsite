<?php
// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$suggestions = "";
$userID = $_SESSION["userID"];
$prefGender = "";
$prefGender = getEntryNameGivenID($link, 'preferences', 'prefGender', 'userID', $userID);
$accessLevel = getEntryNameGivenID($link, 'user', 'accessLevel', 'userID', $userID);
$notifications = getEntryNameGivenID($link, 'user', 'notifications', 'userID', $userID);

// Prepare a select statement
$sql = "SELECT DISTINCT user.userID, firstName, lastName, countyName FROM user JOIN profile JOIN countyList ON 
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
  userID IN (
    SELECT userID FROM preferences WHERE prefGender != '$prefGender'
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

// Execute sql statement and save results to a string
$suggestions = getProfileResultsString($link, $sql);

// Close connection
mysqli_close($link);
?>
 
<?php $title = 'Suggestions'; include("../templates/top.html"); ?>
<div class="mt-3" style="text-align: center">
  <a href="../main/suggestions.php" class="btn btn-secondary m-1">Suggestions</a>
  <a href="../main/matches.php" class="btn btn-secondary m-1">Matches<?php echo ($notifications > 0) ? '<span style="color: darkred;"> ('.$notifications.' new)</span>' : ''?></a>
  <div class="tooltip-wrapper" title='Upgrade to premium in your profile page for access' data-toggle='tooltip' style="display:inline-block;">
    <a href="../main/waiting.php" class="btn btn-secondary m-1 <?php echo ($accessLevel == "regular") ? "disabled" : "" ?>" id="waitingForYou">Waiting For You</a>
  </div>
  <div class="wrapper">
    <h2>Suggestions</h2>
    <div>
      <?php echo $suggestions; ?>
    </div>
  </div>
</div>
<?php include("../templates/bottom.html");?>

<script type="text/javascript">
// Show tooltip if Waiting For You section is disabled
$(document).ready(function(){
  if(document.getElementById("waitingForYou").classList.contains('disabled')) {
    $('[data-toggle="tooltip"]').tooltip();   
  }
});
</script>
