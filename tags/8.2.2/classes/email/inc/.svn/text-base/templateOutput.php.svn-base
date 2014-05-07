<?php
/**
 * Covide Email module
 *
 * @author Gerben Jacobs <ghjacobs@users.sourceforge.net>
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
	$id = $_REQUEST["id"];
	$mid = $_REQUEST["mid"];
	$email_data = new Email_data();
	$data = $email_data->getTemplateFields($id);
	$mdata = $email_data->getEmailById($mid);
	$tpl_values = $email_data->decodeMailOptions($mdata[0]["options"]);
	preg_match_all("/\{{([^}}]*?)}}/sx", $data["html_data"], $fields, PREG_SET_ORDER);
	$output = new layout_output();
	$output->layout_page('',1);

	$venster = new Layout_venster( array(
		"title" => gettext("E-mail templates"),
		"subtitle" => gettext("edit")
	));

	$table_template = new Layout_table(array("align"=>"left"));

	// Find all fields in a template and show them using textareas
	foreach ($fields as $value) {
		// Nasty solution? Maybe some regular expression?
		if (substr($value[1], 0, 4) != "tpl_") {
		$table_template->addTableRow();

			$table_template->addTableData("", header);
				$table_template->addCode($value[1]);
			$table_template->endTableData();

			$table_template->addTableData;
				$table_template->addTextArea(sprintf("mail[template_values][%s]", $value[1]),
					$tpl_values[sprintf("tpl_%s", $value[1])]);
			$table_template->endTableData();

		$table_template->endTableRow();
		}
	}
	$table_template->endTable();

	$output->addCode( $table_template->generate_output() );
	$output->layout_page_end();

	$output->exit_buffer();
?>