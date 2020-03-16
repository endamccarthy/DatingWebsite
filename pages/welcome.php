<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "../scripts/config.php";
 
// Prepare a select statement
$sql = "SELECT firstName, lastName FROM user WHERE userID IN 
            (SELECT userID FROM profile WHERE userID  != ? AND 
                gender = (SELECT prefGender FROM preferences WHERE userID = ?) AND 
                TIMESTAMPDIFF(YEAR, dateOfBirth, NOW()) BETWEEN 
                    (SELECT prefAgeMin FROM preferences WHERE userID = ?) AND 
                    (SELECT prefAgeMax FROM preferences WHERE userID = ?) AND 
                countyID IN 
                    (SELECT prefCountyID FROM preferences WHERE userID = ? UNION ALL 
                        SELECT countyID FROM countyList WHERE NOT EXISTS (
                            SELECT prefCountyID FROM preferences WHERE userID = ? AND prefCountyID IS NOT NULL)))";


if($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "iiiiii", $param_userID, $param_userID, $param_userID, $param_userID, $param_userID, $param_userID);
    // Set parameters
    $param_userID = $_SESSION["userID"];
    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);
        // Check if suggestions are found
        if(mysqli_stmt_num_rows($stmt) >= 1) { 
            $results = array();
            mysqli_stmt_bind_result($stmt, $firstName, $lastName);
            while (mysqli_stmt_fetch($stmt)) {
                $results[] = ['firstName' => $firstName, 'lastName' => $lastName];
            } 
        } 
    } 
    else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    // Close statement
    mysqli_stmt_close($stmt);
}
// Close connection
mysqli_close($link);
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
    <div style="text-align: center">
        <h2>Suggestions</h2>
        <?php
            if(isset($results)) {
                foreach($results as $row){
                    echo '<p><a href="#">';
                    echo $row['firstName'].' '.$row['lastName'];
                    echo '</a></p>';
                }
            }
            else {
                echo "<p>Sorry you have no suggestions!</p>";
            }
        ?>
    </div>
<?php include("templates/bottom.html");?>