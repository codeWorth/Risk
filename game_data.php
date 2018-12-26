<?php

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$stmt = $db->prepare("SELECT `game_id`,`game_name`,`game_password`,`game_ready`,`wanted_players`,`player_ids` FROM games;");
$stmt->execute();
$result = $stmt->get_result();

$count = 0;
while ($row = mysqli_fetch_row($result) and $count < 100) {
	if ($row[2] == "") {
		$row[2] = "0";
	} else {
		$row[2] = "1";
	}

	echo implode("||", $row);
	echo "~~";
	$count += 1;
}

?>