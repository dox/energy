<?php
$meter = $db->where("uid", $_GET['meterUID']);
$meter = $db->getOne("meters");	

if(isset($_FILES['photograph'])) {
	$fileinfo = pathinfo($_FILES['photograph']['name']);
	$extension = strtolower($fileinfo['extension']);
	$uploadedfile = $_FILES['photograph']['tmp_name'];
	
	
	list($width,$height)=getimagesize($uploadedfile);
	
	//set new width            
	$newwidth1=350;
	$newheight1=($height/$width)*$newwidth1;
	$tmp1=imagecreatetruecolor($newwidth1,$newheight1);
	
	if($extension=="jpg" || $extension=="jpeg" ) {
		$src = imagecreatefromjpeg($uploadedfile);
		imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
	} else if($extension=="png") {
		$src = imagecreatefrompng($uploadedfile);
		imagecopyresampled($tmp1,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
	} else {
		echo "Error with upload of image!";
	}
	
	$temp = explode(".", $_FILES["photograph"]["name"]);
	$newfilename = "meter_" . $meter['uid'] . '.' . strtolower(end($temp));
	$filename1 = "uploads/". $newfilename;
	
	imagejpeg($tmp1,$filename1,100);
	
	imagedestroy($src);
	imagedestroy($tmp1);
	
	$insert = $db->rawQueryOne("UPDATE meters SET photograph = ('$newfilename') WHERE uid = '" . $meter['uid'] . "'");
}

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
		<div class="col-sm">
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
		<div class="form-check">
			<input type="checkbox" class="form-check-input" id="enabled" <?php if ($meter['enabled'] == true) { echo " checked";} ?> value="<?php echo $meter['enabled'];?>">
			<label for="enabled" class="form-check-label">Enabled</label>
		</div>
		</form>
		<div id="returnMessage"></div>
		<a href="#" class="btn btn-success meterEdit" id="<?php echo $meter['uid'];?>">Update</a> 
		<a href="#" class="btn btn-danger deleteBookingButton" id="<?php echo $meter['uid'];?>">Delete Meter</a>
		<input type="hidden" id="uid" value="<?php echo $meter['uid'];?>">
	</div>
	<div class="col-sm">
		<?php
		if (isset($meter['photograph'])) {
			$output  = "<img src=\"uploads/" . $meter['photograph'] . "\" class=\"img-fluid\" />";
			
			echo $output;
		}
		?>
		<form action="index.php?n=meter_edit&meterUID=<?php echo $meter['uid'];?>" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label for="photograph">Photograph</label>
			<input type="file" class="form-control-file" id="photograph" name="photograph">
		</div>
		 <button type="submit">Upload</button>
		</form>
	</div>
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
		var serial = $("input#serial").val();
		
		if ($("input#billed").is(':checked')) {
			var billed = "Yes";
		} else {
			var billed = "No";
		}
		
		if ($("input#enabled").is(':checked')) {
			var enabled = 1;
		} else {
			var enabled = 0;
		}
		
		var url = 'actions/meter_edit.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    uid: uid,
		    name: name,
		    location: location,
		    type: type,
		    serial: serial,
		    billed: billed,
		    enabled: enabled
		}, function(data){
			$("#returnMessage").append(data);
		},'html');
	}
	
	return false;

});
</script>