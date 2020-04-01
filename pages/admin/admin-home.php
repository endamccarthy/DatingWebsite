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
                        <a href="countyList-home.php" class="btn btn-success">County List Table</a><br><br>
                        <a href="events-home.php" class="btn btn-success">Events Table</a><br><br>
                        <a href="interestList-home.php" class="btn btn-success">Interest List Table</a><br><br>
                        <a href="user-home.php" class="btn btn-success">User Table</a><br><br>
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