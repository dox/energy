<?php
admin_gatekeeper();

$location = new locations($_GET['locationUID']);


?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2">Location: Edit</h1>
</div>

<div class="row">
  <?php
  foreach ($metersClass->types() AS $type) {
    $output  = "<div class=\"col-md-4\">";
    $output .= "<h3>" . $type . " Consumption</h3>";
    $output .= "<canvas id=\"" . $type . "_consumptionGraph\"></canvas>";
    $output .= "</div>";
    $output .= "";

    echo $output;
  }
  ?>
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
