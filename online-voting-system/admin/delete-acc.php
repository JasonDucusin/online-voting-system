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

$accountId = $_GET['id'];

$accountCheck = "SELECT privilege FROM `accounts` WHERE `id` = '$accountId'";
$accountQuery = mysqli_query($connection, $accountCheck);

$privilege =  mysqli_fetch_assoc($accountQuery)['privilege'];

$deleteSql = "DELETE FROM `accounts` WHERE `id` = '$accountId'";
mysqli_query($connection, $deleteSql);

header("location: student-acc.php");
exit;
// if ($privilege == 'student') {
//   header("location: students");
// } else {
//   header("location: teacher-acc");
// }
?>