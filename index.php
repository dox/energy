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

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.1/chart.min.js" integrity="sha512-5vwN8yor2fFT9pgPS9p9R7AszYaNn0LkQElTXIsZFCL7ucT8zDCAqlQXDdaqgA1mZP47hdvztBMsIoFxq/FyyQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	
	<script src="/js/application.js"></script>

	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
	<!--<link href="css/application.css" rel="stylesheet">-->
		
	<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@0.1.1"></script>
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


<style>
.b-example-divider {
	  height: 3rem;
	  background-color: rgba(0, 0, 0, .1);
	  border: solid rgba(0, 0, 0, .15);
	  border-width: 1px 0;
	  box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
	}
	
	.bi {
	  vertical-align: -.125em;
	  fill: currentColor;
	}
	
	.feature-icon {
	  display: inline-flex;
	  align-items: center;
	  justify-content: center;
	  width: 4rem;
	  height: 4rem;
	  margin-bottom: 1rem;
	  font-size: 2rem;
	  color: #fff;
	  border-radius: .75rem;
	}
	
	.icon-link {
	  display: inline-flex;
	  align-items: center;
	}
	.icon-link > .bi {
	  margin-top: .125rem;
	  margin-left: .125rem;
	  transition: transform .25s ease-in-out;
	  fill: currentColor;
	}
	.icon-link:hover > .bi {
	  transform: translate(.25rem);
	}
	
	.icon-square {
	  display: inline-flex;
	  align-items: center;
	  justify-content: center;
	  width: 3rem;
	  height: 3rem;
	  font-size: 1.5rem;
	  border-radius: .75rem;
	}
	
	.rounded-4 { border-radius: .5rem; }
	.rounded-5 { border-radius: 1rem; }
	
	.text-shadow-1 { text-shadow: 0 .125rem .25rem rgba(0, 0, 0, .25); }
	.text-shadow-2 { text-shadow: 0 .25rem .5rem rgba(0, 0, 0, .25); }
	.text-shadow-3 { text-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .25); }
	
	.card-cover {
	  background-repeat: no-repeat;
	  background-position: center center;
	  background-size: cover;
	}
	
	
</style>

<style>
.ct-label {
	fill: rgba(0, 0, 0, .4);
	color: rgba(0, 0, 0, .4);
	font-size: .75rem;
	line-height:1
}

.ct-chart-bar .ct-label, .ct-chart-line .ct-label {
	display: block;
	display:flex
}

.ct-chart-donut .ct-label, .ct-chart-pie .ct-label {
	dominant-baseline:central
}

.ct-label.ct-horizontal.ct-start {
	align-items: flex-end;
	justify-content: flex-start;
	text-align: left;
	text-anchor:start
}

.ct-label.ct-horizontal.ct-end {
	align-items: flex-start;
	justify-content: flex-start;
	text-align: left;
	text-anchor:start
}

.ct-label.ct-vertical.ct-start {
	align-items: flex-end;
	justify-content: flex-end;
	text-align: right;
	text-anchor:end
}

.ct-label.ct-vertical.ct-end {
	align-items: flex-end;
	justify-content: flex-start;
	text-align: left;
	text-anchor:start
}

.ct-chart-bar .ct-label.ct-horizontal.ct-start {
	align-items: flex-end;
	justify-content: center;
	text-align: center;
	text-anchor:start
}

.ct-chart-bar .ct-label.ct-horizontal.ct-end {
	align-items: flex-start;
	justify-content: center;
	text-align: center;
	text-anchor:start
}

.ct-chart-bar.ct-horizontal-bars .ct-label.ct-horizontal.ct-start {
	align-items: flex-end;
	justify-content: flex-start;
	text-align: left;
	text-anchor:start
}

.ct-chart-bar.ct-horizontal-bars .ct-label.ct-horizontal.ct-end {
	align-items: flex-start;
	justify-content: flex-start;
	text-align: left;
	text-anchor:start
}

.ct-chart-bar.ct-horizontal-bars .ct-label.ct-vertical.ct-start {
	align-items: center;
	justify-content: flex-end;
	text-align: right;
	text-anchor:end
}

.ct-chart-bar.ct-horizontal-bars .ct-label.ct-vertical.ct-end {
	align-items: center;
	justify-content: flex-start;
	text-align: left;
	text-anchor:end
}

.ct-grid {
	stroke: rgba(0, 0, 0, .2);
	stroke-width: 1px;
	stroke-dasharray:2px
}

.ct-grid-background {
	fill:none
}

.ct-point {
	stroke-width: 10px;
	stroke-linecap:round
}

.ct-line {
	fill: none;
	stroke-width:4px
}

.ct-area {
	stroke: none;
	fill-opacity:.1
}

.ct-bar {
	fill: none;
	stroke-width:10px
}

.ct-slice-donut {
	fill: none;
	stroke-width:60px
}

.ct-series-a .ct-bar, .ct-series-a .ct-line, .ct-series-a .ct-point, .ct-series-a .ct-slice-donut {
	stroke:#962b40
}

.ct-series-a .ct-area, .ct-series-a .ct-slice-donut-solid, .ct-series-a .ct-slice-pie {
	fill:#262b40
}

.ct-series-b .ct-bar, .ct-series-b .ct-line, .ct-series-b .ct-point, .ct-series-b .ct-slice-donut {
	stroke:#f8bd7a
}

.ct-series-b .ct-area, .ct-series-b .ct-slice-donut-solid, .ct-series-b .ct-slice-pie {
	fill:#f8bd7a
}

.ct-series-c .ct-bar, .ct-series-c .ct-line, .ct-series-c .ct-point, .ct-series-c .ct-slice-donut {
	stroke:#2ca58d
}

.ct-series-c .ct-area, .ct-series-c .ct-slice-donut-solid, .ct-series-c .ct-slice-pie {
	fill:#2ca58d
}

.ct-series-d .ct-bar, .ct-series-d .ct-line, .ct-series-d .ct-point, .ct-series-d .ct-slice-donut {
	stroke:#31316a
}

.ct-series-d .ct-area, .ct-series-d .ct-slice-donut-solid, .ct-series-d .ct-slice-pie {
	fill:#31316a
}

.ct-series-e .ct-bar, .ct-series-e .ct-line, .ct-series-e .ct-point, .ct-series-e .ct-slice-donut {
	stroke:#c96480
}

.ct-series-e .ct-area, .ct-series-e .ct-slice-donut-solid, .ct-series-e .ct-slice-pie {
	fill:#c96480
}

.ct-series-f .ct-bar, .ct-series-f .ct-line, .ct-series-f .ct-point, .ct-series-f .ct-slice-donut {
	stroke:#fff
}

.ct-series-f .ct-area, .ct-series-f .ct-slice-donut-solid, .ct-series-f .ct-slice-pie {
	fill:#fff
}

.ct-series-g .ct-bar, .ct-series-g .ct-line, .ct-series-g .ct-point, .ct-series-g .ct-slice-donut {
	stroke:#f8bd7a
}

.ct-series-g .ct-area, .ct-series-g .ct-slice-donut-solid, .ct-series-g .ct-slice-pie {
	fill:#f8bd7a
}

.ct-square {
	display: block;
	position: relative;
	width:100%
}

.ct-square:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:100%
}

.ct-square:after {
	content: "";
	display: table;
	clear:both
}

.ct-square > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-minor-second {
	display: block;
	position: relative;
	width:100%
}

.ct-minor-second:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:93.75%
}

.ct-minor-second:after {
	content: "";
	display: table;
	clear:both
}

.ct-minor-second > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-major-second {
	display: block;
	position: relative;
	width:100%
}

.ct-major-second:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:88.88889%
}

.ct-major-second:after {
	content: "";
	display: table;
	clear:both
}

.ct-major-second > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-minor-third {
	display: block;
	position: relative;
	width:100%
}

.ct-minor-third:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:83.33333%
}

.ct-minor-third:after {
	content: "";
	display: table;
	clear:both
}

.ct-minor-third > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-major-third {
	display: block;
	position: relative;
	width:100%
}

.ct-major-third:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:80%
}

.ct-major-third:after {
	content: "";
	display: table;
	clear:both
}

.ct-major-third > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-perfect-fourth {
	display: block;
	position: relative;
	width:100%
}

.ct-perfect-fourth:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:75%
}

.ct-perfect-fourth:after {
	content: "";
	display: table;
	clear:both
}

.ct-perfect-fourth > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-perfect-fifth {
	display: block;
	position: relative;
	width:100%
}

.ct-perfect-fifth:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:66.66667%
}

.ct-perfect-fifth:after {
	content: "";
	display: table;
	clear:both
}

.ct-perfect-fifth > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-minor-sixth {
	display: block;
	position: relative;
	width:100%
}

.ct-minor-sixth:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:62.5%
}

.ct-minor-sixth:after {
	content: "";
	display: table;
	clear:both
}

.ct-minor-sixth > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-golden-section {
	display: block;
	position: relative;
	width:100%
}

.ct-golden-section:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:61.8047%
}

.ct-golden-section:after {
	content: "";
	display: table;
	clear:both
}

.ct-golden-section > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-major-sixth {
	display: block;
	position: relative;
	width:100%
}

.ct-major-sixth:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:60%
}

.ct-major-sixth:after {
	content: "";
	display: table;
	clear:both
}

.ct-major-sixth > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-minor-seventh {
	display: block;
	position: relative;
	width:100%
}

.ct-minor-seventh:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:56.25%
}

.ct-minor-seventh:after {
	content: "";
	display: table;
	clear:both
}

.ct-minor-seventh > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-major-seventh {
	display: block;
	position: relative;
	width:100%
}

.ct-major-seventh:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:53.33333%
}

.ct-major-seventh:after {
	content: "";
	display: table;
	clear:both
}

.ct-major-seventh > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-octave {
	display: block;
	position: relative;
	width:100%
}

.ct-octave:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:50%
}

.ct-octave:after {
	content: "";
	display: table;
	clear:both
}

.ct-octave > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-major-tenth {
	display: block;
	position: relative;
	width:100%
}

.ct-major-tenth:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:40%
}

.ct-major-tenth:after {
	content: "";
	display: table;
	clear:both
}

.ct-major-tenth > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-major-eleventh {
	display: block;
	position: relative;
	width:100%
}

.ct-major-eleventh:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:37.5%
}

.ct-major-eleventh:after {
	content: "";
	display: table;
	clear:both
}

.ct-major-eleventh > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-major-twelfth {
	display: block;
	position: relative;
	width:100%
}

.ct-major-twelfth:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:33.33333%
}

.ct-major-twelfth:after {
	content: "";
	display: table;
	clear:both
}

.ct-major-twelfth > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}

.ct-double-octave {
	display: block;
	position: relative;
	width:100%
}

.ct-double-octave:before {
	display: block;
	float: left;
	content: "";
	width: 0;
	height: 0;
	padding-bottom:25%
}

.ct-double-octave:after {
	content: "";
	display: table;
	clear:both
}

.ct-double-octave > svg {
	display: block;
	position: absolute;
	top: 0;
	left:0
}
</style>
