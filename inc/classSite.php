<?php
class site {
  protected static $table_name = "locations";

  public $uid;
  public $name;
  public $description;
  public $geo;
  
  
  
  public function consumptionByMonth($type = null) {
	  $nodes = $this->allNodesByType($type);
	  
	  $totalConsumption = array();
	  
	  foreach ($nodes AS $node) {
		$node = new node($node['uid']);
		
		foreach ($node->consumptionByMonth() AS $date => $value) {
			$totalConsumption[$date] = $totalConsumption[$date] + $value;
		}
	  }
	  
	  return $totalConsumption;
  }

  public function allNodes($enabledDisabled = "enabled") {
	global $db;

	if ($enabledDisabled == "all") {
	  $sqlEnabled = "";
	} else {
	  $sqlEnabled = " WHERE enabled = '1' ";
	}

	$sql  = "SELECT * FROM nodes";
	$sql .= $sqlEnabled;
	$sql .= " ORDER BY uid DESC";

	$nodes = $db->query($sql)->fetchAll();

	return $nodes;
  }

  public function allNodesByType($type = null, $enabledDisabled = "enabled") {
	global $db;

	if ($enabledDisabled == "all") {
	  $sqlEnabled = "";
	} else {
	  $sqlEnabled = " AND enabled = '1' ";
	}

	$sql  = "SELECT * FROM nodes";
	$sql .= " WHERE type = '" . $type . "' ";
	$sql .= $sqlEnabled;
	$sql .= " ORDER BY uid DESC";

	$nodes = $db->query($sql)->fetchAll();

	return $nodes;
  }
  
  public function geoMarkersOfNodes() {
	  $nodesClass = new nodes();
	  $nodes = $nodesClass->all();
	  
	  foreach ($nodes AS $node) {
		  $node = new node($node['uid']);
		  $array[] = "['" . $node->cleanName() . "', " . $node->geoLocation() . "]";
	  }
	  
	  return $array;
  }
}
?>
