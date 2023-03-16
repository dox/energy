<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
$locationsClass = new locations();
$nodesClass = new nodes();

if ($_POST['nodes_includeHidden'] == 1) {
  $enabled = " AND enabled = '1'";
} else {
  $enabled = "";
}

$nodes = null;
//get each site
foreach ($_POST['locations'] AS $locationUID) {
  //get each node in this site that matches types
  $sql = "SELECT * FROM nodes WHERE location = '" . filter_var($locationUID, FILTER_SANITIZE_NUMBER_INT) . "' " . $enabled . " AND type IN ('" . implode("','", filter_var_array($_POST['nodes'], FILTER_SANITIZE_ENCODED)) . "');";
  $nodesByLocation = $db->query($sql)->fetchAll();
  
  foreach ($nodesByLocation AS $node) {
	$nodes[] = $node;
  }

  $nodeUnit = $node['unit'];
}

$dateFromClean = date('Y-m', strtotime(filter_var($_POST['date_from'], FILTER_SANITIZE_NUMBER_INT)));
$dateToClean   = date('Y-m', strtotime(filter_var($_POST['date_to'], FILTER_SANITIZE_NUMBER_INT)));

$totalConsumption = array();
foreach ($nodes AS $node) {
	$node = new node($node['uid']);
	
	foreach ($node->consumptionByMonth() AS $date => $value) {
		if (date('Y-m', strtotime($date)) >= $dateFromClean && date('Y-m', strtotime($date)) <= $dateToClean) {
			$location = new location($node->location);
			
			$nodeConsumptionByMonth[$node->cleanName()][date('Y-m', strtotime($date))] = $value;
			
			$totalConsumption[$date] = $totalConsumption[$date] + $value;
			$totalConsumptionByLocation[$location->cleanName()] = $totalConsumptionByLocation[$location->cleanName()] + $value;
		}
	}
}
$totalConsumption = array_reverse($totalConsumption);

foreach ($nodeConsumptionByMonth AS $date => $values) {
	if (array_sum($values) == 0) {
		unset($nodeConsumptionByMonth[$date]);
	} else {
		$nodeConsumptionByMonth[$date] = array_reverse($nodeConsumptionByMonth[$date]);
	}
}
?>

<div class="container px-4 py-5">
	<?php
	$title     = "Reports";
	$actions[] = array('name' => 'Saved Reports', 'icon' => 'logs', 'href' => 'index.php?n=logs');
	$actions[] = array('name' => 'Export Data', 'icon' => 'download', 'href' => 'export.php?type=node&filter=');
	
	echo pageHeader($title);
	?>
	
	<form method="post" id="termUpdate" action="index.php?n=reports" class="needs-validation" novalidate>
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
							<li class="dropdown-item">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" onClick="toggleCheckboxes(this)">
									<label class="form-check-label" for="flexCheckDefault">Select All</label>
								</div>
							</li>
							
							<li><hr class="dropdown-divider"></li>
							<?php
							foreach ($locationsClass->all() AS $location) {
								$checked = "";
								if (isset($_POST['locations'])) {
									if (in_array($location['uid'], filter_var_array($_POST['locations'], FILTER_SANITIZE_NUMBER_INT))) {
										$checked = " checked ";
									}
								}
								
								
								$output  = "<li class=\"dropdown-item\">";
								$output .= "<div class=\"form-check\">";
								$output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $location['uid'] . "\" name=\"locations[]\" id=\"loc-" . $location['uid'] . "\"" . $checked . ">";
								$output .= "<label class=\"form-check-label\" for=\"loc-" . $location['uid'] . "\">" . $location['name'] . "</label>";
								$output .= "</div>";
								$output .= "</li>";
								
								echo $output;
							}
							?>
							
							<li><hr class="dropdown-divider"></li>
							
							<li class="dropdown-item">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="1" <?php if (isset($_POST['nodes_includeHidden']) && $_POST['nodes_includeHidden'] == "1") { echo " checked"; } ?> id="nodes_includeHidden" name="nodes_includeHidden">
									<label class="form-check-label" for="nodes_includeHidden">Include Hidden Nodes</label>
								</div>
							</li>
						</ul>
					</li>
				<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Types</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<?php
							
							
							foreach (explode(",", $settingsClass->value('node_types')) AS $nodeType) {
									$checked = "";
									if (isset($_POST['locations'])) {
										if (in_array($nodeType, $_POST['nodes'])) {
											$checked = " checked ";
										}
									}
									
									
									$output  = "<li class=\"dropdown-item\">";
									$output .= "<div class=\"form-check\">";
									$output .= "<input class=\"form-check-input\" type=\"radio\" value=\"" . $nodeType . "\" name=\"nodes[]\" id=\"type-" . $nodeType . "\"" . $checked . ">";
									$output .= "<label class=\"form-check-label\" for=\"type-" . $nodeType . "\">" . $nodeType . " (" . unitByType($nodeType) . ")</label>";
									$output .= "</div>";
									$output .= "</li>";
									
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
			<div id="chart-monthly"></div>
		</div>
		<div class="col-md-4 col-12">
			<div id="chart-location"></div>
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
									<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#<?php echo strtolower(filter_var($_POST['nodes'][0], FILTER_SANITIZE_ENCODED)); ?>"/></svg>
								</div>
							</div>
							<div class="col-9">
								<h3 class="mb-1"><?php echo $node->type; ?></h3>
								<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($totalConsumption), 0) . " " . $nodeUnit; ?></h4>
							</div>
							&nbsp;
						</div>
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
								  $settingName = "unit_cost_" . filter_var($_POST['nodes'][0], FILTER_SANITIZE_ENCODED);
								
								  $unitCost = $settingsClass->value($settingName);
								  $totalCost = array_sum($totalConsumption) * $unitCost;
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
								  $settingName = "unit_co2e_" . filter_var($_POST['nodes'][0], FILTER_SANITIZE_ENCODED);
								
								  $co2eUnit = $settingsClass->value($settingName);
								  ?>
								<h3 class="mb-1">CO&#8322;e</h3>
								<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($totalConsumption) * $co2eUnit, 0) . " kg"; ?></h4>
							</div>
						</div>
						<?php echo "Calculated at " . number_format($co2eUnit, 2) . " kg per " . $node->unit; ?>
					</div>
				</div>
			</div>
		</div>
		
		<div id="chart-heat"></div>
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
  $dateFrom = filter_var($_POST['date_from'], FILTER_SANITIZE_NUMBER_INT);
} else {
  $dateFrom = date('Y-m-d', strtotime('1 year ago'));
}

if (isset($_POST['date_to'])) {
  $dateTo = filter_var($_POST['date_to'], FILTER_SANITIZE_NUMBER_INT);
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


<!-- CONSUMPTION BY MONTH GRAPH -->
<script>
function toggleCheckboxes(source) {
	checkboxes = document.getElementsByName('locations[]');
	for(var i=0, n=checkboxes.length;i<n;i++) {
		checkboxes[i].checked = source.checked;
	}
}
</script>

<script>
// Chart-Monthly
var options = {
	series: [{
		name: "Monthly Consumption",
		data: [<?php echo implode(",", $totalConsumption); ?>]
	}],
	chart: {
		id: 'chart-monthly',
		type: 'bar',
		height: 300,
		toolbar: {
			tools: {
				zoomout: false,
				zoomin: false,
				pan: false
			}
		}
	},
	dataLabels: {
		enabled: false,
	},
	stroke: {
		curve: 'smooth'
	},
	xaxis: {
		categories: ['<?php echo implode("','", array_keys($totalConsumption)); ?>']
	},
	yaxis: {
	  labels: {
		formatter: function (value) {
		  return value + "<?php echo $nodeUnit; ?>";
		}
	  },
	},
};

var chartMonthly = new ApexCharts(document.querySelector("#chart-monthly"), options);
chartMonthly.render();

// Chart-Location
var options = {
	series: [<?php echo implode(",", $totalConsumptionByLocation); ?>],
	chart: {
		id: 'chart-location',
		width: 380,
		type: 'pie',
		toolbar: {
			show: true
		}
	},
	labels: ['<?php echo implode("','", array_keys($totalConsumptionByLocation)); ?>']
};

var chartLocation = new ApexCharts(document.querySelector("#chart-location"), options);
chartLocation.render();
</script>



<?php
foreach ($nodeConsumptionByMonth AS $nodeName => $monthConsumptions) {
	$output  = "{name: '" . $nodeName . "',";
	$output .= "data: [";
	
	$outputMonth = array();
	foreach ($monthConsumptions AS $month => $value) {
		$outputMonth[] .= "{x: '" . $month . "', y: " . $value . "}";
	}
	
	$output .= implode(", ", $outputMonth);
	$output .= "]}";
	
	$outputArray[] = $output;
}
?>

<script>

 var options = {
  series: [<?php echo implode(",", $outputArray); ?>],
  chart: {
  height: 500,
  type: 'heatmap',
},
dataLabels: {
  enabled: false
},


};

var chartHeat = new ApexCharts(document.querySelector("#chart-heat"), options);
chartHeat.render();
</script>