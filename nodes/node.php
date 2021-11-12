<?php
$node = new meter($_GET['nodeUID']);
$location = new location($node->location);

$costUnit = $settingsClass->value("unit_cost_" . $node->type);
$co2eUnit = $settingsClass->value("unit_co2e_" . $node->type);

if (isset($_POST['reading1']) && $_SESSION['logon'] == true) {
	readings::create($node->uid, $_POST['reading1']);
}

$recentReadings = readings::meter_all_readings($node->uid, 5);

$thisYearDateFrom = date('Y-m-d', strtotime('12 months ago'));
$thisYearDateTo = date('Y-m-d');
$consumptionLast12Months = array_reverse($node->consumptionBetweenDatesByMonth($thisYearDateFrom, $thisYearDateTo), true);
$consumptionLast12MonthsTotal = array_sum($consumptionLast12Months);

$previousYearDateFrom = date('Y-m-d', strtotime('24 months ago'));
$previousYearDateTo = date('Y-m-d', strtotime('12 months ago'));
$consumptionPreviousYear12Months = array_reverse($node->consumptionBetweenDatesByMonth($previousYearDateFrom, $previousYearDateTo), true);
$consumptionPreviousYear12MonthsTotal = array_sum($consumptionPreviousYear12Months);

$deltaConsumption = ($consumptionLast12MonthsTotal / $consumptionPreviousYear12MonthsTotal)*100;

if ($deltaConsumption > 100 && !is_infinite($deltaConsumption)) {
	$deltaConsumption = number_format($deltaConsumption-100, 1);
	$deltaConsumptionText = "<span class=\"text-danger fw-bolder me-1\">&#8593; " . $deltaConsumption . "%</span> increase compared to previous year";
} elseif ($deltaConsumption <= 100 && !is_infinite($deltaConsumption)) {
	$deltaConsumption = number_format(100-$deltaConsumption, 1);
	$deltaConsumptionText = "<span class=\"text-success fw-bolder me-1\">&#8595; " . $deltaConsumption . "%</span> decrease compared to previous year";
} else {
	$deltaConsumption = 0;
	$deltaConsumptionText = "&nbsp";
}



$yearlyConsumption = array_reverse($node->consumptionBetweenDatesByYear($dateFrom, $dateTo), true);

?>

<div class="container px-4 py-5">
	<h1 class="d-flex mb-5 justify-content-between align-items-center"><?php echo $node->name; ?>
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dashboard-dropdown">
				<a class="dropdown-item me-2" href="index.php?n=node_edit&nodeUID=<?php echo $node->uid; ?>">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg>
					</span> Edit Node
				</a>
				<div role="separator" class="dropdown-divider my-1"></div>
				<a class="dropdown-item me-2 text-danger" href="#">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#delete"/></svg>
					</span> Delete This Node
				</a>
				
				<a class="dropdown-item" id="test" href="export.php?type=node&filter=<?php echo $node->uid; ?>" target="_blank">
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
					<div class="fs-5 fw-normal mb-2"><?php echo $node->type; ?> Consumption (last 12 months)</div>
					<div class="small mt-2">
						<span class="fw-normal me-2">Yesterday</span>
						<span class="fas fa-angle-up text-success"></span>
						<span class="text-success fw-bold">10.57%</span>
					</div>
				</div>
				<div class="d-flex ms-auto">
					<!--<a href="javascript:DownloadAsImage();" class="btn btn-sm text-muted me-3">
						<svg class="bi" width="24" height="24" role="img"><use xlink:href="inc/icons.svg#download"></use></svg>
					</a>-->
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

<div class="b-example-divider"></div>

<?php
if ($_SESSION['logon'] == true) {
?>
<div class="container px-4 py-5">
	<form class="" method="post" id="readingSubmit" action="index.php?n=node&nodeUID=<?php echo $node->uid; ?>">
		  <div class="input-group">
			<input type="number" class="form-control" name="reading1" placeholder="New Reading" min="<?php echo $node->currentReading(); ?>">
			<button type="submit" class="btn btn-secondary" name="submit">Submit</button>
		  </div>
		</form>
		<div id="reading1Help" class="form-text">Previous reading: <?php echo number_format($node->currentReading()) . " " . $node->unit; ?></div>
</div>
<div class="b-example-divider"></div>
<?php } ?>

<div class="container px-4 py-5">
	<div class="row">
		<div class="col-6">
			graph
		</div>
		<div class="col-6">
			<div class="ct-chart-yearly ct-double-octave ct-series-d"></div>
		</div>
	</div>
</div>

<div class="container px-4 py-5">
	<div class="row">
		<div class="col-lg-6 col-12 mb-3">
			<div class="card border-0 shadow">
				<div class="card-header">
					<div class="row align-items-center">
						<div class="col">
							<h2 class="fs-5 fw-bold mb-0">Recent Readings</h2>
						</div>
						<div class="col text-end">
							<a href="index.php?n=readings&nodeUID=<?php echo $node->uid; ?>" class="btn btn-sm btn-primary">See all</a>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="table align-items-center table-flush">
						<thead class="thead-light">
							<tr>
								<th class="border-bottom" scope="col">Date</th>
								<th class="border-bottom" scope="col">Reading</th>
								<th class="border-bottom" scope="col">Username</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($recentReadings AS $reading) {
								$output  = "<tr>";
								$output .= "<th class=\"text-gray-900\" scope=\"row\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</th>";
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
		<div class="col-lg-6 col-12">
			<div class="card border-0 shadow">
				<div class="card-header border-bottom d-flex align-items-center justify-content-between">
					<h2 class="fs-5 fw-bold mb-0">Node Details</h2>
					<a href="#" class="btn btn-sm btn-primary">Edit</a>
				</div>
				<div class="card-body">
					<ul class="list-group list-group-flush list my--3">
						<li class="list-group-item px-0">Name, Location, Type, Unit, Photograph, Serial, MPRN, Retention, billed, enabled, geo, supplier, account_no, address</li>
					</ul>
					
					<?php
					if ($_SESSION['logon'] == true) {
						printArray($node);
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
var data = {
	// A labels array that can contain any sort of values
	labels: ['<?php echo implode("','", array_keys($consumptionLast12Months)); ?>'],
	// Our series array that contains series objects or in this case series data arrays
	series: [
		[<?php echo implode(",", $consumptionLast12Months); ?>]
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
</script>

<script>
	var data = {
		// A labels array that can contain any sort of values
		labels: ['<?php echo implode("','", array_keys($yearlyConsumption)); ?>'],
		// Our series array that contains series objects or in this case series data arrays
		series: [
			[<?php echo implode(",", $yearlyConsumption); ?>]
		]
	};
	
	new Chartist.Line('.ct-chart-yearly', data, {
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
</script>