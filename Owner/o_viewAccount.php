<!DOCTYPE html>
<html>

<?php
include("common.php");
include("head.html");

if (isset($_POST['save_btn'])) {
	$update_user = mysqli_query($con, "UPDATE user SET username=\"".$_POST['username_in']."\", password=\"".$_POST['password_in']."\" WHERE user_id=\"".$_SESSION['user_id']."\"");
	$update_owner = mysqli_query($con, "UPDATE owner SET name=\"".$_POST['name_in']."\", surname=\"".$_POST['surname_in']."\", phone_number=\"".$_POST['phone_number_in']."\", mail=\"".$_POST['mail_in']."\", ssn=\"".$_POST['ssn_in']."\" WHERE user_id=\"".$_SESSION['user_id']."\"");

	//$select_region = mysqli_query($con, "SELECT * FROM region WHERE name=\"".$_POST['r_region_in']."\"");
	//$row_region = mysqli_fetch_array($select_region);
	$update_restaurant = mysqli_query($con, "INSERT INTO restaurant (name, owner_id, region_id, phone_number, address, cuisine_type) VALUES (\"".$_POST['r_name_in']."\", \"".$_SESSION['user_id']."\", \"".$_POST['r_region_in']."\", \"".$_POST['r_phone_in']."\", \"".$_POST['r_address_in']."\", \"".$_POST['r_cuisine_in']."\") ON DUPLICATE KEY UPDATE name = \"".$_POST['r_name_in']."\", region_id = \"".$_POST['r_region_in']."\", phone_number = \"".$_POST['r_phone_in']."\", address = \"".$_POST['r_address_in']."\", cuisine_type = \"".$_POST['r_cuisine_in']."\";");
	if ($update_user == false || $update_owner == false || $update_restaurant == false) {
		header("Location: o_viewAccount.php?update=false");
	}
	else {
		header("Location: o_viewAccount.php?update=true");
		$_SESSION['name'] = $_POST['name_in'];
		$_SESSION['surname'] = $_POST['surname_in'];
		$_SESSION['username'] = $_POST['username_in'];
		$_SESSION['password'] = $_POST['password_in'];
		$_SESSION['mail'] = $_POST['mail_in'];
		$_SESSION['phone_number'] = $_POST['phone_number_in'];
		$_SESSION['ssn'] = $_POST['ssn_in'];
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
			"<div class=\"list-item-row\" style='align-items: center;'>".
				"<div class=\"list-item-col\"><label class=\"form-label\">SSN No: </label></div>".
				"<div class=\"list-item-col\"><input name=\"ssn_in\" class=\"form-input\" type=\"text\" value=\"".$_SESSION['ssn']."\"></div>".
			"</div>".
		 "</div>";
}

function getRestaurantInfo($con) {
	$select_restaurant = mysqli_query($con, "SELECT * from restaurant WHERE owner_id=\"".$_SESSION['user_id']."\"");
	$restaurant = mysqli_fetch_array($select_restaurant);

	$select_region = mysqli_query($con, "SELECT * from region");
	$regions = "";
	while ($row_region = mysqli_fetch_array($select_region)) {
		if ($restaurant['region_id'] == $row_region['region_id'])
			$regions .= "<option selected value=\"".$row_region['region_id']."\">".$row_region['name']."</option>";
		else
			$regions .= "<option value=\"".$row_region['region_id']."\">".$row_region['name']."</option>";
	}

	echo "<div class=\"list-item\" style=\"background-color: transparent;\">".
		 	"<div class=\"list-item-row\" style='align-items: center;'>".
		 		"<div class=\"list-item-col\"><label class=\"form-label\">Name: </label></div>".
		 		"<div class=\"list-item-col\"><input name=\"r_name_in\" class=\"form-input\" type=\"text\" value=\"".$restaurant['name']."\"></div>".
		 	"</div>".
		 	"<div class=\"list-item-row\" style='align-items: center;'>".
		 		"<div class=\"list-item-col\"><label class=\"form-label\">Region: </label></div>".
		 		"<div class=\"list-item-col\">".
		 			"<select name='r_region_in' class='form-input'>".
		 				$regions.
		 			"</select>".
		 		"</div>".
		 	"</div>".
		 	"<div class=\"list-item-row\" style='align-items: center;'>".
		 		"<div class=\"list-item-col\"><label class=\"form-label\">Address: </label></div>".
		 		"<div class=\"list-item-col\"><input name=\"r_address_in\" class=\"form-input\" type=\"text\" value=\"".$restaurant['address']."\"></div>".
		 	"</div>".
		 	"<div class=\"list-item-row\" style='align-items: center;'>".
		 		"<div class=\"list-item-col\"><label class=\"form-label\">Phone Number: </label></div>".
		 		"<div class=\"list-item-col\"><input name=\"r_phone_in\" class=\"form-input\" type=\"text\" value=\"".$restaurant['phone_number']."\"></div>".
		 	"</div>".
		 	"<div class=\"list-item-row\" style='align-items: center;'>".
		 		"<div class=\"list-item-col\"><label class=\"form-label\">Cuisine Type: </label></div>".
		 		"<div class=\"list-item-col\"><input name=\"r_cuisine_in\" class=\"form-input\" type=\"text\" value=\"".$restaurant['cuisine_type']."\"></div>".
		 	"</div>".
		 	"<div class=\"list-item-row\" style='align-items: center;'>".
		 		"<div class=\"list-item-col\"><label class=\"form-label\">Rating: </label></div>".
		 		"<div class=\"list-item-col\"><input name=\"r_rating_in\" disabled=\"true\" class=\"form-input\" type=\"text\" value=\"".$restaurant['rating']."/10\"></div>".
		 	"</div>".
		 "</div>";
}
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("o_dropdown.html"); ?>
		<div class="page">
			<form id="form" class="login-form" method="post">
				<div class="two-col">
					<div class="col-6" style="padding: 2% 5% 3% 15%;">
						<header>Personal Info</header>
						<br>
						<div class="list" style="min-height: 500px; background-color: rgba(0,0,0,0.3);">
							<?php getPersonalInfo($con); ?>
						</div>
					</div>
					<div class="col-6" style="padding: 2% 15% 3% 5%;">
						<header>Restaurant Info</header>
						<br>
						<div id="restaurant_list" class="list" style="min-height: 500px; background-color: rgba(0,0,0,0.3);">
							<?php getRestaurantInfo($con); ?>
						</div>
					</div>
				</div>
				<?php
				if (isset($_GET['update'])) {
					if ($_GET['update'] == 'false')
						echo "<h3 style='color: white; margin-top: 0.5vw;'>Cannot update!</h3>";
					else
						echo "<h3 style='color: white; margin-top: 0.5vw;'>Update is successful!</h3>";
				}
				?>
				<button class="form-btn" name="save_btn" style="width: 20%;"><i class="material-icons" style="font-size: 1.7vw; transform: translateY(10%);">save</i> Save</button>
			</form>
		</div>
	</div>
</body>
</head>
</html>