<?php
$location = new locations($_GET['locationUID']);
$readingsClass = new readings();
$metersClass = new meters();
$meters = $metersClass->allByLocation($location->uid);

$title = $location->name;
$subtitle = $location->description;
$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#add\"/></svg> Add Meter", "value" => "onclick=\"location.href='index.php?n=meter_add'\"");
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"img/icons.svg#trash\"/></svg> Guest List", "value" => "onclick=\"window.open('guestlist.php?mealUID=" . $mealObject->uid . "')\"");

echo makeTitle($title, $subtitle, $icons);

?>

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
