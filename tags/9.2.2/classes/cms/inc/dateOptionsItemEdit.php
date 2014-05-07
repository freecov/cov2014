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
		"subtitle" => gettext("calendar items")
	));

	$cms_data = new Cms_data();
	$cms = $cms_data->getCalendarItems(0, $id);
	$cms = $cms[0];
	$cms["repeating"] = explode("|", $cms["repeating"]);
	if (!$cms['date_begin']) {
		$cms['date_begin'] = mktime(0,0,0,date('m'),date('d'),date('Y'));
	}
	if (!$cms['date_end']) {
		$cms['date_end'] = mktime(0,0,0,date('m'),date('d'),date('Y'));
	}

	$venster->addVensterData();

	$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("start date"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				for ($i=1; $i<=31; $i++) {
					$days[$i] = $i;
				}
				for ($i=1; $i<=12; $i++) {
					$months[$i] = $i;
				}
				for ($i=2000; $i<=date("Y")+5; $i++) {
					$years[$i] = $i;
				}
				for ($i=0; $i<=23; $i++) {
					$hour[$i] = $i;
				}
				for ($i=0; $i<60; $i+=15) {
					$min[$i] = sprintf("%02s", $i);
				}
				$tbl->addTag("span", array(
					"id" => "s_timestamp_layer"
				));
					$tbl->addSelectField("cms[s_timestamp_day]",   $days,   date("d", $cms["date_begin"]));
					$tbl->addSelectField("cms[s_timestamp_month]", $months, date("m", $cms["date_begin"]));
					$tbl->addSelectField("cms[s_timestamp_year]",  $years,  date("Y", $cms["date_begin"]));
					$calendar = new Calendar_output();
					$tbl->addCode( $calendar->show_calendar("document.getElementById('cmss_timestamp_day')", "document.getElementById('cmss_timestamp_month')", "document.getElementById('cmss_timestamp_year')" ));
					$tbl->addSpace();
					$tbl->addSelectField("cms[s_timestamp_hour]",  $hour,  date("H", $cms["date_begin"]));
					$tbl->addCode(":");
					$tbl->addSelectField("cms[s_timestamp_min]",  $min,  date("i", $cms["date_begin"]));

				$tbl->endTag("span");
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("end date"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");

				$tbl->addTag("span", array(
					"id" => "e_timestamp_layer"
				));
					$tbl->addSelectField("cms[e_timestamp_day]",   $days,   date("d", $cms["date_end"]));
					$tbl->addSelectField("cms[e_timestamp_month]", $months, date("m", $cms["date_end"]));
					$tbl->addSelectField("cms[e_timestamp_year]",  $years,  date("Y", $cms["date_end"]));
					$calendar = new Calendar_output();
					$tbl->addCode( $calendar->show_calendar("document.getElementById('cmse_timestamp_day')", "document.getElementById('cmse_timestamp_month')", "document.getElementById('cmse_timestamp_year')" ));
					$tbl->addSpace();
					$tbl->addSelectField("cms[e_timestamp_hour]",  $hour,  date("H", $cms["date_end"]));
					$tbl->addCode(":");
					$tbl->addSelectField("cms[e_timestamp_min]",  $min,  date("i", $cms["date_end"]));


			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("repeating"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$sel = array(
					0 => gettext("every day"),
					1 => gettext("custom")." ..."
				);
				$tbl->addSelectField("cms[repeat_type]", $sel, ($cms["repeating"][0]) ? 1:0);
				$tbl->addTag("div", array(
					"id"    => "repeat_layer",
					"style" => "display: none;"
				));
					$sel = array(
						"ma" => gettext("monday"),
						"di" => gettext("tuesday"),
						"wo" => gettext("wednesday"),
						"do" => gettext("thursday"),
						"vr" => gettext("friday"),
						"za" => gettext("saturday"),
						"zo" => gettext("sunday"),
					);
					$sel2 = array(
						"maand" => gettext("monthly"),
						"jaar"  => gettext("yearly")
					);
					$tbl2 = new Layout_table();
					$tbl2->addTableRow();
						$tbl2->addTableData();
							foreach ($sel as $k=>$v) {
								$tbl2->addCheckBox(sprintf("cms[repeating][%s]", $k), 1,
									(in_array($k, $cms["repeating"])) ? 1:0);
								$tbl2->addSpace();
								$tbl2->addCode($v);
								$tbl2->addTag("br");
							}
						$tbl2->endTableData();
						$tbl2->addTableData(array("valign" => "top"));
							foreach ($sel2 as $k=>$v) {
								$tbl2->addCheckBox(sprintf("cms[repeating][%s]", $k), 1,
									(in_array($k, $cms["repeating"])) ? 1:0);
								$tbl2->addSpace();
								$tbl2->addCode($v);
								$tbl2->addTag("br");
							}
						$tbl2->endTableData();
					$tbl2->endTableRow();
					$tbl2->endTable();
					$tbl->addCode($tbl2->generate_output());
				$tbl->endTag("div");

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode(gettext("description"));
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextArea("cms[description]", $cms["description"], array(
					"style" => "width: 300px; height: 100px;"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
	$tbl->endTable();

	$venster->addCode( $tbl->generate_output() );

	$venster->insertAction("save", gettext("close"), "javascript: saveSettings();");
	#$venster->insertAction("close", gettext("close"), "javascript: window.close();");
	$venster->endVensterData();

	$output->addTag("form", array(
		"action" => "index.php",
		"method" => "post",
		"id"     => "velden"
	));
	$output->addHiddenField("id", $id);
	$output->addHiddenField("pageid", $_REQUEST["pageid"]);
	$output->addHiddenField("mod", "cms");
	$output->addHiddenField("action", "dateOptionsItemSave");
	$output->addCode($venster->generate_output());
	$output->endTag("form");

	$output->load_javascript(self::include_dir."script_cms.js");
	$output->load_javascript(self::include_dir."dateOptionsItemEdit.js");

	$output->layout_page_end();
	$output->exit_buffer();
?>
