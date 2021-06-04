<?php
include_once("../inc/include.php");
admin_gatekeeper();

if (isset($_POST['uid'])) {
  $meter = new meter($_POST['uid']);
  $meter->delete();
}
?>
