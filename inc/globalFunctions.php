<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function averagePerDay($array) {
	$firstValue = reset($array);
	$firstDate = array_key_first($array);
	
	$lastValue = end($array);
	$lastDate = array_key_last($array);
	
	$diff = abs(strtotime($firstDate) - strtotime($lastDate));
	$days = $diff / (60*60*24);
	
	if ($firstValue > 0 && $lastValue > 0 && $days > 0) {
		$changePerDayAvg = ($firstValue - $lastValue) / $days;
	} else {
		$changePerDayAvg = 0;
	}
	
	return $changePerDayAvg;
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

function percentageDifference($num1, $num2) {
	if (isset($num1) && isset($num2) && $num1 > 0 && $num2 > 0 ) {
		$percentage = ($num1 - $num2) / (($num1 + $num2)/2);
		$percentage = number_format($percentage * 100, 2);
	} else {
		$percentage = 0;
	}
	
	return $percentage;
}

function autoPluralise ($singular, $plural, $count = 1) {
	// fantastically clever function to return the correct plural of a word/count combo
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

function pageHeader($title = null, $actionsArray = null) {
	$output  = "<h1 class=\"d-flex mb-5 justify-content-between align-items-center\">" . $title;
	$output .= "<div class=\"dropdown\">";
	
	if (isset($actionsArray)) {
		$output .= "<button class=\"btn btn-sm btn-outline-info dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">Actions</button>";
		$output .= "<div class=\"dropdown-menu dashboard-dropdown\">";
		
		foreach ($actionsArray AS $action) {
			if ($action['name'] == "separator") {
				$output .= "<div role=\"separator\" class=\"dropdown-divider my-1\"></div>";
			} else {
				if (isset($action['data-bs-target'])) {
					$output .= "<a class=\"dropdown-item me-2 " . $action['class'] . "\" href=\"#\" data-bs-toggle=\"modal\" data-bs-target=\"" . $action['data-bs-target'] . "\">";
					$output .= "<span class=\"sidebar-icon\">";
					$output .= "<svg class=\"dropdown-icon me-2 \" width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#" . $action['icon'] . "\"/></svg>";
					$output .= "</span> " . $action['name'];
					$output .= "</a>";
				} else {
					$output .= "<a class=\"dropdown-item me-2 " . $action['class'] . "\" href=\"" . $action['href'] . "\">";
					$output .= "<span class=\"sidebar-icon\">";
					$output .= "<svg class=\"dropdown-icon me-2 \" width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#" . $action['icon'] . "\"/></svg>";
					$output .= "</span> " . $action['name'];
					$output .= "</a>";
				}
			}
		}
		
		$output .= "</div>";
	}
	
	$output .= "</div>";
	$output .= "</h1>";
	
	return $output;
}

function convertm3TokWh($m3 = 0) {
	global $settingsClass;
	
	$calorificValue = $settingsClass->value("gas_calorific_value");
	
	if (!isset($calorificValue)) {
		$calorificValue = "38";
	}
	
	$correctionFactor = "1.02264";
	$kWhCorrectionFactor = "3.6";
	
	$kWh = (($m3 + $calorificValue) * $correctionFactor) / $kWhCorrectionFactor;
	
	$kWh = number_format($kWh, 0);
	
	return $kWh;
}
?>
