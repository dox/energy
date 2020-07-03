<?php
require_once('../inc/config.php');
require_once('../database/MysqliDb.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_POST['uid'])) {
	foreach ($_POST AS $key => $value) {
		if ($value == "") {
			$_POST[$key] = null;
		}
	}

	$data = Array (
		'reading1' => $_POST['reading1']
	);

	$db->where('uid', $_POST['uid']);

	if ($db->update ('readings', $data)) {
		echo "<p>" . $db->count . " reading has been updated</p>";
		echo "<p>" . "<a href=\"index.php?n=meter&meterUID=" . $_POST['meter'] . "\">Click here</a> to return to the meter</p>";
	} else {
		echo 'update failed: ' . $db->getLastError();
	}
}
?>
