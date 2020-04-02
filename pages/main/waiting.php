<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$pending = "";
$userID = $_SESSION["userID"];
$accessLevel = getEntryNameGivenID($link, 'user', 'accessLevel', 'userID', $userID);

// Only premium or admin allowed access to this page
if($accessLevel == 'regular') {
  header("location: javascript:history.back()");
  exit;
}

// Prepare a select statement
$sql = "SELECT DISTINCT user.userID, firstName, lastName, countyName FROM user JOIN profile JOIN countyList ON 
user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID IN (
  SELECT pendingUserOne FROM pending WHERE pendingUserTwo = $userID
);";

// Execute sql statement and save results to a string
$pending = getProfileResultsString($link, $sql);

// Close connection
mysqli_close($link);
?>
 
<?php $title = 'Waiting For You'; include("../templates/top.html"); ?>
<div class="mt-3" style="text-align: center">
  <a href="../main/suggestions.php" class="btn btn-secondary m-1">Suggestions</a>
  <a href="../main/matches.php" class="btn btn-secondary m-1">Matches</a>
  <div class="tooltip-wrapper" title='Upgrade to premium in your profile page for access' data-toggle='tooltip' style="display:inline-block;">
    <a href="../main/waiting.php" class="btn btn-secondary m-1 <?php echo ($accessLevel == "regular") ? "disabled" : "" ?>" id="waitingForYou">Waiting For You</a>
  </div>
  <div class="wrapper">
    <h2>Waiting For You</h2>
    <div>
      <?php echo $pending; ?>
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