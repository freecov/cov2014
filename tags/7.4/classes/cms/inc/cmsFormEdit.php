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
		"subtitle" => gettext("add/alter metadata definition")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getFormData($pageid, $id);

	$venster->addVensterData();

	$cms_data->meta_field_types["hidden"] = gettext("hidden field");
	$cms_data->meta_field_types["upload"] = gettext("file upload field");
	$cms_data->meta_field_types["date"]   = gettext("date field");
	$cms_data->meta_field_types["datetime"] = gettext("datetime field");
	$cms_data->meta_field_types["address"]  = gettext("address block");

	unset($cms_data->meta_field_types["shop"]);

		$tbl = new Layout_table(array(
			"cellspacing" => 1
		));
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("Form definition"), array("colspan"=>2), "header");
		$tbl->endTableRow();
		/* order */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("order"), "", "header");
			$tbl->addTableData("", "data");
				$sel = array();
				for ($i=0;$i<=50;$i++) {
					$sel[$i] = $i;
				}
				$tbl->addSelectField("cms[order]", $sel, $cms["order"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* field name */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("name"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextField("cms[field_name]", $cms["field_name"], array("style" => "width: 250px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* field description */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("description above field")."*", "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextArea("cms[description]", $cms["description"], array("style" => "width: 250px; height: 50px;"));
				$tbl->addTag("br");
				$tbl->insertTag("i", "* ".gettext("description is normally empty and will be displayed above the field"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* field type */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("type of field"), "", "header");
			$tbl->addTableData("", "data");
				$tbl->addSelectField("cms[field_type]", $cms_data->meta_field_types, $cms["field_type"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* default value */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("default value")."**", "", "header");
			$tbl->addTableData("", "data");
				$tbl->addTextArea("cms[field_value]", $cms["field_value"], array(
					"style" => "width: 300px; height: 150px;",
					"wrap"  => "off"
				));

				$tbl->addTag("i");
				$tbl->addTag("br");
				$tbl->addCode("** ".gettext("special values and behaviors"));
				$tbl->addTag("br");
				$tbl->addTag("ul");
				$tbl->insertTag("li", gettext("For a dropdown menu enter the values seperated by a newline."));
				$tbl->insertTag("li", gettext("One item per line, the first value will be the default choice."));
				$tbl->insertTag("li", gettext("With a field of type address block, the field subject, receipient, etc is referred to is the relations email address."));
				$tbl->endTag("ul");
				$tbl->endTag("i");

			$tbl->endTableData();

		$tbl->endTableRow();
		/* special properties */
		$tbl->addTableRow();
			$tbl->insertTableData(gettext("special properties"), "", "header");
			$tbl->addTableData("", "data");
				/* is_required */
				$tbl->insertAction("toggle", "", "");
				$tbl->addCheckBox("cms[is_required]", 1, ($cms["is_required"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode(gettext("this is a mandatory field"));
				$tbl->addTag("br");
				/* is_rcpt */
				$tbl->insertAction("state_public", "", "");
				$tbl->addCheckBox("cms[is_mailto]", 1, ($cms["is_mailto"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode(gettext("this is the receipient email address"));
				$tbl->addTag("br");
				/* is_from */
				$tbl->insertAction("state_special", "", "");
				$tbl->addCheckBox("cms[is_mailfrom]", 1, ($cms["is_mailfrom"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode(gettext("this is the sender email address"));
				$tbl->addTag("br");
				/* is_subject */
				$tbl->insertAction("mail_copy", "", "");
				$tbl->addCheckBox("cms[is_mailsubject]", 1, ($cms["is_mailsubject"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode(gettext("this is the subject"));
				$tbl->addTag("br");
				/* is_subject */
				$tbl->insertAction("mail_forward", "", "");
				$tbl->addCheckBox("cms[is_redirect]", 1, ($cms["is_redirect"]) ? 1:0);
				$tbl->addSpace();
				$tbl->addCode(gettext("this is the resultpage"));
				$tbl->addTag("br");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* default value */


		$tbl->endTable();
		$venster->addCode( $tbl->generate_output() );


		$venster->insertAction("back", gettext("back"), "javascript: window.close();");
		$venster->insertAction("save", gettext("new item"), "javascript: saveSettings();");

	$venster->endVensterData();

	$output->addTag("form", array(
		"id"     => "velden",
		"method" => "post",
		"action" => "index.php"
	));
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "saveFormData");
	$output->addHiddenField("id", $id);
	$output->addHiddenField("pageid", $pageid);

	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->start_javascript();
		$output->addCode("
			document.getElementById('cmsfield_type').onchange = function() {
				checkFormStat();
			}
			document.getElementById('cmsis_mailto').onchange = function() {
				checkFormStat();
			}
			function checkFormStat() {
				if (document.getElementById('cmsfield_type').value != 'hidden' && document.getElementById('cmsis_mailto').checked == true) {
					document.getElementById('cmsis_mailto').checked = false;
					alert(gettext('Het geaddresseerde veld mag niet vrij door de gebruiker worden ingevuld.'));
				}
			}
		");
	$output->end_javascript();

	$output->load_javascript(self::include_dir."script_cms.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>