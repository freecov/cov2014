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
Class Support_output {
	/* constants */
	const include_dir = "classes/support/inc/";
	/* variables */
	/* methods */
	
	/* show_list_external {{{ */
	/**
	 * show list of issues filed by customers on the website
	 * 
	 * You can put a form on your webpage that injects issues
	 * into the Covide database. This function will allow you to
	 * choose what to do with it
	 */
	public function show_list_external() {
		require(self::include_dir."showListExternal.php");
	}
	/* }}} */
	
    /* 	show_list {{{ */
    /**
     * 	Show list of issues filed in the internal database
     *
     */
	public function show_list() {
		require(self::include_dir."showList.php");
	}
	/* }}} */

    /* 	show_issue {{{ */
    /**
     * 	Show a specific issue
     *
     */
	public function show_issue() {
		require(self::include_dir."showIssue.php");
	}
	/* }}} */

    /* 	show_edit {{{ */
    /**
     * 	show_edit. Show screen to create/alter issue
     *
     */
	public function show_edit() {
		require(self::include_dir."showEdit.php");
	}
  /* }}} */
	/* showSupportForm {{{ */
	/**
	 * showSupportForm. Show the external support form
	 */
	public function showSupportForm($options) {
		require(self::include_dir."showSupportForm.php");
	}
	
}
?>
