<?php
$location = new locations($_GET['locationUID']);
$readingsClass = new readings();
$metersClass = new meters();
$meters = $metersClass->allByLocation($location->uid);


$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#add\"/></svg> Add Meter", "value" => "onclick=\"location.href='index.php?n=meter_add'\"");


?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2">Location: <?php echo $location->name; ?></h1>

  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="title_dropdown" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
      <ul class="dropdown-menu" aria-labelledby="title_dropdown">
        <li><a class="dropdown-item" href="index.php?n=node_add"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Add Node</a></li>
        <li><a class="dropdown-item" href="index.php?n=location_edit&locationUID=<?php echo $location->uid; ?>"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg> Edit Location</a></li>
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteMeterModal"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#refuse"/></svg> Delete Location</a></li>
      </ul>
    </div>
  </div>
</div>

<ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab" aria-controls="home" aria-selected="true">
      Electric
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#yearly" type="button" role="tab" aria-controls="profile" aria-selected="false">
      Gas Consumption
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="readings-tab" data-bs-toggle="tab" data-bs-target="#readings" type="button" role="tab" aria-controls="contact" aria-selected="false">
      Water Consumption
    </button>
  </li>
</ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
    <h3>Electric Consumption</h3>
    <canvas id="Electric_consumptionGraph"></canvas>
  </div>
  <div class="tab-pane fade" id="yearly" role="tabpanel" aria-labelledby="yearly-tab">
    <h3>Gas Consumption</h3>
    <canvas id="Gas_consumptionGraph"></canvas>
  </div>
  <div class="tab-pane fade" id="readings" role="tabpanel" aria-labelledby="readings-tab">
    <h3>Water Consumption Total</h3>
    <canvas id="Water_consumptionGraph"></canvas>
  </div>
</div>

<h2>Meters</h2>
<?php
echo $metersClass->meterTable($meters);
?>

<script>
<?php
foreach ($metersClass->types() AS $type) {
  $chartLabelsMonthly = null;
  $chartDataMonthly = null;
  foreach ($location->consumptionByMonth($type) AS $month => $value) {
    $chartLabelsMonthly[] = "'" . $month . "'";
    $chartDataMonthly[] = "'" . $value . "'";
  }

  $output  = "var " . $type . "_graph = document.getElementById('" . $type . "_consumptionGraph').getContext('2d');";
  $output .= "var annualConsumptionGasChart = new Chart(" . $type . "_graph, {";
  $output .= "    type: 'bar',";
  $output .= "    data: {";
  $output .= "        labels: [" . implode(",", $chartLabelsMonthly) . "],";
  $output .= "        datasets: [{";
  $output .= "            label: 'Consumption',";
  $output .= "            backgroundColor: 'rgb(255, 99, 132, 0.3)',";
  $output .= "            borderColor: 'rgb(255, 99, 132)',";
  $output .= "            data: [" . implode(",", $chartDataMonthly) . "]";
  $output .= "        }]";
  $output .= "    },";
  $output .= "    options: {";
    $output .= "      legend: {";
    $output .= "         display: false";
    $output .= "      }";
    $output .= "  }";
  $output .= "});";

  echo $output;
}

?>



</script>
