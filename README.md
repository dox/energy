# energy
A simple utility to record, review and report on utility meter readings

A working, live example of this utility can be viewed at: http://readings.seh.ox.ac.uk


## Installation
* cd to your web directory and run ```git clone https://github.com/dox/energy .```
* Then install (via composer) [ldaprecord](https://ldaprecord.com)
    * ```composer require directorytree/ldaprecord```
* Create a database in mysql and include the host, database, username and password in config.php
* Modify the inc/config.php file with your LDAP/other settings
* Visit http://yourdomeain/install.php and click 'CLICK HERE TO SETUP TABLES IN YOUR DATABASE'.  This will create the structure for the database
* Check your site it up and running (it should be!)

## Upgrading
Upgrading is as simple as running ```git pull``` in the directory you created above.
