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
		"title" => gettext("E-mail filters"),
		"subtitle" => gettext("edit")
	));

	$mailData = new Email_data();
	if ($id) {
		$list = $mailData->get_filter_list($id);
	}

	$venster->addMenuItem(gettext("filters"), "?mod=email&action=filters");
	$venster->generateMenuItems();

	$tbl = new Layout_table( array("cellspacing"=>1) );

	/* prioriteit */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("priority"), "", "header");
		$tbl->addTableData("", "data");
			$prior = array();
			for ($i=0;$i<=100;$i++) {
				$prior[]=$i;
			}
			$tbl->addSelectField("mail[priority]", $prior, $list[0]["priority"]);
		$tbl->endTableData();
	$tbl->endTableRow();

	/* email from */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("sender address"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[sender]", $list[0]["sender"], array("style"=>"width: 350px"));
			$tbl->addCode("*");
		$tbl->endTableData();
	$tbl->endTableRow();

	/* email rcpt */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("recipient address"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[recipient]", $list[0]["recipient"], array("style"=>"width: 350px"));
			$tbl->addCode("*");
		$tbl->endTableData();
	$tbl->endTableRow();

	/* subject */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("subject"), "", "header");
		$tbl->addTableData("", "data");
			$tbl->addTextField("mail[subject]", $list[0]["subject"], array("style"=>"width: 350px"));
			$tbl->addCode("*");
		$tbl->endTableData();
	$tbl->endTableRow();

	/* to folder */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("to folder"), "", "header");
		$tbl->addTableData("", "data");
			$folders = $mailData->getFolders();
			$tbl->addCode($this->getSelectList("mail[to_mapid]", $folders, $list[0]["to_mapid"], array("style"=>"width: 250px") ) );
		$tbl->endTableData();
	$tbl->endTableRow();

	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->addCode(" * = ");
			$tbl->addCode( gettext("Subject is a match on any part, sender and recipient are exact matches.") );
		$tbl->endTableData();
	$tbl->endTableRow();

	$tbl->addTableRow();
		$tbl->addTableData(array("colspan"=>2), "header");
			$tbl->insertAction("back", gettext("back"), "?mod=email&action=filters");
			$tbl->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
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
	$output->addHiddenField("action", "filterSave");
	$output->addHiddenField("id", $id);
	$output->addCode( $venster->generate_output() );
	$output->endTag("form");

	$output->load_javascript(self::include_dir."filtersEdit.js");

	$output->layout_page_end();

	$output->exit_buffer();
?>
