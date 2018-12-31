<?php

if (!isset($_COOKIE['name']) || !isset($_COOKIE['pass']) || $_COOKIE['name'] == "" || $_COOKIE['pass'] == "") {
	exit();
}

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$game_name = $_POST['name'];
$game_pass = $_POST['pass'];
$max_players = $_POST['players'];
$stmt = $db->prepare("SELECT `user_id` FROM players WHERE `user_name`=? AND `user_password`=?;");
$stmt->bind_param('ss', $_COOKIE['name'], $_COOKIE['pass']);
$stmt->execute();
$p_id = mysqli_fetch_row($stmt->get_result())[0];
$p_list = $p_id . ",";

if ($game_name != "") {
	$stmt = $db->prepare("INSERT INTO games (`game_name`,`game_password`,`wanted_players`,`player_ids`) VALUES(?,?,?,?);");
	$stmt->bind_param('ssss', $game_name, $game_pass, $max_players, $p_list);
	$stmt->execute();
	$game_id = mysqli_fetch_row($db->query("SELECT LAST_INSERT_ID();"))[0];

	$stmt = $db->prepare("SELECT `user_games` FROM players WHERE `user_id`=?;");
	$stmt->bind_param('s', $p_id);
	$stmt->execute();
	$current_games = mysqli_fetch_row($stmt->get_result())[0];

	$current_games = $current_games . $game_id . ",";

	$stmt = $db->prepare("UPDATE players SET `user_games`=? WHERE `user_id`=?;");
	$stmt->bind_param('ss', $current_games, $p_id);
	$stmt->execute();
}

?>