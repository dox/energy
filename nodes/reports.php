<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
$locationsClass = new locations();
$nodesClass = new nodes();

if (isset($_POST['nodes_includeHidden'])) {
  $enabled = "";
} else {
  $enabled = " AND enabled = '1'";
}

$nodes = null;
//get each site
foreach ($_POST['locations'] AS $locationUID) {
  //get each node in this site that matches types
  $sql = "SELECT * FROM nodes WHERE location = '" . $locationUID . "' " . $enabled . " AND type IN ('" . implode("','", $_POST['nodes']) . "');";
  $nodesByLocation = $db->query($sql)->fetchAll();

  foreach ($nodesByLocation AS $node) {
	$nodes[] = $node;
  }

  $nodeUnit = $node['unit'];
}
?>

<div class="container px-4 py-5">
	<h1 class="d-flex mb-5 justify-content-between align-items-center">Reports
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dashboard-dropdown">
				<a class="dropdown-item me-2" href="#">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#add"/></svg>
					</span> Saved Reports (coming soon)
				</a>
				<a class="dropdown-item" href="export.php?type=readings&filter=special" target="_blank">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#download"/></svg>
					</span> Export Data
				</a>
			</div>
		</div>
	</h1>
	
	<form method="post" id="termUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">Filter:</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
			  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Locations</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<?php
							foreach ($locationsClass->all() AS $location) {
								$elementName = "locations[]";
								
								$checked = "";
								if (in_array($location['uid'], $_POST['locations'])) {
									$checked = " checked ";
								}
								
								$output  = "<li><a class=\"dropdown-item\" href=\"#\"><input class=\"form-check-input\" type=\"checkbox\" value=\"" . $location['uid'] . "\" id=\"" . $elementName . "\" name=\"" . $elementName . "\"" . $checked . ">";
								$output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
								$output .= $location['name'];
								$output .= "</label>";
								$output .= "</a></li>";
								
								echo $output;
							}
							?>
							
							<li><hr class="dropdown-divider"></li>
							
							<li><a class="dropdown-item" href="#">Select All (coming soon)</a></li>
							<li><input class="form-check-input" type="checkbox" value="1" <?php if (isset($_POST['nodes_includeHidden']) && $_POST['nodes_includeHidden'] == "1") { echo " checked"; } ?> id="nodes_includeHidden" name="nodes_includeHidden">
							<label class="form-check-label" for="flexCheckDefault">Include Hidden Nodes</label>
							</li>
						</ul>
					</li>
				<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Types</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<?php
							foreach (explode(",", $settingsClass->value('node_types')) AS $nodeType) {
								$elementName = "nodes[]";
								
								$checked = "";
								if (in_array($nodeType, $_POST['nodes'])) {
									$checked = " checked ";
								}
								
								$output  = "<li><a class=\"dropdown-item\" href=\"#\"><input class=\"form-check-input\" type=\"radio\" value=\"" . $nodeType . "\" id=\"" . $elementName . "\" name=\"" . $elementName . "\"" . $checked . ">";
								$output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
								$output .= $nodeType . " (" . unitByType($nodeType) . ")";
								$output .= "</label>";
								$output .= "</a></li>";
								
								echo $output;
							}
							?>
						</ul>
					</li>
				<div class="d-flex">
								<input type="text" class="form-control me-2" name="date_from" id="date_from" placeholder="" value="<?php echo $date_meal; ?>" aria-describedby="date_from-addon" required>
							<input type="text" class="form-control me-2" name="date_to" id="date_to" placeholder="" value="<?php echo $date_meal; ?>" aria-describedby="date_to-addon" required>
					</div>
			  </ul>
			  <form class="d-flex">
				<button class="btn btn-outline-success" type="submit">Apply</button>
			  </form>
			</div>
		  </div>
		</nav>
	</form>
	
	<hr />
	
	<div class="row">
		<div class="col-md-8 col-12">
			<div class="ct-chart-sales-value ct-double-octave ct-series-g"></div>
		</div>
		<div class="col-md-4 col-12">
			<div class="ct-chart-location ct-double-octave ct-series-g"></div>
		</div>
	</div>
	
	<hr />
	
	<div class="row">
			<div class="col-lg-4 col-12 mb-3">
				<div class="card shadow">
					<div class="card-body">
						<div class="row">
							<div class="col-3">
								<div class="feature-icon bg-danger bg-gradient">
									<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#<?php echo strtolower($_POST['nodes'][0]); ?>"/></svg>
								</div>
							</div>
							<div class="col-9">
								<?php
								  $totalConsumption = 0;
								  foreach ($nodes AS $node) {
									$node = new node($node['uid']);
									$totalConsumption = $totalConsumption + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
								  }
								  ?>
								<h3 class="mb-1"><?php echo $node->type; ?></h3>
								<h4 class="fw-extrabold mb-1"><?php echo number_format($totalConsumption, 0) . " " . $nodeUnit; ?></h4>
							</div>
							&nbsp;
						</div>
						<?php echo $deltaConsumptionText; ?>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-12 mb-3">
				<div class="card shadow">
					<div class="card-body">
						<div class="row">
							<div class="col-3">
								<div class="feature-icon bg-info bg-gradient">
									£
								</div>
							</div>
							<div class="col-9">
								<?php
								  $settingName = "unit_cost_" . $_POST['nodes'][0];
								
								  $unitCost = $settingsClass->value($settingName);
								  $totalCost = $totalConsumption * $unitCost;
								 ?>
								<h3 class="mb-1">Cost</h3>
								<h4 class="fw-extrabold mb-1"><?php echo "£" . number_format($totalCost, 0); ?></h4>
							</div>
						</div>
						<?php echo "Calculated at £" . number_format($unitCost, 2) . " per " . $node->unit; ?>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-12 mb-3">
				<div class="card shadow">
					<div class="card-body">
						<div class="row">
							<div class="col-3">
								<div class="feature-icon bg-success bg-gradient">
									<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#co2"/></svg>
								</div>
							</div>
							<div class="col-9">
								<?php
								  $settingName = "unit_co2e_" . $_POST['nodes'][0];
								
								  $co2eUnit = $settingsClass->value($settingName);
								  ?>
								<h3 class="mb-1">CO&#8322;</h3>
								<h4 class="fw-extrabold mb-1"><?php echo number_format($totalConsumption * $co2eUnit, 0) . " kg"; ?></h4>
							</div>
						</div>
						<?php echo "Calculated at " . number_format($co2eUnit, 2) . " kg per " . $node->unit; ?>
					</div>
				</div>
			</div>
		</div>
</div>

<div class="b-example-divider"></div>

<div class="container px-4 py-5">
	<?php
	
	echo $nodesClass->nodeTable($nodes);
	
	?>
</div>





<script>
<?php
if (isset($_POST['date_from'])) {
  $dateFrom = $_POST['date_from'];
} else {
  $dateFrom = date('Y-m-d', strtotime('1 year ago'));
}

if (isset($_POST['date_to'])) {
  $dateTo = $_POST['date_to'];
} else {
  $dateTo = date('Y-m-d');
}
?>
var date_from = flatpickr("#date_from", {
  dateFormat: "Y-m-d",
  defaultDate: ['<?php echo $dateFrom; ?>'],
  maxDate: "today"
});

var date_to = flatpickr("#date_to", {
  dateFormat: "Y-m-d",
  defaultDate: ['<?php echo $dateTo; ?>'],
  maxDate: "today"
});
</script>



<!-- Pie Chart showing type usage per location -->
<?php
foreach ($nodes AS $node) {
  $node = new node($node['uid']);
  $location = new location($node->location);

  $data[$location->name] = $data[$location->name] + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
}

$labels = "'" . implode("','", array_keys($data)) . "'";
?>


<!-- CONSUMPTION BY MONTH GRAPH -->
<?php
// CONSUMPTION BY MONTH
$data = array();
foreach ($nodes AS $node) {
  $node = new node($node['uid']);

  $nodeData = $node->consumptionBetweenDatesByMonth($_POST['date_from'], $_POST['date_to']);

  foreach ($nodeData AS $monthData => $value) {
	$monthlyData[$monthData] = $monthlyData[$monthData] + $value;
  }

}
$monthlyData = array_reverse($monthlyData);
$monthlylabels = "'" . implode("','", array_keys($monthlyData)) . "'";
?>





<script>
// LINE CHART
var data = {
	// A labels array that can contain any sort of values
	labels: ['<?php echo implode("','", array_keys($monthlyData)); ?>'],
	// Our series array that contains series objects or in this case series data arrays
	series: [
		[<?php echo implode(",", $monthlyData); ?>]
	]
};

new Chartist.Line('.ct-chart-sales-value', data, {
	low: 0,
	showArea: true,
	fullWidth: true,
	plugins: [
		//Chartist.plugins.tooltip()
	],
	axisX: {
		// On the x-axis start means top and end means bottom
		position: 'end',
		showGrid: true
	},
	axisY: {
		// On the y-axis start means left and end means right
		showGrid: false,
		showLabel: true
	}
});



<!-- Pie Chart showing type usage per location -->
<?php
foreach ($nodes AS $node) {
  $node = new node($node['uid']);
  $location = new location($node->location);

  $data[$location->name] = $data[$location->name] + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
}

?>
// PIE CHART
var data = {
	// A labels array that can contain any sort of values
	labels: ['<?php echo implode("','", array_keys($data)); ?>'],
	// Our series array that contains series objects or in this case series data arrays
	series: [<?php echo implode(",", $data); ?>]
};

var options = {
	labelInterpolationFnc: function(value) {
		return value[0]
	}
};

var responsiveOptions = [
	  ['screen and (min-width: 640px)', {
		chartPadding: 10,
		labelOffset: 30,
		labelDirection: 'explode',
		labelInterpolationFnc: function(value) {
		  return value;
		}
	  }],
	  ['screen and (min-width: 1024px)', {
		labelOffset: 20,
		chartPadding: 0
	  }]
	];

new Chartist.Pie('.ct-chart-location', data, options, responsiveOptions);
</script>