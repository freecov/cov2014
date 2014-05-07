<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Email {

	/* constants */
	const include_dir = "classes/email/inc/";
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

		if (!$_SESSION["user_id"] && $_REQUEST["action"]!="retrieve") {
			$GLOBALS["covide"]->trigger_login();
		}

		switch ($_REQUEST["action"]) {

			case "permissionsDelete" :
				$email_data = new Email_data();
				$email_data->deletePermissions($_REQUEST["id"], $_REQUEST["user_id"]);
				break;
			case "permissionsEdit" :
				$email_output = new Email_output();
				$email_output->permissionsEdit($_REQUEST["id"], $_REQUEST["user_id"]);
				break;
			case "show_permissions" :
				$email_output = new Email_output();
				$email_output->permissionsList();
				break;
			case "permissionsSave" :
				$email_data = new Email_data();
				$email_data->savePermissions($_REQUEST["id"], $_REQUEST["folder_id"], $_REQUEST["users"], $_REQUEST["user_id"], $_REQUEST["name"]);
				break;
			case "multi_download_zip":
				$email_data = new Email_data();
				$email_data->multi_download_zip();
				break;
			case "create_folder":
				$email_data = new Email_data();
				$email_data->createFolder();
				$email = new Email_output();
				$email->emailList();
				break;
			case "edit_folder":
				$email_data = new Email_data();
				$email_data->editFolder();
				$email = new Email_output();
				$email->emailList();
				break;
			case "move_folder":
				$email = new Email_output();
				$email->folderMove();
				break;
			case "move_folder_exec":
				$emailData = new Email_data();
				$emailData->folderMoveExec();
				$email = new Email_output();
				$email->emailList();
				break;
			case "delete_folder":
				$emailData = new Email_data();
				$folder = $emailData->folderDelete($_REQUEST["folder_id"]);
				$email = new Email_output();
				$email->emailList($folder);
				break;
			case "add_attachment_covide":
				$email = new Email_data();
				$email->addAttachmentCovide();
				break;
			case "view_attachment":
				$email = new Email_output();
				$email->viewAttachment($_REQUEST["id"]);
				break;
			case "download_attachment":
				$email = new Email_data();
				$email->downloadAttachment($_REQUEST["id"]);
				break;
			case "delete_attachments_xml":
				$emailData = new Email_data();
				$emailData->mail_delete_attachment($_REQUEST["id"]);
				$email = new Email_output();
				$email->emailAttachmentListXML();
				break;
			case "delete_attachments":
				$emailData = new Email_data();
				$emailData->mail_delete_attachment($_REQUEST["attachment_id"]);
				$email = new Email_output();
				$email->emailOpen();
				break;
			case "retrieve":
				$email = new Email_retrieve();
				$email->retrieve();
				break;
			case "show_info":
				$email = new Email_output();
				$email->showInfo();
				break;
			case "select_relation":
				$email = new Email_output();
				$email->selectRelation();
				break;
			case "selection_move":
				$email = new Email_output();
				$email->selectionMove();
				break;
			case "multiple_move":
				$emailData = new Email_data();
				$emailData->multipleMove();
				$email = new Email_output();
				$email->emailList();
				break;
			case "toggle_state":
				$emailData = new Email_data();
				$emailData->toggleState();
				$email = new Email_output();
				$email->emailList();
				break;
			case "link_rel_sel":
				$emailData = new Email_data();
				$emailData->linkRelSelection();
				$email = new Email_output();
				$email->emailList();
				break;
			case "autocomplete":
				$emailData = new Email_data();
				$emailData->autocomplete();
				break;
			case "migrate":
				$email = new Email_migration();
				$email->mailMigration();
				break;
			case "viewhtml":
				$email = new Email_output();
				$email->viewHtml($_REQUEST["no_filter"]);
				break;
			case "viewtext":
				$email = new Email_output();
				$email->viewHtml(0, 1);
				break;
			case "compose":
				/* compose a new email */
				/* auto insert an empty email into concepts */
				if ($GLOBALS["covide"]->license["disable_basics"]) {
					echo gettext("You have no access to this module");
					exit();
				}
				$emailData = new Email_data();
				$id = $_REQUEST["id"];
				if (!$id) {
					$id = $emailData->save_concept();
				}
				$email = new Email_output();
				$email->emailCompose($id);
				break;
			case "user_move":
				$email = new Email_data();
				$email->userMove();
				break;
			case "user_copy":
				$email = new Email_data();
				$email->userCopy();
				break;
			case "save_concept":
				$emailData = new Email_data();
				$id = $_REQUEST["id"];
				$emailData->save_concept($id);
				break;
			case "upload_files":
				$emailData = new Email_data();
				$emailData->upload_files();
				break;
			case "upload_list":
				$emailData = new Email_data();
				$emailData->upload_list();
				break;
			case "delete_xml":
				$emailData = new Email_data();
				$emailData->mail_delete_xml();
				break;
			case "delete_multi":
				$emailData = new Email_data();
				$emailData->mail_delete_multi();
				$email = new Email_output();
				$email->emailList();
				break;
			case "delete_multi_attachments":
				$emailData = new Email_data();
				foreach ($_REQUEST["checkbox_attachment"] as $k=>$v) {
					$emailData->mail_delete_attachment($k);
				}
				$email = new Email_output();
				$email->emailList();
				break;

			case "open":
				if ($_REQUEST["new_relation"]) {
					$email = new Email_data();
					$email->change_relation();
				}
				$email = new Email_output();
				$email->emailOpen();
				break;
			case "print":
				$email = new Email_output();
				$email->emailPrint();
				break;
			case "from_list":
				$email = new Email_output();
				$email->emailGetFromList();
				break;
			case "upload_view":
				$email = new Email_output();
				$email->emailUploadView();
				break;
			case "toggle_private_state_xml":
				$email = new Email_data();
				$email->toggle_private_state_xml();
				break;
			case "change_folder_xml":
				$email = new Email_data();
				$email->change_folder_xml();
				break;
			case "change_relation_xml";
				$email = new Email_data();
				$email->change_relation();
				break;
			case "change_relation_xml_list";
				$email = new Email_data();
				$email->change_relation_list();
				break;
			case "change_project_xml":
				$email = new Email_data();
				$email->change_project_xml();
				break;
			case "change_private_xml":
				$email = new Email_data();
				$email->change_private_xml();
				break;
			case "change_description":
				$email = new Email_data();
				$email->change_description();
				break;
			case "headerinfo":
				$email = new Email_output();
				$email->emailHeaderInfo();
				break;
			case "mail_send":
				$emaildata = new Email_data();
				$msg = $emaildata->sendMailComplex($_REQUEST["id"]);
				#$email = new Email_output();
				#$email->emailList("", $msg);
				break;

			case "templates":
				$email = new Email_output();
				$email->templateList();
				break;
			case "templateEdit":
				$email = new Email_output();
				$email->templateEdit($_REQUEST["id"]);
				break;
			case "templateOutput":
				$email = new Email_output();
				$email->templateOutput($_REQUEST["id"]);
				break;
			case "templateSave":
				$emailData = new Email_data();
				$id = $emailData->templateSave($_REQUEST["id"]);
				$email = new Email_output();
				$email->templateEdit($id);
				break;
			case "templateDelete":
				$emailData = new Email_data();
				$emailData->templateDelete($_REQUEST["id"]);
				$email = new Email_output();
				$email->templateList();
				break;
			case "templateDeleteFile":
				$emailData = new Email_data();
				$emailData->templateDeleteFile($_REQUEST["id"]);
				$email = new Email_output();
				$email->templateEdit($_REQUEST["template_id"]);
				break;
			case "send_mail_queue":
				$emailData = new Email_data();
				$emailData->send_queue();
				break;
			case "status_mail_queue":
				$emailData = new Email_data();
				$emailData->status_queue($_REQUEST["id"]);
				break;
			case "signatures":
				$email = new Email_output();
				$email->signatureList($_REQUEST["user_id"]);
				break;
			case "signatureEdit":
				$email = new Email_output();
				$email->signatureEdit($_REQUEST["id"], $_REQUEST["user_id"]);
				break;
			case "signatureSave":
				$emailData = new Email_data();
				$emailData->signatureSave($_REQUEST["id"]);
				$email = new Email_output();
				$email->signatureList($_REQUEST["user_id"]);
				break;
			case "signatureDelete":
				$emailData = new Email_data();
				$emailData->signatureDelete($_REQUEST["id"]);
				$email = new Email_output();
				$email->signatureList($_REQUEST["user_id"]);
				break;

			case "filters":
				$email = new Email_output();
				$email->filterList();
				break;
			case "filterEdit":
				$email = new Email_output();
				$email->filterEdit($_REQUEST["id"]);
				break;
			case "filterSave":
				$emailData = new Email_data();
				$emailData->filterSave($_REQUEST["id"]);
				$email = new Email_output();
				$email->filterList();
				break;
			case "filterDelete":
				$emailData = new Email_data();
				$emailData->filterDelete($_REQUEST["id"]);
				$email = new Email_output();
				$email->filterList();
				break;
			case "notelink":
				$email = new Email_output();
				$email->emailOpen();
				break;
			case "tracking":
				$email = new Email_output();
				$email->emailShowTracking();
				break;
			case "media_gallery":
				$email = new Email_output();
				$email->emailMediaGallery();
				break;
			case "selectCla":
				$email = new Email_output();
				$email->SelectCla();
				break;
			case "selectClaAddress":
				$email = new Email_output();
				$email->selectClaAddress();
				break;
			case "deleteAllTrash":
				$email = new Email_data();
				$email->deleteAllTrash();
				break;
			case "archiveOldEmails":
				$email = new Email_data();
				$email->archiveOldEmails();
				break;
			case "edit_pa_autoreply" :
				$email = new Email_output();
				$email->pa_autoreplyEdit($_REQUEST["user_id"]);
				break;
			case "save_pa_autoreply" :
				$email = new Email_data();
				$email->pa_autoreplySave($_REQUEST["autoreply"], 0);
				break;
			case "resend":
				if ($GLOBALS["covide"]->license["disable_basics"]) {
					echo gettext("You have no access to this module");
					exit();
				}
				$emailData = new Email_data();
				$id = $emailData->copy2concepts($_REQUEST["id"]);

				$email = new Email_output();
				$email->emailCompose($id);
				break;
			case "save_bcard_xml" :
				$emailData = new Email_data();
				$emailData->save_bcard_xml($_REQUEST["mail_id"], $_REQUEST["bcard_id"]);
				break;
			case "toggle_state_xml" :
				$emailData = new Email_data();
				$emailData->toggleStateOne($_REQUEST["mail_id"], 1);
				break;
			case "edit_autoreply" :
				$email_output = new Email_output();
				$email_output->editAutoreply($_REQUEST["user_id"]);
				break;
			case "save_autoreply" :
				$email_data = new Email_data();
				$email_data->saveAutoreply($_REQUEST["autoreply"]);
				break;
			case "delete_autoreply" :
				$email_data = new Email_data();
				$email_data->deleteAutoreply($_REQUEST["autoreply"]);
				break;
			case "send_readnotification" :
				$email_data = new Email_data();
				$email_data->sendReadNotification($_REQUEST["mail_id"]);
				break;
			default:
				$email = new Email_output();
				$email->emailList();
				break;

		}

	}
	/* }}} */
}
?>
