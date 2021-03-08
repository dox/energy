<?php
class readings {
  protected static $table_name = "readings";

  public $uid;
  public $meter;
  public $date;
  public $reading1;
  public $username;

  public function all() {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " ORDER BY date DESC";

    $meters = $db->query($sql)->fetchAll();

    return $meters;
  }

  public function meter_all_readings($meterUID = null) {
    global $db;

    $sql  = "SELECT * FROM " . self::$table_name;
    $sql .= " WHERE meter = '" . $meterUID . "' ";
    $sql .= " ORDER BY date DESC";

    $readings = $db->query($sql)->fetchAll();

    return $readings;
  }

  //UNUSED??
  public function location_all_readings($locationUID = null) {
    global $db;

    $sql  = "SELECT * FROM readings_by_month ";
    $sql .= " WHERE location = '" . $locationUID . "' ";
    $sql .= " ORDER BY year, month DESC";

    $readings = $db->query($sql)->fetchAll();

    //return $readings;
  }

  public function create($meterUID = null, $reading1 = null) {
    global $db, $logsClass;

    $sql  = "INSERT INTO " . self::$table_name;
    $sql .= " (meter, date, reading1, username) ";
    $sql .= " VALUES('" . $meterUID . "', '" . date('Y-m-d H:i:s') . "', '" . $reading1 . "', '" . $_SESSION['username'] . "')";

    $insert = $db->query($sql);

    $logArray['category'] = "reading";
    $logArray['type'] = "success";
    $logArray['value'] = "[readingUID:" . $insert->lastInsertID() . "] for [meterUID:" . $meterUID . "] created successfully";
    $logsClass->create($logArray);

    return $insert;
  }

  public function delete($readingUID = null) {
    global $db, $logsClass;

    $sql  = "DELETE FROM " . self::$table_name;
    $sql .= " WHERE uid = '" . $readingUID . "'";
    $sql .= " LIMIT 1";

    $delete = $db->query($sql);

    $logArray['category'] = "reading";
    $logArray['type'] = "warning";
    $logArray['value'] = "[readingUID:" . $readingUID . "] deleted successfully";
    $logsClass->create($logArray);

    return $delete;
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



  public function meter_monthly_consumption($meterUID = null, $lookupYear = null) {
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

  public function meter_yearly_consumption($meterUID = null) {
    global $db;

    $i = 0;
    do  {
      $thisYear = date('Y') - $i;
      $previousYear = $thisYear - 1;

      // this years
      $sql  = "SELECT * FROM readings_by_month ";
      $sql .= " WHERE meter = '" . $meterUID . "' ";
      $sql .= " AND year = '" . $thisYear . "' ";
      $sql .= " ORDER BY reading1 DESC";
      $sql .= " LIMIT 1";

      $yearTotal = $db->query($sql)->fetchAll();
      $yearTotal = $yearTotal[0]['reading1'];

      // previous years
      $sql  = "SELECT * FROM readings_by_month ";
      $sql .= " WHERE meter = '" . $meterUID . "' ";
      $sql .= " AND year = '" . $previousYear . "' ";
      $sql .= " ORDER BY reading1 DESC";
      $sql .= " LIMIT 1";

      $previousYearTotal = $db->query($sql)->fetchAll();
      $previousYearTotal = $previousYearTotal[0]['reading1'];

      $consumptionValue = $yearTotal - $previousYearTotal;

      if ($consumptionValue <= 0) {
        $consumptionValue = 0;
      }

      $readingsArray[$thisYear] = $consumptionValue;

      $i++;
    } while ($i <= years);

    return $readingsArray;
  }

  public function location_monthly_consumption($locationUID = null, $year = null, $utility = null) {
    global $db;

    $metersClass = new meters();
    $meters = $metersClass->allByLocation($locationUID);

    $i = 0;
    foreach ($meters AS $meter) {
      if ($meter['type'] == $utility) {
        $lookupMonth = 1;
        do {
          $lookupYear = $year - $i;

          // this month
          $sql  = "SELECT * FROM readings_by_month ";
          $sql .= " WHERE meter = '" . $meter['uid'] . "' ";
          $sql .= " AND year = '" . $lookupYear . "' ";
          $sql .= " AND month = '" . $lookupMonth . "' ";

          $thisMonthreading = $db->query($sql)->fetchAll();
          $thisMonthreading = $thisMonthreading[0]['reading1'];

          // previous month
          // if this month is January, the previous month is December of the previous year
          if ($lookupMonth == 1) {
            $lookupYear2 = $lookupYear - 1;
            $sql  = "SELECT * FROM readings_by_month ";
            $sql .= " WHERE meter = '" . $meter['uid'] . "' ";
            $sql .= " AND year = '" . $lookupYear2 . "' ";
            $sql .= " AND month = '12' ";
      		} else {
            $lookupMonth2 = $lookupMonth - 1;
            $sql  = "SELECT * FROM readings_by_month ";
            $sql .= " WHERE meter = '" . $meter['uid'] . "' ";
            $sql .= " AND year = '" . $lookupYear . "' ";
            $sql .= " AND month = '" . $lookupMonth2 . "' ";
      		}

          $previousMonthreading = $db->query($sql)->fetchAll();
          $previousMonthreading = $previousMonthreading[0]['reading1'];

          $consumption = $thisMonthreading - $previousMonthreading;

          if ($thisMonthreading <= 0 || $previousMonthreading <= 0 || $consumption <= 0) {
            $consumption = 0;
          }

          $returnArray[$lookupMonth] = $returnArray[$lookupMonth] + $consumption;
          $lookupMonth++;
        } while ($lookupMonth <= 12);
      }

    }
    return $returnArray;
  }
}
?>
