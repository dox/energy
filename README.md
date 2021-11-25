# energy
A simple utility to record, review and report on utility meter readings

A working, live example of this utility can be viewed at: http://readings.seh.ox.ac.uk


## Installation
* Setup your webserver as you normally would (Apache with PHP and mySQL work well!)
* cd to your web directory and run ```git clone https://github.com/dox/energy .```
* Then install (via composer) [ldaprecord](https://ldaprecord.com)
    * ```composer require directorytree/ldaprecord```
* Create a database in mysql and include the host, database, username and password in config.php
* Modify the inc/config.php file with your LDAP settings
* Visit http://yourdomain/install.php and click 'CLICK HERE TO SETUP TABLES IN YOUR DATABASE'.  This will create the structure for the database
* Check your site is up and running (it should be!)

## Upgrading
Upgrading is as simple as running ```git pull``` in the directory you created above.
