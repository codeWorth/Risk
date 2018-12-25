<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Connecting to database... <br/>";
$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");


$username = $_GET['name'];
$password = $_GET['pass']; 

if ($username != "" and $password != "") {
	if (isset($_GET['new'])) {
		echo "New user. <br/>";

		$stmt = $db->prepare("SELECT * FROM players WHERE `user_name`= ?");
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$me = $stmt->get_result();

		if (mysqli_num_rows($me) != 0) {
			echo "User already exists.";
		} else {
			$stmt = $db->prepare("INSERT INTO players (user_name,user_password) VALUES(?, ?)");
			$stmt->bind_param('ss', $username, $password);
			$stmt->execute();
			$res = $stmt->get_result();

			echo "Added user to players.";
			echo $res;
		}
	} else if (isset($_GET['login'])) {
		echo "Returning user <br/>";

		$stmt = $db->prepare("SELECT `user_id` FROM players WHERE `user_name`=? AND `user_password`=?");
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();
		$me = $stmt->get_result();

		if (mysqli_num_rows($me) == 0) {
			echo "User does not exist.";
		} else {
			echo mysqli_fetch_row($me)[0];
		}
	} else {
		echo "Unknown method.";
	}
} else {
	echo "Invalid username and password (cannot be empty).";
}


?>