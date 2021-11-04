<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
$locationsClass = new locations();
$metersClass = new meters();

if (isset($_POST['nodes_includeHidden'])) {
  $enabled = "";
} else {
  $enabled = " AND enabled = '1'";
}

$nodes = null;
//get each site
foreach ($_POST['locations'] AS $locationUID) {
  //get each meter in this site that matches types
  $sql = "SELECT * FROM meters WHERE location = '" . $locationUID . "' " . $enabled . " AND type IN ('" . implode("','", $_POST['nodes']) . "');";
  $nodesByLocation = $db->query($sql)->fetchAll();

  foreach ($nodesByLocation AS $node) {
    $nodes[] = $node;
  }

  $nodeUnit = $node['unit'];
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#report"/></svg> Quick Filter</h1>
</div>

<form method="post" id="termUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
<div class="row">
  <div class="col-4">
    <h4>Sites</h4>
    <?php
    foreach ($locationsClass->all() AS $location) {
      $elementName = "locations[]";

      if (in_array($location['uid'], $_POST['locations'])) {
        $checked = " checked ";
      } else {
        $checked = "";
      }

      $output  = "<div class=\"form-check\">";
      $output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $location['uid'] . "\" id=\"" . $elementName . "\" name=\"" . $elementName . "\"" . $checked . ">";
      $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
      $output .= $location['name'];
      $output .= "</label>";
      $output .= "</div>";

      echo $output;
    }
    ?>
  </div>
  <div class="col-4">
    <h4>Node Types</h4>
    <?php
    foreach (explode(",", $settingsClass->value('node_types')) AS $nodeType) {
      $elementName = "nodes[]";

      if (in_array($nodeType, $_POST['nodes'])) {
        $checked = " checked ";
      } else {
        $checked = "";
      }

      if (isset($_POST['nodes_includeHidden']) && $_POST['nodes_includeHidden'] == "1") {

      }
      $output  = "<div class=\"form-check\">";
      $output .= "<input class=\"form-check-input\" type=\"radio\" value=\"" . $nodeType . "\" id=\"" . $elementName . "\" name=\"" . $elementName . "\"" . $checked . ">";
      $output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
      $output .= $nodeType . " (" . unitByType($nodeType) . ")";
      $output .= "</label>";
      $output .= "</div>";

      echo $output;
    }
    ?>
    <hr />

    <div class="form-check">
    <input class="form-check-input" type="checkbox" value="1" <?php if (isset($_POST['nodes_includeHidden']) && $_POST['nodes_includeHidden'] == "1") { echo " checked"; } ?> id="nodes_includeHidden" name="nodes_includeHidden">
    <label class="form-check-label" for="flexCheckDefault">
    Include Hidden Nodes
    </label>
    </div>
  </div>
  <div class="col-4">
    <h4>Dates</h4>
    <label for="date_from" class="form-label">Date From:</label>
    <div class="input-group">
      <span class="input-group-text" id="date_from-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="inc/icons.svg#report"/></svg></span>
      <input type="text" class="form-control" name="date_from" id="date_from" placeholder="" value="<?php echo $date_meal; ?>" aria-describedby="date_from-addon" required>
    </div>

    <label for="date_to" class="form-label">Date To:</label>
    <div class="input-group">
      <span class="input-group-text" id="date_to-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="inc/icons.svg#report"/></svg></span>
      <input type="text" class="form-control" name="date_to" id="date_to" placeholder="" value="<?php echo $date_meal; ?>" aria-describedby="date_to-addon" required>
    </div>
  </div>
</div>

<div class="d-grid gap-2">
  <button class="btn btn-primary" type="submit">Quick Filter</button>
</div>

</form>

<hr />

<div class="row">
  <div class="col-md-8 col-12">
    <canvas id="lineChartMonthlyUsage" width="100%"></canvas>
    <a id="download-line"
        download="monthlyData.jpg"
        href=""
        class="btn btn-text float-end"
        title="Line Graph Download">
        <svg width="1em" height="1em">
          <use xlink:href="inc/icons.svg#download"/>
        </svg>
      </a>
  </div>
  <div class="col-md-4 col-12">
    <canvas id="pieChartSiteUsage" width="100%"></canvas>
      <a id="download-pie"
          download="pieChart.jpg"
          href=""
          class="btn btn-text float-end"
          title="Pie Chart Download">
          <svg width="1em" height="1em">
            <use xlink:href="inc/icons.svg#download"/>
          </svg>
        </a>
  </div>
</div>

<hr />
<div class="row row-deck row-cards mb-3">
  <div class="col-6 col-sm-6 col-lg-3 mb-3">
    <div class="card">
      <div class="card-body">
        <div class="subheader">
          Total Usage
        </div>
        <div class="h1 mb-3">
          <?php
          $totalConsumption = 0;
          foreach ($nodes AS $node) {
            $node = new meter($node['uid']);
            $totalConsumption = $totalConsumption + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
          }
          echo number_format($totalConsumption) . " " . $nodeUnit;
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
          $settingName = "unit_cost_" . $_POST['nodes'][0];

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
          CO2e
        </div>
        <div class="h1 mb-3">
          <?php
          $settingName = "unit_co2e_" . $_POST['nodes'][0];

          $co2eUnit = $settingsClass->value($settingName);
          echo number_format($totalConsumption * $co2eUnit, 0) . " kg";
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<hr />

<?php

echo $metersClass->meterTable($nodes);

?>

<script>
<?php
if (isset($_POST['date_from'])) {
  $dateFrom = $_POST['date_from'];
} else {
  $dateFrom = date('Y-m-d', strtotime('1 year ago'));
}

if (isset($_POST['date_to'])) {
  $dateTo = $_POST['date_to'];
} else {
  $dateTo = date('Y-m-d');
}
?>
var date_from = flatpickr("#date_from", {
  dateFormat: "Y-m-d",
  defaultDate: ['<?php echo $dateFrom; ?>'],
  maxDate: "today"
});

var date_to = flatpickr("#date_to", {
  dateFormat: "Y-m-d",
  defaultDate: ['<?php echo $dateTo; ?>'],
  maxDate: "today"
});
</script>



<!-- Pie Chart showing type usage per location -->
<?php
foreach ($nodes AS $node) {
  $node = new meter($node['uid']);
  $location = new location($node->location);

  $data[$location->name] = $data[$location->name] + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
}

$labels = "'" . implode("','", array_keys($data)) . "'";
?>
<script>
var ctx = document.getElementById('pieChartSiteUsage').getContext('2d');
var myChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: [<?php echo $labels; ?>],
    datasets: [{
      label: '# of Votes',
      data: [<?php echo implode(",", $data); ?>],
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
      ],
      borderColor: [
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ],
      borderWidth: 1
    }]
  },
  options: {
    scales: {
      y: {
        ticks: {
          display: false
        },
        grid: {
          display: false
        },
        beginAtZero: true
      }
    }
  }
});
</script>


<!-- CONSUMPTION BY MONTH GRAPH -->
<?php
// CONSUMPTION BY MONTH
$data = array();
foreach ($nodes AS $node) {
  $node = new meter($node['uid']);

  $nodeData = $node->consumptionBetweenDatesByMonth($_POST['date_from'], $_POST['date_to']);

  foreach ($nodeData AS $monthData => $value) {
    $monthlyData[$monthData] = $monthlyData[$monthData] + $value;
  }

}
$monthlyData = array_reverse($monthlyData);
$monthlylabels = "'" . implode("','", array_keys($monthlyData)) . "'";
?>

<script>
var ctx2 = document.getElementById('lineChartMonthlyUsage').getContext('2d');
var myChart = new Chart(ctx2, {
  type: 'bar',
  data: {
    labels: [<?php echo $monthlylabels; ?>],
    datasets: [{
      label: 'Consumption Per Month',
      data: [<?php echo implode(',', $monthlyData); ?>],
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)'
      ],
      borderColor: [
        'rgba(255, 99, 132, 1)'
      ],
      borderWidth: 1
    }]
  },
  options: {
    plugins: {
      legend: {
        display: false
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        title: {
          display: true,
          text: '<?php echo $node->unit; ?>'
        }
      }
    }
  }
});
</script>


<!--Download charts to images -->
<script>
document.getElementById("download-line").addEventListener('click', function(){
  /*Get image of canvas element*/
  var url_base64jp = document.getElementById("lineChartMonthlyUsage").toDataURL("image/jpg");
  /*get download button (tag: <a></a>) */
  var a =  document.getElementById("download-line");
  /*insert chart image url to download button (tag: <a></a>) */
  a.href = url_base64jp;
});

document.getElementById("download-pie").addEventListener('click', function(){
  /*Get image of canvas element*/
  var url_base64jp = document.getElementById("pieChartSiteUsage").toDataURL("image/jpg");
  /*get download button (tag: <a></a>) */
  var a =  document.getElementById("download-pie");
  /*insert chart image url to download button (tag: <a></a>) */
  a.href = url_base64jp;
});
</script>
