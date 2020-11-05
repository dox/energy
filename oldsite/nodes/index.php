<?php
$locations = new locations;
$locations = $locations->all();


?>
<section class="text-center">
	<div class="container">
		<h1 class="display-1">Utility Meter Readings</h1>
		<p class="lead text-muted">A system to record, review and report on utility meter readings.</p>
		<p><a href="index.php?n=site" class="btn btn-primary my-2">Whole Site Usage</a> <a href="index.php?n=locations" class="btn btn-secondary my-2">Locations</a></p>
	</div>
</section>


<div class="container">
	<?php
	$output = "";
	foreach ($locations AS $location) {
		$metersClass = new meters;
		$meters = $metersClass->allByLocation($location['uid']);

		$output .= "<a href=\"index.php?n=location_disabled&locationUID=" . $location['uid'] . "\" class=\"btn btn-secondary btn-sm float-right\" role=\"button\" aria-label=\"View Disabled Meters for " . $location['name'] . "\">View Disabled Meters here</a>";
		$output .= "<h1 class=\"display-4\"><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a></h1>";

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

		foreach ($meters AS $meter) {
			$output .= "<tr>";
			$output .= "<th scope=\"row\">" . $meter['name'] . "</th>";
			$output .= "<td>" . $meter['type'] . "</td>";
			$output .= "</tr>";
		}


		$output .= "</tbody>";
		$output .= "</table>";

		foreach ($meters AS $meter) {
			if ($meter['enabled'] == 1) {
				$output .= $metersClass->displayMeterCard($meter['uid']);
			}
		}

		//$output .= "</div>";
	}

	echo $output;
	?>
</div>
