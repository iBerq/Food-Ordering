<!DOCTYPE html>
<html>
<?php
include("common.php");

if (isset($_POST['remove_btn'])) {
	$mkey_value = $_POST["mkey_in"];
	$cookie = $_COOKIE['basket'];
	$cookie = stripslashes($cookie);
	$basketOld = json_decode($cookie, true);

	foreach ($basketOld as $mkey => $mvalue) {
		if ($mkey == $mkey_value) {
			unset($basketOld[$mkey]);
			$menu_id = explode("_", $mkey)[1];
			$menu = mysqli_fetch_array(mysqli_query($con, "SELECT name, cost FROM menu WHERE menu_id='".$menu_id."'"));
			setcookie('basket_cost', $_COOKIE['basket_cost'] - $menu['cost']);
		}
	}
	$basketNew = json_encode($basketOld);
	setcookie('basket', $basketNew);
	header("Refresh: 0");
}

function getBasket() {
	include("dbConnection.php");

	$cookie = $_COOKIE['basket'];
	$cookie = stripslashes($cookie);
	$basketOld = json_decode($cookie, true);

	foreach ($basketOld as $mkey => $mvalue) {
		$menu_id = explode("_", $mkey)[1];
		$menu = mysqli_fetch_array(mysqli_query($con, "SELECT name, cost FROM menu WHERE menu_id='".$menu_id."'"));

		echo "<div class='row'>".
				 "<div class=\"list-item\" style='width: 90%; margin: 1vw;'>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Name: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$menu['name']."</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Price: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$menu['cost']."</div>".
				 	"</div>";
		$ingredients = "";
		foreach ($basketOld[$mkey] as $ikey => $ivalue) {
			$ingredients .= $ivalue.", ";
		}
		$ingredients = substr($ingredients, 0, -2);
		echo 		"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Content: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$ingredients."</div>".
				 	"</div>".
			 	"</div>".
			 	"<div class=\"list-item\" style='background-color: transparent; width: 10%; margin: 1vw; padding: 0px;'>".
			 		"<form method='post'>".
			 			"<input type='hidden' name='mkey_in' value='".$mkey."'>".
			 			"<button type='submit' name='remove_btn' class='form-btn remove' style='border: 0.2vw black solid; border-radius: 2vw; width: 100%; font-size: 1.2vw; height: 100%; margin: 0px;'>Remove</button>".
			 		"</form>".
			 	/*	"<form method='post' style='display: flex; flex-direction: column; height: 100%; width: 100%;'>".
			 			"<div class=\"row\" style='justify-content: center; width: 100%;'>".
			 				"<button name='inc_btn' class='form-btn increment' style='width: 40%; margin-bottom: 1vw;'>⇧</button>".
			 			"</div>".
			 			"<div class=\"row\" style='justify-content: center; width: 100%;'>".
			 				"<input class='form-input' value='1' style='width: 50%; text-align: center;'></input>".
						"</div>".
						"<div class=\"row\" style='justify-content: center; width: 100%;'>".
			 				"<button name='dec_btn' class='form-btn decrement' style='width: 40%; margin-top: 1vw;'>⇩</button>".
			 			"</div>".
			 		"</form>".*/
			 	"</div>".
			 "</div>";
	}
}

include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("c_dropdown.html"); ?>
		<div class="page" style="padding: 1%; box-sizing: border-box;">
			<header>Basket</header>
			<div class="list" id="basket_list" style="height: 84%;">
				<?php getBasket(); ?>
			</div>
			<div class="row" style="width: 50%; float: right;">
				<div class="row" style="width: 50%; justify-content: flex-end;">
					<h2 style="color: white;">Total Price: $<?php echo $_COOKIE['basket_cost']; ?></h2>
				</div>
				<div class="col" style="width: 50%; justify-content: center;">
					<div class="row" id='info_row' style="justify-content: flex-end;">
						<button onclick="window.location.href='c_paymentPage.php'" name="add_basket_btn" class="form-btn" style="width: 70%; height: 3vw; font-size: 1.5vw;">Continue</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<?php
if (isset($_GET['basket']) && $_GET['basket'] == 0) {
	echo "<script>document.getElementById('basket_list').innerHTML += '<h2>There is no menu in your basket!</h2>';</script>";
}
?>
</head>
</html>