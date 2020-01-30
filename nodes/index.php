<?php
$locations = new locations;
$locations = $locations->all();


?>
<section class="jumbotron text-center">
	<div class="container">
		<h1>SEH Meter Readings</h1>
		<p class="lead text-muted">A simple system to record, review and report on utility meter readings.</p>
		<p><a href="index.php?n=site" class="btn btn-primary my-2">Whole Site Usage</a> <a href="index.php?n=locations" class="btn btn-secondary my-2">Locations</a></p>
	</div>
</section>


<div class="container">
	<?php
	$output = "";
	foreach ($locations AS $location) {
		$metersClass = new meters;
		$meters = $metersClass->allByLocation($location['uid']);
		
		$output .= "<a href=\"index.php?n=location_disabled&locationUID=" . $location['uid'] . "\" class=\"btn btn-secondary btn-sm float-right\" role=\"button\">View Disabled Meters here</a>";
		$output .= "<h3><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a></h3>";
		$output .= "<div class=\"row\">";
		
		foreach ($meters AS $meter) {
			if ($meter['enabled'] == 1) {
				$output .= $metersClass->displayMeterCard($meter['uid']);
			}
		}
		
		$output .= "</div>";
	}
	
	echo $output;
	?>
</div>
