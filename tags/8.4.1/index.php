<?php
	// AVG bug.
	if ($_SERVER["HTTP_USER_AGENT"] == "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1;1813)") {
		echo "Processing stopped. You are AVG 8.0 webscanner and are known to create dos-attack like patterns.";
		die();
	}
	/* handle magic quotes */
	require("common/handle_magicquotesgpc.php");
	_handle_magic_quotes_gpc();

	/* handle some common php bugs */
	require("common/handle_php_bugs.php");
	handle_php_bugs();

	/* xml function classes */
	require("common/functions_xml.php");

	/* autoloader */
	require("common/autoload.php");

	/* little exception for cronjob fetching */
	if (!$_SERVER["SERVER_NAME"] && $argc > 0) {
		$host   = "";
		$action = "cron";
		$param  = array();

		foreach ($argv as $v) {
			$v = explode("=", $v);
			/* get hostname */
			switch ($v[0]) {
				case "--host":
					$host = $v[1];
					break;
				case "--convert":
					$param["convert"] = $v[1];
					$action = "convert_db";
					break;
				case "--password":
					$param["password"] = md5($v[1]);
					break;
				case "--no-output":
					$param["no-output"] = true;
					break;
				case "--funambol":
					if ($v[1] == "no")
						$param["funambol"] = "disabled";
					elseif ($v[1] == "single")
						$param["funambol"] = "single";
					else
						$param["funambol"] = "normal";
					break;
			}
		}
		if (!$host)
			die("Parameter --host=<hostname> not specified\n\n");

		$_SERVER["SERVER_NAME"] = $host;
		$_cron = 1;

		$_REQUEST["mod"]    = "user";
		$_REQUEST["action"] = $action;
		$_REQUEST["param"]  = $param;
	}

	/* scan for virusses */
	require("common/handle_virus_scan.php");
	handle_virus_scan();

	/* do the session initialization here. */
	if (!$skip_session_init) {
		session_cache_limiter('private, must-revalidate');
		session_start();
	}

	/* create covide class */
	$covide =& new Covide();

	$db =& $covide->db;
	if ($_REQUEST["fixdb"] == 1) {
		fix_db();
		die();
	}

	/* detect if we need to run cms instead of covide crm */
	if ($covide->license["has_cms"] && count($_POST)==0 && (!$_SERVER["QUERY_STRING"] || strpos($_SERVER["QUERY_STRING"], "gclid") !== false)
		&& !$skip_run_module && !$skip_run_module_cms && !$_cron)
		$run_cms_module = 1;

	/* send some headers */
	require("common/headers.php");

	/* apply compression */
	if ($enable_gzip && !$_cron) {
		ob_start('ob_gzhandler');
	 	ob_implicit_flush(0);
	} else {
		ob_start();
		ob_implicit_flush(0);
	}
	/* if cms */
	if ($run_cms_module) {
		require("site.php");
		exit();
	}

	/* external file loader */
	if ($_REQUEST["load_external_file"]) {
		$covide->load_file($_REQUEST["load_external_file"]);

	} elseif (!$skip_run_module && !$skip_run_module_cms) {
		/* possible states */
		/* 1st parameter:
				0 = do not force the connection
				1 = do force the connection to ssl
				2 = use non-ssl at default, but show the ssl option
				3 = force to non-ssl
		*/

		$covide->detect_mobile($mobile_agents);

		#debug
		#$covide->mobile = 1;
		/*
		 * If the force_ssl license var is set that value will be sent to force_ssl.
		 * If not, level 2 will be used (which does offer SSL, with the
		 * box ticked by default.
		 */
		if ($covide->license["force_ssl"])
			$covide->force_ssl($covide->license["force_ssl"]);
		else
			$covide->force_ssl(2);

		$covide->output_xhtml = 1;
		$covide->run_module($_REQUEST["mod"]);

		exit();
	}
?>
