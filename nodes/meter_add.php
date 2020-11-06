<?php
if ($_SESSION['logon'] != true) {
  header("Location: http://readings.seh.ox.ac.uk/index.php?n=logon");
	exit;
}
?>

<div class="container">
  <h1>Add New Meter</h1>
  <p>Coming soon... (please don't use this form yet!)</p>
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
					echo "<option value = \"" . $location['uid'] . "\">" . $location['name'] . "</option>";
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
