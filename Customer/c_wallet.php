<!DOCTYPE html>
<html>

<?php
include("common.php");
include("head.html");

$customer = mysqli_fetch_array(mysqli_query($con, "SELECT wallet FROM customer WHERE user_id='".$_SESSION['user_id']."'"));
$_SESSION['wallet'] = $customer['wallet'];

if (isset($_POST['save_btn'])) {
	if ($_POST['action'] == 'wd') {
		if ($_POST['amount_in'] <= $_SESSION['wallet']) {
			mysqli_query($con, "UPDATE customer SET wallet=wallet-".$_POST['amount_in']." WHERE user_id='".$_SESSION['user_id']."'");
			$_SESSION['wallet'] = 0;
			header("Location: c_wallet.php?wd=done");
		}
		else {
			header("Location: c_wallet.php?wd=no");
		}
	}
	if ($_POST['action'] == 'dp') {
		mysqli_query($con, "UPDATE customer SET wallet=wallet+".$_POST['amount_in']." WHERE user_id='".$_SESSION['user_id']."'");
		$customer = mysqli_fetch_array(mysqli_query($con, "SELECT wallet FROM customer WHERE user_id='".$_SESSION['user_id']."'"));
		$_SESSION['wallet'] = $customer['wallet'];
		header("Location: c_wallet.php?dp=done");
	}
}

?>
<body style="background-color: #786ca4;">
	<div class="container">
		<?php include("c_dropdown.html"); ?>
		<div class="page">
			<form id="form" class="login-form" method="post">
				<div class="row" style="padding-top: 2%; padding-right: 10vw; justify-content: center; box-sizing: border-box;">
					<div class="col" style="width: 60%; justify-content: center;">
						<header>Wallet</header>
						<br>
						<div class="list" style="background-color: rgba(0,0,0,0.3); overflow: hidden;">
							<div id="info_row">
								<h2 style="text-align: center; color: white;">Current Amount:  $<?php echo $_SESSION['wallet'];?></h2>
							</div>
							<div class="list-item" style="background-color: transparent;">
								<div class="list-item-row" style='justify-content: center;'>
									<input type="radio" id="wd" name="action" value="wd" checked="">
									<label for="wd">Withdraw</label><br>
									<input type="radio" id="dp" name="action" value="dp">
									<label for="dp">Deposit</label><br>
								</div>
							 	<div class="list-item-row" style='align-items: center;'>
									<div class="list-item-col" style="width: 50%;"><label class="form-label">Name: </label></div>
									<div class="list-item-col\" style="width: 100%;"><input name="name_in" class="form-input" type="text" value="<?php echo $_SESSION['name']; ?>"></div>
								</div>
								<div class="list-item-row" style='align-items: center;'>
									<div class="list-item-col" style="width: 50%;"><label class="form-label">Bank: </label></div>
									<div class="list-item-col\" style="width: 100%;"><input name="bank_in" class="form-input" type="text"></div>
								</div>
								<div class="list-item-row" style='align-items: center;'>
									<div class="list-item-col" style="width: 50%;"><label class="form-label">Bank Account Number: </label></div>
									<div class="list-item-col\" style="width: 100%;"><input name="account_in" class="form-input" type="text"></div>
								</div>
								<div class="list-item-row" style='align-items: center;'>
									<div class="list-item-col" style="width: 50%;"><label class="form-label">Account Owner: </label></div>
									<div class="list-item-col\" style="width: 100%;"><input name="account_owner_in" class="form-input" type="text"></div>
								</div>
								<div class="list-item-row" style='align-items: center;'>
									<div class="list-item-col" style="width: 50%;"><label class="form-label">Amount: </label></div>
									<div class="list-item-col\" style="width: 100%;"><input name="amount_in" class="form-input" type="number"></div>
								</div>
							 </div>
						</div>
						<div class="row" style="justify-content: center; margin-top: 2vw;">
							<button class="form-btn" name="save_btn" style="width: 50%;"><i class="material-icons" style="font-size: 1.7vw; transform: translateY(10%);">save</i> Save</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
<?php 
if (isset($_GET['wd'])) {
	if ($_GET['wd'] == 'done') {
		echo "<script>document.getElementById('info_row').innerHTML += '<h2 style=\'color: white; text-align: center;\'>Done!</h2>';</script>";
	}
	else if ($_GET['wd'] == 'no') {
		echo "<script>document.getElementById('info_row').innerHTML += '<h2 style=\'color: white; text-align: center;\'>Cannot withdraw more than your wallet!</h2>';</script>";
	}
}
if (isset($_GET['dp'])) {
	echo "<script>document.getElementById('info_row').innerHTML += '<h2 style=\'color: white; text-align: center;\'>Done!</h2>';</script>";
}
?>
</head>
</html>