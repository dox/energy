<?php
include_once("inc/include.php");

if (isset($_POST['install_attempt'])) {
  $filename = "sql_import.sql";

  if (!file_exists($filename)) {
	  die('mySQL import file does not exist!');
  }

  clearstatcache();

  $handle = fopen($filename, "r") or die("Can't open mySQL import file");
  while (($line = fgets($handle)) !== false) {
	//echo $line . "<br />";
	$database->query($line);
  }

  fclose($handle);

  echo "Install complete.  Please visit your site.";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="Andrew Breakspear">
  <title>Zendesk Scheduler: Installer</title>

  <!-- Bootstrap core CSS/JS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</head>

<!--<body>-->
<body class="bg-light">
	<div class="container d-flex w-100 h-100 p-3 mx-auto flex-column">
	<div class="px-3 py-3 pt-md-5 pb-md-4 text-center">
		  <h1 class="display-4">Energy DB Installer</h1>
		  <p class="lead">Create the required tables in your database.</p>
	  </div>

	<?php
	$buttonStatus = "";
	?>

	<!-- CHECK FOR DATABASE -->
	<?php
	if (isset($db)) {
	  $content = "<strong>DATABASE</strong> connection to database '" . db_name . "' on '" . db_host. "' successful";
	  echo makeAlert($content, "alert-success");
	} else {
	  $content = "<strong>DATABASE</strong> Cannot connect to database '" . db_name . "' on '" . db_host. "'";
	  echo makeAlert($content, "alert-danger");
	  $buttonStatus = "disabled";
	}
	?>

	<!-- CHECK FOR PRE-EXISTING INSTALL -->
	<?php
	  $sql  = "SHOW tables";
	  $tables = $db->query($sql);

	if (count($tables) == 0) {
	  $content = "<strong>TABLES</strong> Database has no tables (which is good!).  Ready to install";
	  echo makeAlert($content, "alert-success");
	} else {
	  $content = "<strong>TABLES</strong> Database already has tables present";
	  echo makeAlert($content, "alert-danger");
	  $buttonStatus = "disabled";
	}
	?>
	<form method="post" id="install_attempt_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" novalidate>
	  <input type="hidden" name="install_attempt" value="true" />
	  <button <?php echo $buttonStatus; ?> type="submit" class="btn btn-primary" id="install_attempt_button">CLICK HERE TO SETUP TABLES IN YOUR DATABASE</button>
	</form>


	  <?php include_once("views/footer.php"); ?>
	</div>
</body>
</html>


<?php
function makeAlert($content = null, $class = "alert-dark") {
  $output  = "<div class=\"alert " . $class . "\" role=\"alert\">";
  $output .= $content;
  $output .= "</div>";

  return $output;
}
?>