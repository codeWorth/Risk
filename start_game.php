<?php

if (!isset($_COOKIE['name']) || !isset($_COOKIE['pass']) || $_COOKIE['name'] == "" || $_COOKIE['pass'] == "") {
	exit();
}

$db = mysqli_connect("localhost", "risk_game", "", "riskdb");

$gid = $_POST["gid"];

$stmt = $db->prepare("SELECT `user_id` FROM players WHERE `user_name`=? AND `user_password`=?;");
$stmt->bind_param('ss', $_COOKIE['name'], $_COOKIE['pass']);
$stmt->execute();
$result = mysqli_fetch_row($stmt->get_result());
$uid = $result[0];

$stmt = $db->prepare("SELECT `player_ids` FROM games WHERE `game_id`=?;");
$stmt->bind_param('s', $gid);
$stmt->execute();
$p_ids = explode(",", mysqli_fetch_row($stmt->get_result())[0]);

echo $p_ids[0];
echo " --- ";
echo $uid;

if ($p_ids[0] != $uid) {
	exit();
}

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
$game_data .= $troopsPer;

$stmt = $db->prepare("UPDATE games SET `game_data`=? WHERE `game_id`=?;");
$stmt->bind_param('ss', $game_data, $gid);
$stmt->execute();

$stmt = $db->prepare("UPDATE games SET `game_ready`=true WHERE `game_id`=?;");
$stmt->bind_param('s', $gid);
$stmt->execute();

?>
