<?php
// API usage:
// action [readingadd / meterread] * required
// meterUID [numeric]

include_once("inc/include.php");

if ($_GET['action'] == "readingadd") {
  $logArray['category'] = "api";
  $logArray['type'] = "info";
  $logArray['value'] = "API called for 'readingadd' for [meterUID:" . $_GET['meterUID'] . "] with reading value '" . $_GET['reading'] . "'";
  $logsClass->create($logArray);

  if (isset($_GET['reading']) && isset($_GET['meterUID'])) {
    $readingsClass = new readings();

    $readingsClass->create($_GET['meterUID'], $_GET['reading']);
  }

} elseif ($_GET['action'] == "meterread") {
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
} else {

}


?>
