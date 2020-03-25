<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: ../login/login.php");
  exit;
}

?>


<?php $title = 'Welcome'; include("../templates/top.html");?>
  <div style="text-align: center">
    <div class="page-header">
      <h1>Please complete profile!! (to do)</h1>
    </div>
    <a href="../../scripts/logout.php" class="btn btn-warning">Logout</a>
  </div>
<?php include("../templates/bottom.html");?>