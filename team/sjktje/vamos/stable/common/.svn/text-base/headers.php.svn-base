<?php
	/* detect mobile devices for compression level */
	$agent = $_SERVER["HTTP_USER_AGENT"];
	$mobile_agents = "/(Opera Mini)|(Minimo)|(Windows CE)|(Nokia)|(SymbianOS)/s";

	if (preg_match($mobile_agents, $agent)) {
		$compress_level = 9;
		$compress_text  = "Mobile Client";
	} else {
		$compress_level = 1;
		$compress_text  = "Normal browser";
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

	/* msie 5 and 6 store data unencrypted in disk cache when gzip is on ... */
	if (preg_match("/MSIE (5|6)/s", $_SERVER["HTTP_USER_AGENT"])) {
		$enable_gzip    = 0;
		$compress_level = 0;
		$compress_text  = "Not fully supported in MSIE";
	}
	/* msie 7 does support gzip, but stores data in disk cache when status code = 200 ... */
	if (!$_REQUEST["dl"] && preg_match("/MSIE (5|6|7)/s", $_SERVER["HTTP_USER_AGENT"])) {
		header("HTTP/1.0 205 OK");
	}

	/* detect local network, why waste cpu power on a local network */
	/*
	$client = ip2long($_SERVER["REMOTE_ADDR"]);

	$ranges = array(
		"127.0.0.1"   => "127.0.0.1",
		"10.0.0.0"    => "10.255.255.255",
		"172.16.0.0"  => "172.31.255.255",
		"192.168.0.0" => "192.168.255.255",
		"169.254.0.0" => "169.254.255.255"
	);
	foreach ($ranges as $start=>$end) {
		if ($client >= ip2long($start) && $client <= ip2long($end)) {
			$enable_gzip    = 0;
			$compress_level = 0;
			$compress_text  = "Local Area Network";
		}
	}
	*/

	/* if headers are to be sent */
	if ($enable_headers) {
		header("Content-Transfer-Encoding: 7bit");
		header("Expires: ".date("r", mktime() - (24*60*60)) );
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

		header("Cache-Control: no-cache, cachehack=".time());
		header("Cache-Control: no-store, must-revalidate");
		header("Cache-Control: post-check=-1, pre-check=-1", false);

		header("Pragma: no-cache");
		header("X-Covide-Compression-Level: level $compress_level");
		header("X-Covide-Compression-Description: $compress_text");
	}
?>
