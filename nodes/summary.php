<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$locations = new locations;
$locations = $locations->all();
?>

<div class="container">
  <h1>Electricity</h1>

  <table class="table">
    <thead>
      <tr>
        <th scope="col" style="width: 400px;"></th>
        <?php
        $i = 5;

        do {
          $year = date('Y', strtotime($i . " year ago"));

          echo "<th scope=\"col\">" . $year . "</th>";
          $i--;
        } while ($i >= 0);

        ?>
      </tr>
      <?php
      foreach ($locations AS $location) {
        $metersClass = new meters;
  			$meters = $metersClass->allByLocationAndType($location['uid'], 'Electric');

        echo "<tr>";

        echo "<th scope=\"row\">" . $location['name'] . "</th>";

        $i = 5;
        do {
          $previousYear = date('Y', strtotime($i + 1 . " years ago"));
          $year = date('Y', strtotime($i . " year ago"));

          $meterMAXArrayPrevious = array();
          $meterMAXArray = array();
          foreach ($meters AS $meter) {
            //echo "SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $previousYear . "'";
            $meterMAXPrevious = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $previousYear . "'")->fetchAll();
            $meterMAX = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $year . "'")->fetchAll();
            $meterMAXArrayPrevious[] = $meterMAXPrevious[0]['reading1'];
            $meterMAXArray[] = $meterMAX[0]['reading1'];
          }

          $arraySum = array_sum($meterMAXArray) - array_sum($meterMAXArrayPrevious);

          echo "<td>" . number_format($arraySum) . "</td>";
          $i--;
        } while ($i >= 0);

  			echo "</tr>";
      }
      ?>
    </thead>
  </table>
</div>

<div class="container">
  <h1>Gas</h1>

  <table class="table">
    <thead>
      <tr>
        <th scope="col" style="width: 400px;"></th>
        <?php
        $i = 5;

        do {
          $year = date('Y', strtotime($i . " year ago"));

          echo "<th scope=\"col\">" . $year . "</th>";
          $i--;
        } while ($i >= 0);

        ?>
      </tr>
      <?php
      foreach ($locations AS $location) {
        $metersClass = new meters;
        $meters = $metersClass->allByLocationAndType($location['uid'], 'Gas');

        echo "<tr>";

        echo "<th scope=\"row\">" . $location['name'] . "</th>";

        $i = 5;
        do {
          $previousYear = date('Y', strtotime($i + 1 . " years ago"));
          $year = date('Y', strtotime($i . " year ago"));

          $meterMAXArrayPrevious = array();
          $meterMAXArray = array();
          foreach ($meters AS $meter) {
            //echo "SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $previousYear . "'";
            $meterMAXPrevious = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $previousYear . "'")->fetchAll();
            $meterMAX = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $year . "'")->fetchAll();
            $meterMAXArrayPrevious[] = $meterMAXPrevious[0]['reading1'];
            $meterMAXArray[] = $meterMAX[0]['reading1'];
          }

          $arraySum = array_sum($meterMAXArray) - array_sum($meterMAXArrayPrevious);

          echo "<td>" . number_format($arraySum) . "</td>";
          $i--;
        } while ($i >= 0);

        echo "</tr>";
      }
      ?>
    </thead>
  </table>
</div>

<div class="container">
  <h1>Water</h1>

  <table class="table">
    <thead>
      <tr>
        <th scope="col" style="width: 400px;"></th>
        <?php
        $i = 5;

        do {
          $year = date('Y', strtotime($i . " year ago"));

          echo "<th scope=\"col\">" . $year . "</th>";
          $i--;
        } while ($i >= 0);

        ?>
      </tr>
      <?php
      foreach ($locations AS $location) {
        $metersClass = new meters;
        $meters = $metersClass->allByLocationAndType($location['uid'], 'Water');

        echo "<tr>";

        echo "<th scope=\"row\">" . $location['name'] . "</th>";

        $i = 5;
        do {
          $previousYear = date('Y', strtotime($i + 1 . " years ago"));
          $year = date('Y', strtotime($i . " year ago"));

          $meterMAXArrayPrevious = array();
          $meterMAXArray = array();
          foreach ($meters AS $meter) {
            //echo "SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $previousYear . "'";
            $meterMAXPrevious = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $previousYear . "'")->fetchAll();
            $meterMAX = $db->query("SELECT MAX(reading1) AS reading1 from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '" . $year . "'")->fetchAll();
            $meterMAXArrayPrevious[] = $meterMAXPrevious[0]['reading1'];
            $meterMAXArray[] = $meterMAX[0]['reading1'];
          }

          $arraySum = array_sum($meterMAXArray) - array_sum($meterMAXArrayPrevious);

          echo "<td>" . number_format($arraySum) . "</td>";
          $i--;
        } while ($i >= 0);

        echo "</tr>";
      }
      ?>
    </thead>
  </table>
</div>
