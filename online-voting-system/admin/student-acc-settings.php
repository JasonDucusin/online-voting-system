<?php
session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('location: ../');
    exit;
  }
  if ($_SESSION['privilege'] != 'admin') {
    header('location: ../');
    exit;
  }

  require_once '../assets/dbhandler.php';

  $new_password = $confirm_password = $new_password_err = $confirm_password_err = $username_err = '';

  if (isset($_POST['back'])) {
    header('location: student-acc.php');
    exit;
  }

$username = $_GET['username'];
$accCheck = "SELECT * FROM `accounts` WHERE `username` = '$username'";
$accCheckQuery = mysqli_query($connection, $accCheck);
$accCheckResult = mysqli_num_rows($accCheckQuery);
$fetchedData = mysqli_fetch_assoc($accCheckQuery);
$accPrivilege = $fetchedData['privilege'];
$current_password = $fetchedData['password'];

if ($accCheckResult == 1) {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['change_password'])) {
      if (empty(trim($_POST['new_password']))) {
        $new_password_err = 'Please enter the new password.';
      } else if (strlen(trim($_POST['new_password'])) < 3) {
        $new_password_err = 'Password must have at least 3 characters.';
      } else {
        $new_password = trim($_POST['new_password']);
      }
    
      if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = 'Please confirm the password.';
      } else {
        $confirm_password = trim($_POST['confirm_password']);
    
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
          $confirm_password_err = 'Password did not match.';
        }
      }
    
      if (empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE `accounts` SET password = ? WHERE `username` = '$username'";
    
        if ($stmt = mysqli_prepare($connection, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $param_password);
    
          $param_password = $new_password;
    
          if (mysqli_stmt_execute($stmt)) {
            header("location: student-acc.php?error=none");
            exit;
          } else {
            echo 'Oops! Something went wrong. Please try again later.';
          }
    
          mysqli_stmt_close($stmt);
        }
      }
    } else if (isset($_POST['change_username'])) {
      if (empty(trim($_POST['new_username']))) {
        $username_err = 'Please enter a username.';
      } else if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['new_username']))) {
        $username_err = 'Username can only contain letters, numbers, and underscores.';
      } else {
        $sql = "SELECT id FROM `accounts` WHERE `username` = ?";
  
        if ($stmt = mysqli_prepare($connection, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $param_username);
  
          $param_username = trim($_POST['new_username']);
  
          if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
  
            if (mysqli_stmt_num_rows($stmt) == 1) {
              $username_err = 'This username is already used.';
            } else {
              $new_username = trim($_POST['new_username']);
            }
          } else {
            echo 'Oops! Something went wrong. Please try again later.';
          }
  
          mysqli_stmt_close($stmt);
        }
      }
  
      if (empty($username_err)) {
        $sql = "UPDATE `accounts` SET `username` = ? WHERE `accounts`.`username` = '$username'";

        if ($stmt = mysqli_prepare($connection, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $param_username);

          $param_username = $new_username;

          if (mysqli_stmt_execute($stmt)) {
              header("location: student-acc.php?error=none");
            }
            exit;
          } else {
            echo 'Oops! Something went wrong. Please try again later.';
          }

          mysqli_stmt_close($stmt);
        }
      }
    }
    
    mysqli_close($connection);
  }


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo WEBSITE_TITLE ?></title>
    <link rel="stylesheet" href="../assets/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
  </head>
  <body>
    <!-- <header>
      <h1 class="page-head">Admin Page</h1>
      <nav class="top-nav">
        <ul class="t-ul">
          <li class="t-li t-li-1"><a href="./" class="t-nav-links">Results</a></li>
          <li class="t-li"><a href="student-acc" class="t-nav-links active">Student Accounts</a></li>
          <li class="t-li"><a href="register-student" class="t-nav-links">Register Student</a></li>
          <li class="t-li"><a href="register-admin" class="t-nav-links">Register Admin</a></li>
          <li class="t-li"><a href="#" class="t-nav-links">View Candidates</a></li>
          <li class="t-li"><a href="#" class="t-nav-links">Register Candidate</a></li>
          <li class="t-li"><a href="#" class="t-nav-links">View Partylist</a></li>
          <li class="t-li"><a href="#" class="t-nav-links">Register Partylist</a></li>
        </ul>
        <ul class="t-ul">
          <li class="t-li"><a href="../assets/logout" class="t-nav-links">Logout</a></li>               
        </ul>
      </nav>
    </header> -->
    <main>
    <div class="form-box">
        <!-- <h2 class="form-h">Change Username</h2> -->
        
				<form action="<?php echo 'student-acc-settings.php?username=' . $username; ?>" method="POST">
                    <h2 class="form-h">Change Username</h2>

                <div class="input-box">
                  <label class="form-label">Username</label>
                  <input type="text" name="new_username" class="form-input" value="<?php echo $username; ?>">
                  <span class="error"><?php echo $username_err; ?></span>
                </div>

                <div class="input-box mb-0">
                  <input type="submit" class="form-button" name="change_username" value="SAVE CHANGES">
                </div>
              </form>

            <hr style="border-color: darkgray;">

              <h2 class="form-h">Change Password</h2>
              <form action="<?php echo 'student-acc-settings.php?username=' . $username; ?>" method="POST">

                <div class="input-box">
                  <label class="form-label">Current Password</label>
                  <input type="text" disabled placeholder="Current Password" class="form-input" value="<?php echo htmlspecialchars($current_password); ?>">
                </div>

                <div class="input-box">
                  <label class="form-label">New Password</label>
                  <input placeholder="New Password" type="password" name="new_password" class="form-input" value="<?php echo $new_password; ?>">
                  <span class="error"><?php echo $new_password_err; ?></span>
                </div>

                <div class="input-box">
                  <label class="form-label">Confirm Password</label>
                  <input placeholder="Confirm Password" type="password" name="confirm_password" class="form-input">
                  <span class="error"><?php echo $confirm_password_err; ?></span>
                </div>

                  <input type="submit" class="form-button" name="change_password" value="SAVE CHANGES">
              </form>
              <button class="form-button cancel" name="back" onclick="document.location= 'student-acc'">BACK</button>
			</div>
    </main>
  </body>
</html>