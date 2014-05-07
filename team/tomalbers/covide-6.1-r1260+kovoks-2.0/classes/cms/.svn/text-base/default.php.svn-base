<?php
Class Cms {
	/* function __construct {{{  */
	public function __construct() {
		if (!$_SESSION["user_id"]) {
			$GLOBALS["covide"]->trigger_login();
		}
		if (!$GLOBALS["covide"]->license["has_cms"]) {
			die("no license for this module");
		}
		switch ($_REQUEST["action"]) {
			case "saveTemplate":
				$cms_data = new Cms_data();
				$id = $cms_data->saveTemplate($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->editTemplate($id);
				break;
			case "siteTemplates":
				$cms_output = new Cms_output();
				$cms_output->siteTemplates();
				break;
			case "editTemplate":
				$cms_output = new Cms_output();
				$cms_output->editTemplate($_REQUEST["id"]);
				break;
			case "dateoptions":
				$cms_output = new Cms_output();
				$cms_output->dateOptions($_REQUEST["id"]);
				break;
			case "saveDateOptions":
				$cms_data = new Cms_data();
				$cms_data->saveDateOptions($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->dateOptions($_REQUEST["id"]);
				break;
			case "dateOptionsItemEdit":
				$cms_output = new Cms_output();
				$cms_output->dateOptionsItemEdit($_REQUEST["id"]);
				break;
			case "saveAuthorisations":
				$cms_data = new Cms_data();
				$cms_data->saveAuthorisations($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->editAuthorisations($_REQUEST["id"]);
				break;
			case "authorisations":
				$cms_output = new Cms_output();
				$cms_output->editAuthorisations($_REQUEST["id"]);
				break;
			case "cmsfile":
				$cms_data = new Cms_data();
				$cms_data->getCmsFile($_REQUEST["id"]);
				break;
			case "editCmsSettings":
				$cms_output = new Cms_output();
				$cms_output->editCmsSettings();
				break;
			case "saveCmsSettings":
				$cms_data = new Cms_data();
				$cms_data->saveCmsSettings($_REQUEST);
				break;
			case "editAccountsList":
				$cms_output = new Cms_output();
				$cms_output->editAccountsList();
				break;
			case "editAccount":
				$cms_output = new Cms_output();
				$cms_output->editAccount($_REQUEST["id"]);
				break;
			case "saveAccount":
				$cms_data = new Cms_data();
				$cms_data->saveAccount($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->editAccountsList();
				break;
			case "deleteAccount":
				$cms_data = new Cms_data();
				$cms_data->deleteAccount($_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->editAccountsList();
				break;
			case "filesys":
				$cms_data = new Cms_data();
				$cms_data->gotoFilesys();
				break;
			case "media_gallery":
				$cms_data = new Cms_data();
				$cms_data->gotoFilesys(1, $_REQUEST["ftype"]);
				break;
			case "editpage":
				$cms_output = new Cms_output();
				$cms_output->cmsEditor();
				break;
			case "editSettings":
				$cms_output = new Cms_output();
				$cms_output->cmsPageSettings();
				break;
			case "editpagetext":
				$cms_output = new Cms_output();
				$cms_output->cmsEditor(1);
				break;
			case "savePage":
				$cms_data = new Cms_data();
				if ($_REQUEST["cms"]["id"]) {
					$cms_data->savePageData($_REQUEST["cms"]["id"], 0, $_REQUEST);
				} else {
					$cms_data->insertPage($_REQUEST);
				}
				break;
			case "savePageData":
				$cms_data = new Cms_data();
				$cms_data->savePageData($_REQUEST["id"]);
				break;
			case "savePageSettings":
				$cms_data = new Cms_data();
				$cms_data->savePageSettings($_REQUEST);
				break;
			case "saveRestorePoint":
				$cms_data = new Cms_data();
				$cms_data->saveRestorePoint($_REQUEST);
				break;
			case "truncateRestorePoint":
				$cms_data = new Cms_data();
				$cms_data->truncateRestorePoint($_REQUEST["id"], $_REQUEST["close_window"]);
				break;
			case "loadRestorePoint":
				$cms_data = new Cms_data();
				$cms_data->loadRestorePoint($_REQUEST["id"]);
				break;
			case "viewRestorePoint":
				$cms_output = new Cms_output();
				$cms_output->viewRestorePoint($_REQUEST["id"]);
				break;
			case "searchPageXML":
				$cms_data = new Cms_data();
				$cms_data->searchPageXML($_REQUEST["id"]);
				break;
			case "checkalias":
				$cms_data = new Cms_data();
				$cms_data->checkAlias($_REQUEST["id"], $_REQUEST["alias"]);
				break;
			case "checkusername":
				$cms_data = new Cms_data();
				$cms_data->checkUsername($_REQUEST["id"], $_REQUEST["username"]);
				break;
			case "highlight_init":
				$cms_output = new Cms_output();
				$cms_output->highlight_init();
				break;
			case "highlight_show":
				$cms_output = new Cms_output();
				$cms_output->highlight_show();
				break;
			case "metadataDefinitions":
				$cms_output = new Cms_output();
				$cms_output->metadataDefinitions();
				break;
			case "metadataDefinitionsEdit":
				$cms_output = new Cms_output();
				$cms_output->metadataDefinitionsEdit();
				break;
			case "saveMetadataDefinition":
				$cms_data = new Cms_data();
				$cms_data->saveMetadataDefinition($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->metadataDefinitions();
				break;
			case "metadataDefinitionsDelete":
				$cms_data = new Cms_data();
				$cms_data->metadataDefinitionsDelete($_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->metadataDefinitions();
			case "metadata":
				$cms_output = new Cms_output();
				$cms_output->metadata($_REQUEST["id"]);
				break;
			case "saveMetadata":
				$cms_data = new Cms_data();
				$cms_data->saveMetadata($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->metadata($_REQUEST["id"]);
				break;
			case "cmslist":
				$cms_output = new Cms_output();
				$cms_output->cmslist($_REQUEST["id"]);
				break;
			case "cmsSaveList":
				$cms_data = new Cms_data();
				$cms_data->saveCmsList($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->cmslist($_REQUEST["id"]);
				break;
			case "cmsDeleteListItem":
				$cms_data = new Cms_data();
				$cms_data->deleteListItem($_REQUEST["item"], $_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->cmslist($_REQUEST["id"]);
				break;
			default :
				$cms_output = new Cms_output();
				$cms_output->cmsSitemap();
				break;

		}
	}
	/* }}} */
}

?>
