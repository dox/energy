<?php
admin_gatekeeper();

$locationsClass = new locations();
$metersClass = new meters();
$readingsClass = new readings();

if (isset($_POST['name'])) {
  if (!isset($_POST['billed'])) {
    $_POST['billed'] = 0;
  }
  if (!isset($_POST['enabled'])) {
    $_POST['enabled'] = 0;
  }

  $metersClass->create($_POST);
	echo "Meter added!";
	exit();
}

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Node: Add</h1>
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
				echo "<option value = \"" . $location['uid'] . "\">" . $location['name'] . "</option>";
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
    				echo "<option value = \"" . $type . "\">" . $type . "</option>";
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
            echo "<option value = \"" . $unit . "\">" . $unit . "</option>";
          }
          ?>
        </select>
      </div>
    </div>
  </div>
	<div class="mb-3">
		<label for="serial">Serial</label>
		<input type="text" class="form-control" id="serial" name="serial" placeholder="Serial Number" value="<?php echo $meter->serial; ?>">
	</div>
  <div class="form-check mb-3">
		<input type="checkbox" class="form-check-input" id="billed" name="billed" value="1">
		<label for="billed" class="form-check-label">Billed to tenant</label>
	</div>

  <div class="form-check mb-3">
		<input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1" checked>
		<label for="billed" class="form-check-label">Enabled</label>
	</div>

  <div class="mb-3">
    <button type="submit" class="btn btn-primary w-100">Submit</button>
  </div>

  <div id="returnMessage"></div>
</form>
