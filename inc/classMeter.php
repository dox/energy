<?php
class meter {
  protected static $table_name = "meters";

  public $uid;
  public $name;
  public $location;
  public $type;
  public $unit;
  public $photograph;
  public $serial;
  public $mprn;
  public $billed;
  public $enabled;
  public $geo;
  public $address;
  public $supplier;
  public $account_no;

  function __construct($meterUID = null) {

    global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = '" . $meterUID . "'";
		$meter = $db->query($sql)->fetchArray();

		foreach ($meter AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function currentReading() {
    global $db;

    $sql  = "SELECT reading1 FROM readings";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $db->query($sql)->fetchAll()[0];
    $lastReading = $lastReading['reading1'];

    // check for no value at all (in which case, default to 0)
    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      $return = 0;
    }

    return $return;
  }

  public function consumptionForMonth($date = null) {
    global $db;

    if ($date == null) {
      $date = date('Y-m-d');
    }

    $previousMonthDate = date('Y-m-d', strtotime($date . " -1 month"));

    // get this month's and previous months readings
    $thisMonthReading = $this->readingForMonth($date);
    $previousMonthReading = $this->readingForMonth($previousMonthDate);

    // check if there is actually a reading for this/previous month
    if ($thisMonthReading == 0 || $previousMonthReading == 0) {
      $difference = 0;
    } else {
      // the difference between the 2 readings is the consumption
      $difference = $thisMonthReading - $previousMonthReading;
    }

    // check in case the difference is a negative value (it shouldn't be!)
    if ($difference < 0) {
      $difference = 0;
    }

    return $difference;
  }

  public function consumptionForYear($year = null) {
    global $db;

    if ($year == null) {
      $year = date('Y');
    }

    // get this month's and previous months readings
    $thisYearReading = $this->readingForYear($year);
    $previousYearReading = $this->readingForYear($year - 1);

    // check if there is actually a reading for this/previous month
    if ($thisYearReading == 0 || $previousYearReading == 0) {
      $difference = 0;
    } else {
      // the difference between the 2 readings is the consumption
      $difference = $thisYearReading - $previousYearReading;
    }

    // check in case the difference is a negative value (it shouldn't be!)
    if ($difference < 0) {
      $difference = 0;
    }

    return $difference;
  }

  public function readingForMonth($date = null) {
    global $db;

    if ($date == null) {
      $date = date('Y-m-d');
    }

    $sql  = "SELECT reading1 FROM readings";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " AND YEAR(date) = '" . date('Y', strtotime($date)) . "'";
    $sql .= " AND MONTH(date) = '" . date('m', strtotime($date)) . "'";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $db->query($sql)->fetchAll()[0];
    $lastReading = $lastReading['reading1'];

    // check for no value at all (in which case, default to 0)
    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      $return = 0;
    }

    return $return;
  }

  public function readingForYear($year = null) {
    global $db;

    if ($year == null) {
      $year = date('Y');
    }

    $sql  = "SELECT reading1 FROM readings";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " AND YEAR(date) = '" . $year . "'";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $db->query($sql)->fetchAll()[0];
    $lastReading = $lastReading['reading1'];

    // check for no value at all (in which case, default to 0)
    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      $return = 0;
    }

    return $return;
  }

  public function consumptionBetweenTwoDates($dateFrom = null, $dateTo = null) {
    global $db;

    $dateFromSQL = "SELECT reading1 FROM readings WHERE meter = '" . $this->uid . "' AND DATE(date) >= '" . $dateFrom . "' ORDER BY date ASC LIMIT 1";
    $dateFromReading = $db->query($dateFromSQL)->fetchAll()[0]['reading1'];

    $dateToSQL = "SELECT reading1 FROM readings WHERE meter = '" . $this->uid . "' AND DATE(date) <= '" . $dateTo . "' ORDER BY date DESC LIMIT 1";
    $dateToReading = $db->query($dateToSQL)->fetchAll()[0]['reading1'];

    $difference = $dateToReading - $dateFromReading;

    if ($difference < 0) {
      $difference = 0;
    }

    return $difference;
  }

  public function consumptionBetweenDatesByMonth($dateFrom = null, $dateTo = null) {
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

      $consumption[$lookupDate] = $this->consumptionForMonth($lookupDate);
      $i++;

    } while (strtotime($lookupDate) > strtotime($dateFrom));

    return $consumption;
  }

  public function consumptionBetweenDatesByYear($dateFrom = null, $dateTo = null) {
    global $db;

    if ($dateFrom == null || $dateTo == null) {
      $dateFrom = date('Y', strtotime('5 years ago'));
      $dateTo = date('Y');
    }

    if (strtotime($dateFrom) > strtotime($dateTo)) {
      echo "Error: DateTo cannot be larger than DateFrom";
      quit();
    }

    $i = 0;
    do {
      $lookupDate = $dateTo - $i;

      $consumption[$lookupDate] = $this->consumptionForYear($lookupDate);
      $i++;

    } while (strtotime($lookupDate) > strtotime($dateFrom));

    return $consumption;
  }

  public function projectedConsumptionForRemainderOfYear() {
    global $db;

    $metersFirstReading = $this->getFirstReading()['reading1'];
    $metersLastReading = $this->getMostRecentReading()['reading1'];
    $metersTotalConsumption = $metersLastReading - $metersFirstReading;

    $daysLeftInYear = 365 - date('z');

    $metersFirstDate = date('Y-m-d', strtotime($this->getFirstReading()['date']));
    $metersLastDate = date('Y-m-d', strtotime($this->getMostRecentReading()['date']));
    $metersDurationSeconds = abs(strtotime($metersLastDate) - strtotime($metersFirstDate));
    $metersDurationDays = round($metersDurationSeconds / (60 * 60 * 24));
    $metersAverageConsumptionDaily = round($metersTotalConsumption / $metersDurationDays, 2);

    $projectedConsumption = $metersAverageConsumptionDaily * $daysLeftInYear;
    //$projectedAdditionalConsumption = $projectedConsumption - $this->consumptionByYear()[date('Y')];

    return $projectedConsumption;
  }

  public function displaySecurely($field = null) {
    if ($_SESSION['logon'] == true) {
      $return = $this->$field;
    } else {
      $return = "*******";
    }

    return $return;
  }

  public function meterTypeBadge() {
    if ($this->type == "Gas") {
  		$class = "bg-primary";
      $iconSymbol = "gas";
      //$icon = "<img src=\"/inc/icons/bootstrap.svg\" alt=\"\" width=\"32\" height=\"32\" title=\"Bootstrap\">";
  	} elseif ($this->type == "Electric") {
  		$class = "bg-warning";
      $iconSymbol = "electric";
  	} elseif ($this->type == "Water") {
  		$class = "bg-info";
      $iconSymbol = "water";
  	} elseif ($this->type == "Refuse") {
      $class = "bg-dark";
      $iconSymbol = "refuse";
    } elseif ($this->type == "Temperature") {
      $class = "bg-danger";
      $iconSymbol = "temperature";
    } else {
  		$class = "bg-light";
  	}
    $icon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#" . $iconSymbol . "\"/></svg>";
  	$output = "<span class=\"badge rounded-pill w-100 " . $class . "\">" . $icon . " " . $this->type . "</span>";

    return $output;
  }

  public function displayImage() {
    if (isset($this->photograph)) {
      $output = "<img src=\"uploads/" . $this->photograph . "\" class=\"rounded img-fluid w-100 mb-4\" alt=\"Image of utlity meter\">";
    } else {
      $output = "";
    }

    return $output;
  }

  public function update($array = null) {
    global $db, $logsClass;

    $sql  = "UPDATE " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      if ($updateItem != 'uid') {
        $value = str_replace("'", "\'", $value);
        $sqlUpdate[] = $updateItem ." = '" . $value . "' ";
      }
    }

    $sql .= " SET " . implode(", ", $sqlUpdate);
    $sql .= " WHERE uid = '" . $this->uid . "' ";
    $sql .= " LIMIT 1";

    $update = $db->query($sql);

    $logArray['category'] = "meter";
    $logArray['type'] = "success";
    $logArray['value'] = "[meterUID:" . $this->uid . "] updated successfully";
    $logsClass->create($logArray);

    return $update;
  }

  public function delete() {
    global $db, $logsClass;

    $thisUID = $this->uid;

    $sql1  = "DELETE FROM readings";
    $sql1 .= " WHERE meter = '" . $this->uid . "'";

    $deleteReadings = $db->query($sql1);

    $this->deleteImage();

    $sql2  = "DELETE FROM " . self::$table_name;
    $sql2 .= " WHERE uid = '" . $this->uid . "' ";
    $sql2 .= " LIMIT 1";

    $deleteMeter = $db->query($sql2);

    $logArray['category'] = "meter";
    $logArray['type'] = "warning";
    $logArray['value'] = "[meterUID:" . $thisUID . "] deleted successfully";
    $logsClass->create($logArray);

    return $deleteMeter;
  }

  public function geoLocation() {
    global $settingsClass;

    if (isset($this->geo) && !empty($this->geo) && $this->geo != null) {
      $geoReturn = $this->geo;
    } else {
      $geoReturn = $settingsClass->value('site_geolocation');
    }

    return $geoReturn;
  }

  public function deleteImage() {
    global $logsClass;

    if (isset($this->photograph)) {
      $file = $_SERVER["DOCUMENT_ROOT"] . "/uploads/" . $this->photograph;

      if (file_exists($file)) {
        unlink($file);

        $logArray['category'] = "file";
    		$logArray['type'] = "success";
    		$logArray['value'] = $file . " file deleted successfully from [meterUID:" . $this->uid . "]";
    		$logsClass->create($logArray);
      } else {
        $logArray['category'] = "file";
    		$logArray['type'] = "error";
    		$logArray['value'] = $file . " file did not exist to delete from [meterUID:" . $this->uid . "]";
    		$logsClass->create($logArray);
      }
    }
  }

  public function getMostRecentReading() {
    global $db;

    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $recentReading = $db->query($sql)->fetchAll()[0];

    return $recentReading;
  }

  public function mostRecentReadingDate() {
    $date = $this->getMostRecentReading()['date'];
    if (isset($date)) {
      return $date;
    } else {
      return false;
    }
  }

  public function mostRecentReadingValue() {
    return $this->getMostRecentReading()['reading1'];
  }

  // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  //
  // everything below this line needs going over
  //
  // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
















  public function highestReadingForMonth($date = null) {
    global $db;

    if ($date == null) {
  		$date = date('Y-m');
  	} else {
      $date = date('Y-m', strtotime($date));
    }

    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " AND YEAR(date) = '" . date('Y', strtotime($date)) . "' ";
    $sql .= " AND MONTH(date) = '" . date('m', strtotime($date)) . "' ";
    $sql .= " ORDER BY reading1 DESC";
    $sql .= " LIMIT 1";

    $maxReading = $db->query($sql)->fetchAll()[0];

    return $maxReading;
  }

  public function highestReadingsByMonth() {
    global $db;

    $i = 0;
    do {
      $lookupDate = date('Y-m', strtotime($i . " months ago"));

      $reading = $this->highestReadingForMonth($lookupDate);

      $readingsArray[$lookupDate] = $reading['reading1'];
      $i++;
    } while ($i < 12);

    return $readingsArray;
  }


  public function highestReadingForYear($date = null) {
    global $db;

    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " AND YEAR(date) = '" . $date . "' ";
    $sql .= " ORDER BY reading1 DESC";
    $sql .= " LIMIT 1";

    $maxReading = $db->query($sql)->fetchAll()[0];

    return $maxReading;
  }

  public function highestReadingsByYear() {
    global $db;

    $i = 0;
    do {
      $lookupDate = date('Y', strtotime($i . " years ago"));

      $reading = $this->highestReadingForYear($lookupDate);

      $readingsArray[$lookupDate] = $reading['reading1'];
      $i++;
    } while ($i < 10);

    $readingsArray = array_reverse($readingsArray, true);

    return $readingsArray;
  }

  public function consumptionByYear() {
    global $db;

    $highestReadingsByYear = $this->highestReadingsByYear();

    foreach ($highestReadingsByYear AS $date => $value) {
      $previousYear = $date - 1;
      $previousYearReading = $highestReadingsByYear[$previousYear];

      if ($value > 0 && $previousYearReading > 0) {
        $readingsArray[$date] = $value - $previousYearReading;
      } else {
        $readingsArray[$date] = 0;
      }
    }

    return $readingsArray;
  }



  public function getFirstReading() {
    global $db;

    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    //$sql .= " AND date > '" . date('Y-m-d', strtotime('3 years ago')) . "' ";
    $sql .= " ORDER BY date ASC";
    $sql .= " LIMIT 1";

    $recentReading = $db->query($sql)->fetchAll()[0];

    return $recentReading;
  }








  public function fetchReadingsAll() {
    $readingsClass = new readings();

    $readings = $readingsClass->meter_all_readings($this->uid);

    return $readings;
  }



}

?>
