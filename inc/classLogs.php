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

    if (isset($_SESSION['username'])) {
      $username = $_SESSION['username'];
    } else {
      $username = "SYSTEM";
    }

    $array['value'] = escape($array['value']);

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (ip, username, category, type, value) ";
    $sql .= " VALUES (";
    $sql .= "'" . ip2long($_SERVER['REMOTE_ADDR']) . "', ";
    $sql .= "'" . $username . "', ";
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
			}
		}
    $logArray['category'] = "admin";
    $logArray['type'] = "warning";
    $logArray['value'] = count($logsToDelete) . " log(s) purged";
    $this->create($logArray);
	}

  private function displayCategoryBadge($category = null) {
    if ($category == "node") {
      $class = "bg-primary";
    } elseif ($category == "reading") {
      $class = "bg-success";
    } elseif ($category == "logon") {
      $class = "bg-primary";
    } else {
      $class = "bg-dark";
    }

    $output = "<span class=\"badge rounded-pill " . $class . " float-end\">" . $category . "</span>";

    return $output;
  }

  private function displayRow($array = null) {
    //$string = 'Some text [mealUID:123] here';
    $string = $log['description'];
    $patternArray['/\[nodeUID:([0-9]+)\]/'] = "<code><a href=\"index.php?n=node&nodeUID=$1\" class=\"text-decoration-none\">[nodeUID:$1]</a></code>";
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
    $output .= "<td>" . $array['value'] . $this->displayCategoryBadge($array['category']) . "</td>";
    $output .= "</tr>";

    return $output;
  }

  public function displayTable() {
    $output  = "<table class=\"table\">";
    $output .= "<thead>";
    $output .= "<th>" . "Date" . "</th>";
    $output .= "<th>" . "IP" . "</th>";
    $output .= "<th>" . "Username" . "</th>";
    $output .= "<th>" . "Value" . "</th>";
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
