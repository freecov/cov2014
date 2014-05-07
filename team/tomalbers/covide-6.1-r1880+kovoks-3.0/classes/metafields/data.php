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
		if (!is_array($date)) $date = array();
		foreach ($date as $k=>$dateparts) {
			$val[$k] = mktime(0,0,0,$dateparts["month"],$dateparts["day"],$dateparts["year"]);
		}
		foreach ($val as $id=>$value) {
			$sql = sprintf("UPDATE meta_table SET value='%s' WHERE id = %d", $value, $id);
			$res = sql_query($sql);
		}
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
		$sql = sprintf("SELECT * FROM meta_table WHERE tablename='%s' AND record_id = %d ORDER BY fieldorder", $tablename, $record_id);
		$res = sql_query($sql);
		$fieldlist = Array();
		while ($row = sql_fetch_assoc($res)) {
			$fieldlist[] = $row;
		}
		return $fieldlist;
	}
	/* }}} */
}
?>
