<?php
session_start();

require_once('config.php');

if (debug == true) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(1);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(0);
}

require 'vendor/autoload.php';

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


require_once('inc/globalFunctions.php');
require_once('inc/database.php');
//require_once('inc/adLDAP/adLDAP.php');
require_once('inc/classLocation.php');
require_once('inc/classLocations.php');
require_once('inc/classMeter.php');
require_once('inc/classMeters.php');
require_once('inc/classReadings.php');

//$db = new MysqliDb ($db_host, $db_username, $db_password, $db_name);
$db = new db(db_host, db_username, db_password, db_name);
?>
