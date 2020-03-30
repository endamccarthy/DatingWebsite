<?php

// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$firstName = $lastName = $gender = $dateOfBirth = $countyName = $smokes = $description = "";
$height = 0;
$interests;
$preferences;
$profileComplete = $_SESSION["profileComplete"];

// Check existence of id parameter before processing further
if(isset($_GET["userID"]) && !empty(trim($_GET["userID"]))) {
  $userID = $_GET["userID"];
  $myProfile = false;
}
else {
  $userID = $_SESSION["userID"];
  $myProfile = true;
}

// Get user details
$sql = "SELECT firstName, lastName, description, gender, dateOfBirth, countyName, smokes, height FROM user 
JOIN profile JOIN countyList ON user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID = $userID;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) == 1) {
      mysqli_stmt_bind_result($stmt, $firstNameTemp, $lastNameTemp, $descriptionTemp, $genderTemp, $dateOfBirthTemp, $countyNameTemp, $smokesTemp, $heightTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $firstName = $firstNameTemp;
        $lastName = $lastNameTemp;
        if($descriptionTemp) {$description = $descriptionTemp;};
        $gender = $genderTemp;
        $dateOfBirth = $dateOfBirthTemp;
        $countyName = $countyNameTemp;
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

<!-- show as my profile or the other person name -->

<?php $title = ($myProfile) ? 'My Profile' : $firstName.' '.$lastName; include("../templates/top.html");?>
<div class="wrapper">
  <div class="pb-2 mt-4 mb-4 border-bottom">  
    <h2><?php echo ($myProfile) ? 'My Details' : $firstName."'s Details";?></h2>
  </div>
  <div class="pb-2 m-3 mb-4 border-bottom">
    <div>
      <h5>Name: <?php echo $firstName.' '.$lastName ?></h5>
    </div>
    <div>
      <h6>Gender: <?php echo $gender ?></h6>
    </div>
    <div>
      <h6> Age: 
      <?php
        $dateOfBirth = explode("-", $dateOfBirth);
        $age = (date("md", date("U", mktime(0, 0, 0, $dateOfBirth[2], $dateOfBirth[1], $dateOfBirth[0]))) > date("md")
          ? ((date("Y") - $dateOfBirth[0]) - 1)
          : (date("Y") - $dateOfBirth[0]));
        echo $age.' years old';
      ?>
      </h6>
    </div>
    <div>
      <h6>County: <?php echo $countyName ?></h6>
    </div>
    <div>
      <?php
        if (isset($interests)) {
          echo '<h6>Interests: ';
          $str = "";
          foreach ($interests as $interestName) {
            $str .= $interestName.', ';
          }
          $str = rtrim($str, ', ');
          echo $str;
          echo '</h6>';
        }
      ?>
    </div>
    <div>
      <h6>Height: <?php echo $height.'cm' ?></h6>
    </div>
    <div>
      <h6>Smokes? <?php echo $smokes ?></h6>
    </div>
    <div>
      <?php
        if (!empty($description)) {
          echo '<h6>Description: '.$description.'</h6>';
        }
      ?>
    </div>
  </div>
  <?php
    if($myProfile) {
      echo '<div class="m-3">';
      echo '<p><a href="edit-profile.php" class="btn btn-secondary btn-sm">Edit</a></p>';
      echo '</div>';
    }
  ?>
</div>
<div class="wrapper">
  <div class="pb-2 mt-4 mb-4 border-bottom">  
    <h2><?php echo ($myProfile) ? 'My Preferences' : $firstName."'s Preferences";?></h2>
  </div>
  <div class="pb-2 m-3 mb-4 border-bottom">
    <div>
      <h6>Preferred Gender: <?php echo $prefGender ?></h6>
    </div>
    <div>
      <h6>Preferred Age: <?php echo $prefAgeMin.' - '.$prefAgeMax.' year olds' ?></h6>
    </div>
    <div>
      <?php
        if (!empty($prefCountyName)) {
          echo '<h6>Preferred County: '.$prefCountyName.'</h6>';
        }
      ?>
    </div>
    <div>
      <?php
        if (!empty($prefInterestName)) {
          echo '<h6>Preferred Interest: '.$prefInterestName.'</h6>';
        }
      ?>
    </div>
    <div>
      <?php
        if (!empty($prefSmokes)) {
          echo '<h6>Preferred Smokes? '.$prefSmokes.'</h6>';
        }
      ?>
    </div>
    <div>
      <h6>Preferred Height: <?php echo $prefHeightMin.' - '.$prefHeightMax.'cm' ?></h6>
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
<?php include("../templates/bottom.html");?>

