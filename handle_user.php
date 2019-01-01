<?php

$db = mysqli_connect("localhost", "risk_game", "", "riskdb");

$username = $_POST['name'];
$password = $_POST['pass']; 

if ($username == "" || $password == "") {
	echo "Please supply a username and password.";
	exit();
}

if (isset($_POST['new'])) {

	$stmt = $db->prepare("SELECT * FROM players WHERE `user_name`= ?;");
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$me = $stmt->get_result();

	if (mysqli_num_rows($me) != 0) {
		echo "This username is already taken.";
	} else {
		$stmt = $db->prepare("INSERT INTO players (user_name,user_password) VALUES(?, SHA2(?, 256));");
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();

		setcookie('name', $username, false, "/");
		setcookie('pass', hash('sha256', $password), false, "/");

		header('Location: lobby.html');
		exit();

	}

} else if (isset($_POST['login'])) {
	$stmt = $db->prepare("SELECT `user_password` FROM players WHERE `user_name`=? AND `user_password`=SHA2(?, 256);");
	$stmt->bind_param('ss', $username, $password);
	$stmt->execute();
	$me = $stmt->get_result();

	if (mysqli_num_rows($me) == 0) {
		echo "Incorrect username and password.";
	} else {
		$row = mysqli_fetch_row($me);

		setcookie('name', $username, false, "/");
		setcookie('pass', $row[0], false, "/");

		header('Location: lobby.html');
		exit();
	}

} else {
	echo "Error";
}

?>