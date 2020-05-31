<?php
$locations = new locations;
$locations = $locations->all();
?>

<div class="container">
	<div class="row">
		<h1>Locations</h1>
	</div>
	<div class="row">
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
</div>