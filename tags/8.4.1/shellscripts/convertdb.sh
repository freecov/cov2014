#!/bin/sh
#check if lockfile exists. If so, bail out
if [ -f "/var/run/covide.pid" ]; then
	echo "covide.pid already found! terminating script..."
	exit 1
fi

# find out php and www user
if [ -x /usr/bin/php5 ]; then
	# At least debian stable and testing have php5 cli here
	PHP_BIN=/usr/bin/php5
	WWWUSER=www-data
elif [ -x /usr/local/php5/bin/php ]; then
	# Moc OSX with entropy php5
	PHP_BIN=/usr/local/php5/bin/php
	WWWUSER=www
else
	echo "Could not find your php cli binary."
	exit 1
fi

#create lockfile
touch /var/run/covide.pid

#prepare some variables we need later
date=`date +%d-%m-%Y`
time=`date +%H:%M:%S`

cd ..
if [ -n "$*" ]
then
	nice sudo -u ${WWWUSER} ${PHP_BIN} index.php $*

else
	echo "syntax: convertdb.sh --host=<hostname> --convert=<(innodb|myisam|reindex)> --password=<administrator password>"
fi
rm /var/run/covide.pid
