<?php
// API usage:
// meterUID

include_once("inc/include.php");

$meter_uid = $_GET['meterUID'];

$readingsClass = new readings();

$meter = new meter($meter_uid);
$readings = $readingsClass->meter_all_readings($meter->uid);

foreach ($readings AS $reading) {
  $label = date('r', strtotime($reading['date']));
  $value = $reading['reading1'];

  $timeChartArray[] = array('name' => $label, 'age' => $value);
}
$timeChartArray = array_reverse($timeChartArray);

$timeChartArray = array('jsonarray' => $timeChartArray);
echo json_encode($timeChartArray);
?>
