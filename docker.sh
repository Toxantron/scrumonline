#!/bin/bash
command=$1
current_dir=$(pwd)
container_name=scrumonline
image=scrum-lamp

case $command in 
  "prepare")
     echo "Preparing repository for usage with docker"
     # run container with entrypoint which prepares container
     docker run --rm --name scrumonline -v $(pwd):/var/www/scrumonline --entrypoint /var/www/scrumonline/build.sh scrum-lamp
     ;;
  "start")
     running=$(docker ps -a -q | grep $container_name)
     if [ -n "$running" ]; then
        echo "Stopping running containers"
        docker stop $running
        docker rm $running
     fi
     
     echo "Starting container $container_name..."
     mysql_dir=$2
     if [ -n "$mysql_dir" ]; then     
        docker run -d --name $container_name -p 8080:80 -p 3306:3306 \
                    -v $current_dir:/var/www/scrumonline -v $current_dir/$mysql_dir:/var/lib/mysql \
                    $image
     else
        docker run -d --name $container_name -p 8080:80 -p 3306:3306 \
                    -v $current_dir:/var/www/scrumonline $image
     fi                
     echo "...done!"
     ;;
  "stop")
     echo "Stopping container $container_name..."
     docker stop $container_name
     docker rm $container_name
     echo "...done"
     ;;
  "readlog")
     log_name=$2
     if [ -n "$log_name" ]; then
       docker exec -it $container_name tail -f /var/log/apache2/$log_name.log
     else
       echo "No log name specified"
     fi
     ;;
  "db")
     docker exec -it $container_name mysql scrum_online -u root --password=passwd
     ;;
  "myadmin")
     mycommand=$2
     case $mycommand in
        "stop")
           docker stop myadmin
           docker rm myadmin
           ;;
        *)
           docker run --name myadmin -d --link $container_name:db -p 8081:80 phpmyadmin/phpmyadmin
           ;;
    esac
     ;;
  "bash")
     docker exec -it $container_name bash
     ;;
  "")
     echo "No command specified!"
     ;;   
esac
