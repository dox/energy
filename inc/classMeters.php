<?php
class meters extends meter {
  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY uid DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function allByLocation($locationUID = null, $enabledDisabled = "enabled") {
    global $db;

    if ($enabledDisabled == "all") {
      $sqlEnabled = "";
    } else {
      $sqlEnabled = " AND enabled = '1' ";
    }

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE location = '" . $locationUID . "' ";
    $sql .= $sqlEnabled;
    $sql .= " ORDER BY uid DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function allByLocationAndType($locationUID = null, $type = null, $enabledDisabled = "enabled") {
    global $db;

    if ($enabledDisabled == "all") {
      $sqlEnabled = "";
    } else {
      $sqlEnabled = " AND enabled = '1' ";
    }

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE location = '" . $locationUID . "' ";
    $sql .= " AND type = '" . $type . "' ";
    $sql .= $sqlEnabled;
    $sql .= " ORDER BY uid DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function meterTable($meters = null) {
    $output .= "<table class=\"table\">";
    $output .= "<thead>";
    $output .= "<tr>";
    $output .= "<th scope=\"col\" style=\"width: 500px\">Name</th>";
    $output .= "<th scope=\"col\">Type</th>";
    $output .= "<th scope=\"col\">Current Value</th>";
    $output .= "<th scope=\"col\">Last Reading</th>";
    $output .= "<th scope=\"col\">Serial</th>";
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
      $output .= "<th scope=\"row\"><a href=\"index.php?n=meter&meterUID=" . $meter->uid . "\">" . $meter->name . "</a></th>";
      $output .= "<td>" . $meter->meterTypeBadge() . "</td>";
      $output .= "<td>" . number_format($meter->current_reading()) . " " . $meter->unit . "</td>";
      $output .= "<td>" . $meter->daysSinceLastUpdate() . "</td>";

      if ($_SESSION['logon'] == true) {
        $output .= "<td>" . $meter->serial . "</td>";
      } else {
        $output .= "<td>********</td>";
      }
      $output .= "</tr>";
    }

    return $output;
  }

  public function create($array = null) {
	   global $db;

    $sql  = "INSERT INTO " . self::$table_name;

    foreach ($array AS $updateItem => $value) {
      $sqlColumns[] = $updateItem;
      $sqlValues[] = "'" . $value . "' ";
    }

    $sql .= " (" . implode(",", $sqlColumns) . ") ";
    $sql .= " VALUES (" . implode(",", $sqlValues) . ")";

    $create = $db->query($sql);

    return $create;
  }

  public function types() {
    $type[] = "Electric";
    $type[] = "Gas";
    $type[] = "Water";
    $type[] = "Refuse";

    return $type;
  }

  public function units() {
    $unit[] = "m³";
    $unit[] = "kWh";
    $unit[] = "KG";

    return $unit;
  }


}
?>
