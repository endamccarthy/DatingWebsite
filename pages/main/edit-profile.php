<?php

// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$gender = $prefGender = $dateOfBirth = $smokes = $description = $dateOfBirthErr ="";
$height = $countyID = $interestID = 0;
$interestIDs;
$userID = $_SESSION["userID"];
$profileComplete = $_SESSION["profileComplete"];
// YYYY-MM-DD
$pattern = "/^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|".
"(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)".
"\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])".
"|((16|[2468][048]|[3579][26])00))\-02\-29))$/";

// Get list of counties for dropdown menu
$counties = getCountiesList($link);
// Get list of interests for dropdown menu
$interests = getInterestsList($link);

// check if user has completed their profile, show existing values if so
if($profileComplete) {
  $sql = "SELECT description, gender, dateOfBirth, countyID, smokes, height FROM profile WHERE userID = $userID;";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $descriptionTemp, $genderTemp, $dateOfBirthTemp, $countyIDTemp, $smokesTemp, $heightTemp);
        while (mysqli_stmt_fetch($stmt)) {
          if($descriptionTemp) {$description = $descriptionTemp;};
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
  $sql = "SELECT interestID FROM interests WHERE userID = $userID;";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) >= 1) {
        mysqli_stmt_bind_result($stmt, $interestIDTemp);
        while (mysqli_stmt_fetch($stmt)) {
          $interestIDs[] = $interestIDTemp;
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
  if((!preg_match($pattern, $_POST["dateOfBirth"]))) {
    $dateOfBirthErr = "Incorrect Date of Birth Format";
  }
  else {
    // If profile is being created...
    if(!$profileComplete) {
      // Save values passed from form to local variables 
      $gender = $_POST["gender"];
      $prefGender = $_POST["prefGender"];
      $dateOfBirth = $_POST["dateOfBirth"];
      $countyID = $_POST["countyID"];
      $smokes = $_POST["smokes"];
      $height = $_POST["height"];
      if(isset($_POST["description"])) {
        $description = trim($_POST["description"]);
      }
      if(isset($_POST["interestIDs"])) {
        $interestIDs = $_POST["interestIDs"];
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
      $sql = "INSERT INTO preferences (userID, prefGender) VALUES ($userID, '$prefGender');";
      if($stmt = mysqli_prepare($link, $sql)) {
        if(!mysqli_stmt_execute($stmt)) {
          echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }

      // Add new entries into interests table
      $sql = "";
      foreach ($interestIDs as $key => $value) {
        $sql .= "INSERT INTO interests (userID, interestID) VALUES ($userID, $value);";
      }
      if(!mysqli_multi_query($link, $sql)) {
        echo "Something went wrong. Please try again later.";
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
      // Check if interests were changed
      if($_POST["interestIDs"] != $interestIDs) {
        $interestIDs = $_POST["interestIDs"];
        $sql .= "DELETE FROM interests WHERE userID = $userID;";
        foreach ($interestIDs as $row) {
          $sql .= "INSERT INTO interests (userID, interestID) VALUES ($userID, $row);";
        }
      }
      // Execute multi query sql statement
      if(mysqli_multi_query($link, $sql)) {
        header("location: ../main/profile.php");
      }
    }
  }
}
// Close connection
mysqli_close($link);
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
      // Only show preferred gender option if new profile is being created, it will be in the edit PREFERENCES form otherwise
      if(!$profileComplete) {
        echo '<div class="mb-4 form-row required">';
        echo '<div class="col-md-6">';
        echo '<label class="control-label">I am seeking...</label>';
        echo '</div>';
        echo '<div class="col-md-2 custom-control custom-radio custom-control-inline">';
        echo '<input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderMale" value="male" ';
        echo ($prefGender == 'male') ? 'checked ' : '';
        echo 'required>';
        echo '<label class="custom-control-label" for="prefGenderMale">Male</label>';
        echo '</div>';
        echo '<div class="col-md-2 custom-control custom-radio custom-control-inline">';
        echo '<input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderFemale" value="female" ';
        echo ($prefGender == 'female') ? 'checked ' : '';
        echo 'required>';
        echo '<label class="custom-control-label" for="prefGenderFemale">Female</label>';
        echo '</div>';
        echo '</div>';
      }
      ?>

      <div class="mb-4 form-group required">
        <label class="control-label">Date of Birth</label>
        <input type="date" name="dateOfBirth" class="form-control form-control-sm <?php echo (!empty($dateOfBirthErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $dateOfBirth; ?>" placeholder="YYYY-MM-DD" required>
        <span class="invalid-feedback"><?php echo $dateOfBirthErr; ?></span>
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
            for ($cm = 120; $cm <= 230; $cm++) {
              echo ($cm == $height) ? '<option selected' : '<option';
              echo ' value='.$cm.'>'.$cm.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group" onclick="checkMaxInterests()">
        <label class="control-label">Interests</label>
        <select name="interestIDs[]" class="form-control form-control-sm" id="interestIDs" multiple>
          <?php 
            if(isset($interests)) {
              foreach($interests as $id => $name){
                echo (in_array($id, $interestIDs)) ? '<option selected' : '<option';
                echo ' value='.$id.'>'.$name.'</option>';
              }
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


<script type="text/javascript">
// Limit interests to 3
function checkMaxInterests() {
  var selectedOptions = $('#interestIDs option:selected');
  if (selectedOptions.length >= 3) {
    // Disable all other checkboxes.
    var nonSelectedOptions = $('#interestIDs option').filter(function() {
      return !$(this).is(':selected');
    });
    nonSelectedOptions.each(function() {
      var input = $('input[value="' + $(this).val() + '"]');
      input.prop('disabled', true);
      input.parent('li').addClass('disabled');
    });
  }
  else {
    // Enable all checkboxes.
    $('#interestIDs option').each(function() {
      var input = $('input[value="' + $(this).val() + '"]');
      input.prop('disabled', false);
      input.parent('li').addClass('disabled');
    });
  }
};

// Allow for multiple interest selections
$(document).ready(function(){
  $('#interestIDs').multiselect({
    nonSelectedText: 'Choose Interests (3 maximum)',
    enableFiltering: true,
    enableCaseInsensitiveFiltering: true,
    buttonWidth:'100%',
    buttonClass: 'btn btn-light',
    enableHTML: false,
    maxHeight: 200,
    includeSelectAllOption: false,
    onChange: function(option, checked) {
      checkMaxInterests();
    }
  });
});
</script>