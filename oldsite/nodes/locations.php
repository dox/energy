<?php
$locations = new locations;
$locations = $locations->all();
?>

<div class="container">
	<h1 class="display-4">Locations</h1>
	<?php
	$output = "<ul>";
	foreach ($locations AS $location) {
		$meters = new meters;
		$meters = $meters->allByLocation($location['uid']);

		$output .= "<li><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a>";
		$output .= " <small class=\"text-muted\">(" . count($meters) . " meters)</small>";

		if (isset($location['description'])) {
			$output .= "<small> - " . $location['description'] . "</small>";
		}

		$output .= "</li>";
	}
	$output .= "</ul>";
	echo $output;
	?>
</div>
