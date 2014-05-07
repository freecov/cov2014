#!/bin/sh
TARGET=$1

if [ "${TARGET}" = "" ];
then
	TARGET="covide-trunk"
fi

FILES=$(find ${TARGET} -maxdepth 3 -path ${TARGET}/classes/nusoap -prune -o -path ${TARGET}/menudata -prune -o -path ${TARGET}/classes/tbsooo -prune -o -path ${TARGET}/classes/html2pdf -prune -o -path ${TARGET}/editarea* -prune -o -path ${TARGET}/classes/Zend -prune -o -name *.php)

PROCESSFILES=""
for a in ${FILES};
do
	PROCESSFILES="${PROCESSFILES},${a}"
done

phpdoc -q -ti 'Covide API documentation' -pp -f ${PROCESSFILES} -t doc/${TARGET}/
