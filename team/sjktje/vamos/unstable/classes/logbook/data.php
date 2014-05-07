<?php
/**
 * Covide Groupware-CRM Logbook module
 * 
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Svante Kvarnstrom <sjktje@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Svante Kvarnstrom
 * @package Covide
 */
Class Logbook_data {
	
	/* addLogEntry {{{ */
	/**
	 * Adds a new entry to the log.
	 * 
	 * Adds a new entry to the log. Taken an array of arguments with the 
	 * following structure:
	 *
	 * $options = Array(
	 *		module		=> string,
	 *		user_id		=> int,
	 *		message		=> string,
	 *		record_id	=> int
	 * );
	 * 
	 * @param array $options Array of options.
	 * @return No return value
	 */
	public function addLogEntry($options=array()) {
		if (!isset($options["module"])) 
			$options["module"] = "logbook";
			
		if (!isset($options["user_id"]))  
			$options["user_id"] = $_SESSION["user_id"];
			
		/* We don't want to add empty log messages */
		if (!preg_match('/^[\s\n]*$/', $options["message"])) {
			$sql = sprintf("INSERT INTO logbook (module,user_id,timestamp,message,record_id) ".
				"VALUES ('%s','%d','%d','%s','%d')", $options["module"], $options["user_id"],
				mktime(), $options["message"], $options["record_id"]);
			sql_query($sql);
		}
	} /* }}} */

	/* getLogEntries {{{ */
	/**
	 * Fetches log entries.
	 *
	 * This function fetches log entries from the logbook table. The function
	 * takes an array of options, much like the one in the addLogEntry function.
	 * The structure of the array:
	 * 
	 * $options = Array(
	 *		module		=> string,
	 *		record_id	=> int,
	 *      top 		=> int,
	 *		limit		=> int
	 * );
	 * 
	 * @param array $options Array of options
	 * @return array Array with log entries.
	 */
	public function getLogEntries($options=array()) {
		
		$sql = sprintf("SELECT user_id,timestamp,message FROM `logbook` WHERE `module` = '%s'", $options["module"]);
		
		/* If we've been given a record_id, only fetch log messages regarding that record_id */
		$options["record_id"] ? $record_id = " AND `record_id` = ".sprintf("%d", $options["record_id"]) : "";
		$sql .= $record_id;
		$sql .= " ORDER BY `timestamp` ASC";
		$res = sql_query($sql, "", $options["top"], $GLOBALS["covide"]->pagesize);

		/* We need this object later to retrieve usernames etc. */
		$user_data = new User_data();
		
		/* Now lets process the results */
		while ($row = sql_fetch_assoc($res)) {
			$row["username"] = $user_data->getUsernameById($row["user_id"]);
			$row["timestamp"] = date("Y-m-d H:m:s", $row["timestamp"]);
			$row["record_id"] = $options["record_id"];
			$logentries[] = $row;
		}

		return $logentries;
		
	} /* }}} */

	/* {{{ getLastLogEntry */
	/**
	 * Fetches newest log entry.
	 *
	 * This function takes an array of options and returns the latest log record of
	 * a specific module. One can use the limit key to limit how many chars of the
	 * log message should be displayed. If the length of the message is > that the 
	 * limit an "... read more" link will be appended.
	 *
	 * Structure of options array:
	 * Array(
	 *		module		=> string,
	 *		record_id	=> int,
	 *		limit		=> int,
	 *
	 * @param array $options Array of options
	 * @return array Array with log entry info.
	 */
	public function getLastLogEntry($options=array()) {
	
		/* We need this to be able to retrieve usernames etc. */
		$user_data = new User_data();

		/* Get length of message */
		$sql = sprintf("SELECT message FROM logbook WHERE module='%s' AND record_id='%d' ORDER BY timestamp DESC LIMIT 1",
			$options["module"], $options["record_id"]);

		$res = sql_query($sql);

		$row = sql_fetch_assoc($res);
		$length = strlen($row["message"]);
	
		/* Grab the $options["limit"] chars of message */
		$sql = sprintf("SELECT user_id,timestamp,message ".
			"FROM logbook WHERE module='%s' AND record_id='%d' ORDER BY timestamp DESC LIMIT 1",
			$options["module"], $options["record_id"]);
		$res = sql_query($sql);
		
		$logentry = sql_fetch_assoc($res);
		$logentry["message"] = substr($logentry["message"], 0, $options["limit"]);
		
		$logentry["username"] = $user_data->getUsernameById($logentry["user_id"]);
		$logentry["timestamp"] = date("Y-m-d H:m:s", $logentry["timestamp"]);
		$logentry["record_id"] = $options["record_id"];

		/*
		 * If the length of the message is >= limit, then add "... read more" 
		 * to the message. 
		 */
		if ($length > $options["limit"]) {		
			// Should use insertLink for this.
			$href = "javascript: popup('?mod=logbook&regmod=consultants&id=".$logentry["record_id"]."','showentries',600,500,1)";
			$logentry["message"] .= "... <a href=\"$href\">".gettext("read more")."</a>";
		}

		return $logentry;
	} /* }}} */ 
	
	/* getLogEntryCount {{{ */
	/**
	 * Counts number of log entries.
	 * 
	 * TODO multiline desc
	 *
	 * @param string $module Get log entries from this module.
	 * @param int $record_id Get log entries regarding record with this id.
	 * @return int Number of log entries, or "false" upon failure. 
	 */
	public function getLogEntryCount($module, $record_id) {
		if ($module) {
			$sql = sprintf("SELECT COUNT(*) FROM logbook WHERE module = '%s'", $module);

			$record_id ? $record_id = " AND record_id = ".sprintf("%d", $record_id) : "";
			$sql .= $record_id;

			$res = sql_query($sql);
			$count = sql_result($res, 0);
			return $count;
		} else {
			/* We were not given a module */
			return false;
		}
	} /* }}} */

}
