<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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
					  <h3 class="my-2 py-1">9,184</h3>
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
		type: 'bar',
		toolbar: {
			show: false
		}
	},
	series: [{
		name: 'Test',
		data: [30,40,35,50,49,60,70,91,125]
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
		categories: [1991,1992,1993,1994,1995,1996,1997, 1998,1999],
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