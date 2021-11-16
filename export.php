<?php
include_once("inc/include.php");

if ($_GET['type'] == "node") {
	$node = new node($_GET['filter']);
	
	$thisYearDateFrom = date('Y-m-d', strtotime('100 months ago'));
	$thisYearDateTo = date('Y-m-d');
	$consumptionLast12Months = array_reverse($node->consumptionBetweenDatesByMonth($thisYearDateFrom, $thisYearDateTo), true);
	
	echo "consumption per month in " . $node->unit;
	printArray($consumptionLast12Months);
} elseif ($_GET['type'] == "nodes") {
	$nodesClass = new nodes();
	$nodes = $nodesClass->all();
	
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
		$nodeRow['reading_previous_date'] = $node->previousReadingDate();
		$nodeRow['reading_previous_value'] = $node->previousReadingValue();
		
		if ($_SESSION['logon'] == true) {
			$nodeRow['serial'] = $node->serial;
			$nodeRow['mprn'] = $node->mprn;
			$nodeRow['billed_to_tennet'] = $node->billed;
			$nodeRow['supplier'] = $node->supplier;
			$nodeRow['address'] = $node->address;
			$nodeRow['account_no'] = $node->account_no;
			$nodeRow['retention_days'] = $node->retention_days;
		}
		
		$nodeArray[] = $nodeRow;
	}
	
	printArray($nodeArray);
} else {
	echo "<h2>This export hasn't been created yet.  It's on the roadmap to be developed though</h2>";
	printArray($_GET);
}
?>