<?php
session_start();

$root = $_SERVER['DOCUMENT_ROOT'];

require_once($root . '/config.php');

if (debug == true) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

require $root . '/vendor/autoload.php';

use LdapRecord\Connection;

// Create a new connection:
$ldap_connection = new Connection([
    'hosts' => [LDAP_SERVER],
    'port' => LDAP_PORT,
    'base_dn' => LDAP_BASE_DN,
    'username' => LDAP_BIND_DN,
		'password' => LDAP_BIND_PASSWORD,
		'use_tls' => LDAP_STARTTLS,
]);
try {
    $ldap_connection->connect();
} catch (\LdapRecord\Auth\BindException $e) {
    $error = $e->getDetailedError();

    echo $error->getErrorCode();
    echo $error->getErrorMessage();
    echo $error->getDiagnosticMessage();
}


require_once($root . '/inc/globalFunctions.php');
require_once($root . '/inc/database.php');
require_once($root . '/inc/classSettings.php');
require_once($root . '/inc/classSite.php');
require_once($root . '/inc/classLogs.php');
require_once($root . '/inc/classLocation.php');
require_once($root . '/inc/classLocations.php');
require_once($root . '/inc/classNode.php');
require_once($root . '/inc/classNodes.php');
require_once($root . '/inc/classReadings.php');

$db = new db(db_host, db_username, db_password, db_name);
?>
