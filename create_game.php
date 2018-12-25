<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Connected. <br/>";

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$game_name = $_GET['name'];
$game_pass = $_GET['pass'];
$max_players = $_GET['players'];

if ($game_name != "") {
	echo "Adding. <br/>";
	echo $game_name;
	echo "<br/>";
	echo $game_pass;
	echo "<br/>";
	echo $max_players;
	echo "<br/>";
	$stmt = $db->prepare("INSERT INTO games (`game_name`,`game_password`,`wanted_players`) VALUES(?,?,?);");
	$stmt->bind_param('sss', $username, $game_pass, $max_players);
	$stmt->execute();
	$res = $stmt->get_result();
	echo $res;

	echo "   BAM  ";
	echo mysqli_error($db);
}

?>