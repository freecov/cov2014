<?php
/**
 * Covide Groupware-CRM Snack module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Snack_data {

	/* variables */
	public $available_snacks = array();

	/* methods */
	
	/* __construct {{{ */
	public function __construct() {
		/* get available snacks */
		$queryItem = "SELECT * FROM snack_items";
		$resultItem = sql_query($queryItem);
		$snacks = array();
		while ($rowItem = sql_fetch_assoc($resultItem)) {
			$snacks[$rowItem["id"]] = $rowItem["name"];
		}
		$this->available_snacks = $snacks;

	}
	/* }}} */

	/* getSnacks {{{ */
	/**
	 * Generate an array with snack data
	 *
	 * @return Array snack as it is in the db
	 */
	public function getSnacks() {
		$query = "SELECT snack_id, COUNT(snack_id) AS amount, created FROM snack_order GROUP BY snack_id ORDER BY created DESC";
		$result = sql_query($query);
		while ($row = sql_fetch_assoc($result)) {
			$data[] = $row;
		}
		return $data;
	}
	/* }}} */

	/* getSnackItems {{{ */
	/**
	 * Generate an array with all snack data
	 *
	 * @return Array snack as it is in the db
	 */
	public function getSnackItems() {

		$data = Array();
		$query = "SELECT * FROM snack_items ORDER BY name";
		$result = sql_query($query);
		while ($row = sql_fetch_assoc($result)) {
			$dataItem = Array();
			$dataItem["id"] = $row["id"];
			$dataItem["name"] = $row["name"];
			array_push($data, $dataItem);
		}
		return $data;
	}
	/* }}} */

	/* save_snacks {{{ */
	/**
	 * save snacks to the db
	 *
	 * @return Array snack as it is into the db
	 */
	public function save_snacks($newSnackArr) {

		foreach($newSnackArr as $key => $value) {
			$sql = sprintf("INSERT INTO snack_order SET snack_id = %d, user_id = %d, created = %d",
						$key, $_SESSION["user_id"], time());
			$result = sql_query($sql);
		}
		return $result;
	}
	/* }}} */

	/* count_snacks {{{ */
	/**
	 * Counts all snacks in the db
	 *
	 * @param String for the FROM field in sql
	 * @return variable with itemcount
	 */
	public function count_snacks($fromField) {

		$fields = "*";
		$data = Array();
		$query = "SELECT * FROM $fromField";
		$result = sql_query($query);
		while ($row = sql_fetch_assoc($result)) {
			$dataItem = Array();
			$dataItem["id"] = $row["id"];
			$dataItem["name"] = $row["name"];
			array_push($data, $dataItem);
		}
		return $data;
	}
	/* }}} */
	/* save_new_item {{{ */
	/**
	 * Adds the new snack item in the db
	 *
	 * @param String for the item name
	 * @return variable with itemcount
	 */
	public function save_new_item($snackNewName) {
		$result = "INSERT INTO snack_items SET name='".$snackNewName."'";
		$res = sql_query($result);
	}
	/* }}} */
	/* update_item {{{ */
	/**
	 * Updates the new item in the db
	 *
	 * @param int for the item ide
	 * @return true
	 */
	public function update_item($itemArray) {
		$result = sql_query("UPDATE snack_items SET name='".($itemArray[1])."' WHERE id=".$itemArray[0]);
	}
	/* }}} */
	/* getSnackById {{{ */
	/**
	 * Generate an array with all snack data
	 *
	 * @return Array snack as it is in the db
	 */
	public function getSnackById($id) {

		$fields = "*";
		$data = Array();
		$query = "SELECT * FROM snack_items WHERE id = '".$id."'";
		$result = sql_query($query);
		while ($row = sql_fetch_assoc($result)) {
			$dataItem = Array();
			$dataItem["id"] = $row["id"];
			$dataItem["name"] = $row["name"];
			array_push($data, $dataItem);
		}
		return $data;
	}
	/* }}} */
	
	/* getUsersBySnackId{{{ */
	/**
	 * Generate an array with user id's
	 *
	 * @return Array 
	 */
	public function getUsersBySnackId($snack_id) {
		$query = sprintf("SELECT user_id, COUNT(snack_id) AS amount FROM snack_order WHERE snack_id = %d GROUP BY user_id", $snack_id);
		$result = sql_query($query);
		while ($row = sql_fetch_assoc($result)) {
			$data[] = $row;
		}
		return $data;
	}
	/* }}} */
}
?>