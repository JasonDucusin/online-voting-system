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
function get_query_result(string $sql_query) {
  global $connection;

  $sql = $sql_query;
  $query = mysqli_query($connection, $sql);
  
  return mysqli_fetch_assoc($query);
}

function quick_query(string $sql_query) {
  global $connection;

  $sql = $sql_query;
  return mysqli_query($connection, $sql);
}

$candidate_postion = $candidate_position_err = $candidate_partylist = $candidate_partylist_err = '';

$candidateId = $_GET['id'];
$candidateCheck = "SELECT * FROM `candidates` WHERE `id` = '$candidateId'";
$candidateCheckQuery = mysqli_query($connection, $candidateCheck);
$candidateCheckResult = mysqli_fetch_assoc($candidateCheckQuery);

$candidateUsername = $candidateCheckResult['student_username'];
$accCheck = "SELECT * FROM `accounts` WHERE username = '$candidateUsername'";
$accCheckQuery = mysqli_query($connection, $accCheck);
$accCheckResult = mysqli_fetch_assoc($accCheckQuery);

$partylistCheck = "SELECT id, name FROM party_lists ORDER BY name ASC";
$partylistCheckQuery = mysqli_query($connection, $partylistCheck);

$currentPartyId = $candidateCheckResult['party_list_id'];
$currentPartyCheck = "SELECT name FROM `party_lists` WHERE id = '$currentPartyId'";
$currentPartyQuery = mysqli_query($connection, $currentPartyCheck);
$currentPartyData = mysqli_fetch_assoc($currentPartyQuery);

$positionsCheck = "SELECT id, position FROM positions ORDER BY position ASC";
$positionsCheckQuery = mysqli_query($connection, $positionsCheck);

if (isset($_POST['back'])) {
    header('location: view-candidates.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (empty(trim($_POST["position"]))) {
    $candidate_position_error = "Please select a position.";
  } else {
    $candidate_position = trim($_POST["position"]);
  }

  if (empty(trim($_POST["party_list"]))) {
    $candidate_party_list_error = "Please select a party-list.";
  } else {
    $candidate_party_list = trim($_POST["party_list"]);
  }

  if (empty($candidate_position_error) && empty($candidate_party_list_error)) {
    $updateCandidate = "UPDATE `candidates` SET partylist_position = ?, party_list_id = ? WHERE `id` = '$candidateId'";

    if ($stmt = mysqli_prepare($connection, $updateCandidate)) {
      mysqli_stmt_bind_param($stmt, "ss", $param_candidate_position, $param_candidate_party_list);
  
      $param_candidate_position = $candidate_position;
      $param_candidate_party_list = $candidate_party_list;
  
      if (mysqli_stmt_execute($stmt)) {
          header("location: view-candidates.php");
          exit;
      } else {
          echo 'Execution failed: ' . mysqli_error($connection); // Display any errors
      }
      mysqli_stmt_close($stmt);
  } else {
      echo 'Preparation failed: ' . mysqli_error($connection); // Display any errors
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
    <script src="../assets/sidenav.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    
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
    <div class="form-box candidate">
        <h2 class="form-h">Edit Candidate</h2>
				<form autocomplete="off" method="post">  
					<div class="content">
						<div class="input-box">
							<label for="name" class="form-label">Candidate Name</label><br>
							<input type="text"  class="form-input" disabled value="<?php echo $accCheckResult['surname'].', '. $accCheckResult['given_name']?>">
              <span class="error"></span>
						</div>
            <div class="input-box">
              <label for="position" class="form-label">Position</label>
              <select name="position" class="form-select">
                <option hidden><?php echo $candidateCheckResult['partylist_position'];?></option>
                <?php
              while ($row = mysqli_fetch_assoc($positionsCheckQuery)) {
                $positionsID = $row["id"];
                $positionsPosition = $row["position"];
                echo "<option value=\"$positionsPosition\">$positionsPosition</option>";
              }
            ?>
              </select>
            <span class="error"></span>
            </div>
            <div class="input-box">
            <label for="party_list" class="form-label">Party List</label>
              <select name="party_list" class="form-select">
                <option hidden><?php echo $currentPartyData['name'];?></option>
                <?php
                while ($row = mysqli_fetch_assoc($partylistCheckQuery)) {
                  $partylistID = $row["id"];
                  $partylistName = $row["name"];
                  echo "<option value=\"$partylistID\">$partylistName</option>";
                }
              ?>
              </select>
            <span class="error"><?php if (!empty($candidate_party_list_error)) echo $candidate_party_list_error ?></span>
            </div>
						<div class="button-container">
							<button type="submit" name="edit" class="form-button">Save Changes</button>
              <button class="form-button cancel" name="back">Cancel</button>
						</div>
					</div>
				</form>
			</div>
    </main>
    <script>
    $(document).ready(function () {
//change selectboxes to selectize mode to be searchable
   $("select").select2();
});
  </script>
  </body>
</html>