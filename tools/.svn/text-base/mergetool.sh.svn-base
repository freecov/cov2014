#!/bin/sh

# Wrapper script for svnmerge

# Best way to use this would be to rename /usr/bin/svnmerge to
# /usr/bin/svnmerge.real and install this script as /usr/bin/svnmerge.

svnmerge=INSTALL_PREFIX/svnmerge.real

# find out how we are called. All symlinks to this file start with cvd so strip that
which=`basename ${0} | awk '{ print substr($0, 4, length($0)) }'`

if [ `echo ${which} | awk '{ print substr($0, length($0) - 10, length($0)) }'` = "stabletrunk" ]; then
	s_cmd=`echo ${which} | awk '{ print substr($0, 0, length($0) - 11) }'`
	command="${s_cmd} -P branch-stable-merged -B branch-stable-blocked -f ../merge.msg -r $1"
elif [ `echo ${which} | awk '{ print substr($0, length($0) - 7, length($0)) }'` = "trunkdev" ]; then
	s_cmd=`echo ${which} | awk '{ print substr($0, 0, length($0) - 8) }'`
	command="${s_cmd} -f ../merge.msg -r $1"
fi

${svnmerge} ${command}
