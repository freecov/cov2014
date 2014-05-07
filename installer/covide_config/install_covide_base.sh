#!/bin/sh

#Covide      : Cooperative Virtual Desktop copyright (c) 2000-2008 Covide BV
#License     : Licensed under GPL
#Web         : http://www.covide.net, http://covide.sourceforge.net, http://www.covide.nl
#Info        : info@covide.nl
#Author      : SvdHaar - svdhaar@users.sourceforge.net
#Author      : MvanBaak - mvanbaak@users.sourceforge.net
#Last update : November 13 2008

aptitude install $(cat configs/covide.list)
mkdir /var/covide_files
chown -R www-data:root /var/covide_files
chmod -R 775 /var/covide_files
echo "."
echo "."
echo "Installing PEAR::MDB2..."
pear install MDB2 MDB2_Driver_mysql MDB2_Driver_mysqli MDB2_Driver_pgsql MDB2_Driver_sqlite
echo "Installing Covide specific php config..."
mv /etc/php5/apache2/php.ini /etc/php5/apache2/php.ini.before.covideinstaller
mv /etc/php5/cli/php.ini /etc/php5/cli/php.ini.before.covideinstaller
cp configs/php/php.ini /etc/php5/apache2
cp configs/php/php.ini /etc/php5/cli
echo "Changing default website"
mv /etc/apache2/sites-available/default /etc/apache2/sites-available/default.before.covideinstaller
cp configs/apache2/default /etc/apache2/sites-available
echo "Enabling apache modules"
a2enmod headers
a2enmod cache
a2enmod mem_cache
a2enmod rewrite
a2enmod ssl
echo "Enabling ssl"
echo "Listen 443" >> /etc/apache2/ports.conf
sh generate_covide_certificate.sh
echo "Reloading web server..."
/etc/init.d/apache2 force-reload
echo "."
echo "."
echo "Installation of Covide requirements completed successfully..."
