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
      $output .= $nodeType;
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

<h3><?php echo count($nodes); ?> Nodes</h3>

<div class="row">
  <div class="col-md-8 col-12">
    <canvas id="lineChartMonthlyUsage" width="100%"></canvas>
  </div>
  <div class="col-md-4 col-12">
    <canvas id="pieChartSiteUsage" width="100%"></canvas>
  </div>
</div>

<hr />

<?php

echo $metersClass->meterTable($nodes);

?>


<?php

foreach ($_POST['nodes'] AS $nodeType) {
  $output  = "<p>graph for " . $nodeType . "</p>";

  //echo $output;
}


//printArray($nodes); ?>

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
<script>
<?php
foreach ($nodes AS $node) {
  $node = new meter($node['uid']);
  $location = new location($node->location);

  $data[$location->name] = $data[$location->name] + $node->consumptionBetweenDates($_POST['date_from'], $_POST['date_to']);
}

$labels = "'" . implode("','", array_keys($data)) . "'";
?>
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
$data = null;
$data2 = null;
foreach ($nodes AS $node) {
  $node = new meter($node['uid']);

  //$data[$node->type] = $data[$node->type] + $node->consumptionBetweenDates($_POST['date_from'], $_POST['date_to']);

  $startingDate = $_POST['date_to'];

  $i = 0;
  do {
    $lookupDate = date('Y-m-d', strtotime($i . " months ago"));
    $startOfMonth = date('Y-m-01', strtotime($i . " months ago"));
    $endOfMonth = date('Y-m-t', strtotime($i . " months ago"));

    $data2[$startOfMonth] = $data2[$startOfMonth] + $node->consumptionBetweenDates($endOfMonth, $startOfMonth);

    $i++;
  } while ($lookupDate > date('Y-m', strtotime($_POST['date_from'])));

  //printArray($data2);

  foreach ($node->consumptionByMonth() AS $month => $value) {
    $yearMonth = date('M Y', strtotime($month));

    //$chartLabelsMonthly[] = "'" . $yearMonth . "'";
    $data[$yearMonth] = $data[$yearMonth] + $value;
  }
}

$labels = "'" . implode("','", array_keys($data)) . "'";
//printArray($chartDataMonthly);
?>

<script>
var ctx2 = document.getElementById('lineChartMonthlyUsage').getContext('2d');
var myChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [<?php echo $labels; ?>],
        datasets: [{
            label: 'Consumption over last 12 months',
            data: [<?php echo implode(',', $data); ?>],
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
