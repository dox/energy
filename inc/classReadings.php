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
      $sql .= " LIMIT " . cleanInt($limit);
    }

    $nodes = $db->query($sql)->fetchAll();

    return $nodes;
  }

  public function node_all_readings($nodeUID = null, $limit = 0) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE node = ? ";
    $sql .= " ORDER BY date DESC";
    
    if ($limit > 0) {
      $sql .= " LIMIT " . cleanInt($limit);
    }

    $readings = $db->query($sql, cleanInt($nodeUID))->fetchAll();

    return $readings;
  }

  public function node_all_readings_older_than($nodeUID = null, $age = 0) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE node = ? ";
    $sql .= " AND DATE(date) < ?";
    $sql .= " ORDER BY date DESC";

    $readings = $db->query($sql, cleanInt($nodeUID), date('Y-m-d', strtotime('-' . cleanInt($age) . ' days')))->fetchAll();

    return $readings;
  }

  //UNUSED??

  public function create($nodeUID = null, $readingDate = null, $reading1 = null) {
    global $db, $logsClass;
    
    if ($readingDate == null) {
      $readingDate = date('Y-m-d H:i:s');
    } else {
      $readingDate = date('Y-m-d H:i:s', strtotime(cleanDate($readingDate)));
    }
    
    $node = new node($nodeUID);
    $location = new location($node->location);

    if (isset($_SESSION['username'])) {
      $username = $_SESSION['username'];
    } else {
      $username = "SYSTEM";
    }

    $insert = $db->insert(self::$table_name, array(
      'node' => cleanInt($nodeUID),
      'date' => $readingDate,
      'reading1' => is_numeric($reading1) ? $reading1 : 0,
      'username' => $username
    ), array('node', 'date', 'reading1', 'username'));

    $logArray['category'] = "reading";
    $logArray['type'] = "success";
    $logArray['value'] = "[readingUID:" . $insert->lastInsertID() . "] (" . $reading1 . ") for [nodeUID:" . $nodeUID . "] created successfully";
    $logsClass->create($logArray);

    return $insert;
  }

  public function delete($readingUID = null) {
    global $db, $logsClass;

    $delete = $db->delete(self::$table_name, 'uid', cleanInt($readingUID));

    $logArray['category'] = "reading";
    $logArray['type'] = "warning";
    $logArray['value'] = "[readingUID:" . $readingUID . "] deleted successfully";
    $logsClass->create($logArray);

    return $delete;
  }
}
?>
