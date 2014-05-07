<?
	/* js, css and txt are all plain text */
	header("Content-type: text/plain");

	$file = $_REQUEST["f"];
	/* double dot or slashes are not allowed here */
	$file = preg_replace("/\/{2,}/s", "/", $file);
	$file = preg_replace("/((^\/{1,})|(\.{2,})|(')|(\"))/s", "", $file);

	ini_set("open_basedir", dirname($_SERVER["SCRIPT_FILENAME"]));

	$fn = explode(".", basename($file));
	$fn2 =& $fn;

	/* prevent caching */
	header('Pragma: cache');
	header('Cache-Control: private');

	if (in_array($fn[count($fn)-1], array("css", "js"))) {
		if (file_exists($file)) {

			$fn = $file;
			// Getting headers sent by the client.
			if (function_exists('apache_request_headers')) {
				$headers = apache_request_headers();

				// Checking if the client is validating his cache and if it is current.
				if (isset($headers["If-Modified-Since"])) {
					if (strtotime($headers["If-Modified-Since"]) == filemtime($fn2)) {
						// Client's cache IS current, so we just respond '304 Not Modified'.
						header('Last-Modified: '.date('D, d M Y H:i:s', filemtime($fn2)), true, 304);
						exit();
					}
				}
			}

			// File not cached or cache outdated, we respond '200 OK' and output the image.
			/* set gzip compression */
			if (!preg_match("/MSIE 6/s", $_SERVER["HTTP_USER_AGENT"])) {
				ini_set('zlib.output_compression_level', (int)$_REQUEST["c"]);
				ob_start('ob_gzhandler');
 			}
			header('Last-Modified: '.date('D, d M Y H:i:s', filemtime($fn2)), true, 200);

			echo file_get_contents($fn);

		} else {
			echo "404 - file not found";
		}
	} else {
		echo "403 - not allowed";
	}

	ini_restore("open_basedir");
	exit();
?>