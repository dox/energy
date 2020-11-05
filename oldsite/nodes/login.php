<style>
.form-signin {
	max-width: 330px;
	padding: 15px;
	margin: 0 auto;
}

.form-signin .form-signin-heading,
.form-signin .checkbox {
	margin-bottom: 10px;
}

.form-signin .checkbox {
	font-weight: normal;
}

.form-signin .form-control {
	position: relative;
	height: auto;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	padding: 10px;
	font-size: 16px;
}

.form-signin .form-control:focus {
	z-index: 2;
}

.form-signin input[type="text"] {
	margin-bottom: -1px;
	border-bottom-right-radius: 0;
	border-bottom-left-radius: 0;
}

.form-signin input[type="password"] {
	margin-bottom: 10px;
	border-top-left-radius: 0;
	border-top-right-radius: 0;
}
</style>

<?php
	$message = "";
	if (isset($_GET['logout'])) {
	if ($_GET['logout'] == "true") { //destroy the session
		//$logSQLInsert = Array ("type" => "LOGOFF", "description" => $_SESSION["username"] . " logged out");
		//$id = $db->insert ('_logs', $logSQLInsert);
		
		$_SESSION = array();
		session_destroy();
		
		$message = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Success!</strong> You have been logged out.</div>";
	}
}
?>
<div class="container">
		<?php echo $message; ?>
	<form class="form-signin" id="loginForm" method="post" role="form">
		<h2 class="form-signin-heading">Logon Required</h2>
		
		<input type="text" class="form-control" placeholder="Username" id="username" name="username" value="<?php if (isset($_POST['username'])) { echo $username; } ?>" required autofocus>
		<input type="password" class="form-control" placeholder="Password" name="password" id="password" required>
		<button type="submit" name="submit" value="submit" class="btn btn-block btn-large btn-primary" >Sign in</button>
		<input type='hidden' name='oldform' value='1'>
	</form>
</div>

