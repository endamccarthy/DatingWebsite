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

// Function to get user preferences
function getUserPreferences($link, $userID) {
  $sql = "SELECT prefGender, prefAgeMin, prefAgeMax, prefCountyID, prefInterestID, prefSmokes , prefHeightMin, prefHeightMax FROM preferences WHERE userID = $userID;";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) == 1) {
        $preferences = array();
        mysqli_stmt_bind_result($stmt, $prefGenderTemp, $prefAgeMinTemp, $prefAgeMaxTemp, $prefCountyIDTemp, $prefInterestIDTemp, $prefSmokesTemp, $prefHeightMinTemp, $prefHeightMaxTemp);
        while (mysqli_stmt_fetch($stmt)) {
          $preferences['prefGender'] = $prefGenderTemp;
          $preferences['prefAgeMin'] = $prefAgeMinTemp;
          $preferences['prefAgeMax'] = $prefAgeMaxTemp;
          $preferences['prefCountyID'] = ($prefCountyIDTemp) ? $prefCountyIDTemp : 0;
          $preferences['prefInterestID'] = ($prefInterestIDTemp) ? $prefInterestIDTemp : 0;
          $preferences['prefSmokes'] = ($prefSmokesTemp) ? $prefSmokesTemp : "";
          $preferences['prefHeightMin'] = $prefHeightMinTemp;
          $preferences['prefHeightMax'] = $prefHeightMaxTemp;
        }
      }
    } 
    else {
      echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
    if (isset($preferences)) {
      return $preferences;
    }
  }
}

// Function to get a single entry name from a table given the entry ID
function getEntryNameGivenID($link, $table, $entryName, $entryID, $givenID) {
  if ($givenID >= 1) {
    $sql = "SELECT $entryName FROM $table WHERE $entryID = $givenID;";
    if($stmt = mysqli_prepare($link, $sql)) {
      if(mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if(mysqli_stmt_num_rows($stmt) == 1) {
          mysqli_stmt_bind_result($stmt, $entryNameTemp);
          while (mysqli_stmt_fetch($stmt)) {
            $entryNameResult = $entryNameTemp;
          }
        }
      } 
      else {
        echo "Oops! Something went wrong. Please try again later.";
      }
      mysqli_stmt_close($stmt);
      if (isset($entryNameResult)) {
        return $entryNameResult;
      }
    }
  }
}

// Function to get results from suggestions, matches, waiting for you
function getProfileResultsString($link, $sql) {
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) >= 1) {
        $resultsString = "";
        mysqli_stmt_bind_result($stmt, $userIDTemp, $firstNameTemp, $lastNameTemp, $statusTemp, $dateOfBirthTemp, $photoTemp, $countyNameTemp);
        while (mysqli_stmt_fetch($stmt)) {

          $dateOfBirthTemp = explode("-", $dateOfBirthTemp);
          $age = (date("md", date("U", mktime(0, 0, 0, $dateOfBirthTemp[2], $dateOfBirthTemp[1], $dateOfBirthTemp[0]))) > date("md")
            ? ((date("Y") - $dateOfBirthTemp[0]) - 1)
            : (date("Y") - $dateOfBirthTemp[0]));
            
          $resultsString .= '<div class="profile-card"><a href="profile.php?userID='.$userIDTemp.'"><img class="profile-card-img" src="'.$photoTemp.'" alt="Profile Photo"></a>';
          $resultsString .= '<div class="profile-card-text"><h6>'.$firstNameTemp.' '.$lastNameTemp.'<br>'.$countyNameTemp.'<br>'.$age.'</h6></div></div>';
        } 
      }
      else {
        $resultsString = "<p>Sorry, no results!</p>";
      }
    } 
    else {
      echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
    return $resultsString;
  }
}

// Function to get results from search
function getSearchResultsString($link, $userID, $searchText, $countyFilters, $interestFilters) {
  $prefGender = getEntryNameGivenID($link, 'preferences', 'prefGender', 'userID', $userID);
  $sql = "SELECT DISTINCT user.userID, firstName, lastName, status, dateOfBirth, photo, countyName FROM user JOIN profile JOIN countyList ON 
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
    status = 'active'
    AND
    gender = (
      SELECT prefGender FROM preferences WHERE userID = $userID
    )
    AND
    userID IN (
      SELECT userID FROM preferences WHERE prefGender != '$prefGender'
    )
    AND
    userID IN (
      SELECT userID FROM user WHERE CONCAT(firstName, ' ', lastName) LIKE '%$searchText%'
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
  return getProfileResultsString($link, $sql);
}

// Function to check if a user pair exists in either pending or rejections tables
function checkIfUserPairExists($link, $table, $fieldOne, $fieldTwo, $userOne, $userTwo) {
  $exists = false;
  $sql = "SELECT * FROM $table WHERE $fieldOne = $userTwo AND $fieldTwo = $userOne";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) >= 1) {
        $exists = true;
      }
    } 
    mysqli_stmt_close($stmt);
  }
  return $exists;
}

// Function to check if a user pair exists in matches tables
function checkIfUserPairExistsMatches($link, $table, $fieldOne, $fieldTwo, $userOne, $userTwo) {
  $exists = false;
  $sql = "SELECT * FROM $table WHERE ($fieldOne = $userTwo AND $fieldTwo = $userOne) OR ($fieldOne = $userOne AND $fieldTwo = $userTwo)";
  if($stmt = mysqli_prepare($link, $sql)) {
    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) >= 1) {
        $exists = true;
      }
    } 
    mysqli_stmt_close($stmt);
  }
  return $exists;
}

// If user is not already on the landing page...
if (!endsWith($currentPage, 'landing-page.php')) {
  // And they are logged out...
  if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
    // Redirect them to landing page
    header("location: ../login/landing-page.php");
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
// Else if user is on the landing page...
else {
  // And they are already logged in...
  if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
    // Redirect to home page
    header("location: ../main/suggestions.php");
    exit;
  }
}

// Check if the 'Back To Search Results' button should be displayed
if(isset($_SESSION["search"])) {
  if((strpos($currentPage, '/profile.php') === false) || (endsWith($currentPage, '/profile.php'))) {
    if(strpos($currentPage, 'action.php') === false) {
      unset($_SESSION["search"]);
    }
  }
}

?>