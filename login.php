<?php
	include('CheckLogin.php'); // Includes Login Script

	if(isset($_SESSION['login_user'])){
		header("location: index.php");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>GCR DashBoard</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<link rel="stylesheet" href="./css/login.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<div id="main" class="container">
	<div id="login">
		<form action="" method="post" role="form">
			<h2 class="form-group">DashBoard Login</h2>
			<div class="form-group">
				<label for="username">UserName :</label>
				<input id="name" name="username" placeholder="username" type="text" class="form-control">
			</div>
			<div class="form-group">
				<label for="password">Password :</label>
				<input id="password" name="password" placeholder="**********" type="password" class="form-control">
			</div>
			<input name="submit" type="submit" value=" Login " class="btn btn-primary">
			<span><?php echo $error; ?></span>
		</form>
	</div>
</div>
</body>
</html>