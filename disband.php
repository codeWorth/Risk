<?php

if (!isset($_COOKIE['name']) || !isset($_COOKIE['pass']) || $_COOKIE['name'] == "" || $_COOKIE['pass'] == "") {
	exit();
}

$db = mysqli_connect("localhost", "risk_game", "", "riskdb");

$gid = $_POST['gid'];

$stmt = $db->prepare("SELECT `user_id` FROM players WHERE `user_name`=? AND `user_password`=?;");
$stmt->bind_param('ss', $_COOKIE['name'], $_COOKIE['pass']);
$stmt->execute();
$pid = mysqli_fetch_row($stmt->get_result())[0];

$stmt = $db->prepare("SELECT `player_ids` FROM games WHERE `game_id`=?");
$stmt->bind_param('s', $gid);
$stmt->execute();
$game_pids = explode(",", mysqli_fetch_row($stmt->get_result())[0]);

if ($game_pids[0] != $pid) {
	exit();
}

$players = count($game_pids) - 1;
for ($i = 0; $i < $players; $i++) {
	$this_pid = $game_pids[$i];
	$stmt = $db->prepare("SELECT `user_games`,`current_game` FROM players WHERE `user_id`=?");
	$stmt->bind_param('s', $this_pid);
	$stmt->execute();
	$result = mysqli_fetch_row($stmt->get_result());
	$player_gids = explode(",", $result[0]);
	$current_gid = $result[1];
	if ($current_gid == $gid) {
		$current_gid = 0;
	}

	$gid_index = array_search($gid, $player_gids);
	array_splice($player_gids, $gid_index, 1);
	$new_gids = implode(",", $player_gids);
	if (count($player_gids) > 1) {
		$new_gids .= ",";
	}

	$stmt = $db->prepare("UPDATE players SET `user_games`=?,`current_game`=? WHERE `user_id`=?");
	$stmt->bind_param('sss', $new_gids, $current_gid, $this_pid);
	$stmt->execute();
}

$stmt = $db->prepare("DELETE FROM games WHERE `game_id`=?");
$stmt->bind_param('s', $gid);
$stmt->execute();

?>
