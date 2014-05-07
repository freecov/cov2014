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

	if (!class_exists("Email_data")) {
		exit("no class definition found");
	}

	$fspath = $GLOBALS["covide"]->filesyspath;

	$output = new layout_output();
	$output->layout_page();

	$venster = new Layout_venster( array(
		"title" => gettext("E-mail predefined content"),
		"subtitle" => gettext("edit")
	));

	$mailData = new Email_data();
	if ($id) {
		$list = $mailData->get_signature_list($id, $user_id);
	}

	$venster->addMenuItem(gettext("content"), "?mod=email&action=signatures");
	$venster->generateMenuItems();

	$tbl = new Layout_table( array("cellspacing"=>1) );
	/* email */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("email address"), "", "header");
		$tbl->addTableData("", "data");
			if ($list[0]["default"])
				$tbl->addCode(gettext("default email address"));
			else
				$tbl->addTextField("mail[email]", $list[0]["email"], array("style"=>"width: 350px"));
		$tbl->endTableData();
	$tbl->endTableRow();
	if (!$list[0]["default"]) {
		/* subject */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("description"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("mail[subject]", $list[0]["subject"], array("style"=>"width: 350px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* realname */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("sender name"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("mail[realname]", $list[0]["realname"], array("style"=>"width: 350px"));
				//$tbl->insertTag("b", " *");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* companyname */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("company name"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("mail[companyname]", $list[0]["companyname"], array("style"=>"width: 350px"));
				//$tbl->insertTag("b", " *");
			$tbl->endTableData();
		$tbl->endTableRow();
	}
	/* signature */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("text content"), "", "header");
		$tbl->addTableData("", "data");
			//TODO: Don't allow mail to be longer than 78 characters on one line.
			$tbl->addTextArea("mail[signature]", $list[0]["signature"], array("style"=>"width: 850px; height: 180px;", "wrap"=>"off"));
		$tbl->endTableData();
	$tbl->endTableRow();

	/* signature */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("html content"), "", "header");
		$tbl->addTableData("", "data");
			$editor = new Layout_editor();
			$ret = $editor->generate_editor("", nl2br($list[0]["signature_html"]), "false", "contents", 1);
			if ($ret !== false) {
				$tbl->addTextArea("mail[signature_html]", nl2br($list[0]["signature_html"]), array("style" => "width: 850px; height: 480px;"), "contents");
				$tbl->addCode($ret);
			} else {
				$tbl->addCode(gettext("Your browser does not support our HTML editor. Not possible to alter your HTML signature."));
			}
		$tbl->endTableData();
	$tbl->endTableRow();


	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->insertAction("back", gettext("back"), "?mod=email&action=signatures");
			$tbl->insertAction("save", gettext("save"), "javascript: signature_save();");
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();

	$venster->addVensterData();
		$venster->addCode( $tbl->generate_output() );
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"action" => "index.php",
		"method" => "POST",
		"enctype" => "multipart/form-data"
	));
	$output->addHiddenField("mod", "email");
	$output->addHiddenField("action", "signatureSave");
	$output->addHiddenField("id", $id);
	$output->addHiddenField("user_id", $user_id);
	$output->addCode( $venster->generate_output() );
	$output->endTag("form");

	$output->load_javascript(self::include_dir."signatureEdit.js");

	$output->layout_page_end();

	$output->exit_buffer();
?>
