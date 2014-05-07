#!/bin/sh
#check if lockfile exists. If so, bail out
if [ -f "/var/run/covide_mailfetch.lock" ]; then
	exit 0
fi

#create lockfile
touch /var/run/covide_mailfetch.lock

#prepare some variables we need later
date=`date +%d-%m-%Y`
time=`date +%H:%M:%S`
wgetcommand="wget -q --connect-timeout=15 --read-timeout=300 --dns-timeout=30 --no-check-certificate -o /tmp/.wgetout -O /tmp/.mailfetchdata"

for codes in `cat /var/covide_files/codes.txt`; do
	url=`cat /var/covide_files/${codes}-url.txt`
	for b in `cat /var/covide_files/${codes}-users.txt`; do
		echo -n "[${date} ${time}] processing mail for office ${codes} and user ${b}..."
		${wgetcommand} "${url}${b}"
		echo " done."
	done
done
rm /var/run/covide_mailfetch.lock
