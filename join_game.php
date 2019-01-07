<?php

if (!isset($_COOKIE['name']) || !isset($_COOKIE['pass']) || $_COOKIE['name'] == "" || $_COOKIE['pass'] == "") {
	echo "notexist";
	exit();
}

$db = mysqli_connect("localhost", "risk_game", "", "riskdb");

$name = $_POST["name"];
$pass = $_POST["pass"];

$stmt = $db->prepare("SELECT `user_id` FROM players WHERE `user_name`=? AND `user_password`=?;");
$stmt->bind_param('ss', $_COOKIE['name'], $_COOKIE['pass']);
$stmt->execute();
$uid = mysqli_fetch_row($stmt->get_result())[0];

$stmt = $db->prepare("SELECT `player_ids`,`wanted_players`,`game_id`,`game_ready`,`auto_start` FROM games WHERE `game_name`=? AND `game_password`=?;");
$stmt->bind_param('ss', $name, $pass);
$stmt->execute();
$out = $stmt->get_result();

if (mysqli_num_rows($out) == 0) {
	echo "notexist";
} else {

	$result = mysqli_fetch_row($out);

	$players = $result[0];
	$player_a = explode(",", $players);

	if ($result[3] == "1") {
		echo "notexist";
	} else if (in_array($uid, $player_a)) {
		echo "already";
	} else {

		$player_count = sizeof($player_a) - 1;
		$max_players = intval($result[1]);
		$game_id = $result[2];
		$auto_start = $result[4] == "1";

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

			if ($player_count == ($max_players - 1) && $auto_start) {

				echo "STARTING";

				$p_ids = explode(",", $players);

				$order = array();
				for ($i = 0; $i < count($p_ids)-1; $i++) {
					$order[] = $p_ids[$i];
				}
				shuffle($order);

				$game_data = "0||" . implode(",", $order) . "||";

				for ($i = 0; $i < 41; $i++) {
					$game_data .= "-1,";
				}
				$game_data .= "-1||";

				for ($i = 0; $i < 41; $i++) {
					$game_data .= "0,";
				}
				$game_data .= "0||0||";

				$troopsPer = 0;
				$n_players = count($p_ids)-1;
				if ($n_players < 3) {
					exit();
				} else if ($n_players == 3) {
					$troopsPer = 35;
				} else if ($n_players == 4) {
					$troopsPer = 30;
				} else if ($n_players == 5) {
					$troopsPer = 25;
				} else if ($n_players == 6) {
					$troopsPer = 20;
				} else {
					exit();
				}
				for ($i = 0; $i < $n_players-1; $i++) {
					$game_data .= $troopsPer . ",";
				}
				$game_data .= $troopsPer . "||";

				for ($i = 0; $i < 41; $i++) {
					$game_data .= "-1,";
				}
				$game_data .= "-1";

				$stmt = $db->prepare("UPDATE games SET `game_data`=? WHERE `game_id`=?;");
				$stmt->bind_param('ss', $game_data, $game_id);
				$stmt->execute();

				$stmt = $db->prepare("UPDATE games SET `game_ready`=true WHERE `game_id`=?;");
				$stmt->bind_param('s', $game_id);
				$stmt->execute();

			}
		} else {
			echo "filled";
		}
	}

}

?>