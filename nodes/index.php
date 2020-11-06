<?php
$locationsClass = new locations();
$metersClass = new meters();
//$logs = $logsClass->paginatedAll($from, $resultsPerPage);
$locations = $locationsClass->all();
?>

<div class="container">
  <button type="button" class="btn btn-primary float-right" data-toggle="button" aria-pressed="false" autocomplete="off">Single toggle</button>
  <h1>Meters</h1>
<?php

foreach ($locations AS $location) {
  $meters = $metersClass->allByLocation($location['uid']);

  $output  = "<h1 class=\"display-4\"><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a></h1>";
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
</div>

<script>
$("tr").removeClass('d-none');
</script>
