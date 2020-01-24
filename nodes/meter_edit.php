<?php
$meter = $db->where("uid", $_GET['meterUID']);
$meter = $db->getOne("meters");	

$location = $db->where("uid", $meter['location']);
$location = $db->getOne("locations");

$locations = new locations;
$locations->locationUID = $meter['location'];
$locationsAll = $locations->all();
?>

<div class="container">
	<div class="row">
		<h3><?php echo $meter['name']; ?> <small class="text-muted">(<?php echo $location['name'];?>)</small></h3>
	</div>
	<div class="row">
		<form>
		<div class="form-group">
			<label for="formGroupExampleInput">Name</label>
			<input type="text" class="form-control" id="name" placeholder="Example input placeholder" value="<?php echo $meter['name'];?>">
		</div>
		<div class="form-group">
			<label for="formGroupExampleInput2">Location</label>
			<select class="form-control" id="location">
				<?php
				foreach ($locationsAll AS $location) {
					if ($location['uid'] == $meter['location']) {
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
			<input type="text" class="form-control" id="serial" placeholder="Example input placeholder" value="<?php echo $meter['serial'];?>">
		</div>
		<div class="form-check">
			<input type="checkbox" class="form-check-input" id="billed" <?php if ($meter['billed'] == "Yes") { echo " checked";} ?> value="<?php echo $meter['billed'];?>">
			<label for="billed" class="form-check-label">Billed to tenant</label>
		</div>
		<div class="form-group">
			<label for="formGroupExampleInput2">Photograph</label>
			<input type="text" class="form-control" id="photograph" placeholder="Example input placeholder" value="<?php echo $meter['photograph'];?>">
		</div>
		
		</form>
	</div>
	<div class="row">
		<a href="#" class="btn btn-success meterEdit" id="<?php echo $meter['uid'];?>">Update</a> 
		<a href="#" class="btn btn-danger deleteBookingButton" id="<?php echo $meter['uid'];?>">Delete Meter</a>
		<input type="hidden" id="uid" value="<?php echo $meter['uid'];?>">
	</div>
	<div id="returnMessage"></div>
</div>


<script>
$(".deleteBookingButton").click(function() {
	var r=confirm("Warning!  Are you sure you want to delete this meter and all associated readings?  This cannot be undone!");
	
	if (r==true) {
		var thisObject = $(this);
		var uid = $(thisObject).attr('id');
		
		var url = 'actions/meter_delete.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    uid: uid
		}, function(data){
			//$(thisObject).parent().parent().parent().parent().parent().fadeOut();
			location.href = 'index.php';
		},'html');
	} else {
	}
	
	return false;

});

$(".meterEdit").click(function() {
	var r=confirm("Warning!  Are you sure you want to edit this meter?");
	
	if (r==true) {
		var thisObject = $(this);
		var uid = $("input#uid").val();
		var name = $("input#name").val();
		var location = $("select#location").val();
		var type = $("select#type").val();
		var photograph = $("input#photograph").val();
		var serial = $("input#serial").val();
		
		if ($("input#billed").is(':checked')) {
			var billed = "Yes";
		} else {
			var billed = "No";
		}
		
		var url = 'actions/meter_edit.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    uid: uid,
		    name: name,
		    location: location,
		    type: type,
		    photograph: photograph,
		    serial: serial,
		    billed: billed
		}, function(data){
			$("#returnMessage").append(data);
		},'html');
	}
	
	return false;

});
</script>