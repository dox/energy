<?php
class location {
  protected static $table_name = "locations";

  public $uid;
  public $name;
  public $description;
  public $geo;

  function __construct($locationUID = null) {

    global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = '" . $locationUID . "'";
		$meter = $db->query($sql)->fetchArray();

		foreach ($meter AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function cleanName() {
    $cleanName = str_replace("'", "\'", $this->name);

    return $cleanName;
  }

  public function geoLocation() {
    global $settingsClass;

    if (isset($this->geo)) {
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

  public function geoMarkersOfNodes() {
    $nodes = $this->allNodes();

    foreach ($nodes AS $node) {
      $node = new meter($node['uid']);
      $array[] = "['" . $node->cleanName() . "', " . $node->geoLocation() . "]";

    }

    return $array;
  }

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

    $logArray['category'] = "location";
    $logArray['type'] = "success";
    $logArray['value'] = "[locationUID:" . $this->uid . "] updated successfully";
    $logsClass->create($logArray);

    return $update;
  }

  public function allNodes($enabledDisabled = "enabled") {
    global $db;

    if ($enabledDisabled == "all") {
      $sqlEnabled = "";
    } else {
      $sqlEnabled = " AND enabled = '1' ";
    }

    $sql  = "SELECT * FROM meters";
    $sql .= " WHERE location = '" . $this->uid . "' ";
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
    $sql .= " WHERE location = '" . $this->uid . "' ";
    $sql .= " AND type = '" . $type . "' ";
    $sql .= $sqlEnabled;
    $sql .= " ORDER BY uid DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }
}
?>
