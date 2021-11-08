<?php
if (isset($_GET['nodeUID'])) {
	$node = new meter($_GET['nodeUID']);
	$location = new location($node->location);
	$pageTitle = "All readings for " . $location->name;
	$readings = readings::meter_all_readings($node->uid);
} else {
	$pageTitle = "All readings";
	$readings = readings::all(100);
}


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
				 Export Data
			</a>
		</div>
	</div>
</div>

<div class="card border-0 shadow">
	<div class="card-header">
		<h2 class="fs-5 fw-bold mb-0"><?php echo $pageTitle; ?></h2>
	</div>
<div class="table-responsive">
	<table class="table align-items-center table-flush">
		<thead class="thead-light">
			<tr>
				<th class="border-bottom" scope="col">Date</th>
				<th class="border-bottom" scope="col">Location</th>
				<th class="border-bottom" scope="col">Node</th>
				<th class="border-bottom" scope="col">Type</th>
				<th class="border-bottom" scope="col">Reading</th>
				<th class="border-bottom" scope="col">Username</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($readings AS $reading) {
				$meter = new meter($reading['meter']);
				$location = new location($meter->location);
				
				$output  = "<tr>";
				$output .= "<th class=\"text-gray-900\" scope=\"row\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</th>";
				$output .= "<td class=\"fw-bolder text-gray-500\"><a href=\"index2.php?n=location&locationUID=" . $location->uid . "\">" . $location->name . "</a></td>";
				$output .= "<td class=\"fw-bolder text-gray-500\"><a href=\"index2.php?n=node&nodeUID=" . $meter->uid . "\">" . $meter->name . "</a></td>";
				$output .= "<td class=\"fw-bolder text-gray-500\">" . $meter->type . "</td>";
				$output .= "<td class=\"fw-bolder text-gray-500\">" . displayReading($reading['reading1']) . "</td>";
				$output .= "<td class=\"fw-bolder text-gray-500\">" . $reading['username'] . "</td>";
				$output .= "";
				$output .= "</tr>";
				
				echo $output;
			}
			?>
		</tbody>
	</table>
</div>