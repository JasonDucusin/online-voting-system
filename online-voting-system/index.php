<?php
session_start();

require_once 'assets/dbhandler.php';
require 'functions.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
		header('location: loggedin.php');
		exit;
}

$username = $password = '';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (empty($username)) {
		$errors['username'] = 'Please enter username.';
	}

	if (empty($password)) {
		$errors['password'] = 'Please enter your password.';
	}

	if (empty($errors)) {
		$sql = "SELECT id, username, password, privilege, given_name, surname, is_done_voting FROM `accounts` WHERE username = ?";

		if (!($stmt = mysqli_prepare($connection, $sql))) {
			header('location: index.php?error=stmtpreparefailed');
			exit();
		}

		mysqli_stmt_bind_param($stmt, "s", $param_username);
		$param_username = $username;

		if (!mysqli_stmt_execute($stmt)) {
			header('location: index.php?error=stmtexecutefailed');
			exit();
		}

		mysqli_stmt_store_result($stmt);

		if (!mysqli_stmt_num_rows($stmt)) {
			header("location: index.php?error=invaliduser");
			exit();
		}

		mysqli_stmt_bind_result($stmt, $id, $username, $passwordDB, $privilege, $given_name, $surname, $is_done_voting);

		if (!mysqli_stmt_fetch($stmt)) {
			header('location: index.php?error=stmtfetchfailed');
			exit();
		}

		if ($password == $passwordDB) {
			session_start();

			$_SESSION['loggedin'] = true;
			$_SESSION['id'] = $id;
			$_SESSION['username'] = $username;
			$_SESSION['privilege'] = $privilege;
			$_SESSION['given_name'] = $given_name;
			$_SESSION['surname'] = $surname;
			$_SESSION['is_done_voting'] = $is_done_voting;


			header('location: loggedin.php');
		} else {
			$errors['login'] = 'Invalid username or password.';
		}

		mysqli_stmt_close($stmt);
	}

    mysqli_close($connection);
} else {
	$stmtfails = ['stmtpreparefailed', 'stmtexecutefailed', 'stmtfetchfailed'];
	
	if (isset($_GET['error']) && in_array($_GET['error'], $stmtfails)) {
		$errors['login'] = 'Something Went wrong. Please try again later.';
	} elseif (isset($_GET['error']) && $_GET['error'] == 'invaliduser') {
		$errors['login'] = "Account doesn't exist.";
	}
}
?>

<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title><?= WEBSITE_TITLE ?></title>
			<link rel="stylesheet" href="assets/login.css">
			<link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
		</head>
		<body>
			<div class="form-box">
				
				<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?> " autocomplete="off" method="post">
					<h2 class="form-h">Online Voting System</h2>
					
					<div class="content">
						<div class="input-box">
							<label for="username">Username</label>
							<input type="text" name="username" value="<?= $username ?>" class="form-input">
							<?php if (isset($errors['username'])): ?>
								<span class="error"><?= "* {$errors['username']}" ?></span>
								<?php endif; ?>
							</div>
							<div class="input-box">
								<label for="password">Password</label>
								<input type="password" name="password" value="<?= $password ?>" class="form-input">
								<?php if (isset($errors['password'])): ?>
									<span class="error"><?= "* {$errors['password']}" ?></span>
									<?php endif; ?>
								</div>
								<div class="button-container">
									<button type="submit" name="login" class="form-button">Login</button>
								</div>
							</div>
							<?php if (isset($errors['login'])): ?>
								<div class="error"><?= $errors['login'] ?></div>
								<?php endif; ?>
							</form>
						</div>
						<iframe src='https://my.spline.design/droid-48d845acde6e4acc525e2d943702adc8/' frameborder='0' width='100%' height='100%'></iframe>
		</body>
	</html>