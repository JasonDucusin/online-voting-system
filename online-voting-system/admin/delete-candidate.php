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

$candidateId = $_GET['id'];

$candidateCheck = "SELECT privilege FROM `accounts` WHERE `id` = '$candidateId'";
$candidateQuery = mysqli_query($connection, $candidateCheck);

$deleteSql = "DELETE FROM `candidates` WHERE `id` = '$candidateId'";
mysqli_query($connection, $deleteSql);

header("location: view-candidates.php");
exit;
// if ($privilege == 'student') {
//   header("location: students");
// } else {
//   header("location: teacher-acc");
// }
?>