<!doctype html>
<html lang="en">
<?php
include_once("views/header_html.php");


?>

<body>
<?php include_once("views/header.php"); ?>

<main>
	<div class="album py-5 bg-light">
		<?php
		if (isset($_GET['n'])) {
			$node = "nodes/" . $_GET['n'] . ".php";

			if (!file_exists($node)) {
				$node = "nodes/404.php";
			}
		} elseif (!isset($_GET['n'])) {
			$node = "nodes/index.php";
		} else {
			$node = "nodes/404.php";
		}

		include_once($node);
		?>
	</div>
</main>
<?php include_once("views/footer.php"); ?>
</body>
</html>
