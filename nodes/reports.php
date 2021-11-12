<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<?php
$locationsClass = new locations();
$metersClass = new meters();

if (isset($_POST['nodes_includeHidden'])) {
  $enabled = "";
} else {
  $enabled = " AND enabled = '1'";
}

$nodes = null;
//get each site
foreach ($_POST['locations'] AS $locationUID) {
  //get each meter in this site that matches types
  $sql = "SELECT * FROM meters WHERE location = '" . $locationUID . "' " . $enabled . " AND type IN ('" . implode("','", $_POST['nodes']) . "');";
  $nodesByLocation = $db->query($sql)->fetchAll();

  foreach ($nodesByLocation AS $node) {
	$nodes[] = $node;
  }

  $nodeUnit = $node['unit'];
}
?>

<div class="container px-4 py-5">
	<h1 class="d-flex mb-5 justify-content-between align-items-center">Reports
		<div class="dropdown">
		  <button class="btn btn-sm btn-outline-info dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions</button>
		  <div class="dropdown-menu dashboard-dropdown">
			<a class="dropdown-item me-2" href="#">
			  <span class="sidebar-icon">
				<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#add"/></svg>
			  </span> Saved Reports (coming soon)
			</a>
			<a class="dropdown-item" href="export.php?type=readings&filter=special" target="_blank">
			  <span class="sidebar-icon">
				<svg class="dropdown-icon me-2" width="1em" height="1em"><use xlink:href="inc/icons.svg#download"/></svg>
			  </span> Export Data
			</a>
		  </div>
		</div>
	  </h1>
	  
	  <form method="post" id="termUpdate" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="needs-validation" novalidate>
		  <div class="row">
			<div class="col-4">
			  <h4>Sites</h4>
			  <?php
			  foreach ($locationsClass->all() AS $location) {
				$elementName = "locations[]";
		  
				if (in_array($location['uid'], $_POST['locations'])) {
				  $checked = " checked ";
				} else {
				  $checked = "";
				}
		  
				$output  = "<div class=\"form-check\">";
				$output .= "<input class=\"form-check-input\" type=\"checkbox\" value=\"" . $location['uid'] . "\" id=\"" . $elementName . "\" name=\"" . $elementName . "\"" . $checked . ">";
				$output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
				$output .= $location['name'];
				$output .= "</label>";
				$output .= "</div>";
		  
				echo $output;
			  }
			  ?>
			</div>
			<div class="col-4">
			  <h4>Node Types</h4>
			  <?php
			  foreach (explode(",", $settingsClass->value('node_types')) AS $nodeType) {
				$elementName = "nodes[]";
		  
				if (in_array($nodeType, $_POST['nodes'])) {
				  $checked = " checked ";
				} else {
				  $checked = "";
				}
		  
				if (isset($_POST['nodes_includeHidden']) && $_POST['nodes_includeHidden'] == "1") {
		  
				}
				$output  = "<div class=\"form-check\">";
				$output .= "<input class=\"form-check-input\" type=\"radio\" value=\"" . $nodeType . "\" id=\"" . $elementName . "\" name=\"" . $elementName . "\"" . $checked . ">";
				$output .= "<label class=\"form-check-label\" for=\"flexCheckDefault\">";
				$output .= $nodeType . " (" . unitByType($nodeType) . ")";
				$output .= "</label>";
				$output .= "</div>";
		  
				echo $output;
			  }
			  ?>
			  <hr />
		  
			  <div class="form-check">
			  <input class="form-check-input" type="checkbox" value="1" <?php if (isset($_POST['nodes_includeHidden']) && $_POST['nodes_includeHidden'] == "1") { echo " checked"; } ?> id="nodes_includeHidden" name="nodes_includeHidden">
			  <label class="form-check-label" for="flexCheckDefault">
			  Include Hidden Nodes
			  </label>
			  </div>
			</div>
			<div class="col-4">
			  <h4>Dates</h4>
			  <label for="date_from" class="form-label">Date From:</label>
			  <div class="input-group">
				<span class="input-group-text" id="date_from-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="inc/icons.svg#report"/></svg></span>
				<input type="text" class="form-control" name="date_from" id="date_from" placeholder="" value="<?php echo $date_meal; ?>" aria-describedby="date_from-addon" required>
			  </div>
		  
			  <label for="date_to" class="form-label">Date To:</label>
			  <div class="input-group">
				<span class="input-group-text" id="date_to-addon"><svg width="1em" height="1em" class="text-muted"><use xlink:href="inc/icons.svg#report"/></svg></span>
				<input type="text" class="form-control" name="date_to" id="date_to" placeholder="" value="<?php echo $date_meal; ?>" aria-describedby="date_to-addon" required>
			  </div>
			</div>
		  </div>
		  
		  <div class="d-grid gap-2">
			<button class="btn btn-primary" type="submit">Quick Filter</button>
		  </div>
		  
		  </form>
		
		<hr />
		
		<div class="row">
		  <div class="col-md-8 col-12">
			  <div class="ct-chart-sales-value ct-double-octave ct-series-g"></div>
		  </div>
		  <div class="col-md-4 col-12">
			<div class="ct-chart-location ct-double-octave ct-series-g"></div>
		  </div>
		</div>
		
		<hr />
		<div class="row row-deck row-cards mb-3">
		  <div class="col-6 col-sm-6 col-lg-3 mb-3">
			<div class="card">
			  <div class="card-body">
				<div class="subheader">
				  Total Usage
				</div>
				<div class="h1 mb-3">
				  <?php
				  $totalConsumption = 0;
				  foreach ($nodes AS $node) {
					$node = new meter($node['uid']);
					$totalConsumption = $totalConsumption + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
				  }
				  echo number_format($totalConsumption) . " " . $nodeUnit;
				  ?>
				</div>
			  </div>
			</div>
		  </div>
		  <div class="col-6 col-sm-6 col-lg-3 mb-3">
			<div class="card">
			  <div class="card-body">
				<div class="subheader">
				  ~Cost Per Unit
				</div>
				<div class="h1 mb-3">
				  <?php
				  $settingName = "unit_cost_" . $_POST['nodes'][0];
		
				  $unitCost = $settingsClass->value($settingName);
				  echo "£" . number_format($unitCost, 2);
				  ?>
				</div>
			  </div>
			</div>
		  </div>
		  <div class="col-6 col-sm-6 col-lg-3 mb-3">
			<div class="card">
			  <div class="card-body">
				<div class="subheader">
				  ~Total Cost
				</div>
				<div class="h1 mb-3">
				  <?php
				  $totalCost = $totalConsumption * $unitCost;
		
				  echo "~£" . number_format($totalCost);
				  ?>
				</div>
			  </div>
			</div>
		  </div>
		  <div class="col-6 col-sm-6 col-lg-3 mb-3">
			<div class="card">
			  <div class="card-body">
				<div class="subheader">
				  CO&#8322;e
				</div>
				<div class="h1 mb-3">
				  <?php
				  $settingName = "unit_co2e_" . $_POST['nodes'][0];
		
				  $co2eUnit = $settingsClass->value($settingName);
				  echo number_format($totalConsumption * $co2eUnit, 0) . " kg";
				  ?>
				</div>
			  </div>
			</div>
		  </div>
		</div>
</div>

<div class="b-example-divider"></div>

<div class="container px-4 py-5">
	<?php
	
	echo $metersClass->meterTable($nodes);
	
	?>
</div>





<script>
<?php
if (isset($_POST['date_from'])) {
  $dateFrom = $_POST['date_from'];
} else {
  $dateFrom = date('Y-m-d', strtotime('1 year ago'));
}

if (isset($_POST['date_to'])) {
  $dateTo = $_POST['date_to'];
} else {
  $dateTo = date('Y-m-d');
}
?>
var date_from = flatpickr("#date_from", {
  dateFormat: "Y-m-d",
  defaultDate: ['<?php echo $dateFrom; ?>'],
  maxDate: "today"
});

var date_to = flatpickr("#date_to", {
  dateFormat: "Y-m-d",
  defaultDate: ['<?php echo $dateTo; ?>'],
  maxDate: "today"
});
</script>



<!-- Pie Chart showing type usage per location -->
<?php
foreach ($nodes AS $node) {
  $node = new meter($node['uid']);
  $location = new location($node->location);

  $data[$location->name] = $data[$location->name] + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
}

$labels = "'" . implode("','", array_keys($data)) . "'";
?>


<!-- CONSUMPTION BY MONTH GRAPH -->
<?php
// CONSUMPTION BY MONTH
$data = array();
foreach ($nodes AS $node) {
  $node = new meter($node['uid']);

  $nodeData = $node->consumptionBetweenDatesByMonth($_POST['date_from'], $_POST['date_to']);

  foreach ($nodeData AS $monthData => $value) {
	$monthlyData[$monthData] = $monthlyData[$monthData] + $value;
  }

}
$monthlyData = array_reverse($monthlyData);
$monthlylabels = "'" . implode("','", array_keys($monthlyData)) . "'";
?>





<script>
// LINE CHART
var data = {
	// A labels array that can contain any sort of values
	labels: ['<?php echo implode("','", array_keys($monthlyData)); ?>'],
	// Our series array that contains series objects or in this case series data arrays
	series: [
		[<?php echo implode(",", $monthlyData); ?>]
	]
};

new Chartist.Line('.ct-chart-sales-value', data, {
	low: 0,
	showArea: true,
	fullWidth: true,
	plugins: [
		//Chartist.plugins.tooltip()
	],
	axisX: {
		// On the x-axis start means top and end means bottom
		position: 'end',
		showGrid: true
	},
	axisY: {
		// On the y-axis start means left and end means right
		showGrid: false,
		showLabel: true
	}
});



<!-- Pie Chart showing type usage per location -->
<?php
foreach ($nodes AS $node) {
  $node = new meter($node['uid']);
  $location = new location($node->location);

  $data[$location->name] = $data[$location->name] + $node->consumptionBetweenTwoDates($_POST['date_from'], $_POST['date_to']);
}

?>
// PIE CHART
var data = {
	// A labels array that can contain any sort of values
	labels: ['<?php echo implode("','", array_keys($data)); ?>'],
	// Our series array that contains series objects or in this case series data arrays
	series: [<?php echo implode(",", $data); ?>]
};

var options = {
	labelInterpolationFnc: function(value) {
		return value[0]
	}
};

var responsiveOptions = [
	  ['screen and (min-width: 640px)', {
		chartPadding: 10,
		labelOffset: 30,
		labelDirection: 'explode',
		labelInterpolationFnc: function(value) {
		  return value;
		}
	  }],
	  ['screen and (min-width: 1024px)', {
		labelOffset: 20,
		chartPadding: 0
	  }]
	];

new Chartist.Pie('.ct-chart-location', data, options, responsiveOptions);
</script>