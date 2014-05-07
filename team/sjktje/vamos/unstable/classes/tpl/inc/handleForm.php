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
		"href" => "javascript: document.getElementById('formident').reset();"));
	$output_nav->addSpace();
	$output_nav->addCode("|");
	$output_nav->addSpace();
	$output_nav->insertTag("a", gettext("send form")." &gt;&gt;", array(
		"href" => "javascript: formsubmit();"));
	$custom_nav = $output_nav->generate_output();

	$short_description = 1;

	$key = sprintf("s:{%s} p:{%s}", session_id(), $pageid);
	$challenge = md5(rand().session_id().mktime());

	/* check for list entry */
	$q = sprintf("select count(*) from cms_temp where userkey = '%s'", $key);
	$res = sql_query($q);
	$found = sql_result($res,0);

	if ($found == 1) {
		$q = sprintf("update cms_temp set datetime = %d, ids = '%s' where userkey = '%s'",
			mktime(), $challenge, $key);
		sql_query($q);
	} else {
		$q = sprintf("delete from cms_temp where userkey = '%s'", $key);
		sql_query($q);

		$q = sprintf("insert into cms_temp (userkey, ids, datetime) values ('%s', '%s', %d)",
			$key, $challenge, mktime());
		sql_query($q);
	}
	$output = new Layout_output();
	$output->addTag("br");
	$output->addTag("hr");

	$output->addTag("form", array(
		"action" => "site.php",
		"method" => "get",
		"target" => "formhandler",
		"id"     => "formident"
	));
	$output->addHiddenField("system[pageid]", $pageid);
	$output->addHiddenField("system[challenge]", $challenge);
	$output->addHiddenField("mode", "form");

	require_once("handleFormData.php");

	$output->addCode($tbl->generate_output());
	$output->endTag("form");

	$output->addTag("iframe", array(
		"id"     => "formhandler",
		"name"   => "formhandler",
		"src"    => "blank.htm",
		"width"  => "0px",
		"frameborder" => 0,
		"border" => 0,
		"height" => "0px;",
		"visiblity" => "hidden"
	));
	$output->endTag("iframe");

	$data .= $output->generate_output();

?>