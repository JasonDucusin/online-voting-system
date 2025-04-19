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

$partylistId = $_GET['id'];

$deleteSql = "DELETE FROM `party_lists` WHERE `id` = '$partylistId'";
mysqli_query($connection, $deleteSql);

header("location: view-partylist.php");
exit;
?>