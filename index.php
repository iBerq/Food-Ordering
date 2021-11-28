<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once("dbConnection.php");

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function sendNewPassword($from, $to) {
	$subject = "Food Delivery System - New Password";
	$newPassword = randomPassword();
	$header = "From: " . $from;
	mail($to, $subject, $newPassword, $header);
}

$signin = true;
if (isset($_POST['signin_btn'])) {
	$username = $_POST["username_in"];
	$password = $_POST["password_in"];
	
	$selectUser = mysqli_query( $con, "SELECT * from user WHERE username=\"$username\" AND password=\"$password\"" );
	$rowUser = mysqli_fetch_array($selectUser);

	if ( mysqli_num_rows($selectUser) != 0 ) {
		session_start();
		$_SESSION['user_id'] = $rowUser['user_id'];
		$_SESSION['username'] = $rowUser['username'];
		$_SESSION['password'] = $rowUser['password'];

		$selectOwner = mysqli_query( $con, "SELECT * from owner WHERE user_id=\"".$rowUser['user_id']."\"" );
		$selectDelivery = mysqli_query( $con, "SELECT * from delivery_guy WHERE user_id=\"".$rowUser['user_id']."\"" );
		$selectCustomer = mysqli_query( $con, "SELECT * from customer WHERE user_id=\"".$rowUser['user_id']."\"" );
		$selectSupporter = mysqli_query( $con, "SELECT * from support_staff WHERE user_id=\"".$rowUser['user_id']."\"" );
		if (mysqli_num_rows($selectOwner)) {
			$rowOwner = mysqli_fetch_array($selectOwner);
			$select_restaurant = mysqli_query( $con, "SELECT * from restaurant WHERE owner_id=\"".$rowOwner['user_id']."\"" );
			$rowRestaurant = mysqli_fetch_array($select_restaurant);
			$_SESSION['name'] = $rowOwner['name'];
			$_SESSION['surname'] = $rowOwner['surname'];
			$_SESSION['phone_number'] = $rowOwner['phone_number'];
			$_SESSION['mail'] = $rowOwner['mail'];
			$_SESSION['ssn'] = $rowOwner['ssn'];
			$_SESSION['wallet'] = $rowOwner['wallet'];
			$_SESSION['restaurant_id'] = $rowRestaurant['restaurant_id'];
			$_SESSION['restaurant_name'] = $rowRestaurant['restaurant_name'];
			header("Location: Owner/o_homePage.php");
		}
		else if (mysqli_num_rows($selectDelivery)) {
			$rowDelivery = mysqli_fetch_array($selectDelivery);
			$_SESSION['name'] = $rowDelivery['name'];
			$_SESSION['surname'] = $rowDelivery['surname'];
			$_SESSION['phone_number'] = $rowDelivery['phone_number'];
			$_SESSION['mail'] = $rowDelivery['mail'];
			$_SESSION['plate_no'] = $rowDelivery['plate_no'];
			$_SESSION['wallet'] = $rowDelivery['wallet'];
			$_SESSION['ssn'] = $rowDelivery['ssn'];
			header("Location: Delivery_Guy/d_homePage.php");
		}
		else if (mysqli_num_rows($selectCustomer)) {
			$rowCustomer = mysqli_fetch_array($selectCustomer);
			$_SESSION['name'] = $rowCustomer['name'];
			$_SESSION['surname'] = $rowCustomer['surname'];
			$_SESSION['phone_number'] = $rowCustomer['phone_number'];
			$_SESSION['mail'] = $rowCustomer['mail'];
			$_SESSION['wallet'] = $rowCustomer['wallet'];
			$basket = array();
			$jsonBasket = json_encode($basket);
			setcookie("basket", $jsonBasket);
			setcookie("basket_cost", 0);
			header("Location: Customer/c_homePage.php");
		}
		else if (mysqli_num_rows($selectSupporter)) {
			$rowSupporter = mysqli_fetch_array($selectSupporter);
			$_SESSION['name'] = $rowSupporter['name'];
			$_SESSION['surname'] = $rowSupporter['surname'];
			header("Location: Support/s_homePage.php");
		}
	}
	else {
		$signin = false;
	}
}

$correctEmail = -5; //1 successful, 0 wrong email, -1 couldn't send
if (isset($_POST['send_mail_btn'])) {
	$new_pass = randomPassword();
	$mail = $_POST["mail_in"];
	$select = mysqli_query( $con, "SELECT * FROM user WHERE mail=\"$mail\"" );
	if (mysqli_num_rows($select) == 0) {
		$correctEmail = 0;
	}
	else {
		$update = mysqli_query( $con, "UPDATE user SET password=\"$new_pass\" WHERE mail=\"$mail\"" );
		if (!$update) {
			$correctEmail = -1;
		}
		else {
			$correctEmail = 1;
		}
	}
}

function checkSignIn() {
	if (isset($_GET['signin']) && $_GET['signin'] == 'false')
		echo "<h4 style=\"color: #fbceb5; max-width: 350px; text-align: center; word-wrap: normal; padding: 0px 30px 10px 10px;\">You are signed out. You must sign in again to see the pages!</h4>";
}

include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<div class="col-6">
			<img class="img-bg" src="img/courier2.png">
		</div>
		<div class="col-6">
			<div class="form-div">
				<form class="login-form" method="post" name="login_form">
					<h2 style="margin: 5px; font-size: 30px; color: #fbceb5; text-align: center;">Food Delivery System</h2>
					<h2 style="color: white; margin: 5px;">Sign In</h2>
					<div class="form-group">
						<input class="form-input" type="text" name="username_in" placeholder="Username" required="">
					</div>
					<div class="form-group">
						<input class="form-input" type="password" name="password_in" placeholder="Password" required="">
					</div>
					<?php
					checkSignIn();
					if (!$signin) {
					 	echo "<h4 style=\"color: #fbceb5; text-align: center;\">Invalid username and/or password!</h4>";
					}
					?>
					<div class="form-group">
						<button class="form-btn" name="signin_btn">Sign In</button>
					</div>
				</form>
				<div class="login-form">
					<div class="form-group">
						<a href="signup_choice.php">
							<button class="form-btn" name="signup_btn">Sign Up</button>
						</a>
					</div>
					<h4 onclick="forgotPassword()" style="color: white; cursor: pointer;">Forgot Password</h4>
					<?php
					if ($correctEmail == 0) {
						echo "<h4 style=\"color: #fbceb5; text-align: center;\">Invalid email address!</h4>";
					}
					else if ($correctEmail == -1) {
						echo "<h4 style=\"color: #fbceb5; text-align: center;\">Password could NOT be sent!</h4>";
					}
					else if ($correctEmail == 1) {
						echo "<h4 style=\"color: #fbceb5; text-align: center;\">New password was successfully sent!</h4>";
					}
					?>
				</div>
				<form class="login-form" method="post" id="forgot_password_form" style="display: none;">
					<div class="form-group">
						<input class="form-input" type="text" name="mail_in" placeholder="Email Address" required="">
					</div>
					<div class="form-group">
						<button class="form-btn" name="send_mail_btn">Send Mail</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function forgotPassword() {
			var element = document.getElementById("forgot_password_form");
			if (element.style.display == 'none') {
				$("#forgot_password_form").show(1000);
			}
			else {
				$("#forgot_password_form").hide(1000);
			}
		}
	</script>
</body>
</html>