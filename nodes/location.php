<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$locations = new locations;
$locations->locationUID = $_GET['locationUID'];
$location = $locations->location();

$meterClass = new meters;
$metersAll = $meterClass->allByLocation($location['uid']);


$readingsClass = new Readings;
$readingsClass->locationUID = $location['uid'];

$readingsAll = $readingsClass->readingsByMeter(20);

$gasConsumptionThisYear = $readingsClass->consumptionByLocationByYear($thisYear, "Gas");
$gasConsumptionLastYear = $readingsClass->consumptionByLocationByYear($lastYear, "Gas");
$gasConsumptionLastLastYear = $readingsClass->consumptionByLocationByYear($lastLastYear, "Gas");

$electricConsumptionThisYear = $readingsClass->consumptionByLocationByYear($thisYear, "Electric");
$electricConsumptionLastYear = $readingsClass->consumptionByLocationByYear($lastYear, "Electric");
$electricConsumptionLastLastYear = $readingsClass->consumptionByLocationByYear($lastLastYear, "Electric");

?>

<div class="container">
	<div class="row">
		<h3><?php echo $location['name'];?> <small class="text-muted"><?php echo $location['description']; ?></small></h3>
	</div>
	<canvas id="canvas" width="400" height="200"></canvas>
	<div class="btn-group float-right" role="group" aria-label="Basic example">
		<a href="index.php?n=location_edit&meterUID=<?php echo $meter['uid'];?>" class="btn btn-sm btn-outline-secondary">Edit</a>
		<a href="#" class="btn btn-sm btn-outline-secondary" id="link2" download="chart.png">Export as Image</a>
	</div>
	<h3>Gas Consumption <?php echo $thisYear . ": " . array_sum($gasConsumptionThisYear) . $meterClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionThisYear) * 0.14)) . ")</i>";?><br />
	Gas Consumption <?php echo $lastYear . ": " . array_sum($gasConsumptionLastYear) . $meterClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionLastYear) * 0.14)) . ")</i>";?><br />
	Gas Consumption <?php echo $lastLastYear . ": " . array_sum($gasConsumptionLastLastYear) . $meterClass->thisMeterUnits("Gas") . " <i>(~£" . round((array_sum($gasConsumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
	<h3>Electric Consumption <?php echo $thisYear . ": " . array_sum($electricConsumptionThisYear) . $meterClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionThisYear) * 0.14)) . ")</i>";?><br />
	Electric Consumption <?php echo $lastYear . ": " . array_sum($electricConsumptionLastYear) . $meterClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionLastYear) * 0.14)) . ")</i>";?><br />
	Electric Consumption <?php echo $lastLastYear . ": " . array_sum($electricConsumptionLastLastYear) . $meterClass->thisMeterUnits("Electric") . " <i>(~£" . round((array_sum($electricConsumptionLastLastYear) * 0.14)) . ")</i>";?></h3>
	<div class="row">
		<?php
		$output = "";
		
		foreach ($metersAll AS $meter) {
			$output .= "<div class=\"col-md-4\">";
			$output .= "<div class=\"card mb-4 shadow-sm\">";
			$output .= "<svg class=\"bd-placeholder-img card-img-top\" width=\"100%\" height=\"225\" xmlns=\"http://www.w3.org/2000/svg\" preserveAspectRatio=\"xMidYMid slice\" focusable=\"false\" role=\"img\" aria-label=\"Placeholder: Thumbnail\"><title>Placeholder</title><rect width=\"100%\" height=\"100%\" fill=\"#55595c\"/><text x=\"50%\" y=\"50%\" fill=\"#eceeef\" dy=\".3em\">Thumbnail</text></svg>";
			$output .= "<div class=\"card-body\">";
			$output .= "<p class=\"card-text\">" . $meter['name'] . "</p>";
			$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
			$output .= "<div class=\"btn-group\">";
			$output .= "<a href=\"index.php?n=meter&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
			$output .= "<a href=\"index.php?n=meter_edit&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
			$output .= "</div>";
			$output .= "<small class=\"text-muted\">" . $meter['type'] . "</small>";
			$output .= "</div>";
			$output .= "</div>";
			$output .= "</div>";
			$output .= "</div>";
		}
		
		echo $output;
		?>
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