<?php
class site {
  protected static $table_name = "locations";

  public $uid;
  public $name;
  public $description;
  public $geo;
  




  public function highestReadingsByMonth($type = null) {
	global $db;

	$meters = $this->allNodesByType($type);

	foreach ($meters AS $meter) {
	  $meter = new meter($meter['uid']);
	  $readingsByMonth = $meter->highestReadingsByMonth();

	  foreach ($readingsByMonth AS $reading => $value) {
		$maxReading[$reading] = $value;
	  }
	}

	return $maxReading;
  }

  public function consumptionBetweenDatesByMonth($type = null, $dateFrom = null, $dateTo = null) {
	global $db;

	if ($dateFrom == null || $dateTo == null) {
	  $dateFrom = date('Y-m-d', strtotime('1 year ago'));
	  $dateTo = date('Y-m-d');
	}

	if (strtotime($dateFrom) > strtotime($dateTo)) {
	  echo "Error: DateTo cannot be larger than DateFrom";
	  quit();
	}

	$i = 0;
	do {
	  $lookupDate = date('Y-m', strtotime($dateTo . "-" . $i . " months"));

	  $consumption[$lookupDate] = $this->consumptionForMonth($type, $lookupDate);
	  $i++;

	} while (strtotime($lookupDate) > strtotime($dateFrom));

	$consumption = array_reverse($consumption);
	return $consumption;
  }

  public function consumptionForMonth($type = null, $date = null) {
	global $db;

	if ($date == null) {
	  $date = date('Y-m-d');
	}

	$previousMonthDate = date('Y-m-d', strtotime($date . " -1 month"));

	// get this month's and previous months readings
	$meters = $this->allNodesByType($type);

	$totalConsumption = 0;
	foreach ($meters AS $meter) {
	  $meter = new meter($meter['uid']);
	  $totalConsumption = $totalConsumption + $meter->consumptionForMonth($date);
	}

	// check in case the difference is a negative value (it shouldn't be!)
	if ($totalConsumption < 0) {
	  $totalConsumption = 0;
	}

	return $totalConsumption;
  }

  public function allNodes($enabledDisabled = "enabled") {
	global $db;

	if ($enabledDisabled == "all") {
	  $sqlEnabled = "";
	} else {
	  $sqlEnabled = " WHERE enabled = '1' ";
	}

	$sql  = "SELECT * FROM meters";
	$sql .= $sqlEnabled;
	$sql .= " ORDER BY uid DESC";

	$meters = $db->query($sql)->fetchAll();

	return $meters;
  }

  public function allNodesByType($type = null, $enabledDisabled = "enabled") {
	global $db;

	if ($enabledDisabled == "all") {
	  $sqlEnabled = "";
	} else {
	  $sqlEnabled = " AND enabled = '1' ";
	}

	$sql  = "SELECT * FROM meters";
	$sql .= " WHERE type = '" . $type . "' ";
	$sql .= $sqlEnabled;
	$sql .= " ORDER BY uid DESC";

	$meters = $db->query($sql)->fetchAll();

	return $meters;
  }
  
  public function co2BetweenDatesByMonth($dateFrom = null, $dateTo = null) {
	  global $db, $settingsClass;
	  
	  if ($dateFrom == null || $dateTo == null) {
			$dateFrom = date('Y-m-d', strtotime('1 year ago'));
			$dateTo = date('Y-m-d');
		}
	  
	  $totalCO2 = array();
	  
	  foreach (explode(",", $settingsClass->value('node_types')) AS $nodeType) {
		  $co2PerUnit = $settingsClass->value("unit_co2e_" . $nodeType);
		  
		  $consumptionForType = $this->consumptionBetweenDatesByMonth($nodeType, $dateFrom, $dateTo);
		  
		  foreach ($consumptionForType AS $month => $value) {
			  $consumptionForType[$month] = $value * $co2PerUnit;
		  }
		  
		  foreach ($consumptionForType AS $month => $value) {
				$totalCO2[$month] = $totalCO2[$month] + $value;
			}
		  
	  }
	  
	  return $totalCO2;
  }
}
?>