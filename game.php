<!DOCTYPE html>
<html>
	<head>
		<title>Risk Online - Game</title>

		<style type="text/css">
			body {
				margin: 0px;
			}

			#game_back {
				background-color: white;
			}

			#game {
				position: absolute;
				left: 0px;
				top: 0px;

				background: transparent;
			}
/*
			#instruct {
				position:fixed;
				top: 10px;
				left: 10px;
				margin: 0px;

				background-color: rgba(200, 200, 200, 0.8);
			}*/

			#troop_count {
				position: fixed;
				left: 210px;
				bottom: 30px;

				width: 70px;
				height: 70px;
				background: red; 
				-moz-border-radius: 35px; 
				-webkit-border-radius: 35px; 
				border-radius: 35px;

				text-align: center;
				vertical-align: middle;
				line-height: 70px; 

				font-size: 30pt;
				font-family: Helvetica;
				color: white;

				visibility: hidden;
			}

			#confirm_action {
				position: fixed;
				left: 30px;
				bottom: 30px;

				width: 160px;
				height: 70px;
				background: red; 
				-moz-border-radius: 5px; 
				-webkit-border-radius: 5px; 
				border-radius: 5px;

				text-align: center;
				vertical-align: middle;
				line-height: 70px; 

				font-size: 30pt;
				font-family: Helvetica;
				color: white;

				-moz-user-select: none;
				-khtml-user-select: none;
				-webkit-user-select: none;
				-o-user-select: none;

				background-color: rgba(100, 100, 100, 0.5);

				visibility: hidden;
			}

			#confirm_action:hover {
				background-color: rgba(65, 65, 65, 0.5);
			}

			#confirm_action:active {
				box-shadow: 0 0 5px -1px rgba(0,0,0,0.6);
			}

			#finish_action {
				position: fixed;
				left: 210px;
				bottom: 30px;

				width: 270px;
				height: 70px;
				background: red; 
				-moz-border-radius: 5px; 
				-webkit-border-radius: 5px; 
				border-radius: 5px;

				text-align: center;
				vertical-align: middle;
				line-height: 70px; 

				font-size: 30pt;
				font-family: Helvetica;
				color: white;

				-moz-user-select: none;
				-khtml-user-select: none;
				-webkit-user-select: none;
				-o-user-select: none;

				background-color: rgba(100, 100, 100, 0.5);

				visibility: hidden;
			}

			#finish_action:hover {
				background-color: rgba(65, 65, 65, 0.5);
			}

			#finish_action:active {
				box-shadow: 0 0 5px -1px rgba(0,0,0,0.6);
			}

			#cancel_attack {
				position: fixed;
				left: 30px;
				bottom: 120px;

				width: 160px;
				height: 70px;
				background: red; 
				-moz-border-radius: 5px; 
				-webkit-border-radius: 5px; 
				border-radius: 5px;

				text-align: center;
				vertical-align: middle;
				line-height: 70px; 

				font-size: 30pt;
				font-family: Helvetica;
				color: white;

				-moz-user-select: none;
				-khtml-user-select: none;
				-webkit-user-select: none;
				-o-user-select: none;

				background-color: rgba(100, 100, 100, 0.5);

				visibility: hidden;
			}

			#cancel_attack:hover {
				background-color: rgba(65, 65, 65, 0.5);
			}

			#cancel_attack:active {
				box-shadow: 0 0 5px -1px rgba(0,0,0,0.6);
			}

		</style>

		<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	</head>
	<body>

		<div id="info" style="display: none;"><?php

			$db = mysqli_connect("localhost", "risk_game", getenv("MYSQL_PASS"), "riskdb");

			$gid = $_POST['gid'];
			$uid = $_POST['uid'];
			$password = $_POST['pass']; 

			$stmt = $db->prepare("SELECT `user_games` FROM players WHERE `user_id`=? AND `user_password`=?;");
			$stmt->bind_param('ss', $uid, $password);
			$stmt->execute();
			$user = $stmt->get_result();

			if (mysqli_num_rows($user) == 0) {
				header("Location: index.html");
				exit();
			}

			$games = explode(",", mysqli_fetch_row($user)[0]);
			if (!in_array($gid, $games)) {
				header("Location: index.html");
				exit();
			}

			$stmt = $db->prepare("SELECT * FROM games WHERE `game_id`=?;");
			$stmt->bind_param('s', $gid);
			$stmt->execute();
			echo $uid . "||";
			echo implode("||", mysqli_fetch_row($stmt->get_result()));

		?></div>

		<canvas id="game_back" width="2800" height="1400"></canvas>
		<canvas id="game" width="2800" height="1400"></canvas>
		<div id="troop_count">5</div>
		<div id="confirm_action">Confirm</div>
		<div id="cancel_attack">Cancel</div>
		<div id="finish_action">Finish Attacks</div>

		<script>

			var data = document.getElementById("info").textContent;

			var win = $(window);
			var back_canvas = document.getElementById("game_back");
			var canvas = document.getElementById("game");
			var ctx_back = back_canvas.getContext('2d');
			var ctx = canvas.getContext('2d');

			var troopsIcon = document.getElementById("troop_count");
			var confirm_button = document.getElementById("confirm_action");
			var finish_button = document.getElementById("finish_action");
			var cancel_button = document.getElementById("cancel_attack");

			var scale_factor = 8400 / canvas.width;

			var playerColors = ["rgba(80, 230, 80, 0.5)", "rgba(0, 0, 255, 0.5)", "rgba(130, 130, 130, 0.5)", "rgba(200, 0, 200, 0.5)"];

			var prefix = "http://52.53.247.208/";

			var deployPhase = false;
			var attackPhase = false;
			var reinforcePhase = false;

			var backImg = new Image;
			backImg.onload = function () {
				ctx_back.drawImage(backImg, 0, 0, back_canvas.width, back_canvas.height);
				draw();
			};
			backImg.src = prefix + "world-domination-game-board-2.1.1.png";

			var maskImg = new Image;
			var maskCanv = document.createElement('canvas');
			var maskCtx = maskCanv.getContext('2d');
			maskImg.onload = function () {
				maskCanv.width = maskImg.width;
				maskCanv.height = maskImg.height;
				maskCanv.getContext('2d').drawImage(maskImg, 0, 0, maskCanv.width, maskCanv.height);
			};
			maskImg.src = prefix + "total_imgs.png";

			function canvas_arrow(context, fromx, fromy, tox, toy, r, reduce){
				var dx = tox - fromx;
				var dy = toy - fromy;
				var l = Math.sqrt(dx*dx + dy*dy);
				dx /= l;
				dy /= l;
				fromx += dx * reduce;
				fromy += dy * reduce;
				tox -= dx * (reduce + r);
				toy -= dy * (reduce + r);

				ctx.beginPath();
				ctx.moveTo(fromx, fromy);
				ctx.lineTo(tox, toy);
				ctx.closePath();
				ctx.stroke();

				var x_center = tox;
				var y_center = toy;

				context.beginPath();

				var angle = Math.atan2(toy-fromy,tox-fromx)
				var x = r*Math.cos(angle) + x_center;
				var y = r*Math.sin(angle) + y_center;

				context.moveTo(x, y);

				angle += (1/3)*(2*Math.PI)
				x = r*Math.cos(angle) + x_center;
				y = r*Math.sin(angle) + y_center;

				context.lineTo(x, y);

				angle += (1/3)*(2*Math.PI)
				x = r*Math.cos(angle) + x_center;
				y = r*Math.sin(angle) + y_center;

				context.lineTo(x, y);

				context.closePath();

				context.fill();
			}

			function createColoredCanv(img, color) {
				var canv = document.createElement('canvas');
				var clrCtx = canv.getContext('2d');

				canv.width = img.width;
				canv.height = img.height;

				clrCtx.drawImage(img, 0, 0, canv.width, canv.height);
				clrCtx.globalCompositeOperation = "source-in";
				clrCtx.fillStyle = color;
				clrCtx.fillRect(0, 0, canv.width, canv.height);
				clrCtx.globalCompositeOperationSection = "source-over";

				return canv;
			}

			var continents = ['asia', 'europe', 'north america', 'africa', 'australia', 'south america', 'greenland'];
			var cont_troop = [7, 6, 5, 3, 2, 2, 0];
			var cont_terrs = [12, 7, 8, 6, 4, 4, 2];
			var country_cont = [0, 2, 2, 5, 5, 0, 3, 3, 4, 1, 2, 3, 1, 6, 1, 0, 4, 0, 0, 0, 3, 2, 0, 0, 4, 3, 1, 2, 2, 5, 2, 1, 0, 0, 3, 1, 0, 5, 4, 1, 2, 0];
			var img_names = ['afghanistan', 'alaska', 'alberta', 'argentina', 'brazil', 'china', 'congo', 'east_africa', 'eastern_australia', 'eastern_europe', 'eastern_us', 'egypt', 'great_britian', 'greenland', 'iceland', 'india', 'indonesia', 'irkutsk', 'japan', 'kamchatka', 'madagascar', 'mexico', 'middle_eat', 'mongolia', 'new_guinea', 'north_africa', 'northern_europe', 'northwest', 'ontario', 'peru', 'quebec', 'scandinavia', 'siam', 'siberia', 'south_africa', 'ukraine', 'ural', 'venezuela', 'western_australia', 'western_europe', 'western_us', 'yakutsk'];
			var cropped_names = ['4777x1307-684x679_trans_outline_afghanistan.png', '1033x555-609x572_trans_outline_alaska.png', '1564x886-554x423_trans_outline_alberta.png', '2301x2972-530x1070_trans_outline_argentina.png', '2212x2414-1052x953_trans_outline_brazil.png', '5353x1418-1009x908_trans_outline_china.png', '3982x2836-623x607_trans_outline_congo.png', '4294x2570-681x962_trans_outline_east_africa.png', '6507x3157-594x866_trans_outline_eastern_australia.png', '3812x1589-563x589_trans_outline_eastern_europe.png', '1954x1287-838x683_trans_outline_eastern_us.png', '3996x2266-622x371_trans_outline_egypt.png', '3245x1123-453x520_trans_outline_great_britian.png', '2616x101-835x949_trans_outline_greenland.png', '3413x845-362x248_trans_outline_iceland.png', '5134x1852-716x975_trans_outline_india.png', '5736x2804-653x525_trans_outline_indonesia.png', '5646x871-684x535_trans_outline_irkutsk.png', '6443x1204-350x674_trans_outline_japan.png', '6075x508-876x1000_trans_outline_kamchatka.png', '4753x3396-314x485_trans_outline_madagascar.png', '1654x1749-531x658_trans_outline_mexico.png', '4276x1904-948x874_trans_outline_middle_eat.png', '5697x1232-748x559_trans_outline_mongolia.png', '6370x2742-498x361_trans_outline_new_guinea.png', '3438x2149-899x937_trans_outline_north_africa.png', '3758x1199-578x554_trans_outline_northern_europe.png', '1496x474-1036x447_trans_outline_northwest.png', '2069x901-490x622_trans_outline_ontario.png', '2068x2507-732x718_trans_outline_peru.png', '2460x874-520x598_trans_outline_quebec.png', '3821x647-559x624_trans_outline_scandinavia.png', '5756x2153-470x605_trans_outline_siam.png', '5161x327-743x1227_trans_outline_siberia.png', '4059x3222-679x814_trans_outline_south_africa.png', '4198x662-915x1316_trans_outline_ukraine.png', '5030x515-529x1053_trans_outline_ural.png', '2109x2230-758x395_trans_outline_venezuela.png', '6090x3248-698x768_trans_outline_western_australia.png', '3374x1591-487x654_trans_outline_western_europe.png', '1599x1288-601x569_trans_outline_western_us.png', '5743x415-607x556_trans_outline_yakutsk.png'];
			var xs = [];
			var ys = [];
			var ws = [];
			var hs = [];

			var highlightedCountry = -1;
			var sourceCountry = -1;
			var targetCountry = -1;

			var highlight_color = "rgb(255, 255, 255)";
			var source_color = "rgb(163, 247, 237)";
			var target_color = "rgb(255, 238, 114)";

			var highlight_outlines = [];
			var source_outlines = [];
			var target_outlines = [];

			var ownerIndex = [];
			var troopsOn = [];
			var userID = 0;
			var playerOrder = [];
			var currentIndex = 0;
			var gameName = "";
			var startingPhase = false;

			var shiftDown = false;
			var spareTroops = 0;

			var gameID = -1;

			function decode(data) {
				var parts = data.split("||");
				userID = parseInt(parts[0]);

				gameID = parts[1];

				gameName = parts[2];

				currentIndex = parseInt(parts[7]);

				var indecies = parts[8].split(",");
				playerOrder = indecies.map(n => parseInt(n));

				var ownerStrings = parts[9].split(",");
				var troopsStrings = parts[10].split(",");
				ownerIndex = ownerStrings.map(n => parseInt(n));
				troopsOn = troopsStrings.map(n => parseInt(n));

				startingPhase = ownerIndex.includes(-1);
			}

			function encode() {
				var str = currentIndex.toString() + "||";
				str += playerOrder.toString() + "||";
				str += ownerIndex.toString() + "||";
				str += troopsOn.toString();

				return str;
			}

			var ready = false;
			decode(data);
			setInterval(refresh, 7500);
			refresh();

			for (var i = 0; i < cropped_names.length; i++) {
				var outlineImage = new Image;
				highlight_outlines.push(outlineImage);
				source_outlines.push(null);
				target_outlines.push(null);

				outlineImage.onload = function (e) {
					var k = highlight_outlines.indexOf(e.target);

					highlight_outlines[k] = createColoredCanv(highlight_outlines[k], highlight_color);
					source_outlines[k] = createColoredCanv(highlight_outlines[k], source_color);
					target_outlines[k] = createColoredCanv(highlight_outlines[k], target_color);
				}

				outlineImage.src = prefix + "Risk_Cropped_Outlines/" + cropped_names[i];
				var info = cropped_names[i].split("_")[0]
				var pos = info.split("-")[0];
				var sz = info.split("-")[1];

				xs.push(parseInt(pos.split("x")[0]));
				ys.push(parseInt(pos.split("x")[1]));
				ws.push(parseInt(sz.split("x")[0]));
				hs.push(parseInt(sz.split("x")[1]));
			}

			var adjacencies = [[0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0], [0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0], [0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0], [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0], [0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1], [0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0], [1, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0], [0, 0, 0, 0, 1, 0, 1, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0], [0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0], [0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1], [0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0], [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0]];
			var adjacent_indecies = [[5, 15, 22, 35, 36],[2, 19, 27],[1, 27, 28, 40],[4, 29],[3, 25, 29, 37],[0, 15, 23, 32, 33, 36],[7, 25, 34],[6, 11, 20, 22, 25, 34],[24, 38],[11, 22, 25, 26, 35, 39],[21, 28, 30, 40],[7, 9, 22, 25],[14, 26, 31, 39],[14, 27, 28, 30],[12, 31],[0, 5, 22, 32],[24, 32, 38],[19, 23, 33, 41],[19, 23, 24],[17, 18, 23, 41],[7, 34],[10, 37, 40],[0, 7, 9, 11, 15, 35],[5, 17, 19, 33],[8, 16, 18, 32, 38],[4, 6, 7, 9, 11, 39],[9, 12, 31, 35, 39],[1, 2, 13, 28],[2, 10, 13, 27, 30, 40],[3, 4, 37],[10, 13, 28],[12, 14, 26, 35],[5, 15, 16],[5, 17, 23, 36, 41],[6, 7, 20],[0, 9, 22, 26, 31, 36],[0, 5, 33, 35],[4, 21, 29],[8, 16, 24],[9, 12, 25, 26],[2, 10, 21, 28],[17, 19, 33]];
			
			function checkConnected(source, dest) {
				options = [];
				visited = [];

				for (var i = 0; i < adjacencies.length; i++) {
					visited.push(0);
				}
				for (var i = 0; i < adjacent_indecies[source].length; i++) {
					var ind = adjacent_indecies[source][i];
					if (ownerIndex[ind] == userID) {
						options.push(ind);
					}
				}
				visited[source] = 1;

				while (options.length > 0) {
					var last = options[options.length-1];

					if (last == dest) {
						return true;
					}

					visited[last] = 1;

					var found = false;
					for (var i = 0; i < adjacent_indecies[last].length; i++) {
						var ind = adjacent_indecies[last][i];
						if (visited[ind] == 0 && ownerIndex[ind] == userID) {
							options.push(ind);
							found = true;
							break;
						}
					}

					if (!found) {
						options.splice(options.length-1, 1);
					}
				}

				return false;
			}

			function adjacent(source, dest) {
				if (reinforcePhase) {
					return checkConnected(source, dest);
				} else {
					return adjacencies[source][dest] == 1;
				}
			}

			function draw() {
				ctx.clearRect(0,0,canvas.width,canvas.height);

				var highX, highY, highW, highH;
				var sourceX, sourceY, sourceW, sourceH;
				var targetX, targetY, targetW, targetH;

				if (highlightedCountry != -1) {
					highX = xs[highlightedCountry] / scale_factor;
					highY = ys[highlightedCountry] / scale_factor;
					highW = ws[highlightedCountry] / scale_factor;
					highH = hs[highlightedCountry] / scale_factor;

					if (shiftDown && shouldLocations) {
						ctx.drawImage(target_outlines[highlightedCountry], highX, highY, highW, highH);
					} else if (shouldLocations) {
						ctx.drawImage(source_outlines[highlightedCountry], highX, highY, highW, highH);
					} else {
						ctx.drawImage(highlight_outlines[highlightedCountry], highX, highY, highW, highH);
					}
				}

				if (sourceCountry != -1) {
					sourceX = xs[sourceCountry] / scale_factor;
					sourceY = ys[sourceCountry] / scale_factor;
					sourceW = ws[sourceCountry] / scale_factor;
					sourceH = hs[sourceCountry] / scale_factor;
					ctx.drawImage(source_outlines[sourceCountry], sourceX, sourceY, sourceW, sourceH);
				}

				if (targetCountry != -1) {
					targetX = xs[targetCountry] / scale_factor;
					targetY = ys[targetCountry] / scale_factor;
					targetW = ws[targetCountry] / scale_factor;
					targetH = hs[targetCountry] / scale_factor;
					ctx.drawImage(target_outlines[targetCountry], targetX, targetY, targetW, targetH);
				}

				if (sourceCountry != -1 && targetCountry != -1 && sourceCountry != targetCountry) {
					ctx.fillStyle = "red";
					ctx.strokeStyle = 'red';
					ctx.lineJoin = 'butt';
					ctx.lineWidth = 10;

					canvas_arrow(ctx, sourceX + sourceW/2, sourceY + sourceH/2, targetX + targetW/2, targetY + targetH/2, 15, 25);
				}

				for (var i = 0; i < troopsOn.length; i++) {
					if (ownerIndex[i] === -1) {
						continue;
					}

					var colorIndex = playerOrder.indexOf(ownerIndex[i]);
					ctx.fillStyle = playerColors[colorIndex];

					var x = xs[i] / scale_factor;
					var y = ys[i] / scale_factor;
					var w = ws[i] / scale_factor;
					var h = hs[i] / scale_factor;

					ctx.beginPath();
					ctx.arc(x+w/2, y+h/2, 20, 0, Math.PI*2, true);
					ctx.closePath();
					ctx.fill();

					ctx.textAlign = "center";
					ctx.textBaseline = "middle";
					ctx.fillStyle = "white";
					ctx.font = "14px Helvetica";
					ctx.fillText((troopsOn[i]).toString(), x+w/2, y+h/2);
				};

				draw_ui();
			}

			function draw_ui() {
				troop_count.style.backgroundColor  = playerColors[playerOrder.indexOf(userID)];
				troop_count.innerText = spareTroops;

				if (shouldDeploy && myTurn) {
					troop_count.style.visibility = "visible";
				} else {
					troop_count.style.visibility = "hidden";
				}

				if (!myTurn) {
					confirm_button.style.visibility = "hidden";
					cancel_button.style.visibility = "hidden";
					finish_button.style.visibility = "hidden";
				}
			}

			document.body.onmousedown = function (e) {
				var x = e.pageX;
				var y = e.pageY;
				
				var index = 255 - maskCtx.getImageData(x*scale_factor, y*scale_factor, 1, 1).data[0];

				if (!myTurn || index == 255 || highlightedCountry == -1 || !ready || highlightedCountry != index) {
					return;
				}

				if (startingPhase) {
					if (targetCountry == -1) {
						spareTroops--;
						targetCountry = index;
						ownerIndex[index] = userID;
						troopsOn[index] = 1;
					} else if (targetCountry == index) {
						ownerIndex[targetCountry] = -1;
						troopsOn[targetCountry] = 0;
						targetCountry = -1;
						spareTroops++;
					} else {
						ownerIndex[targetCountry] = -1;
						troopsOn[targetCountry] = 0;
						targetCountry = index;
						ownerIndex[index] = userID;
						troopsOn[targetCountry] = 1;
					}
				} else if (shouldLocations) {
					if (shiftDown) {
						if (sourceCountry == -1 || adjacent(sourceCountry, index)) {
							targetCountry = index;
						}
					} else {
						sourceCountry = index;
						if (targetCountry != -1 && !adjacent(sourceCountry, targetCountry)) {
							targetCountry = -1;
						}
					}
				} else if (shouldDeploy) {
					if (e.shiftKey) {
						if (troopsOn[index] > 1) {
							troopsOn[index] -= 1;
							spareTroops++;
						}
					} else if (spareTroops > 0) {
						troopsOn[index] += 1;
						spareTroops--;
					}
				} else if (shouldTransfer) {
					if (index == sourceCountry && troopsOn[targetCountry] > 1) {
						troopsOn[targetCountry]--;
						troopsOn[sourceCountry]++;
					} else if (index == targetCountry && troopsOn[sourceCountry] > 1) {
						troopsOn[sourceCountry]--;
						troopsOn[targetCountry]++;
					}
				}

				draw();
			}

			var lastHovered = 255;
			function checkHighlightable() {
				var index = lastHovered;

				if (index == 255) {
					highlightedCountry = -1;
				} else if (!myTurn) {
					highlightedCountry = index;
				} else if (startingPhase) {
					if (ownerIndex[index] == -1) {
						highlightedCountry = index;
					} else {
						highlightedCountry = -1;
					}
				} else {   
					if (shouldLocations) {
						if (shiftDown) {
							if (targetFriendly && ownerIndex[index] != userID) {
								highlightedCountry = -1;
							} else if (!targetFriendly && ownerIndex[index] == userID) {
								highlightedCountry = -1;
							} else if (sourceCountry != -1 && !adjacent(sourceCountry, index)) {
								highlightedCountry = -1;
							} else {
								highlightedCountry = index;
							}
						} else {
							if (ownerIndex[index] != userID || troopsOn[index] <= 1) {
								highlightedCountry = -1;
							} else {
								highlightedCountry = index;
							}
						}
					} else if (shouldDeploy) {
						if (ownerIndex[index] == userID) {
							highlightedCountry = index;
						} else {
							highlightedCountry = -1;
						}
					} else if (shouldTransfer) {
						if (index == sourceCountry || index == targetCountry) {
							highlightedCountry = index;
						} else {
							highlightedCountry = -1;
						}
					} else {
						highlightedCountry = -1;
					}
				}
			}

			document.body.onmousemove = function (e) {
				var x = e.pageX;
				var y = e.pageY;
				
				lastHovered = 255 - maskCtx.getImageData(x*scale_factor, y*scale_factor, 1, 1).data[0];
				checkHighlightable();

				draw();
			}

			document.body.onkeydown = function (e) {
				if (e.keyCode == 16) {
					shiftDown = true;
					checkHighlightable();
					draw();
				}
			}
			document.body.onkeyup = function (e) {
				if (e.keyCode == 16) {
					shiftDown = false;
					checkHighlightable();
					draw();
				}
			}

			var myTurn = true;
			var shouldLocations = false;
			var shouldDeploy = true;

			var shouldTransfer = false;
			var sourceSavedTroops = 0;
			var targetSavedTroops = 0;

			var targetFriendly = false;

			function troopsPerTurn() {
				if (startingPhase) {
					return 1;
				}

				var terrs = 0;
				var conts = [];
				for (var i = 0; i < cont_terrs.length; i++) {
					conts.push(0);
				}

				for (var i = 0; i < ownerIndex.length; i++) {
					if (ownerIndex[i] == userID) {
						terrs++;
						conts[country_cont[i]]++;
					}
				}

				var troops = Math.floor(terrs / 3);

				for (var i = 0; i < conts.length; i++) {
					if (conts[i] == cont_terrs[i]) {
						troops += cont_troop[i];
					}
				}

				return troops;
			}

			function performAttack(source, dest) {
				var attackDice = Math.min(troopsOn[source], 3);
				var attackRoles = [];
				var defendDice = Math.min(troopsOn[dest], 2);
				var defendRoles = [];

				for (var i = 0; i < attackDice; i++) {
					var roll = Math.floor(Math.random() * 6) + 1;
					attackRoles.push(roll);
				}

				for (var i = 0; i < defendDice; i++) {
					var roll = Math.floor(Math.random() * 6) + 1;
					defendRoles.push(roll);
				}

				attackRoles.sort((a, b) => b - a);
				defendRoles.sort((a, b) => b - a);

				var i = 0;
				var attackerDmg = 0;
				var defenderDmg = 0;
				while (i < attackRoles.length && i < defendRoles.length) {
					var attack = attackRoles[i];
					var defend = defendRoles[i];

					if (attack > defend) {
						attackerDmg++;
					} else {
						defenderDmg++;
					}

					i++;
				}

				attackerDmg = Math.min(attackerDmg, troopsOn[dest]);
				defenderDmg = Math.min(defenderDmg, troopsOn[source]-1);

				takeDamage(dest, attackerDmg);
				takeDamage(source, defenderDmg);
			}

			function takeDamage(terr, dmg) {
				troopsOn[terr] -= dmg;
			}

			function transfer(callback) {
				shouldDeploy = false;
				shouldLocations = false;
				shouldTransfer = true;

				sourceSavedTroops = troopsOn[sourceCountry];
				targetSavedTroops = troopsOn[targetCountry];

				confirm_button.style.visibility = "visible";
				confirm_button.innerText = "Transfer";
				cancel_button.style.visibility = "visible";
				cancel_button.innerText = "Cancel";
				finish_button.style.visibility = "hidden";

				confirm_button.onclick = callback;

				cancel_button.onclick = function () {
					troopsOn[sourceCountry] = sourceSavedTroops;
					troopsOn[targetCountry] = targetSavedTroops;
					callback();
				}
			}

			function endTurn() {
				myTurn = false;
				deployPhase = false;
				attackPhase = false;
				reinforcePhase = false;
				shouldLocations = false;
				shouldDeploy = false;
				shouldTransfer = false;
				sourceCountry = -1;
				targetCountry = -1;
			}

			function playTurn() {
				myTurn = true;
				deployPhase = true;
				attackPhase = false;
				reinforcePhase = false;

				shouldLocations = false;
				shouldDeploy = true;
				spareTroops = troopsPerTurn();
				draw();

				confirm_button.style.visibility = "visible";
				confirm_button.innerText = "Deploy";
				confirm_button.onclick = function () {
					if (spareTroops == 0) {
						if (startingPhase) {
							currentIndex++;
							if (currentIndex >= playerOrder.length) {
								currentIndex = 0;
							}

							var newData = encode();
							var form = {gid: gameID, data:newData};
							$.post("send_game_data.php", form);

							endTurn();
						} else {
							playAttack();
						}
					}
				}
			}

			function playAttack() {
				deployPhase = false;
				attackPhase = true;
				reinforcePhase = false;

				shouldLocations = true;
				shouldDeploy = false;
				targetFriendly = false;
				draw();

				confirm_button.style.visibility = "visible";
				confirm_button.innerText = "Attack";
				cancel_button.style.visibility = "visible";
				cancel_button.innerText = "Cancel";
				finish_button.style.visibility = "visible";
				finish_button.innerText = "Finish Attacks";

				confirm_button.onclick = function () {
					if (sourceCountry != -1 && targetCountry != -1) {
						performAttack(sourceCountry, targetCountry);

						if (troopsOn[targetCountry] == 0) {
							ownerIndex[targetCountry] = userID;
							troopsOn[targetCountry] = 1;
							troopsOn[sourceCountry]--;
							draw();
							transfer(function () {
								sourceCountry = -1;
								targetCountry = -1;
								playAttack();
							});
						} else {
							if (troopsOn[sourceCountry] == 1) {
								sourceCountry = -1;
								targetCountry = -1;
							}
							draw();
						}
					}
				}

				cancel_button.onclick = function () {
					sourceCountry = -1;
					targetCountry = -1;
					draw();
				}

				finish_button.onclick = function () {
					if (sourceCountry != -1 || targetCountry != -1) {
						sourceCountry = -1;
						targetCountry = -1;
						draw();
					} else {
						playReinforce();
					}
				}
			}

			function playReinforce() {
				sourceCountry = -1;
				targetCountry = -1;

				deployPhase = false;
				attackPhase = false;
				reinforcePhase = true;

				shouldLocations = true;
				shouldDeploy = false;
				targetFriendly = true;
				draw();

				confirm_button.innerText = "Move";
				confirm_button.style.visibility = "visible";
				finish_button.innerText = "Finish Moves";
				finish_button.style.visibility = "visible";
				cancel_button.style.visibility = "hidden";

				confirm_button.onclick = function () {
					if (sourceCountry != -1 && targetCountry != -1) {
						transfer(playReinforce);
					}
				}

				finish_button.onclick = function () {
					if (sourceCountry != -1 || targetCountry != -1) {
						sourceCountry = -1;
						targetCountry = -1;
						draw();
					} else {
						currentIndex++;
						if (currentIndex >= playerOrder.length) {
							currentIndex = 0;
						}

						var newData = encode();
						var form = {gid: gameID, data:newData};
						$.post("send_game_data.php", form);

						endTurn();
					}
				}
			}

			function refresh() {
				if (playerOrder[currentIndex] == userID && ready) {
					return;
				}

				var data = {gid:gameID, uid:userID};
				$.post("get_game_data.php", data, function(data) {
					if (data === "-1") {
						window.location.replace("index.html");
					} else {
						ready = true;
						decode(data);

						if (playerOrder[currentIndex] == userID) {
							playTurn();
						} else {
							endTurn();
						}
					}
				});
			}

		</script>
	</body>
</html>