<?php
$locationsClass = new locations();
$locations = $locationsClass->all();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#locations"/></svg> Locations</h1>

  <div class="btn-toolbar mb-2 mb-md-0">
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="title_dropdown" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
      <ul class="dropdown-menu" aria-labelledby="title_dropdown">
        <li><a class="dropdown-item" href="index.php?n=location_add"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#add"/></svg> Add Location</a></li>
      </ul>
    </div>
  </div>
</div>

<?php
foreach ($locations AS $location) {
  echo "<h3><a href=\"index.php?n=location&locationUID=" . $location['uid'] . "\">" . $location['name'] . "</a> <small class=\"text-muted\">" . $location['description'] . "</small></h3>";
}
?>
