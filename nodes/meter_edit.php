<?php
if (isset($_GET['meterUID']) && !isset($_GET['nodeUID'])) {
  $_GET['nodeUID'] = cleanInt($_GET['meterUID']);
}

$_GET['n'] = 'node_edit';
include_once("nodes/node_edit.php");
?>
