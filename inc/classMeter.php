<?php
class meter {
  protected static $table_name = "meters";

  public $uid;
  public $name;
  public $location;
  public $type;
  public $unit;
  public $photograph;
  public $serial;
  public $billed;
  public $enabled;

  function __construct($meterUID = null) {

    global $db;
		$sql = "SELECT * FROM " . self::$table_name . " WHERE uid = '" . $meterUID . "'";
		$meter = $db->query($sql)->fetchArray();

		foreach ($meter AS $key => $value) {
			$this->$key = $value;
		}
  }

  public function meterTypeBadge() {
    if ($this->type == "Gas") {
  		$class = "bg-primary";
      //$icon = "<img src=\"/inc/icons/bootstrap.svg\" alt=\"\" width=\"32\" height=\"32\" title=\"Bootstrap\">";
  	} elseif ($this->type == "Electric") {
  		$class = "bg-warning";
  	} elseif ($this->type == "Water") {
  		$class = "bg-info";
  	} else {
  		$class = "bg-light";
  	}

  	$output = "<span class=\"badge rounded-pill " . $class . "\">" . $this->type . "</span>";

    return $output;
  }

  public function image() {
    if (isset($this->photograph)) {
      $output = "<img src=\"uploads/" . $this->photograph . "\" class=\"rounded img-fluid\" alt=\"Image of utlity meter\">";
    } else {
      $output = "";
    }

    return $output;
  }

}

?>
