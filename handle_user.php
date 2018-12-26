<!DOCTYPE html>
<html>
	<head>
		<title>Risk Online - Game Viewer</title>
		<style>

			html, body {
				height: 100%;
				overflow: hidden;
				margin: 0px;
			}

			#content {
				width: 800px;
				margin:auto;
				height: 100%;
			}

			#refresh {
				position: absolute;
				top: 33px;
				width: 60px;
				left: calc(50% - 30px);
			}

			#logout {
				position: absolute;
				top: 8px;
				width: 60px;
				left: calc(50% - 30px);
			}

			#browse {
				float:left;
				width:390px;
				height: 100%;
				margin: 0px;
			}

			#browse h1 {
				text-align: center;
				top: 10px;
				margin: 10px 0px 0px 0px;
				background-color: white;
			}

			#browse_list {
				width: 100%;
				height: calc(100% - 60px);
				overflow: scroll;
			}

			#joined {
				float:right;
				width:390px;
				height: 100%;
				margin: 0px;
			}

			#joined h1 {
				text-align: center;
				top: 10px;
				margin: 10px 0px 0px 0px;
				background-color: white;
			}

			#joined_list {
				width: 100%;
				height: calc(100% - 60px);
				overflow: scroll;
			}

			.list_item {
				width:370px;
				margin:10px;
				border:1px solid black;
				position: relative;
			}

			.list_item h2 {
				margin: 5px;
			}

			.list_item p {
				margin: 5px;
			}

			.list_item .mainbutton {
				position: absolute;
				top: 5px;
				right: 5px;
			}

			.list_item .secondbutton {
				position: absolute;
				top: 30px;
				right: 5px;
			}

			.list_item .playbutton {
				position: absolute;
				bottom: 5px;
				right: 5px;
			}

			#create_img {
				margin: 5px auto;
				display: block;
				border-radius: 37px;
				background-color: rgba(65, 65, 65, 0.1);
			}

			#create_img:hover {
				background-color: rgba(65, 65, 65, 0.2);
			}

			#create_img:active {
				box-shadow: 0 0 5px -1px rgba(0,0,0,0.6);
			}


			#game_create {
				position: absolute;
				top: 0px;
				left: 0px;
				width: 100%;
				height: 100%;
				background-color: rgba(64, 64, 64, 0.75);
				z-index: 10;
			}

			#game_create #center {
				margin: auto;
				margin-top: 50px;
				width: 300px;
				height: 335px;
				border: 1px solid black;
				background-color: white;
			}

			#game_create h2 {
				width: 100%;
				text-align: center;
				margin-bottom: 20px;
			}

			#game_create h4 {
				margin: auto;
				width: 100%;
				text-align: center;
				margin-bottom: 5px;
			}

			#game_create .input_box {
				margin-left: 67px;
				margin-right: 67px;
				width: 160px;
				margin-top: 0px;
				margin-bottom: 20px;
			}

			#game_create #players {
				margin: 0 auto;
				display: block;
				margin-bottom: 20px;
			}

			#game_create #create {
				width: 60px;
				margin: 0 auto;
				display: block;
			}

			#game_create #cancel {
				width: 60px;
				margin: 0 auto;
				display: block;
				margin-top: 10px;
			}



			#enter_password {
				position: absolute;
				top: 0px;
				left: 0px;
				width: 100%;
				height: 100%;
				background-color: rgba(64, 64, 64, 0.75);
				z-index: 10;
			}

			#enter_password #center {
				margin: auto;
				margin-top: 50px;
				width: 300px;
				height: 200px;
				border: 1px solid black;
				background-color: white;
				position: relative;
			}

			#enter_password h2 {
				width: 100%;
				text-align: center;
				margin-bottom: 20px;
			}

			#enter_password h4 {
				margin: auto;
				width: 100%;
				text-align: center;
				margin-bottom: 5px;
			}

			#enter_password .input_box {
				margin-left: 67px;
				margin-right: 67px;
				width: 160px;
				margin-top: 0px;
				margin-bottom: 30px;
			}

			#enter_password #incorrect {
				display: none;
				position: absolute;
				width: 100%;
				text-align: center;
				left: 0px;
				top: 100px;
				font-size: 10pt;
				color: red;
			}

			#enter_password #join {
				width: 60px;
				margin: 0 auto;
				display: block;
			}

			#enter_password #cancel {
				width: 60px;
				margin: 0 auto;
				display: block;
				margin-top: 10px;
			}

		</style>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	</head>

	<body>

		<div id="info" style="display: none;"><?php

			$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

			$username = $_POST['name'];
			$password = $_POST['pass']; 

			$user_id = $_POST['enc_pass'];

			if (isset($user_id)) {
				echo $user_id;
			} else {
				if ($username != "" and $password != "") {
					if (isset($_POST['new'])) {

						$stmt = $db->prepare("SELECT * FROM players WHERE `user_name`= ?");
						$stmt->bind_param('s', $username);
						$stmt->execute();
						$me = $stmt->get_result();

						if (mysqli_num_rows($me) != 0) {
							echo "exists";
						} else {
							$stmt = $db->prepare("INSERT INTO players (user_name,user_password) VALUES(?, SHA2(?, 256));");
							$stmt->bind_param('ss', $username, $password);
							$stmt->execute();

							$stmt = $db->prepare("SELECT `user_id`, `user_password` FROM players WHERE `user_name`=? AND `user_password`=SHA2(?, 256);");
							$stmt->bind_param('ss', $username, $password);
							$stmt->execute();
							$me = $stmt->get_result();

							$row = mysqli_fetch_row($me);

							echo $row[0];
							echo ",";
							echo $row[1];
						}

					} else if (isset($_POST['login'])) {
						$stmt = $db->prepare("SELECT `user_id`, `user_password` FROM players WHERE `user_name`=? AND `user_password`=SHA2(?, 256);");
						$stmt->bind_param('ss', $username, $password);
						$stmt->execute();
						$me = $stmt->get_result();

						if (mysqli_num_rows($me) == 0) {
							echo "-1";
						} else {
							$row = mysqli_fetch_row($me);
							echo $row[0];
							echo ",";
							echo $row[1];
						}

					} else {
						echo "invalid";
					}
				} else {
					echo "invalid";
				}
			}

		?></div>

		<div id="game_create">
			<div id="center">
				<h2>Create Game</h2>

				<form id="create_form">
					<h4>Lobby Name</h4>
					<input id="username" type="text" placeholder="Enter Name" name="name" class="input_box"> <br/>

					<h4>Lobby Password <br/> (Optional)</h4>
					<input id="password" type="password" placeholder="Enter Password" name="pass" class="input_box"> <br/>

					<h4>Maximum Players</h4>
					<input type="number" id="players" name="players" min="3" max="8" value="5">
				</form>
				<button id="create">Create</button>
				<button id="cancel" onclick="game_create_window.hide(); ">Cancel</button>
			</div>
		</div>

		<div id="enter_password">
			<div id="center">
				<h2>Join [Game Name]</h2>
				<h4>Enter Password</h4>
				<input id="password" type="password" placeholder="Enter Password" class="input_box"> <br/>
				<p id="incorrect">Incorrect Password</p>
				<button id="join">Join</button>
				<button id="cancel" onclick="password_enter_window.hide(); incorrect_text.hide();">Cancel</button>
			</div>
		</div>

		<div id="content">
			<button id="refresh">Refresh</button>
			<button id="logout" onclick="window.location.href = 'index.html';">Log Out</button>

			<div id="browse">
				<h1>Available Games</h1>

				<div id="browse_list">

				</div>
			</div>
			<div id="joined">
				<h1>Joined Games</h1>

				<div id="joined_list">
					
				</div>
			</div>
		</div>

		<script>

			var data = document.getElementById("info").textContent;

			if (data == "invalid") {
				window.location.replace("index.html");
			} else if (data == "-1") {
				document.write("Unknown username and password combination.");
			} else {
				var parts = data.split(",");
				var user_id = data[0];
				var enc_pass = data[1];
			}

			var game_create_window = $("#game_create");
			game_create_window.hide();

			var password_enter_window = $("#enter_password");
			var incorrect_text = $("#enter_password #incorrect");
			password_enter_window.hide();

			var selectBrowse = -1;

			var browse_list = $("#browse_list");
			var joined_list = $("#joined_list");

			var browse_names = [];
			var browse_pass = [];
			var browse_players = [];
			var browse_wanted_players = [];

			var joined_names = [];
			var joined_host = [];
			var joined_players = [];
			var joined_wanted_players = [];
			var joined_started = [];

			function joinSelected() {
				var data = {name:browse_names[selectBrowse], pass:$("#enter_password #password").val(), uid:user_id};
				$.post("join_game.php", data, function(data) {
					if (data === "filled") {
						alert("Game lobby already filled.");
						password_enter_window.hide();
						refresh();
					} else if (data === "already") {
						password_enter_window.hide();
						refresh();
					} else if (data === "notexist") {
						incorrect_text.show();
					} else {
						incorrect_text.hide();
						password_enter_window.hide();
						refresh();
					}
				});
			}

			function refresh() {
				browse_names = [];
				browse_pass = [];
				browse_players = [];
				browse_wanted_players = [];

				joined_names = [];
				joined_host = [];
				joined_players = [];
				joined_wanted_players = [];
				joined_started = [];

				$.post("game_data.php", function(data) {
					lobbies = data.split("~~");
					lobby_count = lobbies.length-1;

					for (var i = 0; i < lobby_count; i++) {
						lobby = lobbies[i].split("||");

						var id = parseInt(lobby[0]);
						var name = lobby[1];
						var pass = lobby[2];
						var ready = lobby[3] === "1";
						var players_wanted = parseInt(lobby[4]);
						var ids = lobby[5].split(",");
						ids.splice(ids.length-1, 1);

						if (ids.includes(user_id)) {
							joined_names.push(name);
							joined_host.push(ids[0] === user_id);
							joined_players.push(ids.length);
							joined_wanted_players.push(players_wanted);
							joined_started.push(ready);
						} else if (!ready) {
							browse_names.push(name);
							browse_pass.push(pass === "1");
							browse_players.push(ids.length);
							browse_wanted_players.push(players_wanted);
						}
					}

					show_browse();
					show_joined();
				});
			}

			function show_create() {
				$("#game_create #password").val("");
				game_create_window.show();
			}

			function show_browse() {
				var items = browse_names.length;

				browse_list.empty();

				for (var i = 0; i < items; i++) {
					var new_item = $("<div class='list_item'></div>");
					new_item.append("<h2>"+ browse_names[i] + "</h2>");
					if (browse_pass[i].length > 0) {
						new_item.append("<p>Requires Password: Yes</p>");
					} else {
						new_item.append("<p>Requires Password: No</p>");
					}
					new_item.append("<p>Players: " + browse_players[i].toString() + " / " + browse_wanted_players[i].toString() + " </p>");

					var joinButton = $("<button class='mainbutton'>Join</button>");
					joinButton.val(i.toString());
					joinButton.click(function (e) {
						selectBrowse = parseInt(e.target.value);
						if (browse_pass[selectBrowse].length === 0) {
							joinSelected();
						} else {
							$("#enter_password #password").val("");
							password_enter_window.show();
						}
					});
					new_item.append(joinButton);

					browse_list.append(new_item);
				}

			}

			function show_joined() {
				var items = joined_names.length;

				joined_list.empty();

				for (var i = 0; i < items; i++) {
					var new_item = $("<div class='list_item'></div>");
					new_item.append("<h2>"+ joined_names[i] + "</h2>");
					if (joined_host[i]) {
						new_item.append("<p>Am Host: Yes</p>");
					} else {
						new_item.append("<p>Am Host: No</p>");
					}
					new_item.append("<p>Players: " + joined_players[i].toString() + " / " + joined_wanted_players[i].toString() + " </p>");
					if (joined_host[i]) {
						new_item.append("<button class='mainbutton'>Start</button>");
						new_item.append("<button class='secondbutton'>Disband</button>");
					} else {
						new_item.append("<button class='mainbutton'>Leave</button>");
					}
					if (joined_started[i]) {
						new_item.append("<button class='playbutton'>Play</button>");
					}

					joined_list.append(new_item);
				}

				joined_list.append("<div class='list_item'><img id='create_img' onclick='show_create()' src='http://pngimg.com/uploads/plus/plus_PNG22.png' width='74' height='74'></img></div>");

			}

			$("#refresh").click(refresh);

			$("#game_create #create").click(function () {
				var formData = $("#create_form").serialize() + "&id=" + user_id;
				$("#game_create #username").val("");
				$.post("create_game.php", formData, function(data) {
					refresh();
					game_create_window.hide();
				});
			});

			$("#enter_password #join").click(joinSelected);

			refresh();

			setInterval(refresh, 10000);

		</script>

	</body>

</html>