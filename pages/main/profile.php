<?php

// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$firstNameTemp = $lastNameTemp = $gender = $dateOfBirth = $countyNameTemp = $smokes = $description = $prefCountyName = $prefInterestName = $prefGender = $prefSmokes = "";
$height = $prefCountyID = $prefInterestID = $prefAgeMin = $prefAgeMax = $prefHeightMin = $prefHeightMax = 0;
$userID = $_SESSION["userID"];
$profileComplete = $_SESSION["profileComplete"];

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
        $description = $descriptionTemp;
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

$sql = "SELECT prefGender, prefAgeMin, prefAgeMax, prefCountyID, prefInterestID, prefSmokes, prefHeightMin, prefHeightMax FROM preferences WHERE preferences.userID = $userID;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) == 1) {
      mysqli_stmt_bind_result($stmt, $prefGenderTemp, $prefAgeMinTemp, $prefAgeMaxTemp, $prefCountyIDTemp, $prefInterestIDTemp, $prefSmokesTemp, $prefHeightMinTemp, $prefHeightMaxTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $prefGender = $prefGenderTemp;
        $prefAgeMin = $prefAgeMinTemp;
        $prefAgeMax = $prefAgeMaxTemp;
        $prefCountyID = $prefCountyIDTemp;
        $prefInterestID = $prefInterestIDTemp;
        $prefSmokes = $prefSmokesTemp;
        $prefHeightMin = $prefHeightMinTemp;
        $prefHeightMax = $prefHeightMaxTemp;
      }
    }
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

$sql = "SELECT countyName FROM countyList WHERE countyID = $prefCountyID;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) == 1) {
      mysqli_stmt_bind_result($stmt, $prefCountyNameTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $prefCountyName = $prefCountyNameTemp;
      }
    }
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

$sql = "SELECT interestName FROM interestList WHERE interestID = $prefInterestID;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) == 1) {
      mysqli_stmt_bind_result($stmt, $prefInterestNameTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $prefInterestName = $prefInterestNameTemp;
      }
    }
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($link);
?>

<!-- show as my profile or the other person name -->

<?php $title = ($profileComplete) ? 'Edit Profile' : 'Create Profile'; include("../templates/top.html");?>
<div class="wrapper">
  <div class="pb-2 mt-4 mb-4 border-bottom">  
    <h2>Profile Details</h2>
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
      <h6>Height: <?php echo $height.'cm' ?></h6>
    </div>
    <div>
      <h6>Smokes? <?php echo $smokes ?></h6>
    </div>
    <div>
      <h6>Description: <?php echo $description ?></h6>
    </div>
  </div>
  <div class="m-3">  
    <p><a href="edit-profile.php" class="btn btn-secondary btn-sm">Edit</a></p>
  </div>
</div>
<div class="wrapper">
  <div class="pb-2 mt-4 mb-4 border-bottom">  
    <h2>Preference Details</h2>
  </div>
  <div class="pb-2 m-3 mb-4 border-bottom">
    <div>
      <h6>Preferred Gender: <?php echo $prefGender ?></h6>
    </div>
    <div>
      <h6>Preferred Age: <?php echo $prefAgeMin.' - '.$prefAgeMax.' years old' ?></h6>
    </div>
    <div>
      <h6>Preferred County: <?php echo $prefCountyName ?></h6>
    </div>
    <div>
      <h6>Preferred Interest: <?php echo $prefInterestName ?></h6>
    </div>
    <div>
      <h6>Preferred Smokes? <?php echo $prefSmokes ?></h6>
    </div>
    <div>
      <h6>Preferred Height: <?php echo $prefHeightMin.' - '.$prefHeightMax.'cm' ?></h6>
    </div>
  </div>
  <div class="m-3">  
    <p><a href="edit-preferences.php" class="btn btn-secondary btn-sm">Edit</a></p>
  </div>
</div> 
<?php include("../templates/bottom.html");?>

