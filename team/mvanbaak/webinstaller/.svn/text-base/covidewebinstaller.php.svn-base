<?php
/**
 * Covide office create tool for Terrazur Hosted Platform
 *
 * Copyright 2008 Terrazur BV
 *
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2008 Terrazur BV
 * @version 0.1
 */
Class covidewebinstaller {
	/* __construct {{{ */
	/**
	 * Connect to database server etc
	 */
	public function __construct() {
		// read configuration file
		$this->configuration = $this->_readConf();
		// connect to database server
		$this->db = $this->_connectDB($this->configuration["database"]);
		$this->configdb = "covideconfig";
	}
	/* }}} */
	/* _readConf {{{ */
	/**
	 * Read configuration file
	 *
	 * @return array Array with configuration paramaters
	 */
	private function _readConf() {
		$configdata = parse_ini_file("conf/settings.ini.php", true);
		return $configdata;
	}
	/* }}} */
	/* _connectDB {{{ */
	/**
	 * Connect to database server
	 *
	 * @param array $dbsettings Assoc array with host, username and password
	 *
	 * @return mixed database connection resource on succes, false on failure
	 */
	private function _connectDB($dbsettings) {
		$link = mysql_connect($dbsettings["hostname"], $dbsettings["username"], $dbsettings["password"]);
		if (!$link) {
			return false;
		} else {
			return $link;
		}
	}
	/* }}} */
	/* _run_query {{{ */
	/**
	 * Internal function to run an sql query.
	 * If devmode = 1 in the configuration file, it will not actually
	 * run the query, but echo it.
	 *
	 * @param string $query The sql query to run
	 *
	 * @return bool true on success, false on failure
	 */
	private function _run_query($query) {
		if ($this->configuration["general"]["devmode"]) {
			echo $query;
			return true;
		} else {
			if (mysql_query($query)) {
				return true;
			} else {
				return false;
			}
		}
	}
	/* }}} */
	/* checkOfficeByName {{{ */
	/**
	 * Check if the officename is available
	 *
	 * @param string $officename The officename to check
	 *
	 * @return bool true if the name is available, false if it's in use
	 */
	public function checkOfficeByName($officename) {
		// do we need a database prefix ?
		if (array_key_exists("prefix", $this->configuration["database"]) && trim($this->configuration["database"]["prefix"])) {
			$officename = sprintf("%s%s", trim($this->configuration["database"]["prefix"]), $officename);
		}
		// first get all database names from the server
		$res = mysql_list_dbs($this->db);
		while ($row = mysql_fetch_assoc($res)) {
			if ($row["Database"] == $officename) {
				return false;
			}
		}
		return true;
	}
	/* }}} */
	/* createDB {{{ */
	/**
	 * Create the database for a new office
	 *
	 * @param string $database The database name
	 *
	 * @return bool true on succes, false on failure
	 */
	public function createDB($database) {
		// do we need a database prefix ?
		if (array_key_exists("prefix", $this->configuration["database"]) && trim($this->configuration["database"]["prefix"])) {
			$database = sprintf("%s%s", trim($this->configuration["database"]["prefix"]), $database);
		}
		$sql = sprintf("CREATE DATABASE %s", $database);
		return $this->_run_query($sql);
	}
	/* }}} */
	/* setDBPermissions {{{ */
	/**
	 * Give a user permissions on a database
	 *
	 * @param string $database The database name to give access on
	 * @param string $username The username that should get access
	 * @param string $password The password for this username
	 * @param string $hostname The hostname this user will cannect from, % for any
	 *
	 * @return bool true on success, false on failure
	 */
	public function setDBPermissions($database, $username, $password = "", $hostname = "%") {
		if (!$database || !$username) {
			// no database/username given
			return false;
		}
		// do we need a database prefix ?
		if (array_key_exists("prefix", $this->configuration["database"]) && trim($this->configuration["database"]["prefix"])) {
			$database = sprintf("%s%s", trim($this->configuration["database"]["prefix"]), $database);
		}
		if ($hostname = "%") {
			$sql = sprintf("GRANT ALL PRIVILEGES ON %s.* TO %s@'%s'", $database, $username, $hostname);
		} else {
			$sql = sprintf("GRANT ALL PRIVILEGES ON %s.* TO %s@%s", $database, $username, $hostname);
		}
		if ($password) {
			$sql .= sprintf(" IDENTIFIED BY '%s'", $password);
		}
		return $this->_run_query($sql);
	}
	/* }}} */
	/* readDBStructure {{{ */
	/**
	 * Read the basic database structure into the database.
	 * The structure file is specified in the configuration file,
	 * in the database section with a setting called structurefile.
	 *
	 * @param string $database The database to read the structure in
	 *
	 * @return bool true on success, false on failure
	 */
	public function readDBStructure($database) {
		// see if we have a valid setting for the structure file
		if (array_key_exists("structurefile", $this->configuration["database"]) && file_exists($this->configuration["database"]["structurefile"])) {
			mysql_select_db($database);
			$sql = sprintf("source %s", $this->configuration["database"]["structurefile"]);
			$res = $this->_run_query($sql);
			mysql_select_db($this->configdb);
			return $res;
		} else {
			return false;
		}
	}
	/* }}} */
	/* setLicense {{{ */
	/**
	 * Set various license settings for the new office
	 *
	 * @param string $database The new office database name
	 * @param array $license
	 *
	 * @return bool true on success, false on failure
	 */
	public function setLicense($database, $license) {
		return true;
	}
	/* }}} */
	/* createConfig {{{ */
	/**
	 * Create configuration for this office in the global configuration scope of covide
	 *
	 * @param string $office The office name
	 * @param array $database_config Array with database name, username and password
	 * @param string $officeurl Main url to access the office
	 * @param array $extra_hosts Optional array with additional hostnames that this office wants
	 *
	 * @return bool true on success, false on failure
	 */
	public function createConfig($office, $database_config = array(), $officeurl, $extra_hosts = array()) {
		// the config file can have a section 'configurationfile' where you
		// can set the default username, host, password, hostprefix etc.
		// We use this by default, and allow elements in the $database_config
		// array overwrite it.
		$conf = $this->configuration["configurationfile"];
		foreach ($database_config as $k=>$v) {
			$conf[$k] = $v;
		}
		// connect to config db just to be sure
		mysql_select_db($this->configdb);
		// now that we have everything in an array, lets create a file
		$configuration_file = sprintf("conf_%s.php", $office);
		$dsn_string = sprintf("mysql://%s:%s@tcp(%s:3306)/%s%s", $conf["database_user"], $conf["database_pass"],
			$conf["database_host"], $this->configuration["database"]["prefix"], $office);
		
		$sql = sprintf("INSERT INTO offices (officename, officeurl, dsn, created) VALUES ('%s', '%s', '%s', %d)",
			$office, $officeurl, $dsn_string, mktime());
		$res = mysql_query($sql);
		if (!$res) {
			return false;
		}
		if (count($extra_hosts)) {
			//grab the inserted id
			$host_id = mysql_insert_id();
			//insert the extra hosts in the database
			foreach ($extra_hosts as $host) {
				$sql = sprintf("INSERT INTO offices_urls (offices_id, officeurl) VALUES (%d, '%s')",
					$host_id, $host);
				$res = mysql_query($sql);
				if (!$res) {
					return false;
				}
			}
			
		}
		return true;
	}
	/* }}} */
}
?>
