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

//define variables and set to empy values
$username = $password = $confirm_password = $username_err = $password_err = $confirm_password_err = $given_name = $given_name_err = $surname = $surname_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty(trim($_POST['username']))) {
    $username_err = 'Please enter a username.';
  } else if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST['username']))) {
    $username_err = 'Username can only contain letters, numbers, and underscores.';
  } else {
    $sql = "SELECT id FROM `accounts` WHERE  username = ?";
          
    if ($stmt = mysqli_prepare($connection, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $param_username);
          
      $param_username = trim($_POST['username']);
          
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $username_err = 'This username is already taken.';
          } else {
            $username = trim($_POST['username']);
          }
        } else {
          echo 'Oops! Something went wrong. Please try again later.';
        }
          
      mysqli_stmt_close($stmt);
    }
  }
          
  if (empty(trim($_POST['password']))) {
    $password_err = 'Please enter a password.';
  } else if (strlen(trim($_POST['password'])) < 3) {
    $password_err = 'Password must have at least 3 characters.';
  } else {
    $password = trim($_POST['password']);
  }
          
  if (empty(trim($_POST['confirm_password']))) {
    $confirm_password_err = 'Please confirm password.';
  } else {
    $confirm_password = trim($_POST['confirm_password']);
              
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = 'Password did not match.';
    }
  }
          
  if (empty(trim($_POST['given_name']))) {
    $given_name_err = 'Please input a name.';
  } else {
    $given_name = trim($_POST['given_name']);
  }
          
  if (empty(trim($_POST['surname']))) {
    $surname_err = 'Please input a name.';
  } else {
    $surname = trim($_POST['surname']);
  }

  // if (empty(trim($_POST['year_level']))) {
  //   $year_level_err = 'Please input your year level.';
  // } else {
  //   $year_level = trim($_POST['year_level']);
  // }
  
  // if (empty(trim($_POST['section']))) {
  //   $section_err = 'Please input your section.';
  // } else {
  //   $section = trim($_POST['section']);
  // }
              
  if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($given_name_err) && empty($surname_err)) {
    $sql = "INSERT INTO accounts (username, password, privilege, given_name, surname) VALUES (?, ?, 'student', ?, ?)";
              
    if ($stmt = mysqli_prepare($connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_password, $param_given_name, $param_surname);
        $param_username = $username;
        $param_password = $password;
        $param_given_name = $given_name;
        $param_surname = $surname;
        // $param_year_level = $year_level;
        // $param_section = $section;
        if (mysqli_stmt_execute($stmt)) {
          header("location: register-student.php?error=none");
        } else {
          echo 'Oops! Something went wrong. Please try again later.';
        }
                  
          mysqli_stmt_close($stmt);
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
        <link rel="stylesheet" href="../assets/styles.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
        <script src="../assets/sidenav.js" defer></script>
  </head>
  <body>
    <header>
      <h1 class="page-head">Voting System Administration</h1>
      <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <ul>
        <li class="t-li t-li-1"><a href="./" class="t-nav-links">View Results</a></li>
          <li class="t-li"><a href="student-acc.php" class="t-nav-links">Voter Accounts</a></li>
          <li class="t-li"><a href="register-student.php" class="t-nav-links active">Register Voter</a></li>
          <li class="t-li"><a href="view-candidates.php" class="t-nav-links">View Candidates</a></li>
          <li class="t-li"><a href="register-candidate.php" class="t-nav-links">Register Candidate</a></li>
          <li class="t-li"><a href="view-partylist.php" class="t-nav-links">View Partylist</a></li>
          <li class="t-li"><a href="register-partylist.php" class="t-nav-links">Register Partylist</a></li>
          <li class="t-li"><a href="add-position.php" class="t-nav-links">Add Position</a></li>
          <li class="t-li"><a href="view-positions.php" class="t-nav-links">View Positions</a></li>
        </ul>
      </div>
      <nav class="top-nav">
        <ul class="t-ul">
          <li class="t-li t-li-1"><span class="burger" onclick="openNav()">&#9776;</span></li>
        </ul>
        <ul class="t-ul">
          <li class="t-li"><a href="../assets/logout.php" class="t-nav-links">Logout</a></li>               
        </ul>
      </nav>
    </header>
  
        <main>
            <div class="form-box">
                <h2 class="form-h">Register Voter</h2>
                <?php 
                     if (isset($_GET["error"])) {
                      if ($_GET['error'] == "none") {
                        # code...
                        echo '<div class="success">Registered Successfully</div>';
                      }
                      }
                      // echo '<div class="success">Registered successfully.</div>';

                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" autocomplete="off" method="post">
                    <div class="content">
                        <div class="input-box">
                            <label for="username" class="form-label">Username</label><br>
                            <input type="text" name="username" class="form-input" value="<?php echo $username; ?>">
                            <span class="error"> <?php echo $username_err;?></span>
                        </div>
                        <div class="input-box">
                            <label for="password" class="form-label">Password</label><br>
                            <input type="password" name="password" class="form-input" value="<?php echo $password; ?>">
                            <span class="error"> <?php echo $password_err;?></span>
                        </div>
                        <div class="input-box">
                            <label for="confirm_password" class="form-label">Confirm Password</label><br>
                            <input type="password" name="confirm_password" class="form-input" value="<?php echo $confirm_password; ?>">
                            <span class="error"> <?php echo $confirm_password_err;?></span>
                        </div>
                        <div class="input-box">
                            <label for="given_name" class="form-label" autocapitalize="words">Given Name</label><br>
                            <input type="text" name="given_name" class="form-input" value="<?php echo $given_name; ?>">
                            <span class="error"> <?php echo $given_name_err;?></span>
                        </div>
                        <div class="input-box">
                            <label for="lastname" class="form-label" autocapitalize="words">Surname</label><br>
                            <input type="text" name="surname" class="form-input" value="<?php echo $surname; ?>">
                            <span class="error"> <?php echo $surname_err;?></span>
                        </div>
                        <!-- <div class="input-box mb-0">
                            <label for="sex" class="form-label">Sex</label><br>
                            <div class="form-radio">
                                <input type="radio" name="sex" value="Male" checked>
                                <label for="sex">Male</label>
                            </div>
                            <div class="form-radio">
                                <input type="radio" name="sex" value="Female">
                                <label for="sex">Female</label>
                            </div>
                        </div> -->
                        <div class="button-container">
                            <button type="submit" name="register" class="form-button">Register</button>
                        </div>
                    </div>
                </form>  
            </div>
        </main>
    </body>
</html>