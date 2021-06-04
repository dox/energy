<?php
$locationsClass = new locations();
$readingsClass = new readings();
$metersClass = new meters();

if (isset($_POST['reading1']) && $_SESSION['logon'] == true) {
  $readingsClass->create($meter->uid, $_POST['reading1']);
}

$readings = $readingsClass->meter_all_readings($meter->uid);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#nodes"/></svg> Nodes</h1>
  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="title_dropdown" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
        <ul class="dropdown-menu" aria-labelledby="title_dropdown">
          <li><a class="dropdown-item <?php if ($_SESSION['logon'] != true) { echo "disabled";} ?>" href="index.php?n=node_edit"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#edit"/></svg> Add Node</a></li>
          <li><a class="dropdown-item" href="javascript:toggleHiddenMeters();"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#hidden"/></svg> Show Hidden Nodes</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php
foreach ($locationsClass->all() AS $location) {
  $meters = $metersClass->allByLocation($location['uid'], "all");

  $output  = "<h3><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a></h3>";
  $output .= $metersClass->meterTable($meters);

  foreach ($meters AS $meter) {
    if ($meter['enabled'] == 1) {
    //  $output .= $metersClass->displayMeterCard($meter['uid']);
    }
  }

  //$output .= "</div>";

  echo $output;
}
?>
