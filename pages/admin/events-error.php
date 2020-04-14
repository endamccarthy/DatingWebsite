<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
?>

<?php $title = 'Admin | Events | countyID Error'; include("../templates/top.html");?>
    <div class="wrapper wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h3>Events | countyID Not Found Error</h3>
                    </div>
                    <div class="alert alert-danger" role="alert">
						<input type="hidden" name="eventID" value="<?php echo trim($_GET["eventID"]); ?>"/>
                        <p><a class="alert-link">The countyID entered for this Event was not Found</a><br>Please go
						<a class="alert-link">Back</a> and try again.</p>
						<a href="events-home.php" class="btn btn-danger">Back</a>
                    </div>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>