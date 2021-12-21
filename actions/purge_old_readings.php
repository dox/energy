<?php
include_once("../inc/include.php");
admin_gatekeeper();

$logArray['category'] = "admin";
$logArray['type'] = "warning";
$logArray['value'] = "Purging of old readings";
$logsClass->create($logArray);

$nodesClass = new nodes();
$readingsClass = new readings();

foreach ($nodesClass->all() AS $node) {
  if ($node['retention_days'] != 0) {
    $readings = $readingsClass->node_all_readings_older_than($node['uid'], $node['retention_days']);

    foreach ($readings AS $reading) {
      $readingsClass->delete($reading['uid']);
    }
  }
}

$logsClass->purge();
?>
