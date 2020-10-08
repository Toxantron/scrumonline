FROM php:7.4-apache

# propagate environment vars from .env file
ENV MYSQL_HOST $MYSQL_HOST \
    MYSQL_ROOT_PASSWORD $MYSQL_ROOT_PASSWORD \
    MYSQL_DATABASE $MYSQL_DATABASE \
    MYSQL_USER $MYSQL_USER \
    MYSQL_PASSWORD $MYSQL_PASSWORD \
    MYSQL_CHECK_TABLE $MYSQL_CHECK_TABLE \
    MYSQL_CHECK_DATABASE $MYSQL_CHECK_DATABASE

# update existing packages / install required packages / cleanup
RUN apt-get -y update \
    && apt-get -y install zip default-mysql-client\
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install database connection extensions
RUN docker-php-ext-install mysqli pdo_mysql

#  create vhost
RUN printf "\n\
<VirtualHost *:80> \n\
        DocumentRoot /var/www/html/src \n\
 \n\
        <Directory /var/www/html/src> \n\
           AllowOverride All \n\
           Require all granted  \n\
        </Directory> \n\
 \n\
        ErrorLog ${APACHE_LOG_DIR}/error.log \n\
        CustomLog ${APACHE_LOG_DIR}/access.log combined \n\
</VirtualHost> \n\
" > /etc/apache2/sites-enabled/000-default.conf

# add project specific files
COPY . /var/www/html

# copy sample configuration
RUN cp /var/www/html/src/sample-config.php /var/www/html/src/config.php

# overwrite previous (sample) configuration (redundant definition of $conn feeded by environment)
RUN printf "\n\
\n\
// docker specific database configuration parameters \n\
\$conn = array( \n\
    'dbname' => \$_ENV['MYSQL_DATABASE'], \n\
    'user' => \$_ENV['MYSQL_USER'], \n\
    'password' => \$_ENV['MYSQL_PASSWORD'], \n\
    'host' => \$_ENV['MYSQL_HOST'], \n\
    'driver' => 'pdo_mysql', \n\
); \n\
 \n\
\$layout_switch = [ \n\
    'enable_fork_banner' => \$_ENV['SCRUMONLINE_ENABLE_FORK_BANNER'], \n\
    'enable_qr_code' => \$_ENV['SCRUMONLINE_ENABLE_QR_CODE'] \n\
]; \n\
 \n\
" >> /var/www/html/src/config.php

# change workdir
WORKDIR /var/www/html

# initialize project
RUN composer install
# the following commands need a working container network/name resolution; moved to entrypoint
#RUN ./vendor/bin/doctrine orm:generate-proxies
#RUN ./vendor/bin/doctrine orm:schema-tool:create

# execute entrypoint
RUN chmod +x /var/www/html/entrypoint.sh
CMD ["/var/www/html/entrypoint.sh"]
