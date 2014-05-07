<?php
/**
 * Covide Groupware-CRM metafields module
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
Class Metafields_data {
	/* constants */

	/* variables */

	/* methods */

	/* meta_save_new {{{ */
	/**
	 * Save new metafield info to database
	 *
	 * @param Array The database fields with their values
	 * @return bool True on success, false on failure
	 */
	public function meta_save_new($meta) {
		$sql = sprintf("INSERT INTO meta_table (fieldname, fieldtype, tablename, record_id, fieldorder) VALUES ('%s', %d, '%s', %d, %d)",
			$meta["fieldname"], $meta["fieldtype"], $meta["tablename"],
			$meta["record_id"], $meta["fieldorder"]
		);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */

	/* meta_save_edit {{{ */
	/**
	 * Save new metafield info to database
	 *
	 * @param Array The database fields with their values
	 * @return bool True on success, false on failure
	 */
	public function meta_save_edit($meta) {
		$sql = sprintf("UPDATE `meta_table` SET `fieldname` = '%s',`fieldtype` = %d,`fieldorder` = %d WHERE `meta_table`.`id` = %d", $meta["fieldname"], $meta["fieldtype"], $meta["fieldorder"], $meta["id"]);
		$res = sql_query($sql);

		return true;
	}
	/* }}} */

	/* meta_remove_field {{{ */
	/**
	 * Remove metafield info from database
	 *
	 * @param string The tablename link identifier
	 * @param Array The database id to remove
	 * @return bool True on success, false on failure
	 */
	public function meta_remove_field($tablename, $id) {
		$sql = sprintf("DELETE FROM meta_table WHERE tablename = '%s' AND id = %d", $tablename, $id);
		$res = sql_query($sql);
		$sql = sprintf("DELETE FROM meta_global WHERE meta_id = %d ", $id);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */

	/* meta_save_field {{{ */
	/**
	 * Save updated field to the database
	 *
	 * @param array The form/field information
	 * @return bool True on success, false on failure
	 */
	public function meta_save_field($fields_data) {
		$datum = Array();
		$val = Array();
		$is_global = 0;
		$global_id = 0;

		foreach ($fields_data as $k=>$field_data) {
			if (is_int($k)) {
				$val[$k] = $field_data;
			} else {
				$parts = explode("_", $k);
				$id = $parts[0];
				$datepart = $parts[1];
				$date[$id][$datepart] = $field_data;
			}
		}
		if (!is_array($date)) {
			$date = array();
		}
		foreach ($date as $k=>$dateparts) {
			if($dateparts["month"]!="--" && $dateparts["day"]!="--" && $dateparts["year"]!="--") {
				$val[$k] = mktime(0,0,0,$dateparts["month"],$dateparts["day"],$dateparts["year"]);
			} else {
				$val[$k] = NULL;
			}
		}
		if ($_REQUEST["address"]["id"]) {
			$checkGlobal = $this->meta_list_fields(0, $_REQUEST["address"]["id"]);
		} else {
			$checkGlobal = $this->meta_list_fields(0, 0);
		}

		foreach ($val as $id=>$value) {
			$is_global = 0;
			$global_id = 0;
			foreach ($checkGlobal as $index=>$values) {
				if ($values["id"] == $id || $values["value"]["meta_id"] == $id) {
					$is_global = 1;
					if (is_array($values) && $values["value"]["relation_id"] == $_REQUEST["address"]["id"])
						$global_id = $values["value"]["id"];
				}
			}
			if ($is_global && $global_id) {
				/* update global field information */
				$sql = sprintf("UPDATE meta_global set value = '%s' WHERE meta_id = %d AND relation_id = %d AND id = %d",
					$value, $id, $_REQUEST["address"]["id"], $global_id);
			} elseif ($is_global) {
				/* global field but nothing in the database so insert */
				$sql = sprintf("INSERT INTO meta_global (meta_id, relation_id, value) VALUES (%d, '%s', '%s')",
					$id, $_REQUEST["address"]["id"], $value);
			} else {
				/* normal metafield */
				$sql = sprintf("UPDATE meta_table SET value='%s' WHERE id = %d", $value, $id);
			}
			$res = sql_query($sql);
		}
		return true;
	}
	/* }}} */

	/* meta_list_fields {{{ */
	/**
	 * Get specified meta fields from db
	 *
	 * @param string The table link identifier
	 * @param id The record id in the linked table
	 * @return array The metafield information
	 */
	public function meta_list_fields($tablename, $record_id) {

		$sql = sprintf("SELECT * FROM meta_table WHERE tablename='%s' AND record_id = %d OR record_id = 0 ORDER BY fieldorder", $tablename, $record_id);
		$res = sql_query($sql);
		$fieldlist = Array();
		while ($row = sql_fetch_assoc($res)) {

			$fieldlist[] = $row;

			if ($row["record_id"] == 0) {
				$newCount = count($fieldlist) - 1;
				/* due to a bug this value can already be set now. reset to nothing before fetching relation specific value */
				$fieldlist[$newCount]["value"] = "";
				/* now fetch the relation specific value */
				if ($record_id) {
					$sql2 = sprintf("SELECT * FROM meta_global WHERE meta_id = %d AND relation_id = %d ORDER BY id", $row["id"], $record_id);
				} else {
					$sql2 = sprintf("SELECT * FROM meta_global WHERE meta_id = '%d' ORDER BY id", $row["id"]);
				}
				$res2 = sql_query($sql2);
				$fieldlist2 = Array();
				while ($row2 = sql_fetch_assoc($res2)) {
					if (!$record_id || $row2["relation_id"] == $record_id) {
						$fieldlist[$newCount]["value"] = $row2;
						$newCount++;
					}
				}
			}
		}
		return $fieldlist;
	}
	/* }}} */
}
?>
