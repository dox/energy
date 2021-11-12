<?php
$location = new location($_GET['locationUID']);
$readingsClass = new readings();
$metersClass = new meters();
$meters = $location->allnodes("all");

$totalCO2Electric = array_sum($location->consumptionBetweenDatesByMonth("electric")) * $settingsClass->value("unit_co2e_electric");
$totalCO2Gas = array_sum($location->consumptionBetweenDatesByMonth("gas")) * $settingsClass->value("unit_co2e_gas");
$totalCO2Water = array_sum($location->consumptionBetweenDatesByMonth("water")) * $settingsClass->value("unit_co2e_water");

$totalCO2 = number_format($totalCO2Electric + $totalCO2Gas + $totalCO2Water, 0);
?>

<div class="container px-4 py-5">
	<h1 class="d-flex mb-5 justify-content-between align-items-center"><?php echo $location->name; ?>
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dashboard-dropdown">
				<a class="dropdown-item me-2 <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=node_edit"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Add Node</a>
				<a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=location_edit&locationUID=<?php echo $location->uid; ?>"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg> Edit Location</a>
				<a class="dropdown-item me-2<?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="#" data-bs-toggle="modal" data-bs-target="#deleteMeterModal"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#refuse"/></svg> Delete Location</a>
				
				<a class="dropdown-item me-2" href="export.php?type=location&filter=<?php echo $location->uid; ?>" target="_blank">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#download"/></svg>
					</span> Export Data
				</a>
			</div>
		</div>
	</h1>

	<div class="col-12 mb-3">
			<div class="card bg-yellow-100 border-0 shadow">
				<div class="card-header d-sm-flex flex-row align-items-center flex-0">
					<div class="d-block mb-3 mb-sm-0">
						<div class="fs-5 fw-normal mb-2">CO&#8322; Emissions from Energy Usage</div>
						<h2 class="fs-3 fw-extrabold"><?php echo number_format($totalCO2, 0) . " kg"; ?></h2>
						<div class="small mt-2">
							<span class="fw-normal me-2">Total for the last 12 months across all utilities</span>
							<span class="fas fa-angle-up text-success"></span>
							<span class="text-success fw-bold">0%</span>
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
					<div class="ct-chart-sales-value ct-double-octave ct-series-g"></div>
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
						</div>
					</div>
					<span class="text-success fw-bolder me-1">22%</span> Since last month
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
						</div>
					</div>
					<span class="text-success fw-bolder me-1">22%</span> Since last month
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
						</div>
					</div>
					<span class="text-success fw-bolder me-1">22%</span> Since last month
				</div>
			</div>
		</div>
	</div>
</div>

<div class="b-example-divider"></div>

<div class="container px-4 py-5">
	<?php
	echo $metersClass->meterTable($meters);
	?>
</div>

<div id="map" style="width: 100%; height: 500px"></div>



<script>
var map = L.map('map').setView([<?php echo $location->geoLocation(); ?>], 18);

var locations = [<?php echo implode(",", $location->geoMarkersOfNodes()); ?>];

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	//attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

for (var i = 0; i < locations.length; i++) {
  L.marker([locations[i][1], locations[i][2]]).addTo(map)
	.bindPopup(locations[i][0], {closeOnClick: false, autoClose: false})
	.openPopup()
}
</script>


<script>
	var data = {
		// A labels array that can contain any sort of values
		labels: ['<?php echo implode("','", array_keys($location->consumptionBetweenDatesByMonth("electric"))); ?>'],
		// Our series array that contains series objects or in this case series data arrays
		series: [
			[<?php echo implode(",", $location->consumptionBetweenDatesByMonth("electric")); ?>]
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
			showLabel: false,
			labelInterpolationFnc: function(value) {
				return '$' + (value / 1) + 'k';
			}
		}
	});
	</script>