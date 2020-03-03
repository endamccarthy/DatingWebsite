<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>
 
<?php $title = 'Welcome'; include("templates/top.html");?>
    <div style="text-align: center">
        <div class="page-header">
            <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["email"]); ?></b>. Welcome to our site.</h1>
        </div>
        <p>
            <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
            <a href="../scripts/logout.php" class="btn btn-danger">Sign Out of Your Account</a>
        </p>
    </div>
<?php include("templates/bottom.html");?>