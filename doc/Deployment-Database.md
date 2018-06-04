# Create Database

First you have to change the connection settings in your [`config.php`](Deployment.md). 
The database engine can create the schema itself. So just execute the following commands:

````bash
$ php bin/composer install
$ ./vendor/bin/doctrine orm:generate-proxies
$ ./vendor/bin/doctrine orm:schema-tool:create
````

## Schema script

If you do not have CLI access to the target server you can generate a schema script instead. Just append `--dump-sql` to the last command. I recommend writing them to a file.

```bash
$ php bin/composer install
$ ./vendor/bin/doctrine orm:generate-proxies
$ ./vendor/bin/doctrine orm:schema-tool:create --dump-sql >> schema.sql
```
