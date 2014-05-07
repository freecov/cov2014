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
	$fields = $this->address_fields;

	$output->start_javascript();
	$output->addCode("
		function formsubmit() {
			var stop = 0;
	");
	foreach ($forms as $formitem) {
		if ($formitem["field_type"] != "hidden" && $formitem["is_required"]) {
			if ($formitem["field_type"] == "address") {
				foreach ($fields as $fld=>$name) {
					if ($fld == "email") {
						$echeck[1] = "echeck(";
						$echeck[2] = ")";
					} else {
						$echeck[1] = "";
						$echeck[2] = "";
					}
					$output->addCode(sprintf("
						if (stop == 0 && !%sdocument.getElementById('%s').value%s) { var stop = 1; alert('%s: %s'); } ",
						$echeck[1],
						preg_replace("/(\[)|(\])|( )/s", "", sprintf("data[%s][%s]",
							$formitem["field_name"], $fld)),
						$echeck[2],
						addslashes(gettext(($echeck[1]) ? "no valid email address":"no values for")),
						addslashes($formitem["field_name"]." > ".addslashes(gettext($name)))
					));
				}
			} else if ($formitem["field_type"] != "checkbox") {
				/* textfield, textarea, dropdown */
				if ($formitem["is_mailto"] || $formitem["is_mailfrom"]) {
					$echeck[1] = "echeck(";
					$echeck[2] = ")";
				} else {
					$echeck[1] = "";
					$echeck[2] = "";
				}

				$output->addCode(sprintf("
					if (stop == 0 && !%sdocument.getElementById('%s').value%s) { var stop = 1; alert('%s: %s'); } ",
					$echeck[1],
					preg_replace("/(\[)|(\])|( )/s", "", sprintf("data[%s]", $formitem["field_name"])),
					$echeck[2],
					addslashes(gettext(($echeck[1]) ? "no valid email address":"no values for")),
					addslashes($formitem["field_name"])
				));
			} else {
				/* check box */
				$sel = explode("\n", $formitem["field_value"]);
				foreach ($sel as $k=>$v) {
					$sel[$k] = sprintf("document.getElementById('%s').checked",
						preg_replace("/(\[)|(\])|( )/s", "", sprintf("data[%s][%s]", $formitem["field_name"], $k)));
				}
				$output->addCode(sprintf("
					if (!(%s)) { var stop = 1; alert('%s: %s'); }
					",
					preg_replace("/(\r)|(\t)|(\n)/s", "", implode(" || ", $sel)),
					addslashes(gettext("no values for")),
					addslashes($formitem["field_name"])
				));
			}
		}
	}

	$output->addCode("
			if (stop == 0) {
				document.getElementById('form_submit_link').style.visibility = 'hidden';
				document.getElementById('formident').submit();
			}
		}
	");
	$output->end_javascript();

	$tbl = new Layout_table();
	foreach ($forms as $formitem) {
		if ($formitem["field_type"] != "hidden") {
			if ($short_description && $formitem["description"]) {
				$tbl->addTableRow();
					$tbl->addTableData();
						$tbl->addSpace();
					$tbl->endTableData();
					$tbl->addTableData(array("style" => "vertical-align: top;"));
						$tbl->addTag("br");
						$tbl->insertTag("i", nl2br($formitem["description"]));
					$tbl->endTableData();
				$tbl->endTableRow();
			} elseif ($formitem["description"]) {
				$tbl->addTableRow();
					$tbl->addTableData(array("style" => "vertical-align: top;", "colspan" => 2));
						$tbl->addCode(nl2br($formitem["description"]));
						$tbl->addTag("br");
						$tbl->addTag("br");
					$tbl->endTableData();
				$tbl->endTableRow();
			}

			$tbl->addTableRow();
				$tbl->addTableData(array("style" => "vertical-align: top;"));
					$tbl->addCode($formitem["field_name"]);

					$i=0;
					if ($formitem["is_required"]) {
						$tbl->addCode("*");
						$i++;
					}
					$tbl->addCode(":");
					$tbl->addSpace(2);
					if ($formitem["field_type"] == "upload") {
						$max_filesize = ini_get('upload_max_filesize');
						$max_fs = $max_filesize;
						if (!is_numeric($max_filesize)) {
							$multipl = strtolower(substr($max_filesize, -1));
							$amount = substr($max_filesize, 0, strlen($max_filesize)-1);
							switch ($multipl) {
								case "m" :
									$max_filesize = 1024*1024*$amount;
									break;
								case "k" :
									$max_filesize = 1024*$amount;
									break;
								case "g" :
									$max_filesize = 1024*1024*1024*$amount;
									break;
								default :
									$max_filesize = 50*1024*1024;
									break;
							}
						}
						$tbl->addTag("br");
						$tbl->addCode("(max: $max_fs)");
					}
				$tbl->endTableData();
				$tbl->addTableData();
					switch ($formitem["field_type"]) {
						case "text":
							$tbl->addTextField(sprintf("data[%s]", $formitem["field_name"]), $formitem["field_value"], array(
								"style" => "text-align: left;"
							));
							break;
						case "upload":
								$tbl->addHiddenField("MAX_FILE_SIZE", $max_filesize);
								$tbl->addTag("div", array("id"=>"uploadcode") );
								$tbl->addUploadField("binFile[]", array(
									"size" => "20",
									"class" => "inputtext"

								));
								$tbl->addTag("br");
								$tbl->endTag("div");
								$tbl->addTag("div", array("id"=>"moreuploadcode") );
								$tbl->endTag("div");

								$tbl->addTag("span", array("id"=>"upload_controls") );
								$tbl->insertTag("a", " + ".gettext("add another attachment"),
									array("href" => "javascript: add_upload_field();"));

								$tbl->endTag("span");
								$tbl->addTag("span", array("id"=>"upload_msg", "style"=>"visibility: hidden") );
									$tbl->insertTag("b", gettext("uploading")." ...");
								$tbl->endTag("span");
							break;
						case "textarea":
							$tbl->addTextArea(sprintf("data[%s]", $formitem["field_name"]), $formitem["field_value"], array(
								"style" => "text-align: left;"
							));
							break;
						case "select":
							$seltemp = explode("\n", $formitem["field_value"]);
							$sel     = array();
							foreach ($seltemp as $vv) {
								$sel[$vv] = $vv;
							}
							$tbl->addSelectField(sprintf("data[%s]", $formitem["field_name"]), $sel, array(
								"style" => "text-align: left;"
							));
							break;
						case "checkbox":
							$sel = explode("\n", $formitem["field_value"]);
							foreach ($sel as $k=>$v) {
								$tbl->addCheckBox(sprintf("data[%s][%s]", $formitem["field_name"], $k), $v, 0);
								$tbl->addSpace();
								$tbl->addCode($v);
								$tbl->addTag("br");
							}
							break;
						case "date":
							if (!$calendar)
								$calendar = New Calendar_output();

							$tbl->addHiddenField(sprintf("hdata[%s]", $formitem["field_name"]), 1);
							$sel = array();
							for ($i=1;$i<=31;$i++)
								$sel[$i] = $i;
							$tbl->addSelectField(sprintf("data[%s][d]", $formitem["field_name"]), $sel, date("d"));

							$sel = array();
							for ($i=1;$i<=12;$i++)
								$sel[$i] = $i;
							$tbl->addSelectField(sprintf("data[%s][m]", $formitem["field_name"]), $sel, date("m"));

							$sel = array();
							for ($i=date("Y");$i<=date("Y")+10;$i++)
								$sel[$i] = $i;
							$tbl->addSelectField(sprintf("data[%s][y]", $formitem["field_name"]), $sel, date("Y"));

							$tbl->addCode($calendar->show_calendar(
								sprintf("document.getElementById('data%sd')", preg_replace("/(\[)|(\])|( )/s", "", $formitem["field_name"])),
								sprintf("document.getElementById('data%sm')", preg_replace("/(\[)|(\])|( )/s", "", $formitem["field_name"])),
								sprintf("document.getElementById('data%sy')", preg_replace("/(\[)|(\])|( )/s", "", $formitem["field_name"]))
							));

							break;
						case "datetime":
							if (!$calendar)
								$calendar = New Calendar_output();

							$tbl->addHiddenField(sprintf("hdata[%s]", $formitem["field_name"]), 1);

							$sel = array();
							for ($i=1;$i<=31;$i++)
								$sel[$i] = $i;
							$tbl->addSelectField(sprintf("data[%s][d]", $formitem["field_name"]), $sel, date("d"));

							$sel = array();
							for ($i=1;$i<=12;$i++)
								$sel[$i] = $i;
							$tbl->addSelectField(sprintf("data[%s][m]", $formitem["field_name"]), $sel, date("m"));

							$sel = array();
							for ($i=date("Y");$i<=date("Y")+10;$i++)
								$sel[$i] = $i;
							$tbl->addSelectField(sprintf("data[%s][y]", $formitem["field_name"]), $sel, date("Y"));

							$tbl->addCode($calendar->show_calendar(
								sprintf("document.getElementById('data%sd')", preg_replace("/(\[)|(\])|( )/s", "", $formitem["field_name"])),
								sprintf("document.getElementById('data%sm')", preg_replace("/(\[)|(\])|( )/s", "", $formitem["field_name"])),
								sprintf("document.getElementById('data%sy')", preg_replace("/(\[)|(\])|( )/s", "", $formitem["field_name"]))
							));

							$tbl->addSpace(3);
							$sel = array();
							for ($i=0;$i<24;$i++)
								$sel[$i] = $i;
							$tbl->addSelectField(sprintf("data[%s][h]", $formitem["field_name"]), $sel, date("H"));
							$tbl->addCode(":");
							$tbl->addSpace();
							$sel = array();
							for ($i=0;$i<60;$i+=5) {
								if ($i < 10)
									$i = "0".$i;

								$sel[(int)$i] = $i;
							}
							$tbl->addSelectField(sprintf("data[%s][i]", $formitem["field_name"]), $sel, 0);
							break;
						case "address":
							$tbl->addHiddenField(sprintf("data[%s]", $formitem["field_name"]), 1);
							$tbl->addSpace();
							$tbl->endTableData();
							// start address block
							if ($_SESSION["visitor_id"]) {
								$user = $this->cms->getAccountList($_SESSION["visitor_id"]);
								$user = $user[0];
								if ($user["address_id"]) {
									$address_data = new Address_data();
									$user["address"] = $address_data->getAddressById($user["address_id"]);
								}
								$user["address"]["email"] = $user["email"];
							}
							foreach ($fields as $fld=>$name) {
								$z++;
								$tbl->endTableRow();
								$tbl->addTableRow();
									$tbl->insertTableData("- ".gettext($name).":");
									$tbl->addTableData();
										$tbl->addTextField(sprintf("data[%s][%s]",
											$formitem["field_name"], $fld), $user["address"][$fld]);

									if (count($fields) < $z)
										$tbl->endTableData();
							}
							break;
					}

				$tbl->endTableData();
			$tbl->endTableRow();
		}
	}
	$tbl->addTableRow();
		$tbl->addTableData(array(
			"style"   => "text-align: left; padding-top: 20px; vertical-align: top;"
		));
			if ($i>0) {
				$tbl->addCode("* = ".gettext("mandatory"));
				$tbl->addSpace(2);
			}
		$tbl->endTableData();
		$tbl->addTableData(array(
			"style"   => "text-align: right; padding-top: 20px;"
		));
			$tbl->addCode($custom_nav);
		$tbl->endTableData();
	$tbl->endTableRow();
	$tbl->endTable();
?>