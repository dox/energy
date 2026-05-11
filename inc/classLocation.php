<?php
class location {
  protected static $table_name = "locations";

  public $uid;
  public $name;
  public $description;
  public $geo;
  public $cache;

  function __construct($locationUID = null) {

    global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = ?";
		$node = $db->query($sql, cleanInt($locationUID))->fetchArray();

		foreach ($node AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function cleanName() {
    $cleanName = escape($this->name);

    // catch empty names
    if ($cleanName == "") {
      $cleanName = "[no-name]";
    }

    return $cleanName;
  }

  public function geoLocation() {
    global $settingsClass;

    if (isset($this->geo) && !empty($this->geo)) {
      $geoReturn = $this->geo;
    } else {
      $geoReturn = $settingsClass->value('site_geolocation');
    }

    return $geoReturn;
  }

  public function geoMarker() {
    $url = "index.php?n=node&nodeUID=" . $this->uid;
    $name = "<a href=\"" . $url . "\">" . $this->cleanName() . "</a>";

    $array[] = "['" . $name . "', " . $this->geoLocation() . "]";

    return $array;
  }

  public function geoMarkersOfNodes() {
    $nodes = $this->allNodes();
    $array = array();

    foreach ($nodes AS $node) {
      $node = new node($node['uid']);

      $url = "index.php?n=node&nodeUID=" . $node->uid;
      $name = "<a href=\"" . $url . "\">" . $node->cleanName() . "</a>";

      $array[] = "['" . $name . "', " . $node->geoLocation() . "]";

    }

    return $array;
  }

  public function highestReadingsByMonth($type = null) {
    global $db;

    $nodes = $this->allNodesByType($type);
    $maxReading = array();

    foreach ($nodes AS $node) {
      $node = new node($node['uid']);
      $readingsByMonth = $node->highestReadingsByMonth();

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
          $nodes = $this->allNodesByType($type, "all");

          $totalConsumption = 0;
          foreach ($nodes AS $node) {
            $node = new node($node['uid']);
            $totalConsumption = $totalConsumption + $node->consumptionForMonth($date);
          }

          // check in case the difference is a negative value (it shouldn't be!)
          if ($totalConsumption < 0) {
            $totalConsumption = 0;
          }

          //$this->cache($type, $date, $totalConsumption);

          return $totalConsumption;
  }

  public function cache($type, $date, $value) {
    global $db;

    $sql  = "SELECT cache FROM " . self::$table_name;
    $sql .= " WHERE uid = ? ";
    $sql .= " LIMIT 1";

    $currentCache = $db->query($sql, cleanInt($this->uid))->fetchArray();

    $currentCache = json_decode($currentCache['cache'] ?? '', TRUE);
    if (!is_array($currentCache)) {
      $currentCache = array();
    }
    $currentCache[$type][$date] = $value;

    $db->update(self::$table_name, array('cache' => json_encode($currentCache)), 'uid', cleanInt($this->uid), array('cache'));

    return true;
  }

  public function getFromCache($type, $date) {
    $cache = json_decode($this->cache);
    //printArray($cache);

    if (isset($cache->$type->$date)) {
      return $cache->$type->$date;
    } else {
      return false;
    }
  }

  public function expireCache() {
    global $db;

    $db->update(self::$table_name, array('cache' => null), 'uid', cleanInt($this->uid), array('cache'));

    return true;
  }

  public function update($array = null) {
    global $db, $logsClass;

    $update = $db->update(
      self::$table_name,
      $array,
      'uid',
      cleanInt($this->uid),
      array('name', 'description', 'geo')
    );

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

    $sql  = "SELECT * FROM nodes";
    $sql .= " WHERE location = ? ";
    $sql .= $sqlEnabled;
    $sql .= " ORDER BY type ASC, name ASC";

    $nodes = $db->query($sql, cleanInt($this->uid))->fetchAll();

    return $nodes;
  }

  public function allNodesByType($type = null, $enabledDisabled = "enabled") {
    global $db;

    if ($enabledDisabled == "all") {
      $sqlEnabled = "";
    } else {
      $sqlEnabled = " AND enabled = '1' ";
    }

    $sql  = "SELECT * FROM nodes";
    $sql .= " WHERE location = ? ";
    $sql .= " AND type = ? ";
    $sql .= $sqlEnabled;
    $sql .= " ORDER BY name ASC";

    $nodes = $db->query($sql, cleanInt($this->uid), $type)->fetchAll();

    return $nodes;
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

  public function co2ByMonth() {
    $nodes = $this->allNodes();

    $co2Array = array();
    foreach ($nodes AS $node) {
      $node = new node($node['uid']);

      foreach ($node->co2ByMonth() AS $date => $value) {
        $co2Array[$date] = $co2Array[$date] + $value;
      }

    }

    return $co2Array;
  }

  public function delete() {
    global $db, $logsClass;

    $locationUID = $this->uid;

    foreach ($this->allnodes("all") AS $node) {
      $node = new node($node['uid']);
      $node->delete();
    }

    $deleteLocation = $db->delete(self::$table_name, 'uid', cleanInt($this->uid));

    $logArray['category'] = "location";
    $logArray['type'] = "warning";
    $logArray['value'] = "[locationUID:" . $locationUID . "] deleted successfully";
    $logsClass->create($logArray);

    return $deleteLocation;
  }
}
?>
