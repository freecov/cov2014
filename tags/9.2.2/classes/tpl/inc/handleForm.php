<?php
/**
 * Covide Template Parser module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

	if (!class_exists("Tpl_output")) {
		die("no class definition found");
	}

	$forms = $this->cms->getFormData($pageid);

	$output_nav = new Layout_output();
	$output_nav->insertTag("a", gettext("reset values"), array(
		"id"   => "form_reset_link",
		"href" => "javascript: document.getElementById('formident').reset();"));
	$output_nav->addSpace();
	if ($this->cms_license['recaptcha_public']) {
		$output_nav->addCode('|');
		$output_nav->addSpace();
		$output_nav->insertTag("a", gettext("renew captcha"), array(
			"id"   => "captcha_reset_link",
			"href" => "javascript: Recaptcha.reload()"));
		$output_nav->addSpace();
	}
	$output_nav->addCode("|");
	$output_nav->addSpace();
	if ($this->is_shop) {
		$output_nav->addTag("span", array(
			"id" => "form_submit_link"
		));
		$output_nav->insertTag("a", gettext("purchase items")." &gt;&gt;", array(
			"href" => "javascript: formsubmit();",
			"id"   => "ideal_order_button"));
		$output_nav->endTag("span");
		$output_nav->insertTag("b", gettext("your order has been sent"), array(
			"style" => "display: none;",
			"id"    => "ideal_order_done"
		));
	} else {
		$output_nav->insertTag("a", gettext("send form")." &gt;&gt;", array(
			"id"   => "form_submit_link",
			"href" => "javascript: formsubmit();"));
	}
	$custom_nav = $output_nav->generate_output();

	$short_description = 1;

	$key = sprintf("s:{%s} p:{%s}", session_id(), $pageid);
	$challenge = md5(rand().session_id().time());

	/* check for list entry */
	$q = sprintf("select count(*) from cms_temp where userkey = '%s'", $key);
	$res = sql_query($q);
	$found = sql_result($res,0);

	if ($found == 1) {
		$q = sprintf("update cms_temp set datetime = %d, ids = '%s' where userkey = '%s'",
			time(), $challenge, $key);
		sql_query($q);
	} else {
		$q = sprintf("delete from cms_temp where userkey = '%s'", $key);
		sql_query($q);

		$q = sprintf("insert into cms_temp (userkey, ids, datetime) values ('%s', '%s', %d)",
			$key, $challenge, time());
		sql_query($q);
	}
	$output = new Layout_output();
	$output->addTag("br");
	$output->addTag("hr");

	$output->addTag("form", array(
		"action"  => "site.php",
		"method"  => "post",
		"target"  => "formhandler",
		"id"      => "formident",
		"enctype" => "multipart/form-data"
	));
	$output->addTag("fieldset");
	$output->addHiddenField("system[pageid]", $pageid);
	$output->addHiddenField("system[activepageid]", $this->pageid);
	$output->addHiddenField("system[challenge]", $challenge);
	if ($this->is_shop)
		$output->addHiddenField("system[is_shop]", 1);
	$output->addHiddenField("mode", "form");

	require("handleFormData.php");

	$t2 = new Layout_table();
	$t2->addTableRow();
		$t2->addTableData();
		$t2->addCode($tbl->generate_output());
		$t2->endTableData();
		$t2->addTableData(array(
			"style" => "padding-left: 10px; vertical-align: bottom;"
			));
			if ($this->cms_license["ideal_type"] == "rabolite" && $this->is_shop) {
				$t2->insertImage("cms/ideal.gif", gettext("iDeal"));
				if ($this->cms_license["ideal_test_mode"])
					$t2->addCode("(test mode!)");
			}
			if ($this->cms_license["ideal_type"] == "ogone") {
				$t2->insertImage("cms/ogone.gif", gettext("Ogone"));
			}
		$t2->endTableData();
	$t2->endTableRow();
	$t2->endTable();


	$output->addCode($t2->generate_output());
	$output->endTag("fieldset");
	$output->endTag("form");

	$output2 = new Layout_output;
	$output2->addTag("iframe", array(
		"id"          => "formhandler",
		"name"        => "formhandler",
		"src"         => "blank.htm",
		"width"       => "100%",
		"frameborder" => 0,
		"border"      => 0,
		"height"      => "35",
		"scrolling"   => "no",
		"allowtransparency" => "allowtransparency"
		//"visibility"  => "xhidden",
	));
	$output2->endTag("iframe");
	
	$output->start_javascript();
	$output->addCode(sprintf('document.write(\'%s\');', addslashes($output2->generate_output())));
	$output->end_javascript();

	$data .= $output->generate_output();

?>
