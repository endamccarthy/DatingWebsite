<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables and initialize with empty values
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
  
  // Check input errors before inserting in database
  if(empty($eventCountyID_err) && empty($eventName_err) && empty($eventDate_err) && empty($eventWebsite_err)) {
    // Add entry to events table
    $sql = "INSERT INTO events (eventCountyID, eventName, eventDate, eventWebsite) VALUES (?, ?, ?, ?)";

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
              header("location: eventsList-home.php");
              exit();
          } else{
              echo "Something went wrong. Please try again later.";
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
                    <p>Please fill this form and submit to add a County record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					
					    <div class="form-group <?php echo (!empty($eventCountyID_err)) ? 'has-error' : ''; ?>">
                            <label>Event CountyID</label>
                            <input type="text" name="eventCountyID" class="form-control" value="<?php echo $eventCountyID; ?>" required>
                        </div>
                        <div class="form-group <?php echo (!empty($eventName_err)) ? 'has-error' : ''; ?>">
                            <label>Event Name</label>
                            <input type="text" name="eventCountyID" class="form-control" value="<?php echo $eventName; ?>" required>
                        </div>
                        <div class="form-group <?php echo (!empty($eventDate_err)) ? 'has-error' : ''; ?>">
                            <label>Event Date</label>
                            <input type="text" name="eventCountyID" class="form-control" value="<?php echo $eventDate; ?>" required>
                        </div>
                        <div class="form-group <?php echo (!empty($eventWebsite_err)) ? 'has-error' : ''; ?>">
                            <label>Event Website</label>
                            <input type="text" name="eventCountyID" class="form-control" value="<?php echo $eventWebsite; ?>" required>
                        </div>                     
                        
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="events-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>