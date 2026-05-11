<?php
class node {
  protected static $table_name = "nodes";

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
  public $retention_days;
  public $cache;

  function __construct($nodeUID = null) {

    global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = ?";
		$node = $db->query($sql, cleanInt($nodeUID))->fetchArray();

		foreach ($node AS $key => $value) {
			$this->$key = $value;
		}
  }

  private function firstResult($sql) {
    global $db;

    $params = array_slice(func_get_args(), 1);
    $queryArgs = array_merge(array($sql), $params);
    $results = call_user_func_array(array($db, 'query'), $queryArgs)->fetchAll();

    if (isset($results[0])) {
      return $results[0];
    }

    return array();
  }

  public function readings_all() {
    global $db;

    $sql  = "SELECT *";
    $sql .= " FROM readings";
    $sql .= " WHERE node = ?";
    $sql .= " ORDER BY date DESC;";

    $readings = $db->query($sql, cleanInt($this->uid))->fetchAll();

    return $readings;
  }

  public function cleanName() {
    $cleanName = escape($this->name);

    // catch empty names
    if ($cleanName == "") {
      $cleanName = "[no-name]";
    }

    return $cleanName;
  }

  public function cleanRetention($includeText = false) {

    if ($this->retention_days == 0) {
        $return = "&#8734;";
    } else {
        $return = $this->retention_days;
    }

    if ($includeText == true) {
      $return = $return . " days";
    }

    return $return;
  }

  public function currentReading() {
    $sql  = "SELECT reading1 FROM readings";
    $sql .= " WHERE node = ? ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $this->firstResult($sql, cleanInt($this->uid));
    $lastReading = $lastReading['reading1'] ?? null;

    // check for no value at all (in which case, default to 0)
    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      $return = 0;
    }

    return $return;
  }

  public function readingsByMonth($debug = false) {
    global $db;

    $returnArray = array();

    $sql  = "SELECT DATE_FORMAT(date, '%Y-%m') AS date, MAX(reading1) AS reading1";
    $sql .= " FROM readings";
    $sql .= " WHERE node = ?";
    $sql .= " GROUP BY DATE_FORMAT(date, '%Y-%m')";
    $sql .= " ORDER BY date DESC;";

    $readingsByMonth = $db->query($sql, cleanInt($this->uid))->fetchAll();

    foreach ($readingsByMonth AS $reading) {
      $returnArray[$reading['date']] = $reading['reading1'];
    }

    krsort($returnArray);

    return $returnArray;
  }

  public function consumptionByMonth($debug = false) {
    $readings = $this->readingsByMonth($debug);
    $consumption = array();

    $i = 0;

    foreach ($readings AS $date => $value) {
      $previousMonth = date('Y-m', strtotime("-1 month", strtotime($date)));
      $thisMonthReading = $value;
      $previousMonthReading = $readings[$previousMonth];


      if ($date != array_key_last($readings)) {
        $value = max($thisMonthReading - $previousMonthReading, 0);

        // if reading data for this month is missing, try to calculate an average consumption
        if ($value == 0 || !array_key_exists($previousMonth, $readings)) {
          $averages = $this->averagesForReadings();

          $value = $averages['differencePerDay'] * 30;

          $consumption[$previousMonth] = $value;

          //echo $previousMonth . " didn't have a reading, so using guess of " . $value . "<br />";
        }

        if ($value < 0) {
          $value = 0;
        }

        $consumption[$date] = $value;

      }

      $i++;
    }

    krsort($consumption);

    return $consumption;
  }

  public function co2ByMonth() {
    global $settingsClass;

    $consumption = $this->consumptionByMonth();

    $unitCO2 = $settingsClass->value("unit_co2e_" . $this->type);

    $co2ByMonth = array();
    foreach ($consumption AS $date => $value) {
      if ($value < 0) {
        $value = 0;
      }

      $co2ByMonth[$date] = ($value * $unitCO2);
    }

    return $co2ByMonth;
  }

  public function consumptionForMonth($date = null) {
    if ($date == null) {
      $date = date('Y-m-d');
    }

    $date = date('Y-m', strtotime($date));

    $consumptionForMonth = $this->consumptionByMonth()[$date];

    if ($consumptionForMonth < 0) {
      $consumptionForMonth = 0;
    }

    return $consumptionForMonth;
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
    if ($date == null) {
      $date = date('Y-m-d');
    }

    $sql  = "SELECT reading1 FROM readings";
    $sql .= " WHERE node = ? ";
    $sql .= " AND YEAR(date) = ?";
    $sql .= " AND MONTH(date) = ?";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $this->firstResult($sql, cleanInt($this->uid), cleanInt(date('Y', strtotime($date))), cleanInt(date('m', strtotime($date))));
    $lastReading = $lastReading['reading1'] ?? null;

    // check for no value at all (in which case, default to 0)
    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      $return = 0;
    }

    return $return;
  }

  public function readingForYear($year = null) {
    if ($year == null) {
      $year = date('Y');
    }

    $sql  = "SELECT reading1 FROM readings";
    $sql .= " WHERE node = ? ";
    $sql .= " AND YEAR(date) = ?";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $this->firstResult($sql, cleanInt($this->uid), cleanInt($year));
    $lastReading = $lastReading['reading1'] ?? null;

    // check for no value at all (in which case, default to 0)
    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      $return = 0;
    }

    return $return;
  }

  public function consumptionBetweenTwoDates($dateFrom = null, $dateTo = null) {
    $dateFrom = cleanDate($dateFrom, date('Y-m-d', strtotime('1 year ago')));
    $dateTo = cleanDate($dateTo, date('Y-m-d'));

    $dateFromSQL = "SELECT reading1 FROM readings WHERE node = ? AND DATE(date) >= ? AND DATE(date) <= ? ORDER BY date ASC LIMIT 1";
    $dateFromReading = $this->firstResult($dateFromSQL, cleanInt($this->uid), $dateFrom, $dateTo)['reading1'] ?? 0;

    $dateToSQL = "SELECT reading1 FROM readings WHERE node = ? AND DATE(date) <= ? AND DATE(date) >= ? ORDER BY date DESC LIMIT 1";
    $dateToReading = $this->firstResult($dateToSQL, cleanInt($this->uid), $dateTo, $dateFrom)['reading1'] ?? 0;

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

    $firstReading = $this->getFirstReading();
    $lastReading = $this->getMostRecentReading();

    if (empty($firstReading) || empty($lastReading)) {
      return 0;
    }

    $nodesFirstReading = $firstReading['reading1'] ?? 0;
    $nodesLastReading = $lastReading['reading1'] ?? 0;
    $nodesTotalConsumption = $nodesLastReading - $nodesFirstReading;

    $daysLeftInYear = 365 - date('z');

    $nodesFirstDate = date('Y-m-d', strtotime($firstReading['date']));
    $nodesLastDate = date('Y-m-d', strtotime($lastReading['date']));
    $nodesDurationSeconds = abs(strtotime($nodesLastDate) - strtotime($nodesFirstDate));
    $nodesDurationDays = round($nodesDurationSeconds / (60 * 60 * 24));

    if ($nodesDurationDays <= 0) {
      return 0;
    }

    $nodesAverageConsumptionDaily = round($nodesTotalConsumption / $nodesDurationDays, 2);

    $projectedConsumption = $nodesAverageConsumptionDaily * $daysLeftInYear;
    //$projectedAdditionalConsumption = $projectedConsumption - $this->consumptionByYear()[date('Y')];

    return $projectedConsumption;
  }

  public function nodeTypeBadge() {
    if ($this->type == "Gas") {
  		$class = "bg-warning";
      $iconSymbol = "gas";
      //$icon = "<img src=\"/inc/icons/bootstrap.svg\" alt=\"\" width=\"32\" height=\"32\" title=\"Bootstrap\">";
  	} elseif ($this->type == "Electric") {
  		$class = "bg-danger";
      $iconSymbol = "electric";
  	} elseif ($this->type == "Water") {
  		$class = "bg-info";
      $iconSymbol = "water";
  	} elseif ($this->type == "Refuse") {
      $class = "bg-primary";
      $iconSymbol = "refuse";
    } elseif ($this->type == "Temperature") {
      $class = "bg-secondary";
      $iconSymbol = "temperature";
    } else {
  		$class = "bg-light";
      $iconSymbol = "nodes";
  	}
    $icon = "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#" . $iconSymbol . "\"/></svg>";
  	$output = "<span class=\"badge rounded-pill w-100 " . $class . "\">" . $icon . " " . $this->type . "</span>";

    return $output;
  }

  public function displayImage() {
    if (isset($this->photograph)) {
      $output = "<img src=\"uploads/" . rawurlencode(basename($this->photograph)) . "\" class=\"rounded img-fluid w-100 mb-4\" alt=\"Image of utlity node\">";
    } else {
      $output = "<div class=\"d-grid gap-2\"><span class=\"btn btn-sm btn-outline-secondary\">No photograph uploaded</span></div>";
    }

    return $output;
  }

  public function displayAddress() {
    $address = escape($this->address);

    $address = nl2br($address);

    return $address;
  }

  public function update($array = null) {
    global $db, $logsClass;

    $update = $db->update(
      self::$table_name,
      $array,
      'uid',
      cleanInt($this->uid),
      array('name', 'location', 'type', 'unit', 'photograph', 'serial', 'mprn', 'billed', 'enabled', 'geo', 'address', 'supplier', 'account_no', 'retention_days')
    );

    $logArray['category'] = "node";
    $logArray['type'] = "success";
    $logArray['value'] = "[nodeUID:" . $this->uid . "] updated successfully";
    $logsClass->create($logArray);

    return $update;
  }

  public function delete() {
    global $db, $logsClass;

    $thisUID = $this->uid;

    $deleteReadings = $db->delete('readings', 'node', cleanInt($this->uid), null);

    $this->deleteImage();

    $deleteNode = $db->delete(self::$table_name, 'uid', cleanInt($this->uid));

    $logArray['category'] = "node";
    $logArray['type'] = "warning";
    $logArray['value'] = "[nodeUID:" . $thisUID . "] deleted successfully";
    $logsClass->create($logArray);

    return $deleteNode;
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

  public function geoMarker() {
    $array[] = "['" . $this->cleanName() . "', " . $this->geoLocation() . "]";

    return $array;
  }

  public function uploadImage($FILE) {
      global $db, $logsClass;

      $uploadOk = 1;

      $target_dir = $_SERVER["DOCUMENT_ROOT"] . "/uploads/";
      $imageFileType = strtolower(pathinfo($FILE["photograph"]["name"] ?? '', PATHINFO_EXTENSION));
      $safeFileName = "node-" . cleanInt($this->uid) . "-" . bin2hex(random_bytes(8)) . "." . $imageFileType;
      $target_file = $target_dir . $safeFileName;

      // Check if image file is a actual image or fake image
      $check = getimagesize($FILE["photograph"]["tmp_name"] ?? '');
      if($check !== false) {
        $uploadOk = 1;
      } else {
        echo "File is not an image.";
        $uploadOk = 0;
      }

      // Check if file already exists
      if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
      }

      // Check file size
      if (($FILE["photograph"]["size"] ?? 0) > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
      }

      // Allow certain file formats
      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
      && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
      }

      if (!is_dir($target_dir) || !is_writable($target_dir)) {
        echo "Sorry, upload directory is not writable.";
        $uploadOk = 0;
      }

      // Check if $uploadOk is set to 0 by an error
      if ($uploadOk == 0) {
       $logArray['category'] = "file";
       $logArray['type'] = "warning";
       $logArray['value'] = $target_file . " file failed to upload for [nodeUID:" . $this->uid . "]";
       $logsClass->create($logArray);
      // if everything is ok, try to upload file
      } else {
        if (move_uploaded_file($FILE["photograph"]["tmp_name"], $target_file)) {
          $db->update('nodes', array('photograph' => $safeFileName), 'uid', cleanInt($this->uid), array('photograph'));

          $logArray['category'] = "file";
          $logArray['type'] = "success";
          $logArray['value'] = $target_file . " file uploaded successfully for [nodeUID:" . $this->uid . "]";
          $logsClass->create($logArray);
        } else {
          echo "Sorry, there was an error uploading your file.";

          $logArray['category'] = "file";
          $logArray['type'] = "warning";
          $logArray['value'] = $target_file . " file failed to upload for [nodeUID:" . $this->uid . "]";
          $logsClass->create($logArray);
        }
      }

      return true;
    }

  public function deleteImage() {
    global $db, $logsClass;

    if (isset($this->photograph)) {
      $file = $_SERVER["DOCUMENT_ROOT"] . "/uploads/" . basename($this->photograph);

      if (file_exists($file)) {
        unlink($file);

        $db->update('nodes', array('photograph' => null), 'uid', cleanInt($this->uid), array('photograph'));

        $logArray['category'] = "file";
    		$logArray['type'] = "success";
    		$logArray['value'] = $file . " file deleted successfully from [nodeUID:" . $this->uid . "]";
    		$logsClass->create($logArray);
      } else {
        $logArray['category'] = "file";
    		$logArray['type'] = "error";
    		$logArray['value'] = $file . " file did not exist to delete from [nodeUID:" . $this->uid . "]";
    		$logsClass->create($logArray);
      }
    }
  }

  public function getMostRecentReading() {
    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE node = ? ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    return $this->firstResult($sql, cleanInt($this->uid));
  }

  public function mostRecentReadingDate() {
    $date = $this->getMostRecentReading()['date'] ?? null;
    if (isset($date)) {
      return $date;
    } else {
      return false;
    }
  }

  public function mostRecentReadingValue() {
    return $this->getMostRecentReading()['reading1'] ?? 0;
  }

  public function getPreviousReading() {
    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE node = ? ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1, 1";

    return $this->firstResult($sql, cleanInt($this->uid));
  }

  public function previousReadingDate() {
    $date = $this->getPreviousReading()['date'] ?? null;
    if (isset($date)) {
      return $date;
    } else {
      return false;
    }
  }

  public function previousReadingValue() {
    return $this->getPreviousReading()['reading1'] ?? 0;
  }

  public function cache($date, $value) {
    global $db;

    $sql  = "SELECT cache FROM " . self::$table_name;
    $sql .= " WHERE uid = ? ";
    $sql .= " LIMIT 1";

    $currentCache = $db->query($sql, cleanInt($this->uid))->fetchArray();

    $currentCache = json_decode($currentCache['cache'] ?? '', TRUE);
    if (!is_array($currentCache)) {
      $currentCache = array();
    }
    $currentCache[$date] = $value;

    $db->update(self::$table_name, array('cache' => json_encode($currentCache)), 'uid', cleanInt($this->uid), array('cache'));

    return true;
  }

  public function getFromCache($date) {
    $cache = json_decode($this->cache);
    //printArray($cache);

    if (isset($cache->$date)) {
      return $cache->$date;
    } else {
      return false;
    }
  }

  public function expireCache() {
    global $db;

    $db->update(self::$table_name, array('cache' => null), 'uid', cleanInt($this->uid), array('cache'));

    return true;
  }

  public function averagesForReadings() {
    $readings = $this->readingsByMonth();

    if (count($readings) >= 2) {
      // Convert the keys (dates) into an array
      $dates = array_keys($readings);

      // Find the oldest and newest dates
      $oldestDate = min($dates);
      $newestDate = max($dates);

      // Convert the date strings to DateTime objects for easier date calculations
      $oldestDateTime = new DateTime($oldestDate);
      $newestDateTime = new DateTime($newestDate);

      // Calculate the difference in days
      $dateDifference = $oldestDateTime->diff($newestDateTime)->days;

      // Retrieve the values associated with the oldest and newest dates
      $oldestValue = $readings[$oldestDate];
      $newestValue = $readings[$newestDate];

      // Calculate the difference in values
      $valueDifference = $newestValue - $oldestValue;
      $differencePerDay = number_format($valueDifference / $dateDifference, 4);

      // Return the results
      return [
          'oldestDate' => $oldestDate,
          'oldestValue' => $oldestValue,
          'newestDate' => $newestDate,
          'newestValue' => $newestValue,
          'dateDifference' => $dateDifference,
          'valueDifference' => $valueDifference,
          'differencePerDay' => $differencePerDay
      ];
    }
  }



  // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  //
  // everything below this line needs going over
  //
  // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!







  public function highestReadingForMonth($date = null) {
    if ($date == null) {
  		$date = date('Y-m');
  	} else {
      $date = date('Y-m', strtotime($date));
    }

    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE node = ? ";
    $sql .= " AND YEAR(date) = ? ";
    $sql .= " AND MONTH(date) = ? ";
    $sql .= " ORDER BY reading1 DESC";
    $sql .= " LIMIT 1";

    return $this->firstResult($sql, cleanInt($this->uid), cleanInt(date('Y', strtotime($date))), cleanInt(date('m', strtotime($date))));
  }

  public function highestReadingsByMonth() {
    global $db;

    $i = 0;
    do {
      $lookupDate = date('Y-m', strtotime($i . " months ago"));

      $reading = $this->highestReadingForMonth($lookupDate);

      $readingsArray[$lookupDate] = $reading['reading1'] ?? 0;
      $i++;
    } while ($i < 12);

    return $readingsArray;
  }


  public function highestReadingForYear($date = null) {
    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE node = ? ";
    $sql .= " AND YEAR(date) = ? ";
    $sql .= " ORDER BY reading1 DESC";
    $sql .= " LIMIT 1";

    return $this->firstResult($sql, cleanInt($this->uid), cleanInt($date));
  }

  public function highestReadingsByYear() {
    global $db;

    $i = 0;
    do {
      $lookupDate = date('Y', strtotime($i . " years ago"));

      $reading = $this->highestReadingForYear($lookupDate);

      $readingsArray[$lookupDate] = $reading['reading1'] ?? 0;
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
      $previousYearReading = $highestReadingsByYear[$previousYear] ?? 0;

      if ($value > 0 && $previousYearReading > 0) {
        $readingsArray[$date] = $value - $previousYearReading;
      } else {
        $readingsArray[$date] = 0;
      }
    }

    return $readingsArray;
  }



  public function getFirstReading() {
    $sql  = "SELECT * FROM readings ";
    $sql .= " WHERE node = ? ";
    //$sql .= " AND date > '" . date('Y-m-d', strtotime('3 years ago')) . "' ";
    $sql .= " ORDER BY date ASC";
    $sql .= " LIMIT 1";

    return $this->firstResult($sql, cleanInt($this->uid));
  }








  public function fetchReadingsAll() {
    $readingsClass = new readings();

    $readings = $readingsClass->node_all_readings($this->uid);

    return $readings;
  }


}

?>
