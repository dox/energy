<?php
require_once('../inc/config.php');
require_once('../database/MysqliDb.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

if (isset($_POST['meter']) && isset($_POST['reading'])) {
	$lastReading = $db->rawQueryOne("SELECT * FROM readings WHERE meter = '" . $_POST['meter'] . "' ORDER BY date DESC LIMIT 1");
	
	if (strtotime($_POST['date'])) {
		$readingDate = date('Y-m-d H:i', strtotime($_POST['date']));
	} else {
		$errorMsg = "Error: Incorrect date parameter: " . $_POST['date'];
	}
	
	if (is_numeric($_POST['reading'])) {
		$readingValue = $_POST['reading'];
	} else {
		$errorMsg = "Error: Incorrect reading parameter: " . $_POST['reading'];
	}
	
	if ($_POST['reading'] < $lastReading['reading1']) {
		$errorMsg = "Error: Reading can't be lower than the previous reading!";
	}
	
	if (isset($errorMsg)) {
		echo json_encode(['code'=>404, 'msg'=>$errorMsg]);
	} else {
		$data =	Array ("meter" => $_POST['meter'],
			"reading1" => $_POST['reading'],
			"date" => $readingDate
			);
		
		$id = $db->insert ('readings', $data);
		
		if($id) {
			echo json_encode(['code'=>200, 'msg'=>"Reading added"]);
		}
	}
}
?>