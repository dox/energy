<?php
$site = new site();

$datePreviousFrom = date('Y-m-d', strtotime('24 months ago'));
$dateFrom = date('Y-m-d', strtotime('12 months ago'));
$dateTo = date('Y-m-d');


$monthlyConsumptionElectric = array_sum($site->consumptionBetweenDatesByMonth("electric"));
$monthlyConsumptionGas = array_sum($site->consumptionBetweenDatesByMonth("gas"));
$monthlyConsumptionWater = array_sum($site->consumptionBetweenDatesByMonth("water"));

$monthlyCO2 = $site->co2BetweenDatesByMonth($dateFrom, $dateTo);
$monthlyCO2previous = $site->co2BetweenDatesByMonth($datePreviousFrom, $dateFrom);

$deltaCO2 = percentageDifference(array_sum($monthlyCO2), array_sum($monthlyCO2previous));

$totalCO2Electric = $monthlyConsumptionElectric * $settingsClass->value("unit_co2e_electric");
$totalCO2Gas = $monthlyConsumptionGas * $settingsClass->value("unit_co2e_gas");
$totalCO2Water = $monthlyConsumptionWater * $settingsClass->value("unit_co2e_water");
?>
<div class="container px-4 py-5">
	<?php
	$title     = site_name;
	echo pageHeader($title);
	?>
	
	<div class="col-12 mb-3">
		<div class="card bg-yellow-100 border-0 shadow">
			<div class="card-header d-sm-flex flex-row align-items-center flex-0">
				<div class="d-block mb-3 mb-sm-0">
					<div class="fs-5 fw-normal mb-2">CO&#8322; Emissions from Energy Usage</div>
					
					<h2 class="fs-3 fw-extrabold"><?php echo number_format(array_sum($monthlyCO2)/1000, 2) . " tonnes"; ?></h2>
					
					<div class="small mt-2">
						<span class="fw-normal me-2">Total for the last 12 months across all utilities</span>
						<?php
						if ($deltaCO2 <= 0) {
							echo "<svg class=\"text-success\" width=\"1em\" height=\"1em\" role=\"img\"><use xlink:href=\"inc/icons.svg#graph-down\"></use></svg>";
							echo " <span class=\"text-success\">" . $deltaCO2 . "% decrease compared to previous year</span>";
						} else {
							echo "<svg class=\"text-danger\" width=\"1em\" height=\"1em\" role=\"img\"><use xlink:href=\"inc/icons.svg#graph-up\"></use></svg>";
							echo " <span class=\"text-danger\">" . $deltaCO2 . "% increase compared to previous year</span>";
						}
						?>
					</div>
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format($monthlyConsumptionElectric, 0) . " kWh"; ?></h4>
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format($monthlyConsumptionGas, 0) . " m³"; ?></h4>
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format($monthlyConsumptionWater, 0) . " m³"; ?></h4>
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
			<div class="col-12 mb-4">
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
								foreach (readings::all(10) AS $reading) {
									$node = new node($reading['node']);
									$location = new location($node->location);
									
									$output  = "<tr>";
									$output .= "<th class=\"text-gray-900\" scope=\"row\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</th>";
									$output .= "<td class=\"fw-bolder text-gray-500\"><a href=\"index.php?n=node&nodeUID=" . $node->uid . "\">" . $node->name . "(" . $location->name . ")</a></td>";
									$output .= "<td class=\"fw-bolder text-gray-500\">" . displayReading($reading['reading1']) . "</td>";
									$output .= "<td class=\"fw-bolder text-gray-500\">" . showHide($reading['username']) . "</td>";
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
	series: [{
		name: 'This Year',
		data: [<?php echo implode(",", $monthlyCO2); ?>]
	},{
		name: 'Previous Year',
		data: [<?php echo implode(",", $monthlyCO2previous); ?>]
	}]
};

new Chartist.Line('.ct-chart-sales-value', data, {
	low: 0,
	showArea: true,
	fullWidth: true,
	series: {
		'Previous Year': {
			showLine: false,
			showPoint: false
		}
	},
	plugins: [
		//Chartist.plugins.tooltip()
		Chartist.plugins.legend()
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