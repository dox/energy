<?php
$locationsClass = new locations();
$readingsClass = new readings();
$metersClass = new meters();
?>

<div class="py-4">
  <div class="dropdown">
    <button class="btn btn-gray-800 d-inline-flex align-items-center me-2 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
       New Task
    </button>
    <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
      <a class="dropdown-item d-flex align-items-center" href="#">
        <span class="sidebar-icon">
          <svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#add"/></svg>
        </span>
         Add New Node
      </a>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <span class="sidebar-icon">
          <svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#hidden"/></svg>
        </span>
         Show Hidden Nodes<!--javascript:toggleHiddenMeters();-->
      </a>
      <a class="dropdown-item d-flex align-items-center" href="#">
        <span class="sidebar-icon">
          <svg class="dropdown-icon me-2"><use xlink:href="inc/icons.svg#download"/></svg>
        </span>
         Export Data 
      </a>
    </div>
  </div>
</div>




<?php
foreach ($locationsClass->all() AS $location) {
  $location = new location($location['uid']);

  $meters = $location->allNodes();
  
  $output  = "<div class=\"card border-0 shadow mb-4\">";
  $output .= "<div class=\"card-header\">";
  $output .= "<h2 class=\"fs-5 fw-bold mb-0\"><a href=\"index2.php?n=location&locationUID=" . $location->uid . "\">" . $location->name . "</a></h2>";
  $output .= "</div>"; //card-header
  $output .= "<div class=\"table-responsive\">";
  $output .= "<table class=\"table table-hover table-flush table-nowrap mb-0\">";
  $output .= "<thead class=\"thead-light\">";
  $output .= "<tr>";
  $output .= "<th class=\"border-0\" style=\"width:20%\">Type</th>";
  $output .= "<th class=\"border-0\" style=\"width:40%\">Name</th>";
  $output .= "<th class=\"border-0\">Current Reading</th>";
  $output .= "</tr>";
  
  $output .= "<tbody>";
  
  foreach ($meters AS $meter) {
    $meter = new meter($meter['uid']);
    
	  $output .= "<tr>";
	  $output .= "<td>" . $meter->type . "</td>";
	  $output .= "<td><a href=\"index2.php?n=node&nodeUID=" . $meter->uid . "\">" . $meter->name . "</a></td>";
	  $output .= "<td>" . displayReading($meter->currentReading()) . " " . $meter->unit . "</td>";
	  $output .= "</tr>";
  }
  
  $output .= "</tbody>";
  
  $output .= "</thead>";
  $output .= "</table>";
  $output .= "</div>"; //table-responsive
  
  
  $output .= "</div>"; //card
  
  //$output .= $metersClass->meterTable($meters);

  foreach ($meters AS $meter) {
	if ($meter['enabled'] == 1) {
	//  $output .= $metersClass->displayMeterCard($meter['uid']);
	}
  }

  //$output .= "</div>";

  echo $output;
}
?>


