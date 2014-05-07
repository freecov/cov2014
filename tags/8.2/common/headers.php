<?php
	/* function to write no-cache headers */
	function no_cache_headers() {
		/* please be carful altering this one */
		/* if set wrong, MSIE will store all files unecrypted in the browser cache, */
		/* even when they are SSL encrypted */
		/*
		if (!$_REQUEST["dl"] && preg_match("/MSIE (5|6|7)/s", $_SERVER["HTTP_USER_AGENT"])
			&& !$skip_run_module_cms && !$skip_session_init) {

			header(sprintf("%s 205 OK", $_SERVER["SERVER_PROTOCOL"]), true, 205);
		}
		*/
		/* other no-cache headers */
		header("Expires: ".gmdate('D, d M Y H:i:s', mktime()-3600)." GMT", true);
		header("Last-Modified: ".gmdate('D, d M Y H:i:s', mktime()-3600)." GMT", true);

		header("Cache-Control: no-cache, cachehack=".time(), true);
		header("Cache-Control: no-store, must-revalidate");
		header("Cache-Control: post-check=-1, pre-check=-1");
		header("Pragma: no-cache", true);

		header("Vary: *");
	}


	/* detect mobile devices for compression level */
	$agent = $_SERVER["HTTP_USER_AGENT"];
	$mobile_agents = "/(Opera Mini)|(Minimo)|(Windows CE)|(Nokia)|(SymbianOS)/s";

	/* set default options */
	$enable_gzip    = 1;
	$enable_headers = 1;

	/* check if zlib output compression is already activated in php.ini */
	if (ini_get("zlib.output_compression") == 1)
		$enable_gzip = 0;

	/* for files we use a different method */
	if ($_REQUEST["dl"] || $_REQUEST["load_external_file"]) {
		$enable_gzip    = 0;
		$enable_headers = 0;
	} else {
		/* check for covide requirements */
		require("common/check_requirements.php");
	}
	if ($skip_run_module || $skip_run_module_cms || $run_cms_module)
		$enable_headers = 0;

	/* if headers are to be sent */
	if ($enable_headers)
		no_cache_headers();
?>