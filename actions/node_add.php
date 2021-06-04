<?php
include_once("../inc/include.php");
admin_gatekeeper();

$metersClass = new meters();

if (isset($_POST['name'])) {
  if ($_POST['billed'] == "true") {
    $_POST['billed'] = 1;
  } else {
    $_POST['billed'] = 0;
  }
  if ($_POST['enabled'] == "true") {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }

  $metersClass->create($_POST);
}
?>
