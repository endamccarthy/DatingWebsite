<?php
// Initialize the session
session_start();
 
// Include script to check if user is logged in and profile is complete
require_once "../../scripts/logged-in.php";
// Include config file
require_once "../../scripts/config.php";
 
// Define variables
$searchText = $county = $interest = $searchResults = "";
$countyFilters = $interestFilters = "''";
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

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {

  // Save search text to variable if entered
  if(isset($_POST["searchText"])) {
    $searchText = trim($_POST["searchText"]);
  }
  
  // If multiple county IDs are included in filters, save as comma separated string list e.g.('1', '2')
  if(isset($_POST["countyFilters"])) {
    $prefix = $countyFilters = "";
    foreach ($_POST["countyFilters"] as $filter)
    {
      $countyFilters .= $prefix."'".$filter."'";
      $prefix = ', ';
    }
    $countyFilters = rtrim($countyFilters, ', ');
  }

  // If multiple interest IDs are included in filters, save as comma separated string list e.g.('1', '2')
  if(isset($_POST["interestFilters"])) {
    $prefix = $interestFilters = "";
    foreach ($_POST["interestFilters"] as $filter)
    {
      $interestFilters .= $prefix."'".$filter."'";
      $prefix = ', ';
    }
    $interestFilters = rtrim($interestFilters, ', ');
  }

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
    userID IN (
      SELECT userID FROM user WHERE CONCAT(firstName, ' ', lastName) LIKE '%$searchText%'
      UNION ALL
      SELECT userID FROM user WHERE '$searchText' = ''
    )
    AND
    gender = (
      SELECT prefGender FROM preferences WHERE userID = $userID
    ) 
    AND 
    countyID IN (
      SELECT countyID FROM countyList WHERE countyID IN ($countyFilters)
      UNION ALL 
      SELECT countyID FROM countyList WHERE NOT EXISTS (
        SELECT countyID FROM countyList WHERE countyID IN ($countyFilters) AND countyID IS NOT NULL
      )
    )
    AND
    userID IN (
      SELECT userID FROM interests WHERE interestID IN (
        SELECT interestID FROM interestList WHERE interestID IN ($interestFilters)
      )
      UNION ALL
      SELECT userID FROM user WHERE NOT EXISTS (
        SELECT interestID FROM interestList WHERE interestID IN ($interestFilters) AND interestID IS NOT NULL
      )
    )
  );";

  // Execute sql statement and save results to a string
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) >= 1) { 
        mysqli_stmt_bind_result($stmt, $firstNameTemp, $lastNameTemp, $countyNameTemp);
        while (mysqli_stmt_fetch($stmt)) {
          $searchResults .= '<p><a href="#">'.$firstNameTemp.' '.$lastNameTemp.'</a><br>'.$countyNameTemp.'</p>';
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

<?php $title = 'Search'; include("../templates/top.html");?>
  <div style="text-align: center">
    <h2>Search - To Do...</h2>
  </div>
  <div class="wrapper">
    <h3>Enter Search Criteria</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

      <div class="form-group">
        <label>Search Users by Name</label>
        <input type="text" name="searchText" class="form-control" value="<?php echo $searchText; ?>">
      </div> 

      <div class="form-group">
        <select name="countyFilters[]" class="form-control" id="countyFilters" multiple>
          <?php 
            if(isset($counties)) {
              $tempCounties = explode("'", $countyFilters);
              foreach($counties as $id => $name){
                echo (in_array($id, $tempCounties)) ? '<option selected' : '<option';
                echo ' value='.$id.'>'.$name.'</option>';
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
              foreach($interests as $id => $name){
                echo (in_array($id, $tempInterests)) ? '<option selected' : '<option';
                echo ' value='.$id.'>'.$name.'</option>';
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

<?php include("../templates/bottom.html");?>

<script>
// Allow for multiple county selections
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

// Allow for multiple interest selections
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

