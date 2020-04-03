#!/bin/bash
cd /var/www/scrumonline
/usr/bin/php ./bin/composer update --no-plugins --no-scripts
/usr/bin/php ./bin/composer install --no-plugins --no-scripts
cp src/sample-config.php src/config.php
