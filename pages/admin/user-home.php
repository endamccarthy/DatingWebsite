<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
?>

<?php $title = 'Admin | User | Home'; include("../templates/top.html");?>
    <div class="wrapper wrapper-wide admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom clearfix">  
                        <h2 class="float-left">User Details</h2>
                        <a href="admin-home.php" class="btn btn-info float-right">Back</a>
                    </div>
                    <?php

                    // Attempt select query execution
                    $sql = "SELECT * FROM user";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped table-sm'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>userID</th>";
                                        echo "<th>email</th>";
                                        //echo "<th>password</th>";
										echo "<th>firstName</th>";
                                        echo "<th>lastName</th>";
                                        echo "<th>dateJoined</th>";
                                        echo "<th>level</th>";
										echo "<th>status</th>";
                                        echo "<th>Notif.</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['userID'] . "</td>";
                                        echo "<td>" . $row['email'] . "</td>";
                                        //echo "<td>" . $row['password'] . "</td>";
                                        echo "<td>" . $row['firstName'] . "</td>";
                                        echo "<td>" . $row['lastName'] . "</td>";
                                        echo "<td>" . $row['dateJoined'] . "</td>";
                                        echo "<td>" . $row['accessLevel'] . "</td>";
                                        echo "<td>" . $row['status'] . "</td>";
                                        echo "<td>" . $row['notifications'] . "</td>";
										
                                        echo "<td>";
                                            echo "<a href='../admin/user-read.php?userID=". $row['userID'] ."' title='View User' data-toggle='tooltip'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                            echo "<a href='../admin/user-update.php?userID=". $row['userID'] ."' title='Update User' data-toggle='tooltip'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                                            echo "<a href='../admin/user-delete.php?userID=". $row['userID'] ."' title='Delete User' data-toggle='tooltip'><i class='fa fa-trash-o' aria-hidden='true'></i></a>";
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
        </div>
    </div>
<?php include("../templates/bottom.html");?>

<script type="text/javascript">
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
</script>