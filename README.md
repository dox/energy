# energy
A simple utility to record, review and report on utility meter readings

A working, live example of this utility can be viewed at: http://readings.seh.ox.ac.uk

## Installation
* Setup your webserver as you normally would (Apache with PHP and mySQL work well!)
* cd to your web directory and run ```git clone https://github.com/dox/energy .``` (the full stop at the end of this command is required!)
* Then install (via composer) [ldaprecord](https://ldaprecord.com)
    * ```composer require directorytree/ldaprecord```
* If not already installed, install php-mysql
* Create a database in mysql and include the host, database, username and password in config.php (copy from config.sample.php)
* Modify the config.php file with your LDAP settings
* Visit http://yourdomain/install.php and click 'CLICK HERE TO SETUP TABLES IN YOUR DATABASE'.  This will create the structure for the database
* Check your site is up and running (it should be!)

## Upgrading
Upgrading is as simple as running ```git pull``` in the directory you created above.

# Support
https://www.paypal.me/andrewbreakspear?locale.x=en_GB

Thanks for using this tool (or any of the others I've built)!  Your support/sponsorship goes directly to allowing me to spend more time coding, and less time doing other things (like my day job, or ironing...)

I'm not a company, and I'm not employed as a developer - so finding time to sit down and code can be hard sometimes.  I'd love to continue to develop (and support!) the tools I've written so far, and contributing in this way allows that to happen.
