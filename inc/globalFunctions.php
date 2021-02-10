<?php
function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
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

function makeTitle($title = null, $subtitle = nulll, $iconsArray = null) {
	$output  = "<div class=\"px-3 py-3 pt-md-5 pb-md-4 text-center\">";
	$output .= "<h1 class=\"display-4\">" . $title . "</h1>";

	if ($subtitle != null) {
		$output .= "<p class=\"lead\">" . $subtitle . "</p>";
	}

	$output .= "</div>";

	$output .= "<div class=\"pb-3 text-end\">";
	foreach ($iconsArray AS $icon) {
		$output .= "<button type=\"button\" class=\"btn btn-sm ms-1 " . $icon['class'] . "\"" . $icon['value'] . ">";
		$output .= $icon['name'];
		$output .= "</button>";
	}
	$output .= "</div>";

	return $output;
}

function admin_gatekeeper() {
	if ($_SESSION['logon'] != true) {
		//global $logsClass;
		//$logsClass->create("view_fail", "Page view for " . $_SERVER['REQUEST_URI'] . " failed");

		header("Location: http://" . $_SERVER['SERVER_NAME'] . "/index.php?n=logon");
	  exit;
	}

	function dateDisplay($date = null, $time = false) {
		if ($time) {
			$dateFormat = "Y-m-d H:i:s";
		} else {
			$dateFormat = "Y-m-d";
		}

		$returnDate = date($dateFormat, strtotime($date));

		return $returnDate;
	}
}
?>
