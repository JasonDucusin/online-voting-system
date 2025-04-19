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

$positionId = $_GET['id'];

$deleteSql = "DELETE FROM `positions` WHERE `id` = '$positionId'";
mysqli_query($connection, $deleteSql);

header("location: view-positions.php");
exit;
?>