<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables
$searchText = $searchResults = "";
$countyFilters = $interestFilters = "''";
$userID = $_SESSION["userID"];

// Get list of counties for dropdown menu
$counties = getCountiesList($link);
// Get list of interests for dropdown menu
$interests = getInterestsList($link);

// Show un-filtered profiles by default
$searchResults = getSearchResultsString($link, $userID, $searchText, $countyFilters, $interestFilters);

// If clear filters is clicked...
if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["clearFilters"])) {
  $searchText = $searchResults = "";
  $countyFilters = $interestFilters = "''";
  $searchResults = getSearchResultsString($link, $userID, $searchText, $countyFilters, $interestFilters);
}

// If search is clicked...
if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["search"])) {

  $_SESSION["search"] = $currentPage;

  // Save search text to variable if entered
  if(isset($_GET["searchText"])) {
    $searchText = trim($_GET["searchText"]);
  }
  
  // If multiple county IDs are included in filters, save as comma separated string list e.g.('1', '2')
  if(isset($_GET["countyFilters"])) {
    $prefix = $countyFilters = "";
    foreach ($_GET["countyFilters"] as $filter)
    {
      $countyFilters .= $prefix."'".$filter."'";
      $prefix = ', ';
    }
    $countyFilters = rtrim($countyFilters, ', ');
  }

  // If multiple interest IDs are included in filters, save as comma separated string list e.g.('1', '2')
  if(isset($_GET["interestFilters"])) {
    $prefix = $interestFilters = "";
    foreach ($_GET["interestFilters"] as $filter)
    {
      $interestFilters .= $prefix."'".$filter."'";
      $prefix = ', ';
    }
    $interestFilters = rtrim($interestFilters, ', ');
  }

  // Execute sql statement and save results to a string
  $searchResults = getSearchResultsString($link, $userID, $searchText, $countyFilters, $interestFilters);

  // This will be used to show a 'Back To Search Results' button if a profile is clicked on
  $_SESSION["searchApplied"] = true;
}
// Close connection
mysqli_close($link);
?>

<?php $title = 'Search'; include("../templates/top.html"); ?>
<div class="container">
  <div class="container-item">
    <h3 class="pb-2 mt-4 mb-4 border-bottom">Search All Available Profiles</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">

      <div class="form-group">
        <input type="text" name="searchText" class="form-control" placeholder="Filter by name..." value="<?php echo $searchText; ?>">
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
        <input name="clearFilters" type="submit" class="btn btn-default" value="Clear Filters">
      </div>

    </form>
  </div>
  <div class="container-item">
    <h3 class="pb-2 mt-4 mb-4 border-bottom">
      <?php echo ($searchText == "" && $countyFilters == "''" && $interestFilters == "''") ? 'Showing All Profiles' : 'Showing Filtered Results' ?>
    </h3>
    <div>
      <?php echo $searchResults; ?>
    </div>
  </div>
</div>
<?php include("../templates/bottom.html");?>

<script>
// Allow for multiple county selections
$(document).ready(function(){
  $('#countyFilters').multiselect({
    nonSelectedText: 'Filter By County',
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
    nonSelectedText: 'Filter By Interest',
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

