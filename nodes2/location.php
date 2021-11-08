<?php
$location = new location($_GET['locationUID']);
$readingsClass = new readings();
$metersClass = new meters();
$meters = $location->allnodes("all");


$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#add\"/></svg> Add Meter", "value" => "onclick=\"location.href='index.php?n=meter_add'\"");


?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#locations"/></svg> Location: <?php echo $location->name; ?></h1>
  <div class="btn-toolbar mb-2 mb-md-0">
	<div class="btn-toolbar mb-2 mb-md-0">
	  <div class="dropdown">
		<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="title_dropdown" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
		<ul class="dropdown-menu" aria-labelledby="title_dropdown">
		  <li><a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=node_edit"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Add Node</a></li>
		  <li><a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=location_edit&locationUID=<?php echo $location->uid; ?>"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg> Edit Location</a></li>
		  <li><a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="#" data-bs-toggle="modal" data-bs-target="#deleteMeterModal"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#refuse"/></svg> Delete Location</a></li>
		</ul>
	  </div>
	</div>
  </div>
</div>

<div id="map" style="width: 100%; height: 500px"></div>

<?php
echo $metersClass->meterTable($meters);
?>

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
