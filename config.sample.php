<?php
DEFINE("debug", true);

DEFINE("db_host", "localhost");
DEFINE("db_name", "database");
DEFINE("db_username", "username");
DEFINE("db_password", "password");

DEFINE("site_name", "Utility Readings");

DEFINE("reset_url", null); // where you want to redirect users to reset their LDAP password

# LDAP OPTIONS
define("LDAP_ENABLE", true);
define("LDAP_SERVER", "server-IP");
define("LDAP_PORT", 389);
define("LDAP_STARTTLS", false);
define("LDAP_BIND_DN", "CN=some,OU=where,DC=ox,DC=ac,DC=uk");
define("LDAP_BASE_DN", "DC=ox,DC=ac,DC=uk");
define("LDAP_BIND_PASSWORD", "ldap-password");
?>
