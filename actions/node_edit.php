<?php
include_once("../inc/include.php");
admin_gatekeeper();

if (isset($_POST['uid'])) {
  $node = new node($_POST['uid']);
  $_POST['billed'] = cleanBool($_POST['billed'] ?? 0);
  $_POST['enabled'] = cleanBool($_POST['enabled'] ?? 0);

  $node->update($_POST);
  $node = new node($_POST['uid']);
}
?>
