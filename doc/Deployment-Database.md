# Create Database
````
$ php bin/composer install
$ cp src/sample-config.php src/config.php
$ ./vendor/bin/doctrine orm:schema-tool:create
$ ./vendor/bin/doctrine orm:generate-proxies
````
The web root should point to the src directory.