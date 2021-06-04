<?php
include_once("../inc/include.php");
admin_gatekeeper();

$readingsClass = new readings();

//printArray($_POST);

if (isset($_POST['readingUID']) && $_SESSION['logon'] == true) {
  $readingsClass->delete($_POST['readingUID']);
} else {
  //$logArray['category'] = "booking";
  //$logArray['result'] = "danger";
  //$logArray['description'] = "Error attempting to delete [bookingUID:" . $bookingObject->uid . "]";
  //$logsClass->create($logArray);
}
?>
