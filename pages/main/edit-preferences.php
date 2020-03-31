<?php

// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables
$userID = $_SESSION["userID"];

// Get list of counties for dropdown menu
$counties = getCountiesList($link);
// Get list of interests for dropdown menu
$interests = getInterestsList($link);
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

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
  $sql = "";
  // Check if preferred gender was changed
  if($_POST["prefGender"] != $prefGender) {
    $prefGender = $_POST["prefGender"];
    $sql .= "UPDATE preferences SET prefGender = '$prefGender' WHERE userID = $userID;";
  }
  // Check if preferred minimum age was changed
  if($_POST["prefAgeMin"] != $prefAgeMin) {
    if($_POST["prefAgeMin"] > $prefAgeMax) {
      $prefAgeMin = $prefAgeMax;
    }
    else {
      $prefAgeMin = $_POST["prefAgeMin"];
    }
    $sql .= "UPDATE preferences SET prefAgeMin = $prefAgeMin WHERE userID = $userID;";
  }
  // Check if preferred max age was changed
  if($_POST["prefAgeMax"] != $prefAgeMax) {
    if($_POST["prefAgeMax"] < $prefAgeMin) {
      $prefAgeMax = $prefAgeMin;
    }
    else {
      $prefAgeMax = $_POST["prefAgeMax"];
    }
    $sql .= "UPDATE preferences SET prefAgeMax = $prefAgeMax WHERE userID = $userID;";
  }
  // Check if preferred county was changed
  if($_POST["prefCountyID"] != $prefCountyID) {
    if($_POST["prefCountyID"] == 0) {
      $sql .= "UPDATE preferences SET prefCountyID = NULL WHERE userID = $userID;";
    }
    else {
      $prefCountyID = $_POST["prefCountyID"];
      $sql .= "UPDATE preferences SET prefCountyID = $prefCountyID WHERE userID = $userID;";
    }
  }
  // Check if preferred interest was changed
  if($_POST["prefInterestID"] != $prefInterestID) {
    if($_POST["prefInterestID"] == 0) {
      $sql .= "UPDATE preferences SET prefInterestID = NULL WHERE userID = $userID;";
    }
    else {
      $prefInterestID = $_POST["prefInterestID"];
      $sql .= "UPDATE preferences SET prefInterestID = $prefInterestID WHERE userID = $userID;";
    }
  }
  // Check if preferred smokes was changed
  if($_POST["prefSmokes"] != $prefSmokes) {
    if($_POST["prefSmokes"] == "") {
      $sql .= "UPDATE preferences SET prefSmokes = NULL WHERE userID = $userID;";
    }
    else {
      $prefSmokes = $_POST["prefSmokes"];
      $sql .= "UPDATE preferences SET prefSmokes = '$prefSmokes' WHERE userID = $userID;";
    }
  }
  // Check if preferred minimum height was changed
  if($_POST["prefHeightMin"] != $prefHeightMin) {
    if($_POST["prefHeightMin"] > $prefHeightMax) {
      $prefHeightMin = $prefHeightMax;
    }
    else {
      $prefHeightMin = $_POST["prefHeightMin"];
    }
    $sql .= "UPDATE preferences SET prefHeightMin = $prefHeightMin WHERE userID = $userID;";
  }
  // Check if preferred max height was changed
  if($_POST["prefHeightMax"] != $prefHeightMax) {
    if($_POST["prefHeightMax"] < $prefHeightMin) {
      $prefHeightMax = $prefHeightMin;
    }
    else {
      $prefHeightMax = $_POST["prefHeightMax"];
    }
    $sql .= "UPDATE preferences SET prefHeightMax = $prefHeightMax WHERE userID = $userID;";
  }
  // Execute multi query sql statement
  if(!mysqli_multi_query($link, $sql)) {
    echo "Something went wrong. Please try again later.";
  }
  header("location: ../main/profile.php");
}
// Close connection
mysqli_close($link);
?>


<?php $title = 'Edit Preferences'; include("../templates/top.html"); ?>
  <div class="wrapper">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <h2>Edit Preferences</h2>

      <div class="mb-4 form-row required">
        <div class="col-md-6">
          <label class="control-label">I am seeking...</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderMale" value="male" <?php echo ($prefGender == 'male') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="prefGenderMale">Male</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderFemale" value="female" <?php echo ($prefGender == 'female') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="prefGenderFemale">Female</label>
        </div>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Preferred County?</label>
        <select name="prefCountyID" class="form-control form-control-sm">
          <option value=0>None</option>
          <?php 
            if(isset($counties)) {
              foreach($counties as $id => $name){
                echo ($id == $prefCountyID) ? '<option selected' : '<option';
                echo ' value='.$id.'>'.$name.'</option>';
              }
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Preferred Interest?</label>
        <select name="prefInterestID" class="form-control form-control-sm">
          <option value=0>None</option>
          <?php 
            if(isset($interests)) {
              foreach($interests as $id => $name){
                echo ($id == $prefInterestID) ? '<option selected' : '<option';
                echo ' value='.$id.'>'.$name.'</option>';
              }
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Minimum Age?</label>
        <select name="prefAgeMin" class="form-control form-control-sm">
          <option value=18>None</option>
          <?php 
            for ($i = 18; $i <= 100; $i++) {
              echo ($i == $prefAgeMin) ? '<option selected' : '<option';
              echo ' value='.$i.'>'.$i.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Maximum Age?</label>
        <select name="prefAgeMax" class="form-control form-control-sm">
          <option value=100>None</option>
          <?php 
            for ($i = 18; $i <= 100; $i++) {
              echo ($i == $prefAgeMax) ? '<option selected' : '<option';
              echo ' value='.$i.'>'.$i.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-row">
        <div class="col-md-3">
          <label class="control-label">Smoker?</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="prefSmokes" class="form-control custom-control-input" id="prefSmokesNo" value="non-smoker" <?php echo ($prefSmokes == 'non-smoker') ? 'checked' : ''; ?>>
          <label class="custom-control-label" for="prefSmokesNo">No</label>
        </div>
        <div class="col-md-2 custom-control custom-radio custom-control-inline">
          <input type="radio" name="prefSmokes" class="form-control custom-control-input" id="prefSmokesYes" value="smoker" <?php echo ($prefSmokes == 'smoker') ? 'checked' : ''; ?>>
          <label class="custom-control-label" for="prefSmokesYes">Yes</label>
        </div>
        <div class="col-md-3 custom-control custom-radio custom-control-inline">
          <input type="radio" name="prefSmokes" class="form-control custom-control-input" id="prefSmokesNeither" value="" <?php echo ($prefSmokes == NULL || $prefSmokes == "neither") ? 'checked' : ''; ?>>
          <label class="custom-control-label" for="prefSmokesNeither">Don't Mind</label>
        </div>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Minimum Height? (cm)</label>
        <select name="prefHeightMin" class="form-control form-control-sm">
          <option value=120>None</option>
          <?php 
            for ($cm = 120; $cm <= 230; $cm++) {
              echo ($cm == $prefHeightMin) ? '<option selected' : '<option';
              echo ' value='.$cm.'>'.$cm.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Maximum Height? (cm)</label>
        <select name="prefHeightMax" class="form-control form-control-sm">
          <option value=230>None</option>
          <?php 
            for ($cm = 120; $cm <= 230; $cm++) {
              echo ($cm == $prefHeightMax) ? '<option selected' : '<option';
              echo ' value='.$cm.'>'.$cm.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <input type="submit" class="btn btn-primary" value="Save">
        <a href="javascript:history.back()" class="btn btn-default">Cancel</a>
      </div>

    </form>
  </div>
<?php include("../templates/bottom.html");?>
