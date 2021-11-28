<?php
$host = "ayberkyilmaz.com";
$username = "xpvbnv2po0n6";
$password = "4zP,TtDp";
$dbName = "food_ordering";
/*$host = "localhost:3306";
$username = "root";
$password = "";
$dbName = "food_ordering";*/
$con = mysqli_connect( $host, $username, $password, $dbName);
if (!$con) {
	echo "Connection failed: \n" + mysqli_connect_error();
  	exit();
}
?>