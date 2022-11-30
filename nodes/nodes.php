<?php
$locations = new locations();

if (isset($_POST['name'])) {
  $nodes = new nodes();
  $nodes->create($_POST);
}
?>

<div class="container px-4 py-5">
  <?php
  $title     = "Nodes";
  $actions[] = array('name' => 'Add New Node', 'icon' => 'add', 'href' => 'index.php?n=node_add');
  $actions[] = array('name' => 'Show Hidden Nodes', 'icon' => 'hidden', 'href' => 'javascript:toggleHiddenMeters();');
  $actions[] = array('name' => 'separator');
  $actions[] = array('name' => 'Export Data', 'icon' => 'download', 'href' => 'export.php?type=nodes&filter=all');
  
  echo pageHeader($title, $actions);
  ?>
  
  <?php
  foreach ($locations->all() AS $location) {
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
    $output .= "<th class=\"border-0\">Reading Date</th>";
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
      $output .= "<td>" . dateDisplay($node->mostRecentReadingDate()) . " <i>(" . howLongAgo($node->mostRecentReadingDate()) . ")</i></td>";
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