<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
?>

<?php $title = 'Admin | Home'; include("../templates/top.html");?>
    <div class="wrapper wrapper-wide">
        <div class="container-fluid">
            <div class="row border-bottom">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 clearfix">  
                        <h2 class="float-left">Reported Users</h2>
                        <a href="../main/suggestions.php" class="btn btn-warning float-right">Back</a>
                    </div>
                    <?php

                    // Attempt select query execution
                    $sql = "SELECT * FROM reported";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped table-sm'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>User Who Filed Report</th>";
                                        echo "<th>Reported User</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['reportedUserOne'] . "</td>";
                                        echo "<td>" . $row['reportedUserTwo'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='../admin/user-suspend.php?userID=". $row['reportedUserTwo'] ."' title='Suspend Reported User' data-toggle='tooltip'>Suspend</a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom clearfix"> 
                        <h6><b>Table Maintenance:</b></h6> 
                        <a href="events-home.php" class="btn btn-success">Events</a>
                        <a href="interestList-home.php" class="btn btn-success">Interests</a>
                        <a href="user-home.php" class="btn btn-success">Users</a>
                </div>
            </div>        
        </div>
    </div>
<?php include("../templates/bottom.html");?>

<script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
</script>