#!/bin/bash
COUNT_LOOPS=0
MAX_LOOPS=120
WAIT_TIME=5

# change base dir
cd /var/www/html

echo "Waiting for database service: checking database '${MYSQL_CHECK_DATABASE}'..."

ROWS=$(mysql -h ${MYSQL_HOST} -uroot -p${MYSQL_ROOT_PASSWORD} ${MYSQL_CHECK_DATABASE} -e "${MYSQL_CHECK_TABLE}"| grep -v ERROR | wc -l)
ROWS="${ROWS:-0}"

while [ $ROWS -eq 0 ]
do
    COUNT_LOOPS=$((COUNT_LOOPS+1))

    echo "Waiting for database connection... ($COUNT_LOOPS/$MAX_LOOPS)"
    sleep ${WAIT_TIME}

    ROWS=$(mysql -h db -uroot -p${MYSQL_ROOT_PASSWORD} ${MYSQL_CHECK_DATABASE} -e "${MYSQL_CHECK_TABLE}"| grep -v ERROR | wc -l)
    ROWS="${ROWS:-0}"

    if [ $COUNT_LOOPS -gt $MAX_LOOPS ]; then
        # loop limit reached
        echo "No database connection etablished. Abort."
        exit 1
    fi
done

echo "Database connection established! Proceeding..."

# check if first run
CONTAINER_ALREADY_STARTED="CONTAINER_ALREADY_STARTED_PLACEHOLDER"
if [ ! -e $CONTAINER_ALREADY_STARTED ]; then
    touch $CONTAINER_ALREADY_STARTED
    echo "Container started first time. Initializing Scrumonline..."
    # wait 
    composer install
    ./vendor/bin/doctrine orm:generate-proxies
    ./vendor/bin/doctrine orm:schema-tool:create
fi

echo "Starting web service..."
echo ""
echo "################################################################"
echo "#####                                                      #####"
echo "#####   Scrumonline is now accessible. Have a nice time!   #####"
echo "#####                                                      #####"
echo "################################################################"
/usr/sbin/apache2ctl -D FOREGROUND