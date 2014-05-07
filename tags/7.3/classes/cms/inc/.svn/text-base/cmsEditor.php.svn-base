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

	$output = new Layout_output();
	$output->layout_page("cms", 1);

	$venster = new Layout_venster(array(
		"title" => gettext("CMS"),
		"subtitle" => gettext("Edit page")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getPageById($_REQUEST["id"], $_REQUEST["parentpage"]);

	$user_data = new User_data();
	$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

	$r = $cms_data->getUserPermissions($_REQUEST["id"], $_SESSION["user_id"]);
	if ($user_info["xs_cms_level"] < 2 && !$r["editRight"] && $_REQUEST["id"]) {
		$output->start_javascript();
			$output->addCode("alert(gettext('You have no permissions to open this page.'));");
			$output->addCode("window.close();");
		$output->end_javascript();
		$output->exit_buffer();
	}
	if ($cms["pageRedirect"] && !$cms["pageRedirectPopup"] && !$_REQUEST["noredir"]) {
		header("Location: ?mod=cms&action=editSettings&reason=redirect&id=".$_REQUEST["id"]);
		exit();
	}
	if ($cms["isSticky"]) {
		header("Location: ?mod=cms&action=editSettings&reason=sticky&id=".$_REQUEST["id"]);
		exit();
	}
	if ($_REQUEST["id"]) {
		$this->addMenuItems(&$venster);
	} else {
		$venster->addMenuItem(gettext("close"), "javascript: window.close();");
		$venster->addMenuItem(gettext("save"), "");
	}
	$venster->generateMenuItems();

	$venster->addVensterData();

	$tbl = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1,
		"width" => "740px"
	));
	if ($cms["autosave_info"]) {
		$tbl->addTableRow(array("id" => "autosave_info"));
			$tbl->addTableData(array("colspan"=>4), "data");
			$tbl->addTag("div", array("style" => "border: 1px dashed red; padding: 2px;"));
				$tbl->addTag("b");
				$tbl->addCode(gettext("This page was not saved last time, but it was altered."));
				$tbl->endTag("b");
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext("Do you want to restore ?")." ");
				$tbl->insertAction("ok", gettext("yes"), "javascript: loadRestorePoint();");
				$tbl->insertAction("cancel", gettext("no"), "javascript: truncateRestorePoint();");
				$tbl->addTag("br");
				$tbl->addTag("br");
				$tbl->addCode(gettext("Page data of restorepoint"));

				$user_data = new User_data();
				$tmp = explode("|", $cms["autosave_info"]);
				$tbl->addCode("(".gettext("user").": ".$user_data->getUsernameById($tmp[0]));
				$tbl->addCode(",".gettext("timestamp").": ".date("d-m-Y H:i:s", $tmp[1])."):");
				$tbl->addTag("iframe", array(
					"name" => "restorepoint",
					"src" => "?mod=cms&action=viewRestorePoint&id=".$cms["id"],
					"width" => "686px",
					"height" => "450px;",
					"style" => "border: 1px solid black;"
				));
				$tbl->endTag("iframe");

			$tbl->endTag("div");
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	$tbl->addTableRow();

		$tbl->addTableData(array(
			"colspan" => 4
		), "header");
			if ($_REQUEST["id"]) {
				$tbl->addCode(gettext("page id")." [".$cms["id"]."], ".
					gettext("last changed on").": ".date("d-m-Y H:i"));
			} else {
				$tbl->addCode(gettext("create new page"));
				$tbl->start_javascript();
					$tbl->addCode("function sync_editor_contents() { void(0); }");
				$tbl->end_javascript();
			}
		$tbl->addTableData();
			$tbl->insertAction("close", gettext("close"), "javascript: closePage();");
		$tbl->endTableData();
	$tbl->endTableRow();

	$tbl->addTableRow();
		$tbl->addTableData("", "header");
			$tbl->addCode(gettext("page title"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addTextField("cms[pageTitle]", $cms["pageTitle"], array("style"=>"width: 250px;"));
		$tbl->endTableData();
		$tbl->addTableData("", "header");
			$tbl->addCode(gettext("publication date"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			for ($i=1; $i<=31; $i++) {
				$days[$i] = $i;
			}
			for ($i=1; $i<=12; $i++) {
				$months[$i] = $i;
			}
			for ($i=2000; $i<=date("Y")+5; $i++) {
				$years[$i] = $i;
			}
			for ($i=0; $i<=23; $i++) {
				$hour[$i] = $i;
			}
			for ($i=0; $i<60; $i+=15) {
				$min[$i] = sprintf("%02s", $i);
			}
			$cms["timestamp"] =& $cms["datePublication"];

			$tbl->addSelectField("cms[timestamp_day]",   $days,   date("d", $cms["timestamp"]));
			$tbl->addSelectField("cms[timestamp_month]", $months, date("m", $cms["timestamp"]));
			$tbl->addSelectField("cms[timestamp_year]",  $years,  date("Y", $cms["timestamp"]));
			$calendar = new Calendar_output();
			$tbl->addCode( $calendar->show_calendar("document.getElementById('cmstimestamp_day')", "document.getElementById('cmstimestamp_month')", "document.getElementById('cmstimestamp_year')" ));
			$tbl->addSpace();
			$tbl->addSelectField("cms[timestamp_hour]",  $hour,  date("H", $cms["timestamp"]));
			$tbl->addCode(":");
			$tbl->addSelectField("cms[timestamp_min]",  $min,  date("i", $cms["timestamp"]));

		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->addTableRow();
		$tbl->addTableData(array("valign" => "top"), "header");
			$tbl->addCode(gettext("page alias"));
		$tbl->endTableData();
		$tbl->addTableData("", "data");
			$tbl->addTextField("cms[pageAlias]", $cms["pageAlias"], array("style"=>"width: 250px;"));
			$tbl->insertTag("span", "", array(
				"id" => "alias_layer"
			));
		$tbl->endTableData();
		$tbl->addTableData(array("valign" => "top"), "header");
			$tbl->addCode(gettext("page label"));
		$tbl->endTableData();
		$tbl->addTableData(array("valign" => "top"), "data");
			$tbl->addTextField("cms[pageLabel]", $cms["pageLabel"], array("style"=>"width: 200px;"));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();
	$venster->addCode($tbl->generate_output());

	if ($_REQUEST["id"]) {
		$venster->addTextArea("contents", $cms["pageData"], array(
			"style" => "width: 700px; height: 464px;"
		));
		if (!$text_only) {
			$editor = new Layout_editor();
			$venster->addCode( $editor->generate_editor("", $cms["pageData"]) );
		} else {
			$venster->start_javascript();
				$venster->addCode("function sync_editor_contents() { return true; }");
			$venster->end_javascript();
		}
		$venster->addTag("br");
		$venster->addTag("span", array(
			"id" => "save_page_layer"
		));
			$venster->insertAction("save", gettext("save"), "javascript: savePage();");
		$venster->endTag("span");
		$venster->insertAction("close", gettext("close"), "javascript: closePage();");

		$venster->insertAction("last", gettext("force restorepoint"), "javascript: saveRestorePoint();");
		$venster->addSpace(2);
		$venster->addCode( gettext("save restorepoint").": " );
		$venster->insertTag("span", "", array("id"=>"autosave_progressbar"));

	}	else {
		$tbl = new Layout_table();
		/* isActive */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page is active"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isActive]", 1, 1);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isPublic */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page is public"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isPublic]", 1, 1);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* isMenuItem */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("page is menuitem"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->insertCheckBox("cms[isMenuItem]", 1, 0);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* templates */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("use template"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$sel = $cms_data->getTemplates();
				$tbl->addSelectField("cms[template]", $sel, "");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());

		$venster->insertAction("close", gettext("close"), "javascript: window.close();");
		$venster->addSpace();
		$venster->addTag("span", array(
			"id" => "save_page_layer"
		));
			$venster->insertAction("forward", gettext("next"), "javascript: savePage();");
		$venster->endTag("span");
	}
	$venster->load_javascript(self::include_dir."script_cms.js");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "post",
		"target" => "dbhandler"
	));
	if ($cms["autosave_info"]) {
		$block = 1;
	} elseif (!$_REQUEST["id"]) {
		$block = 2;
	} else {
		$block = 0;
	}
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("block_autosave", $block);
	$output->addHiddenField("syncweb", $_REQUEST["syncweb"]);
	$output->addHiddenField("action", "savePage");
	$output->addHiddenField("cms[id]", $cms["id"]);
	$output->addHiddenField("cms[parentPage]", $cms["parentPage"]);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->addTag("iframe", array(
		"id"          => "dbhandler",
		"name"        => "dbhandler",
		"src"         => "blank.htm",
		"width"       => "0px",
		"frameborder" =>  0,
		"border"      =>  0,
		"height"      => "0px;",
		"visiblity"   => "hidden"
	));
	$output->endTag("iframe");
	$output->load_javascript(self::include_dir."cmsEditor.js");


	$output->layout_page_end();
	$output->exit_buffer();

?>
