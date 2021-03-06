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
$accessLevel = getEntryNameGivenID($link, 'user', 'accessLevel', 'userID', $userID);
$notifications = getEntryNameGivenID($link, 'user', 'notifications', 'userID', $userID);

// Reset notifications
$sql = "UPDATE user SET notifications = 0 WHERE userID = $userID;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(!mysqli_stmt_execute($stmt)) {
    echo "Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

// Prepare a select statement
$sql = "SELECT DISTINCT user.userID, firstName, lastName, dateOfBirth, photo, countyName FROM user JOIN profile JOIN countyList ON 
user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID IN (
  SELECT matchesUserTwo FROM matches WHERE matchesUserOne = $userID
  UNION ALL
  SELECT matchesUserOne FROM matches WHERE matchesUserTwo = $userID
);";

// Execute sql statement and save results to a string
$matches = getProfileResultsString($link, $sql);

// Close connection
mysqli_close($link);
?>
 
<?php $title = 'Matches'; include("../templates/top.html"); include("../templates/sub-navbar.html");?>
<div class="container">
  <div class="container-item container-item-center-text container-item-shadow">
    <h2 class="pb-2 mt-2 mb-4 border-bottom">Matches</h2>
    <div>
      <?php echo $matches; ?>
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