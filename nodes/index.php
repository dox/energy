<?php
$site = new site();

$datePreviousFrom = date('Y-m-d', strtotime('24 months ago'));
$dateFrom = date('Y-m-d', strtotime('12 months ago'));
$dateTo = date('Y-m-d');


$monthlyConsumptionElectric = $site->consumptionBetweenDatesByMonth("electric");
$monthlyConsumptionGas = $site->consumptionBetweenDatesByMonth("gas");
$monthlyConsumptionWater = $site->consumptionBetweenDatesByMonth("water");

$monthlyCO2 = $site->co2BetweenDatesByMonth($dateFrom, $dateTo);
$monthlyCO2previous = $site->co2BetweenDatesByMonth($datePreviousFrom, $dateFrom);

$deltaCO2 = percentageDifference(array_sum($monthlyCO2), array_sum($monthlyCO2previous));

//$totalCO2Electric = array_sum($monthlyConsumptionElectric) * $settingsClass->value("unit_co2e_electric");
//$totalCO2Gas = array_sum($monthlyConsumptionGas) * $settingsClass->value("unit_co2e_gas");
//$totalCO2Water = array_sum($monthlyConsumptionWater) * $settingsClass->value("unit_co2e_water");
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($monthlyConsumptionElectric), 0) . " kWh"; ?></h4>
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($monthlyConsumptionGas), 0) . " m³"; ?></h4>
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
							<h4 class="fw-extrabold mb-1"><?php echo number_format(array_sum($monthlyConsumptionWater), 0) . " m³"; ?></h4>
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
								$readingsClass = new readings();
								
								foreach ($readingsClass->all(10) AS $reading) {
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
// Chart-Monthly
var options = {
	series: [{
		name: "This Year",
		data: [<?php echo implode(",", $monthlyCO2); ?>]
	}, {
		name: "Previous Year",
		data: [<?php echo implode(",", $monthlyCO2previous); ?>]
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
	dataLabels: {
		enabled: false,
	},
	stroke: {
		curve: 'smooth'
	},
	xaxis: {
		categories: ['<?php echo implode("','", array_keys($monthlyCO2)); ?>']
	},
	yaxis: {
	  labels: {
		formatter: function (value) {
		  return (value/1000).toFixed(2) + "t";
		}
	  },
	},
};

var chartMonthly = new ApexCharts(document.querySelector("#chart-monthly"), options);
chartMonthly.render();
</script>