<?php
$locations = locations::all();

foreach ($locations AS $location) {
  $location = new location($location['uid']);
  
  $tableOutput  = "<h2>" . $location->name . "</h2>";
  
  $tableOutput .= "<table class=\"table\">";
  
  $tableColumns = $location->consumptionBetweenDatesByMonth("BLANK", "2012-04-01", date("Y-m-d"));
  
  $tableOutput .= "<thead>";
  $tableOutput .= "<tr>";
  
  $tableOutput .= "<th scope=\"col\"></th>";
  
  foreach ($tableColumns AS $date => $value) {
    $tableOutput .= "<th scope=\"col\">" . $date . "</th>";
  }
  $tableOutput .= "</tr>";
  $tableOutput .= "</thead>";
  
  
  foreach (explode(",", $settingsClass->value('node_types')) AS $type) {
    $tableData = $location->consumptionBetweenDatesByMonth($type, "2012-04-01", date("Y-m-d"));
    
    $tableOutput .= "<tr>";
    $tableOutput .= "<td>" . $type . "</td>";
    
    foreach ($tableData AS $date => $value) {
        $tableOutput .= "<td>" . $value . "</td>";
    }
    
    $tableOutput .= "</tr>";
  }
  
  $tableOutput .= "</table>";

  echo $tableOutput;
}
?>