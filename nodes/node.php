<?php
$readingsClass = new readings();
$meter = new meter($_GET['meterUID']);
$location = new location($meter->location);

if (isset($_POST['reading1']) && $_SESSION['logon'] == true) {
  $readingsClass->create($meter->uid, $_POST['reading1']);
}

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
  <div class="col-lg-8">
    <h3 class="text-center float-end"><?php echo $meter->meterTypeBadge();?></h3>

    <p>Location: <a href="index.php?n=location&locationUID=<?php echo $location->uid; ?>"><?php echo $location->name; ?></a></p>
    <p>Supplier: <?php echo $meter->displaySecurely('supplier'); ?> / Account #: <?php echo $meter->displaySecurely('account_no'); ?></p>
    <p>Serial Number: <?php echo $meter->displaySecurely('serial'); ?></p>
    <p>MPRN: <?php echo $meter->displaySecurely('mprn'); ?></p>
    <p><?php echo $meter->displayAddress(); ?></p>

    <?php
    if ($_SESSION['logon'] == true) {
    ?>
    <form class="" method="post" id="readingSubmit" action="index.php?n=node&meterUID=<?php echo $meter->uid; ?>">
      <div class="input-group">
        <input type="number" class="form-control" name="reading1" placeholder="New Reading" min="<?php echo $meter->currentReading(); ?>">
        <button type="submit" class="btn btn-secondary" name="submit">Submit</button>
      </div>
    </form>
    <div id="reading1Help" class="form-text">Previous reading: <?php echo number_format($meter->currentReading()) . " " . $meter->unit; ?></div>
    <?php } ?>

  </div>
  <div class="col-lg-4">
    <?php echo $meter->displayImage(); ?>
  </div>
</div>

<div id="map" style="width: 100%; height: 500px"></div>

<hr />

<div class="row">
  <div class="col-lg-6">
    <h2>Consumption By Month</h2>
      <canvas id="monthlyConsumption" width="100%"></canvas>
      <a id="download-monthly"
          download="monthlyConsumption.jpg"
          href=""
          class="btn btn-text float-end"
          title="Line Graph Download">
          <svg width="1em" height="1em">
            <use xlink:href="inc/icons.svg#download"/>
          </svg>
        </a>
  </div>
  <div class="col-lg-6">
    <h2>Consumption By Year</h2>
      <canvas id="yearlyConsumption" width="100%"></canvas>
      <a id="download-yearly"
          download="yearlyConsumption.jpg"
          href=""
          class="btn btn-text float-end"
          title="Line Graph Download">
          <svg width="1em" height="1em">
            <use xlink:href="inc/icons.svg#download"/>
          </svg>
        </a>
      <button type="button" class="btn btn-small btn-link" data-bs-toggle="modal" data-bs-target="#projectedConsumptionModal">How Is 'Projected Comsumption' calculated?</button>
  </div>
</div>

<hr />
<div class="row row-deck row-cards mb-3">
  <div class="col-6 col-sm-6 col-lg-3 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          Usage in last 12 months
        </div>
        <div class="h1 mb-3">
          <?php
          $totalConsumption = $meter->consumptionBetweenTwoDates(date('Y-m-d', strtotime('1 year ago')), date('Y-m-d'));

          echo number_format($totalConsumption) . " " . $meter->unit;
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-6 col-lg-3 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          ~Cost Per Unit
        </div>
        <div class="h1 mb-3">
          <?php
          $settingName = "unit_cost_" . $meter->type;

          $unitCost = $settingsClass->value($settingName);
          echo "£" . number_format($unitCost, 2);
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-6 col-lg-3 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          ~Total Cost
        </div>
        <div class="h1 mb-3">
          <?php
          $totalCost = $totalConsumption * $unitCost;

          echo "~£" . number_format($totalCost);
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-6 col-lg-3 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          Readings
        </div>
        <div class="h1 mb-3">
          <?php echo count($meter->fetchReadingsAll()); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<hr />



<div class="row">
  <div class="col-12">
    <h2 class="d-flex justify-content-between align-items-center mb-3">All Readings
      <span class="badge bg-secondary rounded-pill"><?php echo count($meter->fetchReadingsAll()); ?></span>
    </h2>

    <canvas id="meterReadings"></canvas>

    <div class="table-responsive">
      <table class="table table-striped table-sm" id="table_readings">
        <thead>
          <tr>
            <th onclick="sortTable(0, 'table_readings')">Date</th>
            <th onclick="sortTable(1, 'table_readings')">Reading</th>
            <th onclick="sortTable(2, 'table_readings')">Username</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($meter->fetchReadingsAll() AS $reading) {
            if ($_SESSION['logon'] == true) {
              $deleteIcon = "<svg width=\"1em\" height=\"1em\" onclick=\"readingDelete(" . $reading['uid'] . ")\" class=\"text-decoration-none text-muted me-1 float-end\"><use xlink:href=\"inc/icons.svg#delete\"/></svg>";
            } else {
              $deleteIcon = "";
            }

            $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\" id=\"reading_row_" . $reading['uid' ]. "\">";

            $output  = "<tr>";
            $output .= "<td>" . date('Y-m-d H:i', strtotime($reading['date'])) . "</td>";
            $output .= "<td>" . displayReading($reading['reading1']) . " " . $meter->unit . "</td>";
            $output .= "<td>" . $reading['username'] . $deleteIcon . "</td>";
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
// CONSUMPTION BY MONTH
$monthsToShow = $settingsClass->value('node_graph_monthly_display');
$dateFrom = date('Y-m-d', strtotime($monthsToShow . ' months ago'));
$dateTo = date('Y-m-d');

$monthlyConsumption = array_reverse($meter->consumptionBetweenDatesByMonth($dateFrom, $dateTo), true);
$monthlyConsumptionLabels = "'" . implode("','", array_keys($monthlyConsumption)) . "'";



// CONSUMPTION BY YEAR
$yearsToShow = $settingsClass->value('node_graph_yearly_display');
$dateFrom = date('Y') - $yearsToShow;
$dateTo = date('Y');

$yearlyConsumption = array_reverse($meter->consumptionBetweenDatesByYear($dateFrom, $dateTo), true);
$yearlyConsumptionLabels = "'" . implode("','", array_keys($yearlyConsumption)) . "'";

$i = 0;
do {
  $yearlyConsumptionProjected[] = 0;
  $i++;
} while ($i < count($yearlyConsumption)-1);
$yearlyConsumptionProjected[] = $meter->projectedConsumptionForRemainderOfYear();

// READINGS (CHEATING, I KONW - THIS NEEDS TO COME FROM THE JSON!)
foreach ($meter->fetchReadingsAll() AS $reading) {
  $timeChartArrray["'" . $reading['date'] . "'"] = "'" . $reading['reading1'] . "'";
}
?>
var timeFormat = 'YYYY/MM/DD';

var config_meter_consumption_monthly = {
	type: 'bar',
	data: {
	   labels: [ <?php echo $monthlyConsumptionLabels; ?> ],
     datasets: [{
      label: 'Monthly Consumption',
      backgroundColor: "#3CB44B30",
      borderColor: "#3CB44B",
      fill: true,
      data: [<?php echo implode(",", $monthlyConsumption); ?>]
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
      labels: [<?php echo $yearlyConsumptionLabels; ?>],
      datasets: [{
          label: 'Consumption by Year',
          backgroundColor: 'rgb(255, 99, 132, 0.6)',
          borderColor: 'rgb(255, 99, 132)',
          data: [<?php echo implode(",", $yearlyConsumption); ?>]
      },{
          label: 'Projected Consumption',
          backgroundColor: 'rgb(255, 99, 132, 0.3)',
          borderColor: 'rgb(255, 99, 132)',
          data: [<?php echo implode(",", $yearlyConsumptionProjected); ?>]
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
      fill: true
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

// create the graphs
window.onload = function() {
  var chart_meter_consumption_monthly = document.getElementById('monthlyConsumption').getContext('2d');
	window.monthly = new Chart(chart_meter_consumption_monthly, config_meter_consumption_monthly);

  var chart_meter_consumption_yearly = document.getElementById('yearlyConsumption').getContext('2d');
	window.yearly = new Chart(chart_meter_consumption_yearly, config_meter_consumption_yearly);


	//window.readings = new Chart(chart_meter_readings, config_meter_readings);

  // load the remote data into the readings graph
  var url = './api.php?action=meterread&meterUID=<?php echo $meter->uid; ?>';

  readTextFile(url, function(text){
      var jsonfile2 = JSON.parse(text);

  		var labels = jsonfile2.jsonarray.map(function(e) {
  		   return e.name;
  		});
  		var data = jsonfile2.jsonarray.map(function(e) {
  		   return e.age;
  		});

      var chart_meter_readings = document.getElementById('meterReadings').getContext('2d');
  		var config = {
  		   type: 'line',
  		   data: {
  		      labels: labels,
  		      datasets: [{
  		         label: 'Meter Total',
  		         data: data,
  		         backgroundColor: 'rgba(0, 119, 204, 0.3)',
               fill: true
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
             }
           } //end scales
         } // end options
       }; //end config

  		var chart = new Chart(chart_meter_readings, config);
  });
};
</script>


<!-- Modal -->
<div class="modal" tabindex="-1" id="deleteMeterModal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Node</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this node?  This will also delete <strong>all</strong> recorded readings for this node.</p>

        <?php
        if ($meter->enabled == 1) {
          echo "<p>Are you sure you wouldn't rather just mark it as 'disabled'?</p>";
        }
        ?>
        <p class="text-danger"><strong>WARNING!</strong> This action cannot be undone!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link link-secondary mr-auto" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" onclick="nodeDelete(this)" id="<?php echo $meter->uid; ?>"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#refuse"/></svg> Delete</button>
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

        $projectedAdditionalConsumption = $meter->projectedConsumptionForRemainderOfYear();
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

var locations = [<?php echo implode(",", $meter->geoMarker()); ?>];

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

for (var i = 0; i < locations.length; i++) {
  L.marker([locations[i][1], locations[i][2]]).addTo(map)
    .bindPopup(locations[i][0], {closeOnClick: false, autoClose: false})
    .openPopup()
}
</script>

<!--Download charts to images -->
<script>
document.getElementById("download-monthly").addEventListener('click', function(){
  /*Get image of canvas element*/
  var url_base64jp = document.getElementById("monthlyConsumption").toDataURL("image/jpg");
  /*get download button (tag: <a></a>) */
  var a =  document.getElementById("download-monthly");
  /*insert chart image url to download button (tag: <a></a>) */
  a.href = url_base64jp;
});

document.getElementById("download-yearly").addEventListener('click', function(){
  /*Get image of canvas element*/
  var url_base64jp = document.getElementById("yearlyConsumption").toDataURL("image/jpg");
  /*get download button (tag: <a></a>) */
  var a =  document.getElementById("download-yearly");
  /*insert chart image url to download button (tag: <a></a>) */
  a.href = url_base64jp;
});
</script>
