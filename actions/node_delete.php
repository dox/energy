<?php
include_once("../inc/include.php");
admin_gatekeeper();

if (isset($_POST['nodeUID'])) {
  $node = new node($_POST['nodeUID']);
  $node->delete();
}
?>
