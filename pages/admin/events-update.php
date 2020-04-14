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
if(isset($_POST["eventID"]) && !empty($_POST["eventID"])){
    // Get hidden input value
    $eventID = $_POST["eventID"];
	$eventCountyID = $_POST["eventCountyID"];
	$eventName = $_POST["eventName"];
	$eventDate = $_POST["eventDate"];
	$eventID = $_POST["eventID"];
    
	// Validate eventCountyID 
	if(trim($_POST["eventCountyID"])) {
		$eventCountyID = trim($_POST["eventCountyID"]);
	}
	
	// Validate eventName 
	if(trim($_POST["eventName"])) {
		$eventName = trim($_POST["eventName"]);
	}
   	// Validate eventDate 
	if(trim($_POST["eventDate"])) {
		$eventDate = trim($_POST["eventDate"]);
	}
	// Validate eventWebsite 
	if(trim($_POST["eventWebsite"])) {
		$eventWebsite = trim($_POST["eventWebsite"]);
	}
   
    // Check input errors before updating database
    if(empty($eventCountyID_err) && empty($eventName_err) && empty($eventDate_err) && empty($eventWebsite_err)){
        // Prepare an update statement
        $sql = "UPDATE events SET eventCountyID=?, eventName=?, eventDate=?, eventWebsite=? WHERE eventID=?";

		 
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssi", $paramEventCountyID, $paramEventName, $paramEventDate, $paramEventWebsite, $paramEventID);

            // Set parameters
            $paramEventCountyID = $eventCountyID;
			$paramEventName = $EventName;
            $paramEventDate = $eventDate;
			$paramEventWebsite = $eventWebsite;
            $paramEventID = $eventID;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: events-home.php");
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
	
} else {
    // Check existence of eventID parameter before processing further
    if(isset($_GET["eventID"]) && !empty(trim($_GET["eventID"]))){
        // Get URL parameter
        $eventID =  trim($_GET["eventID"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM events WHERE eventID = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $paramEventID);
            
            // Set parameters
            $paramEventID = $EventID;
			$paramEventCountyID = $eventCountyID;
			$paramEventName = $EventName;
            $paramEventDate = $eventDate;
			$paramEventWebsite = $eventWebsite;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
					$EventID = $row["EventID"];
					$eventCountyID = $row["eventWebsite"];
					$EventName = $row["EventName"];
					$eventDate = $row["eventDate"];
					$eventWebsite = $row["eventWebsite"];
                } else{
                    // URL doesn't contain valid eventID. Redirect to error page
					echo "This ERROR ONE <br>"; 
                    header("location: events-error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
		
    }  else{
        // URL doesn't contain eventID parameter. Redirect to error page
		echo "This ERROR TWO <br>"; 
        header("location: events-error.php");
        exit();
    }
}
?>
 
 <?php $title = 'Admin | Interest | Update'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-narrow">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Update County</h2>
                    </div>
                    <p>Please edit the Event and submit to update the record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
						<div class="form-group <?php echo (!empty($eventCountyID_err)) ? 'has-error' : ''; ?>">
                            <label>Event County ID</label>
                            <input type="text" name="eventCountyID" class="form-control" value="<?php echo $eventCountyID; ?>" required>
                        </div>						
						<div class="form-group <?php echo (!empty($EventName_err)) ? 'has-error' : ''; ?>">
                            <label>Event Name</label>
                            <input type="text" name="EventName" class="form-control" value="<?php echo $EventName; ?>" required>
                        </div>
						<div class="form-group <?php echo (!empty($EventDate_err)) ? 'has-error' : ''; ?>">
                            <label>Event Date</label>
                            <input type="text" name="EventDate" class="form-control" value="<?php echo $EventDate; ?>" required>
                        </div>						
 						<div class="form-group <?php echo (!empty($EventWebsite_err)) ? 'has-error' : ''; ?>">
                            <label>Event Website</label>
                            <input type="text" name="EventWebsite" class="form-control" value="<?php echo $EventWebsite; ?>" required>
                        </div>                      
                       
						<input type="hidden" name="eventID" value="<?php echo $eventID; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="events-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>