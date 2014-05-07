<?php
/**
 * Covide Groupware-CRM Core PostGreSQL database module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Covide_postgresql Extends Covide_db {

	/* constants */
	const include_dir = "classes/covide/inc/";
	const class_name  = "Covide_postgresql";

	/* variables */
	public $_table = array();
	public $_db;

	/* methods */
	public function __construct($db) {
		$this->_db =& $db;
		require(self::include_dir."define_schema.php");
	}

	private function _convertType($data) {
		$q = " ";
		$q.= sprintf("\"%s\"", $data["column"]);
		switch ($data["type"]) {
			case "date":
				$q.= " timestamp without time zone";
				break;
			case "smallint":
				$int=1;
				$q.= " smallint";
				break;
			case "int":
				$int=1;
				if ($data["is_primary"]) {
					$q.= " serial";
				} else {
					$q.= " integer";
				}
				break;
			case "varchar":
				$q.= " character varying";
				if ($data["length"]) {
					$q.= sprintf("(%d)", $data["length"]);
				} else {
					$q.= "(255)";
				}
				break;
			case "text":
				$q.= " text";
				break;
			default:
				die("unknown field type");
				break;
		}
		if ($data["not_null"]) {
			$q.= " NOT NULL";
		}
		if ($data["default"]) {
			$q.= " DEFAULT ";
			if ($int) {
				$q.= (int)$data["default"];
			} else {
				$q.= "'".$data["default"]."'";
			}
		}
		$q = trim($q);
		return $q;
	}

	private function _createTable($name) {
		$table = $this->_table[$name];
		$fields = $table["fields"];
		$q = sprintf("CREATE TABLE \"%s\" (", $name);
		foreach ($fields as $v) {
			$flag++;
			if ($flag > 1) {
				$q.= ", ";
			}
			$q.= sprintf("%s", $this->_convertType($v));
		}
		$q.= ");";

		$this->_db->query($q);
		echo "table [$name] created\n";
	}

	public function check_database() {

		##debug##
		return true;

		echo "<PRE>";

		/*check postgresql version */
		$q = "show server_version;";
		$res = $this->_db->query($q);
		$row = $res->fetchRow();
		$version = explode(".", $row["server_version"]);
		if ($version[0] < 7) {
			die("db version is not supported (>=7.4.0)");
		} elseif ($version[0] == 7 && $version[1] < 4) {
			die("db version is not supported (>=7.4.0)");
		}

		/* check if the versions and table exists */
		$info = $this->_db->tableInfo("table_versions");
		if (PEAR::isError($info)) {
			$this->_createTable("table_versions");
		} else {
			$info = $this->_decodeMetaArray($info);
			echo "table [table_versions] already exists\n";
		}

		/* for each table in definition array */
		foreach ($this->_table as $table=>$data) {
			echo $table."\n";
			print_r($data);
		}
	}
}
?>
