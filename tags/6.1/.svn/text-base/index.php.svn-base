<?php
	/* detect mobile devices for compression level */
	$agent = $_SERVER["HTTP_USER_AGENT"];
	$mobile_agents = "/(Opera Mini)|(Minimo)|(Windows CE)/s";

	if (preg_match($mobile_agents, $agent)) {
		$compress_level = 9;
	} else {
		$compress_level = 1;
	}

	/* set default options */
	$enable_gzip    = 1;
	$enable_headers = 1;

	/* please be carful altering this one */
	/* if set wrong, MSIE will store all files locally in the browser cache, */
	/* even when they are SSL encrypted */
	if ($skip_run_module || $_REQUEST["dl"] || $_REQUEST["load_external_file"]) {
		$enable_gzip    = 0;
		$enable_headers = 0;
	} else {
		/* check for covide requirements */
		require("common/check_requirements.php");
	}

	if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"])) {
		$enable_gzip    = 0;
		$compress_level = 0;
	}

	/* if headers are to be sent */
	if ($enable_headers) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("X-Covide-Compression: level $compress_level");
	}

	//error handling
	if ($_REQUEST["debug"]==1) {
		require_once("errorhandler.php");
	}

	//autoload needed object files
	function __autoload($class_name) {
		$class_name = strtolower($class_name);
		if (strpos($class_name, "_")) {
			$class_dir   = preg_replace("/[^a-z0-9]/si", "", substr($class_name, 0, strpos($class_name, "_")));
			$class_file  = preg_replace("/[^a-z0-9]/si", "", substr(strstr($class_name, "_"), 1));

			if ($class_dir == "layout") {
				//TODO: switch statement for output type
				$class_dir = "html";
			}
			$includefile = "classes/".$class_dir."/".$class_file.".php";
		} else {
			$includefile = "classes/".$class_name."/default.php";
		}
		if (file_exists($includefile)) {
			require_once($includefile);
		} else {
			trigger_error("classfile ($includefile) does not exists.", E_USER_ERROR);
		}
	}

	/* create covide class */
	$covide = new Covide();
	$db =& $covide->db;
	if ($_REQUEST["fixdb"] == 1) {
		fix_db();
		die();
	}

	/* apply compression */
	if ($enable_gzip) {
		ini_set('zlib.output_compression_level', $compress_level);	//level 9 for mobile devices
		ob_start('ob_gzhandler');
		ob_implicit_flush(0);
	} else {
		ob_start();
		ob_implicit_flush(0);
	}

	/* external file loader */
	if ($_REQUEST["load_external_file"]) {
		$covide->load_file($_REQUEST["load_external_file"]);

	} elseif (!$skip_run_module) {
		/* possible states */
		/* 1st parameter:
				0 = do not force the connection
				1 = do force the connection to ssl
				2 = use non-ssl at default, but show the ssl option
		*/
		$covide->detect_mobile($mobile_agents);

		#debug
		#$covide->mobile = 1;
		$covide->force_ssl(2);

		$covide->run_module($_REQUEST["mod"]);
	}
	#exit();
?>
