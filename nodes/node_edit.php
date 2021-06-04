<?php
admin_gatekeeper();

$locationsClass = new locations();
$metersClass = new meters();
$readingsClass = new readings();

//check if we're editing, or adding new
if (isset($_GET['nodeUID'])) {
  // editing an existing node
  $pageTitle = "Edit";
  $submitButtonAction = "nodeEdit()";

  $meter = new meter($_GET['nodeUID']);
  $location = new location($meter->location);

} else {
  // we're adding a new node
  $pageTitle = "Add New";
  $submitButtonAction = "nodeAdd()";

  $meter = new meter();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Node: <?php echo $pageTitle; ?></h1>
</div>

<form method="post" id="meterUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<div class="mb-3">
		<label for="name">Name</label>
		<input type="text" class="form-control" id="name" name="name" placeholder="Meter Name" value="<?php echo $meter->name; ?>">
	</div>
  <div class="mb-3">
		<label for="location">Location</label>
		<select class="form-select" id="location" name="location">
			<?php
			foreach ($locationsClass->all() AS $location) {
        if ($location['uid'] == $meter->location) {
          $selected = " selected";
        } else {
          $selected = "";
        }
				echo "<option value = \"" . $location['uid'] . "\" " . $selected . ">" . $location['name'] . "</option>";
			}
			?>
		</select>
	</div>
  <div class="row">
    <div class="col-6">
      <div class="mb-3">
    		<label for="type">Type</label>
    		<select class="form-select" id="type" name="type">
    			<?php
    			foreach (explode(",", $settingsClass->value('node_types')) AS $type) {
    				if ($type == $meter->type) {
    					$selected = " selected";
    				} else {
    					$selected = " ";
    				}
    				echo "<option value = \"" . $type . "\" " . $selected . ">" . $type . "</option>";
    			}
    			?>
    		</select>
    	</div>
    </div>
    <div class="col-6">
      <div class="mb-3">
        <label for="unit">Unit</label>
        <select class="form-select" id="unit" name="unit">
          <?php
          foreach (explode(",", $settingsClass->value('node_units')) AS $unit) {
            if ($unit == $meter->unit) {
              $selected = " selected";
            } else {
              $selected = " ";
            }
            echo "<option value = \"" . $unit . "\" " . $selected . ">" . $unit . "</option>";
          }
          ?>
        </select>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-6">
      <div class="mb-3">
    		<label for="serial">Serial</label>
    		<input type="text" class="form-control" id="serial" name="serial" placeholder="Serial Number" value="<?php echo $meter->serial; ?>">
    	</div>
      <div class="form-check mb-3">
    		<input type="checkbox" class="form-check-input" id="billed" name="billed" value="1" <?php if ($meter->billed == "1") { echo " checked";} ?>>
    		<label for="billed" class="form-check-label">Billed to tenant</label>
    	</div>
    </div>
    <div class="col-6">
      <div class="mb-3">
    		<label for="mprn">MPRN</label>
    		<input type="text" class="form-control" id="mprn" name="mprn" placeholder="MPRN" value="<?php echo $meter->mprn; ?>">
    	</div>
    </div>
  </div>

  <div class="mb-3">
    <label for="retention_days">Retention Policy</label>
    <select class="form-select" id="retention_days" name="retention_days">
      <option value="0" <?php if ($meter->retention_days == 0) { echo " selected"; } ?>>Forever (do not delete readings)</option>
      <option value="1" <?php if ($meter->retention_days == 1) { echo " selected"; } ?>>1 day</option>
      <option value="7" <?php if ($meter->retention_days == 7) { echo " selected"; } ?>>1 week</option>
      <option value="31" <?php if ($meter->retention_days == 31) { echo " selected"; } ?>>1 month</option>
      <option value="365" <?php if ($meter->retention_days == 365) { echo " selected"; } ?>>1 year</option>
      <option value="1825" <?php if ($meter->retention_days == 1825) { echo " selected"; } ?>>5 years</option>
      <option value="3650" <?php if ($meter->retention_days == 3650) { echo " selected"; } ?>>10 years</option>
    </select>
    <div id="retention_daysHelp" class="form-text">Readings older than this duration will be automatically deleted.</div>
  </div>

  <div class="form-check mb-3">
		<input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1" <?php if ($meter->enabled == "1") { echo " checked";} ?>>
		<label for="billed" class="form-check-label">Enabled</label>
	</div>

  <input type="hidden" id="geo" name="geo" value="<?php $meter->geoLocation(); ?>">
  <div id="map" style="width: 100%; height: 500px"></div>

  <div class="mb-3">
    <?php

    ?>
    <button type="button" onclick="<?php echo $submitButtonAction; ?>" class="btn btn-primary w-100">Submit</button>
    <input type="hidden" id="uid" name="uid" value="<?php echo $meter->uid; ?>">
  </div>

  <div id="returnMessage"></div>
</form>

<script>
var map = L.map('map').setView([<?php echo $meter->geoLocation(); ?>], 18);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

<?php
if (isset($meter->geo)) {
  $output  = "L.marker([" . $meter->geoLocation() . "]).addTo(map)";
  $output .= ".bindPopup('" . escape($meter->name) . "')";
  $output .= ".openPopup();";

  echo $output;
}
?>
var popup = L.popup();

function onMapClick(e) {
  popup
    .setLatLng(e.latlng)
    .setContent("New Node Location: " + e.latlng.toString())
    .openOn(map);

  document.getElementById("geo").value = e.latlng.lat + ',' + e.latlng.lng;
}

map.on('click', onMapClick);
</script>
