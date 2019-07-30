FROM debian:stretch
ENV DEBIAN_FRONTEND noninteractive

# Install tools
RUN apt-get update && \
    apt-get -y install curl wget vim \
    lsb-release gnupg \
    ca-certificates apt-transport-https 

# Download and register MySQL and PHP package source
RUN mkdir /download
RUN wget -P /download https://dev.mysql.com/get/mysql-apt-config_0.8.10-1_all.deb
RUN dpkg -i /download/mysql-apt-config*
RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -
RUN echo "deb https://packages.sury.org/php/ stretch main" | tee /etc/apt/sources.list.d/php.list

# Install packages
RUN apt-get update && \
    apt-get -y --allow-unauthenticated install supervisor \
    apache2 libapache2-mod-php7.3 \
    mysql-server mysql-client php7.3-mysql \
    php7.3-xdebug php7.3-curl php7.3-imagick

# Prepare apache/php config and directory
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
ADD php_config /etc/php/7.3/apache2/php.ini
ADD php_config /etc/php/7.3/cli/php.ini

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
ADD xdebug.ini /etc/php/7.3/mods-available/xdebug.ini
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
