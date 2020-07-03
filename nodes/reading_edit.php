<?php
if (!isset($_SESSION['username'])) {
	echo "<a href=\"index.php?n=login\">You are not logged in</a>";
	exit;
}
$reading = $db->where("uid", $_GET['uid']);
$reading = $db->getOne("readings");

$meter = $db->where("uid", $reading['meter']);
$meter = $db->getOne("meters");

$location = $db->where("uid", $meter['location']);
$location = $db->getOne("locations");
?>

<div class="container">
	<div class="row">
		<h3><?php echo $meter['name']; ?> <small class="text-muted">(<?php echo $location['name'];?>)</small></h3>
	</div>
	<div class="row">
		<div class="col-sm">
		<form>
		<div class="form-group">
			<label for="formGroupExampleInput">Reading for <?php echo date('Y-m-d H:i', strtotime($reading['date'])); ?></label>
			<input type="text" class="form-control" id="reading1" placeholder="Reading" value="<?php echo $reading['reading1'];?>">
		</div>



		<div id="returnMessage"></div>
		<a href="#" class="btn btn-success readingEdit" id="<?php echo $meter['uid'];?>">Update</a>
		<!--<a href="#" class="btn btn-danger deleteBookingButton" id="<?php echo $meter['uid'];?>">Delete Reading</a>-->
		<input type="hidden" id="meter" value="<?php echo $meter['uid'];?>">
		<input type="hidden" id="uid" value="<?php echo $reading['uid'];?>">
		</form>
	</div>
</div>


<script>
$(".readingEdit").click(function() {
	var r=confirm("Warning!  Are you sure you want to edit this meter?");

	if (r==true) {
		var thisObject = $(this);
		var uid = $("input#uid").val();
		var reading1 = $("input#reading1").val();
		var meter = $("input#meter").val();

		var url = 'actions/reading_edit.php';

		// perform the post to the action (take the info and submit to database)
		$.post(url,{
		    uid: uid,
				reading1: reading1,
				meter: meter
		}, function(data){
			$("#returnMessage").append(data);
		},'html');
	}

	return false;

});
</script>
