# Create Database

First you have to change the connection settings in your [`config.php`](Deployment.md). 
The database engine can create the schema itself. So just execute the following commands:

````bash
$ php bin/composer install
$ ./vendor/bin/doctrine orm:schema-tool:create
$ ./vendor/bin/doctrine orm:generate-proxies
````