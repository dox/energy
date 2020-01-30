<?php
$locations = new locations;
$locationsAll = $locations->all();
?>

<div class="container">
	<div class="row">
		<h3>Add New Meter</h3>
	</div>
	<div class="row">
		<form>
		<div class="form-group">
			<label for="formGroupExampleInput">Name</label>
			<input type="text" class="form-control" id="name" placeholder="Meter Name">
		</div>
		<div class="form-group">
			<label for="formGroupExampleInput2">Location</label>
			<select class="form-control" id="location">
				<?php
				foreach ($locationsAll AS $location) {
					if ($location['uid'] == $_GET['location']) {
						$selected = " selected";
					} else {
						$selected = " ";
					}
					echo "<option value = \"" . $location['uid'] . "\" " . $selected . ">" . $location['name'] . "</option>";
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="formGroupExampleInput2">Type</label>
			<select class="form-control" id="type">
				<?php
				$typesArray = array ("Gas", "Electric", "Water");
				foreach ($typesArray AS $type) {
					if ($meter['type'] == $type) {
						$selected = " selected";
					} else {
						$selected = " ";
					}
					echo "<option value = \"" . $type . "\" " . $selected . ">" . $type . "</option>";
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label for="formGroupExampleInput2">Serial</label>
			<input type="text" class="form-control" id="serial" placeholder="Serial Number">
		</div>
		<div class="form-check">
			<input type="checkbox" class="form-check-input" id="billed" value="Yes">
			<label for="billed" class="form-check-label">Billed to tenant</label>
		</div>
		</form>
	</div>
	<div class="row">
		<a href="#" class="btn btn-success meterAdd">Add</a> 
	</div>
	<div id="returnMessage"></div>
</div>


<script>
$(".meterAdd").click(function() {
	var r=confirm("Warning!  Are you sure you want to add this meter?");
	
	if (r==true) {
		var thisObject = $(this);
		var name = $("input#name").val();
		var location = $("select#location").val();
		var type = $("select#type").val();
		var serial = $("input#serial").val();
		
		if ($("input#billed").is(':checked')) {
			var billed = "Yes";
		} else {
			var billed = "No";
		}
		
		var url = 'actions/meter_add.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    name: name,
		    location: location,
		    type: type,
		    serial: serial,
		    billed: billed
		}, function(data){
			location.href = 'index.php';
		},'html');
	}
	
	return false;

});
</script>