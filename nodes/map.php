<?php
$siteClass = new site();
$locationsClass = new locations();

if (isset($_POST['name'])) {
	$locationsClass->create($_POST);
}

$locations = $locationsClass->all();
?>

<div class="container px-4 py-5">
	<h1 class="d-flex mb-5 justify-content-between align-items-center">Map
		<div class="dropdown">
			<button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
			<div class="dropdown-menu dashboard-dropdown">
				<a class="dropdown-item me-2 <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=location_add"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Add Location</a>
			</div>
		</div>
	</h1>
	
	<div id="map" style="width: 100%; height: 600px"></div>
</div>

<script>
	var map = L.map('map').setView([<?php echo $settingsClass->value("site_geolocation"); ?>], 18);
	
	var locations = [<?php echo implode(",", $siteClass->geoMarkersOfNodes()); ?>];
	
	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		//attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);
	
	for (var i = 0; i < locations.length; i++) {
	  L.marker([locations[i][1], locations[i][2]]).addTo(map)
		.bindPopup(locations[i][0], {closeOnClick: false, autoClose: false})

	}
	</script>