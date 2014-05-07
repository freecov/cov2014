<?php
/**
  * Covide Includes
  *
  * @author Stephan vd Haar <svdhaar@users.sourceforge.net>
  * @version %%VERSION%%
  * @license http://www.gnu.org/licenses/gpl.html GPL
  * @link http://www.covide.net Project home.
  * @copyright Copyright 2000-2008 Covide BV
  * @package Covide
  */

	/* some virus scanner functionality to scan the $_FILES array */
	function handle_virus_scan() {
		// check for custom config directive inside offices.php
		require("conf/offices.php");
		if (isset($virus) && is_array($virus) && $virus["conf"])
			$conf = $virus["conf"];

		// check if defaults are set
		if (!isset($conf) || !is_array($conf))                           $conf = array();
		if (!array_key_exists("enable_clamav", $conf))  $conf["enable_clamav"] = 0;
		if (!array_key_exists("enable_fprot", $conf))   $conf["enable_fprot"]  = 0;

		if (in_array(1, $conf) && is_array($_FILES)) {
			foreach ($_FILES as $f) {
				if (is_array($f["tmp_name"])) {
					foreach ($f["tmp_name"] as $k=>$v) {
						$ret = virscan_clamav($v, $conf);
						virscan_handle_response($ret, $f["name"][$k]);

						$ret = virscan_fprot($v, $conf);
						virscan_handle_response($ret, $f["name"][$k]);
					}
				} else {
					$ret = virscan_clamav($f["tmp_name"], $conf);
					virscan_handle_response($ret, $f["name"][$k]);

					$ret = virscan_fprot($f["tmp_name"], $conf);
					virscan_handle_response($ret, $f["name"]);
				}
			}
		}
	}
	function virscan_clamav($file, $conf) {
		if ($conf["enable_clamav"] && file_exists("/usr/bin/clamscan")) {
			$cmd = sprintf("nice /usr/bin/clamscan --no-summary '%s'", $file);
			exec ($cmd, $ret, $ret1);
			if ($ret1 > 0)
				return $ret;
		}
	}
	function virscan_fprot($file, $conf) {
		if ($conf["enable_fprot"] && (file_exists("/usr/local/bin/f-prot") || file_exists('/usr/local/bin/fpscan'))) {
			if (file_exists("/usr/local/bin/fpscan")) {
				/* f-prot 6.0+ */
				$cmd = sprintf("nice /usr/local/bin/fpscan --report '%s'", $file);
				$str = "/\[Found virus\]/s";
			} else {
				/* f-prot < 6.0 */
				$cmd = sprintf("nice /usr/local/bin/f-prot -archive -ai '%s'", $file);
				$str = "/Infection: /s";
			}
			exec ($cmd, $ret, $ret1);
			if ($ret1 > 0) {
				foreach ($ret as $k=>$v) {
					if (!preg_match($str, $v))
						unset($ret[$k]);
				}
				return $ret;
			}

		}
	}
	function virscan_handle_response($ret, $file) {
		if (is_array($ret)) {
			$ret = implode("\n", $ret);
			$ret = str_replace($file, "", $ret);
			ob_clean();
			ob_start();
			header('HTTP/1.1 403 Forbidden');
			echo "A virus was found in file: ".$file;
			echo sprintf("<script>alert('A virus was found in uploaded file: %s\\n\\n%s');</script>",
				$file, $ret);
			exit();
		}
	}

?>
