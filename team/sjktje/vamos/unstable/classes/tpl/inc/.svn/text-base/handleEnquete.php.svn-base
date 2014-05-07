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

	$avail_fields = $this->cms->getFormData($pageid);
	$hidden = array();
	$avail  = array();
	foreach ($avail_fields as $k=>$v) {
		if ($v["field_type"] == "hidden") {
			$hidden[] = $v;
			if ($v["field_name"] == "resulturl") {
				$resulturl = $v["field_value"];
			}
		} else {
			$avail[] = $v;
		}
	}
	if (!$resulturl)
		$resulturl = "/";


	$total = count($avail);

	$current = (int)$_REQUEST["step"];
	$forms[0] = $avail[$current];

	/* if no visitor id, generate one */
	$visitor_id = $_REQUEST["visitor_id"];
	if (is_array($_REQUEST["data"])) {
		if (!$visitor_id) {
			$visitor_id = $this->createVistorRecord($this->pageid);
			/* insert the hidden fields */
			#foreach ($hidden as $k=>$v) {
			#	$this->updateFieldRecord($this->pageid, $v["field_name"], $v["field_value"], $visitor_id);
			#}
		}
		foreach ($_REQUEST["data"] as $k=>$v) {
			$this->updateFieldRecord($this->pageid, $k, $v, $visitor_id);
		}
	}


	$output_nav = new Layout_output();
	if ($current > 0) {
		$output_nav->insertTag("a", gettext("previous"), array(
			"href" => ""));
		$output_nav->addSpace();
		$output_nav->addCode("|");
		$output_nav->addSpace();
	}
	if ($current == $total-1) {
		$output_nav->insertTag("a", gettext("finish enquete")." &gt;&gt;", array(
			"href" => $resulturl));
	} else {
		$output_nav->insertTag("a", gettext("next question")." &gt;&gt;", array(
			"href" => "javascript: document.getElementById('formident').submit();"));
	}
	$custom_nav = $output_nav->generate_output();

	$output = new Layout_output();
	$output->addTag("br");

	$output->addTag("form", array(
		"action" => sprintf("/enquete/%d#enquete", $pageid),
		"method" => "get",
		"id"     => "formident"
	));
	$output->addHiddenField("current",    (int)$current);
	$output->addHiddenField("step",       (int)($current+1));
	$output->addHiddenField("visitor_id", (int)$visitor_id);
	$output->addHiddenField("mode",       $_REQUEST["mode"]);

	$output->addTag("hr");
	$output->insertTag("a", "", array(
		"name" => "enquete"
	));

	$output->insertAction("view_all", gettext("Enquete"), "");
	$output->addSpace();
	$output->addTag("b");
	$output->addCode(gettext("Enquete step")." ");
	$output->addCode($current+1);
	$output->addCode(" ".gettext("of")." ");
	$output->addCode($total);
	$output->endTag("b");
	$output->addTag("br");
	$output->addTag("br");

	require_once("handleFormData.php");

	$output->addCode($tbl->generate_output());
	$output->endTag("form");

	$output->start_javascript();
		$output->addCode(" document.getElementById('formident').elements[0].focus(); ");
	$output->end_javascript();


	$data .= $output->generate_output();

?>