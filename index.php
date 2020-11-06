<?php
	include_once("inc/include.php");

	if (isset($_POST['inputUsername']) && isset($_POST['inputPassword'])) {
		if ($ldap_connection->auth()->attempt($_POST['inputUsername'] . LDAP_ACCOUNT_SUFFIX, $_POST['inputPassword'], $stayAuthenticated = true)) {
			// Successfully authenticated user.
			$_SESSION['logon'] = true;
			$_SESSION['username'] = strtoupper($_POST['inputUsername']);
		} else {
			// Username or password is incorrect.
			session_destroy();
		}
	}

	if (isset($_GET['logout'])) {
	  $_SESSION['logon'] = false;
	}
?>
<!doctype html>
<html lang="en">


 <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
  <meta name="generator" content="Jekyll v3.8.6">
  <title>Utility Meter Readings</title>

  <link rel="canonical" href="http://readings.seh.ox.ac.uk">

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/css/bootstrap.min.css" integrity="sha384-DhY6onE6f3zzKbjUPRc2hOzGAdEf4/Dz+WJwBvEYL/lkkIsI3ihufq9hk9K4lVoK" crossorigin="anonymous"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />

  <meta name="theme-color" content="#563d7c">

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/js/bootstrap.bundle.min.js" integrity="sha384-BOsAfwzjNJHrJ8cZidOg56tcQWfp6y72vEJ8xQ9w6Quywb24iOsW913URv1IS4GD" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js" integrity="sha256-TQq84xX6vkwR0Qs1qH5ADkP+MvH0W+9E7TdHJsoIQiM=" crossorigin="anonymous"></script>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container">
  <a class="navbar-brand" href="index.php">Utility Meter Readings</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto mb-2 mb-lg-0">
      <li class="nav-item">
        <a class="nav-link" href="index.php?n=locations" class="text-white">Locations</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php?n=audit_template" class="text-white">Usage Audit</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">Admin</a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="index.php?n=meter_add" class="text-white">Add New Meter</a></li>
					<li><a class="dropdown-item" href="index.php?n=settings" class="text-white">Site Settings</a></li>
          <li><a class="dropdown-item" href="index.php?n=logs" class="text-white">Logs</a></li>
					<?php
					if ($_SESSION['logon'] == true) {
						echo "<li><a class=\"dropdown-item\" href=\"index.php?n=logon&logout=true\" class=\"text-white\">Log Out</a></li>";
					} else {
						echo "<li><a class=\"dropdown-item\" href=\"index.php?n=logon\" class=\"text-white\">Log In</a></li>";
					}
					?>
        </ul>
      </li>
    </ul>
		<?php
		if ($_SESSION['logon'] == true) {
			echo "<button type=\"button\" class=\"btn btn-warning btn-sm float-right\">You are logged in</button>";
		}
		?>
  </div>
</div>
</nav>

<main>
	<div class="album py-5 bg-light">
		<?php
		if (isset($_GET['n'])) {
			$node = "nodes/" . $_GET['n'] . ".php";

			if (!file_exists($node)) {
				$node = "nodes/404.php";
			}
		} elseif (!isset($_GET['n'])) {
			$node = "nodes/index.php";
		} else {
			$node = "nodes/404.php";
		}

		include_once($node);
		?>
	</div>
</main>

<footer class="text-muted">
	<div class="container">
		<p class="float-right"><a href="#">Back to top</a></p>
		<p>Utility Meter Readings  is &copy;<a href="https://github.com/dox/energy">Andrew Breakspear</a>, but please download and customise it for yourself.</p>
	</div>
</footer>
</body>
</html>
