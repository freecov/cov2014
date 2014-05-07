<?php
/**
 * Covide Email module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
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

	/* __construct {{{ */
	public function __construct() {
		$this->output="";
	}
	/* }}} */
	/* emailSelectFromPrepare {{{ */
	/**
	 * Prepare layer with email addresses and predefined content
	 *
	 * @param int $use_new_window if set opens a new window object
	 * @return string Html code for the layer where you can select email addresses
	 */
	public function emailSelectFromPrepare($use_new_window=0) {
		require(self::include_dir."emailSelectFromPrepare.php");
		return $buf;
	}
	/* }}} */
	/* emailSelectFrom {{{ */
	/**
	 * Select email address/predefined content in the prepared layer
	 *
	 * @param int $address_id The address id to link the mail to
	 * @param string $address_type The type of address where the address_id is from
	 */
	public function emailSelectFrom($address_id="", $address_type="") {
		require(self::include_dir."emailSelectFrom.php");
	}
	/* }}} */
	/* emailList {{{ */
	/**
	 * Show list of emails in a folder
	 *
	 * @param int $folder_id The folder to show
	 * @param int $msg message to show in a javascript alert when the list is loaded
	 */
	public function emailList($folder_id="", $msg="") {
		require(self::include_dir."emailList.php");
	}
	/* }}} */
	/* emailOpen {{{ */
	/**
	 * Show specific email
	 */
	public function emailOpen() {
		require(self::include_dir."emailOpen.php");
	}
	/* }}} */
	/* emailCompose {{{ */
	/**
	 * Show compose screen for a new email
	 *
	 * @param int $id The concept id to edit
	 */
	public function emailCompose($id) {
		require(self::include_dir."emailCompose.php");
	}
	/* }}} */
	/* emailGetFromList {{{ */
	public function emailGetFromList() {
		require(self::include_dir."emailGetFromList.php");
	}
	/* }}} */
	/* emailFrom {{{ */
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
	/* }}} */
	/* parseEmailCheckbox {{{ */
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
	/* }}} */
	/* folderMove {{{ */
	public function folderMove() {
		require(self::include_dir."folderMove.php");
	}
	/* }}} */
	/* selectionMove {{{ */
	public function selectionMove() {
		require(self::include_dir."selectionMove.php");
	}
	/* }}} */
	/* viewHtml {{{ */
	/**
	 * View html part of an email
	 *
	 * @param int $no_filter if set, dont filter external images
	 * @param int $text if set, show text version of the mail
	 * @param int $return if set, return the data instead of printing it
	 * @return string the html data to show. Only when the $return param is set.
	 */
	public function viewHtml($no_filter=0, $text=0, $return=0) {
		$mailData = new Email_data();
		$data = $mailData->getEmailById($_REQUEST["id"]);

		if (!$text) {
			$body =& $data[0]["body_html"];
		} else {
			$body =& $data[0]["body_hl"];
			/* filter links inside output from html2text */
			$ret = explode("\n", $body);
			$links = 0;
			foreach ($ret as $k=>$line) {
				if (trim($line) == "Links:")
					$links = 1;

				if ($links) {
					if (preg_match("/\d{1,}\. ((((http(s){0,1})|(ftp)):\/\/)|(mailto:))/si", $line)) {
						$link = preg_replace("/\d{1,}\. /si", "", $line);
						$line = str_replace("mailto:", "mailto - ", $line);
						$link = sprintf("<a target=\"_new\" href=\"%s\">%s</a>", $link, $line);
						$ret[$k] = $link;
					}
				}
			}
			$body = implode("\n", $ret);
		}

		$body = preg_replace("/<title[^>]*?>.*?<\/title>/sxi", "", $body);
		$body = $mailData->stylehtml($body, 1);

		$body = preg_replace("/ target=\"[^\"]*?\"/sxi", "", $body);
		preg_match_all("/<a ([^>]*?)>/sxi", $body, $matches);
		foreach ($matches[0] as $k=>$v) {
			if (!preg_match("/ href=(\"|')mailto:/sxi", $v)) {
				$repl = preg_replace("/<a /sxi", "<a target=\"_blank\" ", $v);
				$body = str_replace($v, $repl, $body);
			}
		}
		//Pattern building across multiple lines to avoid page distortion.
		$pattern  = "/((@import\s+[\"'`]([\w:?=@&\/#._;-]+)[\"'`];)|";
		$pattern .= "(:\s*url\s*\([\s\"'`]*([\w:?=@&\/#._;-]+)";
		$pattern .= "([\s\"'`]*\))|<[^>]*\s+(src|href|url|background)\=[\s\"'`]*";
		$pattern .= "([\w:?=@&\/#._;-]+)[\s\"'`]*[^>]*>))/i";
		//End pattern building.
		preg_match_all ($pattern, $body, $matches);
		foreach ($matches[0] as $v) {
			if (preg_match("/^<a[^>]*?>/sxi", $v)) {
				$r = preg_replace("/='([^']*?)'/sx", "=\"$1\"", $v);
				preg_match_all("/ target=\"[^\"]*?\"/sxi", $r, $rm);

				if (count($rm[0]) > 1) {
					$r = preg_replace("/ target=\"[^\"]*?\"/sxi", "", $r);
					$r = preg_replace("/^<a /si", "<a target=\"_blank\" ", $r);
				}
				$body = str_replace($v, $r, $body);
			}
		}
		if (!$no_filter) {
			$output_js = new Layout_output();
			$output_js->start_javascript();
				$output_js->addCode(" parent.document.getElementById('js_show_inline').style.display = 'block'; ");
			$output_js->end_javascript();

			/* scan for external images and tracking items */
			//preg_match_all("/ src=('|\")([^('|\")]*?)('|\")/sxi", $body, $matches);

			foreach ($matches[0] as $v) {
				if (preg_match("/^\: url\(.*\)$/si", $v)) {
					$v = preg_replace("/^\: url\((.*)\)$/si", "$1", $v);
					$matches[8][] = trim($v);
					$matches[7][] = "css";
				}
			}
			foreach ($matches[7] as $k=>$v) {
				if (!in_array($matches[7][$k], array("src", "background", "css"))) {
					unset($matches[8][$k]);
				}
			}
			$matches[8] = array_unique($matches[8]);
			foreach ($matches[8] as $k=>$v) {
				if (trim($v)) {
					if (preg_match("/^((http)|(https)|(ftp)|(file)|(res)){0,1}:\/\//sxi", $v)) {
						/* is a full link */
						$check = substr($v, 0, strlen($GLOBALS["covide"]->webroot));
						if ($check != $GLOBALS["covide"]->webroot) {
							/* is external */
							$ext_found++;
							$body = str_replace($v, $this->notfound_image."?".trim($v), $body);
						}
					}
				}
			}

			if ($ext_found >= 1)
				$body = preg_replace("/(<body[^>]*?>)/sxi", "$1".$output_js->generate_output(), $body);
		} else {
			$body = preg_replace("/(<body[^>]*?>)/sxi", "$1"."\n<script language='Javascript1.2' type='text/javascript'>window.onload = function() { if (parent) { parent.mail_resize_frame(); } }</script>\n", $body);
		}
		/* scan for mailto links */
		preg_match_all("/ href=('|\")([^('|\")]*?)('|\")/sxi", $body, $matches);
		$len = strlen($GLOBALS["covide"]->webroot);
		foreach ($matches[2] as $k=>$v) {
			if (preg_match("/ href=(\"|')mailto:/sxi", $matches[0][$k])) {
				$repl = preg_replace("/^mailto:/sxi", "", $v);
				if ($matches[3][$k] == "\"")
					$repl = sprintf("javascript: parent.handleMailtoLinks('%s');", $repl);
				else
					$repl = sprintf("javascript: parent.handleMailtoLinks(\"%s\");", $repl);

				$body = str_replace($v, $repl, $body);
			}
		}

		/* apply newline compression */
		$body = explode("<br>", $body);
		foreach ($body as $k=>$v) {
			$body[$k] = trim($v);
		}
		$body = implode("<br>", $body);
		$body = preg_replace("/(<br>){3,}/six", "<!-- crlf --><br><br>", $body);

		$body = wordwrap($body, 997, "\n", 1);

		$output = new Layout_output();
		$output->addCode( $body );

		if ($return) {
			return $output->generate_output();
		} else {
			header("Content-type: text/html; charset=UTF-8");
			$output->exit_buffer();
		}
	}
	/* }}} */
	/* emailUploadView {{{ */
	/**
	 * Show attachment upload field
	 */
	public function emailUploadView() {
		$output = new Layout_output();
		$output->layout_page("upload", 1);
		$output->addCode(1);
		$output->insertBinaryField("binFile");
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
	/* getSharedFolderList {{{ */
	/**
	 * Get a list of folders others are sharing with the current user
	 *
	 * @param int $current Highlight this folder
	 * @return string the html you can use to print this list
	 */
	public function getSharedFolderList($current) {

		$email_data = new Email_data();
		$folders = $email_data->getSharedFolderAccess($_SESSION["user_id"]);

		$table = new Layout_table(array("cellspacing"=>1), 1);
		foreach ($folders as $f) {
			$flag++;
			$v = $email_data->getFolder($f["folder_id"], 1);
			if (!$v["id"]) {
				$q = sprintf("select count(*) from mail_folders where id = %d", $f["folder_id"]);
				$res2 = sql_query($q);
				if (sql_result($res2,0) == 0) {
					$q = sprintf("delete from mail_permissions where folder_id = %d", $f["folder_id"]);
					sql_query($q);
				}
			}
			if ($v["id"]) {
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
							if ($f["name"])
								$n = $f["name"];
							else
								$n = $v["name"];

							if ($f["username"] != "archiefgebruiker")
								$n.= " (".$f["username"].")";

							$table->insertLink($n, array(
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
		}
		$table->endTable();
		if ($flag) {
			return $table->generate_output();
		} else {
			return "";
		}
	}
	/* }}} */
	/* getFolderList {{{ */
	/**
	 * Format a list of folders
	 *
	 * @param array $folders folderdata to format
	 * @param int $current The folder to highlight (we are in this folder)
	 * @param int $move_folder
	 * @return string the html code for the folderlist
	 */
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
	/* }}} */
	/* getSelectList {{{ */
	/**
	 * Format a select field with all folders user has access to
	 *
	 * @param string $name The name of the selectbox
	 * @param array $folders The folderdata
	 * @param int $current The current folder to automagically select in the selectbox
	 * @param array $settings additional settings for the select field, is sent verbatim to the addSelectField method
	 * @param array $folders_shared optional array for shared mailfolders
	 * @return string html code for the selectbox
	 */
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
	/* }}} */
	/* emailPrint {{{ */
	/**
	 * Print an email
	 */
	public function emailPrint() {
		$email_data = new Email_data();
		$mdata      = $email_data->getEmailById($_REQUEST["id"]);

		/* start output buffer routines */
		$output = new Layout_output();
		//$output->layout_page( gettext("Email"), 1);
		$output->addTag("html");
		$output->addTag("body");
		$output->addTag("style", array("type" => "text/css"));
			$output->addCode(" #venster_onderdeel { font-size: 12pt !important; font-weight: bold; } ");
		$output->endTag("style");

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
		$prefix.= "  <td class='cell1'>".gettext("from").": </td><td class='cell2'>".htmlentities($mdata[0]["sender_emailaddress"])."</td>\n";
		$prefix.= " </tr><tr class='head2'>\n";
		$prefix.= "  <td class='cell1'>".gettext("to").": </td><td class='cell2'>".htmlentities($mdata[0]["to"])."</td>\n";
		$prefix.= " </tr><tr class='head2'>\n";
		$prefix.= "  <td class='cell1'>".gettext("subject").": </td><td class='cell2'>".htmlentities($mdata[0]["subject"])."</td>\n";
		$prefix.= " </tr><tr class='head2'>\n";
		if ($mdata[0]["cc"]) {
			$prefix.= "  <td class='cell1'>".gettext("cc").": </td><td class='cell2'>".htmlentities($mdata[0]["cc"])."</td>\n";
			$prefix.= " </tr><tr class='head2'>\n";
		}
		if ($mdata[0]["bcc"]) {
			$prefix.= "  <td class='cell1'>".gettext("bcc").": </td><td class='cell2'>".htmlentities($mdata[0]["bcc"])."</td>\n";
			$prefix.= " </tr><tr class='head2'>\n";
		}
		$prefix.= "  <td class='cell1'>".gettext("date").": </td><td class='cell2'>".$mdata[0]["h_date"]."</td>\n";
		$prefix.= " </tr>";
		if ($mdata[0]["attachments_ids"]) {
			$prefix.= "<tr class='head2'>\n  <td class='cell1'>".gettext("attachment").":</td>\n";
				$attachmentIDs = explode(",", $mdata[0]["attachments_ids"]);
				foreach($attachmentIDs as $attachmentIDs_value) {
					if($mdata[0]["attachments_count"] > 1 && $firstRoundAttachments != 0) {
						$prefix.= "<tr class='head2'>\n  <td class='cell1'>&nbsp;</td><td class='cell2'>" . $mdata[0]["attachments"][$attachmentIDs_value]["name"] ."</td>\n";
					} else {
						$prefix.= "  <td class='cell2'>" . $mdata[0]["attachments"][$attachmentIDs_value]["name"] ."</td>\n";
						$firstRoundAttachments = 1;
					}
		$prefix.= " </tr>";
				}
		}
		$prefix.= "\n</table>\n";
		$prefix.= "<br>\n";

		$prefix.= "<div style='border-left: 1px solid blue; padding-left: 6px;'>\n";
		$suffix = "</div>";

		$body = trim($this->viewHtml(1, $mdata[0]["is_text"], 1));

		$body = preg_replace("/<title>[^>]*?>/sxi", "", $body);
		$body = $email_data->stripBodyTags($body);

		$venster->addCode($prefix.$body);

		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		unset($venster);
		$output->start_javascript();
			$output->addCode("
				window.print();
				setTimeout('window.close();', 2000);
			");
		$output->end_javascript();
		//$output->layout_page_end();
		$output->endTag("body");
		$output->endTag("html");
		$output->exit_buffer();

	}
	/* }}} */
	/* emailShowTracking {{{ */
	/**
	 * Show tracking stats for an email
	 */
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
				$view->addMapping(gettext("to"), "%h_to", "", "", 1, "", 1);
				$view->addMapping(gettext("subject"), "%subject");
				$view->addMapping(gettext("cc"), "%h_cc", "", "", 1, "", 1);
				$view->addMapping(gettext("bcc"), "%h_bcc", "", "", 1, "", 1);
				$view->addMapping(gettext("date sent"), "%h_date", "", "list_hidden");
				$view->addMapping(gettext("classification"), "%h_classifications_target");
				$view->addMapping(gettext("classification selection"), "%h_classifications_type", "", "", "", "", 1);
				$view->addMapping(gettext("positive classifications"), "%h_classifications_positive", "", "list_hidden", "", "", 1);
				$view->addMapping(gettext("negative classifications"), "%h_classifications_negative", "", "list_hidden", "", "", 1);
				$tbl->addCode( $view->generate_output_vertical() );
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("colspan"=>2));
				$tbl->addTag("br");

				$items = $email_data->get_tracker_items($_REQUEST["id"]);
				$view = new Layout_view();
				$view->addData($items["list"]);
				$view->addMapping(gettext("recipient"), "%email");
				$view->addMapping(gettext("read first/last"), array(
					"%read_first_h",
					"\n",
					"%read_last_h"
				));
				$view->addMapping(gettext("number"), array("%count", "x gelezen"));
				$view->addMapping(gettext("software"), "%%complex_agents_h", "", "", "", "", 1);
				$view->addMapping(gettext("hyperlinks"), "%hyperlinks_h", "", "", 1, "", 1);
				$view->addMapping(" ", "%%complex_msg", "", "", "", "", 1);

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
	/* }}} */
	/* emailHeaderInfo {{{ */
	/**
	 * Show header info for a mail
	 */
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
	/* }}} */
	/* stripMailBody {{{ */
	/**
	 * strip body start and end tag and everything before the body tag and everything after the body tag
	 *
	 * @param string $body html body
	 * @return string the html without body tags
	 */
	public function stripMailBody($body) {
		$body = preg_replace("/^.*<body[^>]*?>/sxi","",$body);
		$body = preg_replace("/<\/body>.*$/sxi","",$body);
		return $body;
	}
	/* }}} */
	/* insertFromToHeader {{{ */
	/**
	 * Insert block with from, to etc when forwarding/replying email
	 *
	 * @param int $html if set generate html string, if not set generate text string
	 * @param array $maildata The original email
	 * @param int $hide_orig_msg if set dont put the 'Original message' line in this block
	 */
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
	/* }}} */
	/* showInfo {{{ */
	/**
	 * Show basic info about an email
	 */
	public function showInfo() {
		$mailData = new Email_data();
		$addressData = new Address_data();

		$data = $mailData->getEmailById($_REQUEST["id"]);
		$mdata =& $data[0];

		$fields[gettext("from")]              = $mdata["sender_emailaddress_h"];
		$fields[gettext("to")]                = $mdata["to"];
		$fields[gettext("subject")]           = $mdata["subject"];
		$fields[gettext("cc")]                = $mdata["cc"];
		$fields[gettext("bcc")]               = $mdata["bcc"];
		$fields[gettext("reply to")]          = $mdata["replyto"];
		$fields[gettext("date sent")]         = $mdata["h_date"];
		$fields[gettext("date received")]     = $mdata["h_date_received"];
		$fields[gettext("read confirmation")] = $mdata["readconfirm"];
		$fields[gettext("priority")]          = $mdata["prioriteit"];
		$fields[gettext("description")]       = $mdata["description"];
		$fields[gettext("contact")]           = $addressData->getAddressNameById($mdata["address_id"]);

		$conversion = new Layout_conversion();
		$table = new Layout_table();
		foreach ($fields as $k=>$v) {
			if ($v) {
				$table->addTableRow();
					$table->addTableData("", "header");
						$table->addCode($k);
					$table->endTableData();
					$table->addTableData("", "data");
						$table->addCode($conversion->convertHtmlTags($v));
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
	/* }}} */
	/* viewAttachment {{{ */
	/**
	 * generate preview link for an attachment
	 *
	 * @param int $id The attachment id
	 */
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
	/* }}} */
	/* viewAttachmentImage {{{ */
	/**
	 * functions to show attachments
	 */
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
	/* }}} */
	/* emailAttachmentListXML {{{ */
	public function emailAttachmentListXML() {
		/* simple refresh call for the upload list */
		echo "mail_upload_update_list();";
	}
	/* }}} */
	/* templateList {{{ */
	/**
	 * view list of templates 
	 */
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
	/* }}} */
	/* templateEdit {{{ */
	/**
	 * Edit a template
	 *
	 * @param int $id The template id
	 */
	public function templateEdit($id) {
		require(self::include_dir."templateEdit.php");
	}
	/* }}} */
	/* templateOutput {{{ */
	public function templateOutput($id) {
		require(self::include_dir."templateOutput.php");
	}
	/* }}} */
	/* signatureList {{{ */
	/**
	 * show list of signatures
	 *
	 * @param int $user_id The userid to list the signatures for
	 */
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
		#$view->addMapping( gettext("signature"), "%signature" );
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
	/* }}} */
	/* signatureEdit {{{ */
	/**
	 * Edit a signature
	 *
	 * @param int $id the signature id
	 * @param int $user_id The user that owns this signature
	 */
	public function signatureEdit($id, $user_id=0) {
		require(self::include_dir."signatureEdit.php");
	}
	/* }}} */
	/* filterList {{{ */
	/**
	 * Show list of mailfilters
	 */
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
		$view->addMapping( gettext("recipient address"), "%recipient" );
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
	/* }}} */
	/* filterEdit {{{ */
	/**
	 * Edit a mailfilter
	 *
	 * @param int $id The filter to edit
	 */
	public function filterEdit($id) {
		require(self::include_dir."filterEdit.php");
	}
	/* }}} */
	/* emailMediaGallery {{{ */
	public function emailMediaGallery() {
		$output = new Layout_output();
		if ($_REQUEST["fullhtml"]) {
			$output->layout_page("email", 1);

			$venster = new Layout_venster(array(
				"title" => gettext("E-mail"),
				"subtitle" => gettext("Add inline image")
			));
			$venster->addVensterData();

			$venster->start_javascript();
				$venster->addCode("
					function setImage(id) {
						var str = '';
						str = str.concat('".$GLOBALS["covide"]->webroot."?mod=email&action=download_attachment&dl=1&view_only=1&newcid=', id, '&id=', id);

						parent.document.getElementById('f_url').value = str;
						parent.onPreview();
					}
				");
			$venster->end_javascript();
			$email_data = new Email_data();
			$data = $email_data->attachments_list($_REQUEST["mail_id"]);
			/* javascript: setImage('%d') */
			foreach ($data as $k=>$v) {
				if ($v["is_image"])
					$i++;
				else
					unset($data[$k]);
			}
			if (!$i) {
				$venster->addCode(gettext("No email attachment found, please add an attachment first to the email."));
			} else {
				$view = new Layout_view();
				$view->addData($data);
				$view->addMapping("", "%%complex_image");
				$view->addMapping(gettext("filename"), "%name");
				$view->addMapping(gettext("size"), "%h_size");
				$view->addMapping(gettext("add"), "%%complex_actions");

				$view->defineComplexMapping("complex_actions", array(
					array(
						"type"  => "action",
						"src"   => "file_attach",
						"alt"   => gettext("add"),
						"link"  => array("javascript: setImage('", "%id", "');")
					)
				));
				$view->defineComplexMapping("complex_image", array(
					array(
						"type"  => "action",
						"src"   => "ftype_image",
						"alt"   => gettext("image")
					)
				));
				$venster->addCode($view->generate_output());
			}

			$venster->endVensterData();
			$output->addCode($venster->generate_output());

		} else {

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
				$i++;
			}
			if (!$i)
				$output->addCode(gettext("No valid image attachment found, please upload an attachment first."));
			$output->endTag("body");
			$output->endTag("html");
		}
		$output->exit_buffer();
	}
	/* }}} */
	/* selectCla {{{ */
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
	/* }}} */
	/* selectClaAddress {{{ */
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
	/* }}} */
	/* permissionsList {{{ */
	/**
	 * List permissions for a user
	 *
	 * @param int $user_id The user to list the permissions of
	 */
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
		$view->addMapping( gettext("custom name"), "%name" );
		$view->setHtmlField("h_users");

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
	/* }}} */
	/* permissionsEdit {{{ */
	public function permissionsEdit($id, $user_id=0) {
		require(self::include_dir."permissionsEdit.php");
	}
	/* }}} */
	/* vacationEdit {{{ */
	/**
	 * Edit postfixadmin virtual vacation settings
	 *
	 * @param int $user_id The user to edit vacation for
	 */
	public function autoreplyEdit($user_id = 0) {
		if (!$user_id)
			$user_id = $_SESSION["user_id"];

		/* get current autoreply state */
		$email_data = new Email_data();
		$autoreply = $email_data->getAutoreplyByUserID($user_id);

		$output = new layout_output();
		$output->layout_page(gettext("Mail Autoreply"), 1);

		$output->addTag("form", array(
			"id"     => "replyform",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "email");
		$output->addHiddenField("action", "save_autoreply");
		$output->addHiddenField("autoreply[email]", $autoreply["email"]);
		$output->addHiddenField("autoreply[domain]", $autoreply["domain"]);

		$venster = new Layout_venster( array(
			"title" => gettext("Mail Autoreply"),
			"subtitle" => gettext("edit")
		));

		$venster->addVensterData();
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("active"), "", "header");
				$table->addTableData("", "data");
					$table->addCheckbox("autoreply[active]", 1, $autoreply["autoreply"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("Subject"), "", "header");
				$table->addTableData("", "data");
					$table->addTextfield("autoreply[subject]", $autoreply["subject"], array("style" => "width: 500px;"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("Body"), "", "header");
				$table->addTableData("", "data");
					$table->addTextArea("autoreply[body]", $autoreply["body"], array("style" => "width: 500px; height: 300px;"));
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(" ", "", "header");
				$table->addTableData("", "data");
					$table->insertAction("close", gettext("close window"), "javascript: window.close();");
					$table->addSpace(2);
					$table->insertAction("save", gettext("save"), "javascript: save_autoreply();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."autoreply_actions.js");
		$output->exit_buffer();
	}
	/* }}} */
}
?>
