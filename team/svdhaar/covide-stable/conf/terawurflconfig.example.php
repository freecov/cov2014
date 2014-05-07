<?php
/* RENAME THIS FILE TO terawurflconfig.php and enjoy! (svdhaar@users.sourceforge.net) */
/*
 * Tera_WURFL - PHP MySQL driven WURFL
 * 
 * Tera-WURFL was written by Steve Kamerman, and is based on the
 * Java WURFL Evolution package by Luca Passani and WURFL PHP Tools by Andrea Trassati.
 * This version uses a MySQL database to store the entire WURFL file, multiple patch
 * files, and a persistent caching mechanism to provide extreme performance increases.
 * 
 * @package TeraWurfl
 * @author Steve Kamerman, stevekamerman AT gmail.com
 * @version Stable 2.0.0 $Date: 2009/11/13 23:59:59
 * @license http://www.mozilla.org/MPL/ MPL Vesion 1.1
 * $Id: TeraWurflConfig.php,v 1.11 2008/03/01 00:05:25 kamermans Exp $
 * $RCSfile: TeraWurflConfig.php,v $
 * 
 * Based On: Java WURFL Evolution by Luca Passani
 *
 */

class TeraWurflConfig{
	/**
	 * Database Hostname
	 * @var String
	 */
	public static $DB_HOST = "localhost";
	/**
	 * Database User
	 * @var String
	 */
	public static $DB_USER = "terawurfl";
	/**
	 * Database Password
	 * @var String
	 */
	public static $DB_PASS = 'secret!';
	/**
	 * Database Name / Schema Name
	 * @var String
	 */
	public static $DB_SCHEMA = "terawurfl";
	/**
	 * Database Connector (MySQL4 and MySQL5 are implemented at this time)
	 * @var String
	 */
	public static $DB_CONNECTOR = "MySQL5";
	/**
	 * Device Table Name
	 * @var String
	 */
	public static $DEVICES = "TeraWurfl";
	/**
	 * Device Cache Table Name
	 * @var String
	 */
	public static $CACHE = "TeraWurflCache";
	/**
	 * Device Index Table Name
	 * @var String
	 */
	public static $INDEX = "TeraWurflIndex";
	/**
	 * Merged Device Table
	 * @var String
	 */
	public static $MERGE = "TeraWurflMerge";
	/**
	 * URL of WURFL File
	 * @var String
	 */
	public static $WURFL_DL_URL = "http://downloads.sourceforge.net/project/wurfl/WURFL/latest/wurfl-latest.zip";
	/**
	 * URL of CVS WURFL File
	 * @var String
	 */
	public static $WURFL_CVS_URL = "http://wurfl.cvs.sourceforge.net/%2Acheckout%2A/wurfl/xml/wurfl.xml";
	/**
	 * Data Directory
	 * @var String
	 */
	public static $DATADIR = 'data/';
	/**
	 * Enable Caching System
	 * @var Bool
	 */
	public static $CACHE_ENABLE = true;
	/**
	 * Enable Patches (must reload WURFL after changing)
	 * @var Bool
	 */
	public static $PATCH_ENABLE = true;
	/**
	 * Filename of patch file.  If you want to use more than one, seperate them with semicolons.  They are loaded in order.
	 * ex: $PATCH_FILE = 'web_browsers_patch.xml;custom_patch_ver2.3.xml';
	 * @var String
	 */
	public static $PATCH_FILE = 'custom_web_patch.xml;web_browsers_patch.xml';
	/**
	 * Filename of main WURFL file (found in DATADIR; default: wurfl.xml)
	 * @var String
	 */
	public static $WURFL_FILE = 'wurfl.xml';
	/**
	 * Filename of Log File (found in DATADIR; default: wurfl.log)
	 * @var String
	 */
	public static $LOG_FILE = 'wurfl.log';
	/**
	 * Log Level as defined by PHP Constants LOG_ERR, LOG_WARNING and LOG_NOTICE.
	 * Should be changed to LOG_WARNING or LOG_ERR for production sites
	 * @var Int
	 */
	public static $LOG_LEVEL = LOG_WARNING;
	/**
	 * Enable to override PHP's memory limit if you are having problems loading the WURFL data like this:
	 * Fatal error: Allowed memory size of 67108864 bytes exhausted (tried to allocate 24 bytes) in TeraWurflLoader.php on line 287
	 * @var Bool
	 */
	public static $OVERRIDE_MEMORY_LIMIT = false;
	/**
	 * PHP Memory Limit.  See OVERRIDE_MEMORY_LIMIT for more info
	 * @var String
	 */
	public static $MEMORY_LIMIT = "256M";
}
?>
