<!DOCTYPE html>
<html>

<?php
include("common.php");
include("head.html");

$messages = array();
$messageCount = 0;

if (isset($_POST['send_btn'])) {
	$message = $_POST["message_in"];
	$date = date("Y-m-d h:i:s");
	mysqli_query($con, "INSERT INTO message (text, date) VALUES ('$message', '$date')");
	$message_id = $con->insert_id;
	mysqli_query($con, "INSERT INTO sends (message_id, customer_id, support_id, sent_by) VALUES ('$message_id', '".$_COOKIE['customer_id']."', '".$_SESSION['user_id']."', 's')");
}

?>
<body style="background-color: #786ca4;">
	<div class="container" id="co">
		<?php
		include("s_dropdown.html");
		?>
		<div class="page" style="padding: 1vw; box-sizing: border-box;">
			<header>Support</header>
			<br>
			<div class="list" style="padding: 1vw; overflow: hidden; height: 90%; background-color: rgba(0,0,0,0.3);">
				<div class="list" style="box-sizing: border-box; padding: 2vw; height: 90%;">
					<div class="row">
						<h2>Choose a customer to support</h2>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</head>
</html>