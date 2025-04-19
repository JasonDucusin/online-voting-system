<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('location: ../');
  exit;
}
if ($_SESSION['privilege'] != 'student') {
    header('location: ../');
    exit;
  }

  if ($_SESSION["is_done_voting"] == 'true') {
    header("location: result.php");
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

$vote_error = false;
$vote_success = false;

$voter_username = $_SESSION["username"];

$positions_array = array();
$positions_max_array = array();

$positions = quick_query("SELECT position, maximum FROM positions ORDER BY id  ASC");

while ($position = mysqli_fetch_assoc($positions)) {
  $name = $position["position"];
  array_push($positions_array, $name);
  
  $max = $position["maximum"];
  array_push($positions_max_array, $max);
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
  </head>
  <body>
    <header>
      <h1 class="page-head">Ballot</h1>
      <nav class="top-nav">
        <ul class="t-ul">
          <li class="t-li t-li-1"><a href="./" class="t-nav-links active">Vote</a></li>
          <!-- <li class="t-li"><a href="vote" class="t-nav-links">Vote</a></li> -->
        </ul>
        <ul class="t-ul">
          <li class="t-li"><a href="../assets/logout.php" class="t-nav-links">Logout</a></li>               
        </ul>
      </nav>
    </header>
    <main>
    <?php
      if (count($positions_array) > 0) {
        // echo "<h1>Ballot</h1>";
        echo '<div class="form-box voter-form">';
        echo '<form autocomplete="off" action="" method="POST">';
        for ($i = 0; $i < count($positions_array); $i++) {
          $position_name = $positions_array[$i];
          $position_max = $positions_max_array[$i];

          // $counter = 0;

          // echo '<div class="list-box-container">';
          // echo '<div class="list-box">';
          echo
          "<fieldset>
            <h3>$position_name (Vote for $position_max)</h3>";

          $position_query = quick_query("SELECT student_username, partylist_position, party_list_id, votes FROM candidates WHERE partylist_position = '$position_name' ORDER BY id ASC");

          while ($position_row = mysqli_fetch_assoc($position_query)) {
            $username = $position_row["student_username"];
            $party_list_id = $position_row["party_list_id"];
            $votes = $position_row["votes"];

            $name_sql = get_query_result("SELECT given_name, surname FROM accounts WHERE username = '$username'");

            $name = $name_sql["given_name"] . " " . $name_sql["surname"];
            $party_list = get_query_result("SELECT name FROM party_lists WHERE id = '$party_list_id'")["name"];

            // echo
            // "<div>
            //   <input type=\"checkbox\" id=\"$username\" name=\"$position_name\" value=\"$username\">
            //   <label for=\"$username\">$name ($party_list)</label>
            // </div>";
            echo
            "<div>
              <input type=\"checkbox\" id=\"$username\" name=\"{$position_name}[]\" value=\"$username\">
              <label for=\"$username\">$name ($party_list)</label>
            </div>";
          }
          
          echo "</fieldset>";
          
          if (isset($_POST["submit_ballot"])) {
            if (!empty($_POST[$position_name])) {
              $$position_name = $_POST[$position_name];
    
              if (count($$position_name) > $position_max) {
                echo "Please select only $position_max or less.";
                $vote_error = true;
              } else if (count($$position_name) <= $position_max) {
                foreach($$position_name as $selected) {
                  $votes = get_query_result("SELECT votes FROM candidates WHERE student_username = '$selected'")["votes"];
                  $votes++;

                  quick_query("UPDATE candidates SET votes = '$votes' WHERE student_username = '$selected'");
                }
                quick_query("UPDATE accounts SET is_done_voting = 'true' WHERE username = '$voter_username'");

                $vote_success = true;
              }
            }
          }

          // echo "</div>";
          // echo "</div>";

          if ($i == (count($positions_array) - 1)) {
            echo '<button type="submit" id="submit-ballot" name="submit_ballot" onCLick="return confirm(\'Are you sure to submit this ballot?\');" class="form-button">Submit</button>';
            echo "</div>";
          }
        }

        echo "</form>";
      }
    ?>
    </main>
  </body>
</html>
<?php
if ($vote_error == false && $vote_success == true) {
  $_SESSION["is_done_voting"] = "true";
  header("location: result.php");
}
?>
