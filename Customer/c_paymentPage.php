<!DOCTYPE html>
<html>
<?php
include("common.php");
include("head.html");

$customer = mysqli_fetch_array(mysqli_query($con, "SELECT wallet FROM customer WHERE user_id='".$_SESSION['user_id']."'"));
$_SESSION['wallet'] = $customer['wallet'];

if ($_COOKIE['basket_cost'] <= 0) {
	header("Location: c_viewBasket.php?basket=0");
}

if (isset($_POST['give_order_btn'])) {
	if (isset($_COOKIE['basket_cost']) && $_COOKIE['basket_cost'] > $_SESSION['wallet']) {
		header("Location: c_paymentPage.php?pay=no");
	}
	else {
		mysqli_query($con, "INSERT INTO order_ (cost, status, address, order_date, customer_id) VALUES ('".$_COOKIE['basket_cost']."', '2', '".$_POST['address_in']."', '".date("Y-m-d h:i:s")."', '".$_SESSION['user_id']."')");
		$order_id = $con->insert_id;

		$cookie = $_COOKIE['basket'];
		$cookie = stripslashes($cookie);
		$basketOld = json_decode($cookie, true);

		foreach ($basketOld as $mkey => $mvalue) {
			$menu_id = explode("_", $mkey)[1];
			mysqli_query($con, "INSERT INTO contains_menu VALUES ('".$order_id."', '".$menu_id."', '1')");
			
			foreach ($basketOld[$mkey] as $ikey => $ivalue) {
				$select_meals = mysqli_query($con, "SELECT * FROM consists_of WHERE menu_id='".$menu_id."'");
				while ($row_meal = mysqli_fetch_array($select_meals)) {
					$select_ingredients = mysqli_query($con, "SELECT * FROM ingredient WHERE meal_id='".$row_meal['meal_id']."'");
					while ($row_ingredient = mysqli_fetch_array($select_ingredients)) {
						if ($ivalue == $row_ingredient['name']) {
							mysqli_query($con, "INSERT INTO contains_ingredient VALUES ('".$order_id."', '".$row_meal['meal_id']."', '".$ivalue."')");
							break;
						}
					}
				}
			}
			//$menu = mysqli_fetch_array(mysqli_query($con, "SELECT name, cost FROM menu WHERE menu_id='".$menu_id."'"));
			//setcookie('basket_cost', $_COOKIE['basket_cost'] - $menu['cost']);
		}

		$select_dgs = mysqli_query($con, "SELECT * FROM delivery_guy");
		while ($row_dg = mysqli_fetch_array($select_dgs)) {
			mysqli_query($con, "INSERT INTO delivers VALUES ('$order_id', '".$row_dg['user_id']."', '2')");
		}
		//$basketNew = json_encode($basketOld);
		//setcookie('basket', $basketNew);

		mysqli_query($con, "UPDATE customer SET wallet=wallet-".$_COOKIE['basket_cost']." WHERE user_id='".$_SESSION['user_id']."'");
		$_SESSION['wallet'] -= $_COOKIE['basket_cost'];
		setcookie("basket_cost", 0);
		$basket = array();
		$jsonBasket = json_encode($basket);
		setcookie("basket", $jsonBasket);
		header("Location: c_paymentPage.php?pay=done");
	}
}
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("c_dropdown.html"); ?>
		<div class="page" style="padding: 1%; box-sizing: border-box; display: flex; flex-direction: row; justify-content: center;">
			<form class="login-form" method="post" name="login_form" style="width: 50%; margin-top: 10vw;">
				<div class="row" style="width: 100%; justify-content: center;">
					<div class="col" style="width: 50%;">
						<h2 style="color: white; text-align: right; padding: 0.5vw;">Total Cost:</h2>
					</div>
					<div class="col" style="width: 50%;">
						<h2 style='text-align: center; padding: 0.5vw; margin-left: 1vw; margin-right: 10vw; color:white;'>$ <?php echo $_COOKIE['basket_cost']; ?></h2>
					</div>
				</div>
				<div class="row" style="width: 100%; justify-content: center;">
					<div class="col" style="width: 50%;">
						<h2 style="color: white; text-align: right; padding: 0.5vw;">Wallet:</h2>
					</div>
					<div class="col" style="width: 50%;">
						<h2 style='text-align: center; padding: 0.5vw; margin-left: 1vw; margin-right: 10vw; color:white;'>$ <?php echo $_SESSION['wallet']; ?></h2>
					</div>
				</div>
				<div class="row" id="info_row" style="width: 100%; justify-content: center;">
				</div>
				<div class="row" style="width: 100%; justify-content: center;">
					<h2 style="color: white; text-align: center;">Last Step</h2>
				</div>
				<div class="row" style="justify-content: center;">
					<div class="form-group">
						<input class="form-input" type="text" name="address_in" placeholder="Address" required="">
					</div>
				</div>
				<div class="row" style="justify-content: center;">
					<div class="form-group">
						<button class="form-btn" type="submit" id="give_order_btn" name="give_order_btn">Give Order</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
<?php
if (isset($_GET['pay'])) {
	if ($_GET['pay'] == 'done') {
		echo "<script>document.getElementById('info_row').innerHTML += '<h2 style=\'border: dashed 1px white; padding: 1vw;\'>Done!</h2>';</script>";
	}
	else {
		echo "<script>document.getElementById('info_row').innerHTML += '<h2 style=\'border: dashed 1px white; padding: 1vw;\'>There is no enough money in your wallet!</h2>';</script>";
	}
}
?>
</head>
</html>