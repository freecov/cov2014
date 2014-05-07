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
 * @copyright Copyright 2000-2006 Covide BV
 * @package Covide
 */
Class Covide {
	/* variables */
	public $pagesize = 20;
	public $pagesize_default = 20;
	public $pagesize_alt = 10;

	public $loaded_scripts = array();

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
	public $vernr = "6.3";
	/**
	 * @var array holds all user info
	 */
	public $userinfo = Array();
	/**
	 * @var object database connection identifier
	 */
	public $db;
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
	public $webroot = "";
	public $sslmode;

	public $mobile = 0;

	public $conversion;

	/* methods */

    /* 	__construct {{{ */
    /**
     * 	__construct. Init a covide object and set some defaults
     */
	public function __construct() {
		/* do the session initialization here. */
		session_cache_limiter('private, must-revalidate');
		session_start();

		ini_set('include_path',ini_get('include_path').':./PEAR:');

		/* open database connection and set some defaults. */
		require_once("DB.php");
		require_once("conf/offices.php");

		$this->db =& DB::connect($dsn, $options);
		if (PEAR::isError($this->db)) {
			 echo ("Warning: no Covide office configured at this address or no valid database specified. ");
			 echo ($this->db->getMessage());
			 die();
		}
		/* include our own db lib. This should be removed asap. */
		require_once("common/functions_pear.php");
		$this->db->setFetchMode(DB_FETCHMODE_ASSOC);
		$this->db->setOption("autofree", 1);

		/* create the database structure */
		/*
		$patch = new Covide_postgresql(&$this->db);
		$patch->check_database();
		*/

		/* fill some default stuff we need throughout whole covide. */
		$this->_get_license();
		/* check if we have everything in place for covide */
		$this->_check_officereq();

		$this->temppath = dirname($_SERVER["SCRIPT_FILENAME"])."/tmp/";
		/* set the gettext env. */
		$this->_set_language();

		$dir = dirname($_SERVER["SCRIPT_NAME"])."/";
		$dir = preg_replace("/\/{1,}/s", "/", $dir);
		if ($_SERVER["HTTPS"] == "on") {
			$uri = "https://";
		} else {
			$uri = "http://";
		}
		$uri.= $_SERVER["HTTP_HOST"].$dir;
		$this->webroot = $uri;

		/* overwrite default theme 0 if session theme is set */
		$compress_level = 1;
		if (isset($_SESSION["theme"])) {
			$this->theme = $_SESSION["theme"];
		} else {
			$this->theme = 1;
		}
		if ($_SESSION["pagesize"] < 5) {
			$_SESSION["pagesize"] = 5;
		}
		if ($_SESSION["pagesize"] > 1000) {
			$_SESSION["pagesize"] = 1000;
		}
		if ($_SESSION["pagesize"]) {
			$this->pagesize = $_SESSION["pagesize"];
		}

		$this->conversion = new Layout_conversion();
	}
    /* }}} */
    /* 	__destruct {{{ */
    /**
     * 	__destruct. clean up everything
     */
	public function __destruct() {
		/* disconnect from database. */
		@$this->db->disconnect();
	}
    /* }}} */
    /* 	_get_license {{{ */
    /**
     * 	_get_license. Read the license table and init some vars
     */
	private function _get_license() {
		$q = "SELECT * FROM license ;";
		$res = $this->db->query($q);
		$res->fetchInto($row);
		$this->license = $row;
		if (trim($row["filesyspath"])) {
			$basepath = $row["filesyspath"];
		} else {
			$basepath = "/var/covide_files/";
		}
		$basepath .= $row["code"];
		$this->filesyspath = $basepath;
		$res->free();
	}
    /* }}} */

	/* _set_language {{{ */
	/**
	 * Set the gettext enviroment for complete covide.
	 */
	private function _set_language() {
		if ($_SESSION["user_id"]) {
			//language settings
			$sql = sprintf("SELECT language FROM users WHERE id=%d", $_SESSION["user_id"]);
			$res = $this->db->query($sql);
			$res->fetchInto($row);
			switch ($row["language"]) {
				case "EN" : $language = "en_US"; $hlindex = "index_en.htm"; break;
				case "NL" : $language = "nl_NL"; $hlindex = "index.htm";    break;
				case "DE" : $language = "de_DE"; $hlindex = "index_de.htm"; break;
				case "ES" : $language = "es_ES"; $hlindex = "index_en.htm"; break;
				case "IT" : $language = "it_IT"; $hlindex = "index_en.htm"; break;
				default   : $language = "nl_NL"; $hlindex = "index.htm";    break;
			}
			putenv("LANG=$language");
			$_SESSION["locale"] = $language;
			setlocale(LC_ALL, $language);
			setlocale(LC_NUMERIC, "en_US"); //always use . as decimal
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
		switch ($module) {
			case "address" :
				$address = new Address();
				break;
			case "classification" :
				$classification = new Classification();
				break;
			case "calendar" :
				$calendar = new Calendar();
				break;
			case "note" :
				$note = new Note();
				break;
			case "todo" :
				$todo = new Todo();
				break;
			case "email" :
				$email = new Email();
				break;
			case "newsletter" :
				$newsletter = new Newsletter();
				break;
			case "user" :
				$user = new User();
				break;
			case "filesys" :
				$filesys = new Filesys();
				break;
			case "project" :
				$project = new Project();
				break;
			case "projectext" :
				$project = new ProjectExt();
				break;
			case "history" :
				$history = new Layout_history();
				break;
			case "voip" :
				$voip = new Voip();
				break;
			case "support" :
				$support = new Support();
				break;
			case "sync4j" :
				$sync = new Sync4j();
				break;
			case "rss" :
				$rss = new Rss();
				break;
			case "sales":
				$sales = new Sales();
				break;
			case "index":
				$sales = new Index();
				break;
			case "templates":
				$sales = new Templates();
				break;
			case "metafields":
				$meta = new Metafields();
				break;
			case "mortgage":
				$mortgage = new Mortgage();
				break;
			case "product":
				$product = new Product();
				break;
			case "cms":
				$cms = new Cms();
				break;
			default :
				if (!$_SESSION["user_id"]) {
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
			$state = 0;
		} else {
			$uri = $_SERVER["HTTP_HOST"]."/".dirname($_SERVER["SCRIPT_NAME"])."/?".$_SERVER["QUERY_STRING"];
			$uri = preg_replace("/\/{1,}/s", "/", $uri);
			$uri = preg_replace("/\?{1,}/s", "?", $uri);

			if ($state == 3 && $_SERVER["HTTPS"]) {
				header("Location: http://".$uri);
				exit();
			} elseif (($state == 1 || ($_SESSION["ssl_enable"] && $state != 3)) && !$_SERVER["HTTPS"]) {
				header("Location: https://".$uri);
				exit();
			}
		}
		$this->sslmode = $state;
	}

	/* this mechanism prevents caching of *.js and *.css files */
	public function load_file($file) {

		if (!file_exists($file)) {
			exit();
		}

		//Opera has it's own caching mechanism
		if (preg_match("/Opera/si", $_SERVER["HTTP_USER_AGENT"])) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $file");
			exit();
		}

		$fn = $file;
		ini_set("open_basedir", dirname($_SERVER["SCRIPT_FILENAME"]));

		$allowed_extensions = array(
			"png" => "image/png",
			"gif" => "image/gif",
			"css" => "text/plain",
			"js"  => "text/javascript"
		);

		if (preg_match("/(\.|\/){2,}/s", $file)) {
			exit("no access");
		}
		$f = basename($file);
		$f = preg_replace("/[^a-z0-9_\.]/si", "", $f);
		$f = explode(".", $f);
		$ext = strtolower( $f[count($f)-1] );

		if ($allowed_extensions[$ext]) {
			$mime = $allowed_extensions[$ext];

	   /* Checking if the client is validating his cache and if it is current. */
   		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($fn))) {
       /* Client's cache IS current, so we just respond '304 Not Modified'. */
       	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($fn)).' GMT', true, 304);
       	header('Connection: close');
   		} else {
       	/* Image not cached or cache outdated, we respond '200 OK' and output the image. */
   			Header("Expires: ".gmdate("D, j M Y H:i:s", mktime()+(24*60*60))." GMT");
       	header('Last-Modified: '.gmdate('D, d M Y H:i:s', mktime()-(24*60*60)).' GMT', true, 200);
				header("Content-Transfer-Encoding: binary");
    	  header('Content-Type: '.$mime);
 	  		header("Pragma: public");
  	    print file_get_contents($fn);
	   	}
		}

		ini_restore("open_basedir");
		exit();
	}

	public function trigger_login() {
		echo "<html>";
		echo "<body>";
			?>
			<script language="javascript">
				alert('<?=addslashes(gettext("U bent niet ingelogd."))?>');
				setTimeout('document.location.href="index.php"', 1000);
			</script>
			<?
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
				"syncml/todos"
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
			$_SESSION["allok"] = 1;
		}
	}
	/* }}} */

	public function cleanUpTempDir() {
		$exclude = array(".", "..", ".svn", "README", "check.txt", "menu");
		$files = scandir($this->temppath);
		foreach ($files as $file) {
			if (!in_array($file, $exclude)) {
				$ctime = filectime($this->temppath.$file);
				/* if file change time is older than 15 minutes */
				if ($ctime + (15*60) >= mktime()) {
					if (is_dir($this->temppath.$file)) {
						/* remove directory */
						$dirfiles = scandir($this->temppath.$file);
						foreach ($dirfiles as $dirfile) {
							/* remove contents of directory */
							if (!in_array($dirfile, $exclude))
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

	}
}
?>
