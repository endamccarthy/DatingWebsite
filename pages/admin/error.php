<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: ../login/login.php");
  exit;
}
?>

<?php $title = 'Admin | Error'; include("../templates/top.html");?>
    <div class="wrapper wrapper-admin-wide">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom">  
                        <h2>Invalid Request</h2>
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <p>Sorry, you've made an invalid request. Please <a href="index.php" class="alert-link">go back</a> and try again.</p>
                    </div>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>