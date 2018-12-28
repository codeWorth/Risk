<?php

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$gid = $_POST["gid"];

$stmt = $db->prepare("UPDATE games SET `game_ready`=true WHERE `game_id`=?;");
$stmt->bind_param('s', $gid);
$stmt->execute();


$game_data = "0||";

$stmt = $db->prepare("SELECT `player_ids` FROM games WHERE `game_id`=?;");
$stmt->bind_param('s', $gid);
$stmt->execute();
$p_ids = explode(",", mysqli_fetch_row($stmt->get_result())[0]);

$order = array();
for ($i = 0; $i < count($p_ids)-1; $i++) {
	$order[] = $p_ids[$i];
}
shuffle($order);

$game_data .= implode(",", $order) . "||";

for ($i = 0; $i < 41; $i++) {
	$game_data .= "-1,";
}
$game_data .= "-1||";

for ($i = 0; $i < 41; $i++) {
	$game_data .= "0,";
}
$game_data .= "0";

$stmt = $db->prepare("UPDATE games SET `game_data`=? WHERE `game_id`=?;");
$stmt->bind_param('ss', $game_data, $gid);
$stmt->execute();

?>