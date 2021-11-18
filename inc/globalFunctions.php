<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function displayReading($reading = null) {
	if (is_numeric( $reading ) && floor( $reading ) != $reading) {
		$returnReading = $reading;
	} else {
		$returnReading = number_format($reading);
	}
	return $returnReading;
}

function escape($var) {
	$var=stripslashes($var);
	$var=htmlentities($var);
	$var=strip_tags($var);
	$var=str_replace("'", "\'", $var);

	return $var;
}

function autoPluralise ($singular, $plural, $count = 1) {
	// fantasticly clever function to return the correct plural of a word/count combo
	// Usage:	$singular	= single version of the word (e.g. 'Bus')
	//       	$plural 	= plural version of the word (e.g. 'Busses')
	//			$count		= the number you wish to work out the plural from (e.g. 2)
	// Return:	the singular or plural word, based on the count (e.g. 'Jobs')
	// Example:	autoPluralise("Bus", "Busses", 3)  -  would return "Busses"
	//			autoPluralise("Bus", "Busses", 1)  -  would return "Bus"

	return ($count == 1)? $singular : $plural;
} // END function autoPluralise

function admin_gatekeeper() {
	if ($_SESSION['logon'] != true) {
		//$_SESSION['last_node_access'] = $_GET['n'];
		//global $logsClass;
		//$logsClass->create("view_fail", "Page view for " . $_SERVER['REQUEST_URI'] . " failed");

		include_once("nodes/logon.php");
		include_once("views/footer.php");
	  exit;
	}
}

function dateDisplay($date = null, $longFormat = false) {
	global $settingsClass;

	if ($longFormat == true) {
		$dateFormat = $settingsClass->value('datetime_format_long');
	} else {
		$dateFormat = $settingsClass->value('datetime_format_short');
	}

	$returnDate = date($dateFormat, strtotime($date));

	return $returnDate;
}

function howLongAgo($time = false) {
	if ($time != false) {
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = time();

		$difference			= $now - strtotime($time);
		$tense					= "ago";

		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if($difference != 1) {
			$periods[$j].= "s";
		}

		$return = "$difference $periods[$j] ago";
	} else {
		return "No readings";
	}
	return $return;
}

function displayGraph($chartID = null) {
	$graphOutput  = "<canvas id=\"" . $chartID . "\" width=\"100\" height=\"30\"></canvas>";
	$graphOutput .= "";

	return $graphOutput;
}

function showHide($string = null) {
	if ($_SESSION['logon'] == true) {
		$returnString = $string;
	} else {
		$returnString = "***";
	}
	
	return $returnString;
}

function unitByType($type = null) {
	if ($type == "Gas") {
		$unitName = "m³";
	} elseif ($type == "Water") {
		$unitName = "m³";
	} elseif ($type == "Electric") {
		$unitName = "kWh";
	} elseif ($type == "Refuse") {
		$unitName = "m³";
	} else {
		$unitName = "Unknown";
	}
	
	return $unitName;
}
?>
