<?php
require_once('inc/config.php');
require_once('database/MysqliDb.php');

$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);

//print_r($_POST['data']);
$json = json_decode($_POST['data']);

$meterValue = $json->meter;
$readingValue = $json->reading;

echo $meterValue . " <br /> " . $readingValue;

if (isset($meterValue) && isset($readingValue)) {
	$readingDate = date('Y-m-d H:i:s');
	
	if (!is_numeric($readingValue)) {
		$errorMsg = "Error: Incorrect reading parameter: " . $readingValue;
	}
	
	if (isset($errorMsg)) {
		echo json_encode(['code'=>500, 'msg'=>$errorMsg]);
	} else {
		$data =	Array ("meter" => $meterValue,
			"reading1" => $readingValue,
			"date" => $readingDate
			);
		
		$id = $db->insert ('readings', $data);
		
		if($id) {
			echo json_encode(['code'=>200, 'msg'=>"Reading added"]);
		}
	}
} else {
	$errorMsg = "Error: either no meter or no reading value supplied";
	json_encode(['code'=>500, 'msg'=>"Reading added"]);
}
?>