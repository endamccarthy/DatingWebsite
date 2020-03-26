<?php

// Initialize the session
session_start();

// Include script to check if user is logged in and profile is complete
require_once "../../scripts/logged-in.php";
// Include config file
require_once "../../scripts/config.php";

$prefGender = $prefSmokes = "";
$prefInterestsID = $prefAgeMin = $prefAgeMax = $prefHeightMin = $prefHeightMax = $prefCountyID = 0;
$userID = $_SESSION["userID"];

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

// Get list of interests for dropdown menu
$sql = "SELECT interestID, interestName FROM interestList;";
if($stmt = mysqli_prepare($link, $sql)) {
  if(mysqli_stmt_execute($stmt)) {
    mysqli_stmt_store_result($stmt);
    if(mysqli_stmt_num_rows($stmt) >= 1) { 
      $interests = array();
      mysqli_stmt_bind_result($stmt, $interestIDTemp, $interestNameTemp);
      while (mysqli_stmt_fetch($stmt)) {
        $interests[$interestIDTemp] = $interestNameTemp;
      } 
    } 
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
  mysqli_stmt_close($stmt);
}

// get existing values from preferences table
$sql = "SELECT prefGender, prefAgeMin, prefAgeMax, prefCountyID, prefInterestID, prefSmokes , prefHeightMin, prefHeightMax FROM preferences WHERE userID = $userID;";
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
    $prefCountyID = $_POST["prefCountyID"];
    $sql .= "UPDATE preferences SET prefCountyID = $prefCountyID WHERE userID = $userID;";
  }
  // Check if preferred interest was changed
  if($_POST["prefInterestID"] != $prefInterestID) {
    $prefInterestID = $_POST["prefInterestID"];
    $sql .= "UPDATE preferences SET prefInterestID = $prefInterestID WHERE userID = $userID;";
  }
  // Check if preferred smokes was changed
  if($_POST["prefSmokes"] != $prefSmokes) {
    $prefSmokes = $_POST["prefSmokes"];
    $sql .= "UPDATE preferences SET prefSmokes = '$prefSmokes' WHERE userID = $userID;";
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
  if(mysqli_multi_query($link, $sql)) {
    header("location: ../main/suggestions.php");
  }
  // Close connection
  mysqli_close($link);
}
?>


<?php $title = 'Edit Preferences'; include("../templates/top.html");?>
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
          <option value=NULL>None</option>
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
          <option value=NULL>None</option>
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
          <input type="radio" name="prefSmokes" class="form-control custom-control-input" id="prefSmokesNeither" value=NULL <?php echo ($prefSmokes == NULL) ? 'checked' : ''; ?>>
          <label class="custom-control-label" for="prefSmokesYes">Don't Mind</label>
        </div>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Minimum Height? (cm)</label>
        <select name="prefHeightMin" class="form-control form-control-sm">
          <option value=100>None</option>
          <?php 
            for ($cm = 100; $cm <= 250; $cm++) {
              echo ($cm == $prefHeightMin) ? '<option selected' : '<option';
              echo ' value='.$cm.'>'.$cm.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <label class="control-label">Maximum Height? (cm)</label>
        <select name="prefHeightMax" class="form-control form-control-sm">
          <option value=250>None</option>
          <?php 
            for ($cm = 100; $cm <= 250; $cm++) {
              echo ($cm == $prefHeightMax) ? '<option selected' : '<option';
              echo ' value='.$cm.'>'.$cm.'</option>';
            }
          ?>
        </select>
      </div>

      <div class="mb-4 form-group">
        <input type="submit" class="btn btn-primary" value="Save">
        <input type="reset" class="btn btn-default" value="Reset">
      </div>

    </form>
  </div>
<?php include("../templates/bottom.html");?>
