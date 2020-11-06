<?php
class readings {
  protected static $table_name = "readings";

  public $uid;
  public $meter;
  public $date;
  public $reading1;

  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY date DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function allByMeter($meterUID = null) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE meter = '" . $meterUID . "' ";
    $sql .= " ORDER BY date DESC";

    $readings = $db->query($sql)->fetchAll();

    return $readings;
  }

  public function create($meterUID = null, $reading1 = null) {
    global $db;

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (meter, date, reading1) ";
    $sql .= " VALUES('" . $meterUID . "', '" . date('Y-m-d H:i:s') . "', '" . $reading1 . "')";
    
    $insert = $db->query($sql);

    return $insert;
  }

  public function meterTable($meters = null) {
    $output .= "<table class=\"table\">";
    $output .= "<thead>";
    $output .= "<tr>";
    $output .= "<th scope=\"col\">Name</th>";
    $output .= "<th scope=\"col\">Type</th>";
    $output .= "<th scope=\"col\">Current Value</th>";
    $output .= "<th scope=\"col\">Last Reading</th>";
    $output .= "<th scope=\"col\">Serial</th>";
    $output .= "</tr>";
    $output .= "</thead>";

    $output .= "<tbody>";
    $output .= $this->meterRow($meters);
    $output .= "</tbody>";

    $output .= "</table>";

    return $output;
  }

  private function meterRow($meters = null) {
    foreach ($meters AS $meterUnique) {
      $meter = new meter($meterUnique['uid']);

      if ($meter->enabled == 1) {
        $rowClass = "";
      } else {
        $rowClass = "table-secondary";
      }
      $output .= "<tr class=\"" . $rowClass . "\">";
      $output .= "<th scope=\"row\"><a href=\"index.php?n=meter&meterUID=" . $meter->uid . "\">" . $meter->name . "</a></th>";
      $output .= "<td>" . $meter->meterTypeBadge() . "</td>";
      $output .= "<td>" . $meter->type . "</td>";
      $output .= "<td>" . $meter->type . "</td>";
      $output .= "<td>" . $meter->serial . "</td>";
      $output .= "</tr>";
    }

    return $output;
  }

  public function readings_monthlyByyear($meterUID = null) {
    global $db;

    $thisYear = date('Y');
    $i = 0;
    do {
      $lookupYear = $thisYear - $i;

      $lookupMonth = 1;
      do  {
        $sql  = "SELECT * FROM readings_by_month ";
        $sql .= " WHERE meter = '" . $meterUID . "' ";
        $sql .= " AND year = '" . $lookupYear . "' ";
        $sql .= " AND month = '" . $lookupMonth . "' ";
        $sql .= " ORDER BY year DESC";

        $readings = $db->query($sql)->fetchAll();

        // catch 0 values for each month!
        if ($readings[0]['reading1'] <= 0) {
          $readings[0]['reading1'] = 0;
        }

        $readingsArray[$lookupYear][$lookupMonth] = $readings[0]['reading1'];

        $lookupMonth++;
      } while ($lookupMonth <= 12);
      $i++;
    } while ($i < 2);
    return $readingsArray;
  }

  public function consumption_monthly($meterUID = null, $lookupYear = null) {
    global $db;

    if ($lookupYear == null) {
  		$lookupYear = date('Y');
  	}

    $lookupMonth = 1;
    do  {
      // this month
      $sql  = "SELECT * FROM readings_by_month ";
      $sql .= " WHERE meter = '" . $meterUID . "' ";
      $sql .= " AND year = '" . $lookupYear . "' ";
      $sql .= " AND month = '" . $lookupMonth . "' ";
      $sql .= " ORDER BY year DESC";

      $thisMonthreading = $db->query($sql)->fetchAll();
      $thisMonthreading = $thisMonthreading[0]['reading1'];

      // previous month
      // if this month is January, the previous month is December of the previous year
      if ($lookupMonth == 1) {
        $lookupYear2 = $lookupYear - 1;
        $sql  = "SELECT * FROM readings_by_month ";
        $sql .= " WHERE meter = '" . $meterUID . "' ";
        $sql .= " AND year = '" . $lookupYear2 . "' ";
        $sql .= " AND month = '12' ";
        $sql .= " ORDER BY year DESC";
  		} else {
        $lookupMonth2 = $lookupMonth - 1;
        $sql  = "SELECT * FROM readings_by_month ";
        $sql .= " WHERE meter = '" . $meterUID . "' ";
        $sql .= " AND year = '" . $lookupYear . "' ";
        $sql .= " AND month = '" . $lookupMonth2 . "' ";
        $sql .= " ORDER BY year DESC";
  		}

      $previousMonthreading = $db->query($sql)->fetchAll();
      $previousMonthreading = $previousMonthreading[0]['reading1'];

      // catch 0 values for each month!
      if ($thisMonthreading <= 0) {
        $thisMonthreading = 0;
      }

      if ($previousMonthreading <= 0) {
        $previousMonthreading = 0;
      }

      $consumptionValue = $thisMonthreading - $previousMonthreading;

      if ($consumptionValue <= 0 || $previousMonthreading <= 0) {
        $readingsArray[$lookupYear][$lookupMonth] = 0;
      } else {
        $readingsArray[$lookupYear][$lookupMonth] = $thisMonthreading - $previousMonthreading;
      }

      $lookupMonth++;
    } while ($lookupMonth <= 12);

    return $readingsArray;
  }
}
?>
