<?php

// Get the current page address
$currentPage = $_SERVER['REQUEST_URI'];

// Function to check if a string ends with certain value
function endsWith($stringToCheck, $checkAgainst) {
  $length = strlen($checkAgainst);
  if ($length == 0) {
    return true;
  }
  return (substr($stringToCheck, -$length) === $checkAgainst);
}

// Function to get list of counties for dropdown menu
function getCountiesList($link) {
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
    if (isset($counties)) {
      return $counties;
    }
  }
}

// Function to get list of interests for dropdown menu
function getInterestsList($link) {
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
    if (isset($interests)) {
      return $interests;
    }
  }
}


// If user is not already on the login or register page...
if (!endsWith($currentPage, 'login.php') && !endsWith($currentPage, 'register.php')) {
  // And they are logged out...
  if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
    // Redirect them to login page
    header("location: ../login/login.php");
    exit;
  }
  // If user is not already on the edit profile page...
  if (!endsWith($currentPage, 'edit-profile.php')) {
    // And their profile is not complete...
    if(isset($_SESSION["profileComplete"]) && $_SESSION["profileComplete"] !== true) {
      // Redirect them to edit profile page
      header("location: edit-profile.php");
      exit;
    }
  }
}
// Else if user is on the login or register page...
else {
  // And they are already logged in...
  if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
    // Redirect to landing page
    header("location: ../main/welcome.php");
    exit;
  }
}


?>