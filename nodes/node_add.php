<?php
admin_gatekeeper();

$locationsClass = new locations();
$nodesClass = new nodes();
$readingsClass = new readings();

$locationUID = $_GET['locationUID'];
?>

<div class="container px-4 py-5">
  <h1 class="mb-5">Add New Node</h1>

  <form method="post" id="nodeUpdate" action="index.php?n=nodes">
    <div class="mb-3">
      <label for="name">Name</label>
      <input type="text" class="form-control" id="name" name="name" placeholder="Node Name"s>
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
              echo "<option value = \"" . $type . "\" >" . $type . "</option>";
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
             
              echo "<option value = \"" . $unit . "\">" . $unit . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="mb-3">
          <label for="supplier">Supplier</label>
          <input class="form-control" id="supplier" name="supplier" list="suppliers"/>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="mb-3">
          <label for="account_no">Account #</label>
          <input class="form-control" id="account_no" name="account_no"/>
        </div>
      </div>
    </div>
  
    <div class="row">
      <div class="col-6">
        <div class="mb-3">
          <label for="serial">Serial</label>
          <input type="text" class="form-control" id="serial" name="serial" placeholder="Serial Number">
        </div>
      </div>
      <div class="col-6">
        <div class="mb-3">
          <label for="mprn">MPRN</label>
          <input type="text" class="form-control" id="mprn" name="mprn" placeholder="MPRN" >
        </div>
      </div>
    </div>
  
    <div class="row">
      <div class="col-6">
        <div class="mb-3">
          <label for="address">Address</label>
          <textarea class="form-control" id="address" name="address" rows="4" placeholder="Address"></textarea>
        </div>
      </div>
      <div class="col-6">
        <div class="form-check mb-3">
          <input type="checkbox" class="form-check-input" id="billed" name="billed" value="1">
          <label for="billed" class="form-check-label">Billed to tenant</label>
        </div>
  
        <label for="retention_days">Retention Policy</label>
        <select class="form-select" id="retention_days" name="retention_days">
          <option value="0" selected>Forever (do not delete readings)</option>
          <option value="1">1 day</option>
          <option value="7">1 week</option>
          <option value="31">1 month</option>
          <option value="365">1 year</option>
          <option value="1825">5 years</option>
          <option value="3650">10 years</option>
        </select>
        <div id="retention_daysHelp" class="form-text">Readings older than this duration will be automatically deleted.</div>
      </div>
    </div>
  
  
  
    <div class="mb-3">
  
    </div>
  
    <div class="form-check mb-3">
      <input type="checkbox" class="form-check-input" id="enabled" name="enabled" value="1" checked>
      <label for="billed" class="form-check-label">Enabled</label>
    </div>
  
    <input type="hidden" id="geo" name="geo" value="">
    <div id="map" class="mb-3" style="width: 100%; height: 500px"></div>
  
    <div class="mb-3">
      <?php
  
      ?>
      <button type="submit" class="btn btn-primary w-100">Submit</button>
    </div>
  
    <div id="returnMessage"></div>
  </form>
</div>



<script>
var map = L.map('map').setView([<?php echo $settingsClass->value("site_geolocation"); ?>], 18);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

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
