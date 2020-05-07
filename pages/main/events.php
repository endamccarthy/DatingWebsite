<?php
// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$events = "";
$userID = $_SESSION["userID"];

// Prepare a select statement
$sql = "SELECT DISTINCT events.eventID, eventName, eventDate, eventWebsite, eventPhoto, countyName FROM events 
JOIN countyList ON events.eventCountyID = countyList.countyID;";


if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) >= 1) {
      $resultsString = "";
      mysqli_stmt_bind_result($stmt, $eventIDTemp, $eventNameTemp, $eventDateTemp, $eventWebsiteTemp, $eventPhotoTemp, $countyNameTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $eventDateTemp = date_format(date_create($eventDateTemp), "d-m-Y");
        $resultsString .= '<div class="profile-card"><a href="'.$eventWebsiteTemp.'"><img class="profile-card-img" src="'.$eventPhotoTemp.'" alt="Event Photo"></a>';
        $resultsString .= '<div class="profile-card-text"><h6>'.$eventNameTemp.'<br>'.$eventDateTemp.'<br>'.$countyNameTemp.'</h6></div></div>';
      }
    }
    else {
      $resultsString = "<p>Sorry, no results!</p>";
    }
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
  $events = $resultsString;
}

// Close connection
mysqli_close($link);
?>
 
<?php $title = 'Events'; include("../templates/top.html");?>
<div class="container">
  <div class="container-item container-item-center-text container-item-shadow">
    <h2 class="pb-2 mt-2 mb-4 border-bottom">Events</h2>
    <div>
      <?php echo $events; ?>
    </div>
  </div>
</div>
<?php include("../templates/bottom.html");?>
