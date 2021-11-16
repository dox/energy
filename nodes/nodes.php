<?php
$locationsClass = new locations();
$readingsClass = new readings();
$nodesClass = new nodes();

if (isset($_POST['name'])) {
  $nodesClass->create($_POST);
}
?>

<div class="container px-4 py-5">
  <h1 class="d-flex mb-5 justify-content-between align-items-center">Nodes
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
      <div class="dropdown-menu dashboard-dropdown">
        <a class="dropdown-item me-2" href="#">
          <span class="sidebar-icon">
            <svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#add"/></svg>
          </span> Add New Node
        </a>
        <a class="dropdown-item me-2" href="javascript:toggleHiddenMeters();">
          <span class="sidebar-icon">
            <svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#hidden"/></svg>
          </span> Show Hidden Nodes
        </a>
        <a class="dropdown-item" href="export.php?type=nodes&filter=all" target="_blank">
          <span class="sidebar-icon">
            <svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#download"/></svg>
          </span> Export Data
        </a>
      </div>
    </div>
  </h1>
  
  <?php
  foreach ($locationsClass->all() AS $location) {
    $location = new location($location['uid']);
  
    $nodes = $location->allNodes("all");
    
    $output  = "<div class=\"card mb-4 shadow\">";
    $output .= "<div class=\"card-header\">";
    $output .= "<h2 class=\"mb-0\"><a href=\"index.php?n=location&locationUID=" . $location->uid . "\">" . $location->name . "</a></h2>";
    $output .= "</div>"; //card-header
    $output .= "<div class=\"table-responsive\">";
    $output .= "<table class=\"table table-hover table-flush table-nowrap mb-0\">";
    $output .= "<thead class=\"thead-light\">";
    $output .= "<tr>";
    $output .= "<th class=\"border-0\" style=\"width:20%\">Type</th>";
    $output .= "<th class=\"border-0\" style=\"width:40%\">Name</th>";
    $output .= "<th class=\"border-0\">Current Reading</th>";
    $output .= "</tr>";
    $output .= "</thead>";
    
    $output .= "<tbody>";
    
    foreach ($nodes AS $node) {
      $node = new node($node['uid']);
      
      if ($node->enabled == 0) {
        $enabledClass = " table-secondary d-none";
      } else {
        $enabledClass = "";
      }
      
      $output .= "<tr class=\"" . $enabledClass . "\">";
      $output .= "<td>" . $node->nodeTypeBadge() . "</td>";
      $output .= "<td><a href=\"index.php?n=node&nodeUID=" . $node->uid . "\">" . $node->name . "</a></td>";
      $output .= "<td>" . displayReading($node->currentReading()) . " " . $node->unit . "</td>";
      $output .= "</tr>";
    }
    
    $output .= "</tbody>";
    
    $output .= "</table>";
    $output .= "</div>"; //table-responsive
    
    
    $output .= "</div>"; //card
    
    //$output .= $metersClass->meterTable($meters);
  
    foreach ($nodes AS $node) {
    if ($node['enabled'] == 1) {
    //  $output .= $metersClass->displayMeterCard($meter['uid']);
    }
    }
  
    //$output .= "</div>";
  
    echo $output;
  }
  ?>
</div>