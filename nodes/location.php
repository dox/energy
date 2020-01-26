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

$readingsAll = $readingsClass->readingsByMeter(20);

$gasConsumptionThisYear = $readingsClass->consumptionByLocationByYear($thisYear, "Gas");
$gasConsumptionLastYear = $readingsClass->consumptionByLocationByYear($lastYear, "Gas");
$gasConsumptionLastLastYear = $readingsClass->consumptionByLocationByYear($lastLastYear, "Gas");

$electricConsumptionThisYear = $readingsClass->consumptionByLocationByYear($thisYear, "Electric");
$electricConsumptionLastYear = $readingsClass->consumptionByLocationByYear($lastYear, "Electric");
$electricConsumptionLastLastYear = $readingsClass->consumptionByLocationByYear($lastLastYear, "Electric");

$waterConsumptionThisYear = $readingsClass->consumptionByLocationByYear($thisYear, "Water");
$waterConsumptionLastYear = $readingsClass->consumptionByLocationByYear($lastYear, "Water");
$waterConsumptionLastLastYear = $readingsClass->consumptionByLocationByYear($lastLastYear, "Water");

?>

<div class="container">
	<div class="row">
		<h3><?php echo $location['name'];?> <small class="text-muted"><?php echo $location['description']; ?></small></h3>
	</div>
	
	<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
		<div class="carousel-inner">
			<div class="carousel-item active">
				<canvas id="canvasGas" width="400" height="200"></canvas>
				<h3>Consumption <?php echo $thisYear . ": " . array_sum($gasConsumptionThisYear) . $metersClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionThisYear) * 0.14)) . ")</i>";?><br />
				Consumption <?php echo $lastYear . ": " . array_sum($gasConsumptionLastYear) . $metersClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionLastYear) * 0.14)) . ")</i>";?><br />
				Consumption <?php echo $lastLastYear . ": " . array_sum($gasConsumptionLastLastYear) . $metersClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
			</div>
			<div class="carousel-item">
				<canvas id="canvasElectric" width="400" height="200"></canvas>
				<h3>Consumption <?php echo $thisYear . ": " . array_sum($electricConsumptionThisYear) . $metersClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionThisYear) * 0.14)) . ")</i>";?><br />
				Consumption <?php echo $lastYear . ": " . array_sum($electricConsumptionLastYear) . $metersClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionLastYear) * 0.14)) . ")</i>";?><br />
				Consumption <?php echo $lastLastYear . ": " . array_sum($electricConsumptionLastLastYear) . $metersClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
			</div>
			<div class="carousel-item">
				<canvas id="canvasWater" width="400" height="200"></canvas>
				<h3>Consumption <?php echo $thisYear . ": " . array_sum($waterConsumptionThisYear) . $metersClass->thisMeterUnits("Water") . " <i>(~£" . round((array_sum($waterConsumptionThisYear) * 0.14)) . ")</i>";?><br />
				Consumption <?php echo $lastYear . ": " . array_sum($waterConsumptionLastYear) . $metersClass->thisMeterUnits("Water") . " <i>(~£" . round((array_sum($waterConsumptionLastYear) * 0.14)) . ")</i>";?><br />
				Consumption <?php echo $lastLastYear . ": " . array_sum($waterConsumptionLastLastYear) . $metersClass->thisMeterUnits("Water") . " <i>(~£" . round((array_sum($waterConsumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
			</div>
		</div>
		<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	</div>




	
	<div class="btn-group float-right" role="group" aria-label="Basic example">
		<a href="index.php?n=location_edit&meterUID=<?php echo $meter['uid'];?>" class="btn btn-sm btn-outline-secondary">Edit</a>
		<a href="index.php?n=meter_add&location=<?php echo $location['uid'];?>" class="btn btn-sm btn-outline-secondary">Add Meter</a>
		<a href="#" class="btn btn-sm btn-outline-secondary" id="link2" download="chart.png">Export as Image</a>
	</div>
	
	
	<div class="row">
		<?php
		
		$output = "";
		
		foreach ($metersAll AS $meter) {
			$output .= $metersClass->displayMeterCard($meter['uid']);
		}
		
		echo $output;
		?>
	</div>
</div>

<script>
var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var color = Chart.helpers.color;
var gasBarChartData = {
	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	datasets: [{
		label: 'Gas: <?php echo $lastLastYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colour_gas_year3; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year3; ?>,
		borderWidth: 1,
		data: [<?php echo implode($gasConsumptionLastLastYear, ", ");?>]
	}, {
		label: 'Gas: <?php echo $lastYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colour_gas_year2; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year2; ?>,
		borderWidth: 1,
		data: [<?php echo implode($gasConsumptionLastYear, ", ");?>]
	}, {
		label: 'Gas: <?php echo $thisYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colour_gas_year1; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($gasConsumptionThisYear, ", ");?>]
	}]
};

var electricBarChartData = {
	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	datasets: [{
		label: 'Electric: <?php echo $lastLastYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colour_electric_year3; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_electric_year3; ?>,
		borderWidth: 1,
		data: [<?php echo implode($electricConsumptionLastLastYear, ", ");?>]
	}, {
		label: 'Electric: <?php echo $lastYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colour_electric_year2; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_electric_year2; ?>,
		borderWidth: 1,
		data: [<?php echo implode($electricConsumptionLastYear, ", ");?>]
	}, {
		label: 'Electric: <?php echo $thisYear;?>',
		backgroundColor: color(window.chartColors.<?php echo $colour_electric_year1; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($electricConsumptionThisYear, ", ");?>]
	}]
};


var waterBarChartData = {
	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	datasets: [{
		label: 'Water: <?php echo $lastLastYear;?>',
		stack: 'Stack 0',
		backgroundColor: color(window.chartColors.<?php echo $colour_water_year3; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_water_year3; ?>,
		borderWidth: 1,
		data: [<?php echo implode($waterConsumptionLastLastYear, ", ");?>]
	}, {
		label: 'Water: <?php echo $lastYear;?>',
		stack: 'Stack 1',
		backgroundColor: color(window.chartColors.<?php echo $colour_water_year2; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_water_year2; ?>,
		borderWidth: 1,
		data: [<?php echo implode($waterConsumptionLastYear, ", ");?>]
	}, {
		label: 'Water: <?php echo $thisYear;?>',
		stack: 'Stack 2',
		backgroundColor: color(window.chartColors.<?php echo $colour_water_year1; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_water_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($waterConsumptionThisYear, ", ");?>]
	}]
};



window.onload = function() {
	var ctx = document.getElementById('canvasGas').getContext('2d');
	var ctx2 = document.getElementById('canvasElectric').getContext('2d');
	var ctx3 = document.getElementById('canvasWater').getContext('2d');
	
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
			}
		},
	});
	
	window.myBar = new Chart(ctx3, {
		type: 'bar',
		data: watercBarChartData,
		options: {
			responsive: true,
			legend: {
				position: 'top',
			},
			animation: {
				duration: 1000,
				onComplete: done
			}
		},
	});
};


function done(){
	var url=myBar.toBase64Image();
	document.getElementById("link2").href=url;

	//var url_base64jp = document.getElementById("canvas").toDataURL("image/jpg");
	//link2.href = url_base64;
	//document.getElementById("link2").href=url;
}
</script>