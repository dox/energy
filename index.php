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
	
	unset($_SESSION);
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
	<meta name="generator" content="Panic Nova">
	<title><?php echo site_name; ?></title>

	<link rel="apple-touch-icon" sizes="57x57" href="/inc/favicons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/inc/favicons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/inc/favicons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/inc/favicons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/inc/favicons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/inc/favicons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/inc/favicons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/inc/favicons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/inc/favicons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="/inc/favicons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/inc/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="/inc/favicons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/inc/favicons/favicon-16x16.png">
	<link rel="manifest" href="/inc/favicons/manifest.json">
	<meta name="msapplication-TileColor" content="#212529">
	<meta name="msapplication-TileImage" content="/inc/favicons/ms-icon-144x144.png">
	<meta name="theme-color" content="#212529">
	
	<link rel="canonical" href="http://readings.seh.ox.ac.uk">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
	<link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
	<link href="css/application.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
	<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
	<script src="/js/application.js"></script>
</head>

<body>
	<?php
	include_once("views/header.php");
	
	if (isset($_SESSION['last_node_access'])) {
		$node = "nodes/" . $_SESSION['last_node_access'] . ".php";
		unset($_SESSION['last_node_access']);
	} elseif (isset($_GET['n'])) {
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
	
	include_once("views/footer.php");
	?>
</body>
</html>