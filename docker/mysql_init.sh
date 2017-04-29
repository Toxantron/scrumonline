#!/bin/bash

/usr/bin/mysqld_safe > /dev/null 2>&1 &

RET=1
while [[ RET -ne 0 ]]; do
  echo "=> Waiting for confirmation of MySQL service startup"
  sleep 5
  mysql -uroot -e "status" > /dev/null 2>&1
  RET=$?
done

# Create user for myadmin
mysql -uroot -e "CREATE USER 'myadmin'@'%' IDENTIFIED BY 'myadmin'"
mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'myadmin'@'%'"
# Create database and set root password to match config
mysql -uroot -e "CREATE DATABASE scrum_online"
mysql -uroot -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('passwd');"

# Create database schema
pushd /var/www/scrumonline
./vendor/bin/doctrine orm:schema-tool:create
./vendor/bin/doctrine orm:generate-proxies
popd
