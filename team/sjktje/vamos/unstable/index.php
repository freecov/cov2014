<?php
	/* handle magic quotes */
	require("common/handle_magicquotesgpc.php");
	_handle_magic_quotes_gpc();

	/* handle some common php bugs */
	require("common/handle_php_bugs.php");
	handle_php_bugs();

	/* scan for virusses */
	require("common/handle_virus_scan.php");
	handle_virus_scan();

	/* xml function classes */
	require("common/functions_xml.php");

	/* autoloader */
	require("common/autoload.php");

	/* little exception for cronjob fetching */
	if (!$_SERVER["SERVER_NAME"] && $argc > 0) {
		$host = "";
		foreach ($argv as $v) {
			$v = explode("=", $v);
			if ($v[0] == "--host")
				$host = $v[1];
		}
		if (!$host)
			die("Parameter --host=<hostname> not specified\n\n");

		$_SERVER["SERVER_NAME"] = $host;
		$_cron = 1;

		$_REQUEST["mod"] = "user";
		$_REQUEST["action"] = "cron";
	}

	/* create covide class */
	$covide = new Covide();
	$db =& $covide->db;
	if ($_REQUEST["fixdb"] == 1) {
		fix_db();
		die();
	}

	if ($covide->license["has_cms"] && count($_POST)==0 && !$_SERVER["QUERY_STRING"]
		&& !$skip_run_module && !$skip_run_module_cms && !$_cron) {
		require("site.php");
		exit();
	}

	require("common/headers.php");

	/* apply compression */
	if ($enable_gzip && !$_cron) {
		ob_start('ob_gzhandler');
	 	ob_implicit_flush(0);
	} else {
		ob_start();
		ob_implicit_flush(0);
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
		if ($GLOBALS["covide"]->license["force_ssl"])
			$covide->force_ssl($GLOBALS["covide"]->license["force_ssl"]);
		else
			$covide->force_ssl(2);

		$covide->run_module($_REQUEST["mod"]);

		exit();
	}
?>
