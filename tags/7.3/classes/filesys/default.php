<?php
/**
 * Covide Groupware-CRM Filesys module
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
Class Filesys {

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
		/* do login check */
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		/* logged in so go on */
		/* sanity check. check for "My Documents" */
		$folderdata = array(
			"name"      => "mijn documenten",
			"user_id"   => $_SESSION["user_id"],
			"is_public" => 0,
			"sticky"    => 0,
			"parent_id" => 0
		);
		$filesys_data = new Filesys_data();
		$filesys_data->check_folder($folderdata);
		unset($folderdata);
		/* end check */

		switch ($_REQUEST["action"]) {
			case "view_folder" :
				$filesys_output = new Filesys_output();
				$filesys_output->view_folder($_REQUEST["folderid"]);
				break;
			case "opendir" :
				$filesys_output = new Filesys_output();
				$filesys_output->checkSSL($_REQUEST["id"]);
				$filesys_output->show_index($_REQUEST["id"]);
				break;
			case "fdownload" :
				$filesys_output = new Filesys_output();
				$filesys_output->checkSSL(0, $_REQUEST["id"]);

				$filesys_data = new Filesys_data();
				$filesys_data->file_download($_REQUEST["id"]);
				break;
			case "multi_download_zip":
				$filesys_data = new Filesys_data();
				$filesys_data->multi_download_zip($_REQUEST["ids"], $_REQUEST["id"]);
				break;
			case "fremove" :
				$filesys_data = new Filesys_data();
				$filesys_data->file_remove($_REQUEST["fileid"], $_REQUEST["folderid"]);
				break;
			case "fremove_multi" :
				$filesys_data = new Filesys_data();
				$filesys_data->file_remove_multi($_REQUEST["checkbox_file"], $_REQUEST["id"]);
				break;
			case "fedit" :
				$filesys_output = new Filesys_output();
				$filesys_output->file_edit($_REQUEST["fileid"]);
				break;
			case "feditsave" :
				$filesys_data = new Filesys_data();
				$filesys_data->file_edit_save($_REQUEST);
				break;
			case "fupload" :
				$filesys_data = new Filesys_data();
				$filesys_data->file_upload($_REQUEST);
				break;
			case "dircreate" :
				$filesys_data = new Filesys_data();
				$filesys_data->create_dir($_REQUEST);
				break;
			case "set_permissions" :
				$filesys_output = new Filesys_output();
				$filesys_output->set_permissions();
				break;
			case "delete_folder" :
				$filesys_output = new Filesys_output();
				$filesys_output->deleteFolderOverview();
				break;
			case "delete_folder_exec" :
				$filesys_data = new Filesys_data();
				$filesys_data->deleteFolderExec();
				break;
			case "cut_folder" :
				$filesys_output = new Filesys_output();
				$filesys_output->moveFolderOverview();
				break;
			case "paste_exec" :
				$filesys_data = new Filesys_data();
				$filesys_data->pasteExec();
				break;
			case "save_attachment" :
				$filesys_data = new Filesys_data();
				$filesys_data->save_attachment();
				$filesys_output = new Filesys_output();
				$filesys_output->filesCopied();
				break;
			case "save_fax" :
				$filesys_data = new Filesys_data();
				$filesys_data->save_fax();
				$filesys_output = new Filesys_output();
				$filesys_output->filesCopied("faxredir");
				break;
			case "search" :
				$filesys_output = new Filesys_output();
				$filesys_output->search();
				break;
			case "preview_file" :
				$filesys_data = new Filesys_data();
				$filesys_data->preview_file($_REQUEST["file"], $_REQUEST["module"], $_REQUEST["pdf"]);
				break;
			case "getPreviewFile" :
				$filesys_data = new Filesys_data();
				$filesys_data->file_preview_readfile($_REQUEST["file"]);
				break;
			case "preview_header" :
				$filesys_data = new Filesys_data();
				$filesys_data->file_preview_header();
				break;
			case "view_file" :
				$filesys_output = new Filesys_output();
				$filesys_output->checkSSL(0, $_REQUEST["id"]);

				$filesys_output = new Filesys_output();
				$filesys_output->view_file($_REQUEST["id"]);
				break;
			case "folderedit" :
				$filesys_output = new Filesys_output();
				$filesys_output->folderEdit($_REQUEST["folder"]);
				break;
			case "foldereditsave" :
				$filesys_data = new Filesys_data();
				$filesys_data->folderSave($_REQUEST["folder"]);
				break;
			case "find_double_rel":
				$filesys_output = new Filesys_output();
				$filesys_output->find_double_rel();
				break;
			default :
				$filesys_data = new Filesys_data();
				$filesys_data->getCmsFolder();

				$filesys_output = new Filesys_output();
				$filesys_output->show_index();
				break;
		}
	}
	/* }}} */
}
?>
