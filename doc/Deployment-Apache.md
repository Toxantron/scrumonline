# Apache Deployment

## Requirements
The app requires a couple of packages you need to install on your system. Those are:
* Apache >= 2.2
  * mod_rewrite
* PHP >= 5.6
* MySQL, MySQL-PDO

You need to install and enable `mod_rewrite`. You can do this by following [this SO answer](http://stackoverflow.com/a/5758551/6082960) or simply run the commands (as root):

```sh
a2enmod rewrite
service apache2 restart
```

## Configuration
Configure Apache to run the application from the _src_ directory and make sure to ether `AllowOverride All` or move the rewrite rule from _.htaccess_ to the VirtualHost config.

With _.htaccess_:

```ini
<VirtualHost *:80>
        DocumentRoot /var/www/scrumonline/src

        <Directory /var/www/scrumonline/src>
           AllowOverride All
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Without _.htaccess_:

```ini
<VirtualHost *:80>
        DocumentRoot /var/www/scrumonline/src

        RewriteEngine on

        # Rule that includes session and member id
        RewriteRule ^/api/(\w+)/(\w+)/(\d+)/(\d+) /api.php?c=$1&m=$2&id=$3&mid=$4 [QSA]
        # Rule that includes the session id, mostly used for HTTP GET
        RewriteRule ^/api/(\w+)/(\w+)/(\d+) /api.php?c=$1&m=$2&id=$3 [QSA]
        # Standard rule for controller and method - applies to most queries
        RewriteRule ^/api/(\w+)/(\w+) /api.php?c=$1&m=$2 [QSA]

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```
