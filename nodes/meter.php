<?php
$locationsClass = new locations();
$readingsClass = new readings();
$meter = new meter($_GET['meterUID']);

if (isset($_POST['reading1'])) {
  $readingsClass->create($meter->uid, $_POST['reading1']);
}

$location = new location($meter->location);
$readings = $readingsClass->allByMeter($meter->uid);
?>

<div class="container">
  <h1 class="float-right"><?php echo $meter->meterTypeBadge();?></h1>
  <h1><?php echo $meter->name;?> <small class="text-muted"><?php echo $location->name; ?></small></h1>

  <main>
    <div class="row">
    <div class="col-md-8">
      <h3>Consumption by Month</h3>
      <canvas id="annualConsumption"></canvas>
      <hr class="my-4">

      <h3>Consumption by Year (these calculations are not correct yet!)</h3>
      <canvas id="yearlyConsumption"></canvas>
      <hr class="my-4">

      <h3>Meter Total</h3>
      <canvas id="meterReadings"></canvas>
    </div>

    <div class="col-md-4">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted">Readings</span>
          <span class="badge bg-secondary rounded-pill"><?php echo count($readings); ?></span>
        </h4>

        <?php
        if ($_SESSION['logon'] == true) {
          $class = "";
        } else {
          $class = " disabled";
        }
        ?>
        <form class="card p-2" method="post" id="readingSubmit" action="index.php?n=meter&meterUID=<?php echo $meter->uid; ?>">
          <div class="input-group">
            <input type="text" class="form-control" <?php echo $class; ?> name="reading1" placeholder="Reading">
            <button type="submit" class="btn btn-secondary" <?php echo $class; ?> name="submit">Submit</button>
          </div>
        </form>

        <hr class="my-4">

        <ul class="list-group mb-3">
          <?php
          foreach ($readings AS $reading) {
            $output  = "<li class=\"list-group-item d-flex justify-content-between lh-sm\">";
            $output .= "<div>";
            $output .= "<h6 class=\"my-0\">" . $reading['date'] . "</h6>";
            $output .= "<small class=\"text-muted\">Brief description</small>";
            $output .= "</div>";
            $output .= "<span class=\"text-muted\">" . $reading['reading1'] . " " . $meter->unit . "</span>";
            $output .= "</li>";

            echo $output;
          }
          ?>
        </ul>

        <?php echo $meter->image(); ?>
    </div>
  </div>
  </main>
</div>

<script>
<?php
$thisYear = date('Y');
$i = 0;
do {
  $lookupYear = $thisYear - $i;

  $results = $readingsClass->consumption_monthly($meter->uid, $lookupYear);
  $readingsByYearArray[$lookupYear] =  $results[$lookupYear];

  $i++;
} while ($i <= years);
$readingsByYearArray = array_reverse($readingsByYearArray, true);
?>
var annualConsumption = document.getElementById('annualConsumption').getContext('2d');
var annualConsumptionChart = new Chart(annualConsumption, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [<?php
        foreach ($readingsByYearArray AS $year => $readingsYear) {
          $output  = "{";
          $output .= "label: '" . $year . "',";
          $output .= "backgroundColor: 'rgb(255, 99, 132, 0.3)',";
          $output .= "borderColor: 'rgb(255, 99, 132)',";
          $output .= "data: [" . implode(",", $readingsYear) . "]";
          $output .= "}";

          $outputArray[] = $output;
        }
        echo implode(",", $outputArray);
        ?>]
    },

    // Configuration options go here
    options: {}
});

<?php
foreach ($readingsByYearArray AS $year => $monthlyReadings) {
  $yearlyTotal["'". $year . "'"] = array_sum($monthlyReadings);
}
?>
var yearlyConsumption = document.getElementById('yearlyConsumption').getContext('2d');
var yearlyConsumptionChart = new Chart(yearlyConsumption, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: [<?php echo implode(",", array_keys($yearlyTotal)); ?>],
        datasets: [{
            label: 'Consumption by Year',
            backgroundColor: 'rgb(255, 99, 132, 0.3)',
            borderColor: 'rgb(255, 99, 132)',
            data: [<?php echo implode(",", $yearlyTotal); ?>]
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
                  suggestedMin: 0
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
