<?php
	include_once("inc/include.php");

	if (isset($_POST['inputUsername']) && isset($_POST['inputPassword'])) {
		if ($ldap_connection->auth()->attempt($_POST['inputUsername'] . LDAP_ACCOUNT_SUFFIX, $_POST['inputPassword'], $stayAuthenticated = true)) {
			// Successfully authenticated user.
			$_SESSION['logon'] = true;
			$_SESSION['username'] = strtoupper($_POST['inputUsername']);

			$logArray['category'] = "logon";
			$logArray['type'] = "success";
			$logArray['value'] = $_SESSION['username'] . " logged on successfully";
			$logsClass->create($logArray);

		} else {
			// Username or password is incorrect.
			$logArray['category'] = "logon";
			$logArray['type'] = "warning";
			$logArray['value'] = $_POST['inputUsername'] . " failed to log on.  Username or password incorrect";
			$logsClass->create($logArray);

			session_destroy();
		}
	}

	if (isset($_GET['logout'])) {
		$logArray['category'] = "logon";
		$logArray['type'] = "success";
		$logArray['value'] = $_SESSION['username'] . " logged off successfully";
		$logsClass->create($logArray);

		session_destroy();
	  $_SESSION['logon'] = false;
	}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Andrew Breakspear">
  <meta name="generator" content="Github Atom">
  <title>Utility Meter Readings</title>

  <link rel="canonical" href="http://readings.seh.ox.ac.uk">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />

  <meta name="theme-color" content="#563d7c">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js" integrity="sha256-TQq84xX6vkwR0Qs1qH5ADkP+MvH0W+9E7TdHJsoIQiM=" crossorigin="anonymous"></script>
</head>

<body>
<?php include_once("views/header.php"); ?>
<main>
	<div class="py-5">
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

		echo "<div class=\"container\">";
		include_once($node);
		echo "</div>";
		?>
	</div>
</main>

<?php include_once("views/footer.php"); ?>
</body>
</html>
