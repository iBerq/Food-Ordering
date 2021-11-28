<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once("dbConnection.php");
include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<div class="col-6">
			<img class="img-bg" src="img/courier2.png">
		</div>
		<div class="col-6">
			<div class="form-div">
				<div class="form-group">
					<h2 style="color: white; margin: 5px; font-size: 22px;">Sign Up As...</h2>
				</div>
				<div class="form-group">
					<a href="o_signup.php"><button class="form-btn">Owner</button></a>
				</div>
				<div class="form-group">
					<a href="c_signup.php"><button class="form-btn">Customer</button></a>
				</div>
				<div class="form-group">
					<a href="d_signup.php"><button class="form-btn">Delivery Guy</button></a>
				</div>
				<div class="form-group">
					<button type="button" onclick="window.location.href='index.php'" class="form-btn" name="back_btn">
						<< Back
					</button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>