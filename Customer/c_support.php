<!DOCTYPE html>
<html>

<?php
include("common.php");
include("head.html");

$supporter_id = -1;
$messages = array();
$messageCount = 0;

if (isset($_POST['end_support_btn'])) {
	matchSupporter();
	mysqli_query($con, "UPDATE supports SET state='1' WHERE supporter_id='".$supporter_id."' AND customer_id='".$_SESSION['user_id']."' AND state='0'");
	$support_id = -1;
	header("Location: c_homePage.php");
}

if (isset($_POST['send_btn'])) {
	matchSupporter();
	$message = $_POST["message_in"];
	$date = date("Y-m-d h:i:s");
	mysqli_query($con, "INSERT INTO message (text, date) VALUES ('$message', '$date')");
	$message_id = $con->insert_id;
	mysqli_query($con, "INSERT INTO sends (message_id, customer_id, support_id, sent_by) VALUES ('$message_id', '".$_SESSION['user_id']."', '$supporter_id', 'c')");
}

function matchSupporter() {
	include("dbConnection.php");
	global $messages, $messageCount, $supporter_id;

	$supporter_ids = array();
	$counter = 0;
	$select_supports = mysqli_query($con, "SELECT * FROM supports WHERE customer_id='".$_SESSION['user_id']."' and state='0'");
	if (mysqli_num_rows($select_supports) == 0) {
		$select_supporters = mysqli_query($con, "SELECT * FROM support_staff");
		while ($staff = mysqli_fetch_array($select_supporters)) {
			$supporter_ids[$counter++] = $staff['user_id'];
		}

		$randIndex = rand(0,count($supporter_ids) - 1);
		$supporter_id = $supporter_ids[$randIndex];

		mysqli_query($con, "INSERT INTO supports VALUES ('".$supporter_id."', '".$_SESSION['user_id']."', '".date("Y-m-d h:i:s")."', '0') ON DUPLICATE KEY UPDATE state='0'");
		echo mysqli_error($con);
	}
	else {
		$supporter = mysqli_fetch_array($select_supports);
		$supporter_id = $supporter['supporter_id'];
	}
}

/*function checkSupporter() {
	global $messages, $messageCount, $supporter_id;
	include("dbConnection.php");
	$select_supports = mysqli_query($con, "SELECT * FROM supports WHERE customer_id='".$_SESSION['user_id']."'");
	if (mysqli_num_rows($select_supports) != 0) {
		$supporter = mysqli_fetch_array($select_supports);
		$supporter_id = $supporter['supporter_id'];
	}
}*/

function getMessages() {
	global $messages, $messageCount, $supporter_id;
	include("dbConnection.php");
	$select_sends = mysqli_query($con, "SELECT * FROM sends WHERE customer_id='".$_SESSION['user_id']."' AND support_id='$supporter_id'");
	while ($row_sends = mysqli_fetch_array($select_sends)) {
		$message = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM message WHERE message_id='".$row_sends['message_id']."' ORDER BY date"));
		echo mysqli_error($con);
		$messages[$messageCount++] = array($message['text'], $message['date'], $row_sends['sent_by']);
	}
	foreach ($messages as $key => $value) {
		if ($messages[$key][2] == 's') {
			echo "<hr><div class='row' style='padding: 1vw; width: 100%; justify-content: flex-start;'>".
					"<div class='col' style='width: 15%; justify-content: flex-start;'>".
						"<h3>Supporter:</h3>".
					"</div>".
					"<div class='col' style='width: 85%; justify-content: flex-start;'>".
						"<h4 style='word-wrap: break-word;'>".$messages[$key][0]."</h4>".
					"</div>".
				 "</div>".
				 "<div class='row' style='max-height: 2vw; padding: 1vw; width: 100%; justify-content: flex-end;'>".
				 	"<div class='col' style='width: 85%; justify-content: flex-start;'>".
						"<label style='font-size: 1vw; word-wrap: break-word;'>".$messages[$key][1]."</label>".
					"</div>".
				 "</div>";
		}
		else {
			echo "<hr><div class='row' style='padding: 1vw; width: 100%; justify-content: flex-end;'>".
					"<div class='col' style='width: 15%; justify-content: flex-start;'>".
						"<h3>You:</h3>".
					"</div>".
					"<div class='col' style='width: 85%; justify-content: flex-start;'>".
						"<h4 style='word-wrap: break-word;'>".$messages[$key][0]."</h4>".
					"</div>".
				 "</div>".
				 "<div class='row' style='max-height: 2vw; padding: 1vw; width: 100%; justify-content: flex-end;'>".
				 	"<div class='col' style='width: 85%; justify-content: flex-start;'>".
						"<label style='font-size: 1vw; word-wrap: break-word;'>".$messages[$key][1]."</label>".
					"</div>".
				 "</div>";
		}
	}
}

?>
<body style="background-color: #786ca4;">
	<div class="container" id="co">
		<?php
		include("c_dropdown.html");
		matchSupporter();
		?>
		<div class="page" style="padding: 1vw; box-sizing: border-box;">
			<div class="row" style="height: 2vw;">
				<button class="form-btn" onclick="window.location.href='c_support.php'" name="send_btn" style="width: 10%; height: 2vw; font-size: 1.5vw; margin-left: 1vw; ">Refresh</button>
				<h4 style="margin: 0px 0px 0px 2vw; color: white; width: 100%;">Page will be refreshed in</h4>
				<span id="timer" style="margin-left: 1vw; color: white;"></span>
				<form method="post" style="width: 80%; margin-left: 50vw;">
					<button class="form-btn" type="submit" name="end_support_btn" style="width: 100%; height: 2vw; font-size: 1.5vw;">End Support</button>
				</form>
			</div>
			<header>Support</header>
			<br>
			<div class="list" style="padding: 1vw; overflow: hidden; height: 90%; background-color: rgba(0,0,0,0.3);">
				<div class="list" style="box-sizing: border-box; padding: 2vw; height: 90%;">
					<?php getMessages(); ?>
				</div>
				<form id="form" class="login-form" method="post" style="width: 100%; height: 90%;">
					<div class="row" style="margin-top: 1vw;">
						<input type="text" name="message_in" class="form-input" placeholder="Type your message...">
						<button class="form-btn" name="send_btn" style="width: 20%; margin-left: 1vw;">Send</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	document.getElementById('timer').innerHTML = 000 + ":" + 30;
	startTimer();

	function startTimer() {
  		var presentTime = document.getElementById('timer').innerHTML;
  		var timeArray = presentTime.split(/[:]+/);
  		var m = timeArray[0];
  		var s = checkSecond((timeArray[1] - 1));
  		
  		if(s == 59){
  			m = m - 1
  		}
  
  		document.getElementById('timer').innerHTML = m + ":" + s;
  		setTimeout(startTimer, 1000);
	}

	function checkSecond(sec) {
	  	if (sec < 10 && sec >= 0) {sec = "0" + sec}; // add zero in front of numbers < 10
	  	if (sec < 0) {sec = "59"};
	  	return sec;
	}

	var x = setInterval(function() {
		window.location.href = "c_support.php";
	}, 30000);
</script>
</head>
</html>