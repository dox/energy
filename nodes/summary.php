<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$metersClass = new meters;
$locations = new locations;
$locations = $locations->all();

$title = "Usage Summary";
//$subtitle = $location->name;

echo makeTitle($title, $subtitle, $icons);

$utilitiesToInclude = array("Electric", "Gas", "Water");

foreach ($metersClass->types() AS $type) {
  $icon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#" . strtolower($type) . "\"/></svg>";
  $output  = "<h1>" . $icon . " " . $type . "</h1>";
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
    $meters = $metersClass->allByLocationAndType($location['uid'], $type);
    $output .= "<tr>";

    $output .= "<th scope=\"row\">" . $location['name'] . "</th>";

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

      $output .= "<td>" . number_format($arraySum) . "</td>";
      $i--;
    } while ($i >= 0);
  }
  $output .= "<table>";

  echo $output;
}
?>
