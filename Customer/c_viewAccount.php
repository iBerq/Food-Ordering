<!DOCTYPE html>
<html>

<?php
include("common.php");
include("head.html");

if (isset($_POST['save_btn'])) {
	$update_user = mysqli_query($con, "UPDATE user SET username=\"".$_POST['username_in']."\", password=\"".$_POST['password_in']."\" WHERE user_id=\"".$_SESSION['user_id']."\"");
	$update_customer = mysqli_query($con, "UPDATE customer SET name=\"".$_POST['name_in']."\", surname=\"".$_POST['surname_in']."\", phone_number=\"".$_POST['phone_number_in']."\", mail=\"".$_POST['mail_in']."\" WHERE user_id=\"".$_SESSION['user_id']."\"");

	if ($update_user == false || $update_customer == false) {
		header("Location: c_viewAccount.php?update=false");
	}
	else {
		$_SESSION['name'] = $_POST['name_in'];
		$_SESSION['surname'] = $_POST['surname_in'];
		$_SESSION['username'] = $_POST['username_in'];
		$_SESSION['password'] = $_POST['password_in'];
		$_SESSION['mail'] = $_POST['mail_in'];
		$_SESSION['phone_number'] = $_POST['phone_number_in'];
		header("Location: c_viewAccount.php?update=true");
	}
}

function getPersonalInfo($con) {
	echo "<div class=\"list-item\" style=\"background-color: transparent;\">".
		 	"<div class=\"list-item-row\" style='align-items: center;'>".
				"<div class=\"list-item-col\"><label class=\"form-label\">Name: </label></div>".
				"<div class=\"list-item-col\"><input name=\"name_in\" class=\"form-input\" type=\"text\" value=\"".$_SESSION['name']."\"></div>".
			"</div>".
			"<div class=\"list-item-row\" style='align-items: center;'>".
				"<div class=\"list-item-col\"><label class=\"form-label\">Surname: </label></div>".
				"<div class=\"list-item-col\"><input name=\"surname_in\" class=\"form-input\" type=\"text\" value=\"".$_SESSION['surname']."\"></div>".
			"</div>".
			"<div class=\"list-item-row\" style='align-items: center;'>".
				"<div class=\"list-item-col\"><label class=\"form-label\">Username: </label></div>".
				"<div class=\"list-item-col\"><input name=\"username_in\" class=\"form-input\" type=\"text\" value=\"".$_SESSION['username']."\"></div>".
			"</div>".
			"<div class=\"list-item-row\" style='align-items: center;'>".
				"<div class=\"list-item-col\"><label class=\"form-label\">Password: </label></div>".
				"<div class=\"list-item-col\"><input name=\"password_in\" class=\"form-input\" type=\"text\" value=\"".$_SESSION['password']."\"></div>".
			"</div>".
			"<div class=\"list-item-row\" style='align-items: center;'>".
				"<div class=\"list-item-col\"><label class=\"form-label\">Mail: </label></div>".
				"<div class=\"list-item-col\"><input name=\"mail_in\" class=\"form-input\" type=\"text\" value=\"".$_SESSION['mail']."\"></div>".
			"</div>".
			"<div class=\"list-item-row\" style='align-items: center;'>".
				"<div class=\"list-item-col\"><label class=\"form-label\">Phone Number: </label></div>".
				"<div class=\"list-item-col\"><input name=\"phone_number_in\" class=\"form-input\" type=\"text\" value=\"".$_SESSION['phone_number']."\"></div>".
			"</div>".
		 "</div>";
}

?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("c_dropdown.html"); ?>
		<div class="page" style="padding: 1vw; box-sizing: border-box;">
			<div class="col-6" style="align-self: center;">
			<form id="form" class="login-form" method="post" style="width: 100%; height: 90%;">
				<header>Personal Info</header>
				<br>
				<div class="list" style="min-height: 500px; background-color: rgba(0,0,0,0.3);">
					<?php getPersonalInfo($con); ?>
				</div>
				<button class="form-btn" name="save_btn" style="width: 20%;"><i class="material-icons" style="font-size: 1.7vw; transform: translateY(10%);">save</i> Save</button>
			</form>
			<?php
			if (isset($_GET['update'])) {
				if ($_GET['update'] == 'false')
					echo "<h3 style='color: white; margin-top: 0.5vw;'>Cannot update!</h3>";
				else
					echo "<h3 style='color: white; margin-top: 0.5vw;'>Update is successful!</h3>";
			}
			?>
			</div>
		</div>
	</div>
</body>
</head>
</html>