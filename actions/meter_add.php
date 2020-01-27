<?php
require_once('../inc/config.php');
require_once('../database/MysqliDb.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_POST['name'])) {
	foreach ($_POST AS $key => $value) {
		if ($value == "") {
			$_POST[$key] = null;
		}
	}
	
	$data =	Array (
		'name' => $_POST['name'],
		'location' => $_POST['location'],
		'type' => $_POST['type'],
		'photograph' => $_POST['photograph'],
		'serial' => $_POST['serial'],
		'billed' => $_POST['billed']
		);
	
	$id = $db->insert ('meters', $data);
	
	if($id) {
		echo json_encode(['code'=>200, 'msg'=>"Meter added"]);
	}
}
?>