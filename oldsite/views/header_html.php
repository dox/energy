<?php
	session_start();

	require_once('inc/config.php');
	require_once('database/MysqliDb.php');
	require_once('inc/adLDAP/adLDAP.php');
	require_once('inc/locations.php');
	require_once('inc/meters.php');
	require_once('inc/readings.php');

	$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);
?>

 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.6">
    <title>Utility Meter Readings</title>

    <link rel="canonical" href="http://readings.seh.ox.ac.uk">

    <!-- Bootstrap core CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/css/bootstrap.min.css" integrity="sha384-DhY6onE6f3zzKbjUPRc2hOzGAdEf4/Dz+WJwBvEYL/lkkIsI3ihufq9hk9K4lVoK" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link rel="stylesheet" href="css/bootstrap-editable.css">
<!-- Custom styles for this template -->
<link href="css/readings.css" rel="stylesheet">

<!-- Favicons -->
<meta name="theme-color" content="#563d7c">

<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha2/js/bootstrap.bundle.min.js" integrity="sha384-BOsAfwzjNJHrJ8cZidOg56tcQWfp6y72vEJ8xQ9w6Quywb24iOsW913URv1IS4GD" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js" integrity="sha256-TQq84xX6vkwR0Qs1qH5ADkP+MvH0W+9E7TdHJsoIQiM=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js"></script>
<script src="js/utils.js"></script>
<script src="js/bootstrap-editable.js"></script>
</head>

<?php
//you should look into using PECL filter or some form of filtering here for POST variables
if (isset($_POST["username"])) {
	$username = strtoupper($_POST["username"]); //remove case sensitivity on the username
	$password = $_POST["password"];
}

if (isset($_POST["oldform"])) { //prevent null bind
	if ($username != NULL && $password != NULL){
        try {
		    $adldap = new adLDAP();
        }
        catch (adLDAPException $e) {
            echo $e;
            exit();
        }

		//authenticate the user
		if ($adldap->authenticate($username, $password)){
			//establish your session and redirect

			$_SESSION["username"] = $username;
            $_SESSION["userinfo"] = $adldap->user()->info($username);
			$redir = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php";

			//$logSQLInsert = Array ("type" => "LOGON", "description" => $_SESSION["username"] . " logged on with LDAP");
			//$id = $db->insert ('_logs', $logSQLInsert);

			header($redir);
			exit;
		} else {
			//$logSQLInsert = Array ("type" => "LOGON FAIL", "description" => $username . " attempted to log on with LDAP");
			//$id = $db->insert ('_logs', $logSQLInsert);
		}
	}

	$message = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button><strong>Warning!</strong> Login attempt failed.</div>";
}

?>
