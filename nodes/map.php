<?php
$locationsClass = new locations();
$locations = $locationsClass->all();
?>

<div class="container px-4 py-5">
	<h1 class="d-flex justify-content-between align-items-center">Map
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dashboard-dropdown">
				<a class="dropdown-item me-2 <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=node_edit"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Add Location</a>
				
				<a class="dropdown-item me-2" href="export.php?type=location&filter=all" target="_blank">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#download"/></svg>
					</span> Export Data
				</a>
			</div>
		</div>
	</h1>
</div>

<div class="container px-4 py-5">
	Coming soon...
	<div id="map" style="width: 100%; height: 100px"></div>

</div>

<div class="container px-4 py-5">
	<div class="table-responsive">
		<table class="table align-items-center table-flush">
			<thead class="thead-light">
				<tr>
					<th class="border-bottom" scope="col">Name</th>
					<th class="border-bottom" scope="col">Address</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($locations AS $location) {
					$location = new location($location['uid']);
					
					$output  = "<tr>";
					$output .= "<th class=\"text-gray-900\" scope=\"row\"><a href=\"index.php?n=location&locationUID=" . $location->uid . "\">" . $location->name. "</th>";
					$output .= "<td class=\"fw-bolder text-gray-500\">" . $location->geo . "</td>";
					$output .= "</tr>";
					
					echo $output;
				}
				?>
			</tbody>
		</table>
	</div>
</div>