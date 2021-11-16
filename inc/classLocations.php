<?php
class locations extends location {
  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY name ASC";

    $locations = $db->query($sql)->fetchAll();

    return $locations;
  }

  public function geoMarkers() {
    $locations = $this->all();

    foreach ($locations AS $location) {
      $location = new location($location['uid']);
      $array[] = "['" . $location->cleanName() . "', " . $location->geoLocation() . "]";

    }

    return $array;
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
    
      $logArray['category'] = "location";
      $logArray['type'] = "success";
      $logArray['value'] = "[locationUID:" . $create->lastInsertID() . "] created successfully";
      $logsClass->create($logArray);
    
      return $create;
    }
}
?>
