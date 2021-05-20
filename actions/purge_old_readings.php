<?php
include_once("../inc/include.php");

$logArray['category'] = "admin";
$logArray['type'] = "warning";
$logArray['value'] = "Purging of old readings";
$logsClass->create($logArray);

$metersClass = new meters();
$readingsClass = new readings();

$metersAll = $metersClass->all();

foreach ($metersClass->all() AS $meter) {
  if ($meter['retention_days'] != 0) {
    $readings = $readingsClass->meter_all_readings_older_than($meter['uid'], $meter['retention_days']);

    foreach ($readings AS $reading) {
      $readingsClass->delete($reading['uid']);
    }
  }
}

$logsClass->purge();
?>
