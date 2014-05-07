#!/bin/sh
cd ..
for a in `find lang/ -type f -name '*.po' | grep -v 'en_US'`; 
	do 
		echo "--- processing "$a;
		find . -type f -iregex '.*\.\(\(php\)\|\(js\)\)' -not -iwholename './tinymce*' -exec echo $a": "{} \; -exec xgettext -o $a -j -L php {} \;
done
echo "-- translations updated!";
