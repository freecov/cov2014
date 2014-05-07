<?php
/**
 * Covide Groupware-CRM Core database module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Covide_db {

	/* constants */
	const include_dir = "classes/covide/inc/";
		
	/* variables */
	public $_table = array();
	public $_db;

	/* methods */
	public function __construct($db) {
		$this->_db =& $db;
		require(self::include_dir."define_schema.php");
	}

	public function _decodeMetaArray($array) {
		foreach ($array as $k=>$field) {
			foreach ($field as $key=>$v) {
				$field[$key] = preg_replace("/%([A-Z0-9]{2})/se", "chr(hexdec('$1'))", $v);
			}
			$array[$k] = $field;
		}
		return $array;
	}

	private function _checkError($result) {
		if (is_array($result)) {
			return 0;
		} else {
			return 1;
		}
	}

	public function addField($table, $settings) {
		/*
			field name
			field type
			length
			allow null
			default value
			primary key
			indexed
			index condition
			foreign key (info only)
		*/

		$_settings["column"]          = $settings["column"];
		$_settings["type"]            = $settings["type"];
		$_settings["length"]          = (int)$settings["length"];
		$_settings["not_null"]        = (int)$settings["not_null"];
		$_settings["default"]         = $settings["default"];
		$_settings["is_primary"]      = (int)$settings["is_primary"];
		$_settings["is_indexed"]      = (int)$settings["is_indexed"];
		$_settings["index_condition"] = $settings["index_condition"];
		$_settings["foreign_key"]     = $settings["foreign_key"];

		$this->_table[$table]["fields"][] = $_settings;
		$this->_table[$table]["hash"]     = md5(serialize($_settings));
		$this->_table[$table]["updated"]  = date("d-m-Y H:i:s");
	}
}
?>
