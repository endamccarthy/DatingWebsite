<?php

// Initialize the session
session_start();

// Include utility script to check if user is logged in and profile is complete
require_once "../../utilities/utility.php";
// Include config file
require_once "../../utilities/config.php";
 
// Define variables
$firstName = $lastName = $emailLogin = $passwordLogin = $emailRegister = $passwordRegister = $confirmPassword = "";
$emailErrLogin = $passwordErrLogin = $emailErrRegister = $passwordErrRegister = $confirmPasswordErr = $tAndCErr = $over18Err = "";
$userID; 
$tAndCText = "<p><i>Last amended 14 April 2020</i></p><p>These terms and conditions govern your use of www.foxyfarmers.ie (or such other URL that 
We may use to provide the Service from time to time, including but not limited to www.foxyfarmers.ie).</p>
<br><p>Please read these terms and conditions and ensure that You have understood them. If You do not agree to these terms and conditions, please 
cease use of the Website immediately. In addition, when using particular Foxy Farmers Limited services or other items provided by Foxy Farmers Limited, 
You will be subject to any Posted guidelines or rules applicable to such services which may be posted on the Website from time to time. All such 
guidelines or rules are hereby incorporated by reference into these terms and conditions.<p>
<ol><li><b>Definitions:</b> Check the Oxford English Dictionary</li><li><b>Eligibility:</b> You are at least 18 years old, have not been made subject 
to a Sex Offender Preventative Order or have/have had a restraining order made against you by the courts of ANY jurisdiction. You have not been 
convicted of any offence relating to violence or sexual harassment and/or any offence under related laws in any state. If you are unable or willing 
to give theses warranties and representations You must not apply to become a Member or attend an Event.</li>
<li><b>Terms of Agreement:</b> If you become a Member or attend an Event You agree to be bound by this Agreement. We reserve the right to monitor 
and at Our option to remove for any reason any Content Posted by You, refuse to grant applications for membership, and suspend your membership at 
any time.</li>
<li><b>Price & Payment: </b> A limited free service is available but the fun starts when you pay up!</li>
<li><b>Use of the Services: </b> We provide searches on or via the Website aimed at establishing contacts between You and other Members, however 
you are solely responsible for any Content that You publish or display on the Website or that You transmit to other Members. You cannot assume 
that the Content contained in any Profile is necessarily correct and accurate.</li>
<li><b>Members Obligations: </b> You will not misuse in any way the Services or any Content Posted on the Services or use the Content on the Website 
other than for the purposes contemplated in this Agreement, attempt to gain unauthorised access to any Content available on or via the sEries or to 
any of the networks used in providing the Services; promote another site or service; impersonate any person or entity or falsely state or otherwise 
misrepresent Your affiliation with a person or entity; intentionally violate any applicable local, state, national or international law; defame, 
harass, stalk, threaten or otherwise violate the legal rights of others; post content that contains personal contact details without first becoming 
a Subscriber; display pornographic or sexually explicit material of any kind; bully, intimidate or disparage any other Member. In the event we in 
Our sole discretion consider that any Content violates the terms of this Agreement or is offensive or illegal or has the potential to violate the 
rights of, harm or threaten the safety of other Members, We reserve the right to take action that We deem necessary, including, but not limited to, 
deleting such Content, restricting or suspending Your account.</li>
<li><b>Security: </b> Members and Subscribers must ensure You protect against unauthorised access to Your computer and notify Us immediately if any 
apparent breach of security has occurred.</li>
<li><b>Your Profile: </b> You acknowledge that other Users and Members will be able to view Your Profile, and that We reserve the right to monitor 
and at Our option to remove or amend for any reason any Content Posted by You.</li>
<li><b>Personal Data:</b> Personal Content collected from You is subject to GDPR.</li>
<li><b>Functioning of the Services: </b> We do not guarantee that any of the Content provided in or via the Serves is accurate or reliable. You 
rely on it at Your own risk. We also do not guarantee that you will make a successful match on this site. We have the right to suspend Services 
without notice for repair, maintenance or other technical reasons.</li>
<li><b>Events: </b> We reserve the right at Our sole and absolute discretion to accept or reject Your application for registration to any Event we 
organise, refuse your attendance and eject you from an Event.</li>
<li><b>Termination:</b> Right now you cannot terminate your membership on the website yourself. You can contact us at admin@foxyfarmers.ie to delete 
your account and terminate your membership on your behalf at any time.</li>
</ol>";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
  switch ($_POST["action"]) {
    // Login Form
    case "login":
      // Check if email is empty
      if(empty(trim($_POST["emailLogin"]))) {
        $emailErrLogin = "Please enter email.";
      } 
      else {
        $emailLogin = trim($_POST["emailLogin"]);
      }

      // Check if password is empty
      if(empty(trim($_POST["passwordLogin"]))) {
        $passwordErrLogin = "Please enter your password.";
      } 
      else {
        $passwordLogin = trim($_POST["passwordLogin"]);
      }
      
      // Validate credentials
      if(empty($emailErrLogin) && empty($passwordErrLogin)) {
        // Prepare a select statement
        $sql = "SELECT userID, firstName, email, password, status, accessLevel FROM user WHERE email = '$emailLogin';";
        if($stmt = mysqli_prepare($link, $sql)) {
          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            // Check if email exists, if yes then verify password
            if(mysqli_stmt_num_rows($stmt) == 1) {
              mysqli_stmt_bind_result($stmt, $userIDTemp, $firstNameTemp, $emailTemp, $hashedPasswordTemp, $statusTemp, $accessLevelTemp);
              if(mysqli_stmt_fetch($stmt)) {
                if($statusTemp == "suspended") {
                  $passwordErrLogin = "Sorry this account is temporarily suspended.";
                }
                else if($statusTemp == "banned") {
                  $passwordErrLogin = "This account has been banned.";
                }
                else {
                  if(password_verify($passwordLogin, $hashedPasswordTemp)) {
                    // Password is correct, so start a new session
                    session_start();
                    // Store data in session variables
                    $_SESSION["loggedIn"] = true;
                    $_SESSION["userID"] = $userIDTemp;
                    $_SESSION["email"] = $emailTemp;
                    $_SESSION["accessLevel"] = $accessLevelTemp;
                    $_SESSION["firstName"] = $firstNameTemp;
                  } 
                  else {
                    $passwordErrLogin = "The password you entered was not valid.";
                  }
                }
              }
            } 
            else {
              $emailErrLogin = "No account found with that email.";
            }
          } 
          else {
            echo "Oops! Something went wrong. Please try again later.";
          }
          // Close statement
          mysqli_stmt_close($stmt);
        }

        // check if user has completed their profile, redirect them to edit profile page if not
        if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
          $userID = $_SESSION["userID"];
          // check if user has entry in profile table
          $sql = "SELECT userID FROM profile WHERE userID = $userID;";
          if($stmt = mysqli_prepare($link, $sql)) {
            if(mysqli_stmt_execute($stmt)) {
              mysqli_stmt_store_result($stmt);
              if(mysqli_stmt_num_rows($stmt) !== 1) {
                $_SESSION["profileComplete"] = false;
                header("location: ../main/edit-profile.php");
              } 
              else {
                $_SESSION["profileComplete"] = true;
                header("location: ../main/suggestions.php");
              }
            } 
            else {
              echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
          }
        }
      }
    break;
    // Register Form
    case "register":
      // Validate first name
      if(trim($_POST["firstName"])) {
        $firstName = trim($_POST["firstName"]);
      }

      // Validate last name
      if(trim($_POST["lastName"])) {
        $lastName = trim($_POST["lastName"]);
      }

      // Validate email
      if(empty(trim($_POST["emailRegister"]))) {
        $emailErrRegister = "Please enter an email address.";
      }
      else {
        // Check user table for email address
        $sql = "SELECT userID FROM user WHERE email = ?;";
        if($stmt = mysqli_prepare($link, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $paramEmail);
          $paramEmail = trim($_POST["emailRegister"]);
          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1) {
              $emailErrRegister = "This email is already taken.";
            } 
            else {
              $emailRegister = trim($_POST["emailRegister"]);
            }
          } 
          else {
            echo "Oops! Something went wrong. Please try again later.";
          }
          // Close statement
          mysqli_stmt_close($stmt);
        }
      }
      
      // Validate password
      if(empty(trim($_POST["passwordRegister"]))) {
        $passwordErrRegister = "Please enter a password.";     
      } 
      elseif(strlen(trim($_POST["passwordRegister"])) < 6) {
        $passwordErrRegister = "Password must have at least 6 characters.";
      } 
      else {
        $passwordRegister = trim($_POST["passwordRegister"]);
      }
      
      // Validate confirm password
      if(empty(trim($_POST["confirmPassword"]))) {
        $confirmPasswordErr = "Please confirm password.";     
      } 
      else {
        $confirmPassword = trim($_POST["confirmPassword"]);
        if(empty($passwordErrRegister) && ($passwordRegister != $confirmPassword)) {
          $confirmPasswordErr = "Password did not match.";
        }
      }
      
      // Validate terms and conditions 
      if(!isset($_POST["tAndC"])) {
        $tAndCErr = "You must agree before submitting.";
      }

      // Validate over 18 
      if(!isset($_POST["over18"])) {
        $over18Err = "You must be over 18 to register";
      }

      // Check input errors before inserting in database
      if(empty($emailErrRegister) && empty($passwordErrRegister) && empty($confirmPasswordErr) && empty($tAndCErr) && empty($over18Err)) {
        $sql = "INSERT INTO user (firstName, lastName, email, password) VALUES ('$firstName', '$lastName', '$emailRegister', ?);";
        if($stmt = mysqli_prepare($link, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $paramPassword);
          // Create a password hash
          $paramPassword = password_hash($passwordRegister, PASSWORD_DEFAULT);
          if(!mysqli_stmt_execute($stmt)) {
            echo "Oops! Something went wrong. Please try again later.";
          }
          // Close statement
          mysqli_stmt_close($stmt);
        }
        // Retrieve the new users ID
        $sql = "SELECT userID, firstName FROM user WHERE email = '$emailRegister';";
        if($stmt = mysqli_prepare($link, $sql)) {
          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if(mysqli_stmt_num_rows($stmt) == 1) {
              mysqli_stmt_bind_result($stmt, $userIDTemp, $firstNameTemp);
              while (mysqli_stmt_fetch($stmt)) {
                // Store data in session variables
                $_SESSION["loggedIn"] = true;
                $_SESSION["userID"] = $userIDTemp;
                $_SESSION["email"] = $emailRegister;
                $_SESSION["profileComplete"] = false;
                $_SESSION["firstName"] = $firstNameTemp;
                header("location: ../main/edit-profile.php");
              }
            }
          } 
          else {
            echo "Oops! Something went wrong. Please try again later.";
          }
          // Close statement
          mysqli_stmt_close($stmt);
        }
      }
    break;
    default:
      echo "Oops! Something went wrong. Please try again later.";
  }
}
// Close connection
mysqli_close($link);
?>


<?php $title = 'Foxy Farmers'; include("../templates/top.html");?>
<div class="container">
  <div class="container-item container-item-center-text container-item-shadow">
    <h2><i>Find Your Foxy Farmer!</i></h2>
  </div>
</div>
<div class="container">
  <div class="container-item container-item-shadow">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form name="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="emailLogin" class="form-control <?php echo (!empty($emailErrLogin)) ? 'is-invalid' : ''; ?>" value="<?php echo $emailLogin; ?>" required>
        <span class="invalid-feedback"><?php echo $emailErrLogin; ?></span>
      </div>   
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="passwordLogin" class="form-control <?php echo (!empty($passwordErrLogin)) ? 'is-invalid' : ''; ?>" value="<?php echo $passwordLogin; ?>">
        <span class="invalid-feedback"><?php echo $passwordErrLogin; ?></span>
      </div>
      <div class="form-group">
        <input type="hidden" name="action" value="login">
        <input type="submit" class="btn btn-primary" value="Login">
      </div>
    </form>
    <div class="p-3 mt-4 border-top" style="text-align: center;">
      <p>“Sometimes I think I’d be less lonely living in an enclosed convent than in small town Ireland.”</p>
      <p><i>The Irish Times, Aine Ryan, 25 June 2019</i></p><br>
      <h5><b>Foxy Farmers</b> is a dating site for people living or wanting to live in a rural farming community in Ireland.</h5>
    </div>
  </div> 

  <div class="container-item container-item-shadow">
    <h2>Register</h2>
    <p>Please fill in this form to create an account.</p>
    <form name="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="form-row">
        <div class="col-md-6 mb-3">
          <label>First name</label>
          <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label>Last name</label>
          <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="emailRegister" class="form-control <?php echo (!empty($emailErrRegister)) ? 'is-invalid' : ''; ?>" value="<?php echo $emailRegister; ?>" required>
        <span class="invalid-feedback"><?php echo $emailErrRegister; ?></span>
      </div>    
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="passwordRegister" class="form-control <?php echo (!empty($passwordErrRegister)) ? 'is-invalid' : ''; ?>" value="<?php echo $passwordRegister; ?>" required>
        <span class="invalid-feedback"><?php echo $passwordErrRegister; ?></span>
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirmPassword" class="form-control <?php echo (!empty($confirmPasswordErr)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirmPassword; ?>" required>
        <span class="invalid-feedback"><?php echo $confirmPasswordErr; ?></span>
      </div>
      <div class="form-group">
        <div class="form-check">
          <input type="checkbox" name="tAndC" class="form-check-input <?php echo (!empty($tAndCErr)) ? 'is-invalid' : ''; ?>">
          <label class="form-check-label"><a href="#" data-target="#tAndCModal" data-toggle="modal" class="">Agree to terms and conditions</a></label>
          <span class="invalid-feedback"><?php echo $tAndCErr; ?></span>
        </div>
      </div>
      <div class="form-group">  
        <div class="form-check">
          <input type="checkbox" name="over18" class="form-check-input <?php echo (!empty($over18Err)) ? 'is-invalid' : ''; ?>">
          <label class="form-check-label">I am over 18</label>
          <span class="invalid-feedback"><?php echo $over18Err; ?></span>
        </div>
      </div>
      <div class="form-group">
        <input type="hidden" name="action" value="register">
        <input type="submit" class="btn btn-primary" value="Submit">
      </div>
    </form>
  </div>
</div>

<!-- Upgrade to Premium Modal -->
<div class="modal fade" id="tAndCModal" tabindex="-1" role="dialog" aria-labelledby="tAndCModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tAndCModalLabel">Terms & Conditions</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><?php echo $tAndCText; ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include("../templates/bottom.html");?>
