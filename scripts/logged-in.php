<?php
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: ../login/login.php");
  exit;
}

// Function to check if string ends with certain value
function endsWith($stringToCheck, $checkAgainst) {
  $length = strlen($checkAgainst);
  if ($length == 0) {
    return true;
  }
  return (substr($stringToCheck, -$length) === $checkAgainst);
}

// Check if the current page is not the edit profile page
$str = $_SERVER['REQUEST_URI'];
if (!endsWith($str, 'edit-profile.php')) {
  // Check if user has completed their profile, redirect them to edit profile page if not
  if($_SESSION["profileComplete"] !== true) {
    header("location: edit-profile.php");
    exit;
  }
}

?>