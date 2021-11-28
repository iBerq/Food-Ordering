<!DOCTYPE html>
<html>
<?php
include("common.php");

$filter = "";
$orderBy = "order_date DESC";

if (isset($_POST['review_r_btn'])) {
	$order_id = $_POST['order_id_in'];
	$comment = $_POST['r_comment_in'];
	$rating = $_POST['r_rating_in'];
	$restaurant_id = $_POST['restaurant_id_in'];

	$insert_review = mysqli_query($con, "INSERT INTO restaurant_review (order_id, customer_id, restaurant_id, comment, rating) VALUES ('$order_id', '".$_SESSION['user_id']."', '$restaurant_id', '$comment', '$rating')");
	header("Reload: 0");
}

if (isset($_POST['review_d_btn'])) {
	$order_id = $_POST['order_id_in'];
	$rating = $_POST['d_rating_in'];
	$delivery_id = $_POST['delivery_id_in'];

	$insert_review = mysqli_query($con, "INSERT INTO delivery_review (order_id, customer_id, delivery_guy_id, rating) VALUES ('$order_id', '".$_SESSION['user_id']."', '$delivery_id', '$rating')");
	header("Reload: 0");
}

if (isset($_POST['order_by_in'])) {
	if ($_POST['order_by_in'] == 'cost_a') {
		$orderBy = "cost ASC";
	}
	else if ($_POST['order_by_in'] == 'cost_d') {
		$orderBy = "cost DESC";
	}
	else if ($_POST['order_by_in'] == 'date_a') {
		$orderBy = "order_date ASC";
	}
	else if ($_POST['order_by_in'] == 'date_d') {
		$orderBy = "order_date DESC";
	}
	else if ($_POST['order_by_in'] == 'd_date_a') {
		$orderBy = "delivery_date ASC";
	}
	else if ($_POST['order_by_in'] == 'd_date_d') {
		$orderBy = "delivery_date DESC";
	}
}

if (isset($_POST['apply_filter_btn'])) {
	if ($_POST['filter_select'] == 'f_restaurant') {
		$r_name = $_POST['restaurant_name_in'];
		$filter = "AND r.name=\"".$r_name."\"";
	}
	else if ($_POST['filter_select'] == 'f_date') {
		$s_date = $_POST['date_first_in'];
		$f_date = $_POST['date_last_in'];
		$filter = "AND order_date BETWEEN '".$s_date."' AND '".$f_date."'";
	}
	else if ($_POST['filter_select'] == 'f_cost') {
		$cost = $_POST['cost_in'];
		$filter = "AND o.cost>='".$cost."'";
	}
}

function getReport() {
	include("dbConnection.php");
	$customer_report = mysqli_fetch_array(mysqli_query($con, "SELECT FORMAT(SUM(cost),2) AS sum_cost, FORMAT(AVG(cost),2) AS avg_cost, COUNT(order_id) AS order_num FROM order_ WHERE customer_id='".$_SESSION['user_id']."'"));
	echo "<h3 style=\"margin: 0px;\">Until today you ordered ".$customer_report['order_num']." orders with total cost of ".$customer_report['sum_cost']." and average cost of ".$customer_report['avg_cost']."</h3>";
}

function getOrders($orderBy, $filter) {
	include("dbConnection.php");
	$select_orders = mysqli_query($con, "SELECT * FROM order_ o, contains_menu cm, menu m, restaurant r WHERE o.customer_id='".$_SESSION['user_id']."' AND cm.order_id=o.order_id AND cm.menu_id=m.menu_id AND m.restaurant_id=r.restaurant_id ".$filter." ORDER BY ".$orderBy."");
	$deliveryGuyAssigned;

	if (mysqli_num_rows($select_orders) != 0) {
		while ($row_order = mysqli_fetch_array($select_orders)) {
			$select_order_info = mysqli_query($con, "SELECT o.order_date, o.delivery_date, o.address, o.cost, o.status, dg.user_id AS dg_id, dg.name AS dg_name, dg.surname AS dg_surname FROM order_ o, delivers d , delivery_guy dg WHERE o.order_id = \"".$row_order['order_id']."\" AND d.decision = '1' AND d.order_id = o.order_id AND d.delivery_id = dg.user_id");
			$deliveryGuyAssigned = true;
			if(mysqli_num_rows($select_order_info) == 0){
				$deliveryGuyAssigned = false;
				$select_order_info = mysqli_query($con, "SELECT o.order_date, o.delivery_date, o.address, o.cost, o.status FROM order_ o WHERE o.order_id = \"".$row_order['order_id']."\"");
			}
			$order_info = mysqli_fetch_array($select_order_info);
			$review = mysqli_fetch_array(mysqli_query($con, "SELECT comment, rating FROM restaurant_review WHERE order_id=\"".$row_order['order_id']."\""));
			
			$menu_content = "";
			$select_menu_info = mysqli_query($con, "SELECT m.name, m.menu_id FROM contains_menu cm, order_ o, menu m WHERE cm.order_id = o.order_id AND cm.menu_id = m.menu_id AND o.order_id = \"".$row_order['order_id']."\"");

			$restaurant = "";
			while ($row_menu_info = mysqli_fetch_array($select_menu_info)) {
				$restaurant = mysqli_fetch_array(mysqli_query($con, "SELECT r.name, r.restaurant_id FROM restaurant r, menu m WHERE m.restaurant_id = r.restaurant_id AND m.menu_id = '".$row_menu_info['menu_id']."'") );

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
				 	"<div class=\"list-item\" id='li1' style='width: 50%;'>".
				 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 20%;'>Restaurant: </div>".
				 			"<div class=\"list-item-col\" style='width: 80%;'>".$restaurant['name']."</div>".
				 		"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 20%;'>Content: </div>".
					 		"<div class=\"list-item-col\" style='width: 80%;'>$menu_content</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 20%;'>Cost: </div>".
					 		"<div class=\"list-item-col\" style='width: 80%;'>".$order_info['cost']."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 20%;'>Order Date: </div>".
					 		"<div class=\"list-item-col\" style='width: 80%;'>". date("j M Y - H:i", strtotime($order_info['order_date']))."</div>".
					 	"</div>";
			if($order_info['status'] == 1){
				echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 20%;'>Delivery Date: </div>".
					 		"<div class=\"list-item-col\" style='width: 80%;'>". date("j M Y - H:i", strtotime($order_info['delivery_date']))."</div>".
					 	"</div>";
			}
			else{
				echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 20%;'>Delivery Status: </div>".
					 		"<div class=\"list-item-col\" style='width: 80%;'>";
				if($order_info['status'] == 2){
					echo 		"Preparing";
				}
				else if ($order_info['status'] == 0){
					echo 		"In delivery";
				}
				echo		"</div>".
					 	"</div>";
			}
			if($deliveryGuyAssigned == true){
				echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 20%;'>Delivery Guy: </div>".
				 			"<div class=\"list-item-col\" style='width: 80%;'>".$order_info['dg_name']." ".$order_info['dg_surname']."</div>".
				 		"</div>";
			}
				echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 20%;'>Address: </div>".
					 		"<div class=\"list-item-col\" style='width: 80%;'>".$order_info['address']."</div>".
					 	"</div>".
				 	"</div>".
				 	"<div class=\"list-item\" id='li2' style='width: 25%;'>".
				 		"<form method='post' width='100%;'>".
				 			"<h3>Restaurant Review</h3>".
				 			"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 			"<div class=\"form-label\" style=\"width: 30%;\">Rating: </div>";
			if ($review['rating'] != NULL) {
				echo 			"<div class=\"form-label\" style=\"width: 70%;\">".$review['rating']." / 10</div>".
					 		"</div>".
					 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 			"<div class=\"form-label\" style=\"width: 30%;\">Comment: </div>".
					 			"<div class=\"text-overflow\" style=\"width: 70%; text-align: left; font-size: 0.8vw; padding: 0px;\">".$review['comment']."</div>".
					 		"</div>";
				$respond = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM responds WHERE order_id='".$row_order['order_id']."'"));
				if ($respond == NULL) {
					echo 	"<div class=\"list-item-row\">".
								"<input type='hidden' value='".$restaurant['restaurant_id']."' name='restaurant_id_in'>".
								"<input type='hidden' value='".$row_order['order_id']."' name='order_id_in'>".
					 			"<button name='review_r_btn' disabled class=\"form-btn respond\" style=\"font-size: 1.4vw; height: 100%; width: 80%; margin:0 auto;\">Review</button>".
					 	 	"</div>";
				}
				else {
					echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
						 		"<div class=\"form-label\" style=\"width: 30%;\">Response: </div>".
						 		"<div class=\"text-overflow\" style=\"width: 70%; text-align: left; font-size: 0.8vw; padding: 0px;\">".$respond['response']."</div>".
						 	"</div>";

				}
			}
			else {
				if ($row_order['status'] != 1) {
					echo 		"<div class=\"form-label\" style=\"width: 70%;\"><input value='' name='r_rating_in' disabled type='number'  min='0' max='10' style='width: 3vw; font-size: 1vw;'> / 10</div>".
					 		"</div>".
					 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 			"<div class=\"form-label\" style=\"width: 30%;\">Comment: </div>".
					 			"<div class=\"text-overflow\" style=\"width: 70%; text-align: left; font-size: 0.8vw; padding: 0px;\"><input name='r_comment_in' disabled type='text' style='max-width: 90%; height: 5vw;'></div>".
					 		"</div>".
					 		"<div class=\"list-item-row\">".
					 			"<input type='hidden' value='".$restaurant['restaurant_id']."' name='restaurant_id_in'>".
					 			"<input type='hidden' value='".$row_order['order_id']."' name='order_id_in'>".
					 			"<button name='review_r_btn' disabled='true' class=\"form-btn\" style=\"transform: scale(0.8);\">Review</button>".
					 		"</div>";
				}
				else {
					echo 		"<div class=\"form-label\" style=\"width: 70%;\"><input name='r_rating_in' value='' type='number'  min='0' max='10' style='width: 3vw; font-size: 1vw;'> / 10</div>".
					 		"</div>".
					 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 			"<div class=\"form-label\" style=\"width: 30%;\">Comment: </div>".
					 			"<div class=\"text-overflow\" style=\"width: 70%; text-align: left; font-size: 0.8vw; padding: 0px;\"><input name='r_comment_in' type='text' style='max-width: 90%; height: 5vw;'></div>".
					 		"</div>".
					 		"<div class=\"list-item-row\">".
					 			"<input type='hidden' value='".$restaurant['restaurant_id']."' name='restaurant_id_in'>".
					 			"<input type='hidden' value='".$row_order['order_id']."' name='order_id_in'>".
					 			"<button name='review_r_btn' class=\"form-btn\" style=\"transform: scale(0.8);\">Review</button>".
					 		"</div>";
				}
			}

			echo    	"</form>".
					"</div>".
					"<div class=\"list-item\" id='li3' style='width: 100%; min-height: 15vw;'>".
						"<form method='post' width='100%;'>".
							"<h3>Delivery Guy Review</h3>";
			$review_d = mysqli_fetch_array(mysqli_query($con, "SELECT rating, comment FROM delivery_review WHERE order_id='".$row_order['order_id']."'"));
			if ($review_d['rating'] != NULL) {
				echo 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 			"<div class=\"form-label\" style=\"width: 30%;\">Rating: </div>".
					 			"<div class=\"form-label\" style=\"width: 70%;\">".$review_d['rating']." / 10</div>".
					 		"</div>".
					 	"</div>";
			}
			else {
				if($deliveryGuyAssigned == false){
					$dg_id_review = 0;
				}
				else{
					$dg_id_review = $order_info['dg_id'];
				}
				if ($row_order['status'] != 1) {
					echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 			"<div class=\"form-label\" style=\"width: 30%;\">Rating: </div>".
					 			"<div class=\"form-label\" style=\"width: 70%;\"><input name='d_rating_in' value='' disabled type='number' min='0' max='10' style='width: 3vw; font-size: 1vw;'> / 10</div>".
					 	 	"</div>".
					 	 	"<div class=\"list-item-row\">".
					 	 		"<input type='hidden' value='".$dg_id_review."' name='delivery_id_in'>".
					 	 		"<input type='hidden' value='".$restaurant['restaurant_id']."' name='restaurant_id_in'>".
					 	 		"<input type='hidden' value='".$row_order['order_id']."' name='order_id_in'>".
					 			"<button name='review_d_btn' disabled='true' class=\"form-btn\" style=\"transform: scale(0.8);\">Review</button>".
					 		"</div>".
					 	"</div>";
				}
				else {
					echo 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 			"<div class=\"form-label\" style=\"width: 30%;\">Rating: </div>".
					 			"<div class=\"form-label\" style=\"width: 70%;\"><input name='d_rating_in' value='' type='number'  min='0' max='10' style='width: 3vw; font-size: 1vw;'> / 10</div>".
					 	 	"</div>".
					 	 	"<div class=\"list-item-row\">".
					 	 		"<input type='hidden' value='".$dg_id_review."' name='delivery_id_in'>".
					 	 		"<input type='hidden' value='".$restaurant['restaurant_id']."' name='restaurant_id_in'>".
					 	 		"<input type='hidden' value='".$row_order['order_id']."' name='order_id_in'>".
					 			"<button name='review_d_btn' class=\"form-btn\" style=\"transform: scale(0.8);\">Review</button>".
					 		"</div>".
					 	"</div>";
				}
			}

			echo 	"</form>".
				"</div>";
		}
	}
}

include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("c_dropdown.html"); ?>
		<div class="page" style="padding: 1%; box-sizing: border-box;">
			<div class="list-item-row" style="padding: 0vw 0vw 1vw 0vw; justify-content: center;">
				<h2 style="margin: 0px;">Past Orders</h2>
			</div>
			<div class="list-item-row" style="padding: 0vw 0vw 1vw 0vw; justify-content: center;">
				<?php getReport(); ?>
			</div>
			<div class="row" style="padding: 0vw 0vw 1vw 0vw;">
				<div class="list-item-col" style="width: 50%; display: flex; flex-direction: row;">
					<div class="col-6" style="width: 20%; padding: 0px;">
						<form name="orderForm" method="post" style="display: flex; flex-direction: row;">
							<select class='form-input' name="order_by_in" onchange="orderForm.submit();" style="cursor: pointer; float: right; width: 100%; height: 2vw; font-size: 1vw;">
								<option value="" disabled="" selected="">Order By</option>
								<option value="date_a">Order Date (Ascending)</option>
								<option value="date_d">Order Date (Descending)</option>
								<option value="cost_a">Cost (Ascending)</option>
								<option value="cost_d">Cost (Descending)</option>
								<option value="d_date_a">Delivery Date (Ascending)</option>
								<option value="d_date_d">Delivery Date (Descending)</option>
							</select>
						</form>
					</div>
					<div class="col-6" style="width: 20%; padding: 0px 0px 0px 1vw;">
						<button type="button" class="form-btn" id="filter_btn" onclick="filterClicked()" style="float: right; width: 100%; height: 2vw; font-size: 1.3vw;">Filter</button>
					</div>
					<div class="col-6" style="width: 60%; margin: 0px 0px 0px 1vw;">
						<div id="filter_list" class="list" style="background-color: rgba(0,0,0,0.5); display: none; justify-content: center; flex-direction: row; padding: 0px; margin: 0px 0px 1vw 0px;">
							<form method="post">
								<select onchange="filterSelected()" id="filter_select" name="filter_select" class="form-input" style="height: 2vw; font-size: 1vw; width: 80%;">
									<option value="f_restaurant" id="f_restaurant" class="filter_option">Restaurant</option>
									<option value="f_date" id="f_date" class="filter_option">Date</option>
									<option value="f_cost" id="f_cost" class="filter_option">Cost</option>
								</select>
								<input type="text" placeholder="Restaurant Name" id="restaurant_name_in" name="restaurant_name_in" class="form-input" style="font-size: 1vw; height: 2vw; display: none; width: 80%;">
								<input type="date" placeholder="First Date" id="date_first_in" name="date_first_in" class="form-input" style="font-size: 1vw; height: 2vw; display: none; width: 80%;">
								<input type="date" placeholder="Last Date" id="date_last_in" name="date_last_in" class="form-input" style="font-size: 1vw; height: 2vw; display: none; width: 80%;">
								<input type="number" placeholder="Min Cost" id="cost_in" name="cost_in" class="form-input" style="font-size: 1vw; height: 2vw; display: none; width: 80%;">
								<button name="apply_filter_btn" class="form-btn" style="width: 80%; height: 2vw; font-size: 1.2vw; position: relative;">Apply Filter</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="list">
				<?php getOrders($orderBy, $filter); ?>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	function filterSelected() {
		var e = document.getElementById("filter_select").value;
		console.log(e);
		if (e == 'f_restaurant') {
			$('#restaurant_name_in').show();
			$('#date_first_in').hide();
			$('#date_last_in').hide();
			$('#cost_in').hide();
		}
		else if (e == 'f_date') {
			$('#restaurant_name_in').hide();
			$('#date_first_in').show();
			$('#date_last_in').show();
			$('#cost_in').hide();
		}
		else if (e == 'f_cost') {
			$('#restaurant_name_in').hide();
			$('#date_first_in').hide();
			$('#date_last_in').hide();
			$('#cost_in').show();
		}
	}

	function filterClicked() {
		if ($('#filter_list').css('display') == 'none') {
			$('#filter_list').show(1000);
		}
		else
			$('#filter_list').hide(1000);
	}
</script>
</head>
</html>