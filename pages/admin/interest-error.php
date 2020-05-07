<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
?>

<?php $title = 'Admin | Interest | Create/Update Error'; include("../templates/top.html");?>
    <div class="wrapper wrapper admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Interest | Error</h2>
                    </div>
                    <div class="alert alert-danger" role="alert">
						<input type="hidden" name="interestID" value="<?php echo trim($_GET["interestID"]); ?>"/>
                        <p><a class="alert-link">Attempting to duplicate an entry to the Database</a><br>Please go
						<a class="alert-link">Back</a> and try again.</p>
						<a href="interestList-home.php" class="btn btn-danger">Back</a>
                    </div>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>