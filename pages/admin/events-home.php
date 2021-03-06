<?php
// Initialize the session
session_start();
 
// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
?>

<?php $title = 'Admin | County | Home'; include("../templates/top.html");?>
    <div class="wrapper wrapper-wide admin">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom clearfix">  
                        <h2 class="float-left">Event Details</h2>
                        <a href="events-create.php" class="btn btn-success float-right">Add New Event</a>
                        <a href="admin-home.php" class="btn btn-info float-right mr-2">Back</a>

                    </div>
                    <?php

                    // Attempt select query execution
                    $sql = "SELECT * FROM events";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped table-sm'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>eventID</th>";
                                        echo "<th>eventCountyID</th>";
                                        echo "<th>eventName</th>";
                                        echo "<th>eventDate</th>";
                                        echo "<th>eventWebsite</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['eventID'] . "</td>";
                                        echo "<td>" . $row['eventCountyID'] . "</td>";
                                        echo "<td>" . $row['eventName'] . "</td>";
                                        echo "<td>" . $row['eventDate'] . "</td>";
                                        echo "<td>" . $row['eventWebsite'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='../admin/events-read.php?eventID=". $row['eventID'] ."' title='View Record' data-toggle='tooltip'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                            echo "<a href='../admin/events-delete.php?eventID=". $row['eventID'] ."' title='Delete Record' data-toggle='tooltip'><i class='fa fa-trash-o' aria-hidden='true'></i></a>";
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