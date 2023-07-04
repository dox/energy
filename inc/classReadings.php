<?php
class readings {
  protected static $table_name = "readings";

  public $uid;
  public $node;
  public $date;
  public $reading1;
  public $username;

  public function all($limit = 0) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY date DESC";
    
    if ($limit > 0) {
      $sql .= " LIMIT " . $limit;
    }

    $nodes = $db->query($sql)->fetchAll();

    return $nodes;
  }

  public function node_all_readings($nodeUID = null, $limit = 0) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE node = '" . $nodeUID . "' ";
    $sql .= " ORDER BY date DESC";
    
    if ($limit > 0) {
      $sql .= " LIMIT " . $limit;
    }

    $readings = $db->query($sql)->fetchAll();

    return $readings;
  }

  public function node_all_readings_older_than($nodeUID = null, $age = 0) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE node = '" . $nodeUID . "' ";
    $sql .= " AND DATE(date) < '" . date('Y-m-d', strtotime('-' . $age . ' days')) . "'";
    $sql .= " ORDER BY date DESC";

    $readings = $db->query($sql)->fetchAll();

    return $readings;
  }

  //UNUSED??

  public function create($nodeUID = null, $readingDate = null, $reading1 = null) {
    global $db, $logsClass;
    
    if ($readingDate == null) {
      $readingDate = date('Y-m-d H:i:s');
    }
    
    $node = new node($nodeUID);
    $location = new location($node->location);

    if (isset($_SESSION['username'])) {
      $username = $_SESSION['username'];
    } else {
      $username = "SYSTEM";
    }

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (node, date, reading1, username) ";
    $sql .= " VALUES('" . $nodeUID . "', '" . $readingDate . "', '" . $reading1 . "', '" . $username . "')";
    
    $insert = $db->query($sql);

    $logArray['category'] = "reading";
    $logArray['type'] = "success";
    $logArray['value'] = "[readingUID:" . $insert->lastInsertID() . "] (" . $reading1 . ") for [nodeUID:" . $nodeUID . "] created successfully";
    $logsClass->create($logArray);

    return $insert;
  }

  public function delete($readingUID = null) {
    global $db, $logsClass;

    $sql  = "DELETE FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $readingUID . "'";
    $sql .= " LIMIT 1";

    $delete = $db->query($sql);

    $logArray['category'] = "reading";
    $logArray['type'] = "warning";
    $logArray['value'] = "[readingUID:" . $readingUID . "] deleted successfully";
    $logsClass->create($logArray);

    return $delete;
  }
}
?>
