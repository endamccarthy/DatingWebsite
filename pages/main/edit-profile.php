<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: ../login/login.php");
  exit;
}

// Include config file
require_once "../../scripts/config.php";

$gender = $prefGender = $description = "";
$userID = $_SESSION["userID"];

// check if user has completed their profile, show existing values if so
if($_SESSION["profileComplete"] == true) {
  // get existing values from profile table
  $sql = "SELECT gender FROM profile WHERE userID = $userID;";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $gender);
        while (mysqli_stmt_fetch($stmt)) {
          $gender = $gender;
        }
      }
    } 
    else {
      echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
  }
  // get existing values from preferences table
  $sql = "SELECT prefGender FROM preferences WHERE userID = $userID;";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) == 1) {
        mysqli_stmt_bind_result($stmt, $prefGender);
        while (mysqli_stmt_fetch($stmt)) {
          $prefGender = $prefGender;
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
  // If profile being edited is new...
  if($_SESSION["profileComplete"] == false) {
    $gender = $_POST["gender"];
    $prefGender = $_POST["prefGender"];
    // Validate description
    if($_POST["description"]) {
      $description = trim($_POST["description"]);
    }
    // Add new entry to profile table
    $sql = "INSERT INTO profile (userID, description, gender, dateOfBirth, countyID, photo, smokes, height) 
    VALUES ('$userID', '$description', '$gender', '1990-01-01', 4, 'photoXX.jpg', 'non-smoker', 170);";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
      mysqli_stmt_close($stmt);
    }
    // Add new entry to preferences table
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
  // Else if profile being edited already exists...
  else {
    $sql = "";
    // Check if gender was changed
    if($_POST["gender"] != $gender) {
      $gender = $_POST["gender"];
      $sql .= "UPDATE profile SET gender = '$gender' WHERE userID = $userID;";
    }
    // Check if preferred gender was changed
    if($_POST["prefGender"] != $prefGender) {
      $prefGender = $_POST["prefGender"];
      $sql .= "UPDATE preferences SET prefGender = '$prefGender' WHERE userID = $userID;";
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


<?php $title = 'Edit Profile'; include("../templates/top.html");?>
  <div class="wrapper">
    <h2>Edit Profile</h2>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

      <div class="form-row">
        <div class="col-md-5 mb-3">
          <label>I am...</label>
        </div>
        <div class="col-md-2 mb-3 custom-control custom-radio custom-control-inline">
          <input type="radio" name="gender" class="form-control custom-control-input" id="genderMale" value="male" <?php echo ($gender == 'male') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="genderMale">Male</label>
        </div>
        <div class="col-md-2 mb-3 custom-control custom-radio custom-control-inline">
          <input type="radio" name="gender" class="form-control custom-control-input" id="genderFemale" value="female" <?php echo ($gender == 'female') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="genderFemale">Female</label>
        </div>
      </div>

      <div class="form-row">
        <div class="col-md-5 mb-3">
          <label>I am seeking...</label>
        </div>
        <div class="col-md-2 mb-3 custom-control custom-radio custom-control-inline">
          <input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderMale" value="male" <?php echo ($prefGender == 'male') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="prefGenderMale">Male</label>
        </div>
        <div class="col-md-2 mb-3 custom-control custom-radio custom-control-inline">
          <input type="radio" name="prefGender" class="form-control custom-control-input" id="prefGenderFemale" value="female" <?php echo ($prefGender == 'female') ? 'checked' : ''; ?> required>
          <label class="custom-control-label" for="prefGenderFemale">Female</label>
        </div>
      </div>

      <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Save">
        <input type="reset" class="btn btn-default" value="Reset">
      </div>

    </form>

  </div>    
<?php include("../templates/bottom.html");?>

