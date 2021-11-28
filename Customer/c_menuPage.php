<!DOCTYPE html>
<html>
<?php
include("common.php");

$select_menu = mysqli_query($con, "SELECT * FROM menu WHERE menu_id=\"".$_COOKIE['menu_id']."\"");
$menu = mysqli_fetch_array($select_menu);

$ingredientCount = 0;

if (isset($_POST['add_basket_btn'])) {
	$cookie = $_COOKIE['basket'];
	$cookie = stripslashes($cookie);
	$basketOld = json_decode($cookie, true);

	$countKey = 0;
	foreach ($basketOld as $mkey => $mvalue) {
		for ($i = 0; $i < count($basketOld); $i++) { 
			if ($mkey == "menu_".$menu['menu_id']."_".$i) {
				$countKey++;
			}
		}
	}

	$count = 0;
	for ($i = 0; $i < $_POST['ingredient_count_in']; $i++) {
		$inputName = "ingredient_chosen_in_".$i;
		if ($_POST[$inputName] != "") {
			$basketOld['menu_'.$menu['menu_id']."_".$countKey]['ingredient_'.$count++] = $_POST[$inputName];
		}
	}
	if ($count == 0) {
		$basketOld['menu_'.$menu['menu_id']."_".$countKey]['ingredient_'.$count++] = "";
	}
	setcookie("basket_cost", $_COOKIE['basket_cost']+$menu['cost']);

	$basketNew = json_encode($basketOld);
	setcookie('basket', $basketNew);
	header("Refresh:0");
}

/*if (isset($_POST['add_basket_btn'])) {

	$cookie = $_COOKIE['basket'];
	$cookie = stripslashes($cookie);
	$basketOld = json_decode($cookie, true);

	for ($i = 0; $i < $ingredientCount; $i++) {
		$inputName = "ingredient_chosen_in_".$i;
		if ($_POST[$inputName] != "") {
			$basketOld['menu_'.$menu['menu_id']] = array();
			$basketOld['menu_'.$menu['menu_id']]['ingredient_'.$i] = $_POST[$inputName];
		}
	}

	print_r($basketOld);

	$basketNew = json_encode($basketOld);
	setcookie('basket', $basketNew);
}*/

function getMenuInfo() {
	global $menu, $ingredientCount;
	include("dbConnection.php");

	$select_meals = mysqli_query($con, "SELECT me.name, me.meal_id FROM menu m, consists_of co, meal me WHERE m.menu_id = \"".$menu['menu_id']."\" AND m.menu_id = co.menu_id AND co.meal_id = me.meal_id");

	$str = "";
	while ($row_meal = mysqli_fetch_array($select_meals)) {
		echo "<div class='row'>".
				"<h2 style='font-size: 1vw;'>".$row_meal['name']."</h2>".
			 "</div>";

		$select_ingredients = mysqli_query($con, "SELECT name FROM ingredient WHERE meal_id='".$row_meal['meal_id']."' AND deleted='0'");
		while ($row_ingredient = mysqli_fetch_array($select_ingredients)) {
			echo "<div class='row'>".
					"<input type='checkbox' checked class='checkbox' id='".$row_ingredient['name']."' name='".$row_ingredient['name']."' style='width: 5vw;'>".
					"<input type='hidden' value='".$row_ingredient['name']."' id='ingredient_chosen_in_".$row_ingredient['name']."' name='ingredient_chosen_in_".$ingredientCount."'>".
				 	"<h3 style='font-size: 0.8vw;'>".$row_ingredient['name']."</h3>".
				 "</div>";
			$ingredientCount++;
		}
	}
}

include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("c_dropdown.html"); ?>
		<div class="page" style="padding: 1%; box-sizing: border-box;">
			<div class="row">
				<button onclick="window.location.href='c_restaurantPage.php'" class="form-btn" style="height: 2vw; width: 10vw; font-size: 1.2vw;">Back</button>
			</div>
			<header><?php echo $menu['name']; ?></header>
			<form method="post" style="height: 85%;">
				<div class="list">
					<div id="contents" class="list-item" style="margin: 1vw; padding: 1vw;">
						<div class="list-item-col" style="width: 100%; display: flex; flex-direction: column;">
							<?php getMenuInfo(); ?>
						</div>
					</div>
				</div>
				<div class="row" style="width: 50%; float: right;">
					<div class="row" style="width: 50%; justify-content: flex-end;">
						<h2 style="color: white;">Total Price: $<?php echo $menu['cost']; ?></h2>
					</div>
					<div class="col" style="width: 50%; justify-content: center;">
						<div class="row" style="justify-content: flex-end;">
							<input type="hidden" name="ingredient_count_in" id="ingredient_count_in" value="<?php echo $ingredientCount; ?>">
							<button type="submit" name="add_basket_btn" class="form-btn" style="width: 70%; height: 3vw; font-size: 1.5vw;">Add to Basket</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(".clickable").click(function () {
		var menu_id = $(this).attr("id");
		document.cookie= "menu_id="+menu_id;
		window.location.href = "c_menuPage.php";
	});

	$(".checkbox").change( function() {
		var checkBox = document.getElementById($(this).attr('id'));
		var ingredientCountInput = document.getElementById("ingredient_count_in");

		if (checkBox.checked == true){
			document.getElementById("ingredient_chosen_in_" + $(this).attr('id')).value = $(this).attr('id');
			//ingredientCountInput.value++;
		}
		else {
		    document.getElementById("ingredient_chosen_in_" + $(this).attr('id')).value = "";
		    //ingredientCountInput.value--;
		}
	});
</script>
</head>
</html>