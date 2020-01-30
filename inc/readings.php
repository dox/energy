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


public function readingsByMeterByYear2($year = null) {
	global $db;
	
	if ($year == null) {
		$year = date('Y');
	}
	
	$monthNum = 1;
	
	do {
		$readings = $db->where("meter", $this->meterUID);
		$readings = $db->where("year", $year);
		$readings = $db->where("month", $monthNum);
		$readings = $db->getOne("readings_by_month");
		
		//echo "<pre>"; print_r($readings); echo "</pre>";
		
		$readingsArray[$monthNum] = $readings['reading1'];
		$monthNum++;
	} while ($monthNum <= 12);
	
	return $readingsArray;
}

public function consumptionByMeterByYear2($year = null) {
	global $db;
	
	if ($year == null) {
		$year = date('Y');
	}
	
	$readingsByMeterByYear = $this->readingsByMeterByYear2($year);
	$readingsByMeterByPreviousYear = $this->readingsByMeterByYear2($year - 1);
	
	$monthNum = 1;
	
	do {
		if ($monthNum == 1) {
			$thisMonthReading = $readingsByMeterByYear[$monthNum];
			$lastMonthReading = $readingsByMeterByPreviousYear[12];
			
			if ($lastMonthReading <= 0 || $thisMonthReading == 0) {
				$difference = 0;	
			} else {
				$difference = $thisMonthReading - $lastMonthReading;
			}
			
			
		} else {
			$thisMonthReading = $readingsByMeterByYear[$monthNum];
			$lastMonthReading = $readingsByMeterByYear[$monthNum - 1];
			
			if ($lastMonthReading <= 0 || $thisMonthReading == 0) {
				$difference = 0;	
			} else {
				$difference = $thisMonthReading - $lastMonthReading;
			}
		}
		$consumptionArray[$monthNum] = $difference;
		
		$monthNum++;
	} while ($monthNum <= 12);
	
	return $consumptionArray;
}








// THIS IS ALL GOOD>>>>>>>
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
			
			if ($previousMonthReading == 0) {
				$consumtpionArray[$arrayMonth] = 0;
			} else {
				$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;
			}
		} else {
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $readingsArray[$arrayMonth - 1];
			
			if ($previousMonthReading == 0) {
				$consumtpionArray[$arrayMonth] = 0;
			} else {
				$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;[$monthNum] = $thisMonthReading - $previousMonthReading;
			}
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
			$thisMonthReading = $readingsArray[$arrayMonth];
			
			$previousYearReadings = $db->rawQueryOne("SELECT month, type, sum(reading1) AS reading1 FROM readings_by_month WHERE location = '" . $this->locationUID . "' AND year = '" . ($year - 1) . "' AND month = '12' AND type = '" . $type . "' GROUP BY month");
			$previousMonthReading = $previousYearReadings['reading1'];
			
			if ($thisMonthReading > $previousMonthReading) {
				$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;
			}
			
		} else {
			$thisMonthReading = $readingsArray[$arrayMonth];
			
			if (isset($readingsArray[$arrayMonth - 1])) {
				$previousMonthReading = $readingsArray[$arrayMonth - 1];
			} else {
				$previousMonthReading = 0;
			}
			if ($thisMonthReading > $previousMonthReading) {
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
			
			if ($thisMonthReading > $previousMonthReading) {
				$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;
			}
		} else {
			$thisMonthReading = $readingsArray[$arrayMonth];
			$previousMonthReading = $readingsArray[$arrayMonth - 1];
			
			if ($thisMonthReading > $previousMonthReading) {
				$consumtpionArray[$arrayMonth] = $thisMonthReading - $previousMonthReading;[$monthNum] = $thisMonthReading - $previousMonthReading;
			}
		}
	}
	
	// sort array to month order (JAN first)
	ksort($consumtpionArray );
	
	return $consumtpionArray;
}

public function consumptionByMeterAllYears() {
	global $db;
	$consumtpionArray = array();
	$readings = $db->rawQuery("SELECT year, meter, MAX(reading1) AS reading1 FROM readings_by_month WHERE meter = '" . $this->meterUID . "' GROUP BY year ORDER BY year DESC;");
	
	foreach ($readings AS $reading) {
		$readingsArray[$reading['year']] = $reading['reading1'];
	}
	
	foreach ($readingsArray AS $year => $reading) {
		if (array_key_exists($year-1, $readingsArray)) {
			$consumtpionArray[$year] = $reading - $readingsArray[$year-1];
		}
	}
	
	// sort array to year order (oldest first)
	ksort($consumtpionArray);
	return $consumtpionArray;
}

} //end CLASS
?>