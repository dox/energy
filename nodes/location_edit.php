<?php
admin_gatekeeper();

$location = new locations(filter_var($_GET['locationUID'], FILTER_SANITIZE_NUMBER_INT));

if (isset($_POST['uid'])) {
  $location->update($_POST);
  $location = new locations(filter_var($_POST['uid'], FILTER_SANITIZE_NUMBER_INT));
}

?>
<div class="container px-4 py-5">
  <?php
  $title     = "Location: Edit";
  echo pageHeader($title);
  ?>
  
  <form method="post" id="locationUpdate" action="index.php?n=location_edit&locationUID=<?php echo $location->uid; ?>">
      <div class="mb-3">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Location Name" value="<?php echo $location->name; ?>">
      </div>
    
      <div class="mb-3">
        <label for="serial">Description</label>
        <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php echo $location->description; ?>">
      </div>
    
      <input type="hidden" id="geo" name="geo" value="<?php echo $location->geo; ?>">
      <div id="map" style="width: 100%; height: 500px"></div>
      <input type="hidden" id="uid" name="uid" value="<?php echo $location->uid; ?>">
    
      <div class="mb-3">
        <button type="submit" class="btn btn-primary w-100">Submit</button>
        <input type="hidden" id="uid" name="uid" value="<?php echo $location->uid; ?>">
      </div>
    
      <div id="returnMessage"></div>
    </form>
</div>

<script>
var map = L.map('map').setView([<?php echo $location->geoLocation(); ?>], 18);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

<?php
if (isset($location->geo)) {
  $output  = "L.marker([" . $location->geoLocation() . "]).addTo(map)";
  $output .= ".bindPopup('" . escape($location->name) . "')";
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
