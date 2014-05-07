#!/bin/sh

# Covide installer (c) 2006-2007 Covide BV
#
# This script will install covide on a machine.
# It will create a database and place all files on the machine.
# It will also create the cronjobs and stuff
#
# Written by Michiel van Baak <mvanbaak@users.sourceforge.net>

# Covide installer is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 1 or 2 of the License.
#
# Covide installer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Foobar; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

VERSION="1.0.3"

# Some variables we need
# If not set ask the user for it later

DEBUG="NO"
INTERACTIVE="YES"

COVIDE_ROOT="/var/www/covide"
COVIDE_FILES="/var/covide_files"

# version can be one of the following:
# stable == stable updates on latest release
# trunk  == development tree. Use this if you want to test latest version
# vamos  == stable version of the vamos branch. Ues this to test it or if your name is sjk|svante
# kovoks == branch of kovoks. Use this to test it or if your name is toma|tom albers
COVIDE_VERSION="stable"

WWW_USER="www-data"
WWW_GROUP="www-data"

DB_TYPE="mysql" #can be mysql or pgsql

MYSQL_HOST="localhost"
MYSQL_USER="covide_install"
MYSQL_PASS="covide_install"
MYSQL_DB="covide_install"
MYSQL_ROOT_USER="root"
MYSQL_ROOT_PASS=""

PGSQL_HOST="localhost"
PGSQL_USER="covide"
PGSQL_PASS="covide"
PGSQL_DB="covide"

ADD_CRONJOB="YES" #can be YES or NO

#####################
# All magic is here #
#####################
_ask_question() {
	user_said=0
	QUESTION="$1"
	shift
	echo $QUESTION
	while [ "$user_said" -lt 1 -o "$user_said" -gt $# ]; do
		a=1
		for answer in "$@"; do
			echo "$a) $answer"
			let "a += 1"
		done
		read user_said
	done
}

_interactive() {
	if [ "$INTERACTIVE" == "YES" ]; then
		#function for interactive use. Set all vars we defined above
		echo "Covide Installer $VERSION Copyright (c) 2006-2007 Covide"
		echo ""
		echo "Covide installer comes with ABSOLUTELY NO WARRANTY;"
		echo "This is free software, and you are welcome"
		echo "to redistribute it under certain conditions;"
		echo "For more details see the LICENSE file;"
		echo ""
		echo "Before you begin installing Covide..."
		echo "Hit any key to read the REQUIREMENTS."
		echo "You can close the REQUIREMENTS by hitting the 'q'"
		read $a
		less <<!
System environment:
- Any environment with full support for the software listed below
- The use of Linux Debian Etch (http://www.debian.org, current stable release) is recommended, but Covide should run in all update to date *nix environments.

Webserver environment:
* Apache-SSL (1.3.29) -or-
* Apache2 (2.0.55) (recommended) -or-
* Any Apache2.2 server

Script environment:
* PHP 5.0.2 minimum (5.1.2 or 5.2 as Apache Module or higher strongly recommended) with the following extensions:
 - pgsql (PostgreSQL)
 - mysql (MySQL)
 - imap  (IMAP/POP3)
 - mbstring (MultiByte String)
 - session  (Session)
 - gettext  (Gettext)
 - pcre (Perl regular expressions)
 - zlib (gzip compression support)

* PHP APC (Alternative Php Cache)
 - the Covide CMS module extensively uses apc functions to dramatically reduce load  on the database and webserver on sites with many concurrent visitors.
   We strongly recommend  the installation the php apc module on high traffic web sites. This condition also applies when using the VoIP module with many concurrent Covide users.
   The php apc extension is available at http://pecl.php.net/package/APC

Database environment:
- PostgreSQL 7.4 minimum (PostgreSQL 8.0 or higher with TSearchV2 strongly recommended) -or-
- MySQL 4.1 minimum (MySQL 5.0 or higher strongly recommended)

* Note for CMS *:
- When using the CMS module, only Mysql 5.0 or higher is currently supported.


System programs:
- Beagle (Indexing tool) (version 0.2.4 recommended)
- UUDecode (decoder for uuencoded files) (version 4.2.1 recommended)
- UUDeview (decoder for various binary file formats) (version 2.5.20 recommended)
- TNef (Transport Neutral Encapsulation Format support) (version 1.4 recommended)
- PdfToHtml (pdf to html conversion)
- wvHtml (MsWord to html conversion)
- xlHtml (Excel to html conversion)
- unzip (unzip support)
- o3view (OpenOffice.org conversion)
- utf8tolatin1 (commandline utf8 to latin1 conversion)
- ELinks (Text based browser) (version 0.10.6 or higher recommended)
- convert (for image conversions)
- wmf2eps (wmf support)
- sfftobmp (Structured Fax File to Bitmap support)
- tiff2pdf (convert Tiff files to Pdf)
- Html2Ps (html to PostScript support)
- Ps2Pdf (PostScript to Pdf support)
- Curl (to retrieve images inside the pdf convertor)
- fortunes (for the Quote-of-the-Day)
!
		_ask_question "What version of Covide do you want to install?" "stable" "trunk" "vamos" "kovoks"
		_what_version=$user_said
		echo "Where do you want to have covide installed?"
		read COVIDE_ROOT
		echo "Where do you want the binary data to be located?"
		read COVIDE_FILES
		_ask_question "What database type do you want to use?" "MySQL" "PostgreSQL"
		_database_type=$user_said

		case $_database_type in
			2)
				DB_TYPE="pgsql"
				_ask_pgsql_info
				;;
			*)
				DB_TYPE="mysql"
				_ask_mysql_info
				;;
		esac

		case $_what_version in
			2)
				COVIDE_VERSION="trunk"
				;;
			3)
				COVIDE_VERSION="vamos"
				;;
			4)
				COVIDE_VERSION="kovoks"
				;;
			*)
				COVIDE_VERSION="stable"
				;;
		esac
		_ask_question "Should we create the default cronjobs?" "YES" "NO"
		if [ "$user_said" == 1 ]; then
			ADD_CRONJOB="YES"
		else
			ADD_CRONJOB="NO"
		fi
	fi
	
	if [ "$DEBUG" == "YES" ]; then
		_print_vars
	fi
}

_ask_mysql_info() {
	echo "What user has database and user creation access in mysql?"
	read MYSQL_ROOT_USER
	echo "What's this users password?"
	read MYSQL_ROOT_PASS
	echo "Database to create for covide?"
	read MYSQL_DB
	echo "What's the user that should get access to this database?"
	read MYSQL_USER
	
	# I'm not very good with coming up with good passwords. So, why not
	# generate one randomly? We can do this with openssl. Openssl is installed
	# on most machines, but we might want to check if openssl's actually installed...
	# -- sjk
	echo "What should its password be? [random]"
	read MYSQL_PASS

	if [ "$MYSQL_PASS" == "" ]; then MYSQL_PASS="random"; fi

	if [ "$MYSQL_PASS" == "random" ]; then
		MYSQL_PASS=`openssl rand -base64 6`
		echo "Password set to $MYSQL_PASS"
	fi
	
	echo "What's the location of the mysql database server?"
	read MYSQL_HOST
}

_ask_pgsql_info() {
	echo "Database to create for covide?"
	read PGSQL_DB
	echo "What's the user that should get access to this database?"
	read PGSQL_USER
	echo "What should it's password be?"
	read PGSQL_PASS
	echo "What's the location of the postgresql database server?"
	read PGSQL_HOST
}

_init() {
	echo -n "Setting up environment..."
	MYSQL_ROOT_CMD="mysql -s -h $MYSQL_HOST -u $MYSQL_ROOT_USER -p$MYSQL_ROOT_PASS"
	MYSQL_CMD="mysql -s -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASS $MYSQL_DB"
	echo "done."
}

_svn_checkout() {
	echo -n "Checking out sources from subversion..."
	#checkout copy from svn
	case "$COVIDE_VERSION" in
		stable)
			svn -q co https://covide.svn.sourceforge.net/svnroot/covide/branches/covide-stable $COVIDE_ROOT
			;;
		trunk)
			svn -q co https://covide.svn.sourceforge.net/svnroot/covide/trunk $COVIDE_ROOT
			;;
		vamos)
			svn -q co https://covide.svn.sourceforge.net/svnroot/covide/branches/vamos/stable $COVIDE_ROOT
			EXTRA_DB_PATCH="vamos.sql"
			;;
		kovoks)
			svn -q co https://covide.svn.sourceforge.net/svnroot/covide/branches/cvd_kovoks $COVIDE_ROOT
			EXTRA_DB_PATCH="kovoks-finance-scemes.sql kovoks-add-speeddial.sql kovoks-relations-scemes.sql kovoks-add-trasporter-and-contact.sql kovoks-products.sql kovoks-products-no-sell.sql"
			;;
		*)
			echo "You have to supply a covide version to checkout"
			exit 1;
			;;
	esac
	echo "done."
}

_fix_permissions() {
	echo -n "Setting permissions on various directories..."
	#make tmp folder webserver writable
	chown $WWW_USER:$WWW_GROUP $COVIDE_ROOT/tmp/
	#create directory for files and make it webserver writable
	mkdir $COVIDE_FILES
	chown $WWW_USER:$WWW_GROUP $COVIDE_FILES
	echo "done."
}

_create_mysql_db() {
	echo -n "Creating database and setting permissions..."
	#create db
	$MYSQL_ROOT_CMD -e "CREATE DATABASE $MYSQL_DB"
	$MYSQL_ROOT_CMD -e "GRANT ALL PRIVILEGES on $MYSQL_DB.* to $MYSQL_USER@'%' identified by '$MYSQL_PASS'"
	echo "done."
	echo -n "Reading default installer tables and data..."
	#read default install file into it
	$MYSQL_CMD  < $COVIDE_ROOT/sql/mysql/covide_6_install.sql
	echo "done."
	echo "Applying upstream patches to the databas..."
	#apply all patches
	for a in `ls $COVIDE_ROOT/sql/mysql/patches/*.sql`; do
		$MYSQL_CMD < $a
	done
	echo "done."
	echo -n "Applying vendor specific patches..."
	#apply version specific patches
	if [ "$EXTRA_DB_PATCH" != "" ]; then
		for i in $EXTRA_DB_PATCH; do
			$MYSQL_CMD < $COVIDE_ROOT/sql/mysql/$i
		done
	fi
	echo "done."
}

_create_pgsql_db() {
	echo ""
	echo "Automatic PostgreSQL creation warning!"
	echo ""
	echo "This is not implemented yet."
	echo "please create the database on $PGSQL_HOST by hand using:"
	echo "su - postgres"
	echo "createuser -E -P $PGSQL_USER"
	echo "createdb -O $PGSQL_USER -E latin1 $PGSQL_DB"
}

_create_config_files() {
	CONFIG_FILE=$COVIDE_ROOT/conf/offices.php
	case "$DB_TYPE" in
		mysql)
			dsn="$DB_TYPE://$MYSQL_USER:$MYSQL_PASS@tcp($MYSQL_HOST:3306)/$MYSQL_DB"
			;;
		pgsql)
			dsn="$DB_TYPE://$PGSQL_USER:$PGSQL_PASS@tcp($PGSQL_HOST:5432)/$PGSQL_DB"
			;;
	esac
	echo -e "<?php\n//Created with the covide installer. See offices.php.sample for more info\n\$dsn = \"$dsn\";\n?>" >  $CONFIG_FILE
}

_create_cronjob() {
	if [ "$ADD_CRONJOB" == "YES" ]; then
		current_crontab=`crontab -l`
		if [ "$?" == "1" ]; then
			$current_crontab=""
		fi
		#handle 'no crontab for root'
		echo -e "$current_crontab\n#covide_install.sh covide cronjob to fetch mail\n*/5\t*\t*\t*\t*\tsh $COVIDE_ROOT/shellscripts/mailfetch.sh" | crontab -
	fi
}

_print_vars() {
	if [ "$DEBUG" == "YES" ]; then
		echo "variable content:"
		echo "COVIDE_ROOT: $COVIDE_ROOT"
		echo "COVIDE_FILES: $COVIDE_FILES"
		echo "COVIDE_VERSION: $COVIDE_VERSION"
		echo "WWW_USER: $WWW_USER"
		echo "WWW_GROUP: $WWW_GROUP"
		echo "DB_TYPE: $DB_TYPE"
		echo "MYSQL_HOST: $MYSQL_HOST"
		echo "MYSQL_USER: $MYSQL_USER"
		echo "MYSQL_PASS: $MYSQL_PASS"
		echo "MYSQL_DB: $MYSQL_DB"
		echo "MYSQL_ROOT_USER: $MYSQL_ROOT_USER"
		echo "MYSQL_ROOT_PASS: $MYSQL_ROOT_PASS"
		echo "PGSQL_HOST: $PGSQL_HOST"
		echo "PGSQL_USER: $PGSQL_USER"
		echo "PGSQL_PASS: $PGSQL_PASS"
		echo "PGSQL_DB: $PGSQL_DB"
		echo "ADD_CRONJOB: $ADD_CRONJOB"
	fi
}

_show_help() {
	echo "Usage: $0 [-h] [-v] [-d] [-n]"
	echo ""
	echo "-h This help"
	echo "-v Show version and exit"
	echo "-d Enable debug mode"
	echo "-n Dont run in interactive mode."
}

_show_version() {
	echo "Covide installer $VERSION"
	echo "Copyright (c) 2006-2007 Covide BV"
}

_handle_args() {
	for arg in "$@"; do
		case $arg in
			-n)
				INTERACTIVE="NO"
				;;
			-d)
				_do_debug
				;;
			-h)
				_show_help
				exit 0
				;;
			-v)
				_show_version
				exit 0
				;;
			*)
				;;
		esac
	done
}

_do_debug() {
	DEBUG="YES"
	echo "DEBUG mode is ON"
	echo ""
}

main() {
	_handle_args "$@"
	_interactive
	_init
	_svn_checkout
	_fix_permissions
	_create_mysql_db
	_create_config_files
	_create_cronjob
}

main "$@"; exit 0;
