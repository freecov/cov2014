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

		$data = Array();
		$query = "SELECT * FROM snack_order ORDER BY id";
		$result = sql_query($query);
		while ($row = sql_fetch_assoc($result)) {
			$dataItem = Array();
			$dataItem["ammount"] = $row["ammount"];
			$dataItem["name"] = $this->available_snacks[$row["id"]];
			array_push($data, $dataItem);
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
			$query = "SELECT ammount FROM snack_order WHERE id = ".$key;
			$result = sql_query($query);
			$count = sql_result($result, 0);

			if($count > 0) {
				$result = sql_query("UPDATE snack_order SET ammount=".($count+1)." WHERE id=".$key);
			} else {
				$result = sql_query("INSERT INTO snack_order SET id=".$key.", ammount = 1");
			}
		}
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
}
?>
