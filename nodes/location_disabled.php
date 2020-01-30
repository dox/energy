<?php
$thisYear = date('Y');
$lastYear = (date('Y')-1);
$lastLastYear = (date('Y')-2);

$locations = new locations;
$locations->locationUID = $_GET['locationUID'];
$location = $locations->location();

$metersClass = new meters;
$metersAll = $metersClass->allByLocation($location['uid']);


$readingsClass = new Readings;
$readingsClass->locationUID = $location['uid'];

$readingsAll = $readingsClass->readingsByMeter(20);

?>

<div class="container">
	<div class="row">
		<h3><?php echo $location['name'];?> <small class="text-muted"><?php echo $location['description']; ?></small></h3>
	</div>
	
	<div class="row">
		<?php
		
		$output = "";
		
		foreach ($metersAll AS $meter) {
			if ($meter['enabled'] == 0) {
				$output .= $metersClass->displayMeterCard($meter['uid']);
			}
		}
		
		echo $output;
		?>
	</div>
</div>