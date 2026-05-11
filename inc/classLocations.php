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
    $array = array();

    foreach ($locations AS $location) {
      $location = new location($location['uid']);
      $url = "index.php?n=location&locationUID=" . $location->uid;
      $name = "<a href=\"" . $url . "\">" . $location->cleanName() . "</a>";
      $array[] = "['" . $name . "', " . $location->geoLocation() . "]";

    }

    return $array;
  }
  
  public function create($array = null) {
       global $db, $logsClass;
    
      $create = $db->insert(
        self::$table_name,
        $array,
        array('name', 'description', 'geo')
      );
    
      $logArray['category'] = "location";
      $logArray['type'] = "success";
      $logArray['value'] = "[locationUID:" . $create->lastInsertID() . "] created successfully";
      $logsClass->create($logArray);
    
      return $create;
    }
}
?>
