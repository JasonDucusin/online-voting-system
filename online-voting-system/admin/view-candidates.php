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

  $candidateSql = "SELECT * FROM candidates";
  $candidateResult = mysqli_query($connection, $candidateSql);
  
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
          <li class="t-li"><a href="register-student.php" class="t-nav-links">Register Voter</a></li>
          <li class="t-li"><a href="view-candidates.php" class="t-nav-links active">View Candidates</a></li>
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
      <div class="table-container">

        <table>
          <caption>Candidates</caption>
          <thead>
            <tr>
              <th>Name</th>
              <th>Position</th>
              <th>Partylist</th>
              <th colspan="2" class="actions">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php
            if (mysqli_num_rows($candidateResult) > 0) {
              // output data of each row
              while($candidateRow = mysqli_fetch_assoc($candidateResult)) {
                  echo 
                  '<tr>
                    <td>';
                    $accUsername = $candidateRow['student_username'];
                    $accSql = "SELECT * FROM accounts WHERE username='$accUsername'";
                    $accResult = mysqli_query($connection, $accSql);
                    $accFetchedData = mysqli_fetch_assoc($accResult);

                    echo $accFetchedData['surname'].', '.$accFetchedData['given_name'].'</td>
                    <td>' .$candidateRow['partylist_position']. '</td>';
                    $party_list_id = $candidateRow['party_list_id'];
                    $partylistSql = "SELECT * FROM party_lists WHERE id = '$party_list_id'";
                    $partylistResult = mysqli_query($connection, $partylistSql);
                    $partylistFetchedData = mysqli_fetch_assoc($partylistResult);
                    echo '<td>'
                     .$partylistFetchedData['name']. '</td>
                    <!-- <td>
                       <a href="edit-candidate.php?id='.$candidateRow["id"].'"><button>Edit</button></a>
                     </td> -->
                    <td colspan="2">
                      <a onClick="return confirm(\'Proceed to Delete?\');" href="delete-candidate.php?id=' . $candidateRow["id"] . '" title="Delete"><button>Delete</button></a></td>
                  </tr>';
              }
          } else {
            echo "<tr><td colspan='5'><center>Register a candidate first.</center></td></tr>";
        }
          ?>
          </tbody>
        </table>
      </div>
            
    </main>
  </body>
</html>