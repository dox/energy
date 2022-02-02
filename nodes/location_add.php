<?php
admin_gatekeeper();

?>
<div class="container px-4 py-5">
  <?php
  $title     = "Location: Add"
  echo pageHeader($title);
  ?>
  
  <form method="post" id="locationUpdate" action="index.php?n=map">
      <div class="mb-3">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Location Name">
      </div>
    
      <div class="mb-3">
        <label for="serial">Description</label>
        <input type="text" class="form-control" id="description" name="description" placeholder="Description">
      </div>
    
      <input type="hidden" id="geo" name="geo">
      <div id="map" style="width: 100%; height: 500px"></div>
          
      <div class="mb-3">
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
