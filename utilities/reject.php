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

  // Check to see if you have already matched the other person
  if (checkIfUserPairExistsMatches($link, 'matches', 'matchesUserOne', 'matchesUserTwo', $userOne, $userTwo)) { $relationshipStatus = "match"; }

  // Insert new entry into rejections
  $sql = "INSERT INTO rejections (rejectionsUserOne, rejectionsUserTwo) VALUES ($userOne, $userTwo);";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(!mysqli_stmt_execute($stmt)) {
      echo "Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
  }

  // If other user had already liked you, remove entry from pending
  if ($relationshipStatus == "theyLikeYou") {
    $sql = "DELETE FROM pending WHERE pendingUserOne = $userTwo AND pendingUserTwo = $userOne;";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(!mysqli_stmt_execute($stmt)) {
        echo "Something went wrong. Please try again later.";
      }
      mysqli_stmt_close($stmt);
    }
  }

  // If you have already matched other user, remove entry from matches
  if($relationshipStatus == "match") {
    $sql = "DELETE FROM matches WHERE (matchesUserOne = $userTwo AND matchesUserTwo = $userOne) OR (matchesUserOne = $userOne AND matchesUserTwo = $userTwo);";
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