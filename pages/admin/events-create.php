<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";

// Define variables and initialize with empty values

$CountyIDs = array();
$userID = $_SESSION["userID"];

$eventPhotoAddress = $eventCountyID = $eventName = $eventDate = $eventWebsite = $eventPhoto = "" ;
$eventCounty_err = $eventName_err = $eventDate_err = $eventWebsite_err =  $eventPhoto_err = "";
$eventID;

// YYYY-MM-DD
$pattern = "/^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|".
"(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)".
"\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])".
"|((16|[2468][048]|[3579][26])00))\-02\-29))$/";

// Get list of counties for dropdown menu
$counties = getCountiesList($link);


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {

    // If a event photo is posted check if it's valid
    if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
      $file_size = $_FILES['image']['size'];
      $exploded = explode('.',$_FILES['image']['name']);
      $file_ext = strtolower(end($exploded));
      $extensions = array("jpeg","jpg","png");
      if(in_array($file_ext, $extensions) === false) {
        $eventPhoto_err .= "Extension not allowed, please choose a JPEG or PNG file.";
      }
      else if($file_size > 2097152) {
        $eventPhoto_err .= "File size must be less than 2 MB";
      }
      else {
        $sql = "SELECT max(eventID) FROM events;";
        if($stmt = mysqli_prepare($link, $sql)) {
          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1) {
              mysqli_stmt_bind_result($stmt, $maxEventID);
              while (mysqli_stmt_fetch($stmt)) {
                $eventID = $maxEventID;
              }
              $eventPhotoAddress = '../../images/event-photos/event'.$eventID.'-photo.jpg';
            }
          } 
          else {
            echo "Oops! Something went wrong. Please try again later.";
          }
        }
      }
    }
 
    // If event date is invalid...
  if((!preg_match($pattern, $_POST["eventDate"]))) {
    $eventDate_err = "Incorrect Event Date Format";
  }
  else if(!isset($_POST["eventCountyID"]) || $_POST["eventCountyID"] < 1 || $_POST["eventCountyID"] > 32 || $_POST["eventCountyID"] == '') {
    $eventCounty_err = "Incorrect County Format";
  }
  // Else if photo is valid...
  else if($eventPhoto_err == "") {

      if(isset($_POST["eventCountyID"])) {
        $eventCountyID = trim($_POST["eventCountyID"]);
      }
      if(isset($_POST["eventName"])) {
        $eventName = $_POST["eventName"];
      }
      if(isset($_POST["eventDate"])) {
        $eventDate = trim($_POST["eventDate"]);
      }
      if(isset($_POST["eventWebsite"])) {
        $eventWebsite = $_POST["eventWebsite"];
      }

  
      // Add new entry into events table
      $sql = "INSERT INTO events (eventCountyID, eventName, eventDate, eventWebsite, eventPhoto) 
      VALUES ($eventCountyID, '$eventName', '$eventDate', '$eventWebsite', '$eventPhotoAddress');";
      if($stmt = mysqli_prepare($link, $sql)) {
        if(!mysqli_stmt_execute($stmt)) {
          echo "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }

      // If photo is posted, save it to images/event-photos folder
      if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        move_uploaded_file($_FILES['image']['tmp_name'], $eventPhotoAddress);
      }
      header("location: ../admin/events-home.php");
  }
}
// Close connection
mysqli_close($link);
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
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

                        <div class="mb-4 form-group required">
                          <label class="control-label">County</label>
                          <select name="eventCountyID" class="form-control form-control-sm <?php echo (!empty($eventCounty_err)) ? 'is-invalid' : ''; ?>" required>
                            <option selected disabled>Choose County...</option>
                            <?php 
                              if(isset($counties)) {
                                foreach($counties as $id => $name){
                                  echo '<option value='.$id.'>'.$name.'</option>';
                                }
                              }
                            ?>
                          </select>
                          <span class="invalid-feedback"><?php echo $eventCounty_err; ?></span>
                        </div>
                        <div class="form-group required <?php echo (!empty($eventName_err)) ? 'has-error' : ''; ?>">
                            <label class="control-label">Event Name</label>
                            <input type="text" name="eventName" class="form-control" value="<?php echo $eventName; ?>" required>
                        </div>
                        <div class="form-group required <?php echo (!empty($eventDate_err)) ? 'has-error' : ''; ?>">
                            <label class="control-label">Event Date</label>
                            <input type="date" onload="getDate()" name="eventDate" class="form-control" value="<?php echo $eventDate; ?>" required>
                        </div>
                        <div class="form-group required <?php echo (!empty($eventWebsite_err)) ? 'has-error' : ''; ?>">
                            <label class="control-label">Event Website</label>
                            <input type="text" name="eventWebsite" class="form-control" value="<?php echo $eventWebsite; ?>" required>
                        </div>                       

                        <div class="form-group">
                          <label class="control-label">Upload Photo:</label><br>
                          <input type="file" name="image" class="<?php echo (!empty($photoErr)) ? 'is-invalid' : ''; ?>">
                          <span class="invalid-feedback"><?php echo $photoErr; ?></span>
                        </div>

                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="events-home.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>