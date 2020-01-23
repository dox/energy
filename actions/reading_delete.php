<?php
require_once('../inc/config.php');
require_once('../database/MysqliDb.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_POST['uid'])) {
	$db->where('uid', $_POST['uid']);
	if($db->delete('readings')) {
		echo 'reading successfully deleted';
	}
}
?>