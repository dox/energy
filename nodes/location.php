<?php
$location = new locations($_GET['locationUID']);
$metersClass = new meters();
$meters = $metersClass->allByLocation($location->uid);
?>

<div class="container">
  <h1><?php echo $location->name; ?> <small class="text-muted"><?php echo $location->description; ?></small></h1>

<h2>Meters</h2>
<?php
echo $metersClass->meterTable($meters);
?>

</div>
