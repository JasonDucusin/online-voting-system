<?php
// error_reporting(0);

define('WEBSITE_TITLE', 'Online Voting');

// Login to phpmyadmin
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');

date_default_timezone_set('Asia/Manila');

$loginConn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

//Creates database if it doesn't exist
$createDB = "CREATE DATABASE IF NOT EXISTS `online_voting`";
mysqli_query($loginConn, $createDB);

//Connects to database
define('DB_NAME', 'online_voting');
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($connection === false) {
  die('ERROR: Could not connect. ' . mysqli_connect_error());
}

// creates accounts table
$createAccountsTable = "CREATE TABLE IF NOT EXISTS `online_voting`.`accounts` 
(`id` INT NOT NULL AUTO_INCREMENT, 
`username` VARCHAR(255) NOT NULL, 
`password` VARCHAR(255) NOT NULL, 
`privilege` VARCHAR(255) NOT NULL, 
`given_name` VARCHAR(255) NOT NULL,
`surname` VARCHAR(255) NOT NULL, 
-- `year_level` VARCHAR(255) NOT NULL, 
-- `section` VARCHAR(255) NOT NULL,
`is_done_voting` VARCHAR(255) NOT NULL , 
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
`date_edited` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
PRIMARY KEY (`id`), 
UNIQUE (`username`)
) ENGINE = InnoDB;";
mysqli_query($connection, $createAccountsTable);

// Creates the "party_lists" table in the database
$createPartyListsTable = "CREATE TABLE IF NOT EXISTS `online_voting`.`party_lists` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_edited` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  UNIQUE (`name`)
  ) ENGINE = InnoDB;";
mysqli_query($connection, $createPartyListsTable);

// Creates the "positions" table in the database
$createPositionsTable = "CREATE TABLE IF NOT EXISTS `online_voting`.`positions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `position` VARCHAR(255) NOT NULL ,
  `maximum`  INT NOT NULL,
  `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_edited` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  UNIQUE (`position`)
  ) ENGINE = InnoDB;";
mysqli_query($connection, $createPositionsTable);

// Creates the "candidates" table in the database
$createCandidatesTable = "CREATE TABLE IF NOT EXISTS `online_voting`.`candidates` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `student_username` VARCHAR(255) NOT NULL ,
  `partylist_position` VARCHAR(255) NOT NULL ,
  `party_list_id` INT NOT NULL ,
  `votes` INT NOT NULL DEFAULT '0' ,
  `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_edited` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`),
  UNIQUE (`student_username`) ,
  CONSTRAINT `fk_candidates_accounts` FOREIGN KEY (`student_username`) REFERENCES `accounts`(`username`) ON DELETE CASCADE ON UPDATE CASCADE ,
  CONSTRAINT `fk_candidates_party_list` FOREIGN KEY (`party_list_id`) REFERENCES `party_lists`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_candidates_positions` FOREIGN KEY (`partylist_position`) REFERENCES `positions`(`position`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE = InnoDB;";
mysqli_query($connection, $createCandidatesTable);


$accountsSql = "SELECT * FROM `accounts`";
$accountsResult = mysqli_query($connection, $accountsSql);

if (mysqli_num_rows($accountsResult) == 0) {
  $addAdminSql = "INSERT INTO `accounts` (`username`, `password`, `privilege`) VALUES ('admin', 'admin', 'admin')";
  mysqli_query($connection, $addAdminSql);

  // $addStudentSql = "INSERT INTO `accounts` (`username`, `password`, `privilege`) VALUES ('student', 'student', 'student')";
  // mysqli_query($connection, $addStudentSql);

  // $addUserSql = "INSERT INTO `accounts` (`username`, `password`, `privilege`) VALUES ('user', 'user', 'user')";
  // mysqli_query($connection, $addUserSql);
}

?>