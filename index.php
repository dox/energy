<?php include_once("inc/include.php"); ?>

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

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
	<link href="css/application.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
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