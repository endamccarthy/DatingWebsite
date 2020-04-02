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
$userOne = $_SESSION["userID"];

// Check user wants to upgrade to premium
if(isset($_GET["action"]) && $_GET["action"] == 'upgrade') {
  $sql = "UPDATE user SET accessLevel = 'premium' WHERE userID = $userOne";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(!mysqli_stmt_execute($stmt)) {
      echo "Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
  }
  header("location: ../pages/main/profile.php");
  exit;
}
else {
  echo "Something went wrong. Please try again later.";
}

// Check if user ID (of other person) has been passed in
if(isset($_GET["userID"]) && !empty(trim($_GET["userID"]))) {
  $userTwo = $_GET["userID"];

  if(isset($_GET["action"])) {
    // Check to see if the other person has already liked you
    if (checkIfUserPairExists($link, 'pending', 'pendingUserOne', 'pendingUserTwo', $userOne, $userTwo)) { $relationshipStatus = "theyLikeYou"; }

    // If the action is a like...
    if($_GET["action"] == 'like') {
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
    }

    // If the action is a reject...
    if($_GET["action"] == 'reject') {
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
    }

    // If the action is a report...
    if($_GET["action"] == 'report') {
      // Check to see if you have already rejected the other person
      if (checkIfUserPairExists($link, 'rejections', 'rejectionsUserOne', 'rejectionsUserTwo', $userTwo, $userOne)) { $relationshipStatus = "youRejectedThem"; }

      // If they have already liked you, add entry to rejections and remove from pending
      if($relationshipStatus == "theyLikeYou") {
        $sql = "INSERT INTO rejections (rejectionsUserOne, rejectionsUserTwo) VALUES ($userOne, $userTwo);
        DELETE FROM pending WHERE pendingUserOne = $userTwo AND pendingUserTwo = $userOne;";
        if(!mysqli_multi_query($link, $sql)) {
          echo "Something went wrong. Please try again later.";
        }
      }
      // Else if they have not liked you, just add entry to rejections
      else if ($relationshipStatus == "none") {
        $sql = "INSERT INTO rejections (rejectionsUserOne, rejectionsUserTwo) VALUES ($userOne, $userTwo);";
        if($stmt = mysqli_prepare($link, $sql)) {
          if(!mysqli_stmt_execute($stmt)) {
            echo "Something went wrong. Please try again later.";
          }
          mysqli_stmt_close($stmt);
        }
      }
      // TODO: send notice to admin to suspend the reported users account....
    }

  }
  header("location: ../pages/main/profile.php?userID=".$userTwo);
  exit;
}
else {
  echo "Something went wrong. Please try again later.";
}

?>