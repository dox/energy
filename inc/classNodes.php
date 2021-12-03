<?php
class nodes extends node {
  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY uid DESC";

    $nodes = $db->query($sql)->fetchAll();

    return $nodes;
  }

  public function allEnabled() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE enabled = 1";
    $sql .= " ORDER BY uid DESC";

    $nodes = $db->query($sql)->fetchAll();

    return $nodes;
  }

  public function recentlyUpdated() {
    global $db;

    $readingsSQL  = "SELECT node FROM readings ";
    $readingsSQL .= " WHERE date BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
    $readingsSQL .= " ORDER BY date DESC";
    $readingsSQL .= " LIMIT 30";

    $nodeUIDS = $db->query($readingsSQL)->fetchAll();

    foreach ($nodeUIDS AS $nodeUID) {
      $sql  = "SELECT * FROM nodes ";
      $sql .= " WHERE uid = '" . $nodeUID['node'] . "'";
      $sql .= " LIMIT 1";

      $node = $db->query($sql)->fetchArray();

      $nodesArray[] = $node;
    }

    return $nodesArray;
  }

  public function nodeTable($nodes = null) {
    $tableID = "table_" . rand(0, 1000);

    $output .= "<table class=\"table\" id=\"" . $tableID . "\">";
    $output .= "<thead>";
    $output .= "<tr>";
    $output .= "<th scope=\"col\" style=\"width: 30%\" onclick=\"sortTable(0, '" . $tableID . "')\">Name</th>";
    $output .= "<th scope=\"col\" style=\"width: 10%\" onclick=\"sortTable(1, '" . $tableID . "')\">Type</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\" onclick=\"sortTable(2, '" . $tableID . "')\">Current Value</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\" onclick=\"sortTable(3, '" . $tableID . "')\">Last Reading</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\" onclick=\"sortTable(4, '" . $tableID . "')\">Serial</th>";
    $output .= "<th scope=\"col\" style=\"width: 15%\" onclick=\"sortTable(5, '" . $tableID . "')\"MPRN</th>";
    $output .= "</tr>";
    $output .= "</thead>";

    $output .= "<tbody>";
    $output .= $this->nodeRow($nodes);
    $output .= "</tbody>";

    $output .= "</table>";

    return $output;
  }

  private function nodeRow($nodes = null) {
    foreach ($nodes AS $nodeUnique) {
      $node = new node($nodeUnique['uid']);

      if ($node->enabled == 1) {
        $rowClass = " ";
      } else {
        $rowClass = "table-secondary d-none";
      }
      $output .= "<tr class=\"" . $rowClass . "\">";
      $output .= "<td><a href=\"index.php?n=node&nodeUID=" . $node->uid . "\">" . $node->name . "</a></td>";
      $output .= "<td>" . $node->nodeTypeBadge() . "</td>";
      $output .= "<td>" . displayReading($node->currentReading()) . " " . $node->unit . "</td>";
      $output .= "<td>" . howLongAgo($node->mostRecentReadingDate()) . "</td>";
      $output .= "<td>" . showHide($node->serial) . "</td>";
      $output .= "<td>" . showHide($node->mprn) . "</td>";
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

    $logArray['category'] = "node";
    $logArray['type'] = "success";
    $logArray['value'] = "[nodeUID:" . $create->lastInsertID() . "] created successfully";
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
    $nodes = $this->allEnabled();

    foreach ($nodes AS $node) {
      $node = new node($node['uid']);
      $array[] = "['" . $node->cleanName() . "', " . $node->geoMarker() . "]";

    }

    return $array;
  }
}
?>
