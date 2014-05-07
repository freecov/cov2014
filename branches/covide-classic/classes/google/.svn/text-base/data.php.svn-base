<?php
/**
 * Covide Google Apps module
 *
 * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
 * @version %%VERSION%%
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.covide.net Project home.
 * @copyright Copyright 2000-2007 Covide BV
 * @package Covide
 */

Class Google_data {
	/* constants */
	const include_dir      = "classes/google/inc/";
	const include_dir_main = "classes/html/inc/";
	const class_name       = "google";

	/* uri for single login */
	private $google_login   = "https://www.google.com/accounts/ClientLogin";

	/* uri for document listing */
	private $google_docs    = "https://docs.google.com/feeds/documents/private/full";

	/* uri for document exports */
	private $google_export_doc = "https://docs.google.com/MiscCommands?command=saveasdoc&exportformat=%s&hl=en&docID=%s";
	private $google_export_zip = "https://docs.google.com/UserMiscCommands?command=saveaszip&hl=en&docID=%s";

	/* excel specific feeds */
	//only for viewing
	private $google_export_xls = "https://spreadsheets.google.com/fm?fmcmd=%d&hl=en&key=%s";
	//for attachments
	private $google_export_xls_worksheet = "http://spreadsheets.google.com/feeds/worksheets/%s/private/full";
	private $google_export_xls_cellbased = "http://spreadsheets.google.com/feeds/cells/%s/%s/private/full";
	private $google_export_xls_rowbased  = "http://spreadsheets.google.com/feeds/list/%s/%s/private/full";

	/* uri for session tokens */
	private $google_multi_session   = "https://www.google.com/accounts/AuthSubRequest?scope=%s&session=1&secure=0&next=";
	private $google_upgrade_session = "https://www.google.com/accounts/AuthSubSessionToken";

	/* internal vars */
	private $google = array();
	private $cache  = array();
	private $token;
	private $debug = 0;
	private $debug_file = "/tmp/google.xml";
	private $use_sessions = 0; //downloads not implemented
	private $user_data;

	/* function __construct {{{  */
	public function __construct() {
		$this->user_data = new User_data();
		$this->google_multi_session = str_replace("%s", urlencode($this->google_docs), $this->google_multi_session);

		/* debug handler */
		if ($this->debug && file_exists($this->debug_file) && !$GLOBALS["google"]) {
			unlink($this->debug_file);
		}
		/* fill the cache */
		$this->cache =& $GLOBALS["google"]["cache"];

		/* if google session token is set */
		if ($_SESSION["google_id"] && $this->use_sessions)
			$this->token =& $_SESSION["google_id"];
	}
	/* }}} */

	private function authToHeader($auth, $session_token=0) {
		if ($this->use_sessions && ($session_token || $_SESSION["google_id"])) {
			/* convert auth token to session request header */
			return sprintf("Authorization: AuthSub token=\"%s\"", $auth);
		} else {
			/* convert single auth token to a header */
			return sprintf("Authorization: GoogleLogin auth=%s", $auth);
		}
	}

	public function getGoogleFolders($current_folder, $subaction="") {
		/* retrieve google folders */
		$ret     = $this->getGoogleDocList("", "", $subaction);
		$folders = array();

		if (preg_match("/^g_/s", $current_folder)) {
			return array();
		} else {
			foreach ($ret as $r) {
				if (!$r["google_folder"]) {
					$k = 0;
					$folders[$k]["name"]        = gettext("items not in folders");
					$folders[$k]["foldericon"]  = "folder_my_docs";
					$folders[$k]["description"] = gettext("default folder");
				} else {
					$k = base64_encode($r["google_folder"]);
					$folders[$k]["name"]        = $r["google_folder"];
					$folders[$k]["foldericon"]  = "folder_lock";
					$folders[$k]["description"] = gettext("google folder");
				}
				$folders[$k]["id"]         = sprintf("g_%s", $k);
				$folders[$k]["h_name"]      = $folders[$k]["name"];
				$folders[$k]["foldercount"] = 0;
				$folders[$k]["parent_id"]   = $parent_id;
				$folders[$k]["allow"]       = 1;
				$folders[$k]["filecount"]++;
			}
			natcasesort($folders);
			return $folders;
		}
	}
	private function descr2Ftype($str) {
		return substr($str, 1, 3);
	}
	public function getGoogleDocList($current_folder=0, $file_search="", $subaction="") {
		/* if no folder was requested */
		if (!preg_match("/^g_/s", $current_folder) && $current_folder)
			return array();

		if ($this->cache["data"]) {
			$data = $this->cache["data"];
			if (preg_match("/^g_/s", $current_folder))
				$gfolder = base64_decode(preg_replace("/^g_/s", "", $current_folder));

			foreach ($data as $k=>$v) {
				/* filter documents */
				if ($gfolder && $gfolder != $v["google_folder"])
					unset($data[$k]);
				elseif ($current_folder == "g_0" && $v["google_folder"])
					unset($data[$k]);
				elseif ($file_search) {
					if (preg_match("/^[a-z]\*$/si", $file_search)) {

						$regex = "/^".strtolower(substr($file_search, 0, 1))."/si";
						if (!preg_match($regex, $v["name"]))
							unset($data[$k]);

					} elseif (!stristr($v["name"], $file_search)) {
						unset($data[$k]);
					}
				}
			}
			return $data;
		}
		if (!$_SESSION["google_id"]) {
			$this->userSettings();
			$this->token = $this->googleLoginAuth();
		}

		$user_data =& $this->user_data;
		$fs_data   = new Filesys_data();
		$output    = new Layout_output();

		if ($file_search)
			$uri = sprintf("%s?q=%s", $this->google_docs, urlencode($file_search));
		else
			$uri = $this->google_docs;

		$ret = $this->googleQuery($uri, "", 0, $this->authToHeader($this->token));
		$xml =& $ret["data"];

		require_once(self::include_dir."atom.php");
		$objXML = new xml2Array();
 		$arr = $objXML->parse($xml);

		if ($this->debug)
			file_put_contents($this->debug_file, print_r($arr, true)."\n\n", FILE_APPEND);

 		$data = array();
 		foreach ($arr as $a) {
 			if ($a["name"] == "FEED") {
 				foreach ($a["children"] as $child) {
 					if ($child["name"] == "ENTRY") {
 						$doc = array();
 						foreach ($child["children"] as $attrib) {
 							switch ($attrib["name"]) {
 								case "UPDATED":
 									$doc["timestamp"] = strtotime($attrib["tagData"]);
 									break;
 								case "CATEGORY":
 									if ($attrib["attrs"]["LABEL"] == $attrib["attrs"]["TERM"]) {
 										$doc["google_folder"] = $attrib["attrs"]["LABEL"];
 									} else {
										switch ($attrib["attrs"]["LABEL"]) {
											case "document":
												$doc["type"] = "application/msword";
												break;
											case "spreadsheet":
												$doc["type"] = "application/vnd.ms-excel";
												break;
											case "presentation":
												$doc["type"] = "application/mspowerpoint";
												break;
										}
									}
 									break;
 								case "TITLE":
 									$doc["name"] = $attrib["tagData"];
 									break;
 								case "LINK":
 									if ($attrib["attrs"]["TYPE"] == "text/html")
 										$doc["edit_url"] = urldecode($attrib["attrs"]["HREF"]);
 									break;
 								case "CONTENT":
 									$doc["data_url"] = urldecode($attrib["attrs"]["SRC"]);
 									break;
 								case "AUTHOR":
 									foreach ($attrib["children"] as $c) {
 										$doc["description"][] = $c["tagData"];
 									}
									$doc["description"] = gettext("owner").": ".implode(" - ", $doc["description"]);
									break;
								case "ID":
									$doc["google_id"]  = explode(":", urldecode($attrib["tagData"]));
									$doc["google_id"]  = $doc["google_id"][count($doc["google_id"])-1];
									$doc["google_md5"] = md5($doc["google_id"]);
									break;
 							}
 						}
 						/* guess export formats, this is not documented at all! */
 						switch ($doc["type"]) {
 							case "application/msword":
 								$doc["name"]         .= ".doc";
 								$doc["fileicon"]      = "ftype_doc";
 								$doc["export"][sprintf(".doc (%s)", gettext("microsoft word"))]     = sprintf($this->google_export_doc, "doc", $doc["google_id"]);
 								$doc["export"][sprintf(".pdf (%s)", gettext("pdf document"))]       = sprintf($this->google_export_doc, "pdf", $doc["google_id"]);
 								$doc["export"][sprintf(".odt (%s)", gettext("open office writer"))] = sprintf($this->google_export_doc, "oo",  $doc["google_id"]);
 								$doc["export"][sprintf(".zip (%s)", gettext("zip file with html"))] = sprintf($this->google_export_zip, $doc["google_id"]);
								$doc["allow_attach"] = 1;
 								break;
 							case "application/vnd.ms-excel":
 								$doc["name"]         .= ".xls";
 								$doc["fileicon"]      = "ftype_calc";
 								$doc["export"][sprintf(".xls (%s)", gettext("microsoft excel"))]  = sprintf($this->google_export_xls, "4",  $doc["google_id"]);
 								$doc["export"][sprintf(".ods (%s)", gettext("open office calc"))] = sprintf($this->google_export_xls, "13", $doc["google_id"]);
 								$doc["export"][sprintf(".pdf (%s)", gettext("pdf document"))]     = sprintf($this->google_export_xls, "12", $doc["google_id"]);
								$doc["allow_attach"] = 2;
 								break;
 							case "application/mspowerpoint":
 								$doc["name"]         .= ".ppt";
 								$doc["fileicon"]      = "ftype_ppt";
 								$doc["export"][sprintf(".zip (%s)", gettext("zip file with html"))] = sprintf($this->google_export_zip, $doc["google_id"]);
								$doc["allow_attach"] = 1;
								unset($doc["data_url"]);
 								break;
 							default:
 								$doc["name"] .= ".unknown";
								unset($doc["data_url"]);
 								break;
 						}

 						if (is_array($doc["export"])) {
							$sel = array("0" => gettext("choose action"));
							foreach ($doc["export"] as $ek=>$ev) {
								$ev .= sprintf("|%s", $doc["name"]); //add name to string
								$sel[gettext("download as")][sprintf("dl:%s:%s", $this->descr2Ftype($ek), urlencode(base64_encode($ev)))] = sprintf("%s", $ek);
							}
							if ($subaction == "add_attachment" && $doc["allow_attach"] == 1) {
								foreach ($doc["export"] as $ek=>$ev) {
									$ev .= sprintf("|%s", $doc["name"]); //add name to string
									$sel[gettext("attach as")][sprintf("att:%s:%s", $this->descr2Ftype($ek), urlencode(base64_encode($ev)))] = sprintf("+ %s", $ek);
								}
							} elseif ($subaction == "add_attachment" && $doc["allow_attach"] == 2) {
								$ev = end($doc["export"]);
								$ek = sprintf(".csv (%s)", gettext("csv file(s)"));

								$ev .= sprintf("|%s", $doc["name"]); //add name to string
								$sel[gettext("attach as")][sprintf("att:%s:%s", $this->descr2Ftype($ek), urlencode(base64_encode($ev)))] = sprintf("+ %s", $ek);
							}
							$output->addSelectField(sprintf("g_%s", $doc["google_md5"]), $sel, "", "", array(
								"style" => "width: 170px;"
							));
							$output->start_javascript();
								$output->addCode(sprintf("document.getElementById('g_%1\$s').onchange = function() { googleAction(document.getElementById('g_%1\$s')); }",
									$doc["google_md5"]));
							$output->end_javascript();

							$doc["selectbox"] = $output->generate_output();
 						}

 						$doc["size"]         = 0;
 						$doc["user_id"]      = $_SESSION["user_id"];
 						$doc["username"]     = $user_data->getUsernameById($doc["user_id"]);
 						$doc["user_name"]    = $doc["username"];
 						$doc["date_human"]   = date("d-m-Y H:i", $doc["timestamp"]);
 						$doc["show_google_actions"] = 1;
 						if ($doc["google_folder"]) {
 							$doc["folder_name"]  = $doc["google_folder"];
 							$doc["folder_id"]    = sprintf("g_%s", urlencode(base64_encode($doc["folder_name"])));
 						} else {
 							$doc["folder_name"]  = gettext("items not in folders");
 							$doc["folder_id"]    = $fs_data->getGoogleFolders();
 						}
 						$doc = $fs_data->detect_preview($doc);
 						$doc["subview"] = 0;

 						$data[$doc["google_id"]] = $doc;
 					}
 				}
 			}
 		}
 		ksort($data);

 		$this->cache["data"] = $data;
 		return $data;
	}
	private function userSettings($service="writely") {
		$user_data =& $this->user_data;
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		if ($user_info["google_username"] && $user_info["google_password"]) {
			$this->google["Email"]  = $user_info["google_username"];
			$this->google["Passwd"] = $user_info["google_password"];
		} else {
			return false;
		}
		$this->google["accounttype"] = "HOSTED_OR_GOOGLE";
		$this->google["service"] = $service;
		$this->google["source"]  = "covide";
	}

	public function getGoogleUserLogin() {
		if ($this->use_sessions) {
			$uri = sprintf("%s%s%s",
				$this->google_multi_session,
				urlencode($GLOBALS["covide"]->webroot),
				urlencode("?mod=google&action=gtoken")
			);
		} else {
			$uri = "https://docs.google.com";
		}
		return $uri;
	}
	public function checkGoogleSession() {
		$user_data =& $this->user_data;
		$user_info = $user_data->getUserDetailsById($_SESSION["user_id"]);

		if ($this->use_sessions && !$_SESSION["google_id"])
			return false;
		elseif (!$user_info["google_username"] || !$user_info["google_password"])
			return false;
		else
			return true;
	}
	private function googleLoginAuth($sid=0) {
		if ($this->use_sessions == 1) {
			if (!$_SESSION["google_id"]) {
				/* require login! */
				return false;
			}
		} else {
			/* single server side login */
			$p = array();
			foreach ($this->google as $k=>$v) {
				$p[] = sprintf("%s=%s", $k, $v);
			}
			$param = implode("&", $p);
			$ret  = $this->googleQuery($this->google_login, $param);

			/* search for the auth key */
			$data = explode("\n", $ret["data"]);
			foreach ($data as $k=>$v) {
				if ($sid) {
					if (preg_match("/^SID=/s", $v))
						return trim($v);
				} else {
					if (preg_match("/^Auth=/s", $v))
						return trim(preg_replace("/^Auth=/s", "", $v));
				}
			}
		}
	}
	private function googleQuery($url, $param="", $post=1, $header="") {
		$s = $this->mtime();

		if (!function_exists('curl_init'))
			return false;

		$cl   = curl_init();
		$opts = array();

		if ($post) {
			$opts[CURLOPT_POST] = true;
			$opts[CURLOPT_POSTFIELDS] = $param;
		}
		if ($header)
			$opts[CURLOPT_HTTPHEADER] = array($header);

		#$fp = fopen("/tmp/curl.txt", "w");
		#curl_setopt($cl, CURLOPT_WRITEHEADER, $fp);

		$opts[CURLOPT_SSL_VERIFYPEER]       = true;
		$opts[CURLOPT_HEADER]               = 0;
		$opts[CURLOPT_RETURNTRANSFER]       = true;
		$opts[CURLOPT_FOLLOWLOCATION]       = true;
		$opts[CURLOPT_DNS_USE_GLOBAL_CACHE] = true;
		$opts[CURLOPT_URL]                 = $url;
		$opts[CURLOPT_ENCODING]             = "gzip";
		$opts[CURLOPT_HTTP_VERSION]         = CURL_HTTP_VERSION_1_1;
		/*
			$opts[CURLOPT_COOKIEJAR]            = "-"; /* sprintf("%sgcookie_%s.txt",
			$GLOBALS["covide"]->temppath, md5(session_id())); */
		//$opts[CURLOPT_MAXCONNECTS]          = 10;

		/* set options */
		curl_setopt_array($cl, $opts);

		$res  = curl_exec($cl);
		$info = curl_getinfo($cl);

		$param = preg_replace("/(\&Passwd=)[^\&]*?\&/si", "$1=***********&", $param);

		$return = array(
			"info"  => $info,
			"error" => ($res === false) ? curl_error($cl):false,
			"data"  => $res,
			"param" => $param,
			"code"  => $info["http_code"],
			"time"  => $s-$this->mtime(),
			"dns"   => number_format($info["namelookup_time"], 6)

		);
		#fclose($fp);
		#unset($fp);
		curl_close($cl);

		if (!in_array($return["code"], array(200,302))) {
			echo "<b>An error occured while connecting to Google:</b><br><br>";
			echo $return["code"].": ".print_r($info, true)."<BR><BR>".$return["data"];
		}

		if ($this->debug)
			file_put_contents($this->debug_file, print_r($return, true)."\n\n", FILE_APPEND);

		return $return;
	}
	private function compareDomains($domain1, $domain2) {
		$domain[1] = $domain1;
		$domain[2] = $domain2;

		foreach ($domain as $k=>$v) {
			$domain[$k] = preg_replace("/^http(s){0,1}/si", "", $v);
			$domain[$k] = preg_replace("/\/(.*)$/si", "", $domain[$k]);
		}
		if ($domain[1] == $domain[2])
			return true;
		else
			return false;
	}
	public function gdownload($f, $mail_id=0) {
		// strip first part
		$f = preg_replace("/^((att)|(dl)):/s", "", $f);

		// strip extension
		$ext = preg_replace("/^(.{3}):.*$/s", "$1", $f);
		$f = preg_replace("/^(.{3}:)/s", "", $f);

		// decode
		$f = base64_decode($f);

		// split by |
		$f = explode("|", $f);
		$f[1] = preg_replace("/\..{3}$/s", ".".$ext, $f[1]);

		if (!$mail_id) {
			/* download to client directly */
			header(sprintf("Location: %s", $f[0]));
			exit();
		} else {
			/* fetch within covide and attach to mail */

			if ($this->compareDomains($f[0], $this->google_export_xls) == true) {

				/* extract the key */
				$k = preg_replace("/^(.*)\&key=(.*)$/s", "$2", $f[0]);
				$uri = sprintf($this->google_export_xls_worksheet, $k);

				$this->userSettings("wise");
				$this->token = $this->googleLoginAuth();
				$ret = $this->googleQuery($uri, "", 0, $this->authToHeader($this->token));

				require_once(self::include_dir."atom.php");
				$objXML = new xml2Array();
				$arr = $objXML->parse($ret["data"]);

		 		foreach ($arr as $a) {
					if ($a["name"] == "FEED") {
						foreach ($a["children"] as $child) {
							if ($child["name"] == "TITLE") {
								$docname = $child["tagData"];
							} elseif ($child["name"] == "ENTRY") {
								$doc = array();
								$t = "";
								$n = "";
								foreach ($child["children"] as $attrib) {
									switch($attrib["name"]) {
										case "ID":
											$t = explode("/", $attrib["tagData"]);
											$t = end($t);
											$t = sprintf($this->google_export_xls_cellbased, $k, $t);
											break;
										case "TITLE":
											$n = $attrib["tagData"];
											break;
									}
								}
								$tags[$t] = sprintf("%s [%s].csv", $docname, $n);
							}
						}
					}
				}
				$j = 0;
				$ws = array();
				foreach ($tags as $t=>$n) {
					$j++;
					$ws[$j] = $n;

					$this->userSettings("wise");
					$this->token = $this->googleLoginAuth();

					unset($ret);
					unset($arr);
					unset($objXML);
					$objXML = new xml2Array();

					$ret = $this->googleQuery($t, "", 0, $this->authToHeader($this->token));
					$arr = $objXML->parse($ret["data"]);
					foreach ($arr as $a) {
						if ($a["name"] == "FEED") {
							foreach ($a["children"] as $child) {
								if ($child["name"] == "ENTRY") {
									foreach ($child["children"] as $scell) {
										if ($scell["name"] == "GS:CELL") {
											$attrib = $scell["attrs"];
											$cells[$j]["c"][$attrib["ROW"]][$attrib["COL"]] = $attrib["INPUTVALUE"];
											if ($attrib["ROW"] > $cells[$j]["rows"])
												$cells[$j]["rows"] = $attrib["ROW"];
											if ($attrib["COL"] > $cells[$j]["cols"])
												$cells[$j]["cols"] = $attrib["COL"];
										}
									}
								}
							}
						}
					}
				}
				$conversion = new Layout_conversion();
				foreach ($cells as $w=>$c) {
					$lines = array();
					for ($i=1;$i<=$c["rows"];$i++) {
						$cols = array();
						for ($j=1;$j<=$c["cols"];$j++) {
							$cols[] =& $c["c"][$i][$j];
						}
						$lines[] = $conversion->generateCSVRecord($cols);
					}
					$lines = implode("\n", $lines)."\n";

					/* add as attachment here */
					$files[$w] = array(
						"name" => $ws[$w],
						"type" => "text/comma-separated-values",
						"size" => strlen($lines),
						"data" => $lines
					);
				}
				unset($lines);
				unset($cols);

			} else {
				$this->userSettings();
				$this->token = $this->googleLoginAuth();
				// query google
				$ret = $this->googleQuery($f[0], "", 0, $this->authToHeader($this->token));

				$files[0] = array(
					"name" => $f[1],
					"type" => $ret["info"]["content_type"],
					"size" => strlen($file["data"]),
					"data" => $ret["data"]
				);
				unset($ret);
			}
			$fsdata = new Filesys_data();
			$fspath = $GLOBALS["covide"]->filesyspath;
			$fsdir_target  = "email";

			foreach ($files as $file) {
				/* insert file into dbase */
				$q = "insert into mail_attachments (message_id, name, size, type) values ";
				$q.= sprintf("(%d, '%s', '%s', '%s')", $mail_id, addslashes($file["name"]), $file["size"], $file["type"]);
				sql_query($q);
				$new_id = sql_insert_id("mail_attachments");

				/* move data to the destination */
				$ext = $fsdata->get_extension($file["name"]);

				$destination = sprintf("%s/%s/%s.%s", $fspath, $fsdir_target, $new_id, $ext);

				/* write file data */
				file_put_contents($destination, $file["data"]);
				$fsdata->FS_checkFile($destination);
			}

			echo "mail_upload_update_list();";
			exit();
		}
	}

	private function mtime() {
		list($usec, $sec) = explode(" ",microtime());
		$m = ((float)$usec + (float)$sec);
		return $m;
	}
	public function gtoken($token) {
		if ($token != -1) {
			$ret = $this->googleQuery($this->google_upgrade_session, "", "", $this->authToHeader($token, 1));
			if ($ret["code"] == 200) {
				$session_token = preg_replace("/^Token=/s", "", trim($ret["data"]));
				$_SESSION["google_id"] = $session_token;
			}
		}
		if ($session_token || $token == -1) {
			if ($token == -1)
				unset($_SESSION["google_id"]);

			$output = new Layout_output();
			$output->start_javascript();
				$output->addCode("
					opener.document.getElementById('velden').submit();
					window.close();
				");
			$output->end_javascript();
			$output->exit_buffer();
		} else {
			echo "Invalid or illegal request, please check your settings!";
		}
	}
	public function gsearch($search, $subaction="") {
		if ($this->userSettings() !== false) {
			$ret = $this->getGoogleDocList(0, $search, $subaction);
			return $ret;
		} else {
			return array();
		}
	}
}
?>
