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
	<a href="index.php?n=meter_add" class="btn btn-sm btn-outline-secondary float-right">Add New Meter</a>
	<?php
	$output = "";
	foreach ($locations AS $location) {
		$meters = new meters;
		$meters = $meters->allByLocation($location['uid']);
		
		$output .= "<h2>" . $location['name'] . "</h2>";
		$output .= "<div class=\"row\">";
		
		foreach ($meters AS $meter) {
			$output .= "<div class=\"col-md-4\">";
			$output .= "<div class=\"card mb-4 shadow-sm\">";
			if (isset($meter['photograph'])) {
				$img = "uploads/" . $meter['photograph'];
				$output .= "<img class=\"bd-placeholder-img card-img-top\" src=\"" . $img . "\" width=\"100%\" height=\"225\">";
			} else {
				$output .= "<svg class=\"bd-placeholder-img card-img-top\" width=\"100%\" height=\"225\" xmlns=\"http://www.w3.org/2000/svg\" preserveAspectRatio=\"xMidYMid slice\" focusable=\"false\" role=\"img\" aria-label=\"Placeholder: Thumbnail\"><title>Placeholder</title><rect width=\"100%\" height=\"100%\" fill=\"#55595c\"/><text x=\"50%\" y=\"50%\" fill=\"#eceeef\" dy=\".3em\">Thumbnail</text></svg>";
			}
			$output .= "<div class=\"card-body\">";
			$output .= "<p class=\"card-text\">" . $meter['name'] . "</p>";
			$output .= "<div class=\"d-flex justify-content-between align-items-center\">";
			$output .= "<div class=\"btn-group\">";
			$output .= "<a href=\"index.php?n=meter&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\">View</a>";
			$output .= "<a href=\"index.php?n=meter_edit&meterUID=" . $meter['uid'] . "\" class=\"btn btn-sm btn-outline-secondary\">Edit</a>";
			$output .= "</div>";
			$output .= "<small class=\"text-muted\">" . $meter['type'] . "</small>";
			$output .= "</div>";
			$output .= "</div>";
			$output .= "</div>";
			$output .= "</div>";
		}
		
		$output .= "</div>";
	}
	
	echo $output;
	?>
</div>
