<!DOCTYPE html>
<html>
<?php
include("common.php");

if (isset($_POST['delete_response_btn'])) {
	$order_id = $_POST['order_id_in'];

	mysqli_query($con, "DELETE FROM responds WHERE order_id=\"$order_id\"");
}

if (isset($_POST['send_response_btn'])) {
	$order_id = $_POST['order_id_in'];
	$response = $_POST['response_in'];

	$insert_respond = mysqli_query($con, "INSERT INTO responds (order_id, owner_id, response) VALUES (\"$order_id\", \"".$_SESSION['user_id']."\", \"$response\")");
}

function getOrders($pastOrCurrent) {
	include("dbConnection.php");
	if ($pastOrCurrent == "past")
		$status = 1;
	else if ($pastOrCurrent == "current_delivery")
		$status = 0;
	else if ($pastOrCurrent == "current_prep")
		$status = 2;

	$select_order_ids = mysqli_query($con, "SELECT o.order_id FROM contains_menu cm, menu m, order_ o WHERE cm.menu_id = m.menu_id AND cm.order_id = o.order_id AND m.restaurant_id = \"".$_SESSION['restaurant_id']."\" AND o.status = '$status' GROUP BY o.order_id");

	if (mysqli_num_rows($select_order_ids) != 0) {
		while ($row_order = mysqli_fetch_array($select_order_ids)) {
			$order_info = mysqli_fetch_array(mysqli_query($con, "SELECT c.name, c.surname, o.order_date, o.delivery_date, o.address, o.cost, dg.name AS dg_name, dg.surname AS dg_surname FROM customer c, order_ o, delivers d , delivery_guy dg WHERE o.order_id = \"".$row_order['order_id']."\" AND d.decision = '1' AND d.order_id = o.order_id AND d.delivery_id = dg.user_id"));
			$review = mysqli_fetch_array(mysqli_query($con, "SELECT rw.comment, rw.rating FROM restaurant_review rw WHERE rw.order_id = \"".$row_order['order_id']."\""));
			$menu_content = "";
			$select_menu_info = mysqli_query($con, "SELECT m.name, m.menu_id FROM contains_menu cm, order_ o, menu m WHERE cm.order_id = o.order_id AND cm.menu_id = m.menu_id AND o.order_id = \"".$row_order['order_id']."\"");
			
			while ($row_menu_info = mysqli_fetch_array($select_menu_info)) {
				$menu_content .= $row_menu_info['name']." -> ";
				$select_meal_info = mysqli_query($con, "SELECT me.name, me.meal_id FROM menu m, consists_of co, meal me WHERE m.menu_id = \"".$row_menu_info['menu_id']."\" AND m.menu_id = co.menu_id AND co.meal_id = me.meal_id");
				
				$meals = "";
				while ($row_meal_info = mysqli_fetch_array($select_meal_info)) {
					$meals .= $row_meal_info['name'];
					$select_ingredient_info = mysqli_query($con, "SELECT i.name FROM contains_ingredient ci, ingredient i WHERE ci.meal_id = \"".$row_meal_info['meal_id']."\" AND i.name = ci.ingredient_name");
					
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
					 		"<div class=\"list-item-col\" style='width: 15%;'>Order Date: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>". date("j M Y - H:i", strtotime($order_info['order_date']))."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Delivery Date: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>". date("j M Y - H:i", strtotime($order_info['delivery_date']))."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 15%;'>Delivery Guy: </div>".
				 			"<div class=\"list-item-col\" style='width: 85%;'>".$order_info['dg_name']." ".$order_info['dg_surname']."</div>".
				 		"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Address: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>".$order_info['address']."</div>".
					 	"</div>".
				 	"</div>".
				 	"<div class=\"list-item\">".
				 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"form-label\" style=\"width: 30%;\">Rating: </div>";
			if ($review['rating'] != NULL) {
				echo "<div class=\"form-label\" style=\"width: 70%;\">".$review['rating']." / 10</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"form-label\" style=\"width: 30%;\">Comment: </div>".
					 		"<div class=\"text-overflow\" style=\"width: 70%; text-align: left; font-size: 0.8vw; padding: 0px;\">".$review['comment']."</div>".
					 	"</div>";
				$respond = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM responds WHERE order_id='".$row_order['order_id']."'"));
				if ($respond == NULL) {
				echo	"<div class=\"list-item-row\">".
					 		"<button class=\"form-btn respond\" style=\"font-size: 1.4vw; height: 100%; width: 80%; margin:0 auto;\">Respond</button>".
					 	"</div>".
					 	"<form class=\"login-form\" method=\"post\" id=\"respond-form\" style=\"display: none;\">".
					 		"<div class=\"form-group\">".
					 			"<input type='hidden' name=\"order_id_in\" value=\"".$row_order['order_id']."\">".
								"<textarea name=\"response_in\" id=\"ta_review\" placeholder=\"Type your response here...\" style='resize: none; font-size: 0.8vw; padding: 1vw; min-height: 5vw; max-width: 100%; min-width: 100%; border-radius: 1vw;' class='form-input input-ta sm-placeholder'></textarea>".
							"</div>".
							"<div class=\"form-group\">".
								"<input type='submit' style=\"font-size: 1vw; height: 2vw; width: 50%; float: right;\" class=\"form-btn\" name=\"send_response_btn\" value='Send'>".
							"</div>".
						"</form>";
				}
				else {
					echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
						 		"<div class=\"form-label\" style=\"width: 30%;\">Response: </div>".
						 		"<div class=\"text-overflow\" style=\"width: 70%; text-align: left; font-size: 0.8vw; padding: 0px;\">".$respond['response']."</div>".
						 	"</div>".
						 	"<form class=\"login-form\" method=\"post\" id=\"respond-form\">".
						 		"<input type='hidden' name=\"order_id_in\" value=\"".$row_order['order_id']."\">".
							 	"<div class=\"list-item-row\">".
							 		"<button name=\"delete_response_btn\" class=\"form-btn\" style=\"font-size: 1vw; height: 100%; width: 80%; margin:0 auto; margin-top: 0.5vw;\">Delete Response</button>".
							 	"</div>".
							 "</form>";

				}
			}
			else {
				echo "<div class=\"form-label\" style=\"width: 70%;\">? / 10</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"form-label\" style=\"width: 30%;\">Comment: </div>".
					 		"<div class=\"text-overflow\" style=\"width: 70%; text-align: left; font-size: 0.8vw; padding: 0px;\">No comment available yet.</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\">".
					 		"<button disabled='true' class=\"form-btn\" style=\"transform: scale(0.8);\">Respond</button>".
					 	"</div>";
			}
					 		
			echo     "</div>".
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
			<div class="list-item-row" style="padding: 0vw 0vw 1vw 0vw; justify-content: center;">
				<h2 style="margin: 0px;">Past Orders</h2>
			</div>
			<div class="list">
				<?php getOrders("past"); ?>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$('.form-btn.respond').click(function () {
		var element = document.getElementById("respond-form");
		if (element.style.display == 'none') {
			$(this).parent().css({
			  "transform" 	: "scale(0.7)",
			});
			$("#respond-form").show(1000);
		}
		else {
			$(this).parent().css({
			  "transform" 	: "scale(1)",
			});
			$("#respond-form").hide(1000);
		}
	});
</script>
</head>
</html>