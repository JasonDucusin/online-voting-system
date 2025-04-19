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
  header('location: view-partylist.php');
  exit;
}

$name = $name_err = '';

$partylistId = $_GET['id'];
$partylistCheck = "SELECT * FROM `party_lists` WHERE `id` = '$partylistId'";
$partylistCheckQuery = mysqli_query($connection, $partylistCheck);
$partylistCheckResult = mysqli_fetch_assoc($partylistCheckQuery);

if (isset($_POST['back'])) {
  header('location: view-partylist.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty(trim($_POST['name']))) {
    $partylist_err = 'Please input a partylist name.';
  } else {
    // Checks if party list name is taken or not
    $party_list_check = "SELECT id FROM party_lists WHERE `name` = ?";

    if ($stmt = mysqli_prepare($connection, $party_list_check)) {
      mysqli_stmt_bind_param($stmt, "s", $param_name);

      $param_name = trim($_POST["name"]);

      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
          $name_err = "This partylist name is already taken.";
        } else {
          $name = trim($_POST["name"]);
        }
      } else {
        echo 'Oops! Something went wrong. Please try again later.';
      }

      mysqli_stmt_close($stmt);
    }
  }

  if (empty($name_err)) {
    $sql = "UPDATE `party_lists` SET name = ? WHERE `id` = '$partylistId'";

    if ($stmt = mysqli_prepare($connection, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $param_name);

      $param_name = $name;

      if (mysqli_stmt_execute($stmt)) {
        header('location: view-partylist.php');
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
        <h2 class="form-h">Edit Party-list</h2>
				<form autocomplete="off" method="post">  
					<div class="content">
						<div class="input-box">
							<label for="name" class="form-label">Party-list Name</label><br>
							<input type="text" name="name"  class="form-input"value="<?php echo $partylistCheckResult['name']; ?>">
              <span class="error"><?php echo $name_err; ?></span>
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