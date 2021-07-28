<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$metersClass = new meters;
$locations = new locations;
$locations = $locations->all();

?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2">Site Summary</h1>
</div>

<?php
foreach (explode(",", $settingsClass->value('node_types')) AS $type) {
  $chartData = array();
  $chartName = "siteSummaryGraph_" . $type;

  $icon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#" . strtolower($type) . "\"/></svg>";
  $output  = "<h3>" . $icon . " " . $type . "</h3>";
  $output .= displayGraph($chartName);

  $output .= "<table class=\"table mb-5\">";
  $output .= "<thead>";
  $output .= "<tr>";
  $output .= "<th scope=\"col\" style=\"width: 400px;\"></th>";
  $i = 5;
  do {
    $year = date('Y', strtotime($i . " year ago"));
    $output .= "<th scope=\"col\">" . $year . "</th>";
    $i--;
  } while ($i >= 0);
  $output .= "</tr>";
  $output .= "</thead>";

  foreach ($locations AS $location) {
    $metersClass = new meters;
    $location = new location($location['uid']);

    $meters = $location->allNodesByType($type);
    $output .= "<tr>";

    $output .= "<th scope=\"row\">" . $location->name . "</th>";

    $i = 5;
    do {
      $previousYear = date('Y', strtotime($i + 1 . " years ago"));
      $year = date('Y', strtotime($i . " year ago"));
      $meterMAXArrayPrevious = array();
      $meterMAXArray = array();

      foreach ($meters AS $meter) {
        $meterMAXPrevious = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $previousYear . "'")->fetchAll();
        $meterMAX = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $year . "'")->fetchAll();

        if ($meterMAX[0]['reading1'] > 0 && $meterMAXPrevious[0]['reading1'] > 0) {
          $meterMAXArray[] = ($meterMAX[0]['reading1'] - $meterMAXPrevious[0]['reading1']);
        }
      }

      $arraySum = array_sum($meterMAXArray);

      $chartData[$year] = $chartData[$year] + $arraySum;

      $output .= "<td>" . number_format($arraySum) . "</td>";
      $i--;
    } while ($i >= 0);
    $output .= "</tr>";
  }
  $output .= "</table>";

  $scriptOutput .= "var timeFormat = 'YYYY/MM/DD';";
  $scriptOutput .= "var config_meter_consumption_monthly = {";
  $scriptOutput .= "type: 'bar',";
  $scriptOutput .= "data: {";
  $scriptOutput .= "labels: [ " . implode(",", array_keys($chartData)) . "],";
  $scriptOutput .= "datasets: [{";
  $scriptOutput .= "label: 'Monthly Consumption',";
  $scriptOutput .= "backgroundColor: \"#3CB44B30\",";
  $scriptOutput .= "borderColor: \"#3CB44B\",";
  $scriptOutput .= "fill: true,";
  $scriptOutput .= "data: [" . implode(",", $chartData) . "]";
  $scriptOutput .= "}]";
  $scriptOutput .= "},";
  $scriptOutput .= "options: {";
  $scriptOutput .= "plugins: {";
  $scriptOutput .= "title: {";
  $scriptOutput .= "text: 'Monthly Consumption',";
  $scriptOutput .= "display: false";
  $scriptOutput .= "},";
  $scriptOutput .= "legend: {";
  $scriptOutput .= "display: false,";
  $scriptOutput .= "},";
  $scriptOutput .= "},";
  $scriptOutput .= "scales: {";
  $scriptOutput .= "x: {";
  $scriptOutput .= "title: {";
  $scriptOutput .= "display: false";
  $scriptOutput .= "}";
  $scriptOutput .= "},";
  $scriptOutput .= "y: {";
  $scriptOutput .= "title: {";
  $scriptOutput .= "display: true,";
  $scriptOutput .= "text: '" . "Unit" . "'";
  $scriptOutput .= "}";
  $scriptOutput .= "}";
  $scriptOutput .= "},";
  $scriptOutput .= "}";
  $scriptOutput .= "};";
  $scriptOutput .= "";


  echo $output;

  echo displayGraphJC($chartName, $chartData);

}
?>

<script>
<?php
//echo $scriptOutput;
?>
</script>



<?php


?>
