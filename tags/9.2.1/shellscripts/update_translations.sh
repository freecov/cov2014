#!/bin/sh
# let's go to the root of the source if not there
curpath=`pwd`
curpath=`basename $curpath`
if [ "$curpath" = "shellscripts" ]; then
	cd ..
fi

OS=`uname -s`
if [ "$OS" = "OpenBSD" ]; then
	findcmd=gfind # remember to install the findutils package or port
else
	findcmd=find
fi
# update the .pot file
echo "--- Updating .po template"
$findcmd . -type f -path './tinymce*' -prune -o -path './editarea*' -prune -o -iregex '.*\.\(\(php\)\|\(js\)\)' -exec echo "processing sourcefile " {} \; -exec xgettext --force-po --no-wrap --package-name=Covide -o ./lang/covide.pot -j -L php {} \;
# merge the new .pot file with all the translations we have
for a in `find lang/ -type f -name '*.po' | grep -v 'en_US'`; 
	do 
		echo "--- processing "$a;
		msgmerge -U -N --no-wrap --force-po $a ./lang/covide.pot
done
echo "-- translations updated!";
