<?php
include_once("inc/include.php");

$commands[] = "ALTER TABLE nodes ADD `example_Col` int NOT NULL";
$commands[] = "SELECT * FROM nodes";

echo "<h1>Performing DB update</h1>";

echo "<ul>";
foreach ($commands AS $command) {
	echo "<li>Performing command: " . $command . "</li>";
	
	$command = "mysql --user='" . db_username . "' --password='" . db_password . "' -h '" . db_host . "' -D '" . db_name . "' -e '" . $command . "'";
	
	//echo $command;
	
	$output = shell_exec($command);
}
echo "</ul>";

echo "<p><strong>DB update complete</strong></p>";
?>