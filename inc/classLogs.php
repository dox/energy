<?php
class logs {
  protected static $table_name = "logs";

  public $uid;
  public $date;
  public $ip;
  public $username;
  public $category;
  public $type;
  public $value;

  public function all() {
    global $db;

    $sql  = "SELECT uid, INET_NTOA(ip) AS ip, date, username, category, type, value  FROM " . self::$table_name;
    $sql .= " ORDER BY date DESC";

    $logs = $db->query($sql)->fetchAll();

    return $logs;
  }

  public function create($array = null) {
    global $db;

    $array['value'] = escape($array['value']);

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (ip, username, category, type, value) ";
    $sql .= " VALUES (";
    $sql .= "'" . ip2long($_SERVER['REMOTE_ADDR']) . "', ";
    $sql .= "'" . $_SESSION['username'] . "', ";
    $sql .= "'" . $array['category'] . "', ";
    $sql .= "'" . $array['type'] . "', ";
    $sql .= "'" . $array['value'] . "'";
    $sql .= ")";

    $logs = $db->query($sql);
  }

  public function purge() {
		global $db;

    $logs_retention = "360";

		$sql = "SELECT * FROM " . self::$table_name . " WHERE type = 'purge' AND DATE(date) = '" . date('Y-m-d') . "' LIMIT 1";
		$lastPurge = $db->query($sql)->fetchAll();

		if (empty($lastPurge)) {
			$sql = "SELECT * FROM " . self::$table_name . " WHERE DATE(date) < '" . date('Y-m-d', strtotime('-' . $logs_retention . ' days')) . "'";
			$logsToDelete = $db->query($sql)->fetchAll();

			if (count($logsToDelete) > 0) {
				$sql = "DELETE FROM " . self::$table_name . " WHERE DATE(date) < '" . date('Y-m-d', strtotime('-' . $logs_retention . ' days')) . "'";
				$logsToDelete = $db->query($sql);

        $this->create("purge", count($logsToDelete) . " log(s) purged");
			}
		}
	}

  private function displayRow($array = null) {
    //$string = 'Some text [mealUID:123] here';
    $string = $log['description'];
    $patternArray['/\[meterUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=meter&meterUID=$1\" class=\"text-decoration-none\">[meterUID:$1]</a></code>";
    $patternArray['/\[readingUID:([0-9]+)\]/'] = "<code>[readingUID:$1]</code>";
    //$patternArray['/\[bookingUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=booking&bookingUID=$1\" class=\"text-decoration-none\">[bookingUID:$1]</a></code>";
    $patternArray['/\[locationUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=location&locationUID=$1\" class=\"text-decoration-none\">[locationUID:$1]</a></code>";

    foreach ($patternArray AS $pattern => $replace) {
      //echo $pattern . $replace;
      $array['value'] = preg_replace($pattern, $replace, $array['value']);
    }

    //$preg_string = preg_replace($pattern, $replace, $string);

    $output  = "<tr class=\"table-" . $array['type'] . "\">";
    $output .= "<td>" . dateDisplay($array['date'], true) . "</td>";
    $output .= "<td>" . $array['ip'] . "</td>";
    $output .= "<td>" . $array['username'] . "</td>";
    $output .= "<td>" . $array['category'] . "</td>";
    $output .= "<td>" . $array['value'] . "</td>";
    $output .= "</tr>";

    return $output;
  }

  public function displayTable() {
    $output  = "<table class=\"table\">";
    $output .= "<thead>";
    $output .= "<td>" . "Date" . "</td>";
    $output .= "<td>" . "IP" . "</td>";
    $output .= "<td>" . "Username" . "</td>";
    $output .= "<td>" . "Category" . "</td>";
    $output .= "<td>" . "Value" . "</td>";
    $output .= "</thead>";

    $output .= "<tbody>";

    foreach ($this->all() AS $log) {
      $output .= $this->displayRow($log);
    }

    $output .= "</tbody>";
    $output .= "</table>";

    return $output;
  }
}

$logsClass = new logs();
?>
