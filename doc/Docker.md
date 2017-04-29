# Docker Image

based on [tutum/lamp](https://github.com/tutumcloud/lamp). It was specialized for scrumonline.

## Software Stack
The image creates a basic container of debian jessie and the standard LAMP stack.

* Apache2
* PHP5.6
* MySQL 5

## Build
The image needs to be build once on every machine before using it. Using the *docker* command may require root access.

```sh
cd docker
docker build -t scrum-lamp .
```

**Note:** When rebuilding the image the old one is not replaced. To avoid wasting disk space you should delete the old image before
running `docker build` again. Delete the old image by running:

```sh
docker rmi scrum-lamp
docker build -t scrum-lamp .
```

## Usage
While you can use the standard docker commands it is recommended to use the `docker.sh` script in the root directory.

```sh
# Prepare repository and database. This must be called before calling start for the first time
docker.sh prepare

# All other commands might require root access, e.g. 'sudo ./docker.sh start'
# Start container instance on localhost:8080 using empty database from image
docker.sh start
# Start container, but initialize and reuse database in directory 'mysql_db'
docker.sh start mysql_db

# Enter bash
docker.sh bash
# Access logs
docker.sh readlog error
docker.sh readlog access

# Access db
docker.sh db
# Start myadmin graphical UI on localhost:8081 with credentials myadmin:myadmin
docker.sh myadmin
# Stop myadmin
docker.sh myadmin stop

# Kill the running container
docker.sh stop
```