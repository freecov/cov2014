<?php
/**
 * Covide Groupware-CRM Voip module
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
Class Voip_output {
	const include_dir = "classes/voip/inc/";

	public function showFaxes() {
		$faxdata = new Voip_data();
		$faxinfo = $faxdata->getFaxes();

		$output = new Layout_output();
		$output->layout_page();
			$output->addTag("form", array(
				"id" => "alterfax",
				"method" => "get",
				"action" => "index.php"
			));
			$output->addHiddenField("faxid", "");
			$output->addHiddenField("address_id", "");
			$output->endTag("form");
			$venster = new Layout_venster(array("title" => gettext("faxes")));
			$venster->addVensterData();
				$view = new Layout_view();
				$view->addData($faxinfo["items"]);
				$view->addMapping(gettext("date"), "%human_date");
				$view->addMapping(gettext("faxnumber"), "%sender");
				$view->addMapping(gettext("contact"), "%%relation_name");
				$view->addMapping("", "%%complex_actions");
				$view->defineComplexMapping("relation_name", array(
					array(
						"type"  => "link",
						"link"  => array("index.php?mod=address&action=relcard&id=", "%relation_id"),
						"text"  => "%relation_name",
						"check" => "%relation_id"
					),
					array(
						"type" => "action",
						"src"  => "edit",
						"link" => array("javascript: alter_relation(", "%id", ");")
					)
				));
				$view->defineComplexMapping("complex_actions", array(
					array(
						"type" => "action",
						"src"  => "view",
						"link" => array("javascript: preview_fax(", "%id", ");")
					),
					array(
						"type" => "action",
						"src"  => "file_download",
						"link" => array("javascript: view_fax(", "%id", ");")
					),
					array(
						"type" => "action",
						"src"  => "save",
						"link" => array("javascript: save_fax(", "%id", ");")
					),
					array(
						"type" => "action",
						"src"  => "delete",
						"link" => array("javascript: delete_fax(", "%id", ");")
					)
				));
				$venster->addCode($view->generate_output());
			$venster->endVensterData();
			$output->addCode($venster->generate_output());
			unset($venster);
			$output->load_javascript(self::include_dir."fax_actions.js");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function viewFax() {
		if (!$_REQUEST["faxid"]) {
			die("no fax specified");
		}
		$voipdata = new Voip_data();
		$faxinfo = $voipdata->getFaxFromFS($_REQUEST["faxid"]);

		header('Content-Transfer-Encoding: binary');

		if ($_REQUEST["preview"]) {
			header('Content-Type: image/gif');
			$faxinfo["name"] = "fax.gif";
		} else {
			header('Content-Type: application/pdf');
			if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
				header('Content-Disposition: filename="'.$faxinfo["name"].'"'); //msie 5.5 header bug
			} else {
				header('Content-Disposition: attachment; filename="'.$faxinfo["name"].'"');
			}
		}
		if ($_REQUEST["preview"]) {
			echo $faxinfo["gifdata"];
		} else {
			echo $faxinfo["bindata"];
		}
		exit;
	}

	public function previewFax() {
		$fax = $_REQUEST["faxid"];
		$output = new Layout_output();
		$output->addTag("html");
		$output->addTag("body");
			$output->addTag("img", array(
				"src" => "index.php?mod=voip&amp;action=viewfax&amp;faxid=$fax&amp;preview=1"
			));
		$output->endTag("body");
		$output->endTag("html");
		$output->exit_buffer();
	}
}
?>
