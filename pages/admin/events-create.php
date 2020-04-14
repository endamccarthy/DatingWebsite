<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables and initialize with empty values
$CountyID = "";
$eventCountyID = $eventName = $eventDate = $eventWebsite = "";
$eventCountyID_err = $eventName_err = $eventDate_err = $eventWebsite_err = "";


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
  if(trim($_POST["eventCountyID"])) {
    $eventCountyID = trim($_POST["eventCountyID"]);
  }

  if(trim($_POST["eventName"])) {
    $eventName = trim($_POST["eventName"]);
  }

  if(trim($_POST["eventDate"])) {
    $eventDate = trim($_POST["eventDate"]);
  }

  if(trim($_POST["eventWebsite"])) {
    $eventWebsite = trim($_POST["eventWebsite"]);
  }
  
  	// Check countyList table for valid countyID
	$sql = "SELECT  FROM countylist WHERE CountyID = '$eventCountyID';";
	
	echo "$sql<br>";
	
	if($stmt = mysqli_prepare($link, $sql)) {
			echo "$sql<br>";
		if(mysqli_stmt_execute($stmt)) {
			mysqli_stmt_store_result($stmt);
			if(mysqli_stmt_num_rows($stmt) == 1) {
				//FOUND countyID ...... Proceed
				$eventCountyID = trim($_POST["eventCountyID"]);
				$eventName = trim($_POST["eventName"]);
				$eventDate = trim($_POST["eventDate"]);
				$eventWebsite = trim($_POST["eventWebsite"]);
				echo "$eventCountyID<br>";
				echo "$eventName<br>";
				echo "$eventDate<br>";
				echo "$eventWebsite<br>";
				
				//$countyName_err = "This eventCountyID already exists";
				//echo $countyName_err;
			} 
			else {
				// NOT FOUND countyID ...... Retry 
				$eventCountyID_err = "This countyID does not exist, please re-try";
				//$eventCountyID = trim($_POST["eventCountyID"]);
				//$eventName = trim($_POST["eventName"]);
				//$eventDate = trim($_POST["eventDate"]);
				//$eventWebsite = trim($_POST["eventWebsite"]);
								echo "$eventCountyID<br>";
				echo "$eventName<br>";
				echo "$eventDate<br>";
				echo "$eventWebsite<br>";
				
			}
		} 
		else {
			echo "Oops! Something went wrong  ONE. Please try again later.";
		}
		// Close statement
		mysqli_stmt_close($stmt);
	}
  
  // Check input errors before inserting in database
  
	echo "$eventCountyID<br>";
	echo "$eventName<br>";
	echo "$eventDate<br>";
	echo "$eventWebsite<br>";
 
  if(empty($eventCountyID_err) && empty($eventName_err) && empty($eventDate_err) && empty($eventWebsite_err)) {
    // Add entry to events table
    $sql = "INSERT INTO events (eventCountyID, eventName, eventDate, eventWebsite) VALUES (?, ?, ?, ?);";
	
		echo "$sql<br>";

    if($stmt = mysqli_prepare($link, $sql)) {
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ssss", $paramEventCountyID, $paramEventName, $paramEventDate, $paramEventWebsite);
      // Set parameters
      $paramEventCountyID = $eventCountyID;
      $paramEventName = $eventName;
      $paramEventDate = $eventDate;
      $paramEventWebsite = $eventWebsite;
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)){
              // Records created successfully. Redirect to landing page
              header("location: events-home.php");
              exit();
          } else{
			  	// eventCountyID does not exist as a countyID i countylist
				// URL doesn't contain userID parameter. Redirect to error page
				header("location: events-error.php");
				exit();
				echo "Something went wrong. Please try again later.<br>";
				echo "The eventCountyID entere is not a valid coountyID, please check and retry";
          }
      }
       
      // Close statement
      mysqli_stmt_close($stmt);
  }
  // Close connection
  mysqli_close($link);
}
?>
 
<?php $title = 'Admin | Event | Create'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Create Event</h2>
                    </div>
                    <p>Please fill this form and submit to add an Event record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
					    <div class="form-group <?php echo (!empty($eventCountyID_err)) ? 'has-error' : ''; ?>">
                            <label>Event CountyID</label>
                            <input type="text" name="eventCountyID" class="form-control" value="<?php echo $eventCountyID; ?>" required>
                        </div>
                        <div class="form-group <?php echo (!empty($eventName_err)) ? 'has-error' : ''; ?>">
                            <label>Event Name</label>
                            <input type="text" name="eventName" class="form-control" value="<?php echo $eventName; ?>" required>
                        </div>
                        <div class="form-group <?php echo (!empty($eventDate_err)) ? 'has-error' : ''; ?>">
                            <label>Event Date</label>
                            <input type="date" onload="getDate()" name="eventDate" class="form-control" value="<?php echo $eventDate; ?>" required>
                        </div>
                        <div class="form-group <?php echo (!empty($eventWebsite_err)) ? 'has-error' : ''; ?>">
                            <label>Event Website</label>
                            <input type="text" name="eventWebsite" class="form-control" value="<?php echo $eventWebsite; ?>" required>
                        </div>                     
                        
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="events-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>