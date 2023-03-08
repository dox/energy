<?php
$site = new site();

$datePreviousFrom = date('Y-m-d', strtotime('24 months ago'));
$dateFrom = date('Y-m-d', strtotime('12 months ago'));
$dateTo = date('Y-m-d');


printArray($site->consumptionByMonth("Gas"));

//$monthlyConsumptionElectric = $site->consumptionBetweenDatesByMonth("electric");
//$monthlyConsumptionGas = $site->consumptionBetweenDatesByMonth("gas");
//$monthlyConsumptionWater = $site->consumptionBetweenDatesByMonth("water");

?>

<div class="container">
	<h4 class="page-title">Dashboard</h4>
</div>

<div class="container">
  <div class="row align-items-start">
	<div class="col">
	  <div class="card">
		  <div class="card-body">
			  <div class="row align-items-center">
				  <div class="col-6">
					  <h5 class="text-muted fw-normal mt-0 text-truncate" title="Campaign Sent">Test Graph</h5>
					  <h3 class="my-2 py-1"><?php echo number_format(array_sum($monthlyConsumptionElectric)); ?></h3>
					  <p class="mb-0 text-muted">
						  <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 3.27%</span>
					  </p>
				  </div>
				  <div class="col-6">
					  <div class="text-end">
						  <div id="chart">
						  </div>
					  </div>
				  </div>
			  </div> <!-- end row-->
		  </div> <!-- end card-body -->
	  </div>
	</div>
	<div class="col">
	  <div class="card">
		<div class="card-body">
		  This is some text within a card body.
		</div>
	  </div>
	</div>
	<div class="col">
	  <div class="card">
		<div class="card-body">
		  This is some text within a card body.
		</div>
	  </div>
	</div>
  </div>
</div>

<div class="container px-4 py-5">
	<?php
	echo "test";
	?>
</div>



<script>
var options = {
	chart: {
		type: 'line',
		toolbar: {
			show: false
		}
	},
	stroke: {
		curve: 'smooth'
	},
	series: [{
		name: 'Test',
		data: [<?php echo implode(",", $monthlyConsumptionElectric); ?>]
	}],
	grid: {
		xaxis: {
			lines: {
				show: false
			}
		},
		yaxis: {
			lines: {
				show: false
			}
		}
	},
	dataLabels: {
		enabled: false
	},
	xaxis: {
		show: false,
		axisBorder: {
			show: false
		},
		axisTicks: {
			show: false
		},
		categories: ['<?php echo implode("','", array_keys($monthlyConsumptionElectric)); ?>'],
		labels: {
			show: false
		}
	},
	yaxis: {
		labels: {
			show: false
		}
	}
}

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>



<?php
$node = new node(194);
$recentReadings = $node->readings_all();

foreach ($recentReadings AS $date => $value) {
	$chartReadingsArray[] = "[" . (strtotime($date)*1000) . "," . $value . "]";
}
?>

<div id="chart-timeline"></div>


<!-- APEX -->
<script>
var options = {
	chart: {
		type: 'line',
		toolbar: {
			show: false
		}
	},
	series: [{
		name: 'Test',
		data: [<?php echo implode(",", $recentReadings); ?>]
	}],
	grid: {
		xaxis: {
			lines: {
				show: false
			}
		},
		yaxis: {
			lines: {
				show: false
			}
		}
	},
	dataLabels: {
		enabled: false
	},
	xaxis: {
		show: false,
		axisBorder: {
			show: false
		},
		axisTicks: {
			show: false
		},
		categories: ['<?php echo implode("','", array_keys($recentReadings)); ?>'],
		labels: {
			show: false
		}
	},
	yaxis: {
		labels: {
			show: false
		}
	}
}

var chart = new ApexCharts(document.querySelector("#chart-readings"), options);
chart.render();




var options = {
	  series: [{
	  data: [
		  <?php echo implode (",", $chartReadingsArray); ?>
	  ]
	}],
	  chart: {
	  id: 'area-datetime',
	  type: 'line',
	},
	xaxis: {
	  type: 'datetime',
	},
	tooltip: {
	  x: {
		format: 'dd MMM yyyy'
	  }
	},
	};

	var chart = new ApexCharts(document.querySelector("#chart-timeline"), options);
	chart.render();
</script>