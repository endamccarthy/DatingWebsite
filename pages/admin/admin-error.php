<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
?>

<?php $title = 'Admin | Error'; include("../templates/top.html");?>
    <div class="wrapper wrapper-wide">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Invalid Request</h2>
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <p>Sorry, you've made an invalid request. Please <a href="admin-home.php" class="alert-link">go back</a> and try again.</p>
                    </div>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>