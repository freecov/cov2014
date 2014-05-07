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
		"subtitle" => gettext("external accounts")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getAccountList($id);
	$cms = $cms[0];

	$this->addMenuItems($venster);
	$venster->generateMenuItems();

	$venster->addVensterData();
		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("username"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTextField("cms[username]", $cms["username"]);
				$tbl->insertTag("span", "", array(
					"id"    => "username_layer"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("password"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addPasswordField("cms[password]", $cms["password"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("email"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addTextField("cms[email]", $cms["email"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("active"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addCheckBox("cms[is_enabled]", 1, ($cms["is_enabled"]) ? 1:0);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("registered"));
			$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addHiddenField("cms[is_active]", 1, ($cms["is_active"]) ? 1:0);
				$tbl->addCode(($cms['is_enabled']) ? gettext('yes') : gettext('no'));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("address selection"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addHiddenField("cms[address_id]", $cms["address_id"]);
				$tbl->insertTag("span", "", array(
					"id" => "searchrel"
				));
				$tbl->addSpace(1);
				$tbl->insertAction("edit", gettext("change:"), "javascript: popup('?mod=address&action=searchRel', 'searchrel', 0, 0, 1);");
				$address_data = new Address_data();
				$tbl->addCode($cms["address_name"]);

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode($tbl->generate_output());

		$venster->insertAction("back", gettext("back"), "?mod=cms&action=editAccountsList");
		$venster->addTag("span", array(
			"id"    => "save_page_layer",
			"style" => "visibility: hidden;"
		));
			$venster->insertAction("save", gettext("save"), "javascript: saveSettings();");
		$venster->endTag("span");
		#$venster->insertAction("close", gettext("close"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveAccount");
	$output->addHiddenField("id", $id);

	$output->addCode($venster->generate_output());
	$output->endTag("form");
	$output->load_javascript(self::include_dir."editAccount.js");
	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();

?>
