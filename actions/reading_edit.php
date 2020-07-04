<?php
require_once('../inc/config.php');
require_once('../database/MysqliDb.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_POST['pk'])) {
	$data = Array (
		'reading1' => $_POST['value']
	);

	$db->where('uid', $_POST['pk']);

	if ($db->update ('readings', $data)) {
		echo "<p>" . $db->count . " reading has been updated</p>";
		echo "<p>" . "<a href=\"index.php?n=meter&meterUID=" . $_POST['pk'] . "\">Click here</a> to return to the meter</p>";
	} else {
		echo 'update failed: ' . $db->getLastError();
	}
}
?>
