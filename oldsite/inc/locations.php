<?php
class locations {

public $locationUID;

public function all() {
	global $db;
	
	$locations = $db->orderBy('name', "ASC");
	$locations = $db->get("locations");
	
	return $locations;
}

public function location() {
	global $db;
	
	$location = $db->where("uid", $this->locationUID);
	$location = $db->getOne("locations");
	
	return $location;
}

} //end CLASS
?>