<?php

if (!isset($_COOKIE['name']) || !isset($_COOKIE['pass']) || $_COOKIE['name'] == "" || $_COOKIE['pass'] == "") {
	exit();
}

$gid = $_POST["gid"];

$db = mysqli_connect("localhost", "risk_game", "", "riskdb");

$stmt = $db->prepare("UPDATE players SET `current_game`=? WHERE `user_name`=? AND `user_password`=?;");
$stmt->bind_param('sss', $gid, $_COOKIE['name'], $_COOKIE['pass']);
$stmt->execute();

?>