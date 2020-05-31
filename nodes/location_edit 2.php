<?php
if (!isset($_SESSION['username'])) {
	echo "<a href=\"index.php?n=login\">You are not logged in</a>";
	exit;
}
?>
Coming soon...