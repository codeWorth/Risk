<?php

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$gid = $_POST['gid'];
$uid = $_POST['uid'];

$stmt = $db->prepare("SELECT `user_games` FROM players WHERE `user_id`=?");
$stmt->bind_param('s', $uid);
$stmt->execute();
$user = $stmt->get_result();

if (mysqli_num_rows($user) == 0) {
	echo "-1";
} else {

	$games = explode(",", mysqli_fetch_row($user)[0]);

	if (!in_array($gid, $games)) {
		echo "-1";
	} else {
		$stmt = $db->prepare("SELECT * FROM games WHERE `game_id`=?;");
		$stmt->bind_param('s', $gid);
		$stmt->execute();
		echo $uid . "||";
		echo implode("||", mysqli_fetch_row($stmt->get_result()));
	}

}

?>