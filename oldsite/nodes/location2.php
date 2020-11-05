<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$locations = new locations;
$locations->locationUID = $_GET['locationUID'];
$location = $locations->location();

$metersClass = new meters;
$metersAll = $metersClass->allByLocation($location['uid']);

$readingsClass = new Readings;

foreach ($metersAll AS $meter) {
	$readingsClass->meterUID = $meter['uid'];
	$readings = $readingsClass->consumptionByMeterAll();

	//print_r($readings);

	foreach ($readings AS $reading) {
		$date = "'" . date('Y-m-d', strtotime($reading['date'])) . "'";
		$readingArray[$meter['uid']][$date] = "'" . $reading['reading1'] . "'";
		$datasetOutputDates[] = $date;
	}
}
$datasetOutputDates = array_unique($datasetOutputDates);
foreach ($readingArray AS $date => $dataset) {
	$dataSetArray[$date] = implode(",", $dataset);
}

foreach ($dataSetArray AS $dataSetUnique) {
	$output  = "{";
	$output .= "label: 'Reading',";
	$output .= "data: [" . $dataSetUnique . "]";
	$output .= "}";

	$datasetOutput[] = $output;
}

?>

<div class="container">
	<a href="index.php?n=location_disabled&locationUID=<?php echo $location['uid']; ?>" class="btn btn-secondary btn-sm float-right" role="button">View Disabled Meters here</a>
	<div class="row">
		<h3><?php echo $location['name'];?> <small class="text-muted"><?php echo $location['description']; ?></small></h3>

	</div>

	<canvas id="canvas" width="400" height="200"></canvas>

	<div class="row">
		<div class="btn-group float-right" role="group" aria-label="Basic example">
			<a href="index.php?n=location_edit&meterUID=<?php echo $meter['uid'];?>" class="btn btn-sm btn-outline-secondary">Edit</a>
			<a href="index.php?n=meter_add&location=<?php echo $location['uid'];?>" class="btn btn-sm btn-outline-secondary">Add Meter</a>
			<a href="#" class="btn btn-sm btn-outline-secondary" id="link2" download="chart.png">Export as Image</a>
		</div>
	</div>

	<div class="clearfix"></div>
	<div class="row">
		<?php
		$output  = "";

		foreach ($metersAll AS $meter) {
			if ($meter['enabled'] == 1) {
				$output .= $metersClass->displayMeterCard($meter['uid']);
			}
		}

		echo $output;
		?>
	</div>
</div>



<script>
	var timeFormat = 'YYYY/MM/DD';

	var config = {
		type: 'line',
		data: {
			labels: [<?php echo implode(",", $datasetOutputDates); ?>],
			datasets: [<?php echo implode(",", $datasetOutput); ?>]
		},
		options: {
			title: {
				text: 'Readings'
			},
			elements: {
				line: {
					tension: 0
				}
			},
			legend: {
				display: false
			},
			scales: {
				xAxes: [{
					type: 'time',
					time: {
						parser: timeFormat,
						// round: 'day'
						tooltipFormat: 'll'
					},
					scaleLabel: {
						display: false
					}
				}],
				yAxes: [{
					ticks: {
						suggestedMin: 0
					},
					scaleLabel: {
						display: true,
						labelString: 'Reading'
					}
				}]
			},
		}
	};

	window.onload = function() {
		var ctx = document.getElementById('canvas').getContext('2d');
		window.myLine = new Chart(ctx, config);
	};
</script>
