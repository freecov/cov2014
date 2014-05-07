<?php
/**
 * Covide Groupware-CRM
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
Class Covide {
	/* variables */
	public $pagesize = 20;
	public $pagesize_default = 20;
	public $pagesize_alt = 10;

	/**
	 * @var array loaded javascripts
	 */
	public $loaded_scripts = array();
	/**
	 * @var array used html id=identifier inside html tags
	 */
	public $loaded_htmlids = array();
	/**
	 * @var integer show debug information.
	 */
	public $debug = 1;
	/**
	 * @var integer clean output html with indenting.
	 */
	public $debug_indent = 0;
	/**
	 * @var array holds all license info for an office.
	 */
	public $license = Array();
	/**
	 * @var string holds version number
	 */
	public $vernr = "9.1";
	/**
	 * @var array holds all user info
	 */
	public $userinfo = Array();
	/**
	 * @var object database connection identifier
	 */
	public $db;
	public $dsn;
	/**
	 * @var integer theme number
	 */
	public $theme = 0;
	/**
	 * @var array of tags to filter
	 */
	public $filter_tags = array("iframe", "script");
	public $filter_pattern;
	/**
	 * @var string physical filesystem location for files
	 */
	public $filesyspath = "";
	public $temppath = "";
	public $logpath = "";
	public $webroot = "";
	public $sslmode;
	public $certificate;

	public $mobile = 0;

	public $conversion;
	public $venster;

	public $current_module = array();
	public $contrib        = array();
	public $sync_stats     = array();

	public $query_log      = array();
	public $query_file     = array();

	public $output_xhtml   = 0;

	/**
	 * @var int set to 1 for development mode
	 */
	public $devmode = 0;

	/* methods */

    /* 	__construct {{{ */
    /**
     * 	__construct. Init a covide object and set some defaults
     */
	public function __construct() {

		/* preset database connection into the db var, so custom scripts can be run */
		/* this will we overwritten when the covide object is inited */
		$GLOBALS["covide"]->db    =& $this->db;
		$GLOBALS["covide"]->theme =  $_SESSION["theme"];

		/* include paths */
		ini_set('include_path',ini_get('include_path').':./PEAR:');

		/* open database connection and set some defaults. */
		require_once("MDB2.php");
		require("conf/offices.php");

		$options = array(
			"persistent"  => TRUE,
			'portability' => MDB2_PORTABILITY_NONE
		);

		$this->dsn = $dsn;
		$this->certificate = $certificate;

		$this->db =& MDB2::connect($dsn, $options);
		if (PEAR::isError($this->db)) {
			 // respond with 500.x internal server error for search engines
			 header($_SERVER["SERVER_PROTOCOL"]." 500 Internal Server Error", true, 500);
			 echo ("<h1>500 Internal Server Error</h1>Fatal: no Covide office configured at this address or no valid database specified / database connection failed.");
			 echo ($this->db->getMessage());
			 die();
		}
		$GLOBALS["covide"]->db    =& $this->db;
		$GLOBALS["covide"]->theme =  $_SESSION["theme"];

		/* include our own db lib. This should be removed asap. */
		require_once("common/functions_pear.php");
		$this->db->setFetchMode(DB_FETCHMODE_ASSOC);
		$this->db->setOption("autofree", 1);

		/* set temp path */
		$this->temppath = dirname($_SERVER["SCRIPT_FILENAME"])."/tmp/";

		/* fill some default stuff we need throughout whole covide. */
		$this->_get_license();

		/* check if we have everything in place for covide */
		$this->_check_officereq();

		/* check for patches to be applied */
		$this->checkPatchLevel();

		/* set the gettext env. */
		$this->_set_language();

		$dir = dirname($_SERVER["SCRIPT_NAME"])."/";
		$dir = preg_replace("/\/{1,}/s", "/", $dir);
		if ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https") {
			$uri = "https://";
		} else {
			$uri = "http://";
		}

		if ($GLOBALS["autoloader_include_path"]) {
			$dir = str_replace(dirname($_SERVER["SCRIPT_NAME"]), "/", $dir);
			$dir = preg_replace("/\/\/$/s", "/", $dir);
		}

		$uri.= $_SERVER["HTTP_HOST"].$dir;
		$this->webroot = $uri;

		/* overwrite default theme 0 if session theme is set */
		if (isset($_SESSION["theme"]))
			$this->theme = $_SESSION["theme"];
		elseif (!$_SESSION["user_id"])
			$this->theme = 1;

		if ($_SESSION["pagesize"] < 5)
			$_SESSION["pagesize"] = 5;

		if ($_SESSION["pagesize"] > 1000)
			$_SESSION["pagesize"] = 1000;

		if ($_SESSION["pagesize"])
			$this->pagesize = $_SESSION["pagesize"];

		$this->conversion = new Layout_conversion();
		$this->run_once_a_day();

		$this->contrib = $contrib;

		if ($_SESSION["user_id"]) {
			$user_data = new User_data();
			$user_data->updateLoginInfo(&$this->db);
		}

		if ($this->contrib["USE_CONTRIB_SCRIPT"]) {
			$file = "contrib/".$this->contrib["USE_CONTRIB_SCRIPT"]."/pre_covide.php";
			if (file_exists($file))
				require_once($file);
		}

	}
    /* }}} */
    /* 	__destruct {{{ */
    /**
     * 	__destruct. clean up everything
     */
	public function __destruct() {
		/* disconnect from database. */
		@$this->db->disconnect();

		/* flush logs to disk */
		if (count($this->query_log) > 0) {
			$logs = implode("", $this->query_log);
			file_put_contents($this->query_file, $logs, FILE_APPEND);
		}
	}
    /* }}} */
    /* 	_get_license {{{ */
    /**
     * 	_get_license. Read the license table and init some vars
     */
	private function _get_license() {
		$q = "SELECT * FROM license ;";
		$res = $this->db->query($q);
		if (PEAR::isError($res))
			die("Database is not correctly created. Missing table license.");

		$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		$this->license = $row;
		if (trim($row["filesyspath"])) {
			$basepath = $row["filesyspath"];
		} else {
			$basepath = "/var/covide_files/";
		}
		$basepath .= $row["code"];
		$this->filesyspath = $basepath;
		$this->logpath = sprintf("%s/../logs/%s", $basepath, $row["code"]);
		$res->free();
	}
    /* }}} */

	/* _set_language {{{ */
	/**
	 * Set the gettext enviroment for complete covide.
	 */
	private function _set_language($override_lang="") {
		if ($_SESSION["user_id"] || $override_lang) {
			//language settings
			if (!$override_lang) {
				$sql = sprintf("SELECT language FROM users WHERE id=%d", $_SESSION["user_id"]);
				$res = $this->db->query($sql);
				//$res->fetchInto($row);
				$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			} else {
				$row["language"] = $override_lang;
			}
		} else {
			/* get the default language from the database */
			if ($this->license["default_lang"]) {
				$row["language"] = $this->license["default_lang"];
			}
		}
		switch ($row["language"]) {
			case "NL" : $language = "nl_NL"; $hlindex = "index.htm";    break;
			case "DE" : $language = "de_DE"; $hlindex = "index_de.htm"; break;
			case "ES" : $language = "es_ES"; $hlindex = "index_es.htm"; break;
			case "IT" : $language = "it_IT"; $hlindex = "index_it.htm"; break;
			case "DA" : $language = "da_DK"; $hlindex = "index_da.htm"; break;
			case "NO" : $language = "nn_NO"; $hlindex = "index_no.htm"; break;
			default   : $language = "en_US"; $hlindex = "index_en.htm"; break;
		}
		putenv("LANG=$language");
		putenv("LANGUAGE=$language");
		$_SESSION["locale"] = $language;
		if (!setlocale(LC_ALL, $language)) setlocale(LC_ALL, $language.".UTF-8");
		if (!setlocale(LC_NUMERIC, "en_US")) setlocale(LC_NUMERIC, "en_US.UTF-8"); //always use . as decimal
		setlocale(LC_MONETARY, "C"); //C system locale
		$domain = 'messages';
		$filepath = $_SERVER["SCRIPT_FILENAME"];
		$path_parts = explode("/", $filepath);
		$path_len = count($path_parts)-1;
		$path1 = "";
		for ($i=0;$i<$path_len;$i++) {
			$path1 .= "/".$path_parts[$i];
		}
		$path1 .= "/".$parent."lang";
		bindtextdomain($domain, $path1);
		bind_textdomain_codeset($domain, "UTF-8");
		textdomain($domain);
	}
	/* }}} */
	/* override_language {{{ */
	/**
	 * override_language. Override the language setting
	 *
	 * @param string set to language.
	 */
	public function override_language($lang) {
		$this->_set_language($lang);
	}
	/* }}} */


	/* run_module {{{ */
	/**
	 * run_module. Run the class we want
	 *
	 * This function is to run the correct module.
	 *
	 * @param string name of the class.
	 */
	public function run_module($module="") {
		$module = str_replace("/", "", $module);
		$this->current_module[] = $module;

		$alias = array(
			"mortgage"           => "address",
			"privoxyconf"        => "user",
			"projectdeclaration" => "project",
			"templates"          => "address",
			"twinfield"          => "address"
		);
		if (array_key_exists($module, $alias))
			$this->current_module[] = $alias[$module];

		switch ($module) {
			case "address":            $mod = new Address();            break;
			case "calendar":           $mod = new Calendar();           break;
			case "campaign":           $mod = new Campaign();           break;
			case "chat":               $mod = new Chat();               break;
			case "classification":     $mod = new Classification();     break;
			case "cms":                $mod = new Cms();                break;
			case "email":              $mod = new Email();              break;
			case "filesys":            $mod = new Filesys();            break;
			case "finance" :           $mod = new Finance();            break;
			case "funambol":           $mod = new Funambol();           break;
			case "google":             $mod = new Google();             break;
			case "history":            $mod = new Layout_history();     break;
			case "index":              $mod = new Index();              break;
			case "metafields":         $mod = new Metafields();         break;
			case "mortgage":           $mod = new Mortgage();           break;
			case "newsletter":         $mod = new Newsletter();         break;
			case "note":               $mod = new Note();               break;
			case "privoxyconf":        $mod = new Privoxyconf();        break;
			case "project":            $mod = new Project();            break;
			case "projectdeclaration": $mod = new ProjectDeclaration(); break;
			case "projectext":         $mod = new ProjectExt();         break;
			case "rss":                $mod = new Rss();                break;
			case "sales":              $mod = new Sales();              break;
			case "snack":              $mod = new Snack();              break;
			case "support":            $mod = new Support();            break;
			case "templates":          $mod = new Templates();          break;
			case "todo":               $mod = new Todo();               break;
			case "twinfield":          $mod = new Twinfield();          break;
			case "user":               $mod = new User();               break;
			case "voip":               $mod = new Voip();               break;
			case "googlemaps":         $mod = new Googlemaps();         break;
			case "dimdim":             $mod = new Dimdim();             break;
			default:
				if (!$_SESSION["user_id"] && !$GLOBALS["autoloader_include_path"]) {
					/* remove old temp files */
					$this->cleanUpTempDir();

					/* show login */
					$user = new User_output();
					$user->show_login();
				} else {
					/* show desktop */
					$desktop = new Desktop();
				}
				break;
		}
	}
	/* }}} */

	public function detect_mobile($str) {
		$agent = $_SERVER["HTTP_USER_AGENT"];
		if (preg_match($str, $agent)) {
			$this->mobile = 1;
			$this->theme = 9;
			header("X-Covide-Version: Mobile");
		} else {
			header("X-Covide-Version: Normal");
		}
	}

	public function force_ssl($state) {
		/* detect and change ssl state */
		if ($this->mobile) {
			//$state = 0;
		} else {
			$uri = $_SERVER["HTTP_HOST"]."/".dirname($_SERVER["SCRIPT_NAME"])."/?".$_SERVER["QUERY_STRING"];
			$uri = preg_replace("/\/{1,}/s", "/", $uri);
			$uri = preg_replace("/\?{1,}/s", "?", $uri);

			if ($state == 3 && ($_SERVER["HTTPS"] || $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] == "https")) {
				header("Location: http://".$uri);
				exit();
			} elseif ((($state == 1 && $_SESSION["user_id"]) || ($_SESSION["ssl_enable"] && $state != 3)) && !$_SERVER["HTTPS"] && $_SERVER["HTTP_X_FORWARDED_PROTOCOL"] != "https") {
				/* some exceptions */
				if ($_REQUEST["mod"] == "cms") {
					$this->force_ssl = 3;
					return;
				}
				/* some ssl exceptions */
				/* filesys is checked inside filesystem module on highest parent = cms */
				if (!($_REQUEST["mod"] == "email" && $_REQUEST["action"] == "retrieve")
					&& !($_REQUEST["mod"] == "user" && $_REQUEST["action"] == "cron")
					&& !($_REQUEST["mod"] == "user" && $_REQUEST["action"] == "translate")
					&& !($_REQUEST["mod"] == "user" && $_REQUEST["subaction"] == "validate")
					&& !($_REQUEST["mod"] == "user" && $_REQUEST["action"] == "logout")
					&& !($_REQUEST["mode"] == "loginimage")
					&& !($_REQUEST["mod"] == "filesys" && in_array($_REQUEST["action"], array(
						"opendir", "fdownload", "view_file", "preview_file", "fupload",
						"getPreviewFile", "preview_header", "dircreate")))
					) {

					$q = sprintf("select cms_manage_hostname from cms_license");
					$res = sql_query($q);
					$mhost = strtolower(trim(sql_result($res,0)));
					if (!$mhost)
						$mhost = $_SERVER["HTTP_HOST"];

					$hhost = preg_replace("/((^http(s){0,1}:\/\/)|(\/$))/s", "", $GLOBALS["covide"]->webroot);
					if ($mhost != $hhost) {
						//echo sprintf("no access with this hostname [%s], please use <a href='http://%s/covide/'>http://%s/covide/</a>", $hhost, $mhost);
						header(sprintf("Location: http://%s/covide/", $mhost));
						exit();
					}
					header("Location: https://".$uri);
					exit();
				}
			}
		}
		$this->sslmode = $state;
	}

	public function trigger_login() {
		echo "<html>";
		echo "<body>";
			?>
			<script language="javascript">
				alert('<?php echo addslashes(gettext("You are not logged in.")) ?>');
				setTimeout('document.location.href="index.php"', 1000);
			</script>
			<?php
		echo "</body>";
		echo "</html>";
		exit();
	}

	/* _check_officereq() {{{ */
	/**
	 * Check wether we have all directories with correct permissions
	 */
	private function _check_officereq() {
		/* check only first time of the session */
		if (!$_SESSION["allok"]) {
			/* dir structure we need */
			$dirs = array(
				"bestanden",
				"email",
				"bedrijfsinfo",
				"templates",
				"maildata",
				"mailhandtekening",
				"faxes",
				"relphotos",
				"relphotos/bcards",
				"syncml",
				"syncml/calendar",
				"syncml/contacts",
				"syncml/todos",
				"funambol",
				"gallery",
				"cmscache",
				"../logs",
				"../logs/".$this->license["code"]
			);
			/* check office filesyspath */
			if (!is_dir($this->filesyspath)) {
				if (!mkdir($this->filesyspath)) {
					die("Could not create needed directory: ".$this->filesyspath);
				}
			}
			if (!is_writable($this->filesyspath)) {
				die("Could not write to directory: ".$this->filesyspath);
			}
			foreach ($dirs as $dir) {
				$check = $this->filesyspath."/".$dir;
				if (!is_dir($check)) {
					/* missing dir */
					if (!mkdir($check)) {
						die("Could not create needed directory: $check");
					}
				}
				if (!is_writable($check)) {
					die("Could not write to directory: $check");
				}
			}
			/* create lastcall file */
			$timestampfile = sprintf("%s/lastcall_%s.txt",
				$this->temppath,
				$this->license["code"]
			);
			if (!file_exists($timestampfile))
				file_put_contents($timestampfile, "0");

			/* check write permissions */
			$ary = array(
				"tmp",
				"tmp_cms",
				"classes/funambol/plug-ins",
				"classes/chat/inc/data"
			);
			foreach ($ary as $check) {
				if (!is_writable($check))
					die("Could not write to directory: $check");
			}
			$_SESSION["allok"] = 1;
		}
	}
	/* }}} */

	public function cleanUpTempDir() {
		//temp dir
		$exclude = array(".", "..", ".svn", "README", "check.txt", "menu", "cache");
		$files = scandir($this->temppath);
		foreach ($files as $file) {
			if (!in_array($file, $exclude) && !preg_match("/^lastcall_/s", $file)) {
				$ctime = @filectime($this->temppath.$file);
				/* if file change time is older than 15 minutes */
				$cdiff = 60*15; //15 min
				if ($ctime + $cdiff <= mktime()) {
					if (is_dir($this->temppath.$file)) {
						/* remove directory */
						$dirfiles = scandir($this->temppath.$file);
						foreach ($dirfiles as $dirfile) {
							/* remove contents of directory */
							if (!in_array($dirfile, $exclude) && file_exists($this->temppath.$file."/".$dirfile))
								@unlink($this->temppath.$file."/".$dirfile);
						}
						@rmdir($this->temppath.$file);
					} else {
						/* remove file */
						@unlink($this->temppath.$file);
					}
				}
			}
		}
		//temp/code cache dir
		$files = array();
		if (file_exists($this->temppath."cache/")) {
			$files = scandir($this->temppath."cache/");
			foreach ($files as $file) {
				if (is_file($file)) {
					$ctime = @filectime($file);
					$cdiff = 60*60*24*7; //one week lifetime of code cache
					if ($ctime + $cdiff <= mktime()) {
						@unlink($file);
					}
				}
			}
		}

		/* move (or merge) old log files */
		$files = scandir($this->filesyspath);
		foreach ($files as $file) {
			if (!is_dir($file) && preg_match("/\.log$/s", $file)) {
				$target = $this->logpath."/".$file;
				$target = preg_replace("/\.log$/s", ".moved.log", $target);
				$file   = $this->filesyspath."/".$file;

				copy($file, $target);
				unlink($file);
			}
		}

	}
	private function run_once_a_day() {
		/* prepare dayqoute */
		$today = mktime(0,0,0,date("m"),date("d"),date("Y"));
		if ($this->license["dayquote"] != $today) {
			/* make new qoute */
			if (file_exists("/usr/games/fortune")) {
				$cmd = "/usr/games/fortune -a";
				exec($cmd, $ret, $retval);
				$this->license["dayquote_nr"] = implode("\n", $ret);
				$this->license["dayquote"]    = $today;
			}
			$q = sprintf("update license set dayquote = %d, dayquote_nr = '%s'",
				$today, $this->license["dayquote_nr"]);
			$this->db->query($q);
		}
		$cfile = sprintf("%schatclean.txt", $this->temppath);

		/* cleanup cached chat files once a day */
		if (!$GLOBALS["autoloader_include_path"]) {
			if (!file_exists($cfile) || trim(file_get_contents($cfile)) != date("Ymd")) {
				/* cleanup chat dir */
				$dir = sprintf("%s/classes/chat/inc/data/private/", dirname($_SERVER["SCRIPT_FILENAME"]));
				$cmd = sprintf("find %s -type f | grep -v '\\.svn' | grep -v '\\.htaccess'", $dir);
				exec($cmd, $ret, $retval);
				foreach ($ret as $r) {
					@unlink($r);
				}
			}
			file_put_contents($cfile, date("Ymd"));
		}
	}

	public function checkPatchLevel() {
		if (!$GLOBALS["autoloader_include_path"]) {
			if (preg_match("/^mysql(i){0,1}:\/\//s", $this->dsn)) {
				if ($this->license["autopatcher_enable"]) {
					$this->patchAll();
				}
			}
		}
	}
	public function patchAll() {
		if (preg_match("/^mysql(i){0,1}:\/\//s", $this->dsn)) {

			/* check if autopatcher level is inside a valid range */
			if ($this->license["autopatcher_lastpatch"] > date("Ymd99")) {
				$q = "update license set autopatcher_lastpatch = 0";
				$this->db->query($q);
				$this->license["autopatcher_lastpatch"] = 0;
			}
			$dir = scandir("sql/mysql/patches");
			$files = array();
			foreach ($dir as $file) {
				if (preg_match("/\.sql$/s", $file)) {
					$files[] = (int)preg_replace("/\.sql$/s", "", $file);
				}
			}
			/* get last patch level */
			$lastpatch = (int)$this->license["autopatcher_lastpatch"];

			/* if at least the last patchfile needs to be applied */
			if (count($files) > 0) {
				if (end($files) > $lastpatch) {
					/* reparse peardb dsn to array */
					$dsn = preg_replace("/^mysql(i){0,1}:\/\//s", "", $this->dsn);
					$dsn = str_replace("@tcp(", ":", $dsn);
					$dsn = str_replace(")/", ":", $dsn);
					$dsn = explode(":", $dsn);

					/* check if patches need to be applied */
					foreach ($files as $f) {
						$patches++;
						if ($f > $lastpatch) {
							/* apply patch */
							$fn = sprintf("sql/mysql/patches/%u.sql", $f);
							$cmd = sprintf("mysql --host=%s --port=%s --user=%s --password=%s %s < %s",
								escapeshellarg($dsn[2]),
								escapeshellarg($dsn[3]),
								escapeshellarg($dsn[0]),
								escapeshellarg($dsn[1]),
								escapeshellarg($dsn[4]),
								$fn
							);
							#echo $cmd."<BR>";
							exec($cmd, $ret, $retval);
						}
					}
					/* update autopatcher lastpatch */
					$cmd = sprintf("mysql --host=%s --port=%s --user=%s --password=%s %s --execute=%s",
						escapeshellarg($dsn[2]),
						escapeshellarg($dsn[3]),
						escapeshellarg($dsn[0]),
						escapeshellarg($dsn[1]),
						escapeshellarg($dsn[4]),
						sprintf("\"update license set autopatcher_lastpatch='%s'\"", end($files))
					);
					#echo $cmd."<BR>";
					exec($cmd, $ret, $retval);
				}
			}
		}
	}
}
?>
