<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Email_migration {

	/* constants */
	const include_dir = "classes/email/inc/";
	const class_name = "email_migration";

	private $output;
	private $lockfile;

	/* methods */

    /* 	__construct {{{ */
    /**
     * 	__construct. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function __construct() {
		$this->output="";
		$this->lockfile = $GLOBALS["covide"]->temppath."migration.lock";
	}

	private function _archive_body($id) {
		$fspath = $GLOBALS["covide"]->filesyspath;

		$filename = $fspath."/maildata/".$id.".dat";
		if (@filesize($filename)>0) {
			$handle = fopen($filename, "r");
			$body = fread($handle, filesize($filename));
			fclose($handle);
		} else {
			$body = "";
		}
		return ($body);
	}

	public function writeLock() {
		$str = "mail migration in progress";
		$out = fopen($this->lockfile, "w");
		fwrite($out, $str);
		fclose($out);
	}

	public function readLock() {
		if (file_exists($this->lockfile)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function removeLock() {
		unlink($this->lockfile);
	}


	public function prepareMigration() {

		/* just a function call to initialize the complete index */
		$q = "select id, indexed from mail_messages where (indexed is null or indexed < 2) order by id asc";
		sql_query($q);

	}
	public function mailMigration() {

		$syntax = sql_syntax("escape");

		$output = new Layout_output();

		/* get the oldest email in the database */

		$blocksize = 40;
		for ($i=0;$i<$blocksize;$i++) {

			$q = "select id, header, body, ".$syntax."date".$syntax.", date_received from mail_messages where (indexed is null or indexed < 2) limit 1";
			$res = sql_query($q);
			if (sql_num_rows($res) > 0) {
				$row = sql_fetch_assoc($res);
				$body = $this->_archive_body($row["id"]);
				if (!$body) {
					$body = $row["body"];
				}
				$q = "insert into mail_messages_data (mail_id, body, header) values (";
				$q.= sprintf("%d, '%s', '%s')", $row["id"], addslashes($body), addslashes($row["header"]) );
				sql_query($q);

				$q = sprintf("update mail_messages set is_public = 1, body = '', header = '', indexed = 2 where id = %d", $row["id"]);
				sql_query($q);

				if (!$row["date_received"]) {
					if (!$row["date"]) {
						$row["date"] = time();
					}
					$q = sprintf("update mail_messages set date_received = '%d' where id = %d", (int)$row["date"], $row["id"]);
					sql_query($q);
				}

			} else {
				/* remove lockfile */
				$this->removeLock();

				$output->addCode("location.href='index.php?mod=email'");
				$output->exit_buffer();
			}
		}
		$output->addCode("update_stats('$blocksize')");
		$output->exit_buffer();
	}

	public function needMigration() {
		$q = "select id, project_id from mail_messages where project_id!=0";
		$res = sql_query($q);
		if (sql_num_rows($res) > 0) {
			while($row = sql_fetch_assoc($res)) {
				$q1 = sprintf("select * from mail_messages where project_id=%d and message_id=%d", $row["project_id"], $row["id"]);
				$res1 = sql_query($q1);
				if (sql_num_rows($res1) == 0) {
					$q = sprintf("insert into mail_projects (message_id, project_id) values (%d, %d)", $row["id"], $row["project_id"]);
					sql_query($q);
					$q = sprintf("update mail_messages set project_id = 0 where id=%d", $row["id"]);
					sql_query($q);
				}
			}
		}

		$q = "select mail_migrated from license";
		$res = sql_query($q);
		$result = sql_result($res,0);
		if ($result == 2) {
			return 0;
		} elseif ($result == 1) {
			/* check if there's old tracking data */
			$mail_data = new Email_data();
			$cleanTracking = $mail_data->cleanTrackingData();
			$q = "update license set mail_migrated = 2";
			sql_query($q);
		} else {
			$q = "select count(*) from mail_messages where indexed is null or indexed < 2";
			$res = sql_query($q);
			$count = sql_result($res,0);

			if ($count == 0) {
				$q = "update license set mail_migrated = 1";
				sql_query($q);
			}
			return $count;
		}
	}
	/* }}} */
}
?>
