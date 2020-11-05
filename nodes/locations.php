<?php
$locationsClass = new locations();
$locations = $locationsClass->all();
?>

<div class="container">
  <?php
  foreach ($locations AS $location) {
    echo "<h1><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a> <small class=\"text-muted\">" . $location['description'] . "</small></h1>";
  }
  ?>
</div>
