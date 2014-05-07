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
		} elseif($return == 0) {
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
		//get usersettings
		$user_data = new User_data();
		$usersettings = $user_data->getUserDetailsById($_SESSION["user_id"]);


		$table = new Layout_table(array("cellspacing"=>1), 1);
		foreach ($folders as $f) {
			if ($usersettings["mail_hide_cmsforms"] && $f["username"] == "archiefgebruiker") continue;
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

					$table->addTableData("", ($current==$v["id"]) ? "header":"data");
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
								"alt"   => gettext("unread messages"),
								"title" => gettext("unread messages"),
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
		// Add folder "Starred"
		if ($_REQUEST["action"] == "viewStarredMail") {
			$starred_style = "header";
		} else {
			$starred_style = "data";
		}
		$table->addTableRow();
		$table->addTableData(array("style" => "padding-left: $margin"), $starred_style);
		$table->insertLink(gettext("Starred"), array("href"  => "javascript: viewStarredMail()"));
		$table->endTableData();
		$table->endTableRow();
		foreach ($folders as $k=>$v) {
			$table->addTableRow();
				$margin = ($v["level"]*10)."px;";
				$table->addTableData(array("style" => "padding-left: $margin"), ($current==$v["id"]) ? "header":"data");
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
		$output->layout_page( gettext("Email"), 1);
		/*
		$output->addTag("html");
		$output->addTag("head");
		$output->addTag("style");
			$output->addCode("body { background-color: #FFFFFF; }");
		$output->endTag("style");
		$output->endTag("head");
		$output->addTag("body");
		*/
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
		$prefix.= " <tr class='head2'>\n";
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
				window.onload = function () {
					window.print();
				}
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
	public function saveClass() {
		//get items from ajaxCall(getEmails.js)
		$cla_id = mysql_escape_string($_POST["classification"]);
		//bcards ids how are checked by user
		$idbox = mysql_escape_string($_POST["idbox"]);
		$idbox = explode(',', $idbox);
		//save classification into the database
		foreach ($idbox as $k=>$v) {
			//get current classifications from database
			$q_get = sprintf("SELECT classification FROM address_businesscards WHERE id = %d", $v);
			$res_get = sql_query($q_get);
			$current_cla = sql_result($res_get, 0);
			//save new classifications
			$new_cla = preg_replace("/\|{1,}/si", "|", $current_cla."|".$cla_id."|");
			$q = sprintf("update address_businesscards set classification = '%s' where id = %d", $new_cla, $v);
			sql_query($q);
		}
	}

	public function getEmails() {
		//get the url clicked
		$url = $_POST["url"];
		//get the id newletter
		$id = $_POST["id"];
		$email_data = new Email_data();
		$items = $email_data->get_tracker_items($id);
		$address_data = new Address_data();

		$classification_output = new Classification_output();
		$tbl = new Layout_table(array("cellspacing" => 1, "width" =>"100%", "id" => "tblEmail"));
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_header", "colspan"=>"5"));
				$tbl->addCode(gettext("relations who clicked the link:  ").$url);
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->addTableRow();
			$tbl->addTableData(array("class" => "list_header"));
				$tbl->addCode(gettext("email"));
			$tbl->endTableData();
			$tbl->addTableData(array("class" => "list_header", "style"=> "white-space:normal"));
				$tbl->addCode(gettext("selected for classification"));
			$tbl->endTableData();
			$tbl->addTableData(array("class" => "list_header", "style"=> "white-space:normal"));
				$tbl->addCode(gettext("shared classification"));
			$tbl->endTableData();
			$tbl->addTableData(array("class" => "list_header", "style"=> "white-space:normal"));
				$tbl->addCode(gettext("add classification"));
			$tbl->endTableData();
			$tbl->addTableData(array("class" => "list_header"));
				$tbl->addCode(gettext("save"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$countItems = $items['count'];
		// first classification is compared with others classification, so countItems is -1
		$countEmails = $countItems - 1;
		$address_id = array();
		$same_classification = array();
		$i = 1;
		//look for shared classifications
		if (!is_array($items["list"])) {
			$items["list"] = array();
		}
		foreach ($items["list"] as $k=>$v) {
			if (strpos($v["hyperlinks_h"], $url) !== false) {
				$address_id[] = $v["address_id"];
				$address = $v["address_id"];
				$email[] = $v["email"];
				$bcardinfo = $address_data->getAddressById($address, "bcards");
				//get the first classification and compare this classifications with others
				if ($i == 1) {
					$classifications = $bcardinfo["classification"];
					$classifications = explode("|", $classifications);
				}
				if ($i > 1) {
					$classification = $bcardinfo["classification"];
					$classification = explode("|", $classification);
					foreach ($classification as $key=>$class) {
						if (in_array($class, $classifications)) {
							$count[$class]++;
						}
						//if classifications count same as count emails then it's a shared classification
						if ($countEmails == $count[$class] && !($class == '')) {
							$same_classification[] = $class;
						}
					}
				}
				$i++;
			}
		}
		//put hidden field to show selected classification in add classification
		$tbl->addHiddenField("bcard[classification]", "");
		$same_classification  = implode("|", $same_classification);
		$bcardinfo["classification"] = $same_classification;
		$i = 0;
		foreach ($address_id as $k=>$v) {
			$tbl->addTableRow();
				$tbl->addTableData(array("class" => "list_data"));
					$tbl->addCode($email[$i]);
				$tbl->endTableData();
				$tbl->addTableData(array("class" => "list_data"));
					$tbl->insertCheckbox("idbox", $v, 1);
				$tbl->endTableData();
				if ($i == 0) {
					$tbl->addTableData(array("class" => "list_data", "rowspan"=> $countItems , "valign" => "top"));
						//get venster 'choose classifications'
						$tbl->addCode($classification_output->classification_selection("", $bcardinfo["classification"], "enabled", 1));
					$tbl->endTableData();
					$tbl->addTableData(array("class" => "list_data", "rowspan"=> $countItems , "valign" => "top"));
						//get venster 'choose classifications'
						$tbl->addCode($classification_output->classification_selection("bcardclassification"));
					$tbl->endTableData();
					$tbl->addTableData(array("id" => "savedClass", "class" => "list_data", "rowspan"=> $countItems, "valign" => "top"));
						$tbl->addTag("br");
						$tbl->insertAction("save", gettext("save"), "javascript: save_class();");
						$tbl->addTag("span", array("id"=> "loading", "style" => "display:none"));
						$tbl->endTag("span");
					$tbl->endTableData();
				}
			$tbl->endTableRow();
			$i++;
		}
		$tbl->endTable();
		$tbl = $tbl->generate_output();
		echo $tbl;
	}

	public function emailShowTracking() {

		$email_data = new Email_data();
		$mdata = $email_data->getEmailById($_REQUEST["id"]);
		$id = $_REQUEST["id"];
		//get tracker information
		$items = $email_data->get_tracker_items($_REQUEST["id"]);
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
		$output->addTag("form", array(
			"id" => "emailsclass"
		));

		$settings = array(
			"title"    => gettext("Email"),
			"subtitle" => gettext("tracking")
		);
		$venster = new Layout_venster($settings);
		unset($settings);
		$venster->addVensterData();
		$tbl = new Layout_table(array("width" =>"100%"));
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
		$tbl->endTable();

		//get number emails sends
		$readAndUnread = array();
		$countRead = 0;
		$countItems = $items['count'];
		//function count who open newletter
		for ($i=0; $i <= $countItems; $i++) {
			$readAndUnread[] =  $items["list"][$i]["count"];
			//if readAndUnread bigger is than 0 it means the recipient read the email
			if ($readAndUnread[$i] > 0) {
				$countRead ++;
			}
		}
		//count unread en read emails
		$countUnread = $countItems - $countRead;
		$prctUnread = (100*$countUnread) / $countItems;
		$prctRead = (100*$countRead) / $countItems;
		$prctTotal = $prctRead + $prctUnread;
		$prctUnread = round($prctUnread, 2);
		$prctRead = round($prctRead, 2);

		//create chart read and unread
		$pieChartRead = array(
			"chs" => "640x170",
			"cht" => "p",
			"chco" => "",
			"chtt" => gettext("Read and Unread")."(%)",
			"chd" => "t:".$prctUnread.",".$prctRead,
			"chl" => gettext("unread")." (".$prctUnread." %) |".gettext("read")." (".$prctRead." %)",
		);
		$gPieChartLinkRead = "index.php?mod=google&action=chart";
		foreach ($pieChartRead as $k => $v) {
			$gPieChartLinkRead .= "&param[".$k."]=".urlencode($v);
		}

		/* put 'general info' in div */
		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->insertTag("h1", gettext("General information"));
		$venster->addCode($tbl->generate_output());
		$venster->endTag("div");

		$tbl = new Layout_table(array("cellspacing" => 1, "width" =>"90%"));
		//row1(number)
		$tbl->addTableRow();
			//data1
			$tbl->addTableData(array("class" => "list_header", "style" =>"min-width:33%"));
			$tbl->endTableData();
			//data2
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("amount"));
			$tbl->endTableData();
			//data3
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("amount")."(%)");
			$tbl->endTableData();
		$tbl->endTableRow();
		//row2
		$tbl->addTableRow();
			//data1
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("read"));
			$tbl->endTableData();
			//data2
			$tbl->addTableData('', "data");
			$tbl->addCode($countRead);
			$tbl->endTableData();
			//data3
			$tbl->addTableData('', "data");
			$tbl->addCode($prctRead." %");
			$tbl->endTableData();
		$tbl->endTableRow();
		//row3
		$tbl->addTableRow();
			//data1
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("unread"));
			$tbl->endTableData();
			//data2
			$tbl->addTableData('', "data");
				$tbl->addCode($countUnread);
			$tbl->endTableData();
			//data3
			$tbl->addTableData('', "data");
				$tbl->addCode($prctUnread." %");
			$tbl->endTableData();
		$tbl->endTableRow();
		//row4
		$tbl->addTableRow();
			//data1
			$tbl->addTableData('', "header");
				$tbl->addCode(gettext("total"));
			$tbl->endTableData();
			//data2
			$tbl->addTableData('', "data");
				$tbl->addCode($items['count']);
			$tbl->endTableData();
			//data3
			$tbl->addTableData('', "data");
				$tbl->addCode($prctTotal." %");
			$tbl->endTableData();
		$tbl->endTableRow();
		//row 5
		$tbl->addTableRow();
			//data1
			$tbl->addTableData();
			$tbl->endTableData();
			//data2
			$tbl->addTableData();
			$tbl->endTableData();
			//data3
			$tbl->addTableData();
				$tbl->insertLink(gettext("show chart"), array("href" => "javascript: getChartRead();"));
			$tbl->endTableData();
		$tbl->endTableRow();
		$tbl->endTable();

		//function count clicked hyperlink
		$hyperlinks = array();
		foreach ($items["list"] as $k=>$v) {
			// hyperlinks_h contains the links the user clicked on, seperated by <br>
			if (!trim($v["hyperlinks_h"]) == "") {
				$hyper_arr = explode("<br>", trim($v["hyperlinks_h"]));
				foreach ($hyper_arr as $hyperlink) {
					if (trim($hyperlink)) {
						if (array_key_exists($hyperlink, $hyperlinks)) {
							$hyperlinks[$hyperlink]++;
						} else {
							$hyperlinks[$hyperlink] = 1;
						}
					}
				}
			}
		}
		$prctClicksAr = array();
		$names = array();
		$totalClicks = 0;
		foreach ($hyperlinks as $k=>$v) {
			$totalClicks = $v + $totalClicks;
		}
		foreach ($hyperlinks as $k=>$v) {
			$prctClicks = (100* $v) / $totalClicks;
			$prctClicks = round($prctClicks);
			//remove http:// or www.
			$k = str_replace("www.", "", str_replace("http://", "", $k));
			$countString = strlen($k);
			//if string had more than 39 characters remove the characters after 35
			if ($countString > 39) {
				$k = substr($k, 0, 35);
				$names[] = $k."... (".$prctClicks."%)";
			} else {
				$names[] = $k." (".$prctClicks."%)";
			}
			$prctClicksAr[] = round($prctClicks);
		}
		if (!is_array($names)) {
			$names = array();
		}
		if (!is_array($prctClicksAr)) {
			$prctClicksAr = array();
		}

		$tbl2 = new Layout_table(array("cellspacing" => 1, "width" =>"90%"));
		//row1(count clicks)
		$tbl2->addTableRow();
			//data1
			$tbl2->addTableData(array("class" => "list_header", "style" =>"min-width:27%"));
				$tbl2->addCode(gettext("hyperlink"));
			$tbl2->endTableData();
			//data2
			$tbl2->addTableData('', "header");
				$tbl2->addCode(gettext("count clicks"));
			$tbl2->endTableData();
			//data3
			$tbl2->addTableData('', "header");
				$tbl2->addCode(gettext("count clicks")."(%)");
			$tbl2->endTableData();
		$tbl2->endTableRow();
		//row2
		foreach ($hyperlinks as $k=>$v) {
				$prctClicks = 100 * $v / $totalClicks;
				$prctClicks = round($prctClicks);
				$tbl2->addTableRow();
					//data1
					$tbl2->addTableData('', "data");
						$tbl2->insertLink($k, array("href" => $k, "target" => "_blank"));
					$tbl2->endTableData();
					//data2
					$tbl2->addTableData('', "data");
						$tbl2->insertLink($v.' clicks', array("href" => "javascript: getEmails('".$k."',".$id.");"));
					$tbl2->endTableData();
					//data3
					$tbl2->addTableData('', "data");
						$tbl2->addCode($prctClicks." %");
					$tbl2->endTableData();
				$tbl2->endTableRow();
		}
		$tbl2->addTableRow();
			//data1
			$tbl2->insertTableData("&nbsp;");
			//data2
			$tbl2->insertTableData("&nbsp;");
			//data3
			$tbl2->addTableData();
				$tbl2->insertLink(gettext("show chart"), array("href" => "javascript: getChart();"));
			$tbl2->endTableData();
		$tbl2->endTableRow();
		$tbl2->endTable();
		//create chart percent clicks hyperlink
		$pieChart = array(
			"chs" => "640x170",
			"cht" => "p",
			"chco" => "",
			"chtt" => gettext("Count clicks(%)"),
			"chd" => "t:".implode(",", $prctClicksAr),
			"chl" => implode("|", $names),
		);
		$gPieChartLink = "index.php?mod=google&action=chart";
		foreach ($pieChart as $k => $v) {
			$gPieChartLink .= "&param[".$k."]=".urlencode($v);
		}

		$tbl4 = new Layout_table(array("id" => "pieChart"));
		$tbl4->addTableRow();
			$tbl4->addTableData('', "");
				$tbl4->addTag("img", array("src"=>$gPieChartLink, "id"=> "pieChartLink"));
				$tbl4->endTag("img");
				$tbl4->addTag("img", array("src"=>$gPieChartLinkRead, "style"=>"display:none;", "id"=> "pieChartRead"));
				$tbl4->endTag("img");
			$tbl4->endTableData();
		$tbl4->endTableRow();
		$tbl4->addTableRow();
			$tbl4->addTableData();
				$tbl4->addSpace();
				$tbl4->addTag("br");
				$tbl4->addSpace();
				$tbl4->addTag("br");
			$tbl4->endTableData();
		$tbl4->endTableRow();
		$tbl4->endTable();

		/* put 'Tracking info' and charts in div */
		$venster->addTag("div", array("class" => "hence_campaign"));
		$venster->insertTag("h1", gettext("Tracking information"));
		$venster->addTag("div", array("class" => "hence_left_email"));
		$venster->addCode($tbl->generate_output());
		$venster->addSpace();
		$venster->addCode($tbl2->generate_output());
		$venster->addSpace();
		$venster->endTag("div");
		$venster->addTag("div", array("class" => "div_right_chart"));
		$venster->addCode($tbl4->generate_output());
		$venster->endTag("div");
		$venster->endTag("div");

		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		unset($venster);
		$output->endTag("form");
		$output->layout_page_end();
		$output->load_javascript("classes/email/inc/getEmails.js");
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
		$output = new Layout_output();
		$output->insertAction("info", gettext("view"), sprintf("index.php?mod=email&action=open&id=%d", $mdata["id"]));
		$fields[gettext("actions")] = $output->generate_output();
		unset($output);

		$conversion = new Layout_conversion();
		$table = new Layout_table();
		foreach ($fields as $k=>$v) {
			if ($v) {
				$table->addTableRow();
					$table->addTableData("", "header");
						$table->addCode($k);
					$table->endTableData();
					$table->addTableData("", "data");
						if ($k == gettext("actions")) {
							$table->addCode($v);
						} else {
							$table->addCode($conversion->convertHtmlTags($v));
						}
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

		$tbl = new Layout_table(array("width" => "100%"));

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
			"title" => gettext("E-mail predefined content"),
			"subtitle" => gettext("list")
		));

		$mailData = new Email_data();
		$list = $mailData->get_signature_list("", $user_id);

		$view = new Layout_view();
		$view->addData($list);

		$view->addMapping( gettext("email"), "%email" );
		$view->addMapping( gettext("description"), "%subject" );
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

		$venster->addVensterData();
			$venster->addCode( $view->generate_output() );
		$venster->endVensterData();

		$history = new Layout_history();
		$output->addcode( $history->generate_history_call() );

		$output->addCode($venster->generate_output());
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
		$output->layout_page(gettext("E-mail filters"), 1);

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
		$venster->generateMenuItems();

		$tbl = new Layout_table(array("width" => "100%"));

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
				document.getElementById('current_selection').value = parent.document.getElementById('".$_REQUEST["field"]."').value;
			");
		$output->end_javascript();


		$classification = new Classification_output();

		$output_alt = new Layout_output();
		$output_alt->insertAction("back", gettext("back"), "javascript: closepopup();");
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
				parent.document.getElementById('".$_REQUEST["field"]."').value = '".implode(", ", $emails)."';
				setTimeout('closepopup();', 100);
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
		$output->layout_page("email", 1);

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
		$venster->generateMenuItems();

		$tbl = new Layout_table(array("width" => "100%"));

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
	public function pa_autoreplyEdit($user_id = 0) {
		if (!$user_id)
			$user_id = $_SESSION["user_id"];

		/* get current autoreply state */
		$email_data = new Email_data();
		$autoreply = $email_data->pa_getAutoreplyByUserID($user_id);

		$output = new layout_output();
		$output->layout_page(gettext("Mail Autoreply"), 1);

		$output->addTag("form", array(
			"id"     => "replyform",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "email");
		$output->addHiddenField("action", "save_pa_autoreply");
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
					$table->addSpace(2);
					$table->insertAction("save", gettext("save"), "javascript: save_pa_autoreply();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
		$venster->endVensterData();

		$output->addCode($venster->generate_output());
		$output->load_javascript(self::include_dir."pa_autoreply_actions.js");
		$output->exit_buffer();
	}
	/* }}} */
	/* editAutoreply {{{ */
	/**
	 * Edit autoreply settings.
	 * This is the autoreply that Covide will sent
	 *
	 * @param int $user_id The userid to edit the autoreply for
	 */
	public function editAutoreply($user_id) {
		// grab autoreply for this user
		$email_data = new Email_data();
		$autoreply = $email_data->getAutoreplyByUserId($user_id);
		$start_day = $start_month = $start_year = 0;
		$end_day = $end_month = $end_year = 0;
		if ($autoreply["timestamp_start"]) {
			$start_day = date("d", $autoreply["timestamp_start"]);
			$start_month = date("m", $autoreply["timestamp_start"]);
			$start_year = date("Y", $autoreply["timestamp_start"]);
		}
		if ($autoreply["timestamp_end"]) {
			$end_day = date("d", $autoreply["timestamp_end"]);
			$end_month = date("m", $autoreply["timestamp_end"]);
			$end_year = date("Y", $autoreply["timestamp_end"]);
		}

		$days = array("0" => "---");
		for ($i = 1; $i <= 31; $i++) {
			$days[$i] = $i;
		}
		$months = array("0" => "---");
		for ($i = 1; $i <= 12; $i++) {
			$months[$i] = $i;
		}
		$years = array("0" => "---");
		for ($i = date("Y")-2; $i <= date("Y")+2; $i++) {
			$years[$i] = $i;
		}

		$output = new Layout_output();
		$output->layout_page(gettext("Mail Autoreply"), 1);
		$output->addTag("form", array(
			"id"     => "replyform",
			"action" => "index.php",
			"method" => "post"
		));
		$output->addHiddenField("mod", "email");
		$output->addHiddenField("action", "save_autoreply");
		$output->addHiddenField("autoreply[user_id]", $user_id);
		$output->addHiddenField("autoreply[id]", $autoreply["id"]);

		$venster = new Layout_venster( array(
			"title" => gettext("Mail Autoreply"),
			"subtitle" => gettext("edit")
		));

		$venster->addVensterData();
			$table = new Layout_table();
			$table->addTableRow();
				$table->insertTableData(gettext("active"), "", "header");
				$table->addTableData("", "data");
					$table->addCheckbox("autoreply[active]", 1, $autoreply["is_active"]);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("start date"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("autoreply[start_day]", $days, $start_day);
					$table->addSelectField("autoreply[start_month]", $months, $start_month);
					$table->addSelectField("autoreply[start_year]", $years, $start_year);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("end date"), "", "header");
				$table->addTableData("", "data");
					$table->addSelectField("autoreply[end_day]", $days, $end_day);
					$table->addSelectField("autoreply[end_month]", $months, $end_month);
					$table->addSelectField("autoreply[end_year]", $years, $end_year);
				$table->endTableData();
			$table->endTableRow();
			$table->addTableRow();
				$table->insertTableData(gettext("subject"), "", "header");
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
					$table->insertAction("save", gettext("save"), "javascript: document.getElementById('replyform').submit();");
				$table->endTableData();
			$table->endTableRow();
			$table->endTable();
			$venster->addCode($table->generate_output());
		$venster->endVensterData();
		$output->addCode($venster->generate_output());
		$output->layout_page_end();
		$output->exit_buffer();
	}
	/* }}} */
}
?>
