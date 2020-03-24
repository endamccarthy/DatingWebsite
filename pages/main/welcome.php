<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: ../login/login.php");
  exit;
}
?>
 
<?php $title = 'Welcome'; include("../templates/top.html");?>
  <div style="text-align: center">
    <div class="page-header">
      <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["email"]); ?></b>. Welcome to our site.</h1>
    </div>
  </div>
<?php include("../templates/bottom.html");?>