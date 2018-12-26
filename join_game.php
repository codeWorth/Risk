<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$name = $_POST["name"];
$pass = $_POST["pass"];
$uid = $_POST["uid"];

$stmt = $db->prepare("SELECT `player_ids`,`wanted_players`,`game_id` FROM games WHERE `game_name`=? AND `game_password`=?;");
$stmt->bind_param('ss', $name, $pass);
$stmt->execute();
$out = $stmt->get_result();

if (mysqli_num_rows($out) == 0) {
	echo "notexist";
} else {

	$result = mysqli_fetch_row($out);

	$players = $result[0];
	$player_a = explode(",", $players);

	if (in_array($uid, $player_a)) {
		echo "already";
	} else {

		$player_count = sizeof($player_a) - 1;
		$max_players = intval($result[1]);
		$game_id = $result[2];

		if ($player_count < $max_players) {
			$players = $players . $uid . ",";

			$stmt = $db->prepare("UPDATE games SET `player_ids`=? WHERE `game_id`=?;");
			$stmt->bind_param('ss', $players, $game_id);
			$stmt->execute();

			$stmt = $db->prepare("SELECT `user_games` FROM players WHERE `user_id`=?;");
			$stmt->bind_param('s', $uid);
			$stmt->execute();
			$current_games = mysqli_fetch_row($stmt->get_result())[0];

			$current_games = $current_games . $game_id . ",";

			$stmt = $db->prepare("UPDATE players SET `user_games`=? WHERE `user_id`=?;");
			$stmt->bind_param('ss', $current_games, $uid);
			$stmt->execute();
		} else {
			echo "filled";
		}
	}

}

?>