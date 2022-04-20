<?php
$siteClass = new site();

if (isset($_POST['name'])) {
	$locations = new locations();
	$locations->create($_POST);
}
?>

<div class="container px-4 py-5">
	<?php
	$title     = "Map";
	$actions[] = array('name' => 'Add Location', 'icon' => 'nodes', 'href' => 'index.php?n=location_add');
	
	echo pageHeader($title, $actions);
	?>
	
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
	.bindPopup(locations[i][0])
}
</script>