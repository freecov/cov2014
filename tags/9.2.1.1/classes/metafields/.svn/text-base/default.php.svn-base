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
Class Metafields {
	/* constants */
	
	/* variables */

	/* methods */
	
	/* __construct {{{ */
	/**
	 * Find out what part of this class to use based on request vars
	 */
	public function __construct() {
		/* check if user is logged in */
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		/* find out what to do */
		switch ($_REQUEST["action"]) {
			case "xml_remove" :
				$meta_data = new Metafields_data();
				$meta_data->meta_remove_field($_REQUEST["table"], $_REQUEST["id"]);
				break;
			case "add_meta" :
				$meta_output = new Metafields_output();
				$meta_output->meta_add_field($_REQUEST["tablename"], $_REQUEST["record_id"]);
				break;
			case "edit_meta" :
				$meta_output = new Metafields_output();
				$meta_output->meta_edit_field($_REQUEST["tablename"],  $_REQUEST["meta_id"]);
				break;
			case "save_add_field" :
				$meta_data = new Metafields_data();
				$meta_data->meta_save_new($_REQUEST["meta"]);
				/* refresh parent */
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("parent.location.href=parent.location.href;");
					$output->addCode("closewindow();");
				$output->end_javascript();
				$output->exit_buffer();
				break;
			case "save_edit_field" :
				$meta_data = new Metafields_data();
				$meta_data->meta_save_edit($_REQUEST["meta"]);
				/* refresh parent */
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("parent.location.href=parent.location.href;");
					$output->addCode("closewindow();");
				$output->end_javascript();
				$output->exit_buffer();
				break;
			default :
				break;
			/* end switch */
		}
	}
	/* }}} */
}
