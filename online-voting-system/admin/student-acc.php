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

  $sql = "SELECT * FROM accounts WHERE privilege='student'";
  $result = mysqli_query($connection, $sql);
  
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
          <li class="t-li"><a href="student-acc.php" class="t-nav-links active">Voter Accounts</a></li>
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
            <div class="table-container voter-table">

                <table>
                    <caption>Accounts</caption>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Password</th>
                            <!-- <th>Year Level & Secion</th> -->
                            <th colspan="3" class="actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            // output data of each row
                            while($row = mysqli_fetch_assoc($result)) {
                                echo '<tr><td>' .$row['surname'].', '.$row['given_name']. '</td><td>' .$row['username']. '</td><td>' .$row['password']. '</td>
                                <td><a href="edit-student.php?id='.$row["id"].'">
                                    <button>Edit</button>
                                </a>
                                </td>
                                <td><a href="student-acc-settings.php?username='.$row["username"].'">
                                    <button>Settings</button>
                                </a>
                                </td>
                                <td><a onClick="return confirm(\'Proceed to Delete?\');" href="delete-acc.php?id=' . $row["id"] . '" title="Delete">
                                    <button>Delete</button>
                                </a></td></tr>';
                            }
                        } else {
                            echo "<tr><td colspan='6'><center>Create a voter account first.</center></td></tr>";
                        }
                        ?>
                </tbody>
            </table>
        </div>
        </main>
    </body>
    </html>