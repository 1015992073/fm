<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title><?php echo  (isset($err) && isset($err["message"])) ? $err["message"] : "404 -page not found!"  ?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" type="image/png" href="/favicon.ico" />

	<!-- STYLES -->

	<style>
		.messagebox {
			width: 80%;
			margin: 60px auto;
			font-size: 18px;
			padding: 20px;
			border: 1px solid #ccc;
			box-shadow: 2px 2px 2px #ccc;
		}
	</style>
</head>

<body>


	<diV class="messagebox">
		<?php echo (isset($err) && isset($err["message"])) ? $err["message"] : "404 -page not found!"  ?>
	</diV>


</body>

</html>