<?php
class meters {

public $meterUID;

public function thisMeterUnits($type = null) {
	global $db;



	if ($type == null) {
		$meter = $db->where("uid", $this->meterUID);
		$meter = $db->getOne("meters");

		$meterType = $meter['type'];
	} else {
		$meterType = $type;
	}

	if ($meterType == "Electric") {
		$unit = "kWh";
	} elseif ($meterType == "Gas") {
		$unit = "m<sup>3</sup>";
	} elseif ($meterType == "Water") {
		$unit = "m<sup>3</sup>";
	} else {
		$unit = "?";
	}
	return $unit;
}

public function daysSinceLastUpdate($uid = null) {
	global $db;

	$meter = $db->where("uid", $uid);
	$meter = $db->getOne("meters");

	$lastReading = $db->where("meter", $meter['uid']);
	$lastReading = $db->orderBy("date", "DESC");
	$lastReading = $db->getOne("readings");

	if (isset($lastReading)) {
		$dt1 = date('Y-m-d');
		$dt2 = date('Y-m-d', strtotime($lastReading['date']));

		$y1 = substr($dt1,0,4);
		$m1 = substr($dt1,5,2);
		$d1 = substr($dt1,8,2);
		$h1 = substr($dt1,11,2);
		$i1 = substr($dt1,14,2);
		$s1 = substr($dt1,17,2);

		$y2 = substr($dt2,0,4);
		$m2 = substr($dt2,5,2);
		$d2 = substr($dt2,8,2);
		$h2 = substr($dt2,11,2);
		$i2 = substr($dt2,14,2);
		$s2 = substr($dt2,17,2);

		$r1=date('U',mktime($h1,$i1,$s1,$m1,$d1,$y1));
		$r2=date('U',mktime($h2,$i2,$s2,$m2,$d2,$y2));

		$differenceInSeconds = ($r1 - $r2);
		$differenceInMins = ($differenceInSeconds)/60;
		$differenceInHours = ($differenceInMins)/60;
		$differenceInDays = ($differenceInHours)/24;
	} else {
		$differenceInDays = 0;
	}


	return round($differenceInDays);
}

public function getOne() {
	global $db;

	$meter = $db->where("uid", $this->meterUID);
	$meter = $db->getOne("meters");

	return $meter;
}

public function all() {
	global $db;

	$meters = $db->orderBy('name', "DESC");
	$meters = $db->get("meters");

	return $meters;
}

public function allByLocation($locationUID = null, $type = null) {
	global $db;

	$meters = $db->where("location", $locationUID);

	if ($type != null) {
		$meters = $db->where("type", $type);
	}
	$meters = $db->orderBy('name', "DESC");
	$meters = $db->get("meters");

	return $meters;
}

public function displayMeterCard($uid = null) {
	$this->meterUID = $uid;
	$meter = $this->getOne();

	$text = "Updated " . $this->daysSinceLastUpdate($meter['uid']) . autoPluralise(" day", " days", $this->daysSinceLastUpdate($meter['uid'])) . " ago";

	if ($this->daysSinceLastUpdate($meter['uid']) > 80) {
		$lastUpdated = "<span class=\"badge badge-danger\">" . $text . "</span>";
	} else if ($this->daysSinceLastUpdate($meter['uid']) > 40) {
		$lastUpdated = "<span class=\"badge badge-warning\">" . $text . "</span>";
	} else if ($this->daysSinceLastUpdate($meter['uid']) < 30 && $this->daysSinceLastUpdate($meter['uid']) > 0) {
		$lastUpdated = "<span class=\"badge badge-primary\">" . $text . "</span>";
	}	else if ($this->daysSinceLastUpdate($meter['uid']) == 0) {
	 $lastUpdated = "<span class=\"badge badge-success\">Updated Today</span>";
 } else {
		$lastUpdated = "<span class=\"badge badge-primary\">No Readings Yet</span>";
	}

	$output  = "<div class=\"col-md-4\">";
		$output .= "<div class=\"card mb-4 shadow-sm\" >";
			if (isset($meter['photograph'])) {
				$img = "uploads/" . $meter['photograph'];
				$output .= "<img class=\"bd-placeholder-img card-img-top\" src=\"" . $img . "\" width=\"100%\" height=\"225\" alt=\"" . $meter['name'] . "\">";
			} else {
				$output .= "<svg class=\"bd-placeholder-img card-img-top\" width=\"100%\" height=\"225\" xmlns=\"http://www.w3.org/2000/svg\" preserveAspectRatio=\"xMidYMid slice\" focusable=\"false\" role=\"img\" aria-label=\"No image available\"\"><rect width=\"100%\" height=\"100%\" fill=\"#55595c\"/></svg>";
			}
			$output .= "<div class=\"card-img-overlay\">" . $lastUpdated . "</div>";
			$output .= "<div class=\"card-body\">";
				$output .= "<h5 class=\"card-title\">" . $meter['name'] . "</h5>";
				$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
					$output .= "<div class=\"btn-group\">";
						$output .= "<a href=\"index.php?n=meter&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\" aria-label=\"View " . $meter['name'] . "\">View</a>";
						$output .= "<a href=\"index.php?n=meter_edit&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\" aria-label=\"Edit " . $meter['name'] . "\">Edit</a>";
					$output .= "</div>";
					$output .= "<small class=\"text-muted\">" . $meter['type'] . " / <samp>" . $meter['serial'] . "</samp></small>";
				$output .= "</div>";


			$output .= "</div>";
		$output .= "</div>";
	$output .= "</div>";

	return $output;
}
} //end CLASS

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
?>
