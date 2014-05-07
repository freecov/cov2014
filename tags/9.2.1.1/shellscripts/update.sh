#!/bin/sh
# ---------------------
# Covide updater script
# ---------------------
# NOTE: please run this script from the directory '/shellscripts/'
svn info | grep Revision
svn up ../sql ../
