<?php

$yearsToInclude = "5";

$i = 0;
do {
	$thisYear = date("Y", strtotime($i . "years ago"));
	$lastYear = date("Y", strtotime($i + 1 . "years ago"));
	
	echo $thisYear . "<br />";
	
	$i++;	
} while ($i <= $yearsToInclude);

?>