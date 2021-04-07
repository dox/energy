<?php
$locationsClass = new locations();
$readingsClass = new readings();
$meter = new meter($_GET['meterUID']);

if (isset($_POST['reading1']) && $_SESSION['logon'] == true) {
  $readingsClass->create($meter->uid, $_POST['reading1']);
}

$location = new location($meter->location);
$readings = $readingsClass->meter_all_readings($meter->uid);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Node: <?php echo $meter->name; ?></h1>

  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="title_dropdown" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
      <ul class="dropdown-menu" aria-labelledby="title_dropdown">
        <li><a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=node_edit&nodeUID=<?php echo $meter->uid; ?>"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg> Edit Node</a></li>
        <li><a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="#" data-bs-toggle="modal" data-bs-target="#deleteMeterModal"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#refuse"/></svg> Delete Node</a></li>
      </ul>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-3">
  </div>
  <div class="col-6">
    <h1 class="text-center"><?php echo $meter->meterTypeBadge();?></h1>
  </div>
  <div class="col-3">
  </div>
</div>

<?php
if ($_SESSION['logon'] == true) {
?>
<form class="card mb-4 p-2" method="post" id="readingSubmit" action="index.php?n=node&meterUID=<?php echo $meter->uid; ?>">
  <div class="input-group">
    <input type="text" class="form-control" name="reading1" placeholder="Reading">
    <button type="submit" class="btn btn-secondary" name="submit">Submit</button>
  </div>
</form>
<?php } ?>



<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">
      Details
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab" aria-controls="home" aria-selected="false">
      Consumption By Month
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly" type="button" role="tab" aria-controls="profile" aria-selected="false">
      Consumption By Year
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="readings-tab" data-bs-toggle="tab" data-bs-target="#readings" type="button" role="tab" aria-controls="contact" aria-selected="false">
      Readings
    </button>
  </li>
</ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
    <h3>Details</h3>

    <?php echo "<strong>Serial Number: " . $meter->displaySerialNumber() . "</strong>"; ?>
    <div id="map" style="width: 100%; height: 500px"></div>

    <?php echo $meter->displayImage(); ?>
  </div>
  <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
    <h3>Consumption by Month</h3>
    <canvas id="monthlyConsumption"></canvas>
  </div>
  <div class="tab-pane fade" id="yearly" role="tabpanel" aria-labelledby="yearly-tab">
    <h3>Consumption by Year</h3>
    <button type="button" class="btn btn-small btn-link float-end" data-bs-toggle="modal" data-bs-target="#projectedConsumptionModal">How Is 'Projected Comsumption' calculated?</button>

    <canvas id="yearlyConsumption"></canvas>
  </div>
  <div class="tab-pane fade" id="readings" role="tabpanel" aria-labelledby="readings-tab">
    <h3>Meter Total</h3>
    <canvas id="meterReadings"></canvas>

    <h4 class="d-flex justify-content-between align-items-center mb-3">Readings
      <span class="badge bg-secondary rounded-pill"><?php echo count($readings); ?></span>
    </h4>

    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>Date</th>
            <th>Reading</th>
            <th>Username</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($readings AS $reading) {
            if ($_SESSION['logon'] == true) {
              $deleteIcon = "<svg width=\"1em\" height=\"1em\" onclick=\"readingDelete(" . $reading['uid'] . ")\" class=\"text-decoration-none text-muted me-1 float-end\"><use xlink:href=\"inc/icons.svg#delete\"/></svg>";
            } else {
              $deleteIcon = "";
            }

            $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\" id=\"reading_row_" . $reading['uid' ]. "\">";

            $output  = "<tr>";
            $output .= "<td>" . date('Y-m-d H:i', strtotime($reading['date'])) . "</td>";
            $output .= "<td>" . number_format($reading['reading1']) . " " . $meter->unit . "</td>";
            $output .= "<td>" . "Recorded By: " . $reading['username'] . $deleteIcon . "</td>";
            $output .= "</tr>";

            echo $output;
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


<script>
function readingDelete(this_id) {
  event.preventDefault();

  var readingRow = document.getElementById('reading_row_' + this_id);

  var isGood=confirm('Are you sure you want to delete this specific meter reading?  This action cannot be undone!');

	if(isGood) {
    var formData = new FormData();

    formData.append("readingUID", this_id);

    var request = new XMLHttpRequest();

    request.open("POST", "../actions/reading_delete.php", true);
    request.send(formData);

    // 4. This will be called after the response is received
    request.onload = function() {
      if (request.status != 200) { // analyze HTTP status of the response
        alert("Something went wrong.  Please refresh this page and try again.");
        alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
      } else {
        readingRow.className = 'visually-hidden';
      }
    }

    request.onerror = function() {
      alert("Request failed");
    };

    return false;
  }
}

<?php
foreach ($meter->consumptionByMonth() AS $month => $value) {
  $chartLabelsMonthly[] = "'" . date('M', strtotime($month)) . "'";
  $chartDataMonthly[] = "'" . $value . "'";
}
?>


<?php
foreach ($meter->consumptionByYear() AS $month => $value) {
  $chartLabelsYearly[] = "'" . $month . "'";
  $chartDataYearly[] = "'" . $value . "'";

  if ($month == date('Y-m')) {
    $chartDataYearlyProjection[] = "'" . $meter->getProjectedConsumptionForRemainderOfYear() . "'";
  } else {
    $chartDataYearlyProjection[] = "'0'";
  }
}
?>

<?php
foreach ($readings AS $reading) {
  $timeChartArrray["'" . $reading['date'] . "'"] = "'" . $reading['reading1'] . "'";
}

?>
var timeFormat = 'YYYY/MM/DD';

var config_meter_consumption_monthly = {
	type: 'bar',
	data: {
	   labels: [ <?php echo implode(",", $chartLabelsMonthly); ?> ],
     datasets: [{
      label: 'Monthly Consumption',
      backgroundColor: "#3CB44B30",
      borderColor: "#3CB44B",
      fill: true,
      data: [<?php echo implode(",", $chartDataMonthly); ?>]
    }]
	},
	options: {
    plugins: {
			title: {
				text: 'Monthly Consumption',
				display: false
			},
      legend: {
        display: false,
      },
		},
		scales: {
			x: {
				title: {
				  display: false
				}
			},
			y: {
				title: {
					display: true,
					text: '<?php echo $meter->unit; ?>'
				}
			}
		},
	}
};

var config_meter_consumption_yearly = {
	type: 'bar',
  data: {
      labels: [<?php echo implode(",", $chartLabelsYearly); ?>],
      datasets: [{
          label: 'Consumption by Year',
          backgroundColor: 'rgb(255, 99, 132, 0.6)',
          borderColor: 'rgb(255, 99, 132)',
          data: [<?php echo implode(",", $chartDataYearly); ?>]
      },{
          label: 'Projected Consumption',
          backgroundColor: 'rgb(255, 99, 132, 0.3)',
          borderColor: 'rgb(255, 99, 132)',
          data: [<?php echo implode(",", $chartDataYearlyProjection); ?>]
      }]
  },
	options: {
    plugins: {
			title: {
				text: 'Yearly Consumption',
				display: false
			},
      legend: {
        display: false,
      },
		},
		scales: {
			x: {
				title: {
				  display: false
				},
        stacked: true
			},
			y: {
				title: {
					display: true,
					text: '<?php echo $meter->unit; ?>'
				},
        ticks: {
            suggestedMin: <?php echo min($timeChartArrray); ?>
        },
        stacked: true
			}
		},
	}
};

var config_meter_readings = {
	type: 'line',
	data: {
	   labels: [ <?php echo implode(",", array_keys($timeChartArrray)); ?> ],
     datasets: [{
      label: 'Meter Total',
      backgroundColor: "#3CB44B30",
      borderColor: "#3CB44B",
      fill: true,
      data: [<?php //echo implode(",", $timeChartArrray); ?>]
    }]
	},
	options: {
		plugins: {
			title: {
				text: 'Meter Total',
				display: false
			},
      legend: {
        display: false,
      },
		},
		scales: {
			x: {
				type: 'time',
        min: <?php echo min(array_keys($timeChartArrray)); ?>,
				time: {
					parser: timeFormat,
					// round: 'day'
					tooltipFormat: 'll',
          scaleLabel: {
              display: false
          }
				},
				title: {
				  display: false
				}
			},
			y: {
				title: {
					display: true,
					text: '<?php echo $meter->unit; ?>'
				},
        ticks: {
            suggestedMin: <?php echo min($timeChartArrray); ?>
        },

			}
		},
	}
};

window.onload = function() {
  var chart_meter_consumption_monthly = document.getElementById('monthlyConsumption').getContext('2d');
	window.monthly = new Chart(chart_meter_consumption_monthly, config_meter_consumption_monthly);

  var chart_meter_consumption_yearly = document.getElementById('yearlyConsumption').getContext('2d');
	window.yearly = new Chart(chart_meter_consumption_yearly, config_meter_consumption_yearly);

  var chart_meter_readings = document.getElementById('meterReadings').getContext('2d');
	window.readings = new Chart(chart_meter_readings, config_meter_readings);
};



//var url = 'http://my-json-server.typicode.com/apexcharts/apexcharts.js/yearly';
var url = 'http://readings.seh.ox.ac.uk/api.php?meterUID=<?php echo $meter->uid; ?>';



function loadJSON(path, success, error) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function()
    {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                if (success)
                    success(JSON.parse(xhr.responseText));
            } else {
                if (error)
                    error(xhr);
            }
        }
    };
    xhr.open("GET", path, true);
    xhr.send();
}

loadJSON(url,
  function(data) {
    console.log(data);

    data.forEach((item, i) => {
      addData(window.readings, item.x, item.y);
    });
  }
);

function addData(chart, label, data) {
  chart.data.labels.push(label);
  chart.data.datasets.forEach((dataset) => {
    dataset.data.push(data);
  });

  chart.update();
}
</script>


<!-- Modal -->
<div class="modal" tabindex="-1" id="deleteMeterModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Meter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this meter?  This will also delete all readings for this meter (affecting past statistics).</p>
        <p>Are you sure you wouldn't rather just mark it as 'disabled'?</p>
        <p class="text-danger"><strong>WARNING!</strong> This action cannot be undone!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <a href="index.php?n=meter_edit&deleteMeterUID=<?php echo $meter->uid; ?>" role="button" class="btn btn-danger"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#refuse"/></svg> Delete</a>
      </div>
    </div>
  </div>
</div>

<div class="modal" tabindex="-1" id="projectedConsumptionModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Projected Consumption</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        $meterUnits = $meter->unit;
        $metersFirstReading = $meter->getFirstReading()['reading1'];
        $metersLastReading = $meter->getMostRecentReading()['reading1'];
        $metersTotalConsumption = $metersLastReading - $metersFirstReading;

        $daysLeftInYear = 365 - date('z');

        $metersFirstDate = date('Y-m-d', strtotime($meter->getFirstReading()['date']));
        $metersLastDate = date('Y-m-d', strtotime($meter->getMostRecentReading()['date']));
        $metersDurationSeconds = abs(strtotime($metersLastDate) - strtotime($metersFirstDate));
        $metersDurationDays = round($metersDurationSeconds / (60 * 60 * 24));
        $metersAverageConsumptionDaily = round($metersTotalConsumption / $metersDurationDays, 2);

        $projectedAdditionalConsumption = $meter->getProjectedConsumptionForRemainderOfYear();
        $projectedYearlyConsumption = $projectedAdditionalConsumption + $meter->consumptionByYear()[date('Y')];
        ?>
        <p>Projected consumption is calculated based on the difference between the meter's first and last reading (the actual total consumption for the meter), divided by the difference in these 2 readings (in days), multiplied by the remaining days in the year.</p>
        <hr />
        <p>Meter's First Reading: <?php echo number_format($metersFirstReading) . " " . $meterUnits; ?> <i>(Date: <?php echo $metersFirstDate; ?>)</i><br />
        Meter's Last Reading: <?php echo number_format($metersLastReading) . " " . $meterUnits; ?> <i>(Date: <?php echo $metersLastDate; ?>)</i></p>
        <p>Meter's Total Consumption: <?php echo number_format($metersTotalConsumption) . " " . $meterUnits; ?></p>
        <p>Duration between First/Last Reading: <?php echo $metersDurationDays; ?> days</p>
        <p>Average Consumption Per Day: <?php echo number_format($metersAverageConsumptionDaily,2) . " " . $meterUnits; ?></p>
        <p>Current Year's Consumption: <?php echo number_format($meter->consumptionByYear()[date('Y')]) . " " . $meterUnits; ?></p>
        <p>Days Left In This Year: <?php echo $daysLeftInYear; ?></p>
        <p>Projected Additional Consumption: <strong><?php echo number_format($projectedAdditionalConsumption) . " " . $meterUnits; ?></strong><br />
        Projected Yearly Consumption: <strong><?php echo number_format($projectedYearlyConsumption) . " " . $meterUnits; ?></strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
var map = L.map('map').setView([<?php echo $meter->geoLocation(); ?>], 18);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

<?php
if (isset($meter->geo)) {
  $output  = "L.marker([" . $meter->geoLocation() . "]).addTo(map)";
  $output .= ".bindPopup('" . escape($meter->name) . "')";
  $output .= ".openPopup();";

  echo $output;
}
?>
</script>
