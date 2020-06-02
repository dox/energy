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
    </tr>
</thead>
	<tbody>
		<?php
		foreach ($locations AS $location) {
			$metersClass = new meters;
			$meters = $metersClass->allByLocation($location['uid'], 'Electric');
			
			$meterMAXArray2019 = null;
			$meterMAXArray2018 = null;
			$meterMAXArray2017 = null;
			$meterMAXArray2016 = null;
			$meterMAXArray2015 = null;
			
			foreach ($meters AS $meter) {
				$meterMAX2019 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2019' ORDER BY reading1 DESC");
				$meterMAX2018 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2018' ORDER BY reading1 DESC");
				$meterMAX2017 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2017' ORDER BY reading1 DESC");
				$meterMAX2016 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2016' ORDER BY reading1 DESC");
				$meterMAX2015 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2015' ORDER BY reading1 DESC");
				$meterMAXArray2019[] = $meterMAX2019['reading1'];
				$meterMAXArray2018[] = $meterMAX2018['reading1'];
				$meterMAXArray2017[] = $meterMAX2017['reading1'];
				$meterMAXArray2016[] = $meterMAX2016['reading1'];
				$meterMAXArray2015[] = $meterMAX2015['reading1'];
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
			
			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . $location['name'] . "</th>";
			$output .= "<td>" . $arraySum2015 . "</td>";
			$output .= "<td>" . $arraySum2016 . "</td>";
			$output .= "<td>" . $arraySum2017 . "</td>";
			$output .= "<td>" . $arraySum2018 . "</td>";
			$output .= "<td>" . $arraySum2019 . "</td>";
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
    </tr>
</thead>
	<tbody>
		<?php
		foreach ($locations AS $location) {
			$metersClass = new meters;
			$meters = $metersClass->allByLocation($location['uid'], 'Gas');
			
			$meterMAXArray2019 = null;
			$meterMAXArray2018 = null;
			$meterMAXArray2017 = null;
			$meterMAXArray2016 = null;
			$meterMAXArray2015 = null;
			
			foreach ($meters AS $meter) {
				$meterMAX2019 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2019' ORDER BY reading1 DESC");
				$meterMAX2018 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2018' ORDER BY reading1 DESC");
				$meterMAX2017 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2017' ORDER BY reading1 DESC");
				$meterMAX2016 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2016' ORDER BY reading1 DESC");
				$meterMAX2015 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2015' ORDER BY reading1 DESC");
				$meterMAXArray2019[] = $meterMAX2019['reading1'];
				$meterMAXArray2018[] = $meterMAX2018['reading1'];
				$meterMAXArray2017[] = $meterMAX2017['reading1'];
				$meterMAXArray2016[] = $meterMAX2016['reading1'];
				$meterMAXArray2015[] = $meterMAX2015['reading1'];
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
			
			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . $location['name'] . "</th>";
			$output .= "<td>" . $arraySum2015 . "</td>";
			$output .= "<td>" . $arraySum2016 . "</td>";
			$output .= "<td>" . $arraySum2017 . "</td>";
			$output .= "<td>" . $arraySum2018 . "</td>";
			$output .= "<td>" . $arraySum2019 . "</td>";
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
    </tr>
</thead>
	<tbody>
		<?php
		foreach ($locations AS $location) {
			$metersClass = new meters;
			$meters = $metersClass->allByLocation($location['uid'], 'Water');
			
			$meterMAXArray2019 = null;
			$meterMAXArray2018 = null;
			$meterMAXArray2017 = null;
			$meterMAXArray2016 = null;
			$meterMAXArray2015 = null;
			
			foreach ($meters AS $meter) {
				$meterMAX2019 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2019' ORDER BY reading1 DESC");
				$meterMAX2018 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2018' ORDER BY reading1 DESC");
				$meterMAX2017 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2017' ORDER BY reading1 DESC");
				$meterMAX2016 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2016' ORDER BY reading1 DESC");
				$meterMAX2015 = $db->rawQueryOne("SELECT * from readings WHERE meter = '" . $meter['uid'] . "' AND YEAR(date) = '2015' ORDER BY reading1 DESC");
				$meterMAXArray2019[] = $meterMAX2019['reading1'];
				$meterMAXArray2018[] = $meterMAX2018['reading1'];
				$meterMAXArray2017[] = $meterMAX2017['reading1'];
				$meterMAXArray2016[] = $meterMAX2016['reading1'];
				$meterMAXArray2015[] = $meterMAX2015['reading1'];
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
			
			$output  = "<tr>";
			$output .= "<th scope=\"row\">" . $location['name'] . "</th>";
			$output .= "<td>" . $arraySum2015 . "</td>";
			$output .= "<td>" . $arraySum2016 . "</td>";
			$output .= "<td>" . $arraySum2017 . "</td>";
			$output .= "<td>" . $arraySum2018 . "</td>";
			$output .= "<td>" . $arraySum2019 . "</td>";
			$output .= "</tr>";
			
			echo $output;
		}
		?>
  </tbody>
</table>
</div>