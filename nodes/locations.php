<?php
$locationsClass = new locations();
$locations = $locationsClass->all();

$title = "Locations";
//$icons[] = array("class" => "btn-primary", "name" => "<svg width=\"1em\" height=\"1em\"><use xlink:href=\"inc/icons.svg#add\"/></svg> Add Meter", "value" => "onclick=\"location.href='index.php?n=meter_add'\"");

echo makeTitle($title, $subtitle, $icons);
?>

<?php
foreach ($locations AS $location) {
  echo "<h2><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a> <small class=\"text-muted\">" . $location['description'] . "</small></h2>";
}
?>
