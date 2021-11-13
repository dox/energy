<?php
$site = new site();
$recentReadings = readings::all(5);

$meter = new meter('69');

$dateFrom = date('Y-m-d', strtotime('12 months ago'));
$dateTo = date('Y-m-d');


$monthlyConsumptionElectric = array_reverse($site->consumptionBetweenDatesByMonth("electric"), true);
$monthlyConsumptionGas = array_reverse($site->consumptionBetweenDatesByMonth("gas"), true);
$monthlyConsumptionWater = array_reverse($site->consumptionBetweenDatesByMonth("water"), true);

$monthlyCO2 = $site->co2BetweenDatesByMonth();
$co2eUnit = $settingsClass->value("unit_co2e_" . $node->type);

$totalCO2Electric = array_sum($monthlyConsumptionElectric) * $settingsClass->value("unit_co2e_electric");
$totalCO2Gas = array_sum($monthlyConsumptionGas) * $settingsClass->value("unit_co2e_gas");
$totalCO2Water = array_sum($monthlyConsumptionWater) * $settingsClass->value("unit_co2e_water");
?>
<div class="container px-4 py-5">
	<h1 class="mb-5"><?php echo site_name; ?></h1>
	
	<div class="col-12 mb-3">
		<div class="card bg-yellow-100 border-0 shadow">
			<div class="card-header d-sm-flex flex-row align-items-center flex-0">
				<div class="d-block mb-3 mb-sm-0">
					<div class="fs-5 fw-normal mb-2">CO&#8322; Emissions from Energy Usage</div>
					<h2 class="fs-3 fw-extrabold"><?php echo number_format(array_sum($monthlyCO2), 0) . " kg"; ?></h2>
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($monthlyConsumptionElectric), 0) . " kWh"; ?></h4>
						</div>
					</div>
					<span class="text-success fw-bolder me-1"><?php echo number_format($totalCO2Electric, 0) . " kg"; ?></span> CO2
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($monthlyConsumptionGas), 0) . " m³"; ?></h4>
						</div>
					</div>
					<span class="text-success fw-bolder me-1"><?php echo number_format($totalCO2Gas, 0) . " kg"; ?></span> CO2
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($monthlyConsumptionWater), 0) . " m³"; ?></h4>
						</div>
					</div>
					<span class="text-success fw-bolder me-1"><?php echo number_format($totalCO2Water, 0) . " kg"; ?></span> CO2
				</div>
			</div>
		</div>
	</div>
	
</div>

<div class="b-example-divider"></div>

<div class="container px-4 py-5">
	<div class="col-12">
		<div class="row">
			<div class="col-6 mb-4">
				<div class="card border-0 shadow">
					<div class="card-header">
						<div class="row align-items-center">
							<div class="col">
								<h2 class="fs-5 fw-bold mb-0">Recent Readings</h2>
							</div>
							<div class="col text-end">
								<a href="index.php?n=readings" class="btn btn-sm btn-primary">See all</a>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table align-items-center table-flush">
							<thead class="thead-light">
								<tr>
									<th class="border-bottom" scope="col">Date</th>
									<th class="border-bottom" scope="col">Node</th>
									<th class="border-bottom" scope="col">Reading</th>
									<th class="border-bottom" scope="col">Username</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($recentReadings AS $reading) {
									$meter = new meter($reading['meter']);
									$location = new location($meter->location);
									
									$output  = "<tr>";
									$output .= "<th class=\"text-gray-900\" scope=\"row\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</th>";
									$output .= "<td class=\"fw-bolder text-gray-500\"><a href=\"index.php?n=node&nodeUID=" . $meter->uid . "\">" . $meter->name . "(" . $location->name . ")</a></td>";
									$output .= "<td class=\"fw-bolder text-gray-500\">" . displayReading($reading['reading1']) . "</td>";
									$output .= "<td class=\"fw-bolder text-gray-500\">" . $reading['username'] . "</td>";
									$output .= "";
									$output .= "</tr>";
									
									echo $output;
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<script>
var data = {
	// A labels array that can contain any sort of values
	labels: ['<?php echo implode("','", array_keys($monthlyCO2)); ?>'],
	// Our series array that contains series objects or in this case series data arrays
	series: [
		[<?php echo implode(",", $monthlyCO2); ?>]
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
		showLabel: true,
		labelInterpolationFnc: function(value) {
			return (value / 1000) + 't';
		}
	}
});
</script>