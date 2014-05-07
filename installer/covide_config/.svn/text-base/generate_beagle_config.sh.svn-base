#!/bin/bash

#Covide      : Cooperative Virtual Desktop copyright (c) 2000-2007 Covide BV
#License     : Licensed under GPL
#Web         : http://www.covide.net, http://covide.sourceforge.net
#Info        : info@covide.nl
#Author      : SvdHaar - svdhaar@users.sf.net
#Author      : MvanBaak - mvanbaak@users.sf.net
#Last update : November 13 2008


echo "Stopping beagle..."
export BEAGLE_HOME=/var/covide_files
beagle-shutdown
sleep 1

echo "Removing old config files..."
rm -rf /var/covide_files/.beagle

echo "Creating /var/www/.wapi ..."
mkdir /var/www/.wapi
chown -R www-data: /var/www/.wapi
chmod -R 775 /var/www/.wapi

echo "Installing template config files..."
mkdir /var/covide_files/.beagle
mkdir /var/covide_files/.beagle/config

echo "Updating permissions on /var/covide_files..."
chown www-data: /var/covide_files
chmod -R 775 /var/covide_files

echo "Installing beagle config files in /var/covide_files ..."
cp configs/beagleconfig/* /var/covide_files/.beagle/config
cat /var/covide_files/.beagle/config/indexing.prefix > /var/covide_files/.beagle/config/indexing.xml

for a in `find /var/covide_files -type d | grep '\(bestanden\)\|\(email\)\|\(maildata\)'`;
do
	echo '<Root>'$a'</Root>' >> /var/covide_files/.beagle/config/indexing.xml; 
done

cat /var/covide_files/.beagle/config/indexing.suffix >> /var/covide_files/.beagle/config/indexing.xml

echo "Installing beagle config files in /etc/beagle ..."
cp configs/external-filters.xml /etc/beagle

echo "Installing beagle start/stop script in /etc/init.d ..."
cp configs/beagle /etc/init.d/beagle

echo "Settings permissions on config files..."
chown root: /etc/beagle/external-filters.xml
chmod 775 /etc/beagle/external-filters.xml
chown www-data: /var/covide_files/.beagle
chmod -R 775 /var/covide_files/.beagle
chown root: /etc/init.d/beagle
chmod 775 /etc/init.d/beagle

echo "Adding beagle to startup scripts..."
update-rc.d beagle defaults

echo "Starting beagle..."
/etc/init.d/beagle start

echo "."
echo "."
echo "Beagle install finished. Do not forget to run generate_beagle_config.sh after every new Covide install."
