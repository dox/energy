<?php
include_once("inc/include.php");

$type = $_GET['type'] ?? '';
$filter = $_GET['filter'] ?? '';
$output = array();

if ($type == "node") {
	$node = new node(cleanInt($filter));
	$location = new location($node->location);
	
	$thisYearDateFrom = date('Y-m-d', strtotime('100 months ago'));
	$thisYearDateTo = date('Y-m-d');
	$consumptionLast12Months = array_reverse($node->consumptionBetweenDatesByMonth($thisYearDateFrom, $thisYearDateTo), true);
	
	//echo "consumption per month in " . $node->unit;
	
	$nodeRow['uid'] = $node->uid;
	$nodeRow['enabled'] = $node->enabled;
	$nodeRow['name'] = $node->cleanName();
	$nodeRow['location'] = $location->name;
	$nodeRow['type'] = $node->type;
	$nodeRow['unit'] = $node->unit;
	$nodeRow['photograph'] = $node->photograph;
	$nodeRow['geo'] = $node->geo;
	$nodeRow['reading_current_date'] = $node->mostRecentReadingDate();
	$nodeRow['reading_current_value'] = $node->mostRecentReadingValue();
	$nodeRow['reading_previous_date'] = $node->previousReadingDate();
	$nodeRow['reading_previous_value'] = $node->previousReadingValue();
	
	$nodeRow['serial'] = showHide($node->serial);
	$nodeRow['mprn'] = showHide($node->mprn);
	$nodeRow['billed_to_tennet'] = showHide($node->billed);
	$nodeRow['supplier'] = showHide($node->supplier);
	$nodeRow['address'] = showHide($node->address);
	$nodeRow['account_no'] = showHide($node->account_no);
	$nodeRow['retention_days'] = $node->retention_days;
	
	$output[0] = $consumptionLast12Months;
	$output[1] = array_keys($nodeRow);
	$output[2] = $nodeRow;
	
} elseif ($type == "nodes") {
	$nodesClass = new nodes();
	$nodes = $nodesClass->allEnabled();
	
	foreach ($nodes AS $node) {
		$node = new node($node['uid']);
		$location = new location($node->location);
		
		$nodeRow['uid'] = $node->uid;
		$nodeRow['enabled'] = $node->enabled;
		$nodeRow['name'] = $node->cleanName();
		$nodeRow['location'] = $location->name;
		$nodeRow['type'] = $node->type;
		$nodeRow['unit'] = $node->unit;
		$nodeRow['photograph'] = $node->photograph;
		$nodeRow['geo'] = $node->geo;
		$nodeRow['reading_current_date'] = $node->mostRecentReadingDate();
		$nodeRow['reading_current_value'] = $node->mostRecentReadingValue();
		//$nodeRow['reading_previous_date'] = $node->previousReadingDate();
		//$nodeRow['reading_previous_value'] = $node->previousReadingValue();
		
		$nodeRow['serial'] = showHide($node->serial);
		$nodeRow['mprn'] = showHide($node->mprn);
		$nodeRow['billed_to_tennet'] = showHide($node->billed);
		$nodeRow['supplier'] = showHide($node->supplier);
		$nodeRow['address'] = showHide($node->address);
		$nodeRow['account_no'] = showHide($node->account_no);
		$nodeRow['retention_days'] = $node->retention_days;
		
		$nodeArray[] = $nodeRow;
	}
	
	$output = $nodeArray;
} elseif ($type == "readings") {
	$readingsClass = new readings();
	if ($filter == "all") {
		$readings = $readingsClass->all();
	} else {
		$readings = $readingsClass->node_all_readings(cleanInt($filter));
	}
	
	foreach ($readings AS $reading) {
		$node = new node($reading['node']);
		$location = new location($node->location);
		
		$nodeRow['uid'] = $reading['uid'];
		$nodeRow['date'] = $reading['date'];
		$nodeRow['node_uid'] = $reading['node'];
		$nodeRow['node_name'] = $node->cleanName();
		$nodeRow['location_uid'] = $location->uid;
		$nodeRow['location'] = $location->cleanName();
		$nodeRow['reading1'] = $reading['reading1'];
		$nodeRow['node_unit'] = $node->unit;
		$nodeRow['username'] = showHide($reading['username']);
		$nodeRow['node_type'] = $node->type;
		
		$nodeArray[] = $nodeRow;
	}
	
	$output = $nodeArray;
	
} else {
	$output[] = array('error' => "This export hasn't been created yet.  It's on the roadmap to be developed though");
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=readings_export.csv');

$outputFile = fopen('php://output', 'w');

// build headers
fputcsv($outputFile, array_keys($output[0] ?? array('error' => 'No data')));
// build rows
foreach ($output as $outputRow) {
	fputcsv($outputFile, $outputRow);
}
?>
