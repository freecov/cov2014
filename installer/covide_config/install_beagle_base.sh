#!/bin/sh

#Covide      : Cooperative Virtual Desktop copyright (c) 2000-2007 Covide BV
#License     : Licensed under GPL
#Web         : http://www.covide.net, http://covide.sourceforge.net
#Info        : info@covide.nl
#Author      : SvdHaar - svdhaar@users.sf.net
#Author      : MvanBaak - mvanbaak@users.sf.net
#Last update : November 13 2008


aptitude install beagle
mkdir /var/covide_files/.beagle
mkdir /var/covide_files/.beagle/config
chmod -R 775 /var/covide_files/.beagle
chown -R www-data.root /var/covide_files/.beagle
echo "Executing generate_beagle_config.sh..."
sh generate_beagle_config.sh
