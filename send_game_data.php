<?php

$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

$gid = $_POST['gid'];
$data = $_POST['data'];

$stmt = $db->prepare("UPDATE games SET `game_data`=? WHERE `game_id`=?;");
$stmt->bind_param('ss', $data, $gid);
$stmt->execute();

?>