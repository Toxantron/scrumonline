#!/bin/bash

VOLUME_HOME="/var/lib/mysql"

sed -ri -e "s/^upload_max_filesize.*/upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}/" \
        -e "s/^post_max_size.*/post_max_size = ${PHP_POST_MAX_SIZE}/" /etc/php5/apache2/php.ini
    
# Make sure mysql is installed in directory
if [[ ! -d $VOLUME_HOME/mysql ]]; then
  echo "=> An empty or uninitialized MySQL volume is detected in $VOLUME_HOME"
  echo "=> Installing MySQL ..."
  mysql_install_db > /dev/null 2>&1
  echo "=> Done!"  
else
  echo "=> Using an existing volume of MySQL"
fi

# Make sure there is a scrumpoker database
if [[ ! -d $VOLUME_HOME/scrum_online ]]; then
  /utils/mysql_init.sh
fi

exec supervisord -n
