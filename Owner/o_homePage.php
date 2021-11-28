<!DOCTYPE html>
<html>
<?php
include("common.php");

$isThereRestaurant = false;
$select_restaurant = mysqli_query($con, "SELECT * FROM restaurant WHERE owner_id=\"".$_SESSION['user_id']."\"");
if (mysqli_num_rows($select_restaurant))
	$isThereRestaurant = true;

function getOrders($pastOrCurrent, $isThereRestaurant) {
	if (!$isThereRestaurant) {
		echo "<h3 style='text-align: center;'>Currently, you do not have a restaurant!</h3>";
		return;
	}
	include("dbConnection.php");
	if ($pastOrCurrent == "past")
		$status = 1;
	else if ($pastOrCurrent == "current")
		$status = 0;

	//$select_contains = mysqli_query($con, "SELECT * FROM contains_menu c WHERE c.menu_id IN (SELECT m.menu_id FROM menu m WHERE m.restaurant_id=\"".$_SESSION['restaurant_id']."\")");

	$select_order_ids = mysqli_query($con, "SELECT o.order_id FROM contains_menu cm, menu m, order_ o WHERE cm.menu_id = m.menu_id AND cm.order_id = o.order_id AND m.restaurant_id = \"".$_SESSION['restaurant_id']."\" AND o.status = \"$status\" GROUP BY o.order_id");

	if (mysqli_num_rows($select_order_ids) != 0) {
		while ($row_order = mysqli_fetch_array($select_order_ids)) {
			//$order = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM order_ WHERE order_id=\"".$contains_row['order_id']."\""));
			//$customer = mysqli_fetch_array(mysqli_query($con, "SELECT name, surname FROM customer WHERE user_id=\"".$order['customer_id']."\""));
			$order_info = mysqli_fetch_array(mysqli_query($con, "SELECT c.name, c.surname, o.order_date, o.delivery_date, o.address, o.cost FROM customer c, order_ o WHERE o.order_id = \"".$row_order['order_id']."\" AND c.user_id = o.customer_id"));

			$menu_content = "";
			$select_menu_info = mysqli_query($con, "SELECT m.name FROM contains_menu cm, order_ o, menu m WHERE cm.order_id = o.order_id AND cm.menu_id = m.menu_id AND o.order_id = \"".$row_order['order_id']."\"");
			while ($row_menu_info = mysqli_fetch_array($select_menu_info)) {
				$menu_content .= $row_menu_info['name'].", ";
			}
			$menu_content = substr($menu_content, 0, -2);

			echo "<div class=\"list-item\">".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Customer: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$order_info['name']." ".$order_info['surname']."</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Order: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>$menu_content</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Cost: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$order_info['cost']."</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Date: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>". date("j M Y - H:i", strtotime($order_info['order_date']))."</div>".
				 	"</div>";
			if ($order_info['delivery_date'] != NULL && $status == 1) {
				echo "<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Delivered: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>". date("j M Y - H:i", strtotime($order_info['delivery_date']))."</div>".
				 	 "</div>";
			}
			echo 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Address: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$order_info['address']."</div>".
				 	"</div>".
				 "</div>";
		}
	}
}

function getMenu($isThereRestaurant) {
	if (!$isThereRestaurant) {
		echo "<h3 style='text-align: center;'>Currently, you do not have a restaurant!</h3>";
		return;
	}
	include("dbConnection.php");
	$select_menus = mysqli_query($con, "SELECT * from customer_menu WHERE restaurant_id=\"".$_SESSION['restaurant_id']."\"");
	if (mysqli_num_rows($select_menus) != 0) {
		while ($menu = mysqli_fetch_array($select_menus)) {
			echo "<div id='".$menu['menu_id']."' class=\"list-item clickable\">".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\">Name: </div>".
				 		"<div class=\"list-item-col\">".$menu['name']."</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\">Price: </div>".
				 		"<div class=\"list-item-col\">".$menu['cost']."</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\"></div>".
				 		"<div class=\"list-item-col\">Click for details</div>".
				 	"</div>".
				 "</div>";
		}
	}
}

include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("o_dropdown.html"); ?>
		<div class="page">
			<div class="col-4">
				<header>Past Orders</header>
				<div class="list">
					<?php getOrders("past", $isThereRestaurant); ?>
				</div>
			</div>
			<div class="col-4">
				<header>Current Orders</header>
				<div class="list">
					<?php getOrders("current", $isThereRestaurant); ?>
				</div>
			</div>
			<div class="col-4">
				<header>Menus</header>
				<div class="list">
					<?php getMenu($isThereRestaurant); ?>
				</div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(".clickable").click(function () {
		var menu_id = $(this).attr("id");
		document.cookie= "menu_id="+menu_id;
		window.location.href = "o_menuPage.php?from=edit";
	});
</script>
</head>
</html>