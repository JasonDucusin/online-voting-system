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

if (isset($_POST['back'])) {
  header('location: view-positions.php');
  exit;
}

$position = $position_err = $maximum = $maximum_err = '';

$positionId = $_GET['id'];
$positionCheck = "SELECT * FROM `positions` WHERE `id` = '$positionId'";
$positionCheckQuery = mysqli_query($connection, $positionCheck);
$positionCheckResult = mysqli_fetch_assoc($positionCheckQuery);

// if (isset($_POST['back'])) {
//   header('location: view-positions');
//   exit;
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty(trim($_POST['name']))) {
    $position_err = 'Please input a position name.';
  } else {
    $position_check = "SELECT id FROM positions WHERE `position` = ?";

    if ($stmt = mysqli_prepare($connection, $position_check)) {
      mysqli_stmt_bind_param($stmt, "s", $param_position);

      $param_position = trim($_POST["name"]);

      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
          $position_err = "This partylist name is already taken.";
        } else {
          $position = trim($_POST["name"]);
        }
      } else {
        echo 'Oops! Something went wrong. Please try again later.';
      }

      mysqli_stmt_close($stmt);
    }
  }

  if (empty(trim($_POST["maximum"]))) {
    $maximum_err = "Please enter a number.";
  } else {
    $maximum = trim($_POST["maximum"]);
  }

  if (empty($position_err)) {
    $sql = "UPDATE `positions` SET position = ?, maximum = ? WHERE `id` = '$positionId'";

    if ($stmt = mysqli_prepare($connection, $sql)) {
      mysqli_stmt_bind_param($stmt, "si", $param_position, $param_maximum);

      $param_position = $position;
      $param_maximum = $maximum;

      if (mysqli_stmt_execute($stmt)) {
        header('location: view-positions.php');
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
        <h2 class="form-h">Edit Position</h2>
				<form autocomplete="off" method="post">  
					<div class="content">
						<div class="input-box">
							<label for="name" class="form-label">Position Name</label><br>
							<input type="text" name="name"  class="form-input"value="<?php echo $positionCheckResult['position']; ?>">
              <span class="error"><?php echo $position_err;?></span>
						</div>
            <div class="input-box">
							<label for="maximum" class="form-label">Maximum Candidate per Party</label><br>
							<input type="text" name="maximum"  class="form-input"value="<?php echo $positionCheckResult['maximum']; ?>">
              <span class="error"><?php echo $maximum_err;?></span>
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