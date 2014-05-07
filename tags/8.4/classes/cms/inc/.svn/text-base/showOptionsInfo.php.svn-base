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

	if (!class_exists("Cms_output")) {
		die("no class definition found");
	}

	$cms_data = new Cms_data();
	$data = $cms_data->getPageById($id);

	$table = new Layout_table(array(
		"cellspacing" => 1, "cellpadding" => 1
	));
	$table->addTableRow();
		$table->addTableData(array("colspan" => 2), "header");
			$table->addCode(gettext("details of pageid")." [".$id."]");
			$table->addSpace();
			$table->insertAction("view_all", gettext("view page"), sprintf(
				"javascript: popup('http://%s/page/%s.htm');", $_SERVER["HTTP_HOST"], (($data["pageAlias"])?$data["pageAlias"]:$data["id"])));
			$table->insertAction("edit", gettext("view page"), sprintf(
				"javascript:cmsEdit('cmsEditor','%d','');", $id));

		$table->endTableData();
	$table->endTableRow();
	/* page title */
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode(gettext("page title"));
		$table->endTableData();
		$table->addTableData();
			$table->addCode($data["pageTitle"]);
		$table->endTableData();
	$table->endTableRow();
	/* page alias */
	if ($data["pageAlias"]) {
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("page alias"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode($data["pageAlias"].".htm");
			$table->endTableData();
		$table->endTableRow();
	}
	/* page label */
	if ($data["pageLabel"]) {
		$table->addTableRow();
			$table->addTableData("", "header");
				$table->addCode(gettext("page label"));
			$table->endTableData();
			$table->addTableData();
				$table->addCode($data["pageLabel"]);
			$table->endTableData();
		$table->endTableRow();
	}
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode(gettext("publication date"));
		$table->endTableData();
		$table->addTableData();
			$table->addCode(date("d-m-Y H:i", $data["datePublication"]));
		$table->endTableData();
	$table->endTableRow();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode(gettext("last modification"));
		$table->endTableData();
		$table->addTableData();
			$table->addCode(date("d-m-Y H:i", $data["date_changed"]));
		$table->endTableData();
	$table->endTableRow();

	/* start page options */
	$opts = new Layout_table(array(
		"cellspacing" => 1, "cellpadding" => 2
	));
	$opts->addTableRow();
		$opts->addTableData("", "data");
			if ($data["isActive"])
				$opts->insertAction("enabled", "", "");
			else
				$opts->insertAction("disabled", "", "");
		$opts->endTableData();
		$opts->addTableData("", "data");
			if ($data["isActive"])
				$opts->addCode(gettext("This page is active"));
			else
				$opts->addCode(gettext("This page is inactive"));
		$opts->endTableData();
	$opts->endTableRow();
	/* ispublic */
	if ($data["isPublic"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("go_support", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page is public"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	/* ismenuitem */
	if ($data["isMenuItem"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("go_desktop", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page is a menuitem"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	/* istemplate */
	if ($data["isTemplate"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("ftype_calc", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page is available as template"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	/* istemplate */
	if ($data["pageRedirect"]) {
		$opts->addTableRow();
			$opts->addTableData(array("valign" => "top"), "data");
				$opts->insertAction("reload", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has a redirect"));
				$opts->addTag("br");
				$opts->addCode(gettext("Location").": ");
				$opts->insertTag("a", $data["pageRedirect"], array(
					"href" => sprintf("javascript: popup('%s');", $data["pageRedirect"])
				));
				$opts->addTag("br");
				if ($data["pageRedirectPopup"]) {
					$opts->addCode(gettext("Redirect in a popup"));
					$opts->addSpace();
					if ($data["popup_height"] && $data["popup_width"])
						$opts->addCode(sprintf("%dx%d ", $data["popup_width"], $data["popup_height"]));
					if ($data["popup_hidenav"])
						$opts->addCode(gettext("and hide navigation"));

				}
			$opts->endTableData();
		$opts->endTableRow();
	}
	/* search override */
	if ($data["search_override"]) {
		$opts->addTableRow();
			$opts->addTableData(array("valign" => "top"), "data");
				$opts->insertAction("mail_tracking", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has custom SEO settings"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	/* date info */
	if ($data["isDateRange"]) {
		$opts->addTableRow();
			$opts->addTableData(array("valign" => "top"), "data");
				$opts->insertAction("calendar_today", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has a publication range"));
				$opts->addTag("br");
				if ($data["date_start"] && $data["date_end"]) {
					$opts->addCode(date("d-m-Y H:i", $data["date_start"]));
					$opts->addCode(" - ");
					$opts->addCode(date("d-m-Y H:i", $data["date_end"]));
				}	elseif ($data["date_start"]) {
					$opts->addCode(date("d-m-Y H:i", $data["date_start"]));
					$opts->addCode(" - ".gettext("no end date"));
				} else {
					$opts->addCode(gettext("no start date")." - ");
					$opts->addCode(date("d-m-Y H:i", $data["date_end"]));
				}

			$opts->endTableData();
		$opts->endTableRow();
	}
	if ($data["isDate"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("calendar_reg_hour", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has calendar items"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	if ($data["isList"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("mail_templates", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has a listing"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	if ($data["isForm"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("state_special", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has a contact form"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	if ($data["useMetaData"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("mail_readconfirm", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has extra meta information"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	if ($data["isSticky"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("down", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page is sticky / locked"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	if ($data["isGallery"]) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("ftype_image", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has an image gallery"));
			$opts->endTableData();
		$opts->endTableRow();
	}
	if ($cms_data->checkPagePermissions($id)) {
		$opts->addTableRow();
			$opts->addTableData("", "data");
				$opts->insertAction("permissions", "", "");
			$opts->endTableData();
			$opts->addTableData("", "data");
				$opts->addCode(gettext("This page has custom page permissions"));
			$opts->endTableData();
		$opts->endTableRow();
	}



	$opts->endTable();
	$table->addTableRow();
		$table->addTableData("", "header");
			$table->addCode(gettext("page options"));
		$table->endTableData();
		$table->addTableData("", "header");
			$table->addCode($opts->generate_output());
		$table->endTableData();
	$table->endTableRow();

	$table->endTable();

	$buf = str_replace("'", "\'", preg_replace("/(\r|\n)/si", "", $table->generate_output()) );
	echo sprintf("infoLayer('%s');", $buf);
?>