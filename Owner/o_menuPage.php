<!DOCTYPE html>
<html>
<?php
include("common.php");

$menu_id = "";
$mealIds = array();
$mealNames = array();
$mealCalories = array();
$mealTypes = array();
$mealQuantities = array();
$counter = 0;

if ($_GET['from'] == "new") {
	setcookie("menu_id", "-1");
}

if (isset($_POST['remove_meal_btn'])) {
	$menu_id = $_COOKIE['menu_id'];
	$menu = mysqli_fetch_array(mysqli_query($con, "SELECT * from menu WHERE menu_id='$menu_id'"));
	$name = $menu['name'];
	$cost = $menu['cost'];
	$meal_id = $_POST['meal_id_in'];

	mysqli_query($con, "INSERT INTO menu (restaurant_id, name, cost, calorie, visibility, deleted) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$cost', '0', '1', '0')");
	$menu_id = $con->insert_id;
	mysqli_query($con, "UPDATE menu SET deleted='1' WHERE menu_id='".$_COOKIE['menu_id']."'");

	$select_css = mysqli_query($con, "SELECT * FROM consists_of WHERE menu_id='".$_COOKIE['menu_id']."'");

	$isThere = false;
	while ($row_cs = mysqli_fetch_array($select_css)) {
		mysqli_query($con, "INSERT INTO consists_of (menu_id, meal_id, quantity) VALUES ('".$menu_id."', '".$row_cs['meal_id']."', '".$row_cs['quantity']."')");
		if ($meal_id == $row_cs['meal_id']) {
			if ($row_cs['quantity'] > 1) {
				mysqli_query($con, "UPDATE consists_of SET quantity=quantity-1 WHERE menu_id='$menu_id' AND meal_id='$meal_id'");
			}
			else {
				mysqli_query($con, "DELETE FROM consists_of WHERE menu_id='".$menu_id."' AND meal_id='".$meal_id."'");
			}
		}
	}

	setcookie("menu_id", $menu_id);
	header("Location: o_menuPage.php?from=edit");
}

if (isset($_POST['save_btn'])) {
	$name = $_POST['item_name_in'];
	$cost = $_POST['item_cost_in'];

	if ($_GET['from'] == 'edit') {
		$select_css = mysqli_query($con, "SELECT * FROM consists_of WHERE menu_id='".$_COOKIE['menu_id']."'");
		if (mysqli_num_rows($select_css) == 0) {
			mysqli_query($con, "UPDATE menu SET name='$name', cost='$cost' WHERE menu_id='".$_COOKIE['menu_id']."'");
		}
		else {
			mysqli_query($con, "INSERT INTO menu (restaurant_id, name, cost, calorie, visibility, deleted) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$cost', '0', '1', '0')");
			$menu_id = $con->insert_id;
			mysqli_query($con, "UPDATE menu SET deleted='1' WHERE menu_id='".$_COOKIE['menu_id']."'");

			while ($row_cs = mysqli_fetch_array($select_css)) {
				mysqli_query($con, "INSERT INTO consists_of (menu_id, meal_id, quantity) VALUES ('".$menu_id."', '".$row_cs['meal_id']."', '".$row_cs['quantity']."')");
			}
		}
	}
	else {
		mysqli_query($con, "INSERT INTO menu (restaurant_id, name, cost, calorie, visibility, deleted) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$cost', '0', '1', '0')");
		$menu_id = $con->insert_id;
	}
	setcookie("menu_id", $menu_id);
	header("Location: o_menuPage.php?from=edit");
}

if (isset($_POST['add_to_menu_btn'])) {
	$meal_id = $_POST['meal_in'];
	$menu_id = $_COOKIE['menu_id'];
	$menu = mysqli_fetch_array(mysqli_query($con, "SELECT * from menu WHERE menu_id='$menu_id'"));
	$name = $menu['name'];
	$cost = $menu['cost'];
	$calorie = $menu['calorie'];

	if ($_COOKIE['menu_id'] == '-1') {
		echo "<script type='text/javascript'>alert('Cannot add meal before creating a menu! First create a menu!');</script>";
	}
	else {
		mysqli_query($con, "INSERT INTO menu (restaurant_id, name, cost, calorie, visibility, deleted) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$cost', '0', '1', '0')");
		$menu_id = $con->insert_id;
		mysqli_query($con, "UPDATE menu SET deleted='1' WHERE menu_id='".$_COOKIE['menu_id']."'");

		$select_css = mysqli_query($con, "SELECT * FROM consists_of WHERE menu_id='".$_COOKIE['menu_id']."'");
		
		if (mysqli_num_rows($select_css) == 0) {
			mysqli_query($con, "INSERT INTO consists_of (menu_id, meal_id) VALUES ('".$menu_id."', '".$meal_id."')");
		}
		else {
			$isThere = false;
			while ($row_cs = mysqli_fetch_array($select_css)) {
				mysqli_query($con, "INSERT INTO consists_of (menu_id, meal_id, quantity) VALUES ('".$menu_id."', '".$row_cs['meal_id']."', '".$row_cs['quantity']."')");
				if ($meal_id == $row_cs['meal_id']) {
					$isThere = true;
					mysqli_query($con, "UPDATE consists_of SET quantity=quantity+1 WHERE menu_id='$menu_id' AND meal_id='$meal_id'");
				}
			}
			if (!$isThere) {
				mysqli_query($con, "INSERT INTO consists_of (menu_id, meal_id) VALUES ('".$menu_id."', '".$meal_id."')");
			}
		}

		setcookie("menu_id", $menu_id);
		header("Location: o_menuPage.php?from=edit");
	}
}

function getMenuInfo() {
	global $menu_id;
	if ($_GET['from'] == 'edit') {
		include("dbConnection.php");

		$menu_id = $_COOKIE['menu_id'];
		
		$select_menu = mysqli_query($con, "SELECT menu_id,name,cost,visibility,calorie FROM menu WHERE restaurant_id=".$_SESSION['restaurant_id']." AND deleted=0 AND menu_id='$menu_id' ORDER BY visibility DESC");
		$menu = mysqli_fetch_array($select_menu);
		return $menu;
	}
}

function getContents($mealIds, $mealNames, $mealCalories, $mealTypes, $mealQuantities, $counter) {
	global $mealIds, $mealNames, $mealCalories, $mealTypes, $mealQuantities, $counter, $menu_id;
	if ($_GET['from'] == 'edit') {
		include("dbConnection.php");
		$menu_id = $_COOKIE['menu_id'];
		$select_meals = mysqli_query($con, "SELECT * FROM consists_of co, meal me WHERE co.menu_id = \"".$_COOKIE['menu_id']."\" AND co.meal_id = me.meal_id");
		while ($row_meal = mysqli_fetch_array($select_meals)) {
			$mealIds[$counter] = $row_meal['meal_id'];
			$mealNames[$counter] = $row_meal['name'];
			$mealCalories[$counter] = $row_meal['calorie'];
			$mealTypes[$counter] = $row_meal['type'];
			$mealQuantities[$counter++] = $row_meal['quantity'];
		}
		
		getContentsFromArray($mealIds, $mealNames, $mealCalories, $mealTypes, $mealQuantities, $counter);
	}
	if ($counter == 0) {
		echo "When you add a meal, it will be shown here.";
	}
}

function getContentsFromArray($mealIds, $mealNames, $mealCalories, $mealTypes, $mealQuantities, $counter) {
	global $mealIds, $mealNames, $mealCalories, $mealTypes, $mealQuantities, $counter, $menu_id;
	include("dbConnection.php");
	for ($i=0; $i < $counter; $i++) { 
		//$row_meal = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM meal WHERE meal_id='".$mealIds[$i]."'"));
		echo 	"<form method='post' style='width: 100%;'>".
					"<div id='".$mealIds[$i]."' class='list-item' style='width: 100%; display: flex; flex-direction: row; align-items: flex-start;'>".
						"<div style='display: flex; flex-direction: column; width: 70%;'>".
							"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Name: ".$mealNames[$i]."</div>".
							"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Calorie: ".$mealCalories[$i]."</div>".
							"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Type: ".$mealTypes[$i]."</div>".
							"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Quantity: ".$mealQuantities[$i]."</div>".
						"</div>".
					"</div>".
					"<div class='list-item-row' style='padding: 0px;'>".
						"<div class='list-item-col' style='width: 30%;'>".
							"<button type='button' name='edit_meal_btn' id='$mealIds[$i]_edit' class='form-btn edit' style=' width: 90%; height: 2vw; font-size: 1.2vw;'>Edit Meal</button>".
						"</div>".
						"<div class='list-item-col' style='width: 70%;'>".
							"<input name='meal_id_in' type='text' style='display: none;' value='".$mealIds[$i]."'>".
							"<button name='remove_meal_btn' class='form-btn' style='float: right; width: 90%; height: 2vw; font-size: 1.2vw;'>Remove from menu</button>".
						"</div>".
					"</div>".
				"</form>";
	}
}

function getMeals() {
	include("dbConnection.php");
	$meals = "";
	$select_meals = mysqli_query($con, "SELECT * from meal WHERE restaurant_id=\"".$_SESSION['restaurant_id']."\" AND deleted='0'");
	while ($row_meal = mysqli_fetch_array($select_meals)) {
			$meals .= "<option form='add_meal_form' value=\"".$row_meal['meal_id']."\">".$row_meal['name']." -> ".$row_meal['calorie']." cal -> ".$row_meal['type']."</option>";
	}

	echo "<div class=\"list-item\" style=\"padding: 0px; margin: 0px; background-color: transparent;\">".
		 	"<div class=\"list-item-row\" style='padding-right: 0px; align-items: center;'>".
		 		"<div class=\"list-item-col\"><label class=\"form-label\">Content: </label></div>".
		 		"<div class=\"list-item-col\">".
		 			"<select id='meal_in' name='meal_in' class='form-input'>".
		 				$meals.
		 			"</select>".
		 		"</div>".
		 	"</div>".
		 "</div>";
}

include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("o_dropdown.html"); ?>
		<div class="page" style="padding: 1%; box-sizing: border-box;">
			<div style="width: 100%; padding: 1vw; box-sizing: border-box; display: flex; flex-direction: row;">
				<div style="width: 10%">
					<button onclick="window.location.href='o_menu.php'" class="form-btn" style="width: 100%; font-size: 1.2vw;">Back</button>
				</div>
				<div style="width: 90%;">
					<h2 style="margin-top: 1vw; margin-left: 30vw; color: white;">Menu</h2>
				</div>
			</div>
			<div class="list" style="height: 85%">
				<form method="post">
					<div class="col-6">
						<h2 style="text-align: left; margin: 0px 0px 1vw 1vw;">Menu Info</h2>
						<div class="list-item" style="min-height: 30vw; margin: 1vw 0px 1vw 0px;">
							<div class="list-item-row">
								<img src="img/add_image.png" id="selected_img" style="float: left; width: 100px; height: auto;">
								<input type="file" name="picture_in" id="picture_in" accept="file/*" onchange="readURL(this);">
								<label class="form-input" style="cursor: pointer; padding: 1vw; box-sizing: border-box; border: solid white 1px; width: 100%; text-align: center;" for="picture_in" class="form-label"><i class="material-icons" style="display: inline-flex; vertical-align: middle; font-size: 2vw;">add_photo_alternate</i> Upload Picture</label>
							</div>
							<div class="list-item-row" style='align-items: center;'>
								<div class="list-item-col" style="width: 20%;"><label class="form-label">Name: </label></div>
								<div class="list-item-col" style="width: 80%;"><input name="item_name_in" class="form-input" type="text" value="<?php echo getMenuInfo()['name']; ?>"></div>
							</div>
							<div class="list-item-row" style='align-items: center;'>
								<div class="list-item-col" style="width: 20%;"><label class="form-label">Cost: </label></div>
								<div class="list-item-col" style="width: 80%;"><input name="item_cost_in" class="form-input" type="text" value="<?php echo getMenuInfo()['cost']; ?>"></div>
							</div>
							<div class="list-item-row" style='align-items: center;'>
								<div class="list-item-col" style="width: 20%;"><label class="form-label">Calorie: </label></div>
								<div class="list-item-col" style="width: 80%;"><input disabled class="form-input" type="text" value="<?php echo getMenuInfo()['calorie']; ?>"></div>
							</div>
						</div>
						<div style="margin-top: 1vw;">
							<button type="submit" name="save_btn" class="form-btn" style="float: right;">Save</button>
						</div>
					</div>
				</form>
				<form method="post">
					<div class="col-6" style="display: flex; flex-direction: column;">
						<h2 style="text-align: left; margin: 0px 0px 1vw 1vw;">Content Info</h2>
						<div class="list-item" style="padding: 0px; margin: 0px; min-height: 15vw;">
							<div class="form-group">
								<?php getMeals(); ?>
								<div style="float: right; width: 50%; display: table;">
									<input type="submit" class="form-btn" name="add_to_menu_btn" style="display: table-cell; width:50%; float: right; font-size: 1vw; height: 3vw;" value="Add to this menu">
								</div>
							</div>
							<div class="form-group">
								<button type="button" onclick="window.location.href='o_mealPage.php?from=new'" class="form-btn" style="width: 50%; float: right; font-size: 1vw; height: 3vw;">Add a new meal to the meals list</button>
							</div>
						</div>
					</div>
				</form>
				<div class="col-6" style="display: flex; flex-direction: column;">
					<div id="contents_div" class="list-item" style="margin: 1vw 0px 1vw 0px;">
						<?php getContents($mealIds, $mealNames, $mealCalories, $mealTypes, $mealQuantities, $counter); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$('.clickable').click(function () {
		var meal_id = $(this).attr('id');

		if ($('#list_meal_' + meal_id).css('display') == 'none') {
			$('#list_meal_' + meal_id).show(1000);
		}
		else {
			$('#list_meal_' + meal_id).hide(1000);
		}
	});
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
    $('.form-btn.edit').click(function () {
    	var meal_id = $(this).attr('id').split("_")[0];
    	document.cookie = "meal_id=" + meal_id;
    	window.location.href = "o_mealPage.php?from=edit";
    });
	/*function addContentClicked() {
		var id = $('#meal_in').children("option:selected").val();
		var value = $('#meal_in').children("option:selected").text();
		var values = value.split("->");

		var counter = <?php //echo json_encode($counter); ?>;
		var ids = <?php //echo json_encode($mealIds); ?>;
		var names = <?php //echo json_encode($mealNames); ?>;
		var calories = <?php //echo json_encode($mealCalories); ?>;
		var types = <?php //echo json_encode($mealTypes); ?>;

		var element = document.getElementById("contents_div");
		
		element.innerHTML += "<div id='"+values[0]+"' class='list-item' onclick='' style='width: 100%; display: flex; flex-direction: row; align-items: flex-start;'>"+
								"<div style='display: flex; flex-direction: column; width: 70%;'>"+
									"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Name: "+values[0]+"</div>"+
									"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Calorie: "+values[1]+"</div>"+
									"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Type: "+values[2]+"</div>"+
								"</div>"+
								"<div style='display: flex; flex-direction: column; width: 30%;'>"+
									"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Click for details</div>"+
								"</div>"+
							"</div>"+
							"<input class='form-input' type='text' name='added_name_"+counter+"' value='"+values[0]+"' style='width: 30%' required>"+
							"<input class='form-input' type='text' name='added_calorie_"+counter+"' value='"+values[0]+"' style='width: 30%' required>"+
							"<input class='form-input' type='text' name='added_meal_"+counter+"' value='"+values[0]+"' style='width: 30%' required>"+
							"<div id='list_meal_"+values[0]+"' style='display: none;'>"+
							 	"<div class='form-group'>"+
							 		"<input class='form-input' type='text' name='content_name_in' placeholder='"+values[0]+"' style='width: 30%' required>"+
							 		"<input class='form-input' type='text' name='content_name_in' placeholder='"+values[0]+"' style='width: 30%' required>"+
							 		"<button  class='form-input' name='remove_ingredient_btn' placeholder='"+values[0]+"' style='width: 30%' required>"+
							 	"</div>"+
							 "</div>";
	}*/
</script>
</head>
</html>