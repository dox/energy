<?php
class location {
  protected static $table_name = "locations";

  public $uid;
  public $name;
  public $description;

  function __construct($locationUID = null) {

    global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = '" . $locationUID . "'";
		$meter = $db->query($sql)->fetchArray();

		foreach ($meter AS $key => $value) {
			$this->$key = $value;
		}
  }
}
?>
