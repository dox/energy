<?php
$readingsClass = new readings();
$readings_recent = $readingsClass->getRecentReadings();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#home"/></svg> Dashboard</h1>

  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="title_dropdown" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
        <ul class="dropdown-menu" aria-labelledby="title_dropdown">
          <li><a class="dropdown-item" href="#"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg> Edit Node</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div id = "map" style = "width: 100%; height: 500px"></div>

<hr />

<h2>Recent Updates</h2>
<div class="table-responsive">
  <table class="table table-striped table-sm">
    <thead>
      <tr>
        <th>Node</th>
        <th>Date</th>
        <th>Reading</th>
        <th>Username</th>
      </tr>
    </thead>
    <tbody>
      <?php
      foreach ($readings_recent AS $reading) {
        $node = new meter($reading['meter']);

        $output  = "<tr>";
        $output .= "<td><a href=\"index.php?n=node&meterUID=" . $node->uid . "\">" . $node->name . "</a></td>";
        $output .= "<td>" . $reading['date'] . "</td>";
        $output .= "<td>" . $reading['reading1'] . "</td>";
        $output .= "<td>" . $reading['username'] . "</td>";
        $output .= "</tr>";

        echo $output;
      }
      ?>
    </tbody>
  </table>
</div>


<script>
// Creating map options
var mapOptions = {
   center: [51.75283, -1.24969],
   zoom: 18
}

// Creating a map object
var map = new L.map('map', mapOptions);

// Creating a Layer object
var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

// Adding layer to the map
map.addLayer(layer);
</script>
