<?php

echo "Connecting to database... <br/>";
$db = mysqli_connect("localhost", "risk_game", "PLACEHOLDER", "riskdb");

echo "Connected to database <br/>";

$username = $_GET['name'];
$password = $_GET['pass']; 

if ($username != "" and $password != "") {
	$stmt = $accounts->prepare("SELECT * FROM players WHERE `user_name`= ?");
	$stmt->bind_param('s', $username);
	$stmt->execute();
	$me = $stmt->get_result();

	if (mysqli_num_rows($me) != 0) {
		echo "User already exists.";
	} else {
		$stmt = $accounts->prepare("INSERT INTO players (user_name,user_password) VALUES(?, ?)");
		$stmt->bind_param('ss', $username, $password);
		$stmt->execute();

		echo "Added user to players.";
	}
}

?>