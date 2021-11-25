<?php
if (isset($_GET['nodeUID'])) {
	$node = new node($_GET['nodeUID']);
	$location = new location($node->location);
	$pageTitle = "All readings for " . $location->name;
	$readings = readings::node_all_readings($node->uid);
} else {
	$pageTitle = "All readings";
	$readings = readings::all(100);
}


?>

<div class="container px-4 py-5">
	<h1 class="d-flex mb-5 justify-content-between align-items-center">Readings
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dashboard-dropdown">
				<a class="dropdown-item" href="export.php?type=readings&filter=all" target="_blank">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#download"/></svg>
					</span> Export Data
				</a>
			</div>
		</div>
	</h1>
	
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
					<th class="border-bottom" scope="col"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($readings AS $reading) {
					$node = new node($reading['node']);
					$location = new location($node->location);
					
					$output  = "<tr>";
					$output .= "<th class=\"text-gray-900\" scope=\"row\">" . date('Y-m-d H:i', strtotime($reading['date'])) . "</th>";
					$output .= "<td class=\"fw-bolder text-gray-500\"><a href=\"index.php?n=location&locationUID=" . $location->uid . "\">" . $location->name . "</a></td>";
					$output .= "<td class=\"fw-bolder text-gray-500\"><a href=\"index.php?n=node&nodeUID=" . $node->uid . "\">" . $node->name . "</a></td>";
					$output .= "<td class=\"fw-bolder text-gray-500\">" . $node->type . "</td>";
					$output .= "<td class=\"fw-bolder text-gray-500\">" . displayReading($reading['reading1']) . "</td>";
					$output .= "<td class=\"fw-bolder text-gray-500\">" . showHide($reading['username']) . "</td>";
					
					
					if ($_SESSION['logon'] == true) {
						$output .= "<td><a href=\"#\" class=\"text-muted\" id=\"" . $reading['uid'] . "\" onclick=\"deleteReading(this.id);\"><svg width=\"1em\" height=\"1em\" role=\"img\"><use xlink:href=\"inc/icons.svg#delete\"></use></svg></a></td>";
					} else {
						$output .= "<td></td>";
					}
					
					$output .= "</tr>";
					
					
					echo $output;
				}
				?>
			</tbody>
		</table>
	</div>
</div>
</div>

<script>
function deleteReading(this_id) {	
	var isGood=confirm('Are you sure you want to delete this reading from the database?  This action cannot be undone!');
	
	if(isGood) {
		var formData = new FormData();
		
		formData.append("readingUID", this_id);
		
		var request = new XMLHttpRequest();
		
		request.open("POST", "../actions/reading_delete.php", true);
		request.send(formData);
		
		// 4. This will be called after the response is received
		request.onload = function() {
		  if (request.status != 200) { // analyze HTTP status of the response
			alert("Something went wrong.  Please refresh this page and try again.");
			alert(`Error ${request.status}: ${request.statusText}`); // e.g. 404: Not Found
		  } else {
			location.reload();
		  }
		}
		
		request.onerror = function() {
		  alert("Request failed");
		};
		
		return false;
	}
}
</script>