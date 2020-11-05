<?php
class readings {

public $meterUID;

public function units() {
	global $db;
	
	$meter = $db->where("uid", $this->meterUID);
	$meter = $db->getOne("meters");
	
	if ($meter['type'] == "Electric") {
		$unit = "KwH";
	} elseif ($meter['type'] == "Gas") {
		$unit = "m<sup>3</sup>";
	} elseif ($meter['type'] == "Water") {
		$unit = "m<sup>3</sup>";
	} else {
		$unit = "?";
	}
	return $unit;
}

public function meterReadingsAll() {
	global $db;
	
	$readings = $db->orderBy('date', "DESC");
	$readings = $db->where("meter", $this->meterUID);
	$readings = $db->get("readings");
	
	return $readings;
}

public function siteUsageLast12Months() {
	global $db;
	
	// BUILD ARRAY OF THE LAST 12 MONTHS
	$i = 1;
	$dateArray[] = date('Y-m-d', strtotime("first day of this month"));
	do {
		$dateArray[] = date('Y-m-d', strtotime("first day of " . $i . " month ago"));
		$i++;
	} while ($i < 12);
	
	$readingsArray = array();
	
	foreach ($dateArray AS $date) {
		$returnedReading = $this->siteReadingsByMonth(date('Y',strtotime($date)), date('m',strtotime($date)));
		foreach ($returnedReading AS $reading) {
			$dateVar = date('Y-m', strtotime($reading['date']));
			
			if (array_key_exists($dateVar, $readingsArray)) {
				$readingsArray[$dateVar] = $readingsArray[$dateVar] + $reading['reading1'];
			} else {
				$readingsArray[$dateVar] = $reading['reading1'];
			}
			
		}
	}
	
	return $readingsArray;
}

public function meterReadingsByMonth($year = null, $month = null) {
	global $db;
	
	//$thisMonthNum = date('m');
	
	$readings = $db->orderBy('date', "DESC");
	$readings = $db->where("meter", $this->meterUID);
	$readings = $db->where("YEAR(date)", $year);
	$readings = $db->where("MONTH(date)", $month);
	$readings = $db->getOne("readings");
	
	return $readings;
}

public function siteReadingsByMonth($year = null, $month = null) {
	global $db;
	
	//$thisMonthNum = date('m');
	
	$readings = $db->orderBy('date', "DESC");
	$readings = $db->where("YEAR(date)", $year);
	$readings = $db->where("MONTH(date)", $month);
	$readings = $db->get("readings");
	
	return $readings;
}

public function usageLast12Months() {
	global $db;
	
	// BUILD ARRAY OF THE LAST 12 MONTHS
	$i = 1;
	$dateArray[] = date('Y-m-d', strtotime("first day of this month"));
	do {
		$dateArray[] = date('Y-m-d', strtotime("first day of " . $i . " month ago"));
		$i++;
	} while ($i < 12);
	
	foreach ($dateArray AS $date) {
		$returnedReading = $this->meterReadingsByMonth(date('Y',strtotime($date)), date('m',strtotime($date)));
		
		if ($returnedReading) {
			$readingsArray[] = $returnedReading['reading1'];
		}
	}
	
	//$readingsArray = array_reverse($readingsArray);
	
	$i=0;
	foreach ($readingsArray AS $readingArray) {
		if ($i == 0) {
			//$usageArray[] = 0;
		} else {
			$usageArray[] = $readingsArray[$i -1] -$readingsArray[$i];
		}
		$i++;
	}
	
	return $usageArray;
}

public function siteUsageLast12MonthsTotal() {
	global $db;
	
	//$thisMonthNum = date('m');
	
	$readings = $db->rawQuery("SELECT SUM(reading1) AS sumReading FROM readings");
	//$readings = $db->get("readings");
		
	return $readings;
}

public function readingsLast12Months() {
	global $db;
	
	// BUILD ARRAY OF THE LAST 12 MONTHS
	$i = 1;
	$dateArray[] = date('Y-m-d', strtotime("first day of this month"));
	do {
		$dateArray[] = date('Y-m-d', strtotime("first day of " . $i . " month ago"));
		$i++;
	} while ($i < 120);
	
	$readingsArray = array();
	
	foreach ($dateArray AS $date) {
		$returnedReading = $this->meterReadingsByMonth( date('Y',strtotime($date)), date('m',strtotime($date)));
		
		if ($returnedReading) {
			$readingsArray[] = $returnedReading;
		}
	}
	
	return $readingsArray;
}

public function usage12Months($year = null) {
	global $db;
	
	if ($year == null) {
		$year = date('Y');
	}
	
	$last12MonthsReadings = $this->readingsLast12Months();
	
	$recentReading = $last12MonthsReadings[0]['reading1'];
	
	$last12MonthsReadings = array_reverse($last12MonthsReadings);
	$oldestReading = $last12MonthsReadings[0]['reading1'];

	$difference = $recentReading - $oldestReading;
	
	return $difference . "" . $this->units();
}



public function consumptionMeterMonth($year = null, $month = null) {
	global $db;
	
	$readings = $db->where("meter", $this->meterUID);
	$reading = $db->where("YEAR(date)", $year);
	$reading = $db->where("MONTH(date)", $month);
	$reading = $db->orderBy('reading1', "DESC");
	$reading = $db->getOne("readings");
	
	return $reading;
}

public function consumptionSiteMonth($year = null, $month = null, $type = null) {
	global $db;
	//echo "Looking up site consumption for " . $year . " and month " . $month . " and type " . $type . "<br />";
	$reading = $db->rawQueryOne("SELECT SUM(reading1) AS reading1 FROM readings, meters WHERE readings.meter = meters.uid AND YEAR(date) = '$year' AND MONTH(date) = '$month' AND meters.type = '$type'");
	
	return $reading;
}

public function consumption12Months($year = null) {
	global $db;
	
	if ($year == null) {
		$year = date('Y');
	}
	
	$monthNum = 1;
	
	do {
		if ($monthNum == 1) {
			$thisMonthReading = $this->consumptionMeterMonth($year, $monthNum);
			$previousMonthReading = $this->consumptionMeterMonth($year - 1, 12);
			$consumtpion = $thisMonthReading['reading1'] - $previousMonthReading['reading1'];
		} else {
			$thisMonthReading = $this->consumptionMeterMonth($year, $monthNum);
			$previousMonthReading = $this->consumptionMeterMonth($year, $monthNum-1);
			$consumtpion = $thisMonthReading['reading1'] - $previousMonthReading['reading1'];
		}
		
		//echo $year . "-" . $monthNum;
		//echo "This: " . $thisMonthReading['reading1'] . " Last: " . $previousMonthReading['reading1'] . "<br />";
		
		// check if there is a previous meter reading!
		
		//echo $monthNum . " - " . $previousMonthReading['reading1'] . "<br />";
		
		if (isset($previousMonthReading['reading1']) && isset($thisMonthReading['reading1'])) {
			$consumtpion = $thisMonthReading['reading1'] - $previousMonthReading['reading1'];
		} else {
			$consumtpion = 0;
		}
		
		$readingsArray[$monthNum] = $consumtpion;
		$monthNum ++;
	} while ($monthNum <= 12);
	
	
	return $readingsArray;
}


public function siteConsumption12Months($year = null, $type = null) {
	global $db;
	
	if ($year == null) {
		$year = date('Y');
	}
	
	$monthNum = 1;
	
	do {
		if ($monthNum == 1) {
			$thisMonthReading = $this->consumptionSiteMonth($year, $monthNum, $type);
			$previousMonthReading = $this->consumptionSiteMonth($year - 1, 12, $type);
			$consumtpion = $thisMonthReading['reading1'] - $previousMonthReading['reading1'];
		} else {
			$thisMonthReading = $this->consumptionSiteMonth($year, $monthNum, $type);
			$previousMonthReading = $this->consumptionSiteMonth($year, $monthNum-1, $type);
			$consumtpion = $thisMonthReading['reading1'] - $previousMonthReading['reading1'];
		}
		
		if (isset($previousMonthReading['reading1']) && isset($thisMonthReading['reading1'])) {
			$consumtpion = $thisMonthReading['reading1'] - $previousMonthReading['reading1'];
		} else {
			$consumtpion = 0;
		}
		
		$readingsArray[$monthNum] = $consumtpion;
		$monthNum ++;
	} while ($monthNum <= 12);
	
	echo "<pre>";
	print_r($readingsArray);
	echo "</pre>";
	return $readingsArray;
}



} //end CLASS
?>