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
  <div class="col-md-4">
    <h3>Gas Consumption</h3>
    <canvas id="annualConsumptionGas"></canvas>
  </div>
  <div class="col-md-4">
    <h3>Electric Consumption</h3>
    <canvas id="annualConsumptionElectric"></canvas>
  </div>
  <div class="col-md-4">
    <h3>Water Consumption</h3>
    <canvas id="annualConsumptionWater"></canvas>
  </div>
</div>

<h2>Meters</h2>
<?php
echo $metersClass->meterTable($meters);
?>

<script>
<?php
$i = 0;
do {
  $lookupYear = date('Y') - $i;
  $readingsByYear_gas[$lookupYear] = $readingsClass->location_monthly_consumption($location->uid, $lookupYear, 'Gas');

  $i++;
} while ($i <= 2);
$readingsByYear_gas = array_reverse($readingsByYear_gas, true);
?>
var annualConsumptionGas = document.getElementById('annualConsumptionGas').getContext('2d');
var annualConsumptionGasChart = new Chart(annualConsumptionGas, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [<?php
        foreach ($readingsByYear_gas AS $year => $readingsYear) {
          $output  = "{";
          $output .= "label: '" . $year . "',";
          $output .= "backgroundColor: 'rgb(255, 99, 132, 0.3)',";
          $output .= "borderColor: 'rgb(255, 99, 132)',";
          $output .= "data: [" . implode(",", $readingsYear) . "]";
          $output .= "}";

          $outputArray[] = $output;
        }
        echo implode(",", $outputArray);
        $outputArray = null;
        ?>]
    },

    // Configuration options go here
    options: {}
});


<?php
$i = 0;
do {
  $lookupYear = date('Y') - $i;
  $readingsByYear_electric[$lookupYear] = $readingsClass->location_monthly_consumption($location->uid, $lookupYear, 'Electric');

  $i++;
} while ($i <= 2);
$readingsByYear_electric = array_reverse($readingsByYear_electric, true);
?>
var annualConsumptionElectric = document.getElementById('annualConsumptionElectric').getContext('2d');
var annualConsumptionElectricChart = new Chart(annualConsumptionElectric, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [<?php
        foreach ($readingsByYear_electric AS $year => $readingsYear) {
          $output  = "{";
          $output .= "label: '" . $year . "',";
          $output .= "backgroundColor: 'rgb(255, 99, 132, 0.3)',";
          $output .= "borderColor: 'rgb(255, 99, 132)',";
          $output .= "data: [" . implode(",", $readingsYear) . "]";
          $output .= "}";

          $outputArray[] = $output;
        }
        echo implode(",", $outputArray);
        $outputArray = null;
        ?>]
    },

    // Configuration options go here
    options: {}
});

<?php
$i = 0;
do {
  $lookupYear = date('Y') - $i;
  $readingsByYear_water[$lookupYear] = $readingsClass->location_monthly_consumption($location->uid, $lookupYear, 'Water');

  $i++;
} while ($i <= 2);
$readingsByYear_water = array_reverse($readingsByYear_water, true);
?>
var annualConsumptionWater = document.getElementById('annualConsumptionWater').getContext('2d');
var annualConsumptionWaterChart = new Chart(annualConsumptionWater, {
    // The type of chart we want to create
    type: 'bar',

    // The data for our dataset
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [<?php
        foreach ($readingsByYear_water AS $year => $readingsYear) {
          $output  = "{";
          $output .= "label: '" . $year . "',";
          $output .= "backgroundColor: 'rgb(255, 99, 132, 0.3)',";
          $output .= "borderColor: 'rgb(255, 99, 132)',";
          $output .= "data: [" . implode(",", $readingsYear) . "]";
          $output .= "}";

          $outputArray[] = $output;
        }
        echo implode(",", $outputArray);
        $outputArray = null;
        ?>]
    },

    // Configuration options go here
    options: {}
});

</script>
