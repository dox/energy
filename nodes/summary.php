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
      <th scope="col">2015</th>
      <th scope="col">2016</th>
      <th scope="col">2017</th>
      <th scope="col">2018</th>
      <th scope="col">2019</th>
      <th scope="col">2020</th>
    </tr>
</thead>
	<tbody>
		<?php
		foreach ($locations AS $location) {
			$metersClass = new meters;
			$meters = $metersClass->allByLocationAndType($location['uid'], 'Electric');

			$meterMAXArray2020 = null;
			$meterMAXArray2019 = null;
			$meterMAXArray2018 = null;
			$meterMAXArray2017 = null;
			$meterMAXArray2016 = null;
			$meterMAXArray2015 = null;

			foreach ($meters AS $meter) {
				$meterMAX2020 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2020' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2019 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2019' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2018 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2018' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2017 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2017' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2016 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2016' ORDER BY reading1 DESC")->fetchAll();
        $meterMAX2015 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2015' ORDER BY reading1 DESC")->fetchAll();
        $meterMAX2014 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2014' ORDER BY reading1 DESC")->fetchAll();
				$meterMAXArray2020[] = $meterMAX2020[0]['reading1'];
				$meterMAXArray2019[] = $meterMAX2019[0]['reading1'];
				$meterMAXArray2018[] = $meterMAX2018[0]['reading1'];
				$meterMAXArray2017[] = $meterMAX2017[0]['reading1'];
				$meterMAXArray2016[] = $meterMAX2016[0]['reading1'];
        $meterMAXArray2015[] = $meterMAX2015[0]['reading1'];
        $meterMAXArray2014[] = $meterMAX2014[0]['reading1'];
			}

			if (isset($meterMAXArray2020)) {
				$arraySum2020 = array_sum($meterMAXArray2020);
			} else {
				$arraySum2020 = 0;
			}
			if (isset($meterMAXArray2019)) {
				$arraySum2019 = array_sum($meterMAXArray2019);
			} else {
				$arraySum2019 = 0;
			}
			if (isset($meterMAXArray2018)) {
				$arraySum2018 = array_sum($meterMAXArray2018);
			} else {
				$arraySum2018 = 0;
			}
			if (isset($meterMAXArray2017)) {
				$arraySum2017 = array_sum($meterMAXArray2017);
			} else {
				$arraySum2017 = 0;
			}
			if (isset($meterMAXArray2016)) {
				$arraySum2016 = array_sum($meterMAXArray2016);
			} else {
				$arraySum2016 = 0;
			}
			if (isset($meterMAXArray2015)) {
				$arraySum2015 = array_sum($meterMAXArray2015);
			} else {
				$arraySum2015 = 0;
			}

      //calculate difference/consumption
      $arraySum2020 = $arraySum2020 - $arraySum2019;
      $arraySum2019 = $arraySum2019 - $arraySum2018;
      $arraySum2018 = $arraySum2018 - $arraySum2017;
      $arraySum2017 = $arraySum2017 - $arraySum2016;
      $arraySum2016 = $arraySum2016 - $arraySum2015;
      $arraySum2015 = $arraySum2015 - $arraySum2014;

			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . $location['name'] . "</th>";
      $output .= "<td>" . number_format($arraySum2015) . "</td>";
			$output .= "<td>" . number_format($arraySum2016) . "</td>";
			$output .= "<td>" . number_format($arraySum2017) . "</td>";
			$output .= "<td>" . number_format($arraySum2018) . "</td>";
			$output .= "<td>" . number_format($arraySum2019) . "</td>";
      $output .= "<td>" . number_format($arraySum2020) . "</td>";
			$output .= "</tr>";

			echo $output;
		}
		?>
  </tbody>
</table>
</div>

<div class="container">
<h1>Gas</h1>
<table class="table">
  <thead>
    <tr>
      <th scope="col" style="width: 400px;"></th>
      <th scope="col">2015</th>
      <th scope="col">2016</th>
      <th scope="col">2017</th>
      <th scope="col">2018</th>
      <th scope="col">2019</th>
      <th scope="col">2020</th>
    </tr>
</thead>
	<tbody>
		<?php
		foreach ($locations AS $location) {
			$metersClass = new meters;
			$meters = $metersClass->allByLocationAndType($location['uid'], 'Gas');

			$meterMAXArray2020 = null;
			$meterMAXArray2019 = null;
			$meterMAXArray2018 = null;
			$meterMAXArray2017 = null;
			$meterMAXArray2016 = null;
			$meterMAXArray2015 = null;

			foreach ($meters AS $meter) {
        $meterMAX2020 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2020' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2019 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2019' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2018 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2018' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2017 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2017' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2016 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2016' ORDER BY reading1 DESC")->fetchAll();
        $meterMAX2015 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2015' ORDER BY reading1 DESC")->fetchAll();
        $meterMAX2014 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2014' ORDER BY reading1 DESC")->fetchAll();
				$meterMAXArray2020[] = $meterMAX2020[0]['reading1'];
				$meterMAXArray2019[] = $meterMAX2019[0]['reading1'];
				$meterMAXArray2018[] = $meterMAX2018[0]['reading1'];
				$meterMAXArray2017[] = $meterMAX2017[0]['reading1'];
				$meterMAXArray2016[] = $meterMAX2016[0]['reading1'];
        $meterMAXArray2015[] = $meterMAX2015[0]['reading1'];
        $meterMAXArray2014[] = $meterMAX2014[0]['reading1'];
			}

			if (isset($meterMAXArray2020)) {
				$arraySum2020 = array_sum($meterMAXArray2020);
			} else {
				$arraySum2020 = 0;
			}
			if (isset($meterMAXArray2019)) {
				$arraySum2019 = array_sum($meterMAXArray2019);
			} else {
				$arraySum2019 = 0;
			}
			if (isset($meterMAXArray2018)) {
				$arraySum2018 = array_sum($meterMAXArray2018);
			} else {
				$arraySum2018 = 0;
			}
			if (isset($meterMAXArray2017)) {
				$arraySum2017 = array_sum($meterMAXArray2017);
			} else {
				$arraySum2017 = 0;
			}
			if (isset($meterMAXArray2016)) {
				$arraySum2016 = array_sum($meterMAXArray2016);
			} else {
				$arraySum2016 = 0;
			}
			if (isset($meterMAXArray2015)) {
				$arraySum2015 = array_sum($meterMAXArray2015);
			} else {
				$arraySum2015 = 0;
			}

      //calculate difference/consumption
      $arraySum2020 = $arraySum2020 - $arraySum2019;
      $arraySum2019 = $arraySum2019 - $arraySum2018;
      $arraySum2018 = $arraySum2018 - $arraySum2017;
      $arraySum2017 = $arraySum2017 - $arraySum2016;
      $arraySum2016 = $arraySum2016 - $arraySum2015;
      $arraySum2015 = $arraySum2015 - $arraySum2014;

			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . $location['name'] . "</th>";
      $output .= "<td>" . number_format($arraySum2015) . "</td>";
			$output .= "<td>" . number_format($arraySum2016) . "</td>";
			$output .= "<td>" . number_format($arraySum2017) . "</td>";
			$output .= "<td>" . number_format($arraySum2018) . "</td>";
			$output .= "<td>" . number_format($arraySum2019) . "</td>";
      $output .= "<td>" . number_format($arraySum2020) . "</td>";
			$output .= "</tr>";

			echo $output;
		}
		?>
  </tbody>
</table>
</div>

<div class="container">
<h1>Water</h1>
<table class="table">
  <thead>
    <tr>
      <th scope="col" style="width: 400px;"></th>
      <th scope="col">2015</th>
      <th scope="col">2016</th>
      <th scope="col">2017</th>
      <th scope="col">2018</th>
      <th scope="col">2019</th>
      <th scope="col">2020</th>
    </tr>
</thead>
	<tbody>
		<?php
		foreach ($locations AS $location) {
			$metersClass = new meters;
			$meters = $metersClass->allByLocationAndType($location['uid'], 'Water');

			$meterMAXArray2020 = null;
			$meterMAXArray2019 = null;
			$meterMAXArray2018 = null;
			$meterMAXArray2017 = null;
			$meterMAXArray2016 = null;
			$meterMAXArray2015 = null;

			foreach ($meters AS $meter) {
        $meterMAX2020 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2020' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2019 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2019' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2018 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2018' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2017 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2017' ORDER BY reading1 DESC")->fetchAll();
				$meterMAX2016 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2016' ORDER BY reading1 DESC")->fetchAll();
        $meterMAX2015 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2015' ORDER BY reading1 DESC")->fetchAll();
        $meterMAX2014 = $db->query("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2014' ORDER BY reading1 DESC")->fetchAll();
				$meterMAXArray2020[] = $meterMAX2020[0]['reading1'];
				$meterMAXArray2019[] = $meterMAX2019[0]['reading1'];
				$meterMAXArray2018[] = $meterMAX2018[0]['reading1'];
				$meterMAXArray2017[] = $meterMAX2017[0]['reading1'];
				$meterMAXArray2016[] = $meterMAX2016[0]['reading1'];
				$meterMAXArray2014[] = $meterMAX2015[0]['reading1'];
			}

			if (isset($meterMAXArray2020)) {
				$arraySum2020 = array_sum($meterMAXArray2020);
			} else {
				$arraySum2020 = 0;
			}
			if (isset($meterMAXArray2019)) {
				$arraySum2019 = array_sum($meterMAXArray2019);
			} else {
				$arraySum2019 = 0;
			}
			if (isset($meterMAXArray2018)) {
				$arraySum2018 = array_sum($meterMAXArray2018);
			} else {
				$arraySum2018 = 0;
			}
			if (isset($meterMAXArray2017)) {
				$arraySum2017 = array_sum($meterMAXArray2017);
			} else {
				$arraySum2017 = 0;
			}
			if (isset($meterMAXArray2016)) {
				$arraySum2016 = array_sum($meterMAXArray2016);
			} else {
				$arraySum2016 = 0;
			}
			if (isset($meterMAXArray2015)) {
				$arraySum2015 = array_sum($meterMAXArray2015);
			} else {
				$arraySum2015 = 0;
			}

      //calculate difference/consumption
      $arraySum2020 = $arraySum2020 - $arraySum2019;
      $arraySum2019 = $arraySum2019 - $arraySum2018;
      $arraySum2018 = $arraySum2018 - $arraySum2017;
      $arraySum2017 = $arraySum2017 - $arraySum2016;
      $arraySum2016 = $arraySum2016 - $arraySum2015;
      $arraySum2015 = $arraySum2015 - $arraySum2014;

      $output  = "<tr>";
			$output .= "<th scope=\"row\">" . $location['name'] . "</th>";
      $output .= "<td>" . number_format($arraySum2015) . "</td>";
			$output .= "<td>" . number_format($arraySum2016) . "</td>";
			$output .= "<td>" . number_format($arraySum2017) . "</td>";
			$output .= "<td>" . number_format($arraySum2018) . "</td>";
			$output .= "<td>" . number_format($arraySum2019) . "</td>";
      $output .= "<td>" . number_format($arraySum2020) . "</td>";
			$output .= "</tr>";
      
			echo $output;
		}
		?>
  </tbody>
</table>
</div>
