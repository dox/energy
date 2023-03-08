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

if (isset($_POST['inputUsername']) && isset($_POST['inputPassword'])) {
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
    
    $cleanUsername = addslashes($_POST['inputUsername']);

    if ($ldap_connection->auth()->attempt($cleanUsername . LDAP_ACCOUNT_SUFFIX, $_POST['inputPassword'], $stayAuthenticated = true)) {
        // Successfully authenticated user.
        $user = $ldap_connection->query()->where('samaccountname', '=', $cleanUsername)->firstOrFail(); //look up user
        $userGroups = $user['memberof']; //get user's groups

        if (in_array(strtolower(LDAP_ALLOWED_DN), array_map('strtolower',$userGroups))) {
            // User is in allowed group for logon
            $_SESSION['logon'] = true;
            $_SESSION['username'] = strtoupper($cleanUsername);
        
            $logArray['category'] = "logon";
            $logArray['type'] = "success";
            $logArray['value'] = $_SESSION['username'] . " logged on successfully";
            $logsClass->create($logArray);
        } else {
            // User is not in correct group for logon
            $logArray['category'] = "logon";
            $logArray['type'] = "warning";
            $logArray['value'] = $cleanUsername . " failed to log on.  Not in correct group";
            $logsClass->create($logArray);

            session_destroy();
        }

    } else {
        // Username or password is incorrect.
        $logArray['category'] = "logon";
        $logArray['type'] = "warning";
        $logArray['value'] = $cleanUsername . " failed to log on.  Username or password incorrect";
        $logsClass->create($logArray);
        
        session_destroy();
    }
}

if (isset($_GET['logout'])) {
    $logArray['category'] = "logon";
    $logArray['type'] = "success";
    $logArray['value'] = $_SESSION['username'] . " logged off successfully";
    $logsClass->create($logArray);
    
    unset($_SESSION);
    session_destroy();
    $_SESSION['logon'] = false;
}
?>
