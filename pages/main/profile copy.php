<?php

// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$firstName = $lastName = $photoAddress = $gender = $dateOfBirth = $countyName = $smokes = $description = $email = $accessLevel = "";
$height = 0;
$interests;
$preferences;
$profileComplete = $_SESSION["profileComplete"];
$relationshipStatus = "none";
$myProfile = false;

// Check if profile is user's own or someone elses
if(isset($_GET["userID"]) && !empty(trim($_GET["userID"]))) {
  $userID = $_GET["userID"];
  // Get relationship status with other user
  if (checkIfUserPairExists($link, 'pending', 'pendingUserOne', 'pendingUserTwo', $userID, $_SESSION["userID"])) { $relationshipStatus = "youLikeThem"; }
  else if (checkIfUserPairExists($link, 'pending', 'pendingUserOne', 'pendingUserTwo', $_SESSION["userID"], $userID)) { $relationshipStatus = "theyLikeYou"; }
  else if (checkIfUserPairExists($link, 'rejections', 'rejectionsUserOne', 'rejectionsUserTwo', $userID, $_SESSION["userID"])) { $relationshipStatus = "youRejectThem"; }
  else if (checkIfUserPairExists($link, 'rejections', 'rejectionsUserOne', 'rejectionsUserTwo', $_SESSION["userID"], $userID)) { $relationshipStatus = "theyRejectYou"; }
  else if (checkIfUserPairExistsMatches($link, 'matches', 'matchesUserOne', 'matchesUserTwo', $_SESSION["userID"], $userID)) { $relationshipStatus = "match"; }
}
else {
  $userID = $_SESSION["userID"];
  $myProfile = true;
}

// Get user details
$sql = "SELECT firstName, lastName, email, accessLevel, description, gender, dateOfBirth, countyName, photo, smokes, height FROM user 
JOIN profile JOIN countyList ON user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID = $userID;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) == 1) {
      mysqli_stmt_bind_result($stmt, $firstNameTemp, $lastNameTemp, $emailTemp, $accessLevelTemp, $descriptionTemp, $genderTemp, $dateOfBirthTemp, $countyNameTemp, $photoTemp, $smokesTemp, $heightTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $firstName = $firstNameTemp;
        $lastName = $lastNameTemp;
        $email = $emailTemp;
        $accessLevel = $accessLevelTemp;
        if($descriptionTemp) {$description = $descriptionTemp;};
        $gender = $genderTemp;
        $dateOfBirth = $dateOfBirthTemp;
        $countyName = $countyNameTemp;
        $photoAddress = $photoTemp;
        $smokes = $smokesTemp;
        $height = $heightTemp;
      }
    }
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

// Get user interests
$sql = "SELECT interestName FROM interestList JOIN interests ON interestList.interestID = interests.interestID WHERE interests.userID = $userID;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) >= 1) {
      mysqli_stmt_bind_result($stmt, $interestNameTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $interests[] = $interestNameTemp;
      }
    }
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

// Get user preferences
$preferences = getUserPreferences($link, $userID);
$prefGender = $preferences['prefGender'];
$prefSmokes = $preferences['prefSmokes'];
$prefAgeMin = $preferences['prefAgeMin'];
$prefAgeMax = $preferences['prefAgeMax'];
$prefHeightMin = $preferences['prefHeightMin'];
$prefHeightMax = $preferences['prefHeightMax'];
$prefInterestID = $preferences['prefInterestID'];
$prefCountyID = $preferences['prefCountyID'];
$prefCountyName = getEntryNameGivenID($link, 'countyList', 'countyName', 'countyID', $prefCountyID);
$prefInterestName = getEntryNameGivenID($link, 'interestList', 'interestName', 'interestID', $prefInterestID);

// Close connection
mysqli_close($link);
?>

<?php $title = ($myProfile) ? 'My Profile' : $firstName.' '.$lastName; include("../templates/top.html"); ?>

<div class="container">
  <div class="container-item">
    <?php
      if(isset($_SESSION["search"]) && !$myProfile) {
        echo '<a href="'.$_SESSION["search"].'" class="btn btn-secondary m-1">Back To Search Results</a>';
      }
    ?>
    <?php
      if($myProfile) {
        if($accessLevel == 'regular') {
          echo '<button type="button" data-target="#upgradeConfirm" data-toggle="modal" class="btn btn-light btn-sm m-1">Upgrade to Premium</button>';
        }
      }
    ?>
    <div class="mt-1 mb-2 border-bottom"> 
      <h2><?php echo ($myProfile) ? 'My Profile' : $firstName.' '.$lastName ?></h2>
    </div>
    <div class="pb-2 mt-2 mb-4 border-bottom">
      <img src="<?php echo $photoAddress ?>" alt="Avatar"> 
    </div>
    <div class="pb-2 m-3 mb-4 border-bottom">
      <div>
        <h6>Gender: <?php echo $gender ?></h6>
      </div>
    </div>
  </div>
  <div class="container-item container-item-stack">
    <div class="container-item container-item-inside-stack">
      <div class="mt-1 mb-2 border-bottom">  
        <h2><?php echo ($myProfile) ? 'My Preferences' : $firstName."'s Preferences";?></h2>
      </div>
      <div class="pb-2 m-3 mb-4 border-bottom">
        <div>
          <h6>Preferred Gender: <?php echo $prefGender ?></h6>
        </div>
      </div>
      <?php
        if($myProfile) {
          echo '<div class="m-3">';
          echo '<p><a href="edit-preferences.php" class="btn btn-secondary btn-sm">Edit</a></p>';
          echo '</div>';
        }
      ?>
    </div>
    <div class="container-item container-item-inside-stack">
      <div class="mt-1 mb-2 border-bottom">  
        <h2><?php echo ($myProfile) ? 'My Preferences' : $firstName."'s Preferences";?></h2>
      </div>
      <div class="pb-2 m-3 mb-4 border-bottom">
        <div>
          <h6>Preferred Gender: <?php echo $prefGender ?></h6>
        </div>
      </div>
      <?php
        if($myProfile) {
          echo '<div class="m-3">';
          echo '<p><a href="edit-preferences.php" class="btn btn-secondary btn-sm">Edit</a></p>';
          echo '</div>';
        }
      ?>
    </div>
  </div>
</div>

<!-- Upgrade to Premium Modal -->
<div class="modal fade" id="upgradeConfirm" tabindex="-1" role="dialog" aria-labelledby="upgradeConfirmLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="upgradeConfirmLabel">Upgrade to Premium</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to upgrade to premium?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a href="../../utilities/action.php?action=upgrade" class="btn btn-primary">Yes, I'm sure</a>
      </div>
    </div>
  </div>
</div>

<?php include("../templates/bottom.html");?>
