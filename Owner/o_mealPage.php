<!DOCTYPE html>
<html>
<?php
include("common.php");

$meal_id = "";
$ingredientIds = array();
$ingredientNames = array();
$counter = 0;

if ($_GET['from'] == "new") {
	setcookie("meal_id", "-1");
}

if (isset($_POST['remove_ingredient_btn'])) {
	$ingredient_name = $_POST['ingredient_name_in2'];
	$meal_id = $_COOKIE['meal_id'];
	$meal = mysqli_fetch_array(mysqli_query($con, "SELECT * from meal WHERE meal_id='$meal_id'"));
	echo mysqli_error($con);
	$name = $meal['name'];
	$description = $meal['description'];
	$calorie = $meal['calorie'];
	$type = $meal['type'];

	mysqli_query($con, "INSERT INTO meal (restaurant_id, name, description, calorie, type) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$description', '$calorie', '$type')");
	echo mysqli_error($con);
	$meal_id = $con->insert_id;
	mysqli_query($con, "UPDATE meal SET deleted='1' WHERE meal_id='".$_COOKIE['meal_id']."'");
	echo mysqli_error($con);

	$select_ingredients = mysqli_query($con, "SELECT * FROM ingredient WHERE meal_id='".$_COOKIE['meal_id']."'");
	while ($row_ingredient = mysqli_fetch_array($select_ingredients)) {
		if ($row_ingredient['name'] != $ingredient_name) {
			mysqli_query($con, "INSERT INTO ingredient (meal_id, name) VALUES ('".$meal_id."', '".$row_ingredient['name']."')");
			echo mysqli_error($con);
		}
	}

	setcookie("meal_id", $meal_id);
	header("Location: o_mealPage.php?from=edit");
}

if (isset($_POST['save_btn'])) {
	$name = $_POST['meal_name_in'];
	$calorie = $_POST['meal_calorie_in'];
	$type = $_POST['meal_type_in'];
	$description = $_POST['meal_description_in'];

	if ($_GET['from'] == 'edit') {
		$select_ingredients = mysqli_query($con, "SELECT * FROM ingredient WHERE meal_id='".$_COOKIE['meal_id']."'");
		if (mysqli_num_rows($select_ingredients) == 0) {
			mysqli_query($con, "UPDATE meal SET name='$name', description='$description', type='$type', calorie='$calorie' WHERE meal_id='".$_COOKIE['meal_id']."'");
		}
		else {
			mysqli_query($con, "INSERT INTO meal (restaurant_id, name, description, calorie, type, deleted) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$description', '$calorie', '$type', '0')");
			$meal_id = $con->insert_id;
			mysqli_query($con, "UPDATE meal SET deleted='1' WHERE meal_id='".$_COOKIE['meal_id']."'");

			while ($row_ingredient = mysqli_fetch_array($select_ingredients)) {
				mysqli_query($con, "INSERT INTO ingredient (meal_id, name) VALUES ('".$meal_id."', '".$row_ingredient['name']."')");
				mysqli_query($con, "UPDATE ingredient SET deleted='1' WHERE meal_id='".$_COOKIE['meal_id']."' AND name='".$row_ingredient['name']."'");
			}
		}
	}
	else {
		mysqli_query($con, "INSERT INTO meal (restaurant_id, name, description, calorie, type, deleted) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$description', '$calorie', '$type', '0')");
		$meal_id = $con->insert_id;
	}
	setcookie("meal_id", $meal_id);
	header("Location: o_mealPage.php?from=edit");
}

if (isset($_POST['add_to_meal_btn'])) {
	$ingredient_name = $_POST['ingredient_name_in'];
	$meal = mysqli_fetch_array(mysqli_query($con, "SELECT * from meal WHERE meal_id='".$_COOKIE['meal_id']."'"));
	$name = $meal['name'];
	$description = $meal['description'];
	$calorie = $meal['calorie'];
	$type = $meal['type'];

	if ($_COOKIE['meal_id'] == '-1') {
		echo "<script type='text/javascript'>alert('Cannot add meal before creating a meal! First create a meal!');</script>";
	}
	else {
		$select_ingredients = mysqli_query($con, "SELECT * FROM ingredient WHERE meal_id='".$_COOKIE['meal_id']."'");

		$isThere = false;
		while ($row_ingredient = mysqli_fetch_array($select_ingredients)) {
			if ($row_ingredient['name'] == $ingredient_name) {
				$isThere = true;
			}
		}
		
		if ($isThere) {
			echo "<script type='text/javascript'>alert('This meal has an ingredient with name ".$ingredient_name.".');</script>";
		}
		else {
			mysqli_query($con, "INSERT INTO meal (restaurant_id, name, description, calorie, type) VALUES ('".$_SESSION['restaurant_id']."', '$name', '$description', '$calorie', '$type')");
			echo mysqli_error($con);
			$meal_id = $con->insert_id;
			mysqli_query($con, "UPDATE meal SET deleted='1' WHERE meal_id='".$_COOKIE['meal_id']."'");

			$select_ingredients = mysqli_query($con, "SELECT * FROM ingredient WHERE meal_id='".$_COOKIE['meal_id']."'");
			while ($row_ingredient = mysqli_fetch_array($select_ingredients)) {
				mysqli_query($con, "INSERT INTO ingredient (meal_id, name) VALUES ('".$meal_id."', '".$row_ingredient['name']."')");
			}
			mysqli_query($con, "INSERT INTO ingredient (meal_id, name) VALUES ('".$meal_id."', '".$ingredient_name."')");
			
			setcookie("meal_id", $meal_id);
			header("Location: o_mealPage.php?from=edit");
		}
	}
}

function getMealInfo() {
	global $meal_id;
	if ($_GET['from'] == 'edit') {
		include("dbConnection.php");

		$meal_id = $_COOKIE['meal_id'];
		
		$select_meal = mysqli_query($con, "SELECT * FROM meal WHERE meal_id='".$meal_id."'");
		$meal = mysqli_fetch_array($select_meal);
		return $meal;
	}
}

function getContents() {
	global $counter, $meal_id, $ingredientIds, $ingredientNames;
	if ($_GET['from'] == 'edit') {
		include("dbConnection.php");
		$meal_id = $_COOKIE['meal_id'];
		$select_ingredients = mysqli_query($con, "SELECT * FROM ingredient WHERE meal_id=\"".$_COOKIE['meal_id']."\"");
		while ($row_ingredient = mysqli_fetch_array($select_ingredients)) {
			$ingredientIds[$counter] = $row_ingredient['meal_id'];
			$ingredientNames[$counter++] = $row_ingredient['name'];
		}
		
		getContentsFromArray();
	}
	if ($counter == 0) {
		echo "When you add a meal, it will be shown here.";
	}
}

function getContentsFromArray() {
	global $counter, $meal_id, $ingredientIds, $ingredientNames;
	include("dbConnection.php");
	for ($i=0; $i < $counter; $i++) { 
		//$row_meal = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM meal WHERE meal_id='".$mealIds[$i]."'"));
		echo 	"<form method='post' style='width: 100%;'>".
					"<div id='".$ingredientIds[$i]."' class='list-item' style='width: 100%; display: flex; flex-direction: row; align-items: flex-start;'>".
						"<div style='display: flex; flex-direction: column; width: 70%;'>".
							"<div class='list-item-row' style='padding: 0.3vw 0.5vw 0.3vw 0.5vw;'>Name: ".$ingredientNames[$i]."</div>".
						"</div>".
					"</div>".
					"<div class='list-item-row' style='padding: 0px;'>".
						"<div class='list-item-col' style='width: 30%;'>".
							"<button type='button' name='edit_meal_btn' id='".$ingredientIds[$i]."_edit' class='form-btn edit' style=' width: 90%; height: 2vw; font-size: 1.2vw;'>Edit Meal</button>".
						"</div>".
						"<div class='list-item-col' style='width: 70%;'>".
							"<input name='ingredient_name_in2' type='text' style='display: none;' value='".$ingredientNames[$i]."'>".
							"<button name='remove_ingredient_btn' class='form-btn' style='float: right; width: 90%; height: 2vw; font-size: 1.2vw;'>Remove from meal</button>".
						"</div>".
					"</div>".
				"</form>";
	}
}

include("head.html");
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("o_dropdown.html"); ?>
		<div class="page" style="padding: 1%; box-sizing: border-box;">
			<div style="width: 100%; padding: 1vw; box-sizing: border-box; display: flex; flex-direction: row;">
				<div style="width: 10%">
					<button onclick="window.location.href='o_meal.php'" class="form-btn" style="width: 100%; font-size: 1.2vw;">Back</button>
				</div>
				<div style="width: 90%;">
					<h2 style="margin-top: 1vw; margin-left: 30vw; color: white;">Meal</h2>
				</div>
			</div>
			<div class="list" style="height: 85%">
				<div class="col-6">
					<form method="post">
						<h2 style="text-align: left; margin: 0px 0px 1vw 1vw;">Meal Info</h2>
						<div class="list-item" style="margin: 1vw 0px 1vw 0px;">
								<div class="list-item-row" style='align-items: center;'>
								<div class="list-item-col" style="width: 20%;"><label class="form-label">Name: </label></div>
								<div class="list-item-col" style="width: 80%;"><input name="meal_name_in" class="form-input" type="text" value="<?php echo getMealInfo()['name']; ?>"></div>
							</div>
							<div class="list-item-row" style='align-items: center;'>
								<div class="list-item-col" style="width: 20%;"><label class="form-label">Description: </label></div>
								<div class="list-item-col" style="width: 80%;"><input name="meal_description_in" class="form-input" type="text" value="<?php echo getMealInfo()['description']; ?>"></div>
							</div>
							<div class="list-item-row" style='align-items: center;'>
								<div class="list-item-col" style="width: 20%;"><label class="form-label">Type: </label></div>
								<div class="list-item-col" style="width: 80%;">
									<select class="form-input" name="meal_type_in" style="margin: 0px;">
										<?php
										if (mysqli_fetch_array(mysqli_query($con, "SELECT * FROM meal WHERE meal_id='".$_COOKIE['meal_id']."'"))['type'] == 'f') {
											echo "<option value='f' selected>Food</option>".
											     "<option value='b'>Beverage</option>";
										}
										else {
											echo "<option value='f'>Food</option>".
											     "<option value='b' selected>Beverage</option>";
										}
										?>
									</select>
								</div>
							</div>
							<div class="list-item-row" style='align-items: center;'>
								<div class="list-item-col" style="width: 20%;"><label class="form-label">Calorie: </label></div>
								<div class="list-item-col" style="width: 80%;"><input name="meal_calorie_in" class="form-input" type="text" value="<?php echo getMealInfo()['calorie']; ?>"></div>
							</div>
							<div class="list-item-row" style="justify-content: flex-end;">
								<button type="submit" name="save_btn" class="form-btn" style="width: 30%; height: 3vw; float: right;">Save</button>
							</div>
						</div>
					</form>
					<form method="post">
						<h2 style="text-align: left; margin: 0px 0px 1vw 1vw;">Ingredient Info</h2>
						<div class="list-item" style="padding: 0px; margin: 0px;">
							<div class="form-group">
								<div class="list-item" style="padding: 0px; margin: 0px; background-color: transparent;">
								 	<div class="list-item-row" style='align-items: center;'>
								 		<div class="list-item-col" style="width: 20%;"><label class="form-label">Content Name: </label></div>
								 		<div class="list-item-col" style="width: 80%;"><input type='text' class='form-input' name='ingredient_name_in' id='ingredient_name_in'></div>
								 	</div>
								</div>
								<div style="float: right; width: 50%;">
									<input type="submit" class="form-btn" name="add_to_meal_btn" style="width:50%; float: right; font-size: 1vw; height: 3vw;" value="Add to this meal">
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="col-6" style="display: flex; flex-direction: column;">
					<form method="post">
						<h2 style="text-align: left; margin: 0px 0px 1vw 1vw;">Contents</h2>
						<div id="contents_div" class="list-item" style="margin: 1vw 0px 1vw 0px;">
							<?php getContents(); ?>
						</div>
					</form>
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
    /*function selectChanged() {
    	console.log("anan");
    	var selectValue = $('#ingredient_in :selected').val();
    	console.log(selectValue);
    	$('#ingredient_name_in').val(selectValue);
    }*/
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