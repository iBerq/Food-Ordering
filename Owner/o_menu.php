<!DOCTYPE html>
<html>
<?php
include("common.php");

if (isset($_POST['edit_btn'])) {
	$menu_id = $_POST['menu_id_in'];
	setcookie("menu_id", $menu_id);
	header("Location: o_menuPage.php?from=edit");
}

if (isset($_POST['remove_btn'])) {
	$menu_id = $_POST['menu_id_in'];
	
	if (!mysqli_query($con, "UPDATE menu SET deleted=1 WHERE menu_id=$menu_id")) {
		echo mysqli_error($con);
	}
}

if (isset($_POST['show_hide_btn'])) {
	$menu_id = $_POST['menu_id_in'];

	if (!mysqli_query($con, "UPDATE menu SET visibility=IF (visibility=1, 0, 1) WHERE menu_id=$menu_id")) {
		echo mysqli_error($con);
	}
}

function getMenu() {
	include("dbConnection.php");

	$select_menus = mysqli_query($con, "SELECT menu_id,name,cost,visibility FROM menu WHERE restaurant_id=".$_SESSION['restaurant_id']." AND deleted=0 ORDER BY visibility DESC, name");

	if (mysqli_num_rows($select_menus) != 0) {
		while ($row_menu = mysqli_fetch_array($select_menus)) {
			$select_meal_info = mysqli_query($con, "SELECT me.name, me.meal_id FROM consists_of co, meal me WHERE co.menu_id = \"".$row_menu['menu_id']."\" AND co.meal_id = me.meal_id");
				
			$meals = "";
			while ($row_meal_info = mysqli_fetch_array($select_meal_info)) {
				$meals .= $row_meal_info['name'];
				$select_ingredient_info = mysqli_query($con, "SELECT i.name FROM ingredient i WHERE i.meal_id = \"".$row_meal_info['meal_id']."\"");
				
				$ingredients = "";
				while ($row_ingredient_info = mysqli_fetch_array($select_ingredient_info)) {
						$ingredients .= $row_ingredient_info['name'] . ", ";
				}
				$ingredients = substr($ingredients, 0, -2);
				$meals .= " (".$ingredients.") - ";
			}
			$meals = substr($meals, 0, -2);

			echo "<div class=\"list-row\">";
			if ($row_menu['visibility'] == 1) {
				$btnValue = "hide";
				echo "<div class=\"list-item\">";
			}
			elseif ($row_menu['visibility'] == 0) {
				$btnValue = "show";
				echo "<div class='col-2' style='width: 100%; height: 10%; writing-mode: tb-rl; transform: rotate(-180deg);'>HIDDEN</div>".
					 "<div class=\"list-item hidden\" style='width: 80%;'>";
			}
			echo	 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 15%;'>Menu Name: </div>".
				 			"<div class=\"list-item-col\" style='width: 85%;'>".$row_menu['name']."</div>".
				 		"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Content: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>".$meals."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Total Price: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>".$row_menu['cost']."</div>".
					 	"</div>".
				 	"</div>".
					"<div class=\"list-item\" style='width: 20%;'>".
				 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<form method='post'>".
						 		"<input type='hidden' name=\"menu_id_in\" value=\"".$row_menu['menu_id']."\">".
						 		"<button name=\"edit_btn\" class=\"form-btn\" style=\"font-size: 1vw; margin:0 auto;\">Edit</button>".
						 		"<button name=\"remove_btn\" class=\"form-btn\" style=\"font-size: 1vw; margin:0 auto; margin-top: 0.5vw;\">Remove</button>".
						 		"<button name=\"show_hide_btn\" class=\"form-btn\" style=\"font-size: 1vw; margin:0 auto; margin-top: 0.5vw;\">".ucfirst($btnValue)."</button>".
						 	"</form>".
					 	"</div>".
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
			<header>Menus</header>
			<div class="list" style="height: 85%">
				<?php getMenu(); ?>
			</div>
			<div style="margin-top: 1vw;">
				<a href="o_menuPage.php?from=new"><button class="form-btn" style="float: right;">Add New Menu</button></a>
			</div>
		</div>
	</div>
</body>
</head>
</html>