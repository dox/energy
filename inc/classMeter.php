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
  	$output = "<span class=\"badge rounded-pill " . $class . "\">" . $icon . $this->type . "</span>";

    return $output;
  }

  public function image() {
    if (isset($this->photograph)) {
      $output = "<img src=\"uploads/" . $this->photograph . "\" class=\"rounded img-fluid\" alt=\"Image of utlity meter\">";
    } else {
      $output = "";
    }

    return $output;
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

    $sql  = "SELECT * FROM readings";
    $sql .= " WHERE meter = '" . $this->uid . "' ";
    $sql .= " ORDER BY date DESC";
    $sql .= " LIMIT 1";

    $lastReading = $db->query($sql)->fetchAll();
    $lastReading = $lastReading[0]['reading1'];

    if (isset($lastReading)) {
      $return = $lastReading;
    } else {
      return "Unknown";
    }

    return $return;
  }

  public function update($array = null) {
    global $db;

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

    return $update;
  }

  public function delete() {
    global $db;

    $sql1  = "DELETE FROM readings";
    $sql1 .= " WHERE meter = '" . $this->uid . "'";

    $deleteReadings = $db->query($sql1);

    $sql2  = "DELETE FROM " . self::$table_name;
    $sql2 .= " WHERE uid = '" . $this->uid . "' ";
    $sql2 .= " LIMIT 1";

    $deleteMeter = $db->query($sql2);

    return $deleteMeter;
  }

}

?>
