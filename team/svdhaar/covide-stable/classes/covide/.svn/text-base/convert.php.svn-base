<?php
/**
 * Covide Convert module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

class Covide_convert {

	public function __construct() {
		set_time_limit(60*60*4);
		ob_clean();
		ob_start();
		echo "Initializing...\n";

		if (!$_REQUEST["param"]["password"]) {
			echo "Please supply the administrator password with --password=<password>\n";
			exit();
		} else {
			$q = sprintf("select count(*) from users where username = 'administrator' and password = '%s'",
				$_REQUEST["param"]["password"]);
			$res = sql_query($q);
			if (sql_result($res,0) == 0) {
				echo "Administrator password is not valid.\n";
				exit();
			}
		}
		switch ($_REQUEST["param"]["convert"]) {
			case "myisam":
				$this->convertDatabase("myisam");
				break;
			case "innodb":
				$this->convertDatabase("innodb");
				break;
			case "reindex":
				$this->convertDatabase("reindex");
				break;
			default:
				echo "Please supply a valid conversion target with --convert=<(innodb|myisam)>\n";
		}
	}

	private function dropindex($table) {
		/* scan for any indexes */
		$drop = array();
		$fields = array();

		$q = sprintf("show table status like '%s'", $table);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			/* show indexes from table */
			$table = $row["Name"];
			$q = sprintf("show index from `%s`", $table);
			$res2 = sql_query($q);
			while ($row2 = sql_fetch_assoc($res2)) {
				if ($row2["Key_name"] != "PRIMARY") {
					/* save fields to index */
					$fields[] = sprintf("%s.%s", $table, $row2["Column_name"]);

					/* add index names to drop */
					$drop[$table][$row2["Key_name"]] = 1;
				}
			}
		}
		/* read last index state from disk */
		/*
		## debug, read current indexes to disk
		$file = sprintf("%sfields_%s.txt",
			$GLOBALS["covide"]->temppath,
			$GLOBALS["covide"]->license["code"]);


		if (file_exists($file))
			$fd = explode("\n", file_get_contents($file));
		else
			$fd = array();

		// save field list to disk for later use
		foreach ($fd as $v) {
			$fields[] = $v;
		}
		foreach ($fields as $k=>$v) {
			if (!trim($v))
				unset($fields[$k]);
			else
				$fields[$k] = trim($v);
		}
		$fields = array_unique($fields);
		$fields = implode("\n", $fields)."\n";

		file_put_contents($file, $fields);
		*/
		foreach ($drop as $table=>$keys) {
			foreach ($keys as $k=>$v) {
				#echo sprintf("- drop index from table [%s] index [%s]\n",
				#	$table, $k);
				$q = sprintf("alter table `%s` drop index `%s`",
					$table, $k);
				sql_query($q);
			}
		}
	}
	private function createindex($table) {
		/* get current table descriptions */
		$tables = array();

		$q = sprintf("show table status like '%s'", $table);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$dbtable = $row["Name"];
			$engine = $row["Engine"];


			$q = sprintf("describe `%s`", $table);
			$res2 = sql_query($q);
			while ($row2 = sql_fetch_assoc($res2)) {
				$tables["fields"][$dbtable][$row2["Field"]] = $row2["Type"];
				$tables["engine"][$dbtable] = strtolower($engine);
			}
		}

		/* read last index state from disk */
		$file = sprintf("%s../sql/indexes.txt",
			$GLOBALS["covide"]->temppath);

		if (file_exists($file))
			$index = explode("\n", file_get_contents($file));
		else
			$index = array();

		foreach ($index as $i) {
			$i = explode(".", $i);
			if ($i[0] == $table) {
				$table = $i[0];
				$field = $i[1];

				/* check field type */
				$type   = $tables["fields"][$table][$field];
				$engine = $tables["engine"][$table];
				$keyname = sprintf("cvd_%s_%s", $table, $field);

				/* check table engine */
				switch ($engine) {
					case "myisam":
						$maxlength = 255;
						break;
					case "innodb":
						$maxlength = 1024;
						break;
					default:
						echo sprintf("Engine type [%s] on table [%s] is not supported!\n",
							$engine, $table);
						exit();
				}
				if (preg_match("/text/si", $type)) {
					$q = sprintf("create index `%s` ON `%s` (%s(%d))",
						$keyname, $table, $field, $maxlength);
					sql_query($q);
				} else {
					$q = sprintf("create index `%s` ON `%s` (%s)",
						$keyname, $table, $field);
					sql_query($q);
				}
			}
		}
	}
	private function optimize($table) {
		$q = sprintf("show table status like '%s'", $table);
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			/* show indexes from table */
			$table = $row["Name"];
			$overhead = $row["Data_free"];

			if ($overhead > 0) {
				$q = sprintf("optimize table `%s`", $table);
				sql_query($q);
			}
		}
	}
	private function convertdb($target) {
		if (in_array($target, array("innodb", "myisam"))) {
			echo sprintf("Converting storage engine to type %s ...\n", $target);
			$q = "show table status";
			$res = sql_query($q);
			while ($row = sql_fetch_assoc($res)) {
				/* convert to target */

				$table = $row["Name"];
				$engine = strtolower($row["Engine"]);

				if ($engine != $target) {
					/* drop index */
					$this->dropindex($table);

					/* alter storage engine */
					$q = sprintf("alter table `%s` engine = %s", $table, $target);
					sql_query($q);

					/* create index */
					$this->createindex($table);

					/* optimize */
					$this->optimize($table);

					/* sleep 1 second */
					sleep(1);
				}
			}
		}
	}
	private function convertDatabase($target) {
		if (in_array($target, array("innodb", "myisam")))
			$this->convertdb($target);
		else
			$this->reindexDatabase();

		echo "done!\n";
	}
	private function reindexDatabase() {
		echo "Reindexing database tables ...\n";
		$q = "show tables";
		$res = sql_query($q);
		while ($row = sql_fetch_assoc($res)) {
			$table = current($row);

			/* drop index */
			$this->dropindex($table);

			/* re-create index */
			$this->createindex($table);

			/* optimize table */
			$this->optimize($table);
		}
	}
}
?>
