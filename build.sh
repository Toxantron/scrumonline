#!/bin/bash
cd /var/www/scrumonline
echo Running composer update
/usr/bin/php ./bin/composer update --no-plugins --no-scripts
echo Running composer install
/usr/bin/php ./bin/composer install --no-plugins --no-scripts
echo Copying config
cp src/sample-config.php src/config.php
# Overwrite host
echo '$host = "http://localhost:8080";' >> src/config.php
