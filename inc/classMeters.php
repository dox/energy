<?php
class meters extends meter {
  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY uid DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function allEnabled() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE enabled = 1";
    $sql .= " ORDER BY uid DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function recentlyUpdated() {
    global $db;

    $readingsSQL  = "SELECT meter FROM readings ";
    $readingsSQL .= " WHERE date BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
    $readingsSQL .= " ORDER BY date DESC";
    $readingsSQL .= " LIMIT 30";

    $meterUIDS = $db->query($readingsSQL)->fetchAll();

    foreach ($meterUIDS AS $meterUID) {
      $sql  = "SELECT * FROM meters ";
      $sql .= " WHERE uid = '" . $meterUID['meter'] . "'";
      $sql .= " LIMIT 1";

      $meter = $db->query($sql)->fetchArray();

      $metersArray[] = $meter;
    }

    return $metersArray;
  }

  public function meterTable($meters = null) {
    $output .= "<table class=\"table\">";
    $output .= "<thead>";
    $output .= "<tr>";
    $output .= "<th scope=\"col\" style=\"width: 30%\">Name</th>";
    $output .= "<th scope=\"col\" style=\"width: 10%\">Type</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\">Current Value</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\">Last Reading</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\">Serial</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\">MPRN</th>";
    $output .= "</tr>";
    $output .= "</thead>";

    $output .= "<tbody>";
    $output .= $this->meterRow($meters);
    $output .= "</tbody>";

    $output .= "</table>";

    return $output;
  }

  private function meterRow($meters = null) {
    foreach ($meters AS $meterUnique) {
      $meter = new meter($meterUnique['uid']);

      if ($meter->enabled == 1) {
        $rowClass = "";
      } else {
        $rowClass = "table-secondary d-none";
      }
      $output .= "<tr class=\"" . $rowClass . "\">";
      $output .= "<th scope=\"row\"><a href=\"index.php?n=node&meterUID=" . $meter->uid . "\">" . $meter->name . "</a></th>";
      $output .= "<td>" . $meter->meterTypeBadge() . "</td>";
      $output .= "<td>" . displayReading($meter->currentReading()) . " " . $meter->unit . "</td>";
      $output .= "<td>" . howLongAgo($meter->mostRecentReadingDate()) . "</td>";
      $output .= "<td>" . $meter->displaySecurely('serial') . "</td>";
      $output .= "<td>" . $meter->displaySecurely('mprn') . "</td>";
      $output .= "</tr>";
    }

    return $output;
  }

  public function create($array = null) {
	   global $db, $logsClass;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      $sqlColumns[] = $updateItem;
      $sqlValues[] = "'" . $value . "' ";
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);

    $logArray['category'] = "meter";
    $logArray['type'] = "success";
    $logArray['value'] = "[meterUID:" . $create->lastInsertID() . "] created successfully";
    $logsClass->create($logArray);

    return $create;
  }

  public function suppliers() {
    global $db;

    $sql  = "SELECT supplier FROM " . self::$table_name;
    $sql .= " GROUP BY supplier";
    $sql .= " ORDER BY supplier ASC";

    $suppliers = $db->query($sql)->fetchAll();

    foreach ($suppliers AS $supplier) {
      $supplierArray[] = $supplier['supplier'];
    }

    return $supplierArray;
  }

  public function geoMarkers() {
    $meters = $this->allEnabled();

    foreach ($meters AS $meter) {
      $meter = new meter($meter['uid']);
      $array[] = "['" . $meter->cleanName() . "', " . $meter->geoMarker() . "]";

    }

    return $array;
  }
}
?>
