<?php
class readings {

public $meterUID;
public $locationUID;

public function readingsByMeter($limit = null) {
	global $db;
	
	if ($limit == null) {
		$limit = 999;
	}
	
	$readings = $db->orderBy('date', "DESC");
	$readings = $db->where("meter", $this->meterUID);
	$readings = $db->get("readings", $limit);
	
	return $readings;
}



public function consumptionByMeterByYear($year = null) {
	global $db;
	
	if ($year == null) {
		$year = $thisYear = date('Y');
	}
	
	$readings = $db->where("meter", $this->meterUID);
	$readings = $db->where("year", $year);
	$readings = $db->orderBy('month', "DESC");
	$readings = $db->get("readings_by_month");
	
	$readingsArray = array();
	foreach ($readings AS $reading) {
		$readingsArray[$reading['month']] = $reading['reading1'];
	}
	
	$consumtpionArray = array();
	
	foreach ($readingsArray AS $arrayMonth => $reading) {
		if ($arrayMonth == 1) {
			$previousYearReadings = $db->where("meter", $this->meterUID);
			$previousYearReadings = $db->where("year", $year -1);
			$previousYearReadings = $db->where("month", 12);
			$previousYearReadings = $db->getOne("readings_by_month");
	
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $previousYearReadings['reading1'];
			
			$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;
		} else {
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $readingsArray[$arrayMonth - 1];
			
			$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;[$monthNum] = $thisMonthReading - $previousMonthReading;
		}
	}
	
	// sort array to month order (JAN first)
	ksort($consumtpionArray );
	//echo "<pre>"; print_r($consumtpionArray); echo "</pre>";
	return $consumtpionArray;
}

public function consumptionByLocationByYear($year = null, $type = null) {
	global $db;
	
	if ($year == null) {
		$year = $thisYear = date('Y');
	}
	
	$readings = $db->rawQuery("SELECT month, type, sum(reading1) AS reading1 FROM readings_by_month WHERE location = '" . $this->locationUID . "' AND year = '" . $year . "' AND type = '" . $type . "' GROUP BY month");
	
	//echo "<pre>"; print_r($readings); echo "</pre>";
	
	$readingsArray = array();
	foreach ($readings AS $reading) {
		$readingsArray[$reading['month']] = $reading['reading1'];
	}
	
	$consumtpionArray = array();
	
	foreach ($readingsArray AS $arrayMonth => $reading) {
		if ($arrayMonth == 1) {
			$previousYearReadings = $db->rawQueryOne("SELECT month, type, sum(reading1) AS reading1 FROM readings_by_month WHERE location = '" . $this->locationUID . "' AND year = '" . ($year - 1) . "' AND month = '12' AND type = '" . $type . "' GROUP BY month");
			
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $previousYearReadings['reading1'];
			if ($previousMonthReading) {
				$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;
			}
			
		} else {
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $readingsArray[$arrayMonth - 1];
			if ($previousMonthReading > 0) {
				$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;[$monthNum] = $thisMonthReading - $previousMonthReading;
			}
		}
	}
	
	// sort array to month order (JAN first)
	ksort($consumtpionArray );
	
	return $consumtpionArray;
}

public function consumptionBySiteByYear($year = null, $type = null) {
	global $db;
	
	if ($year == null) {
		$year = $thisYear = date('Y');
	}
	
	$readings = $db->rawQuery("SELECT month, type, sum(reading1) AS reading1 FROM readings_by_month WHERE year = '" . $year . "' AND type = '" . $type . "' GROUP BY month");
	
	//echo "<pre>"; print_r($readings); echo "</pre>";
	
	$readingsArray = array();
	foreach ($readings AS $reading) {
		$readingsArray[$reading['month']] = $reading['reading1'];
	}
	
	$consumtpionArray = array();
	
	foreach ($readingsArray AS $arrayMonth => $reading) {
		if ($arrayMonth == 1) {
			$previousYearReadings = $db->rawQueryOne("SELECT month, type, sum(reading1) AS reading1 FROM readings_by_month WHERE year = '" . ($year - 1) . "' AND month = '12' AND type = '" . $type . "' GROUP BY month");
			
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $previousYearReadings['reading1'];
			
			$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;
		} else {
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $readingsArray[$arrayMonth - 1];
			
			$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;[$monthNum] = $thisMonthReading - $previousMonthReading;
		}
	}
	
	// sort array to month order (JAN first)
	ksort($consumtpionArray );
	
	return $consumtpionArray;
}

} //end CLASS
?>