<?php
$datePreviousFrom = date('Y-m-d', strtotime('24 months ago'));
$dateFrom = date('Y-m-d', strtotime('12 months ago'));
$dateTo = date('Y-m-d');

$location = new location($_GET['locationUID']);

$readingsClass = new readings();
$nodesClass = new nodes();
$nodes = $location->allnodes("all");

$totalCO2Electric = array_sum($location->consumptionBetweenDatesByMonth("electric")) * $settingsClass->value("unit_co2e_electric");
$totalCO2Gas = array_sum($location->consumptionBetweenDatesByMonth("gas")) * $settingsClass->value("unit_co2e_gas");
$totalCO2Water = array_sum($location->consumptionBetweenDatesByMonth("water")) * $settingsClass->value("unit_co2e_water");

$monthlyCO2 = array_slice($location->co2ByMonth(),0,12);
$monthlyPreviousCO2 = array_slice($location->co2ByMonth(),12,12);

$monthlyCO2previous = $location->co2BetweenDatesByMonth($datePreviousFrom, $dateFrom);

$totalCO2 = array_sum($monthlyCO2);
$deltaCO2 = percentageDifference(array_sum($monthlyCO2), array_sum($monthlyCO2previous));
?>

<div class="container px-4 py-5">
	<?php
	$title     = $location->cleanName();
	$actions[] = array('name' => 'Edit Location', 'icon' => 'edit', 'href' => 'index.php?n=location_edit&locationUID=' . $location->uid);
	$actions[] = array('name' => 'Add Node', 'icon' => 'add', 'href' => 'index.php?n=node_add&locationUID=' . $location->uid);
	$actions[] = array('name' => 'Delete Location', 'icon' => 'delete', 'href' => 'javascript:locationDelete(' . $location->uid . ');', 'class' => 'text-danger');
	$actions[] = array('name' => 'separator');
	$actions[] = array('name' => 'Export Data', 'icon' => 'download', 'href' => 'export.php?type=location&filter=' . $location->uid);
	
	echo pageHeader($title, $actions);
	?>

	<div class="col-12 mb-3">
			<div class="card bg-yellow-100 border-0 shadow">
				<div class="card-header d-sm-flex flex-row align-items-center flex-0">
					<div class="d-block mb-3 mb-sm-0">
						<div class="fs-5 fw-normal mb-2">CO&#8322; Emissions from Energy Usage</div>
						<h2 class="fs-3 fw-extrabold"><?php echo number_format($totalCO2/1000, 2) . " tonnes"; ?></h2>
						<div class="small mt-2">
							<span class="fw-normal me-2">Total for the last 12 months across all utilities</span>
							
							<?php
							if ($deltaCO2 <= 0) {
								echo "<svg class=\"text-success\" width=\"1em\" height=\"1em\" role=\"img\"><use xlink:href=\"inc/icons.svg#graph-down\"></use></svg>";
								echo " <span class=\"text-success\">" . abs($deltaCO2) . "% decrease compared to previous year</span>";
							} else {
								echo "<svg class=\"text-danger\" width=\"1em\" height=\"1em\" role=\"img\"><use xlink:href=\"inc/icons.svg#graph-up\"></use></svg>";
								echo " <span class=\"text-danger\">" . abs($deltaCO2) . "% increase compared to previous year</span>";
							}
							?>
						</div>
					</div>
					<div class="d-flex ms-auto">
						<!--<a href="#" class="btn btn-sm text-muted me-3">
							<svg class="bi" width="24" height="24" role="img"><use xlink:href="inc/icons.svg#download"></use></svg>
						</a>
						<a href="#" class="btn btn-dark btn-sm me-3">Week</a>-->
						</div>
				</div>
				<div class="card-body p-2">
					<div id="chart-monthly"></div>
				</div>
			</div>
		</div>
	
	<div class="row">
		<div class="col-lg-4 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<div class="feature-icon bg-danger bg-gradient">
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#electric"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1">Electricity</h3>
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($location->consumptionBetweenDatesByMonth("electric")), 0) . " kWh"; ?></h4>
							<p>&nbsp;</p>
						</div>
					</div>
					<!--<span class="text-success fw-bolder me-1"><?php echo number_format($totalCO2Electric, 0) . " kg"; ?></span> CO2-->
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<div class="feature-icon bg-warning bg-gradient">
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#gas"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1">Gas</h3>
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($location->consumptionBetweenDatesByMonth("gas")), 0) . " m&#179;"; ?></h4>
							<p><i>(~<?php echo convertm3TokWh(array_sum($location->consumptionBetweenDatesByMonth("gas"))) . " kWh"; ?>)</i></p>
						</div>
					</div>
					<!--<span class="text-success fw-bolder me-1"><?php echo number_format($totalCO2Gas, 0) . " kg"; ?></span> CO2-->
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<div class="feature-icon bg-primary bg-gradient">
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#water"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1">Water</h3>
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($location->consumptionBetweenDatesByMonth("water")), 0) . " m&#179;"; ?></h4>
							<p>&nbsp;</p>
						</div>
					</div>
					<!--<span class="text-success fw-bolder me-1"><?php echo number_format($totalCO2Water, 0) . " kg"; ?></span> CO2-->
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

<div class="container px-4 py-5">
	<div id="map" style="width: 100%; height: 500px"></div>
</div>
		



<script>
var map = L.map('map').setView([<?php echo $location->geoLocation(); ?>], 18);

var locations = [<?php echo implode(",", $location->geoMarkersOfNodes()); ?>];

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	//attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

for (var i = 0; i < locations.length; i++) {
  L.marker([locations[i][1], locations[i][2]]).addTo(map)
	.bindPopup(locations[i][0])
}
</script>
	

<script>
// Chart-Monthly
var options = {
	series: [{
			name: "This Year",
			data: [<?php echo implode(",", array_reverse($monthlyCO2)); ?>]
		},{
			name: "Previous Year",
			data: [<?php echo implode(",", array_reverse($monthlyPreviousCO2)); ?>]
		}],
	chart: {
		id: 'chart-monthly',
		type: 'area',
		height: 300,
		toolbar: {
			tools: {
				zoomout: false,
				zoomin: false,
				pan: false
			}
		}
	},
	stroke: {
		curve: 'smooth'
	},
	dataLabels: {
		enabled: false
	},
	xaxis: {
		categories: ['<?php echo implode("','", array_keys(array_reverse($monthlyCO2))); ?>']
	},
	yaxis: {
	  labels: {
		formatter: function (value) {
		  return (value/1000) + "t";
		}
	  },
	},
};

var chartMonthly = new ApexCharts(document.querySelector("#chart-monthly"), options);
chartMonthly.render();
</script>