<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$meterClass = new meters;

$readingsClass = new Readings;

$gasConsumptionByYear = $readingsClass->consumptionBySiteAllYears('Gas');
$electricConsumptionByYear = $readingsClass->consumptionBySiteAllYears('Electric');
$waterConsumptionByYear = $readingsClass->consumptionBySiteAllYears('Water');

?>

<div class="container">
	<div class="row">
		<h3>Site Settings</h3>
	</div>

	<div class="row">
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
	</div>
</div>



<script>
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

	//var url_base64jp = document.getElementById("canvas").toDataURL("image/jpg");
	//link2.href = url_base64;
	//document.getElementById("link2").href=url;
}
</script>
