<!DOCTYPE html>
<html>
<?php
include("common.php");

if (isset($_POST['ready_btn'])) {
	$order_id = $_POST['order_id_in'];

	mysqli_query($con, "UPDATE order_ SET status=0 WHERE order_id='$order_id'");
}

function getOrders($pastOrCurrent) {
	include("dbConnection.php");
	if ($pastOrCurrent == "current_delivery")
		$status = 0;
	else if ($pastOrCurrent == "current_prep")
		$status = 2;

	$select_order_ids = mysqli_query($con, "SELECT o.order_id FROM contains_menu cm, menu m, order_ o WHERE cm.menu_id = m.menu_id AND cm.order_id = o.order_id AND m.restaurant_id = \"".$_SESSION['restaurant_id']."\" AND o.status = '$status' GROUP BY o.order_id");

	if (mysqli_num_rows($select_order_ids) != 0) {
		while ($row_order = mysqli_fetch_array($select_order_ids)) {
			$order_info = mysqli_fetch_array(mysqli_query($con, "SELECT o.order_id, c.name, c.surname, o.order_date, o.address, o.cost, o.status FROM customer c, order_ o WHERE o.order_id = \"".$row_order['order_id']."\" AND c.user_id = o.customer_id"));
			$menu_content = "";
			$select_menu_info = mysqli_query($con, "SELECT m.name, m.menu_id FROM contains_menu cm, order_ o, menu m WHERE cm.order_id = o.order_id AND cm.menu_id = m.menu_id AND o.order_id = \"".$row_order['order_id']."\"");
			
			while ($row_menu_info = mysqli_fetch_array($select_menu_info)) {
				$menu_content .= $row_menu_info['name']." -> ";
				$select_meal_info = mysqli_query($con, "SELECT me.name, me.meal_id FROM menu m, consists_of co, meal me WHERE m.menu_id = \"".$row_menu_info['menu_id']."\" AND m.menu_id = co.menu_id AND co.meal_id = me.meal_id");
				
				$meals = "";
				while ($row_meal_info = mysqli_fetch_array($select_meal_info)) {
					$meals .= $row_meal_info['name'];
					$select_ingredient_info = mysqli_query($con, "SELECT ingredient_name AS name FROM contains_ingredient WHERE order_id = \"".$row_order['order_id']."\" AND meal_id = \"".$row_meal_info['meal_id']."\"");
					
					$ingredients = "";
					while ($row_ingredient_info = mysqli_fetch_array($select_ingredient_info)) {
						$ingredients .= $row_ingredient_info['name'] . ", ";
					}
					$ingredients = substr($ingredients, 0, -2);
					$meals .= " (".$ingredients.") - ";
				}
				$meals = substr($meals, 0, -2);
				$menu_content .= $meals . "<br>";
			}

			echo "<div class=\"list-row\">".
				 	"<div class=\"list-item\">".
				 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 15%;'>Customer: </div>".
				 			"<div class=\"list-item-col\" style='width: 85%;'>".$order_info['name']." ".$order_info['surname']."</div>".
				 		"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Content: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>$menu_content</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Cost: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>".$order_info['cost']."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Order Date: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>". date("j M Y - H:i", strtotime($order_info['order_date']))."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Address: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>".$order_info['address']."</div>".
					 	"</div>".
				 	"</div>".
				 	"<div class=\"list-item\">".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw; text-align: center;'>";
			if ($order_info['status'] == '2') {
				echo		"<form method='post'>".
					 			"<input type='hidden' name=\"order_id_in\" value=\"".$row_order['order_id']."\">".
					 			"<button name='ready_btn' class=\"form-btn\" style='font-size: 1vw;'>READY FOR DELIVERY</button>".
					 		"</form>";
			}
			else {
				echo		"<label>In delivery.</label>";
			}
			echo        "</div>".
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
		<div class="page" style="padding: 1%; box-sizing: border-box;">
			<header>Current Orders</header>
			<div class="list">
				<?php getOrders("current_prep"); ?>
				<?php getOrders("current_delivery"); ?>
			</div>
		</div>
	</div>
</body>
</head>
</html>