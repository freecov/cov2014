<?php
/**
 * Covide CMS module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Cms {
	/* function __construct {{{  */
	public function __construct() {
		if (!$_SESSION["user_id"] && !in_array($_REQUEST["action"], array(
			"recoverAccountPassword", "registerAccount", "registerAccountConfirm", "initdb"
		))) {
			$GLOBALS["covide"]->trigger_login();
		}
		if (!$GLOBALS["covide"]->license["has_cms"] && !in_array($_REQUEST["action"], array("initdb", "gotoFilesys", "media_gallery"))) {
			die("no license for this module");
		}
		switch ($_REQUEST["action"]) {
			case "initdb":
				$cms_data = new Cms_data();
				break;
			case "registerAccount":
				$cms_output = new Cms_output();
				$cms_output->registerAccount($_REQUEST["data"]);
				break;
			case "registerAccountConfirm":
				$cms_output = new Cms_output();
				$cms_output->registerAccountConfirm($_REQUEST["id"], $_REQUEST["hash"], $_REQUEST["site"]);
				break;
			case "recoverAccountPassword":
				$cms_output = new Cms_output();
				$cms_output->recoverAccountPassword($_REQUEST["data"]);
				break;
			case "saveTemplate":
				$cms_data = new Cms_data();
				$id = $cms_data->saveTemplate($_REQUEST);
				#$cms_output = new Cms_output();
				#$cms_output->editTemplate($id);
				break;
			case "deleteTemplate":
				$cms_data = new Cms_data();
				$cms_data->deleteTemplate($_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->siteTemplates();
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
			case "dateOptionsItemDelete":
				$cms_data = new Cms_data();
				$cms_data->dateOptionsItemDelete($_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->dateOptions($_REQUEST["page_id"]);
				break;
				break;
			case "dateOptionsItemSave":
				$cms_data = new Cms_data();
				$cms_data->dateOptionsItemSave($_REQUEST);
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
			case "editSiteInfo":
				$cms_output = new Cms_output();
				$cms_output->editSiteInfo($_REQUEST["siteroot"]);
				break;
			case "editAccountsList":
				$cms_output = new Cms_output();
				$cms_output->editAccountsList();
				break;
			case "editAbbreviations":
				$cms_output = new Cms_output();
				$cms_output->editAbbreviations();
				break;
			case "editAbbreviation":
				$cms_output = new Cms_output();
				$cms_output->editAbbreviation($_REQUEST["id"]);
				break;
			case "saveAbbreviation":
				$cms_data = new Cms_data();
				$cms_data->saveAbbreviation($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->editAbbreviations();
				break;
			case "deleteAbbreviation":
				$cms_data = new Cms_data();
				$cms_data->deleteAbbreviation($_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->editAbbreviations();
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
				$cms_data->gotoFilesys(1, $_REQUEST["ftype"], $_REQUEST["in"]);
				break;
			case "cms_pagelist":
				$cms_output = new Cms_output();
				$cms_output->cmsPageList();
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
			case "cmsform":
				$cms_output = new Cms_output();
				$cms_output->cmsForm($_REQUEST["id"]);
				break;
			case "cmsformedit":
				$cms_output = new Cms_output();
				$cms_output->cmsFormEdit($_REQUEST["pageid"], $_REQUEST["id"]);
				break;
			case "saveFormData":
				$cms_data = new Cms_data();
				$cms_data->saveFormData($_REQUEST);
				break;
			case "saveFormMode":
				$cms_data = new Cms_data();
				$cms_data->saveFormMode($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->cmsForm($_REQUEST["id"]);
				break;
			case "cmsformresults":
				$cms_output = new Cms_output();
				$cms_output->cmsFormResults($_REQUEST["id"]);
				break;
			case "cmsformdelete":
				$cms_data = new Cms_data();
				$cms_data->deleteFormData($_REQUEST["id"], $_REQUEST["itemid"]);
				$cms_output = new Cms_output();
				$cms_output->cmsForm($_REQUEST["id"]);
				break;
			case "cmsresultdelete":
				$cms_data = new Cms_data();
				$cms_data->deleteFormResultData($_REQUEST["id"], $_REQUEST["itemid"]);
				$cms_output = new Cms_output();
				$cms_output->cmsFormResults($_REQUEST["id"]);
				break;
			case "cmsformcrmoptions" :
				$cms_output = new Cms_output();
				$cms_output->cmsFormSettings($_REQUEST["pageid"]);
				break;
			case "saveFormSettings":
				$cms_data = new Cms_data();
				$cms_data->saveFormSettings($_REQUEST["id"], $_REQUEST["formsettings"]);
				$cms_output = new Cms_output();
				$cms_output->cmsFormSettings($_REQUEST["id"]);
				break;
			case "deletepage":
				$cms_output = new Cms_output();
				$cms_output->preparePageDelete($_REQUEST["id"]);
				break;
			case "delete_pages_exec":
				$cms_data = new Cms_data();
				$cms_data->deletePages($_REQUEST["id"]);
				break;
			case "cmsgallery":
				$cms_output = new Cms_output();
				$cms_output->cmsGallery($_REQUEST["id"]);
				break;
			case "saveGallerySettings":
				$cms_data = new Cms_data();
				$cms_data->saveGallerySettings($_REQUEST);
				break;
			case "fupload":
				$cms_data = new Cms_data();
				$cms_data->galleryUpload($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->cmsGallery($_REQUEST["id"]);
				break;
			case "cmsgalleryitemdelete":
				$cms_data = new Cms_data();
				$cms_data->cmsGalleryItemDelete($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->cmsGallery($_REQUEST["id"]);
				break;
			case "cmsgalleryitemedit":
				$cms_output = new Cms_output();
				$cms_output->cmsGalleryItemEdit($_REQUEST["itemid"]);
				break;
			case "saveGalleryItem":
				$cms_data = new Cms_data();
				$cms_data->cmsGalleryItemSave($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->cmsGalleryItemEdit($_REQUEST["id"]);
				break;
			case "galleryitemswitch":
				$cms_data = new Cms_data();
				$cms_data->cmsGalleryItemSwitch($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->cmsGallery($_REQUEST["id"]);
				break;
			case "addSiteRoot":
				$cms_data = new Cms_data();
				$cms_data->addSiteRoot($_REQUEST["name"]);
				$cms_output = new Cms_output();
				$cms_output->cmsSitemap();
				break;
			case "saveSiteInfo":
				$cms_data = new Cms_data();
				$cms_data->saveSiteInfo($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->editSiteInfo($_REQUEST["siteroot"]);
				break;
			case "linkchecker":
				$cms_output = new Cms_output();
				$cms_output->linkchecker();
				break;
			case "startlinkchecker":
				$cms_data = new Cms_data();
				$cms_data->startlinkchecker();
				break;
			case "show_options_info":
				$cms_output = new Cms_output();
				$cms_output->showOptionsInfo($_REQUEST["id"]);
				break;
			case "send_mailing":
				$cms_data = new Cms_data();
				$cms_data->send_mailing($_REQUEST["id"], $_REQUEST["emails"], $_REQUEST["length"]);
				/* no break */
			case "mailings":
			case "init_mailing":
				$cms_output = new Cms_output();
				$cms_output->cmsMailings($_REQUEST["id"]);
				break;
			case "aliashistory":
				$cms_output = new Cms_output();
				$cms_output->cmsAliasHistory($_REQUEST["id"]);
				break;
			case "deletealiashistory":
				$cms_data = new Cms_data();
				$cms_data->cmsDeleteAliasHistory($_REQUEST["itemid"]);
				$cms_output = new Cms_output();
				$cms_output->cmsAliasHistory($_REQUEST["id"]);
				break;
			case "pagehistory":
				$cms_output = new Cms_output();
				$cms_output->cmsPageHistory($_REQUEST["id"]);
				break;
			case "deletepagehistory":
				$cms_data = new Cms_data();
				$cms_data->cmsDeletePageHistory($_REQUEST["itemid"]);
				$cms_output = new Cms_output();
				$cms_output->cmsPageHistory($_REQUEST["id"]);
				break;
			case "restorepagehistory" :
				$cms_data = new Cms_data();
				$cms_data->cmsRestorePageHistory($_REQUEST["itemid"], $_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->cmsEditor();
				break;
			case "bannerList":
				$cms_output = new Cms_output();
				$cms_output->cmsBannerList();
				break;
			case "bannerEdit":
				$cms_output = new Cms_output();
				$cms_output->cmsBannerEdit();
				break;
			case "saveBanner":
				$cms_data = new Cms_data();
				$cms_data->saveBanner($req);
				$cms_output = new Cms_output();
				$cms_output->cmsBannerList();
				break;
			case "pick_banner":
				$cms_output = new Cms_output();
				$cms_output->cmsBannerList(1);
				break;
			case "validateSitemap":
				$cms_output = new Cms_output();
				$cms_output->validateSitemap($_REQUEST["sitemap"]);
				break;
			case "cmsImport":
				$cms_output = new Cms_output();
				$cms_output->cmsImport();
				break;
			case "cmsImportExec":
				$cms_data = new Cms_data();
				$cms_data->cmsImportExec($_REQUEST);
				break;
			case "siteCounters":
				$cms_output = new Cms_output();
				$cms_output->siteCounters();
				break;
			case "deleteHitCounter":
				$cms_data = new Cms_data();
				$cms_data->removeCounter($_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->siteCounters();
				break;
			case "polllist" :
				$cms_output = new Cms_output();
				$cms_output->cmsPolls();
				break;
			case "pollEdit" :
				$cms_output = new Cms_output();
				$cms_output->cmsPollEdit($_REQUEST["id"]);
				break;
			case "pollSave" :
				$cms_data = new Cms_data();
				$poll_id = $cms_data->cmsPollSave($_REQUEST);
				$cms_output = new Cms_output();
				$cms_output->cmsPollEdit($poll_id);
				break;
			case "pollDelete" :
				$cms_data = new Cms_data();
				$cms_data->cmsPollDelete($_REQUEST["id"]);
				break;
			case "pollDeleteAnswer" :
				$cms_data = new Cms_data();
				$pollid = $cms_data->cmsPollDeleteAnswer($_REQUEST["id"]);
				$cms_output = new Cms_output();
				$cms_output->cmsPollEdit($pollid);
				break;
			case "addUserCla" :
				$cms_data = new Cms_data();
				$cms_data->addUserCla($_REQUEST["number"], $_REQUEST["id"]);
				break;
			case "removeUserCla" :
				$cms_data = new Cms_data();
				$cms_data->removeUserCla($_REQUEST["name"], $_REQUEST["id"]);
				break;
			case "loginlog" :
				$cms_output = new Cms_output();
				$cms_output->loginLog();
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
