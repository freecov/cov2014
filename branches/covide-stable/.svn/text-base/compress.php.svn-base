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
 * @copyright Copyright 2000-2009 Covide BV
 * @package Covide
 */
/* js, css and txt are all plain text */
header("Content-type: text/plain");

if (!$_REQUEST["f"]) {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
	echo "404 Not Found - no file specified";
}

$p = parse_url($_REQUEST["f"]);
$path = pathinfo($p["path"]);

$f["file"] = $p["path"];
$f["ext"]  = strtolower($path["extension"]);

/* double dot or slashes are not allowed here */
$f["file"] = preg_replace("/\/{2,}/s", "/", $f["file"]);
$f["file"] = preg_replace("/((^\/{1,})|(\.{2,})|(')|(\"))/s", "", $f["file"]);

if (!in_array($f["ext"], array("js", "css"))) {
	header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
	echo "403 Forbidden";
	exit();
}

if (file_exists($f["file"])) {
	/* get modification time */
	$f["timestamp"] = filemtime($f["file"]);

	/* Checking if the client is validating his cache and if it is current. */
	if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $f["timestamp"])) {
		/* Client's cache IS current, so we just respond '304 Not Modified'. */
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $f["timestamp"]).' GMT', true, 304);
	} else {
		if (ini_get("zlib.output_compression") == 1) {
			ob_start();
		} else {
			ob_start('ob_gzhandler');
		}

		header("Expires: ".gmdate("D, j M Y H:i:s", time() + (60*60*4))." GMT", true);
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()-(24*60*60)).' GMT', true, 200);
		header("Pragma: public");
		header('Cache-Control: public');

		echo file_get_contents($f["file"]);
	}
} else {
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
	echo "404 Not Found";
}
exit();
?>
