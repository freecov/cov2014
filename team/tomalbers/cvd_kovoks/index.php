<?php
	require("common/autoload.php");

	/* create covide class */
	$covide = new Covide();
	$db =& $covide->db;
	if ($_REQUEST["fixdb"] == 1) {
		fix_db();
		die();
	}

	if ($covide->license["has_cms"] && count($_POST)==0 && !$_SERVER["QUERY_STRING"]
		&& !$skip_run_module && !$skip_run_module_cms) {

		require("site.php");
		exit();
	}
	require("common/headers.php");

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
		$covide->force_ssl(2);

		$covide->run_module($_REQUEST["mod"]);

		exit();
	}
?>
