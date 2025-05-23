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
  
  $positions_array = array();
  
  $positions = quick_query("SELECT position FROM positions ORDER BY id ASC");
  
  while ($position = mysqli_fetch_assoc($positions)) {
    $name = $position["position"];
    array_push($positions_array, $name);
  }

//   echo "<pre>";
// echo ini_get('session.save_path');
// echo "</pre>";

// die();
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
        <li class="t-li t-li-1"><a href="./" class="t-nav-links active">View Results</a></li>
          <li class="t-li"><a href="student-acc.php" class="t-nav-links">Voter Accounts</a></li>
          <li class="t-li"><a href="register-student.php" class="t-nav-links">Register Voter</a></li>
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
    <?php
      if (count($positions_array) > 0) {
        // echo "<h1>Current Result</h1>";
        echo '<div class="table-container result">';
        for ($i = 0; $i < count($positions_array); $i++) {
          $position_name = $positions_array[$i];
          echo '<table class="result">';
          // echo '<div class="list-box">';
          echo "<caption>$position_name</caption>";
          echo "<thead>
            <tr>
              <th class='rank'>Rank</th>
              <th>Name</th>
              <th>Partylist</th>
              <th>Votes</th>
            </tr>
          </thead><tbody>";

          $position_query = quick_query("SELECT student_username, partylist_position, party_list_id, votes FROM candidates WHERE partylist_position = '$position_name' ORDER BY votes DESC");
          $rank=0;
          while ($position_row = mysqli_fetch_assoc($position_query)) {
            $rank++;
            $username = $position_row["student_username"];
            $party_list_id = $position_row["party_list_id"];
            $votes = $position_row["votes"];

            $name_sql = get_query_result("SELECT given_name, surname FROM accounts WHERE username = '$username'");

            $name = $name_sql["given_name"] . " " . $name_sql["surname"];
            $party_list = get_query_result("SELECT name FROM party_lists WHERE id = '$party_list_id'")["name"];

            echo
            "<tr>
            <td>$rank.</td><td>$name</td> <td>$party_list</td> <td>$votes</td>
            </tr>";
          }

          // echo "</div>";
          echo "</tbody></table>";
        }
        echo "</div>";
      } else {
        echo "<h2 class='result'>No candidates to show. Register candidates first.</h2>";
      }
    ?>
    </main>
  </body>
</html>
<?php
mysqli_close($connection);
?>
