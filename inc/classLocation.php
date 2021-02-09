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

  public function highestReadingsByMonth($type = null) {
    global $db;

    $meters = meters::allByLocationAndType($this->uid, $type);

    foreach ($meters AS $meter) {
      $meter = new meter($meter['uid']);
      $readingsByMonth = $meter->highestReadingsByMonth();

      foreach ($readingsByMonth AS $reading => $value) {
        $maxReading[$reading] = $value;
      }
    }

    return $maxReading;
  }


  public function consumptionByMonth($type = null) {
    global $db;

    $highestReadingsByMonth = $this->highestReadingsByMonth($type);

    foreach ($highestReadingsByMonth AS $date => $value) {
      $previousMonth = date('Y-m', strtotime($date . " -1 month"));


      if ($value > 0 && $highestReadingsByMonth[$previousMonth] > 0) {
        $readingsArray[$date] = $value - $highestReadingsByMonth[$previousMonth];
      } else {
        $readingsArray[$date] = 0;
      }
    }

    $readingsArray = array_reverse($readingsArray, true);

    return $readingsArray;
  }
}
?>
