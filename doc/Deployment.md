The easiest way is to deploy the application to your webserver and execute the following commands from the root directory. Make sure to adjust the config.php to your requirments

## Requirements
The app requires a couple of packages you need to install on your system. Those are:
- Apache with PHP >= 5.6
- MySQL
- MySQL-PDO

## Create Database
<pre>
$ php bin/composer install
$ cp src/sample-config.php src/config.php
$ vendor/bin/doctrine orm:schema-tool:create
$ vendor/bin/doctrine orm:generate-proxies
</pre>
The web root should point to the src directory.