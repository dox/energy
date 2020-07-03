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
$readingsClass->locationUID = $location['uid'];

$gasConsumptionByYear = $readingsClass->consumptionByLocationAllYears($location['uid'], 'Gas');
$electricConsumptionByYear = $readingsClass->consumptionByLocationAllYears($location['uid'], 'Electric');
$waterConsumptionByYear = $readingsClass->consumptionByLocationAllYears($location['uid'], 'Water');
?>

<div class="container">
	<a href="index.php?n=location_disabled&locationUID=<?php echo $location['uid']; ?>" class="btn btn-secondary btn-sm float-right" role="button">View Disabled Meters here</a>
	<div class="row">
		<h3><?php echo $location['name'];?> <small class="text-muted"><?php echo $location['description']; ?></small></h3>

	</div>

	<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
		<ol class="carousel-indicators">
			<li data-target="#carouselExampleControls" data-slide-to="0" class="active"></li>
			<li data-target="#carouselExampleControls" data-slide-to="1"></li>
		</ol>
		<div class="carousel-inner">
			<div class="carousel-item active">
				<canvas id="canvasGas" width="400" height="200"></canvas>
			</div>
			<div class="carousel-item">
				<canvas id="canvasElectric" width="400" height="200"></canvas>
			</div>
		</div>
	</div>

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
var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var color = Chart.helpers.color;
var gasBarChartData = {
	labels: [<?php echo implode(", ",array_keys($gasConsumptionByYear));?>],
	datasets: [{
		label: 'Gas Consumption',
		backgroundColor: color(window.chartColors.<?php echo $colour_gas_year1; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($gasConsumptionByYear, ", ");?>]
	}]
};

var electricBarChartData = {
	labels: [<?php echo implode(", ",array_keys($electricConsumptionByYear));?>],
	datasets: [{
		label: 'Electric Consumption',
		backgroundColor: color(window.chartColors.<?php echo $colour_electric_year1; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_electric_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($electricConsumptionByYear, ", ");?>]
	}]
};


var waterBarChartData = {
	labels: [<?php echo implode(", ",array_keys($waterConsumptionByYear));?>],
	datasets: [{
		label: 'Water Consumption',
		stack: 'Stack 0',
		backgroundColor: color(window.chartColors.<?php echo $colour_water_year1; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_water_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($waterConsumptionByYear, ", ");?>]
	}]
};



window.onload = function() {
	var ctx = document.getElementById('canvasGas').getContext('2d');
	var ctx2 = document.getElementById('canvasElectric').getContext('2d');

	window.myBar = new Chart(ctx, {
		type: 'bar',
		data: gasBarChartData,
		options: {
			responsive: true,
			legend: {
				position: 'top',
			},
			animation: {
				duration: 1000,
				onComplete: done
			},
			scales: {
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'm3'
					}
				}]
			}
		},
	});


	window.myBar = new Chart(ctx2, {
		type: 'bar',
		data: electricBarChartData,
		options: {
			responsive: true,
			legend: {
				position: 'top',
			},
			animation: {
				duration: 1000,
				onComplete: done
			},
			scales: {
				yAxes: [{
					display: true,
					scaleLabel: {
						display: true,
						labelString: 'kWh'
					}
				}]
			}
		},
	});


};


function done(){
	var url=myBar.toBase64Image();
	document.getElementById("link2").href=url;
}
</script>
