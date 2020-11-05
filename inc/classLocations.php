<?php
class locations extends location {
  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY name ASC";

    $locations = $db->query($sql)->fetchAll();

    return $locations;
  }
}


?>
