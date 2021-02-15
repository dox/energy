<?php
$locationsClass = new locations();
$readingsClass = new readings();
$meter = new meter($_GET['meterUID']);

if (isset($_POST['reading1'])) {
  $readingsClass->create($meter->uid, $_POST['reading1']);
}

$location = new location($meter->location);
$readings = $readingsClass->meter_all_readings($meter->uid);

$title = $meter->name;
$subtitle = $location->name;
if ($_SESSION['logon'] == true) {
  $icons[] = array("class" => "btn-danger", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#refuse\"/></svg> Delete Meter", "value" => "data-bs-toggle=\"modal\" data-bs-target=\"#deleteMeterModal\"");
  $icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#edit\"/></svg> Edit Meter", "value" => "onclick=\"location.href='index.php?n=meter_edit&meterUID=" . $meter->uid . "'\"");
}

echo makeTitle($title, $subtitle, $icons);
?>

<div class="row">
  <div class="col-md-8">
    <h3>Consumption by Month</h3>
    <canvas id="annualConsumption"></canvas>

    <hr class="my-4">

    <h3>Consumption by Year</h3>
    <canvas id="yearlyConsumption"></canvas>

    <hr class="my-4">

    <h3>Meter Total</h3>
    <canvas id="meterReadings"></canvas>
  </div>

  <div class="col-md-4">
    <h1 class="text-center"><?php echo $meter->meterTypeBadge();?></h1>

    <?php
    if ($_SESSION['logon'] == true) {
    ?>

    <form class="card mb-4 p-2" method="post" id="readingSubmit" action="index.php?n=meter&meterUID=<?php echo $meter->uid; ?>">
      <div class="input-group">
        <input type="text" class="form-control" name="reading1" placeholder="Reading">
        <button type="submit" class="btn btn-secondary" name="submit">Submit</button>
      </div>
    </form>
    <?php } ?>

    <?php echo $meter->displayImage(); ?>

    <h4 class="d-flex justify-content-between align-items-center mb-3">
      <span class="text-muted">Readings</span>
      <span class="badge bg-secondary rounded-pill"><?php echo count($readings); ?></span>
    </h4>

    <hr class="my-4">

    <ul class="list-group mb-3">
      <?php
      foreach ($readings AS $reading) {
        if ($_SESSION['logon'] == true) {
          $deleteIcon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#delete\"/></svg> ";
        } else {
          $deleteIcon = "";
        }

        $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
        $output .= "<div>";
        $output .= "<h6 class=\"my-0\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</h6>";
        $output .= "<small class=\"text-muted\">Recorded By: " . $reading['username'] . "</small>";
        $output .= "</div>";
        $output .= "<span class=\"text-muted\">" . number_format($reading['reading1']) . " " . $meter->unit . "</span>";
        $output .= "</li>";

        echo $output;
      }
      ?>
    </ul>
  </div>
</div>

<script>
<?php
foreach ($meter->consumptionByMonth() AS $month => $value) {
  $chartLabelsMonthly[] = "'" . $month . "'";
  $chartDataMonthly[] = "'" . $value . "'";
}
?>
var annualConsumption = document.getElementById('annualConsumption').getContext('2d');
var annualConsumptionChart = new Chart(annualConsumption, {
    // The type of chart we want to create
    type: 'line',

    // The data for our dataset
    data: {
        labels: [<?php echo implode(",", $chartLabelsMonthly); ?>],
        datasets: [{
            label: 'Consumption',
            backgroundColor: 'rgb(255, 99, 132, 0.3)',
            borderColor: 'rgb(255, 99, 132)',
            data: [<?php echo implode(",", $chartDataMonthly); ?>]
        }]
    },

    // Configuration options go here
    options: {
      legend: {
        display: false
      }
    }
});

<?php
foreach ($meter->consumptionByYear() AS $month => $value) {
  $chartLabelsYearly[] = "'" . $month . "'";
  $chartDataYearly[] = "'" . $value . "'";

  if ($month == date('Y-m')) {
    $currentValue = $value;
    $percentageThroughYear = (365 - date('z'))/100;
    $projectedConsumption = $currentValue * $percentageThroughYear;

    $chartDataYearlyProjection[] = "'" . $projectedConsumption . "'";
  } else {
    $chartDataYearlyProjection[] = "'0'";
  }
}
?>
var yearlyConsumption = document.getElementById('yearlyConsumption').getContext('2d');
var yearlyConsumptionChart = new Chart(yearlyConsumption, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
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

    // Configuration options go here
    options: {
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          stacked: true
        }],
        yAxes: [{
          stacked: true
        }]
      }
    }
});

<?php
foreach ($readings AS $reading) {
  $timeChartArrray["'" . $reading['date'] . "'"] = "'" . $reading['reading1'] . "'";
}
?>
var timeFormat = 'YYYY/MM/DD';

var meterReadings = document.getElementById('meterReadings').getContext('2d');
var meterReadingsChart = new Chart(meterReadings, {
  type: 'line',
  data: {
      labels: [<?php echo implode(",", array_keys($timeChartArrray)); ?>],
      datasets: [{
          label: '<?php echo $meter->unit; ?>',
          borderColor: "#3CB44B",
          backgroundColor: "#3CB44B30",
          fill: true,
          data: [<?php echo implode(",", $timeChartArrray); ?>]
      }]
  },
  options: {
      title: {
          text: 'Running Budget'
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
                  suggestedMin: <?php echo min($timeChartArrray); ?>
              },
              scaleLabel: {
                  display: false,
                  labelString: '<?php echo $meter->unit; ?>'
              }
          }]
      },
  }
});
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
