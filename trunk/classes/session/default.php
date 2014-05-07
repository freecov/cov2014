<?php
/**
 * Covide Groupware-CRM Session handling module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @copyright Copyright 2009 Covide BV
 * @package Covide
 */
Class Session {
	/* variables */
	/**
	 * @var int Max lifetime of a session
	 */
	public $lifetime;
	/* methods */
	/* __construct {{{ */
	/**
	 * Constructor to setup lifetime and session save handler
	 *
	 * @param int $lifetime Optional override of default session lifetime
	 *
	 * @return void
	 */
	public function __construct($lifetime = 0) {
		//grab lifetime from php configuration if not given
		if ($lifetime == 0) {
			$this->lifetime = get_cfg_var("session.gc_maxlifetime");
		} else {
			$this->lifetime = $lifetime;
		}
		//register our own functions for session handling
		session_set_save_handler(
			array(&$this, "open"),
			array(&$this, "close"),
			array(&$this, "read"),
			array(&$this, "write"),
			array(&$this, "destroy"),
			array(&$this, "gc")
		);
		//on shutdown make session readonly
		register_shutdown_function("session_write_close");
		//and start the sesssion
		session_start();
	}
	/* }}} */
	/* open {{{ */
	/**
	 * Open session, really does nothing
	 *
	 * @param string $save_path The session save path.
	 * @param string $session_name The session name.
	 *
	 * @return bool true
	 */
	public function open($save_path, $session_name) {
		return true;
	}
	/* }}} */
	/* close {{{ */
	/**
	 * Close session.
	 *
	 * @return bool true
	 */
	public function close() {
		return true;
	}
	/* }}} */
	/* read {{{ */
	/**
	 * Read session data.
	 * This function should ALWAYS return a string value to make the save handler work.
	 * If there's no data, return an empty string.
	 *
	 * @param string $session_id The session id to read
	 *
	 * @return string session data if any, empty string otherwise.
	 */
	public function read($session_id) {
		//grab the data stored for session_id for the specific HTTP_USER_AGENT and data that's not expired
		$sql = sprintf("SELECT session_data FROM sessions WHERE session_id = '%s' AND user_agent = '%s' AND expire > %d", sql_escape_string($session_id), sql_escape_string($_SERVER["HTTP_USER_AGENT"]), time());
		$res = sql_query($sql);
		if (sql_num_rows($res) > 0) {
			$row = sql_fetch_assoc($res);
			// We dont have to unserialize, PHP does that for us
			return $row["session_data"];
		}
		// if nothing there, return empty string
		return "";
	}
	/* }}} */
	/* write {{{ */
	/**
	 * Write session data to db
	 * This function is called after the output stream is closed.
	 * Debugging is only possible if you write debug info to a file.
	 *
	 * @param string $session_id The session id to update/create
	 * @param string $session_data The actual data to store
	 *
	 * @return bool true on success, false on failure
	 */
	public function write($session_id, $session_data) {
		// check if we already have this session in the database
		$sql = sprintf("SELECT COUNT(session_id) FROM sessions WHERE session_id = '%s'", sql_escape_string($session_id));
		$res = sql_query($sql);
		$count = sql_result($res, 0);
		if ($count > 0) {
			//update
			$sql = sprintf("UPDATE sessions SET session_data = '%s', expire = %d WHERE session_id = '%s'", sql_escape_string($session_data), (time() + $this->lifetime), sql_escape_string($session_id));
			$res = sql_query($sql);
			return true;
		} else {
			//insert
			$sql = sprintf("INSERT INTO sessions (session_id, session_data, expire, user_agent) VALUES ('%s', '%s', %d, '%s')", sql_escape_string($session_id), sql_escape_string($session_data), (time() + $this->lifetime), sql_escape_string($_SERVER["HTTP_USER_AGENT"]));
			$res = sql_query($sql);
			return true;
		}
	}
	/* }}} */
	/* destroy {{{ */
	/**
	 * Destroy a specific session
	 *
	 * @param string $session_id The session to destroy
	 *
	 * @return bool true on success, false on failure
	 */
	public function destroy($session_id) {
		$sql = sprintf("DELETE FROM sessions WHERE session_id = '%s'", sql_escape_string($session_id));
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* gc {{{ */
	/**
	 * Garbage collector ran by PHP itself
	 *
	 * @param int $maxlifetime The max lifetime a session can be in the database
	 *
	 * @return void
	 */
	public function gc($maxlifetime) {
		$sql = sprintf("DELETE FROM sessions WHERE expire < %d", (time() - $maxlifetime));
		$res = sql_query($sql);
	}
	/* }}} */
}
?>
