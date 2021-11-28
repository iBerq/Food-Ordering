<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: index.php?signin=false");
	exit;
}

if (isset($_POST['sign_out_btn'])) {
	session_destroy();
	header("Location: index.php");
	exit;
}

include_once("dbConnection.php");
?>