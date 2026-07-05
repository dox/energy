<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
$node = new node(cleanInt($_GET['nodeUID'] ?? 0));
$isLoggedIn = isset($_SESSION['logon']) && $_SESSION['logon'] === true;

$consumption = array_merge(generateMonthsArray(24), $node->consumptionByMonth());
krsort($consumption);

$consumptionLast12Months = array_slice($consumption, 0, 12, true);
$consumptionPrevious12Months = array_slice($consumption, 12, 12, true);

$consumptionLast12MonthsTotal = array_sum($consumptionLast12Months);
$consumptionPrevious12MonthsTotal = array_sum($consumptionPrevious12Months);

$location = new location($node->location);

if (isset($_FILES['photograph']) && $isLoggedIn) {
	$node->uploadImage($_FILES);
	$node = new node(cleanInt($_GET['nodeUID'] ?? 0));
}

if (isset($_POST['deletePhoto']) && $isLoggedIn) {
	$node->deleteImage($_POST['deletePhoto']);
	$node = new node(cleanInt($_GET['nodeUID'] ?? 0));
}

$costUnit = $settingsClass->value("unit_cost_" . $node->type);
$co2eUnit = $settingsClass->value("unit_co2e_" . $node->type);

if (isset($_POST['reading1']) && $isLoggedIn) {
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
	<?php
	$title     = $node->cleanName();
	$actions[] = array('name' => 'Edit Node', 'icon' => 'edit', 'href' => 'index.php?n=node_edit&nodeUID= ' . $node->uid);
	$actions[] = array('name' => 'Delete Node', 'icon' => 'delete', 'href' => 'javascript:nodeDelete(' . $node->uid . ');', 'class' => 'text-danger');
	$actions[] = array('name' => 'View Debug', 'icon' => 'settings', 'href' => 'index.php?n=node-debug&nodeUID= ' . $node->uid);
	$actions[] = array('name' => 'separator');
	$actions[] = array('name' => 'Export Data', 'icon' => 'download', 'href' => 'export.php?type=node&filter=' . $node->uid);
	
	echo pageHeader($title, $actions);
	?>
		
	<div class="col-12 mb-3">
		<div class="card bg-yellow-100 border-0 shadow">
			<div class="card-header d-sm-flex flex-row align-items-center flex-0">
				<div class="d-block mb-3 mb-sm-0">
					<div class="fs-5 fw-normal mb-2"><?php echo escape($node->type); ?> Consumption (<?php echo escape($node->unit); ?>)</div>
					<div class="small mt-2">
						<span class="fw-normal me-2"> </span>
						<span class="fas fa-angle-up text-success"></span>
						<span class="text-success fw-bold"></span>
					</div>
				</div>
				<div class="d-flex ms-auto">
					<!--<a href="javascript:DownloadAsImage();" class="btn btn-sm text-muted me-3">
						<svg class="bi" width="24" height="24" role="img"><use xlink:href="inc/icons.svg#download"></use></svg>
					</a>-->
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
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#<?php echo escape(strtolower($node->type)); ?>"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1"><?php echo escape($node->type); ?></h3>
							<h4 class="fw-extrabold mb-1"><?php echo number_format($consumptionLast12MonthsTotal, 0) . " " . escape($node->unit); ?></h4>
							<?php
							if ($node->type == "Gas") {
								echo "<p><i>(~" . convertm3TokWh($consumptionLast12MonthsTotal) . " kWh" . ")</i></p>";
							} else {
								echo "<p>&nbsp;</p>";
							}
							?>
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
					<?php echo "Calculated at £" . number_format($costUnit, 2) . " per " . escape($node->unit); ?>
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
					<?php echo "Calculated at " . number_format($co2eUnit, 2) . " kg per " . escape($node->unit); ?>
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
	<form class="" method="post" id="readingSubmit" action="index.php?n=node&nodeUID=<?php echo cleanInt($node->uid); ?>">
		  <div class="input-group">
			<input type="text" class="form-control input-primary " name="reading_date" id="reading_date" placeholder="Select Date" readonly="readonly">
			<input type="number" class="form-control input-primary" name="reading1" placeholder="New Reading" min="<?php echo $node->currentReading(); ?>">
			<button type="submit" class="btn btn-lg btn-primary" name="submit">Submit</button>
		  </div>
		</form>
		<div id="reading1Help" class="form-text">Previous reading: <?php echo number_format($node->currentReading()) . " " . escape($node->unit); ?></div>
</div>
<div class="b-example-divider"></div>
<?php } ?>

<div class="container px-4 py-5">
	<div class="row">
		<div class="col-lg-6 col-12 mb-3">
			<div class="card border-0 shadow mb-3">
				<div class="card-header">
					<div class="row align-items-center">
						<div class="col">
							<h2 class="fs-5 fw-bold mb-0">Recent Readings</h2>
						</div>
						<div class="col text-end">
							<a href="index.php?n=readings&nodeUID=<?php echo cleanInt($node->uid); ?>" class="btn btn-sm btn-primary">See all</a>
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
							foreach (array_splice($node->readings_all(), 0, 5) as $reading) {
								$output  = "<tr>";
								$output .= "<th class=\"text-gray-900\" scope=\"row\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</th>";
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
				<div id="chart-readings"></div>
			</div>
			
			<div class="card border-0 shadow">
				<div class="card-body">
					<?php echo $node->displayImage();
					
					if ($isLoggedIn) {
						$photographs = $node->photographFilenames();
						$uploadButtonText = empty($photographs) ? "Upload" : "Add Photograph";

						$output  = "<form method=\"POST\" enctype=\"multipart/form-data\" class=\"mb-3\">";
						$output .= "<div class=\"input-group\">";
						$output .= "<input class=\"form-control\" type=\"file\" id=\"photograph\" name=\"photograph\" accept=\"image/*\" required>";
						$output .= "<button type=\"submit\" class=\"btn btn-primary\">" . $uploadButtonText . "</button>";
						$output .= "</div>";
						$output .= "</form>";

						foreach ($photographs AS $index => $photograph) {
							$deleteButtonText = count($photographs) === 1 ? "Delete Photograph" : "Delete Photograph " . cleanInt($index + 1);

							$output .= "<form method=\"POST\" class=\"d-grid mb-2\" onsubmit=\"return confirm('Are you sure you want to delete this photograph?');\">";
							$output .= "<button type=\"submit\" class=\"btn btn-sm btn-outline-danger\">" . $deleteButtonText . "</button>";
							$output .= "<input type=\"hidden\" name=\"deletePhoto\" value=\"" . escape($photograph) . "\"/>";
							$output .= "</form>";
						}

						echo $output;
					}
					?>
					
					
				</div>
			</div>
			
		</div>
		<div class="col-lg-6 col-12">
			<div class="card border-0 mb-3 shadow">
				<div class="card-header border-bottom d-flex align-items-center justify-content-between">
					<h2 class="fs-5 fw-bold mb-0">Annual Consumption (<?php echo escape($node->unit); ?>)</h2>
				</div>
				<div class="card-body">
					<div id="chart-annual"></div>
				</div>
			</div>
			
			<div class="card border-0 shadow">
				<div class="card-header border-bottom d-flex align-items-center justify-content-between">
					<h2 class="fs-5 fw-bold mb-0">Node Details</h2>
					<a href="index.php?n=node_edit&nodeUID=<?php echo cleanInt($node->uid); ?>" class="btn btn-sm btn-primary">Edit</a>
				</div>
				<div class="card-body">
					<ul class="list-group list-group-flush list my--3">
						<?php
						if ($node->enabled == 1) {
							$enabled = "<span class=\"btn btn-sm btn-outline-success float-end\">Enabled</span>";
						} else {
							$enabled = "<span class=\"btn btn-sm btn-outline-dark float-end\">Disabled</span>";
						}
						?>
						<li class="list-group-item px-0"><strong>Name:</strong> <?php echo escape($node->name) . $enabled ?></li>
						<li class="list-group-item px-0"><strong>Location:</strong> <?php echo $location->cleanName();?></li>
						<li class="list-group-item px-0"><strong>Type / Units:</strong> <?php echo escape($node->type) . " / " . escape($node->unit);?></li>
						<li class="list-group-item px-0"><strong>Retention:</strong> <?php echo $node->cleanRetention(true);?></li>
						<li class="list-group-item px-0"><strong>Geo:</strong> <?php echo escape($node->geo);?></li>
						
						<li class="list-group-item px-0"><strong>Serial:</strong> <?php echo escape(showHide($node->serial));?></li>
						<li class="list-group-item px-0"><strong>MPRN:</strong> <?php echo escape(showHide($node->mprn));?></li>
						<li class="list-group-item px-0"><strong>Billed to Tennant:</strong> <?php echo escape(showHide($node->billed));?></li>
						<li class="list-group-item px-0"><strong>Supplier:</strong> <?php echo escape(showHide($node->supplier));?></li>
						<li class="list-group-item px-0"><strong>Account No:</strong> <?php echo escape(showHide($node->account_no));?></li>
						<li class="list-group-item px-0"><strong>Address:</strong> <?php echo nl2br(escape(showHide($node->address)));?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

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

<script>
var fp2 = flatpickr("#reading_date", {
  enableTime: true,
  time_24hr: true,
  dateFormat: "Y-m-d H:i",
  defaultDate: '<?php echo date('Y-m-d H:i');?>'
})
</script>


<?php
// build array for readings
foreach ($node->readings_all() as $reading) {
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
	  decimalsInFloat: 0
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
	yaxis: {
	  decimalsInFloat: 0
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
	  decimalsInFloat: 0
	},
};

var chartAnnual = new ApexCharts(document.querySelector("#chart-annual"), options);
chartAnnual.render();
</script>
