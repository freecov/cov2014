<?php
/**
 * Covide Groupware-CRM support module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version 6.0
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Support_data {
	/* constants */
	const include_dir = "classes/support/inc/";

	/* variables */
	/* methods */

	/* getSupportItems {{{ */
	/**
	 * Get supportitems based on options
	 *
	 * @param array Options to controll what items to fetch
	 * @return array The items matching the options
	 */
	public function getSupportItems($options) {
		require(self::include_dir."dataGetSupportItems.php");
		return $supportItems;
	}
	/* }}} */
	/* getSupportItemById {{{ */
	/**
	 * Get a specific support item from db
	 *
	 * @param int The database id of the item to get
	 * @return array The support item
	 */
	public function getSupportItemById($id) {
		require(self::include_dir."dataGetSupportItemById.php");
		return $supportItem;
	}
	/* }}} */
	/* getExternalIssues {{{ */
	public function getExternalIssues() {
		require(self::include_dir."dataGetExternalIssues.php");
		return $issues;
	}
	/* }}} */
	/* remove_ext_item {{{ */
	public function remove_ext_item($id, $returnxml=0) {
		$sql = sprintf("DELETE FROM support WHERE id = %d", $id);
		$res = sql_query($sql);
		if ($returnxml) {
			echo "reload_doc();";
			exit;
		} else {
			header("Location: index.php?mod=support&action=list_external");
			exit;
		}
	}
	/* }}} */
    /* 	save2db  {{{ */
    /**
     * 	save2db . save issue to db
     */
	public function save2db () {
		require(self::include_dir."save2db.php");
	}
    /* }}} */
}
?>
