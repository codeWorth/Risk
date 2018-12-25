<!DOCTYPE html>
<html>
	<body>
		<h1><?php

				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);

				$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");


				$username = $_POST['name'];
				$password = $_POST['pass']; 

				if ($username != "" and $password != "") {
					if (isset($_POST['new'])) {

						$stmt = $db->prepare("SELECT * FROM players WHERE `user_name`= ?");
						$stmt->bind_param('s', $username);
						$stmt->execute();
						$me = $stmt->get_result();

						if (mysqli_num_rows($me) != 0) {
							echo "exists";
						} else {
							$stmt = $db->prepare("INSERT INTO players (user_name,user_password) VALUES(?, ?)");
							$stmt->bind_param('ss', $username, $password);
							$stmt->execute();

							$stmt = $db->prepare("SELECT `user_id` FROM players WHERE `user_name`=? AND `user_password`=?");
							$stmt->bind_param('ss', $username, $password);
							$stmt->execute();
							$me = $stmt->get_result();

							echo mysqli_fetch_row($me)[0];
						}

					} else if (isset($_POST['login'])) {
						echo "Returning user <br/>";

						$stmt = $db->prepare("SELECT `user_id` FROM players WHERE `user_name`=? AND `user_password`=?");
						$stmt->bind_param('ss', $username, $password);
						$stmt->execute();
						$me = $stmt->get_result();

						if (mysqli_num_rows($me) == 0) {
							echo "-1";
						} else {
							echo mysqli_fetch_row($me)[0];
						}

					} else {
						echo "invalid";
					}
				} else {
					echo "invalid";
				}

		?></h1>
	</body>
</html>