<?php
include_once("../inc/include.php");
admin_gatekeeper();

$nodesClass = new nodes();

if (isset($_POST['name'])) {
  if (($_POST['billed'] ?? '') == "true") {
    $_POST['billed'] = 1;
  } else {
    $_POST['billed'] = 0;
  }
  if (($_POST['enabled'] ?? '') == "true") {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }

  $nodesClass->create($_POST);
}
?>
