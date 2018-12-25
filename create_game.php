<?php

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$game_name = $_POST['name'];
$game_pass = $_POST['pass'];
$max_players = $_POST['players'];

if ($game_name != "") {
	$stmt = $db->prepare("INSERT INTO games (`game_name`,`game_password`,`wanted_players`) VALUES(?,?,?);");
	$stmt->bind_param('sss', $game_name, $game_pass, $max_players);
	$stmt->execute();
}

?>