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


public function readingsByMeterByYear($year = null) {
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

public function readingsByMeterByAll() {
	global $db;

	$yearCount = 1;
	$monthNum = 1;

	$year = date('Y');

	do {
		do {
			$readings = $db->where("meter", $this->meterUID);
			$readings = $db->where("year", $year);
			$readings = $db->where("month", $monthNum);
			$readings = $db->getOne("readings_by_month");

			$readingsArray[$year . "-" . $monthNum] = $readings['reading1'];
			$monthNum++;
		} while ($monthNum <= 12);

		$year = $year + 1;
		$yearCount++;
	} while ($yearCount <= 10);

	return $readingsArray;
}

public function consumptionByMeterAll() {
	global $db;

	$readingsByMeterByYear = $this->readingsByMeterByAll();

	$monthNum = 1;

	do {
		if ($monthNum == 1) {
			$thisMonthReading = $readingsByMeterByYear[$monthNum];
			$lastMonthReading = $readingsByMeterByPreviousYear[12];
		} else {
			$thisMonthReading = $readingsByMeterByYear[$monthNum];
			$lastMonthReading = $readingsByMeterByYear[$monthNum - 1];
		}

		if ($lastMonthReading <= 0 || $thisMonthReading == 0) {
			$difference = 0;
		} else {
			$difference = $thisMonthReading - $lastMonthReading;
		}

		$consumptionArray[$monthNum] = $difference;

		$monthNum++;
	} while ($monthNum <= 12);

	return $consumptionArray;
}

public function consumptionByMeterByYear($year = null) {
	global $db;

	if ($year == null) {
		$year = date('Y');
	}

	$readingsByMeterByYear = $this->readingsByMeterByYear($year);
	$readingsByMeterByPreviousYear = $this->readingsByMeterByYear($year - 1);

	$monthNum = 1;

	do {
		if ($monthNum == 1) {
			$thisMonthReading = $readingsByMeterByYear[$monthNum];
			$lastMonthReading = $readingsByMeterByPreviousYear[12];
		} else {
			$thisMonthReading = $readingsByMeterByYear[$monthNum];
			$lastMonthReading = $readingsByMeterByYear[$monthNum - 1];
		}

		if ($lastMonthReading <= 0 || $thisMonthReading == 0) {
			$difference = 0;
		} else {
			$difference = $thisMonthReading - $lastMonthReading;
		}

		$consumptionArray[$monthNum] = $difference;

		$monthNum++;
	} while ($monthNum <= 12);

	return $consumptionArray;
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

	$readingsArray = array();
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

public function readingsByLocationByYear($year = null, $location = null, $type = null) {
	global $db;
	$consumtpionArray = array();

	$readings = $db->rawQueryOne("SELECT SUM(reading1) AS reading1 FROM (SELECT meter, type, MAX(reading1) AS reading1 FROM readings_by_month WHERE location = '" . $location . "' AND year = '" . $year . "' AND type = '" . $type . "' GROUP BY meter) s;");

	return $readings;
}

public function consumptionByLocationAllYears($location = null, $type = null) {
	$i = 0;
	$year = date('Y');

	do {
		$readingsByLocationByYear = $this->readingsByLocationByYear($year, $location, $type);
		$readingsByLocationByPreviousYear = $this->readingsByLocationByYear($year-1, $location, $type);

		if ($readingsByLocationByYear['reading1'] <= 0) {
			$consumptionArray[$year] = 0;
		} else {
			if ($readingsByLocationByYear['reading1'] - $readingsByLocationByPreviousYear['reading1'] <= 0) {
				$consumptionArray[$year] = 0;
			} else {
				$consumptionArray[$year] = $readingsByLocationByYear['reading1'] - $readingsByLocationByPreviousYear['reading1'];
			}
		}

		$year--;
		$i++;
	} while ($i < 10);

	ksort($consumptionArray);

	return $consumptionArray;
}

public function readingsBySiteByYear2($year = null, $type = null) {
	global $db;
	$consumtpionArray = array();

	$readings = $db->rawQueryOne("SELECT SUM(reading1) AS reading1 FROM (SELECT meter, type, MAX(reading1) AS reading1 FROM readings_by_month WHERE year = '" . $year . "' AND type = '" . $type . "' GROUP BY meter) s;");

	return $readings;
}

public function consumptionBySiteAllYears($type = null) {
	$i = 0;
	$year = date('Y');

	do {
		$readingsByLocationByYear = $this->readingsBySiteByYear2($year, $type);
		$readingsByLocationByPreviousYear = $this->readingsBySiteByYear2($year-1, $type);

		if ($readingsByLocationByYear['reading1'] <= 0 || $readingsByLocationByPreviousYear['reading1'] <= 0) {
			$consumptionArray[$year] = 0;
		} else {
			if ($readingsByLocationByYear['reading1'] - $readingsByLocationByPreviousYear['reading1'] <= 0) {
				$consumptionArray[$year] = 0;
			} else {
				$consumptionArray[$year] = $readingsByLocationByYear['reading1'] - $readingsByLocationByPreviousYear['reading1'];
			}
		}

		$year--;
		$i++;
	} while ($i < 10);

	ksort($consumptionArray);

	return $consumptionArray;
}

} //end CLASS
?>
