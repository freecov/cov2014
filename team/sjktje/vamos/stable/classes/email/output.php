<?php
/**
 * Covide Groupware-CRM Email module
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
Class Email_output {

	/* constants */
	const include_dir = "classes/email/inc/";
	const include_dir_main = "classes/html/inc/";

	const class_name = "email";
	private $notfound_image = "themes/default/bt.png";

	/* variables */
	private $_folders = array();
	private $archief;

	private $output;
	private $email_selector_loaded;

	/* methods */

    /* 	__construct {{{ */
    /**
     * 	__construct. TODO Single line description
     *
     * TODO Multiline description
     *
     * @param type Description
     * @return type Description
     */
	public function __construct() {
		$this->output="";
	}
	/* }}} */

	public function emailSelectFromPrepare() {
		require(self::include_dir."emailSelectFromPrepare.php");
		return $buf;
	}
	public function emailSelectFrom($address_id="", $address_type="") {
		require(self::include_dir."emailSelectFrom.php");
	}

	public function emailList($folder_id="", $msg="") {
		require(self::include_dir."emailList.php");
	}

	public function emailOpen() {
		require(self::include_dir."emailOpen.php");
	}

	public function emailCompose($id) {
		require(self::include_dir."emailCompose.php");
	}

	public function emailGetFromList() {
		require(self::include_dir."emailGetFromList.php");
	}

	public function emailFrom() {
		$output = new Layout_output();
		$output->addTag("div", array(
			"id"    => "email_completion_layer"
		));
		$output->endTag("div");
		$output->load_javascript("classes/html/inc/xmlhttp.js");
		$output->load_javascript("classes/email/inc/emailSelectFrom.js");
		return $output->generate_output();
	}

	public function parseEmailCheckbox($array) {
		$ids = array();
		if (is_array($array)) {
			foreach ($array as $k=>$v) {
				$ids[]=$k;
			}
			return $ids;
		} else {
			return 0;
		}
	}

	public function folderMove() {
		require(self::include_dir."folderMove.php");
	}
	public function selectionMove() {
		require(self::include_dir."selectionMove.php");
	}

	public function viewHtml() {
		$mailData = new Email_data();
		$data = $mailData->getEmailById($_REQUEST["id"]);
		$body =& $data[0]["body_html"];

		$body = $mailData->stylehtml($body);
		$body = preg_replace("/ target=\"[^\"]*?\"/si", "", $body);
		preg_match_all("/<a ([^>]*?)>/si", $body, $matches);
		foreach ($matches[0] as $k=>$v) {
			if (!preg_match("/ href=(\"|')mailto:/si", $v)) {
				$repl = preg_replace("/<a /si", "<a target=\"_blank\" ", $v);
				$body = str_replace($v, $repl, $body);
			}
		}

		$output_js = new Layout_output();
		$output_js->start_javascript();
			$output_js->addCode(" parent.document.getElementById('js_show_inline').style.display = 'block'; ");
		$output_js->end_javascript();

		/* scan for external images and tracking items */
		//preg_match_all("/ src=('|\")([^('|\")]*?)('|\")/si", $body, $matches);

		preg_match_all("/<\w{1,} (src|background)=[^>]*>/si", $body, $matches);

		$len = strlen($GLOBALS["covide"]->webroot);
		foreach ($matches[0] as $k=>$v) {
			$rv = str_replace("'", "\"", $v);
			preg_match_all("/(src|background)=\"([^\"]*?)\"/si", $rv, $vmatch);

			$check = strtolower(preg_replace("/\/{1,}$/s", "", strtolower(substr($vmatch[2][0], 0, $len))));
			$webroot = strtolower(preg_replace("/\/{1,}$/s", "", $GLOBALS["covide"]->webroot));

			if ($webroot != $check) {
				/* external url found! */
				$ext_found++;
				$body = str_replace($vmatch[2][0], $this->notfound_image."?".urlencode(trim($vmatch[2][0])), $body);
				if ($ext_found == 1)
					$body = preg_replace("/(<body[^>]*?>)/si", "$1".$output_js->generate_output(), $body);
			}
		}
		/* scan for mailto links */
		preg_match_all("/ href=('|\")([^('|\")]*?)('|\")/si", $body, $matches);
		$len = strlen($GLOBALS["covide"]->webroot);
		foreach ($matches[2] as $k=>$v) {
			if (preg_match("/ href=(\"|')mailto:/si", $matches[0][$k])) {
				$repl = preg_replace("/^mailto:/si", "", $v);
				$repl = sprintf("javascript: parent.handleMailtoLinks('%s');", $repl);
				$body = str_replace($v, $repl, $body);
			}
		}

		$output = new Layout_output();
		$output->addCode( $body );

		header("Content-type: text/html; charset=UTF-8");
		$output->exit_buffer();
	}

	public function emailUploadView() {
		$output = new Layout_output();
		$output->layout_page("upload", 1);
		$output->addCode(1);
		$output->insertBinaryField("binFile");
		$output->layout_page_end();
		$output->exit_buffer();
	}

	public function getSharedFolderList($current) {

		$email_data = new Email_data();
		$folders = $email_data->getSharedFolderAccess($_SESSION["user_id"]);

		$table = new Layout_table(array("cellspacing"=>1), 1);
		foreach ($folders as $f) {
			$flag++;
			$v = $email_data->getFolder($f["folder_id"], 1);
			$table->addTableRow();
				$table->addTableData("", ($current==$v["id"]) ? "header nowrap":"data nowrap");
					if ($current == $v["id"]) {
						$class = "marked";
					} else {
						$class = "";
					}
					if (!$v["name"]) {
						$v["name"] = "[".gettext("no name")."]";
					}
					/* only translate when parent_id is not set */
					if (!$v["parent_id"])
						$v["name"] = gettext($v["name"]);
					if ($move_folder && ($v["id"]==$current)) {
						$table->addCode($v["name"]);
					} else {
						$table->insertLink($v["name"]." (".$f["username"].")", array(
							"href"  => "javascript: setFolder('".$v["id"]."', '".(int)$v["archive"]."')",
							"class" => $class
						));
					}
					$table->addSpace();
					$table->addCode("(");
					if ($v["unread"]) {
						$table->addTag("span", array("class"=>"marked"));
						$table->addCode((int)$v["unread"]);
						$table->endTag("span");
					} else {
						$table->addCode((int)$v["unread"]);
					}
					$table->addCode("/".(int)$v["count"].")");
				$table->endTableData();
				$table->addTableData("", "data");
					if ($v["unread"]) {
						$table->insertTag("div", "&nbsp;", array(
							"style" => "background-color: red;",
							"alt"   => "ongelezen berichten",
							"title" => "ongelezen berichten"
						));
					}
				$table->endTableData();
			$table->endTableRow();
		}
		$table->endTable();
		if ($flag) {
			return $table->generate_output();
		} else {
			return "";
		}
	}

	public function getFolderList($folders, $current, $move_folder=0) {
		$table = new Layout_table(array("cellspacing"=>1), 1);
		foreach ($folders as $k=>$v) {
			$table->addTableRow();
				$table->addTableData("", ($current==$v["id"]) ? "header nowrap":"data nowrap");
					$table->addSpace($v["level"]*3);
					if ($current == $v["id"]) {
						$class = "marked";
					} else {
						$class = "";
					}
					if (!$v["name"]) {
						$v["name"] = "[".gettext("no name")."]";
					}
					/* only translate folders at level 0, the rest is usersupplied */
					if ($v["level"] == 0)
						$v["name"] = gettext($v["name"]);

					if ($move_folder && ($v["id"]==$current)) {
						$table->addCode($v["name"]);
					} else {
						$table->insertLink($v["name"], array(
							"href"  => "javascript: setFolder('".$v["id"]."', '".(int)$v["archive"]."')",
							"class" => $class
						));
					}
					if (!$move_folder) {
						$table->addSpace();
						$table->addCode("(");
						if ($v["unread"]) {
							$table->addTag("span", array("class"=>"marked"));
							$table->addCode((int)$v["unread"]);
							$table->endTag("span");
						} else {
							$table->addCode((int)$v["unread"]);
						}
						$table->addCode("/".(int)$v["count"].")");
					}
				$table->endTableData();
				$table->addTableData("", "data nowrap");
					if ($v["unread"]) {
						$table->insertTag("span", "&nbsp;", array(
							"style" => "background-color: red;",
							"alt"   => "ongelezen berichten",
							"title" => "ongelezen berichten"
						));
						$table->addSpace();
					}
					if ($v["shared"]) {
						$table->insertTag("span", "&nbsp;", array(
							"style" => "background-color: blue;",
							"alt"   => "deze map is gedeeld",
							"title" => "deze map is gedeeld"
						));
					}
				$table->endTableData();
			$table->endTableRow();
		}
		$table->endTable();
		return $table->generate_output();
	}

	public function getSelectList($name, $folders, $current, $settings="", $folders_shared="") {
		$email_data = new Email_data();

		$list = array();
		$output = new Layout_output();
		foreach ($folders as $k=>$v) {
			$n = $output->nbspace($v["level"]*3);
			if ($v["level"] == 0)
				$n.= gettext($v["name"]);
			else
				$n.= $v["name"];
			$list[$v["id"]] = $n;
		}
		if ($folders_shared) {
			foreach ($folders_shared as $k=>$v) {
				$n = $email_data->getFolder($v["folder_id"]);
				if (!$n["parent_id"])
					$list2[$v["folder_id"]] = gettext($n["name"])." (".$v["username"].")";
				else
					$list2[$v["folder_id"]] = $n["name"]." (".$v["username"].")";
			}
			$list = array(
				gettext("my folders") => $list,
				gettext("shared folders") => $list2
			);
		}


		$output->addSelectField($name, $list, $current, 0, $settings);
		return $output->generate_output();
	}

	public function emailPrint() {
		$email_data = new Email_data();
		$mdata      = $email_data->getEmailById($_REQUEST["id"]);

		/* start output buffer routines */
		$output = new Layout_output();
		$output->layout_page( gettext("Email"), 1 );
		$settings = array(
			"title"    => gettext("Email")
		);
		$venster = new Layout_venster($settings);
		unset($settings);
		$venster->addVensterData();

		if ($mdata[0]["body_html"]) {
			$body =& $mdata[0]["body_html"];
		} else {
			$body = nl2br($mdata[0]["body"]);
		}
		$prefix = $email_data->getPrefix(1);
		$prefix.= "<table class='table1' cellspacing='1' cellpadding='0'>\n";
		$prefix.= " <tr class='head1'>\n";
		$prefix.= "  <td colspan='2'>";
		$prefix.= "----- ".gettext("original message")." -----\n";
		$prefix.= "  </td>\n";
		$prefix.= " </tr><tr class='head2'>\n";
		$prefix.= "  <td class='cell1'>".gettext("from").": </td><td class='cell2'>".$mdata[0]["sender_emailaddress"]."</td>\n";
		$prefix.= " </tr><tr class='head2'>\n";
		$prefix.= "  <td class='cell1'>".gettext("to").": </td><td class='cell2'>".$mdata[0]["to"]."</td>\n";
		$prefix.= " </tr><tr class='head2'>\n";
		$prefix.= "  <td class='cell1'>".gettext("subject").": </td><td class='cell2'>".$mdata[0]["subject"]."</td>\n";
		$prefix.= " </tr><tr class='head2'>\n";
		if ($mdata[0]["cc"]) {
			$prefix.= "  <td class='cell1'>".gettext("cc").": </td><td class='cell2'>".$mdata[0]["cc"]."</td>\n";
			$prefix.= " </tr><tr class='head2'>\n";
		}
		$prefix.= "  <td class='cell1'>".gettext("date").": </td><td class='cell2'>".$mdata[0]["h_date"]."</td>\n";
		$prefix.= " </tr>\n</table>\n";
		$prefix.= "<br>\n";

		$prefix.= "<div style='border-left: 2px solid blue; padding-left: 6px;'>\n";
		$suffix = "</div>";

		$body = $email_data->stripBodyTags($body);

		$venster->addCode($prefix."<BR><BR><BR>".$body);

		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		unset($venster);
		$output->start_javascript();
			$output->addCode("
				window.print();
				window.close();
			");
		$output->end_javascript();
		$output->layout_page_end();
		$output->exit_buffer();

	}

	public function emailShowTracking() {
		$email_data = new Email_data();
		$mdata      = $email_data->getEmailById($_REQUEST["id"]);

		/* some classification related stuff */
		$classification = new Classification_data();

		/* positive */
		$cla_positive =  explode(",", $mdata[0]["classifications_positive"]);
		foreach ($cla_positive as $k=>$v) {
			if (!$v) unset($cla_positive[$k]);
		}
		if (count($cla_positive)>0) {
			$tmp = $classification->getClassifications($cla_positive, 1);
			$cla_positive = "";
			foreach ($tmp as $t) {
				$cla_positive .= "<li class='enabled'>".$t["description"]."</li>";
			}
			$mdata[0]["h_classifications_positive"] = $cla_positive;
		}


		/* negative */
		$cla_negative =  explode(",", $mdata[0]["classifications_negative"]);
		foreach ($cla_negative as $k=>$v) {
			if (!$v) unset($cla_negative[$k]);
		}
		if (count($cla_negative)>0) {
			$tmp = $classification->getClassifications($cla_negative, 1);
			$cla_negative = "";
			foreach ($tmp as $t) {
				$cla_negative .= "<li class='enabled'>".$t["description"]."</li>";
			}
			$mdata[0]["h_classifications_negative"] = $cla_negative;
		}

		if ($mdata[0]["classifications_type"]=="and") {
			$mdata[0]["h_classifications_type"] = gettext("unique classifications (AND)");
		} elseif ($mdata[0]["classifications_type"]=="or") {
			$mdata[0]["h_classifications_type"] = gettext("added classifications (OR)");
		}

		if ($mdata[0]["classifications_target"]=="relations") {
			$mdata[0]["h_classifications_target"] = gettext("addresses");
		} elseif ($mdata[0]["classifications_target"]=="businesscards") {
			$mdata[0]["h_classifications_target"] = gettext("business cards");
		}


		/* start output buffer routines */
		$output = new Layout_output();
		$output->layout_page( gettext("Email"), 1 );
		$settings = array(
			"title"    => gettext("Email"),
			"subtitle" => gettext("tracking")
		);
		$venster = new Layout_venster($settings);
		unset($settings);
		$venster->addVensterData();

		$tbl = new Layout_table();
		$tbl->addTableRow();
			$tbl->addTableData();
				$view = new Layout_view();
				$view->addData($mdata);
				$view->addMapping(gettext("from"), "%sender_emailaddress_h");
				$view->addMapping(gettext("to"), "%h_to", "", "", 1);
				$view->addMapping(gettext("subject"), "%subject");
				$view->addMapping(gettext("cc"), "%h_cc", "", "", 1);
				$view->addMapping(gettext("bcc"), "%h_bcc", "", "", 1);
				$view->addMapping(gettext("date sent"), "%h_date", "", "list_hidden");
				$view->addMapping(gettext("classification"), "%h_classifications_target");
				$view->addMapping(gettext("classification selection"), "%h_classifications_type");
				$view->addMapping(gettext("positive classifications"), "%h_classifications_positive", "", "list_hidden");
				$view->addMapping(gettext("negative classifications"), "%h_classifications_negative", "", "list_hidden");
				$tbl->addCode( $view->generate_output_vertical() );
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2));
				$tbl->addTag("br");

				$items = $email_data->get_tracker_items($_REQUEST["id"]);
				$view = new Layout_view();
				$view->addData($items["list"]);
				$view->addMapping(gettext("receipient"), "%email");
				$view->addMapping(gettext("read first/last"), array(
					"%read_first_h",
					"\n",
					"%read_last_h"
				));
				$view->addMapping(gettext("number"), array("%count", "x gelezen"));
				$view->addMapping(gettext("software"), "%%complex_agents_h");
				$view->addMapping(gettext("hyperlinks"), "%hyperlinks_h", "", "", 1);
				$view->addMapping(" ", "%%complex_msg");

				$view->defineComplexMapping("complex_msg", array(
					array(
						"type"  => "action",
						"src"   => "important",
						"alt"   => gettext("Deze ontvanger heeft de nieuwsbrief (nog) niet gehad"),
						"check" => "%not_sent"
					)
				));

				$view->defineComplexMapping("complex_agents_h", array(
					array(
						"text"  => "<div style='height: 26px; overflow: auto'>"
					),
					array(
						"text"  => "%agents_h"
					)
				));

				$tbl->addCode( $view->generate_output() );

			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();
		$venster->addCode( $tbl->generate_output() );

		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		unset($venster);
		$output->layout_page_end();
		$output->exit_buffer();

	}


	public function emailHeaderInfo() {
		$output = new Layout_output();
		$mailData = new Email_data();
		$data = $mailData->getEmailById($_REQUEST["id"]);
		$header = $data[0]["header"];

		$header = htmlentities($header);

		$output->addTag("html");
		$output->addTag("body");
		$output->addTag("pre");

		$output->addCode( $header );
		$output->endTag("pre");
		$output->endTag("body");
		$output->endTag("html");

		$output->exit_buffer();
	}

	public function stripMailBody($body) {
		$body = preg_replace("/^.*<body[^>]*?>/si","",$body);
		$body = preg_replace("/<\/body>.*$/si","",$body);
		return $body;
	}

	public function insertFromToHeader($html=0, $maildata, $hide_orig_msg=0) {

		$output = new Layout_output();
		$output->addTag("html");
		$output->addTag("body");

		if ($html) {
			$output->addTag("blockquote", array(
				"style"=>"border-left: 2px solid #000000; padding-right: 0px; padding-left: 5px; margin-left: 5px; margin-right: 0px;"
			));
			$output->addTag("font", array("size"=>2));
			$output->addTag("div" , array("style"=>"font: 10pt arial, serif"));
			if (!$hide_orig_msg) {
				$output->addCode( sprintf("----- %s -----", gettext("Original message")) );

			}
			$output->addTag("div", array("style"=>"background: #e4e4e4; font-color: black"));
				$output->insertTag("b", gettext("to"));
				$output->addSpace();
				$output->addCode($maildata["to"]);
			$output->endTag("div");
			$output->addTag("div");
				$output->insertTag("b", gettext("from"));
				$output->addSpace();
				$output->addCode($maildata["sender_emailaddress"]);
			$output->endTag("div");
			if (strstr($maildata["cc"],"@")) {
				$output->addTag("div");
					$output->insertTag("b", gettext("cc"));
					$output->addSpace();
					$output->addCode($maildata["cc"]);
				$output->endTag("div");
			}
			$output->addTag("div");
				$output->insertTag("b", gettext("date"));
				$output->addSpace();
				$output->addCode($maildata["h_date"]);
			$output->endTag("div");
			$output->addTag("div");
				$output->insertTag("b", gettext("subject"));
				$output->addSpace();
				$output->addCode($maildata["subject"]);
			$output->endTag("div");

			$output->endTag("div");
			$output->endTag("font");
			$output->endTag("blockquote");

			$output->addCode($maildata["body"]);
			$output->print_javascript();
			$output->endTag("body");
			$output->endTag("html");
			$output->exit_buffer();

		} else {

			echo "textheader here";

		}

	}

	public function showInfo() {
		$mailData = new Email_data();
		$addressData = new Address_data();

		$data = $mailData->getEmailById($_REQUEST["id"]);
		$mdata =& $data[0];

		$fields[gettext("from")]              = $mdata["sender_emailaddress_h"];
		$fields[gettext("to")]             = $mdata["to"];
		$fields[gettext("subject")]        = $mdata["subject"];
		$fields[gettext("cc")]               = $mdata["cc"];
		$fields[gettext("bcc")]              = $mdata["bcc"];
		$fields[gettext("reply to")]       = $mdata["replyto"];
		$fields[gettext("date sent")]  = $mdata["h_date"];
		$fields[gettext("date received")]  = $mdata["h_date_received"];
		$fields[gettext("read confirmation")]  = $mdata["readconfirm"];
		$fields[gettext("priority")]       = $mdata["prioriteit"];
		$fields[gettext("description")]     = $mdata["description"];
		$fields[gettext("contact")]          = $addressData->getAddressNameById($mdata["address_id"]);

		$table = new Layout_table();
		foreach ($fields as $k=>$v) {
			if ($v) {
				$table->addTableRow();
					$table->addTableData("", "header");
						$table->addCode($k);
					$table->endTableData();
					$table->addTableData("", "data");
						$table->addCode($v);
					$table->endTableData();
				$table->endTableRow();
			}
		}
		if ($mdata["askwichrel"]) {
			$table->addTableRow();
				$table->insertTableData(gettext("pick contact"), "", "header");
				$table->addTableData();
					$multi = $addressData->lookupRelationEmail($mdata["clean_emailaddress"]);
					foreach ($multi as $k=>$v) {
						$table->addTag("li", array("class"=>"enabled"));
							$table->addTag("a", array(
								"href" => "javascript: void(0);",
								"onclick" => "selectRelation('".$_REQUEST["id"]."', '$k');"
							));
							$table->addCode($v);
							$table->endTag("a");
						$table->endTag("li");
					}

				$table->endTableData();
			$table->endTableRow();
		}

		$table->endTable();
		$buf = str_replace("'", "\'", preg_replace("/(\r|\n)/si", "", $table->generate_output()) );

		echo sprintf("infoLayer('%s');", $buf);
	}

	public function viewAttachment($id) {
		$data = new Email_data();
		$attachment = $data->getAttachment($id);
		switch ($attachment["subtype"]) {
			case "image":
				$this->viewAttachmentImage($id);
				break;
			default:
				$filesys = new Filesys_data();

				$file = array(
					"id"      => $id,
					"subtype" => $attachment["subtype"],
					"module"  => "email",
					"name"    => $attachment["name"]
				);
				$filesys->file_preview($file);
				exit();
		}
	}

	//functions to show attachments
	public function viewAttachmentImage($id) {
		$output = new layout_output();
		$output->addTag("html");
		$output->addTag("body", array(
			"style" => "margin: 0px;"
		));
		$output->addTag("center");
			$output->addTag("img", array(
				"src" => "?dl=1&mod=email&action=download_attachment&id=".$id, "image"
			));
		$output->endTag("center");
		$output->load_javascript(self::include_dir."fitPicture.js");
		$output->endTag("body");
		$output->endTag("html");
		$output->exit_buffer();
	}

	public function emailAttachmentListXML() {
		/* simple refresh call for the upload list */
		echo "mail_upload_update_list();";
	}

	/* templates */
	public function templateList() {
		$output = new layout_output();
		$output->layout_page();

		$venster = new Layout_venster( array(
			"title" => gettext("E-mail templates"),
			"subtitle" => gettext("list")
		));

		$mailData = new Email_data();
		$list = $mailData->get_template_list();

		$view = new Layout_view();
		$view->addData($list);

		$view->addMapping( gettext("template description"), "%description" );
		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=email&action=templateEdit&id=", "%id")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("?mod=email&action=templateDelete&id=", "%id")
			)
		));

		$venster->addMenuItem(gettext("new"), "?mod=email&action=templateEdit");
		$venster->addMenuItem(gettext("back"), "javascript: history_goback();");
		$venster->generateMenuItems();

		$tbl = new Layout_table();

		$venster->addVensterData();
			$venster->addCode( $view->generate_output() );
		$venster->endVensterData();

		$history = new Layout_history();
		$output->addcode( $history->generate_history_call() );
		$output->addCode( $tbl->createEmptyTable( $venster->generate_output() ) );
		$output->layout_page_end();

		$output->exit_buffer();
	}


	public function templateEdit($id) {
		require(self::include_dir."templateEdit.php");
	}


	/* signatures */
	public function signatureList($user_id=0) {
		if (!$user_id) $user_id = $_SESSION["user_id"];
		$output = new layout_output();
		$output->layout_page();

		$venster = new Layout_venster( array(
			"title" => gettext("E-mail signatures"),
			"subtitle" => gettext("list")
		));

		$mailData = new Email_data();
		$list = $mailData->get_signature_list("", $user_id);

		$view = new Layout_view();
		$view->addData($list);

		$view->addMapping( gettext("email"), "%email" );
		$view->addMapping( gettext("description"), "%subject" );
		$view->addMapping( gettext("signature"), "%signature" );
		$view->addMapping( gettext("full name"), "%realname" );
		$view->addMapping( gettext("company name"), "%companyname" );


		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=email&action=signatureEdit&id=", "%id", "&user_id=", $user_id)
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=email&action=signatureDelete&id=", "%id", "&user_id=", $user_id, "';")
			)
		));

		$venster->addMenuItem(gettext("new"), "?mod=email&action=signatureEdit");
		$venster->addMenuItem(gettext("back"), "javascript: history_goback();");
		$venster->generateMenuItems();

		$tbl = new Layout_table();

		$venster->addVensterData();
			$venster->addCode( $view->generate_output() );
		$venster->endVensterData();

		$history = new Layout_history();
		$output->addcode( $history->generate_history_call() );

		$output->addCode( $tbl->createEmptyTable( $venster->generate_output() ) );
		$output->layout_page_end();

		$output->exit_buffer();
	}


	public function signatureEdit($id, $user_id=0) {
		require(self::include_dir."signatureEdit.php");
	}

	/* signatures */
	public function filterList() {
		$output = new layout_output();
		$output->layout_page();

		$venster = new Layout_venster( array(
			"title" => gettext("E-mail filters"),
			"subtitle" => gettext("list")
		));

		$mailData = new Email_data();
		$list = $mailData->get_filter_list();

		$view = new Layout_view();
		$view->addData($list);

		$view->addMapping( gettext("priority"), "%priority" );
		$view->addMapping( gettext("sender address"), "%sender" );
		$view->addMapping( gettext("receipient address"), "%receipient" );
		$view->addMapping( gettext("subject"), "%subject" );
		$view->addMapping( gettext("to folder"), "%folder_name" );


		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=email&action=filterEdit&id=", "%id")
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("?mod=email&action=filterDelete&id=", "%id")
			)
		));

		$venster->addMenuItem(gettext("new"), "?mod=email&action=filterEdit");
		$venster->addMenuItem(gettext("back"), "javascript: history_goback();");
		$venster->generateMenuItems();

		$tbl = new Layout_table();

		$venster->addVensterData();
			$venster->addCode( $view->generate_output() );
		$venster->endVensterData();

		$history = new Layout_history();
		$output->addcode( $history->generate_history_call() );

		$output->addCode( $tbl->createEmptyTable( $venster->generate_output() ) );
		$output->layout_page_end();

		$output->exit_buffer();
	}

	public function filterEdit($id) {
		require(self::include_dir."filterEdit.php");
	}

	public function emailMediaGallery() {
		$output = new Layout_output();
		$output->addTag("html");
		$output->addTag("body");
		$output->addTag("style", array("type"=>"text/css"));
			$output->addCode("body, a { font-family: arial, serif; font-size: 11px; color: black; background-color: white;");
		$output->endTag("style");
		$output->start_javascript();
			$output->addCode("
				function setImage(id) {
					var str = '';
					str = str.concat('".$GLOBALS["covide"]->webroot."index.php?mod=email&action=download_attachment&dl=1&view_only=1&newcid=', id, '&id=', id);

					parent.document.getElementById('f_url').value = str;
					parent.onPreview();
				}
			");
		$output->end_javascript();

		$email_data = new Email_data();
		$data = $email_data->attachments_list($_REQUEST["mail_id"]);
		foreach ($data as $k=>$v) {
			if ($v["is_image"]) {
				$output->addTag("li", array("type" => "circle"));
				$output->insertTag("a", $v["name"], array("href" => sprintf("javascript: setImage('%d')", $v["id"])));
			}
		}
		$output->endTag("body");
		$output->endTag("html");
		$output->exit_buffer();
	}

	public function selectCla() {
		$output = new Layout_output();
		$output->layout_page("templates", 1);

		$settings = array(
			"title"    => gettext("Email"),
			"subtitle" => gettext("pick classification(s)")
		);

		$output->addTag("form", array(
			"id"     => "velden",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "email");
		$output->addHiddenField("action", "selectClaAddress");
		$output->addHiddenField("target_type", $_REQUEST["target_type"]);
		$output->addHiddenField("field", $_REQUEST["field"]);
		$output->addHiddenField("current_selection", "");

		$output->start_javascript();
			$output->addCode("
				document.getElementById('current_selection').value = opener.document.getElementById('".$_REQUEST["field"]."').value;
			");
		$output->end_javascript();


		$classification = new Classification_output();

		$output_alt = new Layout_output();
		$output_alt->insertAction("back", gettext("back"), "javascript: window.close();");
		$output_alt->insertAction("forward", gettext("next"), "javascript: document.getElementById('velden').submit();");

		$venster = new Layout_venster($settings, 1);
		$venster->addVensterData();
			$venster->addCode( $classification->select_classification("", $output_alt->generate_output() ) );
		$venster->endVensterData();

		$placeholder = new Layout_table();
		$output->addCode ( $placeholder->createEmptyTable($venster->generate_output()) );

		$output->layout_page_end();
		$output->exit_buffer();

	}

	public function selectClaAddress() {

		$address_data = new Address_data();
		$email_data   = new Email_data();

		$list = $address_data->getRelationsList( array("addresstype"=>$_REQUEST["addresstype"], "nolimit"=>1 ));
		$cols = array("business_email", "email", "personal_email");

		$data = array();
		foreach ($list["address"] as $k=>$v) {
			if ($_REQUEST["addresstype"] == "bcards") {
				foreach ($cols as $cc) {
					if ($email_data->validateEmail($v[$cc]) && !$data[$v["id"]]) {
						$data[$v["id"]] = $v[$cc];
					}
				}
			} else {
				if ($email_data->validateEmail($v["email"])) {
					$data[$v["id"]] = $v["email"];
				}
			}
		}
		natcasesort($data);
		$current_selection = explode(",", str_replace(" ", "", $_REQUEST["current_selection"]));
		$emails = array_merge($current_selection, $data);
		$emails = array_unique($emails);
		foreach ($emails as $k=>$v) {
			if (!$v) {
				unset($emails[$k]);
			}
		}

		$output = new Layout_output();
		$output->layout_page("email", 1);

		$output->start_javascript();
			$output->addCode("
				opener.document.getElementById('".$_REQUEST["field"]."').value = '".implode(", ", $emails)."';
				setTimeout('window.close();', 100);
			");
		$output->end_javascript();


		$output->layout_page_end();
		$output->exit_buffer();
	}

	/* signatures */
	public function permissionsList($user_id=0) {
		if (!$user_id) $user_id = $_SESSION["user_id"];
		$output = new layout_output();
		$output->layout_page();

		$venster = new Layout_venster( array(
			"title" => gettext("E-mail"),
			"subtitle" => gettext("share folders")
		));

		$mailData = new Email_data();
		$list = $mailData->get_permissions_list($user_id);

		$view = new Layout_view();
		$view->addData($list);

		$view->addMapping( gettext("foldername"), "%%complex_folder" );
		$view->addMapping( gettext("users"), "%h_users" );

		$view->addMapping( " ", "%%complex_actions" );

		$view->defineComplexMapping("complex_folder", array(
			array(
				"type" => "action",
				"src"  => "view"
			),
			array(
				"text" => array(" ", "%h_folder")
			)
		));

		$view->defineComplexMapping("complex_actions", array(
			array(
				"type" => "action",
				"src"  => "edit",
				"alt"  => gettext("edit"),
				"link" => array("?mod=email&action=permissionsEdit&id=", "%id", "&user_id=", $user_id)
			),
			array(
				"type" => "action",
				"src"  => "delete",
				"alt"  => gettext("delete"),
				"link" => array("javascript: if (confirm(gettext('Weet u zeker dat u deze entry wilt verwijderen?'))) document.location.href='index.php?mod=email&action=permissionsDelete&id=", "%id", "&user_id=", $user_id, "';")
			)
		));

		$venster->addMenuItem(gettext("new"), "?mod=email&action=permissionsEdit&user_id=".$_REQUEST["user_id"]);
		$venster->addMenuItem(gettext("back"), "javascript: window.close();");
		$venster->generateMenuItems();

		$tbl = new Layout_table();

		$venster->addVensterData();
			$venster->addCode( $view->generate_output() );
		$venster->endVensterData();

		$history = new Layout_history();
		$output->addcode( $history->generate_history_call() );

		$output->addCode( $tbl->createEmptyTable( $venster->generate_output() ) );
		$output->layout_page_end();

		$output->exit_buffer();
	}

	public function permissionsEdit($id, $user_id=0) {
		require(self::include_dir."permissionsEdit.php");
	}
}
?>
