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
 * @copyright Copyright 2000-2009 Covide BV
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
	 * @param Array $meta The database fields with their values
	 * @return bool True on success
	 */
	public function meta_save_new($meta) {
		$sql = sprintf("INSERT INTO meta_table (fieldname, fieldtype, tablename, record_id, fieldorder, group_id) VALUES ('%s', %d, '%s', %d, %d, %d)",
			$meta["fieldname"], $meta["fieldtype"], $meta["tablename"],
			$meta["record_id"], $meta["fieldorder"], $meta["group"]
		);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* meta_save_edit {{{ */
	/**
	 * Save new metafield info to database
	 *
	 * @param Array $meta The database fields with their values
	 * @return bool True on success
	 */
	public function meta_save_edit($meta) {
		$sql = sprintf("UPDATE meta_table SET fieldname = '%s', fieldtype = %d, fieldorder = %d, group_id = %d WHERE id = %d", $meta["fieldname"], $meta["fieldtype"], $meta["fieldorder"], $meta["group"], $meta["id"]);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* meta_remove_field {{{ */
	/**
	 * Remove metafield info from database
	 *
	 * @param string $tablename The tablename link identifier
	 * @param int $id The database id to remove
	 * @return bool True on success
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
	 * @param array $fields_data The form/field information
	 * @return bool True on success, false if no relation_id is set
	 */
	public function meta_save_field($fields_data) {
		$relation_id = $fields_data["relation_id"];
		$date = Array();
		$val = Array();
		if (!$relation_id) {
			return false;
		}

		foreach ($fields_data as $type => $fields) {
			if ($type == "global" || $type == "normal") {
				foreach ($fields as $k => $field_data) {
					if (is_int($k)) {
						$val[$type][$k] = $field_data;
					} else {
						$parts = explode("_", $k);
						$id = $parts[0];
						$datepart = $parts[1];
						$date[$type][$id][$datepart] = $field_data;
					}
				}
			}
		}
		foreach ($date as $type => $dateparts_arr) {
			foreach ($dateparts_arr as $k => $dateparts) {
				if ($dateparts["month"] != "--" && $dateparts["day"] != "--" && $dateparts["year"] != "--") {
					$val[$type][$k] = mktime(0,0,0,$dateparts["month"],$dateparts["day"],$dateparts["year"]);
				} else {
					$val[$type][$k] = NULL;
				}
			}
		}
		foreach ($val as $type => $vals) {
			foreach ($vals as $id => $value) {
				if ($type == "global") {
					//check if we already have this in the db
					$sql_count = sprintf("SELECT COUNT(*) FROM meta_global WHERE meta_id = %d AND relation_id = %d", $id, $relation_id);
					$res_count = sql_query($sql_count);
					if (sql_result($res_count, 0) == 1) {
						/* update global field information */
						$sql = sprintf("UPDATE meta_global set value = '%s' WHERE meta_id = %d AND relation_id = %d",
							$value, $id, $relation_id);
					} else {
						/* global field but nothing in the database so insert */
						$sql = sprintf("INSERT INTO meta_global (meta_id, relation_id, value) VALUES (%d, '%s', '%s')",
							$id, $relation_id, $value);
					}
				} else {
					/* normal metafield */
					$sql = sprintf("UPDATE meta_table SET value='%s' WHERE id = %d", $value, $id);
				}
				$res = sql_query($sql);
			}
		}
		return true;
	}
	/* }}} */
	/* meta_list_fields {{{ */
	/**
	 * Get specified meta fields from db
	 *
	 * @param string $tablename The table link identifier
	 * @param int $record_id The record id in the linked table
	 * @return array The metafield information
	 */
	public function meta_list_fields($tablename, $record_id) {
		$sql = sprintf("SELECT * FROM meta_table WHERE tablename='%s' AND record_id = %d OR record_id = 0 ORDER BY fieldorder", $tablename, $record_id);
		$res = sql_query($sql);
		$fieldlist = Array();
		while ($row = sql_fetch_assoc($res)) {
			$fieldlist[$row["id"]] = $row;
			if ($row["record_id"] == 0) {
				// this is a global field, fetch the value from meta_global
				$sql_global = sprintf("SELECT * FROM meta_global WHERE meta_id = %d AND relation_id = %d", $row["id"], $record_id);
				$res_global = sql_query($sql_global);
				$fieldlist[$row["id"]]["global"] = sql_fetch_assoc($res_global);
			}
		}
		return $fieldlist;
	}
	/* }}} */
	/* metagroup_save_new {{{ */
	/**
	 * Save new meta group info to database
	 *
	 * @param Array $metagroup The database fields with their values
	 * @return bool True on success, false on failure
	 */
	public function metagroup_save_new($metagroup) {
		$sql = sprintf("INSERT INTO meta_groups (name, description) VALUES ('%s', '%s')",
			$metagroup["name"], $metagroup["description"]
		);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* metagroup_save_edit {{{ */
	/**
	 * Edit meta group info to database
	 *
	 * @param Array $metagroup The database fields with their values
	 * @return bool True on success, false on failure
	 */
	public function metagroup_save_edit($metagroup) {
		$sql = sprintf("UPDATE `meta_groups` SET `name` = '%s',`description` = '%s' WHERE `id` = %d",
			$metagroup["name"], $metagroup["description"], $metagroup["id"]);

		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* metagroup_remove {{{ */
	/**
	 * Edit meta group info to database
	 *
	 * @param int $id Metagroup id to remove
	 * @return bool True on success, false on failure
	 */
	public function metagroup_remove($id) {
		$address_data = new Address_data();
		$metagroups = $address_data->get_metagroups($id);

		$sql = sprintf("DELETE FROM meta_table WHERE group_id = %s", $id);
		$res = sql_query($sql);

		$sql = sprintf("DELETE FROM meta_groups WHERE id = %s", $id);
		$res = sql_query($sql);

		$sql = sprintf("DELETE FROM meta_global WHERE meta_id = %d ", $metagroups[1]['id']);
		$res = sql_query($sql);
		return true;
	}
	/* }}} */
	/* migrateRelationsToBCs {{{ */
	/**
	 * Migrate all metafields from relation specific to businesscard specific
	 *
	 * @return void
	 */
	public function migrateRelationsToBCs() {
		/* convert meta_table items with tablename 'address' and a record_id to the tablename 'bcards' and the corresponding RCBC id */
		$sql = "SELECT * FROM meta_table WHERE tablename = 'adres'";
		$res = sql_query($sql);
		while ($row = sql_fetch_assoc($res)) {
			if ($row["record_id"]) {
				$q = sprintf("SELECT id FROM address_businesscards WHERE rcbc = 1 AND address_id = %d", $row["record_id"]);
				$r = sql_query($q);
				$update_query = sprintf("UPDATE meta_table SET tablename = 'bcards', record_id = %d WHERE id = %d", sql_result($r, 0), $row["id"]);
				$update_res = sql_query($update_query);
			} else {
				//this is a global field, so only flip the tablename field
				$update_query = sprintf("UPDATE meta_table SET tablename = 'bcards' WHERE id = %d", $row["id"]);
				$update_res = sql_query($update_query);
				//now convert the children
				$sql_global = sprintf("SELECT * FROM meta_global WHERE meta_id = %d", $row["id"]);
				$res_global = sql_query($sql_global);
				while ($row_global = sql_fetch_assoc($res_global)) {
					$q = sprintf("SELECT id FROM address_businesscards WHERE rcbc = 1 AND address_id = %d", $row_global["relation_id"]);
					$r = sql_query($q);
					$update_query = sprintf("UPDATE meta_global SET relation_id = %d WHERE id = %d", sql_result($r, 0), $row_global["id"]);
					$update_res = sql_query($update_query);
				}
			}
		}
		/* done converting */
		$sql = "UPDATE license SET address_migrated = 3";
		$res = sql_query($sql);
		$GLOBALS["covide"]->license["address_migrated"] = 3;
	}
	/* }}} */
}
?>
