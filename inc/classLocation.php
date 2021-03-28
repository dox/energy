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

  public function geoLocation() {
    global $settingsClass;

    if (isset($this->geo)) {
      $geoReturn = $this->geo;
    } else {
      $geoReturn = $settingsClass->value('site_geolocation');
    }

    return $geoReturn;
  }

  public function highestReadingsByMonth($type = null) {
    global $db;

    $meters = meters::allByLocationAndType($this->uid, $type);

    foreach ($meters AS $meter) {
      $meter = new meter($meter['uid']);
      $readingsByMonth = $meter->highestReadingsByMonth();

      foreach ($readingsByMonth AS $reading => $value) {
        $maxReading[$reading] = $value;
      }
    }

    return $maxReading;
  }


  public function consumptionByMonth($type = null) {
    global $db;

    $highestReadingsByMonth = $this->highestReadingsByMonth($type);

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
}
?>
