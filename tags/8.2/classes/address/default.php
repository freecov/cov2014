<?php
/**
 * Covide Groupware-CRM Addressbook module
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Address {

	/* variables */

	/**
	 * @var array Addressbook accesslist
	 */
	public $access = Array();

	/* methods */

	/* __construct {{{ */
	/**
	 * __construct. What in the addressbook do you want to do?
	 *
	 * Init the correct address class based on request vars.
	 * This is the Addressbook Controller.
	 */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}

		$this->get_access();
		switch ($_REQUEST["action"]) {
			case "gendebtornr" :
				$address_data = new Address_data();
				$address_data->gendebtornr($_REQUEST["cur"]);
				break;
			case "zoekcla":
				$address_output = new Address_output();
				$address_output->selectClassification();
				break;
			case "edit_bcard" :
				$address_output = new Address_output();
				$address_output->edit_bcard($_REQUEST["id"], $_REQUEST["address_id"], $_REQUEST["private_id"]);
				break;
			case "save_bcard" :
				$address_data = new Address_data();
				$address_data->save_bcard($_REQUEST["bcard"], $_REQUEST["metafield"]);
				break;
			case "cardrem" :
				$address_data = new Address_data();
				$address_data->remove_bcard($_REQUEST["cardid"]);
				if ($_REQUEST["closewin"] == 1) {
					$output = new Layout_output();
					$output->start_javascript();
						$output->addCode("opener.document.getElementById('deze').submit();");
						$output->addCode("window.close();");
					$output->end_javascript();
					$output->exit_buffer();
				}
				break;
			case "cardshow" :
			case "show_bcard" :
				$address_output = new Address_output();
				$address_output->edit_bcard($_REQUEST["id"], $_REQUEST["address_id"], $_REQUEST["private_id"], 1);
				break;
			case "edit" :
				$address_output = new Address_output();
				if (in_array($_REQUEST["addresstype"], array("private", "users")))
					$address_output->show_edit_private($_REQUEST["id"]);
				else
					$address_output->show_edit($_REQUEST["id"], $_REQUEST["addresstype"], $_REQUEST["sub"], $_REQUEST["private_id"]);
				break;
			case "save" :
				$address_data = new Address_data();
				$address_data->store2db($_REQUEST["address"], $_REQUEST["metafield"]);
				break;
			case "relcard" :
				$address_output = new Address_output();
				$address_output->relationCard($_REQUEST["id"]);
				break;
			case "relcardsearchform" :
				$address_output = new Address_output();
				$address_output->relcardsearchform();
				break;
			case "relcardsearch" :
				$address_output = new Address_output();
				$address_output->relcardsearch();
				break;
			case "usercard" :
				$address_output = new Address_output();
				$address_output->userCard($_REQUEST["id"]);
				break;
			case "delete"  :
				$address_data = new Address_data();
				$address_data->delete($_REQUEST["id"], $_REQUEST["addresstype"]);
				break;
			case "show_item" :
				$address_output = new Address_output();
				if ($_REQUEST["addresstype"] == "relations")
					$address_output->show_edit($_REQUEST["id"], $_REQUEST["addresstype"], $_REQUEST["sub"], $_REQUEST["private_id"], 1);
				else
					$address_output->show_item($_REQUEST["id"], $_REQUEST["addresstype"]);
				break;
			case "show_private" :
				$address_output = new Address_output();
				$address_output->show_edit_private($_REQUEST["id"], 1);
				break;
			case "togglesync" :
				$address_data = new Address_data();
				$address_data->toggleSync($_REQUEST["address_id"], $_REQUEST["identifier"], $_REQUEST["toggleaction"],
					($_REQUEST["funambol_user"]) ? $_REQUEST["funambol_user"]:$_SESSION["user_id"]);
				break;
			case "export" :
				$address_output = new Address_output();
				$address_output->export();
				break;
			case "print" :
				$address_output = new Address_output();
				$address_output->print_selection();
				break;
			case "import" :
				$address_output = new Address_output();
				$address_output->show_import_start();
				break;
			case "import_step_2" :
				$address_output = new Address_output();
				$address_output->show_import_step2();
				break;
			case "importVcard" :
				$address_output = new Address_output();
				$address_output->importVcard();
				break;
			case "importVcard_process" :
				$address_output = new Address_output();
				$address_output->importVcard_process();
				break;
			case "importVcard_save" :
				$address_data = new Address_data();
				$address_data->importVcard_save($_REQUEST);
				break;
			case "import_save" :
				$address_data = new Address_data();
				$address_data->import_save($_REQUEST);
				break;
			case "check_double" :
				$address_data = new Address_data();
				$address_data->check_double($_REQUEST);
				break;
			case "addcla_multi" :
				$address_output = new Address_output();
				$address_output->addcla_multi($_REQUEST["info"]);
				break;
			case "savecla_multi" :
				$address_data = new Address_data();
				$address_data->savecla_multi($_REQUEST);
				break;
			case "checkcla_xml" :
				$address_data = new Address_data();
				$address_data->checkcla_xml($_REQUEST);
				break;
			case "showrelimg" :
				$address_output = new Address_output();
				$address_output->showRelIMG($_REQUEST["photo"], $_REQUEST["addresstype"]);
				break;
			case "removerelimg" :
				$address_data = new Address_data();
				$address_data->removeRelIMG($_REQUEST["address_id"], $_REQUEST["addresstype"]);
				break;
			case "edithrm" :
				$address_output = new Address_output();
				$address_output->edit_hrm($_REQUEST["user_id"]);
				break;
			case "save_hrminfo" :
				$address_data = new Address_data();
				$address_data->save_hrminfo($_REQUEST["hrm"]);
				break;
			case "glob_metalist" :
				$address_output = new Address_output();
				$address_output->show_metafields();
				break;
			case "glob_metaedit" :
				$address_output = new Address_output();
				$address_output->show_metaedit($_REQUEST["id"]);
				break;
			case "glob_metasave" :
				$address_data = new Address_data();
				$address_data->save_metafield($_REQUEST);
				header("Location: index.php?mod=address&action=glob_metalist");
				break;
			case "bcardsxml" :
				$address_output = new Address_output();
				$address_output->getProjectBcardsByRelationXML($_REQUEST["address_id"], $_REQUEST["current"]);
				break;
			case "move2public":
				$address_output = new Address_output();
				$address_output->movePrivate2Public($_REQUEST["id"], $_REQUEST["subaction"]);
				break;
			case "getBcardsXML":
				$address_output = new Address_output();
				$address_output->getBcardsXML($_REQUEST["address_id"], $_REQUEST["search"], $_REQUEST["output"]);
				break;
			case "getDebtornrById":
				$address_output = new Address_output();
				$address_output->getDebtornrById($_REQUEST["id"]);
				break;
			case "showheaders":
				$address_output = new Address_output();
				$address_output->showheaders();
				break;
			case "editTitles":
				$address_output = new Address_output();
				$address_output->editTitles();
				break;
			case "saveTitles":
				$address_data = new Address_data();
				$address_data->saveTitles($_REQUEST);
				break;
			case "removeTitles":
				$address_data = new Address_data();
				$address_data->removeTitles($_REQUEST);
				break;
			case "removeSelection":
				$address_output = new Address_output();
				$address_output->removeSelection($_REQUEST["info"]);
				break;
			case "deleteSelection" :
				$address_output = new Address_output();
				$address_output->removeSelection($_REQUEST["info"], 1);
				break;
			case "deactivateSelection":
				$address_data = new Address_data();
				$address_data->deactivateSelection($_REQUEST);
				break;
			case "deleteSelectionExec" :
				$address_data = new Address_data();
				$address_data->deleteSelectionExec($_REQUEST);
				break;
			case "syncMultivers" :
				$address_data = new Address_data();
				$address_data->syncMultivers();
				break;
			case "showPrivate" :
				$address_output = new Address_output();
				$address_output->showPrivate($_REQUEST["private_id"]);
				break;
			case "exportVcard" :
				$address_output = new Address_output();
				$address_output->exportVcard();
				break;
			default :
				if ($GLOBALS["covide"]->license["has_funambol"]) {
					$funambol_data = new Funambol_data();
					$funambol_data->checkRecords("address");
					unset($funambol_data);
				}

				$address_output = new Address_output();
				$address_output->show_list();
				break;
		}
	}
	/* }}} */
	/* get_access {{{ */
	/**
	 * get_access. put addressbook access in array
	 *
	 * This one should be moved to a covide/user specific class.
	 * We dont want to be doing this over and over again for every module.
	 *
	 * @return Array Accesslist
	 */
	public function get_access() {
		$query      = "SELECT xs_relationmanage,xs_addressmanage,xs_usermanage,addressaccountmanage,xs_salesmanage FROM users WHERE id=".$_SESSION["user_id"];
		$result     = sql_query($query);
		$row = sql_fetch_assoc($result);
		$this->access["xs_adres"]   = $row["xs_addressmanage"];
		$this->access["xs_user"]    = $row["xs_usermanage"];
		$this->access["xs_relatie"] = $row["xs_relationmanage"];
		$this->access["xs_sales"]   = $row["xs_salesmanage"];
		$this->access["xs_adresaccmanage"] = explode(",", $row["addressaccountmanage"]);
		return $this->access;
	}
	/* }}} */
}
?>
