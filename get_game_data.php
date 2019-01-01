<?php

$db = mysqli_connect("localhost", "risk_game", "", "riskdb");

$gid = $_POST['gid'];

$stmt = $db->prepare("SELECT * FROM games WHERE `game_id`=?;");
$stmt->bind_param('s', $gid);
$stmt->execute();
echo $uid . "||";
echo implode("||", mysqli_fetch_row($stmt->get_result()));

?>
