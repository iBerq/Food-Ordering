<!DOCTYPE html>
<html>
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once("dbConnection.php");

$isThere = "none";
if (isset($_POST['signup_btn'])) {
	$username = $_POST["username_in"];
	$password = $_POST["password_in"];
	$name = $_POST["name_in"];
	$surname = $_POST["surname_in"];
	$phone_number = $_POST["phone_in"];
	$mail = $_POST["mail_in"];
	$ssn = $_POST["ssn_in"];

	$picture_name = $_FILES['picture_in']['name'];
    $target = 'img/';
    //$basename = basename($picture_name);
    $fulltarget = $target.$picture_name;
    move_uploaded_file($_FILES['picture_in']['tmp_name'], $fulltarget);

	$select_user = mysqli_query( $con, "SELECT * from user WHERE username=\"$username\"");
	if (mysqli_num_rows($select_user)) {
		$isThere = "username";
	}

	//check phone number
	$select_owner = mysqli_query( $con, "SELECT * from owner WHERE phone_number=\"$phone_number\"" );
	$select_customer = mysqli_query( $con, "SELECT * from customer WHERE phone_number=\"$phone_number\"" );
	$select_delivery = mysqli_query( $con, "SELECT * from delivery_guy WHERE phone_number=\"$phone_number\"" );
	if (mysqli_num_rows($select_owner) || mysqli_num_rows($select_customer) || mysqli_num_rows($select_delivery)) {
		$isThere = "phone";
	}

	//check mail
	$select_owner = mysqli_query( $con, "SELECT * from owner WHERE mail=\"$mail\"" );
	$select_customer = mysqli_query( $con, "SELECT * from customer WHERE mail=\"$mail\"" );
	$select_delivery = mysqli_query( $con, "SELECT * from delivery_guy WHERE mail=\"$mail\"" );
	if (mysqli_num_rows($select_owner) || mysqli_num_rows($select_customer) || mysqli_num_rows($select_delivery)) {
		$isThere = "mail";
	}

	//check ssn
	$select_owner = mysqli_query( $con, "SELECT * from owner WHERE ssn=\"$ssn\"" );
	$select_delivery = mysqli_query( $con, "SELECT * from delivery_guy WHERE ssn=\"$ssn\"" );
	if (mysqli_num_rows($select_owner) || mysqli_num_rows($select_delivery)) {
		$isThere = "ssn";
	}

	if ($isThere == "none") {
		$insertUser = mysqli_query( $con, "INSERT INTO user (username, password) VALUES (\"$username\",\"$password\")" );
		if (!$insertUser) {
			echo "USER INSERT FAILED.";
		}
		else {
			$selectUser = mysqli_query( $con, "SELECT user_id from user WHERE username=\"$username\"" );
			$rowUser = mysqli_fetch_array( $selectUser );

			$insertOwner = mysqli_query( $con, "INSERT INTO owner (user_id, name, surname, phone_number, mail, ssn) VALUES (\"".$rowUser['user_id']."\", \"$name\", \"$surname\", \"$phone_number\", \"$mail\", \"$ssn\")" );

			if (!$insertOwner) {
				$deleteUser = mysqli_query( $con, "DELETE FROM user WHERE user_id=\"".$rowUser['user_id']."\"" );
				echo "OWNER INSERT FAILED => " . mysqli_error($con);
			}
			else {
				session_start();
				$_SESSION['user_id'] = $rowUser['user_id'];
				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;
				$_SESSION['name'] = $name;
				$_SESSION['surname'] = $surname;
				$_SESSION['phone_number'] = $phone_number;
				$_SESSION['mail'] = $mail;
				$_SESSION['ssn'] = $ssn;
				header("Location: o_homePage.php");
			}
		}
	}
}
include("head.html");
?>

<body style="background-color: #786ca4;">
	<div class="container">
		<div class="col-6">
			<img class="img-bg" src="img/courier2.png">
		</div>
		<div class="col-6">
			<div class="form-div" style="width: 100%;">
				<form class="login-form" method="post" name="login_form">
					<div class="two-col">
						<div class="form-group">
							<h2 style="color: white; font-size: 22px; width: 105%; text-align: center;">Sign Up As Owner</h2>
						</div>
						<div class="col-6">
							<div class="form-group" style="margin-bottom: 1vw;">
								<img src="img/add_image.png" style="height: 5vw; width: auto;" id="selected_img" src="#" alt="Your image"/>
							</div>
							<div class="form-group">
								<input type="file" name="picture_in" id="picture_in" accept="file/*" onchange="readURL(this);">
								<label class="form-input" style="cursor: pointer; padding: 1vw; box-sizing: border-box; border: solid white 1px; width: 100%; text-align: center;" for="picture_in" class="form-label"><i class="material-icons" style="display: inline-flex; vertical-align: middle; font-size: 2vw;">add_photo_alternate</i> Upload Picture</label>
							</div>
						</div>
					</div>
					<div class="two-col">
						<?php
						if ($isThere == "username")
							echo "<div class=\"form-group\" style=\"margin: 0px;\"><h4 style=\"color: white; text-align: center; margin-left: 5%;\">Username is already taken!</h4></div>";
						else if ($isThere == "phone")
							echo "<div class=\"form-group\" style=\"margin: 0px;\"><h4 style=\"color: white; text-align: center; margin-left: 5%;\">Phone number is already taken!</h4></div>";
						else if ($isThere == "mail")
							echo "<div class=\"form-group\" style=\"margin: 0px;\"><h4 style=\"color: white; text-align: center; margin-left: 5%;\">Mail is already taken!</h4></div>";
						else if ($isThere == "ssn")
							echo "<div class=\"form-group\" style=\"margin: 0px;\"><h4 style=\"color: white; text-align: center; margin-left: 5%;\">SSN Number belongs to another user!</h4></div>";
						?>
						<div class="col-6">
							<div class="form-group">
								<input class="form-input" type="text" name="username_in" placeholder="Username" required="">
							</div>
							<div class="form-group">
								<input class="form-input" type="password" name="password_in" placeholder="Password" required="">
							</div>
							<div class="form-group">
								<input class="form-input" type="text" name="name_in" placeholder="Name" required="">
							</div>
							<div class="form-group">
								<input class="form-input" type="text" name="surname_in" placeholder="Surname" required="">
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								<input class="form-input" type="text" name="phone_in" placeholder="Phone Number" required="">
							</div>
							<div class="form-group">
								<input class="form-input" type="text" name="mail_in" placeholder="Mail" required="">
							</div>
							<div class="form-group">
								<input class="form-input" type="text" name="ssn_in" placeholder="SSN Number" required="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="form-btn" name="signup_btn">Sign Up</button>
					</div>
					<div class="form-group">
						<button type="button" onclick="window.location.href='signup_choice.php'" class="form-btn" name="back_btn" style="float: left; width: 50%;">
							<< Back
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
            	$('#selected_img').show();
                $('#selected_img')
                    .attr('src', e.target.result)
                    .width(auto)
                    .height("5vw");
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
</html>