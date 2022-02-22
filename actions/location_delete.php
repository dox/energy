<?php
include_once("../inc/include.php");
admin_gatekeeper();

if (isset($_POST['locationUID'])) {
  $location = new location($_POST['locationUID']);
  $location->delete();
}
?>
