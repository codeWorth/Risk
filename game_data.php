<?php

if (!isset($_COOKIE['name']) || !isset($_COOKIE['pass']) || $_COOKIE['name'] == "" || $_COOKIE['pass'] == "") {
	echo "zoop";
	exit();
}

$db = mysqli_connect("localhost", "risk_game", "", "riskdb");

$stmt = $db->prepare("SELECT `user_id`,`user_games` FROM players WHERE `user_name`=? AND `user_password`=?;");
$stmt->bind_param('ss', $_COOKIE['name'], $_COOKIE['pass']);
$stmt->execute();
$result = mysqli_fetch_row($stmt->get_result());
$uid = $result[0];
$game_ids = explode(",", $result[1]);

$stmt = $db->prepare("SELECT `game_id`,`game_name`,`game_password`,`game_ready`,`wanted_players`,`player_ids`,`auto_start` FROM games;");
$stmt->execute();
$result = $stmt->get_result();

$count = 0;
while ($row = mysqli_fetch_row($result) and $count < 100) {
	if ($row[2] == "") {
		$row[2] = "0";
	} else {
		$row[2] = "1";
	}

	$player_ids = explode(",", $row[5]);
	$num_players = count($player_ids) - 1;
	if (in_array($row[0], $game_ids)) {
		$row[5] = "1";
	} else {
		$row[5] = "0";
	}

	echo implode("||", $row) . "||" . $num_players;

	if ($player_ids[0] == $uid) {
		echo "||1";
	} else {
		echo "||0";
	}

	echo "~~";
	$count += 1;
}

?>