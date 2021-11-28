<!DOCTYPE html>
<html>
<?php
include("common.php");

$select_restaurant = mysqli_query($con, "SELECT * FROM restaurant WHERE restaurant_id=\"".$_COOKIE['restaurant_id']."\"");
$restaurant = mysqli_fetch_array($select_restaurant);

function getMenus() {
	global $restaurant;
	include("dbConnection.php");

	$select_menus = mysqli_query($con, "SELECT * FROM menu WHERE restaurant_id='".$restaurant['restaurant_id']."' AND visibility='1' AND deleted='0'");

	if (mysqli_num_rows($select_menus) != 0) {
		while ($row_menu = mysqli_fetch_array($select_menus)) {
			echo "<div class=\"list-item clickable\" id=\"".$row_menu['menu_id']."\">".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Name: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$row_menu['name']."</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Price: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$row_menu['cost']."</div>".
				 	"</div>".
				 	"<div class=\"list-item-row\">".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>Calorie: </div>".
				 		"<div class=\"list-item-col\" style='font-size: 0.8vw;'>".$row_menu['calorie']."</div>".
				 	"</div>".
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
			<div class="row">
				<button onclick="window.location.href='c_homePage.php'" class="form-btn" style="height: 2vw; width: 10vw; font-size: 1.2vw;">Back</button>
			</div>
			<header><?php echo $restaurant['name']; ?></header>
			<div class="list">
				<?php getMenus(); ?>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	$(".clickable").click(function () {
		var menu_id = $(this).attr("id");
		document.cookie= "menu_id="+menu_id;
		window.location.href = "c_menuPage.php";
	});
</script>
</head>
</html>