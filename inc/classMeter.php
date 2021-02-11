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
  public $billed;
  public $enabled;

  function __construct($meterUID = null) {

    global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = '" . $meterUID . "'";
		$meter = $db->query($sql)->fetchArray();

		foreach ($meter AS $key => $value) {
			$this->$key = $value;
		}
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

  public function daysSinceLastUpdate() {
  	global $db;

    $sql  = "SELECT * FROM readings";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $db->query($sql)->fetchAll();
    $lastReading = $lastReading[0]['date'];

  	if (isset($lastReading)) {
      $today = date('Y-m-d H:i:s'); // today date
      $diff = strtotime($today) - strtotime($lastReading);

      $differenceInDays = round($days = (int)$diff/(60*60*24));

  	} else {
  		$differenceInDays = 0;
  	}

    if ($differenceInDays < 0) {
      $return = "Unknown";
    } elseif ($differenceInDays == 0) {
      $return = "Today";
    } else {
      $return = round($differenceInDays) . autoPluralise (" day ", " days ", $differenceInDays) . " ago";
    }

  	return $return;
  }

  public function current_reading() {
    global $db;

    $sql  = "SELECT reading1 FROM readings";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $db->query($sql)->fetchAll()[0];
    $lastReading = $lastReading['reading1'];

    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      return "Unknown";
    }

    return $return;
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

  public function consumptionByMonth() {
    global $db;

    $highestReadingsByMonth = $this->highestReadingsByMonth();

    foreach ($highestReadingsByMonth AS $date => $value) {
      $previousMonth = date('Y-m', strtotime($date . " -1 month"));


      if ($value > 0 && $highestReadingsByMonth[$previousMonth] > 0) {
        $readingsArray[$date] = $value - $highestReadingsByMonth[$previousMonth];
      } else {
        $readingsArray[$date] = 0;
      }
    }

    $readingsArray = array_reverse($readingsArray, true);

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

}

?>
