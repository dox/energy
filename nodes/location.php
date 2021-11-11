<?php
$location = new location($_GET['locationUID']);
$readingsClass = new readings();
$metersClass = new meters();
$meters = $location->allnodes("all");
?>

<div class="container px-4 py-5">
	<h1 class="d-flex justify-content-between align-items-center"><?php echo $location->name; ?>
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dashboard-dropdown">
				<a class="dropdown-item me-2 <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=node_edit"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Add Node</a>
				<a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=location_edit&locationUID=<?php echo $location->uid; ?>"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg> Edit Location</a>
				<a class="dropdown-item me-2<?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="#" data-bs-toggle="modal" data-bs-target="#deleteMeterModal"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#refuse"/></svg> Delete Location</a>
				
				<a class="dropdown-item me-2" href="export.php?type=location&filter=<?php echo $location->uid; ?>" target="_blank">
					<span class="sidebar-icon">
						<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#download"/></svg>
					</span> Export Data
				</a>
			</div>
		</div>
	</h1>
</div>

<div class="container px-4 py-5">
	graph
	
	<div class="row">
		<div class="col-lg-4 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<div class="feature-icon bg-danger bg-gradient">
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#electric"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1">Electricity</h3>
							<h4 class="fw-extrabold mb-1"><?php echo array_sum($location->consumptionBetweenDatesByMonth("electric")) . " unit"; ?></h4>
						</div>
					</div>
					<span class="text-success fw-bolder me-1">22%</span> Since last month
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<div class="feature-icon bg-warning bg-gradient">
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#gas"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1">Gas</h3>
							<h4 class="fw-extrabold mb-1"><?php echo array_sum($location->consumptionBetweenDatesByMonth("gas")) . " unit"; ?></h4>
						</div>
					</div>
					<span class="text-success fw-bolder me-1">22%</span> Since last month
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-12 mb-3">
			<div class="card shadow">
				<div class="card-body">
					<div class="row">
						<div class="col-3">
							<div class="feature-icon bg-primary bg-gradient">
								<svg class="bi" width="1em" height="1em"><use xlink:href="inc/icons.svg#water"/></svg>
							</div>
						</div>
						<div class="col-9">
							<h3 class="mb-1">Water</h3>
							<h4 class="fw-extrabold mb-1"><?php echo array_sum($location->consumptionBetweenDatesByMonth("water")) . " unit"; ?></h4>
						</div>
					</div>
					<span class="text-success fw-bolder me-1">22%</span> Since last month
				</div>
			</div>
		</div>
	</div>
</div>

<div class="b-example-divider"></div>

<div class="container px-4 py-5">
	<?php
	echo $metersClass->meterTable($meters);
	?>
</div>

<div id="map" style="width: 100%; height: 500px"></div>



<script>
var map = L.map('map').setView([<?php echo $location->geoLocation(); ?>], 18);

var locations = [<?php echo implode(",", $location->geoMarkersOfNodes()); ?>];

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	//attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

for (var i = 0; i < locations.length; i++) {
  L.marker([locations[i][1], locations[i][2]]).addTo(map)
	.bindPopup(locations[i][0], {closeOnClick: false, autoClose: false})
	.openPopup()
}
</script>
