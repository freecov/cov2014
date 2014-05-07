#!/bin/sh
#check if lockfile exists. If so, bail out
if [ -f "/var/run/covide.pid" ]; then
	echo "covide.pid already found! terminating script..."
	exit 1
fi

#create lockfile
touch /var/run/covide.pid

#prepare some variables we need later
date=`date +%d-%m-%Y`
time=`date +%H:%M:%S`

if [ -n "$1" ]
then
	cd $1
else
	cd ..
fi

#copy over codes (and make unique)
`cat /var/covide_files/codes.txt | sort | uniq | grep '.' > /tmp/covide_codes.txt`

for codes in `cat /tmp/covide_codes.txt`; do
	url=`cat /var/covide_files/${codes}-url.txt | cut -d '/' -f 3`
	nice sudo -u www-data /usr/bin/php5 index.php --host=${url}
done

rm /var/run/covide.pid
