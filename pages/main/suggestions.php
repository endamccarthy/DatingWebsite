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
$gender = getEntryNameGivenID($link, 'profile', 'gender', 'userID', $userID);
$prefGender = getEntryNameGivenID($link, 'preferences', 'prefGender', 'userID', $userID);
$accessLevel = getEntryNameGivenID($link, 'user', 'accessLevel', 'userID', $userID);
$notifications = getEntryNameGivenID($link, 'user', 'notifications', 'userID', $userID);

// Prepare a select statement
$sql = "SELECT DISTINCT user.userID, firstName, lastName, dateOfBirth, photo, countyName FROM user JOIN profile JOIN countyList ON 
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
  status = 'active'
  AND
  gender = (
    SELECT prefGender FROM preferences WHERE userID = $userID
  )
  AND
  userID IN (
    SELECT userID FROM preferences WHERE prefGender = '$gender'
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
 
<?php $title = 'Suggestions'; include("../templates/top.html"); include("../templates/sub-navbar.html");?>
<div class="container">
  <div class="container-item container-item-center-text container-item-shadow">
    <h2 class="pb-2 mt-2 mb-4 border-bottom">Suggestions</h2>
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
