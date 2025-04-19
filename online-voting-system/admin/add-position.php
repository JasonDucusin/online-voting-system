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
  
require_once "../assets/dbhandler.php";

// Student Variables
$position = $position_error = $maximum = $maximum_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty(trim($_POST["position"]))) {
    $position_error = "Please enter a position.";
  } else {
    // Checks if party list position is taken or not
    $party_list_check = "SELECT id FROM positions WHERE position = ?";

    if ($stmt = mysqli_prepare($connection, $party_list_check)) {
      mysqli_stmt_bind_param($stmt, "s", $param_position);

      $param_position = trim($_POST["position"]);

      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
          $position_error = "This position already exist.";
        } else {
          $position = trim($_POST["position"]);
        }
      } else {
        echo 'Oops! Something went wrong. Please try again later.';
      }

      mysqli_stmt_close($stmt);
    }
  }
  if (empty(trim($_POST["maximum"]))) {
    $maximum_error = "Please enter a number.";
  } else {
    $maximum = trim($_POST["maximum"]);
  }
  
  if (empty($position_error) && empty($maximum_error)) {
    $insertPosition = "INSERT INTO positions (position, maximum) VALUES (?, ?)";

    if ($stmt = mysqli_prepare($connection, $insertPosition)) {
      mysqli_stmt_bind_param($stmt, "si", $param_position, $param_maximum);

      $param_position = $position;
      $param_maximum = $maximum;

      if (mysqli_stmt_execute($stmt)) {
        $add_success = "Successfully added.";
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
    <meta position="viewport" content="width=device-width, initial-scale=1.0">
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
          <li class="t-li"><a href="register-student.php" class="t-nav-links">Register Voter</a></li>
          <li class="t-li"><a href="view-candidates.php" class="t-nav-links">View Candidates</a></li>
          <li class="t-li"><a href="register-candidate.php" class="t-nav-links">Register Candidate</a></li>
          <li class="t-li"><a href="view-partylist.php" class="t-nav-links">View Partylist</a></li>
          <li class="t-li"><a href="register-partylist.php" class="t-nav-links">Register Partylist</a></li>
          <li class="t-li"><a href="add-position.php" class="t-nav-links active">Add Position</a></li>
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
    <!-- ADD PARTY-LIST FORM -->
    <div class="form-box">
      <h2 class="form-h">Add Position</h2>
      <?php 
        if (!empty($add_success)) {
           echo '<div class="success">' . $add_success . '</div>';
         }
      ?>
      <div class="content">
        <form autocomplete="off" action="" method="POST">
          <div class="input-box">
            <label for="position" class="form-label">Position Name</label>
            <input autofocus type="text" name="position" class="form-input" value="<?php
            if (empty($add_success)) {
              if (!empty($position)) {
                echo $position;
              }
            }
            ?>">
            <span class="error"><?php if (!empty($position_error)) echo $position_error ?><span>
          </div>
          <div class="input-box">
            <label for="maximum " class="form-label">Maximum Candidate per Party</label>
            <input autofocus type="number" name="maximum" class="form-input" value="<?php
            if (empty($add_success)) {
              if (!empty($maximum)) {
                echo $maximum;
              }
            }
            ?>">
            <span class="error"><?php if (!empty($maximum_error)) echo $maximum_error ?><span>
          </div>
          <div class="button-container"></div>
            <button type="submit" name="add_party_list" class="form-button">Add</button>
          </div>
      </div>
      </form>
    </div>
  </main>
</body>
</html>