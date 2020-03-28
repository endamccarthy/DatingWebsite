<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";

?>
 
<?php $title = 'Welcome'; include("../templates/top.html");?>
  <div style="text-align: center">
    <div class="page-header">
      <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["email"]); ?></b>. Welcome to our site.</h1>
    </div>
  </div>
<?php include("../templates/bottom.html");?>