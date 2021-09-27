<?php
include_once("../inc/include.php");

admin_gatekeeper();

$locationsClass = new locations();
$readingsClass = new readings();
$metersClass = new meters();

$nodes = $metersClass->allEnabled();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

foreach ($nodes AS $node) {
	$node = new meter($node['uid']);
	$location = new location($node->location);
	
	//printArray($node);
	
	$nodeRow['uid'] = $node->uid;
	$nodeRow['enabled'] = $node->enabled;
	$nodeRow['name'] = $node->cleanName();
	$nodeRow['location'] = $location->name;
	$nodeRow['type'] = $node->type;
	$nodeRow['unit'] = $node->unit;
	$nodeRow['photograph'] = $node->photograph;
	$nodeRow['serial'] = $node->serial;
	$nodeRow['mprn'] = $node->mprn;
	$nodeRow['billed_to_tennet'] = $node->billed;
	$nodeRow['geo'] = $node->geo;
	$nodeRow['address'] = $node->address;
	$nodeRow['supplier'] = $node->supplier;
	$nodeRow['account_no'] = $node->account_no;
	$nodeRow['retention_days'] = $node->retention_days;
	$nodeRow['reading_current_date'] = $node->mostRecentReadingDate();
	$nodeRow['reading_current_value'] = $node->mostRecentReadingValue();
	$nodeRow['reading_previous_date'] = $node->previousReadingDate();
	$nodeRow['reading_previous_value'] = $node->previousReadingValue();

	$nodeArray[] = $nodeRow;
}

$columnNames = array_keys($nodeArray[0]);

$rowOutput = null;
foreach ($columnNames AS $columnName) {
	$rowOutput[] = $columnName;
}

$csvOUTPUT[] = $rowOutput;

// Build the CSV from the bookingsArray...
foreach ($nodeArray AS $node) {
  $rowOutput = null;

  foreach ($node AS $column) {
	if (!empty($column)) {
	  $rowOutput[] = $column;
	} else {
	  $rowOutput[] = '';
	}

  }

  $csvOUTPUT[] = $rowOutput;

}

// loop over the rows, outputting them
foreach ($csvOUTPUT AS $row) {
  fputcsv($output, $row);
  //printArray($csvOUTPUT);

}
?>
