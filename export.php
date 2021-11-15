<?php
include_once("inc/include.php");

if ($_GET['type'] == "node") {
	$node = new meter($_GET['filter']);
	
	$thisYearDateFrom = date('Y-m-d', strtotime('100 months ago'));
	$thisYearDateTo = date('Y-m-d');
	$consumptionLast12Months = array_reverse($node->consumptionBetweenDatesByMonth($thisYearDateFrom, $thisYearDateTo), true);
	
	echo "consumption per month in " . $node->unit;
	printArray($consumptionLast12Months);
} else {
	echo "<h2>This export hasn't been created yet.  It's on the roadmap to be developed though</h2>";
	printArray($_GET);
}
?>