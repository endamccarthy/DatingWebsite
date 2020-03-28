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
            <div class="row">
                <div class="col-md-12">
                    <div class="pb-2 mt-4 mb-4 border-bottom clearfix">  
                        <h2 class="float-left">Employees Details</h2>
                        <a href="admin-create.php" class="btn btn-success float-right">Add New Employee</a>
                    </div>
                    <?php
                    // Attempt select query execution
                    $sql = "SELECT * FROM employees";
                    if($result = mysqli_query($link, $sql)) {
                        if(mysqli_num_rows($result) > 0) {
                            echo "<table class='table table-bordered table-striped table-sm'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Name</th>";
                                        echo "<th>Address</th>";
                                        echo "<th>Salary</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['name'] . "</td>";
                                        echo "<td>" . $row['address'] . "</td>";
                                        echo "<td>" . $row['salary'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='admin-read.php?id=". $row['id'] ."' title='View Record' data-toggle='tooltip'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                            echo "<a href='admin-update.php?id=". $row['id'] ."' title='Update Record' data-toggle='tooltip'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                                            echo "<a href='admin-delete.php?id=". $row['id'] ."' title='Delete Record' data-toggle='tooltip'><i class='fa fa-trash-o' aria-hidden='true'></i></a>";
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } 
                        else {
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } 
                    else {
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