<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
  header("location: login.php");
  exit;
}
?>
 
<?php $title = 'Matches'; include("templates/top.html");?>
  <div style="text-align: center">
    <h2>Matches - To Do...</h2>
  </div>
<?php include("templates/bottom.html");?>