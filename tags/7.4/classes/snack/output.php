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
	Class Snack_output {
		/* constants */
		const include_dir = "classes/snack/inc/";
		const include_dir_main = "classes/html/inc/";

		//TODO: somwhere there is a bug with other values than 20
		public $pagesize = 20;
		/* methods */

		public function __construct() {
			$this->pagesize = $GLOBALS["covide"]->pagesize;
		}
		/* generate_list {{{ */
		/**
		 * generate_list. Show snack order
		 *
		 * Show snack orders.
		 *
		 *
		 * @return bool true
		 */
		public function generate_list() {
			require(self::include_dir."generate_list.php");
		}
		/* }}} */
		/* edit_snacks {{{ */
		/**
		 * edit_snacks. Add new snacks to the current order
		 *
		 *  Add new snacks to the current order
		 *
		 *
		 * @return bool true
		 */
		public function edit_snacks() {
			require(self::include_dir."edit_snacks.php");
		}
		/* }}} */
		/* save_snacks {{{ */
		/**
		 * save_snacks. Add new snacks to the current order
		 *
		 * Save new snacks into the db
		 *
		 *
		 * @return bool true
		 */
		public function save_snacks() {
			require(self::include_dir."save_snacks.php");
		}
		/* }}} */
		/* add_items {{{ */
		/**
		 * add_items. Adds new snacks into the list of snacks
		 *
		 * Save new snacks into the db
		 *
		 *
		 * @return bool true
		 */
		public function add_items() {
			require(self::include_dir."add_items.php");
		}
		/* }}} */
		/* item_list {{{ */
		/**
		 * item_list. Lists all the option items
		 *
		 * Lists all the optional items
		 *
		 *
		 * @return bool true
		 */
		public function item_list() {
			require(self::include_dir."item_list.php");
		}
		/* }}} */
	}
?>

