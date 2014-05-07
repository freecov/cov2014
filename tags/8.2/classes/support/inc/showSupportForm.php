<?php
	/**
	 * Covide Groupware-CRM support module
	 *
	 * Covide Groupware-CRM is the solutions for all groups off people
	 * that want the most efficient way to work to together.
	 * @version %%VERSION%%
	 * @license http://www.gnu.org/licenses/gpl.html GPL
	 * @link http://www.covide.net Project home.
	 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
	 * @copyright Copyright 2000-2007 Covide BV
	 * @package Covide
	 */
	if (!class_exists("Support_output")) {
		die("no class definition found");
	}
	$reference_nr = rand(100000000, 999999999);

	$output = new Layout_output();
	if ($options["fullpage"]) {
		$output->addCode("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
		$output->addTag("html");
		$output->addTag("head");
		$output->insertTag("title", gettext("New support request"));
		if ($options["css"])
			$output->addTag("link", array(
				"rel"  => "stylesheet",
			  "href" => $options["css"],
				"type" => "text/css"
			));

		$output->endTag("head");
		$output->addTag("body");
	}
	$output->addTag("form", array(
		"id"     => "supportform",
		"method" => "post",
		"action" => $GLOBALS["covide"]->webroot."supportform.php",
		"target" => "formhandler"
	));
	$output->addHiddenField("action", "submit");
	$tbl = new Layout_table(array(
		"cellspacing" => 1,
		"cellpadding" => 1,
		"class" => "supportform"
	));
	/* type of request */
	$tbl->addTableRow();
		$tbl->addTableData(array("style" => "vertical-align: top", "colspan" => 2));
			$tbl->addRadioField("support[type]", gettext("support request"), 1, 1);
			$tbl->addTag("br");
			$tbl->addRadioField("support[type]", gettext("question")."/".gettext("information request"), 2, 1);
			$tbl->addTag("br");
			$tbl->addTag("br");
			$tbl->endTableData();
	$tbl->endTableRow();
	/* ref nr */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("reference nr"));
		$tbl->insertTableData($reference_nr);
	$tbl->endTableRow();
	/* customer name */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("customer name"));
		$tbl->addTableData();
			$tbl->addHiddenField("support[reference_nr]", $reference_nr);
			$tbl->addTextField("support[name]", "", array(
				"style" => "width: 250px; text-align: left;"
			), "", 1);
		$tbl->endTableData();
	$tbl->endTableRow();
	/* email address */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("email address"));
		$tbl->addTableData();
			$tbl->addTextField("support[email]", "", array(
				"style" => "width: 250px; text-align: left;"
			), "", 1);
		$tbl->endTableData();
	$tbl->endTableRow();
	/* description */
	$tbl->addTableRow();
		$tbl->insertTableData(gettext("description"), array("style" => "vertical-align: top"));
		$tbl->addTableData();
			$tbl->addTextArea("support[description]", "", array(
				"style" => "width: 250px; height: 200px; text-align: left;"
			));
		$tbl->endTableData();
	$tbl->endTableRow();
	/* submit */
	$tbl->addTableRow();
		$tbl->addTableData(array("colspan" => 2, "style" => "text-align: right"));
			$tbl->insertTag("a", gettext("submit support call"), array(
				"href" => "javascript: submitSupportCall();"
			));
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();
	$output->addCode($tbl->generate_output());

	$output->addHiddenField("alert[name]", gettext("no value specified for: customer name"));
	$output->addHiddenField("alert[email]", gettext("no value specified for: email address"));
	$output->addHiddenField("alert[description]", gettext("no value specified for: description"));

	$output->addHiddenField("support[result_url]", $options["result_url"]);

	$output->addTag("iframe", array(
		"id"     => "formhandler",
		"name"   => "formhandler",
		"src"    => $GLOBALS["covide"]->webroot."blank.htm",
		"width"  => "200px",
		"frameborder" => 0,
		"border" => 0,
		"height" => "200px;",
		"visiblity" => "hidden"
	));
	$output->endTag("iframe");
	$output->endTag("form");

	$output->start_javascript();
	$output->addCode( "\n".file_get_contents(self::include_dir."showSupportForm.js")."\n" );
	$output->end_javascript();

	if ($options["fullpage"]) {
		$output->endTag("body");
		$output->endTag("html");
	}
	$output->exit_buffer();
?>
