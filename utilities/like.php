<?php

// Initialize the session
session_start();

// Include utility file
require_once "utility.php";
// Include config file
require_once "config.php";

// Define variables
$userOne = $userTwo = 0;
$relationshipStatus = "none";

// Check if user ID (of other person) has been passed in
if(isset($_GET["userID"]) && !empty(trim($_GET["userID"]))) {
  $userOne = $_SESSION["userID"];
  $userTwo = $_GET["userID"];

  // Check to see if the other person has already liked you
  if (checkIfUserPairExists($link, 'pending', 'pendingUserOne', 'pendingUserTwo', $userOne, $userTwo)) { $relationshipStatus = "theyLikeYou"; }

  // If they have already liked you, add entry to matches and remove from pending
  if($relationshipStatus == "theyLikeYou") {
    $sql = "INSERT INTO matches (matchesUserOne, matchesUserTwo) VALUES ($userTwo, $userOne);
    DELETE FROM pending WHERE pendingUserOne = $userTwo AND pendingUserTwo = $userOne;";
    if(!mysqli_multi_query($link, $sql)) {
      echo "Something went wrong. Please try again later.";
    }
  }
  // Else if they have not liked you yet, add entry to pending
  else if ($relationshipStatus == "none") {
    $sql = "INSERT INTO pending (pendingUserOne, pendingUserTwo) VALUES ($userOne, $userTwo);";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
      mysqli_stmt_close($stmt);
    }
  }
  header("location: ../pages/main/profile.php?userID=".$userTwo);
  exit;
}
else {
  echo "Something went wrong. Please try again later.";
}

?>