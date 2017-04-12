# Vagrant

## Requirements

* Vagrant
* vagrant-host-manager plugin

## Setup

Boot Vagrant machine login and go to the project directory:

```bash
vagrant up
vagrant ssh
cd /vagrant
```

Install the dependencies:
```bash
composer install
```

Create database:

```bash
./vendor/bin/doctrine orm:schema-tool:create
./vendor/bin/doctrine orm:generate-proxies
```

Copy prepared configuration:

```bash
cp src/sample-config.php src/config.php
```

## Mysql

Mysql credentials:
Login: root
Password: passwd
