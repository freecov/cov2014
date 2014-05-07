#!/bin/sh

#Covide      : Cooperative Virtual Desktop copyright (c) 2000-2008 Covide BV
#License     : Licensed under GPL
#Web         : http://www.covide.net, http://covide.sourceforge.net, http://www.covide.nl
#Info        : info@covide.nl
#Author      : SvdHaar - svdhaar@users.sourceforge.net
#Author      : MvanBaak - mvanbaak@users.sourceforge.net
#Last update : August 29 2008

echo -n "Making backup of /etc/apt/sources.list..."
mv /etc/apt/sources.list /etc/apt/sources.list.before.covide.installer
echo "done."
echo -n "Creating new /etc/apt/sources.list..."
echo "deb http://debian.bit.nl/debian/ etch main contrib non-free" > /etc/apt/sources.list
echo "deb http://security.debian.org/ etch/updates main contrib non-free" >> /etc/apt/sources.list
echo "deb http://www.backports.org/debian etch-backports main contrib non-free" >> /etc/apt/sources.list
echo "done."
aptitude update
aptitude install debian-backports-keyring
aptitude update
aptitude dist-upgrade
aptitude install ssh sudo
mkdir /var/covide_files
cp covide_config.tgz /var/covide_files
cd /var/covide_files
tar zxvf covide_config.tgz
cd covide_config
sh install_covide_base.sh
sh install_beagle_base.sh
sh covide_install.sh
echo "."
echo "."
echo "Setup finished."
