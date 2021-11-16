<?php
admin_gatekeeper();

$logsClass = new logs;
?>

<div class="container px-4 py-5">
  <h1 class="mb-5">Logs</h1>
  <?php
  echo $logsClass->displayTable();
  ?>
</div>
