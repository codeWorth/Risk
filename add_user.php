<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Connecting to database... <br/>";
$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

echo "Connected to database. <br/>";

$username = $_GET['name'];
$password = $_GET['pass']; 

if ($username != "" and $password != "") {
	$stmt = $accounts->prepare("SELECT * FROM players WHERE `user_name`= ?");
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$me = $stmt->get_result();

	// if (mysqli_num_rows($me) != 0) {
	// 	echo "User already exists.";
	// } else {
	echo "Adding username and password to users list. <br/>";

	$stmt = $accounts->prepare("INSERT INTO players (user_name,user_password) VALUES(?, ?)");
	$stmt->bind_param('ss', $username, $password);
	$stmt->execute();
	$res = $stmt->get_result();

	echo "Added user to players.";
	echo $res;
	// }
} else {
	echo "Invalid username and password (cannot be empty).";
}

?>