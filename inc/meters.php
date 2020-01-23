<?php
class meters {

public $meterUID;

public function thisMeterUnits($type = null) {
	global $db;
	
	
	
	if ($type == null) {
		$meter = $db->where("uid", $this->meterUID);
		$meter = $db->getOne("meters");
		
		$meterType = $meter['type'];
	} else {
		$meterType = $type;
	}
	
	if ($meterType == "Electric") {
		$unit = "kWh";
	} elseif ($meterType == "Gas") {
		$unit = "m<sup>3</sup>";
	} elseif ($meterType == "Water") {
		$unit = "m<sup>3</sup>";
	} else {
		$unit = "?";
	}
	return $unit;
}

public function getOne() {
	global $db;
	
	$meter = $db->where("uid", $this->meterUID);
	$meter = $db->getOne("meters");
	
	return $meter;
}









public function all() {
	global $db;
	
	$meters = $db->orderBy('name', "DESC");
	$meters = $db->get("meters");
	
	return $meters;
}

public function allByLocation($locationUID = null) {
	global $db;
	
	$meters = $db->where("location", $locationUID);
	$meters = $db->orderBy('name', "DESC");
	$meters = $db->get("meters");
	
	return $meters;
}

} //end CLASS
?>