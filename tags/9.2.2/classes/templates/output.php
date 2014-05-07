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
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */
Class Templates_output {

	/* constants */
	const include_dir =  "classes/templates/inc/";

	/* variables */

	/* methods */
	public function templatePrint($pdf=0, $finance="") {

		$pdf=1; #always force on

		$output      = new Layout_output();
		$page_output = new Layout_output();

		$page_output->addTag("html");


		/* create objects */
		$address_data = new Address_data();
		$template_data = new Templates_data();

		if (is_array($finance)) {
			$finance_id       =& $finance["id"];
			$finance_txt      =& $finance["txt"];
			$finance_debtor   =& $finance["debtor"];
			$finance_tpl      =& $finance["tpl"];
			$finance_font     =& $finance["font"];
			$finance_fontsize =& $finance["fontsize"];
			$finance_header   =& $finance["header"];
		}

		/* get id */
		if ($finance_id)
			$id = $template_data->getFinanceTemplate($finance_id);
		else
			$id = $_REQUEST["id"];

		/* retrieve data and settings */
		if (!$id && $finance_tpl) {
			$settings = $template_data->getTemplateSettingById($finance_tpl);
			$data = array(
				"settings_id" => $finance_tpl,
				"font"        => $finance_font,
				"fontsize"    => $finance_fontsize
			);
		} else {
			$data = $template_data->getTemplateById($id);
			if ($data["settings_id"])
				$settings = $template_data->getTemplateSettingById($data["settings_id"]);
			else {
				$settings = array(
					"address_position" => "left",
					"address_left"     => 0,
					"address_top"      => 0,
					"address_width"    => 0,
					"page_left"        => 1,
					"page_top"         => 4,
					"page_right"       => 1
				);
				$data["fontsize"] = $finance_fontsize;
				$data["font"] = $finance_font;
			}
		}

		if ($finance_debtor) {
			$aid = $address_data->getAddressIdByDebtor($finance_debtor);
			$data["ids"] = $aid;
		}

		$settings["address_top"]  = number_format($settings["address_top"],1);
		$settings["address_left"] = number_format($settings["address_left"],1);
		$settings["page_top"]     = number_format($settings["page_top"],1);
		$settings["page_left"]    = number_format($settings["page_left"],1);
		$settings["page_right"]   = number_format($settings["page_right"],1);

		if (!$pdf)
			$pdf = $_REQUEST["pdf"];

		$conversion = new Layout_conversion();
		$fonts = $conversion->getFonts(1);
		$fsize = $fonts["sizes"][$data["fontsize"]];
		$data["font"] = $fonts["fonts"][$data["font"]];

		$page_output->addTag("head");
		$page_output->insertTag("title", " ");
			$page_output->addTag("style", array(
				"type" => "text/css"
			));
			$page_output->addCode("
				body, td, p, a, span, div, th, table {
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
					table {
						page-break-inside: auto;
					}
					div.content_data, div.address_data, div.container {
						width: 100%;
					}
				}
			");
			$page_output->endTag("style");
		$page_output->endTag("head");
		$page_output->addTag("body");

		$file = $template_data->getTemplateFile($settings["id"]);
		if ($file["id"]) {
			$img = $GLOBALS["covide"]->webroot."/index.php?mod=templates&action=view_file&dl=1&id=".$file["id"];
			$img = preg_replace("/^http(s){0,1}:\/\//si", "", $img);
			$img = "http://".preg_replace("/\/{1,}/s", "/", $img);
		}

		$null_img = preg_replace("/^https:\/\//si", "http://", $GLOBALS["covide"]->webroot)."img/null.gif";
		#$null_img = "http://www.terrazur.nl/cmsfile/933";

		$ids = explode(",", $data["ids"]);

		/* link these two vars */
		$tpl =& $data;

		//sort address names
		if ($tpl["address_businesscard_id"] == 0)
			$tt = "address";
		elseif ($tpl["address_businesscard_id"] == 1)
			$tt = "bcards";
		else
			$tt = "both";

		$names = $this->sortAddressNames($ids, $tpl["address_businesscard_id"], $tt, $tpl["businesscard_id"]);
		unset($ids);
		$ids = array();
		foreach ($names as $k=>$v) {
			$ids[] = $k;
		}

		$ii=0;
		foreach ($ids as $k=>$v) {
			$address_id = $v;

			/* create address header */
			$table = new Layout_table();
				$table->addTableRow();
				if ($settings["address_position"] == 0 && $settings["address_left"] > 0) {
					$table->addTableData();
					for ($i=0;$i<=$settings["address_left"]*0.7;$i++) {
						$table->addTag("img", array(
							"src"    => $null_img
						));
					}
					$table->endTableData();
				}
				$table->addTableData(array("align"=>"left"));

					$vv = preg_replace("/[^0-9]/s", "", $v);
					if ($tpl["businesscard_id"] && !$tpl["and_or"]) {
						$vv = $tpl["businesscard_id"];
						$tt = "bcards";
					} else if ($finance["bcard_id"]) {
						$vv = $finance["bcard_id"];
						$tt = "bcards";
					}
					$address = $address_data->getAddressByID($vv, $tt);

					/* exception for parsing business card info */
					if ($tt == "bcards") {
						$addressinfo = $address_data->getAddressByID($address["address_id"]);

						if (!(trim($address["business_address"]) || trim($address["personal_address"]))) {
							/* no address info attached, get addressinfo from company record */
							$address["address"]   = $addressinfo["address"];
							$address["address2"]  = $addressinfo["address2"];
							$address["zipcode"]   = $addressinfo["zipcode"];
							$address["city"]      = $addressinfo["city"];
							$address["country"]   = $addressinfo["country"];
							$address["phone_nr"]  = $addressinfo["phone_nr"];
							$address["mobile_nr"] = $addressinfo["mobile_nr"];
							$address["fax_nr"]    = $addressinfo["fax_nr"];
							$address["email"]     = $addressinfo["email"];
						} else {
							if (trim($address["business_address"])) {
								$address["address"] = $address["business_address"];
								$address["address2"] = "";
								$address["zipcode"] = $address["business_zipcode"];
								$address["city"] = $address["business_city"];
								$address["country"] = $address["business_country"];
							} else {
								/* put personal address there */
								$address["address"] = $address["personal_address"];
								$address["address2"] = $address["personal_address2"];
								$address["zipcode"] = $address["personal_zipcode"];
								$address["city"] = $address["personal_city"];
								$address["country"] = $address["personal_country"];
							}
						}
						$address["letterinfo"] = $address_data->generate_letterinfo(array(
							"contact_initials"     => $address["initials"],
							"contact_letterhead"   => $address["letterhead"],
							"contact_commencement" => $address["commencement"],
							"contact_givenname"    => $address["givenname"],
							"contact_infix"        => $address["infix"],
							"contact_surname"      => $address["surname"],
							"title"                => $address["title"]
						));
						$address["tav"] = $address["letterinfo"]["tav"];
						$address["contact_person"] = $address["letterinfo"]["contact_person"];
					}

					if (trim($address["pobox"]) && trim($address["pobox_zipcode"]) && trim($address["pobox_city"])) {
						$address["address"] = gettext("pobox")." ".trim(str_replace(strtolower(gettext("pobox")), "", strtolower($address["pobox"])));
						$address["zipcode"] = $address["pobox_zipcode"];
						$address["city"] = $address["pobox_city"];
					}

					/* company name */
					if ($address["companyname"] && !$address["is_person"]) {
						$table->addCode( htmlentities($address["companyname"], ENT_NOQUOTES, "UTF-8") );
						$table->addTag("br");
					}
					/* person */
					if (!$address["is_person"]) {
						$table->addCode( gettext("T.a.v.") . " ");
					}
					$table->addCode( htmlentities($address["tav"], ENT_NOQUOTES, "UTF-8") );
					$table->addTag("br");

					/* address */
					$table->addCode( htmlentities($address["address"], ENT_NOQUOTES, "UTF-8") );
					$table->addTag("br");

					/* zipcode and city */
					$table->addCode( $address["zipcode"] . "&nbsp;&nbsp;");
					$table->addCode( htmlentities($address["city"], ENT_NOQUOTES, "UTF-8") );

					/* fax */
					if ($data["fax_nr"]) {
						$table->addTag("br");
						$table->addCode( gettext("Fax") . $address["fax_nr"]);
					}

			$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$address_output_data = $table->generate_output();
			unset($table);

			$ii++;
			if ($ii > 1)
				$output->addCode("<!--NewPage-->");

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
				if ($settings["logo_position"] > 0 && $img) {
					$tbl->addTableRow();
						$tbl->addTableData();
							// left
							if ($settings["logo_position"] == 1) {
								$tbl->addTag("img", array(
									"src"    => $img
								));
								$tbl->addTag("br");
								$tbl->addSpace();
								$tbl->addTag("br");
							}
						$tbl->endTableData();
						$tbl->addTableData(array("align" => "right"));
							// right
							if ($settings["logo_position"] == 2) {
								$tbl->addTag("img", array(
									"src"    => $img,
									"align"  => "right"
								));
								$tbl->addTag("br");
								$tbl->addSpace();
								$tbl->addTag("br");
							}
						$tbl->endTableData();
					$tbl->endTableRow();
				}
				$tbl->addTableRow();
					$tbl->addTableData();
						if ($settings["address_position"] == 0) {
							$tbl->addCode($address_output_data);
						} else {
							if (!$settings["logo_position"] && $img) {
								$tbl->addTag("img", array(
									"src"    => $img,
									"align"  => "right"
								));
							}
						}
					$tbl->endTableData();
					$tbl->addTableData(array("align" => "right"));
						if ($settings["address_position"] == 1) {
							$tbl->addCode($address_output_data);
						} else {
							if (!$settings["logo_position"] && $img) {
								$tbl->addTag("img", array(
									"src"    => $img,
									"align"  => "right"
								));
							}
						}
					$tbl->endTableData();
				$tbl->endTableRow();
				$tbl->endTableRow();
				$tbl->endTable();

				$address_buffer = $tbl->generate_output();

				$output->addCode ( $address_buffer );
				$output->endTag("div");

				$output->addSpace();
				$output->addTag("br");
				$output->addSpace();
				$output->addTag("br");

				/* letter contents */
				$output->addTag("div", array(
					"class"    => "content_data"
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
				$output->addTag("br");
				$output->addCode( preg_replace("/(<br[^>]*?>)/si", "&nbsp;$1", nl2br(htmlentities(stripslashes($data["sender"]), ENT_NOQUOTES, "UTF-8"))) );

				if (trim(strip_tags($finance_txt))) {
					$output->addCode("<!--NewPage-->");
					$output->addCode(trim($finance_txt));
				}

				$output->endTag("div");

			$output->endTag("div");
		}
		if (!$id && is_array($finance)) {
			/* if no id, just parse the finance template */
			if (!$finance_header_used)
				$page_output->addCode($finance_header);

			$page_output->addCode($finance_txt);
		} else {
			$page_output->addCode($output->generate_output());
		}

		$page_output->endTag("body");
		$page_output->endTag("html");

		session_write_close();

		if ($GLOBALS["autoloader_include_path"])
			$dir = $GLOBALS["autoloader_include_path"]."/tmp/";
		else
			$dir = $GLOBALS["covide"]->temppath;

		$buffer = $page_output->generate_output();
		$buffer = str_replace("###addressdata###", $address_buffer, $buffer);
		$buffer = preg_replace("/(<br style=\"page-break-after: always\"[^>]*?>)/si", "<!--NewPage-->", $buffer);

		#die($buffer);

		require_once("classes/html2pdf/HTML_ToPDF.php");
		require_once("classes/html2pdf/PDFEncryptor.php");

		$pdf =& new HTML_ToPDF($buffer, preg_replace("/^https:\/\//si", "http://", $GLOBALS["covide"]->webroot));
		$pdf->setDefaultPath($dir);
		if ($settings["footer_text"])
			$pdf->footers = array($settings["footer_position"] => $settings["footer_text"]);
		else
			$pdf->footers = array("center" => "");

		$pdf->headers = array('left' => '', 'right' => '');

		// Could turn on debugging to see what exactly is happening
		// (commands being run, images being grabbed, etc.)
		// $pdf->setDebug(true);
		// Convert the file
		$result = $pdf->convert();
		// Check if the result was an error
		if (is_a($result, 'HTML_ToPDFException')) {
				die($result->getMessage());
		}

		$file = sprintf("%s/pdf_%s.pdf", $dir, md5(rand().session_id().time()));
		copy($result, $file);
		unlink($result);

		// Set up encryption
		$encryptor =& new PDFEncryptor($file);
		// Set paths
		$encryptor->setJavaPath('/bin/java');

		$itext = sprintf("%s/../html2pdf/lib/itext-1.3.jar", dirname(__FILE__));
		$encryptor->setITextPath($itext);
		// Set meta-data
		$encryptor->setAuthor('Covide');
		$encryptor->setKeywords('Covide');
		$encryptor->setSubject('Covide PDF');
		$encryptor->setTitle('Covide PDF');
		// Set permissions
		$encryptor->setAllowPrinting(true);
		$encryptor->setAllowModifyContents(false);
		$encryptor->setAllowDegradedPrinting(true);
		$encryptor->setAllowCopy(true);
		// Set password
		//$encryptor->setUserPassword("covide");
		$encryptor->setOwnerPassword(rand());
		$result = $encryptor->encrypt();

		if (is_a($result, 'PDFEncryptorException')) {
				die($result->getMessage());
		}

		if ($finance["email_sender"]) {
			$email_data = new Email_data();
			$param = array(
				"mail" => array("from" => $finance["email_signature"]),
				"to"   => $finance["email"],
				"relation" => $address_id,
				"new_subject"  => $finance["email_subject"]
			);
			/* create a concept */
			$mail_id = $email_data->save_concept(0, $param);

			/* add attachment */
			$data = array(
				"name" => sprintf("%s.pdf", $finance["email_subject"]),
				"type" => "application/pdf",
				"bin"  => file_get_contents($file),
				"id"   => $mail_id
			);
			$email_data->addAttachmentFromString($data);

			/* unlink the file */
			unlink($file);

			/* now go to the email */
			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode(sprintf("
					location.href = '%s/?mod=email&action=compose&id=%d';
				", $GLOBALS["covide"]->webroot, $mail_id));
			$output->end_javascript();
			$output->exit_buffer();

		} else {

			#file download
			header('Content-Transfer-Encoding: binary');
			header('Content-Type: application/pdf');

			if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 5.5")) {
				header('Content-Disposition: filename="covide_template.pdf"'); //msie 5.5 header bug
			}else{
				header('Content-Disposition: attachment; filename="covide_template.pdf"');
			}
			/* send file contents */
			echo file_get_contents($file);

			/* unlink the file */
			unlink($file);
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
		$templates_data = new Templates_data();

		$upd_finance = $_REQUEST["upd_finance"];

		if ($_REQUEST["id"]) {
			$id = $_REQUEST["id"];
		}
		if ($_REQUEST["finance"]) {
			$t = $templates_data->getFinanceTemplate($_REQUEST["finance"]);
			if ($t) {
				unset($output);
				$output = new Layout_output();
				$output->start_javascript();
				$output->addCode(sprintf("location.href='index.php?mod=templates&action=edit&id=%d&upd_finance=1';", $t));
				$output->end_javascript();
				$output->exit_buffer();
			} else {
				$upd_finance = 1;
			}
		}
		if ($upd_finance) {
			$output->start_javascript();
			$output->addCode("
				opener.document.getElementById('template_edit').style.display = '';
				opener.document.getElementById('template_new').style.display = 'none';
			");
			$output->end_javascript();
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
		$output->addHiddenField("use_signature", 0);
		$output->addHiddenField("pdf", 0);
		$output->addHiddenField("upd_finance", $_REQUEST["upd_finance"]);
		$output->addHiddenField("dl", 0);

		/* generate nice window */
		$venster = new Layout_venster(array(
			"title"    => gettext("templates"),
			"subtitle" => gettext("start")
		));
		$venster->addVensterData();

		$data = $templates_data->getTemplateSettings();

		if ($id) {
			$tpl = $templates_data->getTemplateById($id);
		} else {
			$tpl["ids"] = $_REQUEST["address_id"];
			$tpl["businesscard_id"] = $_REQUEST["businesscard_id"];
			$tpl["date"] = utf8_encode(strftime("%d %B %Y"));
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
				$tbl->addCode( gettext("recipients") );
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

				/* sort the addresses by custom routine */
				$names = $this->sortAddressNames($ids, $tpl["address_businesscard_id"], $type, $tpl["businesscard_id"]);

				$names = sprintf("<ul type='circle'><li>%s</ul>", implode("<li>", $names));
				if ($tpl["and_or"])
					$names = gettext("by classifications").": ".$names;
				elseif ($tpl["businesscard_id"])
					$names = gettext("by businesscard").": ".$names;
				else
					$names = gettext("by addresses").": ".$names;


				$tbl->addHiddenField("tpl[finance]", ($_REQUEST["finance"]) ? $_REQUEST["finance"]:$tpl["finance"]);
				if ($_REQUEST["finance"] || $tpl["finance"]) {
					$tbl->addCode(gettext("this template is linked to Covide finance"));
				} else {
					$tbl->insertTag("div", $names, array(
						"style" => "width: 700px; height: 120px; border: 1px solid black; overflow: auto; padding: 4px;",
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

					$tbl->addTag("br");
					/* by address id */
					if (!$tpl["and_or"]) {
						$tbl->addSpace();
						$tbl->addCode(gettext("select businesscard").": ");

						if (!$address_data)
							$address_data = new Address_data();

						$bcards = $address_data->getBcardsByRelationID($ids[0]);
						$sel = array(
							gettext("default") => array(
								0 => gettext("no businesscard")
							)
						);
						$address_name = $address_data->getAddressNameByID($ids[0]);
						foreach ($bcards as $b) {
							$sel[$address_name][$b["id"]] = $b["fullname"];
						}
						$tbl->addSelectField("tpl[businesscard_id]", $sel, $tpl["businesscard_id"]);
						$tbl->insertAction("forward", gettext("select"), "javascript: document.getElementById('velden').submit();");
						$tbl->start_javascript();
							$tbl->addCode("document.getElementById('tplbusinesscard_id').onchange = function() { document.getElementById('velden').submit(); }");
						$tbl->end_javascript();
						$tbl->addSpace(5);

					}

					$tbl->insertAction("addressbook", gettext("pick addresses"), "javascript: popup('?mod=address&action=searchRel', 'search_address');");
					$tbl->addSpace();
					$tbl->insertTag("a", gettext("pick addresses"), array(
						"href" => "javascript: popup('?mod=address&action=searchRel', 'search_address');"
					));
					$tbl->insertAction("state_special", gettext("pick classifications"), "javascript: popup('?mod=templates&action=selectCla&$uri', 'search_address', 450, 500, 1);");
					$tbl->addSpace();
					$tbl->insertTag("a", gettext("pick classifications"), array(
						"href" => "javascript: popup('?mod=templates&action=selectCla&$uri', 'search_address', 450, 500, 1);"
					));
				}
			$tbl->endTableData();
		$tbl->endTableRow();
		/* default fonts */
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("standard font") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$conversion = new Layout_conversion();
				$fonts = $conversion->getFonts();

				$tbl->addSelectField("tpl[font]", $fonts["fonts"], $tpl["font"]);
				$tbl->addSelectField("tpl[fontsize]", $fonts["sizes"], $tpl["fontsize"]);

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
			$tbl->addCode( $editor->generate_editor(2, $tpl["body"]) );

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
		$tbl->addTableRow();
			$tbl->addTableData("", "header");
				$tbl->addCode( gettext("sender data") );
			$tbl->endTableData();
			$tbl->addTableData("", "data");
				$tbl->addTextArea("tpl[sender]", stripslashes($tpl["sender"]), array(
					"style" => "width: 700px; height: 150px; font-family:"
				));
				$tbl->addTag("br");
				$tbl->addCode(gettext("or choose a signature").": ");

				$email_data = new Email_data();
				$sigs = $email_data->getEmailAliases();
				$tbl->addSelectField("tpl[address_signature]", $sigs, "");
				$tbl->insertAction("forward", gettext("use this signature"), "javascript: document.getElementById('use_signature').value = 1; save();");
			$tbl->endTableData();
		$tbl->endTableRow();

		/* actions */
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2), "header");
				$tbl->insertAction("save", gettext("save"), "javascript: save();");
				$tbl->addSpace();
				if ($id) {
					//$tbl->insertAction("print", gettext("print"), "javascript: printTemplate(0);");
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

	public function show_list() {

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
		$view->addMapping(gettext("description"), "%%complex_description");
		$view->addMapping(gettext("contact"), "%%complex_address");
		$view->addMapping(gettext("date and city"), "%datecity");
		$view->addMapping("", "%%complex_actions");

		$view->defineSortForm("sort", "velden");
		$view->defineSort(gettext("contact"), "companyname");
		$view->defineSort(gettext("description"), "description");

		$view->defineComplexMapping("complex_address", array(
			array(
				"type"  => "action",
				"src"   => "addressbook",
				"alt"   => gettext("single address"),
				"check" => "%icon_single"
			),
			array(
				"type"  => "action",
				"src"   => "state_public",
				"alt"   => gettext("businesscard"),
				"check" => "%icon_bcard"
			),
			array(
				"type"  => "action",
				"src"   => "state_multiple",
				"alt"   => gettext("multiple addresses"),
				"check" => "%icon_multi"
			),
			array(
				"text"  => " "
			),
			array(
				"type"  => "link",
				"text"  => "%address",
				"link"  => array("?mod=address&action=relcard&id=", "%address_id")
			)
		));
		$view->defineComplexMapping("complex_description", array(
			array(
				"type"    => "link",
				"text"    => "%description",
				"link"    => array("javascript: popup('index.php?mod=templates&action=edit&id=", "%id", "', 'salesedit', 0, 0, 1);")
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
		//$venster->addMenuItem(gettext("templates"), "?mod=templates");
		$venster->addMenuItem(gettext("address book"), "?mod=address");
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
						$pos->addTextField("data[description]", $data["description"], array("style"=>"width: 400px;"));
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

				/* outline logo - higher - normal */
				$pos->addTableRow();
					$pos->addTableData(array("colspan" => 2), "data");
						$pos->insertTag("b", gettext("position of logo").":" );
						$pos->addTag("br");
						$pos->addRadioField("data[logo_position]", gettext("beside the address data"), 0, $data["logo_position"]);
						$pos->addRadioField("data[logo_position]", gettext("on top left of the page"), 1, $data["logo_position"]);
						$pos->addRadioField("data[logo_position]", gettext("on top right of the page"), 2, $data["logo_position"]);
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
						$pos->addCode( gettext("footer text") );
					$pos->endTableData();
				$pos->endTableRow();
				$pos->addTableRow();
					$pos->addTableData(array("colspan"=>2), "data");
					$pos->addTextArea("data[footer_text]", $data["footer_text"], array("style"=>"width: 400px; height: 80px;"));
					$pos->addTag("br");
					$pos->addTag("br");
					$pos->endTableData();
				$pos->endTableRow();

				/* actions */
				$pos->addTableRow();
					$pos->addTableData( array("colspan"=>2, "align"=>"right"), "header");
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

	private function sortAddressNames($ids, $bc, $type, $bid=0) {
		$names = array();
		$address_data = new Address_data();

		foreach ($ids as $k=>$v) {
			if ($v) {
				$type = substr($v, 0, 1);
				$vv = preg_replace("/[^0-9]/s", "", $v);
				if ($type == "R" || !$bc) {
					$tmp = $address_data->getAddressNameByID($vv);
					if ($bid) {
						$tmp1 = $address_data->getAddressByID($bid, "bcards");
						if ($tmp1["fullname"] && $tmp1["fullname"] != "--")
							$tmp = sprintf("%s, %s", $tmp1["companyname"], $tmp1["fullname"]);

						unset($tmp1);
					}
					if (!$bid)
						$names[$vv] = $tmp;
					else
						$names[] = $tmp;

					unset($tmp);

				} else {
					$tmp = $address_data->getAddressByID($vv, "bcards");
					if ($tmp["companyname"])
						$tmp["tmpname"] = $tmp["companyname"];

					if ($tmp["fullname"] && $tmp["fullname"] != "--") {
						if ($tmp["tmpname"])
							$tmp["tmpname"].= ", ";

						$tmp["tmpname"].= $tmp["fullname"];
					}
					if ($tmp["tmpname"])
						$names[$vv] = $tmp["tmpname"];

					unset($tmp);
				}
			}
		}
		natcasesort($names);
		return $names;
	}

}
?>
