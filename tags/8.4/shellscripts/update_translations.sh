#!/bin/sh
# let's go to the root of the source
cd ..
# update the .pot file
echo "--- Updating .po template"
find . -type f -iregex '.*\.\(\(php\)\|\(js\)\)' -not -iwholename './tinymce*' -exec echo "processing sourcefile " {} \; -exec xgettext --force-po --no-wrap --package-name=Covide -o ./lang/covide.pot -j -L php {} \;
# merge the new .pot file with all the translations we have
for a in `find lang/ -type f -name '*.po' | grep -v 'en_US'`; 
	do 
		echo "--- processing "$a;
		msgmerge -U -N --no-wrap --force-po $a ./lang/covide.pot
done
echo "-- translations updated!";
