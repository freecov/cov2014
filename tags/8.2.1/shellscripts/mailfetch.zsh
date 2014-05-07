#!/bin/zsh
#check if lockfile exists. If so, bail out
if [[ -a "/var/run/covide_mailfetch.lock" ]]
then
	exit 0
fi

#create lockfile
touch /var/run/covide_mailfetch.lock

#prepare some variables we need later
datestring=`date +%d-%m-%Y`
timestring=`date +%H:%M:%S`

for codes in `cat /var/covide_files/codes.txt`
do
	mailurl=`cat /var/covide_files/$codes-url.txt`
	calendarurl=`cat /var/covide_files/$codes-calendar-url.txt`
	for b in `cat /var/covide_files/$codes-users.txt`
	do
		echo -n "[$datestring $timestring] processing mail for office $codes and userid $b..."
		wget -q --connect-timeout=15 --read-timeout=300 --dns-timeout=30 --no-check-certificate -o /tmp/.wgetout -O /tmp/.mailfetchdata "$mailurl$b"
		echo " done."

		echo -n "[$datestring $timestring] processing external calendars for office $codes and userid $b..."
		wget -q --connect-timeout=15 --read-timeout=300 --dns-timeout=30 --no-check-certificate -o /tmp/.wgetout -O /tmp/.mailfetchdata "$calendarurl$b"
		echo " done."
	done
done
rm /var/run/covide_mailfetch.lock
