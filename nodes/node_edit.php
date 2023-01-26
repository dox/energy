<?php
admin_gatekeeper();

$locationsClass = new locations();
$nodesClass = new nodes();
$readingsClass = new readings();

$node = new node($_GET['nodeUID']);
$locationUID = $node->location;

$location = new location($node->location);

if (isset($_POST['uid'])) {
    $node->update($_POST);
    $node = new node($_GET['nodeUID']);
    $locationUID = $node->location;
}
?>

<div class="container px-4 py-5">
  <?php
  $title     = "Node Edit: " . $node->name;
  
  echo pageHeader($title);
  ?>

  <form method="post" id="nodeUpdate" action="index.php?n=node_edit&nodeUID=<?php echo $node->uid; ?>">
    <div class="mb-3">
      <label for="name">Name</label>
      <input type="text" class="form-control" id="name" name="name" placeholder="Node Name" value="<?php echo $node->name; ?>">
    </div>
    <div class="mb-3">
      <label for="location">Location</label>
      <select class="form-select" id="location" name="location">
        <?php
        foreach ($locationsClass->all() AS $location) {
          if ($location['uid'] == $locationUID) {
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
      <div class="col-lg-3 col-6">
        <div class="mb-3">
          <label for="type">Type</label>
          <select class="form-select" id="type" name="type">
            <?php
            foreach (explode(",", $settingsClass->value('node_types')) AS $type) {
              if ($type == $node->type) {
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
      <div class="col-lg-3 col-6">
        <div class="mb-3">
          <label for="unit">Unit</label>
          <select class="form-select" id="unit" name="unit">
            <?php
            foreach (explode(",", $settingsClass->value('node_units')) AS $unit) {
              if ($unit == $node->unit) {
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
      <div class="col-lg-3 col-6">
        <div class="mb-3">
          <label for="supplier">Supplier</label>
          <input class="form-control" id="supplier" name="supplier" list="suppliers" value="<?php echo $node->supplier; ?>" />
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="mb-3">
          <label for="account_no">Account #</label>
          <input class="form-control" id="account_no" name="account_no" value="<?php echo $node->account_no; ?>" />
        </div>
      </div>
    </div>
  
    <div class="row">
      <div class="col-6">
        <div class="mb-3">
          <label for="serial">Serial</label>
          <input type="text" class="form-control" id="serial" name="serial" placeholder="Serial Number" value="<?php echo $node->serial; ?>">
        </div>
      </div>
      <div class="col-6">
        <div class="mb-3">
          <label for="mprn">MPRN</label>
          <input type="text" class="form-control" id="mprn" name="mprn" placeholder="MPRN" value="<?php echo $node->mprn; ?>">
        </div>
      </div>
    </div>
  
    <div class="row">
      <div class="col-6">
        <div class="mb-3">
          <label for="address">Address</label>
          <textarea class="form-control" id="address" name="address" rows="4" placeholder="Address"><?php echo $node->address; ?></textarea>
        </div>
      </div>
      <div class="col-6">
        <div class="form-check mb-3">
          <input type="checkbox" class="form-check-input" id="billed" name="billed" value="1" <?php if ($node->billed == "1") { echo " checked";} ?>>
          <label for="billed" class="form-check-label">Billed to tenant</label>
        </div>
  
        <label for="retention_days">Retention Policy</label>
        <select class="form-select" id="retention_days" name="retention_days">
          <option value="0" <?php if ($node->retention_days == 0) { echo " selected"; } ?>>Forever (do not delete readings)</option>
          <option value="1" <?php if ($node->retention_days == 1) { echo " selected"; } ?>>1 day</option>
          <option value="7" <?php if ($node->retention_days == 7) { echo " selected"; } ?>>1 week</option>
          <option value="31" <?php if ($node->retention_days == 31) { echo " selected"; } ?>>1 month</option>
          <option value="365" <?php if ($node->retention_days == 365) { echo " selected"; } ?>>1 year</option>
          <option value="1825" <?php if ($node->retention_days == 1825) { echo " selected"; } ?>>5 years</option>
          <option value="3650" <?php if ($node->retention_days == 3650) { echo " selected"; } ?>>10 years</option>
        </select>
        <div id="retention_daysHelp" class="form-text">Readings older than this duration will be automatically deleted.</div>
      </div>
    </div>
  
  
  
    <div class="row">
  
    <div class="form-check mb-3">
      <label for="retention_days">Node Enabled</enabledlabel>
      <select class="form-select" id="retention_days" name="enabled">
        <option value="0" <?php if ($node->enabled == 0) { echo " selected"; } ?>>Disabled (hidden)</option>
        <option value="1" <?php if ($node->enabled == 1) { echo " selected"; } ?>>Enabled</option>
      </select>
      <div id="retention_daysHelp" class="form-text">Disabled (hidden) nodes still contribute to historic energy usage calculations.</div>
    </div>
    </div>
  
    <input type="hidden" id="geo" name="geo" value="<?php echo $node->geoLocation(); ?>">
    <div id="map" class="mb-3" style="width: 100%; height: 500px"></div>
  
    <div class="mb-3">
      <?php
  
      ?>
      <button type="submit" class="btn btn-primary w-100">Submit</button>
      <input type="hidden" id="uid" name="uid" value="<?php echo $node->uid; ?>">
    </div>
  </form>
</div>



<script>
var map = L.map('map').setView([<?php echo $node->geoLocation(); ?>], 18);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

<?php
if (isset($node->geo)) {
  $output  = "L.marker([" . $node->geoLocation() . "]).addTo(map)";
  $output .= ".bindPopup('" . escape($node->name) . "')";
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


<datalist id="suppliers">
  <?php
  foreach ($nodesClass->suppliers() AS $supplier) {
    echo "<option value=\"" . $supplier . "\">";
  }
  ?>
</datalist>
