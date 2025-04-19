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

  $given_name = $given_name_err = $surname = $surname_err = '';

  $accountId = $_GET['id'];
  $accCheck = "SELECT * FROM `accounts` WHERE `id` = '$accountId'";
  $accCheckQuery = mysqli_query($connection, $accCheck);
  $accCheckResult = mysqli_fetch_assoc($accCheckQuery);
  
  if (isset($_POST['back'])) {
    header('location: student-acc.php');
    exit;
  }
  
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    
    if (empty($given_name_err) && empty($surname_err)) {
      $sql = "UPDATE `accounts` SET given_name = ?, surname = ? WHERE `id` = '$accountId'";
  
      if ($stmt = mysqli_prepare($connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $param_given_name, $param_surname);
  
        $param_given_name = $given_name;
        $param_surname = $surname;
        
        if (mysqli_stmt_execute($stmt)) {
          header('location: student-acc.php?error=none');
          exit;
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
        <h2 class="form-h">Edit Student</h2>
        
				<form autocomplete="off" method="post">
          
					<div class="content">
						<div class="input-box">
							<label for="given_name" class="form-label">Given Name</label><br>
							<input type="text" name="given_name"  class="form-input" value="<?php echo $accCheckResult['given_name']; ?>">
              <span class="error"> <?php echo $given_name_err;?></span>
						</div>
						<div class="input-box">
							<label for="surname" class="form-label">Surname</label><br>
							<input type="text" name="surname"  class="form-input" value="<?php echo $accCheckResult['surname']; ?>">
              <span class="error"> <?php echo $surname_err;?></span>
						</div>
						<div class="button-container">
							<button type="submit" name="edit" class="form-button">Save Changes</button>
              <button class="form-button cancel" name="back">Cancel</button>
						</div>
					</div>
				</form>
			</div>
    </main>
  </body>
</html>