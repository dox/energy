<?php
class meters extends meter {
  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY uid DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function allByLocation($locationUID = null) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE location = '" . $locationUID . "' ";
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
        $rowClass = "table-secondary";
      }
      $output .= "<tr class=\"" . $rowClass . "\">";
      $output .= "<th scope=\"row\"><a href=\"index.php?n=meter&meterUID=" . $meter->uid . "\">" . $meter->name . "</a></th>";
      $output .= "<td>" . $meter->meterTypeBadge() . "</td>";
      $output .= "<td>" . $meter->type . "</td>";
      $output .= "<td>" . $meter->type . "</td>";
      $output .= "<td>" . $meter->serial . "</td>";
      $output .= "</tr>";
    }

    return $output;
  }
}
?>
