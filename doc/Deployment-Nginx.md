# Nginx Deployment

## Requirements
The app requires a couple of packages you need to install on your system. Those are:
- Nginx >=1.6
- PHP >= 5.6
- MySQL, MySQL-PDO

## PHP Configuration

Your PHP intance should contain the openssl module to load dependencies with the composer. 
For the database connection php mysql is necessary.

*php.ini*
````
extension=php_openssl.dll
extension=php_pdo_mysql.dll
````

## Nginx Configuration
The nginx configuration is really default. Add the virtual host to your section. 
If you are using php fastcgi, dont forget to change the port or add a unix socket.

As nginx will ignore apaches .htacces and it is [also bad](https://www.nginx.com/resources/wiki/start/topics/examples/likeapache-htaccess/), we add the needed rewrite rules to the servers configuration.

````
server {
    listen       80;
    server_name  scrum.local;

    root /var/www/scrumonline/src;

    rewrite /api\/(\w+)\/(\w+)\/(\d+)\/(\d+) /api.php?c=$1&m=$2&id=$3&mid=$4;

    rewrite /api\/(\w+)\/(\w+)\/(\d+) /api.php?c=$1&m=$2&id=$3;

    rewrite /api\/(\w+)\/(\w+) /api.php?c=$1&m=$2;

    location / {
        index  index.html index.htm index.php;
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9123; #sample port
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
````
