<?php

// Initialize the session
session_start();

// Include script to check if user is logged in and profile is complete
require_once "../../scripts/logged-in.php";
// Include config file
require_once "../../scripts/config.php";

// Define variables
$gender = $prefGender = $dateOfBirth = $smokes = $description = "";
$height = $countyID = 0;
$userID = $_SESSION["userID"];
$profileComplete = $_SESSION["profileComplete"];

// Get list of counties for dropdown menu
$sql = "SELECT countyID, countyName FROM countyList;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) >= 1) { 
      $counties = array();
      mysqli_stmt_bind_result($stmt, $countyIDTemp, $countyNameTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $counties[$countyIDTemp] = $countyNameTemp;
      } 
    } 
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

// check if user has completed their profile, show existing values if so
if($profileComplete) {
  $sql = "SELECT description, gender, dateOfBirth, countyID, smokes, height FROM profile WHERE userID = $userID;";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $descriptionTemp, $genderTemp, $dateOfBirthTemp, $countyIDTemp, $smokesTemp, $heightTemp);
        while (mysqli_stmt_fetch($stmt)) {
          $description = $descriptionTemp;
          $gender = $genderTemp;
          $dateOfBirth = $dateOfBirthTemp;
          $countyID = $countyIDTemp;
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
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
  // If profile is being created...
  if(!$profileComplete) {
    // Save values passed from form to local variables 
    $gender = $_POST["gender"];
    $prefGender = $_POST["prefGender"];
    $dateOfBirth = $_POST["dateOfBirth"];
    $countyID = $_POST["countyID"];
    $smokes = $_POST["smokes"];
    $height = $_POST["height"];
    if($_POST["description"]) {
      $description = trim($_POST["description"]);
    }
    // Add new entry into profile table
    $sql = "INSERT INTO profile (userID, description, gender, dateOfBirth, countyID, photo, smokes, height) 
    VALUES ($userID, '$description', '$gender', '$dateOfBirth', $countyID, 'photoXX.jpg', '$smokes', $height);";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
      mysqli_stmt_close($stmt);
    }
    // Add new entry into preferences table
    $sql = "INSERT INTO preferences (userID, prefGender) 
    VALUES ('$userID', '$prefGender');";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
      mysqli_stmt_close($stmt);
    }
    $_SESSION["profileComplete"] = true;
    header("location: ../main/suggestions.php");
  }
  // Else, if profile is being edited...
  else {
    $sql = "";
    // Check if description was changed
    if($_POST["description"] != $description) {
      $description = $_POST["description"];
      $sql .= "UPDATE profile SET description = '$description' WHERE userID = $userID;";
    }
    // Check if gender was changed
    if($_POST["gender"] != $gender) {
      $gender = $_POST["gender"];
      $sql .= "UPDATE profile SET gender = '$gender' WHERE userID = $userID;";
    }
    // Check if date of birth was changed
    if($_POST["dateOfBirth"] != $dateOfBirth) {
      $dateOfBirth = $_POST["dateOfBirth"];
      $sql .= "UPDATE profile SET dateOfBirth = '$dateOfBirth' WHERE userID = $userID;";
    }
    // Check if county ID was changed
    if($_POST["countyID"] != $countyID) {
      $countyID = $_POST["countyID"];
      $sql .= "UPDATE profile SET countyID = $countyID WHERE userID = $userID;";
    }
    // Check if smokes was changed
    if($_POST["smokes"] != $smokes) {
      $smokes = $_POST["smokes"];
      $sql .= "UPDATE profile SET smokes = '$smokes' WHERE userID = $userID;";
    }
    // Check if height was changed
    if($_POST["height"] != $height) {
      $height = $_POST["height"];
      $sql .= "UPDATE profile SET height = $height WHERE userID = $userID;";
    }
    // Execute multi query sql statement
    if(mysqli_multi_query($link, $sql)) {
      header("location: ../main/suggestions.php");
    }
  }
  // Close connection
  mysqli_close($link);
}
?>


<?php $title = ($profileComplete) ? 'Edit Profile' : 'Create Profile'; include("../templates/top.html");?>
  <div class="wrapper">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <h2><?php echo ($profileComplete) ? 'Edit Profile' : 'Create Profile'; ?></h2>

      <div class="mb-4 form-row required">
        <div class="col-md-6">
          <label class="control-label">I am...</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="gender" class="form-control custom-control-input" id="genderMale" value="male" <?php echo ($gender == 'male') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="genderMale">Male</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="gender" class="form-control custom-control-input" id="genderFemale" value="female" <?php echo ($gender == 'female') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="genderFemale">Female</label>
        </div>
      </div>

      <?php
      // Only show preferred gender if new profile is being created, it will be in the edit PREFERENCES form otherwise
      if(!$profileComplete) {
        echo '<div class="mb-4 form-row required">';
        echo '<div class="col-md-6">';
        echo '<label class="control-label">I am seeking...</label>';
        echo '</div>';
        echo '<div class="col-md-2 custom-control custom-radio custom-control-inline">';
        echo '<input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderMale" value="male" required>';
        echo '<label class="custom-control-label" for="prefGenderMale">Male</label>';
        echo '</div>';
        echo '<div class="col-md-2 custom-control custom-radio custom-control-inline">';
        echo '<input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderFemale" value="female" required>';
        echo '<label class="custom-control-label" for="prefGenderFemale">Female</label>';
        echo '</div>';
        echo '</div>';
      }
      ?>

      <div class="mb-4 form-group required">
        <label class="control-label">Date of Birth (YYYY-MM-DD)</label>
        <input type="date" name="dateOfBirth" class="form-control form-control-sm" value="<?php echo $dateOfBirth; ?>" required>
      </div>

      <div class="mb-4 form-group required">
        <label class="control-label">County</label>
        <select name="countyID" class="form-control form-control-sm" required>
          <option selected disabled>Choose County...</option>
          <?php 
            if(isset($counties)) {
              foreach($counties as $id => $name){
                echo ($id == $countyID) ? '<option selected' : '<option';
                echo ' value='.$id.'>'.$name.'</option>';
              }
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-row required">
        <div class="col-md-6">
          <label class="control-label">Are you a smoker?</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="smokes" class="form-control custom-control-input" id="smokesNo" value="non-smoker" <?php echo ($smokes == 'non-smoker') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="smokesNo">No</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="smokes" class="form-control custom-control-input" id="smokesYes" value="smoker" <?php echo ($smokes == 'smoker') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="smokesYes">Yes</label>
        </div>
      </div>

      <div class="mb-4 form-group required">
        <label class="control-label">Height (cm)</label>
        <select name="height" class="form-control form-control-sm" required>
          <option selected disabled>Choose Height...</option>
          <?php 
            for ($cm = 100; $cm <= 250; $cm++) {
              echo ($cm == $height) ? '<option selected' : '<option';
              echo ' value='.$cm.'>'.$cm.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">About me (max 100 characters):</label>
        <textarea name="description" class="form-control form-control-sm" rows="4" maxlength="100"><?php echo $description; ?></textarea>
      </div>

      <div class="mb-4 form-group">
        <input type="submit" class="btn btn-primary" value="Save">
        <input type="reset" class="btn btn-default" value="Reset">
      </div>

    </form>
  </div>    
<?php include("../templates/bottom.html");?>

