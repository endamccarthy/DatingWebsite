<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: login.php");
  exit;
}

// Include config file
require_once "../scripts/config.php";
 
// Define variables
$county = $interest = $searchResults = "";
$countyFilters = $interestFilters = "''";

// Get list of counties for dropdown menu
$sql = "SELECT countyName FROM countyList";
if($stmt = mysqli_prepare($link, $sql)) {
  // Attempt to execute the prepared statement
  if(mysqli_stmt_execute($stmt)) {
    // Store result
    mysqli_stmt_store_result($stmt);
    // Check if counties are found
    if(mysqli_stmt_num_rows($stmt) >= 1) { 
      $counties = array();
      mysqli_stmt_bind_result($stmt, $countyName);
      while (mysqli_stmt_fetch($stmt)) {
        $counties[] = ['countyName' => $countyName];
      } 
    } 
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
}

// Get list of interests for dropdown menu
$sql = "SELECT interestName FROM interestList";
if($stmt = mysqli_prepare($link, $sql)) {
  // Attempt to execute the prepared statement
  if(mysqli_stmt_execute($stmt)) {
    // Store result
    mysqli_stmt_store_result($stmt);
    // Check if interests are found
    if(mysqli_stmt_num_rows($stmt) >= 1) { 
      $interests = array();
      mysqli_stmt_bind_result($stmt, $interestName);
      while (mysqli_stmt_fetch($stmt)) {
        $interests[] = ['interestName' => $interestName];
      } 
    } 
  } 
  else {
    echo "Oops! Something went wrong. Please try again later.";
  }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
  
  // Validate county
  if(isset($_POST["countyFilters"])) {
    $prefix = $countyFilters = "";
    foreach ($_POST["countyFilters"] as $filter)
    {
      $countyFilters .= $prefix."'".$filter."'";
      $prefix = ', ';
    }
    $countyFilters = rtrim($countyFilters, ', ');
  }

  // Validate interest
  if(isset($_POST["interestFilters"])) {
    $prefix = $interestFilters = "";
    foreach ($_POST["interestFilters"] as $filter)
    {
      $interestFilters .= $prefix."'".$filter."'";
      $prefix = ', ';
    }
    $interestFilters = rtrim($interestFilters, ', ');
  }

  $userID = $_SESSION["userID"];

  // Prepare a select statement
  $sql = "SELECT DISTINCT firstName, lastName, countyName FROM user JOIN profile JOIN countyList ON 
  user.userID = profile.userID AND profile.countyID = countyList.countyID WHERE user.userID IN (
    SELECT userID FROM profile WHERE userID != $userID
    AND
    userID NOT IN (
      SELECT pendingUserTwo FROM pending WHERE pendingUserOne = $userID
      UNION ALL
      SELECT matchesUserTwo FROM matches WHERE matchesUserOne = $userID
      UNION ALL
      SELECT matchesUserOne FROM matches WHERE matchesUserTwo = $userID
      UNION ALL
      SELECT rejectionsUserTwo FROM rejections WHERE rejectionsUserOne = $userID
      UNION ALL
      SELECT rejectionsUserOne FROM rejections WHERE rejectionsUserTwo = $userID
    )
    AND
    gender = (
      SELECT prefGender FROM preferences WHERE userID = $userID
    ) 
    AND 
    countyID IN (
      SELECT countyID FROM countyList WHERE countyName IN ($countyFilters)
      UNION ALL 
      SELECT countyID FROM countyList WHERE NOT EXISTS (
        SELECT countyID FROM countyList WHERE countyName IN ($countyFilters) AND countyID IS NOT NULL
      )
    )
    AND
    userID IN (
      SELECT userID FROM interests WHERE interestID IN (
        SELECT interestID FROM interestList WHERE interestName IN ($interestFilters)
      )
      UNION ALL
      SELECT userID FROM user WHERE NOT EXISTS (
        SELECT interestID FROM interestList WHERE interestName IN ($interestFilters) AND interestID IS NOT NULL
      )
    )
  );";

  if($stmt = mysqli_prepare($link, $sql)) {
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)) {
      // Store result
      mysqli_stmt_store_result($stmt);
      // Check if search results are found
      if(mysqli_stmt_num_rows($stmt) >= 1) { 
        mysqli_stmt_bind_result($stmt, $firstName, $lastName, $countyName);
        while (mysqli_stmt_fetch($stmt)) {
          $searchResults .= '<p><a href="#">'.$firstName.' '.$lastName.'</a><br>'.$countyName.'</p>';
        } 
      } 
      else {
        $searchResults = "<p>No search results</p>";
      }
    } 
    else {
      echo "Oops! Something went wrong. Please try again later.";
    }
    // Close statement
    mysqli_stmt_close($stmt);
  }

  // Close connection
  mysqli_close($link);
}

?>
 
<?php $title = 'Search'; include("templates/top.html");?>
  <div style="text-align: center">
    <h2>Search - To Do...</h2>
  </div>
  <div class="wrapper">
    <h3>Enter Search Criteria</h3>
    
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
        <select name="countyFilters[]" class="form-control" id="countyFilters" multiple>
          <?php 
            if(isset($counties)) {
              $tempCounties = explode("'", $countyFilters);
              foreach($counties as $row){
                echo (in_array($row['countyName'], $tempCounties)) ? '<option selected>' : '<option>';
                echo $row['countyName'].'</option>';
              }
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <select name="interestFilters[]" class="form-control" id="interestFilters" multiple>
          <?php 
            if(isset($interests)) {
              $tempInterests = explode("'", $interestFilters);
              foreach($interests as $row){
                echo (in_array($row['interestName'], $tempInterests)) ? '<option selected>' : '<option>';
                echo $row['interestName'].'</option>';
              }
            }
          ?>
        </select>
      </div>
      <div class="form-group">
        <input name="search" type="submit" class="btn btn-secondary" value="Search">
      </div>
    </form>
    <h2>Search Results</h2>
    <div>
      <?php echo $searchResults; ?>
    </div>
  </div>
<?php include("templates/bottom.html");?>


<script>
$(document).ready(function(){
 $('#countyFilters').multiselect({
  nonSelectedText: 'Filter County',
  enableFiltering: true,
  enableCaseInsensitiveFiltering: true,
  buttonWidth:'100%',
  buttonClass: 'btn btn-light',
  enableHTML: false,
  maxHeight: 200,
  includeSelectAllOption: true
 });
});

$(document).ready(function(){
 $('#interestFilters').multiselect({
  nonSelectedText: 'Filter Interest',
  enableFiltering: true,
  enableCaseInsensitiveFiltering: true,
  buttonWidth:'100%',
  buttonClass: 'btn btn-light',
  enableHTML: false,
  maxHeight: 200,
  includeSelectAllOption: true
 });
});
</script>