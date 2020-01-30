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
		'name' => $_POST['name'],
		'location' => $_POST['location'],
		'type' => $_POST['type'],
		'serial' => $_POST['serial'],
		'billed' => $_POST['billed'],
		'enabled' => $_POST['enabled']
	);
	
	$db->where('uid', $_POST['uid']);
	
	if ($db->update ('meters', $data)) {
		echo $db->count . ' records were updated';
	} else {
		echo 'update failed: ' . $db->getLastError();
	}
}
?>