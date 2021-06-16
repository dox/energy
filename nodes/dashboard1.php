<?php
include_once("../inc/include.php");
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Andrew Breakspear">
  <meta name="generator" content="Github Atom">
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

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.2.1/chart.min.js" integrity="sha512-tOcHADT+YGCQqH7YO99uJdko6L8Qk5oudLN6sCeI4BQnpENq6riR6x9Im+SGzhXpgooKBRkPsget4EOoH5jNCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="/js/application.js"></script>

	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@0.1.1"></script>

  <link href="../css/application.css" rel="stylesheet">

  <style>
  .container {
  max-width: 960px;
}

/*
 * Custom translucent site header
 */

.site-header {
  background-color: rgba(0, 0, 0, .85);
  -webkit-backdrop-filter: saturate(180%) blur(20px);
  backdrop-filter: saturate(180%) blur(20px);
}
.site-header a {
  color: #8e8e8e;
  transition: color .15s ease-in-out;
}
.site-header a:hover {
  color: #fff;
  text-decoration: none;
}

/*
 * Dummy devices (replace them with your own or something else entirely!)
 */

.product-device {
  position: absolute;
  right: 10%;
  bottom: -30%;
  width: 300px;
  height: 540px;
  background-color: #333;
  border-radius: 21px;
  transform: rotate(30deg);
}

.product-device::before {
  position: absolute;
  top: 10%;
  right: 10px;
  bottom: 10%;
  left: 10px;
  content: "";
  background-color: rgba(255, 255, 255, .1);
  border-radius: 5px;
}

.product-device-2 {
  top: -25%;
  right: auto;
  bottom: 0;
  left: 5%;
  background-color: #e5e5e5;
}


/*
 * Extra utilities
 */

.flex-equal > * {
  flex: 1;
}
@media (min-width: 768px) {
  .flex-md-equal > * {
    flex: 1;
  }
}

</style>
</head>

<body>
  <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
    <div class="col-md-5 p-lg-5 mx-auto my-5">
      <h1 class="display-4 fw-normal">Energy Dashboard</h1>
      <p class="lead fw-normal">St Edmund Hall utilities dashboard (IN DEVELOPMENT)</p>
      <a class="btn btn-outline-primary" href="../index.php">View All Utility Data</a>
    </div>
    <div class="product-device shadow-sm d-none d-md-block"></div>
    <div class="product-device product-device-2 shadow-sm d-none d-md-block"></div>
  </div>

  <?php
  $utilities = array("electric", "gas", "water");

  $locations = array("1", "2", "3", "4");

  foreach ($locations AS $location) {
    $location = new location($location['uid']);

    $output  = "<h3 class=\"display-6 fw-normal text-center\">" . $location->name . "</h3>";
    $output .= "<div class=\"d-md-flex flex-md-equal w-100 my-md-3 ps-md-3\">";

		$jsGraphs = null;
    foreach ($utilities AS $utility) {
			$chartID = "x_" . rand(0, 100000);

      $consumptionThisMonth = $location->consumptionForMonth(date('Y-m-d'), $utility);
      $consumptionPreviousMonth = $location->consumptionForMonth(date('Y-m-d', strtotime('1 month ago')), $utility);
      $consumptionDelta = $consumptionThisMonth - $consumptionPreviousMonth;

      if ($consumptionDelta > 0) {
        $bgClass = "bg-warning";
        $textClass = "bg-light text-dark";
        $subtitle = $consumptionDelta . " more units consumed than previous month";
				$icon = "graph-up";
      } elseif ($consumptionDelta == 0) {
        $bgClass = "bg-primary";
        $textClass = "bg-light text-dark";
        $subtitle = "Same units consumed as previous month";
				$icon = "graph-down";
      } elseif ($consumptionDelta < 0) {
        $bgClass = "bg-success";
        $textClass = "bg-light text-dark";
        $subtitle = $consumptionDelta . " fewer units consumed than previous month";
				$icon = "graph-down";
      } else {
        $bgClass = "bg-dark";
        $textClass = "bg-light text-dark";
        $subtitle = $consumptionDelta . " units consumed";
				$icon = "graph-down";
      }

			$iconOutput = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"../inc/icons.svg#" . $icon . "\"/></svg>";

      $output .= "<div class=\"" . $bgClass . " me-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center text-white overflow-hidden\">";
      $output .= "<div class=\"my-3 py-3\">";
      $output .= "<h2 class=\"display-5\">" . ucwords($utility) . " " . $iconOutput . "</h2>";
      $output .= "<p class=\"lead\">" . $subtitle . "</p>";
      $output .= "</div>";
      $output .= "<div class=\"" . $textClass . " shadow-sm mx-auto\" style=\"width: 100%; height: 300px; border-radius: 21px 21px 0 0;\">";
			$output .= displayGraph($chartID);
      $output .= "</div>";
      $output .= "</div>";

			$jsGraphs .= displayGraphJC($chartID, $location->consumptionByMonth($utility));
    }
    $output .= "</div>";

    echo $output;

		echo $jsGraphs;
  }

  include_once("../views/footer.php");
  ?>

</body>
</html>

<?php

function displayGraph($chartID = null) {
	$graphOutput  = "<canvas id=\"" . $chartID . "\" width=\"370\" height=\"230\"></canvas>";
	$graphOutput .= "";

	return $graphOutput;
}

function displayGraphJC($chartID = null, $data = null) {
	foreach($data AS $date => $value) {
		$dataArray["'" . $date . "'"] = $value;
	}

	echo $datum;
	$jsOutput  = "<script>";
	$jsOutput .= "var ctx = document.getElementById('" . $chartID . "').getContext('2d');";
	$jsOutput .= "var " . $chartID . " = new Chart(ctx, {";
	$jsOutput .= "type: 'bar',";
	$jsOutput .= "data: {";
	$jsOutput .= "labels: [" . implode(",", array_keys($dataArray)) . "],";
	$jsOutput .= "datasets: [{";
	$jsOutput .= "label: 'Consumption',";
	$jsOutput .= "data: [" . implode(",", $dataArray) . "],";
	$jsOutput .= "backgroundColor: [";
	$jsOutput .= "'rgba(153, 102, 255, 0.2)'";
	$jsOutput .= "],";
	$jsOutput .= "borderColor: [";
	$jsOutput .= "'rgba(153, 102, 255, 1)'";
	$jsOutput .= "],";
	$jsOutput .= "borderWidth: 1";
	$jsOutput .= "}]";
	$jsOutput .= "},";
	$jsOutput .= "options: {";
	$jsOutput .= "plugins: {";
	$jsOutput .= "legend: {";
	$jsOutput .= "display: false";
	$jsOutput .= "}";
	$jsOutput .= "},";

	$jsOutput .= "scales: {";
	$jsOutput .= "y: {";
	$jsOutput .= "beginAtZero: true";
	$jsOutput .= "}";
	$jsOutput .= "}";
	$jsOutput .= "}";
	$jsOutput .= "});";
	$jsOutput .= "</script>";


	return $jsOutput;
}


?>
