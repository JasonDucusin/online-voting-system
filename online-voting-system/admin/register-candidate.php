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

  // Candidate Variables
$candidate_username = $candidate_username_error = $candidate_position = $candidate_position_error = $candidate_party_list = $candidate_party_list_error = $candidate_valid = '';

// Checks users for choosing of candidate
$studentCheck = "SELECT username, given_name, surname FROM accounts WHERE privilege = 'student' ORDER BY given_name ASC";
$studentCheckQuery = mysqli_query($connection, $studentCheck);

// Checks party_lists for choosing of party-list
$partylistCheck = "SELECT id, name FROM party_lists ORDER BY name ASC";
$partylistCheckQuery = mysqli_query($connection, $partylistCheck);

$positionsCheck = "SELECT id, position FROM positions ORDER BY position ASC";
$positionsCheckQuery = mysqli_query($connection, $positionsCheck);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["add_candidate"])) {
    // What the server does when adding candidate

    if (empty(trim($_POST["candidate"]))) {
      $candidate_username_error = "Please select a student.";
    } else {
      // Checks if username is registered and belongs to a student
      $usernameCheck = "SELECT username FROM accounts WHERE username = ? AND privilege = 'student'";
  
      if ($stmt = mysqli_prepare($connection, $usernameCheck)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
  
        $param_username = trim($_POST["candidate"]);
  
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
  
          if (mysqli_stmt_num_rows($stmt) == 1) { 
            $candidate_valid = true;
          } else {
            $candidate_username_error = "Username not found or does not belong to a student.";
          }
        } else {
          echo 'Oops! Something went wrong. Please try again later.';
        }
  
        mysqli_stmt_close($stmt);
      }

      if ($candidate_valid == true) {
        // Checks if username is already registered in the candidates list
        $candidateCheck = "SELECT student_username FROM candidates WHERE student_username = ?";
        
        if ($stmt = mysqli_prepare($connection, $candidateCheck)) {
          mysqli_stmt_bind_param($stmt, "s", $param_username);

          $param_username = trim($_POST["candidate"]);

          if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 0) {
              $candidate_username = trim($_POST["candidate"]);
            } else {
              $candidate_username_error = "Already registered in the candidates list.";
            }
          } else {
            echo 'Oops! Something went wrong. Please try again later.';
          }

          mysqli_stmt_close($stmt);
        }
      }
    }
  
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
  
    if (!empty($candidate_position) && !empty($candidate_party_list)) {
      // Checks if position is already fully occupied for the party-list in the candidates list
      $position_max = get_query_result("SELECT maximum FROM positions WHERE position = '$candidate_position'")["maximum"];
      
      $same_position_check = "SELECT student_username FROM candidates WHERE partylist_position = ? AND party_list_id = ?";
      
      if ($stmt = mysqli_prepare($connection, $same_position_check)) {
        mysqli_stmt_bind_param($stmt, "si", $param_position, $param_party_list);

        $param_position = $candidate_position;
        $param_party_list = $candidate_party_list;

        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);

          if (mysqli_stmt_num_rows($stmt) < $position_max) {
            if (empty($candidate_username_error) && empty($candidate_position_error) && empty($candidate_party_list_error)) {
              $insert_candidate = "INSERT INTO candidates (student_username, partylist_position, party_list_id) VALUES (?, ?, ?)";
          
              if ($stmt2 = mysqli_prepare($connection, $insert_candidate)) {
                mysqli_stmt_bind_param($stmt2, "ssi", $param_username, $param_position, $param_party_list);
          
                $param_username = $candidate_username;
                $param_position = $candidate_position;
                $param_party_list = $candidate_party_list;
          
                if (mysqli_stmt_execute($stmt2)) {
                  $candidate_add_success = "Successfully added.";
                } else {
                  echo 'Oops! Something went wrong. Please try again later.';
                }
          
                mysqli_stmt_close($stmt2);
              }
            }
          } else {
              $candidate_same_position_error = "Position is fully occupied for this party-list.";
          }
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
    <link rel="stylesheet" href="../assets/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="../assets/sidenav.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    
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
          <li class="t-li"><a href="register-candidate.php" class="t-nav-links active">Register Candidate</a></li>
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
      <div class="form-box candidate">
        <h2 class="form-h">Register Candidate</h2>
        <?php if (!empty($candidate_add_success)) echo '<div class="success">'.$candidate_add_success. '</div>'?>
        <form action="" autocomplete="off" method="post">
          <div class="input-box">
            <label for="candidate" class="form-label">Candidate</label>
            <select name="candidate" class="form-select">
              <option hidden></option>
              <?php
              while ($row = mysqli_fetch_assoc($studentCheckQuery)) {
                $studentUsername = $row["username"];
                $studentName = $row["surname"]. ", " .$row["given_name"];
                echo "<option value=\"$studentUsername\">$studentName</option>";
              }
            ?>
            </select>
            <span class="error"><?php if (!empty($candidate_username_error)) echo $candidate_username_error?></span>
          </div>
          <div class="input-box">
            <label for="position" class="form-label">Position</label>
            <select name="position" class="form-select">
              <option hidden></option>
              <?php
              while ($row = mysqli_fetch_assoc($positionsCheckQuery)) {
                $positionsID = $row["id"];
                $positionsPosition = $row["position"];
                echo "<option value=\"$positionsPosition\">$positionsPosition</option>";
              }
            ?>
            </select>
            <span class="error"><?php if (!empty($candidate_position_error)) echo $candidate_position_error?></span>
          </div>
          <div class="input-box">
            <label for="party_list" class="form-label">Party List</label>
            <select name="party_list" class="form-select">
              <option hidden></option>
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
          <button type="submit" name="add_candidate" class="form-button">Register</button>
          <?php if (!empty($candidate_same_position_error)) echo "<p style=\"margin-top: 10px;margin-bottom: 0px;\">$candidate_same_position_error</p>";?>
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