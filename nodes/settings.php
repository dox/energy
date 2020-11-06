<?php
if ($_SESSION['logon'] != true) {
  header("Location: http://readings.seh.ox.ac.uk/index.php?n=logon");
	exit;
}
?>

<div class="container">
  <h1>Site Settings</h1>
  <p>Coming soon...</p>
</div>
