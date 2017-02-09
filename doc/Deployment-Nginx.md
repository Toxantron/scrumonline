# Nginx Deployment

## Requirements
The app requires a couple of packages you need to install on your system. Those are:
- Nginx >=1.6
- PHP >= 5.6
- MySQL
- MySQL-PDO

## PHP Configuration

php.ini
````
extension=php_openssl.dll
extension=php_pdo_mysql.dll
````

## Nginx Configuration

````
server {
    listen       80;
    server_name  scrum.local;

    root   C:/http/projects/scrumonline/src;

    rewrite /api\/(\w+)\/(\w+) /api.php?c=$1&m=$2 ;

    location / {
        index  index.html index.htm index.php;
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9123;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
````