<?php

// Initialize the session
session_start();

// Include utility file
require_once "utility.php";
// Include config file
require_once "config.php";

// Define variables
$userOne = $userTwo = 0;

// Check if user ID (of other person) has been passed in
if(isset($_GET["userID"]) && !empty(trim($_GET["userID"]))) {
  $userOne = $_SESSION["userID"];
  $userTwo = $_GET["userID"];

  if(isset($_GET["action"])) {

    // If the action is an unlike...
    if($_GET["action"] == 'unlike') {
      $sql = "DELETE FROM pending WHERE pendingUserOne = $userOne AND pendingUserTwo = $userTwo;";
      if($stmt = mysqli_prepare($link, $sql)) {
        if(!mysqli_stmt_execute($stmt)) {
          echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }
    }

    // If the action is an unreject...
    if($_GET["action"] == 'unreject') {
      $sql = "DELETE FROM rejections WHERE rejectionsUserOne = $userOne AND rejectionsUserTwo = $userTwo;";
      if($stmt = mysqli_prepare($link, $sql)) {
        if(!mysqli_stmt_execute($stmt)) {
          echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }
    }

    // If the action is an unmatch...
    if($_GET["action"] == 'unmatch') {
      $sql = "DELETE FROM matches WHERE (matchesUserOne = $userOne AND matchesUserTwo = $userTwo) OR (matchesUserOne = $userTwo AND matchesUserTwo = $userOne);";
      if($stmt = mysqli_prepare($link, $sql)) {
        if(!mysqli_stmt_execute($stmt)) {
          echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }
    }

    // If the action is an unreport...
    if($_GET["action"] == 'unreport') {
      $sql = "DELETE FROM reported WHERE reportedUserOne = $userOne AND reportedUserTwo = $userTwo;";
      if($stmt = mysqli_prepare($link, $sql)) {
        if(!mysqli_stmt_execute($stmt)) {
          echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }
    }
  }
  header("location: ../pages/main/profile.php?userID=".$userTwo);
  exit;
}
else {
  echo "Something went wrong. Please try again later.";
}

?>