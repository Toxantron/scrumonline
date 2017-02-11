The easiest way is to deploy the application to your webserver and execute the following commands from the root directory. Make sure to adjust the config.php to your requirments

# Requirements
The app requires a couple of packages you need to install on your system. Those are:
- Apache or Ngnix
- PHP >= 5.6
- MySQL, MySQL-PDO

# Webservers
- [Nginx Deploxment](Deploxment-Nginx.md)
- [Apache Deploxment](Deploxment-Apache.md)

# Configuration
The repository provides a sample config (config-sample.php). 
Rename the file to `config.php` and make your changes.

````
$ cp src/sample-config.php src/config.php
````

# Database
- [Create Database](Deploxment-Database.md)