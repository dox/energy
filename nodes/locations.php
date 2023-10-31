<?php
$locationsClass = new locations();
$locations = $locationsClass->all();
?>
<div class="py-4">
	<div class="dropdown">
		<button class="btn btn-gray-800 d-inline-flex align-items-center me-2 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
			</svg>
			 New Task
		</button>
		<div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
			<a class="dropdown-item d-flex align-items-center" href="#">
				<span class="sidebar-icon">
					<svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#download"/></svg>
				</span>
				 Add Location
			</a>
		</div>
		<div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
			<a class="dropdown-item d-flex align-items-center" href="#">
				<span class="sidebar-icon">
					<svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#download"/></svg>
				</span>
				 Export Data
			</a>
		</div>
	</div>
</div>

<div class="container px-4 py-5">
	map
	<div id="map" style="width: 100%; height: 500px"></div>
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
					$output .= "<th class=\"text-gray-900\" scope=\"row\"><a href=\"index2.php?n=location&locationUID=" . $location->uid . "\">" . $location->name. "</th>";
					$output .= "<td class=\"fw-bolder text-gray-500\">" . $location->geo . "</td>";
					$output .= "</tr>";
					
					echo $output;
				}
				?>
			</tbody>
		</table>
	</div>
</div>