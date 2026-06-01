<?php
include_once("../inc/include.php");
admin_gatekeeper();

$nodesClass = new nodes();

if (isset($_POST['name'])) {
  $_POST['billed'] = cleanBool($_POST['billed'] ?? 0);
  $_POST['enabled'] = cleanBool($_POST['enabled'] ?? 0);

  $nodesClass->create($_POST);
}
?>
