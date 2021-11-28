<!DOCTYPE html>
<html>
<?php
include("common.php");

if (isset($_POST['edit_btn'])) {
	$meal_id = $_POST['meal_id_in'];
	setcookie("meal_id", $meal_id);
	header("Location: o_mealPage.php?from=edit");
}

if (isset($_POST['remove_btn'])) {
	$meal_id = $_POST['meal_id_in'];
	
	if (!mysqli_query($con, "UPDATE meal SET deleted=1 WHERE meal_id=$meal_id")) {
		echo mysqli_error($con);
	}
}

function getMeals() {
	include("dbConnection.php");

	$select_meals = mysqli_query($con, "SELECT * FROM meal WHERE restaurant_id=".$_SESSION['restaurant_id']." AND deleted=0");

	if (mysqli_num_rows($select_meals) != 0) {
		while ($row_meal = mysqli_fetch_array($select_meals)) {
			$select_ingredient_info = mysqli_query($con, "SELECT * FROM ingredient WHERE meal_id='".$row_meal['meal_id']."'");

			$ingredients = "";
			while ($row_ingredient = mysqli_fetch_array($select_ingredient_info)) {
				$ingredients .= $row_ingredient['name'].", ";
			}
			$ingredients = substr($ingredients, 0, -2);

			if ($ingredients == "")
				$ingredients = "No content available.";

			echo "<div class=\"list-row\">".
					"<div class=\"list-item\" style='width: 80%;'>".
						"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 15%;'>Name: </div>".
				 			"<div class=\"list-item-col\" style='width: 85%;'>".$row_meal['name']."</div>".
				 		"</div>".
				 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 15%;'>Description: </div>".
				 			"<div class=\"list-item-col\" style='width: 85%;'>".$row_meal['description']."</div>".
				 		"</div>".
				 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
				 			"<div class=\"list-item-col\" style='width: 15%;'>Calorie: </div>".
				 			"<div class=\"list-item-col\" style='width: 85%;'>".$row_meal['calorie']."</div>".
				 		"</div>".
					 	"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Content: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>".$ingredients."</div>".
					 	"</div>";
			$type = "";
			if ($row_meal['type'] == 'f')
				$type = "Food";
			else
				$type = "Beverage";
			echo		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<div class=\"list-item-col\" style='width: 15%;'>Type: </div>".
					 		"<div class=\"list-item-col\" style='width: 85%;'>".$type."</div>".
					 	"</div>".
				 	"</div>".
					"<div class=\"list-item\" style='width: 20%;'>".
				 		"<div class=\"list-item-row\" style='padding: 0.3vw;'>".
					 		"<form method='post'>".
						 		"<input type='hidden' name=\"meal_id_in\" value=\"".$row_meal['meal_id']."\">".
						 		"<button name=\"edit_btn\" class=\"form-btn\" style=\"font-size: 1vw; margin:0 auto;\">Edit</button>".
						 		"<button name=\"remove_btn\" class=\"form-btn\" style=\"font-size: 1vw; margin:0 auto; margin-top: 0.5vw;\">Remove</button>".
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
			<header>Meals</header>
			<div class="list" style="height: 85%">
				<?php getMeals(); ?>
			</div>
			<div style="margin-top: 1vw;">
				<button type="button" onclick="window.location.href='o_mealPage.php?from=new'" class="form-btn" style="float: right;">Add New Meal</button>
			</div>
		</div>
	</div>
</body>
</head>
</html>