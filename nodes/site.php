<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$meterClass = new meters;

$readingsClass = new Readings;

$gasConsumptionThisYear = $readingsClass->consumptionBySiteByYear($thisYear, "Gas");
$gasConsumptionLastYear = $readingsClass->consumptionBySiteByYear($lastYear, "Gas");
$gasConsumptionLastLastYear = $readingsClass->consumptionBySiteByYear($lastLastYear, "Gas");

$electricConsumptionThisYear = $readingsClass->consumptionBySiteByYear($thisYear, "Electric");
$electricConsumptionLastYear = $readingsClass->consumptionBySiteByYear($lastYear, "Electric");
$electricConsumptionLastLastYear = $readingsClass->consumptionBySiteByYear($lastLastYear, "Electric");

?>

<div class="container">
	
	<div class="row">
		<h3>Site Settings</h3>
	</div>
	<canvas id="canvas" width="400" height="200"></canvas>
	<h3>Gas Consumption <?php echo $thisYear . ": " . array_sum($gasConsumptionThisYear) . $meterClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionThisYear) * 0.14)) . ")</i>";?><br />
	Gas Consumption <?php echo $lastYear . ": " . array_sum($gasConsumptionLastYear) . $meterClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionLastYear) * 0.14)) . ")</i>";?><br />
	Gas Consumption <?php echo $lastLastYear . ": " . array_sum($gasConsumptionLastLastYear) . $meterClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
	<h3>Electric Consumption <?php echo $thisYear . ": " . array_sum($electricConsumptionThisYear) . $meterClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionThisYear) * 0.14)) . ")</i>";?><br />
	Electric Consumption <?php echo $lastYear . ": " . array_sum($electricConsumptionLastYear) . $meterClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionLastYear) * 0.14)) . ")</i>";?><br />
	Electric Consumption <?php echo $lastLastYear . ": " . array_sum($electricConsumptionLastLastYear) . $meterClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
	
	<a href="index.php?n=meter_edit&meterUID=<?php echo $meter['uid'];?>" class="btn btn-sm btn-outline-secondary float-right">Edit</a>
	
	<div class="row">

		
	</div>
</div>



<script>
var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var color = Chart.helpers.color;
var barChartData = {
	labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	datasets: [{
		label: 'Gas: <?php echo $lastLastYear;?>',
		stack: 'Stack 0',
		backgroundColor: color(window.chartColors.<?php echo $colour_gas_year3; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year3; ?>,
		borderWidth: 1,
		data: [<?php echo implode($gasConsumptionLastLastYear, ", ");?>]
	}, {
		label: 'Gas: <?php echo $lastYear;?>',
		stack: 'Stack 1',
		backgroundColor: color(window.chartColors.<?php echo $colour_gas_year2; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year2; ?>,
		borderWidth: 1,
		data: [<?php echo implode($gasConsumptionLastYear, ", ");?>]
	}, {
		label: 'Gas: <?php echo $thisYear;?>',
		stack: 'Stack 2',
		backgroundColor: color(window.chartColors.<?php echo $colour_gas_year1; ?>).alpha(0.5).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_gas_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($gasConsumptionThisYear, ", ");?>]
	}, {
		label: 'Electric: <?php echo $lastLastYear;?>',
		stack: 'Stack 0',
		backgroundColor: color(window.chartColors.<?php echo $colour_electric_year3; ?>).alpha(1).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_electric_year3; ?>,
		borderWidth: 1,
		data: [<?php echo implode($electricConsumptionLastLastYear, ", ");?>]
	}, {
		label: 'Electric: <?php echo $lastYear;?>',
		stack: 'Stack 1',
		backgroundColor: color(window.chartColors.<?php echo $colour_electric_year2; ?>).alpha(1).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_electric_year2; ?>,
		borderWidth: 1,
		data: [<?php echo implode($electricConsumptionLastYear, ", ");?>]
	}, {
		label: 'Electric: <?php echo $thisYear;?>',
		stack: 'Stack 2',
		backgroundColor: color(window.chartColors.<?php echo $colour_electric_year1; ?>).alpha(1).rgbString(),
		borderColor: window.chartColors.<?php echo $colour_electric_year1; ?>,
		borderWidth: 1,
		data: [<?php echo implode($electricConsumptionThisYear, ", ");?>]
	}]
};

window.onload = function() {
	var ctx = document.getElementById('canvas').getContext('2d');
	window.myBar = new Chart(ctx, {
		type: 'bar',
		data: barChartData,
		options: {
			responsive: true,
			legend: {
				position: 'top',
			},
			scales: {
				xAxes: [{
					stacked: true,
				}],
				yAxes: [{
					stacked: true
				}]
			}
		}
	});
};
</script>