<?php
require_once('../inc/config.php');
require_once('../database/MysqliDb.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_POST['uid'])) {
	$data = Array (
		'name' => $_POST['name'],
		'location' => $_POST['location'],
		'type' => $_POST['type'],
		'photograph' => $_POST['photograph'],
		'serial' => $_POST['serial'],
		'billed' => $_POST['billed']
	);
	
	$db->where('uid', $_POST['uid']);
	
	if ($db->update ('meters', $data)) {
		echo $db->count . ' records were updated';
	} else {
		echo 'update failed: ' . $db->getLastError();
	}
}
?>