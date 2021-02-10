<?php
admin_gatekeeper();

$logsClass = new logs;

$title = "Logs";
echo makeTitle($title, $subtitle, $icons);

echo $logsClass->displayTable();
?>
