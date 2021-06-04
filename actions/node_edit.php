<?php
include_once("../inc/include.php");
admin_gatekeeper();

if (isset($_POST['uid'])) {
  $meter = new meter($_POST['uid']);
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

  $meter->update($_POST);
  $meter = new meter($_POST['uid']);
}
?>
