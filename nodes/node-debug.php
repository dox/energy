<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
admin_gatekeeper();

$node = new node($_GET['nodeUID']);

$consumptionLast12Months = array_slice($node->consumptionByMonth(), 0, 12, true);
$consumptionPrevious12Months = array_slice($node->consumptionByMonth(), 12, 12, true);
?>

<div class="container px-4 py-5">
	<?php
	$title     = $node->cleanName();
	$actions[] = array('name' => 'Edit Node', 'icon' => 'edit', 'href' => 'index.php?n=node_edit&nodeUID= ' . $node->uid);
	$actions[] = array('name' => 'Delete Node', 'icon' => 'delete', 'href' => 'javascript:nodeDelete(' . $node->uid . ');', 'class' => 'text-danger');
	$actions[] = array('name' => 'separator');
	$actions[] = array('name' => 'Export Data', 'icon' => 'download', 'href' => 'export.php?type=node&filter=' . $node->uid);
	
	echo pageHeader($title, $actions);
	?>
		
	
	
	<div class="row">
		<div class="col-lg-4 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<div class="feature-icon bg-danger bg-gradient">
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#<?php echo strtolower($node->type); ?>"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1"><?php echo $node->type; ?></h3>
							<h4 class="fw-extrabold mb-1"><?php echo number_format($consumptionLast12MonthsTotal, 0) . " " .$node->unit; ?></h4>
						</div>
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
							<h3 class="mb-1">Cost</h3>
							<h4 class="fw-extrabold mb-1"><?php echo "£" . number_format($consumptionLast12MonthsTotal * $costUnit); ?></h4>
						</div>
					</div>
					<?php echo "Calculated at £" . number_format($costUnit, 2) . " per " . $node->unit; ?>
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
							<h3 class="mb-1">CO&#8322;e</h3>
							<h4 class="fw-extrabold mb-1"><?php echo number_format($consumptionLast12MonthsTotal * $co2eUnit, 0) . " kg"; ?></h4>
						</div>
					</div>
					<?php echo "Calculated at " . number_format($co2eUnit, 2) . " kg per " . $node->unit; ?>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="container px-4 py-5">
	<div class="row">
		<div class="col-lg-6 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<?php
					printArray($node);
					?>
				</div>
			</div>
			
		</div>
		<div class="col-lg-6 col-12 mb-3">
			<?php
			echo $node->displayImage();
			
			echo $node->photograph;
			?>
		</div>
	</div>
	
</div>

<div class="b-example-divider"></div>

<div class="container px-4 py-5">
	<div class="row">
		<div class="col-8 mb-3">
			<div class="card bg-yellow-100 border-0 shadow">
				<div class="card-header d-sm-flex flex-row align-items-center flex-0">
					<div class="d-block mb-3 mb-sm-0">
						<div class="fs-5 fw-normal mb-2"><?php echo $node->type; ?> Consumption (last 12 months)</div>
					</div>
				</div>
				<div class="card-body p-2">
					<div id="chart-monthly"></div>
				</div>
			</div>
		</div>
		<div class="col-4 mb-3">
			<div class="card bg-yellow-100 border-0 shadow">
				<div class="card-header d-sm-flex flex-row align-items-center flex-0">
					<div class="d-block mb-3 mb-sm-0">
						<div class="fs-5 fw-normal mb-2">Annual Consumption</div>
					</div>
				</div>
				<div class="card-body p-2">
					<div id="chart-annual"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container px-4 py-5">
	<div class="col-12 mb-3">
		<div class="card border-0 shadow">
			<div class="card-header d-sm-flex flex-row align-items-center flex-0">
				<div class="d-block mb-3 mb-sm-0">
					<div class="fs-5 fw-normal mb-2">All Readings</div>
				</div>
			</div>
			<div class="card-body p-2">
				<div id="chart-readings"></div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-6 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<h3>Readings</h3>
					<table class="table">
						<thead>
							<th>uid</th>
							<th>Date</th>
							<th>Username</th>
							<th>Reading</th>
						</thead>
					<?php
					foreach ($node->readings_all() AS $reading) {
						$output  = "<tr>";
					
						$output .= "<td>" . $reading['uid'] . "</td>";
						$output .= "<td>" . $reading['date'] . "</td>";
						$output .= "<td>" . $reading['username'] . "</td>";
						$output .= "<td>" . $reading['reading1'] . "</td>";
						$output .= "</tr>";
						
						echo $output;
					}
					?>
					</table>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<h3>Consumption</h3>
					<table class="table">
						<thead>
							<th>Date</th>
							<th>Consumption</th>
						</thead>
					<?php
					$averages = $node->averagesForReadings();
					printArray($averages);
					foreach ($node->consumptionByMonth($debug = true) AS $date => $value) {
						$output  = "<tr>";
						$output .= "<td>" . $date . "</td>";
						$output .= "<td>" . $value;
						
						if ($value == ($averages['differencePerDay'] * 30)) {
							$output .= " <span class=\"badge rounded-pill text-bg-info\">ESTIMATE</span>";
						}
						$output .= "</td>";
						$output .= "</tr>";
						
						echo $output;
					}
					?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="b-example-divider"></div>

<?php






$consumptionLast12MonthsTotal = array_sum($consumptionLast12Months);
$consumptionPrevious12MonthsTotal = array_sum($consumptionPrevious12Months);

$location = new location($node->location);

if (isset($_FILES['photograph']) && $_SESSION['logon'] == true) {
	$node->uploadImage($_FILES);
	$node = new node($_GET['nodeUID']);
}

if (isset($_POST['deletePhoto']) && $_SESSION['logon'] == true) {
	$node->deleteImage();
	$node = new node($_GET['nodeUID']);
}

$costUnit = $settingsClass->value("unit_cost_" . $node->type);
$co2eUnit = $settingsClass->value("unit_co2e_" . $node->type);

if (isset($_POST['reading1']) && $_SESSION['logon'] == true) {
	$readingsClass = new readings();
	$readingsClass->create($node->uid, $_POST['reading_date'], $_POST['reading1']);
}

if ($consumptionLast12MonthsTotal <= $consumptionPrevious12MonthsTotal && $consumptionLast12MonthsTotal > 0 && $consumptionPrevious12MonthsTotal > 0) {
	$deltaConsumption = ($consumptionLast12MonthsTotal / $consumptionPrevious12MonthsTotal)*100;
	
	$deltaConsumption = number_format(100-$deltaConsumption, 1);
	$deltaConsumptionText = "<span class=\"text-success fw-bolder me-1\">&#8595; " . $deltaConsumption . "%</span> less than previous year";
} elseif ($consumptionLast12MonthsTotal >= $consumptionPrevious12MonthsTotal && $consumptionLast12MonthsTotal > 0 && $consumptionPrevious12MonthsTotal > 0) {
	$deltaConsumption = ($consumptionLast12MonthsTotal / $consumptionPrevious12MonthsTotal)*100;
	
	$deltaConsumption = number_format($deltaConsumption-100, 1);
	$deltaConsumptionText = "<span class=\"text-danger fw-bolder me-1\">&#8593; " . $deltaConsumption . "%</span> more than previous year";
	
} else {
	
	$deltaConsumptionText = "";
}



?>

<div class="container px-4 py-5">
	<div id="map" style="width: 100%; height: 500px"></div>
</div>


<script>
	var map = L.map('map').setView([<?php echo $node->geoLocation(); ?>], 18);
	
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		//attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);
	
	<?php
	if (isset($location->geo)) {
	  $output  = "L.marker([" . $node->geoLocation() . "]).addTo(map)";
	  $output .= ".bindPopup('" . escape($node->name) . "')";
	  $output .= ".openPopup();";
	
	  echo $output;
	}
	?>
	var popup = L.popup();
</script>


<?php
// build array for readings
foreach ($node->readings_all() AS $reading) {
	$chartReadingsArray[] = "[" . (strtotime($reading['date'])*1000) . "," . $reading['reading1'] . "]";
}

// build array for annual
$yearlyConsumption = array_reverse($node->consumptionBetweenDatesByYear($dateFrom, $dateTo), true);
?>
<script>
// Chart-Monthly
var options = {
	series: [{
		name: "This Year",
		data: [<?php echo implode(",", array_reverse($consumptionLast12Months)); ?>]
	}, {
		name: "Previous Year",
		data: [<?php echo implode(",", array_reverse($consumptionPrevious12Months)); ?>]
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
		enabled: false
	},
	stroke: {
		curve: 'smooth'
	},
	xaxis: {
		categories: ['<?php echo implode("','", array_reverse(array_keys($consumptionLast12Months))); ?>']
	},
	yaxis: {
	  labels: {
		formatter: function (value) {
		  return value + "<?php echo $node->unit; ?>";
		}
	  },
	},
};

var chartMonthly = new ApexCharts(document.querySelector("#chart-monthly"), options);
chartMonthly.render();

// Chart-Readings
var options = {
	series: [{
		name: "Reading Value",
		data: [<?php echo implode (",", $chartReadingsArray); ?>]
	}],
	chart: {
		id: 'chart-readings',
		type: 'line',
		height: 300,
		toolbar: {
			tools: {
				zoomout: false,
				zoomin: false,
				pan: false
			}
		}
	},
	xaxis: {
		type: 'datetime',
	},
	tooltip: {
		x: {
			format: 'yyyy MMM dd'
		}
	},
};

var chartReadings = new ApexCharts(document.querySelector("#chart-readings"), options);
chartReadings.render();


// Chart-Annual
var options = {
	series: [{
		name: "Annual Consumption",
		data: [<?php echo implode (",", $yearlyConsumption); ?>]
	}],
	chart: {
		id: 'chart-annual',
		type: 'bar',
		toolbar: {
			tools: {
				zoomout: false,
				zoomin: false,
				pan: false
			}
		}
	},
	xaxis: {
		categories: ['<?php echo implode ("','", array_keys($yearlyConsumption)); ?>']
	},
	yaxis: {
	  labels: {
		formatter: function (value) {
		  return value + "<?php echo $node->unit; ?>";
		}
	  },
	},
};

var chartAnnual = new ApexCharts(document.querySelector("#chart-annual"), options);
chartAnnual.render();
</script>