<?php
admin_gatekeeper();

$logsClass = new logs;
$logsClass->purge();
?>

<div class="container px-4 py-5">
  <?php
  $title     = "Logs";
  
  echo pageHeader($title);
  
  echo $logsClass->displayTable();
  ?>
</div>
