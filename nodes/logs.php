<?php
admin_gatekeeper();

$logsClass = new logs;
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <h1 class="h2"><svg width="1em" height="1em"><use xlink:href="inc/icons.svg#logs"/></svg> Logs</h1>
</div>

<?php
echo $logsClass->displayTable();
?>
