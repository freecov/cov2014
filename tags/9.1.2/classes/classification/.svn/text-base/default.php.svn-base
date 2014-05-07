<?php
/**
 * Covide Groupware-CRM Classification module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Classification {

	/* variables */

	/* methods */

	/* __construct {{{ */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			case "cla_edit" :
			case "edit" :
				$classification_output = new Classification_output();
				$classification_output->show_edit($_REQUEST["id"], $_REQUEST["addresstype"]);
				break;
			case "cla_save" :
			case "save" :
				$classification_data = new Classification_data();
				$classification_data->store2db($_REQUEST["cla"]);
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						if (parent && parent.document.getElementById('addclamulti')) {
							parent.location.href = parent.location.href;
							var t = setTimeout('closepopup();', 100);
						}
						if (parent && parent.document.getElementById('claform')) {
							parent.document.getElementById('claform').submit();
							var t = setTimeout('closepopup();', 100);
						}
					");
				$output->end_javascript();
				$output->exit_buffer();
				break;
			case "remove"  :
			case "delete"  :
				$classification_data = new Classification_data();
				$classification_data->removecla($_REQUEST["id"]);
				break;
			case "pick_cla" :
				$classification_output = new Classification_output();
				$classification_output->pick_cla();
				break;
			case "select_classification":
				$classification_output = new Classification_output();
				$classification_output->select_classification();
				break;
			case "showgroups" :
				$classification_output = new Classification_output();
				$classification_output->show_groups();
				break;
			case "cla_group_edit" :
				$classification_output = new Classification_output();
				$classification_output->cla_group_edit($_REQUEST["id"]);
				break;
			case "cla_group_save" :
				$classification_data = new Classification_data();
				$classification_data->saveclagroup($_REQUEST["cla"]);
				$output = new Layout_output();
				$output->start_javascript();
					$output->addCode("
						if (parent && parent.document.getElementById('claform')) {
							parent.document.getElementById('claform').submit();
							var t = setTimeout('closepopup();', 100);
						}
					");
				$output->end_javascript();
				$output->exit_buffer();
				break;
			case "cla_group_remove"  :
				$classification_data = new Classification_data();
				$classification_data->removeclagroup($_REQUEST["id"]);
				break;
			default :
				$classification_output = new Classification_output();
				$classification_output->show_classifications();
				break;
		}
	}
	/* }}} */
}
?>
