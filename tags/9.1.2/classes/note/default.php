<?php
/**
 * Covide Groupware-CRM Notes module
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
Class Note {

	/* variables */

	/* methods */

    /* 	__construct {{{ */
    /**
     * 	__construct. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		switch ($_REQUEST["action"]) {
			case "reply" :
			case "reply_single" :
			case "forward" :
			case "edit" :
				$note_edit = new Note_output();
				$note_edit->edit_note();
				break;
			case "store" :
				$note_save = new Note_data();
				$store = $note_save->store2db($_REQUEST["note"]);
				if ($store) {
					unset($note_save);
					$note_output = new Note_output();
					$note_output->show_sent();
				}
				break;
			case "message" :
				$note = new Note_output();
				$note->show_note($_REQUEST["msg_id"], $_REQUEST["hidenav"], $_REQUEST["actions"]);
				break;
			case "sent" :
				$options["note_type"] = "sent";
				$note_output = new Note_output();
				$note_output->generate_list($options);
				break;
			case "old" :
				$options["note_type"] = "old";
				$note_output = new Note_output();
				$note_output->generate_list($options);
				break;
			case "show" :
				$options["note_type"] = "show";
				$note_output = new Note_output();
				$note_output->generate_list($options);
				break;
			case "flagdone" :
				$note_data = new Note_data();
				$note_data->flagdone($_REQUEST["id"]);
				break;
			case "print" :
				$note_output = new Note_output();
				$note_output->printnote();
				break;
			case "storeaddressid" :
				$note_data = new Note_data();
				$note_data->storeaddressid($_REQUEST["noteid"], $_REQUEST["address_id"]);
				break;
			case "storeprojectid" :
				$note_data = new Note_data();
				$note_data->storeprojectid($_REQUEST["noteid"], $_REQUEST["project_id"]);
				break;
			case "drafts" :
				$options["note_type"] = "drafts";
				$note_output = new Note_output();
				$note_output->generate_list($options);
				break;
			case "store_draft" :
				$note_save = new Note_data();
				$store = $note_save->store2db($_REQUEST["note"]);
				break;
			case "delete_draft" :
				$note_data = new Note_data();
				$delete = $note_data->delete_draft($_REQUEST["id"]);
				break;
			default :
				$note_output = new Note_output();
				$note_output->generate_list();
				break;
		}
	}
	/* }}} */
}
?>
