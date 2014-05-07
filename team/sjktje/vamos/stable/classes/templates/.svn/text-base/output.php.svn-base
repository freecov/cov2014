<?php
/**
 * Covide Groupware-CRM Sales output class
 *
 * Covide Groupware-CRM is the solutions for all groups off people
 * that want the most efficient way to work to together.
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @author Michiel van Baak <mvanbaak@users.sourceforge.net>
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Templates_output {

	/* constants */
	const include_dir =  "classes/templates/inc/";

	/* variables */

	/* methods */
	public function calibratePrinter() {
		$output = new Layout_output();
		$output->addTag("html", array(
			"style" => "height: 100%; width: 100%;"
		));
		$output->addTag("head");
			$output->addTag("style", array(
				"type" => "text/css"
			));
			$output->addCode("
				@page {
					size: A4;
				}
			");
			$output->endTag("style");
		$output->endTag("head");
		$output->addTag("body", array(
			"style" => "height: 20cm; width: 15cm; margin: 0px;"
		));

		$output->insertTag("div", "
			<font face='arial' size='2'>
			<div style='margin-left: 8cm'>&lt; top &gt;</div>
			<br><br>
			<div style='margin-left: 4px;'>
			<b>This is the Covide Template Calibration Page.</b><br><br>
			Please print this test page on your printer.<br>
			You can use this page to determine your printer offset margins.<br><br><br>

			<b>Dit is de Covide Template Calibratie Pagina.</b><br><br>
			U kunt deze pagina afdrukken op uw printer om op deze manier de marges van uw printer te bepalen.<br>
			<br><br><br>
			</div>

			<div style='text-align: center; width: 10px;'>
				l<br>e<br>f<br>t
			</div>
			<br><br>
			<div style='width: 1cm; height: 1cm; padding: 2px; border: 2px solid black; margin-left: 20px;'>1x1 cm</div>
			<div style='width: 3cm; height: 3cm; padding: 2px; border: 2px solid black; margin-left: 20px; margin-top: 10px;'>3x3cm</div>
			</font>


			", array(
			"style" => "width: 100%; height: 100%; border-top: 2px solid black; border-left: 2px solid black;"
		));

		$output->endTag("body");
		$output->endTag("html");
		$output->exit_buffer();

	}

	public function templatePrint($pdf=0) {

		$output = new Layout_output();
		$output->addTag("html");

		/* get id */
		$id = $_REQUEST["id"];

		/* create objects */
		$address_data = new Address_data();
		$template_data = new Templates_data();

		/* retrieve data and settings */
		$data = $template_data->getTemplateById($id);
		$settings = $template_data->getTemplateSettingById($data["settings_id"]);

		$settings["address_top"]  = number_format($settings["address_top"],1);
		$settings["address_left"] = number_format($settings["address_left"],1);
		$settings["page_top"]     = number_format($settings["page_top"],1);
		$settings["page_left"]    = number_format($settings["page_left"],1);
		$settings["page_right"]   = number_format($settings["page_right"],1);

		/* note: */
		/*
			We use html2pdf to generate pdf pages.
			Html2Pdf doesn't support complex css markup, so we have
			to use simple html tags. The browser print and pdf print
			use the same html code to print
		*/
		if ($_REQUEST["pdf"]) {
			switch ($data["fontsize"]) {
				case 1: $fsize = "11pt";  break;
				case 2: $fsize = "13pt"; break;
				case 3: $fsize = "15pt"; break;
				case 4: $fsize = "18pt"; break;
				case 5: $fsize = "21pt"; break;
				case 6: $fsize = "27pt"; break;
				case 7: $fsize = "39pt"; break;
			}
		} else {
			switch ($data["fontsize"]) {
				case 1: $fsize = "8pt";  break;
				case 2: $fsize = "10pt"; break;
				case 3: $fsize = "12pt"; break;
				case 4: $fsize = "14pt"; break;
				case 5: $fsize = "18pt"; break;
				case 6: $fsize = "24pt"; break;
				case 7: $fsize = "36pt"; break;
			}
		}
		$conv_table = array(
			"arial,serif"       => "Arial",
			"courier,monospace" => "Courier",
			"georgia,serif"     => "Georgia",
			"tahoma,serif"      => "Tahoma",
			"times,serif"       => "Times",
			"verdana,serif"     => "Verdana",
			"palatino linotype,serif" => "Palatino"
		);
		$data["font"] = $conv_table[$data["font"]];
		$data["body"] = preg_replace("/(<font[^>]*?>)|(<\/font[^>]*?>)/si", "", $data["body"]);

		$output->addTag("head");
			$output->addTag("style", array(
				"type" => "text/css"
			));
			$output->addCode("
				body, td, p, a, span, div {
					font-family: ".$data["font"].";
					font-size: ".$fsize.";
				}
				@page {
					size: A4;
					".sprintf("
						page-break-inside: auto;
						margin-left: %scm;
						margin-top: %scm;
						margin-right: %scm",
							$settings["page_left"],
							$settings["page_top"],
							$settings["page_right"])."
				}
				@media screen {
					.container {
						".sprintf("
						page-break-inside: auto;
						margin-left: %scm;
						margin-top: %scm;
						margin-right: %scm",
							$settings["page_left"],
							$settings["page_top"],
							$settings["page_right"])."
					}
					html {
						height: 100%;
						width: 100%;
					}
					body {
						height: 100%;
						width: 100%;
						margin: 0px;
					}
					div.content_data, div.address_data, div.container {
						width: 100%;
					}
				}
			");
			$output->endTag("style");
		$output->endTag("head");
		$output->addTag("body");

		$file = $template_data->getTemplateFile($settings["id"]);
		if ($file["id"]) {
			$img = $GLOBALS["covide"]->webroot."/index.php?mod=templates&action=view_file&dl=1&id=".$file["id"];
			$img = preg_replace("/^http(s){0,1}:\/\//si", "", $img);
			$img = "http://".preg_replace("/\/{1,}/s", "/", $img);
		}


		$ids = explode(",", $data["ids"]);
		foreach ($ids as $k=>$v) {
			/* create pages */
			$output->addTag("div", array(
				"class"    => "container",
				"style"    => "page-break-after: always"
			));

				$output->addTag("div", array(
					"class"    => "address_data",
					"width"    => "100%"
				));

				$tbl = new Layout_table(array(
					"cellspacing" => 0,
					"cellpadding" => 0,
					"border"      => 0,
					"width"       => "100%"
				));
				$tbl->addTableRow();
					$tbl->addTableData();
						if (!$_REQUEST["pdf"]) {
							$tbl->addTag("img", array(
								"width"  => $settings["address_left"]."cm",
								"height" => $settings["address_top"]."cm",
								"src"    => "http://covide.atreides.aol/img/null.gif"
							));
						} elseif ($_REQUEST["pdf"]) {
							//FIXME: very ugly code
							for ($i=0;$i<=$settings["address_left"]*0.7;$i++) {
								$tbl->addTag("img", array(
									"src"    => "http://covide.atreides.aol/img/null.gif"
								));
							}
						}
					$tbl->endTableData();
				$tbl->endTableRow();
				$tbl->addTableRow();
					/* position = right */
					if ($settings["address_position"]) {
						$tbl->addTableData();
						if ($img) {
							$tbl->addTag("img", array(
								"src" => $img
							));
						}
						$tbl->endTableData();
					} elseif ($_REQUEST["pdf"]) {
						//FIXME: another hack
						$tbl->addTableData();
							for ($i=0;$i<=$settings["address_left"]*0.7;$i++) {
								$tbl->addTag("img", array(
									"src"    => "http://covide.atreides.aol/img/null.gif"
								));
							}								
						$tbl->endTableData();
					}
					$tbl->addTableData(array(
						"align" => ($settings["address_position"]) ? "left":"left"
					));
						/* begin address header */
						$table = new Layout_table();
							$table->addTableRow();
							$table->addTableData(array("align"=>"left"));

								$table->addTag("font", array(
									"face" => $data["font"],
									"size" => $data["fontsize"]
								));

								if ($tpl["address_businesscard_id"]==0) {
									$tt = "address";
								} elseif ($tpl["address_businesscard_id"]==1) {
									$tt = "bcards";
								} else {
									$tt = "both";
								}
								$vv = preg_replace("/[^0-9]/s", "", $v);
								$address = $address_data->getAddressByID($vv, $tt);

								/* company name */
								if ($address["companyname"]) {
									$table->addCode( $address["companyname"] );
									$table->addTag("br");
								}
								/* person */
								$table->addCode( gettext("T.a.v.") . " ");
								$table->addCode( $address["tav"] );
								$table->addTag("br");

								/* address */
								$table->addCode( $address["address"] );
								$table->addTag("br");

								/* zipcode and city */
								$table->addCode( $address["zipcode"] . " ");
								$table->addCode( $address["city"] );

								/* fax */
								if ($data["fax_nr"]) {
									$table->addTag("br");
									$table->addCode( gettext("Fax") . $address["fax_nr"]);
								}

								$table->endTag("font");

						$table->endTableData();
						$table->endTableData();
						$table->endTable();

						$tbl->addCode( $table->generate_output() );


						/* end address header */
					$tbl->endTableData();
					/* if position = left */
					if (!$settings["address_position"] && $img) {
						$tbl->addTableData(array("align"=>"right"));
							$tbl->addTag("img", array(
								"src"   => $img,
								"align" => "right"
							));
						$tbl->endTableData();
					}
				$tbl->endTableRow();
				$tbl->endTable();

				$output->addCode ($tbl->generate_output() );
				$output->endTag("div");



				$output->addSpace();
				$output->addTag("br");
				$output->addSpace();
				$output->addTag("br");

				/* letter contents */
				$output->addTag("div", array(
					"class"    => "content_data"
				));
				$output->addTag("font", array(
					"face" => $data["font"],
					"size" => $data["fontsize"]
				));

				$output->addCode( $data["city"] . ", " . $data["date"] );
				$output->addSpace();
				$output->addTag("br");
				$output->addSpace();
				$output->addTag("br");
				$output->addSpace();
				$output->addTag("br");
				$output->addCode( gettext("Betreft") . ": " . $data["description"] );
				$output->addSpace();
				$output->addTag("br");
				$output->addSpace();
				$output->addTag("br");
				$output->addSpace();
				$output->addTag("br");
				$output->addCode( $address["contact_person"] . "," );
				$output->addTag("br");
				$output->addCode( preg_replace("/(<br[^>]*?>)/si", "&nbsp;$1", $data["body"]) );

				$output->endTag("font");
				$output->endTag("div");

			$output->endTag("div");
			$output->addCode("<!--NewPage-->");
		}

		$output->endTag("body");
		$output->endTag("html");

		session_write_close();

		if (!$_REQUEST["pdf"]) {
			$output->start_javascript();
				$output->addCode("window.print();");
			$output->end_javascript();

			$output->exit_buffer();
		} else {
			$dir = $GLOBALS["covide"]->temppath;
			$file = $dir."pdf_".md5(mktime()*rand()).".html";
			$pdf = $dir."pdf_".md5(mktime()*rand()).".pdf";

		$out = fopen($file, "w");
		$buffer = $output->generate_output();
		//FIXME: need a pdf library that support unicode 
		$buffer = mb_convert_encoding($buffer, "ISO-8859-1", "UTF-8");
		fwrite( $out, $buffer );
		fclose($out);

		require_once('classes/html2pdf/pdf.php');

		createPdf($file, $_SERVER["SERVER_NAME"], $pdf, $settings["footer_position"], $settings["footer_text"]);

		#file download
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: application/pdf');

		if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
			header('Content-Disposition: filename="covide_template.pdf"'); //msie 5.5 header bug
		}else{
			header('Content-Disposition: attachment; filename="covide_template.pdf"');
		}

		$handle = fopen ($pdf, "r");
		$pdfdata = fread ($handle, filesize ($pdf));
		fclose ($handle);

		print($pdfdata);

		@unlink($file);
		@unlink($pdf);

		exit();
		}
	}

	public function selectAddress(){
		$address_data = new Address_data();
		$list = $address_data->getRelationsList( array("addresstype"=>$_REQUEST["addresstype"], "nolimit"=>1 ));

		$data = array();
		foreach ($list["address"] as $k=>$v) {
			$type = substr($k, 0, 1);
			switch ($type) {
			case "B":
				$data["B".$v["id"]] = $v["fullname"];
				if ($v["companyname"]) {
					$data["B".$v["id"]].= sprintf(" (%s)", $v["companyname"]);
				}
				break;
			case "R":
				$data["R".$v["id"]] = $v["companyname"];
				break;
			default:
				if ($_REQUEST["addresstype"] == "bcards") {
					$data[$v["id"]] = $v["fullname"];
					if ($v["companyname"]) {
						$data["B".$v["id"]].= sprintf(" (%s)", $v["companyname"]);
					}
				} else {
					$data["R".$v["id"]] = $v["companyname"];
				}
			}
		}
		natcasesort($data);

		$ids = array();
		//$names = array();

		foreach ($data as $k=>$v) {
			$ids[] = $k;
			//$names[] = $v;
		}

		$output = new Layout_output();
		$output->layout_page("templates", 1);

		$output->addTag("form", array(
			"name"   => "velden",
			"action" => "index.php"
		));

		$cla_positive = str_replace("|", ",", $_REQUEST["classifications"]["positive"]);
		$cla_negative = str_replace("|", ",", $_REQUEST["classifications"]["negative"]);

		$output->addHiddenField("ids", implode(",", $ids));
		//$output->addHiddenField("names", implode(", ", $names));
		$output->addHiddenField("addresstype", $_REQUEST["addresstype"]);
		$output->addHiddenField("classification", $cla_positive);
		$output->addHiddenField("negative_classification", $cla_negative);
		$output->addHiddenField("and_or", $_REQUEST["selectiontype"]);

		$output->endTag("form");
		$output->load_javascript(self::include_dir."templates.js");

		$output->start_javascript();
			$output->addCode("
				init_address_selection();
			");
		$output->end_javascript();


		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function selectCla() {
		$output = new Layout_output();
		$output->layout_page("templates", 1);

		$settings = array(
			"title"    => gettext("Templates"),
			"subtitle" => gettext("pick classification(s)")
		);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "templates");
		$output->addHiddenField("action", "selectAddress");
		$output->addHiddenField("target_type", $_REQUEST["target_type"]);


		$classification = new Classification_output();

		$output_alt = new Layout_output();
		$output_alt->insertAction("back", gettext("back"), "javascript: window.close();");
		$output_alt->insertAction("forward", gettext("next"), "javascript: document.getElementById('velden').submit();");

		$venster = new Layout_venster($settings, 1);
		$venster->addVensterData();
			$venster->addCode( $classification->select_classification("", $output_alt->generate_output(), 1) );
		$venster->endVensterData();

		$placeholder = new Layout_table();
		$output->addCode ( $placeholder->createEmptyTable($venster->generate_output()) );

		$output->layout_page_end();
		$output->exit_buffer();

	}


	public function templateEdit($id=0) {
		$output = new Layout_output();
		$output->layout_page(gettext("templates"), 1);

		if ($_REQUEST["id"]) {
			$id = $_REQUEST["id"];
		}

		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "POST",
			"action" => "index.php",
			"target" => "_self"
		));
		$output->addHiddenField("mod", $_REQUEST["mod"]);
		$output->addHiddenField("id", $id);
		$output->addHiddenField("start", (int)$_REQUEST["start"]);
		$output->addHiddenField("action", "save");
		$output->addHiddenField("pdf", 0);
		$output->addHiddenField("dl", 0);

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("templates"),
			"subtitle" => gettext("start")
		));
		$venster->addVensterData();

		$templates_data = new Templates_data();
		$data = $templates_data->getTemplateSettings();

		if ($id) {
			$tpl = $templates_data->getTemplateById($id);
		}

		$tbl = new Layout_table( array(
			"cellspacing" => 1,
			"cellpadding" => 1
		));
		/* page settings */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("page settings") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addSelectField("tpl[settings_id]", $data, $tpl["settings_id"]);
			$tbl->endTableData();
		$tbl->endTableRow();
		/* show fax number */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("faxnumber") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCheckbox("tpl[fax_nr]", "1", $tpl["fax_nr"]);
				$tbl->addSpace();
				$tbl->addCode( gettext("show faxnumber") );
			$tbl->endTableData();
		$tbl->endTableRow();
		/* subject */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("concerns") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("tpl[description]", $tpl["description"], array("style"=>"width: 300px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		/* plaats en datum */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("city")." / ".gettext("date") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("tpl[city]", $tpl["city"], array("style"=>"width: 158px"));
				$tbl->addTextField("tpl[date]", $tpl["date"], array("style"=>"width: 140px"));
			$tbl->endTableData();
		$tbl->endTableRow();

		/* destination */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("receipients") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				/* address selection */
				$tbl->addHiddenField("tpl[address_businesscard_id]", $tpl["address_businesscard_id"]);
				$tbl->addHiddenField("tpl[classification]", $tpl["classification"]);
				$tbl->addHiddenField("tpl[negative_classification]", $tpl["negative_classification"]);
				$tbl->addHiddenField("tpl[and_or]", $tpl["and_or"]);
				$tbl->addHiddenField("tpl[ids]", $tpl["ids"]);

				/* if the selection is classification based */
				if ($tpl["and_or"]) {

					if ($tpl["address_businesscard_id"]==0) {
						$tt = "address";
					} elseif ($tpl["address_businesscard_id"]==1) {
						$tt = "bcards";
					} else {
						$tt = "both";
					}
					$opts["cla"]["selectiontype"] = strtolower($tpl["and_or"]);
					$opts["cla"]["classifications"]["positive"] = str_replace(",", "|", $tpl["classification"]);
					$opts["cla"]["classifications"]["negative"] = str_replace(",", "|", $tpl["negative_classification"]);
					$opts["addresstype"] = $tt;
					$opts["nolimit"] = 1;

					$address_data = new Address_data();

					$list = $address_data->getRelationsList($opts);

					$ids = array();
					foreach ($list["address"] as $k=>$v) {
						$ids[]=$k;
					}
					$tpl["ids"] = implode(",",$ids);

					/* update the current selection in the database */

					$templates_data->templateSaveSelection(implode(",", $ids), $tpl["id"]);
				}

				$ids = explode(",", $tpl["ids"]);

				$names = array();
				if (!$address_data) $address_data = new Address_data();

				foreach ($ids as $k=>$v) {
					if ($v) {
						$type = substr($v, 0, 1);
						$vv = preg_replace("/[^0-9]/s", "", $v);
						switch ($type) {
							case "B":
								$tmp = $address_data->getAddressByID($vv, "bcards");
								if ($tmp["companyname"]) {
									$names[] = $tmp["fullname"]." (".$tmp["companyname"].")";
								} else {
									$names[] = $tmp["fullname"];
								}
								break;
							case "R":
								$tmp = $address_data->getAddressByID($vv, "address");
								$names[] = $tmp["companyname"];
								break;
							default:
								if ($tpl["address_businesscard_id"]) {
									$tmp = $address_data->getAddressByID($vv, "bcards");
									if ($tmp["companyname"]) {
										$names[] = $tmp["fullname"]." (".$tmp["companyname"].")";
									} else {
										$names[] = $tmp["fullname"];
									}
								} else {
									$tmp = $address_data->getAddressByID($vv, "address");
									$names[] = $tmp["companyname"];
								}
						}
					}
				}
				$names = implode(", ", $names);
				if ($tpl["and_or"]) {
					$names = gettext("by classifications").": ".$names;
				} else {
					$names = gettext("by addresses").": ".$names;
				}

				$tbl->insertTag("div", $names, array(
					"style" => "width: 700px; height: 120px; border: 1px solid black; overflow: auto;",
					"id"    => "address_view"
				));
				if ($tpl["address_businesscard_id"]==0) {
					$tt = "relations";
				} elseif ($tpl["address_businesscard_id"]==1) {
					$tt = "bcards";
				} else {
					$tt = "both";
				}
				$uri = "&addresstype=".$tt;
				$uri.= "&selectiontype=".strtolower($tpl["and_or"]);
				$uri.= "&classifications[positive]=".str_replace(",", "|", $tpl["classification"]);
				$uri.= "&classifications[negative]=".str_replace(",", "|", $tpl["negative_classification"]);

				$tbl->insertAction("addressbook", gettext("pick addresses"), "javascript: popup('?mod=address&action=searchRel', 'search_address');");
				$tbl->insertAction("state_special", gettext("pick classifications"), "javascript: popup('?mod=templates&action=selectCla&$uri', 'search_address', 450, 500);");
			$tbl->endTableData();
		$tbl->endTableRow();
		/* default fonts */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("standard font") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");

				$tbl->addSelectField("tpl[font]", array(
					"arial,serif"       => gettext("Arial"),
					"courier,monospace" => gettext("Courier New"),
					"georgia,serif"     => gettext("Georgia"),
					"tahoma,serif"      => gettext("Tahoma"),
					"times,serif"       => gettext("Times new roman"),
					"verdana,serif"     => gettext("Verdana"),
					"palatino linotype,serif" => gettext("Palatino Linotype")
				), $tpl["font"]);
				$tbl->addSelectField("tpl[fontsize]", array(
					"1" => "1 (8pt)",
					"2" => "2 (10pt)",
					"3" => "3 (12pt)",
					"4" => "4 (14pt)",
					"5" => "5 (18pt)",
					"6" => "6 (24pt)",
					"7" => "7 (36pt)"
				), $tpl["fontsize"]);

				$tbl->addCode(" ".gettext(" * this font will be forced in the letter.") );
			$tbl->endTableData();
		$tbl->endTableRow();
		/* contents */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("content of letter") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextArea("contents", $tpl["body"], array(
				"style" => "width: 700px; height: 400px;"
			));
			$editor = new Layout_editor();
			$tbl->addCode( $editor->generate_editor() );

			$tbl->endTableData();
		$tbl->endTableRow();

		/* last line */
		/*
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("last sentence") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextField("tpl[footer]", $tpl["footer"], array("style"=>"width: 300px"));
			$tbl->endTableData();
		$tbl->endTableRow();
		*/
		/* leave space for signature */
		/*
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("signature") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addCheckbox("tpl[signature]", "1", $tpl["signature"]);
				$tbl->addSpace();
				$tbl->addCode( gettext("leave room for autograph") );
			$tbl->endTableData();
		$tbl->endTableRow();
		*/
		/* sender data */
		/*
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("sender data") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextArea("tpl[sender]", $tpl["sender"], array(
					"style" => "width: 300px; height: 80px; font-family:"
				));
			$tbl->endTableData();
		$tbl->endTableRow();
		*/

		/* actions */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("close", gettext("close window"), "javascript: window.close();");
				$tbl->insertAction("save", gettext("save"), "javascript: save();");
				$tbl->addSpace();
				if ($id) {
					$tbl->insertAction("print", gettext("print"), "javascript: printTemplate(0);");
					$tbl->insertAction("ftype_pdf", gettext("export as PDF"), "javascript: printTemplate(1);");
				}

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode( $tbl->generate_output() );

		$venster->endVensterData();

		$output->load_javascript(self::include_dir."templates.js");

		$output->addCode( $venster->generate_output() );
		$output->endTag("form");

		$output->layout_page_end();
		$output->exit_buffer();

	}

	public function show_welcome() {

		$output = new Layout_output();
		$output->layout_page(gettext("templates"));

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", $_REQUEST["mod"]);
		$output->addHiddenField("start", (int)$_REQUEST["start"]);
		$output->addHiddenField("address_id", $_REQUEST["address_id"]);
		$output->addHiddenField("sort", $_REQUEST["sort"]);
		
		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("templates"),
			"subtitle" => gettext("start")
		));

		/* menu items */
		$venster->addMenuItem(gettext("new template"), "javascript: popup('index.php?mod=templates&action=edit', 'salesedit', 0, 0, 1);");
		$venster->addMenuItem(gettext("settings"), "?mod=templates&action=settings");
		$venster->addMenuItem(gettext("address book"), "?mod=address");
		$venster->generateMenuItems();
		$venster->addVensterData();

		$template_data = new Templates_data();
		$data = $template_data->getTemplateBySearch("", $_REQUEST["start"], "", $_REQUEST["sort"], $_REQUEST["search"]);

		$venster->addCode(gettext("search").": ");
		$venster->addTextField("search", $_REQUEST["search"]);
		$venster->insertAction("toggle", gettext("show all"), "javascript: document.getElementById('search').value = ''; document.getElementById('velden').submit();");
		$venster->insertAction("forward", gettext("search"), "javascript: document.getElementById('start').value = ''; document.getElementById('velden').submit();");
		
		$view = new Layout_view();
		$view->addData($data["data"]);

		/* add the mappings (columns) we needed */
		$view->addMapping("&nbsp;", "%%complex_icon");
		$view->addMapping(gettext("contact"), "%%complex_address");
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping(gettext("date"), "%date");
		$view->addMapping("", "%%complex_actions");

		$view->defineSortForm("sort", "velden");
		$view->defineSort(gettext("contact"), "companyname");
		$view->defineSort(gettext("description"), "description");
		
		$view->defineComplexMapping("complex_address", array(
			array(
				"type"  => "link",
				"text"  => "%address",
				"link"  => array("?mod=address&action=relcard&id=", "%address_id")
			)
		));
		$view->defineComplexMapping("complex_icon", array(
			array(
				"type"  => "action",
				"src"   => "addressbook",
				"alt"   => gettext("single address"),
				"check" => "%icon_single"
			),
			array(
				"type"  => "action",
				"src"   => "state_public",
				"alt"   => gettext("multiple addresses"),
				"check" => "%icon_multi"
			)
		));
		
		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("edit"),
				"link"    => array("javascript: popup('index.php?mod=templates&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("delete"),
				"link"    => array("?mod=templates&action=delete&id=", "%id")
			)
		));
		$venster->addCode( $view->generate_output() );

		$paging = new Layout_paging();
		$paging->setOptions((int)$_REQUEST["start"], $data["count"], "javascript: blader('%%');");
		$venster->addCode( $paging->generate_output() );

		$venster->endVensterData();

		$output->load_javascript(self::include_dir."templates.js");

		$output->addCode( $venster->generate_output() );

		$output->endTag("form");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function settingsList() {
		$output = new Layout_output();
		$output->layout_page(gettext("template settings"));

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php"
		));
		$output->addHiddenField("mod", $_REQUEST["mod"]);

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("templates"),
			"subtitle" => gettext("settings")
		));

		/* menu items */
		$venster->addMenuItem(gettext("new setting"), "javascript: popup('index.php?mod=templates&action=edit_settings', 'salesedit', 850, 600, 1);");
		$venster->addMenuItem(gettext("templates"), "?mod=templates");
		$venster->addMenuItem(gettext("address book"), "?mod=address");
		$venster->addMenuItem(gettext("printer calibration"), "javascript: popup('?mod=templates&action=calibrate');");
		$venster->generateMenuItems();
		$venster->addVensterData();

		$template_data = new Templates_data();
		$data = $template_data->getTemplateSettings(1);

		$view = new Layout_view();
		$view->addData($data);

		/* add the mappings (columns) we needed */
		$view->addMapping(gettext("description"), "%description");
		$view->addMapping("", "%%complex_actions");

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type"    => "action",
				"src"     => "edit",
				"alt"     => gettext("edit"),
				"link"    => array("javascript: popup('index.php?mod=templates&action=edit_settings&id=", "%id", "', 'salesedit', 850, 600, 1);")
			),
			array(
				"type"    => "action",
				"src"     => "delete",
				"alt"     => gettext("delete"),
				"link"    => array("?mod=templates&action=settingsdelete&id=", "%id")
			)
		));
		$venster->addCode( $view->generate_output() );

		$venster->endVensterData();

		$output->endTag("form");
		$output->load_javascript(self::include_dir."templates.js");

		$output->addCode( $venster->generate_output() );

		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function settingsEdit() {

		$output = new Layout_output();
		$output->layout_page(gettext("template settings"), 1);

		$tpldata = new Templates_data();
		if ($_REQUEST["id"]) {
			$data = $tpldata->getTemplateSettingById($_REQUEST["id"]);
		}
		$output->addTag("form", array(
			"id"     => "velden",
			"method" => "POST",
			"action" => "index.php",
			"enctype" => "multipart/form-data"
		));
		$output->addHiddenField("mod", $_REQUEST["mod"]);
		$output->addHiddenField("id", $_REQUEST["id"]);
		$output->addHiddenField("action", "save_settings");

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("template settings"),
			"subtitle" => gettext("change:")
		));
		$venster->addVensterData();

		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData();

				$tbl->addTag("div", array(
					"style" => "width: 210px; height: 297px; border: 1px solid black; background-color: white; position: relative;"
				));
					$tbl->addTag("div", array(
						"id" => "preview_logo",
						"style" => "position: absolute; top: 0px; left: 120px; width: 80px; height: 53px; padding: 10px; overflow: hidden;"
					));
						$tbl->addTag("img", array(
							"src" => "themes/default/misc/ond_top.gif"
						));
					$tbl->endTag("div");
					$tbl->addTag("div", array(
						"id" => "preview_address",
						"style" => "border: 1px solid black; font-size: 2px; font-face: sans-serif; position: absolute; top: 20px; left: 10px; width: 80px; height: 25px; padding: 10px; overflow: hidden;"
					));
						$tbl->addCode("Test reciepient");
						$tbl->addTag("br");
						$tbl->addCode("A. Fokkerstraat 1b");
						$tbl->addTag("br");
						$tbl->addCode("1234 XY  Rotterdam");
						$tbl->addTag("br");
						$tbl->addCode("The Netherlands");
					$tbl->endTag("div");

					$tbl->addTag("div", array(
						"id" => "preview_data",
						"style" => "overflow: auto; border: 1px solid black; position: absolute; top: 80px; left: 10px; width: 160px; height: 180px; padding: 10px;"
					));
					$tbl->addCode( $tpldata->load_preview("lorem ipsum"));
					$tbl->endTag("div");


					$tbl->endTableData();
			$tbl->addTableData();
				$tbl->addSpace();
			$tbl->endTableData();
			$tbl->addTableData("", "top");

				$pos = new Layout_table();
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2), "header");
						$pos->addCode( gettext("positioning") );
					$pos->endTableData();
				$pos->endTableRow();
				/* outline - left/right */
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("position addressinfo near a logo").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addRadioField("data[address_position]", gettext("left"), 0, $data["address_position"]);
						$pos->addRadioField("data[address_position]", gettext("right"), 1, $data["address_position"]);
					$pos->endTableData();
				$pos->endTableRow();

				/* outline - left / top / max width */
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("left marge addressinfo").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addTextField("data[address_left]", $data["address_left"], array("style"=>"width: 80px;"));
						$pos->addCode("cm");
					$pos->endTableData();
				$pos->endTableRow();
				/*
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("adresgegevens marge boven circa").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addTextField("data[address_top]", $data["address_top"], array("style"=>"width: 80px;"));
						$pos->addCode("cm");
					$pos->endTableData();
				$pos->endTableRow();
				*/
				/* outline - horizontal */
				/*
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("afstand links/rechts").":" );
						$pos->addTag("br");
						$pos->addCode( "(".gettext("t.o.v. de pagina marges").")" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addTextField("data[address_left]", $data["address_left"], array("style"=>"width: 80px;"));
						$pos->addCode("cm");
					$pos->endTableData();
				$pos->endTableRow();
				*/
				/* outline - vertical */
				/*
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("afstand verticaal").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addTextField("data[address_top]", $data["address_top"], array("style"=>"width: 80px;"));
						$pos->addCode("cm");
					$pos->endTableData();
				$pos->endTableRow();
				*/

				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2), "header");
						$pos->addCode( gettext("page margins") );
					$pos->endTableData();
				$pos->endTableRow();
				/* outline - left */
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("left").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addTextField("data[page_left]", $data["page_left"], array("style"=>"width: 80px;"));
						$pos->addCode("cm");
					$pos->endTableData();
				$pos->endTableRow();
				/* outline - top */
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("top").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addTextField("data[page_top]", $data["page_top"], array("style"=>"width: 80px;"));
						$pos->addCode("cm");
					$pos->endTableData();
				$pos->endTableRow();
				/* outline - right */
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("right").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
						$pos->addTextField("data[page_right]", $data["page_right"], array("style"=>"width: 80px;"));
						$pos->addCode("cm");
					$pos->endTableData();
				$pos->endTableRow();

				/* description */
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2), "header");
						$pos->addCode( gettext("description") );
					$pos->endTableData();
				$pos->endTableRow();
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2), "data");
						$pos->addTextField("data[description]", $data["description"], array("style"=>"width: 250px;"));
					$pos->endTableData();
				$pos->endTableRow();

				$pos->addTableData( array("colspan"=>2), "header");
					$pos->addCode( gettext("logo")." / ".gettext("image") );
				$pos->endTableData();
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2), "data");
						$file = $tpldata->getTemplateFile($_REQUEST["id"]);
						if (!$_REQUEST["id"]) {
							$pos->addCode( gettext("save template first") );
						} else {
							if ($file["id"]) {
								$pos->insertAction("delete", gettext("delete"), sprintf("javascript: delfile();", $file["id"], $_REQUEST["id"]));
								$pos->addSpace();
								$pos->addCode($file["name"]);
							} else {
								$pos->addBinaryField("image");
							}
						}
					$pos->addTag("br");
					$pos->addTag("br");
					$pos->endTableData();
				$pos->endTableRow();
				/* footer */
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2), "header");
						$pos->addCode( gettext("footer positionering (alleen PDF)") );
					$pos->endTableData();
				$pos->endTableRow();
				/* outline - left/right */
				$pos->addTableRow();
					$pos->addTableData("", "data");
						$pos->insertTag("b", gettext("position footer").":" );
					$pos->endTableData();
					$pos->addTableData("", "data");
					$pos->addSelectField("data[footer_position]", array(
						"left"   => gettext("left"),
						"center" => gettext("middle"),
						"right"  => gettext("right")
					), $data["footer_position"]);
					$pos->endTableData();
				$pos->endTableRow();
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2), "header");
						$pos->addCode( gettext("footer text (alleen PDF)") );
					$pos->endTableData();
				$pos->endTableRow();
				$pos->addTableRow();
					$pos->addTableData(array("colspan"=>2), "data");
					$pos->addTextField("data[footer_text]", $data["footer_text"], array("style"=>"width: 250px;"));
					$pos->addTag("br");
					$pos->addTag("br");
					$pos->endTableData();
				$pos->endTableRow();

				/* actions */
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2, "align"=>"right"), "header");
						$pos->insertAction("close", gettext("close"), "javascript: window.close();");
						$pos->insertAction("save", gettext("save"), "javascript: document.getElementById('velden').submit();");
					$pos->endTableData();
				$pos->endTableRow();
				$pos->endTable();

				$tbl->addCode( $pos->generate_output() );

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		$venster->addCode( $tbl->generate_output() );
		$venster->endVensterData();


		$output->addCode( $venster->generate_output() );
		$output->endTag("form");
		$output->load_javascript(self::include_dir."templates.js");

		$output->layout_page_end();
		$output->exit_buffer();
	}

}
?>
