<!DOCTYPE html>
<html>
<?php
include("common.php");

function getRestaurants() {
	include("dbConnection.php");

	if (!isset($_SESSION['region_id'])) {
		echo "<h2>Currently, you did not choose a region!<h2>";
	}
	else {
		$select_restaurants = mysqli_query($con, "SELECT * FROM restaurant WHERE region_id='".$_SESSION['region_id']."'");

		if (mysqli_num_rows($select_restaurants) != 0) {
			while ($row_restaurant = mysqli_fetch_array($select_restaurants)) {
				echo "<div class=\"list-item clickable\" id=\"".$row_restaurant['restaurant_id']."\">".
					 	"<div class=\"list-item-row\">".
					 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Restaurant Name: </div>".
					 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$row_restaurant['name']."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\">".
					 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Cuisine Type: </div>".
					 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$row_restaurant['cuisine_type']."</div>".
					 	"</div>".
					 	"<div class=\"list-item-row\">".
					 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Rating: </div>".
					 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$row_restaurant['rating']."</div>".
					 	"</div>".
					 "</div>";
			}
		}
	}
}

include("head.html");
?>
<?php
$header = "No region chosen!";
if (isset($_SESSION['region_id'])) {
	$region = mysqli_fetch_array(mysqli_query($con, "SELECT * from region WHERE region_id='".$_SESSION['region_id']."'"));
	$header = "Restaurants in ".$region['name'];
}
?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("c_dropdown.html"); ?>
		<div class="page" style="padding: 1%; box-sizing: border-box;">
			<header><?php echo $header; ?></header>
			<div class="list">
				<?php getRestaurants(); ?>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(".clickable").click(function () {
		var restaurant_id = $(this).attr("id");
		//console.log(restaurant_id);
		document.cookie= "restaurant_id="+restaurant_id;
		window.location.href = "c_restaurantPage.php";
	});
</script>
</head>
</html>