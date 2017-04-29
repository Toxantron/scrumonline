FROM debian:jessie

# Install packages
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && \
    apt-get -y install supervisor \
    apache2 libapache2-mod-php5 \
    mysql-server mysql-client php5-mysql \
    php5-xdebug php5-curl php5-imagick \
    curl wget vim

# Prepare apache/php config and directory
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
ADD php_config /etc/php5/apache2/php.ini
ADD php_config /etc/php5/cli/php.ini

# Utils folder
RUN mkdir /utils
RUN mkdir /utils/custom

# Run script
ADD run.sh /utils/run.sh
RUN chmod 755 /utils/run.sh

# MySQL configuration
ADD my.cnf /etc/mysql/conf.d/my.cnf
ADD mysql_init.sh /utils/mysql_init.sh
RUN chmod 755 /utils/mysql_init.sh

# Supervisor configurations
ADD supervisord-apache2.conf /etc/supervisor/conf.d/supervisord-apache2.conf
ADD supervisord-mysqld.conf /etc/supervisor/conf.d/supervisord-mysqld.conf
ADD start-apache2.sh /utils/start-apache2.sh
ADD start-mysqld.sh /utils/start-mysqld.sh

# Config with mod_rewrite
ADD apache_default /etc/apache2/sites-available/000-default.conf
ADD xdebug.ini /etc/php5/mods-available/xdebug.ini
RUN a2enmod rewrite
# Allow modification of http headers (needed for CORS)
RUN a2enmod headers

#Environment variables to configure php
ENV PHP_UPLOAD_MAX_FILESIZE 20M
ENV PHP_POST_MAX_SIZE 10M

# Add volumes for MySQL and webroot
VOLUME  ["/var/lib/mysql", "/var/www/scrumonline"]

EXPOSE 80 3306
CMD ["/utils/run.sh"]
